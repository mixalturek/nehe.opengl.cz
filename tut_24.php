<?
$g_title = 'CZ NeHe OpenGL - Lekce 24 - Výpis OpenGL roz¹íøení, oøezávací testy a textury z TGA obrázkù';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(24);?>

<h1>Lekce 24 - Výpis OpenGL roz¹íøení, oøezávací testy a textury z TGA obrázkù</h1>

<p class="nadpis_clanku">V této lekci se nauèíte, jak zjistit, která OpenGL roz¹íøení (extensions) podporuje va¹e grafická karta. Vypí¹eme je do støedu okna, se kterým budeme moci po stisku ¹ipek rolovat. Pou¾ijeme klasický 2D texturový font s tím rozdílem, ¾e texturu vytvoøíme z TGA obrázku. Jeho nejvìt¹ími pøednostmi jsou jednoduchá práce a podpora alfa kanálu. Odbouráním bitmap u¾ nebudeme muset inkludovat knihovnu glaux.</p>

<p>Tento tutoriál je daleko od prezentace grafické nádhery, ale nauèíte se nìkolik nových vìcí. Pár lidí se mì ptalo na OpenGL roz¹íøení a na to, jak zjistit, které jsou podporovány konkrétním typem grafické karty. Mohu smìle øíci, ¾e s tímto po doètení nebudete mít nejmen¹í problémy. Také se dozvíte, jak rolovat èástí scény bez toho, aby se ovlivnilo její okolí. Pou¾ijeme oøezávací testy (scissor testing). Dále si uká¾eme, jak vykreslovat linky pomocí line strips a co je dùle¾itìj¹í, kompletnì odbouráme knihovnu glaux, kterou jsme pou¾ívali kvùli texturám z bitmapových obrázkù. Budeme pou¾ívat Targa (TGA) obrázky, se kterými se snadno pracuje a které podporují alfa kanál.</p>

<p>Zaèneme programovat. První vìcí, které si v¹imneme u vkládání hlavièkových souborù je, ¾e neinkludujeme knihovnu glaux (glaux.h). Také nepøilikujeme soubor glaux.lib. U¾ nebudeme pracovat s bitmapami, tak¾e tyto soubory v projektu nepotøebujeme.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src"></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;stdarg.h&gt;<span class="kom">// Hlavièkový soubor pro funkce s promìnným poètem parametrù</span></p>
<p class="src0">#include &lt;string.h&gt;<span class="kom">// Hlavièkový soubor pro práci s øetìzci</span></p>
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

<p>Pøidáme promìnné. Scroll bude pou¾ito pro rolování èástí scény nahoru a dolù. Druhá promìnná, maxtokens, bude ukládat záznam kolik roz¹íøení je podporováno grafickou kartou. Base u¾ tradiènì ukazuje na display listy fontu. Do swidth a sheight nagrabujeme aktuální velikost okna, pomohou nám vypoèítat koordináty pro oøezání oblasti okna, které umo¾ní rolování.</p>

<p class="src0">int scroll;<span class="kom">// Pro rolování okna</span></p>
<p class="src0">int maxtokens;<span class="kom">// Poèet podporovaných roz¹íøení</span></p>
<p class="src"></p>
<p class="src0">GLuint base;<span class="kom">// Základní display list fontu</span></p>
<p class="src"></p>
<p class="src0">int swidth;<span class="kom">// ©íøka oøezané oblasti</span></p>
<p class="src0">int sheight;<span class="kom">// Vý¹ka oøezané oblasti</span></p>

<p>Napí¹eme strukturu, která bude ukládat informace o nahrávaném TGA obrázku. Pointer imageData bude ukazovat na data, ze kterých vytvoøíme obrázek. Bpp oznaèuje barevnou hloubku (bits per pixel), která mù¾e být 24 nebo 32, podle pøítomnosti alfa kanálu. Width a height definuje rozmìry. Do texID vytvoøíme texturu. Celou strukturu nazveme TextureImage.</p>

<p class="src0">typedef struct<span class="kom">// Struktura textury</span></p>
<p class="src0">{</p>
<p class="src1">GLubyte *imageData;<span class="kom">// Data obrázku</span></p>
<p class="src1">GLuint bpp;<span class="kom">// Barevná hloubka obrázku</span></p>
<p class="src1">GLuint width;<span class="kom">// ©íøka obrázku</span></p>
<p class="src1">GLuint height;<span class="kom">// Vý¹ka obrázku</span></p>
<p class="src"></p>
<p class="src1">GLuint texID;<span class="kom">// Vytvoøená textura</span></p>
<p class="src0">} TextureImage;<span class="kom">// Jméno struktury</span></p>

<p>V tomto programu budeme pou¾ívat pouze jednu texturu, tak¾e vytvoøíme pole textur o velikosti jedna.</p>

<p class="src0">TextureImage textures[1];<span class="kom">// Jedna textura</span></p>

