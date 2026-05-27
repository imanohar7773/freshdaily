<?php
/**
 * Main Template — composes all template-parts
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Inject products data as JSON for JS consumption
$products_data = hb_get_all_products();
$delivery_areas_str = hb_get( 'hb_delivery_areas', 'Hanging Garden,Vaishali Nagar,Malviya Nagar,Mansarovar' );
$delivery_areas = array_map( 'trim', explode( ',', $delivery_areas_str ) );

get_header();
?>

<script>
window.HB_PRODUCTS = <?php echo wp_json_encode( $products_data, JSON_UNESCAPED_UNICODE ); ?>;
window.HB_SOCIETIES = <?php echo wp_json_encode( $delivery_areas ); ?>;
</script>

<?php
get_template_part( 'template-parts/hero' );
get_template_part( 'template-parts/info-cards' );
get_template_part( 'template-parts/repeat-order' );
get_template_part( 'template-parts/countdown' );
get_template_part( 'template-parts/how-it-works' );
get_template_part( 'template-parts/trust-bar' );
get_template_part( 'template-parts/products' );
get_template_part( 'template-parts/features' );
get_template_part( 'template-parts/reviews' );
get_template_part( 'template-parts/pincode-check' );
get_template_part( 'template-parts/faq' );
get_template_part( 'template-parts/contact' );

// Always present
get_template_part( 'template-parts/cart-bar' );
get_template_part( 'template-parts/cart-drawer' );
get_template_part( 'template-parts/checkout' );
get_template_part( 'template-parts/success' );
get_template_part( 'template-parts/bottom-nav' );
get_template_part( 'template-parts/nav-drawer' );

// Hidden modal section sources (consumed by nav-drawer modal)
get_template_part( 'template-parts/about' );
get_template_part( 'template-parts/blog' );
get_template_part( 'template-parts/wishlist' );
get_template_part( 'template-parts/privacy' );

get_footer();
