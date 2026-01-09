<?php
/**
 * Page template
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

get_header(); ?>

        <!----Main Content Wrapper Start---->
        <div class="ms_content_wrapper padder_top80">

            <?php while (have_posts()) : the_post(); ?>

                <div class="container">
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>

                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                            </div>
                        <?php endif; ?>

                        <div class="entry-content">
                            <?php the_content(); ?>
                            <?php
                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . esc_html__('Pages:', 'miraculous-music'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </div>

                    </article>

                    <?php
                    // If comments are open or there is at least one comment, load up the comment template.
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>

                </div>

            <?php endwhile; ?>

        </div>
        <!----Main Content Wrapper End---->

<?php get_footer(); ?>
