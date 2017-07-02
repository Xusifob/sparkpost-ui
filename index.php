<?php

include __DIR__ . '/header.php';


if(isset($_POST['template_id']) && !empty($_POST['template_id'])){
    include 'update-template.php';
}


if(isset($_POST['campaign_id']) && !empty($_POST['campaign_id'])) {


    $array = @json_decode(@file_get_contents('sent.json'),true);

    if(!is_array($array)){
        $array = array();
    }


    if(in_array($_POST,$array) && !strpos($_POST['list_id'],'test')){
        unset($_POST);
        $data = 'cette campagne a deja été envoyée';
    }else {


        $array[] = $_POST;

        file_put_contents('sent.json', json_encode($array));

        try {


            $data = $sparkpost->sendEmail(array('list_id' => $_POST['list_id']),$_POST['template_id'],$_POST['campaign_id']);

            $data = json_encode(json_decode($data),JSON_PRETTY_PRINT);


        } catch (\GuzzleHttp\Exception\ClientException $e) {
            echo $e->getMessage();
        }
    }
}



$recipient_list =$sparkpost->getRecipientList();

$template_list = $sparkpost->getTemplates();



function is_selected($key,$value)
{
    if(isset($_GET[$key]) && $_GET[$key] == $value ){
        echo 'selected';
    }
}

if(isset($_FILES) && !empty($_FILES)) {
    $sparkpost->createSparkpostList();
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Envoi d'un emailing</title>


    <link rel="stylesheet" href="css/style.css">
</head>
<body>


<a href="metrics.php" class="back">Voir les résultats</a>

<form action="#" method="POST" enctype="multipart/form-data">

    <h2>CSV to Sparkpost</h2>

    <p>Ce formulaire permet de transformer un fichier CSV dans un format pour pouvoir l'importer dans SparkPost</p>

    <input type="file" accept="text/csv" name="file" required>

    <a href="data/dunforce-test.csv">Télécharger le fichier de test</a>
    <input type="submit" value="go">
</form>
<div style="margin-top: 140px;" ></div>



<form action="" method="POST">

    <h2>Envoi d'un e-mailing</h2>

    <?php if(isset($data)){ ?>
        <strong><pre><?php echo $data; ?></pre></strong>
    <?php } ?>

    <label for="campaign_id">ID de la campagne à envoyer (utile pour trier les résultats)</label>
    <input type="text" required name="campaign_id" value="<?php echo isset($_GET['campaign_id']) ? $_GET['campaign_id'] : ''; ?>" id="campaign_id" placeholder="Sparkpost Campaign ID">
    <label for="list_id">Identifiant Sparkpost de la liste de destinataires</label>
    <select name="list_id" required id="list_id">
        <option value="">Sélectionnez une liste</option>
        <?php foreach($recipient_list['results'] as $value){ ?>
            <option value="<?php echo $value['id']; ?>" <?php is_selected('list_id',$value['id']) ; ?> ><?php echo $value['name']; ?> (<?php echo $value['total_accepted_recipients']; ?>)</option>
        <?php } ?>
    </select>
    <label for="template_id">Iidentifiant Sparkpost du template à envoyer</label>
    <select name="template_id" required id="template_id">
        <option value="">Sélectionnez un template</option>
        <?php foreach($template_list['results'] as $value){ ?>
            <option value="<?php echo $value['id']; ?>" <?php is_selected('template_id',$value['id']) ; ?>><?php echo $value['name']; ?></option>
        <?php } ?>
    </select>
    <label for="campaign_source">Type D'email envoyé</label>
    <select name="campaign_source" id="campaign_source">
        <option value="">Sélectionnez un type d'email</option>
        <option value="Test" <?php is_selected('campaign_source','Test') ; ?>>Test</option>
        <option value="Newsletter" <?php is_selected('campaign_source','Newsletter') ; ?>>Newsletter</option>
        <option value="Cold Emailing" <?php is_selected('campaign_source','Cold Emailing') ; ?>>Cold Emailing</option>
    </select>


    <input type="submit" value="Envoyer">
</form>

</body>
</html>