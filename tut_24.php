<?
$g_title = 'CZ NeHe OpenGL - Lekce 24 - V�pis OpenGL roz���en�, o�ez�vac� testy a textury z TGA obr�zk�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(24);?>

<h1>Lekce 24 - V�pis OpenGL roz���en�, o�ez�vac� testy a textury z TGA obr�zk�</h1>

<p class="nadpis_clanku">V t�to lekci se nau��te, jak zjistit, kter� OpenGL roz���en� (extensions) podporuje va�e grafick� karta. Vyp�eme je do st�edu okna, se kter�m budeme moci po stisku �ipek rolovat. Pou�ijeme klasick� 2D texturov� font s t�m rozd�lem, �e texturu vytvo��me z TGA obr�zku. Jeho nejv�t��mi p�ednostmi jsou jednoduch� pr�ce a podpora alfa kan�lu. Odbour�n�m bitmap u� nebudeme muset inkludovat knihovnu glaux.</p>

<p>Tento tutori�l je daleko od prezentace grafick� n�dhery, ale nau��te se n�kolik nov�ch v�c�. P�r lid� se m� ptalo na OpenGL roz���en� a na to, jak zjistit, kter� jsou podporov�ny konkr�tn�m typem grafick� karty. Mohu sm�le ��ci, �e s t�mto po do�ten� nebudete m�t nejmen�� probl�my. Tak� se dozv�te, jak rolovat ��st� sc�ny bez toho, aby se ovlivnilo jej� okol�. Pou�ijeme o�ez�vac� testy (scissor testing). D�le si uk�eme, jak vykreslovat linky pomoc� line strips a co je d�le�it�j��, kompletn� odbour�me knihovnu glaux, kterou jsme pou��vali kv�li textur�m z bitmapov�ch obr�zk�. Budeme pou��vat Targa (TGA) obr�zky, se kter�mi se snadno pracuje a kter� podporuj� alfa kan�l.</p>

<p>Za�neme programovat. Prvn� v�c�, kter� si v�imneme u vkl�d�n� hlavi�kov�ch soubor� je, �e neinkludujeme knihovnu glaux (glaux.h). Tak� nep�ilikujeme soubor glaux.lib. U� nebudeme pracovat s bitmapami, tak�e tyto soubory v projektu nepot�ebujeme.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src"></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src0">#include &lt;stdarg.h&gt;<span class="kom">// Hlavi�kov� soubor pro funkce s prom�nn�m po�tem parametr�</span></p>
<p class="src0">#include &lt;string.h&gt;<span class="kom">// Hlavi�kov� soubor pro pr�ci s �et�zci</span></p>
<p class="src"></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Priv�tn� GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trval� Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na�eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>P�id�me prom�nn�. Scroll bude pou�ito pro rolov�n� ��st� sc�ny nahoru a dol�. Druh� prom�nn�, maxtokens, bude ukl�dat z�znam kolik roz���en� je podporov�no grafickou kartou. Base u� tradi�n� ukazuje na display listy fontu. Do swidth a sheight nagrabujeme aktu�ln� velikost okna, pomohou n�m vypo��tat koordin�ty pro o�ez�n� oblasti okna, kter� umo�n� rolov�n�.</p>

<p class="src0">int scroll;<span class="kom">// Pro rolov�n� okna</span></p>
<p class="src0">int maxtokens;<span class="kom">// Po�et podporovan�ch roz���en�</span></p>
<p class="src"></p>
<p class="src0">GLuint base;<span class="kom">// Z�kladn� display list fontu</span></p>
<p class="src"></p>
<p class="src0">int swidth;<span class="kom">// ���ka o�ezan� oblasti</span></p>
<p class="src0">int sheight;<span class="kom">// V��ka o�ezan� oblasti</span></p>

<p>Nap�eme strukturu, kter� bude ukl�dat informace o nahr�van�m TGA obr�zku. Pointer imageData bude ukazovat na data, ze kter�ch vytvo��me obr�zek. Bpp ozna�uje barevnou hloubku (bits per pixel), kter� m��e b�t 24 nebo 32, podle p��tomnosti alfa kan�lu. Width a height definuje rozm�ry. Do texID vytvo��me texturu. Celou strukturu nazveme TextureImage.</p>

<p class="src0">typedef struct<span class="kom">// Struktura textury</span></p>
<p class="src0">{</p>
<p class="src1">GLubyte *imageData;<span class="kom">// Data obr�zku</span></p>
<p class="src1">GLuint bpp;<span class="kom">// Barevn� hloubka obr�zku</span></p>
<p class="src1">GLuint width;<span class="kom">// ���ka obr�zku</span></p>
<p class="src1">GLuint height;<span class="kom">// V��ka obr�zku</span></p>
<p class="src"></p>
<p class="src1">GLuint texID;<span class="kom">// Vytvo�en� textura</span></p>
<p class="src0">} TextureImage;<span class="kom">// Jm�no struktury</span></p>

<p>V tomto programu budeme pou��vat pouze jednu texturu, tak�e vytvo��me pole textur o velikosti jedna.</p>

