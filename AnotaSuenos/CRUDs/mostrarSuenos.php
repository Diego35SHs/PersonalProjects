<?php 

//Inicializar conexión con los parámetros definidos.
    try{
        $link = mysqli_connect('localhost','root','','anotasuenos');
        $response = array();
        //TODO: Cambiar el LIMIT para mostrar más registros en la página principal
        $offsetQuery = $_GET["offset"];
        if($offsetQuery == null || $offsetQuery == ''){
            $offsetQuery = 0;
        }
        $result = mysqli_query($link, "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 0 ORDER BY fec_sue DESC LIMIT 10 OFFSET ".$offsetQuery." ");
        if(mysqli_num_rows($result)>0){
            $response["suenos"] = array();
            while($row = mysqli_fetch_array($result)){
                $temp = array();
                echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
                //TODO: Encontrar una manera más acorde a las convenciones de HTML y CSS para mostrar un multiline. Esto es un parche.
                $cantidadCarac = strlen($row["sueno"]);
                $nombreUsuario = nombreUsuSueno($row["cod_usu"],$link);
                $alto = heigthTXA($cantidadCarac);
                $cantComentarios = cantidadComentarios($row["id_sue"],$link);
                $cantLikes = cantidadLikes($row["id_sue"],$link);
                echo "<label>";
                    echo "Por: ";
                    echo "<a href='perfilPublico.php?nom_usu=".$nombreUsuario."'>";
                    echo $nombreUsuario;
                    echo "</a>";
                echo "</label>";
                echo "<textarea class='form-control' style='resize:none;".$alto."border:none;maxlength:500;background-color: white;' disabled='true'>";
                    echo $row["sueno"];
                echo "</textarea> <br>";
                echo "<span>";
                    if($row["sue_pri"] == 0){
                        echo "Sueño público";
                        echo "&nbsp;&nbsp;";
                    }
                echo "</span>";
                echo "<span>";
                    //TODO: Crear función que permita cambiar el botón dependiendo de si ha dado like o no ya al sueño
                    // Si no le ha dado, que la función inserte el like a la base de datos
                    // Si ya le ha dado, que el botón se muestre como "Ya no me gusta" y permita borrar el like de la base de datos.
                    echo "<a  class='btn border another-element' href=''>Me gusta</a>";    
                    echo "&nbsp;&nbsp;";
                    echo "Me gusta: ".$cantLikes;
                    echo "&nbsp;&nbsp;";
                echo "</span>";
                echo "<a  class='btn btn-info another-element' href='verComentarios.php?id_sue=".$row["id_sue"]." '>Comentarios (".$cantComentarios.")</a>";
                echo "</div> </br>";
                array_push($response["suenos"], $temp);
            }
        }else{
            echo "<tr colspan='5'>";
                echo "<td>";
                    echo "No se encontró ningún registro.";
                echo "</td>";
            echo "</tr>";
        }
    }catch(Exception $agga){
        echo $agga;
    }

    function heigthTXA($cantidadCarac){
        //TODO: Este aspecto podría ajustarse más por cada línea.
        // ¿Quizás height : 100CH?
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

    function nombreUsuSueno($codusu,$link){
        //Consulta individual
        $sql = "SELECT nom_usu FROM Login WHERE cod_usu = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"i",$param_codusu);
            $param_codusu = $codusu;
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt,$nombreUsuario);
                    if(mysqli_stmt_fetch($stmt)){
                        return $nombreUsuario;
                    }
                }else{
                    return "No se encontró el usuario";
                }
            }
        }
        return "Usuario no encontrado";
    }

    function cantidadComentarios($id_sue,$link){
        $result = mysqli_query($link,"SELECT count(*) as total FROM Comentario WHERE id_sue = ".$id_sue." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

    function cantidadLikes($id_sue,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

    //TODO: Crear páginas para ver comentarios y para ver perfiles de usuario.
    //TODO: Comparar tiempo para poder mostrar hace cuanto se publicó un sueño.

?>