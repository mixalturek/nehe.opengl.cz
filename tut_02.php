<?
$g_title = 'CZ NeHe OpenGL - Lekce 2 - Vytváøení trojúhelníkù a ètyøúhelníkù';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(2);?>

<h1>Lekce 2 - Vytváøení trojúhelníkù a ètyøúhelníkù</h1>

<p class="nadpis_clanku">Zdrojový kód z první lekce trochu upravíme, aby program vykreslil trojúhelník a ètverec. Vím, ¾e si asi myslíte, ¾e takovéto vykreslování je banalita, ale a¾ zaènete programovat pochopíte, ¾e orientovat se ve 3D prostoru není na pøedstavivost a¾ tak jednoduché. Jakékoli vytváøení objektù v OpenGL závisí na trojúhelnících a ètvercích. Pokud pochopíte tuto lekci máte napùl vyhráno.</p>

<p>Chcete-li pou¾ít kód z první lekce, tak pøepi¹te funkci DrawGLScene(), v¹e ostatní zùstává nezmìnìno.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Pou¾itím glLoadIdentity() se pøesunete doprostøed obrazovky. Osa x prochází zleva doprava, osa y zespodu nahoru a osa z od monitoru smìrem k vám. Støed OpenGL scény je na souøadnicích, kde se x, y i z rovná nule.
Funkce glTranslatef(x, y, z) pohybuje poèátkem souøadnicových os. Následující øádek ho posune doleva o 1.5 jednotky. Nepøesouváme se na ose y (0.0f) a posune se smìrem dovnitø obrazovky o 6.0 jednotek. Pøi pou¾ití glTranslatef(x, z, z), se nepohybujeme v¾dy z centra obrazovky, ale z místa, kde jsme se s pomocí této funkce dostali. Zadáváme tedy pouze offset pohybu.</p>

<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>

<p>Nyní jsme se posunuli do levé poloviny obrazovky a nastavili jsme místo pohledu dostateènì hluboko, tak¾e mù¾eme vidìt celou scénu. Vykreslíme trojúhelník. Posun po ose z urèuje jak velké budou vykreslované objekty (perspektiva). GlBegin(GL_TRIANGLES) OpenGL øíká, ¾e chceme zaèít kreslit trojúhelníky a glEnd() oznamuje ukonèení kreslení trojúhelníkù. Kreslení trojúhelníkù je na vìt¹inì grafických karet o hodnì rychlej¹í ne¾ kreslení ètvercù. Pokud chcete spojovat ètyøi body pou¾ijete GL_QUADS. Mnohoúhelníky se vytváøejí pomocí GL_POLYGON. Vìt¹ina karet je ale stejnì konvertuje na trojúhelníky. V na¹em jednoduchém programu nakreslíme pouze jeden trojúhelník. Pokud bychom chtìli nakreslit druhý trojúhelník, staèí pøidat dal¹í tøi body hned za první tøi. V¹ech ¹est øádkù by bylo mezi glBegin(GL_TRIANGLES) a glEnd() a ka¾dá skupina po tøech tvoøí jeden trojúhelník. Tohle platí stejnì i pro ètyøúhelníky, kde jsou skupiny brány po ètyøech. Kreslení mnohoúhelníku je ale u¾ o nìèem jiném, proto¾e mù¾e být vytvoøen z libovolného poètu bodù. V¹echny body by byly pøiøazeny k jedinému mnohoúhelníku. První øádek po glBegin() definuje první bod trojúhelníku. První parametr ve funkci glVertex3f() urèuje souøadnici na ose x, druhý parametr osu y a tøetí parametr osu z. V prvním øádku se tedy nepohybujeme po ose x. Bod umístíme pouze o jednu jednotku nahoru na ose y a na ose z necháme opìt nulu. Tím nastavíme horní bod. Druhá volání glVertex3f() umis»uje bod posunutý o jednotku vlevo a dolù. Tím vytvoøíme druhý vrchol. Tøetí funkce umis»uje bod vpravo na x  a dolù na y. Máme vytvoøený tøetí vrchol. Funkcí glEnd() øekneme OpenGL, ¾e u¾ nebudeme umís»ovat dal¹í body. Zobrazí se vyplnìný trojúhelník.</p>

<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Zaèátek kreslení trojúhelníkù</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horní bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src1">glEnd();<span class="kom">// Ukonèení kreslení trojúhelníkù</span></p>

<p>Na levé stranì obrazovky jsem vykreslili trojúhelník. Provedeme translaci doprava a umístíme ètverec. Na ose x se posunujeme o 1.5, abychom dosáhli zpìt støedu a k tomu pøièteme dal¹ích 1.5 a budeme vpravo. Na osách y a z se nepøesunujeme.</p>

<p class="src1">glTranslatef(3.0f,0.0f,0.0f);<span class="kom">// Posun o 3 jednotky doprava</span></p>

<p>Kód pro nakreslení ètverce je velice podobný tomu, který jsme pou¾ili pro trojúhelník. Jediný rozdíl v pou¾ití GL_QUADS místo GL_TRIANGLES je pøidání dal¹ího vertexu pro ètvrtý bod. Nakreslíme postupnì levý horní, pravý horní, pravý dolní a levý dolní vrchol.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Levý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Pravý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení obdélníkù</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Ukonèení funkce</span></p>
<p class="src0">}</p>

<p>To je pro tuto lekci v¹e. Probrali jsme nejjednodu¹¹í kreslení. Doufám, ¾e vás moc neodradilo - to teprve pøijde :-]</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Václav Slováèek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson02.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson02.zip">ASM</a> kód této lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson02_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson02.zip">C#</a> kód této lekce. ( <a href="mailto:joachim_rohde@freenet.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson02.zip">VB.Net CsGL</a> kód této lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson02.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson02.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson02.zip">Delphi</a> kód této lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson02.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson02.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson02.zip">GLUT</a> kód této lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson02.zip">Irix</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson02.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson02.zip">Java/SWT</a> kód této lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson02.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson02.jar">JoGL</a> kód této lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson02.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson02.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson02.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson02.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson02.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson02.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson02.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson02.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson02.zip">Perl</a> kód této lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson02.gz">Python</a> kód této lekce. ( <a href="mailto:hakuin@voicenet.com">John</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/qt_cpp/lesson02.tar.gz">QT/C++</a> kód této lekce. ( <a href="mailto:pmarian@cnlo.ro">Popeanga Marian</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson02.rb.hqx">REALbasic</a> kód této lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson02.zip">Scheme</a> kód této lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson02.zip">Solaris</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson02.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson02.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson02.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(2);?>
<?FceNeHeOkolniLekce(2);?>

<?
include 'p_end.php';
?>
