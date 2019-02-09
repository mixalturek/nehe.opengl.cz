<?
$g_title = 'CZ NeHe OpenGL - Lekce 6 - Textury';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(6);?>

<h1>Lekce 6 - Textury</h1>

<p class="nadpis_clanku">Namapujeme bitmapový obrázek na krychli. Pou¾ijeme zdrojové kódy z první lekce, proto¾e je jednodu¹í (a pøehlednìj¹í) zaèít s prázdným oknem ne¾ slo¾itì upravovat pøedchozí lekci.</p>

<p>Porozumìní texturám má mnoho výhod. Øeknìme, ¾e chcete nechat pøeletìt pøes obrazovku støelu. A¾ do tohoto tutoriálu byste ji pravdìpodobnì vytvoøili z vybarvených n-úhelníkù. S pou¾itím textur mù¾ete vzít obrázek skuteèné støely a nechat jej letìt pøes obrazovku. Co myslíte, ¾e bude vypadat lépe? Fotografie, nebo obrázek poskládaný z trojúhelníkù a ètvercù? S pou¾itím textur to bude nejen vypadat lépe, ale i vá¹ program bude rychlej¹í. Støela vytvoøená pomocí textury bude jen jeden ètverec pohybující se po obrazovce. Støela tvoøená n-úhelníky by mohla být tvoøena stovkami, nebo tisíci n-úhelníky. Jeden ètverec pokrytý texturou bude mít mnohem men¹í nároky.</p>

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

<p>Pøidáme tøi nové desetinné promìnné... xrot, yrot a zrot. Tyto promìnné budou pou¾ity k rotaci krychle okolo os. Poslední øádek GLuint texture[1] deklaruje prostor pro jednu texturu. Pokud chcete nahrát více ne¾ jednu texturu, zmìòte èíslo jedna na èíslo odpovídající poèet textur, které chcete nahrát.</p>

<p class="src0">GLfloat xrot;<span class="kom">// X Rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y Rotace</span></p>
<p class="src0">GLfloat zrot;<span class="kom">// Z Rotace</span></p>
<p class="src"></p>
<p class="src0">GLuint texture[1];<span class="kom">// Ukládá texturu</span></p>

<p>Bezprostøednì za pøedcházející kód a pøed funkci ReSizeGLScene() pøidáme následující funkci. Jejím úèelem je nahrávání souboru s bitmapou. Pokud soubor neexistuje, vrátí NULL, co¾ vyjadøuje, ¾e textura nemù¾e být nahrána. Pøed vysvìtlováním kódu je tøeba vìdìt nìkolik <b>VELMI</b> dùle¾itých vìcí o obrázcích pou¾itých pro textury. Vý¹ka a ¹íøka obrázku musí být mocnina dvou, ale nejménì 64 pixelù. Z dùvodù kompatibility by nemìly být vìt¹í ne¾ 256 pixelù. Pokud by bitmapa, kterou chcete pou¾ít nemìla velikost 64, 128 nebo 256, zmìòte její velikost pomocí editoru obrázkù. Existují zpùsoby jak obejít tyto limity, ale my zùstaneme u standardních velikostí textury. První vìc kterou udìláme je deklarace ukazatele na soubor. Na zaèátku jej nastavíme na NULL.</p>

