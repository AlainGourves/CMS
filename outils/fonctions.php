<?php
/**
 * ************************* FONCTIONS
 * 		- connexion()
 * 		- protocole()
 * 		- security()
 * 		- login()
 * 		- fichier_type()
 * 		- redimage()
 * 		- avatar()
 * 		- format_date()
 * 		- envoi_mel()
 * 		- afficher_contacts()
 * 		- afficher_comptes()
 * 		- extrait()
 * 		- afficher_articles()
 * 		- afficher_menus()
 * 		- afficher_droits()
 * 		- afficher_sliders()
 * 		- afficher_pages()
 * 		- maDate()
 * 		- generer_flux_rss()
 */


//===============================
// la fonction connecter() permet de choisir une
// base de données et de s'y connecter.

function connexion() {
	require_once("connect.php");
	//si numéro de port
	//$connexion = mysqli_connect(SERVEUR,LOGIN,PASSE,BASE,PORT) or die("Error " . mysqli_error($connexion));
	//si pas de numéro de port	
	$connexion = mysqli_connect(SERVEUR,LOGIN,PASSE,BASE) or die("Error " . mysqli_error($connexion));
	mysqli_set_charset($connexion, "utf8mb4"); // pour une gestion complète de l'UTF8 par mySQL
	
	return $connexion;
}

//================================================
function protocole() {
	if(isset($_SERVER['HTTPS'])) {
		$protocole="https://";	
	} else {
		$protocole="http://";	
	}
	//$protocole="http://";
	return $protocole;	
}
	
//================================
function security($chaine) {
	$connexion=connexion();
	$security=addcslashes(mysqli_real_escape_string($connexion,$chaine), "%_");
	mysqli_close($connexion);
	return $security;
}

//===========================pour se loguer=======================================================
function login($login,$password) {	
	$connexion=connexion();
	$login=security($login);
	$password=security($password);

	$requete="SELECT * FROM comptes WHERE login_compte= '" . $login . "' AND pass_compte=SHA1('" . $password . "')";
	$resultat=mysqli_query($connexion, $requete);
	$nb=mysqli_num_rows($resultat);
	
	if($nb==0) {
		return false;
	}else{ 
		$ligne=mysqli_fetch_object($resultat);
		
		//on stocke en mémoire de session les infos que l'on souhaite afficher sur l'accueil du back
		$_SESSION['id_compte']=$ligne->id_compte;
		$_SESSION['prenom_compte']=$ligne->prenom_compte;    
		$_SESSION['nom_compte']=$ligne->nom_compte;
		$_SESSION['statut_compte']=$ligne->statut_compte;		
		if(!empty($ligne->fichier_compte)) {
			$_SESSION['fichier_compte']="<img src=\"" . $ligne->fichier_compte . "\" alt=\"avatar\" class=\"avatar\" />";
		}
		// Savoir s'il faut permettre en fonction du statut l'affichage du réglage des paramètres
		$requete="SELECT d.". $ligne->statut_compte. " FROM droits d
				INNER JOIN menus m
				ON d.id_menu=m.id_menu
				WHERE m.intitule_menu='Paramètres'";
		$resultat=mysqli_query($connexion, $requete);
		$ligne=mysqli_fetch_array($resultat);
		$_SESSION['parametres'] = ($ligne[0]=='oui') ? true : false;
		header("Location:../admin/admin.php");    
		return true;
	}		
	mysqli_close($connexion); 	
}


// ====détecter l'extension du fichier================
function fichier_type($uploadedFile) {
	$tabType = explode(".", $uploadedFile);
	$nb=sizeof($tabType)-1;
	$typeFichier=$tabType[$nb];
	if($typeFichier == "jpeg") {
		$typeFichier = "jpg";
	}
	$extension=strtolower($typeFichier);
	return $extension;
}


//============================================
function redimage($img_src, $img_dest, $dst_w, $dst_h, $quality) {
	if (!isset($quality)) {
		$quality = 100;
	}
	$extension = fichier_type($img_src);

	// Lit les dimensions de l'image
	$size = @getimagesize($img_src);
	$src_w = $size[0];
	$src_h = $size[1];
	// Crée une image vierge aux bonnes dimensions   truecolor
	$dst_im = @imagecreatetruecolor($dst_w, $dst_h);
	imagealphablending($dst_im, false);
	imagesavealpha($dst_im, true);

	// Copie dedans l'image initiale redimensionnée  
	if ($extension == "jpg") {
		$src_im = @ImageCreateFromJpeg($img_src);
		imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

		// Sauve la nouvelle image
		@ImageJpeg($dst_im, $img_dest, $quality);
	}

	if ($extension == "png") {
		$src_im = @ImageCreateFromPng($img_src);
		imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

		// Sauve la nouvelle image
		@ImagePng($dst_im, $img_dest, 0);
	}

	if ($extension == "gif") {
		$src_im = @ImageCreateFromGif($img_src);
		imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

		// Sauve la nouvelle image
		@ImagePng($dst_im, $img_dest, 0);
	}
	// Détruis les tampons
	@ImageDestroy($dst_im);
	@ImageDestroy($src_im);
}

