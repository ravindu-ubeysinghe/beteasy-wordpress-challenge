
<?php
/**
 * Plugin Name: BetEasy WordPress Challenge
 * Plugin URI: https://www.beteasy.com.au
 * Description: A plugin that fetches most recent races via BetEasy REST API. Simply use [be_display_races] on any content block after installation.
 * Version: 1.0
 * Author: Ravindu Ubeysinghe
 * Author URI: https://www.linkedin.com/in/ravinduubeysinghe/
 */

 /**
  * API URL
  */
define("BE_API_URL", "https://s3-ap-southeast-2.amazonaws.com/bet-easy-code-challenge/next-to-jump");

/**
 * Enqueue styles
 */
function load_styles() {
  wp_enqueue_style( 'be-custom-styles', plugins_url( 'style.css' , __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'load_styles' );

/**
 *
 * Fetch data from the REST API
 *
 * @return array an array containing the decoded json response received from the API.
 *                false if the api responds with an http code other than 200
 *
 */
function get_races(){
  $api_url = BE_API_URL;
  $curl = curl_init($api_url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  $curl_response = curl_exec($curl);
  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);
  $curl_jason = json_decode($curl_response, true);
  if($httpcode == 200){
    return $curl_jason;
  }else{
    return false;
  }
}

/**
 *
 * Mapping icons with event imagetypes
 *
 * @return string the icon pertaining to the event type
 *
 */
function get_icon($event_type_id){
  switch($event_type_id){
    case 1:
      return "https://beteasy.com.au/promotionslanding/wp-content/uploads/sites/2/2018/10/icon-horse.png";
      break;
    case 2:
      return "https://beteasy.com.au/promotionslanding/wp-content/uploads/sites/2/2018/10/icon-harness.png";
      break;
    case 3:
      return "https://beteasy.com.au/promotionslanding/wp-content/uploads/sites/2/2018/10/icon-dogs.png";
      break;
    default:
      return "https://beteasy.com.au/promotionslanding/wp-content/themes/crownbet-promotions/img/logo-beteasy-primary.png";
  }
}

/**
 *
 * Print an event row within the events loop
 *
 * @param array event array
 *
 */
function display_results_print_row($event){
  ?>
  <tr>
    <td class="be-event-type">
      <img src="<?php echo get_icon($event['EventType']['EventTypeID']); ?>" />
    </td>
    <td class="be-event-venue-name">
      <div class="be-event-venue"><?php echo $event['Venue']['Venue']; ?></div>
      <div class="be-event-name"><?php echo $event['EventName']; ?></div>
    </td>
    <td class="be-event-time">
      <strong>Event starting at</strong> <?php echo date("g:i:s a \o\\n d, M Y ", strtotime($event['AdvertisedStartTime'])); ?>
    </td>
  </tr>
  <?php
}

/**
 *
 * Outputs the shortcode view including the events table
 *
 */
function display_results(){
  $events = get_races();
  if(is_array($events)){
  ?>
    <div class="be-title">Next Up</div>
    <div class="be-filter-set">
      <div id="be-filter-reset">
        <p>RESET</p>
      </div>
      <div id="be-filter-thoroughbred">
        <img src="https://beteasy.com.au/promotionslanding/wp-content/uploads/sites/2/2018/10/icon-horse.png" />
      </div>
      <div id="be-filter-trots">
        <img src="https://beteasy.com.au/promotionslanding/wp-content/uploads/sites/2/2018/10/icon-harness.png" />
      </div>
      <div id="be-filter-greyhound">
        <img src="https://beteasy.com.au/promotionslanding/wp-content/uploads/sites/2/2018/10/icon-dogs.png" />
      </div>
    </div>
    <table class="be-table">
    <?php
    foreach($events['result'] as $event){

      if(isset($_GET['type'])){
        if($event['EventType']['EventTypeID'] == $_GET['type']){
          display_results_print_row($event);
        }
      }else{
        display_results_print_row($event);
      }
    }
    ?>
    </table>

    <script>
      jQuery('document').ready(function(){
        jQuery('#be-filter-thoroughbred').click(function(){
          window.location.href = window.location.href.split('?')[0] + "?type=1";
        });
        jQuery('#be-filter-trots').click(function(){
          window.location.href = window.location.href.split('?')[0] + "?type=2";
        });
        jQuery('#be-filter-greyhound').click(function(){
          window.location.href = window.location.href.split('?')[0] + "?type=3";
        });
        jQuery('#be-filter-reset').click(function(){
          window.location.href = window.location.href.split('?')[0];
        });
      });
    </script>
    <?php
  }else{
    ?>
    <p class="be-error"><strong>We're experiencing some issues. Please try again later!</strong></p>
    <?php
  }
}

add_shortcode('be_display_races', 'display_results');
