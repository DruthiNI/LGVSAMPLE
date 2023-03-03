<?php
class Managequery_Model_Managequery {
  function __construct() {
  $this->query = new Managequery_Model_DbTable_Managequery();
  $this->userquery = new Managequery_Model_DbTable_Manageuserquery();
  $this->followDate = new Managequery_Model_DbTable_Followupdate();
  $this->comments = new Managequery_Model_DbTable_Comments();
  $this->query_file= new Managequery_Model_DbTable_Files();
  $this->action_history= new Managequery_Model_DbTable_Actionhistory();
  $this->tat_score= new Managequery_Model_DbTable_Tatscore();
  $this->client_external_mail= new Managequery_Model_DbTable_Clientexternalmail();
  $this->campaign_comments= new Managequery_Model_DbTable_Campaigncomments();
  
  
 		$this->db = Zend_Registry::get('db');
		
		$this->userInfo=$userInfo = Zend_Auth::getInstance()->getStorage()->read();
     }
     /*

      * 

      * Date: 14-OCT-2015

      * Developer: Niranjan Singh

      * Modified By: Niranjan Singh

      * @param: id: int

      */

 	  public function addQuery($data) {

         $id = $this->query->insert($data);

         return $id;

     }
	 public function insertCampaignComment($data)
	 {
	$id = $this->campaign_comments->insert($data);

         return $id;	 
	 }
   function insertActionHistory($data)
   {
	$id = $this->action_history->insert($data);
    return $id;   
   }
   
   function insertTATScore($data)
   {
	$id = $this->tat_score->insert($data);
    return $id;   
   }
   
   function insertClientExternalmail($data)
   {
	$id = $this->client_external_mail->insert($data);
    return $id;   
   }
  
   function getidofLastQueryofuser(){
        $select = $this->query->select()->order('id DESC limit 1');
 $temps = $this->query->fetchRow($select);
 
 return $temps;	
      	
      
   }
   function getAssignQuerydetailsofuser($queryid){
         $select = $this->userquery->select()->where('query_id = ?', ($queryid));
      $Result =  $this->userquery->fetchRow($select);
 	 return $Result;
      	
      
   }
   
   
   function checkTatScoreExist($user_id,$ref_id,$in_time)
   {
	   $select = $this->tat_score->select()->where('user_id = ?', ($user_id))->where('ref_id = ?', ($ref_id))->where('in_time = ?', ($in_time));
      $Result =  $this->tat_score->fetchAll($select);
 	 return $Result;
	 
	    
   }
   
  public function CheckExistQuery($name,$email_id,$website)
  {
	$select = $this->db->select();
    $select->from(array('ass_qr'=>'tbl_assign_query', array('id')))
 	->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('created_on'))
	->where('query.email_id = ?', ($email_id))
    ->where('ass_qr.website_id = ?', ($website))->order("ass_qr.id desc");
     $temps = $this->db->fetchRow($select);
     return $temps;  
	     
	}
	
    public function	CheckExistQueryEmail($email_id){
        
      $select = $this->db->select();
    $select->from(array('ass_qr'=>'tbl_assign_query', array('id','assign_date','update_status')))
 	->join(array('query' => 'tbl_query'), 'query.id = ass_qr.query_id', array("name as client_name","email_id as client_email"))
	->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array("name as user_name"))
	->joinLeft(array('st' => 'tbl_update_status'), 'ass_qr.update_status = st.id', array("status_name"))
	->where('query.email_id = ?', ($email_id));
     $temps = $this->db->fetchAll($select);
     return $temps;  
    }
	public function CheckExistQueryEmailAndWebsite($email_id,$website)
	{
	$select = $this->db->select();
    $select->from(array('ass_qr'=>'tbl_assign_query', array('id','assign_date','update_status')))
 	->join(array('query' => 'tbl_query'), 'query.id = ass_qr.query_id', array("name as client_name","email_id as client_email"))
	->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array("name as user_name"))
	->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array("website"))
	->joinLeft(array('st' => 'tbl_update_status'), 'ass_qr.update_status = st.id', array("status_name"))
	->where('query.email_id = ?', ($email_id))
    ->where('ass_qr.website_id = ?', ($website));
     $temps = $this->db->fetchAll($select);
     return $temps;	
	}
	
	public function getMatchingEmailCleints($email_id)
	{
	$select = $this->db->select();
    $select->from(array('ass_qr'=>'tbl_assign_query'))
 	->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id',array('email_id'))
	->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array("name as user_name"))
	->where('query.email_id = ?', ($email_id));
	$temps = $this->db->fetchAll($select);
     return $temps;	
	}
 

 	 public function getQueryId($queryid)
 	 {
 	  $select = $this->query->select()->where('id = ?', ($queryid));
      $Result =  $this->query->fetchRow($select);
 	 return $Result;
      }
	  public function getEditQueryInfo($ass_query_id)
	  {
 $select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','team_id','profile_id','tags','assign_priority','assign_priority','assign_contact_by','website_id','other_website','id as assign_id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id')
	   ->where("ass_qr.id =?", $ass_query_id);
       $temps = $this->db->fetchRow($select);
         return $temps;	  
	  }

     /*



       /*



      * Date: 14-OCT-2015



      * Developer: Niranjan Singh



      * Modified By: Niranjan Singh



      * @param: data: array



      * @param: $template_id: int



      */



 



    public function updateQuery($data, $queryid) 
	{ //print_r($data);die;
	  if (Zend_Validate::is($queryid, 'Int'))
		  {
	  $this->query->update($data, 'id = ' . $queryid);
	   }
 }
 
	public function DeleteQuery($queryid)
  	{  
 	  $id=implode(',',$queryid);  
  	  $this->db->delete('tbl_query','id in('.$id.')');
 	  $this->db->delete('tbl_assign_query','query_id in('.$id.')');
 	  $this->db->delete('tbl_followup_date','query_id in('.$id.')');
	  $this->db->delete('tbl_comments','query_id in('.$id.')');
	  $this->db->delete('tbl_meeting_schedule','client_id in('.$id.')');
	  $this->db->delete('tbl_query_action_history','query_id in('.$id.')');
	  $this->db->delete('tbl_query_files','query_id in('.$id.')');
	  //$this->db->delete('tbl_campaign_comments','query_id in('.$id.')');
   	}
	public function DeleteQueryBefore2019($queryid)
  	{  
	  $id=implode(',',$queryid);  
  	  $this->db->delete('tbl_query','id in('.$id.')');
 	  $this->db->delete('tbl_assign_query','query_id in('.$id.')');
 	  $this->db->delete('tbl_followup_date','query_id in('.$id.')');
	  $this->db->delete('tbl_comments','query_id in('.$id.')');
	  $this->db->delete('tbl_meeting_schedule','client_id in('.$id.')');
	  $this->db->delete('tbl_query_action_history','query_id in('.$id.')');
	  $this->db->delete('tbl_query_files','query_id in('.$id.')');
	  //$this->db->delete('tbl_campaign_comments','query_id in('.$id.')');
   	}

