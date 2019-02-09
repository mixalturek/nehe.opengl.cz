<?
$g_title = 'CZ NeHe OpenGL - Lekce 25 - Morfování objektù a jejich nahrávání z textového souboru';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(25);?>

<h1>Lekce 25 - Morfování objektù a jejich nahrávání z textového souboru</h1>

<p class="nadpis_clanku">V této lekci se nauèíte, jak nahrát souøadnice vrcholù z textového souboru a plynulou transformaci z jednoho objektu na druhý. Nezamìøíme se ani tak na grafický výstup jako spí¹e na efekty a potøebnou matematiku okolo. Kód mù¾e být velice jednodu¹e modifikován k vykreslování linkami nebo polygony.</p>

<p>Poznamenejme, ¾e ka¾dý objekt by mìl být seskládán ze stejného poètu bodù jako v¹echny ostatní. Je to sice hodnì omezující po¾adavek, ale co se dá dìlat - chceme pøece, aby zmìny vypadaly dobøe. Zaèneme vlo¾ením hlavièkových souborù. Tentokrát nepou¾íváme textury, tak¾e se obejdeme bez glaux.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavièkový soubor pro matematickou knihovnu</span></p>
<p class="src"></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavièkový soubor pro Glu32 knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Privátní GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trvalý Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na¹eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>Po deklarování v¹ech standardních promìnných pøidáme nové. Rot ukládají aktuální úhel rotace na jednotlivých souøadnicových osách, speed definují rychlost rotace. Poslední tøi desetinné promìnné urèují pozici ve scénì.</p>

<p class="src0">GLfloat xrot, yrot, zrot;<span class="kom">// Rotace</span></p>
<p class="src0">GLfloat xspeed, yspeed, zspeed;<span class="kom">// Rychlost rotace</span></p>
<p class="src0">GLfloat cx, cy, cz = -15;<span class="kom">// Pozice</span></p>

<p>Aby se program zbyteènì nezpomaloval pøi pokusech morfovat objekt sám na sebe, deklarujeme key, který oznaèuje právì zobrazený objekt. Morph v programu indikuje, jestli právì provádíme transformaci objektù nebo ne. V ustáleném stavu má hodnotu FALSE.</p>

<p class="src0">int key = 1;<span class="kom">// Právì zobrazený objekt</span></p>
<p class="src0">bool morph = FALSE;<span class="kom">// Probíhá právì morfování?</span></p>

<p>Pøiøazením 200 do steps urèíme, ¾e zmìna jednoho objektu na druhý bude trvat 200 pøekreslení. Èím vìt¹í èíslo zadáme, tím budou pøemìny plynulej¹í, ale zároveò ménì pomalé. No a step definuje èíslo právì provádìného kroku.</p>

<p class="src0">int steps = 200;<span class="kom">// Poèet krokù zmìny</span></p>
<p class="src0">int step = 0;<span class="kom">// Aktuální krok</span></p>

<p>Struktura VERTEX obsahuje x, y, z slo¾ky pozice jednoho bodu ve 3D prostoru.</p>

<p class="src0">typedef struct<span class="kom">// Struktura pro bod ve 3D</span></p>
<p class="src0">{</p>
<p class="src1">float x, y, z;<span class="kom">// X, y, z slo¾ky pozice</span></p>
<p class="src0">} VERTEX;<span class="kom">// Nazvaný VERTEX</span></p>

<p>Pokusíme se o vytvoøení struktury objektu. co v¹echno budeme potøebovat? Tak urèitì to bude nìjaké pole pro ulo¾ení v¹ech vrcholù. Abychom ho mohli v prùbìhu programu libovolnì mìnit, deklarujeme jej jako ukazatel do dynamické pamìti. Celoèíselná promìnná vert specifikuje maximální mo¾ný index tohoto pole a vlastnì i poèet bodù, ze kterých se skládá.</p>

<p class="src0">typedef struct<span class="kom">// Struktura objektu</span></p>
<p class="src0">{</p>
<p class="src1">int verts;<span class="kom">// Poèet bodù, ze kterých se skládá</span></p>
<p class="src1">VERTEX* points;<span class="kom">// Ukazatel do pole vertexù</span></p>
<p class="src0">} OBJECT;<span class="kom">// Nazvaný OBJECT</span></p>

