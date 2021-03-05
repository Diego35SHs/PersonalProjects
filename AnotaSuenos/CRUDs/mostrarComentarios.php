<?php
session_start();
require "../config.php";
$id_sue = $_GET["id_sue"];
$funcion = $_GET["funcion"];
//Se encarga de separar las funciones a realizar en cada consulta ajax
switch ($funcion) {
    case "verComentarios":
        verComentarios($link);
        break;
    case "verSueno":
        // echo "<script> alert('Hola'); </script>";
        //1 = Privado -- 0 = Público.
        if (checkPrivacidad($link) == 1) {
            if (checkPropiedad($link) == 1) {
                verSueno($link);
            } else {
                echo "<script> alert('Este sueño es privado y no es tuyo, buen intento. Volviendo a home.'); </script>";
                //Esta función fue tomada desde StackOverflow. Gracias Dan Heberden.
                echo  "<script>";
                echo "parent.changeURL('../home.php' );";
                echo "</script>";
                //Fin.
                die();
            }
        } else {
            //Sueño no privado
            verSueno($link);
        }
        break;
}

//MOD
function checkMod($link)
{
    $sql = "SELECT id_mod FROM modd WHERE id_usu = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_codusu);
        $param_codusu = $_SESSION["id"];
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $registro);
                if (mysqli_stmt_fetch($stmt)) {
                    return 1;
                }
            } else {
                echo "<script>";
                echo "alert('No eres parte del staff. Volviendo a home..');";
                echo "</script>";
                header("location: ../index.php");
            }
        }
    }
}

//Función verSueno
//Input: Ninguno directo - Toma el id del sueño con metodo GET
//Output: Mostrar el sueño
function verSueno($link)
{
    $response = array();
    $result = mysqli_query($link, "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE id_sue =  " . $_GET["id_sue"] . " ");
    if (mysqli_num_rows($result) > 0) {
        $response["suenos"] = array();
        while ($row = mysqli_fetch_array($result)) {
            $temp = array();
            echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
            $cantidadCarac = strlen($row["sueno"]);
            $nombreUsuario = nombreUsuSueno($row["cod_usu"], $link);
            $alto = heightTXA($cantidadCarac);
            //TODO: Ajustes acerca de chequeos, etc, llevar a la par con la función principal.
            $cantLikes = cantidadLikes($row["id_sue"], $link);
            echo "<label>";
            echo "Por : <a href='perfilPublico.php?nom_usu=" . $nombreUsuario . "'>" . $nombreUsuario . "</a>";
            echo "</label>";
            echo "<textarea class='form-control' style='resize:none;" . $alto . "border:none;maxlength:500;background-color: white;' disabled='true'>";
            echo $row["sueno"];
            echo "</textarea> <br>";
            echo "<span>";
            if ($row["sue_pri"] == 0) {
                echo "Sueño público";
            } else if ($row["sue_pri"] == 1) {
                echo "Sueño privado";
            }
            echo "&nbsp;&nbsp;";
            echo "</span>";
            if ($row["sue_m18"] == 1) {
                echo "<span> +18 </span> &nbsp;&nbsp;";
            }
            echo "<span>";
            echo "</span>";
            echo "<span>";
            if (checkLike($row["id_sue"], $row["cod_usu"], $link) == 0) {
                echo "<button id='" . $row['id_sue'] . "' class='likeSue btn btn-info'>Me gusta</button>";
            } else {
                echo "<button id='" . $row['id_sue'] . "' class='dislikeSue btn btn-danger'>Ya no me gusta</button>";
            }
            echo "&nbsp;&nbsp;";
            echo "Me gusta: ";
            echo "<input type='text' disabled='true' id='cantLikes" . $row["id_sue"] . "'  value='" . $cantLikes . "' style='border: none; width: 35px;' >";
            echo "&nbsp;&nbsp;";
            echo "</span>";
            echo "</div>";
            array_push($response["suenos"], $temp);
        }
    } else {
        echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
        echo "No se encontró ningún registro.";
        echo "</div>";
    }
}

function checkPrivacidad($link)
{
    $id_sue = $_GET["id_sue"];
    $sql = "SELECT sue_pri FROM Sueno WHERE id_sue = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id_sue);
        $param_id_sue = $id_sue;
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $privacidad);
                if (mysqli_stmt_fetch($stmt)) {
                    return $privacidad;
                }
            } else {
                return "No se pudo encontrar la privacidad del sueño.";
            }
        }
    }
    return "Posible error de conexión.";
}

