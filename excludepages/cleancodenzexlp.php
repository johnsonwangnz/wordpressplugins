<?php
/*
 Plugin Name: CleanCode NZ Exclude Pages Plugin
 Plugin URI: http://www.cleancode.co.nz/cleancodenz-exclude-pages-wordpress-plugin
 Description: Exclude pages from navigation and(or) search results using custom field
 Version: 2.0.0
 Author: CleanCodeNZ
 Author URI: http://www.cleancode.co.nz/about
 License: GPL2
 */

/*
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; version 2 of the License.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */



$cleancodenzexlp_ver='2.0.0';


/*
 * Add options menu
 * @author Zhizhong Wang
 *  */
function cleancodenzexlp_option_menu()
{
  //Create sub menu page
  add_options_page('CleanCodeNZ Exclude Pages','CleanCodeNZEXLP','administrator' , __FILE__, 'cleancodenzexlp_options_page');

  //call register settings function
  add_action( 'admin_init', 'cleancodenzexlp_register_settings' );
}

/*
 * Register settings
 * @author Zhizhong Wang
 * */

function cleancodenzexlp_register_settings()
{
  //register our settings
  register_setting( 'cleancodenzexlp-settings-group', 'cleancodenzexlp_customfield_name','');
  register_setting( 'cleancodenzexlp-settings-group', 'cleancodenzexlp_customfield_value','');

  register_setting( 'cleancodenzexlp-settings-group', 'cleancodenzexlp_notinsearch','');

  register_setting( 'cleancodenzexlp-settings-group', 'cleancodenzexlp_notinsearch_only','');

   
}

/*
 * Generate options page
 * @author Zhizhong Wang
 */
function cleancodenzexlp_options_page() {
  global $cleancodenzexlp_ver;
  ?>
<div class="wrap">
<h2>CleanCodeNZ Exclude Pages Plugin ver <?php echo $cleancodenzexlp_ver ?></h2>

<form method="post" action="options.php"><?php settings_fields('cleancodenzexlp-settings-group'); ?>
<p>This plugin will find pages with exact match of following custom
field name and custom field value to exclude from navigation and/or
search result.</p>
<p>As a default exclusion only happens for navigation</p>
<p>If you want these pages are excluded from search as well as navigation please tick 'exclude
from search too' checkbox</p>
<p>If you want these pages are excluded from search only, but not navigation please tick 'exclude
from search only' checkbox</p>

<table class="form-table">
	<tr valign="top">
		<th scope="row">Exclude Pages with Custom Field Name</th>
		<td><input type="text" name="cleancodenzexlp_customfield_name"
			value="<?php echo get_option('cleancodenzexlp_customfield_name') ; ?>" /></td>
	</tr>

	<tr valign="top">
		<th scope="row">And Custom Field Value</th>
		<td><input type="text" name="cleancodenzexlp_customfield_value"
			value="<?php echo get_option('cleancodenzexlp_customfield_value') ; ?>" /></td>
	</tr>

	<tr valign="top">
		<th scope="row">Exclude from search too</th>
		<td><input type="checkbox" name="cleancodenzexlp_notinsearch"
			value="1" class="code"
			<?php echo checked(1,get_option('cleancodenzexlp_notinsearch'),false); ?> /></td>
	</tr>

	<tr valign="top">
		<th scope="row">Exclude from search only</th>
		<td><input type="checkbox" name="cleancodenzexlp_notinsearch_only"
			value="1" class="code"
			<?php echo checked(1,get_option('cleancodenzexlp_notinsearch_only'),false); ?> /></td>
	</tr>

</table>

<p class="submit"><input type="submit" class="button-primary"
	value="<?php _e('Save Changes') ?>" /></p>

</form>
</div>
			<?php
}
/*
 * This is function to get a list of page ids according to the match of custom field and value
 * @param  $customfieldname,$customfieldvalue, string, if no values for either params, a blank array is returned
 * @return $pagesid, an array of page ids
 * @author Zhizhong Wang
 *
 */
function cleancodenzexlp_getpagesidarray($customfieldname,$customfieldvalue ) {

  $pagesidarray=array();

  if($customfieldname!=null && $customfieldname!=''
  &&$customfieldvalue!=null && $customfieldvalue!='')
  {
    $pagequeryargs= array(
             'meta_key'=>$customfieldname,
             'meta_value' => $customfieldvalue,
             'hierarchical' => 0); // this will exclude child pages even their parents are not excluded

    $excludedpages = get_pages($pagequeryargs);

    if(count($excludedpages)>0)
    {
      foreach ($excludedpages as $excludedpage)
      {
        $pagesidarray[]= $excludedpage->ID;
      }
       
    }
     
  }
  return $pagesidarray;
}

/*
 * This is actually the filtering function
 * @param  $pages array from get_pages
 * @return $pages array after being filtered
 * @author Zhizhong Wang
 *
 */
function cleancodenzexlp_listen_excludepages($excludedpageids) {

  if(!get_option('cleancodenzexlp_notinsearch_only') ==1)
  {
    $customfieldname =  get_option('cleancodenzexlp_customfield_name');
    $customfieldvalue = get_option('cleancodenzexlp_customfield_value');

    $customexcludepageids=cleancodenzexlp_getpagesidarray($customfieldname,$customfieldvalue);

    if ( count($customexcludepageids)>0)
    {

      foreach ($customexcludepageids as $id)
      {
        //check if it has already been excluded
        if (! in_array( $id,$excludedpageids)) {
          $excludedpageids[] =$id  ;
        }
      }
    }
  }
  return $excludedpageids;
}


/*
 * This is actually the filtering function
 * @param  $pages array from get_pages
 * @return $pages array after being filtered
 * @author Zhizhong Wang
 *
 */
function cleancodenzexlp_listen_pagessearch($query) {

  if( $query->is_search && ((get_option('cleancodenzexlp_notinsearch')==1) ||
   get_option('cleancodenzexlp_notinsearch_only') ==1
  ))
  {
    $customfieldname =  get_option('cleancodenzexlp_customfield_name');
    $customfieldvalue = get_option('cleancodenzexlp_customfield_value');

    $customexcludepageids=cleancodenzexlp_getpagesidarray($customfieldname,$customfieldvalue);

    if ( count($customexcludepageids)>0)
    {
      $query->set('post__not_in',$customexcludepageids);

    }
  }
  return $query;
}



add_filter('pre_get_posts', 'cleancodenzexlp_listen_pagessearch');


add_filter('wp_list_pages_excludes', 'cleancodenzexlp_listen_excludepages');
// add admin menu
add_action('admin_menu', 'cleancodenzexlp_option_menu');
?>