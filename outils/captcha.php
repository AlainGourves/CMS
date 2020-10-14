<?php
session_start();
//Le nombre de caracteres
$ncarac = 5;
//Le nombre de lignes
$nlignes = 6;
//Les caractères qui seront utilises
$carac = array('@','2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
$nca = count($carac);//On determine le nombre de lettres possible

// Nom de la police à utiliser
$font =__DIR__ .  "/adler.ttf";
 
//On determine les tailles de limage
$x = $ncarac*30+20;
$y = 40;

//On cree limage
$img = imagecreatetruecolor($x,$y);

//pour rendre le fond transparent
imagealphablending($img, false);
imagefill($img,0,0,0x7fff0000);
imagesavealpha($img, true); 

//On ajoute les caracteres
$chaine = "";
for($i=1;$i<=$ncarac;$i++) {
	//On ajoute $ncarac caracteres
	$c = $carac[rand(0,($nca-1))];//Le nouveau caractere sera choisi aleatoirement

	//fichier image
	//taille police
	//angle des caracteres
	//x et y : point de départ du premier caractere
	//couleur désirée pour le texte
	//police du texte
	//texte à écrire
	imagettftext($img, 25, rand(-10,10), (($i-1)*30)+5, 30, imagecolorallocate($img, rand(0,100), rand(0,100), rand(0,100)),$font, $c);//On ajoute le caractere sur limage
	$chaine .= $c;//On ajoute le nouveau caractere a la chaine
}
	
//On ajoute les lignes
for($i=1;$i<=$nlignes;$i++) {
	//On ajoute "$nlignes" lignes
	imagesetthickness($img,rand(1,3));//On specifie l'epaisseur de la ligne
	imageline($img,rand(0,$x),rand(0,$y),rand(0,$x),rand(0,$y), imagecolorallocate($img, rand(0,100), rand(0,100), rand(0,100)));//On ajoute la ligne
}

//On stocke la chaine de caractere dans les sessions
$_SESSION['captcha'] = $chaine;
//On affiche l'image finale
header('Content-type: image/png');
imagepng($img);

?>
