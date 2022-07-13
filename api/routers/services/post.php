<?php
    /****************************************
     *todo Petición POST.
    ****************************************/
        /********************************************
         *! Requerimientos.
        ********************************************/
            require_once "models/connection.php";
            require_once "controllers/post.controller.php";
        /********************************************
         *? Variables
         ********************************************/
            $suffix=$_GET["suffix"]?? "user";
            $columns=array();
            $response = new PostController();
            $return = new PostController();
        /********************************************
         *? Validar la tabla y columnas
         ********************************************/
            if(isset($_POST)){
                foreach(array_keys($_POST) as $key => $value){
                    array_push($columns, $value);
                }
            }
            if (empty(Connection::getColumnsData($table, $columns))){
                $return -> fncResponse(null,"POST");
                return;
            }
        /***********************************************************************************
         *? Petición POST para el registro de usuarios
         ***********************************************************************************/
            if(isset($_GET["register"]) && $_GET["status"]==true){
                $response->postRegister($table, $_POST, $suffix);
            }else{
                /***********************************************************************************
                 *? solicitud de repuestas del controlador para crear datos en cualquier tabla
                ***********************************************************************************/
                    $response->postData($table, $_POST);
            }
?>