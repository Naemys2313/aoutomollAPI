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
$user = new User($db, $database);
$car = new Car($db);
$car_model = new CarModel($db);
$car_mark = new CarMark($db);
$temp_phone_code = new TempPhoneCode($db);

$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method == "GET") {
    $user->id = isset($_GET['id']) ? $_GET['id'] : die();
    $user->readOne();

    if($user->name != null) {
        $car->user_id = $user->id;
        $stmt = $car->findCarsByUserId();
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
                $car_model->readOneById();

                $car_mark->id = $mark_id;
                $car_mark->readOneById();

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

        http_response_code(200);
        echo json_encode(getResponseWithUser("ok", 0, "success", $user_item), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(200);

        echo json_encode(getResponse("error", 1, "user banned"), JSON_UNESCAPED_UNICODE); 
    }
} else if($request_method = "PUT") {
    if(isset($_GET['smsCode']) && isset($_GET['userPhone'])) {
        $sms_code = htmlspecialchars(strip_tags($_GET['smsCode']));
        $user_phone = htmlspecialchars(strip_tags($_GET['userPhone']));

        $temp_phone_code->code = $sms_code;
        $temp_phone_code->phone = $user_phone;
        $temp_phone_code->findByCode();

        if($temp_phone_code->id != null) {
            http_response_code(200);
            if($temp_phone_code->isActiveCode()) {
                $user->phone = $user_phone;
                $user->readByPhone();

                if($user->id == null) {
                    //TODO Удалить строки name, state, city
                    $user->name = "undefined";
                    $user->phone = $user->phone;
                    $user->birthday = date("Y-m-d");
                    $user->state = "undefined";
                    $user->city = "undefined";
                    if(!$user->create()) {
                        echo json_encode(getResponse("error", 1, "User not create"));
                    } else {
                        $user->readByPhone();
                    }
                }

                $user_item = array(
                    "id" => $user->id,
                    "name" => $user->name,
                    "phone" => $user->phone,
                    "birthday" => $user->birthday,
                    "state" => $user->state,
                    "city" => $user->city
                );

                echo json_encode(getResponseWithUser("ok", 0, "success", $user_item));                 
            } else {
                echo json_encode(getResponse("error", 0, "code is not active"));
            }

            $temp_phone_code->delete();
        } else {
            echo json_encode(getResponse("error", 1, "sms code not valid"));
        }
    } else if(isset($_GET['userPhone'])) {
        $user_phone = $_GET['userPhone'];
        $response_user_phone = json_decode(file_get_contents("https://smscentre.com/sys/send.php?login=naemys&psw=8738910001v&phones=".$user_phone."&mes=code&call=1&fmt=3"));

        if(!empty($response_user_phone->code)) {
            $temp_phone_code->phone = $_GET['userPhone'];
            $temp_phone_code->findByCode();
            if($temp_phone_code->id != null) {
                if (!$temp_phone_code->delete()) {
                    echo json_encode(getResponse("error", 1, "error for delete temp code"));
                    return;
                }
            }

            $temp_phone_code->phone = $user_phone;
            $temp_phone_code->code = $response_user_phone->code;
            if(!$temp_phone_code->create()) {
                echo json_encode(getResponse("error", 1, "error for creating temp_phone_code"));
                return;
            }

            echo json_encode(getResponse("ok", 0, "success"), JSON_UNESCAPED_UNICODE);
        } elseif($response_user_phone->error_code == 7) {
            echo json_encode(getResponse("error", 7, "invalud number"));
        } elseif($response_user_phone->error_code == 9) {
            echo json_encode(getResponse("error", 9, "duplicate request, wait a minute"));
        } else {
            echo json_encode(getResponse("error", 1, "unknown error"));
        }
    } else if(isset($_GET['user_id'])) {
        $data = json_decode(file_get_contents("php://input"));

        $user->id = $_GET['user_id'];
        $user->name = $data->name;
        $user->phone = $data->phone;
        $user->birthday = $data->birthday;
        $user->state = $data->state;
        $user->city = $data->city;

        if($user->update()) {
            echo json_encode(getResponse("ok", 0, "success"));
        } else {
            echo json_encode(getResponse("error", 1, "user banned"));
        }
    } else {
        echo json_encode(getResponse("error", 1, "data not filled"));
    }
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

?>