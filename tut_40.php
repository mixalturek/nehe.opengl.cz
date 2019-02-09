<?
$g_title = 'CZ NeHe OpenGL - Lekce 40 - Fyzikální simulace lana';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(40);?>

<h1>Lekce 40 - Fyzikální simulace lana</h1>

<p class="nadpis_clanku">Pøichází druhá èást dvoudílné série o fyzikálních simulacích. Základy u¾ známe, a proto se pustíme do komplikovanìj¹ího úkolu - klávesnicí ovládat pohyby simulovaného lana. Zatáhneme-li za horní konec, prostøední èást se rozhoupe a spodek se vláèí po zemi. Skvìlý efekt.</p>

<p>Celé demo zalo¾íme na jednoduchém fyzikálním enginu z lekce 39. Nyní u¾ byste mìli umìt aplikovat libovolné síly na jakýkoli hmotný objekt, pøepoèítat jeho novou pozici i rychlost a samozøejmì provádìt operace s 3D vektory. Pokud nìèemu z toho nerozumíte, vra»te se k minulé lekci, popø. zkuste jiné zdroje.</p>

<p>Pøedpokladem pro fyzikální simulace je implementace fyzikálních podmínek a závislostí, kdy se sna¾íme o to, aby v¹e vypadalo jako v reálném prostøedí. Nejvíce na oèích bývá v¾dy dynamika - pohybové reakce objektù na u¾ivatelovy pøíkazy. Právì na nich hodnotí, zda se na¹e práce podaøila, èi ne. Na úplném zaèátku se v¾dy musí najít vhodný kompromis mezi rychlostí a kvalitou. Jsme schopni v¹imnout si atomù, elektronù nebo protonù? Ne. Urèitì bude staèit aproximace pohybu skupiny èástic.</p>

<h2>Matematika pohybù</h2>

<p>Klasická mechanika reprezentuje pøedmìty jako èástice v prostoru, které mají urèitou hmotnost. Jejich zrychlení závisí na pùsobících silách a pozice na uplynulém èase od minulých výpoètù. Mù¾eme ji pou¾ít k simulaci chování objektù, které jsou viditelné prostým okem (pro mikrosvìt platí jiné fyzikální zákony). V lekci 39 jsme s její pomocí implementovali pohyb v gravitaci a objekt zavì¹ený na pru¾inì. Nyní zkusíme simulovat slo¾itìj¹í pøedmìt - lano.</p>

<h2>Výkon poèítaèe, který pou¾íváme k simulaci</h2>

<p>Rychlost poèítaèe je hlavním omezením pro mno¾ství detailù simulace. Napøíklad pøi simulaci chodícího èlovìka eliminujeme na pomalém poèítaèi pohyb prstù na nohou, které jsou sice dùle¾ité, ale výsledek vypadá pøesvìdèivì i bez nich. Musíme je vynechat z jednoduchého dùvodu: poèítaè by nestíhal provádìt potøebné výpoèty, kterých je i bez nich a¾ pøíli¹.</p>

<p>Jako minimální po¾adavek pro simulaci urèíme poèítaè s frekvencí procesoru kolem 500 MHz. Z toho plyne omezení poètu detailù. Pøi implementaci pou¾ijeme knihovnu Physics1.h z lekce 39. Tato knihovna obsahuje tøídu Mass (hmota), která reprezentuje jeden hmotný bod. Spojením nìkolika za sebe získáme fyzikální model, který reprezentuje lano. Mù¾eme usoudit, ¾e se bude kývat a rùznì vlát, ale nebude moci krou¾it, proto¾e krou¾ení nejde pomocí hmotných bodù implementovat (nemohou rotovat okolo os). Urèíme si, ¾e body spojíme po vzdálenostech 10 cm. Tato hodnota vychází z toho, ¾e kvùli rychlosti pou¾ijeme maximálnì 50 a¾ 100 èástic na 3 a¾ 4 metrové lano. Z toho plynou 3 a¾ 8 cm velké mezery, co¾ bude je¹tì vìt¹í pøesnost, ne¾ jsme pùvodnì zamý¹leli.</p>

<h2>Odvození rovnice pohybu</h2>

<p>Rovnice pohybu je v matematice vyjádøena diferenciální rovnicí druhého stupnì. V modelu lana si mù¾eme ka¾dé dvì sousední èástice, ze kterých je slo¾eno, pøedstavit jako konce jedné pru¾iny. Znak o pøedstavuje èástici a pomlèka pru¾inu.</p>

