<?php

/*
 * Add options menu
 * 
 * */
function cleancodenzgeop_option_menu()
{
  //Create sub menu page
  add_options_page('CleanCodeNZ Geo Posts','CCNZGeoPosts','administrator' , __FILE__, 'cleancodenzgeop_options_page');

  //call register settings function
  add_action( 'admin_init', 'cleancodenzgeop_register_settings' );
}

/*
 * Register settings
 * */

function cleancodenzgeop_register_settings()
{
  //register our settings
  register_setting( 'cleancodenzgeop-settings-group', 'cleancodenzgeop_default_lat','');
  register_setting( 'cleancodenzgeop-settings-group', 'cleancodenzgeop_default_long','');
  register_setting( 'cleancodenzgeop-settings-group', 'cleancodenzgeop_default_zoom','');
  register_setting( 'cleancodenzgeop-settings-group', 'cleancodenzgeop_map_page_title','');

  register_setting( 'cleancodenzgeop-settings-group', 'cleancodenzgeop_searchable','');
  
}

// add admin menu
add_action('admin_menu', 'cleancodenzgeop_option_menu');

/*
 * Generate options page
 * @author Zhizhong Wang
 */
function cleancodenzgeop_options_page() {
  global $cleancodenz_gp_ver;
?>
<div class="wrap">
<h2>CleanCodeNZ Geo Posts Plugin ver <?php echo $cleancodenz_gp_ver ?></h2>

<form method="post" action="options.php"><?php settings_fields('cleancodenzgeop-settings-group'); ?>
<p>This plugin will allow you to enter posts with geo locations and plot them onto a map.</p>
<table class="form-table">
    <tr valign="top">
        <th scope="row">Default Location Latitude</th>
        <td><input type="text" name="cleancodenzgeop_default_lat"
            value="<?php echo get_option('cleancodenzgeop_default_lat') ; ?>" /></td>
    </tr>

    <tr valign="top">
        <th scope="row">Default Location Longitude</th>
        <td><input type="text" name="cleancodenzgeop_default_long"
            value="<?php echo get_option('cleancodenzgeop_default_long') ; ?>" /></td>
    </tr>
   
     <tr valign="top">
        <th scope="row">Default Zoom Level</th>
        <td><input type="text" name="cleancodenzgeop_default_zoom"
            value="<?php echo get_option('cleancodenzgeop_default_zoom') ; ?>" /></td>
    </tr>
    
    <tr valign="top">
        <th scope="row">Title of Map Page </th>
        <td><input type="text" name="cleancodenzgeop_map_page_title"
            value="<?php echo get_option('cleancodenzgeop_map_page_title') ; ?>" /></td>
    </tr>
    
     <tr valign="top">
        <th scope="row">Search Layout </th>
        <td><input type="checkbox" name="cleancodenzgeop_searchable"
          value="1"  <?php echo checked("1", get_option('cleancodenzgeop_searchable'), "0") ; ?> /></td>
    </tr>
    

</table>

<p class="submit"><input type="submit" class="button-primary"
    value="<?php _e('Save Changes') ?>" /></p>

</form>
</div>
<?php
}

