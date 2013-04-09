<?php
/*
 * zitalk ajax chat
 * 
 * @autor: ZiTAL
 * @version: 3.5.3
 */
class zitalk
{
	public $METHOD = 'POST';

	public $CONFIG = array(
		'LANG' => 'eu',				// html lang
		'AMOUNT' => 100,			// amount of messages when the page is loaded
		'DELETE_RATE' => 20,		// Time to delete online users in seconds
		'TIME_FORMAT' => 'H:i:s',	// Time format
		'DATE_FORMAT' => 'Y/m/d',	// Date format
		'TEXT2LINK' => true,		// Change text links to valid links
		'ENCODING' => 'utf-8',		// html encoding
		'TEMPLATE' => 'zitalk.tpl',	// template to display the chat
		'MAIL' => 'mail@host.com'	// contact mail
	);

	public $INSTALL = array(
		'INSTALL' => false,			// change to true to install
		'UNINSTALL' => false		// change to true to uninstall
	);

	public $CONFIGDB = array(
		'PERSISTENCE' => true,		// persistence database connection
		// connection settings
		'HOST' => 'localhost',
		'USER' => 'user',
		'PASSWD' => 'password',
		'NAME' => 'DB_name',
		// table config
		'DB_ENGINE' => 'InnoDB',
		'DB_CHARSET' => 'utf8',
		'DB_COLLATION' => 'utf8_spanish_ci',
		// CHAT table structure
		'TABLE' => 'chat',
		'TABLE_ID' => 'id',
		'TABLE_NAME' => 'name',
		'TABLE_COMMENT' => 'comment',
		'TABLE_DATE' => 'data',
		// ONLINE USERS table structure
		'TABLE_OU' => 'chat_ou',
		'TABLE_OU_ID' => 'id',
		'TABLE_OU_NAME' => 'name',
		'TABLE_OU_DATE' => 'data'
	);

	// Javascript vars to load in template
	public $CONFIGJS = array(
		'PAGE' => '',					// ajax petitions page
		'nameMaxLength' => 12,			// Name max length
		'REFRESH_RATE' => 2000,			// Chat refresh rate in miliseconds
		'REFRESH_RATE_OU' => 10000,		// Online Users refresh rate in miliseconds
		'SOUND' => 'true',				// sound config
		'AUTOSCROLL' => 'true'			// chat's autoscroll
	);

	/*
	 * Method to run zitalk
	 * 
	 */
	public function run()
	{
		$this->session('start');
		$this->dbconnect($this->CONFIGDB['HOST'], $this->CONFIGDB['USER'], $this->CONFIGDB['PASSWD'], $this->CONFIGDB['NAME'], $this->CONFIGDB['PERSISTENCE']);
		$this->CONFIG['ENCODING'] = $this->getEncoding();
		$request = $this->getRequest();
		if($request)
		{
			$reqVars = $this->getRequestValues($request);
			$this->runMethod($reqVars);
		}
		$this->showTemplate($this->CONFIG['TEMPLATE']);
	}

	/*
	 * Method to switch the ajax method
	 * 
	 * @return none
	 * @params array $reqVars['action'] to execute AJAX method and $reqVars['data'] values to work with in method
	 */
	private function runMethod($reqVars)
	{
		switch($reqVars['action'])
		{
			case 'write':
				$this->write($reqVars['data']);
				break;
			case 'read':
				$this->read($reqVars['data']);
				break;
			case 'maxId':
				// add comet
/*				
				$time = ini_get('max_execution_time');
				set_time_limit(0);				
*/				
				$maxid = $this->maxId($this->CONFIGDB['TABLE'], $this->CONFIGDB['TABLE_ID']);
/*				
				while($maxid===$reqVars['data'])
				{
					sleep($this->CONFIG['REFRESH_RATE']);
					$maxid = $this->maxId($this->CONFIGDB['TABLE'], $this->CONFIGDB['TABLE_ID']);
				}
*/				
				echo $maxid;
//				set_time_limit($time);
				unset($time, $maxid, $reqVars);				
				exit();
				break;
			case 'login':
				$this->login($reqVars['data']);
				break;
			case 'logout':
				$this->logout();
				break;
			case 'readOu':
				$this->readOUsers();
				break;
			case 'deleteSession':
				$this->session('destroy');
				exit();
				break;
			case 'mail':
				$this->sendMail($reqVars['data']);
				exit();
				break;				
		}
	}
	
