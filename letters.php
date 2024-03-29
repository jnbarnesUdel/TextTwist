<?php
//Access-Control_Allow_Oregin;
$str_json = file_get_contents('php://input');
json_decode($str_json);
$fullStr = "";
$count = 25;
for ($i = 0; $i < $count; $i++){
    $str = $str_json[$i];
    $fullStr = $fullStr.$str;
}
$fullStr = str_replace('"', "", $fullStr);
$fullStr= str_replace('[', "", $fullStr);
$fullStr = str_replace(']', "", $fullStr);
$fullStr = str_replace(' ', "", $fullStr);
$fullStr = str_replace(',', "", $fullStr);

//get subracks
$racks = [];
for($i = 0; $i < pow(2, strlen($fullStr)); $i++){
	$ans = "";
	for($j = 0; $j < strlen($fullStr); $j++){
		//if the jth digit of i is 1 then include letter
		if (($i >> $j) % 2) {
		  $ans .= $fullStr[$j];
		}
	}
	if (strlen($ans) > 1){
  	    $racks[] = $ans;	
	}
}
$racks = array_unique($racks);

$words = "";
foreach ($racks as $var){
$var = '"'.$var.'"';
$dbhandle = new PDO("sqlite:scrabble.sqlite") or die("Failed to open DB");
if (!$dbhandle) die ($error);


$query = "SELECT rack, words FROM racks WHERE rack = $var ";


$statement = $dbhandle->prepare($query);
$statement->execute();

//The results of the query are typically many rows of data
//there are several ways of getting the data out, iterating row by row,
//I chose to get associative arrays inside of a big array
//this will naturally create a pleasant array of JSON data when I echo in a couple lines
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
// print_r($results);
foreach ($results as $parts){
    foreach ($parts as $stuff){
            $words .= $stuff;
            $words .= ",";
        }
    }
}

//this part is perhaps overkill but I wanted to set the HTTP headers and status code
//making to this line means everything was great with this request
header('HTTP/1.1 200 OK');
//this lets the browser know to expect json
header('Content-Type: application/json');
//this creates json and gives it back to the browser
echo json_encode($words);