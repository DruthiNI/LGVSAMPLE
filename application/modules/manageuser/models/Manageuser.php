<?php
 
class Manageuser_Model_Manageuser {
  function __construct() {
  $this->user = new Manageuser_Model_DbTable_Manageuser();
  $this->state = new Manageuser_Model_DbTable_State();
  $this->notes = new Manageuser_Model_DbTable_Notes();
  $this->message = new Manageuser_Model_DbTable_Message();
  $this->userProfile = new Manageuser_Model_DbTable_Profile();
  $this->tat_score= new Managequery_Model_DbTable_Tatscore();
  
  $this->db = Zend_Registry::get('db');
     }
     /*
      * 
      * Date: 14-OCT-2015
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * @param: id: int
      */
 	  public function addUser($data) {
         $id = $this->user->insert($data);
         return $id;
     }
	 public function insertUserProfile($data)
	 {
		$id = $this->userProfile->insert($data);
         return $id; 
	 }
  public function getUserCheck($username) {
         $db = Zend_Registry::get('db');
   $select = $this->user->select()->where('username = ?', ($username));
   $Result =  $this->user->fetchRow($select);
                     return $Result;
     }
	public function CheckUserEmailID($email_id) {
   $db = Zend_Registry::get('db');
   $select = $this->user->select()->where('email_id = ?', ($email_id));
   $Result =  $this->user->fetchRow($select);
   return $Result;
     } 
	 
	  
 	 public function getUserId($userid)
 	 {
  	 $select = $this->user->select()->where('id = ?', ($userid));
      $Result =  $this->user->fetchRow($select);
 	 return $Result;
      }

 
     /*
       /*
      * Date: 14-OCT-2015
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * @param: data: array
      * @param: $template_id: int
      */
 
    public function updateUser($data, $user_id) {
         if (Zend_Validate::is($user_id, 'Int')) {
              $this->user->update($data, 'id = ' . $user_id);
         }
     }
	 
	 public function updateUserProfile($data, $profile_id)
	 {
		if (Zend_Validate::is($profile_id, 'Int')) {
              $this->userProfile->update($data, 'id = ' . $profile_id);
         } 
	 }
	 
	 /*public function updateTatScore($data, $ref_id)
	 {
		if (Zend_Validate::is($ref_id, 'Int')) {
              $this->tat_score->update($data, 'ref_id = ' . $ref_id);
         } 
	 }*/
 
	public function DeleteUser($userid)
 	{
	  $id=implode(',',$userid);
 	  $this->db->delete('tbl_users','id in('.$id.')');
	  $this->db->delete('tbl_notes','user_id in('.$id.')');
	  $this->db->delete('tbl_message','user_id in('.$id.')');

	  $this->db->delete('tbl_assign_query','user_id in('.$id.')');
	  $this->db->delete('tbl_followup_date','user_id in('.$id.')');
	  $this->db->delete('tbl_comments','user_id in('.$id.')');
	  $this->db->delete('tbl_meeting_schedule','user_id in('.$id.')');
	  $this->db->delete('tbl_user_profile','user_id in('.$id.')');
  	}
	public function DeleteUserProfile($id)
	{
	$this->db->delete('tbl_user_profile','id in('.$id.')');	
	}
	public function getAllUsers($whereStr="") {
         $select = $this->user->select()->where('user_type != ?', 'admin')->order('created_on DESC');
		 if($whereStr)
		{
		$select->where($whereStr);	
		}
         $temps = $this->user->fetchAll($select);
         return $temps;
    }
	
	public function getSubadminAllUsers($allocated_to)
	{
		$id=explode(',',$allocated_to);
	    $select = $this->user->select()->where('user_type != ?', 'admin')->where("id IN(?)", $id)->order('created_on DESC');
        $temps = $this->user->fetchAll($select);
        return $temps;	
	}
 	
	  
	public function fetchActiveUsers($user_type="") {
         $select = $this->user->select()->where('status = ?', 1)->order('created_on DESC');
		 if($user_type)
		 {
		$select->where('user_type = ?', $user_type);	 
		 }
		 //echo $select;die;
         $temps = $this->user->fetchAll($select);
         return $temps;
    }
	 
	
	public function getAllCheckedExportUser($userid)
	{
	 $id=implode(',',$userid);
	$select = $this->user->select()->where('id in('.$id.')')->order('id DESC');
         $temps = $this->user->fetchAll($select);
         return $temps;		
	}
	
	public function getSelfUserAddQuery($userid)
	{
 	$select = $this->user->select()->where('id=?',$userid);
         $temps = $this->user->fetchAll($select);
         return $temps;		
	}

  public function getAllNewUsers() {
         $select = $this->user->select()->where('user_type = ?', 'Lead Validator')->where('notification = ?', 0)->order('id DESC');
         $temps = $this->user->fetchAll($select);
         return $temps;

    }
	
  public function AddNotes($data)
  {
	 $id = $this->notes->insert($data);
         return $id;  
  }
  
