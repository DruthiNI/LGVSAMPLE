<?php
 class Dashboard_IndexController extends Zend_Controller_Action
 {
     public function init()
     {
           
        
         /* Initialize action controller here */
 		//parent::init ();
 		//$this->_helper->ViewRenderer->setViewSuffix('php');
 		//Zend_Session::start();	
 		$this->db = Zend_Registry::get('db');
 		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		 
 		 if(!isset($userInfo->id)){
             $this->_redirect('login/');
         } 
		
		 
		 if(isset($userInfo->id) and $userInfo->user_type=='Consultant')
		 {
			$this->_redirect('consultant'); 
		 }
		  if(isset($userInfo->id) and $userInfo->user_type=='Campaign Manager')
		 {
			$this->_redirect('managequery/camp-history'); 
		 }
		  
		 if(isset($userInfo->id) and $userInfo->id==178)
		 {
			$this->_redirect('managequery/camp-history'); 
		 }
		 date_default_timezone_set("Asia/Kolkata");
 		//////////////////Flash message session ////////////////
         $this->_s = Zend_Registry::get('session');
         if ($this->_s->message) {
             $this->view->successmessage = $this->_s->message;
             $this->_s->message = NULL;
         }
 		   $this->view->leftsection='message';
		   
    
		   $this->userObj =$userObj= new Manageuser_Model_Manageuser();
		 
		   $this->QueryObj = new Managequery_Model_Managequery();
		   $this->boxQueryObj = new Managequery_Model_Boxquery();
		   $this->QueryDashboardObj = new Dashboard_Model_Managequery();
		  $this->followupsettingObj = new Followupsetting_Model_Followupsetting();
		
		   $this->teamsObj = new Teams_Model_Teams();
		   $this->tagsObj = new Tags_Model_Tags();
		   $status = new Managequery_Model_Status();
		  $this->view->updateStatus=$status->getAllStatus();
		  $this->smsObj = new Meetingschedule_Model_Meetingschedule();
		  $this->template = new Template_Model_Template();
		  $this->QuoteObj = new Managequote_Model_Managequote();
		 
		   $date = new Zend_Date();
		   
		   $this->view->userInfo = $this->userInfo =$userObj->getUserId($userInfo->id);
		   
		   
		   
		   if($this->getRequest()->getParam('ajaxify')==1)
	     {
		 $this->_helper->layout->disableLayout();
	     }
     }
      public function indexAction()
     {
 
      
      $this->view->tagsArr=$this->tagsObj->getAllCategoryTags($this->userInfo->category);
      $defaultcategory='PhD';
      $this->view->CategoryData=$this->QueryObj->fetchAllCategory($defaultcategory);
	  $this->view->WebsiteData=$this->QueryObj->fetchAllCategoryWebsite($this->userInfo->category);
	  $this->view->serviceData=$this->template->getAllCategoryService($this->userInfo->category);
       $this->view->notUnread=$this->commonNotificationAction('dashboard');	
		$this->view->leftsection='home'; 
		
		if($this->userInfo->user_type=='admin')
		{
	    $whereStr = "user_type = 'Lead Validator'";
		$this->view->userArr =$userfetch= $this->userObj->getAllUsers($whereStr);
		$this->view->WebsiteData=$this->QueryObj->fetchAllWebsite();
		}
		else
		{ // if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='Lead Generator')
		$myTeamUserData= $this->userObj->getAllUsersMyTeams($this->userInfo->team_id);	
		$this->view->userArr =$userfetch= $this->userObj->getSubadminAllUsers(implode(",",$myTeamUserData));	
		}
		
        $whereStr="";
		if($this->userInfo->user_type!='admin')
		{
		if($whereStr!="")
			   {
	         $whereStr .= " AND tm.id in (".$this->userInfo->team_id.")";
		       }
			 else{
		     $whereStr .= " tm.id in (".$this->userInfo->team_id.")";
		         }	
		}
		 
	   $this->view->teamsArr = $this->teamsObj->getAllTeams($whereStr);	
		
	 
    $datediffWeek = time() - strtotime("this week");
 $this->view->thisweekday= round($datediffWeek / (60 * 60 * 24));
 $this->view->thismonthday=date('j');
 	  
     }
	 

public function loadingdashboardAction()
{
	$myTeamUserData=array();
$whereStrt="";
if($this->userInfo->user_type=='Lead Validator')
{
if($whereStrt!="")
{
$whereStrt .= " AND score.user_id='".$this->userInfo->id."'";
}
else{
$whereStrt .= " score.user_id='".$this->userInfo->id."'";
}

}
else if($this->userInfo->user_type=='Lead Generator' or $this->userInfo->user_type=='sub-admin')
{
$myTeamUserData= $this->userObj->getAllUsersMyTeams($this->userInfo->team_id);	
}
 


 
	$usertpe='Lead Generator';

	if($this->userInfo->user_type=='Lead Generator'){
    	$this->view->tatScore = $this->userObj->getAllTATScore($whereStrt,$myTeamUserData,$usertpe,$this->userInfo->id);
	}

        $this->view->teamsArr = $this->teamsObj->getAllTeams();
    
        $this->_helper->layout->disableLayout();
        $limit=20;
		$whereStr="";
		$converted_value="";
		
	
		
		if($this->getRequest()->getParam('searchBtn')!="")
		  {
			  $whereStr ="";
			   
			if($this->getRequest()->getParam('first_filter_day'))
			{
			  if($this->getRequest()->getParam('first_filter_day')=='custom')	
			  {
                 $fromdate=$this->getRequest()->getParam('first_from_date');	
                 $todate=$this->getRequest()->getParam('first_to_date');				 
			  }
			  else
			  {
				 $first_filter_day=$this->getRequest()->getParam('first_filter_day'); 
				 $fromdate= date('m/d/Y', strtotime("-".$first_filter_day."days"));
				 $todate=date("m/d/Y",time());
			  }
			}
			else 
			{
			$this->view->filter_date=$filter_date=$this->getRequest()->getParam('filter_date');
			$dateArray=explode(' - ',$filter_date);
			$fromdate=$dateArray[0]; 
		    $todate=$dateArray[1];	
			}
			
			
			
			
			 $todate = date("m/d/Y",strtotime("+1 days", strtotime($todate)));
			
			//$this->view->search_type=$search_type=$this->getRequest()->getParam('search_type');
			$this->view->ref_id=$ref_id=trim($this->getRequest()->getParam('ref_id'));
			$this->view->search_keywords=$search_keywords=trim($this->getRequest()->getParam('search_keywords'));
  			$this->view->website=$website=$this->getRequest()->getParam('website'); 
  			$this->view->category=$category=$this->getRequest()->getParam('category');
			//$this->view->requirement=$requirement=$this->getRequest()->getParam('requirement');
			if($this->getRequest()->getParam('date_type'))
			{
			$this->view->date_type=$date_type=$this->getRequest()->getParam('date_type');
			}
			else
			{
			$this->view->date_type=$date_type='ass_qr.update_status_date';
			}
			
			if($this->getRequest()->getParam('tags'))
			{
 			 $tags=$this->getRequest()->getParam('tags');
			}
			
            $this->view->converted_value=$converted_value=$this->getRequest()->getParam('converted_value');
			 
			
			
			if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='Lead Generator')
			{
				$myTeamUserData= $this->userObj->getAllUsersMyTeams($this->userInfo->team_id);  
			}

            $teamid=$this->getRequest()->getParam('teamid');
			if($teamid)
			{
			$myTeamUserData= $this->userObj->getAllUsersMyTeams($teamid);	
			}
			 
            $this->view->userid=$userid=$this->getRequest()->getParam('userid');
			
			if($this->userInfo->user_type=='admin'){
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
 			}
			/*if($fromdate and $todate)
			{ 
              if($whereStr!="")
			   {
	         $whereStr .= " AND  ass_qr.update_status_date BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		       }
			 else{
		     $whereStr .= " ass_qr.update_status_date BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		         }
 			}*/
			 
			
			if($website)
			{
		      $keyword = $website;
              if($whereStr!="")
			   {
	         $whereStr .= " AND ass_qr.website_id LIKE '%".$keyword."%'";
		       }
			 else{
		     $whereStr .= " ass_qr.website_id LIKE '%".$keyword."%'";
		         }
		    }

		   
		    
			if($ref_id)
			{
		      $keyword = $ref_id;
              if($whereStr!="")
			   {
	         $whereStr .= " AND ass_qr.id = '".$keyword."'";
		       }
			 else{
		     $whereStr .= " ass_qr.id = '".$keyword."'";
		         }
		    }
			 
			
			
 		  }
		  
		  $whereStr2=$whereStr;
		  if($search_keywords)
			{
		      $keyword = trim($search_keywords);
              if($whereStr2!="")
			   {
	         $whereStr2 .= ") AND (query.name LIKE '%".$keyword."%' or query.email_id LIKE '%".$keyword."%' or query.phone LIKE '%".$keyword."%'";
		       }
			 else{
			$whereStr2 .= " query.name LIKE '%".$keyword."%' or query.email_id LIKE '%".$keyword."%' or query.phone LIKE '%".$keyword."%'";
		     
		         }
		    }

		     if($category!='')
			{
				//echo $category;
		     if($whereStr2!="")
			   {
	         $whereStr2 .= ") AND (query.requirement = '".$category."'  ";
		       }
			 else{
			 $whereStr2 .= "query.requirement = '".$category."' ";
		     
		         }
		    }
		    
		   // print_r($whereStr2);
		     if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='admin'){
		  $this->view->openTask=$this->QueryDashboardObj->selectAllOpenTask($whereStr2,$tags,'');
		  }
		  else if($this->userInfo->user_type=='Lead Generator'){
		  	$addedby_user_id=$this->userInfo->id;
		  	 $currentDate=date("m/d/Y",time());
		  	$this->view->openTask=$this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr2,500,$tags,$addedby_user_id,strtotime($currentDate));
		  }
		  else{
		  	$curruserid=$this->userInfo->id;
		  	 $this->view->openTask=$this->QueryDashboardObj->selectAllOpenTask($whereStr2,$tags,$curruserid);
		  }

		  if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='admin'){
		  $currentDate=date("m/d/Y",time()); 
		  $this->view->todayTask=$this->QueryDashboardObj->selectAllNotificationQuery($whereStr2,500,strtotime($currentDate),$tags,'');
		  }
		  else if($this->userInfo->user_type=='Lead Generator'){
		  	$addedby_user_id=$this->userInfo->id;
		  	 $currentDate=date("m/d/Y",time());
		  	$this->view->todayTask=$this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr2,500,$tags,$addedby_user_id,strtotime($currentDate));
		  }
		  else{
		  	$curruserid=$this->userInfo->id;
		  	 $this->view->todayTask=$this->QueryDashboardObj->selectAllNotificationQuery($whereStr2,500,strtotime($currentDate),$tags,$curruserid);
		  }
		  

		  if($this->userInfo->user_type=='Lead Generator'){
		  	$addedby_user_id=$this->userInfo->id;
		  	$this->view->pendingTask=$this->QueryDashboardObj->selectAllPendingNotificationofLeadGeneratorQuery($whereStr2,500,$tags,$addedby_user_id);
		  }else{
		  	$this->view->pendingTask=$this->QueryDashboardObj->selectAllPendingNotificationQuery($whereStr2,500,strtotime($currentDate),$tags);
		 }
		  
		  
		  
		$whereStr1=$whereStr;
		if($fromdate and $todate)
			{ 
             
				  if($date_type=='date')
			 {
			  if($whereStr1!="")
			   {
	         $whereStr1 .= " AND  query.date BETWEEN '".$fromdate."' AND '".$todate."'";
		       }
			 else{
		     $whereStr1 .= " query.date BETWEEN '".$fromdate."' AND '".$todate."'";
		         }	 
			 }
			 else
			 {
			 if($whereStr1!="")
			   {
	         $whereStr1 .= " AND  ass_qr.assign_date BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		       }
			 else{
		     $whereStr1 .= " ass_qr.assign_date BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		         }	 
			 } 
 			}
			if($search_keywords)
			{
		      $keyword = trim($search_keywords);
              if($whereStr1!="")
			   {
	         $whereStr1 .= ") AND (query.name LIKE '%".$keyword."%' or query.email_id LIKE '%".$keyword."%' or query.phone LIKE '%".$keyword."%'";
		       }
			 else{
			$whereStr1 .= " query.name LIKE '%".$keyword."%' or query.email_id LIKE '%".$keyword."%' or query.phone LIKE '%".$keyword."%'";
		     
		         }
		    }
		     if($category!='')
			{
				//echo $category;
		     if($whereStr1!="")
			   {
	         $whereStr1 .= ") AND (query.requirement = '".$category."'  ";
		       }
			 else{
			 $whereStr1 .= "query.requirement = '".$category."' ";
		     
		         }
		    }
			
			
		  //echo $whereStr;die;
		  
		///////////////////////////////////////////////All Leads/////////////////////////////////////////////////
 		

		if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='admin'){
$this->view->LeadsData=$LeadsData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr1,1,$limit,$tags,'');
//print_r($LeadsData);
$this->view->LeadsCount=$LeadsCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr1,1,"",$tags,''));
		}
		 else if($this->userInfo->user_type=='Lead Generator'){
		  	$addedby_user_id=$this->userInfo->id;
		  	$this->view->LeadsData=$LeadsData=$this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr1,500,$tags,$addedby_user_id,1,'');
		  	$this->view->LeadsCount=$LeadsCount=count($this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr1,500,$tags,$addedby_user_id,1,''));
		  }
		else{
			$curruserid=$this->userInfo->id;
			$this->view->LeadsData=$LeadsData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr1,1,$limit,$tags,$curruserid);
			$this->view->LeadsCount=$LeadsCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr1,1,"",$tags,$curruserid));
		}
		
		  
		  
  
 
		  
		  
		
		if($fromdate and $todate)
			{ 
             /* if($whereStr!="")
			   {
	         $whereStr .= " AND  ass_qr.update_status_date BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		       }
			 else{
		     $whereStr .= " ass_qr.update_status_date BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		         }
				 */
			 if($date_type=='date')
			 {
				 if($whereStr!="")
			   {
	         $whereStr .= " AND  query.date BETWEEN '".$fromdate."' AND '".$todate."'";
		       }
			 else{
		     $whereStr .=  " query.date BETWEEN '".$fromdate."' AND '".$todate."'";
		         } 
			 }
		else
		{
		    if($whereStr!="")
			   {
	         $whereStr .= " AND  ".$date_type." BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		       }
			 else{
		     $whereStr .= $date_type." BETWEEN '".strtotime($fromdate)."' AND '".strtotime($todate)."'";
		         }	
		}
 			}
			
			if($search_keywords)
			{
		      $keyword = trim($search_keywords);
              if($whereStr!="")
			   {
	         $whereStr .= ") AND (query.name LIKE '%".$keyword."%' or query.email_id LIKE '%".$keyword."%' or query.phone LIKE '%".$keyword."%'";
		       }
			 else{
			$whereStr .= " query.name LIKE '%".$keyword."%' or query.email_id LIKE '%".$keyword."%' or query.phone LIKE '%".$keyword."%'";
		     
		         }
		    }

		     if($category!='')
			{
				//echo $category;
		     if($whereStr!="")
			   {
	         $whereStr .= ") AND (query.requirement = '".$category."'  ";
		       }
			 else{
			 $whereStr .= "query.requirement = '".$category."' ";
		     
		         }
		    }
			//echo $whereStr;
			// echo $whereStr;die;
			
				
		///////////////////////////////////////////Contact Made///////////////////////////////////////////////////
if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='admin'){

 		$this->view->ContactMadeData=$ContactMadeData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,2,$limit,$tags,'');
 		$this->view->ContactMadeCount=$ContactMadeCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,2,"",$tags,''));
 	}
 	else if($this->userInfo->user_type=='Lead Generator'){
		  	$addedby_user_id=$this->userInfo->id;
		  	$this->view->ContactMadeData=$ContactMadeData=$this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr,500,$tags,$addedby_user_id,2,'');
		  	$this->view->ContactMadeCount=$ContactMadeCount=count($this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr,500,$tags,$addedby_user_id,2,''));
		  }
 	else{
 		$curruserid=$this->userInfo->id;
 		$this->view->ContactMadeData=$ContactMadeData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,2,$limit,$tags,$curruserid);
 		$this->view->ContactMadeCount=$ContactMadeCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,2,"",$tags,$curruserid));
 	}	
 	
		 
		
		
		///////////////////////////////////////////Quoted///////////////////////////////////////////////////
 		if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='admin'){
 		$this->view->QuotedData=$QuotedData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,3,$limit,$tags,'');
 		$this->view->QuotedCount=$QuotedCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,3,"",$tags,''));
 		}
 		else if($this->userInfo->user_type=='Lead Generator'){
		  	$addedby_user_id=$this->userInfo->id;
		  	$this->view->QuotedData=$QuotedData=$this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr,500,$tags,$addedby_user_id,3,'');
		  	$this->view->QuotedCount=$QuotedCount=count($this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr,500,$tags,$addedby_user_id,3,''));
		  }
 		else{
 				$curruserid=$this->userInfo->id;
 				$this->view->QuotedData=$QuotedData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,3,$limit,$tags,$curruserid);
 		$this->view->QuotedCount=$QuotedCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,3,"",$tags,$curruserid));
 		}
 		
 		
		
		
		////////////////////////////////// Follow Up/////////////////////
		if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='admin'){
			$this->view->FollowUpData=$FollowUpData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,9,$limit,$tags,'');
		}
		else if($this->userInfo->user_type=='Lead Generator'){
		  	$addedby_user_id=$this->userInfo->id;
		  	$this->view->FollowUpData=$FollowUpData=$this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr,500,$tags,$addedby_user_id,9,'');
		  	}
		else{
			$curruserid=$this->userInfo->id;
			$this->view->FollowUpData=$FollowUpData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,9,$limit,$tags,$curruserid);
		}
		///////////////////////////////////////////Negotiating///////////////////////////////////////////////////
 		/*$this->view->NegotiatingData=$NegotiatingData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,4,$limit);
		$this->view->NegotiatingCount=$NegotiatingCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,4,""));
		*/
		///////////////////////////////////////////Converted///////////////////////////////////////////////////
 		if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='admin'){
 		$this->view->ConvertedData=$ConvertedData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,5,$limit,$tags,'');
 		
		$this->view->ConvertedCount=$ConvertedCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,5,"",$tags,''));
		}
		else if($this->userInfo->user_type=='Lead Generator'){
		  	$addedby_user_id=$this->userInfo->id;
		  	$this->view->ConvertedData=$ConvertedData=$this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr,500,$tags,$addedby_user_id,5,'');
		  	$this->view->ConvertedCount=$ConvertedCount=count($this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr,500,$tags,$addedby_user_id,5,''));
		  }
		else{
			$curruserid=$this->userInfo->id;
			$this->view->ConvertedData=$ConvertedData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,5,$limit,$tags,$curruserid);
 		
		$this->view->ConvertedCount=$ConvertedCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,5,"",$tags,$curruserid));
		}
		///////////////////////////////////////////Client Not Interested///////////////////////////////////////////////////
		if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='admin'){
 		$this->view->NotInterestedData=$NotInterestedData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,6,$limit,$tags,'');
 			
		
		$this->view->NotInterestedCount=$NotInterestedCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,6,"",$tags,''));
		}
		else if($this->userInfo->user_type=='Lead Generator'){
		  	$addedby_user_id=$this->userInfo->id;
		  	$this->view->NotInterestedData=$NotInterestedData=$this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr,500,$tags,$addedby_user_id,6,'');
		  	$this->view->NotInterestedCount=$NotInterestedCount=count($this->QueryDashboardObj->selectAllLeadsofLeadGeneratorQuery($whereStr,500,$tags,$addedby_user_id,6,''));
		  }
		else{
			$curruserid=$this->userInfo->id;
			$this->view->NotInterestedData=$NotInterestedData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,6,$limit,$tags,$curruserid);
 			
		
		$this->view->NotInterestedCount=$NotInterestedCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,6,"",$tags,$curruserid));
		}
		
		///////////////////////////////////////////Lost Deals///////////////////////////////////////////////////
 		$this->view->LostDealsData=$LostDealsData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,8,$limit,$tags);
 		
		$this->view->LostDealsCount=$LostDealsData=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,8,"",$tags));
		
		
		
		$this->view->escalationTask=$this->QueryDashboardObj->selectAllEscalation($whereStr1,$tags);
		
		
		///////////////////////////////////////////Delay///////////////////////////////////////////////////
		 $rem_date=strtotime(date("m/d/Y"));
		 /* 
              if($whereStr!="")
			   {
	         $whereStr .= " AND  ass_qr.remainder_date = '".$rem_date."' ";
		       }
			 else{
		     $whereStr .= " ass_qr.remainder_date = '".$rem_date."'";
		         }
 			 */
		
 		$this->view->DelayData=$DelayData=$this->QueryDashboardObj->selectAllDashboardQuery($whereStr,7,$limit,$tags);
 		
 		
		$this->view->DelayCount=$DelayCount=count($this->QueryDashboardObj->selectAllDashboardQuery($whereStr,7,"",$tags));
		
		 
	  
	$this->view->notUnread=$this->commonNotificationAction('dashboard');	
			
		 
}
 
