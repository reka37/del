<?php

require_once 'userApi.php';
try {  
   $api = new usersApi();
    echo $api->run(); 
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}