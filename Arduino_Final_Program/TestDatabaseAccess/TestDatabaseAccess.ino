#include <WiFi.h>
#include <HTTPClient.h>
#include <Arduino_JSON.h>

//======================================== SSID and Password of your WiFi router.
const char* ssid = "PoleDeVinci_DVIC";
const char* password = "8PfURsp!dvic";
//const char* ssid = "Livebox-070E";
//const char* password = "y5hpyP5Sc2CpDmiY4q"; 

//======================================== Variables for HTTP POST request data.
String postData = ""; //--> Variables sent for HTTP POST request data.
String payload = "";  //--> Variable for receiving response from HTTP POST. 

//======================================== Variables for Water Level Sensor (WLS) data.
#define sensorPower 32
#define sensorPin 35
int water_level;
String Status_WLS = "";

//======================================== Variables to send messages on Discord
#define SECRET_WEBHOOK "https://discordapp.com/api/webhooks/1089920652741443714/PN5CWmoUTowBg8WyO8c4Sy236Ll9oJXVpHhz6lRDuj3vpyiEK3kqbDYQXftkcXeEa_Jh"
#define SECRET_TTS "false"
const String discord_webhook = SECRET_WEBHOOK;
const String discord_tts = SECRET_TTS;

//openssl s_client -showcerts -connect discordapp.com:443 (get last certificate)
const char* discordappCertificate = \
                                    "-----BEGIN CERTIFICATE-----\n"
                                    "MIIDzTCCArWgAwIBAgIQCjeHZF5ftIwiTv0b7RQMPDANBgkqhkiG9w0BAQsFADBa\n"
                                    "MQswCQYDVQQGEwJJRTESMBAGA1UEChMJQmFsdGltb3JlMRMwEQYDVQQLEwpDeWJl\n"
                                    "clRydXN0MSIwIAYDVQQDExlCYWx0aW1vcmUgQ3liZXJUcnVzdCBSb290MB4XDTIw\n"
                                    "MDEyNzEyNDgwOFoXDTI0MTIzMTIzNTk1OVowSjELMAkGA1UEBhMCVVMxGTAXBgNV\n"
                                    "BAoTEENsb3VkZmxhcmUsIEluYy4xIDAeBgNVBAMTF0Nsb3VkZmxhcmUgSW5jIEVD\n"
                                    "QyBDQS0zMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEua1NZpkUC0bsH4HRKlAe\n"
                                    "nQMVLzQSfS2WuIg4m4Vfj7+7Te9hRsTJc9QkT+DuHM5ss1FxL2ruTAUJd9NyYqSb\n"
                                    "16OCAWgwggFkMB0GA1UdDgQWBBSlzjfq67B1DpRniLRF+tkkEIeWHzAfBgNVHSME\n"
                                    "GDAWgBTlnVkwgkdYzKz6CFQ2hns6tQRN8DAOBgNVHQ8BAf8EBAMCAYYwHQYDVR0l\n"
                                    "BBYwFAYIKwYBBQUHAwEGCCsGAQUFBwMCMBIGA1UdEwEB/wQIMAYBAf8CAQAwNAYI\n"
                                    "KwYBBQUHAQEEKDAmMCQGCCsGAQUFBzABhhhodHRwOi8vb2NzcC5kaWdpY2VydC5j\n"
                                    "b20wOgYDVR0fBDMwMTAvoC2gK4YpaHR0cDovL2NybDMuZGlnaWNlcnQuY29tL09t\n"
                                    "bmlyb290MjAyNS5jcmwwbQYDVR0gBGYwZDA3BglghkgBhv1sAQEwKjAoBggrBgEF\n"
                                    "BQcCARYcaHR0cHM6Ly93d3cuZGlnaWNlcnQuY29tL0NQUzALBglghkgBhv1sAQIw\n"
                                    "CAYGZ4EMAQIBMAgGBmeBDAECAjAIBgZngQwBAgMwDQYJKoZIhvcNAQELBQADggEB\n"
                                    "AAUkHd0bsCrrmNaF4zlNXmtXnYJX/OvoMaJXkGUFvhZEOFp3ArnPEELG4ZKk40Un\n"
                                    "+ABHLGioVplTVI+tnkDB0A+21w0LOEhsUCxJkAZbZB2LzEgwLt4I4ptJIsCSDBFe\n"
                                    "lpKU1fwg3FZs5ZKTv3ocwDfjhUkV+ivhdDkYD7fa86JXWGBPzI6UAPxGezQxPk1H\n"
                                    "goE6y/SJXQ7vTQ1unBuCJN0yJV0ReFEQPaA1IwQvZW+cwdFD19Ae8zFnWSfda9J1\n"
                                    "CZMRJCQUzym+5iPDuI9yP+kHyCREU3qzuWFloUwOxkgAyXVjBYdwRVKD05WdRerw\n"
                                    "6DEdfgkfCv4+3ao8XnTSrLE=\n"
                                    "-----END CERTIFICATE-----\n";