	/*
	 * Method to switch ajax method: GET, POST, REQUEST.
	 * 
	 * @return array $request
	 */
	private function getRequest()
	{
		$request = array();
		$method = strtoupper(trim($this->METHOD));
		switch($method)
		{
			case 'POST':
				if(isset($_POST))
					$request = $_POST;
				break;
			case 'GET':
				if(isset($_GET))
					$request = $_GET;
				break;
			case 'REQUEST':
				if(isset($_REQUEST))
					$request = $_REQUEST;
				break;
		}
		return $request;
	}
	
	/*
	 * Method to get the request values.
	 * 
	 * @return array $result
	 * @params array $request 
	 */
	private function getRequestValues($request)
	{
		$action = (isset($request['action']))?$request['action']:'failed';
		$data = (isset($request['data']))?$request['data']:NULL;
		$result = array('action' => $action, 'data' => $data);
		return $result;
	}

	/*
	 * AJAX Method to login in chat
	 * 
	 * @params string $data 
	 */
	private function login($data)
	{
		header("Cache-Control: no-store, no-cache, must-revalidate");
		$result = $this->checkUser($data);
		if($result=='true')
		{
			$this->setUser($data);
			$this->insertOUser($data);
		}
		echo $result;
		exit();
	}

	/*
	 * AJAX Method to write comments in DDBB
	 * 
	 * @params string $data
	 */
	private function write($data)
	{
		$name = $this->getUser();
		if($name)
		{
			$max = $this->maxId($this->CONFIGDB['TABLE'], $this->CONFIGDB['TABLE_ID'])+1;
			$date = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));

