<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @author Tomáš Šujan <sujan@itart.cz>
 *  @package extensions/phpmailer
 *  @uses PHPMailer
 */
class Art_PHPMailer extends PHPMailer
{
	/**
	 * A short description of the mail content.
	 * 
	 * @var string 
	 * @example "newsletter", "registration_form", "order_confirmation", ...
	 */
	public $mail_type = '';
	
	public function __construct($mail_type = '', $exceptions = false)	
	{
		parent::__construct($exceptions);
		$this->mail_type = $mail_type;
	}
			
	/**
	 * Sends the mail.
	 * 
	 * @param bool $save_dump [optional] if true, saves the dump of the mail.
	 * @return this
	 */		
    function send($save_dump = true)
    {
        if( !parent::send() )
        {
            trigger_error("Error sending mail: ".$this->ErrorInfo, E_USER_WARNING);
        }
		
		if( $save_dump ) 
		{
			$this->saveDump();
		}
		
		return $this;
    }
	
	
	/**
	 * Converts a date from the SQL datetime format into the RFC822 format,
	 * as used by PHPMail.
	 * Is the inverse to rfcdate_to_sqldate.
	 * 
	 * @static
	 * @param string $sqldate
	 * @return string
	 */
	static function sqldateToRfcdate( $sqldate )
	{		
		$timestamp = strtotime($sqldate);
		$rfcdate = date("D, j M Y H:i:s O", $timestamp);
		return $rfcdate;
	}
	
	
	/**
	 * Converts a date from the RFC822 format into the SQL format.
	 * Is the inverse to sqldate_to_rfcdate.
	 * 
	 * @static
	 * @param string $rfcdate
	 * @return string
	 */
	static function rfcdateToSqldate( $rfcdate )
	{
		$timestamp = strtotime($rfcdate);
		$sqldate = date ("Y-m-d H:i:s", $timestamp);
		return $sqldate;
	}
	
	/**
	 * Loads the data from a corresponding mail_dump row and loads the data
	 * into this instance.
	 * 
	 * @param Art_Model_Mail_Dump $dump
	 * @return this
	 */
	function loadDump( Art_Model_Mail_Dump $dump )
	{
		if( $dump->isLoaded() ) 
		{
			$this->SetFrom($dump->from_address, $dump->from_name);
			$this->Body = $dump->body;
			$this->AltBody = $dump->alt_body;
			$this->Subject = $dump->subject;
			$this->mail_type = $dump->mail_type;
						
			
			foreach($dump->to_address as $tos)
			{
				$this->AddAddress($tos[0], $tos[1]);
			}
			foreach($dump->cc_addresses as $ccs)
			{
				$this->AddCC($ccs);
			}
			foreach($dump->bcc_addresses as $bccs)	
			{
				$this->AddBCC($bccs);
			}
			foreach($dump->reply_to_addresses as $reps)	
			{
				$this->AddReplyTo($reps[0], $reps[1]);
			}
			foreach($dump->attachments as $attach)
			{
				$this->addAttachment(
						$attach[0],
						$attach[2],
						$attach[3],
						$attach[4],
						$attach[6]
				);
			}
		}
		else
		{
			trigger_error("Error: Input mail dump has not been loaded.");
		}
		
		return $this;
	}
	
	
	/**
	 * Saves the data located in this instance into the database, as a new row
	 * in the mail_dump table. The instance is then returned.
	 * 
	 * @return Art_Model_Mail_Dump
	 */
	function saveDump()
	{
		$dump = new Art_Model_Mail_Dump();
		
		
		$dump->setUser( Art_User::getCurrentUser() ); 		
		$dump->mail_type = $this->mail_type;
		$dump->from_address = $this->From;
		$dump->from_name = $this->FromName;
		$dump->subject = $this->Subject;
		$dump->body = $this->Body;
		$dump->alt_body = $this->AltBody;
		$dump->to_address = $this->getToAddresses();
		$dump->cc_addresses = $this->getCCAddresses();
		$dump->bcc_addresses = $this->getBCCAddresses();
		$dump->reply_to_addresses = $this->getReplyToAddresses();
		$dump->attachments = $this->getAttachments();
		$dump->message_id = $this->MessageID;
		$dump->message_date = static::rfcdateToSqldate( $this->MessageDate );
		
		$dump->save();
		return $dump;
	}
}