<p class="src0"><span class="kom">o----o----o----o</span></p>

<p>První èástice poutá druhou, ta zase tøetí, ta ètvrtou atd. Vzniká jakýsi øetìzec, slo¾ený ze ètyø èástic a tøí pru¾in. Pru¾iny pøedstavují zdroje síly mezi ka¾dými dvìma èásticemi. Zapamatujte si, ¾e síla pru¾iny se dá vyjádøit takto:</p>

<p class="src0"><span class="kom">síla = -k * x</span></p>
<p class="src0"><span class="kom">k: konstanta urèující tuhost pru¾iny</span></p>
<p class="src0"><span class="kom">x: vzdálenost mezi body pru¾iny</span></p>

<p>Kdybychom pou¾ili tuto rovnici, lano by se za chvíli smr¹tilo, proto¾e z rovnice vyplývá, ¾e dokud není vzdálenost èástic nulová, pùsobí na nì síla. V¹echny by tíhly k ostatním a to nechceme. Pøedstavte si lano polo¾ené na stole. Chceme, aby to na¹e mìlo stejnou pevnost a tudí¾ musíme explicitnì udr¾ovat jeho délku konstantní. Abychom toho dosáhli, musí být pøi urèité kladné vzdálenosti síla pru¾iny nulová. Nic tì¾kého:</p>

<p class="src0"><span class="kom">síla = -k * (x - d)</span></p>
<p class="src0"><span class="kom">k: konstanta urèující tuhost pru¾iny</span></p>
<p class="src0"><span class="kom">x: vzdálenost mezi body pru¾iny</span></p>
<p class="src0"><span class="kom">d: konstanta oznaèující kladnou vzdálenost èástic, pøi které pru¾ina zùstane ve stálé poloze</span></p>

<p>Z rovnice vyplývá, ¾e pokud se bude vzdálenost mezi èásticemi rovnat konstantì d, nebudou aplikovány ¾ádné síly. Definovali jsme si lano slo¾ené ze sta èástic. Zvolíme-li d = 5 cm (0,05 metrù), získáme pevné pìtimetrové lano. Pokud bude x vìt¹í ne¾ d, pru¾ina se zaène natahovat a pøi men¹ím x naopak smr¹»ovat. Ka¾dopádnì se bude neustále nacházet v blízkosti bodu rovnováhy.</p>

<p>Máme zaji¹tìn celkem slu¹ný pohyb, ale nìco mu schází. A to nìco jsou ztráty - napìtí vláken, jejich tøení a podobnì. Beze ztrát si fyzikální systém uchovává ve¹kerou energii, kterou mu dodáme - lano se nikdy nepøestane houpat. Ne¾ se zaèneme ztrátám vìnovat, pojïme se nejprve podívat na kód.</p>

<h2>Tøída pru¾iny</h2>

<p>Tøída pru¾iny (anglicky spring) popisuje dvì èástice a silové pùsobení pru¾iny na ka¾dou z nich.</p>

<p class="src0">class Spring<span class="kom">// Tøída pru¾iny</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">Mass* mass1;<span class="kom">// Èástice na prvním konci pru¾iny</span></p>
<p class="src1">Mass* mass2;<span class="kom">// Èástice na druhém konci pru¾iny</span></p>
<p class="src"></p>
<p class="src1">float springConstant;<span class="kom">// Konstanta tuhosti pru¾iny</span></p>
<p class="src1">float springLength;<span class="kom">// Délka, pøi které nepùsobí ¾ádné síly</span></p>
<p class="src1">float frictionConstant;<span class="kom">// Konstanta vnitøního tøení</span></p>

<p>V konstruktoru nastavíme vnitøní datové èleny na hodnoty, které byly pøedány v parametrech.</p>

<p class="src1">Spring(Mass* mass1, Mass* mass2, float springConstant, float springLength, float frictionConstant)<span class="kom">// Konstruktor</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nastavení èlenských promìnných</span></p>
<p class="src2">this-&gt;springConstant = springConstant;</p>
<p class="src2">this-&gt;springLength = springLength;</p>
<p class="src2">this-&gt;frictionConstant = frictionConstant;</p>
<p class="src"></p>
<p class="src2">this-&gt;mass1 = mass1;</p>
<p class="src2">this-&gt;mass2 = mass2;</p>
<p class="src1">}</p>

