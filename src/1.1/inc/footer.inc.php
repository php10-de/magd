            </div>
            <aside id="sidebar-right" class="sidebar-right">
                <div class="nano">
                    <div class="nano-content">
                        
                        <a href="#" class="mobile-close visible-xs">
                            Collapse <i class="fa fa-chevron-right"></i>
                        </a>

                        <div class="sidebar-right-wrapper">
                            
                            <div class="sidebar-widget widget-friends">
                                <h6 style="padding-top:20px;padding-left:20px;padding-right:20px;">Actions</h6>
                                <ul class="widget-list nav-main">
                                    <?php
                                        if($n4a){
                                            foreach ($n4a as $key => $value) { ?>
                                                <li>
                                                    <a style="display:block;" href="<?php echo $key; ?>">
                                                        <span><?php echo $value; ?></span>
                                                    </a>
                                                </li>

                                            <?php }
                                        }
                                    ?>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </aside>
        </section>
        
        <!-- replace:js -->
        <script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/js/footer.common.js?version=<?php echo $front_version; ?>"></script>
        <!-- /replace:js -->


        <?php

            if (isset($_GET['ok'])) {
                $headerMsg = $_GET['ok'];
            }

            if (!isset($headerMsg)) {$headerMsg = null;}
            if (!isset($headerError)) {$headerError = null;}

            if ($headerMsg) {
                $color = 'green';
            } elseif ($headerError) {
                $color = 'red';
                $headerMsg = $headerError;
            }

            if ($headerMsg || $headerError) {
                $cnt = strlen($headerMsg);
                $fontSize = '1.5em';
                $imgSize = '20';
                if ($cnt > 120) {
                    $l = $cnt/3;
                    $fontSize = '0.95em';
                    $imgSize = '10';
                    $lbPos = strpos($headerMsg, ' ', $l);
                    $msg1 = substr($headerMsg, 0, $lbPos);
                    $lbPos2 = strpos($headerMsg, ' ', ($l*2));
                    $msg2 = substr($headerMsg, $lbPos, $l+1);
                    $msg3 = substr($headerMsg, $lbPos2);
                    $headerMsg = $msg1 . '<br>' . $msg2 . '<br>' . $msg3;
                } else if ($cnt > 45) {
                    $fontSize = '1.22em';
                    $imgSize = '15';
                    $lbPos = strpos($headerMsg, ' ', ($cnt/2));
                    $msg1 = substr($headerMsg, 0, $lbPos);
                    $msg2 = substr($headerMsg, $lbPos);
                    $headerMsg = $msg1 . '<br>' . $msg2;
                }
            }
            if (isset($homeMsg)) {
                $headerMsg = $homeMsg;
            }

            if ($headerMsg) { ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        new PNotify({
                            title: '<?php echo ($color == 'red' ? 'Error' : ( $color == 'green' ? 'Success' : 'Info') ) ?>',
                            text: '<?php echo $headerMsg?>',
                            type: '<?php echo ($color == 'red' ? 'error' : ( $color == 'green' ? 'success' : 'info') ) ?>',
                            shadow: true
                            <?php if($color == 'red') { ?>
                            ,hide: false,
                            buttons: {
                                    sticker: false
                            }
                            <?php } ?>
                        });
                    });
                </script>
            <?php } ?>
                <script type="text/javascript">
                    var entity = '<?php echo $modul; ?>';
                </script>

    </body>
</html>

<?php
    if (file_exists('cache_end.php')) {
        require_once('cache_end.php');
    }
?>

