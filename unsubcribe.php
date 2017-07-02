<?php

/**
 * Template Name: Unsubscribe
 */


include  __DIR__ . 'header.php';

if(isset($_POST['email'])){
    $sparkpist_result = $sparkpost->unsubscribe($_POST['email']);

}



?>
<!DOCTYPE html>

<html>

<head>

    <?php if(function_exists('get_header')) get_header(); ?>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style type="text/css">

        nav{
            display: none;
        }

        body {
            font: 14px/20px 'Helvetica', Arial, sans-serif;
            margin: 0;
            padding: 75px 0 0 0;
            text-align: center;
        }

        p {
            padding: 0 0 10px 0;
        }

        textarea {
            font: 12px/18px 'Helvetica', Arial, sans-serif;
        }

        h2 {
            font-size: 22px;
            line-height: 28px;
            margin: 0 0 10px 0;
        }

        h3 {
            margin: 0 0 5px 0;
            padding: 0;
        }

        .wrapper {
            width: 600px;
            margin: 0 auto 10px auto;
            text-align: left;
        }

        .container {
            position: relative;
            border-width: 0 !important;
            border-color: transparent !important;
            margin: 0;
            text-align: left;
            -moz-box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            -webkit-box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            float: none;
            clear: both;
            display: block;
            width: auto;
            margin-top: 8px;
            text-align: left;
            font-weight: bold;
            position: relative;
        }

        label.error{
            color: red;
            font-weight: normal;
            font-size: 12px;
            display:none;
        }

        .field-group {
            float: none;
            margin: 3px 0 15px 0;
            padding: 5px;
            border-style: solid;
            background: -moz-linear-gradient(top, rgba(255, 255, 255, 0),
            rgba(255, 255, 255, .25) );
            background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, rgba(255,
            255, 255, 0) ), color-stop(1, rgba(255, 255, 255, .25) ) );
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00ffffff',
            endColorstr='#3fffffff' );
            -ms-filter:
                "progid:DXImageTransform.Microsoft.gradient(startColorstr='#00ffffff', endColorstr='#3fffffff')";
        }

        .field-group input {
            display: block;
            margin: 0;
            padding: 5px;
            border: 0;
            background: none;
            width: 98%;
        }

        .radiogroup input {
            width: auto;
        }

        .radio-group input,.checkbox-group input {
            width: auto;
            display: inline-block;
            margin-right: 5px;
        }

        .field-group label {
            clear: none;
        }

        .field-group textarea {
            background: none;
            border: none;
            width: 525px;
            height: 150px;
            margin: 0;
            overflow: auto;
        }

        select {
            width: 300px;
            margin: 5px;
        }

        .button,.button-small {
            display: inline-block;
            font-family: 'Helvetica', Arial, sans-serif;
            width: auto;
            white-space: nowrap;
            height: 32px;
            margin: 5px 5px 0 0;
            padding: 0 22px;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            font-style: normal;
            font-size: 15px;
            line-height: 32px;
            cursor: pointer;
            border: 0;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -webkit-border-radius: 4px;
            vertical-align: top;
        }

        .button-small {
            float: none;
            display: inline-block;
            height: auto;
            line-height: 18px !important;
            padding: 2px 15px !important;
            font-size: 11px !important;
        }

        .button span {
            display: inline;
            font-family: 'Helvetica', Arial, sans-serif;
            text-decoration: none;
            font-weight: bold;
            font-style: normal;
            font-size: 15px;
            line-height: 32px;
            cursor: pointer;
            border: none;
        }

        .rounded6 {
            -moz-border-radius: 6px;
            border-radius: 6px;
            -webkit-border-radius: 6px;
        }

        .poweredWrapper {
            padding: 20px 0;
            width: 560px;
            margin: 0 auto;
        }

        .poweredBy {
            display: block;
        }

        span.or {
            display: inline-block;
            height: 32px;
            line-height: 32px;
            padding: 0 5px;
            margin: 5px 5px 0 0;
        }

        .clear {
            clear: both;
        }

        body {
            -webkit-text-size-adjust: none;
        }

        input {
            -webkit-appearance: none;
        }

        input[type=checkbox] {
            -webkit-appearance: checkbox;
        }

        input[type=radio] {
            -webkit-appearance: radio;
        }

        body,#bodyTable {
            background-color: #eeeeee;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 15px;
            padding: 0;
            margin-top: 0;
        }

        #templateContainer {
            background-color: none;
        }

        #templateBody {
            background-color: #ffffff;
        }

        .bodyContent {
            line-height: 200%;
            font-family: Helvetica;
            font-size: 15px;
            color: #333333;
            padding: 20px;
        }

        a:link,a:active,a:visited,a {
            color: #336699;
        }

        .button:link,.button:active,.button:visited,.button,.button span,.button-small:link,.button-small:active,.button-small:visited,.button-small
        {
            background-color: #336699;
            color: #ffffff;
        }

        .button:hover,.button-small:hover {
            background-color: #1e5781;
            color: #ffffff;
        }

        label {
            line-height: 150%;
            font-family: Helvetica;
            font-size: 14px;
            color: #333333;
        }

        .field-group {
            background-color: #eeeeee;
            border-width: 2px;
            border-color: #d0d0d0;
        }

        .field-group input,.field-group textarea {
            font-family: Helvetica;
            font-size: 16px;
            color: #333333;
        }

        html[dir=rtl] .wrapper,html[dir=rtl] .container,html[dir=rtl] label {
            text-align: right !important;
        }

        @media ( max-width : 601px) {
            body {
                width: 100%;
                -webkit-font-smoothing: antialiased;
                padding: 10px 0 0 0 !important;
                min-width: 300px !important;
            }
        }

        @media ( max-width : 601px) {
            textarea,select,ul.unsub-options li textarea {
                width: 80% !important;
            }
        }

        @media ( max-width : 601px) {
            h1 img {
                max-width: 100%;
                height: auto !important;
            }
        }

        @media ( max-width : 601px) {
            .wrapper,.poweredWrapper {
                width: auto !important;
                max-width: 600px !important;
                padding: 0 10px;
            }
        }

        @media ( max-width : 601px) {
            .addressfield .countryfield select {
                max-width: 50% !important;
            }
        }

        @media ( max-width : 601px) {
            #templateContainer,#templateBody,#templateContainer table {
                width: 100% !important;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }
        }

    </style>
