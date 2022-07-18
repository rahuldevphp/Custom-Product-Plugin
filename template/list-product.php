<?php 

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class List_Product extends WP_List_Table {    
    
   
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'product',     //singular name of the listed records
            'plural'    => 'product',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }

    function get_customers() {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}cp_product";

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'title':
            case 'manufacture':
            case 'feature_product':
            case 'price':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&id=%s">Edit</a>','add-product',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  
            /*$2%s*/ $item['id']   
        );
    }
   
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Title',
            'manufacture'    => 'Manufacture',
            'feature_product'  => 'Feature Product',
            'price'  => 'Price',
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'feature_product'  => array('feature_product',false),
            'price'    => array('price',false),
        );
        return $sortable_columns;
    }
 
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {

                global $wpdb;
                $table_name = $wpdb->prefix . 'cp_product';

                if (isset( $_GET['action'] ) && $_GET['action'] == 'delete' || ( isset( $_GET['id'] ) && $_GET['id'] != '' ) ) {
                    $ids = $_GET['id'];

                    $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
                    wp_die( 'Product deleted' );
                    //wp_redirect( esc_url_raw(add_query_arg()) );
                   // exit;
                     
                }             

                    // If the delete bulk action is triggered
                if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' )
                     || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' )
                ) {

                    $delete_ids = esc_sql( $_GET['product'] );

                    // loop over the array of record ids and delete them
                    foreach ( $delete_ids as $id ) {
                        //self::delete_customer( $id );
                         $wpdb->query("DELETE FROM $table_name WHERE id IN($id)");

                    }                 
                    wp_redirect( esc_url_raw(add_query_arg()) );
                    exit;
                }        

        }
        
    }
    
    function delete_customer( $id ) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}cp_product",
            [ 'id' => $id ],
            [ '%d' ]
        );
    }

    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;       
        
     
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();        
      
        $this->_column_headers = array($columns, $hidden, $sortable);        
       
        $this->process_bulk_action();
        
        //$data = $this->example_data;    
        $data = self::get_customers();
        
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');        
       
        $current_page = $this->get_pagenum();        
       
        $total_items = count($data);
       
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);        
      
        $this->items = $data;        
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}

$testListTable = new List_Product();

$testListTable->prepare_items();

?>
<div class="wrap">
    
    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php echo esc_html__('List Product','custom-product'); ?></h2>       
  
    <form id="movies-filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <!-- Now we can render the completed list table -->
        <?php $testListTable->display() ?>
    </form>
    
</div>
