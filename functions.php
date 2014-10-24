<?php

define('GALOPIN_COCORICO_PREFIX', 'galopin_');
if(is_admin())
	require_once 'admin/Cocorico/Cocorico.php';

// Widgets
require_once 'admin/widgets/social.php';
require_once 'admin/widgets/calltoaction.php';
require_once 'admin/widgets/video.php';

// Themes functions
require_once 'admin/functions/galopin-functions.php';

//////////////////
// Bootstraping //
//////////////////
if (!function_exists('galopin_activation')){
	function galopin_activation(){
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}
add_action('after_switch_theme', 'galopin_activation');

//Register menus, sidebars and image sizes
if (!function_exists('galopin_setup')){
	function galopin_setup(){
		// Register menus
		register_nav_menu('primary', __('Main menu', 'galopin'));
		register_nav_menu('footer', __('Footer menu', 'galopin'));
		
		//Register sidebars
		register_sidebar(array(
			'name'          => __('Sidebar', 'galopin'),
			'id'            => 'blog',
			'description'   => __('Add widgets in the sidebar.', 'galopin'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		));

		register_sidebar(array(
			'name'          => __('Footer', 'galopin'),
			'id'            => 'footer',
			'description'   => __('Add widgets in the footer.', 'galopin'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		));
		
		// Enable thumbnails
		add_theme_support('post-thumbnails');
		
		// Set images sizes
		add_image_size('galopin-post-thumbnail', 633, 400, true);
		add_image_size('galopin-post-thumbnail-full', 900, 400, true);
		
		// Add Meta boxes for post formats
		require_once 'admin/metaboxes/post-formats.php';
		
		// Load language
		//load_theme_textdomain('galopin', get_template_directory().'/local');
	}
}
add_action('after_setup_theme', 'galopin_setup');

//add custom image size to native dailogs
if (!function_exists('galopin_image_size_names_choose')){
	function galopin_image_size_names_choose($sizes) {
		$added = array('galopin-post-thumbnail'=>__('Post', 'galopin'));
		$newsizes = array_merge($sizes, $added);
		return $newsizes;
	}
}
add_filter('image_size_names_choose', 'galopin_image_size_names_choose');

//register supported post formats
if(!function_exists('galopin_custom_format')){
	function galopin_custom_format() {
		$cpts = array('post' => array('video', 'link', 'quote'));
		$current_post_type = $GLOBALS['typenow'];
		if ($current_post_type == 'post') add_theme_support('post-formats', $cpts[$GLOBALS['typenow']]);
	}
}
add_action( 'load-post.php', 'galopin_custom_format' );
add_action( 'load-post-new.php', 'galopin_custom_format' );

//enqueue styles & scripts
if (!function_exists('galopin_enqueue')){
	function galopin_enqueue(){
	
		$theme = wp_get_theme();
		
		wp_register_script('fitvids', get_template_directory_uri().'/js/jquery.fitvids.js', array('jquery'), $theme->get('Version'), true);
		
		wp_register_script('galopin', get_template_directory_uri().'/js/galopin.js', array('jquery'), $theme->get('Version'), true);
		
		//main stylesheet
		wp_enqueue_style('stylesheet', get_stylesheet_directory_uri().'/style.css', array(), $theme->get('Version'));
		
		//icons
		wp_enqueue_style('icons', get_stylesheet_directory_uri().'/fonts/typicons.min.css', array(), $theme->get('Version'));
		
		wp_enqueue_script('fitvids');
		wp_enqueue_script('masonry');
		
		wp_enqueue_script('galopin');
	}
}
add_action('wp_enqueue_scripts', 'galopin_enqueue');

/////////////////////////
////  Admin stuff   /////
/////////////////////////

// Add admin menu
if (!function_exists('galopin_admin_menu')){
	function galopin_admin_menu(){
		add_theme_page('Galopin Settings', 'Galopin Settings', 'edit_theme_options', 'galopin_options', 'galopin_options');
	}
}
add_action('admin_menu', 'galopin_admin_menu');

if (!function_exists('galopin_options')){
	function galopin_options(){
		if (!current_user_can('edit_theme_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
       	
       	include 'admin/index.php';
    }
}

// Custom CSS loading
if(!function_exists('galopin_custom_styles')){
	function galopin_custom_styles(){
		if (get_option("galopin_custom_css")){
			echo '<style type="text/css">';
			echo htmlentities(stripslashes(get_option("galopin_custom_css")), ENT_NOQUOTES);
			echo '</style>';
		}
	}
}
add_action('wp_head', 'galopin_custom_styles', 99);

// Main galopin color
if(!function_exists('galopin_user_styles')){
	function galopin_user_styles(){
		if (get_option('galopin_color')){
			$color = apply_filters('galopin_color', get_option('galopin_color'));
			
			require_once 'admin/functions/color-functions.php';
			$hsl = galopin_RGBToHSL(galopin_HTMLToRGB($color));
			if ($hsl->lightness > 180){
				$contrast = apply_filters('galopin_color_contrast', '#333');
			}
			else{
				$contrast = apply_filters('galopin_color_contrast', '#fff');
			}
			
			$hsl->lightness -= 30;
			$complement = apply_filters('galopin_color_complement', galopin_HSLToHTML($hsl->hue, $hsl->saturation, $hsl->lightness));
		}
		else{ // Default color
			$color = '#E54C3C';
			$complement = '#c73829';
			$contrast = '#fff';
		} 
		?>
			<style type="text/css">
			.button, 
			.comment-form input[type="submit"], 
			html a.button, 
			.widget_calendar #next a, 
			.widget_calendar #prev a,
			.menu-wrapper,
			.search-form .submit-btn,
			.post-content th,
			.post-pagination,
			.pagination,
			.menu-wrapper .sub-menu a:hover,
			.back-to-top{
				background: <?php echo $color; ?>;
				color: <?php echo $contrast; ?>;
			}
			.button:hover, 
			.comment-form input[type="submit"]:hover, 
			html a.button:hover, 
			.widget_calendar #next a:hover, 
			.widget_calendar #prev a:hover,
			.search-form .submit-btn:hover,
			.post-content th:hover,
			.post-pagination:hover,
			.back-to-top:hover{
				background: <?php echo $complement; ?>;
				color: <?php echo $contrast; ?>;
			}
			.menu-wrapper .sub-menu a,
			.footer a,
			.post-header-title a:hover,
			.post-header-meta a,
			.masonry .post-header-title,
			.post-content ul > li:before,
			.post-content ol > li:before,
			.post-content a,
			.post-footer-meta a,
			.comment-author a,
			.comment-reply-link,
			.widget a,
			.comment-form .logged-in-as a,
			.post-header-title:before,
			.widget > h3:before{
				color: <?php echo $color; ?>;
			}
			.footer a:hover,
			.post-header-meta a:hover,
			.post-content a:hover,
			.post-footer-meta a:hover,
			.comment-author a:hover,
			.comment-reply-link:hover,
			.widget a:hover,
			.comment-form .logged-in-as a:hover{
				color: <?php echo $complement; ?>;
			}
			
			.footer,
			.post-header,
			.comment-footer,
			.masonry-footer,
			.masonry-header{
				border-color: <?php echo $color; ?>;
			}
			
			.masonry .brick:hover{
				<?php $hsl_hover = galopin_RGBToHSL(galopin_HTMLToRGB($contrast)); ?>
				background: <?php echo $color; ?>;
				text-shadow: 0 0 3px <?php echo galopin_HSLToHTML($hsl_hover->hue, $hsl_hover->saturation, $hsl_hover->lightness, 0.7); ?>;
			}
			
			.masonry .brick:hover .post-header-title,
			.masonry .brick:hover .post-header-title:before,
			.masonry .brick:hover .post-header-title blockquote a,
			.masonry .brick:hover .masonry-footer,
			.typcn-th-menu:before{
				text-shadow: 0 0 3px <?php echo galopin_HSLToHTML($hsl_hover->hue, $hsl_hover->saturation, $hsl_hover->lightness, 0.7); ?>;
			}
			
			.masonry .brick-link:before{
				background: <?php echo $contrast; ?>;
				color: <?php echo $color; ?>;
			}
			@media only screen and (max-width: 550px){
				body:not(.home) .hero-image{
					background: <?php echo $color; ?> !important;
					color: <?php echo $contrast; ?> !important;
				}
			}
			
			
			</style>
		<?php }
}
add_action('wp_head','galopin_user_styles', 98);


