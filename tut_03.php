<?
$g_title = 'CZ NeHe OpenGL - Lekce 3 - Barvy';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(3);?>

<h1>Lekce 3 - Barvy</h1>

<p class="nadpis_clanku">S jednoduchým roz¹íøením znalostí ze druhé lekce budete moci pou¾ívat barvy. Nauèíte se jak ploché vybarvování, tak i barevné pøechody. Barvy rozzáøí vzhled aplikace a tím spí¹e zaujmou diváka.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Zaèátek kreslení trojúhelníkù</span></p>

<p>Z minulé lekci si pamatujete, ¾e jsme kreslili trojúhelník na levou èást obrazovky - to zùstává. Dal¹í øádek bude na¹e první pou¾ití pøíkazu glColor3f(r,g,b). Parametry v závorkách jsou intenzita èervené, zelené a modré barvy. Mohou nabývat hodnot od 0 do 1. Pracují stejným zpùsobem jako u funkce pro barvu pozadí glClearColor(r, g, b, 1.0f). Nastavujeme barvu na èistou èervenou (¾ádná zelená a modrá). S pou¾itím této barvy vykreslíme první vrchol trojúhelníku. Dokud nezmìníme barvu, bude mít v¹e, co nakreslíme èervenou barvu.</p>

<p class="src2">glColor3f(1.0f, 0.0f, 0.0f);<span class="kom">// Èervená barva</span></p>
<p class="src2">glVertex3f(0.0f, 1.0f, 0.0f);<span class="kom">// Horní bod</span></p>

<p>Máme umístìn první bod. Teï ne¾ umístíme druhý bod, ale pøedtím zmìníme barvu na zelenou.</p>

<p class="src2">glColor3f(0.0f, 1.0f, 0.0f);<span class="kom">// Zelená barva</span></p>
<p class="src2">glVertex3f(-1.0f, -1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>

<p>Pravý dolní bod bude modrý. Jakmile provedeme pøíkaz glEnd(), vybarví se trojúhelník. Ale proto¾e má v ka¾dém vrcholu jinou barvu, budou se barvy ¹íøit z ka¾dého rohu a nakonec se setkají uprostøed, kde se smísí dohromady.</p>

<p class="src2">glColor3f(0.0f, 0.0f, 1.0f);<span class="kom">// Modrá barva</span></p>
<p class="src2">glVertex3f(1.0f, -1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Ukonèení kreslení trojúhelníkù</span></p>

<p>Vykreslíme ètverec vyplnìný modrou barvou. Je dùle¾ité zapamatovat si, ¾e cokoli nakreslíme po nastavení barvy, bude vykresleno touto barvou. Ka¾dý projekt, který vytváøíte pou¾ívá nìjaký zpùsob vybarvování. Dokonce i ve scénách, kde je v¹e kresleno pomocí textur, mù¾e být funkce glColor3f() pou¾ita k dodání nádechu po¾adované barvy.</p>

<p class="src1">glTranslatef(3.0f,0.0f,0.0f);<span class="kom">// Posun o 3 jednotky doprava</span></p>
<p class="src"></p>
<p class="src1">glColor3f(0.5f,0.5f,1.0f);<span class="kom">// Svìtle modrá barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Levý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Pravý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení obdélníkù</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Ukonèení funkce</span></p>
<p class="src0">}</p>

<p>V tomto tutoriálu jsem se sna¾il vysvìtlit co nejvíce podrobností o jednobarevném a pøechodovém vybarvování mnohoúhelníkù. Pohrajte si s tímto kódem, zkuste zmìnit hodnoty èervené, zelené a modré na jiná èísla. Podívejte se co se stane.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson03.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson03.zip">ASM</a> kód této lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson03_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson03.zip">C#</a> kód této lekce. ( <a href="mailto:joachim_rohde@freenet.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson03.zip">VB.Net CsGL</a> kód této lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson03.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson03.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson03.zip">Delphi</a> kód této lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson03.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson03.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson03.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson03.zip">GLUT</a> kód této lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson03.zip">Irix</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson03.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson03.zip">Java/SWT</a> kód této lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson03.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson03.jar">JoGL</a> kód této lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson03.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson03.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson03.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson03.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson03.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson03.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson03.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson03.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson03.zip">Perl</a> kód této lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson03.gz">Python</a> kód této lekce. ( <a href="mailto:hakuin@voicenet.com">John</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson03.rb.hqx">REALbasic</a> kód této lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson03.zip">Scheme</a> kód této lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson03.zip">Solaris</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson03.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson03.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson03.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(3);?>
<?FceNeHeOkolniLekce(3);?>

<?
include 'p_end.php';
?>
