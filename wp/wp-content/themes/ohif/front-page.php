<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 2015-08-12
 * Time: 10:59
 */ ?>

<?php get_header(); ?>


    <div class="row" id="frontpage">
        <div id="overlay">

            <div class="aamedium-12 aacolumns" style="position: absolute; z-index: 200;">
                <h1>Opensource solution<br>
                    for modern health care</h1>
                <h2>Free HTML5 DICOM web viewer, Free image viewer software and server</h2>
                <a href="http://viewer.ohif.org" target="_blank" class="button large orange-bg radius">Try demo now</a>

                <ul class="inline-list">
                    <!--                    <li><a href="#">Product overview</a></li>-->
                    <li><a href="<?php echo site_url(); ?>/for-developers">For Developers</a></li>
                    <li><a href="<?php echo site_url(); ?>/for-health-care-executive">For Health Care Executive </a></li>
                    <li><a href="<?php echo site_url(); ?>/for-radiologists">For Radiologists</a></li>
                    <li><a href="<?php echo site_url(); ?>/for-imaging-pro">For Imaging Professional</a></li>
                </ul>
            </div>

            <img src="<?php echo get_template_directory_uri(); ?>/img/movie.gif" alt="">




        </div>


    </div>


    <section class="news-bar">
        <div class="row">
            <div class="medium-12 columns text-center">
                                Latest news:
                <?php
                $args = array( 'numberposts' => '1' );
                $recent_posts = wp_get_recent_posts( $args );
                foreach( $recent_posts as $recent ){
                    echo '<a href="' . get_permalink($recent["ID"]) . '">' .   $recent["post_title"].'</a>  ';
                }
                ?>
            </div>


        </div>
    </section>





<?php get_footer(); ?>
