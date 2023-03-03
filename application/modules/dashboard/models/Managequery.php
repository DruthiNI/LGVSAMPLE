<?php
class Dashboard_Model_Managequery {
   function __construct() {
   $this->query = new Managequery_Model_DbTable_Managequery();
   $this->userquery = new Managequery_Model_DbTable_Manageuserquery();
   $this->followDate = new Managequery_Model_DbTable_Followupdate();
   $this->comments = new Managequery_Model_DbTable_Comments();
  		$this->db = Zend_Registry::get('db');
      }

     /*

      * 

      * Date: 10-Nov-2016

      * Developer: Niranjan Singh

      * Modified By: Niranjan Singh

      * @param: id: int

      */
public function selectAllEscalation($whereStr,$tags)
{

$select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','update_status_date','remainder_date','website_id','other_website','open_status','open_date','assign_follow_up_date'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))->order("open_date desc")
       //->where("ass_qr.user_status =?",1)
	  ->where("ass_qr.escalation_mark =?",1)
	  ->where("ass_qr.update_status !=5 and ass_qr.update_status !=6 and ass_qr.update_status !=8");
	  
	  if($tags!="" and count($tags) > 0)
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
	   $temps = $this->db->fetchAll($select);
	   return $temps;	
}	  
	  
	  
	  
	  
	  public function selectAllOpenTask($whereStr,$tags,$curruserid='')
	  {
$todayDate=time();
$select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','addedby_user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','update_status_date','remainder_date','website_id','other_website','open_status','open_date','assign_follow_up_date'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))->order("open_date desc")
       //->where("ass_qr.user_status =?",1)
	  //->where("ass_qr.open_status !=1")
	  ->where('ass_qr.open_status !=1 or (ass_qr.update_status=7 and ass_qr.remainder_date <= "'.$todayDate.'")')
	  ->where("ass_qr.update_status !=1");
	  
	  if($tags!="" and count($tags) > 0)
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
	  
	  if($curruserid!=''){
	   	$select->where("ass_qr.user_id =?",$curruserid);
		}
		if($this->userInfo->user_type=='Lead Generator'){
			$select->where("ass_qr.addedby_user_id =?",$curruserid);
		}
	   if($whereStr)
		  {  
		 $select->where($whereStr);
		  }
		 
	   $temps = $this->db->fetchAll($select);
	   return $temps;	  
	  }

 public function selectAllDashboardQuery($whereStr,$status,$limit="",$tags,$curruserid="")
{
	  
$select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','addedby_user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','update_status_date','remainder_date','website_id','tags','other_website','assign_follow_up_date','open_date','open_status'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('id','name','email_id','phone','date','location','requirement','priority','word_count','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
	   //->joinLeft(array('follow_date' => 'tbl_followup_date'), 'ass_qr.id = follow_date.assign_id', array('id','followup_day','contact_by'))
	  
	  ->limit($limit);
	
	  
	   if($status!=1 and $status!=5 and $status!=6 and $status!=8)
	   {
	   $select->where("ass_qr.open_status =?",1);
	   }
	   
	   if($status!=5 and $status!=6 and $status!=8)
	   {
	   $select->where("ass_qr.escalation_mark !=?",1);
	   }
	    
	   $select->where("ass_qr.update_status =?",$status);
	   
	    $select ->where("ass_qr.team_id !=?",0);
	 
       $select->where("ass_qr.profile_id !=?",0); 
       if($curruserid!=''){
	   	$select->where("ass_qr.user_id =?",$curruserid);
		}
		if($this->userInfo->user_type=='Lead Generator'){
			$select->where("ass_qr.addedby_user_id =?",$curruserid);
		}
	   if($whereStr)
		  {  
		  $select->where($whereStr);
		  }
	 if($tags!="" and count($tags) > 0)
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
	 
	 
	 if($status==7)
	 {
		 $select->order("ass_qr.remainder_date asc"); 
	 }
	 else
	 {
		$select->order("ass_qr.id desc"); 
	 }
	      
		   
           $temps = $this->db->fetchAll($select);
          // echo $select;
           return $temps;		 
}