//Función checkPropiedad
//Input: Directo: Código de usuario del sueño - Indirecto: Código de usuario en sesión.
//Output: 1: El usuario es propietario del sueño - 0: El usuario NO es propietario del sueño.
function checkPropiedad($link)
{
    $id_sue = $_GET["id_sue"];
    $sql = "SELECT cod_usu FROM Sueno WHERE id_sue = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id_sue);
        $param_id_sue = $id_sue;
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $usuarioProp);
                if (mysqli_stmt_fetch($stmt)) {
                    $id_usu_ses = $_SESSION["id"];
                    if ($usuarioProp == $id_usu_ses) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            }
        }
    }
}

//Función verComentarios
//Input: Ninguno directo - Toma el id del sueño con metodo GET
//Output: Listado de comentarios correspondientes a un sueño
//TODO: Hacer funcionar el sistema de offset.
function verComentarios($link)
{
    // $link = mysqli_connect('localhost','root','','anotasuenos');
    $response = array();
    $result = mysqli_query($link, "SELECT id_com,id_sue,id_usu,comentario FROM Comentario WHERE id_sue = " . $_GET["id_sue"] . " ORDER BY id_com desc ");
    if (mysqli_num_rows($result) > 0) {
        $response["comentarios"] = array();
        while ($row = mysqli_fetch_array($result)) {
            $temp = array();
            $cantidadCarac = strlen($row["comentario"]);
            $nombreUsuario = nombreUsuarioCome($row["id_usu"], $link);
            $alto = heightTXA($cantidadCarac);
            $cantLikes = cantidadLikesCom($row["id_com"], $link);
            echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
            echo "<label>";
            echo "Por: ";
            echo "<a href='perfilPublico.php?nom_usu=" . $nombreUsuario . "'>";
            echo $nombreUsuario;
            echo "</a>";
            echo "</label>";
            echo "<textarea class='form-control' id='textAreaCom" . $row["id_com"] . "' style='resize:none;" . $alto . "border:none;maxlength:500;background-color: white;' disabled='true'>";
            echo $row["comentario"];
            echo "</textarea> <br>";
            echo "<span>";
            if (checkPropiedadCom($row["id_usu"]) == 1) {
                echo "<button id='" . $row["id_com"] . "' class='modificarCome btn btn-warning'><i id='modIcon" . $row["id_com"] . "' class='modIcon fa fa-pencil'></i></button> &nbsp;";
                echo "<button id='" . $row["id_com"] . "' class='eliminarCome btn btn-danger'><i id='modIcon" . $row["id_com"] . "' class='eliIcon fa fa-trash'></i></button> &nbsp;";
            } else if (checkPropiedad($link)  == 1) {
                echo "<button id='" . $row["id_com"] . "' class='eliminarCome btn btn-danger'><i id='modIcon" . $row["id_com"] . "' class='eliIcon fa fa-trash'></i></button> &nbsp;";
            } else if (checkMod($link) == 1) {
                echo "<button id='" . $row["id_com"] . "' class='eliminarCome btn btn-danger'><i id='modIcon" . $row["id_com"] . "' class='eliIcon fa fa-trash'></i></button> &nbsp;";
            }
            if (checkLikeCom($row["id_com"], $row["id_usu"], $link) == 0) {
                echo "<button id='" . $row['id_com'] . "' class='like btn btn-info'>Me gusta</button>";
            } else {
                echo "<button id='" . $row['id_com'] . "' class='dislike btn btn-danger'>Ya no me gusta</button>";
            }
            echo "&nbsp;&nbsp;";
            echo "Me gusta: ";
            echo "<input type='text' disabled='true' id='cantLikesCom" . $row["id_com"] . "'  value='" . $cantLikes . "' style='border: none; width: 35px;' >";
            echo "&nbsp;&nbsp;";
            echo "</span>";
            echo "</div>";
            echo "</br>";
            array_push($response["comentarios"], $temp);
        }
    } else {
        echo "No hay comentarios";
    }
    return null;
}

//Función nombreUsuSueno
//Input: ID de usuario y Link de conexión
//Output: Nombre de usuario correspondiente al sueño.
function nombreUsuSueno($codusu, $link)
{
    $sql = "SELECT nom_usu FROM Login WHERE cod_usu = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_codusu);
        $param_codusu = $codusu;
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $nombreUsuario);
                if (mysqli_stmt_fetch($stmt)) {
                    return $nombreUsuario;
                }
            } else {
                return "No se encontró el usuario";
            }
        }
    }
    return "Usuario no encontrado";
}

