<?
$g_title = 'CZ NeHe OpenGL - Generování planet';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Generování planet</h1>

<p class="nadpis_clanku">Pokud budete nìkdy potøebovat pro svou aplikaci vygenerovat realisticky vypadající planetu, tento èlánek se vám bude urèitì hodit - popisuje jeden ze zpùsobù vytváøení nedeformovaných kontinentù. Obvyklé zpùsoby pokrývání koule rovinnou texturou konèí obrovskými deformacemi na pólech. Dal¹í nevýhodou nìkterých zpùsobù je, ¾e výsledek je orientován v nìjakém smìru. To u této metody nehrozí.</p>

<h3>Postup</h3>

<p>Princip je jednoduchý, hor¹í ale bude implementace. Jak tedy postupovat?</p>

<ol>
<li>Vezmeme kouli</li>
<li>Náhodnì zvolenou rovinou procházející pøes její støed, ji rozdìlíme na dvì poloviny</li>
<li>Jednu polovinu o trochu zvìt¹íme a druhou zmen¹íme</li>
<li>Opakujeme kroky 2 a 3</li>
</ol>

<div class="okolo_img"><img src="images/clanky/cl_gl_planety/land1.gif" width="160" height="158" alt="Postup" /></div>

<div class="okolo_img">
<div>Na následujících obrázcích vidíte celý postup v nìkolik prvních krocích.</div>
<img src="images/clanky/cl_gl_planety/land2.gif" width="111" height="111" alt="Pøed rozdìlením" />
<img src="images/clanky/cl_gl_planety/land3.gif" width="111" height="111" alt="Po prvním rozdìlení" />
<img src="images/clanky/cl_gl_planety/land4.gif" width="111" height="111" alt="Po druhém rozdìlení" />
<img src="images/clanky/cl_gl_planety/land5.gif" width="111" height="111" alt="Po tøetím rozdìlení" />
</div>

<p>Po velkém poètu dìlení se zaènou malé høebeny formovat do tvaru kontinentù, tak¾e nechte algoritmus probíhat tak dlouho, dokud nemají po¾adovaný tvar.</p>

<div class="okolo_img">
<div><b>100 iterací</b> - Pìknì tvarované kontinenty se objeví u¾ po sto iteracích.</div>
<img src="images/clanky/cl_gl_planety/land6.gif" width="222" height="222" alt="Èelní pohled" />
<img src="images/clanky/cl_gl_planety/land7.gif" width="222" height="222" alt="Pohled zezadu" />
</div>

<div class="okolo_img">
<div><b>1000 iterací</b> - Objevuje se první známka hor a zároveò se zaèínají objevovat i ostrovy.</div>
<img src="images/clanky/cl_gl_planety/land8.gif" width="222" height="222" alt="Èelní pohled" />
<img src="images/clanky/cl_gl_planety/land9.gif" width="222" height="222" alt="Pohled zezadu" />
</div>

<div class="okolo_img">
<div><b>10000 iterací</b> - Teï se ji¾ objevují velké hory. Pobøe¾í je komplexní a objevují se ostrovy a jezera.</div>
<img src="images/clanky/cl_gl_planety/land10.gif" width="222" height="222" alt="Èelní pohled" />
<img src="images/clanky/cl_gl_planety/land11.gif" width="222" height="222" alt="Pohled zezadu" />
</div>


<h3>Nedostatky</h3>

<p>Jistì jste si v¹imli nìèeho podivného. Pohled zezadu vypadá skoro stejnì jako èelní strana vzhùru nohama a s prohozenými kontinenty za oceány. To je asi nejvìt¹í nevýhoda této metody. Na místì, kde se na jedné stranì planety nacházejí moøe, je na druhé stranì kontinent, nicménì si toho èasto ani nev¹imnete.</p>

<h3>Implementace</h3>

<p>Nejdøíve si musíme nadefinovat &quot;kouli&quot; s mo¾ností promìnného polomìru. Udìláme to pomocí dvourozmìrného pole m_r - viz následující výpis hlavièkového souboru. Mù¾eme si to dovolit, proto¾e ka¾dý bod v prostoru je dán dvìma úhly a vzdáleností od støedu. První index pole udává úhel alfa, druhý index pøedstavuje úhel beta a hodnota ulo¾ená v poli vyjadøuje vzdálenost od støedu. Úhel alfa udává odklon spojnice bodu a poèátkem s osou x v rovinì x, z. Úhel beta udává úhel mezi spojnicí bodu a poèátkem s osou y. Pøedpokládáme klasickou orientaci souøadnic v OpenGL, tj. x doprava, y nahoru a z ven z obrazovky (k u¾ivateli). Pro pøepoèet indexù pole na úhel pou¾íváme následující makro:</p>

