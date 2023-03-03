<?php
    /*
     * class Template_IndexController
     * Date: 15-Oct-2015
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
 class Manageuser_IndexController extends Zend_Controller_Action {
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
		  date_default_timezone_set("Asia/Kolkata");
		  
		  if(isset($this->userInfo->id) and $this->userInfo->id==178)
		 {
			$this->_redirect('managequery/userquery'); 
		 }
		 
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
		 
		 if(!isset($this->userInfo->id) || $this->userInfo->user_type=='Lead Validator'){ 
            $this->_redirect('dashboard/');
         } 
		 $myTeamUserData= $this->userObj->getAllUsersMyTeams($this->userInfo->team_id);
		 
		 if(isset($this->userInfo->id) and $this->userInfo->user_type=='admin')
      {
		$this->view->userArr =$userfetch= $this->userObj->getAllUsers();
	  }
	  else
	  {
		  $this->view->userArr =$userfetch= $this->userObj->getSubadminAllUsers(implode(",",$myTeamUserData)); 
	  }
		
		
  		if($this->getRequest()->getParam('Action')=="Delete")
	    {
		 $userid=$this->getRequest()->getParam('checkid');
 		 $delete=$this->userObj->DeleteUser($userid);
		  $this->_redirect('manageuser');
	    }

		

		if($this->getRequest()->getParam('userid'))
	    {
		 $userid=$this->getRequest()->getParam('userid');
		 $status=$this->getRequest()->getParam('status');
		 
		  $array=array('status'=>$status);
 		  $update= $this->userObj->updateUser($array,$userid);
 		  $this->_redirect('manageuser');

	    }
     }
       /*
      * Date: 15-April-2013
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * To update temeplate
      */
    public function adduserAction() 
 	{
		if($this->getRequest()->getParam('ajaxify')==1)
	    {
		 $this->_helper->layout->disableLayout();
	    }
	$this->view->WebsiteData=$this->QueryObj->fetchAllWebsite();
	$this->view->teamsArr = $this->teamsObj->getAllTeams();
	
		 if(!isset($this->userInfo->id) || $this->userInfo->user_type=='Lead Validator'){ 
            $this->_redirect('dashboard/');
			 
        }
		
  			if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
 			{
 			  $name=$this->getRequest()->getParam('name');
 			  $username= $this->getRequest()->getParam('username');
 			  $password=$this->getRequest()->getParam('password');
 			  $email_id=$this->getRequest()->getParam('email_id');
 			  $mobile_no=$this->getRequest()->getParam('mobile_no');
 			  $user_type=$this->getRequest()->getParam('user_type');
 			  $category=$this->getRequest()->getParam('category');
 			  //$set_order=$this->getRequest()->getParam('set_order');
 			  //$campaign_type=$this->getRequest()->getParam('campaign_type');
 			  $team_id=implode(",",$this->getRequest()->getParam('team_id'));
			 
			  if($this->getRequest()->getParam('sms_notify'))
			  {
			  $sms_notify=$this->getRequest()->getParam('sms_notify');
			  }
			  else
			  {
			  $sms_notify="";  
			  }
			  
			   if($this->getRequest()->getParam('whatsaap_notification'))
			  {
			  $whatsaap_notification=$this->getRequest()->getParam('whatsaap_notification');
			  }
			  else
			  {
			  $whatsaap_notification="";  
			  }
			  
 			  $selectuser=$this->userObj->getUserCheck($username);
 			  $this->view->errors = '';
  			  if($selectuser['username'])
 			  {				  
 				  $this->view->errors ='<p><strong>Error Message - A user already exists with this username  address.</strong></p>';
     			  }
  			 if($this->view->errors=='')
 			  {				
 			   $usertype = 'Lead Validator';			
 			   $array=array('name'=>$name,'username'=>$username,'password'=>$password,'email_id'=>$email_id,'mobile_no'=>$mobile_no,'user_type'=>$user_type,'team_id'=>$team_id,'sms_notify'=>$sms_notify,'whatsaap_notification'=>$whatsaap_notification,'category'=>$category, 'created_on'=>time());	
			   
			     $insert= $this->userObj->addUser($array);
			   
			   ////////////////Insert Profile/////////////////////
					 $profile_name=$this->getRequest()->getParam('profile_name');
				  $website=$this->getRequest()->getParam('website');
				  $website_email=$this->getRequest()->getParam('website_email');
				  $signature=$this->getRequest()->getParam('signature');
				 if(count($profile_name) > 0 and $user_type=='Lead Validator')
				 {
				  for($i=0; $i<count($profile_name); $i++)
				  {
					  if($profile_name[$i])
					  {
						$arrayP=array("user_id"=>$insert,"profile_name"=>$profile_name[$i],"website"=>$website[$i],"website_email"=>$website_email[$i],"signature"=>$signature[$i]);  
						 $this->userObj->insertUserProfile($arrayP);
					  }
				  }					  
				 }
			   
			   
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
 			   $this->_s->message ='<div class="msg msg-ok"><p>Thank you for Registering With <strong>'.$username.'</strong></p><a href="#" class="close">close</a></div>';
          $this->_redirect('manageuser/');
			  }
 			  $this->view->params = $this->_getAllParams();

			}

}
 
	public function edituserAction()
	{
	  if($this->getRequest()->getParam('ajaxify')==1)
	  {
		 $this->_helper->layout->disableLayout();
	  }
	$this->view->teamsArr = $this->teamsObj->getAllTeams();
	$this->view->WebsiteData=$this->QueryObj->fetchAllWebsite();
	
	 if(!isset($this->userInfo->id) || $this->userInfo->user_type=='Lead Validator'){ 
            $this->_redirect('dashboard/');
			 
        }	
		 
		
      $this->view->ActiveUsers=$this->userObj->fetchActiveUsers();
    if($this->getRequest()->getParam('userid'))
 	    {
 		  $userid =$this->getRequest()->getParam('userid');
 		  $this->view->userIntro = $this->userObj->getUserId($userid);
 		  $this->view->userProfile = $this->userObj->getUserProfile($userid);
		  
		  
  	 }
 	 
	 if($this->getRequest()->getParam('notification')==1)
	 {
		 $array12=array('notification'=>1); 
 		 $this->userObj->updateUser($array12,$userid);
	 }
  			
			
			
			if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
 			{
  			  $name=$this->getRequest()->getParam('name');
 			  $username= $this->getRequest()->getParam('username');
 			  $password=$this->getRequest()->getParam('password');
 			  $email_id=$this->getRequest()->getParam('email_id');
 			  $mobile_no=$this->getRequest()->getParam('mobile_no');
 			  $user_type=$this->getRequest()->getParam('user_type');
 			  $category=$this->getRequest()->getParam('category');
 			  //$set_order=$this->getRequest()->getParam('set_order');
 			  //$campaign_type=$this->getRequest()->getParam('campaign_type');
			 /* if($this->getRequest()->getParam('allocated_to'))
			  {
			  $allocated_to=implode(",",$this->getRequest()->getParam('allocated_to'));
			  }
			  else
			  {
			   $allocated_to="";  
			  }
			  */
			  $team_id=implode(",",$this->getRequest()->getParam('team_id'));
			  
 			   
			  if($this->getRequest()->getParam('sms_notify'))
			  {
			  $sms_notify=$this->getRequest()->getParam('sms_notify');
			  }
			  else
			  {
			  $sms_notify="";  
			  }
			   if($this->getRequest()->getParam('whatsaap_notification'))
			  {
			  $whatsaap_notification=$this->getRequest()->getParam('whatsaap_notification');
			  }
			  else
			  {
			  $whatsaap_notification="";  
			  }
			  
			  $download_option=$this->getRequest()->getParam('download_option');
			  $download_all=$this->getRequest()->getParam('download_all');
			  
			  $old_password=$this->getRequest()->getParam('old_password');
			  
			  if($old_password!=$password)
			  {
			   	  //////////////////////////////////////////////////////////////////////////////
	 $body = "Dear <b>".$name.",</b> <br/><br/>".
		"The password was reset by the ".$username." </b> <br/>
New password : ".$password."<br/><br/>".
		"Thanks & Regards,<br />rapidcollaborate.com";
	 
	 $message = array(
        'html' => $body,
        'subject' => 'Password reset by admin',
        'from_email' => 'support@rapidcollaborate.com',
        'from_name' => 'Rapidcollaborate.com',
        'to' => array(
            array(
            	'email'=>$email_id,
            	'name' => $name, 
            	'type' => 'to'
            )
        )
    );
$mailsent=SendMandrilMail($message);
	 
	 /*$mail = new Zend_Mail();
$mail->setBodyHtml($body);
$mail->setFrom('support@rapidcollaborate.com', 'rapidcollaborate.com');
$mail->addTo($email_id, $name);
$mail->setSubject('Password reset by admin');
$mail->send();*/
	 //////////////////////////////////////////////////////////////////////////////// 
			  }
 			  
  			  $this->view->errors='';
 				if($this->view->errors==''){				
 				 $array=array('name'=>$name,'password'=>$password,'email_id'=>$email_id,'mobile_no'=>$mobile_no,'user_type'=>$user_type,'category'=>$category, 'sms_notify'=>$sms_notify,'whatsaap_notification'=>$whatsaap_notification,'download_option'=>$download_option,'download_all'=>$download_all,'team_id'=>$team_id);
				 
				 //print_r($array);die;
				  
 					$update = $this->userObj->updateUser($array,$userid);
					
					///////////////////update Profile////////////////////////
					$old_profile_name=$this->getRequest()->getParam('old_profile_name');
					$old_website=$this->getRequest()->getParam('old_website');
				    $old_website_email=$this->getRequest()->getParam('old_website_email');
				    $old_signature=$this->getRequest()->getParam('old_signature');
					$profile_id=$this->getRequest()->getParam('profile_id');
					
					if(count($old_profile_name) > 0 and $user_type=='Lead Validator')
				 {
				  for($j=0; $j<count($old_profile_name); $j++)
				  {
					  if($old_profile_name[$j])
					  {
						$arrayP=array("profile_name"=>$old_profile_name[$j],"website"=>$old_website[$j],"website_email"=>$old_website_email[$j],"signature"=>$old_signature[$j]);  
						 $this->userObj->updateUserProfile($arrayP,$profile_id[$j]);
					  }
				  }					  
				 }
					 
					////////////////Insert Profile/////////////////////
					 $profile_name=$this->getRequest()->getParam('profile_name');
				  $website=$this->getRequest()->getParam('website');
				  $website_email=$this->getRequest()->getParam('website_email');
				  $signature=$this->getRequest()->getParam('signature');
				 if(count($profile_name) > 0)
				 {
				  for($i=0; $i<count($profile_name); $i++)
				  {
					  if($profile_name[$i])
					  {
						$arrayP=array("user_id"=>$userid,"profile_name"=>$profile_name[$i],"website"=>$website[$i],"website_email"=>$website_email[$i],"signature"=>$signature[$i]);  
						 $this->userObj->insertUserProfile($arrayP);
					  }
				  }					  
				 }
			////////////////////////////////////////////////////////////
    $this->_s->message ='<div class="msg msg-ok"><p>User information has been updated successfully</p><a href="#" class="close">close</a></div>';
          $this->_redirect('manageuser/');
				}

			   

			}
 
	}
	
	public function exportAction()
	{
	$this->_helper->layout->disableLayout();
		$checkid=$this->_getParam('checkid');
		$this->view->userArr =$Queryfetch= $this->userObj->getAllCheckedExportUser($checkid);
		$file="user_".date("d-m-Y",time()).".xls";
header('Content-Type: text/html');
header("Content-type: application/x-msexcel"); //tried adding  charset='utf-8' into header
header("Content-Disposition: attachment; filename=$file");	
	}
 

	public function profileAction()
 	{ 
	 if($this->getRequest()->getParam('ajaxify')==1)
	   {
		 $this->_helper->layout->disableLayout();
	   }
		$this->view->leftsection='setting';

		$userObj = new User_Model_User();

		$userinfo= $userObj->getUserById($this->userInfo->id);

		$this->view->user_fname=$userinfo['user_fname'];

		$this->view->user_lname=$userinfo['user_lname'];

		$this->view->user_email=$userinfo['user_email'];

		$this->view->mobile=$userinfo['mobile'];

		$this->view->address=$userinfo['address'];

		$this->view->dob=$userinfo['dob'];

		$this->view->gender=$userinfo['gender'];

		

		if($this->getRequest()->isPost('submituser'))

			{

			  $first_name=$this->getRequest()->getParam('first_name');

			  $last_name= $this->getRequest()->getParam('last_name');

			  $mobile=$this->getRequest()->getParam('mobile');

			  $email_id=$this->getRequest()->getParam('email_id');

			  $address= $this->getRequest()->getParam('address');

			  $gender=$this->getRequest()->getParam('gender');

			  $dob=$this->getRequest()->getParam('dob');

			  

			  $array=array('user_email'=>$email_id,'user_fname'=>$first_name,'user_lname'=>$last_name,'mobile'=>$mobile,'address'=>$address,'gender'=>$gender,'dob'=>$dob);	

					$update = $userObj->updateUser($array,$this->userInfo->id);

					$this->_s->successmessage ='<div style="color:#006600;">User information has been updated successfully.</strong></div>';
  
			}
 	}

