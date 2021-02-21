<?php
require "config.php";
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OniricNote - Inicio</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="estilo.css">
    <link rel="icon" type="image/png" href="Recursos/Fotos/ONPLACEHOLDERFAV.png"/>
</head>
<body style="background-color: #48BEFF;">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navBar" aria-controls="navBar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="home.php">OniricNote</a>
        <div class="collapse navbar-collapse" id="navBar">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" id="op1" href="home.php">Inicio<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item ">
                    <span class="nav-link" id="op2"><a href="javascript:void(0);" id="verSeguidos" style="text-decoration: none; color:inherit;">Seguidos</a></span>
                </li>
                <li class="nav-item">
                    <span class="nav-link" id="op3">  <a href="javascript:void(0);" id="verPopulares" style="text-decoration: none; color:inherit;" >Populares</a></span>
                </li>
                <li class="nav-item">
                    <span class="nav-link" id="op4">  <a href="javascript:void(0);" id="verMas18" style="text-decoration: none; color:inherit;" >+18</a></span>
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
                        <li><a class="dropdown-item" href="ajustes.php">Ajustes</a></li>
                        <li><a class="dropdown-item" href="creditos.php">Créditos</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a href="Registro\reset-pass.php" class="dropdown-item bg-warning" style="color: white;">Cambiar contraseña</a></li>
                        <li><a href="Registro\logout.php" class="dropdown-item bg-danger" style="color: white;">Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" method="GET" action="CRUDs/busqueda.php">
                <input class="form-control mr-sm-2" type="search" name="buscar" placeholder="Buscar" aria-label="Buscar">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </div>
    </nav>
    <div class="container">
        <br>
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
                        <span id="filtroSueList"> sueño(s) públicos.</span>
                    </div> <br>
                    <div id="mostrarSuenosPublic">
                        <p class="border border-info rounded p-3" style="background-color:white;">Cargando sueños...</p>
                    </div>
                </div>
                <div id="listContainer" class="border border-info rounded p-3 text-center" style="width:100%;background-color:white;">
                    <a href="home.php" class="btn btn-info">Inicio</a>
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
                </div> <br>
                <div id="reglasGenerales" class="center border border-info rounded p-3" style="background-color:white;">
                    <p class="text-center"><b>Reglas generales</b></p>
                    <p>Por favor, no incluir nada de lo siguiente en los sueños o comentarios. Podría resultar en la eliminación de tus sueños y/o tu cuenta.</p>
                    <ul>
                        <li>Información personal.</li>
                        <li>Links.</li>
                        <li>Contenido ilegal de todo tipo.</li>
                        <li>Funas.</li>
                        <li>Protestas políticas.</li>
                    </ul>
                    <p>Básicamente, evitemos tratar este sitio como Twitter.</p>
                    <p>Extra: ¿500 caractéres no es suficiente? Puedes usar los comentarios de tu sueño para continuarlo, también tienen 500 caractéres como máximo.</p>
                </div> <br>
                <div id="espacioPublicitario" class="center border border-info rounded p-3" style="background-color:white;">
                    <p class="text-center"><b>Espacio publicitario</b></p>
                    <br><br><br><br><br><br><br><br><br>
                </div> <br>
                <div id="contenedorEstadisticas" class="center border border-info rounded p-3" style="background-color:white;">
                    <p class="text-center"><strong>Estadísticas del sitio</strong></p>
                    <p id="cantidadSuenosUsu">Cargando...</p>
                    <p id="cantidadComentUsu">Cargando...</p>
                    <p id="cantidadUsuarios">Cargando...</p>
                    <p id="cantidadSuenosTotal">Cargando...</p>
                    <p id="cantidadSuenosPublic">Cargando...</p>
                    <p id="cantidadSuenosPrivados">Cargando...</p>
                    <p id="cantidadSuenosM18">Cargando...</p>
                    <p id="cantidadLikesSuenos">Cargando...</p>
                    <p id="cantidadComent">Cargando...</p>
                    <p id="cantidadLikesComent">Cargando...</p>
                    <button id="actualizarEstadisticas" class="btn btn-info">Actualizar</button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php
    echo "<div hidden='true'>";
    echo "<input type='hidden' id='filtroActual' value='default '";
    echo " </div>";
    ?>
