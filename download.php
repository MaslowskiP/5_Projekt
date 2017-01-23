<?php

	$name = $_GET['file'];
    $file = $name.".txt";
	unlink($file);
	$link = mysqli_connect("###", "###", "###", "###");//tutaj jest adres bazy danych oraz login i hasło
	$sql = "SELECT * FROM Reviews INNER JOIN Names ON Reviews.proID=Names.proID WHERE revID=".$name."  ORDER BY proName";




	$result = mysqli_query($link, $sql);
	
	if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
		
		
		
		$row["revCons"] = substr($row["revCons"], 1);
		$row["revPros"] = substr($row["revPros"], 1);
		$row["revReco"] = substr($row["revReco"], 2);
       $text = "Nazwa Produktu: ".$row["proName"]."\r\nData recenzji: ".$row["revDate"]."\r\nNazwa użytkownika: ".$row["revName"]."\r\nWady: ".$row["revCons"]."\r\nZalety: ".$row["revPros"]."\r\nOpinia: ".$row["revText"]."\r\nOcena: ".$row["revStar"]."\r\nRekomendacja: ".$row["revReco"]."\r\nIlość osób uznających tą ocene za pomocną: ".$row["revFor"]."\r\nIlość osób uważająca tą ocene za nie przydatną: ".$row["revAgainst"];
    }
} else {
    $text = "Brak takiego wpisu";
}
    
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