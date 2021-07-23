
//ไม่ทำ .h .cpp
///----------pcf8574-hand------------
#include <Wire.h>  // pin scl-P22  sda-P21
#define PCF8574_ADDR B0100000
unsigned char PCF_8574 = 0xff; 
//l >27 26 25 33 32 35 34 r> rx2 tx2 5 18
const uint8_t SENSOR_PIN = 35;
bool SENSOR_VAL = 0;

const uint8_t END_SW_PIN = 0x80;  //redefined
const uint8_t TOP_SW_PIN = 0x40;
bool TOP_SW_VAL = 0;
bool END_SW_VAL = 0;

const uint8_t ITEM_PIN = 18;
bool ITEM_VAL = false;

//uint8_t CURRENT_FLOOR = 0;
const uint8_t ctr = 34;
uint8_t LINE_CNT = 0;
bool LINE_MARK = false;

//---------------pcf8574-lag--------------------
const uint8_t sen_pin [] = {27 , 26 , 25 , 33 , 32};
uint8_t fron_stat[5] , point_ctr;
//portMUX_TYPE mux = portMUX_INITIALIZER_UNLOCKED;
const byte STOP = 0x00; 
const byte FWARD = 0x06;
const byte BWARD = 0x09;
const byte TURNR = 0x0a;
const byte TURNL = 0x05;

//1.stanby(bot) 2.recieve point 3.store 4.sent point  -> "recieve>F2:L1:F2:S1:G1:->store>F2:L1:F2:S2:G2:->sent>F5:" 

struct cmd{ //allocaion is better way
  char comand[200];
  uint16_t counter[200];
  uint16_t siz = 0;
};

static cmd robot_run;
//----------------------------------------wifi----------------------------------------------------------------------
#include <WiFi.h>
WiFiClient client; //exturn

static bool ACCESS_DATA_TASK = true;
static String STR_REQ_T0 = "";
static String STR_REQ_T1 = "";
//static String current_req = "";

const char* ssid     = "MY_PROJECT"; // the ssid/name of the wifi, the esp will be connected to
const char* password = "03913352"; // the password of that wifi
//server port
const char* host = "192.168.1.107"; //ipserver
const int httpPort = 8089;

const String BOT_NAME = "BOT1";
String server_say;
//static volatile bool FORWARD_LOOP = true;  //isr
//static volatile bool FORWARD_COUNTER = false;  //isr
//static bool STOP_WAIT = false;
//String CHANGE_LOCATION;
static bool CAN_STEP_OUT = false; 
static uint8_t CAN_STEP_EXCOUNT = 0;
static uint8_t CAN_STEP = 0;
//static uint8_t RUN_STATE = 0; // 0 stop 1 stop wait 2 run spd
static bool ACCESS_PULSE = true;
static String BOT_STATUS = "STANBY";
static String JOBID = "";
static String JOBID_2 = "";
static String DATA_COMMANDS = "";
static String LOCATION_LIST = "";
static String DATA_COMMANDS_2 = "";
static String LOCATION_LIST_2 = "";
static String STOP_POINT = "";
static bool CMD_BREAKER = false;
uint16_t COMMANDS_COUNTER = 0; 
static String CURRENT_LOCATION = "STANBY";

//motor control
const int RIGHT_SPD_PIN = 13;
const int LEFT_SPD_PIN = 14;
const int RIGHT_PWM_CH0 = 0;
const int LEFT_PWM_CH1 = 1;
const long FREAQUENCY = 10000;
const int RESOLUTION = 8;  //ESP32 uses 8, 10, 12, 15-bit resolution for PWM generation PWM value
//l298n 6.5v
//bot1 200 230 COMMANDS_COUNTER = 2
//bot2 205 230 COMMANDS_COUNTER = 1
const int MIN_SPD = 205 , MAX_SPD = 220;//,CMD_START = 1;
const int PWM_SENSITIVE_TUNE = 190; // bot1=177 t2=180
int LEFT_SPD = MIN_SPD;
int RIGHT_SPD = MIN_SPD;
const int STEP_SPD = 4;

TaskHandle_t Task1;
TaskHandle_t Task2;


void setup() {
  Serial.begin(115200); 
  server_say.reserve(2048);
   
  //--------------- lag pin init---------------------------
  for(int i=0;i<5;i++){
      pinMode(sen_pin[i],INPUT);
      digitalWrite(sen_pin[i],LOW);
  }
  pinMode(ctr,INPUT);
  //attachInterrupt(digitalPinToInterrupt(ctr), handleInterrupt, RISING);
  
  //*hands
  Serial.begin(115200);
  pinMode(SENSOR_PIN,INPUT);
  pinMode(ITEM_PIN,INPUT);
  pinMode(END_SW_PIN,INPUT);
  pinMode(TOP_SW_PIN,INPUT);
  
  digitalWrite(SENSOR_PIN,LOW);
  digitalWrite(ITEM_PIN,LOW);
  digitalWrite(TOP_SW_PIN,LOW);
  digitalWrite(END_SW_PIN,LOW);
  //set_hand();
    //Serial.print("Connecting to ");
    //Serial.println(ssid);
  Wire.begin();
  IOexpanderWrite(PCF8574_ADDR  , PCF_8574); //write 0
      //pwm control
  pinMode(RIGHT_SPD_PIN , OUTPUT);
  pinMode(LEFT_SPD_PIN , OUTPUT);
  ledcSetup(RIGHT_PWM_CH0,FREAQUENCY , RESOLUTION);  //Channel ledcSetup(pwmChannel, freq, resolution);
  ledcSetup(LEFT_PWM_CH1,FREAQUENCY , RESOLUTION);
  ledcAttachPin(RIGHT_SPD_PIN,RIGHT_PWM_CH0); //ledcAttachPin(enable1Pin, pwmChannel);
  ledcAttachPin(LEFT_SPD_PIN,LEFT_PWM_CH1);
  /*
  //test hand
  set_hand();
  delay(2000);
  //auto floor true get : false pass
  hand_move(1,false);
  delay(2000);
  hand_move(1,true);
  delay(2000);
  hand_move(2,true);
  delay(2000);
  hand_move(1,false);
  return;
  */
 
  
  
    pinMode(LED_BUILTIN, OUTPUT);
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
          digitalWrite(LED_BUILTIN, HIGH);   // turn the LED on (HIGH is the voltage level)
          delay(250);                       // wait for a second
          digitalWrite(LED_BUILTIN, LOW);    // turn the LED off by making the voltage LOW
          delay(250);  
        //Serial.print(".");
    }
    digitalWrite(LED_BUILTIN, LOW);
    
    set_hand();
    /*
    Serial.println("");
    Serial.println("WiFi connected");
    Serial.println("IP address: ");
    Serial.println(WiFi.localIP());
    */
  //Wire.begin();
  

  
  //auto floor true get : false pass
  //if(hand_move(1,false)); //return;
  //delay(4000);
  //if(hand_move(2,false)); //return;
  delay(1000);
  request("&REQ=report&STATUS=STANBY&JOBID=none&LOCATION="+BOT_NAME+"&HASJOB2=1"); //slow
  
  
  //create a task that will be executed in the Task1code() function, with priority 1 and executed on core 0
  xTaskCreatePinnedToCore(
                    Task_core0,   /* Task function. */
                    "Taskcore0",     /* name of task. */
                    10000,       /* Stack size of task */
                    NULL,        /* parameter of the task */
                    1,           /* priority of the task */
                    &Task1,      /* Task handle to keep track of created task */
                    0);          /* pin task to core 0 */                  
  delay(500); 

  //create a task that will be executed in the Task2code() function, with priority 1 and executed on core 1
  xTaskCreatePinnedToCore(
                    Task_core1,   /* Task function. */
                    "Taskcore1",     /* name of task. */
                    10000,       /* Stack size of task */
                    NULL,        /* parameter of the task */
                    1,           /* priority of the task */
                    &Task2,      /* Task handle to keep track of created task */
                    1);          /* pin task to core 1 */
    delay(500); 
}


