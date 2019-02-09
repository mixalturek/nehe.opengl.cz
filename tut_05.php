<?
$g_title = 'CZ NeHe OpenGL - Lekce 5 - Pevn� objekty';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(5);?>

<h1>Lekce 5 - Pevn� objekty</h1>

<p class="nadpis_clanku">Roz���en�m posledn� ��sti vytvo��me skute�n� 3D objekty. Narozd�l od 2D objekt� ve 3D prostoru. Zm�n�me troj�heln�k na pyramidu a �tverec na krychli. Pyramida bude vybarvena barevn�m p�echodem a ka�dou st�nu krychle vybarv�me jinou barvou.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>
<p class="src1">glRotatef(rtri,0.0f,1.0f,0.0f);<span class="kom">// Oto�� pyramidu okolo osy y</span></p>

<p>��st k�du vezmeme z p�edchoz� ��sti a vyrob�me pomoc� n�j 3D objekt. Je jedna v�c na kterou jsem �asto dotazov�n. Pro� se objekty neot��ej� okolo sv� osy. Vypad� to jako by se l�taly po cel� obrazovce. Pokud objektu �eknete, aby se oto�il okolo osy, oto�� se okolo osy sou�adnicov�ho syst�mu. Pokud chcete, aby se rotoval okolo sv� osy, mus�te d�t po��tek sou�adnic do jeho st�edu nebo aspo� tak, aby se sou�adnicov� osa, okolo kter� ot���te, kryla s osou objektu okolo kter� chcete oto�it.</p>

<p>N�sleduj�c� k�d vytvo�� pyramidu okolo centr�ln� osy. Vrcholek pyramidy je o jednu naho�e od st�edu, spodek o jednu dol�. Vrchn� bod je vpravo uprost�ed a doln� body jsou vpravo a vlevo od st�edu. V�imn�te si, �e v�echny troj�heln�ky jsou kresleny ve sm�ru proti hodinov�m ru�i�k�m. Je to d�le�it� a bude to vysv�tleno v dal��ch lekc�ch - nap�. lekce 11. Te� si pouze zapamatujte, �e je dobr� kreslit po sm�ru nebo proti sm�ru hodinov�ch ru�i�ek a pokud k tomu nem�te d�vod, nem�li byste dv� osy prohodit. Za�neme kreslen�m �eln� st�ny. Proto�e v�echny sd�l� horn� bod, ud�l�me jej u v�ech st�n �erven�. Barvy na spodn�ch vrcholech se budou st��dat. �eln� st�na bude m�t lev� bod zelen� a prav� modr�. Troj�heln�k na prav� stran� bude m�t lev� bod modr� a prav� zelen�. Prohozen�m doln�ch dvou barev na ka�d� st�n� ud�l�me spole�n� vybarven� vrcholy na spodku ka�d� st�ny.</p>

<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek kreslen� pyramidy</span></p>
<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// �erven�</span></p>
<p class="src2">glVertex3f(0.0f,1.0f,0.0f);<span class="kom">// Horn� bod (�eln� st�na)</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelen�</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Lev� spodn� bod (�eln� st�na)</span></p>
<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modr�</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Prav� spodn� bod (�eln� st�na)</span></p>

<p>Vykresl�me pravou st�nu. Spodn� body kresl�me vpravo od st�edu a horn� bod je kreslen o jedna na ose y od st�edu a prav� st�ed na ose x. To zp�sobuje, �e se st�na sva�uje od horn�ho bodu doprava dol�. Lev� bod je tentokr�t modr� stejn� jako prav� doln� bod �eln� st�ny, ke kter�mu p�il�h�.
Zbyl� t�i troj�heln�ky kresleny ve stejn�m glBegin(GL_TRIANGLES) a glEnd() jako prvn� troj�heln�k. Proto�e d�l�m cel� objekt z troj�heln�k�, OpenGL v�, �e ka�d� t�i body tvo�� troj�heln�k. Jakmile nakresl�te t�i body a p�id�te dal�� body, OpenGL p�edpokl�d�, �e je t�eba kreslit dal�� troj�heln�k. Pokud zad�te �ty�i body m�sto t��, OpenGL pou�ije prvn� t�i a bude p�edpokl�dat, �e �tvrt� je za��tek dal��ho troj�heln�ku. Nevykresl� �tverec. Proto si d�vejte pozor, aby jste n�hodou nep�idali n�jak� bod nav�c.</p>

