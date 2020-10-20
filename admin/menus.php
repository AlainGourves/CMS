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
                    }elseif(empty($_POST['rang_menu']) || !is_int((int) $_POST['rang_menu'])) {
                        // Tester aussi que c'est un INT & qu'il est unique !
                        $message['rang_menu'] = "<label for=\"rang_menu\" class=\"pas_ok\">Il faut donner un rang au menu et ça doit être un entier !</label>";
                    }else{
                        // vérifie que le rang n'est pas déjà utilisé
                        $requete = "SELECT * FROM menus WHERE rang_menu='". $_POST['rang_menu']. "'";
                        $resultat = mysqli_query($connexion, $requete);
                        $nb = mysqli_num_rows($resultat);
                        if ($nb>0){
                            $message['rang_menu'] = "<label for=\"rang_menu\" class=\"pas_ok\">Il existe déjà un menu de rang ". $_POST['rang_menu']."</label>";
                        }else{
                            $requete = "INSERT INTO menus SET
                            intitule_menu='". addslashes($_POST['intitule_menu']). "',
                            lien_menu='". addslashes($_POST['lien_menu']). "',
                            rang_menu='". $_POST['rang_menu']."'";
                            echo $requete;
                            $resultat = mysqli_query($connexion, $requete);
                            if ($resultat) {
                                $insertion = true;
                                $message['resultat'] = "<p class=\"alerte ok\">Menu ajouté !</p>";
                            }else{
                                $message['resultat'] = "<p class=\"alerte pas_ok\">Une couille git dans le potage !</p>";
                            }
                        }
                    }
                }
            break;
            
            case 'modifier_menu':
                $action_form = "modifier_menu&id_menu=". $_GET['id_menu'];
                if (isset($_POST['submit'])) {
                    $requete = "UPDATE menus SET 
                    intitule_menu='". addslashes($_POST['intitule_menu']). "',
                    lien_menu='". addslashes($_POST['lien_menu']). "',
                    rang_menu='". addslashes($_POST['rang_menu']). "'
                    WHERE id_menu='".  $_GET['id_menu']. "'";
                    
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
                    $_POST['rang_menu'] = $ligne->rang_menu;
                }
            break;
            
            case 'supprimer_menu':
                if(isset($_GET['id_menu'])) {
                    $entete="<h1 class=\"alerte ouinon\">Vous-voulez vraiment supprimer ce menu&nbsp;? 
                    <a href=\"admin.php?module=menus&action=supprimer_menu&id_menu=".$_GET['id_menu']."&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=menus&action=afficher_menus\">NON</a>
                    </h1>";
                    if(isset($_GET['confirm']) && $_GET['confirm']==1) {
                        $requete = "DELETE FROM menus WHERE id_menu='". $_GET['id_menu']."'";
                        $resultat = mysqli_query($connexion, $requete);

                        // Redirection
                        $url=$_SERVER['PHP_SELF']. "?module=menus&action=afficher_menus";
                        header("Location: $url");
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