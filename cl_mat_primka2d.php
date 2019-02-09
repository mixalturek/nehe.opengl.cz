<?
$g_title = 'CZ NeHe OpenGL - Pøímka ve 2D';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Pøímka ve 2D</h1>

<p class="nadpis_clanku">Radomír Vrána mì po¾ádal o radu, jak vypoèítat prùseèík dvou 2D pøímek. Rozhodl jsem se, ¾e mu místo obecných matematických vzorcù po¹lu rovnou kompletní C++ kód. Nicménì mi trochu pøerostl pøes hlavu, a tak vznikla kompletní tøída pøímky v obecném tvaru. Kromì prùseèíku umí urèit i jejich vzájemnou polohu (rovnobì¾né, kolmé...), úhel, který svírají nebo vzdálenost libovolného bodu od pøímky. Doufám, ¾e tento mùj drobný úlet nebude moc vadit :-]</p>

<p>Kód rozdìlíme klasicky na hlavièkový a implementaèní soubor tøídy, zaèneme hlavièkovým. Aby nenastaly problémy pøi vícenásobném inkludování, nadefinujeme symbolickou konstantu __PRIMKA2D_H__ a pøed vlastní definicí otestujeme, jestli u¾ existuje. Pokud ano, instrukce preprocesoru #ifndef zajistí, ¾e se tento soubor nebude zpracovávat dvakrát.</p>

<p class="src0">#ifndef __PRIMKA2D_H__</p>
<p class="src0">#define __PRIMKA2D_H__</p>
<p class="src"></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Matematická knihovna</span></p>

<p>Pøedpokládám, ¾e u¾ znáte rovnici pøímky v obecném tvaru, ale pro ty z vás, kteøí je¹tì nejsou na støední ¹kole (konec druhého roèníku), ji zkusím alespoò naznaèit.</p>

<p>Obecná rovnice pøímky je vyjádøena ve tvaru a*x + b*y + c = 0. Pokud za x, y dosadíme souøadnice libovolného 2D bodu a vyjde nám nula, máme jistotu, ¾e tento bod na pøímce le¾í. Konstanty a, b pøedstavují normálový vektor pøímky (vektor, který je k pøímce kolmý). c je také konstanta, urèí se výpoètem pøi dosazení bodu do rovnice. Kromì obecného tvaru existují i dal¹í vzájemnì zamìnitelné tvary - napøíklad parametrický a smìrnicový.</p>

<p>Myslím, ¾e komentáøe v¹e vysvìtlují...</p>

