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
    case "eliminarCuentaUsuario":
        eliminarCuentaUsuario($link);
    break;
    case "eliminarCuentaMOD":

    break;
}

//Jerarquía
//Estas funciones eliminarán todas y cada una de las cosas en las que el usuario tenga algo que ver.
// 1.- 
function eliminarCuentaUsuario($link){
    try {
        buscarSuenosUsuarioUser($link);
        eliminarSuenos($link);
        buscarComentariosUser($link);
        eliminarSeguidos($link);
        eliminarCuenta($link);
    } catch (\Throwable $th) {
        echo "<script> alert('Error: ".$th." '); </script>";
    }
}

//PARA BORRAR COSAS DE CADA SUEÑO DEL USUARIO
//CREAR UNA FUNCIÓN QUE BORRE LAS COSAS Y USARLA EN OTRA FUNCIÓN QUE BUSQUE LOS SUEÑOS
//WHILE SELECT SUEÑOS (IDSUE) ELIMINAR X,Y,Z FIN WHILE
//El "User" Al final significa que se accede por métodos disponibles para el USUARIO, no MODERADORES
//PRIMERA
function buscarSuenosUsuarioUser($link){
    $cod_usu = $_SESSION["id"];
    $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE cod_usu = ".$cod_usu." ";
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) > 0) {
        $response["suenos"] = array();
        while ($row = mysqli_fetch_array($result)) {
            $id_sue = $row["id_sue"];
            eliminarComSue($link,$id_sue);
            eliminarLikeSue($link,$id_sue);
            eliminarLikeCom($link,$id_sue);
        }
    }else{
        echo "<script> alert('No se pudieron buscar y eliminar los sueños del usuario'); </script>";
    }
}

function buscarComentariosUser($link){
    $cod_usu = $_SESSION["id"];
    $query = "SELECT id_usu,id_sue,id_com FROM Comentario WHERE id_usu = ".$cod_usu." ";
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) > 0) {
        $response["suenos"] = array();
        while ($row = mysqli_fetch_array($result)) {
            $id_usu = $row["id_usu"];
            $id_com = $row["id_com"];
            //Eliminar los likes y dislikes de los comentarios
            elimLikesComsUsu($link,$id_com);
            elimComent($link,$cod_usu);
        }
    }else{
        echo "<script> alert('No se pudieron buscar y eliminar los sueños del usuario'); </script>";
    }
}

function elimLikesComsUsu($link,$id_com){
    $sql = "DELETE FROM LikeDislikeCom WHERE id_com = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$id_com_param);
        $id_com_param = $id_com;
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }else{
        echo "<script> alert('No se pudieron eliminar los me gusta de los comentarios del usuario'); </script>";
    }
}

function elimComent($link,$cod_usu){
    $sql = "DELETE FROM Comentario WHERE id_usu = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$id_com_param);
        $id_com_param = $cod_usu;
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }else{
        echo "<script> alert('No se pudieron eliminar los me gusta de los comentarios del usuario'); </script>";
    }
}

//NO LLAMAR NINGUNA DE LAS SIGUIENTES 3 POR SI SOLAS.
function eliminarComSue($link,$id_sue){
    $sql = "DELETE FROM Comentario WHERE id_sue = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$id_sue_param);
        $id_sue_param = $id_sue;
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }else{
        echo "<script> alert('No se pudieron eliminar los comentarios de los sueños'); </script>";
    }
}

function eliminarLikeSue($link,$id_sue){
    $sql = "DELETE FROM LikeDislike WHERE id_sue = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$id_sue_param);
        $id_sue_param = $id_sue;
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }else{
        echo "<script> alert('No se pudieron eliminar los me gusta de los sueños del usuario'); </script>";
    }
}

function eliminarLikeCom($link,$id_sue){
    $sql = "DELETE FROM LikeDislikeCom WHERE id_sue = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$id_sue_param);
        $id_sue_param = $id_sue;
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }else{
        echo "<script> alert('No se pudieron eliminar los likes de los comentarios del sueño del usuario.'); </script>";
    }
}

//Borrar todos los sueños de un usuario, el usuario se determina por la SESIÓN. No se puede cambiar
//por medio de inspeccionar elemento.
//SEGUNDA
function eliminarSuenos($link){
    $sql = "DELETE FROM Sueno WHERE cod_usu = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$cod_usu_param);
        $cod_usu_param = $_SESSION["id"];
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            echo "Fallo al eliminar. El usuario en sesión podría no coincidir con el sueño que se intenta borrar.";
        }
    }else{
        echo "<script> alert('No se pudieron eliminar los sueños del usuario'); </script>";
    }
}

//Eliminar comentarios del usuario y los likes correspondientes
//Eliminar seguidos
function eliminarSeguidos($link){
    $sql = "DELETE FROM Seguidores WHERE id_usu_sdo = ? OR id_usu_sdr = ?";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"ii",$id_usu_sdo_param,$id_usu_sdr_param);
        $id_usu_sdo_param = $_SESSION["id"]; $id_usu_sdr_param = $_SESSION["id"];
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }
}

function eliminarCuenta($link){
    $sql = "DELETE FROM Login WHERE cod_usu = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$cod_usu_param);
        $cod_usu_param = $_SESSION["id"];
        if(mysqli_stmt_execute($stmt)){
            header("location: ../Registro/logout.php");
        }else{
            echo "<script> alert('Fallo al intentar eliminar cuenta'); </script>";
        }
    }else{
        echo "<script> alert('No se pudo eliminar la cuenta'); </script>";
    }
}

//=====================================================================

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