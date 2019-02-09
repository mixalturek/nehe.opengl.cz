<?
$g_title = 'CZ NeHe OpenGL - Lekce 42 - Více viewportù';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(42);?>

<h1>Lekce 42 - Více viewportù</h1>

<p class="nadpis_clanku">Tento tutoriál byl napsán pro v¹echny z vás, kteøí se chtìli dozvìdìt, jak do jednoho okna zobrazit více pohledù na jednu scénu, kdy v ka¾dém probíhá jiný efekt. Jako bonus pøidám získávání velikosti OpenGL okna a velice rychlý zpùsob aktualizace textury bez jejího znovuvytváøení.</p>

<p>Vítejte do dal¹ího perfektního tutoriálu. Tentokrát se pokusíme v jednom oknì zobrazit ètyøi viewporty, které se budou pøi zmìnì velikosti okna bez problémù zmen¹ovat i zvìt¹ovat. Ve dvou z nich zapneme svìtla, jeden bude pou¾ívat pravoúhlou projekci a tøi perspektivní. Abychom demu zajistili kvalitní efekty, budeme do textury postupnì generovat pùdorys bludi¹tì a mapovat ji na objekty v jednotlivých viewportech.</p>

<p>Jakmile jednou porozumíte tomuto tutoriálu, nebudete mít nejmen¹í problémy pøi vytváøení her pro více hráèù s rozdìlenými scénami nebo 3D aplikací, ve kterých potøebujete nìkolik pohledù na modelovaný objekt (pùdorys, nárys, bokorys, drátìný model ap.).</p>

<p>Jako základní kód mù¾ete pou¾ít buï nejnovìj¹í NeHeGL nebo IPicture. Je to, dá se øíct, jedno, ale provedeme v nìm nìkolik úprav. Nejdùle¾itìj¹í zmìnu najdete ve funkci ReshapeGL(), ve které se definují dimenze scény (hlavní viewport). V¹echna nastavení pøesuneme do vykreslovací smyèky, zùstane zde pouze definování rozmìrù hlavního okna.</p>

<p class="src0">void ReshapeGL(int width, int height)<span class="kom">// Volá se pøi zmìnì velikosti okna</span></p>
<p class="src0">{</p>
<p class="src1">glViewport(0, 0, (GLsizei)(width), (GLsizei)(height));<span class="kom">// Reset aktuálního viewportu</span></p>
<p class="src0">}</p>

<p>Druhá zmìna spoèívá v o¹etøení systémové události WM_ERASEBKGND. Ukonèením funkce zamezíme rùznému mihotání a blikání scény pøi roztahování okna, kdy systém automaticky ma¾e pozadí. Pokud nerozumíte, odstraòte oba øádky a porovnejte chování okna pøi zmìnì jeho velikosti.</p>

