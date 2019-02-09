<?
$g_title = 'CZ NeHe OpenGL - Lekce 9 - Pohyb bitmap ve 3D prostoru';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(9);?>

<h1>Lekce 9 - Pohyb bitmap ve 3D prostoru</h1>

<p class="nadpis_clanku">Tento tutori�l v�s nau�� pohyb objekt� ve 3D prostoru a kreslen� bitmap bez �ern�ch m�st, zakr�vaj�c�ch objekty za nimi. Jednoduchou animaci a roz���en� pou�it� blendingu. Te� byste u� m�li rozum�t OpenGL velmi dob�e. Nau�ili jste se v�e od nastaven� OpenGL okna, po mapov�n� textur za pou�it� sv�tel a blendingu. To byl prvn� tutori�l pro st�edn� pokro�il�. A pokra�ujeme d�le...</p>

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

<p>Twinkle ur�uje, zda se pou��v� t�pytiv� efekt a tp indikuje stisk kl�vesy T.</p>

<p class="src0">bool twinkle;<span class="kom">// T�pytiv� efekt</span></p>
<p class="src0">bool tp;<span class="kom">// Stisknuto T?</span></p>

<p>Num ur�uje kolik hv�zd bude zobrazeno na obrazovce. Je definov�no jako konstanta, tak�e ho m��ete m�nit libovoln�, ale jen v tomto ��dku. Nezkou�ejte m�nit hodnotu num pozd�ji v k�du, pokud nechcete p�ivodit katastrofu.</p>

<p class="src0">const num=50;<span class="kom">// Po�et zobrazovan�ch hv�zd</span></p>

<p>Deklarujeme strukturu, v n� budeme uchov�vat informace o jednotliv�ch hv�zd�ch.</p>

<p class="src0">typedef struct<span class="kom">// Struktura hv�zdy</span></p>
<p class="src0">{</p>
<p class="src1">int r, g, b;<span class="kom">// Barva</span></p>
<p class="src1">GLfloat dist,<span class="kom">// Vzd�lenost od st�edu</span></p>
<p class="src1">angle;<span class="kom">// �hel nato�en�</span></p>
<p class="src0">} stars;<span class="kom">// Jm�no struktury je stars</span></p>

<p>Ka�d� polo�ka v poli star obsahuje objekt struktury stars, tj. p�t hodnot popisuj�c�ch hv�zdu.</p>

<p class="src0">stars star[num];<span class="kom">// Pole hv�zd o velikosti num</span></p>

<p>D�le vytvo��me prom�nn� pro nastaven� vzd�lenosti pozorovatele (zoom) a �hlu pozorov�n� (tilt). Deklarujeme prom�nnou spin nat��ej�c� hv�zdy okolo osy z, co� bude vypadat jako by se ot��ely okolo sv� sou�asn� pozice. Loop je ��d�c� prom�nn� cyklu, kter� pou�ijeme pro nakreslen� v�ech pades�ti hv�zd. Texture[1] ukl�d� jednu �ernob�lou texturu.</p>

<p class="src0">GLfloat zoom=-15.0f;<span class="kom">// Hloubka v obrazovce</span></p>
<p class="src0">GLfloat tilt=90.0f;<span class="kom">// �hel pohledu</span></p>
<p class="src0">GLfloat spin;<span class="kom">// Nato�en� hv�zd</span></p>
<p class="src0"></p>
<p class="src0">GLuint loop;<span class="kom">// ��d�c� prom�nn� cyklu</span></p>
<p class="src0">GLuint texture[1];<span class="kom">// Ukl�d� texturu</span></p>

<p>Hned po p�edch�zej�c�m k�du p�id�me k�d pro nahr�n� textury. Nebudu jej znovu opisovat. Je to ten sam� jako v lekci 6, 7 a 8. Bitmapa, kterou tentokr�t nahrajeme je nazv�na star.bmp. Textura bude pou��vat line�rn� filtrov�n�.</p>

