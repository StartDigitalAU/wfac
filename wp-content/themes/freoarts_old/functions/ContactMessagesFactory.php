<?php

class ContactMessagesFactory extends HumaanTableFactory
{

    public $name = 'Contact Messages';

    public $table_name = 'contact_messages';

    public $admin_menu_icon = 'dashicons-clipboard';

    public $admin_menu_position = '300';

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
        array(
            'name'  => 'id',
            'label' => 'ID',
            'sql'   => "bigint(20) unsigned NOT NULL auto_increment",
        ),
        array(
            'name'      => 'full_name',
            'label'     => 'Full Name',
            'wp_col'    => array(
                'type'          => 'string',
                'select_sql'    => "CONCAT(last_name, ', ', first_name) AS full_name",
                'where_sql'     => "CONCAT(last_name, ', ', first_name)"
            )
        ),
        array(
            'name'      => 'first_name',
            'label'     => 'First Name',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'last_name',
            'label'     => 'Last Name',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'contact_number',
            'label'     => 'Contact Number',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'email',
            'label'     => 'Email Address',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_edit' => 'input_text',
            'wp_col'    => array(
                'type'          => 'string'
            )
        ),
        array(
            'name'      => 'message',
            'label'     => 'Message',
            'sql'       => "text NOT NULL default ''",
            'form_edit' => 'textarea'
        ),
        array(
            'name'      => 'created_at',
            'label'     => 'Created At',
            'sql'       => "datetime NOT NULL default '0000-00-00 00:00:00'",
            'form_edit' => 'datetime'
        ),
        array(
            'name'      => 'updated_at',
            'label'     => 'Updated At',
            'sql'       => "datetime NOT NULL default '0000-00-00 00:00:00'"
        ),
        array(
            'name'      => 'trashed',
            'label'     => 'Trashed',
            'sql'       => "tinyint(1) NOT NULL DEFAULT 0"
        ),
    );

    public function updateEntry( $entry_id )
    {

        $this->wpdb->update(
            $this->wp_table_name,
            array(
                'first_name'                => $_POST['first_name'],
                'last_name'                 => $_POST['last_name'],
                'contact_number'            => $_POST['contact_number'],
                'email'                     => $_POST['email'],
                'message'                   => $_POST['message'],
                'updated_at'                => date('Y-m-d H:i:s')
            ),
            array(
                'id' => $entry_id
            )
        );
    }

    /**
     * Get the HTML for the list filters
     *
     * @return string
     */
    public function getFiltersHTML()
    {

        $html = '';

        return $html;
    }
}