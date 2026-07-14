<?php
/**
 * Harris-Nelson — Member Benefits Bridge (WooCommerce dues -> member status)
 *
 * PURPOSE
 *   Ultimate Member handles login. This snippet adds the DUES layer on top:
 *   when a family member buys a Dues product (or enough $25 installments),
 *   they become a "dues-current member" for the configured reunion year.
 *   Refund/cancel of the granting order removes exactly what it granted.
 *
 *   Matches the family constitution: voting members are those current on
 *   dues — so member-only content can be gated at two levels:
 *     level 1: logged in            -> Ultimate Member restriction (pages)
 *     level 2: logged in + dues paid -> [hn_members] shortcode (sections)
 *
 * PRIVACY (owner directive)
 *   A member's dues status is shown ONLY to that member and to admins
 *   (Users list column for the Treasurer). It is never printed publicly.
 *
 * INSTALL
 *   Paste into the free "Code Snippets" plugin (Snippets -> Add New ->
 *   run everywhere), or drop in wp-content/mu-plugins/. The OWNER should
 *   approve/paste this herself — it touches order handling.
 *
 * CONFIG — after creating the WooCommerce products (handoff Task I),
 *   fill in the real product IDs below. Zeros are ignored safely.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** The reunion year dues currently apply to (change each cycle, or via
 *  the hn_current_dues_year filter). */
if ( ! function_exists( 'hn_dues_year' ) ) {
	function hn_dues_year() {
		return (string) apply_filters( 'hn_current_dues_year', get_option( 'hn_current_dues_year', '2026' ) );
	}
}

/** Product map. type 'dues' grants membership outright;
 *  type 'installment' accumulates dollars toward hn_dues_threshold(). */
if ( ! function_exists( 'hn_mb_map' ) ) {
	function hn_mb_map() {
		$map = array(
			0 => array( 'label' => 'Dues — Single (or w/ 1 minor) $125', 'type' => 'dues' ),        // TODO real ID
			0 => array( 'label' => 'Dues — Single + guest/college $175', 'type' => 'dues' ),        // TODO real ID
			0 => array( 'label' => 'Dues — Family of 3+ $225',           'type' => 'dues' ),        // TODO real ID
			0 => array( 'label' => 'Dues Installment $25',               'type' => 'installment' ), // TODO real ID
		);
		return apply_filters( 'hn_member_product_map', $map );
	}
}

/** Dollars of installments that count as dues-current (lowest tier). */
if ( ! function_exists( 'hn_dues_threshold' ) ) {
	function hn_dues_threshold() {
		return (float) apply_filters( 'hn_dues_threshold', 125.0 );
	}
}

/* ------------------------------------------------------------------
 * Read helpers. Admins always pass, so the owner can preview gates.
 * ------------------------------------------------------------------ */
if ( ! function_exists( 'hn_installment_total' ) ) {
	function hn_installment_total( $user_id = 0 ) {
		$user_id = $user_id ? (int) $user_id : get_current_user_id();
		if ( ! $user_id ) { return 0.0; }
		return max( 0.0, (float) get_user_meta( $user_id, 'hn_installments_' . hn_dues_year(), true ) );
	}
}

if ( ! function_exists( 'hn_is_current_member' ) ) {
	function hn_is_current_member( $user_id = 0 ) {
		$user_id = $user_id ? (int) $user_id : get_current_user_id();
		if ( ! $user_id ) { return false; }
		if ( user_can( $user_id, 'manage_options' ) ) { return true; }
		if ( get_user_meta( $user_id, 'hn_dues_paid_' . hn_dues_year(), true ) ) { return true; }
		return hn_installment_total( $user_id ) >= hn_dues_threshold();
	}
}

/* ------------------------------------------------------------------
 * Grant on payment, revoke on refund/cancel. Idempotent per order.
 * ------------------------------------------------------------------ */
