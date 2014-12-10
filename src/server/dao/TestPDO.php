<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-12
 * Time: 下午11:09
 */

namespace customer;
header('Content-Type: text/html; charset=utf-8');
require_once(dirname(__FILE__) ."/../database/DbConfig.php");

use \PDO;
use \PDOException;
use database\DbConfig;

class TestPDO {

    private $conn = null;

    /**
     * @param null|\PDO $conn
     */
    public function setConn($conn)
    {
        $this->conn = $conn;
    }

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


    public function checkDB(){
        try {
            $clnq_user_success = array(
                ':dateFrom' => null,
                ':dateTo' => null
            );

            $sql = 'SELECT * FROM clnq_site_allocation LIMIT 0 , 30';

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_success);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            $r = $q->fetchAll();
            return $r;
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

}

//// db setup test
$obj = new TestPDO();
//
//// TEST
//

//// find
try {
    echo date("Y-m-d").'<br>';
    echo date("Y-m-d",strtotime("+1 day")).'<br>';
    $time = time();
    echo $time."<br/>";
    echo date("Y-m-d H:i:s", time())."<br/>";
    //  if($success = $obj->checkDB()) {
 //           echo '<br>Found.<br>'.(var_dump($success));
 //       } else {
 //           echo '<br>Error.<br>';
 //       }
} catch (Exception $ex) {
    echo($ex->getMessage());
}


