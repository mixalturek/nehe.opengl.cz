<?
$g_title = 'CZ NeHe OpenGL - Lekce 36 - Radial Blur, renderování do textury';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(36);?>

<h1>Lekce 36 - Radial Blur, renderování do textury</h1>

<p class="nadpis_clanku">Spoleènými silami vytvoøíme extrémnì pùsobivý efekt radial blur, který nevy¾aduje ¾ádná OpenGL roz¹íøení a funguje na jakémkoli hardwaru. Nauèíte se také, jak lze na pozadí aplikace vyrenderovat scénu do textury, aby pozorovatel nic nevidìl.</p>

<p>Ahoj, jmenuji se Dario Corno, ale jsem také z nám jako rIo ze Spinning Kids. První ze v¹eho vysvìtlím, proè jsem se rozhodl napsat tento tutoriál. Roku 1989 jsem se stal &quot;scénaøem&quot;. Chtìl bych po vás, abyste si stáhli nìjaká dema. Pochopíte, co to demo je a v èem spoèívají demo efekty.</p>

<p>Dema vytváøejí opravdoví kodeøi na ukázku hardcore a èasto i brutálních kódovacích technik. Svým zpùsobem jsou druhem umìní, ve kterém se spojuje v¹e od hudby (hudba na pozadí, zvuky) a malíøství (grafika, design, modely) pøes matematiku a fyziku (v¹e funguje na nìjakých principech) a¾ po programování a detailní znalost poèítaèe na úrovni hardwaru. Obrovské kolekce dem mù¾ete najít na <?OdkazBlank('http://www.pouet.net/');?> a <?OdkazBlank('http://ftp.scene.org/');?>, v Èechách pak <?OdkazBlank('http://www.scene.cz/');?>. Ale abyste se hned na zaèátku nevylekali... toto není pravý smrtící tutoriál, i kdy¾ musím uznat, ¾e výsledek stojí za to.</p>

<p>Pøekl.: Se svým prvním demem jsem se setkal ve druháku na støední, kdy nám spolubydlící na intru Luká¹ Duzsty Hoger ukazoval na 486 notebooku jeden prográmek, který zabíral kolem 2 kB. Na zaèátku byla vidìt ruka, jak kreslí na plátno dùm, strom a postavy, scéna se vyboulila do 3D a musím øíct, ¾e na 256 barev a DOSovou grafiku v¹e vypadalo úchvatnì - kam se programátoøi vyu¾ívající pohodlných slu¾eb OpenGL vùbec hrabou :-). Proti tomu koderovi fakt batolata. Asi nejlep¹í demo, které jsem kdy vidìl byla 64 kB animace &quot;reálného&quot; 3D prostøedí ve video kvalitì, která trvala nìco pøes ètvrt hodiny. Jenom texty v kreditu na konci musely zabírat polovinu místa. Zkuste si pro zajímavost zkompilovat prázdnou MFC aplikaci vygenerovanou APP Wizzardem, která navíc tahá vìt¹inu potøebných funkcí z DLL knihoven - nedostanete se pod 30 kB.</p>

<p>Tolik tedy k úvodu... Co se ale dozvíte v tomto tutoriálu? Vysvìtlím vám, jak vytvoøit perfektní efekt (pou¾ívaný v demech), který vypadá jako radial blur (radiální rozmazání). Nìkdy je také oznaèován jako volumetrická svìtla, ale nevìøte, je to pouze obyèejný radial blur.</p>

<p>Radial blur bývá obyèejnì vytváøen (pouze pøi softwarovém renderingu) rozmazáváním pixelù originálního obrázku v opaèném smìru ne¾ se nachází støed rozmazávání. S dne¹ním hardwarem je docela obtí¾né provádìt ruèní blurring (rozmazávání) za pou¾ití color bufferu (alespoò v pøípadì, ¾e je podporován v¹emi grafickými kartami), tak¾e potøebujeme vyu¾ít malého triku, abychom dosáhli alespoò podobného efektu. Jako bonus se také dozvíte, jak je snadné renderovat do textury.</p>

