<?
$g_title = 'CZ NeHe OpenGL - Lekce 8 - Blending';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(8);?>

<h1>Lekce 8 - Blending</h1>

<p class="nadpis_clanku">Dal¹í typ speciálního efektu v OpenGL je blending, neboli prùhlednost. Kombinace pixelù je urèena alfa hodnotou barvy a pou¾itou funkcí. Nabývá-li alfa 0.0f, materiál zprùhlední, hodnota 1.0f pøiná¹í pravý opak.</p>

<h3>Rovnice blendingu</h3>

<p>Nemáte rádi matematiku a chcete vidìt, jak se pou¾ívá prùhlednost prakticky, pak pøeskoète tuto èást. Pro nìkoho mù¾e být nepochopitelná.</p>

<p class="src0"><span class="kom">(Rs Sr + Rd Dr, Gs Sg + Gd Dg, Bs Sb + Bd Db, As Sa + Ad Da)</span></p>

<p>OpenGL vypoèítá výsledek blendingu dvou pixelù z pøedchozí rovnice. 's' a 'r' pøedstavují zdrojový a cílový pixel. 'S' a 'D' jsou èinitelé blendingu. Tyto hodnoty urèují jak moc budou pixely prùhledné. Vìt¹ina obyèejných hodnot pro S a D jsou (As, As, As, As) (AKA zdrojová alfa) pro S a (1, 1, 1, 1) - (As, As, As, As) (AKA jedna minus zdrojová alfa) pro D. Rovnice bude vypadat takto:</p>

<p class="src0"><span class="kom">(Rs As + Rd (1 - As), Gs As + Gd (1 - As), Bs As + Bd (1 - As), As As + Ad (1 - As))</span></p>

<p>Nyní u¾ se budeme vìnovat praktickému kódu. Pou¾ijeme kód z lekce 7.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavièkový soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavièkový soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Privátní GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trvalý Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na¹eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>Blending se v OpenGL zapíná stejnì, jako v¹echno ostatní. Nastavíme jeho parametry a vypneme depth buffer, jinak by se objekty za prùhlednými polygony nevykreslily. To sice není správná cesta k blendingu, ale ve vìt¹inì jednoduchých projektù bude fungovat. Správná cesta k vykreslení prùhledných (alfa < 1.0) polygonù je obrácení depth bufferu (nejvzdálenìj¹í objekty se vykreslují první). Napø.: uva¾ujme polygon 1 jako vzdálenìj¹í od pozorovatele. Správnì by mìl být tedy vykreslen polygon 2 a a¾ po nìm polygon 1. Kdy¾ se na to podíváte, jako ve skuteènosti, v¹echno svìtlo se mísí za tìmito dvìma polygony (jsou-li prùhledné), musí se vykreslit polygon 2 první a potom polygon 1. Radìji øaïte prùhledné objekty podle hloubky a kreslete je a¾ po vykreslení celé scény se zapnutým depth bufferem, jinak mù¾ete dostat ¹patné výsledky. Vím ¾e je to tì¾ké, ale je to jediná správná cesta.</p>

<p class="src0">bool light;<span class="kom">// Svìtlo ON/OFF</span></p>
<p class="src0">bool blend;<span class="kom">// Blending OFF/ON</span></p>
<p class="src0">bool lp;<span class="kom">// Stisknuto L?</span></p>
<p class="src0">bool fp;<span class="kom">// Stisknuto F?</span></p>
<p class="src0">bool bp;<span class="kom">// Stisknuto B?</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot;<span class="kom">// X Rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y Rotace</span></p>
<p class="src0">GLfloat xspeed;<span class="kom">// Rychlost x rotace</span></p>
<p class="src0">GLfloat yspeed;<span class="kom">// Rychlost y rotace</span></p>
<p class="src0">GLfloat z=-5.0f;<span class="kom">// Hloubka v obrazovce</span></p>
<p class="src"></p>
<p class="src0">GLfloat LightAmbient[]= { 0.5f, 0.5f, 0.5f, 1.0f };<span class="kom">// Okolní svìtlo</span></p>
<p class="src0">GLfloat LightDiffuse[]= { 1.0f, 1.0f, 1.0f, 1.0f };<span class="kom">// Pøímé svìtlo</span></p>
<p class="src0">GLfloat LightPosition[]= { 0.0f, 0.0f, 2.0f, 1.0f };<span class="kom">// Pozice svìtla</span></p>
<p class="src"></p>
<p class="src0">GLuint filter;<span class="kom">// Pou¾itý texturový filtr</span></p>
<p class="src0">GLuint texture[3];<span class="kom">// Ukládá 3 textury</span></p>

