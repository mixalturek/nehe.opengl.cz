<?
$g_title = 'CZ NeHe OpenGL - Lekce 30 - Detekce kolizí';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(30);?>

<h1>Lekce 30 - Detekce kolizí</h1>

<p class="nadpis_clanku">Na podobný tutoriál jste u¾ jistì netrpìlivì èekali. Nauèíte se základy o detekcích kolizí, jak na nì reagovat a na fyzice zalo¾ené modelovací efekty (nárazy, pùsobení gravitace ap.). Tutoriál se více zamìøuje na obecnou funkci kolizí ne¾ zdrojovým kódùm. Nicménì dùle¾ité èásti kódu jsou také popsány. Neoèekávejte, ¾e po prvním pøeètení úplnì v¹emu z kolizí porozumíte. Je to komplexní námìt, se kterým vám pomohu zaèít.</p>

<p>Zdrojový kód, na nìm¾ tato lekce staví, pochází z mého døívìj¹ího pøíspìvku do jedné soutì¾e (najdete na OGLchallenge.dhs.org). Tématem byly Bláznivé kolize a mùj pøíspìvek (mimochodem, získal první místo :-) se jmenoval Magická místnost.</p>

<p>Detekce kolizí jsou obtí¾né, dodnes nebylo nalezeno ¾ádné snadné øe¹ení. Samozøejmì existují velice obecné algoritmy, které umí pracovat s jakýmkoli druhem objektù, ale oèekávejte od nich také patøiènou cenu. My budeme zkoumat postupy, které jsou velmi rychlé, relativnì snadné k pochopení a v rámci mezí celkem flexibilní. Dùraz musí být vlo¾en nejen na detekci kolize, ale i na reakci objektù na náraz. Je dùle¾ité, aby se v¹e dìlo podle fyzikálních zákonù. Máme mnoho vìcí na práci! Pojïme se tedy podívat, co v¹echno se v této lekci nauèíme:</p>

<h3>Detekce kolizí mezi</h3>
<ul>
<li>Pohybující se koulí a rovinou</li>
<li>Pohybující se koulí a válcem</li>
<li>Dvìma pohybujícími se koulemi</li>
</ul>

<h3>Fyzikálnì zalo¾ené modelování</h3>
<ul>
<li>Reakce na kolize - odrazy</li>
<li>Pohyb v gravitaci za pou¾ití Eulerových rovnic</li>
</ul>

<h3>Speciální efekty</h3>
<ul>
<li>Modelování explozí za pou¾ití metody Fin-Tree Billboard</li>
<li>Zvuky pomocí Windows Multimedia Library (pouze Windows)</li>
</ul>

<h3>Zdrojový kód se dìlí na pìt èástí</h3>

<ul>
<li>Lesson30.cpp - Základní kód tutoriálu</li>
<li>Image.cpp, Image.h - Nahrávání bitmap</li>
<li>Tmatrix.cpp, Tmatrix.h - Tøída pro práci s maticemi</li>
<li>Tray.cpp, Tray.h - Tøída pro práci s polopøímkami</li>
<li>Tvector.cpp, Tvector.h - Tøída pro práci s vektory</li>
</ul>

<p>Tøídy Vektor, Ray a Matrix jsou velmi u¾iteèné. Dají se pou¾ít v jakémkoli projektu. Doporuèuji je peèlivì prostudovat, tøeba se vám budou nìkdy hodit.</p>

<h2>Detekce kolizí</h2>

<h3>Polopøímka</h3>

<p>Pøi detekci kolizí vyu¾ijeme algoritmus, který se vìt¹inou pou¾ívá v trasování polopøímek (ray tracing). Vektorová reprezentace polopøímky je tvoøena bodem, který oznaèuje zaèátek a vektorem (obyèejnì normalizovaným) urèujícím smìr polopøímky. Rovnice polopøímky:</p>

<p class="src0"><span class="kom">bod_na_polopøímce = zaèátek + t * smìr</span></p>

<p>Èíslo t nabývá hodnot od nuly do nekoneèna. Dosadíme-li nulu získáme poèáteèní bod. U vìt¹ích èísel dostaneme odpovídající body na polopøímce. Bod, poèátek i smìr jsou 3D vektory se slo¾kami x, y, a z. Nyní mù¾eme pou¾ít tuto reprezentaci polopøímky k výpoètu prùseèíku s rovinou nebo válcem.</p>

<h3>Prùseèík polopøímky a roviny</h3>

<p>Vektorová reprezentace roviny vypadá takto:</p>

<p class="src0"><span class="kom">Xn dot X = d</span></p>

<p>Xn je normála roviny, X je bod na jejím povrchu a d je èíslo, které urèuje vzdálenost roviny od poèátku souøadnicového systému.</p>

<p>Pøekl.: Pod operací &quot;dot&quot; se skrývá skalární souèin dvou vektorù (dot product), který se vypoète souètem násobkù jednotlivých x, y a z slo¾ek. Nechávám ho v pùvodním znìní, proto¾e i ve zdrojových kódech budeme volat metodu dot().</p>

<p class="src0"><span class="kom">Pøekl.: P dot Q = Px * Qx + Py * Qy + Pz * Qz</span></p>

<p>Abychom definovali rovinu, potøebujeme 3D bod a vektor, který je kolmý k rovinì. Pokud vezmeme za 3D bod vektor (0, 0, 0) a pro normálu vektor (0, 1, 0), protíná rovina osy x a z. Pokud známe bod a normálu, dá se chybìjící èíslo d snadno dopoèítat.</p>

<p>Pozn.: Vektorová reprezentace roviny je ekvivalentní více známé formì Ax + By + Cz + D = 0 (obecná rovnice roviny). Pro pøepoèet dosaïte za A, B, C slo¾ky normály a pøiøaïte D = -d.</p>

<p>Nyní máme dvì rovnice</p>

<p class="src0"><span class="kom">bod_na_polopøímce = zaèátek + t * smìr</span></p>
<p class="src0"><span class="kom">Xn dot X = d</span></p>

<p>Pokud polopøímka protne rovinu v nìjakém bodì, musí souøadnice prùseèíkù vyhovovat obìma rovnicím</p>

<p class="src0"><span class="kom">Xn dot bod_na_polopøímce = d</span></p>

<p>Nebo</p>

<p class="src0"><span class="kom">(Xn dot zaèátek) + t * (Xn dot smìr) = d</span></p>

<p>Vyjádøíme t</p>

<p class="src0"><span class="kom">t = (d - Xn dot zaèátek) / (Xn dot smìr)</span></p>

<p>Dosadíme d</p>

<p class="src0"><span class="kom">t = (Xn dot bod_na_polopøímce - Xn dot zaèátek) / (Xn dot smìr)</span></p>

<p>Vytkneme Xn</p>

