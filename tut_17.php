<?
$g_title = 'CZ NeHe OpenGL - Lekce 17 - 2D fonty z textur';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(17);?>

<h1>Lekce 17 - 2D fonty z textur</h1>

<p class="nadpis_clanku">V t�to lekci se nau��te, jak vykreslit font pomoc� texturou omapovan�ho obd�ln�ku. Dozv�te se tak�, jak pou��vat pixely m�sto jednotek. I kdy� nem�te r�di mapov�n� 2D znak�, najdete zde spoustu nov�ch informac� o OpenGL.</p>

<p>Tu��m, �e u� v�s asi fonty unavuj�. Textov� lekce v�s, nicm�n� nenau�ili jenom "n�co vypsat na monitor", nau�ili jste se tak� 3D fonty, mapov�n� textur na cokoli a spoustu dal��ch v�c�. Nicm�n�, co se stane pokud budete kompilovat projekt pro platformu, kter� nepodporuje fonty? Pod�v�te se do lekce 17... Pokud si pamatujete na prvn� lekci o fontech (13), tak jsem tam vysv�tloval pou��v�n� textur pro vykreslov�n� znak� na obrazovku. Oby�ejn�, kdy� pou��v�te textury ke kreslen� textu na obrazovku, spust�te grafick� program, zvol�te font, nap�ete znaky, ulo��te bitmapu a "loadujete" ji do sv�ho programu. Tento postup nen� zrovna efektivn� pro program, ve kter�m pou��v�te hodn� text� nebo texty, kter� se neust�le m�n�. Ale jak to ud�lat l�pe? Program v t�to lekci pou��v� pouze JEDNU! texturu. Ka�d� znak na tomto obr�zku bude zab�rat 16x16 pixel�. Bitmapa tedy celkem zab�r� �tverec o stran� 256 bod� (16*16=256) - standardn� velikost. Tak�e... poj�me vytvo�it 2D font z textury. Jako oby�ejn�, i tentokr�t rozv�j�me prvn� lekci.</p>

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
<p class="src0">GLuint base;<span class="kom">// Ukazatel na prvn� z display list� pro font</span></p>
<p class="src0">GLuint texture[2];<span class="kom">// Ukl�d� textury</span></p>
<p class="src0">GLuint loop;<span class="kom">// Pomocn� pro cykly</span></p>
<p class="src"></p>
<p class="src0">GLfloat cnt1;<span class="kom">// ��ta� 1 pro pohyb a barvu textu</span></p>
<p class="src0">GLfloat cnt2;<span class="kom">// ��ta� 2 pro pohyb a barvu textu</span></p>

<p>N�sleduj�c� k�d je trochu odli�n�, od toho z p�edchoz�ch lekc�. V�imn�te si, �e TextureImage[] ukl�d� dva z�znamy o obr�zc�ch. Je velmi d�le�it� zdvojit pam�ov� m�sto a loading. Jedno �patn� ��slo by mohlo zplod� p�ete�en� pam�ti nebo tot�ln� error.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Nahraje bitmapu a konvertuje na texturu </span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>
<p class="src1">AUX_RGBImageRec *TextureImage[2];<span class="kom">// Alokuje m�sto pro bitmapy</span></p>

<p>Pokud byste zam�nili ��slo 2 za jak�koli jin�, budou se d�t v�ci. V�dy se mus� rovnat ��slu z p�edchoz� ��dky (tedy v TextureImage[] ). Textury, kter� chceme nahr�t se jmenuj� font.bmp a bumps.bmp. Tu druhou m��ete zam�nit - nen� a� tak podstatn�.</p>

<p class="src1">memset(TextureImage,0,sizeof(void *)*2);<span class="kom">// Nastav� ukazatel na NULL</span></p>
<p class="src"></p>
<p class="src1">if ((TextureImage[0]=LoadBMP(&quot;Data/Font.bmp&quot;)) &amp;&amp; (TextureImage[1]=LoadBMP(&quot;Data/Bumps.bmp&quot;)))</p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;<span class="kom">// Nastav� status na TRUE</span></p>

