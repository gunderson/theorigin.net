<?php

/* Site Wide Functions */
function dbconnect() {
  $db = mysql_connect('localhost','theorigi_web','web123');
  $select = mysql_select_db('theorigi_tgsite');
  return $db;
};

/* Session Handling */
function getSession(){
    global $username, $userClass;
    if ($_COOKIE['sessionID']){
        session_id($_COOKIE['sessionID']);
        $username = $_SESSION['username'];
        $userClass = $_SESSION['class'];
    };
};

function set_cookie_array($array,$expire){
	foreach ($array as $name => $value){
	   if(setcookie($name,$value,time()+60*$expire)) {
           $error = 0;
        } else {
           $error = 1;
        };
	};
	if ($error){
        return true;
    } else {
       return false;
    };
};

/* Sitewide variables */
$now = time();
$db = dbconnect();

/* Login Handling*/
function logout(){
	session_destroy();
	$_COOKIE["sessionID"] = 0;
	if (set_cookie_array($_COOKIE,-60)){
        $error = 0;
    } else {
        $error = 1;
    };
    if ($error){
        return true;
    } else {
       return false;
    };
}; // end logout()

function login($username, $password){
    global $db;
    $sql = "SELECT * FROM users WHERE username='$username'";
	$result = mysql_query($sql, $db);
	if ($result){
		$user_info = mysql_fetch_array($result);
		$username = $user_info['username'];
		if ($password == $user_info['password'] || $password == $user_info['tempPassword']){
			$_SESSION['username'] = $user_info['username'];
			$_SESSION['class'] = $userinfo['class'];
            $_SESSION['loggedin'] = true;
            $_COOKIE['sessionID'] = session_id();
			set_cookie_array($_COOKIE,60);

            /* log login IP and time */
            $sql = "UPDATE users SET lastLogin='".time()."', lastIP='$_SERVER[REMOTE_ADDR]' WHERE username=$username";
            $result = mysql_query($sql,$db);
			
			$error = 0;
		} else {
			$_COOKIE['sessionID'] = 0;
			set_cookie_array($_COOKIE,60);
			$error = 2;
		};
	} else {
        $_COOKIE['sessionID'] = 0;
    	set_cookie_array($_COOKIE,60);
        $error = 1;
	};

    /* Error Definitions */
    /* Error 0 = no error */
    /* Error 1 = bad username */
    /* Error 2 = bad password */

    if (!$error){
        return false;
    } else {
        return mysql_error();
    };
}; // end login()

/* Post Handling*/
function addPost($username, $userClass, $title, $content, $class, $image, $isFeatured){
    global $db, $userInfo;
    if ($userClass >=60){ // userclass >60 are contributers
        if (!get_magic_quotes_gpc()){
    		$title = addslashes($title);
    		$content = addslashes($content);
    	};

    	$sql = "INSERT INTO posts SET title='$title', content='$content', owner='$username', timestamp='$now', class='$class'";
    	$result = mysql_query($sql, $db);
        $postID = mysql_insert_id();
        if($result){
    		addToPostCount($db);
    		/* upload image */
            if ($image){
                addImage($db, $username, $postID);
            };
    		/* Set as only featured story */
    		if ($isFeatured){
    			$sql = "UPDATE posts SET isFeatured='0'";
    			$result = mysql_query($sql,$db);
    			if ($result) {
    				$sql = "UPDATE posts SET isFeatured='1' WHERE id='$postID'";
    				$result = mysql_query($sql,$db);
    			} else {
                  $error = 2;
                };
    		};
    		/* Create Permanent URL */
            $permanentURL = preg_replace(' ','_',$title) . '_' . $postID;
            $sql = "UPDATE posts SET permanentURL='$permanentURL' WHERE id='$postID'";
            $result = mysql_query($sql,$db);
            $error = 0;
    	} else {
            $error = 2;
    	};
	} else {
         $error = 1;
	};
	
    /* Error Definitions */
    /* Error 0 = no error */
    /* Error 1 = not authorized */
    /* Error 2 = SQL error*/
	
    if (!$error){
        return true;
    } else {
        return false;
    };
}; // end addPost()

