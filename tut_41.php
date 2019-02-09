<?
$g_title = 'CZ NeHe OpenGL - Volumetrická mlha a nahrávání obrázkù pomocí IPicture';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(41);?>

<h1>Lekce 41 - Volumetrická mlha a nahrávání obrázkù pomocí IPicture</h1>

<p class="nadpis_clanku">V tomto tutoriálu se nauèíte, jak pomocí roz¹íøení EXT_fog_coord vytvoøit volumetrickou mlhu. Také zjistíte, jak pracuje IPicture kód a jak ho mù¾ete vyu¾ít pro nahrávání obrázkù ve svých vlastních projektech. Demo sice není a¾ tak komplexní jako nìkterá jiná, nicménì i pøesto vypadá hodnì efektnì.</p>

<p>Pokud demo nebude na va¹em systému fungovat, nejdøíve se ujistìte, ¾e máte nainstalované nejnovìj¹í ovladaèe grafické karty. Pokud to nepomohlo, zauva¾ujte o koupi nové (Pøekl.: :-] ). V souèasné dobì u¾ ne zrovna nejnovìj¹í GeForce 2 pracuje dobøe a ani nestojí tak moc. Pokud va¹e grafická karta nepodporuje roz¹íøení mlhy, kdo mù¾e vìdìt, jaká dal¹í roz¹íøení nebude podporovat?</p>

<p>Pro ty z vás, kterým toto demo nejede a cítí se vylouèeni... mìjte na pamìti následující: Snad ka¾dý den dostávám nejménì jeden email s dotazem na nový tutoriál. Nejhor¹í z toho je, ¾e vìt¹ina z nich u¾ je online. Lidé se neobtì¾ují èíst to, co u¾ je napsáno a pøeskakují na témata, která je více zajímají. Nìkteré tutoriály jsou pøíli¹ komplexní, a proto z mé strany vy¾adují nìkdy i týdny programování. Pak jsou tady tutoriály, které bych sice mohl napsat, ale vìt¹inou se jim vyhýbám, proto¾e nefungují na v¹ech kartách. Nyní jsou u¾ karty jako GeForce levné natolik, aby si je mohl dovolit témìø ka¾dý, tak¾e u¾ nebudu dále ospravedlòovat nepsání takovýchto tutoriálù. Popravdì, pokud va¹e karta podporuje pouze základní roz¹íøení, budete s nejvìt¹í pravdìpodobností chybìt! Pokud se vrátím k pøeskakování témat jako jsou napø. roz¹íøení, tutoriály se brzy oproti ostatním zaènou výraznì opo¾ïovat.</p>

<p>Kód zaèíná velmi podobnì jako starý základní kód a povìt¹inou je identický s novým NeHeGL kódem. Jediný rozdíl spoèívá v inkludování OLECTL hlavièkového souboru, který, chcete-li pou¾ívat IPicture pro loading obrázkù, musí být pøítomen.</p>

<p>Pøekl.: IPicture je podle mì sice hezký nápad a pracuje perfektnì, nicménì je kompletnì vystavìn na ABSOLUTNÌ NEPØENOSITELNÝCH technologiích MS, které jdou tradiènì pou¾ívat výhradnì pod nejmenovaným OS, v¹ichni víme, o který jde.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Windows</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// OpenGL</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// GLU</span></p>
<p class="src0">#include &lt;olectl.h&gt;<span class="kom">// Knihovna OLE Controls Library (pou¾ita pøi nahrávání obrázkù)</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Matematika</span></p>
<p class="src"></p>
<p class="src0">#include &quot;NeHeGL.h&quot;<span class="kom">// NeHeGL</span></p>
<p class="src"></p>
<p class="src0">#pragma comment(lib, &quot;opengl32.lib&quot;)<span class="kom">// Pøilinkování OpenGL a GLU</span></p>
<p class="src0">#pragma comment(lib, &quot;glu32.lib&quot;)</p>
<p class="src"></p>
<p class="src0">#ifndef CDS_FULLSCREEN<span class="kom">// Nìkteré kompilátory CDS_FULLSCREEN nedefinují</span></p>
<p class="src0">#define CDS_FULLSCREEN 4</p>
<p class="src0">#endif</p>
<p class="src"></p>
<p class="src0">GL_Window* g_window;<span class="kom">// Struktura okna</span></p>
<p class="src0">Keys* g_keys;<span class="kom">// Klávesnice</span></p>

<p>Deklarujeme ètyø prvkové pole fogColor, které bude ukládat barvu mlhy, v na¹em pøípadì se jedná o tmavì oran¾ovou (trocha èervené smíchaná se ¹petkou zelené). Desetinná hodnota camz bude slou¾it pro umístìní kamery na ose z. Pøed vykreslením v¾dy provedeme translaci.</p>

