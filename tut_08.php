<?
$g_title = 'CZ NeHe OpenGL - Lekce 8 - Blending';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(8);?>

<h1>Lekce 8 - Blending</h1>

<p class="nadpis_clanku">Dal�� typ speci�ln�ho efektu v OpenGL je blending, neboli pr�hlednost. Kombinace pixel� je ur�ena alfa hodnotou barvy a pou�itou funkc�. Nab�v�-li alfa 0.0f, materi�l zpr�hledn�, hodnota 1.0f p�in�� prav� opak.</p>

<h3>Rovnice blendingu</h3>

<p>Nem�te r�di matematiku a chcete vid�t, jak se pou��v� pr�hlednost prakticky, pak p�esko�te tuto ��st. Pro n�koho m��e b�t nepochopiteln�.</p>

<p class="src0"><span class="kom">(Rs Sr + Rd Dr, Gs Sg + Gd Dg, Bs Sb + Bd Db, As Sa + Ad Da)</span></p>

<p>OpenGL vypo��t� v�sledek blendingu dvou pixel� z p�edchoz� rovnice. 's' a 'r' p�edstavuj� zdrojov� a c�lov� pixel. 'S' a 'D' jsou �initel� blendingu. Tyto hodnoty ur�uj� jak moc budou pixely pr�hledn�. V�t�ina oby�ejn�ch hodnot pro S a D jsou (As, As, As, As) (AKA zdrojov� alfa) pro S a (1, 1, 1, 1) - (As, As, As, As) (AKA jedna minus zdrojov� alfa) pro D. Rovnice bude vypadat takto:</p>

<p class="src0"><span class="kom">(Rs As + Rd (1 - As), Gs As + Gd (1 - As), Bs As + Bd (1 - As), As As + Ad (1 - As))</span></p>

<p>Nyn� u� se budeme v�novat praktick�mu k�du. Pou�ijeme k�d z lekce 7.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Priv�tn� GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trval� Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na�eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>Blending se v OpenGL zap�n� stejn�, jako v�echno ostatn�. Nastav�me jeho parametry a vypneme depth buffer, jinak by se objekty za pr�hledn�mi polygony nevykreslily. To sice nen� spr�vn� cesta k blendingu, ale ve v�t�in� jednoduch�ch projekt� bude fungovat. Spr�vn� cesta k vykreslen� pr�hledn�ch (alfa < 1.0) polygon� je obr�cen� depth bufferu (nejvzd�len�j�� objekty se vykresluj� prvn�). Nap�.: uva�ujme polygon 1 jako vzd�len�j�� od pozorovatele. Spr�vn� by m�l b�t tedy vykreslen polygon 2 a a� po n�m polygon 1. Kdy� se na to pod�v�te, jako ve skute�nosti, v�echno sv�tlo se m�s� za t�mito dv�ma polygony (jsou-li pr�hledn�), mus� se vykreslit polygon 2 prvn� a potom polygon 1. Rad�ji �a�te pr�hledn� objekty podle hloubky a kreslete je a� po vykreslen� cel� sc�ny se zapnut�m depth bufferem, jinak m��ete dostat �patn� v�sledky. V�m �e je to t�k�, ale je to jedin� spr�vn� cesta.</p>

<p class="src0">bool light;<span class="kom">// Sv�tlo ON/OFF</span></p>
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
<p class="src0">GLfloat LightAmbient[]= { 0.5f, 0.5f, 0.5f, 1.0f };<span class="kom">// Okoln� sv�tlo</span></p>
<p class="src0">GLfloat LightDiffuse[]= { 1.0f, 1.0f, 1.0f, 1.0f };<span class="kom">// P��m� sv�tlo</span></p>
<p class="src0">GLfloat LightPosition[]= { 0.0f, 0.0f, 2.0f, 1.0f };<span class="kom">// Pozice sv�tla</span></p>
<p class="src"></p>
<p class="src0">GLuint filter;<span class="kom">// Pou�it� texturov� filtr</span></p>
<p class="src0">GLuint texture[3];<span class="kom">// Ukl�d� 3 textury</span></p>

