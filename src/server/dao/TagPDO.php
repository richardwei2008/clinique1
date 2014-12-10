<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-12
 * Time: 下午10:16
 */

namespace customer;
require_once(dirname(__FILE__) ."/../database/DbConfig.php");

use \PDO;
use \PDOException;
use database\DbConfig;
class TagPDO {
    private $conn = null;

    /**
     * @return null|\PDO
     */
    public function getConn()
    {
        if ($this->conn == null) {
            $connectionString = sprintf("mysql:host=%s;dbname=%s",
                DbConfig::DB_HOST,
                DbConfig::DB_NAME);
            try {
                $this->conn = new PDO($connectionString,
                    DbConfig::DB_USER,
                    DbConfig::DB_PASSWORD,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8",
                    )
                );
            } catch (PDOException $pe) {
                die($pe->getMessage());
            }
        }
        return $this->conn;
    }

    /**
     * Open the database connection
     */
    public function __construct(){
        // open database connection
    }

    public function insertSingleRow($openid, $tag){
        try {
            $clnq_user_tag = array(
                ':openid' => $openid,
                ':tag' => $tag);

            $sql = 'INSERT INTO clnq_user_tag(openid, tag)
                    VALUES(:openid, :tag)';
            $q = $this->getConn()->prepare($sql);

            return $q->execute($clnq_user_tag);
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findTag($openid){
        if (is_null($openid) || (is_string($openid) &&  '' === trim($openid))) {
            return null;
        }
        try {
            $clnq_user_tag = array(
                ':openid' => $openid
            );
            $sql = "SELECT TRIM(LEADING '<br>' FROM t.tag ) as content, t.*
                    FROM clnq_user_tag t
                    WHERE openid  = :openid";

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_tag);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function updateTag($openid, $tag){
        $clnq_user_tag = array(
            ':tag' => $tag,
            ':openid' => $openid
        );
        $sql = 'UPDATE clnq_user_tag
            SET tag  = :tag
            WHERE openid = :openid';
        $q = $this->getConn()->prepare($sql);
        return $q->execute($clnq_user_tag);
    }
}

//// db setup test
// $obj = new TagPDO();
//
// try {
//     if($obj->insertSingleRow('oxqECj7JTrpVG7BfJnNCUpQap012', '不会抱怨，只会包容！我就是这样的真男人。')) {
//         echo 'A new task has been added successfully<br>';
//     } else {
//         echo 'Error adding the task';
//     }
// } catch (Exception $ex) {
//        echo($ex->getMessage());
// }
//
//// find
//try {
//    if($tag = $obj->findTag('oxqECj7JTrpVG7BfJnNCUpQap012')) {
//        echo '<br>Found.<br>'.(var_dump($tag));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//// update
//try {
//    if($obj->updateTag('oxqECj7JTrpVG7BfJnNCUpQap012', '不懂放弃，只懂坚持！我就是这样的真男人。') !== false) {
//        echo 'The task has been updated successfully';
//    } else {
//        echo 'Error updated the task';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//// find
//try {
//    if($tag = $obj->findTag('oxqECj7JTrpVG7BfJnNCUpQap012')) {
//        echo '<br>Found.<br>'.(var_dump($tag));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}