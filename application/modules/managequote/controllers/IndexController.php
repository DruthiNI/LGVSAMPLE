<?php
    /*
     * class Template_IndexController
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
 class managequote_IndexController extends Zend_Controller_Action {
    /*
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
    public function init() {
        parent::init();
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$redirect_url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		 if(!isset($userInfo->id)){
             $this->_redirect('login/?redirect_url='.$redirect_url);
         }
		//////////////////Flash message session ////////////////
        $this->_s = Zend_Registry::get('session');
        if ($this->_s->message) {
            $this->view->successmessage = $this->_s->message;
            $this->_s->message = NULL;
        }
		
		 date_default_timezone_set("Asia/Kolkata");
		/////check User type
         $this->view->leftsection='manageuser';
          $this->teamsObj = new Teams_Model_Teams();
		  $this->userObj=$userObj = new Manageuser_Model_Manageuser();
		  $this->template = new Template_Model_Template();
		  $this->QueryObj = new Managequery_Model_Managequery();
		  $this->QuoteObj = new Managequote_Model_Managequote();
		  $this->chatObj = new Managequote_Model_Chat();
		  $this->tagsObj = new Tags_Model_Tags();
		   $this->followupsettingObj = new Followupsetting_Model_Followupsetting();
  		   if(!isset($userInfo->id)){ 
            $this->_redirect('dashboard/');
         }
		  $this->view->userInfo = $this->userInfo =$userObj->getUserId($userInfo->id);
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
 
public function listAskForScopeAction()
{
	
	 $this->view->serviceData=$this->template->getAllCategoryService($this->userInfo->category);
		 
		if($this->userInfo->user_type=='admin')
		{
			$whereStr = "user_type = 'user'";
		 $this->view->crmUserData=$this->userObj->getAllUsers($whereStr);
		}
		else if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='Lead Generator')
		{
	     $myTeamUserData= $this->userObj->getAllUsersMyTeams($this->userInfo->team_id);
		 $this->view->crmUserData=$this->userObj->getSubadminAllUsers(implode(",",$myTeamUserData));	
		}
		
 		$whereStr ="";
		 
 
			 
            $this->view->userid=$userid=$this->getRequest()->getParam('userid');
			
 			if($userid)
			{ 
              if($whereStr!="")
			   {
	         $whereStr .= " AND ass_qr.user_id = '".$userid."'";
		       }
			 else{
		     $whereStr .= "ass_qr.user_id = '".$userid."'";
		         }
 			}
		
		
		if($this->userInfo->user_type=='user')
			{
 				$allocated_to=$this->userInfo->id;
               if($whereStr!="")
			   {
	         $whereStr .= " AND ass_qr.user_id ='".$allocated_to."'";
		       }
			 else{
		     $whereStr .= " ass_qr.user_id ='".$allocated_to."'";
		         }
		    }
		 
		$this->view->allQuoteData=$this->QuoteObj->FetchAllQuote($whereStr);
		
		
		
		
		
		
		if($this->getRequest()->getParam('Action')=="Delete")
	    {
		 $queryid=$this->getRequest()->getParam('checkid');
 		 $delete=$this->QuoteObj->DeleteQuote($queryid);
		  $this->_redirect('managequote/list-ask-for-scope');
	    }	
}	 
	 
public function loadsearchallquoteAction()
{
$this->_helper->layout->disableLayout();
		$whereStr ="";
	     if($this->getRequest()->getParam('searchBtn')!="")
		  {
 			$whereStr ="";
			$todate="";
			$fromdate="";
 			$this->view->userid=$userid=$this->getRequest()->getParam('userid');
			if($this->getRequest()->getParam('filter_date'))
			{
			$this->view->filter_date=$filter_date=$this->getRequest()->getParam('filter_date');
			$dateArray=explode(' - ',$filter_date);
			$fromdate=$dateArray[0]; 
		    $todate=$dateArray[1];
			$todate = date("m/d/Y",strtotime("+1 days", strtotime($todate)));
			}
			
			$this->view->status=$status=$this->getRequest()->getParam('status');
			$this->view->search_keywords=$search_keywords=$this->getRequest()->getParam('search_keywords');
			if($userid)
			{ 
              if($whereStr!="")
			   {
	         $whereStr .= " AND ass_qr.user_id = '".$userid."'";
		       }
			 else{
		     $whereStr .= "ass_qr.user_id = '".$userid."'";
		         }

 			}
		 
			if($fromdate and $todate)
			{
              if($whereStr!="")
			   {
	          $whereStr .= " AND qt.created_date between  '".strtotime($fromdate)."' and '".strtotime($todate)."'";		
		       }
			 else{
		     $whereStr .= " qt.created_date between  '".strtotime($fromdate)."' and '".strtotime($todate)."'";
		         }
		    }
			 
			

			 
			//////////////////////////////////////
			if($status)
		{
			if($status=="Pending")
			{
	        $keyword = 0;
			}
			else{
			$keyword = $status;	
				
			}
         if($whereStr!="")
			   {
	         $whereStr .= " AND qt.status = '".$keyword."'";
		       }
			 else{
		     $whereStr .= " qt.status = '".$keyword."'";
		         }
		    }
			
			if($search_keywords)
			{
		      $keyword = $search_keywords;
              if($whereStr!="")
			   {
	         $whereStr .= ") AND (query.name LIKE '%".$keyword."%' or query.email_id LIKE '%".$keyword."%' or query.phone LIKE '%".$keyword."%'";
		       }
			 else{
			$whereStr .= " query.name LIKE '%".$keyword."%' or query.email_id LIKE '%".$keyword."%' or query.phone LIKE '%".$keyword."%'";
		     
		         }
	    }


   		  }
		 // echo $whereStr;die;
      
		 $this->view->allQuoteData=$this->QuoteObj->FetchAllQuote($whereStr);
		  
}		 
 
public function submitRequestQuoteAction()
{  
 $ref_id=$this->getRequest()->getParam('ref_id');
 $deadline_date=$this->getRequest()->getParam('deadline_date');
 $currency=$this->getRequest()->getParam('currency');
 $quote_comments=$this->getRequest()->getParam('quote_comments');
 
 /////////////////////////////////////////////////////////////////////
			    $locatie = 'public/QuotationFolder/'; 
            $time=time();
  	        $quote_upload_file=array();
  		 	$count= count($_FILES['quote_upload_file']['name']); 
			if ($count!="" && $count > 0) {
			for($i=0;$i<$count;$i++){
				$doc.=$_FILES['quote_upload_file']['name'][$i].',';
				$folder = time().$i.'_'.basename(preg_replace("/[^a-zA-Z0-9.]/","",$_FILES['quote_upload_file']['name'][$i])) ; 
 				$base_name=basename($_FILES['quote_upload_file']['name'][$i]);
			if(move_uploaded_file($_FILES['quote_upload_file']['tmp_name'][$i], $locatie.$folder)){
				$j=$i+1;
				$quote_upload_file[]=array("filename"=>$base_name,"file_path"=>$folder);
 
   			 }
			}
            }
			 
			   /////////////////////////////////////////////////////////////////////
 
 $quoteInfo=$this->QuoteObj->checkExistQuote($ref_id);
 if(!$quoteInfo['id'])
 {
$array=array(
"ref_id"=>$ref_id,
"user_id"=>$this->userInfo->id,
"comments"=>$quote_comments,
"deadline_date"=>$deadline_date,
"currency"=>$currency,
"relevant_file"=>($quote_upload_file)?json_encode($quote_upload_file):"",
"created_date"=>time(),
);
//print_r($array);die;
$insertid=$this->QuoteObj->addRequestQuote($array);


$categoryType=$this->getUserCategoryType($this->userInfo->category);
$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>1,"message"=>$categoryType.' Request a quote',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$insertid,"noti_tab_type"=>2));


$body="Dear Admin<br><br>";
$body.="CRM has submitted a quote for ".$client_name." (".$ref_id.").  Kindly login your panel for further action. <br />";

$body.= '<br />Do not reply to this email <br /><br />Thanks and regards <br /><br />';
	$message = array(
			'html' => $body,
			'subject' => 'Quote Posted by CRM for '.$ref_id, 
			'from_email' =>'info@rapidcollaborate.com',
			'from_name' => 'rapidcollaborate.com',
			'to' => array(
				array(
					'email' => 'niranjan.kumar@redmarkediting.com',
					'name' => "Admin",
					'type' => 'to'
				)
			)
	   );
SendMandrilMail($message);

$html='';
$html.='<div class="box-body"> 
 <div class="form-group">
                  <label for="inputEmail3" class="col-sm-12 control-label" style="color:green;text-align:center;font-size: 15px;">Quote has been submitted successfully.</label>
                 </div>
					  <div class="form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Ref No.</label>
                  <div class="col-sm-8">'.$ref_id.'</div>
                </div>
				<div class="clearfix"></div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Deadline</label>
                  <div class="col-sm-8">'.$deadline_date.'</div>
                </div>
				<div class="clearfix"></div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Currency</label>
                  <div class="col-sm-8">'.$currency.'</div>
                </div>
				<div class="clearfix"></div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Comments</label>
                  <div class="col-sm-8">'.$quote_comments.'</div>
                </div>
				<div class="clearfix"></div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Created Date</label>
                  <div class="col-sm-8">'.date("d M Y h:i A",time()).'</div>
                </div>';
				
				if($quote_upload_file){ 
				$html.='<div class="clearfix"></div><div class="row form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Relevant Files</label>
                  <div class="col-sm-8">';
				    
					foreach($quote_upload_file as $files)
					{  
					$html.='<a href="'.PUBLICURL.'QuotationFolder/'.$files['file_path'].'" download>'.$files['filename'].'</a><br>';
				  }
				   
				$html.='</div>
                </div>';
				  }
				
				$html.='<div class="clearfix"></div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                  <div class="col-sm-8">Pending</div>
                </div>
				<div class="clearfix"></div>';
				
				$html.='<div class="clearfix"></div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Reference Url</label>
                  <div class="col-sm-8"><a href="'.SITEURL.'managequote/view-askforscope/'.$ref_id.'" target="_blank">'.SITEURL.'managequote/view-askforscope/'.$ref_id.'</a></div>
                </div>
				<div class="clearfix"></div>';

echo $html;
 }
die;	
}

 
public function submituserchatAction()
{
 $quote_id =$this->getRequest()->getParam('quote_ids'); 
 $ref_id =$this->getRequest()->getParam('ref_id'); 
 $message =$this->getRequest()->getParam('message'); 

 $array_update=array("ref_id"=>$ref_id,"quote_id"=>$quote_id,"sender_id"=>$this->userInfo->id,"user_type"=>$this->userInfo->user_type,"message"=>$message,"date"=>time(),"new_message"=>"0");	     
 $fetchalldata=$this->chatObj->InsertQuoteChat($array_update);
 $html= $this->getchatContentHtml($quote_id);
 
 $quoteInfo=$this->QuoteObj->FetchViewQuoteInformation($quote_id);
 
$assignInfo=$this->QuoteObj->CheckAssignTask($quote_id);

if($this->userInfo->user_type=='admin')
{
$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>$quoteInfo['user_id'],"message"=>' sent chat message',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quote_id,"noti_tab_type"=>2));	

if($assignInfo['email_id'] and $assignInfo['status']==0)
{
$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>$assignInfo['assign_to_userid'],"message"=>'sent chat message',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quote_id,"noti_tab_type"=>2));	
}

}
if($this->userInfo->user_type=='user')
{
$categoryType=$this->getUserCategoryType($this->userInfo->category);	
$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>1,"message"=>'sent chat message',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quote_id,"noti_tab_type"=>2));


if($assignInfo['email_id'] and $assignInfo['status']==0)
{
$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>$assignInfo['assign_to_userid'],"message"=>'sent chat message',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quote_id,"noti_tab_type"=>2));	
}
	
}
if($this->userInfo->user_type=='sub-admin')
{
$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>$quoteInfo['user_id'],"message"=>'sent chat message',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quote_id,"noti_tab_type"=>2));

$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>1,"message"=>'sent chat message',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quote_id,"noti_tab_type"=>2));	
} 
 
 
//////////////////////////////////////////////////


if($this->userInfo->user_type=='admin')
{
/////////////////////////////////CRM Mail////////////////////////////////////////
$body="Dear ".$quoteInfo['name']."<br><br>";
				$body.="Admin has posted comments  for ".$quoteInfo['client_name']." (".$quoteInfo['ref_id']."). Kindly login your panel to check the updates. <br /><br/>";
				 
			$body.= '<br />Do not reply to this email<br>.<br />Thanks and regards<br />Query Panel';
 				 $message = array(
							'html' => $body,
							'subject' => 'Comments Posted by Admin for  '.$quoteInfo['ref_id'], 
							'from_email' => 'info@rapidcollaborate.com',
							'from_name' => 'rapidcollaborate.com',
							'to' => array(
								array(
									'email' => $quoteInfo['email_id'],
									'name' => $quoteInfo['name'],
									'type' => 'to'
								) 
							)
					   );
		SendMandrilMail($message);
/////////////////////////////////Consultant Mail////////////////////////////////////////
if($assignInfo['email_id'])
{
$body="Dear ".$assignInfo['name']."<br><br>";
				$body.="Admin has posted comments  for ".$quoteInfo['client_name']." (".$quoteInfo['ref_id']."). Kindly login your panel to check the updates. <br /><br/>";
				 
			$body.= '<br />Do not reply to this email<br>.<br />Thanks and regards<br />https://rapidcollaborate.com/askforquote';
 				 $message = array(
							'html' => $body,
							'subject' => 'Comments Posted by Admin for  '.$quoteInfo['ref_id'], 
							'from_email' => 'info@rapidcollaborate.com',
							'from_name' => 'rapidcollaborate.com',
							'to' => array(
								array(
									'email' => $assignInfo['email_id'],
									'name' => $assignInfo['name'],
									'type' => 'to'
								) 
							)
					   );
		SendMandrilMail($message);
}
}
//////////////////Sent CRM//////////////////////////////////////
if($this->userInfo->user_type=='user')
{
/////////////////////////////////CRM Mail////////////////////////////////////////
$body="Dear Admin<br><br>";
				$body.="CRM has posted comments  for ".$quoteInfo['client_name']." (".$quoteInfo['ref_id']."). Kindly login your panel to check the updates. <br /><br/>";
				 
			$body.= '<br />Do not reply to this email<br>.<br />Thanks and regards<br />Query Panel';
 				 $message = array(
							'html' => $body,
							'subject' => 'Comments Posted by CRM for  '.$quoteInfo['ref_id'], 
							'from_email' => 'info@rapidcollaborate.com',
							'from_name' => 'rapidcollaborate.com',
							'to' => array(
								array(
									'email' => ADMIN_EMAIL,
									'name' => 'Admin',
									'type' => 'to'
								) 
							)
					   );
		SendMandrilMail($message);
/////////////////////////////////Consultant Mail////////////////////////////////////////
if($assignInfo['email_id'])
{
$body="Dear ".$assignInfo['name']."<br><br>";
				$body.="CRM has posted comments  for ".$quoteInfo['client_name']." (".$quoteInfo['ref_id']."). Kindly login your panel to check the updates. <br /><br/>";
				 
			$body.= '<br />Do not reply to this email<br>.<br />Thanks and regards<br />https://rapidcollaborate.com/askforquote';
 				 $message = array(
							'html' => $body,
							'subject' => 'Comments Posted by CRM for  '.$quoteInfo['ref_id'], 
							'from_email' => 'info@rapidcollaborate.com',
							'from_name' => 'rapidcollaborate.com',
							'to' => array(
								array(
									'email' => $assignInfo['email_id'],
									'name' => $assignInfo['name'],
									'type' => 'to'
								) 
							)
					   );
		SendMandrilMail($message);
}
}

 

/////////////////////////////////////////////////////
echo $html;
die;		
	 }
	 
public function getquotechatAction()
{
$quote_id =$this->getRequest()->getParam('quote_id');
$chatData=$this->getchatContentHtml($quote_id);
echo $chatData;
 die;
	
}	

public function getchatContentHtml($quote_id)
{
 $chatDetail=$this->chatObj->FetchQuoteChat($quote_id);	
 $html="";
$chatArray=array(); 

  foreach($chatDetail as $chatVal){ 
  
/*$chatArray[]=array(
"sender_id"=>$chatVal['sender_id'],
"my_id"=>$this->userInfo->id,
"user_name"=>ucwords($chatVal['name']),
"chat_date"=>date('d M Y H:i A', $chatVal['date']),
"message"=>$chatVal['message'],
);			  
*/			   if($chatVal['sender_id']==$this->userInfo->id){ 
			  $html.='<div class="direct-chat-msg right">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-right">'.ucwords($chatVal['name']).'</span>
                    <span class="direct-chat-timestamp pull-left">'.date('d M Y H:i A', $chatVal['date']).'</span>
                  </div>
                  <div class="direct-chat-text">'.$chatVal['message'].'</div>
                </div>';
			    } 
				else
					{ 
                $html.='<div class="direct-chat-msg">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">'.ucwords($chatVal['name']).'</span>
                    <span class="direct-chat-timestamp pull-right">'.date('d M Y H:i A', $chatVal['date']).'</span>
                  </div>
                  <div class="direct-chat-text">'.$chatVal['message'].'</div>
                </div>';
			    } 
                
			   }
