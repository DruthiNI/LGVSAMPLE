<?php
 class Changepassword_IndexController extends Zend_Controller_Action
 {
    public function init()
    {
        /* Initialize action controller here */
		  parent::init();
        $this->view->userInfo = $this->userInfo = Zend_Auth::getInstance()->getStorage()->read();
		//$this->_helper->ViewRenderer->setViewSuffix('php');
		 Zend_Session::start();	
		$this->db = Zend_Registry::get('db');
		 $this->userObj = new Manageuser_Model_Manageuser();
		 
		  if(!isset($this->userInfo->id))
		  { 
            $this->_redirect('login/');
         }
		 
		  if(isset($this->userInfo->id) and $this->userInfo->user_type=='Consultant')
		  { 
             $layout = $this->_helper->layout();
		   $layout->setLayout('consultant-layout');
         }
		  
		 
		 
		 
     }
    public function indexAction()
 	 {  
        if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
 			{
 			  $old_password=$this->_getParam('old_password');
 			  $new_password= $this->_getParam('new_password');
   			  $getuserinfo= $this->userObj->getUserId($this->userInfo->id);
 			  
 			  if($getuserinfo['password']==$old_password)
 			  {
 				$array=array('password'=>$new_password);	
 				$update = $this->userObj->updateUser($array,$this->userInfo->id);
 				$this->view->errors ='<div style="color:#006600;">Your password has been  changed successfully.</strong></div>';  			  }
 			  else
 			  {
 			  $this->view->errors = "<div style='color:#CC0000;'>Please enter correct old password.</div>";
 			  }
 			}
 	}
 
}