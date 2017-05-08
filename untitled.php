<?php



  // adding contact info below image in product page
  add_action( 'woocommerce_single_product_summary', 'add_contact_info_below_image', 35 ); 

function add_contact_info_below_image() {
   echo '<div class="contact-info">
    
    <h4>FOR MORE INFORMATION CALL  </h4>
    <p>
    <a href="tel:+34 931 863 342">+34 931 863 342</a></p>
    <p>
    <a href="tel:+34 628 317 320">+34 628 317 320</a>
    </p>
<div class="gift-image-text">
  
  
  <p><a class="elroy-button" href="#popup1"><img  src="http://ambrosiaspabcn.com/wp-content/uploads/send-as-a-gift.png"></a></p>


<div id="popup1" class="overlay">

	<div class="popup">
		<h2>Info box</h2>
		
		<a class="elroy-close" href="#" onclick="sendgift()">&times;</a>
		<input id="pop-name" type="text" name="name-on-tshirt" value="" /> 
		 For: <input id="pop-for" type="text" name="for">
		</div></div>
 <a class="gift-link" href="#">Send as Gift</a> </div><!-- gift-image-text  -->
    </div>'; 
}

// take price next to add to cart on product page

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 ); 
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 22 ); 
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
add_action( 'woocommerce_single_product_summary', 'the_content_by_elroy', 19 ); 

add_action('woocommerce_single_product_summary', 'elroy', 21);

function elroy() 
{
  echo '<div class="price-and-cart">';
}
add_action('woocommerce_single_product_summary', 'elroy_end', 31);
function elroy_end() 
{
  echo '</div>';
}

function the_content_by_elroy() 
{  ?>
 <div class"product-content"> <?php

  the_content(); ?>  </div> <!-- .product-content  --> <?php
}

// reodering tabs
add_filter( 'woocommerce_product_tabs', 'woo_reorder_tabs', 98 );
function woo_reorder_tabs( $tabs ) {

    $tabs['reviews']['priority'] = 5;           // Reviews first
    $tabs['description']['priority'] = 10;          // Description second
    

    return $tabs;
}

//Remove Sales Flash
add_filter('woocommerce_sale_flash', 'woo_custom_hide_sales_flash');
function woo_custom_hide_sales_flash()
{
    return false;
}
//  adding from https://sarkware.com/adding-custom-product-fields-to-woocommerce-without-using-plugins/

function add_name_on_tshirt_field() {
    echo '
                  <input id="in-cart"  type="hidden" name="name-on-tshirt" value="" />  
                  <input id="in-cart-for"  type="hidden" name="for" value="" />                       
              ';
}
add_action( 'woocommerce_before_add_to_cart_button', 'add_name_on_tshirt_field' );

 function tshirt_name_validation() { 
  print_r($_REQUEST);
   
    if ( empty( $_REQUEST['name-on-tshirt'] ) ) {
        wc_add_notice( __( 'Please enter a Name for Printing&hellip;', 'woocommerce' ), 'error' );
        return false;
    }
    return true;
  }
add_action( 'woocommerce_add_to_cart_validation', 'tshirt_name_validation', 10, 3 );

function save_name_on_tshirt_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['name-on-tshirt'] ) ) {
        $cart_item_data[ 'name_on_tshirt' ] = $_REQUEST['name-on-tshirt'];
       
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_name_on_tshirt_field', 10, 2 );

function render_meta_on_cart_and_checkout( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['name_on_tshirt'] ) ) {
        $custom_items[] = array( "name" => 'Name On T-Shirt', "value" => $cart_item['name_on_tshirt'] );
    }
    return $custom_items;
}
add_filter( 'woocommerce_get_item_data', 'render_meta_on_cart_and_checkout', 10, 2 );

function tshirt_order_meta_handler( $item_id, $values, $cart_item_key ) {
    if( isset( $values['name_on_tshirt'] ) ) {
        wc_add_order_item_meta( $item_id, "name_on_tshirt", $values['name_on_tshirt'] );
    }
}
add_action( 'woocommerce_add_order_item_meta', 'tshirt_order_meta_handler', 1, 3 );
