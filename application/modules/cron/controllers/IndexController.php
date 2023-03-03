<?php
use Postmark\PostmarkClient;
use Postmark\PostmarkAdminClient;
use Postmark\Models\PostmarkException;
use Postmark\Models\PostmarkAttachment;
    /*

     * class Cron_IndexController

     * Date: 14-Jan-2015

     * Developer: Niranjan Kumar

     * Modified By: Niranjan Kumar

     * 

     */

class Cron_IndexController extends Zend_Controller_Action {
  
     public function init() {       
         // $this->view->addScriptPath(COMMON_VIEW);
         $this->smsObj = new Meetingschedule_Model_Meetingschedule();
		 $this->QueryDashboardObj = new Dashboard_Model_Managequery();
		 $this->QueryObj = new Managequery_Model_Managequery();
		 $this->db = Zend_Registry::get('db');
         $this->_helper->layout->disableLayout();
		 $this->boxQueryObj = new Managequery_Model_Boxquery();
		 $this->userObj = new Manageuser_Model_Manageuser();
		 $this->template = new Template_Model_Template();
		 
		 
		 
		 
		   if($_SERVER['HTTP_HOST']=='lgv.regentapps.com')
		  {
		  $this->postmarkclient = new PostmarkClient("2d8321dc-0079-4fbe-9911-bd5307e3e3dc"); //e32dad06-b277-4040-985d-52107130c6d1 
		  }
		 
		 date_default_timezone_set("Asia/Kolkata");
      }
 
    public function indexAction()
	 {
	$currentDate=strtotime(date("m/d/Y",time())); 	 
    $this->smsObj->AutoSentSMS($currentDate);
	
	
	  $currentDate=date("m/d/Y",time()); 
 $Date = date("m/d/Y",strtotime($currentDate));
 $currentDate= strtotime($Date. ' - '.(1).' days');
 $currentDate= strtotime($Date. ' + '.(1).' days');
	 
	$this->QueryObj->AutoLoadCron($currentDate);
		 
	 $to = "niranjan421989@gmail.com";
     $subject = "Test mail";
     $message = "Hello! This is a simple email message.";
     $from = "support@rapidcollaborate.com";
     $headers = "From:" . $from;
     //mail($to,$subject,$message,$headers);
     //echo "Mail Sent.";
		 
	 }
	 
 
	 public function automationMailAction()
	 {
	 $LeadsData=$this->QueryDashboardObj->getFirstAutoFollowupQuery();	
echo "<pre>";
print_r($LeadsData);
echo "</pre>";	 
		echo count($LeadsData);   die;
	 }
 
