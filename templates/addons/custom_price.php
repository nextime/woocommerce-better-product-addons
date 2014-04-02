<?php foreach ( $addon['options'] as $key => $option ) :

	$current_value = isset( $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) . '-' . sanitize_title( $option['label'] ) ] ) ? $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) . '-' . sanitize_title( $option['label'] ) ] : '';
	$price = $option['price'] > 0 ? '(' . woocommerce_price( $option['price'] ) . ')' : '';

	if ( empty( $option['label'] ) ) : ?>

		<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
			<input type="number" step="any" class="input-text addon addon-custom-price" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>-<?php echo sanitize_title( $option['label'] ); ?>" value="<?php echo esc_attr( $current_value ); ?>" <?php if ( ! empty( $option['min'] ) ) echo 'min="' . $option['min'] .'"'; ?> <?php if ( ! empty( $option['max'] ) ) echo 'max="' . $option['max'] .'"'; ?> />
		</p>

	<?php else : ?>

		<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
			<label><?php echo wptexturize( $option['label'] ) . ' ' . $price; ?> <input type="number" step="any" class="input-text addon addon-custom-price" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>-<?php echo sanitize_title( $option['label'] ); ?>" value="<?php echo esc_attr( $current_value ); ?>" <?php if ( ! empty( $option['min'] ) ) echo 'min="' . $option['min'] .'"'; ?> <?php if ( ! empty( $option['max'] ) ) echo 'max="' . $option['max'] .'"'; ?> /></label>
		</p>

	<?php endif; ?>

<?php endforeach; ?>