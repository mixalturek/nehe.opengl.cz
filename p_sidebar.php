<div id="sidebar">

<div class="group">
<div class="title">Hlavn� menu</div>
<ul>
<li><?php MenuItem('index', 'Dom�'); ?></li>
<li><?php MenuItem('autori', 'Auto�i'); ?></li>
<li><?php MenuItem('novinky', 'Novinky'); ?></li>
<li><?php MenuItem('download', 'St�hnout'); ?></li>
<li><?php Blank('http://www.ceske-hry.cz/forum/', 'F�rum'); ?></li>
<li><a href="javascript:window.print();" title="Vytiskne tuto str�nku">Tisk</a></li>
</ul>

<ul>
<li><?php MenuItem('tut_obsah', 'NeHe Tutori�ly'); ?></li>
<li><?php MenuItem('clanky', '�l�nky'); ?></li>
<li><?php MenuItem('programy', 'Programy'); ?></li>
<li><?php MenuItem('cl_gl_zacinam', 'Pomoc, za��n�m'); ?></li>
<li><?php MenuItem('cl_gl_faq', 'FAQ'); ?></li>
</ul>
</div>
<div class="clr"></div>


<div class="group">
<div class="title">NeHe Tutori�ly</div>
<div class="rectangles">
<?php
MenuItem('tut_00', '00', 'P�edmluva k NeHe Tutori�l�m'); echo "\n";
MenuItem('tut_01', '01', 'Vytvo�en� OpenGL okna ve Windows'); echo "\n";
MenuItem('tut_02', '02', 'Vytv��en� troj�heln�k� a �ty��heln�k�'); echo "\n";
MenuItem('tut_03', '03', 'Barvy'); echo "\n";
MenuItem('tut_04', '04', 'Rotace'); echo "\n";
MenuItem('tut_05', '05', 'Pevn� objekty'); echo "\n";
MenuItem('tut_06', '06', 'Textury'); echo "\n";
MenuItem('tut_07', '07', 'Texturov� filtry, osv�tlen�, ovl�d�n� pomoc� kl�vesnice'); echo "\n";
MenuItem('tut_08', '08', 'Blending'); echo "\n";
MenuItem('tut_09', '09', 'Pohyb bitmap ve 3D prostoru'); echo "\n";
MenuItem('tut_10', '10', 'Vytvo�en� 3D sv�ta a pohyb v n�m'); echo "\n";
MenuItem('tut_11', '11', 'Efekt vln�c� se vlajky'); echo "\n";
MenuItem('tut_12', '12', 'Display list'); echo "\n";
MenuItem('tut_13', '13', 'Bitmapov� fonty'); echo "\n";
MenuItem('tut_14', '14', 'Outline fonty'); echo "\n";
MenuItem('tut_15', '15', 'Mapov�n� textur na fonty'); echo "\n";
MenuItem('tut_16', '16', 'Mlha'); echo "\n";
MenuItem('tut_17', '17', '2D fonty z textur'); echo "\n";
MenuItem('tut_18', '18', 'Kvadriky'); echo "\n";
MenuItem('tut_19', '19', '��sticov� syst�my'); echo "\n";
MenuItem('tut_20', '20', 'Maskov�n�'); echo "\n";
MenuItem('tut_21', '21', 'P��mky, antialiasing, �asov�n�, pravo�hl� projekce, z�kladn� zvuky a jednoduch� hern� logika'); echo "\n";
MenuItem('tut_22', '22', 'Bump Mapping a Multi Texturing'); echo "\n";
MenuItem('tut_23', '23', 'Mapov�n� textur na kulov� kvadriky'); echo "\n";
MenuItem('tut_24', '24', 'V�pis OpenGL roz���en�, o�ez�vac� testy a textury z TGA obr�zk�'); echo "\n";
MenuItem('tut_25', '25', 'Morfov�n� objekt� a jejich nahr�v�n� z textov�ho souboru'); echo "\n";
MenuItem('tut_26', '26', 'Odrazy a jejich o�ez�v�n� za pou�it� stencil bufferu'); echo "\n";
MenuItem('tut_27', '27', 'St�ny'); echo "\n";
MenuItem('tut_28', '28', 'Bezierovy k�ivky a povrchy, fullscreen fix'); echo "\n";
MenuItem('tut_29', '29', 'Blitter, nahr�v�n� .RAW textur'); echo "\n";
MenuItem('tut_30', '30', 'Detekce koliz�'); echo "\n";
MenuItem('tut_31', '31', 'Nahr�v�n� a renderov�n� model�'); echo "\n";
MenuItem('tut_32', '32', 'Picking, alfa blending, alfa testing, sorting'); echo "\n";
MenuItem('tut_33', '33', 'Nahr�v�n� komprimovan�ch i nekomprimovan�ch obr�zk� TGA'); echo "\n";
MenuItem('tut_34', '34', 'Generov�n� ter�n� a krajin za pou�it� v��kov�ho mapov�n� textur'); echo "\n";
MenuItem('tut_35', '35', 'P�ehr�v�n� videa ve form�tu AVI'); echo "\n";
MenuItem('tut_36', '36', 'Radial Blur, renderov�n� do textury'); echo "\n";
MenuItem('tut_37', '37', 'Cel-Shading'); echo "\n";
MenuItem('tut_38', '38', 'Nahr�v�n� textur z resource souboru a texturov�n� troj�heln�k�'); echo "\n";
MenuItem('tut_39', '39', '�vod do fyzik�ln�ch simulac�'); echo "\n";
MenuItem('tut_40', '40', 'Fyzik�ln� simulace lana'); echo "\n";
MenuItem('tut_41', '41', 'Volumetrick� mlha a nahr�v�n� obr�zk� pomoc� IPicture'); echo "\n";
MenuItem('tut_42', '42', 'V�ce viewport�'); echo "\n";
MenuItem('tut_43', '43', 'FreeType Fonty v OpenGL'); echo "\n";
MenuItem('tut_44', '44', '�o�kov� efekty'); echo "\n";
MenuItem('tut_45', '45', 'Vertex Buffer Object (VBO)'); echo "\n";
MenuItem('tut_46', '46', 'Fullscreenov� antialiasing'); echo "\n";
MenuItem('tut_47', '47', 'CG vertex shader'); echo "\n";
MenuItem('tut_48', '48', 'ArcBall rotace'); echo "\n";
?>
</div>
(<?php MenuItem('tut_obsah', 'obsah'); ?>)
</div>
<div class="clr"></div>


