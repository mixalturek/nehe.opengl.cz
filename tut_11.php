<?
$g_title = 'CZ NeHe OpenGL - Lekce 11 - Efekt vln�c� se vlajky';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(11);?>

<h1>Lekce 11 - Efekt vln�c� se vlajky</h1>

<p class="nadpis_clanku">Nau��me se jak pomoc� sinusov� funkce animovat obr�zky. Pokud zn�te standartn� �et�i� Windows &quot;L�taj�c� 3D objekty&quot; (i on by m�l b�t programovan� v OpenGL), tak budeme d�lat n�co podobn�ho.</p>

<p>Budeme vych�zet z �est� lekce. Neopisuji cel� zdrojov� k�d, tak�e mo�n� bude lep��, kdy� budete m�t n�kde po ruce i zdrojov� k�d ze zmi�ovan� lekce. Prvn� v�c, kterou mus�te ud�lat je vlo�it hlavi�kov� soubor matematick� knihovny. Nebudeme pracovat s moc slo�itou matematikou, nebojte se, pou�ijete pouze siny a kosiny.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavi�kov� soubor pro matematickou knihovnu</span></p>
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

<p>Deklarujte trojrozm�rn� pole bod� k ulo�en� sou�adnic v jednotliv�ch os�ch. Wiggle_count se pou�ije k nastaven� a n�sledn�mu zji��ov�n�, jak rychle se bude textura vlnit. Prom�nn� hold zajist� plynul� vln�n� textury.</p>

<p class="src0">float points[45][45][3];<span class="kom">// Pole pro body v m��ce vlny</span></p>
<p class="src0">int wiggle_count = 0;<span class="kom">// Rychlost vln�n�</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot;<span class="kom">// Rotace na ose x</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Rotace na ose y</span></p>
<p class="src0">GLfloat zrot;<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src0">GLfloat hold;<span class="kom">// Pomocn�, k zaji�t�n� plynulosti pohybu</span></p>
<p class="src0">GLuint texture[1];<span class="kom">// Ukl�d� texturu</span></p>

<p>P�esu�te se dol� k funkci LoadGLTexture(). Budete pou��vat novou texturu s n�zvem Tim.bmp, tak�e najd�te funkci LoadBMP("Data/NeHe.bmp") a p�epi�te ji tak, aby nahr�vala nov� obr�zek.</p>

<p class="src1">if(TextureImage[0]=LoadBMP("Data/Tim.bmp"))<span class="kom">// Loading bitmapy</span></p>

<p>Te� p�idejte n�sleduj�c� k�d na konec funkce InitGL(). V�sledek uvid�te na prvn� pohled. P�edn� strana textury bude norm�ln� vybarven�, ale jak se po chv�li obr�zek nato��, zjist�te, �e ze zadn� strany zbyl dr�t�n� model. GL_FILL ur�uje klasick� kreslen� polygony, GL_LINES vykresluje pouze okrajov� linky, p�i GL_POINTS by �lo vid�t pouze vrcholov� body. Kter� strana polygonu je p�edn� a kter� zadn� nelze ur�it jednozna�n�, sta�� rotace a u� je to naopak. Proto vznikla konvence, �e mnoho�heln�ky, u kter�ch byly p�i vykreslov�n� zad�ny vrcholy proti sm�ru hodinov�ch ru�i�ek jsou p�ivr�cen�.</p>

<p class="src0"><span class="kom">// Konec funkce InitGL()</span></p>
<p class="src1">glPolygonMode(GL_BACK, GL_FILL);<span class="kom">// P�edn� strana vypln�n� polygony</span></p>
<p class="src1">glPolygonMode(GL_FRONT, GL_LINE);<span class="kom">// Zadn� strana vypln�n� m��kou</span></p>

<p>N�sleduj�c� dva cykly inicializuj� na�i s�. Abychom dostali spr�vn� index mus�me d�lit ��d�c� prom�nou smy�ky p�ti (tzn. 45/9=5). Od��t�m 4,4 od ka�d� sou�adnice, aby se vlna vycentrovala na po��tku sou�adnic. Stejn�ho efektu m��e b�t dosa�eno s pomoc� posunut�, ale j� m�m rad�i tuto metodu. Hodnota points[x][y][2] je tvo�en� hodnotou sinu. Funkce sin() pot�ebuje radi�ny, tud� vezmeme hodnotu ve stupn�ch, co� je na�e x/5 n�soben� �ty�iceti a pomoc� vzorce  (radi�ny=2*P�*stupn�/360) ji p�epo��t�me.</p>

