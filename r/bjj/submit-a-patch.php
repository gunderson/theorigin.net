<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
	<title>2014 /r/bjj Patch Submission</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta property="og:image" content="http://i.imgur.com/VZ4aONs.png"/>
	<style>
	    body{
			width: auto;
			height: auto;
			margin: 80px;
			padding: 0;
			background: #000;
			acolor: #ccc;
	    }
	    h1,h2,label{
	    	color: #ccc;
	    }
	    label{
	    	display: block;
	    }
	    .disclaimer{
	    	margin-top: 2em;
	    	font-size: .7em;
	    }
	    #container{
			text-align: center;
			margin: auto;
	    }
	    form{
			margin: auto;
			text-align: center;
	    	margin: 30px 0 0 0;	
	    }
	    
	    table{
	    	text-align: center;
	    	width: auto;
	    	align: center;
	    	border:none;
	    }
	    #thumbs td{
	    	width: 100px;
	    }
	</style>
    </head>
    <body>
		<div id="container">
			<h1>2014 r/bjj Patch Design Contest</h1>
			<h2>Submissions</h2>
			<form action="submission-process.php" method="post" enctype="multipart/form-data">
				<label>Reddit username: <input name="username" required="true"/></label>
				<label>SVG: <input type="file" name="userfile[]" required="true" accept="image/svg,image/svg+xml"/></label>
				<label>PNG: <input type="file" name="userfile[]" required="true" accept="image/png"/></label>
				<input type="submit">Submit</input>
			</form>
		</div>
    </body>
</html>
