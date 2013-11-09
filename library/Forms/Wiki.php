<?php
require_once "Zend/Form.php";
class Forms_Wiki extends Zend_Form {
    /**
     * @name __construct
     * @param null $wiki
     * @param bool $validation
     * @since 1.0.0
     * @author Luca Martini
     * @return void|Zend_Form
     */
    public function __construct($wiki = null, $validation = false) {
		// call the parent constructor
        parent::__construct ();

        // init the form
		$this->init ();

        // populate wiki if not empty
		if (!empty($wiki)) {
			$this->populate();
		}
	}

    /**
     * @name populate
     * @since 1.0.0
     * @author Luca Martini
     * @return void|Zend_Form
     */
    public function populate(array $wiki) {
		$id = $this->getElement("wiki_id")->setValue($wiki["wiki_id"]);
        $title = $this->getElement("wiki_title")->setValue($wiki["wiki_title"]);
        $body = $this->getElement("wiki_body")->setValue($wiki["wiki_body"]);
	}

    /**
     * @name init
     * @since 1.0.0
     * @author Luca Martini
     * @return void|Zend_Form
     */
	public function init() {
		// init the action scope parameters
		$this->setAction("insert");
		$this->setName("insert");
        $this->setMethod("post");
		$this->setAttrib('accept-charset', 'utf-8');
        
        // add the wiki title field
        $title = new Zend_Form_Element_Text("wiki_title");
        $title->setLabel("Title")
        	  ->addValidator("Alnum")
              ->setAttrib("class", "form-control")
              ->setAttrib("placeholder", "Title")
              ->setRequired(true);
        $this->addElement($title);
        
        // add the wiki body
        $body = new Zend_Form_Element_Textarea("wiki_body");
        $body->setLabel("Body")
		        	->addValidator("Alnum")
		            ->setRequired(true)
		            ->setAttrib("rows",3);
        $this->addElement($body,"wiki_body");

        // add the wiki hidden id
        $id = new Zend_Form_Element_Hidden("wiki_id");
        $this->addElement($id,"wiki_id");

        // add the submit
        $submit = new Zend_Form_Element_Submit("Submit");
        $this->addElement($submit);
	}

    /**
     * Set the form action
     * @name setAction
     * @param string $action
     * @since 1.0.0
     * @author Luca Martini
     * @return void|Zend_Form
     */
    public function setAction($action) {
		$config = Zend_Registry::get("config");
		$siteurl = $config->site->url;
		parent::setAction($siteurl."/wiki/".$action);
	}
}
?>