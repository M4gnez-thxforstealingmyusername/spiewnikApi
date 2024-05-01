<?php

class Songs{
    public static function handle(){
        include "./config/conn.php";
        header('Content-type: text/plain; charset=utf-8');

        switch($_SERVER["REQUEST_METHOD"]){
            case "GET":
                $sql = "SELECT * FROM song";
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $result = $stmt->get_result();

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
        include "./config/conn.php";

        switch($_SERVER["REQUEST_METHOD"]){
            case "GET":
                $sql = "SELECT * FROM song WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $id);
                $stmt->execute();

                $result = $stmt->get_result();

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