<p class="src0">GLfloat fogColor[4] = {0.6f, 0.3f, 0.0f, 1.0f};<span class="kom">// Barva mlhy</span></p>
<p class="src0">GLfloat camz;<span class="kom">// Pozice kamery na ose z</span></p>

<p>Ze souboru glext.h pøevezmeme symbolické konstanty GL_FOG_COORDINATE_SOURCE_EXT a GL_FOG_COORDINATE_EXT. Pokud chcete kód zkompilovat, musí být nastaveny.</p>

<p class="src0"><span class="kom">// Pøevzato z glext.h</span></p>
<p class="src0">#define GL_FOG_COORDINATE_SOURCE_EXT 0x8450<span class="kom">// Symbolické konstanty potøebné pro roz¹íøení FogCoordfEXT</span></p>
<p class="src0">#define GL_FOG_COORDINATE_EXT 0x8451</p>

<p>Abychom mohli pou¾ívat funkci glFogCoordfExt(), která bude vstupním bodem pro roz¹íøení, potøebujeme deklarovat její prototyp. Nejdøíve pomocí typedef vytvoøíme nový datový typ, ve kterém bude specifikován poèet a typ parametrù (jedno desetinné èíslo). Vytvoøíme globální promìnnou tohoto typu - ukazatel na funkci a prozatím ho nastavíme na NULL. Jakmile mu pøiøadíme pomocí wglGetProcAddress() adresu OpenGL ovladaèe roz¹íøení, budeme moci zavolat glFogCoordfEXT(), jako kdyby to byla normální funkce.</p>

<p> Tak¾e co u¾ máme... Víme, ¾e PFNGLFOGCOORDFEXTPROC pøebírá jednu desetinnou hodnotu (GLfloat coord). Proto¾e je promìnná glFogCoordfEXT stejného typu mù¾eme øíct, ¾e také potøebuje jednu desetinnou hodnotu... tedy glFogCoordfEXT(GLfloat coord). Funkci máme definovanou, ale zatím nic nedìlá, proto¾e glFogCoordfEXT se v tuto chvíli rovná NULL. Dále v kódu jí pøiøadíme adresu OpenGL ovladaèe pro roz¹íøení.</p>

<p>Doufám, ¾e to v¹echno dává smysl. Pokud jednou víte, jak tento kód pracuje, je velmi jednoduchý, ale jeho popsání je, alespoò pro mì, extrémnì slo¾ité.</p>

<p class="src0">typedef void (APIENTRY * PFNGLFOGCOORDFEXTPROC) (GLfloat coord);<span class="kom">// Funkèní prototyp</span></p>
<p class="src0">PFNGLFOGCOORDFEXTPROC glFogCoordfEXT = NULL;<span class="kom">// Ukazatel na funkci glFogCoordfEXT()</span></p>
<p class="src"></p>
<p class="src0">GLuint texture[1];<span class="kom">// Jedna textura</span></p>

<p>Pojïme se podívat na pøevod obrázkù do textury pomocí magické IPicture. Funkci se pøedává øetìzec se jménem obrázku a ID textury. Za jméno se mù¾e dosadit buï disková cesta nebo webové URL.</p>

<p>Pro pomocnou bitmapu budeme potøebovat kontext zaøízení (hdcTemp) a místo, kam by se dala ulo¾it (hbmpTemp). Ukazatel pPicture pøedstavuje rozhraní k IPicture. WszPath a szPath slou¾í k ulo¾ení absolutní cesty k souboru nebo URL. Dále potøebujeme dvì promìnné pro ¹íøku a dvì promìnné pro vý¹ku. LWidth a LHeight ukládají aktuální rozmìry obrázku, lWidthpixels a lHeightpixels obsahují ¹íøku a vý¹ku v pixelech upravenou podle maximální velikosti textury, která mù¾e být ulo¾ena do grafické karty. Hodnotu maximální velikosti ulo¾íme do glMaxTexdim.</p>