<p>Nejdùle¾itìj¹í èástí tøídy je metoda solve(), ve které se aplikují síly. Za èíslo x se má dosadit vzdálenost mezi okrajovými body, v na¹em pøípadì se jedná o délku 3D vektoru, kterou vypoèteme odeètením pozic bodù.</p>

<p class="src1">void solve()<span class="kom">// Aplikování sil na èástice</span></p>
<p class="src1">{</p>
<p class="src2">Vector3D springVector = mass1-&gt;pos - mass2-&gt;pos;<span class="kom">// Vektor mezi èásticemi</span></p>
<p class="src"></p>
<p class="src2">float r = springVector.length();<span class="kom">// Vzdálenost èástic</span></p>

<p>Vytvoøíme dal¹í vektor, který bude oznaèovat výslednou sílu. Konstruktor ji automaticky vynuluje. Proto¾e v dal¹í èásti dìlíme, musíme o¹etøit, jestli se èíslo r nerovná nule. Pokud je v¹e v poøádku, mù¾eme pøistoupit k výpoètu.</p>

<p class="src2">Vector3D force;<span class="kom">// Pomocný vektor síly</span></p>
<p class="src"></p>
<p class="src2">if (r != 0)<span class="kom">// Proti dìlení nulou</span></p>
<p class="src2">{</p>

<p>Chceme-li dosáhnout rovnice uvedené vý¹e, potøebujeme získat jednotkový vektor, který reprezentuje smìr pùsobení síly. Defakto ho u¾ máme ulo¾en v objektu springVector, ale je v nìm navíc zapoèítána i jeho délka a to nechceme. Dá se v¹ak velice jednodu¹e odstranit dìlením (springVector / r). Dále se pokusíme implementovat èást (x - d). Máme jak vzdálenost bodù, tak délku pru¾iny, nic nám proto nebrání, abychom je odeèetli (r - springLength). Koneèný výsledek je¹tì vynásobíme tuhostí pru¾iny. Záporná hodnota oznaèuje, ¾e se lano bude spí¹e vléci ne¾ odrá¾et.</p>

<p class="src3">force += (springVector / r) * (r - springLength) * (-springConstant);<span class="kom">// Výpoèet síly</span></p>
<p class="src2">}</p>

<p>Vyøe¹ili jsme silové pùsobení pru¾iny, ale je¹tì nám chybí ztráty energie v materiálu. Pokud se na hmotu aplikuje síla v opaèném smìru, ne¾ se pohybuje, zpomalí. Kam se ztratila pohybová energie? Mohla se napøíklad pøemìnit ve tøení a následnì v tepelnou energii.</p>

<p class="src0"><span class="kom">tøecí síla = -k * rychlost</span></p>
<p class="src0"><span class="kom">k: konstanta pøedstavující velikost ztrát (napø. v závislosti na drsnosti povrchu)</span></p>
<p class="src0"><span class="kom">rychlost: rychlost hmoty, na kterou pùsobí tøecí síla</span></p>

<p>Tato rovnice by se dala napsat i jinak (více slo¾itì), ale nám bude tato verze bohatì staèit. V¹imnìte si, ¾e lze dosadit pouze rychlost jednoho bodu, ale pru¾ina se skládá ze dvou. Co dìlat? Vypoèteme rozdíl rychlostí a pøedáme ho jako relativní rychlost. Ztráty tedy budou pøedstavovat vnitøní tøení v materiálu.</p>

<p class="src2">force += -(mass1-&gt;vel - mass2-&gt;vel) * frictionConstant;<span class="kom">// Zmen¹ení síly o tøení</span></p>

<p>Podle Newtonova zákona akce a reakce aplikujeme na jeden bod pru¾iny kladnou sílu a na druhý zápornou. (Pùsobí-li jedno tìleso na druhé silou, pùsobí i druhé na první. Obì síly jsou stejnì velké, ale opaènì orientované.) Pøedstavte si dvì loïky na jezeøe. Po odstrèení se nezaène pohybovat jenom jedna, ale obì. Pokud by bylo jedno tìleso mnohonásobnì tì¾¹í ne¾ to druhé, mohlo by se silové pùsobení na nìj zanedbat, jeho zrychlení by se blí¾ilo k nule. Pøedstavte si napøíklad raketu v gravitaèním poli planety, která je pøitahována dolù do jejího støedu. Raketa se zároveò sna¾í pøitáhnout planetu nahoru, nicménì nemá nejmen¹í ¹anci :-) a tak se druhé pùsobení jednodu¹e zanedbává. To ale není ná¹ pøípad, proto¾e oba na¹e objekty mají stejnou hmotnost.</p>

