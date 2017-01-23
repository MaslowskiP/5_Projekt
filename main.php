<?php
echo '<html><head><meta http-equiv="Content-Type" content="text/html;charset=UTF-8" ></head><title>Projekt ETL</title>';
echo '<link rel="stylesheet" type="text/css" href="style.css"><body>';

$e_button = enabled; //enabled
$t_button = disabled;
$l_button = disabled;
$etl_button = enabled;
$text_thing = enabled;
$link = mysqli_connect("###", "###", "###", "###");//tutaj jest adres bazy danych oraz login i hasło

if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
	echo 'Nie połączyłem się z bazą...';
} else {
	//echo 'Wszystko gra'; //tylko do testów
}

function doE($input) {
	
	unlink('reviews.txt');
	unlink('specs.txt');
	$site = "http://www.ceneo.pl/".$input."#tab=spec";
	$html = file_get_contents($site);
	
	preg_match_all('/<th>(.*?)<\/th>/s', $html, $matches); //wycinanie kategori

	preg_match_all('/<li class="attr-value">(.*?)<\/li>/s', $html, $matches2); //wycinanie cech

	preg_match_all('/<h2 class="section-title with-context header-curl"><strong>(.*?)<\/strong>/s', $html, $matches3); // wycinanie nazwy

	preg_match_all('/<li class="page-tab reviews">(.*?)<\/li>/s', $html, $matches5); // obliczanie ile będzie stron do wyciecia
	
	preg_match_all('/data-GACategoryName="(.*?)data-City/s', $html, $matches6);
	
	
	foreach ($matches5[1] as $node) { //obliczam ile stron będzie do zrobienia
		$node = strip_tags($node);
		
		$number_total_reviews = preg_replace("/[^0-9]/","",$node);
		$number_total_reviews_new = $number_total_reviews+(10-($number_total_reviews%10));
		$number_total_page = ($number_total_reviews_new/10);

			break;
	}
	

	
	foreach ($matches3[1] as $node) { //wycinanie nazwy do zmiennej $fullname
			$fullname = $node;
			break;
	}

	$specs = $fullname;
	
	foreach ($matches6[1] as $node) { //wycinanie kategori
			$cat = $node;
			break;
	}

	
	//$cat = strstr($cat, '/');
	$cats = explode('/', $cat);
	$cat = str_replace('"', '', $cats[2]);
	$specs  = $specs ."\n".$cat;
	
	$model = strstr($fullname, ' ');
	$model = trim($model);
	
	$name = strstr($fullname, ' ', true);
	
	$specs  = $specs ."\n".$name;
	$specs  = $specs ."\n".$model;
	
	
	foreach (array_combine($matches[1], $matches2[1]) as $category => $data) { //wycinanie kategorii i cech do zmiennej
		
		$data2 = strip_tags($data);
		//$category = preg_replace('/\s+/',' ', $category);
		//$data = preg_replace('/\s+/',' ', $data);
		$category = trim($category);
		$data = trim($data);
		$specs = $specs."\n".$category."\n".$data2;
	}
	
	
	$specs = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $specs); //wycinanie pustych linijek
	$specs = str_replace("  ", '', $specs); //usuwanie tabulatorow
	
	$file = "specs.txt"; 
	$fp = fopen($file, "a"); 
	flock($fp, 2); 
	fwrite($fp, $specs); 
	flock($fp, 3); 
	fclose($fp); 
	// tutaj koniec specyfikacji  <button class="vote-yes js_product-review-vote js_vote-yes" data-icon="&#xe00d;" data-url='/SetOpinionVote' data-review-id="2828828" data-vote="1" data-voted="false" data-product-id="24493469"> DODAC TO!!!!!
	//
	
	// http://www.ceneo.pl/40988869#tab=reviews
	
	$site2 = "http://www.ceneo.pl/".$input."#tab=reviews";
	
	for($i=1; $i < ($number_total_page+1); $i++) { //duza petla
		$counter = 1;
		
		$html = file_get_contents($site2);
		preg_match_all('/<li class="product-review js_product-review">(.*?)<div class="product-review-toolbar">/s', $html, $matches4);
	
		foreach ($matches4[1] as $node) {
			
			$counter = $counter+1;
			
			$review = $node;
			//$message5 = $review; //do testów
			
				preg_match_all('/data-review-id="(.*?)"/s', $review, $matches_id);//wycinam id recenzji żeby wywalać duble
				foreach ($matches_id[1] as $node) {
					$revs = $revs.$node."\n";
					//$message = $message + 1; //do testów
					break;
				}
			
			
			
				preg_match_all('/<time datetime="(.*?)">/s', $review, $matches_date);//wycinam date <td class="tg-yw4l">
				foreach ($matches_date[1] as $node) {
					$revs = $revs.$node."\n";
					//$message = $message + 1; //do testów
					break;
				}
			
				preg_match_all('/<div class="product-reviewer">(.*?)<\/div>/s', $review, $matches_name);//wycinam imie
				foreach ($matches_name[1] as $node) {
					$node = str_replace("  ", '', $node); //usuwanie tabulatorow przy anonimach
					$node = trim($node); //usuwanie nowych lini 
					$revs = $revs.$node."\n";

					break;
				}
				
				
				preg_match_all('/<div class="cons-cell">(.*?)<\/div>/s', $review, $matches_cons_all);//wycinam wady na 2 razy <div class="cons-cell"> 
				foreach ($matches_cons_all[1] as $node) {
					
					//$message5 = $node; //do testów
						preg_match_all('/<li>(.*?)<\/li>/s', $node, $matches_cons);
							foreach ($matches_cons[1] as $node_cons) {
								$cons = " ".$cons.$node_cons.",";
							}
					$cons = rtrim($cons, ','); //wywala ostatni przecinek zeby było ładnie!
					$cons = str_replace("  ", '', $cons); //usuwanie tabulatorow
					$cons = trim($cons);
					$revs = $revs."- ".$cons."\n";
					$cons = "";
				}
				
				
				preg_match_all('/<div class="pros-cell">(.*?)<\/div>/s', $review, $matches_pros_all);//wycinam zalety na 2 razy <div class="pros-cell">
				foreach ($matches_pros_all[1] as $node) {
					
					//$message5 = $node;
						preg_match_all('/<li>(.*?)<\/li>/s', $node, $matches_pros);
							foreach ($matches_pros[1] as $node_pros) {
								$pros = " ".$pros.$node_pros.",";
							}
					$pros = rtrim($pros, ','); //wywala ostatni przecinek zeby było ładnie!
					$pros = str_replace("  ", '', $pros); //usuwanie tabulatorow
					$pros = trim($pros);
					$revs = $revs."- ".$pros."\n";
					$pros = "";
				}
				
				preg_match_all('/<p class="product-review-body">(.*?)<\/p>/s', $review, $matches_text);//wycinam opis
				foreach ($matches_text[1] as $node) {
					$node = strip_tags($node);
					$node = trim(preg_replace('/\s+/', ' ', $node));
					$revs = $revs.$node."\n";
					break;
				}
				
				preg_match_all('/<span class="review-score-count">(.*?)<\/span>/s', $review, $matches_points);//wycinam ocene
				foreach ($matches_points[1] as $node) {
					$revs = $revs.$node."\n- ";
					break;
				}
				
				
				preg_match_all('/<em class="product-recommended">(.*?)<\/em>/s', $review, $matches_recomendation);//wycinam polecenie <em class="product-not-recommended">Nie polecam</em>
				foreach ($matches_recomendation[1] as $node) {
					$revs = $revs.$node;
					break;
				}
				
				preg_match_all('/<em class="product-not-recommended">(.*?)<\/em>/s', $review, $matches_norecomendation);//wycinam  Nie polecam
				foreach ($matches_norecomendation[1] as $node) {
					$revs = $revs.$node;
					break;
				}
				

				
				preg_match_all('/<span id="votes-yes-.*?>(.*?)<\/span>/s', $review, $matches_for);//wycinam za a nawet przeciw xD
				foreach ($matches_for[1] as $node) {
					$revs = $revs."\n".$node."\n";

					break;
				}
				
				preg_match_all('/<span id="votes-no-.*?>(.*?)<\/span>/s', $review, $matches_against);//wycinam za a nawet przeciw xD
				foreach ($matches_against[1] as $node) {
					$revs = $revs.$node."\n";

					break;
				}
			
			//break; //narazie dawac brake bo serwer wybuchnie
			//$message5 = $counter." zmiena i: ".$i."<br>";
			if ($counter == 11) {
				$site2 = "http://www.ceneo.pl/".$input."/opinie-".($i+1);

				break;
			}
			
		}
	}
	
	$revs = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $revs); //wycinanie pustych linijek
	$file = 'reviews.txt'; 
	$fp = fopen($file, "a"); 
	flock($fp, 2); 
	fwrite($fp, $revs); 
	flock($fp, 3); 
	fclose($fp); 
	$revs = "";
	// podsumowanie wszystkich dzialan w tym czyms
	$info = ($number_total_reviews);

	return $info;

	
}

