<?php
/**
 * Plugin Name:       Parafrasear
 * Plugin URI:        https://parafrasear.org/
 * Description:       Parafrasear proporciona la mejor solución para parafrasear
 * Version:           1.0
 * Author:            ASKSEO
 * Author URI:        https://askseo.me/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       parafrasear
 */


 if (!defined('ABSPATH')) {
  echo 'Activate plugin first';
  exit;
}



  // enqueue scripts start

 function enqueue_scripts_parafrasear() {

  $directory = plugin_dir_url(__FILE__);

    if (get_current_screen()->id === 'post' || get_current_screen()->id === 'page') {

      wp_enqueue_style(
        'style-parafrasear',
        $directory . 'css/style.css',
        array(),
        1,
        'all'
    );

      wp_enqueue_script('script-parafrasear', $directory . 'js/main.js', 'jQuery', '1.0.0', true);
      wp_localize_script('script-parafrasear', 'parafrasear_param', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));

    }
  }
  add_action('admin_enqueue_scripts', 'enqueue_scripts_parafrasear');

  // enqueue scripts end




 
 // Add a custom meta box to the post and page editor screens
function add_meta_box_parafrasear() {
    add_meta_box(
      'meta-box-parafrasear', // Unique ID
      'Parafrasear', // Box title
      'meta_box_callback_parafrasear', // Callback function
      array('post', 'page'), // Post and pages types to display the meta box
      'side', // Context
      'high' // Priority
    );
  }
  add_action('add_meta_boxes', 'add_meta_box_parafrasear');
  
  // Define the content of the meta box
  function meta_box_callback_parafrasear($post) {
    
    // Output your meta box content here
    require plugin_dir_path( __FILE__ ). 'views/front.php';

  }




  //Plugin Ajax Paraphrase Function
function paraphrase_result_action() {
  
  global $wpdb;

  $text = sanitize_text_field($_POST['text']);
  $text = $wpdb->prepare('%s', $text);

             if(is_numeric($text))
             {
               echo json_encode(array(
                 "error" => "Ingrese solo la cadena",
                 "errortype" => "string"
             ));
               wp_die();
             }
            elseif (strlen($text) === 0)
            {
                echo json_encode(array(
                    "error" => "Empty Input! Try entering some text in input box.",
                    "errortype" => "empty-input"
                ));
                wp_die();
            }
            else
            {
                
              $url = 'http://162.0.237.226/parafrasear-gratis';
              $body = array(
                  'text' => $text,
                  'level' => '1',
                  'dest' => 'es',
              );
              
              
              $response = wp_remote_post( $url, array(
                  'method' => 'POST',
                  'body' => $body,
              ) );



                if ( is_wp_error( $response ) ) {
                    $error_message = $response->get_error_message();
                    // handle error
                    echo wp_kses_data($error_message);
                    wp_die();

                } else {
                    $response_body = wp_remote_retrieve_body( $response );
                    // handle response

                    echo wp_kses_data($response_body) ;
                    wp_die();
                }

            }

        
    
}
add_action('wp_ajax_paraphrase_result_action', 'paraphrase_result_action');

