<?
$g_title = 'CZ NeHe OpenGL - Lekce 14 - Outline fonty';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(14);?>

<h1>Lekce 14 - Outline fonty</h1>

<p class="nadpis_clanku">Bitmapové fonty nestaèí? Potøebujete kontrolovat pozici textu i na ose z? Chtìli byste fonty s hloubkou? Pokud zní va¹e odpovìï ano, pak jsou 3D fonty nejlep¹í øe¹ení. Mù¾ete s nimi pohybovat na ose z a tím mìnit jejich velikost, otáèet je, prostì dìlat v¹e, co nemù¾ete s obyèejnými. Jsou nejlep¹í volbou ke hrám a demùm.</p>

<p>Tato lekce je volnými pokraèováním té minulé (13). Tehdy jsme se nauèili pou¾ívat bitmapové fonty. 3D písma se vytváøejí velmi podobnì. Nicménì... vypadají stokrát lépe. Mù¾ete je zvìt¹ovat, pohybovat s nimi ve 3D, mají hloubku. Pøi osvìtlení vypadají opravdu efektnì. Stejnì jako v minulé lekci je kód specifický pro Windows. Pokud by mìl nìkdo na platformì nezávislý kód, sem s ním a já napí¹u nový tutoriál. Roz¹íøíme typický kód první lekce.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src"></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavièkový soubor pro matematickou knihovnu</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;stdarg.h&gt;<span class="kom">// Hlavièkový soubor pro funkce s promìnným poètem parametrù</span></p>
<p class="src"></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavièkový soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavièkový soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Privátní GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trvalý Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na¹eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>

<p>Base si pamatujete z 13. lekce jako ukazatel na první z display listù ascii znakù, rot slou¾í k pohybu, rotaci a vybarvování textu.</p>

<p class="src0">GLuint base;<span class="kom">// Èíslo základního display listu znakù</span></p>
<p class="src0">GLfloat rot;<span class="kom">// Pro pohyb, rotaci a barvu textu</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>GLYPHMETRICSFLOAT gmf[256] ukládá informace o velikosti a orientaci ka¾dého z 256 display listù fontu. Dále v lekci vám uká¾u, jak zjistit ¹íøku jednotlivých znakù a tím velmi snadno a pøesnì vycentrovat text na obrazovce.</p>

<p class="src0">GLYPHMETRICSFLOAT gmf[256];<span class="kom">// Ukládá informace o fontu</span></p>

<p>Skoro celý kód následující funkce byl pou¾it ji¾ ve 13. lekci, tak¾e pokud mu moc nerozumíte, víte, kde hledat informace.</p>

<p class="src0">GLvoid BuildFont(GLvoid)<span class="kom">// Vytvoøení fontu</span></p>
<p class="src0">{</p>
<p class="src1">HFONT font;<span class="kom">// Promìnná fontu</span></p>
<p class="src1">base = glGenLists(256);<span class="kom">// 256 znakù</span></p>
<p class="src"></p>
<p class="src1">font = CreateFont(-24,<span class="kom">// Vý¹ka</span></p>
<p class="src2">0,<span class="kom">// ©íøka</span></p>
<p class="src2">0,<span class="kom">// Úhel escapement</span></p>
<p class="src2">0,<span class="kom">// Úhel orientace</span></p>
<p class="src2">FW_BOLD,<span class="kom">// Tuènost</span></p>
<p class="src2">FALSE,<span class="kom">// Kurzíva</span></p>
<p class="src2">FALSE,<span class="kom">// Podtr¾ení</span></p>
<p class="src2">FALSE,<span class="kom">// Pøe¹krtnutí</span></p>
<p class="src2">ANSI_CHARSET,<span class="kom">// Znaková sada</span></p>
<p class="src2">OUT_TT_PRECIS,<span class="kom">// Pøesnost výstupu (TrueType)</span></p>
<p class="src2">CLIP_DEFAULT_PRECIS,<span class="kom">// Pøesnost oøezání</span></p>
<p class="src2">ANTIALIASED_QUALITY,<span class="kom">// Výstupní kvalita</span></p>
<p class="src2">FF_DONTCARE|DEFAULT_PITCH,<span class="kom">// Rodina a pitch</span></p>
<p class="src2">&quot;Courier New&quot;);<span class="kom">// Jméno fontu</span></p>
<p class="src"></p>
<p class="src1">SelectObject(hDC, font);<span class="kom">// Výbìr fontu do DC</span></p>

<p>Pomocí funkce wglUseFontOutlines() vytvoøíme 3D font. V parametrech pøedáme DC, první znak, poèet display listù, které se budou vytváøet a ukazatel na pamì», kam se budou vytvoøené display listy ukládat.</p>

<p class="src1">wglUseFontOutlines(hDC,<span class="kom">// Vybere DC</span></p>
<p class="src2">0,<span class="kom">// Poèáteèní znak</span></p>
<p class="src2">255,<span class="kom">// Koncový znak</span></p>
<p class="src2">base,<span class="kom">// Adresa prvního znaku</span></p>

<p>Nastavíme úroveò odchylek, která urèuje jak hranatì bude vypadat. Potom urèíme ¹íøku nebo spí¹e hloubku na ose z. 0.0f by byl plochý 2D font. Èím vìt¹í èíslo pøiøadíme, tím bude hlub¹í. Parametr WGL_FONT_POLYGONS øíká, ¾e má OpenGL vytvoøit pevné (celistvé) znaky s pou¾itím polygonù. Pøi pou¾ití WGL_FONT_LINES se vytvoøí z linek (podobné drátìnému modelu). Je dùle¾ité poznamenat, ¾e by se v tomto pøípadì negenerovaly normálové vektory, tak¾e svìtlo nebude vypadat dobøe. Poslední parametr ukazuje na buffer pro ulo¾ení informací o display listech.</p>

<p class="src2">0.0f,<span class="kom">// Hranatost</span></p>
<p class="src2">0.2f,<span class="kom">// Hloubka v ose z</span></p>
<p class="src2">WGL_FONT_POLYGONS,<span class="kom">// Polygony ne drátìný model</span></p>
<p class="src2">gmf);<span class="kom">// Adresa bufferu pro ulo¾ení informací.</span></p>
<p class="src0">}</p>

