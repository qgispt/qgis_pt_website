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
		$sql = "SELECT uid FROM site.reg_encontros2016 WHERE email = '{$email}'";
		$query = pg_query($sql);
		if (pg_num_rows($query) > 0){
			return false;
		}
		return true;
	}
	
	function sendEmail($to, $msg){
  $subject = 'Inscrição no 3º Encontro de Utilizadores QGIS Portugal';
  $from = "qgis@qgis.pt"; // this is the sender's Email address
  $headers = "Reply-To: $from\r\n";
  $headers .= "Return-Path: $from\r\n";
  $headers .= "From: $from\r\n";
  $headers .= "Bcc: $from\r\n";  
  $headers .= "Organization: Grupo de Utilizadores QGIS Portugal\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
  $headers .= "X-Priority: 3\r\n";
  $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
	
		if (!mail($to, $subject, $msg, $headers, "-f$from")){
			return false;
		}
		return true;
	}
	
	
	function sendEmailPhpMailer($to, $msg){

$correio = new PHPMailer(); // create a new object
$correio->IsSMTP(); // enable SMTP
//$correio->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
$correio->SMTPAuth = true; // authentication enabled
$correio->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
$correio->Host = "smtp.gmail.com";
$correio->Port = 465; // or 587
$correio->IsHTML(true);
$correio->Username = "qgis.portugal@gmail.com";
$correio->Password = "***";
$correio->SetFrom("qgis.portugal@gmail.com", "Grupo Utilizadores QGIS Portugal");
$correio->Subject = "Inscrição no 3º Encontro de Utilizadores QGIS Portugal";
$correio->Body = $msg;
$correio->AddAddress($to);
$correio->AddBCC("qgis.portugal@gmail.com");
$correio->CharSet = 'UTF-8';
//$correio->Send();
	
		if (!$correio->Send()){
			return false;
		}
		return true;	
	}	
	
?>
