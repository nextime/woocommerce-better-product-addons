<?php
$classes = array();

if ( 1 == $required ) {
  $classes[] = 'required-product-addon';
}

$classes[] = 'product-addon product-addon-' . sanitize_title( $name );
$classes[] = $class;
?>
<div class="<?php echo implode(' ', $classes); ?>">

	<?php do_action( 'wc_product_addon_start', $addon ); ?>

	<?php if ( $name ) : ?>
		<h3 class="addon-name"><?php echo wptexturize( $name ); ?> <?php if ( 1 == $required ) echo '<abbr class="required" title="required">*</abbr>'; ?></h3>
	<?php endif; ?>

	<?php if ( $description ) : ?>
		<?php echo '<div class="addon-description">' . wpautop( wptexturize( $description ) ) . '</div>'; ?>
	<?php endif; ?>

	<?php do_action( 'wc_product_addon_options', $addon ); ?>
