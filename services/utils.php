<?php
	function makeSafeArray($unsafe){
		$safe = array();
		foreach ($unsafe as $key => $value){
			$safe[$key] = preg_replace('/[^-a-zA-Z0-9_@\/\.\:\,"[]\ ]/', '', $value);
		}
		return $safe;
	}
	
	function testVar($var, $resp){
		if (!empty($var) or (is_numeric($var) == true and (int)$var >= 0)){
			return pg_escape_string($var);
		} else {
			$response = json_encode($resp);
			exit($response);
		}
	}
	
	function check_email_address($email) {
		// First, we check that there's one @ symbol,
		// and that the lengths are right.
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters
			// in one section or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}
		// Check if domain is IP. If not,
		// it should be valid domain name
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
    		$domain_array = explode(".", $email_array[1]);
	    	if (sizeof($domain_array) < 2) {
	    		return false; // Not enough parts to domain
	    	}
	    	for ($i = 0; $i < sizeof($domain_array); $i++) {
	    		if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
        			return false;
				}
			}
		}
		
		return true;
	}
	
	function check_email_exists($email){
		$sql = "SELECT uid FROM site.reg_encontros2015 WHERE email = '{$email}'";
		$query = pg_query($sql);
		if (pg_num_rows($query) > 0){
			return false;
		}
		return true;
	}
	
	function sendEmail($to, $msg){
		$subject = 'Inscrição no 2º Encontro de Utilizadores QGIS Portugal';
		$headers = 'From: Grupo de Utilizadores QGIS PT <qgis.portugal@gmail.com>' . "\r\n";
		$headers .= 'Bcc: qgis.portugal@gmail.com' . "\r\n";
		if (!mail($to, $subject, $msg, $headers)){
			return false;
		}
		return true;
	}
?>