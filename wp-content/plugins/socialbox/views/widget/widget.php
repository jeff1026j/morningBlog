<?php if(!defined('ABSPATH')) die('Direct access is not allowed.');


/*
 * SocialBox 1.7.3
 * Copyright by Jonas Döbertin
 * Available only at CodeCanyon: http://codecanyon.net/item/socialbox-social-wordpress-widget/627127
 */


?>

<!-- SocialBox Widget -->
<li class="socialbox-widget socialbox-style-<?php echo $theme['slug'] ?>" <?php if($forcedWidgetWidth) echo "style=\"width: {$forcedWidgetWidth}px !important\"" ?>>
	

		<?php foreach($networks as $network): ?>

			<a href="<?php echo $network['link']; ?>" title="<?php echo $network['buttonHint']; ?>"
       class="omc-social-media-icon large<?php if ($i == 3 || $i == 6) {
           echo(' no-right');
       } ?>" <?php if ($newWindow) echo 'target="_blank"'; ?>>
        <span class="omc-<?php echo $network['name']; ?> omc-icon"><span></span></span>
        <span class="omc-following-info"><?php
           	if ($network['name'] == 'Feedburner') { 
          		echo('RSS');
          	} else {           
           		 if ($network['count'] !== false) {
			   		 echo number_format($network['count']);
			   		 }
            } ?><br/><strong><?php echo $network['metric']; ?></strong></span>
    </a>

		<?php endforeach ?>

<br class="clear"/>


</li>
<!-- End SocialBox Widget -->
