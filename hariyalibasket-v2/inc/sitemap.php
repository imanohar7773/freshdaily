<?php
/**
 * Dynamic XML Sitemap — for Google indexing
 * Accessible at: /sitemap.xml
 *
 * Why dynamic: products auto-included as soon as published.
 * Google reads this to discover all your URLs.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register /sitemap.xml endpoint
 */
function hb_sitemap_init() {
    add_rewrite_rule( '^sitemap\.xml$', 'index.php?hb_sitemap=1', 'top' );
    add_rewrite_tag( '%hb_sitemap%', '([^&]+)' );
}
add_action( 'init', 'hb_sitemap_init' );

/**
 * Render XML sitemap
 */
function hb_sitemap_render() {
    if ( ! get_query_var( 'hb_sitemap' ) ) return;

    header( 'Content-Type: application/xml; charset=UTF-8' );
    header( 'X-Robots-Tag: noindex' ); // sitemap itself shouldn't be indexed

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

    // Homepage
    $home = home_url( '/' );
    echo "  <url>\n";
    echo '    <loc>' . esc_url( $home ) . '</loc>' . "\n";
    echo '    <lastmod>' . date( 'Y-m-d' ) . '</lastmod>' . "\n";
    echo "    <changefreq>daily</changefreq>\n";
    echo "    <priority>1.0</priority>\n";
    echo "  </url>\n";

    // All products
    $products = get_posts( [
        'post_type'      => 'hb_product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ] );
    foreach ( $products as $p ) {
        $url = get_permalink( $p->ID );
        if ( ! $url ) continue;
        echo "  <url>\n";
        echo '    <loc>' . esc_url( $url ) . '</loc>' . "\n";
        echo '    <lastmod>' . get_the_modified_date( 'Y-m-d', $p->ID ) . '</lastmod>' . "\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.8</priority>\n";
        if ( has_post_thumbnail( $p->ID ) ) {
            echo "    <image:image>\n";
            echo '      <image:loc>' . esc_url( get_the_post_thumbnail_url( $p->ID, 'full' ) ) . '</image:loc>' . "\n";
            echo '      <image:title>' . esc_html( $p->post_title ) . '</image:title>' . "\n";
            echo "    </image:image>\n";
        }
        echo "  </url>\n";
    }

    // Standard pages
    $pages = get_posts( [
        'post_type'      => 'page',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ] );
    foreach ( $pages as $p ) {
        echo "  <url>\n";
        echo '    <loc>' . esc_url( get_permalink( $p->ID ) ) . '</loc>' . "\n";
        echo '    <lastmod>' . get_the_modified_date( 'Y-m-d', $p->ID ) . '</lastmod>' . "\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.5</priority>\n";
        echo "  </url>\n";
    }

    // Standard blog posts
    $posts = get_posts( [ 'posts_per_page' => 50, 'post_status' => 'publish' ] );
    foreach ( $posts as $p ) {
        echo "  <url>\n";
        echo '    <loc>' . esc_url( get_permalink( $p->ID ) ) . '</loc>' . "\n";
        echo '    <lastmod>' . get_the_modified_date( 'Y-m-d', $p->ID ) . '</lastmod>' . "\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.6</priority>\n";
        echo "  </url>\n";
    }

    echo '</urlset>';
    exit;
}
add_action( 'template_redirect', 'hb_sitemap_render' );

/**
 * Add sitemap reference to robots.txt
 */
function hb_robots_txt( $output ) {
    $home = home_url( '/' );
    $output .= "\n# HariyaliBasket SEO\n";
    $output .= "Sitemap: {$home}sitemap.xml\n";
    return $output;
}
add_filter( 'robots_txt', 'hb_robots_txt' );

/**
 * Flush rewrite rules on activation (admin notice if needed)
 */
function hb_sitemap_flush_notice() {
    if ( ! is_admin() ) return;
    $rules = get_option( 'rewrite_rules' );
    if ( ! is_array( $rules ) || ! isset( $rules['^sitemap\.xml$'] ) ) {
        add_action( 'admin_notices', function() {
            ?>
            <div class="notice notice-info is-dismissible">
                <p><strong>🌿 HariyaliBasket:</strong>
                Sitemap activate karne ke liye:
                <a href="<?php echo admin_url( 'options-permalink.php' ); ?>">Settings → Permalinks</a> par jaake "Save Changes" dabao.
                Phir <a href="<?php echo home_url( '/sitemap.xml' ); ?>" target="_blank">/sitemap.xml</a> chalu ho jayega.</p>
            </div>
            <?php
        } );
    }
}
add_action( 'admin_init', 'hb_sitemap_flush_notice' );
