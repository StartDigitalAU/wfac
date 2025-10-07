<?php

/**
 * Class PrintAwardsFactory
 *
 * This is being used as a way in which to interact with the print awards via the admin
 * The object classes are PrintAwards, PrintAwardsSubmission and PrintAwardsSubmissionImage
 *
 */

class PrintAwardsFactory extends HumaanTableFactory
{

    public $name = 'Print Awards';

    public $table_name = 'print_awards_submissions';

    public $admin_menu_icon = 'dashicons-media-interactive';

    public $columns = array(
        array(
            'name'  => 'id',
            'label' => 'ID',
            'form_edit' => 'plain',
        ),
        array(
            'name'  => 'user_id',
            'label' => 'User ID',
            'form_edit' => 'plain',
            'wp_col'    => array(
                'type'          => 'string',
                'select_sql'    => "(SELECT user_email FROM wp_users WHERE ID = user_id) AS user_id",
                'where_sql'     => "(SELECT user_email FROM wp_users WHERE ID = user_id)"
            )
        ),
        array(
            'name'  => 'awards_year',
            'label' => 'Awards Year',
            'wp_col'    => array(
                'type'          => 'string'
            ),
            'form_edit' => 'plain',
        ),
        array(
            'name'  => 'status',
            'label' => 'Status',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'created_at',
            'label' => 'Created At',
            'form_edit' => 'plain',
        ),
        array(
            'name'  => 'terms_accepted',
            'label' => 'Terms Accepted?',
            'form_edit' => 'plain',
        ),
        array(
            'name'  => 'artist_first_name',
            'label' => 'Artist First Name',
            'wp_col'    => array(
                'type'          => 'string'
            ),
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'artist_surname',
            'label' => 'Artist Surname',
            'wp_col'    => array(
                'type'          => 'string'
            ),
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'aboriginal_torres_strait_islander',
            'label' => 'Aborginal or Torres Strait Islander?',
            'form_edit' => 'plain',
        ),
        array(
            'name'  => 'artist_agent',
            'label' => 'Artist Agent',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'artist_email',
            'label' => 'Artist Email',
            'wp_col'    => array(
                'type'          => 'string'
            ),
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'gender',
            'label' => 'Gender',
            'wp_col'    => array(
                'type'          => 'string'
            ),
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'address',
            'label' => 'Address',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'suburb',
            'label' => 'Suburb',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'state',
            'label' => 'State',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'postcode',
            'label' => 'Postcode',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'mobile',
            'label' => 'Mobile',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'home_phone',
            'label' => 'Home Phone',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'work_phone',
            'label' => 'Work Phone',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'title_of_work',
            'label' => 'Title of Work',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'year_made',
            'label' => 'Year Made',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'height',
            'label' => 'Height',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'width',
            'label' => 'Width',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'depth',
            'label' => 'Depth',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'medium',
            'label' => 'Medium',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'printer',
            'label' => 'Printer',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'number_of_works_in_edition',
            'label' => 'Number of Works in Edition',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'edition_number_of_work',
            'label' => 'Edition Number of Work',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'number_of_works_for_sale',
            'label' => 'Number of Works for Sale',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'price',
            'label' => 'Price',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'gst',
            'label' => 'GST',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'abn',
            'label' => 'ABN',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'notes_about_work',
            'label' => 'Notes About Work',
            'form_edit' => 'input_text',
        ),
        array(
            'name'  => 'paid',
            'label' => 'Paid',
            'wp_col'    => array(
                'type'          => 'string'
            ),
            'form_new' => array(
                'type'          => 'select',
                'html_fn'       => array('this', 'getPaidOptionHTML')
            ),
            'form_edit' => array(
                'type'          => 'select',
                'html_fn'       => array('this', 'getPaidOptionHTML')
            ),
        ),
        array(
            'name'  => 'order_id',
            'label' => 'Order ID',
            'wp_col'    => array(
                'type'          => 'string'
            ),
            'form_edit' => array(
                'type'          => 'plain',
                'html_fn'       => array('this', 'getOrderIDHTML')
            ),
        ),
        array(
            'name'  => 'submitted',
            'label' => 'Submitted',
            'form_edit' => array(
                'type'          => 'plain',
                'html_fn'       => array('this', 'getSubmittedHTML')
            ),
        ),
    );

