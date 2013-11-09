<?php
/**
 * Created by PhpStorm.
 * User: luc
 * Date: 06/11/13
 * Time: 21.00
 */

require_once ("Zend/Loader/Autoloader.php");
require_once ("SessionChecker.php");

class Bootstrap {
    public static $frontController;
    public static $controller;
    public static $registry;
    public static $configPath;
    public static $config;
    public static $output_compression;
    public static $log_queries;
    public static $mySession;
    public static $vendorAcl;
    public static $transport;

    /**
     * Bootstrap start
     * @name start
     * @since 1.0.0
     * @author luca martini
     */
    public static function start($configPath) {
  		self::$configPath = $configPath;
  		// setup the Zend Loader
  		self::setupLoader();
  		// load the configuration file
  		self::loadConfiguration();
		// setup the enviroment
	    self::setupEnvironment();
	    // prepare your application
        self::prepare();
        // send the response to the browser
        self::sendResponse(self::$frontController->dispatch());
    }

    /**
     * Bootstrap setup class loader
     * @name setupLoader
     * @since 1.0.0
     * @author luca martini
     */
    public static function setupLoader() {
    	// Load Zend Loader
		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace("Forms_");
		$loader->setFallbackAutoloader(true);
		$loader->suppressNotFoundWarnings(false);
    }

    /**
     * Load Xml Configuration
     * @name loadConfiguration
     * @since 1.0.0
     * @author luca martini
     */
    public static function loadConfiguration() {
    	// Load the Configuration
    	self::$config = new Zend_Config_Xml(self::$configPath, APPSTAGE);
    }

    /**
     * Setup environment
     * @name setupEnvironment
     * @since 1.0.0
     * @author luca martini
     */
    public static function setupEnvironment() {
    	// GEt the environment settings
    	$environment = self::$config->environment;

		// Setup error reporting
		if($environment->display_errors) {
			error_reporting(E_ALL ^ E_NOTICE);
		} else {
			error_reporting(E_NONE);
		}

        // Setup other envoironmental configurations
        date_default_timezone_set($environment->default_timezone);
        self::$output_compression = (self::$config->environment->compress_output) ? true : false;
        self::$log_queries 		  = (self::$config->environment->log_queries)     ? true : false;

        // Setup loggin and debugging options
		// I am using Zend_Log_Writer_Firebug here, you can use your favorite method
        if(self::$config->environment->debug) {
        	$logger = new Zend_Log();
			$logger->addWriter(new Zend_Log_Writer_Firebug());
			Zend_Registry::set("logger",$logger);
        }
    }

    /**
     * Prepare application
     * @name prepare
     * @since 1.0.0
     * @author luca martini
     */
	public static function prepare() {
		// setup acl
		self::setupAcl();
		// setup session
    	self::setupSession();
    	// setup smtp
		self::setupTransport();
    	// setup registry
    	self::setupRegistry();
        // setup front controller, view, database
        self::setupFrontController();
        self::setupDatabase();
        self::setupView();
        // setup routes
		self::setupRoutes();
		// setup helpers
		self::setupHelpers();
	}

    /**
     * Setup transport
     * @name setupTransport
     * @since 1.0.0
     * @author luca martini
     */
	public static function setupTransport() {
		if (self::$config->mail->use_auth == 1) {
			$config = array('auth' => self::$config->mail->auth_type,
		 					'username' => self::$config->mail->auth_username,
		 					'password' => self::$config->mail->auth_password);
			self::$transport = new Zend_Mail_Transport_Smtp(self::$config->mail->smtp, $config);
		} else {
			self::$transport = new Zend_Mail_Transport_Smtp(self::$config->mail->smtp);
		}
	}

    /**
     * Send Response
     * @name sendResponse
     * @since 1.0.0
     * @author luca martini
     */
	public static function sendResponse(Zend_Controller_Response_Http $response) {
    	if(self::$output_compression) {
			null;
    	} else {
    		$response->sendResponse();
    	}
    }

    /**
     * Setup Acl
     * @name setupAcl
     * @since 1.0.0
     * @author luca martini
     */
    public static function setupAcl() {
    	// create new Zend Acl
        self::$vendorAcl = new Zend_Acl();

    	// define roles
        $roleContributor = new Zend_Acl_Role("contributor");

        // define resources
		self::$vendorAcl->add(new Zend_Acl_Resource("error"));
		self::$vendorAcl->add(new Zend_Acl_Resource("home"));
		self::$vendorAcl->add(new Zend_Acl_Resource("index"));
		self::$vendorAcl->add(new Zend_Acl_Resource("wiki"));

        // add contributor to global roles
        self::$vendorAcl->addRole($roleContributor);

        // assign acl
        $contributorAction = Array("");
        $commonAction = Array("view");

        self::$vendorAcl->allow("contributor", "wiki", $commonAction);
        self::$vendorAcl->allow("contributor", "wiki", $contributorAction);

        // assign acl for error
        self::$vendorAcl->allow("contributor", "error");

        // assign acl for home
        $comAction = Array("index","logout");
        self::$vendorAcl->allow("contributor", "home", $comAction);

        // assign acl for index
        $comAction = Array("index","login","submit");
        self::$vendorAcl->allow("contributor", "index", $comAction);
    }

