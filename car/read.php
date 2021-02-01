<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/car.php';

$database = new Database();
$db = $database->getConnection();
$car = new Car($db);

$stmt = $car->read();
$num = $stmt->rowCount();

if($num > 0) {
    $response = array();
    $response["status"] = "ok";
    $response["errorcode"] = 0;
    $response["message"] = "success";
    $response["cars"] = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $car_item = array(
            "id" => $id,
            "label" => $label,
            "vin" => $vin,
            "mark" => $mark,
            "model" => $model,
            "year" => $year,
            "gosnum" => $gosnum,
            "mileage" => $mileage,
            "user_id" => $user_id
        );

        array_push($response["cars"], $car_item);
    }

    http_response_code(200);

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} else {
    http_response_code(200);

    $response = array();
    $response["status"] = "error";
    $response["errorcode"] = 1;
    $response["message"] = "Cars not found.";

    echo json_encode($response);
}

?>