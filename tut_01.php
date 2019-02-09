<?
$g_title = 'CZ NeHe OpenGL - Lekce 1 - Vytvoøení OpenGL okna ve Windows';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(1);?>

<h1>Lekce 1 - Vytvoøení OpenGL okna ve Windows</h1>

<p class="nadpis_clanku">Nauèíte se jak nastavit a vytvoøit OpenGL okno ve Windows. Program, který vytvoøíte zobrazí "pouze" prázdné okno. Èerné pozadí nevypadá nic moc, ale pokud porozumíte této lekci, budete mít velmi dobrý základ pro jakoukoliv dal¹í práci. Zjistíte jak OpenGL pracuje, jak probíhá vytváøení okna a také jak napsat jednodu¹e pochopitelný kód.</p>

<p>Jsem obyèejný kluk s vá¹ní pro OpenGL. Kdy¾ jsem o nìm poprvé sly¹el, vydalo 3Dfx zrychlené ovladaèe pro Voodoo 1. Hned jsem vìdìl, ¾e OpenGL je nìco, co se musím nauèit. Bohu¾el bylo velice tì¾ké najít nìjaké informace, jak v knihách, tak na internetu. Strávil jsem hodiny pokusy o napsání funkèního kódu a pøesvìdèováním lidí emaily a na IRC. Zjistil jsem, ¾e lidé, kteøí rozumìli OpenGL, se pova¾ovali za elitu a nehodlali se o své vìdomosti dìlit. Velice frustrující... Vytvoøil jsem tyto tutoriály, aby je zájemci o OpenGL mohli pou¾ít, kdy¾ budou potøebovat pomoc. V ka¾dém tutoriálu se v¹e sna¾ím vysvìtlit do detailù, aby bylo jasné, co ka¾dý øádek dìlá. Sna¾ím se svùj kód psát co nejjednodu¹eji (nepou¾ívám MFC)! I absolutní nováèek, jak v C++, tak v OpenGL, by mìl být schopen tento kód zvládnout a mít dal¹í dobré nápady, co dìlat dál. Je mnoho tutoriálù o OpenGL. Pokud jste hardcorový OpenGL programátor asi Vám budou pøipadat pøíli¹ jednoduché, ale pokud právì zaèínáte mají mnoho co nabídnout!</p>

<p>Zaènu tento tutoriál pøímo kódem. První, co se musí udìlat, je vytvoøit projekt. Pokud nevíte jak to udìlat, nemìli byste se uèit OpenGL, ale Visual C++. Nìkteré verze Visual C++ vy¾adují, aby byl bool zmìnìn na BOOL, true na TRUE a false na FALSE. Pokud to budete mít na pamìti nemìly by být s kompilací ¾ádné problémy. Potom co vytvoøíte novou Win32 Application (NE console application) ve Visual C++, budete potøebovat pøipojit OpenGL knihovny. Jsou dvì mo¾nosti, jak to udìlat: Vyberte Project>Settings, pak zvolte zálo¾ku Link a do kolonky Object/Library Modules napi¹te na zaèátek øádku (pøed kernel32.lib) OpenGL32.lib Glu32.lib Glaux.lib. Potom kliknìte na OK. Nebo napi¹te pøímo do kódu programu následující øádky.</p>

<p class="src0"><span class="kom">// Vlo¾ení knihoven</span></p>
<p class="src0">#pragma comment (lib,&quot;opengl32.lib&quot;)</p>
<p class="src0">#pragma comment (lib,&quot;glu32.lib&quot;)</p>
<p class="src0">#pragma comment (lib,&quot;glaux.lib&quot;)</p>

<p>Nyní jste pøipraveni napsat svùj první OpenGL program pro Windows. Zaèneme vlo¾ením hlavièkových souborù.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavièkový soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavièkový soubor pro Glaux knihovnu</span></p>

<p>Dále potøebujete deklarovat globální promìnné, které chcete v programu pou¾ít. Tento program vytváøí prázdné OpenGL okno, proto jich nebudeme potøebovat mnoho. Ty, které nyní pou¾ijeme jsou ov¹em velmi dùle¾ité a budete je pou¾ívat v ka¾dém programu zalo¾eném na tomto kódu. Nastavíme Rendering Context. Ka¾dý OpenGL program je spojen s Rendering Contextem. Rendering Context øíká, která spojení volá OpenGL, aby se spojilo s Device Context (kontext zaøízení). Nám staèí vìdìt, ¾e OpenGL Rendering Context je definován jako hRC. Aby program mohl kreslit do okna potøebujete vytvoøit Device Context. Ve Windows je Device Context definován jako hDC. Device Context napojí okno na GDI (grafické rozhraní). Promìnná hWnd obsahuje handle pøidìlený oknu a ètvrtý øádek vytvoøí instanci programu.</p>

<p class="src0">HDC hDC = NULL;<span class="kom">// Privátní GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trvalý Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na¹eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>

<p>První øádek deklaruje pole, které budeme pou¾ívat na sledování stisknutých kláves. Je mnoho zpùsobù, jak to udìlat, ale takto to dìlám já. Je to spolehlivé a mù¾eme sledovat stisk více kláves najednou. Promìnná active bude pou¾ita, aby ná¹ program informovala, zda je jeho okno minimalizováno nebo ne. Kdy¾ je okno minimalizováno mù¾eme udìlat cokoliv od pozastavení èinnosti kódu a¾ po opu¹tìní programu. Já pou¾iji pozastavení bìhu programu. Díky tomu zbyteènì nepobì¾í na pozadí, kdy¾ bude minimalizován. Promìnná fullscreen bude obsahovat informaci, jestli ná¹ program bì¾í pøes celou obrazovku - v tom pøípadì bude fullscreen mít hodnotu true, kdy¾ program pobì¾í v oknì bude mít hodnotu false. Je dùle¾ité, aby promìnná byla globální a tím pádem ka¾dá funkce vìdìla, jestli program bì¾í ve fullscreenu, nebo v oknì.</p>

<p class="src0">bool keys[256];<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>
<p class="src"></p>
<p class="src0">LRESULT CALLBACK WndProc(HWND, UINT, WPARAM, LPARAM);<span class="kom">// Deklarace procedury okna (funkèní prototyp)</span></p>

<p>Následující funkce se volá v¾dy, kdy¾ u¾ivatel mìní velikost okna. I kdy¾ nejste schopni zmìnit velikost okna (napøíklad ve fullscreenu), bude tato funkce volána alespoò jednou, aby nastavila perspektivní pohled pøi spu¹tìní programu. Velikost OpenGL scény se bude mìnit v závislosti na ¹íøce a vý¹ce okna, ve kterém je zobrazena.</p>