public function stickynotesAction()
{
      $this->_helper->layout->disableLayout();

            if($this->getRequest()->isPost('submitBtn'))
 			{
 			  $notes=$this->getRequest()->getParam('notes');
			  $array=array('user_id'=>$this->userInfo->id,'notes'=>$notes,'date'=>time());
 			  $insert = $this->userObj->AddNotes($array);
			  echo JS_REDIRECT;
			 // $this->_redirect(SITEURL.'manageuser/index/stickynotes');
			}
			
			if($this->getRequest()->getParam('noteid'))
			{
			$delete = $this->userObj->DeleteNotes($this->getRequest()->getParam('noteid'));	
				
			}
			
	$this->view->notes = $this->userObj->fetchNotes($this->userInfo->id);		
			
			
 	
 }	

public function deletestickynotesAction()
{
	
	  $id=$this->getRequest()->getParam('id');
	$delete = $this->userObj->DeleteNotes($id);
	die;
}

public function messageAction()
{
	if($this->getRequest()->getParam('ajaxify')==1)
	{
		 $this->_helper->layout->disableLayout();
	}
$this->view->userArr =$userfetch= $this->userObj->getAllUsers();
 if($this->userInfo->user_type!="admin")
{ 
 $array=array('status'=>1);
 $update=$this->userObj->UpdateMessageUserNotify($array,$this->userInfo->id);
 $fetchData=$this->userObj->FetchMessage($this->userInfo->id);
 
  $str='<div class="col-md-12">
           <div class="box box-primary direct-chat direct-chat-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Message</h3>
              </div>
             <div class="box-body">
               <div class="direct-chat-messages">';
               
               foreach($fetchData as $message)
           {  
          $userData= $this->userObj->getUserId($message['sender_id']);
               if($message['sender_id']==$this->userInfo->id)
			   {
				$right='left'; 
				$name='right';  
				}
				else
				{
				$right='right';
				$name='left';	
				}
                $str.=' <div class="direct-chat-msg '.$right.'">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-'.$name.'">'.$userData['name'].'</span>
                    <span class="direct-chat-timestamp pull-'.$right.'">'.date('M d, Y  h:i A',$message['date']).'</span>
                  </div>
                   <div class="direct-chat-text">'.$message['message'].'</div>
                 </div>';
 }
                $str.='</div>
             </div>
             <div class="box-footer">
              <form action="#" method="post">
                <div class="input-group">
				<input type="hidden" name="user_id" id="user_id" value="'.$this->userInfo->id.'">
        <input type="hidden" name="sender_id" id="sender_id" value="'.$this->userInfo->id.'">
                  <input type="text" name="message" id="message" placeholder="Type Message ..." class="form-control">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-primary btn-flat" onclick="return submitMessage()">Send</button>
                      </span>
                </div>
              </form>
            </div>
           </div>
         </div>';
 

$this->view->messageList=$str;		   
}
else
{ 
	 if($this->getRequest()->getParam('userid'))
	{
	$this->view->notify_id=$userid=$this->getRequest()->getParam('userid');
 	}
	$fetchData=$this->userObj->FetchAllMessage();
$str='<div class="col-md-12">
           <div class="box box-primary direct-chat direct-chat-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Show All Conversation</h3>
              </div>
             <div class="box-body">
               <div class="direct-chat-messages">';
               
               foreach($fetchData as $message)
           {  
          $userData= $this->userObj->getUserId($message['sender_id']);
               if($message['sender_id']==$this->userInfo->id)
			   {
				$right='left'; 
				$name='right';  
				}
				else
				{
				$right='right';
				$name='left';	
				}
                $str.=' <div class="direct-chat-msg '.$right.'">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-'.$name.'">'.$userData['name'].'</span>
                    <span class="direct-chat-timestamp pull-'.$right.'">'.date('M d, Y  h:i A',$message['date']).'</span>
                  </div>
                   <div class="direct-chat-text">'.$message['message'].'</div>
                 </div>';
 }
                $str.='</div>
             </div>
            </div>
         </div>';
 

 

$this->view->messageList=$str;		
}


	
}

