<?php
/*
Template Name: Image Gallery
*/
?>
<?php get_header(); ?>
<?php global $woo_options; ?>   

		<div id="topbanner" class="column span-14" style="background-image:url(<?php header_image(); ?>)">
            <div class="pagetitle">
                <?php echo $woo_options['woo_pageheading_prefix']; the_title(); ?>
            </div>
        </div>
        
        <div id="post_content" class="column span-14 first">
        
        	<div class="column span-11 first">
                                                                            
            <div class="post">

				<div class="entry">
                <?php query_posts('showposts=60'); ?>
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>				
                    <?php $wp_query->is_home = false; ?>

                    <?php woo_get_image('image',100,100,'thumbnail alignleft'); ?>
                
                <?php endwhile; endif; ?>	
                </div>

            </div>
            <div class="fix"></div>                
                                                            
		</div>
		
        <?php get_sidebar(); ?>

    </div>
		
<?php get_footer(); ?>