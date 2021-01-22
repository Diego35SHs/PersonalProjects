<?php 
//Definir las variables que se van a usar para conectarse 
//Usando el método "mysqli_connect".
//Estas vienen así por defecto al instalar MySQL.
//(a excepción de DB_NAME, que lleva el nombre de la BD de este proyecto)
define('DB_SERVER', 'localhost');
define('DB_USERNAME','root');
define('DB_PASSWORD','');
define('DB_NAME','anotasuenos');

//Inicializar conexión con los parámetros definidos.
$link = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);


//Dar mensaje de error si la conexión falla.
if($link === false){
    die("Error: No se pudo conectar." . mysqli_connect_error());
}


?>