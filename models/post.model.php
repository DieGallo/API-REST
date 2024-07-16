<?php

require_once "connection.php";

class PostModel{

	// PETICION PARA TOMAR LOS NOMBRES DE LAS COLUMNAS
	static public function getColumnsData($table, $database){
		return Connection::connect()
            ->query("SELECT COLUMN_NAME AS item FROM information_schema.columns WHERE table_schema = '$database' AND table_name = '$table' ORDER BY ORDINAL_POSITION")
            ->fetchAll(PDO::FETCH_OBJ);
	}


	// PETICION POST PARA CREAR DATOS
	static public function postData($table, $data){

		// CODIGO PARA HACER DINAMICO EL QUERY
		$columns = "(";
		$params = "(";

		foreach ($data as $key => $value) {
			$columns .= $key.",";
			$params .= ":".$key.",";
		}

		$columns = substr($columns, 0, -1);
		$params = substr($params, 0, -1);

		$columns .= ")";
		$params .= ")";

		$stmt = Connection::connect()->prepare("INSERT INTO $table $columns VALUES $params");

		foreach ($data as $key => $value) {
			
			$stmt->bindParam(":".$key, $data[$key], PDO::PARAM_STR);
			
		}

		if($stmt->execute()){
			return "The process was successful";
		}else{
			return Connection::connect()->errorInfo();
		}
	}
}

