<?php 
require '../config.php';
session_start();

$comentario = $_POST["txtComentario"];
$cod_usu = $_SESSION["id"];
$id_sue = $_POST["id_sue"];

$sql = "INSERT INTO Comentario (id_sue,id_usu,comentario,fec_com) VALUES(?,?,?,NOW())";

if($stmt = mysqli_prepare($link,$sql)){
    mysqli_stmt_bind_param($stmt,"iis",$id_sue_param,$id_usu_param,$comentario_param);
    $id_sue_param = $id_sue; $id_usu_param = $cod_usu;
    $comentario_param = $comentario;
    if(mysqli_stmt_execute($stmt)){
        echo "Comentario publicado con éxito.";
    }else{
        echo "No se pudo agregar el comentario, posible error de conexión.";
    }
}else{
    echo "Falla de conexión.";
}
mysqli_close($link);
?>