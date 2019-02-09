<?
$g_title = 'CZ NeHe OpenGL - Lekce 26 - Odrazy a jejich o�ez�v�n� za pou�it� stencil bufferu';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(26);?>

<h1>Lekce 26 - Odrazy a jejich o�ez�v�n� za pou�it� stencil bufferu</h1>

<p class="nadpis_clanku">Tutori�l demonstruje extr�mn� realistick� odrazy za pou�it� stencil bufferu a jejich o�ez�v�n�, aby &quot;nevystoupily&quot; ze zrcadla. Je mnohem v�ce pokrokov� ne� p�edchoz� lekce, tak�e p�ed za��tkem �ten� doporu�uji men�� opakov�n�. Odrazy objekt� nebudou vid�t nad zrcadlem nebo na druh� stran� zdi a budou m�t barevn� n�dech zrcadla - skute�n� odrazy.</p>

<p><b>D�le�it�:</b> Proto�e grafick� karty Voodoo 1, 2 a n�kter� jin� nepodporuj� stencil buffer, nebude na nich tento tutori�l fungovat. Pokud si nejste jist�, �e va�e karta stencil buffer podporuje, st�hn�te si zdrojov� k�d a zkuste jej spustit. Krom� toho budete tak� pot�ebovat procesor a grafickou kartu se slu�n�m v�konem. Na m� GeForce 1 ob�as vid�m mal� zpomalen�. Demo b�� nejl�pe v 32 bitov�ch barv�ch.</p>

<p>Prvn� ��st k�du je celkem standardn�.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
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

<p>Nastav�me pole pro definici osv�tlen�. Okoln� sv�tlo bude 70% b�l�. Dif�zn� sv�tlo nastavuje rozptyl osv�tlen� (mno�stv� sv�tla rovnom�rn� odr�en� na ploch�ch objekt�). V tomto p��pad� odr��me plnou intenzitou. Posledn� je pozice. Pokud bychom ho mohli spat�it, plulo by v prav�m horn�m rohu monitoru.</p>

<p class="src0"><span class="kom">// Parametry sv�tla</span></p>
<p class="src0">static GLfloat LightAmb[] = {0.7f, 0.7f, 0.7f, 1.0f};<span class="kom">// Okoln�</span></p>
<p class="src0">static GLfloat LightDif[] = {1.0f, 1.0f, 1.0f, 1.0f};<span class="kom">// Rozpt�len�</span></p>
<p class="src0">static GLfloat LightPos[] = {4.0f, 4.0f, 6.0f, 1.0f};<span class="kom">// Pozice</span></p>

<p>Ukazatel q je pro quadratic koule (pl�ov� m��). Xrot a yrot ukl�daj� hodnoty nato�en� m��e, xrotspeed a yrotspeed definuj� rychlost rotace. Zoom pou��v�me pro p�ibli�ov�n� a oddalov�n� sc�ny a height je v��ka bal�nu nad podlahou. Pole texture[] u� standardn� ukl�d� textury.</p>

<p class="src0">GLUquadricObj *q;<span class="kom">// Quadratic pro kreslen� koule (m��e)</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot = 0.0f;<span class="kom">// X rotace</span></p>
<p class="src0">GLfloat yrot = 0.0f;<span class="kom">// Y rotace</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrotspeed = 0.0f;<span class="kom">// Rychlost x rotace</span></p>
<p class="src0">GLfloat yrotspeed = 0.0f;<span class="kom">// Rychlost y rotace</span></p>
<p class="src"></p>
<p class="src0">GLfloat zoom = -7.0f;<span class="kom">// Hloubka v obrazovce</span></p>
<p class="src0">GLfloat height = 2.0f;<span class="kom">// V��ka m��e nad sc�nou</span></p>
<p class="src"></p>
<p class="src0">GLuint texture[3];<span class="kom">// 3 textury</span></p>

<p>Vytv��en� line�rn� filtrovan�ch textur z bitmap je standardn�, v p�edchoz�ch lekc�ch jsme jej pou��vali velice �asto, tak�e ho sem nebudu opisovat. Na obr�zc�ch vid�te texturu m��e, podlahy a sv�tla odr�en�ho od m��e.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_26_tex_mic.jpg" width="128" height="128" alt="Textura m��e" />
<img src="images/nehe_tut/tut_26_tex_podlaha.jpg" width="128" height="128" alt="Textura podlahy" />
<img src="images/nehe_tut/tut_26_tex_svetlo.jpg" width="128" height="128" alt="Textura sv�tla odr�en�ho od m��e" />
</div>

