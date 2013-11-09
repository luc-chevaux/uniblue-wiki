<?php
require_once "Zend/Controller/Action.php"; 
class Zion_Controller_Action extends Zend_Controller_Action {
	protected static $_redirector;
	protected static $_config;
	protected static $_siteurl;
	protected static $_session;
	protected static $_flashmessenger;
	protected static $_transport;

    /**
     * @name init
     * @author Luca Martini
     * @since 1.0.0
     */
	public function init() {
		// init the controller
		self::$_redirector = $this->_helper->getHelper("redirector");
		self::$_redirector->setPrependBase(true);
		self::$_config = Zend_Registry::get("config");
		self::$_transport = Zend_Registry::get("transport");
		self::$_siteurl = self::$_config->site->url;
		self::$_flashmessenger = $this->_helper->getHelper("FlashMessenger");
		self::$_session = new Zend_Session_Namespace(self::$_config->session->name . "_" .APPSTAGE);
	}

    /**
     * @name viewInit
     * @author Luca Martini
     * @since 1.0.0
     */
    public function viewInit() {
        // init common view values
        $this->view->siteurl = self::$_siteurl;
        $this->view->controllername = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        $this->view->actionname = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $this->view->appname = self::$_config->application->name;
        $this->view->username = self::$_session->username;
        $this->view->auth = Zend_Registry::get("config")->authentication->active;
    }
}
?>