public function dashboardConversionSummaryAction()
{

$this->_helper->layout->disableLayout();
	 /*if($this->getRequest()->getParam('ajaxify')==1)
	{
		 $this->_helper->layout->disableLayout();
	}*/

 
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
 
/*if($this->getRequest()->getParam('filter_date'))
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
 	}*/

 
$usertpe='Lead Generator';

if($this->userInfo->user_type=='Lead Generator'){
    $this->view->tatScore = $tatScore=$this->userObj->getAllTATScore($whereStr,$myTeamUserData,$usertpe,$this->userInfo->id);
}


 
}
 




 public function updatequerystatusAction()
 {
     $this->_helper->layout->disableLayout();
 
	 if($this->getRequest()->getParam('sender_status')!="" and $this->getRequest()->getParam('receiver_status') and $this->getRequest()->getParam('query_id'))
	 {
 $sender_status=$this->getRequest()->getParam('sender_status');

 $receiver_status=$this->getRequest()->getParam('receiver_status');
 $update_status= str_replace("sortable","",$receiver_status);
 $query_id=$this->getRequest()->getParam('query_id');
 $remainder_date=strtotime($this->getRequest()->getParam('remainder_date'));
 
	 if($update_status==5)
	 {
		$converted=1; 
	 }
     else
	 {
	    $converted=0; 	
     }
     $array=array('action_taken'=>1,'converted'=>$converted,'update_status'=>$update_status,'update_status_date'=>time(),"remainder_date"=>$remainder_date);
	
 	 $update= $this->QueryObj->updateUserQuery($array,$query_id); 
 	}
	
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
	 $this->view->internalCommentData =$this->QueryObj->GetComentsAssign($QueryInfo['assign_id'],'internalComments');
	 
	 $this->view->tagsArr=$this->tagsObj->getAllCategoryTags($this->userInfo->category);
	 $this->view->TatScore=$this->userObj->getParticularTatScore($QueryInfo['assign_id']);
	 //$this->view->campaginCommentData=$this->QueryObj->getCampaignComment($query_id);
	 
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
	         $whereStr .= " AND  assign_user ='".$this->userInfo->id."'";
		       }
			 else
		     {
		     $whereStr .= "  assign_user ='".$this->userInfo->id."'";
		     }
			/* if($whereStr!="")
			   {
	         $whereStr .= " AND  find_in_set(".$user_id.", assign_user) <> 0";
		       }
			 else
		     {
		     $whereStr .= "  find_in_set(".$user_id.", assign_user) <> 0";
		     }
			 */
			 
			  
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
	
    $whereStr="";
    if($this->userInfo->user_type!='admin')
    {
        if($whereStr!="")
        {
            $whereStr .= " AND tm.id in (".$this->userInfo->team_id.")";
        }
        else{
            $whereStr .= " tm.id in (".$this->userInfo->team_id.")";
        }	
    }
    
    $this->view->teamsArr = $this->teamsObj->getAllTeams($whereStr);
    
    	if($this->userInfo->user_type=='admin')
		{
	    $whereStr = "user_type = 'Lead Validator'";
		$this->view->userArr =$userfetch= $this->userObj->getAllUsers($whereStr);
		$this->view->WebsiteData=$this->QueryObj->fetchAllWebsite();
		}
		else
		{ // if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='Lead Generator')
		$myTeamUserData= $this->userObj->getAllUsersMyTeams($this->userInfo->team_id);	
		$this->view->userArr =$userfetch= $this->userObj->getSubadminAllUsers(implode(",",$myTeamUserData));	
		}
 }
 
 
  
  public function getquerycommentsAction()
 {  $this->_helper->layout->disableLayout();
if($this->getRequest()->getParam('assign_qid'))
	{
$assign_qid=$this->getRequest()->getParam('assign_qid');
$archive_no=$this->getRequest()->getParam('archive_no');
$this->view->QueryInfo=$QueryInfo = $this->QueryObj->getViewQueryId($assign_qid);
	//print_r($QueryInfo); die;
	/*if($QueryInfo['transfer_type']==1)
	{
	$this->view->CommentInfo = $this->QueryObj->GetComents($QueryInfo['id']);	
	}
	else
	{
	$this->view->CommentInfo = $this->QueryObj->GetComentsAssign($QueryInfo['assign_id']);	
	}*/
	
	$this->view->CommentInfo = $this->QueryObj->GetComentsAssign($QueryInfo['assign_id'],$archive_no);
	
	
	}	

 }
 
 public function getActivityHistoryAction()
 {

if($this->getRequest()->getParam('assign_id'))
{
$assign_id=$this->getRequest()->getParam('assign_id');
$actionHistoryData=$this->QueryObj->GetActionHistory($assign_id);
$array=array();
foreach($actionHistoryData as $data)
{
	$array[]=array(
	"user_name"=>$data['user_name'],
	"message"=>$data['message'],
	"action_date"=>date("d M Y h:i:s A",$data['action_date'])
	);
} 

 

echo json_encode(array("activity"=>$array));die;
}
die;
 }
 
 public function getFollowupHistoryAction()
 {
	 $this->_helper->layout->disableLayout();
	if($this->getRequest()->getParam('assign_id'))
	{
	$assign_id=$this->getRequest()->getParam('assign_id');
	  
	$this->view->followupData=$this->QueryObj->GetFollowupData($assign_id);
	}	 
 }
 
 public function commonNotificationAction($dashboard="")
 {
	 
 $currentDate=date("m/d/Y",time()); 
 $Date = date("m/d/Y",strtotime($currentDate));
 $currentDate= strtotime($Date. ' - '.(1).' days');
 $currentDate= strtotime($Date. ' + '.(1).' days');
	 
 $this->QueryObj->AutoLoadCron($currentDate);	 
	 
 
if($this->userInfo->user_type=='sub-admin' or $this->userInfo->user_type=='Lead Generator')
{
	$myTeamUserData= $this->userObj->getAllUsersMyTeams($this->userInfo->team_id); 
 	$profileData=$this->userObj->getUserProfilesEmails($this->userInfo->id,$myTeamUserData);
}
else
{
$profileData=$this->userObj->getUserProfilesEmails($this->userInfo->id);	
}
$webEmails=array();
foreach($profileData as $emailVal)
{
$webEmails[]=$emailVal['website_email'];
}
$ClientMailExternal=$this->QueryObj->FetchAllUnreadClientMailExternal($webEmails,$this->userInfo->user_type);
	
	  
 $pendingQuoteNoti=$this->QuoteObj->FetchAllUnreadNotification($this->userInfo->id);
 
 if($dashboard)
 {
 return array("TotalNoti"=>count($pendingQuoteNoti),"ClientTotalUnraed"=>count($ClientMailExternal));	 
 }
 else
 {
 $arrayNoti=array();
 foreach($pendingQuoteNoti as $noti)
 {
	$arrayNoti[]=array(
	"ref_id"=>$noti['ref_id'],
	"id"=>$noti['id'],
	"name"=>$noti['name'],
	"message"=>ucfirst($noti['message']),
	"date"=>date("d M, Y h:i A",$noti['date']),
	); 
 }
 echo json_encode(array("arrayNoti"=>$arrayNoti,"TotalNoti"=>count($pendingQuoteNoti),"ClientTotalUnraed"=>count($ClientMailExternal)));	 
 }

	die; 
 }