<p>Inicializace OpenGL.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Loading textur</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Ukon�� program</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Vyhlazen� st�nov�n�</span></p>
<p class="src1">glClearColor(0.2f, 0.5f, 1.0f, 1.0f);<span class="kom">// Sv�tle modr� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>

<p>P��kaz glClearStencil() definuje chov�n� funkce glClear() p�i maz�n� stencil bufferu. V tomto p��pad� ho budeme vypl�ovat nulami.</p>

<p class="src1">glClearStencil(0);<span class="kom">// Nastaven� maz�n� stencil bufferu</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� testov�n� hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testov�n� hloubky</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivn� korekce</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Mapov�n� textur</span></p>

<p>Nastav�me sv�tla. Pro okoln� pou�ijeme hodnoty z pole LightAmb[], rozptylov� sv�tlo definujeme pomoc� LightDif[] a pozici z LightPos[]. Nakonec povol�me sv�tla. Pokud bychom d�le v k�du cht�li vypnout v�echna sv�tla, pou�ili bychom glDisable(GL_LIGHTING), ale p�i vyp�n�n� jenom jednoho posta�� pouze glDisable(GL_LIGHT(0a�7)). GL_LIGHTING v parametru zakazuje glob�ln� v�echna sv�tla.</p>

<p class="src1">glLightfv(GL_LIGHT0, GL_AMBIENT, LightAmb);<span class="kom">// Okoln�</span></p>
<p class="src1">glLightfv(GL_LIGHT0, GL_DIFFUSE, LightDif);<span class="kom">// Rozptylov�</span></p>
<p class="src1">glLightfv(GL_LIGHT0, GL_POSITION, LightPos);<span class="kom">// Pozice</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Povol� sv�tlo 0</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Povol� sv�tla</span></p>

<p>D�le vytvo��me a nastav�me objekt quadraticu. Vygenerujeme mu norm�ly pro sv�tlo a texturov� koordin�ty, jinak by m�l ploch� st�nov�n� a ne�ly by na n�j namapovat textury.</p>

<p class="src1">q = gluNewQuadric();<span class="kom">// Nov� quadratic</span></p>
<p class="src"></p>
<p class="src1">gluQuadricNormals(q, GL_SMOOTH);<span class="kom">// Norm�ly pro sv�tlo</span></p>
<p class="src1">gluQuadricTexture(q, GL_TRUE);<span class="kom">// Texturov� koordin�ty</span></p>

<p>Nastav�me mapov�n� textur na vykreslovan� objekty a to tak, aby p�i nat��en� m��e byla viditeln� st�le stejn� ��st textury. Zat�m ho nezap�n�me.</p>

<p class="src1">glTexGeni(GL_S, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);<span class="kom">// Automatick� mapov�n� textur</span></p>
<p class="src1">glTexGeni(GL_T, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);<span class="kom">// Automatick� mapov�n� textur</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace v po��dku</span></p>
<p class="src0">}</p>

<p>N�sleduj�c� funkci budeme volat pro vykreslen� pl�ov�ho m��e. Bude j�m quadraticov� koule s nalepenou texturou. Nastav�me barvu na b�lou, aby se textura nezabarvovala, pot� zvol�me texturu a vykresl�me kouli o polom�ru 0.35 jednotek, s 32 rovnob�kami a 16 poledn�ky.</p>

<p class="src0">void DrawObject()<span class="kom">// Vykresl� pl�ov� m��</span></p>
<p class="src0">{</p>
<p class="src1">glColor3f(1.0f, 1.0f, 1.0f);<span class="kom">// B�l� barva</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[1]);<span class="kom">// Zvol� texturu m��e</span></p>
<p class="src1">gluSphere(q, 0.35f, 32, 16);<span class="kom">// Nakresl� kouli</span></p>

<p>Po vykreslen� prvn� koule vybereme texturu sv�tla, nastav�me op�t b�lou barvu, ale tentokr�t s 40% alfou. Povol�me blending, nastav�me jeho funkci zalo�enou na zdrojov� alfa hodnot�, zapneme kulov� mapov�n� textur a nakresl�me stejnou kouli jako p�ed chv�l�. V�sledkem je simulovan� odr�en� sv�tla od m��e, ale vlastn� se jedn� jen o sv�tl� body namapovan� na pl�ov� m��. Proto�e je povoleno kulov� mapov�n�, textura je v�dy nato�ena k pozorovateli stejnou ��st� bez ohledu na nato�en� m��e. Je tak� zapnut� blending tak�e nov� textura nep�ebije starou (jednoduch� forma multitexturingu).</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[2]);<span class="kom">// Zvol� texturu sv�tla</span></p>
<p class="src1">glColor4f(1.0f, 1.0f, 1.0f, 0.4f);<span class="kom">// B�l� barva s 40% alfou</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE);<span class="kom">// M�d blendingu</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_GEN_S);<span class="kom">// Zapne kulov� mapov�n�</span></p>
<p class="src1">glEnable(GL_TEXTURE_GEN_T);<span class="kom">// Zapne kulov� mapov�n�</span></p>
<p class="src"></p>
<p class="src1">gluSphere(q, 0.35f, 32, 16);<span class="kom">// Stejn� koule jako p�ed chv�l�</span></p>

