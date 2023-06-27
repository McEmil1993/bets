package com.soas.service.impl;

import com.soas.dao.impl.PropertiesDAO;
import com.soas.service.PropertiesService;
import com.soas.util.CommonUtil;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.Map;

@Service("PropertiesService")
public class PropertiesServiceImpl implements PropertiesService {


    private PropertiesDAO propertiesDAO;

    @Autowired
    public void PropertiesServiceImpl(PropertiesDAO propertiesDAO){
        this.propertiesDAO = propertiesDAO;
    }


    @Override
    public boolean isInspection(Map<String, Object> params) throws Exception {

        Map<String,Object> config = propertiesDAO.selectInspection(params);
        if(CommonUtil.isEmpty(config))
            return false;

        String flag = config.get("value").toString().trim();
        if(flag.equals("N"))
            return false;

        return true;
    }


}
