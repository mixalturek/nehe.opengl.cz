<?
$g_title = 'CZ NeHe OpenGL - Lekce 4 - Rotace';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(4);?>

<h1>Lekce 4 - Rotace</h1>

<p class="nadpis_clanku">Nauèíme se, jak otáèet objekt okolo os. Trojúhelník se bude otáèet kolem osy y a ètverec kolem osy x. Je jednoduché vytvoøit scénu z polygonù. Pøidání pohybu ji pìknì o¾iví.</p>

<p>Zaèneme pøidáním dvou promìnných pro ulo¾ení rotace ka¾dého objektu. Deklarujeme je jako globální na zaèátku programu. Brzy zjistíte, ¾e desetinná èísla jsou nezbytná k programování v OpenGL.</p>

<p class="src0">GLfloat rtri;<span class="kom">// Úhel pro trojúhelník</span></p>
<p class="src0">GLfloat rquad;<span class="kom">// Úhel pro ètverec</span></p>

<p>Pøepí¹eme funkci DrawGLScene, která slou¾í pro vykreslování.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>

<p>Následující èást kódu je nová. Funkce glRotatef(uhel, x_vektor, y_vektor, z_vektor) je odpovìdná za rotaci souøadnicových os. Budete ji èasto pou¾ívat. Uhel je èíslo (obvykle ulo¾ené v promìnné), které urèuje o kolik stupòù chcete otoèit objektem. Parametry x_vektor, y_vektor a z_vektor dohromady se souèasným poèátkem souøadnic urèují vektor okolo kterého se objekt bude otáèet.</p>
<p>Pro lep¹í vysvìtlení uvedu vysvìtlení na pøíkladech: <b>Osa x</b> - Pøedstavte si, ¾e stojíte u vodorovné desky a chcete jí otoèit. Pokud zadáte kladnou rotaci [1,0,0] zvedá se vzdálenìj¹í èást desky a bli¾¹í klesá. Pøi záporné rotaci [-1,0,0] je to naopak. <b>Osa y</b> - Opìt stejná deska. Pøi kladné rotaci [0,1,0] se pravá èást desky pohybuje od vás a levá èást desky k vám. Pøi záporné rotaci [0,-1,0] je to naopak. <b>Osa z</b> - Opìt stejná deska. Pøi kladné rotaci [0,0,1] se zvedá pravá èást desky a levá klesá a pøi záporné [0,0,-1] je to naopak.</p>

<p>Pokud bude rtri 7, následujícím øádkem pootoèíme trojúhelníkem o 7° okolo osy y proti smìru hodinových ruèièek.</p>

<p class="src1">glRotatef(rtri,0.0f,1.0f,0.0f);<span class="kom">// Otoèí trojúhelník okolo osy y</span></p>

<p>Dal¹í èást kódu zùstává nezmìnìna. Vykreslí vybarvený trojúhelník. Tentokrát ale díky pøedcházejícímu øádku pootoèený okolo osy y.</p>

