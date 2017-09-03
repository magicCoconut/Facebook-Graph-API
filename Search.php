<!DOCTYPE html>
<html>
    <head>
        <title>Page Title</title>
        <style>
			body {padding: 10px;width: 100%; }
			table, th, td {
			    border: 1px solid #ccc;
			    border-collapse: collapse;
			}
			form{
				margin: 0 auto;
				text-align: left;
			}
			hr{
				margin: 0;
			}

			p{
				margin: 5px;
			}
			.col1{
				width: 11.66666667%;
				float: left;
			}


			.offset{
				margin-left: 11.66666667%;
			}

			#hidden{
				visibility: hidden;
			}
			#showup{
				visibility: visible;
			}
			.clickable{
			color: -webkit-link;
		    text-decoration: underline;
		    cursor: pointer;
			}
		</style>
		<meta charset="UTF-8">
    </head>
    <body>

<!-- php main logic chain  -->
<?php
	require_once __DIR__ . '/facebook-sdk-v5/autoload.php';

	$fb = new Facebook\Facebook([
	  'app_id' => '1276769982370540',
	  'app_secret' => '4ea747001bbde8f27cfd73cb1d182d17',
	  'default_graph_version' => 'v2.8',
	]);

	$fb->setDefaultAccessToken('EAASJNy1CUuwBAPGKimMdyKxvliZAAJE0LmXFqOlzAJY928sGFsqvX7Fy5LiIZCQGw7mOOsbErCeGcfadmGsdk88i7iFgKM5cnjVsFghvZCeLhfNZA2F1B0ItVGvoiN4yYzLSmUEhw5RqCkZAbAizwjFRlYcd3C38ZD');
	// logic line
	if(isset($_GET['q'])){
		if($_GET['q'] == "img"){
		echo '<img src="'.$_GET['url'].'">';
		}else{
			showSearchTable($_GET['keyword'],$_GET['type'],$_GET['location'],$_GET['distance']);
	        if($_GET['q'] == "search"){
	        	if($_GET['type']=='palce'){
	        		$result = showLocationResult($_GET['keyword'],$_GET['type'],$_GET['location'],$_GET['distance'],$fb);
	        	}else{
	        		$result = showSearchResult($_GET['keyword'],$_GET['type'],$fb);
	        	}
	        	resultDispay($result,$_GET['keyword'],$_GET['type'],$_GET['location'],$_GET['distance']);
	        	
	        }else if($_GET['q'] == "getdetails"){
	        	showDetails($_GET['id'],$fb);
	        }
		}
	}else{
		showSearchTable('USC','user',90007,1000);
		echo '</div>';
	}   
?>
<!--php main logic chain end-->

<!-- show search table function -->
<?php function showSearchTable($keyword,$type,$loc,$dis){?>
	
	        <div  style="margin: 0px auto 0px auto; padding: 10px;">
		     <!-- search div start-->
	        <div style="background-color: #e9ebee;width: 50%;margin: 5px auto 20px auto; padding: 5px 10px 5px 10px">

	        	<p style="text-align: center;"><i>Facebook Search</i></p>
	        	<hr>

		        <form action="Search.php" id="myform">
		        	<div>
		        	<input type="hidden" name="q" value="search">
				   	<label class="col1">Keyword</label> <input type="text" name="keyword" value="<?php echo $keyword ?>" required>
				   	</div>
				 	 <div>
				  	<label class="col1">Type:</label>
				  	<!-- <input type="text" name="lastname" value="Mouse"> -->
				 	<select name="type" id="type" onchange="showLocation();" >
						<option value="user" id='user'<?php echo $type == 'user' ? ' selected="selected"' : '';?>>Users</option>
						<option value="page" <?php echo $type == 'page' ? ' selected="selected"' : '';?>>Pages</option>
						<option value="event" <?php echo $type == 'event' ? ' selected="selected"' : '';?>>Events</option>
						<option value="palce" <?php echo $type == 'palce' ? ' selected="selected"' : ''; ?>>Places</option>
					  	<option value="group" <?php echo $type == 'group' ? ' selected="selected"' : '';?>>Groups</option>
					  	

					</select>

				 	</div>
				 	<div id="<?php echo $type == 'palce' ? 'showup' : 'hidden'; ?>">
					<label class="col1">Location </label><input type="text" name="location" value="<?php echo $loc ?>"  >
					<label> Distance(Meters) </label><input type="text" name="distance" value="<?php echo $dis ?>" >
					</div>

				  	<input class="offset" type="submit" value="Submit">
				  	<input class="offset" type="button" value="Clear" onclick="Myreset()">

				  	<br><br>
				</form>
	        </div>
	        <!-- search div end-->
<?php } ?>
<!-- show search table function end-->


