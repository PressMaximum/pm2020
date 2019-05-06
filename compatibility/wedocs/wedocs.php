<?php
/**
 * Customize docs permalinks parsing.
 *
 * @author Vova Feldman
 */
function wpcustomify_docs_permastruct_rewrite() {

	if ( post_type_exists( 'docs' ) ) {
		// Modify root slug to "help" instead of docs.
		global $wp_post_types;
		/**
		 * @var WP_Post_Type $docs
		 */
		$docs = $wp_post_types['docs'];
		$docs->remove_rewrite_rules();
		$docs->rewrite['slug'] = 'help';
		$docs->add_rewrite_rules();

		add_post_type_support( 'docs', 'excerpt' );
		add_post_type_support( 'docs', 'author' );
		add_post_type_support( 'docs', 'page-attributes' );
		add_post_type_support( 'docs', 'custom-fields' );
	}
}
add_action( 'init', 'wpcustomify_docs_permastruct_rewrite' );

/**
 * Customize docs search permastruct for search caching.
 *
 * @author Vova Feldman
 */
function wpcustomify_docs_search_permastruct_rewrite() {
	if ( post_type_exists( 'docs' ) ) {
		/**
		 * We want the search pages to be cached.
		 */
		add_rewrite_rule(
			'^help/documentation/search/([^/]+)/?',
			'index.php?post_type=docs&s=$matches[1]',
			'top'
		);
	}
}
// The reason we use priority 20, is because this method must be triggered
// after `remove_rewrite_rules()` of the docs CPT is called.
add_action( 'init', 'wpcustomify_docs_search_permastruct_rewrite', 20 );

/**
 * Customize docs permalinks parsing.
 *
 * @author Vova Feldman
 */
function wpcustomify_deregister_wedocs_style() {

	wp_deregister_style('wedocs-styles');
}
//add_action( 'wp_enqueue_scripts', 'wpcustomify_deregister_wedocs_style');

/**
 * Override weDocs breadcrumb to add schema.org rich snippets metadata.
 *
 * @author Vova Feldman (@svovaf)
 *
 * @return void
 */
function wpcustomify_wedocs_breadcrumbs() {
	global $post;

	$args = apply_filters( 'wedocs_breadcrumbs', array(
		'delimiter' => '<li class="delimiter">&rarr;</li>',
		'home'      => __( 'Home', 'wedocs' ),
		'before'    => '<li><span class="current">',
		'after'     => '</span></li>'
	) );

	$breadcrumb_position = 1;

	echo '<ul class="wedocs-breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';
	echo wpcustomify_wedocs_get_breadcrumb( $args['home'], home_url( '/' ), $breadcrumb_position );
	echo $args['delimiter'];
	$breadcrumb_position ++;

	if ( $post->post_type == 'docs' && $post->post_parent ) {
		$parent_id   = $post->post_parent;
		$breadcrumbs = array();

		while ( $parent_id ) {
			$page          = get_post( $parent_id );
			$breadcrumbs[] = wpcustomify_wedocs_get_breadcrumb( get_the_title( $page->ID ), get_permalink( $page->ID ), $breadcrumb_position );

			$parent_id = $page->post_parent;
			$breadcrumb_position ++;
		}

		$breadcrumbs = array_reverse( $breadcrumbs );
		for ( $i = 0; $i < count( $breadcrumbs ); $i ++ ) {
			echo $breadcrumbs[ $i ];

			if ( $i != count( $breadcrumbs ) - 1 ) {
				echo $args['delimiter'];
			}
		}

		echo ' ' . $args['delimiter'] . ' ' . $args['before'] . get_the_title() . $args['after'];

	}

	echo '</ul>';
}

/**
 * @author Vova Feldman (@svovaf)
 *
 * @param string $label
 * @param string $permalink
 * @param int    $position
 *
 * @return string
 */
function wpcustomify_wedocs_get_breadcrumb( $label, $permalink, $position = 1 ) {
	return '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
    <a itemprop="item" href="' . esc_attr( $permalink ) . '">
    <span itemprop="name">' . esc_html( $label ) . '</span></a>
    <meta itemprop="position" content="' . $position . '" />
</li>';
}

/* Turn off live search */
function wpcustomify_wedocs_ajax_search_filter( $args = array() ){

	if( isset( $_REQUEST['post_type'] ) ) {
		$post_type = sanitize_text_field( $_REQUEST['post_type'] );
		if ( strtolower( $post_type ) == 'docs' ) {
			$args['post_type'] = $post_type;
		}
	}

	return $args;

}
add_filter( 'searchwp_live_search_query_args', 'wpcustomify_wedocs_ajax_search_filter' );


// Add callout shortcode.
function wpcustomify_callout_shortcode( $atts, $html = '', $shortcode = 'c_note' ) {
	return "<blockquote class=\"{$shortcode}\"><p>{$html}</p></blockquote>";
}
function wpcustomify_add_callout_shortcodes() {
	add_shortcode( 'c_note', 'wpcustomify_callout_shortcode' );
	add_shortcode( 'c_warning', 'wpcustomify_callout_shortcode' );
	add_shortcode( 'c_tip', 'wpcustomify_callout_shortcode' );
}
add_action( 'init', 'wpcustomify_add_callout_shortcodes' );


