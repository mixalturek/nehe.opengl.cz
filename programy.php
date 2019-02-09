<?
$g_title = 'CZ NeHe OpenGL - Èeské OpenGL programy';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Èeské OpenGL programy</h1>


<div class="object">
<img class="img_prog" src="images/programy/terenek.jpg" alt="Terének" />
<h3>Terének</h3>
<div>Autor: Michal Varnu¹ka <?VypisEmail('michal.varnuska@gmail.com');?></div>
<div>Web: <?OdkazBlank('http://herakles.zcu.cz/~miva/');?></div>
<div>Pøidáno: 22.10.2005</div>
<div>Licence: ???</div>
<div>Download: <?OdkazDown('download/programy/terenek.tar.bz2');?> (se zdroj. kódy)</div>
<div>Programovací jazyk: MSVC 2003</div>
<p>Terén je generovaný pomocí pøesouvání støedního bodu, jednoduchý pøístup, ale vypadá hezky. Po vygenerování se vyhladí, aby nevypadal tak ostøe, a spoèítá se osvìtlení, které je modulováno barvou podle vý¹ky. Je pøidaná i trocha travièky a dynamická obloha, nejsou to sice nìjaký pøevratný mráèky, ale pùsobí dobøe. Kdy¾ to vezmu ve zkratce, tak to zatím umí: nekoneèný, dynamicky generovaný terén, dynamická obloha, vlnící se tráva, voda s odlesky, generované stromy, øe¹ení viditelnosti, LOD, vertex buffery, multitexturing, programování textur pipeline... viz zdrojáky - je v nich mo¾ná kapku chaoz. Je to z toho dùvodu, ¾e vìci pustupnì pøidìlávám jak mì napadne. Jediný problém je zatím v navazazování jednotlivých plátù terénu, smoothing tam moc nefunguje ;-(.</p>
</div>


<div class="object">
<img class="img_prog" src="images/programy/3d_putpixel.png" alt="3D grafika bez OpenGL" />
<h3>3D grafika bez OpenGL</h3>
<div>Autor: Adam Haiduk <?VypisEmail('adam.haiduk@atlas.cz');?></div>
<div>Pøidáno: 08.10.2005</div>
<div>Licence: ???</div>
<div>Download: <?OdkazDown('download/programy/3d_putpixel.tar.gz');?> (se zdroj. kódy)</div>
<div>Programovací jazyk: C/C++, SDL</div>
<p>Program naèítá 3D objekt z .IFS souboru a vykresluje ho na obrazovku. To sice není nic slo¾itého, ale jen do doby, ne¾ vám øeknou, ¾e nesmíte pou¾ít ¾ádné OpenGL funkce ani funkce z jiného 3D API. A pokud je navíc pátek a termín odevzdání máte definován na pondìlí, jedná se o docela vra¾ednou kombinaci... Kompletní popis programu naleznete na stránkách <?OdkazBlank('http://kmlinux.fjfi.cvut.cz/~chalupec/pogr/galerie-2005/haiduk-adam.php', 'Fakulty jaderné');?>, ÈVUT Praha.</p>
</div>


<div class="object">
<img class="img_prog" src="images/programy/paleta.png" alt="Barevná paleta" />
<h3>Barevná paleta</h3>
<div>Autor: ssil <?VypisEmail('ssil@centrum.cz');?></div>
<div>Pøidáno: 08.09.2005</div>
<div>Licence: ???</div>
<div>Download: <?OdkazDown('download/programy/paleta.tar.gz');?> (se zdroj. kódy)</div>
<div>Programovací jazyk: C, SDL, OpenGL</div>
<p>Program vykresluje barevnou paletu. Její odstín se dá zmìnit zvolením barevné slo¾ky pomocí kláves r, g, b a následným stisknutím ¹ipky doleva/doprava. Díky pøenositelné knihovnì SDL lze po kompilaci spustit pod MS Windows, GNU/Linuxem a dal¹ími operaènímy systémy.</p>
</div>


<div class="object">
<img class="img_prog" src="images/programy/tunneler.png" alt="Tunneler" />
<h3>Tunneler</h3>
<div>Autor:Vladimír Hoffman, Gabriel Gécy <?VypisEmail('gabez@zoznam.sk');?></div>
<div>Pøidáno: 02.03.2005</div>
<div>Licence: ???</div>
<div>Download: <?OdkazDown('download/programy/tunneler.zip');?> (se zdroj. kódy)</div>
<div>Programovací jazyk: C/C++, Win32 API</div>
<p>Hra je urcena hlavne pre dvoch hracov, ale je implementovane aj AI, takze si moze clovek zahrat aj sam. Pravidla su prebrate zo starej dosovky Tuneller (nasa sa vola Tunneler, pretoze nam to tak napisal zadavatel :)) ). Na nahodne vygenerovanej mape su nahodne umiestnene dva domceky, kde zacinaju jednotlivi hraci/UI. Tu si mozu tanky doplnat zdravie aj energiu. Vsade okolo je zem, ktorou si treba razit tunely, co stoji istu energiu. Ta sa spotrebovava aj na pohyb a na strielanie. Ulohou je rostrielat (ako inak :) ) protivnika. Hra sa na 1 - 10 vitazstiev podla nastavenia, standardne 3. Blizsie detaily zistite pocas hry :)).</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/winter_wars2.jpg" alt="Winter Wars 2" />
<h3>Winter Wars 2</h3>
<div>Autor: Michal Bubnár <?VypisEmail('michalbb@centrum.sk');?></div>
<div>Web: <?OdkazBlank('http://www.mgbsoft.tym.sk/');?></div>
<div>Pøidáno: 13.12.2004</div>
<div>Licence: freeware open source</div>
<div>Download: <?OdkazDown('download/programy/winter_wars2.rar');?></div>
<div>Programovací jazyk: Visual C++</div>
<p>Tato hra je FPS støíleèkou se zdrojovými kódy. Má spoustu chyb a potøebuje opravit, ale OpenGL a 3D matematiku neumím zrovna do detailù. Pokud vás napadají nìjaké dotazy ohlednì vytváøení, komentáøe, návrhy a podobnì, prosím po¹lete mi je.</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/2dcollisions.jpg" alt="Collisions in 2D world" />
<h3>Collisions in 2D world</h3>
<div>Autor: Michal Bubnár <?VypisEmail('michalbb@centrum.sk');?></div>
<div>Web: <?OdkazBlank('http://www.mgbsoft.tym.sk/');?></div>
<div>Pøidáno: 24.11.2004</div>
<div>Licence: freeware open source</div>
<div>Download: <?OdkazDown('download/programy/2dcollisions.rar');?></div>
<div>Programovací jazyk: Visual C++</div>
<p>Tento program ukazuje, jak detekovat kolize ve 2D svìtì. Výpoèty nejsou úplnì pøesné, ale dostaèující pro herní vývojáøe, kteøí s programováním právì zaèínají. V archivu se nachází mimojiné i soubor Collisions.doc, který popisuje trochu teorie okolo.</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/hlsaver.png" alt="hlsaver" />
<h3>hlsaver</h3>
<div>Autor: Johny <?VypisEmail('johny@ammo.sk');?></div>
<div>Web: <?OdkazBlank('http://www.ammo.sk/');?></div>
<div>Pøidáno: 14.08.2004</div>
<div>Licence: use like you want ;o)</div>
<div>Download: <?OdkazDown('download/programy/hlsaver.zip');?></div>
<div>Programovací jazyk: bez zdrojových kódù</div>
<p>Hezký program (demíèko), &quot;ktory kopiruje uvodnu miestnost z half-lifu, tu recepciu, kam pride vlak. Su tam 2 ludia, barney a scientist,... ktory nieco robia pri kompe, kamera lieta okolo. a to je vsetko ;o)&quot;.</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/sdl_extensions.jpg" alt="SDL extension" />
<h3>SDL extension</h3>
<div>Autor: Radomír Vrána <?VypisEmail('rvalien@c-box.cz');?></div>
<div>Pøidáno: 17.03.2004</div>
<div>Licence: ???</div>
<div>Download: <?OdkazDown('download/programy/sdl_extension.rar');?></div>
<div>Programovací jazyk: Visual C++, SDL, OpenGL</div>
<p>Jednoduché demo, které ukazuje, jak se v programu zalo¾eném na multiplatformní knihovnì SDL pou¾ívají OpenGL roz¹íøení (extensions). Kód vykresluje rotující krychli, na kterou jsou pomocí multitexturingového roz¹íøení (GL_ARB_multitexture) namapované dvì textury najednou.</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/projekt_dialog.jpg" alt="Projekt - dialog" />
<h3>Projekt - Dialog</h3>
<div>Autor: Max Zelený <?VypisEmail('prog.max@seznam.cz');?></div>
<div>Pøidáno: 27.06.2003</div>
<div>Licence: ???</div>
<div>Download: <?OdkazDown('download/programy/projekt_dialog.rar');?></div>
<div>Programovací jazyk: Visual C++, MFC, OpenGL</div>
<p>Existují dvì mo¾nosti, jak vykreslovat OpenGL scénu do dialogového okna. Dá se buï kreslit do prvku &quot;Picture&quot;, který je umístìn na dialogu nebo vytvoøení dìtského okna. Program implementuje druhou mo¾nost, proto¾e je viditelnì rychlej¹í. Pøekreslení je zaji¹tìno tlaèítkem, ale jednoduchou modifikací smyèky zpráv  se mù¾e volat periodicky.</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/svetla.jpg" alt="Svìtla" />
<h3>Svìtla</h3>
<div>Autor: Petr Vanìèek <?VypisEmail('pet@kiv.zcu.cz');?></div>
<div>Web: <?OdkazBlank('http://herakles.zcu.cz/~pet/');?></div>
<div>Pøidáno: 08.05.2003</div>
<div>Licence: ???</div>
<div>Download: <?OdkazDown('download/programy/svetla.rar');?></div>
<div>Programovací jazyk: Delphi, OpenGL</div>
<p>Jak u¾ název napovídá, jedná se o demonstraci svìtel v OpenGL. Nasvìcuje se vlnící se voda, která vypadá opravdu stylovì. Ale to není v¹e! Pomocí ovládacích prvkù mù¾ete pøi bìhu programu mìnit jednotlivé parametry od drátìného modelu pøes intenzitu, pozici, smìr, barvu... a¾ po tvar svìtla - do v¹ech smìrù, spot, baterka... Ihned vidíte zmìnu. <span class="warning">Je¹tì to sice není finální verze</span>, ale stáhnutím a vyzkou¹ením urèitì neudìláte chybu!</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/vertex_arrays.jpg" alt="Vertex Arrays" />
<h3>Vertex Arrays</h3>
<div>Autor: Petr Vanìèek <?VypisEmail('pet@kiv.zcu.cz');?></div>
<div>Web: <?OdkazBlank('http://herakles.zcu.cz/~pet/');?></div>
<div>Pøidáno: 08.05.2003</div>
<div>Licence: ???</div>
<div>Download: <?OdkazDown('download/programy/vertex_arrays.rar');?></div>
<div>Programovací jazyk: Delphi, OpenGL</div>
<p>Tento program by se asi dal nejvýsti¾nìji popsat jako výukový. Uprostøed je vyrenderován kreslený objekt a po stranách se nacházejí okýnka s popisem, jak co pracuje. Perfektní nápad.</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/3d-engine.jpg" alt="FLASH software - 3D Engine" />
<h3>FLASH software - 3D Engine</h3>
<div>Autor: Pavel Barák <?VypisEmail('barak@flashsoftware.cz');?></div>
<div>Web: <?OdkazBlank('http://www.flashsoftware.cz/');?></div>
<div>Pøidáno: 25.04.2003</div>
<div>Licence: ???</div>
<div>Download: <?OdkazDown('download/programy/3d-engine.rar');?></div>
<div>Programovací jazyk: Visual C++, OpenGL</div>
<p>Upravená <a href="tut_10.php">10. lekce</a> o detekce kolizí se stìnami, roz¹íøené ovládání a nìkolik textur. "... a i dal¹í moje úpravy jsou fakt dost krizové, nicménì mohlo by to nìkomu pomoci..."</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/texture_font_creator.gif" alt="Texture Font Creator" />
<h3>Texture Font Creator</h3>
<div>Autor: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?></div>
<div>Web: <?OdkazBlank('http://woq.nipax.cz/');?>, <?OdkazBlank('http://nehe.ceske-hry.cz/');?></div>
<div>Pøidáno: 17.04.2003</div>
<div>Licence: GNU GPL</div>
<div>Download: <?OdkazDown('download/programy/texture_font_creator.rar');?></div>
<div>Programovací jazyk: MS Visual C++ 6.0, MFC</div>
<p>Chcete pou¾ívat texturové fonty ze <a href="tut_17.php">17. lekce</a>, ale nemáte ¾ádný èeský? Pomocí tohoto jednoduchého programu mù¾ete vytvoøit (+ulo¾it) bitmapu na bázi jakéhokoli fontu nainstalovaného v systému. Generuje se plný ASCII kód - v¹ech 256 znakù.</p>
</div>

