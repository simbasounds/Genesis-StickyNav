<?php
/*
Plugin Name: Genesis Sticky Nav
Description: Stick your nav to the top on scroll 
Author: Simon Barnett
Version: 1.0.1
Author URI: http://simonbarnett.co.za
*/

function genesis_sticky_nav_enqueue_script() {
	wp_enqueue_script('jquery');
}

function genesis_sticky_nav_footer() {
	$sel = get_option('genesis_sticky_nav');
	$max = get_option('genesis_sticky_nav_max');
	$topoffset = get_option('genesis_sticky_nav_topoffset');
	if (is_admin_bar_showing()) $topoffset = $topoffset + 32;
	if ( !isset($sel) || !isset($max) || !isset($topoffset)) return;
	if ( $sel == '' || $max == '' || $topoffset == '') return;

	echo <<<EOT
	<script>
		jQuery(document).ready(function($) {
			var winwidth = $(window).width();
			if (winwidth >= $max) {
				var style = document.createElement('style');
				style.type = 'text/css';
				style.innerHTML = '@media screen and (min-width: ' + $max + 'px) { $sel.fix { position: fixed; top: ' + $topoffset + 'px; left: 0; z-index: 1000; width: 100%; max-width: 100%; overflow: visible; } } '

				document.getElementsByTagName('head')[0].appendChild(style);
				var stknav = $('$sel');
				var stknavSpacer = $('<div />', {
					"class": "filter-drop-spacer",
					"height": stknav.outerHeight()
				});
			
				if (stknav.size()) {
					$(window).scroll(function () {
						if (!stknav.hasClass('fix') && $(window).scrollTop() > stknav.offset().top - $topoffset) {
							stknav.before(stknavSpacer);
							stknav.addClass("fix");
						} else if (stknav.hasClass('fix')  && $(window).scrollTop() < stknavSpacer.offset().top - $topoffset) {
							stknav.removeClass("fix");
							stknavSpacer.remove();
						}
					});
				}
			};
		});
	</script>
EOT;
}

if (!is_admin()):
	add_action('wp_enqueue_scripts', 'genesis_sticky_nav_enqueue_script');
	add_action('wp_footer', 'genesis_sticky_nav_footer', 1);
else:
	add_action('admin_menu', 'genesis_sticky_nav_create_menu');

	function genesis_sticky_nav_create_menu() {
		add_options_page('Genesis Sticky Nav Settings', 'Genesis Sticky Nav', 'administrator', __FILE__, 'genesis_sticky_nav_settings_page',plugins_url('/icon.png', __FILE__));
		add_action( 'admin_init', 'register_genesis_sticky_nav_settings' );
	}

	function register_genesis_sticky_nav_settings() {
		register_setting( 'genesis-sticky-nav-settings-group', 'genesis_sticky_nav', 'validate_genesis_sticky_nav_selector' );
		register_setting( 'genesis-sticky-nav-settings-group', 'genesis_sticky_nav_max', 'validate_genesis_sticky_nav_number' );
		register_setting( 'genesis-sticky-nav-settings-group', 'genesis_sticky_nav_topoffset', 'validate_genesis_sticky_nav_number' );
	}

	function validate_genesis_sticky_nav_selector($input) {
		if ($input == '') return $input;
		if (!preg_match("/^[#\w\s\.\-\[\]\=\^\~\:]+$/", $input)) return '#invalid-selector';
		return $input;
	}

	function validate_genesis_sticky_nav_number($input) {
		if ($input == '') return $input;
		if (!is_numeric($input)) return '0';
		return $input;
	}

	function genesis_sticky_nav_settings_page() { ?>
		<div class="wrap">
			<h2 style="float: left;">Genesis Sticky Nav</h2>
			<form method="post" action="options.php">
			    <?php settings_fields( 'genesis-sticky-nav-settings-group' ); ?>
			    <?php //do_settings_fields( 'genesis-sticky-nav-settings-group' ); ?>
			    <table class="form-table">
			        <tr valign="top">
			        <th scope="row">Genesis nav class</th>
			        <td><input type="text" name="genesis_sticky_nav" value="<?php echo get_option('genesis_sticky_nav'); ?>" placeholder="e.g. .nav-primary or .nav-secondary" /></td>
			        </tr>
			        
			        <tr valign="top">
			        <th scope="row">Active when window is wider than</th>
			        <td><input type="text" name="genesis_sticky_nav_max" value="<?php echo get_option('genesis_sticky_nav_max'); ?>" placeholder="e.g. 960" />px</td>
			        </tr>

			        <tr valign="top">
			        <th scope="row">Offset from the top</th>
			        <td><input type="text" name="genesis_sticky_nav_topoffset" value="<?php echo get_option('genesis_sticky_nav_topoffset'); ?>" placeholder="e.g. 26" />px</td>
			        </tr>
			    </table>		    
			    <?php submit_button(); ?>
			</form>
		</div>
	<?php }
endif;
?>
