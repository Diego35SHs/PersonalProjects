<?php 
    require "../config.php";
    session_start();
    $nombreUsuarioPerf = nombreUsuSueno($_GET["cod_usu"],$link);
    $descrUsuario = descripcionUsuario($_GET["cod_usu"],$link);
    $codigoUsuario = $_GET["cod_usu"];
    if($codigoUsuario <= 0){
        header("location: ../home.php");
        exit;
    }
    if($descrUsuario == null || $descrUsuario == ''){
        $descrUsuario = "Este usuario no ha escrito ninguna descripción.";
    }

    //Función nombreUsuSueno
    //Input: Código de usuario y Link de conexión.
    //Output: Nombre de usuario correspondiente al sueño.
    function nombreUsuSueno($codusu,$link){
        $sql = "SELECT nom_usu FROM Login WHERE cod_usu = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"i",$param_codusu);
            $param_codusu = $codusu;
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt,$nombreUsuario);
                    if(mysqli_stmt_fetch($stmt)){
                        return $nombreUsuario;
                    }
                }else{
                    return "No se encontró el usuario";
                }
            }
        }
        return "Usuario no encontrado";
    }

    //Función descripcionUsuario
    //Input: Código de usuario y Link de conexión
    //Output: La descripción del usuario.
    function descripcionUsuario($codusu,$link){
        $sql = "SELECT des_usu FROM Login WHERE cod_usu = ?";
        if($stmt = mysqli_prepare($link,$sql)){
            mysqli_stmt_bind_param($stmt,"i",$param_codusu);
            $param_codusu = $codusu;
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt,$descrUsuario);
                    if(mysqli_stmt_fetch($stmt)){
                        return $descrUsuario;
                    }
                }else{
                    return "El usuario no ha escrito una descripción.";
                }
            }
        }
        return "Usuario no encontrado";
    }

    //funcion heightTXA 
    //Input: cántidad de caracteres de un sueño
    //Output: Altura de textarea que ocupará el sueño.
    function heightTXA($cantidadCarac){
        $altoTXA = "height:50px;";
        if($cantidadCarac <= 100){
            $altoTXA = "height:50px;";
        }
        if($cantidadCarac > 100 && $cantidadCarac <= 200){
            $altoTXA = "height:135px;";
        }
        if($cantidadCarac > 200 && $cantidadCarac <=300){
            $altoTXA = "height:190px;";
        }
        if($cantidadCarac > 300 && $cantidadCarac <= 400){
            $altoTXA = "height:160px;";
        }
        if($cantidadCarac > 400){
            $altoTXA = "height:180px;";
        }
        return $altoTXA;
    }

    function checkSeguir($id_usu_sdo, $id_usu_sdr, $link){
        $result = mysqli_query($link, "SELECT count(*) as total FROM Seguidores WHERE id_usu_sdo = " . $id_usu_sdo . " AND id_usu_sdr =" . $id_usu_sdr . " ");
        $data = mysqli_fetch_assoc($result);
        return $data["total"];
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nombreUsuarioPerf; ?> - Perfil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="../estilo.css">
</head>
<body style="background-color: #48BEFF;">
    <div class="container">
        <br>
        <div class="col-md-12 col-lg 12"> <a href="../home.php" class="btn btn-warning">Volver al home</a> </div> <br>
            <div class="col-md-12 col-lg-12 border border-info rounded p-3 " style="background-color:white;width:100%;">
            <span class="col-md-3 col-lg-2"> <img src="https://img.icons8.com/ios-filled/50/000000/help.png" width="60px" height="60px" alt="FDP" /> </span>
            <span class="font-weight-bold col-md-4 col-lg-4" style="text-size: 80px;"> <?php echo htmlspecialchars($nombreUsuarioPerf); ?> </span> 
            <span>Sueños publicados: </span><span id="cantidadSuenosUserProf">---</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span>Seguidores: </span><span id="cantidadSeguidoresUserProf">---</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span>Me gusta recibidos: </span><span id="cantidadMegustaUserProf">---</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php if($_SESSION["id"] != $_GET["cod_usu"]){ 
                if(checkSeguir($_GET["cod_usu"],$_SESSION["id"],$link) == 0){
                    echo "<span><button class='seguirUser btn btn-info'>Seguir</button></span>"; 
                }else{
                    echo "<span><button class='noseguirUser btn btn-warning'>Dejar de seguir</button></span>"; 
                }
            }?>
            <br><br>
            <p>Descripción:</p>
            <?php 
                $cantidadCarac = strlen($descrUsuario);
                $alto = heightTXA($cantidadCarac);
                echo "<textarea id='descUsu".$_GET["cod_usu"]."' class='form-control' style='resize:none;".$alto."border:none;maxlength:500;background-color:white;text-color:black;' disabled='true'  >".$descrUsuario."</textarea><br>";
                if($_SESSION["id"] == $_GET["cod_usu"]){
                    echo "<button id='".$_GET["cod_usu"]."' class='modificarDes btn btn-warning'>Editar descripción</button>";
                }
            ?>
        </div> <br>
        <div class="row">
            <div id="contenedorSuenos" class="col-md-8 col-lg-8" style="width:100%;">
                    <div id="mostrarSuenosPublic">
                    <p>Cargando sueños...</p>
                    </div>
                    <div id="listContainer" class="border border-info rounded p-3 text-center" style="width:100%;background-color:white;"> 
                    <a href="" class="btn btn-info">Inicio</a>
                    <button id="anteriores10" class="btn btn-primary">Anteriores 10</button>
                    <span>Mostrando: </span>
                    <span id="offsetDisplay">-----------</span>
                    <span> - </span>
                    <span id="offsetLimDisplay">-----------</span>
                    <button id="siguientes10" class="btn btn-primary">Siguientes 10</button>
            </div> 
            </div>
            <div class="col-md-4">
                <div id="contenedorMiniPerfil" class="center border border-info rounded p-3" style="background-color:white;">
                    <a href="javascript:void(0)" id="mostrarPublic"     >Sueños públicos no +18</a><br>
                    <a href="javascript:void(0)" id="mostrarM18Public"  >Sueños públicos y +18</a><br>
                    <?php 
                    if($codigoUsuario == $_SESSION["id"]){
                        echo  "<a href='javascript:void(0)' id='mostrarAllM18PubPri' >Todos mis sueños (+18, públicos y privados)</a><br>";
                        echo  "<a href='javascript:void(0)' id='mostrarPriv'          >Solo sueños privados</a><br>";
                    }
                    ?>
                    <a href="javascript:void(0)" id="mostrarM18"           >Solo sueños +18</a><br>        
                </div> <br>
            </div>
        </div>

    </div>
    <?php
        echo "<div hidden='true'>";
        echo "<input type='hidden' id='cod_usuHid' value='".$_GET["cod_usu"]."'>";
        echo " </div>";
    ?>
</body>
<script>

$(document).ready(function(){
    listarRegistrosUsuarioPerf();
    listarCantSueUsuPerf(); 
    listarCantLikesRecUsu();
    listarCantSegUsu();
});

$(document).on("click",".seguirUser",function(){
    console.log("Seguir usuario - Iniciado");
    var cod_usu = document.getElementById("cod_usuHid").value;
    var button = $(this);
    var paquete = "function=seguirUsuario&cod_usu=" + cod_usu;
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
        data: paquete,
    }).done(function(respuesta){
        button.text("Dejar de seguir");
        button.removeClass("seguirUser");
        button.removeClass("btn-info");
        button.addClass("noseguirUser");
        button.addClass("btn-warning");
        document.getElementById("cantidadSeguidoresUserProf").innerHTML = respuesta;
        console.log("CANTIDAD DE Seguidores ACTUALIZADA");
        event.stopPropagation();
    }).fail(function(respuesta) {
        alert("Error de conexión. Probablemente.");
    });
});