    /**
     * Render Select Option HTML for paid state
     *
     * @param bool|false $is_child
     * @return string
     */
    public function getPaidOptionHTML($name, $value = false)
    {

        $html = '<select id="' . $name . '" name="' . $name . '">';

        $selected = ($value == 'no') ? ' selected="selected"' : '';
        $html .= '<option value="no"' . $selected . '>No</option>';

        $selected = ($value == 'yes') ? ' selected="selected"' : '';
        $html .= '<option value="yes"' . $selected . '>Yes</option>';

        $html .= '</select>';

        if ($value == 'no') {
            $html .= '<small>Changing this value to Yes assumes that payment has been taken offline, and no online Order ID is required.</small>';
        }

        return $html;
    }

    /**
     * Render Select Option HTML for paid state
     *
     * @param bool|false $is_child
     * @return string
     */
    public function getOrderIDHTML($name, $value = false)
    {

        if (!empty($value)) {
            $html = '#' . $value . '<br><a href="' . get_edit_post_link($value) . '">View Order</a>';
        }
        else {
            $html = 'N/A';
        }

        return $html;
    }

    /**
     * Render Select Option HTML for paid state
     *
     * @param bool|false $is_child
     * @return string
     */
    public function getSubmittedHTML($name, $value = false)
    {

        if (!empty($value)) {
            $html = $value;
        }
        else {
            $html = 'Submission has not yet been paid for.';
        }

        return $html;
    }

    public function initCustom()
    {

        add_action('admin_menu', array($this, 'addPrintAwardMenuItems' ));

        add_action('admin_post_' . $this->table_name . '_complete.csv', array($this, 'downloadCSV'));
    }

    public function addPrintAwardMenuItems()
    {

        //Print
        $hook = add_submenu_page(
            null,
            'Print',
            'Print',
            $this->table_name . '_print',
            $this->table_name . '_print',
            array($this, 'renderPrint')
        );

        //Download images
        $hook = add_submenu_page(
            null,
            'Download Images',
            'Download Images',
            'manage_options',
            $this->table_name . '_download_images',
            array($this, 'view_image_archives')
        );
        // add_action("load-$hook", array($this, 'view_image_archives'));

        //Download CSV
        /*
        $hook = add_submenu_page(
            null,
            'Download CSV',
            'Download CSV',
            'manage_options',
            $this->table_name . '_download_csv',
            array($this, 'downloadCSV')
        );
        add_action("load-$hook", array($this, 'downloadCSV'));
        */
    }