<p>Na øadu pøichází asi nejobtí¾nìj¹í èást - nahrávání TGA obrázku a jeho konvertování na texturu. Musím je¹tì poznamenat, ¾e kód následující funkce umo¾òuje loadovat buï 24 nebo 32 bitové <b>nekomprimované</b> TGA soubory. Zabralo dost èasu zprovoznit kód, který by pracoval s obìma typy. Nikdy jsem neøekl, ¾e jsem génius. Rád bych poukázal, ¾e úplnì v¹echno není z mé hlavy. Spoustu opravdu dobrých nápadù jsem získal proèítáním internetu. Pokusil jsem se je zkombinovat do funkèního kódu, který pracuje s OpenGL. Nic snadného, nic extrémnì slo¾itého!</p>

<p>Funkci pøedáváme dva parametry. První ukazuje do pamìti, kam ulo¾íme texturu. Druhý urèuje diskovou cestu k souboru, který chceme nahrát.</p>

<p class="src0">bool LoadTGA(TextureImage *texture, char *filename)<span class="kom">// Do pamìti nahraje TGA soubor</span></p>
<p class="src0">{</p>

<p>Pole TGAheader[] definuje 12 bytù. Porovnáme je s prvními 12 bity, které naèteme z TGA souboru - TGAcompare[], abychom se ujistili, ¾e je to opravdu Targa obrázek a ne nìjaký jiný.</p>

<p class="src1">GLubyte TGAheader[12] = { 0,0,2,0,0,0,0,0,0,0,0,0 };<span class="kom">// Nekomprimovaná TGA hlavièka</span></p>
<p class="src1">GLubyte TGAcompare[12];<span class="kom">// Pro porovnání TGA hlavièky</span></p>

<p>Header[] ukládá prvních ¹est DÙLE®ITÝCH bytù z hlavièky souboru (¹íøka, vý¹ka, barevná hloubka).</p>

<p class="src1">GLubyte header[6];<span class="kom">// Prvních 6 u¾iteèných bytù z hlavièky</span></p>

<p>Do bytesPerPixel pøiøadíme výsledek operace, kdy vydìlíme barevnou hloubku v bitech osmi, abychom získali barevnou hloubku v bytech na pixel. ImageSize definuje poèet bytù, které jsou zapotøebí k vytvoøení obrázku (¹íøka*vý¹ka*barevná hloubka).</p>

<p class="src1">GLuint bytesPerPixel;<span class="kom">// Poèet bytù na pixel pou¾itý v TGA souboru</span></p>
<p class="src1">GLuint imageSize;<span class="kom">// Ukládá velikost obrázku pøi alokování RAM</span></p>

<p>Temp umo¾ní prohodit byty dále v programu. A koneènì poslední promìnnou pou¾ijeme ke zvolení správného parametru pøi vytváøení textury. Bude záviset na tom, zda je TGA 24 nebo 32 bitová. V pøípadì 24 bitù pøedáme GL_RGB a máme-li 32 bitový obrázek pou¾ijeme GL_RGBA. Implicitnì pøedpokládáme, ¾e je obrázek 32 bitový, tudí¾ do type pøiøadíme GL_RGBA.</p>

<p class="src1">GLuint temp;<span class="kom">// Pomocná promìnná</span></p>
<p class="src1">GLuint type = GL_RGBA;<span class="kom">// Implicitním GL módem je RGBA (32 BPP)</span></p>

<p>Pomocí funkce fopen() otevøeme TGA soubor filename pro ètení v binárním módu (rb). Následuje vìtvení if, ve kterém dìláme hned nìkolik vìcí najednou. Nejprve testujeme jestli soubor obsahuje data. Pokud tam ¾ádná nejsou, vrátíme false. Obsahuje-li informace, pøeèteme prvních dvanáct bytù do TGAcompare. Pou¾ijeme funkci fread(), která po jednom bytu naète ze souboru file dvanáct bytù (sizeof(TGAcompare)) a výsledek ulo¾í do TGAcompare. Vrací poèet pøeètených bytù, které porovnáme se sizeof(TGAcompare). Mìlo by jich být, jak tu¹íte :-), dvanáct. Pokud jsme bez potí¾í do¹li a¾ tak daleko, porovnáme funkcí memcmp() pole TGAheader a TGAcompare. Nebudou-li stejné zavøeme soubor a vrátíme false, proto¾e se nejedná o TGA obrázek. Do header nakonec naèteme dal¹ích ¹est bytù. Pøi chybì opìt zavøeme soubor a funkci ukonèíme.</p>

