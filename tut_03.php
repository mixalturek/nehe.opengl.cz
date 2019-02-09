<?
$g_title = 'CZ NeHe OpenGL - Lekce 3 - Barvy';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(3);?>

<h1>Lekce 3 - Barvy</h1>

<p class="nadpis_clanku">S jednoduch�m roz���en�m znalost� ze druh� lekce budete moci pou��vat barvy. Nau��te se jak ploch� vybarvov�n�, tak i barevn� p�echody. Barvy rozz��� vzhled aplikace a t�m sp�e zaujmou div�ka.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek kreslen� troj�heln�k�</span></p>

<p>Z minul� lekci si pamatujete, �e jsme kreslili troj�heln�k na levou ��st obrazovky - to z�st�v�. Dal�� ��dek bude na�e prvn� pou�it� p��kazu glColor3f(r,g,b). Parametry v z�vork�ch jsou intenzita �erven�, zelen� a modr� barvy. Mohou nab�vat hodnot od 0 do 1. Pracuj� stejn�m zp�sobem jako u funkce pro barvu pozad� glClearColor(r, g, b, 1.0f). Nastavujeme barvu na �istou �ervenou (��dn� zelen� a modr�). S pou�it�m t�to barvy vykresl�me prvn� vrchol troj�heln�ku. Dokud nezm�n�me barvu, bude m�t v�e, co nakresl�me �ervenou barvu.</p>

<p class="src2">glColor3f(1.0f, 0.0f, 0.0f);<span class="kom">// �erven� barva</span></p>
<p class="src2">glVertex3f(0.0f, 1.0f, 0.0f);<span class="kom">// Horn� bod</span></p>

<p>M�me um�st�n prvn� bod. Te� ne� um�st�me druh� bod, ale p�edt�m zm�n�me barvu na zelenou.</p>

<p class="src2">glColor3f(0.0f, 1.0f, 0.0f);<span class="kom">// Zelen� barva</span></p>
<p class="src2">glVertex3f(-1.0f, -1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>

<p>Prav� doln� bod bude modr�. Jakmile provedeme p��kaz glEnd(), vybarv� se troj�heln�k. Ale proto�e m� v ka�d�m vrcholu jinou barvu, budou se barvy ���it z ka�d�ho rohu a nakonec se setkaj� uprost�ed, kde se sm�s� dohromady.</p>

<p class="src2">glColor3f(0.0f, 0.0f, 1.0f);<span class="kom">// Modr� barva</span></p>
<p class="src2">glVertex3f(1.0f, -1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Ukon�en� kreslen� troj�heln�k�</span></p>

<p>Vykresl�me �tverec vypln�n� modrou barvou. Je d�le�it� zapamatovat si, �e cokoli nakresl�me po nastaven� barvy, bude vykresleno touto barvou. Ka�d� projekt, kter� vytv���te pou��v� n�jak� zp�sob vybarvov�n�. Dokonce i ve sc�n�ch, kde je v�e kresleno pomoc� textur, m��e b�t funkce glColor3f() pou�ita k dod�n� n�dechu po�adovan� barvy.</p>

<p class="src1">glTranslatef(3.0f,0.0f,0.0f);<span class="kom">// Posun o 3 jednotky doprava</span></p>
<p class="src"></p>
<p class="src1">glColor3f(0.5f,0.5f,1.0f);<span class="kom">// Sv�tle modr� barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Lev� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Prav� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen� obd�ln�k�</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Ukon�en� funkce</span></p>
<p class="src0">}</p>

<p>V tomto tutori�lu jsem se sna�il vysv�tlit co nejv�ce podrobnost� o jednobarevn�m a p�echodov�m vybarvov�n� mnoho�heln�k�. Pohrajte si s t�mto k�dem, zkuste zm�nit hodnoty �erven�, zelen� a modr� na jin� ��sla. Pod�vejte se co se stane.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson03.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson03.zip">ASM</a> k�d t�to lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson03_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson03.zip">C#</a> k�d t�to lekce. ( <a href="mailto:joachim_rohde@freenet.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson03.zip">VB.Net CsGL</a> k�d t�to lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson03.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson03.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson03.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson03.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson03.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson03.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson03.zip">GLUT</a> k�d t�to lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson03.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson03.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson03.zip">Java/SWT</a> k�d t�to lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson03.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson03.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson03.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson03.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson03.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson03.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson03.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson03.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson03.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson03.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson03.zip">Perl</a> k�d t�to lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson03.gz">Python</a> k�d t�to lekce. ( <a href="mailto:hakuin@voicenet.com">John</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson03.rb.hqx">REALbasic</a> k�d t�to lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson03.zip">Scheme</a> k�d t�to lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson03.zip">Solaris</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson03.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson03.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson03.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(3);?>
<?FceNeHeOkolniLekce(3);?>

<?
include 'p_end.php';
?>