$(document).on("click",".noseguirUser",function(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var button = $(this);
    var paquete = "function=noseguirUsuario&cod_usu=" + cod_usu;
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
        data: paquete,
    }).done(function(respuesta){
        button.text("Seguir");
        button.addClass("seguirUser");
        button.addClass("btn-info");
        button.removeClass("noseguirUser");
        button.removeClass("btn-warning");
        document.getElementById("cantidadSeguidoresUserProf").innerHTML = respuesta;
        console.log("CANTIDAD DE Seguidores ACTUALIZADA");
        event.stopPropagation();
    }).fail(function(respuesta) {
        alert("Error de conexión. Probablemente.");
    });
});

$(document).on("click",".modificarDes",function(){
    console.log("Botón modificar sueño - Iniciado");
    var id_des = $(this).attr("id");
    console.log("ID BUTTON Y Descripción = "+id_des);
    var button = $(this);
    button.text("Guardar cambios");
    console.log("Quitar clases actuales referentes al funcionamiento del botón");
    button.removeClass("modificarDes");
    button.removeClass("btn-warning");
    console.log("Añadir clases necesarias para el nuevo funcionamiento del botón");
    button.addClass("btn-info");
    button.addClass("guardarCambiosDes");
    //Conseguir el texto del sueño para luego utilizarlo de alguna forma.
    var textArea = document.getElementById("descUsu"+id_des).innerHTML;
    console.log(textArea);
    //Tomar control sobre el textarea por medio de su id.
    var controlTXA = document.getElementById("descUsu"+id_des);
    controlTXA.style="border-stiyle:solid;border-color:black;resize:none;";
    controlTXA.removeAttribute("disabled");
    event.stopPropagation();
});

