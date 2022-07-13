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
         *? solicitud de repuestas del controlador para crear datos en cualquier tabla
            ***********************************************************************************/
            $response->postData($table, $_POST);
?>