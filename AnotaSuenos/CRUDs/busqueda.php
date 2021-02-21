<?php 
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../estilo.css">
</head>
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
                        <li><a href="Registro\reset-pass.php" class="dropdown-item bg-warning" style="color: white;">Cambiar contraseña</a></li>
                        <li><a href="Registro\logout.php" class="dropdown-item bg-danger" style="color: white;">Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" method="GET" >
                <input class="form-control mr-sm-2" type="search" name="buscar" placeholder="Buscar" aria-label="Buscar">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </div>
    </nav>
    <div class="container">
        <div class="row">
        <div class="col-md-12">
            <br>
            <p class="h3"><span>Buscando sueños que contegan "</span><span id="termBusqueda"><?php echo $_GET["buscar"]; ?></span><span>"</span></p>
            <br>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
            <div id="cantidadSuenos" class="text-center border border-info rounded p-1" style="background-color: white; height: 40px;">
                        <span>Hay </span>
                        <span id="cantidadTotalSuenos"></span>
                        <span id="filtroSueList"> coincidencia(s).</span>
                    </div> <br>
                <div id="mostrarSuenoBus">Cargando sueños...</div>
            
                <div id="listContainer" class="border border-info rounded p-3 text-center" style="width:100%;background-color:white;">
                    <button id="anteriores10" class="btn btn-primary">Anteriores 10</button>
                    <span>Mostrando: </span>
                    <span id="offsetDisplay">-----------</span>
                    <span> - </span>
                    <span id="offsetLimDisplay">-----------</span>
                    <button id="siguientes10" class="btn btn-primary">Siguientes 10</button>
                </div>
            </div>
            <div class="col-md-4">
                <div id="contenedorMiniPerfil" class="center border border-info rounded p-3" style="background-color:white;">
                    <span><img src="https://img.icons8.com/ios-filled/50/000000/help.png" width="50px" height="50px" alt="FDP" /></span>
                    <span id="nomUsuMiniPerfil"> <a href="../CRUDs/perfilPublico.php?cod_usu=<?php echo $_SESSION["id"]; ?>"> <?php echo $_SESSION["username"]; ?></a></span>
                </div>
            </div>
            
        </div>
    </div>
    <?php
    echo "<div hidden='true'>";
    echo "<input type='hidden' id='filtroActual' value='default '";
    echo " </div>";
    ?>
