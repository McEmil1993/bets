// 경기출력
function sportsFixtureDisplay(firstFixtureid, firstFixtureTime, firstFixtureTeam1, firstFixtureTeam2){
    html = "<img src='/assets_w/images/main_live.png'> 잠시 후 "+firstFixtureTime+" <span class='font06'>"+firstFixtureTeam1+" <img src='/assets_w/images/vs.png' width='30'> "+firstFixtureTeam2+"</span>";
    $(".sports_list_title4").html(html);
    //console.log('firstFixtureid : '+firstFixtureid);
    if(!isMobile){
        openBetData(firstFixtureid);
    }
}

// 상단 스포츠 항목 선택표시
function choiceSportsImage(sports_id){
    let className = '';
        switch (sports_id){
            case 6046:
                className = 'sports_ck2';
                break;
            case 48242:
                className = 'sports_ck3';
                break;
            case 154914:
                className = 'sports_ck4';
                break;
            case 154830:
                className = 'sports_ck5';
                break;
            case 35232:
                className = 'sports_ck6';
                break;
            case 687890:
                className = 'sports_ck7';
                break;
            case 154919:
                className = 'sports_ck8';
                break;
            case 54094:
                className = 'sports_ck9';
                break;
            default :
                className = 'sports_ck1';
                break;
        }
        $('#sports_img_'+sports_id).addClass(className);
}