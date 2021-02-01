<?php 
require "config.php";
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: index.php");
    exit;
}
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
    <link rel="stylesheet" href="estilo.css">
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
                <div id="contenedorAgregarSueno" class="border border-info rounded p-3" style="background-color:white;">
                    <p>Publicar un sueño:</p>
                    <textarea name="txtSueno" id="txtSueno" cols="30" rows="10" style="width:100%; height: 80px; resize:none;" placeholder="¿Qué soñaste anoche?"></textarea><br>
                    <input type="checkbox" id="suenoMas18" name="suenoMas18"> Sueño +18 &nbsp;
                    <input type="checkbox" id="suenoPrivado" name="suenoPrivado"> Sueño privado &nbsp;
                    <button id="publicarSueno" class="btn btn-primary">Publicar</button> <br>
                    <p id="Resultado"></p>
                </div> <br>
                <div id="contenedorSuenos" style="width:100%;">
                    <div id="cantidadSuenos" class="text-center border border-info rounded p-1" style="background-color: white; height: 40px;">
                    <span>Listando </span>
                    <span id="cantidadTotalSuenos"></span>
                    <span> sueño(s) públicos.</span>
                    </div> <br>
                    <div id="mostrarSuenosPublic">
                    <p>Cargando sueños...</p>
                    </div>
                </div>
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
                    <span id="nomUsuMiniPerfil"><?php echo $_SESSION["username"]; ?></span><br>
                    
                    <span> <a href="CRUDs/handlerAuxUsuario.php">Test AuxUsuario</a> </span>
                </div> <br>
                <div id="contenedorEstadisticas" class="center border border-info rounded p-3" style="background-color:white;">
                    <p class="text-center"><strong>Estadísticas del sitio.</strong></p>
                    <p id="cantidadSuenosUsu"        >Cargando...</p>
                    <p id="cantidadComentUsu"        >Cargando...</p>
                    <p id="cantidadUsuarios"         >Cargando...</p>
                    <p id="cantidadSuenosTotal"      >Cargando...</p>
                    <p id="cantidadSuenosPublic"     >Cargando...</p>
                    <p id="cantidadSuenosPrivados"   >Cargando...</p>
                    <p id="cantidadSuenosM18"        >Cargando...</p>
                    <p id="cantidadLikesSuenos"      >Cargando...</p>
                    <p id="cantidadComent"           >Cargando...</p>
                    <p id="cantidadLikesComent"      >Cargando...</p>
                    <button id="actualizarEstadisticas" class="btn btn-info">Actualizar</button>
                </div>
            </div>
        </div>
    </div>
    <a href="Registro\logout.php" class="btn btn-danger">Cerrar sesión</a>
</body>
<!-- FIN BODY -->
<script>

$(document).ready(function(){
    //Ejecutar función ajax que mostará registros
    //Parámetros
    // NPV : NO PRIVADOS
    // NM18 : NO MAS 18
    listarRegistrosNPVNM18();
    listarCantidadSuenos();
    mostrarEstadisticas();
});

$('#publicarSueno').click(function(){
    var sueno = document.getElementById('txtSueno').value;
    var suenoL = sueno.length;
    if(sueno == null || sueno == ''){
        alert("Tu sueño no puede estar vacío");
    }else if(suenoL > 500){
        alert("Tu sueño no debe pasar de 500 caracteres. Tienes: "+suenoL);
    }else{
        publicarSueno();
        console.log("PublicarSueno completada");
        listarRegistrosNPVNM18();
        mostrarEstadisticas();
        console.log("ListarRegistros completada");
    }
});

$('#siguientes10').click(function(){
    //Se consigue un nuevo offset para la consulta sql y se le suma 10, haciendo que avance a los siguientes
    //10 registros.
    console.log("-----------INICIO BTNSIGUIENTE-----------");
    console.log("Botón siguientes 10 - Presionado, iniciando");
    var newOffset = parseInt(document.getElementById("offsetDisplay").innerHTML) + parseInt(10);
    
    //Estos dos deben ser iguales siempre 0 - 0 -> 10 - 10
    //En este caso, se hace automáticamente.
    var offset = "offset="+newOffset;
    var offsetDspl = newOffset;
    var offsetLimDspl = parseInt(offsetDspl) + parseInt(10);
    console.log("Siguientes 10: Variables definidas");

    var limite = parseInt(document.getElementById("cantidadTotalSuenos").innerHTML);
    if(limite > newOffset && limite < offsetLimDspl){
        offset = "&offset="+limite;
    }

    //Mostrar nuevos valores en la página
    console.log("Funcion cambiarSpans llamada");
    cambiarSpans(offsetDspl,offsetLimDspl);
    
    //Limitar la cantidad máxima de sueños que se pueden mostrar.
    console.log(document.getElementById("cantidadTotalSuenos").innerHTML);
    

    //TODO: Limitar la cantidad de registros máxima a la cantidad total de registros.

    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
    console.log("Botón siguientes 10 - Ajax mostrando");
    console.log("-----------FIN BTNSIGUIENTE-----------");
    scrollTop();
});