<p>Pokud bychom se nedr¾eli zásady, ¾e v¹echny objekty musí mít stejný poèet vrcholù, vznikly by komplikace. Dají se vyøe¹it promìnnou, která obsahuje èíslo maximálního poètu souøadnic. Uveïme pøíklad: jeden objekt bude krychle s osmi vrcholy a druhý pyramida se ètyømi. Do maxver tedy ulo¾íme èíslo osm. Nicménì stejnì doporuèuji, aby mìly v¹echny objekty stejný poèet bodù - v¹e bude jednodu¹¹í.</p>

<p class="src0">int maxver;<span class="kom">// Eventuálnì ukládá maximální poèet bodù v jednom objektu</span></p>

<p>První tøi instance struktury OBJECT ukládají data, která nahrajeme ze souborù. Do ètvrtého vygenerujeme náhodná èísla - body náhodnì rozházené po obrazovce. Helper je objekt pro vykreslování. Obsahuje mezistavy získané kombinací objektù v urèitém kroku morfingu. Poslední dvì promìnné jsou ukazatele na zdrojový a výsledný objekt, které chce u¾ivatel zamìnit.</p>

<p class="src0">OBJECT morph1, morph2, morph3, morph4;<span class="kom">// Koule, toroid, válec (trubka), náhodné body</span></p>
<p class="src0">OBJECT helper, *sour, *dest;<span class="kom">// Pomocný, zdrojový a cílový objekt</span></p>

<p>Ve funkci objallocate() alokujeme pamì» pro strukturu objektu, na který ukazuje pointer *k pøedaný parametrem. Celoèíselné n definuje poèet vrcholù objektu.</p>

<p>Funkci malloc(), která vrací ukazatel na dynamicky alokovanou pamì» pøedáme její po¾adovanou velikost. Získáme ji operátorem sizeof() vynásobeným poètem vertexù. Proto¾e malloc() vrací ukazatel na void, musíme ho pøetypovat.</p>

<p>Pozn. pøekl.: Program by je¹tì mìl otestovat jestli byla opravdu alokována. Kdyby se operace nezdaøila, program by pøistupoval k nezabrané pamìti a aplikace by se zcela jistì zhroutila. Malloc() v pøípadì neúspìchu vrací NULL.</p>

<p class="src0">void objallocate(OBJECT *k,int n)<span class="kom">// Alokuje dynamickou pamì» pro objekt</span></p>
<p class="src0">{</p>
<p class="src1">k-&gt;points = (VERTEX*) malloc(sizeof(VERTEX) * n);<span class="kom">// Alokuje pamì»</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pøekladatel:</span></p>
<p class="src1"><span class="kom">// if(k-&gt;points == NULL)</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// MessageBox(NULL,&quot;Chyba pøi alokaci pamìti pro objekt&quot;, &quot;ERROR&quot;, MB_OK | MB_ICONSTOP);</span></p>
<p class="src2"><span class="kom">// Ukonèit program</span></p>
<p class="src1"><span class="kom">// }</span></p>
<p class="src0">}</p>

<p>Po ka¾dé alokaci dynamické pamìti musí V®DY pøijít její uvolnìní. Funkci opìt pøedáváme ukazatel na objekt.</p>

<p class="src0">void objfree(OBJECT *k)<span class="kom">// Uvolní dynamickou pamì» objektu</span></p>
<p class="src0">{</p>
<p class="src1">free(k-&gt;points);<span class="kom">// Uvolní pamì»</span></p>
<p class="src0">}</p>

