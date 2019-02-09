<?
$g_title = 'CZ NeHe OpenGL - Lekce 16 - Mlha';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(16);?>

<h1>Lekce 16 - Mlha</h1>

<p class="nadpis_clanku">Tato lekce roz�i�uje pou�it�m mlhy lekci 7. Nau��te se pou��vat t�� r�zn�ch filtr�, m�nit barvu a nastavit oblast p�soben� mlhy (v hloubce). Velmi jednoduch� a "efektn�" efekt.</p>

<p>Na za��tek programu, za v�echna #include, p�id�me deklarace nov�ch prom�nn�ch. Gp pro zji�t�n� stisku kl�vesy G, ve filte" najdeme ��slo 0 a� 2, specifikuj�c� pr�v� pou��van� texturov� filtr. V poli fogMode[] ukl�d�me t�i r�zn� typy mlhy. Fogfilter ur�uje pr�v� pou��vanou mlhu. Ve fogColor je ulo�ena �ed� barva.</p>

<p class="src0">bool gp;<span class="kom">// G stisknuto?</span></p>
<p class="src0">GLuint filter;<span class="kom">// Ur�uje texturov� filtr</span></p>
<p class="src0">GLuint fogMode[]= { GL_EXP, GL_EXP2, GL_LINEAR };<span class="kom">// T�i typy mlhy</span></p>
<p class="src0">GLuint fogfilter= 0;<span class="kom">// Kter� mlha se pou��v�</span></p>
<p class="src0">GLfloat fogColor[4]= {0.5f, 0.5f, 0.5f, 1.0f};<span class="kom">// Barva mlhy</span></p>

<p>P�esuneme se do funkce InitGL(). glClearColor(r,g,b,a) jsme v�dy pou��vali pro nastaven� �ern�ho pozad�. Tentokr�t ud�l�me malou zm�nu - pou�ijeme �ed� pozad� (barvu mlhy), proto�e vypad� l�pe.</p>

<p class="src0"><span class="kom">// Funkce InitGL()</span></p>
<p class="src1">glClearColor(0.5f,0.5f,0.5f,1.0f);<span class="kom">// �ed� barva pozad� (stejn�, jako m� mlha)</span></p>

<p>P��kaz glFogi(GL_FOG_MODE, fogMode[fogfilter]) vybere typ filtru. Pro n�s bude zat�m nejjednodu��� v�echny mo�nosti vlo�it do pole a pak je vol�n�m pou��t. Co tedy znamenaj�: <b>GL_EXP</b> - z�kladn� renderovan� mlha, kter� zahal� celou obrazovku; neposkytuje zrovna perfektn� v�sledek, ale odv�d� slu�nou pr�ci na star��ch po��ta��ch. <b>GL_EXP2</b> - dal�� v�vojov� krok GL_EXP; op�t zaml�� cel� monitor, ale tentokr�t do v�t�� hloubky. <b>GL_LINEAR</b> - nejlep�� renderovac� m�d; objekty se mnohem l�pe ztr�cej� a vyno�uj�</p>

<p class="src1">glFogi(GL_FOG_MODE, fogMode[fogfilter]);<span class="kom">// M�d mlhy</span></p>
<p class="src1">glFogfv(GL_FOG_COLOR, fogColor);<span class="kom">// Barva mlhy</span></p>
<p class="src1">glFogf(GL_FOG_DENSITY, 0.35f);<span class="kom">// Hustota mlhy</span></p>

<p>O kvalitu mlhy se starat nebudeme, nicm�n� lze tak� pou��t GL_NICEST nebo GL_FASTEST. Nebudu je d�le rozeb�rat - n�zvy mluv� sami za sebe.</p>

<p class="src1">glHint(GL_FOG_HINT, GL_DONT_CARE);<span class="kom">// Kvalita mlhy</span></p>
<p class="src1">glFogf(GL_FOG_START, 1.0f);<span class="kom">// Za��tek mlhy - v hloubce (osa z)</span></p>
<p class="src1">glFogf(GL_FOG_END, 5.0f);<span class="kom">// Konec mlhy - v hloubce (osa z)</span></p>
<p class="src1">glEnable(GL_FOG);<span class="kom">// Zapne mlhu</span></p>

<p>O�et��me stisk kl�vesy 'G', kterou m��eme p�i b�hu cyklovat mezi r�zn�mi m�dy mlhy.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src1">if(keys['G'] &amp;&amp; !gp)<span class="kom">// Je stisknuto 'G'?</span></p>
<p class="src1">{</p>
<p class="src2">gp=TRUE;</p>
<p class="src2">fogfilter+=1;<span class="kom">// Inkrementace fogfilter</span></p>
<p class="src"></p>
<p class="src2">if(fogfilter>2)<span class="kom">// Hl�d� p�ete�en�</span></p>
<p class="src2">{</p>
<p class="src3">fogfilter=0;</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">glFogi (GL_FOG_MODE, fogMode[fogfilter]);<span class="kom">// Nastaven� m�du mlhy</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(!keys['G'])<span class="kom">// Bylo uvoln�no 'G'?</span></p>
<p class="src1">{</p>
<p class="src2">gp=FALSE;</p>
<p class="src1">}</p>

<p>Hodn� zaj�mav�, ale p�edev��m tot�ln� jednoduch� efekt. Celkem bezbolestn� jsme se nau�ili pou��vat mlhu v OpenGL programech.</p>

<p class="autor">napsal: Christopher Aliotta - Precursor <?VypisEmail('chris@incinerated.com');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson16.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson16_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson16.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson16.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson16.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson16.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson16.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson16.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson16.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson16.tar.gz">Irix / GLUT</a> k�d t�to lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson16.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson16.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:ncb000gt65@hotmail.com">Nicholas Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson16.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson16.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:planetes@mediaone.net">Daniel Davis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson16.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson16.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson16.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson16.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson16.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:greg@ozducati.com">Greg Helps</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson16.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/sdl/lesson16.tar.gz">SDL</a> k�d t�to lekce. ( <a href="mailto:kjrockot@home.com">Ken Rockot</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson16.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson16-2.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson16.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(16);?>
<?FceNeHeOkolniLekce(16);?>

<?
include 'p_end.php';
?>