$('#anteriores10').click(function(){
    console.log("-----------INICIO BTNANTERIORES-----------");

    //Conseguir offset "antiguo" o sea, el que está en la página en este momento.
    var oldOffSet = parseInt(document.getElementById("offsetDisplay").innerHTML);

    //Conseguir el nuevo offset, este es básicamente el offset actual - 10. Para retroceder.
    var newOffset = parseInt(document.getElementById("offsetDisplay").innerHTML) - parseInt(10);

    //Revisar si estamos al inicio de la página, con offset 0, si es así, abortar ejecución con el return.
    if((parseInt(oldOffSet) == parseInt(0))  ){
        alert("Ya estás en el inicio");
        return null;
    }

    //Definir variables para el offset.
    var offset = "offset="+parseInt(newOffset);
    var offsetDspl = newOffset;
    var offsetLim = parseInt(offsetDspl) + parseInt(10);
    console.log("Anteriores 10: Variables definidas");
    
    //Cambiar los spans con los nuevos límites.
    console.log("Funcion cambiarSpans llamada");
    cambiarSpans(offsetDspl,offsetLim);
    
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
    console.log("Anteriores 10 - Ajax mostrando");
    console.log("-----------FIN BTNANTERIORES-----------");
    scrollTop();
});

$('#actualizarEstadisticas').click(function(){
    mostrarEstadisticas();
});

function cambiarSpans(offsetDspl, offsetLimDspl){
    console.log("CAMBIANDO SPANS");
    console.log("nuevos spans: "+offsetDspl+" - "+offsetLimDspl);
    document.getElementById("offsetDisplay").innerHTML =  offsetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = offsetLimDspl;
    console.log("SPANS CAMBIADOS");
}

function scrollTop(){
    $("html,body").animate({ scrollTop: 0}, "slow");
    return false;
}

function publicarSueno(){
    var txtSueno = document.getElementById('txtSueno').value;
    var suenoMas18 = 0;
    var suenoPrivado = 0;
    if(document.getElementById('suenoMas18').checked){
        suenoMas18 = 1;
    }
    if(document.getElementById('suenoPrivado').checked){
        suenoPrivado = 1;
    }
    //Consulta SQL
    //Empaquetar registro a enviar.
    var paquete = "funcion=agregarSueno&txtSueno="+txtSueno+"&suenoMas18="+suenoMas18+"&suenoPrivado="+suenoPrivado;
    $.ajax({
        type: 'POST',
        url: 'http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php',
        data: paquete,
    })
    .done(function(respuesta){
        $('#Resultado').html(respuesta);
        listarRegistrosNPVNM18();
        listarCantidadSuenos();
        document.getElementById('txtSueno').value = null;
        document.getElementById('suenoMas18').checked = false;
        document.getElementById('suenoPrivado').checked = false;
    })
    .fail(function(){
        $('#Resultado').html("No se pudo agregar tu sueño, posiblemente debido a un problema de conexión");
    })
}

function listarRegistrosNPVNM18(){
    var offset = "offset=0";
    var offSetDspl = "0";
    document.getElementById("offsetDisplay").innerHTML = offSetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
}

function listarCantidadSuenos(){
    var paquete = "funcion=cantidadSuenosPublic";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadTotalSuenos").html(res);
    }).fail(function(){
        $("#cantidadTotalSuenos").html("Algo falló.");
    });
}

//Sección estadísticas.
function mostrarEstadisticas(){
    cantSuenosUsuario();
    cantComentUsuario();
    cantidadUsuarios();
    cantidadSuenosTotal();
    cantidadSuenosPublic();
    cantidadSuenosPrivados();
    cantidadSuenosM18();
    cantidadLikesSuenos();
    cantidadComent();
    cantidadLikesComent();
}

function cantSuenosUsuario(){
    var paquete = "function=getCantSueUsuario";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadSuenosUsu").html(res);
    }).fail(function(){
        $("#cantidadSuenosUsu").html("Algo falló.");
    });
}

function cantComentUsuario(){
    var paquete = "funcion=getCantComentUsu";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadComentUsu").html(res);
    }).fail(function(){
        $("#cantidadComentUsu").html("No se pudo cargar.");
    });
}

function cantidadUsuarios(){
    var paquete = "funcion=getCantUsuarios";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadUsuarios").html(res);
    }).fail(function(){
        $("#cantidadUsuarios").html("No se pudo cargar.");
    });
}

function cantidadSuenosTotal(){
    var paquete = "funcion=getCantSuenosTotal";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadSuenosTotal").html(res);
    }).fail(function(){
        $("#cantidadSuenosTotal").html("No se pudo cargar.");
    });
}

function cantidadSuenosPublic(){
    var paquete = "funcion=getCantSuenosPublic";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadSuenosPublic").html(res);
    }).fail(function(){
        $("#cantidadSuenosPublic").html("No se pudo cargar.");
    });
}

function cantidadSuenosPrivados(){
    var paquete = "funcion=getCantSuenosPriv";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadSuenosPrivados").html(res);
    }).fail(function(){
        $("#cantidadSuenosPrivados").html("No se pudo cargar.");
    });
}

function cantidadSuenosM18(){
    var paquete = "funcion=getCantSuenosM18";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadSuenosM18").html(res);
    }).fail(function(){
        $("#cantidadSuenosM18").html("No se pudo cargar.");
    });
}

function cantidadLikesSuenos(){
    var paquete = "funcion=getCantLikesSuenos";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadLikesSuenos").html(res);
    }).fail(function(){
        $("#cantidadLikesSuenos").html("No se pudo cargar.");
    });
}

function cantidadComent(){
    var paquete = "funcion=getCantComent";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadComent").html(res);
    }).fail(function(){
        $("#cantidadComent").html("No se pudo cargar.");
    });
}

function cantidadLikesComent(){
    var paquete = "funcion=getCantLikesComent";
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadLikesComent").html(res);
    }).fail(function(){
        $("#cantidadLikesComent").html("No se pudo cargar.");
    });
}

</script>
</html>