   public function webhookAction()
	 {
	 \Postmark\Autoloader::register();
	  $inbound = new \Postmark\Inbound(file_get_contents('php://input'));
	 
	  $subject=$inbound->Subject();
	  $FromEmail=$inbound->FromEmail();
	  $to_email=$inbound->To();
	  $ToFull=$inbound->ToFull();
	  $cc=$inbound->Cc();
	  $CcFull=$inbound->CcFull();
	  $bcc=$inbound->Bcc();
	  $BccFull=$inbound->BccFull();
	  
	  $FromFull=$inbound->FromFull();
	  $FromName=$inbound->FromName();
	  $Date=$inbound->Date();
	  $OriginalRecipient=$inbound->OriginalRecipient();
	  $ReplyTo=$inbound->ReplyTo();
	  $MailboxHash=$inbound->MailboxHash();
	  $Tag=$inbound->Tag();
	  $MessageID=$inbound->MessageID();
	  $TextBody=$inbound->TextBody();
	  $HtmlBody=$inbound->HtmlBody();
	  $StrippedTextReply=$inbound->StrippedTextReply();
	  $refMessageId=$inbound->HeadersRefrence();
	  
	  $toEmails=array();
if($ToFull)
{
	foreach($ToFull as $toooo)
	{
	$toEmails[]=$toooo->Email;	
	}
}
	  
	  $ccEmails=array();
if($CcFull)
{
	foreach($CcFull as $cc)
	{
	$ccEmails[]=$cc->Email;	
	}
}
	  
	  $filesArray=array();
	  foreach($inbound->Attachments() as $attachment)
	  {
	$filesArray[]=$attachment->Name;
	$attachment->ContentType;
	$attachment->ContentLength;
	$attachment->Download('public/UploadFolder/'); //takes directory as first argument
      }

$comments_file="";
if($filesArray)
{
$comments_file=implode("||",$filesArray);
}

if($HtmlBody)
{
$comments=str_replace("\r\n","<br>",$HtmlBody);
}
else if($TextBody)
{
$comments=str_replace("\r\n","<br>",$TextBody);
}
else
{
$comments=str_replace("\r\n","<br>",$StrippedTextReply);
}


if($refMessageId)
{
$commentInfo=$this->QueryObj->getMainMessageID($refMessageId);
}
  
////////////////////////////////////////////////////////////////////////
if($refMessageId!="" and $commentInfo['id'])
{
$assign_id=$commentInfo['ass_query_id'];
$userid=$commentInfo['user_id'];
$QueryInfo = $this->QueryObj->getViewQueryId($assign_id);

 

$this->QueryObj->updateUserQuery(array("open_status"=>0,"open_date"=>time(),'update_status_date'=>time()),$assign_id);


$array=array("ass_query_id"=>$assign_id,
"query_id"=>$QueryInfo['id'],
"user_id"=>$userid,
"comments"=>$comments,
"comments_file"=>$comments_file,
"date"=>strtotime($Date),
"query_status"=>$QueryInfo['update_status'],
"comments_sent_type"=>'client',
"subject"=>$subject,
"from_email"=>$FromEmail,
"FromName"=>$FromName,
"to_email"=>$ToFull[0]->Email,
"cc_email"=>implode(", ",$ccEmails),
"bcc_email"=>($BccFull)?$BccFull[0]->Email:"",
"message_id"=>$MessageID
);
$insert= $this->QueryObj->insertComment($array);
}
else
{
$array=array(
"FromName"=>$FromName,
"FromEmail"=>$FromEmail,
"ToEmail"=>implode(",",$toEmails),
"ToName"=>$ToFull[0]->Name,
"OriginalRecipient"=>$OriginalRecipient,
"MessageID"=>$MessageID,
"Date"=>$Date,
"TextBody"=>$comments,
"Attachments"=>$comments_file,
"Subject"=>$subject,
"cc_email"=>implode(",",$ccEmails),
"bcc_email"=>($BccFull)?$BccFull[0]->Email:"",
"created_date"=>time()
);	
$insert= $this->QueryObj->insertClientExternalmail($array);
}
 
die;
	 }
 
    public function gettodaycronAction()
    {
$date=date("Y-m-d");	
$result=$this->QueryObj->gettodaycroncheck($date);
echo "<pre>";
print_r($result);
echo "<pre>";
die;
} 

    public function dailycronrunAction()
    {
$this->getblanktagquery();
die;	
}

    public function getblanktagquery()
    {
$result=$this->QueryObj->getBlankTagQuery();
$resultArray=array();
foreach($result as $key=>$val)
{
$totalTags=($val['tags'])?explode(",",$val['tags']):array();
$resultArray[]=array(
"assign_id"=>$val['assign_id'],
"user_id"=>$val['user_id'],
"tags"=>$val['tags'],
"totalTags"=>count($totalTags),
"user_status"=>$val['user_status'],
"update_status"=>$val['update_status'],
"user_name"=>$val['user_name'],
"open_status"=>$val['open_status'],
"assign_date"=>($val['assign_date'])?date("d M Y",$val['assign_date']):"",
"update_status_date"=>($val['update_status_date'])?date("d M Y",$val['update_status_date']):"",
"open_date"=>($val['open_date'])?date("d M Y",$val['open_date']):"",
"assign_follow_up_date"=>($val['assign_follow_up_date'])?date("d M Y",$val['assign_follow_up_date']):"",
);
if(count($totalTags) < 2)
{
$update=$this->QueryObj->updateUserQuery(array("open_status"=>1,"open_date"=>time()),$val['assign_id']);	
}
}
echo count($result);
echo "<pre>";
print_r($resultArray);
echo "<pre>";
die;	
}

