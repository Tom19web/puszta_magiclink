<?php
/**
 * PusztaPlay Magic Login — Uninstall hook
 * Cleans up cron jobs, options, and user meta on plugin deletion.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

wp_clear_scheduled_hook('pp_daily_reminder_check');

delete_option('pp_smtp_settings');
delete_option('pp_reminder_lock');

// User meta cleanup (all pp_* keys)
global $wpdb;
$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'pp\_%'");
