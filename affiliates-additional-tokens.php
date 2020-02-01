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

add_action( 'plugins_loaded', 'aat_init' );

/**
 * Initializing
 */
function aat_init() {
	$dependencies = aat_check_dependencies();
	if ( $dependencies ) {
		add_filter( 'affiliates_notifications_tokens', 'aat_affiliates_notifications_tokens' );
		add_filter( 'affiliates_registration_tokens', 'aat_affiliates_registration_tokens' );
	}
}

/**
 * Check plugin dependencies
 *
 * @return bool
 */
function aat_check_dependencies() {
	$active_plugins = get_option( 'active_plugins', array() );
	$affiliates_is_active = in_array( 'affiliates-pro/affiliates-pro.php', $active_plugins ) || in_array( 'affiliates-enterprise/affiliates-enterprise.php', $active_plugins );
	return $affiliates_is_active;
}

/**
 * Set additional tokens for referral notification emails
 *
 * @param array $tokens
 * @return array $tokens
 */
function aat_affiliates_notifications_tokens( $tokens ) {

	if ( isset( $tokens['affiliate_id'] ) ) {
		if ( function_exists( 'affiliates_get_affiliate_user' ) ) {
			$user_id = affiliates_get_affiliate_user( $tokens['affiliate_id'] );
			if ( isset( $user_id ) ) {
				$tokens['affiliate_phone'] = get_user_meta( $user_id, 'phone', true );
				$tokens['company_name'] = get_user_meta( $user_id, 'company_name', true );
			}
		}
	}
	return $tokens;
}

/**
 * Set additional tokens for registration notification emails
 *
 * @param array $tokens
 * @return array $tokens
 */
function aat_affiliates_registration_tokens( $tokens ) {
	if ( !class_exists( 'Affiliates_Service' ) ) {
		require_once( AFFILIATES_CORE_DIR . 'lib/core/class-affiliates-service.php' );
	}

	$referrer_affiliate_id = Affiliates_Service::get_referrer_id();
	if ( $referrer_affiliate_id ) {
		$referrer_user_id = affiliates_get_affiliate_user( $referrer_affiliate_id );
		if ( isset( $referrer_user_id ) ) {
			$tokens['referring_affiliate_phone'] = get_user_meta( $referrer_user_id, 'phone', true );
			$tokens['referring_affiliate_email'] = get_user_meta( $referrer_user_id, 'email', true );
			$tokens['referring_affiliate_first_name'] = get_user_meta( $referrer_user_id, 'first_name', true );
			$tokens['referring_affiliate_last_name'] = get_user_meta( $referrer_user_id, 'last_name', true );
		}
	}
	return $tokens;
}
