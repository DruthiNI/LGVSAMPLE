<?php
    /*
     * class Template_IndexController
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
 class followupsetting_IndexController extends Zend_Controller_Action {
    /*
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
    public function init() {
        parent::init();
        $this->view->userInfo = $this->userInfo = Zend_Auth::getInstance()->getStorage()->read();
		//////////////////Flash message session ////////////////
        $this->_s = Zend_Registry::get('session');
        if ($this->_s->message) {
            $this->view->successmessage = $this->_s->message;
            $this->_s->message = NULL;
        }
		/////check User type
         $this->view->leftsection='manageuser';
          $this->followupsettingObj = new Followupsetting_Model_Followupsetting();
		  $this->QueryObj = new Managequery_Model_Managequery();
  		   if(!isset($this->userInfo->id)){ 
            $this->_redirect('dashboard/');
         }
		 
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
 if($this->userInfo->user_type=='Lead Validator'){ 
            $this->_redirect('dashboard/');
         }

		 
		$this->view->priorityArr =$userfetch= $this->followupsettingObj->getAllPriority();
		if($this->getRequest()->getParam('Action')=="Delete")
	    {
		 $userid=$this->getRequest()->getParam('checkid');
 		 $delete=$this->followupsettingObj->DeletePriority($userid);
		 $this->_redirect('followupsetting');
	    }
		if($this->getRequest()->getParam('userid'))
	    {
		 $userid=$this->getRequest()->getParam('userid');
		 $status=$this->getRequest()->getParam('status');
		 $array=array('status'=>$status);
 		 $update= $this->followupsettingObj->updatePriority($array,$userid);
 		 $this->_redirect('followupsetting');
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
		 if($this->userInfo->user_type=='Lead Validator'){ 
            $this->_redirect('dashboard/');
         }
		 
		 
		if($this->getRequest()->getParam('id'))
 	    {
 		  $this->view->id=$id =$this->getRequest()->getParam('id');
 		 $this->view->priorityInfo = $this->followupsettingObj->getPriorityId($id);
	 
  	 }
		
  		if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
 			{
 			  $priority=$this->getRequest()->getParam('priority');
			  $follow_up_day= implode(',',$this->getRequest()->getParam('follow_up_day'));
  			  $contact_by= implode(',',$this->getRequest()->getParam('contact_by'));
 			  $array=array("priority"=>$priority,"follow_up_day"=>$follow_up_day,"contact_by"=>$contact_by);
			  
			  if($id)
			  {
			  $update=$this->followupsettingObj->updatePriority($array,$id);  
			  }
			  else
			  {
  			  $insert=$this->followupsettingObj->addPriority($array);
			  }
			  $this->_redirect('followupsetting');
			}	

}

	
 public function updateFollowupDateAction()
 {
	 if($this->getRequest()->getParam('fl_id'))
	 {
$fl_id =$this->getRequest()->getParam('fl_id');	
$assign_id =$this->getRequest()->getParam('assign_id');	
	 
$followup_day =$this->getRequest()->getParam('followup_day');		 
		 $update=$this->followupsettingObj->updateFollowupDate(array("followup_day"=>strtotime($followup_day)),$fl_id);

$this->QueryObj->updateExistUserStatusQuery(array('assign_follow_up_date'=>strtotime($followup_day)),$assign_id);
		 
echo date("d-m-Y",strtotime($followup_day));die;		 
	 }
	 echo "hi";
die;	 
 }
	

}