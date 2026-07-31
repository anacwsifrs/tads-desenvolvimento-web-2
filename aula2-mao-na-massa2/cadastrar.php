<?php
    $aluno = [
        "nome" => $_POST['nome'],
        "materia" => $_POST['materia'],
        "nota" => $_POST['nota']
    ];

    echo '<h1>Dados do Aluno</h1>';
    echo 'Nome: ' . $aluno['nome'] . '<br>';
    echo 'Matéria: ' . $aluno['materia'] . '<br>';
    echo 'Nota: ' . $aluno['nota'] . '<br>';
?>