<p class="src0">class WPrimka2D<span class="kom">// Tøída 2D pøímky v obecném tvaru</span></p>
<p class="src0">{</p>
<p class="src0">private:</p>
<p class="src1">double a, b, c;<span class="kom">// Obecná rovnice pøímky a*x + b*y + c = 0</span></p>
<p class="src"></p>
<p class="src0">public:</p>
<p class="src1">WPrimka2D();<span class="kom">// Konstruktor</span></p>
<p class="src1">~WPrimka2D();<span class="kom">// Destruktor</span></p>
<p class="src1">WPrimka2D(const WPrimka2D& primka);<span class="kom">// Kopírovací konstruktor</span></p>
<p class="src1">WPrimka2D(double a, double b, double c);<span class="kom">// Pøímé zadání promìnných</span></p>
<p class="src1">WPrimka2D(double x1, double y1, double x2, double y2);<span class="kom">// Pøímka ze dvou bodù</span></p>
<p class="src"></p>
<p class="src1">void Create(double a, double b, double c);<span class="kom">// Pøímé zadání promìnných</span></p>
<p class="src1">void Create(double x1, double y1, double x2, double y2);<span class="kom">// Pøímka ze dvou bodù</span></p>
<p class="src"></p>
<p class="src1">inline double GetA() { return a; }<span class="kom">// Získání atributù</span></p>
<p class="src1">inline double GetB() { return b; }</p>
<p class="src1">inline double GetC() { return b; }</p>
<p class="src"></p>
<p class="src1">bool operator==(WPrimka2D&amp; primka);<span class="kom">// Splývající pøímky?</span></p>
<p class="src1">bool operator!=(WPrimka2D&amp; primka);<span class="kom">// Nesplývající pøímky?</span></p>
<p class="src"></p>
<p class="src1">bool JeNaPrimce(double x, double y);<span class="kom">// Le¾í bod na pøímce?</span></p>
<p class="src1">bool JsouRovnobezne(WPrimka2D&amp; primka);<span class="kom">// Jsou pøímky rovnobì¾né?</span></p>
<p class="src1">bool JsouKolme(WPrimka2D&amp; primka);<span class="kom">// Jsou pøímky kolmé?</span></p>
<p class="src"></p>
<p class="src1">bool Prusecik(WPrimka2D&amp; primka, double&amp; retx, double&amp; rety);<span class="kom">// Prùseèík pøímek</span></p>
<p class="src1">double Uhel(WPrimka2D&amp; primka);<span class="kom">// Úhel pøímek (v radiánech)</span></p>
<p class="src1">double VzdalenostBodu(double x, double y);<span class="kom">// Vzdálenost bodu od pøímky</span></p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">#endif</p>

<p>Hlavièkový soubor je za námi. Zaèneme implementovat jednotlivé metody. V obecném konstruktoru nastavíme v¹echny vlastnosti na nulu, destruktor necháme prázdný.</p>

<p class="src0">#include &quot;primka2d.h&quot;<span class="kom">// Hlavièkový soubor</span></p>
<p class="src"></p>
<p class="src0">WPrimka2D::WPrimka2D()<span class="kom">// Obecný konstruktor</span></p>
<p class="src0">{</p>
<p class="src1">a = 0.0;<span class="kom">// Nulování</span></p>
<p class="src1">b = 0.0;</p>
<p class="src1">c = 0.0;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">WPrimka2D::~WPrimka2D()<span class="kom">// Destruktor</span></p>
<p class="src0">{</p>
<p class="src"></p>
<p class="src0">}</p>

<p>Kopírovací konstruktor...</p>

<p class="src0">WPrimka2D::WPrimka2D(const WPrimka2D& primka)<span class="kom">// Kopírovací konstruktor</span></p>
<p class="src0">{</p>
<p class="src1">a = primka.a;</p>
<p class="src1">b = primka.b;</p>
<p class="src1">c = primka.c;</p>
<p class="src0">}</p>

<p>Abychom mohli inicializovat tøídu u¾ pøi jejím vytvoøení, pøetí¾íme konstruktor. Kdykoli v programu ho mù¾e nahradit metoda Create().</p>

<p class="src0">WPrimka2D::WPrimka2D(double a, double b, double c)<span class="kom">// Pøímé zadání promìnných</span></p>
<p class="src0">{</p>
<p class="src1">Create(a, b, c);</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">void WPrimka2D::Create(double a, double b, double c)<span class="kom">// Pøímé zadání promìnných</span></p>
<p class="src0">{</p>
<p class="src1">this-&gt;a = a;</p>
<p class="src1">this-&gt;b = b;</p>
<p class="src1">this-&gt;c = c;</p>
<p class="src0">}</p>

<p>Ètvrtý konstruktor umí vytvoøit pøímku ze dvou bodù, opìt ho mù¾e nahradit funkce Create(). Jak jsem naznaèil vý¹e, promìnné a, b pøedstavují normálový vektor pøímky. Smìrový vektor by se získal jednoduchým odeètením koncového bodu od poèáteèního. Vytvoøení normálového vektoru je podobné, ale navíc prohodíme slo¾ky vektoru a u jedné invertujeme znaménko.</p>

<p>Radìji pøíklad. Máme dva body [1; 2] a [4; 3], smìrový vektor se získá odeètením koncového bodu od poèáteèního, nicménì pøi vytváøení pøímky je úplnì jedno, který pova¾ujeme za poèáteèní a který za koncový. První bod bude napøíklad poèáteèní a druhý koncový. Smìrový vektor je tedy s = (4-1, 3-2) = (3; 1). Normálový vektor má prohozené poøadí slo¾ek a u jedné opaèné znaménko. n = (-1; 3) nebo (1; -3).</p>

<p>Pro úplnost: je naprosto jedno, zda vezmeme pøímo vypoètený vektor nebo jeho k-násobek. Oba vektory uvedené v minulém odstavci jsou k-násobkem toho druhého (k = -1). Stejnì tak bychom mohli vykrátit vektor (5; 10) na (1; 2). Z toho plyne, ¾e jedna pøímka mù¾e být k-násobkem druhé - viz. dále.</p>

<p class="src0">WPrimka2D::WPrimka2D(double x1, double y1, double x2, double y2)<span class="kom">// Pøímka ze dvou bodù</span></p>
<p class="src0">{</p>
<p class="src1">Create(x1, y1, x2, y2);</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">void WPrimka2D::Create(double x1, double y1, double x2, double y2)</p>
<p class="src0">{</p>
<p class="src1">if(x1 == x2 &amp;&amp; y1 == y2)<span class="kom">// 2 stejné body netvoøí pøímku</span></p>
<p class="src1">{</p>
<p class="src2">Create(0.0, 0.0, 0.0);<span class="kom">// Platné hodnoty</span></p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">a = y2 - y1;</p>
<p class="src1">b = x1 - x2;</p>

<p>Promìnnou c vypoèteme dosazením jednoho bodu (v na¹em pøípadì prvního) do zatím neúplné rovnice. V základní rovnici a*x + b*y + c = 0 pøesuneme v¹echno kromì c na pravou stranu a získáme c = -a*x -b*y.</p>

<p class="src1">c = -a*x1 -b*y1;</p>
<p class="src0">}</p>

<p>Zda bod le¾í na pøímce, zjistíme dosazením jeho souøadnic do rovnice pøímky. Pokud se výsledek rovná nule, le¾í na ní.</p>

<p class="src0">bool WPrimka2D::JeNaPrimce(double x, double y)<span class="kom">// Le¾í bod na pøímce?</span></p>
<p class="src0">{</p>
<p class="src1">if(a*x + b*y + c == 0.0)<span class="kom">// Dosazení souøadnic do rovnice</span></p>
<p class="src1">{</p>
<p class="src2">return true;</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jestli jsou pøímky stejné (splývající) se zjistí porovnáním jejich slo¾ek, ale navíc musíme vzít v úvahu i k-násobky. Nebudeme tedy porovnávat pøímo vnitøní promìnné, ale místo toho vypoèteme pomìry a/a, b/b a c/c. Budou-li tyto pomìry vnitøních promìnných stejné, je jasné, ¾e se jedná se o jednu a tu samou pøímku.</p>

<p class="src0">bool WPrimka2D::operator==(WPrimka2D&amp; primka)<span class="kom">// Jsou pøímky splývající?</span></p>
<p class="src0">{</p>
<p class="src1">double ka = a / primka.a;<span class="kom">// Nestaèí pouze zkontrolovat hodnoty, primka mù¾e být k-násobkem</span></p>
<p class="src1">double kb = b / primka.b;</p>
<p class="src1">double kc = c / primka.c;</p>
<p class="src"></p>
<p class="src1">if(ka == kb &amp;&amp; ka == kc)<span class="kom">// Musí být stejné</span></p>
<p class="src1">{</p>
<p class="src2">return true;<span class="kom">// Splývající pøímky</span></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">return false;<span class="kom">// Dvì rùzné pøímky</span></p>
<p class="src1">}</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">bool WPrimka2D::operator!=(WPrimka2D&amp; primka)<span class="kom">// Nejsou pøímky splývající?</span></p>
<p class="src0">{</p>
<p class="src1">return !(*this == primka);<span class="kom">// Negace porovnání</span></p>
<p class="src0">}</p>

<p>Zji¹tìní, jestli jsou pøímky rovnobì¾né, je velmi podobné operátoru porovnání. Mají-li stejný normálový vektor, popø. vektor jedné je k-násobkem druhé, jsou rovnobì¾né. Tøetí promìnnou, c, nemusíme a vlastnì ani nesmíme testovat.</p>

<p class="src0">bool WPrimka2D::JsouRovnobezne(WPrimka2D&amp; primka)<span class="kom">// Jsou pøímky rovnobì¾né?</span></p>
<p class="src0">{</p>
<p class="src1">double ka = a / primka.a;<span class="kom">// Nestaèí zkontrolovat hodnoty, p mù¾e být k-násobkem</span></p>
<p class="src1">double kb = b / primka.b;</p>
<p class="src"></p>
<p class="src1">if(ka == kb)<span class="kom">// Musí být stejné</span></p>
<p class="src1">{</p>
<p class="src2">return true;</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Kolmost dvou pøímek se nejjednodu¹eji odhalí tak, ¾e se jedna z nich natoèí o 90 stupòù a otestuje se jejich rovnobì¾nost - proè to zbyteènì komplikovat...</p>

<p class="src0">bool WPrimka2D::JsouKolme(WPrimka2D&amp; primka)<span class="kom">// Jsou pøímky kolmé?</span></p>
<p class="src0">{</p>
<p class="src1">WPrimka2D pom(-primka.b, primka.a, primka.c);<span class="kom">// Pøímka s kolmým vektorem</span></p>
<p class="src"></p>
<p class="src1">return JsouRovnobezne(pom);</p>
<p class="src0">}</p>

<p>Dostáváme se k podstatì celého èlánku - prùseèík dvou pøímek. Nejdøíve otestujeme jestli se nejedná o dvì splývající pøímky, pokud ano, mají nekoneènì mnoho spoleèných bodù. Nejsou-li splývající, mohou být je¹tì rovnobì¾né, pak nemají ¾ádný spoleèný bod. Ve v¹ech ostatních pøípadech mají pouze jeden spoleèný bod a tím je prùseèík. Proto¾e musí vyhovovat souèasnì obìma rovnicím, øe¹íme soustavu dvou rovnic o dvou neznámých x a y.</p>

<div class="okolo_img"><img src="images/clanky/primka2d.gif" width="175" height="153" alt="Výpoèet prùseèíku" /></div>

<p>Pokud funkce vrátí true, byl prùseèík nalezen, souøadnice ulo¾íme do referencí retx a rety. False indikuje buï ¾ádný prùseèík (rovnobì¾né pøímky), nebo nekoneènì mnoho spoleèných bodù (splývající pøímky).</p>

<p class="src0">bool WPrimka2D::Prusecik(WPrimka2D&amp; primka, double&amp; retx, double&amp; rety)<span class="kom">// Prùseèík pøímek</span></p>
<p class="src0">{</p>
<p class="src1">if(*this == primka)<span class="kom">// Pøímky jsou splývající - nekoneènì mnoho spoleèných bodù</span></p>
<p class="src1">{</p>
<p class="src2">return false;<span class="kom">// Spí¹e by se mìlo vrátit true a nìjaký bod... zále¾í na pou¾ití</span></p>
<p class="src1">}</p>
<p class="src1">else if(JsouRovnobezne(primka))<span class="kom">// Pøímky jsou rovnobì¾né - ¾ádný spoleèný bod</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Jeden spoleèný bod - prùseèík (vyhovuje souèasnì obìma rovnicím)</span></p>
<p class="src1">{</p>
<p class="src2">retx = (b*primka.c - c * primka.b) / (a*primka.b - primka.a*b);</p>
<p class="src2">rety = -(a*primka.c - primka.a * c) / (a*primka.b -  primka.a*b);</p>
<p class="src"></p>
<p class="src2">return true;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Úhel dvou pøímek je úhlem dvou smìrových vektorù, mù¾eme v¹ak pou¾ít i normálové vektory, proto¾e výsledek bude stejný. Kosinus úhlu se rovná zlomku, u kterého se v èitateli nachází skalární souèin vektorù (násobí se zvlá¹» x a zvlá¹» y slo¾ky) a ve jmenovateli souèin délek vektorù (Pythagorova vìta). Pokud nechápete, berte to jako vzorec.</p>

<p class="src0">double WPrimka2D::Uhel(WPrimka2D&amp; primka)<span class="kom">// Úhel pøímek</span></p>
<p class="src0">{</p>
<p class="src1">return acos((a*primka.a + b*primka.b) / (sqrt(a*a + b*b) * sqrt(primka.a*primka.a + primka.b*primka.b)));</p>
<p class="src0">}</p>

<p>Vzdálenost bodu od pøímky je u¾ trochu slo¾itìj¹í. Vypoète se rovnice pøímky, která je kolmá k zadané pøímce a prochází urèeným bodem. Potom se najde prùseèík tìchto pøímek a vypoète se vzdálenost bodù. Celý tento postup se ale dá mnohonásobnì zjednodu¹it, kdy¾ si najdete vzorec v matematicko fyzikálních tabulkách :-)</p>

<p class="src0">double WPrimka2D::VzdalenostBodu(double x, double y)<span class="kom">// Vzdálenost bodu od pøímky</span></p>
<p class="src0">{</p>
<p class="src1">double vzdalenost = (a*x + b*y + c) / sqrt(a*a + b*b);</p>
<p class="src"></p>
<p class="src1">if(vzdalenost &lt; 0.0)<span class="kom">// Absolutní hodnota</span></p>
<p class="src1">{</p>
<p class="src2">vzdalenost = -vzdalenost;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return vzdalenost;</p>
<p class="src0">}</p>

<p>Abych se ale vrátil na zaèátek, pùvodním zámìrem bylo vypoèítat prùseèík dvou pøímek. S na¹í tøídou to není nic slo¾itého...</p>

<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Knihovna pro standardní vstup a výstup</span></p>
<p class="src0">#include &quot;primka2d.h&quot;<span class="kom">// Tøída pøímky</span></p>
<p class="src"></p>
<p class="src0">int main(int argc, char** argv)<span class="kom">// Vstup do programu</span></p>
<p class="src0">{</p>
<p class="src1">WPrimka2D primka1(3.0, -1.0, 1.0);<span class="kom">// Dvì pøímky</span></p>
<p class="src1">WPrimka2D primka2(1.0, 3.0, -14.0);</p>
<p class="src"></p>
<p class="src1">double prusecik_x, prusecik_y;<span class="kom">// Souøadnice prùseèíku</span></p>
<p class="src"></p>
<p class="src1">if(primka1.Prusecik(primka2, prusecik_x, prusecik_y))<span class="kom">// Výpoèet prùseèíku</span></p>
<p class="src1">{</p>
<p class="src2">printf(&quot;Prùseèík [%f; %f]\n&quot;, prusecik_x, prusecik_y);<span class="kom">// Vypsání hodnot</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return 0;</p>
<p class="src0">}</p>

<p>Doufám, ¾e se vám tento èlánek líbil. Pokud bude zájem (napi¹te napø. do Diskuze k tomuto èlánku), mohu vytvoøit nìco podobného o pøímce ve 3D. Tam ale bode situace o trochu komplikovanìj¹í, proto¾e v trojrozmìrném prostoru obecná rovnice pøímky neexistuje. Budeme si muset vystaèit se soustavou parametrických rovnic.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/primka2d.tar.gz');?> - Linuxové g++ OK, jiné kompilátory by mìli být také pou¾itelné</li>
</ul>

<?
include 'p_end.php';
?>
