<?
$g_title = 'CZ NeHe OpenGL - Lekce 1 - Vytvo�en� OpenGL okna ve Windows';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(1);?>

<h1>Lekce 1 - Vytvo�en� OpenGL okna ve Windows</h1>

<p class="nadpis_clanku">Nau��te se jak nastavit a vytvo�it OpenGL okno ve Windows. Program, kter� vytvo��te zobraz� "pouze" pr�zdn� okno. �ern� pozad� nevypad� nic moc, ale pokud porozum�te t�to lekci, budete m�t velmi dobr� z�klad pro jakoukoliv dal�� pr�ci. Zjist�te jak OpenGL pracuje, jak prob�h� vytv��en� okna a tak� jak napsat jednodu�e pochopiteln� k�d.</p>

<p>Jsem oby�ejn� kluk s v�n� pro OpenGL. Kdy� jsem o n�m poprv� sly�el, vydalo 3Dfx zrychlen� ovlada�e pro Voodoo 1. Hned jsem v�d�l, �e OpenGL je n�co, co se mus�m nau�it. Bohu�el bylo velice t�k� naj�t n�jak� informace, jak v knih�ch, tak na internetu. Str�vil jsem hodiny pokusy o naps�n� funk�n�ho k�du a p�esv�d�ov�n�m lid� emaily a na IRC. Zjistil jsem, �e lid�, kte�� rozum�li OpenGL, se pova�ovali za elitu a nehodlali se o sv� v�domosti d�lit. Velice frustruj�c�... Vytvo�il jsem tyto tutori�ly, aby je z�jemci o OpenGL mohli pou��t, kdy� budou pot�ebovat pomoc. V ka�d�m tutori�lu se v�e sna��m vysv�tlit do detail�, aby bylo jasn�, co ka�d� ��dek d�l�. Sna��m se sv�j k�d ps�t co nejjednodu�eji (nepou��v�m MFC)! I absolutn� nov��ek, jak v C++, tak v OpenGL, by m�l b�t schopen tento k�d zvl�dnout a m�t dal�� dobr� n�pady, co d�lat d�l. Je mnoho tutori�l� o OpenGL. Pokud jste hardcorov� OpenGL program�tor asi V�m budou p�ipadat p��li� jednoduch�, ale pokud pr�v� za��n�te maj� mnoho co nab�dnout!</p>

<p>Za�nu tento tutori�l p��mo k�dem. Prvn�, co se mus� ud�lat, je vytvo�it projekt. Pokud nev�te jak to ud�lat, nem�li byste se u�it OpenGL, ale Visual C++. N�kter� verze Visual C++ vy�aduj�, aby byl bool zm�n�n na BOOL, true na TRUE a false na FALSE. Pokud to budete m�t na pam�ti nem�ly by b�t s kompilac� ��dn� probl�my. Potom co vytvo��te novou Win32 Application (NE console application) ve Visual C++, budete pot�ebovat p�ipojit OpenGL knihovny. Jsou dv� mo�nosti, jak to ud�lat: Vyberte Project>Settings, pak zvolte z�lo�ku Link a do kolonky Object/Library Modules napi�te na za��tek ��dku (p�ed kernel32.lib) OpenGL32.lib Glu32.lib Glaux.lib. Potom klikn�te na OK. Nebo napi�te p��mo do k�du programu n�sleduj�c� ��dky.</p>

<p class="src0"><span class="kom">// Vlo�en� knihoven</span></p>
<p class="src0">#pragma comment (lib,&quot;opengl32.lib&quot;)</p>
<p class="src0">#pragma comment (lib,&quot;glu32.lib&quot;)</p>
<p class="src0">#pragma comment (lib,&quot;glaux.lib&quot;)</p>

<p>Nyn� jste p�ipraveni napsat sv�j prvn� OpenGL program pro Windows. Za�neme vlo�en�m hlavi�kov�ch soubor�.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glaux knihovnu</span></p>

<p>D�le pot�ebujete deklarovat glob�ln� prom�nn�, kter� chcete v programu pou��t. Tento program vytv��� pr�zdn� OpenGL okno, proto jich nebudeme pot�ebovat mnoho. Ty, kter� nyn� pou�ijeme jsou ov�em velmi d�le�it� a budete je pou��vat v ka�d�m programu zalo�en�m na tomto k�du. Nastav�me Rendering Context. Ka�d� OpenGL program je spojen s Rendering Contextem. Rendering Context ��k�, kter� spojen� vol� OpenGL, aby se spojilo s Device Context (kontext za��zen�). N�m sta�� v�d�t, �e OpenGL Rendering Context je definov�n jako hRC. Aby program mohl kreslit do okna pot�ebujete vytvo�it Device Context. Ve Windows je Device Context definov�n jako hDC. Device Context napoj� okno na GDI (grafick� rozhran�). Prom�nn� hWnd obsahuje handle p�id�len� oknu a �tvrt� ��dek vytvo�� instanci programu.</p>

<p class="src0">HDC hDC = NULL;<span class="kom">// Priv�tn� GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trval� Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na�eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>

<p>Prvn� ��dek deklaruje pole, kter� budeme pou��vat na sledov�n� stisknut�ch kl�ves. Je mnoho zp�sob�, jak to ud�lat, ale takto to d�l�m j�. Je to spolehliv� a m��eme sledovat stisk v�ce kl�ves najednou. Prom�nn� active bude pou�ita, aby n� program informovala, zda je jeho okno minimalizov�no nebo ne. Kdy� je okno minimalizov�no m��eme ud�lat cokoliv od pozastaven� �innosti k�du a� po opu�t�n� programu. J� pou�iji pozastaven� b�hu programu. D�ky tomu zbyte�n� nepob�� na pozad�, kdy� bude minimalizov�n. Prom�nn� fullscreen bude obsahovat informaci, jestli n� program b�� p�es celou obrazovku - v tom p��pad� bude fullscreen m�t hodnotu true, kdy� program pob�� v okn� bude m�t hodnotu false. Je d�le�it�, aby prom�nn� byla glob�ln� a t�m p�dem ka�d� funkce v�d�la, jestli program b�� ve fullscreenu, nebo v okn�.</p>

<p class="src0">bool keys[256];<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>
<p class="src"></p>
<p class="src0">LRESULT CALLBACK WndProc(HWND, UINT, WPARAM, LPARAM);<span class="kom">// Deklarace procedury okna (funk�n� prototyp)</span></p>

<p>N�sleduj�c� funkce se vol� v�dy, kdy� u�ivatel m�n� velikost okna. I kdy� nejste schopni zm�nit velikost okna (nap��klad ve fullscreenu), bude tato funkce vol�na alespo� jednou, aby nastavila perspektivn� pohled p�i spu�t�n� programu. Velikost OpenGL sc�ny se bude m�nit v z�vislosti na ���ce a v��ce okna, ve kter�m je zobrazena.</p>