<p class="src1">FILE *file = fopen(filename, &quot;rb&quot;);<span class="kom">// Otevøe TGA soubor</span></p>
<p class="src"></p>
<p class="src1">if(file == NULL || <span class="kom">// Existuje soubor?</span></p>
<p class="src2">fread(TGAcompare,1,sizeof(TGAcompare),file) != sizeof(TGAcompare) ||<span class="kom">// Podaøilo se naèíst 12 bytù?</span></p>
<p class="src2">memcmp(TGAheader,TGAcompare,sizeof(TGAheader)) != 0 ||<span class="kom">// Mají potøebné hodnoty?</span></p>
<p class="src2">fread(header,1,sizeof(header),file) != sizeof(header))<span class="kom">// Pokud ano, naète dal¹ích ¹est bytù</span></p>
<p class="src1">{</p>
<p class="src2">if (file == NULL)<span class="kom">// Existuje soubor?</span></p>
<p class="src3">return false;<span class="kom">// Konec funkce</span></p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">fclose(file);<span class="kom">// Zavøe soubor</span></p>
<p class="src3">return false;<span class="kom">// Konec funkce</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Pokud program pro¹el kódem bez chyby máme dost informací pro definování nìkterých promìnných. První bude ¹íøka obrázku. Problém spoèívá v tom, ¾e toto èíslo je rozdìleno do dvou bytù. Ni¾¹í byte mù¾e nabývat 256 hodnot (8 bitù), tak¾e vynásobíme vy¹¹í byte 256 a k nìmu pøièteme ni¾¹í. Získali jsme ¹íøku obrázku. Stejným postupem dostaneme i vý¹ku, akorát pou¾ijeme jiné indexy v poli.</p>

<p class="src1">texture-&gt;width  = header[1] * 256 + header[0];<span class="kom">// Získá ¹íøku obrázku</span></p>
<p class="src1">texture-&gt;height = header[3] * 256 + header[2];<span class="kom">// Získá vý¹ku obrázku</span></p>

<p>Zkontrolujeme jestli je ¹íøka i vý¹ka vìt¹í ne¾ nula. Pokud ne zavøeme soubor a vrátíme false. Zároveò zkontrolujeme i barevnou hloubku, kterou hledáme v header[4]. Musí být buï 24 nebo 32 bitová.</p>

<p class="src1"> if(texture-&gt;width &lt;= 0 ||<span class="kom">// Platná ¹íøka?</span></p>
<p class="src2">texture-&gt;height &lt;= 0 ||<span class="kom">// Platná vý¹ka?</span></p>
<p class="src2">(header[4] != 24 &amp;&amp; header[4] != 32))<span class="kom">// Platná barevná hloubka?</span></p>
<p class="src1">{</p>
<p class="src2">fclose(file);<span class="kom">// Zavøe soubor</span></p>
<p class="src2">return false;<span class="kom">// Konec funkce</span></p>
<p class="src1">}</p>

<p>Spoèítali a zkontrolovali jsme ¹íøku a vý¹ku, mù¾eme pøejít k barevné hloubce v bitech a bytech a velikosti pamìti potøebné k ulo¾ení dat obrázku. U¾ víme, ¾e v header[4] je barevná hloubka v bitech na pixel. Pøiøadíme ji do bpp. Jeden byte se skládá z 8 bitù. Z toho plyne, ¾e barevnou hloubku v bytech získáme dìlením bpp osmi. Velikost dat obrázku získáme vynásobením ¹íøky, vý¹ky a bytù na pixel.</p>

<p class="src1">texture-&gt;bpp = header[4];<span class="kom">// Bity na pixel (24 nebo 32)</span></p>
<p class="src"></p>
<p class="src1">bytesPerPixel = texture-&gt;bpp / 8;<span class="kom">// Byty na pixel</span></p>
<p class="src"></p>
<p class="src1">imageSize = texture-&gt;width * texture-&gt;height * bytesPerPixel;<span class="kom">// Velikost pamìti pro data obrázku</span></p>

<p>Potøebujeme alokovat pamì» pro data obrázku. Funkci malloc() pøedáme po¾adovanou velikost. Mìla by vrátit ukazatel na zabrané místo v RAM. Následující if má opìt nìkolik úloh. V prvé øadì testuje správnost alokace. Pokud pøi ní nìco nevy¹lo, ukazatel má hodnotu NULL. V takovém pøípadì zavøeme soubor a vrátíme false. Nicménì pokud se alokace podaøila, tak  pomocí fread() naèteme data obrázku a ulo¾íme je do právì alokované pamìti. Pokud se data nepodaøí zkopírovat, uvolníme pamì», zavøeme soubor a ukonèíme funkci.</p>

<p class="src1">texture-&gt;imageData = (GLubyte *)malloc(imageSize);<span class="kom">// Alokace pamìti pro data obrázku</span></p>
<p class="src"></p>
<p class="src1">if(texture-&gt;imageData == NULL ||<span class="kom">// Podaøilo se pamì» alokovat?</span></p>
<p class="src2">fread(texture-&gt;imageData, 1, imageSize, file) != imageSize)<span class="kom">// Podaøilo se kopírování dat?</span></p>
<p class="src1">{</p>
<p class="src2">if(texture-&gt;imageData != NULL)<span class="kom">// Byla data nahrána?</span></p>
<p class="src3">free(texture-&gt;imageData);<span class="kom">// Uvolní pamì»</span></p>
<p class="src"></p>
<p class="src2">fclose(file);<span class="kom">// Zavøe soubor</span></p>
<p class="src2">return false;<span class="kom">// Konec funkce</span></p>
<p class="src1">}</p>

<p>Pokud se a¾ doteï nestalo nic, èím bychom ukonèovali funkci, máme vyhráno. Stojí pøed námi, ale je¹tì jeden úkol. Formát TGA specifikuje poøadí barevných slo¾ek BGR (modrá, zelená, èervená) narozdíl od OpenGL, které pou¾ívá RGB. Pokud bychom neprohodili èervenou a modrou slo¾ku, tak v¹echno, co má být v obrázku modré by bylo èervené a naopak. Deklarujeme cyklus, jeho¾ øídící promìnná i nabývá hodnot od nuly do velikosti obrázky. Ka¾dým prùchodem se zvìt¹uje o 3 nebo o 4 v závislosti na barevné hloubce. (24/8=3, 32/8=4). Uvnitø cyklu prohodíme R a B slo¾ky. Modrá je na indexu i a èervená i+2. Modrá by byla na i+1, ale s tou nic nedìláme, proto¾e je umístìná správnì.</p>

<p class="src1">for(GLuint i=0; i &lt; int(imageSize); i += bytesPerPixel)<span class="kom">// Prochází data obrázku</span></p>
<p class="src1">{</p>
<p class="src2">temp = texture-&gt;imageData[i];<span class="kom">// B ulo¾íme do pomocné promìnné</span></p>
<p class="src2">texture-&gt;imageData[i] = texture-&gt;imageData[i + 2];<span class="kom">// R je na správném místì</span></p>
<p class="src2">texture-&gt;imageData[i + 2] = temp;<span class="kom">// B je na správném místì</span></p>
<p class="src1">}</p>

<p>Po této operaci máme v pamìti ulo¾en obrázek TGA ve formátu, který podporuje OpenGL. Nic nám nebrání, abychom zavøeli soubor. U¾ ho k nièemu nepotøebujeme.</p>

<p class="src1">fclose(file);<span class="kom">// Zavøe soubor</span></p>

<p>Mù¾eme zaèít vytváøet texturu. Tento postup je v principu úplnì stejný, jako ten, který jsme pou¾ívali v minulých tutoriálech. Po¾ádáme OpenGL o vygenerování jedné textury na adrese texture[0].textID, kterou jsme získali pøedáním parametru ve funkci InitGL(). Pokud bychom chtìli vytvoøit druhou texturu z jiného obrázku TGA, tak se tato funkci vùbec nezmìní. V InitGL() bychom provedli volání dvakrát, ale s jinými parametry. Programujeme obecnìji...</p>

<p class="src1">glGenTextures(1, &amp;texture[0].texID);<span class="kom">// Generuje texturu</span></p>

<p>Zvolíme právì vytváøenou texturu za aktuální a nastavíme jí lineární filtrování pro zmen¹ení i zvìt¹ení.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0].texID);<span class="kom">// Zvolí texturu</span></p>
<p class="src"></p>
<p class="src1">glTexParameterf(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);<span class="kom">// Lineární filtrování</span></p>
<p class="src1">glTexParameterf(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);<span class="kom">// Lineární filtrování</span></p>

