<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/database.php';
include_once 'objects/user.php';
include_once 'objects/car.php';
include_once 'objects/car_model.php';
include_once 'objects/car_mark.php';
include_once 'objects/temp_phone_code.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($database);
$car = new Car($database);
$car_model = new CarModel($database);
$car_mark = new CarMark($database);
$temp_phone_code = new TempPhoneCode($database);

$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method == "GET") {
    $id= isset($_GET['id']) ? $_GET['id'] : die();
    $user_item = getUser($id);

    http_response_code(200);
    if($user_item != null) {
        printJsonResponse(getResponseWithUser("ok", 0, "success", $user_item));
    } else {
        printJsonResponse(getResponse("error", 1, "user banned"));
    }
} else if($request_method = "PUT") {
    if(isset($_GET['code']) && isset($_GET['phone'])) {
        $code = htmlspecialchars(strip_tags($_GET['code']));
        $phone = htmlspecialchars(strip_tags($_GET['phone']));

        verificationCode($code, $phone);
    } else if(isset($_GET['phone'])) {
        $phone = $_GET['phone'];
        requestCode($phone);
    } else if(isset($_GET['id'])) {
        $id = $_GET['id'];
        updateUser($id);  
    } else {
        printJsonResponse(getResponse("error", 1, "data not filled"));
    }
} else {
    printJsonResponse(getResponse("error", 1, "unknown method."));
}

function getResponse($status, $errorcode, $message) {
    $response = array();
    $response['status'] = $status;
    $response['errorcode'] = $errorcode;
    $response['message'] = $message;
    
    return $response;
}

function getResponseWithUser($status, $errorcode, $message, $user) {
    $response = getResponse($status, $errorcode, $message);
    $response['user'] = $user;
    return $response;
}

function getUser($id) {
    $database = new Database();

    $car = new Car($database);
    $user = new User($database);

    $car_model = new CarModel($database);
    $car_mark = new CarMark($database);

    $user->id = $id;
    $user->readOne();

    if($user->name != null) {
        $car->user_id = $user->id;
        $stmt = $car->readCarsByUserId();
        $num = $stmt->rowCount();

        $user_item = array(
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'birthday' => $user->birthday,
            'state' => $user->state,
            'city' => $user->city,
            'auto' => array()
        );

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $car_model->id = $model_id;
                $car_model->readOne();

                $car_mark->id = $mark_id;
                $car_mark->readOne();

                $car_item = array(
                    'id' => $id,
                    'label' => $label,
                    'vin' => $vin,
                    'mark' => $car_mark->title,
                    'model' => $car_model->title,
                    'year' => $year,
                    'gosnum' => $gosnum,
                    'mileage' => $mileage
                );

                array_push($user_item['auto'], $car_item);
            }
        }
        
        return $user_item;
    }

    return null;
}

function verificationCode($code, $phone) {
    $database = new Database();
    $temp_phone_code = new TempPhoneCode($database);
    $user = new User($database);

    $temp_phone_code->code = $code;
    $temp_phone_code->phone = $phone;

    $temp_phone_code->readByCodeAndPhone();

    if($temp_phone_code->id != null) {
        http_response_code(200);
        if($temp_phone_code->isActiveCode()) {
            $user->phone = $phone;
            $user->readByPhone();

            if($user->id == null) {
                $user->phone = $phone;
                $user->birthday = date("Y-m-d");
                if($user->create()) {
                    $user->readByPhone();
                }
            }

            $user_item = array(
                "id" => $user->id,
                "name" => $user->name,
                "phone" => $user->phone,
                "birthday" => $user->birthday,
                "comment" => $user->comment,
                "state" => $user->state,
                "city" => $user->city
            );

            printJsonResponse(getResponseWithUser("ok", 0, "success", $user_item));
        } else {
            printJsonResponse(getResponse("error", 1, "code is not active"));
        }

        $temp_phone_code->delete();
    } else {
        printJsonResponse(getResponse("error", 1, "sms code not valid"));
    }   
}

function requestCode($phone) {
    $database = new Database();
    $temp_phone_code = new TempPhoneCode($database);
    
    $response_user_phone = json_decode(file_get_contents("https://smscentre.com/sys/send.php?login=naemys&psw=8738910001v&phones=".$phone."&mes=code&call=1&fmt=3"));

    if(!empty($response_user_phone->code)) {
        $temp_phone_code->phone = $phone;

        $selection = "phone=?";
        $selection_args = array();
        array_push($selection_args, $phone);

        $temp_phone_code->readOne(null, $selection, $selection_args);
        if($temp_phone_code->id != null) {
            $temp_phone_code->delete();
        }

        $temp_phone_code->phone = $phone;
        $temp_phone_code->code = $response_user_phone->code;
        $temp_phone_code->create();

        printJsonResponse(getResponse("ok", 0, "success"));
    } elseif($response_user_phone->error_code == 7) {
        printJsonResponse(getResponse("error", 7, "invalud number"));
    } elseif($response_user_phone->error_code == 9) {
        printJsonResponse(getResponse("error", 9, "duplicate request, wait a minute"));
    } else {
        printJsonResponse(getResponse("error", 1, "unknown error"));
    }
}

function updateUser($id) {
    $user = new User(new Database());
    $data = json_decode(file_get_contents("php://input"));

    $user->id = $id;
    $user->name = $data->name;
    $user->phone = $data->phone;
    $user->birthday = $data->birthday;
    $user->state = $data->state;
    $user->city = $data->city;

    if($user->update("id=:id")) {
        echo json_encode(getResponse("ok", 0, "success"));
    } else {
        echo json_encode(getResponse("error", 1, "user banned"));
    }
}

function printJsonResponse($response) {
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?>