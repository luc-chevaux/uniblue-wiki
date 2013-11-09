<?php
// application context definition
define("ZEND_ROOT", realpath("../../../resources/library"));
define("PUBLIC_ROOT", realpath(dirname(__FILE__)));
define("PROJ_ROOT", realpath(dirname(dirname(__FILE__))));
define("APP_ROOT", realpath(PROJ_ROOT . "/application"));
define("XML_CONF_FILE", realpath(APP_ROOT . "/config/config.xml"));

// server list to automate configuration selection
$development = array("wiki.uniblue.com:6060");
$staging = array();
$production = array();

// get the current host
$host = $_SERVER["HTTP_HOST"];

// check which environment am I running
if (in_array($host, $development)) {
    define("APPSTAGE", "development");
    set_include_path(PROJ_ROOT . "/library" . PATH_SEPARATOR . ZEND_ROOT . PATH_SEPARATOR . APP_ROOT . "/models" . PATH_SEPARATOR . get_include_path());
} elseif (in_array($host, $staging)) {
    define("APPSTAGE", "staging");
    set_include_path(PROJ_ROOT . "/library" . PATH_SEPARATOR . ZEND_ROOT . PATH_SEPARATOR . APP_ROOT . "/models" . PATH_SEPARATOR . get_include_path());
} elseif (in_array($host, $production)) {
    define("APPSTAGE", "production");
    set_include_path(PROJ_ROOT . "/library" . PATH_SEPARATOR . ZEND_ROOT . PATH_SEPARATOR . APP_ROOT . "/models" . PATH_SEPARATOR . get_include_path());
} else {
    die("This application has no configuration for this host.");
}

// load bootstrap
require_once(APP_ROOT . "/Bootstrap.php");
Bootstrap::start(XML_CONF_FILE);
?>