$(document).on("click",".guardarCambiosDes",function(){
    console.log("Botón guardar cambios sueño - Iniciando");
    var id_des = $(this).attr("id");
    console.log("ID BOTÓN Y SUEÑO = "+id_des);
    //Tomar control del botón y del textarea
    var button = $(this);
    var controlTXA = document.getElementById("descUsu"+id_des);
    //Conseguir valor nuevo del textarea
    var textArea = controlTXA.value;
    var alto = heightTXA(textArea.length);
    if(textArea == null || textArea == ''){
        textArea = "Este usuario no ha escrito ninguna descripción.";
    }else if(textArea.length > 500){
        alert("Tu descripción no puede pasar de 500 caracteres. Tienes "+textArea.length);
        return null;
    }
    //empaquetar la información
    var paquete = "function=updateDescrUsuario&id_sue="+id_des+"&nuevaDes="+textArea;
    console.log(paquete);
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
        data: paquete,
    }).done(function(respuesta){
        button.text("Editar descripción");
        console.log("Quitar clases actuales referentes al funcionamiento del botón");
        button.removeClass("btn-info");
        button.removeClass("guardarCambiosDes");
        console.log("Añadir clases necesarias para el nuevo funcionamiento del botón");
        button.addClass("modificarDes");
        button.addClass("btn-warning");
        document.getElementById("descUsu"+id_des).innerHTML = respuesta;
        console.log("RESPUESTA: "+respuesta);
        controlTXA.style="border:none;resize:none;background-color:white;"+alto+"";
        controlTXA.setAttribute("disabled","true");
        console.log("Botón guardar cambios sueño - Finalizado");
        alert("Descripción modificada.");
        event.stopPropagation();
    }).fail(function(respuesta){
        document.getElementById("descUsu"+id_sue).innerHTML = respuesta;
    });
});

function listarRegistrosUsuarioPerf(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var offset = "function=mostrarSueCustomQuery&cod_usu="+cod_usu+"&offset=0&opcion=noPVnoM18User";
    var offSetDspl = "0";
    document.getElementById("offsetDisplay").innerHTML = offSetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
}

$(document).on("click","#mostrarPriv",function(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var offset = "function=mostrarSueCustomQuery&cod_usu="+cod_usu+"&offset=0&opcion=soloPVUser";
    var offSetDspl = "0";
    document.getElementById("offsetDisplay").innerHTML = offSetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
    $("#mostrarSuenosPublic").html("Cargando sueños...");
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
});

