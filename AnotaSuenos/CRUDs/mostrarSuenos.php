<?php

session_start();
require "../config.php";

$function = $_GET["function"];

switch ($function) {
    case "mostrarSuenosNPVNM18":
        // $offsetQuery = $_GET["offset"];
        // $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 0 ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
        // mostrarSuenosGeneric($query, $link);
        break;
    case "mostrarSueCustomQuery":
        prepararQuery($link);
        break;
}

//Preparar consulta.
//Este switch es en extremo importante, es aquí donde se elegirá la consulta pre-hecha 
//a utilizar en la función "mostrarSuenosGeneric"
function prepararQuery($link)
{
    $opcion = $_GET["opcion"];
    $offsetQuery = $_GET["offset"];
    switch ($opcion) {
            //PARA USO COMÚN:
        case "noPVnoM18":
            //Sueños que no sean privados y tampoco +18.
            //Esta es la opción que se usa en home.php
            $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 0 ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
            mostrarSuenosGeneric($query, $link);
            break;
        case "noPVsiM18":
            $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 1 ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
            mostrarSuenosGeneric($query, $link);
            break;
        //PARA USO EN PERFIL DE USUARIO
        case "noPVnoM18User":
            //Sueños que no sean privados y tampoco sean +18 del usuario dueño del perfil.
            //Deben aparecer solo sueños públicos.
            $cod_usu = $_GET["cod_usu"];
            $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 0 AND cod_usu = " . $cod_usu . " ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
            mostrarSuenosGeneric($query, $link);
            break;
        case "noPVsiM18User":
            //Sueños que no sean privados y sean o no +18 del usuario dueño del perfil.
            //Deben aparecer sueños públicos y sueños públicos +18
            $cod_usu = $_GET["cod_usu"];
            $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND (sue_m18 = 1 OR sue_m18 = 0) AND cod_usu = " . $cod_usu . " ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
            mostrarSuenosGeneric($query, $link);
            break;
        case "todosUser":
            //Todos los sueños que ha publicado el usuario, juntos.
            //Esta opción es exclusiva para el usuario en sesión que visite su perfil y solo aparecerá si el chequeo de propiedad da 1, es decir, positivo.
            //Deben aparecer todos los sueños del usuario.
            $cod_usu = $_GET["cod_usu"];
            if($cod_usu == $_SESSION["id"]){
                $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE cod_usu = " . $cod_usu . " ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
                mostrarSuenosGeneric($query, $link);
            }else{
                echo "<script> alert('Si de verdad quieres ver lo que este usuario no quiere que veas, pídeselo, a ver que dice. Volviendo a home.'); </script>";
                //Esta función fue tomada desde StackOverflow. Gracias Dan Heberden.
                echo  "<script>";
                echo "parent.changeURL('../home.php' );";
                echo "</script>";
                //Fin.
            }
            break;
        case "soloPVUser":
            //Los sueños que sean privados, sin importar si son +18 o no del usuario.
            //Opción exclusiva para el usuario en sesión que visita su propio perfil.
            //Deben aparecer todos los sueños privados, sin importar si son +18 o no.
            $cod_usu = $_GET["cod_usu"];
            if($cod_usu == $_SESSION["id"]){
                $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 1 AND cod_usu = " . $cod_usu . " ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
                mostrarSuenosGeneric($query, $link);
            }else{
                echo "<script> alert('¿Inspeccionar elemento? Casi resulta. Volviendo a home.'); </script>";
                //Esta función fue tomada desde StackOverflow. Gracias Dan Heberden.
                echo  "<script>";
                echo "parent.changeURL('../home.php' );";
                echo "</script>";
                //Fin.
            }
            break;
        case "soloM18User":
            //Los sueños que sean públicos y +18.
            //Opción disponible para todos los usuarios
            //Deben aparecer solo sueños +18 y solo públicos.
            $cod_usu = $_GET["cod_usu"];
            $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND sue_m18 = 1 AND cod_usu = " . $cod_usu . " ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
            mostrarSuenosGeneric($query, $link);
            break;
        case "soloSeguidosNoM18":
            //Los sueños correspondientes a los usuarios seguidos por el usuario en sesión
            //Opción disponible para todos los usuarios.
            //Esta opción NO funciona hasta ahora.
            $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,Sueno.cod_usu FROM Sueno,Seguidores,Login WHERE Sueno.cod_usu = Seguidores.id_usu_sdo AND Seguidores.id_usu_sdr = ".$_SESSION["id"]." AND Sueno.sue_pri = 0 AND Sueno.sue_m18 = 0 ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
            mostrarSuenosGeneric($query, $link);
            break;
        case "modSue":
            if(checkMod($link) <= 0 || checkMod($link) > 1){
                echo "<script> alert('Se agradece el intento, pero por favor, nada más. Volviendo a home.'); </script>";
                //Esta función fue tomada desde StackOverflow. Gracias Dan Heberden.
                echo  "<script>";
                echo "parent.changeURL('../home.php' );";
                echo "</script>";
                //Fin.
            }else{
                $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno ORDER BY id_sue desc";
                mostrarSuenosEspecial($query, $link);
            }
            break;
        case "busqueda":
            $busqueda = $_GET["termBusqueda"];
            $query = "SELECT id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 AND (sue_m18 = 1 OR sue_m18 = 0) AND sueno LIKE '%".$busqueda."%' ORDER BY fec_sue DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
            mostrarSuenosGeneric($query,$link);
            break;
        case "masPopulares":
            //840 líneas con 40 miserables registros? No es aceptable, esta función queda en espera hasta que se resuelva el tema con los likes y dislikes.
            //Posibles soluciones: añadir la columna de cantidad de likes a los sueños y actualizarla al momento de dar like o dislike a un sueño.
            //esto podría habilitar la función para ordenar por cantidad de comentarios
            //pero será un cambio más o menos grande comparado al sistema que ya está en funcionamiento
            //No para mostrar, sino para retocar estos aspectos internos que el usuario no ve.
            $query = "SELECT Sueno.id_sue,sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM Sueno WHERE sue_pri = 0 ORDER BY megusta DESC LIMIT 10 OFFSET " . $offsetQuery . " ";
            mostrarSuenosGeneric($query,$link);
            break;
    }
}


