<?php
// Fist full of comments
function custom_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
                 
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
    
    	<a name="comment-<?php comment_ID() ?>"></a>
  			
		<div class="commentcont" id="comment-<?php comment_ID(); ?>">
		<?php if(get_comment_type() == "comment"){ ?>
		<div class="fright"><?php the_commenter_avatar($args) ?></div>
		<?php } ?>
							
			<?php comment_text() ?>
				
				<p>
					<?php if ($comment->comment_approved == '0') : ?>
				
					<em><?php _e('Your comment is awaiting moderation','woothemes');?>.</em>
					
					<?php endif; ?>
				</p>
				
		<cite>
		
		<?php _e('Posted by','woothemes'); ?> <span class="commentauthor"><?php comment_author_link() ?></span> | <a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date(get_option('date_format')) ?>, <?php comment_time() ?></a> <?php edit_comment_link('edit','| ',''); ?>						
		
		</cite>
		
		</div>
		
		
		
		<div class="reply">
         <?php comment_reply_link(array_merge( $args, array('reply_text' => 'Reply to this comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </div>

		
<?php 
}

// PINGBACK / TRACKBACK OUTPUT
function list_pings($comment, $args, $depth) {

	$GLOBALS['comment'] = $comment; ?>
	
	<li id="comment-<?php comment_ID(); ?>">

		<span class="pingcontent"><?php comment_text() ?></span>
		<div class="ping_meta">
			<span class="author"><?php comment_author_link(); ?></span> - 
			<span class="date"><?php echo get_comment_date(get_option('date_format')) ?></span>
		</div>

<?php 
} 
		
function the_commenter_link() {
    $commenter = get_comment_author_link();
    if ( ereg( ']* class=[^>]+>', $commenter ) ) {$commenter = ereg_replace( '(]* class=[\'"]?)', '\\1url ' , $commenter );
    } else { $commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );}
    echo $commenter ;
}

function the_commenter_avatar($args) {
    $email = get_comment_author_email();
    $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( "$email",  $args['avatar_size']) );
    echo $avatar;
}

?>