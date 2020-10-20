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
                            // on insère dans la table
                            $requete = "INSERT INTO sliders 
                                    SET titre_slider='". addslashes($_POST['titre_slider']). "',
                                    descriptif_slider='". addslashes($_POST['descriptif_slider']). "'";
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

                }
                if(isset($_GET['id_slider'])) {

                }
            break;

            case 'supprimer_slider':
                if(isset($_GET['id_slider'])) {
                    $entete = "<h1 class=\"alerte ouinon\">Vous-voulez vraiment supprimer cette image ? 
                    <a href=\"admin.php?module=sliders&action=supprimer_slider&id_slider=" . $_GET['id_slider'] . "&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=sliders&action=afficher_sliders\">NON</a>
                    </h1>";
                    //si l'internaute a confirmé la suppression (bouton oui)
                    if (isset($_GET['confirm']) && $_GET['confirm'] == 1) {
                        $requete = "DELETE FROM sliders WHERE id_slider='" . $_GET['id_slider'] . "'";
                        $resultat = mysqli_query($connexion, $requete2);
                        $entete = "<h1 class=\"alerte ok\">Image supprimée</h1>";
                    }
                }
            break;
        }

        // on construit un tableau qui affiche tous les sliders
        $requete = "SELECT * FROM sliders ORDER BY id_slider";
        $tab_resultats = afficher_sliders($connexion,$requete);  
    }
}else{
    header("Location:../index.php");
}
?>