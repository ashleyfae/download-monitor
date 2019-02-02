<?php

namespace Never5\DownloadMonitor\Ecommerce\Util;

use Never5\DownloadMonitor\Ecommerce\Ajax;
use Never5\DownloadMonitor\Ecommerce\Services\Services;

class Assets {

	/**
	 * Setup hook
	 */
	public function setup() {
		add_action( 'dlm_frontend_scripts_after', array( $this, 'enqueue_assets' ) );
		add_action( 'dlm_admin_scripts_after', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Enqueue assets
	 */
	public function enqueue_assets() {

		if ( Services::get()->service( 'page' )->is_cart() ) {
			wp_enqueue_style( 'dlm-frontend-cart', download_monitor()->get_plugin_url() . '/assets/css/cart.css' );
		}

		if ( Services::get()->service( 'page' )->is_checkout() ) {
			wp_enqueue_style( 'dlm-frontend-checkout', download_monitor()->get_plugin_url() . '/assets/css/checkout.css' );

			wp_enqueue_script(
				'dlm-frontend-checkout-js',
				plugins_url( '/assets/js/shop/checkout' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', download_monitor()->get_plugin_file() ),
				array( 'jquery' ),
				DLM_VERSION
			);

			// Make JavaScript strings translatable
			wp_localize_script( 'dlm-frontend-checkout-js', 'dlm_strings', array(
				'ajax_url_place_order' => Ajax\Manager::get_ajax_url( 'place_order' )
			) );
		}

	}

	/**
	 * Enqueue shop backend assets
	 */
	public function enqueue_admin_assets() {
		global $pagenow;

		if (
			'edit.php' == $pagenow
			&& isset( $_GET['post_type'] )
			&& 'dlm_download' === $_GET['post_type']
			&& isset( $_GET['page'] )
			&& 'download-monitor-orders' == $_GET['page']
			&& isset( $_GET['details'] )
		) {

			// Enqueue order details script
			wp_enqueue_script(
				'dlm_admin_order_details',
				plugins_url( '/assets/js/shop/admin-order-details' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', download_monitor()->get_plugin_file() ),
				array( 'jquery' ),
				DLM_VERSION
			);

			wp_localize_script( 'dlm_admin_order_details', 'dlm_strings', array(
				'ajax_url_change_order_status' => Ajax\Manager::get_ajax_url( 'admin_change_order_status' ),
				'order_id'                     => absint( $_GET['details'] )
			) );

		}

	}

}