<p class="src1">for (int x=0; x&lt;45; x++)<span class="kom">// Inicializace vlny</span></p>
<p class="src1">{</p>
<p class="src2">for (int y=0; y&lt;45; y++)</p>
<p class="src2">{</p>
<p class="src3">points[x][y][0]=float((x/5.0f)-4.5f);</p>
<p class="src3">points[x][y][1]=float((y/5.0f)-4.5f);</p>
<p class="src3">points[x][y][2]=float(sin((((x/5.0f)*40.0f)/360.0f)*3.141592654*2.0f));</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Na za��tku vykreslovac� funkce deklarujeme prom�nn�. Jsou pou�ity jako ��d�c� v cykl�. Uvid�te je v k�du n�, ale v�t�ina z nich neslou�� k n��emu jin�mu ne�, �e kontroluj� cykly a ukl�daj� do�asn� hodnoty</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">int x, y;</p>
<p class="src1">float float_x, float_y, float_xb, float_yb;</p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-12.0f);<span class="kom">// Posunut� do obrazovky</span></p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src1">glRotatef(zrot,0.0f,0.0f,1.0f);<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// V�b�r textury</span></p>

<p>V�imn�te si, �e �tverce jsou kresleny <b>po</b> sm�ru hodinov�ch ru�i�ek. Z toho plyne, �e �eln� plocha, kterou vid�te bude vypln�n� a zezadu bude dr�t�n� model. Pokud bychom �tverce vykreslovali <b>proti</b> sm�ru hodinov�ch ru�i�ek dr�t�n� model by byl na p�edn� stran�.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� �tverc�</span></p>
<p class="src"></p>
<p class="src2">for( x = 0; x &lt; 44; x++ )<span class="kom">// Cykly proch�zej� pole</span></p>
<p class="src2">{</p>
<p class="src3">for( y = 0; y &lt; 44; y++ )</p>
<p class="src3">{</p>

<p>Ka�d� z polygon� (�tverce v s�ti) m� 1/44x1/44 textury. Cyklus ur�uje lev� doln� bod (prvn� 2 ��dky). Pot� spo��t�me prav� horn� (dal�� 2 ��dky). Tak�e m�me dva body na �hlop���ce �tverce a kombinac� hodnot jejich sou�adnic z�sk�me zbyl� dva body na textu�e.</p>

<p class="src4"><span class="kom">// Vypo��t�n� texturov�ch koordin�t�</span></p>
<p class="src4">float_x = float(x)/44.0f;</p>
<p class="src4">float_y = float(y)/44.0f;</p>
<p class="src4">float_xb = float(x+1)/44.0f;</p>
<p class="src4">float_yb = float(y+1)/44.0f;</p>
<p class="src"></p>
<p class="src4"><span class="kom">// Zad�n� jednotliv�ch bod�</span></p>
<p class="src4">glTexCoord2f(float_x, float_y);</p>
<p class="src4">glVertex3f(points[x][y][0], points[x][y][1], points[x][y][2]);</p>
<p class="src"></p>
<p class="src4">glTexCoord2f(float_x, float_yb);</p>
<p class="src4">glVertex3f(points[x][y+1][0], points[x][y+1][1], points[x][y+1][2]);</p>
<p class="src"></p>
<p class="src4">glTexCoord2f(float_xb, float_yb);</p>
<p class="src4">glVertex3f(points[x+1][y+1][0], points[x+1][y+1][1], points[x+1][y+1][2]);</p>
<p class="src"></p>
<p class="src4">glTexCoord2f(float_xb, float_y);</p>
<p class="src4">glVertex3f(points[x+1][y][0], points[x+1][y][1], points[x+1][y][2]);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen� �tverc�</span></p>

<p>P�i sud�m vykreslen� v po�ad� p�esuneme sou�adnice v poli do sousedn�ch sou�adnic a t�m p�esuneme i vlnu o kousek vedle. Cel� prvn� sloupec (vn�j�� cyklus) postupn� ukl�d�me do pomocn� prom�nn�. Potom o kousek p�esuneme vlnu jednoduch�m p�i�azen�m ka�d�ho prvku do  sousedn�ho a nakonec p�i�ad�me ulo�enou hodnotu okraje na opa�n� konec obr�zku. T�m vznik� dojem, �e kdy� miz� jedna vlna, okam�it� za��n� vznikat nov�, ale programov� je to konec t� star� :-] Zjednodu�en� �e�eno m�me jen jednu vlnu, kter� se po opu�t�n� obr�zku p�esouv� na za��tek. Nakonec vynulujeme wiggle_count, abychom udr�eli animaci v chodu.</p>

<p class="src1">if (wiggle_count == 2)<span class="kom">// Pro sn�en� rychlosti pohybu</span></p>
<p class="src1">{</p>
<p class="src2">for (y = 0; y &lt; 45; y++)<span class="kom">// Proch�z� hodnoty na y</span></p>
<p class="src2">{</p>
<p class="src3">hold=points[0][y][2];<span class="kom">// Ulo�� kraj vlny</span></p>
<p class="src"></p>
<p class="src3">for (x = 0; x &lt; 44; x++)<span class="kom">// Proch�z� hodnoty na x</span></p>
<p class="src3">{</p>
<p class="src4">points[x][y][2] = points[x+1][y][2];<span class="kom">// P�i�azen� do sousedn�ho prvku</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">points[44][y][2]=hold;<span class="kom">// Ulo�en� kraj bude na druh� stran�</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">wiggle_count = 0;<span class="kom">// Nulov�n� po��tadla vykreslov�n�</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">wiggle_count++;<span class="kom">// Inkrementace po��tadla</span></p>

<p>Aktualizujeme rotaci a ukon��me funkci.</p>

<p class="src1">xrot+=0.3f;</p>
<p class="src1">yrot+=0.2f;</p>
<p class="src1">zrot+=0.4f;</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Zkompilujte a spus�te program. Z p�edn� strany byste m�li vid�t hezkou vln�c� se bitmapu a po n�sledn�m nato�en� z n� z�stane pouze dr�t�n� model.</p>

<p class="autor">napsal: Bosco <?VypisEmail('bosco4@home.com');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kodye_download">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson11.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson11_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson11.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson11.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson11.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson11.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson11.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson11.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson11.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson11.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson11.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson11.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson11.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:pepijn.vaneeckhoudt@luciad.com">Pepijn Van Eeckhoudt</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson11.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson11.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson11.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson11.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson11.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson11.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson11.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson11.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson11.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson11.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson11.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(11);?>
<?FceNeHeOkolniLekce(11);?>

<?
include 'p_end.php';
?>