<p>Posuneme se dol� na LoadGLTextures() a zm�n�me jm�no textury.</p>

<p class="src0"><span class="kom">// Funkce LoadGLTextures()</span></p>
<p class="src1">if(TextureImage[0]=LoadBMP("Data/Glass.bmp"))<span class="kom">// Loading bitmapy</span></p>

<p>Dal�� ��dky p�id�me do InitGL(). Nastaven� jimi objekty na pln� jas a 50% alfu (pr�hlednost). To znamen�, �e kdy� bude blending zapnut, objekt bude z 50% pr�hledn�. Alfa hodnota 0.0 je �pln� pr�hlednost, 1.0 je opak. Druh� ��dek nastav� typ blendingu.</p>

<p class="src0"><span class="kom">// Funkce InitGL()</span></p>
<p class="src1">glColor4f(1.0f,1.0f,1.0f,0.5f);<span class="kom">// Pln� jas, 50% alfa</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE);<span class="kom">// Funkce blendingu pro pr�svitnost zalo�en� na hodnot� alfa</span></p>

<p>Pod�vejte se na n�sleduj�c� k�d, je um�st�n na konci lekce ve funkci WinMain().</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if (keys['B'] &amp;&amp; !bp)<span class="kom">// Kl�vesa B - zapne blending</span></p>
<p class="src4">{</p>
<p class="src5">bp=TRUE;</p>
<p class="src5">blend = !blend;</p>
<p class="src"></p>
<p class="src5">if(blend)</p>
<p class="src5">{</p>
<p class="src6">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src6">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne hloubkov� testov�n�</span></p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">glDisable(GL_BLEND);<span class="kom">// Vypne blending</span></p>
<p class="src6">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkov� testov�n�</span></p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys['B'])<span class="kom">// Uvoln�n� B</span></p>
<p class="src4">{</p>
<p class="src5">bp=FALSE;</p>
<p class="src4">}</p>

<p>Nen� to jednoduch�? Sta�� zapnout blending, vypnout hloubkov� testov�n� a zavolat funkci glColor4f(r,g,b,a) - v�e, co nakresl�me bude pr�hledn�.</p>

<p>Jak ale nastav�me barvu pou�itou v textu�e? Jednodu�e, v modulated texture modu, ka�d� pixel mapovan� texturou je n�sobkem aktu�ln� barvy. Kdy� je kreslen� barva (0.5, 0.6, 0.4), n�sob�me barvou a p�ed�me (0.5, 0.6, 0.4, 0.2) (alfa se rovn� 1.0, nen�-li ur�ena).</p>

<h3>Alfa z textury</h3>

<p>Alfa hodnota pou�it� pro pr�hlednost m��e b�t p�e�tena z textury pouze jako barva. To se d�l� tak, �e se p�ed� alfa do obr�zku p�i nahr�v�n� a pot� se ve funkci glTexImage2D() pou�ije pro barevn� form�t GL_RGBA.</p>

<p class="autor">napsal: Tom Stanis <?VypisEmail('stanis@cs.wisc.edu');?><br />
p�elo�il: Ji�� Rajsk� - RAJSOFT junior <?VypisEmail('predator.jr@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson08.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson08_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson08.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson08.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson08.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson08.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson08.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson08.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson08.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson08.zip">GLUT</a> k�d t�to lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson08.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson08.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson08.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson08.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:pepijn.vaneeckhoudt@luciad.com">Pepijn Van Eeckhoudt</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson08.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson08.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson08.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson08.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson08.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson08.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson08.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson08.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson08.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson08.zip">Solaris</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson08.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson08.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson08.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(8);?>
<?FceNeHeOkolniLekce(8);?>

<?
include 'p_end.php';
?>