    public function checkexistqueryAction()
    {
$name=$_POST['name'];	
$email_id=$_POST['email_id'];	
$website=$_POST['website'];	
 $DataCheck=$this->QueryObj->CheckExistQuery($name,$email_id,$website);
 return json_encode(array("datas"=>$DataCheck));
	die;
}

    public function getBoxQueryAfter2hoursAction()
    {
	
	
	$boxQuery=$this->boxQueryObj->getBoxQueryUnclaimedOver2Hours();
	//echo '<pre>';
	//print_r($boxQuery);die;
	$queryArray=array();
	foreach($boxQuery as $query)
	{
	$queryArray[$query->website_id][]=array("id"=>$query->id, "name"=>$query->name, "email_id"=>$query->email_id, "website_id"=>$query->website_id, "created_on"=>date("d M Y H:i:s", $query->created_on));	
	}
	$main_array=array();
	foreach($queryArray as $key=>$values)
	{
		$usersData=$this->userObj->getUsersforThiswbsiteUsersCron($key);
		$counter=0;
		if(count($usersData) > 0) 
		{
		$counter=0;	
			foreach($values as $index=>$qry)
			{
			if(count($usersData) <= $counter)
			{
			$counter=0;
			}
			$rotanalData=$this->getRotationalUser($usersData, $counter);
			$main_array[$key][]=array(
			"query_id"=>$qry['id'],
			"name"=>$qry['name'],
			"email_id"=>$qry['email_id'],
			"website_id"=>$qry['website_id'],
			"created_on"=>$qry['created_on'],
			"user_id"=>$rotanalData,
			);
			 //$this->autoclaimeboxquery($qry['id'], $rotanalData);
			$counter++;
			}
					
		}
		
		
	}
	    echo '<pre>';
		print_r($main_array);
		echo '</pre>';

	die;
}

    public function deletebeforejan2019Action()
    {
	$datas=$this->QueryObj->queryGetBeforeJan2019();
	echo "<pre>";
	print_r($datas);
	die;
	echo count($datas);
	$qIds=array();
	foreach($datas as $data)
	{
		$qIds[]=$data->id;	
		//$delete=$this->QueryObj->DeleteQueryBefore2019($data->id);
		//$fileData=$this->QueryObj->fetchQueryFiles($data->id);
		//foreach($fileData as $file)
		//{
			//echo $file->file_path.'<br>';
		//}
		
	}
	if(count($datas) > 0)
	{
	//$delete=$this->QueryObj->DeleteQueryBefore2019($qIds);	
	}
	
	echo "<pre>";
	print_r($qIds);
	die;
}