<p class="src0">AUX_RGBImageRec *LoadBMP(char *Filename)<span class="kom">// Nahraje bitmapu</span></p>
<p class="src0">{</p>
<p class="src1">FILE *File=NULL;<span class="kom">// Ukazatel na soubor</span></p>

<p>Dále se ujistíme, ¾e bylo pøedáno jméno souboru. Je mo¾né zavolat funkci LoadBMP() bez zadání jména souboru, tak¾e to musíme zkontrolovat. Nechceme se sna¾it nahrát nic. Dále se pokusíme otevøít tento soubor pro ètení, abychom zkontrolovali, zda soubor existuje.</p>

<p class="src1">if (!Filename)<span class="kom">// Byla pøedána cesta k souboru?</span></p>
<p class="src1">{</p>
<p class="src2">return NULL;<span class="kom">// Pokud ne, konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">File=fopen(Filename,&quot;r&quot;);<span class="kom">// Otevøení pro ètení</span></p>

<p>Pokud se nám podaøilo soubor otevøít, zjevnì existuje. Zavøeme soubor a  pomocí funkce auxDIBImageLoad(Filename) vrátíme data obrázku.</p>

<p class="src1">if (File)<span class="kom">// Existuje soubor?</span></p>
<p class="src1">{</p>
<p class="src2">fclose(File);<span class="kom">// Zavøe ho</span></p>
<p class="src2">return auxDIBImageLoad(Filename);<span class="kom">// Naète bitmapu a vrátí na ni ukazatel</span></p>
<p class="src1">}</p>

<p>Pokud se nám soubor nepodaøilo otevøít soubor vrátíme NULL, co¾ indikuje, ¾e soubor nemohl být nahrán. Pozdìji v programu budeme kontrolovat, zda se v¹e povedlo v poøádku.</p>

<p class="src1">return NULL;<span class="kom">// Pøi chybì vrátíme NULL</span></p>
<p class="src0">}</p>

<p>Nahraje bitmapu (voláním pøedchozího kódu) a konvertujeme jej na texturu.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Loading bitmapy a konverze na texturu</span></p>
<p class="src0">{</p>

<p>Deklarujeme bool promìnnou zvanou Status. Pou¾ijeme ji k sledování, zda se nám podaøilo nebo nepodaøilo nahrát bitmapu a sestavit texturu. Její poèáteèní hodnotu nastavíme na FALSE.</p>

<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>

<p>Vytvoøíme záznam obrázku, do kterého mù¾eme bitmapu ulo¾it. Záznam bude ukládat vý¹ku, ¹íøku a data bitmapy.</p>

<p class="src1">AUX_RGBImageRec *TextureImage[1];<span class="kom">// Ukládá bitmapu</span></p>

<p>Abychom si byli jisti, ¾e je obrázek prázdný, vynulujeme pøidìlenou pamì».</p>

<p class="src1">memset(TextureImage,0,sizeof(void *)*1);<span class="kom">// Vynuluje pamì»</span></p>

<p>Nahrajeme bitmapu a konvertujeme ji na texturu. TextureImage[0]=LoadBMP("Data/NeHe.bmp") zavolá døíve napsanou funkci LoadBMP(). Pokud se v¹e podaøí, data bitmapy se ulo¾í do TextureImage[0], Status je nastaven na TRUE a zaèneme sestavovat texturu.</p>

<p class="src1">if (TextureImage[0]=LoadBMP(&quot;Data/NeHe.bmp&quot;))<span class="kom">// Nahraje bitmapu a kontroluje vzniklé chyby</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;<span class="kom">// V¹e je bez problémù</span></p>

<p>Teï, kdy¾ máme nahrána data obrázku do TextureImage[0], sestavíme texturu s pou¾itím tìchto dat. První øádek glGenTextures(1, &amp;texture[0]) øekne OpenGL, ¾e chceme sestavit jednu texturu a chceme ji ulo¾it na index 0 pole. Vzpomeòte si, ¾e jsme na zaèátku vytvoøili místo pro jednu texturu pomocí GLuint texture[1]. Druhý øádek glBindTexture(GL_TEXTURE_2D, texture[0]) øekne OpenGL, ¾e texture[0] (první textura), bude 2D textura. 2D textury mají vý¹ku (na ose Y) a ¹íøku (na ose X). Hlavní funkcí glBindTexture() je ukázat OpenGL dostupnou pamì». V tomto pøípadì øíkáme OpenGL, ¾e volná pamì» je na &amp;texture[0]. Kdy¾ vytvoøíme texturu, bude ulo¾ena na tomto pamì»ovém místì. V podstatì glBindTexture() uká¾e do pamìti RAM, kde je ulo¾ena na¹e textura.</p>

<p class="src2">glGenTextures(1, &amp;texture[0]);<span class="kom">// Generuje texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Typické vytváøení textury z bitmapy</span></p>

<p>Vytvoøíme 2D texturu (GL_TEXTURE_2D), nula reprezentuje hladinu podrobností obrázku (obvykle se zadává nula). Tøi je poèet datových komponent. Proto¾e je obrázek tvoøen èervenou, zelenou a modrou slo¾kou dat, jsou to tøi komponenty. TextureImage[0]->sizeX je ¹íøka textury. Pokud znáte ¹íøku, mù¾ete ji tam pøímo napsat, ale je jednodu¹¹í a univerzálnìj¹í nechat práci na poèítaèi. TextureImage[0]->sizeY je analogicky vý¹ka textury. Nula je rámeèek (obvykle nechán nulový). GL_RGB øíká OpenGL, ¾e obrazová data jsou tvoøena èervenou, zelenou a modrou v tomto poøadí. GL_UNSIGNED_BYTE znamená, ¾e data (jednotlivé hodnoty R, G a B) jsou tvoøeny z bezznaménkových bytù a koneènì TextureImage[0]->data øíká OpenGL, kde vzít data textury. V tomto pøípadì jsou to data ulo¾ená v záznamu TextureImage[0].</p>

<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);<span class="kom">// Vlastní vytváøení textury</span></p>

<p>Dal¹í dva øádky oznamují OpenGL, jaké pou¾ít typy filtrování, kdy¾ je obrázek vìt¹í (GL_TEXTURE_MAG_FILTER) nebo men¹í (GL_TEXTURE_MIN_FILTER) ne¾ originální bitmapa. Já obvykle pou¾ívám GL_LINEAR pro oba pøípady. To zpùsobuje, ¾e textura vypadá hladce ve v¹ech pøípadech. Pou¾ití GL_LINEAR po¾aduje spoustu práce procesoru a video karty, tak¾e kdy¾ je vá¹ systém pomalý, mìli by jste pou¾ít GL_NEAREST. Textura filtrovaná pomocí GL_NEAREST bude pøi zvìt¹ení vypadat kostièkovanì. Lze také kombinovat obojí. GL_LINEAR pro pøípad zvìt¹ení a GL_NEAREST na zmen¹ení.</p>

<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);<span class="kom">// Filtrování pøi zmen¹ení</span></p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);<span class="kom">// Filtrování pøi zvìt¹ení</span></p>
<p class="src1">}</p>

