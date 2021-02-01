<?php
class Database {
    private $host = "localhost";
    private $db_name = "automoll";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name, $this->username, $this->password);
            $this->conn->exec("Set name utf8");
        } catch (PDOException $exception) {
            echo "Connection error: ".$exception->getMessage();
        }

        return $this->conn;
    }

    function read($table_name, $columns, $selection, $selection_args, $limit) {
        $columns_string = "";
        if($columns == null) {
            $columns_string = "*";
        } else {
            foreach ($columns as $column) {
                $div = ", ";
                if(empty($columns_string)) {
                    $columns_string = $column;
                } else {
                    $columns_string = $columns_string.$div.$column;
                }
            }
        }

        $selection = $this->getSelection($selection);

        if($limit != null) {
            $limit = "LIMIT " .$limit;
        } else {
            $limit = "";
        }
        
        $query = "SELECT " .$columns_string. " FROM " .$table_name. " " .$selection. "  " .$limit;
        $stmt = $this->conn->prepare($query);

        $this->bindSelections($stmt, $selection_args);
        
        $stmt->execute();

        return $stmt;
    }

    function insert($table_name, $content_values) {
        $set_query = $this->getSetQuery($content_values);

        $query = "INSERT INTO " .$table_name. " SET ".$set_query;
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $content_values);
        
        if ($stmt->execute()) {
            return true;
        }

        else false;
    }

    function update($table_name, $content_values, $selection = null) {
        $set_query = $this->getSetQuery($content_values);

        $selection = $this->getSelection($selection);

        $query = "UPDATE " .$table_name. " SET " .$set_query. " " .$selection;
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $content_values, false);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    function delete($table_name, $selection, $selection_args) {
        $selection = $this->getSelection($selection);
        $query = "DELETE FROM ".$table_name. " " .$selection;
        $stmt = $this->conn->prepare($query);
        $this->bindSelections($stmt, $selection_args);
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    private function getSetQuery($content_values) {
        $set_query = "";
        $keys = array_keys($content_values);
        foreach($keys as $key) {
            if(!empty($set_query)) {
               $set_query = $set_query.", ";            
            }

            $set_query = $set_query.$key. "=:" .$key;
        }

        return $set_query;
        
    }

    private function bindParams($stmt, $content_values, $with_nulable = true) {
        $keys = array_keys($content_values);
        foreach($keys as $key) {
            if(!$with_nulable && empty($content_values[$key])) {
                continue;
            }
            $stmt->bindParam(":".$key, $content_values[$key]);
        }
    }

    private function bindSelections($stmt, $selection_args) {
        if($selection_args != null) {
            $index = 1;
            foreach($selection_args as $selection_arg) {
                $stmt->bindParam($index, $selection_arg);
                $index++;
            }
        }
    }

    private function getSelection($selection) {
        if($selection != null) {
            return "WHERE " .$selection;
        } else {
            return "";
        }    
    }
}
?>