<?php
/**
 * Plugin Name: Affiliates Additional Tokens
 * Plugin URI: http://www.netpad.gr
 * Description: Additional Tokens for affiliates referral notifications
 * Version: 1.0
 * Author: George Tsiokos
 * Author URI: http://www.netpad.gr
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright (c) 2015-2016 "gtsiokos" George Tsiokos www.netpad.gr
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_notices', 'aat_check_dependencies' );

/**
 * Check plugin dependencies
 */
function aat_check_dependencies() {
	$active_plugins = get_option( 'active_plugins', array() );
	$affiliates_is_active = in_array( 'affiliates/affiliates.php', $active_plugins ) || in_array( 'affiliates-pro/affiliates-pro.php', $active_plugins ) || in_array( 'affiliates-enterprise/affiliates-enterprise.php', $active_plugins );
	//$woocommerce_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
	
	if ( !$affiliates_is_active ) {
		echo "<div class='error'><strong>Affiliates Initial Bonus</strong> plugin requires one of the <a href='http://wordpress.org/plugins/affiliates/'>Affiliates</a>, <a href='http://www.itthinx.com/shop/affiliates-pro/'>Affiliates Pro</a> or <a href='http://www.itthinx.com/shop/affiliates-enterprise/'>Affiliates Enterprise</a> plugins to be installed and activated.</div>";
	}
}

add_filter( 'affiliates_notifications_tokens', 'gt_affiliates_notifications_tokens' );

/**
 * Set additional tokens for referral notification emails
 *
 * @param array $tokens
 * @return array $tokens
 */
function gt_affiliates_notifications_tokens( $tokens ) {
	
	if ( isset( $tokens['affiliate_id'] ) ) {
		if ( 
			function_exists( 'affiliates_get_affiliate' ) &&
			function_exists( 'affiliates_get_affiliate_user' )
		) {
			$user_id = affiliates_get_affiliate_user( $tokens['affiliate_id'] );
			if ( isset( $user_id ) ) {
				$tokens['affiliate_phone'] = get_user_meta( $user_id, 'phone', true );
				$tokens['company_name'] = get_user_meta( $user_id, 'company_name', true );
			}
		}
	}

	return $tokens;
}
