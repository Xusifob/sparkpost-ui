<?php



try{

    $sparkpost->UpdateTemplateLinks($_POST['template_id'],$_POST['campaign_id'],$_POST['campaign_source']);


}
catch (\GuzzleHttp\Exception\ClientException $e){

    unset($_POST);

    $data =  $e->getMessage();
}

