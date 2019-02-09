<?
$g_title = 'CZ NeHe OpenGL - Lekce 27 - Stíny';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(27);?>

<h1>Lekce 27 - Stíny</h1>

<p class="nadpis_clanku">Pøedstavuje se vám velmi komplexní tutoriál na vrhání stínù. Efekt je doslova neuvìøitelný. Stíny se roztahují, ohýbají a zahalují i ostatní objekty ve scénì. Realisticky se pokroutí na stìnách nebo podlaze. Se v¹ím lze pomocí klávesnice pohybovat ve 3D prostoru. Pokud je¹tì nejste se stencil bufferem a matematikou jako jedna rodina, nemáte nejmen¹í ¹anci.</p>

<p>Tento tutoriál má trochu jiný pøístup - sumarizuje v¹echny va¹e znalosti o OpenGL a pøidává spoustu dal¹ích. Ka¾dopádnì byste mìli stoprocentnì chápat nastavování a práci se stencil bufferem. Pokud máte pocit, ¾e v nìèem existují mezery, zkuste se vrátit ke ètení døívìj¹ích lekcí. Mimo jiné byste také mìli mít alespoò malé znalosti o analytické geometrii (vektory, rovnice pøímek a rovin, násobení matic...) - urèitì mìjte po ruce nìjakou knihu. Já osobnì pou¾ívám zápisky z matematiky prvního semestru na univerzitì. V¾dy jsem vìdìl, ¾e se nìkdy budou hodit.</p>

<p>Nyní u¾ ale ke kódu. Aby byl program pøehledný, definujeme nìkolik struktur. První z nich, sPoint, vyjadøuje bod nebo vektor v prostoru. Ukládá jeho x, y, z souøadnice.</p>

<p class="src0">struct sPoint<span class="kom">// Souøadnice bodu nebo vektoru</span></p>
<p class="src0">{</p>
<p class="src1">float x, y, z;</p>
<p class="src0">};</p>

<p>Struktura sPlaneEq ukládá hodnoty a, b, c, d obecné rovnice roviny, která je definována vzorcem ax + by + cz + d = 0.</p>

<p class="src0">struct sPlaneEq<span class="kom">// Rovnice roviny</span></p>
<p class="src0">{</p>
<p class="src1">float a, b, c, d;<span class="kom">// Ve tvaru ax + by + cz + d = 0</span></p>
<p class="src0">};</p>

<p>Struktura sPlane obsahuje v¹echny informace potøebné k popsání trojúhelníku, který vrhá stín. Instance tìchto struktur budou reprezentovat facy (èelo, stìna - nebudu pøekládat, proto¾e je tento termín hodnì pou¾ívaný i v èe¹tinì) trojúhelníkù. Facem se rozumí stìna trojúhelníku, která je pøivrácená nebo odvrácená od pozorovatele. Jeden trojúhelník má v¾dy dva facy.</p>

<p>Pole p[3] definuje tøi indexy v poli vertexù objektu, které dohromady tvoøí tento trojúhelník. Druhé trojrozmìrné pole, normals[3], zastupuje normálový vektor ka¾dého rohu. Tøetí pole specifikuje indexy sousedních facù. PlaneEq urèuje rovnici roviny, ve které le¾í tento face a parametr visible oznamuje, jestli je face pøivrácený (viditelný) ke zdroji svìtla nebo ne.</p>

<p class="src0">struct sPlane<span class="kom">// Popisuje jeden face objektu</span></p>
<p class="src0">{</p>
<p class="src1">unsigned int p[3];<span class="kom">// Indexy 3 vertexù v objektu, které vytváøejí tento face</span></p>
<p class="src1">sPoint normals[3];<span class="kom">// Normálové vektory ka¾dého vertexu</span></p>
<p class="src1">unsigned int neigh[3];<span class="kom">// Indexy sousedních facù</span></p>
<p class="src"></p>
<p class="src1">sPlaneEq PlaneEq;<span class="kom">// Rovnice roviny facu</span></p>
<p class="src1">bool visible;<span class="kom">// Je face viditelný (pøivrácený ke svìtlu)?</span></p>
<p class="src0">};</p>

<p>Poslední struktura, glObject, je mezi právì definovanými strukturami na nejvy¹¹í úrovni. Promìnné nPoints a nPlanes urèují poèet prvkù, které pou¾íváme v polích points a planes.</p>

<p class="src0">struct glObject<span class="kom">// Struktura objektu</span></p>
<p class="src0">{</p>
<p class="src1">GLuint nPoints;<span class="kom">// Poèet vertexù</span></p>
<p class="src1">sPoint points[100];<span class="kom">// Pole vertexù</span></p>
<p class="src"></p>
<p class="src1">GLuint nPlanes;<span class="kom">// Poèet facù</span></p>
<p class="src1">sPlane planes[200];<span class="kom">// Pole facù</span></p>
<p class="src0">};</p>

<p>GLvector4f a GLmatrix16f jsou pomocné datové typy, které definujeme pro snadnìj¹í pøedávání parametrù funkci VMatMult(). Více pozdìji.</p>

<p class="src0">typedef float GLvector4f[4];<span class="kom">// Nový datový typ</span></p>
<p class="src0">typedef float GLmatrix16f[16];<span class="kom">// Nový datový typ</span></p>

<p>Nadefinujeme promìnné. Obj je objektem, který vrhá stín. Pole ObjPos[] definuje jeho polohu, roty jsou úhlem natoèení na osách x, y a speedy jsou rychlosti otáèení.</p>

<p class="src0">glObject obj;<span class="kom">// Objekt, který vrhá stín</span></p>
<p class="src"></p>
<p class="src0">float ObjPos[] = { -2.0f, -2.0f, -5.0f };<span class="kom">// Pozice objektu</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot = 0, xspeed = 0;<span class="kom">// X rotace a x rychlost rotace objektu</span></p>
<p class="src0">GLfloat yrot = 0, yspeed = 0;<span class="kom">// Y rotace a y rychlost rotace objektu</span></p>

<p>Následující ètyøi pole definují svìtlo a dal¹í ètyøi pole materiál. Pou¾ijeme je pøedev¹ím v InitGL() pøi inicializaci scény.</p>

<p class="src0">float LightPos[] = { 0.0f, 5.0f,-4.0f, 1.0f };<span class="kom">// Pozice svìtla</span></p>
<p class="src0">float LightAmb[] = { 0.2f, 0.2f, 0.2f, 1.0f };<span class="kom">// Ambient svìtlo</span></p>
<p class="src0">float LightDif[] = { 0.6f, 0.6f, 0.6f, 1.0f };<span class="kom">// Diffuse svìtlo</span></p>
<p class="src0">float LightSpc[] = { -0.2f, -0.2f, -0.2f, 1.0f };<span class="kom">// Specular svìtlo</span></p>
<p class="src"></p>
<p class="src0">float MatAmb[] = { 0.4f, 0.4f, 0.4f, 1.0f };<span class="kom">// Materiál - Ambient hodnoty (prostøedí, atmosféra)</span></p>
<p class="src0">float MatDif[] = { 0.2f, 0.6f, 0.9f, 1.0f };<span class="kom">// Materiál - Diffuse hodnoty (rozptylování svìtla)</span></p>
<p class="src0">float MatSpc[] = { 0.0f, 0.0f, 0.0f, 1.0f };<span class="kom">// Materiál - Specular hodnoty (zrcadlivost)</span></p>
<p class="src0">float MatShn[] = { 0.0f };<span class="kom">// Materiál - Shininess hodnoty (lesk)</span></p>

