<?
$g_title = 'CZ NeHe OpenGL - Lekce 18 - Quadratics';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(18);?>

<h1>Lekce 18 - Kvadriky</h1>

<p class="nadpis_clanku">P�edstavuje se v�m b�je�n� sv�t kvadrik�. Jedn�m ��dkem k�du snadno vytv���te komplexn� objekty typu koule, disku, v�lce ap. Pomoc� matematiky a trochy pl�nov�n� lze snadno morphovat jeden do druh�ho.</p>

<p>Quadratic (nezn�m �esk� ekvivalent slova, tak�e z�stanu u p�vodn� verze) je jednoduchou cestou k vykreslen� komplexn�ch objekt�. Na pozad� pracuj� na n�kolika cyklech for a tro�e trigonometrie. Rozvineme k�d z lekce 7, p�id�me p�r prom�nn�ch a aby byla tak� n�jak� zm�na, pou�ijeme jinou texturu</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
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
<p class="src"></p>
<p class="src0">bool light;<span class="kom">// Sv�tlo ON/OFF</span></p>
<p class="src0">bool lp;<span class="kom">// L stisknut�? </span></p>
<p class="src0">bool fp;<span class="kom">// F stisknut�? </span></p>
<p class="src0">bool sp;<span class="kom">// Stisknut� mezern�k?</span></p>
<p class="src"></p>
<p class="src0">int part1;<span class="kom">// Za��tek disku</span></p>
<p class="src0">int part2;<span class="kom">// Konec disku</span></p>
<p class="src0">int p1=0;<span class="kom">// P��r�stek 1</span></p>
<p class="src0">int p2=1;<span class="kom">// P��r�stek 2</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot;<span class="kom">// X rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y rotace</span></p>
<p class="src0">GLfloat xspeed;<span class="kom">// Rychlost x rotace</span></p>
<p class="src0">GLfloat yspeed;<span class="kom">// Rychlost y rotace</span></p>
<p class="src0">GLfloat z=-5.0f;<span class="kom">// Hloubka v obrazovce</span></p>
<p class="src"></p>
<p class="src0">GLUquadricObj *quadratic;<span class="kom">// Bude ukl�dat kvadrik</span></p>
<p class="src0"></p>
<p class="src"></p>
<p class="src0">GLfloat LightAmbient[]= { 0.5f, 0.5f, 0.5f, 1.0f };<span class="kom">// Okoln� sv�tlo</span></p>
<p class="src0">GLfloat LightDiffuse[]= { 1.0f, 1.0f, 1.0f, 1.0f };<span class="kom">// P��m� sv�tlo</span></p>
<p class="src0">GLfloat LightPosition[]= { 0.0f, 0.0f, 2.0f, 1.0f };<span class="kom">// Pozice sv�tla</span></p>
<p class="src"></p>
<p class="src0">GLuint filter;<span class="kom">// Typ filtru</span></p>
<p class="src0">GLuint texture[3];<span class="kom">// M�sto pro 3 textury</span></p>
<p class="src0">GLuint object=0;<span class="kom">// Ur�uje aktu�ln� vykreslovan� objekt</span></p>

