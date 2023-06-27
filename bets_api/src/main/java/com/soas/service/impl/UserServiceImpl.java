package com.soas.service.impl;

import com.soas.dao.impl.UserDAO;
import com.soas.domain.CustomMap;
import com.soas.prop.CustomException;
import com.soas.prop.ResponseCode;
import com.soas.service.UserService;
import com.soas.util.CommonUtil;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.Map;


@Service("UserService")
public class UserServiceImpl implements UserService {


    private UserDAO userDAO;

    @Autowired
    public UserServiceImpl(UserDAO userDAO) {
        this.userDAO = userDAO;
    }


    @Override
    public Map<String, Object> getUserInfo(Map<String, Object> params) throws Exception {

        CustomMap userInfo = userDAO.selectUser(params);
        if(CommonUtil.isEmpty(userInfo))
            throw new CustomException(ResponseCode.INVALID_USER);

        return userInfo;
    }

    @Override
    public Map<String, Object> getUserInfoOdd(Map<String, Object> params) throws Exception {

        CustomMap userInfo = userDAO.selectUserOdd(params);
        if(CommonUtil.isEmpty(userInfo))
            throw new CustomException(ResponseCode.INVALID_USER);

        return userInfo;
    }

    
    @Override
    public int modifyMoney(Map<String, Object> params) throws Exception {

        if(CommonUtil.isEmpty(params.get("money")) && CommonUtil.isEmpty(params.get("addMoney")))
            throw new CustomException(ResponseCode.INVALID_PARAMETER);

//        if(CommonUtil.isEmpty(params.get("kpId")))
//            throw new CustomException(ResponseCode.INVALID_USER);

        userDAO.updateUserMoney(params);
        CustomMap userInfo = userDAO.selectUser(params);
        int money = Integer.parseInt(String.valueOf(userInfo.get("money")));
        return money;
    }

    @Override
    public int getUserMoney(Map<String, Object> params) throws Exception {
        CustomMap userInfo = userDAO.selectUser(params);
        int money = Integer.parseInt(String.valueOf(userInfo.get("money")));
        return money;
    }


}
