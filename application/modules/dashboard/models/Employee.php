<?php 
  class Dashboard_Model_Employee extends Zend_Db_Table_Abstract
   {
 
    function __construct()
 	{
 	 $this->employee = new Dashboard_Model_DbTable_Employee();
	 $this->db = Zend_Registry::get('db');
 	}
	
 
  public function insertEmployee($array)
   {
     $id = $this->employee->insert($array);
     return $id;
  }
   
  public function updateEmployee($array,$user_id)
  {
	 if (Zend_Validate::is($user_id, 'Int')) {
             // print_r($data);die;
             $this->employee->update($array, 'id = ' . $user_id);
         }  
  }
  
 public function ExistUserCheck($email_id)
  {
         
   $select = $this->employee->select()->where('user_email = ?', ($email_id));
    $Result =  $this->employee->fetchRow($select);
     return $Result;
 
  }
  
  
  function getAllEmployee()
  {
	$select = $this->employee->select()
	->where('user_type=?','Lead Validator')
	->order('user_fname asc');
    $temps = $this->employee->fetchAll($select);
    return $temps;  
  }
 
   

}



	



?>