<p class="src0"><span class="kom">t = (Xn dot (bod_na_polopøímce - zaèátek)) / (Xn dot smìr)</span></p>

<p>Èíslo t reprezentuje vzdálenost od zaèátku polopøímky k prùseèíku s rovinou ve smìru polopøímky (ne na kolmici). Dosazením t do rovnice polopøímky získáme kolizní bod. Existuje nìkolik speciálních situací. Pokud Xn dot smìr = 0, budou tyto dva vektory navzájem kolmé. Polopøímka prochází rovnobì¾nì s rovinou a tudí¾ neexistuje kolizní bod. Pokud je t záporné, prùseèík le¾í pøed poèáteèním bodem. Polopøímka nesmìøuje k rovinì, ale od ní. Opìt ¾ádný prùseèík.</p>

<p class="src0">int TestIntersionPlane(const Plane&amp; plane, const TVector&amp; position, const TVector&amp; direction, double&amp; lamda, TVector&amp; pNormal)</p>
<p class="src0">{</p>
<p class="src1">double DotProduct = direction.dot(plane._Normal);<span class="kom">// Skalární souèin vektorù</span></p>
<p class="src1">double l2;<span class="kom">// Urèuje kolizní bod</span></p>
<p class="src"></p>
<p class="src1">if ((DotProduct &lt; ZERO) &amp;&amp; (DotProduct &gt; -ZERO))<span class="kom">// Je polopøímka rovnobì¾ná s rovinou?</span></p>
<p class="src2">return 0;<span class="kom">// Bez prùseèíku</span></p>
<p class="src"></p>
<p class="src1">l2 = (plane._Normal.dot(plane._Position - position)) / DotProduct;<span class="kom">// Dosazení do vzorce</span></p>
<p class="src"></p>
<p class="src1">if (l2 &lt; -ZERO)<span class="kom">// Smìøuje polopøímka od roviny?</span></p>
<p class="src2">return 0;<span class="kom">// Bez prùseèíku</span></p>
<p class="src"></p>
<p class="src1">pNormal = plane._Normal;<span class="kom">// Normála roviny</span></p>
<p class="src1">lamda = l2;<span class="kom">// Kolizní bod</span></p>
<p class="src"></p>
<p class="src1">return 1;<span class="kom">// Prùseèík existuje</span></p>
<p class="src0">}</p>

<h3>Prùseèík polopøímky a válce</h3>

<p>Výpoèet prùseèíku polopøímky s nekoneènì dlouhým válcem je mnohem komplikovanìj¹í ne¾ vysvìtlení toho, proè se tím zde nebudeme zabývat. Na pozadí je pøíli¹ mnoho matematiky. Mým primárním zámìrem je poskytnout a vysvìtlit nástroje bez zabíhání do zbyteèných detailù, které by stejnì nìkteøí nepochopili. Válec je tvoøen polopøímkou, která reprezentuje jeho osu, a polomìrem podstavy. Pro detekci kolize se v tomto tutoriálu pou¾ívá funkce TestIntersetionCylinder(), která vrací jednièku, pokud byl nalezen prùseèík, jinak nulu.</p>

<p class="src0">int TestIntersionCylinder(const Cylinder& cylinder, const TVector& position, const TVector& direction, double& lamda, TVector& pNormal, TVector& newposition)</p>

<p>V parametrech se pøedává struktura válce, zaèátek a smìrový vektor polopøímky. Kromì návratové hodnoty získáme z funkce vzdálenost od prùseèíku (na polopøímce), normálu vycházející z prùseèíku a bod prùseèíku.</p>

<h3>Kolize mezi dvìma pohybujícími se koulemi</h3>

<p>Koule je v geometrii reprezentována støedem a polomìrem. Zji¹tìní, jestli do sebe dvì koule narazily je banální. Vypoèteme vzdálenost mezi dvìma støedy a porovnáme ji se souètem polomìrù. Jak snadné!</p>

<p>Problémy nastanou pøi hledání kolizního bodu dvou POHYBUJÍCÍCH se koulí. Na obrázku je pøíklad, kdy se dvì koule pøesunou z jednoho místa do druhého za urèitý èasový úsek. Jejich dráhy se protínají, ale to není dostateèný dùvod k tvrzení, ¾e do sebe opravdu narazí. Mohou se napøíklad pohybovat rozdílnou rychlostí. Jedna témìø stojí a druhá je za okam¾ik úplnì nìkde jinde.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_30_1.gif" width="128" height="128" alt="Dvì pohybující se koule" /></div>

<p>V pøedchozích metodách jsme øe¹ili rovnice dvou geometrických objektù. Pokud podobná rovnice pro daný objekt neexistuje nebo nemù¾e být pou¾ita (pohyb po slo¾ité køivce), pou¾ívá se jiná metoda. V na¹em pøípadì známe poèáteèní i koncové body pøesunu, èasový krok, rychlost (+smìr) a metodu zji¹tìní nárazu statických koulí. Rozkouskujeme èasový úsek na malé èásti. Koule budeme v závislosti na nich postupnì posunovat a poka¾dé testovat kolizi. Pokud najdeme nìkterý bod, kdy je vzdálenost koulí men¹í ne¾ souèet jejich polomìrù, vezmeme minulou pozici a oznaèíme ji jako kolizní bod. Mù¾e se je¹tì zaèít interpolovat mezi tìmito dvìma body na rozhraní, kdy kolize je¹tì nebyla a u¾ je, abychom na¹li úplnì pøesnou pozici, ale vìt¹inou to není potøeba.</p>

<p>Èím men¹í bude èasový úsek, tím budou èásti vzniklé rozsekáním men¹í a metoda bude více pøesná (a více nároèná na hardware poèítaèe). Napøíklad, pokud bude èasový úsek 1 a èásti 3, budeme zji¹»ovat kolizi v èasech 0, 0,33, 0,66 a 1. V následujícím výpisu kódu hledáme koule, které bìhem následujícího èasového kroku narazí do kterékoli z ostatních. Funkce vrátí indexy obou koulí, bod a èas nárazu.</p>

