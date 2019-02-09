<?
$g_title = 'CZ NeHe OpenGL - Volumetrick� mlha a nahr�v�n� obr�zk� pomoc� IPicture';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(41);?>

<h1>Lekce 41 - Volumetrick� mlha a nahr�v�n� obr�zk� pomoc� IPicture</h1>

<p class="nadpis_clanku">V tomto tutori�lu se nau��te, jak pomoc� roz���en� EXT_fog_coord vytvo�it volumetrickou mlhu. Tak� zjist�te, jak pracuje IPicture k�d a jak ho m��ete vyu��t pro nahr�v�n� obr�zk� ve sv�ch vlastn�ch projektech. Demo sice nen� a� tak komplexn� jako n�kter� jin�, nicm�n� i p�esto vypad� hodn� efektn�.</p>

<p>Pokud demo nebude na va�em syst�mu fungovat, nejd��ve se ujist�te, �e m�te nainstalovan� nejnov�j�� ovlada�e grafick� karty. Pokud to nepomohlo, zauva�ujte o koupi nov� (P�ekl.: :-] ). V sou�asn� dob� u� ne zrovna nejnov�j�� GeForce 2 pracuje dob�e a ani nestoj� tak moc. Pokud va�e grafick� karta nepodporuje roz���en� mlhy, kdo m��e v�d�t, jak� dal�� roz���en� nebude podporovat?</p>

<p>Pro ty z v�s, kter�m toto demo nejede a c�t� se vylou�eni... m�jte na pam�ti n�sleduj�c�: Snad ka�d� den dost�v�m nejm�n� jeden email s dotazem na nov� tutori�l. Nejhor�� z toho je, �e v�t�ina z nich u� je online. Lid� se neobt�uj� ��st to, co u� je naps�no a p�eskakuj� na t�mata, kter� je v�ce zaj�maj�. N�kter� tutori�ly jsou p��li� komplexn�, a proto z m� strany vy�aduj� n�kdy i t�dny programov�n�. Pak jsou tady tutori�ly, kter� bych sice mohl napsat, ale v�t�inou se jim vyh�b�m, proto�e nefunguj� na v�ech kart�ch. Nyn� jsou u� karty jako GeForce levn� natolik, aby si je mohl dovolit t�m�� ka�d�, tak�e u� nebudu d�le ospravedl�ovat neps�n� takov�chto tutori�l�. Popravd�, pokud va�e karta podporuje pouze z�kladn� roz���en�, budete s nejv�t�� pravd�podobnost� chyb�t! Pokud se vr�t�m k p�eskakov�n� t�mat jako jsou nap�. roz���en�, tutori�ly se brzy oproti ostatn�m za�nou v�razn� opo��ovat.</p>

<p>K�d za��n� velmi podobn� jako star� z�kladn� k�d a pov�t�inou je identick� s nov�m NeHeGL k�dem. Jedin� rozd�l spo��v� v inkludov�n� OLECTL hlavi�kov�ho souboru, kter�, chcete-li pou��vat IPicture pro loading obr�zk�, mus� b�t p��tomen.</p>

<p>P�ekl.: IPicture je podle m� sice hezk� n�pad a pracuje perfektn�, nicm�n� je kompletn� vystav�n na ABSOLUTN� NEP�ENOSITELN�CH technologi�ch MS, kter� jdou tradi�n� pou��vat v�hradn� pod nejmenovan�m OS, v�ichni v�me, o kter� jde.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Windows</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// OpenGL</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// GLU</span></p>
<p class="src0">#include &lt;olectl.h&gt;<span class="kom">// Knihovna OLE Controls Library (pou�ita p�i nahr�v�n� obr�zk�)</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Matematika</span></p>
<p class="src"></p>
<p class="src0">#include &quot;NeHeGL.h&quot;<span class="kom">// NeHeGL</span></p>
<p class="src"></p>
<p class="src0">#pragma comment(lib, &quot;opengl32.lib&quot;)<span class="kom">// P�ilinkov�n� OpenGL a GLU</span></p>
<p class="src0">#pragma comment(lib, &quot;glu32.lib&quot;)</p>
<p class="src"></p>
<p class="src0">#ifndef CDS_FULLSCREEN<span class="kom">// N�kter� kompil�tory CDS_FULLSCREEN nedefinuj�</span></p>
<p class="src0">#define CDS_FULLSCREEN 4</p>
<p class="src0">#endif</p>
<p class="src"></p>
<p class="src0">GL_Window* g_window;<span class="kom">// Struktura okna</span></p>
<p class="src0">Keys* g_keys;<span class="kom">// Kl�vesnice</span></p>

<p>Deklarujeme �ty� prvkov� pole fogColor, kter� bude ukl�dat barvu mlhy, v na�em p��pad� se jedn� o tmav� oran�ovou (trocha �erven� sm�chan� se �petkou zelen�). Desetinn� hodnota camz bude slou�it pro um�st�n� kamery na ose z. P�ed vykreslen�m v�dy provedeme translaci.</p>