<p class="src2">mass1-&gt;applyForce(force);<span class="kom">// Aplikování síly na èástici 1</span></p>
<p class="src2">mass2-&gt;applyForce(-force);<span class="kom">// Aplikování opaèné síly na èástici 2</span></p>
<p class="src1">}</p>
<p class="src0">};</p>

<p>Nyní u¾ máme vyøe¹enu rovnici pohybu, kterou si mù¾eme pøedstavit jako silové pùsobení pru¾in. Abychom simulaci dokompletovali, pøidáme je¹tì gravitaèní sílu, tøení lana se vzduchem a plochý povrch, po kterém lanem posunujeme. První dvì jsou jednoduché, nejprve odpor vzduchu:</p>

<p class="src0"><span class="kom">odpor vzduchu = -k * rychlost</span></p>
<p class="src0"><span class="kom">k: konstanta, která pøedstavuje velikost odporu</span></p>
<p class="src0"><span class="kom">rychlost: rychlost pohybu</span></p>

<p>A gravitace...</p>

<p class="src0"><span class="kom">gravitaèní síla = gravitaèní zrychlení * hmotnost</span></p>

<p>Gravitace i odpor vzduchu pùsobí na ka¾dou èástici lana zvlá¹». Co se zemí? Mù¾eme si vytvoøit pomyslnou rovinu a testovat, kolize s èásticemi lana. Pokud se ocitne pod úrovní roviny, vyvý¹íme ji a roz¹íøíme pùsobící sílu o tøení s podlahou.</p>

<h2>Nastavení poèáteèních hodnot simulace</h2>

<p>V tuto chvíli je prostøedí pøipraveno pro simulaci, ale potøebujeme definovat jednotky pou¾ívaných fyzikálních velièin. Vzdálenost bude specifikována v metrech, èas v sekundách a hmotnost v kilogramech. Gravitaci nastavíme tak, aby pùsobila ve smìru záporné èásti osy y se zrychlením 9,81 m*s<sup>-2</sup> (odpovídá gravitaci na zemi). Pøed spu¹tìním umístíme lano rovnobì¾nì se zemí ve vzdálenosti ètyø metrù od ní. Aby se jí mohlo dotknout bude mít délku (v klidovém stavu) také ètyøi metry, Z toho vyplývá 5 cm vzdálenost mezi jednotlivými èásticemi (4 m / 80 èástic = 0,05 m = 5 cm). Normální délku pru¾iny mezi èásticemi (délka bez pùsobení ¾ádných sil) tedy nastavíme na tìchto 5 cm, aby lano na zaèátku simulace nebylo ani napnuté ani prohnuté. Celkovou hmotnost lana urèíme na 4 kg a to dává 0,05 kg (= 50 gramù) na ka¾dou èástici. Pro pøehlednost si v¹e shrneme:</p>

<ul>
<li>gravitaèní zrychlení: 9,81 m*s<sup>-2</sup></li>
<li>poèet èástic lana: 80</li>
<li>normální vzdálenost mezi sousedními èásticemi (bez pùsobení sil): 5 cm</li>
<li>hmotnost jedné èástice: 50 gramù</li>
<li>poèáteèní orientace lana: horizontálnì bez napìtí</li>
</ul>

<p>Nyní zkusíme vypoèítat konstantu urèující tuhost pru¾iny. Pokud budeme dr¾et lano za jeden konec, tak se natáhne. Pøedstavte si elastické lano se záva¾ím. Je to úplnì stejné, akorát my nemáme pouze jednu pru¾inu ale hned nìkolik. Nejvíce se natáhne horní pru¾ina, proto¾e dr¾í hmotnost v¹ech ostatních èástic - prakticky celé lano. Naopak nejménì se prodlou¾í ta spodní, proto¾e je na ní zavì¹ena jenom jedna jediná èástice. Nechceme, aby se ta horní natáhla více ne¾ o 1 cm.</p>

<p class="src0"><span class="kom">f = hmotnost lana * gravitaèní zrychlení = (4 * 9,81) N ~= 40 N</span></p>

<p>Síla pru¾iny odpovídá pøibli¾nì 40 N. Dá se ale vyjádøit i jinak:</p>

<p class="src0"><span class="kom">síla pru¾iny = -k * x = -k * 0,1 metrù</span></p>

<p>Suma tìchto sil by se mìla rovnat nule.</p>

<p class="src0"><span class="kom">40 N + (-k * 0,01 metrù) = 0</span></p>

