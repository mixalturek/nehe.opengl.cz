<?
$g_title = 'CZ NeHe OpenGL - Analytická geometrie';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<style>
<!--
.vektor { text-decoration: overline; }
-->
</style>

<h1>Analytická geometrie</h1>

<p class="nadpis_clanku">Tento èlánek vychází z mých zápiskù do matematiky z druhého roèníku na støední ¹kole. Jo, na diktování byla Wakuovka v¾dycky dobrá... Tehdy jsem moc nechápal k èemu mi tento obor matematiky vùbec bude, ale kdy¾ jsem se zaèal vìnovat OpenGL, záhy jsem pochopil. Zkuste si vzít napøíklad nìjaký pozdìj¹í NeHe Tutoriál. Bez znalostí 3D matematiky nemáte ¹anci. Doufám, ¾e vám tento èlánek pomù¾e alespoò se základy a pochopením principù.</p>

<h2>Kartézská soustava souøadnic</h2>
<p>Tak tedy zaèneme. Co je geometrie ví snad ka¾dý. Ale co je analytická geometrie? Dala by se definovat velice jednodu¹e: Analytická geometrie øe¹í výpoètem to, co se dá nakreslit. A pøesnì o to se v poèítaèové grafice jedná. Pro v¹echny výpoèty je nutná soustava souøadnic. Tvoøí ji dvì (popø. tøi) navzájem kolmé osy, které mají stejnì velké jednotky.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/soustava_souradnic.gif" width="175" height="78" alt="Soustava souøadnic" /></div>

<h2>Bod</h2>
<p>Nejjednodu¹¹ím útvarem je bod. Nemá ¾ádný rozmìr, ale pouze polohu. Jeho poloha má ve dvourozmìrném systému dvì slo¾ky, ve tøírozmìrném tøi atd. Formálnì se zapisuje takto: JMÉNO BODU[x, y]. V následujícím pøíkladu by se uvedené body definovaly takto:</p>

<p class="src0">A[-2; 1]</p>
<p class="src0">B[ 3; 1]</p>
<p class="src0">C[-2; 3]</p>
<p class="src"></p>
<p class="src0">S[ ?; ?]<span class="kom">// Je støedem úseèky BC</span></p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/priklad_01.gif" width="200" height="200" alt="Pøíklad 1" /></div>

<h3>Støed úseèky - grafická metoda</h3>
<p>Bod S jsme nedefinovali, proto¾e jen tak od oka jeho slo¾ky neurèíme. Víme pouze, ¾e je støedem úseèky BC a chceme urèit polohu. V pøípadì grafické metody bychom si vzali papír, tu¾ku a pravítko, nakreslili bychom souøadnicové osy, vepsali body a spojili je úseèkami. Dále bychom zmìøili délku úseèky BC, vydìlili ji dvìma a tuto vypoèítanou vzdálenost nanesli (je jedno, zda z bodu B nebo C, proto¾e S je v polovinì). Odeèteme jednotlivé x, y slo¾ky a získáme polohu bodu S.</p>

<h3>Støed úseèky - poèetní metoda</h3>
<p>Jiná je ale situace, kdybychom nemìli ¾ádné pomùcky a museli v¹echno spoèítat. Z obrázku jde na první pohled vidìt, ¾e x-ová slo¾ka bodu S je prùmìrem x-ových slo¾ek bodu B a C. Y-ová slo¾ka je to samé. Mù¾eme tedy vytvoøit vzorec a dosadit hodnoty. Porovnáme-li výsledek S[0,5; 2] s obrázkem, souøadnice odpovídají. Od nynìj¹ka budeme pou¾ívat pouze poèetní metodu.</p>

<p class="src0">S[x; y] = [((x<sub>B</sub> + x<sub>C</sub>) / 2); ((y<sub>B</sub> + y<sub>C</sub>) / 2)]</p>
<p class="src0">S[((3 - 2)) / 2); ((1 + 3) / 2)]</p>
<p class="src0">S[0,5; 2]</p>
<p class="src"></p>

