<?php 
    $funcion = $_GET["funcion"];
    $id_sue = $_GET["id_sue"];
    if($funcion == "cantidadComent"){
        $link = mysqli_connect('localhost','root','','anotasuenos');
        $result = mysqli_query($link,"SELECT count(*) as total FROM Comentario WHERE id_sue = ".$id_sue." ");
        $data = mysqli_fetch_assoc($result);
        echo $data["total"];
    }
?>