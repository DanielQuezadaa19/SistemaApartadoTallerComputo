<?php
require_once "../config/database.php";

class Docente {

    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Crear docente
    public function crear($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Docente 
            (nombre, apellidoPaterno, apellidoMaterno, correo, password_hash, idCarrera)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data["nombre"],
            $data["apellidoPaterno"],
            $data["apellidoMaterno"],
            $data["correo"],
            password_hash($data["password"], PASSWORD_DEFAULT),
            $data["idCarrera"]
        ]);
    }

    // Buscar docente por correo (login)
    public function obtenerPorCorreo($correo) {
        $stmt = $this->db->prepare("
            SELECT * FROM Docente WHERE correo = ?
        ");
        $stmt->execute([$correo]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function obtenerPorId($idDocente) {
        $stmt = $this->db->prepare("
            SELECT d.*, c.nombre AS carrera
            FROM Docente d
            JOIN Carrera c ON d.idCarrera = c.idCarrera
            WHERE d.idDocente = ?
        ");
        $stmt->execute([$idDocente]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function obtenerTodos() {
        $stmt = $this->db->query("
            SELECT d.idDocente, d.nombre, d.apellidoPaterno, d.apellidoMaterno,
                   d.correo, c.nombre AS carrera
            FROM Docente d
            JOIN Carrera c ON d.idCarrera = c.idCarrera
            ORDER BY d.apellidoPaterno
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function actualizar($idDocente, $data) {
        $stmt = $this->db->prepare("
            UPDATE Docente
            SET nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, 
                correo = ?, idCarrera = ?
            WHERE idDocente = ?
        ");

        return $stmt->execute([
            $data["nombre"],
            $data["apellidoPaterno"],
            $data["apellidoMaterno"],
            $data["correo"],
            $data["idCarrera"],
            $idDocente
        ]);
    }

    
    public function cambiarPassword($idDocente, $password) {
        $stmt = $this->db->prepare("
            UPDATE Docente SET password_hash = ? WHERE idDocente = ?
        ");

        return $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $idDocente
        ]);
    }
}