//MOD
function checkMod($link){
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

function checkPrivacidad($link,$id_sue){
    $sql = "SELECT sue_pri FROM Sueno WHERE id_sue = ?";
    if($stmt = mysqli_prepare($link,$sql)){
        mysqli_stmt_bind_param($stmt,"i",$param_id_sue);
        $param_id_sue = $id_sue;
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1){
                mysqli_stmt_bind_result($stmt,$privacidad);
                if(mysqli_stmt_fetch($stmt)){
                    return $privacidad;
                }
            }else{
                return "No se pudo encontrar la privacidad del sueño.";
            }
        }
    }
    return "Posible error de conexión.";
}

//Función genérica para mostrar sueños:
//INPUT: Directo - Consulta SQL preparada en el switch y link de conexión.
//OUTPUT: Los sueños
//NOTA: Esta función es extremadamente importante.
function mostrarSuenosGeneric($query, $link)
{
    $response = array();
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) > 0) {
        $response["suenos"] = array();
        while ($row = mysqli_fetch_array($result)) {
            $temp = array();
            echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
            $cantidadCarac = strlen($row["sueno"]);
            $nombreUsuario = nombreUsuSueno($row["cod_usu"], $link);
            $alto = heightTXA($cantidadCarac);
            $cantComentarios = cantidadComentarios($row["id_sue"], $link);
            $cantLikes = cantidadLikes($row["id_sue"], $link);
            echo "<label>Por: <a href='../CRUDs/perfilPublico.php?cod_usu=" . $row["cod_usu"] . "'>".$nombreUsuario."</a></label>";
            echo "<textarea class='form-control' id='textAreaSue" . $row["id_sue"] . "' style='resize:none;" . $alto . "border:none;maxlength:500;background-color:white;text-color:black;' disabled='true'>".$row["sueno"]."</textarea> <br>";
            if ($row["sue_pri"] == 0) {
                echo "<span id='privaSue".$row["id_sue"]."'>Sueño público</span>";
            } else {
                echo "<span id='privaSue".$row["id_sue"]."'>Sueño privado</span>";
            }
            echo "&nbsp;&nbsp;";
            if ($row["sue_m18"] == 1) {
                echo "<span id='estadoMas18".$row["id_sue"]."'> +18 </span> &nbsp;&nbsp;";
            }else{
                echo "<span id='estadoMas18".$row["id_sue"]."'></span> &nbsp;";
            }
            echo "<span>";
            if (checkPropiedad($row["cod_usu"]) == 1) {
                if ($row["sue_m18"] == 1) {
                    echo "<span><button id='" . $row["id_sue"] . "' class='setNoMas18 btn btn-danger'>No +18</button> </span> &nbsp;";
                }else{
                    echo "<span><button id='" . $row["id_sue"] . "' class='setMas18 btn btn-warning'>+18</button> </span> &nbsp;";
                }
                echo "<button id='" . $row["id_sue"] . "' class='modificar btn btn-warning'><i id='modIcon".$row["id_sue"]."' class='modIcon fa fa-pencil'></i></button> &nbsp;";
                echo "<button id='" . $row["id_sue"] . "' class='eliminar btn btn-danger'><i id='modIcon".$row["id_sue"]."' class='eliIcon fa fa-trash'></i></button> &nbsp;";
                if(checkPrivacidad($link,$row["id_sue"]) == 1){
                    echo "<button id='" . $row["id_sue"] . "' class='desprivatizar btn btn-danger'><i id='modIcon".$row["id_sue"]."' class='eliIcon fa fa-lock'></i></button> &nbsp;";
                }else{
                    echo "<button id='" . $row["id_sue"] . "' class='privatizar btn btn-warning'><i id='modIcon".$row["id_sue"]."' class='eliIcon fa fa-lock'></i></button> &nbsp;";
                }
            }
            echo "</span>";
            echo "<span>";
            if (checkLike($row["id_sue"], $_SESSION["id"], $link) == 0) {
                echo "<button id='" . $row['id_sue'] . "' class='like btn btn-info'>Me gusta</button>";
            } else {
                echo "<button id='" . $row['id_sue'] . "' class='dislike btn btn-danger'>Ya no me gusta</button>";
            }
            echo "&nbsp;&nbsp;Me gusta: <input type='text' disabled='true' id='cantLikes" . $row["id_sue"] . "' class='cantLikes' value='" . $cantLikes . "' style='border: none; width: 35px; background-color:white;' >";
            echo "&nbsp;&nbsp;</span>";
            echo "<a  class='btn btn-info another-element' href='../CRUDs/verComentarios.php?id_sue=" . $row["id_sue"] . " '>Comentarios (" . $cantComentarios . ")</a>";
            echo "</div> </br>";
            array_push($response["suenos"], $temp);
        }
    } else {
        echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
        echo "<p>No se encontró ningún registro.</p>";
        echo "</div> <br>";
    }
}

