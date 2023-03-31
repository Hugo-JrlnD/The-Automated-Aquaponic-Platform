<?php
require 'database.php'; // Require the database.php file, which contains the Database class used to connect to the database

// Check if POST data is not empty
if (!empty($_POST)) {

    // Retrieve the POST values and store them in variables
    $id = $_POST['id'];
    $water_level = $_POST['water_level'];
    $Status_WLS = $_POST['Status_WLS'];
    
    date_default_timezone_set("Europe/Paris"); // Set the timezone to Europe/Paris
    $tm = date("H:i:s");     				   // Get the current time
    $dt = date("Y-m-d");     				   // Get the current date 
    
    $pdo = Database::connect();  	  		   // Connect to the database
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set the error mode to throw exceptions
    
    // Update the water_level_temp table with the new values
    $sql = "UPDATE water_level_temp SET water_level = ?, Status_WLS = ?, time = ?, date = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($water_level,$Status_WLS,$tm,$dt,$id));
    Database::disconnect();

    // Generate a unique ID for the new row to be inserted into the table
    $id_key;
    $found_empty = false;
    
    $pdo = Database::connect(); // Connect to the database again.
    
    // Loop until a unique ID is found
    while ($found_empty == false) {
        $id_key = generate_string_id(10); // Generate a random string of characters and numbers, with a length of 10 characters
        
        // Check if the generated ID is already in use in the table
        $sql = 'SELECT * FROM water_level_temp WHERE id="' . $id_key . '"';
        $q = $pdo->prepare($sql);
        $q->execute();
      
        // If no data is found with the generated ID, set found_empty to true to exit the loop
        if (!$data = $q->fetch()) {
            $found_empty = true;
        }
    }

    // Insert the new row into the water_level_temp table with the generated ID and the other values
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO water_level_temp (id,water_level,Status_WLS,time,date) values(?, ?, ?, ?, ?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($id_key,$water_level,$Status_WLS,$tm,$dt));
    Database::disconnect();
}

// Function to generate a random string of characters and numbers
function generate_string_id($strength = 16) {
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($permitted_chars);
    $random_string = '';
    
    for($i = 0; $i < $strength; $i++) {
        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}
?>