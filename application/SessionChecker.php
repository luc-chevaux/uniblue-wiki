<?php
/**
 * Created by PhpStorm.
 * User: luc
 * Date: 06/11/13
 * Time: 21.00
 */

require_once "Zend/Controller/Plugin/Abstract.php";
require_once "Zend/Controller/Front.php";
require_once "Zend/Controller/Request/Abstract.php";
require_once "Zend/Controller/Action/HelperBroker.php";

class SessionChecker extends Zend_Controller_Plugin_Abstract {
    protected $_front;
    protected $_controller;
    protected $_registry;
    protected $_session;

    /**
     * Constructor
     * @name __construct
     * @since 1.0.0
     * @author luca martini
     */
    public function __construct() {
    	$this->_front = Zend_Controller_Front::getInstance();
    	$this->_registry = Zend_Registry::getInstance();
    	$this->_config = $this->_registry->get("config");
		$this->_siteurl = $this->_config->site->url;
    }

    /**
     * Handle predispatch
     * @name preDispatch
     * @since 1.0.0
     * @author luca martini
     */
    public function preDispatch (Zend_Controller_Request_Abstract $request) {
        // get the controller name
        $this->_controller = $this->_front->getRequest()->getControllerName();
		
        if (($this->_controller=="wiki") && ($this->_request->getActionName() == 'create')) {
            if ($this->_config->authentication->active) {           	
                $this->checkSession();
            } 
        }
    }

    /**
     * Check session auth before dispatch to a single controller
     * @name checkSession
     * @since 1.0.0
     * @author luca martini
     */
    private function checkSession () {
    	$this->_session = new Zend_Session_Namespace($this->_config->session->name . "_" .APPSTAGE);
    	/*$this->_session = $this->_registry->get("Zend_Session");*/
        if (empty($this->_session->username)) {
            $this->_response->setRedirect($this->_siteurl."/index/login")->sendResponse();
	    	exit;
        }
    }
}
?>