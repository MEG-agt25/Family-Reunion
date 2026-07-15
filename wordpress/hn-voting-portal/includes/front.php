<?php
/**
 * Harris-Nelson Voting Portal — front end.
 * [hn_ballot id="123"]          the voting form (eligibility-gated)
 * [hn_ballot_results id="123"]  aggregate totals ONLY, after close,
 *                               only if the admin enabled "publish totals"
 * Submissions POST back to the same page and are processed on
 * template_redirect (PRG pattern: redirect after write, so refreshing
 * never double-votes). No external CSS/JS — styles print inline once.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ------------------------------------------------------------------
 * Submission handler (runs before output; redirects when done).
 * ------------------------------------------------------------------ */
add_action( 'template_redirect', 'hn_vp_handle_submit' );
function hn_vp_handle_submit() {
	if ( empty( $_POST['hn_vp_ballot'] ) ) { return; }
	global $wpdb;
	$ballot = (int) $_POST['hn_vp_ballot'];
	$back   = remove_query_arg( array( 'hn_voted', 'hn_vp_err' ), wp_get_referer() ?: home_url( '/' ) );
	$fail   = function ( $code ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'hn_vp_err', $code, $back ) ); exit;
	};

	if ( ! wp_verify_nonce( sanitize_key( $_POST['hn_vp_nonce'] ?? '' ), 'hn_vp_vote_' . $ballot ) ) { $fail( 'nonce' ); }
	$post = get_post( $ballot );
	if ( ! $post || 'hn_ballot' !== $post->post_type || 'publish' !== $post->post_status ) { $fail( 'gone' ); }
	if ( 'open' !== hn_vp_window( $ballot ) ) { $fail( 'window' ); }
	if ( true !== hn_vp_eligible( $ballot ) ) { $fail( 'eligible' ); }

	$questions = hn_vp_questions( $ballot );
	$answers   = array();
	foreach ( $questions as $qi => $q ) {
		$raw = isset( $_POST[ 'hn_q' . $qi ] ) ? wp_unslash( $_POST[ 'hn_q' . $qi ] ) : '';
		if ( 'choice' === $q['type'] ) {
			$raw = sanitize_text_field( $raw );
			// server-side: an answer must be one of the defined choices
			$answers[ 'q' . $qi ] = in_array( $raw, $q['choices'], true ) ? $raw : '';
		} else {
			$answers[ 'q' . $qi ] = mb_substr( sanitize_textarea_field( $raw ), 0, 2000 );
		}
	}

	$mode    = get_post_meta( $ballot, '_hn_eligibility', true );
	$user_id = get_current_user_id();
	$name    = '';
	$branch  = '';
	if ( 'open' === $mode && ! $user_id ) {
		$name   = mb_substr( sanitize_text_field( wp_unslash( $_POST['hn_voter_name'] ?? '' ) ), 0, 190 );
		$branch = sanitize_text_field( wp_unslash( $_POST['hn_voter_branch'] ?? '' ) );
		if ( ! in_array( $branch, hn_vp_branches(), true ) ) { $branch = ''; }
		if ( '' === $name || '' === $branch ) { $fail( 'name' ); }
		// soft double-vote deterrent for open ballots (real dedupe: the CSV)
		if ( ! empty( $_COOKIE[ 'hn_vp_' . $ballot ] ) ) { $fail( 'dupe' ); }
		setcookie( 'hn_vp_' . $ballot, '1', time() + YEAR_IN_SECONDS, COOKIEPATH ?: '/', COOKIE_DOMAIN, is_ssl(), true );
	}

	$now      = current_time( 'mysql' );
	$existing = $user_id ? hn_vp_existing_vote( $ballot, $user_id ) : null;
	if ( $existing ) {
		if ( ! get_post_meta( $ballot, '_hn_revise', true ) ) { $fail( 'dupe' ); }
		$wpdb->update( hn_vp_table(),
			array( 'answers' => wp_json_encode( $answers ), 'revised_at' => $now ),
			array( 'id' => (int) $existing->id ),
			array( '%s', '%s' ), array( '%d' ) );
	} else {
		$ok = $wpdb->insert( hn_vp_table(), array(
			'ballot_id'    => $ballot,
			'user_id'      => $user_id ? $user_id : null,
			'voter_name'   => $name,
			'voter_branch' => $branch,
			'answers'      => wp_json_encode( $answers ),
			'voted_at'     => $now,
		), array( '%d', $user_id ? '%d' : null, '%s', '%s', '%s', '%s' ) );
		if ( false === $ok ) { $fail( 'dupe' ); } // unique key raced: someone double-clicked
	}
	wp_safe_redirect( add_query_arg( 'hn_voted', '1', $back ) );
	exit;
}

