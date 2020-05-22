<?php

function get_ID_from_url()
{
	$current_path = url(current_path(), array('absolute' => TRUE));
	$current_path_splitted = explode('/', $current_path);
	$count = count($current_path_splitted);
	$a = $current_path_splitted[$count-2];
	$b = $current_path_splitted[$count-1];
	$forum_id = $a.'/'.$b;
	
	return $forum_id;
}


function getComments($forum_id)
{
	$results = db_query("select * from {fossee_forum_discussion_comments} where forum_id = '$forum_id'");
	$output = array();
  	foreach($results as $result)
  	{
  		$output[] =array($result);
  	}
  	
	return $output;
}


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
  	}
  	return $output;
}


function get_user_emails($cid)
{
	$results = db_query("select distinct user_email from {fossee_forum_discussion_comment_replies} where parent_comment_id='$cid'");
	$output = array();
	$emails = " ";
  	foreach($results as $result)
  	{
  		$output[] = $result->user_email;
  		$emails.= $result->user_email.",";
  	}
  	return $emails;
}


function send_email_to_reply_author($user_email, $replymsg)
{
	$params['reply_email_to_author']['reply_msg'] = $replymsg;
	$params['reply_email_to_author']['headers'] = array(
        	'From' => variable_get('fossee_forum_discussion_from_email'),
		'Cc' => variable_get('fossee_forum_discussion_cc_emails'),
		'Bcc' => variable_get('fossee_forum_discussion_bcc_emails'),
	);
	drupal_mail('fossee_forum_discussion', 'reply_email_to_author', $user_email, language_default(), $params);
}


function send_email_to_forum_members($comment_author_email, $all_user_emails, $replymsg)
{
	$bcc = variable_get('fossee_forum_discussion_bcc_emails');
	$all_user_emails = $all_user_emails.','.$bcc;
	$all_user_emails = str_replace(" ", "", $all_user_emails);	
	
	$params['reply_email_to_forum_members']['reply_msg'] = $replymsg;
	$params['reply_email_to_forum_members']['headers'] = array(
		'From' => variable_get('fossee_forum_discussion_from_email'),
		'Cc' => variable_get('fossee_forum_discussion_cc_emails'),
		'Bcc' => $all_user_emails
	);
	drupal_mail('fossee_forum_discussion', 'reply_email_to_forum_members', $comment_author_email, language_default(), $params);
}




