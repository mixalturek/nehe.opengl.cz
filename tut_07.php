<?
$g_title = 'CZ NeHe OpenGL - Lekce 7 - Texturové filtry, osvìtlení, ovládání pomocí klávesnice';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(7);?>

<h1>Lekce 7 - Texturové filtry, osvìtlení, ovládání pomocí klávesnice</h1>

<p class="nadpis_clanku">V tomto dílu se pokusím vysvìtlit pou¾ití tøí odli¹ných texturových filtrù. Dále pak pohybu objektù pomocí klávesnice a nakonec aplikaci jednoduchých svìtel v OpenGL. Nebude se jako obvykle navazovat na kód z pøedchozího dílu, ale zaène se pìknì od zaèátku.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standartdní vstup/výstup</span></p>
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

<p>Pøidáme tøi booleovské promìnné. Promìnná light sleduje zda je svìtlo zapnuté. Promìnné lp a fp nám indikují stisk klávesy 'L' nebo 'F'. Proè je potøebujeme se dozvíme dále. Teï staèí vìdìt, ¾e zabraòují opakování obslu¾ného kódu pøi del¹ím dr¾ení.</p>

<p class="src0">bool light;<span class="kom">// Svìtlo ON/OFF</span></p>
<p class="src0">bool lp;<span class="kom">// Stisknuto L?</span></p>
<p class="src0">bool fp;<span class="kom">// Stisknuto F?</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot;<span class="kom">// X Rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y Rotace</span></p>
<p class="src0">GLfloat xspeed;<span class="kom">// Rychlost x rotace</span></p>
<p class="src0">GLfloat yspeed;<span class="kom">// Rychlost y rotace</span></p>
<p class="src0">GLfloat z=-5.0f;<span class="kom">// Hloubka v obrazovce</span></p>

<p>Následují pole pro specifikaci svìtla. Pou¾ijeme dva odli¹né typy. První bude okolní (ambient). Okolní svìtlo nevychází z jednoho bodu, ale jsou jím nasvíceny v¹echny objekty ve scénì. Druhým typem bude pøímé (diffuse). Pøímé svìtlo vychází z nìjakého zdroje a odrá¾í se o povrch. Povrchy objektu, na které svìtlo dopadá pøímo, budou velmi jasné a oblasti málo osvìtlené budou temné. To vytváøí pìkné stínové efekty po stranách krabice. Svìtlo se vytváøí stejným zpùsobem jako barvy. Je-li první èíslo 1.0f a dal¹í dvì 0.0f, dostáváme jasnou èervenou. Poslední hodnotou je alfa kanál. Ten tentokrát necháme 1.0f. Èervená, zelená a modrá nastavené na stejnou hodnotu v¾dy vytvoøí stín z èerné (0.0f) do bílé (1.0f). Bez okolního svìtla by místa bez pøímého svìtla byla pøíli¹ tmavá.</p>

<p class="src0">GLfloat LightAmbient[]= { 0.5f, 0.5f, 0.5f, 1.0f };<span class="kom">// Okolní svìtlo</span></p>

<p>V dal¹ím øádku jsou hodnoty pro pøímé svìtlo. Proto¾e, jsou v¹echny hodnoty 1.0f, bude to nejjasnìj¹í svìtlo jaké mù¾eme získat. Pìknì osvítí krabici.</p>

<p class="src0">GLfloat LightDiffuse[]= { 1.0f, 1.0f, 1.0f, 1.0f };<span class="kom">// Pøímé svìtlo</span></p>

<p>Nakonec nastavíme pozici svìtla. Proto¾e chceme aby svìtlo svítilo na bednu zpøedu, nesmíme pohnout svìtlem na ose x a y. Tøetí parametr nám zaruèí, ¾e bedna bude osvìtlena zepøedu. Svìtlo bude záøit smìrem k divákovi. Zdroj svìtla neuvidíme, proto¾e je pøed monitorem, ale uvidíme jeho odraz od bedny. Poslední èíslo definujeme na 1.0f. Urèuje koordináty pozice svìtelného zdroje. Více v dal¹í lekci.</p>

