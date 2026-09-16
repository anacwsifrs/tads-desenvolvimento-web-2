<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Loja do Tio Patinhas - Acesso</title>
</head>
<body>
    <h2>Autenticação - Loja do Tio Patinhas</h2>
    <form action="autenticar.php" method="POST">
        <label for="usuario">Usuário:</label><br>
        <input type="text" id="usuario" name="usuario" required><br><br>
        
        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" required><br><br>
        
        <button type="submit">Fazer Login</button>
    </form>
</body>
</html>