<?php
 
class Managequote_Model_Chat {
  function __construct() {
  $this->quote = new Managequote_Model_DbTable_Managequote();
  $this->chat = new Managequote_Model_DbTable_Chat();

		$this->db = Zend_Registry::get('db');
     }
     /*
      * 
      * Date: 12-AUG-2017
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * @param: id: int
      */
	  
	  public function InsertQuoteChat($arr)
	  {
		$insert=$this->chat->insert($arr); 
        return $insert;		
	  }
	  public function UpdateChat($arr,$id)
	  {
		$query=$this->db->update("tbl_quote_chat", $arr, 'id ='.$id);  
	  }
 public function FetchQuoteChat($quote_id)
	{
	$select = $this->db->select();
    $select->from(array('chat'=>'tbl_quote_chat'))
 	->joinLeft(array('user' => 'tbl_users'), 'chat.sender_id = user.id', array('name'))
		->where('chat.quote_id=?',$quote_id);
     $temps = $this->db->fetchAll($select);	 
	return $temps;		
	}
	
	public function FetchUnreadChatAdmin($user_id)
	{
	$select = $this->db->select();
    $select->from(array('chat'=>'tbl_quote_chat'))
 	->joinLeft(array('qt' => 'tbl_quote'), 'chat.quote_id = qt.quote_id', array('ref_id','client_name'))
	->joinLeft(array('user' => 'tbl_users'), 'chat.sender_id = user.id', array('name','username'))
		->where('chat.sender_id!=?',$user_id)
		->where('chat.new_message!=?',1)
		->order('id desc');
     $temps = $this->db->fetchAll($select);	 
	return $temps;		
	}
	
	public function FetchUnreadChatCRM($user_id)
	{
	$select = $this->db->select();
    $select->from(array('chat'=>'tbl_quote_chat'))
 	->joinLeft(array('qt' => 'tbl_quote'), 'chat.quote_id = qt.quote_id', array('ref_id','client_name'))
	->joinLeft(array('user' => 'tbl_users'), 'chat.sender_id = user.id', array('name','username'))
		->where('chat.sender_id!=?',$user_id)
		->where('qt.user_id=?',$user_id)
		->where('chat.new_message!=?',1)->order('id desc');
     $temps = $this->db->fetchAll($select);	 
	return $temps;		
	}
	public function FetchUnreadChatConsultant($user_id)
	{
	$select = $this->db->select();
    $select->from(array('chat'=>'tbl_quote_chat'))
 	->joinLeft(array('qt' => 'tbl_quote'), 'chat.quote_id = qt.quote_id', array('ref_id','client_name'))
	->joinLeft(array('task' => 'tbl_taskassign'), 'qt.quote_id = task.quote_id', array('assign_to_userid'))
	->joinLeft(array('user' => 'tbl_users'), 'chat.sender_id = user.id', array('name','username'))
		->where('chat.sender_id!=?',$user_id)
		->where('task.assign_to_userid=?',$user_id)
		->where('chat.new_message!=?',1)->order('id desc');
     $temps = $this->db->fetchAll($select);	 
	return $temps;	
	}
	 
   

}