function getDay($day)
{
    $days = ['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7];

    $today = new \DateTime();
    $today->setISODate((int)$today->format('o'), (int)$today->format('W'), $days[ucfirst($day)]);
    return $today;
}




public function getcommentsarchivetabsAction()
{
$this->_helper->layout->disableLayout();
if($this->getRequest()->getParam('assign_id'))
	{
	$this->view->assign_id=$assign_id=$this->getRequest()->getParam('assign_id');
	  
	$this->view->UnArchiveData=$this->QueryObj->GetQueryUnArchive($assign_id);
	$this->view->ArchiveData=$this->QueryObj->GetQueryArchiveNo($assign_id);
	}
	
}
public function saveCommentsArchiveAction()
{
$this->_helper->layout->disableLayout();
if($this->getRequest()->getParam('assign_id'))
	{
$this->view->assign_id=$assign_id=$this->getRequest()->getParam('assign_id');
$archivedata=$this->QueryObj->GetLastArchiveByQuery($assign_id);
$archive_no=$archivedata->archive_no+1;
$update=$this->QueryObj->saveCommentsArchive(array("archive_no"=>$archive_no), $assign_id);
echo 1; die;
	}
}
public function showInternalcommentsAction()
{
$this->_helper->layout->disableLayout();
$ref_id=$this->getRequest()->getParam('ref_id');	
$this->view->CommentInfo = $CommentInfo=$this->QueryObj->GetComentsAssign($ref_id,'internalComments');
}

public function logoutAction()
{
	Zend_Auth::getInstance()->clearIdentity();
	 $this->_redirect(SITEURL.'login');
}

public function getboxquerydetailsAction()
{
	$this->_helper->layout->disableLayout();
 
	 if($this->getRequest()->getParam('query_id'))
	 {
    $genericWebsiteIds=array(1,4,8,11);
	$profileData=$this->userObj->getUserProfiles($this->userInfo->id);
	$genericWebsite=array();
	if(count($profileData) > 0)
	{
		foreach($profileData as $web)
		{
		if(in_array($web['website'], $genericWebsiteIds))
		{
		$genericWebsite[]=array("id"=>$web['website'], "website_name"=>$web['website_name']);	
		}
		}
	}	
    $this->view->genericWebsite=$genericWebsite;
	 	
     $query_id=$this->getRequest()->getParam('query_id');
    $this->view->QueryInfo= $this->boxQueryObj->getBoxQueryDetails($query_id);
 
	 }
} 


public function getActivityHistoryDashboardAction()
 {

if($this->getRequest()->getParam('assign_id'))
{
$assign_id=$this->getRequest()->getParam('assign_id');
$actionHistoryData=$this->QueryObj->GetActionHistory($assign_id);

	$html = '<div>';
foreach($actionHistoryData as $data)
{
  $username = $data['user_name'];
  $message =$data['message'];
  $action_date = date("d M Y h:i:s A",$data['action_date']);
  
  $html .= "<span>".$username.' '.$message.' '.$action_date."</span><br/>";
 
}
$html .= '</div>';
echo $html;
} 
die;
 
}

 }
 
