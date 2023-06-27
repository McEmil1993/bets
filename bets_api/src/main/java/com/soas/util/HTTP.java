package com.soas.util;

import com.fasterxml.jackson.core.JsonParseException;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.soas.prop.Properties;
import lombok.extern.log4j.Log4j2;
import org.springframework.http.*;
import org.springframework.web.client.RestTemplate;

import javax.net.ssl.HttpsURLConnection;
import java.io.*;
import java.net.HttpURLConnection;
import java.net.Socket;
import java.net.URL;
import java.net.URLConnection;
import java.nio.charset.Charset;
import java.util.HashMap;
import java.util.Map;

@Log4j2
public class HTTP {


    private static final String USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36";
    public static Map<String, Object> requestKplayAPI(String uri) {

        try {

//            String queryString = params.entrySet().stream()
//                    .map(p -> CommonUtil.urlEncodeUTF8(p.getKey()) + "=" + CommonUtil.urlEncodeUTF8(p.getValue().toString()))
//                    .reduce((p1, p2) -> p1 + "&" + p2)
//                    .orElse("");

            String url = Properties.KPLAY_API_DOMAIN + "/" + uri;
            log.info("======================================");
            log.info("URL : " + url);
//            log.info("Params : " + queryString);
            log.info("======================================");
            String timestamp = String.valueOf(System.currentTimeMillis());

            Map<String,Object> result = httpRequestGet(url);
            return result;

        } catch (Exception e) {
            log.error(e);
            Map<String, Object> retMap = new HashMap<>();
            setHttpResponse(retMap, 500, e.getMessage());
            return retMap;
        }
    }

    public static URLConnection open(String url) throws IOException {
        try {

            URL req = new URL(url);
            HttpURLConnection conn = (HttpURLConnection) req.openConnection();

            /* Header */
            conn.setRequestProperty("ac-code", Properties.KPLAY_AG_CODE);
            conn.setRequestProperty("ac-token", Properties.KPLAY_AG_TOKEN);

            conn.setRequestMethod("GET");
            conn.setConnectTimeout(1000);
            conn.setReadTimeout(10 * 1000);
            conn.setRequestProperty("User-Agent", USER_AGENT);
            conn.setRequestProperty("Content-Type", "application/json;charset=UTF-8"); // "application/json;charset=UTF-8"
            conn.setDoOutput(true);
            conn.setDoInput(true);
            return conn;
        } catch (Exception e) {
            log.error(e);
        }
        return null;

    }


    private static Map<String, Object> setHttpResponse(Map<String,Object> retMap, int code, String message) {
        retMap.put("code", code);
        retMap.put("message", message);
        return retMap;
    }


    public static void close(URLConnection conn) throws IOException {
        if (conn instanceof HttpURLConnection) {
            HttpURLConnection v = (HttpURLConnection) conn;
            v.disconnect();
        } else if (conn instanceof HttpsURLConnection) {
            HttpsURLConnection v = (HttpsURLConnection) conn;
            v.disconnect();
        }
    }


    @SuppressWarnings("unchecked")
    private static Map<String, Object> httpRequestGet(String uri) throws Exception {
        RestTemplate rest = new RestTemplate();
        HttpHeaders headers = new HttpHeaders();
        headers.setContentType(new MediaType("application", "json", Charset.forName("UTF-8")));
        headers.add("ag-code", Properties.KPLAY_AG_CODE);
        headers.add("ag-token", Properties.KPLAY_AG_TOKEN);
        headers.add("User-Agent", USER_AGENT);

        HttpEntity<String> entity = new HttpEntity<String>(null, headers);
//        log.info("HTTP.httpRequestGet : " + uri + "?" + queryString);

        ResponseEntity<String> respEntity = rest.exchange(uri, HttpMethod.GET, entity, String.class);
        ObjectMapper mapper = new ObjectMapper();
        Map<String, Object> retMap = null;

        try {
            log.info("httpRequestPost response => " + respEntity.getBody());
            retMap = mapper.readValue(respEntity.getBody(), Map.class);
            setHttpResponse(retMap, 200, "");
        } catch(JsonParseException e) {
            log.error(e);
            retMap = new HashMap<String, Object>();
            setHttpResponse(retMap, 500, e.getMessage());
        }

        return retMap;
    }


}
