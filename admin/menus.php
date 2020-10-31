<?php

if (isset($_SESSION['id_compte'])) {
    $entete = "<h1>Gestion des menus</h1>";

    
    if(isset($_GET['action'])) {
        
        $message = array();
        $insertion = false;
        
        switch ($_GET['action']) {
            case 'afficher_menus':
                $action_form = "afficher_menus";
                if (isset($_POST['submit'])) {
                    if(empty($_POST['intitule_menu'])) {
                        $message['intitule_menu'] = "<label for=\"intitule_menu\" class=\"pas_ok\">Il faut donner un intitulé au menu !</label>";
                    }elseif(empty($_POST['lien_menu'])) {
                        $message['lien_menu'] = "<label for=\"lien_menu\" class=\"pas_ok\">Il faut donner un lien au menu !</label>";
                    }else{
                        // recherche le dernier rang
                        $requete = "SELECT rang_menu FROM menus ORDER BY rang_menu DESC LIMIT 1";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne = mysqli_fetch_object($resultat);
                        $dernier_rang = $ligne->rang_menu + 1;
                        $requete = "INSERT INTO menus SET
                            intitule_menu='". addslashes($_POST['intitule_menu']). "',
                            lien_menu='". addslashes($_POST['lien_menu']). "',
                            rang_menu='". $dernier_rang."'";
                        $resultat = mysqli_query($connexion, $requete);
                        if ($resultat) {
                            $insertion = true;
                            $message['resultat'] = "<p class=\"alerte ok\">Menu ajouté !</p>";
                        }else{
                            $message['resultat'] = "<p class=\"alerte pas_ok\">Une couille git dans le potage !</p>";
                        }
                    }
                }
            break;
            
            case 'trier_menu':
                $action_form = "afficher_menus";
                if (isset($_GET['id_menu']) && isset($_GET['tri'])){

                    // on vérifie quel était le rang du menu à trier
                    $requete = "SELECT id_menu, rang_menu FROM menus WHERE id_menu='". $_GET['id_menu']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object(($resultat));
                    $isUpdate = false;
                    switch($_GET['tri']){
                        case "up":
                            if ($ligne->rang_menu > 1){
                                $isUpdate = true;
                                // calcule le nouveau rang du menu
                                $nouveau_rang = $ligne->rang_menu - 1;
                                // modifie le rang de la ligne qui avait déjà ce rang
                                $inversion_rang = $ligne->rang_menu;
                            }
                        break;
                        
                        case "down":
                            // on compte le nbre de lignes de la table
                            $requete = "SELECT id_menu FROM menus";
                            $resultat=mysqli_query($connexion, $requete);
                            $nb_lignes = mysqli_num_rows($resultat);
                            if($ligne->rang_menu < $nb_lignes){
                                $isUpdate = true;
                                // calcule le nouveau rang du menu
                                $nouveau_rang = $ligne->rang_menu + 1;
                                // modifie le rang de la ligne qui avait déjà ce rang
                                $inversion_rang = $ligne->rang_menu;
                            }
                        break;
                    }
                    if ($isUpdate){
                        $requete = "UPDATE menus SET rang_menu='" . $inversion_rang . "' WHERE rang_menu='" . $nouveau_rang . "'";
                        $resultat = mysqli_query($connexion, $requete);
                        // attribue le nouveau rang au menu concerné
                        $requete="UPDATE menus SET rang_menu='". $nouveau_rang. "' WHERE id_menu='". $_GET['id_menu'] . "'";
                        $resultat=mysqli_query($connexion, $requete);
                    }
                }
            break;

            case 'modifier_menu':
                $action_form = "modifier_menu&id_menu=". $_GET['id_menu'];
                if (isset($_POST['submit'])) {
                    $requete = "UPDATE menus SET 
                    intitule_menu='". addslashes($_POST['intitule_menu']). "',
                    lien_menu='". addslashes($_POST['lien_menu']). "' WHERE id_menu='".  $_GET['id_menu']. "'";
                    
                    $resultat = mysqli_query($connexion, $requete);
                    if ($resultat) {
                        $insertion = true;
                        $message['resultat'] = "<p class=\"alerte ok\">Le menu a bien été modifié.</p>";
                        $action_form = "afficher_menus";
                        //on suprime la variable $_GET['id_menu']
				        //afin de ne pas executer le if(isset($_GET['id_menu'])) qui suit
			        	unset($_GET['id_menu']);
                    }else{
                        $message['resultat'] = "<p class=\"alerte pas_ok\">Hélas, il y a eu un problème !</p>";
                    }
                }
                if(isset($_GET['id_menu'])) {
                    // récupère les infos sur le menu
                    $requete = "SELECT * FROM menus WHERE id_menu='". $_GET['id_menu']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    $_POST['intitule_menu'] = $ligne->intitule_menu;
                    $_POST['lien_menu'] = $ligne->lien_menu;
                }
            break;
            
            case 'supprimer_menu':
                if(isset($_GET['id_menu'])) {
                    $entete="<h1 class=\"alerte ouinon\">Vous-voulez vraiment supprimer ce menu&nbsp;? 
                    <a href=\"admin.php?module=menus&action=supprimer_menu&id_menu=".$_GET['id_menu']."&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=menus&action=afficher_menus\">NON</a>
                    </h1>";
                    if(isset($_GET['confirm']) && $_GET['confirm']==1) {
                        // récupère le rang du menu à supprimer
                        $requete = "SELECT rang_menu FROM menus WHERE id_menu='".$_GET['id_menu']."'";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne=mysqli_fetch_object($resultat);
                        $rang_a_supprimer = $ligne->rang_menu;
                        $requete = "DELETE FROM menus WHERE id_menu='". $_GET['id_menu']."'";
                        $resultat = mysqli_query($connexion, $requete);
                        // Mise à jour des rangs
                        $requete = "UPDATE menus SET rang_menu=(rang_menu -1) WHERE rang_menu>". $rang_a_supprimer;
                        $resultat = mysqli_query($connexion, $requete);
                        $action_form = "afficher_menus";
                        $entete = "<h1 class=\"alerte ok\">Menu supprimé</h1>";
                    }
                }
            break;
        }
    }
    
    $requete = "SELECT * FROM menus ORDER BY rang_menu ASC";
    $tab_resultats=afficher_menus($connexion,$requete);
    
}else{
    header("Location:../index.php");
}
?>