<p>V následují funkci se ma¾e 256 display listù fontu poèínaje prvním, který je definován v base. Nejsem si jistý, jestli by to Windows udìlaly automaticky. Jeden øádek za jistotu stojí. Funkce se volá pøi skonèení programu.</p>

<p class="src0">GLvoid KillFont(GLvoid)<span class="kom">// Sma¾e font</span></p>
<p class="src0">{</p>
<p class="src1">glDeleteLists(base, 256);<span class="kom">// Sma¾e v¹ech 256 znakù</span></p>
<p class="src0">}</p>

<p>Tento kód zavoláte v¾dy, kdy¾ budete potøebovat vypsat nìjaký text. Øetìzec je ulo¾en ve "fmt".</p>

<p class="src0">GLvoid glPrint(const char *fmt, ...)<span class="kom">// Klon printf() pro OpenGL</span></p>
<p class="src0">{</p>

<p>Promìnnou "length" pou¾ijeme ke zji¹tìní délky textu. Pole "text" ukládá koneèný øetìzec pro vykreslení. Tøetí promìnná je ukazatel do parametrù funkce (pokud bychom zavolali funkci s nìjakou promìnnou, "ap" na ni bude ukazovat.</p>

<p class="src1">float length=0;<span class="kom">// Délka znaku</span></p>
<p class="src1">char text[256];<span class="kom">// Koneèný øetìzec</span></p>
<p class="src1">va_list ap;<span class="kom">// Ukazatel do argumentù funkce</span></p>
<p class="src"></p>
<p class="src1">if (fmt == NULL)<span class="kom">// Pokud nebyl pøedán øetìzec</span></p>
<p class="src2">return;<span class="kom">// Konec</span></p>

<p>Následující kód konvertuje ve¹keré symboly v øetìzci (%d, %f ap.) na znaky, které reprezentují èíselné hodnoty v promìnných. Poupravovaný text se ulo¾í do øetìzce text.</p>

<p class="src1">va_start(ap, fmt);<span class="kom">// Rozbor øetìzce pro promìnné</span></p>
<p class="src2">vsprintf(text, fmt, ap);<span class="kom">// Zamìní symboly za èísla</span></p>
<p class="src1">va_end(ap);<span class="kom">// Výsledek je nyní ulo¾en v text</span></p>

<p>Text by ¹el vycentrovat manuálnì, ale následující metoda je urèitì lep¹í. V ka¾dém prùchodu cyklem pøièteme k délce øetìzce ¹íøku aktuální znaku, kterou najdeme v gmf[text[loop]].gmfCellIncX. gmf ukládá informace o ka¾dém znaku (display listu), tedy napøíklad i vý¹ku znaku, ulo¾enou pod gmfCellIncY. Tuto techniku lze pou¾ít pøi vertikálním vykreslování.</p>

<p class="src1">for (unsigned int loop=0;loop&lt;(strlen(text));loop++)<span class="kom">// Zjistí poèet znakù textu</span></p>
<p class="src1">{</p>
<p class="src2">length+=gmf[text[loop]].gmfCellIncX;<span class="kom">// Inkrementace o ¹íøku znaku</span></p>
<p class="src1">}</p>

<p>K vycentrování textu posuneme poèátek doleva o polovinu délky øetìzce.</p>

<p class="src1">glTranslatef(-length/2,0.0f,0.0f);<span class="kom">// Zarovnání na støed</span></p>

<p>Nastavíme GL_LIST_BIT a tím zamezíme pùsobení jiných display listù, pou¾itých v programu na glListBase(). Pøede¹lým pøíkazem urèíme, kde má OpenGL hledat správné display listy jednotlivých znakù.</p>

<p class="src1">glPushAttrib(GL_LIST_BIT);<span class="kom">// Ulo¾í souèasný stav display listù</span></p>
<p class="src1">glListBase(base);<span class="kom">// Nastaví první display list na base</span></p>

<p>Zavoláme funkci glCallLists(), která najednou zobrazuje více display listù. strlen(text) vrátí poèet znakù v øetìzci a tím i poèet k zobrazení. Dále potøebujeme znát typ pøedávaného parametru (poslední). Ani teï nebudeme vkládat více ne¾ 256 znakù, tak¾e pou¾ijeme GL_UNSIGNED_BYTE (byte mù¾e nabývat hodnot 0-255, co¾ je pøesnì to, co potøebujeme). V posledním parametru pøedáme text. Ka¾dý display list ví, kde je pravá hrana toho pøedchozího, èím¾ zamezíme nakupení znakù na sebe, na jedno místo. Pøed zaèátkem kreslení následující znaku se pøesune o tuto hodnotu doprava (glTranslatef()). Nakonec nastavíme GL_LIST_BIT zpìt na hodnotu mající pøed voláním glListBase().</p>

<p class="src1">glCallLists(strlen(text), GL_UNSIGNED_BYTE, text);<span class="kom">// Vykreslí display listy</span></p>
<p class="src1">glPopAttrib();<span class="kom">// Obnoví pùvodní stav display listù</span></p>
<p class="src0">}</p>

<p>Provedeme pár drobných zmìn v inicializaèním kódu. Øádka BuildFont() ze 13. lekce zùstala na stejném místì, ale pøibyl nový kód pro pou¾ití svìtel. Light0 je pøeddefinován na vìt¹inì grafických karet. Také jsem pøidal glEnable(GL_COLOR_MATERIAL). Ke zmìnì barvy písma potøebujeme zapnout vybarvování materiálù, proto¾e i znaky jsou 3D objekty. Pokud vykreslujete vlastní objekty a nìjaký text, musíte pøed funkcí glPrint() zavolat glEnable(GL_COLOR_MATERIAL) a po vykreslení textu glDisable(GL_COLOR_MATERIAL), jinak by se zmìnila barva i vámi vykreslovaného objektu.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echna nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povolí jemné stínování</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí hloubkové testování</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne implicitní svìtlo</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne svìtla</span></p>
<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Zapne vybarvování materiálù</span></p>
<p class="src1">BuildFont();<span class="kom">// Vytvoøí font</span></p>
<p class="src1"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Pøesuneme se 10 jednotek do obrazovky. Outline fonty vypadají skvìle v perspektivním módu. Kdy¾ jsou umístìny hloubìji, zmen¹ují se. Pomocí funkce glScalef(x,y,z) mù¾eme také mìnit mìøítka os. Pokud bychom napøíklad chtìli vykreslit font dvakrát vy¹¹í, pou¾ijeme glScalef(1.0f,2.0f,1.0f).</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-10.0f);<span class="kom">// Pøesun do obrazovky</span></p>
<p class="src1">glRotatef(rot,1.0f,0.0f,0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(rot*1.5f,0.0f,1.0f,0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src1">glRotatef(rot*1.4f,0.0f,0.0f,1.0f);<span class="kom">// Rotace na ose z</span></p>

<p>Jako obyèejnì jsem pou¾il pro zmìnu barev "jednoduché" matematiky. (Pozn. pøekladatele: tahle vìta se mi povedla :)</p>

<p class="src1"><span class="kom">// Pulzování barev závislé na pozici a rotaci</span></p>
<p class="src1">glColor3f(1.0f*float(cos(rot/20.0f)),1.0f*float(sin(rot/25.0f)),1.0f-0.5f*float(cos(rot/17.0f)));</p>
<p class="src"></p>
<p class="src1">glPrint(&quot;NeHe - %3.2f&quot;,rot/50);<span class="kom">// Výpis textu</span></p>
<p class="src"></p>
<p class="src1">rot+=0.5f;<span class="kom">// Inkrementace èítaèe</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Poslední kód, který se provede pøed opu¹tìním programu je smazání fontu voláním KillFont().</p>

<p class="src0"><span class="kom">//Konec funkce KillGLWindow(GLvoid)</span></p>
<p class="src1">if(!UnregisterClass("OpenGL",hInstance))</p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,"Could Not Unregister Class.","SHUTDOWN ERROR",MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">hInstance=NULL;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">KillFont();<span class="kom">//Smazání fontu</span></p>
<p class="src0">}</p>

<p>Po doètení této lekce byste mìli být schopni pou¾ívat 3D fonty. Stejnì jako jsem psal ve 13. lekci, ani tentokrát jsem na internetu nena¹el podobný èlánek. Mo¾ná jsem opravdu první, kdo pí¹e o tomto tématu.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson14.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson14_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson14.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson14.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson14.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson14.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson14.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson14.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson14.jar">JoGL</a> kód této lekce. ( <a href="mailto:pepijn.vaneeckhoudt@luciad.com">Pepijn Van Eeckhoudt</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson14.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson14.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson14.zip">MASM</a> kód této lekce. ( <a href="mailto:greg@ozducati.com">Greg Helps</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson14.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson14-2.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:fredo@studenten.net">Edo</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson14.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(14);?>
<?FceNeHeOkolniLekce(14);?>

<?
include 'p_end.php';
?>
