<div id="header">
    <div id="logo"><a href="<?php echo $base_url; ?>">BRIM</a></div>
    <div id="userInfo"><?php echo 'Logged in as';?>: <span class="underline"><?php echo $currentUser->name . " " . $currentUser->lastName;?></span> | <a href="<?php echo $base_url; ?>/logout"><?php echo 'Logout';?></a></div>
</div>
<div id="menu">
    <div id="menuHolder" class="clearfix">
        <ul class="clearfix">
            <li><a href="javascript: void(0);" style="background: none; cursor: auto; padding-left: 3px; padding-right: 3px;" class="firstLevel">&nbsp;</a></li>
            <li><a href="<?php echo $base_url; ?>/home" style="background-image: none; padding-right: 10px;" class="firstLevel"><?php echo 'Files Log';?></a></li>
        </ul>
    </div>
</div>