<p>Funkce readstr() je velmi podobná (úplnì stejná) jako v lekci 10. Naète jeden øádek ze souboru f a ulo¾í ho do øetìzce string. Abychom mohli udr¾et data souboru pøehledná funkce pøeskakuje prázdné øádky (\n) a c-éèkovské komentáøe (øádky zaèínající //, respektive '/').</p>

<p class="src0">void readstr(FILE *f,char *string)<span class="kom">// Naète jeden pou¾itelný øádek ze souboru</span></p>
<p class="src0">{</p>
<p class="src1">do</p>
<p class="src1">{</p>
<p class="src2">fgets(string, 255, f);<span class="kom">// Naèti øádek</span></p>
<p class="src1">} while ((string[0] == '/') || (string[0] == '\n'));<span class="kom">// Pokud není pou¾itelný, naèti dal¹í</span></p>
<p class="src1">return;</p>
<p class="src0">}</p>

<p>Napí¹eme funkci pro loading objektu z textového souboru. Name specifikuje diskovou cestu k souboru a k je ukazatel na objekt, do kterého ulo¾íme výsledek.</p>

<p class="src0">void objload(char *name,OBJECT *k)<span class="kom">// Nahraje objekt ze souboru</span></p>
<p class="src0">{</p>

<p>Zaèneme deklarací lokálních promìnných funkce. Do ver naèteme poèet vertexù, který urèuje první øádka v souboru (více dále). Dá se øíct, ¾e rx, ry, rz jsou pouze pro zpøehlednìní zdrojového kódu programu. Ze souboru do nich naèteme jednotlivé slo¾ky bodu. Ukazatel filein ukazuje na soubor (po otevøení). Oneline je znakový buffer. V¾dy do nìj naèteme jednu øádku, analyzujeme ji a získáme informace, které potøebujeme.</p>

<p class="src1">int ver;<span class="kom">// Poèet bodù</span></p>
<p class="src1">float rx, ry, rz;<span class="kom">// X, y, z pozice</span></p>
<p class="src1">FILE* filein;<span class="kom">// Handle souboru</span></p>
<p class="src1">char oneline[255];<span class="kom">// Znakový buffer</span></p>

<p>Pomocí funkce fopen() otevøeme soubor pro ètení. Pozn. pøekl.: Stejnì jako u alokace pamìti i zde chybí o¹etøení chyb.</p>

<p class="src1">filein = fopen(name, &quot;rt&quot;);<span class="kom">// Otevøe soubor</span></p>

<p class="src1"><span class="kom">// Pøekladatel:</span></p>
<p class="src1"><span class="kom">// if(filein == NULL)</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// MessageBox(NULL,&quot;Chyba pøi otevøení souboru s daty&quot;, &quot;ERROR&quot;, MB_OK | MB_ICONSTOP);</span></p>
<p class="src2"><span class="kom">// Ukonèit program</span></p>
<p class="src1"><span class="kom">// }</span></p>

<p>Do znakového bufferu naèteme první øádku. Mìla by být ve tvaru: <span class="src0">Vertices: x\n</span>. Z øetìzce tedy potøebujeme vydolovat èíslo x, které udává poèet vertexù definovaných v souboru. Tento poèet ulo¾íme do vnitøní promìnné struktury a potom alokujeme tolik pamìti, aby se do ní v¹echny koordináty souøadnic ve¹ly.</p>

<p class="src1">readstr(filein, oneline);<span class="kom">// Naète první øádku ze souboru</span></p>
<p class="src1">sscanf(oneline, &quot;Vertices: %d\n&quot;, &amp;ver);<span class="kom">// Poèet vertexù</span></p>
<p class="src"></p>
<p class="src1">k-&gt;verts = ver;<span class="kom">// Nastaví polo¾ku struktury na správnou hodnotu</span></p>
<p class="src"></p>
<p class="src1">objallocate(k, ver);<span class="kom">// Alokace pamìti pro objekt</span></p>

<p>U¾ tedy víme z kolikati bodù je objekt vytvoøen a máme alokovánu potøebnou pamì». Nyní je¹tì musíme naèíst jednotlivé hodnoty. Provedeme to cyklem for s øídící promìnnou i, která se ka¾dým prùchodem inkrementuje. Postupnì naèteme v¹echny øádky do bufferu, ze kterého pøes funkci sscanf() dostaneme èíselné hodnoty slo¾ek vertexu pro v¹echny tøi souøadnicové osy. Pomocné promìnné zkopírujeme do promìnných struktury. Po analýze celého souboru ho zavøeme.</p>

<p>Je¹tì musím upozornit, ¾e je dùle¾ité, aby soubor obsahoval stejný poèet bodù jako je definováno na zaèátku. Pokud by jich bylo více, tolik by to nevadilo - poslední by se prostì nenaèetly. V ¾ádném pøípadì jich ale NESMÍ být ménì! S nejvìt¹í pravdìpodobností by to zhroutilo program. V¹e, na co se pokou¹ím upozornit by se dalo shrnout do vìty: Jestli¾e soubor zaèíná &quot;Vertices: 10&quot;, musí v nìm být specifikováno 10 souøadnic (30 èísel - x, y, z).</p>

<p class="src1">for (int i = 0; i &lt; ver; i++)<span class="kom">// Postupnì naèítá body</span></p>
<p class="src1">{</p>
<p class="src2">readstr(filein, oneline);<span class="kom">// Naète øádek ze souboru</span></p>
<p class="src"></p>
<p class="src2">sscanf(oneline, &quot;%f %f %f&quot;, &amp;rx, &amp;ry, &amp;rz);<span class="kom">// Najde a ulo¾í tøi èísla</span></p>
<p class="src"></p>
<p class="src2">k-&gt;points[i].x = rx;<span class="kom">// Nastaví vnitøní promìnnou struktury</span></p>
<p class="src2">k-&gt;points[i].y = ry;<span class="kom">// Nastaví vnitøní promìnnou struktury</span></p>
<p class="src2">k-&gt;points[i].z = rz;<span class="kom">// Nastaví vnitøní promìnnou struktury</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">fclose(filein);<span class="kom">// Zavøe soubor</span></p>

<p>Otestujeme, zda není promìnná ver (poèet bodù aktuálního objektu) vìt¹í ne¾ maxver (v souèasnosti maximální známý poèet vertexù v jednom objektu). Pokud ano pøiøadíme ver do maxver.</p>

<p class="src1">if(ver &gt; maxver)<span class="kom">// Aktualizuje maximální poèet vertexù</span></p>
<p class="src2">maxver = ver;</p>
<p class="src0">}</p>

<p>Na øadu pøichází trochu ménì pochopitelná funkce - zvlá¹» pro ty, kteøí nemají v lásce matematiku. Bohu¾el morfing na ní staví. Co tedy dìlá? Spoèítá o klik máme posunout bod specifikovaný parametrem i. Na zaèátku deklarujeme pomocný vertex, podle vzorce spoèítáme jeho jednotlivé x, y, z slo¾ky a v závìru ho vrátíme volající funkci.</p>

<p>Pou¾itá matematika pracuje asi takto: od souøadnice i-tého bodu zdrojového objektu odeèteme souøadnici bodu, do kterého morfujeme. Rozdíl vydìlíme zamý¹leným poètem krokù a koneèný výsledek ulo¾íme do a.</p>

<p>Øeknìme, ¾e x-ové souøadnice zdrojového objektu (sour) je rovna ètyøiceti a cílového objektu (dest) dvaceti. U deklarace globálních promìnných jsme steps pøiøadili 200. Výpoètem a.x = (40-20)/200 = 20/200 = 0,1 zjistíme, ¾e pøi pøesunu ze 40 na 20 s krokem 200 potøebujeme ka¾dé pøekreslení pohnout na ose x bodem o desetinu jednotky. Nebo jinak: násobíme-li 200*0,1 dostaneme rozdíl pozic 20, co¾ je také pravda (40-20=20). Mìlo by to fungovat.</p>

<p class="src0">VERTEX calculate(int i)<span class="kom">// Spoèítá o kolik pohnout bodem pøi morfingu</span></p>
<p class="src0">{</p>
<p class="src1">VERTEX a;<span class="kom">// Pomocný bod</span></p>
<p class="src"></p>
<p class="src1">a.x = (sour-&gt;points[i].x - dest-&gt;points[i].x) / steps;<span class="kom">// Spoèítá posun</span></p>
<p class="src1">a.y = (sour-&gt;points[i].y - dest-&gt;points[i].y) / steps;<span class="kom">// Spoèítá posun</span></p>
<p class="src1">a.z = (sour-&gt;points[i].z - dest-&gt;points[i].z) / steps;<span class="kom">// Spoèítá posun</span></p>
<p class="src"></p>
<p class="src1">return a;<span class="kom">// Vrátí výsledek</span></p>
<p class="src0">}</p>

<p>Zaèátek inicializaèní funkce není ¾ádnou novinkou, ale dále v kódu najdete zmìny.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echno nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE);<span class="kom">// Typ blendingu</span></p>
<p class="src1"><span class="kom">// glEnable(GL_BLEND);// Zapne blending (pøekl.: autor asi zapomnìl)</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// Èerné pozadí</span></p>
<p class="src"></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolení testování hloubky</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>

<p>Proto¾e je¹tì neznáme maximální poèet bodù v jednom objektu, pøiøadíme do maxver nulu. Poté pomocí funkce objload() naèteme z disku data jednotlivých objektù (koule, toroid, válec). V prvním parametru pøedáváme cestu se jménem souboru, ve druhém adresu objektu, do kterého se mají data ulo¾it.</p>

<p class="src1">maxver = 0;<span class="kom">// Nulování maximálního poètu bodù</span></p>
<p class="src"></p>
<p class="src1">objload(&quot;data/sphere.txt&quot;, &amp;morph1);<span class="kom">//Naète kouli</span></p>
<p class="src1">objload(&quot;data/torus.txt&quot;, &amp;morph2);<span class="kom">// Naète toroid</span></p>
<p class="src1">objload(&quot;data/tube.txt&quot;, &amp;morph3);<span class="kom">// Naète válec</span></p>

<p>Ètvrtý objekt nenaèítáme ze souboru. Budou jím po scénì rozházené body (pøesnì 486 bodù). Nejdøíve musíme alokovat pamì» pro jednotlivé vertexy a potom staèí v cyklu vygenerovat náhodné souøadnice. Budou v rozmezí od -7 do +7.</p>

<p class="src1">objallocate(&amp; morph4, 486);<span class="kom">// Alokace pamìti pro 486 bodù</span></p>
<p class="src"></p>
<p class="src1">for(int i=0; i &lt; 486; i++)<span class="kom">// Cyklus generuje náhodné souøadnice</span></p>
<p class="src1">{</p>
<p class="src2">morph4.points[i].x = ((float)(rand() % 14000) / 1000) - 7;<span class="kom">// Náhodná hodnota</span></p>
<p class="src2">morph4.points[i].y = ((float)(rand() % 14000) / 1000) - 7;<span class="kom">// Náhodná hodnota</span></p>
<p class="src2">morph4.points[i].z = ((float)(rand() % 14000) / 1000) - 7;<span class="kom">// Náhodná hodnota</span></p>
<p class="src1">}</p>

<p>Ze souborù jsme loadovali v¹echny objekty do struktur. Jejich data u¾ nebudeme upravovat. Od teï jsou jen pro ètení. Potøebujeme tedy je¹tì jeden objekt, helper, který bude pøi morfingu ukládat jednotlivé mezistavy. Proto¾e na zaèátku zobrazujeme morp1 (koule) naèteme i do pomocného tento objekt.</p>

<p class="src1">objload(&quot;data/sphere.txt&quot;, &amp;helper);<span class="kom">// Naètení koule do pomocného objektu</span></p>

<p>Nastavíme je¹tì pointery pro zdrojový a cílový objekt, tak aby ukazovali na adresu morph1.</p>

<p class="src1">sour = dest = &amp;morph1;<span class="kom">// Inicializace ukazatelù na objekty</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Ukonèí funkci</span></p>
<p class="src0">}</p>

