<?
$g_title = 'CZ NeHe OpenGL - Lekce 33 - Nahrávání komprimovaných i nekomprimovaných obrázkù TGA';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(33);?>

<h1>Lekce 33 - Nahrávání komprimovaných i nekomprimovaných obrázkù TGA</h1>

<p class="nadpis_clanku">V lekci 24 jsem vám ukázal cestu, jak nahrávat nekomprimované 24/32 bitové TGA obrázky. Jsou velmi u¾iteèné, kdy¾ potøebujete alfa kanál, ale nesmíte se starat o jejich velikost, proto¾e byste je ihned pøestali pou¾ívat. K diskovému místu nejsou zrovna ¹etrné. Problém velikosti vyøe¹í nahrávání obrázkù komprimovaných metodou RLE. Kód pro loading a hlavièkové soubory jsou oddìleny od hlavního projektu, aby mohly být snadno pou¾ity i jinde.</p>

<p>Zaèneme dvìma hlavièkový soubory. Texture.h, první z nich, popisuje strukturu textury. Ka¾dý hlavièkový soubor by mìl obsahovat ochranu proti vícenásobnému vlo¾ení. Zaji¹»ují ji pøíkazy preprocesoru jazyka C. Pokud není definovaná symbolická konstanta __TEXTURE_H__, nadefinujeme ji a do stejného bloku podmínky vepí¹eme zdrojový kód. Pøi následujícím pokusu o inkludování hlavièkového souboru existence konstanty oznámí preprocesoru, ¾e u¾ byl soubor jednou vlo¾en, a tudí¾ ho nemá vkládat podruhé.</p>

<p class="src0">#ifndef __TEXTURE_H__</p>
<p class="src0">#define __TEXTURE_H__</p>

<p>Budeme potøebovat strukturu informací o obrázku, ze kterého se vytváøí textura. Ukazatel imageData obsahuje data obrázku, bpp barevnou hloubku, width a height rozmìry. TexID je identifikátorem OpenGL textury, který se pøedává funkci glBindTexture(). Type urèuje typ textury - GL_RGB nebo GL_RGBA.</p>

<p class="src0">typedef struct<span class="kom">// Struktura textury</span></p>
<p class="src0">{</p>
<p class="src1">GLubyte* imageData;<span class="kom">// Data</span></p>
<p class="src1">GLuint bpp;<span class="kom">// Barevná hloubka v bitech</span></p>
<p class="src1">GLuint width;<span class="kom">// ©íøka</span></p>
<p class="src1">GLuint height;<span class="kom">// Vý¹ka</span></p>
<p class="src1">GLuint type;<span class="kom">// Typ (GL_RGB, GL_RGBA)</span></p>
<p class="src1">GLuint texID;<span class="kom">// ID textury</span></p>
<p class="src0">} Texture;</p>
<p class="src"></p>
<p class="src0">#endif</p>

<p>Druhý hlavièkový soubor, tga.h, je speciálnì urèen pro loading TGA. Opìt zaèneme o¹etøením vícenásobného inkludování, poté vlo¾íme hlavièkový soubor textury.</p>

<p class="src0">#ifndef __TGA_H__</p>
<p class="src0">#define __TGA_H__</p>
<p class="src"></p>
<p class="src0">#include &quot;texture.h&quot;<span class="kom">// Hlavièkový soubor textury</span></p>

<p>Strukturu TGAHeader pøedstavuje pole dvanácti bytù, které ukládají hlavièku obrázku. Druhá struktura obsahuje pomocné promìnné pro nahrávání - napø. velikost dat, barevnou hloubku a podobnì.</p>

