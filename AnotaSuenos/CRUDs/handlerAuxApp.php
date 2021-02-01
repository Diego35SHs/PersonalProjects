<?php 
session_start();
require "../config.php";
$funcion = $_POST["funcion"];

switch($funcion){
    //Cant Sueños usuario en handlerUsuario
    case "getCantComentUsu":
        echo "Comentarios publicados por ti: ".cantidadComentUsu($link);
    break;
    case "getCantUsuarios":
        echo "Cantidad de usuarios: ".cantidadUsuarios($link);
    break;
    case "getCantSuenosTotal":
        echo "Cantidad total de sueños: ".cantidadSuenosTotal($link);
    break;
    case "getCantSuenosPublic":
        echo "Sueños públicos: ".cantidadSuenosPublic($link);
    break;
    case "getCantSuenosPriv":
        echo "Sueños privados: ".cantidadSuenosPrivados($link);
    break;
    case "getCantSuenosM18":
        echo "Sueños +18: ".cantidadSuenosM18($link);
    break;
    case "getCantLikesSuenos":
        echo "Me gusta dados a sueños: ".cantidadLikesSuenos($link);
    break;
    case "getCantComent":
        echo "Cantidad total de comentarios: ".cantidadComent($link);
    break;
    case "getCantLikesComent":
        echo "Me gusta dados a comentarios: ".cantidadLikesComent($link);
    break;
}

function cantidadComentUsu($link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Comentario WHERE id_usu = ".$_SESSION["id"]." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function cantidadUsuarios($link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Login");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function cantidadSuenosTotal($link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function cantidadSuenosPublic($link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno WHERE sue_pri = 0 ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function cantidadSuenosPrivados($link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno WHERE sue_pri = 1 ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function cantidadSuenosM18($link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno WHERE sue_m18 = 1 ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function cantidadLikesSuenos($link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM LikeDislike");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function cantidadComent($link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Comentario");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function cantidadLikesComent($link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM LikeDislikeCom");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}




?>