void Task_core0( void * pvParameters )
{
  while(true){
    //Serial.print("Task1 running on core ");
    //Serial.println(xPortGetCoreID());
          //JOBID = "555";
          //DATA_COMMANDS = "B0:L0:F1:R1>F2:L1:G1:R0>F2:F1:R1:P2:L0:F3:R1:F1:R1:F6>F2:R1>F2:R1:F0";
          //LOCATION_LIST ="sta>re>sto>sen>sta";
    if(DATA_COMMANDS.length() > 0 ||  DATA_COMMANDS_2.length() > 0){
          Serial.print("working");
          //COMMANDS_COUNTER = CMD_START;
           ///"F2:L1:F2:S1:G1>F2:L1:F2:S2:G2>F5"
          //Work("sta>re>sto>sen>sta","B0:L0:F1:R1>F2:L1:G1:R0>F2:F1:R1:P2:L0:F3:R1:F1:R1:F6>F2:R1>F2:R1:F0");
          if(DATA_COMMANDS.length() > 0){ //coppy cmd2 to cmd1 clear cmd2 is best way
              Work(LOCATION_LIST,DATA_COMMANDS , 1);
              DATA_COMMANDS = "";
              LOCATION_LIST = "";              
              if(DATA_COMMANDS_2 == ""){
                  set_hand();
                  Access_Req_Task("&REQ=report&STATUS=STANBY&JOBID=none&LOCATION="+BOT_NAME+"&HASJOB2=1"); //slow
                  delay(500);
              }
            }else{
              CMD_BREAKER = false;
              STOP_POINT ="";
              Work(LOCATION_LIST_2 , DATA_COMMANDS_2 , 2); 
              DATA_COMMANDS_2 = "";
              LOCATION_LIST_2 = "";
              set_hand();
              Access_Req_Task("&REQ=report&STATUS="+DATA_COMMANDS_2+"&JOBID=none&LOCATION="+BOT_NAME+"&HASJOB2=1"); //slow
              delay(500);
            }

          CAN_STEP = 0;
    }else{
         delay(1000);
         Access_Req_Task("&REQ=report&STATUS=STANBY&JOBID=none&LOCATION="+BOT_NAME+"&HASJOB2=1");
         delay(2000); //server spd
         Access_Req_Task("&REQ=newjob"); 
         delay(2);
    }
      
  }
  vTaskDelete(NULL);
}


void Task_core1( void * pvParameters )
{
  long randDelay = 150 + random(100);
  while(true){
    //Serial.print("Task2 running on core ");
    //Serial.println(xPortGetCoreID());
    
    if(/*ACCESS_DATA_TASK && */ STR_REQ_T0 != ""){
        ACCESS_DATA_TASK = false;
        STR_REQ_T1 = STR_REQ_T0;
        STR_REQ_T0 = "";
        ACCESS_DATA_TASK = true;
    }


    if(STR_REQ_T1.length() > 0){
       //if(JOBID != "" && ! STR_REQ_T1.endsWith("newjob")){  //!( id && renew job)
            
        //}else{
          request(STR_REQ_T1);
        //}
        
        delay(randDelay);
    }
    
    //hand_read_sensor();
   delay(100);
  }
  vTaskDelete(NULL);
}

void Access_Req_Task(String mydata)
{   
  bool waitting = ACCESS_DATA_TASK;
    if(waitting){
        ACCESS_DATA_TASK = false;
        STR_REQ_T0 = mydata; 
        ACCESS_DATA_TASK = true;
        return;
      }
      
    while(!waitting){
        ACCESS_DATA_TASK = false;
        STR_REQ_T0 = mydata; 
        ACCESS_DATA_TASK = true;
        waitting = true;  //ex loop
    }
}




void loop() 
{
  
}



