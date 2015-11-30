

<?php $p='post'; include("header.php"); ?>

    <div data-magellan-expedition="fixed">
        <dl class="sub-nav">
            <dd data-magellan-arrival="js"><a href="<?php echo site_url(); ?>/news">Back to the latest news</a></dd>

        </dl>
    </div>




                <?php

                if (have_posts()) :
                    while (have_posts()) :
                        the_post();?>
                        <section class="grey-block">
                            <div class="row">
                                <div class="medium-6 columns">
                                    <h1><?php the_title(); ?></h1>
                                 <?php   the_content();?>
                                </div>
                            </div>
                            <div class="row">
                            <div class="medium-6 columns">
                            </div>
                            </div>
                        </section>

                   <?php endwhile;
                endif;

                ?>


<section class="grey-block">
    <div class="row">
    <div class="medium-12 columns">
<?php
$postlist = get_posts( 'sort_column=menu_order&sort_order=asc' );
$posts = array();
foreach ( $postlist as $post ) {
    $posts[] += $post->ID;
}

$current = array_search( get_the_ID(), $posts );
$prevID = $posts[$current-1];
$nextID = $posts[$current+1];
?>

    <div class="navigation text-center">
        <?php if ( !empty( $prevID ) ): ?>
            <div class="alignleft">
                <a href="<?php echo get_permalink( $prevID ); ?>"
                   title="<?php echo get_the_title( $prevID ); ?>">Previous</a>
            </div>
        <?php endif;
        if ( !empty( $nextID ) ): ?>
            <div class="alignright">
                <a href="<?php echo get_permalink( $nextID ); ?>"
                   title="<?php echo get_the_title( $nextID ); ?>">Next</a>
            </div>
        <?php endif; ?>
    </div><!-- .navigation -->

</div></div></section>






<?php get_footer(); ?>