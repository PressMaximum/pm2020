<?php
/**
 * Template Name: Docs
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package customify
 */

get_header(); ?>
<div id="page-titlebar" class="page-titlebar">
		<div class="page-titlebar-inner customify-container">
			<h1 class="titlebar-title h3"><?php the_title(); ?></h1>
			<div class="titlebar-tagline">Everything you need to get started</div>
			<div class="wedocs-header-search">
				<form role="search" method="post" class="search-form wedocs-search-form"
					itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction"
					action="<?php echo esc_url( home_url( '/help/documentation/search/' ) ); ?>">
					<meta itemprop="target" content="https://wpcustomify.com/help/documentation/search/{query}/"/>
					<div class="wedocs-search-input">
						<input type="search"
							class="search-field"
							itemprop="query-input"
							name="query"
							data-swplive="true"
							tabindex="1"
							placeholder="<?php _e( 'What can we help you with?', 'freemius' ); ?>"
							value="<?php echo get_search_query(); ?>"
							title="<?php echo esc_attr_x( 'Search for:', 'label' ); ?>"/>
						<input type="hidden" name="post_type" value="docs">
						<button type="submit" tabindex="1"><span><?php _e( 'Search', 'freemius' ); ?></span></button>

					</div>
					<div class="wedocs-sample-term"><?php _e( 'Search documentation using terms like "install", "import site", or "header builder".' ); ?></div>
				</form>
			</div>
		</div>
	</div>

	<div class="content-inner">

		<?php
		do_action( 'customify/content/before' );

		if ( ! customify_is_e_theme_location( 'single' ) ) {
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'page' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			endwhile; // End of the loop.
		}
		do_action( 'customify/content/after' );
		?>
	</div><!-- #.content-inner -->
<?php
get_footer();
