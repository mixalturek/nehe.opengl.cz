<?
$g_title = 'CZ NeHe OpenGL - Lekce 10 - Vytvo�en� 3D sv�ta a pohyb v n�m';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(10);?>

<h1>Lekce 10 - Vytvo�en� 3D sv�ta a pohyb v n�m</h1>

<p class="nadpis_clanku">Do sou�asnosti jsme programovali ot��ej�c� se kostku nebo p�r hv�zd. M�te (m�li byste m�t :-) z�kladn� pojem o 3D. Ale rotuj�c� krychle asi nejsou to nejlep�� k tvorb� dobr�ch deathmatchov�ch protivn�k�! Ne�ekejte a za�n�te s Quakem IV je�t� dnes! Tyto dny pot�ebujete k velk�mu, komplikovan�mu a dynamick�mu 3D sv�tu s pohybem do v�ech sm�r�, skv�l�mi efekty zrcadel, port�l�, deformacemi a t�eba tak� vysok�m frameratem. Tato lekce v�m vysv�tl� z�kladn� strukturu 3D sv�ta a pohybu v n�m.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavi�kov� soubor pro matematickou knihovnu</span></p>
<p class="src"></p>
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
<p class="src0">bool blend;<span class="kom">// Blending ON/OFF</span></p>
<p class="src0">bool bp;<span class="kom">// B stisknuto? (blending)</span></p>
<p class="src0">bool fp;<span class="kom">// F stisknuto? (texturov� filtry)</span></p>
<p class="src"></p>
<p class="src0">const float piover180 = 0.0174532925f;<span class="kom">// Zjednodu�� p�evod mezi stupni a radi�ny</span></p>
<p class="src0">float heading;<span class="kom">// Pomocn� pro p�epo��t�v�n� xpos a zpos p�i pohybu</span></p>
<p class="src0">float xpos;<span class="kom">// Ur�uje x-ov� sou�adnice na podlaze</span></p>
<p class="src0">float zpos;<span class="kom">// Ur�uje z-ov� sou�adnice na podlaze</span></p>
<p class="src"></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y rotace (nato�en� sc�ny doleva/doprava - sm�r pohledu)</span></p>
<p class="src0">GLfloat walkbias = 0;<span class="kom">// Houp�n� sc�ny p�i pohybu (simulace krok�)</span></p>
<p class="src0">GLfloat walkbiasangle = 0;<span class="kom">// Pomocn� pro vypo��t�n� walkbias</span></p>
<p class="src0">GLfloat lookupdown = 0.0f;<span class="kom">// Ur�uje �hel nato�en� pohledu nahoru/dol�</span></p>
<p class="src0">GLfloat z=0.0f;<span class="kom">// Hloubka v obrazovce</span></p>
<p class="src"></p>
<p class="src0">GLuint filter;<span class="kom">// Pou�it� texturov� filtr</span></p>
<p class="src0">GLuint texture[3];<span class="kom">// Ukl�d� textury</span></p>

<p>B�hem definov�n� 3D sv�ta stylem dlouh�ch s�ri� ��sel se st�v� st�le obt�n�j��m udr�et slo�it� k�d p�ehledn�. Mus�me t��dit data do jednoduch�ho a p�edev��m funk�n�ho tvaru. Pro zp�ehledn�n� vytvo��me celkem t�i struktury.</p>

<p>Body obsahuj� skute�n� data, kter� zaj�maj� OGL. Ka�d� bod definujeme pozic� v prostoru (x,y,z) a koordin�ty textury (u,v).</p>

<p class="src0">typedef struct tagVERTEX<span class="kom">// Struktura bodu</span></p>
<p class="src0">{</p>
<p class="src1">float x, y, z;<span class="kom">// Sou�adnice v prostoru</span></p>
<p class="src1">float u, v;<span class="kom">// Texturov� koordin�ty</span></p>
<p class="src0">} VERTEX;</p>

<p>V�echno se skl�d� z ploch. Proto�e troj�heln�ky jsou nejjednodu���, vyu�ijeme pr�v� je.</p>

<p class="src0">typedef struct tagTRIANGLE<span class="kom">// Struktura troj�heln�ku</span></p>
<p class="src0">{</p>
<p class="src1">VERTEX vertex[3];<span class="kom">// Pole t�� bod�</span></p>
<p class="src0">} TRIANGLE;</p>

<p>Na po��tku v�eho je sektor. Ka�d� 3D sv�t je v z�klad� cel� ze sektor�. M��e j�m b�t m�stnost, kostka �i jak�koli jin� v�t�� �tvar.</p>

