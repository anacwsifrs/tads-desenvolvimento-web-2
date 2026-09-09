<?php
class Conexao {
    private static $instancia;

    public static function getConexao() {
        if (!isset(self::$instancia)) {
            try {
                self::$instancia = new PDO('mysql:host=localhost;dbname=brecho_db;charset=utf8', 'root', '');
                self::$instancia->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro de conexão: " . $e->getMessage());
            }
        }
        return self::$instancia;
    }
}
?>