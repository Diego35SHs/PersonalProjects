<?php 
require '../config.php';
session_start();

$sueno = $_POST["txtSueno"];
$sue_pri = $_POST["suenoPrivado"];
$sue_m18 = $_POST["suenoMas18"];
$cod_usu = $_SESSION["id"];

$sql = "INSERT INTO Sueno (sueno,sue_pri,sue_m18,fec_sue,cod_usu) VALUES(?,?,?,NOW(),?)";

if($stmt = mysqli_prepare($link,$sql)){
    mysqli_stmt_bind_param($stmt,"siii",$sueno_param,$sue_pri_param,$sue_m18_param,$cod_usu_param);
    $sueno_param = $sueno; $sue_pri_param = $sue_pri;
    $sue_m18_param = $sue_m18; $cod_usu_param = $cod_usu;
    if(mysqli_stmt_execute($stmt)){
        echo "Sueño publicado con éxito.";
    }else{
        echo "No se pudo agregar el sueño, posible error de conexión.";
    }
}else{
    echo "Falla de conexión.";
}
mysqli_close($link);
?>