<p>Poslední dvì promìnné jsou pro kouli, na kterou dopadá stín objektu.</p>

<p class="src0">GLUquadricObj *q;<span class="kom">// Quadratic pro kreslení koule</span></p>
<p class="src0">float SpherePos[] = { -4.0f, -5.0f, -6.0f };<span class="kom">// Pozice koule</span></p>

<p>Struktura datového souboru, který pou¾íváme pro definici objektu, není a¾ tak slo¾itá, jak na první pohled vypadá. Soubor se dìlí do dvou èástí: jedna èást pro vertexy a  druhá pro facy. První èíslo první èásti urèuje poèet vertexù a po nìm následují jejich definice. Druhá èást zaèíná specifikací poètu facù. Na ka¾dém dal¹ím øádku je celkem dvanáct èísel. První tøi pøedstavují indexy do pole vertexù (ka¾dý face má tøi vrcholy) a zbylých devìt hodnot urèuje tøi normálové vektory (pro ka¾dý vrchol jeden). To je v¹e. Abych nezapomnìl v adresáøi Data mù¾ete najít je¹tì tøi podobné soubory.</p>

<p class="src0"><span class="kom">24</span></p>
<p class="src0"><span class="kom">-2  0.2 -0.2</span></p>
<p class="src0"><span class="kom">2  0.2 -0.2</span></p>
<p class="src0"><span class="kom">2  0.2  0.2</span></p>
<p class="src0"><span class="kom">-2  0.2  0.2</span></p>
<p class="src0"><span class="kom">-2 -0.2 -0.2</span></p>
<p class="src0"><span class="kom">2 -0.2 -0.2</span></p>
<p class="src0"><span class="kom">2 -0.2  0.2</span></p>
<p class="src0"><span class="kom">-2 -0.2  0.2</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">-0.2  2 -0.2</span></p>
<p class="src0"><span class="kom">0.2  2 -0.2</span></p>
<p class="src0"><span class="kom">0.2  2  0.2</span></p>
<p class="src0"><span class="kom">0.2  2  0.2</span></p>
<p class="src0"><span class="kom">-0.2 -2 -0.2</span></p>
<p class="src0"><span class="kom">0.2 -2 -0.2</span></p>
<p class="src0"><span class="kom">0.2 -2  0.2</span></p>
<p class="src0"><span class="kom">-0.2 -2  0.2</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">-0.2  0.2 -2</span></p>
<p class="src0"><span class="kom">0.2  0.2 -2</span></p>
<p class="src0"><span class="kom">0.2  0.2  2</span></p>
<p class="src0"><span class="kom">-0.2  0.2  2</span></p>
<p class="src0"><span class="kom">-0.2 -0.2 -2</span></p>
<p class="src0"><span class="kom">0.2 -0.2 -2</span></p>
<p class="src0"><span class="kom">0.2 -0.2  2</span></p>
<p class="src0"><span class="kom">-0.2 -0.2  2</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">36</span></p>
<p class="src0"><span class="kom">1 3 2 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">1 4 3 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">5 6 7 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">5 7 8 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">5 4 1 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">5 8 4 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">3 6 2 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">3 7 6 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">5 1 2 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">5 2 6 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">3 4 8 0 0 1 0 0 1 0 0 1</span></p>
<p class="src0"><span class="kom">3 8 7 0 0 1 0 0 1 0 0 1</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">9 11 10 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">9 12 11 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">13 14 15 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">13 15 16 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">13 12 9 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">13 16 12 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">11 14 10 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">11 15 14 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">13 9 10 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">13 10 14 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">11 12 16 0 0 1 0 0 1 0 0 1</span></p>
<p class="src0"><span class="kom">11 16 15 0 0 1 0 0 1 0 0 1</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">17 19 18 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">17 20 19 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">21 22 23 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">21 23 24 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">21 20 17 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">21 24 20 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">19 22 18 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">19 23 22 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">21 17 18 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">21 18 22 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">19 20 24 0 0 1 0 0 1 0 0 1</span></p>
<p class="src0"><span class="kom">19 24 23 0 0 1 0 0 1 0 0 1</span></p>

<p>Právì pøedstavený soubor nahrává funkce ReadObject(). Pro pochopení podstaty by mìly staèit komentáøe.</p>

<p class="src0">inline int ReadObject(char *st, glObject *o)<span class="kom">// Nahraje objekt</span></p>
<p class="src0">{</p>
<p class="src1">FILE *file;<span class="kom">// Handle souboru</span></p>
<p class="src1">unsigned int i;<span class="kom">// Øídící promìnná cyklù</span></p>
<p class="src"></p>
<p class="src1">file = fopen(st, &quot;r&quot;);<span class="kom">// Otevøe soubor pro ètení</span></p>
<p class="src"></p>
<p class="src1">if (!file)<span class="kom">// Podaøilo se ho otevøít?</span></p>
<p class="src2">return FALSE;<span class="kom">// Pokud ne - konec funkce</span></p>
<p class="src"></p>
<p class="src1">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;nPoints));<span class="kom">// Naètení poètu vertexù</span></p>
<p class="src"></p>
<p class="src1">for (i = 1; i &lt;= o-&gt;nPoints; i++)<span class="kom">// Naèítá vertexy</span></p>
<p class="src1">{</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;points[i].x));<span class="kom">// Jednotlivé x, y, z slo¾ky</span></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;points[i].y));</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;points[i].z));</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;nPlanes));<span class="kom">// Naètení poètu facù</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; o-&gt;nPlanes; i++)<span class="kom">// Naèítá facy</span></p>
<p class="src1">{</p>
<p class="src2">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;planes[i].p[0]));<span class="kom">// Naètení indexù vertexù</span></p>
<p class="src2">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;planes[i].p[1]));</p>
<p class="src2">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;planes[i].p[2]));</p>
<p class="src"></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[0].x));<span class="kom">// Normálové vektory prvního vertexu</span></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[0].y));</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[0].z));</p>
<p class="src"></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[1].x));<span class="kom">// Normálové vektory druhého vertexu</span></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[1].y));</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[1].z));</p>
<p class="src"></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[2].x));<span class="kom">// Normálové vektory tøetího vertexu</span></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[2].y));</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[2].z));</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹e v poøádku</span></p>
<p class="src0">}</p>

<p>Díky funkci SetConnectivity() zaèínají být vìci zajímavé :-) Hledáme v ní ke ka¾dému facu tøi sousední facy, se kterými má spoleènou hranu. Proto¾e je zdrojový kód, abych tak øekl, trochu hùøe pochopitelný, pøidávám i pseudo kód, který by mohl situaci malièko objasnit.</p>

