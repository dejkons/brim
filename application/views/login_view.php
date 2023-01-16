<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $base;?>/css/style.css?v=1" />
</head>

<body class="loginBody">
	<div id="header">
		BRIM Financial
    </div>
    <div id="loginpanel">
    	<form method="post">
        <input name="login" type="hidden" value="1" />
            <table width="310" cellspacing="10">
                <tr>
                    <td colspan="2" style="color: #F00;"><?php echo $errorMessage;?></td>
                </tr>
                <tr>
                    <td width="150" align="right">User name:</td>
                    <td width="122"><input type="text" name="username" class="inputField" style="width: 180px;" /></td>
                </tr>
                <tr>
                    <td width="150" align="right">Password:</td>
                    <td width="122"><input type="password" name="password" class="inputField" style="width: 180px;" /></td>
                </tr>
                <tr>
                    <td width="150">&nbsp;</td>
                    <td width="122" align="right"><input type="submit" name="submit" value="Log In" class="submitButton" /></td>
                </tr>
                        
            </table> 
        </form>
	</div>
</body>
</html>
