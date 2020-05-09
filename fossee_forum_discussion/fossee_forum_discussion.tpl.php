<?php 
if($comment)
{
?>
<div style="width:100%;display:flex;flex-direction:column">
<?php
$i = 1;
foreach($comment as $c)
{
?>
	<div style="margin-left:3%;width:95%;height:100%;margin-bottom:1%">
		<div style="border:1px solid #868686;border-radius:30px;">
			<p style="color:black;padding:0 1.5%;padding-top:1%;margin-bottom:1%">
				<?php print ($c[0]->comment); ?>
			</p>
		</div>
		<div style="width:95%;margin:0 auto;">
			<p style="display:inline-block;padding-left:1%;margin-bottom:1%">
				<a style="text-decoration:none;cursor:pointer;" onclick="toggleReplyForm(<?php print($i); ?>)">Reply</a>
			</p>
			<p style="display:inline-block;padding-left:3%;cursor:pointer;color:#015f8c" onclick="toggleReplyView(<?php print($i) ?>)">
				<?php print(get_reply_count($i)." Reply"); ?>
			</p>
			<p style="color:black;float:right;padding-right:2%;margin:0;font-size:13px;">
				<?php print ($c[0]->user_name); ?>
			</p>
			<div style="width:90%;margin:-2% auto 2% auto;display:none" id="replydiv<?php echo $i ?>">
				<?php
					$all_replies = get_replies($i);
					if($all_replies)
					{
						foreach($all_replies as $reply)
						{
							$replyMsg = $reply[0]->reply_message; 
							echo"<p>$replyMsg</p>";
						}
					}
				?>
			</div>
			
			<div style="width:75%;margin: 0 auto;margin-top:-1%;text-align:center;display:none;" id="replyForm<?php echo $i;?>">
				<form action="" method="POST">
					<input type="hidden" name="commentid" value="<?php print($i); ?>">
					<textarea rows="3" cols="50" name="replyMsg" placeholder="Your reply goes here...."></textarea>
					<button 
						type="submit" 
						style="border:none;background-color:inherit;color:green;cursor:pointer"
						name="replyForm">
						REPLY
					</button>
				</form>
			</div>
		</div>
		<!--<div style="border:1px solid black;width:90%;margin-left:4%;">
//			<?php 
//				$all_replies = get_replies($i);
//				if($all_replies)
//				{
//					print_r($all_replies[0][0]->reply_message);
//				}
//				else
//				{
//					print("N/A");
//				}
//			 ?>
		</div>-->
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
	<div style="border:1px solid #e4e4e4;height:100px;width:76%;">
		<div style="width:70%;height:30%;border:1px solid #e4e4e4;margin:1% 1%;"></div>
		<div style="width:70%;height:30%;border:1px solid #e4e4e4;margin:1% 1%;"></div>
	</div>
<?php
}
?>

<script>
	function toggleReplyForm(i)
	{
		//console.log("replyForm" +i);
		var replyForm = document.getElementById("replyForm"+i);
		if(replyForm.style.display === "none")
		{
			replyForm.style.display = "block";
		}
		else
		{
			replyForm.style.display = "none";
		}
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
	
//	function replysubmit(i)
//	{
//		var maxlength = <?php print(variable_get('fossee_forum_discussion_maxlength')); ?>;
//		var minlength = <?php print(variable_get('fossee_forum_discussion_minlength')); ?>;
//		var logged    = <?php print(user_is_logged_in()); ?>;
//		
//		var reply = document.getElementById("replyText"+i);
//		var replyMsg = reply.value;
//		reply.value = "";
//		var replyMsgFiltered = replyMsg.replace(/\s+/g, " ");
//		var currentLength = replyMsgFiltered.length;
//		var cid = document.getElementById("comment_id"+i).innerHTML;
//		cid = parseInt(cid);
//		
//		if(!logged)
//		{
//			console.log("NOT LOGGED IN");
//		}
//		else if(currentLength < minlength)
//		{
//			console.log("TOO SHORT");
//		}
//		else if(currentLength > maxlength)
//		{
//			console.log("TOO BIG");
//		}
//		else
//		{
//			var userid   = document.getElementById("userid").innerHTML;
//			var username = document.getElementById("username").innerHTML;
//			console.log("DONE");
//			console.log(cid);
//			console.log("USER ID "+userid);
//			console.log("USER NAME "+username);
//			<?php
//				echo("ooo");
//				$query = db_insert('fossee_forum_discussion_comment_replies')
//            			->fields(array(
//            			'parent_comment_id' => $_GET['cid'],
//              			'user_id'   => $_GET['userid'],
//              			'user_name' => $_GET['username'],
//              			'reply_message'   => $_GET['replyMsgFiltered'],
//            			));
//        			$query->execute();
//				drupal_set_message(t("DONE"));
//			?>
//		}
//		
//	}
</script>





<?php
	if(isset($_POST['replyForm']))
	{
		if(user_is_logged_in())
		{
			global $user;
			
			$uid = $user->uid;
			$uname = $user->name;
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
            			'reply_message' => $replymsg,
            			));
        			$query->execute();
        			drupal_set_message(t("DONE"));
        			$redirect = url(current_path(), array('absolute' => TRUE));
				header('Location: '.$redirect);
				drupal_set_message(t("DONE"));
			}
  			
		}
		else
		{
			drupal_set_message("You are not logged in.", "error");
		}
	}
?>



<?php

function get_reply_count($cid)
{
	$results = db_query("select * from {fossee_forum_discussion_comment_replies} where parent_comment_id='$cid'");
	$count = 0;
	foreach($results as $result)
	{
		$count++;
	}
	return $count;
}

function get_replies($cid)
{
	$results = db_query("select * from {fossee_forum_discussion_comment_replies} where parent_comment_id='$cid'");
	$output = array();
  	foreach($results as $result)
  	{
  		$output[] =array($result);
		//print_r($result);
  	}
  	return $output;
}
?>























