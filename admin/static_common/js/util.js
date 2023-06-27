/*
 * 날짜포맷 yyyy-MM-dd 변환
 */
function getFormatDate(date){
    var year = date.getFullYear();
    var month = (1 + date.getMonth());
    month = month >= 10 ? month : '0' + month;
    var day = date.getDate();
    day = day >= 10 ? day : '0' + day;
    return year + '-' + month + '-' + day;
}

/*
 * 날짜포맷 MM-dd 변환
 */
function getFormatDateMonth(date){
    date = new Date(date);
    let month = (1 + date.getMonth());
    month = month >= 10 ? month : '0' + month;
    let day = date.getDate();
    day = day >= 10 ? day : '0' + day;
    return month + '-' + day;
}

// 몇일전, 몇일후 날짜 구하기
function getAddDate(date, days){
    date = date.split("-");
    var beforeDate = new Date();
    beforeDate.setDate(beforeDate.getDate()+days);
    var y = beforeDate.getFullYear();
    var m = beforeDate.getMonth() + 1;
    var d = beforeDate.getDate();
    if(m < 10) { m = "0" + m; }
    if(d < 10) { d = "0" + d; }
    return beforeDate = y + "-" + m + "-" + d;
}

// 요일구하기
function getDayOfWeek(date){
    let week = ['일', '월', '화', '수', '목', '금', '토'];
    return week[new Date(date).getDay()];
}

// 배당리셋
const fnResetBetPrice = function(type, pre_url, real_url) {
    let resetBetType = $("#resetBetType").val();    
    let url = '';
    let str_msg = '';
    
    //console.log('리셋하였습니다.');
    //return;
    
    // type 1이면 프로바이더 변경(배당도 업데이트가 된다.)
    if(resetBetType == 1){
        url = pre_url+'?type='+type;
        str_msg = '스포츠';
    }else{
        url = real_url+'?type='+type;
        str_msg = '실시간';
    }
    console.log(url);
    
    let nowTime = new Date();
    let obj = JSON.parse(sessionStorage.getItem('initDataTime'));
    if(null !== obj){
        let lastTimeStamp = obj.timestamp + 60000;
        if(nowTime.getTime() < lastTimeStamp){
            //console.log('nowTimeStamp : ' + nowTime.getTime());
            //console.log('lstTimeStamp : ' + lastTimeStamp);
            alert('마지막 호출후 1분이 되지 않았습니다.');
            return;
        }
    }
    
    str_msg = str_msg + ' 배당리셋하시겠습니까?';
    let result = confirm(str_msg);
    if (result) {
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: url,
            data: {},
            success: function (result) {
                /*if (result['retCode'] == "1000") {
                    alert('리셋하였습니다.');
                    window.location.reload();
                    return;
                } else {
                    alert(result['retMsg']);
                    return;
                }*/
                console.log('리셋하였습니다.');
            },
            error: function (request, status, error) {
                /*if(request.status != 200){
                    console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    alert('리셋에 실패하였습니다.');
                }
                sessionStorage.removeItem('initDataTime');
                sessionStorage.setItem('initDataTime', JSON.stringify({timestamp: nowTime.getTime()}));
                alert('리셋하였습니다');*/
                return;
            }
        });
    } else {
        return;
    }
    
    sessionStorage.removeItem('initDataTime');
    sessionStorage.setItem('initDataTime', JSON.stringify({timestamp: nowTime.getTime()}));
    alert('리셋요청 하였습니다');
};

//yyyyMMddhhmmss 형식의  string 데이터 반환 ( EX : 20210324112500 )
function getCurrentDate()
{
    var date = new Date();
    var year = date.getFullYear().toString();

    var month = date.getMonth() + 1;
    month = month < 10 ? '0' + month.toString() : month.toString();

    var day = date.getDate();
    day = day < 10 ? '0' + day.toString() : day.toString();

    var hour = date.getHours();
    hour = hour < 10 ? '0' + hour.toString() : hour.toString();

    var minites = date.getMinutes();
    minites = minites < 10 ? '0' + minites.toString() : minites.toString();

    var seconds = date.getSeconds();
    seconds = seconds < 10 ? '0' + seconds.toString() : seconds.toString();

    return year + month + day + hour + minites + seconds;
}

// 엑셀 ArrayBuffer 만들어주는 함수
function s2ab(s) { 
    var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
    var view = new Uint8Array(buf);  //create uint8array as viewer
    for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
    return buf;    
}

let excelHandler = {
        /*getExcelFileName : function(){
            return 'table-test.xlsx';
        },
        getSheetName : function(){
            return 'Table Test Sheet';
        },
        getExcelData : function(){
            //return document.getElementById('tableData');
            //return [{'상품명':'삼성 갤럭시 s11' , '가격':'200000'}, {'상품명':'삼성 갤럭시 s12' , '가격':'220000'}, {'상품명':'삼성 갤럭시 s13' , '가격':'230000'}];
            //return [['이름' , '나이', '부서'],['도사원' , '21', '인사팀'],['김부장' , '27', '비서실'],['엄전무' , '45', '기획실']];
            return excelData;
        },*/
        // 1:talbe, 2:json, 3:array
        getWorksheet : function(type, excelData){
            if(1 == type){
                return XLSX.utils.table_to_sheet(excelData);
            }else if(2 == type){
                return XLSX.utils.json_to_sheet(excelData);
            }else{
                return XLSX.utils.aoa_to_sheet(excelData);
            }
        }
}

// 회원정보 엑셀 다운로드(결과처리)
function result_excel_file_download(response, dataType, fileName){
    //console.log('response', response);
    //response = JSON.parse(response);
    let excelData = response.data_list;
    
    //alert("환불 처리 목록 엑셀파일이 다운로드 되었습니다.\n다운로드 폴더를 확인하세요.");
    // step 1. workbook 생성
    var wb = XLSX.utils.book_new();

    // step 2. 시트 만들기 
    var newWorksheet = excelHandler.getWorksheet(dataType, excelData);

    // step 3. workbook에 새로만든 워크시트에 이름을 주고 붙인다.  
    XLSX.utils.book_append_sheet(wb, newWorksheet, fileName);

    // step 4. 엑셀 파일 만들기 
    var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});

    // step 5. 엑셀 파일 내보내기 
    saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'member_list.xlsx');
}