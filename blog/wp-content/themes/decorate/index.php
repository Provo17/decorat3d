<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one of the
 * two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

	<div class="color-box4">
		<div class="container masonry">
            <div class="row">
            <?php if ( have_posts() ) : ?>
    
                <?php /* The loop */ ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <div class="item">
                        <div class="well">
                        	<?php get_template_part( 'content', get_post_format() ); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
    
                <?php twentythirteen_paging_nav(); ?>
    
            <?php else : ?>
                <?php get_template_part( 'content', 'none' ); ?>
            <?php endif; ?>
            
            </div>
		</div><!-- .container -->
	</div><!-- .color-box4 -->

<?php get_footer(); ?>
