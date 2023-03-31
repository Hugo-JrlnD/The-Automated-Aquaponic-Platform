<?php
	// Define a class called "Database"
	class Database {
		// Private static properties to store database information
		private static $dbName = 'aquaponie_hp_hugo'; // database name
		private static $dbHost = 'localhost'; 		  // database host
		private static $dbUsername = 'root';          // database username
		private static $dbUserPassword = '';          // database password
		 
		private static $cont  = null; // Private static property to store database connection
		 
		// Constructor method to prevent instantiation of class
		public function __construct() {
			die('Init function is not allowed');
		}
		
		// Public static method to connect to database
		public static function connect() {
			if ( null == self::$cont ) {     
				try {
					// Create new PDO object to connect to database
					self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword); 
				} catch(PDOException $e) {
					// If connection fails, print error message and stop script execution
					die($e->getMessage()); 
				}
			}
			return self::$cont; // Return database connection
		}
		 
		// Public static method to disconnect from database
		public static function disconnect() {
			self::$cont = null; // Set the database connection property to null
		}
	}
?>