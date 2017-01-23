<?php
echo '<html><head><meta http-equiv="Content-Type" content="text/html;charset=UTF-8" ></head><title>Projekt ETL</title><body>';
echo '<link rel="stylesheet" type="text/css" href="style.css">';
$link = mysqli_connect("###", "###", "###", "###");//tutaj jest adres bazy danych oraz login i hasło


if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
	echo 'Nie połączyłem się z bazą...';
} else {
	//echo 'Wszystko gra'; //tylko do testów
}


$sql= "SELECT proID, proName FROM Names ORDER BY proName";
$select = '<select name="select"><option value ="all_products">* Wybierz wszystkie *</option>';

$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
		$select = $select.'<option value="'.$row["proID"].'">'.$row["proName"].'</option>';
    }
} 
$select = $select.'</select>';


function displayTable($input) {
	
	$test = 'all_products';
	
	$table = '<table class="tg"><tr><th class="tg-yw4l">Pobierz</th><th class="tg-yw4l">Data</th><th class="tg-yw4l">Autor</th><th class="tg-yw4l">Wady</th><th class="tg-yw4l">Zalety</th><th class="tg-yw4l">Posumowanie opinii</th><th class="tg-yw4l">Ocena</th><th class="tg-yw4l">Polecam / Nie Polecam</th><th class="tg-yw4l">Opinia przydatna</th><th class="tg-yw4l">Opinia nie przydatna</th></tr>';
	
	$link = mysqli_connect("###", "###", "###", "###");//tutaj jest adres bazy danych oraz login i hasło
	
	$sql = "SELECT * FROM Reviews WHERE proID=".$input;
	
	if (strcmp($input, $test) == 0) {
		$sql = "SELECT * FROM Reviews INNER JOIN Names ON Reviews.proID=Names.proID ORDER BY proName";
		$table = '<table class="tg"><tr><th class="tg-yw4l">Pobierz</th><th class="tg-yw4l">Produkt</th><th class="tg-yw4l">Data</th><th class="tg-yw4l">Autor</th><th class="tg-yw4l">Wady</th><th class="tg-yw4l">Zalety</th><th class="tg-yw4l">Posumowanie opinii</th><th class="tg-yw4l">Ocena</th><th class="tg-yw4l">Polecam / Nie Polecam</th><th class="tg-yw4l">Opinia przydatna</th><th class="tg-yw4l">Opinia nie przydatna</th></tr>';
	
		$result = mysqli_query($link, $sql);
	
	if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {

		$row["revCons"] = substr($row["revCons"], 1);
		$row["revPros"] = substr($row["revPros"], 1);
		$row["revReco"] = substr($row["revReco"], 2);
        $table = $table.'<tr><td class="tg-yw4l"><a href="download.php?file='.$row["revID"].'">TXT</a></td><td class="tg-yw4l">'.$row["proName"].'</td><td class="tg-yw4l">'.$row["revDate"].'</td><td class="tg-yw4l">'.$row["revName"].'</td><td class="tg-yw4l">'.$row["revCons"].'</td><td class="tg-yw4l">'.$row["revPros"].'</td><td class="tg-yw4l">'.$row["revText"].'</td><td class="tg-yw4l">'.$row["revStar"].'</td><td class="tg-yw4l">'.$row["revReco"].'</td><td class="tg-yw4l">'.$row["revFor"].'</td><td class="tg-yw4l">'.$row["revAgainst"].'</tr>';
    }
} else {
    echo "0 results";
}
	
	return $table;

}
	
	
	$result = mysqli_query($link, $sql);
	
	if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
		
		
		
		$row["revCons"] = substr($row["revCons"], 1);
		$row["revPros"] = substr($row["revPros"], 1);
		$row["revReco"] = substr($row["revReco"], 2);
        $table = $table.'<tr><td class="tg-yw4l"><a href="download.php?file='.$row["revID"].'">TXT</a></td><td class="tg-yw4l">'.$row["revDate"].'</td><td class="tg-yw4l">'.$row["revName"].'</td><td class="tg-yw4l">'.$row["revCons"].'</td><td class="tg-yw4l">'.$row["revPros"].'</td><td class="tg-yw4l">'.$row["revText"].'</td><td class="tg-yw4l">'.$row["revStar"].'</td><td class="tg-yw4l">'.$row["revReco"].'</td><td class="tg-yw4l">'.$row["revFor"].'</td><td class="tg-yw4l">'.$row["revAgainst"].'</tr>';
    }
} else {
    echo "0 results";
}

	return $table;
	
}

function displayProduct($input) {
	
	$test = 'all_products';
	
	if (strcmp($input, $test) == 0) {
		$productMessage = "";
		return $productMessage;
	}

	$link = mysqli_connect("###", "###", "###", "###");//tutaj jest adres bazy danych oraz login i hasło 
	
	$sql = "SELECT * FROM Names WHERE proID=".$input;
	$result = mysqli_query($link, $sql);

	if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $productMessage = '<h1>'.$row["proName"].'<h1><h2>Kategoria - '. $row["proCat"].'<br>Marka - '.$row["proCompany"].'<br>Model - '.$row["proModel"].'</h2>';
    }
} else {
    echo "0 results";
}
	
	$productMessage = $productMessage.'<table>';
	
	$sql = "SELECT * FROM Products WHERE proID=".$input;
	$result = mysqli_query($link, $sql);

	if (mysqli_num_rows($result) > 0) {

    while($row = mysqli_fetch_assoc($result)) {
        $productMessage = $productMessage.'<tr><td>'.$row["proCategory"].'</td><td>'. $row["proData"].'</td></tr>';
    }
} else {
    echo "0 results";
}
	
	$productMessage = $productMessage.'</table>';
	
	
	return $productMessage;
}

function createCSV($input) {
	
		
	$csv = '<a href="createcsv.php?file='.$input.'">Pobierz plik CSV</a>';
		
	return $csv;

	
	
	
}




if(isset($_POST['SubmitButton'])){ 

	$input = $_POST['select'];
	$table_all = displayTable($input);
	$message2 = displayProduct($input);
	$csv = createCSV($input);


}






echo '<a href="http://maslo.c0.pl/main.php">Dodaj nowy produkt</a>';
echo '<form action="" method="post">';
echo $select;
echo '<input type="submit" name="SubmitButton" value="Pokaż"/>';

echo '</form>';
echo $csv;
echo $message2;
echo $table_all;











?>