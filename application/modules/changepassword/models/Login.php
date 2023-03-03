<?php 
class Login_Model_Login extends Zend_Db_Table_Abstract
{
    function __construct()
	{
	 	$this->user = new Login_Model_DbTable_Login();

	}
	
  public function fetchUser()
  {
   $res = $this->user->select();
    
  return $this->user->fetchAll($res);
  }
  
  public function fetchUser1()
  {
     $db = Zend_Registry::get('db');  
     $res = $db->select()->from('tbl_user');
     return $db->fetchAll($res);
  }
	
}
	
?>