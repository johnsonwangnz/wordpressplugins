<?php

class GP_meta_boxes {

  protected $_meta_boxes;

  // create meta box based on given data
  function __construct($meta_boxes) {
    if (!is_admin()) return;
     
    $this->_meta_boxes = $meta_boxes;

    // fix upload bug: http://www.hashbangcode.com/blog/add-enctype-wordpress-post-and-page-forms-471.html
    $current_page = substr(strrchr($_SERVER['PHP_SELF'], '/'), 1, -4);

    $current_post_type = $_GET['post_type'];
     

    if ($current_page == 'post' || $current_page == 'post-new') {

      // to narrow down the scope when following setup is performed,
      //otherwise it might conflict with others,especially the uploader
      //to check if the type is of our interest
      if(!isset($current_post_type))
      {
        // editing otherwise it is addnew
        $post_id =$_GET['post'];

        if(isset($post_id))
        {
          $edit_post = get_post($post_id);

          $current_post_type  = $edit_post->post_type;
           
        }
      }

      if (isset($current_post_type) &&
      $this->validMetatBoxForType($current_post_type))
      {
        add_action('admin_print_scripts',  array(&$this, 'meta_box_scripts'));
        add_action('admin_print_styles', array(&$this,  'meta_box_styles'));

        add_action('admin_head', array(&$this, 'add_post_enctype'));

        add_action('admin_head', array(&$this, 'add_geo_finder'));

      }
    }

    //if new image size selected, then handle it
    add_filter('media_send_to_editor',array(&$this, 'gp_process_markerimage'), 30, 3);

    //to add the marker image size to media uploader
    add_filter('attachment_fields_to_edit', array(&$this, 'gp_marker_image_add'), 11, 2);

     
    add_action('add_meta_boxes', array(&$this, 'add'));

    add_action('save_post', array(&$this, 'save'));
  }


  //add new size to media form
  function gp_marker_image_add($form_fields, $post)
  {
    if ( isset($_GET['marker']) && $_GET['marker'] == 1 )
    {
      if ( substr($post->post_mime_type, 0, 5) == 'image' ) {

        $name = "attachments[$post->ID][is_marker]";

        $html = "<input type='checkbox' id='$name' name='$name' value='CCNZMarkerImage'/>";

        $form_fields['is_marker'] = array(
      'label' => __("Google Marker Image"),
      'input' => 'html',
      'html' =>  $html, 
      'helps' => __("If this is used as Google marker image, check this.")
        );
      }
    }
    return $form_fields;
  }

  //send to editor
  function gp_process_markerimage($html, $attachment_id, $attachment)
  {

    if(isset($attachment['is_marker']) &&
    $attachment['is_marker']=='CCNZMarkerImage')
    {
      $html= get_image_tag($attachment_id, 'CCNZMarkerImage', '', '', 'CCNZMarkerImage');

      $html = '<a href="#">'.$html.'</a>';
       
    }
    return $html;
  }

