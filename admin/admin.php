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

	// Calcule le menu côté back
	$requete = "SELECT d.*,m.* FROM droits d 
					INNER JOIN menus m ON d.id_menu=m.id_menu 
					WHERE d." . $_SESSION['statut_compte'] . "='oui' 
					AND m.type_menu='back' ORDER BY m.rang_menu";
	$menu_back = afficher_menus($connexion,$requete,"menu_back");

	if (isset($_GET['module'])) {
		$contenu = "form_" . $_GET['module'] . ".html";

		switch ($_GET['module']) {
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
	
			case 'droits':
				include_once("droits.php");
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

	if($_SESSION['parametres']){
		// Couleurs du site : on récupère les valeurs actuelles
		$css_file = @file_get_contents($css_colors);
		preg_match_all('/--color_(\d):\s?(#[0-9a-f]{6,8});/', $css_file, $matches);
		$colors = array_pop($matches); // les couleurs sont dans le dernier array de $matches
		fclose($css_file);
	}

	// on referme la connexion à la bdd
	$connexion = mysqli_close($connexion);

	include("admin.html");

}else{
    header("Location:../index.php");
}
?>