<p>Zkontrolujeme, jestli je textura 24 nebo 32 bitová. V prvním pøípadì nastavíme type na GL_RGB (bez alfa kanálu), jinak ponecháme implicitní hodnotu GL_RGBA (s alfa kanálem). Pokud bychom test neprovedli, program by se s nejvìt¹í pravdìpodobností zhroutil.</p>

<p class="src1">if (texture[0].bpp == 24)<span class="kom">// Je obrázek 24 bitový?</span></p>
<p class="src1">{</p>
<p class="src2">type = GL_RGB;<span class="kom">// Nastaví typ na GL_RGB</span></p>
<p class="src1">}</p>

<p>Teï koneènì sestavíme texturu. Jako obvykle, tak i tentokrát, pou¾ijeme funkci glTexImage2D(). Místo ruèního zadání typu textury (GL_RGB, GL_RGBA) pøedáme hodnotu pomocí promìnné. Jednodu¹e øeèeno: Program sám detekuje, co má pøedat.</p>

<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, type, texture[0].width, texture[0].height, 0, type, GL_UNSIGNED_BYTE, texture[0].imageData);<span class="kom">// Vytvoøí texturu</span></p>
<p class="src"></p>
<p class="src1">return true;<span class="kom">// V¹echno je v poøádku</span></p>
<p class="src0">}</p>