<p class="src0">int BuildTexture(char *szPathName, GLuint &amp;texid)<span class="kom">// Nahraje obrázek a konvertuje ho na texturu</span></p>
<p class="src0">{</p>
<p class="src1">HDC hdcTemp;<span class="kom">// Pomocný kontext zaøízení</span></p>
<p class="src1">HBITMAP hbmpTemp;<span class="kom">// Pomocná bitmapa</span></p>
<p class="src1">IPicture *pPicture;<span class="kom">// Rozhraní pro IPicture</span></p>
<p class="src1">OLECHAR wszPath[MAX_PATH+1];<span class="kom">// Absolutní cesta k obrázku (unicode)</span></p>
<p class="src1">char szPath[MAX_PATH+1];<span class="kom">// Absolutní cesta k obrázku (ascii)</span></p>
<p class="src1">long lWidth;<span class="kom">// ©íøka v logických jednotkách</span></p>
<p class="src1">long lHeight;<span class="kom">// Vý¹ka v logických jednotkách</span></p>
<p class="src1">long lWidthPixels;<span class="kom">// ©íøka v pixelech</span></p>
<p class="src1">long lHeightPixels;<span class="kom">// Vý¹ka v pixelech</span></p>
<p class="src1">GLint glMaxTexDim;<span class="kom">// Maximální rozmìr textury</span></p>

<p>V dal¹í èásti kódu zjistíme, zda je jméno obrázku diskovou cestou nebo URL. Jedná-li se o URL, zkopírujeme jméno do promìnné szPath. V opaèném pøípadì získáme pracovní adresáø a spojíme ho se jménem. Dìláme to, proto¾e potøebujeme plnou cestu k souboru. Pokud máme napø. demo ulo¾ené v adresáøi C:\WOW\LESSON41 a pokou¹íme se nahrát obrázek DATA\WALL.BMP. Uvedená konstrukce pøidá doprostøed je¹tì zpìtné lomítko a tak vznikne C:\WOW\LESSON41\DATA\WALL.BMP.</p>

<p class="src1">if (strstr(szPathName, &quot;http://&quot;))<span class="kom">// Obsahuje cesta øetìzec &quot;http://&quot;?</span></p>
<p class="src1">{</p>
<p class="src2">strcpy(szPath, szPathName);<span class="kom">// Zkopírování do szPath</span></p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Nahrávání ze souboru</span></p>
<p class="src1">{</p>
<p class="src2">GetCurrentDirectory(MAX_PATH, szPath);<span class="kom">// Pracovní adresáø</span></p>
<p class="src2">strcat(szPath, &quot;\\&quot;);<span class="kom">// Pøidá zpìtné lomítko</span></p>
<p class="src2">strcat(szPath, szPathName);<span class="kom">// Pøidá cestu k souboru</span></p>
<p class="src1">}</p>

<p>Aby funkce OleLoadPicturePath() rozumìla cestì k souboru, musíme ji pøevést z ASCII do kódování UNICODE (dvoubytové znaky). Pomù¾e nám s tím MultiByteToWideChar(). První parametr, CP_ACP, znamená Ansi Codepage, druhý specifikuje zacházení s nenamapovanými znaky (ignorujeme ho). SzPath je samozøejmì pøevádìný øetìzec a ètvrtý parametr pøedstavuje ¹íøku øetìzce s Unicode znaky. Pokud za nìj pøedáme -1, pøedpokládá se, ¾e bude ukonèen pomocí NULL. Do wszPath se ulo¾í výsledek, MAX_PATH je maximální velikostí cesty k souboru (256 znakù).</p>

<p>Po konverzi cesty do kódování Unicode se pokusíme pomocí OleLoadPicturePath nahrát obrázek. Pøi úspìchu bude pPicture obsahovat ukazatel na data obrázku, návratový kód se ulo¾í do hr.</p>

<p class="src1">MultiByteToWideChar(CP_ACP, 0, szPath, -1, wszPath, MAX_PATH);<span class="kom">// Konverze ascii kódování na Unicode</span></p>
<p class="src1">HRESULT hr = OleLoadPicturePath(wszPath, 0, 0, 0, IID_IPicture, (void**)&amp;pPicture);<span class="kom">// Loading obrázku</span></p>
<p class="src"></p>
<p class="src1">if(FAILED(hr))<span class="kom">// Neúspìch</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>

<p>Pokusíme se vytvoøit kompatibilní kontext zaøízení. Pokud se to nepovede uvolníme data obrázku a ukonèíme program.</p>

<p class="src1">hdcTemp = CreateCompatibleDC(GetDC(0));<span class="kom">// Pomocný kontext zaøízení</span></p>
<p class="src"></p>
<p class="src1">if(!hdcTemp)<span class="kom">// Neúspìch</span></p>
<p class="src1">{</p>
<p class="src2">pPicture-&gt;Release();<span class="kom">// Uvolní IPicture</span></p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>

<p>Pøi¹el èas na polo¾ení dotazu grafické kartì, jakou podporuje maximální velikost textury. Tato èást kódu je dùle¾itá, proto¾e díky ní bude obrázek vypadat dobøe na v¹ech grafických kartách. Nejen, ¾e umo¾ní upravit velikost na mocninou dvou, ale také ho pøizpùsobí podle velikosti pamìti grafické karty. Zkrátka: budeme moci nahrávat obrázky s libovolnou ¹íøkou a vý¹kou. Jediná nevýhoda pro majitele málo výkonných grafických karet spoèívá v tom, ¾e se pøi zobrazení obrázkù s vysokým rozli¹ením ztratí spousta detailù.</p>

<p>Funkce glGetIntegerv() vrátí maximální rozmìry textur (256, 512, 1024, atd.), potom zjistíme aktuální velikost na¹eho obrázku a pøevedeme ji na pixely. Matematiku zde nebudu vysvìtlovat.</p>

<p class="src1">glGetIntegerv(GL_MAX_TEXTURE_SIZE, &amp;glMaxTexDim);<span class="kom">// Maximální podporovaná velikost textury</span></p>
<p class="src"></p>
<p class="src1">pPicture-&gt;get_Width(&amp;lWidth);<span class="kom">// ©íøka obrázku a konvertování na pixely</span></p>
<p class="src1">lWidthPixels = MulDiv(lWidth, GetDeviceCaps(hdcTemp, LOGPIXELSX), 2540);</p>
<p class="src"></p>
<p class="src1">pPicture-&gt;get_Height(&amp;lHeight);<span class="kom">// Vý¹ka obrázku a konvertování na pixely</span></p>
<p class="src1">lHeightPixels = MulDiv(lHeight, GetDeviceCaps(hdcTemp, LOGPIXELSY), 2540);</p>

<p>Pokud je velikost obrázku men¹í ne¾ maximální podporovaná, zmìníme velikost na mocninu dvou, která ale bude zalo¾ená na aktuální velikosti. Pøièteme 0.5f, tak¾e se bude v¾dy zvìt¹ovat na následující velikost. Napøíklad rovná-li se ¹íøka 400 pixelùm a karta podporuje maximálnì 512, bude lep¹í zvolit 512 ne¾ 256, proto¾e by se zbyteènì zahodily detaily. Naopak pøi vìt¹í velikosti ne¾ maximální musíme zmen¹ovat na podporovanou velikost. Toté¾ platí i pro vý¹ku.</p>

<p>Pøekl.: Opravte mì, jestli se mýlím. Co se stane kdy¾ napø. vezmu obrázek, který má ¹íøku 80 a vý¹ku 300 pixelù? Té matematice sice moc nerozumím :-), ale z toho, co je zde uvedeno, logicky vychází, ¾e vznikne obdélníkový (ne ètvercový!) obrázek o rozmìrech 128x512 pixelù. Mo¾ná by bylo vhodné je¹tì pøidat nìco ve stylu: pokud je jeden rozmìr men¹í ne¾ druhý, uprav hodnoty na ètverec.</p>

<p class="src1">if (lWidthPixels &lt;= glMaxTexDim)<span class="kom">// Je ¹íøka men¹í nebo stejná ne¾ maximálnì podporovaná</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Zmìna velikosti na nejbli¾¹í mocninu dvou</span></p>
<p class="src2">lWidthPixels = 1 &lt;&lt; (int)floor((log((double)lWidthPixels)/log(2.0f)) + 0.5f);</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Bude se zmen¹ovat na maximální velikost</span></p>
<p class="src1">{</p>
<p class="src2">lWidthPixels = glMaxTexDim;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (lHeightPixels &lt;= glMaxTexDim)<span class="kom">// Je vý¹ka men¹í nebo stejná ne¾ maximálnì podporovaná</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Zmìna velikosti na nejbli¾¹í mocninu dvou</span></p>
<p class="src2">lHeightPixels = 1 &lt;&lt; (int)floor((log((double)lHeightPixels)/log(2.0f)) + 0.5f);</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Bude se zmen¹ovat na maximální velikost</span></p>
<p class="src1">{</p>
<p class="src2">lHeightPixels = glMaxTexDim;</p>
<p class="src1">}</p>

<p>V tuto chvíli máme data nahraná a také známe po¾adovanou velikost obrázku, abychom ho mohli dále upravovat, musíme vytvoøit pomocnou bitmapu. Bi bude obsahovat informace o hlavièce a pBits bude ukazovat na data obrázku. Po¾adujeme barevnou hloubku 32 bitù na pixel, správnou ¹íøku i vý¹ku v kódování RGB s jednou bitplane.</p>

<p class="src1"><span class="kom">// Pomocná bitmapa</span></p>
<p class="src1">BITMAPINFO bi = {0};<span class="kom">// Typ bitmapy</span></p>
<p class="src1">DWORD *pBits = 0;<span class="kom">// Ukazatel na data bitmapy</span></p>
<p class="src"></p>
<p class="src1">bi.bmiHeader.biSize = sizeof(BITMAPINFOHEADER);<span class="kom">// Velikost struktury</span></p>
<p class="src1">bi.bmiHeader.biBitCount = 32;<span class="kom">// 32 bitù</span></p>
<p class="src1">bi.bmiHeader.biWidth = lWidthPixels;<span class="kom">// ©íøka</span></p>
<p class="src1">bi.bmiHeader.biHeight = lHeightPixels;<span class="kom">// Vý¹ka</span></p>
<p class="src1">bi.bmiHeader.biCompression = BI_RGB;<span class="kom">// RGB formát</span></p>
<p class="src1">bi.bmiHeader.biPlanes = 1;<span class="kom">// 1 Bitplane</span></p>

<p>Pøevzato z MSDN: Funkce CreateDIBSection() vytváøí DIB, do kterého mù¾e aplikace pøímo zapisovat. Vrací ukazatel na umístìní bitù bitmapy, mù¾eme také nechat systém alokovat pamì».</p>

<p>HdcTemp ukládá pomocný kontext zaøízení, bi je hlavièka bitmapy. DIB_RGB_COLORS øíká programu, ¾e chceme ulo¾it RGB data, která nebudou indexována do logické palety (ka¾dý pixel bude mít èervenou, zelenou a modrou slo¾ku). Ukazatel pBits bude obsahovat adresu výsledných dat a poslední dva parametry budeme ignorovat. Pokud nenastane ¾ádná chyba, pomocí Selectobject() pøipojíme bitmapu k pomocnému kontextu zaøízení.</p>

<p class="src1"><span class="kom">// Touto cestou je mo¾né specifikovat barevnou hloubku a získat pøístup k datùm</span></p>
<p class="src1">hbmpTemp = CreateDIBSection(hdcTemp, &amp;bi, DIB_RGB_COLORS, (void**)&amp;pBits, 0, 0);</p>
<p class="src"></p>
<p class="src1">if(!hbmpTemp)<span class="kom">// Neúspìch</span></p>
<p class="src1">{</p>
<p class="src2">DeleteDC(hdcTemp);<span class="kom">// Uvolnìní kontextu zaøízení</span></p>
<p class="src2">pPicture-&gt;Release();<span class="kom">// Uvolní IPicture</span></p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SelectObject(hdcTemp, hbmpTemp);<span class="kom">// Zvolí bitmapu do kontextu zaøízení</span></p>

<p>Nastal èas pro vyplnìní pomocné bitmapy daty obrázku. Funkce pPicture->Render() to udìlá za nás a navíc upraví obrázek na libovolnou velikost, kterou potøebujeme. HdcTemp pøedstavuje pomocný kontext zaøízení a dal¹í dva následující parametry specifikují vertikální a horizontální offset (poèet prázdných pixelù zleva a seshora). My chceme, aby byla celá bitmapa kompletnì vyplnìna, tak¾e zadáme dvì nuly. Dal¹í dva parametry urèují po¾adovanou velikost výsledného obrázku (na kolik pixelù se má roztáhnout popø. zmen¹it). Nula na dal¹ím místì je horizontální offset ve zdrojových datech, od kterého chceme zaèít èíst, z èeho¾ plyne, ¾e pùjdeme zleva doprava. LHeight urèuje vertikální offset, data chceme èíst od zdola nahoru. Zadáním lHeight se pøesuneme na samé dno zdrojového obrázku. LWidth je mno¾stvím pixelù, které se budou kopírovat ze zdrojového obrázku, v na¹em pøípadì se jedná o v¹echna horizontální data. Pøedposlední parametr, trochu odli¹ný, má zápornou hodnotu, záporné lHeight, abychom byli pøesní. Ve výsledku to znamená, ¾e chceme zkopírovat v¹echna vertikální data, ale od zdola nahoru. Touto cestou bude pøi kopírování do cílové bitmapy pøevrácen. Poslední parametr nepou¾ijeme.</p>

<p class="src1"><span class="kom">// Vykreslení IPicture do bitmapy</span></p>
<p class="src1">pPicture-&gt;Render(hdcTemp, 0, 0, lWidthPixels, lHeightPixels, 0, lHeight, lWidth, -lHeight, 0);</p>

<p>Nyní máme k dispozici novou bitmapu se správnými rozmìry, ale bohu¾el je ulo¾ena ve formátu BGR. (Pøekl.: Proè tomu tak je, bylo vysvìtlováno v 35. tutoriálu na pøehrávání AVI videa.) Pomocí jednoduchého cyklu tyto dvì slo¾ky prohodíme a zároveò nastavíme alfu na 255. Dá se øíci, ¾e jakákoli jiná hodnota stejnì nebude mít nejmen¹í efekt, proto¾e alfu ignorujeme.</p>

<p class="src1"><span class="kom">// Konverze BGR na RGB</span></p>
<p class="src1">for(long i = 0; i &lt; lWidthPixels * lHeightPixels; i++)<span class="kom">// Cyklus pøes v¹echny pixely</span></p>
<p class="src1">{</p>
<p class="src2">BYTE* pPixel = (BYTE*)(&amp;pBits[i]);<span class="kom">// Aktuální pixel</span></p>
<p class="src2">BYTE  temp = pPixel[0];<span class="kom">// Modrá slo¾ka do pomocné promìnné</span></p>
<p class="src2">pPixel[0] = pPixel[2];<span class="kom">// Ulo¾ení èervené slo¾ky na správnou pozici</span></p>
<p class="src2">pPixel[2] = temp;<span class="kom">// Vlo¾ení modré slo¾ky na správnou pozici</span></p>
<p class="src2">pPixel[3] = 255;<span class="kom">// Konstantní alfa hodnota</span></p>
<p class="src1">}</p>

<p>Po v¹ech nutných operacích mù¾eme z obrázku vygenerovat texturu. Zvolíme ji jako aktivní a nastavíme lineární filtrování. Myslím, ¾e glTexImage2D() u¾ nemusím vysvìtlovat.</p>

<p class="src1">glGenTextures(1, &amp;texid);<span class="kom">// Generování jedné textury</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texid);<span class="kom">// Zvolí texturu</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER, GL_LINEAR);<span class="kom">// Lineární filtrování</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER, GL_LINEAR);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vytvoøení textury</span></p>
<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, 3, lWidthPixels, lHeightPixels, 0, GL_RGBA, GL_UNSIGNED_BYTE, pBits);</p>

<p>Poté, co je textura vytvoøena, mù¾eme uvolnit zabrané systémové zdroje. U¾ nebudeme potøebovat pomocnou ani bitmapu ani kontext zaøízení ani pPicture.</p>

<p class="src1">DeleteObject(hbmpTemp);<span class="kom">// Sma¾e bitmapu</span></p>
<p class="src1">DeleteDC(hdcTemp);<span class="kom">// Sma¾e kontext zaøízení</span></p>
<p class="src1">pPicture-&gt;Release();<span class="kom">// Uvolní IPicture</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// OK</span></p>
<p class="src0">}</p>

<p>Následující funkce zji¹»uje, jestli grafická karta podporuje roz¹íøení EXT_fog_coord. Tento kód mù¾e být pou¾it pouze, pokud u¾ má program k dispozici renderovací kontext. Jestli¾e ho zkusíme zavolat pøed inicializací okna, dostaneme chyby.</p>

<p>Vytvoøíme pole obsahující jméno na¹eho roz¹íøení. Alokujeme dynamickou pamì», do které následnì zkopírujeme seznam v¹ech podporovaných roz¹íøení. Pokud strstr() mezi nimi najde EXT_fog_coord, vrátíme false. (Pøekl.: Uvolnit dynamickou pamì»!!!)</p>

<p class="src0">int Extension_Init()<span class="kom">// Je roz¹íøení EXT_fog_coord podporováno?</span></p>
<p class="src0">{</p>
<p class="src1">char Extension_Name[] = &quot;EXT_fog_coord&quot;;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Alokace pamìti pro øetìzec</span></p>
<p class="src1">char* glextstring = (char *)malloc(strlen((char *)glGetString(GL_EXTENSIONS)) + 1);</p>
<p class="src1">strcpy (glextstring,(char *)glGetString(GL_EXTENSIONS));<span class="kom">// Grabování seznamu podporovaných roz¹íøení</span></p>
<p class="src"></p>
<p class="src1">if (!strstr(glextstring, Extension_Name))<span class="kom">// Není podporováno?</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// free(glextstring);// Pøekl.: Uvolnìní alokované pamìti !!!</span></p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">free(glextstring);<span class="kom">// Uvolnìní alokované pamìti</span></p>

<p>Na samém zaèátku programu jsme deklarovali promìnnou glFogCoordfEXT jako ukazatel na funkci. Proto¾e u¾ s jistotou víme, ¾e grafická karta toto roz¹íøení podporuje, mù¾eme ho pomocí wglGetProcAddress() nastavit na správnou adresu. Od této chvíle máme k dispozici novou funkci glFogCoordfEXT(), které se pøedává jedna GLfloat hodnota.</p>

<p class="src1">glFogCoordfEXT = (PFNGLFOGCOORDFEXTPROC) wglGetProcAddress(&quot;glFogCoordfEXT&quot;);<span class="kom">// Nastaví ukazatel na funkci</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// OK</span></p>
<p class="src0">}</p>

<p>Pøi vstupu do Initialize() má program k dispozici renderovací kontext, tak¾e se mù¾eme dotázat na podporu roz¹íøení. Pokud není dostupné, ukonèíme program. Texturu nahráváme pomocí nového IPicture kódu. Pokud se z nìjakého dùvodu loading nezdaøí, opìt ukonèíme program. Následuje obvyklá inicializace OpenGL.</p>

<p class="src0">BOOL Initialize(GL_Window* window, Keys* keys)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;<span class="kom">// Okno</span></p>
<p class="src1">g_keys = keys;<span class="kom">// Klávesnice</span></p>
<p class="src"></p>
<p class="src1">if (!Extension_Init())<span class="kom">// Je roz¹íøení podporováno?</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!BuildTexture(&quot;data/wall.bmp&quot;, texture[0]))<span class="kom">// Nahrání textury</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapování textur</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testování hloubky</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>

<p>Dále potøebujeme nastavit mlhu. Nejdøíve ji zapneme, potom urèíme lineární renderovací mód (vypadá lépe) a definujeme barvu na tmav¹í odstín oran¾ové. Startovní pozice mlhy je místo, kde bude nejménì hustá. Abychom udr¾eli vìci jednoduché pøedáme èíslo 0.0f. Naopak nejvíce hustá bude s hodnotou 1.0f. Podle v¹ech dokumentací, které jsem kdy èetl, nastavení hintu na GL_NICEST zpùsobí, ¾e se bude pùsobí mlhy urèovat zvlá¹» pro ka¾dý pixel. Pøedáte-li GL_FASTEST, bude se poèítat pro jednotlivé vertexy, nicménì nejde vidìt ¾ádný rozdíl. Poslední glFogi() pøíkaz oznámí OpenGL, ¾e chceme nastavovat mlhu v závislosti na koordinátech vertexù. To zpùsobí, ¾e ji budeme moci umístit kamkoli na scénu bez toho, ¾e bychom tak ovlivnili její zbytek.</p>

<p class="src1"><span class="kom">// Nastavení mlhy</span></p>
<p class="src1">glEnable(GL_FOG);<span class="kom">// Zapne mlhu</span></p>
<p class="src1">glFogi(GL_FOG_MODE, GL_LINEAR);<span class="kom">// Lineární pøechody</span></p>
<p class="src1">glFogfv(GL_FOG_COLOR, fogColor);<span class="kom">// Barva</span></p>
<p class="src1">glFogf(GL_FOG_START, 0.0f);<span class="kom">// Poèátek</span></p>
<p class="src1">glFogf(GL_FOG_END, 1.0f);<span class="kom">// Konec</span></p>
<p class="src1">glHint(GL_FOG_HINT, GL_NICEST);<span class="kom">// Výpoèty na jednotlivých pixelech</span></p>
<p class="src1">glFogi(GL_FOG_COORDINATE_SOURCE_EXT, GL_FOG_COORDINATE_EXT);<span class="kom">// Mlha v závislosti na souøadnicích vertexù</span></p>

<p>Poèáteèní hodnotu promìnné camz urèíme na -19.0f. Proto¾e chodbu renderujeme od -19.0f do +14.0f, bude to pøesnì na zaèátku.</p>

<p class="src1">camz = -19.0f;<span class="kom">// Pozice kamery</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// OK</span></p>
<p class="src0">}</p>

<p>Funkce zaji¹»ující stisky kláves je dnes opravdu jednoduchá. Pomocí ¹ipek nahoru a dolù nastavujeme pozici kamery ve scénì. Zároveò musíme o¹etøit &quot;pøeteèení&quot;, abychom se neocitli venku z chodby.</p>

<p class="src0">void Update(DWORD milliseconds)<span class="kom">// Aktualizace scény</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown[VK_ESCAPE])<span class="kom">// ESC</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication(g_window);<span class="kom">// Konec programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_F1])<span class="kom">// F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// Zmìna fullscreen/okno</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_UP] &amp;&amp; camz &lt; 14.0f)<span class="kom">// ©ipka nahoru</span></p>
<p class="src1">{</p>
<p class="src2">camz+=(float)(milliseconds) / 100.0f;<span class="kom">// Pohyb dopøedu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_DOWN] &amp;&amp; camz &gt; -19.0f)<span class="kom">// ©ipka dolù</span></p>
<p class="src1">{</p>
<p class="src2">camz-=(float)(milliseconds) / 100.0f;<span class="kom">// Pohyb dozadu</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jsem si jistý, ¾e u¾ netrpìlivì èekáte na vykreslování. Sma¾eme buffery, resetujeme matici a v závislosti na hodnotì camz se pøesuneme do hloubky.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f, 0.0f, camz);<span class="kom">// Translace v hloubce</span></p>

<p>Kamera je umístìna, tak¾e zkusíme vykreslit první quad. Bude jím zadní stìna, která by mìla být kompletnì ponoøená v mlze. Z inicializace si jistì pamatujete, ¾e nejhust¹í mlhu nastavuje hodnota GL_FOG_END; urèili jsme ji na 1.0f. Mlha se aplikuje podobnì jako texturové koordináty, pro nejmen¹í viditelnost pøedáme funkci glFogCoordfEXT() èíslo 1.0f a pro nejvìt¹í 0.0f. Zadní stìna je kompletnì ponoøená v mlze, tak¾e pøedáme v¹em jejím vertexùm jednièku.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zadní stìna</span></p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f(-2.5f,-2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f( 2.5f,-2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f( 2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f(-2.5f, 2.5f,-15.0f);</p>
<p class="src1">glEnd();</p>

<p>První dva body podlahy navazují na vertexy zadní stìny, a proto také zde uvedeme 1.0f. Pøední body jsou u¾ naopak z mlhy venku, tudí¾ je musíme nastavit na 0.0f. Místa le¾ící mezi okraji se automaticky interpolují, a tak vznikne plynulý pøechod. V¹echny ostatní stìny budou analogické.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Podlaha</span></p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f(-2.5f,-2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f( 2.5f,-2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f( 2.5f,-2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f(-2.5f,-2.5f, 15.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Strop</span></p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f(-2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f( 2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f( 2.5f, 2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f(-2.5f, 2.5f, 15.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Pravá stìna</span></p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f( 2.5f,-2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f( 2.5f, 2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f( 2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f( 2.5f,-2.5f,-15.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Levá stìna</span></p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 0.0f);glVertex3f(-2.5f,-2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(0.0f); glTexCoord2f(0.0f, 1.0f);glVertex3f(-2.5f, 2.5f, 15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 1.0f);glVertex3f(-2.5f, 2.5f,-15.0f);</p>
<p class="src2">glFogCoordfEXT(1.0f); glTexCoord2f(1.0f, 0.0f);glVertex3f(-2.5f,-2.5f,-15.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vyprázdnìní renderovací pipeline</span></p>
<p class="src0">}</p>

<p>Doufám, ¾e nyní u¾ rozumíte, jak vìci pracují. Èím vzdálenìj¹í je objekt, tím by mìl být více ponoøen v mlze a tudí¾ musí být nastavena hodnota 1.0f. V¾dycky si také mù¾ete pohrát s GL_FOG_START a GL_FOG_END a pozorovat, jak ovlivòují scénu. Efekt nebude pracovat podle oèekávání, pokud prohodíte hodnoty. Iluze se vytvoøila tím, ¾e je zadní stìna kompletnì oran¾ová. nejvýhodnìj¹í pou¾ití spoèívá u temných koutù, kde se hráè nemù¾e dostat za mlhu.</p>

<p>Plánujete-li tento typ mlhy ve svém 3D enginu, bude mo¾ná vhodné upravovat poèáteèní a koncové hodnoty podle toho, kde hráè stojí, kterým smìrem se dívá a podobnì.</p>

<p>Doufám, ¾e jste si u¾ili tento tutoriál. Vytváøel jsem ho pøes tøi dny, ètyøi hodiny dennì. Vìt¹inu èasu zabralo psaní textù, které právì ètete. Pùvodnì jsme chtìl vytvoøit kompletní 3D místnost s mlhou v jednom rohu, ale nane¹tìstí jsem mìl velmi málo èasu na kódování. Pøesto¾e zaml¾ená chodba je velmi jednoduchá, vypadá perfektnì a modifikace kódu pro vá¹ projekt by také nemìla být moc slo¾itá.</p>

<p>Je dùle¾ité poznamenat, ¾e toto je pouze jednou z nejrùznìj¹ích mo¾ností, jak vytvoøit volumetrickou mlhu. Podobný efekt mù¾e být naprogramován pomocí blendingu, èásticových systémù, maskování a podobných technologií. Pokud modifikujete pohled na scénu tak, aby byla kamera umístìna ne v chodbì, ale venku, zjistíte, ¾e se mlha nachází uvnitø chodby.</p>

<p>Originální my¹lenka tohoto tutoriálu ke mnì dorazila u¾ hodnì dávno, co¾ je jedním z dùvodù, ¾e jsem ztratil email. Osobì, která mi nápad zaslala, dìkuji.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson41.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson41_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson41.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson41.zip">Dev C++</a> kód této lekce. ( <a href="mailto:rdieffenbach@chello.nl">Rob Dieffenbach</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson41.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:ant@solace.mh.se">Anthony Whitehead</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson41.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(41);?>
<?FceNeHeOkolniLekce(41);?>

<?
include 'p_end.php';
?>
