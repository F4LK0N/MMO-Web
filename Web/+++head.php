<?php
//####################
//### HTTP HEADERS ###
//####################

//Encoding
header("Content-Type: text/html; charset=utf-8");

//Cross Origin (Allowed)
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, access-control-allow-origin, enctype, Access-Control-Allow-Origin');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Content-Type: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Credentials: true');



//#################
//### HTML HEAD ###
//#################
?><html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= PATH::CSS(); ?>libs.css">
    <link rel="stylesheet" href="<?= PATH::CSS(); ?>app.css">

    <!-- TITLE -->
	<title>MMO Game - By F4LK0N</title>

    <?php if(false){ ?>
    <!-- FAVICON -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?= PATH::IMG(); ?>favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= PATH::IMG(); ?>favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= PATH::IMG(); ?>favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= PATH::IMG(); ?>favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= PATH::IMG(); ?>favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= PATH::IMG(); ?>favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= PATH::IMG(); ?>favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= PATH::IMG(); ?>favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= PATH::IMG(); ?>favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?= PATH::IMG(); ?>favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= PATH::IMG(); ?>favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= PATH::IMG(); ?>favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= PATH::IMG(); ?>favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= PATH::IMG(); ?>favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= PATH::IMG(); ?>favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <?php } ?>
</head>
<body>