void request(String report){
    //WiFiClient client; //exturn
    // Use WiFiClient class to create TCP connections
    delay(50);
   // Serial.print("\n");
    //Serial.print("connecting to ");
    //Serial.println(host);
    
    if (!client.connect(host, httpPort)) {
        Serial.println("connection failed");
        return;
    }

    // We now create a URI for the request
    String url = "/handleROBOT.php?BOT_NAME="+BOT_NAME+report;

    Serial.print("Requesting URL: ");
    Serial.println(url);

    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                 "Host: " + host + "\r\n" +
                 "Connection: close\r\n\r\n");
    unsigned long timeout = millis();
    while (client.available() == 0) {
        if (millis() - timeout > 4000) {
            Serial.println(">>> Client Timeout !");
            client.stop();
            //RUN_STATE = 1;
            return;
        }
    }

    // Read all the lines of the reply from server and print them to Serial
    
    while(client.available()) {
        String server_line = client.readStringUntil('\r');
        server_line = server_line.substring(1,server_line.length()-1); //\nlinedata
        //Serial.println("sever say > ");
        //Serial.println(">");
        Serial.println(server_line); ///substring
        //Serial.println("<"); 
        
          if(server_line.startsWith(BOT_NAME)){//\nline\r
                //Serial.println(); 
                //Serial.println(); 
                //Serial.print("yed condition");
                int start = server_line.indexOf('>');  //name>data
                server_say = server_line.substring(start+1 , server_line.length());
                //Serial.println();
                Serial.println(server_say );
                Serial.println(); 

                
                if(server_say.startsWith("YOURJOB")){ //recieve job = "BOTNAME>YOURJOB:564646>hell location<B0:L0:F1:R1>F2:R1:G1:L0:F1:R1:F1:L1>F3:L1:P2:R0:F1:R1:F1:R1:F4:L1:F1:R1>F3:R1>F2:R1:F0"
                  int colon = server_say.indexOf(':');
                  int first_shift = server_say.indexOf('>');
                  int rev_shift = server_say.indexOf('<');
                  JOBID = server_say.substring( colon + 1 , first_shift);  //:564646>
                  LOCATION_LIST = server_say.substring( first_shift +1 , rev_shift  );  //>sta>re>sto>sen>sta<
                  DATA_COMMANDS = server_say.substring( rev_shift + 1 ,  server_say.length());  //<F1:R1>F2 length........
                }
                /*
                Serial.print("JOBID:");
                Serial.println(JOBID);
                Serial.print("LOCATION_LIST:");
                Serial.println(LOCATION_LIST);
                Serial.print("DATA_COMMANDS:");
                Serial.println(DATA_COMMANDS);
                */
                if(server_say.startsWith("cango")){
                    int inumb = server_say.indexOf('>');
                    server_say = server_say.substring(inumb+1 , server_say.length());
                    Serial.println(server_say);
                    if(CAN_STEP == 0 && server_say != "0"){
                      CAN_STEP = server_say.toInt(); //= and deceass
                      Serial.println(">");
                      Serial.println(CAN_STEP);
                    }
                    Serial.println(">");
                    Serial.println("CAN_STEP:");
                    Serial.println(CAN_STEP);
                }

                if(server_say.startsWith("canstep_out")){
                    int inumb = server_say.indexOf('>');
                    server_say = server_say.substring(inumb+1 , server_say.length());
                    Serial.println("KCAN_STEPout:");
                    Serial.println(server_say);
                    if(server_say == "1"){
                      CAN_STEP_EXCOUNT++;
                      if(CAN_STEP_EXCOUNT > 3){
                        CAN_STEP_OUT = true;
                        CAN_STEP_EXCOUNT = 0;
                      }
                    }else{
                      if(CAN_STEP_EXCOUNT > 0) CAN_STEP_EXCOUNT--;
                    }

                }

                //บอกจุดที่จะไปรับงาน2
                if(server_say.startsWith("stop_point")){ //stop_point>point>id2434234>loc<B0:L0:F1:R
                    STOP_POINT = find_index(server_say ,'>', 1);   ///BOT1>   //stop_point>sto:11>6756756>sto:sto:11>sen:0>sta:0<L1>F1:R1:F2:R1:F0
                    JOBID_2 = find_index(server_say ,'>', 2);
                    DATA_COMMANDS_2 = find_index(server_say ,'<', 1);
                    int firsin = server_say.indexOf('>');
                    int secindex = server_say.indexOf('>' , firsin +1);
                    int thirdindex = server_say.indexOf('>' , secindex +1);
                    int back_sep = server_say.indexOf('<');
                    LOCATION_LIST_2 = server_say.substring(thirdindex + 1 , back_sep);
                }

                if(server_say.startsWith("emergencystop")){
                    CAN_STEP = 0;
                }
          }
          server_say = "";
    }
    
}

void getCMD(String cmd , struct cmd * p)
{ 
   ///////"F2:L1:F2:S1:G1>F2:L1:F2:S2:G2>F5"
   int index = 0; 
   String sub_cmd = "";  
   int siz = 0;
   
   for(int i = 0; i < cmd.length() ; i++){
      if(cmd.charAt(i) == ':') siz++;
   }
   
   if(siz == 0){
        p->comand[index] = cmd.charAt(0);
        String numb = cmd.substring(1 , sub_cmd.length()-1);
        p->counter[index] = numb.toInt(); //if(isint)
        p->siz = 1;
        return;
    } 
   //Serial.print("siz");
   //Serial.println(p->siz);
    while((sub_cmd = find_index(cmd,':',index)) != "" ){
        p->comand[index] = sub_cmd.charAt(0);
        String numb = sub_cmd.substring(1,sub_cmd.length());
        p->counter[index] = numb.toInt(); //if(isint)
        //Serial.println(p->comand[index]);
        //Serial.println(p->counter[index]);
        index++;
    }
    p->siz = siz+1;
}


