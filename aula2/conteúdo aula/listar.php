<?php
$diretorio = "uploads/";

if (is_dir($diretorio)) {
    $arquivos = scandir($diretorio);
    echo "<h2>Arquivos Disponíveis:</h2>";
    echo "<ul>";
    foreach ($arquivos as $arquivo) {
        if ($arquivo !== "." && $arquivo !== "..") {
            echo "<li>";
            echo "<a href='$diretorio$arquivo' download>$arquivo</a> ";
            echo "[<a href='deletar.php?arquivo=$arquivo'>Excluir</a>]";
            echo "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "A pasta de uploads não existe.";
}

echo "<br><a href='form.html'>Enviar novo arquivo</a>";
?>