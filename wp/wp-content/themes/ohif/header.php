<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="sixpoints.pl">
    <title>OHIF Website</title>
    <link href='http://fonts.googleapis.com/css?family=Sanchez:400,700,400italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href="<?php echo get_template_directory_uri(); ?>/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" />
    <script src="<?php echo get_template_directory_uri(); ?>/bower_components/modernizr/modernizr.js"></script>
</head>
<body>
<header>
<nav class="top-bar" data-topbar role="navigation">
    <ul class="title-area">
        <li class="name">
            <h1><a href="<?php echo site_url(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/logo.png"></a></h1>
        </li>
        <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
    </ul>

    <section class="top-bar-section">

        <ul class="right">
            <li class="has-dropdown <?php if(@$p == 'solution'){ echo 'active'; } ?>"><a href="#">Solution</a>
                <ul class="dropdown">
<!--                    <li><a href="#build">Product overview </a></li>-->
                    <li><a href="<?php echo site_url(); ?>/for-developers">For Developers </a></li>
                    <li><a href="<?php echo site_url(); ?>/for-health-care-executive">For Health Care Executive </a></li>
                    <li><a href="<?php echo site_url(); ?>/for-radiologists">For Radiologists</a></li>
                    <li><a href="<?php echo site_url(); ?>/for-imaging-pro">For Imaging Professional</a></li>
                </ul></li>
            <li><a href="http://viewer.ohif.org" target="_blank">Demo</a></li>
            <li class="has-dropdown <?php if (@$p == 'about') echo 'active';?>"><a href="<?php echo site_url(); ?>/about-us">About us</a>
                <ul class="dropdown">
                    <li><a href="<?php echo site_url(); ?>/about-us">Story </a></li>
                    <li><a href="<?php echo site_url(); ?>/news">News </a></li>
                    <li><a href="<?php echo site_url(); ?>/documents">Documents </a></li>
                    <li><a href="<?php echo site_url(); ?>/contact">Contact</a></li>
                </ul></li></li>
            <li><a href="/forum">Forum</a></li>
            <li class="has-form">
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="NQXLPHWKCY4CC">
                    <button type="submit" class="button orange-bg radius">Donate</button>
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
            </li>
        </ul>
    </section>
</nav>
</header>
