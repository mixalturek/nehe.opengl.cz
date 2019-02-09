<?
$g_title = 'CZ NeHe OpenGL - Komprimované textury a SDL_Image';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Komprimované textury a SDL_Image</h1>

<p class="nadpis_clanku">V tomto èlánku si uká¾eme, jak vytváøet komprimované OpenGL textury a jak za pomoci knihovny SDL_Image snadno naèítat obrázky s alfa kanálem nebo v paletovém re¾imu. Tøídu Picture jsem se sna¾il navrhnou tak, aby byla co nejjednodu¹¹í a dala se snadno pou¾ít v ka¾dém programu, zároveò díky SDL_Image poskytuje velké mo¾nosti.</p>

<p>První, co si uká¾eme a popí¹eme, je deklarace tøídy Picture. Jak u¾ je patrné z komentáøù, SizeX a SizeY oznaèují rozmìr obrázku. U Bpp, které specifikuje velikost jednoho pixelu, pozor! Tato zkratka se vìt¹inou pou¾ívá jako <b>Bit</b> Per Pixel, nicménì my v ní ukládáme <b>Byte</b> Per Pixel. Její hodnota nám tedy øíká nejen, kolik zabere jeden pixel bytù v pamìti, ale také kolik má slo¾ek (3 = RGB, 4 = RGBA, ...). Ukazatel Data bude v sobì ukládat informace obrázku, tedy jednotlivé pixely. V¹echny funkce si rozepí¹eme dále kromì Free(), FlipHorizontal() a FlipVertical(), u nich¾ je to zbyteèné. Ty, které mají návratovou hodnotu typu bool, vracejí true jako úspìch a false jako neúspìch, ale to je, doufám, ka¾dému jasné.</p>

<p class="src0">class Picture<span class="kom">// Tøída obrázku</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">Uint16 SizeX;<span class="kom">// ©íøka obrázku</span></p>
<p class="src1">Uint16 SizeY;<span class="kom">// Vý¹ka obrázku</span></p>
<p class="src1">Uint16 Bpp;<span class="kom">// Poèet <b>!BYTÙ!</b> na pixel</span></p>
<p class="src1">Uint8 *Data;<span class="kom">// Ukazatel na data obrázku</span></p>
<p class="src"></p>
<p class="src1">bool Load(const char *FileName);<span class="kom">// Naète obrázek</span></p>
<p class="src1">void Free(void);<span class="kom">// Uvolní obrázek z pamìti</span></p>
<p class="src"></p>
<p class="src1">void FlipHorizontal(void);<span class="kom">// Obrátí obrázek vodorovnì</span></p>
<p class="src1">void FlipVertical(void);<span class="kom">// Obrátí obrázek svisle</span></p>
<p class="src1">bool HalfSize(void);<span class="kom">// Zmen¹í obrázek na polovinu</span></p>
<p class="src"></p>
<p class="src1">GLuint CreateTexture(int MinFilter, int MagFilter, int BitsPerColor, bool MipMaps, bool Compress);<span class="kom">// Vytvoøení textury</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Konstruktor a destruktor</span></p>
<p class="src1">Picture(void) { memset(this, 0 , sizeof(Picture)); }<span class="kom">// Vyèistí pamì» objektu</span></p>
<p class="src1">~Picture() { Free(); }<span class="kom">// Uvolní obrázek z pamìti</span></p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">bool GetCompressTexExt(int Format);<span class="kom">// Zjistí pøítomnost po¾adovaného roz¹íøení formátu pro texturu</span></p>
<p class="src0">unsigned int GetInternalFormat(int Pixel_Format, int BitsPerColor);<span class="kom">// Pomocná funkce pro zji¹tìní internal formátu</span></p>

