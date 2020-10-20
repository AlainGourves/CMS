<?php
session_start();


if (isset($_SESSION['id_compte'])) {
	// Si la personne est autorisée à accéder au back, 
	// calcule une phrase de bienvenue
	$bienvenue = $_SESSION['prenom_compte']. " ". substr($_SESSION['nom_compte'], 0, 1). ".";
	if (!empty($_SESSION['fichier_compte'])) {
		$bienvenue .= $_SESSION['fichier_compte'];
	}else{
		$bienvenue .= "<span class=\"dashicons dashicons-admin-users avatar\"></span>";
	}
	$bienvenue .= "[statut: ". $_SESSION['statut_compte']. "]";

	require_once("../outils/fonctions.php");
	$connexion = connexion();

	if (isset($_GET['module'])) {
		$val = $_GET['module'];
		$contenu = "form_" . $val . ".html";

		switch ($val) {
			case 'deconnecter':
				// détruit l'ensemble des variables de session
				session_destroy();
				header("Location:../log");
				break;

			case 'menus':
				include_once("menus.php");
				break;
	
			case 'comptes':
				include_once("comptes.php");
			break;
			
			case 'actus':
			break;
			
			case 'slider':
				include_once("sliders.php");
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
	if ($nb_lignes > 0) {
		$notification = " <span class=\"notif\">" . $nb_lignes . "</span>";
	}

	// on referme la connexion à la bdd
	$connexion = mysqli_close($connexion);

	include("admin.html");

}else{
    header("Location:../index.php");
}
?>