$(document).on("click","#mostrarPublic",function(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var offset = "function=mostrarSueCustomQuery&cod_usu="+cod_usu+"&offset=0&opcion=noPVnoM18User";
    var offSetDspl = "0";
    document.getElementById("offsetDisplay").innerHTML = offSetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
});

$(document).on("click","#mostrarM18",function(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var offset = "function=mostrarSueCustomQuery&cod_usu="+cod_usu+"&offset=0&opcion=soloM18User";
    var offSetDspl = "0";
    document.getElementById("offsetDisplay").innerHTML = offSetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
});

$(document).on("click","#mostrarM18Public",function(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var offset = "function=mostrarSueCustomQuery&cod_usu="+cod_usu+"&offset=0&opcion=noPVsiM18User";
    var offSetDspl = "0";
    document.getElementById("offsetDisplay").innerHTML = offSetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
});

$(document).on("click","#mostrarAllM18PubPri",function(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var offset = "function=mostrarSueCustomQuery&cod_usu="+cod_usu+"&offset=0&opcion=todosUser";
    var offSetDspl = "0";
    document.getElementById("offsetDisplay").innerHTML = offSetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
});

$(document).on("click","#queryUserPerf",function(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var offset = "function=mostrarSueCustomQuery&cod_usu="+cod_usu+"&offset=0&opcion=noPVnoM18User";
    var offSetDspl = "0";
    document.getElementById("offsetDisplay").innerHTML = offSetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = parseInt(offSetDspl) + parseInt(10);
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
});

//Esto será útil para los spans del perfil y el mensaje de "listando X sueños".
function listarCantSueUsuPerf(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var paquete = "funcion=cantSueUsuario&cod_usu="+cod_usu;
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadSuenosUserProf").html(res);
    }).fail(function(){
        $("#cantidadSuenosUserProf").html("Algo falló.");
    });
}

function listarCantLikesRecUsu(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var paquete = "funcion=cantLikesRecUsuario&cod_usu="+cod_usu;
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxSuenos.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadMegustaUserProf").html(res);
    }).fail(function(){
        $("#cantidadMegustaUserProf").html("Algo falló.");
    });
}

function listarCantSegUsu(){
    var cod_usu = document.getElementById("cod_usuHid").value;
    var paquete = "function=getSeguidoresUsuario&cod_usu="+cod_usu;
    $.ajax({
        type: "POST",
        url: "http://anotasuenos:8080/CRUDs/handlerAuxUsuario.php",
        dataType: "html",
        data: paquete,
    }).done(function(res){
        $("#cantidadSeguidoresUserProf").html(res);
    }).fail(function(){
        $("#cantidadSeguidoresUserProf").html("Algo falló.");
    });
}

//funcion heightTXA 
//Input: cántidad de caracteres de un sueño
//Output: Altura de textarea que ocupará el sueño.
function heightTXA($cantidadCarac){
    $altoTXA = "height:100px;";
    if($cantidadCarac <= 100){
        $altoTXA = "height:50px;";
    }
    if($cantidadCarac > 100 && $cantidadCarac <= 200){
        $altoTXA = "height:135px;";
    }
    if($cantidadCarac > 200 && $cantidadCarac <=300){
        $altoTXA = "height:190px;";
    }
    if($cantidadCarac > 300 && $cantidadCarac <= 400){
        $altoTXA = "height:160px;";
    }
    if($cantidadCarac > 400){
        $altoTXA = "height:180px;";
    }
    return $altoTXA;
}

