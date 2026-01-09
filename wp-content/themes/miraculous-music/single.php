<?php
/**
 * Single post template
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

get_header(); ?>

        <!----Main Content Wrapper Start---->
        <div class="ms_content_wrapper padder_top80">

            <?php while (have_posts()) : the_post(); ?>

                <div class="container">
                    <div class="row">
                        <div class="col-lg-8">
                            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                                    </div>
                                <?php endif; ?>

                                <header class="entry-header">
                                    <h1 class="entry-title"><?php the_title(); ?></h1>

                                    <div class="entry-meta">
                                        <span class="posted-on">
                                            <?php echo get_the_date(); ?>
                                        </span>
                                        <span class="byline">
                                            <?php esc_html_e('by', 'miraculous-music'); ?>
                                            <?php the_author(); ?>
                                        </span>
                                        <?php if (has_category()) : ?>
                                            <span class="cat-links">
                                                <?php the_category(', '); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </header>

                                <div class="entry-content">
                                    <?php the_content(); ?>
                                </div>

                                <?php if (has_tag()) : ?>
                                    <footer class="entry-footer">
                                        <div class="tags-links">
                                            <?php the_tags('<strong>' . esc_html__('Tags:', 'miraculous-music') . '</strong> ', ', '); ?>
                                        </div>
                                    </footer>
                                <?php endif; ?>

                                <?php
                                // If comments are open or there is at least one comment, load up the comment template.
                                if (comments_open() || get_comments_number()) :
                                    comments_template();
                                endif;
                                ?>

                            </article>
                        </div>

                        <div class="col-lg-4">
                            <?php get_sidebar(); ?>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>

        </div>
        <!----Main Content Wrapper End---->

<?php get_footer(); ?>
