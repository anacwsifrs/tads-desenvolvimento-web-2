<?php
$diretorio = "uploads/";
if (is_dir($diretorio)) {
    $arquivos = scandir($diretorio);
    echo "<h2>Arquivos Disponíveis:</h2><ul>";
    foreach ($arquivos as $arquivo) {
        if ($arquivo !== "." && $arquivo !== "..") {
            echo "<li><a href='$diretorio$arquivo' download>$arquivo</a></li>";
        }
    }
    echo "</ul>";
} else {
    echo "A pasta de uploads não existe.";
}
?>