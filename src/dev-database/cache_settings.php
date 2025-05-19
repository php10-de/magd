<?php
$modul="sql";

require("inc/req.php");
require("inc/header.inc.php");
require("inc/fsc.class.php");

/*** Rights ***/
// For Technicians only
RR(2);

$cache = new Fsc();
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
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-down"></a>
                </div>

                <h2 class="panel-title">Control</h2>
            </header>
            <div class="panel-body">
                <?php
                    if (isset($_REQUEST['action'])) {
                        $fscMethod = $_REQUEST['action'].'Action';
                        if (method_exists($cache, $fscMethod)) {
                            $cache->$fscMethod();
                        }
                    } else {
                        $cache->showMenu();
                    }

                   
                    ?>

            </div>
        </section>
    </div>
</div>

<?php  require("inc/footer.inc.php"); ?>