<p class="src0"><span class="key">#define</span> UHEL(x) (2 * PI * (<span class="key">double</span>(x) / <span class="key">double</span>(SLICES)))</p>

<p>...kde x je index pole, PI je hodnota 3.14159... a SLICES udává v kolika bodech na obvodu je koule definovaná, tj. poèet poledníkù. Poèet rovnobì¾ek je polovièní, aby byl zjednodu¹ený kód pro vykreslování a výpoèet úhlu z indexu. Pro výpoèet souøadnic z úhlù a polomìru se pou¾ívají následující vzorce:</p>

<p class="src0"><span class="kom">x = r * cos(alfa) * sin(beta)</span></p>
<p class="src0"><span class="kom">y = r * cos(beta)</span></p>
<p class="src0"><span class="kom">z = r * sin(alfa) * sin(beta)</span></p>

<p>A zde je slibovaný výpis hlavièkového souboru.</p>

<p class="src0"><span class="kom">// na kolik èástí je planeta rozdìlena</span></p>
<p class="src0"><span class="kom">// poèet poledníkù je roven SLICES</span></p>
<p class="src0"><span class="kom">// poèet rovnobì¾ek je roven SLICES / 2</span></p>
<p class="src0"><span class="kom">// èím je hodnota vìt¹í, tím déle trvá výpoèet a vykreslování, ale zároveò se zlep¹uje vzhled</span></p>
<p class="src0"><span class="key">#define</span> SLICES 500</p>
<p class="src"></p>
<p class="src0"><span class="kom">// polomìr hladiny oceánù - nastavuje se pomocí funkce Reset </span></p>
<p class="src0"><span class="kom">// (je volána automaticky v konstruktoru, ale mù¾ete ji volat i sami)</span></p>
<p class="src0"><span class="key">#define</span> R m_default_r</p>
<p class="src"></p>
<p class="src0"><span class="key">#define</span> PI 3.1415926535897932384626433832795</p>
<p class="src"></p>
<p class="src0"><span class="kom">// pøepoèet indexu pole na úhel</span></p>
<p class="src0"><span class="key">#define</span> UHEL(x) (2 * PI * (<span class="key">double</span>(x) / <span class="key">double</span>(SLICES)))</p>
<p class="src"></p>
<p class="src0"><span class="key">class</span> CPlanet</p>
<p class="src0">{</p>
<p class="src0"><span class="key">public</span>:</p>
<p class="src1"><span class="kom">// funkce pro generování kontinentù</span></p>
<p class="src1"><span class="kom">// nsteps udává kolik krokù algoritmu pro generování provést</span></p>
<p class="src1"><span class="kom">// pìkné výsledky lze dostat asi od 250 krokù</span></p>
<p class="src1"><span class="key">void</span> GenerujKontinenty(<span class="key">const</span> <span class="key">int</span> nsteps);</p>
<p class="src1"><span class="kom">// resetuje do výchozího stavu a nastaví polomìr planety</span></p>
<p class="src1"><span class="key">void</span> Reset(<span class="key">const</span> <span class="key">double</span> r);</p>
<p class="src1"><span class="kom">// vykreslí planetu (pomocí OpenGL)</span></p>
<p class="src1"><span class="key">void</span> Draw();</p>
<p class="src1">CPlanet();</p>
<p class="src1"><span class="key">virtual</span> ~CPlanet();</p>
<p class="src"></p>
<p class="src0"><span class="key">protected</span>:</p>
<p class="src1"><span class="kom">// pomocná promìnná do které se ukládá vý¹ka nejvy¹¹ího vrcholu kvùli volbì barvy pøi vykreslování</span></p>
<p class="src1"><span class="key">double</span> m_max_r;</p>
<p class="src1"><span class="kom">// pole pro ulo¾ení vý¹ky povrchu</span></p>
<p class="src1"><span class="key">double</span> m_r[SLICES][SLICES/2];</p>
<p class="src1"><span class="kom">// výchozí polomìr planety a zároveò vý¹ka hladiny moøí</span></p>
<p class="src1"><span class="key">double</span> m_default_r;</p>
<p class="src0">};</p>

<p>Funkce která implementuje generování kontinentù vypadá takto:</p>

