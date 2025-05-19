<?php
if ($includeFromHomeDir) {
	$dir = '';
} else {
	$dir = '../';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
        <head>
                <title>
                        <?php echo TITLE?>
                </title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <link rel="stylesheet" type="text/css" href="<?php echo $dir?>css/pm.css" />
                         <link rel="stylesheet" type="text/css" href="<?php echo $dir?>css/pso.css" />
                        <link rel="stylesheet" type="text/css" href="<?php echo $dir?>css/jquery-ui.css" />
                        <link rel="stylesheet" type="text/css" href="<?php echo $dir?>css/jquery.autocomplete.css" />
                        <link rel="stylesheet" type="text/css" href="<?php echo $dir?>assets/vendor/fancybox/jquery.fancybox-1.3.4.css" />
                        <link rel="stylesheet" type="text/css" href="<?php echo $dir?>assets/vendor/jquery-treeview/jquery.treeview.css" />
                        <link rel="stylesheet" type="text/css" href="<?php echo $dir?>js/tiptip/tipTip.css" />
        </head>

        <body>
        
			<script src="<?php echo $dir?>js/jquery-1.4.3.min.js"></script>
			<script src="<?php echo $dir?>assets/vendor/jquery.fancybox-1.3.4.js"></script>
			<script src="<?php echo $dir?>js/jquery-ui.pso.js"></script>
			<script src="<?php echo $dir?>assets/vendor/jquery-treeview/jquery.treeview.js"></script>
			<script src="<?php echo $dir?>js/jquery.bgiframe.min.js"></script>
			<script src="<?php echo $dir?>js/jquery.autocomplete.js"></script>
			<script src="<?php echo $dir?>js/tiny_mce/jquery.tinymce.js"></script>
			<script src="<?php echo $dir?>js/tiptip/jquery.tipTip.minified.js"></script>
			<script>
			$(document).ready(function(){
				$(".tooltip").tipTip({ defaultPosition:'top' });
			});
			
			</script>
            
            <div id="main">
            	<!--<div id="head" style="height:40px; padding-top:20px; padding-left:20px;"><img src="<?php echo $dir?>images/if-pso.png">
                    <noscript><span class="error">Please activate JavaScritpt</span>, without JavaScript the Application colud be faulty.</noscript>
                    </div>-->
                    <div id="content">