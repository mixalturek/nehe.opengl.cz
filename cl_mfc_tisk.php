<?
$g_title = 'CZ NeHe OpenGL - Tisk a náhled pøed tiskem OpenGL scény v MFC';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Tisk a náhled pøed tiskem OpenGL scény v MFC</h1>

<p class="nadpis_clanku">Obalení OpenGL tøídami MFC nám dovolí vyu¾ít obou výhod API: rychlého vykreslování a elegantního rozhraní. Nicménì díky faktu, ¾e mnoho ovladaèù tiskáren nepracuje s API funkcí SetPixelFormat(), není mo¾né tisknout OpenGL scénu pøímo na tiskárnu. Velmi roz¹íøená technika je vykreslit OpenGL scénu do DIBu a poté ji zkopírovat do DC pro tisk nebo náhled. V tomto èlánku uvidíte jak to udìlat.</p>

<p>Pøi vysvìtlování pou¾iji klasickou architekturu dokument/pohled. Tøída CMyView je potomkem tøídy COpenGLView, ve které je udìlána ve¹kerá inicializace OpenGL. V této nové tøídì implementujeme tisk a náhled OpenGL scény. Tisk na tiskárnu nebo do náhledu se ve tøídì pohledu provádí pomocí virtuální funkce OnPrint(). Upravíme ji následujícím zpùsobem.</p>

<p class="src0">void CMyView::OnPrint(CDC* pDC, CPrintInfo* pInfo)</p>
<p class="src0">{</p>
<p class="src1">OnPrint1(pDC, pInfo, this);<span class="kom">// Pøíprava tisku scény</span></p>
<p class="src1">OnDraw(pDC);<span class="kom">// Vykreslení scény je spoleèné pro výstup do okna, tisk i náhled</span></p>
<p class="src1">OnPrint2(pDC);<span class="kom">// Vlastní výstup na tiskárnu a úklid po tisku</span></p>
<p class="src0">}</p>

<p>Funkce OnPrint1() je vytvoøena pro renderování mimo obrazovku. Hlavní úkoly této funkce jsou vytvoøit DIB a pamì»ové DC i RC. Pamì»ové RC bude pozdìji pou¾ito pro vykreslení OpenGL scény mimo obrazovku. Funkce OnDraw() je standardní virtuální funkce tøidy pohledu ve které provádíme vlastní vykreslení scény a to jak na tiskárnu a do náhledu, tak na obrazovku. Funkce OnPrint2() zkopíruje získaný DIB OpenGL scény na tiskárnu nebo do náhledu a provede úklid, tj. uvolnìní DIBu a pamì»ových kontextù.</p>

<h2>Pøíprava OpenGL pro tisk mimo obrazovku - OnPrint1()</h2>

<ol>
<li>Vypoèítáme velikost DIBu pro tisk a náhled, která závisí na velikosti zobrazovacího zaøízení. Pro náhled pou¾ijeme rozli¹ení tiskárny. Pro tisk pou¾ijeme redukované rozli¹ení tiskárny. V ideálním pøípadì, pokud by velikost potøebné pamìti a rychlost nebyla problém bychom pou¾ili plné rozli¹ení obrazovky. Nicménì pro tiskárnu s rozli¹ením 720 DPI a pou¾itím papíru o velikosti &quot;letter&quot; pøesáhne pamì», potøebná pro DIB sekci, snadno 100MB. Proto redukujeme rozli¹ení tisku.</li>
<li>Pro vytvoøení DIB sekce o velikosti zmínìné døíve voláme funkci CreateDIBSection().</li>
<li>Vytvoøení pamì»ového DC a jeho pøipojení k DIB sekci. Voláním Win32 funkce CreateCompatibleDC() vytvoøíme pamì»ové DC, potom do nìj vybereme DIB sekci.</li>
<li>Nastavení pixel formátu pamì»ového DC je podobné nastavení pixel formátu obrazovkového DC. Jediný rozdíl je ve flagu, který nastavuje vlastnosti pixelového bufferu. Pro obrazovku nastavujieme PFD_DRAW_TO_WINDOW a PFD_DOUBLEBUFFER, ale pro pamì»ové DC potøebujeme PFD_DRAW_TO_BITMAP.</li>
<li>Vytvoøení pamì»ového RC. Pou¾ijeme døíve vytvoøené pamì»ové DC pro vytvoøení pamì»ového RC pro OpenGL mimoobrazovkového renderingu. Po skonèení tisku budou uvolnìny (tj. ve funkci OnPrint2).</li>
<li>Staré DC a RC pro vykreslování na obrazovku si musíme ulo¾it, proto¾e je po skonèení tisku musíme opìt nastavit jako aktuální.</li>
<li>Funkce wglMakeCurrent() nastaví pamì»ové RC jako aktuální. Od teï bude OpenGL kreslit do pamì»ového, ne obrazovkového, RC. Nicménì nejdøíve musíme inicializovat pamì»ové RC stejnì jako jsme to dìlali s obrazovkovým.</li>
<li>Inicializace pamì»ového RC. Pøe vlastním kreslením do pamì»ového RC je¹tì musíme nastavit velikost plochy do které se mù¾e kreslit (velikost DIBu) a perspektivu.</li>
<li>Vytvoøení display listù pro pamì»ové RC. Pokud pou¾íváte display listy, tak je musíte znovu vytvoøit pro novì vytvoøené pamì»ové RC. Pamatujte si, ¾e display listy nejsou znovupou¾itelné pro rùzná RC.</li>
</ol>

