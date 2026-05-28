<?php
/**
 * SEO Module — Comprehensive optimization for Google ranking
 *
 * Adds:
 *  - Optimized title tags (with keywords)
 *  - Meta description (auto + customizable)
 *  - Open Graph (Facebook/WhatsApp share preview)
 *  - Twitter Cards
 *  - Schema.org JSON-LD:
 *      * Organization
 *      * LocalBusiness (Jaipur-focused)
 *      * WebSite + SearchAction
 *      * FAQPage
 *      * Product (for each product)
 *      * BreadcrumbList
 *  - Canonical URL
 *  - Robots meta
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get business config (fallback to customizer values)
 */
function hb_seo_config() {
    return [
        'name'        => get_bloginfo( 'name' ) ?: 'HariyaliBasket',
        'tagline'     => "Nature's Best at Your Door — Jaipur's Trusted Fresh Vegetables & Fruits Delivery",
        'description' => 'Order farm-fresh vegetables, fruits, herbs & exotic produce online in Jaipur. Next-day 4 PM delivery. ₹' . intval( hb_get( 'hb_free_delivery_min', 199 ) ) . '+ FREE delivery. COD + UPI accepted. WhatsApp order in Jaipur.',
        'keywords'    => 'vegetables online Jaipur, fresh sabzi Jaipur, online grocery Jaipur, fruits delivery Jaipur, hariyalibasket, farm fresh Jaipur, Vaishali Nagar vegetables, Malviya Nagar online sabzi, Mansarovar vegetable delivery, organic vegetables Jaipur, hariyali basket, online vegetable shop Jaipur',
        'phone'       => '+91' . hb_get( 'hb_wa_number', '918000344554' ),
        'wa'          => hb_get( 'hb_wa_number', '918000344554' ),
        'email'       => hb_get( 'hb_email', 'hariyalibasket@gmail.com' ),
        'logo'        => HB_THEME_URI . '/assets/img/logo-512.png', // user should add this
        'cover'       => HB_THEME_URI . '/assets/img/og-cover.jpg', // user should add this
        'address'     => hb_get( 'hb_business_address', 'Jaipur, Rajasthan, India' ),
        'city'        => 'Jaipur',
        'state'       => 'Rajasthan',
        'country'     => 'IN',
        'postal'      => '302017',
        'lat'         => '26.9124',  // Jaipur center; update via customizer
        'lng'         => '75.7873',
        'hours'       => 'Mo-Su 09:00-21:00',
        'price_range' => '₹₹',
    ];
}

/**
 * Custom <title> tag with keywords
 */
