<?
$g_title = 'CZ NeHe OpenGL - Lekce 7 - Texturov� filtry, osv�tlen�, ovl�d�n� pomoc� kl�vesnice';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(7);?>

<h1>Lekce 7 - Texturov� filtry, osv�tlen�, ovl�d�n� pomoc� kl�vesnice</h1>

<p class="nadpis_clanku">V tomto d�lu se pokus�m vysv�tlit pou�it� t�� odli�n�ch texturov�ch filtr�. D�le pak pohybu objekt� pomoc� kl�vesnice a nakonec aplikaci jednoduch�ch sv�tel v OpenGL. Nebude se jako obvykle navazovat na k�d z p�edchoz�ho d�lu, ale za�ne se p�kn� od za��tku.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standartdn� vstup/v�stup</span></p>
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

<p>P�id�me t�i booleovsk� prom�nn�. Prom�nn� light sleduje zda je sv�tlo zapnut�. Prom�nn� lp a fp n�m indikuj� stisk kl�vesy 'L' nebo 'F'. Pro� je pot�ebujeme se dozv�me d�le. Te� sta�� v�d�t, �e zabra�uj� opakov�n� obslu�n�ho k�du p�i del��m dr�en�.</p>

<p class="src0">bool light;<span class="kom">// Sv�tlo ON/OFF</span></p>
<p class="src0">bool lp;<span class="kom">// Stisknuto L?</span></p>
<p class="src0">bool fp;<span class="kom">// Stisknuto F?</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot;<span class="kom">// X Rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y Rotace</span></p>
<p class="src0">GLfloat xspeed;<span class="kom">// Rychlost x rotace</span></p>
<p class="src0">GLfloat yspeed;<span class="kom">// Rychlost y rotace</span></p>
<p class="src0">GLfloat z=-5.0f;<span class="kom">// Hloubka v obrazovce</span></p>

<p>N�sleduj� pole pro specifikaci sv�tla. Pou�ijeme dva odli�n� typy. Prvn� bude okoln� (ambient). Okoln� sv�tlo nevych�z� z jednoho bodu, ale jsou j�m nasv�ceny v�echny objekty ve sc�n�. Druh�m typem bude p��m� (diffuse). P��m� sv�tlo vych�z� z n�jak�ho zdroje a odr�� se o povrch. Povrchy objektu, na kter� sv�tlo dopad� p��mo, budou velmi jasn� a oblasti m�lo osv�tlen� budou temn�. To vytv��� p�kn� st�nov� efekty po stran�ch krabice. Sv�tlo se vytv��� stejn�m zp�sobem jako barvy. Je-li prvn� ��slo 1.0f a dal�� dv� 0.0f, dost�v�me jasnou �ervenou. Posledn� hodnotou je alfa kan�l. Ten tentokr�t nech�me 1.0f. �erven�, zelen� a modr� nastaven� na stejnou hodnotu v�dy vytvo�� st�n z �ern� (0.0f) do b�l� (1.0f). Bez okoln�ho sv�tla by m�sta bez p��m�ho sv�tla byla p��li� tmav�.</p>

<p class="src0">GLfloat LightAmbient[]= { 0.5f, 0.5f, 0.5f, 1.0f };<span class="kom">// Okoln� sv�tlo</span></p>

<p>V dal��m ��dku jsou hodnoty pro p��m� sv�tlo. Proto�e, jsou v�echny hodnoty 1.0f, bude to nejjasn�j�� sv�tlo jak� m��eme z�skat. P�kn� osv�t� krabici.</p>

<p class="src0">GLfloat LightDiffuse[]= { 1.0f, 1.0f, 1.0f, 1.0f };<span class="kom">// P��m� sv�tlo</span></p>

<p>Nakonec nastav�me pozici sv�tla. Proto�e chceme aby sv�tlo sv�tilo na bednu zp�edu, nesm�me pohnout sv�tlem na ose x a y. T�et� parametr n�m zaru��, �e bedna bude osv�tlena zep�edu. Sv�tlo bude z��it sm�rem k div�kovi. Zdroj sv�tla neuvid�me, proto�e je p�ed monitorem, ale uvid�me jeho odraz od bedny. Posledn� ��slo definujeme na 1.0f. Ur�uje koordin�ty pozice sv�teln�ho zdroje. V�ce v dal�� lekci.</p>

