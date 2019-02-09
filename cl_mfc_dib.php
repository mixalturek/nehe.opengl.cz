<?
$g_title = 'CZ NeHe OpenGL - Kop�rov�n� OpenGL okna do DIBu';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Kop�rov�n� OpenGL okna do DIBu</h1>

<p class="nadpis_clanku">Ob�as pot�ebujeme sejmout obrazovku v OpenGL a pot� s n� pracovat jako s oby�ejnou bitmapou. V tomto �l�nku v�m uk�i z�sk�n� obsahu OpenGL okna a jeho ulo�en� do DIBu ve form� nekomprimovan� bitmapy. Jedin� omezen�m m��e b�t 24 bitov� barevn� hloubka obrazovky.</p>

<p>Cel� k�d �l�nku pojmeme jako jedinou funkci, kter� bude zaji��ovat v�echny pot�ebn� operace.</p>

<p class="src0">HANDLE COpenGLView::CreateDIB()<span class="kom">// Sejme OpenGL obrazovku a vytvo�� z n� DIB, jeho� handle vr�t�</span></p>
<p class="src0">{</p>
<p class="src1">BeginWaitCursor();<span class="kom">// P�epne kurzor na p�es�pac� hodiny</span></p>

<p>V prvn� f�zi pot�ebujeme z�skat rozm�ry klientsk� oblasti okna. Tj. velikost zobrazovan� sc�ny.</p>

<p class="src1">CRect rect;<span class="kom">// Obd�ln�k</span></p>
<p class="src1">GetClientRect(&amp;rect);<span class="kom">// Grabov�n� velikosti klientsk� oblasti</span></p>
<p class="src1">CSize size(rect.Width(), rect.Height());<span class="kom">// ���ka a v��ka okna</span></p>
<p class="src1">TRACE(&quot;Klientsk� plocha: (%d; %d)\n&quot;, size.cx, size.cy);<span class="kom">// Pro lad�n�</span></p>

<p>��dky musej� b�t zarovn�v�ny na 32 byt�, za p�edpokladu 24 bit� na pixel, tak�e co je nav�c, odst�ihneme.</p>

<p class="src1">size.cx -= size.cx % 4;</p>
<p class="src1">TRACE(&quot;Kone�n� klientsk� plocha: (%d; %d)\n&quot;, size.cx, size.cy);<span class="kom">// Pro lad�n�</span></p>

<p>Nyn� pot�ebujeme alokovat pam� pro jednotliv� pixely. Po�et pixel� z�sk�me velice jednodu�e. Vyn�sob�me v��ku obr�zku se ���kou. Abychom dostali pot�ebnou pam� mus�me je�t� n�sobit t�emi, proto�e m� ka�d� pixel 3 byty (RGB).</p>

<p class="src1">int NbBytes = 3 * size.cx * size.cy;<span class="kom">// Velikost pam�ti</span></p>
<p class="src1">unsigned char *pPixelData = new unsigned char[NbBytes];<span class="kom">// Alokace pam�ti</span></p>

<p>Pomoc� funkce glReadPixels() zkop�rujeme pixely z obrazovky do pr�v� alokovan� pam�ti. P�ed�van� parametry vyjad�uj� x, y sou�adnice lev�ho doln�ho rohu, ���ku a v��ku kop�rovan� oblasti, barevnou hloubku (GL_RGB = 24 bit�, GL_RGBA = 32 bit�), typ pixelov�ch dat a ukazatel do pam�ti, kam se maj� data nakop�rovat.</p>

<p class="src1">::glReadPixels(0, 0, size.cx, size.cy, GL_RGB, GL_UNSIGNED_BYTE, pPixelData);<span class="kom">// Kop�rovan� pixel�</span></p>

<p>Deklarujeme a vypln�me hlavi�ku DIBu.</p>

<p class="src1">BITMAPINFOHEADER header;<span class="kom">// Hlavi�ka DIBu</span></p>
<p class="src"></p>
<p class="src1">header.biWidth = size.cx;<span class="kom">// ���ka</span></p>
<p class="src1">header.biHeight = size.cy;<span class="kom">// V��ka</span></p>
<p class="src1">header.biSizeImage = NbBytes;<span class="kom">// Po�et byt� obr�zku</span></p>
<p class="src1">header.biSize = sizeof(BITMAPINFOHEADER);<span class="kom">// Velikost t�to struktury</span></p>
<p class="src1">header.biPlanes = 1;<span class="kom">// V�dy jedna</span></p>
<p class="src1">header.biBitCount =  24;<span class="kom">// Barevn� hloubka</span></p>
<p class="src1">header.biCompression = BI_RGB;<span class="kom">// Typ komprese -&gt; nekomprimovan�</span></p>
<p class="src1">header.biXPelsPerMeter = 0;</p>
<p class="src1">header.biYPelsPerMeter = 0;</p>
<p class="src1">header.biClrUsed = 0;</p>
<p class="src1">header.biClrImportant = 0; </p>

<p>Vygenerujeme handle glob�ln� pam�ti pro DIB o velikosti hlavi�ky se�ten� s po�tem byt�.</p>

<p class="src1">HANDLE handle = (HANDLE)::GlobalAlloc(GHND, sizeof(BITMAPINFOHEADER) + NbBytes);</p>

<p>Pokud se ukazatel nerovn� NULL byla alokace �sp�n�. V takov�m p��pad� uzamkneme handle a t�m na n�j z�rove� z�sk�me ukazatel. Potom zkop�rujeme hlavi�ku i data a odemkneme handle.</p>

<p class="src1">if(handle != NULL)<span class="kom">// OK</span></p>
<p class="src1">{</p>
<p class="src2">char *pData = (char *) ::GlobalLock((HGLOBAL) handle);<span class="kom">// Uzamkne handle</span></p>
<p class="src"></p>
<p class="src2">memcpy(pData, &amp;header, sizeof(BITMAPINFOHEADER));<span class="kom">// Zkop�ruje hlavi�ku</span></p>
<p class="src2">memcpy(pData + sizeof(BITMAPINFOHEADER), pPixelData, NbBytes);<span class="kom">// Zkop�ruje data</span></p>
<p class="src"></p>
<p class="src2">::GlobalUnlock((HGLOBAL)handle);<span class="kom">// Odemkne handle</span></p>
<p class="src"></p>
<p class="src2">delete [] pPixelData;<span class="kom">// Uvoln�n� pam�ti pro data</span></p>

<p>Nastav�me p�vodn� tvar kurzoru a vr�t�me pr�v� vytvo�en� handle DIBu.</p>

<p class="src2">EndWaitCursor();<span class="kom">// P�vodn� tvar kurzoru</span></p>
<p class="src"></p>
<p class="src2">return handle;<span class="kom">// Vr�t� handle DIBu</span></p>
<p class="src1">}</p>

<p>P�i ne�sp�chu vr�t�me NULL</p>

<p class="src1">delete [] pPixelData;<span class="kom">// Uvoln�n� pam�ti pro data</span></p>
<p class="src"></p>
<p class="src1">EndWaitCursor();<span class="kom">// P�vodn� tvar kurzoru</span></p>
<p class="src1">return NULL;<span class="kom">// DIB nebyl nahr�n</span></p>
<p class="src0">}</p>

<p>A� p�estaneme DIB pou��vat mus�me jej smazat pomoc� funkce GlobalFree(). Jedinou v�jimkou by bylo, kdybychom tento DIB p�edali do schr�nky, kter� by se o smaz�n� postarala sama.</p>

<p>A nakonec uk�zka pou�it� t�to funkce (uvnit� n�jak� funkce t��dy okna).</p>

<p class="src0">HANDLE hDib = CreateDIB();<span class="kom">// Vytvo�� DIB</span></p>
<p class="src"></p>
<p class="src0">if (hDib)<span class="kom">// Byl vytvo�en?</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Pr�ce s DIBem</span></p>
<p class="src"></p>
<p class="src1">::GlobalFree(hDib);<span class="kom">// Nakonec ho sma�eme</span></p>
<p class="src0">}</p>

<p>Pokud nepot�ebujete z�skat handle DIBu, ale sta�� v�m m�t jen DC zobrazovan� OpenGL sc�ny, je to velmi jednoduch�. Z�skejte DC okna ve kter�m se OpenGL sc�na vykresluje pomoc� funkce GetDC().</p>

<p class="autor">napsal: Milan Turek - Woq <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojov�ch k�d�</li>
</ul>

<?
include 'p_end.php';
?>