public function DeleteFllowupAssignQuery($assign_id)
{
 	  $this->db->delete('tbl_followup_date','assign_id in ('.$assign_id.')');
	 
}

 public function DeleteAssignQuery($queryid)
 {
	
  $id=implode(',',$queryid); 
    
    $querySql=	$this->db->select("query_id")->from('tbl_assign_query')->where("id IN(?)", $queryid)->group("query_id");
    $temps = $this->db->fetchAll($querySql);
	 
      $this->db->delete('tbl_assign_query','id in('.$id.')');
 	  $this->db->delete('tbl_followup_date','assign_id in('.$id.')');	
	  $this->db->delete('tbl_comments','ass_query_id in('.$id.')'); 
	  $this->db->delete('tbl_query_action_history','query_assign_id in('.$id.')');
	 $qIds=array();
	  foreach($temps as $query)
	 {
    $querySql1=	$this->db->select("query_id")->from('tbl_assign_query')
	->where("query_id  = ?", $query['query_id']);
    $temps1 = $this->db->fetchAll($querySql1);
	if(count($temps1)==0)
	{
	$qIds[]=$query['query_id'];	
	}
	 }
	
	if(count($qIds) > 0)
	{
	$this->DeleteQuery($qIds);
	}
  
 }

	 public function getAllQuery() {
         $select = $this->db->select();
		           $select->from(array('qr'=>'tbl_query'))
				   ->joinLeft(array('web' => 'tbl_website'), 'qr.website = web.id', array('website as website_name'))
				 ->limit(50)
				 ->order('qr.created_on DESC');
				 $temps = $this->db->fetchAll($select);
				 return $temps;
				 
      }

 

	  public function getAllSubadminQuery($allocateduser)
	  {
		$id=explode(',',$allocateduser);
 $select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','id as assign_id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	    ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'))
	   ->where("ass_qr.user_id IN(?)", $id)->group ('ass_qr.query_id')->order("id desc")
	   ->limit(50); 
       $temps = $this->db->fetchAll($select);
         return $temps;	  



	  }



	  



	  public function getAllSubadminSearchQuery($allocateduser,$whereStr)
	  {
   $id=explode(',',$allocateduser);
  $select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','id as assign_id'))
 ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
  ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
  ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'))
 ->where("ass_qr.user_id IN(?)", $id)->group ('ass_qr.query_id')->where($whereStr)->order("id desc"); 
 $temps = $this->db->fetchAll($select);
 return $temps;	  
 }



	  



	   public function getAllSearchQuery($whereStr)
	   {
		     $select = $this->db->select();
		          $select->from(array('qr'=>'tbl_query'))
				 ->joinLeft(array('web' => 'tbl_website'), 'qr.website = web.id', array('website as website_name'))
                ->where($whereStr)
				 ->order('qr.created_on DESC');
				 $temps = $this->db->fetchAll($select);
				 return $temps;
	 
      }
 
	public function getAllUserQuery($user_id)
 {
 $select = $this->query->select()->where('allocated_to = ?',$user_id)->order('id DESC');
 $temps = $this->query->fetchAll($select);
 return $temps;	
 
 
 }
 
//////////////////////////////////////////////////////////////////////////////////////
 
  public function addUserQuery($data) {
 $id = $this->userquery->insert($data);
  return $id;
  }



public function fetchQueryUsers($queryid)



{



$select = $this->userquery->select()->where('query_id = ?',$queryid)->where("user_status = ?",1)->order('id DESC');



         $temps = $this->userquery->fetchAll($select);



         return $temps;	



}



public function CheckQueryUsers($userid,$queryid)
{
$select = $this->userquery->select()->where('user_id = ?',$userid)->where('query_id = ?',$queryid);
$temps = $this->userquery->fetchAll($select);
return $temps;
}

public function CheckQueryRepliUsers($userid,$queryid,$profile_id)
{
$select = $this->userquery->select()
->where('user_id = ?',$userid)
->where('query_id = ?',$queryid)
->where('profile_id = ?',$profile_id);
$temps = $this->userquery->fetchAll($select);
return $temps;	
}


public function FetchAssignIdByQueryAndUser($userid,$queryid)
{
$select = $this->userquery->select()->where('user_id = ?',$userid)->where('query_id = ?',$queryid);
$temps = $this->userquery->fetchRow($select);
return $temps;  		
}




public function FetchAllUserQuery($whereStr="",$myTeamUserData="",$search_keywords="",$tags="",$ifCampgn="")
{
$select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','transfer_type','trans_repli_user','website_id','other_website','tags','state','id as assign_id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','requirement','priority','word_count','academic_level','requirement','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->order("assign_date desc")
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'));
	  //->joinLeft(array('tag' => 'tbl_tags'), ' tag.id IN( ass_qr.tags)', array('tag_name'));
	   //->where('ass_qr.converted != ?',2)
	   //->where('ass_qr.user_status = ?',1);
	   if($search_keywords)
	   {
		$select->where(" query.name LIKE '%".$search_keywords."%' or query.email_id LIKE '%".$search_keywords."%' or query.phone LIKE '%".$search_keywords."%'");   
	   }
	   
	   if($myTeamUserData)
	   {
		$select->where('ass_qr.user_id IN(?)', $myTeamUserData); 
	   }
	   
	   
	 if(count($tags) > 0)
	 {
		$i=0;
foreach($tags as $tag)
{
	
$i++;
if($i ==1)
{
$select->where('find_in_set("'.$tag.'", tags) <> 0');
}
else
{
$select->ORwhere('find_in_set("'.$tag.'", tags) <> 0');
}	
 
} 
	 }		 
	   
	   /*if($ifCampgn==1)
	   {
		$select->where('ass_qr.if_campagn =?', $ifCampgn); 
		$select->where('ass_qr.assign_campaign_user =?', $this->userInfo->id); 
	   }*/
	   
	   if($whereStr)
	   {
		$select->where($whereStr);   
	   }
	   else
	   {
		$select->limit(500);  
	   }
	   
	    
	   //echo $select;
            $temps = $this->db->fetchAll($select);
         return $temps;		
}

 
public function FetchAllExportQuery($whereStr="",$myTeamUserData="",$search_keywords="",$tags="", $ifCampgn="")
{

$select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','user_status','update_status','tags'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','location','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array())
	   ->order("assign_date desc")
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'));
	  //->joinLeft(array('tag' => 'tbl_tags'), ' tag.id IN( ass_qr.tags)', array('tag_name'));
	   //->where('ass_qr.converted != ?',2)
	   //->where('ass_qr.user_status = ?',1);
	   if($search_keywords)
	   {
		$select->where(" query.name LIKE '%".$search_keywords."%' or query.email_id LIKE '%".$search_keywords."%' or query.phone LIKE '%".$search_keywords."%'");   
	   }
	   
	   if($myTeamUserData)
	   {
		$select->where('ass_qr.user_id IN(?)', $myTeamUserData); 
	   }
	   
	   
	 if(count($tags) > 0)
	 {
		$i=0;
foreach($tags as $tag)
{
	
$i++;
if($i ==1)
{
$select->where('find_in_set("'.$tag.'", tags) <> 0');
}
else
{
$select->ORwhere('find_in_set("'.$tag.'", tags) <> 0');
}	
 
} 
	 }		 
	   
	   
	    if($ifCampgn==1)
	   {
		$select->where('ass_qr.if_campagn =?', $ifCampgn); 
	   }
	   
	   if($whereStr)
	   {
		$select->where($whereStr);   
	   }
	   else
	   {
		$select->limit(500);  
	   }
	   
	    
	   //echo $select;
            $temps = $this->db->fetchAll($select);
         return $temps;		
	
}

 
    public function updateUserQuery($data, $queryid) {
 
         if (Zend_Validate::is($queryid, 'Int')) {
 
              $this->userquery->update($data, 'id = ' . $queryid);
 
         }
 
     }

 

	  public function updateUserStatusQuery($data, $queryid) {
 
         if (Zend_Validate::is($queryid, 'Int')) {
 
              $this->userquery->update($data, 'query_id = ' . $queryid);
 
         }
 
     }
 public function updateExistUserStatusQuery($data, $assign_id) {
         if (Zend_Validate::is($assign_id, 'Int')) {
              $this->userquery->update($data, 'id = ' . $assign_id);
         }
     }
 
	 public function updateQueryUserStatus($data, $queryid, $userid)
    {
	   if (Zend_Validate::is($queryid, 'Int'))
		   {
		  $this->userquery->update($data, 'query_id = ' . $queryid.' and user_id='.$userid);
		  }	 
   }

 
	 public function getViewQueryId($queryid)
	 {
	 $select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','comments','comment_by','admin_comments','update_status','update_status_date','remainder_date','transfer_type','trans_repli_user','assign_follow_up_date','assign_priority','assign_contact_by','profile_id','tags','website_id','other_website','open_status','open_date','escalation_mark','id as assign_id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id')
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->joinLeft(array('p' => 'tbl_user_profile'), 'ass_qr.profile_id = p.id', array('profile_name','website_email','signature'))
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name','sender_id'))
	   ->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))
	   ->where("ass_qr.id = ?", $queryid); 
         $temps = $this->db->fetchRow($select);
         return $temps;		 
	 }

 
	 /////////////////////////////////////////////////////////////////////////////////////////
 

	 public function FetchQueryUser($queryid)
	 {
	   $select = $this->db->select();
       $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->where("ass_qr.query_id = ?", $queryid); 
       $temps = $this->db->fetchAll($select);
       return $temps;	 
	 }



	 



	 public function FetchQueryAllocatedUser($queryid)
 {
  $select = $this->db->select();
  $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id'))
  ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id'))
  ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 ->where("ass_qr.query_id = ?", $queryid)
 ->where("ass_qr.user_status = ?", 1); 
  $temps = $this->db->fetchAll($select);
  return $temps;	 
  }


 public function FetchQueryActiveAllocatedUser($queryid)
 {
  $select = $this->db->select();
  $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','id as assign_id'))
  ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id'))
  ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 ->where("ass_qr.id = ?", $queryid)
 ->where("ass_qr.user_status = ?", 1)
 ->order('id desc'); 
  $temps = $this->db->fetchRow($select);
  return $temps;	 
  }
	 



		



	public function getAllCheckedExportQuery($queryid)
   {   $id=implode(',',$queryid); 
 $select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','website_id','tags','other_website','id as assign_id'))
  ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id')
->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website'))
->joinLeft(array('serv' => 'tbl_requirement'), 'query.requirement = serv.id', array('name as service_name'))
->where('ass_qr.id in('.$id.')'); 
$temps = $this->db->fetchAll($select);
return $temps;



		



		 



	}


 


	public function getAllCheckedExportMainQuery($checkid)
	{
     $id=implode(',',$checkid); 
	 $select = $this->query->select()->where('id IN (?)',$checkid)->order('created_on DESC');
	 $temps = $this->query->fetchAll($select);
 
	 return $temps;
  /*
		$select = $this->db->select();
$select->from(array('query' => 'tbl_query'), array('id','query_code','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','deadline','area_of_study','created_on'))
 	   ->joinLeft(array('ass_qr'=>'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id','assign_date','action_taken','converted','update_status','user_status','id as assign_id','query_id'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->where('query.id IN (?)',$checkid)->group ('ass_qr.query_id');
echo $select;
          $temps = $this->db->fetchAll($select);
		*/
          	
	}
	////////////////////////////////////////////////////////////////////////////////////

	 public function fetchTotalQuery()

	 {

		/*$select = $this->query->select();

         $temps = $this->query->fetchAll($select);

         return $temps;*/ 

		 $select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'));

        $temps = $this->db->fetchAll($select);

       return $temps;

	 }



	  public function fetchSubadminTotalQuery($allocated_to)

	 {

 		$id=explode(",",$allocated_to); 

		$select = $this->db->select();

	    $select->from(array('query'=>'tbl_query'), array('id'))

	    ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

 	    ->where('ass_qr.user_id IN(?)', $id);

        $temps = $this->db->fetchAll($select);

         return $temps; 

	 }



	 public function fetchTotalTakenQuery()

 	 {

 		 $select = $this->db->select();

 	     $select->from(array('query'=>'tbl_query'), array('id'))

 	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

 	   ->where("ass_qr.action_taken = ?", 1); 

             $temps = $this->db->fetchAll($select);

           return $temps;

 	 }

	public function fetchTotalTakenNotDeadQuery($whereStr="")

 	 {

 		 $select = $this->db->select();

 	     $select->from(array('query'=>'tbl_query'), array('id'))

 	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

 	   ->where("ass_qr.action_taken = ?", 1)

	    ->where("ass_qr.converted != ?", 2); 

		if($whereStr)

		  {  

		 $select->where($whereStr);

		 }

             $temps = $this->db->fetchAll($select);

           return $temps;

 	 } 

	 



	 

  public function fetchTotalDeadQuery($whereStr="")

 	 {

 	   $select = $this->db->select();

 	   $select->from(array('query'=>'tbl_query'), array('id'))

 	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

 	   ->where("ass_qr.converted = ?", 2); 

        $temps = $this->db->fetchAll($select);

		if($whereStr)

		  {  

		 $select->where($whereStr);

		 }

		

		

        return $temps;

 	 }

