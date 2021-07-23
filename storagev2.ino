//node32s 
const int LED_CON_PIN = 17;
//GTECHNIC I2C IS MASTER NEAEBY 1,2,3,4... PARITY OF NUMBMERIC
#include <Wire.h>  // pin scl-P22  sda-P21
#include <WiFi.h>
WiFiClient client; //exturn
const char* ssid     = "MY_PROJECT"; // the ssid/name of the wifi, the esp will be connected to
const char* password = "03913352"; // the password of that wifi
const char* host = "192.168.1.107";
const int httpPort = 8089;

//unsigned char PCF_8574 = 0x00; //out 
//const byte PCF8574_ADDR [] = {0x20,0x21,0x22,0x23};
//byte PCF8574_STATE[4];
//String STORAGE_NAME [] = {"science","science","mechanic","electronics"};
///STO wifi libe
struct STO_PCF{
  byte Addr;
  String Name;
  const uint8_t Addr_Len;
  byte Value;
};   

static int LOOP = 0;
static String Storage_Name;
static String BYTE_STRING = "";

static bool ACCESS_TESK = true;
static int siz_q = 0;
static String Q_Data_StatusT1 = "";
static String Q_Data_StatusT2 = "";
static int Q_COUNTER = 0;
static bool T1_CLEARE = false;

static struct STO_PCF Ar_Storage[] = {
                                { 0x20 , "science" , 6 , 0 },
                                { 0x21 , "science" , 6 , 0 },
                                { 0x22 , "mechanic" , 6 , 0 },
                                { 0x23 , "electronics" , 6 , 0 }
                              };
TaskHandle_t Task1;
TaskHandle_t Task2;

void setup() {
  Serial.begin(115200);
  Wire.begin();
  //Q_Data_Status.reserve(2048);
  pinMode(LED_CON_PIN , OUTPUT);
  digitalWrite(LED_CON_PIN , HIGH);
  delay(20);
  WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
          digitalWrite(LED_CON_PIN , HIGH);   // turn the LED on (HIGH is the voltage level)
          delay(250);                       // wait for a second
          digitalWrite(LED_CON_PIN , LOW);    // turn the LED off by making the voltage LOW
          delay(250);  
        //Serial.print(".");
   }
 
   digitalWrite(LED_CON_PIN , LOW);
   delay(20);
   /*
   int chker = 1 , index = 0 ;
   while(true){    
       if(!chk_slave(Ar_Storage[index++].Addr)){
          chker *= (index+1);
       }
       if(chker == 24) break;
       if(index >= 4){
          index = 0;
          chker = 0;
       }
       delay(20);
   }
 */
   for(int i=0;i<4;i++){
     digitalWrite(LED_BUILTIN, HIGH);
     //IOexpanderWrite(PCF8574_ADDR[i],PCF_8574);
     //Serial.print(Ar_Storage[i].Addr,HEX);
     if(! chk_slave(Ar_Storage[i].Addr) );  //return;
     delay(20);
   }
   
   digitalWrite(LED_BUILTIN, LOW);
   delay(1000);
    for(LOOP ; LOOP < 4 ; LOOP ++){
        byte current_state = IOexpanderRead(Ar_Storage[LOOP].Addr);
        //Serial.println(current_state ,HEX);
              for(uint8_t index_name = 0 ; index_name < 4 ; index_name++ ){  //วนหาชื่อ storage name ที่เหมือนกัน
                if(Ar_Storage[index_name].Name == Ar_Storage[LOOP].Name){  //yed เหมือนกัน ทำการต่อ Byte_String
                    if(index_name == LOOP){  //index ที่สถานะมีการเปลี่ยนแปลงค่า
                      //Serial.println("if");
                      for(uint8_t byt = 0 ; byt < Ar_Storage[index_name].Addr_Len  ; byt++){
                        BYTE_STRING += ((current_state >> byt) & 0x01) == 0 ? "0" : "1";
                      } 
                    }else{   //index อื่นที่เป็นแผนกเดียวกัน
                      //Serial.println("else");
                      for(int byt = 0 ; byt < Ar_Storage[index_name].Addr_Len  ; byt++){
                        BYTE_STRING += ((Ar_Storage[index_name].Value >> byt) & 0x01) == 0 ? "0" : "1";
                      } 
                    }
                    
                  }
              }
              
              Serial.print("core0:");
              Serial.println(Q_Data_StatusT1);

              
              Q_Data_StatusT1 += "storage_name=" + Ar_Storage[LOOP].Name + "&val=" + BYTE_STRING + ">";
              //HTTP_req("/handleSTORAGE.php?storage_name=" + Ar_Storage[LOOP].Name + "&val=" + BYTE_STRING);
              Ar_Storage[LOOP].Value = current_state;
              BYTE_STRING = "";

          delay(40);
    }
  LOOP = 0;
  delay(1000);
     //create a task that will be executed in the Task1code() function, with priority 1 and executed on core 0
  xTaskCreatePinnedToCore(
                    Task_readSensor,   /* Task function. */
                    "TaskReadSensor",     /* name of task. */
                    10000,       /* Stack size of task */
                    NULL,        /* parameter of the task */
                    1,           /* priority of the task */
                    &Task1,      /* Task handle to keep track of created task */
                    0);          /* pin task to core 0 */                  
  delay(500); 

  //create a task that will be executed in the Task2code() function, with priority 1 and executed on core 1
  xTaskCreatePinnedToCore(
                    Task_update,   /* Task function. */
                    "TaskUpdate",     /* name of task. */
                    10000,       /* Stack size of task */
                    NULL,        /* parameter of the task */
                    1,           /* priority of the task */
                    &Task2,      /* Task handle to keep track of created task */
                    1);
   delay(500); 
   
   
}


