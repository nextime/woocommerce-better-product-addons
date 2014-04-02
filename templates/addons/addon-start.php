<div class="<?php if ( 1 == $required ) echo 'required-product-addon'; ?> product-addon product-addon-<?php echo sanitize_title( $name ); ?>">

	<?php do_action( 'wc_product_addon_start', $addon ); ?>

	<?php if ( $name ) : ?>
		<h3 class="addon-name"><?php echo wptexturize( $name ); ?> <?php if ( 1 == $required ) echo '<abbr class="required" title="required">*</abbr>'; ?></h3>
	<?php endif; ?>

	<?php if ( $description ) : ?>
		<?php echo '<div class="addon-description">' . wpautop( wptexturize( $description ) ) . '</div>'; ?>
	<?php endif; ?>

	<?php do_action( 'wc_product_addon_options', $addon ); ?>
