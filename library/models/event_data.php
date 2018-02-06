<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Event_Data {
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $_name;
	
	/**
	 * @access protected
	 * @var mixed
	 */
	protected $_data;
	
	/**
	 * @access protected
	 * @var int
	 */
	protected $_status_code;
	
	/**
	 * Number of all functions enqueued to event
	 * 
	 * @access protected
	 * @var int
	 */
	protected $_queued_num;
	
	/**
	 * Number of current function enqueued to event
	 * 
	 * @access protected
	 * @var int
	 */
	protected $_current_num = 1;
	
	
	/**
	 * 
	 * @param string $name
	 * @param mixed $data
	 * @param int $queued_num
	 */
	function __construct($name, $data, $status_code, $queued_num) 
	{
		$this->_name = $name;
		$this->_data = $data;
		$this->_status_code = $status_code;
		$this->_queued_num = $queued_num;
	}
	
	
	/**
	 * Get name of the event fired
	 * 
	 * @return string
	 */
	function getName()
	{
		return $this->_name;
	}
	
	
	/**
	 * Get event data submitted by caller
	 * 
	 * @return mixed
	 */
	function getData()
	{
		return $this->_data;
	}
	
	
	/**
	 * Get number of all functions bound to the event
	 * 
	 * @return int
	 */
	function getQueuedNum()
	{
		return $this->_queued_num;
	}
	
	
	/**
	 * Get current function call number
	 * 
	 * @return int
	 */
	function getCurrentNum()
	{
		return $this->_current_num;
	}
	
	
	/**
	 * Increment current function call number
	 * 
	 * @return this
	 */
	function nextCurrentNum()
	{
		++$this->_current_num;
		return $this;
	}
	
	
	/**
	 * Get event status code
	 * 
	 * @return int [Art_Main::OK, Art_Main::ALERT, Art_Main::ERROR]
	 */
	function getStatusCode()
	{
		return $this->_status_code;
	}
}