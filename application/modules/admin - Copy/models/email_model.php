<?php

require_once "./application/libraries/sendgrid/sendgrid.php";

class Email_model extends CI_Model 
{
	/*
	*	Send an email
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function send_mail($receiver, $sender, $message)
	{
		$this->load->library('email');

		$this->email->from($sender['email'], $sender['name']);
		$this->email->to($receiver['email']);
		
		$this->email->subject($message['subject']);
		$this->email->message($message['text']);
		
		$this->email->send();
		
		return $this->email->print_debugger();
		$this->email->clear();
	}
	/*
	*	Send an email
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function send_sendgrid_mail($receiver, $sender, $message, $attachment)
	{

		$sendgrid = new SendGrid('SG.WZgtvddLT-Kj8v5oYUKmAg.dDo-PsINDbNo7KKv-k7XbWtiO28mNREKoFYUgii9RKU');
		// $sendgrid = new SendGrid('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');
		// SG.WZgtvddLT-Kj8v5oYUKmAg.dDo-PsINDbNo7KKv-k7XbWtiO28mNREKoFYUgii9RKU
		$email = new SendGrid\Email();
		// var_dump($receiver); die();
		$email
			->addTo($receiver['email'], $receiver['name'])
			//->addTo('bar@foo.com') //One of the most notable changes is how `addTo()` behaves. We are now using our Web API parameters instead of the X-SMTPAPI header. What this means is that if you call `addTo()` multiple times for an email, **ONE** email will be sent with each email address visible to everyone.
			->setFrom($sender['email'])
			->setFromName($sender['name'])
			->setSubject($message['subject'])
			// ->setAttachments(array($attachment))
			//->setText('Hello World!')
			->setHtml($message['text'])
		;
		
		$res = $sendgrid->send($email);
		
		return $res;
	}
	/*
	*	Send an email
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function send_sendgrid_mail_no_attachment($receiver, $sender, $message)
	{
		$sendgrid = new SendGrid('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');
		//$sendgrid = new SendGrid('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');
		$email = new SendGrid\Email();
		$email
			->addTo($receiver['email'], $receiver['name'])
			//->addTo('bar@foo.com') //One of the most notable changes is how `addTo()` behaves. We are now using our Web API parameters instead of the X-SMTPAPI header. What this means is that if you call `addTo()` multiple times for an email, **ONE** email will be sent with each email address visible to everyone.
			->setFrom($sender['email'])
			->setFromName($sender['name'])
			->setSubject($message['subject'])
			//->setText('Hello World!')
			->setHtml($message['text'])
		;
		
		$res = $sendgrid->send($email);
		
		return $res;
	}
	/*
	*	Send an email via mandrill api
	*	@param string $user_email
	* 	@param string $user_name
	*	@param string $subject
	* 	@param string $message
	*	@param string $sender_email
	* 	@param string $shopping
	*	@param string $from
	* 	@param string $button
	* 	@param string $cc
	*
	*/
	function send_mandrill_mail($user_email, $user_name, $subject, $message, $sender_email = NULL, $shopping = NULL, $from = NULL, $button = NULL, $cc = NULL)
	{
		if(!isset($sender_email)){
			$sender_email = "info.omnis.co.ke";
		}
		if(!isset($shopping)){
			$shopping = "";
		}
		if(!isset($from)){
			$from = "Omnis Limited";
		}
		
		if(!isset($button)){
			//$button = '<a class="mcnButton " title="Confirm Account" href="http://www.intorelook.com.au" target="_blank" style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;">Shop Now</a>';
			$button = '';
		}
		
		$template_name = 'omnis';
		$template_content = array(
			array(
				'name' => 'salutation',
				'content' => $user_name
			),
			array(
				'name' => 'body',
				'content' => $message
			),
			array(
				'name' => 'sub-text',
				'content' => $shopping
			),
			array(
				'name' => 'button',
				'content' => $button
			)
		);
		$message = array(
			//'html' => '<p>Example HTML content</p>',
			'text' => $message,
			'subject' => $subject,
			'from_email' => $sender_email,
			'from_name' => $from,
			'to' => array(
				array(
				'email' => $user_email,
				'name' => $user_name,
				'type' => 'to'
			)
		),
		'headers' => array('Reply-To' => $sender_email),
		'important' => false,
		'track_opens' => null,
		'track_clicks' => null,
		'auto_text' => null,
		'auto_html' => null,
		'inline_css' => null,
		'url_strip_qs' => null,
		'preserve_recipients' => null,
		'view_content_link' => null,
		'bcc_address' => $cc,
		'tracking_domain' => null,
		'signing_domain' => null,
		'return_path_domain' => null,
		'merge' => true,
		'global_merge_vars' => array(
			array(
				'name' => 'merge1',
				'content' => 'merge1 content'
			)
		),
		'merge_vars' => array(
			array(
				'rcpt' => $sender_email,
				'vars' => array(
					array(
						'name' => 'merge2',
						'content' => 'merge2 content'
					)
				)
			)
		),
		'tags' => array('mandrill-mail'),
		'subaccount' => NULL, //'customer-123',
		'google_analytics_domains' => array('www.omnis.co.ke'),
		'google_analytics_campaign' => 'alvaromasitsa104@gmail.com',
		'metadata' => array('website' => 'www.omnis.co.ke'),
		'recipient_metadata' => array(
			array(
				'rcpt' => $sender_email,
				'values' => array('user_id' => 123456)
			)
		),
		/*'attachments' => array(
		array(
		'type' => 'text/plain',
		'name' => 'myfile.txt',
		'content' => 'ZXhhbXBsZSBmaWxl'
		)
		),*/
		'attachments' => NULL,
		'images' => NULL
		/*'images' => array(
		array(
		'type' => 'image/png',
		'name' => 'IMAGECID',
		'content' => 'ZXhhbXBsZSBmaWxl'
		)
		)*/
		);
		$async = false;
		$ip_pool = 'Main Pool';
		$send_at = date("H.i");
		
		$response = $this->mandrill->messages->sendTemplate($template_name, $template_content, $message);
		
		/*if($response == TRUE)
		{
			return TRUE;
		}
		
		else
		{
			return $response;
		}*/
		return $response;
	} 
}
?>