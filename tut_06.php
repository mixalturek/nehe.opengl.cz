<?
$g_title = 'CZ NeHe OpenGL - Lekce 6 - Textury';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(6);?>

<h1>Lekce 6 - Textury</h1>

<p class="nadpis_clanku">Namapujeme bitmapov� obr�zek na krychli. Pou�ijeme zdrojov� k�dy z prvn� lekce, proto�e je jednodu�� (a p�ehledn�j��) za��t s pr�zdn�m oknem ne� slo�it� upravovat p�edchoz� lekci.</p>

<p>Porozum�n� textur�m m� mnoho v�hod. �ekn�me, �e chcete nechat p�elet�t p�es obrazovku st�elu. A� do tohoto tutori�lu byste ji pravd�podobn� vytvo�ili z vybarven�ch n-�heln�k�. S pou�it�m textur m��ete vz�t obr�zek skute�n� st�ely a nechat jej let�t p�es obrazovku. Co mysl�te, �e bude vypadat l�pe? Fotografie, nebo obr�zek poskl�dan� z troj�heln�k� a �tverc�? S pou�it�m textur to bude nejen vypadat l�pe, ale i v� program bude rychlej��. St�ela vytvo�en� pomoc� textury bude jen jeden �tverec pohybuj�c� se po obrazovce. St�ela tvo�en� n-�heln�ky by mohla b�t tvo�ena stovkami, nebo tis�ci n-�heln�ky. Jeden �tverec pokryt� texturou bude m�t mnohem men�� n�roky.</p>

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

<p>P�id�me t�i nov� desetinn� prom�nn�... xrot, yrot a zrot. Tyto prom�nn� budou pou�ity k rotaci krychle okolo os. Posledn� ��dek GLuint texture[1] deklaruje prostor pro jednu texturu. Pokud chcete nahr�t v�ce ne� jednu texturu, zm��te ��slo jedna na ��slo odpov�daj�c� po�et textur, kter� chcete nahr�t.</p>

<p class="src0">GLfloat xrot;<span class="kom">// X Rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y Rotace</span></p>
<p class="src0">GLfloat zrot;<span class="kom">// Z Rotace</span></p>
<p class="src"></p>
<p class="src0">GLuint texture[1];<span class="kom">// Ukl�d� texturu</span></p>

<p>Bezprost�edn� za p�edch�zej�c� k�d a p�ed funkci ReSizeGLScene() p�id�me n�sleduj�c� funkci. Jej�m ��elem je nahr�v�n� souboru s bitmapou. Pokud soubor neexistuje, vr�t� NULL, co� vyjad�uje, �e textura nem��e b�t nahr�na. P�ed vysv�tlov�n�m k�du je t�eba v�d�t n�kolik <b>VELMI</b> d�le�it�ch v�c� o obr�zc�ch pou�it�ch pro textury. V��ka a ���ka obr�zku mus� b�t mocnina dvou, ale nejm�n� 64 pixel�. Z d�vod� kompatibility by nem�ly b�t v�t�� ne� 256 pixel�. Pokud by bitmapa, kterou chcete pou��t nem�la velikost 64, 128 nebo 256, zm��te jej� velikost pomoc� editoru obr�zk�. Existuj� zp�soby jak obej�t tyto limity, ale my z�staneme u standardn�ch velikost� textury. Prvn� v�c kterou ud�l�me je deklarace ukazatele na soubor. Na za��tku jej nastav�me na NULL.</p>

