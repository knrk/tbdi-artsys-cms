<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/contact/public
 */
class Module_Contact extends Art_Abstract_Module {
	
	const VISIBLE_FIELDS = array('name','surname','company_name',
									'phone','mobile','email','mail_to',
									'street','city','zip','country',
									'fb_link','tw_link','gp_link','yt_link',
									'map');
	
	const REQUEST_CONTACTFORM = 'tUeRgnfcAp';
	const CONTACTFORM_SENT =  'contactform-sent';
	const CONTACTFORM_COUNT = 'contactform-count';
	
	function indexAction() 
	{
		$this->view->data = $this->getSettings();
		foreach(self::VISIBLE_FIELDS AS $field)
		{
			if( !isset( $this->view->data->$field ) )
			{
				$this->view->data->$field = NULL;
			}
		}
		
		Art_Template::setTitle(__('module_contact'));
		$this->view->social = $this->view->data->fb_link || $this->view->data->tw_link || $this->view->data->gp_link || $this->view->data->yt_link;
	}
	
	function embeddAction()
	{
		$this->view->name = "";
		if($this->getShowName())
		{
			$this->view->name = $this->getName();
		}
		
		$this->view->data = $this->getSettings();
		foreach(self::VISIBLE_FIELDS AS $field)
		{
			if( !isset( $this->view->data->$field ) )
			{
				$this->view->data->$field = NULL;
			}
		}
		
		$this->view->social = $this->view->data->fb_link || $this->view->data->tw_link || $this->view->data->gp_link || $this->view->data->yt_link;
	}
	
	
	function contactFormShortAction()
	{
		if( $this->getParams('status') == 'done' )
		{
			$this->setView('contactFormDone');
		}
		elseif( Art_Ajax::isRequestedBy(self::REQUEST_CONTACTFORM) )
		{				
			//Input email & message
			$data = Art_Ajax::getData();
			$response = Art_Ajax::newResponse();

			if( count($data) )
			{
				if( isset($data['email']) )
				{
					if( !Art_Validator::validate($data['email'], array(Art_Validator::IS_EMAIL)) )
					{
						$response->addMessage(__('module_contactform_email_wrong'),Art_Main::ALERT);
					}
				}
				else
				{
					$response->addMessage(__('module_contactform_email_empty'),Art_Main::ALERT);
				}

				if( !isset($data['message']) )
				{
					$response->addMessage(__('module_contactform_message_empty'),Art_Main::ALERT);
				}
				elseif( strlen($data['message']) < 20 )
				{
					$response->addMessage(__('module_contactform_message_short'),Art_Main::ALERT);
				}
			}
			else
			{
				$response->addMessage(__('module_contactform_form_empty'),Art_Main::ALERT);
			}
			
			if( !isset($data['fullname']) )
			{
				$data['fullname'] = '';
			}
				
			if( $time_rem = $this->_timeRemaining() )
			{
				$response->addMessage(sprintf(__('module_contactform_spam_alert'),$time_rem),Art_Main::ALERT);
			}

			
			if( $response->isValid() )
			{
				$this->_tagSender();
				
				$this->_contactFormSend($data['email'],$data['message'], $data['fullname']);
				$response->addVariable('contact-content', Art_Module::createAndRenderModule('contact','contactFormShort',array('status'=>'done')));
			}
			$response->execute();
		}
		else 
		{	
			$this->showAsWidget();

			$request = Art_Ajax::newRequest(self::REQUEST_CONTACTFORM);
			$request->setAction('/contact/contactformshort');
			$request->addUpdate('contact-content','.module_contact_contactFormShort',true);
			$this->view->request = $request;
		}
	}
	
	
	/**
	 *	Returns 0 if not spam, number if spam (number of minutes till next allowed sent)
	 */
	protected function _timeRemaining()
	{
		$time_rem = 0;
		if( Art_Session::get(self::CONTACTFORM_COUNT) )
		{
			$time_mnts = ( time() - Art_Session::get(self::CONTACTFORM_SENT) ) / 60;
			switch( Art_Session::get(self::CONTACTFORM_COUNT) )
			{
				case 1:
					if( $time_mnts < 2 )
					{
						$time_rem = 2 - floor($time_mnts);
					}
					break;
				case 2:
				case 3:
					if( $time_mnts < 5 )
					{
						$time_rem = 5 - floor($time_mnts);
					}
					break;
				default:
					if( $time_mnts < 10 )
					{
						$time_rem = 10 - floor($time_mnts);
					}
			}
		}
		
		return $time_rem;
	}
	
	
	protected function _tagSender()
	{
		if( !Art_Session::get(self::CONTACTFORM_COUNT) )
		{
			Art_Session::set(self::CONTACTFORM_SENT, time());
			Art_Session::set(self::CONTACTFORM_COUNT, 1);
		}
		else
		{
			Art_Session::set(self::CONTACTFORM_SENT, time());
			Art_Session::set(self::CONTACTFORM_COUNT, self::CONTACTFORM_COUNT + 1);
		}
	}
	
	
	protected function _contactFormSend($from_email, $message, $phone = NULL)
	{
		$domain = Art_Server::getDomain();
		$message = htmlentities($message);
		
		$subject = 'Zpráva z kontaktního formuláře '.$domain.' od '.$from_email;
		
		$body = '<h2>Nová zpráva z kontaktního formuláře</h2>
				<div style="font-size:18px;font-weight:bold">Od: <a href="mailto:'.$from_email.'">'.$from_email.'</a>'.(!empty($phone)?', '.$phone:'').'</div><br><br>
					'.$message.'<br><br><br><br><br>
				<div style="font-size:12px;font-style:italic;color:#666666">Tato zpráva byla odeslána prostřednictvím kontaktního formuláře na <a href="'.Art_Server::getHost().'">'.$domain.'</a></div>';
		
		$mail = Art_Mail::newMail();
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->addReplyTo($from_email);
		if (!empty($this->getSettings()->mail_to)) {
			$mail->addAddress($this->getSettings()->mail_to);	
		} else {
			$mail->addAddress(MAIL_CONTACT_DEFAULT);	
		}

		$mail->send();
	}	
}
