<?php
/**
 * Plugin Name: KD Dark Light Toggle
 * Description: Dark mode and light mode switch for the Kadence theme. Flip between your Kadence Global Palettes with one click, with optional auto dark mode that follows the visitor's device.
 * Version: 2.2.0
 * Author: ASJ
 * Requires at least: 5.7
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kd-dark-light-toggle
 *
 * Copyright (c) ASJ.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version. It is distributed WITHOUT ANY WARRANTY. See the LICENSE file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const KD_OPTION = 'kd_settings';

/* Front-end CSS (minified). The switch has no hover effects. */
define( 'KD_CSS', <<<'CSS'
.kd-dl{--w:44px;--h:24px;--k:18px;--p:3px;position:relative;display:inline-block;flex:none;box-sizing:border-box;width:var(--w);height:var(--h);border-radius:999px;background:#e2e8f0;box-shadow:inset 0 0 0 1px rgba(15,23,42,.14);cursor:pointer;vertical-align:middle;-webkit-tap-highlight-color:transparent;transition:background .3s}
.kd-dl:after{content:"";position:absolute;top:-10px;bottom:-10px;left:-6px;right:-6px}
.kd-dl:focus-visible{outline:2px solid #2563eb;outline-offset:3px}
.kd-m{--w:52px;--h:28px;--k:22px}
.kd-l{--w:60px;--h:32px;--k:26px}
.kd-k{position:absolute;top:var(--p);left:var(--p);width:var(--k);height:var(--k);border-radius:50%;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.28);transition:transform .3s cubic-bezier(.4,0,.2,1)}
.kd-i{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;transition:opacity .25s}
.kd-i svg{width:60%;height:60%;display:block}
.kd-su{color:#f59e0b}
.kd-mo{color:#4f46e5;opacity:0}
[data-kd=b] .kd-dl{background:#0f172a;box-shadow:inset 0 0 0 1px rgba(255,255,255,.14)}
[data-kd=b] .kd-k{transform:translateX(calc(var(--w) - var(--k) - var(--p) * 2))}
[data-kd=b] .kd-su{opacity:0}
[data-kd=b] .kd-mo{opacity:1}
.kd-f{position:fixed;z-index:99999;box-shadow:0 2px 10px rgba(0,0,0,.2),inset 0 0 0 1px rgba(15,23,42,.14)}
[data-kd=b] .kd-f{box-shadow:0 2px 10px rgba(0,0,0,.4),inset 0 0 0 1px rgba(255,255,255,.14)}
.kd-bottom-right{bottom:calc(16px + env(safe-area-inset-bottom,0px));right:calc(16px + env(safe-area-inset-right,0px))}
.kd-bottom-left{bottom:calc(16px + env(safe-area-inset-bottom,0px));left:calc(16px + env(safe-area-inset-left,0px))}
.kd-top-right{top:calc(16px + env(safe-area-inset-top,0px));right:calc(16px + env(safe-area-inset-right,0px))}
.kd-top-left{top:calc(16px + env(safe-area-inset-top,0px));left:calc(16px + env(safe-area-inset-left,0px))}
@media(prefers-reduced-motion:reduce){.kd-dl,.kd-k,.kd-i{transition:none}}
CSS
);

/* Front-end JS (minified). The choice lives only in the visitor's browser (localStorage): no cookies, nothing sent to the server. %d = auto dark on/off. */
define( 'KD_JS', <<<'JS'
(function(d,w){var h=d.documentElement,A=%d;function g(){try{var v=w.localStorage.getItem('kd-mode');return v==='a'||v==='b'?v:null}catch(z){return null}}function s(v){h.setAttribute('data-kd',v);var n=d.querySelectorAll('.kd-dl');for(var i=0;i<n.length;i++)n[i].setAttribute('aria-checked',v==='b'?'true':'false')}function e(){return g()||(A&&w.matchMedia&&w.matchMedia('(prefers-color-scheme:dark)').matches?'b':'a')}function f(){var v=h.getAttribute('data-kd')==='b'?'a':'b';try{w.localStorage.setItem('kd-mode',v)}catch(z){}s(v)}s(e());d.addEventListener('DOMContentLoaded',function(){s(e())});d.addEventListener('click',function(x){if(x.target.closest&&x.target.closest('.kd-dl'))f()});d.addEventListener('keydown',function(x){if((x.key==='Enter'||x.key===' ')&&x.target.classList&&x.target.classList.contains('kd-dl')){x.preventDefault();f()}})})(document,window);
JS
);

/* -------------------------------------------------------------------------
 * Settings (always validated when read, so a bad value can never reach output)
 * ---------------------------------------------------------------------- */

function kd_keys() {
	return array( 1 => 'palette', 2 => 'second-palette', 3 => 'third-palette' ); // Global Palette keys
}

function kd_defaults() {
	return array(
		'palette_a'     => '1',           // light mode
		'palette_b'     => '2',           // dark mode
		'auto_dark'     => 1,
		'show_floating' => 1,
		'position'      => 'bottom-right',
		'icon_size'     => 'small',
		'visibility'    => 'all',         // all | only | except
		'vis_pages'     => array(),
		'hide_paths'    => array(),
	);
}

function kd_pick( $v, $allowed, $default ) {
	return ( is_scalar( $v ) && in_array( (string) $v, $allowed, true ) ) ? (string) $v : $default;
}

function kd_clean( $in ) {
	$in    = wp_parse_args( is_array( $in ) ? $in : array(), kd_defaults() );
	$paths = $in['hide_paths'];
	if ( is_string( $paths ) ) {
		$paths = explode( "\n", sanitize_textarea_field( $paths ) );
	}
	$paths = array_map( function ( $line ) {
		return is_scalar( $line ) ? substr( sanitize_text_field( (string) $line ), 0, 200 ) : '';
	}, (array) $paths );
	$pages = array_map( 'intval', array_filter( (array) $in['vis_pages'], 'is_scalar' ) );

	return array(
		'palette_a'     => kd_pick( $in['palette_a'], array( '1', '2', '3' ), '1' ),
		'palette_b'     => kd_pick( $in['palette_b'], array( '1', '2', '3' ), '2' ),
		'auto_dark'     => empty( $in['auto_dark'] ) ? 0 : 1,
		'show_floating' => empty( $in['show_floating'] ) ? 0 : 1,
		'position'      => kd_pick( $in['position'], array( 'bottom-right', 'bottom-left', 'top-right', 'top-left' ), 'bottom-right' ),
		'icon_size'     => kd_pick( $in['icon_size'], array( 'small', 'medium', 'large' ), 'small' ),
		'visibility'    => kd_pick( $in['visibility'], array( 'all', 'only', 'except' ), 'all' ),
		'vis_pages'     => array_slice( array_values( array_unique( $pages ) ), 0, 300 ),
		'hide_paths'    => array_slice( array_values( array_filter( $paths ) ), 0, 100 ),
	);
}

function kd_settings() {
	static $s = null;
	if ( null === $s ) {
		$saved = get_option( KD_OPTION, array() );
		$s = kd_clean( is_array( $saved ) ? $saved : array() );
	}
	return $s;
}

/* -------------------------------------------------------------------------
 * Global Palette data
 * ---------------------------------------------------------------------- */

function kd_raw() {
	static $raw = null;
	if ( null === $raw ) {
		$v = get_theme_mod( 'kadence_global_palette' );
		if ( empty( $v ) ) {
			$v = get_option( 'kadence_global_palette' );
		}
		$v   = is_string( $v ) ? json_decode( $v, true ) : $v;
		$raw = is_array( $v ) ? $v : array();
	}
	return $raw;
}

/* Up to 9 colors of one palette, number => color. Only plain CSS colors pass, so nothing can break out of the <style> block. */
function kd_colors( $list ) {
	$out = array();
	if ( is_array( $list ) ) {
		foreach ( array_slice( array_values( $list ), 0, 9 ) as $i => $item ) {
			$c = ( is_array( $item ) && isset( $item['color'] ) && is_string( $item['color'] ) ) ? trim( $item['color'] ) : '';
			if ( preg_match( '/^(#[0-9a-f]{3,8}|(?:rgb|hsl)a?\([0-9.,%\s\/-]+\)|var\(--[\w-]+\)|[a-z]+)$/iD', $c ) ) {
				$out[ $i + 1 ] = $c;
			}
		}
	}
	return $out;
}

function kd_vars( $list ) {
	$css = '';
	foreach ( kd_colors( $list ) as $n => $c ) {
		$css .= '--global-palette' . $n . ':' . $c . ';';
	}
	return $css;
}

/* array( light CSS vars, dark CSS vars ), or null when the setup isn't usable */
function kd_palettes() {
	static $p = false;
	if ( false === $p ) {
		$p = null;
		$s = kd_settings();
		if ( $s['palette_a'] !== $s['palette_b'] ) {
			$k = kd_keys();
			$r = kd_raw();
			$a = kd_vars( isset( $r[ $k[ (int) $s['palette_a'] ] ] ) ? $r[ $k[ (int) $s['palette_a'] ] ] : null );
			$b = kd_vars( isset( $r[ $k[ (int) $s['palette_b'] ] ] ) ? $r[ $k[ (int) $s['palette_b'] ] ] : null );
			if ( $a && $b ) {
				$p = array( $a, $b );
			}
		}
	}
	return $p;
}

/* -------------------------------------------------------------------------
 * Front end. Everything is identical for every visitor (no cookies, no
 * per-user output), so full-page caching is safe.
 * ---------------------------------------------------------------------- */

add_action( 'wp_head', function () {
	$p = kd_palettes();
	if ( ! $p || is_customize_preview() ) {
		return;
	}
	$s = kd_settings();
	echo '<style id="kd-css">:root{' . $p[0] . '}:root[data-kd=b]{' . $p[1] . '}' . KD_CSS . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- validated colors + constant
	wp_print_inline_script_tag( sprintf( KD_JS, (int) $s['auto_dark'] ), array( 'id' => 'kd-js' ) );
}, 999 );

function kd_button( $floating = false ) {
	if ( ! kd_palettes() ) {
		return '';
	}
	$s     = kd_settings();
	$class = 'kd-dl' . ( 'medium' === $s['icon_size'] ? ' kd-m' : ( 'large' === $s['icon_size'] ? ' kd-l' : '' ) );
	if ( $floating ) {
		$class .= ' kd-f kd-' . $s['position'];
	}
	$svg = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">';
	$sun = $svg . '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>';
	$moon = $svg . '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>';

	return '<span class="' . esc_attr( $class ) . '" role="switch" tabindex="0" aria-checked="false" aria-label="' . esc_attr__( 'Dark mode', 'kd-dark-light-toggle' ) . '"><span class="kd-k"><span class="kd-i kd-su">' . $sun . '</span><span class="kd-i kd-mo">' . $moon . '</span></span></span>';
}

add_shortcode( 'kd_toggle', function () {
	return kd_button();
} );

class KD_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct( 'kd_widget', __( 'KD Dark / Light Toggle', 'kd-dark-light-toggle' ), array( 'description' => __( 'Dark / Light switch for your site palettes.', 'kd-dark-light-toggle' ) ) );
	}
	public function widget( $args, $instance ) {
		$b = kd_button();
		if ( ! $b ) {
			return;
		}
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput -- theme-provided markup
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $b . $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		echo '<p><label>' . esc_html__( 'Title:', 'kd-dark-light-toggle' ) . '<input class="widefat" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '"></label></p>';
	}
	public function update( $new, $old ) {
		return array( 'title' => sanitize_text_field( isset( $new['title'] ) ? $new['title'] : '' ) );
	}
}
add_action( 'widgets_init', function () {
	register_widget( 'KD_Widget' );
} );

/* ID of the single page/post being viewed (0 on archives, so a category ID can never be mistaken for a page ID) */
function kd_current_id() {
	return ( is_singular() || ( is_home() && ! is_front_page() ) ) ? (int) get_queried_object_id() : 0;
}

function kd_is_hidden( $list ) {
	if ( ! $list ) {
		return false;
	}
	global $wp;
	$id   = kd_current_id();
	$slug = $id ? (string) get_post_field( 'post_name', $id ) : '';
	$req  = trim( strtolower( (string) $wp->request ), '/' );
	$path = '' === $req ? '/' : '/' . $req . '/';

	foreach ( $list as $e ) {
		if ( ctype_digit( $e ) ) {                            // post ID
			if ( $id && (int) $e === $id ) {
				return true;
			}
		} elseif ( '/' === $e[0] ) {                          // URL path (and everything under it)
			$n = trailingslashit( strtolower( $e ) );
			if ( '/' === $n ? '/' === $path : 0 === strpos( $path, $n ) ) {
				return true;
			}
		} elseif ( $slug && sanitize_title( $e ) === $slug ) { // slug
			return true;
		}
	}
	return false;
}

function kd_show_here() {
	$s = kd_settings();
	if ( ! $s['show_floating'] || is_search() || is_404() || kd_is_hidden( $s['hide_paths'] ) ) {
		return false;
	}
	if ( 'all' === $s['visibility'] ) {
		return true;
	}
	$id    = kd_current_id();
	$match = ( is_front_page() && in_array( 0, $s['vis_pages'], true ) ) || ( $id && in_array( $id, $s['vis_pages'], true ) );
	return 'only' === $s['visibility'] ? $match : ! $match;
}

add_action( 'wp_footer', function () {
	if ( kd_palettes() && kd_show_here() ) {
		echo kd_button( true ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside kd_button()
	}
} );

/* -------------------------------------------------------------------------
 * Settings page: Settings → KD Dark / Light
 * ---------------------------------------------------------------------- */

register_uninstall_hook( __FILE__, 'kd_uninstall' );
function kd_uninstall() {
	delete_option( KD_OPTION );
}

add_action( 'admin_menu', function () {
	add_options_page( 'KD Dark / Light', 'KD Dark / Light', 'manage_options', 'kd-dark-light-toggle', 'kd_settings_page' );
} );

add_action( 'admin_init', function () {
	register_setting( 'kd_group', KD_OPTION, array(
		'type'              => 'array',
		'default'           => kd_defaults(),
		'show_in_rest'      => false,
		'sanitize_callback' => function ( $in ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return kd_clean( get_option( KD_OPTION, array() ) );
			}
			$in = is_array( $in ) ? $in : array();
			foreach ( array( 'auto_dark', 'show_floating' ) as $k ) {
				$in[ $k ] = empty( $in[ $k ] ) ? 0 : 1; // unchecked boxes are simply absent from the form
			}
			$in['vis_pages'] = isset( $in['vis_pages'] ) ? $in['vis_pages'] : array();
			return kd_clean( $in );
		},
	) );
} );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=kd-dark-light-toggle' ) ) . '">Settings</a>' );
	return $links;
} );