<p class="src0">GLfloat LightPosition[]= { 0.0f, 0.0f, 2.0f, 1.0f };<span class="kom">// Pozice svìtla</span></p>

<p>Promìnná filter bude pou¾ita pøi zobrazení textury. První textura je vytváøena pou¾itím GL_NEAREST. Druhá textura bude GL_LINEAR - filtrování pro úplnì hladký obrázek. Tøetí textura pou¾ívá mipmapingu, který tvoøí hodnì dobrý povrch. Promìnná filter tedy bude nabývat hodnot 0, 1 a 2. GLuint texture[3] ukazuje na tøi textury.</p>

<p class="src0">GLuint filter;<span class="kom">// Specifikuje pou¾ívaný texturový filtr</span></p>
<p class="src0">GLuint texture[3];<span class="kom">// Ukládá tøi textury</span></p>

<p>Nahrajeme bitmapu a vytvoøíme z ní tøi rùzné textury. Tato lekce pou¾ívá glaux knihovny k nahrávání bitmap. Vím ¾e Delphi a VC++ mají tuto knihovnu. Co ostatní jazyky, nevím. K tomu u¾ moc øíkat nebudu, øádky jsou okomentované a kompletní vysvìtlení je v 6 lekci. Nahraje a vytvoøí textury z bitmap.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Loading bitmapy a konverze na texturu</span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>
<p class="src1">AUX_RGBImageRec *TextureImage[1];<span class="kom">// Ukládá bitmapu</span></p>
<p class="src1">memset(TextureImage,0,sizeof(void *)*1);<span class="kom">// Vynuluje pamì»</span></p>

<p>Nyní nahrajeme bitmapu. Kdy¾ v¹e probìhne, data obrázku budou ulo¾ena v TextureImage[0], status se nastaví na true a zaèneme sestavovat texturu.</p>

<p class="src1">if (TextureImage[0]=LoadBMP(&quot;Data/Crate.bmp&quot;))<span class="kom">// Nahraje bitmapu a kontroluje vzniklé chyby</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;<span class="kom">// V¹e je bez problémù</span></p>

<p>Data bitmapy jsou nahrána do TextureImage[0]. Pou¾ijeme je k vytvoøení tøí textur. Následující øádek oznámí, ¾e chceme sestavit 3 textury a chceme je mít v ulo¾eny v texture[0], texture[1] a texture[2].</p>

<p class="src2">glGenTextures(3, &amp;texture[0]);<span class="kom">// Generuje tøi textury</span></p>

<p>V ¹esté lekci jsme pou¾ili lineární filtrování, které vy¾aduje hodnì výkonu, ale vypadá velice pìknì. Pro první texturu pou¾ijeme GL_NEAREST. Spotøebuje málo výkonu, ale výsledek je relativnì ¹patný. Kdy¾ ve høe vidíte ètvereèkovanou texturu, pou¾ívá toto filtrování, nicménì dobøe funguje i na slab¹ích poèítaèích. V¹imnìte si ¾e jsme pou¾ili GL_NEAREST pro MIN i MAG. Mù¾eme smíchat GL_NEAREST s GL_LINEAR a textury budou vypadat slu¹nì, ale zároveò nevy¾adují vysoký výkon. MIN_FILTER se u¾ívá pøi zmen¹ování, MAG_FILTER pøi zvìt¹ování.</p>

<p class="src2"><span class="kom">// Vytvoøí nelineárnì filtrovanou texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[0]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_NEAREST);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_NEAREST);</p>
<p class="src"></p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);</p>

<p>Dal¹í texturu vytvoøíme stejnì jako v lekci 6. Lineárnì filtrovaná. Jediný rozdíl spoèívá v pou¾ití texture[1] místo texture[0], proto¾e se jedná o druhou texturu.</p>

<p class="src2"><span class="kom">// Vytvoøí lineárnì filtrovanou texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[1]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);</p>