<p class="src0">GLfloat fogColor[4] = {0.6f, 0.3f, 0.0f, 1.0f};<span class="kom">// Barva mlhy</span></p>
<p class="src0">GLfloat camz;<span class="kom">// Pozice kamery na ose z</span></p>

<p>Ze souboru glext.h p�evezmeme symbolick� konstanty GL_FOG_COORDINATE_SOURCE_EXT a GL_FOG_COORDINATE_EXT. Pokud chcete k�d zkompilovat, mus� b�t nastaveny.</p>

<p class="src0"><span class="kom">// P�evzato z glext.h</span></p>
<p class="src0">#define GL_FOG_COORDINATE_SOURCE_EXT 0x8450<span class="kom">// Symbolick� konstanty pot�ebn� pro roz���en� FogCoordfEXT</span></p>
<p class="src0">#define GL_FOG_COORDINATE_EXT 0x8451</p>

<p>Abychom mohli pou��vat funkci glFogCoordfExt(), kter� bude vstupn�m bodem pro roz���en�, pot�ebujeme deklarovat jej� prototyp. Nejd��ve pomoc� typedef vytvo��me nov� datov� typ, ve kter�m bude specifikov�n po�et a typ parametr� (jedno desetinn� ��slo). Vytvo��me glob�ln� prom�nnou tohoto typu - ukazatel na funkci a prozat�m ho nastav�me na NULL. Jakmile mu p�i�ad�me pomoc� wglGetProcAddress() adresu OpenGL ovlada�e roz���en�, budeme moci zavolat glFogCoordfEXT(), jako kdyby to byla norm�ln� funkce.</p>

<p> Tak�e co u� m�me... V�me, �e PFNGLFOGCOORDFEXTPROC p�eb�r� jednu desetinnou hodnotu (GLfloat coord). Proto�e je prom�nn� glFogCoordfEXT stejn�ho typu m��eme ��ct, �e tak� pot�ebuje jednu desetinnou hodnotu... tedy glFogCoordfEXT(GLfloat coord). Funkci m�me definovanou, ale zat�m nic ned�l�, proto�e glFogCoordfEXT se v tuto chv�li rovn� NULL. D�le v k�du j� p�i�ad�me adresu OpenGL ovlada�e pro roz���en�.</p>

<p>Douf�m, �e to v�echno d�v� smysl. Pokud jednou v�te, jak tento k�d pracuje, je velmi jednoduch�, ale jeho pops�n� je, alespo� pro m�, extr�mn� slo�it�.</p>

<p class="src0">typedef void (APIENTRY * PFNGLFOGCOORDFEXTPROC) (GLfloat coord);<span class="kom">// Funk�n� prototyp</span></p>
<p class="src0">PFNGLFOGCOORDFEXTPROC glFogCoordfEXT = NULL;<span class="kom">// Ukazatel na funkci glFogCoordfEXT()</span></p>
<p class="src"></p>
<p class="src0">GLuint texture[1];<span class="kom">// Jedna textura</span></p>

<p>Poj�me se pod�vat na p�evod obr�zk� do textury pomoc� magick� IPicture. Funkci se p�ed�v� �et�zec se jm�nem obr�zku a ID textury. Za jm�no se m��e dosadit bu� diskov� cesta nebo webov� URL.</p>

<p>Pro pomocnou bitmapu budeme pot�ebovat kontext za��zen� (hdcTemp) a m�sto, kam by se dala ulo�it (hbmpTemp). Ukazatel pPicture p�edstavuje rozhran� k IPicture. WszPath a szPath slou�� k ulo�en� absolutn� cesty k souboru nebo URL. D�le pot�ebujeme dv� prom�nn� pro ���ku a dv� prom�nn� pro v��ku. LWidth a LHeight ukl�daj� aktu�ln� rozm�ry obr�zku, lWidthpixels a lHeightpixels obsahuj� ���ku a v��ku v pixelech upravenou podle maxim�ln� velikosti textury, kter� m��e b�t ulo�ena do grafick� karty. Hodnotu maxim�ln� velikosti ulo��me do glMaxTexdim.</p>

