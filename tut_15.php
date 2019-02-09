<?
$g_title = 'CZ NeHe OpenGL - Lekce 15 - Mapování textur na fonty';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(15);?>

<h1>Lekce 15 - Mapování textur na fonty</h1>

<p class="nadpis_clanku">Po vysvìtlení bitmapových a 3D fontù v pøedchozích dvou lekcích jsem se rozhodl napsat lekci o mapování textur na fonty. Jedná se o tzv. automatické generování koordinátù textur. Po doètení této lekce budete umìt namapovat texturu opravdu na cokoli - zcela snadno a jednodu¹e.</p>

<p>Stejnì jako v minulé a pøedminulé lekci je kód specifický pro Windows. Pokud by mìl nìkdo na platformì nezávislý kód sem s ním a já napí¹u nový tutoriál o fontech.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavièkový soubor pro matematickou knihovnu</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
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
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>
<p class="src"></p>
<p class="src0">GLuint base;<span class="kom">// Ukazatel na první z display listù pro font</span></p>
<p class="src0">GLuint texture[1];<span class="kom">// Ukládá texturu</span></p>
<p class="src0">GLuint rot;<span class="kom">// Pro pohyb, rotaci a barvu textu</span></p>

<p>Pøi psaní funkce nahrávající font jsem udìlal malou zmìnu. Pokud jste si spustili program, asi jste na první pohled nena¹li ten font - ale byl tam. V¹imli jste si poletující pirátské lebky se zkøí¾enými kostmi. Právì ona je jeden znak z písma Wingdings, které patøí mezi tzv. symbolové fonty.</p>

<p class="src0">GLvoid BuildFont(GLvoid)<span class="kom">// Vytvoøení fontu</span></p>
<p class="src0">{</p>
<p class="src1">GLYPHMETRICSFLOAT gmf[256];<span class="kom">// Ukládá informace o fontu</span></p>
<p class="src1">HFONT font;<span class="kom">// Promìnná fontu</span></p>
<p class="src"></p>
<p class="src1">base = glGenLists(256);<span class="kom">// 256 znakù</span></p>
<p class="src"></p>
<p class="src1">font = CreateFont(-12,<span class="kom">// Vý¹ka</span></p>
<p class="src2">0,<span class="kom">// ©íøka</span></p>
<p class="src2">0,<span class="kom">// Úhel escapement</span></p>
<p class="src2">0,<span class="kom">// Úhel orientace</span></p>
<p class="src2">FW_BOLD,<span class="kom">// Tuènost</span></p>
<p class="src2">FALSE,<span class="kom">// Kurzíva</span></p>
<p class="src2">FALSE,<span class="kom">// Podtr¾ení</span></p>
<p class="src2">FALSE,<span class="kom">// Pøe¹krtnutí</span></p>

<p>Místo ANSI_CHARSET podle stylu lekce 14, pou¾ijeme SYMBOL_CHARSET. Tím øekneme Windowsùm, ¾e vytváøený font není typickým písmem tvoøeným znaky, ale ¾e obsahuje malé obrázky (symboly). Pokud byste zapomnìli zmìnit tuto øádku, písma typu Wingdings, webdings a dal¹í, která zkou¹íte pou¾ít, nebudou vykreslovat symboly (lebka ...), ale normální znaky (A, B ...).</p>

<p class="src2">SYMBOL_CHARSET,<span class="kom">// Znaková sada</span></p>
<p class="src2">OUT_TT_PRECIS,<span class="kom">// Pøesnost výstupu (TrueType)</span></p>
<p class="src2">CLIP_DEFAULT_PRECIS,<span class="kom">// Pøesnost oøezání</span></p>
<p class="src2">ANTIALIASED_QUALITY,<span class="kom">// Výstupní kvalita</span></p>
<p class="src2">FF_DONTCARE|DEFAULT_PITCH,<span class="kom">// Rodina a pitch</span></p>
<p class="src2">&quot;Wingdings&quot;);<span class="kom">// Jméno fontu</span></p>
<p class="src"></p>
<p class="src1">SelectObject(hDC, font);<span class="kom">// Výbìr fontu do DC</span></p>
<p class="src"></p>
<p class="src1">wglUseFontOutlines(hDC,<span class="kom">// Vybere DC</span></p>
<p class="src2">0,<span class="kom">// Poèáteèní znak</span></p>
<p class="src2">255,<span class="kom">// Koncový znak</span></p>
<p class="src2">base,<span class="kom">// Adresa prvního znaku</span></p>

<p>Poèítám s vìt¹í hranatostí. To znamená, ¾e se OpenGL nebude dr¾et obrysù fontu tak tìsnì. Pokud zde pøedáte 0.0f, v¹imnete si problémù spojených s mapováním textur na zakøivené roviny. Povolíte-li jistou hranatost, vìt¹ina problémù zmizí. (Já (pøekladatel) jsem ¾ádné problémy s 0.0f nemìl, dokonce to vypadalo o dost lépe.)</p>

<p class="src2">0.1f,<span class="kom">// Hranatost</span></p>
<p class="src2">0.2f,<span class="kom">// Hloubka v ose z</span></p>
<p class="src2">WGL_FONT_POLYGONS,<span class="kom">// Polygony ne drátìný model</span></p>
<p class="src2">gmf);<span class="kom">// Adresa bufferu pro ulo¾ení informací.</span></p>
<p class="src0">}</p>

