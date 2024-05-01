<?php
    class Authentication{
        const TOKEN_KEY = "losowe";

        private static function isPost(){
            return $_SERVER["REQUEST_METHOD"] == "POST";
        }

        private static function GenerateToken(){
            $hex = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f"];
            $token = "";
            for($i = 0; $i < 16; $i++)
                $token .= $hex[rand(0, 15)];
            return $token;
        }

        private static function pushToken($id, $token){
            include "./config/conn.php";

            $sql = "INSERT INTO `remembertoken` values (DEFAULT, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $id, $token);
            $stmt->execute();
        }

        private static function getToken($cookie){
            include "./config/conn.php";
            list($id, $token) = explode("-", $cookie);

            $sql = "SELECT * FROM remembertoken WHERE userId = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', intval($id));
            $stmt->execute();

            $result = $stmt->get_result();

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    if(hash_equals(hash_hmac('sha256', $row["token"], Authentication::TOKEN_KEY), $token))
                        return true; //0
                }
                return false; //1
            }
            return false; //2
        }

        public static function LogIn(){
            if(Authentication::isPost()){
                include "./config/conn.php";
                $username = $_POST["username"];
                $password = $_POST["password"];

                $sql = "SELECT * FROM user WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        if(password_verify($password, $row["password"]))
                        {
                            session_start();
                            $_SESSION["user"] = $row["id"];

                            echo json_encode('{id: '.$row["id"].', logged: true}', JSON_UNESCAPED_UNICODE);

                            if(isset($_POST["remember"])){
                                $token = Authentication::GenerateToken();
                                Authentication::pushToken($row["id"], $token);
                                $mac = hash_hmac('sha256', $token, Authentication::TOKEN_KEY);
                                $cookie = $row["id"] . '-' . $mac;
                                setcookie('autologin', $cookie, time()+120, "/");
                            }

                        }
                        else
                            echo json_encode('{id: '.$row["id"].', logged: false}', JSON_UNESCAPED_UNICODE);
                    }
                }
                else
                    echo json_encode('{id: '.$row["id"].', logged: false}', JSON_UNESCAPED_UNICODE);
            }
            else
                echo json_encode('{id: '.$row["id"].', logged: false}', JSON_UNESCAPED_UNICODE);
        }


        public static function TokenLogIn(){
            $cookie = isset($_COOKIE['autologin']) ? $_COOKIE['autologin'] : '';
            if(Authentication::getToken($cookie)){
                list($id, $token) = explode("-", $cookie);
                session_start();
                $_SESSION["user"] = $id;
                echo json_encode('{id: '.$id.', logged: true}', JSON_UNESCAPED_UNICODE);
            }

        }

        public static function Register(){
            if(Authentication::isPost())
            {

                if(isset($_POST["city"]) && isset($_POST["username"]) && isset($_POST["password"])){
                    $city = $_POST["city"];
                    $username = $_POST["username"];
                    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    include "./config/conn.php";

                    $sql = "INSERT INTO `user`(`cityId`, `username`, `password`) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $city, $username, $password);
                    $stmt->execute();

                    session_start();
                    $_SESSION["user"] = $id;
                    echo json_encode('{id: '.$id.', logged: true}', JSON_UNESCAPED_UNICODE);
                }
                else
                    echo json_encode('{id: '.$row["id"].', logged: false}', JSON_UNESCAPED_UNICODE);
            }
        }

        public static function LogOut(){
            session_start();
            unset($_SESSION["user"]);
            session_destroy();
        }

        public static function CheckSession(){
            session_start();
            if(isset($_SESSION["user"]))
                echo "logged";
            else
                echo "not logged";
        }

    }
?>