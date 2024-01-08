<?php get_header(); ?>        
<?php global $woo_options; ?>
        
        <div id="topbanner" class="column span-14" style="background-image:url(<?php header_image(); ?>)">
            <div class="pagetitle">
                <?php echo $woo_options['woo_pageheading_prefix'] . stripslashes($woo_options['woo_pageheading_search']); ?>
            </div>
        </div>
        
        
        <div id="arch_content" class="column span-14">
        
        <?php if (have_posts()) : ?>
        
        	<div class="column span-3 first">        
            	<h2 class="archive_name"><?php _e('Search Results','woothemes');?></h2>        
            	
            	<div class="archive_meta">
					
					<div class="archive_number">
						<?php _e('You searched for','woothemes');?> '<?php the_search_query(); ?>'. <?php _e('Your search returned','woothemes');?> <?php $NumResults = $wp_query->found_posts; echo $NumResults; ?> <?php _e('results','woothemes');?>.
					</div>
            	
            	</div>
            </div>
            
                        
            <div class="column span-8">
            
            <?php while (have_posts()) : the_post(); ?>
            
            	<div class="archive_post_block">
            		<h3 class="archive_title" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e('Permanent Link to','woothemes');?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
            		
            		<div class="archive_post_meta"><?php _e('By','woothemes');?> <?php the_author_posts_link(); ?> <span class="dot">&sdot;</span> <?php the_time('F j, Y'); ?> <span class="dot">&sdot;</span> <a href="<?php comments_link(); ?>"><?php comments_number(__('Post a comment', 'woothemes'), __('One comment', 'woothemes'),'% comments'); ?></a></div>
            		
            		<?php the_excerpt(); ?>
            	</div>
            	
        	<?php endwhile; ?>

			<div class="navigation">
				<p><?php next_posts_link(__('&laquo; Previous', 'woothemes')) ?> &nbsp; <?php previous_posts_link(__('Next &raquo;', 'woothemes')) ?></p>
			</div>

            </div>
            
            <?php get_sidebar(); ?>

		<?php else : ?>
			<div class="column span-3 first">        
            	<h2 class="archive_name"><?php _e('Search Results','woothemes');?></h2>        
            	
            	<div class="archive_meta">
					
					<div class="archive_number">
						<?php _e('You searched for','woothemes');?> '<?php the_search_query(); ?>'. <?php _e('Your search returned','woothemes');?> <?php $NumResults = $wp_query->found_posts; echo $NumResults; ?> <?php _e('results','woothemes');?>.
					</div>
            	
            	</div>
            </div>
            
                        
            <div class="column span-8">
            
            <h3><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></h3>

            </div>
            
            <?php get_sidebar(); ?>
			
		<?php endif; ?>
            	
        
        </div>
        
        
<?php get_footer(); ?>