<p>Vykreslování zaèneme klasicky smazáním obrazovky a hloubkového bufferu, resetem matice, posunem a rotacemi. Místo abychom v¹echny pohyby provádìli na konci funkce, tentokrát je umístíme na zaèátek. Poté deklarujeme pomocné promìnné. Do tx, ty, tz spoèítáme souøadnice, které pak pøedáme funkci glVertex3f() kvùli nakreslení bodu. Q je pomocný bod pro výpoèet.</p>

<p class="src0">void DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(cx,cy,cz);<span class="kom">// Pøesun na pozici</span></p>
<p class="src1">glRotatef(xrot, 1,0,0);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(yrot, 0,1,0);<span class="kom">// Rotace na ose y</span></p>
<p class="src1">glRotatef(zrot, 0,0,1);<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src1">xrot += xspeed;<span class="kom">// Zvìt¹í úhly rotace</span></p>
<p class="src1">yrot += yspeed;</p>
<p class="src1">zrot += zspeed;</p>
<p class="src"></p>
<p class="src1">GLfloat tx, ty, tz;<span class="kom">// Pomocné souøadnice</span></p>
<p class="src1">VERTEX q;<span class="kom">// Pomocný bod pro výpoèty</span></p>

<p>Pøes glBegin(GL_POINTS) oznámíme OpenGL, ¾e v blízké dobì budeme vykreslovat body. V cyklu for procházíme vertexy. Øídící promìnnou i bychom také mohli porovnávat s maxver, ale proto¾e mají v¹echny objekty stejný poèet souøadnic, mù¾eme s klidem pou¾ít poèet vertexù prvního objektu - morph1.verts.</p>

