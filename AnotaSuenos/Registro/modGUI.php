<?php 

require "../config.php";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOD</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../estilo.css">
</head>
<body>
    <div class="container"> 
        <div class="row">
            <div class="col-md-8">
                <h5>Sueños</h5>
                <p>Listado de sueños</p>
                <div id="contenedorSuenos">
                    <div id="mostrarSuenos">Los sueños van acá.</div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>

    $(document).ready(function() {
        checkModJS(); //Lista la cantidad de sueños
        listarRegistros(); //TODO
    });

    function listarRegistros() {
        var paquete = "function=mostrarSueCustomQuery&opcion=modSue&offset=0";
        // var offSetDspl = "0";
        // document.getElementById("offsetDisplay").innerHTML = offSetDspl;
        // document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
        $.ajax({
            type: "GET",
            url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
            dataType: "html",
            data: paquete,
        }).done(function(respuesta) {
            $("#mostrarSuenos").html(respuesta);
        }).fail(function() {
            $("#mostrarSuenos").html("No se pudieron recuperar los registros.");
        });
    }

    function checkModJS(){
        var paquete = "funcion=checkMod";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/Registro/mod.php",
            dataType: "html",
            data: paquete,
        }).done(function(respuesta){
            alert(respuesta);
            if(respuesta > 1 || respuesta <= 0){
                alert("No eres parte del staff. Volviendo a home");
            }else{
                alert("Verificado rol de moderador");
            }
        }).fail(function(){
            alert("No se pudo verificar tu rol de staff. Volviendo a home.");
        })
    }
</script>
</html>