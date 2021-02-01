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

    function __construct($database) {
        $this->database = $database;
    }

    function read($columns, $selection = null, $selection_args = null, $limit = null) {
        return $this->database->read("cars", $columns, $selection, $selection_args, $limit);
    }

    function readOne($columns, $selection = null, $selection_args = null, $limit = null) {
        if($selection == null) {
            $selection = "id=?";
            $selection_args = array();
            array_push($selection_args, $this->id);
        }
        
        $stmt = $this->database->read("cars", $columns, $selection, $selection_args, $limit);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
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
        // $query = "SELECT * FROM cars WHERE vin = ? LIMIT 0,1";
        // $stmt = $this->conn->prepare($query);
        // $stmt->bindParam(1, $this->vin);
        // $stmt->execute();

        // $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // $this->id = $row['id'];
        // $this->label = $row['label'];
        // $this->model_id = $row['model_id'];
        // $this->mark_id = $row['mark_id'];
        // $this->year = $row['year'];
        // $this->gosnum = $row['gosnum'];
        // $this->mileage = $row['mileage'];
        // $this->user_id = $row['user_id'];

        $selection_args = array();
        array_push($selection_args, $this->vin);

        $this->readOne(null, "vin=?", $selection_args);
    }

    function readByGosnum() {
        // $query = "SELECT * FROM cars WHERE gosnum = ? LIMIT 0,1";
        // $stmt = $this->conn->prepare($query);
        // $stmt->bindParam(1, $this->gosnum);
        // $stmt->execute();

        // $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // $this->id = $row['id'];
        // $this->vin = $row['vin'];
        // $this->label = $row['label'];
        // $this->model_id = $row['model_id'];
        // $this->mark_id = $row['mark_id'];
        // $this->year = $row['year'];
        // $this->mileage = $row['mileage'];
        // $this->user_id = $row['user_id'];

        $selection_args = array();
        array_push($selection_args, $this->gosnum);
        $this->readOne(null, "gosnum=?", $selection_args);
    }

    function readCarsByUserId($limit = null) {
        $selection_args = array();
        array_push($selection_args, $this->user_id);

        return $this->read(null, "user_id=?", $selection_args, $limit);
    }

    function create() {
        // $query = "INSERT INTO cars SET label=:label,
        //                                         vin=:vin,
        //                                         model_id=:model_id,
        //                                         mark_id=:mark_id,
        //                                         year=:year,
        //                                         gosnum=:gosnum,
        //                                         mileage=:mileage,
        //                                         user_id=:user_id";

        // $stmt = $this->conn->prepare($query);

        // $stmt->bindParam(":label", htmlspecialchars(strip_tags($this->label)));
        // $stmt->bindParam(":vin", htmlspecialchars(strip_tags($this->vin)));
        // $stmt->bindParam(":model_id", htmlspecialchars(strip_tags($this->model_id)));
        // $stmt->bindParam(":mark_id", htmlspecialchars(strip_tags($this->mark_id)));
        // $stmt->bindParam(":year", htmlspecialchars(strip_tags($this->year)));
        // $stmt->bindParam(":gosnum", htmlspecialchars(strip_tags($this->gosnum)));
        // $stmt->bindParam(":mileage", htmlspecialchars(strip_tags($this->mileage)));
        // $stmt->bindParam(":user_id", htmlspecialchars(strip_tags($this->user_id)));

        // if ($stmt->execute()) {
        //     return true;
        // }

        // return false;

        $content_values = array(
            "label" => htmlspecialchars(strip_tags($this->label)),
            "vin" => htmlspecialchars(strip_tags($this->vin)),
            "gosnum" => htmlspecialchars(strip_tags($this->gosnum)),
            "mark_id" => htmlspecialchars(strip_tags($this->mark_id)),
            "model_id" => htmlspecialchars(strip_tags($this->model_id)),
            "user_id" => htmlspecialchars(strip_tags($this->user_id)),
            "mileage" => htmlspecialchars(strip_tags($this->mileage)),
            "year" => htmlspecialchars(strip_tags($this->year)),
        );

        return $this->database->insert("cars", $content_values);
    }

    function update($selection) {
        $content_values = array(
            "id" => htmlspecialchars(strip_tags($this->id))
        );

        if(!empty($this->label)) {
            $content_values["label"] = $this->label;
        }

        if(!empty($this->vin)) {
            $content_values["vin"] = $this->vin;
        }

        if(!empty($this->gosnum)) {
            $content_values["gosnum"] = $this->gosnum;
        }

        if(!empty($this->mark_id)) {
            $content_values["mark_id"] = $this->mark_id;
        }

        if(!empty($this->model_id)) {
            $content_values["model_id"] = $this->model_id;
        }

        if(!empty($this->user_id)) {
            $content_values["user_id"] = $this->user_id;
        }

        if(!empty($this->mileage)) {
            $content_values["mileage"] = $this->mileage;
        }

        if(!empty($this->year)) {
            $content_values["year"] = $this->year;
        }

        return $this->database->update("cars", $content_values, $selection);
    }

    function delete($selection, $selection_args) {
        return $this->database->delete("cars", $selection, $selection_args);
    }
}
?>