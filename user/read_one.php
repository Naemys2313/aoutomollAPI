<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$user->id = isset($_GET['id']) ? $_GET['id'] : die();

$user->readOne();

if($user->name != null) {
    $user_arr = array(
        "id" => $user->id,
        "name" => $user->name,
        "phone" => $user->phone,
        "birthday" => $user->birthday,
        "state" => $user->state,
        "city" => $user->city
    );

    http_response_code(200);
    $response = array();
    $response["status"] = "ok";
    $response["errorcode"] = 0;
    $response["message"] = "success";
    $response["user"] = $user;

    echo json_encode($response);
} else {
    http_response_code(200);

    $response = array();
    $response["status"] = "error";
    $response["errorcode"] = 1;
    $response["message"] = "User not found";

    echo json_encode($response);
}

?>