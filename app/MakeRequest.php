<?php

namespace App;

class MakeRequest {
  static public function request($model, $method, $key, $properties = [])
		{
      $client = new \GuzzleHttp\Client();
      $url = 'https://api.novaposhta.ua/v2.0/json/';

      $data = [
        "modelName" => $model,
        "calledMethod" => $method,
        "apiKey" => $key
      ];
      if (!empty($properties)) {
        $data['methodProperties'] = $properties;
      }
      $response = $client->post($url,
        ['body' => json_encode($data)]
      );

      return json_decode($response->getBody()->getContents(), true);
		}
}