<?php
    require_once "./Songs.php";
    require_once "./Authentication.php";


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
            $url = '';

            if($dynamic)
            {
                $uriD_break = explode("/", $uri);
                $dynamicId = $uriD_break[count($uriD_break)-1];
                $uriD_break[count($uriD_break)-1] = null;
                $uriD = join("/", $uriD_break);

                $url = "/spiewnikApi/".$route;

                if($url == $uriD && $dynamicId != ""){
                    call_user_func(str_replace("/", '', $route), $dynamicId);
                    $valid = true;
                }

            }
            else{
                $url = "/spiewnikApi/".$route;
                if($url == $uri || rtrim($url, "/") == $uri){
                    call_user_func(str_replace("/", '', $route));
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