<p class="src1">glBegin(GL_POINTS);<span class="kom">// Zaèátek kreslení bodù</span></p>
<p class="src2">for(int i = 0; i &lt; morph1.verts; i++)<span class="kom">// Cyklus prochází vertexy</span></p>
<p class="src2">{</p>

<p>V pøípadì morfingu spoèítáme o kolik se má vykreslovaný bod posunout oproti pozici pøi minulém vykreslení. Takto vypoèítané hodnoty odeèteme od souøadnic pomocného objektu, do kterého ka¾dé pøekreslení ukládáme aktuální mezistav morfingu. Pokud se zrovna objekty mezi sebou netransformují odeèítáme nulu, tak¾e se souøadnice defakto nemìní.</p>

<p class="src3">if(morph)<span class="kom">// Pokud zrovna morfujeme</span></p>
<p class="src4">q = calculate(i);<span class="kom">// Spoèítáme hodnotu posunutí</span></p>
<p class="src3">else<span class="kom">// Jinak</span></p>
<p class="src4">q.x = q.y = q.z = 0;<span class="kom">// Budeme odeèítat nulu, ale tím neposouváme</span></p>
<p class="src"></p>
<p class="src3">helper.points[i].x -= q.x;<span class="kom">// Posunutí na ose x</span></p>
<p class="src3">helper.points[i].y -= q.y;<span class="kom">// Posunutí na ose y</span></p>
<p class="src3">helper.points[i].z -= q.z;<span class="kom">// Posunutí na ose z</span></p>

<p>Abychom si zpøehlednili program a také kvùli malièkému efektu, zkopírujeme právì získaná èísla do pomocných promìnných.</p>

<p class="src3">tx = helper.points[i].x;<span class="kom">// Zpøehlednìní + efekt</span></p>
<p class="src3">ty = helper.points[i].y;<span class="kom">// Zpøehlednìní + efekt</span></p>
<p class="src3">tz = helper.points[i].z;<span class="kom">// Zpøehlednìní + efekt</span></p>

<p>V¹echno máme spoèítáno, tak¾e pøejdeme k vykreslení. Nastavíme barvu na zelenomodrou a nakreslíme bod. Potom zvolíme trochu tmav¹í modrou barvu. Odeèteme dvojnásobek souøadnic q od t a získáme umístìní bodu pøi následujícím volání této funkce (ob jedno). Na této pozici znovu vykreslíme bod. Do tøetice v¹eho dobrého znovu ztmavíme barvu a opìt spoèítáme dal¹í pozici, na které se vyskytne po ètyøech prùchodech touto funkcí a opìt ho vykreslíme.</p>

<p>Proè jsme krásnì pøehledný kód vlastnì komplikovali? I kdy¾ si to asi neuvìdomujete, vytvoøili jsme jednoduchý èásticový systém. S pou¾itím blendingu vytvoøí perfektní efekt, který se ale bohu¾el projeví pouze pøi transformaci objektù z jednoho na druhý. Pokud zrovna nemorfujeme, v q souøadnicích jsou ulo¾eny nuly, tak¾e druhý a tøetí bod kreslíme na stejné místo jako první.</p>

<p class="src3">glColor3f(0, 1, 1);<span class="kom">// Zelenomodrá barva</span></p>
<p class="src3">glVertex3f(tx, ty, tz);<span class="kom">// Vykreslí první bod</span></p>
<p class="src"></p>
<p class="src3">glColor3f(0, 0.5f, 1);<span class="kom">// Modøej¹í zelenomodrá barva</span></p>
<p class="src3">tx -= 2*q.x;<span class="kom">// Spoèítání nových pozic</span></p>
<p class="src3">ty -= 2*q.y;</p>
<p class="src3">ty -= 2*q.y;</p>
<p class="src3">glVertex3f(tx, ty, tz);<span class="kom">// Vykreslí druhý bod v nové pozici</span></p>
<p class="src"></p>
<p class="src3">glColor3f(0, 0, 1);<span class="kom">// Modrá barva</span></p>
<p class="src3">tx -= 2*q.x;<span class="kom">// Spoèítání nových pozic</span></p>
<p class="src3">ty -= 2*q.y;</p>
<p class="src3">ty -= 2*q.y;</p>
<p class="src3">glVertex3f(tx, ty, tz);<span class="kom">// Vykreslí tøetí bod v nové pozici</span></p>

<p>Ukonèíme tìlo cyklu a glEnd() oznámí, ¾e dále u¾ nebudeme nic vykreslovat.</p>

<p class="src2">}</p>
<p class="src1">glEnd();<span class="kom">// Ukonèí kreslení</span></p>