<p class="src0">GLvoid ReSizeGLScene(GLsizei width, GLsizei height)<span class="kom">// Zmìna velikosti a inicializace OpenGL okna
</span></p>
<p class="src0">{</p>
<p class="src1">if (height==0)<span class="kom">// Zabezpeèení proti dìlení nulou</span></p>
<p class="src1">{</p>
<p class="src2">height=1;<span class="kom">// Nastaví vý¹ku na jedna</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glViewport(0,0,width,height);<span class="kom">// Resetuje aktuální nastavení</span></p>

<p>Nastavíme obraz na perspektivní pohled. To znamená, ¾e vzdálenìj¹í objekty budou men¹í. glMatrixMode(GL_PROJECTION) ovlivní formu obrazu. Forma obrazu urèuje, jak výrazná bude perspektiva. Vytvoøíme realisticky vypadající scénu. glLoadIdentity() resetuje matici. Vrátí ji do jejího pùvodního stavu. Po glLoadIdentity() nastavíme perspektivní pohled scény. Perspektiva je vypoèítána s úhlem pohledu 45 stupòù a je zalo¾ena na vý¹ce a ¹íøce okna. Èíslo 0.1f je poèáteèní a 100.0f koneèný bod, který øíká jak hluboko do obrazovky mù¾eme kreslit. glMatrixMode(GL_MODELVIEW) oznamuje, ¾e forma pohledu bude znovu zmìnìna. Nakonec znovu resetujeme matici. Pokud pøedcházejícímu textu nerozumíte, nic si z toho nedìlejte, vysvìtlím ho celý v dal¹ích tutoriálech. Jediné co nyní musíte vìdìt je, ¾e následující øádky musíte do svého programu napsat, pokud chcete, aby scéna vypadala pìknì.</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvolí projekèní matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1"></p>
<p class="src1">gluPerspective(45.0f,(GLfloat)width/(GLfloat)height,0.1f,100.0f);<span class="kom">// Výpoèet perspektivy</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvolí matici Modelview</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src0">}</p>

<p>Nastavíme v¹e potøebné pro OpenGL. Definujeme èerné pozadí, zapneme depth buffer, aktivujeme smooth shading (vyhlazené stínování), atd.. Tato funkce se volá po vytvoøení okna. Vrací hodnotu, ale tím se nyní nemusíme zabývat, proto¾e na¹e inicializace není zatím úplnì komplexní.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echno nastavení OpenGL</span></p>
<p class="src0">{</p>

<p>Následující øádek povolí jemné stínování, aby se barvy na polygonech pìknì promíchaly. Více detailù o smooth shading si povíme v jiných tutoriálech.</p>

<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povolí jemné stínování</span></p>

<p>Nastavíme barvu pozadí prázdné obrazovky. Rozsah barev se urèuje ve stupnici od 0.0f do 1.0f. 0.0f je nejtmav¹í a 1.0f je nejsvìtlej¹í. První parametr ve funkci glClearColor() je intenzita èervené barvy, druhý zelené a tøetí modré. Èím bli¾¹í je hodnota barvy 1.0f, tím svìtlej¹í slo¾ka barvy bude. Poslední parametr je hodnota alpha (prùhlednost). Kdy¾ budeme èistit obrazovku, tak se o prùhlednost starat nemusíme. Nyní ji necháme na 0.0f. Mù¾ete vytváøet rùzné barvy kombinováním svìtlosti tøí základních barev (èervené, zelené, modré). Pokud budete mít glClearColor(0.0f,0.0f,1.0f,0.0f), bude obrazovka modrá. Kdy¾ budete mít glClearColor(0.5f,0.0f,0.0f,0.0f), bude obrazovka støednì tmavì èervená. Abyste udìlali bílé pozadí nastavte v¹echny hodnoty na nejvy¹¹í hodnotu (1.0f), pro èerné pozadí zadejte pro v¹echny slo¾ky 0.0f.</p>

<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>

<p>Následující tøi øádky ovlivòují depth buffer. Depth buffer si mù¾ete pøedstavit jako vrstvy/hladiny obrazovky. Obsahuje informace, o tom jak hluboko jsou zobrazované objekty. Tento program sice nebude deep buffer pou¾ívat (nic nevykreslujeme). Objekty se seøadí tak, aby bli¾¹í pøekrývaly vzdálenìj¹í.</p>

<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí hloubkové testování</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>

<p>Dále oznámíme, ¾e chceme pou¾ít nejlep¹í korekce perspektivy. Jen nepatrnì se sní¾í výkon, ale zlep¹í se vzhled celé scény</p>

<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>

<p>Nakonec vrátíme true. Kdy¾ budeme chtít zjistit zda inicializace probìhla bez problémù, mù¾eme zkontrolovat, zda funkce vrátila hodnotu true nebo false. Mù¾ete pøidat vlastní kód, který vrátí false, kdy¾ se inicializace nezdaøí - napø. loading textur. Nyní se tím nebudeme dále zabývat.</p>

<p class="src1">return TRUE;<span class="kom">// Inicializace probìhla v poøádku</span></p>
<p class="src0">}</p>

<p>Do této funkci umístíme v¹echno vykreslování. Následující tutoriály budou pøepisovat pøedev¹ím tento a inicializaèní kód této lekce. ( Pokud ji¾ nyní rozumíte základùm OpenGL, mù¾ete si zde pøipsat kreslení základních tvarù (mezi glLoadIdentity() a return). Pokud jste nováèek, tak poèkejte do dal¹ího tutoriálu. Jediné co nyní udìláme, je vymazání obrazovky na barvu, pro kterou jste se rozhodli, vyma¾eme obsah hloubkového bufferu a resetujeme scénu. Zatím nebudeme nic kreslit. Pøíkaz return true nám øíká, ¾e pøi kreslení nenastaly ¾ádné problémy. Pokud z nìjakého dùvodu chcete pøeru¹it bìh programu, staèí pøidat return false pøed return true - to øíká na¹emu programu, ¾e kreslení scény selhalo a program se ukonèí.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Sem mù¾ete kreslit</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Vykreslení probìhlo v poøádku</span></p>
<p class="src0">}</p>

<p>Následující èást kódu je volána tìsnì pøed koncem programu. Úkolem funkce KillGLWindow() je uvolnìní renderovacího kontextu, kontextu zaøízení a handle okna. Pøidal jsem zde nezbytné kontrolování chyb. Kdy¾ není program schopen nìco uvolnit, oznámí chybu, která øíká co selhalo. Usnadní slo¾ité hledání problémù.</p>

<p class="src0">GLvoid KillGLWindow(GLvoid)<span class="kom">// Zavírání okna</span></p>
<p class="src0">{</p>

<p>Zjistíme zda je program ve fullscreenu. Pokud ano, tak ho pøepneme zpìt do systému. Mohli bychom vypnout okno pøed opu¹tìním fullscreenu, ale na nìkterých grafických kartách tím zpùsobíme problémy a systém by se mohl zhroutit.</p>

<p class="src1">if (fullscreen)<span class="kom">// Jsme ve fullscreenu?</span></p>
<p class="src1">{</p>

<p>K návratu do pùvodního nastavení systému pou¾íváme funkci ChangeDisplaySettings(NULL,0). Jako první parametr zadáme NULL a jako druhý 0 - pou¾ijeme hodnoty ulo¾ené v registrech Windows (pùvodní rozli¹ení, barevnou hloubku, obnovovací frekvenci, atd.). Po pøepnutí zviditelníme kurzor.</p>

<p class="src2">ChangeDisplaySettings(NULL,0);<span class="kom">// Pøepnutí do systému</span></p>
<p class="src2">ShowCursor(TRUE);<span class="kom">// Zobrazí kurzor my¹i</span></p>
<p class="src1">}</p>

<p>Zkontrolujeme zda máme renderovací kontext (hRC). Kdy¾ ne, program pøeskoèí èást kódu pod ním, který kontroluje, zda máme kontext zaøízení.</p>

<p class="src1">if (hRC)<span class="kom">// Máme rendering kontext?</span></p>
<p class="src1">{</p>

<p>Zjistíme, zda mù¾eme odpojit hRC od hDC. V¹imnìte si, jak kontroluji chyby. Nejdøíve programu øeknu, a» odpojí Rendering Context (s pou¾itím wglMakeCurrent(NULL,NULL)), pak zkontroluji zda akce byla úspì¹ná. Takto dám více øádku do jednoho.</p>

<p class="src2">if (!wglMakeCurrent(NULL,NULL))<span class="kom">// Jsme schopni oddìlit kontexty?</span></p>
<p class="src2">{</p>

<p>Pokud nejsme schopni uvolnit DC a RC, pou¾ijeme zobrazíme zprávu, ¾e DC a RC nelze uvolnit. NULL v parametru znamená, ¾e informaèní okno nemá ¾ádného rodièe. Text ihned za NULL je text, který se vypí¹e do zprávy. Dal¹í parametr definuje text li¹ty. Parametr MB_OK znamená, ¾e chceme mít na chybové zprávì jen jedno tlaèítko s nápisem OK. MB_ICONINFORMATION zobrazí ikonu.</p>

<p class="src3">MessageBox(NULL,&quot;Release Of DC And RC Failed.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">}</p>

<p>Zkusíme vymazat Rendering Context. Pokud se pokus nezdaøí, opìt se zobrazí chybová zpráva. Nakonec nastavíme hRC a NULL.</p>

<p class="src2">if (!wglDeleteContext(hRC))<span class="kom">// Jsme schopni smazat RC?</span></p>
<p class="src2">{</p>
<p class="src3">MessageBox(NULL,&quot;Release Rendering Context Failed.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">}</p>
<p class="src2">hRC=NULL;<span class="kom">// Nastaví hRC na NULL</span></p>
<p class="src1">}</p>

<p>Zjistíme zda má program kontext zaøízení. Kdy¾ ano odpojíme ho. Pokud se odpojení nezdaøí, zobrazí se chybová zpráva a hDC bude nastaven na NULL.</p>

<p class="src1">if (hDC &amp;&amp; !ReleaseDC(hWnd,hDC))<span class="kom">// Jsme schopni uvolnit DC</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Release Device Context Failed.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">hDC=NULL;<span class="kom">// Nastaví hDC na NULL</span></p>
<p class="src1">}</p>

<p>Nyní zjistíme zda máme handle okna a pokud ano pokusíme se odstranit okno pou¾itím funkce DestroyWindow(hWnd). Pokud se pokus nezdaøí, zobrazí se chybová zpráva a hWnd bude nastaveno na NULL.</p>

<p class="src1">if (hWnd &amp;&amp; !DestroyWindow(hWnd))<span class="kom">// Jsme schopni odstranit okno?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Could Not Release hWnd.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">hWnd=NULL;<span class="kom">// Nastaví hWnd na NULL</span></p>
<p class="src1">}</p>

<p>Odregistrováním  tøídy okna oficiálnì uzavøeme okno a pøedejdeme zobrazení chybové zprávy &quot;Windows Class already registered&quot; pøi opìtovném spu¹tìní programu.</p>

<p class="src1">if (!UnregisterClass(&quot;OpenGL&quot;,hInstance))<span class="kom">// Jsme schopni odregistrovat tøídu okna?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Could Not Unregister Class.&quot;,&quot;SHUTDOWN ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">hInstance=NULL;<span class="kom">// Nastaví hInstance na NULL</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Dal¹í èást kódu má na starosti vytvoøení OpenGL okna. Strávil jsem mnoho èasu pøemý¹lením zda mám udìlat pouze fullscreen mód, který by vy¾adoval ménì kódu, nebo jednodu¹e upravitelnou, u¾ivatelsky pøíjemnou verzi s variantou jak pro okno tak pro fullscreen, která v¹ak vy¾aduje mnohem více kódu. Rozhodl jsem se pro druhou variantu, proto¾e jsem dostával mnoho dotazù jako napøíklad: Jak mohu vytvoøit okno místo fullscreenu? Jak zmìním popisek okna? Jak zmìním rozli¹ení ne formát pixelù? Následující kód dovede v¹echno. Jak si mù¾ete v¹imnout funkce vrací bool a pøijímá 5 parametrù v poøadí: název okna, ¹íøku okna, vý¹ku okna, barevnou hloubku, fullscreen (pokud je parametr true program pobì¾í ve fullscreenu, pokud bude false program pobì¾í v oknì). Vracíme bool, abychom vìdìli zda bylo okno úspì¹nì vytvoøeno.</p>

<p class="src0">BOOL CreateGLWindow(char* title, int width, int height, int bits, bool fullscreenflag)</p>
<p class="src0">{</p>

<p>Za chvíli po¾ádáme Windows, aby pro nás na¹el pixel format, který odpovídá tomu, který chceme. Toto èíslo ulo¾íme do promìnné PixelFormat.</p>

<p class="src1">GLuint PixelFormat;<span class="kom">// Ukládá formát pixelù</span></p>

<p>Wc bude pou¾ijeme k uchování informací o struktuøe Windows Class. Zmìnou hodnot jednotlivých polo¾ek, lze ovlivnit vzhled a chování okna. Pøed vytvoøením samotného okna se musí zaregistrovat nìjaká struktura pro okno.</p>

<p class="src1">WNDCLASS wc;<span class="kom">// Struktura Windows Class</span></p>

<p>DwExStyle a dwStyle ponesou informaci o normálních a roz¹íøených informacích o oknu. Pou¾iji promìnné k uchování stylù, tak¾e mohu mìnit vzhled okna, který potøebuji vytvoøit (pro fullscreen bez okraje a pro okno okraj).</p>

<p class="src1">DWORD dwExStyle;<span class="kom">// Roz¹íøený styl okna</span></p>
<p class="src1">DWORD dwStyle;<span class="kom">// Styl okna</span></p>

<p>Zjistíme polohu levého horního a pravého dolního rohu okna. Tyto promìnné vyu¾ijeme k tomu, abychom nakreslili okno v takovém rozli¹ení, v jakém si ho pøejeme mít. Pokud vytvoøíme okno s rozli¹ením 640x480, okraje budou zabírat èást na¹eho rozli¹ení.</p>

<p class="src1">RECT WindowRect;<span class="kom">// Obdélník okna</span></p>
<p class="src"></p>
<p class="src1">WindowRect.left = (long)0;<span class="kom">// Nastaví levý okraj na nulu</span></p>
<p class="src1">WindowRect.right = (long)width;<span class="kom">// Nastaví pravý okraj na zadanou hodnotu</span></p>
<p class="src1">WindowRect.top = (long)0;<span class="kom">// Nastaví horní okraj na nulu</span></p>
<p class="src1">WindowRect.bottom = (long)height;<span class="kom">// Nastaví spodní okraj na zadanou hodnotu</span></p>

<p>Pøiøadíme globální promìnné fullscreen, hodnotu fullscreenflag. Tak¾e pokud na¹e okno pobì¾í ve fullscreenu, promìnná fullscreen se bude rovnat true. Kdybychom zavírali okno ve fullscreenu, ale hodnota promìnné fullscreen by byla false místo true, jak by mìla být, poèítaè by se nepøepl zpìt do systému, proto¾e by si myslel, ¾e v nìm ji¾ je. Jednodu¹e shrnuto, fullscreen v¾dy musí obsahovat správnou hodnotu.</p>

<p class="src1">fullscreen = fullscreenflag;<span class="kom">// Nastaví promìnnou fullscreen na správnou hodnotu</span></p>

<p>Získáme instanci pro okno a poté definujeme Window Class. CS_HREDRAW a CS_VREDRAW donutí na¹e okno, aby se pøekreslilo, kdykoliv se zmìní jeho velikost. CS_OWNDC vytvoøí privátní kontext zaøízení. To znamená, ¾e není sdílen s ostatními aplikacemi. WndProc je procedura okna, která sleduje pøíchozí zprávy pro program. ®ádná extra data pro okno nepou¾íváme, tak¾e do dal¹ích dvou polo¾ek pøiøadíme nulu. Nastavíme instanci a hIcon na NULL, co¾ znamená, ¾e nebudeme pro ná¹ program pou¾ívat ¾ádnou speciální ikonu a pro kurzor my¹i pou¾íváme standardní ¹ipku. Barva pozadí nás nemusí zajímat (to zaøídíme v OpenGL). Nechceme, aby okno mìlo menu, tak¾e i tuto hodnotu nastavíme na NULL. Jméno tøídy mù¾e být libovolné. Já pou¾iji pro jednoduchost &quot;OpenGL&quot;.</p>

<p class="src1">hInstance = GetModuleHandle(NULL);<span class="kom">// Získá instanci okna</span></p>
<p class="src"></p>
<p class="src1">wc.style = CS_HREDRAW | CS_VREDRAW | CS_OWNDC;<span class="kom">// Pøekreslení pøi zmìnì velikosti a vlastní DC</span></p>
<p class="src1">wc.lpfnWndProc = (WNDPROC) WndProc;<span class="kom">// Definuje proceduru okna</span></p>
<p class="src1">wc.cbClsExtra = 0;<span class="kom">// ®ádná extra data</span></p>
<p class="src1">wc.cbWndExtra = 0;<span class="kom">// ®ádná extra data</span></p>
<p class="src1">wc.hInstance = hInstance;<span class="kom">// Instance</span></p>
<p class="src1">wc.hIcon = LoadIcon(NULL, IDI_WINLOGO);<span class="kom">// Standardní ikona</span></p>
<p class="src1">wc.hCursor = LoadCursor(NULL, IDC_ARROW);<span class="kom">// Standardní kurzor my¹i</span></p>
<p class="src1">wc.hbrBackground = NULL;<span class="kom">// Pozadí není nutné</span></p>
<p class="src1">wc.lpszMenuName = NULL;<span class="kom">// Nechceme menu</span></p>
<p class="src1">wc.lpszClassName = &quot;OpenGL&quot;;<span class="kom">// Jméno tøídy okna</span></p>

<p>Zaregistrujeme právì definovanou tøídu okna. Kdy¾ nastane chyba a zobrazí se chybové hlá¹ení. Zmáèknutím tlaèítka OK se program ukonèí.</p>

<p class="src1">if (!RegisterClass(&amp;wc))<span class="kom">// Registruje tøídu okna</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Failed To Register The Window Class.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Pøi chybì vrátí false</span></p>
<p class="src1">}</p>

<p>Nyní si zjistíme zda má program bì¾et ve fullscreenu, nebo v oknì.</p>

<p class="src1">if (fullscreen)<span class="kom">// Budeme ve fullscreenu?</span></p>
<p class="src1">{</p>

<p>S pøepínáním do fullscreenu, mívají lidé mnoho problémù. Je zde pár dùle¾itých vìcí, na které si musíte dávat pozor. Ujistìte se, ¾e ¹íøka a vý¹ka, kterou pou¾íváte ve fullscreenu je toto¾ná s tou, kterou chcete pou¾ít v oknì. Dal¹í vìc je hodnì dùle¾itá. Musíte pøepnout do fullscreenu pøedtím ne¾ vytvoøíte okno. V tomto kódu se o rovnost vý¹ky a ¹íøky nemusíte starat, proto¾e velikost ve fullscreenu i v oknì budou stejné.</p>

<p class="src2">DEVMODE dmScreenSettings;<span class="kom">// Mód zaøízení</span></p>
<p class="src"></p>
<p class="src2">memset(&amp;dmScreenSettings,0,sizeof(dmScreenSettings));<span class="kom">// Vynulování pamìti</span></p>
<p class="src"></p>
<p class="src2">dmScreenSettings.dmSize=sizeof(dmScreenSettings);<span class="kom">// Velikost struktury Devmode</span></p>
<p class="src2">dmScreenSettings.dmPelsWidth= width;<span class="kom">// ©íøka okna</span></p>
<p class="src2">dmScreenSettings.dmPelsHeight= height;<span class="kom">// Vý¹ka okna</span></p>
<p class="src2">dmScreenSettings.dmBitsPerPel= bits;<span class="kom">// Barevná hloubka</span></p>
<p class="src2">dmScreenSettings.dmFields=DM_BITSPERPEL|DM_PELSWIDTH|DM_PELSHEIGHT;</p>

<p>Funkce ChangeDisplaySettings() se pokusí pøepnout do módu, který je ulo¾en v dmScreenSettings. Pou¾iji parametr CDS_FULLSCREEN, proto¾e odstraní pracovní li¹tu ve spodní èásti obrazovky a nepøesune nebo nezmìní velikost okna pøi pøepínání z fullscreenu do systému nebo naopak.</p>

<p class="src2"><span class="kom">// Pokusí se pou¾ít právì definované nastavení</span></p>
<p class="src2">if (ChangeDisplaySettings(&amp;dmScreenSettings,CDS_FULLSCREEN)!=DISP_CHANGE_SUCCESSFUL)</p>
<p class="src2">{</p>

<p>Pokud právì vytvoøený fullscreen mód neexistuje, zobrazí se chybová zpráva s nabídkou spu¹tìní v oknì nebo opu¹tìní programu.</p>

<p class="src3"><span class="kom">// Nejde-li fullscreen, mù¾e u¾ivatel spustit program v oknì nebo ho opustit</span></p>
<p class="src3">if (MessageBox(NULL,&quot;The Requested Fullscreen Mode Is Not Supported By\nYour Video Card. Use Windowed Mode Instead?&quot;,&quot;NeHe GL&quot;,MB_YESNO|MB_ICONEXCLAMATION)==IDYES)</p>
<p class="src3">{</p>

<p>Kdy¾ se u¾ivatel rozhodne pro bìh v oknì, do promìnné fullscreen se pøiøadí false a program pokraèuje dále.</p>

<p class="src4">fullscreen=FALSE;<span class="kom">// Bìh v oknì</span></p>
<p class="src3">}</p>
<p class="src3">else</p>
<p class="src3">{</p>

<p>Pokud se u¾ivatel rozhodl pro ukonèení programu, zobrazí se u¾ivateli zpráva, ¾e program bude ukonèen. Bude vrácena hodnota false, která na¹emu programu øíká, ¾e pokus o vytvoøení okna nebyl úspì¹ný a potom se program ukonèí.</p>

<p class="src4"><span class="kom">// Zobrazí u¾ivateli zprávu, ¾e program bude ukonèen</span></p>
<p class="src4">MessageBox(NULL,&quot;Program Will Now Close.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONSTOP);</p>
<p class="src"></p>
<p class="src4">return FALSE;<span class="kom">// Vrátí FALSE</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Proto¾e pokus o pøepnutí do fullscreenu mù¾e selhat, nebo se u¾ivatel mù¾e rozhodnout pro bìh programu v oknì, zkontrolujeme je¹tì jednou zda je promìnná fullscreen true nebo false. A¾ poté nastavíme typ obrazu.</p>

<p class="src1">if (fullscreen)<span class="kom">// Jsme stále ve fullscreenu?</span></p>
<p class="src1">{</p>

<p>Pokud jsme stále ve fullscreenu nastavíme roz¹íøený styl na WS_EX_APPWINDOW, co¾ donutí okno, aby pøekrylo pracovní li¹tu. Styl okna urèíme na WS_POPUP. Tento typ okna nemá ¾ádné okraje, co¾ je pro fullscreen výhodné. Nakonec vypneme kurzor my¹i. Pokud vá¹ program není interaktivní, je vìt¹inou vhodnìj¹í ve fullscreenu kurzor vypnout. Pro co rozhodnete je na vás.</p>

<p class="src2">dwExStyle=WS_EX_APPWINDOW;<span class="kom">// Roz¹íøený styl okna</span></p>
<p class="src2">dwStyle=WS_POPUP;<span class="kom">// Styl okna</span></p>
<p class="src2">ShowCursor(FALSE);<span class="kom">// Skryje kurzor</span></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>

<p>Pokud místo fullscreenu pou¾íváme bìh v oknì, nastavíme roz¹íøený styl na WS_EX_WINDOWEDGE. To dodá oknu trochu 3D vzhledu. Styl nastavíme na WS_OVERLAPPEDWINDOW místo na WS_POPUP. WS_OVERLAPPEDWINDOW vytvoøí okno s li¹tou, okraji, tlaèítky pro minimalizaci a maximalizaci. Budeme moci mìnit velikost.</p>

<p class="src2">dwExStyle=WS_EX_APPWINDOW | WS_EX_WINDOWEDGE;<span class="kom">// Roz¹íøený styl okna</span></p>
<p class="src2">dwStyle=WS_OVERLAPPEDWINDOW;<span class="kom">// Styl okna</span></p>
<p class="src1">}</p>

<p>Pøizpùsobíme okno podle stylu, který jsme vytvoøili. Pøizpùsobení udìlá okno v takovém rozli¹ení, jaké po¾adujeme. Normálnì by okraje pøekrývaly èást okna. S pou¾itím pøíkazu AdjustWindowRectEx ¾ádná èást OpenGL scény nebude pøekryta okraji, místo toho bude okno udìláno o málo vìt¹í, aby se do nìj ve¹ly v¹echny pixely tvoøící okraj okna. Ve fullscreenu tato funkce nemá ¾ádný efekt.</p>

<p class="src1">AdjustWindowRectEx(&amp;WindowRect, dwStyle, FALSE, dwExStyle);<span class="kom">// Pøizpùsobení velikosti okna</span></p>

<p>Vytvoøíme okno a zkontrolujeme zda bylo vytvoøeno správnì. Pou¾ijeme funkci CreateWindowEx() se v¹emi parametry, které vy¾aduje. Roz¹íøený styl, který jsme se rozhodli pou¾ít. Jméno tøídy (musí být stejné jako to, které jste pou¾ili, kdy¾ jste registrovali Window Class).Titulek okna. Styl okna. Horní levá pozice okna (0,0 je nejjistìj¹í). ©íøka a vý¹ka okna. Nechceme mít rodièovské okno ani menu, tak¾e nastavíme tyto parametry na NULL. Zadáme instanci okna a koneènì pøiøadíme NULL na místo posledního parametru. V¹imnìte si, ¾e zahrnujeme styly WS_CLIPSIBLINGS a WS_CLIPCHILDREN do stylu, který jsme se rozhodli pou¾ít. WS_CLIPSIBLINGS a WS_CLIPCHILDREN jsou potøebné pro OpenGL, aby pracovalo správnì. Tyto styly zakazují ostatním oknùm, aby kreslily do na¹eho okna.</p>

<p class="src1"><span class="kom">// Vytvoøení okna</span></p>
<p class="src1">if (!(hWnd=CreateWindowEx(dwExStyle,<span class="kom">// Roz¹íøený styl</span></p>
<p class="src2">&quot;OpenGL&quot;,<span class="kom">// Jméno tøídy</span></p>
<p class="src2">title,<span class="kom">// Titulek</span></p>
<p class="src2">dwStyle |<span class="kom">// Definovaný styl</span></p>
<p class="src2">WS_CLIPSIBLINGS |<span class="kom">// Po¾adovaný styl</span></p>
<p class="src2">WS_CLIPCHILDREN,<span class="kom">// Po¾adovaný styl</span></p>
<p class="src2">0, 0,<span class="kom">// Pozice</span></p>
<p class="src2">WindowRect.right-WindowRect.left,<span class="kom">// Výpoèet ¹íøky</span></p>
<p class="src2">WindowRect.bottom-WindowRect.top,<span class="kom">// Výpoèet vý¹ky</span></p>
<p class="src2">NULL,<span class="kom">// ®ádné rodièovské okno</span></p>
<p class="src2">NULL,<span class="kom">// Bez menu</span></p>
<p class="src2">hInstance,<span class="kom">// Instance</span></p>
<p class="src2">NULL)))<span class="kom">// Nepøedat nic do WM_CREATE</span></p>

<p>Dále zkontrolujeme zda bylo vytvoøeno. Pokud bylo, hWnd obsahuje handle tohoto okna. Kdy¾ se vytvoøení okna nepovede, kód zobrazí chybovou zprávu a program se ukonèí.</p>

<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zru¹í okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Window Creation Error.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Vrátí chybu</span></p>
<p class="src1">}</p>

<p>Vybereme Pixel Format, který podporuje OpenGL, dále zvolíme double buffering a RGBA (èervená, zelená, modrá, prùhlednost). Pokusíme se najít formát, který odpovídá tomu, pro který jsme se rozhodli (16 bitù, 24 bitù, 32 bitù). Nakonec nastavíme Z-Buffer. Ostatní parametry se nepou¾ívají nebo pro nás nejsou dùle¾ité.</p>

<p class="src1">static PIXELFORMATDESCRIPTOR pfd=<span class="kom">// Oznámíme Windows jak chceme v¹e nastavit</span></p>
<p class="src1">{</p>
<p class="src2">sizeof(PIXELFORMATDESCRIPTOR),<span class="kom">// Velikost struktury</span></p>
<p class="src2">1,<span class="kom">// Èíslo verze</span></p>
<p class="src2">PFD_DRAW_TO_WINDOW |<span class="kom">// Podpora okna</span></p>
<p class="src2">PFD_SUPPORT_OPENGL |<span class="kom">// Podpora OpenGL</span></p>
<p class="src2">PFD_DOUBLEBUFFER,<span class="kom">// Podpora Double Bufferingu</span></p>
<p class="src2">PFD_TYPE_RGBA,<span class="kom">// RGBA Format</span></p>
<p class="src2">bits,<span class="kom">// Zvolí barevnou hloubku</span></p>
<p class="src2">0, 0, 0, 0, 0, 0,<span class="kom">// Bity barev ignorovány</span></p>
<p class="src2">0,<span class="kom">// ®ádný alpha buffer</span></p>
<p class="src2">0,<span class="kom">// Ignorován Shift bit</span></p>
<p class="src2">0,<span class="kom">// ®ádný akumulaèní buffer</span></p>
<p class="src2">0, 0, 0, 0,<span class="kom">// Akumulaèní bity ignorovány</span></p>
<p class="src2">16,<span class="kom">// 16-bitový hloubkový buffer (Z-Buffer)</span></p>
<p class="src2">0,<span class="kom">// ®ádný Stencil Buffer</span></p>
<p class="src2">0,<span class="kom">// ®ádný Auxiliary Buffer</span></p>
<p class="src2">PFD_MAIN_PLANE,<span class="kom">// Hlavní vykreslovací vrstva</span></p>
<p class="src2">0,<span class="kom">// Rezervováno</span></p>
<p class="src2">0, 0, 0<span class="kom">// Maska vrstvy ignorována</span></p>
<p class="src1">};</p>

<p>Pokud nenastaly problémy bìhem vytváøení okna, pokusíme se pøipojit kontext zaøízení. Pokud ho se nepøipojí, zobrazí se chybové hlá¹ení a program se ukonèí.</p>

<p class="src1">if (!(hDC=GetDC(hWnd)))<span class="kom">// Podaøilo se pøipojit kontext zaøízení?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Create A GL Device Context.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukonèí program</span></p>
<p class="src1">}</p>

<p>Kdy¾ získáme kontext zaøízení, pokusíme se najít odpovídající Pixel Format. Kdy¾ ho Windows nenajde formát, zobrazí se chybová zpráva a program se ukonèí.</p>

<p class="src1">if (!(PixelFormat=ChoosePixelFormat(hDC,&amp;pfd)))<span class="kom">// Podaøilo se najít Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Find A Suitable PixelFormat.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukonèí program</span></p>
<p class="src1">}</p>

<p>Kdy¾ Windows najde odpovídající formát, tak se ho pokusíme nastavit. Pokud pøi pokusu o nastavení nastane chyba, opìt se zobrazí chybové hlá¹ení a program se ukonèí.</p>

<p class="src1">if(!SetPixelFormat(hDC,PixelFormat,&amp;pfd))<span class="kom">// Podaøilo se nastavit Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Set The PixelFormat.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukonèí program</span></p>
<p class="src1">}</p>

<p>Pokud byl nastaven Pixel Format správnì, pokusíme se získat Rendering Context. Pokud ho nezískáme, program zobrazí chybovou zprávu a ukonèí se.</p>

<p class="src1">if (!(hRC=wglCreateContext(hDC)))<span class="kom">// Podaøilo se vytvoøit Rendering Context?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Create A GL Rendering Context.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukonèí program</span></p>
<p class="src1">}</p>

<p>Pokud nenastaly ¾ádné chyby pøi vytváøení jak Device Context, tak Rendering Context, v¹e co musíme nyní udìlat je aktivovat Rendering Context. Pokud ho nebudeme moci aktivovat, zobrazí se chybová zpráva a program se ukonèí.</p>

<p class="src1">if(!wglMakeCurrent(hDC,hRC))<span class="kom">// Podaøilo se aktivovat Rendering Context?</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Can't Activate The GL Rendering Context.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukonèí program</span></p>
<p class="src1">}</p>

<p>Pokud bylo okno vytvoøeno, zobrazíme ho na obrazovce, nastavíme ho, aby bylo v popøedí (vy¹¹í priorita) a pak nastavíme zamìøení na toto okno. Zavoláme funkci ResizeGLScene() s parametry odpovídajícími vý¹ce a ¹íøce okna, abychom správnì nastavili perspektivu OpenGL.</p>

<p class="src1">ShowWindow(hWnd,SW_SHOW);<span class="kom">// Zobrazení okna</span></p>
<p class="src1">SetForegroundWindow(hWnd);<span class="kom">// Do popøedí</span></p>
<p class="src1">SetFocus(hWnd);<span class="kom">// Zamìøí fokus</span></p>
<p class="src1">ReSizeGLScene(width, height);<span class="kom">// Nastavení perspektivy OpenGL scény</span></p>

<p>Koneènì se dostáváme k volání vý¹e definované funkce InitGL(), ve které nastavujeme osvìtlení, loading textur a cokoliv jiného, co je potøeba. Mù¾ete vytvoøit svou vlastní kontrolu chyb ve funkci InitGL() a vracet true, kdy¾ v¹e probìhne bez problémù, nebo false, pokud nastanou nìjaké problémy. Napøíklad, nastane-li chyba pøi nahrávání textur, vrátíte false, jako znamení, ¾e nìco selhalo a program se ukonèí.</p>

<p class="src1">if (!InitGL())<span class="kom">// Inicializace okna</span></p>
<p class="src1">{</p>
<p class="src2">KillGLWindow();<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(NULL,&quot;Initialization Failed.&quot;,&quot;ERROR&quot;,MB_OK|MB_ICONEXCLAMATION);</p>
<p class="src2">return FALSE;<span class="kom">// Ukonèí program</span></p>
<p class="src1">}</p>

<p>Pokud jsme se dostali a¾ takhle daleko, mù¾eme konstatovat, ¾e vytvoøení okna probìhlo bez problémù. Vrátíme true do WinMain(), co¾ øíká, ¾e nenastaly ¾ádné chyby. To zabrání programu, aby se sám ukonèil.</p>

<p class="src1">return TRUE;<span class="kom">// V¹e probìhlo v poøádku</span></p>
<p class="src0">}</p>

<p>Nyní se vypoøádáme se systémovými zprávami pro okno. Kdy¾ máme zaregistrovanou na¹i Window Class, mù¾eme podstoupit k èásti kódu, která má na starosti zpracování zpráv.</p>

<p class="src0">LRESULT CALLBACK WndProc(HWND hWnd,<span class="kom">// Handle okna</span></p>
<p class="src1">UINT uMsg,<span class="kom">// Zpráva pro okno</span></p>
<p class="src1">WPARAM wParam,<span class="kom">// Doplòkové informace</span></p>
<p class="src1">LPARAM lParam)<span class="kom">// Doplòkové informace</span></p>
<p class="src0">{</p>

<p>Napí¹eme mapu zpráv. Program se bude vìtvit podle promìnné uMsg, která obsahuje jméno zprávy.</p>

<p class="src1">switch (uMsg)<span class="kom">// Vìtvení podle pøíchozí zprávy</span></p>
<p class="src1">{</p>

<p>Po pøíchodu WM_ACTIVE, zkontrolujeme, zda je okno stále aktivní. Pokud bylo minimalizováno, nastavíme hodnotu active na false. Pokud je na¹e okno aktivní, promìnná active bude mít hodnotu true.</p>

<p class="src2">case WM_ACTIVATE:<span class="kom">// Zmìna aktivity okna</span></p>
<p class="src2">{</p>
<p class="src3">if (!HIWORD(wParam))<span class="kom">// Zkontroluje zda není minimalizované</span></p>
<p class="src3">{</p>
<p class="src4">active=TRUE;<span class="kom">// Program je aktivní</span></p>
<p class="src3">}</p>
<p class="src3">else</p>
<p class="src3">{</p>
<p class="src4">active=FALSE;<span class="kom">// Program není aktivní</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">return 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">}</p>

<p>Po pøíchodu WM_SYSCOMMAND (systémový pøíkaz) porovnáme wParam s mo¾nými stavy, které mohly nastat. Kdy¾ je wParam WM_SCREENSAVE nebo SC_MONITORPOWER sna¾í se systém zapnout spoøiè obrazovky, nebo pøejít do úsporného re¾imu. Jestli¾e vrátíme 0 zabráníme systému, aby tyto akce provedl.</p>

<p class="src2">case WM_SYSCOMMAND:<span class="kom">// Systémový pøíkaz</span></p>
<p class="src2">{</p>
<p class="src3">switch (wParam)<span class="kom">// Typ systémového pøíkazu</span></p>
<p class="src3">{</p>
<p class="src4">case SC_SCREENSAVE:<span class="kom">// Pokus o zapnutí ¹etøièe obrazovky</span></p>
<p class="src4">case SC_MONITORPOWER:<span class="kom">// Pokus o pøechod do úsporného re¾imu?</span></p>
<p class="src5">return 0;<span class="kom">// Zabrání obojímu</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">break;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">}</p>

<p>Pøi¹lo-li WM_CLOSE bylo okno zavøeno. Po¹leme tedy zprávu pro opu¹tìní programu, která pøeru¹í vykonávání hlavního cyklu. Promìnnou done (ve WinMain()) nastavíme na true, hlavní smyèka se pøeru¹í a program se ukonèí.</p>

<p class="src2">case WM_CLOSE:<span class="kom">// Povel k ukonèení programu</span></p>
<p class="src2">{</p>
<p class="src3">PostQuitMessage(0);<span class="kom">// Po¹le zprávu o ukonèení</span></p>
<p class="src3">return 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">}</p>

<p>Pokud byla stisknuta klávesa, mù¾eme zjistit, která z nich to byla, kdy¾ zjistíme hodnotu wParam. Potom zadáme do buòky, specifikované wParam, v poli keys[] true. Díky tomu potom mù¾eme zjistit, která klávesa je právì stisknutá. Tímto zpùsobem lze zkontrolovat stisk více kláves najednou.</p>

<p class="src2">case WM_KEYDOWN:<span class="kom">// Stisk klávesy</span></p>
<p class="src2">{</p>
<p class="src3">keys[wParam] = TRUE;<span class="kom">// Oznámí to programu</span></p>
<p class="src3">return 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">}</p>

<p>Pokud byla naopak klávesa uvolnìna ulo¾íme do buòky s indexem wParam v poli keys[] hodnotu false. Tímto zpùsobem mù¾eme zjistit zda je klávesa je¹tì stále stisknuta nebo ji¾ byla uvolnìna. Ka¾dá klávesa je reprezentována jedním èíslem od 0 do 255. Kdy¾ napøíklad stisknu klávesu èíslo 40, hodnota key[40] bude true, jakmile ji pustím její hodnota se vrátí opìt na false.</p>

<p class="src2">case WM_KEYUP:<span class="kom">// Uvolnìní klávesy</span></p>
<p class="src2">{</p>
<p class="src3">keys[wParam] = FALSE;<span class="kom">// Oznámí to programu</span></p>
<p class="src3">return 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">}</p>

<p>Kdykoliv u¾ivatel zmìní velikost okna, po¹le se WM_SIZE. Pøeèteme LOWORD a HIWORD hodnoty lParam, abychom zjistili jaká je nová ¹íøka a vý¹ka okna. Pøedáme tyto hodnoty do funkce ReSizeGLScene(). Perspektiva OpenGL scény se zmìní podle nových rozmìrù.</p>

<p class="src2">case WM_SIZE:<span class="kom">// Zmìna velikosti okna</span></p>
<p class="src2">{</p>
<p class="src3">ReSizeGLScene(LOWORD(lParam),HIWORD(lParam));  <span class="kom">// LoWord=©íøka, HiWord=Vý¹ka</span></p>
<p class="src3">return 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Zprávy, o které se nestaráme, budou pøedány funkci DefWindowProc(), tak¾e se s nimi vypoøádá systém.</p>

<p class="src1">return DefWindowProc(hWnd, uMsg, wParam, lParam);<span class="kom">// Pøedání ostatních zpráv systému</span></p>
<p class="src0">}</p>

<p>Funkce WinMain() je vstupní bod do aplikace, místo, odkud budeme volat funkce na otevøení okna, snímaní zpráv a interakci s u¾ivatelem.</p>

<p class="src0">int WINAPI WinMain(HINSTANCE hInstance,<span class="kom">// Instance</span></p>
<p class="src1">HINSTANCE hPrevInstance,<span class="kom">// Pøedchozí instance</span></p>
<p class="src1">LPSTR lpCmdLine,<span class="kom">// Parametry pøíkazové øádky</span></p>
<p class="src1">int nCmdShow)<span class="kom">// Stav zobrazení okna</span></p>
<p class="src0">{</p>

<p>Deklarujeme dvì lokální promìnné. Msg bude pou¾ita na zji¹»ování, zda se mají zpracovávat nìjaké zprávy. Promìnná done bude mít na poèátku hodnotu false. To znamená, ¾e ná¹ program je¹tì nemá být ukonèen. Dokud se done rovná false, program pobì¾í. Jakmile se zmìní z false na true, program se ukonèí.</p>

<p class="src1">MSG msg;<span class="kom">// Struktura zpráv systému</span></p>
<p class="src1">BOOL done=FALSE;<span class="kom">// Promìnná pro ukonèení programu</span></p>

<p>Dal¹í èást kódu je volitelná. Zobrazuje zprávu, která se zeptá u¾ivatele, zda chce spustit program ve fullscreenu. Pokud u¾ivatel vybere mo¾nost Ne, hodnota promìnné fullscreen se zmìní z výchozího true na false, a tím pádem se program spustí v oknì.</p>

<p class="src1"><span class="kom">// Dotaz na u¾ivatele pro fullscreen/okno</span></p>
<p class="src1">if (MessageBox(NULL,&quot;Would You Like To Run In Fullscreen Mode?&quot;, &quot;Start FullScreen?&quot;, MB_YESNO | MB_ICONQUESTION) == IDNO)</p>
<p class="src1">{</p>
<p class="src2">fullscreen=FALSE;<span class="kom">// Bìh v oknì</span></p>
<p class="src1">}</p>

<p>Vytvoøíme OpenGL okno. Zadáme text titulku, ¹íøku, vý¹ku, barevnou hloubku a true (fullscreen), nebo false (okno) jako parametry do funkce CreateGLWindow(). Tak a je to! Je to pìknì lehké, ¾e? Pokud se okno nepodaøí z nìjakého dùvodu vytvoøit, bude vráceno false a program se okam¾itì ukonèí.</p>

<p class="src1">if (!CreateGLWindow(&quot;NeHe's OpenGL Framework&quot;,640,480,16,fullscreen))<span class="kom">// Vytvoøení OpenGL okna</span></p>
<p class="src1">{</p>
<p class="src2">return 0;<span class="kom">// Konec programu pøi chybì</span></p>
<p class="src1">}</p>

<p>Smyèka se opakuje tak dlouho, dokud se done rovná false.</p>

<p class="src1">while(!done)<span class="kom">// Hlavní cyklus programu</span></p>
<p class="src1">{</p>

<p>První vìc, kterou udìláme, je zkontrolování zpráv pro okno. Pomocí funkce PeekMessage() mù¾eme zjistit zda nìjaké zprávy èekají na zpracování bez toho, aby byl program pozastaven. Mnoho programù pou¾ívá funkci GetMessage(). Pracuje to skvìle, ale program nic nedìlá, kdy¾ nedostává ¾ádné zprávy.</p>

<p class="src2">if (PeekMessage(&amp;msg,NULL,0,0,PM_REMOVE))<span class="kom">// Pøi¹la zpráva?</span></p>
<p class="src2">{</p>

<p>Zkontrolujeme, zda jsme neobdr¾eli zprávu pro ukonèení programu. Pokud je aktuální zpráva WM_QUIT, která je zpùsobena voláním funkce PostQuitMessage(0), nastavíme done na true, èím¾ pøeru¹íme hlavní cyklus a ukonèíme program.</p>

<p class="src3">if (msg.message==WM_QUIT)<span class="kom">// Obdr¾eli jsme zprávu pro ukonèení?</span></p>
<p class="src3">{</p>
<p class="src4">done=TRUE;<span class="kom">// Konec programu</span></p>
<p class="src3">}</p>
<p class="src3">else<span class="kom">// Pøedáme zprávu proceduøe okna</span></p>
<p class="src3">{</p>

<p>Kdy¾ zpráva nevyzývá k ukonèení programu, tak pøedáme funkcím TranslateMessage() a DispatchMessage() referenci na tuto zprávu, aby ji funkce WndProc() nebo Windows zpracovaly.</p>

<p class="src4">TranslateMessage(&amp;msg);<span class="kom">// Pøelo¾í zprávu</span></p>
<p class="src4">DispatchMessage(&amp;msg);<span class="kom">// Ode¹le zprávu</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Pokud nedo¹la ¾ádná zpráva</span></p>
<p class="src2">{</p>

<p>Pokud zde nebudou ji¾ ¾ádné zprávy, pøekreslíme OpenGL scénu. Následující øádek kontroluje, zda je okno aktivní. Na¹e scéna je vyrenderována a je zkontrolována vrácená hodnota. Kdy¾ funkce DrawGLScene() vrátí false nebo je stisknut ESC, hodnota promìnné done je nastavena na true, co¾ ukonèí bìh programu.</p>

<p class="src3">if (active)<span class="kom">// Je program aktivní?</span></p>
<p class="src3">{</p>
<p class="src4">if (keys[VK_ESCAPE])<span class="kom">// Byl stisknut ESC?</span></p>
<p class="src4">{</p>
<p class="src5">done=TRUE;<span class="kom">// Ukonèíme program</span></p>
<p class="src4">}</p>
<p class="src4">else<span class="kom">// Pøekreslení scény</span></p>
<p class="src4">{</p>

<p>Kdy¾ v¹echno probìhlo bez problémù, prohodíme obsah bufferù (s pou¾itím dvou bufferù pøedejdeme blikání obrazu pøi pøekreslování). Pou¾itím dvojtého bufferingu v¹echno vykreslujeme do obrazovky v pamìti, kterou nevidíme. Jakmile vymìníme obsah bufferù, to co je na obrazovce se pøesune do této skryté obrazovky a to, co je ve skryté obrazovce se pøenese na monitor. Díky tomu nevidíme probliknutí.</p>

<p class="src5">DrawGLScene();<span class="kom">// Vykreslení scény</span></p>
<p class="src5">SwapBuffers(hDC);<span class="kom">// Prohození bufferù (Double Buffering)</span></p>
<p class="src4">}</p>
<p class="src3">}</p>

<p>Pøi stisku klávesy F1 pøepneme z fullscreenu do okna a naopak.</p>

<p class="src3">if (keys[VK_F1])<span class="kom">// Byla stisknuta klávesa F1?</span></p>
<p class="src3">{</p>
<p class="src4">keys[VK_F1]=FALSE;<span class="kom">// Oznaè ji jako nestisknutou</span></p>
<p class="src4">KillGLWindow();<span class="kom">// Zru¹í okno</span></p>
<p class="src4">fullscreen=!fullscreen;<span class="kom">// Negace fullscreen</span></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Znovuvytvoøení okna</span></p>
<p class="src4">if (!CreateGLWindow(&quot;NeHe's OpenGL Framework&quot;,640,480,16,fullscreen))</p>
<p class="src4">{</p>
<p class="src5">return 0;<span class="kom">// Konec programu pokud nebylo vytvoøeno</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Pokud se promìnná done rovná true, hlavní cyklus se pøeru¹í. Zavøeme okno a opustíme program.</p>

<p class="src1">KillGLWindow();<span class="kom">// Zavøe okno</span></p>
<p class="src1">return (msg.wParam);<span class="kom">// Ukonèení programu</span></p>
<p class="src0">}</p>

<p>V této lekci jsem se vám pokou¹el co nejpodrobnìji vysvìtlit ka¾dý krok pøi nastavování a vytváøení OpenGL programu. Program se ukonèí pøi stisku klávesy ESC a sleduje, zda je okno aktivní èi nikoliv.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Václav Slováèek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>
<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson01.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/asm/lesson01.zip">ASM</a> kód této lekce. ( <a href="mailto:foolman@bigfoot.com">Foolman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson01_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson01.zip">C#</a> kód této lekce. ( <a href="mailto:joachim_rohde@freenet.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson01.zip">VB.Net CsGL</a> kód této lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson01.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson01.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson01.zip">Delphi</a> kód této lekce. ( <a href="mailto:Perry.dj@glo.be">Peter De Jaegher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson01-2.zip">Delphi</a> kód této lekce. ( <a href="mailto:nelsonnelson@hotmail.com">Nelson Nelson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson01.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson01.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson01.zip">Irix</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson01.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java_swt/lesson01.zip">Java/SWT</a> kód této lekce. ( <a href="mailto:victor@parasoft.com">Victor Gonzalez</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson01.jar">JoGL</a> kód této lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson01.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson01.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson01.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson01.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson01.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson01.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson01.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson01.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/perl/lesson01.zip">Perl</a> kód této lekce. ( <a href="mailto:cahhmc@yahoo.com">Cora Hussey</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson01.gz">Python</a> kód této lekce. ( <a href="mailto:hakuin@voicenet.com">John</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/scheme/lesson01.zip">Scheme</a> kód této lekce. ( <a href="mailto:bcj1980@sbcglobal.net">Jon DuBois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson01.zip">Solaris</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson01.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson01.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson01.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(1);?>
<?FceNeHeOkolniLekce(1);?>

<?
include 'p_end.php';
?>
