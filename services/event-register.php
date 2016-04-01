<?php 
	include_once 'connect.php';
	include_once 'utils.php';
	require_once('phpmailer/class.phpmailer.php');
	require_once('phpmailer/PHPMailerAutoload.php');	
	
	$response = array();
	$response['success'] = false;
	$response['message'] = 'Ocorreu um erro no seu registo. Verifique se introduziu correctamente todos os campos obrigatórios do formulário.';
	
	$safeArray = makeSafeArray($_REQUEST);
		
	$nome = testVar($safeArray['nome'], $response);
	$entidade = testVar($safeArray['entidade'], $response);
	$funcao = testVar($safeArray['funcao'], $response);
	$email = testVar($safeArray['email'], $response);
	$ws1 = testVar($safeArray['ws1'], $response);
	$so = testVar($safeArray['so'], $response);
	$softsig = testVar($safeArray['softsig'], $response);
	$knowhow = testVar($safeArray['knowhowqgis'], $response);	
	//$almoco = testVar($safeArray['almoco'], $response);
	
	if (check_email_address($email) == false){
		$response['message'] = 'O seu endereço de email não é válido.';
		exit(json_encode($response));
	}
	
	if (check_email_exists($email) == false){
		$response['message'] = 'Já existe um utilizador registado com o mesmo endereço de email.';
		exit(json_encode($response));
	}
	
	$interesses = $safeArray['interesses'];
	if (empty($interesses)){
		$sql = "INSERT INTO site.reg_encontros2016 (encontro, nome, entidade, email, ws1, so, softsig, knowhowqgis, interesses, funcao) VALUES (3, '{$nome}', '{$entidade}', '{$email}', '{$ws1}', '{$so}', '{$softsig}', {$knowhow}, NULL, '{$funcao}')";
	} else {
		$sql = "INSERT INTO site.reg_encontros2016 (encontro, nome, entidade, email, ws1, so, softsig, knowhowqgis, interesses, funcao) VALUES (3, '{$nome}', '{$entidade}', '{$email}', '{$ws1}', '{$so}', '{$softsig}', {$knowhow}, '{$interesses}', '{$funcao}')";
	}
	
	$query = pg_query($sql);
	
	if (!$query){
		$response['message'] = 'Ocorreu um erro ao executar o query.';
		exit(json_encode($response));
	} else {
		$msg = "Caro(a) ".$nome.",\r\n";
		$msg .= "Vimos por este meio confirmar a sua inscrição no 3º Encontro de Utilizadores QGIS PT. ";
		$msg .= "Deverá enviar-nos o comprovativo de pagamento para qgis.portugal@gmail.com. Até dia 17 de Junho!";
		if (sendEmailPhpMailer($email, $msg) == true){
			$response['success'] = true;
			$response['message'] = 'Seja bem-vindo ao 3º Encontro de Utilizadores QGIS Portugal! O seu registo foi concluido com sucesso e em breve deverá receber uma confirmação por email.';
		}
		echo json_encode($response);
	}
?>
