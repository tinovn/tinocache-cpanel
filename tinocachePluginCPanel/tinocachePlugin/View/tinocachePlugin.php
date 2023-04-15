<link rel="stylesheet" type="text/css" href="../tinocachePlugin/View/Assets/tinocachePlugin.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
if ($('#icon-').length) {
        $('#icon-').attr('class', 'page-icon icon-tinocachePlugin');
    }
</script>
<div class="container-fluid">
    <div id="additionalErrors"></div>
    <?php

    if (isset($this->result->errors)):
        foreach ($this->result->errors as $error):

            ?>
            <div class="alert alert-danger alert-dismissable alert-hide">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error</strong> <?php echo $error; ?>
            </div>
            <?php
        endforeach;
    endif;
if (isset($this->result->success)): ?>
        <div class="alert alert-success alert-dismissable alert-hide">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success</strong> <?php echo $this->result->success; ?>
        </div>
    <?php endif; ?>

    <div class="tab-content content-margin">
        <div id="home" class="tab-pane fade in active">
            <?php
                require './View/Subpages/service.php';
            ?>
        </div>

    </div>
</div>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>