</head>

<body>
<div class="wrapper rounded6" id="templateContainer">
    <!--<h1>TRANSACTILE</h1>-->
    <div class="imgholder thumb-wrapper thumb-lg" style="border:none;text-align: center;">

        <img class="company_logo w-full" src="..." style="width:120px;"/>
    </div>


    <?php if(isset($success) && isset($sparkpist_result)){ ?>
        <?php if(false === $success || !isset($sparkpist_result['results'])){ ?>
            <p style="color: red; text-align: center; font-weight: bold;">An error occurs, please try again later</p>
        <?php }else{ ?>
            <p style="color: green; text-align: center; font-weight: bold;">You've been unsubscribed successfully</p>
        <?php } ?>
    <?php } ?>

    <div id="templateBody" class="bodyContent rounded6">
        <h2>Unsubscribe</h2>
        <form id="unsubscribe-form" action="" method="post" >
            <input type="hidden" name="unsubscribe_subject" value="">
            <input type="hidden" name="is_unsubscribe_email_disabled" value="false">
            <div class="error"></div>
            <label for="email-address">Email Address <span
                    style="color: red">*</span></label>
            <div class="field-group focused-field">
                <input type="email" name="email" value="<?php echo (isset($_GET['utm_content'])) ? $_GET['utm_content'] : ''; ?>"
                       id="email-address" placeholder="Email to unsubscribe" required/>
            </div>
            <label for="email-address" class="error">Please enter a valid email address.</label>
            <br /> <input class="button" type="submit" value="Unsubscribe" />
        </form>
    </div>
</div>
<br />

</body>

<?php if(function_exists('wp_footer')) wp_footer(); ?>

</html>
