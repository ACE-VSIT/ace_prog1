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
		<title>Shortest Route Calculator</title>
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
<script>
(function () {
	var addRow = function ( ){
		var id = randId();
		$("#row_container").append( $("#row_template").html().replace(/%id%/g , id) );
		return id;
	} , randId = function () {
		var str = '' , clist = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM" , l = 5;
		
		for(var i=0;i<5;i++)
			str += clist[ Math.floor( Math.random() *  (clist.length-1) ) ];
		
		return str;
	},
	coords = function (a) {
		if(!a)
			return {};
		a = a.split(",");
		if(!a || !a[0] || !a[1])
			return {};
		return { x : parseFloat(a[0].trim()) , y : parseFloat(a[1].trim() ) };
	},token = $("#token").val() , pathToStr = function (p) {
		var str = '';
		p.forEach(function (o) {
			str += "(" + o.x + "," + o.y + "),";
		});
		
		return str.substr( 0 , str.length-1 );
	};
	
	
	(function () {
		var i = 0;
		try {
			var paths = <?php echo json_encode($_SESSION['paths']); ?>;
			
			paths.forEach(function (p) {
				$("#" + addRow()).val( pathToStr(p) );
				i++;
			});
		}
		catch (e) {
			console.log(e);
			for(;i<4;i++)
				addRow(); //Add 4 Paths at first
		}
	})();
	
	$("#addrow").click(addRow);
	
	
	$("#mainform").submit(function (e) {
		e.preventDefault();
		
		var data = { action : "getPath" , paths : [] , token : token };
		
		$("#row_container").find("[name='row[]']").filter(function () {
			var v = this.value.replace(/\s/g,'').match(/\((\d+(\.\d+)?),(\d+(\.\d+)?)\)/g); //matches all (x,y)
			
			if(v) {
				v.forEach(function (value,index) {
					v[index] = coords( value.replace(/^\(|\)$/,'') ); //remove parenthesis
				});
				data.paths.push(v);
			}
		});
		
		$("#response").html("<div class='alert alert-info' >Loading....</div>");
		
		$.post( window.location.href , data , function (r) {
			
			token = r.match(/^[A-Za-z0-9]+/i);
			console.log(r,token);
			if(!token)
				return $("#response").html(r);
			
			r = r.replace(token,'').replace("|",'');
			
			token = token[0];
			
			$("#response").html( r );
			
		}).error(function (e) {
			$("#response").html("<div class='alert alert-danger' >Please check your internet connection..</div>");
		});
	});
})();
</script>
