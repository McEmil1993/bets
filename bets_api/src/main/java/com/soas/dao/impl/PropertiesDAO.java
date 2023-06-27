package com.soas.dao.impl;

import com.soas.dao.MysqlMapper;
import com.soas.domain.CustomMap;
import org.springframework.stereotype.Repository;

import java.util.Map;

@Repository
public class PropertiesDAO extends MysqlMapper {


    public CustomMap selectInspection (Map<String,Object> params) {
        return selectOne("PropertiesMapper.selectInspection", params);
    }


    public CustomMap selectUserRollPer (Map<String,Object> params) {
        return selectOne("PropertiesMapper.selectUserRollPer", params);
    }


    public CustomMap selectApiKey (Map<String,Object> params) {
        return selectOne("PropertiesMapper.selectApiKey", params);
    }

}
