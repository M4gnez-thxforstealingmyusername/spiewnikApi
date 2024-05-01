<?php
class Err{
    private static $errors;
    public static function GetErrors(){
        $json = file_get_contents("./config/errors.json");
        Err::$errors = json_decode($json);
    }
    public static function Die($errorCode){
        foreach (Err::$errors as $e)
        {
            if ($e->code == $errorCode)
            {
                exit($e->message);
            }
        }
    }

}
?>