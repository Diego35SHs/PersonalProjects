<?php 
require "../config.php";
//require "CRUDs/mostrarSuenos.php";
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<html>
<!-- INICIO HEAD -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnotaSueños - Inicio</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="../estilo.css">
</head>
<!-- FIN HEAD -->
<!-- INICIO BODY -->
<body style="background-color: #48BEFF;">
    <div class="container">
        <div class="col-md-12 col-lg-12 p-3">
            <h1>Sueños</h1>
            <p>Este es un trabajo en progreso. Todo está sujeto a cambios, especialmente el mal código.</p>
        </div>
        <div class="row">
            <div class="col-md-8"> 
                <div style="display:none;">
                <?php echo "<input type='hidden' style='display:none;' id='id_sue' value=".$_GET["id_sue"]." '>";?>
                </div>
                <div id="contenedorSueno"  style="background-color: white;">
                </div> <br>

                <div id="cantidadSuenos" class="text-center border border-info rounded p-1" style="background-color: white; height: 40px;">
                    <span>Actualmente hay </span>
                    <span id="cantidadTotalSuenos"></span>
                    <span> comentarios.</span>
                    </div> <br>
                <div id="contenedorAgregarComentario" class="border border-info rounder p-3" style="background-color:white;">
                    <p>Comentario:</p>
                    <textarea name="txtComentario" id="txtComentario" cols="30" rows="10" style="width:100%; height: 80px; resize:none;" placeholder="¿Qué opinas?"></textarea><br>
                    <button id="publicarComentario" class="btn btn-primary">Comentar</button> <br>
                    <p id="Resultado"></p>
                </div> <br>
                <div id="contenedorComentarios" >
                    <p>Cargando comentarios...</p>
                </div> <br>
                <div id="listContainer" class="border border-info rounded p-3" style="width:100%;background-color:white;"> 
                    <button id="anteriores10" class="btn btn-info">Anteriores 10</button>
                    <span>Mostrando: </span>
                    <span id="offsetDisplay">-----------</span>
                    <span> - </span>
                    <span id="offsetLimDisplay">--------</span>
                    <button id="siguientes10" class="btn btn-primary">Siguientes 10</button>
                </div>
            </div>
            <div class="col-md-4">
                <div id="contenedorMiniPerfil" class="center border border-info rounded p-3" style="background-color:white;">
                    <span><img src="https://img.icons8.com/ios-filled/50/000000/help.png" width="50px" height="50px" alt="FDP" /></span>
                    <span id="nomUsuMiniPerfil">Nombre de usuario</span><br>
                    <span id="cantidadSuenosUsu">AGGA</span>
                </div>
            </div>
        </div>
    </div>
    <a href="Registro\logout.php" class="btn btn-danger">Cerrar sesión</a>
</body>
<script>
$(document).ready(function(){
    mostrarComentarios();
    mostrarSueno();
});

function mostrarComentarios(){
    var id_sue = document.getElementById("id_sue").value;
    console.log(id_sue);
    paquete = "funcion=verComentarios&id_sue="+id_sue;
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarComentarios.php",
        dataType: "html",
        data: paquete,
    }).done(function(respuesta){
        $("#contenedorComentarios").html(respuesta);
    }).fail(function(){
        $("#contenedorComentarios").html("No se pudieron recuperar los registros.");
    });
}

function mostrarSueno(){
    var id_sue = document.getElementById("id_sue").value;
    console.log(id_sue);
    paquete = "funcion=verSueno&id_sue="+id_sue;
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarComentarios.php",
        dataType: "html",
        data: paquete,
    }).done(function(respuesta){
        $("#contenedorSueno").html(respuesta);
    }).fail(function(){
        $("#contenedorSueno").html("No se pudieron recuperar los registros.");
    });
}

$('#publicarComentario').click(function(){
    var comentario = document.getElementById('txtComentario').value;
    var comentarioL = comentario.length;
    if(comentario == null || comentario == ''){
        alert("Tu comentario no puede estar vacío");
    }else if(comentarioL > 500){
        alert("Tu comentario no debe pasar de 500 caracteres. Tienes: "+suenoL);
    }else{
        publicarSueno();
        console.log("PublicarComentario completada");
        listarRegistrosNPVNM18();
        console.log("ListarRegistros completada");
    }
});

</script>