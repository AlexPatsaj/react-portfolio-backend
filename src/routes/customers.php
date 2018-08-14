<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


//Get All Customers
$app->get('/api/customers', function(Request $req, Response $res){
    $sql = "SELECT * FROM customers";

    try{
        $db = new db();

        $db = $db->connect();

        $stmt = $db->query($sql);

        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db=null;
        echo json_encode($customers);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});

//Get Single Customer
$app->get('/api/customer/{id}', function(Request $req, Response $res){

    $id = $req->getAttribute('id');

    $sql = "SELECT * FROM customers WHERE id = $id";

    try{
        $db = new db();

        $db = $db->connect();

        $stmt = $db->query($sql);

        $customer = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db=null;
        echo json_encode($customer);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});

// Add Visitor's Info
$app->post('/ip/details', function(Request $req, Response $res){

    $ip = $req->getParam('ip');
    $city = $req->getParam('city');
    $region = $req->getParam('region');
    $country = $req->getParam('country_name');
    $continent = $req->getParam('continent_name');
    $flag = $req->getParam('flag');

    $dt = new DateTime("now", new DateTimeZone('Asia/Singapore'));
    $date = $dt->format("Y/m/d");
    $time = $dt->format("H:i:s");
    $visitortimezone = $req->getParam('time_zone');
    // $visitortimezone = json_decode($visitortimezone, true);
    $visitortime = $visitortimezone['current_time'];

    $sql = "INSERT INTO list (ip, city, region, country, continent, flag, date, time, visitortime) 
    VALUES(:ip, :city, :region, :country, :continent, :flag, :date, :time, :visitortime)";

    try{
        $db = new db();

        $db = $db->connect();

       $stmt = $db->prepare($sql);

       $stmt->bindParam(':ip', $ip);
       $stmt->bindParam(':city', $city);
       $stmt->bindParam(':region', $region);
       $stmt->bindParam(':country', $country);
       $stmt->bindParam(':continent', $continent);
       $stmt->bindParam(':flag', $flag);
       $stmt->bindParam(':date', $date);
       $stmt->bindParam(':time', $time);
       $stmt->bindParam(':visitortime', $visitortime);

       $stmt->execute();

       echo '{"notice": {"text": "successfully added"}';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});


// Confirm administrator
$app->post('/ip/login', function(Request $req, Response $res){

    $username = $req->getParam('username');
    $password = $req->getParam('password');

    $sql = "SELECT * FROM administrator WHERE username = '$username'";

    try{
        $db = new db();

        $db = $db->connect();

        $stmt = $db->query($sql);

        $user = $stmt->fetchAll();
        $db=null;

        if(sizeof($user) == 0){
            echo '{"error": {"text": "There is no matched user."}}';
            // echo '{"error": {"text": '.$username.'}}';
        } else {
            if($user[0]['password'] != $password){
                echo '{"error": {"text": "Wrong password."}}';
            } else{
                echo '{"status": "true"}';
            }
        }

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});

// Confirm token and return visitors info
$app->post('/ip/lists', function(Request $req, Response $res){

    $token = $req->getParam('token');

    if($token == "wdaf12345678"){
        $sql = "SELECT * FROM list";

        try{
            $db = new db();
    
            $db = $db->connect();
    
            $stmt = $db->query($sql);
    
            $list = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db=null;
            echo json_encode($list);
    
        } catch(PDOException $e){
            echo '{"error": {"text": "token error"}}';
        }
    } else{

    }
});

// Update customer
$app->post('/api/customer/update/{id}', function(Request $req, Response $res){


    $id = $req->getAttribute('id');

    $first_name = $req->getParam('first_name');
    $last_name = $req->getParam('last_name');
    $phone = $req->getParam('phone');
    $email = $req->getParam('email');
    $address = $req->getParam('address');
    $city = $req->getParam('city');
    $state = $req->getParam('state');

    $sql = "UPDATE customers SET 
        first_name = :first_name, 
        last_name = :last_name, 
        phone = :phone, 
        email = :email, 
        address = :address, 
        city = :city, 
        state = :state
     WHERE id=$id";

    try{
        $db = new db();

        $db = $db->connect();

       $stmt = $db->prepare($sql);

       $stmt->bindParam(':first_name', $first_name);
       $stmt->bindParam(':last_name', $last_name);
       $stmt->bindParam(':phone', $phone);
       $stmt->bindParam(':email', $email);
       $stmt->bindParam(':address', $address);
       $stmt->bindParam(':city', $city);
       $stmt->bindParam(':state', $state);

       $stmt->execute();

       echo '{"notice": {"text": "customer updated"}';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});

//Delete Single Customer
$app->delete('/api/customer/delete/{id}', function(Request $req, Response $res){

    $id = $req->getAttribute('id');

    $sql = "DELETE FROM customers WHERE id = $id";

    try{
        $db = new db();

        $db = $db->connect();

        $stmt = $db->query($sql);

        $db=null;
        
        echo '{"notice": {"text": "customer deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});