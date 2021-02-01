<?php
class CarModel {
    private $conn;

    public $id;
    public $title;

    function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO car_models SET title=:title";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", htmlspecialchars(strip_tags($this->title)));
        
        if($stmt->execute()) {
            return true;
        }

        return false;

    }

    function readOne() {
        $query = "SELECT * FROM car_models WHERE title=:title";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", $this->title);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
    }

    function readOneById() {
        $query = "SELECT * FROM car_models WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->title = $row['title'];
    }
}
?>