  function meta_box_scripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('my-upload');
  }
   
  function meta_box_styles() {
    wp_enqueue_style('thickbox');
  }

   

  function add_post_enctype() {
    echo '
        <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery("#post").attr("enctype", "multipart/form-data");
            jQuery("#post").attr("encoding", "multipart/form-data");
        });
        </script>';
  }



  function add_geo_finder() {
    echo '
    <script type="text/javascript">
        jQuery(document).ready(function(){
          
        jQuery("#gp_get_geofinder_button").click(function() {
           formfield =escape(jQuery(".gplocationfield").val());
           tb_show("Get Latitude and Longitude", "'.plugins_url( 'cleancodenzgf-core.php', __FILE__ ).'?location="+formfield+"&amp;TB_iframe=true");
           return false;
         });
   
        send_to_editor= function(htmlstr) {
           var imgurl = jQuery("img",htmlstr);
               
            if(imgurl!=null && imgurl.length>0)
            {             
              if(imgurl.attr("alt")=="CCNZMarkerImage")
              {
               jQuery(".gpmarkerimagefield").val(imgurl.attr("src"));
              }
              else
              {
                jQuery(".gpimagefield").val(imgurl.attr("src"));
             }
            }
            else
            {      
                var hiddenfields = jQuery("input",htmlstr);
               if(hiddenfields!=null && hiddenfields.length==3)
               {                  
                    jQuery(".gplocationfield").val(hiddenfields[0].value);
                    jQuery(".gpgeolatfield").val(hiddenfields[1].value);
                    jQuery(".gpgeolongfield").val(hiddenfields[2].value);
               }
            }
            tb_remove();
          }
        });
        </script>';
  }

  function validMetatBoxForType($posttype)
  {
    $valid = false;
    foreach ($this->_meta_boxes as $meta_box)
    {
      if($meta_box['type']==$posttype)
      {
        $valid = true;
        break;
      }
    }

    return $valid;
  }

  /// Add meta box for multiple post types
  function add() {
    foreach($this->_meta_boxes as $meta_box)
    {
      add_meta_box('gp_metabox_'.$meta_box['id'],
      $meta_box['name'],
      array(&$this, 'show'),
      $meta_box['type'],
      'normal',
      'high',
      null);
    }
     
  }

  // Callback function to show fields in meta box
  function show($post,$metaboxdef) {

    // to find meta fields of this metaboxdef
    $metafields = null;
    foreach ( $this->_meta_boxes as $metadef)
    {
      if($metadef['type']==$post->post_type)
      {
        $metafields = $metadef['fields'];
      }
    }

    if($metafields!=null)
    {

      // Use nonce for verification
      echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

      echo '<table class="form-table">';


      foreach ($metafields as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);

        echo '<tr>',
                    '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                    '<td>';
        switch ($field['type']) {
          case 'text':
            echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
                        '<br />', $field['desc'];
            break;
          case 'textarea':
            echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
                        '<br />', $field['desc'];
            break;
          case 'select':
            echo '<select name="', $field['id'], '" id="', $field['id'], '">';

            $alloptions = call_user_func($field['options']);
             
            foreach ($alloptions as $option) {
              echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
            }
            echo '</select>';
            break;
          case 'radio':
            foreach ($field['options'] as $option) {
              echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
            }
            break;
          case 'checkbox':
            echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
            break;
          case 'file':
            echo $meta ? "$meta<br />" : '', '<input type="file" name="', $field['id'], '" id="', $field['id'], '" />',
                        '<br />', $field['desc'];
            break;
          case 'image':
            echo  $meta ? "<img src=\"$meta\" width=\"50\" height=\"50\" /><br />" : '',
                  '<input class="gpimagefield" type="text"  name="', $field['id'],'" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:90%"  />',
                  '<a title="Add an Image" class="thickbox" id="add_image" href="media-upload.php?post_id='.$post->ID.'&amp;type=image&amp;TB_iframe=1" style="padding:0 0 5px 10px;"><img alt="Add an Image" src="'.plugins_url( 'arrow.png', __FILE__ ).'?v001" width="16" height="16" /></a>',
                        '<br />', $field['desc'];
            break;
          case 'markerimage':
            echo  $meta ? "<img src=\"$meta\" width=\"50\" height=\"50\" /><br />" : '',
                  '<input class="gpmarkerimagefield" type="text"  name="', $field['id'],'" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:90%"  />',
                  '<a title="Add a Marker Image" class="thickbox" id="add_marker_image" href="media-upload.php?post_id='.$post->ID.'&amp;type=image&amp;marker=1&amp;TB_iframe=1" style="padding:0 0 5px 10px;"><img alt="Add a Marker Image" src="'.plugins_url( 'arrow.png', __FILE__ ).'?v001" width="16" height="16" /></a>',
                      '<br />', $field['desc'];
            break;


          case 'geolocation':
            echo  '<input class="gplocationfield" type="text"  name="', $field['id'],'" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:90%" />',
                '<a id="gp_get_geofinder_button" title="Get Latitude and Longitude" class="thickbox" style="padding:0 0 5px 10px;"><img alt="Get Latitude and Longitude" src="'.plugins_url( 'arrow.png', __FILE__ ).'?v001" width="16" height="16" /></a>',

                        '<br />', $field['desc'];
            break;

          case 'geolatitude':
            echo '<input class="gpgeolatfield" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
                        '<br />', $field['desc'];
            break;

          case 'geolongitude':
            echo '<input class="gpgeolongfield" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
                        '<br />', $field['desc'];
            break;
        }
        echo    '<td>',
                '</tr>';
      }

      echo '</table>';
    }
  }

  // Save data from meta box
  function save($post_id) {
    global $post;

    // verify nonce
    if (!wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))) {
      return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    // check permissions
    if ('page' == $_POST['post_type']) {
      if (!current_user_can('edit_page', $post_id)) {
        return $post_id;
      }
    } elseif (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }

    // try to find the fields def of this type

    $metafields = null;

    foreach ( $this->_meta_boxes as $metadef)
    {
      if($post->post_type ==$metadef['type'])
      {
        $metafields = $metadef['fields'];
      }
    }

    if($metafields!=null)
    {

      foreach ($metafields as $field) {
        $name = $field['id'];

        $old = get_post_meta($post_id, $name, true);
        $new = $_POST[$field['id']];

        if ($field['type'] == 'file') {
          $file = wp_handle_upload($_FILES[$name], array('test_form' => false));
          $new = $file['url'];
        }

        if ($new && $new != $old) {
          update_post_meta($post_id, $name, $new);
        } elseif ('' == $new && $old && $field['type'] != 'file' && $field['type'] != 'image') {
          delete_post_meta($post_id, $name, $old);
        }
      }
    }
  }
}

