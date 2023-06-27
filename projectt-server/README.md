# Project  - T
- 해당 프로젝트는 php7.3/CI4/docker 로 개발 되었습니다.

## 1. 최초 구성 방법 및 유지보수 
### 1.1 최초 구성 (docker 설치)
#### 1.1.1 window, mac, ...
아래 docker 사이트에서 각 운영체제에 맞는 docker 를 설치 (docker-compose 도 같이 설치됩니다. )  
    - https://docs.docker.com/compose/install/
### 1.1.2 ubuntu
```
$ sudo apt-get update
$ sudo apt-get install \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg-agent \
    software-properties-common
$ curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
$ sudo apt-key fingerprint 0EBFCD88
$ sudo add-apt-repository \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable"

$ sudo apt-get update
$ sudo apt-get install docker-ce docker-ce-cli containerd.io
```   
### 1.2 Project - T 소스 다운로드 
### 1.3 docker 실행 / 중지 
- cd tt_project/projectt-server (project - T  프로젝트 폴더 내 docker-compose.yml 위치로 이동)  
- docker-compose build (설치 - 최초 한번 만 실행합니다.) 
- docker-compose up -d (실행)
- docker-compose down (중지)

### 1.4 php composer
* 이미 모두 설치되어 있는 프로젝트를 Clone 받은 경우 이 과정은 생략해도 됩니다
#### 1.4.1 composer install
#### 1.4.2 composer update
#### 1.4.3 composer require
#### 1.4.4 업데이트 실패할 경우 --ignore-platform-reqs

### 1.5 DB Connect
* test-db 접속 시 구동하는 PC의 IP주소를 허용해야 접속 가능 합니다. 
* 허용 문의는 baran0627@gmail.com 으로 주세요.
#### DB 정보
    AWS - b4ahlma9@naver.com / ysh462462!
    -host: test-db.cub8bts0p13r.ap-southeast-1.rds.amazonaws.com
    -user: admin
    -pass: admin1234
    -권한
        - ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '12341234';

### 1.6 운영   
#### 1.6.1 리얼 서버 정보 
- 210.175.73.148
- root / 빼빼로@123
- (서버 재시작) 도커를 사용하기 떄문에 1.3 의 실행, 중지만 반복하여 사용합니다. PHP라서, 소스 반영해도 따로 재시작은 안해도 됩니다.
- (소스 반영 방법) git 연동되어 있어, 해당 서버 접속 후 tt_project 내에서 pull 받으면 됩니다.
- (크론 적용 방법) crontab -e 실행 후 경로는 다음과 같이 기재 (php /{경로}/tt_project/projectt-server/public/index.php LSportsInitController initData) 

