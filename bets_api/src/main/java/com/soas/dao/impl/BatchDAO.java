package com.soas.dao.impl;

import com.soas.dao.MysqlMapper;
import org.springframework.stereotype.Repository;

import java.util.Map;


@Repository
public class BatchDAO extends MysqlMapper{


	public void insertBatchHistory (Map<String,Object> params) {
		insert("BatchMapper.insertBatchHistory", params);
	}


	public void updateBatchHistory (Map<String,Object> params) {
		insert("BatchMapper.updateBatchHistory", params);
	}


}
