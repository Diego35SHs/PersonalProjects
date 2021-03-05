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
        eliminarCuentaUsuarioMOD($link);
    break;
    case "asignarModerador":
        asignarModerador($link);
    break;
    case "desasignarModerador":
        desasignarModerador($link);
    break;
    case "listarUsuarios":
        prepararQueryListUsu($link);
    break;
    case "procCambioNombre":
        if(comprobarNombre($link) == 1){
            echo 0;
            return null;
        }else{
            if(updateNombre($link) == 1){
                echo 1;
            }
        }
    break;
    case "resetDescUsu":
        resetDesc($link);
    break;
}

function resetDesc($link){
    $id_usu = $_POST["id_usu"];
    $nueva_des = "El moderador ".$_SESSION["username"]." restableció la descripción de este usuario.";
    $sql = "UPDATE Login SET des_usu=? WHERE cod_usu=? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"si",$des_param,$cod_usu_param);
        $des_param = $nueva_des; 
        $cod_usu_param = $id_usu;
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }else{
        echo "Falla de conexión.";
    }
}

function comprobarNombre($link){
    $sql = "SELECT cod_usu FROM Login WHERE nom_usu = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt,"s",$param_username);
            $param_username = trim($_POST["nuevoNombre"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    //Nombre de usuario en uso.
                    return 1;
                }else{
                    //Nombre de usuario libre.
                    return 0;
                }
            }else{
                echo "Algo salió mal, inténtelo de nuevo más tarde";
            }
            mysqli_stmt_close($stmt);
        }
}

function updateNombre($link){
    $nuevo_nom = $_POST["nuevoNombre"];
    $cod_usu = $_SESSION["id"];
    $sql = "UPDATE Login SET nom_usu=? WHERE cod_usu=? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"si",$nom_param,$cod_usu_param);
        $nom_param = $nuevo_nom; 
        $cod_usu_param = $cod_usu;
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }else{
        echo "Falla de conexión.";
    }
}

function prepararQueryListUsu($link){
    $opcion = $_POST["opcion"];
    switch($opcion){
        case "listarEspecial":
            $query = "SELECT cod_usu,nom_usu,fec_usu FROM Login";
            mostrarUsuariosEspecial($link,$query);
        break;
        case "listarUser":
            $query = "SELECT cod_usu,nom_usu,fec_usu FROM Login";
            mostrarUsuariosGeneric($link,$query);
        break;
    }
    return null;
}

function mostrarUsuariosEspecial($link,$query){
    $response = array();
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) > 0) {
        $response["usuarios"] = array();
        echo "<table class='table table-lg table-light table-hover border-info rounded'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th scope='col'>Código</th>";
        echo "<th scope='col'>Nombre de usuario</th>";
        echo "<th scope='col'>Fecha de registro</th>";
        echo "<th scope='col'>Acción</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = mysqli_fetch_array($result)) {
            $temp = array();
            echo "<tr>";
                echo "<td> ".$row["cod_usu"]." </td>";
                echo "<td> ".$row["nom_usu"]." </td>";
                echo "<td> ".$row["fec_usu"]." </td>";
                //TODO: Acciones para estos botones.
                echo "<td>";
                if($row["cod_usu"] != 1){
                    if($row["cod_usu"] == $_SESSION["id"]){
                        echo "Usuario en sesión";
                    }else{
                        echo "<a  class='borrarUser btn btn-danger another-element' id=' ".$row["cod_usu"]." ' href='javascript:void(0);'><i id='eliIcon".$row["cod_usu"]."' class='eliIcon fa fa-trash'></i></a> ";
                    } 
                }
                if($_SESSION["id"] == 1){
                    if(checkModCustom($link,$row["cod_usu"]) == 1){
                        if($row["cod_usu"] == $_SESSION["id"]){
                            echo "No disponible.";   
                        }else{
                            echo "<a  class='desasigMod btn btn-danger another-element' id=' ".$row["cod_usu"]." ' href='javascript:void(0);'>Desasignar moderador</a>";
                        }
                    }else{
                        if($row["cod_usu"] == $_SESSION["id"]){
                            echo "No disponible";
                        }else{
                            echo "<a  class='asigMod btn btn-warning another-element' id=' ".$row["cod_usu"]." ' href='javascript:void(0);'>Asignar moderador</a>";
                        }
                    }
                }
                echo "</td>";
            echo "</tr>";
            array_push($response["usuarios"], $temp);
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
        echo "<p>No se encontró ningún registro.</p>";
        echo "</div> <br>";
    }
}

