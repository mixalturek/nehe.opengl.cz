<?
$g_title = 'CZ NeHe OpenGL - Lekce 47 - CG vertex shader';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(47);?>

<h1>Lekce 47 - CG vertex shader</h1>

<p class="nadpis_clanku">Pou¾ívání vertex a fragment (pixel) shaderù ke &quot;¹pinavé práci&quot; pøi renderingu mù¾e mít nespoèet výhod. Nejvíce je vidìt napø. pohyb objektù do teï výhradnì závislý na CPU, který nebì¾í na CPU, ale na GPU. Pro psaní velice kvalitních shaderù poskytuje CG (pøimìøenì) snadné rozhraní. Tento tutoriál vám uká¾e jednoduchý vertex shader, který sice nìco dìlá, ale nebude pøedvádìt ne nezbytné osvìtlení a podobné slo¾itìj¹í nadstavby. Tak jako tak je pøedev¹ím urèen pro zaèáteèníky, kteøí u¾ mají nìjaké zku¹enosti s OpenGL a zajímají se o CG.</p>

<p>Hned na zaèátku uvedu dvì internetové adresy, které by se vám mohli hodit. Jedná se o <?OdkazBlank('http://developer.nvidia.com/');?> a <?OdkazBlank('http://www.cgshaders.org/');?>.</p>

<p>Pøekl.: Perfektní èlánek o vertex a pixel shaderech vy¹el v èasopise CHIP 01/2004: Hardwarový Fotorealismus - Mo¾nosti moderních 3D grafických akcelerátorù (str. 96 - 100).</p>

<p>Poznámka: úèelem tohoto tutoriálu není nauèit úplnì v¹echno o psaní vertex shaderù pou¾ívajících CG. Má v úmyslu vysvìtlit, jak úspì¹nì nahrát a spustit vertex shader v OpenGL.</p>

<h3>Nastavení</h3>

<p>První krok spoèívá v downloadu CG kompilátoru od nVidie. Proto¾e existují rozdíly mezi verzemi 1.0 a 1.1, dbejte na to, abyste si stáhli ten novìj¹í. Kód pøelo¾ený pro jeden nemusí pracovat i s druhým. Rozdíly jsou napø. v rozdílnì pojmenovaných promìnných, nahrazených funkcích a podobnì.</p>

<p>Dále musíme nahrát hlavièkové a knihovní soubory CG na místo, kde je mù¾e Visual Studio najít. Proto¾e ze zásady nedùvìøuji instalátorùm, které povìt¹inou pracují jinak, ne¾ se oèekává, osobnì dávám pøednost ruènímu kopírování knihovních souborù</p>

<p class="src0">z: C:\Program Files\NVIDIA Corporation\Cg\lib</p>
<p class="src0">do: C:\Program Files\Microsoft Visual Studio\VC98\Lib</p>

<p>a hlavièkových souborù</p>

<p class="src0">z: C:\Program Files\NVIDIA Corporation\Cg\include</p>
<p class="src0">do: C:\Program Files\Microsoft Visual Studio\VC98\Include</p>

<h3>CG Tutoriál</h3>