function fetchTotalDeadSearchQuery($whereStr)

{

$select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.converted = ?", 2)

	   ->where($whereStr);

       $temps = $this->db->fetchAll($select);

       return $temps;	

}	 

	 

 

	 function fetchSubadminTotalTakenQuery($allocated_to)

 	 {

 	   $id=explode(",",$allocated_to); 

 	   $select = $this->db->select();

 	   $select->from(array('query'=>'tbl_query'), array('id'))

 	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

 	   ->where("ass_qr.action_taken = ?", 1)->where('ass_qr.user_id IN(?)', $id); 

        $temps = $this->db->fetchAll($select);

           return $temps;	 

 	 }

 function fetchSubadminTotalTakenNotDeadQuery($allocated_to)

 	 {

 	   $id=explode(",",$allocated_to); 

 	   $select = $this->db->select();

 	   $select->from(array('query'=>'tbl_query'), array('id'))

 	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

 	   ->where("ass_qr.action_taken = ?", 1)

	   ->where("ass_qr.converted != ?", 2)

	   ->where('ass_qr.user_id IN(?)', $id); 

        $temps = $this->db->fetchAll($select);

           return $temps;	 

 	 }

	 

	 public function fetchSubadminTotalDeadQuery($allocated_to)

	 {

	   $id=explode(",",$allocated_to);

	   $select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.converted = ?", 2)->where('ass_qr.user_id IN(?)', $id); 

       $temps = $this->db->fetchAll($select);

       return $temps;	 

	 }

	 



	  public function fetchTotalTakenConvertedQuery()

 	 {

 	   $select = $this->db->select();

 	   $select->from(array('query'=>'tbl_query'), array('id'))

 	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

 	   ->where("ass_qr.converted = ?", 1); 

        $temps = $this->db->fetchAll($select);

        return $temps;

 	 }



	 



	 public function fetchSubadminTotalTakenConvertedQuery($allocated_to)

	 {

	   $id=explode(",",$allocated_to);

	   $select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.converted = ?", 1)->where('ass_qr.user_id IN(?)', $id); 

       $temps = $this->db->fetchAll($select);

       return $temps;	 

	 }



	public function fetchTotalSearchQuery($whereStr)

	{ 

	   $select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

 	   ->where($whereStr);

       $temps = $this->db->fetchAll($select);

       return $temps;	

 	}



	public function fetchSubadminTotalSearchQuery($whereStr,$allocated_to)

	{

		$id=explode(",",$allocated_to);

	   $select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

 	   ->where($whereStr)->where('ass_qr.user_id IN(?)', $id);

       $temps = $this->db->fetchAll($select);

       return $temps;	

	}

 function FetchAllQueryNotConvertedQuery()

 {

	   $select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id','name','email_id','phone'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.converted = ?", 0)->group('ass_qr.query_id'); 

       $temps = $this->db->fetchAll($select);

       return $temps;	 	

 }

 

  function FetchSubAdminNotConvertedQuery($allocateduser)

 {

	 $id=explode(',',$allocateduser);

	   $select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id','name','email_id','phone'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.converted = ?", 0)->where("ass_qr.user_id IN(?)", $id)->group('ass_qr.query_id'); 

       $temps = $this->db->fetchAll($select);

       return $temps;	 	

 }





