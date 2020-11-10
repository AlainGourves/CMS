<?php
session_start();

// Couleurs du site
$css_colors = "../css/colors.css";

if (isset($_SESSION['id_compte'])) {
	// Si la personne est autorisée à accéder au back, 
	// calcule une phrase de bienvenue
	$bienvenue = "<div><span class=\"user_name\">";
	$bienvenue .= $_SESSION['prenom_compte']. " ". substr($_SESSION['nom_compte'], 0, 1). ".</span>";
	$bienvenue .= "<span class=\"user_status\">[". $_SESSION['statut_compte']. "]</span></div>";
	if (!empty($_SESSION['fichier_compte'])) {
		$bienvenue .= $_SESSION['fichier_compte'];
	}else{
		$bienvenue .= "<span class=\"dashicons dashicons-admin-users avatar flex-center\"></span>";
	}

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
			
			case 'articles':
				include_once("articles.php");
			break;
			
			case 'slider':
				include_once("sliders.php");
				break;

			case 'messages':
				include_once("messages.php");
				break;
	
			case 'config':
				include_once("config.php");
				break;

			default:
				$contenu = "intro.html";
				break;
		}
	} else {
		$contenu = "intro.html";
	}

	// Couleurs du site : on récupère les valeurs actuelles
	$css_file = @file_get_contents($css_colors);
	preg_match_all('/--color_(\d):\s?(#[0-9a-f]{6,8});/', $css_file, $matches);
	$colors = array_pop($matches); // les couleurs sont dans le dernier array de $matches
	fclose($css_file);

	// on calcule les notifications de nouveaux messages
	$requete = "SELECT lu FROM contacts WHERE lu=0";
	$resultat = mysqli_query($connexion, $requete);
	$nb_lignes = mysqli_num_rows($resultat);
	if ($nb_lignes > 0) {
		$notification = " <span class=\"notif flex-center\">" . $nb_lignes . "</span>";
	}

	// on referme la connexion à la bdd
	$connexion = mysqli_close($connexion);

	include("admin.html");

}else{
    header("Location:../index.php");
}
?>