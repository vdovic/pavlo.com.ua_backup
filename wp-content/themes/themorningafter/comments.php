<?php
	
// Do not delete these lines

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');

if ( post_password_required() ) { ?>
	<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'woothemes') ?></p>

<?php return; } ?>

<?php $comments_by_type = &separate_comments($comments); ?>    

<!-- You can start editing here. -->
<div id="comments">

<div id="commenthead">
<h2 class="post_comm"><?php _e('Discussion','woothemes');?></h2>
</div>

<?php if ( have_comments() ) : ?>

	<?php if ( ! empty($comments_by_type['comment']) ) : ?>

		<h3><?php comments_number(__('No Responses', 'woothemes'), __('One Response', 'woothemes'), __('% Responses', 'woothemes') );?> <?php _e('to', 'woothemes') ?> &#8220;<?php the_title(); ?>&#8221;</h3>

		<ol id="commentlist">
	
			<?php wp_list_comments('avatar_size=40&callback=custom_comment&type=comment'); ?>
		
		</ol>    

		<div class="navigation">
			<div class="fl"><?php previous_comments_link() ?></div>
			<div class="fr"><?php next_comments_link() ?></div>
			<div class="fix"></div>
		</div><!-- /.navigation -->
	<?php endif; ?>
		    
	<?php if ( ! empty($comments_by_type['pings']) ) : ?>
    		
        <h3 id="pings"><?php _e('Trackbacks/Pingbacks', 'woothemes') ?></h3>
    
        <ol id="pinglist">
            <?php wp_list_comments('type=pings&callback=list_pings'); ?>
        </ol>
    	
	<?php endif; ?>
    	
<?php else : // this is displayed if there are no comments so far ?>

		<?php if ('open' == $post->comment_status) : ?>
			<!-- If comments are open, but there are no comments. -->
			<h3 class="mast3"><?php _e('No comments yet.', 'woothemes') ?></h3>

		<?php else : // comments are closed ?>
			<!-- If comments are closed. -->
			<h3 class="mast3"><?php _e('Comments are closed.', 'woothemes') ?></h3>

		<?php endif; ?>

<?php endif; ?>

</div>


<?php if ('open' == $post->comment_status) : ?>

<div id="respond">

<h2 id="comment-form" class="post_comm2"><?php comment_form_title( 'Post a comment', 'Reply to %s' ); ?></h2>

<div class="cancel-comment-reply">
	<?php cancel_comment_reply_link(); ?>
</div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p><?php _e('You must be', 'woothemes'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>"><?php _e('logged in','woothemes'); ?></a> to post a comment.</p>

</div>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p><?php _e('Logged in as', 'woothemes'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account"><?php _e('Logout', 'woothemes'); ?> &raquo;</a></p>

<?php else : ?>

<fieldset>

	<p>
		<label for="author" class="com"><?php _e('Name', 'woothemes'); ?> <?php if ($req) echo "*"; ?></label>
		<input class="comtext" type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
	</p>

	<p>
		<label for="email" class="com"><?php _e('E-mail', 'woothemes'); ?> <?php if ($req) echo "*"; ?></label>
		<input class="comtext" type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
	</p>

	<p>
		<label for="url" class="com"><?php _e('Web site', 'woothemes'); ?></label>
		<input class="comtext" type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
	</p>


<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small></p>-->

	<p>
		<label for="comment" class="com"><?php _e('Comment', 'woothemes'); ?></label>
		<textarea class="comtext" name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea>
	</p>
	
</fieldset>

<fieldset>

	<p>
		<input name="submit" type="submit" id="submit" tabindex="5" class="comsubmit" value="<?php _e('Submit Comment','woothemes'); ?>" />
		<?php comment_id_fields(); ?>	
	</p>

<?php do_action('comment_form', $post->ID); ?>

</fieldset>

</form>

</div>

<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>
