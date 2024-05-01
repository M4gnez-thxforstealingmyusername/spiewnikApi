<?php
    require_once "./class/Songs.php";
    require_once "./class/Authentication.php";


    $routes = array(
        "register/" => false,
        "login/" => false,
        "token/" => false,
        "logout/" => false,
        "check/" => false,
        "songs/" => false,
        "song/" => true
    );


    function router($routes) {
        $uri = $_SERVER['REQUEST_URI'];
        $valid = false;

        foreach ($routes as $route => $dynamic) {

            $uriD_break = explode("/", $uri);

            if($uriD_break[3] != "" && $dynamic)
            {
                if($uriD_break[2]."/" == $route){
                    call_user_func($uriD_break[2], $uriD_break[3]);
                    $valid = true;
                }
            }
            else if($uriD_break[3] == "" && !$dynamic){
                if($uriD_break[2]."/" == $route){
                    call_user_func($uriD_break[2]);
                    $valid = true;
                }
            }
        }

        if(!$valid){
            http_response_code(404);
            include('404.php');
            exit();
        }
    }

    router($routes);

    function song($id){
        Songs::one($id);
    }
    function songs(){
        Songs::handle();
    }

    function register(){
        Authentication::Register();
    }
    function login(){
        Authentication::LogIn();
    }
    function logout(){
        Authentication::LogOut();
    }
    function check(){
        Authentication::CheckSession();
    }
    function token(){
        Authentication::TokenLogIn();
    }

?>