<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// �erven�</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horn� bod (prav� st�na)</span></p>
<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modr�</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Lev� bod (prav� st�na)</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelen�</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, -1.0f);<span class="kom">// Prav� bod (prav� st�na)</span></p>

<p>Te� vykresl�me zadn� st�nu. Op�t prohozen� barev. Lev� bod je op�t zelen�, proto�e odpov�daj�c� prav� bod je zelen�.</p>

<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// �erven�</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horn� bod (zadn� st�na)</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelen�</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, -1.0f);<span class="kom">// Lev� bod (zadn� st�na)</span></p>
<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modr�</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, -1.0f);<span class="kom">// Prav� bod (zadn� st�na)</span></p>

<p>Nakonec nakresl�me levou st�nu pyramidy. Proto�e pyramida rotuje okolo osy Y, nikdy neuvid�me podstavu. Pokud chcete experimentovat, zkuste p�idat ji p�idat. Potom pooto�te pyramidu okolo osy x a uvid�te zda se v�m to povedlo.</p>


<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// �erven�</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horn� bod (lev� st�na)</span></p>
<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modr�</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f,-1.0f)<span class="kom">// Lev� bod (lev� st�na)</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelen�</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Prav� bod (lev� st�na)</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen� pyramidy</span></p>

<p>Te� vykresl�me krychli. Je tvo�ena �esti �tverci, kter� jsou kresleny op�t proti sm�ru hodinov�ch ru�i�ek. To znamen�, �e prvn� bod je prav� horn�, druh� lev� horn�, t�et� lev� doln� a �tvrt� prav� doln�. Kdy� kresl�me zadn� st�nu, m��e to vypadat, �e kresl�me ve sm�ru hodinov�ch ru�i�ek, ale pamatujte, �e jsme za krychl� a d�v�me se sm�rem k �eln� st�n�. Tak�e lev� strana obrazovky je pravou stranou �tverce. Tentokr�t posouv�me krychli trochu d�l. T�m velikost v�ce odpov�d� velikosti pyramidy a ��sti mohou b�t o��znuty okraji obrazovky. M��ete si pohr�t s nastaven�m po��tku a uvid�te, �e posunut�m d�le se zd� men�� a naopak. D�vodem je perspektiva. Vzd�len�j�� objekty se zdaj� men��.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(1.5f,0.0f,-7.0f);<span class="kom">// Posun po��tku vpravo a dozadu</span></p>
<p class="src1">glRotatef(rquad,1.0f,1.0f,1.0f);<span class="kom">// Rotace okolo x, y, a z</span></p>

<p>Za�neme kreslen�m vrcholku krychle. V�imn�te si, �e sou�adnice y je v�dy jedna. T�m kresl�me st�nu rovnob�n� s rovinou xz. Za�neme prav�m horn�m bodem. Ten je o jedna vpravo a o jedna dozadu. Dal�� bod je o jedna vlevo a o jedna dozadu. Pot� vykresl�me spodn� ��st �tverce sm�rem k pozorovateli. Abychom toho dos�hli, narozd�l od posunu do obrazovky, posuneme se o jeden bod z obrazovky.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� krychle</span></p>
<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Modr�</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f,-1.0f);<span class="kom">// Prav� horn� (horn� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f,-1.0f);<span class="kom">// Lev� horn� (horn� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 1.0f);<span class="kom">// Lev� doln� (horn� st�na)</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 1.0f);<span class="kom">// Prav� doln� (horn� st�na)</span></p>

<p>Spodn� ��st krychle se kresl� stejn�m zp�sobem, jen je posunuta na ose y do -1. Dal�� zm�na je, �e prav� horn� bod je tentokr�t bod bli��� k v�m, narozd�l od horn� st�ny, kde to byl bod vzd�len�j��. V tomto p��pad� by se nic nestalo pokud by jste pouze zkop�rovali p�edchoz� �ty�i ��dky a zm�nili hodnotu y na -1, ale pozd�ji by v�m to mohlo p�in�st probl�my nap��klad u textur.</p>

