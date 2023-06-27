package com.soas.service.impl;

import com.soas.dao.impl.BatchDAO;
import com.soas.service.BatchService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.Map;

@Service("BatchService")
public class BatchServiceImpl implements BatchService {


    private BatchDAO batchDAO;

    @Autowired
    public void BatchServiceImpl (BatchDAO batchDAO) {
        this.batchDAO = batchDAO;
    }

    @Override
    public void saveBatchHistory(Map<String, Object> params) throws Exception {
        params.put("platform", "P");
        batchDAO.insertBatchHistory(params);
    }

    @Override
    public void modifyBatchHistory(Map<String, Object> params) throws Exception {
        batchDAO.updateBatchHistory(params);
    }

}