<p>ReSizeGLScene() nastavuje pravoúhlou projekci. Souøadnice [0; 1] jsou levým horním rohem okna a [640; 480] pravým dolním. Dostáváme rozli¹ení 640x480. Na zaèátku nastavíme globální promìnné swidth a sheight na aktuální rozmìry okna. Pøi ka¾dém pøesunutí nebo zmìnì velikosti okna se aktualizují. Ostatní kód znáte.</p>

<p class="src0">GLvoid ReSizeGLScene(GLsizei width, GLsizei height)<span class="kom">// Zmìna velikosti a inicializace OpenGL okna</span></p>
<p class="src0">{</p>
<p class="src1">swidth = width;<span class="kom">// ©íøka okna</span></p>
<p class="src1">sheight = height;<span class="kom">// Vý¹ka okna</span></p>
<p class="src"></p>
<p class="src1">if (height == 0)<span class="kom">// Zabezpeèení proti dìlení nulou</span></p>
<p class="src1">{</p>
<p class="src2">height = 1;<span class="kom">// Nastaví vý¹ku na jedna</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glViewport(0,0,width,height);<span class="kom">// Resetuje aktuální nastavení</span></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvolí projekèní matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glOrtho(0.0f,640,480,0.0f,-1.0f,1.0f);<span class="kom">// Pravoúhlá projekce 640x480, [0; 0] vlevo nahoøe</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvolí matici Modelview</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src0">}</p>

<p>Inicializace OpenGL se minimalizovala. Zùstala z ní jenom kostra. Nahrajeme TGA obrázek a vytvoøíme z nìj texturu. V prvním parametru je urèeno, kam ji ulo¾íme a v druhém disková cesta k obrázku. Vrátí-li funkce z jakéhokoli dùvodu false, inicializace se pøeru¹í, program zobrazí chybovou zprávu a ukonèí se. Pokud byste chtìli nahrát druhou nebo i dal¹í textury pou¾ijte volání nìkolik. Podmínka se logicky ORuje.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadTGA(&amp;textures[0], &quot;Data/Font.TGA&quot;))<span class="kom">// Nahraje texturu fontu z TGA obrázku</span></p>
<p class="src1">{</p>
<p class="src2">return false;<span class="kom">// Pøi chybì ukonèí program</span></p>
<p class="src1">}</p>

<p>Po úspì¹ném nahrání textury vytvoøíme font. Je dùle¾ité upozornit, ¾e se BuildFont() musí volat a¾ po funkci LoadTGA(), proto¾e pou¾ívá jí vytvoøenou texturu. Dále nastavíme vyhlazené stínování, èerné pozadí, povolíme mazání depth bufferu a zvolíme texturu fontu.</p>

<p class="src1">BuildFont();<span class="kom">// Sestaví font</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Vyhlazené stínování</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, textures[0].texID);<span class="kom">// Zvolí texturu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace v poøádku</span></p>
<p class="src0">}</p>

<p>Pøejdeme k vykreslování. Zaèneme deklarováním promìnných. O ukazateli token zatím jen tolik, ¾e bude ukládat øetìzec jednoho podporovaného roz¹íøení a cnt je pro zji¹tìní jeho poøadí.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">char* token;<span class="kom">// Ukládá jedno roz¹íøení</span></p>
<p class="src1">int cnt = 0;<span class="kom">// Èítaè roz¹íøení</span></p>

<p>Sma¾eme obrazovku a hloubkový buffer. Potom nastavíme barvu na støednì tmavì èervenou a do horní èásti okna vypí¹eme slova Renderer (jméno grafické karty), Vendor (její výrobce) a Version (verze). Dùvod, proè nejsou v¹echny umístìny 50 pixelù od okraje na ose x, je ten, ¾e je nezarovnáváme doleva, ale doprava.</p>

<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src"></p>
<p class="src1">glColor3f(1.0f,0.5f,0.5f);<span class="kom">// Èervená barva</span></p>
<p class="src1">glPrint(50,16,1,&quot;Renderer&quot;);<span class="kom">// Výpis nadpisu pro grafickou kartu</span></p>
<p class="src1">glPrint(80,48,1,&quot;Vendor&quot;);<span class="kom">// Výpis nadpisu pro výrobce</span></p>
<p class="src1">glPrint(66,80,1,&quot;Version&quot;);<span class="kom">// Výpis nadpisu pro verzi</span></p>

<p>Zmìníme èervenou barvu na oran¾ovou a nagrabujeme informace z grafické karty. Pou¾ijeme funkci glGetString(), která vrátí po¾adované øetìzce. Kvùli glPrint() pøetypujeme výstup funkce na char*. Výsledek vypí¹eme doprava od nadpisù.</p>