<p>Jako poslední v této funkci zkontrolujeme jestli transformujeme objekty. Pokud ano a zároveò musí být aktuální krok morfingu men¹í ne¾ celkový poèet krokù, inkrementujeme aktuální krok. Po dokonèení morfingu ho vypneme. Proto¾e jsme u¾ do¹li k cílovému objektu, udìláme z nìj zdrojový. Krok reinicializujeme na nulu.</p>

<p class="src1">if(morph &amp;&amp; step &lt;= steps)<span class="kom">// Morfujeme a krok je men¹í ne¾ maximum</span></p>
<p class="src1">{</p>
<p class="src2">step++;<span class="kom">// Pøí¹tì pokraèuj následujícím krokem</span></p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Nemorfujeme nebo byl právì ukonèen</span></p>
<p class="src1">{</p>
<p class="src2">morph = FALSE;<span class="kom">// Konec morfingu</span></p>
<p class="src2">sour = dest;<span class="kom">// Cílový objekt je nyní zdrojový</span></p>
<p class="src2">step = 0;<span class="kom">// První (nulový) krok morfingu</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>KillGLWindow upravíme jenom málo. Uvolníme pouze dynamicky alokovanou pamì».</p>

<p class="src0">GLvoid KillGLWindow(GLvoid)<span class="kom">// Zavírání okna</span></p>
<p class="src0">{</p>
<p class="src1">objfree(&amp;morph1);<span class="kom">// Uvolní alokovanou pamì»</span></p>
<p class="src1">objfree(&amp;morph2);<span class="kom">// Uvolní alokovanou pamì»</span></p>
<p class="src1">objfree(&amp;morph3);<span class="kom">// Uvolní alokovanou pamì»</span></p>
<p class="src1">objfree(&amp;morph4);<span class="kom">// Uvolní alokovanou pamì»</span></p>
<p class="src1">objfree(&amp;helper);<span class="kom">// Uvolní alokovanou pamì»</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zbytek nezmìnìn</span></p>
<p class="src0">}</p>

<p>Ve funkci WinMain() upravíme kód testující stisk kláves. Následujícími ¹esti testy regulujeme rychlost rotace objektu.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if(keys[VK_PRIOR])<span class="kom">// PageUp?</span></p>
<p class="src5">zspeed += 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_NEXT])<span class="kom">// PageDown?</span></p>
<p class="src5">zspeed -= 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_DOWN])<span class="kom">// ©ipka dolu?</span></p>
<p class="src5">xspeed += 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_UP])<span class="kom">// ©ipka nahoru?</span></p>
<p class="src5">xspeed -= 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_RIGHT])<span class="kom">// ©ipka doprava?</span></p>
<p class="src5">yspeed += 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_LEFT])<span class="kom">// ©ipka doleva?</span></p>
<p class="src5">yspeed -= 0.01f;</p>

