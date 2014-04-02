<div id="product_addons_data" class="panel woocommerce_options_panel">

	<div class="wc-metaboxes-wrapper">

		<p class="toolbar">
			<a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce' ); ?></a><a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce' ); ?></a>
		</p>

		<div class="woocommerce_product_addons wc-metaboxes">

			<?php
				$loop = 0;

				foreach ( $product_addons as $addon ) {
					include( 'html-addon.php' );

					$loop++;
				}
			?>

		</div>

		<div class="toolbar">
			<button type="button" class="button add_new_addon button-primary"><?php _e( 'New Addon Group', 'wc_product_addons' ); ?></button>

			<button type="button" class="button import_addons"><?php _e( 'Import', 'wc_product_addons' ); ?></button>
			<button type="button" class="button export_addons"><?php _e( 'Export', 'wc_product_addons' ); ?></button>

			<textarea name="export_product_addon" class="export" cols="20" rows="5" readonly="readonly"><?php echo esc_textarea( serialize( $product_addons ) ); ?></textarea>

			<textarea name="import_product_addon" class="import" cols="20" rows="5" placeholder="<?php _e('Paste exported form data here and then save to import fields. The imported fields will be appended.', 'wc_product_addons'); ?>"></textarea>

		</div>
		<?php if( isset($post->ID) ):?>
        <div class="options_group">
			<p class="form-field">
            <label for="_product_addons_exclude_global"><?php _e( 'Global Addon Exclusion', 'wc_product_addons' ); ?></label>
            <input id="_product_addons_exclude_global" name="_product_addons_exclude_global" class="checkbox" type="checkbox" value="1" <?php checked( get_post_meta( $post->ID, '_product_addons_exclude_global', TRUE ), 1 ); ?>/><span class="description"><?php _e( 'Check this to exclude this product from all Global Addons', 'wc_product_addons' ); ?></span>
			</p>
		</div>
		<?php endif; ?>
	</div>

</div>
<script type="text/javascript">
	jQuery(function(){

		jQuery( 'select.chosen_select' ).chosen();

		jQuery('#product_addons_data')
		.on( 'change', '.addon_name input', function() {
			if ( jQuery(this).val() )
				jQuery(this).closest('.woocommerce_product_addon').find('span.group_name').text( '"' + jQuery(this).val() + '"' );
			else
				jQuery(this).closest('.woocommerce_product_addon').find('span.group_name').text('');
		})
		.on( 'change', 'select.product_addon_type', function() {

			var value = jQuery(this).val();

			if ( value == 'custom' || value == 'custom_price' || value == 'custom_textarea' || value == 'input_multiplier' ) {
				jQuery(this).closest('.woocommerce_product_addon').find('td.minmax_column, th.minmax_column').show();
			} else {
				jQuery(this).closest('.woocommerce_product_addon').find('td.minmax_column, th.minmax_column').hide();
			}

			if ( value == 'custom_price' ) {
				jQuery(this).closest('.woocommerce_product_addon').find('td.price_column, th.price_column').hide();
			} else {
				jQuery(this).closest('.woocommerce_product_addon').find('td.price_column, th.price_column').show();
			}
		})
		.on( 'click', 'button.add_addon_option', function() {

			var loop = jQuery(this).closest('.woocommerce_product_addon').index('.woocommerce_product_addon');

			var html = '<?php
				ob_start();

				$option['label'] 	= '';
				$option['price']	= '';
				$option['min'] 		= '';
				$option['max'] 		= '';
				$loop = "{loop}";

				include( 'html-addon-option.php' );

				$html = ob_get_clean();
				echo str_replace( array( "\n", "\r" ), '', str_replace( "'", '"', $html ) );
			?>';

			html = html.replace( /{loop}/g, loop );

			jQuery(this).closest('.woocommerce_product_addon .data').find('tbody').append( html );

			jQuery('select.product_addon_type').change();

			return false;
		})
		.on( 'click', '.add_new_addon', function() {

			var loop = jQuery('.woocommerce_product_addons .woocommerce_product_addon').size();

			var html = '<?php
				ob_start();

				$addon['name'] 			= '';
				$addon['description']	= '';
				$addon['required'] 		= '';
				$addon['type'] 			= 'checkbox';
				$addon['options'] 		= array();
				$loop = "{loop}";

				include( 'html-addon.php' );

				$html = ob_get_clean();
				echo str_replace( array( "\n", "\r" ), '', str_replace( "'", '"', $html ) );
			?>';

			html = html.replace( /{loop}/g, loop );

			jQuery('.woocommerce_product_addons').append( html );

			jQuery('select.product_addon_type').change();

			return false;
		})
		.on( 'click', '.remove_addon', function() {

			var answer = confirm('<?php _e('Are you sure you want remove this add-on?', 'wc_product_addons'); ?>');

			if (answer) {
				var addon = jQuery(this).closest('.woocommerce_product_addon');
				jQuery(addon).find('input').val('');
				jQuery(addon).hide();
			}

			return false;
		})
		.find('select.product_addon_type').change();

		// Import / Export
		jQuery('#product_addons_data').on('click', '.export_addons', function() {

			jQuery('#product_addons_data textarea.import').hide();
			jQuery('#product_addons_data textarea.export').slideToggle('500', function() {
				jQuery(this).select();
			});

			return false;
		});

		jQuery('#product_addons_data').on('click', '.import_addons', function() {

			jQuery('#product_addons_data textarea.export').hide();
			jQuery('#product_addons_data textarea.import').slideToggle('500', function() {
				jQuery(this).val('');
			});

			return false;
		});

		// Sortable
		jQuery('.woocommerce_product_addons').sortable({
			items:'.woocommerce_product_addon',
			cursor:'move',
			axis:'y',
			handle:'h3',
			scrollSensitivity:40,
			helper:function(e,ui){
				return ui;
			},
			start:function(event,ui){
				ui.item.css('border-style','dashed');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
				addon_row_indexes();
			}
		});

		function addon_row_indexes() {
			jQuery('.woocommerce_product_addons .woocommerce_product_addon').each(function(index, el){ jQuery('.product_addon_position', el).val( parseInt( jQuery(el).index('.woocommerce_product_addons .woocommerce_product_addon') ) ); });
		};

		// Sortable options
		jQuery('.woocommerce_product_addon .data table tbody').sortable({
			items:'tr',
			cursor:'move',
			axis:'y',
			scrollSensitivity:40,
			helper:function(e,ui){
				ui.children().each(function(){
					jQuery(this).width(jQuery(this).width());
				});
				return ui;
			},
			start:function(event,ui){
				ui.item.css('background-color','#f6f6f6');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
			}
		});

		// Remove option
		jQuery('button.remove_addon_option').live('click', function(){

			var answer = confirm('<?php _e('Are you sure you want delete this option?', 'wc_product_addons'); ?>');

			if (answer) {
				jQuery(this).closest('tr').remove();
			}

			return false;

		});

	});
</script>