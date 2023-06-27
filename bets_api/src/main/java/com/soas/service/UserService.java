package com.soas.service;


import java.util.Map;

public interface UserService {


    /* 회원 조회 */
    public Map<String,Object> getUserInfo (Map<String,Object> params) throws Exception;
    
    /* 회원 조회 */
    public Map<String,Object> getUserInfoOdd (Map<String,Object> params) throws Exception;

    /* 회원 머니 변경 */
    public int modifyMoney (Map<String,Object> params) throws Exception;

    /* 회원 머니 조회 */
    public int getUserMoney (Map<String,Object> params) throws Exception;


}
