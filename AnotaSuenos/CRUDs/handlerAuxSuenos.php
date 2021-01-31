<?php 
    //handlerAuxSuenos.php
    //Este handler debe ser capaz de apoyar el funcionamiento de los sueños por medio de las funciones
    //a las que se accederá por medio del switch de función.

    session_start();
    require "../config.php";
    $funcion = $_POST["funcion"];

    //A partir de este switch se accederá a las funciones requeridas.
    switch($funcion){
        case "agregarSueno":
            agregarSueno($link);
        break;
        case "modificarSueno":
            modificarSueno($link);
        break;
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

    //Función agregarSueno
    //Input: Directo: Link de conexión - Indirecto: sueño, privacidad del sueño, clasificación +18 o no del sueño e id de usuario
    //Output: Mensaje de éxito o fallo en la operación.
    function agregarSueno($link){
        $sueno = $_POST["txtSueno"];
        $sue_pri = $_POST["suenoPrivado"];
        $sue_m18 = $_POST["suenoMas18"];
        $cod_usu = $_SESSION["id"];

        $sql = "INSERT INTO Sueno (sueno,sue_pri,sue_m18,fec_sue,cod_usu) VALUES(?,?,?,NOW(),?)";

        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"siii",$sueno_param,$sue_pri_param,$sue_m18_param,$cod_usu_param);
            $sueno_param = $sueno; $sue_pri_param = $sue_pri;
            $sue_m18_param = $sue_m18; $cod_usu_param = $cod_usu;
            if(mysqli_stmt_execute($stmt)){
                echo "Sueño publicado con éxito.";
            }else{
                echo "No se pudo agregar el sueño, posible error de conexión.";
            }
        }else{
            echo "Falla de conexión.";
        }
    }

    //Función modificarComentario
    //Input: Directo: Link de conexión - Indirecto: código de sueño, nuevo sueño y código de usuario.
    //Output: Texto actualizado del sueño.
    function modificarSueno($link){
        $id_sue = $_POST["id_sue"];
        $nuevo_sue = $_POST["nuevoSue"];
        $cod_usu = $_SESSION["id"];

        $sql = "UPDATE Sueno SET sueno=? WHERE id_sue=? AND cod_usu=? ";

        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"sii",$sueno_param,$id_sue_param,$cod_usu_param);
            $sueno_param = $nuevo_sue; $id_sue_param = $id_sue;
            $cod_usu_param = $cod_usu;
            if(mysqli_stmt_execute($stmt)){
                echo $nuevo_sue;
            }else{
                echo "No se pudo actualizar el sueño. Help.";
            }
        }else{
            echo "Falla de conexión.";
        }
    }

    //Función cantidadSuenos
    //Input: Directo: Link de conexión - Indirecto: id_sue por método GET
    //Output: Cantidad de sueños total.
    function cantidadSuenos($link){
        $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno");
        $data = mysqli_fetch_assoc($result);
        echo $data["total"];
    }

    //Función insertLike
    //Input: Directo: Link de conexión - Indirecto: id_sue por método POST
    //Output: Cantidad de likes actualizada tras insertar un like para ese sueño.
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

    //Función deleteLike
    //Input: Directo: Link de conexión - Indirecto: id_sue por método POST
    //Output: Cantidad de likes actualizada tras borrar un like correspondiente a ese sueño.
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

    //Función cantidadLikes
    //Input: Id de sueño y Link de conexión
    //Output: Cantidad de likes correspondiente al sueño
    function cantidadLikes($id_sue,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

    //Función cantidadLikesUsuario
    //Input: Id de sueño, id del usuario y Link de conexión
    //Output: Cantidad de likes que el usuario ha dado al sueño.
    //Nota: Nunca debería ser más de 1 ni menos que 0.
    function cantidadLikesUsuario($id_sue,$id_usu,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." AND id_usu = ".$id_usu." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

?>