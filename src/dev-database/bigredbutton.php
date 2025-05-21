<?php
$modul="red_button";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view red_button
GRGR(29);

$n4a['red_button.php'] = '' . ss('Code Replacement') . '';
require("inc/header.inc.php");

?>
<header class="page-header">
<!--    <h2>The Big Red Button</h2>-->
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span>The Big Red Button</span></li>
        </ol>
        <a class="sidebar-right-toggle" data-open="sidebar-right" ><i class="fa fa-chevron-left"></i></a>
    </div>
</header>

<?php require 'red_button/index.php'?>;



<?php require 'inc/footer.inc.php';?>