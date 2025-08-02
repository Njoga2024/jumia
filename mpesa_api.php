<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$phone = $data["phone"];
$amount = $data["amount"];
$service = $data["service"];

$consumerKey = "ou1nZBqNAeS2RUdJuQmxqfI2jrYnW4WjZwDbRpAaIvE0BIPB";
$consumerSecret = "hG6PxOTTBhZNyhbXMsXGS9fbf7bmUpYSFOZAudRXCUiQ7vYqLfP7v5tPhW9bUqE1";
$shortcode = "8492912";
$passkey = "AFs/mB28rely2uYJG9M1kN+Yn7hcwSxJNOH0j8hR+CW/T7MfcoQq6cfkYgNOAiEkr+Hpgz/bwLYWuSUXgmrrBBd9Ob4JIJ7TiqmFcfTproSH7pxqrdKly30MgtTKqvI48gidy5YX+F4WcnDjyzLuUKTBjJZCbKhZ9R/cQ9+nFhFJosjcYID6XLCBTfFl8GVQ2GRJv8Qz4GJVxJ/A/P6mECwtFYJEixI63SH/S4yVN5CiGS1msW1HykIEkw73Tdq3ARVwo6GTyH4x8uY0xF36i12zC7wNDlMYQTDUUc//axjqTS4IVCHx2XFSPJd7VDuGUUUcpK3z1fP4COtFIspsjA==";
$callbackURL = "https://yourdomain.com/callback.php";

// 1. Get Access Token
$credentials = base64_encode("$consumerKey:$consumerSecret");
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($ch));
$accessToken = $response->access_token;
curl_close($ch);

// 2. Prepare STK Push
$timestamp = date("YmdHis");
$password = base64_encode($shortcode . $passkey . $timestamp);

$stkPushPayload = [
  "BusinessShortCode" => $shortcode,
  "Password" => $password,
  "Timestamp" => $timestamp,
  "TransactionType" => "CustomerBuyGoodsOnline",
  "Amount" => (int)$amount,
  "PartyA" => $phone,
  "PartyB" => $shortcode,
  "PhoneNumber" => $phone,
  "CallBackURL" => $callbackURL,
  "AccountReference" => $service,
  "TransactionDesc" => "$service Purchase"
];

$ch = curl_init("https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $accessToken",
  "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stkPushPayload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo json_encode(["success" => true, "message" => "STK Push Sent"]);
?>
