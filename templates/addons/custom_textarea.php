<?php foreach ( $addon['options'] as $key => $option ) :
	$current_value = isset( $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) . '-' . sanitize_title( $option['label'] ) ] ) ? $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) . '-' . sanitize_title( $option['label'] ) ] : '';
	$price = $option['price'] > 0 ? '(' . woocommerce_price( $option['price'] ) . ')' : '';

	if ( empty( $option['label'] ) ) : ?>

		<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
			<textarea type="text" class="input-text addon addon-custom-textarea" data-price="<?php echo $option['price']; ?>" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>-<?php echo sanitize_title( $option['label'] ); ?>" rows="4" cols="20" <?php if ( ! empty( $option['max'] ) ) echo 'maxlength="' . $option['max'] .'"'; ?>><?php echo esc_textarea( $current_value ); ?></textarea>
		</p>

	<?php else : ?>

		<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
			<label><?php echo wptexturize( $option['label'] ) . ' ' . $price; ?> <textarea type="text" class="input-text addon addon-custom-textarea" data-price="<?php echo $option['price']; ?>" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>-<?php echo sanitize_title( $option['label'] ); ?>" rows="4" cols="20" <?php if ( ! empty( $option['max'] ) ) echo 'maxlength="' . $option['max'] .'"'; ?>><?php echo esc_textarea( $current_value ); ?></textarea></label>
		</p>

	<?php endif; ?>

<?php endforeach; ?>