<p class="src1">if(TextureImage[0]=LoadBMP("Data/Tim.bmp"))<span class="kom">// Loading bitmapy</span></p>

<p>V tomto projektu nebudeme pou��vat hloubkov� testov�n�, tak�e pokud pou��v�te k�d z lekce 1, ujist�te se, �e jste odstranili vol�n� glDepthFunc(GL_LEQUAL); a glEnable(GL_DEPTH_TEST); jinak z�sk�te velmi �patn� v�sledky. Nicm�n� v tomto k�du pou��v�me mapov�n� textur, tak�e se ujist�te, �e jste p�idali ��dky, kter� nejsou v lekci 1. V�imn�te si �e povolujeme mapov�n� textur a blending.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echna nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje textury</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturov� mapov�n�</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povol� jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE);<span class="kom">// Typ blendingu pro pr�hlednost</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>

<p>N�sleduj�c� k�d je nov�. Nastav� po��te�n� �hel, vzd�lenost a barvu ka�d� hv�zdy. V�imn�te si jak je jednoduch� zm�nit hodnoty ve struktu�e. Smy�ka projde v�ech 50 hv�zd.</p>

<p class="src1">for (loop=0; loop&lt;num; loop++)<span class="kom">// Inicializuje hv�zdy</span></p>
<p class="src1">{</p>
<p class="src2">star[loop].angle=0.0f;<span class="kom">// V�echny maj� na za��tku nulov� �hel</span></p>

<p>Po��t�m vzd�lenost pomoc� aktu�ln� hv�zdy (hodnoty prom�nn� loop), kterou d�l�m maxim�ln�m po�tem hv�zd. Pot� n�sob�m v�sledek p�ti. V podstat� to posune ka�dou hv�zdu o trochu d�le ne� tu p�edch�zej�c�. Kdy� je loop 50 (posledn� hv�zda), loop d�leno num je 1.0f. P���ina pro� n�sob�m p�ti je, �e 1*5= 5 a to je okraj obrazovky. Nechci aby hv�zdy nebyly zobrazen� tak�e 5.0f je perfektn�. Pokud nastav�te hodnotu prom�nn� zoom hloub�ji do obrazovky, m��ete pou��t hodnotu v�t�� ne� 5.0f, ale hv�zdy budou men�� (z d�vodu perspektivy). V�imn�te si, �e barva ka�d� hv�zdy je tvo�ena pomoc� n�hodn�ch hodnot od 0 do 255. M��ete se divit jak m��eme pou��t tak velk� hodnoty, kdy� norm�ln� jsou hodnoty barev od 0.0f do 1.0f. Kdy� nastavujeme barvu, pou�ijeme funkci glColor4ub nam�sto glColor4f. ub znamen� unsigned byte, kter� m��e nab�vat hodnot od 0 do 255. V tomto programu je jednodu��� pou��t byty ne� generovat desetinn� hodnoty.</p>

<p class="src2">star[loop].dist=(float(loop)/num)*5.0f;<span class="kom">// Vzd�lenost od st�edu</span></p>
<p class="src2">star[loop].r=rand()%256;<span class="kom">// Barva</span></p>
<p class="src2">star[loop].g=rand()%256;<span class="kom">// Barva</span></p>
<p class="src2">star[loop].b=rand()%256;<span class="kom">// Barva</span></p>
<p class="src1">}</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Na �adu p�ich�z� vykreslov�n�.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// V�b�r textury</span></p>
<p class="src"></p>
<p class="src1">for (loop=0; loop&lt;num; loop++)<span class="kom">// Proch�z� jednotliv� hv�zdy</span></p>
<p class="src1">{</p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glTranslatef(0.0f,0.0f,zoom);<span class="kom">// P�esun do obrazovky o zoom</span></p>
<p class="src2">glRotatef(tilt,1.0f,0.0f,0.0f);<span class="kom">// Naklopen� pohledu</span></p>

<p>Te� pohneme hv�zdou. Prvn� v�c kterou ud�l�me je pooto�en� okolo osy y. Dal�� ��dek k�du posune hv�zdu na ose x. Norm�ln� to znamen� posun na pravou stranu obrazovky, ale proto�e jsme pooto�ili v�hled okolo osy y, kladn� hodnota osy x m��e b�t kdekoli.</p>

<p class="src2">glRotatef(star[loop].angle,0.0f,1.0f,0.0f);<span class="kom">// Rotace o �hel konkr�tn� hv�zdy</span></p>
<p class="src2">glTranslatef(star[loop].dist,0.0f,0.0f);<span class="kom">// P�esun vp�ed na ose x</span></p>

<p>Hv�zda je ve skute�nosti ploch� textura. Pokud nakresl�te ploch� �ty��heln�k a namapujete na n�j texturu, bude to vypadat dob�e. Bude �elem k v�m, jak m�. Ale kdy� sc�nu pooto��te o 90 stup�� okolo osy y, textura bude �elem k lev� nebo prav� stran� obrazovky a vy uvid�te pouze tenkou linku, co� nechceme. Chceme aby hv�zdy byly po��d �elem k n�m nez�visle na nato�en� a naklopen�. Ud�l�me to zru�en�m v�ech rotac� v opa�n�m po�ad� t�sn� p�edt�m ne� vykresl�me hv�zdu. Pooto��me zp�t zad�n�m invertovan�ho �hlu pro rotaci a pot� zru��me naklopen� op�t pomoc� z�porn�ho �hlu. Proto�e jsme d��ve posunuli po��tek, tak je na pozici ve kter� jsme ji cht�li. Zm�nili jsme jej� polohu, ale texturu st�le vid�me spr�vn� zep�edu. </p>

<p class="src2">glRotatef(-star[loop].angle,0.0f,1.0f,0.0f);<span class="kom">// Zru�en� pooto�en�</span></p>
<p class="src2">glRotatef(-tilt,1.0f,0.0f,0.0f);<span class="kom">// Zru�en� naklopen�</span></p>

<p>Jestli�e je twinkle TRUE nakresl�me na obrazovku nerotuj�c� hv�zdu. Pro z�sk�n� rozd�ln�ch barev vezmeme maxim�ln� po�et hv�zd (num) a ode�teme ��slo aktu�ln� hv�zdy (loop), pot� ode�teme 1, proto�e loop nab�v� hodnot od 0 do num-1. T�mto zp�sobem z�sk�me hv�zdy rozd�ln�ch barev. Nen� to pr�v� nejlep�� zp�sob, ale je efektivn�. Posledn� hodnota je alfa hodnota. ��m je ni���, t�m je hv�zda pr�hledn�j��. Pokud projde k�d podm�nkou, bude ka�d� hv�zda nakreslena dvakr�t. To zpomal� program. O kolik z�vis� na va�em po��ta�i, ale v�sledek bude st�t za to - sm�s� se barvy dvou hv�zd. Proto�e se nenat���, budou vypadat, jako by byly animovan�. V�imn�te si jak je jednoduch� p�idat barvu do textury. T�eba�e je textura �ernob�l�, dostaneme takovou barvu, jakou zvol�me p�ed vykreslen�m.</p>

<p class="src2">if (twinkle)<span class="kom">// Pokud je zapnut� t�pytiv� efekt</span></p>
<p class="src2">{</p>
<p class="src3">glColor4ub(star[(num-loop)-1].r,star[(num-loop)-1].g,star[(num-loop)-1].b,255);</p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f, 0.0f);</p>
<p class="src3">glEnd();</p>
<p class="src2">}</p>