<p class="src0">AUX_RGBImageRec *LoadBMP(char *Filename)<span class="kom">// Nahraje bitmapu</span></p>
<p class="src0">{</p>
<p class="src1">FILE *File=NULL;<span class="kom">// Ukazatel na soubor</span></p>

<p>D�le se ujist�me, �e bylo p�ed�no jm�no souboru. Je mo�n� zavolat funkci LoadBMP() bez zad�n� jm�na souboru, tak�e to mus�me zkontrolovat. Nechceme se sna�it nahr�t nic. D�le se pokus�me otev��t tento soubor pro �ten�, abychom zkontrolovali, zda soubor existuje.</p>

<p class="src1">if (!Filename)<span class="kom">// Byla p�ed�na cesta k souboru?</span></p>
<p class="src1">{</p>
<p class="src2">return NULL;<span class="kom">// Pokud ne, konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">File=fopen(Filename,&quot;r&quot;);<span class="kom">// Otev�en� pro �ten�</span></p>

<p>Pokud se n�m poda�ilo soubor otev��t, zjevn� existuje. Zav�eme soubor a  pomoc� funkce auxDIBImageLoad(Filename) vr�t�me data obr�zku.</p>

<p class="src1">if (File)<span class="kom">// Existuje soubor?</span></p>
<p class="src1">{</p>
<p class="src2">fclose(File);<span class="kom">// Zav�e ho</span></p>
<p class="src2">return auxDIBImageLoad(Filename);<span class="kom">// Na�te bitmapu a vr�t� na ni ukazatel</span></p>
<p class="src1">}</p>

<p>Pokud se n�m soubor nepoda�ilo otev��t soubor vr�t�me NULL, co� indikuje, �e soubor nemohl b�t nahr�n. Pozd�ji v programu budeme kontrolovat, zda se v�e povedlo v po��dku.</p>

<p class="src1">return NULL;<span class="kom">// P�i chyb� vr�t�me NULL</span></p>
<p class="src0">}</p>

<p>Nahraje bitmapu (vol�n�m p�edchoz�ho k�du) a konvertujeme jej na texturu.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Loading bitmapy a konverze na texturu</span></p>
<p class="src0">{</p>

<p>Deklarujeme bool prom�nnou zvanou Status. Pou�ijeme ji k sledov�n�, zda se n�m poda�ilo nebo nepoda�ilo nahr�t bitmapu a sestavit texturu. Jej� po��te�n� hodnotu nastav�me na FALSE.</p>

<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>

<p>Vytvo��me z�znam obr�zku, do kter�ho m��eme bitmapu ulo�it. Z�znam bude ukl�dat v��ku, ���ku a data bitmapy.</p>

<p class="src1">AUX_RGBImageRec *TextureImage[1];<span class="kom">// Ukl�d� bitmapu</span></p>

<p>Abychom si byli jisti, �e je obr�zek pr�zdn�, vynulujeme p�id�lenou pam�.</p>

<p class="src1">memset(TextureImage,0,sizeof(void *)*1);<span class="kom">// Vynuluje pam�</span></p>

<p>Nahrajeme bitmapu a konvertujeme ji na texturu. TextureImage[0]=LoadBMP("Data/NeHe.bmp") zavol� d��ve napsanou funkci LoadBMP(). Pokud se v�e poda��, data bitmapy se ulo�� do TextureImage[0], Status je nastaven na TRUE a za�neme sestavovat texturu.</p>

<p class="src1">if (TextureImage[0]=LoadBMP(&quot;Data/NeHe.bmp&quot;))<span class="kom">// Nahraje bitmapu a kontroluje vznikl� chyby</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;<span class="kom">// V�e je bez probl�m�</span></p>

<p>Te�, kdy� m�me nahr�na data obr�zku do TextureImage[0], sestav�me texturu s pou�it�m t�chto dat. Prvn� ��dek glGenTextures(1, &amp;texture[0]) �ekne OpenGL, �e chceme sestavit jednu texturu a chceme ji ulo�it na index 0 pole. Vzpome�te si, �e jsme na za��tku vytvo�ili m�sto pro jednu texturu pomoc� GLuint texture[1]. Druh� ��dek glBindTexture(GL_TEXTURE_2D, texture[0]) �ekne OpenGL, �e texture[0] (prvn� textura), bude 2D textura. 2D textury maj� v��ku (na ose Y) a ���ku (na ose X). Hlavn� funkc� glBindTexture() je uk�zat OpenGL dostupnou pam�. V tomto p��pad� ��k�me OpenGL, �e voln� pam� je na &amp;texture[0]. Kdy� vytvo��me texturu, bude ulo�ena na tomto pam�ov�m m�st�. V podstat� glBindTexture() uk�e do pam�ti RAM, kde je ulo�ena na�e textura.</p>

<p class="src2">glGenTextures(1, &amp;texture[0]);<span class="kom">// Generuje texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Typick� vytv��en� textury z bitmapy</span></p>

<p>Vytvo��me 2D texturu (GL_TEXTURE_2D), nula reprezentuje hladinu podrobnost� obr�zku (obvykle se zad�v� nula). T�i je po�et datov�ch komponent. Proto�e je obr�zek tvo�en �ervenou, zelenou a modrou slo�kou dat, jsou to t�i komponenty. TextureImage[0]->sizeX je ���ka textury. Pokud zn�te ���ku, m��ete ji tam p��mo napsat, ale je jednodu��� a univerz�ln�j�� nechat pr�ci na po��ta�i. TextureImage[0]->sizeY je analogicky v��ka textury. Nula je r�me�ek (obvykle nech�n nulov�). GL_RGB ��k� OpenGL, �e obrazov� data jsou tvo�ena �ervenou, zelenou a modrou v tomto po�ad�. GL_UNSIGNED_BYTE znamen�, �e data (jednotliv� hodnoty R, G a B) jsou tvo�eny z bezznam�nkov�ch byt� a kone�n� TextureImage[0]->data ��k� OpenGL, kde vz�t data textury. V tomto p��pad� jsou to data ulo�en� v z�znamu TextureImage[0].</p>

<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);<span class="kom">// Vlastn� vytv��en� textury</span></p>

<p>Dal�� dva ��dky oznamuj� OpenGL, jak� pou��t typy filtrov�n�, kdy� je obr�zek v�t�� (GL_TEXTURE_MAG_FILTER) nebo men�� (GL_TEXTURE_MIN_FILTER) ne� origin�ln� bitmapa. J� obvykle pou��v�m GL_LINEAR pro oba p��pady. To zp�sobuje, �e textura vypad� hladce ve v�ech p��padech. Pou�it� GL_LINEAR po�aduje spoustu pr�ce procesoru a video karty, tak�e kdy� je v� syst�m pomal�, m�li by jste pou��t GL_NEAREST. Textura filtrovan� pomoc� GL_NEAREST bude p�i zv�t�en� vypadat kosti�kovan�. Lze tak� kombinovat oboj�. GL_LINEAR pro p��pad zv�t�en� a GL_NEAREST na zmen�en�.</p>

<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);<span class="kom">// Filtrov�n� p�i zmen�en�</span></p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);<span class="kom">// Filtrov�n� p�i zv�t�en�</span></p>
<p class="src1">}</p>