public function loadmessageAction()
{
$this->_helper->layout->disableLayout();
 $id=$this->getRequest()->getParam('id');
 $this->view->userData= $this->userObj->getUserId($id);	
 
 $fetchData=$this->userObj->FetchMessage($id);
 $array=array('status'=>1);
 $update=$this->userObj->UpdateMessage($array,$id);
 
 $str="";
foreach($fetchData as $message)
{  
 $userData= $this->userObj->getUserId($message['sender_id']);
               if($message['sender_id']==$this->userInfo->id)
			   {
				$right='left'; 
				$name='right';  
				}
				else
				{
				$right='right';
				$name='left';	
				}
                $str.=' <div class="direct-chat-msg '.$right.'">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-'.$name.'">'.$userData['name'].'</span>
                    <span class="direct-chat-timestamp pull-'.$right.'">'.date('M d, Y  h:i A',$message['date']).'</span>
                  </div>
                   <div class="direct-chat-text">'.$message['message'].'</div>
                 </div>';
  } 

$this->view->messageList=$str;
 
 
}

public function savemessageAction()
{
$user_id=$this->getRequest()->getParam('user_id');
$sender_id=$this->getRequest()->getParam('sender_id');
$message=$this->getRequest()->getParam('message');
if($message)
{
$array=array("user_id"=>$user_id,"sender_id"=>$sender_id,"message"=>$message,"date"=>time());
$insert=$this->userObj->InsertMessage($array);	
}

$fetchData=$this->userObj->FetchMessage($user_id);
$str="";
foreach($fetchData as $message)
{  
$userData= $this->userObj->getUserId($message['sender_id']);	
 if($message['sender_id']==$this->userInfo->id)
			   {
				$right='left'; 
				$name='right';  
				}
				else
				{
				$right='right';
				$name='left';	
				}
                $str.=' <div class="direct-chat-msg '.$right.'">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-'.$name.'">'.$userData['name'].'</span>
                    <span class="direct-chat-timestamp pull-'.$right.'">'.date('M d, Y  h:i A',$message['date']).'</span>
                  </div>
                   <div class="direct-chat-text">'.$message['message'].'</div>
                 </div>';
  }

echo $str;
die;	
}

