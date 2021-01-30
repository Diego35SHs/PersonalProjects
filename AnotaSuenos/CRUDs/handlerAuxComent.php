<?php 
    session_start();
    require "../config.php";
    $funcion = $_POST["funcion"];

    switch($funcion){
        case "cantidadComent":
            cantidadComent($link);
        break;
        case "insertLikeCom":
            insertLikeCom($link);
        break;
        case "deleteLikeCom":
            deleteLikeCom($link);
        break;
    }

    //Función cantidadComent
    //Input: Directo: Link de conexión - Indirecto: id_sue por método GET
    //Output: Cantidad de comentarios correspondientes al sueño en cuestión.
    function cantidadComent($link){
        $id_sue = $_POST["id_sue"];
        $result = mysqli_query($link,"SELECT count(*) as total FROM Comentario WHERE id_sue = ".$id_sue." ");
        $data = mysqli_fetch_assoc($result);
        echo $data["total"];
    }

    //Función insertLikeCom
    //Input: Directo: Link de conexión - Indirecto: id_com por método GET
    //Output: Cantidad de likes actualizada tras insertar un like para ese comentario.
    function insertLikeCom($link){
        $sql = "INSERT INTO LikeDislikeCom(id_com,id_usu) VALUES(?,?)";
        $id_com = $_POST["id_com"];
        if(cantidadLikesUsuario($id_com,$_SESSION["id"],$link) == 1){
            echo cantidadLikes($id_com,$link);
            return null;
        }
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

    //Función deleteLikeCom
    //Input: Directo: Link de conexión - Indirecto: id_com por método GET
    //Output: Cantidad de likes actualizada tras borrar un like correspondiente a ese comentario.
    function deleteLikeCom($link){
        $sql = "DELETE FROM LikeDislikeCom WHERE id_com = ? AND id_usu = ?";
        $id_com = $_POST["id_com"];
        if(cantidadLikesUsuario($id_com,$_SESSION["id"],$link) == 0){
            echo cantidadLikes($id_com,$link);
            return null;
        }
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

    //Función cantidadLikes
    //Input: Id de comentario y Link de conexión
    //Output: Cantidad de likes correspondiente al comentario
    function cantidadLikes($id_com,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislikeCom WHERE id_com = ".$id_com." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }
    
    //Función cantidadLikesUsuario
    //Input: Id de comentario, id del usuario y Link de conexión
    //Output: Cantidad de likes que el usuario ha dado al comentario
    //Nota: Nunca debería ser más de 1 ni menos que 0.
    function cantidadLikesUsuario($id_com,$id_usu,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislikeCom WHERE id_com = ".$id_com." AND id_usu = ".$id_usu." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

?>