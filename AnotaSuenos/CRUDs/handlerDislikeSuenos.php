<?php
    session_start();
    //Esta página solo debería poder accedida por medio de ajax.
    $id_sue = $_POST["id_sue"];
    $link = mysqli_connect('localhost','root','','anotasuenos');
    $function = "deleteLike";
    if($function == "deleteLike"){
        $sql = "DELETE FROM LikeDislike WHERE id_sue = ? AND id_usu = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"ii",$id_sue_param,$cod_usu_param);
            $id_sue_param = $id_sue; $cod_usu_param = $_SESSION["id"];
            mysqli_stmt_execute($stmt);
            $function = null;
            echo cantidadLikes($id_sue,$link);
        }else{
            echo "Falla de conexión.";
        }
    }
        
    function cantidadLikes($id_sue,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

    ?>