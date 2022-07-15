<?php
    /************************
     *! Requerimientos.
     ************************/
        require_once "models/post.model.php";
        require_once "models/get.model.php";
    /******************************
     *todo Class Controller POST
     ******************************/
        class PostController{
            /****************************************
             ** Petición Post para crear datos.
             ****************************************/
                static public function postData($table,$data){
                    /***********************************************
                     *? Variables
                     ***********************************************/
                        $responde = new PostController();
                        $return = new PostController();
                    /*****************************************************
                     *? Llamado al modelo del postData.
                     *****************************************************/
                        $response = PostModel::postData($table,$data);
                    /***********************************************
                     *? Retorno del postData.
                     ***********************************************/
                        $return -> fncResponse($response,"postData",null);
                }
            /****************************************
             ** Petición Post para registra usuario.
             ****************************************/
                static public function postRegister($table, $data, $suffix){
                    /***********************************************
                     *? Variables
                     ***********************************************/
                        $return = new PostController();
                        $pass_crypt='$2a$07$use1pass2table3base5$';
                    /**************************************************
                     *? Encriptación de password
                     **************************************************/
                    if(isset($data["password_".$suffix]) && $data["password_".$suffix]!=null){
                        $crypt = crypt($data["password_".$suffix],$pass_crypt);
                        $data["password_".$suffix]=$crypt;
                    }
                    /*****************************************************
                     *? Llamado al modelo del postData.
                     *****************************************************/
                        $response = PostModel::postData($table,$data);
                    /***********************************************
                     *? Retorno del postRegister.
                     ***********************************************/
                        $return->fncResponse($response,"postData",null);
                }
            /****************************************
             ** Petición Post para login usuario.
             ****************************************/
                static public function postLogin($table, $data, $suffix){
                    /***********************************************
                     *? Variables
                     ***********************************************/
                        $return = new PostController();
                        $pass_crypt='$2a$07$use1pass2table3base5$';
                    /***********************************************
                     *? Validación del usuario existe en DB
                     ***********************************************/
                        $response=GetModel::getDataFilter($table, "*",
                        "email_".$suffix, $data["email_".$suffix], null, null, null, null);
                        /***********************************************
                         *? Validación del password existe en DB
                         ***********************************************/
                            if(!empty($response)){
                                $crypt=crypt($data["password_".$suffix],$pass_crypt);
                                if($response[0]->{"password_".$suffix} == $crypt){
                                    $return -> fncResponse(null,"postLogin", "pilas");
                                }else{
                                    $return -> fncResponse(null,"postLogin", "Wrong password");
                                }
                            }else{
                                $return -> fncResponse(null,"postLogin", "Wrong email");
                            }
                }
            /*******************************
             ** Respuesta del controlador
            *******************************/
                public function fncResponse($response, $method, $error){
                    if(!empty($response)){
                        $json = array(
                            "status" => 201,
                            "method" => $method,
                            "total" => count($response),
                            "detalle" => $response
                        );
                    }else{
                    if($error != null){
                        $json = array(
                            "status" => 400,
                            "error" => $error,
                            "method" => $method
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
        }
?>