<p>Dal¹ích ¹est kláves pohybuje objektem po scénì.</p>

<p class="src4">if (keys['Q'])<span class="kom">// Q?</span></p>
<p class="src5">cz -= 0.01f;<span class="kom">// Dále</span></p>
<p class="src"></p>
<p class="src4">if (keys['Z'])<span class="kom">// Z?</span></p>
<p class="src5">cz += 0.01f;<span class="kom">// Blí¾e</span></p>
<p class="src"></p>
<p class="src4">if (keys['W'])<span class="kom">// W?</span></p>
<p class="src5">cy += 0.01f;<span class="kom">// Nahoru</span></p>
<p class="src"></p>
<p class="src4">if (keys['S'])<span class="kom">// S?</span></p>
<p class="src5">cy -= 0.01f;<span class="kom">// Dolu</span></p>
<p class="src"></p>
<p class="src4">if (keys['D'])<span class="kom">// D?</span></p>
<p class="src5">cx += 0.01f;<span class="kom">// Doprava</span></p>
<p class="src"></p>
<p class="src4">if (keys['A'])<span class="kom">// A?</span></p>
<p class="src5">cx -= 0.01f;<span class="kom">// Doleva</span></p>

<p>Teï o¹etøíme stisk kláves 1-4. Aby se kód provedl, nesmí být pøi stisku jednièky key roven jedné (nejde morfovat z prvního objektu na první) a také nesmíme právì morfovat (nevypadalo by to dobøe). V takovém pøípadì nastavíme pro pøí¹tí prùchod tímto místem key na jedna a morph na TRUE. Cílovým objektem bude objekt jedna. Klávesy 2, 3, 4 jsou analogické.</p>