<p>Uvoln�me pam� RAM, kterou jsme pot�ebovali pro ulo�en� dat bitmapy. Ujist�me se, �e data bitmapy byla ulo�ena v TextureImage[0]. Pokud ano, ujist�me se, �e data byla ulo�ena v polo�ce data, pokud ano sma�eme je. Potom uvoln�me strukturu obr�zku.</p>

<p class="src1">if (TextureImage[0])<span class="kom">// Pokud obr�zek existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (TextureImage[0]-&gt;data)<span class="kom">// Pokud existuj� data obr�zku</span></p>
<p class="src2">{</p>
<p class="src3">free(TextureImage[0]-&gt;data);<span class="kom">// Uvoln� pam� obr�zku</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">free(TextureImage[0]);<span class="kom">// Uvoln� strukturu obr�zku</span></p>
<p class="src1">}</p>

<p>Nakonec vr�t�me status. Pokud je v�echno v po��dku, obsahuje TRUE. FALSE indikuje chybu.</p>

<p class="src1">return Status;<span class="kom">// Ozn�m� p��padn� chyby</span></p>
<p class="src0">}</p>

<p>P�id�me p�r ��dk� k�du do InitGL. Vyp�i celou funkci znovu, tak�e bude jednoduch� naj�t zm�ny. Prvn� ��dek if (!LoadGLTextures()) sko�� do k�du, kter� jsme napsali v p�edchoz� ��sti. Nahraje bitmapu a vygeneruje z n� texturu. Pokud z jak�hokoli d�vodu sel�e, tak ukon��me funkci s n�vratovou hodnotou FALSE. Pokus se texturu poda�ilo nahr�t, povol�me mapov�n� 2D textur - glEnable(GL_TEXTURE_2D). Pokud jej zapomeneme povolit, budou se objekty obvykle zobrazovat jako b�l�, co� n�m asi nebude vyhovovat.</p>

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
<p class="src1">return TRUE;<span class="kom">// Inicializace prob�hla v po��dku</span></p>
<p class="src0">}</p>

