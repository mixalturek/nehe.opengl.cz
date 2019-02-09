<div id="sidebar">

<div class="group">
<div class="title">Hlavní menu</div>
<ul>
<li><?php MenuItem('index', 'Domù'); ?></li>
<li><?php MenuItem('autori', 'Autoøi'); ?></li>
<li><?php MenuItem('novinky', 'Novinky'); ?></li>
<li><?php MenuItem('download', 'Stáhnout'); ?></li>
<li><?php Blank('http://www.ceske-hry.cz/forum/', 'Fórum'); ?></li>
<li><a href="javascript:window.print();" title="Vytiskne tuto stránku">Tisk</a></li>
</ul>

<ul>
<li><?php MenuItem('tut_obsah', 'NeHe Tutoriály'); ?></li>
<li><?php MenuItem('clanky', 'Èlánky'); ?></li>
<li><?php MenuItem('programy', 'Programy'); ?></li>
<li><?php MenuItem('cl_gl_zacinam', 'Pomoc, zaèínám'); ?></li>
<li><?php MenuItem('cl_gl_faq', 'FAQ'); ?></li>
</ul>
</div>
<div class="clr"></div>


<div class="group">
<div class="title">NeHe Tutoriály</div>
<div class="rectangles">
<?php
MenuItem('tut_00', '00', 'Pøedmluva k NeHe Tutoriálùm'); echo "\n";
MenuItem('tut_01', '01', 'Vytvoøení OpenGL okna ve Windows'); echo "\n";
MenuItem('tut_02', '02', 'Vytváøení trojúhelníkù a ètyøúhelníkù'); echo "\n";
MenuItem('tut_03', '03', 'Barvy'); echo "\n";
MenuItem('tut_04', '04', 'Rotace'); echo "\n";
MenuItem('tut_05', '05', 'Pevné objekty'); echo "\n";
MenuItem('tut_06', '06', 'Textury'); echo "\n";
MenuItem('tut_07', '07', 'Texturové filtry, osvìtlení, ovládání pomocí klávesnice'); echo "\n";
MenuItem('tut_08', '08', 'Blending'); echo "\n";
MenuItem('tut_09', '09', 'Pohyb bitmap ve 3D prostoru'); echo "\n";
MenuItem('tut_10', '10', 'Vytvoøení 3D svìta a pohyb v nìm'); echo "\n";
MenuItem('tut_11', '11', 'Efekt vlnící se vlajky'); echo "\n";
MenuItem('tut_12', '12', 'Display list'); echo "\n";
MenuItem('tut_13', '13', 'Bitmapové fonty'); echo "\n";
MenuItem('tut_14', '14', 'Outline fonty'); echo "\n";
MenuItem('tut_15', '15', 'Mapování textur na fonty'); echo "\n";
MenuItem('tut_16', '16', 'Mlha'); echo "\n";
MenuItem('tut_17', '17', '2D fonty z textur'); echo "\n";
MenuItem('tut_18', '18', 'Kvadriky'); echo "\n";
MenuItem('tut_19', '19', 'Èásticové systémy'); echo "\n";
MenuItem('tut_20', '20', 'Maskování'); echo "\n";
MenuItem('tut_21', '21', 'Pøímky, antialiasing, èasování, pravoúhlá projekce, základní zvuky a jednoduchá herní logika'); echo "\n";
MenuItem('tut_22', '22', 'Bump Mapping a Multi Texturing'); echo "\n";
MenuItem('tut_23', '23', 'Mapování textur na kulové kvadriky'); echo "\n";
MenuItem('tut_24', '24', 'Výpis OpenGL roz¹íøení, oøezávací testy a textury z TGA obrázkù'); echo "\n";
MenuItem('tut_25', '25', 'Morfování objektù a jejich nahrávání z textového souboru'); echo "\n";
MenuItem('tut_26', '26', 'Odrazy a jejich oøezávání za pou¾ití stencil bufferu'); echo "\n";
MenuItem('tut_27', '27', 'Stíny'); echo "\n";
MenuItem('tut_28', '28', 'Bezierovy køivky a povrchy, fullscreen fix'); echo "\n";
MenuItem('tut_29', '29', 'Blitter, nahrávání .RAW textur'); echo "\n";
MenuItem('tut_30', '30', 'Detekce kolizí'); echo "\n";
MenuItem('tut_31', '31', 'Nahrávání a renderování modelù'); echo "\n";
MenuItem('tut_32', '32', 'Picking, alfa blending, alfa testing, sorting'); echo "\n";
MenuItem('tut_33', '33', 'Nahrávání komprimovaných i nekomprimovaných obrázkù TGA'); echo "\n";
MenuItem('tut_34', '34', 'Generování terénù a krajin za pou¾ití vý¹kového mapování textur'); echo "\n";
MenuItem('tut_35', '35', 'Pøehrávání videa ve formátu AVI'); echo "\n";
MenuItem('tut_36', '36', 'Radial Blur, renderování do textury'); echo "\n";
MenuItem('tut_37', '37', 'Cel-Shading'); echo "\n";
MenuItem('tut_38', '38', 'Nahrávání textur z resource souboru a texturování trojúhelníkù'); echo "\n";
MenuItem('tut_39', '39', 'Úvod do fyzikálních simulací'); echo "\n";
MenuItem('tut_40', '40', 'Fyzikální simulace lana'); echo "\n";
MenuItem('tut_41', '41', 'Volumetrická mlha a nahrávání obrázkù pomocí IPicture'); echo "\n";
MenuItem('tut_42', '42', 'Více viewportù'); echo "\n";
MenuItem('tut_43', '43', 'FreeType Fonty v OpenGL'); echo "\n";
MenuItem('tut_44', '44', 'Èoèkové efekty'); echo "\n";
MenuItem('tut_45', '45', 'Vertex Buffer Object (VBO)'); echo "\n";
MenuItem('tut_46', '46', 'Fullscreenový antialiasing'); echo "\n";
MenuItem('tut_47', '47', 'CG vertex shader'); echo "\n";
MenuItem('tut_48', '48', 'ArcBall rotace'); echo "\n";
?>
</div>
(<?php MenuItem('tut_obsah', 'obsah'); ?>)
</div>
<div class="clr"></div>


