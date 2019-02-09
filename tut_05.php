<?
$g_title = 'CZ NeHe OpenGL - Lekce 5 - Pevné objekty';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(5);?>

<h1>Lekce 5 - Pevné objekty</h1>

<p class="nadpis_clanku">Roz¹íøením poslední èásti vytvoøíme skuteèné 3D objekty. Narozdíl od 2D objektù ve 3D prostoru. Zmìníme trojúhelník na pyramidu a ètverec na krychli. Pyramida bude vybarvena barevným pøechodem a ka¾dou stìnu krychle vybarvíme jinou barvou.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>
<p class="src1">glRotatef(rtri,0.0f,1.0f,0.0f);<span class="kom">// Otoèí pyramidu okolo osy y</span></p>

<p>Èást kódu vezmeme z pøedchozí èásti a vyrobíme pomocí nìj 3D objekt. Je jedna vìc na kterou jsem èasto dotazován. Proè se objekty neotáèejí okolo své osy. Vypadá to jako by se létaly po celé obrazovce. Pokud objektu øeknete, aby se otoèil okolo osy, otoèí se okolo osy souøadnicového systému. Pokud chcete, aby se rotoval okolo své osy, musíte dát poèátek souøadnic do jeho støedu nebo aspoò tak, aby se souøadnicová osa, okolo které otáèíte, kryla s osou objektu okolo které chcete otoèit.</p>

<p>Následující kód vytvoøí pyramidu okolo centrální osy. Vrcholek pyramidy je o jednu nahoøe od støedu, spodek o jednu dolù. Vrchní bod je vpravo uprostøed a dolní body jsou vpravo a vlevo od støedu. V¹imnìte si, ¾e v¹echny trojúhelníky jsou kresleny ve smìru proti hodinovým ruèièkám. Je to dùle¾ité a bude to vysvìtleno v dal¹ích lekcích - napø. lekce 11. Teï si pouze zapamatujte, ¾e je dobré kreslit po smìru nebo proti smìru hodinových ruèièek a pokud k tomu nemáte dùvod, nemìli byste dvì osy prohodit. Zaèneme kreslením èelní stìny. Proto¾e v¹echny sdílí horní bod, udìláme jej u v¹ech stìn èervený. Barvy na spodních vrcholech se budou støídat. Èelní stìna bude mít levý bod zelený a pravý modrý. Trojúhelník na pravé stranì bude mít levý bod modrý a pravý zelený. Prohozením dolních dvou barev na ka¾dé stìnì udìláme spoleènì vybarvené vrcholy na spodku ka¾dé stìny.</p>

<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Zaèátek kreslení pyramidy</span></p>
<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// Èervená</span></p>
<p class="src2">glVertex3f(0.0f,1.0f,0.0f);<span class="kom">// Horní bod (èelní stìna)</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelená</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Levý spodní bod (èelní stìna)</span></p>
<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modrá</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Pravý spodní bod (èelní stìna)</span></p>

<p>Vykreslíme pravou stìnu. Spodní body kreslíme vpravo od støedu a horní bod je kreslen o jedna na ose y od støedu a pravý støed na ose x. To zpùsobuje, ¾e se stìna sva¾uje od horního bodu doprava dolù. Levý bod je tentokrát modrý stejnì jako pravý dolní bod èelní stìny, ke kterému pøiléhá.
Zbylé tøi trojúhelníky kresleny ve stejném glBegin(GL_TRIANGLES) a glEnd() jako první trojúhelník. Proto¾e dìlám celý objekt z trojúhelníkù, OpenGL ví, ¾e ka¾dé tøi body tvoøí trojúhelník. Jakmile nakreslíte tøi body a pøidáte dal¹í body, OpenGL pøedpokládá, ¾e je tøeba kreslit dal¹í trojúhelník. Pokud zadáte ètyøi body místo tøí, OpenGL pou¾ije první tøi a bude pøedpokládat, ¾e ètvrtý je zaèátek dal¹ího trojúhelníku. Nevykreslí ètverec. Proto si dávejte pozor, aby jste náhodou nepøidali nìjaký bod navíc.</p>

