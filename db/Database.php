<?php

class Database {

    private static $host = "localhost";
    private static $db = "computer_room";
    private static $user = "root";
    private static $pass = "";
    private static $charset = "utf8mb4";

    public static function connect() {
        $dsn = "mysql:host=" . self::$host . 
               ";dbname=" . self::$db . 
               ";charset=" . self::$charset;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            return new PDO($dsn, self::$user, self::$pass, $options);
        } catch (PDOException $e) {
            
            die("Error de conexión a la base de datos");
        }
    }
}
