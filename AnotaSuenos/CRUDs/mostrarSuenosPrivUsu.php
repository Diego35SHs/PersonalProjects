<?php 

//Inicializar conexión con los parámetros definidos.
    try{
        echo "<table class='table table-hover'>";
        $link = mysqli_connect('localhost','root','','anotasuenos');
        $response = array();
        $result = mysqli_query($link, "SELECT sueno,sue_pri,sue_m18,fec_sue,cod_usu FROM sueno WHERE sue_pri = 1 AND cod_usu = ".$_SESSION['id']." ORDER BY fec_sue DESC");
        if(mysqli_num_rows($result)>0){
            $response["suenos"] = array();
            while($row = mysqli_fetch_array($result)){
                $temp = array();
                echo "<tr>";
                    echo "<td>";
                        echo $row["sueno"];
                    echo "</td>";
                    echo "<td>";
                        echo $row["sue_pri"];
                    echo "</td>";
                    echo "<td>";
                        echo $row["sue_m18"];
                    echo "</td>";
                    echo "<td>";
                        echo $row["fec_sue"];
                    echo "</td>";
                    echo "<td>";
                        echo $row["cod_usu"];
                    echo "</td>";                   
                echo "</tr>";
                array_push($response["suenos"], $temp);
            }
        }else{
            echo "<tr colspan='5'>";
                echo "<td>";
                    echo "No se encontró ningún registro.";
                echo "</td>";
            echo "</tr>";
        }
    echo "</table>";
    }catch(Exception $agga){
        echo $agga;
    }

?>