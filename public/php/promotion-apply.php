<?php
	$xml = simplexml_load_file('../promotions.xml');
	
	insertIntoXML($xml);
	
	#first store xml into temp xml
	persistXML('../promotions_new.xml', $xml);
	
	#validate the temp xml
	$xml_new_valid = validation('../promotions_new.xml');
	
	#store xml into original xml if validation is ok
	if($xml_new_valid){
		persistXML('../promotions.xml', $xml);
		echo "promotion was successfully added";
	}
	else{
		echo "validation failed";
	}
	
	function validation($xml){
		$data = file_get_contents($xml);
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($data);
		return validateXML($xmlDoc, '../schemas/promotions.xsd');
	}
	
	function validateXML($xml, $xsd){
		if(!$xml->schemaValidate($xsd)){
			return false;
		}
		else{
			return true;
		}
	}
	
	function insertIntoXML($xml) {
		$promotion = $xml->addChild('promotion', '');
		$promotion->addAttribute('added', date('Y-m-d'));
		$promotion->addChild('name', $_POST['name']);
		$promotion->addChild('description', $_POST['description']);
		$promotion->addChild('discount', $_POST['discount']);
		$promotion->addChild('provider', $_POST['provider']);
		$promotion->addChild('amount', $_POST['amount']);
	}
	
	function persistXML($path, $xml) {
		file_put_contents($path, $xml->asXML());
	}
?>

<a href="../add-promotion.xml">return</a>