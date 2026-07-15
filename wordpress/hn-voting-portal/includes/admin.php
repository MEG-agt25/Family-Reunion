<?php
/**
 * Harris-Nelson Voting Portal — admin side.
 * Everything here is manage_options-only: ballot editor metaboxes,
 * duplicate action, the Results screen, CSV export, settings.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ------------------------------------------------------------------
 * Metaboxes: Ballot settings + Questions repeater.
 * ------------------------------------------------------------------ */
add_action( 'add_meta_boxes_hn_ballot', function () {
	add_meta_box( 'hn_vp_settings', __( 'Ballot Settings', 'hn' ), 'hn_vp_box_settings', 'hn_ballot', 'side' );
	add_meta_box( 'hn_vp_questions', __( 'Questions', 'hn' ), 'hn_vp_box_questions', 'hn_ballot', 'normal', 'high' );
} );

function hn_vp_box_settings( $post ) {
	wp_nonce_field( 'hn_vp_save', 'hn_vp_nonce' );
	$open   = get_post_meta( $post->ID, '_hn_open', true );
	$close  = get_post_meta( $post->ID, '_hn_close', true );
	$mode   = get_post_meta( $post->ID, '_hn_eligibility', true ) ?: 'dues';
	$revise = (bool) get_post_meta( $post->ID, '_hn_revise', true );
	$pub    = (bool) get_post_meta( $post->ID, '_hn_publish_totals', true );
	$dt     = function ( $v ) { return $v ? esc_attr( str_replace( ' ', 'T', substr( $v, 0, 16 ) ) ) : ''; };
	?>
	<p><label><strong><?php esc_html_e( 'Opens', 'hn' ); ?></strong><br>
		<input type="datetime-local" name="hn_open" value="<?php echo $dt( $open ); ?>"></label></p>
	<p><label><strong><?php esc_html_e( 'Closes', 'hn' ); ?></strong><br>
		<input type="datetime-local" name="hn_close" value="<?php echo $dt( $close ); ?>"></label></p>
	<p><label><strong><?php esc_html_e( 'Who may vote', 'hn' ); ?></strong><br>
		<select name="hn_eligibility">
			<option value="dues" <?php selected( $mode, 'dues' ); ?>><?php esc_html_e( 'Dues-current members', 'hn' ); ?></option>
			<option value="loggedin" <?php selected( $mode, 'loggedin' ); ?>><?php esc_html_e( 'Anyone logged in', 'hn' ); ?></option>
			<option value="open" <?php selected( $mode, 'open' ); ?>><?php esc_html_e( 'Open (no login; asks name + branch)', 'hn' ); ?></option>
		</select></label></p>
	<p><label><input type="checkbox" name="hn_revise" value="1" <?php checked( $revise ); ?>>
		<?php esc_html_e( 'Voters may revise their vote until close', 'hn' ); ?></label></p>
	<p><label><input type="checkbox" name="hn_publish_totals" value="1" <?php checked( $pub ); ?>>
		<?php esc_html_e( 'Publish aggregate totals after close ([hn_ballot_results])', 'hn' ); ?></label></p>
	<p class="description"><?php echo esc_html( sprintf( __( 'Shortcode: [hn_ballot id="%d"]', 'hn' ), $post->ID ) ); ?></p>
	<?php
}