</body>
<!-- FIN BODY -->
<script>
    $(document).ready(function() {
        listarRegistrosNPVNM18(); //No privados, no +18.
        listarCantidadSuenos(); //Lista la cantidad de sueños
        mostrarEstadisticas(); //Ejecuta todas las funciones para mostrar estadísticas.
    });

    $('#publicarSueno').click(function() {
        var sueno = document.getElementById('txtSueno').value;
        var suenoL = sueno.length;
        if (sueno == null || sueno == '') {
            alert("Tu sueño no puede estar vacío");
        } else if (suenoL > 500) {
            alert("Tu sueño no debe pasar de 500 caracteres. Tienes: " + suenoL);
        } else {
            publicarSueno();
            console.log("PublicarSueno completada");
            listarRegistrosNPVNM18();
            mostrarEstadisticas();
            console.log("ListarRegistros completada");
        }
    });

    $('#siguientes10').click(function() {
        //Se consigue un nuevo offset para la consulta sql y se le suma 10, haciendo que avance a los siguientes
        //10 registros.
        console.log("-----------INICIO BTNSIGUIENTE-----------");
        console.log("Botón siguientes 10 - Presionado, iniciando");
        var newOffset = parseInt(document.getElementById("offsetDisplay").innerHTML) + parseInt(10);

        //Estos dos deben ser iguales siempre 0 - 0 -> 10 - 10
        //En este caso, se hace automáticamente.
        var opcion = document.getElementById("filtroActual").value;
        var offset = "function=mostrarSueCustomQuery&opcion=" + opcion + "&offset=" + newOffset;
        var offsetDspl = newOffset;
        var offsetLimDspl = parseInt(offsetDspl) + parseInt(10);
        console.log("Siguientes 10: Variables definidas");

        var limite = parseInt(document.getElementById("cantidadTotalSuenos").innerHTML);
        if (limite > newOffset && limite < offsetLimDspl) {
            offset = "function=mostrarSueCustomQuery&opcion=" + opcion + "&offset=" + limite;
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
            $("#mostrarSuenosPublic").html(respuesta);
        }).fail(function() {
            $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
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
        var opcion = document.getElementById("filtroActual").value;
        var offset = "function=mostrarSueCustomQuery&opcion=" + opcion + "&offset=" + parseInt(newOffset);
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
            $("#mostrarSuenosPublic").html(respuesta);
        }).fail(function() {
            $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
        });
        console.log("Anteriores 10 - Ajax mostrando");
        console.log("-----------FIN BTNANTERIORES-----------");
        scrollTop();
    });

    $('#actualizarEstadisticas').click(function() {
        mostrarEstadisticas();
    });

    $('#verSeguidos').click(function() {
        $("#mostrarSuenosPublic").html("Cargando filtro...");
        var paquete = "function=mostrarSueCustomQuery&offset=0&opcion=soloSeguidosNoM18";
        var offSetDspl = "0";
        document.getElementById("offsetDisplay").innerHTML = offSetDspl;
        document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
        document.getElementById("filtroActual").value = "soloSeguidosNoM18";
        document.getElementById("filtroSueList").innerHTML = " sueños de usuarios seguidos.";
        $.ajax({
            type: "GET",
            url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#mostrarSuenosPublic").html(res);
            listarCantidadSueCustom();
            event.stopPropagation();
        }).fail(function() {
            $("#mostrarSuenosPublic").html("Algo falló.");
        });
    });

    $('#verPopulares').click(function(){
        $("#mostrarSuenosPublic").html("Cargando filtro...");
        var paquete = "function=mostrarSueCustomQuery&offset=0&opcion=masPopulares";
        var offSetDspl = "0";
        document.getElementById("offsetDisplay").innerHTML = offSetDspl;
        document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
        document.getElementById("filtroActual").value = "masPopulares";
        document.getElementById("filtroSueList").innerHTML = " sueños por popularidad. (Se incluyen +18)";
        $.ajax({
            type: "GET",
            url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#mostrarSuenosPublic").html(res);
            listarCantidadSueCustom();
            event.stopPropagation();
        }).fail(function() {
            $("#mostrarSuenosPublic").html("Algo falló.");
        });
    });

    $('#verMas18').click(function(){
        $("#mostrarSuenosPublic").html("Cargando filtro...");
        var paquete = "function=mostrarSueCustomQuery&offset=0&opcion=noPVsiM18";
        var offSetDspl = "0";
        document.getElementById("offsetDisplay").innerHTML = offSetDspl;
        document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
        document.getElementById("filtroActual").value = "noPVsiM18";
        document.getElementById("filtroSueList").innerHTML = " sueños públicos y +18.";
        $.ajax({
            type: "GET",
            url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#mostrarSuenosPublic").html(res);
            listarCantidadSueCustom();
            event.stopPropagation();
        }).fail(function() {
            $("#mostrarSuenosPublic").html("Algo falló.");
        });
    });

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
                data: paquete,
            }).done(function(respuesta) {
                alert(respuesta);
                listarRegistrosNPVNM18();
                mostrarEstadisticas();
                event.stopPropagation();
            }).fail(function(respuesta) {
                document.getElementById("textAreaSue" + id_sue).innerHTML = respuesta;
            });
            event.stopPropagation();
        } else {
            alert("Sueño no eliminado.");
        }
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

    function publicarSueno() {
        var txtSueno = document.getElementById('txtSueno').value;
        var suenoMas18 = 0;
        var suenoPrivado = 0;
        if (document.getElementById('suenoMas18').checked) {
            suenoMas18 = 1;
        }
        if (document.getElementById('suenoPrivado').checked) {
            suenoPrivado = 1;
        }
        //Consulta SQL
        //Empaquetar registro a enviar.
        var paquete = "funcion=agregarSueno&txtSueno=" + txtSueno + "&suenoMas18=" + suenoMas18 + "&suenoPrivado=" + suenoPrivado;
        $.ajax({
                type: 'POST',
                url: 'http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php',
                data: paquete,
            })
            .done(function(respuesta) {
                $('#Resultado').html(respuesta);
                listarRegistrosNPVNM18();
                listarCantidadSuenos();
                document.getElementById('txtSueno').value = null;
                document.getElementById('suenoMas18').checked = false;
                document.getElementById('suenoPrivado').checked = false;
            })
            .fail(function() {
                $('#Resultado').html("No se pudo agregar tu sueño, posiblemente debido a un problema de conexión");
            })
    }

    //FILTROS
    function listarRegistrosNPVNM18() {
        var offset = "function=mostrarSueCustomQuery&opcion=noPVnoM18&offset=0";
        var offSetDspl = "0";
        document.getElementById("offsetDisplay").innerHTML = offSetDspl;
        document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
        document.getElementById("filtroActual").value = "noPVnoM18";
        $.ajax({
            type: "GET",
            url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
            dataType: "html",
            data: offset,
        }).done(function(respuesta) {
            $("#mostrarSuenosPublic").html(respuesta);
        }).fail(function() {
            $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
        });
    }

    function listarCantidadSuenos() {
        var paquete = "funcion=cantidadSuenosPublic";
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

    //Esta función tomará el valor del filtro bajo el id "filtroActual"
    //y la consulta ajax llevará este valor a la opción que ejecutará la función
    //para contar sueños. La consulta estará preparada de antemano en el switch
    //y devolverá el valor correspondiente.
    function listarCantidadSueCustom(){
        var filtro = document.getElementById("filtroActual").value;
        var paquete = "funcion=contarSueFiltro&opcion="+filtro;
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

//Sección estadísticas.
//Función principal.
    function mostrarEstadisticas() {
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

    function cantSuenosUsuario() {
        var paquete = "function=getCantSueUsuario";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadSuenosUsu").html(res);
        }).fail(function() {
            $("#cantidadSuenosUsu").html("Algo falló.");
        });
    }

    function cantComentUsuario() {
        var paquete = "funcion=getCantComentUsu";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadComentUsu").html(res);
        }).fail(function() {
            $("#cantidadComentUsu").html("No se pudo cargar.");
        });
    }

    function cantidadUsuarios() {
        var paquete = "funcion=getCantUsuarios";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadUsuarios").html(res);
        }).fail(function() {
            $("#cantidadUsuarios").html("No se pudo cargar.");
        });
    }

    function cantidadSuenosTotal() {
        var paquete = "funcion=getCantSuenosTotal";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadSuenosTotal").html(res);
        }).fail(function() {
            $("#cantidadSuenosTotal").html("No se pudo cargar.");
        });
    }

    function cantidadSuenosPublic() {
        var paquete = "funcion=getCantSuenosPublic";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadSuenosPublic").html(res);
        }).fail(function() {
            $("#cantidadSuenosPublic").html("No se pudo cargar.");
        });
    }

    function cantidadSuenosPrivados() {
        var paquete = "funcion=getCantSuenosPriv";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadSuenosPrivados").html(res);
        }).fail(function() {
            $("#cantidadSuenosPrivados").html("No se pudo cargar.");
        });
    }

    function cantidadSuenosM18() {
        var paquete = "funcion=getCantSuenosM18";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadSuenosM18").html(res);
        }).fail(function() {
            $("#cantidadSuenosM18").html("No se pudo cargar.");
        });
    }

    function cantidadLikesSuenos() {
        var paquete = "funcion=getCantLikesSuenos";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadLikesSuenos").html(res);
        }).fail(function() {
            $("#cantidadLikesSuenos").html("No se pudo cargar.");
        });
    }

    function cantidadComent() {
        var paquete = "funcion=getCantComent";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadComent").html(res);
        }).fail(function() {
            $("#cantidadComent").html("No se pudo cargar.");
        });
    }

    function cantidadLikesComent() {
        var paquete = "funcion=getCantLikesComent";
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxApp.php",
            dataType: "html",
            data: paquete,
        }).done(function(res) {
            $("#cantidadLikesComent").html(res);
        }).fail(function() {
            $("#cantidadLikesComent").html("No se pudo cargar.");
        });
    }
//Fin sección estadísticas

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