<p class="src0">GLvoid ReSizeGLScene(GLsizei width, GLsizei height)<span class="kom">// Zm�na velikosti a inicializace OpenGL okna
</span></p>
<p class="src0">{</p>
<p class="src1">if (height==0)<span class="kom">// Zabezpe�en� proti d�len� nulou</span></p>
<p class="src1">{</p>
<p class="src2">height=1;<span class="kom">// Nastav� v��ku na jedna</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glViewport(0,0,width,height);<span class="kom">// Resetuje aktu�ln� nastaven�</span></p>

<p>Nastav�me obraz na perspektivn� pohled. To znamen�, �e vzd�len�j�� objekty budou men��. glMatrixMode(GL_PROJECTION) ovlivn� formu obrazu. Forma obrazu ur�uje, jak v�razn� bude perspektiva. Vytvo��me realisticky vypadaj�c� sc�nu. glLoadIdentity() resetuje matici. Vr�t� ji do jej�ho p�vodn�ho stavu. Po glLoadIdentity() nastav�me perspektivn� pohled sc�ny. Perspektiva je vypo��t�na s �hlem pohledu 45 stup�� a je zalo�ena na v��ce a ���ce okna. ��slo 0.1f je po��te�n� a 100.0f kone�n� bod, kter� ��k� jak hluboko do obrazovky m��eme kreslit. glMatrixMode(GL_MODELVIEW) oznamuje, �e forma pohledu bude znovu zm�n�na. Nakonec znovu resetujeme matici. Pokud p�edch�zej�c�mu textu nerozum�te, nic si z toho ned�lejte, vysv�tl�m ho cel� v dal��ch tutori�lech. Jedin� co nyn� mus�te v�d�t je, �e n�sleduj�c� ��dky mus�te do sv�ho programu napsat, pokud chcete, aby sc�na vypadala p�kn�.</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvol� projek�n� matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1"></p>
<p class="src1">gluPerspective(45.0f,(GLfloat)width/(GLfloat)height,0.1f,100.0f);<span class="kom">// V�po�et perspektivy</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvol� matici Modelview</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src0">}</p>

<p>Nastav�me v�e pot�ebn� pro OpenGL. Definujeme �ern� pozad�, zapneme depth buffer, aktivujeme smooth shading (vyhlazen� st�nov�n�), atd.. Tato funkce se vol� po vytvo�en� okna. Vrac� hodnotu, ale t�m se nyn� nemus�me zab�vat, proto�e na�e inicializace nen� zat�m �pln� komplexn�.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echno nastaven� OpenGL</span></p>
<p class="src0">{</p>

<p>N�sleduj�c� ��dek povol� jemn� st�nov�n�, aby se barvy na polygonech p�kn� prom�chaly. V�ce detail� o smooth shading si pov�me v jin�ch tutori�lech.</p>

<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povol� jemn� st�nov�n�</span></p>

<p>Nastav�me barvu pozad� pr�zdn� obrazovky. Rozsah barev se ur�uje ve stupnici od 0.0f do 1.0f. 0.0f je nejtmav�� a 1.0f je nejsv�tlej��. Prvn� parametr ve funkci glClearColor() je intenzita �erven� barvy, druh� zelen� a t�et� modr�. ��m bli��� je hodnota barvy 1.0f, t�m sv�tlej�� slo�ka barvy bude. Posledn� parametr je hodnota alpha (pr�hlednost). Kdy� budeme �istit obrazovku, tak se o pr�hlednost starat nemus�me. Nyn� ji nech�me na 0.0f. M��ete vytv��et r�zn� barvy kombinov�n�m sv�tlosti t�� z�kladn�ch barev (�erven�, zelen�, modr�). Pokud budete m�t glClearColor(0.0f,0.0f,1.0f,0.0f), bude obrazovka modr�. Kdy� budete m�t glClearColor(0.5f,0.0f,0.0f,0.0f), bude obrazovka st�edn� tmav� �erven�. Abyste ud�lali b�l� pozad� nastavte v�echny hodnoty na nejvy��� hodnotu (1.0f), pro �ern� pozad� zadejte pro v�echny slo�ky 0.0f.</p>

<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>

<p>N�sleduj�c� t�i ��dky ovliv�uj� depth buffer. Depth buffer si m��ete p�edstavit jako vrstvy/hladiny obrazovky. Obsahuje informace, o tom jak hluboko jsou zobrazovan� objekty. Tento program sice nebude deep buffer pou��vat (nic nevykreslujeme). Objekty se se�ad� tak, aby bli��� p�ekr�valy vzd�len�j��.</p>

<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� hloubkov� testov�n�</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>

<p>D�le ozn�m�me, �e chceme pou��t nejlep�� korekce perspektivy. Jen nepatrn� se sn�� v�kon, ale zlep�� se vzhled cel� sc�ny</p>

<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>

<p>Nakonec vr�t�me true. Kdy� budeme cht�t zjistit zda inicializace prob�hla bez probl�m�, m��eme zkontrolovat, zda funkce vr�tila hodnotu true nebo false. M��ete p�idat vlastn� k�d, kter� vr�t� false, kdy� se inicializace nezda�� - nap�. loading textur. Nyn� se t�m nebudeme d�le zab�vat.</p>

<p class="src1">return TRUE;<span class="kom">// Inicializace prob�hla v po��dku</span></p>
<p class="src0">}</p>

<p>Do t�to funkci um�st�me v�echno vykreslov�n�. N�sleduj�c� tutori�ly budou p�episovat p�edev��m tento a inicializa�n� k�d t�to lekce. ( Pokud ji� nyn� rozum�te z�klad�m OpenGL, m��ete si zde p�ipsat kreslen� z�kladn�ch tvar� (mezi glLoadIdentity() a return). Pokud jste nov��ek, tak po�kejte do dal��ho tutori�lu. Jedin� co nyn� ud�l�me, je vymaz�n� obrazovky na barvu, pro kterou jste se rozhodli, vyma�eme obsah hloubkov�ho bufferu a resetujeme sc�nu. Zat�m nebudeme nic kreslit. P��kaz return true n�m ��k�, �e p�i kreslen� nenastaly ��dn� probl�my. Pokud z n�jak�ho d�vodu chcete p�eru�it b�h programu, sta�� p�idat return false p�ed return true - to ��k� na�emu programu, �e kreslen� sc�ny selhalo a program se ukon��.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Sem m��ete kreslit</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Vykreslen� prob�hlo v po��dku</span></p>
<p class="src0">}</p>

<p>N�sleduj�c� ��st k�du je vol�na t�sn� p�ed koncem programu. �kolem funkce KillGLWindow() je uvoln�n� renderovac�ho kontextu, kontextu za��zen� a handle okna. P�idal jsem zde nezbytn� kontrolov�n� chyb. Kdy� nen� program schopen n�co uvolnit, ozn�m� chybu, kter� ��k� co selhalo. Usnadn� slo�it� hled�n� probl�m�.</p>

<p class="src0">GLvoid KillGLWindow(GLvoid)<span class="kom">// Zav�r�n� okna</span></p>
<p class="src0">{</p>

<p>Zjist�me zda je program ve fullscreenu. Pokud ano, tak ho p�epneme zp�t do syst�mu. Mohli bychom vypnout okno p�ed opu�t�n�m fullscreenu, ale na n�kter�ch grafick�ch kart�ch t�m zp�sob�me probl�my a syst�m by se mohl zhroutit.</p>

<p class="src1">if (fullscreen)<span class="kom">// Jsme ve fullscreenu?</span></p>
<p class="src1">{</p>

<p>K n�vratu do p�vodn�ho nastaven� syst�mu pou��v�me funkci ChangeDisplaySettings(NULL,0). Jako prvn� parametr zad�me NULL a jako druh� 0 - pou�ijeme hodnoty ulo�en� v registrech Windows (p�vodn� rozli�en�, barevnou hloubku, obnovovac� frekvenci, atd.). Po p�epnut� zviditeln�me kurzor.</p>

<p class="src2">ChangeDisplaySettings(NULL,0);<span class="kom">// P�epnut� do syst�mu</span></p>
<p class="src2">ShowCursor(TRUE);<span class="kom">// Zobraz� kurzor my�i</span></p>
<p class="src1">}</p>

<p>Zkontrolujeme zda m�me renderovac� kontext (hRC). Kdy� ne, program p�esko�� ��st k�du pod n�m, kter� kontroluje, zda m�me kontext za��zen�.</p>

<p class="src1">if (hRC)<span class="kom">// M�me rendering kontext?</span></p>
<p class="src1">{</p>

<p>Zjist�me, zda m��eme odpojit hRC od hDC. V�imn�te si, jak kontroluji chyby. Nejd��ve programu �eknu, a� odpoj� Rendering Context (s pou�it�m wglMakeCurrent(NULL,NULL)), pak zkontroluji zda akce byla �sp�n�. Takto d�m v�ce ��dku do jednoho.</p>

<p class="src2">if (!wglMakeCurrent(NULL,NULL))<span class="kom">// Jsme schopni odd�lit kontexty?</span></p>
<p class="src2">{</p>

<p>Pokud nejsme schopni uvolnit DC a RC, pou�ijeme zobraz�me zpr�vu, �e DC a RC nelze uvolnit. NULL v parametru znamen�, �e informa�n� okno nem� ��dn�ho rodi�e. Text ihned za NULL je text, kter� se vyp�e do zpr�vy. Dal�� parametr definuje text li�ty. Parametr MB_OK znamen�, �e chceme m�t na chybov� zpr�v� jen jedno tla��tko s n�pisem OK. MB_ICONINFORMATION zobraz� ikonu.</p>

<p class="src3">MessageBox(NULL,&quot;Release Of DC And RC Failed.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">}</p>

<p>Zkus�me vymazat Rendering Context. Pokud se pokus nezda��, op�t se zobraz� chybov� zpr�va. Nakonec nastav�me hRC a NULL.</p>

<p class="src2">if (!wglDeleteContext(hRC))<span class="kom">// Jsme schopni smazat RC?</span></p>
<p class="src2">{</p>
<p class="src3">MessageBox(NULL,&quot;Release Rendering Context Failed.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">}</p>
<p class="src2">hRC=NULL;<span class="kom">// Nastav� hRC na NULL</span></p>
<p class="src1">}</p>

<p>Zjist�me zda m� program kontext za��zen�. Kdy� ano odpoj�me ho. Pokud se odpojen� nezda��, zobraz� se chybov� zpr�va a hDC bude nastaven na NULL.</p>

<p class="src1">if (hDC &amp;&amp; !ReleaseDC(hWnd,hDC))<span class="kom">// Jsme schopni uvolnit DC</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Release Device Context Failed.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">hDC=NULL;<span class="kom">// Nastav� hDC na NULL</span></p>
<p class="src1">}</p>

<p>Nyn� zjist�me zda m�me handle okna a pokud ano pokus�me se odstranit okno pou�it�m funkce DestroyWindow(hWnd). Pokud se pokus nezda��, zobraz� se chybov� zpr�va a hWnd bude nastaveno na NULL.</p>

<p class="src1">if (hWnd &amp;&amp; !DestroyWindow(hWnd))<span class="kom">// Jsme schopni odstranit okno?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Could Not Release hWnd.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">hWnd=NULL;<span class="kom">// Nastav� hWnd na NULL</span></p>
<p class="src1">}</p>

<p>Odregistrov�n�m  t��dy okna ofici�ln� uzav�eme okno a p�edejdeme zobrazen� chybov� zpr�vy &quot;Windows Class already registered&quot; p�i op�tovn�m spu�t�n� programu.</p>

<p class="src1">if (!UnregisterClass(&quot;OpenGL&quot;,hInstance))<span class="kom">// Jsme schopni odregistrovat t��du okna?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Could Not Unregister Class.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">hInstance=NULL;<span class="kom">// Nastav� hInstance na NULL</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Dal�� ��st k�du m� na starosti vytvo�en� OpenGL okna. Str�vil jsem mnoho �asu p�em��len�m zda m�m ud�lat pouze fullscreen m�d, kter� by vy�adoval m�n� k�du, nebo jednodu�e upravitelnou, u�ivatelsky p��jemnou verzi s variantou jak pro okno tak pro fullscreen, kter� v�ak vy�aduje mnohem v�ce k�du. Rozhodl jsem se pro druhou variantu, proto�e jsem dost�val mnoho dotaz� jako nap��klad: Jak mohu vytvo�it okno m�sto fullscreenu? Jak zm�n�m popisek okna? Jak zm�n�m rozli�en� ne form�t pixel�? N�sleduj�c� k�d dovede v�echno. Jak si m��ete v�imnout funkce vrac� bool a p�ij�m� 5 parametr� v po�ad�: n�zev okna, ���ku okna, v��ku okna, barevnou hloubku, fullscreen (pokud je parametr true program pob�� ve fullscreenu, pokud bude false program pob�� v okn�). Vrac�me bool, abychom v�d�li zda bylo okno �sp�n� vytvo�eno.</p>

<p class="src0">BOOL CreateGLWindow(char* title, int width, int height, int bits, bool fullscreenflag)</p>
<p class="src0">{</p>

<p>Za chv�li po��d�me Windows, aby pro n�s na�el pixel format, kter� odpov�d� tomu, kter� chceme. Toto ��slo ulo��me do prom�nn� PixelFormat.</p>

<p class="src1">GLuint PixelFormat;<span class="kom">// Ukl�d� form�t pixel�</span></p>

<p>Wc bude pou�ijeme k uchov�n� informac� o struktu�e Windows Class. Zm�nou hodnot jednotliv�ch polo�ek, lze ovlivnit vzhled a chov�n� okna. P�ed vytvo�en�m samotn�ho okna se mus� zaregistrovat n�jak� struktura pro okno.</p>

<p class="src1">WNDCLASS wc;<span class="kom">// Struktura Windows Class</span></p>

<p>DwExStyle a dwStyle ponesou informaci o norm�ln�ch a roz���en�ch informac�ch o oknu. Pou�iji prom�nn� k uchov�n� styl�, tak�e mohu m�nit vzhled okna, kter� pot�ebuji vytvo�it (pro fullscreen bez okraje a pro okno okraj).</p>

<p class="src1">DWORD dwExStyle;<span class="kom">// Roz���en� styl okna</span></p>
<p class="src1">DWORD dwStyle;<span class="kom">// Styl okna</span></p>

<p>Zjist�me polohu lev�ho horn�ho a prav�ho doln�ho rohu okna. Tyto prom�nn� vyu�ijeme k tomu, abychom nakreslili okno v takov�m rozli�en�, v jak�m si ho p�ejeme m�t. Pokud vytvo��me okno s rozli�en�m 640x480, okraje budou zab�rat ��st na�eho rozli�en�.</p>

<p class="src1">RECT WindowRect;<span class="kom">// Obd�ln�k okna</span></p>
<p class="src"></p>
<p class="src1">WindowRect.left = (long)0;<span class="kom">// Nastav� lev� okraj na nulu</span></p>
<p class="src1">WindowRect.right = (long)width;<span class="kom">// Nastav� prav� okraj na zadanou hodnotu</span></p>
<p class="src1">WindowRect.top = (long)0;<span class="kom">// Nastav� horn� okraj na nulu</span></p>
<p class="src1">WindowRect.bottom = (long)height;<span class="kom">// Nastav� spodn� okraj na zadanou hodnotu</span></p>

<p>P�i�ad�me glob�ln� prom�nn� fullscreen, hodnotu fullscreenflag. Tak�e pokud na�e okno pob�� ve fullscreenu, prom�nn� fullscreen se bude rovnat true. Kdybychom zav�rali okno ve fullscreenu, ale hodnota prom�nn� fullscreen by byla false m�sto true, jak by m�la b�t, po��ta� by se nep�epl zp�t do syst�mu, proto�e by si myslel, �e v n�m ji� je. Jednodu�e shrnuto, fullscreen v�dy mus� obsahovat spr�vnou hodnotu.</p>

<p class="src1">fullscreen = fullscreenflag;<span class="kom">// Nastav� prom�nnou fullscreen na spr�vnou hodnotu</span></p>

<p>Z�sk�me instanci pro okno a pot� definujeme Window Class. CS_HREDRAW a CS_VREDRAW donut� na�e okno, aby se p�ekreslilo, kdykoliv se zm�n� jeho velikost. CS_OWNDC vytvo�� priv�tn� kontext za��zen�. To znamen�, �e nen� sd�len s ostatn�mi aplikacemi. WndProc je procedura okna, kter� sleduje p��choz� zpr�vy pro program. ��dn� extra data pro okno nepou��v�me, tak�e do dal��ch dvou polo�ek p�i�ad�me nulu. Nastav�me instanci a hIcon na NULL, co� znamen�, �e nebudeme pro n� program pou��vat ��dnou speci�ln� ikonu a pro kurzor my�i pou��v�me standardn� �ipku. Barva pozad� n�s nemus� zaj�mat (to za��d�me v OpenGL). Nechceme, aby okno m�lo menu, tak�e i tuto hodnotu nastav�me na NULL. Jm�no t��dy m��e b�t libovoln�. J� pou�iji pro jednoduchost &quot;OpenGL&quot;.</p>

<p class="src1">hInstance = GetModuleHandle(NULL);<span class="kom">// Z�sk� instanci okna</span></p>
<p class="src"></p>
<p class="src1">wc.style = CS_HREDRAW | CS_VREDRAW | CS_OWNDC;<span class="kom">// P�ekreslen� p�i zm�n� velikosti a vlastn� DC</span></p>
<p class="src1">wc.lpfnWndProc = (WNDPROC) WndProc;<span class="kom">// Definuje proceduru okna</span></p>
<p class="src1">wc.cbClsExtra = 0;<span class="kom">// ��dn� extra data</span></p>
<p class="src1">wc.cbWndExtra = 0;<span class="kom">// ��dn� extra data</span></p>
<p class="src1">wc.hInstance = hInstance;<span class="kom">// Instance</span></p>
<p class="src1">wc.hIcon = LoadIcon(NULL, IDI_WINLOGO);<span class="kom">// Standardn� ikona</span></p>
<p class="src1">wc.hCursor = LoadCursor(NULL, IDC_ARROW);<span class="kom">// Standardn� kurzor my�i</span></p>
<p class="src1">wc.hbrBackground = NULL;<span class="kom">// Pozad� nen� nutn�</span></p>
<p class="src1">wc.lpszMenuName = NULL;<span class="kom">// Nechceme menu</span></p>
<p class="src1">wc.lpszClassName = &quot;OpenGL&quot;;<span class="kom">// Jm�no t��dy okna</span></p>

<p>Zaregistrujeme pr�v� definovanou t��du okna. Kdy� nastane chyba a zobraz� se chybov� hl�en�. Zm��knut�m tla��tka OK se program ukon��.</p>

<p class="src1">if (!RegisterClass(&amp;wc))<span class="kom">// Registruje t��du okna</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Failed To Register The Window Class.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// P�i chyb� vr�t� false</span></p>
<p class="src1">}</p>

<p>Nyn� si zjist�me zda m� program b�et ve fullscreenu, nebo v okn�.</p>

<p class="src1">if (fullscreen)<span class="kom">// Budeme ve fullscreenu?</span></p>
<p class="src1">{</p>

<p>S p�ep�n�n�m do fullscreenu, m�vaj� lid� mnoho probl�m�. Je zde p�r d�le�it�ch v�c�, na kter� si mus�te d�vat pozor. Ujist�te se, �e ���ka a v��ka, kterou pou��v�te ve fullscreenu je toto�n� s tou, kterou chcete pou��t v okn�. Dal�� v�c je hodn� d�le�it�. Mus�te p�epnout do fullscreenu p�edt�m ne� vytvo��te okno. V tomto k�du se o rovnost v��ky a ���ky nemus�te starat, proto�e velikost ve fullscreenu i v okn� budou stejn�.</p>

<p class="src2">DEVMODE dmScreenSettings;<span class="kom">// M�d za��zen�</span></p>
<p class="src"></p>
<p class="src2">memset(&amp;dmScreenSettings,0,sizeof(dmScreenSettings));<span class="kom">// Vynulov�n� pam�ti</span></p>
<p class="src"></p>
<p class="src2">dmScreenSettings.dmSize=sizeof(dmScreenSettings);<span class="kom">// Velikost struktury Devmode</span></p>
<p class="src2">dmScreenSettings.dmPelsWidth= width;<span class="kom">// ���ka okna</span></p>
<p class="src2">dmScreenSettings.dmPelsHeight= height;<span class="kom">// V��ka okna</span></p>
<p class="src2">dmScreenSettings.dmBitsPerPel= bits;<span class="kom">// Barevn� hloubka</span></p>
<p class="src2">dmScreenSettings.dmFields=DM_BITSPERPEL|DM_PELSWIDTH|DM_PELSHEIGHT;</p>

<p>Funkce ChangeDisplaySettings() se pokus� p�epnout do m�du, kter� je ulo�en v dmScreenSettings. Pou�iji parametr CDS_FULLSCREEN, proto�e odstran� pracovn� li�tu ve spodn� ��sti obrazovky a nep�esune nebo nezm�n� velikost okna p�i p�ep�n�n� z fullscreenu do syst�mu nebo naopak.</p>

<p class="src2"><span class="kom">// Pokus� se pou��t pr�v� definovan� nastaven�</span></p>
<p class="src2">if (ChangeDisplaySettings(&amp;dmScreenSettings,CDS_FULLSCREEN)!=DISP_CHANGE_SUCCESSFUL)</p>
<p class="src2">{</p>

<p>Pokud pr�v� vytvo�en� fullscreen m�d neexistuje, zobraz� se chybov� zpr�va s nab�dkou spu�t�n� v okn� nebo opu�t�n� programu.</p>

<p class="src3"><span class="kom">// Nejde-li fullscreen, m��e u�ivatel spustit program v okn� nebo ho opustit</span></p>
<p class="src3">if (MessageBox(NULL,&quot;The Requested Fullscreen Mode Is Not Supported By\nYour Video Card. Use Windowed Mode Instead?&quot;,&quot;NeHe GL&quot;,MB_YESNO|MB_ICONEXCLAMATION)==IDYES)</p>
<p class="src3">{</p>

<p>Kdy� se u�ivatel rozhodne pro b�h v okn�, do prom�nn� fullscreen se p�i�ad� false a program pokra�uje d�le.</p>

<p class="src4">fullscreen=FALSE;<span class="kom">// B�h v okn�</span></p>
<p class="src3">}</p>
<p class="src3">else</p>
<p class="src3">{</p>

<p>Pokud se u�ivatel rozhodl pro ukon�en� programu, zobraz� se u�ivateli zpr�va, �e program bude ukon�en. Bude vr�cena hodnota false, kter� na�emu programu ��k�, �e pokus o vytvo�en� okna nebyl �sp�n� a potom se program ukon��.</p>

<p class="src4"><span class="kom">// Zobraz� u�ivateli zpr�vu, �e program bude ukon�en</span></p>
<p class="src4">MessageBox(NULL,&quot;Program Will Now Close.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONSTOP);</p>
<p class="src"></p>
<p class="src4">return FALSE;<span class="kom">// Vr�t� FALSE</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Proto�e pokus o p�epnut� do fullscreenu m��e selhat, nebo se u�ivatel m��e rozhodnout pro b�h programu v okn�, zkontrolujeme je�t� jednou zda je prom�nn� fullscreen true nebo false. A� pot� nastav�me typ obrazu.</p>

<p class="src1">if (fullscreen)<span class="kom">// Jsme st�le ve fullscreenu?</span></p>
<p class="src1">{</p>

<p>Pokud jsme st�le ve fullscreenu nastav�me roz���en� styl na WS_EX_APPWINDOW, co� donut� okno, aby p�ekrylo pracovn� li�tu. Styl okna ur��me na WS_POPUP. Tento typ okna nem� ��dn� okraje, co� je pro fullscreen v�hodn�. Nakonec vypneme kurzor my�i. Pokud v� program nen� interaktivn�, je v�t�inou vhodn�j�� ve fullscreenu kurzor vypnout. Pro co rozhodnete je na v�s.</p>

<p class="src2">dwExStyle=WS_EX_APPWINDOW;<span class="kom">// Roz���en� styl okna</span></p>
<p class="src2">dwStyle=WS_POPUP;<span class="kom">// Styl okna</span></p>
<p class="src2">ShowCursor(FALSE);<span class="kom">// Skryje kurzor</span></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>

<p>Pokud m�sto fullscreenu pou��v�me b�h v okn�, nastav�me roz���en� styl na WS_EX_WINDOWEDGE. To dod� oknu trochu 3D vzhledu. Styl nastav�me na WS_OVERLAPPEDWINDOW m�sto na WS_POPUP. WS_OVERLAPPEDWINDOW vytvo�� okno s li�tou, okraji, tla��tky pro minimalizaci a maximalizaci. Budeme moci m�nit velikost.</p>

<p class="src2">dwExStyle=WS_EX_APPWINDOW | WS_EX_WINDOWEDGE;<span class="kom">// Roz���en� styl okna</span></p>
<p class="src2">dwStyle=WS_OVERLAPPEDWINDOW;<span class="kom">// Styl okna</span></p>
<p class="src1">}</p>

<p>P�izp�sob�me okno podle stylu, kter� jsme vytvo�ili. P�izp�soben� ud�l� okno v takov�m rozli�en�, jak� po�adujeme. Norm�ln� by okraje p�ekr�valy ��st okna. S pou�it�m p��kazu AdjustWindowRectEx ��dn� ��st OpenGL sc�ny nebude p�ekryta okraji, m�sto toho bude okno ud�l�no o m�lo v�t��, aby se do n�j ve�ly v�echny pixely tvo��c� okraj okna. Ve fullscreenu tato funkce nem� ��dn� efekt.</p>

<p class="src1">AdjustWindowRectEx(&amp;WindowRect, dwStyle, FALSE, dwExStyle);<span class="kom">// P�izp�soben� velikosti okna</span></p>

<p>Vytvo��me okno a zkontrolujeme zda bylo vytvo�eno spr�vn�. Pou�ijeme funkci CreateWindowEx() se v�emi parametry, kter� vy�aduje. Roz���en� styl, kter� jsme se rozhodli pou��t. Jm�no t��dy (mus� b�t stejn� jako to, kter� jste pou�ili, kdy� jste registrovali Window Class).Titulek okna. Styl okna. Horn� lev� pozice okna (0,0 je nejjist�j��). ���ka a v��ka okna. Nechceme m�t rodi�ovsk� okno ani menu, tak�e nastav�me tyto parametry na NULL. Zad�me instanci okna a kone�n� p�i�ad�me NULL na m�sto posledn�ho parametru. V�imn�te si, �e zahrnujeme styly WS_CLIPSIBLINGS a WS_CLIPCHILDREN do stylu, kter� jsme se rozhodli pou��t. WS_CLIPSIBLINGS a WS_CLIPCHILDREN jsou pot�ebn� pro OpenGL, aby pracovalo spr�vn�. Tyto styly zakazuj� ostatn�m okn�m, aby kreslily do na�eho okna.</p>

<p class="src1"><span class="kom">// Vytvo�en� okna</span></p>
<p class="src1">if (!(hWnd=CreateWindowEx(dwExStyle,<span class="kom">// Roz���en� styl</span></p>
<p class="src2">&quot;OpenGL&quot;,<span class="kom">// Jm�no t��dy</span></p>
<p class="src2">title,<span class="kom">// Titulek</span></p>
<p class="src2">dwStyle |<span class="kom">// Definovan� styl</span></p>
<p class="src2">WS_CLIPSIBLINGS |<span class="kom">// Po�adovan� styl</span></p>
<p class="src2">WS_CLIPCHILDREN,<span class="kom">// Po�adovan� styl</span></p>
<p class="src2">0, 0,<span class="kom">// Pozice</span></p>
<p class="src2">WindowRect.right-WindowRect.left,<span class="kom">// V�po�et ���ky</span></p>
<p class="src2">WindowRect.bottom-WindowRect.top,<span class="kom">// V�po�et v��ky</span></p>
<p class="src2">NULL,<span class="kom">// ��dn� rodi�ovsk� okno</span></p>
<p class="src2">NULL,<span class="kom">// Bez menu</span></p>
<p class="src2">hInstance,<span class="kom">// Instance</span></p>
<p class="src2">NULL)))<span class="kom">// Nep�edat nic do WM_CREATE</span></p>

<p>D�le zkontrolujeme zda bylo vytvo�eno. Pokud bylo, hWnd obsahuje handle tohoto okna. Kdy� se vytvo�en� okna nepovede, k�d zobraz� chybovou zpr�vu a program se ukon��.</p>

<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zru�� okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Window Creation Error.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Vr�t� chybu</span></p>
<p class="src1">}</p>

<p>Vybereme Pixel Format, kter� podporuje OpenGL, d�le zvol�me double buffering a RGBA (�erven�, zelen�, modr�, pr�hlednost). Pokus�me se naj�t form�t, kter� odpov�d� tomu, pro kter� jsme se rozhodli (16 bit�, 24 bit�, 32 bit�). Nakonec nastav�me Z-Buffer. Ostatn� parametry se nepou��vaj� nebo pro n�s nejsou d�le�it�.</p>

<p class="src1">static PIXELFORMATDESCRIPTOR pfd=<span class="kom">// Ozn�m�me Windows jak chceme v�e nastavit</span></p>
<p class="src1">{</p>
<p class="src2">sizeof(PIXELFORMATDESCRIPTOR),<span class="kom">// Velikost struktury</span></p>
<p class="src2">1,<span class="kom">// ��slo verze</span></p>
<p class="src2">PFD_DRAW_TO_WINDOW |<span class="kom">// Podpora okna</span></p>
<p class="src2">PFD_SUPPORT_OPENGL |<span class="kom">// Podpora OpenGL</span></p>
<p class="src2">PFD_DOUBLEBUFFER,<span class="kom">// Podpora Double Bufferingu</span></p>
<p class="src2">PFD_TYPE_RGBA,<span class="kom">// RGBA Format</span></p>
<p class="src2">bits,<span class="kom">// Zvol� barevnou hloubku</span></p>
<p class="src2">0, 0, 0, 0, 0, 0,<span class="kom">// Bity barev ignorov�ny</span></p>
<p class="src2">0,<span class="kom">// ��dn� alpha buffer</span></p>
<p class="src2">0,<span class="kom">// Ignorov�n Shift bit</span></p>
<p class="src2">0,<span class="kom">// ��dn� akumula�n� buffer</span></p>
<p class="src2">0, 0, 0, 0,<span class="kom">// Akumula�n� bity ignorov�ny</span></p>
<p class="src2">16,<span class="kom">// 16-bitov� hloubkov� buffer (Z-Buffer)</span></p>
<p class="src2">0,<span class="kom">// ��dn� Stencil Buffer</span></p>
<p class="src2">0,<span class="kom">// ��dn� Auxiliary Buffer</span></p>
<p class="src2">PFD_MAIN_PLANE,<span class="kom">// Hlavn� vykreslovac� vrstva</span></p>
<p class="src2">0,<span class="kom">// Rezervov�no</span></p>
<p class="src2">0, 0, 0<span class="kom">// Maska vrstvy ignorov�na</span></p>
<p class="src1">};</p>

<p>Pokud nenastaly probl�my b�hem vytv��en� okna, pokus�me se p�ipojit kontext za��zen�. Pokud ho se nep�ipoj�, zobraz� se chybov� hl�en� a program se ukon��.</p>

<p class="src1">if (!(hDC=GetDC(hWnd)))<span class="kom">// Poda�ilo se p�ipojit kontext za��zen�?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Create A GL Device Context.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukon�� program</span></p>
<p class="src1">}</p>

<p>Kdy� z�sk�me kontext za��zen�, pokus�me se naj�t odpov�daj�c� Pixel Format. Kdy� ho Windows nenajde form�t, zobraz� se chybov� zpr�va a program se ukon��.</p>

<p class="src1">if (!(PixelFormat=ChoosePixelFormat(hDC,&amp;pfd)))<span class="kom">// Poda�ilo se naj�t Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Find A Suitable PixelFormat.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukon�� program</span></p>
<p class="src1">}</p>

<p>Kdy� Windows najde odpov�daj�c� form�t, tak se ho pokus�me nastavit. Pokud p�i pokusu o nastaven� nastane chyba, op�t se zobraz� chybov� hl�en� a program se ukon��.</p>

<p class="src1">if(!SetPixelFormat(hDC,PixelFormat,&amp;pfd))<span class="kom">// Poda�ilo se nastavit Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Set The PixelFormat.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukon�� program</span></p>
<p class="src1">}</p>

<p>Pokud byl nastaven Pixel Format spr�vn�, pokus�me se z�skat Rendering Context. Pokud ho nez�sk�me, program zobraz� chybovou zpr�vu a ukon�� se.</p>

<p class="src1">if (!(hRC=wglCreateContext(hDC)))<span class="kom">// Poda�ilo se vytvo�it Rendering Context?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Create A GL Rendering Context.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukon�� program</span></p>
<p class="src1">}</p>

<p>Pokud nenastaly ��dn� chyby p�i vytv��en� jak Device Context, tak Rendering Context, v�e co mus�me nyn� ud�lat je aktivovat Rendering Context. Pokud ho nebudeme moci aktivovat, zobraz� se chybov� zpr�va a program se ukon��.</p>

<p class="src1">if(!wglMakeCurrent(hDC,hRC))<span class="kom">// Poda�ilo se aktivovat Rendering Context?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Activate The GL Rendering Context.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukon�� program</span></p>
<p class="src1">}</p>

<p>Pokud bylo okno vytvo�eno, zobraz�me ho na obrazovce, nastav�me ho, aby bylo v pop�ed� (vy��� priorita) a pak nastav�me zam��en� na toto okno. Zavol�me funkci ResizeGLScene() s parametry odpov�daj�c�mi v��ce a ���ce okna, abychom spr�vn� nastavili perspektivu OpenGL.</p>

<p class="src1">ShowWindow(hWnd,SW_SHOW);<span class="kom">// Zobrazen� okna</span></p>
<p class="src1">SetForegroundWindow(hWnd);<span class="kom">// Do pop�ed�</span></p>
<p class="src1">SetFocus(hWnd);<span class="kom">// Zam��� fokus</span></p>
<p class="src1">ReSizeGLScene(width, height);<span class="kom">// Nastaven� perspektivy OpenGL sc�ny</span></p>

<p>Kone�n� se dost�v�me k vol�n� v��e definovan� funkce InitGL(), ve kter� nastavujeme osv�tlen�, loading textur a cokoliv jin�ho, co je pot�eba. M��ete vytvo�it svou vlastn� kontrolu chyb ve funkci InitGL() a vracet true, kdy� v�e prob�hne bez probl�m�, nebo false, pokud nastanou n�jak� probl�my. Nap��klad, nastane-li chyba p�i nahr�v�n� textur, vr�t�te false, jako znamen�, �e n�co selhalo a program se ukon��.</p>

<p class="src1">if (!InitGL())<span class="kom">// Inicializace okna</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Initialization Failed.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukon�� program</span></p>
<p class="src1">}</p>

<p>Pokud jsme se dostali a� takhle daleko, m��eme konstatovat, �e vytvo�en� okna prob�hlo bez probl�m�. Vr�t�me true do WinMain(), co� ��k�, �e nenastaly ��dn� chyby. To zabr�n� programu, aby se s�m ukon�il.</p>

<p class="src1">return TRUE;<span class="kom">// V�e prob�hlo v po��dku</span></p>
<p class="src0">}</p>

