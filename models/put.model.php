<?php

require_once "connection.php";

class PutModel{

	// PETICION PUT PARA EDITAR DATOS
	static public function putData($table, $data, $id, $nameId){

		$set = "";

		foreach ($data as $key => $value) {

			$set .= $key. " = :".$key.",";
			
		}
		$set = substr($set, 0, -1);

		$stmt = Connection::connect()->prepare("UPDATE $table SET $set WHERE $nameId = :$nameId");

		foreach ($data as $key => $value) {
			
			$stmt->bindParam(":".$key, $data[$key], PDO::PARAM_STR);
			
		}
		
		$stmt->bindParam(":".$nameId, $id, PDO::PARAM_INT);

		if($stmt->execute()){
			return "The process was successful";
		}else{
			return Connection::connect()->errorInfo();
		}
	}

}

