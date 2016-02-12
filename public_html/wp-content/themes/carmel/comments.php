<?php

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly.');

	if ( post_password_required() ) { ?>
		<p class="nocomments">This post is top secret and password protected. Enter the password to view comments.</p>
	<?php
		return;
	}
?>

<div id="comments">

<?php if ( have_comments() ) : ?>
	<h3><?php comments_number('No Comments', '1 Comment', '% Comments' );?></h3>
	<hr class='comment'>
	<ol id="comments_list">
		<?php foreach ($comments as $comment) : ?>
		<?php $comment_type = get_comment_type(); ?><?php if($comment_type == 'comment') { ?>
			<li class="<?php if (the_author('', false) == get_comment_author()) echo 'author'; else echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
				<div class="comment_meta">
					<?php if(function_exists('get_avatar')) { echo get_avatar($comment, '40'); } ?>
					<?php $id = get_comment(get_comment_ID())->user_id; $userData = get_userdata($id);?>
					<p class="meta"><strong>
						<?php 
							/*if($userData->user_url != "") //Use this code in case we decide to allow user websites again
								echo '<a href="'.$userData->user_url.'" rel="external nofollow" class="url">'.get_comment_author().'</a>';//comment_author_link(); 
							else */
								echo '<a href="/staff-directory/?page=profile&person='.$userData->user_login.'" rel="external nofollow" class="url">'.get_comment_author().'</a>';
							$query = "SELECT  photo, share_photo FROM employee WHERE user_login = '".$userData->user_login."'";
							$photoInfo = $wpdb-> get_results($wpdb->prepare($query, ""));
							if($photoInfo[0]->share_photo == 1) {
								echo '<br><img src="/wp-content/uploads/staff_photos/' . $photoInfo[0]->photo . '" width="50" style="border-radius: 15px;"/>';
							} else {
								echo '<br><img src="/wp-content/uploads/staff_photos/anonymous.jpg" width="50" style="border-radius: 15px;"/>';
							}
						?>
					</strong></p>
					<p><?php comment_date('F j, Y') ?></p>
				</div>
			
				<div class="comment_text">
					<?php comment_text(); ?>
				</div><div style='clear:both;'></div>
				<hr class='comment'>
				<?php if ($comment->comment_approved == '0') : ?>
				<em>Your comment is awaiting moderation.</em>
			<?php endif; ?>
			</li>
		<?php } else { $trackback = true; } /* End of is_comment statement */ ?>
		<?php endforeach; /* end for each comment */ ?>
	</ol>
	<div class="clear"></div>
<?php if ($trackback == true) { ?>
	<h3>Trackbacks</h3>
	<ol>
	<?php foreach ($comments as $comment) : ?>
		<?php $comment_type = get_comment_type(); ?>
			<?php if($comment_type != 'comment') { ?>
			<li><?php comment_author_link() ?></li>
		<?php } ?>
	<?php endforeach; ?>
	</ol>
<?php } ?>
<?php else : ?>
	<?php if ( !comments_open() ) : //if comments are closed ?>
		<p class="nocomments">Comments are closed.</p>
	<?php endif; ?>
<?php endif; ?>
</div><!-- end #comments -->
<?php if ( comments_open() ) : ?>
<div class="comments_reply">
	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
	<strong style='line-height: 23px;'><?php comment_form_title( 'Leave a Reply', 'Leave a Reply to %s' ); ?></strong>

	<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : //if visitors must be logged in to comment ?>
		<p>Sorry, but you must be <a href="<?php echo wp_login_url( get_permalink() ); ?>">logged in</a> to post a comment.</p>
	<?php else : ?>

		<?php if ( is_user_logged_in() ) : //if user is logged in, displays username and option to log out ?>
			<p class='right'>Logged in as <a class='username' href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out.</a></p><div style='clear:both;'></div>
		<?php else : ?>

		<p><input type="text" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
		<label for="author"><small>Name <?php if ($req) echo "(required)"; ?></small></label></p>

		<p><input type="text" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
		<label for="email"><small>Mail <small>(will not be published or shared)</small> <?php if ($req) echo "(required)"; ?></small></label></p>

		<p><input type="text" name="url" id="url" value="<?php echo esc_attr($comment_author_url); ?>" size="22" tabindex="3" />
		<label for="url"><small>Website</small></label></p>
		<?php endif; ?>

		<p><textarea class='modify' name="comment" id="comment" cols="80%" rows="10" tabindex="4"></textarea></p>

		<p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" class="btn1" />
		<?php comment_id_fields(); ?>
		</p>
		<?php do_action('comment_form', $post->ID); ?>
	</form>


	<?php endif; // If registration required and not logged in ?>
</div><!-- end .comments_reply -->

<?php endif; ?>