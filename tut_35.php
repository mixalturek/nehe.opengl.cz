<?
$g_title = 'CZ NeHe OpenGL - Lekce 35 - Pøehrávání videa ve formátu AVI';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(35);?>

<h1>Lekce 35 - Pøehrávání videa ve formátu AVI</h1>

<p class="nadpis_clanku">Pøehrávání AVI videa v OpenGL? Na pozadí, povrchu krychle, koule, èi válce, ve fullscreenu nebo v obyèejném oknì. Co víc si pøát...</p>

<p>Na zaèátku bych chtìl podìkovat Fredsterovi za AVI animaci, Maxwellu Saylesovi za rady pøi programování, Jonathanu Nixovi a Johnu F. MCGowanovi, Ph. D. za skvìlé èlánky/dokumenty o AVI formátu. Moc jste mi pomohli.</p>

<p>Musím øíci, ¾e jsem na tento tutoriál opravdu py¹ný. kdy¾ mì Jonathan F. Blok pøivedl na nápad AVI pøehrávaèe v OpenGL, nemìl jsem nejmen¹í potuchu, jak AVI otevøít, nato¾ jak by mohl video pøehrávaè fungovat. Zaèal jsem listováním ve svých knihách o programování - vùbec nic. Poté jsem zkusil MSDN. Na¹el jsem spoustu u¾iteèných informací, ale bylo jich potøeba mnohem, mnohem více. Po hodinách prolézání internetu jsem mìl poznamenány pouze dva weby. Nemohu øíct, ¾e moje vyhledávací postupy jsou úplnì nejlep¹í, ale v cca. 99,9% pøípadù jsem nikdy nemìl nejmen¹í problémy. Byl jsem absolutnì ¹okován, kdy¾ jsem zjistil, jak málo pøíkladù na pøehrávání videa tam bylo. Vìt¹ina z nich navíc ne¹la zkompilovat, nìkteré byly komplexní (alespoò pro mì) a plnily svùj úèel, nicménì byly programovány ve VB, Delphi nebo podobnì (ne VC++).</p>

<p>První z u¾iteèných stránek, které jsem na¹el, byl èlánek od Janathana Nixe nadepsaný <?OdkazBlank('http://www.gamedev.net/reference/programming/features/avifile/', 'AVI soubory');?>. Jonathan má u mì obrovský respekt za tak extrémnì brilantní dokument. Aèkoli jsem se rozhodl jít jinou cestou ne¾ on, vnesl mì do problematiky. Druhý web, tentokrát od Johna F. MCGowana, Ph. D., má titulek The AVI Overview. Mohl bych teï zaèít popisovat, jak ú¾asné jsou Johnovi stránky, ale snadnìj¹í bude, kdy¾ se <?OdkazBlank('http://www.jmcgowan.com/avi.html', 'sami podíváte');?>. Soustøedil na nich snad v¹e, co je o AVI známo.</p>

<p>Poslední vìcí, na kterou chci upozornit, je, ¾e ¾ádná èást z celého kódu NEBYLA vypùjèena a nic nebylo okopírováno. Kódování mi zabralo plné tøi dny, pou¾íval jsem pouze informace z vý¹e uvedených zdrojù. Zároveò cítím, ¾e by bylo vhodné poznamenat, ¾e mùj kód nemusí být nejlep¹ím zpùsobem pro pøehrávání AVI souborù. Dokonce nemusí být ani vhodnou cestou, ale funguje a snadno se pou¾ívá. Nicménì pokud se vám mùj styl a kód nelíbí, nebo cítíte-li, ¾e uvolnìním tohoto tutoriálu dokonce zraòuji programátorskou komunitu, máte nìkolik mo¾ností: 1) zkuste si na internetu najít jiné zdroje, 2) napi¹te si svùj vlastní AVI pøehrávaè nebo 3) napi¹te lep¹í tutoriál. Ka¾dý, kdo nav¹tíví tento web, by mìl vìdìt, ¾e kóduji pro zábavu. Hlavním úèelem tìchto stránek je ulehèit ¾ivot ne-elitním programátorùm, kteøí zaèínají s OpenGL. Tutoriály ukazují, jak jsem !já! dokázal vytvoøit specifický efekt... nic více, nic ménì.</p>

<p>Pojïme ale ke kódu. Jako první vìc vlo¾íme a pøilinkujeme knihovnu Video For Windows. Obrovské díky Microsoft&reg;u (Nikdy bych nevìøil, ¾e to øeknu). Pomocí této knihovny bude otevírání a pøehrávání AVI pouhou banalitou.</p>

<p class="src0">#include &lt;vfw.h&gt;<span class="kom">// Hlavièkový soubor knihovny Video pro Windows</span></p>
<p class="src0">#pragma comment(lib, &quot;vfw32.lib&quot;)<span class="kom">// Pøilinkování VFW32.lib</span></p>

<p>Deklarujeme promìnné. Angle je úhel natoèení zobrazovaného objektu. Next pøedstavuje celé èíslo, které pou¾ijeme pro spoèítání mno¾ství uplynulého èasu (v milisekundách), abychom mohli udr¾et framerate na správné hodnotì. Více o tomto dále. Frame bude samozøejmì obsahovat èíslo aktuálnì zobrazovaného snímku animace. Effect pøedstavuje druh objektu na obrazovce (krychle, koule, válec, ¾ádný). Bude-li env rovno true, budou se automaticky generovat texturové souøadnice. Bg pøedstavuje flag, který definuje, jestli se má pozadí zobrazovat nebo ne. Sp, ep a bp slou¾í pro o¹etøení del¹ího stisku kláves.</p>

