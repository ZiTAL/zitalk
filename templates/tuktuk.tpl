<!DOCTYPE html>
<html lang="<?php echo $this->CONFIG['LANG']; ?>">
<head>
    <meta charset="utf-8" >
	<meta name="keywords" content="zital, ajax, php, mysql, javascript, tuktuk" />
	<meta name="description" content="ZiTALK: PHP, Javascript, AJAX Chat" />

	<!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Normalize for reset css -->
	<link type="text/css" rel="stylesheet" href="resources/css/normalize.css" />

    <!-- TUKTUK -->
    <link type="text/css" rel="stylesheet" href="resources/css/tuktuk/tuktuk.css">
    <link type="text/css" rel="stylesheet" href="resources/css/tuktuk/tuktuk.icons	.css">

    <!-- theme site -->
    <!--
    <link type="text/css" rel="stylesheet" href="resources/css/tuktuk/theme.site.css">
	-->
    <link type="text/css" rel="stylesheet" href="resources/css/tuktuk/tuktuk.theme.css">

<!--    
	<script src="http://jsconsole.com/remote.js?A023FCAD-4AA9-48A7-A7EB-4BC6FEB61688"></script>
-->	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="resources/js/zitalk-jquery.js"></script>	
	<script type="text/javascript">
	$(window).on('load', function()
	{
		zitalk.main();	
	});
	</script>
<!--	
	<script type="text/javascript" src="resources/js/zajax.js"></script>
	<script type="text/javascript" src="resources/js/zitalk.js"></script>
	<script type="text/javascript">	
/*
	window.onload = function()
	{
		zitalk.main();
	};
*/
	</script>
-->	
</head>
<body>
	<!-- ========================== HEADER ========================== -->
	<!--
	<header class="margin_top margin_bottom">
	-->
	<header>
		<div class="row">
			<div class="column_10 center">
				<h4 class="text color theme"><span class="text bold">zital</span>k</h4>
			</div>
		</div>
	</header>

	<!-- LOGIN -->
	<section class="bck light login">
		<div class="row">
			<div class="column_10 center">
				<div id="stylized-form">
					<form>
						<fieldset>
							<input type="text" />
							<button class="margin_right anchor">Login</button>
						</fieldset>
					</form>
				</div>
			</div>			
		</div>
	</section>	

	<!-- POST MESSAGE -->
	<section class="bck center light margin-bottom message" style="display: none">
		<div class="row">
			<div class="column_5 center">
				<span></span>
			</div>							
			<div class="column_5 center">
				<button class="secondary anchor logout"><span class="icon cancel"></span>Logout</button>
			</div>							
		</div>
		<div class="row">
			<div class="column_10">
				<div id="stylized-form">
					<form>
						<fieldset>
							<input type="text" />
							<button class="margin_right anchor send">Send</button>
						</fieldset>
					</form>
				</div>
			</div>						
		</div>
	</section>

	<!-- SEARCH -->
	<!--
	<section class="padding bck light search">
		<div class="row">
			<form>
			<div class="column_8">
				<input type="text" />
			</div>							
			<div class="column_4">
				<button class="secondary anchor search">search</button>
			</div>
			</form>							
		</div>
	</section>	
	-->
	<!-- ONLINE USERS -->
	<section class="bck lightest center online_users">
		<div class="row">
			<div class="column_10">
				<span>Online users: </span>
				<span class="text bold">&nbsp;</span>
			</div>			
		</div>
	</section>

	<!-- MESSAGE LIST -->
	<section class="bck center lightest">
		<div class="row">
            <div class="column_10">
                <table>
                    <thead>
                        <tr>
                            <th>Message List</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Loading messages</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>			
		</div>
	</section>
	<section class="padding bck light">
		<div class="row">
			<div class="column_10">
				<pre><?php echo file_get_contents('README.md'); ?>
				</pre>
			</div>
		</div>
	</section>
</body>
</html>