    /**
     * Setup Session
     * @name setupSession
     * @since 1.0.0
     * @author luca martini
     */
	public static function setupSession() {
		// Start Zend_Session and set session to registry
        Zend_Session::setOptions(self::$config->session->toArray());
        Zend_Session::start();
        self::$mySession = new Zend_Session_Namespace(self::$config->session->name . "_" .APPSTAGE);
        if (!isset(self::$mySession->initialized)) {
            Zend_Session::regenerateId();
            self::$mySession->initialized = true;
        }
    }

    /**
     * Setup Registry
     * @name setupRegistry
     * @since 1.0.0
     * @author luca martini
     */
    public static function setupRegistry() {
	    self::$registry = Zend_Registry::getInstance();
		self::$registry->set("config",   self::$config);
	    self::$registry->set("siteInfo", self::$config->site);
	    self::$registry->set("path", 	 self::$config->path);
	    self::$registry->set("transport", self::$transport);
    }

    /**
     * Setup Helpser
     * @name setupHelpers
     * @since 1.0.0
     * @author luca martini
     */
	public static function setupHelpers() {
		Zend_Controller_Action_HelperBroker::addPath("../application/default/helpers", "Zend_Controller_Action_Helper");
		Zend_Controller_Action_HelperBroker::getStaticHelper("redirector");
	}

    /**
     * Setup Front Controller
     * @name setupFrontController
     * @since 1.0.0
     * @author luca martini
     */
    public static function setupFrontController() {
		// Initialize and set the front controller options
        self::$frontController = Zend_Controller_Front::getInstance();
        self::$frontController->returnResponse(true);
        /*self::$frontController->addModuleDirectory(realpath(APP_ROOT."/modules/"));*/
        self::$frontController->setControllerDirectory(realpath(APP_ROOT."/controllers/"));
       	self::$frontController->setBaseUrl(self::$config->site->baseurl);
        self::$frontController->registerPlugin(new SessionChecker());

        // acl plugin
        // $aclPlugin = new Zion_Controller_Plugin_Acl(self::$vendorAcl);

       /* if (self::$mySession->username == "admin") {
        	$aclPlugin->setRoleName("admin");
		} else {
			$aclPlugin->setRoleName("vendor");
		}

        self::$frontController->registerPlugin($aclPlugin);*/

	    self::$frontController->throwExceptions(false);

        // Register  required controller plugin
		//self::$frontController->registerPlugin(new My_Plugins_Abcde());
	}

    /**
     * Setup View
     * @name setupView
     * @since 1.0.0
     * @author luca martini
     */
    public static function setupView() {
    	$view = new Zend_View();
    	$view->setEncoding("UTF-8");

		// Add additional view helpers and scripts path
		$view->assign("appstage", APPSTAGE);

		// Set the layout options
		Zend_Layout::startMvc(array("layoutPath" => APP_ROOT."/layouts","layout" => "main"));

		// Set the view file extentions (.phtml is default)
    	$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
    	Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }

    /**
     * Setup Database
     * @name setupDatabase
     * @since 1.0.0
     * @author luca martini
     */
    public static function setupDatabase() {
		// Collect database options from config and set up default database adapter
		$dbConfig = self::$config->database;
		$db = Zend_Db::factory($dbConfig->adapter,
              array ("host"   => $dbConfig->params->host,
                 	 "username" => $dbConfig->params->username,
                 	 "password" => $dbConfig->params->password,
					 "dbname"   => $dbConfig->params->dbname));
        $db->query("SET NAMES 'utf8'");
		Zend_Db_Table::setDefaultAdapter($db);
		$registry = Zend_Registry::getInstance();

		$registry->set("db", $db);
    	$registry->set("dbconfig", $dbConfig);

    	// Log all db queries if required
    	if(self::$log_queries) {
			// I am using Zend_Db_Profiler_Firebug for loggin db queries, you can use anythig you want
    		$profiler = new Zend_Db_Profiler_Firebug("All DB Queries");
			$profiler->setEnabled(true);
			$db->setProfiler($profiler);
    	}
    }

    /**
     * Setup Routes
     * @name setupRoutes
     * @since 1.0.0
     * @author luca martini
     */
	public static function setupRoutes() {
		//Set your routings here
	}
}