return $html;die;			   
} 	

public function adminQuoteDetailsAction()
{
	$this->_helper->layout()->disableLayout();
$quote_id =$this->getRequest()->getParam('quote_id');

	
$this->view->ConsultantUserData=$this->userObj->fetchActiveUsers('Consultant');
$this->view->quoteInfo=$this->QuoteObj->FetchViewQuoteInformation($quote_id);
$this->view->assignQuoteInfo=$this->QuoteObj->CheckAssignTask($quote_id);
 
}

public function submitAssignQuoteAction()
{
 $assign_to_userid =$this->getRequest()->getParam('assign_to_userid'); 
 $ref_id =$this->getRequest()->getParam('ref_id'); 
 $quoteid =$this->getRequest()->getParam('quote_id'); 
 $admin_comments =$this->getRequest()->getParam('admin_comments');
	
 $assignUserInfo=$this->userObj->getUserId($assign_to_userid);
		
 
		$array=array("ref_id"=>$ref_id,"quote_id"=>$quoteid,"assign_to_userid"=>$assign_to_userid,"assigned_date"=>time(),"admin_comments"=>$admin_comments);
		$insert=$this->QuoteObj->insertTaskAssign($array);
		 
		
		$ViewQuoteData=$this->QuoteObj->FetchViewQuoteInformation($quoteid);
		
$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>$assign_to_userid,"message"=>'New query assigned',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quoteid,"noti_tab_type"=>2));
		
		
		/////////////////////////////////
		$body="Dear ".$assignUserInfo['name']."<br><br>";
		$body.="You have been allocated a quote for ".$ViewQuoteData['client_name']." (".$ref_id."). Kindly login your panel for further action. <br /><br/>";
			
                 $body.="Do not reply to this email<br>";
				$body.= '<br />Thanks and regards<br />Query Panel<br />';
					$message = array(
							'html' => $body,
							'subject' =>'Quote Allocated by Admin for '.$ref_id, 
 					    	'from_email' =>'info@rapidcollaborate.com',
							'from_name' => 'rapidcollaborate.com',
							'to' => array(
								array(
									'email' => $assignUserInfo['email_id'],
									'name' => $assignUserInfo['name'],
									'type' => 'to'
								)
							)
					   );
		SendMandrilMail($message);
		//////////////////////////////////
