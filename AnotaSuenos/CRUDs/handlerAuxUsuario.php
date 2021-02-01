<?php 
session_start();
//Objetivo de este archivo:
//Servir como "librería" para conseguir cosas del usuario por medio de la sesión $_SESSION["id"]
//Se deberá acceder idealmente por medio de Ajax ingresando por metodo POST el nombre de la función
//y los parámetros necesarios.

//TODO: getFotoPerUsuario(){}
//TODO: getCantSueUsuario(){}
//TODO: getExpUsuario(){}
//TODO: getExpFaltUsuario(){}
//TODO: getNivelUsuario(){}
//TODO: getDescrUsuario(){}

$funcion = $_POST["function"];
$id_usu = $_SESSION["id"];
$link = mysqli_connect('localhost','root','','anotasuenos');
switch($funcion){
    case "getFotoPerUsuario":
        return "FotoPerfilPlaceHolder";
    break;
    case "getCantSueUsuario":
        echo "Sueños publicados por ti: ".cantSueUsuario($id_usu,$link);
    break;
    case "getExpUsuario":
        return "ExpUsuarioPlaceholder";
    break;
    case "getExpFaltUsuario":
        return "ExpFaltUsuarioPlaceholder";
    break;
    case "getNivelUsuario":
        return "NivelUsuarioPlaceholder";
    break;
    case "getDescrUsuario":
        return "getDescrUsuarioPlaceholder";
    break;
}

//Función cantSueUsuario
//Input: Código de usuario y link de conexión.
//Ouput: Cantidad de sueños publicados por el usuario
//TODO: Contar sueños privados en esta función.
function cantSueUsuario($id_usu,$link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno WHERE cod_usu = ".$id_usu." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASS</title>
</head>
<body>
    <!-- <a href="../home.php">Volver a la página principal</a> -->
</body>
</html>