<p>Vypneme kulov� mapov�n� a blending.</p>

<p class="src1">glDisable(GL_TEXTURE_GEN_S);<span class="kom">// Vypne kulov� mapov�n�</span></p>
<p class="src1">glDisable(GL_TEXTURE_GEN_T);<span class="kom">// Vypne kulov� mapov�n�</span></p>
<p class="src"></p>
<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vepne blending</span></p>
<p class="src0">}</p>

<p>N�sleduj�c� funkce kresl� podlahu, nad kterou se m�� vzn��. Vybereme texturu podlahy a na ose z vykresl�me �tverec s jednoduchou texturou.</p>

<p class="src0">void DrawFloor()<span class="kom">// Vykresl� podlahu</span></p>
<p class="src0">{</p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Zvol� texturu podlahy</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�k�</span></p>
<p class="src2">glNormal3f(0.0, 1.0, 0.0);<span class="kom">// Norm�lov� vektor m��� vzh�ru</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(0.0f, 1.0f);<span class="kom">// Lev� doln� bod textury</span></p>
<p class="src2">glVertex3f(-2.0, 0.0, 2.0);<span class="kom">// Lev� doln� bod podlahy</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f);<span class="kom">// Lev� horn� bod textury</span></p>
<p class="src2">glVertex3f(-2.0, 0.0,-2.0);<span class="kom">// Lev� horn� bod podlahy</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f);<span class="kom">// Prav� horn� bod textury</span></p>
<p class="src2">glVertex3f( 2.0, 0.0,-2.0);<span class="kom">// Prav� horn� bod podlahy</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(1.0f, 1.0f);<span class="kom">// Prav� doln� bod textury</span></p>
<p class="src2">glVertex3f( 2.0, 0.0, 2.0);<span class="kom">// Prav� doln� bod podlahy</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src0">}</p>

<p>Na tomto m�st� zkombinujeme v�echny objekty a obr�zky tak, abychom vytvo�ili v�slednou sc�nu. Za�neme maz�n�m obrazovky (GL_COLOR_BUFFER_BIT) na v�choz� modrou barvu, hloubkov�ho bufferu (GL_DEPTH_BUFFER_BIT) a stencil bufferu (GL_STENCIL_BUFFER_BIT). P�i �i�t�n� stencil bufferu ho vypl�ujeme nulami.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykresl� v�slednou sc�nu</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Sma�e obrazovku, hloubkov� buffer a stencil buffer</span></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT | GL_STENCIL_BUFFER_BIT);</p>

<p>Nadefinujeme rovnici o�ez�vac� plochy (clipping plane equation). Bude pou�ita p�i vykreslen� odra�en�ho m��e. Hodnota na ose y je z�porn�, to znamen�, �e uvid�me pixely jen pokud jsou kresleny pod podlahou nebo na z�porn� ��sti osy y. P�i pou�it� t�to rovnice se nezobraz� nic, co vykresl�me nad podlahou (odraz nem��e vystoupit ze zrcadla). V�ce pozd�ji.</p>

<p class="src1"><span class="kom">// Rovnice o�ez�vac� plochy</span></p>
<p class="src1">double eqr[] = { 0.0f, -1.0f, 0.0f, 0.0f };<span class="kom">// Pou�ito pro odra�en� objekt</span></p>

