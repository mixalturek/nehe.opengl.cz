<?
$g_title = 'CZ NeHe OpenGL - FAQ: Èasto kladené dotazy';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>FAQ: Èasto kladené dotazy</h1>

<p class="nadpis_clanku">Na emailu se mi nìkteré, vìt¹inou zaèáteènické, dotazy neustále opakují, jako pøíklad lze uvést problémy s knihovnou GLAUX a symbolickou konstantou CDS_FULLSCREEN v Dev-C++. Doufám, ¾e tato stránka trochu sní¾í zatí¾ení, ale pokud si stále nevíte rady, nebojte se mi napsat. Doufám, ¾e nebude vadit, kdy¾ sem umístím i ten vá¹ problém.</p>


<h3>Co je OpenGL?</h3>
<p>Znalý programátor by vám s nejvìt¹í pravdìpodobností odpovìdìl, ¾e se jedná o standard popisující API (aplikaèní programové rozhraní) mezi programem a hardwarem grafické karty. Já se pokusím o trochu pochopitelnìj¹í vysvìtlení. Urèitì jste u¾ vidìli nìjakou 3D akèní hru (napø. Quake, Unreal Tournament...), ve které se lze pohybovat do jakéhokoli smìru (nahoru, dolù, doleva, doprava, dopøedu, dozadu). OpenGL, velice zjednodu¹enì øeèeno, zaji¹»uje rendering (vykreslení) tìchto 3D objektù na 2D monitor a dále se stará o spoustu dal¹ích vìcí, které byste ale na tomto místì stejnì nepochopili...</p>


<h3>Chci se nauèit programovat v OpenGL, ale nevím, kde zaèít.</h3>
<p>Zaènìte napøíklad u èlánku <?OdkazWeb('cl_gl_zacinam', 'Pomoc, zaèínám');?>, urèitì v nìm naleznete odpovìï.</p>


<h3>Vykreslování je pomalé a trhané (okolo 1 FPS a ménì)</h3>
<p>OpenGL s nejvìt¹í pravdìpodobností nebì¾í na hardwaru grafické karty, ale emuluje se softwarovì. Zkuste nainstalovat/pøeinstalovat ovladaèe grafické karty. Mimochodem, nespoléhejte na to, ¾e jsou v systému, kdy¾ jde nastavit libovolné rozli¹ení a barevnou hloubku (ano, i ve Win XP). <?OdkazWeb('cl_gl_linux', 'Instalace v Linuxu');?>.</p>


<h3>Nefunguje mi blending, mapování textur atd.</h3>
<p>Nezapomnìli jste ho zapnout pomocí funkce glEnable()? Grrrr!!! Klid, mì se to stává taky :-)</p>


<h3>Lze zadávat hodnoty barev celými èísly?</h3>
<p>Ano, místo glColor3<b>f</b>() - hodnoty od 0.0f do 1.0f - zavolejte glColor3<b>ub</b>(). <b>U</b>nsigned <b>B</b>yte mù¾e nabývat hodnot od 0 do 255, na tato èísla jste asi zvyklí více a pravdìpodobnì to bude i rychlej¹í (o málo, ale pøece :-)...</p>


<h3>Co znamenají èísla 0.0f a 1.0f u funkce glTexCoord2f(x, y)?</h3>
<p>Tato dvì èísla oznaèují x, y pozici na textuøe - 0.0f je levý/spodní okraj, 1.0f urèuje pravý/horní okraj a èísla mezi specifikují jinou pozici (napø. 0.5f polovina textury). Nemusí se v¹ak zadávat pouze hodnoty v intervalu od 0.0f do 1.0f. Pøedání vìt¹ích èísel vytvoøí &quot;dla¾dicový&quot; efekt - jako kdy¾ si dáte v OS na plochu obrázek 50x50 pixelù vedle sebe. Napø. èíslo 10.0f zpùsobí namapování dané textury 10x pøes celou ¹íøku polygonu.</p>


<h3>Jak vykreslit objekt na urèitou pozici v oknì a o urèité velikosti (mìøeno v pixelech)?</h3>
<p>Problémem je, ¾e i kdybychom ignorovali natoèení scény a podobné vìci naprosto nepøenositelné ze 3D do 2D, perspektivní korekce zpùsobí, ¾e se bude velikost objektù v závislosti na hloubce mìnit. Samozøejmì lze pou¾ít rùzné pøepoèítávací funkce (gluProject() a <?OdkazWeb('cl_gl_gluunproject', 'gluUnProject()');?>), které konvertují pixelové souøadnice v oknì na OpenGL jednotky a naopak, ale a¾ budete dìlat v OpenGL trochu déle, zjistíte, ¾e ve vìt¹inì pøípadù nic takového vùbec nepotøebujete.</p>

<p>Pokusím se o modelovou situaci. Programujeme klasický 3D automobilový simulátor, ve kterém se mù¾e hráè dívat z pohledu øidièe, z kamery umístìné nahoøe za autem, popø. z dynamicky pøesunované kamery vedle cesty. Budeme zmen¹ovat/zvìt¹ovat v¹echny objekty podle zvoleného pohledu a vymý¹let 3 rùzné funkce pro rendering? Samozøejmì, ¾e ne. Hlavní trik spoèívá v tom, ¾e ve v¹ech tøech pohledech vykreslíme v¹e naprosto stejnì, ale pøed samotným vykreslením zmìníme pozici kamery a úhel pohledu - nic víc, nic míò. Øeknìme, ¾e závodní dráha zabírá plochu 1000 jednotek s maximálním pøevý¹ením 30 jednotek. Stromy jsou vysoké 3 a¾ 5 jednotek a modely závodních aut mají délku 1 jednotku - nejsou dùle¾ité konkrétní velikosti, ale pomìr mezi nimi. S tìmito znalostmi mù¾eme bez problémù vykreslit scénu, o nìjaké pohledy se vùbec nemusíme starat. Zmìnu pohledu pak provedeme zavoláním funkcí glTranslatef(), glRotatef(), popø. gluLookAt() pøed samotným vykreslením. Doufám, ¾e je to pochopitelné.</p>

