<?php
/*
Template Name: About
*/
$pagemenu = 2;

?>

<?php $p='about'; include("header.php"); ?>

<?php include("_subnav-about.php"); ?>



    <section class="grey-block">
        <div class="row">
            <div class="medium-8 columns">


                <?php

                if (have_posts()) :
                    while (have_posts()) :
                        the_post();
                        the_content();
                    endwhile;
                endif;

                ?>


            </div>
        </div>
        <div class="row">
            <div class="medium-6 columns">
            </div>
        </div>
    </section>




<?php get_footer(); ?>