<p class="src0">int BuildTexture(char *szPathName, GLuint &amp;texid)<span class="kom">// Nahraje obr�zek a konvertuje ho na texturu</span></p>
<p class="src0">{</p>
<p class="src1">HDC hdcTemp;<span class="kom">// Pomocn� kontext za��zen�</span></p>
<p class="src1">HBITMAP hbmpTemp;<span class="kom">// Pomocn� bitmapa</span></p>
<p class="src1">IPicture *pPicture;<span class="kom">// Rozhran� pro IPicture</span></p>
<p class="src1">OLECHAR wszPath[MAX_PATH+1];<span class="kom">// Absolutn� cesta k obr�zku (unicode)</span></p>
<p class="src1">char szPath[MAX_PATH+1];<span class="kom">// Absolutn� cesta k obr�zku (ascii)</span></p>
<p class="src1">long lWidth;<span class="kom">// ���ka v logick�ch jednotk�ch</span></p>
<p class="src1">long lHeight;<span class="kom">// V��ka v logick�ch jednotk�ch</span></p>
<p class="src1">long lWidthPixels;<span class="kom">// ���ka v pixelech</span></p>
<p class="src1">long lHeightPixels;<span class="kom">// V��ka v pixelech</span></p>
<p class="src1">GLint glMaxTexDim;<span class="kom">// Maxim�ln� rozm�r textury</span></p>

<p>V dal�� ��sti k�du zjist�me, zda je jm�no obr�zku diskovou cestou nebo URL. Jedn�-li se o URL, zkop�rujeme jm�no do prom�nn� szPath. V opa�n�m p��pad� z�sk�me pracovn� adres�� a spoj�me ho se jm�nem. D�l�me to, proto�e pot�ebujeme plnou cestu k souboru. Pokud m�me nap�. demo ulo�en� v adres��i C:\WOW\LESSON41 a pokou��me se nahr�t obr�zek DATA\WALL.BMP. Uveden� konstrukce p�id� doprost�ed je�t� zp�tn� lom�tko a tak vznikne C:\WOW\LESSON41\DATA\WALL.BMP.</p>

<p class="src1">if (strstr(szPathName, &quot;http://&quot;))<span class="kom">// Obsahuje cesta �et�zec &quot;http://&quot;?</span></p>
<p class="src1">{</p>
<p class="src2">strcpy(szPath, szPathName);<span class="kom">// Zkop�rov�n� do szPath</span></p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Nahr�v�n� ze souboru</span></p>
<p class="src1">{</p>
<p class="src2">GetCurrentDirectory(MAX_PATH, szPath);<span class="kom">// Pracovn� adres��</span></p>
<p class="src2">strcat(szPath, &quot;\\&quot;);<span class="kom">// P�id� zp�tn� lom�tko</span></p>
<p class="src2">strcat(szPath, szPathName);<span class="kom">// P�id� cestu k souboru</span></p>
<p class="src1">}</p>

<p>Aby funkce OleLoadPicturePath() rozum�la cest� k souboru, mus�me ji p�ev�st z ASCII do k�dov�n� UNICODE (dvoubytov� znaky). Pom��e n�m s t�m MultiByteToWideChar(). Prvn� parametr, CP_ACP, znamen� Ansi Codepage, druh� specifikuje zach�zen� s nenamapovan�mi znaky (ignorujeme ho). SzPath je samoz�ejm� p�ev�d�n� �et�zec a �tvrt� parametr p�edstavuje ���ku �et�zce s Unicode znaky. Pokud za n�j p�ed�me -1, p�edpokl�d� se, �e bude ukon�en pomoc� NULL. Do wszPath se ulo�� v�sledek, MAX_PATH je maxim�ln� velikost� cesty k souboru (256 znak�).</p>

<p>Po konverzi cesty do k�dov�n� Unicode se pokus�me pomoc� OleLoadPicturePath nahr�t obr�zek. P�i �sp�chu bude pPicture obsahovat ukazatel na data obr�zku, n�vratov� k�d se ulo�� do hr.</p>

<p class="src1">MultiByteToWideChar(CP_ACP, 0, szPath, -1, wszPath, MAX_PATH);<span class="kom">// Konverze ascii k�dov�n� na Unicode</span></p>
<p class="src1">HRESULT hr = OleLoadPicturePath(wszPath, 0, 0, 0, IID_IPicture, (void**)&amp;pPicture);<span class="kom">// Loading obr�zku</span></p>
<p class="src"></p>
<p class="src1">if(FAILED(hr))<span class="kom">// Ne�sp�ch</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>

<p>Pokus�me se vytvo�it kompatibiln� kontext za��zen�. Pokud se to nepovede uvoln�me data obr�zku a ukon��me program.</p>

<p class="src1">hdcTemp = CreateCompatibleDC(GetDC(0));<span class="kom">// Pomocn� kontext za��zen�</span></p>
<p class="src"></p>
<p class="src1">if(!hdcTemp)<span class="kom">// Ne�sp�ch</span></p>
<p class="src1">{</p>
<p class="src2">pPicture-&gt;Release();<span class="kom">// Uvoln� IPicture</span></p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>

<p>P�i�el �as na polo�en� dotazu grafick� kart�, jakou podporuje maxim�ln� velikost textury. Tato ��st k�du je d�le�it�, proto�e d�ky n� bude obr�zek vypadat dob�e na v�ech grafick�ch kart�ch. Nejen, �e umo�n� upravit velikost na mocninou dvou, ale tak� ho p�izp�sob� podle velikosti pam�ti grafick� karty. Zkr�tka: budeme moci nahr�vat obr�zky s libovolnou ���kou a v��kou. Jedin� nev�hoda pro majitele m�lo v�konn�ch grafick�ch karet spo��v� v tom, �e se p�i zobrazen� obr�zk� s vysok�m rozli�en�m ztrat� spousta detail�.</p>

<p>Funkce glGetIntegerv() vr�t� maxim�ln� rozm�ry textur (256, 512, 1024, atd.), potom zjist�me aktu�ln� velikost na�eho obr�zku a p�evedeme ji na pixely. Matematiku zde nebudu vysv�tlovat.</p>

<p class="src1">glGetIntegerv(GL_MAX_TEXTURE_SIZE, &amp;glMaxTexDim);<span class="kom">// Maxim�ln� podporovan� velikost textury</span></p>
<p class="src"></p>
<p class="src1">pPicture-&gt;get_Width(&amp;lWidth);<span class="kom">// ���ka obr�zku a konvertov�n� na pixely</span></p>
<p class="src1">lWidthPixels = MulDiv(lWidth, GetDeviceCaps(hdcTemp, LOGPIXELSX), 2540);</p>
<p class="src"></p>
<p class="src1">pPicture-&gt;get_Height(&amp;lHeight);<span class="kom">// V��ka obr�zku a konvertov�n� na pixely</span></p>
<p class="src1">lHeightPixels = MulDiv(lHeight, GetDeviceCaps(hdcTemp, LOGPIXELSY), 2540);</p>

<p>Pokud je velikost obr�zku men�� ne� maxim�ln� podporovan�, zm�n�me velikost na mocninu dvou, kter� ale bude zalo�en� na aktu�ln� velikosti. P�i�teme 0.5f, tak�e se bude v�dy zv�t�ovat na n�sleduj�c� velikost. Nap��klad rovn�-li se ���ka 400 pixel�m a karta podporuje maxim�ln� 512, bude lep�� zvolit 512 ne� 256, proto�e by se zbyte�n� zahodily detaily. Naopak p�i v�t�� velikosti ne� maxim�ln� mus�me zmen�ovat na podporovanou velikost. Tot� plat� i pro v��ku.</p>

<p>P�ekl.: Opravte m�, jestli se m�l�m. Co se stane kdy� nap�. vezmu obr�zek, kter� m� ���ku 80 a v��ku 300 pixel�? T� matematice sice moc nerozum�m :-), ale z toho, co je zde uvedeno, logicky vych�z�, �e vznikne obd�ln�kov� (ne �tvercov�!) obr�zek o rozm�rech 128x512 pixel�. Mo�n� by bylo vhodn� je�t� p�idat n�co ve stylu: pokud je jeden rozm�r men�� ne� druh�, uprav hodnoty na �tverec.</p>

<p class="src1">if (lWidthPixels &lt;= glMaxTexDim)<span class="kom">// Je ���ka men�� nebo stejn� ne� maxim�ln� podporovan�</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Zm�na velikosti na nejbli��� mocninu dvou</span></p>
<p class="src2">lWidthPixels = 1 &lt;&lt; (int)floor((log((double)lWidthPixels)/log(2.0f)) + 0.5f);</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Bude se zmen�ovat na maxim�ln� velikost</span></p>
<p class="src1">{</p>
<p class="src2">lWidthPixels = glMaxTexDim;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (lHeightPixels &lt;= glMaxTexDim)<span class="kom">// Je v��ka men�� nebo stejn� ne� maxim�ln� podporovan�</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Zm�na velikosti na nejbli��� mocninu dvou</span></p>
<p class="src2">lHeightPixels = 1 &lt;&lt; (int)floor((log((double)lHeightPixels)/log(2.0f)) + 0.5f);</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Bude se zmen�ovat na maxim�ln� velikost</span></p>
<p class="src1">{</p>
<p class="src2">lHeightPixels = glMaxTexDim;</p>
<p class="src1">}</p>

<p>V tuto chv�li m�me data nahran� a tak� zn�me po�adovanou velikost obr�zku, abychom ho mohli d�le upravovat, mus�me vytvo�it pomocnou bitmapu. Bi bude obsahovat informace o hlavi�ce a pBits bude ukazovat na data obr�zku. Po�adujeme barevnou hloubku 32 bit� na pixel, spr�vnou ���ku i v��ku v k�dov�n� RGB s jednou bitplane.</p>

<p class="src1"><span class="kom">// Pomocn� bitmapa</span></p>
<p class="src1">BITMAPINFO bi = {0};<span class="kom">// Typ bitmapy</span></p>
<p class="src1">DWORD *pBits = 0;<span class="kom">// Ukazatel na data bitmapy</span></p>
<p class="src"></p>
<p class="src1">bi.bmiHeader.biSize = sizeof(BITMAPINFOHEADER);<span class="kom">// Velikost struktury</span></p>
<p class="src1">bi.bmiHeader.biBitCount = 32;<span class="kom">// 32 bit�</span></p>
<p class="src1">bi.bmiHeader.biWidth = lWidthPixels;<span class="kom">// ���ka</span></p>
<p class="src1">bi.bmiHeader.biHeight = lHeightPixels;<span class="kom">// V��ka</span></p>
<p class="src1">bi.bmiHeader.biCompression = BI_RGB;<span class="kom">// RGB form�t</span></p>
<p class="src1">bi.bmiHeader.biPlanes = 1;<span class="kom">// 1 Bitplane</span></p>

<p>P�evzato z MSDN: Funkce CreateDIBSection() vytv��� DIB, do kter�ho m��e aplikace p��mo zapisovat. Vrac� ukazatel na um�st�n� bit� bitmapy, m��eme tak� nechat syst�m alokovat pam�.</p>

<p>HdcTemp ukl�d� pomocn� kontext za��zen�, bi je hlavi�ka bitmapy. DIB_RGB_COLORS ��k� programu, �e chceme ulo�it RGB data, kter� nebudou indexov�na do logick� palety (ka�d� pixel bude m�t �ervenou, zelenou a modrou slo�ku). Ukazatel pBits bude obsahovat adresu v�sledn�ch dat a posledn� dva parametry budeme ignorovat. Pokud nenastane ��dn� chyba, pomoc� Selectobject() p�ipoj�me bitmapu k pomocn�mu kontextu za��zen�.</p>

<p class="src1"><span class="kom">// Touto cestou je mo�n� specifikovat barevnou hloubku a z�skat p��stup k dat�m</span></p>
<p class="src1">hbmpTemp = CreateDIBSection(hdcTemp, &amp;bi, DIB_RGB_COLORS, (void**)&amp;pBits, 0, 0);</p>
<p class="src"></p>
<p class="src1">if(!hbmpTemp)<span class="kom">// Ne�sp�ch</span></p>
<p class="src1">{</p>
<p class="src2">DeleteDC(hdcTemp);<span class="kom">// Uvoln�n� kontextu za��zen�</span></p>
<p class="src2">pPicture-&gt;Release();<span class="kom">// Uvoln� IPicture</span></p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SelectObject(hdcTemp, hbmpTemp);<span class="kom">// Zvol� bitmapu do kontextu za��zen�</span></p>

<p>Nastal �as pro vypln�n� pomocn� bitmapy daty obr�zku. Funkce pPicture->Render() to ud�l� za n�s a nav�c uprav� obr�zek na libovolnou velikost, kterou pot�ebujeme. HdcTemp p�edstavuje pomocn� kontext za��zen� a dal�� dva n�sleduj�c� parametry specifikuj� vertik�ln� a horizont�ln� offset (po�et pr�zdn�ch pixel� zleva a seshora). My chceme, aby byla cel� bitmapa kompletn� vypln�na, tak�e zad�me dv� nuly. Dal�� dva parametry ur�uj� po�adovanou velikost v�sledn�ho obr�zku (na kolik pixel� se m� rozt�hnout pop�. zmen�it). Nula na dal��m m�st� je horizont�ln� offset ve zdrojov�ch datech, od kter�ho chceme za��t ��st, z �eho� plyne, �e p�jdeme zleva doprava. LHeight ur�uje vertik�ln� offset, data chceme ��st od zdola nahoru. Zad�n�m lHeight se p�esuneme na sam� dno zdrojov�ho obr�zku. LWidth je mno�stv�m pixel�, kter� se budou kop�rovat ze zdrojov�ho obr�zku, v na�em p��pad� se jedn� o v�echna horizont�ln� data. P�edposledn� parametr, trochu odli�n�, m� z�pornou hodnotu, z�porn� lHeight, abychom byli p�esn�. Ve v�sledku to znamen�, �e chceme zkop�rovat v�echna vertik�ln� data, ale od zdola nahoru. Touto cestou bude p�i kop�rov�n� do c�lov� bitmapy p�evr�cen. Posledn� parametr nepou�ijeme.</p>

<p class="src1"><span class="kom">// Vykreslen� IPicture do bitmapy</span></p>
<p class="src1">pPicture-&gt;Render(hdcTemp, 0, 0, lWidthPixels, lHeightPixels, 0, lHeight, lWidth, -lHeight, 0);</p>

<p>Nyn� m�me k dispozici novou bitmapu se spr�vn�mi rozm�ry, ale bohu�el je ulo�ena ve form�tu BGR. (P�ekl.: Pro� tomu tak je, bylo vysv�tlov�no v 35. tutori�lu na p�ehr�v�n� AVI videa.) Pomoc� jednoduch�ho cyklu tyto dv� slo�ky prohod�me a z�rove� nastav�me alfu na 255. D� se ��ci, �e jak�koli jin� hodnota stejn� nebude m�t nejmen�� efekt, proto�e alfu ignorujeme.</p>

<p class="src1"><span class="kom">// Konverze BGR na RGB</span></p>
<p class="src1">for(long i = 0; i &lt; lWidthPixels * lHeightPixels; i++)<span class="kom">// Cyklus p�es v�echny pixely</span></p>
<p class="src1">{</p>
<p class="src2">BYTE* pPixel = (BYTE*)(&amp;pBits[i]);<span class="kom">// Aktu�ln� pixel</span></p>
<p class="src2">BYTE  temp = pPixel[0];<span class="kom">// Modr� slo�ka do pomocn� prom�nn�</span></p>
<p class="src2">pPixel[0] = pPixel[2];<span class="kom">// Ulo�en� �erven� slo�ky na spr�vnou pozici</span></p>
<p class="src2">pPixel[2] = temp;<span class="kom">// Vlo�en� modr� slo�ky na spr�vnou pozici</span></p>
<p class="src2">pPixel[3] = 255;<span class="kom">// Konstantn� alfa hodnota</span></p>
<p class="src1">}</p>

<p>Po v�ech nutn�ch operac�ch m��eme z obr�zku vygenerovat texturu. Zvol�me ji jako aktivn� a nastav�me line�rn� filtrov�n�. Mysl�m, �e glTexImage2D() u� nemus�m vysv�tlovat.</p>

<p class="src1">glGenTextures(1, &amp;texid);<span class="kom">// Generov�n� jedn� textury</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texid);<span class="kom">// Zvol� texturu</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER, GL_LINEAR);<span class="kom">// Line�rn� filtrov�n�</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER, GL_LINEAR);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vytvo�en� textury</span></p>
<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, 3, lWidthPixels, lHeightPixels, 0, GL_RGBA, GL_UNSIGNED_BYTE, pBits);</p>

<p>Pot�, co je textura vytvo�ena, m��eme uvolnit zabran� syst�mov� zdroje. U� nebudeme pot�ebovat pomocnou ani bitmapu ani kontext za��zen� ani pPicture.</p>

<p class="src1">DeleteObject(hbmpTemp);<span class="kom">// Sma�e bitmapu</span></p>
<p class="src1">DeleteDC(hdcTemp);<span class="kom">// Sma�e kontext za��zen�</span></p>
<p class="src1">pPicture-&gt;Release();<span class="kom">// Uvoln� IPicture</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// OK</span></p>
<p class="src0">}</p>

<p>N�sleduj�c� funkce zji��uje, jestli grafick� karta podporuje roz���en� EXT_fog_coord. Tento k�d m��e b�t pou�it pouze, pokud u� m� program k dispozici renderovac� kontext. Jestli�e ho zkus�me zavolat p�ed inicializac� okna, dostaneme chyby.</p>

<p>Vytvo��me pole obsahuj�c� jm�no na�eho roz���en�. Alokujeme dynamickou pam�, do kter� n�sledn� zkop�rujeme seznam v�ech podporovan�ch roz���en�. Pokud strstr() mezi nimi najde EXT_fog_coord, vr�t�me false. (P�ekl.: Uvolnit dynamickou pam�!!!)</p>

<p class="src0">int Extension_Init()<span class="kom">// Je roz���en� EXT_fog_coord podporov�no?</span></p>
<p class="src0">{</p>
<p class="src1">char Extension_Name[] = &quot;EXT_fog_coord&quot;;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Alokace pam�ti pro �et�zec</span></p>
<p class="src1">char* glextstring = (char *)malloc(strlen((char *)glGetString(GL_EXTENSIONS)) + 1);</p>
<p class="src1">strcpy (glextstring,(char *)glGetString(GL_EXTENSIONS));<span class="kom">// Grabov�n� seznamu podporovan�ch roz���en�</span></p>
<p class="src"></p>
<p class="src1">if (!strstr(glextstring, Extension_Name))<span class="kom">// Nen� podporov�no?</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// free(glextstring);// P�ekl.: Uvoln�n� alokovan� pam�ti !!!</span></p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">free(glextstring);<span class="kom">// Uvoln�n� alokovan� pam�ti</span></p>

<p>Na sam�m za��tku programu jsme deklarovali prom�nnou glFogCoordfEXT jako ukazatel na funkci. Proto�e u� s jistotou v�me, �e grafick� karta toto roz���en� podporuje, m��eme ho pomoc� wglGetProcAddress() nastavit na spr�vnou adresu. Od t�to chv�le m�me k dispozici novou funkci glFogCoordfEXT(), kter� se p�ed�v� jedna GLfloat hodnota.</p>

<p class="src1">glFogCoordfEXT = (PFNGLFOGCOORDFEXTPROC) wglGetProcAddress(&quot;glFogCoordfEXT&quot;);<span class="kom">// Nastav� ukazatel na funkci</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// OK</span></p>
<p class="src0">}</p>

<p>P�i vstupu do Initialize() m� program k dispozici renderovac� kontext, tak�e se m��eme dot�zat na podporu roz���en�. Pokud nen� dostupn�, ukon��me program. Texturu nahr�v�me pomoc� nov�ho IPicture k�du. Pokud se z n�jak�ho d�vodu loading nezda��, op�t ukon��me program. N�sleduje obvykl� inicializace OpenGL.</p>

<p class="src0">BOOL Initialize(GL_Window* window, Keys* keys)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;<span class="kom">// Okno</span></p>
<p class="src1">g_keys = keys;<span class="kom">// Kl�vesnice</span></p>
<p class="src"></p>
<p class="src1">if (!Extension_Init())<span class="kom">// Je roz���en� podporov�no?</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!BuildTexture(&quot;data/wall.bmp&quot;, texture[0]))<span class="kom">// Nahr�n� textury</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testov�n� hloubky</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>

<p>D�le pot�ebujeme nastavit mlhu. Nejd��ve ji zapneme, potom ur��me line�rn� renderovac� m�d (vypad� l�pe) a definujeme barvu na tmav�� odst�n oran�ov�. Startovn� pozice mlhy je m�sto, kde bude nejm�n� hust�. Abychom udr�eli v�ci jednoduch� p�ed�me ��slo 0.0f. Naopak nejv�ce hust� bude s hodnotou 1.0f. Podle v�ech dokumentac�, kter� jsem kdy �etl, nastaven� hintu na GL_NICEST zp�sob�, �e se bude p�sob� mlhy ur�ovat zvlṻ pro ka�d� pixel. P�ed�te-li GL_FASTEST, bude se po��tat pro jednotliv� vertexy, nicm�n� nejde vid�t ��dn� rozd�l. Posledn� glFogi() p��kaz ozn�m� OpenGL, �e chceme nastavovat mlhu v z�vislosti na koordin�tech vertex�. To zp�sob�, �e ji budeme moci um�stit kamkoli na sc�nu bez toho, �e bychom tak ovlivnili jej� zbytek.</p>

<p class="src1"><span class="kom">// Nastaven� mlhy</span></p>
<p class="src1">glEnable(GL_FOG);<span class="kom">// Zapne mlhu</span></p>
<p class="src1">glFogi(GL_FOG_MODE, GL_LINEAR);<span class="kom">// Line�rn� p�echody</span></p>
<p class="src1">glFogfv(GL_FOG_COLOR, fogColor);<span class="kom">// Barva</span></p>
<p class="src1">glFogf(GL_FOG_START, 0.0f);<span class="kom">// Po��tek</span></p>
<p class="src1">glFogf(GL_FOG_END, 1.0f);<span class="kom">// Konec</span></p>
<p class="src1">glHint(GL_FOG_HINT, GL_NICEST);<span class="kom">// V�po�ty na jednotliv�ch pixelech</span></p>
<p class="src1">glFogi(GL_FOG_COORDINATE_SOURCE_EXT, GL_FOG_COORDINATE_EXT);<span class="kom">// Mlha v z�vislosti na sou�adnic�ch vertex�</span></p>

<p>Po��te�n� hodnotu prom�nn� camz ur��me na -19.0f. Proto�e chodbu renderujeme od -19.0f do +14.0f, bude to p�esn� na za��tku.</p>

<p class="src1">camz = -19.0f;<span class="kom">// Pozice kamery</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// OK</span></p>
<p class="src0">}</p>

<p>Funkce zaji��uj�c� stisky kl�ves je dnes opravdu jednoduch�. Pomoc� �ipek nahoru a dol� nastavujeme pozici kamery ve sc�n�. Z�rove� mus�me o�et�it &quot;p�ete�en�&quot;, abychom se neocitli venku z chodby.</p>

<p class="src0">void Update(DWORD milliseconds)<span class="kom">// Aktualizace sc�ny</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown[VK_ESCAPE])<span class="kom">// ESC</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication(g_window);<span class="kom">// Konec programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_F1])<span class="kom">// F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// Zm�na fullscreen/okno</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_UP] &amp;&amp; camz &lt; 14.0f)<span class="kom">// �ipka nahoru</span></p>
<p class="src1">{</p>
<p class="src2">camz+=(float)(milliseconds) / 100.0f;<span class="kom">// Pohyb dop�edu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_DOWN] &amp;&amp; camz &gt; -19.0f)<span class="kom">// �ipka dol�</span></p>
<p class="src1">{</p>
<p class="src2">camz-=(float)(milliseconds) / 100.0f;<span class="kom">// Pohyb dozadu</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jsem si jist�, �e u� netrp�liv� �ek�te na vykreslov�n�. Sma�eme buffery, resetujeme matici a v z�vislosti na hodnot� camz se p�esuneme do hloubky.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f, 0.0f, camz);<span class="kom">// Translace v hloubce</span></p>

<p>Kamera je um�st�na, tak�e zkus�me vykreslit prvn� quad. Bude j�m zadn� st�na, kter� by m�la b�t kompletn� pono�en� v mlze. Z inicializace si jist� pamatujete, �e nejhust�� mlhu nastavuje hodnota GL_FOG_END; ur�ili jsme ji na 1.0f. Mlha se aplikuje podobn� jako texturov� koordin�ty, pro nejmen�� viditelnost p�ed�me funkci glFogCoordfEXT() ��slo 1.0f a pro nejv�t�� 0.0f. Zadn� st�na je kompletn� pono�en� v mlze, tak�e p�ed�me v�em jej�m vertex�m jedni�ku.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zadn� st�na</span></p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f(-2.5f,-2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f( 2.5f,-2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f( 2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f(-2.5f, 2.5f,-15.0f);</p>
<p class="src1">glEnd();</p>

<p>Prvn� dva body podlahy navazuj� na vertexy zadn� st�ny, a proto tak� zde uvedeme 1.0f. P�edn� body jsou u� naopak z mlhy venku, tud� je mus�me nastavit na 0.0f. M�sta le��c� mezi okraji se automaticky interpoluj�, a tak vznikne plynul� p�echod. V�echny ostatn� st�ny budou analogick�.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Podlaha</span></p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f(-2.5f,-2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f( 2.5f,-2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f( 2.5f,-2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f(-2.5f,-2.5f, 15.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Strop</span></p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f(-2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f( 2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f( 2.5f, 2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f(-2.5f, 2.5f, 15.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Prav� st�na</span></p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f( 2.5f,-2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f( 2.5f, 2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f( 2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f( 2.5f,-2.5f,-15.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Lev� st�na</span></p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f(-2.5f,-2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f(-2.5f, 2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f(-2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f(-2.5f,-2.5f,-15.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vypr�zdn�n� renderovac� pipeline</span></p>
<p class="src0">}</p>

<p>Douf�m, �e nyn� u� rozum�te, jak v�ci pracuj�. ��m vzd�len�j�� je objekt, t�m by m�l b�t v�ce pono�en v mlze a tud� mus� b�t nastavena hodnota 1.0f. V�dycky si tak� m��ete pohr�t s GL_FOG_START a GL_FOG_END a pozorovat, jak ovliv�uj� sc�nu. Efekt nebude pracovat podle o�ek�v�n�, pokud prohod�te hodnoty. Iluze se vytvo�ila t�m, �e je zadn� st�na kompletn� oran�ov�. nejv�hodn�j�� pou�it� spo��v� u temn�ch kout�, kde se hr�� nem��e dostat za mlhu.</p>

<p>Pl�nujete-li tento typ mlhy ve sv�m 3D enginu, bude mo�n� vhodn� upravovat po��te�n� a koncov� hodnoty podle toho, kde hr�� stoj�, kter�m sm�rem se d�v� a podobn�.</p>

<p>Douf�m, �e jste si u�ili tento tutori�l. Vytv��el jsem ho p�es t�i dny, �ty�i hodiny denn�. V�t�inu �asu zabralo psan� text�, kter� pr�v� �tete. P�vodn� jsme cht�l vytvo�it kompletn� 3D m�stnost s mlhou v jednom rohu, ale nane�t�st� jsem m�l velmi m�lo �asu na k�dov�n�. P�esto�e zaml�en� chodba je velmi jednoduch�, vypad� perfektn� a modifikace k�du pro v� projekt by tak� nem�la b�t moc slo�it�.</p>

<p>Je d�le�it� poznamenat, �e toto je pouze jednou z nejr�zn�j��ch mo�nost�, jak vytvo�it volumetrickou mlhu. Podobn� efekt m��e b�t naprogramov�n pomoc� blendingu, ��sticov�ch syst�m�, maskov�n� a podobn�ch technologi�. Pokud modifikujete pohled na sc�nu tak, aby byla kamera um�st�na ne v chodb�, ale venku, zjist�te, �e se mlha nach�z� uvnit� chodby.</p>

<p>Origin�ln� my�lenka tohoto tutori�lu ke mn� dorazila u� hodn� d�vno, co� je jedn�m z d�vod�, �e jsem ztratil email. Osob�, kter� mi n�pad zaslala, d�kuji.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson41.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson41_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson41.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson41.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:rdieffenbach@chello.nl">Rob Dieffenbach</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson41.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:ant@solace.mh.se">Anthony Whitehead</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson41.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(41);?>
<?FceNeHeOkolniLekce(41);?>

<?
include 'p_end.php';
?>