function doT() {
	
	$revs = "";
	$phrase = "Użytkownik Ceneo";
	$anom = file('reviews.txt');
	$lines = count(file('reviews.txt'));
		for ($i=0 ; $i<$lines; $i++) {
			if (strpos($anom[$i], $phrase) !== false) {
				$anom[$i] = "Anonim\n";
			}
			$revs = $revs.$anom[$i];
		}
	$file = 'reviewsT.txt'; 
	$fp = fopen($file, "a"); 
	flock($fp, 2); 
	fwrite($fp, $revs); 
	flock($fp, 3); 
	fclose($fp); 
	unlink('reviews.txt');
	rename('reviewsT.txt' ,'reviews.txt');

}

function doL($input) {
	
	$input = $input;
	$link = mysqli_connect("###", "###", "###", "###");//tutaj jest adres bazy danych oraz login i hasło
	$product = file('specs.txt'); 
	$amount = count(file('specs.txt'));
	
		
		
	$sql = "SELECT * FROM Names WHERE proID=".$input;
	$result = mysqli_query($link, $sql);

	if (mysqli_num_rows($result) > 0) {
	//echo "Produkt już jest w bazie"; //do testów
} else {
    $sql = "INSERT INTO Names (proID, proName, proCat, proCompany, proModel) VALUES ('".$input."', '".$product[0]."', '".$product[1]."', '".$product[2]."', '".$product[3]."')";//dodaje nazwe produktu do tabeli Names
	
	mysqli_query($link, $sql);

		for ($i=4 ; $i<$amount; $i = ($i + 2)) {
		
		$sql = "INSERT INTO Products (proID, proCategory, proData) VALUES ('".$input."', '".$product[$i]."', '".$product[($i+1)]."')"; //dodaje pokolei wszystkie cechy danego produktu
		mysqli_query($link, $sql);
		
	}
	
}

	$reves = file('reviews.txt');
	$amount = count(file('reviews.txt'));
	$amount--;
	$counter = 0;
	for ($i = 0; $i < $amount; $i=$i) {					//dodawanie recenzji do bazy
		
		$sql = "SELECT * FROM Reviews WHERE revID=".$reves[$i];
		$result = mysqli_query($link, $sql);

		if (mysqli_num_rows($result) > 0) {
			//echo "Produkt już jest w bazie"; //do testów
			$i = ($i + 10);
		} else { 
			
		$sql = "INSERT INTO Reviews (revID, revDate, revName, revCons, revPros, revText, revStar, revReco, revFor, revAgainst, proID) VALUES ('".$reves[$i]."', '".$reves[($i+1)]."','".$reves[($i+2)]."', '".$reves[($i+3)]."','".$reves[($i+4)]."', '".$reves[($i+5)]."','".$reves[($i+6)]."', '".$reves[($i+7)]."','".$reves[($i+8)]."', '".$reves[($i+9)]."', '".$input."')"; //dodaje recenzje do bazy
		mysqli_query($link, $sql);
			//if(mysqli_query($link, $sql)){
				//echo "Produkt dodany do bazy"; //do testow
			//} else{
			//	echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
		//}
		$i = ($i + 10);
		$counter++;		
		}	
	}
	
	return $counter;
}