<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// Èervená</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horní bod (pravá stìna)</span></p>
<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modrá</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Levý bod (pravá stìna)</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelená</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, -1.0f);<span class="kom">// Pravý bod (pravá stìna)</span></p>

<p>Teï vykreslíme zadní stìnu. Opìt prohození barev. Levý bod je opìt zelený, proto¾e odpovídající pravý bod je zelený.</p>

<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// Èervená</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horní bod (zadní stìna)</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelená</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, -1.0f);<span class="kom">// Levý bod (zadní stìna)</span></p>
<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modrá</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, -1.0f);<span class="kom">// Pravý bod (zadní stìna)</span></p>

<p>Nakonec nakreslíme levou stìnu pyramidy. Proto¾e pyramida rotuje okolo osy Y, nikdy neuvidíme podstavu. Pokud chcete experimentovat, zkuste pøidat ji pøidat. Potom pootoète pyramidu okolo osy x a uvidíte zda se vám to povedlo.</p>


<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// Èervená</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horní bod (levá stìna)</span></p>
<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modrá</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f,-1.0f)<span class="kom">// Levý bod (levá stìna)</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelená</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Pravý bod (levá stìna)</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení pyramidy</span></p>

<p>Teï vykreslíme krychli. Je tvoøena ¹esti ètverci, které jsou kresleny opìt proti smìru hodinových ruèièek. To znamená, ¾e první bod je pravý horní, druhý levý horní, tøetí levý dolní a ètvrtý pravý dolní. Kdy¾ kreslíme zadní stìnu, mù¾e to vypadat, ¾e kreslíme ve smìru hodinových ruèièek, ale pamatujte, ¾e jsme za krychlí a díváme se smìrem k èelní stìnì. Tak¾e levá strana obrazovky je pravou stranou ètverce. Tentokrát posouváme krychli trochu dál. Tím velikost více odpovídá velikosti pyramidy a èásti mohou být oøíznuty okraji obrazovky. Mù¾ete si pohrát s nastavením poèátku a uvidíte, ¾e posunutím dále se zdá men¹í a naopak. Dùvodem je perspektiva. Vzdálenìj¹í objekty se zdají men¹í.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(1.5f,0.0f,-7.0f);<span class="kom">// Posun poèátku vpravo a dozadu</span></p>
<p class="src1">glRotatef(rquad,1.0f,1.0f,1.0f);<span class="kom">// Rotace okolo x, y, a z</span></p>

<p>Zaèneme kreslením vrcholku krychle. V¹imnìte si, ¾e souøadnice y je v¾dy jedna. Tím kreslíme stìnu rovnobì¾nì s rovinou xz. Zaèneme pravým horním bodem. Ten je o jedna vpravo a o jedna dozadu. Dal¹í bod je o jedna vlevo a o jedna dozadu. Poté vykreslíme spodní èást ètverce smìrem k pozorovateli. Abychom toho dosáhli, narozdíl od posunu do obrazovky, posuneme se o jeden bod z obrazovky.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení krychle</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Modrá</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f,-1.0f);<span class="kom">// Pravý horní (horní stìna)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f,-1.0f);<span class="kom">// Levý horní (horní stìna)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 1.0f);<span class="kom">// Levý dolní (horní stìna)</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 1.0f);<span class="kom">// Pravý dolní (horní stìna)</span></p>

<p>Spodní èást krychle se kreslí stejným zpùsobem, jen je posunuta na ose y do -1. Dal¹í zmìna je, ¾e pravý horní bod je tentokrát bod bli¾¹í k vám, narozdíl od horní stìny, kde to byl bod vzdálenìj¹í. V tomto pøípadì by se nic nestalo pokud by jste pouze zkopírovali pøedchozí ètyøi øádky a zmìnili hodnotu y na -1, ale pozdìji by vám to mohlo pøinést problémy napøíklad u textur.</p>

