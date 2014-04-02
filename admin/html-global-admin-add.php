<div class="wrap woocommerce">
	<div class="icon32 icon32-posts-product" id="icon-woocommerce"><br/></div>

    <h2><?php _e( 'Add/Edit Global Add-on', 'wc_product_addons' ) ?></h2><br/>

	<form method="POST" action="">
		<table class="form-table global-addons-form">
			<tr>
				<th>
					<label for="addon-reference"><?php _e( 'Global Add-on Reference', 'wc_product_addons' ); ?></label>
				</th>
				<td>
					<input type="text" name="addon-reference" id="addon-reference" style="width:50%;" value="<?php echo esc_attr( $reference ); ?>" />
					<p class="description"><?php _e( 'Give this global add-on a reference/name to make it recognisable.', 'wc_product_addons' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="addon-priority"><?php _e( 'Priority', 'wc_product_addons' ); ?></label>
				</th>
				<td>
					<input type="text" name="addon-priority" id="addon-priority" style="width:50%;" value="<?php echo esc_attr( $priority ); ?>" />
					<p class="description"><?php _e( 'Give this global addon a priority - this will deternmine the order in which multiple groups of addons get displayed on the frontend. Per-product add-ons will always have priority 10.', 'wc_product_addons' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="addon-objects"><?php _e( 'Applied to...', 'wc_product_addons' ); ?></label>
				</th>
				<td>
					<select id="addon-objects" name="addon-objects[]" multiple="multiple" style="width:50%;" data-placeholder="<?php _e('Choose some options&hellip;', 'wc_product_addons'); ?>" class="chosen_select">
						<option value="0" <?php selected( in_array( '0', $objects ), true ); ?>><?php _e( 'All Products', 'wc_product_addons' ); ?></option>
						<optgroup label="<?php _e( 'Product category notifications', 'wc_product_addons' ); ?>">
							<?php
								$terms = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );

								foreach( $terms as $term )
									echo '<option value="' . $term->term_id . '" ' . selected( in_array( $term->term_id, $objects ), true, false ) . '>' . __( 'Category:', 'wc_product_addons' ) . ' ' . $term->name . '</option>';
							?>
						</optgroup>
					</select>
					<p class="description"><?php _e( 'Choose categories which should show these addons (or apply to all products).', 'wc_product_addons' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="addon-objects"><?php _e( 'Add-ons', 'wc_product_addons' ); ?></label>
				</th>
				<td id="poststuff" class="postbox">
					<?php include( 'html-addon-panel.php' ); ?>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="hidden" name="edit_id" value="<?php if ( ! empty( $edit_id ) ) echo $edit_id; ?>" />
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Global Add-on', 'wc_product_addons' ); ?>">
		</p>
	</form>
</div>
<script type="text/javascript">
	// Open/close
	jQuery('.wc-metaboxes-wrapper').on('click', '.wc-metabox h3', function(event){
		// If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
		if (jQuery(event.target).filter(':input, option').length) return;

		jQuery(this).next('.wc-metabox-content').toggle();
	})
	.on('click', '.expand_all', function(event){
		jQuery(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox > table').show();
		return false;
	})
	.on('click', '.close_all', function(event){
		jQuery(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox > table').hide();
		return false;
	});
	jQuery('.wc-metabox.closed').each(function(){
		jQuery(this).find('.wc-metabox-content').hide();
	});
</script>