<p class="src0"><span class="kom">// Funkce WindowProc()</span></p>
<p class="src1">switch (uMsg)<span class="kom">// Vìtvení podle do¹lé zprávy</span></p>
<p class="src1">{</p>
<p class="src2">case WM_ERASEBKGND:<span class="kom">//Okno zkou¹í smazat pozadí</span></p>
<p class="src3">return 0;<span class="kom">// Zákaz mazání (prevence blikání)</span></p>

<p>Nyní pøejdeme k opravdovému kódu tohoto tutoriálu. Zaèneme deklarací globálních promìnných. Mx a my specifikují místnost v bludi¹ti, ve které se právì nacházíme. Width a height definují rozmìry textury, ka¾dému pixelu bludi¹tì odpovídá jeden pixel na textuøe. Pokud va¹e grafická karta podporuje vìt¹í textury, zkuste zvìt¹it toto èíslo na následující násobky dvou napø. 256, 512, 1024. Ujistìte se ale, ¾e ho nezvìt¹íte pøíli¹ mnoho. Má-li napøíklad okno ¹íøku 1024 pixelù, viewporty budou polovièní, tak¾e nemá cenu, aby textura byla vìt¹í ne¾ 512, proto¾e by se stejnì zmen¹ovala. To samé samozøejmì platí i pro vý¹ku.</p>

<p class="src0">int mx, my;<span class="kom">// Øídící promìnné cyklù</span></p>
<p class="src"></p>
<p class="src0">const width = 128;<span class="kom">// ©íøka textury (musí být mocninou èísla 2)</span></p>
<p class="src0">const height = 128;<span class="kom">// Vý¹ka textury (musí být mocninou èísla 2)</span></p>

<p>Done povede záznam o tom, jestli u¾ bylo generování bludi¹tì dokonèeno. Více podrobností se dozvíte pozdìji. Sp pou¾íváme k o¹etøení toho, aby program nebral dlouhý stisk mezerníku za nìkolik spolu nesouvisejících stiskù. Po jeho zmáèknutí resetujeme texturu a zaèneme kreslit bludi¹tì od znova.</p>

<p class="src0">BOOL done;<span class="kom">// Bludi¹tì vygenerováno?</span></p>
<p class="src0">BOOL sp;<span class="kom">// Flag stisku mezerníku</span></p>

<p>Ètyøprvková pole r, g, b ukládají slo¾ky barev pro jednotlivé viewporty. Pou¾íváme datový typ BYTE, proto¾e se lépe získávají náhodná èísla od 0 do 255 ne¾ od 0.0f do 1.0f. Tex_data ukazuje na pamì» dat textury.</p>

<p class="src0">BYTE r[4], g[4], b[4];<span class="kom">// Ètyøi náhodné barvy</span></p>
<p class="src0">BYTE* tex_data;<span class="kom">// Data textury</span></p>

<p>Xrot, yrot a zrot specifikují úhel rotace 3D objektu na jednotlivých souøadnicových osách. Quadratic pou¾ijeme pro kreslení koule a válce.</p>

<p class="src0">GLfloat xrot, yrot, zrot;<span class="kom">// Úhly rotací objektù</span></p>
<p class="src0">GLUquadricObj *quadric;<span class="kom">// Objekt quadraticu</span></p>

<p>Pomocí následující funkce budeme moci snadno zabílit pixel textury na souøadnicích dmx, dmy. Tex_data pøedstavuje ukazatel na data textury. Lokaci pixelu získáme vynásobením y pozice (dmy) ¹íøkou øádku (width) a pøiètením pozice na øádku (dmx). Proto¾e se ka¾dý pixel skládá ze tøí bytù násobíme výsledek tøemi. Aby koneèná barva byla bílá, musíme pøiøadit èíslo 255 v¹em tøem barevným slo¾kám.</p>

<p class="src0">void UpdateTex(int dmx, int dmy)<span class="kom">// Zabílí urèený pixel na textuøe</span></p>
<p class="src0">{</p>
<p class="src1">tex_data[0 + ((dmx + (width * dmy)) * 3)] = 255;<span class="kom">// Èervená slo¾ka</span></p>
<p class="src1">tex_data[1 + ((dmx + (width * dmy)) * 3)] = 255;<span class="kom">// Zelená slo¾ka</span></p>
<p class="src1">tex_data[2 + ((dmx + (width * dmy)) * 3)] = 255;<span class="kom">// Modrá slo¾ka</span></p>
<p class="src0">}</p>

<p>Reset má na starosti nìkolik relativnì dùle¾itých úkonù. V první øadì kompletnì zaèerní texturu a tím odstraní dosavadní bludi¹tì, dále pøiøazuje nové barvy viewportùm a reinicializuje pozici v bludi¹ti. První øádkou kódu nulujeme data textury, co¾ ve výsledku znamená, ¾e v¹echny pixely budou èerné.</p>

<p class="src0">void Reset(void)<span class="kom">// Reset textury, barev, aktuální pozice v bludi¹ti</span></p>
<p class="src0">{</p>
<p class="src1">ZeroMemory(tex_data, width * height * 3);<span class="kom">// Nuluje pamì» textury</span></p>

<p>Potøebujeme nastavit náhodnou barvu viewportù. Pro ty z vás, kteøí to je¹tì neví, random není zase tak náhodný, jak by se mohl na první pohled zdát. Pokud vytvoøíte jednoduchý program, který má vypsat deset náhodných èísel, tak samozøejmì vypí¹e deset náhodných èísel, která nemáte ¹anci pøedem odhadnout. Ale pøi pøí¹tím spu¹tìní se bude v¹ech deset &quot;náhodných&quot; èísel opakovat. Abychom tento problém odstranili, inicializujeme generátor. Pokud bychom ho ale nastavili na konstantní hodnotu (1, 2, 3...), výsledkem by opìt byla pøi více spu¹tìních stejná èísla. Proto pøedáváme funkci srand() hodnotu aktuálního èasu (Pøekl.: poèet milisekund od spu¹tìní OS), který se samozøejmì v¾dy mìní.</p>

<p>Pøekl.: Bývá zvykem inicializovat generátor náhodných èísel pouze jednou a to nìkde na zaèátku funkce main() a ne, jak dìláme zde, pøi ka¾dém volání Reset() - není to ¹patnì, ale je to zbyteèné.</p>

<p class="src1">srand(GetTickCount());<span class="kom">// Inicializace generátoru náhodných èísel</span></p>

<p>V cyklu, který projde v¹echny ètyøi viewporty nastavujeme pro ka¾dý náhodnou barvu. Mohli bychom generovat èíslo v plném rozsahu (0 a¾ 255), ale nemìli bychom tak zaruèeno, ¾e nezískáme nìjakou nízkou hodnotu (aby na èerné byla vidìt). Pøiètením 128 získáme svìtlej¹í barvy.</p>

<p class="src1">for (int loop = 0; loop &lt; 4; loop++)<span class="kom">// Generuje ètyøi náhodné barvy</span></p>
<p class="src1">{</p>
<p class="src2">r[loop] = rand() % 128 + 128;<span class="kom">// Èervená slo¾ka</span></p>
<p class="src2">g[loop] = rand() % 128 + 128;<span class="kom">// Zelená slo¾ka</span></p>
<p class="src2">b[loop] = rand() % 128 + 128;<span class="kom">// Modrá slo¾ka</span></p>
<p class="src1">}</p>

<p>Nakonec nastavíme poèáteèní bod v bludi¹ti - opìt náhodný. Výsledkem musí být sudé èíslo (zajistí násobení dvìma), proto¾e liché pozice oznaèují stìny mezi místnostmi.</p>

<p class="src1">mx = int(rand() % (width / 2)) * 2;<span class="kom">// Náhodná x pozice</span></p>
<p class="src1">my = int(rand() % (height / 2)) * 2;<span class="kom">// Náhodná y pozice</span></p>
<p class="src0">}</p>

<p>Prvním øádkem v inicializaci alokujeme dynamickou pamì» pro ulo¾ení textury.</p>

<p class="src0">BOOL Initialize (GL_Window* window, Keys* keys)<span class="kom">// Inicializace OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">tex_data = new BYTE[width * height * 3];<span class="kom">// Alokace pamìti pro texturu</span></p>
<p class="src"></p>
<p class="src1">g_window = window;<span class="kom">// Okno</span></p>
<p class="src1">g_keys = keys;<span class="kom">// Klávesy</span></p>

<p>Voláme Reset(), abychom ji zaèernili a nastavili barvy viewportù.</p>

<p class="src1">Reset();<span class="kom">// Reset textury, barev, pozice</span></p>

<p>Inicializaci textury zaèneme nastavením clamp parametrù do rozmezí [0; 1]. Tímto odstraníme mo¾né artefakty v podobì tenkých linek, které vznikají na okrajích textury. Pøíèina jejich zobrazování spoèívá v lineárním filtrování, které se pokou¹í vyhladit texturu, ale zahrnuje do ní i její okraje. Zkuste odstranit první dva øádky a uvidíte, co myslím. Jak u¾ jsem zmínil, nastavíme lineární filtrování a vytvoøíme texturu.</p>

<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_S, GL_CLAMP);<span class="kom">// Clamp parametry textury</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_T, GL_CLAMP);</p>
<p class="src"></p>
<p class="src1">glTexParameterf(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);<span class="kom">// Lineární filtrování</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR); </p>
<p class="src"></p>
<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, GL_RGB, width, height, 0, GL_RGB, GL_UNSIGNED_BYTE, tex_data);<span class="kom">// Vytvoøí texturu</span></p>

