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
require "../config.php";
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
    //Ya existe en el archivo perfilpublico.php
    // case "getDescrUsuario":
    //     return "getDescrUsuarioPlaceholder";
    // break;
    case "updateDescrUsuario":
        updateDescrUsuario($link);
    break;
    case "seguirUsuario":
        seguirUsuario($link);
    break;
    case "noseguirUsuario":
        noseguirUsuario($link);
    break;
    case "getSeguidoresUsuario":
        ajaxSeguidoresUsu($_POST["cod_usu"],$link);
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
            if($nueva_des == null || $nueva_des == ''){
                $nueva_des = "Este usuario no ha escrito ninguna descripción.";
                echo $nueva_des;
            }else{
                echo $nueva_des;
            }
        }
    }else{
        echo "Falla de conexión.";
    }
}

function seguirUsuario($link){
    $cod_usu_seguir = $_POST["cod_usu"];
    if($cod_usu_seguir == $_SESSION["id"]){
        echo "No se qué hiciste, pero no puedes seguirte a ti mismo. ¿Me cuentas qué hiciste?";
    }else{
        //sdo = Usuario Seguido o A Seguir
        //sdr = Usuario que Sigue o Seguidor
        $sql = "INSERT INTO Seguidores (id_usu_sdo,id_usu_sdr) VALUES(?,?)";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"ii",$id_usu_seguir_param,$id_usu_sigue_param);
            $id_usu_seguir_param = $cod_usu_seguir; $id_usu_sigue_param = $_SESSION["id"];
            mysqli_stmt_execute($stmt);
            echo seguidoresUsuario($cod_usu_seguir,$link);
        }else{
            echo "Falla de conexión.";
        }
    }
}

function noseguirUsuario($link){
    $cod_usu_seguir = $_POST["cod_usu"];
    if($cod_usu_seguir == $_SESSION["id"]){
        echo "<script>alert(De verdad, ¿qué estás haciendo? Llegar a este mensaje debería ser imposible.);</script>";
    }else{
        //sdo = Usuario Seguido o A Seguir
        //sdr = Usuario que Sigue o Seguidor
        $sql = "DELETE FROM Seguidores WHERE id_usu_sdo = ? AND id_usu_sdr = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"ii",$id_usu_seguir_param,$id_usu_sigue_param);
            $id_usu_seguir_param = $cod_usu_seguir; $id_usu_sigue_param = $_SESSION["id"];
            mysqli_stmt_execute($stmt);
            echo seguidoresUsuario($cod_usu_seguir,$link);
        }else{
            echo "Falla de conexión";
        }
    }
}

//Para ser usada desde la página perfil público.
function seguidoresUsuario($cod_usu,$link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Seguidores WHERE id_usu_sdo = ".$cod_usu." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function ajaxSeguidoresUsu($cod_usu,$link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Seguidores WHERE id_usu_sdo = ".$cod_usu." ");
    $data = mysqli_fetch_assoc($result);
    echo $data["total"];
}


?>