function modifyPost($postID, $username, $userClass, $owner, $title, $content, $class, $image, $isFeatured){
    global $db, $userInfo;
    if ($userClass >=80 || $username == $owner){ // userclass >80 are Moderators

        if (!get_magic_quotes_gpc()){
    		$title = addslashes($title);
    		$content = addslashes($content);
    	};

    	$sql = "UPDATE posts SET title='$title', content='$content', owner='$owner', timestamp='$now', class='$class' WHERE id='$postID'";
    	$result = mysql_query($sql, $db);
        if($result){
            if ($image == "remove image") {
                $sql = "UPDATE posts SET image='' WHERE id='$postID'";
                if($result = mysql_query($sql, $db)){
    	       	};
    	   };

    		/* upload image */
            if ($image){
                addImage($username, $postID);
            };

    		/* Set as only featured story */
    		if ($isFeatured){
    			$sql = "UPDATE posts SET isFeatured='0'";
    			$result = mysql_query($sql,$db);
    			if ($result) {
    				$sql = "UPDATE posts SET isFeatured='1' WHERE id='$postID'";
    				$result = mysql_query($sql,$db);
    			};
    		};
    	    $error = 0;
    	} else {
            $error = 2;
        };
    } else {
        $error = 1;
    };

    /* Error Definitions */
    /* Error 0 = no error */
    /* Error 1 = not authorized */
    /* Error 2 = SQL error */

    if (!$error){
        return true;
    } else {
        return false;
    };
}; // end modifyPost()

function deletePost($postID, $username, $userClass){
    global $db, $userInfo;
    $sql = "SELECT * FROM posts WHERE id='$postID'";
    $result = mysql_query($sql, $db);
    $postInfo = mysql_fetch_array($result);
    if ($userClass >= 80 || $postInfo['owner'] == $username){ //class >80 are moderators and above
		$sql = "SELECT * FROM posts WHERE id='$postID'";
		if($result){
			subtractFromPostCount($db,$postInfo['owner']);
		};
		$sql = "DELETE FROM posts WHERE id='$postID'";
		$result = mysql_query($sql, $db);
		if($result){
            $error = 0;
		};
	} else {
        $error = 1;
    };
    
    /* Error Definitions */
    /* Error 0 = no error */
    /* Error 1 = not authorized */
    /* Error 2 = SQL error */

    if (!$error){
        return true;
    } else {
        return false;
    };
}; // end deletePost

function addToPostCount($username){
    global $db, $userInfo;
	$sql = "SELECT postCount FROM users WHERE username='$username'";
	if($result = mysql_query($sql, $db)){
		$myRow = mysql_fetch_array($result);
		$myRow['postCount']++;
		$sql = "UPDATE users SET postCount='$myRow[postCount]' WHERE username='$username'";
		$result = mysql_query($sql, $db);
	} else {
	echo mysql_error($db);
	};
}; // end addToPostCount()

function subtractFromPostCount($username){
    global $db, $userInfo;
	$sql = "SELECT postCount FROM users WHERE username='$username'";
	if($result = mysql_query($sql, $db)){
		$myRow = mysql_fetch_array($result);
		$myRow['postCount']--;
		$sql = "UPDATE users SET postCount='$myRow[postCount]' WHERE username='$username'";
		$result = mysql_query($sql, $db);
	} else {
	echo mysql_error($db);
	};
}; // end subtractFromPostCount()

function addPostView($id){
    global $db, $userInfo;
	$sql = "UPDATE posts SET views=views+1  WHERE id=$id";
	$result = mysql_query($sql, $db);
	if (!$result){
	   echo mysql_error();
   };
}; // end addPostView()

/* User Handling */

function addUser($newUsername, $password, $now, $email, $homepage, $nameFirst, $nameLast, $addressStreet, $addressUnit, $addressCity, $addressState, $addressZip, $im_aim, $im_yahoo, $im_msn, $im_icq, $image, $signature){
    global $db, $userInfo;
	$userHash = hash('md5', ($newUsername . $password . 'Go Cougs!'));
	$sql = "INSERT INTO users SET username='$newUsername', password='$password', timestamp='$now', email='$email', class='10', homepage='$homepage', nameFirst='$nameFirst', nameLast='$nameLast', addressStreet='$addressStreet', addressUnit='$addressUnit', addressCity='$addressCity', addressState='$addressState', addressZip='$addressZip', im_aim='$im_aim', im_yahoo='$im_yahoo', im_msn='$im_msn', im_icq='$im_icq', hash='$userHash'";
	$result = mysql_query($sql, $db);
};

function modifyUser($username, $userClass, $profileUsername, $class, $now, $email, $homepage, $nameFirst, $nameLast, $addressStreet, $addressUnit, $addressCity, $addressState, $addressZip, $im_aim, $im_yahoo, $im_msn, $im_icq, $image, $signature, $confirm){
    global $db, $userInfo;
    if ($username == $profileUsername || $userClass >= 90){
    	$sql = "UPDATE users SET email='$email', homepage='$homepage', nameFirst='$nameFirst', nameLast='$nameLast', addressStreet='$addressStreet', addressUnit='$addressUnit', addressCity='$addressCity', addressState='$addressState', addressZip='$addressZip', im_aim='$im_aim', im_yahoo='$im_yahoo', im_msn='$im_msn', im_icq='$im_icq'";

        if ($userClass >= 90) {
            if ($class){
                $sql .= " class='$class'";
            };
            if ($confirm){
                $sql .= " confirmed='$confirm'";
            };
        };
        $result = mysql_query($sql, $db);
    };
};

