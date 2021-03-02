<?php 
    //handlerAuxSuenos.php
    //Este handler debe ser capaz de apoyar el funcionamiento de los sueños por medio de las funciones
    //a las que se accederá por medio del switch de función.

    session_start();
    require "../config.php";

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: ../index.php");
        exit;
    }

    $funcion = $_POST["funcion"];
    if(!isset($_POST["funcion"])){
        header("location: ../index.php");
        exit;
    }

    //A partir de este switch se accederá a las funciones requeridas.
    switch($funcion){
        case "agregarSueno":
            agregarSueno($link);
        break;
        case "eliminarSueno":
            eliminarSueno($link);
        break;
        case "modificarSueno":
            modificarSueno($link);
        break;
        case "cantidadSuenosPublic":
            cantidadSuenosPublic($link);
        break;
        case "cantSueUsuario":
            cantSueUsuario($link);
        break;
        case "cantLikesRecUsuario":
            cantLikesRecUsuario($link);
        break;
        case "cantidadSuenosTotal":
            cantidadSuenosTotal($link);
        break;
        case "insertLike":
            insertLike($link);
        break;
        case "deleteLike":
            deleteLike($link);
        break;
        case "heightTXA":
            return heightTXA($caracteres);
        break;
        case "contarSueFiltro":
            prepararCountQuery($link);
        break;
        case "setMas18":
            setMas18($link);
        break;
        case "setNoMas18":
            setNoMas18($link);
        break;
        case "privatizar":
            privatizarSue($link);
        break;
        case "desprivatizar":
            desprivatizarSue($link);
        break;
    }

    //Contar cantidad de sueños basado en el filtro actual.
    function prepararCountQuery($link){
        $opcion = $_POST["opcion"];
        switch($opcion){
            case "noPVnoM18":
                $query = "SELECT count(*) as total FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 0";
                cantidadSuenosCustom($link,$query);
            break;
            case "soloSeguidosNoM18":
                $query = "SELECT count(*) as total FROM Sueno,Seguidores,Login WHERE Sueno.cod_usu = Seguidores.id_usu_sdo AND Seguidores.id_usu_sdr = ".$_SESSION["id"]." AND Sueno.sue_pri = 0 AND Sueno.sue_m18 = 0 ";
                cantidadSuenosCustom($link,$query);
            break;
            case "busqueda":
                $busqueda = $_POST["termBusqueda"];
                $query = "SELECT count(*) as total FROM Sueno WHERE sue_pri = 0 AND (sue_m18 = 1 OR sue_m18 = 0) AND sueno LIKE '%".$busqueda."%' ";
                cantidadSuenosCustom($link,$query);
            break;
            case "masPopulares":
                $query = "SELECT count(*) as total FROM Sueno WHERE sue_pri = 0";
                cantidadSuenosCustom($link,$query);
            break;
            case "noPVsiM18":
                $query = "SELECT count(*) as total FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 1";
                cantidadSuenosCustom($link,$query);
            break;
        }
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

    function eliminarSueno($link){
        $id_sue = $_POST["id_sue"];
        $sql = "DELETE FROM Sueno WHERE id_sue = ? AND cod_usu = ? ";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"ii",$id_sue_param,$cod_usu_param);
            $id_sue_param = $id_sue; $cod_usu_param = $_SESSION["id"];
            if(mysqli_stmt_execute($stmt)){
                eliminarComSue($link,$id_sue);
                eliminarLikeSue($link,$id_sue);
                eliminarLikeCom($link,$id_sue);
                echo "Eliminado.";
            }else{
                echo "Fallo al eliminar. El usuario en sesión podría no coincidir con el sueño que se intenta borrar.";
            }
        }
    }

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

    //Función cantSueUsuario
    //Input: Directo: Link de conexión - Indirecto: Código de usuario por método POST
    //Output: Cantidad de sueños publicados por el usuario solicitado, sin importar categorías o configuración.
    function cantSueUsuario($link){
        $cod_usu = $_POST["cod_usu"];
        $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno WHERE cod_usu = ".$cod_usu." ");
        $data = mysqli_fetch_assoc($result);
        echo $data["total"];
    }

    //Función cantLikesRecUsuario
    //Input: Directo: Link de conexión - Indirecto: código de usuario del perfil.
    //Output: Cantidad de likes recibidos (Rec) en total del usuario del perfil.
    function cantLikesRecUsuario($link){
        $cod_usu = $_POST["cod_usu"];
        $result = mysqli_query($link, "SELECT Count(*) AS total From LikeDislike,Sueno WHERE Sueno.cod_usu = ".$cod_usu." AND Sueno.id_sue = LikeDislike.id_sue;");
        $data = mysqli_fetch_assoc($result);
        echo $data["total"];
    }

    //Función cantidadSuenosPublic
    //Input: Directo: Link de conexión - Indirecto: id_sue por método GET
    //Output: Cantidad de sueños total.
    function cantidadSuenosPublic($link){
        $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 0");
        $data = mysqli_fetch_assoc($result);
        echo $data["total"];
    }

    //Función cantidadSuenosTotal
    //Input: Link de conexión
    //Output: Cantidad de sueños total publicados, sin importar sus categorías o configuración.
    function cantidadSuenosTotal($link){
        $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno");
        $data = mysqli_fetch_assoc($result);
        echo $data["total"];
    }

    function cantidadSuenosCustom($link,$query){
        $result = mysqli_query($link, $query);
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
            updateLikesSueno($link, $id_sue, 1);
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
            updateLikesSueno($link, $id_sue, 0);
            echo cantidadLikes($id_sue,$link);
            return null;
        }else{
            echo "Falla de conexión.";
        }
    }

    function setMas18($link){
        $id_sue = $_POST["id_sue"];
        $sql = "UPDATE Sueno SET sue_m18 = 1 WHERE id_sue = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"i",$cod_sue_param);
            $cod_sue_param = $id_sue;
            if(mysqli_stmt_execute($stmt)){
                echo "+18";
            }else{
                echo "ERRRRORR";
            }
        }else{
            echo "ERROR";
        }
    }

    function setNoMas18($link){
        $id_sue = $_POST["id_sue"];
        $sql = "UPDATE Sueno SET sue_m18 = 0 WHERE id_sue = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"i",$cod_sue_param);
            $cod_sue_param = $id_sue;
            if(mysqli_stmt_execute($stmt)){
                echo "No +18";
            }else{
                echo "ERRRRORR";
            }
        }else{
            echo "ERROR";
        }
    }

    function privatizarSue($link){
        $id_sue = $_POST["id_sue"];
        $sql = "UPDATE Sueno SET sue_pri = 1 WHERE id_sue = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"i",$cod_sue_param);
            $cod_sue_param = $id_sue;
            if(mysqli_stmt_execute($stmt)){
                echo "Sueño privado";
            }else{
                echo "ERRRRORR";
            }
        }else{
            echo "ERROR";
        }
    }

    function desprivatizarSue($link){
        $id_sue = $_POST["id_sue"];
        $sql = "UPDATE Sueno SET sue_pri = 0 WHERE id_sue = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"i",$cod_sue_param);
            $cod_sue_param = $id_sue;
            if(mysqli_stmt_execute($stmt)){
                echo "Sueño público";
            }else{
                echo "ERRRRORR";
            }
        }else{
            echo "ERROR";
        }
    }

    //Actualizar cantidad de likes del sueño en su propia tabla.
    //Hay dos opciones, una es añadir el like, buscar la cantidad de nuevo y poner esa cantidad en la columna en la tabla sueños
    //o actualizar con +1 o -1 en la tabla sueños. Esta opción parece más sencilla sin hacer demasiadas consultas.
    function updateLikesSueno($link, $id_sue, $masmenos){
        //1 -> más -- 0 -> menos
        $masmenos = "No se está usando, borrameeee";
        //REWORK -> Buscar cantidad de sueños nueva y poner esa cantidad en la columna de los sueños.
        // if($masmenos == 1){
        //     $sql = "UPDATE Sueno SET megusta = megusta + 1 WHERE id_sue=? ";
        // }else if($masmenos == 0){
        //     $sql = "UPDATE Sueno SET megusta = megusta - 1 WHERE id_sue=? ";
        // }else{
        //     echo "<script> alert('¿Qué pasa aquí?'); </script>";
        // }
        $sql = "UPDATE Sueno SET megusta = ? WHERE id_sue=? ";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"ii",$cant_megusta_actual,$id_sue_param);
            $cant_megusta_actual = cantidadLikes($id_sue,$link); $id_sue_param = $id_sue;
            if(mysqli_stmt_execute($stmt)){
                echo null;
            }else{
                echo "No se pudo actualizar la columna megusta. Help.";
            }
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

    //funcion heightTXA 
    //Input: cántidad de caracteres de un sueño
    //Output: Altura de textarea que ocupará el sueño.
    //NOTA: Esto no se está usando todavía. Pero en teoría es utilizable por medio de Ajax.
    function heightTXA($cantidadCarac){
        $altoTXA = "height:100px;";
        if($cantidadCarac >=180){
            $altoTXA = "height:70px;";
        }
        if($cantidadCarac >= 200){
            $altoTXA = "height:130px;";
        }
        if($cantidadCarac >=300){
            $altoTXA = "height:160px;";
        }
        if($cantidadCarac >= 400){
            $altoTXA = "height:210px;";
        }
        return $altoTXA;
    }

?>