//Función especial para mostrar sueños:
//Esta función es identica a mostrarSuenosGeneric, simplemente se salta algunos chequeos
function mostrarSuenosEspecial($query, $link)
{
    $response = array();
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) > 0) {
        $response["suenos"] = array();
        while ($row = mysqli_fetch_array($result)) {
            $temp = array();
            echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
            $cantidadCarac = strlen($row["sueno"]);
            $nombreUsuario = nombreUsuSueno($row["cod_usu"], $link);
            $alto = heightTXA($cantidadCarac);
            $cantComentarios = cantidadComentarios($row["id_sue"], $link);
            $cantLikes = cantidadLikes($row["id_sue"], $link);
            echo "<label>Por: <a href='../CRUDs/perfilPublico.php?cod_usu=" . $row["cod_usu"] . "'>".$nombreUsuario."</a></label>";
            echo "<textarea class='form-control' id='textAreaSue" . $row["id_sue"] . "' style='resize:none;" . $alto . "border:none;maxlength:500;background-color:white;text-color:black;' disabled='true'>";
            echo $row["sueno"];
            echo "</textarea> <br>";
            if ($row["sue_pri"] == 0) {
                echo "<span id='privaSue".$row["id_sue"]."'>Sueño público</span>";
            } else {
                echo "<span id='privaSue".$row["id_sue"]."'>Sueño privado</span>";
            }
            echo "&nbsp;&nbsp;";
            if ($row["sue_m18"] == 1) {
                echo "<span id='estadoMas18".$row["id_sue"]."'> +18 </span> &nbsp;&nbsp;";
                echo "<span><button id='" . $row["id_sue"] . "' class='setNoMas18 btn btn-danger'>No +18</button> </span> &nbsp;";
            }else{
                echo "<span id='estadoMas18".$row["id_sue"]."'>No +18 </span> &nbsp;&nbsp;";
                echo "<span><button id='" . $row["id_sue"] . "' class='setMas18 btn btn-warning'>+18</button> </span> &nbsp;";
            }
            //SECCIÓN Modificación - Eliminación - Privatización
            echo "<span>";
                // echo "<button id='" . $row["id_sue"] . "' class='modificar btn btn-warning'><i id='modIcon".$row["id_sue"]."' class='modIcon fa fa-pencil'></i></button> &nbsp;";
                echo "<button id='" . $row["id_sue"] . "' class='eliminar btn btn-danger'><i id='modIcon".$row["id_sue"]."' class='eliIcon fa fa-trash'></i></button> &nbsp;";
                //Implementar un chequeo en la versión Generic para buscar si el usuario es dueño o no.
                if(checkPrivacidad($link,$row["id_sue"]) == 1){
                    echo "<button id='" . $row["id_sue"] . "' class='desprivatizar btn btn-danger'><i id='modIcon".$row["id_sue"]."' class='eliIcon fa fa-lock'></i></button> &nbsp;";
                }else{
                    echo "<button id='" . $row["id_sue"] . "' class='privatizar btn btn-warning'><i id='modIcon".$row["id_sue"]."' class='eliIcon fa fa-lock'></i></button> &nbsp;";
                }
            echo "</span>";
            //Fin sección
            echo "<span>";
            echo "&nbsp;&nbsp;Me gusta: <input type='text' disabled='true' id='cantLikes" . $row["id_sue"] . "' class='cantLikes' value='" . $cantLikes . "' style='border: none; width: 35px; background-color:white;' >&nbsp;&nbsp;</span>";
            echo "<a  class='btn btn-info another-element' href='../CRUDs/verComentarios.php?id_sue=" . $row["id_sue"] . " '>Comentarios (" . $cantComentarios . ")</a>";
            echo "</div> </br>";
            array_push($response["suenos"], $temp);
        }
    } else {
        echo "<div class='border border-info rounded p-3' style='width: 100%; background-color: white;'>";
        echo "<p>No se encontró ningún registro.</p>";
        echo "</div> <br>";
    }
}


