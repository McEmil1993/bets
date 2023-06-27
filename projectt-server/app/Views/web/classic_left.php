<?php //$imageBasePath = config(App::class)->IMAGE_SERVER.'/'.config(App::class)->imagePath;?>

<?php

    // var_dump($locationGameList);
    // var_dump($locationGameList[$sport['id']]['location_all']) ;
    // var_dump($locationGameList['154914']['location_all']) ;
    
?>

<div class="sports_wide_left">
    <div class="search">
        <ul>
            <li>
                <input name="league_name" id="league_name" type="text" class="input_search" placeholder="국가 및 리그명">
            </li>
            <li style="float:right;"><a onclick="searchLeague()"><span class="search_btn">검색</span></a></li>
        </ul>
    </div>
    <div class="con_box_left">
        <ul class="dropdown">
            <li>
                <div class="left_list1" onclick="getRealTimeGameLiveScoreList(0, 0)">
                    <span class="menu_left">
                        <img src="/assets_w/images/icon01.png" width="18">&nbsp;&nbsp;&nbsp;전체보기
                    </span>
                    <span class="menu_right">
                        <span class="menu_right_box realTimeTotalCnt"><?= $realTimeTotalCnt ?></span></span>
                    </span>
                </div> 
            </li>
            <?php foreach ($sports as $key => $sport) { ?>
                <li class="menu1">
                    <a href="#">
                        <div class="left_list1" onclick="getRealTimeGameLiveScoreList(<?=$sport['id'].''?>, 0)">
                            <span class="menu_left">
                                <img src="<?=$imageBasePath.'/sports/'?>/icon_game<?=$sport['id']?>.png" width="18">&nbsp;&nbsp;&nbsp; 
                                <?= $sport['name']?>
                            </span>
                            <span class="menu_right">
                                <span class="menu_right_box" id="sports_id_<?= $sport['id']?>" >0</span>
                            </span>
                        </div>
                        <ul>
                            <li>
                                <a href="#">
                                    <?php if (isset($locationGameList[$sport['id']]['location_all'])): ?>
                                    <?php
                                        /* 지역 내림 차순 정리하기 */
                                        $tempList = [];
                                        foreach ($locationGameList[$sport['id']]['location_all'] as $lKey => $lItems) {
                                            array_push($tempList, [
                                                    'id'   => $lItems[0]['fixture_location_id'],
                                                    'name' => $lItems[0]['fixture_location_name'],
                                                    'image_path' => $lItems[0]['fixture_location_image_path'],
                                                    'cnt' => count($lItems),
                                            ]);
                                        }
                                        usort($tempList, function ($a, $b) {
                                            if ($a['cnt'] == $b['cnt']) {
                                                return 0;
                                            }
                                            return $a['cnt'] > $b['cnt'] ? -1 : 1;
                                        });
                                    ?>
                                    <?php foreach ($tempList as $key => $tItems) { ?>

                                        <span class="left_list1_in" id="<?= $sport['id'] ?>"><?= $tItems['name'] ?>
                                            <span class="menu_right_box"><?= $locationFixtureCount[$sport['id']][$tItems['id']] ?></span>
                                        </span>

                                        <ul>
                                            <li>
                                                <a href="#">
                                                    <?php
                                                    /* 리그 내림 차순 정리하기 */
                                                    $tempList = [];
                                                    foreach ($locationGameList[$sport['id'].'']['location_'.$tItems['id']] as $lKey => $lItems) {
                                                        array_push($tempList, [
                                                            'id'   => $lItems[0]['fixture_league_id'],
                                                            'name' => $lItems[0]['fixture_league_name'],
                                                            'cnt' => count($lItems),
                                                        ]);
                                                    }
                                                    //
                                                    usort($tempList, function ($a, $b) {
                                                        if ($a['cnt'] == $b['cnt']) {
                                                            return 0;
                                                        }
                                                        return $a['cnt'] > $b['cnt'] ? -1 : 1;
                                                    });
                                                    ?>
                                                    <?php foreach ($tempList as $llKey => $leagues): ?>
                                                        <span class="left_list1_in"><?= $leagues['name'] ?>
                                                            <span class="menu_right_box"><?= $locationFixtureCount[$sport['id']][$leagues['name']] ?></span>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </a>
                                            </li>
                                        </ul>

                                    <?php } ?>
                                    <?php endif; ?>
                                </a>
                            </li>
                        </ul>
                    </a>
                </li>
            <?php } ?>

        </ul>
    </div>          
		<script src="/assets_w/js/tendina.min.js"></script>
		<script>
			  $('.dropdown').tendina({
				// This is a setup made only
				// to show which options you can use,
				// it doesn't actually make sense!
				animate: true,
				speed: 300,
				onHover: false,
				hoverDelay: 300,
				//activeMenu: $('#deepest'),
				openCallback: function(clickedEl) {
				  console.log('Hey dude!');
				},
				closeCallback: function(clickedEl) {
				  console.log('Bye dude!');
				}

			  })
		
			$(document).ready(function(){
			$("li.menu1").click(function(){
				$(this).addClass("on");
				$(this).siblings().removeClass("on");
			});
		});
		</script>   	
</div>
<!-- sports_wide_left -->
 
<script type="text/javascript">

    let postData = {};
    let myParam = '?sports_id=<?=isset($_GET['sports_id'])? $_GET['sports_id'] : 0 ?>&location_id=<?=isset($_GET['location_id'])? $_GET['location_id'] : 0 ?>&league_id=<?=isset($_GET['league_id'])? $_GET['league_id'] : 0 ?>';




    $(document).ready(function() {

    // 드롭다운 이벤트
    $('.dropdown').tendina({
        animate: true,
        speed: 200,
        onHover: false,
        hoverDelay: 300,
        //activeMenu: $('#deepest'),
        openCallback: function(clickedEl) {
            console.log('Hey dude!', clickedEl);
        },
        closeCallback: function(clickedEl) {
            console.log('Bye dude!', clickedEl);
        }
    });

        // 메뉴 클릭
        /*$("li").click(function(){
            $(this).addClass("on");
            $(this).siblings().removeClass("on");
        });*/

    });

    // 종목
    /*function getListAjax(){
        $.ajax({
            url: '/sports/getListAjax' + myParam,
            type: 'post',
            data: postData,
        }).done(function (response) {
            console.log('종목 response', response);
            // $(".example1").html(response);
            // initDropDown();
        }).fail(function (error) {
            console.log(error);
        });
    }*/
</script>