<p>Nebudu v�m ani ��kat kolik email� jsem obdr�el od lid� ptaj�c�ch se: "Pro� vid�m jenom jednu texturu?" nebo "Pro� jsou v�echny moje textury b�l�!?!". V�t�inou b�v� probl�m v tomto ��dku. Op�t pokud p�ep�ete 2 na 1, bude vid�t jenom jedna textura (druh� bude b�l�). A naopak, zam�n�te-li 2 za 3, program se zhrout�. P��kaz glGenTextures() by se m�l volat jenom jednou a t�mto jedn�m vol�n�m vytvo�it najednou v�echny textury, kter� hodl�te pou��t. U� jsem vid�l lidi, kte�� tvo�ili ka�dou texturu zvlṻ. Je dobr�, si v�dy na za��tku rozmyslet, kolik jich budete pou��vat.</p>

<p class="src2">glGenTextures(2, &amp;texture[0]);<span class="kom">// 2 textury</span></p>
<p class="src2"></p>
<p class="src2">for (loop=0; loop&lt;2; loop++)</p>
<p class="src2">{</p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop]);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src3">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[loop]-&gt;sizeX, TextureImage[loop]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[loop]-&gt;data);</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Na konci funkce uvoln�me v�echnu pam�, kterou jsme alokovali pro vytvo�en� textur. I zde si v�imn�te uvol�ov�n� dvou z�znam�.</p>

