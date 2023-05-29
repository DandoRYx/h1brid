<!DOCTYPE html>
<html lang="<?= Language::get() ?>">
<head>
    <meta charset="utf-8">
    <title><?= Page::getTitle() ?></title>
    <meta name="copyright" content="<?= OWNER ?>" />
    <meta name="author" content="humans.txt">
    <meta name="description" content="<?= Page::getDescription() ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:title" content="<?= Page::getTitle() ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : PAGE; ?>" />
    <meta property="og:image" content="<?= PAGE . 'icon.png' ?>" />
    <meta property="og:description" content="<?= Page::getDescription() ?>" />

    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <link rel="stylesheet" href="/resource/css/clear.css">
    <link rel="stylesheet" href="/resource/css/fonts.css">
    <link rel="stylesheet" href="/resource/css/style.css?v=<?= hash_file('crc32', _PUBLIC . 'css/style.css') ?>">

    <meta name="theme-color" content="<?= THEME_COLOR ?>">
    <meta name="apple-mobile-web-app-status-bar" content="<?= THEME_COLOR ?>">


    <?php

    // SEO
    Breadcrumb::load();
    Page::loadLogo('logo1000to1000.png');

    ?>
</head>
<body>
