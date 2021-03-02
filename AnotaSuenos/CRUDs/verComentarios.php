<?php 
require "../config.php";
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<html>
<!-- INICIO HEAD -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentarios</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../estilo.css">
</head>
<!-- FIN HEAD -->
<!-- INICIO BODY -->
<body style="background-color: #48BEFF;">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navBar" aria-controls="navBar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="../home.php">OniricNote</a>
        <div class="collapse navbar-collapse" id="navBar">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" id="op1" href="../home.php">Volver al inicio<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a href="../CRUDs/perfilPublico.php?cod_usu=<?php echo $_SESSION["id"]; ?>" class="nav-link" id="op4"> <?php echo $_SESSION["username"]; ?> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="op5" href="https://shsblog944322090.wordpress.com" target="_blank">Blog</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                        Opciones
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
                        <li><a class="dropdown-item" href="../ajustes.php">Ajustes</a></li>
                        <li><a class="dropdown-item" href="../creditos.php">Créditos</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a href="../Registro/reset-pass.php" class="dropdown-item bg-warning" style="color: white;">Cambiar contraseña</a></li>
                        <li><a href="../Registro/logout.php" class="dropdown-item bg-danger" style="color: white;">Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" method="GET" action="busqueda.php" >
                <input class="form-control mr-sm-2" type="search" name="buscar" placeholder="Buscar" aria-label="Buscar">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </div>
    </nav>
    <div class="container">
        <br>
        <div class="row">
            <div class="col-md-8"> 
                <div style="display:none;">
                <?php echo "<input type='hidden' style='display:none;' id='id_sue' value=".$_GET["id_sue"]." '>";?>
                </div>
                <div id="contenedorSueno"  style="background-color: white;">
                <p class="border border-info rounded p-3" style="background-color:white;">Cargando sueño...</p>
                </div> <br>
                <div id="cantidadSuenos" class="text-center border border-info rounded p-1" style="background-color: white; height: 40px;">
                    <span>Actualmente hay </span>
                    <span id="cantidadTotalComentarios">0</span>
                    <span> comentario(s).</span>
                    </div> <br>
                <div id="contenedorAgregarComentario" class="border border-info rounder p-3" style="background-color:white;">
                    <p>Comentario:</p>
                    <textarea name="txtComentario" id="txtComentario" cols="30" rows="10" style="width:100%; height: 80px; resize:none;" placeholder="¿Qué opinas?"></textarea><br>
                    <button id="publicarComentario" class="btn btn-primary">Comentar</button> <br>
                    <p id="Resultado"></p>
                </div> <br>
                <div id="contenedorComentarios" >
                    <p class="border border-info rounded p-3" style="background-color:white;">Cargando comentarios...</p>
                </div> <br>
                <div id="listContainer" class="border border-info rounded p-3" style="width:100%;background-color:white;"> 
                    <a href="" class="btn btn-info">Inicio</a>
                    <button id="anteriores10" class="btn btn-primary">Anteriores 10</button>
                    <span>Mostrando: </span>
                    <span id="offsetDisplay">-----------</span>
                    <span> - </span>
                    <span id="offsetLimDisplay">--------</span>
                    <button id="siguientes10" class="btn btn-primary">Siguientes 10</button>
                </div>
                <br>
            </div>
            <div class="col-md-4">
                <div id="contenedorMiniPerfil" class="center border border-info rounded p-3" style="background-color:white;">
                    <span><img src="https://img.icons8.com/ios-filled/50/000000/help.png" width="50px" height="50px" alt="FDP" /></span>
                    <span id="nomUsuMiniPerfil"> <a href="../CRUDs/perfilPublico.php?cod_usu=<?php echo $_SESSION["id"]; ?>"> <?php echo $_SESSION["username"]; ?></a></span>
                </div> <br>
                <div id="espacioPublicitario" class="center border border-info rounded p-3" style="background-color:white;">
                    <p class="text-center"><b>Espacio publicitario</b></p>
                    <br><br><br><br><br><br><br><br><br>
                </div> <br>
            </div>
        </div>
    </div>
</body>
<script>
$(document).ready(function(){
    mostrarComentarios();
    mostrarSueno();
    listarCantidadComent();
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

$(document).on("click",".eliminarCome",function(){
        var button = $(this);
        var id_com = button.attr("id");
        paquete = "funcion=eliminarComentario&id_com="+id_com;
        if(window.confirm("¿Eliminar este comentario?")){
            $.ajax({
                type: "POST",
                url: "http://anotasuenos:8080/CRUDs/handlerAuxComent.php",
                data: paquete,
            }).done(function(respuesta) {
                alert(respuesta);
                mostrarComentarios();
                event.stopPropagation();
            }).fail(function(respuesta) {
                document.getElementById("textAreaCom" + id_com).innerHTML = respuesta;
            });
            event.stopPropagation();
        }else{
            alert("Comentario no eliminado.");
        }
    });

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
        alert("Tu comentario no debe pasar de 500 caracteres. Tienes: "+comentarioL);
    }else{
        publicarComentario();
        console.log("PublicarComentario completada");
        console.log("ListarRegistros completada");
    }
});

function publicarComentario(){
    var txtComentario = document.getElementById('txtComentario').value;
    var id_sue = document.getElementById('id_sue').value;
    //Consulta SQL
    //Empaquetar registro a enviar.
    var paquete = "funcion=agregarComentario&txtComentario="+txtComentario+"&id_sue="+id_sue;
    $.ajax({
        url: 'http://anotasuenos:8080/CRUDs/handlerAuxComent.php',
        type: 'POST',
        data: paquete,
    })
    .done(function(respuesta){
        $('#Resultado').html(respuesta);
        mostrarComentarios();
        listarCantidadComent();
        document.getElementById('txtComentario').value = null;
    })
    .fail(function(){
        $('#Resultado').html("No se pudo agregar tu sueño, posiblemente debido a un problema de conexión");
    })
}



function listarCantidadComent(){
    var id_sue = document.getElementById("id_sue").value;
    var paquete = "funcion=cantidadComent&id_sue="+id_sue;    
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxComent.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadTotalComentarios").html(res);
    }).fail(function(){
        $("#cantidadTotalComentarios").html("Algo falló.");
    });
}
</script>