<p class="src1">for (loop=0; loop&lt;2; loop++)</p>
<p class="src1">{</p>
<p class="src2">if (TextureImage[loop])<span class="kom">// Pokud obr�zek existuje</span></p>
<p class="src2">{</p>
<p class="src3">if (TextureImage[loop]-&gt;data)<span class="kom">// Pokud existuj� data obr�zku</span></p>
<p class="src3">{</p>
<p class="src4">free(TextureImage[loop]-&gt;data);<span class="kom">// Uvoln� pam� obr�zku</span></p>
<p class="src3">}</p>
<p class="src3">free(TextureImage[loop]);<span class="kom">// Uvoln� strukturu obr�zku</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">return Status;</p>
<p class="src0">}</p>

<p>Te� vytvo��me font. Proto�e pou�ijeme trochu matematiky, zab�hneme trochu do detail�.</p>

<p class="src0">GLvoid BuildFont(GLvoid)<span class="kom">// Vytvo�en� display list� fontu</span></p>
<p class="src0">{</p>

<p>Jak u� plyne z n�zvu, budou prom�nn� pou�ity k ur�en� pozice, ka�d�ho znaku na textu�e fontu.</p>

<p class="src1">float cx;<span class="kom">// Koordin�ty x</span></p>
<p class="src1">float cy;<span class="kom">// Koordin�ty y</span></p>

<p>D�le �ekneme OpenGL, �e chceme vytvo�it 256 display list�. "base" ukazuje na prvn� display list. Potom vybereme texturu.</p>

<p class="src1">base=glGenLists(256);<span class="kom">// 256 display list�</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// V�b�r textury</span></p>

<p>Za�neme cyklus generuj�c� v�ech 256 znak�.</p>

<p class="src1">for (loop=0; loop&lt;256; loop++)<span class="kom">// Vytv��� 256 display list�</span></p>
<p class="src1">{</p>

<p>Prvn� ��dka m��e vypadat trochu nejasn�. Symbol % vyjad�uje celo��seln� zbytek po d�len� 16. Pomoc� cx se budeme p�esunovat na textu�e po ��dc�ch (zleva doprava), cy zaji��uje pohyb ve sloupc�ch (od shora dol�). Dal��ch operace "/16.0f" konvertuje v�sledek do koordin�t� textury. Pokud bude loop rovno 16 - cx bude rovno zbytku z 16/16 tedy nule (16/16=1 zbytek 0). Ale cy bude v�sledkem "norm�ln�ho" d�len� - 16/16=1. D�le bychom se tedy m�li na textu�e p�esunout na dal��ch ��dek, dol� o v��ku jednoho znaku a p�esunovat se op�t zleva doprava. loop se tedy rovn� 17, cx=17/16=1,0625. Desetinn� ��st (0,0625) je vlastn� rovna jedn� �estn�ctin�. Z toho plyne, �e jsme se p�esunuli o jeden znak doprava. cy je st�le jedna (viz. d�le). 18/16 ud�v� posun o 2 znaky doprava a jeden znak dol�. Analogicky se dostaneme k loop=32. cx bude rovno 0 (32/16=2 zbytek 0). cy=2, t�m se na textu�e posuneme o dva znaky dol�. D�v� to smysl? (Pozn. p�ekladatele: J� bych asi pou�il vno�en� cyklus - vn�j��m j�t po sloupc�ch a vnit�n�m po ��dc�ch. Bylo by to mo�n� pochopiteln�j�� (...a hlavn� snadn�j�� na p�eklad :-))</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_17_font.gif" width="256" height="256" alt="Textura fontu" /></div>

<p class="src2">cx=float(loop%16)/16.0f;<span class="kom">// X pozice aktu�ln�ho znaku</span></p>
<p class="src2">cy=float(loop/16)/16.0f;<span class="kom">// Y pozice aktu�ln�ho znaku</span></p>

<p>Te�, po tro�e matematick�ho vysv�tlov�n�, za�neme vytv��et 2D font. Pomoc� cx a cy vyjmeme ka�d� znak z textury fontu. P�i�teme loop k hodnot� base - aby se znaky nep�episovaly ukl�d�n�m v�dy do prvn�ho. Ka�d� znak se ulo�� do vlastn�ho display listu.</p>

<p class="src2">glNewList(base+loop,GL_COMPILE);<span class="kom">// Vytvo�en� display listu</span></p>

<p>Po zvolen� display listu do n�j nakresl�me obd�ln�k otexturovan� znakem.</p>

<p class="src3">glBegin(GL_QUADS);<span class="kom">// Pro ka�d� znak jeden obd�ln�k</span></p>

<p>Cx a cy jsou schopny ulo�it velmi malou desetinnou hodnotu. Pokud cx a z�rove� cy budou 0, tak bude p��kaz vypadat takto: glTexCoord2f(0.0f,1-0.0f-0.0625f); Pamatujte si, �e 0,0625 je p�esn� 1/16 na�� textury nebo ���ka/v��ka jednoho znaku. Koordin�ty mohou ukazovat na lev� doln� roh na�� textury. V�imn�te si, �e pou��v�me glVertex2i(x,y) nam�sto glVertex3f(x,y,z). Nebudeme pot�ebovat hodnotu z, proto�e pracujeme s 2D fontem. Proto�e pou��v�me kolnou projekci (ortho), nemus�me se p�esunout do hloubky - sta�� tedy pouze x, y. Okno m� velikost 0-639 a 0-479 (640x480) pixel�, tud� nemus�me pou��vat desetinn� nebo dokonce z�porn� hodnoty. Cesta jak nastavit ortho obraz je ur�it 0, 0 jako lev� doln� roh a 640, 480 jako prav� horn� roh. Zjednodu�en� �e�eno: zbavili jsme se z�porn�ch koordin�t�. U�ite�n� v�c, pro lidi, kte�� se necht�j� starat o perspektivu, a kte�� v�ce preferuj� pr�ci s pixely ne� s jednotkami :)</p>

<p class="src4">glTexCoord2f(cx,1-cy-0.0625f); glVertex2i(0,0);<span class="kom">// Lev� doln�</span></p>

<p>Druh� koordin�t je te� posunut o 1/16 doprava (���ka znaku) - p�i�teme k x-ov� hodnot� 0,0625f.</p>

<p class="src4">glTexCoord2f(cx+0.0625f,1-cy-0.0625f); glVertex2i(16,0);<span class="kom">// Prav� doln�</span></p>

<p>T�et� koordin�t z�st�v� vpravo, ale p�esunul se nahoru (o v��ku znaku).</p>

<p class="src4">glTexCoord2f(cx+0.0625f,1-cy); glVertex2i(16,16);<span class="kom">// Prav� horn�</span></p>

<p>Ur��me lev� horn� roh znaku.</p>

<p class="src4">glTexCoord2f(cx,1-cy); glVertex2i(0,16);<span class="kom">// Lev� horn�</span></p>
<p class="src"></p>
<p class="src3">glEnd();<span class="kom">// Konec znaku</span></p>


<p>P�esuneme se o 10 pixel� doprava, t�m se um�st�me doprava od pr�v� nakreslen� textury. Pokud bychom se nep�esunuli, v�echny znaky by se nakupily na jedno m�sto. Proto�e je font tro�ku "huben�j��" (u���), nep�esuneme se o cel�ch 16 pixel� (���ku znaku), ale pouze o 10. Mezi jednotliv�mi p�smeny by byly velk� mezery.</p>

<p class="src3">glTranslated(10,0,0);<span class="kom">// P�esun na pravou stranu znaku</span></p>
<p class="src"></p>
<p class="src2">glEndList();<span class="kom">// Konec kreslen� display listu</span></p>
<p class="src"></p>
<p class="src1">}<span class="kom">// Cyklus pokra�uje dokud se nevytvo�� v�ech 256 znak�</span></p>
<p class="src0">}</p>