<p class="src0">float angle;<span class="kom">// Úhel rotace objektu</span></p>
<p class="src0">int next;<span class="kom">// Pro animaci</span></p>
<p class="src0">int frame = 0;<span class="kom">// Aktuální snímek videa</span></p>
<p class="src0">int effect;<span class="kom">// Zobrazený objekt</span></p>
<p class="src"></p>
<p class="src0">bool env = TRUE;<span class="kom">// Automaticky generovat texturové koordináty?</span></p>
<p class="src0">bool bg = TRUE;<span class="kom">// Zobrazovat pozadí?</span></p>
<p class="src"></p>
<p class="src0">bool sp;<span class="kom">// Stisknut mezerník?</span></p>
<p class="src0">bool ep;<span class="kom">// Stisknuto E?</span></p>
<p class="src0">bool bp;<span class="kom">// Stisknuto B?</span></p>

<p>Struktura psi bude udr¾ovat informace o AVI souboru. Pavi pøedstavuje ukazatel na buffer, do kterého po otevøení AVI obdr¾íme handle nového proudu. Pgf, pointer na objekt GetFrame, pou¾ijeme pro získávání jednotlivých snímkù, které pomocí bmih zkonvertujeme do formátu, který potøebujeme pro vytvoøení textury. Lastframe ukládá èíslo posledního snímku animace. Width a height definují rozmìry AVI proudu, pdata je ukazatel na data obrázku vrácené po po¾adavku na snímek. Mpf (Miliseconds Per Frame) pou¾ijeme pro výpoèet doby zobrazení snímku. Pøedpokládám, ¾e nemáte nejmen¹í ponìtí, k èemu v¹echny tyto promìnné vlastnì slou¾í... v¹e byste mìli pochopit dále.</p>

<p class="src0">AVISTREAMINFO psi;<span class="kom">// Informace o datovém proudu videa</span></p>
<p class="src0">PAVISTREAM pavi;<span class="kom">// Handle proudu</span></p>
<p class="src0">PGETFRAME pgf;<span class="kom">// Ukazatel na objekt GetFrame</span></p>
<p class="src0">BITMAPINFOHEADER bmih;<span class="kom">// Hlavièka pro DrawDibDraw dekódování</span></p>
<p class="src"></p>
<p class="src0">long lastframe;<span class="kom">// Poslední snímek proudu</span></p>
<p class="src0">int width;<span class="kom">// ©íøka videa</span></p>
<p class="src0">int height;<span class="kom">// Vý¹ka videa</span></p>
<p class="src0">char* pdata;<span class="kom">// Ukazatel na data textury</span></p>
<p class="src0">int mpf;<span class="kom">// Doba zobrazení jednoho snímku (Milliseconds Per Frame)</span></p>

<p>Pomocí knihovny GLU budeme moci vykreslit dva quadratic útvary, kouli a válec. Hdd je handle na DIB (Device Independent Bitmap) a hdc je handle na kontext zaøízení. HBitmap pøedstavuje handle na bitmapu závislou na zaøízení (DDB - Device Dependent Bitmap), pou¾ijeme ji dále pøi konverzích. Data je pointer, který bude ukazovat na data obrázku pou¾itelná pro vytvoøení textury. Opìt - více pochopíte dále.</p>

<p class="src0">GLUquadricObj *quadratic;<span class="kom">// Objekt quadraticu</span></p>
<p class="src"></p>
<p class="src0">HDRAWDIB hdd;<span class="kom">// Handle DIBu</span></p>
<p class="src0">HBITMAP hBitmap;<span class="kom">// Handle bitmapy závislé na zaøízení</span></p>
<p class="src0">HDC hdc = CreateCompatibleDC(0);<span class="kom">// Kontext zaøízení</span></p>
<p class="src0">unsigned char* data = 0;<span class="kom">// Ukazatel na bitmapu o zmìnìné velikosti</span></p>

<p>Nyní malý úvod do jazyka Assembler (ASM). Pokud jste ho je¹tì nikdy døíve nepou¾ili, nelekejte se. Mù¾e vypadat slo¾itì, ale v¹e je velmi jednoduché. Pøi programování tohoto tutoriálu jsem se dostal pøed velký problém. Aplikace bì¾ela v poøádku, ale barvy byly divné. V¹e, co mìlo být èervené bylo modré, a v¹e co mìlo být modré bylo èervené - klasické prohození R a B slo¾ky pixelù. Byl jsem absolutnì ¹okovaný. Myslel jsem si, ¾e jsem v kódu udìlal nìjakou ¹ílenou chybu typu &quot;èárka sem, znaménko tam...&quot;. Po peèlivém prostudování v¹eho, co jsem do té doby napsal, jsem nebyl schopen bug najít. Zaèal jsem znovu proèítat MSDN. Proè byla èervená a modrá slo¾ka barvy prohozená?! V MSDN bylo pøece jasnì napsáno, ¾e 24 bitové bitmapy jsou ve formátu RGB!!! Po spoustì dal¹ího ètení jsem problém objevil. Ve Windows se RGB data ukládají pozpátku a RGB ulo¾ené pozpátku je pøeci BGR! Tak¾e si jednou pro v¾dy zapamatujte, ¾e v OpenGL RGB znamená RGB a ve Windows RGB znamená BGR - jak jednoduché.</p>

<p>Po stí¾nostech od fanou¹kù Microsoft&reg;u (Pøekl.: Ono nìco takového existuje?!): Rozhodl jsem se pøidat krátké vysvìtlení... Nepomlouvám Microsoft kvùli tomu, ¾e oznaèil BGR formát barvy za RGB. Jestli se mu pøevrácená zkratka líbí více, a» si ji pou¾ívá. Nicménì nalezení chyby mù¾e být pro cizího programátora velice frustrující (zvlá¹» kdy¾ ¾ádná neexistuje).</p>

<p>Blue pøidal: Má to co dìlat s konvencemi little endian a big endian. Intel a Intel kompatibilní systémy pou¾ívají little endian, u kterého se ménì významné byty ukládají døíve ne¾ více významné. Specifikaci OpenGL vytvoøila firma SGI (Silicon Graphic), její¾ systémy pravdìpodobnì pou¾ívají big endian, a tudí¾ OpenGL standardnì vy¾adují bitmapy ve formátu big endian.</p>