<p class="src0">typedef struct<span class="kom">// Hlavièka TGA souboru</span></p>
<p class="src0">{</p>
<p class="src1">GLubyte Header[12];<span class="kom">// Dvanáct bytù</span></p>
<p class="src0">} TGAHeader;</p>
<p class="src"></p>
<p class="src0">typedef struct<span class="kom">// Struktura obrázku</span></p>
<p class="src0">{</p>
<p class="src1">GLubyte header[6];<span class="kom">// ©est u¾iteèných bytù z hlavièky</span></p>
<p class="src1">GLuint bytesPerPixel;<span class="kom">// Barevná hloubka v bytech</span></p>
<p class="src1">GLuint imageSize;<span class="kom">// Velikost pamìti pro obrázek</span></p>
<p class="src1"><span class="kom">// GLuint temp;// Pøekl.: nikde není pou¾itá</span></p>
<p class="src1">GLuint type;<span class="kom">// Typ</span></p>
<p class="src1">GLuint Height;<span class="kom">// Vý¹ka</span></p>
<p class="src1">GLuint Width;<span class="kom">// ©íøka</span></p>
<p class="src1">GLuint Bpp;<span class="kom">// Barevná hloubka v bitech</span></p>
<p class="src0">} TGA;</p>

<p>Deklarujeme instance právì vytvoøených struktur, abychom je mohli pou¾ít v programu.</p>

<p class="src0">TGAHeader tgaheader;<span class="kom">// TGA hlavièka</span></p>
<p class="src0">TGA tga;<span class="kom">// TGA obrázek</span></p>

<p>Následující dvì pole pomohou urèit validitu nahrávaného souboru. Pokud se hlavièka obrázku neshoduje s nìkterou z nich, neumíme ho nahrát.</p>

<p class="src0">GLubyte uTGAcompare[12] = { 0,0, 2,0,0,0,0,0,0,0,0,0 };<span class="kom">// TGA hlavièka nekomprimovaného obrázku</span></p>
<p class="src0">GLubyte cTGAcompare[12] = { 0,0,10,0,0,0,0,0,0,0,0,0 };<span class="kom">// TGA hlavièka komprimovaného obrázku</span></p>

<p>Obì funkce nahrávají TGA - jedna nekomprimovaný druhá komprimovaný.</p>

<p class="src0">bool LoadUncompressedTGA(Texture*, char*, FILE*);<span class="kom">// Nekomprimovaný TGA</span></p>
<p class="src0">bool LoadCompressedTGA(Texture*, char*, FILE*);<span class="kom">// Komprimovaný TGA</span></p>
<p class="src"></p>
<p class="src0">#endif</p>

<p>Pøesuneme se k souboru TGALoader.cpp, který implementuje nahrávací funkce. Prvním øádkem kódu vlo¾íme hlavièkový soubor. Inkludujeme pouze tga.h, proto¾e texture.h jsme u¾ vlo¾ili v nìm.</p>

<p class="src0">#include &quot;tga.h&quot;<span class="kom">// Hlavièkový soubor TGA</span></p>

<p>Funkce LoadTGA() je ta, kterou v programu voláme, abychom nahráli obrázek. V parametrech se jí pøedává ukazatel na texturu a øetìzec diskové cesty. Nic dal¹ího nepotøebuje, proto¾e si v¹echny ostatní parametry detekuje sama (ze souboru). Deklarujeme handle souboru a otevøeme ho pro ètení v binárním módu. Pokud nìco sel¾e, napø. soubor neexistuje, vypí¹eme chybovou zprávu a vrátíme false jako indikaci chyby.</p>

