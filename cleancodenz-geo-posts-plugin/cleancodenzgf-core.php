<?php

/** Load WordPress Administration Bootstrap */
require_once('../../../wp-admin/admin.php');

if (!current_user_can('upload_files'))
wp_die(__('You do not have permission to upload files.'));


@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
$title ="CleanCode NZ Geo Finder";
if ( isset($_GET['location']) )
{
  $searchstr = $_GET['location'];
}

$defaultgf_lat = get_option('cleancodenzgeop_default_lat');
 
$defaultgf_lon =get_option('cleancodenzgeop_default_long');

$defaultgf_zoom = get_option('cleancodenzgeop_default_zoom');

if(!$defaultgf_lat ||!$defaultgf_lon||!$defaultgf_zoom)
{
  $defaultgf_lat = '-43.5320544';
  $defaultgf_lon ='172.6362254';
  $defaultgf_zoom = 12;
}


?>
<style type="text/css">
#map-canvas {
	height: 400px;
}
</style>

<script
	type="text/javascript"
	src="http://www.google.com/jsapi?autoload={'modules':[{name:'maps',version:3,other_params:'sensor=false'}]}"></script>


<div class="wrap"><?php screen_icon(); ?>
<h4><?php echo esc_html( $title ); ?></h4>
<p><label>Address</label><input type="text" name="searchstr"
	id="searchstr" value="<?php echo esc_html( $searchstr ); ?>" /> <label>Latitude</label><input
	type="text" name="latitude" id="latitude" /> <label>Longitude</label><input
	type="text" name="longitude" id="longitude" /></p>
<p><input type="button" name="btnsearch" value="Search Again"
	onclick="loadsearchadd();return false;" /> <input type="button"
	name="btnclose" onclick="sendtoparent();return false;" value="Accept">
</p>
<div id="map-canvas"></div>

</div>


<script type="text/javascript">
        var map;
        var geocoder;
        var mapDiv;
        var latEle;
        var lngEle;
        var marker;
        function init() {
       	   mapDiv = document.getElementById('map-canvas');

       	   latEle = document.getElementById('latitude');

       	   lngEle = document.getElementById('longitude');
       	 
       	   geocoder = new google.maps.Geocoder();
       	  
           map = new google.maps.Map(mapDiv, {
                center: new google.maps.LatLng(<?php echo $defaultgf_lat ?>, <?php echo $defaultgf_lon ?>),
                zoom: <?php echo $defaultgf_zoom ?>,
                mapTypeId: google.maps.MapTypeId.ROADMAP
               });

           marker = new google.maps.Marker({  
        	   position: map.getCenter(),
               map: map  
          });

          
           google.maps.event.addListener(map,'center_changed',centerchanged);

           centerchanged();
           
           loadsearchadd();  
        }
        

        google.maps.event.addDomListener(window, 'load', init);

        

        function loadsearchadd()
        {
            var address = document.getElementById('searchstr').value;
            
            if(address!=null && address !="")
            {
            
                geocoder.geocode({'address': address}, function (results, status) {

                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);

                    centerchanged();
                    
                } else {
                    alert("Geocode was not successful for the following reason: " + status);
                }});
            }

           
        }

        function centerchanged()
        {
             var mapcenter = map.getCenter();
            
        	 latEle.value = mapcenter.lat().toString();
             lngEle.value = mapcenter.lng().toString();
             marker.setPosition( mapcenter);
            }
 </script>

<script type="text/javascript">
    function sendtoparent()
    {
    	
        var jsonstr  = '{"address":"'+ document.getElementById('searchstr').value+'",'+
        '"latitude":"'+document.getElementById('latitude').value+'",'+
        '"longitude":"'+document.getElementById('longitude').value+'"'+
        ' }';

       var htmlstr ='<div>'+
           '<input type="hidden" id="address" value="'+document.getElementById('searchstr').value+'" />'+
           '<input type="hidden" id="latitude" value="'+document.getElementById('latitude').value+'" />'+
           '<input type="hidden" id="longitude" value="'+document.getElementById('longitude').value+'" />'+
       '</div>';
             
    	var win = window.dialogArguments || opener || parent || top;
        win.send_to_editor(htmlstr);
        return false;
        
   }
</script>