function changePassword($username, $newPassword, $oldPassword) {
    global $db, $userInfo;
    $sql = "SELECT * FROM users WHERE username='$username'";
    
    if ($newPassword){
        $sql .= " password='$newPassword'";
    };
}


function resetPassword(){
global $db, $userInfo;
}

function deleteUser($userToDelete, $username, $userClass){
    global $db, $userInfo;
    if ($userClass >= 90){ //class >90 are Admins
		$sql = "DELETE FROM users WHERE username='$userToDelete'";
		$result = mysql_query($sql, $db);
		if($result){
            $error = 0;
		} else {
            $error = 2;
        };
	} else {
        $error = 1;
    };

    /* Error Definitions */
    /* Error 0 = no error */
    /* Error 1 = not authorized */
    /* Error 2 = SQL error */

    if (!$error){
        return true;
    } else {
        return false;
    };
}; // end deleteUser


/* image Handling */
function addImage($username, $postID) {
    global $db, $userInfo;
    $thumbnailWidth = 250;
    $thumbnailHeight = 175;
    
	$imageDirectory = $_SERVER['DOCUMENT_ROOT']."users/$username/";
	$imageUniqueName = stripslashes($postID . "_" . $_FILES['postimage']['name']);
	$uploadfile = $imageDirectory . $imageUniqueName;
	if (move_uploaded_file($_FILES['postimage']['tmp_name'], $uploadfile)) {
		$sql = "UPDATE posts SET image='$uploadfile' WHERE id='$postID'";
		if($result = mysql_query($sql, $db)){
            /* Resize Image to fit layout and Create Thumbnail*/
            $image_info = getimagesize($uploadfile);
            makeImageThumbnail($imageDirectory, $imageUniqueName, $thumbnailWidth, $thumbnailHeight);// makeImageThumbnail()function below
            $status = 1;
		};
	} else {
		$message = "Possible file upload attack!  Here's some debugging info:\n";
		print_r($_FILES);
	};

}; // end addImage

function makeImageThumbnail($imageDirectory, $imageUniqueName, $thumbnailWidth, $thumbnailHeight){
    global $db, $userInfo;
   $imageFile = $imageDirectory . $imageUniqueName;
   list($width, $height) = getimagesize($imageFile);

   /* Create Thumbnail */
   /* Pick portion of Source To take */
   $sourceWidth = $width/3;
   $sourceHeight = $sourceWidth * 2 / 3;
               
   /* Pick Thumbnail Crop Point */
   $thumbnailInitialX = round(($width /3) - ($thumbnailWidth /2),0);
   $thumbnailInitialY = round(($height/4) - ($thumbnailheight/2),0);

   if ($thumbnailInitialX < 0) {
	   $thumbnailInitialX = 0;
   };
   if ($thumbnailInitialY < 0) {
	   $thumbnailInitialY = 0;
   };

   /* Resample Image */
   $thumbnailImage = imagecreatetruecolor($thumbnailWidth, $thumbnailheight);
   $imageType = substr($imageUniqueName, strlen($imageUniqueName) - 3,3);
   $imageThumbnailFilename = substr($imageUniqueName,0,strlen($imageUniqueName) - 4) . ".thumb." . $imageType;
   
   if ($imageType == 'jpg'){
       /* JPEG files*/
       $sourceImage = imagecreatefromjpeg($imageFile);
       imagecopyresampled($thumbnailImage, $sourceImage, 0, 0, $thumbnailInitialX, $thumbnailInitialY, $thumbnailWidth, $thumbnailheight, $sourceWidth, $sourceHeight);
       imagejpeg($thumbnailImage, $upload_dir . $imageThumbnailFilename, 100);
   } else if ($imageType == 'png'){
       /* PNG files*/
       $sourceImage = imagecreatefrompng($imageFile);
       imageAlphaBlending($sourceImage, true);
       imageSaveAlpha($sourceImage, true);
       imagecopyresampled($thumbnailImage, $sourceImage, 0, 0, $thumbnailInitialX, $thumbnailInitialY, $thumbnailWidth, $thumbnailheight, $sourceWidth, $sourceHeight);
       imagepng($thumbnailImage, $upload_dir . $imageThumbnailFilename);
   } else if ($imageType == 'gif'){
       /* GIF files*/
       $sourceImage = imagecreatefromgif($imageFile);
       imagecopyresampled($thumbnailImage, $sourceImage, 0, 0, $thumbnailInitialX, $thumbnailInitialY, $thumbnailWidth, $thumbnailheight, $sourceWidth, $sourceHeight);
       imagegif($thumbnailImage, $upload_dir . $imageThumbnailFilename);
   };
}; // end makeImageThumbnail()
