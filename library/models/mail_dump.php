<?php
/**
 *  @author Tomáš Šujan <sujan@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *  @property int		$id_user
 *	@property string	$mail_type
 *  @property string	$from_address
 *  @property string	$from_name
 *  @property string	$subject
 *	@property string	$body
 *  @property string	$alt_body
 *  @property int		$message_id
 *  @property string	$message_date
 *  @property array		$to_address			// JSON
 *  @property array		$cc_addresses		// JSON
 *  @property array		$bcc_addresses		// JSON
 *	@property array		$reply_to_addresses	// JSON
 *  @property array		$attachments		// JSON
 *	
 *  @TODO magic methods?
 */
class Art_Model_Mail_Dump extends Art_Abstract_Model_DB {
    
    protected static $_table = 'mail_dump';

	protected static $_link = array('user' => 'Art_Model_User');
	
	protected static $_foreign = array('id_user');
	
    protected static $_cols = array(
		'id'					=>  array('select','insert'),		  // UNIQUE KEY
		'id_user'				=>  array('select','insert'),		  // FOREIGN KEY
		'mail_type'				=>	array('select','insert','update'),
		'from_address'			=>  array('select','insert','update'),
		'from_name'				=>  array('select','insert','update'),
		'subject'				=>  array('select','insert','update'),
		'body'					=>  array('select','insert','update'),
		'alt_body'				=>  array('select','insert','update'),
		'message_id'			=>  array('select','insert','update'),
		'message_date'			=>  array('select','insert','update'),
		'to_address'			=>  array('select','insert','update'), // JSON
		'cc_addresses'			=>  array('select','insert','update'), // JSON
		'bcc_addresses'			=>  array('select','insert','update'), // JSON
		'reply_to_addresses'	=>  array('select','insert','update'), // JSON
		'attachments'			=>  array('select','insert','update'), // JSON

		'created_by'			=>	array('select','insert'),
		'modified_by'			=>	array('select','update'),
		'created_date'			=>	array('select'),
		'modified_date'			=>	array('select')
	);
	
	
    /**
     *  Create new model
	 *	If parameters are specified it also calls load
	 * 
     *  @param string|int|array|Art_Model_Db_Select|Art_Abstract_Model_Db $where Identifier or array of identifiers with they respective column names
	 *	@param bool|User $privileged
	 *	@param bool $active_only
	 *	@example load('3') Load article from database WHERE id_article = 3
	 *	@example load(array('name'=>'My foo article')) Load article with name = My foo article
     */
	public function __construct( $where = NULL, $privileged = NULL, $active_only = false ) 
	{
		parent::__construct( $where, $privileged, $active_only );
		$this->to_address			= json_decode( $this->to_address );
		$this->cc_addresses			= json_decode( $this->cc_addresses );
		$this->bcc_addresses		= json_decode( $this->bcc_addresses );
		$this->reply_to_addresses	= json_decode( $this->reply_to_addresses );
		$this->attachments			= json_decode( $this->attachments );
	}
	
	
    /**
     *  Save instance to DB
     *
     *  @return this
     */
	public function save() 
	{
		$original = array(
			$this->to_address,
			$this->cc_addresses,
			$this->bcc_addresses,
			$this->reply_to_addresses,
			$this->attachments
		);
		
		$this->to_address			= json_encode( $this->to_address );
		$this->cc_addresses			= json_encode( $this->cc_addresses );
		$this->bcc_addresses		= json_encode( $this->bcc_addresses );
		$this->reply_to_addresses	= json_encode( $this->reply_to_addresses );
		$this->attachments			= json_encode( $this->attachments );
		
		parent::save();
		
		$this->to_address			= $original[0];
		$this->cc_addresses			= $original[1];
		$this->bcc_addresses		= $original[2];
		$this->reply_to_addresses	= $original[3];
		$this->attachments			= $original[4];
		
		return $this;
	}
}