<p class="src0">TextureImage textures[1];<span class="kom">// Jedna textura</span></p>

<p>Na �adu p�ich�z� asi nejobt�n�j�� ��st - nahr�v�n� TGA obr�zku a jeho konvertov�n� na texturu. Mus�m je�t� poznamenat, �e k�d n�sleduj�c� funkce umo��uje loadovat bu� 24 nebo 32 bitov� <b>nekomprimovan�</b> TGA soubory. Zabralo dost �asu zprovoznit k�d, kter� by pracoval s ob�ma typy. Nikdy jsem ne�ekl, �e jsem g�nius. R�d bych pouk�zal, �e �pln� v�echno nen� z m� hlavy. Spoustu opravdu dobr�ch n�pad� jsem z�skal pro��t�n�m internetu. Pokusil jsem se je zkombinovat do funk�n�ho k�du, kter� pracuje s OpenGL. Nic snadn�ho, nic extr�mn� slo�it�ho!</p>

<p>Funkci p�ed�v�me dva parametry. Prvn� ukazuje do pam�ti, kam ulo��me texturu. Druh� ur�uje diskovou cestu k souboru, kter� chceme nahr�t.</p>

<p class="src0">bool LoadTGA(TextureImage *texture, char *filename)<span class="kom">// Do pam�ti nahraje TGA soubor</span></p>
<p class="src0">{</p>

<p>Pole TGAheader[] definuje 12 byt�. Porovn�me je s prvn�mi 12 bity, kter� na�teme z TGA souboru - TGAcompare[], abychom se ujistili, �e je to opravdu Targa obr�zek a ne n�jak� jin�.</p>

<p class="src1">GLubyte TGAheader[12] = { 0,0,2,0,0,0,0,0,0,0,0,0 };<span class="kom">// Nekomprimovan� TGA hlavi�ka</span></p>
<p class="src1">GLubyte TGAcompare[12];<span class="kom">// Pro porovn�n� TGA hlavi�ky</span></p>

<p>Header[] ukl�d� prvn�ch �est D�LE�IT�CH byt� z hlavi�ky souboru (���ka, v��ka, barevn� hloubka).</p>

<p class="src1">GLubyte header[6];<span class="kom">// Prvn�ch 6 u�ite�n�ch byt� z hlavi�ky</span></p>

<p>Do bytesPerPixel p�i�ad�me v�sledek operace, kdy vyd�l�me barevnou hloubku v bitech osmi, abychom z�skali barevnou hloubku v bytech na pixel. ImageSize definuje po�et byt�, kter� jsou zapot�eb� k vytvo�en� obr�zku (���ka*v��ka*barevn� hloubka).</p>

<p class="src1">GLuint bytesPerPixel;<span class="kom">// Po�et byt� na pixel pou�it� v TGA souboru</span></p>
<p class="src1">GLuint imageSize;<span class="kom">// Ukl�d� velikost obr�zku p�i alokov�n� RAM</span></p>

<p>Temp umo�n� prohodit byty d�le v programu. A kone�n� posledn� prom�nnou pou�ijeme ke zvolen� spr�vn�ho parametru p�i vytv��en� textury. Bude z�viset na tom, zda je TGA 24 nebo 32 bitov�. V p��pad� 24 bit� p�ed�me GL_RGB a m�me-li 32 bitov� obr�zek pou�ijeme GL_RGBA. Implicitn� p�edpokl�d�me, �e je obr�zek 32 bitov�, tud� do type p�i�ad�me GL_RGBA.</p>

<p class="src1">GLuint temp;<span class="kom">// Pomocn� prom�nn�</span></p>
<p class="src1">GLuint type = GL_RGBA;<span class="kom">// Implicitn�m GL m�dem je RGBA (32 BPP)</span></p>

<p>Pomoc� funkce fopen() otev�eme TGA soubor filename pro �ten� v bin�rn�m m�du (rb). N�sleduje v�tven� if, ve kter�m d�l�me hned n�kolik v�c� najednou. Nejprve testujeme jestli soubor obsahuje data. Pokud tam ��dn� nejsou, vr�t�me false. Obsahuje-li informace, p�e�teme prvn�ch dvan�ct byt� do TGAcompare. Pou�ijeme funkci fread(), kter� po jednom bytu na�te ze souboru file dvan�ct byt� (sizeof(TGAcompare)) a v�sledek ulo�� do TGAcompare. Vrac� po�et p�e�ten�ch byt�, kter� porovn�me se sizeof(TGAcompare). M�lo by jich b�t, jak tu��te :-), dvan�ct. Pokud jsme bez pot�� do�li a� tak daleko, porovn�me funkc� memcmp() pole TGAheader a TGAcompare. Nebudou-li stejn� zav�eme soubor a vr�t�me false, proto�e se nejedn� o TGA obr�zek. Do header nakonec na�teme dal��ch �est byt�. P�i chyb� op�t zav�eme soubor a funkci ukon��me.</p>

