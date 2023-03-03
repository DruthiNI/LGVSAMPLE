<?php 

/**

 * To get the User credits.

 * 

 */

class Zend_View_Helper_Getcredit

{ 

  function getcredit($user_id=0)

  {    

   $userInfo = Zend_Auth::getInstance()->getStorage()->read();

   $userObj = new User_Model_User();

   $user_id = ($user_id > 0)?$user_id:$userInfo->id;
   $credits = $userObj->getCredit($user_id);

   return $credits;

  }



}