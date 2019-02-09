<?
$g_title = 'CZ NeHe OpenGL - Lekce 18 - Quadratics';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(18);?>

<h1>Lekce 18 - Kvadriky</h1>

<p class="nadpis_clanku">Pøedstavuje se vám bájeèný svìt kvadrikù. Jedním øádkem kódu snadno vytváøíte komplexní objekty typu koule, disku, válce ap. Pomocí matematiky a trochy plánování lze snadno morphovat jeden do druhého.</p>

<p>Quadratic (neznám èeský ekvivalent slova, tak¾e zùstanu u pùvodní verze) je jednoduchou cestou k vykreslení komplexních objektù. Na pozadí pracují na nìkolika cyklech for a tro¹e trigonometrie. Rozvineme kód z lekce 7, pøidáme pár promìnných a aby byla také nìjaká zmìna, pou¾ijeme jinou texturu</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavièkový soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavièkový soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Privátní GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trvalý Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na¹eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>
<p class="src"></p>
<p class="src0">bool light;<span class="kom">// Svìtlo ON/OFF</span></p>
<p class="src0">bool lp;<span class="kom">// L stisknuté? </span></p>
<p class="src0">bool fp;<span class="kom">// F stisknuté? </span></p>
<p class="src0">bool sp;<span class="kom">// Stisknutý mezerník?</span></p>
<p class="src"></p>
<p class="src0">int part1;<span class="kom">// Zaèátek disku</span></p>
<p class="src0">int part2;<span class="kom">// Konec disku</span></p>
<p class="src0">int p1=0;<span class="kom">// Pøírùstek 1</span></p>
<p class="src0">int p2=1;<span class="kom">// Pøírùstek 2</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot;<span class="kom">// X rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y rotace</span></p>
<p class="src0">GLfloat xspeed;<span class="kom">// Rychlost x rotace</span></p>
<p class="src0">GLfloat yspeed;<span class="kom">// Rychlost y rotace</span></p>
<p class="src0">GLfloat z=-5.0f;<span class="kom">// Hloubka v obrazovce</span></p>
<p class="src"></p>
<p class="src0">GLUquadricObj *quadratic;<span class="kom">// Bude ukládat kvadrik</span></p>
<p class="src0"></p>
<p class="src"></p>
<p class="src0">GLfloat LightAmbient[]= { 0.5f, 0.5f, 0.5f, 1.0f };<span class="kom">// Okolní svìtlo</span></p>
<p class="src0">GLfloat LightDiffuse[]= { 1.0f, 1.0f, 1.0f, 1.0f };<span class="kom">// Pøímé svìtlo</span></p>
<p class="src0">GLfloat LightPosition[]= { 0.0f, 0.0f, 2.0f, 1.0f };<span class="kom">// Pozice svìtla</span></p>
<p class="src"></p>
<p class="src0">GLuint filter;<span class="kom">// Typ filtru</span></p>
<p class="src0">GLuint texture[3];<span class="kom">// Místo pro 3 textury</span></p>
<p class="src0">GLuint object=0;<span class="kom">// Urèuje aktuálnì vykreslovaný objekt</span></p>