void clearCMD(struct cmd * p)
{
   for(int i=0 ;i < p->siz ; i++){
        p->comand[i] = ' ';
        p->counter[i] = 0;
    }
    p->siz = 0;
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


void Renew_Location(String location_name , int point)
{
  
       COMMANDS_COUNTER = point;
       CURRENT_LOCATION = location_name; 
}


void Work(String lo_list , String job , int job_time)
{
  Serial.print(":");
  Serial.println(lo_list);
  Serial.print(job);
  Serial.print(":");
   if(job == "" || lo_list == "") return;
   
   uint8_t len = 0;
   for(int sizz=0 ; sizz < lo_list.length() ; sizz++){
      if(lo_list.charAt(sizz) == '>'){
          len++;
        }
   }  
   len++;
   String sub_cmd[len];
   String sub_loc[len],start_at[len];
   String sub = "";
   uint8_t lcnt = 0;
   //Serial.println("cmd list:");
   while((sub = find_index(job,'>',lcnt)) != ""){
      sub_cmd[lcnt] = sub;
      //Serial.println(sub);
      lcnt++;
   }
   //Serial.println("locat list:");
   lcnt = 0;
   while((sub = find_index(lo_list,'>',lcnt)) != ""){
      
      sub_loc[lcnt] = find_index(sub,':',0);
      start_at[lcnt] = find_index(sub,':',1);
      Serial.println(sub);
      Serial.println(">");
      Serial.println(sub_loc[lcnt]);
      Serial.println(">");
      Serial.println(start_at[lcnt]);
      lcnt++;
   }
   
  lcnt = 0;
  if(job_time == 1){
    
    do{ //conter 4
      Access_Req_Task("&REQ=step_out&STATUS=BACKING&JOBID="+JOBID+"&LOCATION="+sub_loc[0]+":"+start_at[0]); //tell first locacation
      delay(1000);
      //Access_Req_Task("&REQ=canstep&STATUS=BACKING&LOCATION="+sub_loc[0]+":"+start_at[0]); //slow
      //delay(1000);
      Serial.println("outing>");
      Serial.println(CAN_STEP);
      Serial.println(">");  
      //if(!CAN_STEP_OUT)  CAN_STEP = 0;
      //if(CAN_STEP == 0) CAN_STEP_OUT = false;
    }while(!CAN_STEP_OUT);  //bolt value only can ex loop
    CAN_STEP_OUT = false;
  }
  
 
  while(lcnt < len){
     getCMD(sub_cmd[lcnt],&robot_run);
     /*
     Serial.println(sub_loc[lcnt]);
     for(int i=0 ; i< robot_run.siz ; i++){
            Serial.println(robot_run.comand[i]);
            Serial.println(robot_run.counter[i]);
      }
      */
     //CHANGE_LOCATION = sub_loc[lcnt];
     //Renew_Location();
     //uint8_t loc_index = lcnt;
     //isInt(start_at[lcnt]);
     ROBOT_RUN(sub_loc[lcnt] , start_at[lcnt].toInt() , robot_run);   //sub_loc[++lcnt]
     clearCMD(&robot_run);
     if(CMD_BREAKER) break;
     lcnt++;
  }


  
}

/*
  char* comand;
  int* counter;
  int siz;

*/
void ROBOT_RUN(String location , int start_point , struct cmd RUN)
{
  if(CURRENT_LOCATION != location) CAN_STEP = 0; //เมื่อเปลี่ยนเส้นจะต้องขอการเดินไหม่
  Renew_Location(location , start_point);  //เปลี่ยนเส้น
  uint16_t i=0;
  while(i < RUN.siz){
     if(CMD_BREAKER) break;
      Serial.println(RUN.comand[i]);
      Serial.println(RUN.counter[i]);
      
      switch(RUN.comand[i]){
        case 'F':  
          MoveTo(RUN.counter[i]); //while true -> can't report
          /*
          if(!ITEM_VAL &&  i+2 <= RUN.siz){ //no item and next is p
              if(RUN.comand[i+2] == 'P')
                i+2;
          }
          */
          break;
        case 'B': 
          Moveback(RUN.counter[i]);
          break;
        case 'L': 
          TurnLeft(RUN.counter[i]);
          break;
        case 'R': 
          TurnRight(RUN.counter[i]);
          break;
        case 'G': 
          getItem(RUN.counter[i]);
          break; //get
        case 'P': 
          setItem(RUN.counter[i]);
          break; //past
        
      }
      
    i++;
  }
  
}



void Forword()
{
    PCF_8574 = (IOexpanderRead(PCF8574_ADDR) & 0xf0) | FWARD;
    IOexpanderWrite(PCF8574_ADDR , PCF_8574);
}

void Backword()
{
    PCF_8574 = (IOexpanderRead(PCF8574_ADDR) & 0xf0) | BWARD;
    IOexpanderWrite(PCF8574_ADDR , PCF_8574);
}

void RotateLeft()
{
    PCF_8574 = (IOexpanderRead(PCF8574_ADDR) & 0xf0) | TURNL;
    IOexpanderWrite(PCF8574_ADDR , PCF_8574);
}

void RotateRight()
{
    PCF_8574 = (IOexpanderRead(PCF8574_ADDR) & 0xf0) | TURNR;
    IOexpanderWrite(PCF8574_ADDR , PCF_8574);
}


void MotorStop()
{
    PCF_8574 = (IOexpanderRead(PCF8574_ADDR) & 0xf0) | STOP;
    IOexpanderWrite(PCF8574_ADDR , PCF_8574);
}

void Forword_SPD(int left_spd , int right_spd)
{   
    Speed_Control(left_spd,right_spd);
    Forword();
}

void Backword_SPD(int left_spd , int right_spd)
{   
    Speed_Control(left_spd,right_spd);
    Backword();
}

void RotateLeft_SPD(int left_spd , int right_spd)
{   
    Speed_Control(left_spd,right_spd);
    RotateLeft();
}

void RotateRight_SPD(int left_spd , int right_spd)
{   
    Speed_Control(left_spd,right_spd);
    RotateRight();
}

void Speed_Control(int left_spd , int right_spd)
{
  RIGHT_SPD = right_spd;
  LEFT_SPD = left_spd;
  
  if(RIGHT_SPD >= MAX_SPD) RIGHT_SPD = MAX_SPD;
  if(LEFT_SPD >= MAX_SPD) LEFT_SPD = MAX_SPD;
  
  if(RIGHT_SPD <= MIN_SPD - PWM_SENSITIVE_TUNE) RIGHT_SPD = MIN_SPD - PWM_SENSITIVE_TUNE;
  if(LEFT_SPD <= MIN_SPD - PWM_SENSITIVE_TUNE) LEFT_SPD = MIN_SPD - PWM_SENSITIVE_TUNE;
  
  ledcWrite(RIGHT_PWM_CH0 , RIGHT_SPD); //ledcWrite(pwmChannel, dutyCycle);
  ledcWrite(LEFT_PWM_CH1 , LEFT_SPD);
}

void Run_Speed(int point_spd , bool up_down)
{
  if(up_down){  //up
    if(point_spd >= 2 && point_spd <= 0){
         LEFT_SPD -= (STEP_SPD+STEP_SPD);   
         RIGHT_SPD -= (STEP_SPD+STEP_SPD);
     }else{
         LEFT_SPD += STEP_SPD;   
         RIGHT_SPD += STEP_SPD;
     }
  }else{ //down
         LEFT_SPD -= (int)STEP_SPD/2;   
         RIGHT_SPD -= (int)STEP_SPD/2;
  }
  
}

void prepare_Moveto()
{
    //if(ITEM) spd+5

     while(true){
            delay(50);
            readSensor();
            if(fron_stat[0] == LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  HIGH && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //00100 ready
                break;
            }else if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] == HIGH && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01100
               RotateLeft_SPD( PWM_SENSITIVE_TUNE , PWM_SENSITIVE_TUNE ); delay(1);
            }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] == HIGH && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00110
               RotateRight_SPD( PWM_SENSITIVE_TUNE , PWM_SENSITIVE_TUNE ); delay(1);
            }else if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01000
               RotateLeft_SPD( PWM_SENSITIVE_TUNE , PWM_SENSITIVE_TUNE );  delay(1);
            }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00010
               RotateRight_SPD( PWM_SENSITIVE_TUNE , PWM_SENSITIVE_TUNE );  delay(1);
            }else if(fron_stat[0] ==  HIGH && fron_stat[1] == HIGH && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //11000
               RotateLeft_SPD( PWM_SENSITIVE_TUNE , PWM_SENSITIVE_TUNE );  delay(2);
            }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] == HIGH && fron_stat[4] ==  HIGH){ //00011
               RotateRight_SPD( PWM_SENSITIVE_TUNE , PWM_SENSITIVE_TUNE );  delay(2);
            }else if(fron_stat[0] ==  HIGH && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] == HIGH && fron_stat[4] ==  HIGH){ //10011
               RotateRight_SPD( PWM_SENSITIVE_TUNE , PWM_SENSITIVE_TUNE );  delay(2);
            }else if(fron_stat[0] ==  HIGH && fron_stat[1] ==  HIGH && fron_stat[2] ==  LOW && fron_stat[3] == LOW && fron_stat[4] ==  HIGH){ //11001
               RotateLeft_SPD( PWM_SENSITIVE_TUNE , PWM_SENSITIVE_TUNE );  delay(2);
            }
            
      }
      MotorStop();
     // Serial.print("prepare ok");
}