die;		
}

public function submittedToAdminQuoteAction()
{

        $ref_id =$this->getRequest()->getParam('ref_id'); 
		$quoteid =$this->getRequest()->getParam('quote_id');
        $quote_amount =$this->getRequest()->getParam('quote_amount'); 
        $comment =$this->getRequest()->getParam('comment'); 
		$array=array("ref_id"=>$ref_id,"assign_to_userid"=>$this->userInfo->id,"quote_id"=>$quoteid,"status"=>2,"assigned_date"=>time(),"user_comments"=>$comment,"quote_price"=>$quote_amount,"user_submitted_date"=>time());
		
		$insert=$this->QuoteObj->insertTaskAssign($array);
		$array1=array("status"=>1);
		$update=$this->QuoteObj->UpdateQuote($array1,$quoteid);
		
		$ViewQuoteData=$this->QuoteObj->FetchViewQuoteInformation($quoteid);
		
		$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>$ViewQuoteData['user_id'],"message"=>'completed a quote',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quoteid,"noti_tab_type"=>2));

//////////////////////////////////////////////////
		$body="Dear ".$ViewQuoteData['name']."<br><br>";
				$body.="Admin has submitted  quotes for ".$ViewQuoteData['client_name']." (".$ViewQuoteData['ref_id']."). Kindly login your panel to check the updates. <br /><br/>";
				 
				 $body.= '<br />Do not reply to this email<br>.<br />Thanks and regards<br />Query Panel';
 				 $message = array(
							'html' => $body,
							'subject' => 'Quote Submitted by Admin for '.$ViewQuoteData['ref_id'], 
							'from_email' => 'info@rapidcollaborate.com',
							'from_name' => 'rapidcollaborate.com',
							'to' => array(
								array(
									'email' => $ViewQuoteData['email_id'],
									'name' => $ViewQuoteData['name'],
									'type' => 'to'
								)
							)
					   );
		SendMandrilMail($message);
		///////////////////////////////////////////////////


 		echo $insert;die;	
}