<p>P�esuneme se do funkce InitGL(), kde inicializujeme kvadrik. Na konec, ale p�ed return, p�id�me n�sleduj�c� k�d t�to lekce. ( V prvn�m ��dku vytvo��me nov� kvadrik (funkce na n�j vr�t� ukazatel, p�i chyb� nulu). Aby sv�tlo vypadalo opravdu perfektn� nastav�me norm�lov� vektory na GLU_SMOOTH (dal�� mo�n� hodnoty GLU_NONE a GLU_FLAT). Nakonec zapneme texturov� mapov�n�. Je celkem "neohraban�", proto�e nem��eme napl�novat, co kam namapujeme - v�echno se generuje automaticky.</p>

<p class="src0">quadratic=gluNewQuadric();<span class="kom">// Vr�t� ukazatel na nov� kvadrik</span></p>
<p class="src0">gluQuadricNormals(quadratic, GLU_SMOOTH);<span class="kom">// Vygeneruje norm�lov� vektory (hladk�)</span></p>
<p class="src0">gluQuadricTexture(quadratic, GL_TRUE);<span class="kom">// Vygeneruje texturov� koordin�ty</span></p>

<p>Rozhodl jsem se, �e p�vodn� krychli z lekce 7 nesma�u, ale �e ji zde ponech�m. M�li byste si uv�domit, �e stejn� jako mapujeme textury na n�mi vytvo�en� objekt, tak se �pln� stejn� mapuj� na kvadriky.</p>

<p class="src0">GLvoid glDrawCube()<span class="kom">// Vykresl� krychli</span></p>
<p class="src0">{</p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// P�edn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Zadn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Vrchn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 1.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Spodn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f,-1.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Prav� st�na</span></p>
<p class="src2">glNormal3f( 1.0f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Lev� st�na</span></p>
<p class="src2">glNormal3f(-1.0f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>Ve funkci DrawGLScene() se program v�tv� podle druhu objektu, kter� chceme kreslit (ku�el, v�lec, koule...). Do v�ech funkc� zaji��uj�c�ch vykreslov�n� (krom� na�� krychle) se p�id�v� parametr &quot;quadratic&quot;.</p>

<p class="src0">int DrawGLScene(GLvoid)</p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,z);</p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);</p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter]);<span class="kom">// Vybere texturu</span></p>
<p class="src"></p>
<p class="src1">switch(object)<span class="kom">// Vybere, co se bude kreslit</span></p>
<p class="src1">{</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_krychle.jpg" width="127" height="129" alt="Krychle" /></div>

<p class="src2">case 0:</p>
<p class="src3">glDrawCube();<span class="kom">// Krychle</span></p>
<p class="src3">break;</p>

<p>Dal��m objektem bude v�lec. Prvn�m parametrem je spodn� polom�r. Druh� ur�uje horn� polom�r. P�ed�n�m rozd�ln�ch hodnot se vykresl� jin� tvar (zu�uj�c� trubka, pop�. ku�el). T�et� parametr specifikuje v��ku/d�lku (vzd�lenost z�kladen). �tvrt� hodnota zna�� mno�stv� polygon� "kolem" osy Z a p�t� po�et polygon� &quot;na&quot; ose Z. Nap��klad pou�it�m 5 m�sto prvn� 32 nevykresl�te v�lec, ale hranatou trubku, jej� podstava je tvo�ena pravideln�m p�ti�heln�kem. Naopak rozd�l p�i z�m�n� druh� 32 snad ani nepozn�te. ��m je t�chto polygon� v�ce, t�m se zv�t�� kvalita (po�et detail�) v�stupu. Mus�m ale podtrhnout, �e se program zpomal�. Sna�te se v�dy naj�t n�jakou rozumnou hodnotu.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_valec.jpg" width="127" height="129" alt="V�lec" /></div>

<p class="src2">case 1:</p>
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrov�n� v�lce</span></p>
<p class="src3">gluCylinder(quadratic,1.0f,1.0f,3.0f,32,32);<span class="kom">// V�lec</span></p>
<p class="src3">break;</p>

<p>T�et�m vytv��en�m objektem bude disk tvaru CD. Prvn� parametr ur�uje vnit�n� polom�r - pokud zad�te nulu vykresl� se celistv� (bez st�edov�ho kruhu). Druhu hodnotou je vn�j�� polom�r (zad�-li se o m�lo v�t�� ne� vnit�n� vytvo��te prsten). Dejte si pozor, abyste nezadali vn�j�� men�� ne� vnit�n�. Nespadne v�m sice program, ale nic neuvid�te. T�et�m parametrem je po�et pl�tk�, jako kdy� se kr�j� pizza. ��m jich bude v�ce, t�m budou okraje m�n� zubat� (nap�i. zad�n�m 5 vykresl�te pravideln� p�ti�heln�k). Posledn�m p�ed�van� ��slo zna�� po�et kru�nic - analogie spir�le na CD nebo gramofonov� desce. Op�t nem� moc velk� vliv na kvalitu.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_disk.jpg" width="127" height="129" alt="Disk" /></div>

<p class="src2">case 2:</p>
<p class="src3">gluDisk(quadratic,0.5f,1.5f,32,32);<span class="kom">// Disk ve tvaru CD</span></p>
<p class="src3">break;</p>

<p>N�sleduje objekt, o kter�m p�em��l�te v dlouh�ch bezesn�ch noc�ch... koule. Sta�� jedna funkce. Nejd�le�it�j��m parametrem je polom�r - net�eba vysv�tlovat. Pokud byste ale cht�li j�t je�t� d�l, zm��te p�ed vykreslen�m m���tko jednotliv�ch os (glScalef(x,y,z)). Vytvo��te zaoblen� tvar, kter� mi v prvn� chv�li p�ipom�nal ozdobu na strome�ek (�i�ka - zplo�t�l� koule). Pop�. zkuste zmen�it prvn� 32 na 5. Vytvo��te hranatou (krychloidn� :-o) kouli. Jak to popsat... kdybyste ji p�es st�ed rozd�lili rovinou, �ezem bude p�ti�heln�k, ale druh�m �ezem kolm�m na prvn� bude st�le koule.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_koule.jpg" width="127" height="129" alt="Koule" /></div>

<p class="src2">case 3:</p>
<p class="src3"><span class="kom">// glScalef(1.0f,0.5f,1.0f);// P�ekl.: Zm�na m���tka</span></p>
<p class="src3">gluSphere(quadratic,1.3f,32,32);<span class="kom">// Koule</span></p>
<p class="src3"><span class="kom">// glScalef(1.0f,2.0f,1.0f);// P�ekl.: Obnoven� m���tka</span></p>
<p class="src3">break;</p>

<p>U� jsem trochu nakousl u v�lce, �e ku�el se vytv��� t�m�� stejn�. P�ed�te jeden polom�r rovn� nule, tud� se na jednom konci objev� �pi�ka.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_kuzel.jpg" width="127" height="129" alt="Ku�el" /></div>

<p class="src2">case 4:</p>
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrov�n� ku�ele</span></p>
<p class="src3">gluCylinder(quadratic,1.0f,0.0f,3.0f,32,32);<span class="kom">// Ku�el</span></p>
<p class="src3">break;</p>

<p>�est� tvar vytvo��me p��kazem gluPartialDisk(). Tento disk bude skoro stejn� jako disk v��e, nicm�n� dal�� dva parametry funkce zajist�, �e se nebude vykreslovat cel�. Parametr part1 specifikuje po��te�n� �hel, od kter�ho chceme kreslit a asi si domysl�te, �e ten druh� ur�uje �hel, za kter�m se u� nic nevykresl�. Je vzta�en k tomu prvn�mu, tak�e pokud prvn� nastav�me na 30 a druh� na 90 p�estane se kreslit na 30� + 90� = 120�. My se rovnou pokus�me o "level 2" - zkus�me p�idat jednoduchou animaci, kdy se disk bude p�ekreslovat (po sm�ru hodinov�ch ru�i�ek). Nejd��ve zvy�ujeme p��r�stkov� �hel. Jakmile dos�hne 360� (jeden ob�h), za�neme zvy�ovat po��te�n� �hel - op�t do 360� atd.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_part_disk.jpg" width="127" height="129" alt="��ste�n� disk" /></div>

<p class="src2">case 5:</p>
<p class="src3">part1+=p1;<span class="kom">// Inkrementace po��te�n�ho �hlu</span></p>
<p class="src3">part2+=p2;<span class="kom">// Inkrementace p��r�stkov�ho �hlu</span></p>
<p class="src"></p>
<p class="src3">if(part1&gt;359)<span class="kom">// 360�</span></p>
<p class="src3">{</p>
<p class="src4">p1=0;<span class="kom">// Zastav� zv�t�ov�n� po��te�n�ho �hlu (part1+=0;)</span></p>
<p class="src4">part1=0;<span class="kom">// Vynulov�n� po��te�n�ho �hlu</span></p>
<p class="src4">p2=1;<span class="kom">// Za�ne zv�t�ovat p��r�stkov� �hel</span></p>
<p class="src4">part2=0;<span class="kom">// Vynulov�n� p��r�stkov�ho �hlu</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if(part2&gt;359)<span class="kom">// 360�</span></p>
<p class="src3">{</p>
<p class="src4">p1=1;<span class="kom">// Za�ne zv�t�ovat po��te�n� �hel</span></p>
<p class="src4">p2=0;<span class="kom">// P�estane zv�t�ovat p��r�stkov� �hel</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">gluPartialDisk(quadratic,0.5f,1.5f,32,32,part1,part2-part1);<span class="kom">// Ne�pln� disk</span></p>
<p class="src3">break;</p>
<p class="src1">};</p>
<p class="src"></p>
<p class="src1">xrot+=xspeed;<span class="kom">// Inkrementace rotace</span></p>
<p class="src1">yrot+=yspeed;<span class="kom">// Inkrementace rotace</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>P�id�me ovl�d�n� kl�vesnic� - pokud stisknete mezern�k objekt se zm�n� na n�sleduj�c� v po�ad�.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src1">if(keys[' '] &amp;&amp; !sp)<span class="kom">// Stisknut� mezern�k?</span></p>
<p class="src1">{</p>
<p class="src2">sp=TRUE;</p>
<p class="src2">object++;<span class="kom">// Cyklov�n� objekty</span></p>
<p class="src2">if(object>5)<span class="kom">// O�et�en� p�ete�en�</span></p>
<p class="src3">object=0;</p>
<p class="src1">}</p>
<p class="src1">if(!keys[' '])<span class="kom">// Uvoln�n� mezern�ku?</span></p>
<p class="src1">{</p>
<p class="src2">sp=FALSE;</p>
<p class="src1">}</p>

<p>Tak�e to je v�e. M�li byste um�t v OpenGL vykreslovat jak�koli kvadrik. Pomoc� morphingu a kvadrik� se d� dos�hnout zaj�mav�ch efekt�. P��kladem budi� n�mi animovan� disk.</p>

<p class="autor">napsal: <?OdkazBlank('http://www.tiptup.com/', 'GB Schmick - TipTup');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson18.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson18_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson18.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson18.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson18.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson18.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson18.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson18.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson18.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson18.tar.gz">Irix / GLUT</a> k�d t�to lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson18.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson18.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson18.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson18.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:rgbe@yahoo.com">Simon Werner</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson18.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:miqster@gmx.net">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson18.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson18.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson18.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson18.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:chris.j84@free.fr">Christophe</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson18.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/sdl/lesson18.tar.gz">SDL</a> k�d t�to lekce. ( <a href="mailto:kjrockot@home.com">Ken Rockot</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson18.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:thegilb@hotmail.com">The Gilb</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson18.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(18);?>
<?FceNeHeOkolniLekce(18);?>

<?
include 'p_end.php';
?>