<p>Pozadí budeme mazat èernou barvou a hloubku jednièkou. Dále nastavíme testování hloubky na men¹í nebo rovno a zapneme ho.</p>

<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení depth bufferu</span></p>
<p class="src"></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>

<p>Povolení GL_COLOR_MATERIAL umo¾ní mìnit barvu textury pou¾itím funkce glColor3f(). Také zapínáme mapování textur.</p>

<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Zapne vybarvování materiálù</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapování textur</span></p>

<p>Vytvoøíme a inicializujeme objekt quadraticu tak, aby obsahoval normálové vektory pro svìtlo a texturové koordináty.</p>

<p class="src1">quadric = gluNewQuadric();<span class="kom">// Vytvoøí objekt quadraticu</span></p>
<p class="src1">gluQuadricNormals(quadric, GLU_SMOOTH);<span class="kom">// Normály pro svìtlo</span></p>
<p class="src1">gluQuadricTexture(quadric, GL_TRUE);<span class="kom">// Texturové koordináty</span></p>

<p>I kdy¾ je¹tì nemáme povoleny svìtla globálnì, zapneme svìtlo 0.</p>

<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne svìtlo 0</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹e v poøádku</span></p>
<p class="src0">}</p>

<p>Po jakékoli alokaci dynamické pamìti musí pøijít její uvolnìní. Tuto akci vlo¾íme do funkce Deinitialize(), která se volá pøed ukonèením programu.</p>

<p class="src0">void Deinitialize(void)<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">delete [] tex_data;<span class="kom">// Sma¾e data textury</span></p>
<p class="src0">}</p>

