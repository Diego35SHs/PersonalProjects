<?php 

//Inicializar conexión con los parámetros definidos.

        $link = mysqli_connect('localhost','root','','anotasuenos');
        $response = array();
        $offsetQuery = $_GET["offset"];
        $funcion = null;
        if($offsetQuery == null || $offsetQuery == ''){
            $offsetQuery = 0;
        }
        $result = mysqli_query($link, "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 0 ORDER BY fec_sue DESC LIMIT 10 OFFSET ".$offsetQuery." ");
        // $result = mysqli_query($link, "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 0 ORDER BY fec_sue ");
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
                    if(checkLike($row["id_sue"],$row["cod_usu"],$link) == 0){
                        echo "<button id='".$row['id_sue']."' class='like btn btn-info'>Me gusta</button>";
                    }else{
                        echo "<button id='".$row['id_sue']."' class='dislike btn btn-danger'>Ya no me gusta</button>";
                    } 
                    echo "&nbsp;&nbsp;";
                    echo "Me gusta: ";
                    echo "<input type='text' disabled='true' id='cantLikes".$row["id_sue"]."' class='cantLikes' value='".$cantLikes."' style='border: none; width: 35px;' >";
                    echo "&nbsp;&nbsp;";
                echo "</span>";
                echo "<a  class='btn btn-info another-element' href='CRUDs/verComentarios.php?id_sue=".$row["id_sue"]." '>Comentarios (".$cantComentarios.")</a>";
                echo "</div> </br>";
                array_push($response["suenos"], $temp);
            }
        }else{
            echo "No se encontró ningún registro.";
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

    function checkLike($id_sue,$id_usu,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." AND id_usu =".$id_usu." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

    

    //TODO: Crear páginas para ver comentarios y para ver perfiles de usuario.
    //TODO: Comparar tiempo para poder mostrar hace cuanto se publicó un sueño.

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No deberías estar viendo esto.</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
</body>
<script>
//TODO : Arreglar el que se repita la función al "cambiar de página".
//Podría tener que ver con el document en esta función de abajo.
//Parece que este bug ya está solucionado según las pruebas realizadas
//Pero de momento parece ya no darse la situación.
//La solución fue un return en la función insertLike en el handlerLikesSuenos.php
$(document).on("click",".like", function(){
    console.log("INICIANDO PROCESO: BOTON LIKE");
    var id_sue = $(this).attr("id");
    var accion = "insertLike";
    var button = $(this);
    //alert("AGGA"+id_sue);
    var paquete = "accion="+accion+"&id_sue="+id_sue;
    console.log("PAQUETE: "+paquete);
    //alert(paquete);
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerLikesSuenos.php",
        data: paquete,
    }).done(function(respuesta){
        // $('.cantLikes').attr("id");
        //alert(respuesta);
        button.text("Ya no me gusta");
        console.log("TEXTO CAMBIADO: Ya no me Gusta");
        button.removeClass("btn-info");
        button.removeClass("like");
        console.log("CLASES REMOVIDAS");
        button.addClass("btn-danger");
        button.addClass("dislike");
        // button.attr("id", id_sue);
        console.log("CLASES AÑADIDAS");
        console.log("Respuesta: "+respuesta);
        document.getElementById("cantLikes"+id_sue).value = respuesta;
        console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
        return null;
    }).fail(function(respuesta){
        alert("Error de conexión. Probablemente.");
    })
});

$(document).on("click",".dislike", function(){
    console.log("INICIANDO PROCESO: BOTON DISLIKE");
    var id_sue = $(this).attr("id");
    var button = $(this);
    var accion = "deleteLike";
    var paquete = "accion="+accion+"&id_sue="+id_sue;
    console.log("PAQUETE: "+paquete);
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerDislikeSuenos.php",
        //Esta cagá no funciona como quiero. Qué sorpresa.
        // url: "http://anotasuenos:8080/CRUDs/handlerLikesSuenos.php",
        data: paquete,
    }).done(function(respuesta){
        button.text("Me gusta");
        console.log("TEXTO CAMBIADO: Me Gusta");
        button.removeClass("btn-danger");
        button.removeClass("dislike");
        console.log("CLASES REMOVIDAS");
        button.addClass("btn-info");
        button.addClass("like");
        console.log("CLASES AÑADIDAS");
        console.log("Respuesta: "+respuesta);
        document.getElementById("cantLikes"+id_sue).value = respuesta;
        console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
        return null;
    }).fail(function(respuesta){
        alert("Error de conexión. Probablemente.");
    })
});

</script>
</html>