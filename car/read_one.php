<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include_once "../config/database.php";
include_once "../objects/car.php";

$database = new Database();
$db = $database->getConnection();
$car = new Car($db);

$car->id = isset($_GET['id']) ? $_GET['id'] : die();

$car->readOne();

if($car->label != null) {
    http_response_code(200);

    $response = array(
        "status" => "ok",
        "errorcode" => 0,
        "message" => "success",
        "car" => array(
            "id" => $car->id,
            "label" => $car->label,
            "vin" => $car->vin,
            "mark" => $car->mark,
            "model" => $car->model,
            "year" => $car->year,
            "gosnum" => $car->gosnum,
            "mileage" => $car->mileage,
            "user_id" => $car->user_id
        )
    );

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} else {
    http_response_code(200);

    $response = array(
        "status" => "error",
        "errorcode" => 1,
        "message" => "Car not found"
    );

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}


?>