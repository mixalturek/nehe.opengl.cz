<?
$g_title = 'CZ NeHe OpenGL - Lekce 39 - Úvod do fyzikálních simulací';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(39);?>

<h1>Lekce 39 - Úvod do fyzikálních simulací</h1>

<p class="nadpis_clanku">V gravitaèním poli se pokusíme rozpohybovat hmotný bod s konstantní rychlostí, hmotný bod pøipojený k pru¾inì a hmotný bod, na který pùsobí gravitaèní síla - v¹e podle fyzikálních zákonù. Kód je zalo¾en na nejnovìj¹ím NeHeGL kódu.</p>

<p>Pokud zvládáte fyziku a chcete pou¾ívat kód pro fyzikální simulaci, tak Vám tento tutoriál mù¾e pomoci. Abyste ale mohli nìco vytì¾it, mìli byste vìdìt nìco o poèítání s vektory v trojrozmìrném prostoru a fyzikálních velièinách, jako je síla nebo rychlost. Tutoriál obsahuje popis velmi jednoduchého fyzikálního simulátoru.</p>

<h2>Tøída Vector3D</h2>

<p>Návrh fyzikálního simulaèního enginu není v¾dy jednoduchý. Ale je zde jednoduchá posloupnost závislostí - aplikace potøebuje simulaèní èást a ta potøebuje matematické knihovny. Tady tuto závislost uplatníme. Na¹ím cílem je získat zásobník na simulaci pohybu objektù v prostoru. Simulaèní èást bude obsahovat tøídy Mass a Simulation. Tøída Simulation bude na¹ím zásobníkem. Pokud vytvoøíme tøídu Simulation budeme schopni vyvíjet aplikace, které ji vyu¾ívají. Ale pøedtím potøebujeme matematickou knihovnu. Knihovna obsahuje pouze jednu tøídu Vector3D, která pro nás bude pøedstavovat body, vektory, pozice, rychlost a sílu ve 3D prostoru.</p>

<p>Vector3D tedy bude jediným èlenem na¹í matematické knihovny. Obsahuje souøadnice x, y, z v pøesnosti float a zavádí operátory pro poèítání s vektory ve 3D. Abychom byli konkrétní, pøetí¾íme operátory sèítání, odèítání, násobení a dìlení. Proto¾e se tento tutoriál zamìøuje na fyziku a ne matematiku, nebudu podrobnì vysvìtlovat Vector3D. Podíváte-li se na jeho zdrojový kód, myslím si, ¾e nebudete mít problémy porozumìt.</p>

<h2>Síla a pohyb</h2>

<p>Abychom mohli implementovat fyzikální simulaci, mìli bychom vìdìt, jak bude vypadat ná¹ objekt. Bude mít polohu a rychlost. Pokud je umístìn na Zemi, Mìsíci, Marsu nebo na jakémkoliv místì, kde je gravitace musí mít také hmotnost, která se li¹í podle velikosti pùsobící gravitaèní síly. Vezmìme si tøeba knihu. Na Zemi vá¾í 1 kg, ale na Mìsíci pouze 0,17 kg, proto¾e Mìsíc na ni pùsobí men¹í gravitaèní silou. My budeme uva¾ovat hmotnost na Zemi.</p>

<p>Poté, kdy¾ jsme pochopili, co pro nás znamená hmotnost, mìli bychom se pøesunout k síle a pohybu. Objekt s nenulovou rychlostí se pohybuje ve smìru rychlosti. Proto je jeden z dùvodù zmìny polohy v prostoru rychlost. Aè se to nezdá, je dal¹í pùsobící velièinou èas. Posunutí pøedmìtu tedy závisí na tom, jak rychle se pohybuje, a na tom kolik èasu uplynulo od poèátku pohybu. Pokud vám vztah mezi polohou, rychlostí a èasem není jasný, tak asi nemá cenu pokraèovat. Doporuèuji si vzít uèebnici fyziky a najít si kapitolu zabývající se Newtonovy zákony.</p>

