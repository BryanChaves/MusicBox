<!DOCTYPE html>
<html>
<head>
	<title>MusicBox</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	{{HTML::style('assets/css/bootstrap.min.css',array('media'=>'screen'))}}
</head>
<body>
	
	<div id="wrap">

	<div id="marco">	
			<marquee id="marco2">
				<h1 id="Principal">MusicBox</h1>
			</marquee>
	</div>		
		<div class="container">
			
			
			{{ $content }}

		</div>

	</div>
	
	


	<script src="//code.jquery.com/jquery.js"></script>
	{{HTML::script('assets/js/bootstrap.min.js')}}
	{{HTML::script('js/validaciones.js');}}
</body>
</html>