#### 1.6.2 소스 코드 주요 정보
프레임워크는 코드이그나이터4 사용 (http://ci4doc.cikorea.net/)
- 1. 게임/배팅 관련 주요 Controllers
    - LSportsInitController: API 에서 조회한 데이터를 로드 및 저장 시킴. API 에서 조회한 데이터 수정시 여기서 작업함 (주석 참고), 
    - RealTimeController: 실시간에서 사용. 메인화면 Ajax 도 실시간 정보라 여기서 일부 사용.
    - SportsController: 스포츠에서 사용. 
    - BetController: 배팅할때 사용
    
- 2. view 
    - game01.php: 실시간
    - game02.php: 스포츠
    - index.php: 메인
    
- 3. Util
    - BetDataUtil: 실제로 데이터를 가공하여 View 에 출력하는 곳입니다. 어드민에서 사용하는 GameDataUtil 과 동일합니다. mergeBetData, mergeBetData2 개의 함수가 있는데, object, array 인지에 따라 1, 2 를 불러 사용하면 됩니다.
    - 나머지 Util 은 모두 상태값을 display 값으로 바꿔주는 Util 입니다. DateTimeUtile 은 사용하지 않습니다. (시간 관련 코드 작성시 참고용으로만 사용)
    
- 기타 유의
    - 리얼 서버에서 Config/App.php 의 $baseURL 은 서버의 도메인 정보(또는 아이피)여야 합니다. 자신의 local 소스를 반영하면 안됩니다. 

### 기타
- LSports IP 허용 여부 체크하기 
- projectt-server/writable 및 아래 폴더 모두 chmod 777 -R 권한으로 변경 
- projectt-server/public 및 아래 폴더 모두  chmod 777 - R 권한으로 변경 

## 2. 외부 API
* 프로젝트 실행 시 아래 LSports 사이트에서 구동하는 PC의 IP 주소를 허용해야합니다.
### 2.1 LSports
#### 2.1.1 계정 정보 
    http://client.lsports.eu/Account/Login?ReturnUrl=%2F
    아이디 : tombow3455@gmail.com 
    비번 : 43SDLK4sd3 
    GUID : a51eae0f-1c1d-453a-84eb-3219061e418d 
    Package : 3065 
    inplay - Package : 3066

## 3. 데이터 코드값
### 3.1 현재 프로젝트에서 사용하는 코드값 
#### 3.1.1 스포츠 ID
    - 축구: 6046
    - 농구: 48242
    - 배구: 154830
    - 야구: 154914
    - 권투: 154919
    - 아이스 하키: 35232
    - 테니스: 35232 
    - 미식축구: 131506
    - 핸드볼: 35709
    - UFC: ????
    - E게임: 687890
    - 배드민턴: 1149093
    - 탁구: 265917
    - 경마: 687888
    - 골프: 687889

#### 3.1.2 마켓 ID
아래는 작업 중 임의로 구분지어 놓은 값이며, 실제 운영중에 알맞은 값으로 구분지어야 합니다.
lsports_markets.bet_group 에서 실시간/스포츠 구분지어 저장하면 됩니다.

    - https://docs.google.com/spreadsheets/d/1rJ06QvostS0WJBH_nEb-zMHGt5AUkYcCqXF0YxkW3Mg/edit#gid=551742047
    - 스포츠 (기본): 1, 2, 3, 7, 52, 101, 102
    - 스포츠 (농구): 16, 28, 220, 221, 226, 342, 1050, 1328, 1332
    - 실시간 (기본): 17, 21, 28, 34, 35, 41, 42, 42, 43, 44, 45, 46, 47, 48, 49, 53, 63, 64, 65, 66, 67, 77, 113, 202, 203, 204, 205, 206, 211, 220, 221, 226,342, 348, 349, 352, 353, 464, 866
    - 실시간 (농구): 21, 53, 63, 64, 69, 70, 71, 77, 153, 155, 202, 282, 390

### 3.2 Status (상태값)
#### 3.2.1 Fixture/Scoreboard 'Status' (경기에 대한 상태값)
    Id	Value	            Description
    1	Not started yet	    The event has not started yet
    2	In progress	        The event is live
    3	Finished	        The event is finished
    4	Cancelled	        The event has been cancelled
    5	Postponed	        The event has been postponed
    6	Interrupted	        The event has been interrupted
    7	Abandoned	        The event has been abandoned
    8	Coverage lost	    The coverage for this event has been lost
    9	About to start	    The event has not started but is about to. NOTE: This status will be shown up to 30 minutes before the event has started
        
#### 3.2.2 Bet Status (배팅에 대한 상태값)
    -1 Open 베팅이 열려 있습니다 (베팅 가능)
    -2 Suspended 베팅이 중단되었습니다 (베팅을 걸 수 없습니다).
    -3 Settled 베팅이 정산 됨 (결과) – 정산이 결정됩니다 (추가 정보는 정산 열거 참조).

### 3.3 Periods (게임 값)
#### 3.3.1 General (공통) 
    General (relevant for multiple sports)
    Period Id	Description
    -1	NSY
    80	Break Time
    99	None
    
#### 3.3.2 Football
    Period Id	Description
    10	1st Half
    20	2nd Half
    30	Overtime 1st Half
    35	Overtime 2nd Half
    50	Penalties
    100	Full time
    101	Full time after overtime
    102	Full time after penalties
    
#### 3.3.4 Baseball
    Period Id	Description
    1	1st Inning
    2	2nd Inning
    3	3rd Inning
    4	4th Inning
    5	5th Inning
    6	6th Inning
    7	7th Inning
    8	8th Inning
    9	9th Inning
    40	Extra Innings
    62	Error
    100	Full time
    101	Full time after extra time    
  
#### 3.3.3 Basketball
    Period Id	Description
    1	1st Quarter
    2	2nd Quarter
    3	3rd Quarter
    4	4th Quarter
    40	Overtime
    100	Full time
    101	Full time after overtime
    
#### 3.3.5 Volleyball
    Period Id	Description
    1	1st Set
    2	2nd Set
    3	3rd Set
    4	4th Set
    5	5th Set
    50	Golden Set
    100	Full time
    
        
### 3.4 Statistics/Incidents
#### 3.4.1 Football
    Incident Id	Description
    1	Corners
    6	Yellow cards
    7	Red cards
    8	Penalties
    9	Goal
    10	Substitutions
    24	Own goal
    25	Penalty goal
    40	Missed penalty
    
#### 3.4.2 Tennis
    Incident Id	Description
    20	Aces
    21	Double faults
    34	First serve wins
    
#### 3.4.3 Basketball
    Incident Id	Description
    12	Fouls
    28	Two points
    30	Three points
    31	Time outs
    32	Free throws
    
#### 3.4.4 Ice Hockey
    Incident Id	Description
    8	Penalties
    
#### 3.4.5 Baseball
    Incident Id	Description
    33	Hits
        