<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Zaèátek kreslení trojúhelníkù</span></p>
<p class="src"></p>
<p class="src2">glColor3f(1.0f, 0.0f, 0.0f);<span class="kom">// Èervená barva</span></p>
<p class="src2">glVertex3f(0.0f, 1.0f, 0.0f);<span class="kom">// Horní bod</span></p>
<p class="src2">glColor3f(0.0f, 1.0f, 0.0f);<span class="kom">// Zelená barva</span></p>
<p class="src2">glVertex3f(-1.0f, -1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src2">glColor3f(0.0f, 0.0f, 1.0f);<span class="kom">// Modrá barva</span></p>
<p class="src2">glVertex3f(1.0f, -1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Ukonèení kreslení trojúhelníkù</span></p>

<p>Jistì si v následujícím kódu v¹imnete, ¾e jsme pøidali dal¹í volání funkce glLoadIdentity(). Proto¾e byly osy pootoèeny neukazují do smìrù, které pøedpokládáte. Tak¾e pokud posouváme okolo osy x, mù¾eme skonèit posouváním ve smìru pùvodní osy z, zále¾í na tom, jak moc jsme pootoèili. Zkuste odstranit øádek s voláním glLoadIdentity(), a» vidíte co myslím. Jakmile máme resetováno míøí osy opìt pùvodními smìry, tj. x - zleva doprava, y - zdola nahoru, z - z obrazovky k vám. V¹imnìte si, ¾e posouváme jen o 1,5 na ose x narozdíl od posunu o 3 z pøedchozí èásti. Kdy¾ resetujeme, posune se poèátek zpátky do støedu obrazovky.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(1.5f,0.0f,-6.0f);<span class="kom">// Posun poèátku</span></p>
<p class="src"></p>
<p class="src1">glRotatef(rquad,1.0f,0.0f,0.0f);<span class="kom">// Pootoèení ètverce okolo osy x</span></p>

<p>Vykreslení je stejné jako v pøedcházející èásti. Opìt vykreslí ètverec, ale tentokrát pootoèený.</p>

<p class="src1">glColor3f(0.5f,0.5f,1.0f);<span class="kom">// Svìtle modrá barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Levý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Pravý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení obdélníkù</span></p>

<p>Po ka¾dém zobrazení se zmìní promìnné rtri a rquad, ve kterých jsou ulo¾eny hodnoty pootoèení trojúhelníku a ètverce. Zmìnou znamínka mù¾ete zmìnit smysl rotace. Zmìnou velikosti pøièítaných hodnot mù¾ete zmìnit rychlost rotace.</p>

<p class="src1">rtri+=0.2f;<span class="kom">// Inkrementace úhlu pootoèení trojúhelníku</span></p>
<p class="src1">rquad-=0.15f;<span class="kom">// Inkrementace úhlu pootoèení ètverce</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Ukonèení funkce</span></p>
<p class="src0">}</p>

<p>Doufám, ¾e jste pochopili, ¾e se v¹e vykresluje poøád stejnì, souøadnice bodù se nikdy nemìní. Poka¾dé se pouze li¹í poèátek souøadnicových os, jejich natoèení nebo mìøítko - glScalef(x,y,z). Pokud chcete, aby se objekt otáèel okolo své osy, umístìte ho okolo poèátku nebo alespoò na jednu ze souøadnicových os - tato lekce. Pøi jiném umístìní bude chaoticky létat po scénì.</p>

<p>Také je dùle¾ité, zda napøed provedete translaci nebo rotaci. Pøi poèáteèním pootoèení budou osy smìøovat jinam ne¾ oèekáváte a následné posunutí skonèí úplnì nìkde jinde ne¾ chcete.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson04.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson04.zip">ASM</a> kód této lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson04_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson04.zip">C#</a> kód této lekce. ( <a href="mailto:sugarbee@gmx.net">Sabine Felsinger</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson04.zip">VB.Net CsGL</a> kód této lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson04.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson04.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson04.zip">Delphi</a> kód této lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson04.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson04.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson04.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/genu/lesson04.zip">Genu</a> kód této lekce. ( <a href="mailto:lcdumais@hotmail.com">Louis-Charles Dumais</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson04.zip">GLUT</a> kód této lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson04.zip">Irix</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson04.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson04.zip">Java/SWT</a> kód této lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson04.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson04.jar">JoGL</a> kód této lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson04.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson04.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson04.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson04.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson04.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson04.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson04.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson04.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson04.zip">Perl</a> kód této lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson04.gz">Python</a> kód této lekce. ( <a href="mailto:hakuin@voicenet.com">John</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson04.rb.hqx">REALbasic</a> kód této lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson04.zip">Scheme</a> kód této lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson04.zip">Solaris</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson04.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson04.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson04.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(4);?>
<?FceNeHeOkolniLekce(4);?>

<?
include 'p_end.php';
?>
