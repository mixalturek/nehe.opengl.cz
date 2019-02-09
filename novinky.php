<?
$g_title = 'CZ NeHe OpenGL - Novinky';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

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

<h3>Nový design</h3>
<p>U¾ to chtìlo zmìnu... ;-)</p>

</div>


<div class="object">
<div class="date">21.05.2006</div>

<h3>Projekt Shiny3D</h3>
<p>Aplikácia Shiny3D je 3D engine urèený pre outdoor hry. Za cieµ si kladie vytvorenie nádherného prostredia a jeho následnej, pokiaµ mo¾no èo najlep¹ej optimalizácie. V podstate sa chcem venova» najmä grafike a jej optimalizácii, ale do budúcna plánujem aj kolízie s terénom. Aplikácia je naprogramovaná v Delphi 7 (pomocou OOP) za pomoci kni¾nice OpenGL.</p>

</div>


<div class="object">
<div class="date">24.03.2006</div>

<h3>Kniha náv¹tìv</h3>
<p>... byla zru¹ena z dùvodu extrémního mno¾ství spamu (stejnì tam u¾ nikdo nepsal).</p>

</div>


<div class="object">
<div class="date">09.11.2005</div>

<h3>Pøesun celého webu na http://nehe.sh.cvut.cz/</h3>
<p>Nedávno vypr¹ela platnost domény opengl.cz a Mirek Topoláø, který ji mìl registrovanou a kterými mi poskytoval webhosting, se pravdìpodobnì rozhodl registraci dále neprodlu¾ovat. Tímto bych mu chtìl podìkovat za ta léta, bìhem kterých mi umo¾nil u sebe hostovat. Nyní je web umístìn na <?OdkazBlank('http://logout.sh.cvut.cz/sh4web/index.php', 'jednom z blokových serverù')?> Strahovských kolejí. Jeho administrátorùm, jmenovitì Charliemu a WilXovi, bych chtìl takté¾ podìkovat. No, kdy¾ u¾ jsem u toho dìkování, nelze nezapomenout na <?OdkazBlank('http://www.ceske-hry.cz/')?>, a <?OdkazBlank('http://www.programovani.com/')?>, kteøí mi u sebe takté¾ nabídli webhosting.</p>

</div>


<div class="object">
<div class="date">22.10.2005</div>