<h3>Délka úseèky</h3>
<p>Druhým problémem mù¾e být, pokud chceme zjistit délku úseèky BC. Opìt se podíváme na obrázek a na první pohled vidíme :-), ¾e úseèka BC je pøeponou pravoúhlého trojúhelníku ABC, pro který platí Pythagorova vìta.</p>

<p class="src0">c<sup>2</sup> = a<sup>2</sup> + b<sup>2</sup></p>
<p class="src0">c = sqrt(a<sup>2</sup> + b<sup>2</sup>)<span class="kom">// sqrt(x) = druhá odmocnina z x</span></p>

<p>Dá se øíct, ¾e následující vzorec by platil i tehdy, pokud by trojúhelník ABC nebyl pravoúhlý a dokonce i tehdy, pokud bychom nemìli vùbec ¾ádný trojúhelník, ale pouze úseèku BC. Pravým úhlem je v tomto pøípadì kolmost souøadnicových os, délky stran a, c zastupují prùmìty úseèky do os x, y. Pythagorova vìta tedy stále platí.</p>

<p class="src0">|BC| = sqrt((x<sub>B</sub> - x<sub>C</sub>)<sup>2</sup> + (y<sub>B</sub> - y<sub>C</sub>)<sup>2</sup>)</p>
<p class="src0">|BC| = sqrt(5<sup>2</sup> + (-2)<sup>2</sup>)</p>
<p class="src0">|BC| = sqrt(25 + 4)</p>
<p class="src0">|BC| = sqrt(29)</p>
<p class="src0">|BC| = 5,3851</p>
<p class="src"></p>

<h2>Vektor</h2>

<p>Vektory obecnì urèují, jak se zmìní slo¾ky souøadnic x a y ne¾ se pøesuneme z poèátku vektoru na jeho konec. Znaèí se ¹ipkou smìøující zleva doprava (od poèáteèního do koncového bodu) nad jménem vektoru. Pozn.: V tomto èlánku ho znaèím pouze èárou, proto¾e nemohu pøijít na to, jak v HTML vytvoøit ¹ipku. Narozdíl od bodu, který se umís»uje do hranatých, pí¹eme slo¾ky vektoru do obyèejných kulatých závorek. Chceme-li zjistit vektor <span class="vektor">BC</span> odeèteme od koncového bodu, poèátek.</p>

<p class="src0"><span class="vektor">BC</span> = ((x<sub>C</sub> - x<sub>B</sub>); (y<sub>C</sub> - y<sub>B</sub>))</p>
<p class="src0"><span class="vektor">BC</span> = ((-2 -3); (3-1))</p>
<p class="src0"><span class="vektor">BC</span> = (-5; 2)</p>

<p>Dva stejné vektory (stejnì velké a stejnì orientované) pøedstavují v matematice dvì rùzná umístìní tého¾ vektoru. Z toho plyne, ¾e vektor mù¾eme pøenést kamkoli na rovnobì¾nou pøímku, akorát nesmíme zmìnit jeho velikost. Toto ale neplatí ve fyzice, kde se u vektoru, kromì velikosti musí urèit i poèátek nebo konec (napø. pùsobi¹tì síly).</p>

<h3>Velikost vektoru</h3>
<p>Pøi zji¹»ování velikosti vektoru se postupuje úplnì stejnì jako pøi urèování délky úseèky.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/priklad_02.gif" width="100" height="100" alt="Pøíklad 2" /></div>

<p class="src0">|<span class="vektor">AB</span>| = sqrt((x<sub>B</sub> - x<sub>A</sub>)<sup>2</sup> + (y<sub>B</sub> - y<sub>A</sub>)<sup>2</sup>)</p>
<p class="src"></p>

