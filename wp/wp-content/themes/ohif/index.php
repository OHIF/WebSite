<?php $p='postlist'; include("header.php"); ?>







        <?php


        $args = array( 'posts_per_page' => 2,  );

        $myposts = get_posts( $args );
        foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
            <section class="grey-block">
            <div class="row">
            <div class="medium-6 columns">
            <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                <?php the_content('Read more...'); ?>

            </div>
            </div>
                <div class="row">
                    <div class="medium-6 columns">
                    </div>
                </div>
            </section>
        <?php endforeach;
        wp_reset_postdata();?>







<?php get_footer(); ?>