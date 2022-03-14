<?php
if(ISSET($_GET['format'])){
  date_default_timezone_set('America/Mexico_City');
  $date = new DateTime();
  echo $date->format($_GET['format']);
}

?>
