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
    case "updateDescrUsuario":
        updateDescrUsuario($link);
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

function updateDescrUsuario($link){
    $nueva_des = $_POST["nuevaDes"];
    $cod_usu = $_SESSION["id"];

    $sql = "UPDATE Login SET des_usu=? WHERE cod_usu=? ";

    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"si",$des_param,$cod_usu_param);
        $des_param = $nueva_des; 
        $cod_usu_param = $cod_usu;
        if(mysqli_stmt_execute($stmt)){
            //BUG: No se muestra este mensaje cuando la nueva descripción viene vacía.
            if($nueva_des == null || $nueva_des = ''){
                echo "Este usuario no ha escrito ninguna descripción.";
            }else{
                echo $nueva_des;
            }
        }else{
            echo "No se pudo actualizar la descripción. Help.";
        }
    }else{
        echo "Falla de conexión.";
    }
}

?>