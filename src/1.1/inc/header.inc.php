<?php
    require dirname(__FILE__) . '/header/nav.a.inc.php';
    $front_version = "4.2";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" class="fixed">
    <head>

        <title>
            <?php echo isset($pageTitle)?$pageTitle:TITLE ?>
        </title>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        
        <!-- test  -->
<!--        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css"/>-->
        
<!--        <link href="<?php echo HTTP_SUB ?>assets/scripts/all.css?version=<?php echo $front_version; ?>" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/scripts/all.js?version=<?php echo $front_version; ?>"></script>-->
        
        <!-- replace:css -->
        <link rel="stylesheet" href="<?php echo HTTP_SUB ?>assets/css/header.common.css?version=<?php echo $front_version; ?>" />
        <!-- /replace:css -->

        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.css"/>
        
        <!-- replace:js -->
        <script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/js/header.common.js?version=<?php echo $front_version; ?>"></script>
        <!-- /replace:js -->

        <style type="text/css">
            .btn-primary {
                margin-top: 5px;
            }
        </style>

        <?php if (1) { ?>
            <?php if ($modul == "int") { ?>
            <?php } else { ?>
                    <?php if (file_exists(dirname(__FILE__) . "/../css/" . $modul . ".css")) { ?>
                        <link rel="stylesheet" type="text/css" href="<?php echo HTTP_SUB . "css/" . $modul . ".css" ?>"/>
                    <?php } ?>

                    <!-- Meta -->
                <link rel="apple-touch-icon" sizes="57x57" href="/assets/images/favicon/apple-icon-57x57.png"/>
                <link rel="apple-touch-icon" sizes="60x60" href="/assets/images/favicon/apple-icon-60x60.png"/>
                <link rel="apple-touch-icon" sizes="72x72" href="/assets/images/favicon/apple-icon-72x72.png"/>
                <link rel="apple-touch-icon" sizes="76x76" href="/assets/images/favicon/apple-icon-76x76.png"/>
                <link rel="apple-touch-icon" sizes="114x114" href="/assets/images/favicon/apple-icon-114x114.png"/>
                <link rel="apple-touch-icon" sizes="120x120" href="/assets/images/favicon/apple-icon-120x120.png"/>
                <link rel="apple-touch-icon" sizes="144x144" href="/assets/images/favicon/apple-icon-144x144.png"/>
                <link rel="apple-touch-icon" sizes="152x152" href="/assets/images/favicon/apple-icon-152x152.png"/>
                <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon/apple-icon-180x180.png"/>
                <link rel="icon" type="image/png" sizes="192x192"  href="/assets/images/favicon/android-icon-192x192.png"/>
                <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon/favicon-32x32.png"/>
                <link rel="icon" type="image/png" sizes="96x96" href="/assets/images/favicon/favicon-96x96.png"/>
                <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon/favicon-16x16.png"/>
                <!--                    <link rel="manifest" href="<?php echo HTTP_SUB ?>manifest.json"/>-->
                    <meta name="msapplication-TileColor" content="#ffffff"/>
<!--                    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png"/>-->
                    <meta name="theme-color" content="#ffffff"/>
                    <!-- /Meta -->

                <?php } ?>

                <?php
                if (defined("include_js")) {
                    echo constant("include_js");
                }
                ?>
                <style type="text/css">#main {
                        font-size: 83.01%;
                    }</style>
            <?php }?>
    </head>

    <body>
        <section class="body">
            <div id="modalCommandConfirm" class="modal-block modal-header-color modal-block-danger mfp-hide">
                <section class="panel">
                    <header class="panel-heading">
                        <h2 class="panel-title"><?php echo ss('Command confirmation'); ?></h2>
                    </header>
                    <div class="panel-body">
                        <div class="modal-wrapper">
                            <div class="modal-icon">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                            <div class="modal-text">
                                <p><?php echo ss('Do you really want to execute this command?'); ?></p>
                            </div>
                        </div>
                    </div>
                    <footer class="panel-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-danger modal-confirm"><?php echo ss('Confirm'); ?></button>
                                <button class="btn btn-default modal-dismiss"><?php echo ss('Cancel'); ?></button>
                            </div>
                        </div>
                    </footer>
                </section>
            </div>
            <!-- Top row -->
            <?php require dirname(__FILE__) . '/header/header.top.bar.inc.php'; ?>

            <div class="inner-wrapper">

                <!-- sidebar -->
                <?php require dirname(__FILE__) . '/header/header.left.bar.inc.php'; ?>

                <section role="main" class="content-body">

                    <?php if (!EXTERN) {
                    } ?>
