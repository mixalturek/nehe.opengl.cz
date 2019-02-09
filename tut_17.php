<?
$g_title = 'CZ NeHe OpenGL - Lekce 17 - 2D fonty z textur';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(17);?>

<h1>Lekce 17 - 2D fonty z textur</h1>

<p class="nadpis_clanku">V této lekci se nauèíte, jak vykreslit font pomocí texturou omapovaného obdélníku. Dozvíte se také, jak pou¾ívat pixely místo jednotek. I kdy¾ nemáte rádi mapování 2D znakù, najdete zde spoustu nových informací o OpenGL.</p>

<p>Tu¹ím, ¾e u¾ vás asi fonty unavují. Textové lekce vás, nicménì nenauèili jenom "nìco vypsat na monitor", nauèili jste se také 3D fonty, mapování textur na cokoli a spoustu dal¹ích vìcí. Nicménì, co se stane pokud budete kompilovat projekt pro platformu, která nepodporuje fonty? Podíváte se do lekce 17... Pokud si pamatujete na první lekci o fontech (13), tak jsem tam vysvìtloval pou¾ívání textur pro vykreslování znakù na obrazovku. Obyèejnì, kdy¾ pou¾íváte textury ke kreslení textu na obrazovku, spustíte grafický program, zvolíte font, napí¹ete znaky, ulo¾íte bitmapu a "loadujete" ji do svého programu. Tento postup není zrovna efektivní pro program, ve kterém pou¾íváte hodnì textù nebo texty, které se neustále mìní. Ale jak to udìlat lépe? Program v této lekci pou¾ívá pouze JEDNU! texturu. Ka¾dý znak na tomto obrázku bude zabírat 16x16 pixelù. Bitmapa tedy celkem zabírá ètverec o stranì 256 bodù (16*16=256) - standardní velikost. Tak¾e... pojïme vytvoøit 2D font z textury. Jako obyèejnì, i tentokrát rozvíjíme první lekci.</p>

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
<p class="src0">GLuint base;<span class="kom">// Ukazatel na první z display listù pro font</span></p>
<p class="src0">GLuint texture[2];<span class="kom">// Ukládá textury</span></p>
<p class="src0">GLuint loop;<span class="kom">// Pomocná pro cykly</span></p>
<p class="src"></p>
<p class="src0">GLfloat cnt1;<span class="kom">// Èítaè 1 pro pohyb a barvu textu</span></p>
<p class="src0">GLfloat cnt2;<span class="kom">// Èítaè 2 pro pohyb a barvu textu</span></p>

<p>Následující kód je trochu odli¹ný, od toho z pøedchozích lekcí. V¹imnìte si, ¾e TextureImage[] ukládá dva záznamy o obrázcích. Je velmi dùle¾ité zdvojit pamì»ové místo a loading. Jedno ¹patné èíslo by mohlo zplodí pøeteèení pamìti nebo totální error.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Nahraje bitmapu a konvertuje na texturu </span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>
<p class="src1">AUX_RGBImageRec *TextureImage[2];<span class="kom">// Alokuje místo pro bitmapy</span></p>

<p>Pokud byste zamìnili èíslo 2 za jakékoli jiné, budou se dít vìci. V¾dy se musí rovnat èíslu z pøedchozí øádky (tedy v TextureImage[] ). Textury, které chceme nahrát se jmenují font.bmp a bumps.bmp. Tu druhou mù¾ete zamìnit - není a¾ tak podstatná.</p>

<p class="src1">memset(TextureImage,0,sizeof(void *)*2);<span class="kom">// Nastaví ukazatel na NULL</span></p>
<p class="src"></p>
<p class="src1">if ((TextureImage[0]=LoadBMP(&quot;Data/Font.bmp&quot;)) &amp;&amp; (TextureImage[1]=LoadBMP(&quot;Data/Bumps.bmp&quot;)))</p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;<span class="kom">// Nastaví status na TRUE</span></p>

<p>Nebudu vám ani øíkat kolik emailù jsem obdr¾el od lidí ptajících se: "Proè vidím jenom jednu texturu?" nebo "Proè jsou v¹echny moje textury bílé!?!". Vìt¹inou bývá problém v tomto øádku. Opìt pokud pøepí¹ete 2 na 1, bude vidìt jenom jedna textura (druhá bude bílá). A naopak, zamìníte-li 2 za 3, program se zhroutí. Pøíkaz glGenTextures() by se mìl volat jenom jednou a tímto jedním voláním vytvoøit najednou v¹echny textury, které hodláte pou¾ít. U¾ jsem vidìl lidi, kteøí tvoøili ka¾dou texturu zvlá¹». Je dobré, si v¾dy na zaèátku rozmyslet, kolik jich budete pou¾ívat.</p>