function hn_vp_box_questions( $post ) {
	$questions = hn_vp_questions( $post->ID );
	if ( ! $questions ) { $questions = array( array( 'label' => '', 'type' => 'choice', 'choices' => array() ) ); }
	?>
	<div id="hn-vp-qs">
		<?php foreach ( $questions as $i => $q ) : hn_vp_question_row( $i, $q ); endforeach; ?>
	</div>
	<p><button type="button" class="button" id="hn-vp-add"><?php esc_html_e( '+ Add question', 'hn' ); ?></button></p>
	<script type="text/template" id="hn-vp-tpl"><?php hn_vp_question_row( '__i__', array( 'label' => '', 'type' => 'choice', 'choices' => array() ) ); ?></script>
	<script>
	(function () {
		var wrap = document.getElementById('hn-vp-qs'), n = wrap.children.length;
		document.getElementById('hn-vp-add').addEventListener('click', function () {
			var div = document.createElement('div');
			div.innerHTML = document.getElementById('hn-vp-tpl').innerHTML.replace(/__i__/g, 'n' + (n++));
			wrap.appendChild(div.firstElementChild);
		});
		wrap.addEventListener('click', function (e) {
			if (e.target.classList.contains('hn-vp-del')) {
				e.target.closest('.hn-vp-q').remove();
			}
		});
	})();
	</script>
	<style>
	.hn-vp-q{border:1px solid #dcdcde;border-left:4px solid #cb923f;border-radius:6px;padding:10px 12px;margin-bottom:10px;background:#fff;}
	.hn-vp-q input[type=text]{width:100%;}
	.hn-vp-q textarea{width:100%;min-height:70px;}
	.hn-vp-q .hn-vp-row{display:flex;gap:10px;align-items:center;margin:.4em 0;flex-wrap:wrap;}
	</style>
	<?php
}

/** One question row (also used as the JS clone template with $i = '__i__'). */
function hn_vp_question_row( $i, $q ) {
	$label   = isset( $q['label'] ) ? $q['label'] : '';
	$type    = isset( $q['type'] ) && 'text' === $q['type'] ? 'text' : 'choice';
	$choices = ! empty( $q['choices'] ) && is_array( $q['choices'] ) ? implode( "\n", $q['choices'] ) : '';
	?>
	<div class="hn-vp-q">
		<div class="hn-vp-row">
			<input type="text" name="hn_q_label[<?php echo esc_attr( $i ); ?>]"
				placeholder="<?php esc_attr_e( 'Question label', 'hn' ); ?>"
				value="<?php echo esc_attr( $label ); ?>">
		</div>
		<div class="hn-vp-row">
			<label><?php esc_html_e( 'Type', 'hn' ); ?>
				<select name="hn_q_type[<?php echo esc_attr( $i ); ?>]">
					<option value="choice" <?php selected( $type, 'choice' ); ?>><?php esc_html_e( 'Single choice (radio)', 'hn' ); ?></option>
					<option value="text" <?php selected( $type, 'text' ); ?>><?php esc_html_e( 'Free-text write-in', 'hn' ); ?></option>
				</select></label>
			<button type="button" class="button-link-delete hn-vp-del"><?php esc_html_e( 'Remove', 'hn' ); ?></button>
		</div>
		<textarea name="hn_q_choices[<?php echo esc_attr( $i ); ?>]"
			placeholder="<?php esc_attr_e( 'Choices — one per line (ignored for write-in questions)', 'hn' ); ?>"><?php echo esc_textarea( $choices ); ?></textarea>
	</div>
	<?php
}

/* Save both metaboxes. */
add_action( 'save_post_hn_ballot', function ( $post_id ) {
	if ( ! isset( $_POST['hn_vp_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['hn_vp_nonce'] ), 'hn_vp_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'manage_options' ) ) { return; }

	$dt = function ( $key ) {
		$v = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $v ) ) { return ''; }
		return str_replace( 'T', ' ', $v ) . ':00';
	};
	update_post_meta( $post_id, '_hn_open', $dt( 'hn_open' ) );
	update_post_meta( $post_id, '_hn_close', $dt( 'hn_close' ) );

	$mode = isset( $_POST['hn_eligibility'] ) ? sanitize_key( $_POST['hn_eligibility'] ) : 'dues';
	update_post_meta( $post_id, '_hn_eligibility', in_array( $mode, array( 'dues', 'loggedin', 'open' ), true ) ? $mode : 'dues' );
	update_post_meta( $post_id, '_hn_revise', empty( $_POST['hn_revise'] ) ? '' : '1' );
	update_post_meta( $post_id, '_hn_publish_totals', empty( $_POST['hn_publish_totals'] ) ? '' : '1' );

	$questions = array();
	$labels    = isset( $_POST['hn_q_label'] ) && is_array( $_POST['hn_q_label'] ) ? wp_unslash( $_POST['hn_q_label'] ) : array();
	foreach ( $labels as $i => $label ) {
		$label = sanitize_text_field( $label );
		if ( '' === $label ) { continue; }
		$type    = isset( $_POST['hn_q_type'][ $i ] ) && 'text' === $_POST['hn_q_type'][ $i ] ? 'text' : 'choice';
		$choices = array();
		if ( 'choice' === $type && isset( $_POST['hn_q_choices'][ $i ] ) ) {
			foreach ( explode( "\n", sanitize_textarea_field( wp_unslash( $_POST['hn_q_choices'][ $i ] ) ) ) as $c ) {
				$c = trim( $c );
				if ( '' !== $c ) { $choices[] = $c; }
			}
		}
		if ( 'choice' === $type && count( $choices ) < 2 ) { $type = 'text'; $choices = array(); } // a radio needs 2+ options
		$questions[] = array( 'label' => $label, 'type' => $type, 'choices' => $choices );
	}
	update_post_meta( $post_id, '_hn_questions', $questions );
} );

