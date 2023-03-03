<?php 
/**
 * To get the User credits.
 * 
 */
class Zend_View_Helper_Getreceiver
{ 
  function getreceiver($mailId=0)
  {    
   $userInfo = Zend_Auth::getInstance()->getStorage()->read();
   $schedule = new Schedule_Model_Schedule();
   $receivers = $schedule->getReceivers($mailId);
   return $receivers[0]['receiver_id'];
  }

}