<?php 
global $wpdb;
$product_availability =array();
if (isset($_GET['id']) && $_GET['id']) {
    $product_id = $_GET['id'];
    $table = $wpdb->prefix . 'cp_product';
    $product = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $product_id ),ARRAY_A );
    $product_availability = explode(',', $product['availability']);
}
 ?>
 <style type="text/css">
     .msg-required{
        color: red;
     }
 </style>

<div class="wrap">
 	<div id="icon-options-general" class="icon32"><br>
 	</div>
        <?php if (isset($product_id) && $product_id != '') { ?>

            <h2><?php echo esc_html__( 'Edit Product', 'custom_product' )?></h2>
         
        <?php }else{ ?>

        <h2><?php echo esc_html__( 'Add Product', 'custom_product' )?></h2>
        <?php } ?>
        
        <div id="message_new" class="" style="display:none;">
        </div>

        <form method="POST" id="cp_form"  enctype="multipart/form-data">
        <table class="form-table">
        	 <?php wp_nonce_field( 'awesome_update', 'awesome_form' ); ?>
             <input type="hidden" name="action" value="cp_add_product">
             <input type="hidden" name="cp_id" value="<?php if(isset($product['id']) && $product['id']!= '') { echo $product['id']; } ?>">
            <tbody>
                <tr>
                    <th> (<span class="msg-required">*</span>) <?php echo esc_html__( 'Required fields', 'custom_product' )?></th>
                    <td></td>
                </tr>
                <tr>
                    <th><label for="title"><?php echo esc_html__( 'Title', 'custom_product' )?></label><span class="msg-required">*</span></th>
                    <td><input name="cp_title" id="cp_title" type="text" value="<?php if(isset($product['title']) && $product['title'] ) echo $product['title']; ?>" class="regular-text" /><br><span><?php echo esc_html__( 'Enter the Product Title.', 'custom_product' )?></span></td>
                </tr>

                <tr>
                    <th><label for="description"><?php echo esc_html__( 'Description', 'custom_product' )?></label><span class="msg-required">*</span></th>
                    <td><textarea id="cp_description" name="cp_description" rows="4" cols="50" class="regular-text"><?php if (isset($product['description']) && $product['description'] !='' ) { echo $product['description'];} ?></textarea><br><span><?php echo esc_html__( 'Enter the Product Description.', 'custom_product' )?></span></td>
                </tr>              

                <tr>
                    <th><label for="availability"><?php echo esc_html__( 'Availabiity', 'custom_product' )?></label><span class="msg-required">*</span></th>
                    <td>
                        <input name="cp_availability[]" id="cp_availability[]" type="checkbox" value="client" class="regular-text" <?php if (in_array('client', $product_availability)) { echo "checked"; } ?> /><?php echo esc_html__( 'Client', 'custom_product' )?>
                        <input name="cp_availability[]" id="cp_availability[]" type="checkbox" value="distributor" class="regular-text" <?php if (in_array('distributor', $product_availability)) { echo "checked"; } ?> /><?php echo esc_html__( 'Distributor', 'custom_product' )?><br><span><?php echo esc_html__( 'Choose the Product Availabiity.', 'custom_product' )?></span>
                    </td>
                </tr>

                <tr>
                    <th><label for="manufacture_by"><?php echo esc_html__( 'Manufacture By', 'custom_product' )?></label><span class="msg-required">*</span></th>
                    <td>
                        <select id="cp_manufacture_by" name="cp_manufacture_by" class="regular-text">
                            <option value=""><?php echo esc_html__( 'Select', 'custom_product' )?></option>
                            <option value="1" <?php if ( isset($product['manufacture']) && $product['manufacture'] == '1') { echo "selected"; } ?>><?php echo esc_html__( 'Type 1', 'custom_product' )?></option>
                            <option value="2" <?php if ( isset($product['manufacture']) && $product['manufacture'] == '2') { echo "selected"; } ?>><?php echo esc_html__( 'Type 2', 'custom_product' )?></option>
                        </select>
                        <br><span><?php echo esc_html__( 'Enter the Product Manufacture By.', 'custom_product' )?></span></td>
                </tr>  
                
                <tr>
                    <th><label for="feature_product"><?php echo esc_html__( 'Feature Product', 'custom_product' )?></label><span class="msg-required">*</span></th>
                    <td>
                        <input name="cp_feature_product" id="cp_feature_product" type="radio" value="yes" class="regular-text" <?php if ( isset($product['feature_product']) && $product['feature_product'] == 'yes') { echo "checked"; } ?> /><?php echo esc_html__( 'Yes', 'custom_product' )?>
                        <input name="cp_feature_product" id="cp_feature_product" type="radio" value="no" class="regular-text" <?php if ( isset($product['feature_product']) && $product['feature_product'] == 'no') { echo "checked"; } ?> /><?php echo esc_html__( 'No', 'custom_product' )?><br><span><?php echo esc_html__( 'Enter the Feature Product.', 'custom_product' )?></span>
                    </td>
                </tr>

                <tr>
                    <th><label for="price"><?php echo esc_html__( 'Price', 'custom_product' )?></label><span class="msg-required">*</span></th>
                    <td><input name="cp_price" id="cp_price" type="text" value="<?php if(isset($product['price']) && $product['price']){  echo $product['price']; } ?>" class="regular-text" /><br><span><?php echo esc_html__( 'Enter the Product Price.', 'custom_product' )?></span></td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php if(isset($product_id) && $product_id !='' ) { echo esc_html__( 'Update', 'custom_product' ); }else{ echo esc_html__( 'Save', 'custom_product' ); } ?>">
        </p>
        </form>
</div>