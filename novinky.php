<?
$g_title = 'CZ NeHe OpenGL - Novinky';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Novinky</h1>


<!--
<div class="object">
<div class="date"></div>


<h3></h3>
<p></p>

</div>
-->


<div class="object">
<div class="date">30.03.2008</div>

<h3>Nov� design</h3>
<p>U� to cht�lo zm�nu... ;-)</p>

</div>


<div class="object">
<div class="date">21.05.2006</div>

<h3>Projekt Shiny3D</h3>
<p>Aplik�cia Shiny3D je 3D engine ur�en� pre outdoor hry. Za cie� si kladie vytvorenie n�dhern�ho prostredia a jeho n�slednej, pokia� mo�no �o najlep�ej optimaliz�cie. V podstate sa chcem venova� najm� grafike a jej optimaliz�cii, ale do bud�cna pl�nujem aj kol�zie s ter�nom. Aplik�cia je naprogramovan� v Delphi 7 (pomocou OOP) za pomoci kni�nice OpenGL.</p>

</div>


<div class="object">
<div class="date">24.03.2006</div>

<h3>Kniha n�v�t�v</h3>
<p>... byla zru�ena z d�vodu extr�mn�ho mno�stv� spamu (stejn� tam u� nikdo nepsal).</p>

</div>


<div class="object">
<div class="date">09.11.2005</div>

<h3>P�esun cel�ho webu na http://nehe.sh.cvut.cz/</h3>
<p>Ned�vno vypr�ela platnost dom�ny opengl.cz a Mirek Topol��, kter� ji m�l registrovanou a kter�mi mi poskytoval webhosting, se pravd�podobn� rozhodl registraci d�le neprodlu�ovat. T�mto bych mu cht�l pod�kovat za ta l�ta, b�hem kter�ch mi umo�nil u sebe hostovat. Nyn� je web um�st�n na <?OdkazBlank('http://logout.sh.cvut.cz/sh4web/index.php', 'jednom z blokov�ch server�')?> Strahovsk�ch kolej�. Jeho administr�tor�m, jmenovit� Charliemu a WilXovi, bych cht�l takt� pod�kovat. No, kdy� u� jsem u toho d�kov�n�, nelze nezapomenout na <?OdkazBlank('http://www.ceske-hry.cz/')?>, a <?OdkazBlank('http://www.programovani.com/')?>, kte�� mi u sebe takt� nab�dli webhosting.</p>

</div>


<div class="object">
<div class="date">22.10.2005</div>

