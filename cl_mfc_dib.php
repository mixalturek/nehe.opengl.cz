<?
$g_title = 'CZ NeHe OpenGL - Kopírování OpenGL okna do DIBu';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Kopírování OpenGL okna do DIBu</h1>

<p class="nadpis_clanku">Obèas potøebujeme sejmout obrazovku v OpenGL a poté s ní pracovat jako s obyèejnou bitmapou. V tomto èlánku vám uká¾i získání obsahu OpenGL okna a jeho ulo¾ení do DIBu ve formì nekomprimované bitmapy. Jediný omezením mù¾e být 24 bitová barevná hloubka obrazovky.</p>

<p>Celý kód èlánku pojmeme jako jedinou funkci, která bude zaji¹»ovat v¹echny potøebné operace.</p>

<p class="src0">HANDLE COpenGLView::CreateDIB()<span class="kom">// Sejme OpenGL obrazovku a vytvoøí z ní DIB, jeho¾ handle vrátí</span></p>
<p class="src0">{</p>
<p class="src1">BeginWaitCursor();<span class="kom">// Pøepne kurzor na pøesýpací hodiny</span></p>

<p>V první fázi potøebujeme získat rozmìry klientské oblasti okna. Tj. velikost zobrazované scény.</p>

<p class="src1">CRect rect;<span class="kom">// Obdélník</span></p>
<p class="src1">GetClientRect(&amp;rect);<span class="kom">// Grabování velikosti klientské oblasti</span></p>
<p class="src1">CSize size(rect.Width(), rect.Height());<span class="kom">// ©íøka a vý¹ka okna</span></p>
<p class="src1">TRACE(&quot;Klientská plocha: (%d; %d)\n&quot;, size.cx, size.cy);<span class="kom">// Pro ladìní</span></p>

<p>Øádky musejí být zarovnávány na 32 bytù, za pøedpokladu 24 bitù na pixel, tak¾e co je navíc, odstøihneme.</p>

<p class="src1">size.cx -= size.cx % 4;</p>
<p class="src1">TRACE(&quot;Koneèná klientská plocha: (%d; %d)\n&quot;, size.cx, size.cy);<span class="kom">// Pro ladìní</span></p>

<p>Nyní potøebujeme alokovat pamì» pro jednotlivé pixely. Poèet pixelù získáme velice jednodu¹e. Vynásobíme vý¹ku obrázku se ¹íøkou. Abychom dostali potøebnou pamì» musíme je¹tì násobit tøemi, proto¾e má ka¾dý pixel 3 byty (RGB).</p>

<p class="src1">int NbBytes = 3 * size.cx * size.cy;<span class="kom">// Velikost pamìti</span></p>
<p class="src1">unsigned char *pPixelData = new unsigned char[NbBytes];<span class="kom">// Alokace pamìti</span></p>

<p>Pomocí funkce glReadPixels() zkopírujeme pixely z obrazovky do právì alokované pamìti. Pøedávané parametry vyjadøují x, y souøadnice levého dolního rohu, ¹íøku a vý¹ku kopírované oblasti, barevnou hloubku (GL_RGB = 24 bitù, GL_RGBA = 32 bitù), typ pixelových dat a ukazatel do pamìti, kam se mají data nakopírovat.</p>

<p class="src1">::glReadPixels(0, 0, size.cx, size.cy, GL_RGB, GL_UNSIGNED_BYTE, pPixelData);<span class="kom">// Kopírovaní pixelù</span></p>

<p>Deklarujeme a vyplníme hlavièku DIBu.</p>

<p class="src1">BITMAPINFOHEADER header;<span class="kom">// Hlavièka DIBu</span></p>
<p class="src"></p>
<p class="src1">header.biWidth = size.cx;<span class="kom">// ©íøka</span></p>
<p class="src1">header.biHeight = size.cy;<span class="kom">// Vý¹ka</span></p>
<p class="src1">header.biSizeImage = NbBytes;<span class="kom">// Poèet bytù obrázku</span></p>
<p class="src1">header.biSize = sizeof(BITMAPINFOHEADER);<span class="kom">// Velikost této struktury</span></p>
<p class="src1">header.biPlanes = 1;<span class="kom">// V¾dy jedna</span></p>
<p class="src1">header.biBitCount =  24;<span class="kom">// Barevná hloubka</span></p>
<p class="src1">header.biCompression = BI_RGB;<span class="kom">// Typ komprese -&gt; nekomprimovaná</span></p>
<p class="src1">header.biXPelsPerMeter = 0;</p>
<p class="src1">header.biYPelsPerMeter = 0;</p>
<p class="src1">header.biClrUsed = 0;</p>
<p class="src1">header.biClrImportant = 0; </p>

<p>Vygenerujeme handle globální pamìti pro DIB o velikosti hlavièky seètené s poètem bytù.</p>

<p class="src1">HANDLE handle = (HANDLE)::GlobalAlloc(GHND, sizeof(BITMAPINFOHEADER) + NbBytes);</p>

<p>Pokud se ukazatel nerovná NULL byla alokace úspì¹ná. V takovém pøípadì uzamkneme handle a tím na nìj zároveò získáme ukazatel. Potom zkopírujeme hlavièku i data a odemkneme handle.</p>

<p class="src1">if(handle != NULL)<span class="kom">// OK</span></p>
<p class="src1">{</p>
<p class="src2">char *pData = (char *) ::GlobalLock((HGLOBAL) handle);<span class="kom">// Uzamkne handle</span></p>
<p class="src"></p>
<p class="src2">memcpy(pData, &amp;header, sizeof(BITMAPINFOHEADER));<span class="kom">// Zkopíruje hlavièku</span></p>
<p class="src2">memcpy(pData + sizeof(BITMAPINFOHEADER), pPixelData, NbBytes);<span class="kom">// Zkopíruje data</span></p>
<p class="src"></p>
<p class="src2">::GlobalUnlock((HGLOBAL)handle);<span class="kom">// Odemkne handle</span></p>
<p class="src"></p>
<p class="src2">delete [] pPixelData;<span class="kom">// Uvolnìní pamìti pro data</span></p>

<p>Nastavíme pùvodní tvar kurzoru a vrátíme právì vytvoøené handle DIBu.</p>

<p class="src2">EndWaitCursor();<span class="kom">// Pùvodní tvar kurzoru</span></p>
<p class="src"></p>
<p class="src2">return handle;<span class="kom">// Vrátí handle DIBu</span></p>
<p class="src1">}</p>

<p>Pøi neúspìchu vrátíme NULL</p>

<p class="src1">delete [] pPixelData;<span class="kom">// Uvolnìní pamìti pro data</span></p>
<p class="src"></p>
<p class="src1">EndWaitCursor();<span class="kom">// Pùvodní tvar kurzoru</span></p>
<p class="src1">return NULL;<span class="kom">// DIB nebyl nahrán</span></p>
<p class="src0">}</p>

<p>A¾ pøestaneme DIB pou¾ívat musíme jej smazat pomocí funkce GlobalFree(). Jedinou výjimkou by bylo, kdybychom tento DIB pøedali do schránky, která by se o smazání postarala sama.</p>

<p>A nakonec ukázka pou¾ití této funkce (uvnitø nìjaké funkce tøídy okna).</p>

<p class="src0">HANDLE hDib = CreateDIB();<span class="kom">// Vytvoøí DIB</span></p>
<p class="src"></p>
<p class="src0">if (hDib)<span class="kom">// Byl vytvoøen?</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Práce s DIBem</span></p>
<p class="src"></p>
<p class="src1">::GlobalFree(hDib);<span class="kom">// Nakonec ho sma¾eme</span></p>
<p class="src0">}</p>

<p>Pokud nepotøebujete získat handle DIBu, ale staèí vám mít jen DC zobrazované OpenGL scény, je to velmi jednoduché. Získejte DC okna ve kterém se OpenGL scéna vykresluje pomocí funkce GetDC().</p>

<p class="autor">napsal: Milan Turek - Woq <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojových kódù</li>
</ul>

<?
include 'p_end.php';
?>
