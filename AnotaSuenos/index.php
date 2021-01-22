<?php 
//Inicializar sesión
session_start();

//Revisar si el usuario ya ha iniciado sesión, si es así, redireccionar a la "Home"
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: home.php");
    exit;
}

//Incluir archivo de configuración
require_once "config.php";

//Definir variables e inicializarlas con valores vacíos
$username = $password = "";
$username_err = $password_err = "";

//Procesar datos del form cuando este se ingresa
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //Revisar si el nombre de usuario viene vacío
    if(empty(trim($_POST["username"]))){
        $username_err = "Ingrese un nombre de usuario.";
    }else{
        $username = trim($_POST["username"]);
    }
    //Revisar si la contraseña viene vacía
    if(empty(trim($_POST["password"]))){
        $password_err = "Ingrese su contraseña.";
    }else{
        $password = trim($_POST["password"]);
    }
    //Validar credenciales
    if(empty($username_err) && empty($password_err)){
        //Preparar el select
        $sql = "SELECT cod_usu,nom_usu,pas_usu FROM Login WHERE nom_usu=?";
        if($stmt = mysqli_prepare($link,$sql)){
            //Asignar variables y parámetros al statement
            mysqli_stmt_bind_param($stmt,"s",$param_username);
            //Definir parámetros
            $param_username = $username;
            //Intentar ejecutar la consulta
            if(mysqli_stmt_execute($stmt)){
                //Guardar resultado
                mysqli_stmt_store_result($stmt);
                //Revisar si el nombre de usuario existe, si es así, verificar contraseña
                if(mysqli_stmt_num_rows($stmt) == 1){
                    //Enlazar variables del resultado.
                    mysqli_stmt_bind_result($stmt,$id,$username,$hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password,$hashed_password)){
                            //Contraseña correcta, se inicia la sesión y se asignan variables a la sesión
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            //Redireccionar a página home
                            header("location: home.php");
                        }else{
                            //Mostrar mensaje de error si la contraseña no es válida.
                            $password_err = "Contraseña incorrecta.";
                        }
                    }
                }else{
                    //Mostrar mensaje de error si el nombre de usuario no existe
                    $username_err = "No existe el usuario.";
                }
            }else{
                echo "Algo salió mal, inténtelo de nuevo";
            }
            //Cerrar statement
            mysqli_stmt_close($stmt);
        }
    }
    //Cerrar conexión
    mysqli_close($link);
}

?>

<!DOCTYPE html>
<html lang="es">
<html>
<!-- INICIO HEAD -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a AnotaSueños</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="estilo.css">
    <script>
        $(document).ready(function(){
            $('div.mostrar').fadeIn(1500);
            console.log("Todo bien.");
        });
    </script>
</head>
<!-- FIN HEAD -->
<!-- INICIO BODY -->
<body class="indexBodyBackground">    
    <div class="row mostrar" id="#titulo" style="display:none;">
        <div class="indexDivTitulo col-xs-12 col-sm-12 col-md-12 col-lg-8">
            <h1 class="indexH1">Bienvenido/a a AnotaSueños</h1>
            <p class="indexP">Ingresa tu nombre de usuario y contraseña para iniciar.</p>
        </div>
        <div class="indexDivTitulo col-xs-12 col-sm-12 col-md-12 col-lg-4">
            <div class="fondoLogin">
                <h3 align="center">Iniciar sesión</h3>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label>Nombre de usuario:</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" width="400px">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>" > 
                    <label>Contraseña:</label>
                    <input type="password" name="password" class="form-control">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Iniciar sesión">
                </div>
                    <p>¿No tienes cuenta? <a href="Registro\registro.php">Registrarse</a>.</p>
                </form>
            </div>
        </div>
        <div class="indexDivTitulo col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-left: 50px;">
            <a href="https://www.pexels.com/@neale-lasalle-197020?utm_content=attributionCopyText&utm_medium=referral&utm_source=pexels">Photo by Neale LaSalle from Pexels</href>
        </div>
    </div>
    
</body>
<!-- FIN BODY -->
</html>