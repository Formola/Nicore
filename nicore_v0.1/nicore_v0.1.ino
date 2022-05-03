/*
    FUNZIONAMENTO MODULO KY-039 
    
    il modulo disponde di un LED IR(*) e di un diodo fotosensibile(**) , oltre ad alcune resistenze di bordo.
    L'uscita del modulo è una tensione analogica che rappresenta la quanitità di luce infrarossa che il fotodiodo riceve.
    Il funzionamento è il seguente : si posiziona il dito tra il led IR e il fotodiodo , i battiti cardiaci dilatano i vasi sanguigni del dito,
    che quindi andranno a filtrare la luce infrarossa emessa dal led IR. Infine la luce infrarossa filtrata viene rilevata
    dal fotodiodo e convertita in una tensione. Per ottenere valori più precisi , bisogna proteggere il sensore da altre fonti di luce,
    altrimenti la luce infrarossa proveniente da tali altre fonti distorcerà il segnale in uscita.
     
    LED IR* = un led a infrarossi è un diodo a emissione di luce infrarossi non visibile ad occhio nudo.
         questo led funziona come un normale led , ma utilizza diversi materiali per generare la luce a infrarossi
         
    FOTODIODO** = è un diodo atto a rilevare della luce utilizzato per la conversione di una certa quantita di luce in tensione
         o corrente a seconda del dispositivo
*/
#include <SoftwareSerial.h>

SoftwareSerial esp8266(5,6);

int ky039 = A0;

float valorePrecedente = 0;
float checkBattito = 0;
int num_battiti = 0;  //numero di battiti che effettivamente misuriamo e che utilizzeremo per calcolare i bpm

float fattore_di_filtro = 0.75;//fattore che dipende dalla trasparenza del vetro del LED IR
int tempo_min_battiti = 300;  //valore minimo di tempo per la misurazione dei battiti in millisecondi

long tempo_inizio_battiti = millis();
long tempoBPM = millis();

void setup() {
  Serial.begin(9600);
  esp8266.begin(9600);
  Serial.println("Ciao, stai utilizzando <3 NICORE <3");
}

void loop() {

  int valoreLetto = analogRead(ky039); //valore analogico tra 0 e 1023 letto dal sensore , di solito è un valore che si aggira tra 500-800
  /*tale valore non rappresenta ancora una misura cardiaca,bensì una quantità che rappresenta la quanità di luce infrarossa ricevuta dal fotodiodo, tuttavia tali valori se plottati
    ci mostrano gia una sorta di andamento cardiaco*/

  float valoreFiltrato =  valorePrecedente*fattore_di_filtro + ( 1 - fattore_di_filtro ) * valoreLetto ; 

  /* in questa riga stiamo calcolando una media dei valori letti, quindi ad ogni 
     valore letto viene aggiornato il calcolo per il valore filtrato effettivo
     che sarà dunque una combinazione ponderata del suo precedente valore e del nuovo valore appena letto.
     tale media cosi calcolata prende il nome di media mobile esponenziale ponderata,
     che sostanzialmente attribuisce maggiore importanza ai valori letti piu recenti
   */

  float differenza = valoreFiltrato - valorePrecedente;  //con differenza capiamo se ci troviamo sul fronte di salita o di discesa del picco di valori letti e quindi di un battito

  /* DEBUG ZONE
    Serial.print("valore letto : ");
    Serial.println(valoreLetto);
    
    Serial.print("valore filtrato : ");
    Serial.println(valoreFiltrato);
    
    Serial.print("differenza : ");
    Serial.println(differenza);
  
    Serial.print("valore checkBattito : ");
    Serial.println(checkBattito);
  
    Serial.print("num battiti : ");
    Serial.println(num_battiti);
  */

  valorePrecedente = valoreFiltrato ; 

  if ( (differenza >= checkBattito ) && ( millis() > tempo_inizio_battiti + tempo_min_battiti) ){   //tramite questo if capiamo se è avvenuto o meno un battito
    checkBattito = differenza ;   // se è avvenuto un battito aggiorna checkBattito all'attuale valore di differenza 
    tempo_inizio_battiti = millis();  //aggiorna la variabile
    num_battiti++;   //incrementa il num di battiti
  }
  
  checkBattito = checkBattito * 0.97;  //se non è avvenuto un battito decrementa di poco il valoreMassimo , se è avvenuto il battito decrementa di poco la differenza

  if ( millis() >= tempoBPM + 15000 ) {  //i num battiti che ho calcolato sono quelli misurati durante tempoBPM+15 secondi
    Serial.print("BPM : ");
    Serial.println(num_battiti * 4);  //devo moltiplicare i num battiti attuali per 4 per arrivare a 60 secondi e ricavare i bpm
    esp8266.write(num_battiti*4);
    tempoBPM = millis(); 
    num_battiti = 0;  //azzera il num battiti per effettuare una prossima misura nei prossimi 5 secondi
  }
  delay(50);
}