<p>Rychlost objektu se mìní, pokud na objekt pùsobí nìjaká síla. Její vektor je kombinací smìru (poèáteèní a koncový bod) a velikosti. Velikost pùsobení je pøímo úmìrná pùsobící síle a nepøímo úmìrná hmotnosti objektu. Zmìna rychlosti za jednotku èasu se nazývá zrychlení. Èím vìt¹í síla pùsobí na objekt, tím více zrychluje. Èím má, ale vìt¹í hmotnost, tím je men¹í zrychlení.</p>

<p class="src0">zrychlení = síla / hmotnost</p>

<p>Odsud jednodu¹e vyjádøíme sílu:</p>

<p class="src0">síla = hmotnost * zrychlení</p>

<p>Pøi pøípravì prostøedí simulace si musíte dávat pozor na to, jaké podmínky v tomto prostøedí panují. Prostøedí v tomto tutoriálu bude prázdný prostor èekající na zaplnìní objekty, které vytvoøíme. Nejdøíve se rozhodneme, jaké jednotky pou¾ijeme pro hmotnost, èas a délku. Rozhodl jsem se pou¾ít kilogram pro hmotnost, sekundu pro èas a metr pro délku. Tak¾e jednotky rychlosti budou m/s a jednotky zrychlení budou m/s^2 (metr za sekundu na druhou).</p>

<p>Abychom toto v¹echno vyu¾ili v praxi, musíme napsat tøídu, která bude reprezentovat objekt a bude obsahovat jeho hmotnost, polohu, rychlost a sílu, která na nìho pùsobí.</p>

<p class="src0">class Mass</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">float m;<span class="kom">// Hmotnost</span></p>
<p class="src"></p>
<p class="src1">Vector3D pos;<span class="kom">// Pozice v prostoru</span></p>
<p class="src1">Vector3D vel;<span class="kom">// Rychlosti a smìr pohybu</span></p>
<p class="src1">Vector3D force;<span class="kom">// Síla pùsobící na objekt</span></p>

<p>V konstruktoru inicializujeme pouze hmotnost, která se jako jediná nebude mìnit. Pozice, rychlost i pùsobící síly se urèitì mìnit budou.</p>

<p class="src1">Mass(float m)<span class="kom">// Konstruktor</span></p>
<p class="src1">{</p>
<p class="src2">this-&gt;m = m;</p>
<p class="src1">}</p>

<p>Aplikujeme silové pùsobení. Objekt mù¾e souèasnì ovlivòovat nìkolik zdrojù. Vektor v parametru je souèet v¹ech sil pùsobících na objekt. Pøed jeho aplikací bychom mìli stávající sílu vynulovat. K tomu slou¾í druhá funkce.</p>

