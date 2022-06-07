#include <ESP8266WiFi.h>        // Include the Wi-Fi library
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <SoftwareSerial.h>

int buttonPin = D2;
int buzzer = D4;

const char* ssid     = "Nicore";         // The SSID (name) of the Wi-Fi network you want to connect to 
const char* password = "12345678";     // The password of the Wi-Fi network

const char* post_link = "http://192.168.235.38:80/Nicore/server.php";   //la stessa SUBNET
const char* serverName = "http://192.168.235.38:80/Nicore/server.php?type=get_threshold";

WiFiClient client;

SoftwareSerial mySerial(D5,D6); //RX e TX

int bpm = 0;
String threshold = "";

void setup() {
  Serial.begin(115200);         // Start the Serial communication to send messages to the computer
  pinMode(buttonPin, INPUT_PULLUP);
  pinMode(buzzer,OUTPUT);
  delay(10);
  Serial.println('\n');
  mySerial.begin(115200);
  
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

  threshold = get_from_server(serverName);
}


void loop() { 

  if ( mySerial.available() > 0 ) {
    bpm = mySerial.read();
    Serial.print("BPM : ");
    Serial.println(bpm);
    send_to_server("value="+ String(bpm));
    if( bpm>threshold.toInt()){
      tone(buzzer,500,500);
      delay(300);
    } 
  }

  if ( digitalRead(buttonPin) == LOW ){
    threshold = get_from_server(serverName);
  }
  noTone(buzzer);
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
    //Serial.println(postData);
  }

  else
  {
    Serial.println("Error in WiFi connection");
  }
}

String get_from_server(const char* serverName){
  String threshold;
  if(WiFi.status()==WL_CONNECTED){
    threshold = httpGETRequest(serverName);
    Serial.println("Threshold : "+threshold);
  } else {
    Serial.println("Error in wifi connection");
    }
    delay(500);
    return threshold;
}

String httpGETRequest(const char* serverName){
  WiFiClient client;
  HTTPClient http;

  http.begin(client,serverName);

  int httpResponseCode = http.GET();

  String payload = "";

  if ( httpResponseCode>0){
    //Serial.print("HTTP Responde code: ");
    //Serial.println(httpResponseCode);
    payload = http.getString();
  } else {
    Serial.print("Error code: ");
    Serial.println(httpResponseCode); 
    }
    http.end();
    return payload;
}