<p class="src2">glGenTextures(2, &amp;texture[0]);<span class="kom">// 2 textury</span></p>
<p class="src2"></p>
<p class="src2">for (loop=0; loop&lt;2; loop++)</p>
<p class="src2">{</p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop]);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src3">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[loop]-&gt;sizeX, TextureImage[loop]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[loop]-&gt;data);</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Na konci funkce uvolníme v¹echnu pamì», kterou jsme alokovali pro vytvoøení textur. I zde si v¹imnìte uvolòování dvou záznamù.</p>

<p class="src1">for (loop=0; loop&lt;2; loop++)</p>
<p class="src1">{</p>
<p class="src2">if (TextureImage[loop])<span class="kom">// Pokud obrázek existuje</span></p>
<p class="src2">{</p>
<p class="src3">if (TextureImage[loop]-&gt;data)<span class="kom">// Pokud existují data obrázku</span></p>
<p class="src3">{</p>
<p class="src4">free(TextureImage[loop]-&gt;data);<span class="kom">// Uvolní pamì» obrázku</span></p>
<p class="src3">}</p>
<p class="src3">free(TextureImage[loop]);<span class="kom">// Uvolní strukturu obrázku</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">return Status;</p>
<p class="src0">}</p>

<p>Teï vytvoøíme font. Proto¾e pou¾ijeme trochu matematiky, zabìhneme trochu do detailù.</p>

<p class="src0">GLvoid BuildFont(GLvoid)<span class="kom">// Vytvoøení display listù fontu</span></p>
<p class="src0">{</p>

<p>Jak u¾ plyne z názvu, budou promìnné pou¾ity k urèení pozice, ka¾dého znaku na textuøe fontu.</p>

<p class="src1">float cx;<span class="kom">// Koordináty x</span></p>
<p class="src1">float cy;<span class="kom">// Koordináty y</span></p>

<p>Dále øekneme OpenGL, ¾e chceme vytvoøit 256 display listù. "base" ukazuje na první display list. Potom vybereme texturu.</p>

<p class="src1">base=glGenLists(256);<span class="kom">// 256 display listù</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Výbìr textury</span></p>

<p>Zaèneme cyklus generující v¹ech 256 znakù.</p>

<p class="src1">for (loop=0; loop&lt;256; loop++)<span class="kom">// Vytváøí 256 display listù</span></p>
<p class="src1">{</p>

<p>První øádka mù¾e vypadat trochu nejasnì. Symbol % vyjadøuje celoèíselný zbytek po dìlení 16. Pomocí cx se budeme pøesunovat na textuøe po øádcích (zleva doprava), cy zaji¹»uje pohyb ve sloupcích (od shora dolù). Dal¹ích operace "/16.0f" konvertuje výsledek do koordinátù textury. Pokud bude loop rovno 16 - cx bude rovno zbytku z 16/16 tedy nule (16/16=1 zbytek 0). Ale cy bude výsledkem "normálního" dìlení - 16/16=1. Dále bychom se tedy mìli na textuøe pøesunout na dal¹ích øádek, dolù o vý¹ku jednoho znaku a pøesunovat se opìt zleva doprava. loop se tedy rovná 17, cx=17/16=1,0625. Desetinná èást (0,0625) je vlastnì rovna jedné ¹estnáctinì. Z toho plyne, ¾e jsme se pøesunuli o jeden znak doprava. cy je stále jedna (viz. dále). 18/16 udává posun o 2 znaky doprava a jeden znak dolù. Analogicky se dostaneme k loop=32. cx bude rovno 0 (32/16=2 zbytek 0). cy=2, tím se na textuøe posuneme o dva znaky dolù. Dává to smysl? (Pozn. pøekladatele: Já bych asi pou¾il vnoøený cyklus - vnìj¹ím jít po sloupcích a vnitøním po øádcích. Bylo by to mo¾ná pochopitelnìj¹í (...a hlavnì snadnìj¹í na pøeklad :-))</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_17_font.gif" width="256" height="256" alt="Textura fontu" /></div>

<p class="src2">cx=float(loop%16)/16.0f;<span class="kom">// X pozice aktuálního znaku</span></p>
<p class="src2">cy=float(loop/16)/16.0f;<span class="kom">// Y pozice aktuálního znaku</span></p>

<p>Teï, po tro¹e matematického vysvìtlování, zaèneme vytváøet 2D font. Pomocí cx a cy vyjmeme ka¾dý znak z textury fontu. Pøièteme loop k hodnotì base - aby se znaky nepøepisovaly ukládáním v¾dy do prvního. Ka¾dý znak se ulo¾í do vlastního display listu.</p>

<p class="src2">glNewList(base+loop,GL_COMPILE);<span class="kom">// Vytvoøení display listu</span></p>

<p>Po zvolení display listu do nìj nakreslíme obdélník otexturovaný znakem.</p>

<p class="src3">glBegin(GL_QUADS);<span class="kom">// Pro ka¾dý znak jeden obdélník</span></p>

<p>Cx a cy jsou schopny ulo¾it velmi malou desetinnou hodnotu. Pokud cx a zároveò cy budou 0, tak bude pøíkaz vypadat takto: glTexCoord2f(0.0f,1-0.0f-0.0625f); Pamatujte si, ¾e 0,0625 je pøesnì 1/16 na¹í textury nebo ¹íøka/vý¹ka jednoho znaku. Koordináty mohou ukazovat na levý dolní roh na¹í textury. V¹imnìte si, ¾e pou¾íváme glVertex2i(x,y) namísto glVertex3f(x,y,z). Nebudeme potøebovat hodnotu z, proto¾e pracujeme s 2D fontem. Proto¾e pou¾íváme kolnou projekci (ortho), nemusíme se pøesunout do hloubky - staèí tedy pouze x, y. Okno má velikost 0-639 a 0-479 (640x480) pixelù, tudí¾ nemusíme pou¾ívat desetinné nebo dokonce záporné hodnoty. Cesta jak nastavit ortho obraz je urèit 0, 0 jako levý dolní roh a 640, 480 jako pravý horní roh. Zjednodu¹enì øeèeno: zbavili jsme se záporných koordinátù. U¾iteèná vìc, pro lidi, kteøí se nechtìjí starat o perspektivu, a kteøí více preferují práci s pixely ne¾ s jednotkami :)</p>

<p class="src4">glTexCoord2f(cx,1-cy-0.0625f); glVertex2i(0,0);<span class="kom">// Levý dolní</span></p>

<p>Druhý koordinát je teï posunut o 1/16 doprava (¹íøka znaku) - pøièteme k x-ové hodnotì 0,0625f.</p>

<p class="src4">glTexCoord2f(cx+0.0625f,1-cy-0.0625f); glVertex2i(16,0);<span class="kom">// Pravý dolní</span></p>

<p>Tøetí koordinát zùstává vpravo, ale pøesunul se nahoru (o vý¹ku znaku).</p>

<p class="src4">glTexCoord2f(cx+0.0625f,1-cy); glVertex2i(16,16);<span class="kom">// Pravý horní</span></p>

<p>Urèíme levý horní roh znaku.</p>

<p class="src4">glTexCoord2f(cx,1-cy); glVertex2i(0,16);<span class="kom">// Levý horní</span></p>
<p class="src"></p>
<p class="src3">glEnd();<span class="kom">// Konec znaku</span></p>


<p>Pøesuneme se o 10 pixelù doprava, tím se umístíme doprava od právì nakreslené textury. Pokud bychom se nepøesunuli, v¹echny znaky by se nakupily na jedno místo. Proto¾e je font tro¹ku "hubenìj¹í" (u¾¹í), nepøesuneme se o celých 16 pixelù (¹íøku znaku), ale pouze o 10. Mezi jednotlivými písmeny by byly velké mezery.</p>

<p class="src3">glTranslated(10,0,0);<span class="kom">// Pøesun na pravou stranu znaku</span></p>
<p class="src"></p>
<p class="src2">glEndList();<span class="kom">// Konec kreslení display listu</span></p>
<p class="src"></p>
<p class="src1">}<span class="kom">// Cyklus pokraèuje dokud se nevytvoøí v¹ech 256 znakù</span></p>
<p class="src0">}</p>