<!-- other php functions -->
<?php 
function showSearchResult($key,$type,$fb){
	if($type=='event')
		$url_tmp = '/search?q='.$key.'&type='.$type.'&fields=id,name,picture.width(9999).height(9999),place';
	else 
		$url_tmp = '/search?q='.$key.'&type='.$type.'&fields=id,name,picture.width(9999).height(9999)';
	try {

	  $response = $fb->get($url_tmp);
	  // $userNode = $response->getGraphUser();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	  // When Graph returns an error
	  // echo 'Graph returned an error: ' . $e->getMessage();
	  return null;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	  // When validation fails or other local issues
	  // echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  return null;
	}
	return $response->getDecodedBody();
}

function showLocationResult($key,$type,$loc,$dis,$fb){
	if(!empty($loc)){
		$address = urlencode($loc);
		$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=Indiahttps://maps.googleapis.com/maps/api/geocode/json?address=$address&key=AIzaSyC-jlGghYPfbTo-0BFXQ1NhUeYay5debXg";
		$json = @file_get_contents($url);
		$data = json_decode($json);

		if($data->status == 'ZERO_RESULTS')
			return null;
		// print_r($data);
		$lat = $data->results[0]->geometry->location->lat;
		$long = $data->results[0]->geometry->location->lng;
		$center = $lat.','.$long;
	}else{
		$center="";
	}


	try {
	  $response = $fb->get('/search?q='.$key.'&type=place& center='.$center.'&distance='.$dis.'&fields=id,name,picture.width(9999).height(9999)');
	  // $userNode = $response->getGraphUser();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	  // When Graph returns an error
	  // echo 'Graph returned an error: ' . $e->getMessage();
	  return null;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	  // When validation fails or other local issues
	  // echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  return null;
	}
	return $response->getDecodedBody();
}


function resultDispay($h,$keyword,$type,$loc,$dis){
	// print_r($h);
	if($h==null||!isset($h['data'])||count($h['data'])==0) {
		echo '<div style="margin: 30px auto;" class="reset">';
		echo '<p style="width: 60%; text-align: center;margin:0px auto;background-color: #e9ebee;"> No Records has been found</p>';
	}else{
		echo '<div style="margin: 30px auto;" class="reset">';
		echo '<table style="width: 60%; text-align: left;margin: 0px auto;">';
		echo 	'<tbody>';
		echo 		'<tr style="background-color: #e9ebee;">';
		echo 			'<th>Profile Photo</th>';
		echo 			'<th>Name</th>';
		if($type=="event")
			echo '<th>Place</th>';
		else 
			echo '<th>Details</th>'			;										
		
		echo 		'</tr>';

		foreach($h['data'] as $item) {
			echo '<tr>';
			echo '<td><a href="Search.php?q=img&url='.urlencode($item['picture']['data']['url']).'" target="_blank"> <img src="'.$item['picture']['data']['url']. '" height="30" width="40"> </a></td>';
			echo '<td>'.$item['name'] . '</td>';
			if($type=="event")
				if(isset($item['place']['name']))
					echo '<td>'.$item['place']['name']. '</td>';
				else
					echo '<td> </td>';
			else 
				echo '<td><a href="Search.php?q=getdetails&id='.$item['id'].'&keyword='.$keyword.'&type='.$type.'&location='.$loc.'&distance='.$dis.'">Details </a></td>';
			echo '</tr>';	    
		}
		echo '</tbody>';
		echo '</table>';
	}
	echo '</div>';
	echo '</div>';
}

