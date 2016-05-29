<?php
// additional custom functions here

// Event filter for Contact Form 7

function cf7_select_events() {

	// The Events Query
	$args = array(
		'post_type'     => 'ai1ec_event',
        'posts_per_page'=> '5',
        'tax_query'     => array(
            array(
                    'taxonomy'  => 'events_categories',
                    'fields'    => 'term_id',
                    'terms'   => 15
                ),
        ),
        'order'         => 'ASC',
        'orderby'       => 'title',
		'post_status'   => 'publish' );

	//$events_added = new WP_Query( $args );
    $events_added = get_posts( $args );

	// The Loop
    foreach ( $events_added as $event ) {
        $label = $event->post_title;
        $value = $event->post_title;
        $options[$label] = $value;        
    }
	return $options;

}

add_filter('event-filter', 'cf7_select_events', 10, 2 );

//Override adminbar menu

function theme_override_adminbar() {
    global $wp_admin_bar;
    /* @var WP_Admin_Bar $wp_admin_bar */

    if ( current_user_can( 'publish_posts' ) )
			$wp_admin_bar->add_node( array(
										  'id'     => 'new-post',
										  'parent' => 'new-content',
										  'title'  => 'Nyhed',
										  'href'   => admin_url( 'post-new.php' )
									 ) );

    if ( current_user_can( 'publish_topics' ) )
			$wp_admin_bar->add_node( array(
										  'id'     => 'new-topic',
										  'parent' => 'new-content',
										  'title'  => 'Snak',
										  'href'   => home_url( '/forums/forum/snakkehjornet/#bbp_topic_title' )
									 ) );
}

add_action( 'admin_bar_menu', 'theme_override_adminbar', 999 );

//Correct lost password link

function redirect_lostpassword_url( $lostpassword_url ) {
    return home_url('/wp-login.php?action=lostpassword');
}

add_filter( 'lostpassword_url','redirect_lostpassword_url', 10, 1 );