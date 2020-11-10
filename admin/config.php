<?php
if (isset($_SESSION['id_compte'])) {
	if(!empty($_POST['color_1']) && !empty($_POST['color_2']) && !empty($_POST['color_3'])){
		$new_css = ":root{\n";
		$new_css .= "\t--color_1: ". $_POST['color_1']. ";\n";
		$new_css .= "\t--color_2: ". $_POST['color_2']. ";\n";
		$new_css .= "\t--color_3: ". $_POST['color_3']. ";\n";
		$new_css .= "}";
		$css_file = @fopen($css_colors, "w");
		fputs($css_file, $new_css);
		fclose($css_file);
	}
}else{
    header("Location:../index.php");
}
?>