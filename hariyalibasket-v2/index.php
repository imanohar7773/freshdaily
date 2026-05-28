<?php
/**
 * Main Template — Shop-First v2 layout (pincode top, products early).
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
// ── 1. PINCODE TOP (most important: customer ka pehla sawaal — "delivery hai?") ──
get_template_part( 'template-parts/pincode-top' );

// ── 2. REPEAT ORDER (returning customer fast-track) ──
get_template_part( 'template-parts/repeat-order' );

// ── 3. PRODUCTS (the main event — visible in 2 scrolls) ──
get_template_part( 'template-parts/products' );

// ── 4. TRUST BAR (social proof after seeing products) ──
get_template_part( 'template-parts/trust-bar' );

// ── 5. COMPACT HERO (1-line tagline, no duplicate brand title) ──
get_template_part( 'template-parts/hero' );

// ── 6. INFO CARDS (4 quick-info pills) ──
get_template_part( 'template-parts/info-cards' );

// ── 7. FEATURES (Khaasiyat — 8 trust points) ──
get_template_part( 'template-parts/features' );

// ── 8. REVIEWS (testimonials) ──
get_template_part( 'template-parts/reviews' );

// ── 9. COUNTDOWN (urgency makes sense after value seen) ──
get_template_part( 'template-parts/countdown' );

// ── 10. HOW IT WORKS (for new/confused users — lower priority) ──
get_template_part( 'template-parts/how-it-works' );

// ── 11. FAQ ──
get_template_part( 'template-parts/faq' );

// ── 12. CONTACT ──
get_template_part( 'template-parts/contact' );

// ── Floating UI (always present) ──
get_template_part( 'template-parts/cart-bar' );
get_template_part( 'template-parts/cart-drawer' );
get_template_part( 'template-parts/checkout' );
get_template_part( 'template-parts/success' );
get_template_part( 'template-parts/bottom-nav' );
get_template_part( 'template-parts/nav-drawer' );

// ── Hidden modal section sources (consumed by nav-drawer modal) ──
get_template_part( 'template-parts/about' );
get_template_part( 'template-parts/blog' );
get_template_part( 'template-parts/wishlist' );
get_template_part( 'template-parts/privacy' );

get_footer();
