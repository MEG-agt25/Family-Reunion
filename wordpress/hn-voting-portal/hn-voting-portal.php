<?php
/**
 * Plugin Name: Harris-Nelson Voting Portal
 * Description: Family ballots — superlatives, bylaws choices, next-reunion cities. Admins build ballots in wp-admin; members vote through the [hn_ballot] shortcode; the Secretary exports results to CSV for the minutes.
 * Version: 1.0.0
 * Author: Harris-Nelson Family Reunion
 *
 * PURPOSE
 *   The constitution says voting members are those current on dues. This
 *   plugin runs the votes: each "Ballot" is a custom post with questions,
 *   an open/close window, and an eligibility level. Soft integration with
 *   the Member Benefits Bridge (hn_is_current_member) decides who counts
 *   as dues-current — no hard dependency on WooCommerce/Ultimate Member.
 *
 * PRIVACY (owner directive)
 *   Individual votes are visible to ADMINS ONLY (Results screen + CSV for
 *   the Secretary's minutes) and are NEVER rendered publicly. The optional
 *   [hn_ballot_results] shortcode exposes aggregate totals only, and only
 *   after close, and only if the admin ticked "publish totals". Write-in
 *   text is never shown publicly (counts only).
 *
 * INSTALL
 *   Upload hn-voting-portal.zip via Plugins -> Add New -> Upload Plugin,
 *   or copy this folder to wp-content/plugins/. Activation creates the
 *   votes table and seeds a DRAFT "Constitution & Bylaws 2026" ballot with
 *   the 14 bracketed decisions from the family constitution.
 *   Full checklist: RUNBOOK-WORDPRESS.md section 10.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'HN_VP_VER', '1.0.0' );
define( 'HN_VP_DIR', plugin_dir_path( __FILE__ ) );

/** Votes table name (single source of truth). */
function hn_vp_table() {
	global $wpdb;
	return $wpdb->prefix . 'hn_votes';
}

/* ------------------------------------------------------------------
 * Activation: votes table (dbDelta) + seed ballot.
 * ------------------------------------------------------------------ */
register_activation_hook( __FILE__, 'hn_vp_activate' );
function hn_vp_activate() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	// user_id is NULL for open (no-login) ballots: UNIQUE allows many NULLs,
	// so one-vote-per-user is enforced for members while open votes still insert.
	$sql = 'CREATE TABLE ' . hn_vp_table() . " (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		ballot_id bigint(20) unsigned NOT NULL,
		user_id bigint(20) unsigned DEFAULT NULL,
		voter_name varchar(190) NOT NULL DEFAULT '',
		voter_branch varchar(120) NOT NULL DEFAULT '',
		answers longtext NOT NULL,
		voted_at datetime NOT NULL,
		revised_at datetime DEFAULT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY ballot_user (ballot_id,user_id),
		KEY ballot (ballot_id)
	) $charset;";
	dbDelta( $sql );
	hn_vp_register_cpt();
	hn_vp_seed_ballot();
	flush_rewrite_rules();
}

/* ------------------------------------------------------------------
 * Ballot custom post type. Admin-UI only — ballots render via shortcode,
 * never as their own public URL (privacy: no accidental exposure).
 * ------------------------------------------------------------------ */
