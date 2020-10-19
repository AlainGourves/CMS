<?php
if (isset($_SESSION['id_compte'])) {
    if(isset($_GET['action'])) {

        switch ($_GET['action']) {
            case 'afficher_message':
                $entete = "<h1>Messagerie</h1>";
                unset($_SESSION['id_contact']);
                break;

            case 'marquer_message':
                // marquer le message comme lu
                $entete = "<h1>Messagerie</h1>";
                if (isset($_GET['id_contact'])) {
                    $requete = "UPDATE contacts SET lu='1' WHERE id_contact='" . $_GET['id_contact'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    // mémorise en variable de session la val de 'id_contact'
                    $_SESSION['id_contact'] = $_GET['id_contact'];
                }
                break;

            case 'supprimer_message':
                if (isset($_GET['id_contact'])) {
                    $entete = "<h1 class=\"alerte ouinon\">Voulez-vous supprimer le message ?
                                <a href=\"admin.php?module=messages&action=supprimer_message&id_contact=" . $_GET['id_contact'] . "&confirm=1\" class=\"btn btn-oui\">OUI</a>
                                <a href=\"admin.php?module=messages&action=afficher_message\" class=\"btn btn-non\">NON</a>
                            </h1>";
                    if (isset($_GET['confirm']) && $_GET['confirm'] == 1) {
                        // s'il y a eu confirmation de la suppression (bouton OUI)
                        $requete = "DELETE FROM contacts WHERE id_contact='" . $_GET['id_contact'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                        $entete = "<h1 class=\"ok\">Message supprimé</h1>";
                    }
                }
                break;
        }


        $requete = "SELECT * FROM contacts ORDER BY date_contact DESC";
        $tab_resultats = afficher_contacts($connexion,$requete);
        // $resultat = mysqli_query($connexion, $requete);
        // // on construit un tableau qui affiche tous les messages reçus depuis le front
        // $tab_resultats = "\n<table class=\"tab_resultats\">\n";

        // // compteur
        // $i = 1;
        // // tant qu'il y a des lignes dans $resultat, on exploite chaque ligne comme objet
        // while ($ligne = mysqli_fetch_object($resultat)) {
        //     // Si le message n'a pas été lu
        //     if ($ligne->lu == 0) {
        //         $class = "non_lu";
        //     } else {
        //         $class = "lu";
        //     }
        //     if (isset($_SESSION['id_contact']) && $ligne->id_contact == $_SESSION['id_contact']) {
        //         $open = " open";
        //     } else {
        //         $open = "";
        //     }

        //     $tab_resultats .= "<tr>\n";
        //     $tab_resultats .= "\t<td class=\"" . $class . $open . "\">\n<a href=\"admin.php?module=messages&action=marquer_message&id_contact=" . $ligne->id_contact . "\">";
        //     if (!empty($ligne->prenom_contact)) {
        //         $tab_resultats .= $ligne->prenom_contact . " ";
        //     }
        //     $tab_resultats .= $ligne->nom_contact;
        //     $tab_resultats .= "</a></td>\n";
        //     $tab_resultats .= "\t<td>\n" . $ligne->date_contact . "</td>\n";

        //     $tab_resultats .= "\t<td>\n";
        //     $tab_resultats .= "<a href=\"admin.php?module=messages&action=supprimer_message&id_contact=" . $ligne->id_contact . "\"><span class=\"dashicons dashicons-no-alt\"></span></a>";
        //     $tab_resultats .= "</td>\n</tr>\n";

        //     // 2e ligne visible si clic
        //     $tab_resultats .= "<tr>\n";
        //     $tab_resultats .= "\t<td class=\"" . $open . "\" colspan=\"3\"";
        //     $tab_resultats .= ">\n<strong>Expéditeur</strong>: ";
        //     $tab_resultats .= $ligne->mel_contact . "<br><strong>Message</strong>: ";
        //     $tab_resultats .= $ligne->message_contact;
        //     $tab_resultats .= "</td>\n";
        //     $tab_resultats .= "</tr>\n";

        //     $i++;
        // }
        // $tab_resultats .= "</table>\n";
    }

}else{
    header("Location:../index.php");
}
?>