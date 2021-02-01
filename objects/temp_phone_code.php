<?php
class TempPhoneCode {
    private $database ;

    public $id;
    public $phone;
    public $code;
    public $temp_date;

    function __construct($database) {
        $this->database = $database;
    }

    function read($columns = null, $selection = null, $selection_args = null, $limit = null) {
        return $this->database->read("temp_phone_code", $columns, $selection, $selection_args, $limit);
    }

    function readOne($columns = null, $selection = null, $selection_args = null) {
        if($selection == null) {
            $selection = "id=?";
            $selection_args = array();
            array_push($selection_args, $this->id);
        }
        $stmt = $this->read(null, $selection, $selection_args, 1);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->phone = $row['phone'];
        $this->code = $row['code'];
        $this->temp_date = $row['date'];
    }

    function readByCodeAndPhone($columns = null) {
        $selection = "code=? AND phone=?";
        $selection_args = array();
        array_push($selection_args, $this->code);
        array_push($selection_args, $this->phone);
        $this->readOne($columns, $selection, $selection_args);
    }

    function delete($selection = null, $selection_args = null) {
        if($selection == null) {
            $selection = "id=?";
            $selection_args = array();
            array_push($selection_args, $this->id);
        }

        return $this->database->delete("temp_phone_code", $selection, $selection_args);
    }

    function create() {
        $content_values = array(
            "phone" => htmlspecialchars(strip_tags($this->phone)),
            "code" => htmlspecialchars(strip_tags($this->code)),
            "date" => htmlspecialchars(strip_tags(date("Y-m-d H-i-s")))
        );

        return $this->database->insert("temp_phone_code", $content_values);
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