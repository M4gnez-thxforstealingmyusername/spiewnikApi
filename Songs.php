<?php

class Songs{
    public static function handle(){
        include "./conn.php";
        header('Content-type: text/plain; charset=utf-8');

        switch($_SERVER["REQUEST_METHOD"]){
            case "GET":
                $request = "SELECT * FROM song";

                $result = $conn->query($request);
                if($result->num_rows > 0)
                {
                    $rows = mysqli_fetch_all($result);

                    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
                }
                break;
            default:
            break;

        }
    }

    public static function one($id){
        include "./conn.php";

        switch($_SERVER["REQUEST_METHOD"]){
            case "GET":
                $request = "SELECT * FROM song WHERE id = ".$id;

                $result = $conn->query($request);
                if($result->num_rows > 0)
                {
                    $rows = mysqli_fetch_row($result);

                    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
                }
            break;
        }
    }

}

?>