<p>Mipmaping je¹tì neznáte. Pou¾ívá se pøi malém obrázku, kdy mnoho detailù mizí z obrazovky. Takto vytvoøený povrch vypadá z blízka dost ¹patnì. Kdy¾ chcete sestavit mipmapovanou texturu, sestaví se více textur odli¹né velikosti a vysoké kvality. Kdy¾ kreslíte takovou texturu na obrazovku vybere se nejlépe vypadající textura. Nakreslí na obrazovku místo toho, aby zmìnilo rozli¹ení pùvodního obrázku, které je pøíèinou ztráty detailù. V ¹esté lekci jsem se zmínil o stanovených limitech ¹íøky a vý¹ky - 64, 128, 256 atd. Pro mipmapovanou texturu mù¾eme pou¾ít jakoukoli ¹íøku a vý¹ku bitmapy. Automaticky se zmìní velikost. Proto¾e toto je textura èíslo 3, pou¾ijeme texture[2]. Nyní máme v texture[0] texturu bez filtru, texture[1] pou¾ívá lineární filtrování a texture[2] pou¾ívá mipmaping.</p>

<p class="src2"><span class="kom">// Vytvoøí mipmapovanou texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[2]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR_MIPMAP_NEAREST);</p>
<p class="src"></p>
<p class="src2">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);</p>
<p class="src1">}</p>

<p>Mù¾eme uvolnit v¹echnu pamì» zaplnìnou daty bitmapy. Otestujeme zda se data nachází v TextureImage[0]. Kdy¾ tam budou, tak je sma¾eme. Nakonec uvolníme strukturu obrázku.</p>

<p class="src1">if (TextureImage[0])<span class="kom">// Pokud obrázek existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (TextureImage[0]-&gt;data)<span class="kom">// Pokud existují data obrázku</span></p>
<p class="src2">{</p>
<p class="src3">free(TextureImage[0]-&gt;data);<span class="kom">// Uvolní pamì» obrázku</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">free(TextureImage[0]);<span class="kom">// Uvolní strukturu obrázku</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return Status;<span class="kom">// Oznámí pøípadné chyby</span></p>
<p class="src0">}</p>

<p>Nejdùle¾itìj¹í èást inicializace spoèívá v pou¾ití svìtel.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echno nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje texturu</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapování textur</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkové testování</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>

<p>Nastavíme svìtla - konkrétnì light1. Na zaèátku této lekce jsme definovali okolní svìtlo do LightAmbient. Pou¾ijeme hodnoty nastavené v poli.</p>

<p class="src1">glLightfv(GL_LIGHT1, GL_AMBIENT, LightAmbient);<span class="kom">// Nastavení okolního svìtla</span></p>

<p>Hodnoty pøímého svìtla jsou v LightDiffuse.</p>

<p class="src1">glLightfv(GL_LIGHT1, GL_DIFFUSE, LightDiffuse);<span class="kom">// Nastavení pøímého svìtla</span></p>

<p>Nyní nastavíme pozici svìtla. Ta je ulo¾ena v LightPosition.</p>

<p class="src1">glLightfv(GL_LIGHT1, GL_POSITION,LightPosition);<span class="kom">// Nastavení pozice svìtla</span></p>

<p>Nakonec zapneme svìtlo jedna. Svìtlo je nastavené, umístìné a zapnuté, jakmile zavoláme glEnable(GL_LIGHTING) rozsvítí se.</p>

<p class="src1">glEnable(GL_LIGHT1);<span class="kom">// Zapne svìtlo</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace probìhla v poøádku</span></p>
<p class="src0">}</p>

<p>Vykreslíme krychli s texturami. Kdy¾ nepochopíte co nìkteré øádky dìlají, podívejte se do lekce 6.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,z);</p>
<p class="src"></p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);</p>

<p>Dal¹í øádek je podobný øádku v lekci 6, ale namísto texture[0] tu máme texture[filter]. Kdy¾ stiskneme klávesu F, hodnota ve filter se zvý¹í. Bude-li vìt¹í ne¾ 2, nastavíme zase 0. Pøi startu programu bude filter nastaven na 0. Promìnnou filter tedy urèujeme, kterou ze tøí textur máme pou¾ít.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter]);<span class="kom">// Zvolí texturu</span></p>