//============================================
function avatar($img_src, $img_dest, $dst_w, $dst_h, $quality) {
	if (!isset($quality)) {
		$quality = 100;
	}
	$extension = fichier_type($img_src);

	// Lit les dimensions de l'image
	$size = @getimagesize($img_src);
	$src_w = $size[0];
	$src_h = $size[1];
	$ratio = $src_w/$src_h;
	// calcule l'origine de l'image de destination en fct de l'aspect ratio
	if ($ratio > 1) {
		// format paysage
		$src_x = ($src_w - $src_h)/2;
		$src_y = 0;
		$src_w = $src_h;
	}elseif($ratio < 1) {
		// portrait
		$src_x = 0;
		$src_y = ($src_h - $src_w)/2;
		$src_h = $src_w;
	}else{
		// carré
		$src_x = 0;
		$src_y = 0;
	}

	// Crée une image vierge aux bonnes dimensions   truecolor
	$dst_im = @imagecreatetruecolor($dst_w, $dst_h);
	imagealphablending($dst_im, false);
	imagesavealpha($dst_im, true);

	// Copie dedans l'image initiale redimensionnée  
	if ($extension == "jpg") {
		$src_im = @ImageCreateFromJpeg($img_src);
		imagecopyresampled($dst_im, $src_im, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		// Sauve la nouvelle image
		@ImageJpeg($dst_im, $img_dest, $quality);
	}

	if ($extension == "png") {
		$src_im = @ImageCreateFromPng($img_src);
		imagecopyresampled($dst_im, $src_im, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		// Sauve la nouvelle image
		@ImagePng($dst_im, $img_dest, 0);
	}

	if ($extension == "gif") {
		$src_im = @ImageCreateFromGif($img_src);
		imagecopyresampled($dst_im, $src_im, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		// Sauve la nouvelle image
		@ImagePng($dst_im, $img_dest, 0);
	}
	// Détruis les tampons
	@ImageDestroy($dst_im);
	@ImageDestroy($src_im);
}
//===============================
function format_date($date,$format) {
	if($format=="anglais") {
		$tab_date=explode("/",$date);
		$date_au_format=$tab_date[2] . "-" . $tab_date[1] . "-" . $tab_date[0];	
	}
	if($format=="francais") {
		$tab_date=explode("-",$date);
		$date_au_format=$tab_date[2] . "/" . $tab_date[1] . "/" . $tab_date[0];	
	}
	return $date_au_format;	
}

//===============================================

 function envoi_mel($destinataire,$sujet,$message_txt, $message_html,$expediteur) {
  if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $destinataire)) // On filtre les serveurs qui rencontrent des bogues.
    {
  	$passage_ligne = "\r\n";
    }
  else
    {
  	$passage_ligne = "\n";
    }
   
  //=====Création de la boundary
  $boundary = "-----=" . md5(rand());
  //==========
   
  //=====Création du header de l'email
  $header = "From: \"" . $_SESSION['expediteur'] . "\"<" . $expediteur . ">" . $passage_ligne;
  $header.= "Reply-to: \"" . $_SESSION['expediteur'] . "\" <" . $expediteur . ">" . $passage_ligne;
  $header.= "MIME-Version: 1.0" . $passage_ligne;
  $header.= "X-Priority: 3" . $passage_ligne;//1 : max et 5 : min
  $header.= "Content-Type: multipart/alternative;" . $passage_ligne . " boundary=\"" . $boundary . "\"" . $passage_ligne;
  //==========
   
  //=====Création du message
  $message = $passage_ligne . "--" . $boundary. $passage_ligne;
  //=====Ajout du message au format texte
  $message.= "Content-Type: text/plain; charset=\"UTF-8\"" . $passage_ligne;
  $message.= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
  $message.= $passage_ligne . $message_txt . $passage_ligne;
  //==========
  $message.= $passage_ligne . "--" . $boundary . $passage_ligne;
  //=====Ajout du message au format HTML
  $message.= "Content-Type: text/html; charset=\"UTF-8\"" . $passage_ligne;
  $message.= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
  $message.= $passage_ligne . $message_html . $passage_ligne;
  //==========
  $message.= $passage_ligne . "--" . $boundary."--" . $passage_ligne;
  $message.= $passage_ligne . "--" . $boundary."--" . $passage_ligne;
  //==========
   
  //=====Envoi de l'email
  mail($destinataire,$sujet,$message,$header);  
  }    
  
//=======================================
function afficher_contacts($connexion,$requete) {
	$resultat = mysqli_query($connexion, $requete);
	// on construit un tableau qui affiche tous les messages reçus depuis le front
	$tab_resultats = "\n<table class=\"tab_resultats\" id=\"tab_contacts\">\n";

	// compteur
	$i = 1;
	// tant qu'il y a des lignes dans $resultat, on exploite chaque ligne comme objet
	while ($ligne = mysqli_fetch_object($resultat)) {
		// Si le message n'a pas été lu
		if ($ligne->lu == 0) {
			$class = "non_lu";
		} else {
			$class = "lu";
		}
		if (isset($_SESSION['id_contact']) && $ligne->id_contact == $_SESSION['id_contact']) {
			$open = " open";
		} else {
			$open = "";
		}

		$tab_resultats .= "<tr>\n";
		$tab_resultats .= "\t<td class=\"" . $class . $open . "\">\n<a href=\"admin.php?module=messages&action=marquer_message&id_contact=" . $ligne->id_contact . "\">";
		if (!empty($ligne->prenom_contact)) {
			$tab_resultats .= $ligne->prenom_contact . " ";
		}
		$tab_resultats .= $ligne->nom_contact;
		$tab_resultats .= "<span class=\"dashicons ";
		$tab_resultats .= ($open=="") ? "dashicons-arrow-down-alt2" : "dashicons-arrow-up-alt2";
		$tab_resultats .= "\"></span></a></td>\n";
		
		//traitement date
		$date = new DateTime($ligne->date_contact);
		$fmt = new IntlDateFormatter( 
				"fr-FR",
				IntlDateFormatter::MEDIUM,
				IntlDateFormatter::SHORT,
				'Europe/Paris',
				IntlDateFormatter::GREGORIAN
		);
		$tab_resultats .= "\t<td>" .$fmt->format($date). "</td>\n";
		$tab_resultats .= "\t<td>\n";
		$tab_resultats .= "<a href=\"admin.php?module=messages&action=supprimer_message&id_contact=" . $ligne->id_contact . "\"><span class=\"dashicons dashicons-no-alt\"></span></a>";
		$tab_resultats .= "</td>\n</tr>\n";

		// 2e ligne visible si clic
		$tab_resultats .= "<tr>\n";
		$tab_resultats .= "\t<td class=\"" . $open . "\" colspan=\"3\"";
		$tab_resultats .= ">\n<strong>Expéditeur</strong>: ";
		$tab_resultats .= $ligne->mel_contact . "<br><strong>Message</strong>: ";
		$tab_resultats .= $ligne->message_contact;
		$tab_resultats .= "</td>\n";
		$tab_resultats .= "</tr>\n";

		$i++;
	}
	$tab_resultats .= "</table>\n";

	return $tab_resultats;
}
//=======================================
function afficher_comptes($connexion,$requete) {
	$resultat=mysqli_query($connexion,$requete);
	$i=0;
	$affichage="<table class=\"tab_resultats\" id=\"tab_comptes\">\n";
	//on calcule les entêtes des colonnes
	$affichage.="<tr>\n";
	$affichage.="<th class=\"large\">Identité</th>\n";
	$affichage.="<th class=\"medium\">Login</th>\n";
	$affichage.="<th class=\"medium\">Statut</th>\n";
	$affichage.="<th class=\"small\">Avatar</th>\n";	
	$affichage.="<th class=\"small\">Actions</th>\n";
	$affichage.="</tr>\n";	
	while($ligne=mysqli_fetch_object($resultat)) {
		//on affiche le contenu de chaque uplet présent dans la table
		$affichage.="<tr>\n";	
		$affichage.="<td>" . $ligne->nom_compte. " " . $ligne->prenom_compte . "</td>\n";
		$affichage.="<td>" . $ligne->login_compte . "</td>\n";	
		$affichage.="<td>" . $ligne->statut_compte . "</td>\n";	
		if(!empty($ligne->fichier_compte)) {
			// on récupère l'extension du fichier pour calculer un paramètre GET
			$extension = "&ext=".fichier_type($ligne->fichier_compte);
			$avatar = "<figure>";
			$avatar .= "<img src=\"" . $ligne->fichier_compte  . "\" alt=\"Avatar\" />";
			$avatar .= "<figcaption><a href=\"admin.php?module=comptes&action=supprimer_avatar&id_compte=" . $ligne->id_compte . $extension. "\"><span class=\"dashicons dashicons-dismiss\"></span></a></figcaption>";
			$avatar .= "</figure>";
		}else{
			$extension = '';
			$avatar="<span class=\"dashicons dashicons-admin-users\"></span>";	
		}
		$affichage.="<td   class=\"miniature\" \">" . $avatar . "</td>\n";		
		$affichage.="<td>";
		$affichage.="<a href=\"admin.php?module=comptes&action=modifier_compte&id_compte=" . $ligne->id_compte . "\"><span class=\"dashicons dashicons-edit\"></span></a>";
		$affichage.="&nbsp;";
		$affichage.="<a href=\"admin.php?module=comptes&action=supprimer_compte&statut_compte=".$ligne->statut_compte."&id_compte=".$ligne->id_compte. $extension. "\"><span class=\"dashicons dashicons-no-alt\"></span></a>";
		$affichage.="</td>\n";						
		$affichage.="</tr>\n";
		$i++;					
	}
	$affichage.="</table>\n";

	return $affichage;
}
//======================================
function extrait($texte,$nb_mots,$tolerance) {
	//on coupe le texte sur les espaces
	$tab_mots=explode(" ",$texte);
	
	//on compte le nombre de valeurs dans le tableau de variables $tab_mots
	$nb_mots_dans_texte=count($tab_mots);
	
	//si le nb de valeur est inférieur ou égal à $nb_mots
	if($nb_mots_dans_texte<=($nb_mots+$tolerance)) {
		$extrait=$texte;	
	}else{
		//alors il faut raccourcir le texte et garder seulement les $nb_mots premiers mots
		//on fait une boucle qui tourne $nb_mots fois	
		$extrait="";
		for($i=0;$i<$nb_mots;$i++) {
			//au premier tour de boucle
			if($i==0) {
				$extrait.=$tab_mots[$i];	
			}else{
				$extrait.=" " . $tab_mots[$i];
			}
		}
		$extrait.="...";
	}
	return $extrait;
}
	
//=======================================
function afficher_articles($connexion,$requete,$cas) {
	$resultat=mysqli_query($connexion,$requete);
	
	if(isset($cas)){
		switch($cas) {
			case "back":
			$affichage="<table class=\"tab_resultats\" id=\"tab_articles\">\n";
			//on calcule les entêtes des colonnes
			$affichage.="<tr>\n";
			$affichage.="<th class=\"small\">Tri</th>\n";			
			$affichage.="<th class=\"medium\">Titre</th>\n";
			$affichage.="<th class=\"large\">Extrait</th>\n";
			$affichage.="<th class=\"small\">Date</th>\n";	
			$affichage.="<th class=\"small\">RSS</th>\n";	
			$affichage.="<th class=\"small\">Image</th>\n";		
			$affichage.="<th class=\"small\">Actions</th>\n";
			$affichage.="</tr>\n";
			$i = 0;
			$tab_comptes = array();
			while($ligne=mysqli_fetch_object($resultat)) {
				// stocke l'auteur de l'article précédent (pour détecter les changements)
				$tab_comptes[$i] = $ligne->id_compte;
				if($i==0 || ($i>0 && $tab_comptes[$i] != $tab_comptes[$i - 1])){
					$affichage.= "<tr><td colspan=\"7\" class=\"auteur\">";
					$affichage.= $ligne->prenom_compte. " ". $ligne->nom_compte;
					$affichage.= "</td></tr>\n";
				}
				$affichage.="<tr>\n";
				$affichage.="<td><a href=\"admin.php?module=articles&action=trier_article&id_article=" . $ligne->id_article . "&tri=up\"><span class=\"dashicons dashicons-arrow-up\"></span></a>&nbsp;<a href=\"admin.php?module=articles&action=trier_article&id_article=" . $ligne->id_article. "&tri=down\"><span class=\"dashicons dashicons-arrow-down\"></span></a></td>\n";	
				$affichage.="<td>". $ligne->titre_article . "</td>\n";
				$affichage.="<td>". extrait($ligne->contenu_article,8,4) . "</td>\n";
				$affichage.="<td>". maDate($ligne->date_article) . "</td>\n";
				$affichage.="<td>"; 
				if ($ligne->flux_article == 1){
					$affichage .= "<span class=\"dashicons dashicons-rss\"></span>";
				}
				$affichage.="</td>\n";
				$affichage.="<td class=\"miniature\">";
				if(empty($ligne->fichier_article)){
					$affichage.="<span class=\"dashicons dashicons-hidden\"></span></td>";
				}else{
					$affichage.="<figure>";

					$affichage.="<a href=\"". str_replace("_s", "_b", $ligne->fichier_article). "\" target=\"blank\">";
					$affichage.="<img class=\"miniature\" src=\"" . $ligne->fichier_article . "\" alt=\"\" />";
					$affichage.="</a>";

					$affichage.="<figcaption><a class=\"suppr_img\" href=\"admin.php?module=articles&action=supprimer_image&id_article=". $ligne->id_article ."\">
					<span class=\"dashicons dashicons-dismiss\"></span></a></figcaption>";		
				}
				$affichage.="</td>\n";
				$affichage.="<td>";
				$affichage.="<a href=\"admin.php?module=articles&action=modifier_article&id_article=" . $ligne->id_article . "\"><span class=\"dashicons dashicons-edit\"></span></a>";
				$affichage.="&nbsp;";
				$affichage.="<a href=\"admin.php?module=articles&action=supprimer_article&id_article=" . $ligne->id_article . "\"><span class=\"dashicons dashicons-trash\"></span></a>";
				$affichage.="</td>\n";						
				$affichage.="</tr>\n";
				$i++;					
			}
			$affichage.="</table>\n";
			break;


			case "front":
			$affichage="";
			$nom_mois=array("Jan","Fev","Mar","Avr","Mai","Juin","Juil","Aou","Sept","Oct","Nov","Dec");
			$i=0;
			while($ligne=mysqli_fetch_object($resultat)) {				
				//calcul de la date en 3 morceaux
				// se débarsser de l'heure
				$tab_date=explode(" ",$ligne->date_article); 
				$tab_date=explode("-",$tab_date[0]);
				$annee=$tab_date[0];
				$mois=$nom_mois[$tab_date[1]-1];
				$jour=$tab_date[2];

				$affichage.="<article class=\"\">\n";
				$affichage.="<div class=\"date\">\n";
				$affichage.="<span class=\"jj\">" . $jour . "</span>\n";
				$affichage.="<span class=\"mm\">" . $mois . "</span>\n"; 
				$affichage.="<span class=\"aaaa\">" . $annee . "</span>\n";								
				$affichage.="</div>\n";
				if(!empty($ligne->fichier_article)){
					$affichage.="<img src=\"". $ligne->fichier_article . "\" alt=\"" . $ligne->titre_article . "\" />\n";
					// $affichage.="<img src=\"". str_replace("_s","_b",$ligne->fichier_article) . "\" alt=\"" . $ligne->titre_article . "\" />\n";
				}
				$affichage .= "<div class=\"article_texte\">";
				$affichage .="<h2>" . $ligne->titre_article . "</h2>\n";
				$affichage .="<p>" . $ligne->contenu_article . "</p>\n";
				$affichage .= "</div>";
				$affichage .="</article>\n";
				$i++;				
			}	
			break;

			case "home":
			$i = 0;
			$affichage = "";
			while($ligne = mysqli_fetch_object($resultat)){
				if ($i==0){
					// article à la une
					$affichage .= "<article class=\"a_la_une\">\n";
					if (!empty($ligne->fichier_article)){
						$affichage .= "<img src=\"". str_replace("_s", "_b",$ligne->fichier_article). "\" alt=\"". $ligne->titre_article. "\" />";
					}
				}elseif($i==1){
					$affichage .= "<div>\n<article>\n";
				}else{
					$affichage .= "<article>\n";
				}
				$affichage .= "<h2>". $ligne->titre_article. "</h2>\n";
				$affichage .= "<p class=\"ref\">". maDate($ligne->date_article). "</p>\n";
				$texte = extrait($ligne->contenu_article, 20, 5);
				$affichage .= "<p>". $texte. "</p>\n";
				if (strlen($texte) < strlen($ligne->contenu_article)) {
					$affichage .= "<a href=\"front.php?page=single&id_article=". $ligne->id_article."\">&gt; LIRE LA SUITE</a>\n";
				}
				$affichage .= "</article>\n";
				$i++;
			}
				$affichage .= "</div>\n";
			break;

			case "single":
			$ligne = mysqli_fetch_object($resultat);
            $affichage = "<article>\n";
            $affichage = "<h1>". $ligne->titre_article. "</h1>\n";
            $affichage .= "<p class=\"date_single\">". maDate($ligne->date_article). "</p>\n";
            $affichage .= "<img src=\"". str_replace("_s", "_b",$ligne->fichier_article). "\" alt=\"". $ligne->titre_article. "\" />\n";
            $affichage .= "<p>". $ligne->contenu_article. "</p>\n";
            $affichage .= "</article>\n";
			break;
		}		
	}

	return $affichage;
}
	
//=======================================
function afficher_menus($connexion,$requete,$cas) {
	$resultat = mysqli_query($connexion, $requete);
	
	if(isset($cas)){
		switch($cas){
			case "back":
				// on construit un tableau qui affiche tous les menus
				$affichage = "\n<table class=\"tab_resultats\" id=\"tab_menus\">\n";
				$affichage .= "<tr>\n
					<th class=\"small\">Tri</th>\n
					<th class=\"medium\">Intitulé</th>\n
					<th class=\"large\">Lien</th>\n
					<th class=\"small\">Actions</th>\n
				</tr>\n";
				
				while ($ligne = mysqli_fetch_object($resultat)) {
					$affichage .= "<tr>\n";
					$affichage .= "\t<td><a href=\"admin.php?module=menus&action=trier_menu&id_menu=" . $ligne->id_menu . "&tri=up\"><span class=\"dashicons dashicons-arrow-up\"></span></a>&nbsp;<a href=\"admin.php?module=menus&action=trier_menu&id_menu=" . $ligne->id_menu . "&tri=down\"><span class=\"dashicons dashicons-arrow-down\"></span></a></td>\n";
					$affichage .= "\t<td>". $ligne->intitule_menu ."</td>\n";
					$affichage .= "\t<td>". $ligne->lien_menu ."</td>\n";
					$affichage .= "\t<td>";
					$affichage .= "<a href=\"admin.php?module=menus&action=modifier_menu&id_menu=".$ligne->id_menu."\">
					<span class=\"dashicons dashicons-edit\"></span></a>";
					$affichage .= "<a href=\"admin.php?module=menus&action=supprimer_menu&id_menu=".$ligne->id_menu."\">
					<span class=\"dashicons dashicons-no-alt\"></span></a>";
					$affichage .= "</td>\n";
					$affichage .= "</tr>\n";
				}
				$affichage .= "</table>\n";
			break;

			case "menu_front":
				$affichage = "<nav id=\"menu_haut\" role=\"navigation\">\n";
				$affichage .= "<ul>\n";
				while($ligne = mysqli_fetch_object($resultat)){
					// on regarde s'il existe des pages associées à cet item de menu
					$requete2 = "SELECT * FROM pages WHERE id_menu='". $ligne->id_menu. "'";
					$resultat2 = mysqli_query($connexion, $requete2);
					$nb = mysqli_num_rows($resultat2);
					if($nb==0){
						$affichage .= "<li><a href=\"". $ligne->lien_menu. "\" target=\"blank\">".$ligne->intitule_menu. "</a></li>\n";
					}elseif($nb==1){
						$pages = mysqli_fetch_object($resultat2);
						$href = "front.php?page=content&amp;id_page=". $pages->id_page;
						$affichage .= "<li><a href=\"". $href. "\">".$ligne->intitule_menu. "</a></li>\n";
					}else{
						// il faut calculer un sous-menu
						//alors il y a plusieurs pages associée à cet item de menu
						$affichage.="<li>";
						$affichage.="<label for=\"item" . $ligne->id_menu . "\">" . $ligne->intitule_menu . "</label>";    
						$affichage.="<input type=\"checkbox\" name=\"item\" id=\"item". $ligne->id_menu. "\" />";
						$affichage.="<ul>";
						while($pages = mysqli_fetch_object($resultat2)) {
							$affichage.="<li><a href=\"front.php?page=content&amp;id_page=" . $pages->id_page . "\">" . $pages->titre_page . "</a></li>";        
							}
						$affichage.="</ul>";
						$affichage.="</li>";
					}
				}
				$affichage .= "</ul>\n";
				$affichage .= "</nav>\n";
			break;

			case "menu_back":
				// on calcule les notifications de nouveaux messages
				$notification = "";
				$req = "SELECT lu FROM contacts WHERE lu=0";
				$res = mysqli_query($connexion, $req);
				$nb_msgs = mysqli_num_rows($res);
				if ($nb_msgs > 0) {
					$notification = " <span class=\"notif flex-center\">" . $nb_msgs . "</span>";
				}

				$affichage = "<nav id=\"menu_back\">\n<ul>\n";
				while($ligne = mysqli_fetch_object($resultat)){
					$query = parse_url($ligne->lien_menu, PHP_URL_QUERY);
					parse_str($query, $output);
					if(isset($output['module'])){
						$module = $output['module'];
					}else{
						$module = "";
					}
					if($module != "config"){
						if(isset($_GET['module']) && $_GET['module']==$module){
							$affichage .= "<li class=\"actif\">";
						}else{
							$affichage .= "<li>";
						}
						$affichage .= "<a href=\"". $ligne->lien_menu. "\">". $ligne->intitule_menu;
						if ($module=="messages" && $notification != ""){
							$affichage .= $notification;
						}
						$affichage .= "</a>\n";
						$affichage .= "</a>";
					}
				}
				$affichage .= "</ul>\n</nav>\n";
			break;
		}
		return $affichage;			
	}
}
	
//==============================================================
function afficher_droits($connexion, $requete) {

	$resultat=mysqli_query($connexion, $requete); 
	$affichage="<table class=\"tab_resultats\" id=\"tab_droits\">\n";
	//on calcule les entêtes des colonnes
	$affichage.="<tr>\n";
	$affichage.="<th class=\"large\">Module</th>\n";
	$affichage.="<th class=\"small\">Admin</th>\n";	
	$affichage.="<th class=\"small\">User</th>\n";
	$affichage.="</tr>\n";	
	while($ligne=mysqli_fetch_object($resultat)) {
		$affichage.="<tr>\n";
		$affichage.="<td>" . $ligne->intitule_menu . "</td>\n";		
		$affichage.="<td><a href=\"admin.php?module=droits&id_droit=" . $ligne->id_droit . "&statut=admin&valeur=" . $ligne->admin . "\"><img src=\"../images/" . $ligne->admin . ".png\" alt=\"\" /></a></td>";
		$affichage.="<td><a href=\"admin.php?module=droits&id_droit=" . $ligne->id_droit . "&statut=user&valeur=" . $ligne->user . "\"><img src=\"../images/" . $ligne->user . ".png\" alt=\"\" /></a></td>";		
		$affichage.="</tr>\n";	
	}
	$affichage.="</table>\n";	
	
	return $affichage;	
}

	
//=======================================
function afficher_sliders($connexion,$requete,$cas="back") {
	$resultat = mysqli_query($connexion, $requete);
	switch($cas) {
		case "back":
			$affichage="<table class=\"tab_resultats\" id=\"tab_sliders\">\n";
			//on calcule les entêtes des colonnes
			$affichage.="<tr>\n";                            
			$affichage.="<th class=\"small\">Tri</th>\n";    
			$affichage.="<th class=\"large\">Titre image</th>\n";    
			$affichage.="<th class=\"small\">Image</th>\n";        
			$affichage.="<th class=\"small\">Actions</th>\n";
			$affichage.="</tr>\n";
			while($ligne=mysqli_fetch_object($resultat)) {
				//on affiche le contenu de chaque uplet présent dans la table
				$affichage.="<tr>\n";
				$affichage.="<td>
					<a href=\"admin.php?module=slider&action=trier_slider&id_slider=". $ligne->id_slider. "&tri=up\"><span class=\"dashicons dashicons-arrow-up\"></span>
					&nbsp;
					<a href=\"admin.php?module=slider&action=trier_slider&id_slider=". $ligne->id_slider. "&tri=down\"><span class=\"dashicons dashicons-arrow-down\"></span>
				</td>\n";                
				$affichage.="<td><strong>" . $ligne->titre_slider . "</strong><br />". extrait($ligne->descriptif_slider, 5, 0). "</td>\n";                
				$affichage.="<td><a href=\"". str_replace("_s", "_b", $ligne->fichier_slider). "\" target=\"blank\"><img src=\"".$ligne->fichier_slider."\" alt=\"\" /></a></td>\n";
				$affichage.="<td>";        
				$affichage.="<a href=\"admin.php?module=slider&action=modifier_slider&id_slider=" . $ligne->id_slider . "\"><span class=\"dashicons dashicons-edit\"></span></a>";
				$affichage.="&nbsp;";
				$affichage.="<a href=\"admin.php?module=slider&action=supprimer_slider&id_slider=" . $ligne->id_slider . "\"><span class=\"dashicons dashicons-no-alt\"></span></a>";
				$affichage.="</td>\n";                        
				$affichage.="</tr>\n";                    
			}
			$affichage.="</table>\n";
			break;

			case "front":
				$affichage = "";
				while($ligne=mysqli_fetch_object($resultat)) {
					$affichage .= "<figure>\n";
					$affichage .= "<img src=\"". str_replace("_s", "_b", $ligne->fichier_slider). "\" alt=\"". $ligne->titre_slider. "\" />";
					$affichage .= "<figcaption class=\"caption\">\n";
					$affichage .= "<h1>". $ligne->titre_slider. "</h1>";
					$affichage .= "<p>". $ligne->descriptif_slider. "</p>";
					$affichage .= "</figcaption>\n";
					$affichage .= "</figure>\n";
				}
			break;
		}

    return $affichage;
}

// ===========================Date en clair
function maDate($date){
	$d = new DateTime($date);
	$fmt = new IntlDateFormatter( "fr-FR",
			IntlDateFormatter::MEDIUM,
			IntlDateFormatter::NONE,
			'Europe/Paris',
			IntlDateFormatter::GREGORIAN
	);
	return $fmt->format($d);
}

// ===========================Flux RSS
function generer_flux_rss($connexion, $requete){
	$resultat = mysqli_query($connexion, $requete);
	// cf. https://stackoverflow.com/questions/4503135/php-get-site-url-protocol-http-vs-https
	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
		$protocol = 'https://';
	}else {
		$protocol = 'http://';
	}
	$host = $_SERVER['HTTP_HOST'];
	$root = $protocol. $host. "/archi/";
	$logo_path = "images/logo.png";
	$logo_dimensions = getimagesize("../". $logo_path);

	//on calcule l'entete du flux RSS
    $flux_rss="<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
	$flux_rss.="<rss version=\"2.0\"\n";
	$flux_rss.="xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\"  xml:base=\"". $root. "\">\n";
    $flux_rss.="<channel>\n";
    $flux_rss.="<atom:link rel=\"self\" href=\"". $root. "feed/rss.xml\"/>\n";    
    $flux_rss.="<title>BETB Construction et rénovation</title>\n";    
    $flux_rss.="<link>". $root. "</link>\n";
    $flux_rss.="<description>Le petit journal hebdo de Archi</description>\n";
    $flux_rss.="<language>fr-FR</language>\n";
	$flux_rss.="<copyright>Copyright ". date("Y") ."</copyright>\n";
    $flux_rss.="<lastBuildDate>". date("r") . "</lastBuildDate>\n";    
	  
    $flux_rss.="<image>\n";
    $flux_rss.="<url>". $root. $logo_path. "</url>\n";
    $flux_rss.="<title>BETB Construction et rénovation</title>\n";
    $flux_rss.="<link>". $root. "</link>\n";
    $flux_rss.="<width>". $logo_dimensions[0]. "</width>\n"; 
    $flux_rss.="<height>". $logo_dimensions[1]. "</height>\n"; 
    $flux_rss.="</image>\n";
    
    $car_replace=array("<br>","<br />");  
    
    //on calcule chaque item du flux (1 item=1 article avec RSS=oui)
	$i=0;
	while($ligne=mysqli_fetch_object($resultat)) {
		$link = $root. "front/front.php?page=single&amp;id_article=". $ligne->id_article;

        $flux_rss.="\n<item>\n";
        $flux_rss.="<title><![CDATA[". $ligne->titre_article. "]]></title>\n";
        $flux_rss.="<link>". $link. "</link>\n";
		
		$contenu_flux=str_replace($car_replace,"\n",$ligne->contenu_article);
        $flux_rss.="<description><![CDATA[". str_replace("&","&amp;",strip_tags($contenu_flux)). "]]></description>\n";
		
		$date_flux=date("r", strtotime($ligne->date_article));
        $flux_rss.="<pubDate>". $date_flux. "</pubDate>\n";    
        $flux_rss.="<guid>". $link. "</guid>\n";
        if(!empty($ligne->fichier_article)) {
			$img = getimagesize($ligne->fichier_article);
            $size =filesize($ligne->fichier_article);
            $flux_rss.="<enclosure length=\"". $size. "\" url=\"". $root. str_replace("../", "", $ligne->fichier_article). "\"  type=\"". $img['mime']. "\" />\n";
        }
        $flux_rss.="</item>\n";    
        $i++;
    }
    
    $flux_rss.="</channel>\n";
    $flux_rss.="</rss>\n";
    return $flux_rss;    
}