void loop() {

  
}

void Task_readSensor (void * pvParameters )
{
  
  //if readstate change
  //disable access task
  //loop io
  //add append
  //clear loop io
  //enble task
  //single task sen onstate change best way
  LOOP = 0;
  delay(1000);
  while(true)
  {
      byte current_state = IOexpanderRead(Ar_Storage[LOOP].Addr);
      //Serial.println(current_state ,HEX);
      if(Ar_Storage[LOOP].Value != current_state ){
           ACCESS_TESK = false;
            for(uint8_t index_name = 0 ; index_name < 4 ; index_name++ ){  //วนหาชื่อ storage name ที่เหมือนกัน
              if(Ar_Storage[index_name].Name == Ar_Storage[LOOP].Name){  //yed เหมือนกัน ทำการต่อ Byte_String
                  if(index_name == LOOP){  //index ที่สถานะมีการเปลี่ยนแปลงค่า
                    //Serial.println("if");
                    for(int byt = 0 ; byt < Ar_Storage[index_name].Addr_Len  ; byt++){
                      BYTE_STRING += ((current_state >> byt) & 0x01) == 0 ? "0" : "1";
                    } 
                  }else{   //index อื่นที่เป็นแผนกเดียวกัน
                    //Serial.println("else");
                    for(int byt = 0 ; byt < Ar_Storage[index_name].Addr_Len  ; byt++){
                      BYTE_STRING += ((Ar_Storage[index_name].Value >> byt) & 0x01) == 0 ? "0" : "1";
                    } 
                  }
                  
                }
            }
            
            Serial.print("core0:");
            Serial.println(Q_Data_StatusT1);
            /*
            Serial.print(Ar_Storage[LOOP].Addr,HEX);
            Serial.print(":");
            Serial.print(BYTE_STRING);
            Serial.print(":");
            Serial.print(Ar_Storage[LOOP].Name);
            Serial.print(":");
            Serial.print(current_state,HEX);
            Serial.println();
            */
            //up
            //add
            //clear
            
            Q_Data_StatusT1 += "storage_name=" + Ar_Storage[LOOP].Name + "&val=" + BYTE_STRING + ">";
            ACCESS_TESK = true;
            //HTTP_req("/handleSTORAGE.php?storage_name=" + Ar_Storage[LOOP].Name + "&val=" + BYTE_STRING);
            Ar_Storage[LOOP].Value = current_state;
            BYTE_STRING = "";
           
            
        }
        
        LOOP++;
        Serial.println(LOOP );
        if(LOOP >= 4){ LOOP = 0;}
        /*
        if(T1_CLEARE){  //T1_CLEARE 
            uint16_t sep_ = 0;
            Serial.println("qcnnt: ");
            Serial.println(Q_COUNTER);
            for(int y = 0 ; y < Q_COUNTER ; y++){
               sep_ = Q_Data_StatusT1.indexOf('>' , sep_ + 1);
            }
            Serial.println("index cnt: "+sep_);
            Serial.println(Q_Data_StatusT1 );
            Q_Data_StatusT1 = Q_Data_StatusT1.substring( sep_ + 1 , Q_Data_StatusT1.length()); //skip '>'  "5443243>s453"
            Serial.println(" cut core0:" );
            Serial.println(Q_Data_StatusT1 );
            Q_COUNTER = 0;
            T1_CLEARE = false;
        }
        */
        //Serial.print("HELLO WORD");
        delay(1000);
        /*
        byte current_state = IOexpanderRead(0x20);
        Serial.println(current_state,HEX);
        delay(1000);
        
        */
  }
  vTaskDelete(NULL);
}


