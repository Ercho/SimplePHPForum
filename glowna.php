<?php
include 'konfiguracja.php';
session_start();
if (!isset($_SESSION["login"])){
  header("Location: logowanie.php");
 exit();
}
?>
<HTML>
<HEAD>
  <TITLE>Strona główna</TITLE>
  <style>
	table, tr, td{
		border-collapse:collapse;
	}
	th, td{
		background-color: #ffd700;
		color: #222;
	}
	body{
		background-color: #444;
		width: 100vw;
		height: 100vh;
		overflow: hidden;
		padding: 0;
		margin: 0;
		color: #dcdcdc;
	}
	a {
		color: #fff;
	}
	.art {
		background-color: #bdb3a8;
		color: #111;
		border-radius: 10px;
	}
	.imydz {
		position: relative;
		left: 170px;
		width: 60px;
	}
	.content {
		margin-top: 60px;
		font-size: 18px;
		margin-left: 30px;
		width: 340px;
	}
  </style>
</HEAD>
<BODY>
<?php
  echo "<div style='position:absolute; right:10px'>Witaj " . $_SESSION["login"];
  echo "<A href='wyloguj.php?" . SID . "'>";
  echo "[Wyloguj]</A></div>";
    
	if(!$conn) die('Brak polaczenia z serwerem');
	$edycja=False;
	$tytul='';
	$tresc='';
	$id='';
	$autor='';
	$obrazek='';
	$admin='';
	if(isset($_POST['action']))
	{
		switch($_POST['action'])
		{
			case'Dodaj':
			if(!empty($_POST['tytul'])&&!empty($_POST['tresc']))
			{
				if($_FILES['plik']['name'] != '' && $_FILES['plik']['type'] == 'image/jpeg' && is_uploaded_file($_FILES['plik']['tmp_name'])){
					
				$tytul=htmlspecialchars($_POST['tytul']);
				$tresc=htmlspecialchars($_POST['tresc']);
				
				$tresc = strip_tags($tresc);
				$tresc = trim($tresc);

	
				$autor=$_SESSION['login'];
				
				$obrazek = addslashes(file_get_contents($_FILES['plik']['tmp_name']));		
				$sql="INSERT INTO artykuly (tytul, tresc, autor, zdjecie) VALUES ('$tytul' ,'$tresc', '$autor', '$obrazek');";
				$conn->query($sql)
					or die ('Rekord nie został dodany');				
				$tytul='';
				$tresc='';
				$id='';
				$autor='';
				$obrazek='';
				}
				else{
					echo '<div style="padding:4px; position:absolute; bottom:10px; right:10px; background-color:white; border:1px black solid">Rekord nie może zostać dodany - sprawdź poprawność pliku!</div>';
				}
			}
			else
			{
					echo '<div style="padding:4px; position:absolute; bottom:10px; right:10px; background-color:white; border:1px black solid">Rekord nie może zostać dodany -  uzupełnij wszystkie pola!</div>';
			}
			break;
			case'Edytuj':
			if(isset($_POST['id']))
			{
				$sql="SELECT * FROM artykuly WHERE id=".$_POST['id'];
				$wynik=$conn->query($sql) or die('Rekord nie został znaleziony');
				if($wynik->num_rows>0)
				{
					$rekord=$wynik->fetch_array();
					$tytul=$rekord['tytul'];
					$tresc=$rekord['tresc'];
					$id=$_POST['id'];
					$edycja=True;
				}
			}
			break;				
			case'Edytuj':
			if(!empty($_POST['tytul'])&&!empty($_POST['tresc']))
			{
				$tytul=$_POST['tytul'];
				$tresc=$_POST['tresc'];
				$id=$_POST['id'];
				$conn->query("UPDATE artykuly SET tytul='$tytul', tresc='$tresc' WHERE id=".$_POST['id'])
				or die ('Rekord nie został zapisany');		
				if($_FILES['plik']['name'] != '' && $_FILES['plik']['type'] == 'image/jpeg' && is_uploaded_file($_FILES['plik']['tmp_name'])){
					$obrazek = addslashes(file_get_contents($_FILES['plik']['tmp_name']));
					$conn->query("UPDATE artykuly SET zdjecie='$obrazek' WHERE id=".$_POST['id'])
					or die ('Rekord nie został zapisany');
				}			
				$edytuj=False;
				
				$tytul='';
				$tresc='';
				$id='';
				$obrazek='';
			}
			else{
				echo '<div style="padding:4px; position:absolute; bottom:10px; left: 400px; background-color:white; border:1px black solid">Rekord nie może zostać aktualizowany - uzupełnij pola!</div>';
			}		
				break;
			case'Usun':
			if(isset($_POST['id']))
			{
				$conn->query("DELETE FROM artykuly WHERE id=".$_POST['id'])
				or die ('Rekord nie został usuniety');
			}
			break;
			case 'Przeczytaj':
			if(isset($_POST['id']))
			{
				$sql="SELECT * FROM artykuly WHERE id=".$_POST['id'];
				$wynik=$conn->query($sql) or die('Rekord nie został znaleziony');
				if($wynik->num_rows>0)
				{
					$_SESSION['idek']=$_POST['id'];
					$rekord=$wynik->fetch_array();
					$title=$rekord['tytul'];
					$text=$rekord['tresc'];
					$idek=$rekord['id'];
					$image=$rekord['zdjecie'];
					echo '<div class="art" style="position:absolute; border:1px black solid; right:100px; top:60px; width: 400px">';
					echo '<div><h1 style="text-align:center">'.$title.'</h1>';
					echo '<img class="imydz" src="data:image/jpeg;base64,'.base64_encode( $image ).'"/></div>';
					echo '<div class="content" style="float:left">'.$text.'</div>';
					echo'</div>';
				}
			}
			break;			
		}
	}
	$sql="SELECT * FROM artykuly";
	$rs=mysqli_query($conn, $sql);
	if($rs->num_rows>0)
	{
	$pom=1;
	echo "<table border=1px black solid, border-collapse=collapse style='position: absolute; left: 250px'>";
	echo "<tr><th>%</th><th>Tytuł</th><th>Autor Artykułu</th><th>Edytuj*</th><th>Usuń*</th><th>Otwórz artykuł</th></tr>";
	while($r=mysqli_fetch_array($rs))
	{
		echo "<form method=POST><tr>";
		echo '<td>'.$pom .'</td>' ;
		echo "<td><input type='hidden' name='id' value=".$r['id']."> ".$r['tytul'] ."</td>";
		echo '<td>  '.$r['autor'] ."</td>" ;
		if($r['autor']==$_SESSION["login"] || $_SESSION["admin"] == 1)
		{
			echo '<td><input type="submit" name="action" value="Edytuj"/></td>' ;
			echo "<td><input type='submit' value='Usun' name='action'/></td>" ;
		}
		else
		{
			echo '<td colspan="2"> brak uprawnień</td>' ;
		
		}
		echo "<td><input type='submit' value='Przeczytaj' name='action'/></td>";
		echo "</tr></form>";
		$pom+=1;
	}
	echo "</table>";
	}

	echo('
    <div style="width:200px; position:absolute; left:20px; top:20px">
	 <form id=main method=POST enctype=multipart/form-data>
	<input type="hidden" name="id" value="'.$id.'"/>
	Tytuł Artykułu<input type="text" name="tytul" value="'.$tytul.'"/>
	Treść artykułu<textarea name="tresc" style="width:180px; height:400px" form="main">'.$tresc.'</textarea>
	
	<input type="hidden" name="MAX_FILE_SIZE" value="1024000" />
	Załącznik w postaci zdjęcia JPEG<input type="file" name="plik" />
	
	
	
	<input type="submit" value='.(($edycja)?'Zapisz':'Dodaj').' name="action" style="margin-top:5px"/>
	</form></div>');
	

?>
 
</BODY>
</HTML>