<p class="src1">FILE *file = fopen(filename, &quot;rb&quot;);<span class="kom">// Otev�e TGA soubor</span></p>
<p class="src"></p>
<p class="src1">if(file == NULL || <span class="kom">// Existuje soubor?</span></p>
<p class="src2">fread(TGAcompare,1,sizeof(TGAcompare),file) != sizeof(TGAcompare) ||<span class="kom">// Poda�ilo se na��st 12 byt�?</span></p>
<p class="src2">memcmp(TGAheader,TGAcompare,sizeof(TGAheader)) != 0 ||<span class="kom">// Maj� pot�ebn� hodnoty?</span></p>
<p class="src2">fread(header,1,sizeof(header),file) != sizeof(header))<span class="kom">// Pokud ano, na�te dal��ch �est byt�</span></p>
<p class="src1">{</p>
<p class="src2">if (file == NULL)<span class="kom">// Existuje soubor?</span></p>
<p class="src3">return false;<span class="kom">// Konec funkce</span></p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">fclose(file);<span class="kom">// Zav�e soubor</span></p>
<p class="src3">return false;<span class="kom">// Konec funkce</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Pokud program pro�el k�dem bez chyby m�me dost informac� pro definov�n� n�kter�ch prom�nn�ch. Prvn� bude ���ka obr�zku. Probl�m spo��v� v tom, �e toto ��slo je rozd�leno do dvou byt�. Ni��� byte m��e nab�vat 256 hodnot (8 bit�), tak�e vyn�sob�me vy��� byte 256 a k n�mu p�i�teme ni���. Z�skali jsme ���ku obr�zku. Stejn�m postupem dostaneme i v��ku, akor�t pou�ijeme jin� indexy v poli.</p>

<p class="src1">texture-&gt;width  = header[1] * 256 + header[0];<span class="kom">// Z�sk� ���ku obr�zku</span></p>
<p class="src1">texture-&gt;height = header[3] * 256 + header[2];<span class="kom">// Z�sk� v��ku obr�zku</span></p>

<p>Zkontrolujeme jestli je ���ka i v��ka v�t�� ne� nula. Pokud ne zav�eme soubor a vr�t�me false. Z�rove� zkontrolujeme i barevnou hloubku, kterou hled�me v header[4]. Mus� b�t bu� 24 nebo 32 bitov�.</p>

<p class="src1"> if(texture-&gt;width &lt;= 0 ||<span class="kom">// Platn� ���ka?</span></p>
<p class="src2">texture-&gt;height &lt;= 0 ||<span class="kom">// Platn� v��ka?</span></p>
<p class="src2">(header[4] != 24 &amp;&amp; header[4] != 32))<span class="kom">// Platn� barevn� hloubka?</span></p>
<p class="src1">{</p>
<p class="src2">fclose(file);<span class="kom">// Zav�e soubor</span></p>
<p class="src2">return false;<span class="kom">// Konec funkce</span></p>
<p class="src1">}</p>

<p>Spo��tali a zkontrolovali jsme ���ku a v��ku, m��eme p�ej�t k barevn� hloubce v bitech a bytech a velikosti pam�ti pot�ebn� k ulo�en� dat obr�zku. U� v�me, �e v header[4] je barevn� hloubka v bitech na pixel. P�i�ad�me ji do bpp. Jeden byte se skl�d� z 8 bit�. Z toho plyne, �e barevnou hloubku v bytech z�sk�me d�len�m bpp osmi. Velikost dat obr�zku z�sk�me vyn�soben�m ���ky, v��ky a byt� na pixel.</p>

<p class="src1">texture-&gt;bpp = header[4];<span class="kom">// Bity na pixel (24 nebo 32)</span></p>
<p class="src"></p>
<p class="src1">bytesPerPixel = texture-&gt;bpp / 8;<span class="kom">// Byty na pixel</span></p>
<p class="src"></p>
<p class="src1">imageSize = texture-&gt;width * texture-&gt;height * bytesPerPixel;<span class="kom">// Velikost pam�ti pro data obr�zku</span></p>

<p>Pot�ebujeme alokovat pam� pro data obr�zku. Funkci malloc() p�ed�me po�adovanou velikost. M�la by vr�tit ukazatel na zabran� m�sto v RAM. N�sleduj�c� if m� op�t n�kolik �loh. V prv� �ad� testuje spr�vnost alokace. Pokud p�i n� n�co nevy�lo, ukazatel m� hodnotu NULL. V takov�m p��pad� zav�eme soubor a vr�t�me false. Nicm�n� pokud se alokace poda�ila, tak  pomoc� fread() na�teme data obr�zku a ulo��me je do pr�v� alokovan� pam�ti. Pokud se data nepoda�� zkop�rovat, uvoln�me pam�, zav�eme soubor a ukon��me funkci.</p>

<p class="src1">texture-&gt;imageData = (GLubyte *)malloc(imageSize);<span class="kom">// Alokace pam�ti pro data obr�zku</span></p>
<p class="src"></p>
<p class="src1">if(texture-&gt;imageData == NULL ||<span class="kom">// Poda�ilo se pam� alokovat?</span></p>
<p class="src2">fread(texture-&gt;imageData, 1, imageSize, file) != imageSize)<span class="kom">// Poda�ilo se kop�rov�n� dat?</span></p>
<p class="src1">{</p>
<p class="src2">if(texture-&gt;imageData != NULL)<span class="kom">// Byla data nahr�na?</span></p>
<p class="src3">free(texture-&gt;imageData);<span class="kom">// Uvoln� pam�</span></p>
<p class="src"></p>
<p class="src2">fclose(file);<span class="kom">// Zav�e soubor</span></p>
<p class="src2">return false;<span class="kom">// Konec funkce</span></p>
<p class="src1">}</p>

<p>Pokud se a� dote� nestalo nic, ��m bychom ukon�ovali funkci, m�me vyhr�no. Stoj� p�ed n�mi, ale je�t� jeden �kol. Form�t TGA specifikuje po�ad� barevn�ch slo�ek BGR (modr�, zelen�, �erven�) narozd�l od OpenGL, kter� pou��v� RGB. Pokud bychom neprohodili �ervenou a modrou slo�ku, tak v�echno, co m� b�t v obr�zku modr� by bylo �erven� a naopak. Deklarujeme cyklus, jeho� ��d�c� prom�nn� i nab�v� hodnot od nuly do velikosti obr�zky. Ka�d�m pr�chodem se zv�t�uje o 3 nebo o 4 v z�vislosti na barevn� hloubce. (24/8=3, 32/8=4). Uvnit� cyklu prohod�me R a B slo�ky. Modr� je na indexu i a �erven� i+2. Modr� by byla na i+1, ale s tou nic ned�l�me, proto�e je um�st�n� spr�vn�.</p>

<p class="src1">for(GLuint i=0; i &lt; int(imageSize); i += bytesPerPixel)<span class="kom">// Proch�z� data obr�zku</span></p>
<p class="src1">{</p>
<p class="src2">temp = texture-&gt;imageData[i];<span class="kom">// B ulo��me do pomocn� prom�nn�</span></p>
<p class="src2">texture-&gt;imageData[i] = texture-&gt;imageData[i + 2];<span class="kom">// R je na spr�vn�m m�st�</span></p>
<p class="src2">texture-&gt;imageData[i + 2] = temp;<span class="kom">// B je na spr�vn�m m�st�</span></p>
<p class="src1">}</p>

<p>Po t�to operaci m�me v pam�ti ulo�en obr�zek TGA ve form�tu, kter� podporuje OpenGL. Nic n�m nebr�n�, abychom zav�eli soubor. U� ho k ni�emu nepot�ebujeme.</p>

<p class="src1">fclose(file);<span class="kom">// Zav�e soubor</span></p>

<p>M��eme za��t vytv��et texturu. Tento postup je v principu �pln� stejn�, jako ten, kter� jsme pou��vali v minul�ch tutori�lech. Po��d�me OpenGL o vygenerov�n� jedn� textury na adrese texture[0].textID, kterou jsme z�skali p�ed�n�m parametru ve funkci InitGL(). Pokud bychom cht�li vytvo�it druhou texturu z jin�ho obr�zku TGA, tak se tato funkci v�bec nezm�n�. V InitGL() bychom provedli vol�n� dvakr�t, ale s jin�mi parametry. Programujeme obecn�ji...</p>

<p class="src1">glGenTextures(1, &amp;texture[0].texID);<span class="kom">// Generuje texturu</span></p>

<p>Zvol�me pr�v� vytv��enou texturu za aktu�ln� a nastav�me j� line�rn� filtrov�n� pro zmen�en� i zv�t�en�.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0].texID);<span class="kom">// Zvol� texturu</span></p>
<p class="src"></p>
<p class="src1">glTexParameterf(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);<span class="kom">// Line�rn� filtrov�n�</span></p>
<p class="src1">glTexParameterf(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);<span class="kom">// Line�rn� filtrov�n�</span></p>

