<?php
// Field Types Config| 
$FieldTypeTitle = 'GPS Coordinates';
$FieldDescription = 'Allows for the capturing of GPS Coordanates and shown as a Map using Google Maps';
$FieldVersion = '1.0';
$FieldAuthor = 'David Cramer';
$FieldURL = 'http://dbtoolkit.digilab.co.za';
$isVisible = true;
$FieldTypes = array();

$FieldTypes['coordinates'] 		= array('name' => 'GPS Coordinates'	, 'func' => 'gps_setup'	, 'visible' => true, 'captionsOff' => true);

?>