<?php
    /*
     * class Template_IndexController
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
 class consultant_IndexController extends Zend_Controller_Action {
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
		date_default_timezone_set("Asia/Kolkata");
		/////check User type
         $this->view->leftsection='manageuser';
          $this->teamsObj = new Teams_Model_Teams();
		  $this->userObj=$userObj = new Manageuser_Model_Manageuser();
		  $this->template = new Template_Model_Template();
		  $this->QueryObj = new Managequery_Model_Managequery();
		  $this->QuoteObj = new Managequote_Model_Managequote();
		   $this->chatObj = new Managequote_Model_Chat();
		  $layout = $this->_helper->layout();
		   $layout->setLayout('consultant-layout');
		  
  		   if(!isset($userInfo->id) || $userInfo->user_type=='Lead Validator'){ 
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
 
 function indexAction()
 {
	 $whereStr="";
	  $limit=10;
	 if($whereStr!="")
			   {
	         $whereStr .= " AND as.assign_to_userid = '".$this->userInfo->id."'";
		       }
			 else
			 {
		     $whereStr .= " as.assign_to_userid = '".$this->userInfo->id."'";
		     }
$this->view->QuoteAssignPendingData=$this->QuoteObj->FetchAllConsultantPendingQuote($whereStr);	

$this->view->assignSubmittedData=$this->QuoteObj->FetchAllAssignSubmittedTask($whereStr,$limit); 
 }
 
 function pendingQuoteAction()
 {
	$whereStr="";
	$limit="";
		if($whereStr!="")
			   {
	         $whereStr .= " AND as.assign_to_userid = '".$this->userInfo->id."'";
		       }
			 else
			 {
		     $whereStr .= " as.assign_to_userid = '".$this->userInfo->id."'";
		     }
		$this->view->QuoteAssignPendingData=$this->QuoteObj->FetchAllConsultantPendingQuote($whereStr);	  
 }
  function priceSubmittedQuoteAction()
 {
	

$this->view->assignSubmittedData=$this->QuoteObj->FetchAllAssignSubmittedTask($whereStr,$limit);	
 }
 
 function viewquoteAction()
 {
$quoteid=$this->getRequest()->getParam('quoteid');
$this->view->ViewQuoteData=$ViewQuoteData=$this->QuoteObj->FetchViewQuoteInformation($quoteid);

$noti_id=$this->getRequest()->getParam('noti_id');
 
 if($noti_id)
 {
	 $this->view->noti_id=$noti_id;
	$this->QuoteObj->updateQuoteNotification(array("status"=>1),$noti_id); 
 }


if($this->getRequest()->isPost('ConsultantpriceSubmitted') and $this->getRequest()->getParam('ConsultantpriceSubmitted')!="")
		{
		$task_id=$this->getRequest()->getParam('task_id'); 
        $quote_amount =$this->getRequest()->getParam('quote_amount'); 
        $comment =$this->getRequest()->getParam('comment'); 
		
		$ViewQuoteData=$this->QuoteObj->FetchViewQuoteInformation($quoteid);
		 
		$array=array("status"=>1,"user_comments"=>$comment,"quote_price"=>$quote_amount,"user_submitted_date"=>time());
		$insert=$this->QuoteObj->UpdateAssignTask($array,$task_id);
		$array1=array("status"=>1);
		$update=$this->QuoteObj->UpdateQuote($array1,$quoteid);
		
		$this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>1,"message"=>'completed a quote',"date"=>time(),"ref_id"=>$ViewQuoteData['ref_id'],"quote_id"=>$quoteid,"noti_tab_type"=>2));
		
		//////////////////////////////////////////////////
		$body="Dear Admin<br><br>";
				$body.="Consultant has submitted  quotes for ".$ViewQuoteData['client_name']." (".$ViewQuoteData['ref_id']."). Kindly login your panel to check the updates. <br /><br/>";
				 
				 $body.= '<br />Do not reply to this email<br>.<br />Thanks and regards<br />Query Panel';
 				 $message = array(
							'html' => $body,
							'subject' => 'Quote Submitted by Consultant for '.$ViewQuoteData['ref_id'], 
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
		///////////////////////////////////////////////////
 		$this->_redirect('consultant/index/viewquote/quoteid/'.$quoteid);
     	}
		
 
 }


	 public function submituserchatAction()
	 {
 $ref_id =$this->getRequest()->getParam('ref_id'); 
 $quote_id =$this->getRequest()->getParam('quote_ids'); 
 $message =$this->getRequest()->getParam('message'); 

 $array_update=array("ref_id"=>$ref_id,"quote_id"=>$quote_id,"sender_id"=>$this->userInfo->id,"user_type"=>$this->userInfo->user_type,"message"=>$message,"date"=>time(),"new_message"=>"0");	     
 $fetchalldata=$this->chatObj->InsertQuoteChat($array_update);
 
 $ViewQuoteInfo=$this->QuoteObj->FetchViewQuoteInformation($quote_id);
 
 $this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>1,"message"=>'sent chat message',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quote_id,"noti_tab_type"=>2));
 
  $this->QuoteObj->insertNotification(array("sender_id"=>$this->userInfo->id,"reciever_id"=>$ViewQuoteInfo['user_id'],"message"=>'sent chat message',"date"=>time(),"ref_id"=>$ref_id,"quote_id"=>$quote_id,"noti_tab_type"=>2));
 
 
 $chatDetail=$this->chatObj->FetchQuoteChat($quote_id);	
 $html="";
  foreach($chatDetail as $chatVal){ 
			  
			   if($chatVal['sender_id']==$this->userInfo->id){ 
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

//////////////////////////////////////////////////
$quoteInfo=$this->QuoteObj->FetchViewQuoteInformation($quote_id);
  
//////////////////Sent Consultant//////////////////////////////////////
 
/////////////////////////////////CRM Mail////////////////////////////////////////
$body="Dear Admin<br><br>";
				$body.="Consultant has posted comments  for ".$quoteInfo['client_name']." (".$quoteInfo['ref_id']."). Kindly login your panel to check the updates. <br /><br/>";
				 
			$body.= '<br />Do not reply to this email<br>.<br />Thanks and regards<br />Query Panel';
 				 $message = array(
							'html' => $body,
							'subject' => 'Comments Posted by Consultant for  '.$quoteInfo['ref_id'], 
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
if($quoteInfo['email_id'])
{
$body="Dear ".$quoteInfo['name']."<br><br>";
				$body.="Consultant has posted comments  for ".$quoteInfo['client_name']." (".$quoteInfo['ref_id']."). Kindly login your panel to check the updates. <br /><br/>";
				 
			$body.= '<br />Do not reply to this email<br>.<br />Thanks and regards<br />Query Panel';
 				 $message = array(
							'html' => $body,
							'subject' => 'Comments Posted by Consultant for  '.$quoteInfo['ref_id'], 
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
}
 



/////////////////////////////////////////////////////
echo $html;
die;		
	 }
	 
 
 	 
}