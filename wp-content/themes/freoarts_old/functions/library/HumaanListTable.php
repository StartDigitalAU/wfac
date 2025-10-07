<?php

if (!class_exists('WP_List_Table')) {

    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class HumaanListTable extends WP_List_Table
{

    public $view_mode = 'all'; //or 'trash'
    public $namespace;
    public $wp_table_name;
    public $style_rules;
    public $visible_columns = array();
    public $filters_html;

    function __construct($a)
    {

        foreach ($a as $key => $value) {
            $this->$key = $value;
        }

        parent::__construct(array(
            'singular'  => $this->namespace . '_entry',     //singular name of the listed records
            'plural'    => $this->namespace . '_entries',   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ));
        add_action('admin_head', array(&$this, 'admin_header' ));
    }

    function admin_header()
    {

        echo '<style type="text/css">';
        echo $this->styles_rules;
        echo '</style>';
    }

    function no_items()
    {

        _e('No entries found.');
    }

    function column_default($item, $column_name)
    {

        $columns = array();
        foreach( $this->visible_columns as $column ){
            $columns[] = $column['name'];
        }

        $column_types = array();
        foreach( $this->visible_columns as $visible_column ){
            $column_types[ $visible_column['name'] ] = $visible_column['type'];
        }

        if ( in_array( $column_name, $columns ) ){

            $val = stripslashes( $item->{$column_name} );

            if ( ifne( $column_types, $column_name, 'tinytext' ) == 'file' ) {

                if ( $val != '' ){

                    $meta = json_decode( $val, true );
                    $val = '<a href="' . admin_url( 'admin.php?page=' . $this->namespace . '_download_file' ) . '&token=' . urlencode( $column_name . '-' . $item->id . '-' . hifne( $meta, 'token' ) ) . '">' . hifne( $meta, 'filename' ) . '</a>';

                } else {
                    //Just print the empty string
                }

            } elseif ( ifne( $column_types, $column_name, 'tinytext' ) == 'url' ){

                if ( $val != '' ){

                    $printable = $val;

                    if (strlen( $val ) > 30 ){
                        $printable = h( substr( $val, 0, 29 ) ) . '&hellip;';
                    } else {
                        $printable = h( $val );
                    }

                    if ( ( strpos( $val, 'http://' ) !== 0 ) && ( strpos( $val, 'https://' ) !== 0 ) ){
                        $url = 'http://' . h( $val );
                    } else {
                        $url = h( $val );
                    }

                    $val = '<a target="_blank" href="' . $url . '">' . $printable . '</a>';

                }

            } else {

                if (strlen( $val ) > 30 ){
                    $val = h( substr( $val, 0, 29 ) ) . '&hellip;';
                } else {
                    $val = h( $val );
                }
            }

            return $val;

        } else {

            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }

    }

    function get_sortable_columns()
    {

        $sortable_columns = array();
        $sortable_columns[ 'created_at' ] = array( 'created_at', false );
        foreach( $this->visible_columns as $column ){
            if ( ifne( $column, 'type', 'tinytext' ) != 'file' ){
                $sortable_columns[ $column['name'] ] = array( $column['name'], false );
            }
        }

        return $sortable_columns;
    }

    function get_columns()
    {
        $columns = array(
            'cb'        	=> '<input type="checkbox" />',
            'created_at' 	=> __( 'Created At', 'humaanlisttable' )
        );

        foreach( $this->visible_columns as $column ){
            $columns[ $column['name'] ] = ifne( $column, 'label', $column['name'] );
        }

        return $columns;
    }

    protected function extra_tablenav( $which ) {

        if ('top' === $which) {

            echo $this->filters_html;
        }
    }

    function usort_reorder( $a, $b )
    {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'created_at';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }

    function column_created_at($item)
    {

        $actions = array(
            'edit'      => sprintf('<a href="?page=' . $this->namespace . '_view&entry=%s">View Entry</a>', $item->id)
        );

        return sprintf('%1$s %2$s', $item->created_at, $this->row_actions($actions) );

    }

    function get_bulk_actions()
    {
        $actions = array(
            // 'delete'    => 'Delete'
        );
        return $actions;
    }

    function column_cb($item)
    {

        return sprintf(
            '<input type="checkbox" name="' . $this->namespace . '_entry[]" value="%s" />', $item->id
        );
    }

    function prepare_items()
    {

        global $wpdb, $_wp_column_headers;

        $screen = get_current_screen();
        $user_id = get_current_user_id();

        // Get the items Per Page filter
        $screen_option = $screen->get_option('per_page', 'option');
        $per_page = get_user_meta($user_id, $screen_option, true);
        if (empty($per_page)) {
            $per_page = 10;
        }

        $where_search = '';

        // Are we viewing the Trashed list?
        $trashed_status = ( $this->view_mode == 'trash' ? 1 : 0 );

        // Parse Search query
        if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {

            $search = $_REQUEST['s'];
            $_GET['s'] = $_REQUEST['s'];

            $where_items = array();

            foreach ( $this->visible_columns as $column ) {

                // Is there a custom search SQL query?
                if (isset($column['where_sql'])) {

                    $where_items[] = ' ' . $column['where_sql'] . " LIKE '%{$search}%'";
                }
                // Or just search the column name?
                else {

                    $where_items[] = ' ' . $column['name'] . " LIKE '%{$search}%'";
                }
            }

            $where_search .= " AND (" . implode(" OR", $where_items ) . ")";
        }

        // Parse Filter query
        if (isset($_REQUEST['filters']) && !empty($_REQUEST['filters'])) {

            $filters = $_REQUEST['filters'];

            foreach ($filters as $filter_key => $filter_value) {

                if (!empty($filter_value)) {
                    $where_search .= " AND ({$filter_key} = {$filter_value})";
                }
            }
        }

        $fields = array();

        // Get the fields we are selecting
        foreach ( $this->visible_columns as $column ) {

            if (isset($column['select_sql'])) {

                $fields[] = $column['select_sql'];
            }
            else {

                $fields[] = $column['name'];
            }
        }

        $query = "SELECT
		        id,
			    created_at,
                " . implode(', ', $fields) . "
            FROM " . $this->wp_table_name . "
			WHERE (trashed = $trashed_status)
			{$where_search}";

        // Determine column sorting by and the direction
        $orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : '';

        if (!empty($orderby) & !empty($order)) {

            $query .= ' ORDER BY '.$orderby.' '.$order;
        }

        // Set pagination
        $totalitems = $wpdb->query($query);
        $paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';

        if (empty($paged) || !is_numeric($paged) || $paged<=0) {

            $paged = 1;
        }

        $totalpages = 1;
        if ($totalitems) {
            $totalpages = ceil($totalitems / $per_page);
        }

        if (!empty($paged) && !empty($per_page)) {

            $offset = ($paged - 1) * $per_page;
            $query .= ' LIMIT ' . (int)$offset . ',' . (int)$per_page;
        }

        $this->set_pagination_args(array(
            "total_items"   => $totalitems,
            "total_pages"   => $totalpages,
            "per_page"      => $per_page,
        ));

        $columns = $this->get_columns();
        $_wp_column_headers[$screen->id] = $columns;

        $this->items = $wpdb->get_results($query);
    }

    public function single_row( $item )
    {
        static $row_class = '';
        $row_class = ($row_class == '' ? ' class="alternate"' : '');

        echo '<tr' . $row_class . '>';
        $this->single_row_columns( $item );
        echo '</tr>';
    }
}