<?php
/**
 * Calls the class on the post edit screen.
 */
function wpcustomify_metabox_init() {
	new WPCustomify_MetaBox();
}
if ( is_admin() ) {
	add_action( 'load-post.php',     'wpcustomify_metabox_init' );
    add_action( 'load-post-new.php', 'wpcustomify_metabox_init' );
    
}
/**
 * The Class.
 */
class WPCustomify_MetaBox {
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post',      array( $this, 'save'         ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'wpcustomify_load_admin_scripts' ), 10, 1 );
    }
    
    public function wpcustomify_load_admin_scripts( $hook ) {
		global $typenow;
		if( 'page' ==  $typenow  ) {
			wp_enqueue_media();
			// Registers and enqueues the required javascript.
			wp_enqueue_script( 'meta-box-imag', get_stylesheet_directory_uri() . '/assets/js/admin.js', array( 'jquery' ), '', false );
			wp_localize_script( 'meta-box-image', 'meta_image',
				array(
					'title' => __( 'Choose or Upload Image', 'wpcustomify' ),
					'button' => __( 'Use this image', 'wpcustomify' ),
				)
			);
			wp_enqueue_script( 'meta-box-image' );
		}
    }
    
	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
		$post_types = array( 'page' );
        if ( in_array( $post_type, array( 'page' ) ) ) {
            add_meta_box(
                'wpcustomify_post_settings',
                __( 'Custom Logo', 'wpcustomify' ),
                array( $this, 'render_meta_box_page' ),
                $post_type,
                'side',
                'high'
            );

        }
        
	}
	public function save( $post_id ) {
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		// Check if our nonce is set.
		if ( ! isset( $_POST['wpcustomify_page_settings_nonce'] ) ) {
			return $post_id;
		}
		$nonce = sanitize_text_field( $_POST['wpcustomify_page_settings_nonce'] );
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wpcustomify_page_settings' ) ) {
			return $post_id;
		}
		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// Check the user's permissions.
		if ( 'page' == get_post_type( $post_id ) ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}
		
    
        if ( isset( $_POST['custom_logo_image'] ) ) {
            update_post_meta( $post_id, 'custom_logo_image', sanitize_text_field( $_POST['custom_logo_image'] ) );
        }
	}
	
    
    public function render_meta_box_page( $post ) {
        wp_nonce_field( 'wpcustomify_page_settings', 'wpcustomify_page_settings_nonce' );
        $custom_image = get_post_meta( $post->ID, 'custom_logo_image', true );
        $image = ' button">Upload image';
        $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
        $display = 'none'; // display state ot the "Remove image" button
    
        if( $image_attributes = wp_get_attachment_image_src( $custom_image, $image_size ) ) {
    
            $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
            $display = 'inline-block';
    
        } 
    
        echo '
        <p>
            <label>
                <a href="#" class="btn_upload_image' . $image . '</a>
                <input type="hidden" name="custom_logo_image" id="custom_logo_image" value="' . $custom_image . '" />
                <a href="#" class="btn_remove_image" style="display:inline-block;display:' . $display . '">Remove</a>
            </label>
        </p>';
    }
}