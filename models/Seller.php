<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__). $ds . '..') . $ds;

require_once("{$base_dir}includes{$ds}Database.php"); //including database
require_once("{$base_dir}includes{$ds}Bcrypt.php");

class Seller{
    private $table = 'sellers';

    public $id;
    public $name;
    public $email;
    public $password;
    public $image;
    public $address;
    public $description;

    public function __construct(){}
        
    //validating if params exist or not    
    public function validate_params($value) {
        //if(!empty($value)){
        //    return true;
        //} else{
        //    return false;
        //}

        return(!empty($value));
    }

    //check unique email
    public function check_unique_email()
    {
        global $database;

        $this->email = trim(htmlspecialchars(strip_tags($this->email)));

        $sql = "SELECT id FROM $this->table WHERE email = '" .$database->escape_value($this->email). "'";

        $result = $database->query($sql);
        $user_id = $database->fetch_row($result);

        return empty($user_id);
    }


    //saving data in the database
    public function register_seller(){
        global $database;

        $this->name = trim(htmlspecialchars(strip_tags($this->name)));
        $this->email = trim(htmlspecialchars(strip_tags($this->email)));
        $this->password = trim(htmlspecialchars(strip_tags(Bcrypt::hashPassword($this->password))));
        $this->image = trim(htmlspecialchars(strip_tags($this->image)));
        $this->address = trim(htmlspecialchars(strip_tags($this->address)));
        $this->description = trim(htmlspecialchars(strip_tags($this->description)));

        $sql = "INSERT INTO $this->table (name, email, password, image, address, description) VALUES(
            '" .$database->escape_value($this->name). "',
            '" .$database->escape_value($this->email). "',
            '" .$database->escape_value($this->password). "',
            '" .$database->escape_value($this->image). "',
            '" .$database->escape_value($this->address). "',
            '" .$database->escape_value($this->description). "'
        )";

        $seller_saved = $database->query($sql);

        if($seller_saved) return true;
        else false;
    }

    public function login () {
        global $database; //creating instance of database

        $this->email = trim(htmlspecialchars(strip_tags($this->email)));
        $this->password = trim(htmlspecialchars(strip_tags($this->password)));

        $sql = "SELECT * FROM $this->table WHERE email = '" .$database->escape_value($this->email). "'";

        $result = $database->query($sql);
        $seller = $database->fetch_row($result);

        if (empty($seller)) {
            return "Seller doesn't exist.";
        } else {
            if (Bcrypt::checkPassword($this->password, $seller['password'])) {
                unset($seller['password']);                                                             //unset prevents the api from returning the password, here it removes password from the values in the seller array
                return $seller;
            } else {
                return "Password doesn't match.";
            }
        }
    }

     // method to return the list of seller
     public function all_sellers() {
        global $database;

        $sql = "SELECT id, name, image, address FROM $this->table";

        $result = $database->query($sql);

        return $database->fetch_array($result); //fetchs related colums
    }
}

$seller = new Seller();