public function fetchTotalTakenSearchQuery($whereStr)

	{

	   $select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.action_taken = ?", 1)

	   ->where($whereStr);

       $temps = $this->db->fetchAll($select);

       return $temps;	

	}

	public function fetchSubadminTotalTakenSearchQuery($whereStr,$allocated_to)

	{

		$id=explode(",",$allocated_to);

		$select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.action_taken = ?", 1)

	   ->where($whereStr)->where('ass_qr.user_id IN(?)', $id);

       $temps = $this->db->fetchAll($select);

       return $temps;

	}

	public function fetchSubadminTotalTakenSearchNotDeadQuery($whereStr,$allocated_to)

	{

		$id=explode(",",$allocated_to);

		$select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.action_taken = ?", 1)

	   ->where("ass_qr.converted != ?", 2)

	   ->where($whereStr)->where('ass_qr.user_id IN(?)', $id);

       $temps = $this->db->fetchAll($select);

       return $temps;

	}

	

	function fetchSubadminTotalSearchDeadQuery($whereStr,$allocated_to)

	{

	$id=explode(",",$allocated_to);

		$select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.converted = ?", 2)

	   ->where($whereStr)->where('ass_qr.user_id IN(?)', $id);

       $temps = $this->db->fetchAll($select);

       return $temps;	

	}

	

	function fetchTotalTakenConvertedSearchQuery($whereStr)

	{

	   $select = $this->db->select();

	   $select->from(array('query'=>'tbl_query'), array('id'))

	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))

	   ->where("ass_qr.converted = ?", 1)

	   ->where($whereStr);

       $temps = $this->db->fetchAll($select);

       return $temps;	

	}



	function fetchSubadminTotalTakenConvertedSearchQuery($whereStr,$allocated_to)
	{
 
		$id=explode(",",$allocated_to);



	   $select = $this->db->select();



	   $select->from(array('query'=>'tbl_query'), array('id'))



	   ->join(array('ass_qr' => 'tbl_assign_query'), 'query.id = ass_qr.query_id', array('user_id'))



	   ->where("ass_qr.converted = ?", 1)



	   ->where($whereStr)->where('ass_qr.user_id IN(?)', $id);



       $temps = $this->db->fetchAll($select);



       return $temps;	



	}



  public function fetchTodayUnFollowupLayout($currentDate,$id)
  {

         $select = $this->db->select();

         $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id','update_status','website_id','other_website',))

 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))

	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))

	   ->where("ass_qr.user_id = ?", $id)

	   ->where("query.follow_up_date <= ?", $currentDate)

	   ->where("ass_qr.action_taken != ?", 1)
	   
	   ->where("ass_qr.update_status != ?", 6)

	   ->where("ass_qr.converted = ?", 0)

	   ->order("query.follow_up_date desc");

       $temps = $this->db->fetchAll($select);

       return $temps;

  }	



  public function fetchTodayUnFollowup($currentDate,$id)

  {

         $select = $this->db->select();

         $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','id as assign_id','query_id','website_id','other_website',))

 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))

	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))

	   ->where("ass_qr.user_id = ?", $id)

	   ->where("query.follow_up_date <= ?", $currentDate)

	   ->where("ass_qr.action_taken != ?", 1)

	   ->where("ass_qr.converted = ?", 0)

	   ->order("query.follow_up_date desc")

	   ->limit(50);

       $temps = $this->db->fetchAll($select);

       return $temps;

  }



  public function fetchSearchTodayUnFollowup($currentDate,$id,$whereStr)

  {

 	    $select = $this->db->select();

        $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id'))



 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))

	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))

	   ->where("ass_qr.user_id = ?", $id)

	   ->where("query.follow_up_date <= ?", $currentDate)

	   ->where($whereStr)

	   ->where("ass_qr.action_taken != ?", 1)

	   ->where("ass_qr.converted = ?", 0)

	   ->order("query.follow_up_date desc");

       $temps = $this->db->fetchAll($select);

       return $temps;	  

  }

   public function fetchAllTodayFollowupLayout($currentDate)
  {

 		$select = $this->db->select();

        $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id'))

 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))

	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))

	   ->where("query.follow_up_date <= ?", $currentDate)

	   ->where("ass_qr.action_taken != ?", 1)

 	   ->where("ass_qr.converted = ?", 0)

	   ->order("query.follow_up_date desc");

	    

	   $temps = $this->db->fetchAll($select);

         return $temps;	  

  }



    public function fetchAllTodayFollowup($currentDate)

  {

 		$select = $this->db->select();

        $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id'))

 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))

	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))

	   ->where("query.follow_up_date <= ?", $currentDate)

	   ->where("ass_qr.action_taken != ?", 1)

 	   ->where("ass_qr.converted = ?", 0)

	   ->order("query.follow_up_date desc")

	   ->limit(50);

         $temps = $this->db->fetchAll($select);

         return $temps;	  

  }

  function fetchSearchTodayFollowup($currentDate,$whereStr)

  {

   $select = $this->db->select();

        $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id'))



 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))



	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))



	   ->where("query.follow_up_date <= ?", $currentDate)



	   ->where($whereStr)



	   ->where("ass_qr.action_taken != ?", 1)



 	   ->where("ass_qr.converted = ?", 0)



	   ->order("query.follow_up_date desc");



       $temps = $this->db->fetchAll($select);



       return $temps;	  



  }



  function fetchAllSubadminTodayFollowupLayout($currentDate,$allocated_to) 

  {

	$id=explode(",",$allocated_to);

	$select = $this->db->select();

$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id'))

 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))

	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))

	   ->where("query.follow_up_date <= ?", $currentDate)

	   ->where("ass_qr.user_id IN(?)", $id)

	   ->where("ass_qr.action_taken != ?", 1)

	   ->where("ass_qr.converted = ?", 0)

	   ->order("query.follow_up_date desc");

         $temps = $this->db->fetchAll($select);

          return $temps;  



  }

  



  function fetchAllSubadminTodayFollowup($currentDate,$allocated_to) 

  {

	$id=explode(",",$allocated_to);

	$select = $this->db->select();

$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','id as assign_id','query_id'))

 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))

	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))

	   ->where("query.follow_up_date <= ?", $currentDate)

	   ->where("ass_qr.user_id IN(?)", $id)

	   ->where("ass_qr.action_taken != ?", 1)

	   ->where("ass_qr.converted = ?", 0)

	   ->order("query.follow_up_date desc")

	   ->limit(50);

         $temps = $this->db->fetchAll($select);

          return $temps;  



  }



  



  function fetchSearchSubadminTodayFollowup($currentDate,$allocated_to,$whereStr) 



  {



	  $id=explode(",",$allocated_to);



	$select = $this->db->select();



$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id'))



 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))



	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))



	   ->where("query.follow_up_date <= ?", $currentDate)



	   ->where("ass_qr.user_id IN(?)", $id)



	   ->where($whereStr)



	   ->where("ass_qr.action_taken != ?", 1)



	   ->where("ass_qr.converted = ?", 0)



	   ->order("query.follow_up_date desc");



       $temps = $this->db->fetchAll($select);



       return $temps;  



  }



 



public function fetchAllCountry()



{



 $select = $this->db->select();



 $select->from('tbl_country');



 $temps = $this->db->fetchAll($select);



 return $temps;



}



public function fetchAllState()



{



 $select = $this->db->select();



 $select->from('tbl_state');



 $temps = $this->db->fetchAll($select);



 return $temps;



}






public function fetchAllWebsite()
{
 $select = $this->db->select();
 $select->from('tbl_website')->where("status=?",1);
 $temps = $this->db->fetchAll($select);
 return $temps;	
}
 
 public function fetchAllCategory($defaultcategory)
{
 $select = $this->db->select();
 $select->from('tbl_requirement')->where("category=?",$defaultcategory);
 $temps = $this->db->fetchAll($select);
 //echo $select;
 return $temps;	
}
public function fetchAllCategoryWebsite($category)
{
 $select = $this->db->select();
 $select->from('tbl_website')->where("website_type LIKE '%".$category."%'")->where("status=?",1);
 $temps = $this->db->fetchAll($select);
 return $temps;	
}

public function SendMail($username,$useremail,$client_name,$email_id,$client_phone,$subject)
{
 //////////////////////////Backup User Mail///////////////////////
 $body='Dear <b> '.ucfirst($username).',</b> <br/><br/>One query is added into your panel.Client details are as follow <strong>'.$client_name.' , '.$client_phone.' , '.$email_id.'</strong> Please check asap. <br><br>
<a href="http://querymanagement.rapidcollaborate.com"> http://querymanagement.rapidcollaborate.com</a> <br><br>
 <p>Thanks & Regards,<br>
Query Panel</p>';

  $message = array(
'html' => $body,
'subject' => $subject,
'from_email' => 'info@rapidcollaborate.com',
'from_name' => 'Query Panel',
'to' => array(
array(
	'email'=> $useremail,
	'name' => $username, 
	'type' => 'to'
)
)
);
         SendMandrilMail($message); 
  
////////////////////////////////////////////////////////////////	
}




