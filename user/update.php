<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db, $database);

$data = json_decode(file_get_contents("php://input"));

$user->id = $data->id;
$user->name = $data->name;
$user->phone = $data->phone;
$user->birthday = $data->birthday;
$user->state = $data->state;
$user->city = $data->city;

if($user->update()) {
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
    $response["message"] = "error";
}
?>