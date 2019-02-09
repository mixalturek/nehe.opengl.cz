<?
$g_title = 'CZ NeHe OpenGL - Lekce 38 - Nahr�v�n� textur z resource souboru &amp; texturov�n� troj�heln�k�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(38);?>

<h1>Lekce 38 - Nahr�v�n� textur z resource souboru &amp; texturov�n� troj�heln�k�</h1>

<p class="nadpis_clanku">Tento tutori�l jsem napsal pro v�echny z v�s, kte�� se m� v emailech dotazovali na to &quot;Jak m�m loadovat texturu ze zdroj� programu, abych m�l v�echny obr�zky ulo�en� ve v�sledn�m .exe souboru?&quot; a tak� pro ty, kte�� psali &quot;V�m, jak otexturovat obd�ln�k, ale jak mapovat na troj�heln�k?&quot; Tutori�l nen�, oproti jin�m, extr�mn� pokrokov�, ale kdy� nic jin�ho, tak se nau��te, jak skr�t va�e precizn� textury p�ed okem u�ivatele. A co v�c - budete moci trochu zt�it jejich kraden� :-)</p>

<p>Tak u� v�te, jak otexturovat �tverec, jak nahr�t bitmapu, tga,... Tak jak kruci otexturovat troj�heln�k? A co kdy� chci textury ukr�t do .exe souboru? Kdy� zjist�te, jak je to jednoduch�, budete se divit, �e v�s �e�en� u� d�vno nenapadlo.</p>

<p>Rad�ji ne� abych v�e do detail� vysv�tloval, p�edvedu p�r screenshot�, tak�e budete p�esn� v�d�t, o �em mluv�m. Budu pou��vat nejnov�j�� z�kladn� k�d, kter� si m��ete na <?OdkazBlank('http://nehe.gamedev.net/');?> pod nadpisem "NeHeGL Basecode" a nebo kliknut�m na odkaz na konci tohoto tutori�lu.</p>

<p>Prvn� co pot�ebujeme ud�lat, je p�idat obr�zky do zdrojov�ho souboru (resource file). Mnoho z v�s u� zjistilo, jak to ud�lat, ale nane�t�st� jste �asto opominuli n�kolik krok�, a proto skon�ili s nepou�iteln�m zdrojov�m souborem napln�n�m bitmapami, kter� nejdou pou��t.</p>

<p>Tento tutori�l je naps�n pro Visual C++ 6.0. Pokud pou��v�te n�co jin�ho, tato ��st tutori�lu je pro v�s zbyte�n�, obzvl�t� obr�zky prost�ed� Visual C++.</p>

<p>Moment�ln� budete schopni nahr�t pouze 24-bitov� BMP. K nahr�n� 8-bitov�ho BMP bychom pot�ebovali mnoho k�du nav�c. R�d bych v�d�l o n�kom, kdo m� mal� optimalizovan� BMP loader. K�d, kter� m�m k sou�asn�mu na��t�n� 8 a 24-bitov�ch BMP je prost� p��ern�. N�co, co pou��v� LoadImage, by se hodilo.</p>

<p>Tak tedy za�neme...</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource1.jpg" width="480" height="360" alt="Resource 1" /></div>

<p>Otev�ete projekt a vyberte z hlavn�ho menu Insert-&gt;Resource.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource2.jpg" width="351" height="282" alt="Resource 2" /></div>

<p>Jste dot�z�ni na typ zdroje, kter� si p�ejete importovat. Vyberte Bitmap a klikn�te na tla��tko Import.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource3.jpg" width="428" height="304" alt="Resource 3" /></div>

<p>Otev�e se prohl�e� soubor�. Vstupte do slo�ky Data a ozna�te v�echny 3 bitmapy (podr�te Ctrl kdy� je budete ozna�ovat). Pak klikn�te na tla��tko Import. Pokud nevid�te soubory bitmap, ujist�te se, �e v poli Files of type je vybr�no All Files(*.*).</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource4.jpg" width="480" height="85" alt="Resource 4" /></div>

<p>T�ikr�t se zobraz� varovn� zpr�va (jednou za ka�d� obr�zek). V�e co v�m ��k� je, �e obr�zky byly v po��dku importov�ny, ale nem��ete je upravovat, proto�e maj� v�ce ne� 256 barev. ��dn� d�vod ke starostem!</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource5.jpg" width="480" height="360" alt="Resource 5" /></div>