<p>Pøi pou¾ití svìtel musíme definovat normálu povrchu. Je to èára vycházející ze støedu polygonu v 90 stupòovém úhlu. Øekne jakým smìrem je èelo polygonu. Kdy¾ ji neurèíte, stane se hodnì divných vìcí. Povrchy které by mìly svítit se nerozsvítí, ¹patná strana polygonu svítit bude, atd. Normála po¾aduje bod vycházející z polygonu. Pohled na pøední povrch ukazuje ¾e normála je kladná na ose z. To znamená ¾e normála ukazuje k divákovi. Na zadní stranì normála jde od diváka, do obrazovky. Kdy¾ bude kostka otoèená o 180 stupòù v na ose x nebo y, pøední povrch bude ukazovat do obrazovky a zadní uvidí divák. Bez ohledu na to který povrch je vidìt divákem, normála tohoto povrchu jde smìrem k nìmu. Kdy¾ se tak stane, povrch bude osvìtlen. U dal¹ích bodù normály smìrem k svìtlu bude povrch také svìtlý. Kdy¾ se posunete do støedu kostky, bude tmavý. Normála je bod ven, nikoli dovnitø, proto není svìtlo uvnitø a tak to má být.</p>

<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// Pøední stìna</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f, 1.0f);<span class="kom">// Normála</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Zadní stìna</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f,-1.0f);<span class="kom">// Normála</span></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Horní stìna</span></p>
<p class="src2">glNormal3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Normála</span></p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Spodní stìna</span></p>
<p class="src2">glNormal3f( 0.0f,-1.0f, 0.0f);<span class="kom">// Normála</span></p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Pravá stìna</span></p>
<p class="src2">glNormal3f( 1.0f, 0.0f, 0.0f);<span class="kom">// Normála</span></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Levá stìna</span></p>
<p class="src2">glNormal3f(-1.0f, 0.0f, 0.0f);<span class="kom">// Normála</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">xrot+=xspeed;</p>
<p class="src1">yrot+=yspeed;</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Posuneme se dolù k WinMain(). Pøidáme kód k zapnutí/vypnutí svìtla, otáèení, výbìr filtru a posun kostky do/z obrazovky. Tìsnì u konce WinMain() uvidíte pøíkaz SwapBuffers(hDC). Ihned za tento øádek pøidáme kód.</p>

<p>Následující kód zji¹»uje, zda je stisknuta klávesa L. Je-li stisknuta ale lp není false, klávesa je¹tì nebyla uvolnìna.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">SwapBuffers(hDC);<span class="kom">// Prohození bufferù</span></p>
<p class="src"></p>
<p class="src4">if (keys['L'] &amp;&amp; !lp)<span class="kom">// Klávesa L - svìtlo</span></p>
<p class="src4">{</p>

<p>Kdy¾ bude lp false, L nebylo stisknuto, nebo bylo uvolnìno. Tento trik je pou¾it pro pøípad, kdy je klávesa dr¾ena déle a my chceme, aby se kód vykonal pouze jednou. Pøi prvním prùchodu se lp nastaví na true a promìnná light se invertuje. Pøi dal¹ím prùchodu je u¾ lp true a kód se neprovede a¾ do uvolnìní klávesy, které nastaví lp zase na false. Kdyby zde toto nebylo, svìtlo by pøi stisku akorát blikalo.</p>

<p class="src5">lp=TRUE;</p>
<p class="src5">light=!light;</p>

<p>Nyní se podíváme na promìnnou light. Kdy¾ bude false, vypneme svìtlo, kdy¾ ne zapneme ho.</p>

<p class="src5">if (!light)</p>
<p class="src5">{</p>
<p class="src6">glDisable(GL_LIGHTING);<span class="kom">// Vypne svìtlo</span></p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">glEnable(GL_LIGHTING);<span class="kom">// Zapne svìtlo</span></p>
<p class="src5">}</p>
<p class="src4">}</p>

<p>Následuje nastavení promìnné lp na false pøi uvolnìní klávesy L.</p>

<p class="src4">if (!keys['L'])</p>
<p class="src4">{</p>
<p class="src5">lp=FALSE;</p>
<p class="src4">}</p>