<p class="src0"><span class="kom">Zaèátek funkce</span></p>
<p class="src0"><span class="kom">{</span></p>
<p class="src1"><span class="kom">Postupnì se prochází ka¾dý face (A) v objektu</span></p>
<p class="src1"><span class="kom">{</span></p>
<p class="src2"><span class="kom">V ka¾dém prùchodu se znovu prochází v¹echny facy (B) objektu (zji¹»uje se sousedství A s B)</span></p>
<p class="src2"><span class="kom">{</span></p>
<p class="src3"><span class="kom">Dále se projdou v¹echny hrany facu A</span></p>
<p class="src3"><span class="kom">{</span></p>
<p class="src4"><span class="kom">Pokud aktuální hrana je¹tì nemá pøiøazeného souseda</span></p>
<p class="src4"><span class="kom">{</span></p>
<p class="src5"><span class="kom">Projdou se v¹echny hrany facu B</span></p>
<p class="src5"><span class="kom">{</span></p>
<p class="src6"><span class="kom">Provedou se výpoèty, kterými se zjistí, jestli je okraj A stejný jako okraj B</span></p>
<p class="src6"><span class="kom">Pokud ano</span></p>
<p class="src6"><span class="kom">{</span></p>
<p class="src7"><span class="kom">Nastaví se soused v A</span></p>
<p class="src7"><span class="kom">Nastaví se soused v B</span></p>
<p class="src6"><span class="kom">}</span></p>
<p class="src5"><span class="kom">}</span></p>
<p class="src4"><span class="kom">}</span></p>
<p class="src3"><span class="kom">}</span></p>
<p class="src2"><span class="kom">}</span></p>
<p class="src1"><span class="kom">}</span></p>
<p class="src0"><span class="kom">}</span></p>
<p class="src0"><span class="kom">Konec funkce</span></p>

<p>U¾ chápete?</p>

