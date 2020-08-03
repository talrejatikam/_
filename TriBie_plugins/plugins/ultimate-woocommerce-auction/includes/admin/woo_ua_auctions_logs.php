<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* == NOTICE ===================================================================
 * Please do not alter this file. Instead: make a copy of the entire plugin, 
 * rename it, and work inside the copy. If you modify this plugin directly and 
 * an update is released, your changes will be lost!
 * ========================================================================== */



/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 * Our theme for this list table is going to be movies.
 */
class Woo_Ua_Logs_List_Table extends WP_List_Table {
	
	/**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;
	    
        parent::__construct(array(
            'singular' => 'woo_ua_logs_list',
            'plural' =>  'woo_ua_logs_list',
			'ajax'      => false 
        ));
    }
	/**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default( $item, $column_name ) {
		global $wpdb;
		$datetimeformat = get_option('date_format').' '.get_option('time_format');
		switch( $column_name ) {
			case 'auction_id':
                return '<a href="'.get_permalink( $item[$column_name] ).'">'.get_the_title(  $item[$column_name] ).'</a>';
			case 'bid':
                return wc_price($item[$column_name]);
            case 'date':				
                return mysql2date($datetimeformat,$item[$column_name]);
				
            case 'userid':
                
                $userdata = get_userdata( $item[$column_name] );
                if ($userdata){
                   ?>
					<a href="<?php echo get_edit_user_link($item[$column_name] );?>"><?php echo $userdata->user_nicename;?><a>
				<?php
                } else {
                    return 'User id:'.$item[$column_name];
                }
            
				
			default:
				$default_value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';    			
    			break;
		}

	}
	
	/**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_title($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2		
		
		 return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['hits'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
	
	/**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
		
        return sprintf(
            '<input type="checkbox" name="id[]"   value="%s"/>',
            $item['id']
        );
		
    }

	/**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
           // 'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'auction_id' => __('Auction Title', 'woo_ua'),
            'userid' => __('Bidder Name', 'woo_ua'),
            'bid' => __('Bid', 'woo_ua'),
            'date' => __('Bidding Time', 'woo_ua'),          
           
        );
        return $columns;
    }

	 /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'auction_id' => array('auction_id', true),
            'bid' => array('bid', true),
            'date' => array('date', true),
          
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        //return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'woo_ua_auction_log'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

	
    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'woo_ua_auction_log'; // do not forget about tables prefix

        $per_page = 50; // constant, how much records will be shown per page
		if(isset($_REQUEST['s'])) {
			
			$search = $_REQUEST['s'];
		}
		
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
		

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] -1) * $per_page) : 0;
       $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'date';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'DESC';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        
		
		if(isset($_REQUEST['s']) != NULL ){
       
        // Trim Search Term
        $search = trim($search);
       
        /* Notice how you can search multiple columns for your search term easily, and return one data set */
        $test = $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `bid` LIKE '%%%s%%' OR `date` LIKE '%%%s%%'", $search, $search), ARRAY_A);
 
		}
		else 
		{
			$test = $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
			
		}
		
		
		//print_r($test);
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
	
}

/**
	 * Information Page
	 
	 * @package Devsite MCC App Menu
	 * @since 1.0.0 
	 */
	 function woo_ua_list_page_handler_display() {
		 // menu list 
			
			global $wpdb;
			$table = new Woo_Ua_Logs_List_Table();
			//$table->prepare_items();
			if( isset($_POST['s']) ){
                $table->prepare_items($_POST['s']);
			} else {
					$table->prepare_items();
			}
			
			
			$message = '';
			if ('delete' === $table->current_action()) {
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Record deleted successfully', 'woo_ua'), count($_REQUEST['id'])) . '</p></div>';
			}
			?>
			
		<div class="wrap">		
			<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>			
			<?php echo $message; ?>			
			<form id="persons-table" method="GET">			
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			
			<?php $table->search_box( __( 'Search' ), 'search-box-id' );?>
			
			<?php $table->display();?>
				
			</form>
		</div>
		
		
		<?php	
	}