<h3>Normalizace vektoru na jednotkovou délku</h3>
<p>V matematice není u vektoru dùle¾itá jeho délka, ale smìr. Smìøují-li dva vektory stejným smìrem, jsou shodné (mohou le¾et i na libovolných rovnobì¾kách). Jednotkovou délku vektoru vypoèítáme tak, ¾e získáme aktuální délku vektoru a touto hodnotou vydìlíme jednotlivé x, y, z slo¾ky.</p>

<h2>Poèítání s vektory</h2>

<h3>Sèítání a odeèítání</h3>
<p>V¾dy seèteme zvlá¹» x-ové a zvlá¹» y-slo¾ky. Nic tì¾kého.</p>

<p class="src0"><span class="vektor">a</span> = (-3; 2)</p>
<p class="src0"><span class="vektor">b</span> = ( 4; 6)</p>
<p class="src"></p>
<p class="src0"><span class="vektor">a</span> + <span class="vektor">b</span> = ( 1; 8)</p>
<p class="src0"><span class="vektor">a</span> - <span class="vektor">b</span> = (-7;-4)</p>
<p class="src"></p>

<h3>Násobení a dìlení vektoru èíslem</h3>
<p>Opìt vynásobíme zvlá¹» jednotlivé slo¾ky.</p>

<p class="src0">4 * <span class="vektor">a</span> = (4 * x<sub>a</sub>; 4 * y<sub>a</sub>)</p>
<p class="src0">4 * <span class="vektor">a</span> = (-12; 8)</p>
<p class="src"></p>

<h3>Skalární souèin vektorù</h3>

<p>Násobí se zvlá¹» x-ové a zvlá¹» y-ové slo¾ky, které se seètou.</p>

<p class="src0"><span class="vektor">u</span> * <span class="vektor">v</span> = u<sub>X</sub>*v<sub>X</sub> + u<sub>Y</sub>*v<sub>Y</sub></p>

<p>Nebo také</p>

<p class="src0"><span class="vektor">u</span> * <span class="vektor">v</span> = |<span class="vektor">u</span>| * |<span class="vektor">v</span>| * cos uhel</p>
<p class="src"></p>

<h3>Úhel dvou vektorù</h3>

<p>Ze vzorce pro výpoèet skalárního souèinu vektorù (viz. vý¹e) lze odvodit vztah pro výpoèet úhlu dvou vektorù.</p>

<p class="src0">cos uhel = (<span class="vektor">u</span> * <span class="vektor">v</span>) / (|<span class="vektor">u</span>| * |<span class="vektor">v</span>|)</p>

<p>Dosadíme-li do rovnice døíve uvedené vzorce, získáme následující vztah:</p>

<img src="images/clanky/analyticka_geometrie/vzorec_uhel_2_vektoru.gif" width="190" height="55" alt="Vzorec pro výpoèet úhlu dvou vektorù" />

<p>A pokud budou vektory normalizované (jednotková délka), mù¾eme jmenovatel ze zlomku vypustit, proto¾e se rovná jedné.</p>

<p class="src0">cos uhel = (<span class="vektor">u</span> * <span class="vektor">v</span>)<span class="kom">// Za pøedpokladu jednotkových délek</span></p>

<h3>Vektorový souèin dvou vektorù</h3>

<p>Vynásobíme-li vektorovì dva vektory, získáme vektor tøetí, který je kolmý k rovinì urèené pùvodními dvìma vektory. Aby bylo mo¾no urèit vektorový souèin, nesmí být vektory <span class="vektor">u</span> a <span class="vektor">v</span> rovnobì¾né, proto¾e by netvoøily rovinu. Vektorový souèin se neznaèí operátorem *, ale x.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/vektorovy_soucin.gif" width="100" height="100" alt="Vektorový souèin dvou vektorù" /></div>

<p class="src0"><span class="vektor">u</span> = (-3; 5; -1)</p>
<p class="src0"><span class="vektor">v</span> = ( 1; 2; -4)</p>

<img src="images/clanky/analyticka_geometrie/vektorovy_soucin_vzorec.gif" width="395" height="60" alt="Vzorec vektorového souèinu dvou vektorù" />

