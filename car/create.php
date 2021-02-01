<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/car.php';

$database = new Database();
$db = $database->getConnection();
$car = new Car($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->label) &&
    !empty($data->vin) &&
    !empty($data->mark) &&
    !empty($data->model) &&
    !empty($data->year) &&
    !empty($data->gosnum) &&
    !empty($data->mileage) &&
    !empty($data->user_id)
) {
    $car->label = $data->label;
    $car->vin = $data->vin;
    $car->mark = $data->mark;
    $car->model = $data->model;
    $car->year = $data->year;
    $car->gosnum = $data->gosnum;
    $car->mileage = $data->mileage;
    $car->user_id = $data->user_id;

    if($car->create()) {
        $response = array();
        http_response_code(200);

        $response = array();
        $response["status"] = "ok";
        $response["errorcode"] = 0;
        $response["message"] = "success";

        echo json_encode($response);
    } else {
        http_response_code(200);

        $response = array();
        $response["status"] = "error";
        $response["errorcode"] = 1;
        $response["message"] = "Car not created";

        echo json_encode($response);
    }
} else {
    http_response_code(200);

    $response = array();
    $response["status"] = "error";
    $response["errorcode"] = 1;
    $response["message"] = "Car not created. Data not filled.";

    echo json_encode($response);
}
?>