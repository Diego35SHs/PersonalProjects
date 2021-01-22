<?php 
require "config.php";
//require "CRUDs/mostrarSuenos.php";
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
        <div class="col-md-12 col-lg-12">
            <h1>Sueños</h1>
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
                    <div id="mostrarSuenosPublic"></div>
                </div>
                <div id="listContainer" style="width:100%;">
                    <button id="anteriores10" class="btn btn-info">Anteriores 10</button>
                    <span>Mostrando: </span>
                    <span id="offsetDisplay">-----------</span>
                    <span> - </span>
                    <span id="offsetLimDisplay">--------</span>
                    <button id="siguientes10" class="btn btn-primary">Siguientes 10</button>
                </div>
            </div>
            <div class="col-md-4">
            BBBBBBBBBBBBBBBBBbbbB
            </div>
        </div>
    </div>
    <a href="Registro\logout.php" class="btn btn-danger">Cerrar sesión</a>
</body>
<!-- FIN BODY -->
<script>
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
    var offset = "&offset="+newOffset;
    var offsetDspl = newOffset;
    var offsetLimDspl = parseInt(offsetDspl) + parseInt(10);
    console.log("Siguientes 10: Variables definidas");

    //Mostrar nuevos valores en la página
    console.log("Funcion cambiarSpans llamada");
    cambiarSpans(offsetDspl,offsetLimDspl);
    

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
    var offset = "&offset="+parseInt(newOffset);
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
    var paquete = "txtSueno="+txtSueno+"&suenoMas18="+suenoMas18+"&suenoPrivado="+suenoPrivado;
    $.ajax({
        url: 'http://anotasuenos:8080/CRUDs/agregarSueno.php',
        type: 'POST',
        data: paquete,
    })
    .done(function(respuesta){
        $('#Resultado').html(respuesta);
        listarRegistrosNPVNM18();
        document.getElementById('txtSueno').value = null;
        document.getElementById('suenoMas18').checked = false;
        document.getElementById('suenoPrivado').checked = false;
    })
    .fail(function(){
        $('#Resultado').html("No se pudo agregar tu sueño, posiblemente debido a un problema de conexión");
    })
}

$(document).ready(function(){
    //Ejecutar función ajax que mostará registros
    //Parámetros
    // NPV : NO PRIVADOS
    // NM18 : NO MAS 18
    listarRegistrosNPVNM18();
});



function listarRegistrosNPVNM18(){
    var offset = "&offset=0";
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

</script>
</html>