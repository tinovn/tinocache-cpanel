<?php if ($this->ac == 'memcached'): ?>
  <div id="addSuccess" class="alert alert-success">
      <span class="glyphicon glyphicon-ok-sign"></span>
      <div class="alert-message">
          <strong>Success</strong>: Memcached cache has been activated, you can start using        </div>
  </div>
<?php endif; ?>
<?php if($this->ac == 'redis'): ?>
    <div id="addSuccess" class="alert alert-success">
        <span class="glyphicon glyphicon-ok-sign"></span>
        <div class="alert-message">
            <strong>Success</strong>: Redis cache has been activated, you can start using        </div>
    </div>
<?php endif; ?>
<?php if($this->ac == 'dememcached'): ?>
    <div id="addSuccess" class="alert alert-warning">
        <span class="glyphicon glyphicon-ok-sign"></span>
        <div class="alert-message">
            <strong>Success</strong>: Memcached has been disable</div>
    </div>
<?php endif; ?>
<?php if($this->ac == 'deredis'): ?>
    <div id="addSuccess" class="alert alert-warning">
        <span class="glyphicon glyphicon-ok-sign"></span>
        <div class="alert-message">
            <strong>Success</strong>: Redis has been disable</div>
    </div>
<?php endif; ?>




    <div class="section">
      <table class="sortable table table-striped responsive-table">
        <thead>
          <tr>
            <th>Type cache</th>
            <th>Unix Socket</th>
            <th>Memory</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Memcached</td>
            <?php if ($this->memcachedStatus == 'Enable'): ?>
              <td>/home/<?php echo $this->username; ?>/.tngcache/memcached.sock</td>
              <?php else: ?>
                <td>N/A</td>
            <?php endif; ?>

            <td><?php echo $this->memcachedMemory; ?></td>
            <td><?php echo $this->memcachedStatus; ?></td>
            <td>
              <?php if ($this->memcachedStatus == 'Enable'): ?>
                <form method="post" style="float:left">
                    <input type="hidden" name="action" value="deactivate">
                    <input type="hidden" name="type" value="memcached">
                    <button type="submit" class="btn btn-outline-primary btn-sm">Deactive</button>
                </form>
                <form method="post">
                    <input type="hidden" name="action" value="rebuild">
                    <input type="hidden" name="type" value="memcached">
                    <button type="submit" class="btn btn-outline-danger btn-sm">Rebuild</button>
                </form>
                <?php else: ?>
              <form method="post">
                  <input type="hidden" name="action" value="activate">
                  <input type="hidden" name="type" value="memcached">
                  <button type="submit" class="btn btn-outline-danger btn-sm">Active</button>
              </form>
                <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td>Redis</td>
            <?php if ($this->redisStatus == 'Enable'): ?>
              <td>/home/<?php echo $this->username; ?>/.tngcache/redis.sock</td>
              <?php else: ?>
                <td>N/A</td>
            <?php endif; ?>
            <td><?php echo $this->redisMemory; ?></td>
            <td><?php echo $this->redisStatus; ?></td>
            <td>

              <?php if ($this->redisStatus == 'Enable'): ?>
                <form method="post" style="float:left">
                    <input type="hidden" name="action" value="deactivate">
                    <input type="hidden" name="type" value="redis">
                    <button type="submit" class="btn btn-outline-primary btn-sm">Deactive</button>
                </form>
                <form method="post">
                    <input type="hidden" name="action" value="rebuild">
                    <input type="hidden" name="type" value="redis">
                    <button type="submit" class="btn btn-outline-danger btn-sm">Rebuild</button>
                </form>
                <?php else: ?>
                <form method="post">
                    <input type="hidden" name="action" value="activate">
                    <input type="hidden" name="type" value="redis">
                    <button type="submit" class="btn btn-outline-danger btn-sm">Active</button>
                </form>
                  <?php endif; ?>

            </td>
          </tr>
        </tbody>
      </table>
    </div>