function showDetails($id,$fb){
	$url = '/'.$id.'?fields=id,name,picture.width(700).height(700),albums.limit(5){name,photos.limit(2){name,%20picture}},posts.limit(5)&access_token=EAASJNy1CUuwBAPGKimMdyKxvliZAAJE0LmXFqOlzAJY928sGFsqvX7Fy5LiIZCQGw7mOOsbErCeGcfadmGsdk88i7iFgKM5cnjVsFghvZCeLhfNZA2F1B0ItVGvoiN4yYzLSmUEhw5RqCkZAbAizwjFRlYcd3C38ZD';
	try {

	  $response1 = $fb->get($url);
	  // $userNode = $response->getGraphUser();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	  // When Graph returns an error
	  // echo 'Graph returned an error: ' . $e->getMessage();
	  $response1 = null;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	  // When validation fails or other local issues
	  // echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  $response1 = null;
	}
	if($response1 != null)
		$h1 = $response1->getDecodedBody();
	// print_r($h1);
	if(isset($h1['albums']))
		$usr_albums = $h1['albums']['data'];
	else
		$usr_albums = null;

	if(isset($h1['posts']))
		$usr_posts = $h1['posts']['data'];
	else
		$usr_posts = null;?>

		<div style="margin: 30px auto;"  class="reset">
		<div style="width: 60%; text-align: center;margin: 0px auto; background-color: #e9ebee;">
		<?php 
				if($usr_albums==null){
					echo '<p id="album"> No Albums has been found</p>';
				}else{
					echo '<p onclick="dispalyAlbum()" class="clickable" id="album"> Albums </p>';
				}
		?>		
		</div>
		<table style="width: 60%; text-align: left;margin: 0px auto;display: none" id='album_data'>
			<tbody>			
				<?php if($usr_albums!=null){
							foreach ($usr_albums as $item) {
								if(!isset($item['photos']['data'])||count($item['photos']['data'])==0){
									echo '<tr>';
									// echo '<td> <p onclick="display('.$item['id'] .')" class="clickable" > '.$item['name'].' </p></td>';
									echo '<td> <p> '.$item['name'].' </p></td>';
									echo '</tr>';
								}else{
									echo '<tr>';
									// echo '<td> <p onclick="display('.$item['id'] .')" class="clickable" > '.$item['name'].' </p></td>';
									echo '<td> <p onclick="display('. '\''.$item['id'].'\'' .')" class="clickable" > '.$item['name'].' </p></td>';
									echo '</tr>';

									echo '<tr id="'.$item['id'].'" style="display:none;" class="imgset">';
									echo '<td><div>';
									foreach($item['photos']['data'] as $img){
										if(isset($img['picture']))
											$url_e='https://graph.facebook.com/v2.8/'.$img['id'].'/picture?&access_token=EAASJNy1CUuwBAPGKimMdyKxvliZAAJE0LmXFqOlzAJY928sGFsqvX7Fy5LiIZCQGw7mOOsbErCeGcfadmGsdk88i7iFgKM5cnjVsFghvZCeLhfNZA2F1B0ItVGvoiN4yYzLSmUEhw5RqCkZAbAizwjFRlYcd3C38ZD';
											echo '
										<a href="Search.php?q=img&url='.urlencode($url_e).'" target="_blank"> <img src="'.$url_e.'" height="80" width="80"> </a>';
									}
									echo '</div></td>';
									echo '</tr>';

								}						
							}
						}
				?>								
			</tbody>
		</table>
		<div style="width: 60%; text-align: center;margin: 0px auto;background-color: #e9ebee;">
		<?php if($usr_posts==null){
					echo '<p id="album"> No Posts has been found</p>';
				}else{
					echo '<p onclick="dispalyPost()" class="clickable" id="post"> Posts </p>';
				}
		?>
		</div>
		<table style="width: 60%; text-align: left;margin: 0px auto;display: none" id='post_data'>
			<tbody>
				<tr style="background-color: #e9ebee;">
					<th>Massage</th>					
				</tr>				
				<?php if($usr_posts!=null){
							foreach ($usr_posts as $item) {
								if(isset($item['message'])){
									echo '<tr>';
									echo '<td> <p> '.$item['message'].' </p></td>';
									echo '</tr>';
								}
							}
						}
				?>					
				
			</tbody>
		</table>

		</div>
<?php echo '</div>';}?>
<!-- other php functions end -->

<script type="text/javascript"> 
       function showLocation(){ 
	        var objS = document.getElementById("type"); 
	        var select = objS.options[objS.selectedIndex].value; 
	        if(select=="palce"){
	        	if(document.getElementById("hidden")!=null){
	        		document.getElementById("hidden").id="showup";
	        	}
	        }
	        else {
	        	if(document.getElementById("showup")!=null){
	        		document.getElementById("showup").id="hidden";
	        	}
	        }
       } 

       function Myreset(){      		
       		document.getElementsByName("keyword")[0].value = '';
       		document.getElementsByName("location")[0].value = '';
       		document.getElementsByName("distance")[0].value = '';
       		
       		document.getElementById("user").selected = true;
       		var x = document.getElementsByClassName("reset");
       		if(x != null){
       			for (var i = 0; i < x.length; i++) {
			    	x[i].style.display = "none";
				}
       		}
       		if(document.getElementById("showup")!=null)
	        	document.getElementById("showup").id="hidden";     	
       }

       function dispalyAlbum(){
       		// console.log(document.getElementById("album_data").style.display);
       		if(document.getElementById("album_data").style.display=="none"){
       			console.log('dd');
       			document.getElementById("album_data").style.display="table";
       		}	     			
       		else {
       			document.getElementById("album_data").style.display="none";
       			var x = document.getElementsByClassName("imgset");
	       		var i;
				for (i = 0; i < x.length; i++) {
				    if(x[i].style.display != "none")
			    		x[i].style.display = "none";
				}
       		}
       		document.getElementById("post_data").style.display="none";
       }

       function dispalyPost(){
	       	if(document.getElementById("post_data").style.display=="none"){
	       		document.getElementById("post_data").style.display="table";
	       	}
	       	else
	       		document.getElementById("post_data").style.display="none";

       		document.getElementById("album_data").style.display="none";
       		var x = document.getElementsByClassName("imgset");
       		var i;
			for (i = 0; i < x.length; i++) {
				if(x[i].style.display != "none")
			    	x[i].style.display = "none";
			}
       }

       function display(){
       		if(document.getElementById(arguments[0]).style.display=='none')
       			document.getElementById(arguments[0]).style.display="table";
       		else
       			document.getElementById(arguments[0]).style.display = 'none';
       }
</script> 
		
		
	</body>
</html>