<p>Opìt pøidáme kód pro uvolnìní v¹ech 256 display listù znaku. Provede se pøi ukonèování programu.</p>

<p class="src0">GLvoid KillFont(GLvoid)<span class="kom">// Uvolní pamì» fontu</span></p>
<p class="src0">{</p>
<p class="src1">glDeleteLists(base,256);<span class="kom">// Sma¾e 256 display listù</span></p>
<p class="src0">}</p>

<p>V následující funkci se provádí výstup textu. V¹echno je pro vás nové, tudí¾ vysvìtlím ka¾dou øádku hodnì podrobnì. Do tohoto kódu by mohla být pøidána spousta dal¹ích funkcí, jako je podpora promìnných, zvìt¹ování znakù, rozestupy ap. Funkci glPrint() pøedáváme tøi parametry. První a druhý je pozice textu v oknì (u Y je nula dole!), tøetí je ¾ádaný øetìzec a poslední je znaková sada. Podívejte se na bitmapu fontu. Jsou tam dvì rozdílené znakové sady (v tomto pøípadì je první obyèejná - 0, druhá kurzívou - cokoli jiného).</p>

<p class="src0">GLvoid glPrint(GLint x, GLint y, char *string, int set)<span class="kom">// Provádí výpis textu</span></p>
<p class="src0">{</p>

<p>Napøed se ujistíme, zda je set buï 1 nebo 0. Pokud je vìt¹í ne¾ 1, pøiøadíme jí 0. (Pozn. pøekladatele: Autor asi zapomnìl na èastou obranu u¾ivatelù pøi zhroucení programu: "Ne urèitì jsem tam nezadal záporné èíslo!" :-)</p>

<p class="src1">if (set&gt;1)</p>
<p class="src1">{</p>
<p class="src2">set=1;</p>
<p class="src1">}</p>

<p>Proto¾e je mo¾né, ¾e máme pøed spu¹tìním funkce vybranou (na tomto místì) "randomovou" texturu, zvolíme tu "fontovou".</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Výbìr textury</span></p>

<p>Vypneme hloubkové textování - blending vypadá lépe (text by mohl skonèit za nìjakým objektem, nemusí vypadat správnì...). Okolí textu vám nemusí vadit, kdy¾ pou¾íváte èerné pozadí.</p>

<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne hloubkové testování</span></p>

<p>Hodnì dùle¾itá vìc! Zvolíme projekèní matici (Projection Matrix) a pøíkazem glPushMatrix() ji ulo¾íme (nìco jako pamì» na kalkulaèce). Do pùvodního stavu ji mù¾eme obnovit voláním glPopMatrix() (viz. dále).</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Vybere projekèní matici</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾í projekèní matici</span></p>

<p>Poté, co byla projekèní matice ulo¾ena, resetujeme matici a nastavíme ji pro kolmou projekci (Ortho screen). Parametry mají význam oøezávacích rovin (v poøadí): levá, pravá, dolní, horní, nejbli¾¹í, nejvzdálenìj¹í. Levou stranu bychom mohli urèit na -640, ale proè pracovat se zápornými èísly? Je moudré nastavit tyto hodnoty, abyste si zvolili meze (rozli¹ení), ve kterých právì pracujete.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glOrtho(0,640,0,480,-1,1);<span class="kom">// Nastavení kolmé projekce</span></p>

<p>Teï urèíme matici modelview a opìt voláním glPushMatrix() ulo¾íme stávající nastavení. Poté resetujeme matici modelview, tak¾e budeme moci pracovat s kolmou projekcí.</p>

<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Výbìr matice</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾ení matice</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>S ulo¾enými nastaveními pro perspektivu a kolmou projekci, mù¾eme zaèít vykreslovat text. Zaèneme translací na místo, kam ho chceme vykreslit. Místo glTranslatef() pou¾ijeme glTranslated(), proto¾e není dùle¾itá desetinná hodnota. Nelze urèit pùlku pixelu :-) (Pozn. pøekladatele: Tady bude asi jeden totálnì velký error, jeliko¾ glTranslated() pracuje v pøesnosti double, tedy je¹tì ve vìt¹í - nicménì stane se. (Alespoò, ¾e víme o co jde :-). Jo, ten smajlík u pùlky pixelu byl i v pùvodní verzi.)</p>