if ( ! function_exists( 'hn_mb_apply_order' ) ) {
	function hn_mb_apply_order( $order_id, $grant ) {
		if ( ! function_exists( 'wc_get_order' ) ) { return; }
		$order = wc_get_order( $order_id );
		if ( ! $order ) { return; }
		$user_id = (int) $order->get_customer_id();
		if ( ! $user_id ) { return; } // guest checkout: Treasurer records manually
		$map  = hn_mb_map();
		$year = hn_dues_year();

		foreach ( $order->get_items() as $item ) {
			$pid = (int) $item->get_product_id();
			if ( empty( $map[ $pid ] ) ) { continue; }
			$type = $map[ $pid ]['type'];

			if ( 'installment' === $type ) {
				$amount  = (float) $item->get_total();
				$log_key = 'hn_installment_log_' . $year;
				$bal_key = 'hn_installments_' . $year;
				$log     = get_user_meta( $user_id, $log_key, true );
				$log     = is_array( $log ) ? $log : array();
				$bal     = max( 0.0, (float) get_user_meta( $user_id, $bal_key, true ) );
				if ( $grant ) {
					if ( isset( $log[ $order_id ] ) ) { continue; } // processing+completed both fire
					$log[ $order_id ] = $amount;
					update_user_meta( $user_id, $bal_key, $bal + $amount );
					update_user_meta( $user_id, $log_key, $log );
				} elseif ( isset( $log[ $order_id ] ) ) {
					update_user_meta( $user_id, $bal_key, max( 0.0, $bal - (float) $log[ $order_id ] ) );
					unset( $log[ $order_id ] );
					update_user_meta( $user_id, $log_key, $log );
				}
				continue;
			}

			// 'dues' product: grant/revoke tied to THIS order only.
			$meta_key = 'hn_dues_paid_' . $year;
			if ( $grant ) {
				update_user_meta( $user_id, $meta_key, (int) $order_id );
			} elseif ( (int) get_user_meta( $user_id, $meta_key, true ) === (int) $order_id ) {
				delete_user_meta( $user_id, $meta_key ); // an older paid order keeps status
			}
		}
	}
}
add_action( 'woocommerce_order_status_processing', function ( $oid ) { hn_mb_apply_order( $oid, true ); } );
add_action( 'woocommerce_order_status_completed',  function ( $oid ) { hn_mb_apply_order( $oid, true ); } );
add_action( 'woocommerce_order_status_refunded',   function ( $oid ) { hn_mb_apply_order( $oid, false ); } );
add_action( 'woocommerce_order_status_cancelled',  function ( $oid ) { hn_mb_apply_order( $oid, false ); } );

/* ------------------------------------------------------------------
 * [hn_members] ... [/hn_members]
 * Gate a SECTION to dues-current members. Logged-out visitors are asked
 * to register; logged-in non-current members are pointed to dues.
 * Whole PAGES should still use Ultimate Member restrictions; this is for
 * sections inside otherwise-visible pages (e.g. voting links).
 * ------------------------------------------------------------------ */
add_shortcode( 'hn_members', function ( $atts, $content = '' ) {
	$atts = shortcode_atts( array(
		'message' => __( 'This section is for dues-current family members.', 'hn' ),
	), $atts, 'hn_members' );

	if ( hn_is_current_member() ) {
		return do_shortcode( $content );
	}
	$out  = '<div class="hn-members-locked" style="border:2px dashed #cb923f;background:#fffaf0;border-radius:12px;padding:18px;text-align:center;font-family:Verdana,sans-serif;">';
	$out .= '<p style="margin:0 0 10px;">' . esc_html( $atts['message'] ) . '</p>';
	if ( ! is_user_logged_in() ) {
		$out .= '<a class="button" href="' . esc_url( wp_login_url() ) . '">' . esc_html__( 'Log in or register', 'hn' ) . '</a>';
	} else {
		$out .= '<a class="button" href="' . esc_url( home_url( '/dues-and-shirts/' ) ) . '">' . esc_html__( 'Pay your dues to unlock', 'hn' ) . '</a>';
	}
	return $out . '</div>';
} );

/**
 * [hn_dues_status] — shows the VIEWER their own status only (privacy rule:
 * never anyone else's). Ideal for the My Account / profile area.
 */
