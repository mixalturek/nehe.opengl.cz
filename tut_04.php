<?
$g_title = 'CZ NeHe OpenGL - Lekce 4 - Rotace';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(4);?>

<h1>Lekce 4 - Rotace</h1>

<p class="nadpis_clanku">Nau��me se, jak ot��et objekt okolo os. Troj�heln�k se bude ot��et kolem osy y a �tverec kolem osy x. Je jednoduch� vytvo�it sc�nu z polygon�. P�id�n� pohybu ji p�kn� o�iv�.</p>

<p>Za�neme p�id�n�m dvou prom�nn�ch pro ulo�en� rotace ka�d�ho objektu. Deklarujeme je jako glob�ln� na za��tku programu. Brzy zjist�te, �e desetinn� ��sla jsou nezbytn� k programov�n� v OpenGL.</p>

<p class="src0">GLfloat rtri;<span class="kom">// �hel pro troj�heln�k</span></p>
<p class="src0">GLfloat rquad;<span class="kom">// �hel pro �tverec</span></p>

<p>P�ep�eme funkci DrawGLScene, kter� slou�� pro vykreslov�n�.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>

<p>N�sleduj�c� ��st k�du je nov�. Funkce glRotatef(uhel, x_vektor, y_vektor, z_vektor) je odpov�dn� za rotaci sou�adnicov�ch os. Budete ji �asto pou��vat. Uhel je ��slo (obvykle ulo�en� v prom�nn�), kter� ur�uje o kolik stup�� chcete oto�it objektem. Parametry x_vektor, y_vektor a z_vektor dohromady se sou�asn�m po��tkem sou�adnic ur�uj� vektor okolo kter�ho se objekt bude ot��et.</p>
<p>Pro lep�� vysv�tlen� uvedu vysv�tlen� na p��kladech: <b>Osa x</b> - P�edstavte si, �e stoj�te u vodorovn� desky a chcete j� oto�it. Pokud zad�te kladnou rotaci [1,0,0] zved� se vzd�len�j�� ��st desky a bli��� kles�. P�i z�porn� rotaci [-1,0,0] je to naopak. <b>Osa y</b> - Op�t stejn� deska. P�i kladn� rotaci [0,1,0] se prav� ��st desky pohybuje od v�s a lev� ��st desky k v�m. P�i z�porn� rotaci [0,-1,0] je to naopak. <b>Osa z</b> - Op�t stejn� deska. P�i kladn� rotaci [0,0,1] se zved� prav� ��st desky a lev� kles� a p�i z�porn� [0,0,-1] je to naopak.</p>

<p>Pokud bude rtri 7, n�sleduj�c�m ��dkem pooto��me troj�heln�kem o 7� okolo osy y proti sm�ru hodinov�ch ru�i�ek.</p>

<p class="src1">glRotatef(rtri,0.0f,1.0f,0.0f);<span class="kom">// Oto�� troj�heln�k okolo osy y</span></p>

<p>Dal�� ��st k�du z�st�v� nezm�n�na. Vykresl� vybarven� troj�heln�k. Tentokr�t ale d�ky p�edch�zej�c�mu ��dku pooto�en� okolo osy y.</p>