<p>Vypoèteme a získáme</p>

<p class="src0"><span class="kom">k = 4000 N/m</span></p>

<p>Pro snadnìj¹í zapamatovatelnost budeme pøedpokládat 10000 N/m, co¾ nám dává vìt¹í tuhost lana, které se v horní èásti natáhne jen o 4 mm.</p>

<p>Aby se nalezla konstanta vnitøního tøení v lanì, museli bychom podniknout mnohem více komplikovanìj¹í výpoèet ne¾ je ten vý¹e. Proto jsem pou¾il metodu pokusù a omylù a získal...</p>

<p class="src0"><span class="kom">konstanta vnitøního tøení = 0,2 N/(m/s)</span></p>

<p>... co¾ vypadá celkem realisticky.</p>

<h2>Tøída simulace lana</h2>

<p>Pøedtím ne¾ zaèneme zkoumat tøení se vzduchem a se zemí, pojïme se podívat na tøídu simulace lana, která je odvozená od obecné tøídy simulace z lekce 39. Tato tøída obsahuje ètyøi metody potøebné pro bìh simulace.</p>

<ul>
<li>virtual void init() - reset sil</li>
<li>virtual void solve() - aplikování sil</li>
<li>virtual void simulate() - iterování pozic a rychlostí</li>
<li>virtual void operate(float dt) - kompletní simulaèní metoda</li>
</ul>

<p>V potomku základní tøídy pøepí¹eme funkci solve() a funkci simulate(float dt), proto¾e kvùli lanu potøebujeme jejich speciální implementaci. Solve() slou¾í k aplikování sil a simulate(float dt) k ovládání lana za jeden konec povì¹ený v prostoru. Jak u¾ bylo øeèeno, tøída RopeSimulation je potomkem tøídy Simulation. Obecnou simulaci roz¹iøuje o lano slo¾ené z hmotných bodù (èástic) pospojovaných pru¾inami. Tyto pru¾iny mají vnitøní tøení a klidovou délku. Jeden konec lana je udr¾ován v prostoru na souøadnicích ropeConnectionPos a je jím mo¾no pohybovat pomocí metody setRopeConnectionVel(Vector3D ropeConnectionVel). Tøída dále zaji¹»uje tøení se vzduchem a s rovinným povrchem (nebo-li se zemí), jeho¾ normála z nìj vychází ve smìru kladné èásti osy y.</p>

<p class="src0">class RopeSimulation : public Simulation<span class="kom">// Tøída simulace lana</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">Spring** springs;<span class="kom">// Pru¾iny spojující èástice</span></p>
<p class="src"></p>
<p class="src1">Vector3D gravitation;<span class="kom">// Gravitaèní zrychlení</span></p>
<p class="src1">Vector3D ropeConnectionPos;<span class="kom">// Bod v prostoru; pozice první èástice pro ovládání lanem</span></p>
<p class="src1">Vector3D ropeConnectionVel;<span class="kom">// Rychlost a smìr po¾adovaného pohybu</span></p>
<p class="src"></p>
<p class="src1">float groundRepulsionConstant;<span class="kom">// Velikost odrá¾ení èástic od zemì</span></p>
<p class="src1">float groundFrictionConstant;<span class="kom">// Velikost tøení èástic se zemí</span></p>
<p class="src1">float groundAbsorptionConstant;<span class="kom">// Velikost absorpce sil èástic zemí (vertikální kolize)</span></p>
<p class="src1">float groundHeight;<span class="kom">// Pozice roviny zemì na ose y</span></p>
<p class="src1">float airFrictionConstant;<span class="kom">// Konstanta odporu vzduchu na èástice</span></p>

<p>Konstruktorem inicializujeme v¹echny èlenské promìnné tøídy, alokujeme pamì» pro v¹echny potøebné pru¾iny a umístíme je v øadì rovnobì¾nì se zemí.</p>

