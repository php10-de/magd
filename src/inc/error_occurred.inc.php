<div class="modal-block modal-header-color modal-block-danger">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title"><?php echo sss("Error"); ?></h2>
        </header>
        <div class="panel-body">
            <div class="modal-wrapper">
                <div class="modal-icon">
                    <i class="fa fa-times-circle"></i>
                </div>
                <div class="modal-text">
                    <h4><?php echo sss("Error"); ?></h4>
                    <p><?php echo $headerError; ?></p>
                </div>
            </div>
        </div>
        <footer class="panel-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <a href="start.php" class="btn btn-danger"><i class="fa fa-home" style="color:white;"></i> <?php echo sss("Return to home"); ?></a>
                </div>
            </div>
        </footer>
    </section>
</div>