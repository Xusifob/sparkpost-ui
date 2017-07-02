<?php

ini_set('display_errors',1);

include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/functions.php';
include_once __DIR__ . '/src/Http/CSVResponse.php';

include_once __DIR__ . '/libs/Encoding.php';

include_once __DIR__ . '/src/Service/Utils.php';
include_once __DIR__ . '/src/Service/SparkPost.php';


use Xusifob\Sparkpost\Service\SparkPost;


$config = json_decode(file_get_contents('config/config.json'),true);

$unsubscribe_link = $config['unsubscribe_link'];

$sparkpost = new SparkPost($config);

$body = file_get_contents('php://input');
$body = json_decode($body,true);

$lang = 'fr';

if(isset($_GET['lang'])){
    $lang = $_GET['lang'];
}
