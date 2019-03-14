<?php
    include __DIR__ . '/../../lib/xmlutils.php';
    //error_reporting(0);
    $xml = simplexml_load_file('../promotions.xml');
    $out = $object = (object) [
        'message' => '',
        'type' => 'error'
    ];

    $promotion = insertIntoXML($xml);

    $xml_new_valid = validateXML($xml, '../schemas/promotions.xsd');

    #store xml into original xml if validation is ok
    if($xml_new_valid){
        persistXML('../promotions.xml', $xml);
        $object->message = <<<EOT
Promotion wurde erfolgreich erstellt.

Ihr Zugriffstoken für diese Promotion (bitte speichern):

EOT;
        $object->message .= $promotion["token"];
        $object->type = "success";
    }
    else{
        $object->message = "validation failed";
    }

    function generateToken() {
        // secure random number generation, 128 bit.
        // this is not feasibly brute-forcable, not even by
        // this application itself and won't collide
        return bin2hex(random_bytes(32));
    }

    function insertIntoXML($xml) {
        $promotion = $xml->addChild('promotion', '');
        $promotion->addAttribute('token', generateToken());
        $promotion->addAttribute('added', date('Y-m-d'));
        $promotion->addChild('name', $_POST['name']);
        $promotion->addChild('description', $_POST['description']);
        $promotion->addChild('discount', $_POST['discount']);
        $promotion->addChild('provider', $_POST['provider']);
        $promotion->addChild('amount', $_POST['amount']);
        return $promotion;
    }

    header('Content-Type: application/json');
    echo json_encode($out);
?>
