<?
$g_title = 'CZ NeHe OpenGL - Lekce 16 - Mlha';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(16);?>

<h1>Lekce 16 - Mlha</h1>

<p class="nadpis_clanku">Tato lekce roz¹iøuje pou¾itím mlhy lekci 7. Nauèíte se pou¾ívat tøí rùzných filtrù, mìnit barvu a nastavit oblast pùsobení mlhy (v hloubce). Velmi jednoduchý a "efektní" efekt.</p>

<p>Na zaèátek programu, za v¹echna #include, pøidáme deklarace nových promìnných. Gp pro zji¹tìní stisku klávesy G, ve filte" najdeme èíslo 0 a¾ 2, specifikující právì pou¾ívaný texturový filtr. V poli fogMode[] ukládáme tøi rùzné typy mlhy. Fogfilter urèuje právì pou¾ívanou mlhu. Ve fogColor je ulo¾ena ¹edá barva.</p>

<p class="src0">bool gp;<span class="kom">// G stisknuto?</span></p>
<p class="src0">GLuint filter;<span class="kom">// Urèuje texturový filtr</span></p>
<p class="src0">GLuint fogMode[]= { GL_EXP, GL_EXP2, GL_LINEAR };<span class="kom">// Tøi typy mlhy</span></p>
<p class="src0">GLuint fogfilter= 0;<span class="kom">// Která mlha se pou¾ívá</span></p>
<p class="src0">GLfloat fogColor[4]= {0.5f, 0.5f, 0.5f, 1.0f};<span class="kom">// Barva mlhy</span></p>

<p>Pøesuneme se do funkce InitGL(). glClearColor(r,g,b,a) jsme v¾dy pou¾ívali pro nastavení èerného pozadí. Tentokrát udìláme malou zmìnu - pou¾ijeme ¹edé pozadí (barvu mlhy), proto¾e vypadá lépe.</p>

<p class="src0"><span class="kom">// Funkce InitGL()</span></p>
<p class="src1">glClearColor(0.5f,0.5f,0.5f,1.0f);<span class="kom">// ©edá barva pozadí (stejná, jako má mlha)</span></p>

<p>Pøíkaz glFogi(GL_FOG_MODE, fogMode[fogfilter]) vybere typ filtru. Pro nás bude zatím nejjednodu¹¹í v¹echny mo¾nosti vlo¾it do pole a pak je voláním pou¾ít. Co tedy znamenají: <b>GL_EXP</b> - základní renderovaná mlha, která zahalí celou obrazovku; neposkytuje zrovna perfektní výsledek, ale odvádí slu¹nou práci na star¹ích poèítaèích. <b>GL_EXP2</b> - dal¹í vývojový krok GL_EXP; opìt zaml¾í celý monitor, ale tentokrát do vìt¹í hloubky. <b>GL_LINEAR</b> - nejlep¹í renderovací mód; objekty se mnohem lépe ztrácejí a vynoøují</p>

<p class="src1">glFogi(GL_FOG_MODE, fogMode[fogfilter]);<span class="kom">// Mód mlhy</span></p>
<p class="src1">glFogfv(GL_FOG_COLOR, fogColor);<span class="kom">// Barva mlhy</span></p>
<p class="src1">glFogf(GL_FOG_DENSITY, 0.35f);<span class="kom">// Hustota mlhy</span></p>

<p>O kvalitu mlhy se starat nebudeme, nicménì lze také pou¾ít GL_NICEST nebo GL_FASTEST. Nebudu je dále rozebírat - názvy mluví sami za sebe.</p>

<p class="src1">glHint(GL_FOG_HINT, GL_DONT_CARE);<span class="kom">// Kvalita mlhy</span></p>
<p class="src1">glFogf(GL_FOG_START, 1.0f);<span class="kom">// Zaèátek mlhy - v hloubce (osa z)</span></p>
<p class="src1">glFogf(GL_FOG_END, 5.0f);<span class="kom">// Konec mlhy - v hloubce (osa z)</span></p>
<p class="src1">glEnable(GL_FOG);<span class="kom">// Zapne mlhu</span></p>

<p>O¹etøíme stisk klávesy 'G', kterou mù¾eme pøi bìhu cyklovat mezi rùznými módy mlhy.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src1">if(keys['G'] &amp;&amp; !gp)<span class="kom">// Je stisknuto 'G'?</span></p>
<p class="src1">{</p>
<p class="src2">gp=TRUE;</p>
<p class="src2">fogfilter+=1;<span class="kom">// Inkrementace fogfilter</span></p>
<p class="src"></p>
<p class="src2">if(fogfilter>2)<span class="kom">// Hlídá pøeteèení</span></p>
<p class="src2">{</p>
<p class="src3">fogfilter=0;</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">glFogi (GL_FOG_MODE, fogMode[fogfilter]);<span class="kom">// Nastavení módu mlhy</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(!keys['G'])<span class="kom">// Bylo uvolnìno 'G'?</span></p>
<p class="src1">{</p>
<p class="src2">gp=FALSE;</p>
<p class="src1">}</p>

<p>Hodnì zajímavý, ale pøedev¹ím totálnì jednoduchý efekt. Celkem bezbolestnì jsme se nauèili pou¾ívat mlhu v OpenGL programech.</p>

<p class="autor">napsal: Christopher Aliotta - Precursor <?VypisEmail('chris@incinerated.com');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson16.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson16_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson16.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson16.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson16.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson16.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson16.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson16.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson16.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson16.tar.gz">Irix / GLUT</a> kód této lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson16.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson16.jar">JoGL</a> kód této lekce. ( <a href="mailto:ncb000gt65@hotmail.com">Nicholas Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson16.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson16.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:planetes@mediaone.net">Daniel Davis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson16.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson16.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson16.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson16.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson16.zip">MASM</a> kód této lekce. ( <a href="mailto:greg@ozducati.com">Greg Helps</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson16.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/sdl/lesson16.tar.gz">SDL</a> kód této lekce. ( <a href="mailto:kjrockot@home.com">Ken Rockot</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson16.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson16-2.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson16.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(16);?>
<?FceNeHeOkolniLekce(16);?>

<?
include 'p_end.php';
?>
