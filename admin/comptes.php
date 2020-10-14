<?php


if (isset($_SESSION['id_compte'])) {
    if(isset($_GET['action'])) {

        $message = array();
        $insertion = false;

        switch ($_GET['action']) {
            case 'ajouter_compte':
                $entete = "<h1>Ajouter un compte</h1>";
                if (isset($_POST['submit'])) {
                    if(!empty($_POST['statut_compte'])) {
                        $selected[$_POST['statut_compte']] = "selected=\"selected\"";
                    }else{
                        $message['statut_compte'] = "<span class=\"pas_ok\">Choisis un statut, gros naze !</span>";
                    }
                    if(empty($_POST['nom_compte'])) {
                        $message['nom_compte'] = "<label for=\"nom_compte\" class=\"pas_ok\">Mets ton nom, gros naze !</span>";
                    }elseif(empty($_POST['prenom_compte'])) {
                        $message['prenom_compte'] = "<label for=\"prenom_compte\" class=\"pas_ok\">Mets ton prénom, gros naze !</label>";
                    }elseif(empty($_POST['login_compte'])) {
                        $message['login_compte'] = "<label for=\"login_compte\" class=\"pas_ok\">Mets un identifiant, gros naze !</label>";
                    }elseif(empty($_POST['pass_compte'])) {
                        $message['pass_compte'] = "<label for=\"pass_compte\" class=\"pas_ok\">Mets un mot de passe, gros naze !</label>";
                    }else{
                        $nom_compte = utf8_decode(addslashes($_POST['nom_compte']));
                        $prenom_compte = utf8_decode(addslashes($_POST['prenom_compte']));
                        $statut_compte = $_POST['statut_compte'];
                        $login_compte = $_POST['login_compte'];
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
                            $message['resultat'] = "<p class=\"ok\">Bravo, ". $prenom_compte. ", te voilà inséré dans la base !</p>";
                        }else{
                            $message['resultat'] = "<p class=\"pas_ok\">Hélas, ". $prenom_compte. ", une couille git dans le potage !</p>";
                        }
                    }
                }
                break;
            case 'afficher_comptes':
                $entete = "<h1>Gestion des comptes</h1>";
                break;

            case 'supprimer_compte':

                // on vérifie que ça n'est pas le dernier admin
                $requete =  "SELECT * FROM comptes WHERE statut_compte='admin'";
                $result = mysqli_query($connexion, $requete);
                $nb = mysqli_num_rows($result);
                if ($nb>1 ) {
                    $requete2 = "DELETE FROM comptes WHERE id_compte='". $_GET['id_compte']. "'";
                    $result2 = mysqli_query($connexion, $requete2);
                    $entete = "<h1 class=\"ok\">Compte supprimé</h1>";
                }else{
                    $entete = "<h1 class=\"pas_ok\">Impossible ! Il faut au moins un compte admin.</h1>";
                }
                break;
        }

        // calculer l'affichage de la liste des comptes
    }
        // on construit un tableau qui affiche tous les comptes
        $tab_resultats = "\n<table class=\"tab_resultats\">\n";
        $tab_resultats .= "<tr>\n<th>Identité</th>\n<th>Login</th>\n<th>Satut</th>\n<th>Actions</th>\n</tr>\n";
        
        $requete = "SELECT * FROM comptes ORDER BY id_compte";
        $resultat = mysqli_query($connexion, $requete);
        while ($ligne = mysqli_fetch_object($resultat)) {
            $tab_resultats .= "<tr>\n";
            $tab_resultats .= "\t<td>". utf8_encode($ligne->prenom_compte). " ". utf8_encode($ligne->nom_compte) ."</td>\n";
            $tab_resultats .= "\t<td>". $ligne->login_compte ."</td>\n";
            $tab_resultats .= "\t<td>". $ligne->statut_compte ."</td>\n";
            $tab_resultats .= "\t<td>X</td>\n";            
            $tab_resultats .= "</tr>\n";
        }
        $tab_resultats .= "</table>\n";
}else{
    header("Location:../index.php");
}
?>