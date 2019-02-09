<?
$g_title = 'CZ NeHe OpenGL - Lekce 34 - Generování terénù a krajin za pou¾ití vý¹kového mapování textur';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(34);?>

<h1>Lekce 34 - Generování terénù a krajin za pou¾ití vý¹kového mapování textur</h1>

<p class="nadpis_clanku">Chtìli byste vytvoøit vìrnou simulaci krajiny, ale nevíte, jak na to? Bude nám staèit obyèejný 2D obrázek ve stupních ¹edi, pomocí kterého deformujeme rovinu do tøetího rozmìru. Na první pohled tì¾ko øe¹itelné problémy bývají èastokrát velice jednoduché.</p>

<p>Nyní byste u¾ mìli být opravdovými experty na OpenGL, ale mo¾ná nevíte, co to je vý¹kové mapování (height mapping). Pøedstavte si rovinu, vytlaèenou podle nìjaké formy do 3D prostoru. Této formì se øíká vý¹ková mapa, kterou mù¾e být defakto jakýkoli typ dat. Obrázky, textové soubory nebo tøeba datový proud zvuku - zále¾í jen na vás. My budeme pou¾ívat .RAW obrázek ve stupních ¹edi.</p>

<p>Definujeme tøi opravdu dùle¾ité symbolické konstanty. MAP_SIZE pøedstavuje rozmìr mapy, v na¹em pøípadì se jedná o ¹íøku/vý¹ku obrázku (1024x1024). Konstanta STEP_SIZE urèuje velikost krokù pøi grabování hodnot z obrázku. V souèasné chvíli bereme v úvahu ka¾dý ¹estnáctý pixel. Zmen¹ením èísla pøidáváme do výsledného povrchu polygony, tak¾e vypadá ménì hranatì, ale zároveò zvy¹ujeme nároènost na rendering. HEIGHT_RATIO slou¾í jako mìøítko vý¹ky na ose y. Malé èíslo zredukuje vysoké hory s údolími na plochou rovinu.</p>

<p class="src0">#define MAP_SIZE 1024<span class="kom">// Velikost .RAW obrázku vý¹kové mapy</span></p>
<p class="src0">#define STEP_SIZE 16<span class="kom">// Hustota grabování pixelù</span></p>
<p class="src0">#define HEIGHT_RATIO 1.5f<span class="kom">// Zoom vý¹ky terénu na ose y</span></p>

<p>Promìnná bRender pøedstavuje pøepínaè mezi pevnými polygony a drátìným modelem, scaleValue urèuje zoom scény na v¹ech tøech osách.</p>

<p class="src0">bool bRender = TRUE;<span class="kom">// Polygony - true, drátìný model - false</span></p>
<p class="src0">float scaleValue = 0.15f;<span class="kom">// Mìøítko velikosti terénu (v¹echny osy)</span></p>

<p>Deklarujeme jednorozmìrné pole pro ulo¾ení v¹ech dat vý¹kové mapy. Pou¾ívaný .RAW obrázek neobsahuje RGB slo¾ky barvy, ale ka¾dý pixel je tvoøen jedním bytem, který specifikuje jeho odstín. Nicménì o barvu se starat nebudeme, jde nám pøedev¹ím o hodnoty. Èíslo 255 bude pøedstavovat nejvy¹¹í mo¾ný bod povrchu a nula nejni¾¹í.</p>

<p class="src0">BYTE g_HeightMap[MAP_SIZE * MAP_SIZE];<span class="kom">// Ukládá data vý¹kové mapy</span></p>

<p>Funkce LoadRawFile() nahrává RAW soubor s obrázkem. Nic komplexního! V parametrech se jí pøedává øetìzec diskové cesty, velikost dat obrázku a ukazatel na pamì», do které se ukládá. Otevøeme soubor pro ètení v binárním módu a o¹etøíme situaci, kdy neexistuje.</p>

<p class="src0">void LoadRawFile(LPSTR strName, int nSize, BYTE* pHeightMap)<span class="kom">// Nahraje .RAW soubor</span></p>
<p class="src0">{</p>
<p class="src1">FILE *pFile = NULL;<span class="kom">// Handle souboru</span></p>
<p class="src"></p>
<p class="src1">pFile = fopen(strName, &quot;rb&quot;);<span class="kom">// Otevøení souboru pro ètení v binárním módu</span></p>
<p class="src"></p>
<p class="src1">if (pFile == NULL)<span class="kom">// Otevøení v poøádku?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Can't Find The Height Map!&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return;</p>
<p class="src1">}</p>