    function getRotationalUser($userData,$index)
    {
	foreach($userData as $key=>$user)
	{
		if($index==$key)
		{
		return $user['user_id'];
		}
		 
		
	    }
    }

/////////////////////Auto Claimed Box Query////////////////////////////////////
private function autoclaimeboxquery($query_id, $user_id)
{
 if($query_id!="" and $user_id!="")
 {
	 $query_id=$query_id;
	 $user_id=$user_id;
	$queryInfo=$this->boxQueryObj->getBoxQueryDetails($query_id); 
	$userInfo=$this->userObj->getUserId($user_id);
	
	$query_code=$queryInfo['query_code'];
	$name=$queryInfo['name'];
	$email_id=$queryInfo['email_id'];
	$alt_email_id=$queryInfo['alt_email_id'];
	$phone =$queryInfo['phone'];
	$alt_contact_no =$queryInfo['alt_contact_no'];
	$rec_date =$queryInfo['date'];
	$location =$queryInfo['location'];
	$city =$queryInfo['city'];
	$upload_file =$queryInfo['upload_file'];
	$website_id =$queryInfo['website_id'];
	$other_website =$queryInfo['other_website'];
	$area_of_study =$queryInfo['area_of_study'];
	$requirement =$queryInfo['requirement'];
	$other_requirement =$queryInfo['other_requirement'];
	$company_name =$queryInfo['company_name'];
	$priority =$queryInfo['priority'];
	$word_count =$queryInfo['word_count'];
	$deadline =$queryInfo['deadline'];
	$academic_level =$queryInfo['academic_level'];
	$approx_value =$queryInfo['approx_value'];
	$tags =$queryInfo['tags'];
	$remarks =$queryInfo['remarks'];
	$flag_mark =$queryInfo['flag_mark'];
	$created_on =$queryInfo['created_on'];
	$entrytype =$queryInfo['entrytype'];
	$websiteInfo=$this->template->getWebsiteInfo($website_id);
    $serviceInfo=$this->template->getServiceInfo($requirement);
	$subject='Enquiry at '.$websiteInfo['website'].' for  '.$serviceInfo['name'];
	
	
	          $priorityArray=explode('|',$priority);
 			  $priority=$priorityArray[0];
			  $no_day=$priorityArray[1]; 
			  $contact_by=$priorityArray[2];
			  $no_day=explode(',',$no_day);
			  $contact_by=explode(',',$contact_by);
			  
			  $follow_up_date= strtotime($rec_date);
	
	$array=array('query_code'=>$query_code,'name'=>$name,'email_id'=>$email_id,'phone'=>$phone,'date'=>$rec_date,'location'=>$location,'city'=>$city,'subject'=>$subject,'alt_email_id'=>$alt_email_id,'alt_contact_no'=>$alt_contact_no,'company_name'=>$company_name,'area_of_study'=>$area_of_study,'requirement'=>$requirement,'other_requirement'=>$other_requirement,'priority'=>$priority,'academic_level'=>$academic_level,'follow_up_date'=>$follow_up_date,'contact_by'=>$contact_by[0],'remarks'=>$remarks,'entrytype'=>$entrytype,'created_on'=>time(), 'claimed_by'=>$user_id,"if_auto_claim"=>1);
//echo "<pre>";
//print_r($array);
//echo "</pre>";
$insert= $this->QueryObj->addQuery($array);	

if($insert)
{
	
	 if($userInfo->sms_notify=="on")
		 {
		$this->QueryObj->SendMail($userInfo->username,$userInfo->email_id,$name,$email_id,$phone,$subject);
		$this->QueryObj->SendSMS($userInfo->username,$userInfo->mobile_no,$name,$email_id,$phone); 
		 }
	  if($userInfo->whatsaap_notification=="on")
		 {
			 if($name!="" and $email_id!="")
			 {
		     $this->QueryObj->SendWhatsAppSMS($userInfo->mobile_no,$name,$email_id, $phone, $website);
			 }					
		 }
	
	$allocated_to=$user_id;
	$getProfileInfo=$this->userObj->getUserProfilesThisWebsite($user_id, $website_id);
	$profile_id=$getProfileInfo['id'];
	$teams=explode(",",$userInfo->team_id);
	$team_id=$teams[0];
	$array1=array('query_id'=>$insert,'user_id'=>$allocated_to,'website_id'=>$website_id,'other_website'=>$other_website,'profile_id'=>$profile_id,'team_id'=>$team_id,'tags'=>$tags,'assign_date'=>time(),"open_date"=>time(),"open_status"=>0,'update_status_date'=>time(),'assign_priority'=>$priority,'assign_follow_up_date'=>$follow_up_date,'assign_contact_by'=>$contact_by[0]);
	
	//print_r($array1);
    //echo "</pre>";
	$insert1= $this->QueryObj->addUserQuery($array1);
	
	       for($i=0;$i< count($no_day);$i++)
				{ 
				if($i==0)
				{
				$status=1;	
				}
				else
				{
				$status=0;	
				}
				
				 $Date1 = date("d-m-Y",strtotime($rec_date));
                 $follow_up_date1= strtotime($Date1. ' + '.$no_day[$i].' days');
				
				 $array2=array('assign_id'=>$insert1,'user_id'=>$allocated_to,'followup_day'=>$follow_up_date1,"query_id"=>$insert,'contact_by'=>$contact_by[$i],'status'=>$status);	
				 $insert2= $this->QueryObj->addUserQueryFolloup($array2);
				}
				
		//////////////////////////////////////////////////////////////////////
		if($upload_file)
		{
			$QueryFilesData=json_decode($upload_file, true);
			   foreach($QueryFilesData as $files)
				{ 
				$array_F=array("query_id"=>$insert,"filename"=>$files['filename'],"file_path"=>$files['file_path']);
				$insertFile=$this->QueryObj->insertQueryFiles($array_F);
				}
		}

////////////////////////////////////////////////////////////////////////////////////////
                 $arrayAct=array("user_id"=>$user_id,'user_name'=>$userInfo->name,'message'=>'Box query created to data manager ','action_date'=>$created_on,'query_id'=>$insert,'query_assign_id'=>$insert1);
				 $this->QueryObj->insertActionHistory($arrayAct);	

                 $arrayAct=array("user_id"=>$user_id,'user_name'=>$userInfo->name,'message'=>'Box query auto claimed by '.$userInfo->username,'action_date'=>time(),'query_id'=>$insert,'query_assign_id'=>$insert1);
				 $this->QueryObj->insertActionHistory($arrayAct);				 
				 
				  $this->boxQueryObj->DeleteBoxQuerySingleId($query_id);
				  
				 ///////////////////////Mail////////////////////////////
				$userprInfo=$this->userObj->getUserProfileInfo($profile_id);
		
				$profile_name	=	explode('-',$userprInfo['profile_name']);			  
				$profile_email	=	$userprInfo['website_email'];
				$profile_signature	=	$userprInfo['signature'];
				
				$crm_name 		= trim($profile_name[0]);
				$crm_company 	= trim($profile_name[1]);
				
				if($profile_email!="" && $email_id!="")
				{
					$subject = 'Your Relationship Manager at '.$crm_company;
					$msg = 'I have been assigned your relationship manager. I will be contacting you through the day today to take discussions ahead.';
					
					if($userprInfo['website'] == 8)		// FiveVidya
					{
						$subject = 'FiveVidya <> PhD Help ';
						$msg = 'At FiveVidya, we are serious about PhD help and I will be your point of contact at FiveVidya. Please feel free to call me or email about your requirements.';
					}
					elseif($userprInfo['website'] == 11)		//PhDGuidance
					{
						$subject = 'Let\'s Begin PhD Research Guidance';
						$msg = 'Thanks for contacting PhD Guidance. We are one of the oldest and most trusted PhD consultation agencies in the country. We started our services in 2006 and till date have completed consultation of over 5000 PhD research scholars.<br/><br/>

						Please share your requirement details and I will proceed accordingly.';
					}
					elseif($userprInfo['website'] == 2)		//ThesisIndia
					{
						$subject = 'EMail from Thesis India';
						$msg = 'Your enquiry is well received at Thesis India and I shall be reaching out to you soon for a discussion about your requirement.<br/><br/>
						
						Let me know your preferred time and mode for discussion.';
					}
					elseif($userprInfo['website'] == 1)		//DissertationIndia
					{
						$subject = 'EMail from Dissertation India';
						$msg = 'Your enquiry is well received at Dissertation India and I shall be reaching out to you soon for a discussion about your requirement.<br/><br/>
						
						Let me know your preferred time and mode for discussion.';
					}
				
					$body='Hi '.$name.'<br/><br/>

					'.$msg.'<br/><br/>
					
					'.$profile_signature.'<br/>';

					$message = array(
						'html' => $body,
						'subject' => $subject,
						'from_email' => $profile_email,
						'from_name' => $crm_name,
						'to' => array(
							array(
								'email'=> $email_id,
								'name' => $name, 
								'type' => 'to'
							)
						)
					);
                     $cc="";
					 $bcc="";
					//SendMandrilMail($message);
					$replyEmail=NULL;
					$headers['ref_id']=$insert1;
					$attachment="";
					$sendResult = $this->postmarkclient->sendEmail($profile_email, $email_id,$replyEmail,$cc,$bcc, $subject,$body,NULL,TRUE,TRUE,$headers,$attachment);
					
					
				}
				///////////////////////////////////////////////////////   
 			    
				
}

return true;
 }	
die; 
}

function sendWhatsappFeedbackAction()
{
//$new_time = date("Y-m-d H:i:s", strtotime('-2 hours', time()));
//$start_time = date("2022-09-19 00:00:00");
	//$queryData=$this->QueryObj->getClinetAddedAfter2Hours($start_time, $new_time);
	//echo "<pre>";
	//print_r($queryData);die;
  $phone='9971126884';
$parameter1='Niranjan';
$template='feedback';
$parameter2='kumar ';
$parameter3='singh';
$lang='';
$headval='';
$this->sendwhatsappmsg_newapi($phone,$parameter1,$template,$parameter2,$parameter3,$lang,$headval);
}

function sendwhatsappmsg_newapi($phone,$parameter1,$template,$parameter2,$parameter3,$lang,$headval)
    {	
    	if($phone !="")
    	{
			if($lang=='')
			{
				$lang = 'en';
			}
			if($headval == ''){
			    $headval='https://interaktprodstorage.blob.core.windows.net/mediaprodstoragecontainer/d75be6ae-38e9-476e-829b-b3caea0dd25a/message_template_media/RLOGkMFZb2Nq/webp.net-resizeimage_4__1.jpg?se=2027-08-20T12%3A24%3A27Z&sp=rt&sv=2019-12-12&sr=b&sig=Bp7BPMDFXqNrEV/5jqdWiI1feYGmJK%2BZTrvS9laK%2BS0%3D';
			}
			$key='N0FlNl9kUnc3LTh0TUR4VThyWEJvZGUybVZaWTdVRWhOaE5kcWdvSFdHYzo=';
			$url='https://api.interakt.ai/v1/public/message/';
			
			if($template=="")
			{
				$template = 'feedback';
			}
			
			if($parameter1!="")
			{
				$data = array(
    			    "countryCode"=>"+91",
    			    "phoneNumber"=>$phone,
    			    "callbackData"=>"some text here",
    			    "type"=>"Template",
    			    "template"=> array(
                        "name"=>$template,
                        "languageCode"=>$lang,
                        "headerValues"=>array($headval),
                        "bodyValues"=>array($parameter1),
    		        )
    			);
			}
			if($parameter1!="" && $parameter2!="")
			{
				$data = array(
    			    "countryCode"=>"+91",
    			    "phoneNumber"=>$phone,
    			    "callbackData"=>"some text here",
    			    "type"=>"Template",
    			    "template"=> array(
                        "name"=>$template,
                        "languageCode"=>$lang,
                        "bodyValues"=>array($parameter1,$parameter2),
    		        )
    			);
			}
			if($parameter1!="" && $parameter2!="" && $parameter3!="")
			{
				$data = array(
    			    "countryCode"=>"+91",
    			    "phoneNumber"=>$phone,
    			    "callbackData"=>"some text here",
    			    "type"=>"Template",
    			    "template"=> array(
                        "name"=>$template,
                        "languageCode"=>$lang,
                        "bodyValues"=>array($parameter1,$parameter2,$parameter3),
    		        )
    			);
			}
			/*echo '<pre>';
			print_r($data);
			echo '</pre>';*/
			$data_string = json_encode($data);
			
			//echo '<pre>';
			//print_r($data_string);
			//echo '</pre>';

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 360);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			  "Authorization: Basic $key",
			  "Content-Type: application/json; charset=utf-8")
			);
			$res=curl_exec($ch);
			echo '<pre>';
			print_r($res);
			echo '</pre>';
			die;
			curl_close($ch);
			
			return true;
    	}
		
    }