add_shortcode( 'hn_dues_status', function () {
	if ( ! is_user_logged_in() ) { return ''; }
	$year = hn_dues_year();
	if ( hn_is_current_member() ) {
		return '<span class="hn-dues-ok" style="color:#2e7d32;font-weight:bold;">✓ ' .
			esc_html( sprintf( __( 'Dues current for %s — thank you!', 'hn' ), $year ) ) . '</span>';
	}
	$paid   = hn_installment_total();
	$need   = max( 0, hn_dues_threshold() - $paid );
	$msg    = $paid > 0
		? sprintf( __( 'Installments so far: $%1$s — $%2$s to go for %3$s dues.', 'hn' ), number_format_i18n( $paid, 2 ), number_format_i18n( $need, 2 ), $year )
		: sprintf( __( 'Dues for %s not yet recorded online.', 'hn' ), $year );
	return '<span class="hn-dues-open" style="color:#b71c1c;">' . esc_html( $msg ) .
		' <a href="' . esc_url( home_url( '/dues-and-shirts/' ) ) . '">' . esc_html__( 'Pay dues', 'hn' ) . '</a></span>';
} );

/* ------------------------------------------------------------------
 * Treasurer's view: a "Dues" column on the admin Users list. Admin-only
 * by nature of wp-admin; never rendered publicly.
 * ------------------------------------------------------------------ */
add_filter( 'manage_users_columns', function ( $cols ) {
	$cols['hn_dues'] = sprintf( __( 'Dues %s', 'hn' ), hn_dues_year() );
	return $cols;
} );
add_filter( 'manage_users_custom_column', function ( $value, $column, $user_id ) {
	if ( 'hn_dues' !== $column ) { return $value; }
	if ( get_user_meta( $user_id, 'hn_dues_paid_' . hn_dues_year(), true ) ) { return '✓ ' . esc_html__( 'paid', 'hn' ); }
	$paid = hn_installment_total( $user_id );
	if ( $paid > 0 ) { return esc_html( '$' . number_format_i18n( $paid, 2 ) . ' partial' ); }
	return '&mdash;';
}, 10, 3 );

/**
 * Treasurer manual override: admins can mark cash/Zelle/Cash App payers as
 * paid from the user's profile screen (checkbox), since those payments
 * never touch WooCommerce.
 */
add_action( 'edit_user_profile', 'hn_mb_profile_box' );
add_action( 'show_user_profile', 'hn_mb_profile_box' );
function hn_mb_profile_box( $user ) {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$year = hn_dues_year();
	$paid = (bool) get_user_meta( $user->ID, 'hn_dues_paid_' . $year, true );
	wp_nonce_field( 'hn_mb_save', 'hn_mb_nonce' );
	echo '<h2>' . esc_html__( 'Harris-Nelson dues (Treasurer)', 'hn' ) . '</h2><table class="form-table"><tr><th>' .
		esc_html( sprintf( __( 'Dues %s', 'hn' ), $year ) ) . '</th><td><label><input type="checkbox" name="hn_dues_paid" value="1" ' .
		checked( $paid, true, false ) . '> ' . esc_html__( 'Paid (incl. Zelle / Cash App / cash recorded by the Treasurer)', 'hn' ) .
		'</label></td></tr></table>';
}
add_action( 'edit_user_profile_update', 'hn_mb_profile_save' );
add_action( 'personal_options_update', 'hn_mb_profile_save' );
function hn_mb_profile_save( $user_id ) {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	if ( ! isset( $_POST['hn_mb_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['hn_mb_nonce'] ), 'hn_mb_save' ) ) { return; }
	$key = 'hn_dues_paid_' . hn_dues_year();
	if ( ! empty( $_POST['hn_dues_paid'] ) ) {
		if ( ! get_user_meta( $user_id, $key, true ) ) { update_user_meta( $user_id, $key, 'manual' ); }
	} elseif ( 'manual' === get_user_meta( $user_id, $key, true ) ) {
		delete_user_meta( $user_id, $key ); // only un-check manual grants, never order-based ones
	}
}
