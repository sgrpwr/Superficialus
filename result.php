<?php
$var_value = $_GET['varname'];
// Include FB config file && User class
require_once 'fbConfig.php';
require_once 'User.php';

if(isset($accessToken)){
	if(isset($_SESSION['facebook_access_token'])){
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}else{
		// Put short-lived access token in session
		$_SESSION['facebook_access_token'] = (string) $accessToken;
		
	  	// OAuth 2.0 client handler helps to manage access tokens
		$oAuth2Client = $fb->getOAuth2Client();
		
		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
		
		// Set default access token to be used in script
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}
	
	// Redirect the user back to the same page if url has "code" parameter in query string
	if(isset($_GET['code'])){
		header('Location: ./');
	}
	
	// Getting user facebook profile info
	try {
		$profileRequest = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,locale,picture');
		$fbUserProfile = $profileRequest->getGraphNode()->asArray();
	} catch(FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		session_destroy();
		// Redirect user back to app login page
		header("Location: ./");
		exit;
	} catch(FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	// Initialize User class
	$user = new User();
	
	// Insert or update user data to the database
	$fbUserData = array(
		'oauth_provider'=> 'facebook',
		'oauth_uid' 	=> $fbUserProfile['id'],
		'first_name' 	=> $fbUserProfile['first_name'],
		'last_name' 	=> $fbUserProfile['last_name'],
		'email' 		=> $fbUserProfile['email'],
		'gender' 		=> $fbUserProfile['gender'],
		'locale' 		=> $fbUserProfile['locale'],
		'picture' 		=> $fbUserProfile['picture']['url'],
		'link' 			=> $fbUserProfile['link']
	);
	$userData = $user->checkUser($fbUserData);
	
	// Put user data into session
	$_SESSION['userData'] = $userData;
	
	// Get logout url
	$logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'logout.php');
	
	// Render facebook profile data
	if(!empty($userData)){
		$output  = '<h1></h1>';
		$output .= '<img src="'.$userData['picture'].'">';
        //$output .= '<br/>Facebook ID : ' . $userData['oauth_uid'];
        $output .= '<br/>' . $userData['first_name'].' '.$userData['last_name'];
        //$output .= '<br/>Email : ' . $userData['email'];
        //$output .= '<br/>Gender : ' . $userData['gender'];
        //$output .= '<br/>Locale : ' . $userData['locale'];
        //$output .= '<br/>Logged in with : Facebook';
		//$output .= '<br/><a href="'.$userData['link'].'" target="_blank">Click to Visit Facebook Page</a>';
        $output .= '<br/><a href="'.$logoutURL.'">Logout</a>'; 
	}else{
		$output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
	}
	
}else{
	// Get login url
	$loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
	
	// Render facebook login button
	$output = '<a href="'.htmlspecialchars($loginURL).'"><button type="button" class="btn btn-primary" style="margin-top: 5px;">Sign in</button></a>';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Superficialus">
    <meta name="author" content="Sagar pawar">
    <link rel="shortcut icon" href="assets/ico/favicon.png">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
           <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  
           <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> 

    <title>Superficialus</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/main.css" rel="stylesheet">
	<link href="assets/css/shop-homepage.css" rel="stylesheet">

    <link href="assets/css/font-awesome.min.css" rel="stylesheet">

    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,700' rel='stylesheet' type='text/css'>

    <script src="assets/js/modernizr.custom.js"></script>
	<script src="jquery.js"></script>
	<script src="assets/js/bootstrap.js"></script>
    
  </head>

  <body>
     <!--search bar -->
	 <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<div id="flipkart-navbar" style="margin-top: -72px; background-color: blueviolet;">
    <div class="container">
        <div class="row row1">
          
        </div>
        <div class="row row2">
            <div class="col-sm-2">
                <h2 style="margin:0px;"><span class="smallnav menu" onclick="openNav()">☰ Brand</span></h2>
               <?php echo $output; ?>
				
				<!-- -->
 
				<!-- -->
				
            </div>
            <div class="flipkart-navbar-search smallsearch col-sm-8 col-xs-11">
                <div class="row">
                    <input type="text" id="search_text" class="flipkart-navbar-input col-xs-11" placeholder="Search for topic, people and interest" name="search_text"" style="color: deeppink;">
                    <button class="flipkart-navbar-button col-xs-1">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                </div>
				<div id="result"></div>
            </div>
        </div>
    </div>
</div>
<div id="mySidenav" class="sidenav">
    <div class="container" style="background-color: #2874f0; padding-top: 10px;">
        <span class="sidenav-heading">Home</span>
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
    </div>

</div> 
	 <!-- search bar ends -->
	<!-- Menu -->
	<nav class="menu" id="theMenu">
	<img src="assets/img/logo.png" alt="logo" style="width: 56px;height: 56px;margin-left: -67px;padding-top: 5px;">
		<div class="menu-wrap">
			<h1 class="logo"><a href="index.php">Go</a></h1>
			<i class="fa fa-arrow-right menu-close"></i>
			<a href="index.php">Home</a>
			<a href="#">Categories</a>
			
			<a href="#">Talk to us</a>
			<a href="https://www.facebook.com/Superficialus/"><i class="fa fa-facebook"></i></a>
			<a href="https://www.youtube.com/channel/UC2RVNrAngpb_uS7GnjUBLTQ"><i class="fa fa-youtube"></i></a>
			<a href="#"><i class="fa fa-twitter"></i></a>
			<a href="#"><i class="fa fa-envelope"></i></a>
		</div>
		
		<!-- Menu button -->
		<div id="menuToggle"><i class="fa fa-bars"></i></div>
	</nav>
	
	<hr class="style13">
	
	<!-- MAIN IMAGE SECTION -->
	<div id="headerwrap">
		<!-- Page Content -->
    <div class="container">

        <div class="row">
           <span class="border-0"></span>
            <div class="col-md-3">
                <p class="strong" style="background-color: #ff1b5a;">Latest video</p>
                <div class="box effect2">
				<div class="container">
                 <div class="col-md-1"><p class="pleft1">Category<br/>Field<br/>Date</p></div>
				  <div class="col-md-1"><p class="pleft1">:<br/>:<br/>:</p></div>
				 <div class="col-md-1"><p class="pright1">Entertainment<br/>Dance<br/>25/01/2017</p></div>
				 </div>
              </div>
            </div>
			
			
			
        <div class="container">
            <div class="col-md-9">

                <div class="row carousel-holder">
				
				<div class="box1 effect1">
                  <div class="col-md-12">
                       <iframe width="800" height="296" src="<?=$var_value ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                  </div>

                    

                </div>
				</div>
				<div>
				      <p style="font-size: 14px;margin-right: 1017px;color: #ff1b5a;">Related Videos</p>
                       <div class="col-md-12">
                         <div class="row">
                          <div class="col-sm-3 col-lg-3 col-md-3">
                             <div class="thumbnail">
                                <iframe width="253" height="150" src="https://www.youtube.com/embed/COlXCIYXgos" frameborder="0" allowfullscreen></iframe>
                                <div class="caption">
                                  <p>"Meet at beat" DANCE #StreetDance - RockTheParty</p>
                                </div>
                              </div>
                          </div>

                    <div class="col-sm-3 col-lg-3 col-md-3">
                        <div class="thumbnail">
                            <iframe width="253" height="150" src="https://www.youtube.com/embed/7CMInoks_CY" frameborder="0" allowfullscreen></iframe>
                            <div class="caption">
                                <p>"Jehri Kuri" BHANGRA DANCE #BHANGRAFUNK - DJ Nimz Remix</p>
                            </div>
                            <div class="ratings">
                                
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 col-lg-3 col-md-3">
                        <div class="thumbnail">
                           <iframe width="253" height="150" src="https://www.youtube.com/embed/o4K3NlZ64b0" frameborder="0" allowfullscreen></iframe>
                            <div class="caption">
                                <p>"ALL THE WAY UP" & "VEERVAAR" - The Spintape by Spin Singh #BHANGRAFUNK dance</p>
                            </div>
                            
                        </div>
                    </div>
					
					<div class="col-sm-3 col-lg-3 col-md-3">
                        <div class="thumbnail">
                           <iframe width="253" height="150" src="https://www.youtube.com/embed/o4K3NlZ64b0" frameborder="0" allowfullscreen></iframe>
                            <div class="caption">
                                <p>"ALL THE WAY UP" & "VEERVAAR" - The Spintape by Spin Singh #BHANGRAFUNK dance</p>
                            </div>
                            
                        </div>
                    </div>


                </div>

            </div><!--yaha se-->
		</div>
           </div>
        </div>

    </div>
    <!-- /.container -->
	</div><!-- /headerwrap -->
	
 <hr class="style13">
 
    <div class="section" style="background-color: white;">
	<div class="container">
		<div class="row mt">
			<div class="col-lg-8">
				<h1>Stay Connected</h1>
				<p>Join us on our social networks for all the latest updates, product/service announcements and more.</p>
				
				<a href="https://www.facebook.com/Superficialus/"><i class="fa fa-facebook-square" aria-hidden="true" style="font-size:40px"></i></a>
				<a href="https://www.youtube.com/channel/UC2RVNrAngpb_uS7GnjUBLTQ"><i class="fa fa-youtube-square" aria-hidden="true" style="font-size:40px"></i></a>
				<a href="#"><i class="fa fa-envelope" aria-hidden="true" style="font-size:40px"></i></a>
				<a href="#"><i class="fa fa-twitter-square" aria-hidden="true" style="font-size:40px"></i></a>
			<br>
			</div><!-- col-lg-8 -->
			<div class="col-lg-4">
				<img src="assets/img/logo.png" alt="logo" style="width: 200px;height: 200px;margin-left: 136px;padding-bottom: 16px;">
			</div><!-- col-lg-4 -->
			
		</div><!-- row -->
	</div><!-- container -->
	</div>
	<div class="table-responsive" id="pagination_data">  
                </div> 
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
	<script src="assets/js/masonry.pkgd.min.js"></script>
	<script src="assets/js/imagesloaded.js"></script>
    <script src="assets/js/classie.js"></script>
	<script src="assets/js/AnimOnScroll.js"></script>
	<script>
		new AnimOnScroll( document.getElementById( 'grid' ), {
			minDuration : 0.4,
			maxDuration : 0.7,
			viewportFactor : 0.2
		} );
	</script>
  </body>
</html>
 <script>  
 $(document).ready(function(){ 
      $('#search_text').keyup(function(){  
           var txt = $(this).val();  
           if(txt != '')  
           {  
                $.ajax({  
                     url:"fetch.php",  
                     method:"post",  
                     data:{search:txt},  
                     dataType:"text",  
                     success:function(data)  
                     {  
                          $('#result').html(data);  
                     }  
                });  
           }  
           else  
           {  
                $('#result').html('');                 
           }
		   
      });  
 });  
 </script>  
 