</body>
<script>
    $(document).ready(function(){
        buscar();
        listarCantidadSueCustom();
    });

    function buscar(){
        var termBusqueda = document.getElementById("termBusqueda").innerHTML;
        var paquete = "function=mostrarSueCustomQuery&opcion=busqueda&termBusqueda="+termBusqueda+"&offset=0";
        var offSetDspl = "0";
        document.getElementById("offsetDisplay").innerHTML = offSetDspl;
        document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
        document.getElementById("filtroActual").value = "busqueda";
        $.ajax({
            type: "GET",
            url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
            data: paquete,
        }).done(function(respuesta){
            $("#mostrarSuenoBus").html(respuesta);
        }).fail(function(){
            $("#mostrarSuenoBus").html("No se pudieron recuperar los registros.");
        });
    }

    $('#siguientes10').click(function() {
        //Se consigue un nuevo offset para la consulta sql y se le suma 10, haciendo que avance a los siguientes
        //10 registros.
        console.log("-----------INICIO BTNSIGUIENTE-----------");
        console.log("Botón siguientes 10 - Presionado, iniciando");
        var newOffset = parseInt(document.getElementById("offsetDisplay").innerHTML) + parseInt(10);

        //Estos dos deben ser iguales siempre 0 - 0 -> 10 - 10
        //En este caso, se hace automáticamente.
        var termBusqueda = document.getElementById("termBusqueda").innerHTML;
        var opcion = document.getElementById("filtroActual").value;
        var offset = "function=mostrarSueCustomQuery&opcion=" + opcion + "&offset=" + newOffset+"&termBusqueda="+termBusqueda;
        var offsetDspl = newOffset;
        var offsetLimDspl = parseInt(offsetDspl) + parseInt(10);
        console.log("Siguientes 10: Variables definidas");

        var limite = parseInt(document.getElementById("cantidadTotalSuenos").innerHTML);
        if (limite > newOffset && limite < offsetLimDspl) {
            offset = "function=mostrarSueCustomQuery&opcion=" + opcion + "&offset=" + limite+"&termBusqueda="+termBusqueda;
        }

        //Mostrar nuevos valores en la página
        console.log("Funcion cambiarSpans llamada");
        cambiarSpans(offsetDspl, offsetLimDspl);

        //Limitar la cantidad máxima de sueños que se pueden mostrar.
        console.log(document.getElementById("cantidadTotalSuenos").innerHTML);


        //TODO: Limitar la cantidad de registros máxima a la cantidad total de registros.

        $.ajax({
            type: "GET",
            url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
            dataType: "html",
            data: offset,
        }).done(function(respuesta) {
            $("#mostrarSuenoBus").html(respuesta);
        }).fail(function() {
            $("#mostrarSuenoBus").html("No se pudieron recuperar los registros.");
        });
        console.log("Botón siguientes 10 - Ajax mostrando");
        console.log("-----------FIN BTNSIGUIENTE-----------");
        scrollTop();
    });

    $('#anteriores10').click(function() {
        console.log("-----------INICIO BTNANTERIORES-----------");

        //Conseguir offset "antiguo" o sea, el que está en la página en este momento.
        var oldOffSet = parseInt(document.getElementById("offsetDisplay").innerHTML);

        //Conseguir el nuevo offset, este es básicamente el offset actual - 10. Para retroceder.
        var newOffset = parseInt(document.getElementById("offsetDisplay").innerHTML) - parseInt(10);

        //Revisar si estamos al inicio de la página, con offset 0, si es así, abortar ejecución con el return.
        if ((parseInt(oldOffSet) == parseInt(0))) {
            alert("Ya estás en el inicio");
            return null;
        }

        //Definir variables para el offset.
        var termBusqueda = document.getElementById("termBusqueda").innerHTML;
        var opcion = document.getElementById("filtroActual").value;
        var offset = "function=mostrarSueCustomQuery&opcion=" + opcion + "&offset=" + parseInt(newOffset)+"&termBusqueda="+termBusqueda;
        var offsetDspl = newOffset;
        var offsetLim = parseInt(offsetDspl) + parseInt(10);
        console.log("Anteriores 10: Variables definidas");

        //Cambiar los spans con los nuevos límites.
        console.log("Funcion cambiarSpans llamada");
        cambiarSpans(offsetDspl, offsetLim);

        $.ajax({
            type: "GET",
            url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
            dataType: "html",
            data: offset,
        }).done(function(respuesta) {
            $("#mostrarSuenoBus").html(respuesta);
        }).fail(function() {
            $("#mostrarSuenoBus").html("No se pudieron recuperar los registros.");
        });
        console.log("Anteriores 10 - Ajax mostrando");
        console.log("-----------FIN BTNANTERIORES-----------");
        scrollTop();
    });

    function cambiarSpans(offsetDspl, offsetLimDspl) {
        console.log("CAMBIANDO SPANS");
        console.log("nuevos spans: " + offsetDspl + " - " + offsetLimDspl);
        document.getElementById("offsetDisplay").innerHTML = offsetDspl;
        document.getElementById("offsetLimDisplay").innerHTML = offsetLimDspl;
        console.log("SPANS CAMBIADOS");
    }

    function scrollTop() {
        $("html,body").animate({
            scrollTop: 0
        }, "slow");
        return false;
    }

    //Esta función tomará el valor del filtro bajo el id "filtroActual"
    //y la consulta ajax llevará este valor a la opción que ejecutará la función
    //para contar sueños. La consulta estará preparada de antemano en el switch
    //y devolverá el valor correspondiente.
    function listarCantidadSueCustom(){
        var filtro = document.getElementById("filtroActual").value;        
        var termBusqueda = document.getElementById("termBusqueda").innerHTML;
        var paquete = "funcion=contarSueFiltro&opcion="+filtro+"&termBusqueda="+termBusqueda;
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadTotalSuenos").html(res);
        }).fail(function() {
            $("#cantidadTotalSuenos").html("Algo falló.");
        });
    }
    
    //Cambiar clase active de los link del navBar
    //Un tanto ineficiente, pero solo se ejecuta al presionar el botón.
    $(document).on("click", ".nav-link", function() {
        var idopcion = $(this);
        console.log(idopcion);
        var opcion1 = document.getElementById("op1");
        var opcion2 = document.getElementById("op2");
        var opcion3 = document.getElementById("op3");
        var opcion4 = document.getElementById("op4");
        var opcion5 = document.getElementById("op5");
        $(opcion1).removeClass("active");
        $(opcion2).removeClass("active");
        $(opcion3).removeClass("active");
        $(opcion4).removeClass("active");
        $(opcion5).removeClass("active");
        idopcion.addClass("active");
    });
    
</script>
</html>