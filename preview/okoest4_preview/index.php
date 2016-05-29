<?php
/*
Default Template
*/
$GLOBALS['theme_current_template_info'] = array('name' => 'default');
?>
<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>
<!DOCTYPE html>
<html <?php echo !is_rtl() ? 'dir="ltr" ' : ''; language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset') ?>" />
    <link href="<?php echo theme_get_image_path('images/68d29dc338a040274a56869a069f775a_favicon.ico'); ?>" rel="icon" type="image/x-icon" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <script>
    var themeHasJQuery = !!window.jQuery;
</script>
<script src="<?php echo get_bloginfo('template_url', 'display') . '/jquery.js?ver=' . wp_get_theme()->get('Version'); ?>"></script>
<script>
    window._$ = jQuery.noConflict(themeHasJQuery);
</script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--[if lte IE 9]>
<link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('template_url', 'display') . '/layout.ie.css' ?>" />
<script src="<?php echo get_bloginfo('template_url', 'display') . '/layout.ie.js' ?>"></script>
<![endif]-->
<script src="<?php echo get_bloginfo('template_url', 'display') . '/layout.core.js' ?>"></script>
<script src="<?php echo get_bloginfo('template_url', 'display'); ?>/CloudZoom.js?ver=<?php echo wp_get_theme()->get('Version'); ?>" type="text/javascript"></script>
    
    <?php wp_head(); ?>
    
</head>
<?php do_action('theme_after_head'); ?>
<?php ob_start(); // body start ?>
<body <?php body_class('data-control-id-49 bootstrap bd-body-2 bd-pagebackground'); /*   */ ?>>
<div data-affix
     data-offset=""
     data-fix-at-screen="top"
     data-clip-at-control="top"
     
 data-enable-lg
     
 data-enable-md
     
 data-enable-sm
     
     class="data-control-id-1365017 bd-affix-1"><header class="data-control-id-936270 bd-headerarea-1 ">
        <div class="data-control-id-1364682 bd-layoutcontainer-3 bd-columns">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row ">
                <div class="data-control-id-1432564 bd-columnwrapper-6 
 col-lg-1
 col-md-1">
    <div class="bd-layoutcolumn-6 bd-column" ><div class="bd-vertical-align-wrapper"><div id="carousel-2" class="bd-slider-2 bd-slider data-control-id-1412659 carousel slide bd-carousel-fade" >
    

    
    <div class="bd-container-inner">

    

    <div class="bd-slides carousel-inner">
        <div class="data-control-id-1465678 bd-slide-9 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class="data-control-id-1414044 bd-slide-6 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class="data-control-id-1463159 bd-slide-8 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class="data-control-id-1472666 bd-slide-10 bd-background-width  bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class="data-control-id-1412661 bd-slide-4 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class="data-control-id-1413473 bd-slide-5 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class="data-control-id-1414504 bd-slide-7 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class="data-control-id-1438408 bd-slide-1 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class="data-control-id-1438864 bd-slide-2 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class="data-control-id-1439320 bd-slide-3 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
    </div>

    

    
    </div>

    

    <script type="text/javascript">
        /* <![CDATA[ */
        if ('undefined' !== typeof initSlider) {
            initSlider(
                '.bd-slider-2',
                {
                    leftButtonSelector: 'bd-left-button',
                    rightButtonSelector: 'bd-right-button',
                    navigatorSelector: '.bd-carousel',
                    indicatorsSelector: '.bd-indicators',
                    carouselInterval: 5000,
                    carouselPause: "hover",
                    carouselWrap: true,
                    carouselRideOnStart: true
                }
            );
        }
        /* ]]> */
    </script>
</div></div></div>
</div>
	
		<div class="data-control-id-1364684 bd-columnwrapper-8 
 col-lg-10
 col-md-10
 col-sm-6">
    <div class="bd-layoutcolumn-8 bd-column" ><div class="bd-vertical-align-wrapper"><div class="data-control-id-700 bd-headline-1 hidden-xs">
    <div class="bd-container-inner">
        <h3>
            <a href="<?php echo  home_url(); ?>/"><?php echo get_bloginfo('name'); ?></a>
        </h3>
    </div>