<p class="src0">GLfloat LightPosition[]= { 0.0f, 0.0f, 2.0f, 1.0f };<span class="kom">// Pozice sv�tla</span></p>

<p>Prom�nn� filter bude pou�ita p�i zobrazen� textury. Prvn� textura je vytv��ena pou�it�m GL_NEAREST. Druh� textura bude GL_LINEAR - filtrov�n� pro �pln� hladk� obr�zek. T�et� textura pou��v� mipmapingu, kter� tvo�� hodn� dobr� povrch. Prom�nn� filter tedy bude nab�vat hodnot 0, 1 a 2. GLuint texture[3] ukazuje na t�i textury.</p>

<p class="src0">GLuint filter;<span class="kom">// Specifikuje pou��van� texturov� filtr</span></p>
<p class="src0">GLuint texture[3];<span class="kom">// Ukl�d� t�i textury</span></p>

<p>Nahrajeme bitmapu a vytvo��me z n� t�i r�zn� textury. Tato lekce pou��v� glaux knihovny k nahr�v�n� bitmap. V�m �e Delphi a VC++ maj� tuto knihovnu. Co ostatn� jazyky, nev�m. K tomu u� moc ��kat nebudu, ��dky jsou okomentovan� a kompletn� vysv�tlen� je v 6 lekci. Nahraje a vytvo�� textury z bitmap.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Loading bitmapy a konverze na texturu</span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>
<p class="src1">AUX_RGBImageRec *TextureImage[1];<span class="kom">// Ukl�d� bitmapu</span></p>
<p class="src1">memset(TextureImage,0,sizeof(void *)*1);<span class="kom">// Vynuluje pam�</span></p>

<p>Nyn� nahrajeme bitmapu. Kdy� v�e prob�hne, data obr�zku budou ulo�ena v TextureImage[0], status se nastav� na true a za�neme sestavovat texturu.</p>

<p class="src1">if (TextureImage[0]=LoadBMP(&quot;Data/Crate.bmp&quot;))<span class="kom">// Nahraje bitmapu a kontroluje vznikl� chyby</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;<span class="kom">// V�e je bez probl�m�</span></p>

<p>Data bitmapy jsou nahr�na do TextureImage[0]. Pou�ijeme je k vytvo�en� t�� textur. N�sleduj�c� ��dek ozn�m�, �e chceme sestavit 3 textury a chceme je m�t v ulo�eny v texture[0], texture[1] a texture[2].</p>

<p class="src2">glGenTextures(3, &amp;texture[0]);<span class="kom">// Generuje t�i textury</span></p>

<p>V �est� lekci jsme pou�ili line�rn� filtrov�n�, kter� vy�aduje hodn� v�konu, ale vypad� velice p�kn�. Pro prvn� texturu pou�ijeme GL_NEAREST. Spot�ebuje m�lo v�konu, ale v�sledek je relativn� �patn�. Kdy� ve h�e vid�te �tvere�kovanou texturu, pou��v� toto filtrov�n�, nicm�n� dob�e funguje i na slab��ch po��ta��ch. V�imn�te si �e jsme pou�ili GL_NEAREST pro MIN i MAG. M��eme sm�chat GL_NEAREST s GL_LINEAR a textury budou vypadat slu�n�, ale z�rove� nevy�aduj� vysok� v�kon. MIN_FILTER se u��v� p�i zmen�ov�n�, MAG_FILTER p�i zv�t�ov�n�.</p>

<p class="src2"><span class="kom">// Vytvo�� neline�rn� filtrovanou texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[0]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_NEAREST);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_NEAREST);</p>
<p class="src"></p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);</p>

<p>Dal�� texturu vytvo��me stejn� jako v lekci 6. Line�rn� filtrovan�. Jedin� rozd�l spo��v� v pou�it� texture[1] m�sto texture[0], proto�e se jedn� o druhou texturu.</p>

<p class="src2"><span class="kom">// Vytvo�� line�rn� filtrovanou texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[1]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);</p>

