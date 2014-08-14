<?php
require('require/class.Connection.php');
require('require/class.Spotter.php');

//for the date manipulation into the query
if($_GET['start_date'] != "" && $_GET['end_date'] != ""){
	$start_date = $_GET['start_date'].":00";
	$end_date = $_GET['end_date'].":00";
  $sql_date = $start_date.",".$end_date;
} else if($_GET['start_date'] != ""){
	$start_date = $_GET['start_date'].":00";
  $sql_date = $start_date;
} else if($_GET['start_date'] == "" && $_GET['end_date'] != ""){
	$end_date = date("Y-m-d H:i:s", strtotime("2014-04-12")).",".$_GET['end_date'].":00";
  $sql_date = $end_date;
}

//for altitude manipulation
if($_GET['highest_altitude'] != "" && $_GET['lowest_altitude'] != ""){
	$end_altitude = $_GET['highest_altitude'];
	$start_altitude = $_GET['lowest_altitude'];
  $sql_altitude = $start_altitude.",".$end_altitude;
} else if($_GET['highest_altitude'] != ""){
	$end_altitude = $_GET['highest_altitude'];
	$sql_altitude = $end_altitude;
} else if($_GET['highest_altitude'] == "" && $_GET['lowest_altitude'] != ""){
	$start_altitude = $_GET['lowest_altitude'].",60000";
	$sql_altitude = $start_altitude;
}

//calculuation for the pagination
if($_GET['limit'] == "")
{
  if ($_GET['number_results'] == "")
  {
  $limit_start = 0;
  $limit_end = 25;
  $absolute_difference = 25;
  } else {
	if ($_GET['number_results'] > 1000){
		$_GET['number_results'] = 1000;
	}
	$limit_start = 0;
	$limit_end = $_GET['number_results'];
	$absolute_difference = $_GET['number_results'];
  }
}  else {
	$limit_explode = explode(",", $_GET['limit']);
	$limit_start = $limit_explode[0];
	$limit_end = $limit_explode[1];
}
$absolute_difference = abs($limit_start - $limit_end);
$limit_next = $limit_end + $absolute_difference;
$limit_previous_1 = $limit_start - $absolute_difference;
$limit_previous_2 = $limit_end - $absolute_difference;

if ($_GET['download'] == "true")
{
	header('Content-disposition: attachment; filename="barriespotter.yaml"');
}

header("Content-type: text/csv");

$spotter_array = Spotter::searchSpotterData($_GET['q'],$_GET['registration'],$_GET['aircraft'],strtolower(str_replace("-", " ", $_GET['manufacturer'])),$_GET['highlights'],$_GET['airline'],$_GET['airline_country'],$_GET['airline_type'],$_GET['airport'],$_GET['airport_country'],$_GET['callsign'],$_GET['departure_airport_route'],$_GET['arrival_airport_route'],$sql_altitude,$sql_date,$limit_start.",".$absolute_difference,$_GET['sort'],'');
      
$output .= "-flights:\n";

if (!empty($spotter_array))
{
  foreach($spotter_array as $spotter_item)
  {
	date_default_timezone_set('America/Toronto');   	
    $output .= ' - callsign: '.$spotter_item['ident'];
    $output .= "\n";
    $output .= ' - registration: '.$spotter_item['registration'];
    $output .= "\n";
    $output .= ' - aircraft_icao: '.$spotter_item['aircraft_type'];
    $output .= "\n";
    $output .= ' - aircraft_name: '.$spotter_item['aircraft_name'];
    $output .= "\n";
    $output .= ' - airline: '.$spotter_item['airline_name'];
    $output .= "\n";
    $output .= ' - departure_airport_icao: '.$spotter_item['departure_airport'];
    $output .= "\n";
    $output .= ' - departure_airport: '.$spotter_item['departure_airport_city'].' '.$spotter_item['departure_airport_name'];
    $output .= "\n";
    $output .= ' - arrival_airport_icao: '.$spotter_item['arrival_airport'];
    $output .= "\n";
    $output .= ' - arrival_airport: '.$spotter_item['arrival_airport_city'].' '.$spotter_item['arrival_airport_name'];
    $output .= "\n";
    $output .= ' - photo: '.$spotter_item['image_thumbnail'];
    $output .= "\n";
    $output .= ' - date: '.date("c", strtotime($spotter_item['date_iso_8601']));
    $output .= "\n\n";
  }
 }

print $output;

?>