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
    }

}else{
    header("Location:../index.php");
}
?>