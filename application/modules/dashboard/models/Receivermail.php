<?php 
class Dashboard_Model_Receivermail extends Zend_Db_Table_Abstract
{
    function __construct()

	{

	 $this->recmail = new Dashboard_Model_DbTable_Receivermail();

	}
	
  public function fetchSentmail()
  {
   $res = $this->recmail->select();
  return $this->recmail->fetchAll($res);
  }
  
   public function getScheduleReceiverMail($mailid)
  {
   $res = $this->recmail->select()->where('mail_id=?',$mailid);
  return $this->recmail->fetchAll($res);
  }
  
  public function insertReceivermail($array)
  {
    $id = $this->recmail->insert($array);
        return $id;
  }
	
}
	
	?>