<?php
if (!isset($this->abort)) {
    return;
} 
?> 

<div class="alert alert-danger">         
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>  
    <strong>Stop! </strong> <?php echo $this->abort; ?> 
</div>

