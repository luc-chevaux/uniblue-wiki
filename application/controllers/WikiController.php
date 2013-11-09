<?php
/**
 * Created by PhpStorm.
 * User: luc
 * Date: 06/11/13
 * Time: 21.00
 */

class WikiController extends Zion_Controller_Action {
    /**
     * This is the wiki wiew action
     * @name viewAction
     * @get_param view
     * @since 1.0.0
     * @author Luca Martini
     */
    public function viewAction() {
        // get the wiki persistent url
        $wikiPsUrl = $this->_getParam("url");

        // create a new repository
        $repo = new WikiRepo();

        // get a wiki by its persistent url
        $wiki = $repo->getWikiByPersistentUrl($wikiPsUrl);

        // update visit
        $dto = $wiki;
        $visits = intval($wiki["wiki_visits_count"]);
        $visits++;
        $dto["wiki_visits_count"] = $visits;
        $repo->updateWiki($dto);

        // init the view
        $this->viewInit();
        $this->view->title = parent::$_config->application->name." - ".$wiki["wiki_title"];
        $this->view->pagetitle = $wiki["wiki_title"];
        $this->view->wiki = $wiki;
    }

    /**
     * This is the wiki creation page action
     * @name createAction
     * @get_param view
     * @since 1.0.0
     * @author Luca Martini
     */
    public function createAction() {
        // init the view
        $this->viewInit();
        $this->view->pagetitle = "Create Wiki";
        $this->view->form = new Forms_Wiki();
    }

    /**
     * This is the wiki creation process action
     * @name insertAction
     * @get_param view
     * @since 1.0.0
     * @author Luca Martini
     */
    public function insertAction() {
        // get the params
        $title = $this->_getParam("wiki_title");
        $body = $this->_getParam("wiki_body");

        // create a dto to persist
        $dto = array();
        $dto["wiki_title"] = $title;
        $dto["wiki_body"] = $body;
        $dto["user_id"] = parent::$_session->userid;
        $dto["wiki_created"] = date('Y-m-d H:i:s');
        $dto["wiki_persistent_url"] = $this->generatePersistentUrl($title);
        $dto["wiki_visits_count"] =parent::$_session->userid;

        // create a repository
        $repo = new WikiRepo();

        // persist
        $repo->insertWiki($dto);

        // init the view
        $this->viewInit();

        // redirect to home
        parent::$_redirector->gotoUrl($this->_siteurl."/home");
    }

    /**
     * @name generatePersistentUrl
     * @param $str
     * @param array $replace
     * @param string $delimiter
     * @return mixed|string
     */
    function generatePersistentUrl($str, $replace=array(), $delimiter='-') {
        setlocale(LC_ALL, 'en_US.UTF8');
        if( !empty($replace) ) {
            $str = str_replace((array)$replace, ' ', $str);
        }
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        return $clean;
    }
}
?>