			$data = preg_replace("/'/", "\'", $data);
			$insert = "insert into ".$this->CONFIGDB['TABLE']." (".$this->CONFIGDB['TABLE_ID'].", ".$this->CONFIGDB['TABLE_NAME'].", ".$this->CONFIGDB['TABLE_COMMENT'].", ".$this->CONFIGDB['TABLE_DATE'].") values(".$max.", '".$name."', '".$data."', ".$date.")";
			if(trim($data)!=NULL)
				$insert = mysql_query($insert);
		}
		exit();
	}

	/*
	 * AJAX method to read last comments from database
	 * 
	 * @params integer $data
	 */
	private function read($data)
	{
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Content-type: application/json; charset=\"".$this->CONFIG['ENCODING']."\"",true);
		$max = $this->maxId($this->CONFIGDB['TABLE'], $this->CONFIGDB['TABLE_ID']);
		if($data==0)
		{	
			$start = $max-$this->CONFIG['AMOUNT'];
			$amount = $this->CONFIG['AMOUNT'];
			if($start<0)
				$start = 0;
		}
		else
		{
			$start = $data;
			$amount = $max-$data;
		}
		$select = "select ".$this->CONFIGDB['TABLE_ID'].", ".$this->CONFIGDB['TABLE_NAME'].", ".$this->CONFIGDB['TABLE_COMMENT'].", ".$this->CONFIGDB['TABLE_DATE']." from ".$this->CONFIGDB['TABLE']." order by ".$this->CONFIGDB['TABLE_ID']." asc limit ".$start.",".$amount;
		$select = mysql_query($select);

		$result = array();
		while($row = mysql_fetch_array($select, MYSQL_ASSOC))
		{
			$result[] = $row;
		}
		echo json_encode($result);
		exit();
	}

	/*
	 * Method to insert user in online users table
	 * 
	 * @param string $data
	 */
	private function insertOUser($data)
	{
		$max = $this->maxId($this->CONFIGDB['TABLE_OU'], $this->CONFIGDB['TABLE_OU_ID'])+1;
		$date = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
		$insert = "insert into ".$this->CONFIGDB['TABLE_OU']." (".$this->CONFIGDB['TABLE_OU_ID'].", ".$this->CONFIGDB['TABLE_OU_NAME'].", ".$this->CONFIGDB['TABLE_OU_DATE'].") values (".$max.",'".$data."',".$date.")";
		$insert = mysql_query($insert);
	}

	/*
	 * AJAX read online users method
	 */
	private function readOUsers()
	{
		header("Content-type: application/json; charset=\"".$this->CONFIG['ENCODING']."\"",true);

		$user = $this->getUser();
		if($user)
			$this->insertOUser($user);

		$this->deleteOUsers();

		$result = array();

		$select = "select distinct(".$this->CONFIGDB['TABLE_OU_NAME'].") from ".$this->CONFIGDB['TABLE_OU']." order by ".$this->CONFIGDB['TABLE_OU_NAME']." asc";
		$select = mysql_query($select);
		
		while($row = @mysql_fetch_array($select, MYSQL_ASSOC))
		{
			$result[] = $row['name'];
		}
		echo json_encode($result);
		exit();
	}
	
	/*
	 * Delete Online users depending DELETE_RATE method
	 */
	private function deleteOUsers()
	{
		$date = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
		$date = intval($date)-intval($this->CONFIG['DELETE_RATE']);
		$delete = "delete from ".$this->CONFIGDB['TABLE_OU']." where ".$this->CONFIGDB['TABLE_OU_DATE']."<".$date;
		$delete = mysql_query($delete);
	}

	/*
	 * Method to see if the user is online
	 */
	private function checkUser($data)
	{
		$select = "select count(".$this->CONFIGDB['TABLE_OU_NAME'].") from ".$this->CONFIGDB['TABLE_OU']." where ".$this->CONFIGDB['TABLE_OU_NAME']." like '".$data."' limit 1";
		$select = mysql_query($select);
		$count = mysql_result($select, 0, 0);
		if($count==0)
			$result = 'true';
		else
			$result = 'false';
		return $result;
		@mysql_free_result($select);
	}
	
	/*
	 * AJAX method to logout user
	 */
	private function logout()
	{
		$user = $this->getUSer();
		if($user)
		{
			$delete = "delete from ".$this->CONFIGDB['TABLE_OU']." where ".$this->CONFIGDB['TABLE_OU_NAME']." like '".$user."'";
			$delete = mysql_query($delete);
		}
		$this->session('destroy');
		exit();
	}
	
	/*
	 * Method to get last ID from table
	 */
	private function maxId($table, $id)
	{
		$select = "select max(".$id.") from ".$table;
		$select = mysql_query($select);
		$id = mysql_result($select,0,0);
		mysql_free_result($select);
		return $id;
	}
	
	/*
	 * Method to set username
	 */
	private function setUser($data)
	{
		$_SESSION['zitalk_name'] = $data;
	}
	
	/*
	 * Method to get username
	 * 
	 * @return string $name
	 */
	private function getUser()
	{
		$name = (isset($_SESSION['zitalk_name']))?$_SESSION['zitalk_name']:NULL;
		return $name;
	}
	
	/*
	 * Method to get encoding depending browser
	 * 
	 * @return string $encoding
	 */
	private function getEncoding()
	{
		$encoding = $this->CONFIG['ENCODING'];
		$this->CONFIGJS['BROWSER'] = $this->getBrowser();
		
		if($this->CONFIG['ENCODING']!='ISO-8859-15')
		{
		
			if($this->CONFIGJS['BROWSER']=='msie')
				$encoding = 'ISO-8859-15';
			else
				$encoding = 'utf-8';
		}		
		return $encoding;
	}
	
	/*
	 * Method to get browser type
	 * 
	 * @return string $browser
	 */
	private function getBrowser()
	{
		if(strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
			$browser = 'msie';
		else
			$browser = 'default';
		return $browser;
	}
	
	/*
	 * Method to manage session
	 * 
	 * @params string $mode
	 */
	private function session($mode)
	{
		switch($mode)
		{
			case 'start':
				session_start();
				break;
			case 'destroy':
				session_destroy();
				break;
		}
	}
	
	/*
	 * Method to set output template
	 * 
	 * @params string $tpl
	 */
	private function showTemplate($tpl)
	{
		include("templates/".$tpl);
	}
	
	/*
	 * Method to connect to DDBB
	 * 
	 * @params string $host, $user, $passwd, $db, $persistence
	 */
	private function dbconnect($host, $user, $passwd, $db, $persistence)
	{
		if($persistence)
			$link = @mysql_pconnect($host, $user, $passwd);
		else
			$link = @mysql_connect($host, $user, $passwd);

		if(!($link))
		{
			echo "Error connecting to DDBB.";
			exit();
		}
		if(!mysql_select_db($db, $link))
		{
			echo "Error in DDBB selection.";
			exit();
		}
	}
	
	/*
	 * Public function to install DDBB configuration
	 */
	public function install()
	{
		if($this->INSTALL['INSTALL'])
		{
			$this->dbconnect($this->CONFIGDB['HOST'], $this->CONFIGDB['USER'], $this->CONFIGDB['PASSWD'], $this->CONFIGDB['NAME'], $this->CONFIGDB['PERSISTENCE']);
			$query = "
				CREATE TABLE `".$this->CONFIGDB['NAME']."`.`".$this->CONFIGDB['TABLE']."` (
				`".$this->CONFIGDB['TABLE_ID']."` int NOT NULL,
				`".$this->CONFIGDB['TABLE_NAME']."` text NOT NULL,
				`".$this->CONFIGDB['TABLE_COMMENT']."` text NOT NULL,
				`".$this->CONFIGDB['TABLE_DATE']."` int NOT NULL,
				PRIMARY KEY  (`".$this->CONFIGDB['TABLE_ID']."`))
				ENGINE=".$this->CONFIGDB['DB_ENGINE']." CHARACTER SET ".$this->CONFIGDB['DB_CHARSET']." COLLATE ".$this->CONFIGDB['DB_COLLATION'];
			$query2 = $query;
			if(mysql_query($query))
				echo "Query OK: ".$query2;
			else
				echo "Error Executing query: ".$query2."<br /><br />Check configuration in config.php";
			$query = "
					CREATE TABLE `".$this->CONFIGDB['NAME']."`.`".$this->CONFIGDB['TABLE_OU']."` (
					`".$this->CONFIGDB['TABLE_OU_ID']."` INT NOT NULL ,
					`".$this->CONFIGDB['TABLE_OU_NAME']."` TEXT NOT NULL ,
					`".$this->CONFIGDB['TABLE_OU_DATE']."` INT NOT NULL ,
					PRIMARY KEY ( `".$this->CONFIGDB['TABLE_OU_ID']."` )
					) ENGINE=".$this->CONFIGDB['DB_ENGINE']." CHARACTER SET ".$this->CONFIGDB['DB_CHARSET']." COLLATE ".$this->CONFIGDB['DB_COLLATION'];
			$query2 = $query;
			if(mysql_query($query))
				echo "Query OK: ".$query2;
			else
				echo "Error Executing query: ".$query2."<br /><br />Check configuration in config.php";
		}
		else
			echo 'You must change INSTALL constant to true';
	}
	
	/*
	 * Public function to uninstall DDBB config
	 */
	public function uninstall()
	{
		if($this->INSTALL['UNINSTALL'])
		{
			$this->dbconnect($this->CONFIGDB['HOST'], $this->CONFIGDB['USER'], $this->CONFIGDB['PASSWD'], $this->CONFIGDB['NAME'], $this->CONFIGDB['PERSISTENCE']);

			$query = "DROP TABLE `".$this->CONFIGDB['NAME']."`.`".$this->CONFIGDB['TABLE']."`";
			$query2 = $query;
			if(mysql_query($query))
				echo "Query OK: ".$query2;
			else
				echo "Error Executing query: ".$query2."<br /><br />Check configuration file ;)";
			$query = "DROP TABLE `".$this->CONFIGDB['NAME']."`.`".$this->CONFIGDB['TABLE_OU']."`";
			$query2 = $query;
			if(mysql_query($query))
				echo "Query OK: ".$query2;
			else
				echo "Error Executing query: ".$query2."<br /><br />Check configuration file ;)";
		}
		else
			echo 'You must change UNINSTALL constant to true in config.php';
	}
	private function sendMail($data)
	{
		$libs = array();
		$libs[] = include('libs/phpmailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->AddAddress($this->CONFIG['MAIL']);
		$mail->Subject = 'zitalk registry';
		$mail->MsgHTML($data);
		if(!$mail->Send())
			echo "Error: ".$mail->ErrorInfo;
		else
			echo 'ok';	
		unset($libs, $mail, $data);
	}
}
?>