<p>Update má na starosti aktualizaci zobrazované scény, stisky kláves, pohyby, rotace a podobnì. Celoèíselnou promìnnou dir vyu¾ijeme k pohybu náhodným smìrem.</p>

<p class="src0">void Update(float milliseconds)<span class="kom">// Aktualizace scény</span></p>
<p class="src0">{</p>
<p class="src1">int dir;<span class="kom">// Ukládá aktuální smìr pohybu</span></p>

<p>V první fázi o¹etøíme klávesnici. Pøi stisku Esc ukonèíme program, F1 pøepíná mód fullscreen/okno a mezerník resetuje bludi¹tì.</p>

<p class="src1">if (g_keys-&gt;keyDown[VK_ESCAPE])<span class="kom">// Klávesa Esc</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication (g_window);<span class="kom">// Konec programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_F1])<span class="kom">// Klávesa F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen (g_window);<span class="kom">// Pøepne fullscreen/okno</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[' '] &amp;&amp; !sp)<span class="kom">// Mezerník</span></p>
<p class="src1">{</p>
<p class="src2">sp = TRUE;</p>
<p class="src2">Reset();<span class="kom">// Resetuje scénu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown[' '])<span class="kom">// Uvolnìní mezerníku</span></p>
<p class="src1">{</p>
<p class="src2">sp = FALSE;</p>
<p class="src1">}</p>

<p>Rot promìnné zvìt¹íme v závislosti na poètu uplynulých milisekund od minulého volání této funkce. Tím zajistíme rotaci objektù.</p>

<p class="src1">xrot += (float)(milliseconds) * 0.02f;<span class="kom">// Aktualizace úhlù natoèení</span></p>
<p class="src1">yrot += (float)(milliseconds) * 0.03f;</p>
<p class="src1">zrot += (float)(milliseconds) * 0.015f;</p>

<p>Kód ní¾e zji¹»uje, jestli bylo kreslení bludi¹tì ukonèeno (textura kompletnì zaplnìna). Nejdøíve nastavíme flag done na true. Pøedpokládáme tedy, ¾e u¾ vykresleno bylo. Ve dvou vnoøených cyklech procházíme jednotlivé øádky i sloupce a kontrolujeme, zda byl ná¹ odhad správný. Pokud ne, nastavíme done na false.</p>

<p>Jak pracuje kód? Øídící promìnné cyklù zvy¹ujeme o dva, proto¾e nám jde jen o sudé indexy v poli. Ka¾dé bludi¹tì se skládá ze stìn (liché) a místností (sudé). Kdy¾ otevøeme dveøe, dostaneme se do místnosti a právì ty tedy musíme testovat. Kontroly stìn jsou samozøejmì zbyteèné. Pokud se hodnota v poli rovná nule, znamená to, ¾e jsme do nìj je¹tì nekreslili a místnost nebyla nav¹tívena.</p>

<p class="src1">done = TRUE;<span class="kom">// Pøedpokládá se, ¾e je u¾ bludi¹tì kompletní</span></p>
<p class="src"></p>
<p class="src1">for (int x = 0; x &lt; width; x += 2)<span class="kom">// Prochází v¹echny místnosti na ose x</span></p>
<p class="src1">{</p>
<p class="src2">for (int y = 0; y &lt; height; y += 2)<span class="kom">// Prochází v¹echny místnosti na ose y</span></p>
<p class="src2">{</p>
<p class="src3">if (tex_data[((x + (width * y)) * 3)] == 0)<span class="kom">// Pokud má pixel èernou barvu</span></p>
<p class="src3">{</p>
<p class="src4">done = FALSE;<span class="kom">// Bludi¹tì je¹tì není hotové</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Pokud byly v¹echny místnosti objeveny, zmìníme titulek okna na ...Maze Complete!, potom poèkáme pìt sekund, aby si ho stihl u¾ivatel pøeèíst, vrátíme titulek zpìt a resetujeme bludi¹tì.</p>

<p class="src1">if (done)<span class="kom">// Je bludi¹tì hotové?</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Zmìna titulku okna</span></p>
<p class="src2">SetWindowText(g_window-&gt;hWnd, &quot;Lesson 42: Multiple Viewports... 2003 NeHe Productions... Maze Complete!&quot;);</p>
<p class="src2">Sleep(5000);<span class="kom">// Zastavení na pìt sekund</span></p>
<p class="src"></p>
<p class="src2">SetWindowText(g_window-&gt;hWnd, &quot;Lesson 42: Multiple Viewports... 2003 NeHe Productions... Building Maze!&quot;);</p>
<p class="src2">Reset();<span class="kom">// Reset bludi¹tì a scény</span></p>
<p class="src1">}</p>

<p>Pøedpokládám, ¾e pro vás následující podmínka vypadá totálnì ¹ílenì, ale vùbec není tì¾ká. skládá se ze ètyø AND-ovaných podpodmínek a ka¾dá z nich ze dvou dal¹ích. V¹echny ètyøi hlavní èásti jsou skoro stejné a dohromady zji¹»ují, jestli existuje místnost okolo aktuální pozice, která je¹tì nebyla nav¹tívena. V¹e si vysvìtlíme na první z podpodmínek: Nejprve se ptáme, jestli jsme v místnosti vpravo u¾ byli a potom, jestli jsou vpravo je¹tì nìjaké místnosti (kvùli okraji textury). Pokud se èervená slo¾ka pixelu rovná 255, podmínka platí. Okraj textury v daném smìru nalezneme také snadno.</p>

<p>To samé vykonáme pro v¹echny smìry a pokud nemáme kam jít, musíme vygenerovat novou pozici. V¹e si ztí¾íme tím, ¾e chceme, abychom se objevili na pozici, která u¾ byla nav¹tívena. Pokud ne, vygenerujeme v cyklu dal¹í souøadnici. Mo¾ná se ptáte, proè hledáme nav¹tívenou místnost? Proto¾e nechceme spoustu malých oddìlených èástí bludi¹tì, ale jedno obrovské. Doká¾ete si to pøedstavit?</p>

<p>Zdá se vám to moc slo¾ité? Abychom udr¾eli velikost kódu na minimu, nekontrolujeme, jestli je mx-2 men¹í ne¾ nula a podobnì pro v¹echny smìry. Pokud si pøejete 100% o¹etøení chyb, modifikujte podmínku tak, aby netestovala pamì», která u¾ nepatøí textuøe.</p>


<p class="src1"><span class="kom">// Máme kam jít?</span></p>
<p class="src1">if (((tex_data[(((mx+2)+(width*my))*3)] == 255) || mx&gt;(width-4)) &amp;&amp; ((tex_data[(((mx-2)+(width*my))*3)] == 255) || mx&lt;2) &amp;&amp; ((tex_data[((mx+(width*(my+2)))*3)] == 255) || my&gt;(height-4)) &amp;&amp; ((tex_data[((mx+(width*(my-2)))*3)] == 255) || my&lt;2))</p>
<p class="src1">{</p>
<p class="src2">do</p>
<p class="src2">{</p>
<p class="src3">mx = int(rand() % (width / 2)) * 2;<span class="kom">// Nová pozice</span></p>
<p class="src3">my = int(rand() % (height / 2)) * 2;</p>
<p class="src2">}</p>
<p class="src2">while (tex_data[((mx + (width * my)) * 3)] == 0);<span class="kom">// Hledá se nav¹tívená místnost</span></p>
<p class="src1">}</p>

<p>Do promìnné dir vygenerujeme náhodné èíslo od nuly do tøí, které vyjadøuje smìr, kterým se pokusíme jít.</p>

<p class="src1">dir = int(rand() % 4);<span class="kom">// Náhodný smìr pohybu</span></p>

<p>Pokud se rovná nule (smìr doprava) a pokud nejsme na okraji bludi¹tì (textury), zkontrolujeme, jestli u¾ byla místnost vpravo nav¹tívena. Pokud ne, oznaèíme dveøe (pixel stìny, ne místnosti) jako nav¹tívené a projdeme do dal¹í místnosti.</p>

<p class="src1">if ((dir == 0) &amp;&amp; (mx &lt;= (width-4)))<span class="kom">// Smìr doprava; vpravo je místo</span></p>
<p class="src1">{</p>
<p class="src2">if (tex_data[(((mx+2) + (width*my)) * 3)] == 0)<span class="kom">// Místnost vpravo je¹tì nebyla nav¹tívena</span></p>
<p class="src2">{</p>
<p class="src3">UpdateTex(mx+1, my);<span class="kom">// Oznaèí prùchod mezi místnostmi</span></p>
<p class="src3">mx += 2;<span class="kom">// Posunutí doprava</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Analogicky o¹etøíme v¹echny dal¹í smìry.</p>

<p class="src1">if ((dir == 1) &amp;&amp; (my &lt;= (height-4)))<span class="kom">// Smìr dolù; dole je místo</span></p>
<p class="src1">{</p>
<p class="src2">if (tex_data[((mx + (width * (my+2))) * 3)] == 0)<span class="kom">// Místnost dole je¹tì nebyla nav¹tívena</span></p>
<p class="src2">{</p>
<p class="src3">UpdateTex(mx, my+1);<span class="kom">// Oznaèí prùchod mezi místnostmi</span></p>
<p class="src3">my += 2;<span class="kom">// Posunutí dolù</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if ((dir == 2) &amp;&amp; (mx &gt;= 2))<span class="kom">// Smìr doleva; vlevo je místo</span></p>
<p class="src1">{</p>
<p class="src2">if (tex_data[(((mx-2) + (width*my)) * 3)] == 0)<span class="kom">// Místnost vlevo je¹tì nebyla nav¹tívena</span></p>
<p class="src2">{</p>
<p class="src3">UpdateTex(mx-1, my);<span class="kom">// Oznaèí prùchod mezi místnostmi</span></p>
<p class="src3">mx -= 2;<span class="kom">// Posunutí doleva</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if ((dir == 3) &amp;&amp; (my &gt;= 2))<span class="kom">// Smìr nahoru; nahoøe je místo</span></p>
<p class="src1">{</p>
<p class="src2">if (tex_data[((mx + (width * (my-2))) * 3)] == 0)<span class="kom">// Místnost nahoøe je¹tì nebyla nav¹tívena</span></p>
<p class="src2">{</p>
<p class="src3">UpdateTex(mx, my-1);<span class="kom">// Oznaèí prùchod mezi místnostmi</span></p>
<p class="src3">my -= 2;<span class="kom">// Posunutí nahoru</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Po pøesunutí se do nové místnosti ji musíme oznaèit.</p>

<p class="src1">UpdateTex(mx, my);<span class="kom">// Oznaèení nové místnosti</span></p>
<p class="src0">}</p>

<p>Vykreslování zaèneme netradiènì. Potøebujeme zjistit velikost klientské oblasti okna, abychom mohli jednotlivé viewporty roztahovat korektnì. Deklarujeme objekt struktury obdélníku a nagrabujeme do nìj souøadnice okna. ©íøku a vý¹ku spoèítáme jednoduchým odeètením.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">RECT rect;<span class="kom">// Struktura obdélníku</span></p>
<p class="src"></p>
<p class="src1">GetClientRect(g_window-&gt;hWnd, &amp;rect);<span class="kom">// Grabování rozmìrù okna</span></p>
<p class="src"></p>
<p class="src1">int window_width = rect.right - rect.left;<span class="kom">// ©íøka okna</span></p>
<p class="src1">int window_height = rect.bottom - rect.top;<span class="kom">// Vý¹ka okna</span></p>

<p>Texturu musíme aktualizovat pøi ka¾dém pøekreslení scény. Nejrychlej¹í metodou je pøíkaz glTexSubImage2D(), který namapuje jakoukoli èást obrázku na objekt ve scénì jako texturu. První parametr oznamuje, ¾e chceme pou¾ít 2D texturu. Èíslo úrovní detailù nastavíme na nulu a také nechceme ¾ádný x ani y offset. ©íøka a vý¹ka je urèena rozmìry obrázku. Ka¾dý pixel se skládá z RGB slo¾ek a data jsou ve formátu bezznaménkových bytù. Poslední parametr pøedstavuje ukazatel na zaèátek dat.</p>

<p>Jak jsem ji¾ napsal, funkcí glTexSubImage2D() velmi rychle aktualizujeme texturu bez nutnosti jejího opakovaného smazání a sestavení. Tento pøíkaz ji ale NEVYTVÁØÍ!!! Musíte ji tedy sestavit pøed první aktualizací, v na¹em pøípadì se jedná o glTexImage2D() ve funkci Initialize().</p>

<p class="src1"><span class="kom">// Zvolí aktualizovanou texturu</span></p>
<p class="src1">glTexSubImage2D(GL_TEXTURE_2D, 0, 0, 0, width, height, GL_RGB, GL_UNSIGNED_BYTE, tex_data);</p>

<p>V¹imnìte si následujícího øádku, je opravdu dùle¾itý. Sma¾eme jím kompletnì celou scénu. Z toho plyne, ¾e nema¾eme podscény jednotlivých viewportù postupnì, ale V©ECHNY NAJEDNOU pøed tím, ne¾ cokoli vykreslíme. Také si v¹imnìte, ¾e v tuto chvíli k volání nepøidáváme mazání depth bufferu. Ten naopak o¹etøíme u ka¾dého viewportu zvlá¹».</p>

<p class="src1">glClear(GL_COLOR_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku</span></p>

<p>Chceme vykreslit ètyøi rozdílné viewporty, tak¾e zalo¾íme cyklus od nuly do tøí, pomocí øídící promìnné nastavíme barvu.</p>

<p class="src1">for (int loop = 0; loop &lt; 4; loop++)<span class="kom">// Prochází viewporty</span></p>
<p class="src1">{</p>
<p class="src2">glColor3ub(r[loop], g[loop], b[loop]);<span class="kom">// Barva</span></p>

<p>Pøedtím ne¾ cokoli vykreslíme, potøebujeme nastavit viewporty. První bude umístìn vlevo nahoøe. Na ose x tedy zaèíná na nule a na ose y v polovinì okna. ©íøku i vý¹ku nastavíme na polovinu rozmìrù okna. Pokud se nacházíme ve fullscreenu s rozli¹ením obrazovky 1024x768, bude tento viewport zaèínat na souøadnicích [0; 384]. ©íøka se bude rovna 512 a vý¹ka 384.</p>

<p class="src2">if (loop == 0)<span class="kom">// První scéna</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Levý horní viewport, velikost poloviny okna</span></p>
<p class="src3">glViewport(0, window_height / 2, window_width / 2, window_height / 2);</p>

<p>Po definování viewportu zvolíme projekèní matici, resetujeme ji a nastavíme kolmou 2D projekci, která kompletnì zaplòuje celý viewport. Levý roh spoèívá na nule a pravý na polovinì velikosti okna (¹íøka viewportu). Spodní bod je také polovinou okna a hornímu pøedáme nulu. Souøadnice [0; 0] tedy odpovídá levému hornímu rohu.</p>

<p class="src3">glMatrixMode(GL_PROJECTION);<span class="kom">// Projekèní matice</span></p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset projekèní matice</span></p>
<p class="src"></p>
<p class="src3">gluOrtho2D(0, window_width / 2, window_height / 2, 0);<span class="kom">// Pravoúhlá projekce</span></p>
<p class="src2">}</p>

<p>Druhý viewport le¾í v pravém horním rohu. Opìt zvolíme projekèní matici a resetujeme ji. Tentokrát nenastavujeme pravoúhlou, ale perspektivní scénu.</p>

<p class="src2">if (loop == 1)<span class="kom">// Druhá scéna</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Pravý horní viewport, velikost poloviny okna</span></p>
<p class="src3">glViewport(window_width / 2, window_height / 2, window_width / 2, window_height / 2);</p>
<p class="src3">glMatrixMode(GL_PROJECTION);<span class="kom">// Projekèní matice</span></p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset projekèní matice</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Perspektivní projekce</span></p>
<p class="src3">gluPerspective(45.0, (GLfloat)(width) / (GLfloat)(height), 0.1f, 500.0); </p>
<p class="src2">}</p>

<p>Tøetí viewport umístíme vpravo a ètvrtý vlevo dolù.</p>

<p class="src2">if (loop == 2)<span class="kom">// Tøetí scéna</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Pravý dolní viewport, velikost poloviny okna</span></p>
<p class="src3">glViewport(window_width / 2, 0, window_width / 2, window_height / 2);</p>
<p class="src3">glMatrixMode(GL_PROJECTION);<span class="kom">// Projekèní matice</span></p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset projekèní matice</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Perspektivní projekce</span></p>
<p class="src3">gluPerspective(45.0, (GLfloat)(width) / (GLfloat)(height), 0.1f, 500.0); </p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">if (loop == 3)<span class="kom">// Ètvrtá scéna</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Levý dolní viewport, velikost poloviny okna</span></p>
<p class="src3">glViewport(0, 0, window_width / 2, window_height / 2);</p>
<p class="src3">glMatrixMode(GL_PROJECTION);<span class="kom">// Projekèní matice</span></p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset projekèní matice</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Perspektivní projekce</span></p>
<p class="src3">gluPerspective(45.0, (GLfloat)(width) / (GLfloat)(height), 0.1f, 500.0); </p>
<p class="src2">}</p>

<p>Zvolíme matici modelview, resetujeme ji a sma¾eme hloubkový buffer.</p>

<p class="src2">glMatrixMode(GL_MODELVIEW);<span class="kom">// Matice modelview</span></p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glClear(GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e hloubkový buffer</span></p>

<p>Scéna prvního viewportu bude obsahovat plochý otexturovaný obdélník. Proto¾e se nacházíme v pravoúhlé projekci, nepotøebujeme zadávat souøadnice na ose z. Objekty by se stejnì nezmen¹ily. Vertexùm pøedáme rozmìry viewportu, který tudí¾ bude kompletnì vyplnìn.</p>

<p class="src2">if (loop == 0)<span class="kom">// První scéna, bludi¹tì pøes celý viewport</span></p>
<p class="src2">{</p>
<p class="src3">glBegin(GL_QUADS);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex2i(window_width / 2, 0);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex2i(0, 0);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex2i(0, window_height / 2);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex2i(window_width / 2, window_height / 2);</p>
<p class="src3">glEnd();</p>
<p class="src2">}</p>

<p>Jako druhý objekt nakreslíme kouli. Máme zapnutou perspektivu, tak¾e se nejdøíve pøesuneme o 14 jednotek do obrazovky. Potom objekt natoèíme o daný úhel na v¹ech tøech souøadnicových osách, zapneme svìtla, vykreslíme kouli o polomìru 4.0f jednotky a vypneme svìtla.</p>

<p class="src2">if (loop == 1)<span class="kom">// Druhá scéna, koule</span></p>
<p class="src2">{</p>
<p class="src3">glTranslatef(0.0f, 0.0f, -14.0f);<span class="kom">// Pøesun do hloubky</span></p>
<p class="src"></p>
<p class="src3">glRotatef(xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(yrot, 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glRotatef(zrot, 0.0f, 0.0f, 1.0f);</p>
<p class="src"></p>
<p class="src3">glEnable(GL_LIGHTING);<span class="kom">// Zapne svìtlo</span></p>
<p class="src3">gluSphere(quadric, 4.0f, 32, 32);<span class="kom">// Koule</span></p>
<p class="src3">glDisable(GL_LIGHTING);<span class="kom">// Vypne svìtlo</span></p>
<p class="src2">}</p>

<p>Tøetí viewport se velmi podobá prvnímu, ale na rozdíl od nìj pou¾ívá perspektivu. Pøesuneme obdélník o dvì jednotky do hloubky a natoèíme matici o 45 stupòù. Horní hrana se tím pádem vzdálí a spodní pøiblí¾í. Abychom je¹tì pøidali nìjaký ten efekt, rotujeme jím také na ose z.</p>

<p class="src2">if (loop == 2)<span class="kom">// Tøetí scéna, bludi¹tì na rovinì</span></p>
<p class="src2">{</p>
<p class="src3">glTranslatef(0.0f, 0.0f, -2.0f);<span class="kom">// Pøesun do hloubky</span></p>
<p class="src"></p>
<p class="src3">glRotatef(-45.0f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace o 45 stupòù</span></p>
<p class="src3">glRotatef(zrot / 1.5f, 0.0f, 0.0f, 1.0f);<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, 0.0f);</p>
<p class="src3">glEnd();</p>
<p class="src2">}</p>

<p>Ètvrtým a posledním objektem je válec, který se nachází sedm jednotek hluboko ve scénì a rotuje na v¹ech tøech osách. Zapneme svìtla a potom se je¹tì posuneme o dvì jednotky (o polovinu jeho délky) na ose z. Chceme, aby se otáèel okolo svého støedu a ne konce. Vykreslíme ho a vypneme svìtla.</p>

<p class="src2">if (loop == 3)<span class="kom">// Tøetí scéna, válec</span></p>
<p class="src2">{</p>
<p class="src3">glTranslatef(0.0f,0.0f,-7.0f);<span class="kom">// Pøesun do hloubky</span></p>
<p class="src"></p>
<p class="src3">glRotatef(-xrot/2,1.0f,0.0f,0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(-yrot/2,0.0f,1.0f,0.0f);</p>
<p class="src3">glRotatef(-zrot/2,0.0f,0.0f,1.0f);</p>
<p class="src"></p>
<p class="src3">glEnable(GL_LIGHTING);<span class="kom">// Zapne svìtlo</span></p>
<p class="src3">glTranslatef(0.0f,0.0f,-2.0f);<span class="kom">// Vycentrování</span></p>
<p class="src3">gluCylinder(quadric,1.5f,1.5f,4.0f,32,16);<span class="kom">// Válec</span></p>
<p class="src3">glDisable(GL_LIGHTING);<span class="kom">// Vypne svìtlo</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Na konci vykreslování flushneme renderovací pipeline.</p>

<p class="src1">glFlush();<span class="kom">// Vyprázdnìní pipeline</span></p>
<p class="src0">}</p>

<p>Doufám, ¾e tento tutoriál zodpovìdìl v¹echny va¹e otázky ohlednì více viewportù v jednom oknì. Nyní také znáte jeden z mnoha zpùsobù generování bludi¹tì a umíte upravit texturu bez jejího komplikovaného mazání a znovuvytváøení. Co víc si pøát?</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson42.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson42_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson42.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson42.zip">Delphi</a> kód této lekce. ( <a href="mailto:Eshat@gmx.net">Eshat Cakar</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson42.zip">Dev C++</a> kód této lekce. ( <a href="mailto:robohog_64@hotmail.com">Victor Andr?e</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson42.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:evik@chaos.hu">Evik</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson42.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson42.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/extras/lesson42/lesson42_dual_window.zip">Lesson 42 - Multi Window</a> Code For This Lesson by Marcel Laverdet</li>
</ul>

<?FceImgNeHeVelky(42);?>
<?FceNeHeOkolniLekce(42);?>

<?
include 'p_end.php';
?>
