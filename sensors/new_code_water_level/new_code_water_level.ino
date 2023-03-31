// Sensor pins
#define sensorPower 32
#define sensorPin 35

int val = 0; // Value for storing water level

void setup() 
{
	pinMode(sensorPower, OUTPUT); 	// Set D7 as an OUTPUT
	digitalWrite(sensorPower, LOW); // Set to LOW so no power flows through the sensor
	
	Serial.begin(115200);
}

void loop() 
{
	int level = readSensor();  	   // Get the reading from the function below and print it
	
	Serial.print("Water level: ");
	Serial.println(level);
	
	delay(4000);
}

//This is a function used to get the reading
int readSensor() 
{
	digitalWrite(sensorPower, HIGH);	// Turn the sensor ON
	delay(10);						
	val = analogRead(sensorPin);	    // Read the analog value form sensor
	digitalWrite(sensorPower, LOW);		// Turn the sensor OFF
	return val;							          // send current reading
}