<p>K nahrání textur pøidáme kód, který u¾ znáte z pøedchozích tutoriálù. Vytvoøíme mipmapovanou texturu, proto¾e vypadá lépe. </p>

<p class="src0">int LoadGLTextures()<span class="kom">// Vytvoøí texturu</span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>
<p class="src"></p>
<p class="src1">AUX_RGBImageRec *TextureImage[1];<span class="kom">// Místo pro obrázek</span></p>
<p class="src1">memset(TextureImage,0,sizeof(void *)*1);<span class="kom">// Nastaví ukazatel na NULL</span></p>
<p class="src"></p>
<p class="src1">if (TextureImage[0]=LoadBMP(&quot;Data/Lights.bmp&quot;))<span class="kom">// Nahraje bitmapu</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;</p>
<p class="src"></p>
<p class="src2">glGenTextures(1, &amp;texture[0]);<span class="kom">// Generuje texturu</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvoøí lineárnì mipmapovanou texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[0]);</p>
<p class="src2">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR_MIPMAP_NEAREST);</p>

<p>Dal¹í øádky umo¾òují pou¾ít automatické generování koordinátù textur na jakékoli zobrazované objekty. Pøíkaz glTexGen je velmi silný a komplexní. Popsat v¹echny vlastnosti, které zahrnuje, by byl tutoriál sám o sobì. Nicménì, v¹e, co potøebujete vìdìt, je ¾e, GL_S a GL_T jsou texturové koordináty. Implicitnì jsou nastaveny tak, aby vzaly pozice x a y na obrazovce a pøi¹ly s bodem textury. V¹imnìte si, ¾e objekty nejsou texturovány na ose z. Pøední i zadní èást ploch je otexturovaná a to je to, na èem zále¾í. x (GL_S) mapuje textury zleva doprava a y (GL_T) nahoru a dolù. GL_TEXTURE_GEN_MODE pou¾ijeme pøi výbìru texturového mapování S i T. Jsou celkem tøi mo¾nosti v dal¹ím parametru:</p>
<p>GL_EYE_LINEAR - textura je namapovaná na v¹echny stìny stejnì<br />
GL_OBJECT_LINEAR - textura je fixovaná na pøední stìnu, do hloubky se protáhne<br />
GL_SPHERE_MAP - textura kovovì odrá¾ející svìtlo</p>
<p>Je dùle¾ité poznamenat, ¾e jsem vypustil spoustu kódu. Správnì bychom mìli urèit také GL_OBJECT_PLANE, ale implicitní nastavení nám staèí. Pokud byste se chtìli dozvìdìt více, tak si kupte nìjakou dobrou knihu nebo zkuste nápovìdu MSDN.</p>

<p class="src2">glTexGeni(GL_S, GL_TEXTURE_GEN_MODE, GL_OBJECT_LINEAR);</p>
<p class="src2">glTexGeni(GL_T, GL_TEXTURE_GEN_MODE, GL_OBJECT_LINEAR);</p>
<p class="src2">glEnable(GL_TEXTURE_GEN_S);</p>
<p class="src2">glEnable(GL_TEXTURE_GEN_T);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (TextureImage[0])<span class="kom">// Pokud bitmapa existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (TextureImage[0]-&gt;data)<span class="kom">// Pokud existují data bitmapy</span></p>
<p class="src2">{</p>
<p class="src3">free(TextureImage[0]-&gt;data);<span class="kom">// Sma¾e data bitmapy</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">free(TextureImage[0]);<span class="kom">// Sma¾e strukturu bitmapy</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return Status;</p>
<p class="src0">}</p>

