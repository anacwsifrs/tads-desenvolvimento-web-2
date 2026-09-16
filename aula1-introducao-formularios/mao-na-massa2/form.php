<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Aluno</title>
</head>
<body>
    <form action="cadastrar.php" method="post">
        <fieldset>
            <legend>Cadastrar Aluno</legend>

            <label>
                Nome: <input type="text" name="nome" />
            </label>
            <br><br>

            <label>
                Matéria: <input type="text" name="materia" />
            </label>
            <br><br>

            <label>
                Nota: <input type="text" name="nota" />
            </label>
            <br><br>

            <input type="submit" value="Enviar" />
        </fieldset>
    </form>
</body>
</html>