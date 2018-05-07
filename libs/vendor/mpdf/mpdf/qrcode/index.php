<?php
	$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
	if (!$msg) $msg = "Le site du spipu\r\nhttp://spipu.net/";

	$err = isset($_GET['err']) ? $_GET['err'] : '';
	if (!in_array($err, array('L', 'M', 'Q', 'H'))) $err = 'L';

	require_once('qrcode.class.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Spipu Qrcode <?= __CLASS_QRCODE__; ?></title>
		<meta name="Title"			content="Spipu - Qrcode <?= __CLASS_QRCODE__; ?>" > 
		<meta name="Description"	content="Spipu - Qrcode <?= __CLASS_QRCODE__; ?>" >
		<meta name="Keywords"		content="spipu">
		<meta name="Author"			content="spipu" >
		<meta name="Reply-to"		content="webmaster@spipu.net" >
		<meta name="Copyright"		content="(c)2009 Spipu" >
		<meta http-equiv="Content-Type"	content="text/html; charset=windows-1252" >
		<style type="text/css">
<!--
table.qr
{
	border-collapse: collapse;
	border: solid 1px black;
	table-layout: fixed;
}

table.qr td
{
	width: 5px;
	height: 5px;
	font-size: 2px;
}

table.qr td.on
{
	background: #000000;
}
-->
		</style>	
	</head>
	<body>
		<center>
			<form method="GET" action="">
				<textarea name="msg" cols="40" rows="7"><?= htmlentities($msg); ?></textarea><br>
				Correction d'erreur : 
				<select name="err">
					<option value="L" <?= $err=='L' ? 'selected' : ''; ?>>L</option>
					<option value="M" <?= $err=='M' ? 'selected' : ''; ?>>M</option>
					<option value="Q" <?= $err=='Q' ? 'selected' : ''; ?>>Q</option>
					<option value="H" <?= $err=='H' ? 'selected' : ''; ?>>H</option>
				</select> | 
				<input type="submit" value="Afficher">
			</form>
			<hr>
			Génération d'un tableau HTML :<br> 
<?php
	$qrcode = new QRcode(utf8_encode($msg), $err);
	$qrcode->displayHTML();
?>
			<br>
			Génération d'une image PNG : <br>
			<img src="./image.php?msg=<?= urlencode($msg); ?>&amp;err=<?= urlencode($err); ?>" alt="generation qr-code" style="border: solid 1px black;">
		</center>
	</body>
</html>
