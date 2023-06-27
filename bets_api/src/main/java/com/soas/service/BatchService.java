package com.soas.service;

import java.util.Map;

public interface BatchService {


    /* 배치 이력 저장 */
    public void saveBatchHistory (Map<String,Object> params) throws Exception;


    /* 배치 이력 수정 */
    public void modifyBatchHistory (Map<String,Object> params) throws Exception;


}
