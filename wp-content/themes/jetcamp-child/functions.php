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


function jetcamp_child_setup() {
    $path = get_stylesheet_directory().'/languages';
    load_child_theme_textdomain( 'jetcamp-child', $path );
}
add_action( 'after_setup_theme', 'jetcamp_child_setup' );


/** woocommerce: change position of add-to-cart on single product **/
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 9 );
    
    
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
     add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 41 );
    



/**
 * Hide subcategory product Woo
 */
    function exclude_product_cat_children($wp_query) {
if ( isset ( $wp_query->query_vars['product_cat'] ) && $wp_query->is_main_query()) {
    $wp_query->set('tax_query', array( 
                                    array (
                                        'taxonomy' => 'product_cat',
                                        'field' => 'slug',
                                        'terms' => $wp_query->query_vars['product_cat'],
                                        'include_children' => false
                                    ) 
                                 )
    );
  }
}  
add_filter('pre_get_posts', 'exclude_product_cat_children');


/**
 * Custom Head Code
 */
function my_custom_js() {
    echo "<script></script>";
}
// Add hook for admin <head></head>
add_action( 'admin_head', 'my_custom_js' );
// Add hook for front-end <head></head>
add_action( 'wp_head', 'my_custom_js' );


function custom_variation_prefixes($price, $product)
{
    $price = '';
 
    if (!$product->min_variation_price || $product->min_variation_price !== $product->max_variation_price)
    {
 $price .= '<span class="from">' . _x('Self-Study:', 'min_price', 'woocommerce') . ' </span>';
 $price .= woocommerce_price($product->get_price());
    }
    if ($product->max_variation_price !== $product->min_variation_price)
    {
 $price .= '<span class="to"> ' . _x('Tutored:', 'max_price', 'woocommerce') . ' </span>';
 $price .= woocommerce_price($product->max_variation_price);
    }
 
    return $price;
}

/// перевод wishlist
add_filter('gettext', 'translate_text');
add_filter('ngettext', 'translate_text');
function translate_text($translated) {
$translated = str_ireplace('Product name', 'Наименование', $translated);
$translated = str_ireplace('Информация о заказе', 'Ваш заказ', $translated);
$translated = str_ireplace('No products added to the wishlist', 'В закладки товаров не добавлено', $translated);
$translated = str_ireplace('Apply Coupon', 'Применить купон', $translated);
$translated = str_ireplace('Консоль', 'Личный кабинет', $translated);
$translated = str_ireplace('RELATED PRODUCTS', 'Похожие товары', $translated);
$translated = str_ireplace('Детали', 'Дополнительная информация', $translated);
$translated = str_ireplace('Unit price', 'Цена', $translated);
return $translated;
}


/// AWS поиск в сайдбар

function my_search_sidebar(){
 //this is where we will implement our filter
	//echo '<div class="aws_title_search">Поиск</div>';
    echo do_shortcode('[aws_search_form]');
    echo '<div class="category_title_sidebar">Категории</div>';
}
add_filter( 'porto_before_sidebar', 'my_search_sidebar', 30 );



/**
 * @snippet       Add new textarea to Product Category Pages - WooCommerce
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.9
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */  
 
// ---------------
// 1. Display field on "Add new product category" admin page
 
add_action( 'product_cat_add_form_fields', 'bbloomer_wp_editor_add', 10, 2 );
 
function bbloomer_wp_editor_add() {
    ?>
    <div class="form-field">
        <label for="seconddesc"><?php echo __( 'Second Description', 'woocommerce' ); ?></label>
       
      <?php
      $settings = array(
         'textarea_name' => 'seconddesc',
         'quicktags' => array( 'buttons' => 'em,strong,link' ),
         'tinymce' => array(
            'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
            'theme_advanced_buttons2' => '',
         ),
         'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
      );
 
      wp_editor( '', 'seconddesc', $settings );
      ?>
       
        <p class="description"><?php echo __( 'This is the description that goes BELOW products on the category page', 'woocommerce' ); ?></p>
    </div>
    <?php
}
 
// ---------------
// 2. Display field on "Edit product category" admin page
 
add_action( 'product_cat_edit_form_fields', 'bbloomer_wp_editor_edit', 10, 2 );
 
function bbloomer_wp_editor_edit( $term ) {
    $second_desc = htmlspecialchars_decode( get_term_meta( $term->term_id, 'seconddesc', true ) );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="second-desc"><?php echo __( 'Second Description', 'woocommerce' ); ?></label></th>
        <td>
            <?php
          
         $settings = array(
            'textarea_name' => 'seconddesc',
            'quicktags' => array( 'buttons' => 'em,strong,link' ),
            'tinymce' => array(
               'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
               'theme_advanced_buttons2' => '',
            ),
            'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
         );
 
         wp_editor( $second_desc, 'seconddesc', $settings );
         ?>
       
            <p class="description"><?php echo __( 'This is the description that goes BELOW products on the category page', 'woocommerce' ); ?></p>
        </td>
    </tr>
    <?php
}
 
// ---------------
// 3. Save field @ admin page
 
add_action( 'edit_term', 'bbloomer_save_wp_editor', 10, 3 );
add_action( 'created_term', 'bbloomer_save_wp_editor', 10, 3 );
 
function bbloomer_save_wp_editor( $term_id, $tt_id = '', $taxonomy = '' ) {
   if ( isset( $_POST['seconddesc'] ) && 'product_cat' === $taxonomy ) {
      update_woocommerce_term_meta( $term_id, 'seconddesc', esc_attr( $_POST['seconddesc'] ) );
   }
}
 
// ---------------
// 4. Display field under products @ Product Category pages 
 
add_action( 'woocommerce_after_shop_loop', 'bbloomer_display_wp_editor_content', 5 );
 
function bbloomer_display_wp_editor_content() {
   if ( is_product_taxonomy() ) {
      $term = get_queried_object();
      if ( $term && ! empty( get_term_meta( $term->term_id, 'seconddesc', true ) ) ) {
         echo '<p class="term-description">' . wc_format_content( htmlspecialchars_decode( get_term_meta( $term->term_id, 'seconddesc', true ) ) ) . '</p>';
      }
   }
}