<p>Mipmaping je�t� nezn�te. Pou��v� se p�i mal�m obr�zku, kdy mnoho detail� miz� z obrazovky. Takto vytvo�en� povrch vypad� z bl�zka dost �patn�. Kdy� chcete sestavit mipmapovanou texturu, sestav� se v�ce textur odli�n� velikosti a vysok� kvality. Kdy� kresl�te takovou texturu na obrazovku vybere se nejl�pe vypadaj�c� textura. Nakresl� na obrazovku m�sto toho, aby zm�nilo rozli�en� p�vodn�ho obr�zku, kter� je p���inou ztr�ty detail�. V �est� lekci jsem se zm�nil o stanoven�ch limitech ���ky a v��ky - 64, 128, 256 atd. Pro mipmapovanou texturu m��eme pou��t jakoukoli ���ku a v��ku bitmapy. Automaticky se zm�n� velikost. Proto�e toto je textura ��slo 3, pou�ijeme texture[2]. Nyn� m�me v texture[0] texturu bez filtru, texture[1] pou��v� line�rn� filtrov�n� a texture[2] pou��v� mipmaping.</p>

<p class="src2"><span class="kom">// Vytvo�� mipmapovanou texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[2]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR_MIPMAP_NEAREST);</p>
<p class="src"></p>
<p class="src2">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);</p>
<p class="src1">}</p>

<p>M��eme uvolnit v�echnu pam� zapln�nou daty bitmapy. Otestujeme zda se data nach�z� v TextureImage[0]. Kdy� tam budou, tak je sma�eme. Nakonec uvoln�me strukturu obr�zku.</p>

<p class="src1">if (TextureImage[0])<span class="kom">// Pokud obr�zek existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (TextureImage[0]-&gt;data)<span class="kom">// Pokud existuj� data obr�zku</span></p>
<p class="src2">{</p>
<p class="src3">free(TextureImage[0]-&gt;data);<span class="kom">// Uvoln� pam� obr�zku</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">free(TextureImage[0]);<span class="kom">// Uvoln� strukturu obr�zku</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return Status;<span class="kom">// Ozn�m� p��padn� chyby</span></p>
<p class="src0">}</p>

<p>Nejd�le�it�j�� ��st inicializace spo��v� v pou�it� sv�tel.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echno nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje texturu</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkov� testov�n�</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>

<p>Nastav�me sv�tla - konkr�tn� light1. Na za��tku t�to lekce jsme definovali okoln� sv�tlo do LightAmbient. Pou�ijeme hodnoty nastaven� v poli.</p>

<p class="src1">glLightfv(GL_LIGHT1, GL_AMBIENT, LightAmbient);<span class="kom">// Nastaven� okoln�ho sv�tla</span></p>

<p>Hodnoty p��m�ho sv�tla jsou v LightDiffuse.</p>

<p class="src1">glLightfv(GL_LIGHT1, GL_DIFFUSE, LightDiffuse);<span class="kom">// Nastaven� p��m�ho sv�tla</span></p>

<p>Nyn� nastav�me pozici sv�tla. Ta je ulo�ena v LightPosition.</p>

<p class="src1">glLightfv(GL_LIGHT1, GL_POSITION,LightPosition);<span class="kom">// Nastaven� pozice sv�tla</span></p>

<p>Nakonec zapneme sv�tlo jedna. Sv�tlo je nastaven�, um�st�n� a zapnut�, jakmile zavol�me glEnable(GL_LIGHTING) rozsv�t� se.</p>

<p class="src1">glEnable(GL_LIGHT1);<span class="kom">// Zapne sv�tlo</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace prob�hla v po��dku</span></p>
<p class="src0">}</p>

<p>Vykresl�me krychli s texturami. Kdy� nepochop�te co n�kter� ��dky d�laj�, pod�vejte se do lekce 6.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,z);</p>
<p class="src"></p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);</p>

<p>Dal�� ��dek je podobn� ��dku v lekci 6, ale nam�sto texture[0] tu m�me texture[filter]. Kdy� stiskneme kl�vesu F, hodnota ve filter se zv���. Bude-li v�t�� ne� 2, nastav�me zase 0. P�i startu programu bude filter nastaven na 0. Prom�nnou filter tedy ur�ujeme, kterou ze t�� textur m�me pou��t.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter]);<span class="kom">// Zvol� texturu</span></p>