public function SendSMS($username,$mobile,$client_name,$email_id,$client_phone)
 {

	 $template="Dear ".$username.",one query is added into your panel.Client details are as follow ".$client_name." ".$client_phone." ".$email_id." Please check asap.";

 //$template="Dear ".$username.", I have added one query into your panel. Please check and do the needful asap.";

 $data = array(

 'user' => "stationeryhut",

 'password'=> "UzRLNC9U",

 'msisdn' => $mobile,

 'sid' => "STAHUT",

 'msg' => $template,

 'fl' =>"0",

 'gwid'=> 2,

 );

 list($header, $content) = $this->PostRequest("http://cloud.smsindiahub.in/vendorsms/pushsms.aspx",

 // the url to post to

 "http://rapidcollaborate.com", // its your url

 $data

 );

 $data = json_decode($content);
 
 }

  
function callAPI($method, $url, $data){
  $curl = curl_init();

  switch ($method){
     case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data)
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
     case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data)
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
     default:
        if ($data)
           $url = sprintf("%s?%s", $url, http_build_query($data));
  }

  // OPTIONS:
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
     'APIKEY: vaBG0xi7VQNM7Xpb',
     'Content-Type: application/json',
  ));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

  // EXECUTE:
  $result = curl_exec($curl);
  if(!$result){die("Connection Failure");}
  curl_close($curl);
  return $result;
}



	private function PostRequest($url, $referer, $_data) {



// convert variables array to string:



$data = array();



while(list($n,$v) = each($_data)){



$data[] = "$n=$v";



}



$data = implode('&', $data);



// parse the given URL



$url = parse_url($url);



if ($url['scheme'] != 'http') {



die('Only HTTP request are supported !');



}



// extract host and path:



$host = $url['host'];



$path = $url['path'];



// open a socket connection on port 80



$fp = fsockopen($host, 80);



// send the request headers:



fputs($fp, "POST $path HTTP/1.1\r\n");



fputs($fp, "Host: $host\r\n");



fputs($fp, "Referer: $referer\r\n");



fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");



fputs($fp, "Content-length: ".strlen($data) ."\r\n");



fputs($fp, "Connection: close\r\n\r\n");



fputs($fp, $data);



$result = '';



while(!feof($fp)) {



// receive the results of the request



$result .= fgets($fp, 128);



}



//HTTP API VER 1.1 



// close the socket connection:



fclose($fp);



// split the result header from the content



$result = explode("\r\n\r\n", $result, 2);



$header = isset($result[0]) ? $result[0] : '';



$content = isset($result[1]) ? $result[1] : '';



// return as array:



return array($header, $content);



}	







function addUserQueryFolloup($data)



{



$id = $this->followDate->insert($data);



return $id;



} 







public function FetchFollowUpType($currentDate,$assign_id)



{



      $select = $this->followDate->select()



	  ->where("query_id = ?", $assign_id)



	  ->where("followup_day = ?", $currentDate);



      $temps = $this->followDate->fetchRow($select);



      return $temps;		 



}	



 



public function AutoLoadCron($currentDate)
{       
        $select = $this->db->select();
        $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','action_taken','converted','user_status','id as assign_id','query_id','assign_follow_up_date'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array())
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array())
	   ->join(array('follow_date' => 'tbl_followup_date'), 'ass_qr.id = follow_date.assign_id', array('id','followup_day','contact_by'))
 	   ->where("follow_date.followup_day = ?", $currentDate)
 	   ->where("follow_date.status = ?", 0)
 	   ->where("ass_qr.assign_follow_up_date != ?", $currentDate)
  	   ->where("ass_qr.converted = ?", 0)
 	   ->group("follow_date.assign_id");
 	   //->order("ass_qr.query_id desc")
        $result = $this->db->fetchAll($select);
   		 foreach($result as $DataVal)
          {
 		 $array1=array("follow_up_date"=>$DataVal['followup_day'],"contact_by"=>$DataVal['contact_by']);
 		 $array2=array("action_taken"=>0,"assign_follow_up_date"=>$DataVal['followup_day'],"assign_contact_by"=>$DataVal['contact_by']);
 		 $array3=array("status"=>1,"updated_at"=>date("Y-m-d"));
         $this->query->update($array1, 'id = ' . $DataVal['query_id']);
 		 $this->userquery->update($array2, 'id = ' . $DataVal['assign_id']);
 		 $this->followDate->update($array3, 'id = ' . $DataVal['id']);  
 		 }
}

function gettodaycroncheck($date)
{
$select = $this->followDate->select()->where("updated_at=?",$date);	
$result = $this->followDate->fetchAll($select);
return $result;
}



function AutoLoadSentmail($currentDate)

{

 $select = $this->db->select();

        $select->from(array('follow_date' => 'tbl_followup_date'),  array('id','followup_day','contact_by'))

 	   ->join(array('ass_qr'=>'tbl_assign_query'), 'follow_date.assign_id = ass_qr.id',array('user_id','converted','query_id'))

	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','alt_email_id','phone','alt_contact_no','date','location','complete_address','designation','company_name','website','other_website','area_of_study','requirement','other_requirement','priority','word_count','deadline','academic_level','approx_value','remarks'))

	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('name as user_name','email_id as user_email',))

  	   ->where("follow_date.followup_day = ?", $currentDate)

 	   ->where("follow_date.sent_mail = ?", 0)

  	   ->where("ass_qr.converted = ?", 0)

 	   ->group("query_id");

 	   //->order("ass_qr.query_id desc")

        $result = $this->db->fetchAll($select);

		/*echo "<pre>";

		print_r($result);

		echo "</pre>";

		die;*/

		if(count($result) > 0)
		{

		$body='';

		 foreach($result as $Info)

		{

		  $body = "Dear <b>".$Info['user_name'].",</b> <br/><br/>".

		"We have following details of Queries for today's follow up:-  <br/><br/>

Name: ".$Info['name']."<br/>

Email Id: ".$Info['email_id']."<br/>

Alternate Email Id: ".$Info['alt_email_id']."<br/>

contact No.: ".$Info['phone']."<br/>

Alternate Contact No.: ".$Info['alt_contact_no']."<br/>

Date: ".$Info['date']."<br/>

Location: ".$Info['location']."<br/>

Complete Address: ".$Info['complete_address']."<br/>

Designation: ".$Info['designation']."<br/>

Company Name: ".$Info['company_name']."<br/>

Website: ".$Info['website']."<br/>

Other Website: ".$Info['other_website']."<br/>

Area of Study: ".$Info['area_of_study']."<br/>

Requirement: ".$Info['requirement']."<br/>

Other Requirement: ".$Info['other_requirement']."<br/>

Priority: ".$Info['priority']."<br/>

Word Count: ".$Info['word_count']."<br/>

Deadline: ".$Info['deadline']."<br/>

Academic Level: ".$Info['academic_level']."<br/>

Approx Value: ".$Info['approx_value']."<br/>

Remarks: ".$Info['remarks']."<br/><br/>".

 		"Thanks & Regards,<br />rapidcollaborate.com";

$mail = new Zend_Mail();

$mail->setBodyHtml($body);

$mail->setFrom('support@rapidcollaborate.com', 'rapidcollaborate.com');

$mail->addTo($Info['user_email'], $Info['user_name']);

$mail->setSubject('Today Followup Query');

$mail->send();

       $array3=array("sent_mail"=>1);

  		 $this->followDate->update($array3, 'id = ' . $Info['id']);

		 $body ="";

		}	

		$body ="";

		}

}



////////////////////////////////////////////////Dead Query//////////////////////////////////////

public function FetchAllUserDeadQuery()

{

$select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','website_id','other_website','id as assign_id'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))->order("id desc")
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'))
	   ->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))
	   ->where('ass_qr.converted = ?',2)->limit(50);
         $temps = $this->db->fetchAll($select);
         return $temps;		



}



public function FetchAllUserSearchDeadQuery($whereStr)
{
$select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','website_id','other_website','id as assign_id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'))
	   ->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))
        ->where('ass_qr.converted = ?',2)
	   ->where($whereStr)->order("id desc");
            $temps = $this->db->fetchAll($select);
         return $temps;	
}



function FetchSubAdminUserDeadQuery($user_id)