<p class="src0">inline void SetConnectivity(glObject *o)<span class="kom">// Nastavení sousedù jednotlivých facù</span></p>
<p class="src0">{</p>
<p class="src1">unsigned int p1i, p2i, p1j, p2j;<span class="kom">// Pomocné promìnné</span></p>
<p class="src1">unsigned int P1i, P2i, P1j, P2j;<span class="kom">// Pomocné promìnné</span></p>
<p class="src1">unsigned int i, j, ki, kj;<span class="kom">// Øídící promìnné cyklù</span></p>
<p class="src"></p>
<p class="src1">for(i = 0; i &lt; o-&gt;nPlanes-1; i++)<span class="kom">// Ka¾dý face objektu (A)</span></p>
<p class="src1">{</p>
<p class="src2">for(j = i+1; j &lt; o-&gt;nPlanes; j++)<span class="kom">// Ka¾dý face objektu (B)</span></p>
<p class="src2">{</p>
<p class="src3">for(ki = 0; ki &lt; 3; ki++)<span class="kom">// Ka¾dý okraj facu (A)</span></p>
<p class="src3">{</p>
<p class="src4">if(!o-&gt;planes[i].neigh[ki])<span class="kom">// Okraj je¹tì nemá souseda?</span></p>
<p class="src4">{</p>
<p class="src5">for(kj = 0; kj &lt; 3; kj++)<span class="kom">// Ka¾dý okraj facu (B)</span></p>
<p class="src5">{</p>

<p>Nalezením dvou vertexù, které oznaèují konce hrany a jejich porovnáním mù¾eme zjistit, jestli mají spoleèný okraj. Èást (kj+1) % 3 oznaèuje vertex umístìný vedle toho, o kterém uva¾ujeme. Ovìøíme, jestli jsou vertexy stejné. Proto¾e mù¾e být jejich poøadí rozdílné musíme testovat obì mo¾nosti.</p>

<p class="src6"><span class="kom">// Výpoèty pro zji¹tìní sousedství</span></p>
<p class="src6">p1i = ki;</p>
<p class="src6">p1j = kj;</p>
<p class="src"></p>
<p class="src6">p2i = (ki+1) % 3;</p>
<p class="src6">p2j = (kj+1) % 3;</p>
<p class="src"></p>
<p class="src6">p1i = o-&gt;planes[i].p[p1i];</p>
<p class="src6">p2i = o-&gt;planes[i].p[p2i];</p>
<p class="src6">p1j = o-&gt;planes[j].p[p1j];</p>
<p class="src6">p2j = o-&gt;planes[j].p[p2j];</p>
<p class="src"></p>
<p class="src6">P1i = ((p1i+p2i) - abs(p1i-p2i)) / 2;</p>
<p class="src6">P2i = ((p1i+p2i) + abs(p1i-p2i)) / 2;</p>
<p class="src6">P1j = ((p1j+p2j) - abs(p1j-p2j)) / 2;</p>
<p class="src6">P2j = ((p1j+p2j) + abs(p1j-p2j)) / 2;</p>
<p class="src"></p>
<p class="src6">if((P1i == P1j) &amp;&amp; (P2i == P2j))<span class="kom">// Jsou sousedé?</span></p>
<p class="src6">{</p>
<p class="src7">o-&gt;planes[i].neigh[ki] = j+1;</p>
<p class="src7">o-&gt;planes[j].neigh[kj] = i+1;</p>
<p class="src6">}</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Abychom se mohli alespoò trochu nadechnout :-) vypí¹i kód funkce DrawGLObject(), který je na první pohled malièko jednodu¹¹í. Jak u¾ z názvu vyplývá, vykresluje objekt.</p>

<p class="src0">void DrawGLObject(glObject o)<span class="kom">// Vykreslení objektu</span></p>
<p class="src0">{</p>
<p class="src1">unsigned int i, j;<span class="kom">// Øídící promìnné cyklù</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Kreslení trojúhelníkù</span></p>
<p class="src2">for (i = 0; i &lt; o.nPlanes; i++)<span class="kom">// Projde v¹echny facy</span></p>
<p class="src2">{</p>
<p class="src3">for (j = 0; j &lt; 3; j++)<span class="kom">// Trojúhelník má tøi rohy</span></p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// Normálový vektor a umístìní bodu</span></p>
<p class="src4">glNormal3f(o.planes[i].normals[j].x, o.planes[i].normals[j].y, o.planes[i].normals[j].z);</p>
<p class="src4">glVertex3f(o.points[o.planes[i].p[j]].x, o.points[o.planes[i].p[j]].y, o.points[o.planes[i].p[j]].z);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>Výpoèet rovnice roviny vypadá pro ne-matematika sice hodnì slo¾itì, ale je to pouze implementace matematického vzorce, který se, kdy¾ je potøeba, najde v tabulkách nebo kní¾ce.</p>

<p>Pøekl.: Malièká chybièka. Pole v[] má rozsah ètyøi prvky, ale pou¾ívají se jenom tøi. Index 0 se nikdy nepou¾ije.</p>

<p class="src0">inline void CalcPlane(glObject o, sPlane *plane)<span class="kom">// Rovnice roviny ze tøí bodù</span></p>
<p class="src0">{</p>
<p class="src1">sPoint v[4];<span class="kom">// Pomocné hodnoty</span></p>
<p class="src1">int i;<span class="kom">// Øídící promìnná cyklù</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; 3; i++)<span class="kom">// Pro zkrácení zápisu</span></p>
<p class="src1">{</p>
<p class="src2">v[i+1].x = o.points[plane-&gt;p[i]].x;<span class="kom">// Ulo¾í hodnoty do pomocných promìnných</span></p>
<p class="src2">v[i+1].y = o.points[plane-&gt;p[i]].y;</p>
<p class="src2">v[i+1].z = o.points[plane-&gt;p[i]].z;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">plane-&gt;PlaneEq.a = v[1].y*(v[2].z-v[3].z) + v[2].y*(v[3].z-v[1].z) + v[3].y*(v[1].z-v[2].z);</p>
<p class="src1">plane-&gt;PlaneEq.b = v[1].z*(v[2].x-v[3].x) + v[2].z*(v[3].x-v[1].x) + v[3].z*(v[1].x-v[2].x);</p>
<p class="src1">plane-&gt;PlaneEq.c = v[1].x*(v[2].y-v[3].y) + v[2].x*(v[3].y-v[1].y) + v[3].x*(v[1].y-v[2].y);</p>
<p class="src1">plane-&gt;PlaneEq.d = -( v[1].x*(v[2].y*v[3].z - v[3].y*v[2].z) + v[2].x*(v[3].y*v[1].z - v[1].y*v[3].z) + v[3].x*(v[1].y*v[2].z - v[2].y*v[1].z) );</p>
<p class="src0">}</p>

<p>Funkce, které jsme právì napsali se volají ve funkci InitGLObjects(). Neexistuje-li po¾adovaný soubor, vrátíme false. Pokud ale existuje, funkcí ReadObject() ho nahrajeme do pamìti, pomocí SetConnectivity() najdeme sousedící facy a potom se v cyklu spoèítáme rovnici roviny ka¾dého facu.</p>

<p class="src0">int InitGLObjects()<span class="kom">// Inicializuje objekty</span></p>
<p class="src0">{</p>
<p class="src1">if (!ReadObject(&quot;Data/Object2.txt&quot;, &amp;obj))<span class="kom">// Nahraje objekt</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Pøi chybì konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SetConnectivity(&amp;obj);<span class="kom">// Pospojuje facy (najde sousedy)</span></p>
<p class="src"></p>
<p class="src1">for (unsigned int i = 0; i &lt; obj.nPlanes; i++)<span class="kom">// Prochází facy</span></p>
<p class="src2">CalcPlane(obj, &amp;(obj.planes[i]));<span class="kom">// Spoèítá rovnici roviny facu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹e v poøádku</span></p>
<p class="src0">}</p>

<p>Nyní pøichází funkce, která renderuje stín. Na zaèátku nastavíme v¹echny potøebné parametry OpenGL a poté, ne na obrazovku, ale do stencil bufferu, vyrenderujeme stín. Dále vykreslíme vepøedu pøed scénu velký ¹edý obdélník. Tam, kde byl stencil buffer modifikován se zobrazí ¹edé plochy - stín.</p>

<p class="src0">void CastShadow(glObject *o, float *lp)<span class="kom">// Vr¾ení stínu</span></p>
<p class="src0">{</p>
<p class="src1">unsigned int i, j, k, jj;<span class="kom">// Pomocné</span></p>
<p class="src1">unsigned int p1, p2;<span class="kom">// Dva body okraje vertexu, které vrhají stín</span></p>
<p class="src1">sPoint v1, v2;<span class="kom">// Vektor mezi svìtlem a pøedchozími body</span></p>

<p>Nejprve urèíme, které povrchy jsou pøivrácené ke svìtlu a to tak, ¾e zjistíme, která strana facu je osvìtlená. Provedeme to velice jednodu¹e: máme rovnici roviny (ax + by + cz + d = 0) i polohu svìtla, tak¾e dosadíme x, y, z koordináty svìtla do rovnice. Nezajímá nás hodnota, ale znaménko výsledku. Pokud bude výsledek vìt¹í ne¾ nula, míøí normálový vektor roviny na stranu ke svìtlu a rovina je osvìtlená. Pøi záporném èísle míøí vektor od svìtla, rovina je od nìj odvrácená. Vy¹el-li by výsledek nula, bude svìtlo le¾et v rovinì facu, ale tím se nebudeme zabývat.</p>

<p class="src1">float side;<span class="kom">// Pomocná promìnná</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; o-&gt;nPlanes; i++)<span class="kom">// Projde v¹echny facy objektu</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Rozhodne jestli je face pøivrácený nebo odvrácený od svìtla</span></p>
<p class="src2">side = o-&gt;planes[i].PlaneEq.a * lp[0] + o-&gt;planes[i].PlaneEq.b * lp[1] + o-&gt;planes[i].PlaneEq.c * lp[2] + o-&gt;planes[i].PlaneEq.d * lp[3];</p>
<p class="src"></p>
<p class="src2">if (side &gt; 0)<span class="kom">// Je pøivrácený?</span></p>
<p class="src2">{</p>
<p class="src3">o-&gt;planes[i].visible = TRUE;</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Není</span></p>
<p class="src2">{</p>
<p class="src3">o-&gt;planes[i].visible = FALSE;</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Nastavíme parametry OpenGL, které jsou nutné pro vr¾ení stínu. Vypneme svìtla, proto¾e nebudeme renderovat do color bufferu (výstup na obrazovku), ale pouze do stencil bufferu. Ze stejného dùvodu zaká¾eme pomocí glColorMask() vykreslování na obrazovku. Aèkoli je testování hloubky stále zapnuté, nechceme, aby stíny byly v depth bufferu reprezentovány pevnými objekty. Jako prevenci tedy nastavíme masku hloubky na GL_FALSE. Nakonec nastavíme stencil buffer tak, aby na místa v nìm oznaèená mohly být vykresleny stíny.</p>

<p class="src1">glDisable(GL_LIGHTING);<span class="kom">// Vypne svìtla</span></p>
<p class="src1">glDepthMask(GL_FALSE);<span class="kom">// Vypne zápis do depth bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Funkce depth bufferu</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_STENCIL_TEST);<span class="kom">// Zapne stencilové testy</span></p>
<p class="src1">glColorMask(0, 0, 0, 0);<span class="kom">// Nekreslit na obrazovky</span></p>
<p class="src1">glStencilFunc(GL_ALWAYS, 1, 0xffffffff);<span class="kom">// Funkce stencilu</span></p>

<p>Proto¾e máme zapnuté oøezávání zadních stran trojúhelníkù (viz. InitGL()), specifikujeme, které strany jsou pøední. Také nastavíme stencil buffer tak, aby se v nìm pøi kreslení zvìt¹ovaly hodnoty.</p>

<p class="src1">glFrontFace(GL_CCW);<span class="kom">// Èelní stìna proti smìru hodinových ruèièek</span></p>
<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_INCR);<span class="kom">// Zvy¹ování hodnoty stencilu</span></p>

<p>V cyklu projdeme ka¾dý face a pokud je oznaèen jako viditelný (pøivrácený ke svìtlu), zkontrolujeme v¹echny jeho okraje. Pokud vedle nìj není ¾ádný sousední face nebo sice má souseda, který ale není viditelný, na¹li jsme okraj objektu, který vrhá stín. Pokud se nad tìmito dvìma podmínkami zamyslíte, zjistíte, ¾e jsou pravdivé. Získali jsme první dvì souøadnice ètyøúhelníku, který je stìnou stínu. V tomto pøípadì si pøedstavte stín jako oblast, který je ohranièena na jedné stranì objektem bránícím prùchodu svìtelných paprskù, z druhé strany promítací rovinou (stìna místnosti) a na okrajích ètyøúhelníky, které se právì sna¾íme vykreslit. U¾ je to trochu jasnìj¹í?</p>

<p class="src1">for (i = 0; i &lt; o-&gt;nPlanes; i++)<span class="kom">// Ka¾dý face objektu</span></p>
<p class="src1">{</p>
<p class="src2">if (o-&gt;planes[i].visible)<span class="kom">// Je pøivrácený ke svìtlu</span></p>
<p class="src2">{</p>
<p class="src3">for (j = 0; j &lt; 3; j++)<span class="kom">// Ka¾dý okraj facu</span></p>
<p class="src3">{</p>
<p class="src4">k = o-&gt;planes[i].neigh[j];<span class="kom">// Index souseda (pomocný)</span></p>

<p>Nyní zjistíme, jestli je vedle aktuálního okraje face, který buï není viditelný nebo vùbec neexistuje (nemá souseda). Pokud podmínka platí, na¹li jsme okraj objektu, který vrhá stín.</p>

<p class="src4"><span class="kom">// Pokud nemá souseda, který je pøivrácený ke svìtlu</span></p>
<p class="src4">if ((!k) || (!o-&gt;planes[k-1].visible))</p>
<p class="src4">{</p>

<p>Rohy hrany právì ovìøovaného trojúhelníku udávají první dva body stínu. Dal¹í dva získáme spoèítáním smìrového vektoru, který vychází ze svìtla, prochází bodem p1 popø. p2 a díky násobení stem pokraèuje ve stejném smìru nìkam do hlubin scény. Násobení stem bychom si mohli pøedstavit jako mìøítko pro prodlou¾ení vektoru a tudí¾ i polygonu, aby dosáhl a¾ k promítací rovinì a neskonèil nìkde pøed ní.</p>

<p>Kreslení stínu hrubou silou pou¾ité zde, není zrovna nejvhodnìj¹í, proto¾e má velmi velké nároky na grafickou kartu. Nekreslíme toti¾ pouze k promítací rovinì, ale a¾ za ni kód této lekce. ( * 100). Pro vìt¹í úèinnost by bylo vhodné modifikovat tento algoritmus tak, aby se polygony stínu oøezaly objektem, na který dopadá. Tento postup by ov¹em byl mnohem nároènìj¹í na vymy¹lení a asi by byl problematický sám o sobì.</p>

<p class="src5"><span class="kom">// Na¹li jsme okraj objektu, který vrhá stín - nakreslíme polygon</span></p>
<p class="src5">p1 = o-&gt;planes[i].p[j];<span class="kom">// První bod okraje</span></p>
<p class="src5">jj = (j+1) % 3;<span class="kom">// Pro získání druhého okraje</span></p>
<p class="src5">p2 = o-&gt;planes[i].p[jj];<span class="kom">// Druhý bod okraje</span></p>
<p class="src"></p>
<p class="src5"><span class="kom">// Délka vektoru</span></p>
<p class="src5">v1.x = (o-&gt;points[p1].x - lp[0]) * 100;</p>
<p class="src5">v1.y = (o-&gt;points[p1].y - lp[1]) * 100;</p>
<p class="src5">v1.z = (o-&gt;points[p1].z - lp[2]) * 100;</p>
<p class="src"></p>
<p class="src5">v2.x = (o-&gt;points[p2].x - lp[0]) * 100;</p>
<p class="src5">v2.y = (o-&gt;points[p2].y - lp[1]) * 100;</p>
<p class="src5">v2.z = (o-&gt;points[p2].z - lp[2]) * 100;</p>

<p>Zbytek u¾ je celkem snadný. Máme dva body s délkou a tak vykreslíme ètyøúhelník - jeden z mnoha okrajù stínu.</p>

<p class="src5">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Nakreslí okrajový polygon stínu</span></p>
<p class="src6">glVertex3f(o-&gt;points[p1].x, o-&gt;points[p1].y, o-&gt;points[p1].z);</p>
<p class="src6">glVertex3f(o-&gt;points[p1].x + v1.x, o-&gt;points[p1].y + v1.y, o-&gt;points[p1].z + v1.z);</p>
<p class="src6">glVertex3f(o-&gt;points[p2].x, o-&gt;points[p2].y, o-&gt;points[p2].z);</p>
<p class="src6">glVertex3f(o-&gt;points[p2].x + v2.x, o-&gt;points[p2].y + v2.y, o-&gt;points[p2].z + v2.z);</p>
<p class="src5">glEnd();</p>

<p>V cyklech zùstaneme tak dlouho, dokud nenajdeme a nevykreslíme v¹echny okraje stínu.</p>

<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Nejjednodu¹¹í a nejpochopitelnìj¹í vysvìtlení toho, proè vykreslujeme to samé je¹tì jednou, je obrázek - stíny budou pouze tam, kde být mají. Pøi vykreslování se nyní budou hodnoty ve stencil bufferu sni¾ovat. Také si v¹imnìte, ¾e funkcí glFrontFace() budeme oøezávat opaèné strany trojúhelníkù.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_27_1.jpg" width="200" height="150" alt="Bez druhého kreslení" />
<img src="images/nehe_tut/tut_27_2.jpg" width="200" height="150" alt="Se druhým kreslením" />
</div>

<p class="src1">glFrontFace(GL_CW);<span class="kom">// Èelní stìna po smìru hodinových ruèièek</span></p>
<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_DECR);<span class="kom">// Sni¾ování hodnoty stencilu</span></p>
<p class="src"></p>
<p class="src1">for (i=0; i &lt; o-&gt;nPlanes; i++)<span class="kom">// Ka¾dý face objektu</span></p>
<p class="src1">{</p>
<p class="src2">if (o-&gt;planes[i].visible)<span class="kom">// Je pøivrácený ke svìtlu</span></p>
<p class="src2">{</p>
<p class="src3">for (j = 0; j &lt; 3; j++)<span class="kom">// Ka¾dý okraj facu</span></p>
<p class="src3">{</p>
<p class="src4">k = o-&gt;planes[i].neigh[j];<span class="kom">// Index souseda (pomocný)</span></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Pokud nemá souseda, který je pøivrácený ke svìtlu</span></p>
<p class="src4">if ((!k) || (!o-&gt;planes[k-1].visible))</p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// Na¹li jsme okraj objektu, který vrhá stín - nakreslíme polygon</span></p>
<p class="src5">p1 = o-&gt;planes[i].p[j];<span class="kom">// První bod okraje</span></p>
<p class="src5">jj = (j+1) % 3;<span class="kom">// Pro získání druhého okraje</span></p>
<p class="src5">p2 = o-&gt;planes[i].p[jj];<span class="kom">// Druhý bod okraje</span></p>
<p class="src"></p>
<p class="src5"><span class="kom">// Délka vektoru</span></p>
<p class="src5">v1.x = (o-&gt;points[p1].x - lp[0])*100;</p>
<p class="src5">v1.y = (o-&gt;points[p1].y - lp[1])*100;</p>
<p class="src5">v1.z = (o-&gt;points[p1].z - lp[2])*100;</p>
<p class="src"></p>
<p class="src5">v2.x = (o-&gt;points[p2].x - lp[0])*100;</p>
<p class="src5">v2.y = (o-&gt;points[p2].y - lp[1])*100;</p>
<p class="src5">v2.z = (o-&gt;points[p2].z - lp[2])*100;</p>
<p class="src"></p>
<p class="src5">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Nakreslí okrajový polygon stínu</span></p>
<p class="src6">glVertex3f(o-&gt;points[p1].x, o-&gt;points[p1].y, o-&gt;points[p1].z);</p>
<p class="src6">glVertex3f(o-&gt;points[p1].x + v1.x, o-&gt;points[p1].y + v1.y, o-&gt;points[p1].z + v1.z);</p>
<p class="src6">glVertex3f(o-&gt;points[p2].x, o-&gt;points[p2].y, o-&gt;points[p2].z);</p>
<p class="src6">glVertex3f(o-&gt;points[p2].x + v2.x, o-&gt;points[p2].y + v2.y, o-&gt;points[p2].z + v2.z);</p>
<p class="src5">glEnd();</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p class="src1">}</p>

<p>A¾ teï opravdu zobrazíme na scénu stíny. Na úrovni roviny obrazovky vykreslíme velký, ¹edý, poloprùhledný obdélník. Zobrazí se pouze ty pixely, které byly právì oznaèeny ve stencil bufferu (na pozici stínu). Èím bude obdélník tmav¹í, tím tmav¹í bude i stín. Mù¾ete zkusit jinou prùhlednost nebo dokonce i barvu. Jak by se vám líbil èervený, zelený nebo modrý stín? ®ádný problém!</p>

<p class="src1">glFrontFace(GL_CCW);<span class="kom">// Èelní stìna proti smìru hodinových ruèièek</span></p>
<p class="src1">glColorMask(1, 1, 1, 1);<span class="kom">// Vykreslovat na obrazovku</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslení obdélníku pøes celou scénu</span></p>
<p class="src1">glColor4f(0.0f, 0.0f, 0.0f, 0.4f);<span class="kom">// Èerná, 40% prùhledná</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);<span class="kom">// Typ blendingu</span></p>
<p class="src"></p>
<p class="src1">glStencilFunc(GL_NOTEQUAL, 0, 0xffffffff);<span class="kom">// Nastavení stencilu</span></p>
<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_KEEP);<span class="kom">// Nemìnit hodnotu stencilu</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾í matici</span></p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Èerný obdélník</span></p>
<p class="src3">glVertex3f(-0.1f, 0.1f,-0.10f);</p>
<p class="src3">glVertex3f(-0.1f,-0.1f,-0.10f);</p>
<p class="src3">glVertex3f( 0.1f, 0.1f,-0.10f);</p>
<p class="src3">glVertex3f( 0.1f,-0.1f,-0.10f);</p>
<p class="src2">glEnd();</p>
<p class="src"></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoví matici</span></p>

<p>Nakonec obnovíme zmìnìné parametry OpenGL na výchozí hodnoty.</p>

<p class="src1"><span class="kom">// Obnoví zmìnìné parametry OpenGL</span></p>
<p class="src1">glDisable(GL_BLEND);</p>
<p class="src1">glDepthFunc(GL_LEQUAL);</p>
<p class="src1">glDepthMask(GL_TRUE);</p>
<p class="src1">glEnable(GL_LIGHTING);</p>
<p class="src1">glDisable(GL_STENCIL_TEST);</p>
<p class="src1">glShadeModel(GL_SMOOTH);</p>
<p class="src0">}</p>

<p>DrawGLScene(), ostatnì jako v¾dycky, zaji¹»uje v¹echno vykreslování. Promìnná Minv bude reprezentovat OpenGL matici, wlp budou lokální koordináty a lp pomocná pozice svìtla.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Hlavní vykreslovací funkce</span></p>
<p class="src0">{</p>
<p class="src1">GLmatrix16f Minv;<span class="kom">// OpenGL matice</span></p>
<p class="src1">GLvector4f wlp, lp;<span class="kom">// Relativní pozice svìtla</span></p>

<p>Sma¾eme obrazovkový, hloubkový i stencil buffer. Resetujeme matici a pøesuneme se o dvacet jednotek do obrazovky. Umístíme svìtlo, provedeme translaci na pozici koule a pomocí quadraticu ji vykreslíme.</p>

<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT | GL_STENCIL_BUFFER_BIT);<span class="kom">// Sma¾e buffery</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -20.0f);<span class="kom">// Pøesun 20 jednotek do hloubky</span></p>
<p class="src"></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_POSITION, LightPos);<span class="kom">// Umístìní svìtla</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(SpherePos[0], SpherePos[1], SpherePos[2]);<span class="kom">// Umístìní koule</span></p>
<p class="src1">gluSphere(q, 1.5f, 32, 16);<span class="kom">// Vykreslení koule</span></p>

<p>Spoèítáme relativní pozici svìtla vzhledem k lokálnímu souøadnicovému systému objektu, který vrhá stín. Do promìnné Min ulo¾íme transformaèní matici objektu, ale obrácenou (v¹e se zápornými èísly a zadávané opaèným poøadím), tak¾e se stane invertovanou transformaèní maticí. Z lp vytvoøíme kopii pozice svìtla a poté ho vynásobíme právì získanou OpenGL maticí. Jednodu¹e øeèeno: na konci bude lp pozicí svìtla v souøadnicovém systému objektu.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glRotatef(-yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src1">glRotatef(-xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, Minv);<span class="kom">// Ulo¾ení ModelView matice do Minv</span></p>
<p class="src"></p>
<p class="src1">lp[0] = LightPos[0];<span class="kom">// Ulo¾ení pozice svìtla</span></p>
<p class="src1">lp[1] = LightPos[1];</p>
<p class="src1">lp[2] = LightPos[2];</p>
<p class="src1">lp[3] = LightPos[3];</p>
<p class="src"></p>
<p class="src1">VMatMult(Minv, lp);<span class="kom">// Vynásobení pozice svìtla OpenGL maticí</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-ObjPos[0], -ObjPos[1], -ObjPos[2]);<span class="kom">// Posun zápornì o pozici objektu</span></p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, Minv);<span class="kom">// Ulo¾ení ModelView matice do Minv</span></p>
<p class="src"></p>
<p class="src1">wlp[0] = 0.0f;<span class="kom">// Globální koordináty na nulu</span></p>
<p class="src1">wlp[1] = 0.0f;</p>
<p class="src1">wlp[2] = 0.0f;</p>
<p class="src1">wlp[3] = 1.0f;</p>
<p class="src"></p>
<p class="src1">VMatMult(Minv, wlp);<span class="kom">// Originální globální souøadnicový systém relativnì k lokálnímu </span></p>
<p class="src"></p>
<p class="src1">lp[0] += wlp[0];<span class="kom">// Pozice svìtla je relativní k lokálnímu souøadnicovému systému objektu</span></p>
<p class="src1">lp[1] += wlp[1];</p>
<p class="src1">lp[2] += wlp[2];</p>

<p>Vykreslíme místnost s objektem a potom zavoláme funkci CastShadow(), která vykreslí stín objektu. Pøedáváme jí referenci na objekt spolu s pozicí svìtla, která je nyní ve stejném souøadnicovém systému jako objekt.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -20.0f);<span class="kom">// Pøesun 20 jednotek do hloubky</span></p>
<p class="src"></p>
<p class="src1">DrawGLRoom();<span class="kom">// Vykreslení místnosti</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(ObjPos[0], ObjPos[1], ObjPos[2]);<span class="kom">// Umístìní objektu</span></p>
<p class="src1">glRotatef(xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src1">DrawGLObject(obj);<span class="kom">// Vykreslení objektu</span></p>
<p class="src"></p>
<p class="src1">CastShadow(&amp;obj, lp);<span class="kom">// Vr¾ení stínu zalo¾ené na siluetì</span></p>

<p>Abychom po spu¹tìní dema vidìli, kde se právì nachází svìtlo, vykreslíme na jeho pozici malý oran¾ový kruh (respektive kouli).</p>

<p class="src1">glColor4f(0.7f, 0.4f, 0.0f, 1.0f);<span class="kom">// Oran¾ová barva</span></p>
<p class="src"></p>
<p class="src1">glDisable(GL_LIGHTING);<span class="kom">// Vypne svìtlo</span></p>
<p class="src1">glDepthMask(GL_FALSE);<span class="kom">// Vypne masku hloubky</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(lp[0], lp[1], lp[2]);<span class="kom">// Translace na pozici svìtla</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Poøád jsme v lokálním souøadnicovém systému objektu</span></p>
<p class="src1">gluSphere(q, 0.2f, 16, 8);<span class="kom">// Vykreslení malé koule (reprezentuje svìtlo)</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne svìtlo</span></p>
<p class="src1">glDepthMask(GL_TRUE);<span class="kom">// Zapne masku hloubky</span></p>

<p>Aktualizujeme rotaci objektu a ukonèíme funkci.</p>

<p class="src1">xrot += xspeed;<span class="kom">// Zvìt¹ení úhlu rotace objektu</span></p>
<p class="src1">yrot += yspeed;</p>
<p class="src"></p>
<p class="src1">glFlush();</p>
<p class="src1">return TRUE;<span class="kom">// V¹echno v poøádku</span></p>
<p class="src0">}</p>

<p>Dále napí¹eme speciální funkci DrawGLRoom(), která vykreslí místnost. Je jí obyèejná krychle.</p>

<p class="src0">void DrawGLRoom()<span class="kom">// Vykreslí místnost (krychli)</span></p>
<p class="src0">{</p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src2"><span class="kom">// Podlaha</span></p>
<p class="src2">glNormal3f(0.0f, 1.0f, 0.0f);<span class="kom">// Normála smìøuje nahoru</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f,-20.0f);<span class="kom">// Levý zadní</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f, 20.0f);<span class="kom">// Levý pøední</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f, 20.0f);<span class="kom">// Pravý pøední</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f,-20.0f);<span class="kom">// Pravý zadní</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Strop</span></p>
<p class="src2">glNormal3f(0.0f,-1.0f, 0.0f);<span class="kom">// Normála smìøuje dolù</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f, 20.0f);<span class="kom">// Levý pøední</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f,-20.0f);<span class="kom">// Levý zadní</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f,-20.0f);<span class="kom">// Pravý zadní</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f, 20.0f);<span class="kom">// Pravý pøední</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Èelní stìna</span></p>
<p class="src2">glNormal3f(0.0f, 0.0f, 1.0f);<span class="kom">// Normála smìøuje do hloubky</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f,-20.0f);<span class="kom">// Levý horní</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f,-20.0f);<span class="kom">// Levý dolní</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f,-20.0f);<span class="kom">// Pravý dolní</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f,-20.0f);<span class="kom">// Pravý horní</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Zadní stìna</span></p>
<p class="src2">glNormal3f(0.0f, 0.0f,-1.0f);<span class="kom">// Normála smìøuje k obrazovce</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f, 20.0f);<span class="kom">// Pravý horní</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f, 20.0f);<span class="kom">// Pravý spodní</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f, 20.0f);<span class="kom">// Levý spodní</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f, 20.0f);<span class="kom">// Levý zadní</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Levá stìna</span></p>
<p class="src2">glNormal3f(1.0f, 0.0f, 0.0f);<span class="kom">// Normála smìøuje doprava</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f, 20.0f);<span class="kom">// Pøední horní</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f, 20.0f);<span class="kom">// Pøední dolní</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f,-20.0f);<span class="kom">// Zadní dolní</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f,-20.0f);<span class="kom">// Zadní horní</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Pravá stìna</span></p>
<p class="src2">glNormal3f(-1.0f, 0.0f, 0.0f);<span class="kom">// Normála smìøuje doleva</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f,-20.0f);<span class="kom">// Zadní horní</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f,-20.0f);<span class="kom">// Zadní dolní</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f, 20.0f);<span class="kom">// Pøední dolní</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f, 20.0f);<span class="kom">// Pøední horní</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src0">}</p>

<p>Pøedtím ne¾ zapomenu... v DrawGLScene() jsme pou¾ili funkci VMatMult(), která násobí vektor maticí. Opìt se jedná o implementaci vzorce z kní¾ky o matematice.</p>

<p class="src0">void VMatMult(GLmatrix16f M, GLvector4f v)</p>
<p class="src0">{</p>
<p class="src1">GLfloat res[4];<span class="kom">// Ukládá výsledky</span></p>
<p class="src"></p>
<p class="src1">res[0] = M[ 0]*v[0] + M[ 4]*v[1] + M[ 8]*v[2] + M[12]*v[3];</p>
<p class="src1">res[1] = M[ 1]*v[0] + M[ 5]*v[1] + M[ 9]*v[2] + M[13]*v[3];</p>
<p class="src1">res[2] = M[ 2]*v[0] + M[ 6]*v[1] + M[10]*v[2] + M[14]*v[3];</p>
<p class="src1">res[3] = M[ 3]*v[0] + M[ 7]*v[1] + M[11]*v[2] + M[15]*v[3];</p>
<p class="src"></p>
<p class="src1">v[0] = res[0];<span class="kom">// Výsledek ulo¾í zpìt do v</span></p>
<p class="src1">v[1] = res[1];</p>
<p class="src1">v[2] = res[2];</p>
<p class="src1">v[3] = res[3];<span class="kom">// Homogenní souøadnice</span></p>
<p class="src0">}</p>

<p>V Inicializaci OpenGL nejsou témìø ¾ádné novinky. Na zaèátku nahrajeme a inicializujeme objekt, který vrhá stín, potom nastavíme obvyklé parametry a svìtla.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!InitGLObjects())<span class="kom">// Nahraje objekt</span></p>
<p class="src2">return FALSE;</p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glClearStencil(0);<span class="kom">// Nastavení stencil bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí testování hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testování hloubky</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivní korekce</span></p>
<p class="src"></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_POSITION, LightPos);<span class="kom">// Pozice svìtla</span></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_AMBIENT, LightAmb);<span class="kom">// Ambient svìtlo</span></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_DIFFUSE, LightDif);<span class="kom">// Diffuse svìtlo</span></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_SPECULAR, LightSpc);<span class="kom">// Specular svìtlo</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHT1);<span class="kom">// Zapne svìtlo 1</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne svìtla</span></p>

<p>Materiály, které urèují jak vypadají polygony pøi dopadu svìtla, jsou, myslím, novinkou. Nemusíme vepisovat ¾ádné hodnoty, proto¾e pøedávané pole jsou definovány na zaèátku tutoriálu. Materiály mimo jiné urèují i barvu povrchu, tak¾e pøi zapnutém svìtle nebude mít zmìna barvy pomocí glColor() ¾ádný vliv (Pøekl. To jsem zjistil úplnì náhodou. Nevím, jestli je to pravda obecnì, ale minimálnì v tomto demu ano.).</p>

<p class="src1">glMaterialfv(GL_FRONT, GL_AMBIENT, MatAmb);<span class="kom">// Prostøedí, atmosféra</span></p>
<p class="src1">glMaterialfv(GL_FRONT, GL_DIFFUSE, MatDif);<span class="kom">// Rozptylování svìtla</span></p>
<p class="src1">glMaterialfv(GL_FRONT, GL_SPECULAR, MatSpc);<span class="kom">// Zrcadlivost</span></p>
<p class="src1">glMaterialfv(GL_FRONT, GL_SHININESS, MatShn);<span class="kom">// Lesk</span></p>

<p>Abychom alespoò trochu zrychlili vykreslování, zapneme culling, tak¾e se zadní strany trojúhelníkù nebudou vykreslovat. Která strana je odvrácená se urèí podle poøadí zadávání vrcholù polygonù (po/proti smìru hodinových ruèièek).</p>

<p class="src1">glCullFace(GL_BACK);<span class="kom">// Oøezávání zadních stran</span></p>
<p class="src1">glEnable(GL_CULL_FACE);<span class="kom">// Zapne oøezávání</span></p>

<p>Budeme vykreslovat i nìjaké koule, tak¾e vytvoøíme a inicializujeme quadratic.</p>

<p class="src1">q = gluNewQuadric();<span class="kom">// Nový quadratic</span></p>
<p class="src1">gluQuadricNormals(q, GL_SMOOTH);<span class="kom">// Generování normálových vektorù pro svìtlo</span></p>
<p class="src1">gluQuadricTexture(q, GL_FALSE);<span class="kom">// Nepotøebujeme texturovací koordináty</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V poøádku</span></p>
<p class="src0">}</p>

<p>Poslední funkcí tohoto tutoriálu je ProcessKeyboard(). Stejnì jako vykreslování, tak i ona, se volá v ka¾dém prùchodu hlavní smyèky programu. O¹etøuje u¾ivatelské pøíkazy pøi stisku kláves. Jak se program zachová, popisují komentáøe.</p>

<p class="src0">void ProcessKeyboard()<span class="kom">// O¹etøení klávesnice</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Rotace objektu</span></p>
<p class="src1">if (keys[VK_LEFT]) yspeed -= 0.1f;<span class="kom">// ©ipka vlevo - sni¾uje y rychlost</span></p>
<p class="src1">if (keys[VK_RIGHT]) yspeed += 0.1f;<span class="kom">// ©ipka vpravo - zvy¹uje y rychlost</span></p>
<p class="src1">if (keys[VK_UP]) xspeed -= 0.1f;<span class="kom">// ©ipka nahoru - sni¾uje x rychlost</span></p>
<p class="src1">if (keys[VK_DOWN]) xspeed += 0.1f;<span class="kom">// ©ipka dolù - zvy¹uje x rychlost</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pozice objektu</span></p>
<p class="src1">if (keys[VK_NUMPAD6]) ObjPos[0] += 0.05f;<span class="kom">// '6' - pohybuje objektem doprava</span></p>
<p class="src1">if (keys[VK_NUMPAD4]) ObjPos[0] -= 0.05f;<span class="kom">// '4' - pohybuje objektem doleva</span></p>
<p class="src"></p>
<p class="src1">if (keys[VK_NUMPAD8]) ObjPos[1] += 0.05f;<span class="kom">// '8' - pohybuje objektem nahoru</span></p>
<p class="src1">if (keys[VK_NUMPAD5]) ObjPos[1] -= 0.05f;<span class="kom">// '5' - pohybuje objektem dolù</span></p>
<p class="src"></p>
<p class="src1">if (keys[VK_NUMPAD9]) ObjPos[2] += 0.05f;<span class="kom">// '9' - pøibli¾uje objekt</span></p>
<p class="src1">if (keys[VK_NUMPAD7]) ObjPos[2] -= 0.05f;<span class="kom">// '7' oddaluje objekt</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pozice svìtla</span></p>
<p class="src1">if (keys['L']) LightPos[0] += 0.05f;<span class="kom">// 'L' - pohybuje svìtlem doprava</span></p>
<p class="src1">if (keys['J']) LightPos[0] -= 0.05f;<span class="kom">// 'J'  - pohybuje svìtlem doleva</span></p>
<p class="src"></p>
<p class="src1">if (keys['I']) LightPos[1] += 0.05f;<span class="kom">// 'I' - pohybuje svìtlem nahoru</span></p>
<p class="src1">if (keys['K']) LightPos[1] -= 0.05f;<span class="kom">// 'K' - pohybuje svìtlem dolù</span></p>
<p class="src"></p>
<p class="src1">if (keys['O']) LightPos[2] += 0.05f;<span class="kom">// 'O' - pøibli¾uje svìtlo</span></p>
<p class="src1">if (keys['U']) LightPos[2] -= 0.05f;<span class="kom">// 'U' - oddaluje svìtlo</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pozice koule</span></p>
<p class="src1">if (keys['D']) SpherePos[0] += 0.05f;<span class="kom">// 'D' - pohybuje koulí doprava</span></p>
<p class="src1">if (keys['A']) SpherePos[0] -= 0.05f;<span class="kom">// 'A' - pohybuje koulí doleva</span></p>
<p class="src"></p>
<p class="src1">if (keys['W']) SpherePos[1] += 0.05f;<span class="kom">// 'W' - pohybuje koulí nahoru</span></p>
<p class="src1">if (keys['S']) SpherePos[1] -= 0.05f;<span class="kom">// 'S'- pohybuje koulí dolù</span></p>
<p class="src"></p>
<p class="src1">if (keys['E']) SpherePos[2] += 0.05f;<span class="kom">// 'E' - pøibli¾uje kouli</span></p>
<p class="src1">if (keys['Q']) SpherePos[2] -= 0.05f;<span class="kom">// 'Q' - oddaluje kouli</span></p>
<p class="src0">}</p>
<p class="src"></p>

<h3>Nìkolik poznámek ohlednì tutoriálu</h3>
<p>Na první pohled vypadá demo hyperefektnì :-), ale má také své mouchy. Tak napøíklad koule nezastavuje projekci stínu na stìnu. V reálném prostøedí by také vrhala stín, tak¾e by se nic moc nestalo. Nicménì je zde pouze na ukázku toho, co se se stínem stane na zakøiveném povrchu.</p>

<p>Pokud program bì¾í extrémnì pomalu, zkuste pøepnout do fullscreenu nebo zmìnit barevnou hloubku na 32 bitù. Arseny L. napsal: &quot;Pokud máte problémy s TNT2 v okenním módu, ujistìte se, ¾e nemáte nastavenu 16bitovou barevnou hloubku. V tomto barevném módu je stencil buffer emulovaný, co¾ ve výsledku znamená malý výkon. V 32bitovém módu je v¹e bez problémù.&quot;</p>

<p class="autor">napsal: Banu Cosmin - Choko &amp; Brett Porter <?VypisEmail('brettporter@yahoo.com');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson27.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson27_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson27.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson27.zip">Delphi</a> kód této lekce. ( <a href="mailto:another.freak@gmx.de">Felix Hahn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson27.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson27.jar">JoGL</a> kód této lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/kde/lesson27.tar.gz">KDE/QT</a> kód této lekce. ( <a href="mailto:zhajdu@socal.rr.com">Zsolt Hajdu</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson27.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:jay@remotepoint.com">Jay Groven</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson27.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson27.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(27);?>
<?FceNeHeOkolniLekce(27);?>

<?
include 'p_end.php';
?>
