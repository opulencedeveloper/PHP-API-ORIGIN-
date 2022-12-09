<?php

//header controls what response sent to the client
header('Access-Control-Allow-Origin: *'); //allows all origin to send request, eg www.test.com is a origin, * means allow any domain,, header('Access-Control-Allow-Origin: victor.com');,,,, here only victor.com can send a request
header('Content-type: application/json'); //this means the format will be json
header('Access-Control-Allow-Method: POST');//this defines the type of request allowed here
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept'); //this is used to handle preflight request{a smal request sent before the actual request}

include_once '../../models/Seller.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if($seller->validate_params($_POST['name'])) {
        $seller->name = $_POST['name'];
        } else {
            echo json_encode(array('success' => 0, 'message' => 'Name is required!'));
            die();
                    }

    if($seller->validate_params($_POST['email'])) {
        $seller->email = $_POST['email'];
        } else {
            echo json_encode(array('success' => 0, 'message' => 'Email is required!'));
            die();
                }

    if($seller->validate_params($_POST['password'])) {
        $seller->password = $_POST['password'];
        } else {
            echo json_encode(array('success' => 0, 'message' => 'Password is required!'));
            die();
                    }

    $seller_images_folder = '../../assets/seller_images/';

    if(!is_dir($seller_images_folder)){
        mkdir($seller_images_folder);
        }

    if(isset($_FILES['image'])){  //$_FILES stores all the files sent in a request
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $extension = end(explode('.', $file_name)); //explode divded the string when it see a dot into an array and end() gets the last string in the array

        $new_file_name = $seller->email . "_profile" . "." . $extension; //email was used for the file name bcus it is unique

        move_uploaded_file($file_tmp, $seller_images_folder . "/" . $new_file_name); // "/" means that we are going into the folder

        $seller->image = 'seller_images/' . $new_file_name;

            }

    if($seller->validate_params($_POST['address'])) {
        $seller->address = $_POST['address'];
        } else {
        echo json_encode(array('success' => 0, 'message' => 'Address is required!'));
        die();
                }

    if($seller->validate_params($_POST['description'])) {
        $seller->description = $_POST['description'];
        } else {
            echo json_encode(array('success' => 0, 'message' => 'Description is required!'));
            die();
                    }

    if($seller->check_unique_email()) {

    if($id = $seller->register_seller()) {
    echo json_encode(array('success' => 1, 'message' => 'Seller Registered'));
    } else {
        http_response_code(500);
        echo json_encode(array('success' => 0, 'message' => 'Internal Server Error'));
            }
        } else {
            http_response_code(401);
            echo json_encode(array('success' => 0, 'message' => 'Email Exists')); 
        }
} else {
    die(header('HTTP/1.1 405 Request Method Not Allowed'));
}