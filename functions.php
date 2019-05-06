<?php
/**
 * WPCustomify functions and definitions.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Disable unuseful metadata
require_once get_stylesheet_directory() . '/metabox.php';

// Disable emoji.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Cleanup RPC.
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

// Cleanup oembed.
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );

// Remove WP generator meta.
remove_action( 'wp_head', 'wp_generator' );

// Disable XML RPC
add_filter(
	'xmlrpc_methods',
	function ( $methods ) {
		unset( $methods['pingback.ping'] );
		return $methods;
	}
);

// Enqueue child theme style
add_action( 'wp_enqueue_scripts', 'wpcustomify_enqueue_styles' );
function wpcustomify_enqueue_styles() {
	wp_enqueue_style( 'wpcustomify-typekit', 'https://use.typekit.net/xlx8wfz.css' );
	wp_enqueue_style( 'wpcustomify-style', get_stylesheet_directory_uri() . '/style.css', array( 'customify-style' ) );

	wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/assets/js/custom.js', array( 'jquery' ), '', false );

}

/* Support 3-rd plugins. */

// Wedocs (Inspiration from freemius guide: https://freemius.com/blog/build-knowledge-base-documentation )
$wedocs_file = trailingslashit( get_stylesheet_directory() ) . 'compatibility/wedocs/wedocs.php';
if ( is_readable( $wedocs_file ) && class_exists( 'WeDocs' ) ) {
	require_once $wedocs_file;
}

// Gravity Forms
$gforms_file = trailingslashit( get_stylesheet_directory() ) . 'compatibility/gravity_forms.php';
if ( is_readable( $gforms_file ) && class_exists( 'GFForms' ) ) {
	require_once $gforms_file;
}


function customify_wedocs_layout( $layout ) {

	if ( is_singular( 'docs' ) ) {
		return 'content';
	}
	return $layout;
}
add_filter( 'customify_get_layout', 'customify_wedocs_layout' );


function customify_edd_dashboad_url( $url ) {
	if ( isset( $GLOBALS['_customify_tab'] ) ) {
		$url = remove_query_arg( ( array( 'tab' ) ), $url );
		return add_query_arg( array( 'tab' => $GLOBALS['_customify_tab'] ), $url );
	}
	return $url;
}

add_filter( 'edd_get_current_page_url', 'customify_edd_dashboad_url', 35 );
add_filter( 'edd_subscription_update_url', 'customify_edd_dashboad_url', 35 );
add_filter( 'edd_subscription_cancel_url', 'customify_edd_dashboad_url', 35 );
add_filter( 'edd_subscription_reactivation_url', 'customify_edd_dashboad_url', 35 );


/*
 EDD */
/* Change download slug */
define( 'EDD_SLUG', 'products' );

/**
 * EDD
 * Displays a Manage Licenses link in purchase history
 *
 * @since 2.7
 */
function customify_edd_sl_site_management_links( $payment_id, $purchase_data ) {

	$licensing = edd_software_licensing();
	$downloads = edd_get_payment_meta_downloads( $payment_id );
	if ( $downloads ) :

		$manage_licenses_url = add_query_arg(
			array(
				'action' => 'manage_licenses',
				'payment_id' => $payment_id,
			)
		);
		if ( isset( $GLOBALS['_customify_tab'] ) ) {
			$manage_licenses_url = remove_query_arg( ( array( 'tab' ) ), $manage_licenses_url );
			$manage_licenses_url = add_query_arg( array( 'tab' => 'license-keys' ), $manage_licenses_url );
		}

		$manage_licenses_url  = esc_url( $manage_licenses_url );

		echo '<td class="edd_license_key">';
		if ( edd_is_payment_complete( $payment_id ) && $licensing->get_licenses_of_purchase( $payment_id ) ) {
			echo '<a href="' . esc_url( $manage_licenses_url ) . '">' . __( 'View Licenses', 'edd_sl' ) . '</a>';
		} else {
			echo '-';
		}
		echo '</td>';
	else :
		echo '<td>&mdash;</td>';
	endif;
}

