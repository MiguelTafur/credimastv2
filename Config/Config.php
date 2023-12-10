<?php 
	
	//define("BASE_URL", "http://localhost/tienda_virtual/");
	//const BASE_URL = "https://credimast.com/credimastv2";
	const BASE_URL = "http://localhost/credimastv2";

	//Zona horaria
	date_default_timezone_set('Brazil/East');

	// const DB_HOST = "sldn297.piensasolutions.com";
	// const DB_NAME = "qahi319";
	// const DB_USER = "qahi319";
	// const DB_PASSWORD = "m1Guel03";

	const DB_HOST = "127.0.0.1:3307";
	const DB_NAME = "test";
	const DB_USER = "root";
	const DB_PASSWORD = "";

	const DB_CHARSET = "utf8";

	//Deliminadores decimal y millar Ej. 24,1989.00
	const SPD = ".";
	const SPM = ",";

	//Simbolo de moneda
	const SMONEY = "R$";

	//Datos envío de correo
	const NOMBRE_REMITENTE = "CREDIMAST";
	const EMAIL_REMITENTE = "no-reply@credimast.com";	
	const NOMBRE_EMPRESA = "CREDIMAST";
	const WEB_EMPRESA = "www.credimast.com";

	//Módulos
	const MDASHBOARD = 1;
	const MUSUARIOS = 2;
	const MCLIENTES = 3;
	const MPRESTAMOS = 4;
	const MRESUMEN = 5;

	//Roles
	//Roles
	const RCLIENTES = 7;
	const RADMINISTRADOR = 1;

 ?>