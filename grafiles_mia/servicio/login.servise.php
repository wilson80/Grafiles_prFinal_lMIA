<?php

define('API_URL', 'http://localhost:3500/connect');

class LoginService
{
    public function callAPI($method, $url, $data = false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($method === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            }
        }

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return "Error CURL: $error_msg";
        }

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($http_code !== 200) {
            return "Error HTTP Code: $http_code, Respuesta de la API: $response";
        }

        return json_decode($response, true);
    }


    public function login($username, $password)
    {
        $data = ['username' => $username, 'password' => $password];
        return $this->callAPI('POST', API_URL, $data);
    }
}