<p>Nyní polo¾íme vektory <span class="vektor">i</span>, <span class="vektor">j</span>, <span class="vektor">k</span> rovny jedné (jednotkové vektory). Násobením jednièkou se koeficienty nezmìní, tak¾e jsme právì získali výsledek.</p>

<p class="src0"><span class="vektor">w</span> = (-18; -13; -11)</p>

<h2>Vektorové prostory</h2>

<h3>Lineárnì závislé a lineárnì nezávislé vektory</h3>

<p>Dimenze (rozmìr) vektorového prostoru urèuje maximální poèet lineárnì nezávislých vektorù. Je také dána poètem slo¾ek souøadnice. Vektory jsou lineárnì závislé, kdy¾ pro libovolný vektor soustavy vektorù platí:</p>

<p class="src0"><span class="vektor">v1</span>, <span class="vektor">v2</span>, <span class="vektor">v3</span> ... <span class="vektor">vn</span></p>
<p class="src0"><span class="vektor">vm</span> = k1*<span class="vektor">v1</span> + k2*<span class="vektor">v2</span> + ... + kn*<span class="vektor">vn</span></p>

<p>Pro lineárnì nezávislé vektory tato rovnice neplatí pro ¾ádný vektor systému.</p>

<h3>Jednorozmìrný prostor - pøímka</h3>
<p>Jednorozmìrný prostor má pouze jeden nezávislý vektor. Ostatní vektory na pøímce se dají pomocí tohoto vektoru vyjádøit.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/dimenze1.gif" width="200" height="57" alt="Jednorozmìrný prostor" /></div>

<h3>Dvourozmìrný prostor - rovina</h3>
<p>Má dva lineárnì nezávislé vektory.</p>

<h3>Tøírozmìrný prostor</h3>
<p>Má tøi lineárnì nezávislé vektory. Myslím, ¾e podstatu u¾ chápete. Tyto vlastnosti se vyu¾ívají k øe¹ení systému N rovnic o N neznámých.</p>

<h2>Kolmé vektory</h2>
<p>Dva kolmé vektory v rovinì mají obrácené poøadí slo¾ek a u jedné z nich (jedno které) opaèné znaménko. Kolmý vektor k zadanému vektoru je rovnì¾ libovolný k-násobek vektoru získaného uvedeným postupem. Odvození této definice vychází ze vzorce pro úhel dvou vektorù, kde se za úhel dosadí 90°.</p>

<p class="src0"><span class="vektor">a</span> = ( 3; 2)</p>
<p class="src0"><span class="vektor">b</span> = ( ?; ?)<span class="kom">// Je kolmý k <span class="vektor">a</span></span></p>
<p class="src"></p>
<p class="src0"><span class="vektor">b</span> = ( 2;-3)</p>
<p class="src0"><span class="vektor">b</span> = (-2; 3)</p>
<p class="src0"><span class="vektor">b</span> = (-4; 6)<span class="kom">// Atd.</span></p>

<h2>Rovnice pøímky v rovinì</h2>
<h3>Typy rovnic pøímky:</h3>
<ul>
<li>Parametrické rovnice pøímky</li>
<li>Obecný tvar</li>
<li>Smìrnicový tvar</li>
<li>Smìrnicový tvar urèený souøadnicemi bodù</li>
<li>Úsekový tvar</li>
</ul>

<h2>Parametrický tvar rovnic pøímky</h2>
<p>Následující vzorec je parametrickou rovnicí pøímky.</p>

<p class="src0">p:</p>
<p class="src0">x = A<sub>X</sub> + s<sub>X</sub>*t</p>
<p class="src0">y = A<sub>Y</sub> + s<sub>Y</sub>*t</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_primky_param.gif" width="139" height="57" alt="Parametrická rovnice pøímky" /></div>

<p class="src0">A[2; 4]</p>
<p class="src0"><span class="vektor">s</span> = (5; 7)</p>
<p class="src"></p>
<p class="src0">p:</p>
<p class="src0">x = 2 + 5*t</p>
<p class="src0">y = 4 + 7*t</p>
<p class="src"></p>

