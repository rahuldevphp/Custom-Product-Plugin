<?php  
/**
 * Plugin Name:       Custom Product Plugin
 * Description:       This plugin is the custom product plugin and created by Rahul prajapati.
 * Version:           1.0.0
 * Author:            Rahul prajapati
 * Author URI:
 */

function cp_enqueue() {

      wp_enqueue_script( 'ajax-script', plugins_url( 'custom-product-plugin/assets/js/custom_product.js' , dirname(__FILE__) ) , array('jquery') );
      wp_localize_script( 'ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
 }
add_action( 'admin_enqueue_scripts', 'cp_enqueue' );

global $cp_db_version;
$cp_db_version = '1.0';

function cp_install() {
    global $wpdb;
    global $cp_db_version;

    $table_name = $wpdb->prefix . 'cp_product';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(11) NOT NULL AUTO_INCREMENT,
        title varchar(55) NOT NULL,
        description text NOT NULL,
        availability varchar(55) NOT NULL,
        manufacture varchar(55) NOT NULL,
        feature_product varchar(55) NOT NULL,
        price varchar(255) NOT NULL,
        product_image varchar(255) NULL,
        create_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        update_at datetime DEFAULT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'cp_db_version', $cp_db_version );
}

register_activation_hook( __FILE__, 'cp_install' );

add_action('wp_ajax_cp_add_product','cp_add_product_fun');

add_action('wp_ajax_nopriv_cp_add_product','cp_add_product_fun');

if (! function_exists('cp_add_product_fun')) {
    
    function cp_add_product_fun()
    {

        global $wpdb;
        $table = $wpdb->prefix . 'cp_product';
        $error_msg = array();
        if (isset($_POST['cp_id']) && $_POST['cp_id'] != '') {
            
            $cp_id = $_POST['cp_id'];
            
            $product = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $cp_id ),ARRAY_A );
            if (empty($product) && $product == '') {
               $error_msg[] = "Product not available";
            }          

        }

        if (isset($_POST['cp_title']) && $_POST['cp_title'] != '') {
            $cp_title = $_POST['cp_title'];           
        }else{
            $error_msg[] = "Product Title";
        }
        

        if (isset($_POST['cp_description']) && $_POST['cp_description'] != '') {
            $cp_description = $_POST['cp_description'];           
        }else{
            $error_msg[] = "Product Description";
        }

        if (isset($_POST['cp_availability']) && $_POST['cp_availability'] != '') {
            $cp_availability = implode(",",$_POST['cp_availability']);          
        }else{
            $error_msg[] = "Product Availabiity";
        }

        if (isset($_POST['cp_manufacture_by']) && $_POST['cp_manufacture_by'] != '') {
            $cp_manufacture_by = $_POST['cp_manufacture_by'];           
        }else{
            $error_msg[] = "Product Manufacture";
        }

        if (isset($_POST['cp_feature_product']) && $_POST['cp_feature_product'] != '') {
            $cp_feature_product = $_POST['cp_feature_product'];           
        }else{
            $error_msg[] = "Product Feature";
        }

        if (isset($_POST['cp_price']) && $_POST['cp_price'] != '') {
            $cp_price = $_POST['cp_price'];  
            if (!preg_match("/^[0-9]+(\.[0-9]{2})?$/", $cp_price)) {
                $error_msg[] = "Product Price must be number format";
            }
        }else{
            $error_msg[] = "Product Price";
        }
        
        if (!empty($error_msg)) {
           $result['msg'] = $error_msg;
           $result['status'] = false;
        }else{

                if (isset($cp_id) && !empty($cp_id)) {

                    $data =  array( 
                            //'id' => $cp_id, 
                            'title' => $cp_title, 
                            'description' => $cp_description, 
                            'availability' => $cp_availability, 
                            'manufacture' => $cp_manufacture_by, 
                            'feature_product' => $cp_feature_product, 
                            'price' => $cp_price, 
                            'product_image' => $product_image, 
                            'update_at' => current_time( 'mysql' ), 
                        ); 
                    $format = array('%s','%s','%s','%s','%s','%s','%s','%s');
                    $result_val = $wpdb->update($table, $data, array( 'id' => $cp_id ), $format);
                 
                if($result_val > 0){
                    $result['msg'] = array('Product Updated');            
                    $result['status'] = true; 
                }
                else{
                  exit( var_dump( $wpdb->last_query ) );
                }
                $wpdb->flush();

            }else{
               
                $data =  array( 
                            'title' => $cp_title, 
                            'description' => $cp_description, 
                            'availability' => $cp_availability, 
                            'manufacture' => $cp_manufacture_by, 
                            'feature_product' => $cp_feature_product, 
                            'price' => $cp_price, 
                            'product_image' => $product_image, 
                            'create_at' => current_time( 'mysql' ), 
                        ); 
                $format = array('%s','%s','%s','%s','%s','%s','%s','%s');
                $wpdb->insert($table,$data,$format);
                
                $result['msg'] = array('Product Created');            
                $result['status'] = true; 
            }           

        }

        $product_id = $wpdb->insert_id;        
        echo json_encode($result);
        die();        
    }
   
}

function cp_custom_product_admin_menu() {
    global $team_page;
    add_menu_page( __( 'Custom Product', 'custom-product' ), __( 'Product', 'custom-product' ), 'edit_posts', 'custom_product', 'cp_list_product', 'dashicons-groups', 6 ) ;

    add_submenu_page( 'custom_product', 'Add Product', 'Add Product', 'edit_posts', 'add-product', 'cp_add_product');
}
add_action( 'admin_menu', 'cp_custom_product_admin_menu' );



function cp_add_product(){
    
    require_once plugin_dir_path( __FILE__ ) . 'template/add-product.php';
     
}
function cp_list_product(){

    require_once plugin_dir_path( __FILE__ ) . 'template/list-product.php';
}



