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
    <title>Créditos</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="estilo.css">
    <link rel="icon" type="image/png" href="Recursos/Fotos/ONPLACEHOLDERFAV.png" />
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
                    <a class="nav-link active" id="op1" href="home.php">Volver al inicio<span class="sr-only">(current)</span></a>
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
        <div class="row">
            <div class="col-md-12">
                <div class="p-3">
                    <h2>Créditos</h2>
                </div>
                <div class="border border-info rounded p-3" style="background-color:white;">
                    <p>Este proyecto fue creado con los siguientes lenguajes y frameworks.</p>
                    <p><b>Lenguajes</b></p>
                    <ul>
                        <li>PHP</li>
                        <li>JavaScript</li>
                        <li>CSS</li>
                        <li>SQL</li>
                    </ul>
                    <p><b>Frameworks</b></p>
                    <ul>
                        <li>Bootstrap 4.5.2</li>
                        <li>JQuery 3.5.1</li>
                        <li>Ajax 3.5.1</li>
                        <li>Font Awesome 4.7.0</li>
                    </ul>
                    <p><b>Software</b></p>
                    <ul>
                        <li>MySQL Workbench</li>
                        <li>Visual Studio Code</li>
                        <li>WAMP</li>
                        <li>Microsoft Edge</li>
                        <li>Bloc de Notas</li>
                    </ul>
                    <p><b>Testers</b></p>
                    <p><b>Gente que ha probado el proyecto durante su desarrollo</b></p>
                    <ul>
                        <li>C</li>
                        <li>K</li>
                    </ul>
                    <p><b>Páginas visitadas durante la creación del proyecto</b></p>
                    <p>Espero que este aspecto ayude a otros desarrolladores con ciertas cosas que podrían necesitar.</p>
                    <ul>
                        <li> <a href="https://stackoverflow.com/questions/3705318/simple-php-pagination-script" target="_blank">Paginación en PHP (no utilizada) - StackOverflow</a> </li>
                        <li> <a href="https://icons8.com/icon/2908/help" target="_blank">Ícono de ayuda - Icons8</a> </li>
                        <li> <a href="https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php" target="_blank">Login en PHP - TutorialRepublic</a> </li>
                        <li> <a href="https://stackoverflow.com/questions/2985353/php-header-location-inside-iframe-to-load-in-top-location" target="_blank">Dan Heberden - Cambiar de página sin la función header - StackOverflow</a> </li>
                        <li> <a href="https://es.stackoverflow.com/questions/445/c%C3%B3mo-obtener-valores-de-la-url-get-en-javascript" target="_blank">Chofoteddy - Obtener parámetro de URL con javascript - StackOverflow</a> </li>
                        <li> <a href="https://stackoverflow.com/questions/1927593/cant-update-textarea-with-javascript-after-writing-to-it-manually" target="_blank">Actualizar un textarea luego de editarlo manualmente - StackOverflow</a> </li>
                        <li> <a href="https://stackoverflow.com/questions/14892516/jquery-button-click-triggers-multiple-times" target="_blank">Problema: Un botón accionando funciones más veces de lo pensado - StackOverflow</a> </li>
                        <li> <a href="https://stackoverflow.com/questions/18605563/href-must-not-reload" target="_blank">El Href no debe recargar la página - StackOverflow</a> </li>
                        <li> <a href="https://www.w3schools.com/tags/att_a_target.asp" target="_blank">Target para links, o sea, abrir uno en una pestaña nueva - W3Schools</a> </li>
                        <li> <a href="https://www.w3schools.com/howto/howto_css_icon_buttons.asp" target="_blank">Íconos en botones con CSS y FontAwesome - W3Schools</a> </li>
                        <li> <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">Catálogo de íconos de FontAwesome - FontAwesome</a> </li>
                        <li> <a href="https://stackoverflow.com/questions/22709936/alert-with-multiple-options" target="_blank">Alert de JavaScript con múltiples opciones - StackOverflow</a> </li>
                        <li> <a href="https://www.w3schools.com/bootstrap4/tryit.asp?filename=trybs_navbar_collapse" target="_blank">Barra de navegación responsiva - W3Schools</a> </li>
                        <li> <a href="https://stackoverflow.com/questions/8919682/remove-all-styling-formatting-from-hyperlinks" target="_blank">Quitar estilo y formato de links - StackOverflow</a> </li>
                        <li> <a href="https://stackoverflow.com/questions/13520127/submit-html-form-on-self-page" target="_blank">Enviar un formulario a la página actual - StackOverflow</a> </li>
                        <li> <a href="https://patorjk.com/software/taag/#p=display&f=Graffiti&t=Type%20Something%20" target="_blank">Creador de texto en ASCII - PatorJK</a> </li>
                    </ul>
                    <p><b>Notas, errores que no debería repetir y cosas aprendidas</b></p>
                    <ul>
                        <li>Para conseguir el nuevo valor escrito en un textarea, se debe usar "value", en vez de innerHTML. Por ejemplo: controlTXA = document.getElementById("textarea"); y luego: controlTXA.value = "ALGO";</li>
                        <li>Al empaquetar información en Ajax no poner NADA antes del primer valor, a pesar de que el "&" funciona, es mejor no ponerlo al principio.</li>
                        <li>Confundir id_usu y cod_usu por ejemplo y ocupar ambos, se hace confuso.</li>
                        <li>Poner .value cuando en realidad se necesita innerHTML o viceversa.</li>
                        <li>No poner el require config.php o ponerlo sin la referencia escrita correctamente -> "../config.php" por ejemplo.</li>
                        <li>No poner el session_start cuando necesito el id o nombre de usuario.</li>
                        <li>Al principio no le veía mucho sentido, pero me agrada Ajax.</li>
                    </ul>
                    <p><b>Notas de parche</b></p>
                    <p>Este es el archivo donde se han ido anotando los avances a medida que se hacen. Las versiones se separan por subida a github.</p>
                    <ul>
                        <li> <a href="TODO.txt">Ver archivo con notas del parche (No tiene CSS)</a> </li>
                    </ul>
                </div>
            </div>
        </div> <br>
    </div>
</body>
<script>
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