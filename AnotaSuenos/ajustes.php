<?php 
require "config.php";
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
    <title>Ajustes</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
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
                <br>
                <div id="opciones" class="border border-info rounded p-3" style="background-color:white;">
                    <p class="h4"><b>Personalización</b></p>
                    <p><b>Tema Oscuro: </b>¡Pronto!</p>
                    <p class="h4"><b>Cuenta</b></p>
                    <p><b>Cambiar nombre de usuario:</b></p>
                    <ul>
                        <li>Se cerrará tu sesión al realizar esta acción</li>
                        <li>Deberás usar tu nuevo nombre de usuario para iniciar sesión</li>
                        <li>El sistema es sensible a mayúsculas y minúsculas.</li>
                    </ul>
                    <p>
                        <input type="text" class="form-control" style="width:300px;" name="txtNombreUsuNue" id="txtNombreUsuNue" placeholder="Nuevo nombre de usuario"> <br>
                        <button class="btn btn-danger" id="cambiarUserName">Cambiar mi nombre de usuario</button>
                    </p>
                    <p><b>Cambiar foto de perfil:</b></p>
                    <ul>
                        <li>Las fotos inapropiadas pueden resultar en reseteo o eliminación de la foto subida.</li>
                    </ul>
                    <p>Foto actual:</p>
                        <?php 
                        $result = $link -> query("SELECT fot_usu FROM Login WHERE cod_usu = ".$_SESSION["id"]." ");
                        if($result->num_rows > 0){
                            while($row = $result->fetch_assoc()){ 
                        ?>
                               <span><img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row["fot_usu"]); ?> " width="120px" height="120px" alt="No hay foto de perfil" style="border-radius: 50%;" /></span>
                        <?php
                                }
                            }
                        ?>
                    <p>
                        <form action="CRUDs/handlerAuxUsuario.php" method="post" enctype="multipart/form-data">
                            <label>Seleccione su nueva foto de perfil:</label> <br>
                            <input type="file" name="image" accept="image/png,image/jpg,image/jpeg,image/gif"> <br> <br>
                            <input type="hidden" name="function" value="updateFP">
                            <input type="submit" class="btn btn-primary" id="cambiarFotoPerfil" name="submit" value="Cambiar foto de perfil">
                        </form>
                    </p>
                    <p><b>Eliminar mi cuenta:</b></p>
                    <p>Ten lo siguiente en cuenta antes de eliminar tu cuenta:</p>
                    <ul>
                        <li>Se liberará tu nombre de usuario y otra persona podrá utilizarlo.</li>
                        <li>Se borrará todo lo ligado a tu ID de usuario.</li>
                        <li>No hay vuelta atrás. A diferencia de, por ejemplo, Facebook, no hay un período de tiempo en el que puedas revertir tu decisión.</li>
                        <li>No quedará una copia de los datos que hayas ingresado en el sitio.</li>
                    </ul>
                    <button class="btn btn-danger eliminarCuenta">Eliminar mi cuenta</button>
                    <br> <br>
                </div>
            </div>
        </div>
    </div>
</body>
<script>

    $(document).on("click","#cambiarUserName",function(){
        var nuevoNombre = document.getElementById("txtNombreUsuNue").value;
        if(nuevoNombre == null || nuevoNombre == ""){
            alert("Tu nuevo nombre no puede estar vacío.");
            return null;
        }else if(nuevoNombre.length > 50){
            alert("Tu nombre de usuario no puede tener más de 50 caractéres.");
            return null;
        }
        if(window.confirm("Cerrarás tu sesión y tendrás que volver a iniciar sesión con tu nuevo nombre. \nEsta acción no se puede deshacer. ¿Continuar?")){
            alert("Se comprobará que tu nuevo nombre de usuario no esté ya en uso. \nSi lo está, no se realizará el cambio.");  
            procesoCambNomb(nuevoNombre);
        }
    });

    function procesoCambNomb(nuevoNombre){
        var paquete = "function=procCambioNombre&nuevoNombre="+nuevoNombre;
        $.ajax({
            type: "POST",
            url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
            // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxUsuario.php",
            data: paquete,
        }).done(function(respuesta){
            if(respuesta == 1){
                alert("¡Éxito! Cerrando sesión.");
                changeURL("Registro/logout.php");
            }else{
                alert("El usuario ya existe, intenta otro nombre.");
            }
            event.stopPropagation();
        }).fail(function(respuesta){
            console.log("FAIL "+respuesta);
            alert("No se pudo realizar la comprobación del nombre de usuario.");
        });
    }

    $(document).on("click", ".eliminarCuenta", function() {
        var button = $(this);
        if (window.confirm("Estás a punto de eliminar tu cuenta. Esta acción no se puede deshacer.")) {
            if(window.confirm("Última advertencia. Presionar aceptar ahora eliminará definitivamente tu cuenta y volverás a la página de inicio de sesión.")){
                paquete = "function=eliminarCuentaUsuario";
                $.ajax({
                    type: "POST",
                    url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
                    // url: "http://oniricnote.epizy.com/CRUDs/handlerAuxUsuario.php",
                    data: paquete,
                }).done(function(respuesta) {
                    alert("¡Adios!");
                    changeURL("../Registro/logout.php");
                    event.stopPropagation();
                }).fail(function(respuesta) {
                    alert("Error.");
                });
            event.stopPropagation();
            }else{
                alert("Cancelaste la eliminación de tu cuenta.");    
            }
        } else {
            alert("Cancelaste la eliminación de tu cuenta.");
        }
    });

    //Función tomada desde Stack Overflow - Usuario Dan Heberden
    function changeURL( url ) {
        document.location = url;
    }
    //Fin.

</script>
</html>