{

$id=explode(',',$user_id);

 $select = $this->db->select();

$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id'))

 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))

	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))

	   ->where("ass_qr.user_id IN(?)", $id)->order("id desc")->where('ass_qr.converted = ?',2); 

             $temps = $this->db->fetchAll($select);

         return $temps;		

}



public function FetchSearchSubAdminUserDeadQuery($user_id,$whereStr)

{

 $id=explode(',',$user_id);

  $select = $this->db->select();

 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id'))

 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))



	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))

        ->where('ass_qr.converted = ?',2)

	   ->where("ass_qr.user_id IN(?)", $id)->where($whereStr)->order("id desc"); 

       $temps = $this->db->fetchAll($select);

         return $temps;		

}

////////////////////////////////////////////////Dead Query////////////////////////////////////////////////
function FetchSelfUserDeadQuery($user_id)
{
  $select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->where("ass_qr.user_id =?", $user_id)->order("id desc")->where('ass_qr.converted = ?',2); 
             $temps = $this->db->fetchAll($select);
         return $temps;		
}
public function FetchSearchSelfUserDeadQuery($user_id,$whereStr)
{
 $id=explode(',',$user_id);
  $select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','user_status','website_id','other_website','id as assign_id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
        ->where('ass_qr.converted = ?',2)
	   ->where("ass_qr.user_id =?", $user_id)->where($whereStr)->order("id desc"); 
       $temps = $this->db->fetchAll($select);
         return $temps;		
}
////////////////////////////////////////////////Remainder Query////////////////////////////////////////////////
function FetchAllUserRemainderQuery($whereStr)
{
$select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status_date','update_status','remainder_date','website_id','other_website','id as assign_id'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'))
	   ->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))
	  ->where('ass_qr.user_status = ?',1)
	  ->where('ass_qr.update_status = ?',7)
	  ->order('ass_qr.remainder_date asc');
	     if($whereStr)
		  {  
		  $select->where($whereStr);
		  }
         $temps = $this->db->fetchAll($select);
         return $temps;		
}

function FetchSubadminRemainderQuery($user_id,$whereStr)
{
	$id=explode(',',$user_id);
$select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status_date','update_status','remainder_date','id as assign_id'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'))
	   ->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))
	  ->where('ass_qr.user_status = ?',1)
	  ->where('ass_qr.update_status = ?',7)
	  ->order('ass_qr.remainder_date asc')
	  ->where("ass_qr.user_id IN(?)", $id);
	     if($whereStr)
		  {  
		  $select->where($whereStr);
		  }
         $temps = $this->db->fetchAll($select);
         return $temps;			
}

function FetchUserRemainderQuery($user_id,$whereStr)
{
$select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status_date','update_status','remainder_date','id as assign_id'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'))
	   ->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))
	  ->where('ass_qr.user_status = ?',1)
	  ->where('ass_qr.update_status = ?',7)
	  ->order('ass_qr.remainder_date asc')
	  ->where("ass_qr.user_id =?", $user_id);
	     if($whereStr)
		  {  
		  $select->where($whereStr);
		  }
         $temps = $this->db->fetchAll($select);
         return $temps;			
}
////////////////////////////////////////////////Remainder Query Notification////////////////////////////////////////////////
public function FetchAllUserRemainderNotification($currentDate)
{
$select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status_date','update_status','remainder_date','id as assign_id'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	  ->where('ass_qr.user_status = ?',1)
	  ->where('ass_qr.update_status = ?',7)
	  ->where("ass_qr.remainder_date <= ?", $currentDate)
	  //->where("ass_qr.remainder_date !=''")
	  ->order('ass_qr.remainder_date asc');
         $temps = $this->db->fetchAll($select);
         return $temps;			
}
public function FetchSubadminRemainderNotification($user_id,$currentDate)
{
$id=explode(',',$user_id);
$select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status_date','update_status','remainder_date','id as assign_id'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	  ->where('ass_qr.user_status = ?',1)
	  ->where('ass_qr.update_status = ?',7)
	  ->where("ass_qr.user_id IN(?)", $id)
	  ->where("ass_qr.remainder_date <= ?", $currentDate)
	  //->where("ass_qr.remainder_date !=''")
	  ->order('ass_qr.remainder_date asc');
         $temps = $this->db->fetchAll($select);
         return $temps;		
}
public function FetchUserRemainderNotification($user_id,$currentDate)
{
$select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status_date','update_status','remainder_date','id as assign_id'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	  ->where('ass_qr.user_status = ?',1)
	  ->where('ass_qr.update_status = ?',7)
	  ->where("ass_qr.user_id =?", $user_id)
	    ->where("ass_qr.remainder_date <= ?", $currentDate)
	  //->where("ass_qr.remainder_date !=''")
	  ->order('ass_qr.remainder_date asc');
         $temps = $this->db->fetchAll($select);
         return $temps;				
}
///////////////////////////////Comment Table////////////////////////////////////////////////////

public function insertComment($data)

{  

$id = $this->comments->insert($data);

         return $id;	

}

public function GetLastClientEmail($assign_id)
{
$archive_no=0;
$select = $this->db->select();
     $select->from(array('comm'=>'tbl_comments'), array('comments','email_body','message_id','date','from_email'))
	   ->where("comm.ass_query_id = ?", $assign_id)
	   ->where("comm.message_id != ?", "")
	   ->where("comm.archive_no = ?", $archive_no)
	   ->order("comm.id desc");
       $temps = $this->db->fetchRow($select);
       return $temps;	
}

public function GetComents($query_id)
{
 $select = $this->db->select();
$select->from(array('comm'=>'tbl_comments'))
	   ->join(array('user' => 'tbl_users'), 'comm.user_id = user.id', array('username','name'))
	   ->where("comm.query_id = ?", $query_id)->order("comm.date desc"); 
       $temps = $this->db->fetchAll($select);
       return $temps;		
} 
public function GetComentsAssign($assign_id,$archive_no=0)
{
$comments_type="internal";	
 $select = $this->db->select();
$select->from(array('comm'=>'tbl_comments'))
	   ->join(array('user' => 'tbl_users'), 'comm.user_id = user.id', array('username','name'))
	   ->where("comm.ass_query_id = ?", $assign_id)
	   ->order("comm.date desc"); 
	   if($archive_no=='internalComments')
	   {
		$select->where("comm.comments_type =?", $comments_type);   
	   }

	   else
	   {
		   if($archive_no!='all')
		   {
		   $select->where("comm.archive_no = ?", $archive_no);
		   }
		$select->where("comm.comments_type !=?", $comments_type);
	   }
       //echo $select;
       $temps = $this->db->fetchAll($select);

       return $temps;		
}
public function GetSearchComents($query_id,$searchdate)
 {
	 $select = $this->db->select();
     $select->from(array('comm'=>'tbl_comments'), array('user_id','ass_query_id','query_id','comments','date','status'))
	   ->join(array('user' => 'tbl_users'), 'comm.user_id = user.id', array('username','name'))
	   ->where("comm.date = ?", $searchdate) 
	   ->where("comm.ass_query_id = ?", $query_id)->order("comm.id desc");
	   //echo $select; die;
       $temps = $this->db->fetchAll($select);
       return $temps;	 
 }

public function getFirstComments($assign_id)
{
 $select = $this->db->select();
     $select->from(array('comm'=>'tbl_comments'), array('subject'))
	   ->where("comm.ass_query_id = ?", $assign_id)
	   ->order("comm.id asc");
       $temps = $this->db->fetchRow($select);
       return $temps;	
} 

function AdminCommentNotification($user_id)
{
     $select = $this->db->select();
     $select->from(array('comm' => 'tbl_comments'), array('user_id','ass_query_id','comments','date','status'))
	 ->join(array('ass_qr'=>'tbl_assign_query'), 'comm.ass_query_id = ass_qr.id', array('id as assign_id','query_id'))
  	 ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name'))
 	 ->join(array('user' => 'tbl_users'), 'comm.user_id = user.id', array())
    	 ->where("comm.status = ?", 0)
		 ->group('comm.ass_query_id')
	 ->where("comm.user_id != ?", $user_id);
     $result = $this->db->fetchAll($select);
     return $result;
}



