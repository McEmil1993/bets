package com.soas.dao.impl;

import com.soas.dao.MysqlMapper;
import org.springframework.stereotype.Repository;

import java.util.Map;


@Repository
public class GameDAO extends MysqlMapper{


	public Map<String,Object> selectProductInfo (Map<String,Object> params) {
		return selectOne("GameMapper.selectProductInfo", params);
	}


	public Map<String,Object> selectProductByTransaction (Map<String,Object> params) {
		return selectOne("GameMapper.selectProductByTransaction", params);
	}


}
