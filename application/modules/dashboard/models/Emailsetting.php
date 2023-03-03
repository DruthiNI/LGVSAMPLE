<?php 
class Dashboard_Model_Emailsetting extends Zend_Db_Table_Abstract

{



    function __construct()

	{

	 $this->emailsetting = new Dashboard_Model_DbTable_Emailsetting();

	}
	
  public function fetchEmailSetting()
  {
   $res = $this->emailsetting->select()->where('id = ?', 1);
    
  return $this->emailsetting->fetchRow($res);
  }
  
   public function UpdateEmailSetting($arr,$id)
  {
	  $db = Zend_Registry::get('db');
	  $query=$db->update('tbl_email_setting', $arr,'id = '.$id);
  }
  
}
	?>