//funcion heightTXA 
//Input: cántidad de caracteres de un sueño
//Output: Altura de textarea que ocupará el sueño.
function heightTXA($cantidadCarac)
{
    $altoTXA = "height:100px;";
    if ($cantidadCarac <= 100) {
        $altoTXA = "height:84px;";
    }
    if ($cantidadCarac > 100 && $cantidadCarac <= 200) {
        $altoTXA = "height:135px;";
    }
    if ($cantidadCarac > 200 && $cantidadCarac <= 300) {
        $altoTXA = "height:190px;";
    }
    if ($cantidadCarac > 300 && $cantidadCarac <= 400) {
        $altoTXA = "height:160px;";
    }
    if ($cantidadCarac > 400) {
        $altoTXA = "height:300px;";
    }
    return $altoTXA;
}

//Función nombreUsuSueno
//Input: Código de usuario y Link de conexión.
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

//Función cantidadComentarios
//Input: Código de sueño y Link de conexión.
//Output: Cantidad de comentarios correspondientes al sueño.
function cantidadComentarios($id_sue, $link)
{
    $result = mysqli_query($link, "SELECT count(*) as total FROM Comentario WHERE id_sue = " . $id_sue . " ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//Función cantidadLikes
//Input: Código de sueño y Link de conexión.
//Output: Cantidad de likes correspondientes al sueño.
function cantidadLikes($id_sue, $link)
{
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = " . $id_sue . " ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//Función checkLike
//Input: Código de sueño, Código de usuario y Link de conexión
//Output: Cantidad de likes que el usuario en sesión ha dado al sueño.
//Nota: No puede ser mayor que 1 ni menor que 0.
function checkLike($id_sue, $id_usu, $link)
{
    $result = mysqli_query($link, "SELECT count(*) as total FROM LikeDislike WHERE id_sue = " . $id_sue . " AND id_usu =" . $id_usu . " ");
    $data = mysqli_fetch_assoc($result);
    return $data["total"];
}

//Función checkPropiedad
//Input: Directo: Código de usuario del sueño - Indirecto: Código de usuario en sesión.
//Output: 1: El usuario es propietario del sueño - 0: El usuario NO es propietario del sueño.
function checkPropiedad($id_usu)
{
    $id_usu_sue = $id_usu;
    $id_usu_ses = $_SESSION["id"];
    if ($id_usu_sue == $id_usu_ses) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
</body>
<script>
    //Función "Like" o "Me Gusta" para sueños.
    //Esta función se encarga de atender la solicitud del usuario (Dar "Me Gusta" a un sueño) y mostrar
    //el resultado cambiando el texto y clase del botón recién presionado y cambiando la cantidad
    //de "Me Gusta" que ha recibido el sueño a la actual.
    $(document).on("click", ".like", function() {
        console.log("INICIANDO PROCESO: BOTON LIKE");
        var id_sue = $(this).attr("id");
        var funcion = "insertLike";
        var button = $(this);
        //alert("AGGA"+id_sue);
        var paquete = "funcion=" + funcion + "&id_sue=" + id_sue;
        console.log("PAQUETE: " + paquete);
        //alert(paquete);
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            data: paquete,
        }).done(function(respuesta) {
            button.text("Ya no me gusta");
            console.log("TEXTO CAMBIADO: Ya no me Gusta");
            button.removeClass("btn-info");
            button.removeClass("like");
            console.log("CLASES REMOVIDAS");
            button.addClass("btn-danger");
            button.addClass("dislike");
            console.log("CLASES AÑADIDAS");
            console.log("Respuesta: " + respuesta);
            document.getElementById("cantLikes" + id_sue).value = respuesta;
            console.log("CANTIDAD DE ME GUSTA ACTUALIZADA");
            event.stopPropagation();
        }).fail(function(respuesta) {
            alert("Error de conexión. Probablemente.");
        });
    });

    //Función "DisLike" o "No Me Gusta" para sueños.
    //Esta función se encarga de atender la solicitud del usuario (Dar "No Me Gusta" a un sueño) y mostrar
    //el resultado cambiando el texto y clase del botón recién presionado y cambiando la cantidad
    //de "No Me Gusta" que ha recibido el sueño a la actual.
    $(document).on("click", ".dislike", function() {
        console.log("INICIANDO PROCESO: BOTON DISLIKE");
        var id_sue = $(this).attr("id");
        var button = $(this);
        var funcion = "deleteLike";
        var paquete = "funcion=" + funcion + "&id_sue=" + id_sue;
        console.log("PAQUETE: " + paquete);
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            data: paquete,
        }).done(function(respuesta) {
            button.text("Me gusta");
            console.log("TEXTO CAMBIADO: Me Gusta");
            button.removeClass("btn-danger");
            button.removeClass("dislike");
            console.log("CLASES REMOVIDAS");
            button.addClass("btn-info");
            button.addClass("like");
            console.log("CLASES AÑADIDAS");
            console.log("Respuesta: " + respuesta);
            document.getElementById("cantLikes" + id_sue).value = respuesta;
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
    $(document).on("click", ".modificar", function() {
        console.log("Botón modificar sueño - Iniciado");
        var id_sue = $(this).attr("id");
        console.log("ID BUTTON Y SUEÑO = " + id_sue);
        var button = $(this);
        // button.text("Guardar");
        // button.text("<i class='modIcon fa fa-save'></i>");
        console.log("Quitar clases actuales referentes al funcionamiento del botón");
        button.removeClass("modificar");
        button.removeClass("btn-warning");
        // document.getElementsById("modIcon"+id_sue).removeClass("fa-pencil");
        // document.getElementsById("modIcon"+id_sue).addClass("fa-save");
        console.log("Añadir clases necesarias para el nuevo funcionamiento del botón");
        button.addClass("btn-info");
        button.addClass("guardarCambios");
        //Conseguir el texto del sueño para luego utilizarlo de alguna forma.
        var textArea = document.getElementById("textAreaSue" + id_sue).innerHTML;
        console.log(textArea);
        //Tomar control sobre el textarea por medio de su id.
        var controlTXA = document.getElementById("textAreaSue" + id_sue);
        controlTXA.style = "border-stiyle:solid;border-color:black;resize:none;";
        controlTXA.removeAttribute("disabled");
        event.stopPropagation();
    });

    //Función para "Guardar Cambios" efectuados al sueño.
    //Esta función se encarga de procesar el cambio hecho al sueño
    //tomará lo que el usuario haya escrito en el textarea, luego hará que vuelva a su estado original
    //y con ese dato hará la consulta update y luego mostrará el nuevo valor en el textarea.
    $(document).on("click", ".guardarCambios", function() {
        console.log("Botón guardar cambios sueño - Iniciando");
        var id_sue = $(this).attr("id");
        var icon = document.getElementsByClassName("modIcon");
        console.log("ID BOTÓN Y SUEÑO = " + id_sue);
        //Tomar control del botón y del textarea
        var button = $(this);
        var controlTXA = document.getElementById("textAreaSue" + id_sue);
        //Conseguir valor nuevo del textarea
        var textArea = controlTXA.value;
        var alto = heightTXA(textArea.length);
        if (textArea == null || textArea == '') {
            alert("El sueño no puede estar vacío");
            return null;
        } else if (textArea.length > 500) {
            alert("El sueño no puede pasar de 500 caracteres. Tienes " + textArea.length);
            return null;
        }
        //empaquetar la información
        var paquete = "funcion=modificarSueno&id_sue=" + id_sue + "&nuevoSue=" + textArea;
        console.log(paquete);
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            data: paquete,
        }).done(function(respuesta) {
            // button.text("Modificar");
            console.log("Quitar clases actuales referentes al funcionamiento del botón");
            button.removeClass("btn-info");
            button.removeClass("guardarCambios");
            console.log("Añadir clases necesarias para el nuevo funcionamiento del botón");
            button.addClass("modificar");
            button.addClass("btn-warning");
            document.getElementById("textAreaSue" + id_sue).innerHTML = respuesta;
            console.log("RESPUESTA: " + respuesta);
            controlTXA.style = "border:none;resize:none;background-color:white;" + alto + "";
            controlTXA.setAttribute("disabled", "true");
            console.log("Botón guardar cambios sueño - Finalizado");
            alert("Sueño modificado.");
            event.stopPropagation();
        }).fail(function(respuesta) {
            document.getElementById("textAreaSue" + id_sue).innerHTML = respuesta;
        });
    });

    //Privatizar un sueño.
    //Tomará el sueño correspondiente y lo hará privado.
    $(document).on("click",".privatizar",function(){
        var id_sue = $(this).attr("id");
        var button = $(this);
        var paquete = "funcion=privatizar&id_sue="+id_sue;
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            data: paquete,
        }).done(function(respuesta){
            button.removeClass("btn-warning");
            button.removeClass("privatizar");
            button.addClass("btn-danger");
            button.addClass("desprivatizar");
            document.getElementById("privaSue"+id_sue).innerHTML = "Sueño privado";
            event.stopPropagation();
        }).fail(function(respuesta){
            alert("No se pudo privatizar el sueño");
        });
    });

    //Desprivatizar un sueño
    //Tomará el sueño correspondiente y lo hará no privado
    $(document).on("click",".desprivatizar",function(){
        var id_sue = $(this).attr("id");
        var button = $(this);
        var paquete = "funcion=desprivatizar&id_sue="+id_sue;
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            data: paquete,
        }).done(function(respuesta){
            button.removeClass("btn-danger");
            button.removeClass("desprivatizar");
            button.addClass("btn-warning");
            button.addClass("privatizar");
            document.getElementById("privaSue"+id_sue).innerHTML = "Sueño público";
            event.stopPropagation();
        }).fail(function(respuesta){
            alert("No se pudo desprivatizar el sueño");
        });
    });

    $(document).on("click",".setMas18",function(){
        console.log("=======Cambiando sueño a +18==========");
        var cod_sue = $(this).attr("id");
        var button = $(this);
        var paquete = "funcion=setMas18&id_sue="+cod_sue;
        console.log(paquete);
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            data: paquete,
        }).done(function(respuesta){
            button.text("No +18");
            button.removeClass("btn-warning");
            button.removeClass("setMas18");
            button.addClass("btn-danger");
            button.addClass("setNoMas18");
            document.getElementById("estadoMas18"+cod_sue).innerHTML = "+18";
            console.log("Operación completada");
            event.stopPropagation();
        }).fail(function(respuesta){
            alert("No se pudo completar la acción: setMas18");
        });
    });

    $(document).on("click",".setNoMas18",function(){
        console.log("=======Cambiando sueño a no +18==========");
        var cod_sue = $(this).attr("id");
        var button = $(this);
        var paquete = "funcion=setNoMas18&id_sue="+cod_sue;
        console.log(paquete);
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            data: paquete,
        }).done(function(respuesta){
            button.text("+18");
            button.removeClass("btn-danger");
            button.removeClass("setNoMas18");
            button.addClass("btn-warning");
            button.addClass("setMas18");
            document.getElementById("estadoMas18"+cod_sue).innerHTML = "No +18";
            console.log("Operación completada");
            event.stopPropagation();
        }).fail(function(respuesta){
            alert("No se pudo completar la acción: setNoMas18");
        });
    });

    //Esta función existe en formato PHP, pero para aplicar el estilo al modificar un comentario
    //Es necesaria esta copia en javascript. Hacen lo mismo, pero usarla en formato php significaría
    //Usar ajax con redirección a esta misma página, es posible, y de hecho podría implementarse una función
    //en handlerAuxSuenos.php que se encargue de esto siempre que sea necesario, de momento, esta solución
    //está funcionando y correctamente.
    function heightTXA(cantidadCarac) {
        var altoTXA = "height:100px;";
        if (cantidadCarac <= 100) {
            altoTXA = "height:84px;";
        }
        if (cantidadCarac > 100 && cantidadCarac <= 200) {
            altoTXA = "height:135px;";
        }
        if (cantidadCarac > 200 && cantidadCarac <= 300) {
            altoTXA = "height:190px;";
        }
        if (cantidadCarac > 300 && cantidadCarac <= 400) {
            altoTXA = "height:160px;";
        }
        if (cantidadCarac > 400) {
            altoTXA = "height:300px;";
        }
        return altoTXA;

    }

    //Función tomada desde Stack Overflow - Usuario Dan Heberden
    function changeURL( url ) {
        document.location = url;
    }
    //Fin.
</script>
</html>