remove_action( 'edd_purchase_history_row_end', 'edd_sl_site_management_links', 10, 2 );
add_action( 'edd_purchase_history_row_end', 'customify_edd_sl_site_management_links', 15, 2 );

// remove_action( 'edd_product_notes', 'edd_all_access_add_receipt_link', 10, 2 );
function add_sub_navigation() {
	if ( ! is_single() && ! is_page() && ! is_singular() ) {
		return;
	}
	global $post;
	$parent_id = ( $post->post_parent > 0 ) ? $post->post_parent : $post->ID;
	$custom_logo_id = get_post_meta( $parent_id, 'custom_logo_image', true );
	$custom_logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );

	if ( $custom_logo ) {

		?>
	<div id="sub-navigation">
		<div class="customify-container">
			<div class="customify-grid">
				<div class="sub-logo customify-col-2_md-2_sm-12">
				   
					<a href="<?php echo get_permalink( $parent_id ); ?>">
						<img src="<?php echo esc_url( $custom_logo[0] ); ?>" alt="">
					</a>
					<a id="NavMobileSelect" class="secondary-nav__mobile-button" aria-expanded="true"><span class="title"><?php echo get_the_title( $post->ID ); ?></span> <span class="nav-icon-angle">&nbsp;</span></a>
				</div>
				<div class="customify-col-10_md-10_sm-12">
					<?php
					$children = wp_list_pages( 'title_li=&child_of=' . $parent_id . '&echo=0&link_before=<span>&link_after=</span>&sort_column=menu_order' );
					if ( $children ) {
						?>
						<nav class="site-navigation nav-menu-desktop">
							<ul class="menu">
								<li class="menu-item <?php echo ( $parent_id == $post->ID ) ? 'current_page_item' : ''; ?>"><a href="<?php echo get_permalink( $parent_id ); ?>"><span><?php echo esc_attr__( 'Overview', 'wpcustomify' ); ?></span></a></li>
								<?php echo $children; ?>
							</ul>
						</nav>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
		<?php
	}
}
add_action( 'customify/after-header', 'add_sub_navigation', 10 );


function pm2020_search_docs_form() {
	?>
	<form role="search" method="post" class="search-form wedocs-search-form"
		itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction"
		action="<?php echo esc_url( home_url( '/help/documentation/search/' ) ); ?>">
		<meta itemprop="target" content="https://wpcustomify.com/help/documentation/search/{query}/"/>
		<div class="wedocs-search-input">
			<span class="wedocs-inw">
				<input type="search"
					class="search-field"
					itemprop="query-input"
					name="s"
					data-swplive="true"
					tabindex="1"
					placeholder="<?php _e( 'What can we help you with?', 'freemius' ); ?>"
					value="<?php echo get_search_query(); ?>"
					title="<?php echo esc_attr_x( 'Search for:', 'label' ); ?>"/>
				<?php
				// Search in the docs but it not working with search wp.
				/*
				$doc_id = get_the_ID();
				$dropdown_args = array(
					'post_type'         => 'docs',
					'echo'              => 1,
					'depth'             => 1,
					'show_option_none'  => __( 'All Docs', 'wedocs' ),
					'option_none_value' => 'all',
					'name'              => 'search_in_doc',
					'class'              => 'search_in_doc',
					'selected'              => false,
				);

				if ( isset( $_GET['search_in_doc'] ) && 'all' != $_GET['search_in_doc'] ) {
					$dropdown_args['selected'] = (int) $_GET['search_in_doc'];
				}

				if ( ! $dropdown_args['selected'] ) {
					$dropdown_args['selected']  = $doc_id;
				}


				wp_dropdown_pages( $dropdown_args );
				*/

				?>
			</span>
			<input type="hidden" name="post_type" value="docs">
			<button type="submit" tabindex="1"><span><?php _e( 'Search', 'freemius' ); ?></span></button>

		</div>
		<div class="wedocs-sample-term"><?php _e( 'Search documentation using terms like "install", "import site", or "header builder".' ); ?></div>
	</form>
	<?php
}
