<?
$g_title = 'CZ NeHe OpenGL - Lekce 2 - Vytv��en� troj�heln�k� a �ty��heln�k�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(2);?>

<h1>Lekce 2 - Vytv��en� troj�heln�k� a �ty��heln�k�</h1>

<p class="nadpis_clanku">Zdrojov� k�d z prvn� lekce trochu uprav�me, aby program vykreslil troj�heln�k a �tverec. V�m, �e si asi mysl�te, �e takov�to vykreslov�n� je banalita, ale a� za�nete programovat pochop�te, �e orientovat se ve 3D prostoru nen� na p�edstavivost a� tak jednoduch�. Jak�koli vytv��en� objekt� v OpenGL z�vis� na troj�heln�c�ch a �tverc�ch. Pokud pochop�te tuto lekci m�te nap�l vyhr�no.</p>

<p>Chcete-li pou��t k�d z prvn� lekce, tak p�epi�te funkci DrawGLScene(), v�e ostatn� z�st�v� nezm�n�no.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Pou�it�m glLoadIdentity() se p�esunete doprost�ed obrazovky. Osa x proch�z� zleva doprava, osa y zespodu nahoru a osa z od monitoru sm�rem k v�m. St�ed OpenGL sc�ny je na sou�adnic�ch, kde se x, y i z rovn� nule.
Funkce glTranslatef(x, y, z) pohybuje po��tkem sou�adnicov�ch os. N�sleduj�c� ��dek ho posune doleva o 1.5 jednotky. Nep�esouv�me se na ose y (0.0f) a posune se sm�rem dovnit� obrazovky o 6.0 jednotek. P�i pou�it� glTranslatef(x, z, z), se nepohybujeme v�dy z centra obrazovky, ale z m�sta, kde jsme se s pomoc� t�to funkce dostali. Zad�v�me tedy pouze offset pohybu.</p>

<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>

<p>Nyn� jsme se posunuli do lev� poloviny obrazovky a nastavili jsme m�sto pohledu dostate�n� hluboko, tak�e m��eme vid�t celou sc�nu. Vykresl�me troj�heln�k. Posun po ose z ur�uje jak velk� budou vykreslovan� objekty (perspektiva). GlBegin(GL_TRIANGLES) OpenGL ��k�, �e chceme za��t kreslit troj�heln�ky a glEnd() oznamuje ukon�en� kreslen� troj�heln�k�. Kreslen� troj�heln�k� je na v�t�in� grafick�ch karet o hodn� rychlej�� ne� kreslen� �tverc�. Pokud chcete spojovat �ty�i body pou�ijete GL_QUADS. Mnoho�heln�ky se vytv��ej� pomoc� GL_POLYGON. V�t�ina karet je ale stejn� konvertuje na troj�heln�ky. V na�em jednoduch�m programu nakresl�me pouze jeden troj�heln�k. Pokud bychom cht�li nakreslit druh� troj�heln�k, sta�� p�idat dal�� t�i body hned za prvn� t�i. V�ech �est ��dk� by bylo mezi glBegin(GL_TRIANGLES) a glEnd() a ka�d� skupina po t�ech tvo�� jeden troj�heln�k. Tohle plat� stejn� i pro �ty��heln�ky, kde jsou skupiny br�ny po �ty�ech. Kreslen� mnoho�heln�ku je ale u� o n��em jin�m, proto�e m��e b�t vytvo�en z libovoln�ho po�tu bod�. V�echny body by byly p�i�azeny k jedin�mu mnoho�heln�ku. Prvn� ��dek po glBegin() definuje prvn� bod troj�heln�ku. Prvn� parametr ve funkci glVertex3f() ur�uje sou�adnici na ose x, druh� parametr osu y a t�et� parametr osu z. V prvn�m ��dku se tedy nepohybujeme po ose x. Bod um�st�me pouze o jednu jednotku nahoru na ose y a na ose z nech�me op�t nulu. T�m nastav�me horn� bod. Druh� vol�n� glVertex3f() umis�uje bod posunut� o jednotku vlevo a dol�. T�m vytvo��me druh� vrchol. T�et� funkce umis�uje bod vpravo na x  a dol� na y. M�me vytvo�en� t�et� vrchol. Funkc� glEnd() �ekneme OpenGL, �e u� nebudeme um�s�ovat dal�� body. Zobraz� se vypln�n� troj�heln�k.</p>

<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek kreslen� troj�heln�k�</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horn� bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src1">glEnd();<span class="kom">// Ukon�en� kreslen� troj�heln�k�</span></p>

<p>Na lev� stran� obrazovky jsem vykreslili troj�heln�k. Provedeme translaci doprava a um�st�me �tverec. Na ose x se posunujeme o 1.5, abychom dos�hli zp�t st�edu a k tomu p�i�teme dal��ch 1.5 a budeme vpravo. Na os�ch y a z se nep�esunujeme.</p>

<p class="src1">glTranslatef(3.0f,0.0f,0.0f);<span class="kom">// Posun o 3 jednotky doprava</span></p>

<p>K�d pro nakreslen� �tverce je velice podobn� tomu, kter� jsme pou�ili pro troj�heln�k. Jedin� rozd�l v pou�it� GL_QUADS m�sto GL_TRIANGLES je p�id�n� dal��ho vertexu pro �tvrt� bod. Nakresl�me postupn� lev� horn�, prav� horn�, prav� doln� a lev� doln� vrchol.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Lev� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Prav� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen� obd�ln�k�</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Ukon�en� funkce</span></p>
<p class="src0">}</p>

<p>To je pro tuto lekci v�e. Probrali jsme nejjednodu��� kreslen�. Douf�m, �e v�s moc neodradilo - to teprve p�ijde :-]</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: V�clav Slov��ek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson02.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson02.zip">ASM</a> k�d t�to lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson02_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson02.zip">C#</a> k�d t�to lekce. ( <a href="mailto:joachim_rohde@freenet.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson02.zip">VB.Net CsGL</a> k�d t�to lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson02.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson02.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson02.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson02.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson02.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson02.zip">GLUT</a> k�d t�to lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson02.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson02.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson02.zip">Java/SWT</a> k�d t�to lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson02.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson02.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson02.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson02.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson02.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson02.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson02.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson02.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson02.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson02.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson02.zip">Perl</a> k�d t�to lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson02.gz">Python</a> k�d t�to lekce. ( <a href="mailto:hakuin@voicenet.com">John</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/qt_cpp/lesson02.tar.gz">QT/C++</a> k�d t�to lekce. ( <a href="mailto:pmarian@cnlo.ro">Popeanga Marian</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson02.rb.hqx">REALbasic</a> k�d t�to lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson02.zip">Scheme</a> k�d t�to lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson02.zip">Solaris</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson02.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson02.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson02.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(2);?>
<?FceNeHeOkolniLekce(2);?>

<?
include 'p_end.php';
?>