<p class="src2">glColor3f(1.0f,0.5f,0.0f);<span class="kom">// Oran¾ová</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Pravý horní bod (spodní stìna)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Levý horní (spodní stìna)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f,-1.0f);<span class="kom">// Levý dolní (spodní stìna)</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f,-1.0f);<span class="kom">// Pravý dolní (spodní stìna)</span></p>

<p>Teï vykreslíme èelní stìnu.</p>

<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// Èervená</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 1.0f);<span class="kom">// Pravý horní (èelní stìna)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 1.0f);<span class="kom">// Levý horní (èelní stìna)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Levý dolní (èelní stìna)</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Pravý dolní (èelní stìna)</span></p>

<p>Zadní stìna.</p>

<p class="src2">glColor3f(1.0f,1.0f,0.0f);<span class="kom">// ®lutá</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f,-1.0f);<span class="kom">// Pravý horní (zadní stìna)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f,-1.0f);<span class="kom">// Levý horní (zadní stìna)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f,-1.0f);<span class="kom">// Levý dolní (zadní stìna)</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f,-1.0f);<span class="kom">// Pravý dolní (zadní stìna)</span></p>

<p>Levá stìna.</p>

<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modrá</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 1.0f);<span class="kom">// Pravý horní (levá stìna)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f,-1.0f);<span class="kom">// Levý horní (levá stìna)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f,-1.0f);<span class="kom">// Levý dolní (levá stìna)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Pravý dolní (levá stìna)</span></p>

<p>Pravá stìna. Je to poslední stìna krychle. Pokud chcete tak ji vynechejte a získáte krabici. Nebo mù¾ete zkusit nastavit pro ka¾dý roh jinou barvu a vybarvit ji barevným pøechodem.</p>

<p class="src2">glColor3f(1.0f,0.0f,1.0f);<span class="kom">// Fialová</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f,-1.0f);<span class="kom">// Pravý horní (pravá stìna)</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 1.0f);<span class="kom">// Levý horní (pravá stìna)</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Levý dolní (pravá stìna)</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f,-1.0f);<span class="kom">// Pravý dolní (pravá stìna)</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení krychle</span></p>
<p class="src"></p>
<p class="src1">rtri+=0.2f;<span class="kom">// Inkrementace úhlu pootoèení pyramidy</span></p>
<p class="src1">rquad-=0.15f;<span class="kom">// Inkrementace úhlu pootoèení krychle</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Na konci tohoto tutoriálu byste mìli lépe rozumìt jak jsou vytváøeny 3D objekty. Mù¾ete pøemý¹let o OpenGL scénì jako o kusu papíru s mnoha prùsvitnými vrstvami. Jako gigantická krychle tvoøená body. Pokud si doká¾ete pøedstavit v obrazovce hloubku, nemìli byste mít problém s vytváøením vlastních 3D objektù.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson05.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson05.zip">ASM</a> kód této lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson05_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson05.zip">C#</a> kód této lekce. ( <a href="mailto:sugarbee@gmx.net">Sabine Felsinger</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson05.zip">VB.Net CsGL</a> kód této lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson05.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson05.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson05.zip">Delphi</a> kód této lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson05.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson05.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson05.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson05.zip">GLUT</a> kód této lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson05.zip">Irix</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson05.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson05.zip">Java/SWT</a> kód této lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson05.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson05.jar">JoGL</a> kód této lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson05.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson05.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson05.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson05.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson05.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson05.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson05.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson05.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson05.zip">Perl</a> kód této lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson05.gz">Python</a> kód této lekce. ( <a href="mailto:acolston@midsouth.rr.com">Tony Colston</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson05.rb.hqx">REALbasic</a> kód této lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson05.zip">Scheme</a> kód této lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson05.zip">Solaris</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson05.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson05.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson05.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(5);?>
<?FceNeHeOkolniLekce(5);?>

<?
include 'p_end.php';
?>
