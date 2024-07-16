<?php

class RoutesController{

	// RUTA PRINCIPAL

	public function index(){
		include "routes/routes.php";
	}

	// NOMBRE DE LA BASE DE DATOS
	static public function database(){
		return "marketplace";
	}
}
