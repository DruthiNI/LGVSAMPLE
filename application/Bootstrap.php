<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
protected function _initAutoload()
{

$moduleLoader = new Zend_Application_Module_Autoloader(array(
'namespace' => '', 
'basePath'  => APPLICATION_PATH));
return $moduleLoader;
}
public function _initDbAdapter()
{

$this->bootstrap('db');

$dbAdapter = $this->getResource('db');
Zend_Registry::set('db', $dbAdapter);
}
		protected function _initSessionData()
		{

			Zend_Session::start();

			$session = new Zend_Session_Namespace('technology');

        	Zend_Registry::set('session', $session);

		}	

protected function _initConfig()
{

Zend_Registry::set('config', $this->getOptions());
}

//................................................. Url Rewriting for Product category  .........................................................

	   protected function _initDoctype()
{
$this->bootstrap('view');
$view = $this->getResource('view');
$view->doctype('XHTML1_STRICT');
}	

   protected function _initRoutes()
{
    $router = Zend_Controller_Front::getInstance()->getRouter();
    include APPLICATION_PATH . "/configs/routes.php";
	
} 

}