<p class="src0">int FindBallCol(TVector&amp; point, double&amp; TimePoint, double Time2, int&amp; BallNr1, int&amp; BallNr2)</p>
<p class="src0">{</p>
<p class="src1">TVector RelativeV;<span class="kom">// Relativní rychlost mezi koulemi</span></p>
<p class="src1">TRay rays;<span class="kom">// Polopøímka</span></p>
<p class="src"></p>
<p class="src1">double MyTime = 0.0;<span class="kom">// Hledání pøesné pozice nárazu</span></p>
<p class="src1">double Add = Time2 / 150.0;<span class="kom">// Rozkouskuje èasový úsek na 150 èástí</span></p>
<p class="src1">double Timedummy = 10000;<span class="kom">// Èas nárazu</span></p>
<p class="src"></p>
<p class="src1">TVector posi;<span class="kom">// Pozice na polopøímce</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Test v¹ech koulí proti v¹em ostatním po 150 krocích</span></p>
<p class="src1">for (int i = 0; i &lt; NrOfBalls - 1; i++)<span class="kom">// V¹echny koule</span></p>
<p class="src1">{</p>
<p class="src2">for (int j = i + 1; j &lt; NrOfBalls; j++)<span class="kom">// V¹echny zbývající koule</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Výpoèet vzdálenosti</span></p>
<p class="src3">RelativeV = ArrayVel[i] - ArrayVel[j];<span class="kom">// Relativní rychlost mezi koulemi</span></p>
<p class="src3">rays = TRay(OldPos[i], TVector::unit(RelativeV));<span class="kom">// Polopøímka</span></p>
<p class="src"></p>
<p class="src3">if ((rays.dist(OldPos[j])) &gt; 40)<span class="kom">// Je vzdálenost vìt¹í ne¾ 2 polomìry?</span></p>
<p class="src3">{</p>
<p class="src4">continue;<span class="kom">// Dal¹í</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Náraz</span></p>
<p class="src"></p>
<p class="src3">MyTime = 0.0;<span class="kom">// Inicializace pøed vstupem do cyklu</span></p>
<p class="src"></p>
<p class="src3">while (MyTime &lt; Time2)<span class="kom">// Pøesný bod nárazu</span></p>
<p class="src3">{</p>
<p class="src4">MyTime += Add;<span class="kom">// Zvìt¹í èas</span></p>
<p class="src4">posi = OldPos[i] + RelativeV * MyTime;<span class="kom">//Pøesun na dal¹í bod (pohyb na polopøímce)</span></p>
<p class="src"></p>
<p class="src4">if (posi.dist(OldPos[j]) &lt;= 40)<span class="kom">// Náraz</span></p>
<p class="src4">{</p>
<p class="src5">point = posi;<span class="kom">// Bod nárazu</span></p>
<p class="src"></p>
<p class="src5">if (Timedummy &gt; (MyTime - Add))<span class="kom">// Bli¾¹í náraz, ne¾ který jsme u¾ na¹li (v èase)?</span></p>
<p class="src5">{</p>
<p class="src6">Timedummy = MyTime - Add;<span class="kom">// Pøiøadit èas nárazu</span></p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">BallNr1 = i;<span class="kom">// Oznaèení koulí, které narazily</span></p>
<p class="src5">BallNr2 = j;</p>
<p class="src5">break;<span class="kom">// Ukonèí vnitøní cyklus</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (Timedummy != 10000)<span class="kom">// Na¹li jsme kolizi?</span></p>
<p class="src1">{</p>
<p class="src2">TimePoint = Timedummy;<span class="kom">// Èas nárazu</span></p>
<p class="src2">return 1;<span class="kom">// Úspìch</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return 0;<span class="kom">// Bez kolize</span></p>
<p class="src0">}</p>

<h3>Kolize mezi koulí a rovinou nebo válcem</h3>

<p>Nyní u¾ umíme zjistit prùseèík polopøímky a roviny/válce. Tyto znalosti pou¾ijeme pro hledání kolizí mezi koulí a jedním z tìchto objektù. Potøebujeme najít pøesný bod nárazu. Pøevod znalostí z polopøímky na pohybující se kouli je relativnì snadný. Podívejte se na levý obrázek, mo¾ná, ¾e podstatu pochopíte sami.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_30_2.gif" width="500" height="150" alt="Náraz pohybující se koule do roviny/válce" /></div>

<p>Ka¾dá koule sice má polomìr, ale my ji budeme brát jako bezrozmìrnou èásti, která má pouze pozici. K povrchu tìlesa pøièteme ve smìru normálového vektoru offset urèený polomìrem koule. Neboli k polomìru válce pøièteme prùmìr koule (2 polomìry; z ka¾dé strany jeden). Operací jsme se vrátili k detekci kolize polopøímka - válec. Rovina je je¹tì jednodu¹¹í. Posuneme ji smìrem ke kouli o její polomìr. Na obrázku jsou èárkovanì nakresleny &quot;virtuální&quot; objekty pro testy kolizí a plnì objekty, které program vykreslí. Kdybychom k objektùm pøi testech nepøipoèítávali offset, koule by pøed odrazem z poloviny pronikaly do objektù (obrázek vpravo).</p>

<p>Máme-li urèit místo nárazu, je vhodné nejdøíve zjistit, jestli kolize nastane pøi aktuálním èasovém úseku. Proto¾e polopøímka má nekoneènou délku, je v¾dy mo¾né, ¾e se kolizní bod nachází a¾ nìkde za novou pozicí koule. Abychom to zjistili, spoèítáme novou pozici a urèíme vzdálenost mezi poèáteèním a koncovým bodem. Pokud je tato vzdálenost krat¹í ne¾ vzdálenost, o kterou se objekt posune, tak máme jistotu, ¾e kolize nastane v tomto èasovém úseku. Abychom spoèítali pøesný èas kolize pou¾ijeme následující jednoduchou rovnici. Dst pøedstavuje vzdálenost mezi poèáteèním a koncovým bodem, Dsc vzdálenost mezi poèáteèním a kolizním bodem a èasový krok je definován jako T. Øe¹ením získáme èas kolize Tc.</p>

<p class="src0"><span class="kom">Tc = Dsc * T / Dst</span></p>

<p>Výpoèet se provede samozøejmì jenom tehdy, kdy¾ má kolize nastat v tomto èasovém kroku. Vrácený èas je zlomkem (èástí) celého èasového kroku. Pokud bude èasový krok 1 s a my nalezneme kolizní bod pøesnì uprostøed vzdálenosti, èas kolize se bude rovnat 0,5 s. Je interpretován jako: V èasovém okam¾iku 0,5 sekund po zaèátku pøesunu do sebe objekty narazí. Kolizní bod se vypoète násobením èasu Tc aktuální rychlostí a pøiètením poèáteèního bodu.</p>

<p class="src0"><span class="kom">bod_kolize = start + rychlost * Tc</span></p>

<p>Tento kolizní bod je v¹ak na objektu s offsetem (pomocném). Abychom nalezli bod nárazu na reálném objektu, pøièteme k bodu kolize invertovaný normálový vektor z bodu kolize, který má velikost polomìru koule. Normálový vektor získáme z funkce pro kolize. V¹imnìte si, ¾e funkce pro kolizi s válcem vrací bod nárazu, tak¾e nemusí být znovu poèítán.</p>

<h2>Modelování zalo¾ené na fyzice</h2>

<h3>Reakce na náraz</h3>

<p>O¹etøení toho, jak se koule zachová po nárazu je stejnì dùle¾ité jako samotné nalezení kolizního bodu. Pou¾ité algoritmy a funkce popisují pøesný bod nárazu, normálový vektor vycházející z objektù v místì nárazu a èasový úsek, ve kterém kolize nastala.</p>

<p>Pøi odrazech nám pomohou fyzikální zákony. Implementujeme pouèku: &quot;Úhel dopadu se rovná úhlu odrazu&quot;. Oba úhly se vztahují k normálovému vektoru, který vychází z objektu v kolizním bodì. Následující obrázek ukazuje odraz polopøímky od koule.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_30_3.gif" width="220" height="180" alt="Odraz od koule" /></div>

<p>I je smìrový vektor pøed nárazem, N je normálový vektor v bodì kolize a R je smìrový vektor po odrazu, který se vypoète podle následující rovnice:</p>

<p class="src0"><span class="kom">R = 2 * (-I dot N) * N + I</span></p>

<p>Omezení spoèívá v tom, ¾e I i N musí být jednotkové vektory. U nás v¹ak délka vektoru reprezentuje rychlost a smìr koule, a proto nemù¾e být bez transformace dosazen do rovnice. Potøebujeme z nìj vyjmout rychlost. Nalezneme jeho velikost a vydìlíme jí jednotlivé x, y, z slo¾ky. Získaný jednotkový vektor dosadíme do rovnice a vypoèteme R. Jsme skoro u konce. Vektor nyní míøí ve smìru odra¾ené polopøímky, ale nemá pùvodní délku. Minule jsme dìlili, tak¾e teï budeme násobit.</p>

<p>Následující výpis kódu se pou¾ívá pro výpoèet odrazu po kolizi koule s rovinou nebo válcem. Uvedený algoritmus pracuje i s jinými povrchy, nezále¾í na jejich tvaru. Pokud nalezneme bod kolize a normálu, je odraz v¾dy stejný.</p>

<p class="src0">rt2 = ArrayVel[BallNr].mag();<span class="kom">// Ulo¾í délku vektoru</span></p>
<p class="src0">ArrayVel[BallNr].unit();<span class="kom">// Normalizace vektoru</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">// Výpoèet odrazu</span></p>
<p class="src0">ArrayVel[BallNr] = TVector::unit((normal * (2 * normal.dot(-ArrayVel[BallNr]))) + ArrayVel[BallNr]);</p>
<p class="src0">ArrayVel[BallNr] = ArrayVel[BallNr] * rt2;<span class="kom">// Nastavení pùvodní délky</span></p>

<h3>Kdy¾ se koule srazí s jinou</h3>

<p>O¹etøení vzájemného nárazu dvou pohybujících se koulí je mnohem obtí¾nìj¹í. Musí být vyøe¹eny slo¾ité rovnice. Nebudeme nic odvozovat, pouze vysvìtlím výsledek. Situace pøi kolizi dvou koulí vypadá pøibli¾nì takto:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_30_4.gif" width="256" height="180" alt="Srá¾ka dvou koulí" /></div>

<p>Vektory U1 a U2 pøedstavují rychlost koulí v èase nárazu. Støedy dohromady spojuje osa X_Axis, na které le¾í vektory U1x a U2x, co¾ jsou vlastnì prùmìty rychlosti. U1y a U2y jsou projekce rychlosti na osu, která je kolmá k X_Axis. K jejich výpoètu postaèí jednoduchý skalární souèin.</p>

<p>Do následujících rovnic dosazujeme je¹tì èísla M1 a M2, která vyjadøují hmotnost koulí. Sna¾íme se vypoèítat orientaci vektorù rychlosti U1 a U2 po odrazu. Budou je vyjadøovat nové vektory V1 a V2. Èísla V1x, V1y, V2x, V2y jsou opìt prùmìty.</p>

<p>a) najít X_Axis</p>

<p class="src0"><span class="kom">X_Axis = (støed2 - støed1)</span></p>
<p class="src0"><span class="kom">Jednotkový vektor, X_Axis.unit();</span></p>

<p>b) najít projekce</p>

<p class="src0"><span class="kom">U1x = X_Axis * (X_Axis dot U1)</span></p>
<p class="src0"><span class="kom">U1y = U1 - U1x</span></p>
<p class="src0"><span class="kom">U2x = -X_Axis * (-X_Axis dot U2)</span></p>
<p class="src0"><span class="kom">U2y = U2 - U2x</span></p>

<p>c) najít nové rychlosti</p>

<p class="src0"><span class="kom">V1x = ((U1x * M1) + (U2x * M2) - (U1x - U2x) * M2) / (M1 + M2)</span></p>
<p class="src0"><span class="kom">V2x = ((U1x * M1) + (U2x * M2) - (U2x - U1x) * M1) / (M1 + M2)</span></p>

<p>V na¹í aplikaci nastavujeme jednotkovou hmotnost (M1 = M2 = 1), a proto se výpoèet výsledných vektorù velmi zjednodu¹í.</p>

<p>d) najít koneèné rychlosti</p>

<p class="src0"><span class="kom">V1y = U1y</span></p>
<p class="src0"><span class="kom">V2y = U2y</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">V1 = V1x + V1y</span></p>
<p class="src0"><span class="kom">V2 = V2x + V2y</span></p>

<p>Odvození rovnic stálo hodnì práce, ale jakmile se nacházejí v této formì, je jejich pou¾ití docela snadné. Kód, které vykonává srá¾ky dvou koulí vypadá takto:</p>

<p class="src0">TVector pb1, pb2, xaxis, U1x, U1y, U2x, U2y, V1x, V1y, V2x, V2y;<span class="kom">// Deklarace promìnných</span></p>
<p class="src0">double a, b;</p>
<p class="src"></p>
<p class="src0">pb1 = OldPos[BallColNr1] + ArrayVel[BallColNr1] * BallTime;<span class="kom">// Nalezení pozice koule 1</span></p>
<p class="src0">pb2 = OldPos[BallColNr2] + ArrayVel[BallColNr2] * BallTime;<span class="kom">// Nalezení pozice koule 2</span></p>
<p class="src"></p>
<p class="src0">xaxis = (pb2 - pb1).unit();<span class="kom">// Nalezení X_Axis</span></p>
<p class="src"></p>
<p class="src0">a = xaxis.dot(ArrayVel[BallColNr1]);<span class="kom">// Nalezení projekce</span></p>
<p class="src0">U1x = xaxis * a;<span class="kom">// Nalezení prùmìtù vektorù</span></p>
<p class="src0">U1y = ArrayVel[BallColNr1] - U1x;</p>
<p class="src"></p>
<p class="src0">xaxis = (pb1 - pb2).unit();</p>
<p class="src"></p>
<p class="src0">b = xaxis.dot(ArrayVel[BallColNr2]);<span class="kom">// To samé pro druhou kouli</span></p>
<p class="src0">U2x = xaxis * b;</p>
<p class="src0">U2y = ArrayVel[BallColNr2] - U2x;</p>
<p class="src"></p>
<p class="src0">V1x = (U1x + U2x - (U1x - U2x)) * 0.5;<span class="kom">// Nalezení nových rychlostí</span></p>
<p class="src0">V2x = (U1x + U2x - (U2x - U1x)) * 0.5;</p>
<p class="src0">V1y = U1y;</p>
<p class="src0">V2y = U2y;</p>
<p class="src"></p>
<p class="src0">for (j = 0; j < NrOfBalls; j++)<span class="kom">// Posun v¹ech koulí do èasu nárazu</span></p>
<p class="src0">{</p>
<p class="src1">ArrayPos[j] = OldPos[j] + ArrayVel[j] * BallTime;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">ArrayVel[BallColNr1] = V1x + V1y;<span class="kom">// Nastavení právì vypoèítaných vektorù koulím, které do sebe narazily</span></p>
<p class="src0">ArrayVel[BallColNr2] = V2x + V2y;</p>

<h3>Pohyb v gravitaci za pou¾ití Eulerových rovnic</h3>

<p>Pro simulaci realistických pohybù nejsou nárazy, hledání kolizních bodù a odrazy dostateèné. Musí být pøidán je¹tì pohyb podle fyzikálních zákonù. Asi nejpou¾ívanìj¹í metodou jsou Eulerovy rovnice. V¹echny výpoèty se vykonávají pro urèitý èasový úsek. To znamená, ¾e se celá simulace neposouvá vpøed plynule, ale po urèitých skocích. Pøedstavte si, ¾e máte fotoaparát a ka¾dou vteøinu výslednou scénu vyfotíte. Bìhem této vteøiny se provedou v¹echny pohyby, testy kolizí a odrazy. Výsledný obrázek se zobrazí na monitoru a zùstane tam a¾ do dal¹í vteøiny. Opìt stejné výpoèty a dal¹í zobrazení. Takto pracují v¹echny poèítaèové animace, ale mnohem rychleji. Oko, stejnì jako u filmu, vidí plynulý pohyb. V závislosti na Eulerových rovnicích se rychlost a pozice v ka¾dém èasovém kroku zmìní takto:</p>

<p class="src0"><span class="kom">nová_rychlost = stará_rychlost + zrychlení * èasový úsek</span></p>
<p class="src0"><span class="kom">nová_pozice = stará_pozice + nová_rychlost * èasový úsek</span></p>

<p>Nyní se objekty pohybují a testují na kolize s pou¾itím nové rychlosti. Zrychlení objektu je získáno vydìlením síly, která na nìj pùsobí, jeho hmotností.</p>

<p class="src0"><span class="kom">zrychlení = síla / hmotnost</span></p>

<p>V tomto demu je gravitace jediná síla, která pùsobí na objekt. Mù¾e být reprezentována vektorem, který udává gravitaèní zrychlení. U nás se bude tento vektor rovnat (0; -0,5; 0). To znamená, ¾e na zaèátku ka¾dého èasového úseku spoèítáme novou rychlost koule a s testováním kolizí ji posuneme. Pokud bìhem èasového úseku narazí (napø. po 0,5 s), posuneme ji na pozici kolize, vypoèteme odraz (nový vektor rychlosti) a pøesuneme ji o zbývající èas (0,5 s). V nìm opìt testujeme kolize atd. Opakujeme tak dlouho, dokud zbývá nìjaký èas.</p>

<p>Pokud je pøítomno více pohybujících se objektù, musí být nejprve testován ka¾dý z nich na nárazy do statických objektù. Ulo¾í se èasovì nejbli¾¹í z nich. Potom se provedou testy nárazù mezi pohybujícími se objekty - ka¾dý s ka¾dým. Vrácený èas je porovnán s èasem u testù se statickými objekty a v úvahu je brán nejbli¾¹í náraz. Celá simulace se posune do tohoto èasu. Vypoète se odraz objektu a opìt se provedou detekce nárazù do statických objektù atd. atd. atd. - dokud zbývá nìjaký èas. Pøekreslí se scéna a v¹e se opakuje nanovo.</p>

<h2>Speciální efekty</h2>

<h3>Exploze</h3>

<p>Kdykoli, kdy¾ se objekty srazí, nastane exploze, která se zobrazí na souøadnicích prùseèíku. Velmi jednoduchou cestou je alfablending dvou polygonù, které jsou navzájem kolmé a jejich støed je na souøadnicích kolizního bodu. Oba polygony se postupnì zvìt¹ují a zprùhledòují. Alfa hodnota se zmen¹uje z poèáteèní jednièky a¾ na nulu. Díky Z bufferu mù¾e spousta alfablendovaných polygonù zpùsobovat problémy - navzájem se pøekrývají, a proto si pùjèíme techniku pou¾ívanou pøi renderingu èástic. Abychom v¹e dìlali správnì, musíme polygony øadit od zadních po pøední podle vzdálenosti od pozorovatele. Také vypneme zápis do Depth bufferu (ne ètení). V¹imnìte si, ¾e omezujeme poèet explozí na maximálnì dvacet na jeden snímek. Nastane-li jich najednou více, pole se zaplní a dal¹í se nebudou brát v úvahu. Následuje kód, který aktualizuje a renderuje exploze.</p>

<p class="src0">glEnable(GL_BLEND);<span class="kom">// Blending</span></p>
<p class="src0">glDepthMask(GL_FALSE);<span class="kom">// Vypne zápis do depth bufferu</span></p>
<p class="src0">glBindTexture(GL_TEXTURE_2D, texture[1]);<span class="kom">// Textura exploze</span></p>
<p class="src"></p>
<p class="src0">for(i = 0; i < 20; i++)<span class="kom">// Prochází výbuchy</span></p>
<p class="src0">{</p>
<p class="src1">if(ExplosionArray[i]._Alpha >= 0)<span class="kom">// Je exploze vidìt?</span></p>
<p class="src1">{</p>
<p class="src2">glPushMatrix();<span class="kom">// Záloha matice</span></p>
<p class="src3">ExplosionArray[i]._Alpha -= 0.01f;<span class="kom">// Aktualizace alfa hodnoty</span></p>
<p class="src3">ExplosionArray[i]._Scale += 0.03f;<span class="kom">// Aktualizace mìøítka</span></p>
<p class="src"></p>
<p class="src3">glColor4f(1, 1, 0, ExplosionArray[i]._Alpha);<span class="kom">// ®lutá barva s prùhledností</span></p>	 
<p class="src3">glScalef(ExplosionArray[i]._Scale, ExplosionArray[i]._Scale, ExplosionArray[i]._Scale);<span class="kom">// Zmìna mìøítka</span></p>
<p class="src"></p>
<p class="src3">glTranslatef((float)ExplosionArray[i]._Position.X() / ExplosionArray[i]._Scale, (float)ExplosionArray[i]._Position.Y() / explosionArray[i]._Scale, (float)ExplosionArray[i]._Position.Z() / ExplosionArray[i]._Scale);<span class="kom">// Pøesun na pozici kolizního bodu, mìøítko je offsetem</span></p>
<p class="src"></p>
<p class="src3">glCallList(dlist);<span class="kom">// Zavolá display list</span></p>
<p class="src2">glPopMatrix();<span class="kom">// Obnova pùvodní matice</span></p>
<p class="src1">}</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">glDepthMask(GL_TRUE);<span class="kom">// Obnova pùvodních parametrù OpenGL</span></p>
<p class="src0">glDisable(GL_BLEND);</p>
<p class="src0">glDisable(GL_TEXTURE_2D);</p>

<h3>Zvuky</h3>

<p>Pro pøehrávání zvukù se pou¾ívá funkce PlaySound() z multimediální knihovny Windows - rychlá cesta, jak bez problémù pøehrát .wav zvuk.</p>

<h2>Vysvìtlení kódu</h2>

<p>Gratuluji... pokud stále ètete, úspì¹nì jste se prokousali dlouhou a nároènou teoretickou sekcí. Pøedtím, ne¾ si zaènete hrát s demem, mìl by být je¹tì vysvìtlen zdrojový kód. Ze v¹eho nejdøíve se ale pùjdeme podívat na globální promìnné.</p>

<p>Vektory dir a pos reprezentují pozici a smìr kamery, kterou v programu pohybujeme funkcí gluLookAt(). Pokud scéna není vykreslována v módu &quot;sledování koule&quot;, otáèí se kolem osy y.</p>

<p class="src0">TVector dir;<span class="kom">// Smìr kamery</span></p>
<p class="src0">TVector pos(0, -50, 1000);<span class="kom">// Pozice kamery</span></p>
<p class="src0">float camera_rotation = 0;<span class="kom">// Rotace scény na ose y</span></p>

<p>Gravitace, která pùsobí na koule.</p>

<p class="src0">TVector accel(0, -0.05, 0);<span class="kom">// Gravitaèní zrychlení aplikované na koule</span></p>

<p>Pole, která ukládají novou a starou pozici v¹ech koulí a jejich smìr. Poèet koulí je natvrdo nastaven na deset.</p>

<p class="src0">TVector ArrayVel[10];<span class="kom">// Rychlost koulí</span></p>
<p class="src0">TVector ArrayPos[10];<span class="kom">// Pozice koulí</span></p>
<p class="src0">TVector OldPos[10];<span class="kom">// Staré pozice koulí</span></p>

<p>Èasový úsek pro simulaci.</p>

<p class="src0">double Time = 0.6;<span class="kom">// Èasový krok simulace</span></p>

<p>Pokud je tato promìnná v jednièce, zmìní se mód kamery tak, aby sledovala pohyby koule. Pro její umístìní a nasmìrování se pou¾ije pozice a smìr koule s indexem 1, která tedy bude v¾dy v zábìru.</p>

<p class="src0">int hook_toball1 = 0;<span class="kom">// Sledovat kamerou kouli?</span></p>

<p>Následující struktury se popisují samy svým jménem. Budou ukládat data o rovinách, válcích a explozích.</p>

<p class="src0">struct Plane<span class="kom">// Struktura roviny</span></p>
<p class="src0">{</p>
<p class="src1">TVector _Position;</p>
<p class="src1">TVector _Normal;</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">struct Cylinder<span class="kom">// Struktura válce</span></p>
<p class="src0">{</p>
<p class="src1">TVector _Position;</p>
<p class="src1">TVector _Axis;</p>
<p class="src1">double _Radius;</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">struct Explosion<span class="kom">// Struktura exploze</span></p>
<p class="src0">{</p>
<p class="src1">TVector _Position;</p>
<p class="src1">float   _Alpha;</p>
<p class="src1">float   _Scale;</p>
<p class="src0">};</p>

<p>Objekty struktur.</p>

<p class="src0">Plane pl1, pl2, pl3, pl4, pl5;<span class="kom">// Pìt rovin místnosti (bez stropu)</span></p>
<p class="src0">Cylinder cyl1, cyl2, cyl3;<span class="kom">// Tøi válce</span></p>
<p class="src0">Explosion ExplosionArray[20];<span class="kom">// Dvacet explozí</span></p>

<p>Textury, display list, quadratic.</p>

<p class="src0">GLuint texture[4];<span class="kom">// Ètyøi textury</span></p>
<p class="src0">GLuint dlist;<span class="kom">// Display list výbuchu</span></p>
<p class="src0">GLUquadricObj *cylinder_obj;<span class="kom">// Quadratic pro kreslení koulí a válcù</span></p>

<p>Funkce pro kolize koulí se statickými objekty a mezi koulemi navzájem.</p>

<p class="src0">int TestIntersionPlane(const Plane& plane, const TVector& position, const TVector& direction, double& lamda, TVector& pNormal);</p>
<p class="src"></p>
<p class="src0">int TestIntersionCylinder(const Cylinder& cylinder, const TVector& position, const TVector& direction, double& lamda, TVector& pNormal, TVector& newposition);</p>
<p class="src"></p>
<p class="src0">int FindBallCol(TVector& point, double& TimePoint, double Time2, int& BallNr1, int& BallNr2);</p>

<p>Loading textur, inicializace promìnných, logika simulace, renderování scény a inicializace OpenGL.</p>

<p class="src0">void LoadGLTextures();</p>
<p class="src0">void InitVars();</p>
<p class="src0">void idle();</p>
<p class="src"></p>
<p class="src0">int DrawGLScene(GLvoid);</p>
<p class="src0">int InitGL(GLvoid)</p>

<p>Pro informace o geometrických tøídách vektoru, polopøímky a matice nahlédnìte do zdrojových kódù. Jsou velmi u¾iteèné a mohou být bez problémù vyu¾ity ve va¹ich vlastních programech.</p>

<p>Nejdùle¾itìj¹í kroky simulace nejprve popí¹i pseudokódem.</p>

<p class="src0"><span class="kom">while (èasový úsek != 0)</span></p>
<p class="src0"><span class="kom">{</span></p>
<p class="src1"><span class="kom">for (ka¾dá koule)</span></p>
<p class="src1"><span class="kom">{</span></p>
<p class="src2"><span class="kom">Výpoèet nejbli¾¹í kolize s rovinami;</span></p>
<p class="src2"><span class="kom">Výpoèet nejbli¾¹í kolize s válci;</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">Ulo¾it/nahradit &quot;záznam&quot; o kolizi, pokud je to do teï nejbli¾¹í kolize v èase;</span></p>
<p class="src1"><span class="kom">}</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">Testy kolizí mezi pohybujícími se koulemi;</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">Ulo¾it/nahradit &quot;záznam&quot; o kolizi, pokud je to do teï nejbli¾¹í kolize v èase;</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">if (nastala kolize?)</span></p>
<p class="src1"><span class="kom">{</span></p>
<p class="src2"><span class="kom">Pøesun v¹ech koulí do èasu nejbli¾¹í kolize;</span></p>
<p class="src2"><span class="kom">(U¾ máme vypoèten bod, normálu a èas kolize.)</span></p>
<p class="src2"><span class="kom">Výpoèet odrazu;</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">èasový úsek -= èas kolize;</span></p>
<p class="src1"><span class="kom">}</span></p>
<p class="src1"><span class="kom">else</span></p>
<p class="src1"><span class="kom">{</span></p>
<p class="src2"><span class="kom">Pøesun v¹ech koulí na konec èasového úseku;</span></p>
<p class="src1"><span class="kom">}</span></p>
<p class="src0"><span class="kom">}</span></p>

<p>Zdrojový kód zalo¾ený na pseudokódu je na první pohled mnohem více nároèný na ètení a hlavnì pochopení, nicménì v základu je jeho pøesnou implementací.</p>

<p class="src0">void idle()<span class="kom">// Simulaèní logika - kolize</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Deklarace promìnných</span></p>
<p class="src1">double rt, rt2, rt4, lamda = 10000;</p>
<p class="src"></p>
<p class="src1">TVector norm, uveloc;</p>
<p class="src1">TVector normal, point, time;</p>
<p class="src"></p>
<p class="src1">double RestTime, BallTime;</p>
<p class="src"></p>
<p class="src1">TVector Pos2;</p>
<p class="src"></p>
<p class="src1">int BallNr = 0, dummy = 0, BallColNr1, BallColNr2;</p>
<p class="src1">TVector Nc;</p>
<p class="src"></p>
<p class="src1">if (!hook_toball1)<span class="kom">// Pokud kamera nesleduje kouli</span></p>
<p class="src1">{</p>
<p class="src2">camera_rotation += 0.1f;<span class="kom">// Pootoèení scény</span></p>
<p class="src"></p>
<p class="src2">if (camera_rotation &gt; 360)<span class="kom">// O¹etøení pøeteèení</span></p>
<p class="src2">{</p>
<p class="src3">camera_rotation = 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">RestTime = Time;</p>
<p class="src1">lamda = 1000;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Výpoèet rychlostí v¹ech koulí pro následující èasový úsek (Eulerovy rovnice)</span></p>
<p class="src1">for (int j = 0; j &lt; NrOfBalls; j++)</p>
<p class="src1">{</p>
<p class="src2">ArrayVel[j] += accel * RestTime;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">while (RestTime &gt; ZERO)<span class="kom">// Dokud neskonèil èasový úsek</span></p>
<p class="src1">{</p>
<p class="src2">lamda = 10000;<span class="kom">// Inicializace na velmi vysokou hodnotu</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Kolize v¹ech koulí s rovinami a válci</span></p>
<p class="src2">for (int i = 0; i &lt; NrOfBalls; i++)<span class="kom">// V¹echny koule</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Výpoèet nové pozice a vzdálenosti</span></p>
<p class="src3">OldPos[i] = ArrayPos[i];</p>
<p class="src3">TVector::unit(ArrayVel[i], uveloc);</p>
<p class="src3">ArrayPos[i] = ArrayPos[i] + ArrayVel[i] * RestTime;</p>
<p class="src3">rt2 = OldPos[i].dist(ArrayPos[i]);</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Kolize koule s rovinou</span></p>
<p class="src3">if (TestIntersionPlane(pl1, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// Èas nárazu</span></p>
<p class="src4">rt4 = rt * RestTime / rt2;</p>
<p class="src"></p>
<p class="src4"><span class="kom">// Pokud je men¹í ne¾ nìkterý z døíve nalezených nahradit ho</span></p>
<p class="src4">if (rt4 &lt;= lamda)</p>
<p class="src4">{</p>
<p class="src5">if (rt4 &lt;= RestTime + ZERO)</p>
<p class="src5">{</p>
<p class="src6">if (!((rt &lt;= ZERO) &amp;&amp; (uveloc.dot(norm) &gt; ZERO)))</p>
<p class="src6">{</p>
<p class="src7">normal = norm;</p>
<p class="src7">point = OldPos[i] + uveloc * rt;</p>
<p class="src7">lamda = rt4;</p>
<p class="src7">BallNr = i;</p>
<p class="src6">}</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionPlane(pl2, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To samé jako minule, ale s jinou rovinou</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionPlane(pl3, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To samé jako minule, ale s jinou rovinou</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionPlane(pl4, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To samé jako minule, ale s jinou rovinou</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionPlane(pl5, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To samé jako minule, ale s jinou rovinou</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Kolize koule s válcem</span></p>
<p class="src3">if (TestIntersionCylinder(cyl1, OldPos[i], uveloc, rt, norm, Nc))</p>
<p class="src3">{</p>
<p class="src4">rt4 = rt * RestTime / rt2;</p>
<p class="src"></p>
<p class="src4">if (rt4 &lt;= lamda)</p>
<p class="src4">{</p>
<p class="src5">if (rt4 &lt;= RestTime + ZERO)</p>
<p class="src5">{</p>
<p class="src6">if (!((rt &lt;= ZERO) &amp;&amp; (uveloc.dot(norm) &gt; ZERO)))</p>
<p class="src6">{</p>
<p class="src7">normal = norm;</p>
<p class="src7">point = Nc;</p>
<p class="src7">lamda = rt4;</p>
<p class="src7">BallNr = i;</p>
<p class="src6">}</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionCylinder(cyl2, OldPos[i], uveloc, rt, norm, Nc))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To samé jako minule, ale s jiným válcem</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionCylinder(cyl3, OldPos[i], uveloc, rt, norm, Nc))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To samé jako minule, ale s jiným válcem</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Kolize mezi koulemi</span></p>
<p class="src2">if (FindBallCol(Pos2, BallTime, RestTime, BallColNr1, BallColNr2))</p>
<p class="src2">{</p>
<p class="src3">if (sounds)<span class="kom">// Jsou zapnuté zvuky?</span></p>
<p class="src3">{</p>
<p class="src4">PlaySound(&quot;Data/Explode.wav&quot;, NULL, SND_FILENAME | SND_ASYNC);</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if ((lamda == 10000) || (lamda &gt; BallTime))</p>
<p class="src3">{</p>
<p class="src4">RestTime = RestTime - BallTime;</p>
<p class="src"></p>
<p class="src4">TVector pb1, pb2, xaxis, U1x, U1y, U2x, U2y, V1x, V1y, V2x, V2y;<span class="kom">// Deklarace promìnných</span></p>
<p class="src4">double a, b;</p>
<p class="src"></p>
<p class="src4">pb1 = OldPos[BallColNr1] + ArrayVel[BallColNr1] * BallTime;<span class="kom">// Nalezení pozice koule 1</span></p>
<p class="src4">pb2 = OldPos[BallColNr2] + ArrayVel[BallColNr2] * BallTime;<span class="kom">// Nalezení pozice koule 2</span></p>
<p class="src"></p>
<p class="src4">xaxis = (pb2 - pb1).unit();<span class="kom">// Nalezení X_Axis</span></p>
<p class="src"></p>
<p class="src4">a = xaxis.dot(ArrayVel[BallColNr1]);<span class="kom">// Nalezení projekce</span></p>
<p class="src4">U1x = xaxis * a;<span class="kom">// Nalezení prùmìtù vektorù</span></p>
<p class="src4">U1y = ArrayVel[BallColNr1] - U1x;</p>
<p class="src"></p>
<p class="src4">xaxis = (pb1 - pb2).unit();</p>
<p class="src"></p>
<p class="src4">b = xaxis.dot(ArrayVel[BallColNr2]);<span class="kom">// To samé pro druhou kouli</span></p>
<p class="src4">U2x = xaxis * b;</p>
<p class="src4">U2y = ArrayVel[BallColNr2] - U2x;</p>
<p class="src"></p>
<p class="src4">V1x = (U1x + U2x - (U1x - U2x)) * 0.5;<span class="kom">// Nalezení nových rychlostí</span></p>
<p class="src4">V2x = (U1x + U2x - (U2x - U1x)) * 0.5;</p>
<p class="src4">V1y = U1y;</p>
<p class="src4">V2y = U2y;</p>
<p class="src"></p>
<p class="src4">for (j = 0; j &lt; NrOfBalls; j++)<span class="kom">// Aktualizace pozic v¹ech koulí</span></p>
<p class="src4">{</p>
<p class="src5">ArrayPos[j] = OldPos[j] + ArrayVel[j] * BallTime;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">ArrayVel[BallColNr1] = V1x + V1y;<span class="kom">// Nastavení právì vypoèítaných vektorù koulím, které do sebe narazily</span></p>
<p class="src4">ArrayVel[BallColNr2] = V2x + V2y;</p>
<p class="src"></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Aktualizace pole explozí</span></p>
<p class="src4">for(j = 0; j &lt; 20; j++)<span class="kom">// V¹echny exploze</span></p>
<p class="src4">{</p>
<p class="src5">if (ExplosionArray[j]._Alpha &lt;= 0)<span class="kom">// Hledá volné místo</span></p>
<p class="src5">{</p>
<p class="src6">ExplosionArray[j]._Alpha = 1;<span class="kom">// Neprùhledná</span></p>
<p class="src6">ExplosionArray[j]._Position = ArrayPos[BallColNr1];<span class="kom">// Pozice</span></p>
<p class="src6">ExplosionArray[j]._Scale = 1;<span class="kom">// Mìøítko</span></p>
<p class="src"></p>
<p class="src6">break;<span class="kom">// Ukonèit prohledávání</span></p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">continue;<span class="kom">// Opakovat cyklus</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Konec testù kolizí</span></p>
<p class="src2"><span class="kom">// Pokud se pro¹el celý èasový úsek a byly vypoèteny reakce koulí, které narazily</span></p>
<p class="src2">if (lamda != 10000)</p>
<p class="src2">{</p>
<p class="src3">RestTime -= lamda;<span class="kom">// Odeètení èasu kolize od èasového úseku</span></p>
<p class="src"></p>
<p class="src3">for (j = 0; j &lt; NrOfBalls; j++)</p>
<p class="src3">{</p>
<p class="src4">ArrayPos[j] = OldPos[j] + ArrayVel[j] * lamda;</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">rt2 = ArrayVel[BallNr].mag();</p>
<p class="src3">ArrayVel[BallNr].unit();</p>
<p class="src3">ArrayVel[BallNr] = TVector::unit((normal * (2 * normal.dot(-ArrayVel[BallNr]))) + ArrayVel[BallNr]);</p>
<p class="src3">ArrayVel[BallNr] = ArrayVel[BallNr] * rt2;</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Aktualizace pole explozí</span></p>
<p class="src3">for(j = 0; j &lt; 20; j++)<span class="kom">// V¹echny exploze</span></p>
<p class="src3">{</p>
<p class="src4">if (ExplosionArray[j]._Alpha &lt;= 0)<span class="kom">// Hledá volné místo</span></p>
<p class="src4">{</p>
<p class="src5">ExplosionArray[j]._Alpha = 1;<span class="kom">// Neprùhledná</span></p>
<p class="src5">ExplosionArray[j]._Position = ArrayPos[BallColNr1];<span class="kom">// Pozice</span></p>
<p class="src5">ExplosionArray[j]._Scale = 1;<span class="kom">// Mìøítko</span></p>
<p class="src"></p>
<p class="src5">break;<span class="kom">// Ukonèit prohledávání</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">RestTime = 0;<span class="kom">// Ukonèení hlavního cyklu a vlastnì i funkce</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jak jsem u¾ napsal na zaèátku, pøedmìt kolizí je velmi tì¾ký a rozsáhlý, aby se dal popsat jen v jednom tutoriálu, pøesto jste se nauèili spoustu nových vìcí. Mù¾ete zaèít vytváøet vlastní pùsobivá dema. Nyní, kdy¾ chápete základy, budete lépe rozumìt i cizím zdrojovým kódùm, které vás zase posunou o kousek dál. Pøeji hodnì ¹tìstí.</p>

<p class="autor">napsal: Dimitrios Christopoulos<br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3>Informace o autorovi</h3>

<p>V souèasné dobì pracuje jako softwarový in¾enýr virtuální reality Helénského svìta v Aténách/Øecko (www.fhw.gr). Aèkoli se narodil v Nìmecku, studoval øeckou univerzitu Patras na bakaláøe pøírodních vìd v poèítaèovém in¾enýrství a informatice. Je také dr¾itelem MSc degree (titul Magistra pøírodních vìd) z univerzity Hull (Anglie) v poèítaèové grafice a virtuálním prostøedí.</p>

<p>První krùèky s programováním podnikl v jazyce Basic na Commodoru 64. Po zaèátku studia se pøeorientoval na C/C++/Assembler na platformì PC. Bìhem nìkolika minulých let si jako grafické API zvolil OpenGL.</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson30.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson30_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson30.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson30.zip">Delphi</a> kód této lekce. ( <a href="mailto:another.freak@gmx.de">Felix Hahn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson30.zip">Dev C++</a> kód této lekce. ( <a href="mailto:conrado@buhrer.net">Conrado</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson30.jar">JoGL</a> kód této lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson30.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:rodolphe.suescun@wanadoo.fr">Rodolphe Suescun</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson30.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson30.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(30);?>
<?FceNeHeOkolniLekce(30);?>

<?
include 'p_end.php';
?>