<p class="src1">glColor3f(1.0f,0.7f,0.4f);<span class="kom">// Oran¾ová barva</span></p>
<p class="src1">glPrint(200,16,1,(char *)glGetString(GL_RENDERER));<span class="kom">// Výpis typu grafické karty</span></p>
<p class="src1">glPrint(200,48,1,(char *)glGetString(GL_VENDOR));<span class="kom">// Výpis výrobce</span></p>
<p class="src1">glPrint(200,80,1,(char *)glGetString(GL_VERSION));<span class="kom">// Výpis verze</span></p>

<p>Definujeme modrou barvu a dolù na scénu vypí¹eme NeHe Productions.</p>

<p class="src1">glColor3f(0.5f,0.5f,1.0f);<span class="kom">// Modrá barva</span></p>
<p class="src1">glPrint(192,432,1,&quot;NeHe Productions&quot;);<span class="kom">// Výpis NeHe Productions</span></p>

<p>Kolem právì vypsaného textu vykreslíme bílý rámeèek. Resetujeme matici, proto¾e v glPrint() se volají funkce, které ji mìní. Potom definujeme bílou barvu.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glColor3f(1.0f,1.0f,1.0f);<span class="kom">// Bílá barva</span></p>

<p>Vykreslování linek pomocí GL_LINE_STRIP je velmi jednoduché. První bod definujeme úplnì vpravo, 63 pixelù (480-417=63) nad spodním okrajem okna. Druhý vertex umístíme ve stejné vý¹ce, ale vlevo. OpenGL je spojí pøímkou. Tøetí bod posuneme dolù do levého dolního rohu. OpenGL opìt zobrazí linku, tentokrát mezi druhým a tøetím bodem. Ètvrtý bod patøí do pravého dolního rohu a k pátému projedeme výchozím vertexem nahoru. Ukonèíme triangle strip, abychom mohli zaèít vykreslovat z nové pozice a stejným zpùsobem vykreslíme druhou èást rámeèku, ale tentokrát nahoøe.</p>

<p>Asi jste pochopili, ¾e pokud vykreslujeme více na sebe navazujících pøímek, tak LINE_STRIP u¹etøí spoustu zbyteèného kódu, který vzniká opakovaným definováním vertexù pøi obyèejném GL_LINES.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_24_line_strip.gif" width="150" height="100" alt="Poøadí zadávání bodù" /></div>

<p class="src1">glBegin(GL_LINE_STRIP);<span class="kom">// Zaèátek kreslení linek</span></p>
<p class="src2">glVertex2d(639,417);<span class="kom">// 1</span></p>
<p class="src2">glVertex2d(0,417);<span class="kom">// 2</span></p>
<p class="src2">glVertex2d(0,480);<span class="kom">// 3</span></p>
<p class="src2">glVertex2d(639,480);<span class="kom">// 4</span></p>
<p class="src2">glVertex2d(639,128);<span class="kom">// 5</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_LINE_STRIP);<span class="kom">// Zaèátek kreslení linek</span></p>
<p class="src2">glVertex2d(0,128);<span class="kom">// 6</span></p>
<p class="src2">glVertex2d(639,128);<span class="kom">// 7</span></p>
<p class="src2">glVertex2d(639,1);<span class="kom">// 8</span></p>
<p class="src2">glVertex2d(0,1);<span class="kom">// 9</span></p>
<p class="src2">glVertex2d(0,417);<span class="kom">// 10</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení</span></p>

<p>Nám neznámá funkce glScissor(x, y, v, ¹) vytváøí nìco, co by se dalo popsat jako okno. Pokud zapneme GL_SCISSOR_TEST, bude se oøezávat okolí této èásti obrazovky, tudí¾ se objekty budou moci vykreslovat pouze uvnitø definovaného obdélníku. Urèíme ho parametry pøedanými funkci. V na¹em pøípadì je to první pixel na ose x ve vý¹ce 13,5% (0,135...f) od spodního okraje. dále bude 638 pixelù ¹iroký (swidth-2) a 59,7% (0,597...f) vý¹ky okna vysoký. Druhým øádkem povolíme oøezávací testy. Mù¾ete se pokusit vykreslit obrovský obdélník pøes celé okno, ale uvidíte pouze èást v neoøezané oblasti. zbytek dosud nakreslené scény zùstane nezmìnìn. Perfektní pøíkaz!</p>

<p class="src1">glScissor(1, int(0.135416f*sheight), swidth-2, int(0.597916f*sheight));<span class="kom">// Definování oøezávací oblasti</span></p>
<p class="src1">glEnable(GL_SCISSOR_TEST);<span class="kom">// Povolí oøezávací testy</span></p>

<p>Na øadu pøichází asi nejtì¾¹í èást této lekce - vypsání podporovaných OpenGL roz¹íøení. V první fázi je musíme získat. Pomocí funkce malloc() alokujeme buffer pro øetìzec znakù text. Pøedává se jí velikost po¾adované pamìti. Strlen() spoèítá poèet znakù øetìzce vráceného glGetString(GL_EXTENSIONS). Pøièteme k nìmu je¹tì jeden znak pro '\0', který uzavírá ka¾dý c-éèkovský øetìzec. Strcpy() zkopíruje øetìzec podporovaných roz¹íøení do promìnné text.</p>

