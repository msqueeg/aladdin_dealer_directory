<?php

class Link_List_Table extends WP_List_table {

	function __construct(){
		parent::__construct( array(
			'singular' => 'wp_list_text_link',
			'plural' => 'wp_list_test_links',
			'ajax' => false
			));
	}

	function extra_tablenav($which) {
		if ($which == "top"){
			echo "I'm the top of the table";
		}
		if ($which == "bottom"){
			echo "I'm after the table";
		}
	}

	function get_columns() {
		return $columns= array(
			'col_dealer_name' => __('name'),
			'col_dealer_url' => __('website'),
			'col_dealer_phone' => __('phone'),
			'col_dealer_latitude' => __('latitude'),
			'col_dealer_longitude' => __('longitude'),
			'col_dealer_city' => __('city'),
			'col_dealer_state' => __('state'),
			);
	}

	public function get_sortable_columns() {
		return $sortable = array(
			'col_dealer_state' => 'state',
			'col_dealer_name' => 'name',
			'col_dealer_phone' => 'phone'
			);
	}

	function prepare_items() {
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();

		$query = "SELECT * FROM wp_aladdin_dealers";

		$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
		$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
		if(!empty($orderby) & !empty($order)) {
			$query.=' ORDER BY'.$orderby.' '.$order;
		}

		// pagination
		$totalitems = $wpdb->query($query);
		$perpage = 5;
		$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
		if(empty($paged) || !is_numeric($paged) || $paged<= 0) {
			$paged = 1;
		}

		$totalpages = ceil($totalitems/$perpage);
		if(!empty($paged) && !empty($perpage)){
			$offset=($paged-1)*$perpage;
			$query.= ' LIMIT '.(int)$offset.','.(int)$perpage;
		}

		// register pagination

		$this->set_pagination_args( array(
			'total_items' => $totalitems,
			'total_pages' => $totalpages,
			'per_page' => $perpage,
			));

		// register columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		// fetch items
		$this->items = $wpdb->get_results($query);
	}

	function display_rows() {
		$records = $this->items;

		list( $columns, $hidden ) = $this->get_column_info();

		if(!empty($records)) { foreach($records as $rec){
			echo '<tr id="record_'.$rec->id.'">';
			
			foreach($columns as $column_name => $column_display_name){
				$class = "class='$column_name column-$column_name'";
				$style = "";
				if( in_array( $column_name, $hidden )){
					$style = 'style="display:none;"';
				}
				$attributes = $class . $style;

				$editlink = '/wp-admin/link.php?action=edit&link_id='.(int)$rec->id;

				switch ( $column_name ) {
				    case "col_dealer_name":  echo '<td '.$attributes.'>'.stripslashes($rec->name).'</td>';   break;
				    case "col_dealer_url": echo '<td '.$attributes.'>'.stripslashes($rec->website).'</td>'; break;
				    case "col_dealer_phone": echo '<td '.$attributes.'>'.stripslashes($rec->phone).'</td>'; break;
				    case "col_dealer_latitude": echo '<td '.$attributes.'>'.$rec->latitude.'</td>'; break;
				    case "col_dealer_longitude": echo '<td '.$attributes.'>'.$rec->longitude.'</td>'; break;
				    case "col_dealer_city": echo '<td '.$attributes.'>'.$rec->city.'</td>'; break;
				    case "col_dealer_state": echo '<td '.$attributes.'>'.$rec->state.'</td>'; break;
				 }
			}
			echo '</tr>';
		}}
	}
}
?>