<p class="src0">bool LoadTGA(Texture* texture, char* filename)<span class="kom">// Nahraje TGA soubor</span></p>
<p class="src0">{</p>
<p class="src1">FILE* fTGA;<span class="kom">// Handle souboru</span></p>
<p class="src1">fTGA = fopen(filename, &quot;rb&quot;);<span class="kom">// Otevøe soubor</span></p>
<p class="src"></p>
<p class="src1">if(fTGA == NULL)<span class="kom">// Nepodaøilo se ho otevøít?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not open texture file&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Zkusíme naèíst hlavièku obrázku (prvních 12 bytù souboru), která urèuje jeho typ. Výsledek se ulo¾í do promìnné tgaheader.</p>

<p class="src1">if(fread(&amp;tgaheader, sizeof(TGAHeader), 1, fTGA) == 0)<span class="kom">// Naète hlavièku souboru</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not read file header&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Právì naètenou hlavièku porovnáme s hlavièkou nekomprimovaného obrázku. Jsou-li shodné nahrajeme obrázek funkcí LoadUncompressedTGA(). Pokud shodné nejsou zkusíme, jestli se nejedná o komprimovaný obrázek. V tomto pøípadì pou¾ijeme pro nahrávání funkci LoadCompressedTGA(). S jinými typy souborù pracovat neumíme, tak¾e jediné, co mù¾eme udìlat, je oznámení neúspìchu a ukonèení funkce.</p>

<p>Pøekl.: Mìla by se je¹tì testovat návratová hodnota, proto¾e, jak uvidíte dále, funkce v mnoha pøípadech vracejí false. Program by si bez kontroly nièeho nev¹iml a pokraèoval dále.</p>

<p class="src1">if(memcmp(uTGAcompare, &amp;tgaheader, sizeof(tgaheader)) == 0)<span class="kom">// Nekomprimovaný</span></p>
<p class="src1">{</p>
<p class="src2">LoadUncompressedTGA(texture, filename, fTGA);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Pøekl.: Testovat návratovou hodnotu !!!</span></p>
<p class="src2"><span class="kom">// if(!LoadUncompressedTGA(texture, filename, fTGA))// Test návratové hodnoty</span></p>
<p class="src2"><span class="kom">// {</span></p>
<p class="src3"><span class="kom">// return false;</span></p>
<p class="src2"><span class="kom">// }</span></p>
<p class="src1">}</p>
<p class="src1">else if(memcmp(cTGAcompare, &amp;tgaheader, sizeof(tgaheader)) == 0)<span class="kom">// Komprimovaný</span></p>
<p class="src1">{</p>
<p class="src2">LoadCompressedTGA(texture, filename, fTGA);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Pøekl.: Testovat návratovou hodnotu !!!</span></p>
<p class="src2"><span class="kom">// if(!LoadCompressedTGA(texture, filename, fTGA))// Test návratové hodnoty</span></p>
<p class="src2"><span class="kom">// {</span></p>
<p class="src3"><span class="kom">// return false;</span></p>
<p class="src2"><span class="kom">// }</span></p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Ani jeden z nich</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;TGA file be type 2 or type 10 &quot;, &quot;Invalid Image&quot;, MB_OK);</p>
<p class="src2">fclose(fTGA);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Pokud dosud nenastala ¾ádná chyba, mù¾eme oznámit volajícímu kódu, ¾e obrázek byl v poøádku nahrán a ¾e mù¾e z jeho dat vytvoøit texturu.</p>

<p class="src1">return true;<span class="kom">// V¹e v poøádku</span></p>
<p class="src0">}</p>

<p>Pøistoupíme k opravdovému nahrávání obrázkù, zaèneme nekomprimovanými. Tato funkce je z velké èásti zalo¾ena na té z lekce 24, moc novinek v ní nenajdete. Zkusíme naèíst dal¹ích ¹est bytù ze souboru a ulo¾íme je do tga.header.</p>

<p class="src0">bool LoadUncompressedTGA(Texture* texture, char* filename, FILE* fTGA)<span class="kom">// Nahraje nekomprimovaný TGA</span></p>
<p class="src0">{</p>
<p class="src1">if(fread(tga.header, sizeof(tga.header), 1, fTGA) == 0)<span class="kom">// ©est u¾iteèných bytù</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not read info header&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Máme dost informací pro urèení vý¹ky, ¹íøky a barevné hloubky obrázku. Ulo¾íme je do obou struktur - textury i obrázku.</p>

<p class="src1">texture-&gt;width = tga.header[1] * 256 + tga.header[0];<span class="kom">// ©íøka</span></p>
<p class="src1">texture-&gt;height = tga.header[3] * 256 + tga.header[2];<span class="kom">// Vý¹ka</span></p>
<p class="src1">texture-&gt;bpp = tga.header[4];<span class="kom">// Barevná hloubka v bitech</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Kopírování dat do struktury obrázku</span></p>
<p class="src1">tga.Width = texture-&gt;width;</p>
<p class="src1">tga.Height = texture-&gt;height;</p>
<p class="src1">tga.Bpp = texture-&gt;bpp;</p>

<p>Otestujeme, jestli má obrázek alespoò jeden pixel a jestli je barevná hloubka 24 nebo 32 bitù.</p>

<p class="src1"><span class="kom">// Platné hodnoty?</span></p>
<p class="src1">if((texture-&gt;width &lt;= 0) || (texture-&gt;height &lt;= 0) || ((texture-&gt;bpp != 24) &amp;&amp; (texture-&gt;bpp != 32)))</p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Invalid texture information&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Nyní nastavíme typ obrázku. V pøípadì 24 bitù je jím GL_RGB, u 32 bitù má obrázek i alfa kanál, tak¾e pou¾ijeme GL_RGBA.</p>

<p class="src1">if(texture-&gt;bpp == 24)<span class="kom">// 24 bitový obrázek?</span></p>
<p class="src1">{</p>
<p class="src2">texture-&gt;type = GL_RGB;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// 32 bitový obrázek</span></p>
<p class="src1">{</p>
<p class="src2">texture-&gt;type = GL_RGBA;</p>
<p class="src1">}</p>

<p>Spoèítáme barevnou hloubku v BYTECH a celkovou velikost pamìti potøebnou pro data. Vzápìtí se ji pokusíme alokovat.</p>

<p class="src1">tga.bytesPerPixel = (tga.Bpp / 8);<span class="kom">// BYTY na pixel</span></p>
<p class="src1">tga.imageSize = (tga.bytesPerPixel * tga.Width * tga.Height);<span class="kom">// Velikost pamìti</span></p>
<p class="src"></p>
<p class="src1">texture-&gt;imageData = (GLubyte *)malloc(tga.imageSize);<span class="kom">// Alokace pamìti pro data</span></p>
<p class="src"></p>
<p class="src1">if(texture-&gt;imageData == NULL)<span class="kom">// Alokace neúspì¹ná</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not allocate memory for image&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2">fclose(fTGA);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Pokud se podaøila alokace pamìti, nahrajeme do ní data obrázku.</p>

<p class="src1"><span class="kom">// Pokusí se nahrát data obrázku</span></p>
<p class="src1">if(fread(texture-&gt;imageData, 1, tga.imageSize, fTGA) != tga.imageSize)</p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not read image data&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src2">if(texture-&gt;imageData != NULL)</p>
<p class="src2">{</p>
<p class="src3">free(texture-&gt;imageData);<span class="kom">// Uvolnìní pamìti</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">fclose(fTGA);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Formát TGA se od formátu OpenGL li¹í tím, ¾e má v pixelech pøehozené R a B slo¾ky barvy (BGR místo RGB). Musíme tedy zamìnit první a tøetí byte v ka¾dém pixelu. Abychom tuto operace urychlili, provedeme tøi binární operace XOR. Výsledek je stejný jako pøi pou¾ití pomocné promìnné.</p>

<p class="src1"><span class="kom">// Pøevod BGR na RGB</span></p>
<p class="src1">for(GLuint cswap = 0; cswap &lt; (int)tga.imageSize; cswap += tga.bytesPerPixel)</p>
<p class="src1">{</p>
<p class="src2">texture-&gt;imageData[cswap] ^= texture-&gt;imageData[cswap+2] ^=</p>
<p class="src2">texture-&gt;imageData[cswap] ^= texture-&gt;imageData[cswap+2];</p>
<p class="src1">}</p>

<p>Obrázek jsme úspì¹nì nahráli, tak¾e zavøeme soubor a vrácením true oznámíme úspìch.</p>

<p class="src1">fclose(fTGA);<span class="kom">// Zavøení souboru</span></p>
<p class="src1">return true;<span class="kom">// Úspìch</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pamì» dat obrázku se uvolòuje a¾ po vytvoøení textury</span></p>
<p class="src0">}</p>

<p>Nyní pøistoupíme k nahrávání obrázku komprimovaného metodou RLE (RunLength Encoded). Zaèátek je stejný jako u nekomprimovaného obrázku - naèteme vý¹ku, ¹íøku a barevnou hloubku, o¹etøíme neplatné hodnoty a spoèítáme velikost potøebné pamìti, kterou opìt alokujeme. V¹imnìte si, ¾e velikost po¾adované pamìti je taková, aby do ní mohla být ulo¾ena data PO DEKOMPRIMOVÁNÍ, ne pøed dekomprimováním.</p>

<p class="src0">bool LoadCompressedTGA(Texture* texture, char* filename, FILE* fTGA)<span class="kom">// Nahraje komprimovaný obrázek</span></p>
<p class="src0">{ </p>
<p class="src1">if(fread(tga.header, sizeof(tga.header), 1, fTGA) == 0)<span class="kom">// ©est u¾iteèných bytù</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not read info header&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">texture-&gt;width = tga.header[1] * 256 + tga.header[0];<span class="kom">// ©íøka</span></p>
<p class="src1">texture-&gt;height = tga.header[3] * 256 + tga.header[2];<span class="kom">// Vý¹ka</span></p>
<p class="src1">texture-&gt;bpp = tga.header[4];<span class="kom">// Barevná hloubka v bitech</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Kopírování dat do struktury obrázku</span></p>
<p class="src1">tga.Width = texture-&gt;width;</p>
<p class="src1">tga.Height = texture-&gt;height;</p>
<p class="src1">tga.Bpp = texture-&gt;bpp;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Platné hodnoty?</span></p>
<p class="src1">if((texture-&gt;width &lt;= 0) || (texture-&gt;height &lt;= 0) || ((texture-&gt;bpp != 24) &amp;&amp; (texture-&gt;bpp != 32)))</p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Invalid texture information&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(texture-&gt;bpp == 24)<span class="kom">// 24 bitový obrázek?</span></p>
<p class="src1">{</p>
<p class="src2">texture-&gt;type = GL_RGB;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// 32 bitový obrázek</span></p>
<p class="src1">{</p>
<p class="src2">texture-&gt;type = GL_RGBA;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">tga.bytesPerPixel = (tga.Bpp / 8);<span class="kom">// BYTY na pixel</span></p>
<p class="src1">tga.imageSize = (tga.bytesPerPixel * tga.Width * tga.Height);<span class="kom">// Velikost pamìti</span></p>
<p class="src"></p>
<p class="src1">texture-&gt;imageData = (GLubyte *)malloc(tga.imageSize);<span class="kom">// Alokace pamìti pro data (po dekomprimování)</span></p>
<p class="src"></p>
<p class="src1">if(texture-&gt;imageData == NULL)<span class="kom">// Alokace neúspì¹ná</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not allocate memory for image&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2">fclose(fTGA);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Dále potøebujeme zjistit pøesný poèet pixelù, ze kterých je obrázek slo¾en. Jednodu¹e vynásobíme vý¹ku obrázku se ¹íøkou. Také musíme znát, na kterém pixelu se právì nacházíme a kam do pamìti zapisujeme.</p>

<p class="src1">GLuint pixelcount = tga.Height * tga.Width;<span class="kom">// Poèet pixelù</span></p>
<p class="src1">GLuint currentpixel = 0;<span class="kom">// Aktuální naèítaný pixel</span></p>
<p class="src1">GLuint currentbyte = 0;<span class="kom">// Aktuální naèítaný byte</span></p>

<p>Alokujeme pomocné pole tøí nebo ètyø bytù (podle barevné hloubky) k ulo¾ení jednoho pixelu. Pøekl.: Mìla by se testovat správnost alokace pamìti!</p>

<p class="src1">GLubyte* colorbuffer = (GLubyte *)malloc(tga.bytesPerPixel);<span class="kom">// Pamì» pro jeden pixel</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pøekl.: Test úspì¹nosti alokace pamìti !!!</span></p>
<p class="src1"><span class="kom">// if(colorbuffer == NULL)// Alokace neúspì¹ná</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// MessageBox(NULL, &quot;Could not allocate memory for color buffer&quot;, &quot;ERROR&quot;, MB_OK);</span></p>
<p class="src2"><span class="kom">// fclose(fTGA);</span></p>
<p class="src2"><span class="kom">// return false;</span></p>
<p class="src1"><span class="kom">// }</span></p>

<p>V hlavním cyklu deklarujeme promìnnou k ulo¾ení bytu hlavièky, který definuje, jestli je následující sekce obrázku ve formátu RAW nebo RLE a jak dlouhá je. Pokud je byte hlavièky men¹í nebo roven 127, jedná se o RAW hlavièku. Hodnota v ní ulo¾ená, urèuje poèet pixelù mínus jedna, které vzápìtí naèteme a zkopírujeme do pamìti. Po tìchto pixelech se v souboru vyskytuje dal¹í byte hlavièky. Pokud je byte hlavièky vìt¹í ne¾ 127, pøedstavuje toto èíslo (zmen¹ené o 127), kolikrát se má následující pixel v dekomprimovaném obrázku opakovat. Hned po nìm se bude vyskytovat dal¹í hlavièkový byte. Naèteme hodnoty tohoto pixelu a zkopírujeme ho do imageData tolikrát, kolikrát potøebujeme.</p>

<p>Podstatu komprese RLE tedy u¾ znáte, podívejme se na kód. Jak jsem ji¾ zmínil, zalo¾íme cyklus pøes celý soubor a pokusíme se naèíst byte první hlavièky.</p>

<p class="src1">do<span class="kom">// Prochází celý soubor</span></p>
<p class="src1">{</p>
<p class="src2">GLubyte chunkheader = 0;<span class="kom">// Byte hlavièky</span></p>
<p class="src"></p>
<p class="src2">if(fread(&amp;chunkheader, sizeof(GLubyte), 1, fTGA) == 0)<span class="kom">// Naète byte hlavièky</span></p>
<p class="src2">{</p>
<p class="src3">MessageBox(NULL, &quot;Could not read RLE header&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src3">if(fTGA != NULL)</p>
<p class="src3">{</p>
<p class="src4">fclose(fTGA);</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if(texture-&gt;imageData != NULL)</p>
<p class="src3">{</p>
<p class="src4">free(texture-&gt;imageData);</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Pøekl.: Uvolnìní dynamické pamìti !!!</span></p>
<p class="src3"><span class="kom">// if(colorbuffer != NULL)</span></p>
<p class="src3"><span class="kom">// {</span></p>
<p class="src4"><span class="kom">// free(colorbuffer);</span></p>
<p class="src3"><span class="kom">// }</span></p>
<p class="src"></p>
<p class="src3">return false;</p>
<p class="src2">}</p>

<p>Pokud se jedná o RAW hlavièku, pøièteme k bytu jednièku, abychom získali poèet pixelù následujících po hlavièce. Potom zalo¾íme dal¹í cyklus, který naèítá v¹echny po¾adovaného pixely do pomocného pole colorbuffer a vzápìtí je ve správném formátu ukládá do imageData.</p>

<p class="src2">if(chunkheader &lt; 128)<span class="kom">// RAW èást obrázku</span></p>
<p class="src2">{</p>
<p class="src3">chunkheader++;<span class="kom">// Poèet pixelù v sekci pøed výskytem dal¹ího bytu hlavièky</span></p>
<p class="src"></p>
<p class="src3">for(short counter = 0; counter &lt; chunkheader; counter++)<span class="kom">// Jednotlivé pixely</span></p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// Naèítání po jednom pixelu</span></p>
<p class="src4">if(fread(colorbuffer, 1, tga.bytesPerPixel, fTGA) != tga.bytesPerPixel)</p>
<p class="src4">{</p>
<p class="src5">MessageBox(NULL, &quot;Could not read image data&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src5">if(fTGA != NULL)</p>
<p class="src5">{</p>
<p class="src6">fclose(fTGA);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(colorbuffer != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(colorbuffer);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(texture-&gt;imageData != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(texture-&gt;imageData);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">return false;</p>
<p class="src4">}</p>

<p>Pøi kopírování do imageData prohodíme poøadí bytù z formátu BGR na RGB. Pokud je v obrázku i alfa kanál, zkopírujeme i ètvrtý byte. Abychom se pøesunuli na dal¹í pixel popø. byte hlavièky, zvìt¹íme aktuální byte o barevnou hloubku (+3 nebo +4). Inkrementujeme také poèet naètených pixelù.</p>

<p class="src4"><span class="kom">// Zápis do pamìti, prohodí R a B slo¾ku barvy</span></p>
<p class="src4">texture-&gt;imageData[currentbyte] = colorbuffer[2];</p>
<p class="src4">texture-&gt;imageData[currentbyte + 1] = colorbuffer[1];</p>
<p class="src4">texture-&gt;imageData[currentbyte + 2] = colorbuffer[0];</p>
<p class="src"></p>
<p class="src4">if(tga.bytesPerPixel == 4)<span class="kom">// 32 bitový obrázek?</span></p>
<p class="src4">{</p>
<p class="src5">texture-&gt;imageData[currentbyte + 3] = colorbuffer[3];<span class="kom">// Kopírování alfy</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">currentbyte += tga.bytesPerPixel;<span class="kom">// Aktualizuje byte</span></p>
<p class="src4">currentpixel++;<span class="kom">// Pøesun na dal¹í pixel</span></p>

<p>Zjistíme, jestli je poøadová èíslo aktuálního pixelu vìt¹í ne¾ celkový poèet pixelù. Pokud ano, je soubor obrázku po¹kozen nebo je v nìm nìkde chyba. Jak jsme na to pøi¹li? Máme naèítat dal¹í pixel, ale defakto je u¾ máme v¹echny naètené, proto¾e aktuální hodnota je vìt¹í ne¾ maximální. Nestaèila by alokovaná pamì» pro dekomprimovanou verzi obrázku. Tuto skuteènost musíme ka¾dopádnì o¹etøit.</p>

<p class="src4">if(currentpixel &gt; pixelcount)<span class="kom">// Jsme za hranicí obrázku?</span></p>
<p class="src4">{</p>
<p class="src5">MessageBox(NULL, &quot;Too many pixels read&quot;, &quot;ERROR&quot;, NULL);</p>
<p class="src"></p>
<p class="src5">if(fTGA != NULL)</p>
<p class="src5">{</p>
<p class="src6">fclose(fTGA);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(colorbuffer != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(colorbuffer);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(texture-&gt;imageData != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(texture-&gt;imageData);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">return false;</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Vyøe¹ili jsme èást RAW, nyní implementujeme sekci RLE. Ze v¹eho nejdøíve od bytu hlavièky odeèteme èíslo 127, abychom získali kolikrát se má následující pixel opakovat.</p>

<p class="src2">else<span class="kom">// RLE èást obrázku</span></p>
<p class="src2">{</p>
<p class="src3">chunkheader -= 127;<span class="kom">// Poèet pixelù v sekci</span></p>

<p>Naèteme jeden pixel po hlavièce a potom ho po¾adovanì-krát vlo¾íme do imageData. Opìt zamìòujeme formát BGR za RGB. Stejnì jako minule inkrementujeme aktuální byte i pixel a o¹etøujeme pøeteèení.</p>

<p class="src3">if(fread(colorbuffer, 1, tga.bytesPerPixel, fTGA) != tga.bytesPerPixel)<span class="kom">// Naète jeden pixel</span></p>
<p class="src3">{</p>
<p class="src4">MessageBox(NULL, &quot;Could not read from file&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src4">if(fTGA != NULL)</p>
<p class="src4">{</p>
<p class="src5">fclose(fTGA);</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if(colorbuffer != NULL)</p>
<p class="src4">{</p>
<p class="src5">free(colorbuffer);</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if(texture-&gt;imageData != NULL)</p>
<p class="src4">{</p>
<p class="src5">free(texture-&gt;imageData);</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">return false;</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">for(short counter = 0; counter &lt; chunkheader; counter++)<span class="kom">// Kopírování pixelu</span></p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// Zápis do pamìti, prohodí R a B slo¾ku barvy</span></p>
<p class="src4">texture-&gt;imageData[currentbyte] = colorbuffer[2];</p>
<p class="src4">texture-&gt;imageData[currentbyte + 1] = colorbuffer[1];</p>
<p class="src4">texture-&gt;imageData[currentbyte + 2] = colorbuffer[0];</p>
<p class="src"></p>
<p class="src4">if(tga.bytesPerPixel == 4)<span class="kom">// 32 bitový obrázek?</span></p>
<p class="src4">{</p>
<p class="src5">texture-&gt;imageData[currentbyte + 3] = colorbuffer[3];<span class="kom">// Kopírování alfy</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">currentbyte += tga.bytesPerPixel;<span class="kom">// Aktualizuje byte</span></p>
<p class="src4">currentpixel++;<span class="kom">// Pøesun na dal¹í pixel</span></p>
<p class="src"></p>
<p class="src4">if(currentpixel &gt; pixelcount)<span class="kom">// Jsme za hranicí obrázku?</span></p>
<p class="src4">{</p>
<p class="src5">MessageBox(NULL, &quot;Too many pixels read&quot;, &quot;ERROR&quot;, NULL);</p>
<p class="src"></p>
<p class="src5">if(fTGA != NULL)</p>
<p class="src5">{</p>
<p class="src6">fclose(fTGA);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(colorbuffer != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(colorbuffer);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(texture-&gt;imageData != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(texture-&gt;imageData);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">return false;</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Hlavní cyklus opakujeme tak dlouho, dokud v souboru zbývají nenaètené pixely. Po konci loadingu soubor zavøeme a vrácením true indikujeme úspìch.</p>

<p class="src1">} while(currentpixel &lt; pixelcount);<span class="kom">// Pokraèuj dokud zbývají pixely</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pøekl.: Uvolnìní dynamické pamìti !!!</span></p>
<p class="src1"><span class="kom">// if(colorbuffer != NULL)</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// free(colorbuffer);</span></p>
<p class="src1"><span class="kom">// }</span></p>
<p class="src"></p>
<p class="src1">fclose(fTGA);<span class="kom">// Zavøení souboru</span></p>
<p class="src1">return true;<span class="kom">// Úspìch</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pamì» dat obrázku se uvolòuje a¾ po vytvoøení textury</span></p>
<p class="src0">}</p>

<p>Nyní jsou data obrázku pøipravena pro vytvoøení textury a to u¾ jistì zvládnete sami. V tomto tutoriálu nám ¹lo pøedev¹ím o nahrávání TGA obrázkù. Ukázkové demo bylo vytvoøeno jen proto, abyste vidìli, ¾e kód opravdu funguje.</p>

<p>A jak je to s úspì¹ností komprimace metody RLE? Je jasné, ¾e nejmen¹í pamì» bude zabírat obrázek s rozsáhlými plochami stejných pixelù (na øádcích). Pokud chcete èísla, tak si vezmeme na pomoc obrázky pou¾ité v tomto demu: oba jsou 128x128 pixelù veliké, nekomprimovaný zabírá na disku 48,0 kB a komprimovaný pouze 5,29 kB. Na obou je sice nìco jiného, ale devítinásobné zmen¹ení velikosti mluví za v¹e.</p>

<p class="autor">napsal: Evan Pipho - Terminate <?VypisEmail('terminate@gdnmail.net');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson33.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson33_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson33.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson33.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson33.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Schubert_P@Yahoo.de">Patrick Schubert</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson33.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson33.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(33);?>
<?FceNeHeOkolniLekce(33);?>

<?
include 'p_end.php';
?>
