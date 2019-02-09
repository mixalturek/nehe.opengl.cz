<?
$g_title = 'CZ NeHe OpenGL - Lekce 11 - Efekt vlnící se vlajky';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(11);?>

<h1>Lekce 11 - Efekt vlnící se vlajky</h1>

<p class="nadpis_clanku">Nauèíme se jak pomocí sinusové funkce animovat obrázky. Pokud znáte standartní ¹etøiè Windows &quot;Létající 3D objekty&quot; (i on by mìl být programovaný v OpenGL), tak budeme dìlat nìco podobného.</p>

<p>Budeme vycházet z ¹esté lekce. Neopisuji celý zdrojový kód, tak¾e mo¾ná bude lep¹í, kdy¾ budete mít nìkde po ruce i zdrojový kód ze zmiòované lekce. První vìc, kterou musíte udìlat je vlo¾it hlavièkový soubor matematické knihovny. Nebudeme pracovat s moc slo¾itou matematikou, nebojte se, pou¾ijete pouze siny a kosiny.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavièkový soubor pro matematickou knihovnu</span></p>
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

<p>Deklarujte trojrozmìrné pole bodù k ulo¾ení souøadnic v jednotlivých osách. Wiggle_count se pou¾ije k nastavení a následnému zji¹»ování, jak rychle se bude textura vlnit. Promìnná hold zajistí plynulé vlnìní textury.</p>

<p class="src0">float points[45][45][3];<span class="kom">// Pole pro body v møí¾ce vlny</span></p>
<p class="src0">int wiggle_count = 0;<span class="kom">// Rychlost vlnìní</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot;<span class="kom">// Rotace na ose x</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Rotace na ose y</span></p>
<p class="src0">GLfloat zrot;<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src0">GLfloat hold;<span class="kom">// Pomocná, k zaji¹tìní plynulosti pohybu</span></p>
<p class="src0">GLuint texture[1];<span class="kom">// Ukládá texturu</span></p>

<p>Pøesuòte se dolù k funkci LoadGLTexture(). Budete pou¾ívat novou texturu s názvem Tim.bmp, tak¾e najdìte funkci LoadBMP("Data/NeHe.bmp") a pøepi¹te ji tak, aby nahrávala nový obrázek.</p>

<p class="src1">if(TextureImage[0]=LoadBMP("Data/Tim.bmp"))<span class="kom">// Loading bitmapy</span></p>

<p>Teï pøidejte následující kód na konec funkce InitGL(). Výsledek uvidíte na první pohled. Pøední strana textury bude normálnì vybarvená, ale jak se po chvíli obrázek natoèí, zjistíte, ¾e ze zadní strany zbyl drátìný model. GL_FILL urèuje klasické kreslení polygony, GL_LINES vykresluje pouze okrajové linky, pøi GL_POINTS by ¹lo vidìt pouze vrcholové body. Která strana polygonu je pøední a která zadní nelze urèit jednoznaènì, staèí rotace a u¾ je to naopak. Proto vznikla konvence, ¾e mnohoúhelníky, u kterých byly pøi vykreslování zadány vrcholy proti smìru hodinových ruèièek jsou pøivrácené.</p>

<p class="src0"><span class="kom">// Konec funkce InitGL()</span></p>
<p class="src1">glPolygonMode(GL_BACK, GL_FILL);<span class="kom">// Pøední strana vyplnìná polygony</span></p>
<p class="src1">glPolygonMode(GL_FRONT, GL_LINE);<span class="kom">// Zadní strana vyplnìná møí¾kou</span></p>

<p>Následující dva cykly inicializují na¹i sí». Abychom dostali správný index musíme dìlit øídící promìnou smyèky pìti (tzn. 45/9=5). Odèítám 4,4 od ka¾dé souøadnice, aby se vlna vycentrovala na poèátku souøadnic. Stejného efektu mù¾e být dosa¾eno s pomocí posunutí, ale já mám rad¹i tuto metodu. Hodnota points[x][y][2] je tvoøená hodnotou sinu. Funkce sin() potøebuje radiány, tudí¾ vezmeme hodnotu ve stupních, co¾ je na¹e x/5 násobené ètyøiceti a pomocí vzorce  (radiány=2*PÍ*stupnì/360) ji pøepoèítáme.</p>