public function updatepriceAction()
{
$quoteid=$this->getRequest()->getParam('quoteid');
$task_id=$this->getRequest()->getParam('task_id');
$quote_price=$this->getRequest()->getParam('quote_price');
$user_comments=$this->getRequest()->getParam('user_comments');

if($task_id!="")
{
$array=array("quote_price"=>$quote_price,"user_comments"=>$user_comments,"status"=>2);
$update = $this->QuoteObj->UpdateAssignTask($array,$task_id);

$ViewQuoteData=$this->QuoteObj->FetchViewQuoteInformation($quoteid);

$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>$ViewQuoteData['user_id'],"message"=>'completed a quote',"date"=>time(),"ref_id"=>$ViewQuoteData['ref_id'],"quote_id"=>$quoteid,"noti_tab_type"=>2));



///////////////////////////////////////////////////////////////
		$body="Dear ".$ViewQuoteData['name']."<br><br>";
				$body.="Admin has submitted  quotes for ".$ViewQuoteData['client_name']." (".$ViewQuoteData['ref_id']."). Kindly login your panel to check the updates. <br /><br/>";
				 
				 $body.= '<br />Do not reply to this email<br>.<br />Thanks and regards<br />Query Panel';
 				 $message = array(
							'html' => $body,
							'subject' => 'Quote Submitted by Admin for '.$ViewQuoteData['ref_id'], 
							'from_email' => 'info@rapidcollaborate.com',
							'from_name' => 'rapidcollaborate.com',
							'to' => array(
								array(
									'email' => $ViewQuoteData['email_id'],
									'name' => $ViewQuoteData['name'],
									'type' => 'to'
								)
							)
					   );
		SendMandrilMail($message);
