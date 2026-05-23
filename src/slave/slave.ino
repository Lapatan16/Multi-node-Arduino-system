const int localAnalogPin = A0;

void setup() {
  Serial1.begin(9600); 
}

void loop() {
  int rawValue = analogRead(localAnalogPin);
  float voltage = rawValue * (3.3 / 1023.0);
  
  Serial1.println(voltage, 2); 
  
  delay(200);
}