<p>Nyní o¹etøíme stisk F. Kdy¾ se stiskne, dojde ke zvý¹ení filter. Pokud bude vìt¹í ne¾ 2, nastavíme ho zpìt na 0. K o¹etøení del¹ího stisku klávesy pou¾ijeme stejný zpùsob jako u svìtla.</p>

<p class="src4">if (keys['F'] &amp;&amp; !fp)<span class="kom">// Klávesa F - zmìna texturového filtru</span></p>
<p class="src4">{</p>
<p class="src5">fp=TRUE;</p>
<p class="src5">filter+=1;</p>
<p class="src"></p>
<p class="src5">if (filter&gt;2)</p>
<p class="src5">{</p>
<p class="src6">filter=0;</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys['F'])<span class="kom">// Uvolnìní F</span></p>
<p class="src4">{</p>
<p class="src5">fp=FALSE;</p>
<p class="src4">}</p>

<p>Otestují stisk klávesy Page Up. Kdy¾ bude stisknuto, sní¾íme promìnnou z. To zpùsobí vzdalování kostky v pøíkazu glTranslatef(0.0f,0.0f,z).</p>

<p class="src4">if (keys[VK_PRIOR])<span class="kom">// Klávesa Page Up - zvý¹í zanoøení do obrazovky</span></p>
<p class="src4">{</p>
<p class="src5">z-=0.02f;</p>
<p class="src4">}</p>

<p>Otestují stisk klávesy Page Down. Kdy¾ bude stisknuta, zvý¹íme promìnnou z. To zpùsobí pøibli¾ování kostky v pøíkazu glTranslatef(0.0f,0.0f,z).</p>

<p class="src4">if (keys[VK_NEXT])<span class="kom">// Klávesa Page Down - sní¾í zanoøení do obrazovky</span></p>
<p class="src4">{</p>
<p class="src5">z+=0.02f;</p>
<p class="src4">}</p>

<p>Dále zkontrolujeme kurzorové klávesy. Bude-li stisknuto vlevo/vpravo, promìnná xspeed se bude zvy¹ovat/sni¾ovat. Bude-li stisknuto nahoru/dolù, promìnná yspeed se bude zvy¹ovat/sni¾ovat. Jestli si vzpomínáte, vý¹e jsem psal, ¾e vysoké hodnoty zpùsobí rychlou rotaci. Dlouhý stisk nìjaké klávesy zpùsobí právì rychlou rotaci kostky.</p>

<p class="src4">if (keys[VK_UP])<span class="kom">// ©ipka nahoru</span></p>
<p class="src4">{</p>
<p class="src5">xspeed-=0.01f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN])<span class="kom">// ©ipka dolu</span></p>
<p class="src4">{</p>
<p class="src5">xspeed+=0.01f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_RIGHT])<span class="kom">// ©ipka vpravo</span></p>
<p class="src4">{</p>
<p class="src5">yspeed+=0.01f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_LEFT])<span class="kom">// ©ipka vlevo</span></p>
<p class="src4">{</p>
<p class="src5">yspeed-=0.01f;</p>
<p class="src4">}</p>

<p>Nyní byste mìli vìdìt jak vytvoøit vysoce kvalitní, realisticky vypadající, texturovaný objekt. Také jsme se nìco dozvìdìli o tøech rùzných filtrech. Stiskem urèitých kláves mù¾ete pohybovat objektem na obrazovce, a nakonec víme jak aplikovat jednoduché svìtlo. Zkuste experimentovat s jeho pozicí a barvou.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Jiøí Rajský - RAJSOFT junior <?VypisEmail('predator.jr@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson07.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson07_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson07.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson07.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson07.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson07.zip">Delphi</a> kód této lekce. ( <a href="mailto:brad@choate.net">Brad Choate</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson07.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson07.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson07.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson07.zip">GLUT</a> kód této lekce. ( <a href="mailto:lordrustad@hotmail.com">Andy Restad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson07.zip">Irix</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson07.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson07.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson07.jar">JoGL</a> kód této lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson07.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson07.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson07.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson07.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson07.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson07.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson07.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson07.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson07.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson07.zip">Solaris</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson07.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson07.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson07.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(7);?>
<?FceNeHeOkolniLekce(7);?>

<?
include 'p_end.php';
?>
