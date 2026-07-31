<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="logar.php" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>Sobre você:</legend>

            <label>
                Nome: <input type="text" name="nome"/>
            </label>
            <br>
            <label>
                Idade: <input type="text" name="idade"/>
            </label>

            <br>
            <br>

            Sexo
            <br>
            <input type="radio" name="sex" value="m" /> Masculino
            <input type="radio" name="sex" value="f" /> Feminino

            <br>
            <br>

            Marque seus esportes favoritos <br>
            <label> <input type="checkbox" name="esporte[]" value="basquete" /> Basquete </label> <br/>
            <label> <input type="checkbox" name="esporte[]" value="futebol" /> Futebol </label> <br/>
            <label> <input type="checkbox" name="esporte[]" value="bocha" /> Bocha </label> <br/>

            <br>
            <br>

            Qual o sabor de seu sorvere preferido <br>
            <select name="sabor">
                    <option value="abacaxi">Abacaxi</option>
                    <option value="morango">Morango</option>
                    <option value="chocolate">Chocolate</option>
                    <option value="creme">Creme</option>
            </select>

            <br>
            <br>

            Fale mais sobre você <br>
            <textarea name="mensagem">Digite aqui a msg!</textarea>
            <br>
            <input type="submit" value="Enviar"/>
        </fieldset>
    </form>  
</body>
</html>
          
                