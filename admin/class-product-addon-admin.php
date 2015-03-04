<?php

/**
 * Product_Addon_Admin class.
 */
class Product_Addon_Admin {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'woocommerce_admin_css', array( $this, 'styles' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id' ) );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'tab' ) );
		add_action( 'woocommerce_product_write_panels', array( $this, 'panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_meta_box'), 1, 2 );
	}

	/**
	 * Add menus
	 */
	public function admin_menu() {
		$page = add_submenu_page( 'edit.php?post_type=product', __( 'Global Add-ons', 'wc_product_addons' ), __( 'Global Add-ons', 'wc_product_addons' ), 'manage_woocommerce', 'global_addons', array( $this, 'global_addons_admin' ) );

		add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );
	}

	/**
	 * admin_enqueue function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_enqueue() {
		wp_enqueue_script( 'chosen' );
	}

	/**
	 * styles function.
	 *
	 * @access public
	 * @return void
	 */
	public function styles() {
    	wp_enqueue_style( 'woocommerce_product_addons_css', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/assets/css/admin.css' );
    }

	/**
	 * Add screen id to WooCommerce
	 *
	 * @param array $screen_ids
	 */
	public function add_screen_id( $screen_ids ) {
		$screen_ids[] = 'product_page_global_addons';
		return $screen_ids;
	}

	/**
	 * Controls the global addons admin page
	 * @return void
	 */
	public function global_addons_admin() {
		if ( ! empty( $_GET['add'] ) || ! empty( $_GET['edit'] ) ) {

			if ( $_POST ) {

				if ( $this->save_global_addons() ) {
					echo '<div class="updated"><p>' . __( 'Add-on saved successfully', 'wc_product_addons' ) . '</p></div>';
				}

				$reference      = woocommerce_clean( $_POST['addon-reference'] );
				$priority       = absint( $_POST['addon-priority'] );
				$objects        = ! empty( $_POST['addon-objects'] ) ? array_map( 'absint', $_POST['addon-objects'] ) : array();
				$product_addons = array_filter( (array) $this->get_posted_product_addons() );

			} 

			if ( ! empty( $_GET['edit'] ) ) {

				$edit_id        = absint( $_GET['edit'] );
				$global_addon   = get_post( $edit_id );

				if ( ! $global_addon ) {
					echo '<div class="error">' . __( 'Error: Global Add-on not found', 'wc_product_addons' ) . '</div>';
					return;
				}

				$reference      = $global_addon->post_title;
				$priority       = get_post_meta( $global_addon->ID, '_priority', true );
				$objects        = (array) wp_get_post_terms( $global_addon->ID, 'product_cat', array( 'fields' => 'ids' ) );
				$product_addons = array_filter( (array) get_post_meta( $global_addon->ID, '_product_addons', true ) );

				if ( get_post_meta( $global_addon->ID, '_all_products', true ) == 1 )
					$objects[] = 0;

			} else {

				$global_addons_count = wp_count_posts( 'global_product_addon' );
				$reference           = __( 'Global Add-on Group' ) . ' #' . ( $global_addons_count->publish + 1 );
				$priority            = 10;
				$objects             = array( 0 );
				$product_addons      = array();

			}

			include( 'html-global-admin-add.php' );
		} else {

			if ( ! empty( $_GET['delete'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'delete_addon' ) ) {
				wp_delete_post( absint( $_GET['delete'] ), true );
				echo '<div class="updated"><p>' . __( 'Add-on deleted successfully', 'wc_product_addons' ) . '</p></div>';
			}

			include( 'html-global-admin.php' );
		}
	}

	/**
	 * tab function.
	 *
	 * @access public
	 * @return void
	 */
	public function tab() {
		?><li class="addons_tab product_addons"><a href="#product_addons_data"><?php _e( 'Add-ons', 'wc_product_addons' ); ?></a></li><?php
	}

	/**
	 * panel function.
	 *
	 * @access public
	 * @return void
	 */
	public function panel() {
		global $post;

		$product_addons = array_filter( (array) get_post_meta( $_GET['post'], '_product_addons', true ) );

		include( 'html-addon-panel.php' );
	}

	/**
	 * Save global addons
	 *
	 * @return bool success or failure
	 */
	public function save_global_addons() {
		$edit_id		= ! empty( $_POST['edit_id'] ) ? absint( $_POST['edit_id'] ) : '';
		$reference      = woocommerce_clean( $_POST['addon-reference'] );
		$priority       = absint( $_POST['addon-priority'] );
		$objects        = ! empty( $_POST['addon-objects'] ) ? array_map( 'absint', $_POST['addon-objects'] ) : array();
		$product_addons = $this->get_posted_product_addons();

		if ( ! $reference ) {
			$global_addons_count = wp_count_posts( 'global_product_addon' );
			$reference           = __( 'Global Add-on Group' ) . ' #' . ( $global_addons_count->publish + 1 );
		}

		if ( ! $priority && $priority !== 0 )
			$priority = 10;

		if ( $edit_id ) {

			$edit_post               = array();
			$edit_post['ID']         = $edit_id;
			$edit_post['post_title'] = $reference;

			wp_update_post( $edit_post );
			wp_set_post_terms( $edit_id, $objects, 'product_cat', false );

		} else {

			$edit_id = wp_insert_post( array(
				'post_title'    => $reference,
				'post_status'   => 'publish',
				'post_type'		=> 'global_product_addon',
				'tax_input'     => array(
				'product_cat' => $objects
				)
			) );

		}

		if ( in_array( 0, $objects ) )
			update_post_meta( $edit_id, '_all_products', 1 );
		else
			update_post_meta( $edit_id, '_all_products', 0 );

		update_post_meta( $edit_id, '_priority', $priority );
		update_post_meta( $edit_id, '_product_addons', $product_addons );

		return true;
	}

    /**
     * process_meta_box function.
     *
     * @access public
     * @param mixed $post_id
     * @param mixed $post
     * @return void
     */
    public function process_meta_box( $post_id, $post ) {
    	// Save addons as serialised array
		$product_addons 				= $this->get_posted_product_addons();
		$product_addons_exclude_global 	= isset( $_POST['_product_addons_exclude_global'] ) ? 1 : 0;

		update_post_meta( $post_id, '_product_addons', $product_addons );
		update_post_meta( $post_id, '_product_addons_exclude_global', $product_addons_exclude_global );
    }

    /**
     * Put posted addon data into an array
     *
     * @return [type] [description]
     */
    private function get_posted_product_addons() {
		$product_addons = array();

		if ( isset( $_POST[ 'product_addon_name' ] ) ) {
			 $addon_name			= $_POST['product_addon_name'];
			 $addon_description		= $_POST['product_addon_description'];
			 $addon_type 			= $_POST['product_addon_type'];
			 $addon_position 		= $_POST['product_addon_position'];
			 $addon_required		= isset( $_POST['product_addon_required'] ) ? $_POST['product_addon_required'] : array();

			 $addon_option_label	= $_POST['product_addon_option_label'];
			 $addon_option_price	= $_POST['product_addon_option_price'];

			 $addon_option_min		= $_POST['product_addon_option_min'];
			 $addon_option_max		= $_POST['product_addon_option_max'];

			 for ( $i = 0; $i < sizeof( $addon_name ); $i++ ) {

			 	if ( ! isset( $addon_name[ $i ] ) || ( '' == $addon_name[ $i ] ) ) continue;

			 	$addon_options 	= array();
			 	$option_label  	= $addon_option_label[ $i ];
			 	$option_price  	= $addon_option_price[ $i ];
			 	$option_min		= $addon_option_min[ $i ];
			 	$option_max		= $addon_option_max[ $i ];

			 	for ( $ii = 0; $ii < sizeof( $option_label ); $ii++ ) {
			 		$label 	= sanitize_text_field( stripslashes( $option_label[ $ii ] ) );
			 		$price 	= sanitize_text_field( stripslashes( $option_price[ $ii ] ) );
			 		$min	= sanitize_text_field( stripslashes( $option_min[ $ii ] ) );
			 		$max	= sanitize_text_field( stripslashes( $option_max[ $ii ] ) );

		 			$addon_options[] = array(
		 				'label' => $label,
		 				'price' => $price,
		 				'min'	=> $min,
		 				'max'	=> $max
		 			);
			 	}

			 	if ( sizeof( $addon_options ) == 0 )
			 		continue; // Needs options

			 	// Add to array
			 	$product_addons[] = array(
			 		'name' 			=> sanitize_text_field( stripslashes( $addon_name[ $i ] ) ),
			 		'description' 	=> wp_kses_post( stripslashes( $addon_description[ $i ] ) ),
			 		'type' 			=> sanitize_text_field( stripslashes( $addon_type[ $i ] ) ),
			 		'position'		=> absint( $addon_position[ $i ] ),
			 		'options' 		=> $addon_options,
			 		'required'		=> isset( $addon_required[ $i ] ) ? 1 : 0,
			 	);
			}
		}

		if ( ! empty( $_POST['import_product_addon'] ) ) {
			$import_addons = maybe_unserialize( maybe_unserialize( stripslashes( trim( $_POST['import_product_addon'] ) ) ) );

			if ( is_array( $import_addons ) && sizeof( $import_addons ) > 0 ) {
				$valid = true;

				foreach ( $import_addons as $addon ) {
					if ( ! isset( $addon['name'] ) || ! $addon['name'] ) $valid = false;
					if ( ! isset( $addon['description'] ) ) $valid = false;
					if ( ! isset( $addon['type'] ) ) $valid = false;
					if ( ! isset( $addon['position'] ) ) $valid = false;
					if ( ! isset( $addon['options'] ) ) $valid = false;
					if ( ! isset( $addon['required'] ) ) $valid = false;
				}

				if ( $valid ) {
					$product_addons = array_merge( $product_addons, $import_addons );
				}
			}
		}

		uasort( $product_addons, array( $this, 'addons_cmp' ) );

		return $product_addons;
    }

	/**
	 * Sort addons
	 *
	 * @param  [type] $a [description]
	 * @param  [type] $b [description]
	 * @return [type]    [description]
	 */
    private function addons_cmp( $a, $b ) {
	    if ( $a['position'] == $b['position'] ) {
	        return 0;
	    }

	    return ( $a['position'] < $b['position'] ) ? -1 : 1;
	}
}