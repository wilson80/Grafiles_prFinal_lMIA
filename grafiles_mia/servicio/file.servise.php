<?php

define('API_URLF', 'http://localhost:3500/archivos');

class FilesService
{
    function callAPI($method, $url, $data = false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

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

        if ($http_code !== 200 && $http_code !== 201 && $http_code !== 204) {
            return [
                'error' => true,
                'message' => "Error HTTP Code: $http_code, Respuesta de la API: $response"
            ];
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'message' => 'Error al decodificar la respuesta JSON: ' . json_last_error_msg()
            ];
        }

        return $decodedResponse;
    }

    public function addFile($data)
    {
        return $this->callAPI('POST', API_URLF . '/', $data);
    }

    public function getFiles($idC, $idU)
    {
        return $this->callAPI('GET', API_URLF . '/' . $idC . '/' . $idU);
    }

    public function getDeletedFiles($idC)
    {
        return $this->callAPI('GET', API_URLF . '/eliminados/' . $idC);
    }

    public function getSharedFiles($idC, $idU)
    {
        return $this->callAPI('GET', API_URLF . '/archivos-compartidos/' . $idC . '/' . $idU);
    }

    public function moveFileToTrash($id)
    {
        return $this->callAPI('DELETE', API_URLF . '/papelera/' . $id);
    }

    public function editFile($data)
    {
        return $this->callAPI('PUT', API_URLF . '/editar', $data);
    }

    public function shareFile($data)
    {
        return $this->callAPI('POST', API_URLF . '/compartir', $data);
    }

    public function deleteFilePermanently($id)
    {
        return $this->callAPI('DELETE', API_URLF . '/eliminar-del-sistema/' . $id);
    }
}
