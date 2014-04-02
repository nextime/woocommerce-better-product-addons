<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="woocommerce_product_addon wc-metabox closed">
	<h3>
		<button type="button" class="remove_addon button"><?php _e( 'Remove', 'woocommerce' ); ?></button>
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'woocommerce' ); ?>"></div>
		<strong><?php _e( 'Group', 'woocommerce' ); ?> <span class="group_name"><?php if ( $addon['name'] ) echo '"' . esc_attr( $addon['name'] ) . '"'; ?></span> &mdash; </strong>
		<select name="product_addon_type[<?php echo $loop; ?>]" class="product_addon_type">
			<option <?php selected('checkbox', $addon['type']); ?> value="checkbox"><?php _e('Checkboxes', 'wc_product_addons'); ?></option>
			<option <?php selected('radiobutton', $addon['type']); ?> value="radiobutton"><?php _e('Radio buttons', 'wc_product_addons'); ?></option>
			<option <?php selected('select', $addon['type']); ?> value="select"><?php _e('Select box', 'wc_product_addons'); ?></option>
			<option <?php selected('custom', $addon['type']); ?> value="custom"><?php _e('Custom input (text)', 'wc_product_addons'); ?></option>
			<option <?php selected('custom_textarea', $addon['type']); ?> value="custom_textarea"><?php _e('Custom input (textarea)', 'wc_product_addons'); ?></option>
			<option <?php selected('file_upload', $addon['type']); ?> value="file_upload"><?php _e('File upload', 'wc_product_addons'); ?></option>
			<option <?php selected('custom_price', $addon['type']); ?> value="custom_price"><?php _e('Custom price input', 'wc_product_addons'); ?></option>
            <option <?php selected('input_multiplier', $addon['type']); ?> value="input_multiplier"><?php _e('Custom Input Multipler', 'wc_product_addons'); ?></option>
		</select>
		<input type="hidden" name="product_addon_position[<?php echo $loop; ?>]" class="product_addon_position" value="<?php echo $loop; ?>" />
	</h3>
	<table cellpadding="0" cellspacing="0" class="wc-metabox-content">
		<tbody>
			<tr>
				<td class="addon_name" width="50%">
					<label for="addon_name_<?php echo $loop; ?>"><?php _e( 'Group Name', 'woocommerce' ); ?></label>
					<input type="text" id="addon_name_<?php echo $loop; ?>" name="product_addon_name[<?php echo $loop; ?>]" value="<?php echo esc_attr( $addon['name'] ) ?>" />
				</td>
				<td class="addon_required" width="50%">
					<label for="addon_required_<?php echo $loop; ?>"><?php _e( 'Required fields?', 'wc_product_addons' ); ?></label>
					<input type="checkbox" id="addon_required_<?php echo $loop; ?>" name="product_addon_required[<?php echo $loop; ?>]" <?php checked( $addon['required'], 1 ) ?> />
				</td>
			</tr>
			<tr>
				<td class="addon_description" colspan="2">
					<label for="addon_description_<?php echo $loop; ?>"><?php _e( 'Group Description', 'woocommerce' ); ?></label>
					<textarea cols="20" id="addon_description_<?php echo $loop; ?>" rows="3" name="product_addon_description[<?php echo $loop; ?>]"><?php echo esc_textarea( $addon['description'] ) ?></textarea>
				</td>
			</tr>
			<tr>
				<td class="data" colspan="3">
					<table cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th><?php _e('Option Label', 'wc_product_addons'); ?></th>
								<th class="price_column"><?php _e('Option Price', 'wc_product_addons'); ?></th>
								<th class="minmax_column"><?php _e('Min', 'wc_product_addons'); ?></th>
								<th class="minmax_column"><?php _e('Max', 'wc_product_addons'); ?></th>
								<th width="1%"></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5"><button type="button" class="add_addon_option button"><?php _e('New&nbsp;Option', 'wc_product_addons'); ?></button></td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							foreach ( $addon['options'] as $option )
								include( 'html-addon-option.php' );
							?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