<p>Kdy� jsou v�echny obr�zky importov�ny, zobraz� se jejich seznam. Ka�d� bitmapa dostane sv� identifika�n� jm�no (ID), kter� za��n� na IDB_BITMAP a n�sleduje ��slo 1 - 3. Pokud jste l�n�, mohli byste to nechat tak a vrhnout se na k�d t�to lekce. ( My ale nejsme l�n�!</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource6.jpg" width="480" height="360" alt="Resource 6" /></div>

<p>Prav�m tla��tkem klikn�te na ka�d� ID a vyberte z menu polo�ku Properties. P�ejmenujte identifika�n� jm�na na p�vodn� n�zvy soubor�.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource7.jpg" width="428" height="304" alt="Resource 7" /></div>

<p>Te�, kdy� jsme hotovi, vyberte z hlavn�ho menu File-&gt;Save All. Proto�e jste pr�v� vytvo�ili nov� zdrojov� soubor, budete dot�z�ni na to, jak chcete soubor pojmenovat. M��ete soubor pojmenovat, jak chcete. Jakmile vypln�te jm�no souboru klikn�te na tla��tko Save.</p>

<p>A� sem se hodn� z v�s propracovalo. M�te zdrojov� soubor pln� bitmapov�ch obr�zk� a u� jste ho i ulo�ili na disk. Abyste v�ak obr�zky mohli pou��t, mus�te ud�lat je�t� p�r v�c�.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource8.jpg" width="480" height="360" alt="Resource 8" /></div>

<p>D�le mus�te p�idat soubor se zdroji do aktu�ln�ho projektu. Z hlavn�ho menu vyberte Project-&gt;Add To Project-&gt;Files.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource9.jpg" width="428" height="304" alt="Resource 9" /></div>

<p>Vyberte resource.h a v� zdrojov� soubor s bitmapami. Podr�te Ctrl pro v�b�r v�c soubor�, nebo je p�idejte samostatn�.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource10.jpg" width="480" height="360" alt="Resource 10" /></div>

<p>Posledn� v�c, kterou je t�eba ud�lat, je kontrola, zda je zdrojov� soubor ve slo�ce Resource Files. Jak vid�te na obr�zku, byl p�id�n do slo�ky Source Files. Klikn�te na n�ho a p�et�hn�te ho do slo�ky Resource Files.</p>

<p>Kdy� je v�e hotovo. Vyberte z hlavn�ho menu File-&gt;Save All. M�me to t쾹� za sebou!</p>

<p>Vrhneme na k�d! Nejd�le�it�j�� ��dek v k�du je #include &quot;resource.h&quot;. Bez tohoto ��dku v�m kompiler p�i kompilov�n� vr�t� chybu &quot;undeclared identifier&quot;. Resource.h umo��uje p��stup k importovan�m obr�zk�m.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src"></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro GLu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavi�kov� soubor pro GLaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">#include &quot;NeHeGL.h&quot;<span class="kom">// Hlavi�kov� soubor pro NeHeGL</span></p>
<p class="src0">#include &quot;resource.h&quot;<span class="kom">// Hlavi�kov� soubor pro Resource (*D�LE�IT�*)</span></p>
<p class="src"></p>
<p class="src0">#pragma comment( lib, &quot;opengl32.lib&quot; )<span class="kom">// P�ilinkuje OpenGL32.lib</span></p>
<p class="src0">#pragma comment( lib, &quot;glu32.lib&quot; )<span class="kom">// P�ilinkuje GLu32.lib</span></p>
<p class="src0">#pragma comment( lib, &quot;glaux.lib&quot; )<span class="kom">// P�ilinkuje GLaux.lib</span></p>
<p class="src"></p>
<p class="src0">#ifndef CDS_FULLSCREEN<span class="kom">// Pokud je�t� CDS_FULLSCREEN nen� definov�n</span></p>
<p class="src1">#define CDS_FULLSCREEN 4<span class="kom">// Tak ho nadefinujeme</span></p>
<p class="src0">#endif<span class="kom">// Vyhneme se tak mo�n�m chyb�m</span></p>
<p class="src"></p>
<p class="src0">GL_Window* g_window;</p>
<p class="src0">Keys* g_keys;</p>
<p class="src"></p>
<p class="src0">GLuint texture[3];<span class="kom">// M�sto pro 3 textury</span></p>

<p>N�sleduj�c� struktura bude obsahovat informace o mot�lku, se kter�m budeme pohybovat po obrazovce. Tex ur�uje, jakou texturu na objekt namapujeme. X, y a z ud�vaj� pozici objektu v prostoru. Yi bude n�hodn� ��slo ud�vaj�c�, jak rychle mot�l pad� k zemi. Spinz se p�i p�du pou�ije na ot��en� okolo osy z. Spinzi ud�v� rychlost t�to rotace. Flap bude pou�ito pro m�v�n� k��dly (k tomu se pozd�ji je�t� vr�t�me). Fi bude ud�vat jak rychle objekt m�v� k��dly.</p>

<p class="src0">struct object<span class="kom">// Struktura nazvan� object</span></p>
<p class="src0">{</p>
<p class="src1">int tex;<span class="kom">// Kterou texturu namapovat</span></p>
<p class="src1">float x;<span class="kom">// X Pozice</span></p>
<p class="src1">float y;<span class="kom">// Y Pozice</span></p>
<p class="src1">float z;<span class="kom">// Z Pozice</span></p>
<p class="src1">float yi;<span class="kom">// Rychlost p�du</span></p>
<p class="src1">float spinz;<span class="kom">// �hel oto�en� kolem osy z</span></p>
<p class="src1">float spinzi;<span class="kom">// Rychlost ot��en� kolem osy z</span></p>
<p class="src1">float flap;<span class="kom">// M�v�n� k��dly</span></p>
<p class="src1">float fi;<span class="kom">// Sm�r m�v�n�</span></p>
<p class="src0">};</p>

<p>Vytvo��me pades�t t�chto objekt� pojmenovan�ch obj[index].</p>

<p class="src0">object obj[50];<span class="kom">// Vytvo�� 50 objekt� na b�zi struktury</span></p>

<p>N�sleduj�c� ��st k�du nastavuje n�hodn� hodnoty v�em objekt�m. Loop se bude pohybovat mezi 0 - 49 (celkem 50 objekt�). Nejd��ve vybereme n�hodnou texturu od 0 do 2, aby nebyli v�ichni stejn�. Potom nastav�me n�hodnou pozici x od -17.0f do 17.0f. Po��te�n� pozice y bude 18.0f. T�m zajist�me, �e se objekt vytvo�� mimo obrazovku, tak�e ho nevid�me �pln� od za��tku. Pozice z je rovn� n�hodn� hodnota od -10.0f do -40.0f. Spinzi op�t je n�hodn� hodnota od -1.0f do 1.0f. Flap nastav�me na 0.0f (k��dla budou p�esn� uprost�ed). Fi a yi nastav�me taky na n�hodn� hodnoty.</p>

<p class="src0">void SetObject(int loop)<span class="kom">// Nastaven� z�kladn�ch vlastnost� objektu</span></p>
<p class="src0">{</p>
<p class="src1">obj[loop].tex = rand() % 3;<span class="kom">// V�b�r jedn� ze t�� textur</span></p>
<p class="src"></p>
<p class="src1">obj[loop].x = rand() % 34 - 17.0f;<span class="kom">// N�hodn� x od -17.0f do 17.0f</span></p>
<p class="src1">obj[loop].y = 18.0f;<span class="kom">// Pozici y nastav�me na 18 (nad obrazovku)</span></p>
<p class="src1">obj[loop].z = -((rand() % 30000 / 1000.0f) + 10.0f);<span class="kom">// N�hodn� z od -10.0f do -40.0f</span></p>
<p class="src"></p>
<p class="src1">obj[loop].spinzi = (rand() % 10000) / 5000.0f - 1.0f;<span class="kom">// Spinzi je n�hodn� ��slo od -1.0f do 1.0f</span></p>
<p class="src1">obj[loop].flap = 0.0f;<span class="kom">// Flap za�ne na 0.0f</span></p>
<p class="src"></p>
<p class="src1">obj[loop].fi = 0.05f + (rand() % 100) / 1000.0f;<span class="kom">// Fi je n�hodn� ��slo od 0.05f do 0.15f</span></p>
<p class="src1">obj[loop].yi = 0.001f + (rand() % 1000) / 10000.0f;<span class="kom">// Yi je n�hodn� ��slo od 0.001f do 0.101f</span></p>
<p class="src0">}</p>

<p>Te� k t� z�bavn�j�� ��sti. Nahr�n� bitmapy ze zdrojov�ho souboru a jej� p�em�na na texturu. hBMP je ukazatel na soubor s bitmapami. �ekne na�emu programu odkud m� br�t data. BMP je bitmapov� struktura, do kter� m��eme ulo�it data z na�eho zdrojov�ho souboru.</p>

<p class="src0">void LoadGLTextures()<span class="kom">// Vytvo�� textury z bitmap ve zdrojov�m souboru</span></p>
<p class="src0">{</p>
<p class="src1">HBITMAP hBMP;<span class="kom">// Ukazatel na bitmapu</span></p>
<p class="src1">BITMAP BMP;<span class="kom">// Struktura bitmapy</span></p>

<p>�ekneme jak� identifika�n� jm�na chceme pou��t. Chceme nahr�t IDB_BUTTEFLY1, IDB_BUTTEFLY2 a IDB_BUTTERFLY3. Pokud chcete p�idat v�ce obr�zk�, p�ipi�te jejich ID.</p>

<p class="src1">byte Texture[] = { IDB_BUTTERFLY1, IDB_BUTTERFLY2, IDB_BUTTERFLY3 };<span class="kom">// ID bitmap, kter� chceme na��st</span></p>

<p>Na dal��m ��dku pou�ijeme sizeof(Texture) na zji�t�n�, kolik textur chceme sestavit. V Texture[] m�me zad�ny 3 identifika�n� ��sla, tak�e v�sledkem sizeof(Texture) bude hodnota bude 3.</p>

<p class="src1">glGenTextures(sizeof(Texture), &amp;texture[0]);<span class="kom">// Vygenerov�n� t�� textur, sizeof(Texture) = 3 ID</span></p>
<p class="src"></p>
<p class="src1">for (int loop = 0; loop &lt; sizeof(Texture); loop++)<span class="kom">// Projde v�echny bitmapy ve zdroj�ch</span></p>
<p class="src1">{</p>

<p>LoadImage() p�ij�m� parametry GetModuleHandle(NULL) - handle instance. MAKEINTRESOURCE(Texture[loop]) p�em�n� hodnotu cel�ho ��sla Texture[loop] na hodnotu zdroje (obr�zku, kter� m� b�t na�ten). Tady je nutn� poznamenat, �e sice pou��v�me identifika�n� jm�no nap�. IDB_BUTTERFLY1, ale v souboru Resource.h je naps�no n�co ve stylu #define IDB_BUTTERFLY1 115, my se t�m ale nemus�me v�bec zab�vat. V�vojov� prost�ed� v�e automatizuje. IMAGE_BITMAP ��k� na�emu programu, �e zdroj, kter� chceme na��st je bitmapov� obr�zek.</p>

<p>Dal�� dva parametry (0,0) jsou po�adovan� v��ka a ���ka obr�zku. Chceme pou��t implicitn� velikost, tak nastav�me ob� na 0. Posledn� parametr (LR_CREATEDIBSECTION) vr�t� DIB ��st mapy, kter� obsahuje jen bitmapu bez informac� o barv�ch v hlavi�ce. P�esn� to, co chceme.</p>

<p>hBMP bude ukazatelem na na�e bitmapov� data nahran� pomoc� LoadImage().</p>

<p class="src2">hBMP = (HBITMAP) LoadImage(GetModuleHandle(NULL), MAKEINTRESOURCE(Texture[loop]), IMAGE_BITMAP, 0, 0, LR_CREATEDIBSECTION);<span class="kom">// Nahraje bitmapu ze zdroj�</span></p>

<p>D�le zkontrolujeme, zda pointer hBMP opravdu ukazuje na data. Pokud byste cht�li p�idat o�et�en� chyb, m��ete zkontrolovat hBMP a zobrazit chybov� hl�en�. Pokud ale data existuj�, pou�ijeme funkci getObject() na z�sk�n� v�ech dat o velikosti sizeof(BMP) a jejich ulo�en� do bitmapov� struktury &amp;BMP.</p>

<p class="src2">if (hBMP)<span class="kom">// Pokud existuje bitmapa</span></p>
<p class="src2">{</p>
<p class="src3">GetObject(hBMP, sizeof(BMP), &amp;BMP);<span class="kom">// Z�sk�n� objektu</span></p>

<p>glPixelStorei() ozn�m� OpenGL, �e data jsou ulo�ena ve form�tu 4 byty na pixel. Nastav�me filtrov�n� na GL_LINEAR a GL_LINEAR_MIPMAP_LINEAR (kvalitn� a vyhlazen�) a vygenerujeme texturu.</p>

<p class="src3">glPixelStorei(GL_UNPACK_ALIGNMENT,4);<span class="kom">// 4 byty na jeden pixel</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop]);<span class="kom">// Zvol� texturu</span></p>
<p class="src"></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);<span class="kom">// Line�rn� filtrov�n�</span></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR_MIPMAP_LINEAR); <span class="kom">// Mipmapovan� line�rn� filtrov�n�</span></p>

<p>V�imn�te si, �e pou��v�me BMP.bmWidth a BMP.bmHeight, abychom z�skali v��ku a ���ku bitmapy. Tak� mus�me pou�it�m GL_BGR_EXT prohodit �ervenou a modrou barvu. Data z�sk�me z BMP.bmBits.</p>

<p class="src3"><span class="kom">// Vygenerov�n� mipmapovan� textury (3 byty, ���ka, v��ka a BMP data)</span></p>
<p class="src3">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, BMP.bmWidth, BMP.bmHeight, GL_BGR_EXT, GL_UNSIGNED_BYTE, BMP.bmBits);</p>

<p>Posledn�m krokem je smaz�n� objektu bitmapy, abychom uvolnili v�echny syst�mov� prost�edky spojen� s t�mto objektem.</p>

<p class="src3">DeleteObject(hBMP);<span class="kom">// Sma�e objekt bitmapy</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>V inicializa�n�m k�du nen� nic moc zaj�mav�ho. Pou�ijeme funkci LoadGLTextures(), abychom zavolali k�d, kter� jsme pr�v� napsali. Nastav�me pozad� na �ernou barvu. Vy�ad�me depth testing (jednoduch� blending). Povol�me texturov�n�, nastav�me a povol�me blending.</p>

<p class="src0">BOOL Initialize (GL_Window* window, Keys* keys)<span class="kom">// Inicializa�n� k�d a nastaven�</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;</p>
<p class="src1">g_keys = keys;</p>
<p class="src"></p>
<p class="src1">LoadGLTextures();<span class="kom">// Nahraje textury ze zdroj�</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src"></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypnut� hloubkov�ho testov�n�</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Vyhlazen� st�nov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// V�po�et perspektivy na nejvy��� kvalitu</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Povol� texturov� mapov�n�</span></p>
<p class="src"></p>
<p class="src1">glBlendFunc(GL_ONE,GL_SRC_ALPHA);<span class="kom">// Nastaven� blendingu (nen�ro�n� / rychl�)</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Povolen� blendingu</span></p>

<p>Hned na za��tku pot�ebujeme inicializovat 50 objekt� tak, aby se neobjevily uprost�ed obrazovky nebo v�echny na stejn�m m�st�. I tuto funkci u� m�me napsanou. Zavol�me ji pades�tkr�t.</p>

<p class="src1">for (int loop = 0; loop &lt; 50; loop++)<span class="kom">// Inicializace 50 mot�l�</span></p>
<p class="src1">{</p>
<p class="src2">SetObject(loop);<span class="kom">// Nastaven� n�hodn�ch hodnot</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace �sp�n�</span></p>
<p class="src0">}</p>

<p>Deinicializaci tentokr�t nevyu�ijeme.</p>

<p class="src0">void Deinitialize (void)<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src0">}</p>

<p>N�sleduj�c� funkce o�et�uje stisk kl�ves ESC a F1. Periodicky ji vol�me v hlavn� smy�ce programu.</p>

<p class="src0">void Update (DWORD milliseconds)<span class="kom">// Vykon�v� aktualizace</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown [VK_ESCAPE] == TRUE)<span class="kom">// Stisknuta kl�vesa ESC?</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication(g_window);<span class="kom">// Ukon�� program</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown [VK_F1] == TRUE)<span class="kom">// Stisknuta kl�vesa F1?</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// Prohod� m�d fullscreen/okno</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Te� k vykreslov�n�. Pokus�m se vysv�tlit nejjednodu��� zp�sob, jak otexturovat jedn�m obr�zkem dva troj�heln�ky. Z n�jak�ho d�vodu si mnoz� mysl�, �e namapovat texturu na troj�heln�k je tak�ka nemo�n�. Pravdou je, �e s velmi malou n�mahou m��ete otexturovat libovoln� tvar. Obr�zek m��e tvaru odpov�dat, nebo m��e b�t tot�ln� odli�n�. Je to �pln� jedno.</p>

<p>Tak od za��tku... vyma�eme obrazovku a deklarujeme cyklus na renderov�n� mot�lk� (objekt�).</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslen� sc�ny</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src"></p>
<p class="src1">for (int loop = 0; loop &lt; 50; loop++)<span class="kom">// Projde 50 mot�lk�</span></p>
<p class="src1">{</p>

<p>Zavol�me glLoadIdentity() pro resetov�n� matice. Pak vybereme texturu, kter� byla p�i inicializaci ur�ena pro dan� objekt (obj[loop].tex). Um�st�me mot�lka pomoc� glTranslatef() a oto��me ho o 45 stup�� na ose x. T�m ho nato��me trochu k div�kovi, tak�e nevypad� tak placat�. Nakonec ho je�t� oto��me kolem osy z o hodnotu spinz - p�i p�du se bude to�it.</p>

<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[obj[loop].tex]);<span class="kom">// Zvol� texturu</span></p>
<p class="src"></p>
<p class="src2">glTranslatef(obj[loop].x,obj[loop].y,obj[loop].z);<span class="kom">// Um�st�n�</span></p>
<p class="src"></p>
<p class="src2">glRotatef(45.0f, 1.0f,0.0f,0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src2">glRotatef((obj[loop].spinz), 0.0f,0.0f,1.0f);<span class="kom">// Rotace na ose y</span></p>

<p>Texturov�n� troj�heln�ku se neli�� od texturov�n� �tverce. To �e m�me jen 3 body, neznamen�, �e nem��eme �ty�hrann�m obr�zkem otexturovat troj�heln�k. Mus�me si pouze d�vat v�t�� pozor na texturovac� sou�adnice. V n�sleduj�c�m k�du nakresl�me prvn� troj�heln�k. Za�neme v prav�m horn�m rohu viditeln�ho �tverce. Pak se p�esuneme do lev�ho horn�ho rohu a potom do lev�ho doln�ho rohu. K�d vyrenderuje n�sleduj�c� obr�zek:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_triangle1.jpg" width="128" height="128" alt="Prvn� troj�heln�k" /></div>

<p>V�imn�te si, �e na prvn� troj�heln�k se vyrenderuje jen polovina mot�la. Druh� ��st bude pochopiteln� na druh�m troj�heln�ku. Texturovac� sou�adnice odpov�daj� tomu, jak jsme texturovali �tverce. T�i sou�adnice sta�� OpenGL k tomu, aby rozpoznalo jakou ��st obr�zku m� na troj�heln�k namapovat.</p>

<p class="src2">glBegin(GL_TRIANGLES);<span class="kom">// Kreslen� troj�heln�k�</span></p>
<p class="src3"><span class="kom">// Prvn� troj�heln�k</span></p>
<p class="src3">glTexCoord2f(1.0f,1.0f); glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Prav� horn� bod</span></p>
<p class="src3">glTexCoord2f(0.0f,1.0f); glVertex3f(-1.0f, 1.0f, obj[loop].flap);<span class="kom">// Lev� horn� bod</span></p>
<p class="src3">glTexCoord2f(0.0f,0.0f); glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>

<p>Dal�� ��st k�du vyrenderuje druh� troj�heln�k stejn�m zp�sobem jako p�edt�m. Za�neme vpravo naho�e, pak p�jdeme vlevo dol� a nakonec vpravo dol�.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_triangle2.jpg" width="128" height="128" alt="Druh� troj�heln�k" /></div>

<p>Druh� bod prvn�ho a t�et� bod druh�ho troj�heln�ku se posunuj� zp�t po ose z, aby se vytvo�ila iluze m�v�n� k��dly. To, co se ve skute�nosti d�je, je pouze posouv�n� t�chto bod� tam a zp�tky od -1.0f do 1.0f, co� zp�sobuje oh�ban� v m�stech, kde m� mot�l t�lo. Pokud se na oba tyto body pod�v�te, zjist�te, �e jsou to ro�ky k��del. Takto vytvo��me p�kn� efekt s minimem n�mahy.</p>

<p class="src3"><span class="kom">// Druh� troj�heln�k</span></p>
<p class="src3">glTexCoord2f(1.0f,1.0f); glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Prav� horn� bod</span></p>
<p class="src3">glTexCoord2f(0.0f,0.0f); glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src3">glTexCoord2f(1.0f,0.0f); glVertex3f( 1.0f,-1.0f, obj[loop].flap);<span class="kom">// Prav� doln� bod</span></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Posuneme mot�ly sm�rem dol� ode�ten�m obj[loop].yi od obj[loop].y. Mot�lovo oto�en� spinz se zv��� o spinzi (co� m��e b�t kladn� i z�porn� ��slo) a ohyb k��del se zv��� o fi. Fi m��e b�t rovn� kladn�, nebo z�porn� podle sm�ru kam se k��dla pohybuj�.</p>

<p class="src2">obj[loop].y -= obj[loop].yi;<span class="kom">// P�d mot�la dol�</span></p>
<p class="src2">obj[loop].spinz += obj[loop].spinzi;<span class="kom">// Zv��en� nato�en� na ose z o spinzi</span></p>
<p class="src2">obj[loop].flap += obj[loop].fi;<span class="kom">// Zv�t�en� m�chnut� k��dlem o fi</span></p>

<p>Potom co se mot�l p�esune dol� mimo viditelnou oblast, zavol�me funkci SetObject(loop) na tohoto mot�la, aby se znovu nastavila n�hodn� textura, pozice, rychlost,... Jednodu�e �e�eno: vytvo��me nov�ho mot�la v horn� ��sti sc�ny, kter� bude op�t padat dol�.</p>

<p class="src2">if (obj[loop].y &lt; -18.0f)<span class="kom">// Je mot�l mimo obrazovku?</span></p>
<p class="src2">{</p>
<p class="src3">SetObject(loop);<span class="kom">// Nastav�me mu nov� parametry</span></p>
<p class="src2">}</p>

<p>Aby mot�l k��dly skute�n� m�val, mus�me zkontrolovat, jestli hodnota m�vnut� nen� v�t�� ne� 1.0f nebo men�� ne� -1.0f. Pokud ano, zm�n�me sm�r m�vnut� jednodu�e nastaven�m fi na opa�nou hodnotu (fi = -fi). Tak�e pokud se k��dla pohybuj� nahoru a dos�hnou 1.0f, fi se zm�n� na z�porn� ��slo a k��dla p�jdou dol�.</p>

<p class="src2">if ((obj[loop].flap &gt; 1.0f) || (obj[loop].flap &lt; -1.0f))<span class="kom">// M�me zm�nit sm�r m�vnut� k��dly</span></p>
<p class="src2">{</p>
<p class="src3">obj[loop].fi = -obj[loop].fi;<span class="kom">// Zm�n� sm�r m�vnut�</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Sleep(15) bylo p�id�no, aby pozastavilo program na 15 milisekund. Na po��ta��ch p��tel b�el zb�sile rychle a m� se necht�lo nijak upravovat program, tak�e jsem jednodu�e pou�il tuto funkci. Nicm�n� osobn� jej� pou�it� ze z�sady nedoporu�uji, proto�e se zbyte�n� pl�tv� v�po�etn�m v�konem procesoru.</p>

<p class="src1">Sleep(15);<span class="kom">// Pozastaven� programu na 15 milisekund</span></p>
<p class="src"></p>
<p class="src1">glFlush ();<span class="kom">// Vypr�zdn� renderovac� pipeline</span></p>
<p class="src0">}</p>

<p>Douf�m, �e jste si u�ili tento tutori�l. Snad pro v�s ud�l� nahr�v�n� textur ze zdroj� programu trochu jednodu���m na pochopen� a texturov�n� troj�heln�k� rovn�. P�e�etl jsem tento tutori�l snad 5kr�t a zd� se mi te� u� dost jednoduch�.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: V�clav Slov��ek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson38.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson38_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson38.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson38.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson38.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:alex_r@vortexentertainment.com">Alexandre Ribeiro de S?</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson38.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson38.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson38.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/extras/lesson38/lesson38 - enhanced.zip">Lesson 38 - Enhanced</a> (Masking, Sorting, Keyboard - NeHe).</li>
<li><a href="http://nehe.gamedev.net/data/lessons/extras/lesson38/lesson38 - screensaver.zip">Lesson 38 - Screensaver</a> by Brian Hunsucker.</li>
</ul>

<?FceImgNeHeVelky(38);?>
<?FceNeHeOkolniLekce(38);?>

<?
include 'p_end.php';
?>