<p>Pøesuneme se do funkce InitGL(), kde inicializujeme kvadrik. Na konec, ale pøed return, pøidáme následující kód této lekce. ( V prvním øádku vytvoøíme nový kvadrik (funkce na nìj vrátí ukazatel, pøi chybì nulu). Aby svìtlo vypadalo opravdu perfektnì nastavíme normálové vektory na GLU_SMOOTH (dal¹í mo¾né hodnoty GLU_NONE a GLU_FLAT). Nakonec zapneme texturové mapování. Je celkem "neohrabané", proto¾e nemù¾eme naplánovat, co kam namapujeme - v¹echno se generuje automaticky.</p>

<p class="src0">quadratic=gluNewQuadric();<span class="kom">// Vrátí ukazatel na nový kvadrik</span></p>
<p class="src0">gluQuadricNormals(quadratic, GLU_SMOOTH);<span class="kom">// Vygeneruje normálové vektory (hladké)</span></p>
<p class="src0">gluQuadricTexture(quadratic, GL_TRUE);<span class="kom">// Vygeneruje texturové koordináty</span></p>

<p>Rozhodl jsem se, ¾e pùvodní krychli z lekce 7 nesma¾u, ale ¾e ji zde ponechám. Mìli byste si uvìdomit, ¾e stejnì jako mapujeme textury na námi vytvoøený objekt, tak se úplnì stejnì mapují na kvadriky.</p>

<p class="src0">GLvoid glDrawCube()<span class="kom">// Vykreslí krychli</span></p>
<p class="src0">{</p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// Pøední stìna</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Zadní stìna</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Vrchní stìna</span></p>
<p class="src2">glNormal3f( 0.0f, 1.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Spodní stìna</span></p>
<p class="src2">glNormal3f( 0.0f,-1.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Pravá stìna</span></p>
<p class="src2">glNormal3f( 1.0f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Levá stìna</span></p>
<p class="src2">glNormal3f(-1.0f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>Ve funkci DrawGLScene() se program vìtví podle druhu objektu, který chceme kreslit (ku¾el, válec, koule...). Do v¹ech funkcí zaji¹»ujících vykreslování (kromì na¹í krychle) se pøidává parametr &quot;quadratic&quot;.</p>

<p class="src0">int DrawGLScene(GLvoid)</p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,z);</p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);</p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter]);<span class="kom">// Vybere texturu</span></p>
<p class="src"></p>
<p class="src1">switch(object)<span class="kom">// Vybere, co se bude kreslit</span></p>
<p class="src1">{</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_krychle.jpg" width="127" height="129" alt="Krychle" /></div>

<p class="src2">case 0:</p>
<p class="src3">glDrawCube();<span class="kom">// Krychle</span></p>
<p class="src3">break;</p>

<p>Dal¹ím objektem bude válec. Prvním parametrem je spodní polomìr. Druhý urèuje horní polomìr. Pøedáním rozdílných hodnot se vykreslí jiný tvar (zu¾ující trubka, popø. ku¾el). Tøetí parametr specifikuje vý¹ku/délku (vzdálenost základen). Ètvrtá hodnota znaèí mno¾ství polygonù "kolem" osy Z a pátá poèet polygonù &quot;na&quot; ose Z. Napøíklad pou¾itím 5 místo první 32 nevykreslíte válec, ale hranatou trubku, její¾ podstava je tvoøena pravidelným pìtiúhelníkem. Naopak rozdíl pøi zámìnì druhé 32 snad ani nepoznáte. Èím je tìchto polygonù více, tím se zvìt¹í kvalita (poèet detailù) výstupu. Musím ale podtrhnout, ¾e se program zpomalí. Sna¾te se v¾dy najít nìjakou rozumnou hodnotu.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_valec.jpg" width="127" height="129" alt="Válec" /></div>

<p class="src2">case 1:</p>
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrování válce</span></p>
<p class="src3">gluCylinder(quadratic,1.0f,1.0f,3.0f,32,32);<span class="kom">// Válec</span></p>
<p class="src3">break;</p>

<p>Tøetím vytváøeným objektem bude disk tvaru CD. První parametr urèuje vnitøní polomìr - pokud zadáte nulu vykreslí se celistvý (bez støedového kruhu). Druhu hodnotou je vnìj¹í polomìr (zadá-li se o málo vìt¹í ne¾ vnitøní vytvoøíte prsten). Dejte si pozor, abyste nezadali vnìj¹í men¹í ne¾ vnitøní. Nespadne vám sice program, ale nic neuvidíte. Tøetím parametrem je poèet plátkù, jako kdy¾ se krájí pizza. Èím jich bude více, tím budou okraje ménì zubaté (napøi. zadáním 5 vykreslíte pravidelný pìtiúhelník). Posledním pøedávané èíslo znaèí poèet kru¾nic - analogie spirále na CD nebo gramofonové desce. Opìt nemá moc velký vliv na kvalitu.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_disk.jpg" width="127" height="129" alt="Disk" /></div>

<p class="src2">case 2:</p>
<p class="src3">gluDisk(quadratic,0.5f,1.5f,32,32);<span class="kom">// Disk ve tvaru CD</span></p>
<p class="src3">break;</p>

<p>Následuje objekt, o kterém pøemý¹líte v dlouhých bezesných nocích... koule. Staèí jedna funkce. Nejdùle¾itìj¹ím parametrem je polomìr - netøeba vysvìtlovat. Pokud byste ale chtìli jít je¹tì dál, zmìòte pøed vykreslením mìøítko jednotlivých os (glScalef(x,y,z)). Vytvoøíte zaoblený tvar, který mi v první chvíli pøipomínal ozdobu na stromeèek (¹i¹ka - zplo¹tìlá koule). Popø. zkuste zmen¹it první 32 na 5. Vytvoøíte hranatou (krychloidní :-o) kouli. Jak to popsat... kdybyste ji pøes støed rozdìlili rovinou, øezem bude pìtiúhelník, ale druhým øezem kolmým na první bude stále koule.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_koule.jpg" width="127" height="129" alt="Koule" /></div>

<p class="src2">case 3:</p>
<p class="src3"><span class="kom">// glScalef(1.0f,0.5f,1.0f);// Pøekl.: Zmìna mìøítka</span></p>
<p class="src3">gluSphere(quadratic,1.3f,32,32);<span class="kom">// Koule</span></p>
<p class="src3"><span class="kom">// glScalef(1.0f,2.0f,1.0f);// Pøekl.: Obnovení mìøítka</span></p>
<p class="src3">break;</p>

<p>U¾ jsem trochu nakousl u válce, ¾e ku¾el se vytváøí témìø stejnì. Pøedáte jeden polomìr rovný nule, tudí¾ se na jednom konci objeví ¹pièka.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_kuzel.jpg" width="127" height="129" alt="Ku¾el" /></div>

<p class="src2">case 4:</p>
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrování ku¾ele</span></p>
<p class="src3">gluCylinder(quadratic,1.0f,0.0f,3.0f,32,32);<span class="kom">// Ku¾el</span></p>
<p class="src3">break;</p>

<p>©estý tvar vytvoøíme pøíkazem gluPartialDisk(). Tento disk bude skoro stejný jako disk vý¹e, nicménì dal¹í dva parametry funkce zajistí, ¾e se nebude vykreslovat celý. Parametr part1 specifikuje poèáteèní úhel, od kterého chceme kreslit a asi si domyslíte, ¾e ten druhý urèuje úhel, za kterým se u¾ nic nevykreslí. Je vzta¾en k tomu prvnímu, tak¾e pokud první nastavíme na 30 a druhý na 90 pøestane se kreslit na 30° + 90° = 120°. My se rovnou pokusíme o "level 2" - zkusíme pøidat jednoduchou animaci, kdy se disk bude pøekreslovat (po smìru hodinových ruèièek). Nejdøíve zvy¹ujeme pøírùstkový úhel. Jakmile dosáhne 360° (jeden obìh), zaèneme zvy¹ovat poèáteèní úhel - opìt do 360° atd.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_18_part_disk.jpg" width="127" height="129" alt="Èásteèný disk" /></div>

<p class="src2">case 5:</p>
<p class="src3">part1+=p1;<span class="kom">// Inkrementace poèáteèního úhlu</span></p>
<p class="src3">part2+=p2;<span class="kom">// Inkrementace pøírùstkového úhlu</span></p>
<p class="src"></p>
<p class="src3">if(part1&gt;359)<span class="kom">// 360°</span></p>
<p class="src3">{</p>
<p class="src4">p1=0;<span class="kom">// Zastaví zvìt¹ování poèáteèního úhlu (part1+=0;)</span></p>
<p class="src4">part1=0;<span class="kom">// Vynulování poèáteèního úhlu</span></p>
<p class="src4">p2=1;<span class="kom">// Zaène zvìt¹ovat pøírùstkový úhel</span></p>
<p class="src4">part2=0;<span class="kom">// Vynulování pøírùstkového úhlu</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if(part2&gt;359)<span class="kom">// 360°</span></p>
<p class="src3">{</p>
<p class="src4">p1=1;<span class="kom">// Zaène zvìt¹ovat poèáteèní úhel</span></p>
<p class="src4">p2=0;<span class="kom">// Pøestane zvìt¹ovat pøírùstkový úhel</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">gluPartialDisk(quadratic,0.5f,1.5f,32,32,part1,part2-part1);<span class="kom">// Neúplný disk</span></p>
<p class="src3">break;</p>
<p class="src1">};</p>
<p class="src"></p>
<p class="src1">xrot+=xspeed;<span class="kom">// Inkrementace rotace</span></p>
<p class="src1">yrot+=yspeed;<span class="kom">// Inkrementace rotace</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Pøidáme ovládání klávesnicí - pokud stisknete mezerník objekt se zmìní na následující v poøadí.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src1">if(keys[' '] &amp;&amp; !sp)<span class="kom">// Stisknutý mezerník?</span></p>
<p class="src1">{</p>
<p class="src2">sp=TRUE;</p>
<p class="src2">object++;<span class="kom">// Cyklování objekty</span></p>
<p class="src2">if(object>5)<span class="kom">// O¹etøení pøeteèení</span></p>
<p class="src3">object=0;</p>
<p class="src1">}</p>
<p class="src1">if(!keys[' '])<span class="kom">// Uvolnìní mezerníku?</span></p>
<p class="src1">{</p>
<p class="src2">sp=FALSE;</p>
<p class="src1">}</p>

<p>Tak¾e to je v¹e. Mìli byste umìt v OpenGL vykreslovat jakýkoli kvadrik. Pomocí morphingu a kvadrikù se dá dosáhnout zajímavých efektù. Pøíkladem budi¾ námi animovaný disk.</p>

<p class="autor">napsal: <?OdkazBlank('http://www.tiptup.com/', 'GB Schmick - TipTup');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson18.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson18_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson18.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson18.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson18.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson18.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson18.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson18.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson18.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson18.tar.gz">Irix / GLUT</a> kód této lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson18.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson18.jar">JoGL</a> kód této lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson18.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson18.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:rgbe@yahoo.com">Simon Werner</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson18.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:miqster@gmx.net">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson18.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson18.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson18.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson18.zip">MASM</a> kód této lekce. ( <a href="mailto:chris.j84@free.fr">Christophe</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson18.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/sdl/lesson18.tar.gz">SDL</a> kód této lekce. ( <a href="mailto:kjrockot@home.com">Ken Rockot</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson18.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:thegilb@hotmail.com">The Gilb</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson18.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(18);?>
<?FceNeHeOkolniLekce(18);?>

<?
include 'p_end.php';
?>
