<?php 
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
require_once "../config.php";
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Ingrese la nueva contraseña.";
    }elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "La contraseña debe tener al menos 6 caractéres";
    }else{
        $new_password = trim($_POST["new_password"]);
    }
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirme la contraseña.";
    }else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }
    if(empty($new_password_err) && empty($confirm_password_err)){
        $sql = "UPDATE Login SET pas_usu = ? WHERE cod_usu = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            $param_password = password_hash($new_password,PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            if(mysqli_stmt_execute($stmt)){
                session_destroy();
                header("location: ../index.php");
                exit();
            }else{
                echo "Algo salió mal. Intente de nuevo más tarde";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../estilo.css">
</head>
<body style="background-color: #48BEFF;">
<div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <br>
                    <h2>Cambiar contraseña</h2>
                    <p>Llene el formulario para cambiar su contraseña:</p>
                    <div class="border border-info rounded p-3" style="background-color:white;">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
                        <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                            <label>Nueva contraseña:</label>
                            <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                            <span class="help-block"><?php echo $new_password_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                            <label>Confirmar contraseña:</label>
                            <input type="password" name="confirm_password" class="form-control">
                            <span class="help-block"><?php echo $confirm_password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Cambiar contraseña">
                            <a class="btn btn-danger" href="../home.php" data-toggle="tooltip" title="Volver a home.">Cancelar</a>
                        </div>
                    </form>
                    </div>
                </div>
                <div class="col-md-8">
                <br><br><br><br> <br>
                    <div class="border border-info rounded p-3" style="background-color:white;">
                        <p><b>Antes de cambiar tu contraseña:</b></p>
                        <ul>
                            <li>Se cerrará tu sesion y deberás iniciarla otra vez con tu nueva contraseña.</li>
                            <li>No existe un historial de contraseñas.</li>
                            <li>De momento, no existe forma de recuperar tu contraseña o autenticación en dos pasos (2FA).</li>
                            <li>Tu contraseña es sensible a mayusculas y minúsculas, no lo olvides.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    </div>   
</body>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();  
    iniToolTip();
});

function iniToolTip(){
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
}
</script>
</html>