<p>Uvolníme pamì» RAM, kterou jsme potøebovali pro ulo¾ení dat bitmapy. Ujistíme se, ¾e data bitmapy byla ulo¾ena v TextureImage[0]. Pokud ano, ujistíme se, ¾e data byla ulo¾ena v polo¾ce data, pokud ano sma¾eme je. Potom uvolníme strukturu obrázku.</p>

<p class="src1">if (TextureImage[0])<span class="kom">// Pokud obrázek existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (TextureImage[0]-&gt;data)<span class="kom">// Pokud existují data obrázku</span></p>
<p class="src2">{</p>
<p class="src3">free(TextureImage[0]-&gt;data);<span class="kom">// Uvolní pamì» obrázku</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">free(TextureImage[0]);<span class="kom">// Uvolní strukturu obrázku</span></p>
<p class="src1">}</p>

<p>Nakonec vrátíme status. Pokud je v¹echno v poøádku, obsahuje TRUE. FALSE indikuje chybu.</p>

<p class="src1">return Status;<span class="kom">// Oznámí pøípadné chyby</span></p>
<p class="src0">}</p>

<p>Pøidáme pár øádkù kódu do InitGL. Vypí¹i celou funkci znovu, tak¾e bude jednoduché najít zmìny. První øádek if (!LoadGLTextures()) skoèí do kódu, který jsme napsali v pøedchozí èásti. Nahraje bitmapu a vygeneruje z ní texturu. Pokud z jakéhokoli dùvodu sel¾e, tak ukonèíme funkci s návratovou hodnotou FALSE. Pokus se texturu podaøilo nahrát, povolíme mapování 2D textur - glEnable(GL_TEXTURE_2D). Pokud jej zapomeneme povolit, budou se objekty obvykle zobrazovat jako bílé, co¾ nám asi nebude vyhovovat.</p>

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
<p class="src1">return TRUE;<span class="kom">// Inicializace probìhla v poøádku</span></p>
<p class="src0">}</p>

