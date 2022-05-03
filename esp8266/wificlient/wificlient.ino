#include <ESP8266WiFi.h>        // Include the Wi-Fi library
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>

const char* ssid     = "Nicore";         // The SSID (name) of the Wi-Fi network you want to connect to 
const char* password = "12345678";     // The password of the Wi-Fi network

const char* post_link = "http://192.168.118.38:80/Nicore/server.php";   //la stessa SUBNET

WiFiClient client;

void setup() {
  Serial.begin(115200);         // Start the Serial communication to send messages to the computer
  delay(10);
  Serial.println('\n');
  
  WiFi.begin(ssid, password);             // Connect to the network
  Serial.print("Connecting to ");
  Serial.print(ssid); Serial.println(" ...");

  int i = 0;
  while (WiFi.status() != WL_CONNECTED) { // Wait for the Wi-Fi to connect
    delay(1000);
    Serial.print(++i); Serial.print(' ');
  }

  Serial.println('\n');
  Serial.println("Connection established!");  
  Serial.print("IP address:\t");
  Serial.println(WiFi.localIP());         // Send the IP address of the ESP8266 to the computer

  send_to_server("value=80");
}

void send_to_server(String postData) {
  HTTPClient http;    //Declare object of class HTTPClient
  if (WiFi.status() == WL_CONNECTED)
  { //Check WiFi connection status
    // Send and get Data
    http.begin(client , post_link);              //Specify request destination
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Specify content-type header
    int httpCode = http.POST(postData);   //Send the request
    //Serial.println(httpCode);   //Print HTTP return code
    http.end();  //Close connection
    Serial.println(postData);
  }

  else
  {
    Serial.println("Error in WiFi connection");
  }

}

void loop() { 

  //qui andrebbe la vera post coi bpm inviati da arduino
}