<p>Pomocí fread() naèteme po jednom bytu ze souboru pFile data o velikosti nSize a ulo¾íme je do pamìti na lokaci pHeightMap. Vyskytne-li se chyba, vypí¹eme varovnou zprávu.</p>

<p class="src1">fread(pHeightMap, 1, nSize, pFile);<span class="kom">// Naète soubor do pamìti</span></p>
<p class="src"></p>
<p class="src1">int result = ferror(pFile);<span class="kom">// Výsledek naèítání dat</span></p>
<p class="src"></p>
<p class="src1">if (result)<span class="kom">// Nastala chyba?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Failed To Get Data!&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src1">}</p>

<p>Na konci zbývá u¾ jenom zavøít soubor.</p>

<p class="src1">fclose(pFile);<span class="kom">// Zavøení souboru</span></p>
<p class="src0">}</p>

<p>Kód pro inicializaci OpenGL byste mìli bez problémù pochopit sami.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Inicializace OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testování hloubky</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivní korekce</span></p>

<p>Pøed vrácením true je¹tì do g_HeightMap nahrajeme .RAW obrázek.</p>

<p class="src1">LoadRawFile(&quot;Data/Terrain.raw&quot;, MAP_SIZE * MAP_SIZE, g_HeightMap);<span class="kom">// Naètení dat vý¹kové mapy</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹e v poøádku</span></p>
<p class="src0">}</p>

<p>Máme zde jeden problém - ulo¾ili jsme dvourozmìrný obrázek do jednorozmìrného pole. Co s tím? Funkce Height() provede výpoèet pro transformaci x, y souøadnic na index do tohoto pole a vrátí hodnotu, která je na nìm ulo¾ená. Pøi práci s poli bychom se v¾dy mìli start o mo¾nost pøeteèení pamìti. Jednoduchým trikem zmen¹íme vysoké hodnoty tak, aby byly v¾dy platné. Pokud nìkterá z hodnot pøesáhne daný index, zbytek po dìlení ji zmen¹í do rozmezí, které mù¾eme bez obav pou¾ít. Dále otestujeme, jestli se v poli opravdu nacházejí data.</p>

<p class="src0">int Height(BYTE *pHeightMap, int X, int Y)<span class="kom">// Pøepoèítá 2D souøadnice na 1D a vrátí ulo¾enou hodnotu</span></p>
<p class="src0">{</p>
<p class="src1">int x = X % MAP_SIZE;<span class="kom">// Proti pøeteèení pamìti</span></p>
<p class="src1">int y = Y % MAP_SIZE;</p>
<p class="src"></p>
<p class="src1">if(!pHeightMap)<span class="kom">// Obsahuje pamì» data?</span></p>
<p class="src1">{</p>
<p class="src2">return 0;</p>
<p class="src1">}</p>

<p>Aby se jednorozmìrné pole chovalo jako dvojrozmìrné, musíme zapojit trochu matematiky. Index do 1D pole na 2D souøadnicích získáme tak, ¾e vynásobíme øádek (y) jeho ¹íøkou (MAP_SIZE) a pøièteme konkrétní pozici na øádku (x).</p>

<p class="src1">return pHeightMap[(y * MAP_SIZE) + x];<span class="kom">// Vrátí hodnotu z pole</span></p>
<p class="src0">}</p>

<p>Na tomto místì nastavujeme barvu vertexu podle aktuální vý¹ky nad height mapou. Získáme hodnotu na indexu pole a dìlením 256.0f ji zmen¹íme do rozmezí 0.0f a¾ 1.0f. Abychom ji je¹tì trochu ztmavili, odeèteme -0.15f. Výsledek pøedáme funkci glColor3f() jako modrou slo¾ku barvy.</p>

