<?php
 class Dashboard_AutoloadController extends Zend_Controller_Action
 {
     public function init()
     {
         /* Initialize action controller here */
 		//parent::init ();
 		//$this->_helper->ViewRenderer->setViewSuffix('php');
 		//Zend_Session::start();	
 		$this->db = Zend_Registry::get('db');
 		 
 		//////////////////Flash message session ////////////////
         $this->_s = Zend_Registry::get('session');
         if ($this->_s->message) {
             $this->view->successmessage = $this->_s->message;
             $this->_s->message = NULL;
         }
 		   $this->view->leftsection='message';
		   
		   $this->userObj = new Manageuser_Model_Manageuser();
		   $this->QueryObj = new Managequery_Model_Managequery();
		   $date = new Zend_Date();
     }
      public function indexAction()
     { 
	 $to = "niranjan.kumar@redmarkediting.com";
     $subject = "Test mail";
     $message = "Hello! This is a simple email message.";
     $from = "support@rapidcollaborate.com";
     $headers = "From:" . $from;
     mail($to,$subject,$message,$headers);
     echo "Mail Sent.";
	 die;
	 }
	 
	 
 }