<p>Následuje zdrojový kód funkce CMyView::OnPrint1().</p>

<p class="src0">void CMyView::OnPrint1(CDC* pDC, CPrintInfo* pInfo, CView* pView)</p>
<p class="src0">{</p>
<p class="src"></p>

<h4>1. Vypoèítáme velikost DIBu pro tisk a náhled</h4>

<p class="src1">CRect rcClient;</p>
<p class="src1">pView->GetClientRect(&amp;rcClient);<span class="kom">// Zji¹tìní velikosti okna</span></p>
<p class="src1">float fClientRatio = float(rcClient.Height())/rcClient.Width();<span class="kom">// Pomìr velikostí stran okna</span></p>

<p>Zjistíme velikost stránky. CSize m_szPage je pomocná èlenská promìnná tøídy CMyView.</p>

<p class="src1">m_szPage.cx = pDC->GetDeviceCaps(HORZRES);</p>
<p class="src1">m_szPage.cy = pDC->GetDeviceCaps(VERTRES);</p>
<p class="src"></p>
<p class="src1">CSize szDIB;</p>

<p>Vìtvíme funkci podle toho, zda je promìnná m_bPreview true (náhled) nebo false (tisk). Pro náhled pou¾ijeme rozli¹ení okna.</p>

<p class="src1">if (pInfo->m_bPreview)<span class="kom">// Náhled</span></p>
<p class="src1">{</p>
<p class="src2">szDIB.cx = rcClient.Width();</p>
<p class="src2">szDIB.cy = rcClient.Height();</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Tisk</span></p>
<p class="src1">{</p>

<p>Pro tisk pou¾ijeme vy¹¹í rozli¹ení. Musíme upravit jeho velikost tak, aby pomìr stran byl stejný jako u okna.</p>

<p class="src2">if (m_szPage.cy > fClientRatio * m_szPage.cx)</p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Plocha okna je ¹ir¹í ne¾ tisknutelná plocha</span></p>
<p class="src3">szDIB.cx = m_szPage.cx;</p>
<p class="src3">szDIB.cy = long(fClientRatio * m_szPage.cx);</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Plocha okna je u¾¹í ne¾ tisknutelná plocha</span></p>
<p class="src3">szDIB.cx = long(float(m_szPage.cy) / fClientRatio);</p>
<p class="src3">szDIB.cy = m_szPage.cy;</p>
<p class="src2">}</p>

<p>Pokud je DIB pamì»ovì pøíli¹ velký, upravíme rozli¹ení. Urèíme maximální velikost DIBu na 20 MB. Mìlo by to záviset na tiskárnì, ale bohu¾el nevím, jak programovì zjistit velikost pamìti tiskárny.</p>

<p class="src2">while (szDIB.cx * szDIB.cy > 20 * 1024 * 1024)</p>
<p class="src2">{</p>
<p class="src3">szDIB.cx = szDIB.cx / 2;</p>
<p class="src3">szDIB.cy = szDIB.cy / 2;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Výpis zji¹tìných hodnot do okna debugeru</span></p>
<p class="src1">TRACE("Buffer size: %d x %d = %6.2f MB\n", szDIB.cx, szDIB.cy, szDIB.cx*szDIB.cy*0.000001);</p>
<p class="src"></p>

<h4>2. Vytvoøení DIB sekce</h4>

<p>BITMAPINFO m_bmi je pomocná èlenská promìnná tøídy CMyView.</p>

<p class="src1">memset(&amp;m_bmi, 0, sizeof(BITMAPINFO));<span class="kom">// Nulování</span></p>
<p class="src1">m_bmi.bmiHeader.biSize = sizeof(BITMAPINFOHEADER);<span class="kom">// Velikost této struktury</span></p>
<p class="src1">m_bmi.bmiHeader.biWidth = szDIB.cx;<span class="kom">// ©íøka DIBu</span></p>
<p class="src1">m_bmi.bmiHeader.biHeight = szDIB.cy;<span class="kom">// Vý¹ka DIBu</span></p>
<p class="src1">m_bmi.bmiHeader.biPlanes = 1;</p>
<p class="src1">m_bmi.bmiHeader.biBitCount = 24;<span class="kom">// Poèet bitù na pixel</span></p>
<p class="src1">m_bmi.bmiHeader.biCompression = BI_RGB;<span class="kom">// Typ komprese (závisí na poètu bitù na pixel) - bez komprese</span></p>
<p class="src1">m_bmi.bmiHeader.biSizeImage = szDIB.cx * szDIB.cy * 3;<span class="kom">// Poèet bytù pro ulo¾ení jednotlivých bodù DIBu (¹íøka*vý¹ka*poèet bytù na bod)</span></p>
<p class="src"></p>
<p class="src1">HDC hDC = ::GetDC(pView->m_hWnd);<span class="kom">// Získání DC okna</span></p>

<p>HANDLE m_hDib je pomocná èlenská promìnná tøídy CMyView.</p>

<p class="src1">m_hDib = ::CreateDIBSection(hDC, &amp;m_bmi, DIB_RGB_COLORS, &amp;m_pBitmapBits, NULL, (DWORD)0);<span class="kom">// Vytvoøíme DIB</span></p>
<p class="src1">::ReleaseDC(pView->m_hWnd, hDC);<span class="kom">// Uvolnìní DC okna</span></p>
<p class="src"></p>

<h4>3. Vytvoøení pamì»ového DC a jeho pøipojení k DIB sekci</h4>

<p>HDC m_hMemDC je pomocná èlenská promìnná tøídy CMyView.</p>

<p class="src1">m_hMemDC = ::CreateCompatibleDC(NULL);<span class="kom">// Vytvoøení pamì»ové DC</span></p>
<p class="src"></p>
<p class="src1">if (!m_hMemDC)<span class="kom">// Pokud se jeho vytvoøení nepovedlo sma¾eme jej a skonèíme</span></p>
<p class="src1">{</p>
<p class="src2">DeleteObject(m_hDib);</p>
<p class="src2">m_hDib = NULL;</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SelectObject(m_hMemDC, m_hDib);<span class="kom">// Vybereme DIB do pamì»ového DC</span></p>
<p class="src"></p>

<h4>4. Nastvení pixel formátu pamì»ového DC</h4>

<p class="src1">if (!SetDCPixelFormat(m_hMemDC, PFD_DRAW_TO_BITMAP | PFD_SUPPORT_OPENGL | PFD_STEREO_DONTCARE))</p>
<p class="src"></p>
<p class="src2">static PIXELFORMATDESCRIPTOR pfd =</p>
<p class="src2">{</p>
<p class="src3">sizeof(PIXELFORMATDESCRIPTOR),<span class="kom">// Velikost této struktury</span></p>
<p class="src3">1,<span class="kom">// Verze</span></p>
<p class="src3">PFD_DRAW_TO_BITMAP<span class="kom">// Kreslení do DIBu</span></p>
<p class="src3">| PFD_SUPPORT_OPENGL<span class="kom">// Podpora OpenGL</span></p>
<p class="src3">| PFD_STEREO_DONTCARE,</p>
<p class="src3">PFD_TYPE_RGBA<span class="kom">// Pou¾ívá se RGBA</span></p>
<p class="src3">24,<span class="kom">// 24-bitová barevná hloubka</span></p>
<p class="src3">0, 0, 0, 0, 0, 0,<span class="kom">// Barevné bity ignorovány</span></p>
<p class="src3">0,<span class="kom">// ®ádný alfa buffer</span></p>
<p class="src3">0,<span class="kom">// Shift bit ignorován (?)</span></p>
<p class="src3">0,<span class="kom">// ®ádný akumulaèní bufer (?)</span></p>
<p class="src3">0, 0, 0, 0,<span class="kom">// Akumulaèní bity ignorovány</span></p>
<p class="src3">32,<span class="kom">// 32 bitový z-buffer</span></p>
<p class="src3">0,<span class="kom">// ®ádný stencil buffer</span></p>
<p class="src3">0,<span class="kom">// ®ádný pomocný buffer</span></p>
<p class="src3">PFD_MAIN_PLANE,<span class="kom">// Hlavní hladina (vrstva)</span></p>
<p class="src3">0,<span class="kom">// Rezervováno</span></p>
<p class="src3">0, 0, 0<span class="kom">// Hladinová maska ignorována</span></p>
<p class="src2">};</p>

<p>Vyhledáme index pixel formátu, který je nejbli¾¹í pøedcházející struktuøe.</p>

<p class="src1">int pixelformat;<span class="kom">// Pomocná promìnná</span></p>
<p class="src"></p>
<p class="src1">if ((pixelformat = ChoosePixelFormat(m_pDC->GetSafeHdc(), &amp;pfd)) == 0)</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nepovedlo se, uvolníme pamì» a skonèíme</span></p>
<p class="src2">DeleteObject(m_hDib);</p>
<p class="src2">m_hDib = NULL;</p>
<p class="src2">DeleteDC(m_hMemDC);</p>
<p class="src2">m_hMemDC = NULL;</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (SetPixelFormat(m_pDC->GetSafeHdc(), pixelformat, &amp;pfd) == FALSE)<span class="kom">// Nastaví formát pixelu</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nepovedlo se, uvolníme pamì» a skonèíme</span></p>
<p class="src2">DeleteObject(m_hDib);</p>
<p class="src2">m_hDib = NULL;</p>
<p class="src2">DeleteDC(m_hMemDC);</p>
<p class="src2">m_hMemDC = NULL;</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">int n = ::GetPixelFormat(m_pDC->GetSafeHdc());<span class="kom">// Zjistí aktuální formát pixelu</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Naplní strukturu pfd informacemi o aktuálním formátu.</span></p>
<p class="src1">::DescribePixelFormat(m_pDC->GetSafeHdc(), n, sizeof(pfd), &amp;pfd);</p>
<p class="src"></p>

<h4>5. Vytvoøení pamì»ového RC</h4>

<p>HGLRC m_hMemRC je pomocná èlenská promìnná tøídy CMyView.</p>

<p class="src1">m_hMemRC = ::wglCreateContext(m_hMemDC);<span class="kom">// Získáme pamì»ové RC</span></p>
<p class="src1">if (!m_hMemRC)</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nepovedlo se, uvolníme pamì» a skonèíme</span></p>
<p class="src2">DeleteObject(m_hDib);</p>
<p class="src2">m_hDib = NULL;</p>
<p class="src2">DeleteDC(m_hMemDC);</p>
<p class="src2">m_hMemDC = NULL;</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>

<h4>6. Ulo¾íme si staré DC a RC</h4>

<p>HDC m_hOldDC a HGLRC m_hOldRC jsou pomocné èlenské promìnné tøídy CMyView.</p>

<p class="src1">m_hOldDC = ::wglGetCurrentDC();<span class="kom">// Získáme aktuální (staré) DC</span></p>
<p class="src1">m_hOldRC = ::wglGetCurrentContext();<span class="kom">// Získáme aktuální (staré) RC</span></p>
<p class="src"></p>

<h4>7. Nastavíme pamì»ové RC jako aktuální</h4>

<p class="src1">::wglMakeCurrent(m_hMemDC, m_hMemRC);</p>
<p class="src"></p>

<h4>8. Inicializace pamì»ového RC je stejná jako u obrazovkového RC</h4>

<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení èisticí hodnoty pro hloubkový buffer</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí kontrolu hloubky</span></p>
<p class="src"></p>
<p class="src1">GLfloat fAspect = (GLfloat)szDIB.cx / szDIB.cy;<span class="kom">// Pomìr ¹íøky k vý¹ce</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Nastaví aktuální matici pro výpoèty potøebné pro vykreslování</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Resetuje aktuální matici</span></p>
<p class="src1">gluPerspective(45.0f, fAspect, 1, 100);<span class="kom">// Nastavení perspektivy</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Nastavení aktuální matici pro výpoèty potøebné pro vykreslování</span></p>
<p class="src1">glViewport(0, 0, cx, cy);<span class="kom">// Nastavení obdélníku do kterého lze vykreslovat</span></p>
<p class="src"></p>

<h4>9. Vytvoøení display listù pro pamì»ové RC</h4>

<p class="src1"><span class="kom">// ®ádné display listy nemáme</span></p>
<p class="src0">}</p>

<h2>Vykreslení scény mimo obrazovku - OnDraw()</h2>

<p>To provedeme vlastním kreslení. Je dobrým zvykem pou¾ít pro vykreslení do pamìti stejnou funkci jako pro vykreslení na obrazovku. Jediným rozdílem bude, ¾e nepou¾ijeme double buffering. Tuto funkci nebudu popisovat, proto¾e si ji ka¾dý napí¹e sám.</p>

<h2>Vlastní tisk na tiskárnu nebo do náhledu - OnPrint2()</h2>

<p>Tato metoda provede &quot;skuteèný&quot; tisk a poté uvolní pamì».</p>

<ol>
<li>Po vykreslení scény do pamìti mù¾eme smazat pamì»ové RC a nastavit pùvodní DC a RC jako aktuální.</li>
<li>Výpoèet cílové velikosti vzhledem k velikosti obrázku a orientaci papíru. Obrázek (scéna) je ulo¾en v pamìti (v DIB sekci). Ve skuteènosti je velikost a orientace papíru rozdílná od obrázku. Potøebujeme zjistit cílovou plochu na stránce, na kterou bud obrázek kopírován. Cílová plocha by mìla mít stejnou orientaci a pomìr stran jako obrázek v DIB sekci.</li>
<li>Rozta¾ení obrázku na velikost cílové plochy (vlastní tisk). Win32 API funkce StretchDIBits() zkopíruje DIB sekci do cíle, tj. na cílové DC (tiskárna nebo náhled). Obrázek je roztáhnut tak aby vyplnil cílovou plochu pøi zachování pomìru stran.</li>
<li>Práce je hotova. Uvolníme pamìt DIBu a DC.</li>
</ol>

<p class="src0">void CMyView::OnPrint2(CDC* pDC) </p>
<p class="src0">{</p>
<p class="src"></p>

<h4>1. Uvolnìní pamì»ového RC, a obnovení pùvoního (starého)  DC a RC</h4>

<p>DIB je hotový. U¾ nepotøebujeme pamì»ové RC. Jenom zkopírujeme obrázek na DC pro tisk nebo náhled.</p>

<p class="src1">::wglMakeCurrent(NULL, NULL);<span class="kom">// Jako aktuální nenastavíme nic</span></p>
<p class="src1">::wglDeleteContext(m_hMemRC);<span class="kom">// Smazání RC</span></p>
<p class="src"></p>
<p class="src1">::wglMakeCurrent(m_hOldDC, m_hOldRC);<span class="kom">// Obnovení pùvodního DC a RC</span></p>
<p class="src"></p>

<h4>2. Výpoèet cílové velikosti vzhledem k velikosti obrázku a orientaci papíru</h4>

<p class="src1">float fBmiRatio = float(m_bmi.bmiHeader.biHeight) / m_bmi.bmiHeader.biWidth;</p>
<p class="src"></p>
<p class="src1">CSize szTarget;</p>
<p class="src"></p>
<p class="src1">if (m_szPage.cx &gt; m_szPage.cy)<span class="kom">// Stránka na ¹íøku</span></p>
<p class="src1">{</p>
<p class="src2">if(fBmiRatio &lt; 1)<span class="kom">// Obrázek na ¹íøku</span></p>
<p class="src2">{</p>
<p class="src3">szTarget.cx = m_szPage.cx;</p>
<p class="src3">szTarget.cy = long(fBmiRatio * m_szPage.cx);</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Obrázek na vý¹ku</span></p>
<p class="src2">{</p>
<p class="src3">szTarget.cx = long(m_szPage.cy / fBmiRatio);</p>
<p class="src3">szTarget.cy = m_szPage.cy;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Stránka na vý¹ku</span></p>
<p class="src1">{</p>
<p class="src2">if(fBmiRatio&lt;1)<span class="kom">// Obrázek na ¹íøku</span></p>
<p class="src2">{</p>
<p class="src3">szTarget.cx = m_szPage.cx;</p>
<p class="src3">szTarget.cy = long(fBmiRatio * m_szPage.cx);</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Obrázek na vý¹ku</span></p>
<p class="src2">{</p>
<p class="src3">szTarget.cx = long(m_szPage.cy/fBmiRatio);</p>
<p class="src3">szTarget.cy = m_szPage.cy;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Výpoèet posunutí pro vycentrování na stránce</span></p>
<p class="src1">CSize szOffset((m_szPage.cx - szTarget.cx) / 2, (m_szPage.cy - szTarget.cy) / 2);</p>
<p class="src"></p>

<h4>3. Rozta¾ení obrázku na velikost cílové plochy (vlastní tisk)</h4>

<p class="src1">int nRet = ::StretchDIBits(pDC-&gt;GetSafeHdc(),</p>
<p class="src2">szOffset.cx, szOffset.cy,</p>
<p class="src2">szTarget.cx, szTarget.cy,</p>
<p class="src2">0, 0,</p>
<p class="src2">m_bmi.bmiHeader.biWidth,</p>
<p class="src2">m_bmi.bmiHeader.biHeight,</p>
<p class="src2">GLubyte*) m_pBitmapBits,</p>
<p class="src2">m_bmi,</p>
<p class="src2">DIB_RGB_COLORS,</p>
<p class="src2">SRCCOPY);</p>
<p class="src"></p>
<p class="src1">if(nRet == GDI_ERROR)<span class="kom">// Tisk byl neúspì¹ný</span></p>
<p class="src1">TRACE0(&quot;Chyba ve StretchDIBits()&quot;);</p>
<p class="src"></p>

<h4>4. Uvolnìní pamìti</h4>

<p class="src1">DeleteObject(m_hDib);<span class="kom">// Smazání DIBu</span></p>
<p class="src1">m_hDib = NULL;</p>
<p class="src1">DeleteDC(m_hMemDC);<span class="kom">//Smazání DC</span></p>
<p class="src1">m_hMemDC = NULL;</p>
<p class="src1">m_hOldDC = NULL;</p>
<p class="src0">}</p>

<p>Pro tento postup pøi tisku potøebuje nejménì 16 bitové barvy. Pokud se v náhledu zobrazuje èerná plocha zkontrolujte nastavení barev. V tomto by mohla být chyba.</p>

<p>Tento èlánek je mírnì upraveným pøekladem èlánku &quot;Printing and Print Preview OpenGL in MFC&quot; z anglického webu o programování <?OdkazBlank('http://www.codeguru.com/');?>, kde si mù¾ete stáhnout i zdrojový kód ukázkové aplikace. Hlavním rozdílem je základní tøída pohledu a nastavení pixel formátu DC, které jsem musel vytvoøit sám. V pùvodním èlánku to bylo udìláno zavoláním dvou funkcí, ale já si nestáhl zdrojový kód a nemìl jsem zrovna pøístup k internetu.</p>

<p class="autor">napsal: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<p>Adresa anglického èlánku je <?OdkazBlank('http://www.codeguru.com/opengl/printpreview.html');?>.</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_mfc_tisk.rar');?> - Visual C++, MFC</li>
</ul>


<?
include 'p_end.php';
?>
