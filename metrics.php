<?php

include __DIR__ . '/header.php';


$campaigns = $sparkpost->getCampaigns();

$recipient_list = $sparkpost->getRecipientList();


$recipients = array();

if(isset($_GET['campaign_metrics']))
{

    $events = $sparkpost->getMessageEvents();
    $recipients = $sparkpost->sortEvents($events,$recipients);
    $metrics = $sparkpost->getMetrics();

    $metrics = $sparkpost->sortMetrics($recipients,$metrics);

}


if(isset($_GET['to_export'])){
   $sparkpost->export($recipients);
    die();

}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Metrics</title>

    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<a href="index.php" class="back">Retour</a>

<form action="#" method="GET">

    <label for="campaign_metrics">Choisissez pour quelle campagne afficher les metrics</label>
    <select name="campaign_metrics[]" multiple style="height: 300px; padding: 0;" id="campaign_metrics">
        <?php foreach($campaigns['results']['campaigns'] as $key => $campaign) { ?>
            <option <?php echo (isset($_GET['campaign_metrics']) && in_array($campaign,$_GET['campaign_metrics'])) ? 'selected' : ''; ?> value="<?php echo $campaign; ?>"><?php echo $campaign; ?></option>
        <?php  } ?>
    </select>

    <input type="submit" value="rechercher">


    <?php if(isset($_GET['campaign_metrics'])){ ?>

        <?php $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; ; ?>

        <a target="_blank" href="<?php echo $actual_link; ?>&to_export=true">Téléchrger la selection</a>
    <?php } ?>
</form>


<?php if(isset($metrics)){ ?>

    <h2>Email envoyés : <?php echo $sparkpost->getTotal()['count_targeted']; ?></h2>
    <h2>Email acceptés : <?php echo $sparkpost->getTotal()['count_accepted']; ?> (<?php echo $sparkpost->getTotal()['AR']; ?>%)</h2>
    <h2>Email Ouverts : <?php echo $sparkpost->getTotal()['count_unique_rendered']; ?> (<?php echo $sparkpost->getTotal()['OR']; ?>%)</h2>
    <h2>Email Clics : <?php echo $sparkpost->getTotal()['count_unique_clicked']; ?> (<?php echo $sparkpost->getTotal()['CTR']; ?>%)</h2>


    <table>
        <tr>
            <th>Domain</th>
            <th>Targeted</th>
            <th>Acceptés</th>
            <th>Ouverts (%)</th>
            <th>Clics (%)</th>
        </tr>
        <?php foreach($metrics as $key =>  $metric){ ?>
            <tr class="bold" style="border-top: 2px solid black;">
                <td><?php echo $metric['domain']; ?></td>
                <td style="text-align: center"><?php echo $metric['count_targeted']; ?></td>
                <td style="text-align: center" >
                    <?php echo $metric['count_accepted']; ?>
                    (<?php echo ($metric['AR']); ?>%)
                </td>
                <td style="text-align: center" >
                    <?php echo $metric['count_unique_rendered']; ?>
                    (<?php echo ($metric['OR']); ?>%)
                </td>
                <td style="text-align: center" >
                    <?php echo $metric['count_unique_clicked']; ?>
                    (<?php echo ($metric['CTR']); ?>%)
                </td>
            </tr>
            <?php if(isset($recipients[$metric['domain']])){ ?>
                <?php foreach($recipients[$metric['domain']] as $email => $recipient){ ?>
                    <tr class="email <?php echo $recipient['unsubscribe'] ? 'unsubscribe' : ''; ?>" >
                        <td><?php echo $email ?></td>
                        <td style="text-align: center"><?php echo $recipient['count_targeted']; ?></td>
                        <td style="text-align: center" >
                            <?php echo $recipient['count_accepted']; ?>
                            (<?php echo ($recipient['AR']); ?>%)
                        </td>
                        <td style="text-align: center" >
                            <?php echo $recipient['count_unique_rendered']; ?>
                            (<?php echo ($recipient['OR']); ?>%)
                        </td>
                        <td style="text-align: center" >
                            <?php echo $recipient['count_unique_clicked']; ?>
                            (<?php echo ($recipient['CTR']); ?>%)
                        </td>
                    </tr>
                <?php  } ?>
            <?php  } ?>
        <?php } ?>
    </table>
<?php } ?>

</body>
</html>