void setup() 
{
  Serial.begin(115200); //--> Initialize serial communications with the PC.


  pinMode(sensorPower, OUTPUT);
	digitalWrite(sensorPower, LOW);   // Set to LOW so no power flows through the sensor

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.println("\nConnecting");
  while(WiFi.status() != WL_CONNECTED)
  {
    Serial.print(".");
    delay(100);
  }
  Serial.println("\nConnected to the WiFi network");
  Serial.print("Local ESP32 IP: ");
  Serial.println(WiFi.localIP());

  delay(2000);
} 

void loop()
{
  //---------------------------------------- Check WiFi connection status.
  if(WiFi.status()== WL_CONNECTED) {
    HTTPClient http;  //--> Declare object of class HTTPClient.
    int httpCode;     //--> Variables for HTTP return code.
    
    // Calls the get_DHT11_sensor_data() subroutine.
    get_WLS_data();
  
    //........................................ The process of sending the WLS data to the database.
    postData = "id=esp32_01";
    postData += "&water_level=" + String(water_level);
    postData += "&Status_WLS=" + Status_WLS;
    
    payload = "";
  
    Serial.println();
    Serial.println("---------------update WLS data.php");
    // ESP32 accesses the data bases at this line of code: 
    http.begin("http://172.21.72.105/HP_AQUAPONIE_HUGO/Test/update_sensor.php");  //--> Specify request destination
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");  //--> Specify content-type header
   
    httpCode = http.POST(postData); //--> Send the request
    payload = http.getString();  //--> Get the response payload
  
    Serial.print("httpCode : ");
    Serial.println(httpCode); //--> Print HTTP return code
    Serial.print("payload  : ");
    Serial.println(payload);  //--> Print request response payload
    
    http.end();  //Close connection
    Serial.println("---------------");

    if(water_level < 200)
    {
      sendDiscord("The water level is very low. There may be a problem with the system. Please check and repair it !");
    }
    
    delay(10000);
  }
}

void get_WLS_data() 
{
  Serial.println();
  Serial.println("-------------get_WLS_data()");
  
  digitalWrite(sensorPower, HIGH);	// Turn the sensor ON
	delay(10);							// wait 10 milliseconds
	water_level = analogRead(sensorPin);		// Read the analog value form sensor
	digitalWrite(sensorPower, LOW);		// Turn the sensor OFF

  // Check if any reads failed.
  if (isnan(water_level)) {
    Serial.println("Failed to read from water level sensor!");
    water_level = 0;
    Status_WLS = "FAILED";
  } else {
    Status_WLS = "SUCCEED";
  }
  
  Serial.print("water level = ");
  Serial.println(water_level);
  
  //Serial.printf("Water level : %.2f \n", water_level);
  Serial.printf("Status Read WLS Sensor : %s\n", Status_WLS);
  Serial.println("-------------");
}

void sendDiscord(String content) 
{
  WiFiClientSecure *client = new WiFiClientSecure;
  if (client) {
    client -> setCACert(discordappCertificate);
    {
      HTTPClient https;
      Serial.println("[HTTP] Connecting to Discord...");
      Serial.println("[HTTP] Message: " + content);
      Serial.println("[HTTP] TTS: " + discord_tts);
      if (https.begin(*client, discord_webhook)) {  // HTTPS
        // start connection and send HTTP header
        https.addHeader("Content-Type", "application/json");
        int httpCode = https.POST("{\"content\":\"" + content + "\",\"tts\":" + discord_tts +"}");

        // httpCode will be negative on error
        if (httpCode > 0) {
          // HTTP header has been send and Server response header has been handled
          Serial.print("[HTTP] Status code: ");
          Serial.println(httpCode);

          // file found at server
          if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
            String payload = https.getString();
            Serial.print("[HTTP] Response: ");
            Serial.println(payload);
          }
        } else {
          Serial.print("[HTTP] Post... failed, error: ");
          Serial.println(https.errorToString(httpCode).c_str());
        }

        https.end();
      } else {
        Serial.printf("[HTTP] Unable to connect\n");
      }
    }

    delete client;
  } else {
    Serial.println("[HTTP] Unable to create client");
  }
}