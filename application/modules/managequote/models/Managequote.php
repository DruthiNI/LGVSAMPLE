<?php
 
class Managequote_Model_Managequote {
  function __construct() {
  $this->quote = new Managequote_Model_DbTable_Managequote();
  $this->assign_task = new Managequote_Model_DbTable_Assigntask();
  $this->notification = new Managequote_Model_DbTable_Notification();

		$this->db = Zend_Registry::get('db');
     }
     /*
      * 
      * Date: 12-AUG-2017
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * @param: id: int
      */
public function addRequestQuote($arr)
{	 
	$insert=$this->quote->insert($arr);
	return $insert;
}	  
public function insertNotification($arr)
{	 
	$insert=$this->notification->insert($arr);
	return $insert;
}
function FetchAllUnreadNotification($receiver_id)
   { 
   if($receiver_id!="")
   {
	$select = $this->db->select();
    $select->from(array('not'=>'tbl_quote_notification'))
 	       ->join(array('u' => 'tbl_users'), 'not.sender_id = u.id', array('name'))
		   ->where('not.status = ?', 0)
		   ->where('not.reciever_id = ?', $receiver_id)
		   ->order('id desc');
	$Result =  $this->db->fetchAll($select);
 	 return $Result;
   }
	else
	{
	 $Result=array();
	 return $Result;	
	}	
   }
   
   function updateQuoteNotification($array,$id)
   {  
	//$query=$this->db->update("tbl_quote_notification", $array, 'id ='.$id);
      $this->db->delete('tbl_quote_notification','id in('.$id.')');	
   }
   
public function checkExistQuote($ref_id)
{
$select = $this->quote->select()->where("ref_id =?",$ref_id);
         $temps = $this->quote->fetchRow($select);
         return $temps;	
}

public function FetchAllNewPendingQuote()
{
$select = $this->assign_task->select()->from('tbl_taskassign', array('quote_id'));
$fetch = $this->assign_task->fetchAll($select);
$id_array = array();
foreach($fetch as $value){
    $id_array[] = $value->quote_id;
}

$select2 = $this->quote->select()
              ->where('quote_id NOT in (?)', $id_array)
			  ->order('quote_id desc');;
			  if($limit)
		  {  
		  $select2->limit($limit);
		  }
$result = $this->quote->fetchAll($select2);
return $result;
}	  
	  
 
public function UpdateQuote($arr,$id)
{
$query=$this->db->update("tbl_quote", $arr, 'id ='.$id);	
}	


public function UpdateAssignTask($arr,$id)
{
$query=$this->db->update("tbl_taskassign", $arr, 'id ='.$id);	
}	

public function DeleteQuote($queryid)
  	{  
 	  $id=implode(',',$queryid);  
  	  $this->db->delete('tbl_quote','id in('.$id.')');
 	  $this->db->delete('tbl_taskassign','quote_id in('.$id.')');
	  $this->db->delete('tbl_quote_chat','quote_id in('.$id.')');
   	}

public function insertTaskAssign($arr)
{
$insert=$this->assign_task->insert($arr);
	return $insert;	
}

public function checkQuoteID($quote_id_byuser)
{
$select = $this->quote->select()->where("quote_id_byuser=?",$quote_id_byuser);
         $temps = $this->quote->fetchAll($select);
         return $temps;	 
}  
	  