<p class="src0">typedef struct tagSECTOR<span class="kom">// Struktura sektoru</span></p>
<p class="src0">{</p>
<p class="src1">int numtriangles;<span class="kom">// Po�et troj�heln�k� v sektoru</span></p>
<p class="src1">TRIANGLE* triangle;<span class="kom">// Ukazatel na dynamick� pole troj�heln�k�</span></p>
<p class="src0">} SECTOR;</p>
<p class="src"></p>
<p class="src0">SECTOR sector1;<span class="kom">// Bude obsahovat v�echna data 3D sv�ta</span></p>

<p>Abychom program je�t� v�ce zp�ehlednili, ve zdrojov�m k�du, kter� se kompiluje, nebudou ��dn� ��seln� sou�adnice. K exe souboru - v�sledku na�� pr�ce - p�ilo��me textov� soubor. V n�m nadefinujeme v�echny body 3D prostoru a k nim odpov�daj�c� texturov� koordin�ty. Z d�vodu v�t�� p�ehlednosti p�id�me koment��e. Bez nich by byl tot�ln� zmatek. Obsah souboru se m��e kdykoli zm�nit. Hodit se to bude p�edev��m p�i vytv��en� prost�ed� - metoda pokus� a omyl�, kdy nemus�te poka�d� rekompilovat program. Upravovat m��e i u�ivatel a t�m si vytvo�it vlastn� prost�ed�. Nemus�te mu poskytovat nic nav�c, ne�kuli zdrojov� k�dy. Tento soubor by p�ece stejn� dostal. Ze za��tku bude lep�� pou��vat textov� soubory (snadn� editace, m�n� k�du), bin�rn� odlo��me na pozd�ji.</p>

<p>Prvn� ��dka NUMPOLLIES xx ur�uje celkov� po�et troj�heln�k�. Text za zp�tn�mi lom�tky zna�� koment��. V ka�d�m n�sleduj�c�m ��dku je definov�n jeden bod v prostoru a texturov� koordin�ty. T�i ��dky ur�� troj�heln�k, cel� soubor sektor.</p>

<p class="src0">NUMPOLLIES 36</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Floor 1</span></p>
<p class="src0">-3.0 0.0 -3.0 0.0 6.0</p>
<p class="src0">-3.0 0.0&nbsp;&nbsp;3.0 0.0 0.0</p>
<p class="src0">&nbsp;3.0 0.0&nbsp;&nbsp;3.0 6.0 0.0</p>
<p class="src0">-3.0 0.0 -3.0 0.0 6.0</p>
<p class="src0">&nbsp;3.0 0.0 -3.0 6.0 6.0</p>
<p class="src0">&nbsp;3.0 0.0&nbsp;&nbsp;3.0 6.0 0.0</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Ceiling 1</span></p>
<p class="src0">-3.0 1.0 -3.0 0.0 6.0</p>
<p class="src0">-3.0 1.0&nbsp;&nbsp;3.0 0.0 0.0</p>
<p class="src0">&nbsp;3.0 1.0&nbsp;&nbsp;3.0 6.0 0.0</p>
<p class="src0">-3.0 1.0 -3.0 0.0 6.0</p>
<p class="src0">&nbsp;3.0 1.0 -3.0 6.0 6.0</p>
<p class="src0">&nbsp;3.0 1.0&nbsp;&nbsp;3.0 6.0 0.0</p>

<p>... atd. Data jednoho troj�heln�ku tedy obecn� vypadaj� takto:</p>

<p class="src0">x1 y1 z1 u1 v1</p>
<p class="src0">x2 y2 z2 u2 v2</p>
<p class="src0">x3 y3 z3 u3 v3</p>

<p>Ot�zkou je, jak tyto data vyjmeme ze souboru. Vytvo��me funkci readstr(), kter� na�te jeden <b>pou�iteln�</b> ��dek.</p>

<p class="src0">void readstr(FILE *f,char *string)<span class="kom">// Na�te jeden pou�iteln� ��dek ze souboru</span></p>
<p class="src0">{</p>
<p class="src1">do</p>
<p class="src1">{</p>
<p class="src2">fgets(string, 255, f);<span class="kom">// Na�ti ��dek</span></p>
<p class="src1">} while ((string[0] == '/') || (string[0] == '\n'));<span class="kom">// Pokud nen� pou�iteln� na�ti dal��</span></p>
<p class="src"></p>
<p class="src1">return;</p>
<p class="src0">}</p>