<p class="src4">if (keys['1'] &amp;&amp; (key!=1) &amp;&amp; !morph)<span class="kom">// Klávesa 1?</span></p>
<p class="src4">{</p>
<p class="src5">key = 1;<span class="kom">// Proti dvojnásobnému stisku</span></p>
<p class="src5">morph = TRUE;<span class="kom">// Zaène morfovací proces</span></p>
<p class="src5">dest = &amp;morph1;<span class="kom">// Nastaví cílový objekt</span></p>
<p class="src4">}</p>
<p class="src4">if (keys['2'] &amp;&amp; (key!=2) &amp;&amp; !morph)<span class="kom">// Klávesa 2?</span></p>
<p class="src4">{</p>
<p class="src5">key = 2;<span class="kom">// Proti dvojnásobnému stisku</span></p>
<p class="src5">morph = TRUE;<span class="kom">// Zaène morfovací proces</span></p>
<p class="src5">dest = &amp;morph2;<span class="kom">// Nastaví cílový objekt</span></p>
<p class="src4">}</p>
<p class="src4">if (keys['3'] &amp;&amp; (key!=3) &amp;&amp; !morph)<span class="kom">// Klávesa 3?</span></p>
<p class="src4">{</p>
<p class="src5">key = 3;<span class="kom">// Proti dvojnásobnému stisku</span></p>
<p class="src5">morph = TRUE;<span class="kom">// Zaène morfovací proces</span></p>
<p class="src5">dest = &amp;morph3;<span class="kom">// Nastaví cílový objekt</span></p>
<p class="src4">}</p>
<p class="src4">if (keys['4'] &amp;&amp; (key!=4) &amp;&amp; !morph)<span class="kom">// Klávesa 4?</span></p>
<p class="src4">{</p>
<p class="src5">key = 4;<span class="kom">// Proti dvojnásobnému stisku</span></p>
<p class="src5">morph = TRUE;<span class="kom">// Zaène morfovací proces</span></p>
<p class="src5">dest = &amp;morph4;<span class="kom">// Nastaví cílový objekt</span></p>
<p class="src4">}</p>

<p>Doufám, ¾e jste si tento tutoriál u¾ili. Aèkoli výstup není a¾ tak fantastický jako v nìkterých jiných, nauèili jste se spoustu vìcí. Hraním si s kódem lze docílit skvìlých efektù - tøeba po scénì náhodnì rozházené body mìnící se ve slova. Zkuste pou¾ít polygony nebo linky namísto bodù, výsledek bude je¹tì lep¹í.</p>

<p>Pøed tím, ne¾ vznikla tato lekce bylo vytvoøeno demo &quot;Morph&quot;, které demonstruje mnohem pokroèilej¹í verzi probíraného efektu. Lze ho najít na adrese <?OdkazBlank('http://homepage.ntlworld.com/fj.williams/PgSoftware.html');?>.</p>

<p class="autor">napsal: Piotr Cieslak<br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson25.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson25_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson25.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson25.zip">Delphi</a> kód této lekce. ( <a href="mailto:Alexandre.Hirzel@nat.unibe.ch">Alexandre Hirzel</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson25.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson25.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson25.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson25.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:jay@remotepoint.com">Jay Groven</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson25.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Schubert_P@Yahoo.de">Patrick Schubert</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson25.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:scarab@egyptian.net">DarkAlloy</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson25.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson25.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(25);?>
<?FceNeHeOkolniLekce(25);?>

<?
include 'p_end.php';
?>