<p class="src1">glTranslated(x,y,0);<span class="kom">// Pozice textu (0,0 - levá dolní)</span></p>

<p>Øádek ní¾e urèí znakovou sadu. Pøi pou¾ití druhé pøièteme 128 k display listu base (128 je polovina z 256 znakù). Pøiètením 128 "pøeskoèíme" prvních 128 znakù.</p>

<p class="src1">glListBase(base-32+(128*set));<span class="kom">// Zvolí znakovou sadu (0 nebo 1)</span></p>

<p>Zbývá vykreslení. Jako poka¾dé v minulých lekcích to provedeme i zde voláním glCallLists(). strlen(string) je délka øetìzce (ve znacích), GL_BYTE znamená, ¾e ka¾dý znak je reprezentován bytem (hodnoty 0 a¾ 255). Nakonec, ve string pøedáváme konkrétní text pro vykreslení.</p>

<p class="src1">glCallLists(strlen(string),GL_BYTE,string);<span class="kom">// Vykreslení textu na obrazovku</span></p>

<p>Obnovíme perspektivní pohled. Zvolíme projekèní matici a pou¾ijeme glPopMatrix() k odvolání se na døíve ulo¾ená (glPushMatrix()) nastavení.</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Výbìr projekèní matice</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení ulo¾ené projekèní matice</span></p>