<p class="src0">void SetVertexColor(BYTE *pHeightMap, int x, int y)<span class="kom">// Získá barvu v závislosti na vý¹ce</span></p>
<p class="src0">{</p>
<p class="src1">if(!pHeightMap)<span class="kom">// Obsahuje pamì» data?</span></p>
<p class="src1">{</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Získání hodnoty, pøepoèet do rozmezí 0.0f a¾ 1.0f, ztmavení</span></p>
<p class="src1">float fColor = (Height(pHeightMap, x, y) / 256.0f) - 0.15f;</p>
<p class="src"></p>
<p class="src1">glColor3f(0, 0, fColor);<span class="kom">// Odstíny modré barvy</span></p>
<p class="src0">}</p>

<p>Dostáváme se k nejpodstatnìj¹í èásti celého tutoriálu - renderování terénu. Promìnné X, Y slou¾í k procházejí vý¹kové mapy a x, y, z jsou 3D souøadnicemi vertexu.</p>

<p class="src0">void RenderHeightMap(BYTE pHeightMap[])<span class="kom">// Renderuje terén</span></p>
<p class="src0">{</p>
<p class="src1">int X = 0, Y = 0;<span class="kom">// Pro procházení polem</span></p>
<p class="src1">int x, y, z;<span class="kom">// Souøadnice vertexù</span></p>
<p class="src"></p>
<p class="src1">if(!pHeightMap)<span class="kom">// Obsahuje pamì» data?</span></p>
<p class="src1">{</p>
<p class="src2">return;</p>
<p class="src1">}</p>

<p>Podle logické hodnoty bRender pøipínáme mezi vykreslováním obdélníkù a linek.</p>

<p class="src1">if(bRender)<span class="kom">// Co chce u¾ivatel renderovat?</span></p>
<p class="src1">{</p>
<p class="src2">glBegin(GL_QUADS);<span class="kom">// Polygony</span></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">glBegin(GL_LINES);<span class="kom">// Drátìný model</span></p>
<p class="src1">}</p>

<p>Zalo¾íme dva vnoøené cykly, které procházejí jednotlivé pixely vý¹kové mapy. Vnìj¹í se stará o osu x a vnitøní o osu y, z èeho¾ plyne, ¾e vykreslujeme po sloupcích a ne po øádcích. V¹imnìte si, ¾e po ka¾dém prùchodu nezvìt¹ujeme øídící promìnnou o jeden pixel, ale hned o nìkolik najednou. Sice výsledný terén nebude tak hladký a pøesný, ale díky men¹ímu poètu polygonù se rendering urychlí. Pokud by se STEP_SIZE rovnalo jedné, ka¾dému pixelu by se pøiøadil jeden polygon. Myslím, ¾e èíslo ¹estnáct bude vyhovující, ale pokud zapnete svìtla, které zvýrazòují hranatost povrchu, mìli byste ho sní¾it.</p>

<p>Pøekl.: Úplnì nejlep¹í by bylo, kdyby se velikost kroku urèovala pøed vstupem do cyklù podle aktuálního FPS. Následující ukázkový kód zavádí zpìtnovazební regulaèní smyèku.</p>

<p class="src1"><span class="kom">// Pøekl.: Regulace poètu polygonù</span></p>
<p class="src1"><span class="kom">// if(FPS &lt; 30)// Ni¾¹í hodnoty =&gt; viditelné trhání pohybù animace</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// if(STEP_SIZE &gt; 1)// Dolní mez (1 pixel)</span></p>
<p class="src2"><span class="kom">// {</span></p>
<p class="src3"><span class="kom">// STEP_SIZE--;// Musí být promìnnou a ne symbolickou konstantou</span></p>
<p class="src2"><span class="kom">// }</span></p>
<p class="src1"><span class="kom">// }</span></p>
<p class="src1"><span class="kom">// else</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// if(STEP_SIZE &lt; MAP_SIZE-1)// Horní mez (velikost vý¹kové mapy)</span></p>
<p class="src2"><span class="kom">// {</span></p>
<p class="src3"><span class="kom">// STEP_SIZE++;// Musí být promìnnou a ne symbolickou konstantou</span></p>
<p class="src2"><span class="kom">// }</span></p>
<p class="src1"><span class="kom">// }</span></p>
<p class="src"></p>
<p class="src1">for (X = 0; X &lt; MAP_SIZE; X += STEP_SIZE)<span class="kom">// Øádky vý¹kové mapy</span></p>
<p class="src1">{</p>
<p class="src2">for (Y = 0; Y &lt; MAP_SIZE; Y += STEP_SIZE)<span class="kom">// Sloupce vý¹kové mapy</span></p>
<p class="src2">{</p>

<p>Pøepokládám, ¾e to, jak urèit pozici vertexu, jste u¾ dávno vytu¹ili. Hodnota na ose x odpovídá x-ové souøadnici vý¹kové mapy a na ose z y-ové. Získali jsme umístìní bodu na rovinì, potøebujeme ho je¹tì vyzdvihnout do vý¹ky, které v OpenGL odpovídá osa y. Tato vý¹ka je definována hodnotou ulo¾enou na daném prvku pole (svìtlostí obrázku). Opravdu nic slo¾itého...</p>

<p class="src3"><span class="kom">// Souøadnice levého dolního vertexu</span></p>
<p class="src3">x = X;</p>
<p class="src3">y = Height(pHeightMap, X, Y );</p>
<p class="src3">z = Y;</p>

<p>Urèíme barvu bodu podle vý¹ky nad rovinou. Èím vý¹e se nachází, tím bude svìtlej¹í. Potom pomocí funkce glVertex3i() pøedáme OpenGL souøadnice vertexu.</p>

<p class="src3">SetVertexColor(pHeightMap, x, z);<span class="kom">// Barva vertexu</span></p>
<p class="src3">glVertex3i(x, y, z);<span class="kom">// Definování vertexu</span></p>

<p>Druhý vertex urèíme pøiètením STEP_SIZE k ose z. Na tomto místì se budeme nacházet pøi pøí¹tím prùchodu cyklem, tak¾e se mezi jednotlivými polygony nebudou vyskytovat mezery. Analogicky získáme i dal¹í dva body obdélníku. Nyní mi u¾ vìøíte, kdy¾ jsem na zaèátku tutoriálu psal, ¾e slo¾itì vypadající vìci bývají èasto velice jednoduché?</p>

<p class="src3"><span class="kom">// Souøadnice levého horního vertexu</span></p>
<p class="src3">x = X;</p>
<p class="src3">y = Height(pHeightMap, X, Y + STEP_SIZE );  </p>
<p class="src3">z = Y + STEP_SIZE ;</p>
<p class="src"></p>
<p class="src3">SetVertexColor(pHeightMap, x, z);<span class="kom">// Barva vertexu</span></p>
<p class="src3">glVertex3i(x, y, z);<span class="kom">// Definování vertexu</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Souøadnice pravého horního vertexu</span></p>
<p class="src3">x = X + STEP_SIZE; </p>
<p class="src3">y = Height(pHeightMap, X + STEP_SIZE, Y + STEP_SIZE ); </p>
<p class="src3">z = Y + STEP_SIZE ;</p>
<p class="src"></p>
<p class="src3">SetVertexColor(pHeightMap, x, z);<span class="kom">// Barva vertexu</span></p>
<p class="src3">glVertex3i(x, y, z);<span class="kom">// Definování vertexu</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Souøadnice pravého dolního vertexu</span></p>
<p class="src3">x = X + STEP_SIZE; </p>
<p class="src3">y = Height(pHeightMap, X + STEP_SIZE, Y ); </p>
<p class="src3">z = Y;</p>
<p class="src"></p>
<p class="src3">SetVertexColor(pHeightMap, x, z);<span class="kom">// Barva vertexu</span></p>
<p class="src3">glVertex3i(x, y, z);<span class="kom">// Definování vertexu</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení</span></p>

<p>Po vykreslení terénu reinicializujeme barvu na bílou, abychom nemìli starosti s barvou ostatních objektù ve scénì (netýká se tohoto dema).</p>

<p class="src1">glColor4f(1.0f, 1.0f, 1.0f, 1.0f);<span class="kom">// Reset barvy</span></p>
<p class="src0">}</p>

<p>Na zaèátku DrawGLScene() zaèneme klasicky smazáním bufferù a resetem matice.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslení OpenGL scény</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma¾e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Pomocí funkce gluLookAt() umístíme a natoèíme kameru tak, aby byl renderovaný terén v zábìru. První tøi parametry urèují její pozici vzhledem k poèátku souøadnicového systému, dal¹í tøi body reprezentují místo, kam je natoèená a poslední tøi pøedstavují vektor vzhùru. V na¹em pøípadì se nacházíme nad sledovaným terénem a díváme se na nìj trochu dolù (55 je men¹í ne¾ 60) spí¹e doleva (186 je men¹í ne¾ 212). Hodnota 171 pøedstavuje vzdálenost od kamery na ose z. Proto¾e se hory zvedají od zdola nahoru, nastavíme u vektoru vzhùru jednièku na ose y. Ostatní dvì hodnoty zùstanou na nule.</p>

<p>Pøi prvním pou¾ití mù¾e být gluLookAt() trochu odstra¹ující, asi jste zmateni. Nejlep¹í radou je pohrát si se v¹emi hodnotami, abyste vidìli, jak se pohled na scénu postupnì mìní. Pokud byste napøíklad pøepsal pozici z 60 na 120, vidìli byste terén spí¹e seshora ne¾ z boku, proto¾e se stále díváte na souøadnice 55.</p>

<p>Praktický pøíklad: Øeknìme, ¾e jste vysoký kolem 1,8 m. Oèi, které reprezentují kameru, jsou trochu ní¾e - 1,7 m. Stojíte pøed stìnou, která je vysoká pouze 1 m, tak¾e bez problémù vidíte její horní stranu. Pokud ale zedníci dostaví stìnu do vý¹ky tøí metrù, budete se muset dívat VZHÙRU, ale její vrch u¾ NEUVIDÍTE. Výhled se zmìnil podle toho, jestli se díváte dolù nebo vzhùru (respektive jestli jste nad nebo pod objektem).</p>

<p class="src1"><span class="kom">// Umístìní a natoèení kamery</span></p>
<p class="src1">gluLookAt(212,60,194, 186,55,171, 0,1,0);<span class="kom">// Pozice, smìr, vektor vzhùru</span></p>

<p>Aby byl výsledný terén ponìkud men¹í, zmìníme mìøítko souøadnicových os. Proto¾e navíc násobíme y-ovou hodnotu, budou se hory jevit vy¹¹í. Mohli bychom také pou¾ít translace a rotace, ale to u¾ nechám na vás.</p>

<p class="src1">glScalef(scaleValue, scaleValue * HEIGHT_RATIO, scaleValue);<span class="kom">// Zoom terénu</span></p>

<p>Pomocí døíve napsané funkce vyrenderujeme terén.</p>

<p class="src1">RenderHeightMap(g_HeightMap);<span class="kom">// Renderování terénu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹e v poøádku</span></p>
<p class="src0">}</p>

<p>Kliknutím levého tlaèítka my¹i mù¾e u¾ivatel pøepnout mezi renderováním polygonù a linek (drátìný model).</p>

<p class="src0"><span class="kom">// Funkce WndProc()</span></p>
<p class="src2">case WM_LBUTTONDOWN:<span class="kom">// Levé tlaèítko my¹i</span></p>
<p class="src2">{</p>
<p class="src3">bRender = !bRender;<span class="kom">// Pøepne mezi polygony a drátìným modelem</span></p>
<p class="src3">return 0;<span class="kom">// Konec funkce</span></p>
<p class="src2">}</p>

<p>©ipkami nahoru a dolù zvìt¹ujeme/zmen¹ujeme mìøítko scény a tím i velikost terénu.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src3">if (keys[VK_UP])<span class="kom">// ©ipka nahoru</span></p>
<p class="src3">{</p>
<p class="src4">scaleValue += 0.001f;<span class="kom">// Vyvý¹í hory</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_DOWN])<span class="kom">// ©ipka dolù</span></p>
<p class="src3">{</p>
<p class="src4">scaleValue -= 0.001f;<span class="kom">// Sní¾í hory</span></p>
<p class="src3">}</p>

<p>Tak to je v¹echno, vý¹kovým mapováním textur jsme naprogramovali nádherou krajinu, která je ale zabarvená do modra. Zkuste si nakreslit texturu (letecký pohled), která reprezentuje zasnì¾ené vrcholy hor, louky, jezera a podobnì a namapujte ji na terén. Texturovací koordináty získáte vydìlením pozice na rovinì rozmìrem obrázku (zmen¹ení hodnot do rozsahu 0.0f a¾ 1.0f). Plazmovými efekty a rolováním se mù¾e krajina dynamicky mìnit. Dé¹» a sníh zajistí èásticové systémy, které u¾ také znáte. Vlo¾íte-li krajinu do skyboxu, nikdo nepozná, ¾e se jedná o poèítaèový model a ne o video animaci.</p>

<p>Nebo mù¾ete vytvoøit moøskou hladinu s vlnami, na kterých se pohupuje uplavaný míè (vý¹ku nad moøským dnem pøece znáte - hodnota na indexu v poli). Nechte u¾ivatele, a» ho mù¾e ovládat. Mo¾nosti jsou bez hranic...</p>

<p class="autor">napsal: Ben Humphrey - DigiBen<br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson34.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson34_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson34.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson34.zip">Delphi</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde (aka Marilyn)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson34.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson34.jar">JoGL</a> kód této lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson34.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson34.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Schubert_P@Yahoo.de">Patrick Schubert</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson34.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson34.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(34);?>
<?FceNeHeOkolniLekce(34);?>

<?
include 'p_end.php';
?>
