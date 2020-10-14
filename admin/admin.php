<?php
session_start();

if (isset($_SESSION['id_compte'])) {

	require_once("../outils/fonctions.php");
	$connexion = connexion();


	if (isset($_GET['module'])) {
		$val = $_GET['module'];
		$contenu = "form_" . $val . ".html";

		switch ($val) {
			case 'comptes':
				include_once("comptes.php");
				break;

			case 'actus':
				break;

			case 'slider':
				break;

			case 'messages':
				include_once("messages.php");
				break;

			default:
				$contenu = "intro.html";
				break;
		}
	} else {
		$contenu = "intro.html";
	}

	// on calcule les notifications de nouveaux messages
	$requete = "SELECT lu FROM contacts WHERE lu=0";
	$resultat = mysqli_query($connexion, $requete);
	$nb_lignes = mysqli_num_rows($resultat);
	$notification = " <span class=\"notif\">" . $nb_lignes . "</span>";

	// on referme la connexion à la bdd
	$connexion = mysqli_close($connexion);

	include("admin.html");

}else{
    header("Location:../index.php");
}
?>