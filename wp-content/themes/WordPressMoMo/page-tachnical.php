<?php
/*
Template Name:技术分享
*/
?>
<?php get_header(); ?>
<?php get_header('masthead'); ?>

<?php query_posts('showposts=10 & cat=5');?>
    <div id="main" class="container" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
        <div class="row">
            <?php
            while (have_posts()) : the_post();
                ?>
                <article id="content" class="col-lg-12" data-post-id="<?php the_ID(); ?>" role="article" itemscope
                         itemtype="http://schema.org/Article">
                    <div>
                        <div class="panel-body">
                            <div>
                                <h2>
                                    <a href="<?php the_permalink() ?>">
                                        <?php the_title(); ?>
                                        <?php if (is_preview() || current_user_can('edit_post', get_the_ID())) echo ' <small><a href="' . get_edit_post_link() . '" data-no-instant>' . __('Edit This') . '</a></small>'; ?>
                                    </a>
                                </h2>
                                <?php dmeng_post_meta(); ?>
                            </div>
                            <?php global $post;
                            if ($post->post_excerpt) {
                                echo '<div class="excerpt">';
                                $excerpt = $post->post_excerpt;
                                echo $excerpt;
                                echo '</div>';
                            }
                            ?>
                            <?php if (!is_sticky() ) : ?>
                                <?php if (has_post_thumbnail()) { ?>
                                    <div class="entry-thumbnail"><a href="<?php the_permalink() ?>"><?php the_post_thumbnail(); ?></a></div>
                                <?php } ?>
                                <?php the_excerpt(); ?>
                                <a href="<?php the_permalink() ?>" title="阅读全文">阅读全文...</a>
                            <?php endif; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="<?php echo apply_filters('dmeng_comment_panel_class', 'panel panel-default'); ?>"
                         id="comments" data-no-instant><?php comments_template('', true); ?></div>
                    <?php
                    $prev_post = get_previous_post(true);
                    if (!empty($prev_post)) {
                        ?>
                        <span id="nav_prev"><a href="<?php echo get_permalink($prev_post->ID); ?>"
                                               title="上一篇：<?php echo $prev_post->post_title; ?>">‹</a></span>
                    <?php }
                    $next_post = get_next_post(true);
                    if (is_a($next_post, 'WP_Post')) { ?>
                        <span id="nav_next"><a href="<?php echo get_permalink($next_post->ID); ?>"
                                               title="下一篇：<?php echo get_the_title($next_post->ID); ?>">›</a></span>
                    <?php } ?>
                </article><!-- #content -->
                <?
            endwhile; // end of the loop.
            dmeng_paginate();
            ?>
        </div>
    </div><!-- #main -->
<?php get_footer('colophon'); ?>
<?php get_footer(); ?>