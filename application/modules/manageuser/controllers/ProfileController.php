<?php
    /*
     * class Template_IndexController
     * Date: 15-Oct-2015
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
 class Manageuser_ProfileController extends Zend_Controller_Action {
    /*
     * Date: 15-Oct-2015
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
		$date = new Zend_Date();
         $this->view->leftsection='manageuser';
         $this->userObj = new Manageuser_Model_Manageuser();
  		  $this->teamsObj = new Teams_Model_Teams();
		  $this->QueryObj = new Managequery_Model_Managequery();
		  $this->template = new Template_Model_Template();
		  date_default_timezone_set("Asia/Kolkata");
		  
		   if($this->userInfo->id==""){ 
            $this->_redirect('dashboard/');
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
		  if($this->getRequest()->getParam('ajaxify')==1)
	{
		 $this->_helper->layout->disableLayout();
	}
	    $this->view->websiteData=$this->template->getAllWebsite();
		$this->view->profileData=$this->userObj->getUserProfiles($this->userInfo->id); 
     }
  public function saveUserProfileAction()
  {	
$profile_id=$this->getRequest()->getParam('profile_id');
$profile_name=$this->getRequest()->getParam('profile_name');
$website=$this->getRequest()->getParam('website');
$website_email=$this->getRequest()->getParam('website_email');
$signature=$this->getRequest()->getParam('signature');
$arrayP=array(
"user_id"=>$this->userInfo->id,
"profile_name"=>$profile_name,
"website"=>$website,
"website_email"=>$website_email,
"signature"=>$signature,
);  
	

if($profile_id)
{
	$this->userObj->updateUserProfile($arrayP,$profile_id);
}
else
{
$this->userObj->insertUserProfile($arrayP);	
}

die;
  }  

public function getProfileInfoAction()
{
$id=$this->getRequest()->getParam('id');
$profileInfo=$this->userObj->getUserProfileInfo($id);
 
echo json_encode($profileInfo);die;	
}
}

