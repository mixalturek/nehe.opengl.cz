<?php
// Against spam
function Email($m)
{
	$m = str_replace('@', ' (zavináè) ', $m);
	echo "<span class=\"m\">&lt;$m&gt;</span>";
}

// Insert image
function Img($path, $alt)
{
	if(file_exists($path))
	{
		$path_big = str_replace("_sm", '', $path);
		$img = getimagesize($path);

		if($path != $path_big && file_exists($path_big))// With link to bigger
		{
			echo "<div class=\"img\"><a href=\"$path_big\"><img src=\"$path\" $img[3] alt=\"\" /><br />$alt</a></div>\n";
		}
		else
		{
			echo "<div class=\"img\"><img src=\"$path\" $img[3] alt=\"\" /><br />$alt</div>\n";
		}
	}
}

// Link to web
function Web($addr, $text, $title = '')
{
	if(file_exists($addr.'.php'))
		if(isset($_GET['offline']))
			if($title != '')
				echo "<a href=\"$addr.htm\" title=\"$title\">$text</a>";
			else
				echo "<a href=\"$addr.htm\">$text</a>";
		else
			if($title != '')
				echo "<a href=\"$addr.php\" title=\"$title\">$text</a>";
			else
				echo "<a href=\"$addr.php\">$text</a>";
	else
		echo "<span class=\"invalid_link\">$text</span>";
}

// Link to foreign website
function Blank($addr, $text = '')
{
	if($text)
		echo "<a href=\"$addr\" class=\"blank\" title=\"$addr\">$text</a>";
	else
		echo "<a href=\"$addr\" class=\"blank\">$addr</a>";
}

// Link for download file
function Down($path)
{
	if(file_exists($path))
	{
		$size = filesize($path);

		if(strlen($size) > 6)// MB
			printf("<a href=\"$path\" class=\"down\">%0.1f MB</a>", $size / 1048576);
		else if(strlen($size) > 3)// kB
			printf("<a href=\"$path\" class=\"down\">%0.1f kB</a>", $size / 1024);
		else// B
			echo "<a href=\"$path\" class=\"down\">$size B</a>";
	}
}

function MenuItem($addr, $text, $title = '')
{
	echo (basename($_SERVER['PHP_SELF']) == "$addr.php")
		? "<a class=\"active\">$text</a>" : Web($addr, $text, $title);
}



/////////////////////////////////////////////////////////////////////////

function IsCl($s)
{
	return ($s[0] == 'c' && $s[1] == 'l' && $s[2] == '_');
}

function IsTut($s)
{
	if($s[0] == 't' && $s[1] == 'u' && $s[2] == 't' && $s[3] == '_')
		return !($s == 'tut_obsah.php' || $s == 'tut_download.php');

	return false;
}

function Is33D($s)
{
	return ($s[0] == '3' && $s[1] == '3' && $s[2] == 'D' && $s[3] == '_');
}

function VypisEmail($m)
{
	if($m == 'woq@email.cz')
		$m = 'WOQ@seznam.cz';

	Email($m);
}

function FceImgNeHeMaly($i)
{
//	$L = ($i < 10) ? '0'.$i : $i;
//	echo "<img src=\"images/nehe_tut/tut_$L.jpg\" class=\"nehe_img_sm\" alt=\"Lekce $i\" />\n";
}

function FceImgNeHeVelky($i)
{
	$L = ($i < 10) ? '0'.$i : $i;

	if($i == 20)// Spe¹l :-]
	{
		echo "<div class=\"img\"><img src=\"images/nehe_tut/tut_".$L."_big_1.jpg\" class=\"nehe_img\" alt=\"Lekce $i - scéna 1\" /></div>\n";
		echo "<div class=\"img\"><img src=\"images/nehe_tut/tut_".$L."_big_2.jpg\" class=\"nehe_img\" alt=\"Lekce $i - scéna 2\" /></div>\n";
		return;
	}

	echo "<div class=\"img\"><img src=\"images/nehe_tut/tut_".$L."_big.jpg\" class=\"nehe_img\" alt=\"Lekce $i\" /></div>\n";
}

function FceNeHeOkolniLekce($i)
{
	$L_minus = ($i <= 10) ? '0'.($i-1) : ($i-1);
	$L_plus = ($i <= 8) ? '0'.($i+1) : ($i+1);

	if($i <= 0)
		echo '<p class="okolni_lekce"><a href="tut_'.$L_plus.'.php">Lekce '.($i+1).' &gt;&gt;&gt;</a></p>';
	else if($i >= 48)
		echo '<p class="okolni_lekce"><a href="tut_'.$L_minus.'.php">&lt;&lt;&lt; Lekce '.($i-1).'</a></p>';
	else
		echo '<p class="okolni_lekce"><a href="tut_'.$L_minus.'.php">&lt;&lt;&lt; Lekce '.($i-1).'</a> | <a href="tut_'.$L_plus.'.php">Lekce '.($i+1).' &gt;&gt;&gt;</a></p>';

	echo "\n";
}

function OdkazWeb($adresa, $text, $roll = '')
{
	Web($adresa, $text, $roll);
}

function OdkazBlank($adresa, $text = 0)
{
	Blank($adresa, $text);
}

function OdkazDown($adresa)
{
	Down($adresa);
}
?>