<p>Objekt, který jsem se pro tento tutoriál rozhodl pou¾ít, je spirála, proto¾e vypadá hodnì dobøe. Navíc jsem u¾ celkem unavený z krychlièek :-] Musím je¹tì poznamenat, ¾e vysvìtluji hlavnì vytváøení výsledného efektu, naopak pomocný kód u¾ ménì detailnìji. Mìli byste ho mít u¾ dávno za¾itý.</p>

<p class="src0"><span class="kom">// U¾ivatelské promìnné</span></p>
<p class="src0">float angle;<span class="kom">// Úhel rotace spirály</span></p>
<p class="src0">float vertexes[4][3];<span class="kom">// Ètyøi body o tøech souøadnicích</span></p>
<p class="src0">float normal[3];<span class="kom">// Data normálového vektoru</span></p>
<p class="src0">GLuint BlurTexture;<span class="kom">// Textura</span></p>

<p>Tak tedy zaèneme... Funkce EmptyTexture() generuje prázdnou texturu a vrací èíslo jejího identifikátoru. Na zaèátku alokujeme pamì» obrázku o velikosti 128*128*4. Tato èísla oznaèují ¹íøku, vý¹ku a barevnou hloubku (RGBA) obrázku. Po alokaci pamì» vynulujeme. Proto¾e budeme texturu roztahovat, pou¾ijeme pro ni lineární filtrování, GL_NEAREST v na¹em pøípadì nevypadá zrovna nejlépe.</p>

<p class="src0">GLuint EmptyTexture()<span class="kom">// Vytvoøí prázdnou texturu</span></p>
<p class="src0">{</p>
<p class="src1">GLuint txtnumber;<span class="kom">// ID textury</span></p>
<p class="src1">unsigned int* data;<span class="kom">// Ukazatel na data obrázku</span></p>
<p class="src"></p>
<p class="src1">data = (unsigned int*) new GLuint[((128 * 128) * 4 * sizeof(unsigned int))];<span class="kom">// Alokace pamìti</span></p>
<p class="src1">ZeroMemory(data,((128 * 128)* 4 * sizeof(unsigned int)));<span class="kom">// Nulování pamìti</span></p>
<p class="src"></p>
<p class="src1">glGenTextures(1, &amp;txtnumber);<span class="kom">// Jedna textura</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, txtnumber);<span class="kom">// Zvolí texturu</span></p>
<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, 4, 128, 128, 0, GL_RGBA, GL_UNSIGNED_BYTE, data);<span class="kom">// Vytvoøení textury</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Lineární filtrování pro zmen¹ení i zvìt¹ení</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);</p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);</p>
<p class="src"></p>
<p class="src1">delete [] data;<span class="kom">// Uvolnìní pamìti</span></p>
<p class="src"></p>
<p class="src1">return txtnumber;<span class="kom">// Vrátí ID textury</span></p>
<p class="src0">}</p>

<p>Následující funkce normalizuje vektor, který je pøedán v parametru jako pole tøí floatù. Spoèítáme jeho délku a s její pomocí vydìlíme v¹echny tøi slo¾ky.</p>