// ===========================Afficher Pages
function afficher_pages($connexion,$requete,$cas) {
	$resultat=mysqli_query($connexion,$requete);
	
	if(isset($cas)){
		switch($cas) {
			case "back":
			$affichage="<table class=\"tab_resultats\" id=\"tab_pages\">\n";
			//on calcule les entêtes des colonnes
			$affichage.="<tr>\n";
			$affichage.="<th class=\"large\">Titre</th>\n";
			$affichage.="<th class=\"medium\">Date</th>\n";	
			$affichage.="<th class=\"small\">Image</th>\n";		
			$affichage.="<th class=\"small\">Actions</th>\n";
			$affichage.="</tr>\n";
			$i = 0;
			$tab_comptes = array();
			while($ligne=mysqli_fetch_object($resultat)) {
				// stocke l'auteur de la page précédente (pour détecter les changements)
				$tab_comptes[$i] = $ligne->id_compte;
				if($i==0 || ($i>0 && $tab_comptes[$i] != $tab_comptes[$i - 1])){
					$affichage.= "<tr><td colspan=\"4\" class=\"auteur\">";
					$affichage.= $ligne->prenom_compte. " ". $ligne->nom_compte;
					$affichage.= "</td></tr>\n";
				}
				$affichage.="<tr>\n";
				$affichage.="<td>". $ligne->titre_page . "</td>\n";
				$affichage.="<td>". maDate($ligne->date_page) . "</td>\n";
				$affichage.="<td class=\"miniature\">";
				if(empty($ligne->fichier_page)){
					$affichage.="<span class=\"dashicons dashicons-hidden\"></span></td>";
				}else{
					$affichage.="<figure>";

					$affichage.="<a href=\"". str_replace("_s", "_b", $ligne->fichier_page). "\" target=\"blank\">";
					$affichage.="<img class=\"miniature\" src=\"" . $ligne->fichier_page . "\" alt=\"\" />";
					$affichage.="</a>";

					$affichage.="<figcaption><a class=\"suppr_img\" href=\"admin.php?module=pages&action=supprimer_image&id_page=". $ligne->id_page ."\">
					<span class=\"dashicons dashicons-dismiss\"></span></a></figcaption>";		
				}
				$affichage.="</td>\n";
				$affichage.="<td>";
				$affichage.="<a href=\"admin.php?module=pages&action=modifier_page&id_page=" . $ligne->id_page . "\"><span class=\"dashicons dashicons-edit\"></span></a>";
				$affichage.="&nbsp;";
				$affichage.="<a href=\"admin.php?module=pages&action=supprimer_page&id_page=" . $ligne->id_page . "\"><span class=\"dashicons dashicons-trash\"></span></a>";
				$affichage.="</td>\n";						
				$affichage.="</tr>\n";
				$i++;					
			}
			$affichage.="</table>\n";
			break;


			case "front":
				$ligne=mysqli_fetch_object($resultat);
				$affichage= "<article id=\"page-". $ligne->id_page."\">";
				$affichage.= "<h1>". $ligne->titre_page. "</h1>";
				if (!empty($ligne->fichier_page)){
					$affichage.= "<figure>\n<img src=\"". str_replace("_s", "_b", $ligne->fichier_page). "\" alt=\"". $ligne->titre_page. "\"></figure>\n";
				}
				$affichage.= $ligne->contenu_page;
				$affichage.= "</article>";
			break;
		}		
	}

	return $affichage;
}
?>