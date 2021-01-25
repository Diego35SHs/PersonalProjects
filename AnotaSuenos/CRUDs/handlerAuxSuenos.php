<?php 
    $funcion = $_GET["funcion"];
    if($funcion == "cantidadSuenos"){
        $link = mysqli_connect('localhost','root','','anotasuenos');
        $result = mysqli_query($link,"SELECT count(*) as total FROM Sueno");
        $data = mysqli_fetch_assoc($result);
        echo $data["total"];
    }
?>