<div class="object">
<img class="img_prog" src="images/programy/komety.jpg" alt="Komety" />
<h3>Komety</h3>
<div>Autor: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></div>
<div>Web: <?OdkazBlank('http://farao.czweb.org/');?></div>
<div>Pøidáno: 17.04.2003</div>
<div>Licence: GNU GPL</div>
<div>Download: <?OdkazDown('download/programy/komety.rar');?></div>
<div>Programovací jazyk: MS Visual C++ 6.0, MFC, OpenGL</div>
<p>Typická ukázka pou¾ití efektních èásticových systémù. Osobnì doporuèuji o malièko rychlej¹í poèítaè ne¾ právì máte, a» u¾ máte jakýkoli :-)</p>
</div>


<h3>V¹echny programy by mìly být v poøádku, ale...</h3>

<p>CZ NeHe OpenGL neruèí za jakékoli ¹kody zpùsobené nefunkèností programù, jejich chybami nebo chybovými stavy nebo úmyslnými zámìry jejich autorù. Nejsem schopen v¹e testovat a kontrolovat zdrojové kódy, pokud jsou pøipojeny k programu. V pøípadì jakýchkoli dotazù, chyb nebo oznámení kontaktujte konkrétního autora, který daný program naprogramoval.</p>

<p>Pokud jste mezi programy na¹li svùj program, který mi byl poslán pod jiným jménem a tudí¾ byl ukraden, prosím upozornìte mì <?VypisEmail('woq@email.cz');?>. Ihned ho odstraním.</p>

<?
include 'p_end.php';
?>