<p>P�i pou�it� sv�tel mus�me definovat norm�lu povrchu. Je to ��ra vych�zej�c� ze st�edu polygonu v 90 stup�ov�m �hlu. �ekne jak�m sm�rem je �elo polygonu. Kdy� ji neur��te, stane se hodn� divn�ch v�c�. Povrchy kter� by m�ly sv�tit se nerozsv�t�, �patn� strana polygonu sv�tit bude, atd. Norm�la po�aduje bod vych�zej�c� z polygonu. Pohled na p�edn� povrch ukazuje �e norm�la je kladn� na ose z. To znamen� �e norm�la ukazuje k div�kovi. Na zadn� stran� norm�la jde od div�ka, do obrazovky. Kdy� bude kostka oto�en� o 180 stup�� v na ose x nebo y, p�edn� povrch bude ukazovat do obrazovky a zadn� uvid� div�k. Bez ohledu na to kter� povrch je vid�t div�kem, norm�la tohoto povrchu jde sm�rem k n�mu. Kdy� se tak stane, povrch bude osv�tlen. U dal��ch bod� norm�ly sm�rem k sv�tlu bude povrch tak� sv�tl�. Kdy� se posunete do st�edu kostky, bude tmav�. Norm�la je bod ven, nikoli dovnit�, proto nen� sv�tlo uvnit� a tak to m� b�t.</p>

<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// P�edn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f, 1.0f);<span class="kom">// Norm�la</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Zadn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f,-1.0f);<span class="kom">// Norm�la</span></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Horn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Norm�la</span></p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Spodn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f,-1.0f, 0.0f);<span class="kom">// Norm�la</span></p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Prav� st�na</span></p>
<p class="src2">glNormal3f( 1.0f, 0.0f, 0.0f);<span class="kom">// Norm�la</span></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Lev� st�na</span></p>
<p class="src2">glNormal3f(-1.0f, 0.0f, 0.0f);<span class="kom">// Norm�la</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">xrot+=xspeed;</p>
<p class="src1">yrot+=yspeed;</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Posuneme se dol� k WinMain(). P�id�me k�d k zapnut�/vypnut� sv�tla, ot��en�, v�b�r filtru a posun kostky do/z obrazovky. T�sn� u konce WinMain() uvid�te p��kaz SwapBuffers(hDC). Ihned za tento ��dek p�id�me k�d.</p>

<p>N�sleduj�c� k�d zji��uje, zda je stisknuta kl�vesa L. Je-li stisknuta ale lp nen� false, kl�vesa je�t� nebyla uvoln�na.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">SwapBuffers(hDC);<span class="kom">// Prohozen� buffer�</span></p>
<p class="src"></p>
<p class="src4">if (keys['L'] &amp;&amp; !lp)<span class="kom">// Kl�vesa L - sv�tlo</span></p>
<p class="src4">{</p>

<p>Kdy� bude lp false, L nebylo stisknuto, nebo bylo uvoln�no. Tento trik je pou�it pro p��pad, kdy je kl�vesa dr�ena d�le a my chceme, aby se k�d vykonal pouze jednou. P�i prvn�m pr�chodu se lp nastav� na true a prom�nn� light se invertuje. P�i dal��m pr�chodu je u� lp true a k�d se neprovede a� do uvoln�n� kl�vesy, kter� nastav� lp zase na false. Kdyby zde toto nebylo, sv�tlo by p�i stisku akor�t blikalo.</p>

<p class="src5">lp=TRUE;</p>
<p class="src5">light=!light;</p>

<p>Nyn� se pod�v�me na prom�nnou light. Kdy� bude false, vypneme sv�tlo, kdy� ne zapneme ho.</p>

<p class="src5">if (!light)</p>
<p class="src5">{</p>
<p class="src6">glDisable(GL_LIGHTING);<span class="kom">// Vypne sv�tlo</span></p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">glEnable(GL_LIGHTING);<span class="kom">// Zapne sv�tlo</span></p>
<p class="src5">}</p>
<p class="src4">}</p>

<p>N�sleduje nastaven� prom�nn� lp na false p�i uvoln�n� kl�vesy L.</p>

<p class="src4">if (!keys['L'])</p>
<p class="src4">{</p>
<p class="src5">lp=FALSE;</p>
<p class="src4">}</p>