<div class="group">
<div class="title">Seriál o SDL <span class="blank"></span></div>
<div class="rectangles">
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-1/" title="Úvod">01</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-2/" title="Instalace SDL">02</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-3/" title="Inicializace SDL programu">03</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-4/" title="Vytvoøení okna">04</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-5/" title="Zobrazování grafiky">05</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-6/" title="Operace se surfacem">06</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-7/" title="Pøímý pøístup k pixelùm, kurzory">07</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-8/" title="OpenGL">08</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-9/" title="Výstup textu pomocí SDL_ttf">09</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-10/" title="Komunikace se správcem oken, úvod do událostí">10</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-11/" title="Fronta událostí">11</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-12/" title="Klávesnice">12</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-13/" title="My¹">13</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-14/" title="Joystick">14</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-15/" title="Ostatní události">15</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-16/" title="Èasovaèe a práce s èasem">16</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-17/" title="Zvuky a hudba">17</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-18/" title="Konverze zvukù, knihovna SDL_sound">18</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-19/" title="Pøehrávání zvukù pomocí SDL_mixer">19</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-20/" title="Hudba a efekty">20</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-21/" title="CD-ROM">21</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-22/" title="Vícevláknové programování">22</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-23/" title="SDL_RWops, SDL_Overlay + v¹e, na co se zapomnìlo">23</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-24/" title="Obsah">24</a>
</div>
<div>(<a href="http://www.root.cz/serialy/sdl-hry-nejen-pro-linux/" title="Obsah">obsah</a>)</div>
</div>
<div class="clr"></div>


