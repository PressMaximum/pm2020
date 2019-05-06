<?php if( ! empty( $_GET['edd-verify-success'] ) ) : ?>
<p class="edd-account-verified edd_success">
	<?php _e( 'Your account has been successfully verified!', 'easy-digital-downloads' ); ?>
</p>
<?php
endif;


class Customify_EDD_All_Access_Shortcodes extends EDD_All_Access_Shortcodes {
    function __construct()
    {
        // do nothing
    }

}

$cedda = new Customify_EDD_All_Access_Shortcodes();

/**
 * This template is used to display the download history of the current user.
 */
$purchases = edd_get_users_purchases( get_current_user_id(), 20, true, 'any' );
if ( $purchases ) :
	do_action( 'edd_before_download_history' ); ?>
	<table id="edd_user_history" class="edd-table">
		<thead>
			<tr class="edd_download_history_row">
				<th class="edd_download_download_name"><?php _e( 'Download Name', 'easy-digital-downloads' ); ?></th>
				<?php if ( ! edd_no_redownload() ) : ?>
					<th class="edd_download_download_files"><?php _e( 'Action', 'easy-digital-downloads' ); ?></th>
				<?php endif; //End if no redownload?>
			</tr>
		</thead>
		<?php

        $download_items = array();
        $all_download_items = array();
        foreach ( $purchases as $payment ) {
            $downloads = edd_get_payment_meta_cart_details($payment->ID, true);
            $purchase_data = edd_get_payment_meta($payment->ID);
            $email = edd_get_payment_user_email($payment->ID);

            if ($downloads) {
                foreach ($downloads as $download) {

                    // Skip over Bundles. Products included with a bundle will be displayed individually

                    if ( edd_is_bundled_product($download['id']) && ! edd_all_access_download_is_all_access( $download['id'] ) ) {
                        continue;
                    }

                    $price_id = edd_get_cart_item_price_id($download);
                    $name = $download['name'];

                    if ( edd_all_access_download_is_all_access( $download['id'] ) ){
                        // Set up the All Access Pass object
                        $all_access_pass = EDD_All_Access_Pass($payment->ID, $download['download_id'], $price_id );

                        $query_args = $cedda->all_access_products_only_in_downloads( array( 'post_type' => 'download' ), array('all_access_customer_downloads_only' => 'yes') );

                        $the_query = new WP_Query( $query_args );
                        if (  $the_query->have_posts() ) {

                            foreach( $the_query->get_posts() as $p ) {
                                $_download_id = $p->ID;
                                $_price_id = edd_get_default_variable_price($_download_id);
                                $_key = $name;
                                if ( ! isset( $download_items[ $_key ] ) ) {
                                    $download_items[ $_key ] = array(
                                        'name' => $name,
                                        'payment_id' => array(),
                                        'download_id' =>  array() ,
                                        'files' => array(),
                                        'msg' => ''
                                    );
                                }

                                // Get all of the files attached to this product.
                                $files_attached_to_product = edd_get_download_files($_download_id);

                                // Set the file id
                                $_file_id = 0;
                                $file_name = '';
                                foreach ($files_attached_to_product as $attached_file_id => $attached_file_info) {
                                    // Set the default file id to be the first file
                                    $_file_id = $attached_file_id;
                                    if ( $attached_file_info['name'] ) {
                                        $file_name = $attached_file_info['name'];
                                    } else {
                                        $file_name = $attached_file_info['name'];
                                    }

                                    //$button_href = edd_all_access_product_download_url($_download_id, $_price_id, $_file_id);
                                    $button_href = edd_get_download_file_url($purchase_data['key'], $email, $attached_file_id, $_download_id, $_price_id);
                                    $download_items[$_key]['files'][$file_name] = $button_href;

                                    $_file_name = basename( $attached_file_info['file'] );
                                    $all_download_items[ $_file_name ] = $button_href;
                                }

                            }

                        }


                    } else {
                        $download_files = edd_get_download_files($download['id'], $price_id);
                        // Retrieve and append the price option name
                        if (!empty($price_id) && 0 !== $price_id) {
                            $name .= ' - ' . edd_get_price_option_name($download['id'], $price_id, $payment->ID);
                        }

                        $_key = $name;

                        $_download_id = explode( '_', $download['id'] ) ;
                        $_download_id  = $_download_id[0];
                        if ( ! isset( $download_items[ $_key ] ) ) {
                            $download_items[ $_key ] = array(
                                'name' => $name,
                                'payment_id' => array(),
                                'download_id' =>  array() ,
                                'files' => array(),
                                'msg' => ''
                            );
                        }

                        $download_items[ $_key ]['payment_id'][] =  $payment->ID;
                        $download_items[ $_key ]['download_id'][] =  $download['id'];

                        if (!edd_no_redownload()) {
                            if ('publish' == $payment->post_status) {

                                if ($download_files) {

                                    foreach ($download_files as $filekey => $file) :
                                        $download_url = edd_get_download_file_url($purchase_data['key'], $email, $filekey, $download['id'], $price_id);
                                        $file_name = edd_get_file_name($file);
                                        $download_items[$_key]['files'][$file_name] = $download_url;


                                        $_file_name = basename( $file['file'] );
                                        $all_download_items[ $_file_name ] = $download_url;

                                    endforeach;
                                }
                            }

                        }  // End if ! edd_no_redownload()
                    }


                } // End foreach $downloads
            } // End if $downloads
        }// end for each payment


        /*
        foreach ( $download_items as $download_id => $item ) {
            ?>
            <tr class="edd_download_download_files">
                <?php
                do_action('edd_download_history_row_start',  $item['payment_id'], $item['download_id']);
                ?>
                <td class="edd_download_download_name"><?php echo esc_html( $item['name'] ); ?></td>
                <td class="edd_download_download_files">
                    <?php
                    if ( ! empty( $item['files'] ) ) {
                        $htm_list = array();
                        foreach ( $item['files'] as $file_name => $file_url ) {
                            $htm_list[] = '<a href="'.esc_url( $file_url ).'" class="edd_download_file_link">'.$file_name.'</a>';
                        }

                        echo join( '<br/>', $htm_list );
                    } else {
                        _e('No downloadable files found.', 'easy-digital-downloads');
                    }
                    ?>
                </td>
                <?php
                do_action('edd_download_history_row_end',  $item['payment_id'], $item['download_id']);
                ?>
            </tr>
            <?php
        }
        */

        foreach( $all_download_items as $file_name => $url ) {
            ?>
            <tr class="edd_download_download_files">
                <?php
                ?>
                <td class="edd_download_download_name"><?php echo esc_html( $file_name ); ?></td>
                <td class="edd_download_download_files">
                    <a href="<?php echo $url; ?>"><?php _e( 'Download' ); ?></a>
                </td>
            </tr>
            <?php
        }


		?>
	</table>
	<div id="edd_download_history_pagination" class="edd_pagination navigation">
		<?php
		$big = 999999;
		echo paginate_links( array(
			'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'  => '?paged=%#%',
			'current' => max( 1, get_query_var( 'paged' ) ),
			'total'   => ceil( edd_count_purchases_of_customer() / 20 ) // 20 items per page
		) );
		?>
	</div>
	<?php do_action( 'edd_after_download_history' ); ?>
<?php else : ?>
	<p class="edd-no-downloads"><?php _e( 'You have not purchased any downloads', 'easy-digital-downloads' ); ?></p>
<?php endif; ?>