function displayTable($input) {
	
	$table = '<table class="tg"><tr><th class="tg-yw4l">Data</th><th class="tg-yw4l">Autor</th><th class="tg-yw4l">Wady</th><th class="tg-yw4l">Zalety</th><th class="tg-yw4l">Posumowanie opinii</th><th class="tg-yw4l">Ocena</th><th class="tg-yw4l">Polecam / Nie Polecam</th><th class="tg-yw4l">Opinia przydatna</th><th class="tg-yw4l">Opinia nie przydatna</th></tr>';
	
	$link = mysqli_connect("###", "###", "###", "###");//tutaj jest adres bazy danych oraz login i hasło
	
	$sql = "SELECT * FROM Reviews WHERE proID=".$input;
	$result = mysqli_query($link, $sql);
	
	if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
		
		$row["revCons"] = substr($row["revCons"], 1);
		$row["revPros"] = substr($row["revPros"], 1);
		$row["revReco"] = substr($row["revReco"], 2);
        $table = $table.'<tr><td class="tg-yw4l">'.$row["revDate"].'</td><td class="tg-yw4l">'.$row["revName"].'</td><td class="tg-yw4l">'.$row["revCons"].'</td><td class="tg-yw4l">'.$row["revPros"].'</td><td class="tg-yw4l">'.$row["revText"].'</td><td class="tg-yw4l">'.$row["revStar"].'</td><td class="tg-yw4l">'.$row["revReco"].'</td><td class="tg-yw4l">'.$row["revFor"].'</td><td class="tg-yw4l">'.$row["revAgainst"].'</tr>';
    }
} else {
    echo "0 results";
}

	return $table;
	
}

