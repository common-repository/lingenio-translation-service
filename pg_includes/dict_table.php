<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class PG_Dict_Table extends WP_List_Table 
{
    
    var $table_data = array();		
	var $column_titles = array();
	var $sortable_columns = array();
	
    function __construct($singular, $plural, $table_data, $titles, $sortables)
	{
		parent::__construct(array($singular, $plural));
		$this->setData($table_data);
		$this->column_titles = $titles;
		$this->sortable_columns = $sortables;
    }
	
	function setData($table_data)
	{
		$this->table_data = $table_data;
	}
	
    function column_default($item, $column_name)
	{
    	return $item[$column_name];
    }
    
	/*methods for special columns*/
	
    function column_de($item)
	{
        
        $actions = array(
            'de'    => sprintf('<a href="?page='.$_REQUEST['page'].'&tab='.$_REQUEST['tab'].'&action=pg_edit_dict&id='.$item['id'].'&de='.$item['de'].'&en='.$item['en'].'&fr='.$item['fr'].'&es='.$item['es'].'">Bearbeiten</a> | <a href="?page='.$_REQUEST['page'].'&tab='.$_REQUEST['tab'].'&action=pg_delete_dict&id='.$item['id'].'"><font color="red">L&ouml;schen</font></a>'),
		);
        
        //Return the title contents
        return sprintf('%1$s %2$s', $item['de'], $this->row_actions($actions));
    }
	
	/****************************************/
    
    function column_cb($item)
	{        
    }
    
    function get_columns()
	{   
        return $this->column_titles;
    }
    
    function get_sortable_columns() 
	{      
        return $this->sortable_columns;
    }
    
    function get_bulk_actions() 
	{
		return array();
    }
    
    function process_bulk_action() 
	{   
    }
    
    function prepare_items() 
	{
        $per_page = 10;
       
        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, array(), $sortable);
        $this->process_bulk_action();
        
        $data = $this->table_data;
        
        function usort_reorder($a,$b)
		{
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'Deutsch';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; 
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order==='asc') ? $result : -$result;
        }
        usort($data, 'usort_reorder');
       
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,   
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }
  
}