<p>Zvolíme matice modelview a udìláme to samé jako pøed chvílí.</p>

<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Výbìr matice modelview</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení ulo¾ené modelview matice</span></p>

<p>Povolíme hloubkové testování. Pokud jste ho na zaèátku nevypínali, tak tuto øádku nepotøebujete.</p>

<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkové testování</span></p>
<p class="src0">}</p>

<p>Vytvoøíme textury a display listy. Pokud se nìco nepovede vrátíme false. Tím program zjistí, ¾e vznikl error a ukonèí se.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echno nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje textury</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">BuildFont();<span class="kom">// Vytvoøí font</span></p>

<p>Následují obvyklé nastavení OpenGL.</p>

<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE);<span class="kom">// Vybere typ blendingu</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povolí jemné stínování</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapování 2D textur</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Zaèneme kreslit scénu - na zaèátku stvoøíme 3D objekt a a¾ potom text. Dùvod proè jsem se rozhodl pøidat 3D objekt je prostý: chci demonstrovat souèasné pou¾ití perspektivní i kolmé projekce v jednom programu.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Zvolíme texturu vytvoøenou z bumps.bmp, pøesuneme se o pìt jednotek dovnitø a provedeme rotaci o 45° na ose Z. Toto pootoèení po smìru hodinových ruèièek vyvolá dojem diamantu a ne dvou ètvercù.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[1]);<span class="kom">// Výbìr textury</span></p>
<p class="src1">glTranslatef(0.0f,0.0f,-5.0f);<span class="kom">// Pøesun o pìt do obrazovky</span></p>
<p class="src1">glRotatef(45.0f, 0.0f,0.0f,1.0f);<span class="kom">// Rotace o 45° po smìru hodinových ruèièek na ose z</span></p>

<p>Provedeme dal¹í rotaci na osách X a Y, která je závislá na promìnné cnt1*30. Má za následek otáèení objektu dokola, stejnì jako se otáèí diamant na jednom místì.</p>

<p class="src1">glRotatef(cnt1*30.0f,1.0f,1.0f,0.0f);<span class="kom">// Rotace na osách x a y</span></p>

<p>Proto¾e chceme aby se jevil jako pevný, vypneme blending a nastavíme bílou barvu. Vykreslíme texturou namapovaný ètyøúhelník.</p>

