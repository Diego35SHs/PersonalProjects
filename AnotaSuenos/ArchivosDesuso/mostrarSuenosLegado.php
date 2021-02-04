<?php 

//Esta función es muy fabulosa como para borrarla, así que aquí hay una copia.
function mostrarSuenos($link){
    $response = array();
    $offsetQuery = $_GET["offset"];
    $result = mysqli_query($link, "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 0 ORDER BY fec_sue DESC LIMIT 10 OFFSET ".$offsetQuery." ");
    if(mysqli_num_rows($result)>0){
        $response["suenos"] = array();
        while($row = mysqli_fetch_array($result)){
            $temp = array();
            echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
            $cantidadCarac = strlen($row["sueno"]);
            $nombreUsuario = nombreUsuSueno($row["cod_usu"],$link);
            $alto = heightTXA($cantidadCarac);
            $cantComentarios = cantidadComentarios($row["id_sue"],$link);
            $cantLikes = cantidadLikes($row["id_sue"],$link);
            echo "<label>";
                echo "Por: ";
                echo "<a href='../CRUDs/perfilPublico.php?cod_usu=".$row["cod_usu"]."'>";
                echo $nombreUsuario;
                echo "</a>";
            echo "</label>";
            echo "<textarea class='form-control' id='textAreaSue".$row["id_sue"]."' style='resize:none;".$alto."border:none;maxlength:500;background-color:white;text-color:black;' disabled='true'>";
                echo $row["sueno"];
            echo "</textarea> <br>";
            echo "<span>";
                if($row["sue_pri"] == 0){
                    echo "Sueño público";
                }
                echo "&nbsp;&nbsp;";
            echo "</span>";
            if($row["sue_m18"] == 1){
                echo "<span> +18 </span> &nbsp;&nbsp;";
            }
            echo "<span>";
                if(checkPropiedad($row["cod_usu"]) == 1){
                    echo "<button id='".$row["id_sue"]."' class='modificar btn btn-warning'>Modificar</button>";
                    echo "&nbsp;";
                }else{
                    echo null;
                }
            echo "</span>";
            echo "<span>";
                if(checkLike($row["id_sue"],$_SESSION["id"],$link) == 0){
                    echo "<button id='".$row['id_sue']."' class='like btn btn-info'>Me gusta</button>";
                }else{
                    echo "<button id='".$row['id_sue']."' class='dislike btn btn-danger'>Ya no me gusta</button>";
                } 
                echo "&nbsp;&nbsp;";
                echo "Me gusta: ";
                echo "<input type='text' disabled='true' id='cantLikes".$row["id_sue"]."' class='cantLikes' value='".$cantLikes."' style='border: none; width: 35px;' >";
                echo "&nbsp;&nbsp;";
            echo "</span>";
            echo "<a  class='btn btn-info another-element' href='../CRUDs/verComentarios.php?id_sue=".$row["id_sue"]." '>Comentarios (".$cantComentarios.")</a>";
            echo "</div> </br>";
            array_push($response["suenos"], $temp);
        }
    }else{
        echo "No se encontró ningún registro.";
    }
}

?>