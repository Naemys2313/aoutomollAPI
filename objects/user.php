<?php
class User {
    private $conn;
    private $database;

    public $id;
    public $name;
    public $birthday;
    public $phone;
    public $state;
    public $city;
    public $comment;

    public function __construct($db, $database) {
        $this->conn = $db;
        $this->database = $database;
    }

    function read($columns, $selection, $selection_args, $limit) {
        return $this->database->read("users", $columns, $selection, $selection_args, $limit);
    }

    function readOne() {
        $query =  "SELECT id, name, phone, birthday, state, city, comment FROM users WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['name'];
        $this->phone = $row['phone'];
        $this->birthday = $row['birthday'];
        $this->state = $row['state'];
        $this->city = $row['city'];
        $this->comment = $row['comment'];
    }

    function readByPhone() {
        $query =  "SELECT id, name, phone, birthday, state, city, comment FROM users WHERE phone = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->phone);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->birthday = $row['birthday'];
        $this->state = $row['state'];
        $this->city = $row['city'];
        $this->comment = $row['comment'];
    }

    function create() {
        // $query = "INSERT INTO users SET name=:name, phone=:phone, birthday=:birthday, state=:state, city=:city, comment=:comment";
        
        // $stmt = $this->conn->prepare($query);
        
        // $this->name = htmlspecialchars(strip_tags($this->name));
        // $this->birthday = htmlspecialchars(strip_tags($this->birthday));
        // $this->phone = htmlspecialchars(strip_tags($this->phone));
        // $this->state = htmlspecialchars(strip_tags($this->state));
        // $this->city = htmlspecialchars(strip_tags($this->city));
        // $this->comment = htmlspecialchars(strip_tags($this->comment));

        // $stmt->bindParam(":name", $this->name);
        // $stmt->bindParam(":birthday", $this->birthday);
        // $stmt->bindParam(":phone", $this->phone);
        // $stmt->bindParam(":state", $this->state);
        // $stmt->bindParam(":city", $this->city);
        // $stmt->bindParam(":comment", $this->comment);

        // if($stmt->execute()) {
        //     return true;
        // }

        // else false;

        $content_values = array(
            "name" => htmlspecialchars(strip_tags($this->name)),
            "birthday" => htmlspecialchars(strip_tags($this->birthday)),
            "phone" => htmlspecialchars(strip_tags($this->phone)),
            "state" => htmlspecialchars(strip_tags($this->state)),
            "city" => htmlspecialchars(strip_tags($this->city)),
            "comment" => htmlspecialchars(strip_tags($this->comment))
        );
        
        $this->database->insert("users", $content_values);
    }

    function update() {
        $query = "UPDATE users SET name=:name, phone=:phone, birthday=:birthday, state=:state, city=:city, comment=:comment WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", htmlspecialchars(strip_tags($this->id)));
        $stmt->bindParam(":name", htmlspecialchars(strip_tags($this->name)));
        $stmt->bindParam(":phone", htmlspecialchars(strip_tags($this->phone)));
        $stmt->bindParam(":birthday", htmlspecialchars(strip_tags($this->birthday)));
        $stmt->bindParam(":state", htmlspecialchars(strip_tags($this->state)));
        $stmt->bindParam(":city", htmlspecialchars(strip_tags($this->city)));
        $stmt->bindParam(":comment", htmlspecialchars(strip_tags($this->comment)));

        $stmt->execute();

        if($stmt->execute()) {
            return true; 
        }

        return false;
    }

    function delete() {
        $query = "DELETE FROM users WHERE id=?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, htmlspecialchars(strip_tags($this->id)));
    
        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>