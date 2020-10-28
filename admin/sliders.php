<?php

if (isset($_SESSION['id_compte'])) {
    $entete = "<h1>Gestion du slider</h1>";
    
    if(isset($_GET['action'])) {
        
        $message = array();
        $insertion = false;
        
        switch ($_GET['action']) {
            case 'afficher_slider':
                $action_form = "afficher_slider";
                if (isset($_POST['submit'])) {
                    if (empty($_POST['titre_slider'])) {
                        $message['titre_slider'] = "<label for=\"titre_slider\" class=\"pas_ok\">mets ton titre, chacal !</label>";
                    }elseif(empty($_FILES['fichier_slider']['name'])){
                        $message['fichier_slider'] = "<label for=\"fichier_slider\" class=\"pas_ok\">Va chercher un fichier, abruti !</label>";
                    }else{
                        // on teste si le fichier a le bon format
                        if (fichier_type($_FILES['fichier_slider']['name'])=='png' ||
                        fichier_type($_FILES['fichier_slider']['name'])=='jpg' ||
                        fichier_type($_FILES['fichier_slider']['name'])=='gif') {
                            
                            //calcule le rang à attribuer au nouveau slider
                            $requete = "SELECT id_slider FROM sliders";
                            $resultat = mysqli_query($connexion, $requete);
                            $nb = mysqli_num_rows($resultat);
                            $rang = $nb + 1;
                            // on insère dans la table
                            $requete = "INSERT INTO sliders SET rang_slider='". $rang ."', titre_slider='". addslashes($_POST['titre_slider']). "', descriptif_slider='". addslashes($_POST['descriptif_slider']). "'";
                            $resultat = mysqli_query($connexion, $requete);
                            $dernier_id_cree = mysqli_insert_id($connexion);
                            
                            // _b: big, _s: small
                            $chemin_b = "../medias/slider_b". $dernier_id_cree. ".". fichier_type($_FILES['fichier_slider']['name']);
                            $chemin_s = "../medias/slider_s". $dernier_id_cree. ".". fichier_type($_FILES['fichier_slider']['name']);
                            
                            if (is_uploaded_file($_FILES['fichier_slider']['tmp_name'])) {
                                // tmp_name : fich temporaire généré sur le serveur
                                if (copy($_FILES['fichier_slider']['tmp_name'], $chemin_b)) {
                                    // on calcule les dimensions de l'image originelle
                                    $size = @getimagesize($chemin_b); // @: empêche les messages d'erreur
                                    $largeur = $size[0];
                                    $hauteur = $size[1];
                                    $rapport = $largeur/$hauteur;
                                    // si $rapport > 1 => format paysage
                                    // si $rapport < 1 => format portrait
                                    // si $rapport = 1 => format carré
                                    
                                    // génère miniature en respectant aspect ratio
                                    $larg_thumbnail = 100;
                                    $quality = 80; // % compression jpeg
                                    // redimentionne et stocke le thumbnail
                                    redimage($chemin_b, $chemin_s, $larg_thumbnail, $larg_thumbnail/$rapport, $quality);
                                    
                                    // On met à jour la table sliders
                                    $requete = "UPDATE sliders 
                                    SET fichier_slider='". $chemin_s."' 
                                    WHERE id_slider='". $dernier_id_cree. "'";
                                    $resultat = mysqli_query($connexion, $requete);
                                    if ($resultat) {
                                        $insertion = true;
                                        $message['resultat'] = "<p class=\"alerte ok\">L'image est bien enregistrée</p>";
                                    }else{
                                        $message['resultat'] = "<p class=\"alerte pas_ok\">Hélas, il y a eu un problème !</p>";
                                    }
                                }
                            }
                        }else{
                            $message['fichier_slider'] = "<label for=\"fichier_slider\" class=\"pas_ok\">Seules les exentions png, svg, jpg et gif sont autorisées !</label>";
                        }
                        // 
                        
                    }
                }
            break;
            
            case 'modifier_slider':
                $action_form = "modifier_slider&id_slider=". $_GET['id_slider'];
                if (isset($_POST['submit'])) {
                    if (empty($_POST['titre_slider'])) {
                        $message['titre_slider'] = "<label for=\"titre_slider\" class=\"pas_ok\">mets ton titre, chacal !</label>";
                    }else{
                        $requete="UPDATE sliders 
                        SET titre_slider='".addslashes($_POST['titre_slider'])."',
                        descriptif_slider='".addslashes($_POST['descriptif_slider'])."' 
                        WHERE id_slider='".$_GET['id_slider']."'";	
                        $resultat=mysqli_query($connexion,$requete);
                                                
                        //si une nouvelle image a été choisie
                        if(!empty($_FILES['fichier_slider']['name'])) {
                            //on teste si le fichier a le bon format
                            if(fichier_type($_FILES['fichier_slider']['name'])=="png" ||
                                fichier_type($_FILES['fichier_slider']['name'])=="jpg" ||
                                fichier_type($_FILES['fichier_slider']['name'])=="gif") {

                                //on génère les 2 chemins des fichiers image : le big et le small
                                $chemin_b="../medias/slider_b" . $_GET['id_slider'] . "." . fichier_type($_FILES['fichier_slider']['name']);
                                $chemin_s="../medias/slider_s" . $_GET['id_slider'] . "." . fichier_type($_FILES['fichier_slider']['name']);						
                                if(is_uploaded_file($_FILES['fichier_slider']['tmp_name'])) {                                
                                    if(copy($_FILES['fichier_slider']['tmp_name'], $chemin_b)) {
                                        //On calcule les dimensions de l'image originelle
                                        $size=GetImageSize($chemin_b);
                                        $largeur=$size[0];
                                        $hauteur=$size[1];
                                        $rapport=$largeur/$hauteur;
                                        //si $rapport>1 alors image paysage
                                        //si $rapport<1 alors image portrait
                                        //si $rapport=1 alors image carrée
                                        
                                        //on genere une miniature en respectant l'homothétie
                                        $largeur_mini=60;
                                        $quality=80;
                                        redimage($chemin_b,$chemin_s,$largeur_mini,$largeur_mini/$rapport,$quality);
                                        //on met la jour la table sliders avec le chemin du fichier
                                        $requete="UPDATE sliders SET fichier_slider='" . $chemin_s . "' WHERE id_slider='".$_GET['id_compte']."'";
                                        $resultat=mysqli_query($connexion, $requete);					
                                    }									
                                }
                            }else{
                                $message="<label class=\"pas_ok\">Seules les extensions png, gif et jpg sont autorisées !</label>";	
                            }					
                        }
                        if ($resultat) {
                            $insertion = true;
                            $message['resultat'] = "<p class=\"alerte ok\">L'item du slider a été modifié.</p>";
                        }else{
                            $message['resultat'] = "<p class=\"alerte pas_ok\">Hélas, il y a eu un problème !</p>";
                        }
                        //on se replace sur l'action afficher_comptes
                        $action_form="afficher_sliders";
                        
                        //on suprime la variable $_GET['id_slider']
                        //afin de ne pas executer le if(isset($_GET['id_slider'])) qui suit
                        unset($_GET['id_slider']);
                    }
                }
                if(isset($_GET['id_slider'])) {
                    $action_form="modifier_slider&id_slider=" . $_GET['id_slider'];
                    
                    //on récupere dans la table les infos
                    $requete="SELECT * FROM sliders WHERE id_slider='".$_GET['id_slider']."'";
                    $resultat=mysqli_query($connexion,$requete);
                    $ligne=mysqli_fetch_object($resultat);
                    
                    //on recharge le formulaire avec les données stockées dans la table
                    $_POST['titre_slider']=$ligne->titre_slider;
                    $_POST['descriptif_slider']=$ligne->descriptif_slider;
                }
            break;
            
            case 'supprimer_slider':
                if(isset($_GET['id_slider'])) {
                    $entete = "<h1 class=\"alerte ouinon\">Vous-voulez vraiment supprimer cette image ? 
                    <a href=\"admin.php?module=slider&action=supprimer_slider&id_slider=" . $_GET['id_slider'] . "&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=slider&action=afficher_sliders\">NON</a>
                    </h1>";
                    //si l'internaute a confirmé la suppression (bouton oui)
                    if (isset($_GET['confirm']) && $_GET['confirm'] == 1) {
                        // Supression image
                        // récupère le nom du fichier
                        $requete = "SELECT fichier_slider,rang_slider FROM sliders WHERE id_slider='".$_GET['id_slider']."'";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne=mysqli_fetch_object($resultat);
                        
                        $rang_a_supprimer = $ligne->rang_slider;
                        $chemin_a_supprimer_s = $ligne->fichier_slider;
                        $chemin_a_supprimer_b = str_replace("_s","_b",$ligne->fichier_slider);
                        unlink($chemin_a_supprimer_b);
                        unlink($chemin_a_supprimer_s);
                        // MàJ table
                        $requete = "DELETE FROM sliders WHERE id_slider='" . $_GET['id_slider'] . "'";
                        $resultat = mysqli_query($connexion, $requete);

                        // Mise à jour des rangs
                        $requete = "UPDATE sliders SET rang_slider=(rang_slider -1) WHERE rang_slider>". $rang_a_supprimer;
                        $resultat = mysqli_query($connexion, $requete);
                        
                        $entete = "<h1 class=\"alerte ok\">Image supprimée</h1>";
                    }
                    $action_form="afficher_sliders";
                }
            break;
            
            case 'trier_slider':
                $action_form = "afficher_slider";
                
                if (isset($_GET['id_slider']) && isset($_GET['tri'])){
                    
                    // on vérifie quel était le rang du slider à trier
                    $requete = "SELECT id_slider, rang_slider FROM sliders WHERE id_slider='". $_GET['id_slider']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object(($resultat));
                    $isUpdate = false;
                    switch($_GET['tri']){
                        case "up":
                            if ($ligne->rang_slider > 1){
                                $isUpdate = true;
                                // calcule le nouveau rang du slider
                                $nouveau_rang = $ligne->rang_slider - 1;
                                // modifie le rang de la ligne qui avait déjà ce rang
                                $inversion_rang = $nouveau_rang + 1;
                            }
                        break;
                        
                        case "down":
                            // on compte le nbre de ligne de la table
                            $requete = "SELECT id_slider FROM sliders";
                            $resultat=mysqli_query($connexion, $requete);
                            $nb_lignes = mysqli_num_rows($resultat);
                            if($ligne->rang_slider < $nb_lignes){
                                $isUpdate = true;
                                // calcule le nouveau rang du slider
                                $nouveau_rang = $ligne->rang_slider + 1;
                                // modifie le rang de la ligne qui avait déjà ce rang
                                $inversion_rang = $nouveau_rang - 1;
                            }
                        break;
                    }
                    if ($isUpdate){
                        $requete = "UPDATE sliders SET rang_slider='" . $inversion_rang . "' WHERE rang_slider='" . $nouveau_rang . "'";
                        $resultat = mysqli_query($connexion, $requete);
                        // attribue le nouveau rang au slider concerné
                        $requete="UPDATE sliders SET rang_slider='". $nouveau_rang. "' WHERE id_slider='". $_GET['id_slider'] . "'";
                        $resultat=mysqli_query($connexion, $requete);
                    }
                }
            break;
        }
        
        // on construit un tableau qui affiche tous les sliders
        $requete = "SELECT * FROM sliders ORDER BY rang_slider";
        $tab_resultats = afficher_sliders($connexion,$requete);  
    }
}else{
    header("Location:../index.php");
}
?>