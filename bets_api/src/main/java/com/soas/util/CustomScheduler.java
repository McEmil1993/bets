package com.soas.util;

import com.soas.prop.Properties;
import com.soas.service.BatchService;
import com.soas.service.BettingService;
import lombok.extern.log4j.Log4j2;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.scheduling.annotation.Scheduled;
import org.springframework.stereotype.Component;

import java.text.SimpleDateFormat;
import java.time.LocalDate;
import java.time.LocalTime;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;


@Log4j2
@Component
public class CustomScheduler {


    private BettingService bettingService;
    private BatchService batchService;

    @Autowired
    public CustomScheduler (BatchService batchService, BettingService bettingService) {
        this.bettingService = bettingService;
        this.batchService = batchService;
    }


    /* 경기 낙첨 배치 */
    @Scheduled(cron = "0 0/5 * * * *")    /* 5분 주기 */
    public void checkBet() throws Exception {
        log.info("CustomScheduler.checkBet");
        log.info("현재 일자 : " + LocalDate.now());
        log.info("현재 시간 : " + LocalTime.now());

        String batchHistoryIdx = "";
        String isSuccess = "";
        String failLog = "";

        Map<String,Object> params = new HashMap<>();
        params.put("batchName", "checkBet");
        params.put("batchDesc", "배팅 낙첨");
        batchService.saveBatchHistory(params);
        batchHistoryIdx = params.get("BTCH_HIST_IDX").toString();

        try {

            /******* ODDS 낙첨 처리 *******/
            bettingService.modifyDelayOdBet(null);


            /******* KPlay 낙첨 처리 *******/
            List<Map<String,Object>> betList = bettingService.getDelayBetList(null);

            for (Map<String,Object> item : betList) {
                String productId = item.get("prdId").toString();
                String transactionId = item.get("trxId").toString();
                String memberIdx = item.get("mbrIdx").toString();
                Map<String,Object> result = HTTP.requestKplayAPI("results" + "/" + productId + "/" + transactionId);
                int status = Integer.parseInt(result.get("status").toString());
                int type = Integer.parseInt(result.get("type").toString());
                int isCancel = 0;
                if(!CommonUtil.isEmpty(result.get("is_cancel")))
                    isCancel = Integer.parseInt(result.get("is_cancel").toString());
                int winMoney = 0;
                if(!CommonUtil.isEmpty(result.get("payout")))
                    winMoney = Long.valueOf(Math.round(Double.valueOf(result.get("payout").toString()))).intValue();
                int betMoney = 0;
                if(!CommonUtil.isEmpty(result.get("stake")))
                    betMoney = Long.valueOf(Math.round(Double.valueOf(result.get("stake").toString()))).intValue();

                if(status == 1 && type == 1){

                    Map<String,Object> betParams = new HashMap<>();
                    betParams.put("memberIdx", memberIdx);
                    betParams.put("amount", winMoney);
                    betParams.put("transactionId", transactionId);

                    /* 취소 */
                    if(isCancel == 1) {
                        Map<String,Object> cancelParams = new HashMap<>();
                        cancelParams.put("memberIdx", memberIdx);
                        cancelParams.put("amount", betMoney == 0 ? winMoney : betMoney);
                        cancelParams.put("transactionId", transactionId);
                        bettingService.modifyCancelBetting(cancelParams);

                    /* 승리 */
                    } else if (isCancel == 0 && winMoney > 0) {
                        bettingService.modifyWinBetting(betParams);

                    /* 패배 */
                    } else {
                        bettingService.modifyLoseBetting(betParams);
                    }

                /* 미완료된 트랜잭션 */
                /* 10분 이상의 결과 딜레이 시 취소 처리 */
                }
                /*else if (status == 1 && type == 0){
                    SimpleDateFormat sdformat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
                    String regDtmStr = item.get("regDtm").toString();
                    Date regDtm = sdformat.parse(regDtmStr);
                    Date curDtm = new Date();
                    long diffMinutes = (curDtm.getTime() - regDtm.getTime()) / (60 * 1000);
                    if(diffMinutes >= Properties.DELAY_BETTING_LIMIT && betMoney != 0){
                        Map<String,Object> cancelParams = new HashMap<>();
                        cancelParams.put("memberIdx", memberIdx);
                        cancelParams.put("amount", betMoney == 0 ? winMoney : betMoney);
                        cancelParams.put("transactionId", transactionId);
                        bettingService.modifyCancelBetting(cancelParams);
                    }
                }*/
            }

            isSuccess = "Y";
        } catch (Exception e){
            isSuccess = "N";
            failLog = e.getMessage();
            log.error(e);
            e.printStackTrace();
        } finally {
            params.put("batchHistoryIdx", batchHistoryIdx);
            params.put("isSuccess", isSuccess);
            params.put("failLog", failLog);
            batchService.modifyBatchHistory(params);
        }
    }

}
