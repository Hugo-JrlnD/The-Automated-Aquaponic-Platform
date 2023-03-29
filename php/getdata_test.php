<?php
  // Include an external file called "database.php", which presumably contains a database connection class
  include 'database.php';
  
  // Check if the $_POST superglobal variable is not empty
  if (!empty($_POST)) {
    // Keep track of the "id" value from the $_POST array
    $id = $_POST['id'];
    
    // Create an empty object
    $myObj = (object)array();
    
    // Create a new database connection using the "Database" class
    $pdo = Database::connect();

    // Define a SQL query to retrieve a single record from the "water_level_temp" table
    $sql = 'SELECT * FROM water_level_temp WHERE id="' . $id . '"';
    
    // Loop through the results of the SQL query
    foreach ($pdo->query($sql) as $row) {
      // Convert the "date" column to a DateTime object and format it as "d-m-Y"
      $date = date_create($row['date']);
      $dateFormat = date_format($date,"d-m-Y");
      
      // Set the properties of the object based on the values from the database
      $myObj->id = $row['id'];
      $myObj->water_level = $row['water_level'];
      $myObj->Status_WLS = $row['Status_WLS'];
      $myObj->ls_time = $row['time'];
      $myObj->ls_date = $dateFormat;
      $myObj->ls_date = $dateFormat;
      
      // Convert the object to a JSON string
      $myJSON = json_encode($myObj);
      
      // Output the JSON string to the browser
      echo $myJSON;
    }
    
    // Close the database connection
    Database::disconnect();
  }
?>