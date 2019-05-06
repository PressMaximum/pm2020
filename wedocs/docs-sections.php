<?php
global $post;
$root        = $post;
$show_drafts = current_user_can( 'edit_others_pages' );
$sections    = get_children( array(
	'post_parent' => $root->ID,
	'post_type'   => 'docs',
	'post_status' => $show_drafts ? 'any' : 'publish',
    'orderby' => 'menu_order',
    'order' => 'ASC'
) );

function wedocs_count_section_docs( $id, $post_status = 'publish' ){
    global $wpdb;
	$id = absint( $id );
	$post_status = esc_sql( $post_status );
    $sql = "SELECT Count( ID ) as num_post FROM {$wpdb->posts} WHERE post_parent = {$id}";
    if ( $post_status != 'any' ) {
	    $sql .= " AND post_status= '{$post_status}'";
    }
    return $wpdb->get_var( $sql );
}


?>
<section id="docs_sections" class="docs_sections">
        <div class="customify-grid-3_md-2_xs-1">
            <?php $i = 0 ?>
            <?php foreach ( $sections as $section ) : ?>
                <div class="customify-col">
                    <a class="docs_section_link" href="<?php echo get_the_permalink( $section ) ?>">
                        <div class="docs_section">
                            <?php printf(
                                '<h2 class="docs_section_title">%s</h2>',
                                get_the_title( $section ),
                                get_the_permalink( $section )
                            ) ?>
                            <?php if ( ! has_excerpt( $section ) && current_user_can( 'edit_others_pages' ) ) : ?>
                                <p><strong
                                        style="text-transform: uppercase; color: red;"><?php _e( 'Excerpt is missing! (this is only visible for admins)' ) ?></strong>
                                </p>
                            <?php else : ?>
                                <p class="excerpt">
                                    <?php echo get_the_excerpt( $section ) ?>
                                </p>
                            <?php endif ?>

                            <p class="counter">
                                <i class="fa fa-book"></i>
                                <?php  echo wedocs_count_section_docs( $section->ID, $show_drafts ? 'any' : 'publish' ) ?>
                                <?php _e( 'articles', 'freemius' ) ?>
                            </p>
                        </div>
                    </a>
                </div>
                <?php $i ++ ?>
            <?php endforeach ?>
        </div>
</section>