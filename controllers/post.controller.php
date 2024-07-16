<?php 

use Firebase\JWT\JWT;

class PostController{

	// PETICION PARA TOMAR LOS NOMBRES DE LAS COLUMNAS
	static public function getColumnsData($table, $database){
		$response = PostModel::getColumnsData($table, $database);
		return $response;
	}

	// PETICION POST PARA CREAR DATOS
	public function postData($table, $data){

		$response = PostModel::postData($table, $data);

		$return = new PostController();
		$return->fncResponse($response, "postData", null);
	}

	// PETICION POST PARA REGISTRAR USUARIOS
	public function postRegister($table, $data){
	    if(isset($data['password_user']) && $data['password_user'] != null){
	        $crypt = crypt($data['password_user'], '$2a$07$swefaswgfgjhjerwsaxvsa$');

	        $data['password_user'] = $crypt;

	        $response = PostModel::postData($table, $data);

	        $return = new PostController();
	        $return->fncResponse($response, "postData", null);
	    } else {
	        $response = PostModel::postData($table, $data);

	        if($response == "The process was successful"){
	            $user = GetModel::getFilterData($table, "email_user", $data['email_user'], null, null, null, null, "*");

	            if(!empty($user)) {
	                // CREACION DE JWT
	                $time = time();
	                $key = "azsc34512fgh321jk234lzxcv568bnm12312q2w3e4r5t6y7u8i9o0p";
	                $token = array(
	                    "iat" => $time, // ESTO PARA EL TIEMPO DEL TOKEN
	                    "exp" => $time + (60*60*24), // TIEMPO DE EXPIRACION DEL TOKEN (24HRS)
	                    'data' => [
	                        "id" => $user[0]->id_user,
	                        "email" => $user[0]->email_user
	                    ]
	                );

	                $alg = 'HS256';
	                $jwt = JWT::encode($token, $key, $alg);

	                // ACTUALIZAMOS LA BASE DE DATOS CON EL TOKEN DEL USUARIO
	                $data = array(
	                    "token_user" => $jwt,
	                    "token_exp_user" => $token["exp"]
	                );

	                $update = PutModel::putData($table, $data, $user[0]->id_user, "id_user");

	                if($update == "The process was successful"){
	                    $return = new PostController();
	                    $return->fncResponse($response, "postData", null);
	                }
	            } 
	        } 
	    }
	}

	// PETICION POST PARA LOGUEAR USUARIOS
	public function postLogin($table, $data){
		$response = GetModel::getFilterData($table, "email_user", $data['email_user'], null, null, null, null, "*");

		if(!empty($response)){

			// ENCRIPTAMOS EL PASSWORD
			$crypt = crypt($data['password_user'], '$2a$07$swefaswgfgjhjerwsaxvsa$');

			if($response[0]->password_user == $crypt){

				// CREACION DE JWT
				$time = time();
				$key = "azsc34512fgh321jk234lzxcv568bnm12312q2w3e4r5t6y7u8i9o0p";
				$token = array(
					"iat" => $time, // ESTO PARA EL TIEMPO DEL TOKEN
					"exp" => $time + (60*60*24), // TIEMPO DE EXPIRACION DEL TOKEN (24HRS)
					'data' => [
						"id" => $response[0]->id_user,
						"email" => $response[0]->email_user
					]
				);

				// SOLUCIÓN DEL ERROR EN POSTMAN DE CARACTERES INSUFICIENTES
				$alg = 'HS256';
				$jwt = JWT::encode($token, $key, $alg);

				// ACTUALIZAMOS LA BASE DE DATOS CON EL TOKEN DEL USUARIO
				$data = array(
					"token_user" => $jwt,
					"token_exp_user" => $token["exp"]
				);

				$update = PutModel::putData($table, $data, $response[0]->id_user, "id_user");
				if($update == "The process was successful"){

					$response[0]->token_user = $jwt;
					$response[0]->token_exp_user = $token["exp"];

					$return = new PostController();
					$return -> fncResponse($response, "postLogin", null);
				}

			}else{
				$response = null;
				$return = new PostController();
				$return -> fncResponse($response, "postLogin", "Wrong password.");
			}

		}else{
			$response = null;
			$return = new PostController();
			$return -> fncResponse($response, "postLogin", "Wrong email.");
		}
	}


	// RESPUESTAS DEL CONTROLADOR
	public function fncResponse($response, $method, $error){
		if(!empty($response)){

			// QUITAMOS EL PASSWORD DE LA RESPUESTA
			if(isset($response[0]->password_user)){
				unset($response[0]->password_user);
			}

			$json = array(
				'status' => 200,
				"results" => $response
			);
		}else{

			if($error != null){
				$json = array(
					'status' => 400,
					"results" => $error
				);
			}else{
				$json = array(
					'status' => 404,
					"results" => "Not Found",
					"method" => $method
				);
			}
		}
		echo json_encode($json, http_response_code($json["status"]));
		return;
	}
}	