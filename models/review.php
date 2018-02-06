<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *  @property int		$id_user
 *	@property string	$note
 *  @property string	$reply
 *	@property boolean	$seen
 *	@property boolean	$visible
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User		getUser()
*	@method this				setUser(Art_Model_User $user)
 */
class Review extends Art_Abstract_Model_DB {
    
    protected static $_table = 'review';
  
	protected static $_foreign = array('id_user');
	
	protected static $_link = array('user'			=> 'Art_Model_User');
	    	
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'id_user'		=>	array('select','insert','update'),
									'note'			=>	array('select','insert','update'),
									'reply'			=>	array('select','insert','update'),
									'seen'			=>	array('select','insert','update'),
									'visible'		=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}