<p>Te� vykresl�me hlavn� hv�zdu. Jedin� rozd�l od p�edch�zej�c�ho k�du je, �e tato hv�zda je nato�ena okolo osy z a m� jinou barvu (viz. indexy).</p>

<p class="src2">glRotatef(spin,0.0f,0.0f,1.0f);</p>
<p class="src2">glColor4ub(star[loop].r,star[loop].g,star[loop].b,255);</p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f, 0.0f);</p>
<p class="src2">glEnd();</p>

<p>Pooto��me hv�zdu zv�t�en�m hodnoty prom�nn� spin. Pot� zm�n�me �hel ka�d� hv�zdy o loop/num. To znamen�, �e vzd�len�j�� hv�zdy se ot��� rychleji. Nakonec sn��me vzd�lenost hv�zdy od st�edu, tak�e to vypad�, �e jsou nas�v�ny doprost�ed.</p>

<p class="src2">spin+=0.01f;<span class="kom">// Pooto�en� hv�zd</span></p>
<p class="src2">star[loop].angle+=float(loop)/num;<span class="kom">// Zv��en� �hlu hv�zdy</span></p>
<p class="src2">star[loop].dist-=0.01f;<span class="kom">// Zm�na vzd�lenosti hv�zdy od st�edu</span></p>