<p class="src1">char* text = (char *)malloc(strlen((char *)glGetString(GL_EXTENSIONS))+1);<span class="kom">// Alokace pamìti pro øetìzec</span></p>
<p class="src1">strcpy(text,(char *)glGetString(GL_EXTENSIONS));<span class="kom">// Zkopíruje seznam roz¹íøení do text</span></p>

<p>Nyní jsme do text nagrabovali z grafické karty øetìzec, který vypadá nìjak takto: &quot;GL_ARB_multitexture GL_EXT_abgr GL_EXT_bgra&quot;. Pomocí strtok() z nìj vyjmeme v poøadí první roz¹íøení. Funkce pracuje tak, ¾e prochází øetìzec a v pøípadì, ¾e najde mezeru zkopíruje pøíslu¹nou èást z text do token. První hodnota token tedy bude &quot;GL_ARB_multitexture&quot;. Zároveò se v¹ak zmìní i text. První mezera se nahradí oddìlovaèem. Více dále.</p>

<p class="src1">token = strtok(text, &quot; &quot;);<span class="kom">// Získá první podøetìzec</span></p>

<p>Vytvoøíme cyklus, který se zastaví tehdy, kdy¾ v token nezbudou u¾ ¾ádné dal¹í informace - bude se rovnat NULL. Ka¾dým prùchodem inkrementujeme èítaè a zkontrolujeme, jestli je jeho hodnota vìt¹í ne¾ maxtokens. Touto cestou velice snadno získáme maximální hodnotu v èítaèi, kterou vyu¾ijeme pøi rolování po stisku kláves.</p>

<p class="src1">while(token != NULL)<span class="kom">// Prochází podporovaná roz¹íøení</span></p>
<p class="src1">{</p>
<p class="src2">cnt++;<span class="kom">// Inkrementuje èítaè</span></p>
<p class="src"></p>
<p class="src2">if (cnt &gt; maxtokens)<span class="kom">// Je maximum men¹í ne¾ hodnota èítaèe?</span></p>
<p class="src2">{</p>
<p class="src3">maxtokens = cnt;<span class="kom">// Aktualizace maxima</span></p>
<p class="src2">}</p>

<p>V této chvíli máme v token ulo¾ené první roz¹íøení. Jeho poøadové èíslo napí¹eme zelenì do levé èásti okna. V¹imnìte si, ¾e ho na ose x napí¹eme na souøadnici 0. Tím bychom mohli zlikvidovat levý (bílý) rámeèek, který jsme u¾ vykreslili, ale proto¾e máme zapnuté oøezávání, pixely na nule nebudou modifikovány. Na ose y zaèínáme kreslit na 96. Abychom nevykreslovali v¹echno na sebe, pøièítáme poøadí násobené vý¹kou textu (cnt*32). Pøi vypisování prvního roz¹íøení se cnt==1 a text se nakreslí na 96+(32*1)=128. U druhého je výsledkem 160. Také odeèítáme scroll. Implicitnì se rovná nule, ale po stisku ¹ipek se jeho hodnota mìní. Umo¾níme tím rolování oøezaného okna, do kterého se vejde celkem devìt øádek (vý¹ka okna/vý¹ka textu = 288/32 = 9). Zmìnou scrollu mù¾eme zmìnit offset textu a tím ho posunout nahoru nebo dolù. Efekt je podobný filmovému projektoru. Film roluje tak, aby v jednom okam¾iku byl vidìt v¾dy jen jeden frame. Nemù¾ete vidìt oblast nad nebo pod ním i kdy¾ máte vìt¹í plátno. Objektiv sehrává stejnou roli jako oøezávací testy.</p>

<p class="src2">glColor3f(0.5f,1.0f,0.5f);<span class="kom">// Zelená barva</span></p>
<p class="src2">glPrint(0, 96+(cnt*32)-scroll, 0, &quot;%i&quot;, cnt);<span class="kom">// Poøadí aktuálního roz¹íøení</span></p>

<p>Po vykreslení poøadového èísla zamìníme zelenou barvu za ¾lutou a koneènì vypí¹eme text ulo¾ený v promìnné token. Vlevo se zaène na padesátém pixelu.</p>

<p class="src2">glColor3f(1.0f,1.0f,0.5f);<span class="kom">// ®lutá barva</span></p>
<p class="src2">glPrint(50,96+(cnt*32)-scroll,0,token);<span class="kom">// Vypí¹e jedno roz¹íøení</span></p>

<p>Po zobrazení prvního roz¹íøení potøebujeme pøipravit pùdu pro dal¹í prùchod cyklem. Nejprve zjistíme, jestli je v text je¹tì nìjaké dal¹í roz¹íøení. Namísto opìtovného volání token = strtok(text, &quot; &quot;), napí¹eme token = strtok(NULL, &quot; &quot;); NULL urèuje, ¾e se má hledat DAL©Í podøetìzec a ne v¹echno provádìt od znova. V na¹em pøíkladì jsem vý¹e napsal, ¾e se mezera nahradí oddìlovaèem - &quot;GL_ARB_multitextureoddìlovaèGL_EXT_abgr GL_EXT_bgra&quot;. Najdeme tedy oddìlovaè a a¾ od nìj se bude hledat dal¹í mezera. Poté se do token zkopíruje podøetìzec mezi oddìlovaèem a mezerou (GL_EXT_abgr) a text bude modifikován na &quot;GL_ARB_multitextureoddìlovaèGL_EXT_abgroddìlovaèGL_EXT_bgra&quot;. Po dosa¾ení konce textu se token nastaví na NULL a cyklus se ukonèí.</p>

<p class="src2">token = strtok(NULL, &quot; &quot;);<span class="kom">// Najde dal¹í roz¹íøení</span></p>
<p class="src1">}</p>