<p class="src0">void ReduceToUnit(float vector[3])<span class="kom">// Výpoèet normalizovaného vektoru (jednotková délka)</span></p>
<p class="src0">{</p>
<p class="src1">float length;<span class="kom">// Délka vektoru</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Výpoèet souèasné délky vektoru</span></p>
<p class="src1">length = (float)sqrt((vector[0]*vector[0]) + (vector[1]*vector[1]) + (vector[2]*vector[2]));</p>
<p class="src"></p>
<p class="src1">if(length == 0.0f)<span class="kom">// Prevence dìlení nulou</span></p>
<p class="src1">{</p>
<p class="src2">length = 1.0f;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">vector[0] /= length;<span class="kom">// Vydìlení jednotlivých slo¾ek délkou</span></p>
<p class="src1">vector[1] /= length;</p>
<p class="src1">vector[2] /= length;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Výsledný vektor je pøedán zpìt v parametru funkce</span></p>
<p class="src0">}</p>

<p>Pomocí funkce calcNormal() lze vypoèítat vektor, který je kolmý ke tøem bodùm tvoøícím rovinu. Dostali jsme dva parametry: v[3][3] pøedstavuje tøi body (o tøech slo¾kách x,y,z) a do out[3] ulo¾íme výsledek. Na zaèátku deklarujeme dva pomocné vektory a tøi konstanty, které vystupují jako indexy do pole.</p>

<p class="src0">void calcNormal(float v[3][3], float out[3])<span class="kom">// Výpoèet normálového vektoru polygonu</span></p>
<p class="src0">{</p>
<p class="src1">float v1[3], v2[3];<span class="kom">// Vektor 1 a vektor 2 (x,y,z)</span></p>
<p class="src"></p>
<p class="src1">static const int x = 0;<span class="kom">// Pomocné indexy do pole</span></p>
<p class="src1">static const int y = 1;</p>
<p class="src1">static const int z = 2;</p>

<p>Ze tøech bodù pøedaných funkci vytvoøíme dva vektory a spoèítáme tøetí vektor, který je k nim kolmý.</p>

<p class="src1">v1[x] = v[0][x] - v[1][x];<span class="kom">// Výpoèet vektoru z 1. bodu do 0. bodu</span></p>
<p class="src1">v1[y] = v[0][y] - v[1][y];</p>
<p class="src1">v1[z] = v[0][z] - v[1][z];</p>
<p class="src"></p>
<p class="src1">v2[x] = v[1][x] - v[2][x];<span class="kom">// Výpoèet vektoru z 2. bodu do 1. bodu</span></p>
<p class="src1">v2[y] = v[1][y] - v[2][y];</p>
<p class="src1">v2[z] = v[1][z] - v[2][z];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Výsledkem vektorového souèinu dvou vektorù je tøetí vektor, který je k nim kolmý</span></p>
<p class="src1">out[x] = v1[y]*v2[z] - v1[z]*v2[y];</p>
<p class="src1">out[y] = v1[z]*v2[x] - v1[x]*v2[z];</p>
<p class="src1">out[z] = v1[x]*v2[y] - v1[y]*v2[x];</p>

<p>Aby v¹e bylo dokonalé, tak výsledný vektor normalizujeme na jednotkovou délku.</p>

<p class="src1">ReduceToUnit(out);<span class="kom">// Normalizace výsledného vektoru</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Výsledný vektor je pøedán zpìt v parametru funkce</span></p>
<p class="src0">}</p>

<p>Následující rutina vykresluje spirálu. Po deklaraci promìnných nastavíme pomocí gluLookAt() výhled do scény. Díváme se z bodu 0, 5, 50 do bodu 0, 0, 0. UP vektor míøí vzhùru ve smìru osy y.</p>

<p class="src0">void ProcessHelix()<span class="kom">// Vykreslí spirálu</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat x;<span class="kom">// Souøadnice x, y, z</span></p>
<p class="src1">GLfloat y;</p>
<p class="src1">GLfloat z;</p>
<p class="src"></p>
<p class="src1">GLfloat phi;<span class="kom">// Úhly</span></p>
<p class="src1">GLfloat theta;</p>
<p class="src1">GLfloat u;</p>
<p class="src1">GLfloat v;</p>
<p class="src"></p>
<p class="src1">GLfloat r;<span class="kom">// Polomìr závitu</span></p>
<p class="src1">int twists = 5;<span class="kom">// Pìt závitù</span></p>
<p class="src"></p>
<p class="src1">GLfloat glfMaterialColor[] = { 0.4f, 0.2f, 0.8f, 1.0f};<span class="kom">// Barva materiálu</span></p>
<p class="src1">GLfloat specular[] = { 1.0f, 1.0f, 1.0f, 1.0f};<span class="kom">// Specular svìtlo</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">gluLookAt(0,5,50, 0,0,0, 0,1,0);<span class="kom">// Pozice oèí (0,5,50), støed scény (0,0,0), UP vektor na ose y</span></p>

<p>Ulo¾íme matici a pøesuneme se o padesát jednotek do scény. V závislosti na úhlu angle (globální promìnná) se spirálou rotujeme. Také nastavíme materiály.</p>

<p class="src1">glPushMatrix();<span class="kom">// Ulo¾ení matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0, 0, -50);<span class="kom">// Padesát jednotek do scény</span></p>
<p class="src1">glRotatef(angle/2.0f, 1, 0, 0);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(angle/3.0f, 0, 1, 0);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastavení materiálù</span></p>
<p class="src1">glMaterialfv(GL_FRONT_AND_BACK, GL_AMBIENT_AND_DIFFUSE, glfMaterialColor);</p>
<p class="src1">glMaterialfv(GL_FRONT_AND_BACK, GL_SPECULAR,specular);</p>

<p>Pokud ovládáte goniometrické funkce, je výpoèet jednotlivých bodù spirály relativnì jednoduchý, ale nebudu to zde vysvìtlovat (Pøekl.: díky bohu... :-), proto¾e spirála není hlavní náplní tohoto tutoriálu. Navíc jsem si kód pùjèil od kamarádù z Listen Software. Pùjdeme jednodu¹¹í, ale ne nejrychlej¹í cestou. S vertex arrays by bylo v¹e mnohem rychlej¹í.</p>

<p class="src1">r = 1.5f;<span class="kom">// Polomìr</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslení obdélníkù</span></p>
<p class="src2">for(phi = 0; phi &lt;= 360; phi += 20.0)<span class="kom">// 360 stupòù v kroku po 20 stupních</span></p>
<p class="src2">{</p>
<p class="src3">for(theta = 0; theta &lt;= 360*twists; theta += 20.0)<span class="kom">// 360 stupòù* poèet závitù po 20 stupních</span></p>
<p class="src3">{</p>
<p class="src4">v = (phi / 180.0f * 3.142f);<span class="kom">// Úhel prvního bodu (0)</span></p>
<p class="src4">u = (theta / 180.0f * 3.142f);<span class="kom">// Úhel prvního bodu (0)</span></p>
<p class="src"></p>
<p class="src4">x = float(cos(u) * (2.0f + cos(v))) * r;<span class="kom">// Pozice x, y, z prvního bodu</span></p>
<p class="src4">y = float(sin(u) * (2.0f + cos(v))) * r;</p>
<p class="src4">z = float(((u - (2.0f * 3.142f)) + sin(v)) * r);</p>
<p class="src"></p>
<p class="src4">vertexes[0][0] = x;<span class="kom">// Kopírování prvního bodu do pole</span></p>
<p class="src4">vertexes[0][1] = y;</p>
<p class="src4">vertexes[0][2] = z;</p>
<p class="src"></p>
<p class="src4">v = (phi / 180.0f * 3.142f);<span class="kom">// Úhel druhého bodu (0)</span></p>
<p class="src4">u = ((theta + 20) / 180.0f * 3.142f);<span class="kom">// Úhel druhého bodu (20)</span></p>
<p class="src"></p>
<p class="src4">x = float(cos(u) * (2.0f + cos(v))) * r;<span class="kom">// Pozice x, y, z druhého bodu</span></p>
<p class="src4">y = float(sin(u) * (2.0f + cos(v))) * r;</p>
<p class="src4">z = float(((u - (2.0f * 3.142f)) + sin(v)) * r);</p>
<p class="src"></p>
<p class="src4">vertexes[1][0] = x;<span class="kom">// Kopírování druhého bodu do pole</span></p>
<p class="src4">vertexes[1][1] = y;</p>
<p class="src4">vertexes[1][2] = z;</p>
<p class="src"></p>
<p class="src4">v=((phi + 20) / 180.0f * 3.142f);<span class="kom">// Úhel tøetího bodu (20)</span></p>
<p class="src4">u=((theta + 20) / 180.0f * 3.142f);<span class="kom">// Úhel tøetího bodu (20)</span></p>
<p class="src"></p>
<p class="src4">x = float(cos(u) * (2.0f + cos(v))) * r;<span class="kom">// Pozice x, y, z tøetího bodu</span></p>
<p class="src4">y = float(sin(u) * (2.0f + cos(v))) * r;</p>
<p class="src4">z = float(((u - (2.0f * 3.142f)) + sin(v)) * r);</p>
<p class="src"></p>
<p class="src4">vertexes[2][0] = x;<span class="kom">// Kopírování tøetího bodu do pole</span></p>
<p class="src4">vertexes[2][1] = y;</p>
<p class="src4">vertexes[2][2] = z;</p>
<p class="src"></p>
<p class="src4">v = ((phi + 20) / 180.0f * 3.142f);<span class="kom">// Úhel ètvrtého bodu (20)</span></p>
<p class="src4">u = ((theta) / 180.0f * 3.142f);<span class="kom">// Úhel ètvrtého bodu (0)</span></p>
<p class="src"></p>
<p class="src4">x = float(cos(u) * (2.0f + cos(v))) * r;<span class="kom">// Pozice x, y, z ètvrtého bodu</span></p>
<p class="src4">y = float(sin(u) * (2.0f + cos(v))) * r;</p>
<p class="src4">z = float(((u - (2.0f * 3.142f)) + sin(v)) * r);</p>
<p class="src"></p>
<p class="src4">vertexes[3][0] = x;<span class="kom">// Kopírování ètvrtého bodu do pole</span></p>
<p class="src4">vertexes[3][1] = y;</p>
<p class="src4">vertexes[3][2] = z;</p>
<p class="src"></p>
<p class="src4">calcNormal(vertexes, normal);<span class="kom">// Výpoèet normály obdélníku</span></p>
<p class="src"></p>
<p class="src4">glNormal3f(normal[0], normal[1], normal[2]);<span class="kom">// Poslání normály OpenGL</span></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Rendering obdélníku</span></p>
<p class="src4">glVertex3f(vertexes[0][0], vertexes[0][1], vertexes[0][2]);</p>
<p class="src4">glVertex3f(vertexes[1][0], vertexes[1][1], vertexes[1][2]);</p>
<p class="src4">glVertex3f(vertexes[2][0], vertexes[2][1], vertexes[2][2]);</p>
<p class="src4">glVertex3f(vertexes[3][0], vertexes[3][1], vertexes[3][2]);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src"></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení matice</span></p>
<p class="src0">}</p>

<p>Funkce ViewOrtho() slou¾í k pøepnutí z perspektivní projekce do pravoúhlé a ViewPerspective() k návratu zpìt. V¹e u¾ bylo popsáno napøíklad v tutoriálech o fontech, ale i jinde, tak¾e to zde nebudu znovu probírat.</p>

<p class="src0">void ViewOrtho()<span class="kom">// Nastavuje pravoúhlou projekci</span></p>
<p class="src0">{</p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Projekèní matice</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾ení matice</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glOrtho(0, 640 , 480 , 0, -1, 1);<span class="kom">// Nastavení pravoúhlé projekce</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Modelview matice</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾ení matice</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">void ViewPerspective()<span class="kom">// Obnovení perspektivního módu</span></p>
<p class="src0">{</p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Projekèní matice</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení matice</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Modelview matice</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení matice</span></p>
<p class="src0">}</p>

<p>Pojïme si vysvìtlit, jak pracuje na¹e imitace efektu radial blur. Potøebujeme vykreslit scénu tak, aby se jevila jakoby rozmazaná od støedu do v¹ech smìrù. Nemù¾eme èíst ani zapisovat pixely a pokud chceme zachovat kompatibilitu s rùzným grafickými kartami, nemìli bychom pou¾ívat ani OpenGL roz¹íøení ani jiné pøíkazy specifické pro urèitý hardware. Øe¹ení je docela snadné, OpenGL nám dává mo¾nost blurnout (rozmazat) textury. OK... ne opravdový blurring. Pokud za pou¾ití lineárního filtrování roztáhneme textury, výsledek bude, s trochou pøedstavivosti, vypadat podobnì jako gausovo rozmazávání (gaussian blur). Tak¾e, co se stane, pokud pøilepíme spoustu roztáhnutých textur vyobrazujících 3D objekt na scénu pøesnì pøed nìj? Odpovìï je celkem snadná - radial blur!</p>

<p>Potøebujeme v¹ak vyøe¹it dva související problémy: jak v realtimu vytváøet tuto texturu a jak ji zobrazit pøesnì pøed objekt. Øe¹ení prvního je mnohem snaz¹í ne¾ si asi myslíte. Co takhle renderovat pøímo do textury? Pokud aplikace pou¾ívá double buffering, je pøední buffer zobrazen na obrazovce a do zadního se kreslí. Dokud nezavoláme pøíkaz SwapBuffers(), zmìny se navenek neprojeví. Renderování do textury spoèívá v renderingu do zadního bufferu (tedy klasicky, jak jsme zvyklí) a v zkopírování jeho obsahu do textury pomocí funkce glCopyTexImage2D().</p>

<p>Problém dva: vycentrování textury pøesnì pøed 3D objekt. Víme, ¾e pokud zmìníme viewport bez nastavení správné perspektivy, získáme deformovanou scénu. Napøíklad, nastavíme-li ho opravdu ¹iroký bude scéna roztáhnutá vertikálnì.</p>

<p>Nejdøíve nastavíme viewport tak, aby byl ètvercový a mìl stejné rozmìry jako textura (128x128). Po renderování objektu, nakopírujeme color buffer do textury a sma¾eme ho. Obnovíme pùvodní rozmìry a vykreslíme objekt podruhé, tentokrát pøi správném rozli¹ení. Poté, co texturu namapujeme na obdélník o velikosti scény, roztáhne se zpìt na pùvodní velikost a bude umístìná pøesnì pøed 3D objekt. Doufám, ¾e to dává smysl. Pøedstavte si 640x480 screenshot zmen¹ený na bitmapu o velikosti 128x128 pixelù. Tuto bitmapu mù¾eme v grafickém editoru roztáhnout na pùvodní rozmìry 640x480 pixelù. Kvalita bude o mnoho hor¹í, ale obrázku si budou odpovídat.</p>

<p>Pojïme se podívat na kód. Funkce RenderToTexture() je opravdu jednoduchá, ale pøedstavuje kvalitní "designový trik". Nastavíme viewport na rozmìry textury a zavoláme rutinu pro vykreslení spirály. Potom zvolíme blur texturu jako aktivní a z viewportu do ní nakopírujeme color buffer. První parametr funkce glCopyTexImage2D() indikuje, ¾e pou¾íváme 2D texturu, nula oznaèuje úroveò mip mapy (mip map level), defaultnì se zadává nula. GL_LUMINANCE pøedstavuje formát dat. Pou¾íváme právì tuto èást bufferu, proto¾e výsledek vypadá pøesvìdèivìji, ne¾ kdybychom zadali napø. GL_ALPHA, GL_RGB, GL_INTENSITY nebo jiné. Dal¹í dva parametry øíkají, kde zaèít (0, 0), dvakrát 128 pøedstavuje vý¹ku a ¹íøku. Poslední parametr bychom zmìnili, kdybychom po¾adovali okraj (rámeèek), ale teï ho nechceme. V tuto chvíli máme v textuøe ulo¾enu kopii color bufferu. Sma¾eme ho a nastavíme viewport zpìt na správné rozmìry.</p>

<p>DÙLE®ITÉ: Tento postup mù¾e být pou¾it pouze s double bufferingem. Dùvodem je, ¾e v¹echny potøebné operace se musí provádìt na pozadí (v zadním bufferu), aby je u¾ivatel nevidìl.</p>

<p class="src0">void RenderToTexture()<span class="kom">// Rendering do textury</span></p>
<p class="src0">{</p>
<p class="src1">glViewport(0, 0, 128, 128);<span class="kom">// Nastavení viewportu (odpovídá velikosti textury)</span></p>
<p class="src"></p>
<p class="src1">ProcessHelix();<span class="kom">// Rendering spirály</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, BlurTexture);<span class="kom">// Zvolí texturu</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zkopíruje viewport do textury (od 0, 0 do 128, 128, bez okraje)</span></p>
<p class="src1">glCopyTexImage2D(GL_TEXTURE_2D, 0, GL_LUMINANCE, 0, 0, 128, 128, 0);</p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.5f, 0.5);<span class="kom">// Støednì modrá barva pozadí</span></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src"></p>
<p class="src1">glViewport(0, 0, 640, 480);<span class="kom">// Obnovení viewportu</span></p>
<p class="src0">}</p>

<p>Funkce DrawBlur() vykresluje pøed scénu nìkolik prùhledných otexturovaných obdélníkù. Pohrajeme-li si trochu s alfou dostaneme imitaci efektu radial blur. Nejprve vypneme automatické generování texturových koordinátù a potom zapneme 2D textury. Vypneme depth testy, nastavíme blending, zapneme ho a zvolíme texturu. Abychom mohli snadno kreslit obdélníky pøesnì pøes celou scénu, pøepneme do pravoúhlé projekce.</p>

<p class="src0">void DrawBlur(int times, float inc)<span class="kom">// Vykreslí rozmazaný obrázek</span></p>
<p class="src0">{</p>
<p class="src1">float spost = 0.0f;<span class="kom">// Poèáteèní offset souøadnic na textuøe</span></p>
<p class="src1">float alphainc = 0.9f / times;<span class="kom">// Rychlost blednutí pro alfa blending</span></p>
<p class="src1">float alpha = 0.2f;<span class="kom">// Poèáteèní hodnota alfy</span></p>
<p class="src"></p>
<p class="src1">glDisable(GL_TEXTURE_GEN_S);<span class="kom">// Vypne automatické generování texturových koordinátù</span></p>
<p class="src1">glDisable(GL_TEXTURE_GEN_T);</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapování textur</span></p>
<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne testování hloubky</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE);<span class="kom">// Mód blendingu</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, BlurTexture);<span class="kom">// Zvolí texturu</span></p>
<p class="src"></p>
<p class="src1">ViewOrtho();<span class="kom">// Pøepne do pravoúhlé projekce</span></p>

<p>V cyklu vykreslíme texturu tolikrát, abychom vytvoøili radial blur. Souøadnice vertexù zùstávají poøád stejné, ale zvìt¹ujeme koordináty u textur a také sni¾ujeme alfu. Takto vykreslíme celkem 25 quadù, jejich¾ textura se roztahuje poka¾dé o 0.015f.</p>

<p class="src1">alphainc = alpha / times;<span class="kom">// Hodnota zmìny alfy pøi jednom kroku</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslení obdélníkù</span></p>
<p class="src2">for (int num = 0; num &lt; times; num++)<span class="kom">// Poèet krokù renderování skvrn</span></p>
<p class="src2">{</p>
<p class="src3">glColor4f(1.0f, 1.0f, 1.0f, alpha);<span class="kom">// Nastavení hodnoty alfy</span></p>
<p class="src"></p>
<p class="src3">glTexCoord2f(0 + spost, 1 - spost);<span class="kom">// Texturové koordináty (0, 1)</span></p>
<p class="src3">glVertex2f(0, 0);<span class="kom">// První vertex (0, 0)</span></p>
<p class="src"></p>
<p class="src3">glTexCoord2f(0 + spost, 0 + spost);<span class="kom">// Texturové koordináty (0, 0)</span></p>
<p class="src3">glVertex2f(0, 480);<span class="kom">// Druhý vertex (0, 480)</span></p>
<p class="src"></p>
<p class="src3">glTexCoord2f(1 - spost, 0 + spost);<span class="kom">// Texturové koordináty (1, 0)</span></p>
<p class="src3">glVertex2f(640, 480);<span class="kom">// Tøetí vertex (640, 480)</span></p>
<p class="src"></p>
<p class="src3">glTexCoord2f(1 - spost, 1 - spost);<span class="kom">// Texturové koordináty (1, 1)</span></p>
<p class="src3">glVertex2f(640, 0);<span class="kom">// Ètvrtý vertex (640, 0)</span></p>
<p class="src"></p>
<p class="src3">spost += inc;<span class="kom">// Postupné zvy¹ování skvrn (zoomování do støedu textury)</span></p>
<p class="src3">alpha = alpha - alphainc;<span class="kom">// Postupné sni¾ování alfy (blednutí obrázku)</span></p>
<p class="src2">}</p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení</span></p>

<p>Zbývá obnovit pùvodní parametry.</p>

<p class="src1">ViewPerspective();<span class="kom">// Obnovení perspektivy</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>
<p class="src1">glDisable(GL_TEXTURE_2D);<span class="kom">// Vypne mapování textur</span></p>
<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vypne blending</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, 0);<span class="kom">// Zru¹ení vybrané textury</span></p>
<p class="src0">}</p>