<p>Pøejdeme k vykreslování. Pokusíme se o otexturovanou krychli.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-5.0f);<span class="kom">// Posun do obrazovky</span></p>
<p class="src"></p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);<span class="kom">// Natoèení okolo osy x</span></p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);<span class="kom">// Natoèení okolo osy y</span></p>
<p class="src1">glRotatef(zrot,0.0f,0.0f,1.0f);<span class="kom">// Natoèení okolo osy z</span></p>

<p>Následující øádek vybere texturu, kterou chceme pou¾ít. Pokud máte více ne¾ jednu texturu, vyberete ji úplnì stejnì, ale s jiným indexem pole glBindTexture(GL_TEXTURE_2D, texture[èíslo textury kterou chcete pou¾ít]). Tuto funkci nesmíte volat mezi glBegin() a glEnd(). Musíte ji volat v¾dy pøed nebo za blokem ohranièeným tìmito funkcemi.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Zvolí texturu</span></p>

<p>Ke správnému namapování textury na ètyøúhelník se musíte ujistit, ¾e levý horní roh textury je pøipojen k levému hornímu rohu ètyøúhelníku ap. Pokud rohy textury nejsou pøipojeny k odpovídajícím rohùm ètyøúhelníku, zobrazí se textura natoèená, pøevrácená nebo se vùbec nezobrazí. První parametr funkce glTexCoord2f je souøadnice x textury. 0.0 je levá strana textury, 0.5 støed, 1.0 pravá strana. Druhý parametr je souøadnice y. 0.0 je spodek textury, 0.5 støed, 1.0 vr¹ek. Tak¾e teï víme, ¾e 0.0 na X a 1.0 na Y je levý horní vrchol ètyøúhelníka atd. V¹e, co musíme udìlat je pøiøadit ka¾dému rohu ètyøúhelníka odpovídající roh textury. Zkuste experimentovat s hodnotami x a y funkce glTexCoord2f. Zmìnou 1.0 na 0.5 vykreslíte pouze polovinu textury od 0.0 do 0.5 atd.</p>

<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// Pøední stìna</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Zadní stìna</span></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Vrchní stìna</span></p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Spodní stìna</span></p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Pravá stìna</span></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Levá stìna</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src1">glEnd();</p>

<p>Nakonec zvìt¹íme hodnoty promìnných xrot, yrot a zrot, které urèují natoèení krychle. Zmìnou hodnot mù¾eme zmìnit rychlost i smìr natáèení.</p>

<p class="src1">xrot+=0.3f;</p>
<p class="src1">yrot+=0.2f;</p>
<p class="src1">zrot+=0.4f;</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Po doètení této lekce byste mìli rozumìt texturovému mapování. Mìli by jste být schopni namapovat libovolnou texturu na libovolný objekt. A¾ si budete jistí, ¾e tomu rozumíte, zkuste namapovat na ka¾dou stìnu krychle jinou texturu</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson06.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson06_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson06.zip">C#</a> kód této lekce. ( <a href="mailto:sugarbee@gmx.net">Sabine Felsinger</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/csgl/lesson06.zip">VB.Net CsGL</a> kód této lekce. ( <a href="mailto:createdbyx@yahoo.com">X</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson06.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson06.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson06.zip">Delphi</a> kód této lekce. ( <a href="mailto:brad@choate.net">Brad Choate</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson06.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson06.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson06.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson06.zip">GLUT</a> kód této lekce. ( <a href="mailto:kgancarz@hotmail.com">Kyle Gancarz</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson06.zip">Irix</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson06.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson06.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson06.jar">JoGL</a> kód této lekce. ( <a href="mailto:jattier@hotmail.com">Kevin J. Duling</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson06.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson06.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson06.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson06.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson06.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson06.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson06.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson06.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson06.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/python/lesson06.tar.gz">Python</a> kód této lekce. ( <a href="mailto:hakuin@voicenet.com">John Ferguson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/realbasic/lesson06.rb.hqx">REALbasic</a> kód této lekce. ( <a href="mailto:mauitom@maui.net">Thomas J. Cunningham</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson06.zip">Solaris</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson06.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson06-2.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson06.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson06.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(6);?>
<?FceNeHeOkolniLekce(6);?>

<?
include 'p_end.php';
?>
