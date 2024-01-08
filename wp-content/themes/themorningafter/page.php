<?php get_header(); ?>
<?php global $woo_options; ?>
        
        <div id="topbanner" class="column span-14" style="background-image:url(<?php header_image(); ?>)">
            <div class="pagetitle">
                <?php echo $woo_options['woo_pageheading_prefix']; the_title(); ?>
            </div>
        </div>
        
        <div id="post_content" class="column span-14">
        
        <?php if (have_posts()) : ?>
			
        <?php while (have_posts()) : the_post(); ?>
        
        	<div class="column span-11 first">
        		            	
            	<?php the_content('<p>'.__('Continue reading this post','woothemes').'</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages','woothemes').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				
				<?php edit_post_link(__('Edit this entry.', 'woothemes'),'<p>','</p>'); ?>				
            	
				<?php $comm = get_option('woo_comments'); if ( 'open' == $post->comment_status && ($comm == "page" || $comm == "both") ) : ?>
	                <?php comments_template('', true); ?>
                <?php endif; ?>

            </div>
            
        <?php endwhile; endif; ?>    
            
            <?php get_sidebar(); ?>     
            
        
        </div>   <!-- start home_content -->
        
        
<?php get_footer(); ?>