<h3>Sestavení parametrických rovnic ze dvou bodù</h3>
<p>Toto u¾ by mìlo být jasné, ale myslím si, ¾e malá ukázka ¹kodit nebude. Je úplnì jedno, zda po získání vektoru dosadíme do rovnice souøadnice bodu A nebo B, proto¾e oba le¾í na hledané pøímce.</p>

<p class="src0">A[2; 4]</p>
<p class="src0">B[5; 3]</p>
<p class="src"></p>
<p class="src0"><span class="vektor">s</span> = B - A = (5-2; 3-4) = (3; -1)</p>
<p class="src"></p>
<p class="src0">p:</p>
<p class="src0">x = 2 + 3*t</p>
<p class="src0">y = 4 - t</p>

<p>A je¹tì si zkusíme, zda bod C[1; 2] na této pøímce le¾í nebo ne. Za x a y dosadíme jeho souøadnice a pokud se budou parametry t v obou rovnicích rovnat, C le¾í na pøímce.</p>

<p class="src0">p:</p>
<p class="src0">1 = 2 + 3*t <span class="kom">=&gt;</span> 3*t = -1 <span class="kom">=&gt;</span> t = -1/3</p>
<p class="src0">2 = 4 - t <span class="kom">=&gt;</span> -t = -2 <span class="kom">=&gt;</span> t = 2</p>

<p>V první rovnici vy¹lo t rovno -1/3 v druhé se t rovnalo 2. Z toho plyne, ¾e bod C nele¾í na pøímce t.</p>

<p>Nevýhoda parametrického vyjádøení je v tom, ¾e na první pohled nepoznáme, jestli se jedná u dvou soustav rovnic o tuté¾ pøímku nebo ne.</p>

<h3>Poèítání s rovnicemi pøímek v parametrickém tvaru</h3>

<p>Pøedem se omlouvám za to, ale v¹echno bude pouze teoreticky, proto¾e psaní pøíkladù v HTML mi u¾ leze opravdu na nervy.</p>

<p>Rovnici pøímky sestavit umíme, ale jak bychom postupovali pøi hledání rovnice pøímky, která je rovnobì¾ná ke známé pøímce v urèitém bodì? Vycházíme z toho, ¾e rovnobì¾né pøímky mohou mít stejné smìrové vektory, tak¾e pouze nahradíme souøadnice bodu v rovnicích.</p>

<p>U kolmé pøímky by se opìt nahradily souøadnice bodu, ale navíc bychom je¹tì museli zmìnit vektor na kolmý - nic tì¾kého (viz. kolmé vektory).</p>

<p>Prùseèík dvou pøímek je spoleèným øe¹ením dvou rovnic pøímek. V parametrickém tvaru porovnáme souø. x první pøímky se souø. x druhé pøímky. Vypoèteme parametr t a jestli platí rovnost i pro souøadnice y pøi stejném t, je tento parametr pøíslu¹ný k prùseèíku. Dosazením parametru do rovnic dostaneme souøadnice prùseèíku.</p>

<p>Úhel dvou pøímek je úhlem mezi dvìma smìrovými vektory (viz. Úhel dvou vektorù).</p>

<h2>Obecná rovnice pøímky</h2>
<p>Ní¾e je uveden její vzorec. Promìnné x a y jsou souøadnice bodu, který le¾í na pøímce. A, b jsou slo¾ky normálového vektoru pøímky.</p>

<p class="src0">p: a*x + b*y + c = 0</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_primky_obec.gif" width="139" height="57" alt="Obecná rovnice pøímky" /></div>

<h3>Sestavení obecné rovnice pøímky ze dvou bodù</h3>

<p>Nejprve vypoèítáme smìrový vektor a ten pøevedeme na normálový. Tím získáme promìnné a, b, x, y. Zùstane nám jednoduchá rovnice o jedné neznámé. Vypoèteme c a dosadíme do pùvodní rovnice</p>

