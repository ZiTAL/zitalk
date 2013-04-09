<?php
	include("zitalk.class.php");

	$zitalk = new zitalk();

    $zitalk->CONFIG = array(
            'LANG' => 'eu',                 // html lang
            'AMOUNT' => 20,                 // amount of messages
            'DELETE_RATE' => 10,            // Time to delete online users 
            'TIME_FORMAT' => 'H:i:s',       // Time format
            'DATE_FORMAT' => 'Y/m/d',       // Date format
            'TEXT2LINK' => true,            // Change text links to valid l
            'ENCODING' => 'utf-8',          // html encoding
            'TEMPLATE' => 'tuktuk.tpl',     // template to display the chat
            'MAIL' => 'zitalman@gmail.com'  // contact mail
    );	
	
    $zitalk->CONFIGDB = array(
            'PERSISTENCE' => true,
            'HOST' => 'localhost',
            'USER' => 'user',
            'PASSWD' => 'passwd',
            'NAME' => 'DB_name',
            'DB_ENGINE' => 'InnoDB',
            'DB_CHARSET' => 'utf8',
            'DB_COLLATION' => 'utf8_spanish_ci',
            'TABLE' => 'chat',
            'TABLE_ID' => 'id',
            'TABLE_NAME' => 'name',
            'TABLE_COMMENT' => 'comment',
            'TABLE_DATE' => 'data',
            'TABLE_OU' => 'chat_ou',
            'TABLE_OU_ID' => 'id',
            'TABLE_OU_NAME' => 'name',
            'TABLE_OU_DATE' => 'data'
    );

	// to run chat
	$zitalk->run();
	
	// to install
	//$zitalk->install();
	
	// to uninstall
	//$zitalk->uninstall();
?>
