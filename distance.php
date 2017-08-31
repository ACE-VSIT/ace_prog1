<?php
	
	class COORD {
		public $x , $y;
		
		public function x($x) {
			$this->x = $x;
		}
		
		public function y($y) {
			$this->y = $y;
		}
		
		public function __construct($x=0,$y=0) {
			$this->x = (float) $x;
			$this->y = (float) $y;
		}
	}

	function distance($x1 , $y1 , $x2 , $y2 ) {
		
		return sqrt( pow($x2-$x1,2) + pow($y2-$y1,2) );
		
	}
	
	function path( $a , $b , $c , $d ) { //$a=$b=$c=$d = Object { x -> coordinate , y -> coordinate }
		
		$dist = distance( $a->x , $a->y , $b->x , $b->y ); //distance btw pt a & b - X
		$dist += distance( $b->x , $b->y , $c->x , $c->y); //distance btw pt b & c - Y
		$dist += distance( $c->x , $c->y , $d->x , $d->y); //distance btw pt c & d - Z
		
		//$dist = X + Y + Z (Distance btw a&b + b&c + c&d)
		
		return $dist;
	}
	
	function paths( $inp ) { //$inp = Array( array($a,$b,$c,$d) , array($e,$f,$g,$h) .... ); WHERE $a,b,c,d,e,f,g,h = Object { x -> coordinate , y -> coordinate }
		
		$distances = array();
		
		foreach($inp as $key=>$path)
			$distances[$key] = call_user_func_array( "path" , $path );
		
		asort($distances);
		
		$ret = array();
		
		foreach($distances as $key=>$dist) {
			
			$ret[] = array("distance" =>$dist , "coords" => $inp[$key] );
			
		}
		
		return $ret;
	}