///////////////////////////////////////////////////////////////




echo 1;die;
}

}
  
function getUserCategoryType($category)
{
if($this->userInfo->category=='Sales')
{
$not_mess='Sales';	
}
else
{
$not_mess='CRM';		
}	
}


function exportallquoteAction()
{
	
}

function viewAskforscopeAction()
{
 
	
	if($this->getRequest()->getParam('query_id')!="" and $this->getRequest()->getParam('Anoti')==1)
		{
		 $query_id=$this->getRequest()->getParam('query_id');
 		  $array=array('status'=>1);
 		 $update= $this->QueryObj->updateCommentStatus($array,$query_id,$this->userInfo->id);
  		}
		
		if($this->getRequest()->getParam('query_id')!="" and $this->getRequest()->getParam('Unoti')==1)
		{
		 $query_id=$this->getRequest()->getParam('query_id');
 		 $array=array('status'=>1);
 		 $update= $this->QueryObj->updateCommentStatus($array,$query_id,$this->userInfo->id);
  		}
	
	 $noti_id=$this->getRequest()->getParam('noti_id');
 
 if($noti_id)
 {
	 $this->view->noti_id=$noti_id;
	$this->QuoteObj->updateQuoteNotification(array("status"=>1),$noti_id); 
 }
 
	
	if($this->getRequest()->getParam('query_id'))
	{
		 $query_id=$this->getRequest()->getParam('query_id');
	////////////////////////////////////////////////////////////////////////////////
	$this->view->QueryInfo=$QueryInfo = $this->QueryObj->getViewQueryId($query_id);
	//print_r($QueryInfo); die;
	if($QueryInfo['transfer_type']==1)
	{
	$this->view->CommentInfo = $this->QueryObj->GetComents($QueryInfo['id']);	
	}
	else
	{
	$this->view->CommentInfo = $this->QueryObj->GetComentsAssign($QueryInfo['assign_id'],'all');	
	}
	 
	 
	 $this->view->tagsArr=$this->tagsObj->getAllCategoryTags($this->userInfo->category);
	 $this->view->TatScore=$this->userObj->getParticularTatScore($QueryInfo['assign_id']);
	 
	 
	 $main_query_id=$QueryInfo['id'];
	 $this->view->QueryFilesData= $this->QueryObj->fetchQueryFiles($main_query_id);
	 $this->view->profileData=$this->userObj->getUserProfiles($this->userInfo->id);
	 $this->view->templateInfo=array();
	 
	     $whereStr='';
		 $user_id=$QueryInfo['user_id'];
             
			 if($whereStr!="")
			   {
	         $whereStr .= " AND website_id ='".$QueryInfo['website_id']."'";
		       }
			 else
		     {
		     $whereStr .= " website_id ='".$QueryInfo['website_id']."'";
		     }
			  
			 if($whereStr!="")
			   {
	         $whereStr .= " AND  find_in_set(".$user_id.", assign_user) <> 0";
		       }
			 else
		     {
		     $whereStr .= "  find_in_set(".$user_id.", assign_user) <> 0";
		     }
			 
			  
		if($QueryInfo['tags'])
		{
		$tags=explode(",",$QueryInfo['tags']);

	      $this->view->templateInfo=$this->template->getTemplateInfoAllDetails('tbl_email_template',$whereStr,$tags);
		}
	 
$whereStr='';
             if($whereStr!="")
			   {
	         $whereStr .= " AND service_id ='".$QueryInfo['requirement']."'";
		       }
			 else
		     {
		     $whereStr .= " service_id ='".$QueryInfo['requirement']."'";
		     }
			 if($whereStr!="")
			   {
	         $whereStr .= " AND website_id ='".$QueryInfo['website_id']."'";
		       }
			 else
		     {
		     $whereStr .= " website_id ='".$QueryInfo['website_id']."'";
		     }
	 	 
	 $this->view->smstemplateInfo=$this->template->getTemplateInfoDetails('tbl_sms_template',$whereStr);	 
	 $this->view->priorityArr = $this->followupsettingObj->getAllPriority();
	 $this->view->quoteInfo=$quoteInfo=$this->QuoteObj->checkExistQuote($QueryInfo['assign_id']);
	 $this->view->followupInfo=$followupInfo=$this->QueryObj->getNextFollowupDate($QueryInfo['assign_id']);
	  
	 
	 if($quoteInfo and $quoteInfo['id'])
	 {
	 $this->view->assignQuoteInfo=$this->QuoteObj->CheckAssignTask($quoteInfo['id']);
	 }
	 ////////////////////////////////////////////////////////////////////////////////
	}
	
  	
}

}