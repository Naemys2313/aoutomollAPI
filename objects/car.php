<?php
class Car {
    private $database;
    private $table_name = "cars";
    
    public $id;
    public $label;
    public $vin;
    public $model_id;
    public $mark_id;
    public $year;
    public $gosnum;
    public $mileage;
    public $user_id;

    function __construct($db) {
        $this->database = $db;
    }

    function read($columns, $selection, $selection_args, $limit) {
        return $this->database->read($this->table_name, $columns, $selection, $selection_args, $limit);
    }

    function readOne($columns) {
        $selection_args = array();
        array_push($selection_args, $this->id);
        $stmt = $this->database->read($this->table_name, $columns, "id=?", $selection_args);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->label = $row['label'];
        $this->vin = $row['vin'];
        $this->model_id = $row['model_id'];
        $this->mark_id = $row['mark_id'];
        $this->year = $row['year'];
        $this->gosnum = $row['gosnum'];
        $this->mileage = $row['mileage'];
        $this->user_id = $row['user_id'];
    }

    function readByVIN() {
        $query = "SELECT * FROM cars WHERE vin = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->vin);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->label = $row['label'];
        $this->model_id = $row['model_id'];
        $this->mark_id = $row['mark_id'];
        $this->year = $row['year'];
        $this->gosnum = $row['gosnum'];
        $this->mileage = $row['mileage'];
        $this->user_id = $row['user_id'];
    }

    function readByGosnum() {
        $query = "SELECT * FROM cars WHERE gosnum = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->gosnum);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->vin = $row['vin'];
        $this->label = $row['label'];
        $this->model_id = $row['model_id'];
        $this->mark_id = $row['mark_id'];
        $this->year = $row['year'];
        $this->mileage = $row['mileage'];
        $this->user_id = $row['user_id'];
    }

    function findCarsByUserId() {
        $query = "SELECT * FROM cars WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        return $stmt;
    }

    function create() {
        $query = "INSERT INTO cars SET label=:label,
                                                vin=:vin,
                                                model_id=:model_id,
                                                mark_id=:mark_id,
                                                year=:year,
                                                gosnum=:gosnum,
                                                mileage=:mileage,
                                                user_id=:user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":label", htmlspecialchars(strip_tags($this->label)));
        $stmt->bindParam(":vin", htmlspecialchars(strip_tags($this->vin)));
        $stmt->bindParam(":model_id", htmlspecialchars(strip_tags($this->model_id)));
        $stmt->bindParam(":mark_id", htmlspecialchars(strip_tags($this->mark_id)));
        $stmt->bindParam(":year", htmlspecialchars(strip_tags($this->year)));
        $stmt->bindParam(":gosnum", htmlspecialchars(strip_tags($this->gosnum)));
        $stmt->bindParam(":mileage", htmlspecialchars(strip_tags($this->mileage)));
        $stmt->bindParam(":user_id", htmlspecialchars(strip_tags($this->user_id)));

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function update() {
        $query = "UPDATE cars SET label=:label,
                                            vin=:vin,
                                            model_id=:model_id,
                                            mark_id=:mark_id,
                                            year=:year,
                                            gosnum=:gosnum,
                                            mileage=:mileage,
                                            user_id=:user_id
                                            WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", htmlspecialchars(strip_tags($this->id)));
        $stmt->bindParam(":label",htmlspecialchars(strip_tags( $this->label)));
        $stmt->bindParam(":vin", htmlspecialchars(strip_tags($this->vin)));
        $stmt->bindParam(":model_id", htmlspecialchars(strip_tags($this->model_id)));
        $stmt->bindParam(":mark_id", htmlspecialchars(strip_tags($this->mark_id)));
        $stmt->bindParam(":year", htmlspecialchars(strip_tags($this->year)));
        $stmt->bindParam(":gosnum", htmlspecialchars(strip_tags($this->gosnum)));
        $stmt->bindParam(":mileage", htmlspecialchars(strip_tags($this->mileage)));
        $stmt->bindParam(":user_id", htmlspecialchars(strip_tags($this->user_id)));

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    function delete() {
        $query = "DELETE FROM cars WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, htmlspecialchars(strip_tags($this->id)));
        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>