<p>Zkontrolujeme, jestli je textura 24 nebo 32 bitov�. V prvn�m p��pad� nastav�me type na GL_RGB (bez alfa kan�lu), jinak ponech�me implicitn� hodnotu GL_RGBA (s alfa kan�lem). Pokud bychom test neprovedli, program by se s nejv�t�� pravd�podobnost� zhroutil.</p>

<p class="src1">if (texture[0].bpp == 24)<span class="kom">// Je obr�zek 24 bitov�?</span></p>
<p class="src1">{</p>
<p class="src2">type = GL_RGB;<span class="kom">// Nastav� typ na GL_RGB</span></p>
<p class="src1">}</p>

<p>Te� kone�n� sestav�me texturu. Jako obvykle, tak i tentokr�t, pou�ijeme funkci glTexImage2D(). M�sto ru�n�ho zad�n� typu textury (GL_RGB, GL_RGBA) p�ed�me hodnotu pomoc� prom�nn�. Jednodu�e �e�eno: Program s�m detekuje, co m� p�edat.</p>

<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, type, texture[0].width, texture[0].height, 0, type, GL_UNSIGNED_BYTE, texture[0].imageData);<span class="kom">// Vytvo�� texturu</span></p>
<p class="src"></p>
<p class="src1">return true;<span class="kom">// V�echno je v po��dku</span></p>
<p class="src0">}</p>

