<?php
class CarModel {
    private $database;

    public $id;
    public $title;

    function __construct($database) {
        $this->database = $database;
    }

    function create() {
        // $query = "INSERT INTO car_models SET title=:title";
        // $stmt = $this->conn->prepare($query);
        // $stmt->bindParam(":title", htmlspecialchars(strip_tags($this->title)));
        
        // if($stmt->execute()) {
        //     return true;
        // }

        // return false;

        $content_values = array(
            "title" => $this->title
        );

        return $this->database->insert("car_models", $content_values);

    }

    function read($selection = null, $selection_args = null, $limit = null) {
        $stmt = $this->database->read("car_models", null, $selection, $selection_args, $limit);

        return $stmt;
    }

    function readOne($selection = null, $selection_args = null) {
        if($selection == null) {
            $selection = "id=?";
            $selection_args = array();
            array_push($selection_args, $this->id);
        }

        $stmt = $this->read($selection, $selection_args, 1);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->id = $row['id'];
        $this->title = $row['title'];
    }

    function update($selection = null) {
        if($selection == null) {
            $selection = "id=:id";
        }

        $content_values = array(
            "id" => $this->id
        );

        if(!empty($this->title)) {
            $content_values["title"] = htmlspecialchars(strip_tags($this->title));
        }
        return $this->database->update("car_models", $content_values, $selection);
    }
}
?>