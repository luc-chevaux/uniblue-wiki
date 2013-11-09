<?php
/**
 * Created by PhpStorm.
 * User: luc
 * Date: 06/11/13
 * Time: 21.00
 */

class HomeController extends Zion_Controller_Action {
    /**
     * This is the default action for app entry point
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
        $this->view->title = "Welcome to our wiki!";
    }
}
?>