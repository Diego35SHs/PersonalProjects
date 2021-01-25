<?php 
session_start();    
//Tomar la funcion y el id del sueño que vienen de ajax.
$function = $_POST["accion"];
$id_com = $_POST["id_com"];
//Conectar a mySQL
$link = mysqli_connect('localhost','root','','anotasuenos');
//Revisar si el usuario ya ha dado me gusta al sueño.
if(cantidadLikesUsuario($id_com,$_SESSION["id"],$link) == 1){
    echo cantidadLikes($id_com,$link);
    return null;
}

if($function == "insertLike"){
    $sql = "INSERT INTO LikeDislikeCom(id_com,id_usu) VALUES(?,?)";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"ii",$id_com_param,$cod_usu_param);
        $id_com_param = $id_com; $cod_usu_param = $_SESSION["id"];
        mysqli_stmt_execute($stmt);
        echo cantidadLikes($id_com,$link);
        return null;
    }else{
        echo "Falla de conexión.";
    }
}

if($function == "deleteLike"){
    $sql = "DELETE FROM LikeDislikeCom WHERE id_com = ? AND id_usu = ?";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"ii",$id_com_param,$cod_usu_param);
        $id_com_param = $id_com; $cod_usu_param = $_SESSION["id"];
        mysqli_stmt_execute($stmt);
        $function = null;
        echo cantidadLikes($id_com,$link);
        return null;
    }else{
        echo "Falla de conexión.";
    }
}


function cantidadLikes($id_com,$link){
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislikeCom WHERE id_com = ".$id_com." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function cantidadLikesUsuario($id_com,$id_usu,$link){
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislikeCom WHERE id_com = ".$id_com." AND id_usu = ".$id_usu." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}


?>