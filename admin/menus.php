<?php

if (isset($_SESSION['id_compte'])) {
    $entete = "<h1>Gestion des menus</h1>";

    if(isset($_GET['action'])) {
        
        $message = array();
        $insertion = false;
        
        $action_form = "afficher_menus";
        switch ($_GET['action']) {
            case 'afficher_menus':
                if (isset($_POST['submit'])) {
                    // gère la liste déroulante des menus
                    if(!empty($_POST['type_menu'])) {
                        $selected[$_POST['type_menu']] = " selected=\"selected\"";
                    }
                    if(empty($_POST['intitule_menu'])) {
                        $message['intitule_menu'] = "<label for=\"intitule_menu\" class=\"pas_ok\">Il faut donner un intitulé au menu !</label>";
                    }elseif(empty($_POST['lien_menu'])) {
                        $message['lien_menu'] = "<label for=\"lien_menu\" class=\"pas_ok\">Il faut donner un lien au menu !</label>";
                    }else if(empty($_POST['type_menu'])){
                        $message['type_menu'] = "<span class=\"pas_ok\">Il faut choisir un type de menu !</span>";
                    }else{
                        $type_menu = ($_POST['type_menu']=="front") ? "front" : "back";
                        // recherche le dernier rang
                        $requete = "SELECT rang_menu FROM menus 
                                        WHERE type_menu='". $type_menu. "'
                                        ORDER BY rang_menu DESC LIMIT 1";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne = mysqli_fetch_object($resultat);
                        $dernier_rang = $ligne->rang_menu + 1;
                        $requete = "INSERT INTO menus SET
                            intitule_menu='". addslashes($_POST['intitule_menu']). "',
                            lien_menu='". addslashes($_POST['lien_menu']). "',
                            type_menu='". $type_menu. "',
                            rang_menu='". $dernier_rang."'";
                        $resultat = mysqli_query($connexion, $requete);
                        $dernier_id_menu=mysqli_insert_id($connexion);
                        // Si c'est un menu pour le back, on l'ajoute à la table `droits`
                        // avec les valeurs par défaut
                        if ($type_menu == "back"){
                            $requete2="INSERT INTO droits SET id_menu='" . $dernier_id_menu . "'";
                            $resultat2=mysqli_query($connexion,$requete2);
                        }
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
                if (isset($_GET['id_menu']) && isset($_GET['tri'])){

                    // on vérifie quel était le rang du menu à trier
                    $requete = "SELECT id_menu, rang_menu, type_menu FROM menus WHERE id_menu='". $_GET['id_menu']. "'";
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
                            $requete = "SELECT id_menu FROM menus WHERE type_menu='". $ligne->type_menu. "'";
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
                        $requete = "UPDATE menus SET rang_menu='" . $inversion_rang . "' 
                                        WHERE rang_menu='" . $nouveau_rang . "'
                                        AND type_menu='". $ligne->type_menu. "'";
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
                    $type_menu = ($_POST['type_menu']=="front") ? "front" : "back";
                    $requete = "SELECT type_menu,rang_menu FROM menus WHERE id_menu='". $_GET['id_menu']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    $ancien_type_menu = $ligne->type_menu;
                    $ancien_rang_menu = $ligne->rang_menu;
                    $requete = "UPDATE menus SET 
                        intitule_menu='". addslashes($_POST['intitule_menu']). "',
                        lien_menu='". addslashes($_POST['lien_menu']). "',
                        type_menu='". $type_menu."'
                        WHERE id_menu='".  $_GET['id_menu']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    if ($type_menu != $ancien_type_menu){
                        // Mise à jour des rangs pour le cas où on passe d'un type_menu à l'autre
                        $requete = "UPDATE menus 
                                        SET rang_menu=(rang_menu + 1) 
                                        WHERE type_menu='". $type_menu. "' 
                                        AND rang_menu>=". $ancien_rang_menu. " AND id_menu!=". $_GET['id_menu'];
                        echo $requete . "<br>";
                        $resultat = mysqli_query($connexion, $requete);
                        // De l'autre côté (puisqu'on a créé un trou)
                        $type_menu = ($type_menu=="front") ? "back" : "front";
                        $requete = "UPDATE menus 
                                        SET rang_menu=(rang_menu - 1) 
                                        WHERE type_menu='". $type_menu. "' 
                                        AND rang_menu>". $ancien_rang_menu;
                        echo $requete . "<br>";
                        $resultat = mysqli_query($connexion, $requete);

                        // Si le nouveau type est "back", il faut créer une nouvelle entrée dans la table droits
                        if ($type_menu == "back"){
                            $requete2="INSERT INTO droits SET id_menu='" . $_GET['id_menu'] . "'";
                            $resultat2=mysqli_query($connexion,$requete2);
                        }
                    }
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
                    // pour la liste déroulante
                    $selected[$ligne->type_menu] = " selected=\"selected\"";
                }
            break;
            
            case 'supprimer_menu':
                if(isset($_GET['id_menu'])) {
                    $entete="<h1 class=\"alerte ouinon flex\">Vous-voulez vraiment supprimer ce menu&nbsp;? 
                    <a href=\"admin.php?module=menus&action=supprimer_menu&id_menu=".$_GET['id_menu']."&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=menus&action=afficher_menus\">NON</a>
                    </h1>";
                    if(isset($_GET['confirm']) && $_GET['confirm']==1) {
                        // récupère le rang du menu à supprimer
                        $requete = "SELECT rang_menu, type_menu FROM menus WHERE id_menu='".$_GET['id_menu']."'";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne=mysqli_fetch_object($resultat);
                        $rang_a_supprimer = $ligne->rang_menu;
                        $requete = "DELETE FROM menus WHERE id_menu='". $_GET['id_menu']."'";
                        $resultat = mysqli_query($connexion, $requete);
                        // Mise à jour des rangs
                        $requete = "UPDATE menus 
                                        SET rang_menu=(rang_menu -1)
                                        WHERE rang_menu>". $rang_a_supprimer
                                        ." AND type_menu='". $ligne->type_menu. "'";
                        $resultat = mysqli_query($connexion, $requete);
                        $action_form = "afficher_menus";
                        $entete = "<h1 class=\"alerte ok\">Menu supprimé</h1>";
                        // Si type_menu="back", il faut supprimer l'entrée correspondantes dans la table droits
                        if($ligne->type_menu == "back"){
                            $requete = "DELETE FROM droits WHERE id_menu=\"". $_GET['id_menu']. "\"";
                            $resultat = mysqli_query($connexion, $requete);
                        }
                    }
                }
            break;
        }
    }
    
    // Affichage menu "Front"
    $tab_resultats = "<div class=\"myMenu\">";
    $tab_resultats .= "<input id=\"menuFront\" type=\"checkbox\" checked/>";
    $tab_resultats .= "<div>";
    $requete = "SELECT * FROM menus WHERE type_menu='front' ORDER BY rang_menu ASC";
    $tab_resultats .= afficher_menus($connexion,$requete,"back");
    $tab_resultats .= "</div>";
    $tab_resultats .= "<label for=\"menuFront\"><h1>Menu Front</h1></label>";
    $tab_resultats .= "</div>";
    
    // Affichage menu "Back"
    $tab_resultats .= "<div class=\"myMenu\">";
    $tab_resultats .= "<input id=\"menuBack\" type=\"checkbox\"/ checked>";
    $tab_resultats .= "<div>";
    $requete = "SELECT * FROM menus WHERE type_menu='back' ORDER BY rang_menu ASC";
    $tab_resultats .= afficher_menus($connexion,$requete,"back");
    $tab_resultats .= "</div>";
    $tab_resultats .= "<label for=\"menuBack\"><h1>Menu Back</h1></label>";
    $tab_resultats .= "</div>";
    
}else{
    header("Location:../index.php");
}
?>