<p>V�emu, co bylo doposud probr�no v t�to lekci byste m�li rozum�t. Te� p�ijde n�co &quot;mali�ko&quot; hor��ho. Pot�ebujeme nakreslit odraz m��e a to tak, aby se na obrazovce zobrazoval jenom na t�ch pixelech, kde je podlaha. K tomu vyu�ijeme stencil buffer. Pomoc� funkce glClear() jsme ho vyplnili sam�mi nulami. R�zn�mi nastaven�mi, kter� si vysv�tl�me d�le, doc�l�me toho, �e se podlaha sice nezobraz� na obrazovce, ale na m�stech, kde se m�la vykreslit se stencil buffer nastav� do jedni�ky. Pro pochopen� si p�edstavte, �e je to obrazovka v pam�ti, jej� pixely jsou rovny jedni�ce, pokud se na nich objekt vykresluje a nule (nezm�n�n�) pokud ne. Na m�sta, kde je stencil buffer v jedni�ce vykresl�me ploch� odraz m��e, ale ne do stencil bufferu - viditeln� na obrazovku. Odraz vlastn� m��eme vykreslit i kdekoli jinde, ale pouze tady bude vid�t. Nakonec klasick�m zp�sobem vykresl�me v�echno ostatn�. To je asi v�echno, co byste m�li o stencil bufferu prozat�m v�d�t.</p>

<p>Nyn� u� konkr�tn� ke k�du. Resetujeme matici modelview a potom p�esuneme sc�nu o �est jednotek dol� a o zoom do hloubky. Nejlep�� vysv�tlen� pro translaci dol� bude na p��klad�. Vezm�te si list pap�ru a um�st�te jej rovnob�n� se zem� do �rovn� o��. Neuvid�te nic v�c ne� tenkou linku. Posunete-li j�m o mali�ko dol�, spat��te celou plochu, proto�e se na n�j budete d�vat v�ce ze shora nam�sto p��mo na okraj. Roz���il se zorn� �hel.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f, -0.6f, zoom);<span class="kom">// Zoom a vyv��en� kamery nad podlahu</span></p>

<p>Nov�m p��kazem definujeme barevnou masku pro vykreslovan� barvy. Funkci se p�ed�vaj� �ty�i parametry reprezentuj�c� �ervenou, zelenou, modrou a alfu. Pokud nap��klad �ervenou slo�ku nastav�me na jedna (GL_TRUE) a v�echny ostatn� na nulu (GL_FALSE), tak se bude moci zobrazit pouze �erven� barva. V opa�n�m p��pad� (0,1,1,1) se budou zobrazovat v�echny barvy mimo �ervenou. Asi tu��te, �e jsou barvy implicitn� nastaveny tak, aby se v�echny zobrazovaly. No, a proto�e v tuto chv�li nechceme nic zobrazovat zak�eme v�echny barvy.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_26_color_1111.jpg" width="63" height="63" alt="glColorMask(1,1,1,1);" />
<img src="images/nehe_tut/tut_26_color_1000.jpg" width="63" height="63" alt="glColorMask(1,0,0,0);" />
<img src="images/nehe_tut/tut_26_color_0111.jpg" width="63" height="63" alt="glColorMask(0,1,1,1);" />
</div>

<p class="src1">glColorMask(0,0,0,0);<span class="kom">// Nastav� masku barev, aby se nic nezobrazilo</span></p>

<p>Za��n�me pracovat se stencil bufferem. Nap�ed pot�ebujeme z�skat obraz podlahy vyj�d�en� jedni�kami (viz. v��e). Za�neme zapnut�m stencilov�ho testov�n� (stencil testing). Jakmile je povoleno jsme schopni modifikovat stencil buffer.</p>

<p class="src1">glEnable(GL_STENCIL_TEST);<span class="kom">// Zapne stencil buffer pro pam�ov� obraz podlahy</span></p>

<p>N�sleduj�c� p��kaz je mo�n� t�ko pochopiteln�, ale ur�it� se velice t�ko vysv�tluje. Funkce glStencilFunc(GL_ALWAYS,1,1) oznamuje OpenGL, jak� typ testu chceme pou��t na ka�d� pixel p�i jeho vykreslov�n�. GL_ALWAYS zaru��, �e test prob�hne v�dy. Druh� parametr je referen�n� hodnotou a t�et� parametr je maska. U ka�d�ho pixelu se hodnota masky ANDuje s referen�n� hodnotou a v�sledek se ulo�� do stencil bufferu. V na�em p��pad� se do n�j um�st� poka�d� jedni�ka (reference &amp; maska = 1 &amp; 1 = 1). Nyn� v�me, �e na sou�adnic�ch pixelu na obrazovce, kde by se vykreslil objekt, bude ve stencil bufferu jedni�ka.</p>

<p>Pozn.: Stencilov� testy jsou vykon�v�ny na pixelech poka�d�, kdy� se objekt vykresluje na sc�nu. Referen�n� hodnota ANDovan� s hodnotou masky se testuje proti aktu�ln� hodnot� ve stencil bufferu ANDovan� s hodnotou masky.</p>