<p>Draw() je tentokrát opravdu krátká. Nastavíme èerné pozadí, sma¾eme obrazovku i hloubku a resetujeme matici. Vyrenderujeme spirálu do textury, potom i na obrazovku a nakonec vykreslíme blur efekt.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslení scény</span></p>
<p class="src0">{</p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubku</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">RenderToTexture();<span class="kom">// Rendering do textury</span></p>
<p class="src1">ProcessHelix();<span class="kom">// Rendering spirály</span></p>
<p class="src1">DrawBlur(25, 0.02f);<span class="kom">// Rendering blur efektu</span></p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vyprázdnìní OpenGL pipeline</span></p>
<p class="src0">}</p>

<p>Doufám, ¾e se vám tento tutoriál líbil. Nenauèili jste se sice nic víc ne¾ rendering do textury, ale výsledný efekt vypadá opravdu skvìle.</p>

<p>Máte svobodu v pou¾ívání tohoto kódu ve svých programech jakkoli chcete, ale pøed tím, ne¾ tak uèiníte, podívejte se na nìj a pochopte ho - jediná podmínka! Abych nezapomnìl, uveïte mì prosím do kreditù.</p>

<p>Tady vám nechávám seznam úloh, které si mù¾ete zkusit vyøe¹it:</p>