 function selectAllNotificationQuery($whereStr,$limit,$currentDate,$tags,$curruserid='')
  {
  		$select = $this->db->select();
         $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','addedby_user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id','update_status_date','tags','assign_follow_up_date','open_date','open_status','assign_contact_by'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','priority','word_count','requirement','academic_level','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))
 	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 	   ->where("ass_qr.assign_follow_up_date <= ?", $currentDate)
 	   //->where("ass_qr.action_taken != ?", 1)
	   ->where('ass_qr.update_status !=5 and ass_qr.update_status !=6 and ass_qr.update_status !=7  and ass_qr.update_status !=8 ')
	   //->where("ass_qr.update_status != ?", 6)
  	   ->where("ass_qr.converted = ?", 0)
  	    ->where("ass_qr.profile_id != ?", 0)
  	    ->where("ass_qr.team_id != ?", 0)
 	   ->order("ass_qr.assign_follow_up_date desc")
 	   ->limit($limit);
	   
	    if($tags!="" and count($tags) > 0)
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
	   if($curruserid!=''){
	   	$select->where("ass_qr.user_id =?",$curruserid);
		}
		if($this->userInfo->user_type=='Lead Generator'){
			$select->where("ass_qr.addedby_user_id =?",$curruserid);
		}
 	   if($whereStr)
 		  {  
 		  $select->where($whereStr);
 		  }
            $temps = $this->db->fetchAll($select);
            return $temps;	  
   }
   
   
   function selectAllPendingNotificationQuery($whereStr="",$limit="",$currentDate="",$tags="")
   {
       
    
    
           $select = $this->db->select();
         $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','addedby_user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id','update_status_date','tags','assign_follow_up_date','open_date','open_status','assign_contact_by'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','priority','word_count','academic_level','requirement','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))
 	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 	   //->where("ass_qr.assign_follow_up_date <= ?", $currentDate)
 	   ->where("ass_qr.action_taken != ?", 1)
	   ->where('ass_qr.update_status !=5 and ass_qr.update_status !=6 and ass_qr.update_status !=7  and ass_qr.update_status !=8 ')
	   //->where("ass_qr.update_status != ?", 6)
  	   ->where("ass_qr.converted = ?", 0)
  	    ->where("ass_qr.profile_id = ?", 0)
  	    ->where("ass_qr.team_id = ?", 0)
 	   ->order("ass_qr.assign_follow_up_date desc")
 	   ->limit($limit);
	   
	    if($tags!="" and count($tags) > 0)
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
            $temps = $this->db->fetchAll($select);
            
            //echo $select;
            return $temps;
          		
   }
   function  selectAllPendingNotificationofLeadGeneratorQuery($whereStr="",$limit="",$tags="",$addedby_user_id="")
   {
       
    
    
           $select = $this->db->select();
         $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','addedby_user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id','update_status_date','tags','assign_follow_up_date','open_date','open_status','assign_contact_by'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','priority','word_count','academic_level','requirement','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))
 	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 	    ->where("ass_qr.addedby_user_id = ?", $addedby_user_id)
 	   ->where("ass_qr.user_id = ?", 1)
 	   ->where("ass_qr.action_taken != ?", 1)
	   ->where('ass_qr.update_status !=5 and ass_qr.update_status !=6 and ass_qr.update_status !=7  and ass_qr.update_status !=8 ')
	   //->where("ass_qr.update_status != ?", 6)
  	   ->where("ass_qr.converted = ?", 0)
  	    ->where("ass_qr.profile_id = ?", 0)
  	    ->where("ass_qr.team_id = ?", 0)
 	   ->order("ass_qr.assign_follow_up_date desc")
 	   ->limit($limit);
	   
	    if($tags!="" and count($tags) > 0)
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
            $temps = $this->db->fetchAll($select);
            
            //echo $select;
            return $temps;
          		
   }
 function  selectAllLeadsofLeadGeneratorQuery($whereStr="",$limit="",$tags="",$addedby_user_id="",$status="",$currentDate="")
   {
       
    
    if($status == 1){
    	 $select = $this->db->select();
         $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','addedby_user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id','update_status_date','tags','assign_follow_up_date','open_date','open_status','assign_contact_by'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','priority','word_count','academic_level','requirement','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))
 	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 	    ->where("ass_qr.addedby_user_id = ?", $addedby_user_id)

 	  	->where("ass_qr.action_taken = ?", 1)
	   ->where('ass_qr.update_status =?',$status)
	   //->where("ass_qr.update_status != ?", 6)
  	   //->where("ass_qr.converted = ?", 0)
  	    ->where("ass_qr.profile_id != ?", 0)
  	    ->where("ass_qr.team_id != ?", 0)
 	   ->order("ass_qr.assign_follow_up_date desc")
 	   ->limit($limit);
    }else{
    	 $select = $this->db->select();
         $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','addedby_user_id','assign_date','action_taken','converted','user_status','update_status','id as assign_id','query_id','update_status_date','tags','assign_follow_up_date','open_date','open_status','assign_contact_by'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name','email_id','phone','date','location','priority','word_count','academic_level','requirement','approx_value','follow_up_date','contact_by','deadline','remarks','flag_mark','created_on'))
 	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 	    ->where("ass_qr.addedby_user_id = ?", $addedby_user_id)

