<?php
    /*
     * class Template_IndexController
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
 class teams_IndexController extends Zend_Controller_Action {
    /*
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
    public function init() {
        parent::init();
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
		//////////////////Flash message session ////////////////
        $this->_s = Zend_Registry::get('session');
        if ($this->_s->message) {
            $this->view->successmessage = $this->_s->message;
            $this->_s->message = NULL;
        }
		/////check User type
         $this->view->leftsection='manageuser';
          $this->teamsObj = new Teams_Model_Teams();
		  $this->userObj=$userObj = new Manageuser_Model_Manageuser();
  		   if(!isset($userInfo->id) || $userInfo->user_type=='Lead Validator'){ 
            $this->_redirect('dashboard/');
         }
		 
		  $this->view->userInfo = $this->userInfo =$userObj->getUserId($userInfo->id);
		  if(isset($this->userInfo->id) and $this->userInfo->id==178)
		 {
			$this->_redirect('managequery/userquery'); 
		 }
		  if($this->getRequest()->getParam('ajaxify')==1)
	   {
		 $this->_helper->layout->disableLayout();
	   }
     }
     /*
     * To list the Templates 
     * Date: 14-OCT-2015
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
    public function indexAction()
 	 { 
	 
		$this->view->teamsArr =$userfetch= $this->teamsObj->getAllTeams();
		if($this->getRequest()->getParam('Action')=="Delete")
	    {
		 $userid=$this->getRequest()->getParam('checkid');
 		 $delete=$this->teamsObj->DeleteTeams($userid);
		 $this->_redirect('teams');
	    }
		 
     }
       /*
      * Date: 15-April-2013
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * To update temeplate
      */
    public function addAction() 
 	{
		$this->view->mangerUsers=$this->userObj->fetchActiveUsers('sub-admin');
	    $this->view->userData=$this->userObj->fetchActiveUsers('Lead Validator');
	    
		if($this->getRequest()->getParam('id'))
 	    {
 		  $this->view->id=$id =$this->getRequest()->getParam('id');
 		 $this->view->teamInfo = $this->teamsObj->getTeamId($id);
	 
  	 }
		
  		if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
 			{
 			  $team_name=$this->getRequest()->getParam('team_name');
			  
			   if($this->getRequest()->getParam('team_manager'))
			  {
				$team_manager=implode(",",$this->getRequest()->getParam('team_manager'));  
			  }
			  else
			  {
				$team_manager="";  
			  }
			  
			 /* if($this->getRequest()->getParam('allocate_user'))
			  {
				$allocate_user=implode(",",$this->getRequest()->getParam('allocate_user'));  
			  }
			  else
			  {
				$allocate_user="";  
			  }
 			  */
 			  $array=array("team_name"=>$team_name,"team_manager"=>$team_manager);
			  
			  if($id)
			  {
			  $update=$this->teamsObj->updateTeams($array,$id);  
			  }
			  else
			  {
  			  $insert=$this->teamsObj->addTeams($array);
			  }
			  $this->_redirect('teams');
			}	

}

	
 
	

}