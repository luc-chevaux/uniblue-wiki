<?php
/**
 * Created by PhpStorm.
 * User: luc
 * Date: 06/11/13
 * Time: 21.00
 */

class ErrorController extends Zion_Controller_Action {
    /**
     * This action handles the error events
     * @name errorAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function errorAction() {
        $errors = $this->_getParam("error_handler");
        
        // assegno i valori alla view
        $this->viewInit();
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found                
                $this->getResponse()->setRawHeader("HTTP/1.1 404 Not Found");
                $this->view->title = "HTTP/1.1 404 Not Found";
                break;
            default:
                // application error; display error page, but don't change                
                // status code
                $this->view->title = "Application Error";
                break;
        }
        $this->view->message = $errors->exception;
    }

    /**
     * This action handles the csrf attack
     * @name csrfForbiddenAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function csrfForbiddenAction() {
    	$this->getResponse()->setRawHeader("HTTP/1.1 403 Forbidden");

        parent::$_flashmessenger->addMessage("Attenzione, per questioni di sicurezza si prega di aggiornare la pagina prima di effettuare la login.");
        parent::$_flashmessenger->addMessage("Si consiglia di non memorizzare mai le informazioni di login.");
        
        // assegno i valori alla view
        $this->viewInit();
        $this->view->messaggi=  parent::$_flashmessenger->getMessages();
        $this->view->title =  parent::$_config->application->name." - 403 Forbidden";
        $this->view->pagetitle = "403 Forbidden";

        // Redirect all'homepage
        parent::$_redirector->gotoUrl($this->_siteurl."/home");
    }

    /**
     * This action handles the denied event
     * @name deniedAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function deniedAction() {
    	// assegno i valori alla view
        $this->viewInit();
    	$this->view->title =  parent::$_config->application->name." - Accesso Negato";
    	$this->view->pagetitle = "Accesso Negato";
    	$this->view->message = "Stai tentando di accedere a una risorsa non consentita per il tuo profilo.";
    }

    /**
     * This action handles the no ownership event
     * @name noownershipAction
     * @since 1.0.0
     * @author Luca Martini
     */
    public function noownershipAction() {
    	// assegno i valori alla view
        $this->viewInit();
    	$this->view->title = parent::$_config->application->name." - Accesso Negato";
    	$this->view->pagetitle = "Accesso Negato";
    	$this->view->message = "Stai tentando di accedere a una risorsa che non possiedi. Per quale motivo stai facendo questo?";
    }
}
?>