<?php

class EmployeesApi
{
    private $username;
    private $password;

    public function __construct(string $username, string $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function apiRequest(string $url, array $jsonArray = array()){

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_USERPWD => "$this->username:$this->password"
        ));

        //if api params are provided:
        if (!empty($jsonArray)){
            $jsonDataEncoded = json_encode($jsonArray);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        }

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpcode !== 200) {
            return json_encode(["api_error"=>"There is a problem with the source data. Please try again."]);
        }

        if (!$response){
            return json_encode(["api_error"=>"Can not access resource. Please try again."]);
        }
        return $response;
    }

    public function getApiResponse(string $url, array $jsonArray = array()){
        $milliseconds = 100;
        //try to receive data from the API within 500 milliseconds in 5 attempts
        for ($i=0; $i <= 4; $i++){
            $response = $this->apiRequest($url, $jsonArray);
            $decoded = json_decode($response, true);

            //if there is no error in the resource, break the 5 attempts
            if (!isset($decoded['api_error'])){
                break;
            }
            usleep($milliseconds * 1000);
        }
        return $response;
    }
}