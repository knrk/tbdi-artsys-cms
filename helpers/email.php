<?php
/**
 *  @author Pastuszek Jakub <pastuszek@itart.cz>
 *  @package helpers
 * 
 *	Helper used for emails
 */
class Helper_Email extends Art_Abstract_Helper {
		
	const DEFAULT_EMAIL_ADDRESS	= MAIL_CONTACT_DEFAULT;
	const DEFAULT_EMAIL_NAME = MAIL_CONTACT_NAME_DEFAULT;

    private static $bcc_club_address = MAIL_BCC_TO;
		
	const EMAIL_TEMPLATE_AUTHORIZATION			= 'Authorization';
	const EMAIL_TEMPLATE_REGISTRATION			= 'Registration';
	const EMAIL_TEMPLATE_REGISTRATION_INFO		= 'Registration_info';
	const EMAIL_TEMPLATE_FORGOTTEN				= 'Forgotten';
	const EMAIL_TEMPLATE_GOT_APPLICATION		= 'Got_application';
	const EMAIL_TEMPLATE_GOT_APPLICATION_COMPANY= 'Got_application_company';
	const EMAIL_TEMPLATE_NOT_GOT_APPLICATION	= 'Not_got_application';
	const EMAIL_TEMPLATE_TERMINATE_APPLICATION	= 'Terminate_application';
	const EMAIL_TEMPLATE_MEMBERSHIP_EARLY_END	= 'Membership_early_end';
	const EMAIL_TEMPLATE_UNPAID_MEMBERSHIP		= 'Unpaid_membership';
	const EMAIL_TEMPLATE_UNMEMBER				= 'Unmember';
	const EMAIL_TEMPLATE_FORFEITED_MEMBERSHIP	= 'Forfeited_membership';
	
	const EMAIL_TEMPLATE_MAN_SERVICE_INTERTESTED	= 'Manager-Service_interested';
	const EMAIL_TEMPLATE_MAN_TERMINATE_APPLICATION	= 'Manager-Terminate_application';
	
	const EMAIL_TEMPLATE_IMPORT					= 'Import';
	
	// @todo Localize this!
	const DEAR_SIR		= 'Vážený pane ';
	const DEAR_MADAM	= 'Vážená paní ';
	
