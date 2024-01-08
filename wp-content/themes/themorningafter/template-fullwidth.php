<?php
/*
Template Name: Full Width
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
            
            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
            
        	<div class="column">
           
                <div class="entry">
	               	<?php the_content(); ?>
	          	</div>
             
             </div>
                                                    
        	<?php endwhile; else: ?>

			<p><?php _e('Lost? Go back to the','woothemes');?> <a href="<?php echo get_option('home'); ?>/"><?php _e('home page','woothemes');?></a>.</p>

			<?php endif; ?>   
            

        
        </div>
                
<?php get_footer(); ?>