void MoveTo(int point)
{
  int pt_cnt = 0;
  bool last = false;
  long iterator = 0;
  /* isr
  FORWARD_COUNTER = false;
  FORWARD_LOOP = true;
  */

  // Forword();  //ก่อนเพราะออกจากเลี้ยวมันจะเอียง
  //delay(200);
  prepare_Moveto();
  BOT_STATUS = "RUNNIG";
  if(point == 0){ //ใช้ในกรณีตรงอย่างเดี่ยวไม่นับจุดจนถึงจุดหยุด
       
        while(true){
          
            readSensor();
            if(fron_stat[0] == HIGH && fron_stat[1] ==  HIGH && fron_stat[2] ==  HIGH && fron_stat[3] ==  HIGH && fron_stat[4] ==  HIGH){ //00000
                break;
            }else if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] == HIGH && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01100
               RotateLeft_SPD(MIN_SPD,MIN_SPD);
               delay(2);
            }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] == HIGH && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00110
               RotateRight_SPD(MIN_SPD,MIN_SPD);
               delay(2);
            }if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01000 
               RotateLeft_SPD(MIN_SPD,MIN_SPD);
               delay(2);
            }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00010
               RotateRight_SPD(MIN_SPD,MIN_SPD);
               delay(2);
            }
            
            Forword_SPD(MIN_SPD,MIN_SPD);
            delay(5);

        }
        
  }else{

        //bool current_stat; //true forword false ..back
        LEFT_SPD = MIN_SPD;
        RIGHT_SPD = MIN_SPD;
        Access_Req_Task("&REQ=canstep&JOBID="+JOBID+"&STATUS=PULSE&LOCATION="+CURRENT_LOCATION +":"+ (COMMANDS_COUNTER == 0 ? 1:COMMANDS_COUNTER)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" )); //slow
        delay(500);
        while(true){ //ใช้ในกรณีนับจุดตามเส้น while(FORWARD_LOOP){ 
           
          iterator++;
           //Serial.print("moving");

          
           if(pt_cnt <= point){
            
                 if(CAN_STEP  == 0){ //stop
                      MotorStop();
                      while(CAN_STEP == 0 ){  //&& point_ctr == HIGH
                          LEFT_SPD = MIN_SPD;
                          RIGHT_SPD = MIN_SPD;
                          Access_Req_Task("&REQ=canstep&JOBID="+JOBID+"&STATUS=PULSE&LOCATION="+CURRENT_LOCATION +":"+ (COMMANDS_COUNTER == 0 ? 1:COMMANDS_COUNTER)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" )); //slow
                          delay(1000);
                      }
                  }
                  
                  if(iterator % 40 == 0) Run_Speed(point - pt_cnt , true);
                  Forword_SPD(LEFT_SPD , RIGHT_SPD);
                  readSensor();
                  //for(int y = 0 ; y < 5 ; y++) Serial.print(fron_stat[y]);
                  //Serial.println();
                  if(fron_stat[0] == LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW && point_ctr == HIGH){
                      MotorStop();
                      while(fron_stat[0] == LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW && point_ctr == HIGH){
                          Access_Req_Task("&REQ=report&STATUS=ERROR>OUT_LINE&LOCATION="+(CURRENT_LOCATION +":"+ COMMANDS_COUNTER)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" )); //slow
                          delay(1000);
                      }
                    }
                    
                  delay(20);
                  if(fron_stat[0] == LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //00000
                       if(iterator % 40 == 0) Run_Speed(point - pt_cnt , true);
                    }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] == HIGH && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //00100
                       if(iterator % 40 == 0) Run_Speed(point - pt_cnt , true);  
                    }else if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] == HIGH && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01100 
                       if(iterator % 40 == 0) Run_Speed(point - pt_cnt , false);     
                       RotateLeft_SPD(LEFT_SPD,RIGHT_SPD);
                       delay(8);
                    }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] == HIGH && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00110  
                        if(iterator % 40 == 0) Run_Speed(point - pt_cnt , false);
                       RotateRight_SPD(LEFT_SPD,RIGHT_SPD);
                       delay(8);
                    }else if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01000 
                       if(iterator % 40 == 0) Run_Speed(point - pt_cnt , false);   
                       RotateLeft_SPD(LEFT_SPD,RIGHT_SPD);
                       delay(8);
                    }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00010  
                       if(iterator % 40 == 0) Run_Speed(point - pt_cnt , false);
                       RotateRight_SPD(LEFT_SPD,RIGHT_SPD);
                       delay(8);
                    }else if(fron_stat[0] ==  HIGH && fron_stat[1] == HIGH && fron_stat[2] == LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //11000 jt V 
                       LEFT_SPD--;   
                       RIGHT_SPD--; 
                    }else if(fron_stat[0] ==  LOW && fron_stat[1] == LOW && fron_stat[2] == LOW && fron_stat[3] ==  HIGH && fron_stat[4] ==  HIGH){ //00011                       
                       //LEFT_SPD++;   
                       //RIGHT_SPD--; 
                    }else if(fron_stat[0] ==  HIGH && fron_stat[1] == HIGH && fron_stat[2] == HIGH && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //11100 l r
                       //LEFT_SPD--;   
                       //RIGHT_SPD++;  
                    }else if(fron_stat[0] ==  LOW && fron_stat[1] == LOW && fron_stat[2] == HIGH && fron_stat[3] ==  HIGH && fron_stat[4] ==  HIGH){ //00111  
                       //LEFT_SPD++;   
                      // RIGHT_SPD--; 
                    }else if(fron_stat[0] ==  HIGH && fron_stat[1] ==  HIGH && fron_stat[2] ==  LOW && fron_stat[3] == HIGH && fron_stat[4] ==  HIGH){ //11011 //เส้นทั้งสองข้าง 
                       LEFT_SPD--;   
                       RIGHT_SPD--;                  
                    }else if(fron_stat[0] ==  HIGH && fron_stat[1] ==  HIGH && fron_stat[2] == HIGH && fron_stat[3] == HIGH && fron_stat[4] ==  HIGH){ //11111
                       LEFT_SPD--;   
                       RIGHT_SPD--;
                    }else if(fron_stat[0] ==  HIGH && fron_stat[1] ==  LOW && fron_stat[2] == HIGH && fron_stat[3] == LOW && fron_stat[4] ==  HIGH){ //10101
                       break;
                    }

                    //Serial.print("runing");                   
                   
                    //point counter
                    if(!fron_stat[2] != last){ //read 0 white 1 black  0
                         if(!fron_stat[2]){
                            //delay(20);
                            //Renew_Location();
                            CAN_STEP--;
                            pt_cnt++;
                            COMMANDS_COUNTER++;
                            Serial.println(pt_cnt);
                            //Access_Req_Task("&REQ=report&STATUS="+BOT_STATUS+"&JOBID="+JOBID+"&LOCATION="+CURRENT_LOCATION + COMMANDS_COUNTER); //slow

                         }
                         last = !last;
                         
                         if(pt_cnt == point){  //stop point slow down
                            LEFT_SPD = MIN_SPD;
                            RIGHT_SPD = MIN_SPD;
                         }
                         delay(10);
                     }
                     
                  
                 }
           
           if(pt_cnt > point){
                     readSensor();
                     Backword_SPD(LEFT_SPD,RIGHT_SPD);
                     delay(20);
                      if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] == HIGH && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01100 handle line
                           RIGHT_SPD -= STEP_SPD; 
                           LEFT_SPD -= STEP_SPD; 
                           RotateRight_SPD(LEFT_SPD,RIGHT_SPD);
                           delay(5);
                      }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] == HIGH && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00110
                           RIGHT_SPD -= STEP_SPD;
                           LEFT_SPD -= STEP_SPD;     
                           RotateLeft_SPD(LEFT_SPD,RIGHT_SPD); 
                           delay(5);
                      }if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01000
                           RIGHT_SPD -= STEP_SPD; 
                           LEFT_SPD -= STEP_SPD; 
                           RotateRight_SPD(LEFT_SPD,RIGHT_SPD);
                           delay(5); 
                      }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00010
                           RIGHT_SPD -= STEP_SPD;
                           LEFT_SPD -= STEP_SPD;     
                           RotateLeft_SPD(LEFT_SPD,RIGHT_SPD);
                           delay(5); 
                      }else if(fron_stat[0] ==  HIGH && fron_stat[1] == HIGH && fron_stat[2] == LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //11000 jt V
                           RIGHT_SPD--;
                           LEFT_SPD--;
                      }else if(fron_stat[0] ==  LOW && fron_stat[1] == LOW && fron_stat[2] == LOW && fron_stat[3] ==  HIGH && fron_stat[4] ==  HIGH){ //00011
                           RIGHT_SPD--;
                           LEFT_SPD--;
                      }
                      
               
                      //point counter
                      if(point_ctr != last){ //if(!fron_stat[2] != last){
                           if(point_ctr){
                              //delay(20);
                              pt_cnt--; COMMANDS_COUNTER--;
                           }
                           last = !point_ctr; ///!fron_stat[2]
                       }
                       //Serial.println(pt_cnt);
                 }
           

          if(STOP_POINT == (CURRENT_LOCATION +":"+ COMMANDS_COUNTER)){
                //MotorStop()
                CMD_BREAKER = true;
          }

          if(point_ctr == HIGH && CMD_BREAKER) break;
          
          if(pt_cnt == point){ //if counter is equal wait center point
             if(point_ctr == HIGH) break; //read white 0 black 1    
          }
          //int tell=0;
          //if(COMMANDS_COUNTER == 0) tell=1;
          Access_Req_Task("&REQ=report&STATUS="+BOT_STATUS+"&JOBID="+JOBID+"&LOCATION="+CURRENT_LOCATION +":"+ (COMMANDS_COUNTER == 0 ? 1:COMMANDS_COUNTER)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" ) ); //slow
          if(iterator >= 60000) iterator = 0;
          

          
        }//loop
  }//end else
  
   MotorStop();
   delay(500);
}//endfunc



