<?php

define('API_URLC', 'http://localhost:3500/carpetas');

class FolderService
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
        if ($http_code !== 200 && $http_code !== 201 && $http_code !== 204) {
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

    public function crearCarpeta($data)
    {
        return $this->callAPI('POST', API_URLC, $data); // Llama a la API usando POST
    }

    public function copiarCarpeta($idFM, $data)
    {
        $url = API_URLC . '/copiar-carpeta/' . $idFM; // Construye la URL con el ID
        return $this->callAPI('POST', $url, $data); // Llama a la API usando POST
    }

    public function obtenerCarpetaRaiz($idU) // Recibe el ID del usuario
    {
        $url = API_URLC . '/carpeta-raiz?idU=' . $idU; // Agrega el parámetro en la URL para GET
        return $this->callAPI('GET', $url);
    }

    public function crearCarpetasEnCarpetas($nombre, $idU, $ficheroMadre)
    {
        // Construye el array de datos que se enviará a la API
        $data = [
            'nombre' => $nombre,
            'idU' => $idU,
            'ficheroMadre' => $ficheroMadre
        ];

        // Llama a la API usando POST
        return $this->callAPI('POST', API_URLC . '/newCarpetInCarpet', $data);
    }


    public function obtenerCarpetasDeCarpetas($idU, $idC)
    {
        return $this->callAPI('GET', API_URLC . '/' . $idU . '/' . $idC); // Llama a la API usando GET
    }

    public function actualizarNombreCarpeta($id, $data)
    {
        $url = API_URLC . '/nombre-actualizar/' . $id; // Construye la URL con el ID
        return $this->callAPI('PUT', $url, $data); // Llama a la API usando PUT
    }

    public function moverCarpeta($id, $data)
    {
        $url = API_URLC . '/mover_carpeta/' . $id; // Construye la URL con el ID
        return $this->callAPI('PUT', $url, $data); // Llama a la API usando PUT
    }

    public function eliminarCarpeta($id)
    {
        return $this->callAPI('DELETE', API_URLC . '/' . $id); // Llama a la API usando DELETE
    }
}
