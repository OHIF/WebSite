<?php
/*
Template Name: test
*/
?>

<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 2015-09-11
 * Time: 12:41
 */
?>

<h1>Conf Test</h1>

    <br><br><b>template dir:</b> <br><?php echo get_template_directory_uri(); ?>
    <br><br><b>stylesheet dir:</b> <br><?php bloginfo('stylesheet_url'); ?>
    <br><br><b>main dir:</b> <br><?php echo site_url(); ?>
    <br><br><b>title:</b> <br><?php echo $post->post_name; ?>



<?php

                if (have_posts()) :
                    while (have_posts()) :
                        the_post();
                        the_content();
                    endwhile;
                endif;

                ?>