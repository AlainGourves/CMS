<?php

session_start();

if (isset($_SESSION['id_compte'])) {
    if(isset($_GET['action'])) {

        switch ($_GET['action']) {
            case 'afficher_comptes':

                break;
            case 'supprimer_comptes':

                break;
        }
    }

}else{
    header("Location:../index.php");
}
?>