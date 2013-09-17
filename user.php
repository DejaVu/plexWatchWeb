<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>plexWatch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- css styles -->
    <link href="css/plexwatch.css" rel="stylesheet">
	<link href="css/plexwatch-tables.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>

    <!-- touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/icon_iphone.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/icon_ipad.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/icon_iphone@2x.png">
	<link rel="apple-touch-icon" sizes="144x144" href="images/icon_ipad@2x.png">
  </head>

  <body>

	
  
	<div class="container">
		    			
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				
				<div class="logo"></div>
				<ul class="nav">
					
					<li><a href="/plexWatch"><i class="icon-home icon-white"></i> Home</a></li>
					<li class="active"><a href="history.php"><i class="icon-calendar icon-white"></i> History</a></li>
					<li><a href="users.php"><i class="icon-user icon-white"></i> Users</a></li>
					<li><a href="charts.php"><i class="icon-list icon-white"></i> Charts</a></li>
					
				</ul>
			</div>
		</div>
    </div>
	<div class="container-fluid">
		<div class='row-fluid'>
			<div class='span12'></div>
		</div>
		<div class='row-fluid'>	
			<div class='span12'>
				<div class='wellbg'>
					<div class='wellheader'>
						
					<?php
					include_once('config.php');
					$user = $_GET['user'];
					date_default_timezone_set('America/New_York');

					$db = new SQLite3($plexWatch['plexWatchDb']);
					$numRows = $db->querySingle("SELECT COUNT(*) as count FROM processed ");

					$results = $db->query("SELECT * FROM processed WHERE user = '$user' ORDER BY time DESC");
					echo "<div class='dashboard-wellheader'>";
							echo"<h3>Watching History for <strong>".$user."</strong></h3>";
						echo"</div>";
					echo"</div>";
					
					if ($numRows < 1) {

					echo "No Results.";

					} else {
					
					echo "<table id='history' class='display'>";
						echo "<thead>";
							echo "<tr>";
								echo "<th align='center'><i class='icon-calendar icon-white'></i> Date</th>";
								echo "<th align='left'><i class='icon-hdd icon-white'></i> Platform</th>";
								echo "<th align='left'><i class='icon-globe icon-white'></i> IP Address</th>";
								echo "<th align='left'>Title</th>";
								echo "<th align='center'><i class='icon-play icon-white'></i> Started</th>";
								echo "<th align='center'><i class='icon-stop icon-white'></i> Stopped</th>";
								echo "<th align='center'><i class='icon-pause icon-white'></i> Paused</th>";
								echo "<th align='center'><i class='icon-time icon-white'></i> Duration</th>";
								echo "<th align='center'>Completed</th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						while ($row = $results->fetchArray()) {
						
						echo "<tr>";
							echo "<td align='center'>".date("m/d/Y",$row['time'])."</td>";
							echo "<td align='left'>".$row['platform']."</td>";

							if (empty($row['ip_address'])) {
								echo "<td align='left'>n/a</td>";

							}else{

								echo "<td align='left'>".$row['ip_address']."</td>";
							}
							$request_url = $row['xml'];
							$xmlfield = simplexml_load_string($request_url) ; 
							$ratingKey = $xmlfield['ratingKey'];
							$type = $xmlfield['type'];
							$duration = $xmlfield['duration'];
							$viewOffset = $xmlfield['viewOffset'];

							if ($type=="movie") {
							echo "<td align='left'><a href='info.php?id=".$ratingKey."'>".$row['title']."</a></td>";
							}else if ($type=="episode") {
							echo "<td align='left'><a href='info.php?id=".$ratingKey."'>".$row['title']."</a></td>";
							}else{

							}

							echo "<td align='center'>".date("g:i a",$row['time'])."</td>";
							$stopped_time = date("g:i a",$row['stopped']);
							
							if ($stopped_time == '7:00 pm') {								//need to find out why it's always this value and write an alternate method.
								echo "<td align='center'>n/a</td>";
							}else{
								echo "<td align='center'>".$stopped_time."</td>";
							}

							$to_time = strtotime(date("m/d/Y g:i a",$row['stopped']));
							$from_time = strtotime(date("m/d/Y g:i a",$row['time']));
							$paused_time = round(abs($row['paused_counter']) / 60,1);
							$viewed_time = round(abs($to_time - $from_time - $paused_time) / 60,0);
							$viewed_time_length = strlen($viewed_time);
							
							echo "<td align='center'>".$paused_time." min</td>";
							
							if ($viewed_time_length == 8) {
								echo "<td align='center'>n/a</td>";
							}else{
								echo "<td align='center'>".$viewed_time. " min</td>";
							}
							
							$percentComplete = sprintf("%2d", ($viewOffset / $duration) * 100);
								if ($percentComplete >= 90) {	
								  $percentComplete = 100;    
								}

							echo "<td align='center'><span class='badge badge-warning'>".$percentComplete."%</span></td>";
						echo "</tr>";   
					}
					}
						echo "</tbody>";
					echo "</table>";

					?>
						
				</div>
			</div>
			
		</div>
	</div>			

		<footer>
		
		</footer>
		
    
    
    <!-- javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-2.0.3.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/jquery.dataTables.js"></script>
	<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
	
	<script>
		$(document).ready(function() {
			var oTable = $('#history').dataTable( {
				"bPaginate": true,
				"bLengthChange": true,
				"bFilter": true,
				"bSort": true,
				"bInfo": true,
				"bAutoWidth": true,
				"aaSorting": [[ 0, "desc" ]],
				"bStateSave": true,
				"bSortClasses": false,
				"sPaginationType": "bootstrap"	
			} );
		} );
	</script>
	

  </body>
</html>