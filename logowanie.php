<?php
include 'konfiguracja.php';
if (isset($_POST['login']) && isset($_POST['pass'])){
  $login=htmlentities($_POST['login']);
  $haslo=htmlentities($_POST['pass']);
  $rs=mysqli_query($conn,"SELECT Count(id) FROM uzytkownicy WHERE nick='$login' AND haslo=sha1('$haslo')");
  $adm = mysqli_query($conn,"SELECT admin FROM uzytkownicy WHERE nick='$login' AND haslo=sha1('$haslo')");
  $admin = mysqli_fetch_array($adm)[0];
	$rec=mysqli_fetch_array($rs);
  if ($rec[0]>0){
    session_start();
	$_SESSION['login']=$_POST['login'];
  $_SESSION['admin']=$admin;
    header("Location: glowna.php?" . SID);
    exit();
  } else
    $error = "<B>Błędny login lub hasło!</B><BR>";
} else
  $error = false;
if (isset($_POST['login']) && isset($_POST['pass'])){
  $login=htmlentities($_POST['login']);
	$pass=htmlentities($_POST['pass']);
  $rs=mysqli_query($conn,"INSERT INTO  uzytkownicy(nick,haslo) VALUES ('$login','$pass' ");
}
?>
<HTML>
<HEAD>
  <TITLE>Logowanie</TITLE>
</HEAD>
<BODY>
<?php
  echo $error ? $error : "";
?>
  <B>Podaj login i haslo</B>
  <FORM method="POST">
    Login: <INPUT type="text" name="login"><BR>
    Hasło: <INPUT type="password" name="pass"><BR>
    <INPUT type="submit" value="Zaloguj siê">
	
  </FORM>
</BODY>
</HTML> 