<p class="src0">A[2; 4]</p>
<p class="src0">B[5; 3]</p>
<p class="src"></p>
<p class="src0"><span class="vektor">s</span> = <span class="vektor">AB</span> = (5-2; 3-4) = (3; -1)<span class="kom">// Smìrový vektor</span></p>
<p class="src0"><span class="vektor">n</span> = (1; 3)<span class="kom">// Normálový vektor</span></p>

<p>Získali jsme èísla a = 1, b = 3 a pou¾ijeme napø. slo¾ky bodu A (je to jedno). V¹e dosadíme do rovnice pøímky a hledáme èíslo c.</p>

<p class="src0">p: a*x + b*y + c = 0</p>
<p class="src0">p: 1*2 + 3*4 + c = 0</p>
<p class="src"></p>
<p class="src0">c = - (1*2 + 3*4) = - (2 + 12) = -14</p>

<p>Nakonec dosadíme do rovnice pøímky èísla a, b, c a máme výsledek.</p>

<p class="src0">p: x + 3y - 14 = 0<span class="kom">// Výsledná rovnice pøímky</span></p>

<p>Je¹tì jedna poznámka na konec. Je-li smìrový (popø. normálový) vektor zadán tak, ¾e jeho slo¾ky se dají roz¹íøit nebo krátit, mù¾eme za nìj zvolit jeho libovolný k násobek. Následující dvì rovnice pøímek jsou tedy toto¾né:</p>

<p class="src0">p: -4x + 8y + 24 = 0</p>
<p class="src0">p: -x + 2y + 6 = 0<span class="kom">// Vykrácená verze</span></p>
<p class="src"></p>

<h3>Rovnice rovnobì¾ných pøímek</h3>
<p>Hledáme rovnobì¾ku q s pøímkou p: x - 2y + 3 = 0, která prochází bodem M[2; 5]. Staèí dosadit souøadnice bodu M do rovnice p a vypoèítat èíslo c, které dosadíme do pùvodní rovnice p. V¹imnìte si, ¾e se pøímky li¹í pouze èíslem c.</p>

<p class="src0">q: x - 2y + c = 0</p>
<p class="src0">2 - 2*5 + c = 0</p>
<p class="src0">c = 8</p>
<p class="src"></p>
<p class="src0">q: x - 2y + 8 = 0<span class="kom">// Pøímka q je rovnobì¾ná s p</span></p>
<p class="src0">p: x - 2y + 3 = 0</p>
<p class="src"></p>

<h3>Rovnice kolmých pøímek</h3>
<p>Opìt pouze postup: Vytvoøí se kolmý vektor k normálovému vektoru pùvodní pøímky. Spolu s bodem, kterým má kolmá pøímka procházet, se dosadí do rovnice a vypoèítá se c. Pøíklad dvou kolmých pøímek:</p>

<p class="src0">p: 5x - 6y + 4 = 0</p>
<p class="src0">q: 6x + 5y -15 = 0</p>

<p>Stejnì jako rovnobì¾né pøímky, jsou si také trochu podobné...</p>

<h2>Rovnice pøímky ve smìrnicovém tvaru</h2>

<p>Smìrnicový tvar rovnice pøímky se u¾ívá tam, kde je zadán sklon pøímky vzhledem k ose x nebo je-li smìrnice vypoètena jiným zpùsobem (napø. derivací). Èíslo q v následující rovnici oznaèuje úsek na ose y, kde ji pøímka protíná. K je smìrnice pøímky, která se vypoèítá goniometrickou funkcí k = tg a. Je to také derivace jakékoli funkce - tedy teèna k jejímu grafu.</p>

<p class="src0">y = k*x + q</p>

<p>Nebo také:</p>

<p class="src0">y - y<sub>A</sub> = k * (x - x<sub>A</sub>)</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_primky_smer.gif" width="345" height="161" alt="Smìrnicová rovnice pøímky" /></div>

<h3>Získání èísla k</h3>

