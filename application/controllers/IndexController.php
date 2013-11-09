<?php
/**
 * Created by PhpStorm.
 * User: luc
 * Date: 06/11/13
 * Time: 21.00
 */

class IndexController extends Zion_Controller_Action {

    /**
     * This is the application entry point
     * @name indexAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function indexAction() {
        // instantiate the repository
        $repo = new WikiRepo();

        // get all wikies
        $wikies = $repo->getAllWikies();

        // init the view
        $this->viewInit();
        $this->view->wikies = $wikies;

		// redirect user to homepage
		parent::$_redirector->gotoUrl($this->_siteurl."/home");
    }

    /**
     * This is the application registration form
     * @name registerAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function registerAction() {
        // init the view
        $this->viewInit();
        if (parent::$_flashmessenger->hasMessages()) {
            $this->view->messaggi = parent::$_flashmessenger->getMessages();
        }
        $this->view->title = parent::$_config->application->name." - Registration page";
        $this->view->pagetitle = "Registration";
        $form = new Forms_Register();
        $this->view->form = $form;
    }

    /**
     * This is the registration form process
     * @name createAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function createAction() {
        // get the form datas
        $username = $this->getRequest()->getPost("username");
        $password = $this->getRequest()->getPost("password");
        $password2 = $this->getRequest()->getPost("password2");

        // init the view
        parent::viewInit();

        // check if the password match
        if ($password != $password2) {
            // add message
            parent::$_flashmessenger->addMessage("Your password do not match");
            $this->view->messaggi = parent::$_flashmessenger->getMessages();

            // redirect user to homepage
            parent::$_redirector->gotoUrl($this->_siteurl."/index/register");
        } else {
            // encript the password
            $encPass = md5(parent::$_config->authentication->salt.$password);

            // create a new repository
            $repo = new UserRepo();

            // create a $dto
            $dto = array();
            $dto["user_name"] = $username;
            $dto["user_password"] = $encPass;

            // insert the user
            $user_id = $repo->insertUser($dto);

            // regenerate the session
            Zend_Session::regenerateId();

            // put user id in session
            parent::$_session->userid = $user_id;

            // put the username in session
            parent::$_session->username = $username;

            // redirect user to homepage
            parent::$_redirector->gotoUrl($this->_siteurl."/home");
        }
    }

    /**
     * This is the login page
     * @name loginAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function loginAction() {
        // init the view
        $this->viewInit();

    	if (parent::$_flashmessenger->hasMessages()) {
            $this->view->messaggi = parent::$_flashmessenger->getMessages();
        }
        $this->view->title = parent::$_config->application->name." - Authentication page";
        $this->view->pagetitle = "Login";
        $this->view->form = new Forms_Login();

        $this->render("login");
    }

    /**
     * This is the logout action
     * @name logoutAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function logoutAction() {
        // destroy the session and do the logout
        if (Zend_Session::sessionExists()) {
            Zend_Session::destroy(true, true);

            // redirect user to login page
            parent::$_redirector->gotoUrl($this->_siteurl . "/home");
        }
    }

    /**
     * This is the login action
     * @name authAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function authAction() {
    	/* makes the signin process */
    	
    	// test if form is valid
        $form= new Forms_Login();
        if (!$form->isValid($_POST)) {
        	if (count($form->getErrors("token")) > 0) {
        		return $this->_forward("csrf-forbidden", "error");
        	} else {
            	$this->view->form = $form;
            	return $this->render("login");
        	}	
        }
        
        // get the form datas
        $username = $this->getRequest()->getPost("username");
        $password = $this->getRequest()->getPost("password");
		
        // use the adapter for authentication
        $authAdapter = new Zend_Auth_Adapter_DbTable(
            Zend_Registry::get("db"),
                "wiki_users",				    	// table
                "user_name",				    	// user
                "user_password",					// password
                "MD5(CONCAT('" .  parent::$_config->authentication->salt . "',?))"	            // salt & active
            );
        
        // sign in
        $authAdapter->setIdentity($username)->setCredential($password);
        $result= $authAdapter->authenticate();
        
        // regenerate the session id
        Zend_Session::regenerateId();

        // init view
        $this->viewInit();

        // test if result is valid
        if (!$result->isValid()) {
            parent::$_flashmessenger->addMessage("Authentication Error");
            $this->view->messaggi = parent::$_flashmessenger->getMessages();
            parent::$_redirector->gotoUrl($this->_siteurl."/index/login");
        } else {
            // instantiate user repo
            $repo = new UserRepo();
            $user = $repo->getUserByName($username);

			// put user id in session
            parent::$_session->userid = $user["user_id"];

            // put the username in session
            parent::$_session->username = $result->getIdentity();

            // redirect user to wiki homepage
            parent::$_redirector->gotoUrl($this->_siteurl."/home");
        }
    }
}
?>