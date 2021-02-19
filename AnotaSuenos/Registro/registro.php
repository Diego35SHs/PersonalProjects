<?php 
//Incluir archivo de configuración.
require_once "..\config.php";

//Definir variables e inicializar con valores vacíos.
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    //Validar nombre de usuario
    if(empty(trim($_POST["username"]))){
        $username_err = "Ingrese un nombre de usuario";
    }else{
        //Preparar consulta, aquí se toma el código de usuario
        //por medio de la coincidencia entre el nombre de usuario
        //escrito por el usuario y el código guardado en BD.
        //stmt = statement
        //TODO: Crear uno de estos sin ver un tutorial.
        $sql = "SELECT cod_usu FROM Login WHERE nom_usu = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            //Insertar variables a la consulta.
            mysqli_stmt_bind_param($stmt,"s",$param_username);
            //Definir parámetros
            $param_username = trim($_POST["username"]);
            //Intentar ejecutar la consulta.
            if(mysqli_stmt_execute($stmt)){
                //Guardar resultado.
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt)==1){
                    $username_err = "Nombre de usuario ya en uso.";
                }else{
                    $username = trim($_POST["username"]);
                }
            }else{
                echo "Algo salió mal, inténtelo de nuevo más tarde";
            }
            //Terminar statement
            mysqli_stmt_close($stmt);
        }
    }
    //Validar contraseña
    if(empty(trim($_POST["password"]))){
        $password_err = "Ingrese una contraseña.";
    }elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "La contraseña debe tener al menos 6 caractéres.";
    }else{
        $password = trim($_POST["password"]);
    }

    //Validar confirmación de contraseña
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirme la contraseña.";
    }else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }

    //Revisar errores en los datos de entrada antes de insertar en BD.
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        //Preparar consulta insert
        $sql = "INSERT INTO Login (nom_usu, pas_usu, fec_usu) VALUES (?,?,NOW())";
        if($stmt = mysqli_prepare($link,$sql)){
            //Asignar variables al statement.
            mysqli_stmt_bind_param($stmt,"ss",$param_username,$param_password);
            //Definir parámetros
            $param_username = $username;
            $param_password = password_hash($password,PASSWORD_DEFAULT); //Crear el hash de contraseña
            //Intentar ejecutar consulta.
            if(mysqli_stmt_execute($stmt)){
                //Redireccionar a página de login
                header("location: ..\index.php");
            }else{
                echo "Algo salió mal, intentelo de nuevo después.";
            }
            //Terminar statement
            mysqli_stmt_close($stmt);
        }
    }
    //Terminar conexión
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
    <link rel="stylesheet" href="../estilo.css">
    <script>
        $(document).ready(function(){
            $('div.mostrar').fadeIn(2000);
            console.log("Todo bien.");
        });
    </script>
</head>
<!-- FIN HEAD -->
<!-- INICIO BODY -->
<body class="indexBodyBackground">    
    <div class="row mostrar" id="#titulo" style="display:none;">
        <div class="indexDivTitulo col-xs-12 col-sm-12 col-md-12 col-lg-8">
            <h1 class="indexH1">Registrarse</h1>
        </div>
        <div class="indexDivTitulo col-xs-12 col-sm-12 col-md-12 col-lg-4">
            <div class="fondoLogin">
                <h3 align="center">Registrarse</h3>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Nombre de usuario:</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirmar contraseña:</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Registrarse">
                <input type="reset" class="btn btn-default" value="Limpiar campos">
            </div>
            <p>¿Ya tienes una cuenta? <a href="../index.php">Inicia sesión</a>.</p>
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