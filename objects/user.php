<?php
class User {
    // private $conn;
    private $database;

    public $id;
    public $name;
    public $birthday;
    public $phone;
    public $state;
    public $city;
    public $comment;

    public function __construct($database) {
        $this->database = $database;
    }

    function read($columns, $selection, $selection_args, $limit) {
        return $this->database->read("users", $columns, $selection, $selection_args, $limit);
    }

    function readOne($selection = null, $selection_args = null) {        
        if(empty($selection)) {
            $selection_args = array();
            array_push($selection_args, $this->id);
            $selection = "id=?";
        }

        $stmt = $this->read(null, $selection, $selection_args, null);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->phone = $row['phone'];
        $this->birthday = $row['birthday'];
        $this->state = $row['state'];
        $this->city = $row['city'];
        $this->comment = $row['comment'];
    }

    function readByPhone() {
        $selection_args = array();
        array_push($selection_args, $this->phone);

        $this->readOne("phone=?", $selection_args);
    }

    function create() {
        $content_values = array(
            "name" => htmlspecialchars(strip_tags($this->name)),
            "birthday" => htmlspecialchars(strip_tags($this->birthday)),
            "phone" => htmlspecialchars(strip_tags($this->phone)),
            "state" => htmlspecialchars(strip_tags($this->state)),
            "city" => htmlspecialchars(strip_tags($this->city)),
            "comment" => htmlspecialchars(strip_tags($this->comment))
        );
        
        return $this->database->insert("users", $content_values);
    }

    function update($selection) {
        // $query = "UPDATE users SET name=:name, phone=:phone, birthday=:birthday, state=:state, city=:city, comment=:comment WHERE id=:id";

        // $stmt = $this->conn->prepare($query);
        // $stmt->bindParam(":id", htmlspecialchars(strip_tags($this->id)));
        // $stmt->bindParam(":name", htmlspecialchars(strip_tags($this->name)));
        // $stmt->bindParam(":phone", htmlspecialchars(strip_tags($this->phone)));
        // $stmt->bindParam(":birthday", htmlspecialchars(strip_tags($this->birthday)));
        // $stmt->bindParam(":state", htmlspecialchars(strip_tags($this->state)));
        // $stmt->bindParam(":city", htmlspecialchars(strip_tags($this->city)));
        // $stmt->bindParam(":comment", htmlspecialchars(strip_tags($this->comment)));

        // $stmt->execute();

        // if($stmt->execute()) {
        //     return true; 
        // }

        // return false;

        $content_values = array(
            "id" => htmlspecialchars(strip_tags($this->id))
        );

        if (!empty($this->name)) {
            $content_values["name"] = htmlspecialchars(strip_tags($this->name));
        }

        if (!empty($this->birthday)) {
            $content_values["birthday"] = htmlspecialchars(strip_tags($this->birthday));
        }

        if (!empty($this->state)) {
            $content_values["state"] = htmlspecialchars(strip_tags($this->state));
        }

        if (!empty($this->city)) {
            $content_values["city"] = htmlspecialchars(strip_tags($this->city));
        }

        if (!empty($this->comment)) {
            $content_values["comment"] = htmlspecialchars(strip_tags($this->comment));
        }

        if (!empty($this->phone)) {
            $content_values["phone"] = htmlspecialchars(strip_tags($this->phone));
        }

        return $this->database->update("users", $content_values, $selection);
    }

    function delete($selection, $selection_args) {
        // $query = "DELETE FROM users WHERE id=?";

        // $stmt = $this->conn->prepare($query);
        // $stmt->bindParam(1, htmlspecialchars(strip_tags($this->id)));
    
        // if($stmt->execute()) {
        //     return true;
        // }

        // return false;

        return $this->database->delete("users", $selection, $selection_args);
    }
}
?>