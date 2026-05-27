<?php
/**
 * Header — Marquee + Site Header + Hamburger
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="description" content="HariyaliBasket — Farm fresh vegetables and fruits delivered to your doorstep next day before 4 PM. Jaipur ki taazi sabzi.">
  <meta name="theme-color" content="#0d3320">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- ── MARQUEE ── -->
<div class="hb-marquee">
  <span class="hb-marquee-inner">
    🎁 Pehla Order: 1 Kg Aloo FREE! &nbsp;•&nbsp;
    🚚 ₹<?php echo intval( hb_get( 'hb_free_delivery_min', 199 ) ); ?>+ pe FREE Delivery &nbsp;•&nbsp;
    ⚡ Kal 4 PM tak Delivery &nbsp;•&nbsp;
    💵 COD + UPI Available &nbsp;•&nbsp;
    🌿 100% Farm Fresh Guarantee &nbsp;•&nbsp;
    📱 WhatsApp: +91 80003 44554 &nbsp;&nbsp;
  </span>
</div>

<!-- ── HEADER ── -->
<header class="hb-header">
  <a class="hb-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
    <div class="hb-logo-icon">🌿</div>
    <div class="hb-logo-text">
      <div class="hb-brand">Hariyali<em>Basket</em></div>
      <div class="hb-tagline">FARM TO DOORSTEP</div>
    </div>
  </a>
  <div class="hb-header-right">
    <div class="hb-header-badge">⚡ 4PM Delivery</div>
    <button class="hb-cart-btn" onclick="HBCheckout.open()" aria-label="Cart">
      🛒
      <span class="hb-cart-badge" id="hb-hdr-badge">0</span>
    </button>
    <button class="hb-menu-btn" onclick="HBNav.open()" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
