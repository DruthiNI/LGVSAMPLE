<?php
$route = new Zend_Controller_Router_Route('/managequery/client-mail',array('module' => 'managequery','controller' => 'index','action'=> 'client-mail'));
$router->addRoute('client-mail', $route); 


$route = new Zend_Controller_Router_Route('/manageuser/adduser',array('module' => 'manageuser','controller' => 'index','action'=> 'adduser'));
$router->addRoute('adduser', $route); 

$route = new Zend_Controller_Router_Route('/managequery/addquery',array('module' => 'managequery','controller' => 'index','action'=> 'addquery'));
$router->addRoute('addquery', $route);

$route = new Zend_Controller_Router_Route('/managequery/summary',array('module' => 'managequery','controller' => 'index','action'=> 'summary'));
$router->addRoute('summary', $route);

$route = new Zend_Controller_Router_Route('/managequery/addboxquery',array('module' => 'managequery','controller' => 'index','action'=> 'addboxquery'));
$router->addRoute('addboxquery', $route);

$route = new Zend_Controller_Router_Route('/managequery/boxquery',array('module' => 'managequery','controller' => 'index','action'=> 'boxquery'));
$router->addRoute('boxquery', $route);

$route = new Zend_Controller_Router_Route('/managequery/import-query',array('module' => 'managequery','controller' => 'index','action'=> 'import-query'));
$router->addRoute('import-query', $route);

$route = new Zend_Controller_Router_Route('/managequery/useraddquery',array('module' => 'managequery','controller' => 'index','action'=> 'useraddquery'));
$router->addRoute('useraddquery', $route);


$route = new Zend_Controller_Router_Route('/managequery/userquery',array('module' => 'managequery','controller' => 'index','action'=> 'userquery'));
$router->addRoute('userquery', $route); 

$route = new Zend_Controller_Router_Route('/managequery/camp-history',array('module' => 'managequery','controller' => 'index','action'=> 'camp-history'));
$router->addRoute('camp-history', $route); 

$route = new Zend_Controller_Router_Route('/managequery/camp-query/:campid/',array('module' => 'managequery','controller' => 'index','action'=> 'camp-query', 'campid'=>'\w+'));
$router->addRoute('managequery/camp-query', $route);

$route = new Zend_Controller_Router_Route('/managequery/allnotification',array('module' => 'managequery','controller' => 'index','action'=> 'allnotification'));
$router->addRoute('allnotification', $route); 

$route = new Zend_Controller_Router_Route('/followupsetting/add',array('module' => 'followupsetting','controller' => 'index','action'=> 'add'));
$router->addRoute('add', $route);


$route = new Zend_Controller_Router_Route('/resetpassword',array('module' => 'signup','controller' => 'index','action'=> 'resetpassword'));
$router->addRoute('resetpassword', $route); 

$route = new Zend_Controller_Router_Route('/loginotp',array('module' => 'login','controller' => 'index','action'=> 'loginotp'));
$router->addRoute('loginotp', $route); 

$route = new Zend_Controller_Router_Route('/managequery/remainderquery',array('module' => 'managequery','controller' => 'index','action'=> 'remainderquery'));
$router->addRoute('remainderquery', $route); 

$route = new Zend_Controller_Router_Route('/managequery/deadquery',array('module' => 'managequery','controller' => 'index','action'=> 'deadquery'));
$router->addRoute('deadquery', $route); 

$route = new Zend_Controller_Router_Route('/meetingschedule/add',array('module' => 'meetingschedule','controller' => 'index','action'=> 'add'));
$router->addRoute('meetingschedule/add', $route); 

$route = new Zend_Controller_Router_Route('/template/emailtemplate',array('module' => 'template','controller' => 'index','action'=> 'emailtemplate'));
$router->addRoute('template/emailtemplate', $route);

$route = new Zend_Controller_Router_Route('/template/quotemplate',array('module' => 'template','controller' => 'index','action'=> 'quotemplate'));
$router->addRoute('template/quotemplate', $route);

$route = new Zend_Controller_Router_Route('/template/smstemplate',array('module' => 'template','controller' => 'index','action'=> 'smstemplate'));
$router->addRoute('template/smstemplate', $route);


$route = new Zend_Controller_Router_Route('/consultant/pending-quote',array('module' => 'consultant','controller' => 'index','action'=> 'pending-quote'));
$router->addRoute('consultant/pending-quote', $route);

$route = new Zend_Controller_Router_Route('/consultant/price-submitted-quote',array('module' => 'consultant','controller' => 'index','action'=> 'price-submitted-quote'));
$router->addRoute('consultant/price-submitted-quote', $route);


$route = new Zend_Controller_Router_Route('/managequote/list-ask-for-scope',array('module' => 'managequote','controller' => 'index','action'=> 'list-ask-for-scope'));
$router->addRoute('managequote/list-ask-for-scope', $route);

$route = new Zend_Controller_Router_Route('/managequote/view-askforscope/:query_id/',array('module' => 'managequote','controller' => 'index','action'=> 'view-askforscope', 'query_id'=>'\w+'));
$router->addRoute('managequote/view-askforscope', $route);
 
$route = new Zend_Controller_Router_Route('/reports',array('module' => 'manageuser','controller' => 'index','action'=> 'reports'));
$router->addRoute('reports', $route);

$route = new Zend_Controller_Router_Route('/box-tags',array('module' => 'tags','controller' => 'index','action'=> 'box-tags'));
$router->addRoute('box-tags', $route);

$route = new Zend_Controller_Router_Route('/cron/schedule-email',array('module' => 'cron','controller' => 'index','action'=> 'schedule-email'));
$router->addRoute('cron/schedule-email', $route);

?>