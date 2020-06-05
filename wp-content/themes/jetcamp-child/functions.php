<?php

add_action( 'wp_enqueue_scripts', 'porto_child_css', 1001 );

// Load CSS
function porto_child_css() {
	// porto child theme styles
	wp_deregister_style( 'styles-child' );
	wp_register_style( 'styles-child', esc_url( get_stylesheet_directory_uri() ) . '/style.css' );
	wp_enqueue_style( 'styles-child' );

	if ( is_rtl() ) {
		wp_deregister_style( 'styles-child-rtl' );
		wp_register_style( 'styles-child-rtl', esc_url( get_stylesheet_directory_uri() ) . '/style_rtl.css' );
		wp_enqueue_style( 'styles-child-rtl' );
	}
}

/**
* Move WooCommerce subcategory list items into
* their own <ul> separate from the product <ul>.
*/

add_action( 'init', 'move_subcat_lis' );

function move_subcat_lis() {
	// Remove the subcat <li>s from the old location.
	remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
	add_action( 'woocommerce_before_shop_loop', 'msc_product_loop_start', 1 );
	add_action( 'woocommerce_before_shop_loop', 'msc_maybe_show_product_subcategories', 2 );
	add_action( 'woocommerce_before_shop_loop', 'msc_product_loop_end', 3 );
}

/**
 * Conditonally start the product loop with a <ul> contaner if subcats exist.
 */
function msc_product_loop_start() {
	$subcategories = woocommerce_maybe_show_product_subcategories();
	if ( $subcategories ) {
		woocommerce_product_loop_start();
	}
}

/**
 * Print the subcat <li>s in our new location.
 */
function msc_maybe_show_product_subcategories() {
	echo woocommerce_maybe_show_product_subcategories();
}

/**
 * Conditonally end the product loop with a </ul> if subcats exist.
 */
function msc_product_loop_end() {
	$subcategories = woocommerce_maybe_show_product_subcategories();
	if ( $subcategories ) {
		woocommerce_product_loop_end();
	}
}



















/**WC Отключение оплаты при оформлении**/
add_filter( 'woocommerce_cart_needs_payment', '__return_false' );












/**Скрыть превью товаров если оно всего одно**/
add_action( 'woocommerce_product_thumbnails', 'enable_gallery_for_multiple_thumbnails_only', 5 );
function enable_gallery_for_multiple_thumbnails_only() {
    global $product;
    if( ! is_a($product, 'WC_Product') ) {
        $product = wc_get_product( get_the_id() );
    }
    if( empty( $product->get_gallery_image_ids() ) ) {
        remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
    }
}

//убираем количество в категориях
add_filter('woocommerce_subcategory_count_html','remove_count');

function remove_count(){
 $html='';
 return $html;
}

/** Hide Download Tab Admin menu */
add_filter( 'woocommerce_account_menu_items', 'custom_remove_downloads_my_account', 999 );
 
function custom_remove_downloads_my_account( $items ) {
unset($items['downloads']);
return $items;
}


/**
 * Дополнительное  описание категории
 **/

function wpm_taxonomy_edit_meta_field( $term ) {
	$t_id      = $term->term_id;
	$term_meta = get_option( "taxonomy_$t_id" );
	$content   = $term_meta['custom_term_meta'] ? wp_kses_post( $term_meta['custom_term_meta'] ) : '';
	$settings  = array( 'textarea_name' => 'term_meta[custom_term_meta]' );
	?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[custom_term_meta]">Дополнительное описание</label></th>
        <td>
			<?php wp_editor( $content, 'product_cat_details', $settings ); ?>
        </td>
    </tr>
	<?php
}

add_action( 'product_cat_edit_form_fields', 'wpm_taxonomy_edit_meta_field', 10, 2 );

function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id      = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys  = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][ $key ] ) ) {
				$term_meta[ $key ] = wp_kses_post( stripslashes( $_POST['term_meta'][ $key ] ) );
			}
		}
		update_option( "taxonomy_$t_id", $term_meta );
	}
}

add_action( 'edited_product_cat', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_product_cat', 'save_taxonomy_custom_meta', 10, 2 );




/**
 * Create Alt and Title Image
 */
function change_attachement_image_attributes( $attr, $attachment ) {
	// Get post parent
	$parent = get_post_field( 'post_parent', $attachment );

	// Get post type to check if it's product
	$type = get_post_field( 'post_type', $parent );
	if ( $type != 'product' ) {
		return $attr;
	}

	/// Get title and alt
	$title = get_post_field( 'post_title', $parent );
	$attr['alt']   = $title . ' - Jetcamp';
	$attr['title'] = $title . ' - Jetcamp';
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'change_attachement_image_attributes', 20, 2 );