public function scheduleEmailAction(){
   
    $conn = new mysqli("localhost","regenpps_ptestinstacrm","qe!kjLH^l8g%","regenpps_ptestinstacrm");

    // Check connection
    if ($conn -> connect_errno) {
        echo "Failed to connect to MySQL: " . $conn -> connect_error;
        exit();
    }else{
        //echo 'hi';
        date_default_timezone_set('Asia/Kolkata');
        $current_date=date('Y-m-d');
        //$current_time=date('2023-02-23 13:05:00');
        $current_time=date('Y-m-d H:i:s');
        $t1=explode(' ',$current_time);
        $t2=$t1[1];
        
        $schedule_email_option='Send Later';
        
        //echo 'SELECT * FROM `tbl_comments` WHERE `schedule_email_time`="'.$t2.'" and `schedule_email_date`="'.$current_date.'" and `schedule_email_option`="'.$schedule_email_option.'"';
        
        
        $querycomments=mysqli_query($conn,'SELECT * FROM `tbl_comments` WHERE `schedule_email_time`="'.$t2.'" and `schedule_email_date`="'.$current_date.'" and `schedule_email_option`="'.$schedule_email_option.'"');
       
        while($result=mysqli_fetch_assoc($querycomments))
        {
             //print_r($result);
            $comment_id=$result['id'];
            $mail_from_email=$result['from_email'];
            $to_email=$result['to_email'];
            $cc=$result['cc_email'];
            $bcc=$result['bcc_email'];
            $mail_subject=$result['subject'];
            $email_body=$result['email_body'];
            $mail_signature=$result['signature'];
            
           
            $body='';
            $body.=$email_body;
            $body.=$mail_signature;


            $lastComment = $this->QueryObj->GetLastClientEmail($result['ass_query_id']);
            
            //print_r($lastComment);
            if($lastComment['message_id'])
            {
                $body.='<div style="color:#500050;margin:0px 0px 0px 0.8ex;border-left:1px solid rgb(204,204,204);padding-left:1ex;"><p>On '.date("l M d, Y",$lastComment['date']).' at '.date("h:i A",$lastComment['date']).' &nbsp; &lt;'.$lastComment['from_email'].'&gt;  wrote:</p>';
                $body.=$lastComment['comments'].$lastComment['email_body'].'</div>';
            }
 

            $fromMailArray=explode("@",$mail_from_email);
            //.$fromMailArray[1]

            //$replyEmail='reply.'.$assign_id.'+'.$this->userInfo->id.'@instacrm.rapidcollaborate.com';
            $replyEmail=NULL;

            $headers['ref_id']=$result['ass_query_id'];
            $attachment='';

           // echo $body;exit;
            
        	$sendResult = $this->postmarkclient->sendEmail($mail_from_email, $to_email,$replyEmail,$cc,$bcc, $mail_subject,$body,NULL,TRUE,TRUE,$headers,$attachment);

/*echo '<pre>';
print_r($sendResult);
echo '</pre>';*/
/*exit;*/

$messageid=$sendResult->messageid;
 if($messageid and $comment_id)
 {
$this->QueryObj->updateComment(array("message_id"=>$messageid,"email_body"=>$body),$comment_id);
$this->QueryObj->updateUserQuery(array("open_status"=>1),$assign_id);
 }
        }
    }
    
    }
}