<ul>
<li>Modifikujte funkci DrawBlur() tak, abyste získali horizontální rozmazání, vertikální rozmazání nebo dal¹í efekty (Twirl blur)</li>
<li>Pohrajte si s parametry DrawBlur() (pøidat, odstranit), abyste grafiku synchronizovali s hudbou</li>
<li>Modifikujte parametry textury - napø. GL_LUMINANCE (hezké stínování)</li>
<li>Zkuste super fale¹né volumetrické stínování pou¾itím tmavých textur namísto luminance textury</li>
</ul>

<p>Tak to u¾ bylo opravdu v¹echno. Zkuste nav¹tívit mé webové stránky <?OdkazBlank('http://www.spinningkids.org/rio');?>, naleznete tam nìkolik dal¹ích tutoriálù...</p>

<p class="autor">napsal: Dario Corno - rIo <?VypisEmail('rio@spinningkids.org');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson36.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson36_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson36.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson36.zip">Delphi</a> kód této lekce. ( <a href="mailto:Eshat@gmx.net">Eshat Cakar</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson36.zip">Dev C++</a> kód této lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson36.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Schubert_P@Yahoo.de">Patrick Schubert</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson36.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:ant@solace.mh.se">Anthony Whitehead</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson36.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson36.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:dario@solinf.it">Dario Corno</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson36.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(36);?>
<?FceNeHeOkolniLekce(36);?>

<?
include 'p_end.php';
?>
