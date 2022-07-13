<?php
    /************************
     *! Requerimientos.
     ************************/
        require_once "models/post.model.php";
    /******************************
     *todo Class Controller POST
     ******************************/
        class PostController{
            /****************************************
             ** Petición Post para crear datos.
             ****************************************/
                static public function postData($table,$data){
                    $response = PostModel::postData($table,$data);
                    $return = new PostController();
                    $return -> fncResponse($response,"postData");
                }
            /****************************************
             ** Petición Post para registra usuario.
             ****************************************/
                static public function postRegister($table, $data, $suffix){
                    if(isset($data["password_".$suffix]) && $data["password_".$suffix]!=null){
                        $crypt=crypt($data["password_".$suffix], '$2a$07$1a2b3c4d5e6f7g8h9i$');
                        $data["password_".$suffix]=$crypt;
                    }
                    $response = PostModel::postData($table,$data);
                    $return = new PostController();
                    $return -> fncResponse($response,"postData");
                }
            /*******************************
             ** Respuesta del controlador
            *******************************/
                public function fncResponse($response,$method){
                    if(!empty($response)){
                    $json = array(
                        "status" => 201,
                        "method" => $method,
                        "total" => count($response),
                        "detalle" => $response
                    );
                    }else{
                    $json = array(
                        "status" => 404,
                        "detalle" => "not found...",
                        "method" => $method
                    );
                    }
                    echo json_encode($json, http_response_code($json["status"]));
                }
        }
?>