<div class="col-sm-12">
    <?php if (isset($this->errors)): ?>
        <?php foreach ($this->errors as $error):
            ?>
            <div class="alert alert-danger alert-dismissable alert-hide">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error</strong> <?php echo $error; ?>
            </div>
            <?php
        endforeach;
    else:
        ?>

        <table id="table2" class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th><strong>Username</strong></th>
                    <th><strong>Email</strong></th>
                    <th><strong>Memcached</strong></th>
                    <th><strong>Redis</strong></th>
                    <th><strong>Actions</strong></th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($this->users as $user): ?>
                    <tr>
                        <td><?php echo $user->user ?></td>
                        <td><?php echo $user->email ?></td>
                        <td data-sort="<?php echo $user->MemcachedStatus; ?>"><?php if ($user->MemcachedStatus == 1) echo('<span class="glyphicon glyphicon-ok" style="color:green"></span>');
                    else echo('<span class="glyphicon glyphicon-remove"></span>'); ?></td>
                        <td data-sort="<?php echo $user->RedisStatus; ?>"><?php if ($user->RedisStatus == 1) echo('<span class="glyphicon glyphicon-ok" style="color:green"></span>');
                    else echo('<span class="glyphicon glyphicon-remove"></span>'); ?></td>
                    <td>
                      <button type="button" class="btn btn-warning btn-sm rebuildmemcached" data-user="<?php echo $user->user ?>"><span class="glyphicon glyphicon-ok"></span> Enable Memcached</button>
                      <button type="button" class="btn btn-primary btn-sm rebuildredis" data-user="<?php echo $user->user ?>"><span class="glyphicon glyphicon-ok"></span> Enable Redis</button>
                      <button type="button" class="btn btn-danger btn-sm deactive" data-user="<?php echo $user->user ?>"> <span class="glyphicon glyphicon-remove"></span> Deactive</button>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script type="text/javascript">
            $('#table2').on('click', '.rebuildmemcached', function () {
                var whmUser = $(this).attr('data-user');
                var btn = this;

                $.ajax({
                    data: {
                        action: 'Rebuild',
                        type: 'memcached',
                        username: whmUser
                    },
                    beforeSend: function () {
                        //$("#userDomains tbody").html("");
                    },
                    success: function (response)
                    {
                        location.reload();

                    }
                });
            });
            $('#table2').on('click', '.rebuildredis', function () {
                var whmUser = $(this).attr('data-user');
                var btn = this;

                $.ajax({
                    data: {
                        action: 'Rebuild',
                        type: 'redis',
                        username: whmUser
                    },
                    beforeSend: function () {
                        //$("#userDomains tbody").html("");
                    },
                    success: function (response)
                    {
                        location.reload();

                    }
                });
            });

            $('#table2').on('click', '.deactive', function () {
                var whmUser = $(this).attr('data-user');
                var btn = this;

                $.ajax({
                    data: {
                        action: 'Deactive',
                        username: whmUser
                    },
                    beforeSend: function () {
                        //$("#userDomains tbody").html("");
                    },
                    success: function (response)
                    {
                        location.reload();

                    }
                });
            });



     $(document).ready(function () {
                $('#table2').DataTable({
                    "columnDefs": [
                        { "orderable": false, "targets": 3 }
                      ],
                    "order": [[0, "asc"]],
                });
    });
    </script>
<?php endif; ?>