<p class="src2">glColor3f(1.0f,0.5f,0.0f);<span class="kom">// Oran�ov�</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Prav� horn� bod (spodn� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Lev� horn� (spodn� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f,-1.0f);<span class="kom">// Lev� doln� (spodn� st�na)</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f,-1.0f);<span class="kom">// Prav� doln� (spodn� st�na)</span></p>

<p>Te� vykresl�me �eln� st�nu.</p>

<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// �erven�</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 1.0f);<span class="kom">// Prav� horn� (�eln� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 1.0f);<span class="kom">// Lev� horn� (�eln� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Lev� doln� (�eln� st�na)</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Prav� doln� (�eln� st�na)</span></p>

<p>Zadn� st�na.</p>

<p class="src2">glColor3f(1.0f,1.0f,0.0f);<span class="kom">// �lut�</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f,-1.0f);<span class="kom">// Prav� horn� (zadn� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f,-1.0f);<span class="kom">// Lev� horn� (zadn� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f,-1.0f);<span class="kom">// Lev� doln� (zadn� st�na)</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f,-1.0f);<span class="kom">// Prav� doln� (zadn� st�na)</span></p>

<p>Lev� st�na.</p>

<p class="src2">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modr�</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 1.0f);<span class="kom">// Prav� horn� (lev� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f,-1.0f);<span class="kom">// Lev� horn� (lev� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f,-1.0f);<span class="kom">// Lev� doln� (lev� st�na)</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 1.0f);<span class="kom">// Prav� doln� (lev� st�na)</span></p>

<p>Prav� st�na. Je to posledn� st�na krychle. Pokud chcete tak ji vynechejte a z�sk�te krabici. Nebo m��ete zkusit nastavit pro ka�d� roh jinou barvu a vybarvit ji barevn�m p�echodem.</p>

<p class="src2">glColor3f(1.0f,0.0f,1.0f);<span class="kom">// Fialov�</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f,-1.0f);<span class="kom">// Prav� horn� (prav� st�na)</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 1.0f);<span class="kom">// Lev� horn� (prav� st�na)</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 1.0f);<span class="kom">// Lev� doln� (prav� st�na)</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f,-1.0f);<span class="kom">// Prav� doln� (prav� st�na)</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen� krychle</span></p>
<p class="src"></p>
<p class="src1">rtri+=0.2f;<span class="kom">// Inkrementace �hlu pooto�en� pyramidy</span></p>
<p class="src1">rquad-=0.15f;<span class="kom">// Inkrementace �hlu pooto�en� krychle</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Na konci tohoto tutori�lu byste m�li l�pe rozum�t jak jsou vytv��eny 3D objekty. M��ete p�em��let o OpenGL sc�n� jako o kusu pap�ru s mnoha pr�svitn�mi vrstvami. Jako gigantick� krychle tvo�en� body. Pokud si dok�ete p�edstavit v obrazovce hloubku, nem�li byste m�t probl�m s vytv��en�m vlastn�ch 3D objekt�.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson05.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson05.zip">ASM</a> k�d t�to lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson05_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson05.zip">C#</a> k�d t�to lekce. ( <a href="mailto:sugarbee@gmx.net">Sabine Felsinger</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson05.zip">VB.Net CsGL</a> k�d t�to lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson05.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson05.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson05.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson05.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson05.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson05.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson05.zip">GLUT</a> k�d t�to lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson05.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson05.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson05.zip">Java/SWT</a> k�d t�to lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson05.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson05.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson05.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson05.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson05.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson05.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson05.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson05.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson05.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson05.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson05.zip">Perl</a> k�d t�to lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson05.gz">Python</a> k�d t�to lekce. ( <a href="mailto:acolston@midsouth.rr.com">Tony Colston</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson05.rb.hqx">REALbasic</a> k�d t�to lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson05.zip">Scheme</a> k�d t�to lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson05.zip">Solaris</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson05.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson05.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson05.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(5);?>
<?FceNeHeOkolniLekce(5);?>

<?
include 'p_end.php';
?>