	/**
	 *	Send mail using template
	 * 
	 *  @param Art_Model_Email_Template	$template
	 *  @param string	$body	
	 *  @param string	$recepient	
	 *  @param array|string	$bccRecipients
	 *	@param boolean	$overload
	 *	@return void
	 */	
	static function sendMailUsingTemplate($template, $body, $recepient, $bccRecipients = NULL, $overload = false) {
		if (!MAIL) return;

		$mail = Art_Mail::newMail();
		if (!$overload) {

			if (is_array($bccRecipients)) {
				foreach ($bccRecipients as $value) {
					$mail->addBCC($value);
				}
			}
			elseif (is_string($bccRecipients)) {
				$mail->addBCC($bccRecipients);
			}
		}
		else {
			$bccs = null;
			
			if (is_array($bccRecipients)) {
				foreach ($bccRecipients as $value) {
					$bccs .= $value.', ';
				}
			}
			elseif (is_string($bccRecipients)) {
				$bccs .= $bccRecipients;
			}
		}
		
		if (!$overload) {
			$mail->addAddress($recepient);
		} else {
			$mail->addAddress(static::DEFAULT_EMAIL_ADDRESS);
			$body .= '<br>'.'Recipient: '.$recepient;
			
			if (NULL != $bccs) {
				$body .= '<br>'.'Bcc: '.$bccs;
			}
		}
		
		$mail->addReplyTo($template->from_email);
		$mail->setFrom($template->from_email, $template->from_name);
		$mail->Subject = $template->subject;
		$mail->Body = $body;

		if (empty($mail->getAllRecipientAddresses())) {
			return;
		}
		
		$mail->send();
	}
	
	
	/**
	 *	Send mail to selected recepients
	 * 
	 *  @param string[]	$recepients (email addresses)
	 *  @param string	$subject		
	 *  @param string	$body		
	 *	@return void
	 */	
	static function sendMail($recepients, $subject, $body) {
		if (!MAIL) return;

		$template = new Art_Model_Email_Template();
		
		$template->subject = $subject;
		$template->from_email = static::DEFAULT_EMAIL_ADDRESS;
		$template->from_name = static::DEFAULT_EMAIL_NAME;
		
		static::sendMailUsingTemplate($template, $body, static::DEFAULT_EMAIL_ADDRESS, $recepients);
	}
	
	
	/**
	 *	Send authorization mail to user
	 * 
	 *  @param Art_Model_User	$user
	 *	@return void
	 */
	static function sendAuthorizationMail($user) {
		if (!MAIL) return;

		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_AUTHORIZATION));
		if (!$mailTemplate->isLoaded()) return;
		
		$userData = $user->getData();
		$pass = Art_User::generatePassword();
		$salt = Art_User::generateSalt();
		
		$userData->password = Art_User::hashPassword($pass, $salt);
		$userData->salt = $salt;
		$userData->save();

		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);		
		$body = Art_Model_Email_Template::replaceIdentities(array('user_number' => $user->user_number,
																  'password' => $pass,
																  'footer' => $footer),
															$mailTemplate->body);
		static::sendMailUsingTemplate($mailTemplate, $body, $userData->email);
	}

	
	/**
	 *	Send registration mail to user
	 * 
	 *  @param string	$recepient (email address)
	 *  @param string	$pdf_url
	 *	@return void
	 */
	static function sendRegistrationMail($recepient, $pdf_url) {
		if (!MAIL) return;

		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_REGISTRATION));
		
		if (!$mailTemplate->isLoaded()) {
			return;
		}

		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('pdf_url' => $pdf_url,
																  'footer' => $footer),
															$mailTemplate->body);
		
		static::sendMailUsingTemplate($mailTemplate, $body, $recepient);
	}
	
	
	/**
	 *	Send registration info mail to company
	 * 
	 *  @param string	$recepient (email address)
	 *	@return void
	 */
	static function sendRegInfoMail($recepient) {
		if (!MAIL) return;

		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_REGISTRATION_INFO));
		if (!$mailTemplate->isLoaded()) {
			return;
		}

		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('footer' => $footer),
															$mailTemplate->body);
		static::sendMailUsingTemplate($mailTemplate, $body, $recepient);
	}
	
	
	/**
	 *	Send forgotten password mail to user
	 * 
	 *  @param string	$recepient (email address)
	 *  @param string	$hash		
	 *	@return void
	 */
	static function sendForgottenMail($recepient, $hash) {
		if (!MAIL) return;

		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_FORGOTTEN));
		if (!$mailTemplate->isLoaded()) {
			return;
		}
		
		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('hash' => $hash,
																  'footer' => $footer),
															$mailTemplate->body);
		
		static::sendMailUsingTemplate($mailTemplate, $body, $recepient);
	}
		
	
	/**
	 *	Send got application mail to user
	 * 
	 *  @param string	$recepient (email address)
	 *	@param int		$user_number
	 *	@param int		$membership_fee_annual
	 *	@return void
	 */
	static function sendGotApplicationMail($recepient, $user_number, $membership_fee_annual) {
		if (!MAIL) return;

		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_GOT_APPLICATION));
		if (!$mailTemplate->isLoaded()) {
			return;
		}

		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('user_number' => $user_number,
																  'membership_fee_annual' => $membership_fee_annual,
																  'footer' => $footer),
															$mailTemplate->body);
				
		static::sendMailUsingTemplate($mailTemplate, $body, $recepient);
	}
	
	
	/**
	 *	Send got application mail to company
	 * 
	 *  @param string	$recepient (email address)
	 *	@param int		$user_number
	 *	@param int		$membership_fee_annual
	 *	@return void
	 */
	static function sendGotApplicationCompanyMail($recepient, $user_number, $membership_fee_annual) {
		if (!MAIL) return;

		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_GOT_APPLICATION_COMPANY));
		if (!$mailTemplate->isLoaded()) return;

		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('user_number' => $user_number,
																  'membership_fee_annual' => $membership_fee_annual,
																  'footer' => $footer),
															$mailTemplate->body);
		static::sendMailUsingTemplate($mailTemplate, $body, $recepient);
	}
	
	
	/**
	 *	Send not got application mail to user
	 * 
	 *  @param string	$recepient (email address)
	 *	@param date		$reg_date
	 *	@param string	$hash
	 *	@return void
	 */
	static function sendNotGotApplicationMail($recepient, $reg_date, $hash) {
		if (!MAIL) return;

		$mailTemplate = new Art_Model_Email_Template(array('name'=>static::EMAIL_TEMPLATE_NOT_GOT_APPLICATION));
		if (!$mailTemplate->isLoaded()) return;

		$resource = Art_Server::getHost().'/resource/'.$hash;
		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('date_now' => nice_date('now'),
																  'date_registration' => nice_date($reg_date),
																  'pdf_url' => $resource,
																  'footer' => $footer),
															$mailTemplate->body);	
		static::sendMailUsingTemplate($mailTemplate, $body, $recepient);
	}

		
	/**
	 *	Send terminate application mail to user
	 * 
	 *  @param Art_Model_User	$user	
	 * 	@param boolean	$overload	
	 *	@return void
	 */
	static function sendTerminateApplicationMail($user, $overload = false) {		
		if (!MAIL || !$user->isLoaded()) return;
		
		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_TERMINATE_APPLICATION));
		if (!$mailTemplate->isLoaded()) return;
		
		$userData = $user->getData();
		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('footer'=>$footer),
															$mailTemplate->body);
		static::sendMailUsingTemplate($mailTemplate, $body, $userData->email, self::$bcc_club_address, $overload);
						
		////////////////////////////////////////
		//Send appropriate mail to manager too
		
		$manager = Helper_TBDev::getManagerForUser($user);
		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_MAN_TERMINATE_APPLICATION));
		
		if (!$mailTemplate->isLoaded()) return;
		
		static::sendMailUsingTemplate($mailTemplate, $mailTemplate->body, $manager->getData()->email);
	}
	
	
	/**
	 *	Send membership early end mail to user
	 * 
	 *  @param Art_Model_User	$user
	 *	@param boolean	$overload		
	 *	@return void
	 */
	static function sendMembershipEarlyEndMail($user, $overload = false) {	
		if (!MAIL || !$user->isLoaded()) return;
		
		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_MEMBERSHIP_EARLY_END));
		if (!$mailTemplate->isLoaded()) return;

		$userData = $user->getData();
		if ($userData->gender) {
			$genderEnd = '';
			$salutation = static::DEAR_SIR.$userData->salutation.',';
		}
		else {
			$genderEnd = 'a';
			$salutation = static::DEAR_MADAM.$userData->salutation.',';
		}
		 
		$membership_to = Helper_TBDev::getMembershipToForUser($user);
		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('salutation' => $salutation,
																  'membership_to' => nice_date($membership_to),
																  'genderEnd' => $genderEnd,
																  'footer' => $footer),
															$mailTemplate->body);
		static::sendMailUsingTemplate($mailTemplate, $body, $userData->email, self::$bcc_club_address, $overload);
	}

	
	/**
	 *	Send unpaid membership mail to user
	 * 
	 *  @param Art_Model_User	$user		
	 * 	@param boolean	$overload
	 *	@return void
	 */
	static function sendUnpaidMembershipMail($user, $overload = false) {	
		if (!MAIL || !$user->isLoaded()) return;
		
		$mailTemplate = new Art_Model_Email_Template(array('name' => static::EMAIL_TEMPLATE_UNPAID_MEMBERSHIP));
		if (!$mailTemplate->isLoaded()) return;
		
		$userData = $user->getData();
		if ($userData->gender) {
			$genderEnd = '';
			$salutation = static::DEAR_SIR.$userData->salutation.',';
		} else {
			$genderEnd = 'a';
			$salutation = static::DEAR_MADAM.$userData->salutation.',';
		}
		
		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('salutation' => $salutation,
																  'date_now' => nice_date('now'),
																  'genderEnd' => $genderEnd,
																  'footer' => $footer),
															$mailTemplate->body);
		static::sendMailUsingTemplate($mailTemplate, $body, $userData->email, self::$bcc_club_address, $overload);
	}
		
		
	/**
	 *	Send unmember mail to user
	 * 
	 *  @param Art_Model_User	$user		
	 *	@return void
	 */
	static function sendUnmemberMail($user) {	
		if (!MAIL || !$user->isLoaded()) return;
		
		$mailTemplate = new Art_Model_Email_Template(array(
			'name' => static::EMAIL_TEMPLATE_UNMEMBER)
		);
		
		if (!$mailTemplate->isLoaded()) {
			return;
		}
		
		$userData = $user->getData();
		if ($userData->gender) {
			$genderEnd = '';
			$salutation = static::DEAR_SIR.$userData->salutation.',';
		} else {
			$genderEnd = 'a';
			$salutation = static::DEAR_MADAM.$userData->salutation.',';
		}
		
		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('salutation' => $salutation,
																  'date_termination' => nice_date('now'),
																  'genderEnd' => $genderEnd,
																  'footer' => $footer),
															$mailTemplate->body);
		static::sendMailUsingTemplate($mailTemplate, $body, $userData->email);
	}
	
		
	/**
	 *	Send forfeited membership mail to user
	 * 
	 *  @param Art_Model_User	$user	
	 * 	@param boolean	$overload	
	 *	@return void
	 */
	static function sendForfeitedMembershipMail($user, $overload = false) {	
		if (!MAIL || !$user->isLoaded()) return;
		
		$mailTemplate = new Art_Model_Email_Template(array(
			'name' => static::EMAIL_TEMPLATE_FORFEITED_MEMBERSHIP)
		);
		
		if (!$mailTemplate->isLoaded()) return;
		
		$userData = $user->getData();
		$membershipTo = Helper_TBDev::getMembershipToForUser($user);
		$plusMonth = date('Y-m-d', strtotime('+1 months', strtotime($membershipTo)));
		$plus3Months = date('Y-m-d', strtotime('+3 months',strtotime($membershipTo)));
		
		if ($userData->gender) {
			$genderEnd = '';
			$salutation = static::DEAR_SIR.$userData->salutation.',';
		} else {
			$genderEnd = 'a';
			$salutation = static::DEAR_MADAM.$userData->salutation.',';
		}
		
		$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
		$body = Art_Model_Email_Template::replaceIdentities(array('salutation' => $salutation,
																  'date_now' => nice_date('now'),
																  'genderEnd' => $genderEnd,
																  'date_membership_to' => nice_date($membershipTo),
																  'date_plus_month' => nice_date($plusMonth),
																  'date_plus_3_months' => nice_date($plus3Months),
																  'footer' => $footer),
															$mailTemplate->body);
		static::sendMailUsingTemplate($mailTemplate, $body, $userData->email, self::$bcc_club_address, $overload);
	}

	/**
	 *	Send service interested mail to manager
	 * 
	 *  @param Art_Model_User	$user
	 *  @param Service	$service		
	 *	@return void
	 */
	static function sendManServiceInterestedMail($user, $service) {	
		if (!MAIL || !$user->isLoaded() || NULL === $service) return;
		
		$mailTemplate = new Art_Model_Email_Template(array(
			'name' => static::EMAIL_TEMPLATE_MAN_SERVICE_INTERTESTED)
		);
		
		if (!$mailTemplate->isLoaded()) return;
		
		$userData = $user->getData();
		$telephone = Helper_TBDev::getTelephoneForUser($user);
		$manager = Helper_TBDev::getManagerForUser($user);
		$body = Art_Model_Email_Template::replaceIdentities(array(
				'fullnameWithDegree' => $userData->fullnameWithDegree,
				'email' => $userData->email,
				'telephone' => $telephone
			),
			$mailTemplate->body
		);
		
		static::sendMailUsingTemplate($mailTemplate, $body, $manager->getData()->email);
	}
		
	
	/**
	 *	Send import mail
	 * 	
	 *  @param Art_Model_User[]	$users	
	 *	@return void
	 */	
	static function sendImportMail(array $users) {
		if (!MAIL) return;
		foreach ($users as $value) {
			$user = new Art_Model_User($value);
			$userData = $user->getData();
			$email = $userData->email;

			if (NULL == $email) continue;
			
			$mail = Art_Mail::newMail();
			$template = new Art_Model_Email_Template(array('name'=>static::EMAIL_TEMPLATE_IMPORT));
			if (!$template->isLoaded()) return;
			
			$mail->addReplyTo($template->from_email);
			$mail->addAddress($email);
			$mail->setFrom($template->from_email, $template->from_name);

			$password = Art_User::generatePassword();
			$userData->salt = $salt = Art_User::generateSalt();
			$userData->password = Art_User::hashPassword($password, $salt);
			$userData->save();

			$mail->Subject = $template->subject;
			$footer = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_MAIL_FOOTER);
			$mail->Body = Art_Model_Email_Template::replaceIdentities(array(
					'password' => $password,
					'email' => $email,
					'footer' => $footer
				),
				$template->body
			);
			$mail->send();
		}
	}
		
	
	/**
	 *	Send report mail
	 * 
	 *  @param array	$object		
	 *	@return void
	 */
	static function sendReportMail($object) {	
		if (!MAIL || NULL === $object) return;

		$template = new Art_Model_Email_Template();
		
		$template->subject = 'TBDI - Daily report';
		$template->from_email = static::DEFAULT_EMAIL_ADDRESS;
		$template->from_name = static::DEFAULT_EMAIL_NAME;

		if (NULL !== $object->data) {
			$body = $object->data;
		} else {
			$body = 'Dnes se neodeslaly žádné automatické emaily';
		}
		
		static::sendMailUsingTemplate($template, $body, static::DEFAULT_EMAIL_ADDRESS);
	}
}