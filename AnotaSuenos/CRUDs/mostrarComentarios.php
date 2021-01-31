<?php 
session_start();
$id_sue = $_GET["id_sue"];
$funcion = $_GET["funcion"];

//Se encarga de separar las funciones a realizar en cada consulta ajax
//Esto evita que se ejecuten de inmediato ambas y quede la embarrá
//Y así tambien evito usar dos archivos diferentes sin razón.
//Los miro a ustedes, handlers csm.
if($funcion == "verComentarios"){
    verComentarios();
}
if($funcion == "verSueno"){
    verSueno();
}

//Función verSueno
//Input: Ninguno directo - Toma el id del sueño con metodo GET
//Output: Mostrar el sueño
function verSueno(){
    $link = mysqli_connect('localhost','root','','anotasuenos');
    $response = array();
    $result = mysqli_query($link, "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE id_sue =  ".$_GET["id_sue"]." ");
    if(mysqli_num_rows($result)>0){
        $response["suenos"] = array();
        while($row = mysqli_fetch_array($result)){
            $temp = array();
            echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
            //TODO: Encontrar una manera más acorde a las convenciones de HTML y CSS para mostrar un multiline. Esto es un parche.
            $cantidadCarac = strlen($row["sueno"]);
            $nombreUsuario = nombreUsuSueno($row["cod_usu"],$link);
            $alto = heightTXA($cantidadCarac);
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
                }else if($row["sue_pri"] == 1){
                    echo "Sueño privado";
                    echo "&nbsp;&nbsp;";
                }
            echo "</span>";
            echo "<span>";
                if(checkLike($row["id_sue"],$row["cod_usu"],$link) == 0){
                    echo "<button id='".$row['id_sue']."' class='likeSue btn btn-info'>Me gusta</button>";
                }else{
                    echo "<button id='".$row['id_sue']."' class='dislikeSue btn btn-danger'>Ya no me gusta</button>";
                } 
                echo "&nbsp;&nbsp;";
                echo "Me gusta: ";
                echo "<input type='text' disabled='true' id='cantLikes".$row["id_sue"]."'  value='".$cantLikes."' style='border: none; width: 35px;' >";
                echo "&nbsp;&nbsp;";
            echo "</span>";
            echo "</div>";
            array_push($response["suenos"], $temp);
        }
    }else{
        echo "No se encontró ningún registro.";
    }
    
}


//Función verComentarios
//Input: Ninguno directo - Toma el id del sueño con metodo GET
//Output: Listado de comentarios correspondientes a un sueño
//TODO: Hacer funcionar el sistema de offset.
function verComentarios(){
    $link = mysqli_connect('localhost','root','','anotasuenos');
    $response = array();
    $result = mysqli_query($link, "SELECT id_com,id_sue,id_usu,comentario FROM Comentario WHERE id_sue = ".$_GET["id_sue"]." ORDER BY id_com desc ");
    if(mysqli_num_rows($result)>0){
        $response["comentarios"] = array();
        while($row = mysqli_fetch_array($result)){
            $temp = array();
            $cantidadCarac = strlen($row["comentario"]);
            $nombreUsuario = nombreUsuarioCome($row["id_usu"],$link);
            $alto = heightTXA($cantidadCarac);
            $cantComentarios = cantidadComentarios($row["id_sue"],$link);
            $cantLikes = cantidadLikesCom($row["id_com"],$link);
            echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
            echo "<label>";
                    echo "Por: ";
                    echo "<a href='perfilPublico.php?nom_usu=".$nombreUsuario."'>";
                    echo $nombreUsuario;
                    echo "</a>";
            echo "</label>";
            echo "<textarea class='form-control' style='resize:none;".$alto."border:none;maxlength:500;background-color: white;' disabled='true'>";
                echo $row["comentario"];
            echo "</textarea> <br>";
            echo "<span>";
                    if(checkLikeCom($row["id_com"],$row["id_usu"],$link) == 0){
                        echo "<button id='".$row['id_com']."' class='like btn btn-info'>Me gusta</button>";
                    }else{
                        echo "<button id='".$row['id_com']."' class='dislike btn btn-danger'>Ya no me gusta</button>";
                    } 
                    echo "&nbsp;&nbsp;";
                    echo "Me gusta: ";
                    echo "<input type='text' disabled='true' id='cantLikesCom".$row["id_com"]."'  value='".$cantLikes."' style='border: none; width: 35px;' >";
                    echo "&nbsp;&nbsp;";
            echo "</span>";
            echo "</div>";
            echo "</br>";
            array_push($response["comentarios"], $temp);
        }
    }else{
        echo "No hay comentarios";
    }
    return null;
}

