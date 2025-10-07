<?php

$is_cal_requested = ifne($_REQUEST, '_cal', null);

if ($is_cal_requested) {

    //Chrome
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'CriOS') !== false) {

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>Chrome iOS Warning</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="apple-mobile-web-app-capable" content="yes" />
            <link href="<?php echo $GLOBALS['template_url']; ?>/css/style.css" rel="stylesheet" />
        </head>
        <body class="chrome-warning">
            <h1 class="title">Chrome does not support downloading of calendar events on iOS.</h1>
            <p>Copy <a href="<?php echo get_the_permalink($post->ID); ?>?_cal=1">this URL</a> and open in Safari to view the event.</p>
        </body>
        </html>
        <?php

        exit;
    }

    $start_date     = ifne($fields, 'start_date');
    $end_date       = ifne($fields, 'end_date');
    $start_time     = ifne($fields, 'start_time');
    $end_time       = ifne($fields, 'end_time');

    // e.g. "2015-04-06T10:00:00+09:00"

    $startDate = date('Y-m-d', strtotime($start_date)) . 'T';
    if (!empty($start_time)) {
        $startDate .= date('H:i', strtotime($start_time)) . ':00+08:00';
    }
    else {
        $startDate .= '00:00:00+08:00';
    }

    $endDate = date('Y-m-d', strtotime($end_date)) . 'T';
    if (!empty($end_time)) {
        $endDate .= date('H:i', strtotime($end_time)) . ':00+08:00';
    }
    else {
        if ($start_date == $end_date) {
            $endDate .= '23:59:59+08:00';
        }
        else {
            $endDate .= '00:00:00+08:00';
        }
    }

    include_once($GLOBALS['template_path'] . '/library/SimpleICS/SimpleICS.php');

    $cal = new SimpleICS();
    $cal->addEvent(function($e) use ( $post, $startDate, $endDate, $fields ) {

        $e->startDate = new DateTime($startDate);
        $e->endDate = new DateTime($endDate);

        $e->uri = get_the_permalink($post->ID);

        if (!empty(ifne('location', $fields))) {
            $e->location = $fields['location'];
        }

        $e->summary = html_entity_decode(get_the_title($post->ID));
    });

    header('Content-Type: ' . SimpleICS::MIME_TYPE);
    header('Content-Disposition: attachment; filename=event.ics');
    echo $cal->serialize();

    exit;

}
