<?php 

session_start();
//Esta página solo debería poder accedida por medio de ajax.
$id_com = $_POST["id_com"];
$link = mysqli_connect('localhost','root','','anotasuenos');
$function = "deleteLike";

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


?>