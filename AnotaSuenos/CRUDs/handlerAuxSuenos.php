<?php 
    //handlerAuxSuenos.php
    //Este handler debe ser capaz de apoyar el funcionamiento de los sueños por medio de las funciones
    //a las que se accederá por medio del switch de función.

    session_start();
    require "../config.php";
    $funcion = $_POST["funcion"];
    switch($funcion){
        case "cantidadSuenos":
            cantidadSuenos($link);
        break;
        case "insertLike":
            insertLike($link);
        break;
        case "deleteLike":
            deleteLike($link);
        break;
    }

    function cantidadSuenos($link){
        $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno");
        $data = mysqli_fetch_assoc($result);
        echo $data["total"];
    }

    function insertLike($link){
        $id_sue = $_POST["id_sue"];
        if(cantidadLikesUsuario($id_sue,$_SESSION["id"],$link) == 1){
            echo cantidadLikes($id_sue,$link);
            return null;
        }
        $sql = "INSERT INTO LikeDislike (id_sue,id_usu) VALUES(?,?)";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"ii",$id_sue_param,$cod_usu_param);
            $id_sue_param = $id_sue; $cod_usu_param = $_SESSION["id"];
            mysqli_stmt_execute($stmt);
            echo cantidadLikes($id_sue,$link);
            return null;
        }else{
            echo "Falla de conexión.";
        }
    }

    function deleteLike($link){
        $id_sue = $_POST["id_sue"];
        if(cantidadLikesUsuario($id_sue,$_SESSION["id"],$link) == 0){
            echo cantidadLikes($id_sue,$link);
            return null;
        }
        $sql = "DELETE FROM LikeDislike WHERE id_sue = ? AND id_usu = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"ii",$id_sue_param,$cod_usu_param);
            $id_sue_param = $id_sue; $cod_usu_param = $_SESSION["id"];
            mysqli_stmt_execute($stmt);
            echo cantidadLikes($id_sue,$link);
            return null;
        }else{
            echo "Falla de conexión.";
        }
    }

    function cantidadLikes($id_sue,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

    
    function cantidadLikesUsuario($id_sue,$id_usu,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." AND id_usu = ".$id_usu." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

?>