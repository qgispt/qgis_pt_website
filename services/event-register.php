<?php 
	include_once 'connect.php';
	include_once 'utils.php';
	
	$response = array();
	$response['success'] = false;
	$response['message'] = 'Ocorreu um erro no seu registo. Verifique se introduziu correctamente todos os campos obrigatórios do formulário.';
	
	$safeArray = makeSafeArray($_REQUEST);
		
	$nome = testVar($safeArray['nome'], $response);
	$entidade = testVar($safeArray['entidade'], $response);
	$funcao = testVar($safeArray['funcao'], $response);
	$email = testVar($safeArray['email'], $response);
	$ws1 = testVar($safeArray['ws1'], $response);
	$ws2 = testVar($safeArray['ws2'], $response);
	$so = testVar($safeArray['so'], $response);
	$softsig = testVar($safeArray['softsig'], $response);
	$knowhow = testVar($safeArray['knowhowqgis'], $response);
	
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
		$sql = "INSERT INTO site.reg_encontros (encontro, nome, entidade, email, ws1, ws2, so, softsig, knowhowqgis, interesses, funcao) VALUES (2, '{$nome}', '{$entidade}', '{$email}', '{$ws1}', '{$ws2}', '{$so}', '{$softsig}', {$knowhow}, NULL, '{$funcao}')";
	} else {
		$sql = "INSERT INTO site.reg_encontros (encontro, nome, entidade, email, ws1, ws2, so, softsig, knowhowqgis, interesses, funcao) VALUES (2, '{$nome}', '{$entidade}', '{$email}', '{$ws1}', '{$ws2}', '{$so}', '{$softsig}', {$knowhow}, '{$interesses}', '{$funcao}')";
	}
	
	$query = pg_query($sql);
	
	if (!$query){
		$response['message'] = 'Ocorreu um erro ao executar o query.';
		exit(json_encode($response));
	} else {
		$msg = "Caro(a) ".$nome.",\r\n";
		$msg .= "Vimos por este meio confirmar a sua inscrição no 2º Encontro de Utilizadores QGIS PT. Lembramos que, se quiser garantir o seu lugar nos workshops, deverá efectuar um donativo mínimo de 5€ por workshop directamente ao projecto QGIS através do botão paypal disponível no nosso";
		$msg .= " site. Deverá depois enviar-nos o comprovativo do donativo para qgis.portugal@gmail.com. Até dia 2 de Junho!";
		if (sendEmail($email, $msg) == true){
			$response['success'] = true;
			$response['message'] = 'Seja bem-vindo ao 2ª Encontro de Utilizadores QGIS Portugal! O seu registo foi concluido com sucesso e em breve deverá receber uma confirmação por email.';
		}
        echo json_encode($response);
	}
?>
