<?php
if (isset($this->log))
{

    ?>
    <a href="index.php?controller=Logs" class="btn btn-primary">Back to logs</a>
    <div class="col-xs-12">
        <h4><b>Date</b></h4>
        <pre><?php echo $this->log->created_at; ?></pre>
        <h4><b>Action</b></h4>
        <pre><?php echo $this->log->action; ?></pre>

        <h4><b>Request</b></h4>
        <textarea rows="12" style="width:100%;"><?php echo!empty($this->log->request) ? $this->log->request : "Request is empty" ?></textarea>

        <h4><b>Response</b></h4>
        <textarea rows="12" style="width:100%;"><?php echo!empty($this->log->response) ? $this->log->response : "Response is empty" ?></textarea>

        <?php
        $r = (object)json_decode($this->log->response);
        if (isset($r->errors) || isset($r->error)):

            ?>

            <h4><b>Error message</b></h4>
            <pre><?php
                if (isset($r->errors))
                {
                    echo $r->errors;
                }

                if (isset($r->error) && $r->error == true)
                {
                    if (isset($r->ecode))
                    {
                        echo $r->ecode.": ".$r->error;
                    }
                    else
                    {
                        $statusCode = (isset($r->status_code)) ? $r->status_code : $r->status;
                        echo $statusCode.": ".$r->message;
                    }
                }

                ?></pre>

        <?php endif;

        ?>

    </div>
    <?php
}
else
{

    ?>

    <div class="col-sm-12">
        <table id="table1" class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th><strong>Date</strong></th>
                    <th><strong>Command</strong></th>
                    <th><strong>Status</strong></th>
                    <th><strong>Action</strong></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <form method="POST">
            <input type="hidden" name="controller" value="Logs" />
            <button class="btn btn-primary" id="clearLogs" name="clearLogs">Clear All Logs</button>
        </form>
    </div>
<?php } ?>


<script>
    $(document).ready(function () {



        $('body').on('click', '#clearLogs', function (e) {
            $.ajax({
                data: {
                    'clearLogs': 'clearLogs'
                },
                beforeSend: function () {
                    $('#clearLogs').text('Clearing logs...');
                },
                success: function (data)
                {
                    console.log(data);
                    $("html, body").animate({scrollTop: 0}, "slow");

                    if (data === 'SUCCESS')
                    {
                        $('.alert_custom').html('<div class="alert alert-success">Successfully cleared logs.</div>');
                    } else
                    {
                        $('.alert_custom').html('<div class="alert alert-danger">An error has occured while trying to clear logs.</div>');
                    }
                }
            });
        });
    });
</script>
