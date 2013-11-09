<?php
require_once "Zend/Form.php";
class Forms_Register extends Zend_Form {
    /**
     * @name init
     * @since 1.0.0
     * @author Luca Martini
     */
    public function init() {
        $this->setAction("create")
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

        $password2= new Zend_Form_Element_Password("password2");
        $password2->setLabel("")
            ->addValidator("Alnum")
            ->setRequired(true)
            ->setAttrib("class", "form-control")
            ->setAttrib("placeholder", "Please repeat your password");

        $this->addElement($password2);
        
        $submit= new Zend_Form_Element_Submit("login");
        $submit->setAttrib("class", "btn btn-primary")
               ->setLabel("Create your account");

        $this->addElement($submit);
	}
}

?>