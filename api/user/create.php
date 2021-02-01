<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db, $database);

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->name) &&
    !empty($data->phone) &&
    !empty($data->birthday) &&
    !empty($data->state) &&
    !empty($data->city) 
) {
    $user->name = $data->name;
    $user->phone = $data->phone;
    $user->birthday = $data->birthday;
    $user->state = $data->state;
    $user->city = $data->city;

    if($user->create()) {
        http_response_code(200);

        $response = array();
        $response["status"] = "ok";
        $response["errorcode"] = 0;
        $response["message"] = "success";

        echo json_encode($response);
    } else {
        http_response_code(200);

        $response["status"] = "error";
        $response["errorcode"] = 1;
        $response["message"] = "User not created";

        echo json_encode($response);
    }
} else {
    http_response_code(200);

    $response["status"] = "error";
    $response["errorcode"] = 1;
    $response["message"] = "User not created. Data not filled";

    echo json_encode($response);
}
?>