<p>Informace o CG uvedené v tomto tutoriálu byly vìt¹inou získány z CG u¾ivatelského manuálu (CG Toolkit User's Manual).</p>

<p>Existuje nìkolik podstatných bodù, které byste si mìli prov¾dy zapamatovat. První a nejdùle¾itìj¹í je, ¾e se vertex program provede na KA®DÉM vertexu, který pøedáte grafické kartì. Jediná mo¾nost, jak ho spustit nad nìkolika zvolenými vertexy je buï ho nahrávat/mazat individuálnì pro ka¾dý vertex nebo posílat vertexy do proudu, ve kterém budou ovlivnìny a do proudu, kde nebudou. Výstup vertex programu je pøedán fragment (pixel) shaderu. To platí pouze tehdy, pokud je implementován a zapnut. Za poslední si zapamatujte, ¾e se vertex program provede nad vertexy pøedtím, ne¾ se vytvoøí primitiva. Fragment shader je na rozdíl od toho vykonán a¾ po rasterizaci.</p>

<p>Pojïme se koneènì podívat na tutoriál. Vytvoøíme prázdný textový soubor a pojmenujeme ho wave.cg. Do nìj budeme psát ve¹kerý CG kód. Nejdøíve vytvoøíme datové struktury, které budou obsahovat v¹echny promìnné a informace potøebné pro shader.</p>

<p>Ka¾dá ze v¹ech tøí promìnných struktury (pozice, barva a hodnota vlny) je následována pøeddefinovaným jménem (POSITION, COLOR0, COLOR1). Tato pøeddefinovaná jména se vztahují k sémantice jazyka. Specifikují mapování vstupù do pøesnì urèených hardwarových registrù. Mimochodem, jedinou opravdu po¾adovanou vstupní promìnnou do vertex programu je position.</p>

<p class="src0">struct appdata</p>
<p class="src0">{</p>
<p class="src1">float4 position : POSITION;</p>
<p class="src1">float4 color: COLOR0;</p>
<p class="src1">float3 wave: COLOR1;</p>
<p class="src0">};</p>

<p>Dále vytvoøíme strukturu vfconn. Ta bude obsahovat výstup vertex programu, který se po rasterizace pøedá fragment shaderu. Stejnì jako vstupy mají i výstupy pøeddefinovaná jména. HPos reprezentuje pozici transformovanou do homogenního souøadnicového systému a Col0 urèuje barvu vertexu zmìnìnou v programu.</p>

<p class="src0">struct vfconn</p>
<p class="src0">{</p>
<p class="src1">float4 HPos: POSITION;</p>
<p class="src1">float4 Col0: COLOR0;</p>
<p class="src0">};</p>

<p>Zbývá nám pouze napsat vertex program. Funkce se definuje stejnì jako v jazyce C. Má návratový typ (struktura vfconn), jméno (main, ale mù¾e jím být i jakékoli jiné) a parametry. V na¹em pøíkladì ze vstupu pøevezmeme strukturu appdata, která obsahuje pozici vertexu, jeho barvu a hodnotu vý¹ky pro vytvoøení sinusových vln. Dostaneme také uniformní parametr, kterým je aktuální modelview matice. Potøebujeme ji pro transformaci pozice do homogenního souøadnicového systému.</p>

<p class="src0">vfconn main(appdata IN, uniform float4x4 ModelViewProj)</p>
<p class="src0">{</p>

<p>Do promìnné OUT ulo¾íme modifikované vstupní parametry a na konci programu ji vrátíme.</p>

<p class="src1">vfconn OUT;<span class="kom">// Výstup z vertex shaderu (posílá se na fragment shader, pokud je dostupný)</span></p>

<p>Vypoèítáme pozici na ose y v závislosti na x a z pozici vertexu. X i z vydìlíme pìti (respektive ètyømi), pøechody budou jemnìj¹í. Zmìòte hodnoty na 1.0, abyste vidìli, co myslím. Promìnná IN.wave specifikovaná hlavním programem obsahuje stále se zvìt¹ující hodnotu, která zpùsobí, ¾e se sinusová vlna rozpohybuje pøes celý mesh. Y pozici spoèítáme z pozice v meshi jako sinus hodnoty vlny plus aktuální x nebo z pozice. Aby byla výsledná vlna vy¹¹í, vynásobíme je¹tì výsledek èíslem 2,5.</p>

<p class="src1"><span class="kom">// Zmìna y pozice v závislosti na sinusové vlnì</span></p>
<p class="src1">IN.position.y = (sin(IN.wave.x + (IN.position.x / 5.0)) + sin(IN.wave.x + (IN.position.z / 4.0))) * 2.5f;</p>

<p>Nastavíme výstupní promìnné na¹eho vertex programu. Nejdøíve transformujeme novou pozici vertexu do homogenního souøadnicového systému a potom pøiøadíme výstupní barvì hodnotu vstupní. Pomocí return pøedáme v¹e fragment shaderu (pokud je zapnutý).</p>


<p class="src1">OUT.HPos = mul(ModelViewProj, IN.position);<span class="kom">// Transformace pozice na homogenní souøadnice</span></p>
<p class="src1">OUT.Col0.xyz = IN.color.xyz;<span class="kom">// Nastavení barvy</span></p>
<p class="src"></p>
<p class="src1">return OUT;</p>
<p class="src0">}</p>

<h3>OpenGL Tutoriál</h3>

<p>V tuto chvíli máme vertex program bì¾ící na grafické kartì hotov. Mù¾eme se pustit do hlavního programu. Vytvoøíme v nìm rovinný mesh poskládaný z trojúhelníkù (triangle stripù), které budeme posílat na grafickou kartu. Na ní se ovlivní y pozice ka¾dého vertexu tak, aby ve výsledku vznikly pohybující se sinusové vlny.</p>

<p>V první øadì inkludujeme hlavièkové soubory, které v OpenGL umo¾ní spustit CG shader. Musíme také øíct Visual Studiu, aby pøilinkovalo potøebné knihovní soubory.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Windows</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// OpenGL</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// GLU</span></p>
<p class="src"></p>
<p class="src0">#include &lt;cg\cg.h&gt;<span class="kom">// CG hlavièky</span></p>
<p class="src0">#include &lt;cg\cggl.h&gt;<span class="kom">// CG hlavièky specifické pro OpenGL</span></p>
<p class="src"></p>
<p class="src0">#include &quot;NeHeGL.h&quot;<span class="kom">// NeHe OpenGL</span></p>
<p class="src"></p>
<p class="src0">#pragma comment(lib, &quot;opengl32.lib&quot;)<span class="kom">// Pøilinkování OpenGL</span></p>
<p class="src0">#pragma comment(lib, &quot;glu32.lib&quot;)<span class="kom">// Pøilinkování GLU</span></p>
<p class="src"></p>
<p class="src0">#pragma comment(lib, &quot;cg.lib&quot;)<span class="kom">// Pøilinkování CG</span></p>
<p class="src0">#pragma comment(lib, &quot;cggl.lib&quot;)<span class="kom">// Pøilinkování OpenGL CG</span></p>
<p class="src"></p>
<p class="src0">#define TWO_PI 6.2831853071<span class="kom">// PI * 2</span></p>
<p class="src"></p>
<p class="src0">GL_Window* g_window;<span class="kom">// Struktura okna</span></p>
<p class="src0">Keys* g_keys;<span class="kom">// Klávesnice</span></p>

<p>Symbolická konstanta SIZE urèuje velikost meshe na osách x a z. Dále vytvoøíme promìnnou cg_enable, která bude oznamovat, jestli má být vertex program zapnutý nebo vypnutý. Pole mesh slou¾í pro ulo¾ení dat meshe a wave_movement pro vytvoøení sinusové vlny.</p>

<p class="src0">#define SIZE 64<span class="kom">// Velikost meshe</span></p>
<p class="src"></p>
<p class="src0">bool cg_enable = TRUE, sp;<span class="kom">// Flag spu¹tìní CG</span></p>
<p class="src0">GLfloat mesh[SIZE][SIZE][3];<span class="kom">// Data meshe</span></p>
<p class="src0">GLfloat wave_movement = 0.0f;<span class="kom">// Pro vytvoøení sinusové vlny</span></p>

<p>Následují promìnné pro CG. CGcontext slou¾í jako kontejner pro nìkolik CG programù. Obecnì staèí pouze jeden CGcontext bez ohledu na poèet vertex a fragment programù, které vyu¾íváme. Z jednoho kontextu mù¾ete pomocí funkcí cgGetFirstProgram() a cgGetNextProgram() zvolit libovolný program. CG profile definuje profil vertexù. CG parametry zprostøedkovávají vazbu mezi hlavním programem a CG programem bì¾ícím na grafické kartì. Ka¾dý CG parameter je handle na korespondující promìnnou v shaderu.</p>

<p class="src0">CGcontext cgContext;<span class="kom">// CG kontext</span></p>
<p class="src0">CGprogram cgProgram;<span class="kom">// CG vertex program</span></p>
<p class="src0">CGprofile cgVertexProfile;<span class="kom">// CG profil</span></p>
<p class="src0">CGparameter position, color, modelViewMatrix, wave;<span class="kom">// Parametry pro shader</span></p>

<p>Deklaraci globálních promìnných máme za sebou, pojïme se podívat na inicializaèní funkci. Po obvyklých nastaveních zapneme vykreslování drátìných modelù. Pou¾íváme je z dùvodu, ¾e vyplnìné polygony nevypadají bez svìtel dobøe. Pomocí dvou vnoøených cyklù inicializujeme pole mesh tak, aby se støed roviny nacházel v poèátku souøadnicového systému. Pozici na ose y nastavíme u v¹ech bodù na 0.0f, sinusovou deformaci má na starosti CG program.</p>

<p class="src0">BOOL Initialize(GL_Window* window, Keys* keys)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;<span class="kom">// Okno</span></p>
<p class="src1">g_keys = keys;<span class="kom">// Klávesnice</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Mazání hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testování hloubky</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí testování hloubky</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nastavení perspektivy</span></p>
<p class="src"></p>
<p class="src1">glPolygonMode(GL_FRONT_AND_BACK, GL_LINE);<span class="kom">// Drátìný model</span></p>
<p class="src"></p>
<p class="src1">for (int x = 0; x &lt; SIZE; x++)<span class="kom">// Inicializace meshe</span></p>
<p class="src1">{</p>
<p class="src2">for (int z = 0; z &lt; SIZE; z++)</p>
<p class="src2">{</p>
<p class="src3">mesh[x][z][0] = (float) (SIZE / 2) - x;<span class="kom">// Vycentrování na ose x</span></p>
<p class="src3">mesh[x][z][1] = 0.0f;<span class="kom">// Plochá rovina</span></p>
<p class="src3">mesh[x][z][2] = (float) (SIZE / 2) - z;<span class="kom">// Vycentrování na ose z</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Musíme také inicializovat CG, jako první vytvoøíme kontext. Pokud funkce vrátí NULL, nìco selhalo, chyby vìt¹inou nastávají kvùli nepovedené alokaci pamìti. Zobrazíme chybovou zprávu a vrátíme false, èím¾ ukonèíme i celý program.</p>

<p class="src1">cgContext = cgCreateContext();<span class="kom">// Vytvoøení CG kontextu</span></p>
<p class="src"></p>
<p class="src1">if (cgContext == NULL)<span class="kom">// OK?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Failed To Create Cg Context&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>

<p>Pomocí cgGLGetLatestProfile() urèíme minulý profil vertexù, za typ profilu pøedáme CG_GL_VERTEX. Kdybychom vytváøeli fragment shader, pøedávali bychom CG_GL_FRAGMENT. Pokud není ¾ádný vhodný profil dostupný, vrátí funkce CG_PROFILE_UNKNOWN. S validním profilem mù¾eme zavolat cgGLSetOptimalOptions(). Tato funkce se pou¾ívá poka¾dé, kdy¾ se pøekládá nový CG program, proto¾e podstatnì optimalizuje kompilaci shaderu v závislosti na aktuálním grafickém hardwaru a jeho ovladaèích.</p>

<p class="src1">cgVertexProfile = cgGLGetLatestProfile(CG_GL_VERTEX);<span class="kom">// Získání minulého profilu vertexù</span></p>
<p class="src"></p>
<p class="src1">if (cgVertexProfile == CG_PROFILE_UNKNOWN)<span class="kom">// OK?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Invalid profile type&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">cgGLSetOptimalOptions(cgVertexProfile);<span class="kom">// Nastavení profilu</span></p>

<p>Zavoláme funkci cgCreateprogramFromFile(), èím¾ naèteme a zkompilujeme CG program. První parametr specifikuje CG kontext, ke kterému bude program pøipojen. Druhý parametr urèuje, ¾e soubor obsahuje zdrojový kód (CG_SOURCE) a ne objektový kód pøedkompilovaného programu (CG_OBJECT). Jako tøetí polo¾ka se pøedává cesta k souboru, ètvrtý je minulým profilem pro konkrétní typ programu (vertex profil pro vertex program, fragment profil pro fragment program). Pátý parametr specifikuje vstupní funkci do programu, její jméno mù¾e být libovolné, ne pouze main(). Poslední parametr slou¾í pro pøedání pøídavných argumentù kompilátoru. Vìt¹inou se dává NULL.</p>

<p>Pokud z nìjakého dùvodu funkce sel¾e, získáme pomocí cgGetError() typ chyby. Do øetìzcové podoby ho mù¾eme pøevést prostøednictvím cgGetErrorString().</p>

<p class="src1"><span class="kom">// Nahraje a zkompiluje vertex shader</span></p>
<p class="src1">cgProgram = cgCreateProgramFromFile(cgContext, CG_SOURCE, &quot;CG/Wave.cg&quot;, cgVertexProfile, &quot;main&quot;, 0);</p>
<p class="src"></p>
<p class="src1">if (cgProgram == NULL)<span class="kom">// OK?</span></p>
<p class="src1">{</p>
<p class="src2">CGerror Error = cgGetError();<span class="kom">// Typ chyby</span></p>
<p class="src"></p>
<p class="src2">MessageBox(NULL, cgGetErrorString(Error), &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>

<p>Nahrajeme zkompilovaný program a pøipravíme ho pro zvolení (binding).</p>

<p class="src1">cgGLLoadProgram(cgProgram);<span class="kom">// Nahraje program do grafické karty</span></p>

<p>Jako poslední krok inicializace získáme handle na promìnné, se kterými bude CG program manipulovat. Pokud daná promìnná neexistuje, cgGetNamedParameter() vrátí NULL. Neznáme-li jména parametrù, mù¾eme pou¾ít dvojici funkcí cgGetFirstParameter() a cgGetNextParameter().</p>

<p class="src1"><span class="kom">// Handle na promìnné</span></p>
<p class="src1">position = cgGetNamedParameter(cgProgram, &quot;IN.position&quot;);</p>
<p class="src1">color = cgGetNamedParameter(cgProgram, &quot;IN.color&quot;);</p>
<p class="src1">wave = cgGetNamedParameter(cgProgram, &quot;IN.wave&quot;);</p>
<p class="src1">modelViewMatrix = cgGetNamedParameter(cgProgram, &quot;ModelViewProj&quot;);</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Pomocí deinicializaèní funkce po sobì uklidíme. Jednodu¹e zavoláme cgDestroyContext() pro ka¾dý CGcontext promìnnou. Také bychom mohli smazat jednotlivé CG programy, k tomu slou¾í funkce cgDestroyProgram(), nicménì cgDestroyContext() je sma¾e automaticky.</p>

<p class="src0">void Deinitialize(void)<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">cgDestroyContext(cgContext);<span class="kom">// Sma¾e CG kontext</span></p>
<p class="src0">}</p>

<p>Do aktualizaèní funkce pøidáme kód pro o¹etøení stisku mezerníku, který zapíná/vypíná CG program bì¾ící na grafické kartì.</p>

<p class="src0">void Update(float milliseconds)<span class="kom">// Aktualizace</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown[VK_ESCAPE])<span class="kom">// Stisk Esc</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication(g_window);<span class="kom">// Konec programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_F1])<span class="kom">// Stisk F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// Pøepnutí do/z fullscreenu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[' '] &amp;&amp; !sp)<span class="kom">// Stisk mezerníku</span></p>
<p class="src1">{</p>
<p class="src2">sp = TRUE;</p>
<p class="src2">cg_enable = !cg_enable;<span class="kom">// Zapne/vypne CG program</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown[' '])</p>
<p class="src1">{</p>
<p class="src2">sp = FALSE;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>A jako poslední vykreslování. Kamerou se pøesuneme o 45 jednotek pøed poèátek souøadnicového systému a nahoru o 25 jednotek.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">gluLookAt(0.0f, 25.0f, -45.0f, 0.0f, 0.0f, 0.0f, 0, 1, 0);<span class="kom">// Pozice kamery</span></p>

<p>Modelview matici vertex shaderu nastavíme na aktuální OpenGL matici. Bez toho bychom nemohli pøepoèítávat pozici vertexù do homogenních souøadnic.</p>

<p class="src1"><span class="kom">// Nastavení modelview matice v shaderu</span></p>
<p class="src1">cgGLSetStateMatrixParameter(modelViewMatrix, CG_GL_MODELVIEW_PROJECTION_MATRIX, CG_GL_MATRIX_IDENTITY);</p>

<p>Pokud je flag cg_enable v true, voláním cgGLEnableProfile() aktivujeme pøedaný profil. Funkce cgGLBindProgram() zvolí ná¹ program a dokud ho nevypneme, provede se nad ka¾dým vertexem poslaným na grafickou kartu. Také musíme poslat barvu vertexù.</p>

<p class="src1">if (cg_enable)<span class="kom">// Zapnout CG shader?</span></p>
<p class="src1">{</p>
<p class="src2">cgGLEnableProfile(cgVertexProfile);<span class="kom">// Zapne profil</span></p>
<p class="src2">cgGLBindProgram(cgProgram);<span class="kom">// Zvolí program</span></p>
<p class="src2">cgGLSetParameter4f(color, 0.5f, 1.0f, 0.5f, 1.0f);<span class="kom">// Nastaví barvu (svìtle zelená)</span></p>
<p class="src1">}</p>

<p>Tak teï jsme koneènì pøipraveni na rendering meshe. Pro ka¾dou hodnotu souøadnice x v cyklu vykreslíme prou¾ek roviny seskládaný triangle stripem.</p>

<p class="src1">for (int x = 0; x &lt; SIZE - 1; x++)<span class="kom">// Vykreslení meshe</span></p>
<p class="src1">{</p>
<p class="src2">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Ka¾dý prou¾ek jedním triangle stripem</span></p>
<p class="src"></p>
<p class="src2">for (int z = 0; z &lt; SIZE - 1; z++)</p>
<p class="src2">{</p>

<p>Souèasnì s renderovanými vertexy dynamicky pøedáme i hodnotu wave parametru, díky kterému bude moci CG program z roviny vygenerovat sinusové vlny. Jakmile grafická karta dostane v¹echna data, automaticky spustí CG program. V¹imnìte si, ¾e do triangle stripu posíláme dva body, to má za následek, ¾e se nevykreslí pouze trojúhelník, ale rovnou celý ètverec.</p>

<p class="src3">cgGLSetParameter3f(wave, wave_movement, 1.0f, 1.0f);<span class="kom">// Parametr vlny</span></p>
<p class="src"></p>
<p class="src3">glVertex3f(mesh[x][z][0], mesh[x][z][1], mesh[x][z][2]);<span class="kom">// Vertex</span></p>
<p class="src3">glVertex3f(mesh[x+1][z][0], mesh[x+1][z][1], mesh[x+1][z][2]);<span class="kom">// Vertex</span></p>
<p class="src"></p>
<p class="src3">wave_movement += 0.00001f;<span class="kom">// Inkrementace parametru vlny</span></p>
<p class="src"></p>
<p class="src3">if (wave_movement &gt; TWO_PI)<span class="kom">// Vìt¹í ne¾ dvì pí (6,28)?</span></p>
<p class="src3">{</p>
<p class="src4">wave_movement = 0.0f;<span class="kom">// Vynulovat</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec triangle stripu</span></p>
<p class="src1">}</p>

<p>Po dokonèení renderingu otestujeme, jestli je cg_enable rovno true a pokud ano, vypneme vertex profil. Dále mù¾eme kreslit cokoli chceme, ani¾ by to bylo ovlivnìno CG programem.</p>

<p class="src1">if (cg_enable)<span class="kom">// Zapnutý CG shader?</span></p>
<p class="src1">{</p>
<p class="src2">cgGLDisableProfile(cgVertexProfile);<span class="kom">// Vypne profil</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vyprázdnìní renderovací pipeline</span></p>
<p class="src0">}</p>

<p class="autor">napsal: Owen Bourne <?VypisEmail('o.bourne@griffith.edu.au');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson47.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson47.tar.gz">Linux/GLut</a> kód této lekce. ( <a href="mailto:foxdie@pobox.sk">Gray Fox</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson47.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:r3lik@shaw.ca">Jason Schultz</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson47.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(47);?>
<?FceNeHeOkolniLekce(47);?>

<?
include 'p_end.php';
?>
