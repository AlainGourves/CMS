<?php 
//permet d'autoriser l'usage des variables de session
session_start();
require_once("../outils/fonctions.php"); 
	
// si appui sur btn "Entrer" du form
if(isset($_POST['submit'])) {
	// login, passw & captcha ne sont pas vides
	if(!empty($_POST['captcha']) && !empty($_POST['login_compte']) && !empty($_POST['pass_compte'])) {
		// captcha ok 
		if(isset($_SESSION['captcha']) && $_SESSION['captcha']==$_POST['captcha']) {
			login($_POST['login_compte'],$_POST['pass_compte']); 	
		}			         
	}				
}
	
include('login.html');
?>