<p class="src1">RopeSimulation(<span class="kom">// Konstruktor tøídy</span></p>
<p class="src2">int numOfMasses,<span class="kom">// Poèet èástic</span></p>
<p class="src2">float m,<span class="kom">// Hmotnost ka¾dé èástice</span></p>
<p class="src2">float springConstant,<span class="kom">// Tuhost pru¾iny</span></p>
<p class="src2">float springLength,<span class="kom">// Délka pru¾iny v klidovém stavu</span></p>
<p class="src2">float springFrictionConstant,<span class="kom">// Konstanta vnitøního tøení pru¾iny</span></p>
<p class="src2">Vector3D gravitation,<span class="kom">// Gravitaèní zrychlení</span></p>
<p class="src2">float airFrictionConstant,<span class="kom">// Odpor vzduchu</span></p>
<p class="src2">float groundRepulsionConstant,<span class="kom">// Odrá¾ení èástic zemí</span></p>
<p class="src2">float groundFrictionConstant,<span class="kom">// Tøení èástic se zemí</span></p>
<p class="src2">float groundAbsorptionConstant,<span class="kom">// Absorpce sil zemí</span></p>
<p class="src2">float groundHeight<span class="kom">// Pozice zemì na ose y</span></p>
<p class="src2">) : Simulation(numOfMasses, m)<span class="kom">// Inicializace pøedka tøídy</span></p>
<p class="src1">{</p>
<p class="src2">this-&gt;gravitation = gravitation;</p>
<p class="src2"></p>
<p class="src2">this-&gt;airFrictionConstant = airFrictionConstant;</p>
<p class="src"></p>
<p class="src2">this-&gt;groundFrictionConstant = groundFrictionConstant;</p>
<p class="src2">this-&gt;groundRepulsionConstant = groundRepulsionConstant;</p>
<p class="src2">this-&gt;groundAbsorptionConstant = groundAbsorptionConstant;</p>
<p class="src2">this-&gt;groundHeight = groundHeight;</p>
<p class="src"></p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Nastavení poèáteèní pozice èástic</span></p>
<p class="src2">{</p>
<p class="src3">masses[a]-&gt;pos.x = a * springLength;<span class="kom">// Offsety jednotlivých èástic</span></p>
<p class="src3">masses[a]-&gt;pos.y = 0;<span class="kom">// Rovnobì¾nì se zemí</span></p>
<p class="src3">masses[a]-&gt;pos.z = 0;<span class="kom">// Rovnobì¾nì s obrazovkou</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">springs = new Spring*[numOfMasses - 1];<span class="kom">// Alokace pamìti pro ukazatele na pru¾iny</span></p>
<p class="src"></p>
<p class="src2">for (a = 0; a &lt; numOfMasses - 1; ++a)<span class="kom">// Vytvoøení jednotlivých pru¾in</span></p>
<p class="src2">{</p>
<p class="src3"></p>
<p class="src3">springs[a] = new Spring(masses[a], masses[a + 1], springConstant, springLength, springFrictionConstant);<span class="kom">// Dvì èástice na pru¾inu</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Jak jsme si u¾ øekli vý¹e, funkce solve() slou¾í k aplikování sil na v¹echny objekty, ze kterých je lano slo¾eno.</p>

<p class="src1">void solve()<span class="kom">// Aplikování sil</span></p>
<p class="src1">{</p>

<p>Nejdøíve o¹etøíme v¹echny pru¾iny; na jejich poøadí nezále¾í. Tøída  obsahuje svoji vlastní funkci.</p>

<p class="src2">for (int a = 0; a &lt; numOfMasses - 1; ++a)<span class="kom">// Prochází pru¾iny</span></p>
<p class="src2">{</p>
<p class="src3">springs[a]-&gt;solve();<span class="kom">// Aplikování sil na pru¾inu</span></p>
<p class="src2">}</p>

<p>V cyklu pøes v¹echny èástice zajistíme pùsobení gravitace a odpor vzduchu.</p>

<p class="src2">for (a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Prochází èástice</span></p>
<p class="src2">{</p>
<p class="src3">masses[a]-&gt;applyForce(gravitation * masses[a]-&gt;m);<span class="kom">// Gravitace</span></p>
<p class="src3">masses[a]-&gt;applyForce(-masses[a]-&gt;vel * airFrictionConstant);<span class="kom">// Odpor vzduchu</span></p>

<p>Síly ze zemì vypadají trochu komplikovanìji, ale jsou právì tak jednoduché, jako v¹echny ostatní. Zemì na èástici mù¾e pùsobit pouze tehdy, pokud se navzájem dotknou, tj. èástice se na ose y nachází pod její úrovní.</p>

<p class="src3">if (masses[a]-&gt;pos.y &lt; groundHeight)<span class="kom">// Kolize se zemí,</span></p>
<p class="src3">{</p>

<p>Smýkání lana na zemi je zaji¹tìno tøecí silou, která ze své podstaty zanedbává rychlost na ose y. Y je smìr, kterým zemì (její normálový vektor) smìøuje vzhùru; smýkání nemù¾e pùsobit v tomto smìru.</p>

<p class="src4">Vector3D v;<span class="kom">// Pomocný vektor</span></p>
<p class="src"></p>
<p class="src4">v = masses[a]-&gt;vel;<span class="kom">// Grabování rychlosti</span></p>
<p class="src4">v.y = 0;<span class="kom">// Vynechání rychlosti na ose y</span></p>
<p class="src"></p>
<p class="src4">masses[a]-&gt;applyForce(-v * groundFrictionConstant);<span class="kom">// Tøecí síla zemì</span></p>

<p>Opakem smýkání je absorpèní efekt, kdy se síla aplikuje pouze ve smìru, kterým zemì smìøuje vzhùru. Proto obì ostatní slo¾ky vynulujeme. Absorpce nemù¾e na èástici pùsobit tehdy, kdy¾ se vzdaluje od zemì. Pokud bychom nepøidali podmínku v.y &lt; 0, lano by tíhlo k zemi, i kdy¾ by se jí u¾ nedotýkalo.</p>

<p class="src4">v = masses[a]-&gt;vel;<span class="kom">// Grabování rychlosti</span></p>
<p class="src"></p>
<p class="src4">v.x = 0;<span class="kom">// Zanedbání rychlosti na osách x a z</span></p>
<p class="src4">v.z = 0;</p>
<p class="src"></p>
<p class="src4">if (v.y &lt; 0)<span class="kom">// Pouze pøi kolizi smìrem k zemi</span></p>
<p class="src4">{</p>
<p class="src5">masses[a]-&gt;applyForce(-v * groundAbsorptionConstant);<span class="kom">//Absorpèní síla</span></p>
<p class="src4">}</p>

<p>Síla odrazu je poslední ze sil, kterou vyvolává kolize se zemí. Zemì odrá¾í èástice právì tak, jako kdyby se mezi nimi nacházela pru¾ina. Její síla je pøímo úmìrná rychlosti èástice pøi nárazu.</p>

<p class="src4">Vector3D force = Vector3D(0, groundRepulsionConstant, 0) * (groundHeight - masses[a]-&gt;pos.y);<span class="kom">// Síla odrazu</span></p>
<p class="src"></p>
<p class="src4">masses[a]-&gt;applyForce(force);<span class="kom">// Aplikování síly odrazu</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Abychom vyvolali dojem média, které za jeden konec dr¾í lano a pohybuje s ním, musíme pøepsat metodu simulate(float dt).</p>

<p class="src1">void simulate(float dt)<span class="kom">// Simulace lana</span></p>
<p class="src1">{</p>

<p>Nejdøíve ze v¹eho zavoláme metodu pøedka, potom k aktuální pozici pøièteme rychlost pohybu a nakonec o¹etøíme náraz do zemì.</p>

<p class="src2">Simulation::simulate(dt);<span class="kom">// Metoda pøedka</span></p>
<p class="src"></p>
<p class="src2">ropeConnectionPos += ropeConnectionVel * dt;<span class="kom">// Zvìt¹ení pozice o rychlost</span></p>
<p class="src"></p>
<p class="src2">if (ropeConnectionPos.y &lt; groundHeight)<span class="kom">// Dostala se èástice pod zem?</span></p>
<p class="src2">{</p>
<p class="src3">ropeConnectionPos.y = groundHeight;<span class="kom">// Pøesunutí na úroveò zemì</span></p>
<p class="src3">ropeConnectionVel.y = 0;<span class="kom">// Nulování rychlosti na ose y</span></p>
<p class="src2">}</p>

<p>Pomocí právì získaných parametrù nastavíme vlastnosti èástice na indexu nula.</p>

<p class="src2">masses[0]-&gt;pos = ropeConnectionPos;<span class="kom">// Pozice první èástice</span></p>
<p class="src2">masses[0]-&gt;vel = ropeConnectionVel;<span class="kom">// Rychlost první èástice</span></p>
<p class="src2">}</p>

<p>Potøebujeme funkci, pomocí které budeme moci nastavit rychlost první èástice.</p>

<p class="src1">void setRopeConnectionVel(Vector3D ropeConnectionVel)<span class="kom">// Nastavení rychlosti první èástice</span></p>
<p class="src1">{</p>
<p class="src2">this-&gt;ropeConnectionVel = ropeConnectionVel;<span class="kom">// Pøiøazení rychlostí</span></p>
<p class="src1">}</p>

<p>Tím konèí vysvìtlování vnitøních závislostí tøídy RopeSimulation. Její objekt v aplikaci vytváøíme dynamicky pomocí operátoru new. Zmìnou hodnot pøedávaných konstruktoru mù¾ete docílit témìø libovolného chování lana. V¹imnìte si, ¾e zemi umis»ujeme -1.5f jednotek pod osou y. Lano inicializujeme na nule rovnobì¾nì se zemí. To nám tedy dává mo¾nost vidìt hned na zaèátku efektní pád a kolizi se zemí.</p>

<p class="src0">RopeSimulation* ropeSimulation = new RopeSimulation(<span class="kom">// Vytvoøení objektu simulace lana</span></p>
<p class="src1">80,<span class="kom">// 80 èástic</span></p>
<p class="src1">0.05f,<span class="kom">// Ka¾dá èástice vá¾í 50 gramù</span></p>
<p class="src1">10000.0f,<span class="kom">// Tuhost pru¾in</span></p>
<p class="src1">0.05f,<span class="kom">// Délka pru¾in, pøi nepùsobení ¾ádné sily</span></p>
<p class="src1">0.2f,<span class="kom">// Konstanta vnitøního tøení pru¾iny</span></p>
<p class="src1">Vector3D(0, -9.81f, 0), <span class="kom">// Gravitaèní zrychlení</span></p>
<p class="src1">0.02f,<span class="kom">// Odpor vzduchu</span></p>
<p class="src1">100.0f,<span class="kom">// Síla odrazu od zemì</span></p>
<p class="src1">0.2f,<span class="kom">// Tøecí síla zemì</span></p>
<p class="src1">2.0f,<span class="kom">// Absorpèní síla zemì</span></p>
<p class="src1">-1.5f);<span class="kom">// Poloha zemì na ose y</span></p>

<p>Stejnì jako v lekci 39 existuje maximální mo¾ná hodnota dt simulace. S vý¹e uvedenými parametry konstruktoru èiní pøibli¾nì 0,002 sekund. Pokud va¹e zmìna tuto hodnotu sní¾í, mù¾e simulace vypadat ponìkud nestabilnì a lano nemusí pracovat správnì. Abyste pomìry stabilizovali, musíte najít nové maximální mo¾né dt. Velké síly a/nebo malé hmotnosti znamenají vìt¹í nestabilitu, proto¾e zrychlení bude vy¹¹í (zrychlení = síla / hmotnost).</p>

<p class="src0"><span class="kom">// Funkce Update(DWORD milliseconds)</span></p>
<p class="src"></p>
<p class="src1">float dt = milliseconds / 1000.0f;<span class="kom">// Pøevod milisekund na sekundy</span></p>
<p class="src1">float maxPossible_dt = 0.002f;<span class="kom">// Maximální mo¾né dt</span></p>
<p class="src1">int numOfIterations = (int)(dt / maxPossible_dt) + 1;<span class="kom">// Výpoèet poètu opakování pøi této aktualizaci</span></p>
<p class="src"></p>
<p class="src1">if (numOfIterations != 0)<span class="kom">// Proti dìlení nulou</span></p>
<p class="src1">{</p>
<p class="src2">dt = dt / numOfIterations;<span class="kom">// Aktualizace dt podle numOfIterations</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">for (int a = 0; a &lt; numOfIterations; ++a)<span class="kom">// Opakování simulace</span></p>
<p class="src1">{</p>
<p class="src2">ropeSimulation-&gt;operate(dt);</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Lanem mù¾ete pohybovat pomocí ¹ipek a kláves HOME a END. Pohrajte si, stojí to za to. Simulaèní procedura hodnì závisí na výkonu procesoru, tudí¾ jsou také dùle¾ité optimalizace kompilátoru. Pøi standardním Visual C++ Release nastavení bì¾í program více ne¾ 10 krát rychleji ne¾ v Debug módu, pro který èiní minimální frekvence procesoru cca. 500 MHz.</p>

<p>V tomto tutoriálu je pøedstaveno kompletní fyzikální nastavení, teoretická funkce, design a implementace. Více pokroèilej¹í simulace uvnitø vypadají úplnì stejnì jako tato.</p>

<p class="autor">napsal: Erkin Tunca <?VypisEmail('erkintunca@icqmail.com');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson40.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson40_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson40.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson40.zip">Dev C++</a> kód této lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson40.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:mailto:cestarigianni@libero.it">Gianni Cestari</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson40.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(40);?>
<?FceNeHeOkolniLekce(40);?>

<?
include 'p_end.php';
?>
