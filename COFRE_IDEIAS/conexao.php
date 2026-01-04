<?php
    // configurar conexao com oMySQL
    $servidor = "localhost";
    $usuario = "root";
    $senha = "";
    $banco = "cofre_ideias";


    $conexao = new mysqli($servidor, $usuario,$senha,$banco);

    // verificar conexao 
    if($conexao->connect_error){
        die("error de conexao:" .$conexao->connect_error);
    }


?>