<div class="group">
<div class="title">Vyhledávání</div>

<?php
/*
<!-- SiteSearch Google -->
<form method="get" action="http://nehe.ceske-hry.cz/search.php" target="_top">
<table border="0" bgcolor="#000000">
<tr><td nowrap="nowrap" valign="top" align="left" height="32">

</td>
<td nowrap="nowrap">
<input type="hidden" name="domains" value="nehe.ceske-hry.cz"></input>
<label for="sbi" style="display: none">Zadejte své vyhledávací termíny</label>
<input type="text" name="q" size="16" maxlength="255" value="" id="sbi"></input>
</td></tr>
<tr>
<td>&nbsp;</td>
<td nowrap="nowrap">
<table>
<tr>
<td>
<input type="radio" name="sitesearch" value="" checked id="ss0"></input>
<label for="ss0" title="Hledání na internetu"><font size="-1" color="#ffffff">Web</font></label></td>
<td>
<input type="radio" name="sitesearch" value="nehe.ceske-hry.cz" id="ss1"></input>
<label for="ss1" title="Vyhledávání nehe.ceske-hry.cz"><font size="-1" color="#ffffff">nehe.ceske-hry.cz</font></label></td>
</tr>
</table>
<label for="sbb" style="display: none">Odeslat vyhledávací formuláøe</label>
<input type="submit" name="sa" value="Vyhledávání Google" id="sbb"></input>
<input type="hidden" name="client" value="pub-1961381671998782"></input>
<input type="hidden" name="forid" value="1"></input>
<input type="hidden" name="channel" value="8175385271"></input>
<input type="hidden" name="ie" value="ISO-8859-2"></input>
<input type="hidden" name="oe" value="ISO-8859-2"></input>
<input type="hidden" name="safe" value="active"></input>
<input type="hidden" name="cof" value="GALT:#008000;GL:1;DIV:#000000;VLC:551A8B;AH:center;BGC:000000;LBGC:000000;ALC:66B5FF;LC:66B5FF;T:FFFFFF;GFNT:7777CC;GIMP:7777CC;FORID:11"></input>
<input type="hidden" name="hl" value="cs"></input>
</td></tr></table>
</form>
<!-- SiteSearch Google -->
*/
?>


<!-- SiteSearch Google -->
<form method="get" action="http://nehe.ceske-hry.cz/search.php">
<div>
<input type="hidden" name="domains" value="nehe.ceske-hry.cz" />

<div><label for="sbi" style="display: none">Zadejte své vyhledávací termíny</label>
<input type="text" name="q" size="16" maxlength="255" value="" id="sbi" /></div>

<div><input type="radio" name="sitesearch" value="" id="ss0" />
<label for="ss0" title="Hledání na internetu">Web</label></div>

<div><input type="radio" name="sitesearch" value="nehe.ceske-hry.cz" checked="checked" id="ss1" />
<label for="ss1" title="Vyhledávání nehe.ceske-hry.cz">NeHe</label></div>

<div><label for="sbb" style="display: none">Odeslat vyhledávací formuláøe</label>
<input type="submit" name="sa" value="Vyhledávání Google" id="sbb" /></div>

<input type="hidden" name="client" value="pub-1961381671998782" />
<input type="hidden" name="forid" value="1" />
<input type="hidden" name="channel" value="8175385271" />
<input type="hidden" name="ie" value="ISO-8859-2" />
<input type="hidden" name="oe" value="ISO-8859-2" />
<input type="hidden" name="safe" value="active" />
<input type="hidden" name="cof" value="GALT:#008000;GL:1;DIV:#000000;VLC:551A8B;AH:center;BGC:000000;LBGC:000000;ALC:66B5FF;LC:66B5FF;T:FFFFFF;GFNT:7777CC;GIMP:7777CC;FORID:11" />
<input type="hidden" name="hl" value="cs" />
</div>
</form>
<!-- SiteSearch Google -->
</div>


</div><!-- div id="sidebar" -->