<p class="src1">glStencilFunc(GL_ALWAYS, 1, 1);<span class="kom">// Poka�d� prob�hne, reference, maska</span></p>

<p>GlStencilOp() zpracuje t�i rozd�ln� po�adavky zalo�en� na stencilov�ch funkc�ch, kter� jsme se rozhodli pou��t. Prvn� parametr ��k� OpenGL, co m� ud�lat pokud test neusp�je. Proto�e je nastaven na GL_KEEP nech� hodnotu stencil bufferu tak, jak pr�v� je. Nicm�n� test usp�je v�dy, proto�e m�me funkci nastavenu na GL_ALWAYS. Druh� parametr ur�uje co d�lat, pokud stencil test prob�hne, ale hloubkov� test bude ne�sp�n�. Tato situace by nastala nap��klad, kdy� by se objekt vykreslil za jin�m objektem a hloubkov� test by nepovolil jeho vykreslen�. Op�t m��e b�t ignorov�n, proto�e hned n�sleduj�c�m p��kazem hloubkov� testy vyp�n�me. T�et� parametr je pro n�s d�le�it�. Definuje, co se m� vykonat, pokud test usp�je (usp�je v�dycky). V na�em p��pad� OpenGL nahrad� nulu ve stencil bufferu na jedni�ku (referen�n� hodnota ANDovan� s maskou = 1).</p>

<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_REPLACE);<span class="kom">// Vykreslen�m nastav�me konkr�tn� bit ve stencil bufferu na 1</span></p>

<p>Po nastaven� stencilov�ch test� vypneme hloubkov� testy a zavol�me funkci pro vykreslen� podlahy.</p>

<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne testov�n� hloubky</span></p>
<p class="src"></p>
<p class="src1">DrawFloor();<span class="kom">// Vykresl� podlahu (do stencil bufferu ne na sc�nu)</span></p>

<p>Tak�e te� m�me ve stencil bufferu neviditelnou masku podlahy. Tak dlouho, jak bude stencilov� testov�n� zapnut�, budeme moci zobrazovat pixely pouze tam, kde je stencil buffer v jedni�ce (tam kde byla vykreslena podlaha). Zapneme hloubkov� testov�n� a nastav�me masku barev zp�t do jedni�ek. To znamen�, �e se od te� v�e vykreslovan� opravdu zobraz�.</p>

<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>
<p class="src1">glColorMask(1, 1, 1, 1);<span class="kom">// Povol� zobrazov�n� barev</span></p>

<p>Nam�sto u�it� GL_ALWAYS pro stencilovou funkci, pou�ijeme GL_EQUAL. Reference i maska z�st�vaj� v jedni�ce. Pro stencilov� operace nastav�me v�echny parametry na GL_KEEP. Vykreslovan� pixely se zobraz� na obrazovku POUZE tehdy, kdy� je na jejich sou�adnic�ch hodnota stencilu v jedni�ce (reference ANDovan� s maskou (1), kter� jsou rovny (GL_EQUAL) hodnot� stencil bufferu ANDovan� s maskou (tak� 1)). GL_KEEP zajist�, �e se hodnoty ve stencil bufferu nebudou modifikovat.</p>

<p class="src1">glStencilFunc(GL_EQUAL, 1, 1);<span class="kom">// Zobraz� se pouze pixely na jedni�k�ch ve stencil bufferu (podlaha)</span></p>
<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_KEEP);<span class="kom">// Nem�nit obsah stencil bufferu</span></p>

<p>Zapneme o�ez�vac� plochu zrcadla, kter� je definov�na rovnic� ulo�enou v poli eqr[]. Umo��uje, aby byl odraz objektu vykreslen pouze sm�rem dol� od podlahy (v podlaze). Touto cestou nebude moci odraz m��e vystoupit do &quot;re�ln�ho sv�ta&quot;. Pokud nech�pete, co je t�mto m�n�no zakoment��ujte v k�du ��dek glEnable(GL_CLIP_PLANE0), zkompilujte program a zkuste proj�t re�ln�m m��em skrz podlahu. Pokud clipping nebude zapnut� uvid�te, jak p�i vstupu m��e do podlahy jeho odraz vystoup� nahoru nad podlahu. V�e vid�te na obr�zku. Mimochodem, v�imn�te si, �e vystoupiv�� obraz je po��d vid�t jen tam, kde je ve stencil bufferu obraz podlahy.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_26_clip.jpg" width="80" height="80" alt="Clipping zapnut�" />
<img src="images/nehe_tut/tut_26_no_clip.jpg" width="80" height="80" alt="Clipping vypnut�" />
</div>