/* ------------------------------------------------------------------
 * List table extras: shortcode column, Duplicate + Results row actions.
 * ------------------------------------------------------------------ */
add_filter( 'manage_hn_ballot_posts_columns', function ( $cols ) {
	$cols['hn_shortcode'] = __( 'Shortcode', 'hn' );
	$cols['hn_votes']     = __( 'Votes', 'hn' );
	return $cols;
} );
add_action( 'manage_hn_ballot_posts_custom_column', function ( $col, $post_id ) {
	global $wpdb;
	if ( 'hn_shortcode' === $col ) {
		echo '<code>[hn_ballot id="' . (int) $post_id . '"]</code>';
	} elseif ( 'hn_votes' === $col ) {
		echo (int) $wpdb->get_var( $wpdb->prepare(
			'SELECT COUNT(*) FROM ' . hn_vp_table() . ' WHERE ballot_id = %d', $post_id ) );
	}
}, 10, 2 );

add_filter( 'post_row_actions', function ( $actions, $post ) {
	if ( 'hn_ballot' !== $post->post_type || ! current_user_can( 'manage_options' ) ) { return $actions; }
	$actions['hn_duplicate'] = '<a href="' . esc_url( wp_nonce_url(
		admin_url( 'admin.php?action=hn_vp_duplicate&ballot=' . $post->ID ), 'hn_vp_dup_' . $post->ID ) ) . '">' .
		esc_html__( 'Duplicate ballot', 'hn' ) . '</a>';
	$actions['hn_results'] = '<a href="' . esc_url( admin_url(
		'edit.php?post_type=hn_ballot&page=hn-vp-results&ballot=' . $post->ID ) ) . '">' .
		esc_html__( 'Results', 'hn' ) . '</a>';
	return $actions;
}, 10, 2 );

/* One-click duplicate: copies title, description, settings, questions —
 * never the votes. New copy lands as a draft with no open/close dates. */
add_action( 'admin_action_hn_vp_duplicate', function () {
	$ballot = isset( $_GET['ballot'] ) ? (int) $_GET['ballot'] : 0;
	if ( ! current_user_can( 'manage_options' ) ||
		! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ?? '' ), 'hn_vp_dup_' . $ballot ) ) {
		wp_die( esc_html__( 'Not allowed.', 'hn' ) );
	}
	$src = get_post( $ballot );
	if ( ! $src || 'hn_ballot' !== $src->post_type ) { wp_die( esc_html__( 'Ballot not found.', 'hn' ) ); }
	$new = wp_insert_post( array(
		'post_type'    => 'hn_ballot',
		'post_status'  => 'draft',
		'post_title'   => $src->post_title . ' ' . __( '(Copy)', 'hn' ),
		'post_content' => $src->post_content,
	) );
	if ( $new && ! is_wp_error( $new ) ) {
		foreach ( array( '_hn_questions', '_hn_eligibility', '_hn_revise', '_hn_publish_totals' ) as $key ) {
			update_post_meta( $new, $key, get_post_meta( $ballot, $key, true ) );
		}
		// open/close intentionally NOT copied — the next vote sets its own window
	}
	wp_safe_redirect( admin_url( 'edit.php?post_type=hn_ballot' ) );
	exit;
} );

/* ------------------------------------------------------------------
 * Bridge soft-dependency notice: a dues-eligibility ballot without the
 * Member Benefits Bridge active silently widens to "anyone logged in" —
 * tell the admin instead of letting that pass unnoticed.
 * ------------------------------------------------------------------ */