//Función nombreUsuarioCome
//Input: ID de usuario y Link de conexión
//Output: Nombre de usuario correspondiente al comentario.
function nombreUsuarioCome($cod_usu, $link)
{
    $sql = "SELECT nom_usu FROM Login WHERE cod_usu = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_codusu);
        $param_codusu = $cod_usu;
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $nombreUsuario);
                if (mysqli_stmt_fetch($stmt)) {
                    return $nombreUsuario;
                }
            } else {
                return "No se encontró el usuario";
            }
        }
    }
    return "Usuario no encontrado";
}

//Función heightTXA
//Input: Cantidad de caracteres del sueño o comentario.
//Output: Alto del textarea que ocupará el comentario.
function heightTXA($cantidadCarac)
{
    $altoTXA = "height:100px;";
    if ($cantidadCarac >= 180) {
        $altoTXA = "height:70px;";
    }
    if ($cantidadCarac >= 200) {
        $altoTXA = "height:130px;";
    }
    if ($cantidadCarac >= 300) {
        $altoTXA = "height:160px;";
    }
    if ($cantidadCarac >= 400) {
        $altoTXA = "height:210px;";
    }
    return $altoTXA;
}

//SUEÑOS
//Función cantidadComentarios 
//Input: ID del sueño y Link de conexion
//Output: Cantidad de comentarios correspondientes al sueño.
function cantidadComentarios($id_sue, $link)
{
    $result = mysqli_query($link, "SELECT count(*) as total FROM Comentario WHERE id_sue = " . $id_sue . " ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//Función cantidadLikes
//Input: ID del sueño y Link de conexión
//Output: Cantidad de "Me gusta" correspondientes al sueño.
function cantidadLikes($id_sue, $link)
{
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = " . $id_sue . " ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//Función checkLike
//Input: ID del sueño, ID de usuario y Link de conexión
//Output: Cantidad de likes dados por un usuario a un sueño.
//Nota: Nunca debe poder se más de 1.
function checkLike($id_sue, $id_usu, $link)
{
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = " . $id_sue . " AND id_usu =" . $id_usu . " ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//COMENTARIOS
//Función cantidadLikesCom
//Input: ID del comentario y Link de conexión
//Output: Cantidad de likes correspondientes al comentario.
function cantidadLikesCom($id_com, $link)
{
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislikeCom WHERE id_com = " . $id_com . " ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//Función checkLikeCom
//Input: ID del comentario, ID del usuario y Link de conexión
//Output: Cantidad de likes dados por un usuario al comentario.
//Nota: Nunca debe poder ser más de uno.
function checkLikeCom($id_com, $id_usu, $link)
{
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislikeCom WHERE id_com = " . $id_com . " AND id_usu =" . $id_usu . " ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

function checkPropiedadCom($id_usu_com)
{
    if ($id_usu_com == $_SESSION["id"]) {
        return 1;
    } else {
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
    //LIKES Y DISLIKES PARA EL SUEÑO.
    //TODO: Arreglar esta cagá que no se por qué no quiere funcionar.

    //Función likeSue en AJAX
    //Input: Atributo ID del botón con clase .likeSue
    //Output: Cambiar clase y texto del botón. Cambiar cantidad de likes
    $(document).on("click", ".likeSue", function() {
        console.log("INICIANDO PROCESO: BOTON LIKE");
        //Conseguir el atributo del botón, que es el id del sueño.
        var id_sue = $(this).attr("id");
        //Definir la acción a realizar en el handler. Esto NO está funcionando.
        var funcion = "insertLike";
        //Tomar control del botón por medio de una variable.
        var button = $(this);
        //Empaquetar la información.
        var paquete = "funcion=" + funcion + "&id_sue=" + id_sue;
        console.log("PAQUETE: " + paquete);
        //Ajax.
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxSuenos.php",
            data: paquete,
        }).done(function(respuesta) {
            //Cambiar características del botón.
            button.text("Ya no me gusta");
            console.log("TEXTO CAMBIADO: Ya no me Gusta");
            button.removeClass("btn-info");
            button.removeClass("like");
            console.log("CLASES REMOVIDAS");
            button.addClass("btn-danger");
            button.addClass("dislike");
            console.log("CLASES AÑADIDAS");
            console.log("Respuesta: " + respuesta);
            //Cambiar la cantidad de likes a la actual. Esta cagá es terrible mañosa ni idea por qué.
            document.getElementById("cantLikes" + id_sue).value = respuesta;
            console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
            event.stopPropagation();
        }).fail(function(respuesta) {
            alert("Error de conexión. Probablemente.");
        })
    });

    $(document).on("click", ".dislikeSue", function() {
        console.log("INICIANDO PROCESO: BOTON DISLIKE");
        //Conseguir el atributo del botón, que es el id del sueño.
        var id_sue = $(this).attr("id");
        //Tomar control del botón por medio de una variable.
        var button = $(this);
        //Definir la acción a realizar en el handler. Esto NO está funcionando.
        var funcion = "deleteLike";
        //Empaquetar la información.
        var paquete = "funcion=" + funcion + "&id_sue=" + id_sue;
        console.log("PAQUETE: " + paquete);
        //Ajax.
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxSuenos.php",
            data: paquete,
        }).done(function(respuesta) {
            //Cambiar características del botón.
            button.text("Me gusta");
            console.log("TEXTO CAMBIADO: Me Gusta");
            button.removeClass("btn-danger");
            button.removeClass("dislike");
            console.log("CLASES REMOVIDAS");
            button.addClass("btn-info");
            button.addClass("like");
            console.log("CLASES AÑADIDAS");
            console.log("Respuesta: " + respuesta);
            console.log("ID CANTLIKESSUE : " + document.getElementById("cantLikes" + id_sue).value);
            //BUG : Cambiar la cantidad de likes a la actual. Esta cagá es terrible mañosa ni idea por qué.
            document.getElementById("cantLikes" + id_sue).value = respuesta;
            console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
            event.stopPropagation();
        }).fail(function(respuesta) {
            alert("Error de conexión. Probablemente.");
        })
    });

    //LIKES Y DISLIKES PARA COMENTARIOS.
    $(document).on("click", ".like", function() {
        console.log("INICIANDO PROCESO: BOTON LIKE");
        //Conseguir el atributo del botón, que es el id del sueño.
        var id_com = $(this).attr("id");
        var id_sue = getParameterByName("id_sue");
        //Definir la acción a realizar en el handler. Esto NO está funcionando.
        var funcion = "insertLikeCom";
        //Tomar control del botón por medio de una variable.
        var button = $(this);
        //Empaquetar la información.
        var paquete = "funcion=" + funcion + "&id_com=" + id_com + "&id_sue=" + id_sue;
        console.log("PAQUETE: " + paquete);
        //Ajax.
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxComent.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxComent.php",
            data: paquete,
        }).done(function(respuesta) {
            //Cambiar características del botón.
            button.text("Ya no me gusta");
            console.log("TEXTO CAMBIADO: Ya no me Gusta");
            button.removeClass("btn-info");
            button.removeClass("like");
            console.log("CLASES REMOVIDAS");
            button.addClass("btn-danger");
            button.addClass("dislike");
            console.log("CLASES AÑADIDAS");
            console.log("Respuesta: " + respuesta);
            console.log("ID CANTLIKESCOM : " + document.getElementById("cantLikesCom" + id_com).value);
            //Cambiar la cantidad de likes a la actual. Esta cagá es terrible mañosa ni idea por qué.
            document.getElementById("cantLikesCom" + id_com).value = respuesta;
            console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
            event.stopPropagation();
        }).fail(function(respuesta) {
            alert("Error de conexión. Probablemente.");
        })
    });

    $(document).on("click", ".dislike", function() {
        console.log("INICIANDO PROCESO: BOTON DISLIKE");
        //Conseguir el atributo del botón, que es el id del sueño.
        var id_com = $(this).attr("id");
        //Tomar control del botón por medio de una variable.
        var button = $(this);
        //Definir la acción a realizar en el handler. Esto NO está funcionando.
        var funcion = "deleteLikeCom";
        //Empaquetar la información.
        var paquete = "funcion=" + funcion + "&id_com=" + id_com;
        console.log("PAQUETE: " + paquete);
        //Ajax.
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxComent.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxComent.php",
            data: paquete,
        }).done(function(respuesta) {
            //Cambiar las características del botón.
            button.text("Me gusta");
            console.log("TEXTO CAMBIADO: Me Gusta");
            button.removeClass("btn-danger");
            button.removeClass("dislike");
            console.log("CLASES REMOVIDAS");
            button.addClass("btn-info");
            button.addClass("like");
            console.log("CLASES AÑADIDAS");
            console.log("Respuesta: " + respuesta);
            //Realmente no entiendo como esta función no puede funcionar tranquilamente.
            document.getElementById("cantLikesCom" + id_com).value = respuesta;
            console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
            event.stopPropagation();
        }).fail(function(respuesta) {
            alert("Error de conexión. Probablemente.");
        })
    });

    //Función para "Modificar" un sueño.
    //Esta función se encarga de simplemente cambiar los atributos del botón y del textarea
    //correspondiente al sueño para permitir que el usuario modifique su sueño y tenga la opción
    //de guardarlo.
    $(document).on("click", ".modificarCome", function() {
        console.log("Botón modificar sueño - Iniciado");
        var id_com = $(this).attr("id");
        console.log("ID BUTTON Y SUEÑO = " + id_sue);
        var button = $(this);
        // button.text("Guardar");
        console.log("Quitar clases actuales referentes al funcionamiento del botón");
        button.removeClass("modificarCome");
        button.removeClass("btn-warning");
        console.log("Añadir clases necesarias para el nuevo funcionamiento del botón");
        button.addClass("btn-info");
        button.addClass("guardarCambiosCome");
        //Conseguir el texto del sueño para luego utilizarlo de alguna forma.
        var textArea = document.getElementById("textAreaCom" + id_com).innerHTML;
        console.log(textArea);
        //Tomar control sobre el textarea por medio de su id.
        var controlTXA = document.getElementById("textAreaCom" + id_com);
        controlTXA.style = "border-stiyle:solid;border-color:black;resize:none;";
        controlTXA.removeAttribute("disabled");
        event.stopPropagation();
    });

    //Función para "Guardar Cambios" efectuados al sueño.
    //Esta función se encarga de procesar el cambio hecho al sueño
    //tomará lo que el usuario haya escrito en el textarea, luego hará que vuelva a su estado original
    //y con ese dato hará la consulta update y luego mostrará el nuevo valor en el textarea.
    $(document).on("click", ".guardarCambiosCome", function() {
        console.log("Botón guardar cambios comentario - Iniciando");
        var id_com = $(this).attr("id");
        var icon = document.getElementsByClassName("modIcon");
        console.log("ID BOTÓN Y COMENTARIO = " + id_com);
        //Tomar control del botón y del textarea
        var button = $(this);
        var controlTXA = document.getElementById("textAreaCom" + id_com);
        //Conseguir valor nuevo del textarea
        var textArea = controlTXA.value;
        var alto = heightTXA(textArea.length);
        if (textArea == null || textArea == '') {
            alert("El comentario no puede estar vacío");
            return null;
        } else if (textArea.length > 500) {
            alert("El comentario no puede pasar de 500 caracteres. Tienes " + textArea.length);
            return null;
        }
        //empaquetar la información
        var paquete = "funcion=modificarComentario&id_com=" + id_com + "&nuevoCom=" + textArea;
        console.log(paquete);
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxComent.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxComent.php",
            data: paquete,
        }).done(function(respuesta) {
            console.log("Quitar clases actuales referentes al funcionamiento del botón");
            button.removeClass("btn-info");
            button.removeClass("guardarCambiosCome");
            console.log("Añadir clases necesarias para el nuevo funcionamiento del botón");
            button.addClass("modificarCome");
            button.addClass("btn-warning");
            document.getElementById("textAreaCom" + id_com).innerHTML = respuesta;
            console.log("RESPUESTA: " + respuesta);
            controlTXA.style = "border:none;resize:none;background-color:white;" + alto + "";
            controlTXA.setAttribute("disabled", "true");
            console.log("Botón guardar cambios comentario - Finalizado");
            alert("Comentario modificado.");
            event.stopPropagation();
        }).fail(function(respuesta) {
            document.getElementById("textAreaCom" + id_com).innerHTML = respuesta;
        });
    });

    //funcion heightTXA 
    //Input: cántidad de caracteres de un sueño
    //Output: Altura de textarea que ocupará el sueño.
    //NOTA: Esto no se está usando todavía. Pero en teoría es utilizable por medio de Ajax.
    function heightTXA($cantidadCarac) {
        $altoTXA = "height:100px;";
        if ($cantidadCarac >= 180) {
            $altoTXA = "height:70px;";
        }
        if ($cantidadCarac >= 200) {
            $altoTXA = "height:130px;";
        }
        if ($cantidadCarac >= 300) {
            $altoTXA = "height:160px;";
        }
        if ($cantidadCarac >= 400) {
            $altoTXA = "height:210px;";
        }
        return $altoTXA;
    }

    //Función tomada desde Stack Overflow - Usuario Dan Heberden
    function changeURL(url) {
        document.location = url;
    }
    //Fin.
    //Función tomada desde Stack Overflow - Usuario Chofoteddy
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
    //Fin
</script>

</html>