//Función nombreUsuSueno
//Input: ID de usuario y Link de conexión
//Output: Nombre de usuario correspondiente al sueño.
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


//Función nombreUsuarioCome
//Input: ID de usuario y Link de conexión
//Output: Nombre de usuario correspondiente al comentario.
function nombreUsuarioCome($cod_usu,$link){
    $sql = "SELECT nom_usu FROM Login WHERE cod_usu = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"i",$param_codusu);
            $param_codusu = $cod_usu;
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


//Función heightTXA
//Input: Cantidad de caracteres del sueño o comentario.
//Output: Alto del textarea que ocupará el comentario.
function heightTXA($cantidadCarac){
    //TODO: Este aspecto podría ajustarse más por cada línea, quizás un switch sea mejor.
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


//SUEÑOS
//Función cantidadComentarios 
//Input: ID del sueño y Link de conexion
//Output: Cantidad de comentarios correspondientes al sueño.
function cantidadComentarios($id_sue,$link){
    $result = mysqli_query($link,"SELECT count(*) as total FROM Comentario WHERE id_sue = ".$id_sue." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}
//Función cantidadLikes
//Input: ID del sueño y Link de conexión
//Output: Cantidad de "Me gusta" correspondientes al sueño.
function cantidadLikes($id_sue,$link){
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//Función checkLike
//Input: ID del sueño, ID de usuario y Link de conexión
//Output: Cantidad de likes dados por un usuario a un sueño.
//Nota: Nunca debe poder se más de 1.
function checkLike($id_sue,$id_usu,$link){
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = ".$id_sue." AND id_usu =".$id_usu." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//COMENTARIOS
//Función cantidadLikesCom
//Input: ID del comentario y Link de conexión
//Output: Cantidad de likes correspondientes al comentario.
function cantidadLikesCom($id_com,$link){
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislikeCom WHERE id_com = ".$id_com." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//Función checkLikeCom
//Input: ID del comentario, ID del usuario y Link de conexión
//Output: Cantidad de likes dados por un usuario al comentario.
//Nota: Nunca debe poder ser más de uno.
function checkLikeCom($id_com,$id_usu,$link){
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislikeCom WHERE id_com = ".$id_com." AND id_usu =".$id_usu." ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
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

//LIKES Y DISLIKES PARA EL SUEÑO.
//TODO: Arreglar esta cagá que no se por qué no quiere funcionar.

//Función likeSue en AJAX
//Input: Atributo ID del botón con clase .likeSue
//Output: Cambiar clase y texto del botón. Cambiar cantidad de likes
$(document).on("click",".likeSue", function(){
    console.log("INICIANDO PROCESO: BOTON LIKE");
    //Conseguir el atributo del botón, que es el id del sueño.
    var id_sue = $(this).attr("id");
    //Definir la acción a realizar en el handler. Esto NO está funcionando.
    var funcion = "insertLike";
    //Tomar control del botón por medio de una variable.
    var button = $(this);
    //Empaquetar la información.
    var paquete = "funcion="+funcion+"&id_sue="+id_sue;
    console.log("PAQUETE: "+paquete);
    //Ajax.
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
        data: paquete,
    }).done(function(respuesta){
        //Cambiar características del botón.
        button.text("Ya no me gusta");
        console.log("TEXTO CAMBIADO: Ya no me Gusta");
        button.removeClass("btn-info");
        button.removeClass("like");
        console.log("CLASES REMOVIDAS");
        button.addClass("btn-danger");
        button.addClass("dislike");
        console.log("CLASES AÑADIDAS");
        console.log("Respuesta: "+respuesta);
        //Cambiar la cantidad de likes a la actual. Esta cagá es terrible mañosa ni idea por qué.
        document.getElementById("cantLikes"+id_sue).value = respuesta;
        console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
        return null;
    }).fail(function(respuesta){
        alert("Error de conexión. Probablemente.");
    })
});

$(document).on("click",".dislikeSue", function(){
    console.log("INICIANDO PROCESO: BOTON DISLIKE");
    //Conseguir el atributo del botón, que es el id del sueño.
    var id_sue = $(this).attr("id");
    //Tomar control del botón por medio de una variable.
    var button = $(this);
    //Definir la acción a realizar en el handler. Esto NO está funcionando.
    var funcion = "deleteLike";
    //Empaquetar la información.
    var paquete = "funcion="+funcion+"&id_sue="+id_sue;
    console.log("PAQUETE: "+paquete);
    //Ajax.
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
        data: paquete,
    }).done(function(respuesta){
        //Cambiar características del botón.
        button.text("Me gusta");
        console.log("TEXTO CAMBIADO: Me Gusta");
        button.removeClass("btn-danger");
        button.removeClass("dislike");
        console.log("CLASES REMOVIDAS");
        button.addClass("btn-info");
        button.addClass("like");
        console.log("CLASES AÑADIDAS");
        console.log("Respuesta: "+respuesta);
        console.log("ID CANTLIKESSUE : "+document.getElementById("cantLikes"+id_sue).value);
        //BUG : Cambiar la cantidad de likes a la actual. Esta cagá es terrible mañosa ni idea por qué.
        document.getElementById("cantLikes"+id_sue).value = respuesta;
        console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
        return null;
    }).fail(function(respuesta){
        alert("Error de conexión. Probablemente.");
    })
});

//LIKES Y DISLIKES PARA COMENTARIOS.
//TODO : Arreglar el que se repita la función al "cambiar de página".
//Podría tener que ver con el document en esta función de abajo.
//Parece que este bug ya está solucionado según las pruebas realizadas
//Pero de momento parece ya no darse la situación.
//La solución fue un return en la función insertLike en el handlerLikesSuenos.php
$(document).on("click",".like", function(){
    console.log("INICIANDO PROCESO: BOTON LIKE");
    //Conseguir el atributo del botón, que es el id del sueño.
    var id_com = $(this).attr("id");
    //Definir la acción a realizar en el handler. Esto NO está funcionando.
    var funcion = "insertLikeCom";
    //Tomar control del botón por medio de una variable.
    var button = $(this);
    //Empaquetar la información.
    var paquete = "funcion="+funcion+"&id_com="+id_com;
    console.log("PAQUETE: "+paquete);
    //Ajax.
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxComent.php",
        data: paquete,
    }).done(function(respuesta){
        //Cambiar características del botón.
        button.text("Ya no me gusta");
        console.log("TEXTO CAMBIADO: Ya no me Gusta");
        button.removeClass("btn-info");
        button.removeClass("like");
        console.log("CLASES REMOVIDAS");
        button.addClass("btn-danger");
        button.addClass("dislike");
        console.log("CLASES AÑADIDAS");
        console.log("Respuesta: "+respuesta);
        console.log("ID CANTLIKESCOM : "+document.getElementById("cantLikesCom"+id_com).value);
        //Cambiar la cantidad de likes a la actual. Esta cagá es terrible mañosa ni idea por qué.
        document.getElementById("cantLikesCom"+id_com).value = respuesta;
        console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
        event.stopPropagation();
    }).fail(function(respuesta){
        alert("Error de conexión. Probablemente.");
    })
});

$(document).on("click",".dislike", function(){
    console.log("INICIANDO PROCESO: BOTON DISLIKE");
    //Conseguir el atributo del botón, que es el id del sueño.
    var id_com = $(this).attr("id");
    //Tomar control del botón por medio de una variable.
    var button = $(this);
    //Definir la acción a realizar en el handler. Esto NO está funcionando.
    var funcion = "deleteLikeCom";
    //Empaquetar la información.
    var paquete = "funcion="+funcion+"&id_com="+id_com;
    console.log("PAQUETE: "+paquete);
    //Ajax.
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxComent.php",
        data: paquete,
    }).done(function(respuesta){
        //Cambiar las características del botón.
        button.text("Me gusta");
        console.log("TEXTO CAMBIADO: Me Gusta");
        button.removeClass("btn-danger");
        button.removeClass("dislike");
        console.log("CLASES REMOVIDAS");
        button.addClass("btn-info");
        button.addClass("like");
        console.log("CLASES AÑADIDAS");
        console.log("Respuesta: "+respuesta);
        //Realmente no entiendo como esta función no puede funcionar tranquilamente.
        document.getElementById("cantLikesCom"+id_com).value = respuesta;
        console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
        event.stopPropagation();
    }).fail(function(respuesta){
        alert("Error de conexión. Probablemente.");
    })
});

</script>
</html>