add_action( 'admin_notices', function () {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || false === strpos( (string) $screen->id, 'hn_ballot' ) ) { return; }
	if ( function_exists( 'hn_is_current_member' ) ) { return; }
	$dues = get_posts( array(
		'post_type' => 'hn_ballot', 'post_status' => 'publish', 'numberposts' => 1,
		'meta_key' => '_hn_eligibility', 'meta_value' => 'dues', 'fields' => 'ids',
	) );
	if ( ! $dues ) { return; }
	echo '<div class="notice notice-warning"><p><strong>' .
		esc_html__( 'Harris-Nelson Voting Portal:', 'hn' ) . '</strong> ' .
		esc_html__( 'a published ballot is set to "Dues-current members" but the Member Benefits Bridge plugin is not active, so it currently allows ANYONE logged in. Activate the Bridge to enforce dues.', 'hn' ) .
		'</p></div>';
} );

/* ------------------------------------------------------------------
 * Results screen (admin-only) + CSV export.
 * ------------------------------------------------------------------ */
add_action( 'admin_menu', function () {
	add_submenu_page( 'edit.php?post_type=hn_ballot', __( 'Results', 'hn' ),
		__( 'Results', 'hn' ), 'manage_options', 'hn-vp-results', 'hn_vp_results_page' );
	add_submenu_page( 'edit.php?post_type=hn_ballot', __( 'Voting Settings', 'hn' ),
		__( 'Settings', 'hn' ), 'manage_options', 'hn-vp-settings', 'hn_vp_settings_page' );
} );

/** Tally a ballot: per-question counts + write-ins + total. */
function hn_vp_tally( $ballot_id ) {
	global $wpdb;
	$rows      = $wpdb->get_results( $wpdb->prepare(
		'SELECT * FROM ' . hn_vp_table() . ' WHERE ballot_id = %d ORDER BY voted_at ASC', (int) $ballot_id ) );
	$questions = hn_vp_questions( $ballot_id );
	$tally     = array();
	foreach ( $questions as $qi => $q ) {
		$tally[ $qi ] = array( 'counts' => array_fill_keys( $q['choices'], 0 ), 'writeins' => array(), 'answered' => 0 );
	}
	foreach ( $rows as $row ) {
		$answers = json_decode( (string) $row->answers, true );
		if ( ! is_array( $answers ) ) { continue; }
		foreach ( $questions as $qi => $q ) {
			$a = isset( $answers[ 'q' . $qi ] ) ? (string) $answers[ 'q' . $qi ] : '';
			if ( '' === $a ) { continue; }
			$tally[ $qi ]['answered']++;
			if ( 'choice' === $q['type'] ) {
				if ( isset( $tally[ $qi ]['counts'][ $a ] ) ) { $tally[ $qi ]['counts'][ $a ]++; }
			} else {
				$tally[ $qi ]['writeins'][] = $a;
			}
		}
	}
	return array( 'rows' => $rows, 'questions' => $questions, 'tally' => $tally );
}

function hn_vp_results_page() {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'Not allowed.', 'hn' ) ); }
	$ballot = isset( $_GET['ballot'] ) ? (int) $_GET['ballot'] : 0;
	echo '<div class="wrap"><h1>' . esc_html__( 'Ballot Results', 'hn' ) . '</h1>';
	$all = get_posts( array( 'post_type' => 'hn_ballot', 'post_status' => 'any', 'numberposts' => -1 ) );
	echo '<form method="get"><input type="hidden" name="post_type" value="hn_ballot">' .
		'<input type="hidden" name="page" value="hn-vp-results"><select name="ballot">';
	foreach ( $all as $b ) {
		echo '<option value="' . (int) $b->ID . '" ' . selected( $ballot, $b->ID, false ) . '>' .
			esc_html( $b->post_title . ' (' . $b->post_status . ')' ) . '</option>';
	}
	echo '</select> <button class="button">' . esc_html__( 'View', 'hn' ) . '</button></form>';
	if ( ! $ballot ) { echo '</div>'; return; }

	$data  = hn_vp_tally( $ballot );
	$total = count( $data['rows'] );
	echo '<h2>' . esc_html( get_the_title( $ballot ) ) . '</h2>';
	echo '<p><strong>' . esc_html( sprintf( _n( '%d ballot cast', '%d ballots cast', $total, 'hn' ), $total ) ) . '</strong> · ' .
		esc_html__( 'window:', 'hn' ) . ' ' . esc_html( hn_vp_window( $ballot ) ) . ' · ' .
		'<a class="button button-primary" href="' . esc_url( wp_nonce_url( admin_url(
			'admin-post.php?action=hn_vp_csv&ballot=' . $ballot ), 'hn_vp_csv_' . $ballot ) ) . '">' .
		esc_html__( 'Export CSV for the minutes', 'hn' ) . '</a></p>';

	foreach ( $data['questions'] as $qi => $q ) {
		echo '<div class="card" style="max-width:760px;padding:12px 16px;margin-bottom:12px;border-left:4px solid #cb923f;">';
		echo '<h3 style="margin:.2em 0;">' . esc_html( ( $qi + 1 ) . '. ' . $q['label'] ) . '</h3>';
		if ( 'choice' === $q['type'] ) {
			$counts = $data['tally'][ $qi ]['counts'];
			$max    = $counts ? max( $counts ) : 0;
			$top    = $max > 0 ? array_keys( $counts, $max, true ) : array();
			foreach ( $counts as $choice => $n ) {
				$lead = ( $n === $max && $max > 0 );
				$tie  = $lead && count( $top ) > 1;
				echo '<p style="margin:.25em 0;">' .
					( $lead ? '<strong>' : '' ) . esc_html( $choice ) . ': ' . (int) $n .
					( $tie ? ' <span style="color:#b71c1c;font-weight:bold;">' . esc_html__( '— TIE', 'hn' ) . '</span>' : '' ) .
					( $lead ? '</strong>' : '' ) . '</p>';
			}
		} else {
			echo '<p><em>' . esc_html( sprintf( __( '%d write-in responses (admin-only):', 'hn' ),
				count( $data['tally'][ $qi ]['writeins'] ) ) ) . '</em></p><ul style="list-style:disc;margin-left:1.4em;">';
			foreach ( $data['tally'][ $qi ]['writeins'] as $w ) {
				echo '<li>' . esc_html( $w ) . '</li>';
			}
			echo '</ul>';
		}
		echo '</div>';
	}
	echo '</div>';
}

