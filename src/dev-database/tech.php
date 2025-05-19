<?php
    $modul = "tech";

    require("inc/req.php");
    require("inc/header.inc.php");

    //RR(2);
?>
<header class="page-header">
    <!--    <h2></h2>-->

    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="start.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
        </ol>

        <a class="sidebar-right-toggle"><i class="fa fa-chevron-left hide"></i></a>
    </div>
</header>
<h3 class="mt-none mb-lg"><?php sss('Features') ?></h3>
<div class="row">
    <div class="col-md-6">
        <section class="panel panel-featured">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-down"></a>
                </div>

                <h2 class="panel-title"><?php sss('General') ?></h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-none">
                        <tbody>
                            <tr><td><?php sss('Nice design') ?></td></tr>
                            <tr><td><?php sss('Fast · Using various forms of caching') ?></td></tr>
                            <tr><td><?php sss('Secure · No XSS, SQL-Injection and Parameter Manipulation') ?></td></tr>
                            <tr><td><?php sss('Five level deep navigation') ?></td></tr>
                            <tr><td><?php sss('Filterable & sortable lists') ?></td></tr>
                            <tr><td><?php sss('XLS export') ?></td></tr>
                            <tr><td><?php sss('Inline edit') ?></td></tr>
                            <tr><td><?php sss('Auto suggest for search fields') ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <section class="panel panel-featured">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-down"></a>
                </div>

                <h2 class="panel-title"><?php sss('User Management') ?></h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-none">
                        <tbody>
                            <tr><td><a href="gr.php"><?php sss('User group managment') ?></a></td></tr>
                            <tr><td><?php sss('Rights management on group level') ?></td></tr>
                            <tr><td><?php sss('Rights management on user level') ?></td></tr>
                            <tr><td><a href="log.php"><?php sss('Changelog') ?></a></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <section class="panel panel-featured">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-down"></a>
                </div>

                <h2 class="panel-title"><?php sss('Multilanguage support') ?></h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-none">
                        <tbody>
                            <tr><td><?php sss('Support of multiple languages') ?></td></tr>
                            <tr><td><?php sss('E-Mail notification when untranslated string is used') ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <section class="panel panel-featured">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-down"></a>
                </div>

                <h2 class="panel-title"><?php sss('Administration & Development') ?></h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-none">
                        <tbody>
                            <tr><td><a href="settings.php"><?php sss('Settings Page') ?></a></td></tr>
                            <tr><td><a href="sql.php" ><?php sss('SQL management and execution') ?></a></td></tr>
                            <tr><td><?php sss('Selenium Tests') ?></td></tr>
                            <tr><td><a href="/bigredbutton.php" ><?php sss('Big Red Button Source Code Generator') ?></a></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<?php require("inc/footer.inc.php"); ?>
