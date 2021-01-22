<?php 
//Inicializar sesión
session_start();

//Desasignar todas las variables de la sesión
$_SESSION = array();

//Destruir la sesión
session_destroy();

//Redireccionar a página de login
header("location: ..\index.php");
exit;

?>