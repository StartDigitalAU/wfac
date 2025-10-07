<?php

class HumaanTableFactory
{

    public $wpdb;

    public $wp_table_name;

    public $visible_columns = array();

    public $filters = array();

    public $columns = array(
        /*  array(
            'name'      => 'course_id', // column key
            'label'     => 'Course ID', // column name
            'sql'       => "bigint(20) unsigned NOT NULL", // insert SQL
            'form_type' => 'select', // HTML form type (plain|input_text|select)
            'options'   => 'getCourseOptionHTML', // options for 'select' form type
            'wp_col'    => array(
                'type'          => 'string',
                'select_sql'    => "(SELECT post_title FROM wp_posts WHERE ID = course_id) AS course_name",
                'where_sql'     => "(SELECT post_title FROM wp_posts WHERE ID = course_id)"
            )
        ) */
    );

    public $admin_menu_icon = 'dashicons-clipboard';

    public $admin_menu_position = '60';

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->wp_table_name = $wpdb->prefix . $this->table_name;
    }

    public function init()
    {

        $this->parseRequests();

        // $this->initDB();
        $this->initMenuItems();
        $this->initScreenOptions();

        $this->initCustom();
    }

    public function initCustom()
    {

    }

    private function parseRequests()
    {

        // TODO: Fix this section, should only fire if within the Factory pages. Currently firing all the time.
        /*
        if (isset($_REQUEST['_wpnonce'])) {

            $url = esc_url_raw(remove_query_arg(array(
                '_wpnonce',
                '_wp_http_referer'
            )));

            wp_redirect($url);
        }
        */
    }

    public function initDB()
    {

        if ($this->wpdb->get_var("SHOW TABLES LIKE '{$this->wp_table_name}'") != $this->wp_table_name) {

            $sql_columns = '';
            foreach ($this->columns as $column) {
                if (isset($column['sql']) && !empty($column['sql'])) {
                    $sql_columns .= "{$column['name']} {$column['sql']}, ";
                }
            }

            $sql = "CREATE TABLE {$this->wp_table_name} (
                  {$sql_columns}
                  PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            $this->wpdb->query($sql);
        }
    }

    private function initMenuItems()
    {

        add_action('admin_menu', array($this, 'addMenuItems' ));
    }

    private function initScreenOptions()
    {

        add_filter('set-screen-option', array($this, 'setScreenOptions'), 10, 3);
    }

    public function addMenuItems()
    {
        //Add as menu
        $hook = add_menu_page(
            $this->name,
            $this->name,
            $this->table_name . '_list',
            $this->table_name . '_list',
            array($this, 'renderList'),
            $this->admin_menu_icon,
            $this->admin_menu_position
        );
        add_action("load-$hook", array($this, 'addOptions'));

        //View trash
        $hook = add_submenu_page(
            null,
            'View Trash',
            'View Trash',
            $this->table_name . '_list_trash',
            $this->table_name . '_list_trash',
            array($this,'renderTrash')
        );
        add_action("load-$hook", array($this, 'addOptions'));

        //New entry
        add_submenu_page(
            null,
            'New Entry',
            'New Entry',
            $this->table_name . '_new',
            $this->table_name . '_new',
            array($this, 'renderNewEntry')
        );

        //View entry
        add_submenu_page(
            null,
            'View Entry',
            'View Entry',
            $this->table_name . '_view',
            $this->table_name . '_view',
            array($this, 'renderEntry')
        );
    }

    public function addOptions()
    {

        $option = 'per_page';

        $args = array(
            'label' => $this->name . ' per page',
            'default' => 10,
            'option' => $this->table_name . '_per_page'
        );
        add_screen_option($option, $args);

        $this->createListTable();
    }

    public function setScreenOptions($status, $option, $value)
    {

        if ($this->table_name . '_per_page' == $option) {
            return $value;
        }

        return $status;
    }

    public function createListTable()
    {

        foreach ($this->columns as $column) {

            if (isset($column['wp_col']) && !empty($column['wp_col'])) {

                $visible_column = array(
                    'name'      => $column['name'],
                    'label'     => $column['label'],
                    'type'      => $column['wp_col']['type']
                );

                if (isset($column['wp_col']['select_sql'])) {
                    $visible_column['select_sql'] = $column['wp_col']['select_sql'];
                }
                if (isset($column['wp_col']['where_sql'])) {
                    $visible_column['where_sql'] = $column['wp_col']['where_sql'];
                }

                $this->visible_columns[] = $visible_column;
            }
        }

        $filters_html = $this->getFiltersHTML();

        $this->list_table = new HumaanListTable(array(
            'wp_table_name'     => $this->wp_table_name,
            'namespace'         => $this->table_name,
            'style_rules'       => '',
            'visible_columns'   => $this->visible_columns,
            'filters_html'      => $filters_html
        ));
    }

    public function fetchEntries($where = array())
    {

        $sql = "SELECT
                *
            FROM " . $this->wp_table_name;

        $first = true;
        if (!empty($where)) {
            foreach ($where as $string) {

                if ($first) {

                    $sql .= " WHERE " . $string;
                    $first = false;
                }
                else {

                    $sql .= " AND " . $string;
                }
            }
        }

        $result = $this->wpdb->get_results($sql);

        return $result;
    }

    public function fetchEntry($id)
    {

        $sql = "SELECT
                *
            FROM " . $this->wp_table_name . "
            WHERE
                id='" . addslashes($id) . "'
            ";

        $result = $this->wpdb->get_row($sql);

        return $result;
    }

    public function createEntry()
    {

        // TODO: Automate this?
    }

    public function updateEntry( $entry_id )
    {

        // TODO: Automate this?
    }

    public function trashSelectedEntries( $entry_ids )
    {

        if (count($entry_ids) == 0) {
            return;
        }

        $ids = array();
        foreach ($entry_ids as $entry_id) {
            $ids[] = addslashes($entry_id);
        }

        $sql = 'UPDATE ' . $this->wp_table_name . ' SET trashed = 1 WHERE id IN (' . implode( ',', $ids ) . ')';

        $this->wpdb->query($sql);

        echo '<script>window.location = "' . admin_url( 'admin.php?page=' . $this->table_name . '_list&orderby=created_at&order=desc&message=trashed' ) . '";</script>';

    }

    public function restoreSelectedEntries( $entry_ids )
    {

        if (count($entry_ids) == 0) {
            return;
        }

        $ids = array();
        foreach( $entry_ids as $entry_id ){
            $ids[] = addslashes( $entry_id );
        }

        $sql = 'UPDATE ' . $this->wp_table_name . ' SET trashed = 0 WHERE id IN (' . implode( ',', $ids ) . ')';

        $this->wpdb->query($sql);

        echo '<script>window.location = "' . admin_url( 'admin.php?page=' . $this->table_name . '_list_trash&orderby=created_at&order=desc&message=restored' ) . '";</script>';

    }

    public function deleteSelectedEntries( $entry_ids )
    {

        if (count($entry_ids) == 0) {
            return;
        }

        $ids = array();
        foreach( $entry_ids as $entry_id ){
            $ids[] = addslashes( $entry_id );
        }

        //Fetch them all first
        $sql = "SELECT * FROM " . $this->wp_table_name . " WHERE id IN (" . implode( ',', $ids ) . ")";

        $results = $this->wpdb->get_results($sql);

        if ( $results ){

            foreach( $results as $result ){

                $entry = json_decode( $result->data, true );

                foreach( $entry as $key => $value ){

                    $column = ifne( $this->columns, $key, array() );

                    $is_file = ( ifne( $column, 'type', 'tinytext' ) == 'file' );

                    if ( $is_file ){

                        $meta = json_decode( stripslashes( $value ), true );

                        if ( file_exists( $this->uploads_dir . ifne( $meta, 'token' ) ) ){
                            //echo 'willdelete' . $this->uploads_dir . ifne($meta, 'token');
                            unlink( $this->uploads_dir . ifne( $meta, 'token' ) );
                        }

                    }

                }

            }

        }

        $sql = 'DELETE FROM ' . $this->wp_table_name . ' WHERE id IN (' . implode( ',', $ids ) . ')';

        $this->wpdb->query($sql);

        echo '<script>window.location = "' . admin_url( 'admin.php?page=' . $this->table_name . '_list_trash&orderby=created_at&order=desc&message=restored' ) . '";</script>';

    }

    public function trashEntry( $entry_id )
    {

        global $wpdb;

        $sql = 'UPDATE ' . $this->wp_table_name . ' SET trashed = 1 WHERE id = ' . addslashes( $entry_id );

        $wpdb->query($sql);

        echo '<script>window.location = "' . admin_url( 'admin.php?page=' . $this->table_name . '_list&orderby=created_at&order=desc&message=trashed' ) . '";</script>';
    }

    public function restoreEntry( $entry_id )
    {

        global $wpdb;

        $sql = 'UPDATE ' . $this->wp_table_name . ' SET trashed = 0 WHERE id = ' . addslashes( $entry_id );

        $wpdb->query($sql);

        echo '<script>window.location = "' . admin_url( 'admin.php?page=' . $this->table_name . '_list&orderby=created_at&order=desc&message=restored' ) . '";</script>';

    }

    public function deleteEntry( $entry_id )
    {

        global $wpdb;

        //Now delete
        $sql = 'DELETE FROM ' . $this->wp_table_name . ' WHERE id = ' . addslashes( $entry_id );

        $wpdb->query($sql);

        echo '<script>window.location = "' . admin_url( 'admin.php?page=' . $this->table_name . '_list&orderby=created_at&order=desc&message=deleted' ) . '";</script>';

    }

    /**
     * Render List view
     *
     */
    public function renderList($is_trash = false)
    {

        if (isset($_GET[$this->table_name . '_entry'])) {

            if (isset( $_GET['restore'])) {
                $this->restoreSelectedEntries($_GET[$this->table_name . '_entry']);
                return;
            }

            if (isset( $_GET['trash'])){
                $this->trashSelectedEntries($_GET[$this->table_name . '_entry']);
                return;
            }

            if (isset( $_GET['delete'])){
                $this->deleteSelectedEntries($_GET[$this->table_name . '_entry']);
                return;
            }
        }

        if ($is_trash) {

            $this->list_table->view_mode = 'trash';
        }

        if (!ifne($_GET, 'orderby', null)) {

            $_GET['orderby'] = 'created_at';

            if (!ifne( $_GET, 'order', null)) {
                $_GET['order'] = 'desc';
            }
        }

        ?>
        <div class="wrap">

            <h2><?php echo $this->name ?></h2>

            <ul class="subsubsub">
                <li class="all">
                    <a<?php if (!$is_trash) { echo ' class="current"'; } ?> href="?page=<?php echo $this->table_name . '_list' ?>&amp;view=all&amp;orderby=created_at&amp;order=desc">All</a> |
                </li>
                <li class="">
                    <a<?php if ($is_trash) { echo ' class="current"'; } ?> href="?page=<?php echo $this->table_name . '_list_trash' ?>&amp;view=trash&amp;orderby=created_at&amp;order=desc">Trash</a>
                </li>
            </ul>

            <?php $this->list_table->prepare_items(); ?>

            <form method="get">

                <input type="hidden" name="page" value="<?php echo $this->table_name . '_list' ?>" />

                <?php $this->list_table->search_box('Search', 'search_id'); ?>

                <?php $this->list_table->display(); ?>

                <?php if ($is_trash) { ?>
                    <input class="button" type="submit" name="restore" value="Restore Selected" />
                    <input class="button" type="submit" name="delete" value="Delete Selected" />
                <?php } else { ?>
                    <input class="button" type="submit" name="trash" value="Trash Selected" />
                <?php } ?>

            </form>

        </div>
        <?php
    }

    /**
     * Render Trash view
     *
     */
    public function renderTrash()
    {

        $this->renderList(true);
    }

    /**
     * Render New view
     *
     */
    public function renderNewEntry()
    {

        $message = '';

        $entry_created = false;

        if (isset($_POST['create'])) {

            if ($this->createEntry()) {
                $entry_created = true;
                $message = 'Entry created.';
            }
            else {
                $message = 'Failed to create entry.';
            }
        }

        //Styles
        ?>
        <style type="text/css">

            span.label {display: block; font-weight: bold;}

            div.field {margin: 0 0 10px; border-bottom: 1px solid #eee; padding: 0 0 10px;}

            form.buttons { float: left; margin-right: 10px; }

            form.buttons input,
            a.button {
                display: block;
                float: left;
                cursor: pointer;
                font-family: sans-serif;
                margin-left: 4px;
                padding: 3px 8px;
                position: relative;
                top: -3px;
                text-decoration: none;
                font-size: 12px;
                border: 0 none;
                background: #f1f1f1;
                color: #21759b;
                margin: 10px 4px 20px;
                line-height: 15px;
                padding: 3px 10px;
                white-space: nowrap;
                -webkit-border-radius: 10px;
            }

            form.buttons input:hover {
                color: #d54e21;
            }

            div.clear { clear: both; }

        </style>

        <div class="wrap">
            <h1>
                <?php echo h($this->name); ?> / New
                <a href="<?php echo admin_url( 'admin.php?page=' . $this->table_name . '_list' ); ?>" class="page-title-action">Return to List</a>
            </h1>

            <?php if (!empty($message)) { ?>
                <div id="message" class="updated notice notice-success">
                    <p><?php echo $message; ?></p>
                </div>
            <?php } ?>

            <?php
            if ($entry_created) {
                return;
            }
            ?>

            <form method="post" action="">

                <div id="poststuff">
                    <div class="postbox acf-postbox">
                        <div class="inside acf-fields -left">
                            <?php foreach ($this->columns as $column) { ?>
                                <?php

                                if (!isset($column['form_new'])) {
                                    continue;
                                }

                                $type       = '';
                                $html_fn    = '';

                                if (is_array($column['form_new'])) {

                                    $type       = $column['form_new']['type'];
                                    $html_fn    = isset($column['form_new']['html_fn']) ? $column['form_new']['html_fn'] : '';

                                    if (is_array($html_fn)) {
                                        $html_fn = array(
                                            $this,
                                            $html_fn[1]
                                        );
                                    }
                                }
                                else {

                                    $type = $column['form_new'];
                                }
                                ?>
                                <?php if ($type == 'plain') { ?>
                                    <div class="acf-field acf-field-text">
                                        <div class="acf-label">
                                            <label for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label>
                                        </div>
                                        <div class="acf-input">
                                            <div class="acf-input-wrap">
                                                <?php
                                                if (!empty($html_fn)) {

                                                    $value = '';
                                                    if (isset($_POST[$column['name']]) && !empty($_POST[$column['name']])) {
                                                        $value = $_POST[$column['name']];
                                                    }
                                                    echo call_user_func($html_fn, $column['name'], $value);
                                                }
                                                else {

                                                    echo $entry->{$column['name']};
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } elseif ($type == 'input_text') { ?>
                                    <div class="acf-field acf-field-text">
                                        <div class="acf-label">
                                            <label for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label>
                                        </div>
                                        <div class="acf-input">
                                            <div class="acf-input-wrap">
                                                <?php
                                                $value = '';
                                                if (isset($_POST[$column['name']]) && !empty($_POST[$column['name']])) {
                                                    $value = $_POST[$column['name']];
                                                }
                                                ?>
                                                <?php if (!empty($html_fn)) { ?>
                                                    <?php echo call_user_func($html_fn, $column['name'], $value); ?>
                                                <?php } else { ?>
                                                    <input type="text" id="<?php echo $column['name']; ?>" name="<?php echo $column['name']; ?>" value="<?php echo $value; ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } elseif ($type == 'input_hidden') { ?>
                                    <?php
                                    $value = '';
                                    if (isset($_POST[$column['name']]) && !empty($_POST[$column['name']])) {
                                        $value = $_POST[$column['name']];
                                    }
                                    ?>
                                    <input type="hidden" id="<?php echo $column['name']; ?>" name="<?php echo $column['name']; ?>" value="<?php echo $value; ?>">
                                <?php } elseif ($type == 'textarea') { ?>
                                    <div class="acf-field acf-field-text">
                                        <div class="acf-label">
                                            <label for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label>
                                        </div>
                                        <div class="acf-input">
                                            <div class="acf-input-wrap">
                                                <?php
                                                $value = '';
                                                if (isset($_POST[$column['name']]) && !empty($_POST[$column['name']])) {
                                                    $value = $_POST[$column['name']];
                                                }
                                                ?>
                                                <?php if (!empty($html_fn)) { ?>
                                                    <?php echo call_user_func($html_fn, $column['name'], $entry->{$column['name']}); ?>
                                                <?php } else { ?>
                                                    <textarea id="<?php echo $column['name']; ?>" name="<?php echo $column['name']; ?>"><?php echo $value; ?></textarea>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } elseif ($type == 'select') { ?>
                                    <div class="acf-field acf-field-select">
                                        <div class="acf-label">
                                            <label for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label>
                                        </div>
                                        <div class="acf-input">
                                            <?php
                                            $value = '';
                                            if (isset($_POST[$column['name']]) && !empty($_POST[$column['name']])) {
                                                $value = $_POST[$column['name']];
                                            }
                                            ?>
                                            <?php echo call_user_func($html_fn, $column['name'], $value); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>

                            <div class="acf-field acf-field-text">
                                <div class="acf-label">
                                </div>
                                <div class="acf-input">
                                    <div class="acf-input-wrap">
                                        <input class="button button-primary" type="submit" name="create" value="Create" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Render single Edit view
     *
     */
    public function renderEntry()
    {

        $message = '';

        if (isset($_POST['entry'])) {

            if (isset($_POST['update'])) {
                $this->updateEntry($_POST['entry']);
                $message = 'Entry updated.';
            }

            if (isset($_POST['trash'])) {
                $this->trashEntry($_POST['entry']);
                return;
            }

            if (isset($_POST['restore'])) {
                $this->restoreEntry($_POST['entry']);
                return;
            }

            if (isset($_POST['delete'])) {
                $this->deleteEntry($_POST['entry']);
                return;
            }
        }

        if (!(isset($_GET['entry']) && !empty($_GET['entry']))) {
            return;
        }

        $entry = $this->fetchEntry($_GET['entry']);

        //Styles
        ?>
        <style type="text/css">

            span.label {display: block; font-weight: bold;}

            div.field {margin: 0 0 10px; border-bottom: 1px solid #eee; padding: 0 0 10px;}

            form.buttons { float: left; margin-right: 10px; }

            form.buttons input,
            a.button {
                display: block;
                float: left;
                cursor: pointer;
                font-family: sans-serif;
                margin-left: 4px;
                padding: 3px 8px;
                position: relative;
                top: -3px;
                text-decoration: none;
                font-size: 12px;
                border: 0 none;
                background: #f1f1f1;
                color: #21759b;
                margin: 10px 4px 20px;
                line-height: 15px;
                padding: 3px 10px;
                white-space: nowrap;
                -webkit-border-radius: 10px;
            }

            form.buttons input:hover {
                color: #d54e21;
            }

            div.clear { clear: both; }

        </style>

        <div class="wrap">
            <h1>
                <?php echo h($this->name); ?> / View
                <a href="<?php echo admin_url( 'admin.php?page=' . $this->table_name . '_list' ); ?>" class="page-title-action">Return to List</a>
            </h1>

            <?php if (!empty($message)) { ?>
                <div id="message" class="updated notice notice-success">
                    <p><?php echo $message; ?></p>
                </div>
            <?php } ?>

            <form method="post" action="">

                <input type="hidden" name="entry" value="<?php echo $entry->id; ?>" />

                <div id="poststuff">
                    <div class="postbox acf-postbox">
                        <div class="inside acf-fields -left">
                            <?php foreach ($this->columns as $column) { ?>
                                <?php

                                if (!isset($column['form_edit'])) {
                                    continue;
                                }

                                $type       = '';
                                $html_fn    = '';

                                if (is_array($column['form_edit'])) {

                                    $type       = $column['form_edit']['type'];
                                    $html_fn    = isset($column['form_edit']['html_fn']) ? $column['form_edit']['html_fn'] : '';

                                    if (is_array($html_fn)) {
                                        $html_fn = array(
                                            $this,
                                            $html_fn[1]
                                        );
                                    }
                                }
                                else {

                                    $type = $column['form_edit'];
                                }

                                if (isset($entry->is_archived) && $entry->is_archived) {

                                    $type = 'plain';
                                }
                                ?>
                                <?php if ($type == 'plain') { ?>
                                    <div class="acf-field acf-field-text">
                                        <div class="acf-label">
                                            <label for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label>
                                        </div>
                                        <div class="acf-input">
                                            <div class="acf-input-wrap">
                                                <?php
                                                if (!empty($html_fn)) {

                                                    echo call_user_func($html_fn, $column['name'], $entry->{$column['name']});
                                                }
                                                else {

                                                    echo $entry->{$column['name']};
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } elseif ($type == 'input_text') { ?>
                                    <div class="acf-field acf-field-text">
                                        <div class="acf-label">
                                            <label for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label>
                                        </div>
                                        <div class="acf-input">
                                            <div class="acf-input-wrap">
                                                <?php if (!empty($html_fn)) { ?>
                                                    <?php echo call_user_func($html_fn, $column['name'], $entry->{$column['name']}); ?>
                                                <?php } else { ?>
                                                    <input type="text" id="<?php echo $column['name']; ?>" name="<?php echo $column['name']; ?>" value="<?php echo $entry->{$column['name']}; ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } elseif ($type == 'input_hidden') { ?>
                                    <input type="hidden" id="<?php echo $column['name']; ?>" name="<?php echo $column['name']; ?>" value="<?php echo $entry->{$column['name']}; ?>">
                                <?php } elseif ($type == 'textarea') { ?>
                                    <div class="acf-field acf-field-text">
                                        <div class="acf-label">
                                            <label for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label>
                                        </div>
                                        <div class="acf-input">
                                            <div class="acf-input-wrap">
                                                <?php if (!empty($html_fn)) { ?>
                                                    <?php echo call_user_func($html_fn, $column['name'], $entry->{$column['name']}); ?>
                                                <?php } else { ?>
                                                    <textarea id="<?php echo $column['name']; ?>" name="<?php echo $column['name']; ?>"><?php echo $entry->{$column['name']}; ?></textarea>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } elseif ($type == 'select') { ?>
                                    <div class="acf-field acf-field-select">
                                        <div class="acf-label">
                                            <label for="<?php echo $column['name']; ?>"><?php echo $column['label']; ?></label>
                                        </div>
                                        <div class="acf-input">
                                            <?php echo call_user_func($html_fn, $column['name'], $entry->{$column['name']}); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>

                            <?php echo $this->afterFormHTML($_GET['entry']); ?>

                            <?php if (!isset($entry->is_archived) || !$entry->is_archived) { ?>
                                <div class="acf-field acf-field-text">
                                    <div class="acf-label">
                                    </div>
                                    <div class="acf-input">
                                        <div class="acf-input-wrap">
                                            <?php if ($entry->trashed == 0) { ?>
                                                <input class="button" type="submit" name="trash" value="Trash" />
                                                <input class="button button-primary" type="submit" name="update" value="Update" />
                                            <?php } else { ?>
                                                <input class="button" type="submit" name="restore" value="Restore" />
                                                <input class="button" type="submit" name="delete" value="Delete Permanently" />
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Get the HTML for the list filters
     *
     * @return string
     */
    public function getFiltersHTML()
    {

        return '';
    }

    /**
     * Get HTML to display after the form
     *
     * @return string
     */
    public function afterFormHTML($entry_id)
    {

        return '';
    }
}