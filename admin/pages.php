<?php

if (isset($_SESSION['id_compte'])) {
    $entete = "<h1>Gestion des pages</h1>";
    
    if(isset($_GET['action'])) {
        $message = array();
        $insertion = false;
        
        // Liste des menus Front
        $requete = "SELECT * FROM menus WHERE type_menu='front' ORDER BY rang_menu ASC";
        $resultat = mysqli_query($connexion, $requete);
        $menus = array();
        $menu_selected = 0;
        while ($ligne = mysqli_fetch_object($resultat)) {
            $menus[$ligne->id_menu] = $ligne->intitule_menu;
            if(isset($_POST['id_menu']) && $ligne->id_menu == $_POST['id_menu']){
                $menu_selected = $ligne->id_menu;
            }
        }

        switch ($_GET['action']) {
            case 'afficher_pages':
                $action_form = "afficher_pages";


                if (isset($_POST['submit'])) {
                    if (empty($_POST['titre_page'])) {
                        $message['titre_page'] = "<label for=\"titre_page\" class=\"pas_ok\">Il faut un titre pour la page !</label>";
                    } elseif (empty($_POST['id_menu'])) {
                        $message['id_menu'] = "<span class=\"pas_ok\">Il faut associer la page à un menu !</span>";                        
                    } elseif (!strlen(trim($_POST['contenu_page']))) {
                        $message['contenu_page'] = "<label for=\"contenu_page\" class=\"pas_ok\">La page n'a pas de contenu !</label>";
                    } else {
                        $requete = "INSERT INTO pages SET 
                                        id_compte=\"". $_SESSION['id_compte']. "\",
                                        id_menu=\"". $_POST['id_menu']. "\",
                                        titre_page=\"". addslashes($_POST['titre_page']). "\",
                                        contenu_page=\"". addslashes($_POST['contenu_page']). "\",
                                        date_page=NOW()";
                        $resultat = mysqli_query($connexion, $requete);
                        $dernier_id_cree = mysqli_insert_id($connexion);

                        // Cas où il y a une image :
                        // on teste si le fichier a le bon format
                        if(!empty($_FILES['fichier_page']['name'])){
                            if (fichier_type($_FILES['fichier_page']['name'])=='png' || fichier_type($_FILES['fichier_page']['name'])=='jpg' || fichier_type($_FILES['fichier_page']['name'])=='gif') {
                                // _b: big, _s: small
                                $chemin_b = "../medias/page_b". $dernier_id_cree. ".". fichier_type($_FILES['fichier_page']['name']);
                                $chemin_s = "../medias/page_s". $dernier_id_cree. ".". fichier_type($_FILES['fichier_page']['name']);
                                
                                if (is_uploaded_file($_FILES['fichier_page']['tmp_name'])) {
                                    // tmp_name : fich temporaire généré sur le serveur
                                    if (copy($_FILES['fichier_page']['tmp_name'], $chemin_b)) {
                                        // on calcule les dimensions de l'image originelle
                                        $size = @getimagesize($chemin_b); // @: empêche les messages d'erreur
                                        $largeur = $size[0];
                                        $hauteur = $size[1];
                                        $rapport = $largeur/$hauteur;
                                        // génère miniature en respectant aspect ratio
                                        $larg_thumbnail = 100;
                                        $quality = 80; // % compression jpeg
                                        // redimentionne et stocke le thumbnail
                                        redimage($chemin_b, $chemin_s, $larg_thumbnail, $larg_thumbnail/$rapport, $quality);
                                        
                                        // On met à jour la table pages
                                        $requete = "UPDATE pages SET fichier_page='". $chemin_s."'  WHERE id_page='". $dernier_id_cree. "'";
                                        $resultat = mysqli_query($connexion, $requete);
                                    }
                                }
                            }else{
                                $message['fichier_slider'] = "<label for=\"fichier_slider\" class=\"pas_ok\">Seules les exentions png, svg, jpg et gif sont autorisées !</label>";
                            }
                        }
                        $insertion = true;
                        $menu_selected = 0; // après insertion, plus besoin de présélectionner
                        $message['resultat'] = "<p class=\"alerte ok\">La nouvelle page a bien été enregistrée !</p>";
                    }
                }
            break;

            case 'modifier_page':
                $action_form = "modifier_page&id_page=". $_GET['id_page'];
                if (isset($_POST['submit'])) {
                    if (empty($_POST['titre_page'])) {
                        $message['titre_page'] = "<label for=\"titre_page\" class=\"pas_ok\">Il faut un titre pour la page !</label>";
                    } elseif (empty($_POST['id_menu'])) {
                        $message['id_menu'] = "<span class=\"pas_ok\">Il faut associer la page à un menu !</span>";                        
                    } elseif (!strlen(trim($_POST['contenu_page']))) {
                        $message['contenu_page'] = "<label for=\"contenu_page\" class=\"pas_ok\">La page n'a pas de contenu !</label>";
                    } else {
                        $titre_page = addslashes($_POST['titre_page']);
                        $contenu_page = addslashes($_POST['contenu_page']);

                        $requete = "UPDATE pages SET 
                                    id_compte=\"". $_SESSION['id_compte']. "\",
                                    id_menu=\"". $_POST['id_menu']. "\",
                                    titre_page='". $titre_page. "',
                                    contenu_page='". $contenu_page. "',
                                    date_page=NOW() 
                                    WHERE id_page='". $_GET['id_page']. "'";
                        $resultat = mysqli_query($connexion, $requete);

                        // Cas où il y a une image :
                        // on teste si le fichier a le bon format
                        if(!empty($_FILES['fichier_page']['name'])){
                            if (fichier_type($_FILES['fichier_page']['name'])=='png' || fichier_type($_FILES['fichier_page']['name'])=='jpg' || fichier_type($_FILES['fichier_page']['name'])=='gif') {
                                // _b: big, _s: small
                                $chemin_b = "../medias/page_b". $_GET['id_page']. ".". fichier_type($_FILES['fichier_page']['name']);
                                $chemin_s = "../medias/page_s". $_GET['id_page']. ".". fichier_type($_FILES['fichier_page']['name']);
                                                        
                                if (is_uploaded_file($_FILES['fichier_page']['tmp_name'])) {
                                    // tmp_name : fich temporaire généré sur le serveur
                                    if (copy($_FILES['fichier_page']['tmp_name'], $chemin_b)) {
                                        // on calcule les dimensions de l'image originelle
                                        $size = @getimagesize($chemin_b); // @: empêche les messages d'erreur
                                        $largeur = $size[0];
                                        $hauteur = $size[1];
                                        $rapport = $largeur/$hauteur;
                                        // génère miniature en respectant aspect ratio
                                        $larg_thumbnail = 100;
                                        $quality = 80; // % compression jpeg
                                        // redimentionne et stocke le thumbnail
                                        redimage($chemin_b, $chemin_s, $larg_thumbnail, $larg_thumbnail/$rapport, $quality);
                                        
                                        // On met à jour la table pages
                                        $requete = "UPDATE pages SET fichier_page='". $chemin_s."'  WHERE id_page='". $_GET['id_page']. "'";
                                        $resultat = mysqli_query($connexion, $requete);
                                    }
                                }
                            }else{
                                $message['fichier_slider'] = "<label for=\"fichier_page\" class=\"pas_ok\">Seules les exentions png, svg, jpg et gif sont autorisées !</label>";
                            }
                        }
                        $insertion = true;
                        $menu_selected = 0;
                        $message['resultat'] = "<p class=\"alerte ok\">La page a bien été modifiée !</p>";
                        $action_form = "afficher_pages";
                    }
                }
                if(isset($_GET['id_page'])) {
                    // on récupère les infos de id_page
                    $requete = "SELECT * FROM pages WHERE id_page='". $_GET['id_page']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    // il y a un seul résultat max (id_compte est une clé primaire)
                    $ligne = mysqli_fetch_object($resultat);
                    $_POST['titre_page'] = $ligne->titre_page;
                    $_POST['contenu_page'] = $ligne->contenu_page;
                    if (!$insertion) $menu_selected = $ligne->id_menu;
                }
            break;

            case 'supprimer_page':
                if(isset($_GET['id_page'])) {
                    $entete = "<h1 class=\"alerte ouinon flex\">Vous-voulez vraiment supprimer cette page&nbsp;? 
                    <a href=\"admin.php?module=pages&action=supprimer_page&id_page=" . $_GET['id_page'] . "&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=pages&action=afficher_pages\">NON</a>
                    </h1>";
                    //si l'internaute a confirmé la suppression (bouton oui)
                    if (isset($_GET['confirm']) && $_GET['confirm'] == 1) {
                        // Cas où il y a une image enregistrée
                        // récupère le nom du fichier
                        $requete = "SELECT fichier_page FROM pages WHERE id_page='".$_GET['id_page']."'";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne=mysqli_fetch_object($resultat);
                        if (!empty($ligne->fichier_page)){
                            $chemin_a_supprimer_s = $ligne->fichier_page;
                            $chemin_a_supprimer_b = str_replace("_s","_b",$ligne->fichier_page);
                            unlink($chemin_a_supprimer_b);
                            unlink($chemin_a_supprimer_s);
                        }
                        $requete = "DELETE FROM pages WHERE id_page='" . $_GET['id_page'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                        $entete = "<h1 class=\"alerte ok\">page supprimée</h1>";
                    }
                }
            break;

            case 'supprimer_image':
                if(isset($_GET['id_page'])) {
                    $entete="<h1 class=\"alerte ouinon flex\">Vous-voulez vraiment supprimer l'image&nbsp;? 
                    <a href=\"admin.php?module=pages&action=supprimer_image&id_page=".$_GET['id_page']. "&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=pages&action=afficher_pages\">NON</a>
                    </h1>";
                    //si l'internaute a confirmé la suppression (bouton oui)
                    if(isset($_GET['confirm']) && $_GET['confirm']==1) {
                        // Supression image
                        // récupère le nom du fichier
                        $requete = "SELECT fichier_page FROM pages WHERE id_page='".$_GET['id_page']."'";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne=mysqli_fetch_object($resultat);

                        $chemin_a_supprimer_s = $ligne->fichier_page;
                        $chemin_a_supprimer_b = str_replace("_s","_b",$ligne->fichier_page);
                        unlink($chemin_a_supprimer_b);
                        unlink($chemin_a_supprimer_s);
                        // MàJ table
                        $requete = "UPDATE pages SET fichier_page='' WHERE id_page='". $_GET['id_page']. "'";
                        $resultat = mysqli_query($connexion, $requete);
                        $entete = "<h1 class=\"alerte ok\">Image supprimée</h1>";
                        // réinitialise l'action du formulaire
                        $action_form = "afficher_pages";
                    }
                }
            break;
        }

        // Calcul de l'affichage de la liste déroulante
        $liste_menus = "";
        foreach($menus as $id => $title){
            $liste_menus .= "<option value=\"". $id. "\"";
            if(isset($menu_selected) && $id == $menu_selected){
                $liste_menus .= " selected";
            }
            $liste_menus .= ">". $title."</option>\n";
        }

        // Calcul de l'afficahge
        $requete = "SELECT c.*, p.* FROM pages p LEFT JOIN comptes c
        ON p.id_compte=c.id_compte
        ORDER BY p.id_compte ASC, p.date_page DESC";
        $tab_resultats = afficher_pages($connexion,$requete,"back");  
    }
}else{
    header("Location:../index.php");
}
?>