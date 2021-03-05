<?php
session_start();
require "../config.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../index.php");
    exit;
}

$funcion = $_POST["funcion"];
switch ($funcion) {
    case "checkMod":
        checkMod($link);
        break;
}

//Revisar si el usuario tiene permisos de moderación.
function checkMod($link)
{
    $sql = "SELECT id_mod FROM modd WHERE id_usu = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_codusu);
        $param_codusu = $_SESSION["id"];
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $registro);
                if (mysqli_stmt_fetch($stmt)) {
                    echo 1;
                }
            } else {
                echo 0;
                // echo "<script>"; 
                // echo "alert('No eres parte del staff. Fuera.');"; 
                // echo "</script>";
                // header("location: ../index.php");
            }
        }
    }
}