public function checkmessagenotificationAction()
{
echo $fetchMessNotify=count($this->userObj->FetchMyMessageAdminNotyfy($this->userInfo->id)); 	 
die;
}

public function deleteuserprofileAction()	
{
$id=$this->getRequest()->getParam('profile_id');
$delete = $this->userObj->DeleteUserProfile($id);
echo $id;
die;	
}
public function teamuserAction()
{
$team_id=$this->getRequest()->getParam('team_id');	
$TeamUsers = $this->userObj->getTeamUsers($team_id);
$html='<option value="">Please Select</option>';
foreach($TeamUsers as $user)
{
$html.='<option value="'.$user['id'].'">'.$user['name'].'</option>';	
}

echo $html;die;	
}
public function loadstatesAction()
{
$country=$this->getRequest()->getParam('country');	
$Getstates = $this->userObj->getCountryStates($country);
$html='<option value="">Please Select</option>';
foreach($Getstates as $statename)
{
$html.='<option value="'.$statename['state_title'].'">'.$statename['state_title'].'</option>';	
}

echo $html;die;	
}
public function getuserprofileAction()
{
$user_id=$this->getRequest()->getParam('user_id');	
$userProfile = $this->userObj->getUserProfile($user_id);
$html='<option value="">Select Profile</option>';
foreach($userProfile as $user)
{
	
$html.='<option value="'.$user['id'].'">'.$user['profile_name'].'</option>';	
}

echo $html;die;
}