void Task_update(void * pvParameters)
{
  //if access task
  //if q 
  //pop LiFO send 
   //-find_index(String dataa, char separator, int index)
   //-sub print
   //-sub FIFO skip > seperate
  //clear
  //next q
  while(true)
  {
      if(ACCESS_TESK && Q_Data_StatusT2 == "" &&  Q_Data_StatusT1 != ""){
          Q_Data_StatusT2 = Q_Data_StatusT1;
          Q_Data_StatusT1 = "";
      }
        //Serial.println(ACCESS_TESK);
            if(Q_Data_StatusT2.length() > 0){
              //Serial.print("core1:");
              //Serial.print(Q_Data_Status);
              String FIFO_storage = find_index(Q_Data_StatusT2 , '>' , 0);
              if(FIFO_storage.length() == Q_Data_StatusT2.length()) //if last one
                  FIFO_storage = FIFO_storage.substring(0 , FIFO_storage.length() -1);
              //Serial.println(FIFO_storage);
              if(HTTP_req("/handleSTORAGE.php?" + FIFO_storage)){
                      // t1-t2 = start text
                      int sep_index = Q_Data_StatusT2.indexOf('>');
                      Q_Data_StatusT2 = Q_Data_StatusT2.substring( sep_index + 1 , Q_Data_StatusT2.length()); //skip '>'  "5443243>s453"
                      
                      Serial.println("cut;");
                     // Serial.println(Q_Data_StatusT2);
               }
    
            }else if(Q_Data_StatusT2.length() > 2040){  //over flow
              digitalWrite(LED_CON_PIN , HIGH);
            }


        delay(500);
    
  }
  
  vTaskDelete(NULL);
}


String find_index(String dataa, char separator, int index)
{
  int found = 0;
  int strIndex[] = {0, -1};
  int maxIndex = dataa.length()-1;

  for(int i=0; i<=maxIndex && found<=index; i++){   //
    if(dataa.charAt(i)==separator || i==maxIndex){  //จับตำแหน่งของ index ที่ต้องการหรือ สุดท่้าย
        found++;
        strIndex[0] = strIndex[1]+1; //inedex start
        strIndex[1] = (i == maxIndex) ? i+1 : i;  //index stop or last index
    }
  }

  return found>index ? dataa.substring(strIndex[0], strIndex[1]) : "";
}

bool HTTP_req(String url)
{
    delay(200);
    if (!client.connect(host,httpPort)) {
       // Serial.println("connection failed");
        return false;
    }
    
    Serial.println("sending");
    Serial.println(String("GET ") + url + " HTTP/1.1\r\n" +"Host: " + host + "\r\n" +"Connection: close\r\n\r\n");
    Serial.println("<<<");             
    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                 "Host: " + host + "\r\n" +
                 "Connection: close\r\n\r\n");
    unsigned long timeout = millis();
    while (client.available() == 0) {
        if (millis() - timeout > 5000) {
            Serial.println(">>> Client Timeout !");
            client.stop();
            return false;
        }
    }

    // Read all the lines of the reply from server and print them to Serial
    while(client.available()) {
        String line = client.readStringUntil('\r');
        Serial.print(line);
    }
    return true;
}



bool chk_slave(byte address)
{
   Wire.beginTransmission(address);
   if(Wire.endTransmission() == 0){
       Serial.println();
       Serial.print("I2C device found at: "); 
       Serial.println(address,HEX);
       return true;
    }
    return false;
}

//Write a byte to the IO expander
void IOexpanderWrite(byte address, byte _data ) 
{
   Wire.beginTransmission(address);
   Wire.write(_data);
   Wire.endTransmission(); 
}

//Read a byte from the IO expander
byte IOexpanderRead(int address) 
{
   byte _data;
   Wire.requestFrom(address, 1);
   if(Wire.available()) {
     _data = Wire.read();
   }
   return _data;
}