<div class="group">
<div class="title">Seri�l o SDL <span class="blank"></span></div>
<div class="rectangles">
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-1/" title="�vod">01</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-2/" title="Instalace SDL">02</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-3/" title="Inicializace SDL programu">03</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-4/" title="Vytvo�en� okna">04</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-5/" title="Zobrazov�n� grafiky">05</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-6/" title="Operace se surfacem">06</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-7/" title="P��m� p��stup k pixel�m, kurzory">07</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-8/" title="OpenGL">08</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-9/" title="V�stup textu pomoc� SDL_ttf">09</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-10/" title="Komunikace se spr�vcem oken, �vod do ud�lost�">10</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-11/" title="Fronta ud�lost�">11</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-12/" title="Kl�vesnice">12</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-13/" title="My�">13</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-14/" title="Joystick">14</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-15/" title="Ostatn� ud�losti">15</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-16/" title="�asova�e a pr�ce s �asem">16</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-17/" title="Zvuky a hudba">17</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-18/" title="Konverze zvuk�, knihovna SDL_sound">18</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-19/" title="P�ehr�v�n� zvuk� pomoc� SDL_mixer">19</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-20/" title="Hudba a efekty">20</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-21/" title="CD-ROM">21</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-22/" title="V�cevl�knov� programov�n�">22</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-23/" title="SDL_RWops, SDL_Overlay + v�e, na co se zapomn�lo">23</a>
<a href="http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-24/" title="Obsah">24</a>
</div>
<div>(<a href="http://www.root.cz/serialy/sdl-hry-nejen-pro-linux/" title="Obsah">obsah</a>)</div>
</div>
<div class="clr"></div>


<div class="group">
<div class="title">Vyhled�v�n�</div>

<?php
/*
<!-- SiteSearch Google -->
<form method="get" action="http://nehe.ceske-hry.cz/search.php" target="_top">
<table border="0" bgcolor="#000000">
<tr><td nowrap="nowrap" valign="top" align="left" height="32">

</td>
<td nowrap="nowrap">
<input type="hidden" name="domains" value="nehe.ceske-hry.cz"></input>
<label for="sbi" style="display: none">Zadejte sv� vyhled�vac� term�ny</label>
<input type="text" name="q" size="16" maxlength="255" value="" id="sbi"></input>
</td></tr>
<tr>
<td>&nbsp;</td>
<td nowrap="nowrap">
<table>
<tr>
<td>
<input type="radio" name="sitesearch" value="" checked id="ss0"></input>
<label for="ss0" title="Hled�n� na internetu"><font size="-1" color="#ffffff">Web</font></label></td>
<td>
<input type="radio" name="sitesearch" value="nehe.ceske-hry.cz" id="ss1"></input>
<label for="ss1" title="Vyhled�v�n� nehe.ceske-hry.cz"><font size="-1" color="#ffffff">nehe.ceske-hry.cz</font></label></td>
</tr>
</table>
<label for="sbb" style="display: none">Odeslat vyhled�vac� formul��e</label>
<input type="submit" name="sa" value="Vyhled�v�n� Google" id="sbb"></input>
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

<div><label for="sbi" style="display: none">Zadejte sv� vyhled�vac� term�ny</label>
<input type="text" name="q" size="16" maxlength="255" value="" id="sbi" /></div>

<div><input type="radio" name="sitesearch" value="" id="ss0" />
<label for="ss0" title="Hled�n� na internetu">Web</label></div>

<div><input type="radio" name="sitesearch" value="nehe.ceske-hry.cz" checked="checked" id="ss1" />
<label for="ss1" title="Vyhled�v�n� nehe.ceske-hry.cz">NeHe</label></div>

<div><label for="sbb" style="display: none">Odeslat vyhled�vac� formul��e</label>
<input type="submit" name="sa" value="Vyhled�v�n� Google" id="sbb" /></div>

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