    /**
     * Update an existing entry
     *
     * @param $entry_id
     * @return false|int
     */
    public function updateEntry( $entry_id )
    {

        $submission = array(
            'status'                => filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING),
            'artist_first_name'     => filter_input(INPUT_POST, 'artist_first_name', FILTER_SANITIZE_STRING),
            'artist_surname'        => filter_input(INPUT_POST, 'artist_surname', FILTER_SANITIZE_STRING),
            'artist_agent'          => filter_input(INPUT_POST, 'artist_agent', FILTER_SANITIZE_STRING),
            'artist_email'          => filter_input(INPUT_POST, 'artist_email', FILTER_SANITIZE_STRING),
            'address'               => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING),
            'suburb'                => filter_input(INPUT_POST, 'suburb', FILTER_SANITIZE_STRING),
            'state'                 => filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING),
            'postcode'              => filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_STRING),
            'mobile'                => filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING),
            'home_phone'            => filter_input(INPUT_POST, 'home_phone', FILTER_SANITIZE_STRING),
            'work_phone'            => filter_input(INPUT_POST, 'work_phone', FILTER_SANITIZE_STRING),
            'title_of_work'         => filter_input(INPUT_POST, 'title_of_work', FILTER_SANITIZE_STRING),
            'year_made'             => filter_input(INPUT_POST, 'year_made', FILTER_SANITIZE_STRING),
            'height'                => filter_input(INPUT_POST, 'height', FILTER_SANITIZE_STRING),
            'width'                 => filter_input(INPUT_POST, 'width', FILTER_SANITIZE_STRING),
            'depth'                 => filter_input(INPUT_POST, 'depth', FILTER_SANITIZE_STRING),
            'medium'                => filter_input(INPUT_POST, 'medium', FILTER_SANITIZE_STRING),
            'printer'               => filter_input(INPUT_POST, 'printer', FILTER_SANITIZE_STRING),
            'number_of_works_in_edition'        => filter_input(INPUT_POST, 'number_of_works_in_edition', FILTER_SANITIZE_STRING),
            'edition_number_of_work'            => filter_input(INPUT_POST, 'edition_number_of_work', FILTER_SANITIZE_STRING),
            'number_of_works_for_sale'          => filter_input(INPUT_POST, 'number_of_works_for_sale', FILTER_SANITIZE_STRING),
            'price'                 => filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING),
            'gst'                   => filter_input(INPUT_POST, 'gst', FILTER_SANITIZE_STRING),
            'abn'                   => filter_input(INPUT_POST, 'abn', FILTER_SANITIZE_STRING),
            'notes_about_work'      => filter_input(INPUT_POST, 'notes_about_work', FILTER_SANITIZE_STRING),
            'paid'                  => filter_input(INPUT_POST, 'paid', FILTER_SANITIZE_STRING),
        );

        $paid = $this->wpdb->get_var("SELECT paid FROM " . $this->wp_table_name . " WHERE id = '" . $entry_id . "'");
        if ($paid == 'no' && $submission['paid'] == 'yes') {
            $submission['submitted'] = date('Y-m-d H:i:s');
        }
        elseif ($paid == 'yes' && $submission['paid'] == 'no') {
            $submission['submitted'] = null;
        }

        // Save the image names
        if (isset($_POST['image_filename'])) {

            foreach ($_POST['image_filename'] as $key => $value) {

                $this->wpdb->update(
                    'wp_print_awards_submission_images',
                    array(
                        'filename' => $value
                    ),
                    array(
                        'id' => $key
                    )
                );
            }
        }

        // Update the print award data
        return $this->wpdb->update(
            $this->wp_table_name,
            $submission,
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
        ob_start();
        ?>
        <div class="alignleft actions">
            <a href="<?php echo admin_url( 'admin.php?page=' . $this->table_name . '_print' ); ?>" class="button" target="_blank">Print All</a>
            <a href="<?php echo admin_url( 'admin.php?page=' . $this->table_name . '_download_images' ); ?>" class="button" target="_blank">Download Images</a>
            <a href="<?php echo admin_url( 'admin-post.php?action=' . $this->table_name . '_complete.csv' ); ?>" class="button" target="_blank">Download CSV</a>
        </div>
        <?php
        $html = ob_get_clean();

        return $html;
    }

    public function afterFormHTML($entry_id) {

        $entry = $this->fetchEntry($entry_id);

        $submission = PrintAwardsSubmission::draftSubmission($entry->user_id);
        $submission->loadImages();
        ?>

        <div class="acf-field acf-field-text">
            <div class="acf-label">
                <label for="images">Images</label>
            </div>
            <div class="acf-input">
                <div class="acf-input-wrap">

                    <?php
                    foreach ($submission->images as $image) {
                        ?>
                        <a href="<?php echo $image->buildUrl(); ?>" title="View enlarged" target="_blank">
                            <img src="<?php echo $image->buildUrl() . '_thumb.jpg'; ?>" title="<?php echo h( $image->filename ) ?>" />
                        </a>
                        <input type="text" name="image_filename[<?php echo $image->id; ?>]" value="<?php echo $image->filename; ?>"/>
                        <?php
                    }
                    ?>

                </div>
            </div>
        </div>

        <div class="acf-field acf-field-text">
            <div class="acf-label">
                <label for="images">Print</label>
            </div>
            <div class="acf-input">
                <div class="acf-input-wrap">

                    <a href="<?php echo admin_url( 'admin.php?page=' . $this->table_name . '_print&entry=' . $entry_id ); ?>" title="Printable version" target="_blank">Click here to view printable version.</a>

                </div>
            </div>
        </div>

        <?php
    }

    public function renderPrint() {

        $entries = array();

        if (isset($_GET['entry']) && !empty($_GET['entry'])) {
            $entries[] = $this->fetchEntry($_GET['entry']);
        }
        else {
            $entries = $this->fetchEntries(array(
                "paid = 'yes'"
            ));
        }
        ?>

        <style type="text/css">

            html.wp-toolbar {
                padding-top: 0;
                background-color: #fff;
                height: auto;
            }

            .clearfix:before,
            .clearfix:after
            {
                content: '\0020';
                display: block;
                overflow: hidden;
                visibility: hidden;
                width: 0;
                height: 0; }
            .clearfix:after
            {
                clear: both;
            }

            #adminmenumain,
            #wpadminbar,
            .update-nag,
            #wpfooter
            {
                display: none;
            }

            #wpwrap {
                min-height: auto;
            }

            #wpcontent {
                padding-left: 0;
                margin-left: 0;
                height: auto;
            }

            #wpbody-content {
                padding-bottom: 0;
            }

            .printable {
                height: 100vh;
            }

            .printable img {
                max-height: 100px;
            }

            section {
                margin-bottom: 20px;
            }

            .column-left {
                float: left;
                width: 45%;
            }

            .column-right {
                float: right;
                width: 45%;
            }

            h2 {
                line-height: 1.5em;
                border-bottom: 1px solid #ccc;
            }

            .field-group {
                margin-bottom: 20px;
            }

            .field {

            }

            .field strong {
                display: inline-block;
                width: 40%;
            }

            .field span {

            }

        </style>

        <?php foreach ($entries as $entry) { ?>

            <div class="printable">

                <?php
                $submission = PrintAwardsSubmission::draftSubmission($entry->user_id);
                $submission->loadImages();
                ?>

                <section class="clearfix">
                    <h1>Fremantle Arts Centre Print Award</h1>
                </section>

                <section class="clearfix">

                    <h2>Application Details</h2>

                    <div class="column-left">

                        <div class="field-group">
                            <div class="field">
                                <strong>Application Number:</strong>
                                <span><?php echo $entry->id; ?></span>
                            </div>
                            <div class="field">
                                <strong>Application Date/Time:</strong>
                                <span><?php echo $entry->created_at; ?></span>
                            </div>
                        </div>

                        <div class="field-group">
                            <div class="field">
                                <strong>Artist Name:</strong>
                                <span><?php echo $entry->artist_first_name . ' ' . $entry->artist_surname; ?></span>
                            </div>
                            <div class="field">
                                <strong>Aborginal or Torres Strait Islander?:</strong>
                                <span><?php echo $entry->aboriginal_torres_strait_islander; ?></span>
                            </div>
                            <div class="field">
                                <strong>Agent:</strong>
                                <span><?php echo $entry->artist_agent; ?></span>
                            </div>
                        </div>

                        <div class="field-group">
                            <div class="field">
                                <strong>Postal Address:</strong>
                                <span><?php echo $entry->address; ?></span>
                            </div>
                            <div class="field">
                                <strong>Suburb:</strong>
                                <span><?php echo $entry->suburb; ?></span>
                            </div>
                            <div class="field">
                                <strong>State:</strong>
                                <span><?php echo $entry->state; ?></span>
                            </div>
                            <div class="field">
                                <strong>Postcode:</strong>
                                <span><?php echo $entry->postcode; ?></span>
                            </div>
                        </div>

                        <div class="field-group">
                            <div class="field">
                                <strong>Home Phone:</strong>
                                <span><?php echo $entry->home_phone; ?></span>
                            </div>
                            <div class="field">
                                <strong>Work Phone:</strong>
                                <span><?php echo $entry->work_phone; ?></span>
                            </div>
                            <div class="field">
                                <strong>Mobile:</strong>
                                <span><?php echo $entry->mobile; ?></span>
                            </div>
                        </div>

                        <div class="field-group">
                            <div class="field">
                                <strong>Email:</strong>
                                <span><?php echo $entry->artist_email; ?></span>
                            </div>
                        </div>

                        <div class="field-group">
                            <div class="field">
                                <strong>Has Paid:</strong>
                                <span><?php echo $entry->paid; ?></span>
                            </div>
                            <div class="field">
                                <strong>Notes:</strong>
                                <span><?php echo $entry->notes_about_work; ?></span>
                            </div>
                        </div>

                    </div>

                    <div class="column-right">

                        <div class="field-group">
                            <div class="field">
                                <strong>Title of Work:</strong>
                                <span><?php echo $entry->title_of_work; ?></span>
                            </div>
                            <div class="field">
                                <strong>Year:</strong>
                                <span><?php echo $entry->year_made; ?></span>
                            </div>
                        </div>

                        <div class="field-group">
                            <div class="field">
                                <strong>Height:</strong>
                                <span><?php echo $entry->height; ?></span>
                            </div>
                            <div class="field">
                                <strong>Width:</strong>
                                <span><?php echo $entry->width; ?></span>
                            </div>
                            <div class="field">
                                <strong>Depth:</strong>
                                <span><?php echo $entry->depth; ?></span>
                            </div>
                        </div>

                        <div class="field-group">
                            <div class="field">
                                <strong>Medium:</strong>
                                <span><?php echo $entry->medium; ?></span>
                            </div>
                            <div class="field">
                                <strong>Printer:</strong>
                                <span><?php echo $entry->printer; ?></span>
                            </div>
                        </div>

                        <div class="field-group">
                            <div class="field">
                                <strong>Number of works in the edition:</strong>
                                <span><?php echo $entry->number_of_works_in_edition; ?></span>
                            </div>
                            <div class="field">
                                <strong>Edition number of work:</strong>
                                <span><?php echo $entry->edition_number_of_work; ?></span>
                            </div>
                            <div class="field">
                                <strong>Number of works available for sale:</strong>
                                <span><?php echo $entry->number_of_works_for_sale; ?></span>
                            </div>
                            <div class="field">
                                <strong>Price:</strong>
                                <span>$<?php echo $entry->price; ?></span>
                            </div>
                            <div class="field">
                                <strong>GST:</strong>
                                <span><?php echo $entry->gst; ?></span>
                            </div>
                            <div class="field">
                                <strong>ABN:</strong>
                                <span><?php echo $entry->abn; ?></span>
                            </div>
                        </div>

                    </div>

                </section>

                <section class="clearfix">

                    <h2>Uploaded Images</h2>

                    <?php
                    foreach ($submission->images as $image) {
                        ?>
                        <a href="<?php echo $image->buildUrl(); ?>" title="View enlarged" target="_blank">
                            <img src="<?php echo $image->buildUrl() . '_thumb.jpg'; ?>" title="<?php echo h( $image->filename ) ?>" />
                        </a>
                        <?php
                    }
                    ?>

                </section>

            </div>
        <?php } ?>

        <?php
    }

    public function archiveImages()
    {

        // $zipfile = tempnam(WP_CONTENT_DIR . '/uploads/tmp' , 'zip');
        // $zip = new ZipArchive();
        // $zip->open($zipfile, ZipArchive::OVERWRITE);

        $entries = $this->fetchEntries(array(
            "paid = 'yes'"
        ));

        $orders_per_zip = 40;

        $i = 1;
        $file_number = 1;
        $file_required = true;

        foreach ($entries as $entry) {

            if ($file_required) {

                $file_required = false;

                $zipfile = WP_CONTENT_DIR . '/uploads/print-awards/archived/images-' . $file_number . '.zip';

                if (file_exists($zipfile)) {
                    unlink($zipfile);
                }

                $zip = new ZipArchive();
                $zip->open($zipfile, ZipArchive::CREATE);
            }

            $submission = PrintAwardsSubmission::draftSubmission($entry->user_id);
            $submission->loadImages();

            foreach ($submission->images as $image) {

                $sanitized_surname = preg_replace("/[^A-Za-z0-9\.]/", '', strtolower($entry->artist_surname));

                $file_path = WP_CONTENT_DIR . '/uploads/print-awards/'  .$image->awards_year . '/' . $image->token;
                $zip_path = 'print-awards/'  .$image->awards_year . '/' . $sanitized_surname . '_' . $entry->order_id . '/' . $image->filename;

                $zip->addFile(
                    $file_path,
                    $zip_path
                );
            }

            $i++;

            if ($i >= $orders_per_zip) {

                $zip->close();

                $i = 1;
                $file_number++;
                $file_required = true;
            }
        }

        // header('Content-Type: application/zip');
        // header('Content-Length: ' . filesize($zipfile));
        // header('Content-Disposition: attachment; filename="images.zip"');
        // readfile($zipfile);
        // unlink($zipfile);
    }

    function view_image_archives()
    {

        $dir = WP_CONTENT_DIR . '/uploads/print-awards/archived';
        $files = scandir($dir);
        ?>
        <div class="wrap">
            <h1>Archived Images</h1>
            <ul>
                <?php foreach ($files as $file) { ?>
                    <?php
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    ?>
                    <li>
                        <a href="<?php echo get_bloginfo('url') . '/wp-content/uploads/print-awards/archived/' . $file; ?>" title="<?php echo $file; ?>">
                            <?php echo $file; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php
    }

    public function downloadCSV()
    {

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=transaction-history.csv');
        header('Pragma: no-cache');

        $output = fopen('php://output', 'w');

        $headers = array();
        foreach ($this->columns as $column) {
            $headers[] = $column['label'];
        }

        fputcsv(
            $output,
            $headers
        );

        $entries = $this->fetchEntries(array(
            "paid = 'yes'"
        ));

        foreach ($entries as $entry) {

            $row = array();
            foreach ($this->columns as $column) {
                $row[] = $entry->{$column['name']};
            }

            fputcsv(
                $output,
                $row
            );
        }
    }
}