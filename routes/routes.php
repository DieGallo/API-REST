<?php 

$routesArray = explode("/",$_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);

// CUANDO NO SE HACE NINGUNA PETICION A LA API
if(count($routesArray) == 0){
	$json = array(
		'status' => 404,
		"results" => "Not Found"
	);

	echo json_encode($json, http_response_code($json["status"]));
	return;
}else{

	// PETICIONES GET
	if(count($routesArray) == 1 && 
		isset($_SERVER['REQUEST_METHOD']) &&
		$_SERVER['REQUEST_METHOD'] == "GET"){

		// PETICIONES CON FILTRO

		if(isset($_GET['linkTo']) && isset($_GET['equalTo']) && 
			!isset($_GET["rel"]) && !isset($_GET["type"])){

			// PREGUNTAMOS SI VIENEN VARIABLES DE ORDEN
			if(isset($_GET['orderBy']) && isset($_GET['orderMode'])){
				$orderBy = $_GET['orderBy'];
				$orderMode = $_GET['orderMode'];
			}else{
				$orderBy = null;
				$orderMode = null;
			}

			// PREGUNTAMOS SI VIENEN VARIABLES DE LIMITE
			if(isset($_GET['startAt']) && isset($_GET['endAt'])){
				$startAt = $_GET['startAt'];
				$endAt = $_GET['endAt'];
			}else{
				$startAt = null;
				$endAt = null;
			}

			$response = new GetController();
			$response->getFilterData(explode("?",$routesArray[1])[0], $_GET['linkTo'], $_GET['equalTo'], $orderBy, $orderMode, $startAt, $endAt, $_GET['select']);

		// PETICIONES GET ENTRE TABLAS RELACIONADAS SIN FILTRO 
		}else if(isset($_GET["rel"]) && isset($_GET["type"]) && explode("?",$routesArray[1])[0] == "relations" && 
			!isset($_GET['linkTo']) && !isset($_GET['equalTo'])){

			// PREGUNTAMOS SI VIENEN VARIABLES DE ORDEN
			if(isset($_GET['orderBy']) && isset($_GET['orderMode'])){
				$orderBy = $_GET['orderBy'];
				$orderMode = $_GET['orderMode'];
			}else{
				$orderBy = null;
				$orderMode = null;
			}

			// PREGUNTAMOS SI VIENEN VARIABLES DE LIMITE
			if(isset($_GET['startAt']) && isset($_GET['endAt'])){
				$startAt = $_GET['startAt'];
				$endAt = $_GET['endAt'];
			}else{
				$startAt = null;
				$endAt = null;
			}

			$response = new GetController();
			$response -> getRelData($_GET["rel"], $_GET["type"], $orderBy, $orderMode, $startAt, $endAt, $_GET['select']);

		// PETICIONES GET ENTRE TABLAS RELACIONADAS CON FILTRO
		}else if(isset($_GET["rel"]) && isset($_GET["type"]) && explode("?",$routesArray[1])[0] == "relations" && 
			isset($_GET['linkTo']) && isset($_GET['equalTo'])){

			// PREGUNTAMOS SI VIENEN VARIABLES DE ORDEN
			if(isset($_GET['orderBy']) && isset($_GET['orderMode'])){
				$orderBy = $_GET['orderBy'];
				$orderMode = $_GET['orderMode'];
			}else{
				$orderBy = null;
				$orderMode = null;
			}

			// PREGUNTAMOS SI VIENEN VARIABLES DE LIMITE
			if(isset($_GET['startAt']) && isset($_GET['endAt'])){
				$startAt = $_GET['startAt'];
				$endAt = $_GET['endAt'];
			}else{
				$startAt = null;
				$endAt = null;
			}

			$response = new GetController();
			$response->getRelFilterData($_GET["rel"], $_GET["type"], $_GET['linkTo'], $_GET['equalTo'], $orderBy, $orderMode, $startAt, $endAt, $_GET['select']);

		// PETICIONES GET PARA EL BUSCADOR
		}else if(isset($_GET['linkTo']) && isset($_GET['search'])){

			// PREGUNTAMOS SI VIENEN VARIABLES DE ORDEN
			if(isset($_GET['orderBy']) && isset($_GET['orderMode'])){
				$orderBy = $_GET['orderBy'];
				$orderMode = $_GET['orderMode'];
			}else{
				$orderBy = null;
				$orderMode = null;
			}

			// PREGUNTAMOS SI VIENEN VARIABLES DE LIMITE
			if(isset($_GET['startAt']) && isset($_GET['endAt'])){
				$startAt = $_GET['startAt'];
				$endAt = $_GET['endAt'];
			}else{
				$startAt = null;
				$endAt = null;
			}

			if(explode("?", $routesArray[1])[0] == "relations" && isset($_GET['rel']) && isset($_GET['type'])){
				$response = new GetController();
				$response -> getSearchRelData($_GET["rel"], $_GET["type"], $_GET['linkTo'], $_GET['search'], $orderBy, $orderMode, $startAt, $endAt, $_GET['select']);
			}else{
				$response = new GetController();
				$response -> getSearchData(explode("?",$routesArray[1])[0], $_GET['linkTo'], $_GET['search'], $orderBy, $orderMode, $startAt, $endAt, $_GET['select']);
			}

		// PETICIONES SIN FILTRO
		}else{
			
			// PREGUNTAMOS SI VIENEN VARIABLES DE ORDEN
			if(isset($_GET['orderBy']) && isset($_GET['orderMode'])){
				$orderBy = $_GET['orderBy'];
				$orderMode = $_GET['orderMode'];
			}else{
				$orderBy = null;
				$orderMode = null;
			}

			// PREGUNTAMOS SI VIENEN VARIABLES DE LIMITE
			if(isset($_GET['startAt']) && isset($_GET['endAt'])){
				$startAt = $_GET['startAt'];
				$endAt = $_GET['endAt'];
			}else{
				$startAt = null;
				$endAt = null;
			}

			$response = new GetController();
			$response -> getData(explode("?", $routesArray[1])[0], $orderBy, $orderMode, $startAt, $endAt, $_GET['select']);
		}
	}

	// PETICIONES POST
	if(count($routesArray) == 1 &&
	   isset($_SERVER["REQUEST_METHOD"]) &&
	   $_SERVER["REQUEST_METHOD"] == "POST"){

		/*=============================================
		Traemos el listado de columnas de la tabla a cambiar
		=============================================*/

		$columns = array();
		
		$database = RoutesController::database();

		$response = PostController::getColumnsData(explode("?", $routesArray[1])[0], $database);
		
		foreach ($response as $key => $value) {

			array_push($columns, $value->item);
	
		}

		/*=============================================
		Quitamos el primer y ultimo indice
		=============================================*/
		array_shift($columns);
		array_pop($columns);

		/*=============================================
		Recibimos las valores POST
		=============================================*/

		if(isset($_POST)){
			
			/*=============================================
			Validamos que las variables de los campos PUT coincidan con los nombres de columnas de la base de datos
			=============================================*/

			$count = 0;

			foreach (array_keys($_POST) as $key => $value) {
				
				$count = array_search($value, $columns);		
				
			
			}

			if($count > 0){

				/*=============================================
				Solicitamos respuesta del controlador para registar usuarios
				=============================================*/	

				if(isset($_GET["register"]) && $_GET["register"] == true){

					$response = new PostController();
					$response -> postRegister(explode("?", $routesArray[1])[0], $_POST);

				/*=============================================
				Solicitamos respuesta del controlador para el ingreso de usuarios
				=============================================*/	

				}else if(isset($_GET["login"]) && $_GET["login"] == true){

					$response = new PostController();
					$response -> postLogin(explode("?", $routesArray[1])[0], $_POST);

				/*=============================================
				Validamos el token de autenticación
				=============================================*/	

				}else if(isset($_GET["token"])){

					/*=============================================
					Agregamos excepción para crear sin autorización
					=============================================*/	

					if($_GET["token"] == "no"){

						if(isset($_GET["except"])){

							$num = 0;

							foreach ($columns as $key => $value) {

								$num++;
								
								/*=============================================
								Buscamos coincidencia con la excepción
								=============================================*/

								if($value == $_GET["except"]){

									/*=============================================
									Solicitamos respuesta del controlador para crear datos en cualquier tabla
									=============================================*/	

									$response = new PostController();
									$response -> postData(explode("?", $routesArray[1])[0], $_POST);

									return;
								}	
							}

							/*=============================================
							Cuando no encuentra coincidencia
							=============================================*/

							if($num == count($columns)){

								$json = array(
								 	'status' => 400,
								 	'results' => "The exception does not match the database"
								);

								echo json_encode($json, http_response_code($json["status"]));

								return;

							}		

						}else{

							/*=============================================
							Cuando no envian excepción
							=============================================*/		

							$json = array(
							 	'status' => 400,
							 	'results' => "There is no exception"
							);

							echo json_encode($json, http_response_code($json["status"]));

							return;
							
						}

					}else{	
			
						/*=============================================
						Traemos el usuario de acuerdo al token
						=============================================*/

						$user = GetModel::getFilterData("users", "token_user", $_GET["token"], null, null, null, null, "token_exp_user");
						
						if(!empty($user)){

							/*=============================================
							Validamos que el token no haya expirado
							=============================================*/	

							$time = time();

							if($user[0]->token_exp_user > $time){

								/*=============================================
								Solicitamos respuesta del controlador para crear datos en cualquier tabla
								=============================================*/	

								$response = new PostController();
								$response -> postData(explode("?", $routesArray[1])[0], $_POST);

							}else{

								$json = array(
								 	'status' => 303,
								 	'results' => "Error: The token has expired"
								);

								echo json_encode($json, http_response_code($json["status"]));

								return;

							}

						}else{


							$json = array(
							 	'status' => 400,
							 	'results' => "Error: The user is not authorized"
							);

							echo json_encode($json, http_response_code($json["status"]));

							return;

						}

					}


				}else{

					$json = array(
					 	'status' => 400,
					 	'results' => "Error: Authorization required"
					);

					echo json_encode($json, http_response_code($json["status"]));

					return;	

				}

			}else{

				$json = array(
				 	'status' => 400,
				 	'results' => "Error: Fields in the form do not match the database"
				);

				echo json_encode($json, http_response_code($json["status"]));

				return;

			}

		}

	}

	if(count($routesArray) == 1 &&
	   isset($_SERVER["REQUEST_METHOD"]) &&
	   $_SERVER["REQUEST_METHOD"] == "PUT"){

	   	/*=============================================
		Preguntamos si viene ID
		=============================================*/

		if(isset($_GET["id"]) && isset($_GET["nameId"])){

			/*=============================================
			Validamos que exista el ID
			=============================================*/
			$table = explode("?", $routesArray[1])[0];
			$linkTo = $_GET["nameId"];
			$equalTo = $_GET["id"];
			$orderBy = null;
			$orderMode = null;
			$startAt = null;
			$endAt = null;
			$select = $_GET["nameId"];

			$response = PutController::getFilterData($table, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt, $select);
			
			if($response){

				/*=============================================
				Capturamos los datos del formulario
				=============================================*/

				$data = array();
				parse_str(file_get_contents('php://input'), $data);

				/*=============================================
				Traemos el listado de columnas de la tabla a cambiar
				=============================================*/

				$columns = array();
				
				$database = RoutesController::database();

				$response = PostController::getColumnsData(explode("?", $routesArray[1])[0], $database);
				
				foreach ($response as $key => $value) {

					array_push($columns, $value->item);
			
				}

				/*=============================================
				Quitamos el primer y ultimo indice
				=============================================*/
				array_shift($columns);
				array_pop($columns);
				array_pop($columns);

				/*=============================================
				Validamos que las variables de los campos PUT coincidan con los nombres de columnas de la base de datos
				=============================================*/

				$count = -1;

				foreach (array_keys($data) as $key => $value) {
					
					$count = array_search($value, $columns);

				}
				
				if($count > -1){

					if(isset($_GET["token"])){

						/*=============================================
						Agregamos excepción para actualizar sin autorización
						=============================================*/	

						if($_GET["token"] == "no"){

							if(isset($_GET["except"])){

								$num = 0;

								foreach ($columns as $key => $value) {

									$num++;
									
									/*=============================================
									Buscamos coincidencia con la excepción
									=============================================*/

									if($value == $_GET["except"]){

										/*=============================================
										Solicitamos respuesta del controlador para editar cualquier tabla
										=============================================*/

										$response = new PutController();
										$response -> putData(explode("?", $routesArray[1])[0], $data, $_GET["id"], $_GET["nameId"]);

										return;
									}	
								}

								/*=============================================
								Cuando no encuentra coincidencia
								=============================================*/

								if($num == count($columns)){

									$json = array(
									 	'status' => 400,
									 	'results' => "The exception does not match the database"
									);

									echo json_encode($json, http_response_code($json["status"]));

									return;

								}		

							}else{

								/*=============================================
								Cuando no envian excepción
								=============================================*/		

								$json = array(
								 	'status' => 400,
								 	'results' => "There is no exception"
								);

								echo json_encode($json, http_response_code($json["status"]));

								return;

							}

						}else{	

							/*=============================================
							Traemos el usuario de acuerdo al token
							=============================================*/

							$user = GetModel::getFilterData("users", "token_user", $_GET["token"], null, null, null, null, "token_exp_user");
							
							if(!empty($user)){

								/*=============================================
								Validamos que el token no haya expirado
								=============================================*/	

								$time = time();

								if($user[0]->token_exp_user > $time){

									/*=============================================
									Solicitamos respuesta del controlador para editar cualquier tabla
									=============================================*/

									$response = new PutController();
									$response -> putData(explode("?", $routesArray[1])[0], $data, $_GET["id"], $_GET["nameId"]);

								}else{

									$json = array(
									 	'status' => 303,
									 	'results' => "Error: The token has expired"
									);

									echo json_encode($json, http_response_code($json["status"]));

									return;

								}

							}else{


								$json = array(
								 	'status' => 400,
								 	'results' => "Error: The user is not authorized"
								);

								echo json_encode($json, http_response_code($json["status"]));

								return;

							}

						}

					}else{

						$json = array(
						 	'status' => 400,
						 	'results' => "Error: Authorization required"
						);

						echo json_encode($json, http_response_code($json["status"]));

						return;	

					}			

				}else{

					$json = array(
					 	'status' => 400,
					 	'results' => "Error: Fields in the form do not match the database"
					);

					echo json_encode($json, http_response_code($json["status"]));

					return;

				}

			}else{

				$json = array(
				 	'status' => 400,
				 	'results' => "Error: The id is not found in the database"
				);

				echo json_encode($json, http_response_code($json["status"]));

				return;

			}
	
		}	

	}

	// PETICIONES DELETE
	if(count($routesArray) == 1 && 
		isset($_SERVER['REQUEST_METHOD']) &&
		$_SERVER['REQUEST_METHOD'] == "DELETE"){

		if(isset($_GET['id']) && isset($_GET['nameId'])){
			$table = explode("?", $routesArray[1])[0];
			$linkTo = $_GET['nameId'];
			$equalTo = $_GET['id'];
			$orderBy = null;
			$orderMode = null;
			$startAt = null;
			$endAt = null;
			$select = $_GET['nameId'];

			$response = PutController::getFilterData($table, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt, $select);

			if($response){
				// VALIDAMOS EL TOKEN DE VERIFICACION
				if(isset($_GET['token'])){
					// TRAEMOS EL USUARIO DE ACUERDO AL TOKEN
					$user = GetModel::getFilterData("users", "token_user", $_GET['token'], null, null, null, null, "token_exp_user");

					if(!empty($user)){
						// VALIDAMOS QUE EL TOKEN NO HAYA EXPIRADO
						$time = time();
						if($user[0]->token_exp_user > $time){
							// SOLICITAMOS RESPUESTA DEL CONTROLADOR
							$response = new DeleteController();
							$response -> deleteData(explode("?", $routesArray[1])[0], $_GET['id'], $_GET['nameId']);
						}else{
							$json = array(
								'status' => 303,
								"results" => "Error: The token has expired"
							);

							echo json_encode($json, http_response_code($json['status']));
							return;
						}
					}else{
						$json = array(
							'status' => 400,
							"results" => "Error: This user is not authorized"
						);

						echo json_encode($json, http_response_code($json['status']));
						return;
					}
				}else{
					$json = array(
						'status' => 400,
						"results" => "Error: Authorization required"
					);

					echo json_encode($json, http_response_code($json['status']));
					return;
				}

			}else{
				$json = array(
					'status' => 400,
					"results" => "Error: The id is not found in the database"
				);

				echo json_encode($json, http_response_code($json['status']));
				return;
			}
		}
	}
}