<p>První a nejdùle¾itìj¹í metoda ve tøídì Picture je Load(), která naète obrázek a pøevede ho na v OpenGL pou¾itelný formát. Pro jeho naètení pou¾íváme funkci IMG_Load(), která vrací SDL_Surface, jen¾e ten má nìkolik nevýhod, které ho pøi pøímém pou¾ití v OpenGL vyøazují. Vìt¹ina naètených obrázkù (kromì JPG) má prohozené èervené a modré slo¾ky pixelù. Dal¹ím problémem jsou obrázky ulo¾ené v paletovém re¾imu, ty budeme muset pøevést na normální formát, a aby toho nebylo málo, obrázek má i prohozené øádky :-(</p>

<p class="src0">bool Picture::Load(const char *FileName)<span class="kom">// Naète obrázek</span></p>
<p class="src0">{</p>
<p class="src1">Free();<span class="kom">// Zkontroluje, jestli u¾ není naètený jiný obrázek a pøípadnì ho uvolni</span></p>
<p class="src"></p>
<p class="src1">SDL_Surface *Image = IMG_Load(FileName);<span class="kom">// Naète SDL_Surface pomocí knihovny SDL_Image</span></p>
<p class="src"></p>
<p class="src1">if(Image == NULL)<span class="kom">// Nelze naèíst</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nelze nacist soubor \&quot;%s\&quot; : %s\n&quot;, FileName, SDL_GetError());</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SizeX = Image-&gt;w;<span class="kom">// Nastavení promìnných ve tøídì</span></p>
<p class="src1">SizeY = Image-&gt;h;</p>
<p class="src"></p>
<p class="src1">if(Image-&gt;format == NULL)</p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Chyba v nactenem obrazku \&quot;%s\&quot;, neni udaj o formatu\n&quot;, FileName);</p>
<p class="src2">SDL_FreeSurface(Image);</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">Uint32 x, y, pix, Change = 0, PalIndex;<span class="kom">// Promìnné pro øízení cyklu</span></p>
<p class="src1">Uint8 *Pixels = (Uint8 *)Image-&gt;pixels;</p>

<p>Obrázek bez palety poznáme jen podle ukazatele pallete, který bude v takovém pøípadì nastaven na hodnotu NULL, u paletových obrázkù obsahuje barvy pro indexy v obrázku. Je¹tì by se dal poznat podle poètu bytù na pixel, který bývá roven jedné, ale kdybychom naèetli obrázek s jednou slo¾kou na pixel, byl by automaticky pova¾ován za paletový, co¾ by mohlo vést k chybám - napøíklad u obrázku obsahujícím pouze alfa kanál (i kdy¾ si nejsem jist, zda takový formát existuje).</p>

<p class="src1">if(Image-&gt;format-&gt;palette == NULL)<span class="kom">// Obrázek bez palety</span></p>
<p class="src1">{</p>
<p class="src2">Bpp = Image-&gt;format-&gt;BytesPerPixel;</p>

<p>Alokujeme potøebnou pamì» podle rozmìrù obrázku a poètu barevných slo¾ek. Na tomto místì by mohli rejpalové namítat, ¾e je zbyteèné pøidávat pøíkaz sizeof(Uint8), který v tomto pøípadì vrátí hodnotu jedna. Mo¾ná ano, ale nemuselo by to tak být, u¹etøíte si mnoho problémù.</p>

<p class="src2">Data = (Uint8 *) malloc(sizeof(Uint8) * SizeX * SizeY * Bpp);<span class="kom">// Alokace pamìti obrázku</span></p>
<p class="src"></p>
<p class="src2">if(Data == NULL)</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Nelze pridelit pamet (%d kB) potrebnou pro obrazek \&quot;%s\&quot;\n&quot;, (SizeX * SizeY * Bpp * sizeof(Uint8)) / 1024, FileName);</p>
<p class="src3">SDL_FreeSurface(Image);</p>
<p class="src3">return false;</p>
<p class="src2">}</p>