function displayProduct($input) {
	

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


if(isset($_POST['SubmitButton'])){ 
	$input = $_POST['inputText']; 
	$number_total_reviews = doE($input);
	$productName = file("specs.txt");
	$productName[0] = trim($productName[0]);
	doT();
	$counter = doL($input);
	$message = 'Zakończony cały proces ETL dla produktu '.$productName[0].'. Dodano '.$counter.' nowych wpisów do bazy danych. Wczasie operacji stworzono następnie usunięto 2 tymczasowe pliki. Wszystkie recenzje przedstawia poniższa tabelka';
	unlink('reviews.txt');
	unlink('specs.txt');
	$table_all = displayTable($input);
	$message2 = displayProduct($input);
	
} 

if(isset($_POST['SubmitButtonE'])){ 

	$input = $_POST['inputText']; 
	$number_total_reviews = doE($input);
	$e_button = disabled; 
	$t_button = enabled;
	$l_button = disabled; 
	$etl_button = disabled;
	$text_thing = readonly;
	$productName = file("specs.txt");
	$productName[0] = trim($productName[0]);
	$message =  'Zakończono proces "Extract". Stworzono plik zawierający charakterystykę produktu oraz plik zawierający '.($number_total_reviews).' opinii dla produktu: '.$productName[0];
	
}


if(isset($_POST['SubmitButtonT'])){

	doT();
	$e_button = disabled; 
	$t_button = disabled;
	$l_button = enabled; 
	$etl_button = disabled;
	$text_thing = readonly;
	$message = 'Zakończono proces "Transform". Dane są gotowę do wgrania do bazy danych.';
	
}	


if(isset($_POST['SubmitButtonL'])){


	$input = $_POST['inputText']; 
	$counter = doL($input);
	$e_button = enabled; 
	$t_button = disabled;
	$l_button = disabled;
	$etl_button = enabled;
	$text_thing = enabled;
	$message = 'Zakończono proces "Load". Dodano '.$counter.' rekordów. Wszystkie recenzje znajdują się w tabelce poniżej.';
	unlink('reviews.txt');
	unlink('specs.txt');
	$table_all = displayTable($input);
	$message2 = displayProduct($input);
	
}

if(isset($_POST['SubmitButtonDelete'])){
	
	$link = mysqli_connect("###", "###", "###", "###");//tutaj jest adres bazy danych oraz login i hasło
	$sql = "TRUNCATE TABLE Reviews";
	mysqli_query($link, $sql);
	$sql = "TRUNCATE TABLE Names";
	mysqli_query($link, $sql);
	$sql = "TRUNCATE TABLE Products";
	mysqli_query($link, $sql);
	$message = "Wyczyszczono bazę danych";
}




echo '<form action="" method="post">';
echo '<input type="text" name="inputText" value="24493469"'.$text_thing.'/>';
echo '<input type="submit" name="SubmitButton" value="ETL" '.$etl_button.'/> Podaj kod produktu';
echo '<BR>';

echo '<input type="submit" name="SubmitButtonE" value="E" '.$e_button.'/>';
echo '<input type="submit" name="SubmitButtonT" value="T" '.$t_button.'/>';
echo '<input type="submit" name="SubmitButtonL" value="L" '.$l_button.'/>';
echo '<input type="submit" name="SubmitButtonDelete" value="Wyczyść całą bazę danych" />';
echo '<BR>';
echo '<a href="http://maslo.c0.pl/list.php">Przeglądaj opinie dodanych wcześniej produktów</a>';
echo '</form>';



echo '<textarea readonly rows="2" disabled cols="150">'.$message;
echo '</textarea><br>';
echo $message2;
echo $table_all;
echo '</body>';








?> 