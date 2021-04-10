<?php 
session_start();
require "../config.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../index.php");
    exit;
}

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
<body style="background-color: #48BEFF;">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navBar" aria-controls="navBar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="../home.php">OniricNote - Moderación</a>
        <div class="collapse navbar-collapse" id="navBar">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" id="op1" href="../home.php">Volver al inicio<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <span class="nav-link" id="op3">  <a href="javascript:void(0);" id="verSuenos" style="text-decoration: none; color:inherit;" >Sueños</a></span>
                </li>
                <li class="nav-item">
                    <span class="nav-link" id="op4">  <a href="javascript:void(0);" id="verUsuarios" style="text-decoration: none; color:inherit;" >Usuarios</a></span>
                </li>
                <li class="nav-item">
                    <a href="../CRUDs/perfilPublico.php?cod_usu=<?php echo $_SESSION["id"]; ?>" class="nav-link" id="op5"> <?php echo $_SESSION["username"]; ?> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="op6" href="https://shsblog944322090.wordpress.com" target="_blank">Blog</a>
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
                        <li><a href="reset-pass.php" class="dropdown-item bg-warning" style="color: white;">Cambiar contraseña</a></li>
                        <li><a href="logout.php" class="dropdown-item bg-danger" style="color: white;">Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" method="GET" action="../CRUDs/busqueda.php">
                <input class="form-control mr-sm-2" type="search" name="buscar" placeholder="Buscar" aria-label="Buscar">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </div>
    </nav>
    <div class="container"> 
        <div class="row">
            <div class="col-md-8">
                <br>
                <h1 id="tituloList" class="">Sueños</h1>
                <p id="estadoList">Listando todos los sueños</p>
                
                <div id="contenedorSuenos">
                    <div id="mostrarSuenos">Los sueños van acá.</div>
                </div>
            </div>
            <div class="col-md-4">
            <br><br><br><br><br>
                <div id="contenedorMiniPerfil" class="center border border-info rounded p-3" style="background-color:white;">
                <?php 
                        $result = $link -> query("SELECT fot_usu FROM Login WHERE cod_usu = ".$_SESSION["id"]." ");
                        if($result->num_rows > 0){
                            while($row = $result->fetch_assoc()){ 
                    ?>
                               <span><img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row["fot_usu"]); ?> " width="70px" height="70px" alt="imagenBD" style="border-radius: 50%;" /></span>
                    <?php
                            }
                        }
                    ?>
                    <span id="nomUsuMiniPerfil"> &nbsp; <a href="../CRUDs/perfilPublico.php?cod_usu=<?php echo $_SESSION["id"]; ?>"> <?php echo $_SESSION["username"]; ?></a></span> <br> <br>
                    <p id="estadoMod">Cargando...</p>
                    <p>Para volver al modo usuario, utiliza la opción "Volver al inicio" en la barra de navegación.</p>
                </div> <br>
            </div>
        </div>
    </div>
