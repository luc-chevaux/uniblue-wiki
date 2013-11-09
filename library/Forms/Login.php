<?php
require_once "Zend/Form.php";
class Forms_Login extends Zend_Form {
    /**
     * @name init
     * @author Luca Martini
     * @since 1.0.0
     */
    public function init() {
        $this->setAction("auth")
             ->setName("login")
             ->setMethod("post");
        $this->setAttrib('accept-charset', 'utf-8');
             
        $username= new Zend_Form_Element_Text("username");
        $username->setLabel("User")
        		 ->addValidator("Alnum")
                 ->setRequired(true)
                 ->setAttrib("class", "form-control")
                 ->setAttrib("placeholder", "Username");

        $this->addElement($username);

        $token = new Zend_Form_Element_Hash("token");
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout(Zend_Registry::getInstance()->get("config")->authentication->timeout);
        $this->addElement($token);

        $password= new Zend_Form_Element_Password("password");
        $password->setLabel("Password")
        		 ->addValidator("Alnum")
                 ->setRequired(true)
                 ->setAttrib("class", "form-control")
                 ->setAttrib("placeholder", "Password");
                  
        $this->addElement($password);
        
        $submit= new Zend_Form_Element_Submit("login");
        $submit->setAttrib("class", "btn btn-primary")
               ->setLabel("Sign In");

        $this->addElement($submit);
	}
}

?>