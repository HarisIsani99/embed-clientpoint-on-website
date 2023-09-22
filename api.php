<?php
require 'env.php';
/**
 * Retrieve data from an ClientPoint API endpoint and extract a value.
 *
 * This function performs the following steps:
 * 1. Sends a GET request to an API endpoint using cURL.
 * 2. Parses the JSON response.
 * 3. Extracts a value using the getLastIndex() function.
 *
 * @param string $base_url The base URL of the API.
 * @param string $api_key  The ClientPoint API key to use for authentication.
 *
 * @return string The extracted value.
 */
function fetchDataAndExtractValue($base_url, $api_key) {
  // Construct the API URL with parameters
  $data = json_decode(file_get_contents('php://input'), true);
  $curl = curl_init();
  curl_setopt_array($curl, array(
      CURLOPT_URL => $base_url . '/v4/clientpoint/get-link/' . $data['pId'] . '?api_key=' . $api_key . '&&type=view',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  $res = json_decode($response);

  // Extract the desired value using the getLastIndex() function
  $pin = getLastIndex($res->data->clientpoint_url);

  return $pin;
}
$pin = fetchDataAndExtractValue($base_url, $api_key);

/**
 * Retrieves the last index (last element after splitting by '/')
 *
 * This function takes an input string and splits it by '/' characters.
 * It then returns the last index (last element) of the resulting array.
 *
 * @param string $input_string The input string to be split
 *
 * @return string The last index (last element) of the split string
 */
function getLastIndex($input_string) {
  // Split the string by '/'
  $split_string = explode('/', $input_string);

  // Retrieve the last index (last element of the array)
  $last_index = end($split_string);

  return $last_index;
}



// Simulate a response data
$responseData = array(
  'status' => 'success',
  'pin' => $pin, // You can use $data or perform any other processing here
);

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($responseData);