<p>Op�t p�id�me k�d pro uvoln�n� v�ech 256 display list� znaku. Provede se p�i ukon�ov�n� programu.</p>

<p class="src0">GLvoid KillFont(GLvoid)<span class="kom">// Uvoln� pam� fontu</span></p>
<p class="src0">{</p>
<p class="src1">glDeleteLists(base,256);<span class="kom">// Sma�e 256 display list�</span></p>
<p class="src0">}</p>

<p>V n�sleduj�c� funkci se prov�d� v�stup textu. V�echno je pro v�s nov�, tud� vysv�tl�m ka�dou ��dku hodn� podrobn�. Do tohoto k�du by mohla b�t p�id�na spousta dal��ch funkc�, jako je podpora prom�nn�ch, zv�t�ov�n� znak�, rozestupy ap. Funkci glPrint() p�ed�v�me t�i parametry. Prvn� a druh� je pozice textu v okn� (u Y je nula dole!), t�et� je ��dan� �et�zec a posledn� je znakov� sada. Pod�vejte se na bitmapu fontu. Jsou tam dv� rozd�len� znakov� sady (v tomto p��pad� je prvn� oby�ejn� - 0, druh� kurz�vou - cokoli jin�ho).</p>

<p class="src0">GLvoid glPrint(GLint x, GLint y, char *string, int set)<span class="kom">// Prov�d� v�pis textu</span></p>
<p class="src0">{</p>

<p>Nap�ed se ujist�me, zda je set bu� 1 nebo 0. Pokud je v�t�� ne� 1, p�i�ad�me j� 0. (Pozn. p�ekladatele: Autor asi zapomn�l na �astou obranu u�ivatel� p�i zhroucen� programu: "Ne ur�it� jsem tam nezadal z�porn� ��slo!" :-)</p>

<p class="src1">if (set&gt;1)</p>
<p class="src1">{</p>
<p class="src2">set=1;</p>
<p class="src1">}</p>

<p>Proto�e je mo�n�, �e m�me p�ed spu�t�n�m funkce vybranou (na tomto m�st�) "randomovou" texturu, zvol�me tu "fontovou".</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// V�b�r textury</span></p>

<p>Vypneme hloubkov� textov�n� - blending vypad� l�pe (text by mohl skon�it za n�jak�m objektem, nemus� vypadat spr�vn�...). Okol� textu v�m nemus� vadit, kdy� pou��v�te �ern� pozad�.</p>

<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne hloubkov� testov�n�</span></p>

<p>Hodn� d�le�it� v�c! Zvol�me projek�n� matici (Projection Matrix) a p��kazem glPushMatrix() ji ulo��me (n�co jako pam� na kalkula�ce). Do p�vodn�ho stavu ji m��eme obnovit vol�n�m glPopMatrix() (viz. d�le).</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Vybere projek�n� matici</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�� projek�n� matici</span></p>

<p>Pot�, co byla projek�n� matice ulo�ena, resetujeme matici a nastav�me ji pro kolmou projekci (Ortho screen). Parametry maj� v�znam o�ez�vac�ch rovin (v po�ad�): lev�, prav�, doln�, horn�, nejbli���, nejvzd�len�j��. Levou stranu bychom mohli ur�it na -640, ale pro� pracovat se z�porn�mi ��sly? Je moudr� nastavit tyto hodnoty, abyste si zvolili meze (rozli�en�), ve kter�ch pr�v� pracujete.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glOrtho(0,640,0,480,-1,1);<span class="kom">// Nastaven� kolm� projekce</span></p>

<p>Te� ur��me matici modelview a op�t vol�n�m glPushMatrix() ulo��me st�vaj�c� nastaven�. Pot� resetujeme matici modelview, tak�e budeme moci pracovat s kolmou projekc�.</p>

<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// V�b�r matice</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>S ulo�en�mi nastaven�mi pro perspektivu a kolmou projekci, m��eme za��t vykreslovat text. Za�neme translac� na m�sto, kam ho chceme vykreslit. M�sto glTranslatef() pou�ijeme glTranslated(), proto�e nen� d�le�it� desetinn� hodnota. Nelze ur�it p�lku pixelu :-) (Pozn. p�ekladatele: Tady bude asi jeden tot�ln� velk� error, jeliko� glTranslated() pracuje v p�esnosti double, tedy je�t� ve v�t�� - nicm�n� stane se. (Alespo�, �e v�me o co jde :-). Jo, ten smajl�k u p�lky pixelu byl i v p�vodn� verzi.)</p>

<p class="src1">glTranslated(x,y,0);<span class="kom">// Pozice textu (0,0 - lev� doln�)</span></p>

<p>��dek n�e ur�� znakovou sadu. P�i pou�it� druh� p�i�teme 128 k display listu base (128 je polovina z 256 znak�). P�i�ten�m 128 "p�esko��me" prvn�ch 128 znak�.</p>

<p class="src1">glListBase(base-32+(128*set));<span class="kom">// Zvol� znakovou sadu (0 nebo 1)</span></p>

<p>Zb�v� vykreslen�. Jako poka�d� v minul�ch lekc�ch to provedeme i zde vol�n�m glCallLists(). strlen(string) je d�lka �et�zce (ve znac�ch), GL_BYTE znamen�, �e ka�d� znak je reprezentov�n bytem (hodnoty 0 a� 255). Nakonec, ve string p�ed�v�me konkr�tn� text pro vykreslen�.</p>

<p class="src1">glCallLists(strlen(string),GL_BYTE,string);<span class="kom">// Vykreslen� textu na obrazovku</span></p>

<p>Obnov�me perspektivn� pohled. Zvol�me projek�n� matici a pou�ijeme glPopMatrix() k odvol�n� se na d��ve ulo�en� (glPushMatrix()) nastaven�.</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// V�b�r projek�n� matice</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� ulo�en� projek�n� matice</span></p>

<p>Zvol�me matice modelview a ud�l�me to sam� jako p�ed chv�l�.</p>

<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// V�b�r matice modelview</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� ulo�en� modelview matice</span></p>

<p>Povol�me hloubkov� testov�n�. Pokud jste ho na za��tku nevyp�nali, tak tuto ��dku nepot�ebujete.</p>

<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkov� testov�n�</span></p>
<p class="src0">}</p>

