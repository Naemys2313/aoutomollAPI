<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/car.php';

$database = new Database();
$db = $database->getConnection();
$car = new Car($db);

$car->id = isset($_GET['id']) ? $_GET['id'] : die();

if($car->delete()) {
    http_response_code(200);

    $response = array(
        "status" => "ok",
        "errorcode" => 0,
        "message" => "success"
    );

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(200);

    $response = array(
        "status" => "error",
        "errorcode" => 1,
        "message" => "Car not delete"
    );

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?>