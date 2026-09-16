<?php
    $email = $_POST['email'];
    $data_nascimento = $_POST['data_nascimento'];

    $partes_email = explode('@', $email);
    if (count($partes_email) != 2) {
        header('Location: formulario.html');
        exit();
    }

    $partes_data = explode('/', $data_nascimento);
    if (count($partes_data) != 3 || strlen($partes_data[0]) != 2 || strlen($partes_data[1]) != 2 || strlen($partes_data[2]) != 4) {
        header('Location: formulario.html');
        exit();
    }

    $receber_emails = isset($_POST['receber_emails']) ? $_POST['receber_emails'] : 'Não';

    $sexo = isset($_POST['sexo']) ? $_POST['sexo'] : 'Não informado';

    echo '<h1>Dados Cadastrados</h1>';
    echo 'Nome: ' . $_POST['nome'] . '<br>';
    echo 'E-mail: ' . $email . '<br>';
    echo 'Sexo: ' . $sexo . '<br>';
    echo 'Data de Nascimento: ' . $data_nascimento . '<br>';
    echo 'Endereço: ' . $_POST['endereco'] . '<br>';
    echo 'Bairro: ' . $_POST['bairro'] . '<br>';
    echo 'Estado: ' . $_POST['estado'] . '<br>';
    echo 'Cidade: ' . $_POST['cidade'] . '<br>';
    echo 'CEP: ' . $_POST['cep'] . '<br>';
    echo 'Deseja receber e-mails: ' . $receber_emails . '<br>';
?>