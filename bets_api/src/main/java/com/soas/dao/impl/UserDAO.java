package com.soas.dao.impl;

import com.soas.dao.MysqlMapper;
import com.soas.domain.CustomMap;
import org.springframework.stereotype.Repository;

import java.util.Map;


@Repository
public class UserDAO extends MysqlMapper{


	public CustomMap selectUser(Map<String,Object> params) {
		return selectOne("UserMapper.selectUser", params);
	}

	public CustomMap selectUserOdd(Map<String,Object> params) {
		return selectOne("UserMapper.selectUserOdd", params);
	}
	
	public void updateUserMoney(Map<String,Object> params) {
		update("UserMapper.updateUserMoney", params);
	}


}
