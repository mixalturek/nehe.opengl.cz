<?php
Header('Content-Type: text/html; charset=iso-8859-2');
include_once 'p_func.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs">

<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-2" />
<meta http-equiv="content-language" content="cs" />

<title><?php echo ($p_title != '') ? $p_title : $g_title;?></title>

<meta name="webmaster" content="Michal Turek; http://woq.nipax.cz/" />
<meta name="copyright" content="Copyright (c) 2002-2008 Michal Turek" />
<meta name="robots" content="all, follow" />
<meta name="resource-type" content="document" />

<style type="text/css" media="all">@import "style.css";</style>
<style type="text/css" media="print">@import "print.css";</style>
<link href="images/website/web.ico" rel="shortcut icon" type="image/x-icon" />
</head>

<body>


<div id="header">
<div id="header_text">
<div><a href="http://nehe.ceske-hry.cz/" id="header_link"><strong>CZ NeHe OpenGL</strong></a></div>
<div>vše o programování 3D grafiky<br />s knihovnou OpenGL</div>
</div>
</div><!-- div id="header" -->



<?php
include_once 'p_sidebar.php';
?>


<div id="page">

<?php
/*
if(IsTut(basename($_SERVER[PHP_SELF])) || IsCl(basename($_SERVER[PHP_SELF])))
{
?>

<div class="google_ads">
<script type="text/javascript"><!--
google_ad_client = "pub-1961381671998782";
google_ad_slot = "0648478591";
google_ad_width = 160;
google_ad_height = 600;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>

<?php
}
*/
?>