<p>ReSizeGLScene() nastavuje pravo�hlou projekci. Sou�adnice [0; 1] jsou lev�m horn�m rohem okna a [640; 480] prav�m doln�m. Dost�v�me rozli�en� 640x480. Na za��tku nastav�me glob�ln� prom�nn� swidth a sheight na aktu�ln� rozm�ry okna. P�i ka�d�m p�esunut� nebo zm�n� velikosti okna se aktualizuj�. Ostatn� k�d zn�te.</p>

<p class="src0">GLvoid ReSizeGLScene(GLsizei width, GLsizei height)<span class="kom">// Zm�na velikosti a inicializace OpenGL okna</span></p>
<p class="src0">{</p>
<p class="src1">swidth = width;<span class="kom">// ���ka okna</span></p>
<p class="src1">sheight = height;<span class="kom">// V��ka okna</span></p>
<p class="src"></p>
<p class="src1">if (height == 0)<span class="kom">// Zabezpe�en� proti d�len� nulou</span></p>
<p class="src1">{</p>
<p class="src2">height = 1;<span class="kom">// Nastav� v��ku na jedna</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glViewport(0,0,width,height);<span class="kom">// Resetuje aktu�ln� nastaven�</span></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvol� projek�n� matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glOrtho(0.0f,640,480,0.0f,-1.0f,1.0f);<span class="kom">// Pravo�hl� projekce 640x480, [0; 0] vlevo naho�e</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvol� matici Modelview</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src0">}</p>

<p>Inicializace OpenGL se minimalizovala. Z�stala z n� jenom kostra. Nahrajeme TGA obr�zek a vytvo��me z n�j texturu. V prvn�m parametru je ur�eno, kam ji ulo��me a v druh�m diskov� cesta k obr�zku. Vr�t�-li funkce z jak�hokoli d�vodu false, inicializace se p�eru��, program zobraz� chybovou zpr�vu a ukon�� se. Pokud byste cht�li nahr�t druhou nebo i dal�� textury pou�ijte vol�n� n�kolik. Podm�nka se logicky ORuje.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadTGA(&amp;textures[0], &quot;Data/Font.TGA&quot;))<span class="kom">// Nahraje texturu fontu z TGA obr�zku</span></p>
<p class="src1">{</p>
<p class="src2">return false;<span class="kom">// P�i chyb� ukon�� program</span></p>
<p class="src1">}</p>

<p>Po �sp�n�m nahr�n� textury vytvo��me font. Je d�le�it� upozornit, �e se BuildFont() mus� volat a� po funkci LoadTGA(), proto�e pou��v� j� vytvo�enou texturu. D�le nastav�me vyhlazen� st�nov�n�, �ern� pozad�, povol�me maz�n� depth bufferu a zvol�me texturu fontu.</p>

<p class="src1">BuildFont();<span class="kom">// Sestav� font</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Vyhlazen� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, textures[0].texID);<span class="kom">// Zvol� texturu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace v po��dku</span></p>
<p class="src0">}</p>

<p>P�ejdeme k vykreslov�n�. Za�neme deklarov�n�m prom�nn�ch. O ukazateli token zat�m jen tolik, �e bude ukl�dat �et�zec jednoho podporovan�ho roz���en� a cnt je pro zji�t�n� jeho po�ad�.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">char* token;<span class="kom">// Ukl�d� jedno roz���en�</span></p>
<p class="src1">int cnt = 0;<span class="kom">// ��ta� roz���en�</span></p>

<p>Sma�eme obrazovku a hloubkov� buffer. Potom nastav�me barvu na st�edn� tmav� �ervenou a do horn� ��sti okna vyp�eme slova Renderer (jm�no grafick� karty), Vendor (jej� v�robce) a Version (verze). D�vod, pro� nejsou v�echny um�st�ny 50 pixel� od okraje na ose x, je ten, �e je nezarovn�v�me doleva, ale doprava.</p>

<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src"></p>
<p class="src1">glColor3f(1.0f,0.5f,0.5f);<span class="kom">// �erven� barva</span></p>
<p class="src1">glPrint(50,16,1,&quot;Renderer&quot;);<span class="kom">// V�pis nadpisu pro grafickou kartu</span></p>
<p class="src1">glPrint(80,48,1,&quot;Vendor&quot;);<span class="kom">// V�pis nadpisu pro v�robce</span></p>
<p class="src1">glPrint(66,80,1,&quot;Version&quot;);<span class="kom">// V�pis nadpisu pro verzi</span></p>

<p>Zm�n�me �ervenou barvu na oran�ovou a nagrabujeme informace z grafick� karty. Pou�ijeme funkci glGetString(), kter� vr�t� po�adovan� �et�zce. Kv�li glPrint() p�etypujeme v�stup funkce na char*. V�sledek vyp�eme doprava od nadpis�.</p>

