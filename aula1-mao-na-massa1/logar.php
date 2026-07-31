<?php
    echo '<h1>' . $_POST['nome'] . '</h1> <br>';
    echo 'Idade: ' . $_POST['idade'] . '<br><br>';
    echo 'Sexo: ' . $_POST['sex'] . '<br><br>';

    echo 'Esportes ' . '<br>';
        for($i = 1; $i<3; $i++){
            echo '<li>' . $_POST['esporte[$i]'] . '</li';
        }
        
    echo 'Sorvete preferido: '. $_POST['sabor'] . '<br><br>';
    echo '<b> Sobre você </b>: ' . $_POST['mensagem'] . '<br><br>';
