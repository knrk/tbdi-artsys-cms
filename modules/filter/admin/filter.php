<?php
/**
 *  @package modules/filter/admin
 */
class Module_Filter extends Art_Abstract_Module {
	
	function embeddAction() {		
		
		$services = Service::fetchAllPrivileged();
		$this->view->services = $services;
		
		if  (NULL !== Art_Main::getPost()) {
			$this->view->data = Art_Main::getPost();
		
		}
	}

	function embeddCompanyAction() {

		$services = Service::fetchAllPrivileged();
		$this->view->services = $services;
		
		if (NULL !== Art_Main::getPost()) {
			$this->view->data = Art_Main::getPost();
		}

	}

	function embeddPaymentAction() {

		$services = Service::fetchAllPrivileged();
		$this->view->services = $services;

		if (NULL !== Art_Main::getPost()) {
			$this->view->data = Art_Main::getPost();
		}

	}
}