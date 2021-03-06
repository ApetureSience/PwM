<?php
/**
 * Created by IntelliJ IDEA.
 * User: MrFibunacci
 * Date: 19.06.2016
 * Time: 20:16
 */
    require_once(__DIR__."/../server.php");
    require_once("../AES/AESclass.php");


    class user {
        private $whiteList = array(
            0 => "MrFibunacci"
            // add more user here
        );

        /**
         * @return array
         */
        public function getWhiteList()
        {
            return $this->whiteList;
        }

        public function setUser($name, $password){
            session_start();
            if(self::isUserOnWhiteList($_SESSION['user'])) {
                if (self::isUserExistent($name) == false) {
                    self::createUser($name, $password);
                    return true;
                } else {
                    return false;
                }
            } else {
                return "403";
            }
        }

        public function getUser($name, $pw){
            $server = new server();

            $db = $server->connect();

            $sql = "SELECT * FROM user WHERE
                    unsername='$name' AND
                    pw='$pw'
                LIMIT 1";

            $res = mysqli_query($db, $sql);

            return mysqli_fetch_array($res);
        }

        public function createUser($name, $pw){
            $server = new server();
            $aes    = new AES($server->getAESKey());

            $db = $server->connect();

            $sql = "INSERT INTO `user` (`unsername`, `pw`) VALUES ('" .$name. "', '" .$aes->encrypt($pw). "')";

            $db->query($sql);

            $sql = "SELECT `id` FROM `user` WHERE `unsername`='".$name."'";

            $ID = mysqli_query($db, $sql);

            $ID = mysqli_fetch_array($ID);

            $sql = "CREATE TABLE IF NOT EXISTS `cont".$ID[0]."` (
                      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                      `nameOfPlattform` varchar(64) NOT NULL,
                      `username` varchar(64) DEFAULT NULL,
                      `pw` varchar(128) DEFAULT NULL,
                      `email` varchar(64) DEFAULT NULL,
                      `name` varchar(32) DEFAULT NULL
                      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
            mysqli_query($db, $sql);
        }

        private function isUserExistent($name){
            $server = new server();

            $db = $server->connect();

            $sql = "SELECT * FROM `user` WHERE `unsername`='".$name."'";

            $res = mysqli_query($db, $sql);
            $res = mysqli_fetch_array($res);

            if($res != null){
                return true;
            } else {
                return false;
            }
        }

        public function isUserOnWhiteList($user){
            $isTrue = false;
            foreach(self::getWhiteList() as $key => $value){
                if($value == $user){
                    $isTrue = true;
                }
            };

            if($isTrue){
                return true;
            } else {
                return false;
            }
        }
    }