<?php
class TempPhoneCode {
    private $conn ;

    public $id;
    public $phone;
    public $code;
    public $temp_date;

    function __construct($db) {
        $this->conn = $db;
    }

    function readOne() {
        $query = "SELECT * FROM temp_phone_code WHERE phone = ? ORDER BY date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, htmlspecialchars(strip_tags($this->phone)));
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->code = $row['code'];
        $this->temp_date = $row['date'];
    }

    function findByCode() {
        $query = "SELECT * FROM temp_phone_code WHERE code = :code AND phone = :phone ORDER BY date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":code", htmlspecialchars(strip_tags($this->code)));
        $stmt->bindParam(":phone", htmlspecialchars(strip_tags($this->phone)));
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->temp_date = $row['date'];
    }

    function delete() {
        $query = "DELETE FROM temp_phone_code WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", htmlspecialchars(strip_tags($this->id)));
        
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    function create() {
        $query = "INSERT INTO temp_phone_code SET phone=:phone, code=:code, date=:date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone", htmlspecialchars(strip_tags($this->phone)));
        $stmt->bindParam(":code", htmlspecialchars(strip_tags($this->code)));
        $stmt->bindParam(":date", date("Y-m-d H:i:s"));
        
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    function isActiveCode() {
        $end_date = date("Y-m-d H:i:s", strtotime("+5 minute", strtotime($this->temp_date)));
        if(strtotime(date("Y-m-d H:i:s")) < strtotime($end_date)) {
            return true;
        }

        return false;
    }
}
?>