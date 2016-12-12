<?php
require dirname( __FILE__ ) . '/vendor/autoload.php';
use Aws\Sns\SnsClient;

$client = SnsClient::factory(array(
        'credentials' => array(
              'key'    => 'AKIAJU2HK2KNTBZ75ERQ',
              'secret' => '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',
        'ssl.certificate_authority' => 'C:\cacert.pem'
  ),
        'region'  => 'us-east-1',
        'version' => '2010-03-31',
    )
);

// $message = array_pop( $argv );
$message = "Hello Hemeos World!!";

$payload = array(
    'TopicArn' => 'arn:aws:sns:us-east-1:381753482005:Patient_Search_Alert',
    'Message' => $message,
    'MessageStructure' => 'string',
);

try {
    $client->publish( $payload );
    echo 'Sent message: "' . $message . '"';
} catch ( Exception $e ) {
    echo ' Send Failed \n ' . $e->getMessage();
};