</div>
	
		<div class="data-control-id-701 bd-slogan-1 hidden-sm hidden-xs">
    <div class="bd-container-inner">
        <?php bloginfo('description'); ?>
    </div>
</div></div></div>
</div>
	
		<div class="data-control-id-1364686 bd-columnwrapper-9 
 col-lg-1
 col-md-1
 col-sm-6">
    <div class="bd-layoutcolumn-9 bd-column" ><div class="bd-vertical-align-wrapper"><?php theme_logo_2(); ?></div></div>
</div>
            </div>
        </div>
    </div>
</div>
</header></div>
	
		<div class="data-control-id-1243624 bd-stretchtobottom-6 bd-stretch-to-bottom" data-control-selector=".bd-contentlayout-2">
<div class="bd-contentlayout-2  bd-sheetstyles data-control-id-318" >
    <div class="bd-container-inner">

        
        <div class="bd-flex-vertical bd-stretch-inner">
            
 <?php theme_sidebar_area_6(); ?>
            <div class="bd-flex-horizontal bd-flex-wide">
                
 <?php theme_sidebar_area_5(); ?>
                <div class="bd-flex-vertical bd-flex-wide">
                    

                    <div class="data-control-id-1118911 bd-layoutitemsbox-16 bd-flex-wide">
    <div class="data-control-id-369814 bd-content-14">
    <div class="bd-container-inner">
    
    <?php theme_print_content(); ?>
    </div>
</div>
</div>

        
                    
                </div>
                
 <?php theme_sidebar_area_1(); ?>
            </div>
            
        </div>

    </div>
</div></div>
	
		<div data-smooth-scroll data-animation-time="250" class="data-control-id-520690 bd-smoothscroll-3"><a href="#" class="data-control-id-2787 bd-backtotop-1 ">
    <span class="bd-icon-67 bd-icon data-control-id-2786"></span>
</a></div>
	
		<footer class="data-control-id-936277 bd-footerarea-1">
    <?php if (theme_get_option('theme_override_default_footer_content')): ?>
        <?php echo do_shortcode(theme_get_option('theme_footer_content')); ?>
    <?php else: ?>
        <div class="data-control-id-2772 bd-layoutcontainer-28 bd-columns">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row ">
                <div class="data-control-id-2764 bd-columnwrapper-62 
 col-sm-3">
    <div class="bd-layoutcolumn-62 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer1", 'footer_2_3');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer1', 'data-control-id-2560 bd-footerwidgetarea-3 clearfix', '');
?></div></div>
</div>
	
		<div class="data-control-id-2766 bd-columnwrapper-63 
 col-sm-3">
    <div class="bd-layoutcolumn-63 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer2", 'footer_8_4');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer2', 'data-control-id-2627 bd-footerwidgetarea-4 clearfix', '');
?></div></div>
</div>
	
		<div class="data-control-id-2768 bd-columnwrapper-64 
 col-sm-3">
    <div class="bd-layoutcolumn-64 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer3", 'footer_6_6');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer3', 'data-control-id-2694 bd-footerwidgetarea-6 clearfix', '');
?></div></div>
</div>
	
		<div class="data-control-id-1494476 bd-columnwrapper-7 
 col-sm-3">
    <div class="bd-layoutcolumn-7 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer4", 'footer_15_13');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer1', 'data-control-id-1494481 bd-footerwidgetarea-13 clearfix', '');
?></div></div>
</div>
            </div>
        </div>
    </div>
</div>
    <?php endif; ?>
</footer>
<div id="wp-footer">
    <?php wp_footer(); ?>
    <!-- <?php printf(__('%d queries. %s seconds.', 'default'), get_num_queries(), timer_stop(0, 3)); ?> -->
</div>
</body>
<?php echo apply_filters('theme_body', ob_get_clean()); // body end ?>
</html>