<p class="src1">glColor3f(1.0f,0.7f,0.4f);<span class="kom">// Oran�ov� barva</span></p>
<p class="src1">glPrint(200,16,1,(char *)glGetString(GL_RENDERER));<span class="kom">// V�pis typu grafick� karty</span></p>
<p class="src1">glPrint(200,48,1,(char *)glGetString(GL_VENDOR));<span class="kom">// V�pis v�robce</span></p>
<p class="src1">glPrint(200,80,1,(char *)glGetString(GL_VERSION));<span class="kom">// V�pis verze</span></p>

<p>Definujeme modrou barvu a dol� na sc�nu vyp�eme NeHe Productions.</p>

<p class="src1">glColor3f(0.5f,0.5f,1.0f);<span class="kom">// Modr� barva</span></p>
<p class="src1">glPrint(192,432,1,&quot;NeHe Productions&quot;);<span class="kom">// V�pis NeHe Productions</span></p>

<p>Kolem pr�v� vypsan�ho textu vykresl�me b�l� r�me�ek. Resetujeme matici, proto�e v glPrint() se volaj� funkce, kter� ji m�n�. Potom definujeme b�lou barvu.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glColor3f(1.0f,1.0f,1.0f);<span class="kom">// B�l� barva</span></p>

<p>Vykreslov�n� linek pomoc� GL_LINE_STRIP je velmi jednoduch�. Prvn� bod definujeme �pln� vpravo, 63 pixel� (480-417=63) nad spodn�m okrajem okna. Druh� vertex um�st�me ve stejn� v��ce, ale vlevo. OpenGL je spoj� p��mkou. T�et� bod posuneme dol� do lev�ho doln�ho rohu. OpenGL op�t zobraz� linku, tentokr�t mezi druh�m a t�et�m bodem. �tvrt� bod pat�� do prav�ho doln�ho rohu a k p�t�mu projedeme v�choz�m vertexem nahoru. Ukon��me triangle strip, abychom mohli za��t vykreslovat z nov� pozice a stejn�m zp�sobem vykresl�me druhou ��st r�me�ku, ale tentokr�t naho�e.</p>

<p>Asi jste pochopili, �e pokud vykreslujeme v�ce na sebe navazuj�c�ch p��mek, tak LINE_STRIP u�et�� spoustu zbyte�n�ho k�du, kter� vznik� opakovan�m definov�n�m vertex� p�i oby�ejn�m GL_LINES.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_24_line_strip.gif" width="150" height="100" alt="Po�ad� zad�v�n� bod�" /></div>

<p class="src1">glBegin(GL_LINE_STRIP);<span class="kom">// Za��tek kreslen� linek</span></p>
<p class="src2">glVertex2d(639,417);<span class="kom">// 1</span></p>
<p class="src2">glVertex2d(0,417);<span class="kom">// 2</span></p>
<p class="src2">glVertex2d(0,480);<span class="kom">// 3</span></p>
<p class="src2">glVertex2d(639,480);<span class="kom">// 4</span></p>
<p class="src2">glVertex2d(639,128);<span class="kom">// 5</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_LINE_STRIP);<span class="kom">// Za��tek kreslen� linek</span></p>
<p class="src2">glVertex2d(0,128);<span class="kom">// 6</span></p>
<p class="src2">glVertex2d(639,128);<span class="kom">// 7</span></p>
<p class="src2">glVertex2d(639,1);<span class="kom">// 8</span></p>
<p class="src2">glVertex2d(0,1);<span class="kom">// 9</span></p>
<p class="src2">glVertex2d(0,417);<span class="kom">// 10</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>N�m nezn�m� funkce glScissor(x, y, v, �) vytv��� n�co, co by se dalo popsat jako okno. Pokud zapneme GL_SCISSOR_TEST, bude se o�ez�vat okol� t�to ��sti obrazovky, tud� se objekty budou moci vykreslovat pouze uvnit� definovan�ho obd�ln�ku. Ur��me ho parametry p�edan�mi funkci. V na�em p��pad� je to prvn� pixel na ose x ve v��ce 13,5% (0,135...f) od spodn�ho okraje. d�le bude 638 pixel� �irok� (swidth-2) a 59,7% (0,597...f) v��ky okna vysok�. Druh�m ��dkem povol�me o�ez�vac� testy. M��ete se pokusit vykreslit obrovsk� obd�ln�k p�es cel� okno, ale uvid�te pouze ��st v neo�ezan� oblasti. zbytek dosud nakreslen� sc�ny z�stane nezm�n�n. Perfektn� p��kaz!</p>

<p class="src1">glScissor(1, int(0.135416f*sheight), swidth-2, int(0.597916f*sheight));<span class="kom">// Definov�n� o�ez�vac� oblasti</span></p>
<p class="src1">glEnable(GL_SCISSOR_TEST);<span class="kom">// Povol� o�ez�vac� testy</span></p>

