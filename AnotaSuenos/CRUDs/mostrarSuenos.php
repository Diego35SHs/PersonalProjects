<?php 
        
    session_start();
    $link = mysqli_connect('localhost','root','','anotasuenos');
    $function = $_GET["function"];

    switch($function){
        case "mostrarSuenosNPVNM18":
            mostrarSuenos($link);
        break;
        case "mostrarSuenosUsuarioPerf":
            mostrarSuenosUsuarioPerf($_GET["cod_usu"],$link);
        break;
        case "mostrarSuenosUsuarioPerfAlt":
            mostrarSueUsuPerfAlt($_GET["cod_usu"],$link);
        break;
        case "mostrarSueUsuAll":
            mostrarSueUsuAll($_GET["cod_usu"],$link);
        break;
    }

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

    //Gracias a esta función se puede control si los sueños son privados, +18 o ambos.
    function mostrarSuenosUsuarioPerf($cod_usu,$link){
        $response = array();
        $offsetQuery = $_GET["offset"];
        $result = mysqli_query($link, "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = ".$_GET["sue_pri"]." AND sue_m18 = ".$_GET["sue_m18"]." AND cod_usu = ".$cod_usu." ORDER BY fec_sue DESC LIMIT 10 OFFSET ".$offsetQuery." ");
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
                    }else{
                        echo "Sueño privado";
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

    function mostrarSueUsuPerfAlt($cod_usu,$link){
        $response = array();
        $offsetQuery = $_GET["offset"];
        $result = mysqli_query($link, "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = ".$_GET["sue_pri"]." AND (sue_m18 = ".$_GET["sue_m18"]." OR sue_m18 = 0) AND cod_usu = ".$cod_usu." ORDER BY fec_sue DESC LIMIT 10 OFFSET ".$offsetQuery." ");
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
                    }else{
                        echo "Sueño privado";
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

    function mostrarSueUsuAll($cod_usu,$link){
        $response = array();
        $result = mysqli_query($link, "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE cod_usu = ".$cod_usu." ORDER BY fec_sue DESC LIMIT 10 OFFSET ".$_GET["offset"]." ");
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
                echo "<label> Por: <a href='../CRUDs/perfilPublico.php?cod_usu=".$row["cod_usu"]."'>".$nombreUsuario."</a>";
                echo "</label>";
                echo "<textarea class='form-control' id='textAreaSue".$row["id_sue"]."' style='resize:none;".$alto."border:none;maxlength:500;background-color:white;text-color:black;' disabled='true'>";
                    echo $row["sueno"];
                echo "</textarea> <br>";
                echo "<span>";
                    if($row["sue_pri"] == 0){
                        echo "Sueño público";                
                    }else{
                        echo "Sueño privado";
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
                    echo "&nbsp;&nbsp;Me gusta: ";
                    echo "<input type='text' disabled='true' id='cantLikes".$row["id_sue"]."' class='cantLikes' value='".$cantLikes."' style='border: none; width: 35px;' > &nbsp;&nbsp;";
                echo "</span>";
                echo "<a  class='btn btn-info another-element' href='../CRUDs/verComentarios.php?id_sue=".$row["id_sue"]." '>Comentarios (".$cantComentarios.")</a>";
                echo "</div> </br>";
                array_push($response["suenos"], $temp);
            }
        }else{
            echo "No se encontró ningún registro.";
        }
    }

    //funcion heightTXA 
    //Input: cántidad de caracteres de un sueño
    //Output: Altura de textarea que ocupará el sueño.
    function heightTXA($cantidadCarac){
        //TODO: Este aspecto podría ajustarse más por cada línea.
        $altoTXA = "height:100px;";
        if($cantidadCarac <= 100){
            $altoTXA = "height:84px;";
        }
        if($cantidadCarac > 100 && $cantidadCarac <= 200){
            $altoTXA = "height:135px;";
        }
        if($cantidadCarac > 200 && $cantidadCarac <=300){
            $altoTXA = "height:190px;";
        }
        if($cantidadCarac > 300 && $cantidadCarac <= 400){
            $altoTXA = "height:160px;";
        }
        if($cantidadCarac > 400){
            $altoTXA = "height:300px;";
        }
        return $altoTXA;
    }

    //Función nombreUsuSueno
    //Input: Código de usuario y Link de conexión.
    //Output: Nombre de usuario correspondiente al sueño.
    function nombreUsuSueno($codusu,$link){
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

    //Función cantidadComentarios
    //Input: Código de sueño y Link de conexión.
    //Output: Cantidad de comentarios correspondientes al sueño.
    function cantidadComentarios($id_sue,$link){
        $result = mysqli_query($link,"SELECT count(*) as total FROM Comentario WHERE id_sue = ".$id_sue." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

    //Función cantidadLikes
    //Input: Código de sueño y Link de conexión.
    //Output: Cantidad de likes correspondientes al sueño.
    function cantidadLikes($id_sue,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

    //Función checkLike
    //Input: Código de sueño, Código de usuario y Link de conexión
    //Output: Cantidad de likes que el usuario en sesión ha dado al sueño.
    //Nota: No puede ser mayor que 1 ni menor que 0.
    function checkLike($id_sue,$id_usu,$link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." AND id_usu =".$id_usu." ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

    //Función checkPropiedad
    //Input: Directo: Código de usuario del sueño - Indirecto: Código de usuario en sesión.
    //Output: 1: El usuario es propietario del sueño - 0: El usuario NO es propietario del sueño.
    function checkPropiedad($id_usu){
        $id_usu_sue = $id_usu;
        $id_usu_ses = $_SESSION["id"];
        if($id_usu_sue == $id_usu_ses){
            return 1;
        }else{
            return 0;
        }
    }
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

$(document).on("click",".like", function(){
    console.log("INICIANDO PROCESO: BOTON LIKE");
    var id_sue = $(this).attr("id");
    var funcion = "insertLike";
    var button = $(this);
    //alert("AGGA"+id_sue);
    var paquete = "funcion="+funcion+"&id_sue="+id_sue;
    console.log("PAQUETE: "+paquete);
    //alert(paquete);
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
        data: paquete,
    }).done(function(respuesta){
        button.text("Ya no me gusta");
        console.log("TEXTO CAMBIADO: Ya no me Gusta");
        button.removeClass("btn-info");
        button.removeClass("like");
        console.log("CLASES REMOVIDAS");
        button.addClass("btn-danger");
        button.addClass("dislike");
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
    var funcion = "deleteLike";
    var paquete = "funcion="+funcion+"&id_sue="+id_sue;
    console.log("PAQUETE: "+paquete);
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
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

$(document).on("click",".modificar",function(){
    console.log("Botón modificar sueño - Iniciado");
    var id_sue = $(this).attr("id");
    console.log("ID BUTTON Y SUEÑO = "+id_sue);
    var button = $(this);
    button.text("Guardar");
    console.log("Quitar clases actuales referentes al funcionamiento del botón");
    button.removeClass("modificar");
    button.removeClass("btn-warning");
    console.log("Añadir clases necesarias para el nuevo funcionamiento del botón");
    button.addClass("btn-info");
    button.addClass("guardarCambios");
    //Conseguir el texto del sueño para luego utilizarlo de alguna forma.
    var textArea = document.getElementById("textAreaSue"+id_sue).innerHTML;
    console.log(textArea);
    //Tomar control sobre el textarea por medio de su id.
    var controlTXA = document.getElementById("textAreaSue"+id_sue);
    controlTXA.style="border-stiyle:solid;border-color:black;resize:none;";
    controlTXA.removeAttribute("disabled");
});

$(document).on("click",".guardarCambios",function(){
    console.log("Botón guardar cambios sueño - Iniciando");
    var id_sue = $(this).attr("id");
    console.log("ID BOTÓN Y SUEÑO = "+id_sue);
    //Tomar control del botón y del textarea
    var button = $(this);
    var controlTXA = document.getElementById("textAreaSue"+id_sue);
    //Conseguir valor nuevo del textarea
    var textArea = controlTXA.value;
    var alto = heightTXA(textArea.length);
    if(textArea == null || textArea == ''){
        alert("El sueño no puede estar vacío");
        return null;
    }else if(textArea.length > 500){
        alert("El sueño no puede pasar de 500 caracteres. Tienes "+textArea.length);
        return null;
    }
    //empaquetar la información
    var paquete = "funcion=modificarSueno&id_sue="+id_sue+"&nuevoSue="+textArea;
    console.log(paquete);
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
        data: paquete,
    }).done(function(respuesta){
        button.text("Modificar");
        console.log("Quitar clases actuales referentes al funcionamiento del botón");
        button.removeClass("btn-info");
        button.removeClass("guardarCambios");
        console.log("Añadir clases necesarias para el nuevo funcionamiento del botón");
        button.addClass("modificar");
        button.addClass("btn-warning");
        document.getElementById("textAreaSue"+id_sue).innerHTML = respuesta;
        console.log("RESPUESTA: "+respuesta);
        controlTXA.style="border:none;resize:none;background-color:white;"+alto+"";
        controlTXA.setAttribute("disabled","true");
        console.log("Botón guardar cambios sueño - Finalizado");
        alert("Sueño modificado.");
        return null;
        event.stopPropagation();
    }).fail(function(respuesta){
        document.getElementById("textAreaSue"+id_sue).innerHTML = respuesta;
    });
});


//TODO: Analizar esto.
//Esta función existe en formato PHP, pero para aplicar el estilo al modificar un comentario
//Es necesaria esta copia en javascript. Hacen lo mismo, pero usarla en formato php significaría
//Usar ajax con redirección a esta misma página, es posible, y de hecho podría implementarse una función
//en handlerAuxSuenos.php que se encargue de esto siempre que sea necesario, de momento, esta solución
//está funcionando y correctamente.
function heightTXA(cantidadCarac){
        //TODO: Este aspecto podría ajustarse más por cada línea.
        //TODO: Cambiar por un switch si es posible.
        var altoTXA = "height:100px;";
        if(cantidadCarac <= 100){
            altoTXA = "height:84px;";
        }
        if(cantidadCarac > 100 && cantidadCarac <= 200){
            altoTXA = "height:135px;";
        }
        if(cantidadCarac > 200 && cantidadCarac <=300){
            altoTXA = "height:190px;";
        }
        if(cantidadCarac > 300 && cantidadCarac <= 400){
            altoTXA = "height:160px;";
        }
        if(cantidadCarac > 400){
            altoTXA = "height:300px;";
        }
        return altoTXA;
        
}
</script>
</html>