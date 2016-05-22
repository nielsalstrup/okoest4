<?php $GLOBALS['theme_content_function'] = 'theme_product_overview'; ?>
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
<script src="<?php echo get_bloginfo('template_url', 'display'); ?>/CloudZoom.js?ver=<?php echo wp_get_theme()->get('Version'); ?>" type="text/javascript"></script>
    
    <?php wp_head(); ?>
    
    <!--[if lte IE 9]>
    <link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('template_url', 'display') . '/style.ie.css?ver=' . wp_get_theme()->get('Version'); ?>" />
    <![endif]-->
</head>
<?php do_action('theme_after_head'); ?>
<?php ob_start(); // body start ?>
<body <?php body_class(' bootstrap bd-body-4 bd-pagebackground'); /*   */ ?>>
<div data-affix
     data-offset=""
     data-fix-at-screen="top"
     data-clip-at-control="top"
     
 data-enable-lg
     
 data-enable-md
     
 data-enable-sm
     
     class=" bd-affix-1"><header class=" bd-headerarea-1 ">
        <div class=" bd-layoutcontainer-3 bd-columns
    
    ">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row">
                <div class=" bd-columnwrapper-6 
 col-lg-1
 col-md-1">
    <div class="bd-layoutcolumn-6 bd-column" ><div class="bd-vertical-align-wrapper"><div id="carousel-2" class="bd-slider-2 bd-slider  carousel slide bd-carousel-fade" >
    

    
    <div class="bd-container-inner">

    

    <div class="bd-slides carousel-inner">
        <div class=" bd-slide-9 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class=" bd-slide-6 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class=" bd-slide-8 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class=" bd-slide-10 bd-background-width  bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class=" bd-slide-4 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class=" bd-slide-5 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class=" bd-slide-7 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class=" bd-slide-1 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class=" bd-slide-2 bd-slide item"
    
    
    >
    <div class="bd-container-inner">
        <div class="bd-container-inner-wrapper">
            
        </div>
    </div>
</div>
	
		<div class=" bd-slide-3 bd-slide item"
    
    
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
	
		<div class=" bd-columnwrapper-8 
 col-lg-10
 col-md-10
 col-sm-6">
    <div class="bd-layoutcolumn-8 bd-column" ><div class="bd-vertical-align-wrapper"><div class=" bd-headline-1 hidden-xs">
    <div class="bd-container-inner">
        <h3>
            <a href="<?php echo  home_url(); ?>/"><?php echo get_bloginfo('name'); ?></a>
        </h3>
    </div>
</div>
	
		<div class=" bd-slogan-1 hidden-sm hidden-xs">
    <div class="bd-container-inner">
        <?php bloginfo('description'); ?>
    </div>
</div></div></div>
</div>
	
		<div class=" bd-columnwrapper-9 
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
	
		<div class="bd-containereffect-5 container ">
<div class="bd-contentlayout-4  bd-sheetstyles ">
    <div class="bd-container-inner">

        
        <div class="bd-flex-vertical bd-stretch-inner">
            
            <div class="bd-flex-horizontal bd-flex-wide">
                
 <?php theme_sidebar_area_5(); ?>
                <div class="bd-flex-vertical bd-flex-wide">
                    

                    <div class=" bd-layoutitemsbox-19 bd-flex-wide">
    <div class=" bd-content-5">
    <div class="bd-container-inner">
    
    <?php theme_print_content(); ?>
    </div>
</div>
</div>

        
                    
                </div>
                
            </div>
            
        </div>

    </div>
</div></div>
	
		<footer class=" bd-footerarea-1">
    <?php if (theme_get_option('theme_override_default_footer_content')): ?>
        <?php echo do_shortcode(theme_get_option('theme_footer_content')); ?>
    <?php else: ?>
        <div class=" bd-layoutcontainer-28 bd-columns
    
    ">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row">
                <div class=" bd-columnwrapper-62 
 col-sm-3">
    <div class="bd-layoutcolumn-62 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer1", 'footer_2_3');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer1', ' bd-footerwidgetarea-3 clearfix', '');
?></div></div>
</div>
	
		<div class=" bd-columnwrapper-63 
 col-sm-3">
    <div class="bd-layoutcolumn-63 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer2", 'footer_8_4');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer2', ' bd-footerwidgetarea-4 clearfix', '');
?></div></div>
</div>
	
		<div class=" bd-columnwrapper-64 
 col-sm-3">
    <div class="bd-layoutcolumn-64 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer3", 'footer_6_6');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer3', ' bd-footerwidgetarea-6 clearfix', '');
?></div></div>
</div>
	
		<div class=" bd-columnwrapper-7 
 col-sm-3">
    <div class="bd-layoutcolumn-7 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar("footer4", 'footer_15_13');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'footer1', ' bd-footerwidgetarea-13 clearfix', '');
?></div></div>
</div>
            </div>
        </div>
    </div>
</div>
    <?php endif; ?>
</footer>
	
		<div data-smooth-scroll data-animation-time="250" class=" bd-smoothscroll-3"><a href="#" class=" bd-backtotop-1 ">
    <span class="bd-icon-67 bd-icon "></span>
</a></div>
<div id="wp-footer">
    <?php wp_footer(); ?>
    <!-- <?php printf(__('%d queries. %s seconds.', THEME_NS), get_num_queries(), timer_stop(0, 3)); ?> -->
</div>
</body>
<?php echo apply_filters('theme_body', ob_get_clean()); // body end ?>
</html>