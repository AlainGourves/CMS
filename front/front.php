<?php
session_start();
// je connecte la librairie de fonctions PHP
require_once("../outils/fonctions.php");
// Je stocke dans une var le resultat de la fonction connexion()
$connexion = connexion();

// calcule un btn de retour vers le back, si un utlisateur est connecté
if(isset($_SESSION['id_compte'])) {
    $retour_back = "<div id=\"back\"><a href=\"../admin/admin.php\">Retour back</a></div>";
}

$contact = "form_contact.html";

if (isset($_POST['submit'])) {
    // déclare le tableau associatif
    $message = array();
    // test des champs obligatoires
    if (empty($_POST['nom_contact'])) {
        $message['nom_contact'] = "<label for=\"nom_contact\" class=\"pas_ok\">Mets ton nom, gros naze !</label>";
    }

    if (empty($_POST['mel_contact'])) {
        $message['mel_contact'] = "<label for=\"mel_contact\" class=\"pas_ok\">Mets ton email, idiot !</label>";
    }

    if (empty($_POST['message_contact'])) {
        $message['message_contact'] = "<label for=\"message_contact\" class=\"pas_ok\">Mets ton message, tête de n&oelig;ud !</label>";
    }

    // Si tout est bien rempli
    if(!empty($_POST['nom_contact']) && !empty($_POST['mel_contact']) && !empty($_POST['message_contact'])) {
        // requête d'insertion dans bdd
        $requete = "INSERT INTO contacts 
                        SET nom_contact='". $_POST['nom_contact']. "',
                            prenom_contact='". $_POST['prenom_contact']. "',
                            mel_contact='". $_POST['mel_contact']. "',
                            message_contact='". $_POST['message_contact']. "',
                            date_contact='". date("Y-m-d H:i:s"). "'";
        $result = mysqli_query($connexion, $requete);

        $contact = "merci.html";
    }

}

// on referme la connexion à la bdd
$connexion = mysqli_close($connexion);

include("front.html");
