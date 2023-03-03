<?php

    /*

     * class Cron_IndexController

     * Date: 14-Jan-2015

     * Developer: Niranjan Kumar

     * Modified By: Niranjan Kumar

     * 

     */



class Meetingschedule_IndexController extends Zend_Controller_Action {
  
     public function init() {       
         // $this->view->addScriptPath(COMMON_VIEW);
		 $this->db = Zend_Registry::get('db');
       $this->view->userInfo = $this->userInfo = Zend_Auth::getInstance()->getStorage()->read();
	   if(!isset($this->userInfo->id)){
             $this->_redirect('login/');
         } 
		  $this->_s = Zend_Registry::get('session');
		  
		   $this->smsObj = new Meetingschedule_Model_Meetingschedule();
		   $this->userObj = new Manageuser_Model_Manageuser();
  		   $this->QueryObj = new Managequery_Model_Managequery();
			date_default_timezone_set("Asia/Kolkata");
			
			 if($this->getRequest()->getParam('ajaxify')==1)
	   {
		 $this->_helper->layout->disableLayout();
	   }
       }
 
    public function indexAction()
	 {
		 if($this->userInfo->user_type=='admin')
		 {
		  $this->view->sheduleData=$sheduleData=$this->smsObj->FetchAllMeetingSchedule();
		 }
		 else if($this->userInfo->user_type=='sub-admin')
		 {
		  $this->view->sheduleData=$sheduleData=$this->smsObj->FetchSubadminMeetingSchedule($this->userInfo->allocated_to); 
		 }
		 else
		 {
		$this->view->sheduleData=$sheduleData=$this->smsObj->FetchUserMeetingSchedule($this->userInfo->id);	 
		 }
		 
 		if($this->getRequest()->getParam('Action')=="Delete")
	    {
		 $usershedule=$this->getRequest()->getParam('checkid');
 		 $delete=$this->smsObj->DeleteShedule($usershedule);
		  $this->_redirect('meetingschedule');
	    }
		
	 }
	 
	  public function addAction()
	 {
		if($this->userInfo->user_type=='admin')
		 {
		 $this->view->User=$this->userObj->getAllUsers();	
		$this->view->QueryData=$QueryData=$this->QueryObj->FetchAllQueryNotConvertedQuery();
		 }
		 else
		 {
	    $this->view->User=$this->userObj->getSubadminAllUsers($this->userInfo->allocated_to);	
		$this->view->QueryData=$QueryData=$this->QueryObj->FetchSubAdminNotConvertedQuery($this->userInfo->allocated_to);
 		 }
		  
		if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
 		{
		      $date=$this->getRequest()->getParam('date');
 			  $time= $this->getRequest()->getParam('time');
 			  $user_id=$this->getRequest()->getParam('user_id');
 			  $client_id=$this->getRequest()->getParam('client_id');
			  
			  $todaydate=date("m/d/Y",time());
			  
			  if($todaydate==$date)
			  {
				$status=1;  
			  }
			  else
			  {
				$status=0;  
			  }
			  
			  $userInfo=$this->userObj->getUserId($user_id);
			  $clientInfo=$this->QueryObj->getQueryId($client_id);
			  
			  $array=array("sender_user_id"=>$this->userInfo->id,'date'=>strtotime($date),'time'=>$time,'user_id'=>$user_id,'client_id'=>$client_id,'status'=>$status,'created_date'=>time());
			  
			 $template1='Dear '.$userInfo['name'].', We scheduled your meeting with Mr. '.$clientInfo['name'].' on '.$date.' at '.$time.' Regards emarketz.'; 
			  
			  $template2='Dear '.$clientInfo['name'].', We scheduled your meeting with Mr. '.$userInfo['name'].' on '.$date.' at '.$time.' Regards emarketz.'; 
			  
			  $insert=$this->smsObj->insert($array); 
			  $sent1=$this->smsObj->SentSMS($template1,$userInfo['mobile_no']);
			  $sent2=$this->smsObj->SentSMS($template2,$clientInfo['phone']);
			  
			  	$this->_redirect('meetingschedule/');	
		}
		 
	 }
 
}