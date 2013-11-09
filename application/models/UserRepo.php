<?php
/**
 * Created by PhpStorm.
 * User: luc
 * Date: 06/11/13
 * Time: 21.00
 */

require_once "Zend/Db/Table/Abstract.php";

class UserRepo extends Zend_Db_Table_Abstract {
    protected $_name = "wiki_users";
    protected $_schema = "uniblue-wiki";
    protected $_primary = "user_id";

    /**
     * Get a user by its id
     * @name getUserById
     * @param $user_id
     * @author Luca Martini
     * @since 1.0.0
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getUserById($user_id) {
        return $this->fetchRow("user_id = " . $user_id);
    }

    /**
     * Get a user by its name
     * @name getUserByName
     * @param $user_name
     * @author Luca Martini
     * @since 1.0.0
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getUserByName($user_name) {
        return $this->fetchRow("user_name = '" . $user_name . "'");
    }

    /**
     * Insert a user into database
     * @name insertUser
     * @param $dto
     * @author Luca Martini
     * @since 1.0.0
     * @return mixed
     */
    public function insertUser($dto) {
        /* insert user */
        return $this->insert($dto);
    }
}
?>