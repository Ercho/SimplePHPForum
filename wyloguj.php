<?php
session_start();
?>
<HTML>
<HEAD>
  <TITLE>Wylogowanie</TITLE>
</HEAD>
<BODY>
<?php
  echo "Użytkownik " . $_SESSION["login"];
  echo " został wylogowany.";
  session_destroy();
?>
<a href="logowanie.php">Logowanie</a>
</BODY>
</HTML>
