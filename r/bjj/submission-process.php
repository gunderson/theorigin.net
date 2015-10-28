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
			<h1>Please Wait:</h1>
			<h2>Uploading</h2>
		</div>
    </body>
</html>
<?php
    /*
     *
     * @ Multiple File upload script.
     *
     * @ Can do any number of file uploads
     * @ Just set the variables below and away you go
     *
     * @ Author: Kevin Waterson
     *
     * @copywrite 2008 PHPRO.ORG
     *
     */

    error_reporting(E_ALL);
 
    /*** the upload directory ***/
    $upload_dir= './uploads';

    /*** numver of files to upload ***/
    $num_uploads = 5;

    /*** maximum filesize allowed in bytes ***/
    $max_file_size  = 0xfffff;
 
    /*** the maximum filesize from php.ini ***/
    $ini_max = str_replace('M', '', ini_get('upload_max_filesize'));
    $upload_max = $ini_max * 1024 * 1024;

    /*** a message for users ***/
    $msg = 'Please select files for uploading';

    /*** an array to hold messages ***/
    $messages = array();

    /*** check if a file has been submitted ***/
    if(isset($_FILES['userfile']['tmp_name']))
    {
        /** loop through the array of files ***/
        for($i=0; $i < count($_FILES['userfile']['tmp_name']);$i++)
        {
            // check if there is a file in the array
            if(!is_uploaded_file($_FILES['userfile']['tmp_name'][$i]))
            {
                $messages[] = 'No file uploaded';
            }
            /*** check if the file is less then the max php.ini size ***/
            elseif($_FILES['userfile']['size'][$i] > $upload_max)
            {
                $messages[] = "File size exceeds $upload_max php.ini limit";
            }
            // check the file is less than the maximum file size
            elseif($_FILES['userfile']['size'][$i] > $max_file_size)
            {
                $messages[] = "File size exceeds $max_file_size limit";
            }
            else
            {
                // copy the file to the specified dir
               	$ext = substr($_FILES['userfile']['name'][$i], -3);
                if(@copy($_FILES['userfile']['tmp_name'][$i],$upload_dir.'/'.$_POST["username"]."_2014.".$ext)){
                    /*** give praise and thanks to the php gods ***/
                    $messages[] = $_FILES['userfile']['name'][$i].' uploaded';
                } 
                else
                {
                    /*** an error message ***/
                    $messages[] = 'Uploading '.$_FILES['userfile']['name'][$i].' Failed';
                }
            }
        }
    }

    foreach($messages as $key => $value){
    	print_r($value);
    };
?>
<script>
	// window.location.href = "submission-complete.php";
</script>