<p class="src0"><span class="key">void</span> CPlanet::GenerujKontinenty(<span class="key">const</span> <span class="key">int</span> nsteps)</p>
<p class="src0">{</p>
<p class="src1">m_max_r = R;</p>
<p class="src1"><span class="key">int</span> i,j,k;</p>
<p class="src1"><span class="key">double</span> nx,ny,nz,x,y,z,ns;</p>
<p class="src"></p>
<p class="src1"><span class="key">for</span> (k=0; k&lt;nsteps; k++)<span class="kom">// opakovat po zadaný poèet krokù</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// náhodnì vygenerovat normálový vektor plochy</span></p>
<p class="src2">nx = (<span class="key">double</span>(rand())/<span class="key">double</span>(RAND_MAX))-0.5;</p>
<p class="src2">ny = (<span class="key">double</span>(rand())/<span class="key">double</span>(RAND_MAX))-0.5;</p>
<p class="src2">nz = (<span class="key">double</span>(rand())/<span class="key">double</span>(RAND_MAX))-0.5;</p>
<p class="src"></p>
<p class="src2"><span class="kom">// pro v¹echny vrcholy</span></p>
<p class="src2"><span class="key">for</span>(i=0; i&lt;SLICES; i++)</p>
<p class="src3"><span class="key">for</span>(j=0; j&lt;SLICES/2; j++)</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// vektor (jednotkový) ze støedu koule k vrcholu</span></p>
<p class="src4">x = cos(UHEL(i))*sin(UHEL(j));</p>
<p class="src4">y = cos(UHEL(j));</p>
<p class="src4">z = sin(UHEL(i))*sin(UHEL(j));</p>
<p class="src"></p>
<p class="src4"><span class="kom">// skalární souèin normálového vektoru a vektoru ze støedu koule k vrcholu</span></p>
<p class="src4"><span class="kom">// pokud je úhel mezi vektory men¹í nebo roven 90 stupòù je kladný, jinak záporný</span></p>
<p class="src4">ns = nx*x + ny*y + nz*z;</p>
<p class="src"></p>
<p class="src4"><span class="key">if</span> (ns&gt;=0)<span class="kom">// úhel mezi vektory men¹í nebo roven 90 stupòù</span></p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// zvý¹it vrchol</span></p>
<p class="src5">m_r[i][j] += 1e-3*R;</p>
<p class="src4">}</p>
<p class="src4"><span class="key">else</span><span class="kom">// úhel mezi vektory vìt¹í ne¾ 90 stupòù</span></p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// sní¾it vrchol</span></p>
<p class="src5">m_r[i][j] -= 1e-3*R;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4"><span class="kom">// pomocný krok kvùli volbì barvy vrcholu</span></p>
<p class="src4"><span class="kom">// pokud je vý¹ka vrcholu vìt¹í ne¾ maximální potom maximální nastavit na vý¹ku vrcholu</span></p>
<p class="src4"><span class="key">if</span> (m_max_r&lt;m_r[i][j]) m_max_r=m_r[i][j];</p>
<p class="src3">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Teï je je¹tì tøeba na základì takto spoèítaných hodnot planetu vykreslit.</p>