function SubAdminCommentNotification($allocated_to,$user_id)
{
	$id=explode(",",$allocated_to);
	 $select = $this->db->select();
     $select->from(array('comm' => 'tbl_comments'), array('user_id','ass_query_id','comments','date','status'))
	 ->join(array('ass_qr'=>'tbl_assign_query'), 'comm.ass_query_id = ass_qr.id', array('id as assign_id','query_id'))
  	 ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name'))
 	 ->join(array('user' => 'tbl_users'), 'comm.user_id = user.id', array())
    	 ->where("comm.status = ?", 0)
		 ->where("comm.user_id != ?", $user_id)
		 ->group('comm.ass_query_id')
	 ->where('ass_qr.user_id IN(?)', $id);
     $result = $this->db->fetchAll($select);
     return $result;
}



function UserCommentNotification($user_id)

{

 	 $select = $this->db->select();

     $select->from(array('comm' => 'tbl_comments'), array('user_id','ass_query_id','comments','date','status'))

	 ->join(array('ass_qr'=>'tbl_assign_query'), 'comm.ass_query_id = ass_qr.id', array('id as assign_id','query_id'))

  	 ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name'))

 	 ->join(array('user' => 'tbl_users'), 'comm.user_id = user.id', array())

    	 ->where("comm.status = ?", 0)

		 ->where("comm.user_id != ?", $user_id)

	 ->group('comm.ass_query_id')

	 ->where('ass_qr.user_id =(?)', $user_id);

     $result = $this->db->fetchAll($select);

     return $result;	

 }

 

 function updateCommentStatus($data,$queryid,$user_id)
 {  
  if (Zend_Validate::is($queryid, 'Int'))
	  {
$this->comments->update($data, 'ass_query_id = ' . $queryid.' and user_id!='.$user_id);
}	 
 } 
 function updateComment($data,$commentid)
 {  
  if (Zend_Validate::is($commentid, 'Int'))
	  {
$this->comments->update($data, 'id = ' . $commentid);
}	 
 }
 

 function selectAllNotificationQuery($whereStr,$status,$limit,$currentDate)
  {
  		$select = $this->db->select();
         $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id','update_status_date'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))
 	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 	   ->where("query.follow_up_date <= ?", $currentDate)
 	   ->where("ass_qr.action_taken != ?", 1)
	   ->where("ass_qr.update_status != ?", 6)
  	   ->where("ass_qr.converted = ?", 0)
 	   ->order("query.follow_up_date desc")
 	   ->limit($limit)
 	   ->where("ass_qr.update_status =?",$status);
 	   if($whereStr)
 		  {  
 		  $select->where($whereStr);
 		  }
            $temps = $this->db->fetchAll($select);
            return $temps;	  
   }
   
 	public function getAssignQueryInfo($assign_id)
 {
        $select = $this->db->select();
        $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id')
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 	   ->where("ass_qr.id = ?", $assign_id);
       $temps = $this->db->fetchRow($select);
       return $temps;	  	 
 	
 }
 
 public function insertQueryFiles($data)
 {
$id = $this->query_file->insert($data);
         return $id;	 
 }
 
 public function fetchQueryFiles($query_id)
 {
$select = $this->query_file->select()->where('query_id = ?',$query_id);
$temps = $this->query_file->fetchAll($select);
return $temps;	 
 }
 
 public function GetActionHistory($qid)
 {
$select = $this->action_history->select()->where('query_assign_id = ?',$qid);
$temps = $this->action_history->fetchAll($select);
return $temps;	 
 }
 
 public function GetFollowupData($ass_id)
 {
$select = $this->followDate->select()->where('assign_id = ?',$ass_id)->order('id asc');
$temps = $this->followDate->fetchAll($select);
return $temps;	 
 }
 
 public function getNextFollowupDate($ass_id)
 {
	$date=strtotime(date("m/d/Y"));
	
$select = $this->db->select();
        $select->from(array('fl'=>'tbl_followup_date'))
  	   ->join(array('ass_qr' => 'tbl_assign_query'), 'fl.assign_id = ass_qr.id', array('user_status'))
 	   ->where('fl.assign_id = ?',$ass_id)
	   //->where('fl.status != ?',1)
	   ->where('fl.followup_day >?',$date)
	   ->where('ass_qr.update_status !=5 and ass_qr.update_status !=6 and ass_qr.update_status !=8 ')
	   ->order('fl.id asc');
       $temps = $this->db->fetchRow($select);
       return $temps;	
	
 	 
 }
  
  
 public function SendWhatsAppSMS($mobile, $name, $email_id, $phone, $website)
 {
$INSTANCE_ID = '12';  // TODO: Replace it with your gateway instance ID here
$CLIENT_ID = "niranjan.kumar@redmarkediting.com";  // TODO: Replace it with your Forever Green client ID here
$CLIENT_SECRET = "16f9663f3d03426291a309bf1add6ae5";   // TODO: Replace it with your Forever Green client secret here
$mess='Hi, A new query has been added in your panel.Query Details :  Name : '.$name.', Email Id : '.$email_id.', Phone No. : '.$phone.', Website Name : '.$website;
 
$postData = array(
  'number' => '+91'.$mobile,  // TODO: Specify the recipient's number here. NOT the gateway number
  'message' => $mess);
$headers = array(
  'Content-Type: application/json',
  'X-WM-CLIENT-ID: '.$CLIENT_ID,
  'X-WM-CLIENT-SECRET: '.$CLIENT_SECRET
);
//$url = 'http://api.whatsmate.net/v1/whatsapp/queue/message';
$url = 'http://api.whatsmate.net/v3/whatsapp/single/text/message/' . $INSTANCE_ID;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
$response = curl_exec($ch);
//echo "Response: ".$response;
curl_close($ch);	 
//die;
 }
 
 
public function checkAnotherUserAssign($mail_to,$assign_id)
{
$select = $this->query->select()->where('email_id = ?',$mail_to);
$mainQ = $this->query->fetchAll($select);

$queries=array();
foreach($mainQ as $qvalue)
{
$queries[]=	$qvalue['id'];
}


$select = $this->userquery->select()
->where('query_id IN(?)', $queries)
->where("id != ?",$assign_id)
->where("update_status !=?",5);
$assignQ = $this->userquery->fetchAll($select);

foreach($assignQ as $assVal)
{
$this->updateUserQuery(array("update_status"=>8),$assVal['id']);	
}


} 


function FetchAllUnreadClientMailExternal($webEmails="",$user_type)
{
$select = $this->client_external_mail->select();

if($user_type!='admin')
{
//$select->where("ToEmail IN(?)", $webEmails);
//$select->ORwhere("cc_email IN(?)", $webEmails);

$i=0;
foreach($webEmails as $email)
{
$i++;

if($i ==1) 
{
$select->where('find_in_set("'.trim($email).'", ToEmail) <> 0 or find_in_set("'.trim($email).'", cc_email) <> 0');
//$select->where("ToEmail LIKE '%".trim($email)."%' or  cc_email LIKE '%".trim($email)."%'");
}
else
{
$select->ORwhere('find_in_set("'.trim($email).'", ToEmail) <> 0 or find_in_set("'.trim($email).'", cc_email) <> 0');
//$select->ORwhere("ToEmail LIKE '%".trim($email)."%' or  cc_email LIKE '%".trim($email)."%'");
}
	
}
}
$select->where('status = ?', 0);
//echo $select;die;
$Result =  $this->client_external_mail->fetchAll($select);
 $arrayNoti=array();
foreach($Result as $noti)
{
	if($noti['status']==0)
	{
$arrayNoti[]=array(
	"id"=>$noti['id']
	);
	}
}
return $arrayNoti;   
}

