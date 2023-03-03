<?php
 class Signup_IndexController extends Zend_Controller_Action
 {
    public function init()
    {
        /* Initialize action controller here */
		//parent::init ();
		//$this->_helper->ViewRenderer->setViewSuffix('php');
		//Zend_Session::start();	
		$this->db = Zend_Registry::get('db');
		
		$this->_s = Zend_Registry::get('session');
          if ($this->_s->message) {
              $this->view->successmessage = $this->_s->message;
              $this->_s->message = NULL;
         }
		 
		 $this->userObj = new Manageuser_Model_Manageuser();
		 $this->_helper->layout->disableLayout();
    }
    public function indexAction()
    { 
	
	if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
 			{
 			  $name=$this->getRequest()->getParam('name');
 			  $username= $this->getRequest()->getParam('username');
 			  $password=$this->getRequest()->getParam('password');
 			  $email_id=$this->getRequest()->getParam('email_id');
 			  $mobile_no=$this->getRequest()->getParam('mobile_no');
    
			  $selectuser=$this->userObj->getUserCheck($username);
 			  $this->view->errMsg = '';
			   
 			  if(count($selectuser) > 0)
 			  {				  
 				  $this->_s->message ='<div class="error"><p><strong>Error Message - A user already exists with this username</strong></p></div>';
				$this->view->errMsg=1;
    			  }
  			 if($this->view->errMsg=='')
 			  {				
 			   $usertype = 'Lead Validator';			
 			   $array=array('name'=>$name,'username'=>$username,'password'=>$password,'email_id'=>$email_id,'mobile_no'=>$mobile_no,'user_type'=>$usertype,'created_on'=>time(),'status'=>0);	

			   $insert= $this->userObj->addUser($array);

 			 ////////////////////////////Mail//////////////////////////////////////////////////
	 $body = "Dear <b>".$name.",</b> <br/><br/>".
		"Your account has been created. Here are the <a href='".SITEURL."'>login</a> credentials. </b> <br/><br/>
Username: ".$username."<br/>
Password: ".$password."<br/><br/>".
 		"Thanks & Regards,<br />rapidcollaborate.com";
 	 $mail = new Zend_Mail();
$mail->setBodyHtml($body);
$mail->setFrom('support@rapidcollaborate.com', 'rapidcollaborate.com');
$mail->addTo($email_id, $name);
$mail->setSubject('Your Query management account created');
$mail->send();
	 //////////////////////////////////////////////////////////////////////////////// 
 			   $this->_s->message ='<div  style="color:#090; font-weight:bold;"><p>Thank you for your Registration.</p></div>';
          $this->_redirect('signup/');
			  }
 			  $this->view->params = $this->_getAllParams();

			}
	
   }
 
 public function resetpasswordAction()
{
if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
{
 $email_id=$this->getRequest()->getParam('email_id');
 $check=$this->userObj->CheckUserEmailID($email_id);
 if(count($check) > 0)
 {
 $name=$check['name'];
 $email_id=$check['email_id'];
 $id=$check['id'];
 $newpass=rand(111111,666666);
 $array=array("password"=>$newpass);
     $update=$this->userObj->updateUser($array,$id);
	 //////////////////////////////////////////////////////////////////////////////
	 $body = "Dear <b>".$name.",</b> <br/><br/>".
		"Please enter this new password:  <b>".$newpass."</b> <br/>
You can change your current password after logging into your account.<br/><br/>".
		"Thanks & Regards,<br />rapidcollaborate.com";
	 
	 $mail = new Zend_Mail();
$mail->setBodyHtml($body);
$mail->setFrom('support@rapidcollaborate.com', 'rapidcollaborate.com');
$mail->addTo($email_id, $name);
$mail->setSubject('Forgot Your Password');
$mail->send();
	 ////////////////////////////////////////////////////////////////////////////////
	 
 $this->_s->message ='<div  style="color:#090; font-weight:bold;"><p>An email with the new password has been sent</p></div>';	 $this->_redirect('resetpassword/');
 }
 else
 {
	 
 $this->_s->message ='<div class="error"><p><strong>No registered user exists related with this email address</strong></p></div>';	
 $this->_redirect('resetpassword/'); 
 }
	
}	
}

}