add_action( 'init', 'hn_vp_register_cpt' );
function hn_vp_register_cpt() {
	register_post_type( 'hn_ballot', array(
		'labels' => array(
			'name'          => __( 'Ballots', 'hn' ),
			'singular_name' => __( 'Ballot', 'hn' ),
			'add_new_item'  => __( 'Add New Ballot', 'hn' ),
			'edit_item'     => __( 'Edit Ballot', 'hn' ),
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-yes-alt',
		'menu_position'       => 26,
		'supports'            => array( 'title', 'editor' ), // editor = ballot description
		'capability_type'     => 'post',
		'capabilities'        => array( 'create_posts' => 'manage_options' ),
		'map_meta_cap'        => true,
		'exclude_from_search' => true,
		'show_in_rest'        => false,
	) );
}

/* ------------------------------------------------------------------
 * Shared helpers.
 * ------------------------------------------------------------------ */

/** Questions for a ballot: array of [label, type('choice'|'text'), choices[]]. */
function hn_vp_questions( $ballot_id ) {
	$q = get_post_meta( (int) $ballot_id, '_hn_questions', true );
	return is_array( $q ) ? $q : array();
}

/** Voting window: 'before' | 'open' | 'closed'. Times are site-local. */
function hn_vp_window( $ballot_id ) {
	$now   = current_time( 'mysql' );
	$open  = (string) get_post_meta( (int) $ballot_id, '_hn_open', true );
	$close = (string) get_post_meta( (int) $ballot_id, '_hn_close', true );
	if ( $open && $now < $open ) { return 'before'; }
	if ( $close && $now > $close ) { return 'closed'; }
	return 'open';
}

/**
 * Can the current visitor vote on this ballot?
 * Returns true, or a string reason: 'login' | 'dues'.
 * Bridge soft-dependency: if hn_is_current_member() is missing, a
 * dues ballot falls back to logged-in-only (admin notice flags it).
 */
function hn_vp_eligible( $ballot_id ) {
	$mode = get_post_meta( (int) $ballot_id, '_hn_eligibility', true );
	if ( 'open' === $mode ) { return true; }
	if ( ! is_user_logged_in() ) { return 'login'; }
	if ( 'dues' === $mode && function_exists( 'hn_is_current_member' ) && ! hn_is_current_member() ) {
		return 'dues';
	}
	return true;
}

/** The logged-in visitor's existing vote row for a ballot, or null. */
function hn_vp_existing_vote( $ballot_id, $user_id ) {
	global $wpdb;
	if ( ! $user_id ) { return null; }
	return $wpdb->get_row( $wpdb->prepare(
		'SELECT * FROM ' . hn_vp_table() . ' WHERE ballot_id = %d AND user_id = %d',
		(int) $ballot_id, (int) $user_id
	) );
}

/** Family branches (matches the registration form's canonical list). */
function hn_vp_branches() {
	return apply_filters( 'hn_vp_branches', array(
		'Mary Nelson', 'Mildred Ellis', 'Jessie Moore', 'James Edward',
		'James Earl', 'Curtis', 'Mary Jane Gray', 'Mandy Ellis',
		'Shirley Harris', 'Nelson line (Joe & Oreatha)',
		'Not sure — help me find my branch!',
	) );
}

/* ------------------------------------------------------------------
 * Seed: the 2026 Constitution & Bylaws ballot — the 14 bracketed
 * decisions from docs/constitution-and-bylaws.md, ready as a DRAFT so
 * the family never retypes it. Runs once (guarded by an option).
 * ------------------------------------------------------------------ */
function hn_vp_seed_ballot() {
	if ( get_option( 'hn_vp_seeded' ) ) { return; }
	$q = function ( $label, $choices = array() ) {
		return array(
			'label'   => $label,
			'type'    => $choices ? 'choice' : 'text',
			'choices' => $choices,
		);
	};
	$questions = array(
		$q( 'Article 2 — Voting members must be current on dues and at least this age:',
			array( '18', '21' ) ),
		$q( 'Article 2 — A constitutional amendment must be distributed to the family this many days in advance:',
			array( '30 days', '14 days', '60 days' ) ),
		$q( 'Article 4 — The Secretary distributes the agenda and minutes this many days ahead of meetings:',
			array( '14 days', '7 days', '30 days' ) ),
		$q( 'Article 4 — The Treasurer needs a second authorized officer\'s sign-off on amounts over:',
			array( '$500', '$250', '$1,000' ) ),
		$q( 'Article 5 — Officers serve a term of:',
			array( 'Two years', 'Until the next reunion' ) ),
		$q( 'Article 5 — Maximum consecutive terms in the same office (waivable by 2/3 vote):',
			array( 'Two terms', 'Three terms', 'No limit' ) ),
		$q( 'Article 6 — Quorum for the Annual Business Meeting (fixed number):',
			array( '25 voting members', '20 voting members', '30 voting members' ) ),
		$q( 'Article 6 — Quorum alternative (percentage, whichever is fewer):',
			array( '20% of voting members', '25% of voting members', '10% of voting members' ) ),
		$q( 'Article 6 — Between meetings, an unbudgeted expense requires a member vote when it exceeds:',
			array( '$500', '$250', '$1,000' ) ),
		$q( 'Article 7 (Finances) — The fiscal year runs:',
			array( 'January 1 – December 31', 'Reunion to reunion' ) ),
		$q( 'Article 7 (Finances) — Net fundraising proceeds split (write your proposed percentages: land-back / scholarship / operating):' ),
		$q( 'Article 7 (Reunion) — The reunion is held:',
			array( 'Every year', 'Every two years' ) ),
		$q( 'Article 8 — Bylaws changes require notice to the family at least:',
			array( '14 days', '30 days' ) ),
		$q( 'Article 9 — If the Organization ever dissolves, remaining funds go:',
			array( 'Equally: scholarship fund + family-history preservation',
			       'All to the scholarship fund',
			       'All to family-history preservation' ) ),
	);
	$id = wp_insert_post( array(
		'post_type'    => 'hn_ballot',
		'post_status'  => 'draft',
		'post_title'   => __( 'Constitution & Bylaws 2026 — Family Ballot', 'hn' ),
		'post_content' => __( 'The bracketed decisions from our Constitution & Bylaws, one question each. Adopted answers get written into the final document at the business meeting.', 'hn' ),
	) );
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_hn_questions', $questions );
		update_post_meta( $id, '_hn_eligibility', 'dues' );
		update_post_meta( $id, '_hn_revise', '1' );
		update_post_meta( $id, '_hn_publish_totals', '' );
		update_option( 'hn_vp_seeded', $id );
	}
}

require_once HN_VP_DIR . 'includes/front.php';
if ( is_admin() ) {
	require_once HN_VP_DIR . 'includes/admin.php';
}
