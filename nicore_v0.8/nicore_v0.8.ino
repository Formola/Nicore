#include <SoftwareSerial.h>
SoftwareSerial esp(5,6); //RX e TX

int ky039 = A0;

float valorePrecedente = 0;
float checkBattito = 0;
int num_battiti = 0;  

float fattore_di_filtro = 0.75;
int tempo_min_battiti = 300;  //ms

long tempo_inizio_battiti = millis();
long tempoBPM = millis();

int bpm = 0; 

void setup() {
  Serial.begin(9600);
  esp.begin(115200);
  Serial.println("Ciao, stai utilizzando <3 NICORE <3");
}

void loop() {

  int valoreLetto = analogRead(ky039); 

  float valoreFiltrato =  valorePrecedente*fattore_di_filtro + ( 1 - fattore_di_filtro ) * valoreLetto ; 

  float differenza = valoreFiltrato - valorePrecedente;  

  valorePrecedente = valoreFiltrato ; 

  if ( (differenza >= checkBattito ) && ( millis() > tempo_inizio_battiti + tempo_min_battiti) ){   
    checkBattito = differenza ;  
    tempo_inizio_battiti = millis();  
    num_battiti++;  
  }
  
  checkBattito = checkBattito * 0.97;  

  if ( millis() >= tempoBPM + 15000 ) {  
    bpm = num_battiti*4;

    if ( bpm < 45 ) {
      Serial.println("Riposiziona dito");
    } else {
        Serial.print("BPM : ");
        Serial.println(bpm);  
        esp.write(bpm);
      }
    tempoBPM = millis(); 
    num_battiti = 0;  
  }
  delay(50);
}
