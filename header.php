<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="sixpoints.pl">
    <title>OHIF Website</title>
    <link href='http://fonts.googleapis.com/css?family=Sanchez:400,700,400italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="stylesheets/app.css" />
    <script src="bower_components/modernizr/modernizr.js"></script>
</head>
<body>
<header>
<nav class="top-bar" data-topbar role="navigation">
    <ul class="title-area">
        <li class="name">
            <h1><a href="index.php"><img src="img/logo.png"></a></h1>
        </li>
        <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
    </ul>

    <section class="top-bar-section">
        <ul class="right">
            <li class="has-dropdown <?php if (@$p == 'solution') { echo 'active'; } ?>"><a href="#">Solution</a>
                <ul class="dropdown">
<!--                    <li><a href="#build">Product overview </a></li>-->
                    <li><a href="for-developers.php">For Developers </a></li>
                    <li><a href="for-managers.php">For Managers </a></li>
                    <li><a href="for-radiologists.php">For Radiologists</a></li>
                    <li><a href="for-imaging-pro.php">For Imaging Professional</a></li>
                </ul></li>
            <li><a href="http://chafey.github.io/cornerstoneDemo/" target="_blank">Demo</a></li>
            <li class="has-dropdown <?php if (@$p == 'about') { echo 'active'; } ?>"><a href="about-us.php">About us</a>
                <ul class="dropdown">
                    <li><a href="about-us.php">Story </a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul></li></li>
            <li><a href="/forum">Forum</a></li>
            <li class="has-form"><a href="/donate" class="button orange-bg">Donate</a></li>
        </ul>
    </section>
</nav>
</header>