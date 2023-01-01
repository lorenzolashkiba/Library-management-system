#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <WiFiClient.h>

const uint16_t port = 8888;
const char *host = "computer.local";
WiFiClient client;
int myBooks[] = {2345678,456789,456789,56789,67890,7890,890}
void setup()
{
    Serial.begin(115200);
    Serial.println("Connecting...\n");
    WiFi.mode(WIFI_STA);
    WiFi.begin("USSID", "PASSWORD"); // change it to your ussid and password
    while (WiFi.status() != WL_CONNECTED)
    {
        delay(500);
        Serial.print(".");
    }
}

void loop()
{
    if (!client.connect(host, port))
    {
        Serial.println("Connection to host failed");
        delay(1000);
        return;
    }
    Serial.println("Connected to server successful!");
    client.println("Hello From ESP8266");
    delay(250);
    while (client.available() > 0)
    {
        char c = client.read();
        int num (int) c;
        
        Serial.write(c);
    }
    Serial.print('\n');
    client.stop();
    delay(5000);
}