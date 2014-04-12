<?php

/**
 * Product_Addon_Display class.
 */
class Product_Addon_Display {

	var $version = '2.2.0';

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Styles
		add_action( 'get_header', array( $this, 'styles' ) );
		add_action( 'wc_quick_view_enqueue_scripts', array( $this, 'addon_scripts' ) );

		// Addon display on single product page
      add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'display' ), 10 );
      // Addon display on archive
      //add_action( 'woocommerce_after_shop_loop_item', array( $this, 'display' ), 10 );
      add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'wc_closeform' ), 10 );

		add_action( 'wc_product_addons_end', array( $this, 'totals' ), 10 );

		// Change buttons/cart urls
		add_filter( 'add_to_cart_text', array( $this, 'add_to_cart_text'), 15 );
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text'), 15 );
		add_filter( 'woocommerce_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 1 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 1 );
	}

   /**
    * close the form after add to cart button
    */
   function wc_closeform() {
      if(!is_product())              
        echo '</form>'; 
   }

	/**
	 * styles function.
	 *
	 * @access public
	 * @return void
	 */
	function styles() {
		if ( is_singular( 'product' ) || class_exists( 'WC_Quick_View' ) )
			wp_enqueue_style( 'woocommerce-addons-css', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/assets/css/frontend.css' );
	}

	/**
	 * Get the plugin path
	 */
	function plugin_path() {
		return $this->plugin_path = untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
	}

	/**
	 * Enqueue addon scripts
	 */
	function addon_scripts() {
      wp_register_style( 'woocommerce-better-addons', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/assets/css/frontend.css', '', '0.3.2' );
      
      wp_enqueue_style( 'woocommerce-better-addons' );

		wp_register_script( 'accounting', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/assets/js/accounting.js', '', '0.3.2' );
      
		wp_enqueue_script( 'woocommerce-addons', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/assets/js/addons.js', array( 'jquery', 'accounting' ), '1.0', true );

		$params = array(
			'i18n_addon_total'             => __( 'Options total:', 'wc_product_addons' ),
			'i18n_grand_total'             => __( 'Total:', 'wc_product_addons' ),
			'i18n_remaining'               => __( 'characters remaining', 'wc_product_addons' ),
			'currency_format_num_decimals' => absint( get_option( 'woocommerce_price_num_decimals' ) ),
			'currency_format_symbol'       => get_woocommerce_currency_symbol(),
			'currency_format_decimal_sep'  => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep' => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) )
		);

		if ( ! function_exists( 'get_woocommerce_price_format' ) ) {
			$currency_pos = get_option( 'woocommerce_currency_pos' );

			switch ( $currency_pos ) {
				case 'left' :
					$format = '%1$s%2$s';
				break;
				case 'right' :
					$format = '%2$s%1$s';
				break;
				case 'left_space' :
					$format = '%1$s&nbsp;%2$s';
				break;
				case 'right_space' :
					$format = '%2$s&nbsp;%1$s';
				break;
			}

			$params['currency_format'] = esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), $format ) );
		} else {
			$params['currency_format'] = esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) );
		}

		wp_localize_script( 'woocommerce-addons', 'woocommerce_addons_params', $params );
	}

	/**
	 * display function.
	 *
	 * @access public
	 * @param bool $post_id (default: false)
	 * @return void
	 */
	function display( $post_id = false, $prefix = false ) {
		global $product;

		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}

		$this->addon_scripts();

		$product_addons = get_product_addons( $post_id, $prefix );

		if ( is_array( $product_addons ) && sizeof( $product_addons ) > 0 ) {

         if(!is_product())
            echo '<form class="cart">';

			do_action( 'wc_product_addons_start', $post_id );

			foreach ( $product_addons as $addon ) {

				if ( ! isset( $addon['field-name'] ) )
					continue;

				woocommerce_get_template( 'addons/addon-start.php', array(
						'addon'       => $addon,
						'required'    => $addon['required'],
						'name'        => $addon['name'],
						'description' => $addon['description'],
						'type'        => $addon['type'],
					), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );

				echo $this->get_addon_html( $addon );

				woocommerce_get_template( 'addons/addon-end.php', array(
						'addon'    => $addon,
					), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );
			}

			do_action( 'wc_product_addons_end', $post_id );
         //if(!is_product())
         //   echo "</form>";
		}
	}

	/**
	 * totals function.
	 *
	 * @access public
	 * @return void
	 */
	function totals( $post_id ) {

		global $product;

		if ( ! isset( $product ) || $product->id != $post_id )
			$the_product = get_product( $post_id );
		else
			$the_product = $product;

		echo '<div data-addons-pid="'.$the_product->id.'" data-total="product-addons-total" data-type="' . $the_product->product_type . '" data-price="' . ( is_object( $the_product ) ? $the_product->get_price() : '' ) . '"></div>';
	}

	/**
	 * get_addon_html function.
	 *
	 * @access public
	 * @param mixed $addon
	 * @return void
	 */
	function get_addon_html( $addon ) {

		ob_start();

		$method_name   = 'get_' . $addon['type'] . '_html';

		if ( method_exists( $this, $method_name ) ) {
			$this->$method_name( $addon );
		}

		do_action( 'wc_product_addons_get_' . $addon['type'] . '_html', $addon );

		return ob_get_clean();
	}

	/**
	 * get_checkbox_html function.
	 *
	 * @access public
	 * @return void
	 */
	function get_checkbox_html( $addon ) {
		woocommerce_get_template( 'addons/checkbox.php', array(
				'addon' => $addon,
			), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );
	}

	/**
	 * get_radiobutton_html function.
	 *
	 * @access public
	 * @param mixed $addon
	 * @return void
	 */
	function get_radiobutton_html( $addon ) {
		woocommerce_get_template( 'addons/radiobutton.php', array(
				'addon' => $addon,
			), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );
	}

	/**
	 * get_select_html function.
	 *
	 * @access public
	 * @return void
	 */
	function get_select_html( $addon ) {
		woocommerce_get_template( 'addons/select.php', array(
				'addon' => $addon,
			), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );
	}

	/**
	 * get_custom_html function.
	 *
	 * @access public
	 * @return void
	 */
	function get_custom_html( $addon ) {
		woocommerce_get_template( 'addons/custom.php', array(
				'addon' => $addon,
			), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );
	}

	/**
	 * get_custom_textarea function.
	 *
	 * @access public
	 * @return void
	 */
	function get_custom_textarea_html( $addon ) {
		woocommerce_get_template( 'addons/custom_textarea.php', array(
				'addon' => $addon,
			), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );
	}

	/**
	 * get_file_upload_html function.
	 *
	 * @access public
	 * @return void
	 */
	function get_file_upload_html( $addon ) {
		woocommerce_get_template( 'addons/file_upload.php', array(
				'addon' => $addon,
				'max_size' => $this->max_upload_size()
			), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );
	}

	/**
	 * get_custom_price_html function.
	 *
	 * @access public
	 * @return void
	 */
	function get_custom_price_html( $addon ) {
		woocommerce_get_template( 'addons/custom_price.php', array(
				'addon' => $addon,
			), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );
	}

	/**
	 * get_input_multiplier_html function.
	 *
	 * @access public
	 * @return void
	 */
	function get_input_multiplier_html( $addon ) {
		woocommerce_get_template( 'addons/input_multiplier.php', array(
				'addon' => $addon,
			), 'woocommerce-product-addons', $this->plugin_path() . '/templates/' );
	}

	/**
	 * check_required_addons function.
	 *
	 * @access private
	 * @param mixed $product_id
	 * @return void
	 */
	private function check_required_addons( $product_id ) {
		$addons = get_product_addons( $product_id );
		if ( $addons && ! empty( $addons ) ) {
			foreach ( $addons as $addon ) {
				if ( '1' == $addon['required'] ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * add_to_cart_text function.
	 *
	 * @access public
	 * @param mixed $text
	 * @return void
	 */
	public function add_to_cart_text( $text ) {
		global $product;
      // XXX Remove it also from archive product!
		if ( ! is_single( $product->id ) ) {
			if ( $this->check_required_addons( $product->id ) ) {
				$product->product_type = 'addons';
				$text = apply_filters( 'addons_add_to_cart_text', __( 'Select options', 'wc_product_addons' ) );
         }
		}
		return $text;
	}

	/**
	 * add_to_cart_url function.
	 *
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	function add_to_cart_url( $url ) {
		global $product;
      // XXX Remove it also from archive product
		if ( ! is_single( $product->id ) && in_array( $product->product_type, array( 'subscription', 'simple' ) ) && ( ! isset( $_GET['wc-api'] ) || $_GET['wc-api'] !== 'WC_Quick_View' ) ) {
			if ( $this->check_required_addons( $product->id ) ) {
				$product->product_type = 'addons';
				$url = apply_filters( 'addons_add_to_cart_url', get_permalink( $product->id ) );
			}
		}

		return $url;
	}

	/**
	 * max_upload_size function.
	 *
	 * @access public
	 * @return void
	 */
	function max_upload_size() {
		$u_bytes = $this->convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
		$p_bytes = $this->convert_hr_to_bytes( ini_get( 'post_max_size' ) );
		$bytes = min($u_bytes, $p_bytes);
		return $this->convert_bytes_to_hr( $bytes );
	}

	/**
	 * convert_hr_to_bytes function.
	 *
	 * @access public
	 * @param mixed $size
	 * @return void
	 */
	function convert_hr_to_bytes( $size ) {
		$size = strtolower($size);
		$bytes = (int) $size;
		if ( strpos($size, 'k') !== false )
			$bytes = intval($size) * 1024;
		elseif ( strpos($size, 'm') !== false )
			$bytes = intval($size) * 1024 * 1024;
		elseif ( strpos($size, 'g') !== false )
			$bytes = intval($size) * 1024 * 1024 * 1024;
		return $bytes;
	}

	/**
	 * convert_bytes_to_hr function.
	 *
	 * @access public
	 * @param mixed $bytes
	 * @return void
	 */
	function convert_bytes_to_hr( $bytes ) {
		$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
		$log = log( $bytes, 1024 );
		$power = (int) $log;
		$size = pow(1024, $log - $power);
		return $size . $units[$power];
	}
}