<p>Skvìlý! Tak¾e jsem vytvoøil pøehrávaè, který je absolutnì k nièemu (Pøekl.: v originále absolute crap - zkuste si toto slovo najít ve slovníku, já chci být slu¹ný :-). Prvním øe¹ením, které mì napadlo, bylo prohodit byty manuálnì pomocí cyklu for. Pracovalo to v poøádku, ale stra¹nì pomalu. Mìl jsem v¹eho po krk. Zkusil jsem modifikoval generování textury na GL_BGR_EXT místo GL_RGB. Obrovský nárùst rychlosti a barvy vypadají skvìle! Tak¾e jsem problém koneènì vyøe¹il... alespoò jsem si to myslel. Nìkteré OpenGL ovladaèe mají s GL_BGR_EXT problémy :-( Maxwell Sayles mi doporuèil prohození bytù pomocí ASM. O minutku pozdìji mi ICQ-oval kód uvedený ní¾e, který je rychlý a plní dokonale svou funkci.</p>

<p>Ka¾dý snímek animace se ukládá do bufferu, obrázek má v¾dy ètvercovou velikost 256 pixelù a 3 barevné slo¾ky ve formátu BGR (speciálnì pro Billa Gatese: RGB). Funkce flipIt() prochází tento buffer po tøí bytových krocích a zamìòuje èervenou slo¾ku za modrou. R má být ulo¾eno na pozici abx+0 a B na abx+2. Cyklus se opakuje tak dlouho, dokud nejsou v¹echny pixely ve formátu RGB.</p>

<p>Pøedpokládám, ¾e vìt¹ina z vás není z ASM moc nad¹ená. Jak u¾ jsem psal, pùvodnì jsem plánoval pou¾ít GL_BGR_EXT. Funguje, ale ne na v¹ech kartách. Potom jsem se rozhodl jít cestou minulých tutoriálù a swapovat byty pomocí bitových operací XOR, které pracují na v¹ech poèítaèích, ale ne extrémnì rychle. Dokud jsme nepracovali s real-time videem, staèily, ale tentokrát potøebujeme co mo¾ná nejrychlej¹í metodu. Zvá¾íme-li v¹echny mo¾nosti, je ASM podle mého názoru nejlep¹í volbou. Pokud máte je¹tì lep¹í zpùsob, prosím... POU®IJTE HO! Neøíkám vám, jak co MÁTE dìlat, já pouze ukazuji, jak jsem problémy vyøe¹il já. V¹e proto také vysvìtluji do detailù, abyste mùj kód, pokud znáte lep¹í, mohli nahradit.</p>

<p class="src0">void flipIt(void* buffer)<span class="kom">// Prohodí èervenou a modrou slo¾ku pixelù v obrázku</span></p>
<p class="src0">{</p>
<p class="src1">void* b = buffer;<span class="kom">// Ukazatel na buffer</span></p>
<p class="src"></p>
<p class="src1">__asm <span class="kom">// ASM kód</span></p>
<p class="src1">{</p>
<p class="src3">mov ecx, 256*256 <span class="kom">// Øídící &quot;promìnná&quot; cyklu</span></p>
<p class="src3">mov ebx, b <span class="kom">// Ebx ukazuje na data</span></p>
<p class="src"></p>
<p class="src2">label: <span class="kom">// Návì¹tí pro cyklus</span></p>
<p class="src3">mov al, [ebx+0] <span class="kom">// Pøesune B slo¾ku do al</span></p>
<p class="src3">mov ah, [ebx+2] <span class="kom">// Pøesune R slo¾ku do ah</span></p>
<p class="src3">mov [ebx+2], al <span class="kom">// Vlo¾í B na správnou pozici</span></p>
<p class="src3">mov [ebx+0], ah <span class="kom">// Vlo¾í R na správnou pozici</span></p>
<p class="src"></p>
<p class="src3">add ebx, 3 <span class="kom">// Pøesun na dal¹í tøi byty</span></p>
<p class="src3">dec ecx <span class="kom">// Dekrementuje èítaè</span></p>
<p class="src3">jnz label <span class="kom">// Pokud se èítaè nerovná nule skok na návì¹tí</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jak u¾ z názvu funkce OpenAVI() vyplývá, otevírá AVI soubor. Parametr szFile je øetìzec s diskovou cestou k souboru. Øetìzec title pou¾ijeme pro zobrazení informací o AVI do titulku okna.</p>

<p class="src0">void OpenAVI(LPCSTR szFile)<span class="kom">// Otevøe AVI soubor</span></p>
<p class="src0">{</p>
<p class="src1">TCHAR title[100];<span class="kom">// Pro vypsání textu do titulku okna</span></p>

<p>Abychom inicializovali knihovnu AVI file, zavoláme AVIFileInit(). Existuje mnoho zpùsobù, jak otevøít video soubor. Rozhodl jsem se pou¾ít AVIStreamOpenFromFile(), která otevøe jeden datový proud. Pavi pøedstavuje ukazatel na buffer, kam funkce vrací handle nového proudu, szFile oznaèuje diskovou cestu k souboru. Tøetí parametr urèuje typ proudu, který si pøejeme otevøít. V tomto projektu nás zajímá pouze video. Nula, dal¹í parametr, oznamuje, ¾e se má pou¾ít první výskyt proudu streamtypeVIDEO - v AVI jich mù¾e být více. OF_READ definuje, ¾e nám staèí otevøení pouze pro ètení a NULL na konci je ukazatel na tøídní identifikátor handleru (Pøekl.: class identifier of the handler). Abych byl upøímný nemám nejmen¹í pøedstavu, co to znamená, proto pomocí NULL nechávám knihovnu, aby vybrala za mì.</p>

<p>Nastanou-li pøi otevírání jakékoli problémy, zobrazí se u¾ivateli informaèní okno, nicménì ukonèení programu není implementováno. Pøidání nìjakého druhu chybových testù by pro vás nemìlo být moc tì¾ké, já jsem byl pøíli¹ líný.</p>

<p class="src1">AVIFileInit();<span class="kom">// Pøipraví knihovnu AVIFile na pou¾ití</span></p>
<p class="src"></p>
<p class="src1">if (AVIStreamOpenFromFile(&amp;pavi, szFile, streamtypeVIDEO, 0, OF_READ, NULL) != 0)<span class="kom">// Otevøe AVI proud</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Chybová zpráva</span></p>
<p class="src2">MessageBox (HWND_DESKTOP, &quot;Failed To Open The AVI Stream&quot;, &quot;Error&quot;, MB_OK | MB_ICONEXCLAMATION);</p>
<p class="src1">}</p>

<p>Pokud jsme se dostali a¾ sem, mù¾eme pøedpokládat, ¾e se soubor otevøel v poøádku a video proud byl lokalizován. U deklarace promìnných jsme vytvoøili objekt struktury AVISTREAMINFO a nazvali ho psi. Voláním funkce AVIStreamInfo() do nìj nagrabujeme rùzné informace o AVI, s jejich¾ pomocí spoèítáme ¹íøku a vý¹ku snímku v pixelech. Potom funkcí AVIStreamLength() získáme èíslo posledního snímku videa, které zároveò oznaèuje celkový poèet v¹ech snímkù.</p>

<p>Výpoèet framerate je snadný. Poèet snímkù za sekundu se rovná psi.dwRate dìleno psi.dwScale. Tato hodnota by mìla odpovídat èíslu, které lze získat kliknutím na AVI soubor a zvolením vlastností. Ptáte se, co to má co spoleèného s mpf (èas zobrazení jednoho snímku)? Kdy¾ jsem poprvé psal kód pro animaci, zkou¹el jsem pro zvolení správného snímku animace pou¾ít FPS. Dostal jsem se do problémù... v¹echna videa se pøehrávala pøíli¹ rychle. Proto jsem nahlédl do vlastností video souboru face2.avi. Je dlouhé 3,36 sekund, framerate èiní 29,974 FPS a má celkem 91 snímkù. Pokud vynásobíme 3,36 krát 29,976 dostaneme 100 snímkù - velmi nepøesné.</p>

<p>Proto jsem se rozhodl dìlat vìci trochu jinak. Namísto poètu snímkù za sekundu spoèítáme, jak dlouho by mìl být snímek zobrazen. Funkce AVIStreamSampleToTime() zkonvertuje pozici v animaci na èas v milisekundách, ne¾ se video dostane do této pozice. Získáme tedy èas posledního snímku, vydìlíme ho jeho pozicí (=poètem v¹ech snímkù) a výsledek vlo¾íme do promìnné mpf. Stejné hodnoty byste dosáhli nagrabováním mno¾ství èasu potøebného pro jeden snímek. Pøíkaz by vypadal takto: AVIStreamSampleToTime(pavi, 1). Oba zpùsoby jsou mo¾né. Dìkuji Albertu Chaulkovi za nápad.</p>

<p class="src1">AVIStreamInfo(pavi, &amp;psi, sizeof(psi));<span class="kom">// Naète informace o proudu</span></p>
<p class="src"></p>
<p class="src1">width = psi.rcFrame.right - psi.rcFrame.left;<span class="kom">// Výpoèet ¹íøky</span></p>
<p class="src1">height = psi.rcFrame.bottom - psi.rcFrame.top;<span class="kom">// Výpoèet vý¹ky</span></p>
<p class="src"></p>
<p class="src1">lastframe = AVIStreamLength(pavi);<span class="kom">// Poslední snímek proudu</span></p>
<p class="src1">mpf = AVIStreamSampleToTime(pavi, lastframe) / lastframe;<span class="kom">// Poèet milisekund na jeden snímek</span></p>

<p>OpenGL po¾aduje, aby rozmìry textury byly mocninou èísla 2, ale vìt¹ina videí mívá velikost 160x120, 320x240 nebo jiné nevhodné hodnoty. Pro konverzi na potøebné rozmìry pou¾ijeme Windows funkce pro práci s DIB obrázky. Jako první vìc specifikujeme hlavièku bitmapy a to tak, ¾e vyplníme BITMAPINFOHEADER promìnnou bmih. Nastavíme velikost struktury a biPlanes. Barevnou hloubku urèíme na 24 bitù (RGB), obrázek bude mít rozmìry 256x256 pixelù a nebude komprimovaný.</p>

<p class="src1">bmih.biSize = sizeof(BITMAPINFOHEADER);<span class="kom">// Velikost struktury</span></p>
<p class="src1">bmih.biPlanes = 1;<span class="kom">// BiPlanes</span></p>
<p class="src1">bmih.biBitCount = 24;<span class="kom">// Poèet bitù na pixel</span></p>
<p class="src1">bmih.biWidth = 256;<span class="kom">// ©íøka bitmapy</span></p>
<p class="src1">bmih.biHeight = 256;<span class="kom">// Vý¹ka bitmapy</span></p>
<p class="src1">bmih.biCompression = BI_RGB;<span class="kom">// RGB mód</span></p>

<p>Funkce CreateDibSection() vytvoøí obrázek DIB, do kterého budeme moci pøímo zapisovat. Pokud v¹e probìhne v poøádku mìl by hBitmap obsahovat novì vytvoøený obrázek. Hdc pøedstavuje handle kontextu zaøízení, druhý parametr je ukazatel na strukturu, kterou jsme právì inicializovali. Tøetí parametr specifikuje RGB typ dat. Do promìnné data se ulo¾í ukazatel na data vytvoøeného obrázku. Nastavíme-li pøedposlední parametr na NULL, funkce za nás sama alokuje pamì». Poslední parametr budeme jednodu¹e ignorovat. Pøíkaz SelectObject() zvolí obrázek do kontextu zaøízení.</p>

<p class="src1">hBitmap = CreateDIBSection(hdc, (BITMAPINFO*)(&amp;bmih), DIB_RGB_COLORS, (void**)(&amp;data), NULL, NULL);</p>
<p class="src1">SelectObject(hdc, hBitmap);<span class="kom">// Zvolí bitmapu do kontextu zaøízení</span></p>

<p>Pøedtím ne¾ budeme moci naèítat jednotlivé snímky, musíme pøipravit program na dekomprimaci videa. Zavoláme funkci AVIStreamGetFrameOpen() a pøedáme jí ukazatel na datový proud videa. Za druhý parametr se mù¾e pøedat struktura podobná té vý¹e, pomocí které lze specifikovat vrácený video formát. Bohu¾el jedinou vìcí, kterou lze ovlivnit je ¹íøka a vý¹ka obrázku. V MSDN se také uvádí, ¾e se mù¾e pøedat AVIGETFRAMEF_BESTDISPLAYFMT, který automaticky zvolí nejlep¹í formát zobrazení. Nicménì mùj kompilátor nemá pro tuto symbolickou konstantu ¾ádnou definici. Dopadne-li v¹e dobøe, získáme GETFRAME objekt potøebný pro ètení dat jednotlivých snímkù. Pøi problémech se zobrazí chybové okno.</p>

<p class="src1">pgf = AVIStreamGetFrameOpen(pavi, NULL);<span class="kom">// Vytvoøí PGETFRAME pou¾itím po¾adovaného módu</span></p>
<p class="src"></p>
<p class="src1">if (pgf == NULL)<span class="kom">// Neúspìch?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox (HWND_DESKTOP, &quot;Failed To Open The AVI Frame&quot;, &quot;Error&quot;, MB_OK | MB_ICONEXCLAMATION);</p>
<p class="src1">}</p>

<p>Jako tøe¹nièku na dortu zobrazíme do titulku okna ¹íøku, vý¹ku a poèet snímkù videa.</p>

<p class="src1"><span class="kom">// Informace o videu (¹íøka, vý¹ka, poèet snímkù)</span></p>
<p class="src1">wsprintf (title, &quot;NeHe's AVI Player: Width: %d, Height: %d, Frames: %d&quot;, width, height, lastframe);</p>
<p class="src1">SetWindowText(g_window-&gt;hWnd, title);<span class="kom">// Modifikace titulku okna</span></p>
<p class="src0">}</p>

<p>Otevírání AVI probìhlo bez problémù, následující funkce nagrabuje jeho jeden snímek, zkonvertuje ho do pou¾itelné formy (velikost, barevná hloubka RGB) a vytvoøí z nìj texturu. Promìnná lpbi bude ukládat informace o hlavièce bitmapy snímku. Pøíkaz na dal¹ím øádku plní hned nìkolik funkcí. Nagrabuje snímek specifikovaný pomocí frame a vyplní lpbi informacemi o hlavièce snímku. Pøeskoèením hlavièky (lpbi-&gt;biSize) a informací o barvách (lpbi-&gt;biClrUsed * sizeof(RGBQUAD)) získáme ukazatel na opravdová data obrázku.</p>

<p class="src0">void GrabAVIFrame(int frame)<span class="kom">// Grabuje po¾adovaný snímek z proudu</span></p>
<p class="src0">{</p>
<p class="src1">LPBITMAPINFOHEADER lpbi;<span class="kom">// Hlavièka bitmapy</span></p>
<p class="src"></p>
<p class="src1">lpbi = (LPBITMAPINFOHEADER)AVIStreamGetFrame(pgf, frame);<span class="kom">// Grabuje data z AVI proudu</span></p>
<p class="src1">pdata = (char *)lpbi + lpbi-&gt;biSize + lpbi-&gt;biClrUsed * sizeof(RGBQUAD);<span class="kom">// Ukazatel na data</span></p>

<p>Kvùli textuøe musíme zkonvertovat právì získaný obrázek na pou¾itelnou velikost a barevnou hloubku. Pomocí funkce DrawDibDraw() mù¾eme kreslit pøímo do na¹eho DIBu. Její první parametr je DrawDib DC, dal¹í parametr pøedstavuje handle na kontext zaøízení. Nuly definují levý horní a 256 pravý dolní roh výsledného obdélníku. Lpbi je ukazatel na hlavièku snímku, který jsme právì naèetli, a pdata ukazuje na data obrázku. Následuje levý horní a pravý dolní roh zdrojového obrázku (èili ¹íøka a vý¹ka snímku). Poslední parametr necháme na nule. Touto cestou mù¾eme zkonvertovat obrázek o jakékoli ¹íøce, vý¹ce a barevné hloubce na obrázek 256x256x24.</p>

<p class="src1"><span class="kom">// Konvertování obrázku na po¾adovaný formát</span></p>
<p class="src1">DrawDibDraw(hdd, hdc, 0, 0, 256, 256, lpbi, pdata, 0, 0, width, height, 0);</p>

<p>V souèasné chvíli u¾ v rukách dr¾íme data, ze kterých lze vygenerovat texturu. Nicménì její R a B slo¾ky jsou prohozeny. Proto zavoláme na¹i ASM funkce, která jednotlivé byty umístí na korektní pozice v obrázku.</p>

<p class="src1">flipIt(data);<span class="kom">// Prohodí R a B slo¾ku pixelù</span></p>

<p>Pùvodnì jsem texturu aktualizoval jejím smazáním a znovuvytvoøením. Nìkolik lidí mi nezávisle na sobì poradilo, abych zkusil pou¾ít glTexSubImage2D(). Uvádím citaci z OpenGL Red Book: &quot;Vytvoøení textury mù¾e být mnohem nároènìj¹í ne¾ modifikace u¾ existující. V OpenGL Release 1.1 pøibyly nové rutiny pro nahrazení v¹ech èástí textury za nové informace. Toto mù¾e být u¾iteèné pro programy, které napø. v real-timu snímají obrázky videa a vytváøejí z nich textury. Aplikace pak za bìhu vytvoøí pouze jednu texturu a pomocí glTexSubImage2D() bude postupnì nahrazovat její data za nové snímky videa.&quot;</p>

<p>Osobnì jsem nezaznamenal vìt¹í nárùst rychlosti, ale na pomalej¹ích kartách mù¾e být v¹e jinak. Parametry funkce jsou následující: typ výstupu, úroveò detailù pro mipmapping, x a y offset poèátku kopírované oblasti (0, 0 - levý dolní roh), ¹íøka a vý¹ka oblasti, RGB formát pixelù, typ dat a ukazatel na data.</p>

<p>Kevin Rogers pøidal: Chtìl bych poukázat na dal¹í dùle¾itou vlastnost glTexSubImage2d(). Nejen, ¾e je rychlej¹í na mnoha OpenGL implementacích, ale cílová oblast obrázku nemusí být nutnì mocninou èísla 2. Toto je pøedev¹ím u¾iteèné pro pøehrávání videa, jeho¾ rozli¹ení bývá mocninou dvojky opravdu zøídka (vìt¹inou 320x200). Dostáváme tak flexibilní mo¾nost pøehrávat video v jeho originální velikosti ne¾ jej slo¾itì mìnit, nìkdy i dvakrát (do textury, zpìt na obrazovku).</p>

<p>Není mo¾né aktualizovat texturu, pokud jste ji je¹tì nevytvoøili! My ji vytváøíme v kódu funkce Initialize(). Druhá dùle¾itá vìc spoèívá v tom, ¾e pokud vá¹ projekt obsahuje více ne¾ jednu texturu, musíte pøed aktualizací zvolit jako aktivní (glBindTexture()) tu správnou, proto¾e byste mohli pøepsat texturu, kterou nechcete.</p>


<p class="src1">glTexSubImage2D(GL_TEXTURE_2D, 0, 0, 0, 256, 256, GL_RGB, GL_UNSIGNED_BYTE, data);<span class="kom">// Aktualizace textury</span></p>
<p class="src0">}</p>

<p>Následující funkce je volána pøi ukonèování programu. Má za úkol smazat DrawDib DC a uvolnit alokované zdroje. Zavírá také GetFrame zdroj, odstraòuje souborový proud a ukonèuje práci s AVI souborem.</p>

<p class="src0">void CloseAVI(void)<span class="kom">// Zavøení AVI souboru</span></p>
<p class="src0">{</p>
<p class="src1">DeleteObject(hBitmap);<span class="kom">// Sma¾e bitmapu</span></p>
<p class="src1">DrawDibClose(hdd);<span class="kom">// Zavøe DIB</span></p>
<p class="src1">AVIStreamGetFrameClose(pgf);<span class="kom">// Dealokace GetFrame zdroje</span></p>
<p class="src1">AVIStreamRelease(pavi);<span class="kom">// Uvolnìní proudu</span></p>
<p class="src1">AVIFileExit();<span class="kom">// Uvolnìní souboru</span></p>
<p class="src0">}</p>

<p>Inicializace je hezky pøímoèará. nastavíme poèáteèní úhel na nulu a pomocí knihovny DrawDib nagrabujeme DC. Pokud se v¹e zdaøí, tak by se mìlo hdd stát handlem na novì vytvoøený kontext zaøízení. Dále urèíme èerné pozadí, zapneme hloubkové testování atd.</p>

<p class="src0">BOOL Initialize (GL_Window* window, Keys* keys)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;</p>
<p class="src1">g_keys = keys;</p>
<p class="src"></p>
<p class="src1">angle = 0.0f;<span class="kom">// Na poèátku nulový úhel</span></p>
<p class="src1">hdd = DrawDibOpen();<span class="kom">// Kontext zaøízení DIBu</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testù hloubky</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivní korekce</span></p>

<p>V dal¹í èásti kódu zapneme mapování 2D textur, nastavíme filtr GL_NEAREST a definujeme kulové mapování, které umo¾ní automatické generování texturových koordinátù. Pokud máte výkonný systém, zkuste pou¾ít lineární filtrování, bude vypadat lépe.</p>

<p class="src1">quadratic = gluNewQuadric();<span class="kom">// Vytvoøí objekt quadraticu</span></p>
<p class="src1">gluQuadricNormals(quadratic, GLU_SMOOTH);<span class="kom">// Normály</span></p>
<p class="src1">gluQuadricTexture(quadratic, GL_TRUE);<span class="kom">// Texturové koordináty</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturování</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER, GL_NEAREST);<span class="kom">// Filtry textur</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER, GL_NEAREST);</p>
<p class="src"></p>
<p class="src1">glTexGeni(GL_S, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);<span class="kom">// Automatické generování koordinátù</span></p>
<p class="src1">glTexGeni(GL_T, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);</p>

<p>Po obvyklé inicializaci otevøeme AVI soubor. Jistì jste si v¹imli, ¾e jsem se sna¾il udr¾et rozhraní v co nejjednodu¹¹í formì, tak¾e staèí pøedat pouze øetìzec se jménem souboru. Na konci vytvoøíme texturu a ukonèíme funkci.</p>

<p class="src1">OpenAVI(&quot;data/face2.avi&quot;);<span class="kom">// Otevøení AVI souboru</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vytvoøení textury</span></p>
<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, GL_RGB, 256, 256, 0, GL_RGB, GL_UNSIGNED_BYTE, data);</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹e OK</span></p>
<p class="src0">}</p>