<p>Vytvo��me textury a display listy. Pokud se n�co nepovede vr�t�me false. T�m program zjist�, �e vznikl error a ukon�� se.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echno nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje textury</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">BuildFont();<span class="kom">// Vytvo�� font</span></p>

<p>N�sleduj� obvykl� nastaven� OpenGL.</p>

<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE);<span class="kom">// Vybere typ blendingu</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povol� jemn� st�nov�n�</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� 2D textur</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Za�neme kreslit sc�nu - na za��tku stvo��me 3D objekt a a� potom text. D�vod pro� jsem se rozhodl p�idat 3D objekt je prost�: chci demonstrovat sou�asn� pou�it� perspektivn� i kolm� projekce v jednom programu.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Zvol�me texturu vytvo�enou z bumps.bmp, p�esuneme se o p�t jednotek dovnit� a provedeme rotaci o 45� na ose Z. Toto pooto�en� po sm�ru hodinov�ch ru�i�ek vyvol� dojem diamantu a ne dvou �tverc�.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[1]);<span class="kom">// V�b�r textury</span></p>
<p class="src1">glTranslatef(0.0f,0.0f,-5.0f);<span class="kom">// P�esun o p�t do obrazovky</span></p>
<p class="src1">glRotatef(45.0f, 0.0f,0.0f,1.0f);<span class="kom">// Rotace o 45� po sm�ru hodinov�ch ru�i�ek na ose z</span></p>

