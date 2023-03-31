# include <OneWire.h>                        
#include <DallasTemperature.h>              

#define WaterTempPin 34                       // Defining the GPIO pin number to which the DS18B20 temperature sensor is connected

OneWire oneWire(WaterTempPin);                // Creating an instance of the OneWire library to communicate with the temperature sensor
DallasTemperature WaterTempsSensor(&oneWire); // Creating an instance of the DallasTemperature library to read data from the temperature sensor

void setup(void)
{
  Serial.begin(115200);                      // Initializing the serial communication at a baud rate of 115200

  WaterTempsSensor.begin();                  // Initializing the DallasTemperature library to start reading temperature data
}

void loop(void)
{
  WaterTempsSensor.requestTemperatures();    // Requesting temperature readings from the sensor
  float temperatureC = WaterTempsSensor.getTempCByIndex(0);  // Getting the temperature reading in Celsius by index
  Serial.print(temperatureC);                // Printing the temperature reading to the serial monitor
  Serial.print("ºC - ");                     // Adding a degree Celsius symbol to the output

  delay(1000);                               // Adding a delay of 1 second before reading the temperature again
}
