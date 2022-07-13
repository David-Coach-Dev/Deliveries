<?php
    /****************************************
     *todo Petición PUT.
    ****************************************/
        /********************************************
         *! Requerimientos.
        ********************************************/
            require_once "models/connection.php";
            require_once "controllers/put.controller.php";
        /********************************************
         *? Variables
        ********************************************/
            $data=array();
            $columns=array();
            $id=$_GET["id"]?? null;
            $nameId=$_GET["nameId"]?? null;
            $response = new PutController();
            $return = new PutController();
        /********************************************
         *? Validando variables de PUt
         ********************************************/
            if(isset($_GET["id"]) && isset($_GET["nameId"])){
                /********************************************
                 *? Capturar los datos del formulario
                 ********************************************/
                    parse_str(file_get_contents('php://input'), $data);
                /********************************************
                 *? Validar la tabla y columnas
                 ********************************************/
                    foreach(array_keys($data) as $key => $value){
                        array_push($columns,$value);
                    }
                    array_push($columns,$_GET["nameId"]);
                    $columns=array_unique($columns);
                    if (empty(Connection::getColumnsData($table, $columns))){
                        $return -> fncResponse(null,"PUT");
                        return;
                    }
                /***********************************************************************************
                 *? solicitud de repuestas del controlador para editar datos en cualquier tabla
                 ***********************************************************************************/
                    $response->putData($table, $data, $id, $nameId);
                }
?>