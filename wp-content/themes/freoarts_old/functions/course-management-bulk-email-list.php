<?php

function view_admin_course_management_sent_email_list()
{

    global $wpdb;

    $limit = 20;

    $search_query = !empty($_GET['sq']) ? $_GET['sq'] : '';

    $paged = !empty($_GET['paged']) ? $_GET['paged'] : 1;

    $offset = ($paged === 1) ? 0 : ($paged - 1) * $limit;

    $table_name = $wpdb->prefix . 'email_scheduler_recipients';

    $search_sql = "";

    if (!empty($search_query)) {

        $search_sql = "WHERE esr.email LIKE '%{$search_query}%' OR esr.full_name LIKE '%{$search_query}%'";
    }

    $sql = "SELECT 
              COUNT(*) 
              
            FROM {$table_name}
            
            {$search_sql}
            ";
    $count = $wpdb->get_var($sql);

    $sql = "SELECT
                *
                
            FROM {$table_name} AS esr
            
            LEFT JOIN wp_email_scheduler_templates AS emt
            ON emt.id = esr.template_id
            
            {$search_sql}
            
            ORDER BY esr.sent_at DESC
            
            LIMIT {$limit}
            OFFSET {$offset}
            ";

    $emails = $wpdb->get_results($sql);

    ?>
    <div class='wrap'>

        <h1 class="wp-heading-inline">Sent Emails</h1>

        <hr class="wp-header-end">

        <form action="<?php echo admin_url('admin.php?page=course-management-sent-email-list'); ?>" method="get">

            <input type="hidden" name="page" value="course-management-sent-email-list">

            <div class="tablenav top">

                <?php /* <div class="alignleft actions">
                    <label for="filter-by-date" class="screen-reader-text">Search</label>
                    <input type="text" name="sq" placeholder="Search by Package ID or Name" size="40" value="<?php echo $search_query; ?>">
                    <button type="submit">Search</button>
                </div> */ ?>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo $count; ?> <?php echo __('items', 'humaan_translations'); ?></span>
                    <span class="pagination-links">
                        <?php if ($paged > 1) : ?>
                            <a class="prev-page" href="<?php echo admin_url('admin.php?page=course-management-sent-email-list&sq=' . $search_query . '&paged=' . ($paged - 1)); ?>">
                                <span class="screen-reader-text">Previous page</span>
                                <span aria-hidden="true">&lsaquo;</span>
                            </a>
                        <?php endif; ?>
                        <span class="paging-input">
                            <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                            <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $paged; ?>" size="2" aria-describedby="table-paging">
                            <span class="tablenav-paging-text"> of <span class="total-pages"><?php echo ceil($count / $limit); ?></span></span>
                            <button type="submit">Go To Page</button>
                        </span>
                        <?php if ($count > $paged * $limit) : ?>
                            <a class="next-page" href="<?php echo admin_url('admin.php?page=course-management-sent-email-list&sq=' . $search_query . '&paged=' . ($paged + 1)); ?>">
                                <span class="screen-reader-text">Next page</span>
                                <span aria-hidden="true">&rsaquo;</span>
                            </a>
                        <?php endif; ?>
                    </span>
                </div>
                <br class="clear">
            </div>

            <table class="widefat striped htl-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Template</th>
                    <th>Created At</th>
                    <th>Sent At</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Template</th>
                    <th>Created At</th>
                    <th>Sent At</th>
                </tr>
                </tfoot>
                <tbody>
                <?php if (!empty($emails)) : ?>
                    <?php foreach ($emails as $email) : ?>
                        <tr>
                            <td><?php echo $email->full_name; ?></td>
                            <td><?php echo $email->email; ?></td>
                            <td><?php echo $email->subject; ?></td>
                            <td><?php echo $email->created_at; ?></td>
                            <td><?php echo $email->sent_at; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>

        </form>

    </div>
    <?php
}