package com.soas.domain;

import org.springframework.jdbc.support.JdbcUtils;

import java.util.HashMap;
import java.util.Locale;

@SuppressWarnings("serial")
public class CustomMap extends HashMap<String,Object> {

    @Override
    public Object put(String key, Object value) {
        return super.put(JdbcUtils.convertUnderscoreNameToPropertyName(key.toLowerCase()), value);
    }
}
