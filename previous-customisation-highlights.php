<?php
/*
Plugin Name: Previous Customisation Highlights
Description: Adds a "Previous Customisations" tab to product pages, with media upload and delete support.
Version: 1.4
Author: Robert Craig
*/

// Add custom tab to product pages
add_filter( 'woocommerce_product_tabs', 'previous_customisation_highlights_add_custom_tab' );

function previous_customisation_highlights_add_custom_tab( $tabs ) {
    global $post;
    $images = get_post_meta( $post->ID, '_previous_customisations_images', true );

    // Check if there are any images, if so, add the custom tab
    if ( !empty($images) ) {
        $tabs['previous_customisations'] = array(
            'title'    => __( 'Previous Customisations', 'previous-customisation-highlights' ),
            'priority' => 50,
            'callback' => 'previous_customisation_highlights_tab_content'
        );
    }

    return $tabs;
}

// Content for the "Previous Customisations" tab on the product page
function previous_customisation_highlights_tab_content() {
    global $post;
    $images = get_post_meta( $post->ID, '_previous_customisations_images', true );

    echo '<h2>' . __( 'Previous Customisations', 'previous-customisation-highlights' ) . '</h2>';
    echo '<div class="previous-customisations-gallery">';
    foreach ( $images as $image_id ) {
        echo wp_get_attachment_image( $image_id, 'medium' );
    }
    echo '</div>';
}

// Add meta box for Previous Customisations images in the product edit page
add_action( 'add_meta_boxes', 'previous_customisation_highlights_add_meta_box' );
add_action( 'save_post', 'previous_customisation_highlights_save_meta_box_data' );

function previous_customisation_highlights_add_meta_box() {
    add_meta_box(
        'previous_customisations_meta_box',
        __( 'Previous Customisations', 'previous-customisation-highlights' ),
        'previous_customisation_highlights_meta_box_callback',
        'product',
        'side'
    );
}

// Meta box content for adding/removing Previous Customisations images in the product edit page
function previous_customisation_highlights_meta_box_callback( $post ) {
    wp_nonce_field( 'previous_customisation_highlights_save_meta_box_data', 'previous_customisation_highlights_meta_box_nonce' );

    $images = get_post_meta( $post->ID, '_previous_customisations_images', true );

    echo '<div id="previous-customisations-gallery-wrapper">';
    if ( !empty($images) ) {
        foreach ( $images as $image_id ) {
            echo '<div class="previous-customisation-image-wrapper">';
            echo wp_get_attachment_image( $image_id, 'thumbnail' );
            echo '<button type="button" class="remove-customisation-image" data-image-id="' . $image_id . '">' . __( 'Delete', 'previous-customisation-highlights' ) . '</button>';
            echo '</div>';
        }
    }
    echo '</div>';
    echo '<input type="hidden" id="previous_customisations_images" name="previous_customisations_images" value="' . esc_attr( json_encode( $images ) ) . '" />';
    echo '<button type="button" id="add-previous-customisations-images">' . __( 'Add Images', 'previous-customisation-highlights' ) . '</button>';
}

// Save meta box data when the product is saved
function previous_customisation_highlights_save_meta_box_data( $post_id ) {
    if ( !isset( $_POST['previous_customisation_highlights_meta_box_nonce'] ) ) {
        return;
    }
    if ( !wp_verify_nonce( $_POST['previous_customisation_highlights_meta_box_nonce'], 'previous_customisation_highlights_save_meta_box_data' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['previous_customisations_images'] ) ) {
        $images = json_decode( stripslashes( $_POST['previous_customisations_images'] ), true );
        update_post_meta( $post_id, '_previous_customisations_images', $images );
    }
}

// Enqueue scripts and styles for the product edit page
add_action( 'admin_enqueue_scripts', 'previous_customisation_highlights_enqueue_scripts' );

function previous_customisation_highlights_enqueue_scripts() {
    global $typenow;
    if ( $typenow == 'product' ) {
        wp_enqueue_media();
        wp_enqueue_script( 'previous-customisation-highlights-script', plugin_dir_url( __FILE__ ) . 'previous-customisation-highlights.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_style( 'previous-customisation-highlights-style', plugin_dir_url( __FILE__ ) . 'previous-customisation-highlights.css', array(), '1.0' );
    }
}
?>