/* CSV: one row per ballot cast — for the Secretary's minutes. Admin-only. */
add_action( 'admin_post_hn_vp_csv', function () {
	$ballot = isset( $_GET['ballot'] ) ? (int) $_GET['ballot'] : 0;
	if ( ! current_user_can( 'manage_options' ) ||
		! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ?? '' ), 'hn_vp_csv_' . $ballot ) ) {
		wp_die( esc_html__( 'Not allowed.', 'hn' ) );
	}
	$data = hn_vp_tally( $ballot );
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=ballot-' . $ballot . '-results.csv' );
	$out  = fopen( 'php://output', 'w' );
	$head = array( 'voted_at', 'revised_at', 'voter', 'family_branch' );
	foreach ( $data['questions'] as $qi => $q ) { $head[] = 'Q' . ( $qi + 1 ) . ': ' . $q['label']; }
	fputcsv( $out, $head );
	foreach ( $data['rows'] as $row ) {
		$user = $row->user_id ? get_userdata( (int) $row->user_id ) : null;
		$line = array(
			$row->voted_at,
			$row->revised_at ?: '',
			$user ? $user->display_name . ' (' . $user->user_login . ')' : $row->voter_name,
			$row->voter_branch,
		);
		$answers = json_decode( (string) $row->answers, true ) ?: array();
		foreach ( $data['questions'] as $qi => $q ) {
			$line[] = isset( $answers[ 'q' . $qi ] ) ? (string) $answers[ 'q' . $qi ] : '';
		}
		fputcsv( $out, $line );
	}
	fclose( $out );
	exit;
} );

/* ------------------------------------------------------------------
 * Settings: the delete-data-on-uninstall opt-in (default: keep data).
 * ------------------------------------------------------------------ */
add_action( 'admin_init', function () {
	register_setting( 'hn_vp_settings', 'hn_vp_delete_on_uninstall', array(
		'type' => 'string', 'sanitize_callback' => function ( $v ) { return $v ? '1' : ''; },
	) );
} );
function hn_vp_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'Not allowed.', 'hn' ) ); }
	?>
	<div class="wrap"><h1><?php esc_html_e( 'Voting Portal Settings', 'hn' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'hn_vp_settings' ); ?>
		<p><label><input type="checkbox" name="hn_vp_delete_on_uninstall" value="1"
			<?php checked( get_option( 'hn_vp_delete_on_uninstall' ), '1' ); ?>>
			<?php esc_html_e( 'Delete ALL ballots and votes when this plugin is uninstalled (default: data is kept).', 'hn' ); ?></label></p>
		<?php submit_button(); ?>
	</form></div>
	<?php
}
