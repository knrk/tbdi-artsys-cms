<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 *  @uses PHPMailer External PHPMailer library
 *  @link https://github.com/PHPMailer/PHPMailer
 *  @example
    $mail = Mail::newMail();
    
    $mail->addReplyTo('reply.to@mail.com','Reply to name');
    $mail->addAddress('send.to@mail.com','Sender\'s name');
    $mail->Subject = ('Subject');
    $mail->Body = ('<b>HTML Body</b>');
    $mail->AltBody = ('Alternative body');
    $mail->addAttachment('images/mail.png');
    
    $mail->send();
 */
final class Art_Mail extends Art_Abstract_Component {
        
    /**
     *  @static
     *  @access private
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Global FROM mail adress
	 */
    protected static $_fromMail;
    
    /**
	 *	@static
	 *	@access protected
	 *	@var string Global FROM name
	 */
    protected static $_fromName;

    
    /**
     *  Set global FROM sender
	 * 
     *  @static
     *  @param string $mail sender FROM mail address
     *  @param string $name sender FROM name
     *  @return void
     *  @example Mail::setFrom('foo@bar.com','Foo Bar');
     */
    static function setFrom($mail,$name)
    {
        $this->_fromMail = $mail;
        $this->_fromName = $name;
    }
    
    
    /**
     *  Initialize the component
	 * 
     *  @static
     *  @return void
     */
    static function init()
    {
        if(parent::init())
        {
			require_once(ftest('extensions/phpmailer/external/PHPMailerAutoload.php'));
			require_once(ftest('extensions/phpmailer/art_phpmailer.php'));
		}
    }
    

    /**
     *  Returns new mail instance
     *  @static
	 *	@param string $mail_type [optional]
     *  @return Art_PHPMAILER Mail instance
     */
    static function newMail($mail_type = '')
    {
		//Lazy init
		static::init();
		
        $mail = new Art_PHPMailer($mail_type);
        
        //Use SMTP
        $mail->isSMTP();
        
        //Set logins from configuration file
		if( Art_Register::hasNamespace('mail') )
		{
			$register = Art_Register::in('mail');
			$mail->Host = $register->get('host');
			$mail->Username = $register->get('username');
			$mail->Password = $register->get('password');
			$mail->SMTPAuth = $register->get('smtp_auth') == 1;
			$mail->SMTPSecure = $register->get('smtp_secure');
			$mail->Port = $register->get('smtp_port');
			$mail->SMTPDebug = $register->get('debug');
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
		}
		else
		{
			trigger_error('Register namespace "mail" is not set - mails are not sent via SMTP');
		}
		
        //If global FROM sender is set, use it
        if(self::$_fromMail || self::$_fromName)
        {
            $mail->setFrom(self::$_fromMail,self::$_fromName);    
        }
        //Else use FROM sender from configuration file
        elseif( Art_Register::hasNamespace('mail') )
        {
            $mail->setFrom(Art_Register::in('mail')->get('from_mail'), Art_Register::in('mail')->get('from_name'));
        }
        
        $mail->Debugoutput = '';
    
        return $mail;
    }
	
	/**
	 * Creates a new mail based on a specific dump from the mail_dump table.
	 * 
	 * @static
	 * @param Art_Model_Mail_Dump $dump
	 * @return Art_PHPMailer
	 */
	static function newMailFromDump( Art_Model_Mail_Dump $dump ) 
	{
		$mail = static::newMail();
		$mail->loadDump($dump);
		return $mail;
	}
	
	
	/**
	 * Saves the mail into the table mail_dump and returns the dump.
	 * 
	 * @static
	 * @param Art_PHPMailer $mail
	 * @return Art_Model_Mail_Dump
	 */
	static function saveToDump(Art_PHPMailer $mail) 
	{
		return $mail->saveDump();
	}
}