<?php

	include 'validation.php';
	//error_reporting(0);
	$xml = simplexml_load_file('../wgs.xml');
	
	insertIntoXML($xml);
	
	#first store xml into temp xml
	persistXML('../wgs_new.xml', $xml);
	
	#validate the temp xml
	$xml_new_valid = validation('../wgs_new.xml', '../schemas/wgs.xsd');
	
	#store xml into original xml if validation is ok
	if($xml_new_valid){
		persistXML('../wgs.xml', $xml);
		echo "wg was successfully added";
	}
	else{
		echo "validation failed";
	}
	
    function insertIntoXML($xml) {
        $xml->registerXPathNamespace('w', 'http://wgcard.xml.hslu.ch/wgs');
        $ids = array_map(function($e) {
            return (int)$e->id;
        }, $xml->xpath("//w:wg/@id"));
        $new_id = $ids ? max($ids) + 1 : 1;

        $wg = $xml->addChild('wg', '');
        $wg->addAttribute('id', $new_id);

        $wg_members = $wg->addChild('wg-members', '');

        for ($i = 1; $i <= $_POST['membercount']; $i++){
            $wg_member = $wg_members->addChild('wg-member', '');
            insertWgMember($wg_member, $i);
        }

        $wg->addChild('alias', $_POST['alias']);

        $address = $wg->addChild('address', '');

        insertWgAddress($address);
    }
	
	function insertWgMember($wg_member, $i){
		$person = $wg_member->addChild('person', '');
		insertPerson($person, $i);
		
		if ($_POST['contactPerson'.$i] == 'on') {
			$wg_member->addChild('contactPerson', 'true');
		}
		else{
			$wg_member->addChild('contactPerson', 'false');
		}
	}
	
	function insertWgAddress($address){
		$address->addChild('street', $_POST['street']);
		$address->addChild('zip', $_POST['zip']);
		$address->addChild('city', $_POST['city']);
	}
	
	function insertPerson($person, $i){
		$person->addChild('firstName', $_POST['firstname'.$i]);
		$person->addChild('lastName', $_POST['lastname'.$i]);
		$person->addChild('email', $_POST['mail'.$i]);
		$person->addChild('tel', $_POST['tel'.$i]);
	}

function persistXML($path, $xml) {
    $dom = new DOMDocument("1.0");
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    file_put_contents($path, $dom->saveXML());
}
?>

<a href="../get-wgcard.xml">return</a>
