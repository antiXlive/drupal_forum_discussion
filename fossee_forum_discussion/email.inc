<?php

function fossee_forum_discussion_mail($key, &$message, $params)
{
	switch($key)
	{
		case 'comment_email_to_author':
			$message['subject'] = t("Your comment has been posted successfully.");
			$message['body'][]  = t("Comment message: @comment_msg", array('@comment_msg' => $params['comment_email_to_author']['comment_msg']));
			
			break;
		case 'reply_email_to_author':
			$message['subject'] = t("Your reply has been posted successfully.");
			$message['body'][]  = t("Reply message: @reply_msg", array('@reply_msg' => $params['reply_email_to_author']['reply_msg']));
			
			break;
		case 'reply_email_to_forum_members':
			$message['headers'] = $params['reply_email_to_forum_members']['headers'];
			$message['subject'] = t("New reply.");
			$message['body'][]  = t("A new reply message has been posted: @reply_msg", array('@reply_msg' => $params['reply_email_to_forum_members']['reply_msg']));
			
			break;
	}
}