function FetchAllClientMailExternal($webEmails="",$user_type,$whereStr="", $whereStrDateFilter="")
{
	
	
$select = $this->client_external_mail->select();
if($user_type!='admin' and $whereStr=="")
{
//$select->where("ToEmail IN(?)", $webEmails);
//$select->ORwhere("cc_email IN(?)", $webEmails);

$i=0;
foreach($webEmails as $email)
{
$i++;
if($i ==1)
{
$select->where('find_in_set("'.$email.'", ToEmail) <> 0 or find_in_set("'.$email.'", cc_email) <> 0');
}
else
{
$select->ORwhere('find_in_set("'.$email.'", ToEmail) <> 0 or find_in_set("'.$email.'", cc_email) <> 0');
}	
}
}

if($whereStr)
{
$select->where($whereStr);
}
if($whereStrDateFilter)
{
$select->where($whereStrDateFilter);
}
$select->order("created_date desc");
//echo $select;
//echo $select;die;
$Result =  $this->client_external_mail->fetchAll($select);
return $Result; 	
}

function FetchClientMailInfo($mail_id)
{
$select = $this->client_external_mail->select()->where('id = ?', $mail_id);
$Result =  $this->client_external_mail->fetchRow($select);
return $Result;	
}
function UpdateClientMailInfo($data,$id)
{
if (Zend_Validate::is($id, 'Int')) {

$this->client_external_mail->update($data, 'id = ' . $id);

}	
}

function DeleteExternalClientMail($mail_id)
{
$id=implode(',',$mail_id);  
  	  $this->db->delete('tbl_client_external_mail','id in('.$id.')');	
}

function getMainMessageID($message_id)
{
$select = $this->comments->select()->where('message_id = ?', $message_id);
$Result =  $this->comments->fetchRow($select);
return $Result;		
}

function getAllQueryCmmentedFiles($ref_id)
{
 $select = $this->db->select();
$select->from(array('comm'=>'tbl_comments'))
	   ->join(array('user' => 'tbl_users'), 'comm.user_id = user.id', array('username','name'))
	   ->where('comm.comments_file != ""')
	   ->where("comm.ass_query_id = ?", $ref_id)
	   ->order("comm.date desc"); 
	   //echo $select;
       $temps = $this->db->fetchAll($select);
       return $temps;		
}

function getAllQueryUploadedCmmentedFiles($ref_id)
{
 $select = $this->db->select();
$select->from(array('comm'=>'tbl_comments'))
	   ->join(array('user' => 'tbl_users'), 'comm.user_id = user.id', array('username','name'))
	   ->where('comm.comments_upload_file != ""')
	   ->where("comm.ass_query_id = ?", $ref_id)
	   ->order("comm.date desc"); 
	   //echo $select;
       $temps = $this->db->fetchAll($select);
       return $temps;		
}


public function GetQueryArchiveNo($ref_id)
{
$select = $this->comments->select('archive_no')->where('ass_query_id = ?', $ref_id)->where("archive_no != ? ",0)->group("archive_no")->order("archive_no asc");
$Result =  $this->comments->fetchAll($select);
return $Result;	
}
public function GetQueryUnArchive($ref_id)
{
$select = $this->comments->select()->where('ass_query_id = ?', $ref_id)->where("archive_no = ? ",0);
$Result =  $this->comments->fetchAll($select);
return $Result;		
}
public function GetLastArchiveByQuery($ref_id)
{
$select = $this->comments->select('archive_no')->where('ass_query_id = ?', $ref_id)->order("archive_no desc");
$Result =  $this->comments->fetchRow($select);
return $Result;		
}
public function saveCommentsArchive($data,$ref_id)
{
if (Zend_Validate::is($ref_id, 'Int'))
	  {
$archive_no=0;
$this->comments->update($data, 'ass_query_id = ' . $ref_id.' and archive_no = '.$archive_no);
}	
}

public function getBlankTagQuery()
{
$todayDate=strtotime(date("2021-12-01"));
$daysAgo3 = strtotime('-3 days');
$select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','user_status','update_status','id as assign_id','update_status_date','open_status','open_date','assign_follow_up_date','tags'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array())
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))->order("open_date desc")
       //->where("ass_qr.user_status =?",1)
	  ->where("ass_qr.open_status !=1")
	  ->where('ass_qr.assign_date >= "'.$todayDate.'"')
	  ->where('ass_qr.update_status_date <= "'.$daysAgo3.'"')
	  ->where("ass_qr.update_status !=1");
	  //->where("ass_qr.tags ='' or ass_qr.tags='null'");
	  
	  /*if($tags!="" and count($tags) > 0)
	 {
		$i=0;
		foreach($tags as $tag)
		{
		$i++;
		if($i ==1)
		{
		$select->where('find_in_set("'.$tag.'", tags) <> 0');
		}
		else
		{
		$select->ORwhere('find_in_set("'.$tag.'", tags) <> 0');
		}	
		} 
		 }
	  
	   if($whereStr)
		  {  
		 $select->where($whereStr);
		  }
		*/ 
	   $temps = $this->db->fetchAll($select);
	   return $temps;	
}

function queryGetBeforeJan2019()
{
		 $select = $this->comments->select()->where('date < ?',strtotime("2019-01-01"))->LIMIT(100)->order("id desc");
		 $temps = $this->comments->fetchAll($select);
		 return $temps;		
}

function getCampaignComment($ref_id)
{
$select = $this->campaign_comments->select()
->where('ass_query_id = ?', $ref_id)
->order("id desc");
$Result =  $this->campaign_comments->fetchAll($select);
return $Result;		
}

public function getCampaignQueries($queryIds)
{
$qid=explode(',',$queryIds);
$select = $this->db->select();
 $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status_date','update_status','tags','id as assign_id'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','website','other_website','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'))
	  ->where("ass_qr.id IN(?)", $qid)
	  ->order('ass_qr.id asc');
         $temps = $this->db->fetchAll($select);
         return $temps;		
}
public function getClinetAddedAfter2Hours($start_time, $new_time)
{


	$select = $this->db->select();
	$select->from(array('query'=>'tbl_query'))
	->where('query.created_on < ?', strtotime($new_time))
	->where('query.created_on > ?', strtotime($start_time)); 
	$temps = $this->db->fetchAll($select);
	return $temps;
	
}
function getParticularTatScore($ref_id,$currentscore)
   {
	   
	    $select = $this->tat_score->select()->where('ref_id = ?', ($ref_id))->where('score = ?', ($currentscore));
      $Result =  $this->tat_score->fetchAll($select);
 	 return $Result;
	 
	    
   }
   
   function updateTatScore($data,$ref_id,$score)
 {  
  if (Zend_Validate::is($ref_id, 'Int'))
	  {
$this->tat_score->update($data, 'ref_id = ' . $ref_id.' and score='.$score);
}	 
 }
 
  public function getAllQuerys($query_user_id)
	  {
 $select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','addedby_user_id','team_id','profile_id','tags','assign_priority','assign_priority','assign_contact_by','website_id','other_website','id as assign_id','assign_date','update_status'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id')
	   ->where("ass_qr.addedby_user_id =?", $query_user_id);
       $temps = $this->db->fetchAll($select);
         return $temps;	  
	  }
 
 public function fetchWebsiteName($website_id)
{
 $select = $this->db->select();
 $select->from('tbl_website')->where("id=?",$website_id);
 $temps = $this->db->fetchRow($select);
 return $temps;	
}

public function fetchAllCategories()



{



 $select = $this->db->select();



 $select->from('tbl_requirement');



 $temps = $this->db->fetchAll($select);



 return $temps;



}



public function FetchLeadUserQueries($whereStr="",$queryuserid="")
{
	//echo $whereStr;
$select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','addedby_user_id','assign_date','action_taken','converted','user_status','update_status','transfer_type','trans_repli_user','website_id','other_website','tags','state','id as assign_id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   ->order("assign_date desc")
	   ->joinLeft(array('web' => 'tbl_website'), 'ass_qr.website_id = web.id', array('website as website_name'));
	  //->joinLeft(array('tag' => 'tbl_tags'), ' tag.id IN( ass_qr.tags)', array('tag_name'));
	   //->where('ass_qr.converted != ?',2)
	   //->where('ass_qr.user_status = ?',1);
	   
	   if($queryuserid)
	   {
		$select->where('ass_qr.addedby_user_id', $queryuserid); 
	   }
	  
	   
	   if($whereStr)
	   {
		$select->where($whereStr);   
	   }
	   else
	   {
		$select->limit(500);  
	   }
	   
	    
	   //echo $select;
            $temps = $this->db->fetchAll($select);
         return $temps;		
}
 }