<?php
if($comment)
{
?>
<div style="width:100%;display:flex;flex-direction:column;padding-top:3%;">
<?php
$i = 1;
foreach($comment as $c)
{
?>
	<div style="margin-left:3%;width:85%;height:100%;margin-bottom:1%" id="demo">
		<div style="border:1px solid #868686;border-radius:30px;">
			<p style="color:black;padding:0 1.5%;padding-top:1%;margin-bottom:1%">
				<?php print_r ($c[0]->comment_msg);?>
			</p>
		</div>
		<div style="width:95%;margin:0 auto;">
			<p style="display:inline-block;padding-left:1%;margin-bottom:1%">
				<a style="text-decoration:none;cursor:pointer;" onclick="toggleReplyForm(<?php print($i); ?>)">Reply</a>
			</p>
			<p style="display:inline-block;padding-left:3%;cursor:pointer;color:#015f8c" onclick="toggleReplyView(<?php print($i) ?>)">
				<?php print(get_reply_count($c[0]->comment_id)." Reply"); ?>
			</p>
			<p style="color:black;float:right;margin:0;font-size:13px;">
				<?php print ($c[0]->user_name); ?>
			</p>
			<div style="width:85%;margin:1% auto 2% auto;display:none" id="replydiv<?php echo $i ?>">
				<?php
					$all_replies = get_replies($c[0]->comment_id);
					if($all_replies)
					{
						foreach($all_replies as $reply)
						{
							$replyMsg = $reply[0]->reply_message;
							$user = $reply[0]->user_name;
							   echo"<p 
							   	   style='border:1px solid #868686;border-radius:30px;padding:0.8% 1.5%;margin-bottom:0;color:black;margin-top:3%'>
							           $replyMsg
							   	</p>";
							   echo"<p style='font-size:12px;margin-bottom:1%;padding:0;color:black;float:right;margin-right:5%;'>$user</p>";
						}
					}
				?>
			</div>
			
			<div style="display:none;" id="replyForm<?php echo $i;?>">
				<form style="display:flex;flex-direction:column;align-items:center;" action="" method="POST">
					<input type="hidden" name="commentid" value="<?php print($c[0]->comment_id); ?>">
					<textarea rows="3" cols="50" name="replyMsg" placeholder="Your reply goes here...." style="width:70%;"></textarea>
					<button 
						type="submit" 
						style="border:none;background-color:inherit;color:green;cursor:pointer"
						name="replyForm">
						REPLY
					</button>
				</form>
			</div>
		</div>
	</div>
	
<?php
$i++;
}
?>
</div>
<?php
}
else
{
?>
	<div style="width:100%;text-align:center">
		<h1 style="font-size:35px;color:#A7DDD6;">No Comments</h1>
	</div>
<?php
}
?>

<script>
	function toggleReplyForm(i)
	{
		<?php 
			if(!user_is_logged_in()){
			global $base_url;
		?>
			window.location = "<?php echo $base_url ?>/user";
		<?php
		}
		else
		{
		?>
			var replyForm = document.getElementById("replyForm"+i);
			if(replyForm.style.display === "none")
			{
				replyForm.style.display = "block";
			}
			else
			{
				replyForm.style.display = "none";
			}
		<?php
		}
		?>
	}
	
	function toggleReplyView(i)
	{
		var div = document.getElementById("replydiv"+i);
		if(div.style.display === "none")
		{
			div.style.display = "block";
		}
		else
		{
			div.style.display = "none";
		}
	}
</script>




<?php
	if(isset($_POST['replyForm']))
	{
		if(user_is_logged_in())
		{
			global $user;
			
			$uid   = $user->uid;
			$uname = $user->name;
			$umail = $user->mail;
			$cid = $_POST['commentid'];
			$replymsg = $_POST['replyMsg'];
			
			$minlength  = variable_get('fossee_forum_discussion_minlength');
			$maxlength  = variable_get('fossee_forum_discussion_maxlength');
			
			$limit      = $maxlength+1;
			$replymsg = trim(preg_replace('/\s+/',' ', $replymsg));
			$current_length = strlen($replymsg);
			
			if($current_length < $minlength)
			{
				drupal_set_message(t("Please enter a minimum of $minlength characters."), 'error');
			}
			elseif( $current_length > $maxlength)
			{
				drupal_set_message(t("Please enter lesss than $limit characters."), 'error');
			}
			else
			{
				$query = db_insert('fossee_forum_discussion_comment_replies')
            			->fields(array(
            			'parent_comment_id' => $cid,
            			'user_id' => $uid,
            			'user_name' => $uname,
            			'user_email' => $umail,
            			'reply_message' => $replymsg,
            			));
        			$query->execute();
        			
        			$results = db_query("select user_email from {fossee_forum_discussion_comments} where comment_id='$cid'");
				$comment_author_email = "";
			  	foreach($results as $result)
  				{
  					$comment_author_email.= $result->user_email;
  				}
        			
        			$filter = array($comment_author_email.",", $umail.",");
        			$all_user_emails = get_user_emails($cid);
        			$all_user_emails = str_replace($filter, '', $all_user_emails);
        			$all_user_emails = substr($all_user_emails, 0, -1);
        			
        			send_email_to_reply_author($umail, $replymsg);
        			send_email_to_forum_members($comment_author_email, $all_user_emails, $replymsg);
        			$current_path = url(current_path(), array('absolute' => TRUE));
				header('Location: '.$current_path);
			}
		}
	}
?>
