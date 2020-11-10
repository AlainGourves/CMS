<?php

require_once("../outils/fonctions.php");

$connexion = connexion();
$requete = "SELECT * FROM articles WHERE flux_article='1' ORDER BY date_article DESC";
$flux_rss = generer_flux_rss($connexion, $requete);

$rss_xml = fopen("../feed/rss.xml", "w");
fputs($rss_xml, $flux_rss);
fclose($rss_xml);
mysqli_close($connexion);

header("Location:rss.xml");
?>