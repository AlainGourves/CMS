<?php

if(isset($_SESSION['id_compte']) && $_SESSION['statut_compte']=="admin") {
    $entete = "<h1>Gestion des droits</h1>";

    if(isset($_GET['id_droit']) && isset($_GET['statut'])){
        $nouvelle_valeur = ($_GET['valeur']=='oui') ? 'non' : 'oui';
        $requete = "UPDATE droits SET ". $_GET['statut']. "=\"". $nouvelle_valeur. "\" 
                        WHERE id_droit=\"". $_GET['id_droit']. "\"";
        $resultat = mysqli_query($connexion,$requete);
    }

    $requete="SELECT d.*,m.* FROM droits d
                    INNER JOIN menus m
                    ON d.id_menu=m.id_menu
                    WHERE m.type_menu='back' ORDER BY m.rang_menu";
    $tab_resultats=afficher_droits($connexion, $requete);//,$requete); 
}else{
    header("Location:../index.php");    
}
?>