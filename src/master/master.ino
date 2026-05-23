#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>

const char *ssid = "Arduino_Master_Net";
const char *password = "123456789";

ESP8266WebServer server(80);

float espVoltage = 0.0;
float teensyVoltage = 0.0;
char cStringBuffer[32];
int bufferIndex = 0;

String rawDebugLog = "No data received yet";
unsigned long packetCount = 0;

unsigned long lastAnalogReadTime = 0;

void handleGetData() {
  String jsonResponse = "{";
  jsonResponse += "\"esp_voltage\":" + String(espVoltage, 2) + ","; 
  jsonResponse += "\"teensy_voltage\":" + String(teensyVoltage, 2) + ",";
  jsonResponse += "\"raw_serial_catch\":\"" + rawDebugLog + "\",";
  jsonResponse += "\"packets_received\":" + String(packetCount);
  jsonResponse += "}";
  
  server.sendHeader("Access-Control-Allow-Origin", "*");
  server.send(200, "application/json", jsonResponse);
}

void setup() {
  Serial.begin(9600);
  delay(500);

  WiFi.mode(WIFI_AP);
  WiFi.softAP(ssid, password, 3, 0); 
  
  server.on("/api/data", HTTP_GET, handleGetData);
  server.begin();
}

void loop() {
  server.handleClient();
  
  if (millis() - lastAnalogReadTime >= 200) {
    int espRaw = analogRead(A0);
    espVoltage = espRaw * (3.3 / 1023.0);
    lastAnalogReadTime = millis();
  }
  
  while (Serial.available() > 0) {
    char c = Serial.read();
    
    if (rawDebugLog == "No data received yet" || rawDebugLog.length() > 60) {
      rawDebugLog = "";
    }
    
    if (c == '\n') rawDebugLog += "[\\n]";
    else if (c == '\r') rawDebugLog += "[\\r]";
    else rawDebugLog += c;

    if (c == '\n') {
      if (bufferIndex > 0) {
        cStringBuffer[bufferIndex] = '\0'; 
        teensyVoltage = atof(cStringBuffer); 
        bufferIndex = 0; 
        packetCount++; 
      }
    } else if (bufferIndex < 31) {
      if ((c >= '0' && c <= '9') || c == '.' || c == '-') {
        cStringBuffer[bufferIndex++] = c;
      }
    }
  }
  
  yield(); 
}