<p class="src0"><span class="key">void</span> CPlanet::Draw()</p>
<p class="src0">{</p>
<p class="src1">register <span class="key">int</span> i,j;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// vykreslení koule s rùznými polomìry jednotlivých bodù</span></p>
<p class="src1">glBegin(GL_TRIANGLE_STRIP);</p>
<p class="src2"><span class="key">if</span> (m_r[0][0] &lt;= R)<span class="kom">// pod hladinou moøe</span></p>
<p class="src2">{</p>
<p class="src3">glColor3d(0, 0, 0.9);<span class="kom">// modrá barva</span></p>
<p class="src3"><span class="kom">// vykreslení vrcholu koule</span></p>
<p class="src3">glVertex3d(R * cos(UHEL(0)) * sin(UHEL(0)), R * cos(UHEL(0)), R * sin(UHEL(0)) * sin(UHEL(0)));</p>
<p class="src2">}</p>
<p class="src2"><span class="key">else</span><span class="kom">// nad hladinou moøe</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// barva mezi zelenou a hnìdou (podle nadmoøské vý¹ky)</span></p>
<p class="src3">glColor3d(0.455 * (m_r[0][0]-R) / (m_max_r-R), 0.39 * (m_r[0][0]-R) / (m_max_r-R) + (1.0 - (m_r[0][0] - R) / (m_max_r-R)), 0.196 * ((m_r[0][0] - R) / (m_max_r - R)));</p>
<p class="src"></p>
<p class="src3"><span class="kom">// vykreslení vrcholu koule</span></p>
<p class="src3">glVertex3d(m_r[0][0] * cos(UHEL(0)) * sin(UHEL(0)), m_r[0][0] * cos(UHEL(0)), m_r[0][0] * sin(UHEL(0)) * sin(UHEL(0)));</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="key">for</span>(i=0; i&lt;SLICES; i++)</p>
<p class="src3"><span class="key">for</span>(j=0; j&lt;SLICES/2; j++)</p>
<p class="src3">{</p>
<p class="src4"><span class="key">if</span> (m_r[i][(j + 1) % (SLICES / 2)] &lt;= R)<span class="kom">// pod hladinou moøe</span></p>
<p class="src4">{</p>
<p class="src5">glColor3d(0, 0, 0.9);<span class="kom">// modrá barva</span></p>
<p class="src5"><span class="kom">// vykreslení vrcholu koule</span></p>
<p class="src5">glVertex3d(R * cos(UHEL(i)) * sin(UHEL(j + 1)), R * cos(UHEL(j + 1)), R * sin(UHEL(i)) * sin(UHEL(j + 1)));</p>
<p class="src4">}</p>
<p class="src4"><span class="key">else</span></p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// barva mezi zelenou a hnìdou (podle nadmoøské vý¹ky)</span></p>
<p class="src5">glColor3d(0.455 * ((m_r[i][(j + 1) % (SLICES/2)] - R) / (m_max_r - R)), 0.39 * (m_r[i][(j + 1) % (SLICES / 2)] - R) / (m_max_r - R) + (1.0 - (m_r[i][(j + 1) % (SLICES / 2)] - R) / (m_max_r - R)), 0.196 * ((m_r[i][(j + 1) % (SLICES / 2)] - R) / (m_max_r - R)));</p>
<p class="src"></p>
<p class="src5"><span class="kom">// vykreslení vrcholu koule</span></p>
<p class="src5">glVertex3d(m_r[i][(j + 1) % (SLICES / 2)] * cos(UHEL(i)) * sin(UHEL(j + 1)), m_r[i][(j + 1) % (SLICES / 2)] * cos(UHEL(j + 1)), m_r[i][(j + 1) % (SLICES / 2)] * sin(UHEL(i)) * sin(UHEL(j + 1)));</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4"><span class="key">if</span> (m_r[(i+1)%SLICES][j] &lt;= R)<span class="kom">// pod hladinou moøe</span></p>
<p class="src4">{</p>
<p class="src5">glColor3d(0, 0, 0.9);<span class="kom">// modrá barva</span></p>
<p class="src5"><span class="kom">// vykreslení vrcholu koule</span></p>
<p class="src5">glVertex3d(R * cos(UHEL(i + 1)) * sin(UHEL(j)), R * cos(UHEL(j)), R * sin(UHEL(i + 1)) * sin(UHEL(j)));</p>
<p class="src4">}</p>
<p class="src4"><span class="key">else</span></p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// barva mezi zelenou a hnìdou (podle nadmoøské vý¹ky)</span></p>
<p class="src5">glColor3d(0.455 * ((m_r[(i + 1) % SLICES][j] - R) / (m_max_r - R)), 0.39 * (m_r[(i + 1) % SLICES][j] - R) / (m_max_r - R) + (1.0 - (m_r[(i + 1) % SLICES][j] - R) / (m_max_r - R)), 0.196 * ((m_r[(i + 1) % SLICES][j] - R) / (m_max_r - R)));</p>
<p class="src"></p>
<p class="src5"><span class="kom">// vykreslení vrcholu koule</span></p>
<p class="src5">glVertex3d(m_r[(i + 1) % SLICES][j] * cos(UHEL(i + 1)) * sin(UHEL(j)), m_r[(i + 1) % SLICES][j] * cos(UHEL(j)), m_r[(i + 1) % SLICES][j] * sin(UHEL(i + 1)) * sin(UHEL(j)));</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>Pro úplnost zde uvedu je¹tì výpis funkce Reset(), konstruktoru a destruktoru.</p>

<p class="src0"><span class="key">void</span> CPlanet::Reset(<span class="key">const</span> <span class="key">double</span> r)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// v¹echny vrcholy se nastaví na výchozí polomìr</span></p>
<p class="src1">m_default_r=r;</p>
<p class="src1">register <span class="key">int</span> i,j;</p>
<p class="src"></p>
<p class="src1"><span class="key">for</span>(i=0; i&lt;SLICES; i++)</p>
<p class="src2"><span class="key">for</span>(j=0; j&lt;SLICES/2; j++)</p>
<p class="src3">m_r[i][j]=R;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">CPlanet::CPlanet()</p>
<p class="src0">{</p>
<p class="src1">Reset(20);</p>
<p class="src1">srand( (<span class="key">unsigned</span>)time( NULL ) );</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">CPlanet::~CPlanet()</p>
<p class="src0">{</p>
<p class="src"></p>
<p class="src0">}</p>

<h3>Náprava nedostatku</h3>

<p>Vý¹e uvedený nedostatek, tj. ¾e tam, kde se na jedné stranì planety vyskytuje kontinent je na druhé stranì moøe, lze obejít jednodu¹e tak, ¾e kouli nerozdìlujete støedem, ale libovolným bodem, který do ní patøí. Pro definování tohoto bodu se mù¾e pou¾ít normálový vektor plochy, jen musíme trochu upravit zpùsob jeho výpoètu - nestaèí pouze jednotkový normálový vektor, ale musí se volit (o náhodné velikosti) men¹í ne¾ je polomìr koule.</p>

<p class="src0">nx = <span class="key">double</span>(rand() % <span class="key">int</span>(R)) * ((<span class="key">double</span>(rand()) / <span class="key">double</span>(RAND_MAX)) - 0.5);</p>
<p class="src0">ny = <span class="key">double</span>(rand() % <span class="key">int</span>(R)) * ((<span class="key">double</span>(rand()) / <span class="key">double</span>(RAND_MAX)) - 0.5);</p>
<p class="src0">nz = <span class="key">double</span>(rand() % <span class="key">int</span>(R)) * ((<span class="key">double</span>(rand()) / <span class="key">double</span>(RAND_MAX)) - 0.5);</p>

<p>Je¹tì je nutné modifikovat zpùsob výpoètu promìnné <span class="src">ns</span>, tak aby se poèítalo od bodu daného normálovým vektorem.</p>

<p class="src0">ns = nx * (x - nx) + ny * (y - ny) + nz * (z - nz);</p>

<p>Tím ale vznikne dal¹í problém. Proto¾e normálový vektor ukazuje v¾dy od støedu k dìlící rovinì, vìt¹í èást koule se bude v¾dy zmen¹ovat. To lze ale odstranit následující jednoduchou úpravou podmínky rozhodující, zda zmen¹ovat nebo zvìt¹ovat.</p>

<p class="src0"><span class="key">if</span> (m * ns &gt;= 0)<span class="kom">// úhel mezi vektory men¹í nebo roven 90 stupòù</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// zvý¹it vrchol</span></p>
<p class="src1">m_r[i][j] += 1e-3 * R;</p>
<p class="src0">}</p>
<p class="src0"><span class="key">else</span><span class="kom">// úhel mezi vektory vìt¹í ne¾ 90 stupòù</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// sní¾it vrchol</span></p>
<p class="src1">m_r[i][j] -= 1e-3 * R;</p>
<p class="src0">}</p>

<p>Jediný rozdíl od pøedcházejícího kódu je v násobení promìnnou m, která má hodnotu buï plus nebo mínus jedna (náhodnì). Tuto hodnotu volíme v¾dy pouze jednou pro ka¾dý výpoèet normálového vektoru. Nejlep¹í je umístit následující øádek hned za výpoèet promìnných nx, ny, nz.</p>

<p class="src0">m = ((rand() % 2) ? -1 : 1)</p>

<p>Výsledek upraveného algoritmu bude vypadat napøíklad takto:</p>

<div class="okolo_img">
<img src="images/clanky/cl_gl_planety/land12.gif" width="222" height="222" alt="Èelní pohled">
<img src="images/clanky/cl_gl_planety/land13.gif" width="222" height="222" alt="Pohled zezadu">
</div>

<p>... mezi pøední a zadní stranou u¾ není ¾ádná shoda.</p>

<p class="autor">napsal: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>


<h3>Anglický originál</h3>

<ul class="zdroj_kody">
<li><?OdkazBlank('http://freespace.virgin.net/hugo.elias/models/m_landsp.htm');?></li>
</ul>


<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/planety.rar');?> - Ukázkové demo ve Visual C++</li>
</ul>

<div class="okolo_img"><img src="images/clanky/cl_gl_planety/planety.jpg" width="640" height="480" alt="Generování planet" /></div>

<?
include 'p_end.php';
?>