void Moveback(int point)
{
 
  int pt_cnt = 0;
  bool last = false;
  
  if(point == 0){
          //Access_Req_Task("&REQ=report&STATUS=PULSE&JOBID="+JOBID+"&LOCATION="+BOT_NAME); //slow
          //delay(3000);
          while(true){ 
                    
                   if(CAN_STEP == 0){
                        MotorStop();
                        while(CAN_STEP == 0){
                            Access_Req_Task("&REQ=canstep&JOBID="+JOBID+"&STATUS=PULSE&LOCATION="+CURRENT_LOCATION +":"+ (COMMANDS_COUNTER == 0 ? 1:COMMANDS_COUNTER)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" )); //slow
                            delay(1000);
                        }
                    }
                    
                    readSensor();
                    if(fron_stat[0] == LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //0000
                       break;
                    }  
                    Backword_SPD(MIN_SPD,MIN_SPD);
                    delay(1);

          }

          
    }else{
      
           while(pt_cnt < point){ //ใช้ในกรณีนับจุดตามเส้น
                 Backword_SPD(LEFT_SPD,RIGHT_SPD); 
                 readSensor();  
                 //ขาด4แยก
                if(fron_stat[0] == LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //00000
                     LEFT_SPD += STEP_SPD*2;   
                     RIGHT_SPD += STEP_SPD*2;
                     Backword_SPD(LEFT_SPD,RIGHT_SPD);  
                  }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] == HIGH && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //00100
                     LEFT_SPD += STEP_SPD*2;   
                     RIGHT_SPD += STEP_SPD*2;
                     Backword_SPD(LEFT_SPD,RIGHT_SPD);
                  }else if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] == HIGH && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01100
                     LEFT_SPD -= STEP_SPD;   
                     RIGHT_SPD -= STEP_SPD;
                     RotateRight_SPD(LEFT_SPD,RIGHT_SPD);
                     
                  }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] == HIGH && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00110
                     LEFT_SPD -= STEP_SPD;   
                     RIGHT_SPD -= STEP_SPD;
                     RotateLeft_SPD(LEFT_SPD,RIGHT_SPD); delay(1); 
                     
                  }if(fron_stat[0] ==  LOW && fron_stat[1] == HIGH && fron_stat[2] ==  LOW && fron_stat[3] ==  LOW && fron_stat[4] ==  LOW){ //01000
                     LEFT_SPD -= STEP_SPD;   
                     RIGHT_SPD -= STEP_SPD;
                     RotateRight_SPD(LEFT_SPD,RIGHT_SPD); delay(1);
                      
                  }else if(fron_stat[0] ==  LOW && fron_stat[1] ==  LOW && fron_stat[2] ==  LOW && fron_stat[3] == HIGH && fron_stat[4] ==  LOW){ //00010
                    
                     LEFT_SPD -= STEP_SPD;   
                     RIGHT_SPD -= STEP_SPD; 
                     RotateLeft_SPD(LEFT_SPD,RIGHT_SPD); delay(1);
                     
                  }else if(fron_stat[0] ==  HIGH && fron_stat[1] ==  HIGH && fron_stat[2] ==  LOW && fron_stat[3] == HIGH && fron_stat[4] ==  HIGH){ //11011 //เส้นทั้งสองข้าง
                     LEFT_SPD--;   
                     RIGHT_SPD--;
                  }else if(fron_stat[0] ==  HIGH && fron_stat[1] ==  HIGH && fron_stat[2] == HIGH && fron_stat[3] == HIGH && fron_stat[4] ==  HIGH){ //11111
                     LEFT_SPD--;   
                     RIGHT_SPD--;
                  }
                  
                  
                  
                  //point counter
                  if(point_ctr != last){
                       if(!point_ctr){
                          //delay(30);
                          pt_cnt++; COMMANDS_COUNTER--;
      
                       }
                       last = point_ctr;
                   }
                   //Serial.println(pt_cnt);
      
                   
                   //COMMANDS_COUNTER = 0;
                   // CURRENT_LOCATION = location;
                  //request("&REQ=report&STATUS=PULSE&JOBID="+JOBID+"&LOCATION="+CURRENT_LOCATION + COMMANDS_COUNTER); //slow
          }
        
    }
    
    MotorStop();
    
}