<p>Na �adu p�ich�z� asi nejt쾹� ��st t�to lekce - vyps�n� podporovan�ch OpenGL roz���en�. V prvn� f�zi je mus�me z�skat. Pomoc� funkce malloc() alokujeme buffer pro �et�zec znak� text. P�ed�v� se j� velikost po�adovan� pam�ti. Strlen() spo��t� po�et znak� �et�zce vr�cen�ho glGetString(GL_EXTENSIONS). P�i�teme k n�mu je�t� jeden znak pro '\0', kter� uzav�r� ka�d� c-��kovsk� �et�zec. Strcpy() zkop�ruje �et�zec podporovan�ch roz���en� do prom�nn� text.</p>

<p class="src1">char* text = (char *)malloc(strlen((char *)glGetString(GL_EXTENSIONS))+1);<span class="kom">// Alokace pam�ti pro �et�zec</span></p>
<p class="src1">strcpy(text,(char *)glGetString(GL_EXTENSIONS));<span class="kom">// Zkop�ruje seznam roz���en� do text</span></p>

<p>Nyn� jsme do text nagrabovali z grafick� karty �et�zec, kter� vypad� n�jak takto: &quot;GL_ARB_multitexture GL_EXT_abgr GL_EXT_bgra&quot;. Pomoc� strtok() z n�j vyjmeme v po�ad� prvn� roz���en�. Funkce pracuje tak, �e proch�z� �et�zec a v p��pad�, �e najde mezeru zkop�ruje p��slu�nou ��st z text do token. Prvn� hodnota token tedy bude &quot;GL_ARB_multitexture&quot;. Z�rove� se v�ak zm�n� i text. Prvn� mezera se nahrad� odd�lova�em. V�ce d�le.</p>

<p class="src1">token = strtok(text, &quot; &quot;);<span class="kom">// Z�sk� prvn� pod�et�zec</span></p>

<p>Vytvo��me cyklus, kter� se zastav� tehdy, kdy� v token nezbudou u� ��dn� dal�� informace - bude se rovnat NULL. Ka�d�m pr�chodem inkrementujeme ��ta� a zkontrolujeme, jestli je jeho hodnota v�t�� ne� maxtokens. Touto cestou velice snadno z�sk�me maxim�ln� hodnotu v ��ta�i, kterou vyu�ijeme p�i rolov�n� po stisku kl�ves.</p>

<p class="src1">while(token != NULL)<span class="kom">// Proch�z� podporovan� roz���en�</span></p>
<p class="src1">{</p>
<p class="src2">cnt++;<span class="kom">// Inkrementuje ��ta�</span></p>
<p class="src"></p>
<p class="src2">if (cnt &gt; maxtokens)<span class="kom">// Je maximum men�� ne� hodnota ��ta�e?</span></p>
<p class="src2">{</p>
<p class="src3">maxtokens = cnt;<span class="kom">// Aktualizace maxima</span></p>
<p class="src2">}</p>

<p>V t�to chv�li m�me v token ulo�en� prvn� roz���en�. Jeho po�adov� ��slo nap�eme zelen� do lev� ��sti okna. V�imn�te si, �e ho na ose x nap�eme na sou�adnici 0. T�m bychom mohli zlikvidovat lev� (b�l�) r�me�ek, kter� jsme u� vykreslili, ale proto�e m�me zapnut� o�ez�v�n�, pixely na nule nebudou modifikov�ny. Na ose y za��n�me kreslit na 96. Abychom nevykreslovali v�echno na sebe, p�i��t�me po�ad� n�soben� v��kou textu (cnt*32). P�i vypisov�n� prvn�ho roz���en� se cnt==1 a text se nakresl� na 96+(32*1)=128. U druh�ho je v�sledkem 160. Tak� ode��t�me scroll. Implicitn� se rovn� nule, ale po stisku �ipek se jeho hodnota m�n�. Umo�n�me t�m rolov�n� o�ezan�ho okna, do kter�ho se vejde celkem dev�t ��dek (v��ka okna/v��ka textu = 288/32 = 9). Zm�nou scrollu m��eme zm�nit offset textu a t�m ho posunout nahoru nebo dol�. Efekt je podobn� filmov�mu projektoru. Film roluje tak, aby v jednom okam�iku byl vid�t v�dy jen jeden frame. Nem��ete vid�t oblast nad nebo pod n�m i kdy� m�te v�t�� pl�tno. Objektiv sehr�v� stejnou roli jako o�ez�vac� testy.</p>

<p class="src2">glColor3f(0.5f,1.0f,0.5f);<span class="kom">// Zelen� barva</span></p>
<p class="src2">glPrint(0, 96+(cnt*32)-scroll, 0, &quot;%i&quot;, cnt);<span class="kom">// Po�ad� aktu�ln�ho roz���en�</span></p>

<p>Po vykreslen� po�adov�ho ��sla zam�n�me zelenou barvu za �lutou a kone�n� vyp�eme text ulo�en� v prom�nn� token. Vlevo se za�ne na pades�t�m pixelu.</p>

<p class="src2">glColor3f(1.0f,1.0f,0.5f);<span class="kom">// �lut� barva</span></p>
<p class="src2">glPrint(50,96+(cnt*32)-scroll,0,token);<span class="kom">// Vyp�e jedno roz���en�</span></p>

<p>Po zobrazen� prvn�ho roz���en� pot�ebujeme p�ipravit p�du pro dal�� pr�chod cyklem. Nejprve zjist�me, jestli je v text je�t� n�jak� dal�� roz���en�. Nam�sto op�tovn�ho vol�n� token = strtok(text, &quot; &quot;), nap�eme token = strtok(NULL, &quot; &quot;); NULL ur�uje, �e se m� hledat DAL�� pod�et�zec a ne v�echno prov�d�t od znova. V na�em p��klad� jsem v��e napsal, �e se mezera nahrad� odd�lova�em - &quot;GL_ARB_multitextureodd�lova�GL_EXT_abgr GL_EXT_bgra&quot;. Najdeme tedy odd�lova� a a� od n�j se bude hledat dal�� mezera. Pot� se do token zkop�ruje pod�et�zec mezi odd�lova�em a mezerou (GL_EXT_abgr) a text bude modifikov�n na &quot;GL_ARB_multitextureodd�lova�GL_EXT_abgrodd�lova�GL_EXT_bgra&quot;. Po dosa�en� konce textu se token nastav� na NULL a cyklus se ukon��.</p>

<p class="src2">token = strtok(NULL, &quot; &quot;);<span class="kom">// Najde dal�� roz���en�</span></p>
<p class="src1">}</p>

