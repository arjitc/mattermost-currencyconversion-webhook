<?php
if($_POST['token'] == "POST_TOKEN") {
	function convert_currency($currency_from, $currency_to) {
		if($currency_from == "SGD" || $currency_from == "USD" || $currency_from == "AUD" || $currency_from == "EUR" || $currency_from == "INR" || $currency_from == "CAD" || $currency_from == "GBP" || $currency_from == "CNY") {
			$data = file_get_contents("https://api.exchangeratesapi.io/latest?base=$currency_from");
			$json = json_decode($data);
			if($currency_to == "SGD" || $currency_to == "USD" || $currency_to == "AUD" || $currency_to == "EUR" || $currency_to == "INR" || $currency_to == "CAD" || $currency_to == "GBP" || $currency_to == "CNY") {
				switch ($currency_to) {
					case 'USD':
					return $json->rates->USD;
					break;
					case 'AUD':
					return $json->rates->AUD;
					break;
					case 'EUR':
					return $json->rates->EUR; 
					break;
					case 'INR':
					return $json->rates->INR; 
					break;
					case 'CAD':
					return $json->rates->CAD; 
					break;
					case 'GBP':
					return $json->rates->GBP; 
					break;
					case 'SGD':
					return $json->rates->SGD;
					break;
					case 'CNY':
					return $json->rates->CNY;
					break;
					default:
					return "Error, invalid TO currency";
					break;
				}
			}
		} else {
			return "Error, invalid FROM currency";
		}
	}
	$message_array = (explode(" ",$_POST['text']));
	$mattermost_webhook_url = "INCOMING_WEBHOOK_URL";
	$bot_name = "BOT_NAME";
	if($message_array[0] != "help") {
		$currency_from = $message_array[1];
		$currency_from = strtoupper($currency_from);
		$currency_to = $message_array[2];
		$currency_to = strtoupper($currency_to);
		$coverted_amount = number_format($message_array[0] * convert_currency($currency_from, $currency_to), 2, '.', ',');
		$message = $message_array[0]." $currency_from is $coverted_amount $currency_to";
		if(convert_currency($currency_from, $currency_to) == "Error, invalid FROM currency") {
			$message = "Error, invalid FROM currency";
		}
		if(convert_currency($currency_from, $currency_to) == "Error, invalid TO currency") {
			$message = "Error, invalid TO currency";
		}
	} else {
		$message = "eg, 1 USD INR \n Currencies supported are, USD, CAD, EUR, INR, AUD, SGD or GBP";
	}
	
	$color = "#3707ad";
	$channel = $_POST['channel_name'];
	$post = [
		'payload' => '{"username": "'.$bot_name.'", "channel": "'.$channel.'", "icon_url": "URL_TO_ICON",
		"attachments": [{
			"fallback": "'.$message.'",
			"color": "'.$color.'",
			"text": "'.$message.'"
		}]
	}',
];

$curl_handle=curl_init();
curl_setopt($curl_handle, CURLOPT_URL, $mattermost_webhook_url);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($post));
$query = curl_exec($curl_handle);
curl_close($curl_handle);
}