<p>Po zapnut� o�ez�vac� plochy 0 (oby�ejn� jich m��e b�t 0 a� 5) j� p�ed�me parametry rovnice ulo�en� v eqr[].</p>

<p class="src1">glEnable(GL_CLIP_PLANE0);<span class="kom">// Zapne o�ez�vac� testy pro odraz</span></p>
<p class="src1">glClipPlane(GL_CLIP_PLANE0, eqr);<span class="kom">// Rovnice o�ez�vac� roviny</span></p>

<p>Z�lohujeme aktu�ln� stav matice, aby ji zm�ny trvale neovlivnily. Zad�n�m m�nus jedni�ky do glScalef() obr�t�me sm�r osy y. Do t�to chv�le proch�zela zezdola nahoru, nyn� naopak. Stejn� efekt by m�la rotace o 180�. V�e je te� invertovan� jako v zrcadle. Pokud n�co vykresl�me naho�e, zobraz� se to dole (zrcadlo je vodorovn� ne svisle), rotujeme-li po sm�ru, objekt se oto�� proti sm�ru hodinov�ch ru�i�ek a podobn�. Tento stav se m��e zru�it bu� op�tovn�m vol�n�m glScalef(), kter� provede op�tovnou inverzi nebo POPnut�m matice.</p>

<p class="src1">glPushMatrix();<span class="kom">// Z�loha matice</span></p>
<p class="src2">glScalef(1.0f, -1.0f, 1.0f);<span class="kom">// Zrcadlen� sm�ru osy y</span></p>

<p>Nadefinujeme pozici sv�tla podle pole LightPos[]. Na re�ln� m�� sv�t� z prav� horn� strany, ale proto�e se i poloha sv�tla zrcadl�, tak na odraz bude z��it zezdola.</p>

<p class="src2">glLightfv(GL_LIGHT0, GL_POSITION, LightPos);<span class="kom">// Um�st�n� sv�tla</span></p>

<p>P�esuneme se na ose y nahoru nebo dol� v z�vislosti na prom�nn� height. Op�t je translace zrcadlena, tak�e pokud se p�esuneme o p�t jednotek nad podlahu budeme vlastn� o p�t jednotek pod podlahou. Stejn�m zp�sobem pracuj� i rotace. Nakonec nakresl�me objekt pl�ov�ho m��e a POPneme matici. T�m zru��me v�echny zm�ny od vol�n� glPushMatrix().</p>

