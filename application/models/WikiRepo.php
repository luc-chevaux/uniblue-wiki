<?php
/**
 * Created by PhpStorm.
 * User: luc
 * Date: 06/11/13
 * Time: 21.00
 */

require_once "Zend/Db/Table/Abstract.php";

class WikiRepo extends Zend_Db_Table_Abstract {
    protected $_name = "wiki_pages";
    protected $_schema = "uniblue-wiki";
    protected $_primary = "wiki_id";

    /**
     * Get a wiki by user id
     * @name getWikiByUserId
     * @param $user_id
     * @author Luca Martini
     * @since 1.0.0
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getWikiByUserId($user_id) {
        return $this->fetchAll("wiki_user_id = " . $user_id);
    }

    /**
     * Get a wiki by persistent url
     * @name getWikiByPersistentUrl
     * @param $url
     * @author Luca Martini
     * @since 1.0.0
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getWikiByPersistentUrl($url) {
        return $this->fetchRow("wiki_persistent_url = '" . $url . "'");
    }

    /**
     * Get all wikies
     * @name getAllWikies
     * @author Luca Martini
     * @since 1.0.0
     * @return mixed
     */
    public function getAllWikies() {
        $db = Zend_Registry::get("db");
        $sql = "SELECT wiki_id, wiki_title, wiki_body, wiki_persistent_url, wiki_visits_count, user_name, wiki_created
                FROM wiki_pages wp, wiki_users wu
                WHERE wp.user_id = wu.user_id
                ORDER BY wiki_visits_count DESC";
        return $db->fetchAll($sql);
    }

    /**
     * Insert a wiki in the database
     * @name insertWiki
     * @param $dto
     * @author Luca Martini
     * @since 1.0.0
     * @return mixed
     */
    public function insertWiki($dto) {
	    /* insert wiki */
		return $this->insert($dto);
	}

    /**
     * Update a wiki into the database
     * @name updateWiki
     * @param $dto
     * @author Luca Martini
     * @since 1.0.0
     * @return mixed
     */
    public function updateWiki($dto) {
        /* modify wiki */
        $set = array("user_id"	            => $dto["user_id"],
                     "wiki_body"            => $dto["wiki_body"],
                     "wiki_persistent_url"	=> $dto["wiki_persistent_url"],
                     "wiki_visits_count"	=> $dto["wiki_visits_count"],
                     "wiki_title"	        => $dto["wiki_title"]
                    );

        $where = array("wiki_id = ?" => $dto["wiki_id"]);
        $this->update($set, $where);
    }
}
?>