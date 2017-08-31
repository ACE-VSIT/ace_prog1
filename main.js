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
			var v = this.value.replace(/\s/g,'').match(/\((\d+(\.\d+)?,\d+(\.\d+)?)\),\((\d+(\.\d+)?,\d+(\.\d+)?)\),\((\d+(\.\d+)?,\d+(\.\d+)?)\),\((\d+(\.\d+)?,\d+(\.\d+)?)\)/);
			
			if(v)
				data.paths.push([ coords( v[1] ) , coords( v[4] ) , coords( v[7] ) , coords( v[10] ) ]);
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