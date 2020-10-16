<?php


if (isset($_SESSION['id_compte'])) {
    if(isset($_GET['action'])) {

        $message = array();
        $insertion = false;

        switch ($_GET['action']) {
            case 'afficher_comptes':
                $entete = "<h1>Gestion des comptes</h1>";
                $action_form = "afficher_comptes";
                if (isset($_POST['submit'])) {
                    // gère la liste déroulante des statuts
                    if(!empty($_POST['statut_compte'])) {
                        $selected[$_POST['statut_compte']] = " selected=\"selected\"";
                    }
                    if(empty($_POST['nom_compte'])) {
                        $message['nom_compte'] = "<label for=\"nom_compte\" class=\"pas_ok\">Mets ton nom, gros naze !</label>";
                    }elseif(empty($_POST['prenom_compte'])) {
                        $message['prenom_compte'] = "<label for=\"prenom_compte\" class=\"pas_ok\">Mets ton prénom, gros naze !</label>";
                    }elseif(empty($_POST['statut_compte'])) {
                        $message['statut_compte'] = "<span class=\"pas_ok\">Choisis un statut, gros naze !</span>";
                    }elseif(empty($_POST['login_compte'])) {
                        $message['login_compte'] = "<label for=\"login_compte\" class=\"pas_ok\">Mets un identifiant, gros naze !</label>";
                    }elseif(empty($_POST['pass_compte'])) {
                        $message['pass_compte'] = "<label for=\"pass_compte\" class=\"pas_ok\">Mets un mot de passe, gros naze !</label>";
                    }else{
                        $nom_compte = addslashes($_POST['nom_compte']);
                        $prenom_compte = addslashes($_POST['prenom_compte']);
                        $statut_compte = addslashes($_POST['statut_compte']);
                        $login_compte = addslashes($_POST['login_compte']);
                        $pass_compte = $_POST['pass_compte'];
                        $requete = "INSERT INTO comptes 
                                        SET nom_compte='". $nom_compte. "',
                                        prenom_compte='". $prenom_compte. "',
                                        login_compte='". $login_compte. "',
                                        pass_compte=SHA1('". $pass_compte. "'),
                                        statut_compte='". $statut_compte. "'";
                        $result = mysqli_query($connexion, $requete);
                        if ($result) {
                            $insertion = true;
                            $message['resultat'] = "<p class=\"alerte ok\">Bravo, ". $prenom_compte. ", te voilà inséré dans la base !</p>";
                        }else{
                            $message['resultat'] = "<p class=\"alerte pas_ok\">Hélas, ". $prenom_compte. ", une couille git dans le potage !</p>";
                        }
                    }
                }
                break;

            case 'modifier_compte':
                $action_form = "modifier_compte&id_compte=". $_GET['id_compte'];
                if (isset($_POST['submit'])) {
                    $requete = "UPDATE comptes SET 
                            nom_compte='". addslashes($_POST['nom_compte']). "',
                            prenom_compte='". addslashes($_POST['prenom_compte']). "',
                            statut_compte='". addslashes($_POST['statut_compte']). "',
                            login_compte='". addslashes($_POST['login_compte']). "'";
                    if (!empty($_POST['pass_compte'])) {
                        $requete .= ", pass_compte = SHA1('". $_POST['pass_compte']. "') ";
                    }
                    $requete .= "WHERE id_compte = '". $_GET['id_compte']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    if ($resultat) {
                        $insertion = true;
                        $message['resultat'] = "<p class=\"alerte ok\">Le compte a bien été modifié.</p>";
                        $action_form = "afficher_comptes";
                    }else{
                        $message['resultat'] = "<p class=\"alerte pas_ok\">Hélas, il y a eu un problème !</p>";
                    }
                }
                if(isset($_GET['id_compte'])) {
                    // on récupère les infos de id_compte
                    $requete = "SELECT * FROM comptes WHERE id_compte='". $_GET['id_compte']. "'";
                    $resultat = mysqli_query($connexion, $requete);
                    // il y a un seul résultat max (id_compte est une clé primaire)
                    $ligne = mysqli_fetch_object($resultat);
                    $_POST['nom_compte'] = $ligne->nom_compte;
                    $_POST['prenom_compte'] = $ligne->prenom_compte;
                    $_POST['login_compte'] = $ligne->login_compte;
                    // pour la liste déroulante
                    $selected[$ligne->statut_compte] = " selected=\"selected\"";
                }
                break;

            case 'supprimer_compte':
                if(isset($_GET['id_compte'])) {
                    $entete="<h1 class=\"alerte ouinon\">Vous-voulez vraiment supprimer ce compte ? 
                    <a href=\"admin.php?module=comptes&action=supprimer_compte&statut_compte=".$_GET['statut_compte']."&id_compte=".$_GET['id_compte']."&confirm=1\">OUI</a>
                    <a href=\"admin.php?module=comptes&action=afficher_comptes\">NON</a>
                    </h1>";
                    //si l'internaute a confirmé la suppression (bouton oui)
                    if(isset($_GET['confirm']) && $_GET['confirm']==1) {
                        // on vérifie que ça n'est pas le dernier admin
                        $requete =  "SELECT * FROM comptes WHERE statut_compte='admin'";
                        $result = mysqli_query($connexion, $requete);
                        $nb = mysqli_num_rows($result);
                        if ($nb==1  && $_GET['statut_compte']=="admin") {
                            $entete="<h1 class=\"alerte pas_ok\">Impossible ! Il faut au moins un compte admin</h1>";
                        }else{
                            $requete2 = "DELETE FROM comptes WHERE id_compte='". $_GET['id_compte']. "'";
                            $result2 = mysqli_query($connexion, $requete2);
                            $entete = "<h1 class=\"alerte ok\">Compte supprimé</h1>";
                        }
                    }
                }
                break;
        }

        // on construit un tableau qui affiche tous les comptes
        $requete = "SELECT * FROM comptes ORDER BY id_compte";
        $resultat = mysqli_query($connexion, $requete);
        $tab_resultats = afficher_comptes($connexion,$requete);
    }
}else{
    header("Location:../index.php");
}
?>