public function reportsAction()
{
	 if($this->getRequest()->getParam('ajaxify')==1)
	{
		 $this->_helper->layout->disableLayout();
	}


}

public function reportsLoadAction()
{
 if($this->getRequest()->getParam('ajaxify')==1)
	{
		 $this->_helper->layout->disableLayout();
	}

 
$myTeamUserData=array();
$whereStr="";
if($this->userInfo->user_type=='Lead Validator')
{
if($whereStr!="")
{
$whereStr .= " AND score.user_id='".$this->userInfo->id."'";
}
else{
$whereStr .= " score.user_id='".$this->userInfo->id."'";
}

}
else if($this->userInfo->user_type=='Lead Generator' or $this->userInfo->user_type=='sub-admin')
{
$myTeamUserData= $this->userObj->getAllUsersMyTeams($this->userInfo->team_id);	
}
 
if($this->getRequest()->getParam('filter_date'))
	{
		
    $filter_date=$this->getRequest()->getParam('filter_date');
			$dateArray=explode(' - ',$filter_date);
			$fromdate=$dateArray[0]; 
		    $todate=$dateArray[1];	
		    $todate = date("m/d/Y",strtotime("+1 days", strtotime($todate)));
		
         if($fromdate and $todate)
			{ 
              if($whereStr!="")
			   {
	         $whereStr .= " AND  score.created_date BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		       }
			 else{
		     $whereStr .= " score.created_date BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		         }
 			}
 	}

 
$usertpe='Lead Generator';

if($this->userInfo->user_type=='Lead Generator'){
    $this->view->tatScore = $this->userObj->getAllTATScore($whereStr,$myTeamUserData,$usertpe,$this->userInfo->id);
}
else{
    $this->view->tatScore = $this->userObj->getAllTATScore($whereStr,$myTeamUserData,$usertpe,'');
}


	
}

public function teamusersAction()
{
$team_id=$this->getRequest()->getParam('team_id');	
$TeamUsers = $this->userObj->getTeamUsers($team_id);
$html='<option value="">Please Select</option>';
foreach($TeamUsers as $user)
{
$html.='<option value="'.$user['id'].'">'.$user['name'].'</option>';	
}
echo $html;die;

}

}