<p class="src1">for (int x=0; x&lt;45; x++)<span class="kom">// Inicializace vlny</span></p>
<p class="src1">{</p>
<p class="src2">for (int y=0; y&lt;45; y++)</p>
<p class="src2">{</p>
<p class="src3">points[x][y][0]=float((x/5.0f)-4.5f);</p>
<p class="src3">points[x][y][1]=float((y/5.0f)-4.5f);</p>
<p class="src3">points[x][y][2]=float(sin((((x/5.0f)*40.0f)/360.0f)*3.141592654*2.0f));</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Na zaèátku vykreslovací funkce deklarujeme promìnné. Jsou pou¾ity jako øídící v cyklù. Uvidíte je v kódu ní¾, ale vìt¹ina z nich neslou¾í k nìèemu jinému ne¾, ¾e kontrolují cykly a ukládají doèasné hodnoty</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">int x, y;</p>
<p class="src1">float float_x, float_y, float_xb, float_yb;</p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-12.0f);<span class="kom">// Posunutí do obrazovky</span></p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src1">glRotatef(zrot,0.0f,0.0f,1.0f);<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Výbìr textury</span></p>

<p>V¹imnìte si, ¾e ètverce jsou kresleny <b>po</b> smìru hodinových ruèièek. Z toho plyne, ¾e èelní plocha, kterou vidíte bude vyplnìná a zezadu bude drátìný model. Pokud bychom ètverce vykreslovali <b>proti</b> smìru hodinových ruèièek drátìný model by byl na pøední stranì.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení ètvercù</span></p>
<p class="src"></p>
<p class="src2">for( x = 0; x &lt; 44; x++ )<span class="kom">// Cykly procházejí pole</span></p>
<p class="src2">{</p>
<p class="src3">for( y = 0; y &lt; 44; y++ )</p>
<p class="src3">{</p>

<p>Ka¾dý z polygonù (ètverce v síti) má 1/44x1/44 textury. Cyklus urèuje levý dolní bod (první 2 øádky). Poté spoèítáme pravý horní (dal¹í 2 øádky). Tak¾e máme dva body na úhlopøíèce ètverce a kombinací hodnot jejich souøadnic získáme zbylé dva body na textuøe.</p>

<p class="src4"><span class="kom">// Vypoèítání texturových koordinátù</span></p>
<p class="src4">float_x = float(x)/44.0f;</p>
<p class="src4">float_y = float(y)/44.0f;</p>
<p class="src4">float_xb = float(x+1)/44.0f;</p>
<p class="src4">float_yb = float(y+1)/44.0f;</p>
<p class="src"></p>
<p class="src4"><span class="kom">// Zadání jednotlivých bodù</span></p>
<p class="src4">glTexCoord2f(float_x, float_y);</p>
<p class="src4">glVertex3f(points[x][y][0], points[x][y][1], points[x][y][2]);</p>
<p class="src"></p>
<p class="src4">glTexCoord2f(float_x, float_yb);</p>
<p class="src4">glVertex3f(points[x][y+1][0], points[x][y+1][1], points[x][y+1][2]);</p>
<p class="src"></p>
<p class="src4">glTexCoord2f(float_xb, float_yb);</p>
<p class="src4">glVertex3f(points[x+1][y+1][0], points[x+1][y+1][1], points[x+1][y+1][2]);</p>
<p class="src"></p>
<p class="src4">glTexCoord2f(float_xb, float_y);</p>
<p class="src4">glVertex3f(points[x+1][y][0], points[x+1][y][1], points[x+1][y][2]);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení ètvercù</span></p>

<p>Pøi sudém vykreslení v poøadí pøesuneme souøadnice v poli do sousedních souøadnic a tím pøesuneme i vlnu o kousek vedle. Celý první sloupec (vnìj¹í cyklus) postupnì ukládáme do pomocné promìnné. Potom o kousek pøesuneme vlnu jednoduchým pøiøazením ka¾dého prvku do  sousedního a nakonec pøiøadíme ulo¾enou hodnotu okraje na opaèný konec obrázku. Tím vzniká dojem, ¾e kdy¾ mizí jedna vlna, okam¾itì zaèíná vznikat nová, ale programovì je to konec té staré :-] Zjednodu¹enì øeèeno máme jen jednu vlnu, která se po opu¹tìní obrázku pøesouvá na zaèátek. Nakonec vynulujeme wiggle_count, abychom udr¾eli animaci v chodu.</p>

<p class="src1">if (wiggle_count == 2)<span class="kom">// Pro sní¾ení rychlosti pohybu</span></p>
<p class="src1">{</p>
<p class="src2">for (y = 0; y &lt; 45; y++)<span class="kom">// Prochází hodnoty na y</span></p>
<p class="src2">{</p>
<p class="src3">hold=points[0][y][2];<span class="kom">// Ulo¾í kraj vlny</span></p>
<p class="src"></p>
<p class="src3">for (x = 0; x &lt; 44; x++)<span class="kom">// Prochází hodnoty na x</span></p>
<p class="src3">{</p>
<p class="src4">points[x][y][2] = points[x+1][y][2];<span class="kom">// Pøiøazení do sousedního prvku</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">points[44][y][2]=hold;<span class="kom">// Ulo¾ený kraj bude na druhé stranì</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">wiggle_count = 0;<span class="kom">// Nulování poèítadla vykreslování</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">wiggle_count++;<span class="kom">// Inkrementace poèítadla</span></p>

<p>Aktualizujeme rotaci a ukonèíme funkci.</p>

<p class="src1">xrot+=0.3f;</p>
<p class="src1">yrot+=0.2f;</p>
<p class="src1">zrot+=0.4f;</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Zkompilujte a spus»te program. Z pøední strany byste mìli vidìt hezkou vlnící se bitmapu a po následném natoèení z ní zùstane pouze drátìný model.</p>

<p class="autor">napsal: Bosco <?VypisEmail('bosco4@home.com');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kodye_download">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson11.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson11_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson11.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson11.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson11.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson11.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson11.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson11.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson11.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson11.zip">Irix</a> kód této lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson11.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson11.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson11.jar">JoGL</a> kód této lekce. ( <a href="mailto:pepijn.vaneeckhoudt@luciad.com">Pepijn Van Eeckhoudt</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson11.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson11.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson11.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson11.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson11.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson11.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson11.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson11.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson11.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson11.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson11.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(11);?>
<?FceNeHeOkolniLekce(11);?>

<?
include 'p_end.php';
?>
