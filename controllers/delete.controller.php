<?php

class DeleteController{

	// DELETE PARA BORRAR DATOS
	public function deleteData($table, $id, $nameId){
		$response = DeleteModel::deleteData($table, $id, $nameId);

		$return = new DeleteController();
		$return->fncResponse($response, "deleteData");
	}

	// RESPUESTAS DEL CONTROLADOR
	public function fncResponse($response, $method){
		if(!empty($response)){
			$json = array(
				'status' => 200,
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