<p>T�m jsme ukon�ili vykreslov�n�, ale je�t� n�m zb�v� po sob� uklidit. Vypneme o�ez�vac� testy a uvoln�me dynamickou pam� - informace z�skan� pomoc� glGetString(GL_EXTENSIONS) ulo�en� v RAM. P��t� a� budeme volat DrawGLScene() se pam� op�t alokuje a provedou se znovu v�echny rozbory �et�zc�.</p>

<p class="src1">glDisable(GL_SCISSOR_TEST);<span class="kom">// Vypne o�ez�vac� testy</span></p>
<p class="src"></p>
<p class="src1">free(text);<span class="kom">// Uvoln� dynamickou pam�</span></p>

<p>P��kaz glFlush() nen� bezpodm�ne�n� nutn�, ale mysl�m, �e je dobr� n�pad se o n�m zm�nit. Nejjednodu��� vysv�tlen� je takov�, �e ozn�m� OpenGL, aby dokon�ilo, co pr�v� d�l� (n�kter� grafick� karty nap�. pou��vaj� vyrovn�vac� pam�ti, jejich� obsah se t�mto po�le na v�stup). Pokud si n�kdy v�imnete mihot�n� nebo blik�n� polygon�, zkuste p�idat na konec v�eho vykreslov�n� vol�n� glFlush(). Vypr�zdn� renderovac� pipeline a t�m zamez� mihot�n�, kter� vznik� tehdy, kdy� program nem� dostatek �asu, aby dokon�il rendering.</p>

<p class="src1">glFlush();<span class="kom">// Vypr�zdn� renderovac� pipeline</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�echno v po��dku</span></p>
<p class="src0">}</p>

<p>Na konec KillGLWindow() p�id�me vol�n� KillFont, kter� sma�e display listy fontu.</p>

<p class="src0"><span class="kom">// Konec KillGLWindow()</span></p>
<p class="src1">KillFont();<span class="kom">// Sma�e font</span></p>
<p class="src0">}</p>

<p>V programu testujeme stisk �ipky nahoru a dol�. V obou p��padech p�i�teme nebo ode�teme od scroll dvojku, ale pouze tehdy, pokud bychom nerolovali mimo okno. U �ipky nahoru je situace jednoduch� - nula je v�dy nejni��� mo�n� rolov�n�. Maximum u �ipky dol� z�sk�me n�soben�m v��ky ��dku a po�tu roz���en�. Dev�tku ode��t�me, proto�e se v jednom okam�iku vejde na sc�nu dev�t ��dk�.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if (keys[VK_UP] &amp;&amp; (scroll &gt; 0))<span class="kom">// �ipka nahoru?</span></p>
<p class="src4">{</p>
<p class="src5">scroll -= 2;<span class="kom">// Posune text nahoru</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN] &amp;&amp; (scroll &lt; 32*(maxtokens-9)))<span class="kom">// �ipka dol�?</span></p>
<p class="src4">{</p>
<p class="src5">scroll += 2;<span class="kom">// Posune text dol�</span></p>
<p class="src4">}</p>

<p>Douf�m, �e byl pro v�s tento tutori�l zaj�mav�. Ji� v�te, jak z�skat informace o v�robci, jm�nu a verzi grafick� karty a tak�, kter� OpenGL roz���en� podporuje. M�li byste v�d�t, jak pou��t o�ez�vac� testy a nem�n� d�le�itou v�c� je nahr�v�n� TGA m�sto bitmapov�ch obr�zk� a jejich konverze na textury.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson24.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson24_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson24.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson24.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson24.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson24.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson24.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson24.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson24.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:jay@remotepoint.com">Jay Groven</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson24.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:scarab@egyptian.net">DarkAlloy</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson24.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson24.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(24);?>
<?FceNeHeOkolniLekce(24);?>

<?
include 'p_end.php';
?>