function kd_swatches( $list ) {
	$out = '';
	foreach ( kd_colors( $list ) as $c ) {
		$out .= '<span style="display:inline-block;width:20px;height:20px;border-radius:4px;margin-right:3px;border:1px solid #ccd0d4;background:' . esc_attr( $c ) . '"></span>';
	}
	return $out ? $out : '<em>not found</em>';
}

function kd_choices() {
	$args  = array( 'post_status' => 'publish', 'numberposts' => 200, 'update_post_meta_cache' => false, 'update_post_term_cache' => false );
	$items = array( 0 => '— Front page —' );
	foreach ( get_posts( $args + array( 'post_type' => 'page', 'orderby' => 'title', 'order' => 'ASC' ) ) as $p ) {
		$items[ $p->ID ] = 'Page: ' . ( '' !== $p->post_title ? $p->post_title : '(no title)' );
	}
	foreach ( get_posts( $args + array( 'post_type' => 'post', 'orderby' => 'date', 'order' => 'DESC' ) ) as $p ) {
		$items[ $p->ID ] = 'Post: ' . ( '' !== $p->post_title ? $p->post_title : '(no title)' );
	}
	return $items;
}

function kd_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s = kd_settings();
	$r = kd_raw();
	$k = kd_keys();
	$n = KD_OPTION;
	?>
	<div class="wrap">
		<h1>KD Dark / Light Toggle</h1>
		<?php if ( ! kd_palettes() ) : ?>
			<div class="notice notice-warning"><p>
				<?php echo $s['palette_a'] === $s['palette_b'] ? 'Choose two different palettes below.' : 'The switch is off because the chosen palettes were not found. Save your Global Palette once in the Customizer (Colors &amp; Fonts → Colors) and make sure your theme provides it.'; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</p></div>
		<?php endif; ?>
		<p>Pick which Global Palette is your <strong>light</strong> look and which is your <strong>dark</strong> look. Edit the colors in Customizer → Colors &amp; Fonts → Colors → Global Palette. A visitor's choice is stored only in their own browser (no cookies).</p>

		<table class="widefat striped" style="max-width:520px">
			<?php foreach ( $k as $i => $key ) : ?>
				<tr><td><strong>Palette <?php echo (int) $i; ?></strong></td><td><?php echo kd_swatches( isset( $r[ $key ] ) ? $r[ $key ] : null ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></td></tr>
			<?php endforeach; ?>
		</table>

		<form method="post" action="options.php">
			<?php settings_fields( 'kd_group' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( array( 'palette_a' => 'Light mode uses', 'palette_b' => 'Dark mode uses' ) as $f => $label ) : ?>
				<tr>
					<th scope="row"><?php echo esc_html( $label ); ?></th>
					<td><select name="<?php echo esc_attr( $n . '[' . $f . ']' ); ?>">
						<?php foreach ( $k as $i => $key ) : ?>
							<option value="<?php echo (int) $i; ?>" <?php selected( $s[ $f ], (string) $i ); ?>>Palette <?php echo (int) $i; ?></option>
						<?php endforeach; ?>
					</select></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<th scope="row">Auto dark mode</th>
					<td>
						<label><input type="checkbox" name="<?php echo esc_attr( $n ); ?>[auto_dark]" value="1" <?php checked( $s['auto_dark'], 1 ); ?>> ON: follow the visitor's device (dark device → dark palette)</label>
						<p class="description">Only until the visitor uses the switch; then their own choice is kept. OFF = always start in light mode.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Switch size</th>
					<td><select name="<?php echo esc_attr( $n ); ?>[icon_size]">
						<?php foreach ( array( 'small' => 'Small', 'medium' => 'Medium', 'large' => 'Large' ) as $v => $l ) : ?>
							<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $s['icon_size'], $v ); ?>><?php echo esc_html( $l ); ?></option>
						<?php endforeach; ?>
					</select></td>
				</tr>
				<tr>
					<th scope="row">Floating switch</th>
					<td>
						<label><input type="checkbox" name="<?php echo esc_attr( $n ); ?>[show_floating]" value="1" <?php checked( $s['show_floating'], 1 ); ?>> Show on pages</label>
						&nbsp;
						<select name="<?php echo esc_attr( $n ); ?>[position]">
							<?php foreach ( array( 'bottom-right' => 'Bottom right', 'bottom-left' => 'Bottom left', 'top-right' => 'Top right', 'top-left' => 'Top left' ) as $v => $l ) : ?>
								<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $s['position'], $v ); ?>><?php echo esc_html( $l ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">Show floating switch on</th>
					<td>
						<p>
							<?php foreach ( array( 'all' => 'Every page (default)', 'only' => 'Only the pages/posts checked below', 'except' => 'Every page except the ones checked below' ) as $v => $l ) : ?>
								<label><input type="radio" name="<?php echo esc_attr( $n ); ?>[visibility]" value="<?php echo esc_attr( $v ); ?>" <?php checked( $s['visibility'], $v ); ?>> <?php echo esc_html( $l ); ?></label><br>
							<?php endforeach; ?>
						</p>
						<div style="max-width:420px;max-height:220px;overflow:auto;border:1px solid #dcdcde;padding:10px;background:#fff">
							<?php foreach ( kd_choices() as $id => $label ) : ?>
								<label style="display:block;margin-bottom:4px"><input type="checkbox" name="<?php echo esc_attr( $n ); ?>[vis_pages][]" value="<?php echo (int) $id; ?>" <?php checked( in_array( (int) $id, $s['vis_pages'], true ) ); ?>> <?php echo esc_html( $label ); ?></label>
							<?php endforeach; ?>
						</div>
						<p class="description">Controls the floating switch only. The shortcode and widget show wherever you place them.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Always hide on</th>
					<td>
						<textarea name="<?php echo esc_attr( $n ); ?>[hide_paths]" rows="4" class="large-text code" placeholder="42&#10;special-offer&#10;/checkout/"><?php echo esc_textarea( implode( "\n", $s['hide_paths'] ) ); ?></textarea>
						<p class="description">One per line; overrides everything above. A <strong>page/post ID</strong> (<code>42</code>), a <strong>slug</strong> (<code>special-offer</code>) or a <strong>URL path</strong> that also covers everything under it (<code>/checkout/</code>; <code>/</code> = home page only). Search and 404 pages are always hidden. URL paths need pretty permalinks.</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>

		<hr>
		<p><strong>Other places:</strong> shortcode <code>[kd_toggle]</code> (works in any page, HTML element or Shortcode block), or Appearance → Widgets → <em>KD Dark / Light Toggle</em>.</p>
	</div>
	<?php
}
