<?
$g_title = 'CZ NeHe OpenGL - Lekce 38 - Nahrávání textur z resource souboru &amp; texturování trojúhelníkù';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(38);?>

<h1>Lekce 38 - Nahrávání textur z resource souboru &amp; texturování trojúhelníkù</h1>

<p class="nadpis_clanku">Tento tutoriál jsem napsal pro v¹echny z vás, kteøí se mì v emailech dotazovali na to &quot;Jak mám loadovat texturu ze zdrojù programu, abych mìl v¹echny obrázky ulo¾ené ve výsledném .exe souboru?&quot; a také pro ty, kteøí psali &quot;Vím, jak otexturovat obdélník, ale jak mapovat na trojúhelník?&quot; Tutoriál není, oproti jiným, extrémnì pokrokový, ale kdy¾ nic jiného, tak se nauèíte, jak skrýt va¹e precizní textury pøed okem u¾ivatele. A co víc - budete moci trochu ztí¾it jejich kradení :-)</p>

<p>Tak u¾ víte, jak otexturovat ètverec, jak nahrát bitmapu, tga,... Tak jak kruci otexturovat trojúhelník? A co kdy¾ chci textury ukrýt do .exe souboru? Kdy¾ zjistíte, jak je to jednoduché, budete se divit, ¾e vás øe¹ení u¾ dávno nenapadlo.</p>

<p>Radìji ne¾ abych v¹e do detailù vysvìtloval, pøedvedu pár screenshotù, tak¾e budete pøesnì vìdìt, o èem mluvím. Budu pou¾ívat nejnovìj¹í základní kód, který si mù¾ete na <?OdkazBlank('http://nehe.gamedev.net/');?> pod nadpisem "NeHeGL Basecode" a nebo kliknutím na odkaz na konci tohoto tutoriálu.</p>

<p>První co potøebujeme udìlat, je pøidat obrázky do zdrojového souboru (resource file). Mnoho z vás u¾ zjistilo, jak to udìlat, ale nane¹tìstí jste èasto opominuli nìkolik krokù, a proto skonèili s nepou¾itelným zdrojovým souborem naplnìným bitmapami, které nejdou pou¾ít.</p>

<p>Tento tutoriál je napsán pro Visual C++ 6.0. Pokud pou¾íváte nìco jiného, tato èást tutoriálu je pro vás zbyteèná, obzvlá¹tì obrázky prostøedí Visual C++.</p>

<p>Momentálnì budete schopni nahrát pouze 24-bitové BMP. K nahrání 8-bitového BMP bychom potøebovali mnoho kódu navíc. Rád bych vìdìl o nìkom, kdo má malý optimalizovaný BMP loader. Kód, který mám k souèasnému naèítání 8 a 24-bitových BMP je prostì pøí¹erný. Nìco, co pou¾ívá LoadImage, by se hodilo.</p>

<p>Tak tedy zaèneme...</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource1.jpg" width="480" height="360" alt="Resource 1" /></div>

<p>Otevøete projekt a vyberte z hlavního menu Insert-&gt;Resource.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource2.jpg" width="351" height="282" alt="Resource 2" /></div>

<p>Jste dotázáni na typ zdroje, který si pøejete importovat. Vyberte Bitmap a kliknìte na tlaèítko Import.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource3.jpg" width="428" height="304" alt="Resource 3" /></div>

<p>Otevøe se prohlí¾eè souborù. Vstupte do slo¾ky Data a oznaète v¹echny 3 bitmapy (podr¾te Ctrl kdy¾ je budete oznaèovat). Pak kliknìte na tlaèítko Import. Pokud nevidíte soubory bitmap, ujistìte se, ¾e v poli Files of type je vybráno All Files(*.*).</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource4.jpg" width="480" height="85" alt="Resource 4" /></div>

<p>Tøikrát se zobrazí varovná zpráva (jednou za ka¾dý obrázek). V¹e co vám øíká je, ¾e obrázky byly v poøádku importovány, ale nemù¾ete je upravovat, proto¾e mají více ne¾ 256 barev. ®ádný dùvod ke starostem!</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource5.jpg" width="480" height="360" alt="Resource 5" /></div>