<p>Nyn� o�et��me stisk F. Kdy� se stiskne, dojde ke zv��en� filter. Pokud bude v�t�� ne� 2, nastav�me ho zp�t na 0. K o�et�en� del��ho stisku kl�vesy pou�ijeme stejn� zp�sob jako u sv�tla.</p>

<p class="src4">if (keys['F'] &amp;&amp; !fp)<span class="kom">// Kl�vesa F - zm�na texturov�ho filtru</span></p>
<p class="src4">{</p>
<p class="src5">fp=TRUE;</p>
<p class="src5">filter+=1;</p>
<p class="src"></p>
<p class="src5">if (filter&gt;2)</p>
<p class="src5">{</p>
<p class="src6">filter=0;</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys['F'])<span class="kom">// Uvoln�n� F</span></p>
<p class="src4">{</p>
<p class="src5">fp=FALSE;</p>
<p class="src4">}</p>

<p>Otestuj� stisk kl�vesy Page Up. Kdy� bude stisknuto, sn��me prom�nnou z. To zp�sob� vzdalov�n� kostky v p��kazu glTranslatef(0.0f,0.0f,z).</p>

<p class="src4">if (keys[VK_PRIOR])<span class="kom">// Kl�vesa Page Up - zv��� zano�en� do obrazovky</span></p>
<p class="src4">{</p>
<p class="src5">z-=0.02f;</p>
<p class="src4">}</p>

<p>Otestuj� stisk kl�vesy Page Down. Kdy� bude stisknuta, zv���me prom�nnou z. To zp�sob� p�ibli�ov�n� kostky v p��kazu glTranslatef(0.0f,0.0f,z).</p>

<p class="src4">if (keys[VK_NEXT])<span class="kom">// Kl�vesa Page Down - sn�� zano�en� do obrazovky</span></p>
<p class="src4">{</p>
<p class="src5">z+=0.02f;</p>
<p class="src4">}</p>

<p>D�le zkontrolujeme kurzorov� kl�vesy. Bude-li stisknuto vlevo/vpravo, prom�nn� xspeed se bude zvy�ovat/sni�ovat. Bude-li stisknuto nahoru/dol�, prom�nn� yspeed se bude zvy�ovat/sni�ovat. Jestli si vzpom�n�te, v��e jsem psal, �e vysok� hodnoty zp�sob� rychlou rotaci. Dlouh� stisk n�jak� kl�vesy zp�sob� pr�v� rychlou rotaci kostky.</p>

<p class="src4">if (keys[VK_UP])<span class="kom">// �ipka nahoru</span></p>
<p class="src4">{</p>
<p class="src5">xspeed-=0.01f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN])<span class="kom">// �ipka dolu</span></p>
<p class="src4">{</p>
<p class="src5">xspeed+=0.01f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_RIGHT])<span class="kom">// �ipka vpravo</span></p>
<p class="src4">{</p>
<p class="src5">yspeed+=0.01f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_LEFT])<span class="kom">// �ipka vlevo</span></p>
<p class="src4">{</p>
<p class="src5">yspeed-=0.01f;</p>
<p class="src4">}</p>

<p>Nyn� byste m�li v�d�t jak vytvo�it vysoce kvalitn�, realisticky vypadaj�c�, texturovan� objekt. Tak� jsme se n�co dozv�d�li o t�ech r�zn�ch filtrech. Stiskem ur�it�ch kl�ves m��ete pohybovat objektem na obrazovce, a nakonec v�me jak aplikovat jednoduch� sv�tlo. Zkuste experimentovat s jeho pozic� a barvou.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Ji�� Rajsk� - RAJSOFT junior <?VypisEmail('predator.jr@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson07.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson07_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson07.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson07.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson07.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson07.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:brad@choate.net">Brad Choate</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson07.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson07.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson07.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson07.zip">GLUT</a> k�d t�to lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson07.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson07.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson07.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson07.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson07.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson07.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson07.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson07.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson07.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson07.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson07.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson07.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson07.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson07.zip">Solaris</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson07.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson07.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson07.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(7);?>
<?FceNeHeOkolniLekce(7);?>

<?
include 'p_end.php';
?>