<p>Provedeme dal�� rotaci na os�ch X a Y, kter� je z�visl� na prom�nn� cnt1*30. M� za n�sledek ot��en� objektu dokola, stejn� jako se ot��� diamant na jednom m�st�.</p>

<p class="src1">glRotatef(cnt1*30.0f,1.0f,1.0f,0.0f);<span class="kom">// Rotace na os�ch x a y</span></p>

<p>Proto�e chceme aby se jevil jako pevn�, vypneme blending a nastav�me b�lou barvu. Vykresl�me texturou namapovan� �ty��heln�k.</p>

<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vypnut� blendingu</span></p>
<p class="src1">glColor3f(1.0f,1.0f,1.0f);<span class="kom">// B�l� barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�ku</span></p>
<p class="src2">glTexCoord2d(0.0f,0.0f);</p>
<p class="src2">glVertex2f(-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2d(1.0f,0.0f);</p>
<p class="src2">glVertex2f( 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2d(1.0f,1.0f);</p>
<p class="src2">glVertex2f( 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2d(0.0f,1.0f);</p>
<p class="src2">glVertex2f(-1.0f,-1.0f);</p>
<p class="src1">glEnd();<span class="kom">// Konec obd�ln�ku</span></p>

<p>D�le provedeme rotaci o 90� na os�ch X a Y. Op�t vykresl�me �ty��heln�k. Tento nov� uprost�ed prot�n� prvn� kreslen� a je na n�j kolm� (90�). Hezk� soum�rn� tvar.</p>

<p class="src1">glRotatef(90.0f,1.0f,1.0f,0.0f);<span class="kom">// Rotace na os�ch X a Y o 90�</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�ku</span></p>
<p class="src2">glTexCoord2d(0.0f,0.0f);</p>
<p class="src2">glVertex2f(-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2d(1.0f,0.0f);</p>
<p class="src2">glVertex2f( 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2d(1.0f,1.0f);</p>
<p class="src2">glVertex2f( 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2d(0.0f,1.0f);</p>
<p class="src2">glVertex2f(-1.0f,-1.0f);</p>
<p class="src1">glEnd();<span class="kom">// Konec obd�ln�ku</span></p>

<p>Zapneme blending a za�neme vypisovat text. Pou�ijeme stejn� pulzov�n� barev jako v n�kter�ch minul�ch lekc�ch.</p>

<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapnut� blendingu</span></p>
<p class="src1"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zm�na barvy zalo�en� na pozici textu</span></p>
<p class="src1">glColor3f(1.0f*float(cos(cnt1)),1.0f*float(sin(cnt2)),1.0f-0.5f*float(cos(cnt1+cnt2)));</p>

<p>Pro vykreslen� st�le vyu��v�me funkci glPrint(). Prvn�mi parametry jsou x-ov� a Y-ov� sou�adnice, t�et� atribut, "NeHe", bude v�stupem a posledn� ur�uje znakovou sadu (0-norm�ln�, 1-kurz�va). Asi jste si domysleli, �e textem pohybujeme pomoc� sin� a kosin�. Pokud jste tak trochu "v pasti", vra�te se do minul�ch lekc�, ale nen� podm�nkou tomu a� tak rozum�t.</p>

<p class="src1">glPrint(int((280+250*cos(cnt1))),int(235+200*sin(cnt2)),&quot;NeHe&quot;,0);<span class="kom">// Vyp�e text</span></p>
<p class="src"></p>
<p class="src1">glColor3f(1.0f*float(sin(cnt2)),1.0f-0.5f*float(cos(cnt1+cnt2)),1.0f*float(cos(cnt1)));</p>
<p class="src"></p>
<p class="src1">glPrint(int((280+230*cos(cnt2))),int(235+200*sin(cnt1)),&quot;OpenGL&quot;,1);<span class="kom">// Vyp�e text</span></p>

<p>Nastav�me barvu na modrou a na spodn� ��st okna nap�eme jm�no autora t�to lekce. Cel� to zopakujeme s b�lou barvou a posunut�m o dva pixely doprava - jednoduch� st�n (nen�-li zapnut� blending nebude to fungovat).</p>

<p class="src1">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modr� barva</span></p>
<p class="src1">glPrint(int(240+200*cos((cnt2+cnt1)/5)),2,&quot;Giuseppe D'Agata&quot;,0);<span class="kom">// Vyp�e text</span></p>
<p class="src"></p>
<p class="src1">glColor3f(1.0f,1.0f,1.0f);<span class="kom">// B�l� barva</span></p>
<p class="src1">glPrint(int(242+200*cos((cnt2+cnt1)/5)),2,&quot;Giuseppe D'Agata&quot;,0);<span class="kom">// Vyp�e text</span></p>

<p>Inkrementujeme ��ta�e - text se bude pohybovat a objekt rotovat.</p>


<p class="src1">cnt1+=0.01f;</p>
<p class="src1">cnt2+=0.0081f;</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Mysl�m, �e te� mohu ofici�ln� prohl�sit, �e moje tutori�ly nyn� vysv�tluj� v�echny mo�n� cesty k vykreslen� textu. K�d z t�to lekce m��e b�t pou�it na jak�koli platform�, na kter� funguje OpenGL, je snadn� k pou��v�n�. Vykreslov�n� t�mto zp�sobem &quot;u��r�&quot; velmi m�lo procesorov�ho �asu. R�d bych pod�koval Guiseppu D'Agatovi za origin�ln� verzi t�to lekce. Hodn� jsem ji upravil a konvertoval na nov� z�kladn� k�d, ale bez n�j bych to asi nesvedl. Jeho verze m� trochu v�ce mo�nost�, jako vzd�lenost znak� apod., ale j� jsem zase stvo�il &quot;extr�mn� skv�l� 3D objekt&quot;. </p>

<p class="autor">napsal: Giuseppe D'Agata <?VypisEmail('waveform@tiscalinet.it');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson17.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson17_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson17.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson17.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson17.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson17.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson17.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson17.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson17.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson17.tar.gz">Irix / GLUT</a> k�d t�to lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson17.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson17.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson17.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson17.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson17.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson17.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson17.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:greg@ozducati.com">Greg Helps</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson17.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson17.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson17.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(17);?>
<?FceNeHeOkolniLekce(17);?>

<?
include 'p_end.php';
?>