<p>Abych se ale vrátil k pùvodní otázce, perspektivní zmen¹ování objektù lze eliminovat pou¾itím pravoúhlé (=kolmé) projekce. S výhodou se jí vyu¾ívá napø. u <?OdkazWeb('tut_17', 'výpisu textù');?> (u nich najdete i implementaci v programu). Postup spoèívá v nahrazení gluPerspective() za funkci glOrtho(), které se pøedávají po¾adované rozmìry 2D scény. Pøi pøesunech objektu do hloubky budou jeho rozmìry pøi pravoúhlé projekci v¾dy stejné. A je¹tì malá poznámka na závìr: pokud nepøedáte glOrtho() aktuální rozmìry viewportu, ale konstantní hodnoty napø. 1000x1000, pøi zmìnì jeho velikosti se relativní poloha vykreslovaného objektu nezmìní. Z toho vyplývá, ¾e po zvìt¹ení okna ze 640x480 na 1600x1200 bude objekt &quot;uprostøed&quot; opravdu &quot;uprostøed&quot; a ne v levé èásti.</p>


<h3>OpenGL &amp; 2D grafika</h3>
<p>OpenGL 2D grafiku pøímo nepodporuje, ale místo 3D glVertex3f(x, y, z) lze pou¾ívat její 2D variantu glVertex2f(x, y) - za z souøadnici se automaticky dosadí 0, ale na pozadí se defakto jedná stále o 3D. Pokud budou vadit &quot;bezrozmìrné&quot; OpenGL jednotky a perspektiva, je mo¾né se pøepnout do pravoúhlé projekce (NeHe Tutoriály <?OdkazWeb('tut_17', '17');?>, <?OdkazWeb('tut_21', '21');?>, <?OdkazWeb('tut_24', '24');?> atd.), kde lze nastavit zadávání souøadnic v pixelech.</p>

<p>Blitter a spol., co vím, OpenGL pøímo nepodporuje (update: glDrawPixels(), glReadPixels(), glCopyPixels() atd.), ale naprogramovat si ho není zase a¾ tak tì¾ké (NeHe <?OdkazWeb('tut_29', '29');?>). Abych to shrnul, s klasickou 2D grafikou je v OpenGL trochu problém, proto¾e bez grafického akcelerátoru se hra (vìt¹inou se jedná o hry :) nepohne z místa, nicménì samo o sobì má také spoustu vychytávek. Napøíklad perfektnì vypadají klasické 2D rovinné hry, ve kterých jsou v¹echny objekty i prostøedí trojrozmìrné (ale na plo¹e, v konstantní hloubce).</p>


<h3>Nejde mi zkompilovat kód <?OdkazWeb('tut_06', '6. NeHe Tutoriálu');?> a vy¹¹í</h3>
<p>V tomto tutoriálu se zaèínají pou¾ívat textury, pro jejich¾ nahrávání je potøeba knihovna GLAUX. Tato knihovna je docela zastaralá, a proto se k nìkterým kompilátorùm standardnì nepøibaluje a obecnì se ji nedoporuèuje pou¾ívat. Asi nejjednodu¹¹í øe¹ení je stáhnout si ji <?OdkazWeb('download', 'v downloadu');?>, dále mù¾ete zkusit nahrát obrázek jinou knihovnou (napø. <?OdkazWeb('cl_sdl_image', 'SDL_Image');?> - nutný projekt v SDL a ne ve Win API) nebo si napsat vlastní nahrávací kód (napø. formát TGA byl vysvìtlen v <?OdkazWeb('tut_24', 'tut. 24');?>, <?OdkazWeb('tut_33', 'tut. 33');?>).</p>


<h3>V Dev-C++ nejde zkompilovat <b>ChangeDisplaySettings(&amp;dmScreenSettings, CDS_FULLSCREEN)</b>.</h3>
<p>Nìkteré kompilátory a vývojová prostøedí, mezi nimi právì Dev-C++, symbolickou konstantu CDS_FULLSCREEN automaticky nedefinují. Problém vyøe¹íte ruèním nadefinováním, mìlo by staèit nìco takového:</p>

<p class="src0">#ifndef CDS_FULLSCREEN<span class="kom">// Nìkteré kompilátory nedefinují CDS_FULLSCREEN</span></p>
<p class="src1">#define CDS_FULLSCREEN 4<span class="kom">// Ruèní nadefinování</span></p>
<p class="src0">#endif</p>


<h3>Jak do programu nahrát objekt ze 3D Studia Max (.3ds)?</h3>
<p>Zkuste èlánek <?OdkazWeb('cl_gl_3ds', 'Naèítání .3DS modelù');?>...</p>

<!--
<h3></h3>
<p class="src"></p>
-->

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?>, 15.08.2004</p>

<?
include 'p_end.php';
?>