<p>Tím jsme ukonèili vykreslování, ale je¹tì nám zbývá po sobì uklidit. Vypneme oøezávací testy a uvolníme dynamickou pamì» - informace získané pomocí glGetString(GL_EXTENSIONS) ulo¾ené v RAM. Pøí¹tì a¾ budeme volat DrawGLScene() se pamì» opìt alokuje a provedou se znovu v¹echny rozbory øetìzcù.</p>

<p class="src1">glDisable(GL_SCISSOR_TEST);<span class="kom">// Vypne oøezávací testy</span></p>
<p class="src"></p>
<p class="src1">free(text);<span class="kom">// Uvolní dynamickou pamì»</span></p>

<p>Pøíkaz glFlush() není bezpodmíneènì nutný, ale myslím, ¾e je dobrý nápad se o nìm zmínit. Nejjednodu¹¹í vysvìtlení je takové, ¾e oznámí OpenGL, aby dokonèilo, co právì dìlá (nìkteré grafické karty napø. pou¾ívají vyrovnávací pamìti, jejich¾ obsah se tímto po¹le na výstup). Pokud si nìkdy v¹imnete mihotání nebo blikání polygonù, zkuste pøidat na konec v¹eho vykreslování volání glFlush(). Vyprázdní renderovací pipeline a tím zamezí mihotání, které vzniká tehdy, kdy¾ program nemá dostatek èasu, aby dokonèil rendering.</p>

<p class="src1">glFlush();<span class="kom">// Vyprázdní renderovací pipeline</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹echno v poøádku</span></p>
<p class="src0">}</p>

<p>Na konec KillGLWindow() pøidáme volání KillFont, které sma¾e display listy fontu.</p>

<p class="src0"><span class="kom">// Konec KillGLWindow()</span></p>
<p class="src1">KillFont();<span class="kom">// Sma¾e font</span></p>
<p class="src0">}</p>

<p>V programu testujeme stisk ¹ipky nahoru a dolù. V obou pøípadech pøièteme nebo odeèteme od scroll dvojku, ale pouze tehdy, pokud bychom nerolovali mimo okno. U ¹ipky nahoru je situace jednoduchá - nula je v¾dy nejni¾¹í mo¾né rolování. Maximum u ¹ipky dolù získáme násobením vý¹ky øádku a poètu roz¹íøení. Devítku odeèítáme, proto¾e se v jednom okam¾iku vejde na scénu devìt øádkù.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if (keys[VK_UP] &amp;&amp; (scroll &gt; 0))<span class="kom">// ©ipka nahoru?</span></p>
<p class="src4">{</p>
<p class="src5">scroll -= 2;<span class="kom">// Posune text nahoru</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN] &amp;&amp; (scroll &lt; 32*(maxtokens-9)))<span class="kom">// ©ipka dolù?</span></p>
<p class="src4">{</p>
<p class="src5">scroll += 2;<span class="kom">// Posune text dolù</span></p>
<p class="src4">}</p>

<p>Doufám, ¾e byl pro vás tento tutoriál zajímavý. Ji¾ víte, jak získat informace o výrobci, jménu a verzi grafické karty a také, která OpenGL roz¹íøení podporuje. Mìli byste vìdìt, jak pou¾ít oøezávací testy a neménì dùle¾itou vìcí je nahrávání TGA místo bitmapových obrázkù a jejich konverze na textury.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson24.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson24_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson24.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson24.zip">Delphi</a> kód této lekce. ( <a href="mailto:mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson24.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson24.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson24.jar">JoGL</a> kód této lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson24.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson24.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:jay@remotepoint.com">Jay Groven</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson24.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:scarab@egyptian.net">DarkAlloy</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson24.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson24.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(24);?>
<?FceNeHeOkolniLekce(24);?>

<?
include 'p_end.php';
?>