//Offsets.
//TODO: Encontrar la manera de que los offsets se apliquen a cada filtro.
//Idea: Mostrar el filtro actual en algun lado, ponerle id que cambie cuando se cambie de filtro
//Tomar el filtro en estas funciones y mandarlo a la consulta, de alguna manera. Quizás cambiar la opcion con una variable.
$('#siguientes10').click(function(){
    //Se consigue un nuevo offset para la consulta sql y se le suma 10, haciendo que avance a los siguientes
    //10 registros.
    console.log("-----------INICIO BTNSIGUIENTE-----------");
    console.log("Botón siguientes 10 - Presionado, iniciando");
    var newOffset = parseInt(document.getElementById("offsetDisplay").innerHTML) + parseInt(10);
    
    //Estos dos deben ser iguales siempre 0 - 0 -> 10 - 10
    //En este caso, se hace automáticamente.
    var cod_usu = document.getElementById("cod_usuHid").value;
    var offset = "function=mostrarSueCustomQuery&opcion=noPVnoM18User&cod_usu="+cod_usu+"&offset="+newOffset;
    var offsetDspl = newOffset;
    var offsetLimDspl = parseInt(offsetDspl) + parseInt(10);
    console.log("Siguientes 10: Variables definidas");

    // var limite = parseInt(document.getElementById("cantidadTotalSuenos").innerHTML);
    // if(limite > newOffset && limite < offsetLimDspl){
    //     offset = "function=mostrarSueCustomQuery&opcion=noPVnoM18&offset="+limite;
    // }

    //Mostrar nuevos valores en la página
    console.log("Funcion cambiarSpans llamada");
    cambiarSpans(offsetDspl,offsetLimDspl);
    
    //Limitar la cantidad máxima de sueños que se pueden mostrar.
    // console.log(document.getElementById("cantidadTotalSuenos").innerHTML);
    

    //TODO: Limitar la cantidad de registros máxima a la cantidad total de registros.

    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
    console.log("Botón siguientes 10 - Ajax mostrando");
    console.log("-----------FIN BTNSIGUIENTE-----------");
    scrollTop();
});

$('#anteriores10').click(function(){
    console.log("-----------INICIO BTNANTERIORES-----------");

    //Conseguir offset "antiguo" o sea, el que está en la página en este momento.
    var oldOffSet = parseInt(document.getElementById("offsetDisplay").innerHTML);

    //Conseguir el nuevo offset, este es básicamente el offset actual - 10. Para retroceder.
    var newOffset = parseInt(document.getElementById("offsetDisplay").innerHTML) - parseInt(10);

    //Revisar si estamos al inicio de la página, con offset 0, si es así, abortar ejecución con el return.
    if((parseInt(oldOffSet) == parseInt(0))  ){
        alert("Ya estás en el inicio");
        return null;
    }

    //Definir variables para el offset.
    var cod_usu = document.getElementById("cod_usuHid").value;
    var offset = "function=mostrarSueCustomQuery&opcion=noPVnoM18User&cod_usu="+cod_usu+"&offset="+parseInt(newOffset);
    var offsetDspl = newOffset;
    var offsetLim = parseInt(offsetDspl) + parseInt(10);
    console.log("Anteriores 10: Variables definidas");
    
    //Cambiar los spans con los nuevos límites.
    console.log("Funcion cambiarSpans llamada");
    cambiarSpans(offsetDspl,offsetLim);
    
    $.ajax({
        type: "GET",
        url: "http://anotasuenos:8080/CRUDs/mostrarSuenos.php",
        dataType: "html",
        data: offset,
    }).done(function(respuesta){
        $("#mostrarSuenosPublic").html(respuesta);
    }).fail(function(){
        $("#mostrarSuenosPublic").html("No se pudieron recuperar los registros.");
    });
    console.log("Anteriores 10 - Ajax mostrando");
    console.log("-----------FIN BTNANTERIORES-----------");
    scrollTop();
});

function cambiarSpans(offsetDspl, offsetLimDspl){
    console.log("CAMBIANDO SPANS");
    console.log("nuevos spans: "+offsetDspl+" - "+offsetLimDspl);
    document.getElementById("offsetDisplay").innerHTML =  offsetDspl;
    document.getElementById("offsetLimDisplay").innerHTML = offsetLimDspl;
    console.log("SPANS CAMBIADOS");
}

function scrollTop(){
    $("html,body").animate({ scrollTop: 0}, "slow");
    return false;
}

</script>
</html>