<p>Promìnná Change obsahuje hodnotu urèující, kolik slo¾ek barev se má prohodit. Skoro v¾dy je tøeba prohodit dvì slo¾ky (z BGR na RGB), ale nìkdy ne, urèuje to Bshift v SDL_Surface. Také si musíme ovìøit, zda má obrázek alespoò tøi slo¾ky, pokud ne, nemá smysl je prohazovat. Tento pøípad mù¾e nastat napøíklad u obrázkù jen s alfa hodnotou.</p>

<p class="src2">if(Image-&gt;format-&gt;Bshift == 0)<span class="kom">// Pokud je obrázek BGR/BGRA, musí se prohodit R a B</span></p>
<p class="src2">{</p>
<p class="src3">if(Bpp &gt;= 3)</p>
<p class="src3">{</p>
<p class="src4">Change = 3;<span class="kom">// Promìnná change zaji¹»uje pøehazování prvních tøí slo¾ek barvy</span></p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Kopírovací cyklus zároveò se swapováním slo¾ek barev prohazuje i øádky, které jsou v SDL_Surface opaènì. První z vnoøených cyklù, který kopíruje pixely nemusí probìhnout, proto¾e promìnná Change mù¾e být nula a to znamená, ¾e se neprohazují barevné slo¾ky. Druhý probìhne ve dvou pøípadech. Hodnota v Change je nula a první cyklus neprobìhl nebo je poèet slo¾ek (Bpp) vìt¹í ne¾ tøi a musí se pøidat k prvním tøem prohozeným je¹tì dal¹í hodnoty (napø. alfa).</p>

<p class="src2">for(y = 0; y &lt; SizeY; y++)<span class="kom">// Kopírovací cyklus</span></p>
<p class="src2">{</p>
<p class="src3">for(x = 0 ; x &lt; SizeX ; x++)</p>
<p class="src3">{</p>
<p class="src4">for(pix = 0 ; pix &lt; Change ; pix++)<span class="kom">// Hodnoty, které se prohodí (v¾dy 3) BGR na RGB</span></p>
<p class="src4">{</p>
<p class="src5">Data[(x + (y * SizeY)) * Bpp + (Change-1 - pix)] = Pixels[(x + ((SizeY-1 - y) * SizeY)) * Bpp + pix];</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">for(pix = Change ; pix &lt; Bpp ; pix++)<span class="kom">// Hodnoty, které zùstanou neprohozeny (napø. alfa)</span></p>
<p class="src4">{</p>
<p class="src5">Data[(x + (y * SizeY)) * Bpp + pix] = Pixels[(x + ((SizeY-1 - y) * SizeY)) * Bpp + pix];</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Obrázek v paletovém re¾imu je díky uspoøádání palety v SDL_Surface, která v sobì obsahuje strukturu SDL_Color, daleko jednodu¹¹í. Staèí jen prohazovat øádky. Obrázek obsahuje místo slo¾ek barev jen indexy do palety, pomocí nich¾ se v ní orientujeme. Tímto zpùsobem naèteme barvy do pole Data.</p>

<p class="src1">else<span class="kom">// Obrázek v paletovém re¾imu</span></p>
<p class="src1">{</p>
<p class="src2">Bpp = 3;<span class="kom">// Pøedpokládáme RGB formát a tudí¾ zabírá jeden pixel 3 byty</span></p>
<p class="src"></p>
<p class="src2">if(Image-&gt;format-&gt;palette-&gt;colors == NULL)<span class="kom">// Kontrola palety</span></p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Chyba v palete obrazku \&quot;%s\&quot;\n&quot;, FileName);</p>
<p class="src3">SDL_FreeSurface(Image);</p>
<p class="src3">return false;</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">Data = (Uint8 *) malloc(sizeof(Uint8) * SizeX * SizeY * Bpp);<span class="kom">// Pamì» pro obrázek</span></p>
<p class="src"></p>
<p class="src2">if(Data == NULL)</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Nelze pridelit pamet(%d kB) potrebnou pro obrazek \&quot;%s\&quot;\n&quot;, (SizeX * SizeY * Bpp * sizeof(Uint8)) / 1024, FileName);</p>
<p class="src3">SDL_FreeSurface(Image);</p>
<p class="src3">return false;</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">for(y = 0 ; y &lt; SizeY ; y++)<span class="kom">// Kopírovací cyklus</span></p>
<p class="src2">{</p>
<p class="src3">for(x = 0 ; x &lt; SizeX ; x++)</p>
<p class="src3">{</p>
<p class="src4">PalIndex = Pixels[x + ((SizeY-1 - y) * SizeY)];<span class="kom">// Index v paletì</span></p>
<p class="src"></p>
<p class="src4">Data[(x + (y * SizeY)) * Bpp] = Image-&gt;format-&gt;palette-&gt;colors[PalIndex].r;<span class="kom">// Èervená</span></p>
<p class="src4">Data[(x + (y * SizeY)) * Bpp + 1] = Image-&gt;format-&gt;palette-&gt;colors[PalIndex].g;<span class="kom">// Zelená</span></p>
<p class="src4">Data[(x + (y * SizeY)) * Bpp + 2] = Image-&gt;format-&gt;palette-&gt;colors[PalIndex].b;<span class="kom">// Modrá</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SDL_FreeSurface(Image);<span class="kom">// Úklid</span></p>
<p class="src1">return true;<span class="kom">// V¹e OK</span></p>
<p class="src0">}</p>

<p>Ne¾ se pustíme do vytváøení textury, dáme si trochu oddych. Podíváme se na funkci HalfSize, z jejího¾ názvu vyplývá i její úèel - zmen¹í obrázek na polovinu. Mo¾ná se nìkteøí ptáte, k èemu je taková funkce dobrá, kdy¾ vlastnì sni¾uje kvalitu obrázku. Právì o to jde, mù¾ete tak snadno ve svém programu v¹echny textury pøi naèítání zmen¹it na polovinu a tím ¹etøit pamì» a výkon slab¹ích strojù.</p>

<p>Tato funkce ov¹em není úplnì primitivním vynecháním jednoho øádku jako v jistém nejmenovaném kreslícím programu od firmy Microsoft&reg;. Pøi zmen¹ení o polovinu se stává ze ètyø pixelù jeden, který je jejich prùmìrem. To zajistí, aby nevymizely dùle¾ité detaily. Podobnì pracuje i roz¹íøení multisample u grafických karet, kde se pro zlep¹ení kvality obrazu a zahlazení hran vyrenderuje vìt¹í obrázek, který je následnì zmen¹en a zobrazen. Kdyby nìkdo chtìl vidìt, jak to vypadá bez tohoto efektu, a» odkomentuje variantu bez zahlazení a odstraní tu se zahlazením.</p>

<p class="src0">bool Picture::HalfSize(void)<span class="kom">// Zmen¹í obrázek na polovinu</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Kontrola velikosti, dat a poètu bytù na slo¾ku barvy</span></p>
<p class="src1">if(Data == NULL || SizeX &lt; 2 || SizeY &lt; 2 || Bpp &lt; 1)</p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">int NewSizeX = SizeX / 2;<span class="kom">// Nová velikost</span></p>
<p class="src1">int NewSizeY = SizeY / 2;</p>
<p class="src"></p>
<p class="src1">BYTE *NewPic = (BYTE *) malloc(sizeof(BYTE) * NewSizeX * NewSizeY * Bpp);<span class="kom">// Pøidìlení pamìti pro nový polovièní obrázek</span></p>
<p class="src"></p>
<p class="src1">if(NewPic == NULL)</p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nelze pridelit pamet(%d kB)\n&quot;, (sizeof(BYTE) * NewSizeX * NewSizeY * Bpp) / 1024);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Projdeme starý obrázek a nahrajeme ho do polovièního. Pøitom v¾dy vytvoøíme ze 4 pixelù jeden, který bude jejich prùmìrem.</span></p>
<p class="src1">int x, y, b;</p>
<p class="src"></p>
<p class="src1">for(y = 0 ; y &lt; NewSizeY ; y++)</p>
<p class="src1">{</p>
<p class="src2">for(x = 0 ; x &lt; NewSizeX ; x++)</p>
<p class="src2">{</p>
<p class="src3">for(b = 0 ; b &lt; Bpp ; b++)</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// NewPic[(x + y * NewSizeX) * Bpp + b] = Data[(x*2 + y*2 * SizeX) * Bpp + b];</span><span class="kom">// Bez vyhlazení</span></p>
<p class="src"></p>
<p class="src4">NewPic[(x + y * NewSizeX) * Bpp + b] = (BYTE) ((float) (Data[(x*2 + y*2 * SizeX) * Bpp + b] + Data[(x*2+1 + y*2 * SizeX) * Bpp + b] + Data[(x*2 + (y*2+1) * SizeX) * Bpp + b] + Data[(x*2+1 + (y*2+1) * SizeX) * Bpp + b]) / 4.0f);<span class="kom">// S vyhlazením</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Uvolnìní starého obrázku a nastavení ukazatele na nový</span></p>
<p class="src1">free(Data);</p>
<p class="src1">SizeX = NewSizeX;</p>
<p class="src1">SizeY = NewSizeY;</p>
<p class="src1">Data = NewPic;</p>
<p class="src"></p>
<p class="src1">return true;<span class="kom">// OK</span></p>
<p class="src0">}</p>

<p>Pomalu se vrhneme na funkci vytváøející z obrázku texturu, ale pøedtím se nejdøíve podíváme na její pomocnou funkci, která ovìøuje podporu po¾adovaného formátu. Pomocí glGetIntegerv() zjistíme poèet podporovaných formátù, abychom mohli alokovat dostateènì velkou pamì» pro jejich seznam. Poté si pomocí stejné funkce vy¾ádáme onen seznam, který následnì prohledáme. Pokud nalezneme shodu s formátem zadaným v jediném parametru, funkce vrátí true. Pokud nebude shoda nalezena, co¾ znamená ¾e tento formát není podporován, vrátíme false.</p>

<p class="src0">bool GetCompressTexExt(int Format)<span class="kom">// Zjistí pøítomnost po¾adovaného roz¹íøení formátu pro texturu</span></p>
<p class="src0">{</p>
<p class="src1">GLint NumFormat = 0;</p>
<p class="src1">GLint *Formats = NULL;</p>
<p class="src"></p>
<p class="src1">glGetIntegerv(GL_NUM_COMPRESSED_TEXTURE_FORMATS_ARB, &amp;NumFormat);</p>
<p class="src1">Formats = (GLint *) malloc(sizeof(GLint) * NumFormat);</p>
<p class="src"></p>
<p class="src1">if(Formats == NULL)</p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glGetIntegerv(GL_COMPRESSED_TEXTURE_FORMATS_ARB, Formats);</p>
<p class="src"></p>
<p class="src1">for(GLint i = 0 ; i &lt; NumFormat ; i++)</p>
<p class="src1">{</p>
<p class="src2">if(Format == Formats[i])</p>
<p class="src2">{</p>
<p class="src3">free(Formats);</p>
<p class="src3">return true;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">free(Formats);</p>
<p class="src1">return false;</p>
<p class="src0">}</p>

<p>Koneènì se dostáváme k funkci CreateTexture(), která po vytvoøení textury vrátí její OpenGL adresu. Napøed si vysvìtlíme parametry. Do MinFilter a MagFilter se zadává filtrování textury. V tìchto parametrech mù¾ete pou¾ít klasické hodnoty OpenGL (GL_LINEAR, GL_NEAREST_MIPMAP_NEAREST, ...) nebo pro zjednodu¹ení RV_LINEAR a RV_NEAREST, které jsou definované v hlavièkovém souboru na¹í tøídy. Za tyto hodnoty vám funkce sama dosadí podle dal¹ího parametru MipMaps správné filtrování pro normální nebo mipmapové textury. Parametr BitsPerColor urèuje velikost jedné slo¾ky barvy v pamìti grafické karty. Jedna slo¾ka mù¾e být tøeba i 4 bity, co¾ je polovina bytu a to je taky dùvod proè se zadává v bitech. Poslední parametr Compress zapíná komprimaci textur, u které odpadá nutnost nastavovat poèet bitù na slo¾ku barvy.</p>

<p class="src0">GLuint Picture::CreateTexture(int MinFilter, int MagFilter, int BitsPerColor, bool MipMaps, bool Compress)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Zjistí, jestli jsou hodnoty filtrování GL* nebo RV* - pøedìlá je na GL</span></p>
<p class="src1">if(MinFilter == RV_NEAREST)</p>
<p class="src1">{</p>
<p class="src2">if(MipMaps)</p>
<p class="src2">{</p>
<p class="src3">MinFilter = GL_NEAREST_MIPMAP_NEAREST;</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">MinFilter = GL_NEAREST;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else if(MinFilter == RV_LINEAR)</p>
<p class="src1">{</p>
<p class="src2">if(MipMaps)</p>
<p class="src2">{</p>
<p class="src3">MinFilter = GL_LINEAR_MIPMAP_LINEAR;</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">MinFilter = GL_LINEAR;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(MagFilter == RV_NEAREST)</p>
<p class="src1">{</p>
<p class="src2">MagFilter = GL_NEAREST;</p>
<p class="src1">}</p>
<p class="src1">else if(MagFilter == RV_LINEAR)</p>
<p class="src1">{</p>
<p class="src2">MagFilter = GL_LINEAR;</p>
<p class="src1">}</p>

<p>Zde podle poètu bytù na pixel urèíme OpenGL formát textury. GL_ALPHA má jednu slo¾ku, RGB má tøi a RGBA má ètyøi slo¾ky. Pokud je obrázek v jiném formátu, nastane chyba. Není ale problém, podle potøeby dopsat i jiné formáty nebo napsat pøetí¾enou funkci která bude mít o parametr víc právì pro formát textury. Já jsem zvolil tento postup proto, aby byla funkce co nejvíce samostatná a nemuselo se zadávat zbyteènì moc parametrù, jejich¾ zji¹»ování by pouze zdr¾ovalo psaní programu a sni¾ovalo jeho pøehlednost.</p>

<p class="src1">unsigned int glFormat;<span class="kom">// Nastaví formát podle poètu bytù na barvu</span></p>
<p class="src"></p>
<p class="src1">switch(Bpp)</p>
<p class="src1">{</p>
<p class="src1">case 1:</p>
<p class="src2">glFormat = GL_ALPHA;</p>
<p class="src2">break;</p>
<p class="src1">case 3:</p>
<p class="src2">glFormat = GL_RGB;</p>
<p class="src2">break;</p>
<p class="src1">case 4:</p>
<p class="src2">glFormat = GL_RGBA;</p>
<p class="src2">break;</p>
<p class="src1">default:</p>
<p class="src2">fprintf(stderr, &quot;Nelze vybrat format textury. Obrazek obsahuje %d bytu na pixel\n&quot;, Bpp);</p>
<p class="src2">return 0;</p>
<p class="src1">}</p>

<p>Zde se podle formátu textury vyhodnocuje její internal formát, který udává, jak se má textura v pamìti ulo¾it. To mù¾e být jeden z komprimaèních formátù nebo obyèejné GL_RGB8, které za nás podle parametru BitsPerColor vybere funkce GetInternalFormat(). Nebudu ji zde popisovat (je to jen seznam, prohlédnìte si ji ve zdrojích).</p>

<p class="src1"><span class="kom">// Nastaví internal format podle poètu Bitù na barvu, nebo vybere compress program</span></p>
<p class="src1">unsigned int InternalFormat;</p>
<p class="src"></p>
<p class="src1">if(Compress)</p>
<p class="src1">{</p>
<p class="src2">if(glFormat == GL_RGB)</p>
<p class="src2">{</p>
<p class="src3">InternalFormat = GL_COMPRESSED_RGB_S3TC_DXT1_EXT;</p>
<p class="src2">}</p>
<p class="src2">else if(glFormat == GL_RGBA)</p>
<p class="src2">{</p>
<p class="src3">InternalFormat = GL_COMPRESSED_RGBA_S3TC_DXT5_EXT;</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Chyba, komprimovane textury mohou byt pouze ve formatu RGB nebo RGBA\n&quot;);</p>
<p class="src3">return 0;</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">if(GetCompressTexExt(InternalFormat) == false)</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Graficka karta nepodporuje rozsireni potrebne pro komprese textur\n&quot;);</p>
<p class="src3">return 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">if((InternalFormat = GetInternalFormat(glFormat, BitsPerColor)) == 0)</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Nelze vybrat internal format. glFormat %d, bytu na slozku barvy %d\n&quot;, glFormat, Bpp);</p>
<p class="src3">return 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Vytvoøení textury z naèteného obrázku - rutina, kterou ka¾dý OpenGL programátor vygeneruje i o pùlnoci. Pou¾ijeme zde hodnoty, které jsme pøedtím pracnì shroma¾ïovali a vybírali.</p>

<p class="src1"><span class="kom">// Vytvoøení textury</span></p>
<p class="src1">GLuint TexID;</p>
<p class="src1">glGenTextures(1, &amp;TexID);</p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, TexID);</p>
<p class="src"></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, MinFilter);</p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, MagFilter);</p>
<p class="src"></p>
<p class="src1">if(MipMaps)</p>
<p class="src1">{</p>
<p class="src2">gluBuild2DMipmaps(GL_TEXTURE_2D, InternalFormat, SizeX, SizeY, glFormat, GL_UNSIGNED_BYTE, Data);</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, InternalFormat, SizeX, SizeY, 0, glFormat, GL_UNSIGNED_BYTE, Data);</p>
<p class="src1">}</p>
<p class="src1"></p>
<p class="src1">return TexID;</p>
<p class="src0">}</p>


<p>Popis tøídy Picture je ¹»astnì za námi, ale jak ji v programu pou¾ít? Staèí includovat Picture.h (+ Picture.cpp) a napsat nìco na tento zpùsob:</p>

<p class="src1">Picture Pic;<span class="kom">// Objekt tøídy</span></p>
<p class="src"></p>
<p class="src1">if(!Pic.Load("Alien2.tga"))<span class="kom">// Nahrání obrázku</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">GLuint Texture = Pic.CreateTexture(RV_LINEAR, RV_LINEAR, 8, true, false);<span class="kom">// Vytvoøení OpenGL textury</span></p>
<p class="src"></p>
<p class="src1">if(Texture == 0)<span class="kom">// Chyba pøi vytváøení textury</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>V tomto pøípadì jsem ani nepou¾il funkci free na uvolnìní pamìti, proto¾e se po skonèení funkce automaticky zavolá destruktor.</p>

<p>A to je v¹e, jak prosté! No zas tak prosté to nebylo. Jen malé upozornìní pro rejpaly: a¾ si budete prohlí¾et funkci FlipHorizontal(), tak mi nepi¹te, ¾e jsem mohl prohazovat celé øádky a ne pixel po pixelu. Pøi tomto postupu toti¾ nepotøebujeme dynamicky pøidìlit pamì» pro celý øádek, ale pou¾íváme statické pole o deseti prvcích a budeme pøedpokládat velikost pixelù men¹í ne¾ deset bytù (samozøejmì je to o¹etøené ifem).</p>

<p class="autor">napsal: Radomír Vrána <?VypisEmail('rvalien@c-box.cz');?></p>

<p>Tento èlánek byl napsán pro web http://nehe.ceske-hry.cz/. Pokud ho chcete umístit i na své stránky, zeptejte autora.</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_sdl_picture.rar');?> - Zdrojové kódy tøídy a ukázkový program (Visual C++)</li>
</ul>

<?
include 'p_end.php';
?>
