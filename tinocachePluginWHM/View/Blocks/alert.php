<?php 
if( !isset($this->alert) ){
    return; 
    
} 

foreach( $this->alert as $type => $message ): ?>     
    <div class="alert alert-<?php echo $type; ?>">    
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>   
        <strong><?php echo ucfirst ( $type ); ?>! </strong>  <?php echo $message;?>   
    </div> 
<?php endforeach; ?>