<p>Vycházíme z toho, ¾e k = tg a. Postup zji¹tìní tg a vysvìtluje obrázek.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_primky_smer_zisk_k.gif" width="183" height="161" alt="Získání èísla k" /></div>

<p>Po zji¹tìní èísla k nám u¾ nic nebrání tomu, abychom ho spolu se slo¾kami bodu A nebo B dosadili do rovnice a získali q.</p>

<h3>Rovnobì¾ky a kolmice</h3>

<p>Rovnobì¾né pøímky mají v rovnicích stejnou hodnotu k, li¹í se v q. Kolmice mají v k zápornou pøevrácenou hodnotu. To znamená, ¾e pokud je u jedné napøíklad k = 2, u druhé bude k = - 1/2.</p>

<h3>Vzdálenost bodu od pøímky a vzdálenost dvou rovnobì¾ek</h3>

<p>Èísla a, b, c jsou konstanty z rovnice pøímky v obecném tvaru a èísla x, y jsou souøadnice bodu. Staèí pak dosadit do vzorce.</p>

<img src="images/clanky/analyticka_geometrie/bod_od_primky.gif" width="90" height="46" alt="Vzdálenost bodu od pøímky" />

<p>Vzdálenost dvou rovnobì¾ek lze zjistit úplnì stejnì. Nejdøíve získáme bod, který le¾í na jedné z nich a pak opìt dosadíme do vzorce.</p>

<h2>Rovina v prostoru</h2>

<h3>Typy rovnic rovin:</h3>
<ul>
<li>Parametrické rovnice roviny</li>
<li>Obecná rovnice</li>
</ul>

<h2>Parametrické rovnice roviny</h2>
<p>Jsou skoro stejné jako parametrické rovnice pøímky. Následuje vzorec a pøíklad.</p>

<p class="src0">R:</p>
<p class="src0">x = A<sub>X</sub> + k<sub>X</sub>*<span class="vektor">u</span> + l<sub>X</sub>*<span class="vektor">v</span></p>
<p class="src0">y = A<sub>Y</sub> + k<sub>Y</sub>*<span class="vektor">u</span> + l<sub>Z</sub>*<span class="vektor">v</span></p>
<p class="src0">z = A<sub>Z</sub> + k<sub>Y</sub>*<span class="vektor">u</span> + l<sub>Z</sub>*<span class="vektor">v</span></p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_roviny_param.gif" width="77" height="80" alt="Parametrické rovnice roviny" /></div>

<p class="src0">R:</p>
<p class="src0">x = 1 + 3k +l</p>
<p class="src0">y = 4 - 4k - 5l</p>
<p class="src0">z = -2 + 7k - l</p>

<h2>Obecná rovnice roviny</h2>

<p>Vzorec je analogický vzorci obecné rovnice pøímky. Èísla a, b, c jsou slo¾ky normálového vektoru roviny a èísla x, y, z jsou souøadnice bodu, který na ní le¾í. Stejnì jako se u pøímky dopoèítávalo c, nyní se musí spoèítat d - ¾ádný problém, v¹e u¾ známe.</p>

<p class="src0">R: ax + by + cz + d = 0</p>

<p>Dáme si jeden pøíklad a zároveò ho teoreticky! vyøe¹íme. Urèete rovnici roviny R, která je dána body K[-2; 1; 1], L[0; 3; -5]; M[4; -6; 9]. Postupujeme tak, ¾e urèíme jeden bod jako základní, napøíklad K a urèíme vektory <span class="vektor">KL</span> a <span class="vektor">KM</span>. Potøebujeme urèit vektor, který je kolmý k obìma najednou. Získáme ho vektorovým souèinem vektorù <span class="vektor">n</span> = <span class="vektor">KL</span> x <span class="vektor">KM</span>. Výsledek dosadíme do rovnice roviny a vypoèítáme neznámou d.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_roviny_obec.gif" width="58" height="63" alt="Parametrické rovnice roviny" /></div>