  public function fetchNotes($id)
  {
	 $select = $this->notes->select()->where('user_id = ?', $id)->order('id asc');
         $temps = $this->notes->fetchAll($select);
         return $temps;  
  }
  
  public function DeleteNotes($id)
  {
  	  $this->db->delete('tbl_notes','id ='.$id);  
  }

public function InsertMessage($data)
{
 $id = $this->message->insert($data);
 return $id;	
}

public function FetchMessage($user_id)
{
$select = $this->message->select()->where('user_id = ?', $user_id)->order('id asc');
$temps = $this->message->fetchAll($select);
return $temps; 	
}

public function FetchAllMessage()
{
$select = $this->message->select()->order('id desc');
$temps = $this->message->fetchAll($select);
return $temps;	
}

public function FetchMyMessageAdminNotyfy($user_id)
{  
$select = $this->message->select()->where('sender_id != ?', $user_id)->where('status = ?', 0);
$temps = $this->message->fetchAll($select);
return $temps;	
}

public function FetchMyMessageNotyfy($user_id)
{  
$select = $this->message->select()->where('sender_id != ?', $user_id)->where('user_id = ?', $user_id)->where('status = ?', 0);
$temps = $this->message->fetchAll($select);
return $temps;	
}

public function FetchMessageUserWiseNotyfy($user_id)
{
$select = $this->db->select();
         $select->from(array('mess'=>'tbl_message'), array('user_id','sender_id','message','date','status'))
 	   ->join(array('user' => 'tbl_users'), 'mess.user_id = user.id', array('name'))
	   ->where('mess.sender_id != ?', $user_id)
	   ->where('mess.status = ?', 0)
	   ->group("mess.user_id");	
 
 $temps = $this->db->fetchAll($select);
 
return $temps;	
}

public function NotifyCountThisUser($admin_id,$user_id)
{
$select = $this->message->select()->where('sender_id != ?', $admin_id)->where('user_id = ?', $user_id)->where('status = ?', 0);
$temps = $this->message->fetchAll($select);
return $temps;	
}

public function UpdateMessage($arr,$sender_id)
{
if (Zend_Validate::is($sender_id, 'Int')) {
    $this->message->update($arr, 'sender_id = ' . $sender_id);
     }	
}

public function UpdateMessageUserNotify($arr,$my_id)
{
	//$where = $this->message->quoteInto('public_id = ?', $PublicId);
//$this->message->update( $arr, $where);
	 
 if (Zend_Validate::is($my_id, 'Int')) {
     $this->message->update($arr, 'sender_id != ' . $my_id. ' and user_id = ' . $my_id );  
     } 		
}

public function getTeamUsers($team_id)
{
$select = $this->user->select()
->where("user_type =?",'Lead Validator')
->where("find_in_set('$team_id',team_id) <> 0 ");
$temps = $this->user->fetchAll($select);
return $temps;	
}
public function getCountryStates($country)
{
$select = $this->state->select()
->where("status =?",'Active')
->where("find_in_set('$country',country_name) <> 0 ");
$temps = $this->state->fetchAll($select);
return $temps;	
}

public function getUserProfile($user_id)
{
$select = $this->userProfile->select()->where('user_id = ?', $user_id);
$temps = $this->userProfile->fetchAll($select);
return $temps;	
}

public function getUserProfiles($user_id)
{
$select = $this->db->select();
         $select->from(array('pr'=>'tbl_user_profile'))
 	   ->join(array('web' => 'tbl_website'), 'pr.website = web.id', array('website as website_name'))
	   ->where('pr.user_id = ?', $user_id) ;
 $temps = $this->db->fetchAll($select);
 
return $temps;	
}

public function getUserProfilesEmails($user_id,$myTeamUserData="")
{
$select = $this->db->select();
         $select->from(array('pr'=>'tbl_user_profile'),array('website_email'))
 	   ->join(array('web' => 'tbl_website'), 'pr.website = web.id', array());
	   
	   if($myTeamUserData)
	   {
		   $select->where("pr.user_id IN(?)", $myTeamUserData);
	   }
	   else
	   {
		 $select->where('pr.user_id = ?', $user_id);  
	   }
 
 $temps = $this->db->fetchAll($select);
 
return $temps;	
}

function getUserProfilesAdminEmails()
{
$select = $this->db->select();
         $select->from(array('pr'=>'tbl_user_profile'),array('website_email'))
 	   ->join(array('web' => 'tbl_website'), 'pr.website = web.id', array())
	   ->group("website_email");
 $temps = $this->db->fetchAll($select);
return $temps;	
}

public function getUserProfileInfo($profile_id)
{
$select = $this->db->select()->from("tbl_user_profile")->where('id = ?', ($profile_id));
      $Result =  $this->db->fetchRow($select);
 	 return $Result;	
}
public function getTeamTeamManagers($usersId)
{
	$ids=explode(",",$usersId);
 
$select = $this->user->select("name")->where('id IN(?)', $ids);
$temps = $this->user->fetchAll($select);
return $temps;	
}

public function getAllUsersMyTeams($team_ids)
{
$ids=explode(",",$team_ids);
$select = $this->user->select('id','name','status');
$select->where("user_type=?",'Lead Validator');
//$select->where('team_id IN(?)', $ids)
$i=0;
foreach($ids as $id)
{
	
$i++;
if($i ==1)
{
$select->where('find_in_set("'.$id.'", team_id) <> 0');
}
else
{
$select->ORwhere('find_in_set("'.$id.'", team_id) <> 0');
}	
 
}

//echo $select;die;
$temps = $this->user->fetchAll($select);
 
$userIds=array();
foreach($temps as $user)
{
	$userIds[]=$user->id;
}
return $userIds;
}


function getAllTATScore($whereStr="",$myTeamUserData="",$usertpe="",$userid="")
{
$select = $this->db->select();

 $select->from(array('score'=>'tbl_user_tat_score'), array("sum(score) as total_score","sum(total_minute) as total_minute","user_id",'count(*) as total_rows','created_date'))
  	   ->join(array('user' => 'tbl_users'), 'score.user_id = user.id', array('name as username'))
	   ->group("user_id");
	   
	  
	   /*if($usertpe == 'Lead Generator' && $userid==''){
	        if($myTeamUserData)
	        {
		        $select->where('score.user_id IN(?)', $myTeamUserData); 
	        }
	   }*/
	   
	   
	   if($usertpe){
	       $select->where('user.user_type=?', $usertpe); 
	   }
	    if($userid){
	       $select->where('user.id=?', $userid); 
	   }
	   if($whereStr)
	   {
		$select->where($whereStr); 
	   }
	   
         $temps = $this->db->fetchAll($select);
        // echo $select;
		 return $temps;		
}
 
 
function getLeadaaprovedscore($userid){
 
 $select = $this->db->select();
 $select->from(array('score'=>'tbl_user_tat_score'), array("",'ref_id as total_ref_rows'))
  	   ->join(array('user' => 'tbl_users'), 'score.user_id = user.id',array())
  	    ->join(array('assq' => 'tbl_assign_query'), 'score.ref_id = assq.id')
	    ->where("assq.update_status =?",5)
	   ->where("score.user_id =?",$userid);
         $temps = $this->db->fetchAll($select);
		 //echo $select;
		 return $temps;	
		 
}		 
/*function getAllTATScore($whereStr="",$myTeamUserData="")
{
$select = $this->db->select();
 $select->from(array('score'=>'tbl_user_tat_score'))
  	   ->join(array('user' => 'tbl_users'), 'score.user_id = user.id', array('name as username'))
	   ->where("score.user_id=?",107);
	   //->group("user_id");
	   
	    if($myTeamUserData)
	   {
		//$select->where('score.user_id IN(?)', $myTeamUserData); 
	   }
	   
	   if($whereStr)
	   {
		$select->where($whereStr); 
	   }
	   
         $temps = $this->db->fetchAll($select);
		 return $temps;		
}
*/
function getTatUserQuery($user_id)
{
$select = $this->tat_score->select()->where('user_id =?', $user_id)->group("ref_id");
$temps = $this->tat_score->fetchAll($select);
//print_r($tems);
return $temps;	
}

function getParticularTatScore($ref_id)
{
$select = $this->db->select();
 $select->from(array('score'=>'tbl_user_tat_score'), array("sum(score) as total_score","sum(total_minute) as total_minute",'count(*) as total_rows'))
  	   ->join(array('user' => 'tbl_users'), 'score.user_id = user.id',array())
	   ->where("score.ref_id =?",$ref_id);
         $temps = $this->db->fetchRow($select);
		 return $temps;	
}


function hoursandmins($time, $format = '%02dh %02dm')
{
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

function getUserProfilesThisWebsite($userId, $websiteId)
{
$select = $this->db->select()->from("tbl_user_profile")->where('user_id = ?', ($userId))->where('website = ?', ($websiteId));
      $Result =  $this->db->fetchRow($select);
 	 return $Result;	
}

function getUsersforThiswbsiteUsers($websiteId)
{
$select = $this->db->select();
 $select->from(array('pr'=>'tbl_user_profile'), array("website","user_id"))
  	   ->join(array('user' => 'tbl_users'), 'pr.user_id = user.id',array('name as username', 'email_id'))
	   ->where("pr.website =?",$websiteId)
	   ->where("user.status =?",1)
	   ->group("pr.user_id");
         $temps = $this->db->fetchAll($select);
		 return $temps;	
}

function getUsersforThiswbsiteUsersCron($websiteId)
{
$select = $this->db->select();
 $select->from(array('pr'=>'tbl_user_profile'), array("website","user_id"))
  	   ->join(array('user' => 'tbl_users'), 'pr.user_id = user.id',array('name as username', 'email_id as user_email_id'))
	   ->where("pr.website =?",$websiteId)
	   ->where("user.status =?",1)
	   ->order("user.id asc")
	   ->group("pr.user_id");
	   
         $temps = $this->db->fetchAll($select);
		 return $temps;	
}

}