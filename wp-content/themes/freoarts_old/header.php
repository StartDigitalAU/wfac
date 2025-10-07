<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie ie9" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en" class=""> <!--<![endif]-->
<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-TMTPSQ7');</script>
	<!-- End Google Tag Manager -->

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo wp_title( '|', false, 'right' ); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    $template_path = $GLOBALS['template_path'];
    $css_path = '/css/style.css';

    $ver = filemtime($template_path . $css_path);
    ?>
    <link href="<?php eu($GLOBALS['template_url'] . $css_path . '?v=' . $ver) ?>" media="screen" rel="stylesheet" />
    <link href="<?php eu($GLOBALS['template_url'] . '/css/print.css') ?>" media="print" rel="stylesheet" />

    <!-- <script src="https://use.typekit.net/zqg8brq.js"></script>
    <script>try{Typekit.load({ async: true });}catch(e){}</script> -->

    <link rel="stylesheet" href="https://use.typekit.net/zqg8brq.css">

    <link href="//cloud.typenetwork.com/projects/6641/fontface.css/" rel="stylesheet" type="text/css">

    <!-- Preload -->
    <?php /*
    <link rel="preload" href="https://cloud.typenetwork.com/projectLicenseWeb/26344/fontfile/woff2/?9d10f00c6d9a3ff178086f9afd15e81f84eff41a" as="font" crossorigin/>
    <link rel="preload" href="<?= $GLOBALS['template_url'] ?>/css/fonts/Icons.woff" as="font" crossorigin/>
    <link rel="preload" href="https://use.typekit.net/af/e99728/00000000000000003b9adcff/27/l?primer=7cdcb44be4a7db8877ffa5c0007b8dd865b3bbc383831fe2ea177f62257a9191&fvd=n4&v=3" as="font" crossorigin />
    <link rel="preload" href="https://use.typekit.net/af/40af23/00000000000000003b9adcfd/27/l?primer=7cdcb44be4a7db8877ffa5c0007b8dd865b3bbc383831fe2ea177f62257a9191&fvd=n5&v=3" as="font" crossorigin/>
    <link rel="preload" href="https://use.typekit.net/af/2c97ea/00000000000000003b9adcf7/27/l?primer=7cdcb44be4a7db8877ffa5c0007b8dd865b3bbc383831fe2ea177f62257a9191&fvd=n7&v=3" as="font" crossorigin/>
    <link rel="preload" href="https://use.typekit.net/af/fb5fb4/00000000000000003b9add00/27/l?primer=7cdcb44be4a7db8877ffa5c0007b8dd865b3bbc383831fe2ea177f62257a9191&fvd=i4&v=3" as="font" crossorigin/>
    */ ?>

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js"></script>
    <![endif]-->

    <!--[if IE 9]>
    <script src="<?php eu($GLOBALS['template_url'] . '/js/lib/matchMedia-addListener.min.js') ?>"></script>
    <script src="<?php eu($GLOBALS['template_url'] . '/js/lib/matchMedia.min.js') ?>"></script>
    <![endif]-->

    <!-- Favicons -->
    <link rel="shortcut icon" href="<?php eu($GLOBALS['template_url'] . '/img/favicon.ico') ?>">
    <link rel="apple-touch-icon" href="<?php eu($GLOBALS['template_url'] . '/img/apple-touch-icon.png') ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php eu($GLOBALS['template_url'] . '/img/apple-touch-icon-72x72.png') ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php eu($GLOBALS['template_url'] . '/img/apple-touch-icon-114x114.png') ?>">


    <!-- Made by Humaan http://humaan.com @wearehumaan -->

    <!-- Facebook Pixel Code -->

    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window,document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1889070078050588');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" src="https://www.facebook.com/tr?id=1889070078050588&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->

    <?php

    wp_head();

    global $body_class;

    ?>
</head>
<body <?php body_class($body_class); ?>>

<!-- Google tag (gtag.js) -->
<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-HQQLV4860T"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-HQQLV4860T');
</script> -->

<a href="#main" class="offscreen" aria-label="Skip to content">Skip to content</a>

<?php get_template_part('parts/header'); ?>