<p>Udìláme také nìkolik zmìn v inicializaèním kódu. BuildFont() pøesuneme pod loading textur. Pokud byste chtìli mìnit barvy textur pou¾itím glColor3f(R,G,B), pøidejte glEnable(GL_COLOR_MATERIAL).</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echna nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje texturu</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">BuildFont();<span class="kom">// Vytvoøí font</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí hloubkové testování</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne implicitní svìtlo</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne svìtla</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>

<p>Zapneme automatické mapování textur. texture[0] se teï namapuje na jakýkoli 3D objekt kreslený na obrazovku. Pokud byste potøebovali více kontroly mù¾ete automatické mapování pøi kreslení ruènì zapínat a vypínat.</p>

<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturové mapování</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Zvolí texturu</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Namísto udr¾ování objektu uprostøed obrazovky budeme v této lekci "létat" dokola po celém monitoru. Pøesuneme se o 3 jednotky dovnitø. Hodnota pro osu x se bude mìnit od -1.1 do +1.1. Krajní meze na ose y jsou -0.8 a +0.8. K výpoètu pou¾ijeme promìnnou "rot". Jako v¾dy budeme rotovat okolo os.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový bufferu</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(1.1f*float(cos(rot/16.0f)),0.8f*float(sin(rot/20.0f)),-3.0f);</p>
<p class="src1">glRotatef(rot,1.0f,0.0f,0.0f);<span class="kom">// Rotace na x</span></p>
<p class="src1">glRotatef(rot*1.2f,0.0f,1.0f,0.0f);<span class="kom">// Rotace na y</span></p>
<p class="src1">glRotatef(rot*1.4f,0.0f,0.0f,1.0f);<span class="kom">// Rotace na z</span></p>

<p>Pøesuneme se trochu doleva, dolù a dopøedu k vycentrování symbolu na ka¾dé ose, abychom simulovali také otáèení kolem vlastního centra (-0.35 je èíslo, které pracuje ;) S tímto pøesunem jsem si musel trochu pohrát, proto¾e si nejsem jistý, jak je font ¹iroký, ka¾dé písmeno se víceménì li¹í. Nejsem si jistý, proè se fonty nevytváøejí kolem centrálního bodu.</p>

<p class="src1">glTranslatef(-0.35f,-0.35f,0.1f);<span class="kom">// Vycentrování</span></p>

<p>Nakonec nakreslíme lebku a zkøí¾ené kosti. Nechápete-li proè právì "N", tak si pus»te MS Word vyberte písmo Wingdings a napi¹te "N" - odpovídá mu tento symbol. Aby se lebka pohybovala ka¾dým pøekreslením inkrementujeme rot.</p>

<p class="src1">glPrint(&quot;N&quot;);<span class="kom">// Vykreslí lebku a zkøí¾ené kosti</span></p>
<p class="src1">rot+=0.1f;<span class="kom">// Inkrementace rotace a pohybu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>I kdy¾ jsem nepopsal probíranou látku do ¾ádných extrémních detailù, mìli byste pochopit, jak po¾ádat OpenGL o automatické generování texturových koordinátù. Nemìli byste mít ¾ádné problémy s otexturováním jakýchkoli objektù. Zmìnou pouhých dvou øádkù kódu (viz. GL_SPHERE_MAP u vytváøení textur), dosáhnete perfektního efektu sférického mapování (kovové odlesky svìtla).</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson15.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson15_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson15.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson15.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson15.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson15.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson15.zip">GLut</a> kód této lekce. ( <a href="mailto:oster@ieee.org">David Phillip Oster</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson15.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson15.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson15.sit.hqx">Mac OS</a> kód této lekce. ( <a href="mailto:oster@ieee.org">David Phillip Oster</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson15.zip">MASM</a> kód této lekce. ( <a href="mailto:greg@ozducati.com">Greg Helps</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson15.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson15.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(15);?>
<?FceNeHeOkolniLekce(15);?>

<?
include 'p_end.php';
?>
