<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db, $database);

$search_id = isset($_GET['id']) ? $_GET['id'] : null;
$search_phone = isset($_GET['phone']) ? $_GET['phone'] : null;
$limit = isset($_GET['limit']) ? $_GET['limit'] : null;

$selection = "";
$selection_args = array();

if($search_id == null && $search_phone == null) {
    $selection = null;
    $selection_args = null;
} else {
    if($search_id != null) {
        $selection = "id=?";
        array_push($selection_args, htmlspecialchars(strip_tags($search_id)));
    } 

    if($search_phone != null) {
        if(!empty($selection)) {
            $selection = $selection ." AND ";
        }
        $selection = $selection."phone LIKE ?";
        array_push($selection_args, htmlspecialchars(strip_tags("".$search_phone."%")));
    }
}

$stmt = $user->read(null, $selection, $selection_args, $limit);
$num = $stmt->rowCount();

if($num > 0) {
    $users_arr = array();
    $users_arr["users"] = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $user_item = array(
            "id" => $id,
            "name" => $name,
            "phone" => $phone,
            "birthday" => $birthday,
            "state" => $state,
            "city" => $city
        );
        array_push($users_arr["users"], $user_item);
    }

    $users_arr["status"] = "success";
    $users_arr["errorcode"] = 0;
    $users_arr["message"] = "ok";

    http_response_code(200);

    echo json_encode($users_arr);
} else {
    http_response_code(200);

    $response = array();
    $response["status"] = "error";
    $response["errorcode"] = 1;
    $response["message"] = "Users are not found.";

    echo json_encode($response);
}
?>