<h3>Terének (<?OdkazWeb('programy', 'Programy')?>)</h3>
<p>Terén je generovaný pomocí pøesouvání støedního bodu, jednoduchý pøístup, ale vypadá hezky. Po vygenerování se vyhladí, aby nevypadal tak ostøe, a spoèítá se osvìtlení, které je modulováno barvou podle vý¹ky. Je pøidaná i trocha travièky a dynamická obloha, nejsou to sice nìjaký pøevratný mráèky, ale pùsobí dobøe. Kdy¾ to vezmu ve zkratce, tak to zatím umí: nekoneèný, dynamicky generovaný terén, dynamická obloha, vlnící se tráva, voda s odlesky, generované stromy, øe¹ení viditelnosti, LOD, vertex buffery, multitexturing, programování textur pipeline... viz zdrojáky - je v nich mo¾ná kapku chaoz. Je to z toho dùvodu, ¾e vìci pustupnì pøidìlávám jak mì napadne. Jediný problém je zatím v navazazování jednotlivých plátù terénu, smoothing tam moc nefunguje ;-(.</p>

</div>


<div class="object">
<div class="date">08.10.2005</div>

<h3>3D grafika bez OpenGL (<?OdkazWeb('programy', 'Programy')?>)</h3>
<p>Program naèítá 3D objekt z .IFS souboru a vykresluje ho na obrazovku. To sice není nic slo¾itého, ale jen do doby, ne¾ vám øeknou, ¾e nesmíte pou¾ít ¾ádné OpenGL funkce ani funkce z jiného 3D API. A pokud je navíc pátek a termín odevzdání máte definován na pondìlí, jedná se o docela vra¾ednou kombinaci... Kompletní popis programu naleznete na stránkách <?OdkazBlank('http://kmlinux.fjfi.cvut.cz/~chalupec/pogr/galerie-2005/haiduk-adam.php', 'Fakulty jaderné');?>, ÈVUT Praha.</p>

<h3>Web <?OdkazBlank('http://herakles.zcu.cz/~miva/');?></h3>
<p>Najdete tam nìkolik úchvatnì vypadajících OpenGL programù...</p>

</div>


<div class="object">
<div class="date">07.09.2005</div>

<h3>Èlánek <?OdkazWeb('cl_gl_kamera_3d', 'Kamera pro 3D svìt');?> - Michal Turek</h3>
<p>V tomto èlánku se pokusíme implementovat snadno pou¾itelnou tøídu kamery, která bude vhodná pro pohyby v obecném 3D svìtì, napøíklad pro nìjakou støíleèku - my¹ mìní smìr natoèení a ¹ipky na klávesnici zaji¹»ují pohyb. Pøesto¾e budeme pou¾ívat malièko matematiky, nebojte se a smìle do ètení!</p>

<h3>Èlánek <?OdkazWeb('cl_gl_generovani_terenu', 'Procedurálne generovanie terénu');?> - Peter Mindek</h3>
<p>Mo¾no ste u¾ poèuli o vý¹kových mapách. Sú to také èiernobiele obrázky, pomocou ktorých sa vytvára 3D terén (vý¹ka terénu na urèitej pozícii je urèená farbou zodpovedajúceho bodu na vý¹kovej mape). Najjednoduch¹ie je vý¹kovú mapu naèíta» zo súboru a je pokoj. Sú v¹ak situácie, ako napr. keï robíte grafické demo, ktoré má by» èo najmen¹ie, keï príde vhod vý¹kovú mapu vygenerova» procedurálne. Tak¾e si uká¾eme ako na to. E¹te snáï spomeniem ¾e èíta» ïalej mô¾u aj tí, ktorí chcú vedie» ako vygenerova» takzvané &quot;oblaky&quot; (niekedy sa tomu hovorí aj plazma), nakoµko tento tutoriál bude z veµkej èasti práve o tom.</p>

<h3>Program <?OdkazWeb('programy', 'Barevná paleta');?> - ssil</h3>
<p>Program vykresluje barevnou paletu. Její odstín se dá zmìnit zvolením barevné slo¾ky pomocí kláves r, g, b a následným stisknutím ¹ipky doleva/doprava. Díky pøenositelné knihovnì SDL lze po kompilaci spustit pod MS Windows, GNU/Linuxem a dal¹ími operaènímy systémy.</p>

</div>



<div class="object">
<div class="date">22.06.2005</div>

<h3>OpenGL na <?OdkazBlank('http://www.root.cz/', 'root.cz');?></h3>
<p>Do odkazù jsem pøidal nìkolik linkù na perfektní seriály o OpenGL</p>

<ul>
<li><?OdkazBlank('http://www.root.cz/serialy/graficka-knihovna-opengl/', 'Grafická knihovna OpenGL');?></li>
<li><?OdkazBlank('http://www.root.cz/serialy/opengl-evaluatory/', 'OpenGL evaluátory');?></li>
<li><?OdkazBlank('http://www.root.cz/serialy/opengl-imaging-subset/', 'OpenGL Imaging Subset');?></li>
<li><?OdkazBlank('http://www.root.cz/serialy/opengl-a-nadstavbova-knihovna-glu/', 'OpenGL a nadstavbová knihovna GLU');?></li>
<li><?OdkazBlank('http://www.root.cz/serialy/tvorba-prenositelnych-grafickych-aplikaci-vyuzivajicich-knihovnu-glut/', 'Tvorba pøenositelných grafických aplikací vyu¾ívajících knihovnu GLUT');?></li>
</ul>


</div>


<div class="object">
<div class="date">09.05.2005</div>

<h3>Hry a herní tutorály</h3>
<p>Michal Bubnar (Michalbb) zaèíná tvoøit web <?OdkazBlank('http://www.mgbsoft.wz.cz/');?>, lze tam nalézt nìkolik herních tutoriálù a postupnì pøibývají dal¹í...</p>

</div>


<div class="object">
<div class="date">02.03.2005</div>

<h3>Tunneler (<?OdkazWeb('programy', 'Programy');?>)</h3>
<p>Hra je urcena hlavne pre dvoch hracov, ale je implementovane aj AI, takze si moze clovek zahrat aj sam. Pravidla su prebrate zo starej dosovky Tuneller (nasa sa vola Tunneler, pretoze nam to tak napisal zadavatel :)) ). Na nahodne vygenerovanej mape su nahodne umiestnene dva domceky, kde zacinaju jednotlivi hraci/UI. Tu si mozu tanky doplnat zdravie aj energiu. Vsade okolo je zem, ktorou si treba razit tunely, co stoji istu energiu. Ta sa spotrebovava aj na pohyb a na strielanie. Ulohou je rostrielat (ako inak :) ) protivnika. Hra sa na 1 - 10 vitazstiev podla nastavenia, standardne 3. Blizsie detaily zistite pocas hry :)).</p>

</div>


<div class="object">
<div class="date">27.01.2005</div>

<h3><?OdkazWeb('cl_gl_billboard', 'Billboarding (pøiklápìní polygonù ke kameøe)');?> - Michal Turek - Woq</h3>
<p>Ka¾dý, kdo nìkdy programoval èásticové systémy, se jistì setkal s problémem, jak zaøídit, aby byly polygony viditelné z jakéhokoli smìru. Nebo-li, aby se nikdy nestalo, ¾e pøi natoèení kamery kolmo na rovinu èástice, nebyla vidìt pouze tenká linka. Slo¾itý problém, ultra jednoduché øe¹ení...</p>

</div>


<div class="object">
<a href="novinky_archiv.php">Archiv novinek</a>
</div>


<?
include 'p_end.php';
?>