<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek kreslen� troj�heln�k�</span></p>
<p class="src"></p>
<p class="src2">glColor3f(1.0f, 0.0f, 0.0f);<span class="kom">// �erven� barva</span></p>
<p class="src2">glVertex3f(0.0f, 1.0f, 0.0f);<span class="kom">// Horn� bod</span></p>
<p class="src2">glColor3f(0.0f, 1.0f, 0.0f);<span class="kom">// Zelen� barva</span></p>
<p class="src2">glVertex3f(-1.0f, -1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src2">glColor3f(0.0f, 0.0f, 1.0f);<span class="kom">// Modr� barva</span></p>
<p class="src2">glVertex3f(1.0f, -1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Ukon�en� kreslen� troj�heln�k�</span></p>

<p>Jist� si v n�sleduj�c�m k�du v�imnete, �e jsme p�idali dal�� vol�n� funkce glLoadIdentity(). Proto�e byly osy pooto�eny neukazuj� do sm�r�, kter� p�edpokl�d�te. Tak�e pokud posouv�me okolo osy x, m��eme skon�it posouv�n�m ve sm�ru p�vodn� osy z, z�le�� na tom, jak moc jsme pooto�ili. Zkuste odstranit ��dek s vol�n�m glLoadIdentity(), a� vid�te co mysl�m. Jakmile m�me resetov�no m��� osy op�t p�vodn�mi sm�ry, tj. x - zleva doprava, y - zdola nahoru, z - z obrazovky k v�m. V�imn�te si, �e posouv�me jen o 1,5 na ose x narozd�l od posunu o 3 z p�edchoz� ��sti. Kdy� resetujeme, posune se po��tek zp�tky do st�edu obrazovky.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(1.5f,0.0f,-6.0f);<span class="kom">// Posun po��tku</span></p>
<p class="src"></p>
<p class="src1">glRotatef(rquad,1.0f,0.0f,0.0f);<span class="kom">// Pooto�en� �tverce okolo osy x</span></p>

<p>Vykreslen� je stejn� jako v p�edch�zej�c� ��sti. Op�t vykresl� �tverec, ale tentokr�t pooto�en�.</p>

<p class="src1">glColor3f(0.5f,0.5f,1.0f);<span class="kom">// Sv�tle modr� barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Lev� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Prav� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen� obd�ln�k�</span></p>

<p>Po ka�d�m zobrazen� se zm�n� prom�nn� rtri a rquad, ve kter�ch jsou ulo�eny hodnoty pooto�en� troj�heln�ku a �tverce. Zm�nou znam�nka m��ete zm�nit smysl rotace. Zm�nou velikosti p�i��tan�ch hodnot m��ete zm�nit rychlost rotace.</p>

<p class="src1">rtri+=0.2f;<span class="kom">// Inkrementace �hlu pooto�en� troj�heln�ku</span></p>
<p class="src1">rquad-=0.15f;<span class="kom">// Inkrementace �hlu pooto�en� �tverce</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Ukon�en� funkce</span></p>
<p class="src0">}</p>

<p>Douf�m, �e jste pochopili, �e se v�e vykresluje po��d stejn�, sou�adnice bod� se nikdy nem�n�. Poka�d� se pouze li�� po��tek sou�adnicov�ch os, jejich nato�en� nebo m���tko - glScalef(x,y,z). Pokud chcete, aby se objekt ot��el okolo sv� osy, um�st�te ho okolo po��tku nebo alespo� na jednu ze sou�adnicov�ch os - tato lekce. P�i jin�m um�st�n� bude chaoticky l�tat po sc�n�.</p>

<p>Tak� je d�le�it�, zda nap�ed provedete translaci nebo rotaci. P�i po��te�n�m pooto�en� budou osy sm��ovat jinam ne� o�ek�v�te a n�sledn� posunut� skon�� �pln� n�kde jinde ne� chcete.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson04.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson04.zip">ASM</a> k�d t�to lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson04_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson04.zip">C#</a> k�d t�to lekce. ( <a href="mailto:sugarbee@gmx.net">Sabine Felsinger</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson04.zip">VB.Net CsGL</a> k�d t�to lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson04.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson04.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson04.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson04.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson04.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson04.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/genu/lesson04.zip">Genu</a> k�d t�to lekce. ( <a href="mailto:lcdumais@hotmail.com">Louis-Charles Dumais</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson04.zip">GLUT</a> k�d t�to lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson04.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson04.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson04.zip">Java/SWT</a> k�d t�to lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson04.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson04.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson04.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson04.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson04.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson04.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson04.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson04.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson04.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson04.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson04.zip">Perl</a> k�d t�to lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson04.gz">Python</a> k�d t�to lekce. ( <a href="mailto:hakuin@voicenet.com">John</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson04.rb.hqx">REALbasic</a> k�d t�to lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson04.zip">Scheme</a> k�d t�to lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson04.zip">Solaris</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson04.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson04.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson04.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(4);?>
<?FceNeHeOkolniLekce(4);?>

<?
include 'p_end.php';
?>
