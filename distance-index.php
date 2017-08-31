<?php
	//Distance - Frontend
	
	require_once(__DIR__ . "/distance.php");
	require_once(__DIR__ . "/tokens.php");
	
	function pathToStr($path) {
		
		$str = '';
		foreach($path as $opt){
			$str .= "({$opt->x},{$opt->y}),";
		}
			
		return substr($str,0,-1);
		
	}
	
	if(isset($_POST['action']) && $_POST['action'] == 'getPath') {
		
		$paths = $_POST['paths'];
		
		if(!is_array($paths))
			die("{$_POST['token']}|<div class='alert alert-danger' >Invalid Paths Array</div>");
		else if( !token_verify($_POST['token']) )
			die("|<div class='alert alert-danger' >Token expired!<br>Please reload to continue</div>");
		
		foreach($paths as $key=>$p){
			foreach($p as $i=>$c) {
				$paths[$key][$i] = new COORD( $c['x'] , $c['y'] );
			}
		}
		$_SESSION['paths'] = $paths;
		
		$result = paths( $paths );
		
		echo token_gen() . "|";
		
		echo "Input:<ul>";
		foreach($paths as $p) {
			echo "<li>" . pathToStr($p);
		}
		
		echo "</ul><br>Best Route: " . pathToStr( $result[0]['coords'] ) . "<br>Distance Covered: " . $result[0]['distance'];
		
		die();
		
	}
	
?>
<html>
	<head>
		<title>Optimal Distance Calculator</title>
		<link rel='stylesheet' href='css/bootswatch.css' />
	</head>
	<body class='container' >
		
		<h2 align='center' class='alert alert-info' >Best Route Calculator</h2>
		
		<div id='response' ></div>
		<form method='post' id='mainform' >
			<div class='hidden' id='row_template' >
				<label for='%id%' >Enter Coordinates in format (a,b),(c,d),(e,f),(g,h)</label>
				<input type='text' class='form-control' id='%id%' name='row[]' placeholder='(a,b),(c,d),(e,f),(g,h)' />
			</div>
			
			<div id='row_container' >
				
			</div>
			<input type='button' class='btn btn-info' value='Add new Route' id='addrow' /> <input type='reset' class='btn btn-primary' value='Clear' />
			<br>
			<input type='submit' class='btn btn-danger' value='Calculate' />
			<input type='hidden' id='token' value="<?php echo token_gen(); ?>" />
		</form>
		
	</body>
</html>

<script src='js/jquery.min.js' ></script>
<script src='./main.js' ></script>