</body>
<script>

    $(document).ready(function() {
        checkModJS(); //Revisa a ver si el usuario es moderador o no
        listarRegistros(); //Lista todos los sueños
    });

    function listarRegistros() {
        var paquete = "function=mostrarSueCustomQuery&opcion=modSue&offset=0";
        // var offSetDspl = "0";
        // document.getElementById("offsetDisplay").innerHTML = offSetDspl;
        // document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
        $.ajax({
            type: "GET",
            url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
            // url: "http://oniricnote.epizy.com/CRUDs/mostrarSuenos.php",
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
            // url: "http://oniricnote.epizy.com/Registro/mod.php",
            dataType: "html",
            data: paquete,
        }).done(function(respuesta){
            if(respuesta > 1 || respuesta <= 0){
                alert("No eres parte del staff. Volviendo a home.");
                parent.changeURL("../home.php");
            }else{
                document.getElementById("estadoMod").innerHTML = "Verificado rol de moderador.";
            }
        }).fail(function(){
            alert("No se pudo verificar tu rol de staff. Volviendo a home.");
        })
    }

    //Esta función se encuentra aquí en lugar del handler de sueños porque
    //es necesario llamar a la función para listar registros nuevamente tras eliminar un sueño
    //no hacer esto en el archivo mostrarSuenos es molesto, ya que se separan funciones que no 
    //deberían estar separadas, haciendo que el código sea menos mantenible, pero es la forma
    //sencilla de hacer esto sin tomar el filtro de algún lado y volver a realizar la consulta
    //dentro del mismo archivo.
    //Resumen: Que esta función esté aquí simplifica la aplicación, pero sacrifica un poco de mantenibilidad.
    $(document).on("click", ".eliminar", function() {
        var button = $(this);
        var id_sue = button.attr("id");
        paquete = "funcion=eliminarSueno&id_sue=" + id_sue;
        if (window.confirm("¿Eliminar este sueño?")) {
            $.ajax({
                type: "POST",
                url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
                // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxSuenos.php",
                data: paquete,
            }).done(function(respuesta) {
                alert(respuesta);
                listarRegistros();
                event.stopPropagation();
            }).fail(function(respuesta) {
                document.getElementById("textAreaSue" + id_sue).innerHTML = respuesta;
            });
            event.stopPropagation();
        } else {
            alert("Sueño no eliminado.");
        }
    });


    $(document).on("click","#verSuenos",function(){
        listarRegistros();
        document.getElementById("estadoList").innerHTML = "Listando sueños";
        document.getElementById("tituloList").innerHTML = "Sueños";
    });

    $(document).on("click","#verUsuarios",function(){
        mostrarUsuarios();
    });

    function mostrarUsuarios(){
        var paquete = "function=listarUsuarios&opcion=listarEspecial";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxUsuario.php",
            data: paquete,
        }).done(function(respuesta){
            $("#mostrarSuenos").html(respuesta);
            document.getElementById("estadoList").innerHTML = "Listando usuarios";
            document.getElementById("tituloList").innerHTML = "Usuarios";
        }).fail(function(respuesta){
            $("#mostrarSuenos").html("No se pudieron recuperar los usuarios.");
        });
    }

    $(document).on("click",".borrarUser",function(){
        var cod_usu = $(this).attr("id");
        var button = $(this);
        var paquete = "function=eliminarCuentaMOD&cod_usu="+cod_usu;
        if(window.confirm("¿Eliminar este usuario? \nEsta acción no se puede deshacer.")){
            $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxUsuario.php",
            data: paquete,
        }).done(function(respuesta){
            mostrarUsuarios();
            console.log("Operación completada");
            event.stopPropagation();
        }).fail(function(respuesta){
            alert("No se pudo completar la acción: Asignar moderador");
        });
        }
    });

    $(document).on("click",".asigMod",function(){
        console.log("=======Asignando moderador==========");
        var cod_usu = $(this).attr("id");
        var button = $(this);
        var paquete = "function=asignarModerador&cod_usu="+cod_usu;
        console.log(paquete);
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxUsuario.php",
            data: paquete,
        }).done(function(respuesta){
            button.text("Desasignar moderador");
            button.removeClass("btn-warning");
            button.removeClass("asigMod");
            button.addClass("btn-danger");
            button.addClass("desasigMod");
            console.log("Operación completada");
            event.stopPropagation();
        }).fail(function(respuesta){
            alert("No se pudo completar la acción: Asignar moderador");
        });
    });

    $(document).on("click",".desasigMod",function(){
        console.log("=======Desasignando moderador==========");
        var cod_usu = $(this).attr("id");
        var button = $(this);
        var paquete = "function=desasignarModerador&cod_usu="+cod_usu;
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxUsuario.php",
            data: paquete,
        }).done(function(respuesta){
            button.text("Asignar moderador");
            button.removeClass("btn-danger");
            button.removeClass("desasigMod");
            button.addClass("btn-warning");
            button.addClass("asigMod");
            console.log("Operación completada");
            event.stopPropagation();
        }).fail(function(respuesta){
            alert("No se pudo completar la acción: Asignar moderador");
        });
    });
        
    //Función tomada desde Stack Overflow - Usuario Dan Heberden
    function changeURL( url ) {
        document.location = url;
    }
    //Fin.

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
        var opcion6 = document.getElementById("op5");
        $(opcion1).removeClass("active");
        $(opcion2).removeClass("active");
        $(opcion3).removeClass("active");
        $(opcion4).removeClass("active");
        $(opcion5).removeClass("active");
        $(opcion6).removeClass("active");
        idopcion.addClass("active");
    });
</script>
</html>