<p>Kdy¾ jsou v¹echny obrázky importovány, zobrazí se jejich seznam. Ka¾dá bitmapa dostane své identifikaèní jméno (ID), které zaèíná na IDB_BITMAP a následuje èíslo 1 - 3. Pokud jste líní, mohli byste to nechat tak a vrhnout se na kód této lekce. ( My ale nejsme líní!</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource6.jpg" width="480" height="360" alt="Resource 6" /></div>

<p>Pravým tlaèítkem kliknìte na ka¾dé ID a vyberte z menu polo¾ku Properties. Pøejmenujte identifikaèní jména na pùvodní názvy souborù.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource7.jpg" width="428" height="304" alt="Resource 7" /></div>

<p>Teï, kdy¾ jsme hotovi, vyberte z hlavního menu File-&gt;Save All. Proto¾e jste právì vytvoøili nový zdrojový soubor, budete dotázáni na to, jak chcete soubor pojmenovat. Mù¾ete soubor pojmenovat, jak chcete. Jakmile vyplníte jméno souboru kliknìte na tlaèítko Save.</p>

<p>A¾ sem se hodnì z vás propracovalo. Máte zdrojový soubor plný bitmapových obrázkù a u¾ jste ho i ulo¾ili na disk. Abyste v¹ak obrázky mohli pou¾ít, musíte udìlat je¹tì pár vìcí.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource8.jpg" width="480" height="360" alt="Resource 8" /></div>

<p>Dále musíte pøidat soubor se zdroji do aktuálního projektu. Z hlavního menu vyberte Project-&gt;Add To Project-&gt;Files.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource9.jpg" width="428" height="304" alt="Resource 9" /></div>

<p>Vyberte resource.h a vá¹ zdrojový soubor s bitmapami. Podr¾te Ctrl pro výbìr víc souborù, nebo je pøidejte samostatnì.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_resource10.jpg" width="480" height="360" alt="Resource 10" /></div>

<p>Poslední vìc, kterou je tøeba udìlat, je kontrola, zda je zdrojový soubor ve slo¾ce Resource Files. Jak vidíte na obrázku, byl pøidán do slo¾ky Source Files. Kliknìte na nìho a pøetáhnìte ho do slo¾ky Resource Files.</p>

<p>Kdy¾ je v¹e hotovo. Vyberte z hlavního menu File-&gt;Save All. Máme to tì¾¹í za sebou!</p>

<p>Vrhneme na kód! Nejdùle¾itìj¹í øádek v kódu je #include &quot;resource.h&quot;. Bez tohoto øádku vám kompiler pøi kompilování vrátí chybu &quot;undeclared identifier&quot;. Resource.h umo¾òuje pøístup k importovaným obrázkùm.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src"></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavièkový soubor pro GLu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavièkový soubor pro GLaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">#include &quot;NeHeGL.h&quot;<span class="kom">// Hlavièkový soubor pro NeHeGL</span></p>
<p class="src0">#include &quot;resource.h&quot;<span class="kom">// Hlavièkový soubor pro Resource (*DÙLE®ITÉ*)</span></p>
<p class="src"></p>
<p class="src0">#pragma comment( lib, &quot;opengl32.lib&quot; )<span class="kom">// Pøilinkuje OpenGL32.lib</span></p>
<p class="src0">#pragma comment( lib, &quot;glu32.lib&quot; )<span class="kom">// Pøilinkuje GLu32.lib</span></p>
<p class="src0">#pragma comment( lib, &quot;glaux.lib&quot; )<span class="kom">// Pøilinkuje GLaux.lib</span></p>
<p class="src"></p>
<p class="src0">#ifndef CDS_FULLSCREEN<span class="kom">// Pokud je¹tì CDS_FULLSCREEN není definován</span></p>
<p class="src1">#define CDS_FULLSCREEN 4<span class="kom">// Tak ho nadefinujeme</span></p>
<p class="src0">#endif<span class="kom">// Vyhneme se tak mo¾ným chybám</span></p>
<p class="src"></p>
<p class="src0">GL_Window* g_window;</p>
<p class="src0">Keys* g_keys;</p>
<p class="src"></p>
<p class="src0">GLuint texture[3];<span class="kom">// Místo pro 3 textury</span></p>

<p>Následující struktura bude obsahovat informace o motýlku, se kterým budeme pohybovat po obrazovce. Tex urèuje, jakou texturu na objekt namapujeme. X, y a z udávají pozici objektu v prostoru. Yi bude náhodné èíslo udávající, jak rychle motýl padá k zemi. Spinz se pøi pádu pou¾ije na otáèení okolo osy z. Spinzi udává rychlost této rotace. Flap bude pou¾ito pro mávání køídly (k tomu se pozdìji je¹tì vrátíme). Fi bude udávat jak rychle objekt mává køídly.</p>

<p class="src0">struct object<span class="kom">// Struktura nazvaná object</span></p>
<p class="src0">{</p>
<p class="src1">int tex;<span class="kom">// Kterou texturu namapovat</span></p>
<p class="src1">float x;<span class="kom">// X Pozice</span></p>
<p class="src1">float y;<span class="kom">// Y Pozice</span></p>
<p class="src1">float z;<span class="kom">// Z Pozice</span></p>
<p class="src1">float yi;<span class="kom">// Rychlost pádu</span></p>
<p class="src1">float spinz;<span class="kom">// Úhel otoèení kolem osy z</span></p>
<p class="src1">float spinzi;<span class="kom">// Rychlost otáèení kolem osy z</span></p>
<p class="src1">float flap;<span class="kom">// Mávání køídly</span></p>
<p class="src1">float fi;<span class="kom">// Smìr mávání</span></p>
<p class="src0">};</p>

<p>Vytvoøíme padesát tìchto objektù pojmenovaných obj[index].</p>

<p class="src0">object obj[50];<span class="kom">// Vytvoøí 50 objektù na bázi struktury</span></p>

<p>Následující èást kódu nastavuje náhodné hodnoty v¹em objektùm. Loop se bude pohybovat mezi 0 - 49 (celkem 50 objektù). Nejdøíve vybereme náhodnou texturu od 0 do 2, aby nebyli v¹ichni stejní. Potom nastavíme náhodnou pozici x od -17.0f do 17.0f. Poèáteèní pozice y bude 18.0f. Tím zajistíme, ¾e se objekt vytvoøí mimo obrazovku, tak¾e ho nevidíme úplnì od zaèátku. Pozice z je rovnì¾ náhodná hodnota od -10.0f do -40.0f. Spinzi opìt je náhodná hodnota od -1.0f do 1.0f. Flap nastavíme na 0.0f (køídla budou pøesnì uprostøed). Fi a yi nastavíme taky na náhodné hodnoty.</p>

<p class="src0">void SetObject(int loop)<span class="kom">// Nastavení základních vlastností objektu</span></p>
<p class="src0">{</p>
<p class="src1">obj[loop].tex = rand() % 3;<span class="kom">// Výbìr jedné ze tøí textur</span></p>
<p class="src"></p>
<p class="src1">obj[loop].x = rand() % 34 - 17.0f;<span class="kom">// Náhodné x od -17.0f do 17.0f</span></p>
<p class="src1">obj[loop].y = 18.0f;<span class="kom">// Pozici y nastavíme na 18 (nad obrazovku)</span></p>
<p class="src1">obj[loop].z = -((rand() % 30000 / 1000.0f) + 10.0f);<span class="kom">// Náhodné z od -10.0f do -40.0f</span></p>
<p class="src"></p>
<p class="src1">obj[loop].spinzi = (rand() % 10000) / 5000.0f - 1.0f;<span class="kom">// Spinzi je náhodné èíslo od -1.0f do 1.0f</span></p>
<p class="src1">obj[loop].flap = 0.0f;<span class="kom">// Flap zaène na 0.0f</span></p>
<p class="src"></p>
<p class="src1">obj[loop].fi = 0.05f + (rand() % 100) / 1000.0f;<span class="kom">// Fi je náhodné èíslo od 0.05f do 0.15f</span></p>
<p class="src1">obj[loop].yi = 0.001f + (rand() % 1000) / 10000.0f;<span class="kom">// Yi je náhodné èíslo od 0.001f do 0.101f</span></p>
<p class="src0">}</p>

<p>Teï k té zábavnìj¹í èásti. Nahrání bitmapy ze zdrojového souboru a její pøemìna na texturu. hBMP je ukazatel na soubor s bitmapami. Øekne na¹emu programu odkud má brát data. BMP je bitmapová struktura, do které mù¾eme ulo¾it data z na¹eho zdrojového souboru.</p>

<p class="src0">void LoadGLTextures()<span class="kom">// Vytvoøí textury z bitmap ve zdrojovém souboru</span></p>
<p class="src0">{</p>
<p class="src1">HBITMAP hBMP;<span class="kom">// Ukazatel na bitmapu</span></p>
<p class="src1">BITMAP BMP;<span class="kom">// Struktura bitmapy</span></p>

<p>Øekneme jaké identifikaèní jména chceme pou¾ít. Chceme nahrát IDB_BUTTEFLY1, IDB_BUTTEFLY2 a IDB_BUTTERFLY3. Pokud chcete pøidat více obrázkù, pøipi¹te jejich ID.</p>

<p class="src1">byte Texture[] = { IDB_BUTTERFLY1, IDB_BUTTERFLY2, IDB_BUTTERFLY3 };<span class="kom">// ID bitmap, které chceme naèíst</span></p>

<p>Na dal¹ím øádku pou¾ijeme sizeof(Texture) na zji¹tìní, kolik textur chceme sestavit. V Texture[] máme zadány 3 identifikaèní èísla, tak¾e výsledkem sizeof(Texture) bude hodnota bude 3.</p>

<p class="src1">glGenTextures(sizeof(Texture), &amp;texture[0]);<span class="kom">// Vygenerování tøí textur, sizeof(Texture) = 3 ID</span></p>
<p class="src"></p>
<p class="src1">for (int loop = 0; loop &lt; sizeof(Texture); loop++)<span class="kom">// Projde v¹echny bitmapy ve zdrojích</span></p>
<p class="src1">{</p>

<p>LoadImage() pøijímá parametry GetModuleHandle(NULL) - handle instance. MAKEINTRESOURCE(Texture[loop]) pøemìní hodnotu celého èísla Texture[loop] na hodnotu zdroje (obrázku, který má být naèten). Tady je nutné poznamenat, ¾e sice pou¾íváme identifikaèní jméno napø. IDB_BUTTERFLY1, ale v souboru Resource.h je napsáno nìco ve stylu #define IDB_BUTTERFLY1 115, my se tím ale nemusíme vùbec zabývat. Vývojové prostøedí v¹e automatizuje. IMAGE_BITMAP øíká na¹emu programu, ¾e zdroj, který chceme naèíst je bitmapový obrázek.</p>

<p>Dal¹í dva parametry (0,0) jsou po¾adovaná vý¹ka a ¹íøka obrázku. Chceme pou¾ít implicitní velikost, tak nastavíme obì na 0. Poslední parametr (LR_CREATEDIBSECTION) vrátí DIB èást mapy, která obsahuje jen bitmapu bez informací o barvách v hlavièce. Pøesnì to, co chceme.</p>

<p>hBMP bude ukazatelem na na¹e bitmapová data nahraná pomocí LoadImage().</p>

<p class="src2">hBMP = (HBITMAP) LoadImage(GetModuleHandle(NULL), MAKEINTRESOURCE(Texture[loop]), IMAGE_BITMAP, 0, 0, LR_CREATEDIBSECTION);<span class="kom">// Nahraje bitmapu ze zdrojù</span></p>

<p>Dále zkontrolujeme, zda pointer hBMP opravdu ukazuje na data. Pokud byste chtìli pøidat o¹etøení chyb, mù¾ete zkontrolovat hBMP a zobrazit chybové hlá¹ení. Pokud ale data existují, pou¾ijeme funkci getObject() na získání v¹ech dat o velikosti sizeof(BMP) a jejich ulo¾ení do bitmapové struktury &amp;BMP.</p>

<p class="src2">if (hBMP)<span class="kom">// Pokud existuje bitmapa</span></p>
<p class="src2">{</p>
<p class="src3">GetObject(hBMP, sizeof(BMP), &amp;BMP);<span class="kom">// Získání objektu</span></p>

<p>glPixelStorei() oznámí OpenGL, ¾e data jsou ulo¾ena ve formátu 4 byty na pixel. Nastavíme filtrování na GL_LINEAR a GL_LINEAR_MIPMAP_LINEAR (kvalitní a vyhlazené) a vygenerujeme texturu.</p>

<p class="src3">glPixelStorei(GL_UNPACK_ALIGNMENT,4);<span class="kom">// 4 byty na jeden pixel</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop]);<span class="kom">// Zvolí texturu</span></p>
<p class="src"></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);<span class="kom">// Lineární filtrování</span></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR_MIPMAP_LINEAR); <span class="kom">// Mipmapované lineární filtrování</span></p>

<p>V¹imnìte si, ¾e pou¾íváme BMP.bmWidth a BMP.bmHeight, abychom získali vý¹ku a ¹íøku bitmapy. Také musíme pou¾itím GL_BGR_EXT prohodit èervenou a modrou barvu. Data získáme z BMP.bmBits.</p>

<p class="src3"><span class="kom">// Vygenerování mipmapované textury (3 byty, ¹íøka, vý¹ka a BMP data)</span></p>
<p class="src3">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, BMP.bmWidth, BMP.bmHeight, GL_BGR_EXT, GL_UNSIGNED_BYTE, BMP.bmBits);</p>

<p>Posledním krokem je smazání objektu bitmapy, abychom uvolnili v¹echny systémové prostøedky spojené s tímto objektem.</p>

<p class="src3">DeleteObject(hBMP);<span class="kom">// Sma¾e objekt bitmapy</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>V inicializaèním kódu není nic moc zajímavého. Pou¾ijeme funkci LoadGLTextures(), abychom zavolali kód, který jsme právì napsali. Nastavíme pozadí na èernou barvu. Vyøadíme depth testing (jednoduchý blending). Povolíme texturování, nastavíme a povolíme blending.</p>

<p class="src0">BOOL Initialize (GL_Window* window, Keys* keys)<span class="kom">// Inicializaèní kód a nastavení</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;</p>
<p class="src1">g_keys = keys;</p>
<p class="src"></p>
<p class="src1">LoadGLTextures();<span class="kom">// Nahraje textury ze zdrojù</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src"></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypnutí hloubkového testování</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Vyhlazené stínování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Výpoèet perspektivy na nejvy¹¹í kvalitu</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Povolí texturové mapování</span></p>
<p class="src"></p>
<p class="src1">glBlendFunc(GL_ONE,GL_SRC_ALPHA);<span class="kom">// Nastavení blendingu (nenároèný / rychlý)</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Povolení blendingu</span></p>

<p>Hned na zaèátku potøebujeme inicializovat 50 objektù tak, aby se neobjevily uprostøed obrazovky nebo v¹echny na stejném místì. I tuto funkci u¾ máme napsanou. Zavoláme ji padesátkrát.</p>

<p class="src1">for (int loop = 0; loop &lt; 50; loop++)<span class="kom">// Inicializace 50 motýlù</span></p>
<p class="src1">{</p>
<p class="src2">SetObject(loop);<span class="kom">// Nastavení náhodných hodnot</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace úspì¹ná</span></p>
<p class="src0">}</p>

<p>Deinicializaci tentokrát nevyu¾ijeme.</p>

<p class="src0">void Deinitialize (void)<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src0">}</p>

<p>Následující funkce o¹etøuje stisk kláves ESC a F1. Periodicky ji voláme v hlavní smyèce programu.</p>

<p class="src0">void Update (DWORD milliseconds)<span class="kom">// Vykonává aktualizace</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown [VK_ESCAPE] == TRUE)<span class="kom">// Stisknuta klávesa ESC?</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication(g_window);<span class="kom">// Ukonèí program</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown [VK_F1] == TRUE)<span class="kom">// Stisknuta klávesa F1?</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// Prohodí mód fullscreen/okno</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Teï k vykreslování. Pokusím se vysvìtlit nejjednodu¹¹í zpùsob, jak otexturovat jedním obrázkem dva trojúhelníky. Z nìjakého dùvodu si mnozí myslí, ¾e namapovat texturu na trojúhelník je takøka nemo¾né. Pravdou je, ¾e s velmi malou námahou mù¾ete otexturovat libovolný tvar. Obrázek mù¾e tvaru odpovídat, nebo mù¾e být totálnì odli¹ný. Je to úplnì jedno.</p>

<p>Tak od zaèátku... vyma¾eme obrazovku a deklarujeme cyklus na renderování motýlkù (objektù).</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslení scény</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src"></p>
<p class="src1">for (int loop = 0; loop &lt; 50; loop++)<span class="kom">// Projde 50 motýlkù</span></p>
<p class="src1">{</p>

<p>Zavoláme glLoadIdentity() pro resetování matice. Pak vybereme texturu, která byla pøi inicializaci urèena pro daný objekt (obj[loop].tex). Umístíme motýlka pomocí glTranslatef() a otoèíme ho o 45 stupòù na ose x. Tím ho natoèíme trochu k divákovi, tak¾e nevypadá tak placatì. Nakonec ho je¹tì otoèíme kolem osy z o hodnotu spinz - pøi pádu se bude toèit.</p>

<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[obj[loop].tex]);<span class="kom">// Zvolí texturu</span></p>
<p class="src"></p>
<p class="src2">glTranslatef(obj[loop].x,obj[loop].y,obj[loop].z);<span class="kom">// Umístìní</span></p>
<p class="src"></p>
<p class="src2">glRotatef(45.0f, 1.0f,0.0f,0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src2">glRotatef((obj[loop].spinz), 0.0f,0.0f,1.0f);<span class="kom">// Rotace na ose y</span></p>

<p>Texturování trojúhelníku se neli¹í od texturování ètverce. To ¾e máme jen 3 body, neznamená, ¾e nemù¾eme ètyøhranným obrázkem otexturovat trojúhelník. Musíme si pouze dávat vìt¹í pozor na texturovací souøadnice. V následujícím kódu nakreslíme první trojúhelník. Zaèneme v pravém horním rohu viditelného ètverce. Pak se pøesuneme do levého horního rohu a potom do levého dolního rohu. Kód vyrenderuje následující obrázek:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_triangle1.jpg" width="128" height="128" alt="První trojúhelník" /></div>

<p>V¹imnìte si, ¾e na první trojúhelník se vyrenderuje jen polovina motýla. Druhá èást bude pochopitelnì na druhém trojúhelníku. Texturovací souøadnice odpovídají tomu, jak jsme texturovali ètverce. Tøi souøadnice staèí OpenGL k tomu, aby rozpoznalo jakou èást obrázku má na trojúhelník namapovat.</p>

<p class="src2">glBegin(GL_TRIANGLES);<span class="kom">// Kreslení trojúhelníkù</span></p>
<p class="src3"><span class="kom">// První trojúhelník</span></p>
<p class="src3">glTexCoord2f(1.0f,1.0f); glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Pravý horní bod</span></p>
<p class="src3">glTexCoord2f(0.0f,1.0f); glVertex3f(-1.0f, 1.0f, obj[loop].flap);<span class="kom">// Levý horní bod</span></p>
<p class="src3">glTexCoord2f(0.0f,0.0f); glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>

<p>Dal¹í èást kódu vyrenderuje druhý trojúhelník stejným zpùsobem jako pøedtím. Zaèneme vpravo nahoøe, pak pùjdeme vlevo dolù a nakonec vpravo dolù.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_38_triangle2.jpg" width="128" height="128" alt="Druhý trojúhelník" /></div>

<p>Druhý bod prvního a tøetí bod druhého trojúhelníku se posunují zpìt po ose z, aby se vytvoøila iluze mávání køídly. To, co se ve skuteènosti dìje, je pouze posouvání tìchto bodù tam a zpátky od -1.0f do 1.0f, co¾ zpùsobuje ohýbaní v místech, kde má motýl tìlo. Pokud se na oba tyto body podíváte, zjistíte, ¾e jsou to ro¾ky køídel. Takto vytvoøíme pìkný efekt s minimem námahy.</p>

<p class="src3"><span class="kom">// Druhý trojúhelník</span></p>
<p class="src3">glTexCoord2f(1.0f,1.0f); glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Pravý horní bod</span></p>
<p class="src3">glTexCoord2f(0.0f,0.0f); glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src3">glTexCoord2f(1.0f,0.0f); glVertex3f( 1.0f,-1.0f, obj[loop].flap);<span class="kom">// Pravý dolní bod</span></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslení</span></p>

<p>Posuneme motýly smìrem dolù odeètením obj[loop].yi od obj[loop].y. Motýlovo otoèení spinz se zvý¹í o spinzi (co¾ mù¾e být kladné i záporné èíslo) a ohyb køídel se zvý¹í o fi. Fi mù¾e být rovnì¾ kladné, nebo záporné podle smìru kam se køídla pohybují.</p>

<p class="src2">obj[loop].y -= obj[loop].yi;<span class="kom">// Pád motýla dolù</span></p>
<p class="src2">obj[loop].spinz += obj[loop].spinzi;<span class="kom">// Zvý¹ení natoèení na ose z o spinzi</span></p>
<p class="src2">obj[loop].flap += obj[loop].fi;<span class="kom">// Zvìt¹ení máchnutí køídlem o fi</span></p>

<p>Potom co se motýl pøesune dolù mimo viditelnou oblast, zavoláme funkci SetObject(loop) na tohoto motýla, aby se znovu nastavila náhodná textura, pozice, rychlost,... Jednodu¹e øeèeno: vytvoøíme nového motýla v horní èásti scény, které bude opìt padat dolù.</p>

<p class="src2">if (obj[loop].y &lt; -18.0f)<span class="kom">// Je motýl mimo obrazovku?</span></p>
<p class="src2">{</p>
<p class="src3">SetObject(loop);<span class="kom">// Nastavíme mu nové parametry</span></p>
<p class="src2">}</p>

<p>Aby motýl køídly skuteènì mával, musíme zkontrolovat, jestli hodnota mávnutí není vìt¹í ne¾ 1.0f nebo men¹í ne¾ -1.0f. Pokud ano, zmìníme smìr mávnutí jednodu¹e nastavením fi na opaènou hodnotu (fi = -fi). Tak¾e pokud se køídla pohybují nahoru a dosáhnou 1.0f, fi se zmìní na záporné èíslo a køídla pùjdou dolù.</p>

<p class="src2">if ((obj[loop].flap &gt; 1.0f) || (obj[loop].flap &lt; -1.0f))<span class="kom">// Máme zmìnit smìr mávnutí køídly</span></p>
<p class="src2">{</p>
<p class="src3">obj[loop].fi = -obj[loop].fi;<span class="kom">// Zmìní smìr mávnutí</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Sleep(15) bylo pøidáno, aby pozastavilo program na 15 milisekund. Na poèítaèích pøátel bì¾el zbìsile rychle a mì se nechtìlo nijak upravovat program, tak¾e jsem jednodu¹e pou¾il tuto funkci. Nicménì osobnì její pou¾ití ze zásady nedoporuèuji, proto¾e se zbyteènì plýtvá výpoèetním výkonem procesoru.</p>

<p class="src1">Sleep(15);<span class="kom">// Pozastavení programu na 15 milisekund</span></p>
<p class="src"></p>
<p class="src1">glFlush ();<span class="kom">// Vyprázdní renderovací pipeline</span></p>
<p class="src0">}</p>

<p>Doufám, ¾e jste si u¾ili tento tutoriál. Snad pro vás udìlá nahrávání textur ze zdrojù programu trochu jednodu¹¹ím na pochopení a texturování trojúhelníkù rovnì¾. Pøeèetl jsem tento tutoriál snad 5krát a zdá se mi teï u¾ dost jednoduchý.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Václav Slováèek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson38.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson38_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson38.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson38.zip">Dev C++</a> kód této lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson38.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:alex_r@vortexentertainment.com">Alexandre Ribeiro de S?</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson38.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson38.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson38.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/extras/lesson38/lesson38 - enhanced.zip">Lesson 38 - Enhanced</a> (Masking, Sorting, Keyboard - NeHe).</li>
<li><a href="http://nehe.gamedev.net/data/lessons/extras/lesson38/lesson38 - screensaver.zip">Lesson 38 - Screensaver</a> by Brian Hunsucker.</li>
</ul>

<?FceImgNeHeVelky(38);?>
<?FceNeHeOkolniLekce(38);?>

<?
include 'p_end.php';
?>