<p>Zkontrolujeme zda hv�zda dos�hla st�edu. Pokud se tak stane, dostane novou barvu a je posunuta o 5 jednotek od st�edu, tak�e m��e op�t za��t svou cestu jako nov� hv�zda.</p>

<p class="src2">if (star[loop].dist&lt;0.0f)<span class="kom">// Dos�hla st�edu</span></p>
<p class="src2">{</p>
<p class="src3">star[loop].dist+=5.0f;<span class="kom">// Nov� pozice</span></p>
<p class="src3">star[loop].r=rand()%256;<span class="kom">// Nov� barva</span></p>
<p class="src3">star[loop].g=rand()%256;<span class="kom">// Nov� barva</span></p>
<p class="src3">star[loop].b=rand()%256;<span class="kom">// Nov� barva</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>P�id�me k�d zji��uj�c� stisk kl�vesy T. P�ejd�te k funkci WinMain(). Najd�te ��dek SwapBuffers(hDC). P�eme za n�j.</p>
<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">SwapBuffers(hDC);<span class="kom">// Prohozen� buffer�</span></p>
<p class="src"></p>
<p class="src4">if (keys['T'] &amp;&amp; !tp)<span class="kom">// T - t�pytiv� efekt</span></p>
<p class="src4">{</p>
<p class="src5">tp=TRUE;</p>
<p class="src5">twinkle=!twinkle;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys['T'])<span class="kom">// Uvoln�n� T</span></p>
<p class="src4">{</p>
<p class="src5">tp=FALSE;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_UP])<span class="kom">// �ipka nahoru - naklon� obraz</span></p>
<p class="src4">{</p>
<p class="src5">tilt-=0.5f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN])<span class="kom">// �ipka dolu - naklon� obraz</span></p>
<p class="src4">{</p>
<p class="src5">tilt+=0.5f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_PRIOR])<span class="kom">// PageUp - zv�t�� hloubku</span></p>
<p class="src4">{</p>
<p class="src5">zoom-=0.2f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_NEXT])<span class="kom">// PageDown - zmen�� hloubku</span></p>
<p class="src4">{</p>
<p class="src5">zoom+=0.2f;</p>
<p class="src4">}</p>

<p>A m�me hotovo. Nau�ili jste se jednoduchou, ale celkem efektn� animaci.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson09.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson09_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson09.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson09.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson09.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson09.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson09.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson09.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson09.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson09.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson09.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson09.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson09.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson09.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson09.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson09.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson09.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson09.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson09.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson09.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson09.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson09.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson09.zip">Solaris</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson09.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson09.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson09.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(9);?>
<?FceNeHeOkolniLekce(9);?>

<?
include 'p_end.php';
?>
