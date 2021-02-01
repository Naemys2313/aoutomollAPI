<?php
class CarMark {
    private $conn;

    public $id;
    public $title;

    function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO car_marks SET title=:title";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", htmlspecialchars(strip_tags($this->title)));
        
        if($stmt->execute()) {
            return true;
        }

        return false;

    }

    function read() {
        $query = "SELECT * FROM car_marks";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function readOne() {
        $query = "SELECT * FROM car_marks WHERE title=:title";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", htmlspecialchars(strip_tags($this->title)));
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
    }

    function readOneById() {
        $query = "SELECT * FROM car_marks WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", htmlspecialchars(strip_tags($this->id)));
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->title = $row['title'];
    }

    function update() {
        $query = "UPDATE car_marks SET title=:title WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":title", htmlspecialchars(strip_tags($this->title)));
        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>