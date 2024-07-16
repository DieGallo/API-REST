<?php

class GetController{
	// PETICIONES GET SIN FILTRO
	public function getData($table, $orderBy, $orderMode, $startAt, $endAt, $select){ 

		$response = GetModel::getData($table, $orderBy, $orderMode, $startAt, $endAt, $select);

		$return = new GetController();
		$return->fncResponse($response, "getData");
	}

	// PETICIONES GET CON FILTRO
	public function getFilterData($table, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt, $select){

		$response = GetModel::getFilterData($table, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt, $select);
		
		$return = new GetController();
		$return->fncResponse($response, "getFilterData");
	}

	// PETICIONES GET TABLAS RELACIONADAS SIN FILTRO
	public function getRelData($rel, $type, $orderBy, $orderMode, $startAt, $endAt, $select){
		$response = GetModel::getRelData($rel, $type, $orderBy, $orderMode, $startAt, $endAt, $select);

		$return = new GetController();
		$return->fncResponse($response, "getRelData");
	}


	// PETICIONES GET TABLAS RELACIONADAS CON FILTRO
	public function getRelFilterData($rel, $type, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt, $select){
		$response = GetModel::getRelFilterData($rel, $type, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt, $select);

		$return = new GetController();
		$return->fncResponse($response, "getRelFilterData");
	}

	// PETICIONES GET PARA EL BUSCADOR
	public function getSearchData($table, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt, $select){
		$response = GetModel::getSearchData($table, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt, $select);

		$return = new GetController();
		$return->fncResponse($response, "getSearchData");
	}

	// PETICIONES GET PARA EL BUSCADOR EN TABLAS RELACIONADAS CON FILTRO
	public function getSearchRelData($rel, $type, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt, $select){
		$response = GetModel::getSearchRelData($rel, $type, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt, $select);

		$return = new GetController();
		$return->fncResponse($response, "getSearchRelData");
	}

	// RESPUESTAS DEL CONTROLADOR
	public function fncResponse($response, $method){
		if(!empty($response)){
			$json = array(
				'status' => 200,
				'total' => count($response),
				"results" => $response
			);
		}else{
			$json = array(
				'status' => 404,
				"results" => "Not Found",
				"method" => $method
			);
		}

		echo json_encode($json, http_response_code($json["status"]));
		return;
	}
}