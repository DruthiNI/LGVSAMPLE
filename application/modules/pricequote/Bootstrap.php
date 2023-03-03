<?php
/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */



/**

 * Description of Bootstrap

 *

 * @author efst

 */

class Pricequote_Bootstrap extends Zend_Application_Module_Bootstrap {
  protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
           'namespace' => 'Pricequote',
           'basePath' => dirname(__FILE__)
      ));

      return $autoloader;
     }
}    