	public function FetchAllQuote($whereStr="")
	{
	$select = $this->db->select();  
		$select->from(array('qt'=>'tbl_quote')) 	 
		->join(array('ass_qr' => 'tbl_assign_query'), 'qt.ref_id = ass_qr.id', array())	  
		->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name as client_name','email_id as client_email','requirement','other_requirement'))
        ->join(array('ut' => 'tbl_users'), 'qt.user_id = ut.id', array('name','email_id'))		
		->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))
		->order('qt.id DESC');	
		 if($whereStr)
		  {  
		  $select->where($whereStr);
		  }
		  //echo $select;die;
	$temps = $this->db->fetchAll($select);    
	return $temps;
		 	
	}
	public function FetchAllExportQuote($quote_id)
	{
		 $id=implode(',',$quote_id);
	$select = $this->quote->select()->where('quote_id in('.$id.')')->order(array('status asc','quote_id DESC'));
         $temps = $this->quote->fetchAll($select);
         return $temps;		
	}
	
	
	
	public function FetchAllQuoteofSent($whereStr, $user_id)
	{
	 $select = $this->quote->select()->where('status =?',0)->where('user_id =?',$user_id)->order(array('status ASC','quote_id DESC'));
         $temps = $this->quote->fetchAll($select);
         return $temps;	
	}
	public function FetchAllNewQuote()
	{
	$select = $this->quote->select()->where('status =?',0)->order('quote_id DESC');
         $temps = $this->quote->fetchAll($select);
         return $temps;	
	}
	
	
	
	public function FetchAllAssignSubmittedTask($whereStr,$limit)
	{	
	$select = $this->db->select();  
		$select->from(array('as'=>'tbl_taskassign'), array('id','assign_to_userid','quote_id','status','assign_by','assigned_date','admin_comments','user_comments','quote_price','user_submitted_date')) 	 
		->join(array('qt' => 'tbl_quote'), 'as.quote_id = qt.id', array('user_id','ref_id','currency'))	  
		->join(array('ut' => 'tbl_users'), 'qt.user_id = ut.id', array('name','email_id'))
		->join(array('ass_qr' => 'tbl_assign_query'), 'qt.ref_id = ass_qr.id', array('query_id'))	  
		->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name as client_name'))	  
		->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))
		
		->where("as.status > 0")	
		->where("qt.status =?",1)	
		->order('id DESC');	
		 if($whereStr)
		  {  
		  $select->where($whereStr);
		  }
		   if($limit)
		  {  
		  $select->limit($limit);
		  }

	$temps = $this->db->fetchAll($select);    
	return $temps; 	
	}
	
	public function FetchAllAssignPendingTask($limit)
	{
	$select = $this->db->select();
    $select->from(array('as'=>'tbl_taskassign'), array('id','quote_id','assign_to_userid','status','assigned_date'))
 	       ->join(array('qt' => 'tbl_quote'), 'as.quote_id = qt.id', array('ref_id','currency','created_date'))
		   ->join(array('ut' => 'tbl_users'), 'as.assign_to_userid = ut.id', array('name','username'))
		   ->join(array('ass_qr' => 'tbl_assign_query'), 'qt.ref_id = ass_qr.id', array('query_id'))	  
		->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name as client_name'))
		
		  ->where("as.status =?",'0')
		  ->order('id DESC');
		  
		  if($limit)
		  {  
		  $select->limit($limit);
		  }
		  
	   $temps = $this->db->fetchAll($select);
	  return $temps; 	
	 	
	}
	
	function CheckAssignTask($quote_id)
	{
	 $select = $this->db->select();
    $select->from(array('as'=>'tbl_taskassign'), array('id','quote_id','assign_to_userid','assigned_date','admin_comments','user_comments','quote_price','user_submitted_date','status'))
 	->joinLeft(array('user' => 'tbl_users'), 'as.assign_to_userid = user.id', array('name','username','email_id'))
		->where('as.quote_id=?',$quote_id);
     $temps = $this->db->fetchRow($select);	 
	return $temps;	
	}
	
	function FetchViewQuoteInformation($quote_id)
	{
	 $select = $this->db->select();
    $select->from(array('qt'=>'tbl_quote'), array('ref_id','user_id','id','currency','created_date','deadline_date','currency','comments','status','relevant_file'))
		   ->join(array('ut' => 'tbl_users'), 'qt.user_id = ut.id', array('name','username','email_id'))
		   ->join(array('ass_qr' => 'tbl_assign_query'), 'qt.ref_id = ass_qr.id', array('query_id'))	  
		->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name as client_name','email_id as client_email'))
		->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))
		  ->where("qt.id =?",$quote_id);
	   $temps = $this->db->fetchRow($select);
	  return $temps; 			
	}
	
	function FetchAllConsultantPendingQuote($whereStr)
	{
	$select = $this->db->select();  
		$select->from(array('as'=>'tbl_taskassign'), array('id','assign_to_userid','quote_id','status','assign_by','assigned_date','admin_comments','user_comments','quote_price','user_submitted_date')) 	 
		->join(array('qt' => 'tbl_quote'), 'as.quote_id = qt.id', array('ref_id','currency'))	
        ->join(array('ass_qr' => 'tbl_assign_query'), 'qt.ref_id = ass_qr.id', array('query_id'))	  
		->join(array('query' => 'tbl_query'), 'ass_qr.query_id = query.id', array('name as client_name','email_id as client_email'))
        ->joinLeft(array('service' => 'tbl_requirement'), 'query.requirement = service.id', array('name as service_name'))		
		->where("as.status =?",0)
		->order('id DESC');	
		if($whereStr)
		  {  
		  $select->where($whereStr);
		  }
	$temps = $this->db->fetchAll($select);    
	return $temps; 		
	}
	 
   

} ?>