<h3>Ter�nek (<?OdkazWeb('programy', 'Programy')?>)</h3>
<p>Ter�n je generovan� pomoc� p�esouv�n� st�edn�ho bodu, jednoduch� p��stup, ale vypad� hezky. Po vygenerov�n� se vyhlad�, aby nevypadal tak ost�e, a spo��t� se osv�tlen�, kter� je modulov�no barvou podle v��ky. Je p�idan� i trocha travi�ky a dynamick� obloha, nejsou to sice n�jak� p�evratn� mr��ky, ale p�sob� dob�e. Kdy� to vezmu ve zkratce, tak to zat�m um�: nekone�n�, dynamicky generovan� ter�n, dynamick� obloha, vln�c� se tr�va, voda s odlesky, generovan� stromy, �e�en� viditelnosti, LOD, vertex buffery, multitexturing, programov�n� textur pipeline... viz zdroj�ky - je v nich mo�n� kapku chaoz. Je to z toho d�vodu, �e v�ci pustupn� p�id�l�v�m jak m� napadne. Jedin� probl�m je zat�m v navazazov�n� jednotliv�ch pl�t� ter�nu, smoothing tam moc nefunguje ;-(.</p>

</div>


<div class="object">
<div class="date">08.10.2005</div>

<h3>3D grafika bez OpenGL (<?OdkazWeb('programy', 'Programy')?>)</h3>
<p>Program na��t� 3D objekt z .IFS souboru a vykresluje ho na obrazovku. To sice nen� nic slo�it�ho, ale jen do doby, ne� v�m �eknou, �e nesm�te pou��t ��dn� OpenGL funkce ani funkce z jin�ho 3D API. A pokud je nav�c p�tek a term�n odevzd�n� m�te definov�n na pond�l�, jedn� se o docela vra�ednou kombinaci... Kompletn� popis programu naleznete na str�nk�ch <?OdkazBlank('http://kmlinux.fjfi.cvut.cz/~chalupec/pogr/galerie-2005/haiduk-adam.php', 'Fakulty jadern�');?>, �VUT Praha.</p>

<h3>Web <?OdkazBlank('http://herakles.zcu.cz/~miva/');?></h3>
<p>Najdete tam n�kolik �chvatn� vypadaj�c�ch OpenGL program�...</p>

</div>


<div class="object">
<div class="date">07.09.2005</div>

<h3>�l�nek <?OdkazWeb('cl_gl_kamera_3d', 'Kamera pro 3D sv�t');?> - Michal Turek</h3>
<p>V tomto �l�nku se pokus�me implementovat snadno pou�itelnou t��du kamery, kter� bude vhodn� pro pohyby v obecn�m 3D sv�t�, nap��klad pro n�jakou st��le�ku - my� m�n� sm�r nato�en� a �ipky na kl�vesnici zaji��uj� pohyb. P�esto�e budeme pou��vat mali�ko matematiky, nebojte se a sm�le do �ten�!</p>

<h3>�l�nek <?OdkazWeb('cl_gl_generovani_terenu', 'Procedur�lne generovanie ter�nu');?> - Peter Mindek</h3>
<p>Mo�no ste u� po�uli o v��kov�ch map�ch. S� to tak� �iernobiele obr�zky, pomocou ktor�ch sa vytv�ra 3D ter�n (v��ka ter�nu na ur�itej poz�cii je ur�en� farbou zodpovedaj�ceho bodu na v��kovej mape). Najjednoduch�ie je v��kov� mapu na��ta� zo s�boru a je pokoj. S� v�ak situ�cie, ako napr. ke� rob�te grafick� demo, ktor� m� by� �o najmen�ie, ke� pr�de vhod v��kov� mapu vygenerova� procedur�lne. Tak�e si uk�eme ako na to. E�te sn�� spomeniem �e ��ta� �alej m��u aj t�, ktor� chc� vedie� ako vygenerova� takzvan� &quot;oblaky&quot; (niekedy sa tomu hovor� aj plazma), nako�ko tento tutori�l bude z�ve�kej �asti pr�ve o tom.</p>

<h3>Program <?OdkazWeb('programy', 'Barevn� paleta');?> - ssil</h3>
<p>Program vykresluje barevnou paletu. Jej� odst�n se d� zm�nit zvolen�m barevn� slo�ky pomoc� kl�ves r, g, b a n�sledn�m stisknut�m �ipky doleva/doprava. D�ky p�enositeln� knihovn� SDL lze po kompilaci spustit pod MS Windows, GNU/Linuxem a dal��mi opera�n�my syst�my.</p>

</div>



<div class="object">
<div class="date">22.06.2005</div>

<h3>OpenGL na <?OdkazBlank('http://www.root.cz/', 'root.cz');?></h3>
<p>Do odkaz� jsem p�idal n�kolik link� na perfektn� seri�ly o OpenGL</p>

<ul>
<li><?OdkazBlank('http://www.root.cz/serialy/graficka-knihovna-opengl/', 'Grafick� knihovna OpenGL');?></li>
<li><?OdkazBlank('http://www.root.cz/serialy/opengl-evaluatory/', 'OpenGL evalu�tory');?></li>
<li><?OdkazBlank('http://www.root.cz/serialy/opengl-imaging-subset/', 'OpenGL Imaging Subset');?></li>
<li><?OdkazBlank('http://www.root.cz/serialy/opengl-a-nadstavbova-knihovna-glu/', 'OpenGL a nadstavbov� knihovna GLU');?></li>
<li><?OdkazBlank('http://www.root.cz/serialy/tvorba-prenositelnych-grafickych-aplikaci-vyuzivajicich-knihovnu-glut/', 'Tvorba p�enositeln�ch grafick�ch aplikac� vyu��vaj�c�ch knihovnu GLUT');?></li>
</ul>


</div>


<div class="object">
<div class="date">09.05.2005</div>

<h3>Hry a hern� tutor�ly</h3>
<p>Michal Bubnar (Michalbb) za��n� tvo�it web <?OdkazBlank('http://www.mgbsoft.wz.cz/');?>, lze tam nal�zt n�kolik hern�ch tutori�l� a postupn� p�ib�vaj� dal��...</p>

</div>


<div class="object">
<div class="date">02.03.2005</div>

<h3>Tunneler (<?OdkazWeb('programy', 'Programy');?>)</h3>
<p>Hra je urcena hlavne pre dvoch hracov, ale je implementovane aj AI, takze si moze clovek zahrat aj sam. Pravidla su prebrate zo starej dosovky Tuneller (nasa sa vola Tunneler, pretoze nam to tak napisal zadavatel :)) ). Na nahodne vygenerovanej mape su nahodne umiestnene dva domceky, kde zacinaju jednotlivi hraci/UI. Tu si mozu tanky doplnat zdravie aj energiu. Vsade okolo je zem, ktorou si treba razit tunely, co stoji istu energiu. Ta sa spotrebovava aj na pohyb a na strielanie. Ulohou je rostrielat (ako inak :) ) protivnika. Hra sa na 1 - 10 vitazstiev podla nastavenia, standardne 3. Blizsie detaily zistite pocas hry :)).</p>

</div>


<div class="object">
<div class="date">27.01.2005</div>

<h3><?OdkazWeb('cl_gl_billboard', 'Billboarding (p�ikl�p�n� polygon� ke kame�e)');?> - Michal Turek - Woq</h3>
<p>Ka�d�, kdo n�kdy programoval ��sticov� syst�my, se jist� setkal s probl�mem, jak za��dit, aby byly polygony viditeln� z jak�hokoli sm�ru. Nebo-li, aby se nikdy nestalo, �e p�i nato�en� kamery kolmo na rovinu ��stice, nebyla vid�t pouze tenk� linka. Slo�it� probl�m, ultra jednoduch� �e�en�...</p>

</div>


<div class="object">
<a href="novinky_archiv.php">Archiv novinek</a>
</div>


<?
include 'p_end.php';
?>
