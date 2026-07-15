<?php
/**
 * Harris-Nelson Voting Portal — uninstall.
 * Data is KEPT by default (family voting history matters). Everything is
 * removed only if the admin ticked "Delete ALL ballots and votes when
 * this plugin is uninstalled" under Ballots -> Settings.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

if ( '1' !== get_option( 'hn_vp_delete_on_uninstall' ) ) {
	return; // leave the table, ballots, and options for a future reinstall
}

global $wpdb;

// votes table
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'hn_votes' ); // phpcs:ignore WordPress.DB.PreparedSQL

// ballots + their meta
$ids = $wpdb->get_col( $wpdb->prepare(
	"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", 'hn_ballot' ) );
foreach ( $ids as $id ) {
	wp_delete_post( (int) $id, true );
}

// options
delete_option( 'hn_vp_seeded' );
delete_option( 'hn_vp_delete_on_uninstall' );
