<?php

	$name = $_GET['file'];
    $file = $name.".csv";
	unlink($file);
	$test = 'all_products';
	$text = "";
	$link = mysqli_connect("###", "###", "###", "###");//tutaj jest adres bazy danych oraz login i has³o
	if (strcmp($name, $test) == 0) {
	$sql = "SELECT * FROM Reviews INNER JOIN Names ON Reviews.proID=Names.proID";
	
	$result = mysqli_query($link, $sql);
	
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
		
		
		
			$row["revCons"] = substr($row["revCons"], 1);
			$row["revPros"] = substr($row["revPros"], 1);
			$row["revReco"] = substr($row["revReco"], 2);
			
			$row["proName"] = trim($row["proName"]);
			$row["revDate"] = trim($row["revDate"]);
			$row["revName"] = trim($row["revName"]);
			$row["revCons"] = trim($row["revCons"]);
			$row["revPros"] = trim($row["revPros"]);
			$row["revText"] = trim($row["revText"]);
			$row["revStar"] = trim($row["revStar"]);
			$row["revReco"] = trim($row["revReco"]);
			$row["revFor"] = trim($row["revFor"]);
			$row["revAgainst"] = trim($row["revAgainst"]);
			
			$text = $text.$row["proName"].";".$row["revDate"].";".$row["revName"].";".$row["revCons"].";".$row["revPros"].";".$row["revText"].";".$row["revStar"].";".$row["revReco"].";".$row["revFor"].";".$row["revAgainst"]."\r\n";
			}
		} else {
			$text = "Brak takiego wpisu22222";
			}


	
	} else {
		
		$sql = "SELECT * FROM Reviews INNER JOIN Names ON Reviews.proID=Names.proID WHERE Reviews.proID=".$name;

		$result = mysqli_query($link, $sql);
	
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {

			$row["revCons"] = substr($row["revCons"], 1);
			$row["revPros"] = substr($row["revPros"], 1);
			$row["revReco"] = substr($row["revReco"], 2);
			
			$row["proName"] = trim($row["proName"]);
			$row["revDate"] = trim($row["revDate"]);
			$row["revName"] = trim($row["revName"]);
			$row["revCons"] = trim($row["revCons"]);
			$row["revPros"] = trim($row["revPros"]);
			$row["revText"] = trim($row["revText"]);
			$row["revStar"] = trim($row["revStar"]);
			$row["revReco"] = trim($row["revReco"]);
			$row["revFor"] = trim($row["revFor"]);
			$row["revAgainst"] = trim($row["revAgainst"]);
			
			$text = $text.$row["proName"].";".$row["revDate"].";".$row["revName"].";".$row["revCons"].";".$row["revPros"].";".$row["revText"].";".$row["revStar"].";".$row["revReco"].";".$row["revFor"].";".$row["revAgainst"]."\r\n";
			}
		} else {
			$text = "Brak takiego wpisu";
			}
	}  
	
	$text = trim($text);
	
	$fp = fopen($file, "a"); 
	flock($fp, 2); 
	fwrite($fp, $text); 
	flock($fp, 3); 
	fclose($fp); 

    $type = filetype($file);


    header("Content-type: $type");
    header("Content-Disposition: attachment;filename={$file}");
    header("Content-Transfer-Encoding: binary"); 
    header('Pragma: no-cache'); 
    header('Expires: 0');

    set_time_limit(0); 
    readfile($file);
	unlink($file);







?>