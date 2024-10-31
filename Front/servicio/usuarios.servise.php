<?php

define('API_URL', 'http://localhost:3500/usuarios');

class UsuariosService
{
    function callAPI($method, $url, $data = false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // Seguir redirecciones

        if ($method === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === "PUT") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === "DELETE") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        $response = curl_exec($curl);

        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            return ['error' => true, 'message' => $error];
        }

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Maneja el código de respuesta HTTP
        if ($http_code !== 200) {
            return [
                'error' => true,
                'message' => "Error HTTP Code: $http_code, Respuesta de la API: $response"
            ];
        }

        // Verifica si la respuesta es JSON
        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'message' => 'Error al decodificar la respuesta JSON: ' . json_last_error_msg()
            ];
        }

        return $decodedResponse; // Retorna la respuesta decodificada
    }


    public function obtenerUsuarios()
    {
        return $this->callAPI('GET', API_URL);
    }
    public function obtenerUsuarioPorId($id)
    {
        $url = API_URL . '/' . $id; // Construye la URL con el ID
        return $this->callAPI('GET', $url); // Llama a la API usando GET
    }

    public function guardarUsuario($data)
    {
        return $this->callAPI('POST', API_URL, $data); // Llama a la API usando POST
    }

    public function actualizarUsuario($id, $data)
    {
        $url = API_URL . '/' . $id; // Construye la URL con el ID
        return $this->callAPI('PUT', $url, $data); // Llama a la API usando PUT
    }
}