<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vypnutí blendingu</span></p>
<p class="src1">glColor3f(1.0f,1.0f,1.0f);<span class="kom">// Bílá barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslení obdélníku</span></p>
<p class="src2">glTexCoord2d(0.0f,0.0f);</p>
<p class="src2">glVertex2f(-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2d(1.0f,0.0f);</p>
<p class="src2">glVertex2f( 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2d(1.0f,1.0f);</p>
<p class="src2">glVertex2f( 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2d(0.0f,1.0f);</p>
<p class="src2">glVertex2f(-1.0f,-1.0f);</p>
<p class="src1">glEnd();<span class="kom">// Konec obdélníku</span></p>

<p>Dále provedeme rotaci o 90° na osách X a Y. Opìt vykreslíme ètyøúhelník. Tento nový uprostøed protíná prvnì kreslený a je na nìj kolmý (90°). Hezký soumìrný tvar.</p>

<p class="src1">glRotatef(90.0f,1.0f,1.0f,0.0f);<span class="kom">// Rotace na osách X a Y o 90°</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslení obdélníku</span></p>
<p class="src2">glTexCoord2d(0.0f,0.0f);</p>
<p class="src2">glVertex2f(-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2d(1.0f,0.0f);</p>
<p class="src2">glVertex2f( 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2d(1.0f,1.0f);</p>
<p class="src2">glVertex2f( 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2d(0.0f,1.0f);</p>
<p class="src2">glVertex2f(-1.0f,-1.0f);</p>
<p class="src1">glEnd();<span class="kom">// Konec obdélníku</span></p>

<p>Zapneme blending a zaèneme vypisovat text. Pou¾ijeme stejné pulzování barev jako v nìkterých minulých lekcích.</p>

<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapnutí blendingu</span></p>
<p class="src1"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zmìna barvy zalo¾ená na pozici textu</span></p>
<p class="src1">glColor3f(1.0f*float(cos(cnt1)),1.0f*float(sin(cnt2)),1.0f-0.5f*float(cos(cnt1+cnt2)));</p>

<p>Pro vykreslení stále vyu¾íváme funkci glPrint(). Prvními parametry jsou x-ová a Y-ová souøadnice, tøetí atribut, "NeHe", bude výstupem a poslední urèuje znakovou sadu (0-normální, 1-kurzíva). Asi jste si domysleli, ¾e textem pohybujeme pomocí sinù a kosinù. Pokud jste tak trochu "v pasti", vra»te se do minulých lekcí, ale není podmínkou tomu a¾ tak rozumìt.</p>

<p class="src1">glPrint(int((280+250*cos(cnt1))),int(235+200*sin(cnt2)),&quot;NeHe&quot;,0);<span class="kom">// Vypí¹e text</span></p>
<p class="src"></p>
<p class="src1">glColor3f(1.0f*float(sin(cnt2)),1.0f-0.5f*float(cos(cnt1+cnt2)),1.0f*float(cos(cnt1)));</p>
<p class="src"></p>
<p class="src1">glPrint(int((280+230*cos(cnt2))),int(235+200*sin(cnt1)),&quot;OpenGL&quot;,1);<span class="kom">// Vypí¹e text</span></p>

<p>Nastavíme barvu na modrou a na spodní èást okna napí¹eme jméno autora této lekce. Celé to zopakujeme s bílou barvou a posunutím o dva pixely doprava - jednoduchý stín (není-li zapnutý blending nebude to fungovat).</p>

<p class="src1">glColor3f(0.0f,0.0f,1.0f);<span class="kom">// Modrá barva</span></p>
<p class="src1">glPrint(int(240+200*cos((cnt2+cnt1)/5)),2,&quot;Giuseppe D'Agata&quot;,0);<span class="kom">// Vypí¹e text</span></p>
<p class="src"></p>
<p class="src1">glColor3f(1.0f,1.0f,1.0f);<span class="kom">// Bílá barva</span></p>
<p class="src1">glPrint(int(242+200*cos((cnt2+cnt1)/5)),2,&quot;Giuseppe D'Agata&quot;,0);<span class="kom">// Vypí¹e text</span></p>

<p>Inkrementujeme èítaèe - text se bude pohybovat a objekt rotovat.</p>


<p class="src1">cnt1+=0.01f;</p>
<p class="src1">cnt2+=0.0081f;</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Myslím, ¾e teï mohu oficiálnì prohlásit, ¾e moje tutoriály nyní vysvìtlují v¹echny mo¾né cesty k vykreslení textu. Kód z této lekce mù¾e být pou¾it na jakékoli platformì, na které funguje OpenGL, je snadný k pou¾ívání. Vykreslování tímto zpùsobem &quot;u¾írá&quot; velmi málo procesorového èasu. Rád bych podìkoval Guiseppu D'Agatovi za originální verzi této lekce. Hodnì jsem ji upravil a konvertoval na nový základní kód, ale bez nìj bych to asi nesvedl. Jeho verze má trochu více mo¾ností, jako vzdálenost znakù apod., ale já jsem zase stvoøil &quot;extrémnì skvìlý 3D objekt&quot;. </p>

<p class="autor">napsal: Giuseppe D'Agata <?VypisEmail('waveform@tiscalinet.it');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson17.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson17_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson17.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson17.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson17.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson17.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson17.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson17.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson17.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson17.tar.gz">Irix / GLUT</a> kód této lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson17.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson17.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson17.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson17.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson17.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson17.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson17.zip">MASM</a> kód této lekce. ( <a href="mailto:greg@ozducati.com">Greg Helps</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson17.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson17.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson17.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(17);?>
<?FceNeHeOkolniLekce(17);?>

<?
include 'p_end.php';
?>
