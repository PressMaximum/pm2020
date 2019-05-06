<?php
/**
 * The template for displaying a single doc
 *
 * To customize this template, create a folder in your current theme named "wedocs" and copy it there.
 *
 * @package weDocs
 */

$skip_sidebar = ( get_post_meta( $post->ID, '_customify_sidebar', true ) == 'content' ) ? true : false;

get_header();

if ( empty( $post->post_parent ) ) {
	wedocs_get_template_part( 'docs', 'header-main' );
	wedocs_get_template_part( 'docs', 'sections' );
    get_footer();
	return;
}
?>


        <?php while ( have_posts() ) : the_post(); ?>

            <div class="wedocs-single-wrap">

                <div class="wedocs-single-content">

                    <?php wpcustomify_wedocs_breadcrumbs(); ?>

                    <div class="wedocs-inner">
                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/Article">
                            <header class="entry-header">
                                <?php the_title( '<h1 class="entry-title" itemprop="headline">', '</h1>' ); ?>

                                <?php if ( wedocs_get_option( 'print', 'wedocs_settings', 'on' ) == 'on' ): ?>
                                    <a href="javascript:window.print()" class="print-page hide-on-mobile" title="<?php echo esc_attr( __( 'Print this article', 'wedocs' ) ); ?>"><i class="fa fa-print"></i></a>
                                <?php endif; ?>
                            </header><!-- .entry-header -->

                            <div class="entry-content" itemprop="articleBody">
                                <?php
                                    the_content( sprintf(
                                        /* translators: %s: Name of current post. */
                                        wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'wedocs' ), array( 'span' => array( 'class' => array() ) ) ),
                                        the_title( '<span class="screen-reader-text">"', '"</span>', false )
                                    ) );

                                    wp_link_pages( array(
                                        'before' => '<div class="page-links">' . esc_html__( 'Docs:', 'wedocs' ),
                                        'after'  => '</div>',
                                    ) );

                                    $children = wp_list_pages("title_li=&order=menu_order&child_of=". $post->ID ."&echo=0&post_type=" . $post->post_type);

                                    if ( $children ) {
                                        echo '<div class="article-child well">';
                                            echo '<h3>' . __( 'Articles', 'wedocs' ) . '</h3>';
                                            echo '<ul>';
                                                echo $children;
                                            echo '</ul>';
                                        echo '</div>';
                                    }

                                    $tags_list = wedocs_get_the_doc_tags( $post->ID, '', ', ' );

                                    if ( $tags_list ) {
                                        printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                                            _x( 'Tags', 'Used before tag names.', 'wedocs' ),
                                            $tags_list
                                        );
                                    }
                                ?>
                            </div><!-- .entry-content -->

                            <footer class="entry-footer wedocs-entry-footer">
                                <span class="wedocs-help-link wedocs-hide-print wedocs-hide-mobile">
                                        <i class="fa fa-envelope"></i>
                                        Still stuck? Check other <a href="https://wpcustomify.com/help/documentation/">articles</a>, or open a <a href="https://wpcustomify.com/contact/">support ticket</a>.
                                </span>
                                <?php if ( wedocs_get_option( 'email', 'wedocs_settings', 'on' ) == 'on' ): ?>
                                    <span class="wedocs-help-link wedocs-hide-print wedocs-hide-mobile">
                                        <i class="fa fa-envelope"></i>
                                        <?php printf( '%s <a id="wedocs-stuck-modal" href="%s">%s</a>', __( 'Still stuck?', 'wedocs' ), '#', __( 'How can we help?', 'wedocs' ) ); ?>
                                    </span>
                                <?php endif; ?>

                                <div class="wedocs-article-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                    <meta itemprop="name" content="<?php echo get_the_author(); ?>" />
                                    <meta itemprop="url" content="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" />
                                </div>

                                <meta itemprop="datePublished" content="<?php echo get_the_time( 'c' ); ?>"/>
                                <time itemprop="dateModified" datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php printf( __( 'Updated on %s', 'wedocs' ), get_the_modified_date() ); ?></time>
                            </footer>

                            <?php wedocs_doc_nav(); ?>

                            <?php if ( wedocs_get_option( 'helpful', 'wedocs_settings', 'on' ) == 'on' ): ?>
                                <?php wedocs_get_template_part( 'content', 'feedback' ); ?>
                            <?php endif; ?>

                            <?php if ( wedocs_get_option( 'email', 'wedocs_settings', 'on' ) == 'on' ): ?>
                                <?php wedocs_get_template_part( 'content', 'modal' ); ?>
                            <?php endif; ?>


                        </article><!-- #post-## -->
                    </div><!-- .wedocs-inner -->
                </div><!-- .wedocs-single-content -->

	            <?php if ( ! $skip_sidebar ) { ?>

		            <?php wedocs_get_template_part( 'docs', 'sidebar' ); ?>

	            <?php } ?>

            </div><!-- .wedocs-single-wrap -->

        <?php endwhile; ?>



<?php get_footer(); ?>
