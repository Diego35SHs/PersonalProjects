<?php 
require "../config.php";
session_start();

$id_sue = $_POST["id_sue"];
$nuevo_sue = $_POST["nuevoSue"];
$cod_usu = $_SESSION["id"];

$sql = "UPDATE Sueno SET sueno=? WHERE id_sue=? AND cod_usu=? ";

if($stmt = mysqli_prepare($link,$sql)){
    mysqli_stmt_bind_param($stmt,"sii",$sueno_param,$id_sue_param,$cod_usu_param);
    $sueno_param = $nuevo_sue; $id_sue_param = $id_sue;
    $cod_usu_param = $cod_usu;
    if(mysqli_stmt_execute($stmt)){
        echo $nuevo_sue;
    }else{
        echo "No se pudo actualizar el sueño. Help.";
    }
}else{
    echo "Falla de conexión.";
}
mysqli_close($link);


?>