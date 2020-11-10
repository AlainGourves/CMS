<?php

if (isset($_SESSION['id_compte'])) {
    $entete = "<h1>Gestion des articles</h1>";
    
    if(isset($_GET['action'])) {
        
        $message = array();
        $insertion = false;
        
        switch ($_GET['action']) {
            case 'afficher_articles':
                $action_form = "afficher_articles";
                if (isset($_POST['submit'])) {
                    if(!empty($_POST['flux_article'])){
                        // la case flux RSS a été cochée
                        $checked = " checked=\"checked\"";
                        $_POST['flux_article'] = 1;
                    }else{
                        // si la case n'est pas cochée, $_POST['flux_article'] n'est pas défini
                        $_POST['flux_article'] = 0;
                    }
                    if (empty($_POST['titre_article'])) {
                        $message['titre_article'] = "<label for=\"titre_article\" class=\"pas_ok\">Mets un titre, gros naze !</label>";
                    } elseif (!strlen(trim($_POST['contenu_article']))) {
                        $message['contenu_article'] = "<label for=\"contenu_article\" class=\"pas_ok\">L'article n'a pas de contenu !</label>";
                    } elseif (empty($_POST['date_article'])) {
                        $message['date_article'] = "<label for=\"date_article\" class=\"pas_ok\">Ajoute une date !</label>";
                    } else {
                        // Rang de l'article : on le place en premier
                        // => le rang des articles précédents est mis à jour
                        $requete = "UPDATE articles SET rang_article=(rang_article + 1)";
                        $resultat = mysqli_query($connexion, $requete);
                        
                        $rang_article = 1;
                        $id_compte = $_SESSION['id_compte'];
                        $titre_article = addslashes($_POST['titre_article']);
                        $contenu_article = addslashes($_POST['contenu_article']);
                        $date_article = addslashes($_POST['date_article']);

                        $requete = "INSERT INTO articles SET 
                                    id_compte='". $id_compte. "',
                                    titre_article='". $titre_article. "',
                                    contenu_article='". $contenu_article. "',
                                    date_article='". $date_article. "',
                                    rang_article='". $rang_article. "',
                                    flux_article='". $_POST['flux_article']. "'";
                                    $resultat = mysqli_query($connexion, $requete);
                        $dernier_id_cree = mysqli_insert_id($connexion);

                        // Cas où il y a une image :
                        // on teste si le fichier a le bon format
                        if(!empty($_FILES['fichier_article']['name'])){
                            if (fichier_type($_FILES['fichier_article']['name'])=='png' ||
                                fichier_type($_FILES['fichier_article']['name'])=='jpg' ||
                                fichier_type($_FILES['fichier_article']['name'])=='gif') {

                                                        
                                // _b: big, _s: small
                                $chemin_b = "../medias/article_b". $dernier_id_cree. ".". fichier_type($_FILES['fichier_article']['name']);
                                $chemin_s = "../medias/article_s". $dernier_id_cree. ".". fichier_type($_FILES['fichier_article']['name']);
                                                        
                                if (is_uploaded_file($_FILES['fichier_article']['tmp_name'])) {
                                    // tmp_name : fich temporaire généré sur le serveur
                                    if (copy($_FILES['fichier_article']['tmp_name'], $chemin_b)) {
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
                                        
                                        // On met à jour la table articles
                                        $requete = "UPDATE articles SET fichier_article='". $chemin_s."'  WHERE id_article='". $dernier_id_cree. "'";
                                        $resultat = mysqli_query($connexion, $requete);
                                    }
                                }
                            }else{
                                $message['fichier_slider'] = "<label for=\"fichier_slider\" class=\"pas_ok\">Seules les exentions png, svg, jpg et gif sont autorisées !</label>";
                            }
                        }
                        $insertion = true;
                        $message['resultat'] = "<p class=\"alerte ok\">Le nouvel article a bien été enregistré !</p>";
                    }
                }
                break;
            
            case 'modifier_article':
                $action_form = "modifier_article&id_article=". $_GET['id_article'];
                if (isset($_POST['submit'])) {
                    if(!empty($_POST['flux_article'])){
                        // la case flux RSS a été cochée
                        $_POST['flux_article'] = 1;
                    }else{
                        // si la case n'est pas cochée, $_POST['flux_article'] n'est pas défini
                        $_POST['flux_article'] = 0;
                    }
                    if (empty($_POST['titre_article'])) {
                        $message['titre_article'] = "<label for=\"titre_article\" class=\"pas_ok\">Mets un titre, gros naze !</label>";
                    } elseif (!strlen(trim($_POST['contenu_article']))) {
                        $message['contenu_article'] = "<label for=\"contenu_article\" class=\"pas_ok\">L'article n'a pas de contenu !</label>";
                    } elseif (empty($_POST['date_article'])) {
                        $message['date_article'] = "<label for=\"date_article\" class=\"pas_ok\">Ajoute une date !</label>";
                    } else {
                        $titre_article = addslashes($_POST['titre_article']);
                        $contenu_article = addslashes($_POST['contenu_article']);
                        $date_article = addslashes($_POST['date_article']);

                        $requete = "UPDATE articles SET 
                                    titre_article='". $titre_article. "',
                                    contenu_article='". $contenu_article. "',
                                    date_article='". $date_article. "',
                                    flux_article='". $_POST['flux_article']. "'
                                    WHERE id_article='". $_GET['id_article']. "'";
                        $resultat = mysqli_query($connexion, $requete);

                        // Cas où il y a une image :
                        // on teste si le fichier a le bon format
                        if(!empty($_FILES['fichier_article']['name'])){
                            if (fichier_type($_FILES['fichier_article']['name'])=='png' ||
                                fichier_type($_FILES['fichier_article']['name'])=='jpg' ||
                                fichier_type($_FILES['fichier_article']['name'])=='gif') {

                                                        
                                // _b: big, _s: small
                                $chemin_b = "../medias/article_b". $_GET['id_article']. ".". fichier_type($_FILES['fichier_article']['name']);
                                $chemin_s = "../medias/article_s". $_GET['id_article']. ".". fichier_type($_FILES['fichier_article']['name']);
                                                        
                                if (is_uploaded_file($_FILES['fichier_article']['tmp_name'])) {
                                    // tmp_name : fich temporaire généré sur le serveur
                                    if (copy($_FILES['fichier_article']['tmp_name'], $chemin_b)) {
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
                                        
                                        // On met à jour la table articles
                                        $requete = "UPDATE articles SET fichier_article='". $chemin_s."'  WHERE id_article='". $_GET['id_article']. "'";
                                        $resultat = mysqli_query($connexion, $requete);
                                    }
                                }
                            }else{
                                $message['fichier_slider'] = "<label for=\"fichier_slider\" class=\"pas_ok\">Seules les exentions png, svg, jpg et gif sont autorisées !</label>";
                            }
                        }
                        $insertion = true;
                        $message['resultat'] = "<p class=\"alerte ok\">L'article a bien été modifié !</p>";
                        $action_form = "afficher_articles";
                    }
                }
                if(isset($_GET['id_article'])) {
                    // on récupère les infos de id_article
                    $requete = "SELECT * FROM articles WHERE id_article='". $_GET['id_article']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    // il y a un seul résultat max (id_compte est une clé primaire)
                    $ligne = mysqli_fetch_object($resultat);
                    $_POST['titre_article'] = $ligne->titre_article;
                    $_POST['contenu_article'] = $ligne->contenu_article;
                    // traitement de la date pour passer du format mySQL à un format YYYY-MM-DD
                    $t = strtotime($ligne->date_article);
                    $_POST['date_article'] = date("Y-m-d", $t);
                    if ($ligne->flux_article == 1) {
                        $checked = " checked=\"checked\"";
                    }
                }
                break;
            
            case 'supprimer_article':
                if(isset($_GET['id_article'])) {
                    $entete = "<h1 class=\"alerte ouinon flex\">Vous-voulez vraiment supprimer cet article&nbsp;? 
                    <a href=\"admin.php?module=articles&action=supprimer_article&id_article=" . $_GET['id_article'] . "&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=articles&action=afficher_articles\">NON</a>
                    </h1>";
                    //si l'internaute a confirmé la suppression (bouton oui)
                    if (isset($_GET['confirm']) && $_GET['confirm'] == 1) {
                        // Cas où il y a une image enregistrée
                        // récupère le nom du fichier
                        $requete = "SELECT fichier_article,rang_article FROM articles WHERE id_article='".$_GET['id_article']."'";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne=mysqli_fetch_object($resultat);
                        $rang_a_supprimer = $ligne->rang_article;
                        if (!empty($ligne->fichier_article)){
                            $chemin_a_supprimer_s = $ligne->fichier_article;
                            $chemin_a_supprimer_b = str_replace("_s","_b",$ligne->fichier_article);
                            unlink($chemin_a_supprimer_b);
                            unlink($chemin_a_supprimer_s);
                        }
                        $requete = "DELETE FROM articles WHERE id_article='" . $_GET['id_article'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                        // Mise à jour des rangs
                        $requete = "UPDATE articles SET rang_article=(rang_article -1) WHERE rang_article>". $rang_a_supprimer;
                        $resultat = mysqli_query($connexion, $requete);
                        
                         $entete = "<h1 class=\"alerte ok\">Article supprimé</h1>";
                    }
                }
                break;
            
            case 'trier_article':
                $action_form = "afficher_articles";
                
                if (isset($_GET['id_article']) && isset($_GET['tri'])){

                    // on vérifie quel était le rang de l'article à trier
                    $requete = "SELECT id_article, rang_article FROM articles WHERE id_article='". $_GET['id_article']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object(($resultat));
                    $isUpdate = false;
                    switch($_GET['tri']){
                        case "up":
                            if ($ligne->rang_article > 1){
                                $isUpdate = true;
                                // calcule le nouveau rang du article
                                $nouveau_rang = $ligne->rang_article - 1;
                                // modifie le rang de la ligne qui avait déjà ce rang
                                $inversion_rang = $nouveau_rang + 1;
                            }
                        break;
                        
                        case "down":
                            // on compte le nbre de ligne de la table
                            $requete = "SELECT id_article FROM articles";
                            $resultat=mysqli_query($connexion, $requete);
                            $nb_lignes = mysqli_num_rows($resultat);
                            if($ligne->rang_article < $nb_lignes){
                                $isUpdate = true;
                                // calcule le nouveau rang du article
                                $nouveau_rang = $ligne->rang_article + 1;
                                // modifie le rang de la ligne qui avait déjà ce rang
                                $inversion_rang = $nouveau_rang - 1;
                            }
                        break;
                    }
                    if ($isUpdate){
                        $requete = "UPDATE articles SET rang_article='" . $inversion_rang . "' WHERE rang_article='" . $nouveau_rang . "'";
                        $resultat = mysqli_query($connexion, $requete);
                        // attribue le nouveau rang au article concerné
                        $requete="UPDATE articles SET rang_article='". $nouveau_rang. "' WHERE id_article='". $_GET['id_article'] . "'";
                        $resultat=mysqli_query($connexion, $requete);
                    }
                }
                break;

            case 'supprimer_image':
                if(isset($_GET['id_article'])) {
                    $entete="<h1 class=\"alerte ouinon flex\">Vous-voulez vraiment supprimer l'image&nbsp;? 
                    <a href=\"admin.php?module=articles&action=supprimer_image&id_article=".$_GET['id_article']. "&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=articles&action=afficher_articles\">NON</a>
                    </h1>";
                    //si l'internaute a confirmé la suppression (bouton oui)
                    if(isset($_GET['confirm']) && $_GET['confirm']==1) {
                        // Supression image
                        // récupère le nom du fichier
                        $requete = "SELECT fichier_article FROM articles WHERE id_article='".$_GET['id_article']."'";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne=mysqli_fetch_object($resultat);

                        $chemin_a_supprimer_s = $ligne->fichier_article;
                        $chemin_a_supprimer_b = str_replace("_s","_b",$ligne->fichier_article);
                        unlink($chemin_a_supprimer_b);
                        unlink($chemin_a_supprimer_s);
                        // MàJ table
                        $requete = "UPDATE articles SET fichier_article='' WHERE id_article='". $_GET['id_article']. "'";
                        $resultat = mysqli_query($connexion, $requete);
                        $entete = "<h1 class=\"alerte ok\">Image supprimée</h1>";
                        // réinitialise l'action du formulaire
                        $action_form = "afficher_articles";
                    }
                }
                break;
        }
        
        // on construit un tableau qui affiche tous les sliders
        $requete = "SELECT * FROM articles ORDER BY rang_article";
        $tab_resultats = afficher_articles($connexion,$requete,"back");  
    }
}else{
    header("Location:../index.php");
}
?>