<p>P�ejdeme k vykreslov�n�. Pokus�me se o otexturovanou krychli.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-5.0f);<span class="kom">// Posun do obrazovky</span></p>
<p class="src"></p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);<span class="kom">// Nato�en� okolo osy x</span></p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);<span class="kom">// Nato�en� okolo osy y</span></p>
<p class="src1">glRotatef(zrot,0.0f,0.0f,1.0f);<span class="kom">// Nato�en� okolo osy z</span></p>

<p>N�sleduj�c� ��dek vybere texturu, kterou chceme pou��t. Pokud m�te v�ce ne� jednu texturu, vyberete ji �pln� stejn�, ale s jin�m indexem pole glBindTexture(GL_TEXTURE_2D, texture[��slo textury kterou chcete pou��t]). Tuto funkci nesm�te volat mezi glBegin() a glEnd(). Mus�te ji volat v�dy p�ed nebo za blokem ohrani�en�m t�mito funkcemi.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Zvol� texturu</span></p>

<p>Ke spr�vn�mu namapov�n� textury na �ty��heln�k se mus�te ujistit, �e lev� horn� roh textury je p�ipojen k lev�mu horn�mu rohu �ty��heln�ku ap. Pokud rohy textury nejsou p�ipojeny k odpov�daj�c�m roh�m �ty��heln�ku, zobraz� se textura nato�en�, p�evr�cen� nebo se v�bec nezobraz�. Prvn� parametr funkce glTexCoord2f je sou�adnice x textury. 0.0 je lev� strana textury, 0.5 st�ed, 1.0 prav� strana. Druh� parametr je sou�adnice y. 0.0 je spodek textury, 0.5 st�ed, 1.0 vr�ek. Tak�e te� v�me, �e 0.0 na X a 1.0 na Y je lev� horn� vrchol �ty��heln�ka atd. V�e, co mus�me ud�lat je p�i�adit ka�d�mu rohu �ty��heln�ka odpov�daj�c� roh textury. Zkuste experimentovat s hodnotami x a y funkce glTexCoord2f. Zm�nou 1.0 na 0.5 vykresl�te pouze polovinu textury od 0.0 do 0.5 atd.</p>

<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// P�edn� st�na</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Zadn� st�na</span></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Vrchn� st�na</span></p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Spodn� st�na</span></p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Prav� st�na</span></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Lev� st�na</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src1">glEnd();</p>

<p>Nakonec zv�t��me hodnoty prom�nn�ch xrot, yrot a zrot, kter� ur�uj� nato�en� krychle. Zm�nou hodnot m��eme zm�nit rychlost i sm�r nat��en�.</p>

<p class="src1">xrot+=0.3f;</p>
<p class="src1">yrot+=0.2f;</p>
<p class="src1">zrot+=0.4f;</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Po do�ten� t�to lekce byste m�li rozum�t texturov�mu mapov�n�. M�li by jste b�t schopni namapovat libovolnou texturu na libovoln� objekt. A� si budete jist�, �e tomu rozum�te, zkuste namapovat na ka�dou st�nu krychle jinou texturu</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson06.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson06_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson06.zip">C#</a> k�d t�to lekce. ( <a href="mailto:sugarbee@gmx.net">Sabine Felsinger</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson06.zip">VB.Net CsGL</a> k�d t�to lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson06.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson06.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson06.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:brad@choate.net">Brad Choate</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson06.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson06.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson06.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson06.zip">GLUT</a> k�d t�to lekce. ( <a href="mailto:kgancarz@hotmail.com">Kyle Gancarz</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson06.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson06.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson06.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson06.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson06.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson06.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson06.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson06.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson06.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson06.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson06.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson06.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson06.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson06.tar.gz">Python</a> k�d t�to lekce. ( <a href="mailto:hakuin@voicenet.com">John Ferguson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson06.rb.hqx">REALbasic</a> k�d t�to lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson06.zip">Solaris</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson06.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson06-2.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson06.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson06.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(6);?>
<?FceNeHeOkolniLekce(6);?>

<?
include 'p_end.php';
?>