<h2>Zvlá¹tní polohy roviny v prostoru</h2>

<h3>Rovina procházející poèátkem souøadnic</h3>
<p>Jeden bod v rovinì (poèátek souøadnic) má souøadnice 0[0; 0; 0]. Z toho plyne, ¾e:</p>
<p class="src0">R: a*x + b*y + c*z + d = 0</p>
<p class="src0">R: a*0 + b*0 + c*0 + d = 0</p>
<p class="src0">d = 0<span class="kom">// Výsledná rovnice roviny</span></p>
<p class="src"></p>

<h3>Rovina rovnobì¾ná s nìkterou osou</h3>
<p>Chybí koeficient u promìnné, která patøí ke konkrétní ose.</p>
<p class="src0">R: 3y - 2z + 5 = 0<span class="kom">// Rovina rovnobì¾ná s osou x</span></p>
<p class="src"></p>

<h3>Rovina rovnobì¾ná s dvìma osami</h3>
<p>U obou chybí koeficienty a tøetí slo¾ka je konstantní.</p>
<p class="src0">R: 3z + 5 = 0<span class="kom">// Rovina rovnobì¾ná s osami x a y</span></p>
<p class="src0">z = - 5/3</p>
<p class="src0">z = konst.</p>

<h2>Zpùsoby zadání roviny</h2>

<p>Ka¾dá rovina je zadána v¾dy tøemi nezávislými prvky:</p>
<ul>
<li>Tøi body nele¾ící v pøímce</li>
<li>Dvì pøímky, které nejsou mimobì¾né (= které se protínají)</li>
<li>Jeden bod a dva smìrové vektory</li>
<li>Rovnobì¾nost roviny s nìkterou osou a dva body, popø. jedna pøímka</li>
<li>Rovnobì¾nost roviny se dvìma osami a jeden bod</li>
</ul>

<h2>Pøímka v prostoru</h2>

<p>V rovinì mohla být pøímka vyjádøena mnoha zpùsoby, které se v prostoru smrskly na jediný mo¾ný zpùsob - parametrické rovnice.</p>

<h3>Parametrická rovnice pøímky</h3>
<p>Tohle u¾ známe, tak¾e to nebudu zbyteènì rozepisovat, pøidává se pouze dal¹í rovnice pro osu z.</p>

<p class="src0">p:</p>
<p class="src0">x = A<sub>X</sub> + <span class="vektor">s</span><sub>X</sub>*t</p>
<p class="src0">y = A<sub>Y</sub> + <span class="vektor">s</span><sub>Y</sub>*t</p>
<p class="src0">z = A<sub>Z</sub> + <span class="vektor">s</span><sub>Z</sub>*t</p>

<p>Hledáme-li pøímku jako prùseènici dvou rovin, zvolíme dva body, které le¾í souèasnì v obou rovinách - tj. jednu souøadnici zvolíme a ostatní dvì musí vyhovovat rovnicím obou rovin. Pak staèí vytvoøit smìrový vektor a následnì ho s jedním z bodù dosadit do parametrických rovin pøímek.</p>

<h2>Závìr</h2>

<p>... a nyní byste mìli rozumìt alespoò základùm analytické geometrie. My jsme toto uèivo brali ve ¹kole skoro ètvrt roku, tak¾e tento èlánek berte jako struèný!!! výtah. Pokud se vám zdá, ¾e je v¹e jakoby useknuté, máte pravdu. Jak to tak bývá, skonèil ¹kolní rok a na zaèátku dal¹ího u¾ se nepokraèovalo. Také musím poznamenat, ¾e ke ka¾dého tématu máme v se¹itì nìkolik pøíkladù, ale opisovat je sem by se s nejvìt¹í pravdìpodobností stalo mojí noèní mùrou - dìkuji za pochopení... nebo jinak øeèeno: Buïte rádi za to, co tady máte, psal jsem to skoro týden, ètyøi hodiny dennì :-)</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<?
include 'p_end.php';
?>
