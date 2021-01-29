<?php 
    session_start();    
    //Tomar la funcion y el id del sueño que vienen de ajax.
    $function = $_POST["accion"];
    $id_sue = $_POST["id_sue"];
    //Conectar a mySQL
    $link = mysqli_connect('localhost','root','','anotasuenos');
    //Revisar si el usuario ya ha dado me gusta al sueño.
    if(cantidadLikesUsuario($id_sue,$_SESSION["id"],$link) == 1){
        echo cantidadLikes($id_sue,$link);
        return null;
    }
    //Revisar si la función a ejecutar es insertLike
    if($function == 'insertLike'){
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
    //Revisar si la función a ejecutar es deleteLike
    //Por alguna razón, esta función se rehusa a funcionar.
    //De momento, para borrar un like, se usa "handlerDislikeSuenos.php"
    // if($function == "deleteLike"){
    //     $sql = "DELETE FROM LikeDislike WHERE id_sue = ? AND id_usu = ?";
    //     if($stmt = mysqli_prepare($link,$sql)){
    //         mysqli_stmt_bind_param($stmt,"ii",$id_sue_param,$cod_usu_param);
    //         $id_sue_param = $id_sue; $cod_usu_param = $_SESSION["id"];
    //         mysqli_stmt_execute($stmt);
    //         $function = null;
    //         echo cantidadLikes($id_sue,$link);
    //     }else{
    //         echo "Falla de conexión.";
    //     }
    // }

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