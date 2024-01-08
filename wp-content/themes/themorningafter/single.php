<?php get_header(); ?>
<?php global $woo_options; ?>   

		<div id="topbanner" class="column span-14" style="background-image:url(<?php header_image(); ?>)">
            <div class="pagetitle">
                <?php echo $woo_options['woo_pageheading_prefix'] . stripslashes($woo_options['woo_pageheading_single']); ?>
            </div>
        </div>
        
        <div id="post_content" class="column span-14">
        
        <?php if (have_posts()) : ?>
			
        <?php while (have_posts()) : the_post(); ?>
        
        	<div class="column span-11 first">
        		<h2 class="post_cat"><?php $cat = get_the_category(); $cat = $cat[0]; echo $cat->cat_name; ?></h2>
        		
            	<h2 class="post_name" id="post-<?php the_ID(); ?>"><?php the_title(); ?></h2>
            	
				<?php echo woo_get_embed('embed','700','420'); ?>
            	<div class="post_meta">
            		<?php _e('By','woothemes');?> <?php the_author_posts_link(); ?> <span class="dot">&sdot;</span> <?php the_time('F j, Y'); ?> <span class="dot">&sdot;</span> <?php if(function_exists('wp_email')) { ?> <?php email_link(); ?> <span class="dot">&sdot;</span> <?php } ?> <?php if(function_exists('wp_print')) { ?> <?php print_link(); ?> <span class="dot">&sdot;</span> <?php } ?> <a href="#comments"><?php _e('Post a comment','woothemes'); ?></a>
            	</div>

				<div class="post_meta">
            		<?php the_tags('<span class="filedunder"><strong>' . __('Filed Under','woothemes') . '</strong></span> &nbsp;', ', ', ''); ?>
            	</div>
            	
				<div class="post_text">

            		<?php the_content('<p>'.__('Continue reading this post','woothemes').'</p>'); ?>

					<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages','woothemes').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				
				<?php edit_post_link(__('Edit this entry.', 'woothemes'),'<p>','</p>'); ?>	

				</div>
				<?php $comm = get_option('woo_comments'); if ( 'open' == $post->comment_status && ($comm == "post" || $comm == "both") ) : ?>
	                <?php comments_template('', true); ?>
                <?php endif; ?>
			
            	
            </div>
            
        <?php endwhile; else: ?>

		<p><?php _e('Lost? Go back to the','woothemes');?> <a href="<?php echo get_option('home'); ?>/"><?php _e('home page','woothemes');?></a>.</p>

		<?php endif; ?>    
            
            <?php get_sidebar(); ?>     
        
        </div>
                
<?php get_footer(); ?>