<p>Tuto funkci budeme volat v SetupWorld(). Nadefinujeme n� soubor jako filein a otev�eme ho pouze pro �ten�. Na konci ho samoz�ejm� zav�eme.</p>

<p class="src0">void SetupWorld()<span class="kom">// Na�ti 3D sv�t ze souboru</span></p>
<p class="src0">{</p>
<p class="src1">float x, y, z, u, v;<span class="kom">// body v prostoru a koordin�ty textur</span></p>
<p class="src1">int numtriangles;<span class="kom">// Po�et troj�heln�k�</span></p>
<p class="src1">FILE *filein;<span class="kom">// Ukazatel na soubor</span></p>
<p class="src1">char oneline[255];<span class="kom">// Znakov� buffer</span></p>
<p class="src1">filein = fopen(&quot;data/world.txt&quot;, &quot;rt&quot;);<span class="kom">// Otev�en� souboru pro �ten�</span></p>

<p>P�e�teme data sektoru. Tato lekce bude po��tat pouze s jedn�m sektorem, ale nen� t�k� prov�st malou �pravu. Program pot�ebuje zn�t po�et troj�heln�k� v sektoru, aby v�d�l, kolik informac� m� p�e��st. Tato hodnota m��e b�t definov�na jako konstanta p��mo v programu, ale ur�it� ud�l�me l�pe, kdy� ji ulo��me p��mo do souboru (program se p�izp�sob�).</p>

<p class="src1">readstr(filein,oneline);<span class="kom">// Na�ten� prvn�ho pou�iteln�ho ��dku</span></p>
<p class="src1">sscanf(oneline, &quot;NUMPOLLIES %d\n&quot;, &amp;numtriangles);<span class="kom">// Vyjmeme po�et troj�heln�k�</span></p>

<p>Alokujeme pot�ebnou pam� pro v�echny troj�heln�ky a ulo��me jejich po�et do polo�ky struktury.</p>

<p class="src1">sector1.triangle = new TRIANGLE[numtriangles];<span class="kom">// Alokace pot�ebn� pam�ti</span></p>
<p class="src1">sector1.numtriangles = numtriangles;<span class="kom">// Ulo�en� po�tu troj�heln�k�</span></p>

<p>Po alokaci pam�ti m��eme p�istoupit k inicializaci v�ech datov�ch slo�ek sektoru.</p>

<p class="src1">for (int loop = 0; loop &lt; numtriangles; loop++)<span class="kom">// Proch�z� troj�heln�ky</span></p>
<p class="src1">{</p>
<p class="src2">for (int vert = 0; vert &lt; 3; vert++)<span class="kom">// Proch�z� vrcholy troj�heln�k�</span></p>
<p class="src2">{</p>

<p>Na�teme ��dek, do pomocn�ch prom�nn�ch ulo��me jednotliv� hodnoty a ty znovu ulo��me do polo�ek struktury. S mezikrokem je k�d mnohem p�ehledn�j��.</p>

<p class="src3">readstr(filein,oneline);<span class="kom">// Na�te ��dek</span></p>
<p class="src3">sscanf(oneline, &quot;%f %f %f %f %f&quot;, &amp;x, &amp;y, &amp;z, &amp;u, &amp;v);<span class="kom">// Na�ten� do pomocn�ch prom�nn�ch</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Inicializuje jednotliv� polo�ky struktury</span></p>
<p class="src3">sector1.triangle[loop].vertex[vert].x = x;</p>
<p class="src3">sector1.triangle[loop].vertex[vert].y = y;</p>
<p class="src3">sector1.triangle[loop].vertex[vert].z = z;</p>
<p class="src3">sector1.triangle[loop].vertex[vert].u = u;</p>
<p class="src3">sector1.triangle[loop].vertex[vert].v = v;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">fclose(filein);<span class="kom">// Zav�e soubor</span></p>
<p class="src1">return;</p>
<p class="src0">}</p>

<p>Pr�v� napsanou funkci zavol�me p�i inicializaci programu.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echna nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje texturu</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE);<span class="kom">// Nastaven� blendingu pro pr�hlednost</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkov� testov�n�</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povol�me jemn� st�nov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>
<p class="src"></p>
<p class="src1">SetupWorld();<span class="kom">// Loading 3D sv�ta</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Te� kdy� m�me sektor na�ten� do pam�ti, pot�ebujeme ho zobrazit. U� dlouho zn�me n�jak� ty rotace a pohyb, ale kamera v�dy sm��ovala do st�edu (0,0,0). Ka�d� dobr� 3D engine umo��uje chodit kolem a objevovat sv�t. Jedna mo�nost, jak k tomu dosp�t je to�it kamerou a kreslit 3D prost�ed� relativn� k pozici kamery - funkce gluLookAt(). Proto�e tohle je�t� nezn�me budeme kameru simulovat takto:</p>

<ul>
<li>U�ivatel stiskne �ipku</li>
<li>Vlevo/vpravo - oto��me sv�t okolo st�edu v opa�n�m sm�ru ne� je rotace kamery - glRoratef()</li>
<li>Dop�edu/dozadu - posuneme sv�t v opa�n�m sm�ru ne� je pohyb kamery - glTranslatef()</li>
</ul>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">GLfloat x_m, y_m, z_m, u_m, v_m;<span class="kom">// Pomocn� sou�adnice a koordin�ty textury</span></p>
<p class="src1">GLfloat xtrans = -xpos;<span class="kom">// Pro pohyb na ose x</span></p>
<p class="src1">GLfloat ztrans = -zpos;<span class="kom">// Pro pohyb na ose z</span></p>
<p class="src1">GLfloat ytrans = -walkbias-0.25f;<span class="kom">// Poskakov�n� kamery (simulace krok�)</span></p>
<p class="src1">GLfloat sceneroty = 360.0f - yrot;<span class="kom">// �hel sm�ru pohledu</span></p>
<p class="src"></p>
<p class="src1">int numtriangles;<span class="kom">// Po�et troj�heln�k�</span></p>
<p class="src"></p>
<p class="src1">glRotatef(lookupdown, 1.0f,0.0f,0.0f);<span class="kom">// Rotace na ose x - pohled nahoru/dol�</span></p>
<p class="src1">glRotatef(sceneroty, 0.0f,1.0f,0.0f);<span class="kom">// Rotace na ose y - oto�en� doleva/doprava</span></p>
<p class="src1">glTranslatef(xtrans, ytrans, ztrans);<span class="kom">// Posun na pozici ve sc�n�</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter]);<span class="kom">// V�b�r textury podle filtru</span></p>
<p class="src"></p>
<p class="src1">numtriangles = sector1.numtriangles;<span class="kom">// Po�et troj�heln�k� - pro p�ehlednost</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Projde a vykresl� v�echny troj�heln�ky</span></p>
<p class="src1">for (int loop_m = 0; loop_m &lt; numtriangles; loop_m++)</p>
<p class="src1">{</p>
<p class="src2">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek kreslen� troj�heln�k�</span></p>
<p class="src3">glNormal3f(0.0f, 0.0f, 1.0f);<span class="kom">// Norm�la ukazuje dop�edu - sv�tlo</span></p>
<p class="src"></p>
<p class="src3">x_m = sector1.triangle[loop_m].vertex[0].x;<span class="kom">// Prvn� vrchol</span></p>
<p class="src3">y_m = sector1.triangle[loop_m].vertex[0].y;</p>
<p class="src3">z_m = sector1.triangle[loop_m].vertex[0].z;</p>
<p class="src3">u_m = sector1.triangle[loop_m].vertex[0].u;</p>
<p class="src3">v_m = sector1.triangle[loop_m].vertex[0].v;</p>
<p class="src3">glTexCoord2f(u_m,v_m); glVertex3f(x_m,y_m,z_m);<span class="kom">// Vykreslen�</span></p>
<p class="src"></p>
<p class="src3">x_m = sector1.triangle[loop_m].vertex[1].x;<span class="kom">// Druh� vrchol</span></p>
<p class="src3">y_m = sector1.triangle[loop_m].vertex[1].y;</p>
<p class="src3">z_m = sector1.triangle[loop_m].vertex[1].z;</p>
<p class="src3">u_m = sector1.triangle[loop_m].vertex[1].u;</p>
<p class="src3">v_m = sector1.triangle[loop_m].vertex[1].v;</p>
<p class="src3">glTexCoord2f(u_m,v_m); glVertex3f(x_m,y_m,z_m);<span class="kom">// Vykreslen�</span></p>
<p class="src"></p>
<p class="src3">x_m = sector1.triangle[loop_m].vertex[2].x;<span class="kom">// T�et� vrchol</span></p>
<p class="src3">y_m = sector1.triangle[loop_m].vertex[2].y;</p>
<p class="src3">z_m = sector1.triangle[loop_m].vertex[2].z;</p>
<p class="src3">u_m = sector1.triangle[loop_m].vertex[2].u;</p>
<p class="src3">v_m = sector1.triangle[loop_m].vertex[2].v;</p>
<p class="src3">glTexCoord2f(u_m,v_m); glVertex3f(x_m,y_m,z_m);<span class="kom">// Vykreslen�</span></p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen� troj�heln�k�</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>P�ejdeme do funkce WinMain() na ovl�d�n� kl�vesnic�. Kdy� je stisknuta �ipka vlevo/vpravo, prom�nn� yrot je zv��ena/sn�ena, tud� se nato�� v�hled. Kdy� je stisknuta �ipka dop�edu/dozadu, spo��t� se nov� pozice pro kameru s pou�it�m sinu a kosinu - vy�aduje trochu znalost� trigonometrie. Piover180 je pouze ��slo pro konverzi mezi stupni a radi�ny. Walkbias je offset vytv��ej�c� houp�n� sc�ny p�i simulaci krok�. Jednodu�e uprav� y pozici kamery podle sinov� vlny. Jako jednoduch� pohyb vp�ed a vzad nevypad� �patn�.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if (keys['B'] &amp;&amp; !bp)<span class="kom">// Kl�vesa B - zapne/vypne blending</span></p>
<p class="src4">{</p>
<p class="src5">bp=TRUE;</p>
<p class="src5">blend=!blend;</p>
<p class="src5">if (!blend)</p>
<p class="src5">{</p>
<p class="src6">glDisable(GL_BLEND);</p>
<p class="src6">glEnable(GL_DEPTH_TEST);</p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">glEnable(GL_BLEND);</p>
<p class="src6">glDisable(GL_DEPTH_TEST);</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src4">if (!keys['B'])</p>
<p class="src4">{</p>
<p class="src5">bp=FALSE;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys['F'] &amp;&amp; !fp)<span class="kom">// Kl�vesa F - cyklov�n� mezi texturov�mi filtry</span></p>
<p class="src4">{</p>
<p class="src5">fp=TRUE;</p>
<p class="src5">filter+=1;</p>
<p class="src5">if (filter&gt;2)</p>
<p class="src5">{</p>
<p class="src6">filter=0;</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src4">if (!keys['F'])</p>
<p class="src4">{</p>
<p class="src5">fp=FALSE;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_UP])<span class="kom">// �ipka nahoru - pohyb dop�edu</span></p>
<p class="src4">{</p>
<p class="src"></p>
<p class="src5">xpos -= (float)sin(heading*piover180) * 0.05f;<span class="kom">// Pohyb na ose x</span></p>
<p class="src5">zpos -= (float)cos(heading*piover180) * 0.05f;<span class="kom">// Pohyb na ose z</span></p>
<p class="src5">if (walkbiasangle &gt;= 359.0f)</p>
<p class="src5">{</p>
<p class="src6">walkbiasangle = 0.0f;</p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">walkbiasangle+= 10;</p>
<p class="src5">}</p>
<p class="src5">walkbias = (float)sin(walkbiasangle * piover180)/20.0f;<span class="kom">// Simulace krok�</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN])<span class="kom">// �ipka dol� - pohyb dozadu</span></p>
<p class="src4">{</p>
<p class="src5">xpos += (float)sin(heading*piover180) * 0.05f;<span class="kom">// Pohyb na ose x</span></p>
<p class="src5">zpos += (float)cos(heading*piover180) * 0.05f;<span class="kom">// Pohyb na ose z</span></p>
<p class="src5">if (walkbiasangle &lt;= 1.0f)</p>
<p class="src5">{</p>
<p class="src6">walkbiasangle = 359.0f;</p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">walkbiasangle-= 10;</p>
<p class="src5">}</p>
<p class="src5">walkbias = (float)sin(walkbiasangle * piover180)/20.0f;<span class="kom">// Simulace krok�</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_RIGHT])<span class="kom">// �ipka doprava</span></p>
<p class="src4">{</p>
<p class="src5">heading -= 1.0f;<span class="kom">// Nato�en� sc�ny</span></p>
<p class="src5">yrot = heading;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_LEFT])<span class="kom">// �ipka doleva</span></p>
<p class="src4">{</p>
<p class="src5">heading += 1.0f;<span class="kom">// Nato�en� sc�ny</span></p>
<p class="src5">yrot = heading;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_PRIOR])<span class="kom">// Page Up</span></p>
<p class="src4">{</p>
<p class="src5">lookupdown-= 1.0f;<span class="kom">// Nato�en� sc�ny</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_NEXT])<span class="kom">// Page Down</span></p>
<p class="src4">{</p>
<p class="src5">lookupdown+= 1.0f;<span class="kom">// Nato�en� sc�ny</span></p>
<p class="src4">}</p>

<p>Vytvo�ili jsme prvn� 3D sv�t. Nevypad� sice jako v Quake-ovi, ale my tak� nejsme Carmack nebo Abrash. Zkuste tla��tka F - texturov� filtr a B - blending. PgUp/PgDown nach�l� kameru nahoru/dol�. Pohyb �ipkami v�s douf�m napadne.</p>

<p>Te� asi p�em��l�te co d�l. Mo�n� pou�ijete tento k�d na plnohodnotn� 3D engine, m�li byste b�t schopni ho vytvo�it. Pravd�podobn� budete m�t ve h�e v�ce ne� jeden sektor, zvl�t� p�i pou�it� vchod�.</p>

<p>Tato implementace k�du umo��uje nahr�v�n� mnohon�sobn�ch sektor� a m� zp�tn� vykreslov�n� /backface culling/ (nekresl� polygony od kamery). Hodn� �t�st� v dal��ch pokusech.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Ji�� Rajsk� - RAJSOFT junior <?VypisEmail('predator.jr@seznam.cz');?><br />
kompletn� p�epsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<p>Pozn.: Tuto lekci nepsal NeHe, ale Lionel Brits. Jak s�m autor uv�d�, je to jeho prvn� tutori�l - a bohu�el bylo to vid�t. Pokud se pod�v�te do anglick� verze, tak zjist�te, �e bez zdrojov�ch k�d� nem�te absolutn� �anci n�co pochopit. N�kdy je dokonce velmi t�k� identifikovat, kter� ��st k�du pat�� ke kter� funkci. Aby byl text krat�� pou��val vynech�vky (n�kdy i u hodn� d�le�it�ho k�du - t�eba na��t�n� pozic ze souboru), ap. P�eklad Ji��ho Rajsk�ho byl, d� se ��ct, p�esn� a to v tomto p��pad�, byla mo�n� chyba. Proto jsem se rozhodl v�t�� ��st lekce p�epsat. V�m, �e ani te� to nen� nijak zvlṻ slavn�, ale sna�il jsem se. K�d jsem samoz�ejm� neupravoval (i kdy� by si to tak� zaslou�il).</p>

<p><b>Chyby v k�du:</b> Kdy� jsem p�episoval tuto lekci, musel jsem ji pochopit ze zdrojov�ch k�d� a p�i tom jsem na�el n�kolik chyb. Je mi to tak trochu blb�, proto�e bych k�d asi s�m nedok�zal napsat, ale na druhou stranu byste o tom m�li v�d�t.</p>

<p>Zbyte�n� deklarace prom�nn� z. Tuto prom�nnou autor pravd�podobn� pou��val ze za��tku a pak ji nahradil jinou. Sv�d�� o tom i dvojit� testov�n� PageUp/PageDown (do lekce nevypisov�no). Nikde jinde ji nenajdete.</p>

<p>Neuvoln�n� dynamicky alokovan� pam�ti. Ve funkci SetupWorld() jsme pomoc� oper�toru new alokovali pam� pro troj�heln�ky. Nikdy v programu, ale nen� jej� uvoln�n�. I kdy� by m�l opera�n� syst�m po skon�en� programu ru�it v�echny syst�mov� zdroje, nelze se na to spol�hat. Tuto chybu odstran�te nap��klad takto:</p>

<p class="src0"><span class="kom">// P�idat na konec funkce KillGLWindow()</span></p>
<p class="src1">delete [] sector1.triangle;<span class="kom">// Uvoln�n� dynamicky alokovan� pam�ti</span></p>
<p class="src"></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson10.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson10_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson10.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson10.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson10.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson10.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson10.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson10.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson10.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson10.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson10.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson10.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson10.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:ncb000gt65@hotmail.com">Nicholas Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson10.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson10.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson10.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson10.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson10.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson10.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson10.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson10.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson10.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson10-2.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:jcapellman@hotmail.com">Jarred Capellman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson10.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson10.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson10.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(10);?>
<?FceNeHeOkolniLekce(10);?>

<?
include 'p_end.php';
?>