<p class="src2">glTranslatef(0.0f, height, 0.0f);<span class="kom">// Um�st�n� m��e</span></p>
<p class="src2">glRotatef(xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src2">glRotatef(yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src2">DrawObject();<span class="kom">// Vykresl� m�� (odraz)</span></p>
<p class="src"></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnov� matici</span></p>

<p>Vypneme o�ez�vac� testy, tak�e se budou zobrazovat i objekty nad podlahou. Tak� vypneme stencil testy, abychom mohli vykreslovat i jinam ne� na pixely, kter� byly modifikov�ny podlahou.</p>

<p class="src1">glDisable(GL_CLIP_PLANE0);<span class="kom">// Vypne o�ez�vac� rovinu</span></p>
<p class="src1">glDisable(GL_STENCIL_TEST);<span class="kom">// U� nebudeme pot�ebovat stencil testy</span></p>

<p>P�iprav�me program na vykreslen� podlahy. Op�t um�st�me sv�tlo, ale tak, aby u� jeho pozice nebyla zrcadlena. Osa y je sice u� v po��dku, ale sv�tlo je st�le vpravo dole.</p>

<p class="src1">glLightfv(GL_LIGHT0, GL_POSITION, LightPos);<span class="kom">// Um�st�n� sv�tla</span></p>

<p>Zapneme blending, vypneme sv�tla (glob�ln�) a nastav�me 80% pr�hlednost bez zm�ny barev textur (b�l� nep�id�v� barevn� n�dech). M�d blendingu je nastaven pomoc� glBlendFunc(). Pot� vykresl�me ��ste�n� pr�hlednou podlahu. Asi nech�pete, pro� jsme nap�ed kreslili odraz a a� pot� zrcadlo. Je to proto, �e chceme, aby byl odraz m��e sm�ch�n s barvami podlahy. Pokud se d�v�te do modr�ho zrcadla, tak tak� o�ek�v�te trochu namodral� odraz. Vykreslen� m��e nap�ed zp�sob� zabarven� podlahou. Efekt je v�ce re�ln�.</p>

<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending, jinak by se odraz m��e nezobrazil</span></p>
<p class="src1">glDisable(GL_LIGHTING);<span class="kom">// Kv�li blendingu vypneme sv�tla</span></p>
<p class="src"></p>
<p class="src1">glColor4f(1.0f, 1.0f, 1.0f, 0.8f);<span class="kom">// B�l� barva s 80% pr�hlednost�</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);<span class="kom">// Funkce na b�zi alfy zdroje a jedna m�nus alfy c�le</span></p>
<p class="src"></p>
<p class="src1">DrawFloor();<span class="kom">// Vykresl� podlahu</span></p>

<p>A kone�n� vykresl�me re�ln� m��. Nap�ed ale zapneme sv�tla (pozice u� je nastaven�). Kdybychom nevypnuli blending, m�� by p�i pr�chodu podlahou vypadal jako odraz. To nechceme.</p>

<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne sv�tla</span></p>
<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vypne blending</span></p>

<p>Tento m�� u� narozd�l od jeho odrazu neo�ez�v�me. Kdybychom pou��vali clipping, nezobrazil by se pod podlahou. Doc�lili bychom toho definov�n�m hodnoty +1.0f na ose y u rovnice o�ez�vac� roviny. Pro toto demo nen� ��dn� d�vod, abychom m�� nemohli vid�t pod podlahou. V�echny translace i rotace z�st�vaj� stejn� jako minule s t�m rozd�lem, �e nyn� u� jde osa y klasick�m sm�rem. Kdy� posuneme re�ln� m�� dol�, odraz jde nahoru a naopak.</p>

<p class="src1">glTranslatef(0.0f, height, 0.0f);<span class="kom">// Um�st�n� m��e</span></p>
<p class="src1">glRotatef(xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src1">DrawObject();<span class="kom">// Vykresl� m��</span></p>

<p>Zv�t��me hodnoty nato�en� m��e a jeho odrazu o rychlost rotac�. P�ed n�vratem z funkce zavol�me glFlush(), kter� po�k� na ukon�en� renderingu. Prevence mihot�n� na pomalej��ch grafick�ch kart�ch.</p>

<p class="src1">xrot += xrotspeed;<span class="kom">// Zv�t�� nato�en�</span></p>
<p class="src1">yrot += yrotspeed;<span class="kom">// Zv�t�� nato�en�</span></p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vypr�zdn� pipeline</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�echno v po��dku</span></p>
<p class="src0">}</p>

<p>N�sleduj�c� funkce testuje stisk kl�ves. Vol�me ji periodicky v hlavn� smy�ce WinMain(). �ipkami ovl�d�me rychlost rotace m��e, kl�vesy A a Z p�ibli�uj�/oddaluj� sc�nu, Page Up s Page Down umo��uj� zm�nit v��ku pl�ov�ho m��e nad podlahou. Kl�vesa ESC pln� st�le svoji funkci, ale jej� um�st�n� z�stalo ve WinMain().</p>

<p class="src0">void ProcessKeyboard()<span class="kom">// Ovl�d�n� kl�vesnic�</span></p>
<p class="src0">{</p>
<p class="src1">if (keys[VK_RIGHT]) yrotspeed += 0.08f;<span class="kom">// �ipka vpravo zv��� rychlost y rotace</span></p>
<p class="src1">if (keys[VK_LEFT]) yrotspeed -= 0.08f;<span class="kom">// �ipka vlevo sn�� rychlost y rotace</span></p>
<p class="src1">if (keys[VK_DOWN]) xrotspeed += 0.08f;<span class="kom">// �ipka dol� zv��� rychlost x rotace</span></p>
<p class="src1">if (keys[VK_UP]) xrotspeed -= 0.08f;<span class="kom">// �ipka nahoru sn�� rychlost x rotace</span></p>
<p class="src"></p>
<p class="src1">if (keys['A']) zoom +=0.05f;<span class="kom">// A p�ibl�� sc�nu</span></p>
<p class="src1">if (keys['Z']) zoom -=0.05f;<span class="kom">// Z odd�l� sc�nu</span></p>
<p class="src"></p>
<p class="src1">if (keys[VK_PRIOR]) height += 0.03f;<span class="kom">// Page Up zv�t�� vzd�lenost m��e nad podlahou</span></p>
<p class="src1">if (keys[VK_NEXT]) height -= 0.03f;<span class="kom">// Page Down zmen�� vzd�lenost m��e nad podlahou</span></p>
<p class="src0">}</p>

<p>V CreateGLWindow() je �pln� miniaturn� zm�na, nicm�n� by bez n� program nefungoval. Ve struktu�e PIXELFORMATDESCRIPTOR pfd nastav�me ��slo, kter� vyjad�uje po�et bit� stencil bufferu. Ve v�ech minul�ch lekc�ch jsme ho nepot�ebovali, tak�e mu byla p�i�azena nula. P�i pou�it� stencil bufferu MUS� b�t po�et jeho bit� v�t�� nebo roven jedn�! N�m sta�� jeden bit.</p>

<p class="src0"><span class="kom">// Uprost�ed funkce CreateGLWindow()</span></p>
<p class="src"></p>
<p class="src1">static PIXELFORMATDESCRIPTOR pfd=<span class="kom">// Oznamuje Windows jak chceme v�e nastavit</span></p>
<p class="src1">{</p>
<p class="src2">sizeof(PIXELFORMATDESCRIPTOR),<span class="kom">// Velikost struktury</span></p>
<p class="src2">1,<span class="kom">// ��slo verze</span></p>
<p class="src2">PFD_DRAW_TO_WINDOW |<span class="kom">// Podpora okna</span></p>
<p class="src2">PFD_SUPPORT_OPENGL |<span class="kom">// Podpora OpenGL</span></p>
<p class="src2">PFD_DOUBLEBUFFER,<span class="kom">// Podpora double bufferingu</span></p>
<p class="src2">PFD_TYPE_RGBA,<span class="kom">// RGBA form�t</span></p>
<p class="src2">bits,<span class="kom">// Barevn� hloubka</span></p>
<p class="src2">0, 0, 0, 0, 0, 0,<span class="kom">// Bity barev ignorov�ny</span></p>
<p class="src2">0,<span class="kom">// ��dn� alfa buffer</span></p>
<p class="src2">0,<span class="kom">// Ignorov�n shift bit</span></p>
<p class="src2">0,<span class="kom">// ��dn� akumula�n� buffer</span></p>
<p class="src2">0, 0, 0, 0,<span class="kom">// Akumula�n� bity ignorov�ny</span></p>
<p class="src2">16,<span class="kom">// 16 bitov� z-buffer</span></p>
<p class="src2"><span class="warning">1</span>,<span class="kom">// Stencil buffer <span class="warning">(D�LE�IT�)</span></span></p>
<p class="src2">0,<span class="kom">// ��dn� auxiliary buffer</span></p>
<p class="src2">PFD_MAIN_PLANE,<span class="kom">// Hlavn� vykreslovac� vrstva</span></p>
<p class="src2">0,<span class="kom">// Rezervov�no</span></p>
<p class="src2">0, 0, 0<span class="kom">// Maska vrstvy ignorov�na</span></p>
<p class="src1">};</p>

<p>Jak jsem se zm�nil v��e, test stisknut� kl�ves u� nebudeme vykon�vat p��mo ve WinMain(), ale ve funkci ProcessKeyboard(), kterou vol�me hned po vykreslen� sc�ny.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src"></p>
<p class="src5">DrawGLScene();<span class="kom">// Vykresl� sc�nu</span></p>
<p class="src5">SwapBuffers(hDC);<span class="kom">// Prohod� buffery</span></p>
<p class="src"></p>
<p class="src5">ProcessKeyboard();<span class="kom">// Vstup z kl�vesnice</span></p>

<p>Douf�m, �e jste si u�ili tuto lekci. V�m, �e prob�ran� t�ma nebylo zrovna nejjednodu���, ale co se d� d�lat? Byl to jeden z nejt쾹�ch tutori�l�, jak jsem kdy napsal. Pro m� je celkem snadn� pochopit, co kter� ��dek d�l� a kter� p��kaz se mus� pou��t, aby vznikl po�adovan� efekt. Ale sedn�te si k po��ta�i a pokuste se to vysv�tlit lidem, kte�� nev�, co to je stencil buffer a mo�n� o n�m dokonce v �ivot� nesly�eli (P�ekl.: M�j p��pad). Osobn� si mysl�m, �e i kdy� mu napoprv� neporozum�te, po druh�m p�e�ten� by m�lo b�t v�e jasn�...</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?> &amp; Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson26.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson26_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson26.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson26.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson26.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson26.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson26.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson26.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:grayfox@pobox.sk">Gray Fox</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson26.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson26.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(26);?>
<?FceNeHeOkolniLekce(26);?>

<?
include 'p_end.php';
?>