/* ------------------------------------------------------------------
 * Inline styles — mobile-first, Verdana, warm gold accents, rounded
 * cards; printed once per page, only when a shortcode renders.
 * ------------------------------------------------------------------ */
function hn_vp_css() {
	static $done = false;
	if ( $done ) { return ''; }
	$done = true;
	return '<style>
	.hn-vp{font-family:Verdana,sans-serif;max-width:680px;margin:0 auto;}
	.hn-vp-card{background:#fffaf0;border:2px solid #cb923f;border-radius:14px;padding:1.1rem 1.2rem;margin-bottom:1rem;}
	.hn-vp-q{background:#fff;border:1px solid #e5ddcd;border-left:5px solid #cb923f;border-radius:12px;padding:.9rem 1rem;margin-bottom:.8rem;}
	.hn-vp-q h4{margin:0 0 .5rem;color:#1b5e20;font-size:.95rem;line-height:1.4;}
	.hn-vp-q label{display:flex;gap:.5em;align-items:flex-start;padding:.45em .6em;border:1px solid #e5ddcd;border-radius:9px;margin-bottom:.35rem;cursor:pointer;font-size:.9rem;}
	.hn-vp-q label:hover{border-color:#cb923f;background:#fffaf0;}
	.hn-vp-q input[type=radio]{accent-color:#2e7d32;margin-top:.2em;}
	.hn-vp-q textarea,.hn-vp input[type=text],.hn-vp select{width:100%;padding:.55em .7em;border:1px solid #cbbfa5;border-radius:9px;font-family:Verdana,sans-serif;font-size:.9rem;}
	.hn-vp-btn{display:inline-block;background:#e65100;color:#fff;font-weight:bold;font-size:.9rem;border:none;border-radius:99px;padding:.75em 1.8em;cursor:pointer;}
	.hn-vp-btn:hover{background:#bf4400;}
	.hn-vp-status{background:#fffaf0;border:2px dashed #cb923f;border-radius:14px;padding:1.1rem 1.2rem;text-align:center;color:#8a5a13;font-size:.92rem;}
	.hn-vp-ok{background:#e8f2e8;border:2px solid #2e7d32;border-radius:14px;padding:1rem 1.2rem;color:#1b5e20;font-weight:bold;text-align:center;}
	.hn-vp-err{background:#fdecea;border:2px solid #b71c1c;border-radius:14px;padding:1rem 1.2rem;color:#b71c1c;text-align:center;font-size:.9rem;}
	.hn-vp-bar{background:#e5ddcd;border-radius:99px;height:14px;overflow:hidden;margin:.2rem 0 .6rem;}
	.hn-vp-bar>span{display:block;height:100%;background:linear-gradient(90deg,#2e7d32,#1b5e20);border-radius:99px;}
	@media(min-width:700px){.hn-vp-q h4{font-size:1rem;}}
	</style>';
}

/* ------------------------------------------------------------------
 * [hn_ballot id="123"]
 * ------------------------------------------------------------------ */
add_shortcode( 'hn_ballot', function ( $atts ) {
	$atts   = shortcode_atts( array( 'id' => 0 ), $atts, 'hn_ballot' );
	$ballot = (int) $atts['id'];
	$post   = get_post( $ballot );
	if ( ! $post || 'hn_ballot' !== $post->post_type || 'publish' !== $post->post_status ) { return ''; }

	$motto = __( 'We all we got, we all we need!', 'hn' );
	$out   = hn_vp_css() . '<div class="hn-vp">';
	$out  .= '<div class="hn-vp-card"><h3 style="margin:.1em 0;color:#1b5e20;">🗳️ ' . esc_html( $post->post_title ) . '</h3>';
	if ( '' !== trim( (string) $post->post_content ) ) {
		$out .= '<p style="font-size:.9rem;color:#5c5546;">' . esc_html( wp_strip_all_tags( $post->post_content ) ) . '</p>';
	}
	$out .= '</div>';

	// flash messages from the PRG redirect
	if ( isset( $_GET['hn_voted'] ) ) {
		return $out . '<div class="hn-vp-ok">✓ ' . esc_html__( 'Your vote is in — thank you!', 'hn' ) .
			' ' . esc_html( $motto ) . '</div></div>';
	}
	if ( isset( $_GET['hn_vp_err'] ) ) {
		$msgs = array(
			'window'   => __( 'Voting is not open right now.', 'hn' ),
			'eligible' => __( 'This ballot is for dues-current members.', 'hn' ),
			'dupe'     => __( 'A vote from you is already recorded for this ballot.', 'hn' ),
			'name'     => __( 'Please give your name and family branch.', 'hn' ),
			'nonce'    => __( 'That form expired — please try again.', 'hn' ),
			'gone'     => __( 'This ballot is no longer available.', 'hn' ),
		);
		$code = sanitize_key( $_GET['hn_vp_err'] );
		$out .= '<div class="hn-vp-err">' . esc_html( $msgs[ $code ] ?? $msgs['nonce'] ) . '</div>';
	}

	// window gate with the family motto
	$window = hn_vp_window( $ballot );
	if ( 'before' === $window ) {
		return $out . '<div class="hn-vp-status">⏳ ' .
			esc_html__( 'This vote hasn\'t opened yet — check back soon.', 'hn' ) .
			'<br><strong>' . esc_html( $motto ) . '</strong></div></div>';
	}
	if ( 'closed' === $window ) {
		$closed = '<div class="hn-vp-status">🗳️ ' .
			esc_html__( 'This vote has closed — thank you to everyone who took part.', 'hn' ) .
			'<br><strong>' . esc_html( $motto ) . '</strong></div>';
		if ( get_post_meta( $ballot, '_hn_publish_totals', true ) ) {
			$closed .= do_shortcode( '[hn_ballot_results id="' . $ballot . '"]' );
		}
		return $out . $closed . '</div>';
	}

	// eligibility gate
	$eligible = hn_vp_eligible( $ballot );
	if ( 'login' === $eligible ) {
		return $out . '<div class="hn-vp-status">' .
			esc_html__( 'This ballot is for family members — please log in to vote.', 'hn' ) .
			'<br><a class="hn-vp-btn" style="margin-top:.6rem;" href="' .
			esc_url( wp_login_url( get_permalink() ) ) . '">' . esc_html__( 'Log in', 'hn' ) . '</a></div></div>';
	}
	if ( 'dues' === $eligible ) {
		return $out . '<div class="hn-vp-status">' .
			esc_html__( 'Voting members are those current on dues (our constitution, Article 2). Bring yours current to vote — every voice counts.', 'hn' ) .
			'<br><a class="hn-vp-btn" style="margin-top:.6rem;" href="' .
			esc_url( home_url( '/dues-and-shirts/' ) ) . '">' . esc_html__( 'Pay dues', 'hn' ) . '</a></div></div>';
	}

	// one-vote / revise handling
	$user_id  = get_current_user_id();
	$existing = $user_id ? hn_vp_existing_vote( $ballot, $user_id ) : null;
	$prefill  = array();
	if ( $existing ) {
		if ( ! get_post_meta( $ballot, '_hn_revise', true ) ) {
			return $out . '<div class="hn-vp-ok">✓ ' .
				esc_html__( 'Your vote is already recorded for this ballot. Thank you!', 'hn' ) . '</div></div>';
		}
		$prefill = json_decode( (string) $existing->answers, true ) ?: array();
		$out    .= '<div class="hn-vp-status" style="margin-bottom:1rem;">✏️ ' .
			esc_html__( 'You already voted — submitting again revises your vote (allowed until the ballot closes).', 'hn' ) . '</div>';
	}

	// the form
	$mode = get_post_meta( $ballot, '_hn_eligibility', true );
	$out .= '<form method="post" action="">';
	$out .= wp_nonce_field( 'hn_vp_vote_' . $ballot, 'hn_vp_nonce', true, false );
	$out .= '<input type="hidden" name="hn_vp_ballot" value="' . (int) $ballot . '">';

	if ( 'open' === $mode && ! $user_id ) {
		$out .= '<div class="hn-vp-q"><h4>' . esc_html__( 'Your name', 'hn' ) . ' *</h4>' .
			'<input type="text" name="hn_voter_name" required maxlength="190"></div>';
		$out .= '<div class="hn-vp-q"><h4>' . esc_html__( 'Your family branch', 'hn' ) . ' *</h4><select name="hn_voter_branch" required>' .
			'<option value="">' . esc_html__( '— choose —', 'hn' ) . '</option>';
		foreach ( hn_vp_branches() as $b ) {
			$out .= '<option value="' . esc_attr( $b ) . '">' . esc_html( $b ) . '</option>';
		}
		$out .= '</select></div>';
	}

	foreach ( hn_vp_questions( $ballot ) as $qi => $q ) {
		$prev = isset( $prefill[ 'q' . $qi ] ) ? (string) $prefill[ 'q' . $qi ] : '';
		$out .= '<div class="hn-vp-q"><h4>' . esc_html( ( $qi + 1 ) . '. ' . $q['label'] ) . '</h4>';
		if ( 'choice' === $q['type'] ) {
			foreach ( $q['choices'] as $choice ) {
				$out .= '<label><input type="radio" name="hn_q' . $qi . '" value="' . esc_attr( $choice ) . '" ' .
					checked( $prev, $choice, false ) . '> <span>' . esc_html( $choice ) . '</span></label>';
			}
			$out .= '<p style="font-size:.75rem;color:#5c5546;margin:.3rem 0 0;">' .
				esc_html__( 'Leave blank to abstain.', 'hn' ) . '</p>';
		} else {
			$out .= '<textarea name="hn_q' . $qi . '" rows="3" maxlength="2000">' . esc_textarea( $prev ) . '</textarea>';
		}
		$out .= '</div>';
	}
	$out .= '<p style="text-align:center;"><button type="submit" class="hn-vp-btn">🗳️ ' .
		( $existing ? esc_html__( 'Revise my vote', 'hn' ) : esc_html__( 'Cast my vote', 'hn' ) ) . '</button></p>';
	$out .= '<p style="text-align:center;font-size:.75rem;color:#5c5546;">' .
		esc_html__( 'Individual votes are seen only by the officers tallying results — never published.', 'hn' ) . '</p>';
	return $out . '</form></div>';
} );

/* ------------------------------------------------------------------
 * [hn_ballot_results id="123"] — AGGREGATE totals only. Renders only
 * after close AND only if the admin enabled "publish totals". Write-in
 * questions show a response count, never the text (privacy).
 * ------------------------------------------------------------------ */
add_shortcode( 'hn_ballot_results', function ( $atts ) {
	global $wpdb;
	$atts   = shortcode_atts( array( 'id' => 0 ), $atts, 'hn_ballot_results' );
	$ballot = (int) $atts['id'];
	$post   = get_post( $ballot );
	if ( ! $post || 'hn_ballot' !== $post->post_type || 'publish' !== $post->post_status ) { return ''; }
	if ( ! get_post_meta( $ballot, '_hn_publish_totals', true ) || 'closed' !== hn_vp_window( $ballot ) ) {
		return hn_vp_css() . '<div class="hn-vp"><div class="hn-vp-status">' .
			esc_html__( 'Results will be shared after the vote closes.', 'hn' ) . '</div></div>';
	}
	$total = (int) $wpdb->get_var( $wpdb->prepare(
		'SELECT COUNT(*) FROM ' . hn_vp_table() . ' WHERE ballot_id = %d', $ballot ) );
	$out = hn_vp_css() . '<div class="hn-vp"><div class="hn-vp-card"><h3 style="margin:.1em 0;color:#1b5e20;">📊 ' .
		esc_html( sprintf( __( '%1$s — Results (%2$d ballots cast)', 'hn' ), $post->post_title, $total ) ) . '</h3></div>';

	$questions = hn_vp_questions( $ballot );
	$rows      = $wpdb->get_results( $wpdb->prepare(
		'SELECT answers FROM ' . hn_vp_table() . ' WHERE ballot_id = %d', $ballot ) );
	foreach ( $questions as $qi => $q ) {
		$out .= '<div class="hn-vp-q"><h4>' . esc_html( ( $qi + 1 ) . '. ' . $q['label'] ) . '</h4>';
		if ( 'choice' === $q['type'] ) {
			$counts = array_fill_keys( $q['choices'], 0 );
			foreach ( $rows as $r ) {
				$a = json_decode( (string) $r->answers, true );
				$v = isset( $a[ 'q' . $qi ] ) ? (string) $a[ 'q' . $qi ] : '';
				if ( isset( $counts[ $v ] ) ) { $counts[ $v ]++; }
			}
			$sum = max( 1, array_sum( $counts ) );
			foreach ( $counts as $choice => $n ) {
				$pct  = round( 100 * $n / $sum );
				$out .= '<div style="font-size:.85rem;">' . esc_html( $choice ) . ' — <strong>' . (int) $n . '</strong> (' . (int) $pct . '%)</div>' .
					'<div class="hn-vp-bar"><span style="width:' . (int) $pct . '%;"></span></div>';
			}
		} else {
			$n = 0;
			foreach ( $rows as $r ) {
				$a = json_decode( (string) $r->answers, true );
				if ( ! empty( $a[ 'q' . $qi ] ) ) { $n++; }
			}
			$out .= '<p style="font-size:.85rem;color:#5c5546;">' .
				esc_html( sprintf( _n( '%d write-in response received (reviewed by the officers).', '%d write-in responses received (reviewed by the officers).', $n, 'hn' ), $n ) ) . '</p>';
		}
		$out .= '</div>';
	}
	return $out . '</div>';
} );