<p>Posuneme se dolù na LoadGLTextures() a zmìníme jméno textury.</p>

<p class="src0"><span class="kom">// Funkce LoadGLTextures()</span></p>
<p class="src1">if(TextureImage[0]=LoadBMP("Data/Glass.bmp"))<span class="kom">// Loading bitmapy</span></p>

<p>Dal¹í øádky pøidáme do InitGL(). Nastavení jimi objekty na plný jas a 50% alfu (prùhlednost). To znamená, ¾e kdy¾ bude blending zapnut, objekt bude z 50% prùhledný. Alfa hodnota 0.0 je úplná prùhlednost, 1.0 je opak. Druhý øádek nastaví typ blendingu.</p>

<p class="src0"><span class="kom">// Funkce InitGL()</span></p>
<p class="src1">glColor4f(1.0f,1.0f,1.0f,0.5f);<span class="kom">// Plný jas, 50% alfa</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE);<span class="kom">// Funkce blendingu pro prùsvitnost zalo¾ená na hodnotì alfa</span></p>

<p>Podívejte se na následující kód, je umístìn na konci lekce ve funkci WinMain().</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if (keys['B'] &amp;&amp; !bp)<span class="kom">// Klávesa B - zapne blending</span></p>
<p class="src4">{</p>
<p class="src5">bp=TRUE;</p>
<p class="src5">blend = !blend;</p>
<p class="src"></p>
<p class="src5">if(blend)</p>
<p class="src5">{</p>
<p class="src6">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src6">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne hloubkové testování</span></p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">glDisable(GL_BLEND);<span class="kom">// Vypne blending</span></p>
<p class="src6">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkové testování</span></p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys['B'])<span class="kom">// Uvolnìní B</span></p>
<p class="src4">{</p>
<p class="src5">bp=FALSE;</p>
<p class="src4">}</p>

<p>Není to jednoduché? Staèí zapnout blending, vypnout hloubkové testování a zavolat funkci glColor4f(r,g,b,a) - v¹e, co nakreslíme bude prùhledné.</p>

<p>Jak ale nastavíme barvu pou¾itou v textuøe? Jednodu¹e, v modulated texture modu, ka¾dý pixel mapovaný texturou je násobkem aktuální barvy. Kdy¾ je kreslená barva (0.5, 0.6, 0.4), násobíme barvou a pøedáme (0.5, 0.6, 0.4, 0.2) (alfa se rovná 1.0, není-li urèena).</p>

<h3>Alfa z textury</h3>

<p>Alfa hodnota pou¾itá pro prùhlednost mù¾e být pøeètena z textury pouze jako barva. To se dìlá tak, ¾e se pøedá alfa do obrázku pøi nahrávání a poté se ve funkci glTexImage2D() pou¾ije pro barevný formát GL_RGBA.</p>

<p class="autor">napsal: Tom Stanis <?VypisEmail('stanis@cs.wisc.edu');?><br />
pøelo¾il: Jiøí Rajský - RAJSOFT junior <?VypisEmail('predator.jr@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson08.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson08_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson08.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson08.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson08.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson08.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson08.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson08.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson08.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson08.zip">GLUT</a> kód této lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson08.zip">Irix</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson08.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson08.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson08.jar">JoGL</a> kód této lekce. ( <a href="mailto:pepijn.vaneeckhoudt@luciad.com">Pepijn Van Eeckhoudt</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson08.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson08.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson08.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson08.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson08.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson08.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson08.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson08.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson08.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson08.zip">Solaris</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson08.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson08.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson08.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(8);?>
<?FceNeHeOkolniLekce(8);?>

<?
include 'p_end.php';
?>