<p class="src1">void applyForce(Vector3D force)</p>
<p class="src1">{</p>
<p class="src2">this-&gt;force += force;<span class="kom">// Vnìj¹í síla je pøiètena</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">void init()</p>
<p class="src1">{</p>
<p class="src2">force.x = 0;</p>
<p class="src2">force.y = 0;</p>
<p class="src2">force.z = 0;</p>
<p class="src1">}</p>

<p>Zde je struèný seznam toho, co pøi simulaci musíme provést:</p>

<ol>
<li>Vynulovat sílu - metoda init()</li>
<li>Vypoèítat znovu pùsobící sílu</li>
<li>Pøizpùsobit pohyb posunu v èase</li>
</ol>

<p>Pro práci s èasem pou¾ijeme Eulerovu metodu, kterou vyu¾ívá vìt¹ina her. Existují mnohem sofistikovanìj¹í metody, ale tahle postaèí. Velmi jednodu¹e se vypoèítá rychlost a poloha pro dal¹í èasový úsek s ohledem na pùsobící sílu a uplynulý èas. Ke stávající rychlosti pøièteme její zmìnu, která je závislá na zrychlení (síla/m) a uplynulém èase (dt). V dal¹ím kroku pøizpùsobíme polohu - opìt v závislosti na èase.</p>

<p class="src1">void simulate(float dt)</p>
<p class="src1">{</p>
<p class="src2">vel += (force / m) * dt;<span class="kom">// Zmìna rychlosti je pøiètena k aktuální rychlosti</span></p>
<p class="src2">pos += vel * dt;<span class="kom">// Zmìna polohy je pøiètena k aktuální poloze</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0">};</p>

<h2>Jak by mìla simulace pracovat</h2>

<p>Pøi fyzikální simulaci se bìhem ka¾dého posunu opakuje toté¾. Síly jsou vynulovány, potom znovu spoèítány. V závislosti na nich se urèují rychlosti a polohy pøedmìtù. Tento postup se opakuje tolikrát, kolikrát chceme. Je zaji¹»ován tøídou Simulation. Jejím úkolem je vytváøet, ukládat a mazat objekty a starat se o bìh simulace.</p>

<p class="src0">class Simulation</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">int numOfMasses;<span class="kom">// Poèet objektù v zásobníku</span></p>
<p class="src1">Mass** masses;<span class="kom">// Objekty jsou uchovávány v jednorozmìrném poli ukazatelù na objekty</span></p>
<p class="src"></p>

<p class="src1">Simulation(int numOfMasses, float m)<span class="kom">// Konstruktor vytvoøí objekty s danou hmotností</span></p>
<p class="src1">{</p>
<p class="src2">this-&gt;numOfMasses = numOfMasses;<span class="kom">// Inicializace poètu</span></p>
<p class="src2">masses = new Mass*[numOfMasses];<span class="kom">// Alokace dynamické pamìti pro pole ukazatelù</span></p>
<p class="src"></p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Projdeme v¹echny ukazatele na objekty</span></p>
<p class="src3">masses[a] = new Mass(m);<span class="kom">// Vytvoøíme objekt a umístíme ho na místo v poli</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">~Simulation()<span class="kom">// Sma¾e vytvoøené objekty</span></p>
<p class="src1">{</p>
<p class="src2">release();</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">virtual void release()<span class="kom">// Uvolní dynamickou pamì»</span></p>
<p class="src1">{</p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Sma¾e v¹echny vytvoøené objekty</span></p>
<p class="src2">{</p>
<p class="src3">delete(masses[a]);<span class="kom">// Uvolní dynamickou pamì» objektù</span></p>
<p class="src3">masses[a] = NULL;<span class="kom">// Nastaví ukazatele na NULL</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">delete(masses);<span class="kom">// Uvolní dynamickou pamì» ukazatelù na objekty</span></p>
<p class="src2">masses = NULL;<span class="kom">// Nastaví ukazatel na NULL</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">Mass* getMass(int index)<span class="kom">// Získání objektu s urèitým indexem</span></p>
<p class="src1">{</p>
<p class="src2">if (index &lt; 0 || index &gt;= numOfMasses)<span class="kom">// Pokud index není v rozsahu pole</span></p>
<p class="src3">return NULL;<span class="kom">// Vrátí NULL</span></p>
<p class="src"></p>
<p class="src2">return masses[index];<span class="kom">// Vrátí objekt s daným indexem</span></p>
<p class="src1">}</p>

<p>Proces simulace se skládá ze tøí krokù:</p>

<ol>
<li>Init() nastaví síly na nulu</li>
<li>Solve() znovu aplikuje síly</li>
<li>Simulate(float dt) posune objekty v závislosti na èase</li>
</ol>

<p class="src1">virtual void init()<span class="kom">// Tato metoda zavolá init() metodu ka¾dého objektu</span></p>
<p class="src1">{</p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Prochází objekty</span></p>
<p class="src3">masses[a]-&gt;init();<span class="kom">// Zavolání init() daného objektu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">virtual void solve()</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Bez implementace, proto¾e nechceme v základním zásobníku ¾ádné síly</span></p>
<p class="src2"><span class="kom">// Ve vylep¹ených zásobnících, bude tato metoda nahrazena, aby na objekty pùsobila nìjaká síla</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">virtual void simulate(float dt)<span class="kom">// Výpoèet v závislosti na èase</span></p>
<p class="src1">{</p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Projdeme v¹echny objekty</span></p>
<p class="src3">masses[a]-&gt;simulate(dt);<span class="kom">// Výpoèet nové polohy a rychlosti objektu</span></p>
<p class="src1">}</p>

<p>V¹echny tyto metody jsou volány v následující funkci.</p>

<p class="src1">virtual void operate(float dt)<span class="kom">// Kompletní simulaèní metoda</span></p>
<p class="src1">{</p>
<p class="src2">init();<span class="kom">// Krok 1: vynulování sil</span></p>
<p class="src2">solve();<span class="kom">// Krok 2: aplikace sil</span></p>
<p class="src2">simulate(dt);<span class="kom">// Krok 3: vypoèítání polohy a rychlosti objektù v závislosti na èase</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0">};</p>

<p>Nyní máme jednoduchý simulaèní engine. Je zalo¾ený na matematické knihovnì. Obsahuje tøídy Mass a Simulation. Pou¾ívá bì¾nou Eulerovu metodu na výpoèet simulace. Teï jsme pøipraveni na vývoj aplikací. Aplikace, kterou budeme vyvíjet vyu¾ívá:</p>

<ol>
<li>Objekty s konstantní hmotností</li>
<li>Objekty v gravitaèním poli</li>
<li>Objekty spojené pru¾inou s nìjakým bodem</li>
</ol>

<h2>Ovládání simulace aplikací</h2>

<p>Pøedtím ne¾ napí¹eme nìjakou simulaci, mìli bychom vìdìt, jak se tøídami zacházet. V tomto tutoriálu jsou simulaèní a aplikaèní èásti oddìleny do dvou samostatných souborù. V souboru s aplikaèní èástí je funkce Update(), která se volá opakovanì pøi ka¾dém novém framu.</p>

<p class="src0">void Update (DWORD milliseconds)<span class="kom">// Aktualizace pohybu</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// O¹etøení vstupu z klávesnice</span></p>
<p class="src1">if (g_keys->keyDown [VK_ESCAPE] == TRUE)</p>
<p class="src2">TerminateApplication (g_window);</p>
<p class="src"></p>
<p class="src1">if (g_keys->keyDown [VK_F1] == TRUE)</p>
<p class="src2">ToggleFullscreen (g_window);</p>
<p class="src"></p>
<p class="src1">if (g_keys->keyDown [VK_F2] == TRUE)</p>
<p class="src2">slowMotionRatio = 1.0f;</p>
<p class="src"></p>
<p class="src1">if (g_keys->keyDown [VK_F3] == TRUE)</p>
<p class="src2">slowMotionRatio = 10.0f;</p>

<p>DWORD milliseconds je èas, který uplynul od pøedchozího volání funkce. Budeme poèítat èas pøi simulacích na milisekundy. Pokud bude simulace sledovat tento èas, pùjde stejnì rychle jako v reálném èase. K provedení simulace jednodu¹e zavoláme funkci operate(float dt). Pøedtím ne¾ ji zavoláme musíme znát hodnotu dt. Proto¾e ve tøídì Simulation nepou¾íváme milisekundy, ale sekundy, pøevedeme promìnnou milliseconds na sekundy. Potom pou¾ijeme promìnnou slowMotionRatio, která udává, jak má být simulace zpomalená vzhledem k reálnému èasu. Touto promìnnou dìlíme dt a dostaneme nové dt. Pøidáme dt k promìnné timeElapsed, která udává kolik èasu simulace u¾ ubìhlo (neudává tedy reálný èas).</p>

<p class="src1">float dt = milliseconds / 1000.0f;<span class="kom">// Pøepoèítá milisekundy na sekundy</span></p>
<p class="src"></p>
<p class="src1">dt /= slowMotionRatio;<span class="kom">// Dìlení dt zpomalovací promìnnou</span></p>
<p class="src"></p>
<p class="src1">timeElapsed += dt;<span class="kom">// Zvìt¹ení uplynulého èasu</span></p>

<p>Teï u¾ je dt skoro pøipraveno na pou¾ití v simulaci. Ale! je tu jedna dùle¾itá vìc, kterou bychom mìli vìdìt: èím men¹í je dt, tím reálnìj¹í je simulace. Pokud nebude dt dostateènì malé, na¹e simulace se nebude chovat realisticky, proto¾e pohyb nebude spoèítán dostateènì preciznì. Analýza stability se u¾ívá pøi fyzikálních simulacích, aby zajistila maximální pøijatelnou hodnotu dt. V tomto tutoriálu se nebudeme pou¹tìt do detailù. Pokud vyvíjíte hru a ne specializovanou aplikaci, tato metoda bohatì staèí na to, abyste se vyhnuli chybám.</p>

<p>Napøíklad v automobilovém simulátoru je vhodné, aby se dt pohybovalo mezi 2 a¾ 5 milisekundami pro bì¾né auto a mezi 1 a 3 milisekundami pro formuli. Pøi arkádovém simulátoru je mo¾né pou¾ít dt v rozsahu od 10 do 200 milisekund. Èím ni¾¹í je dt, tím silnìj¹í procesor potøebujeme, abychom stíhali simulovat v reálném èase. To je dùvod proè se u star¹ích her nepou¾ívají fyzikální simulace.</p>

<p>V následujícím kódu nastavíme maximální hodnotu dt na 0.1 sekundy (100 milisekund). S touto hodnotou spoèítáme kolikrát cyklus simulace pøi ka¾dém projití funkce zopakujeme. To øe¹í následující vzorec:</p>

<p>int numOfIterations = (int)(dt / maxPossible_dt) + 1;</p>

<p>NumOfIterations je poèet cyklù, které pøi simulaci provedeme. Dejme tomu, ¾e aplikace bì¾í 20 framù za sekundu. Z toho plyne, ¾e dt=0.05. numOfIterations tedy bude 1. Simulace se provede jednou po 0.05 sekundách. Pokud by dt bylo 0.12 sekund, pak numOfIterations bude 2. Pod v kódu uvedeným vzorcem mù¾ete vidìt, ¾e dt poèítáme je¹tì jednou. Podìlíme ho poètem cyklù a bude dt = 0.12 / 2 = 0.06. dt bylo pùvodnì vy¹¹í ne¾ maximální mo¾ná hodnota 0.1. Teï se tedy rovná 0.06. My ale provedeme dva cykly simulace, tak¾e v simulaci ubìhne èas 0.12 sekund. Prozkoumejte následující kód a ujistìte se, ¾e v¹emu rozumíte.</p>

<p class="src1"><span class="kom">// Abychom nepøekroèili hranici kdy u¾ se simulace nechová reálnì</span></p>
<p class="src1">float maxPossible_dt = 0.1f;<span class="kom">// Nastavení maximální hodnoty dt na 0.1 sekund</span></p>
<p class="src"></p>
<p class="src1">int numOfIterations = (int)(dt / maxPossible_dt) + 1;<span class="kom">// Výpoèet poètu opakování simulace v závislosti na dt a maximální mo¾né hodnotì dt</span></p>
<p class="src"></p>
<p class="src1">if (numOfIterations != 0)<span class="kom">// Vyhneme se dìlení nulou</span></p>
<p class="src2">dt = dt / numOfIterations;<span class="kom">// dt by se mìla aktualizovat pomocí numOfIterations</span></p>
<p class="src"></p>
<p class="src1">for (int a = 0; a &lt; numOfIterations; ++a)<span class="kom">// Simulaci potøebujeme opakovat numOfIterations-krát</span></p>
<p class="src1">{</p>
<p class="src2">constantVelocity.operate(dt);<span class="kom">// Provedení simulace konstantní rychlosti za dt sekund</span></p>
<p class="src2">motionUnderGravitation.operate(dt);<span class="kom">// Provedení simulace pohybu v gravitaci za dt sekund</span></p>
<p class="src2">massConnectedWithSpring.operate(dt);<span class="kom">// Provedení  simulace pru¾iny za dt sekund
</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0">}</p>

<h2>1. Objekt s konstantní rychlostí</h2>

<p>Objekt s konstantní rychlostí nepotøebuje pùsobení externí síly. Pouze vytvoøíme objekt a nastavíme jeho rychlost na (1.0f, 0.0f, 0.0f), tak¾e se bude pohybovat po ose x rychlostí 1 m/s. Tøídu ConstantVelocity odvodíme od tøídy Simulation.</p>

<p class="src0">class ConstantVelocity : public Simulation</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1"><span class="kom">// Konstruktor nejdøíve pou¾ije konstruktor nadøazené tøídy, aby vytvoøil objekt o hmotnosti 1 kg</span></p>
<p class="src1">ConstantVelocity() : Simulation(1, 1.0f)</p>
<p class="src1">{</p>
<p class="src2">masses[0]-&gt;pos = Vector3D(0.0f, 0.0f, 0.0f);<span class="kom">// Nastavíme polohu objektu na poèátek</span></p>
<p class="src2">masses[0]-&gt;vel = Vector3D(1.0f, 0.0f, 0.0f);<span class="kom">// Nastavíme rychlost objektu na (1.0f, 0.0f, 0.0f) m/s</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0">};</p>

<p>Kdy¾ je volána metoda operate(float dt) tøídy ConstantVelocity, vypoèítá se nová polohu objektu. Tato metoda je volána hlavní aplikací pøed ka¾dým pøekreslením okna. Dejme tomu, ¾e aplikace bì¾í 10 framù za sekundu. To znamená, ¾e pøi ka¾dém novém výpoètu bude dt 0.1 sekundy. Kdy¾ se potom zavolá funkce simulate(float dt) daného objektu, k jeho pozici se pøiète rychlost*dt, které se rovná:</p>

<p>Vector3D(1.0f, 0.0f, 0.0f) * 0.1 = Vector3D(0.1f, 0.0f, 0.0f)</p>

<p>Pøi ka¾dé frame se objekt pohne o 0.1 metru doprava. Po 10 framech to bude právì 1 metr. Rychlost byla 1.0 m/s. Tak¾e to bude fungovat celkem slu¹nì.</p>

<p>Kdy¾ spustíte aplikaci, uvidíte objekt pohybující se konstantní rychlostí po ose x. Aplikace nabízí dvì rychlosti plynutí èasu. Stisknutím F2 pobì¾í stejnì rychle jako reálný èas. Stisknutím F3 pobì¾í 10krát pomaleji. Na obrazovce uvidíte pøímky znázoròující souøadnicovou plochu. Mezery mezi pøimkami jsou 1 metr. Díky tìmto pøímkám uvidíte, ¾e se objekt pohybuje 1 metr za sekundu v reálném èase a 1 metr za 10 sekund ve zpomaleném èase. Vý¹e popsaná technika je zpùsob, jak udìlat simulaci tak, aby bì¾ela v reálném èase. Abyste ji mohli pou¾ít musíte se pevnì rozhodnout, v jakých jednotkách simulace pobì¾í.</p>

<h2>Aplikace síly</h2>

<p>Pøi simulacích s konstantní rychlostí jsme nepou¾ili sílu pùsobící na objekt, proto¾e víme, ¾e pokud síla pùsobí na objekt, tak mìní jeho rychlost. Pokud chceme pohyb s promìnlivou rychlostí pou¾ijeme vnìj¹í sílu. Nejdøíve musíme v¹echny pùsobící síly seèíst, abychom dostali výslednou sílu, kterou v simulaèní fázi aplikujeme na objekt.</p>

<p>Dejme tomu, ¾e chcete pou¾ít na objekt sílu 1 N ve smìru x. Pak do solve() napí¹ete:</p>

<p>mass-&gt;applyForce(Vector3D(1.0f, 0.0f, 0.0f));</p>

<p>Pokud chcete navíc pøidat sílu 2 N ve smìru y, napí¹ete:</p>

<p>mass->applyForce(Vector3D(1.0f, 0.0f, 0.0f));<br />
mass->applyForce(Vector3D(0.0f, 2.0f, 0.0f));</p>

<p>Na objekt mù¾ete pou¾ít libovolné mno¾ství sil, libovolných smìrù, abyste ovlivnili pohyb. V následující èásti pou¾ijeme jednoduchou sílu.</p>

<h2>2. Pohyb v gravitaci</h2>

<p>MotionUnderGravitation vytvoøí objekt a nechá na nìj pùsobit sílu. Touto silou bude právì gravitace, která se vypoèítá vynásobením hmotnosti objektu a gravitaèního zrychlení:</p>

<p>F = m * g</p>

<p>Gravitaèní zrychlení na Zemi odpovídá 9.81 m/s^2. To znamená, ¾e objekt pøi volném pádu zrychlí ka¾dou sekundu o 9.81 m/s dokud na nìho nepùsobí ¾ádná jiná síla ne¾ gravitace. Mù¾e jí být odpor vzduchu, který pùsobí v¾dycky, ale to sem nepatøí.</p>

<p class="src0">class MotionUnderGravitation : public Simulation</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">Vector3D gravitation;<span class="kom">// Gravitaèní zrychlení</span></p>

<p>Konstruktor pøijímá Vector3D, který udává sílu a orientaci gravitace.</p>

<p class="src1"><span class="kom">// Konstruktor nejdøíve pou¾ije konstruktor nadøazené tøídy, aby vytvoøil 1 objekt o hmotnosti 1kg</span></p>
<p class="src1">MotionUnderGravitation(Vector3D gravitation) : Simulation(1, 1.0f)</p>
<p class="src1">{</p>
<p class="src2">this-&gt;gravitation = gravitation;<span class="kom">// Nastavení gravitace</span></p>
<p class="src2">masses[0]-&gt;pos = Vector3D(-10.0f, 0.0f, 0.0f);<span class="kom">// Nastavení polohy objektu</span></p>
<p class="src2">masses[0]-&gt;vel = Vector3D(10.0f, 15.0f, 0.0f);<span class="kom">// Nastavení rychlosti objektu</span></p>
<p class="src1">}</p>

<p class="src1">virtual void solve()<span class="kom">// Aplikace gravitace na v¹echny objekty, na které má pùsobit</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Pou¾ijeme gravitaci na v¹echny objekty (zatím máme jenom jeden, ale to se mù¾e do budoucna zmìnit)</span></p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)</p>
<p class="src3">masses[a]-&gt;applyForce(gravitation * masses[a]-&gt;m);<span class="kom">// Síla gravitace se spoèítá F = m * g</span></p>
<p class="src1">}</p>

<p>V kódu nahoøe si mù¾ete v¹imnout vzorce F = m * g. Pro reálné pùsobení gravitace byste mìli pøedat konstruktoru Vectror3D(0.0f, -9.81f, 0.0f). -9.81 znamená, ¾e má gravitace pùsobit proti smìru y, co¾ zpùsobuje, ¾e objekt padá smìrem dolù. Mù¾ete zkusit zadat kladné èíslo a urèitì poznáte rozdíl.</p>

<h2>3. Objekt spojený pru¾inou s bodem</h2>

<p>V tomto pøíkladì chceme spojit objekt se statickým bodem. Pru¾ina by mìla objekt pøitahovat k bodu upevnìní a tak zpùsobovat oscilaci objektu. V konstruktoru nastavíme bod upevnìní a pozici objektu.</p>

<p class="src0">class MassConnectedWithSpring : public Simulation</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">float springConstant;<span class="kom">// Èím vy¹¹í bude tato konstanta, tím tu¾¹í bude pru¾ina</span></p>
<p class="src1">Vector3D connectionPos;<span class="kom">// Bod ke kterému bude objekt pøipojen</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Konstruktor nejdøíve pou¾ije konstruktor nadøazené tøídy, aby vytvoøil 1 objekt o hmotnosti 1kg</span></p>
<p class="src1">MassConnectedWithSpring(float springConstant) : Simulation(1, 1.0f)</p>
<p class="src1">{</p>
<p class="src2">this-&gt;springConstant = springConstant;<span class="kom">// Nastavení tuhosti pru¾iny</span></p>
<p class="src"></p>
<p class="src2">connectionPos = Vector3D(0.0f, -5.0f, 0.0f);<span class="kom">// Nastavení pozice upevòovacího bodu</span></p>
<p class="src"></p>
<p class="src2">masses[0]-&gt;pos = connectionPos + Vector3D(10.0f, 0.0f, 0.0f);<span class="kom">// Nastavení pozice objektu na 10 metrù napravo od bodu, ke kterému je uchycen</span></p>
<p class="src2">masses[0]-&gt;vel = Vector3D(0.0f, 0.0f, 0.0f);<span class="kom">// Nastavení rychlosti objektu na nulu</span></p>
<p class="src1">}</p>

<p>Rychlost objektu je nula a jeho pozice je 10 metrù napravo od úchytu, tak¾e se bude pohybovat ze zaèátku smìrem doleva. Síla pru¾iny se dá zapsat jako</p>

<p>F = -k * x</p>

<p>k je tuhost pru¾iny a x je vzdálenost od úchytu. Záporná hodnota u k znaèí, ¾e jde o pøita¾livou sílu. Kdyby bylo k kladné, tak by pru¾ina objekt odpuzovala, co¾ zcela jistì neodpovídá skuteènému chování.</p>

<p class="src1">virtual void solve()<span class="kom">// U¾ití síly pru¾iny</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Pou¾ijeme sílu na v¹echny objekty (zatím máme jenom jeden, ale to se mù¾e do budoucna zmìnit)</span></p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)</p>
<p class="src2">{</p>
<p class="src3">Vector3D springVector = masses[a]-&gt;pos - connectionPos;<span class="kom">// Nalezení vektoru od pozice objektu k úchytu</span></p>
<p class="src3">masses[a]-&gt;applyForce(-springVector * springConstant);<span class="kom">// Pou¾ití síly podle uvedeného vzorce</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">};</p>

<p>Výpoèet síly v kódu nahoøe odpovídá vzorci, který jsme si uvedli (F = -k * x). Jenom je zde místo x trojrozmìrný vektor a místo k je zde springConstant. Èím vy¹¹í je springConstant, tím rychleji objekt osciluje.</p>

<p>V tomto tutoriálu jsem se sna¾il pøedvést základní prvky pro tvorbu fyzikálních simulací. Pokud vás zajímá fyzika, nebude pro vás tì¾ké vytvoøit vlastní simulace. Mù¾ete zkou¹et slo¾itìj¹í interakce a vytvoøit tak zajímavá dema a hry. Dal¹í v poøadí by mìli být simulace pevných objektù, jednoduché mechaniky a pokroèilé simulaèní metody.</p>

<p class="autor">napsal: Erkin Tunca <?VypisEmail('erkintunca@icqmail.com');?><br />
pøelo¾il: Václav Slováèek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson39.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson39_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson39.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson39.zip">Dev C++</a> kód této lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson39.tar.gz">Linux/GLut</a> kód této lekce. ( <a href="mailto:laks@imag.fr">Laks Raghupathi</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson39.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(39);?>
<?FceNeHeOkolniLekce(39);?>

<?
include 'p_end.php';
?>