 	  // ->where("ass_qr.action_taken = ?", 1)
	   ->where('ass_qr.update_status =?',$status)
	   //->where("ass_qr.update_status != ?", 6)
  	   //->where("ass_qr.converted = ?", 0)
  	    //->where("ass_qr.profile_id != ?", 0)
  	    //->where("ass_qr.team_id != ?", 0)
 	   ->order("ass_qr.assign_follow_up_date desc")
 	   ->limit($limit);
    }
          
	   
	    if($tags!="" and count($tags) > 0)
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
	   
	   if($currentDate!='')
	 {
		 $select->where("ass_qr.assign_follow_up_date <= ?", $currentDate); 
	 }
 	   if($whereStr)
 		  {  
 		  $select->where($whereStr);
 		  }
            $temps = $this->db->fetchAll($select);
            
            //echo $select;
            return $temps;
          		
   }
  
    function UserPendingTodayTask($currentDate,$category)
  {
  		$select = $this->db->select();
         $select->from(array('ass_qr'=>'tbl_assign_query'), array('count(*) as total_pending'))
  	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array())
 	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 	   ->where("ass_qr.assign_follow_up_date <= ?", $currentDate)
 	   //->where("ass_qr.action_taken != ?", 1)
	   //->where('ass_qr.update_status !=5 and ass_qr.update_status !=6 and ass_qr.update_status !=8 ')
	   //->where("ass_qr.update_status != ?", 6)
  	   //->where("ass_qr.converted = ?", 0)
  	   ->where("user.status = ?", 1)
  	   ->where("user.category = ?", $category)
  	   ->group("ass_qr.user_id")
  	   ->order("total_pending desc");
 	    
            $temps = $this->db->fetchAll($select);
            return $temps;	  
   }
 
function getFirstAutoFollowupQuery()
{ 
$select = $this->db->select();
$select->from(array('ass_qr'=>'tbl_assign_query'), array('id'))
 	   ->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array())
	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array())
	  // ->joinRight(array('com' => 'tbl_comments'), 'ass_qr.id = com.ass_query_id', array())
       ->where("ass_qr.user_status =?",1)
	   ->where("ass_qr.update_status =?",1);
	   
       $temps = $this->db->fetchAll($select);
       return $temps;		
}

function getAllUsersQuery()
{ 
$select = $this->userquery->select()->where("website_id =?",0)->limit(10000);
       $temps = $this->userquery->fetchAll($select);
       return $temps;		
}

function getCategoryName($requirement=""){
	if($requirement!=''){
$select = $this->db->select();
 $select->from('tbl_requirement')->where("id=?",$requirement);
 $temps = $this->db->fetchRow($select);
 //echo $select;
 return $temps;	
 }
}

function GetdateTimeFormate( $time )
{
	if($time)
	{
    $time_difference = (time() > $time)? time() - $time : 0;

    if( $time_difference < 1 ) { return 'less than 1 second ago'; }
    $condition = array( 12 * 30 * 24 * 60 * 60 =>  'Year',
                30 * 24 * 60 * 60       =>  'Month',
                24 * 60 * 60            =>  'Day',
                60 * 60                 =>  'Hour',
                60                      =>  'Min',
                1                       =>  'Sec'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $time_difference / $secs;

        if( $d >= 1 )
        {
            $t = round( $d );
            return  $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
  }
  else
  {
	return 'less than 1 second ago';  
  }
}

 
}