<p>Nyn� se vypo��d�me se syst�mov�mi zpr�vami pro okno. Kdy� m�me zaregistrovanou na�i Window Class, m��eme podstoupit k ��sti k�du, kter� m� na starosti zpracov�n� zpr�v.</p>

<p class="src0">LRESULT CALLBACK WndProc(HWND hWnd,<span class="kom">// Handle okna</span></p>
<p class="src1">UINT uMsg,<span class="kom">// Zpr�va pro okno</span></p>
<p class="src1">WPARAM wParam,<span class="kom">// Dopl�kov� informace</span></p>
<p class="src1">LPARAM lParam)<span class="kom">// Dopl�kov� informace</span></p>
<p class="src0">{</p>

<p>Nap�eme mapu zpr�v. Program se bude v�tvit podle prom�nn� uMsg, kter� obsahuje jm�no zpr�vy.</p>

<p class="src1">switch (uMsg)<span class="kom">// V�tven� podle p��choz� zpr�vy</span></p>
<p class="src1">{</p>

<p>Po p��chodu WM_ACTIVE, zkontrolujeme, zda je okno st�le aktivn�. Pokud bylo minimalizov�no, nastav�me hodnotu active na false. Pokud je na�e okno aktivn�, prom�nn� active bude m�t hodnotu true.</p>

<p class="src2">case WM_ACTIVATE:<span class="kom">// Zm�na aktivity okna</span></p>
<p class="src2">{</p>
<p class="src3">if (!HIWORD(wParam))<span class="kom">// Zkontroluje zda nen� minimalizovan�</span></p>
<p class="src3">{</p>
<p class="src4">active=TRUE;<span class="kom">// Program je aktivn�</span></p>
<p class="src3">}</p>
<p class="src3">else</p>
<p class="src3">{</p>
<p class="src4">active=FALSE;<span class="kom">// Program nen� aktivn�</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">return 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">}</p>

<p>Po p��chodu WM_SYSCOMMAND (syst�mov� p��kaz) porovn�me wParam s mo�n�mi stavy, kter� mohly nastat. Kdy� je wParam WM_SCREENSAVE nebo SC_MONITORPOWER sna�� se syst�m zapnout spo�i� obrazovky, nebo p�ej�t do �sporn�ho re�imu. Jestli�e vr�t�me 0 zabr�n�me syst�mu, aby tyto akce provedl.</p>

<p class="src2">case WM_SYSCOMMAND:<span class="kom">// Syst�mov� p��kaz</span></p>
<p class="src2">{</p>
<p class="src3">switch (wParam)<span class="kom">// Typ syst�mov�ho p��kazu</span></p>
<p class="src3">{</p>
<p class="src4">case SC_SCREENSAVE:<span class="kom">// Pokus o zapnut� �et�i�e obrazovky</span></p>
<p class="src4">case SC_MONITORPOWER:<span class="kom">// Pokus o p�echod do �sporn�ho re�imu?</span></p>
<p class="src5">return 0;<span class="kom">// Zabr�n� oboj�mu</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">break;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">}</p>

<p>P�i�lo-li WM_CLOSE bylo okno zav�eno. Po�leme tedy zpr�vu pro opu�t�n� programu, kter� p�eru�� vykon�v�n� hlavn�ho cyklu. Prom�nnou done (ve WinMain()) nastav�me na true, hlavn� smy�ka se p�eru�� a program se ukon��.</p>

<p class="src2">case WM_CLOSE:<span class="kom">// Povel k ukon�en� programu</span></p>
<p class="src2">{</p>
<p class="src3">PostQuitMessage(0);<span class="kom">// Po�le zpr�vu o ukon�en�</span></p>
<p class="src3">return 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">}</p>

<p>Pokud byla stisknuta kl�vesa, m��eme zjistit, kter� z nich to byla, kdy� zjist�me hodnotu wParam. Potom zad�me do bu�ky, specifikovan� wParam, v poli keys[] true. D�ky tomu potom m��eme zjistit, kter� kl�vesa je pr�v� stisknut�. T�mto zp�sobem lze zkontrolovat stisk v�ce kl�ves najednou.</p>

<p class="src2">case WM_KEYDOWN:<span class="kom">// Stisk kl�vesy</span></p>
<p class="src2">{</p>
<p class="src3">keys[wParam] = TRUE;<span class="kom">// Ozn�m� to programu</span></p>
<p class="src3">return 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">}</p>

<p>Pokud byla naopak kl�vesa uvoln�na ulo��me do bu�ky s indexem wParam v poli keys[] hodnotu false. T�mto zp�sobem m��eme zjistit zda je kl�vesa je�t� st�le stisknuta nebo ji� byla uvoln�na. Ka�d� kl�vesa je reprezentov�na jedn�m ��slem od 0 do 255. Kdy� nap��klad stisknu kl�vesu ��slo 40, hodnota key[40] bude true, jakmile ji pust�m jej� hodnota se vr�t� op�t na false.</p>

<p class="src2">case WM_KEYUP:<span class="kom">// Uvoln�n� kl�vesy</span></p>
<p class="src2">{</p>
<p class="src3">keys[wParam] = FALSE;<span class="kom">// Ozn�m� to programu</span></p>
<p class="src3">return 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">}</p>

<p>Kdykoliv u�ivatel zm�n� velikost okna, po�le se WM_SIZE. P�e�teme LOWORD a HIWORD hodnoty lParam, abychom zjistili jak� je nov� ���ka a v��ka okna. P�ed�me tyto hodnoty do funkce ReSizeGLScene(). Perspektiva OpenGL sc�ny se zm�n� podle nov�ch rozm�r�.</p>

<p class="src2">case WM_SIZE:<span class="kom">// Zm�na velikosti okna</span></p>
<p class="src2">{</p>
<p class="src3">ReSizeGLScene(LOWORD(lParam),HIWORD(lParam));  <span class="kom">// LoWord=���ka, HiWord=V��ka</span></p>
<p class="src3">return 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Zpr�vy, o kter� se nestar�me, budou p�ed�ny funkci DefWindowProc(), tak�e se s nimi vypo��d� syst�m.</p>

<p class="src1">return DefWindowProc(hWnd, uMsg, wParam, lParam);<span class="kom">// P�ed�n� ostatn�ch zpr�v syst�mu</span></p>
<p class="src0">}</p>

<p>Funkce WinMain() je vstupn� bod do aplikace, m�sto, odkud budeme volat funkce na otev�en� okna, sn�man� zpr�v a interakci s u�ivatelem.</p>

<p class="src0">int WINAPI WinMain(HINSTANCE hInstance,<span class="kom">// Instance</span></p>
<p class="src1">HINSTANCE hPrevInstance,<span class="kom">// P�edchoz� instance</span></p>
<p class="src1">LPSTR lpCmdLine,<span class="kom">// Parametry p��kazov� ��dky</span></p>
<p class="src1">int nCmdShow)<span class="kom">// Stav zobrazen� okna</span></p>
<p class="src0">{</p>

<p>Deklarujeme dv� lok�ln� prom�nn�. Msg bude pou�ita na zji��ov�n�, zda se maj� zpracov�vat n�jak� zpr�vy. Prom�nn� done bude m�t na po��tku hodnotu false. To znamen�, �e n� program je�t� nem� b�t ukon�en. Dokud se done rovn� false, program pob��. Jakmile se zm�n� z false na true, program se ukon��.</p>

<p class="src1">MSG msg;<span class="kom">// Struktura zpr�v syst�mu</span></p>
<p class="src1">BOOL done=FALSE;<span class="kom">// Prom�nn� pro ukon�en� programu</span></p>

<p>Dal�� ��st k�du je voliteln�. Zobrazuje zpr�vu, kter� se zept� u�ivatele, zda chce spustit program ve fullscreenu. Pokud u�ivatel vybere mo�nost Ne, hodnota prom�nn� fullscreen se zm�n� z v�choz�ho true na false, a t�m p�dem se program spust� v okn�.</p>

<p class="src1"><span class="kom">// Dotaz na u�ivatele pro fullscreen/okno</span></p>
<p class="src1">if (MessageBox(NULL,&quot;Would You Like To Run In Fullscreen Mode?&quot;, &quot;Start FullScreen?&quot;, MB_YESNO | MB_ICONQUESTION) == IDNO)</p>
<p class="src1">{</p>
<p class="src2">fullscreen=FALSE;<span class="kom">// B�h v okn�</span></p>
<p class="src1">}</p>

<p>Vytvo��me OpenGL okno. Zad�me text titulku, ���ku, v��ku, barevnou hloubku a true (fullscreen), nebo false (okno) jako parametry do funkce CreateGLWindow(). Tak a je to! Je to p�kn� lehk�, �e? Pokud se okno nepoda�� z n�jak�ho d�vodu vytvo�it, bude vr�ceno false a program se okam�it� ukon��.</p>

<p class="src1">if (!CreateGLWindow(&quot;NeHe's OpenGL Framework&quot;,640,480,16,fullscreen))<span class="kom">// Vytvo�en� OpenGL okna</span></p>
<p class="src1">{</p>
<p class="src2">return 0;<span class="kom">// Konec programu p�i chyb�</span></p>
<p class="src1">}</p>

<p>Smy�ka se opakuje tak dlouho, dokud se done rovn� false.</p>

<p class="src1">while(!done)<span class="kom">// Hlavn� cyklus programu</span></p>
<p class="src1">{</p>

<p>Prvn� v�c, kterou ud�l�me, je zkontrolov�n� zpr�v pro okno. Pomoc� funkce PeekMessage() m��eme zjistit zda n�jak� zpr�vy �ekaj� na zpracov�n� bez toho, aby byl program pozastaven. Mnoho program� pou��v� funkci GetMessage(). Pracuje to skv�le, ale program nic ned�l�, kdy� nedost�v� ��dn� zpr�vy.</p>

<p class="src2">if (PeekMessage(&amp;msg,NULL,0,0,PM_REMOVE))<span class="kom">// P�i�la zpr�va?</span></p>
<p class="src2">{</p>

<p>Zkontrolujeme, zda jsme neobdr�eli zpr�vu pro ukon�en� programu. Pokud je aktu�ln� zpr�va WM_QUIT, kter� je zp�sobena vol�n�m funkce PostQuitMessage(0), nastav�me done na true, ��m� p�eru��me hlavn� cyklus a ukon��me program.</p>

<p class="src3">if (msg.message==WM_QUIT)<span class="kom">// Obdr�eli jsme zpr�vu pro ukon�en�?</span></p>
<p class="src3">{</p>
<p class="src4">done=TRUE;<span class="kom">// Konec programu</span></p>
<p class="src3">}</p>
<p class="src3">else<span class="kom">// P�ed�me zpr�vu procedu�e okna</span></p>
<p class="src3">{</p>

<p>Kdy� zpr�va nevyz�v� k ukon�en� programu, tak p�ed�me funkc�m TranslateMessage() a DispatchMessage() referenci na tuto zpr�vu, aby ji funkce WndProc() nebo Windows zpracovaly.</p>

<p class="src4">TranslateMessage(&amp;msg);<span class="kom">// P�elo�� zpr�vu</span></p>
<p class="src4">DispatchMessage(&amp;msg);<span class="kom">// Ode�le zpr�vu</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Pokud nedo�la ��dn� zpr�va</span></p>
<p class="src2">{</p>

<p>Pokud zde nebudou ji� ��dn� zpr�vy, p�ekresl�me OpenGL sc�nu. N�sleduj�c� ��dek kontroluje, zda je okno aktivn�. Na�e sc�na je vyrenderov�na a je zkontrolov�na vr�cen� hodnota. Kdy� funkce DrawGLScene() vr�t� false nebo je stisknut ESC, hodnota prom�nn� done je nastavena na true, co� ukon�� b�h programu.</p>

<p class="src3">if (active)<span class="kom">// Je program aktivn�?</span></p>
<p class="src3">{</p>
<p class="src4">if (keys[VK_ESCAPE])<span class="kom">// Byl stisknut ESC?</span></p>
<p class="src4">{</p>
<p class="src5">done=TRUE;<span class="kom">// Ukon��me program</span></p>
<p class="src4">}</p>
<p class="src4">else<span class="kom">// P�ekreslen� sc�ny</span></p>
<p class="src4">{</p>

<p>Kdy� v�echno prob�hlo bez probl�m�, prohod�me obsah buffer� (s pou�it�m dvou buffer� p�edejdeme blik�n� obrazu p�i p�ekreslov�n�). Pou�it�m dvojt�ho bufferingu v�echno vykreslujeme do obrazovky v pam�ti, kterou nevid�me. Jakmile vym�n�me obsah buffer�, to co je na obrazovce se p�esune do t�to skryt� obrazovky a to, co je ve skryt� obrazovce se p�enese na monitor. D�ky tomu nevid�me probliknut�.</p>

<p class="src5">DrawGLScene();<span class="kom">// Vykreslen� sc�ny</span></p>
<p class="src5">SwapBuffers(hDC);<span class="kom">// Prohozen� buffer� (Double Buffering)</span></p>
<p class="src4">}</p>
<p class="src3">}</p>

<p>P�i stisku kl�vesy F1 p�epneme z fullscreenu do okna a naopak.</p>

<p class="src3">if (keys[VK_F1])<span class="kom">// Byla stisknuta kl�vesa F1?</span></p>
<p class="src3">{</p>
<p class="src4">keys[VK_F1]=FALSE;<span class="kom">// Ozna� ji jako nestisknutou</span></p>
<p class="src4">KillGLWindow();<span class="kom">// Zru�� okno</span></p>
<p class="src4">fullscreen=!fullscreen;<span class="kom">// Negace fullscreen</span></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Znovuvytvo�en� okna</span></p>
<p class="src4">if (!CreateGLWindow(&quot;NeHe's OpenGL Framework&quot;,640,480,16,fullscreen))</p>
<p class="src4">{</p>
<p class="src5">return 0;<span class="kom">// Konec programu pokud nebylo vytvo�eno</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Pokud se prom�nn� done rovn� true, hlavn� cyklus se p�eru��. Zav�eme okno a opust�me program.</p>

<p class="src1">KillGLWindow();<span class="kom">// Zav�e okno</span></p>
<p class="src1">return (msg.wParam);<span class="kom">// Ukon�en� programu</span></p>
<p class="src0">}</p>

<p>V t�to lekci jsem se v�m pokou�el co nejpodrobn�ji vysv�tlit ka�d� krok p�i nastavov�n� a vytv��en� OpenGL programu. Program se ukon�� p�i stisku kl�vesy ESC a sleduje, zda je okno aktivn� �i nikoliv.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: V�clav Slov��ek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>
<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson01.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson01.zip">ASM</a> k�d t�to lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson01_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson01.zip">C#</a> k�d t�to lekce. ( <a href="mailto:joachim_rohde@freenet.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson01.zip">VB.Net CsGL</a> k�d t�to lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson01.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson01.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson01.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson01-2.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:nelsonnelson@hotmail.com">Nelson Nelson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson01.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson01.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson01.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson01.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson01.zip">Java/SWT</a> k�d t�to lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson01.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson01.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson01.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson01.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson01.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson01.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson01.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson01.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson01.zip">Power Basic</a> k�d t�to lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson01.zip">Perl</a> k�d t�to lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson01.gz">Python</a> k�d t�to lekce. ( <a href="mailto:hakuin@voicenet.com">John</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson01.zip">Scheme</a> k�d t�to lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson01.zip">Solaris</a> k�d t�to lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson01.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson01.zip">Visual Fortran</a> k�d t�to lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson01.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(1);?>
<?FceNeHeOkolniLekce(1);?>

<?
include 'p_end.php';
?>