void TurnLeft(int cross)
{ 
  
    int line_counter = 0; 
    bool last = false;
    Access_Req_Task("&REQ=report&STATUS=TURNLEFT&JOBID="+JOBID+"&LOCATION="+(CURRENT_LOCATION +":"+ COMMANDS_COUNTER)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" )); //job2
    
    if(cross == 0){ //ใช้ในกรณีข้างหน้าตัวเองไม่มีเส้น

        while(true){
            RotateLeft_SPD(MIN_SPD,MIN_SPD);
            readSensor();
            if(fron_stat[3] == HIGH && fron_stat[4] == LOW){ break; }
            delay(5);
        }
        
    }else if(cross >= 1){ //ใช้ในกรณีข้างหน้ามีเส้นหรือยู่บนเส้น
      
        while(true){
            RotateLeft_SPD(MIN_SPD,MIN_SPD);
            readSensor();  
            if(fron_stat[2] != last){
                 if(fron_stat[2]){
                    line_counter++;
                 }
                 last = fron_stat[2];
             }
             if(line_counter == cross+1 && fron_stat[2] ==  HIGH && fron_stat[4] ==  LOW){ break; } //4เลียวไม่สุด 
             if(line_counter > cross+1){ //> != 0
                TurnRight( line_counter - cross+1 );  
                line_counter -= cross+1;           
             }
             delay(5);
        }
    }
    
    MotorStop();
}


void TurnRight(int cross)
{
  
    int line_counter = 0; 
    bool last = false;
    Access_Req_Task("&REQ=report&STATUS=TURNRIGHT&JOBID="+JOBID+"&LOCATION="+(CURRENT_LOCATION +":"+ COMMANDS_COUNTER)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" )); //slow
   
    if(cross == 0){
        while(true){
            RotateRight_SPD(MIN_SPD,MIN_SPD);
            readSensor();
            if(fron_stat[0] ==  LOW && fron_stat[1] ==  HIGH ){ break; }
            delay(5);
        }
        
    }else if(cross >= 1){
      
        while(true){
            RotateRight_SPD(MIN_SPD,MIN_SPD);
            readSensor();  
            if(fron_stat[2] != last){
               if(fron_stat[2]){
                  line_counter++;
               }
               last = fron_stat[2];
             }
            if(line_counter == cross+1 && fron_stat[0] ==  LOW && fron_stat[1] ==  HIGH ){ break; }
            if(line_counter > cross+1){
              TurnLeft( line_counter - cross+1 );
              line_counter -= cross+1;                      
            }
            delay(5);
        }
    }
    
     MotorStop();
}


void readSensor()
{
   for(int i=0;i<5;i++){
        fron_stat[i] = digitalRead(sen_pin[i]); //face detaction point
    }  
    //int current_stat =  digitalRead(ctr);  //isr
    point_ctr = digitalRead(ctr);//center point  //isr
    //Serial.print("ctr:");
   //Serial.println(point_ctr);
}


//---------------------------------------------------hands----------------------------------------------------------------------------------------------
bool setItem(uint8_t Floor)
{
   //prepare
   //for ward
   //pass
   //back
   //end line      
   //-----------
    //turn
   Access_Req_Task("&REQ=report&STATUS=PASSINGITEM&JOBID="+JOBID+"&LOCATION="+(CURRENT_LOCATION +":"+ COMMANDS_COUNTER)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" )); 
   prepare_Moveto();
   if(! hand_move(Floor,true) ){  //move if error resethand move hand again
      set_hand(); 
      hand_move(Floor,true);
    }
   MoveTo(0);
   if(hand_move(Floor,false));
   Moveback(0);
    //turn
   Access_Req_Task("&REQ=job_success&JOBID="+(JOBID != "" ? JOBID:JOBID_2)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" )); //report job success
   delay(2000);
   JOBID = "";
 }

 bool getItem(uint8_t Floor)
 {
   //turn
   Access_Req_Task("&REQ=report&STATUS=LOADING_ITEM&JOBID="+JOBID+"&LOCATION="+(CURRENT_LOCATION +":"+ COMMANDS_COUNTER)+"&HASJOB2="+ (DATA_COMMANDS_2 == "" ? "0":"1" )); 
   prepare_Moveto();
   if(! hand_move(Floor,false) ){ 
      set_hand(); 
      hand_move(Floor,false); 
    }
   MoveTo(0); //move to end
   if(hand_move(Floor,true)){}
   if(!ITEM_VAL){  
      Access_Req_Task("&REQ=error&ERROR=ITEM_NOT_FOUND&STATUS=LOADING_ITEM&JOBID="+JOBID+"&LOCATION="+(CURRENT_LOCATION +":"+ COMMANDS_COUNTER)); 
    }
   Moveback(0);
   
   //turn
 }

//auto floor true get : false pass
bool hand_move(uint8_t Floor , bool get_pass)
{
   //fillter floor if(Floor >2) return;
   if(Floor > 2) return false; //maxfloor
   
   int8_t  line_updown;
   bool updown; 
   
   if(get_pass){
      line_updown = Floor*2;  
   }else{
      line_updown = Floor*2 - 1;  //รับของ 1ชั้นมี2มาค ดังนั้นชั้นั้น-1คือรับค่อยยกขึ้น1 = ชั้นนั้น
   }

  hand_read_sensor();
  if(TOP_SW_VAL || END_SW_VAL){
      return false;
   }
  
      while(true){  
        if(LINE_CNT  == line_updown){
           delay(50);
           break;
          }else if(LINE_CNT  < line_updown){
            UP(); updown = true;
            //floor_counter(true);  //read sersor
          }else if(LINE_CNT  > line_updown){
            DONW(); updown = false;
            //floor_counter(false);
          }

        hand_read_sensor();
        if(SENSOR_VAL !=  LINE_MARK){ //bkack 1
           if(SENSOR_VAL){ //low 0 white 0^=1
               if(updown){
                  LINE_CNT++;
                }else{
                  LINE_CNT--;
                } 
           }
           LINE_MARK = SENSOR_VAL;
           
        }
        
        if(TOP_SW_VAL || END_SW_VAL){
            //break;
            STOPHAND();
            return false;
        }
        //if(ENDTOP_VAL == HIGH){STOP(); set_hand(); LINE_CNT = 0; //recounter  return;}  //if error save hand
        //printSensor();
        //Serial.println();
        //Serial.print(LINE_CNT); Serial.print(">updown:"); Serial.print(line_updown);  Serial.print(">line mark:"); Serial.print(SENSOR_VAL !=  LINE_MARK); 
      }
      
   STOPHAND();
   return true;
}

/*
void floor_counter(bool updown)
{
  //true:up++ , false:down--
  hand_read_sensor();
  if(!SENSOR_VAL !=  LINE_MARK){
     if(!SENSOR_VAL){ // 0 white 0^=1
         if(updown){
            LINE_CNT++;
          }else{
            LINE_CNT--;
          }
          LINE_MARK = !SENSOR_VAL;
     }
  }

}
*/

void set_hand()
{
  
  //if(firstTime){  //สวิตจะต้องมี 2 ตัว บนล่างเพื่อตรวจสอบ แต่เรามีแค่1ไม่สามารถช่วยไได้  100%
    hand_read_sensor();
    delay(50);
    hand_read_sensor();
    delay(50);
    if(TOP_SW_VAL)
      while(TOP_SW_VAL){  //ดึงลงหากติดยอด
        Serial.println("slide down1");
        hand_read_sensor();
        DONW();
        delay(50);
      }
    hand_read_sensor();
    if(END_SW_VAL)
      while(END_SW_VAL){  //ดึงลงหากติดล่าง
        Serial.println("slide up1");
        hand_read_sensor();
        UP();
        delay(50);
      }
    delay(200);
    STOPHAND(); //หยุด
    
  //}
  //set
  hand_read_sensor();
  while(!END_SW_VAL){  //ดึงลงตำแหน่งเริ่มต้น
    Serial.println("slide down2");
     hand_read_sensor();
     DONW();
     delay(50);
  }
  delay(200);
  hand_read_sensor();
  while(END_SW_VAL){  //ดึงลงตำแหน่งเริ่มต้น
     Serial.println("slide up2");
     hand_read_sensor();
     UP();
     delay(20);
  }
  delay(200);
  STOPHAND(); //หยุด
  LINE_CNT = 0;
}

void hand_read_sensor()
{
  
  SENSOR_VAL = digitalRead(SENSOR_PIN);
  ITEM_VAL  = digitalRead(ITEM_PIN);
  byte endtop_sw = IOexpanderRead(PCF8574_ADDR);  //0 = high 1 = low
  Serial.println(endtop_sw,BIN);
  TOP_SW_VAL = (endtop_sw & TOP_SW_PIN) == TOP_SW_PIN ? true : false ; 
  END_SW_VAL = (endtop_sw & END_SW_PIN) == END_SW_PIN ? true : false ; 
  delay(1);
  print_sensor();
}

void print_sensor()
{
  Serial.print("SENSOR_VAL: ");
  Serial.println(SENSOR_VAL);
  Serial.print("TOP_SW_VAL: ");
  Serial.println(TOP_SW_VAL);
  Serial.print("END_SW_VAL: ");
  Serial.println(END_SW_VAL);
  Serial.print("ITEM_VAL: ");
  Serial.println(ITEM_VAL);
}

//PCF_8574 = (PCF_8574  & 0x0f) | 0x1;
void UP()
{
  PCF_8574 = (IOexpanderRead(PCF8574_ADDR) & 0x0f) | 0x10;
  IOexpanderWrite(PCF8574_ADDR, PCF_8574);
}

void DONW()
{
  PCF_8574 = (IOexpanderRead(PCF8574_ADDR) & 0x0f) | 0x20;
  IOexpanderWrite(PCF8574_ADDR, PCF_8574);
}

void STOPHAND()
{  //write 0 logic low 0v
  PCF_8574 =  (IOexpanderRead(PCF8574_ADDR) & 0x0f) | 0x30;
  //Serial.println(PCF_8574);//0
  IOexpanderWrite(PCF8574_ADDR, PCF_8574);
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