//Revisar si el usuario tiene permisos de moderación.
function checkModCustom($link,$cod_usu){
    $sql = "SELECT id_mod FROM modd WHERE id_usu = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_codusu);
        $param_codusu = $cod_usu;
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $registro);
                if (mysqli_stmt_fetch($stmt)) {
                    //Es parte del staff
                    return 1;
                }
            } else {
                //No es parte del staff
                return 0;
            }
        }
    }
}

//=======================ASIGNACIÓN Y DESASIGNACION DE MODERADORES==========================
function asignarModerador($link){
    $cod_usu = $_POST["cod_usu"];
    $sql = "INSERT INTO modd (id_usu) VALUES(?)";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$id_usu_param);
        $id_usu_param = $cod_usu;
        mysqli_stmt_execute($stmt);
        return 1;
    }else{
        echo "Falla de conexión.";
    }
}

function desasignarModerador($link){
    $cod_usu = $_POST["cod_usu"];
    $sql = "DELETE FROM modd WHERE id_usu = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$cod_usu_param);
        $cod_usu_param = $cod_usu;
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }else{
        echo "<script> alert('No se pudieron eliminar los me gusta de los comentarios del usuario'); </script>";
    }
}
//==========================================================================================

function mostrarUsuariosGeneric($link,$query){
    $response = array();
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) > 0) {
        $response["usuarios"] = array();
        while ($row = mysqli_fetch_array($result)) {
            $temp = array();
            echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
            echo "<span>ID:".$row["cod_usu"]."</span> &nbsp;";
            echo "<label>";
            echo "<a href='../CRUDs/perfilPublico.php?cod_usu=".$row["cod_usu"]."'>".$row["nom_usu"]."</a>";
            echo "</label>";
            echo "<span>&nbsp;&nbsp;Registrado: ";
            echo $row["fec_usu"];
            echo "&nbsp;&nbsp;</span>";
            echo "</div> </br>";
            array_push($response["usuarios"], $temp);
        }
    } else {
        echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
        echo "<p>No se encontró ningún registro.</p>";
        echo "</div> <br>";
    }
}

//===============================ELIMINACIÓN DE USUARIO A SU PROPIA CUENTA=====================================
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

//===================================================================================================

//===============================ELIMINACIÓN DE USUARIO (MODERADORES)=====================================
//Jerarquía
//Estas funciones eliminarán todas y cada una de las cosas en las que el usuario tenga algo que ver.
// 1.- 
function eliminarCuentaUsuarioMOD($link){
    try {
        buscarSuenosUsuarioUserMOD($link);
        eliminarSuenosMOD($link);
        buscarComentariosUserMOD($link);
        eliminarSeguidosMOD($link);
        eliminarCuentaMOD($link);
    } catch (\Throwable $th) {
        echo "<script> alert('Error: ".$th." '); </script>";
    }
}
//PARA BORRAR COSAS DE CADA SUEÑO DEL USUARIO
//CREAR UNA FUNCIÓN QUE BORRE LAS COSAS Y USARLA EN OTRA FUNCIÓN QUE BUSQUE LOS SUEÑOS
//WHILE SELECT SUEÑOS (IDSUE) ELIMINAR X,Y,Z FIN WHILE
//El "User" Al final significa que se accede por métodos disponibles para el USUARIO, no MODERADORES
//PRIMERA
function buscarSuenosUsuarioUserMOD($link){
    $cod_usu = $_POST["cod_usu"];
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

function buscarComentariosUserMOD($link){
    $cod_usu = $_POST["cod_usu"];
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

//Borrar todos los sueños de un usuario, el usuario se determina por la SESIÓN. No se puede cambiar
//por medio de inspeccionar elemento.
//SEGUNDA
function eliminarSuenosMOD($link){
    $sql = "DELETE FROM Sueno WHERE cod_usu = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$cod_usu_param);
        $cod_usu_param = $_POST["cod_usu"];
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
function eliminarSeguidosMOD($link){
    $sql = "DELETE FROM Seguidores WHERE id_usu_sdo = ? OR id_usu_sdr = ?";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"ii",$id_usu_sdo_param,$id_usu_sdr_param);
        $id_usu_sdo_param = $_POST["cod_usu"]; $id_usu_sdr_param = $_POST["cod_usu"];;
        if(mysqli_stmt_execute($stmt)){
            return 1;
        }else{
            return 0;
        }
    }
}

function eliminarCuentaMOD($link){
    $sql = "DELETE FROM Login WHERE cod_usu = ? ";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$cod_usu_param);
        $cod_usu_param = $_POST["cod_usu"];
        if(mysqli_stmt_execute($stmt)){
            // header("location: ../Registro/logout.php");
        }else{
            echo "<script> alert('Fallo al intentar eliminar cuenta'); </script>";
        }
    }else{
        echo "<script> alert('No se pudo eliminar la cuenta'); </script>";
    }
}

//===================================================================================================

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