<p>Pøi deinicializaci zavoláme CloseAVI(), èím¾ kompletnì ukonèíme práci s videem.</p>

<p class="src0">void Deinitialize(void)<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">CloseAVI();<span class="kom">// Zavøe AVI</span></p>
<p class="src0">}</p>

<p>Ve funkci Update() zji¹»ujeme pøípadné stisky kláves a v závislosti na uplynulém èase aktualizujeme pomìry ve scénì. Jako v¾dy ESC ukonèuje program a F1 pøepíná mód fullscreen/okno. Mezerníkem inkrementujeme promìnnou efekt, její¾ hodnota urèuje, jestli se ve scénì zobrazuje krychle, koule, válec, popø. nic (pouze pozadí).</p>

<p class="src0">void Update(DWORD milliseconds)<span class="kom">// Aktualizace scény</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown[VK_ESCAPE] == TRUE)<span class="kom">// ESC</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication (g_window);<span class="kom">// Konec programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_F1] == TRUE)<span class="kom">// F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen (g_window);<span class="kom">// Zamìní mód fullscreen/okno</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if ((g_keys-&gt;keyDown[' ']) &amp;&amp; !sp)<span class="kom">// Mezerník</span></p>
<p class="src1">{</p>
<p class="src2">sp = TRUE;</p>
<p class="src2">effect++;<span class="kom">// Následující objekt v øadì</span></p>
<p class="src"></p>
<p class="src2">if (effect &gt; 3)<span class="kom">// Pøeteèení?</span></p>
<p class="src2">{</p>
<p class="src3">effect = 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown[' '])<span class="kom">// Uvolnìní mezerníku</span></p>
<p class="src1">{</p>
<p class="src2">sp = FALSE;</p>
<p class="src1">}</p>

<p>Pomocí klávesy B zapínáme/vypínáme pozadí. Generování texturových koordinátù urèuje flag env, který negujeme po stisku klávesy E.</p>

<p class="src1">if ((g_keys-&gt;keyDown['B']) &amp;&amp; !bp)<span class="kom">// Klávesa B</span></p>
<p class="src1">{</p>
<p class="src2">bp = TRUE;</p>
<p class="src2">bg = !bg;<span class="kom">// Nastaví flag pro zobrazování pozadí</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown['B'])<span class="kom">// Uvolnìní B</span></p>
<p class="src1">{</p>
<p class="src2">bp = FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if ((g_keys-&gt;keyDown['E']) &amp;&amp; !ep)<span class="kom">// Klávesa E</span></p>
<p class="src1">{</p>
<p class="src2">ep = TRUE;</p>
<p class="src2">env = !env;<span class="kom">// Nastaví flag pro automatické generování texturových koordinátù</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown['E'])<span class="kom">// Uvolnìní E</span></p>
<p class="src1">{</p>
<p class="src2">ep = FALSE;</p>
<p class="src1">}</p>

<p>V závislosti na uplynulém èase zvìt¹íme úhel natoèení objektu.</p>

<p class="src1">angle += (float)(milliseconds) / 60.0f;<span class="kom">// Aktualizace úhlu natoèení</span></p>

<p>V originální verzi tutoriálu byla v¹echna videa pøehrávána v¾dy stejnou rychlostí a to nebylo pøíli¹ vhodné. Proto jsem kód pøepsal tak, aby jeho rychlost byla v¾dy korektní. Obsah promìnné next zvìt¹íme o poèet uplynulých milisekund od milého volání. Jistì si pamatujete, ¾e mpf obsahuje èas, jak dlouho má být ka¾dý snímek zobrazen. Vydìlíme-li tedy èíslo next hodnotou mpf, získáme správný snímek. Nakonec se ujistíme, ¾e novì vypoètený snímek nepøetekl pøes maximální hodnotu. V takovém pøípadì zaèneme video pøehrávat znovu od zaèátku.</p>

<p>Asi vás nepøekvapí, ¾e pokud je poèítaè pøíli¹ pomalý, nìkteré snímky se automaticky pøeskakují. Pokud chcete, aby byl ka¾dý snímek zobrazen, pøièem¾ nezávisí na tom, jak pomalu program bì¾í, mù¾ete otestovat, jestli je next vy¹¹í ne¾ mpf a pokud ano, inkrementujte snímek o jednièku a resetujte next zpìt na nulu. Oba zpùsoby pracují, ale pro rychlé poèítaèe je vhodnìj¹í uvedený kód.</p>

<p>Cítíte-li se plni síly a energie, zkuste implementovat obvyklé funkce video pøehrávaèù - napø. rychlé pøevíjení, pauzu nebo zpìtný chod.</p>

<p class="src1">next += milliseconds;<span class="kom">// Zvìt¹ení next o uplynulý èas</span></p>
<p class="src1">frame = next / mpf;<span class="kom">// Výpoèet aktuálního snímku</span></p>
<p class="src"></p>
<p class="src1">if (frame &gt;= lastframe)<span class="kom">// Pøeteèení snímkù?</span></p>
<p class="src1">{</p>
<p class="src2">frame = 0;<span class="kom">// Pøetoèí video na zaèátek</span></p>
<p class="src2">next = 0;<span class="kom">// Nulování èasu</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>U¾ máme témìø v¹e, zbývá pouze vykreslování scény. Jako v¾dy na zaèátku sma¾eme obrazovku a hloubkový buffer. Potom nagrabujeme po¾adovaný snímek animace. Pokud byste chtìli souèasnì pou¾ívat více videí, museli byste pøidat i ID textury - dal¹í práce pro vás.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e buffery</span></p>
<p class="src"></p>
<p class="src1">GrabAVIFrame(frame);<span class="kom">// Nagrabuje po¾adovaný snímek videa</span></p>

<p>Chceme-li kreslit pozadí, resetujeme modelview matici a na obyèejný obdélník namapujeme daný snímek videa. Aby se objevil a¾ za v¹emi objekty, umístíme ho dvacet jednotek do scény a samozøejmì ho roztáhneme na po¾adovanou velikost.</p>

<p class="src1">if (bg)<span class="kom">// Zobrazuje se pozadí?</span></p>
<p class="src1">{</p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);<span class="kom">// Vykreslování obdélníkù</span></p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 11.0f, 8.3f,-20.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-11.0f, 8.3f,-20.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-11.0f,-8.3f,-20.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 11.0f,-8.3f,-20.0f);</p>
<p class="src2">glEnd();</p>
<p class="src1">}</p>

<p>Resetujeme matici a pøesuneme se deset jednotek do scény. Pokud se env rovná TRUE, zapneme automatické generování texturových koordinátù.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -10.0f);<span class="kom">// Posun do scény</span></p>
<p class="src"></p>
<p class="src1">if (env)<span class="kom">// Zapnuto generování souøadnic textur?</span></p>
<p class="src1">{</p>
<p class="src2">glEnable(GL_TEXTURE_GEN_S);</p>
<p class="src2">glEnable(GL_TEXTURE_GEN_T);</p>
<p class="src1">}</p>

<p>Na poslední chvíli jsem pøidal i rotaci objektu na osách x, y a následné pøiblí¾ení na ose z. Objekt se bude pohybovat po scénì. Bez tìchto tøí øádkù by pouze rotoval na jednom místì uprostøed obrazovky.</p>

<p class="src1">glRotatef(angle*2.3f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src1">glRotatef(angle*1.8f, 0.0f, 1.0f, 0.0f);</p>
<p class="src1">glTranslatef(0.0f, 0.0f, 2.0f);<span class="kom">// Pøesun na novou pozici</span></p>

<p>Pomocí vìtvení do více smìrù vykreslíme objekt, který je právì aktivní. Jako první mo¾nost máme krychli.</p>

<p class="src1">switch (effect)<span class="kom">// Vìtvení podle efektu</span></p>
<p class="src1">{</p>
<p class="src2">case 0:<span class="kom">// Krychle</span></p>
<p class="src3">glRotatef(angle*1.3f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(angle*1.1f, 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glRotatef(angle*1.2f, 0.0f, 0.0f, 1.0f);</p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);<span class="kom">// Kreslení obdélníkù</span></p>
<p class="src4"><span class="kom">// Èelní stìna</span></p>
<p class="src4">glNormal3f(0.0f, 0.0f, 0.5f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src4"><span class="kom">// Zadní stìna</span></p>
<p class="src4">glNormal3f(0.0f, 0.0f,-0.5f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src4"><span class="kom">// Horní stìna</span></p>
<p class="src4">glNormal3f(0.0f, 0.5f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src4"><span class="kom">// Spodní stìna</span></p>
<p class="src4">glNormal3f(0.0f,-0.5f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src4"><span class="kom">// Pravá stìna</span></p>
<p class="src4">glNormal3f(0.5f, 0.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src4"><span class="kom">// Levá stìna</span></p>
<p class="src4">glNormal3f(-0.5f, 0.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src3">glEnd();</p>
<p class="src3">break;</p>

<p>Jak vykreslit kouli, u¾ jistì dávno víte, nicménì pro jistotu pøidávám krátký komentáø. Její polomìr èiní 1.3f jednotek, skládá se z dvaceti poledníkù a dvaceti rovnobì¾ek. Pou¾ívám èíslo 20, proto¾e chci, aby nebyla perfektnì hladká, ale trochu segmentovaná - bude vidìt náznak její rotace.</p>

<p class="src2">case 1:<span class="kom">// Koule</span></p>
<p class="src3">glRotatef(angle*1.3f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(angle*1.1f, 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glRotatef(angle*1.2f, 0.0f, 0.0f, 1.0f);</p>
<p class="src"></p>
<p class="src3">gluSphere(quadratic, 1.3f, 20, 20);<span class="kom">// Vykreslení koule</span></p>
<p class="src3">break;</p>

<p>Válec vykreslíme pomocí funkce gluCylinder(). Bude mít prùmìr 1.0f a jeho vý¹ka bude èinit tøi jednotky.</p>

<p class="src2">case 2:<span class="kom">// Válec</span></p>
<p class="src3">glRotatef(angle*1.3f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(angle*1.1f, 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glRotatef(angle*1.2f, 0.0f, 0.0f, 1.0f);</p>
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrování</span></p>
<p class="src"></p>
<p class="src3">gluCylinder(quadratic, 1.0f, 1.0f, 3.0f, 32, 32);<span class="kom">// Vykreslení válce</span></p>
<p class="src3">break;</p>
<p class="src1">}</p>

<p>Pokud je env v jednièce, vypneme generování texturových koordinátù.</p>

<p class="src1">if (env)<span class="kom">// Zapnuto generování souøadnic textur?</span></p>
<p class="src1">{</p>
<p class="src2">glDisable(GL_TEXTURE_GEN_S);</p>
<p class="src2">glDisable(GL_TEXTURE_GEN_T);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vyprázdní OpenGL pipeline</span></p>
<p class="src0">}</p>

<p>Doufám, ¾e jste si, stejnì jako já, u¾ili tento tutoriál. Za chvíli budou 2 hodiny ráno... u¾ na nìm pracuji pøes ¹est hodin. Zní to ¹ílenì, ale psaní textu, aby dával smysl, není lehký úkol. V¹e jsem tøikrát pøeèetl a sna¾il se objasnit vìci co nejlépe. Vìøte nebo ne, pro mì je dùle¾ité, abyste pochopili, jak vìci pracují a proè vùbec pracují. Bez ètenáøù bych brzy skonèil.</p>

<p>Jak u¾ jsem napsal, toto je mùj první pokus o pøehrávání videa. Normálnì nepí¹i o pøedmìtu, který jsem se právì nauèil, ale myslím, ¾e mi to pro jednou odpustíte. Faktem je, ¾e jsem si od cizích lidí pùjèil opravdu absolutní minimum kódu, v¹e je pùvodní. Doufám, ¾e se mi podaøilo otevøít dveøe povodni pøehrávání AVI ve va¹ich kvalitních demech. Mo¾ná se tak stane, mo¾ná ne. Ka¾dopádnì ukázkový tutoriál u¾ máte.</p>

<p>Obrovské díky patøí Fredsterovi, který vytvoøil ukázkové video tváøe. Byla to jedna z celkem ¹esti animací, které mi poslal. ®ádné dotazy, ¾ádné po¾adavky. Poslal jsem mu email s prosbou a on mi pomohl. Obrovský respekt.</p>

<p>Nejvìt¹í dík v¹ak patøí Jonathanu de Blok. Nebýt jeho, tento tutoriál by nevznikl. Právì on ve mnì vzbudil zájem o AVI formát. Poslal mi toti¾ èást kódu z jeho pøehrávaèe. Trpìlivì odpovídal na v¹echny otázky ohlednì jeho kódu. Nic jsem si v¹ak nepùjèil, mùj kód pracuje na úplnì jiném základu.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson35.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson35_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson35.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson35.zip">Dev C++</a> kód této lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson35.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:matthias.haack@epost.de">Matthias Haack</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson35.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(35);?>
<?FceNeHeOkolniLekce(35);?>

<?
include 'p_end.php';
?>