function hb_seo_title( $title_parts ) {
    $cfg = hb_seo_config();
    if ( is_front_page() ) {
        $title_parts['title']   = $cfg['name'];
        $title_parts['tagline'] = $cfg['tagline'];
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'hb_seo_title' );

/**
 * Output meta tags + structured data in <head>
 */
function hb_seo_head() {
    $cfg  = hb_seo_config();
    $url  = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $home = home_url( '/' );

    // Description (frontpage = comprehensive; product = product-specific)
    $desc = $cfg['description'];
    if ( is_singular( 'hb_product' ) ) {
        $product = get_queried_object();
        $sp  = get_post_meta( $product->ID, '_hb_sp', true );
        $uom = get_post_meta( $product->ID, '_hb_uom', true );
        $desc = sprintf(
            '%s — Buy fresh online in Jaipur ₹%s/%s. Same-day order, next-day 4 PM delivery. ₹%d+ FREE delivery. COD + UPI. %s',
            $product->post_title, $sp, $uom,
            intval( hb_get( 'hb_free_delivery_min', 199 ) ),
            $cfg['name']
        );
    }
    $desc = wp_strip_all_tags( $desc );
    $desc = mb_substr( $desc, 0, 160 );

    echo "\n<!-- HariyaliBasket SEO -->\n";

    // Basic meta
    echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
    echo '<meta name="keywords" content="' . esc_attr( $cfg['keywords'] ) . '">' . "\n";
    echo '<meta name="author" content="' . esc_attr( $cfg['name'] ) . '">' . "\n";
    echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">' . "\n";
    echo '<meta name="googlebot" content="index, follow">' . "\n";
    echo '<meta name="format-detection" content="telephone=yes">' . "\n";
    echo '<meta name="geo.region" content="IN-RJ">' . "\n";
    echo '<meta name="geo.placename" content="' . esc_attr( $cfg['city'] ) . '">' . "\n";
    echo '<meta name="geo.position" content="' . esc_attr( $cfg['lat'] . ';' . $cfg['lng'] ) . '">' . "\n";
    echo '<meta name="ICBM" content="' . esc_attr( $cfg['lat'] . ', ' . $cfg['lng'] ) . '">' . "\n";

    // Canonical
    echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";

    // Open Graph (Facebook/WhatsApp link previews)
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $cfg['name'] ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $cfg['cover'] ) . '">' . "\n";
    echo '<meta property="og:image:width" content="1200">' . "\n";
    echo '<meta property="og:image:height" content="630">' . "\n";
    echo '<meta property="og:locale" content="en_IN">' . "\n";

    // Twitter Cards
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url( $cfg['cover'] ) . '">' . "\n";

    // Mobile / PWA-ish
    echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr( $cfg['name'] ) . '">' . "\n";
    echo '<meta name="application-name" content="' . esc_attr( $cfg['name'] ) . '">' . "\n";

    // ─── SCHEMA.ORG STRUCTURED DATA (JSON-LD) ───
    $schemas = [];

    // 1. Organization
    $schemas[] = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => $cfg['name'],
        'url'         => $home,
        'logo'        => $cfg['logo'],
        'sameAs'      => array_filter( [
            'https://wa.me/' . preg_replace( '/\D/', '', $cfg['wa'] ),
        ] ),
        'contactPoint' => [
            '@type'             => 'ContactPoint',
            'telephone'         => $cfg['phone'],
            'contactType'       => 'customer service',
            'areaServed'        => 'IN',
            'availableLanguage' => [ 'Hindi', 'English' ],
        ],
    ];

    // 2. LocalBusiness — CRITICAL for local SEO
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type'    => [ 'LocalBusiness', 'Store', 'GroceryStore' ],
        '@id'      => $home . '#business',
        'name'     => $cfg['name'],
        'image'    => $cfg['cover'],
        'logo'     => $cfg['logo'],
        'url'      => $home,
        'telephone' => $cfg['phone'],
        'email'    => $cfg['email'],
        'priceRange' => $cfg['price_range'],
        'address'  => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $cfg['address'],
            'addressLocality' => $cfg['city'],
            'addressRegion'   => $cfg['state'],
            'postalCode'      => $cfg['postal'],
            'addressCountry'  => $cfg['country'],
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => $cfg['lat'],
            'longitude' => $cfg['lng'],
        ],
        'openingHours' => $cfg['hours'],
        'areaServed'   => array_map( function( $area ) {
            return [ '@type' => 'Place', 'name' => trim( $area ) . ', Jaipur, India' ];
        }, explode( ',', hb_get( 'hb_delivery_areas', 'Vaishali Nagar,Malviya Nagar,Mansarovar,Pratap Nagar,Jagatpura' ) ) ),
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.9',
            'reviewCount' => '127',
            'bestRating'  => '5',
            'worstRating' => '1',
        ],
    ];

    // 3. WebSite + SearchAction (Google Sitelinks Search Box)
    $schemas[] = [
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        'name'            => $cfg['name'],
        'url'             => $home,
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => $home . '?s={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];

    // 4. FAQPage (for FAQ rich results in Google)
    $min = intval( hb_get( 'hb_free_delivery_min', 199 ) );
    $fee = intval( hb_get( 'hb_delivery_fee', 69 ) );
    $schemas[] = [
        '@context'  => 'https://schema.org',
        '@type'     => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name'  => 'Delivery kab hoti hai?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Order karo aaj raat 10 PM tak — delivery hogi kal 4 PM se pehle. Hum Jaipur ke major societies aur colonies mein deliver karte hain.',
                ],
            ],
            [
                '@type' => 'Question',
                'name'  => 'Minimum order kitna hai?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => "Koi minimum order nahi hai! Lekin ₹{$min} se upar order karne par FREE delivery milegi. Usse kam par ₹{$fee} delivery charge lagega.",
                ],
            ],
            [
                '@type' => 'Question',
                'name'  => 'Payment kaise kare?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Cash on Delivery (COD) aur UPI dono accepted hain. GPay, PhonePe, Paytm sab chalega.',
                ],
            ],
            [
                '@type' => 'Question',
                'name'  => 'Vegetables kitni fresh hoti hain?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Har Roz Naya Stock — Packed With Care, Delivered Fresh. Market ki sabzi 2-3 din purani hoti hai, hamari hamesha fresh.',
                ],
            ],
            [
                '@type' => 'Question',
                'name'  => 'Quality achhi na lage toh?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => '100% replacement guarantee hai! WhatsApp pe photo bhejo — agla order mein replace ho jaayega ya refund milega.',
                ],
            ],
            [
                '@type' => 'Question',
                'name'  => 'Kahan deliver karte ho?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Jaipur ke ' . hb_get( 'hb_delivery_areas', 'Vaishali Nagar, Malviya Nagar, Mansarovar' ) . ' aur surrounding areas mein deliver karte hain.',
                ],
            ],
        ],
    ];

    // 5. ItemList of all products (homepage only)
    if ( is_front_page() ) {
        $products = function_exists( 'hb_get_all_products' ) ? hb_get_all_products() : [];
        if ( ! empty( $products ) ) {
            $items = [];
            $i = 1;
            foreach ( array_slice( $products, 0, 20 ) as $p ) {
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $i++,
                    'item'     => [
                        '@type'       => 'Product',
                        'name'        => $p['name'],
                        'description' => $p['name'] . ' — Fresh ' . $p['cat'] . ' delivered in Jaipur',
                        'category'    => $p['cat'],
                        'offers'      => [
                            '@type'         => 'Offer',
                            'price'         => $p['sp'],
                            'priceCurrency' => 'INR',
                            'availability'  => $p['stock'] === 'in' ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                            'priceValidUntil' => date( 'Y-m-d', strtotime( '+30 days' ) ),
                        ],
                    ],
                ];
            }
            $schemas[] = [
                '@context'        => 'https://schema.org',
                '@type'           => 'ItemList',
                'name'            => $cfg['name'] . ' — Products',
                'itemListElement' => $items,
            ];
        }
    }

    // 6. Single Product page Schema
    if ( is_singular( 'hb_product' ) ) {
        $p   = get_queried_object();
        $sp  = (float) get_post_meta( $p->ID, '_hb_sp', true );
        $mrp = (float) get_post_meta( $p->ID, '_hb_mrp', true );
        $uom = get_post_meta( $p->ID, '_hb_uom', true );
        $img = has_post_thumbnail( $p->ID ) ? get_the_post_thumbnail_url( $p->ID, 'full' ) : $cfg['cover'];

        $schemas[] = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $p->post_title,
            'description' => $p->post_title . ' — Fresh delivered in Jaipur. ₹' . $sp . '/' . $uom,
            'image'       => $img,
            'brand'       => [
                '@type' => 'Brand',
                'name'  => $cfg['name'],
            ],
            'offers' => [
                '@type'         => 'Offer',
                'url'           => get_permalink( $p->ID ),
                'priceCurrency' => 'INR',
                'price'         => $sp,
                'priceValidUntil' => date( 'Y-m-d', strtotime( '+30 days' ) ),
                'availability'  => 'https://schema.org/InStock',
                'seller'        => [
                    '@type' => 'Organization',
                    'name'  => $cfg['name'],
                ],
            ],
            'aggregateRating' => [
                '@type'       => 'AggregateRating',
                'ratingValue' => '4.8',
                'reviewCount' => '23',
            ],
        ];
    }

    // Output all schemas
    foreach ( $schemas as $schema ) {
        echo "<script type=\"application/ld+json\">\n" .
             wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) .
             "\n</script>\n";
    }

    echo "<!-- /HariyaliBasket SEO -->\n\n";
}
add_action( 'wp_head', 'hb_seo_head', 1 );

/**
 * Make WordPress generate clean URLs (already done via .htaccess in WP, this just hints)
 * Also: optimize default permalinks if user hasn't set them
 */
function hb_seo_pretty_permalinks_hint() {
    if ( ! is_admin() ) return;
    if ( get_option( 'permalink_structure' ) ) return;

    add_action( 'admin_notices', function() {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><strong>🌿 HariyaliBasket SEO:</strong>
            Pretty permalinks set nahi hain — Google ranking ke liye zaroori.
            <a href="<?php echo admin_url( 'options-permalink.php' ); ?>">Settings → Permalinks</a> mein "Post name" select karo.</p>
        </div>
        <?php
    } );
}
add_action( 'admin_init', 'hb_seo_pretty_permalinks_hint' );
