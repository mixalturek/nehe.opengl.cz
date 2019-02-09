<?
$g_title = 'CZ NeHe OpenGL - Tisk a n�hled p�ed tiskem OpenGL sc�ny v MFC';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Tisk a n�hled p�ed tiskem OpenGL sc�ny v MFC</h1>

<p class="nadpis_clanku">Obalen� OpenGL t��dami MFC n�m dovol� vyu��t obou v�hod API: rychl�ho vykreslov�n� a elegantn�ho rozhran�. Nicm�n� d�ky faktu, �e mnoho ovlada�� tisk�ren nepracuje s API funkc� SetPixelFormat(), nen� mo�n� tisknout OpenGL sc�nu p��mo na tisk�rnu. Velmi roz���en� technika je vykreslit OpenGL sc�nu do DIBu a pot� ji zkop�rovat do DC pro tisk nebo n�hled. V tomto �l�nku uvid�te jak to ud�lat.</p>

<p>P�i vysv�tlov�n� pou�iji klasickou architekturu dokument/pohled. T��da CMyView je potomkem t��dy COpenGLView, ve kter� je ud�l�na ve�ker� inicializace OpenGL. V t�to nov� t��d� implementujeme tisk a n�hled OpenGL sc�ny. Tisk na tisk�rnu nebo do n�hledu se ve t��d� pohledu prov�d� pomoc� virtu�ln� funkce OnPrint(). Uprav�me ji n�sleduj�c�m zp�sobem.</p>

<p class="src0">void CMyView::OnPrint(CDC* pDC, CPrintInfo* pInfo)</p>
<p class="src0">{</p>
<p class="src1">OnPrint1(pDC, pInfo, this);<span class="kom">// P��prava tisku sc�ny</span></p>
<p class="src1">OnDraw(pDC);<span class="kom">// Vykreslen� sc�ny je spole�n� pro v�stup do okna, tisk i n�hled</span></p>
<p class="src1">OnPrint2(pDC);<span class="kom">// Vlastn� v�stup na tisk�rnu a �klid po tisku</span></p>
<p class="src0">}</p>

<p>Funkce OnPrint1() je vytvo�ena pro renderov�n� mimo obrazovku. Hlavn� �koly t�to funkce jsou vytvo�it DIB a pam�ov� DC i RC. Pam�ov� RC bude pozd�ji pou�ito pro vykreslen� OpenGL sc�ny mimo obrazovku. Funkce OnDraw() je standardn� virtu�ln� funkce t�idy pohledu ve kter� prov�d�me vlastn� vykreslen� sc�ny a to jak na tisk�rnu a do n�hledu, tak na obrazovku. Funkce OnPrint2() zkop�ruje z�skan� DIB OpenGL sc�ny na tisk�rnu nebo do n�hledu a provede �klid, tj. uvoln�n� DIBu a pam�ov�ch kontext�.</p>

<h2>P��prava OpenGL pro tisk mimo obrazovku - OnPrint1()</h2>

<ol>
<li>Vypo��t�me velikost DIBu pro tisk a n�hled, kter� z�vis� na velikosti zobrazovac�ho za��zen�. Pro n�hled pou�ijeme rozli�en� tisk�rny. Pro tisk pou�ijeme redukovan� rozli�en� tisk�rny. V ide�ln�m p��pad�, pokud by velikost pot�ebn� pam�ti a rychlost nebyla probl�m bychom pou�ili pln� rozli�en� obrazovky. Nicm�n� pro tisk�rnu s rozli�en�m 720 DPI a pou�it�m pap�ru o velikosti &quot;letter&quot; p�es�hne pam�, pot�ebn� pro DIB sekci, snadno 100MB. Proto redukujeme rozli�en� tisku.</li>
<li>Pro vytvo�en� DIB sekce o velikosti zm�n�n� d��ve vol�me funkci CreateDIBSection().</li>
<li>Vytvo�en� pam�ov�ho DC a jeho p�ipojen� k DIB sekci. Vol�n�m Win32 funkce CreateCompatibleDC() vytvo��me pam�ov� DC, potom do n�j vybereme DIB sekci.</li>
<li>Nastaven� pixel form�tu pam�ov�ho DC je podobn� nastaven� pixel form�tu obrazovkov�ho DC. Jedin� rozd�l je ve flagu, kter� nastavuje vlastnosti pixelov�ho bufferu. Pro obrazovku nastavujieme PFD_DRAW_TO_WINDOW a PFD_DOUBLEBUFFER, ale pro pam�ov� DC pot�ebujeme PFD_DRAW_TO_BITMAP.</li>
<li>Vytvo�en� pam�ov�ho RC. Pou�ijeme d��ve vytvo�en� pam�ov� DC pro vytvo�en� pam�ov�ho RC pro OpenGL mimoobrazovkov�ho renderingu. Po skon�en� tisku budou uvoln�ny (tj. ve funkci OnPrint2).</li>
<li>Star� DC a RC pro vykreslov�n� na obrazovku si mus�me ulo�it, proto�e je po skon�en� tisku mus�me op�t nastavit jako aktu�ln�.</li>
<li>Funkce wglMakeCurrent() nastav� pam�ov� RC jako aktu�ln�. Od te� bude OpenGL kreslit do pam�ov�ho, ne obrazovkov�ho, RC. Nicm�n� nejd��ve mus�me inicializovat pam�ov� RC stejn� jako jsme to d�lali s obrazovkov�m.</li>
<li>Inicializace pam�ov�ho RC. P�e vlastn�m kreslen�m do pam�ov�ho RC je�t� mus�me nastavit velikost plochy do kter� se m��e kreslit (velikost DIBu) a perspektivu.</li>
<li>Vytvo�en� display list� pro pam�ov� RC. Pokud pou��v�te display listy, tak je mus�te znovu vytvo�it pro nov� vytvo�en� pam�ov� RC. Pamatujte si, �e display listy nejsou znovupou�iteln� pro r�zn� RC.</li>
</ol>

<p>N�sleduje zdrojov� k�d funkce CMyView::OnPrint1().</p>

<p class="src0">void CMyView::OnPrint1(CDC* pDC, CPrintInfo* pInfo, CView* pView)</p>
<p class="src0">{</p>
<p class="src"></p>

<h4>1. Vypo��t�me velikost DIBu pro tisk a n�hled</h4>

<p class="src1">CRect rcClient;</p>
<p class="src1">pView->GetClientRect(&amp;rcClient);<span class="kom">// Zji�t�n� velikosti okna</span></p>
<p class="src1">float fClientRatio = float(rcClient.Height())/rcClient.Width();<span class="kom">// Pom�r velikost� stran okna</span></p>

<p>Zjist�me velikost str�nky. CSize m_szPage je pomocn� �lensk� prom�nn� t��dy CMyView.</p>

<p class="src1">m_szPage.cx = pDC->GetDeviceCaps(HORZRES);</p>
<p class="src1">m_szPage.cy = pDC->GetDeviceCaps(VERTRES);</p>
<p class="src"></p>
<p class="src1">CSize szDIB;</p>

<p>V�tv�me funkci podle toho, zda je prom�nn� m_bPreview true (n�hled) nebo false (tisk). Pro n�hled pou�ijeme rozli�en� okna.</p>

<p class="src1">if (pInfo->m_bPreview)<span class="kom">// N�hled</span></p>
<p class="src1">{</p>
<p class="src2">szDIB.cx = rcClient.Width();</p>
<p class="src2">szDIB.cy = rcClient.Height();</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Tisk</span></p>
<p class="src1">{</p>

<p>Pro tisk pou�ijeme vy��� rozli�en�. Mus�me upravit jeho velikost tak, aby pom�r stran byl stejn� jako u okna.</p>

<p class="src2">if (m_szPage.cy > fClientRatio * m_szPage.cx)</p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Plocha okna je �ir�� ne� tisknuteln� plocha</span></p>
<p class="src3">szDIB.cx = m_szPage.cx;</p>
<p class="src3">szDIB.cy = long(fClientRatio * m_szPage.cx);</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Plocha okna je u��� ne� tisknuteln� plocha</span></p>
<p class="src3">szDIB.cx = long(float(m_szPage.cy) / fClientRatio);</p>
<p class="src3">szDIB.cy = m_szPage.cy;</p>
<p class="src2">}</p>

<p>Pokud je DIB pam�ov� p��li� velk�, uprav�me rozli�en�. Ur��me maxim�ln� velikost DIBu na 20 MB. M�lo by to z�viset na tisk�rn�, ale bohu�el nev�m, jak programov� zjistit velikost pam�ti tisk�rny.</p>

<p class="src2">while (szDIB.cx * szDIB.cy > 20 * 1024 * 1024)</p>
<p class="src2">{</p>
<p class="src3">szDIB.cx = szDIB.cx / 2;</p>
<p class="src3">szDIB.cy = szDIB.cy / 2;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�pis zji�t�n�ch hodnot do okna debugeru</span></p>
<p class="src1">TRACE("Buffer size: %d x %d = %6.2f MB\n", szDIB.cx, szDIB.cy, szDIB.cx*szDIB.cy*0.000001);</p>
<p class="src"></p>

<h4>2. Vytvo�en� DIB sekce</h4>

<p>BITMAPINFO m_bmi je pomocn� �lensk� prom�nn� t��dy CMyView.</p>

<p class="src1">memset(&amp;m_bmi, 0, sizeof(BITMAPINFO));<span class="kom">// Nulov�n�</span></p>
<p class="src1">m_bmi.bmiHeader.biSize = sizeof(BITMAPINFOHEADER);<span class="kom">// Velikost t�to struktury</span></p>
<p class="src1">m_bmi.bmiHeader.biWidth = szDIB.cx;<span class="kom">// ���ka DIBu</span></p>
<p class="src1">m_bmi.bmiHeader.biHeight = szDIB.cy;<span class="kom">// V��ka DIBu</span></p>
<p class="src1">m_bmi.bmiHeader.biPlanes = 1;</p>
<p class="src1">m_bmi.bmiHeader.biBitCount = 24;<span class="kom">// Po�et bit� na pixel</span></p>
<p class="src1">m_bmi.bmiHeader.biCompression = BI_RGB;<span class="kom">// Typ komprese (z�vis� na po�tu bit� na pixel) - bez komprese</span></p>
<p class="src1">m_bmi.bmiHeader.biSizeImage = szDIB.cx * szDIB.cy * 3;<span class="kom">// Po�et byt� pro ulo�en� jednotliv�ch bod� DIBu (���ka*v��ka*po�et byt� na bod)</span></p>
<p class="src"></p>
<p class="src1">HDC hDC = ::GetDC(pView->m_hWnd);<span class="kom">// Z�sk�n� DC okna</span></p>

<p>HANDLE m_hDib je pomocn� �lensk� prom�nn� t��dy CMyView.</p>

<p class="src1">m_hDib = ::CreateDIBSection(hDC, &amp;m_bmi, DIB_RGB_COLORS, &amp;m_pBitmapBits, NULL, (DWORD)0);<span class="kom">// Vytvo��me DIB</span></p>
<p class="src1">::ReleaseDC(pView->m_hWnd, hDC);<span class="kom">// Uvoln�n� DC okna</span></p>
<p class="src"></p>

<h4>3. Vytvo�en� pam�ov�ho DC a jeho p�ipojen� k DIB sekci</h4>

<p>HDC m_hMemDC je pomocn� �lensk� prom�nn� t��dy CMyView.</p>

<p class="src1">m_hMemDC = ::CreateCompatibleDC(NULL);<span class="kom">// Vytvo�en� pam�ov� DC</span></p>
<p class="src"></p>
<p class="src1">if (!m_hMemDC)<span class="kom">// Pokud se jeho vytvo�en� nepovedlo sma�eme jej a skon��me</span></p>
<p class="src1">{</p>
<p class="src2">DeleteObject(m_hDib);</p>
<p class="src2">m_hDib = NULL;</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SelectObject(m_hMemDC, m_hDib);<span class="kom">// Vybereme DIB do pam�ov�ho DC</span></p>
<p class="src"></p>

<h4>4. Nastven� pixel form�tu pam�ov�ho DC</h4>

<p class="src1">if (!SetDCPixelFormat(m_hMemDC, PFD_DRAW_TO_BITMAP | PFD_SUPPORT_OPENGL | PFD_STEREO_DONTCARE))</p>
<p class="src"></p>
<p class="src2">static PIXELFORMATDESCRIPTOR pfd =</p>
<p class="src2">{</p>
<p class="src3">sizeof(PIXELFORMATDESCRIPTOR),<span class="kom">// Velikost t�to struktury</span></p>
<p class="src3">1,<span class="kom">// Verze</span></p>
<p class="src3">PFD_DRAW_TO_BITMAP<span class="kom">// Kreslen� do DIBu</span></p>
<p class="src3">| PFD_SUPPORT_OPENGL<span class="kom">// Podpora OpenGL</span></p>
<p class="src3">| PFD_STEREO_DONTCARE,</p>
<p class="src3">PFD_TYPE_RGBA<span class="kom">// Pou��v� se RGBA</span></p>
<p class="src3">24,<span class="kom">// 24-bitov� barevn� hloubka</span></p>
<p class="src3">0, 0, 0, 0, 0, 0,<span class="kom">// Barevn� bity ignorov�ny</span></p>
<p class="src3">0,<span class="kom">// ��dn� alfa buffer</span></p>
<p class="src3">0,<span class="kom">// Shift bit ignorov�n (?)</span></p>
<p class="src3">0,<span class="kom">// ��dn� akumula�n� bufer (?)</span></p>
<p class="src3">0, 0, 0, 0,<span class="kom">// Akumula�n� bity ignorov�ny</span></p>
<p class="src3">32,<span class="kom">// 32 bitov� z-buffer</span></p>
<p class="src3">0,<span class="kom">// ��dn� stencil buffer</span></p>
<p class="src3">0,<span class="kom">// ��dn� pomocn� buffer</span></p>
<p class="src3">PFD_MAIN_PLANE,<span class="kom">// Hlavn� hladina (vrstva)</span></p>
<p class="src3">0,<span class="kom">// Rezervov�no</span></p>
<p class="src3">0, 0, 0<span class="kom">// Hladinov� maska ignorov�na</span></p>
<p class="src2">};</p>

<p>Vyhled�me index pixel form�tu, kter� je nejbli��� p�edch�zej�c� struktu�e.</p>

<p class="src1">int pixelformat;<span class="kom">// Pomocn� prom�nn�</span></p>
<p class="src"></p>
<p class="src1">if ((pixelformat = ChoosePixelFormat(m_pDC->GetSafeHdc(), &amp;pfd)) == 0)</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nepovedlo se, uvoln�me pam� a skon��me</span></p>
<p class="src2">DeleteObject(m_hDib);</p>
<p class="src2">m_hDib = NULL;</p>
<p class="src2">DeleteDC(m_hMemDC);</p>
<p class="src2">m_hMemDC = NULL;</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (SetPixelFormat(m_pDC->GetSafeHdc(), pixelformat, &amp;pfd) == FALSE)<span class="kom">// Nastav� form�t pixelu</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nepovedlo se, uvoln�me pam� a skon��me</span></p>
<p class="src2">DeleteObject(m_hDib);</p>
<p class="src2">m_hDib = NULL;</p>
<p class="src2">DeleteDC(m_hMemDC);</p>
<p class="src2">m_hMemDC = NULL;</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">int n = ::GetPixelFormat(m_pDC->GetSafeHdc());<span class="kom">// Zjist� aktu�ln� form�t pixelu</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Napln� strukturu pfd informacemi o aktu�ln�m form�tu.</span></p>
<p class="src1">::DescribePixelFormat(m_pDC->GetSafeHdc(), n, sizeof(pfd), &amp;pfd);</p>
<p class="src"></p>

<h4>5. Vytvo�en� pam�ov�ho RC</h4>

<p>HGLRC m_hMemRC je pomocn� �lensk� prom�nn� t��dy CMyView.</p>

<p class="src1">m_hMemRC = ::wglCreateContext(m_hMemDC);<span class="kom">// Z�sk�me pam�ov� RC</span></p>
<p class="src1">if (!m_hMemRC)</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nepovedlo se, uvoln�me pam� a skon��me</span></p>
<p class="src2">DeleteObject(m_hDib);</p>
<p class="src2">m_hDib = NULL;</p>
<p class="src2">DeleteDC(m_hMemDC);</p>
<p class="src2">m_hMemDC = NULL;</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>

<h4>6. Ulo��me si star� DC a RC</h4>

<p>HDC m_hOldDC a HGLRC m_hOldRC jsou pomocn� �lensk� prom�nn� t��dy CMyView.</p>

<p class="src1">m_hOldDC = ::wglGetCurrentDC();<span class="kom">// Z�sk�me aktu�ln� (star�) DC</span></p>
<p class="src1">m_hOldRC = ::wglGetCurrentContext();<span class="kom">// Z�sk�me aktu�ln� (star�) RC</span></p>
<p class="src"></p>

<h4>7. Nastav�me pam�ov� RC jako aktu�ln�</h4>

<p class="src1">::wglMakeCurrent(m_hMemDC, m_hMemRC);</p>
<p class="src"></p>

<h4>8. Inicializace pam�ov�ho RC je stejn� jako u obrazovkov�ho RC</h4>

<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� �istic� hodnoty pro hloubkov� buffer</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� kontrolu hloubky</span></p>
<p class="src"></p>
<p class="src1">GLfloat fAspect = (GLfloat)szDIB.cx / szDIB.cy;<span class="kom">// Pom�r ���ky k v��ce</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Nastav� aktu�ln� matici pro v�po�ty pot�ebn� pro vykreslov�n�</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Resetuje aktu�ln� matici</span></p>
<p class="src1">gluPerspective(45.0f, fAspect, 1, 100);<span class="kom">// Nastaven� perspektivy</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Nastaven� aktu�ln� matici pro v�po�ty pot�ebn� pro vykreslov�n�</span></p>
<p class="src1">glViewport(0, 0, cx, cy);<span class="kom">// Nastaven� obd�ln�ku do kter�ho lze vykreslovat</span></p>
<p class="src"></p>

<h4>9. Vytvo�en� display list� pro pam�ov� RC</h4>

<p class="src1"><span class="kom">// ��dn� display listy nem�me</span></p>
<p class="src0">}</p>

<h2>Vykreslen� sc�ny mimo obrazovku - OnDraw()</h2>

<p>To provedeme vlastn�m kreslen�. Je dobr�m zvykem pou��t pro vykreslen� do pam�ti stejnou funkci jako pro vykreslen� na obrazovku. Jedin�m rozd�lem bude, �e nepou�ijeme double buffering. Tuto funkci nebudu popisovat, proto�e si ji ka�d� nap�e s�m.</p>

<h2>Vlastn� tisk na tisk�rnu nebo do n�hledu - OnPrint2()</h2>

<p>Tato metoda provede &quot;skute�n�&quot; tisk a pot� uvoln� pam�.</p>

<ol>
<li>Po vykreslen� sc�ny do pam�ti m��eme smazat pam�ov� RC a nastavit p�vodn� DC a RC jako aktu�ln�.</li>
<li>V�po�et c�lov� velikosti vzhledem k velikosti obr�zku a orientaci pap�ru. Obr�zek (sc�na) je ulo�en v pam�ti (v DIB sekci). Ve skute�nosti je velikost a orientace pap�ru rozd�ln� od obr�zku. Pot�ebujeme zjistit c�lovou plochu na str�nce, na kterou bud obr�zek kop�rov�n. C�lov� plocha by m�la m�t stejnou orientaci a pom�r stran jako obr�zek v DIB sekci.</li>
<li>Rozta�en� obr�zku na velikost c�lov� plochy (vlastn� tisk). Win32 API funkce StretchDIBits() zkop�ruje DIB sekci do c�le, tj. na c�lov� DC (tisk�rna nebo n�hled). Obr�zek je rozt�hnut tak aby vyplnil c�lovou plochu p�i zachov�n� pom�ru stran.</li>
<li>Pr�ce je hotova. Uvoln�me pam�t DIBu a DC.</li>
</ol>

<p class="src0">void CMyView::OnPrint2(CDC* pDC) </p>
<p class="src0">{</p>
<p class="src"></p>

<h4>1. Uvoln�n� pam�ov�ho RC, a obnoven� p�von�ho (star�ho)  DC a RC</h4>

<p>DIB je hotov�. U� nepot�ebujeme pam�ov� RC. Jenom zkop�rujeme obr�zek na DC pro tisk nebo n�hled.</p>

<p class="src1">::wglMakeCurrent(NULL, NULL);<span class="kom">// Jako aktu�ln� nenastav�me nic</span></p>
<p class="src1">::wglDeleteContext(m_hMemRC);<span class="kom">// Smaz�n� RC</span></p>
<p class="src"></p>
<p class="src1">::wglMakeCurrent(m_hOldDC, m_hOldRC);<span class="kom">// Obnoven� p�vodn�ho DC a RC</span></p>
<p class="src"></p>

<h4>2. V�po�et c�lov� velikosti vzhledem k velikosti obr�zku a orientaci pap�ru</h4>

<p class="src1">float fBmiRatio = float(m_bmi.bmiHeader.biHeight) / m_bmi.bmiHeader.biWidth;</p>
<p class="src"></p>
<p class="src1">CSize szTarget;</p>
<p class="src"></p>
<p class="src1">if (m_szPage.cx &gt; m_szPage.cy)<span class="kom">// Str�nka na ���ku</span></p>
<p class="src1">{</p>
<p class="src2">if(fBmiRatio &lt; 1)<span class="kom">// Obr�zek na ���ku</span></p>
<p class="src2">{</p>
<p class="src3">szTarget.cx = m_szPage.cx;</p>
<p class="src3">szTarget.cy = long(fBmiRatio * m_szPage.cx);</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Obr�zek na v��ku</span></p>
<p class="src2">{</p>
<p class="src3">szTarget.cx = long(m_szPage.cy / fBmiRatio);</p>
<p class="src3">szTarget.cy = m_szPage.cy;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Str�nka na v��ku</span></p>
<p class="src1">{</p>
<p class="src2">if(fBmiRatio&lt;1)<span class="kom">// Obr�zek na ���ku</span></p>
<p class="src2">{</p>
<p class="src3">szTarget.cx = m_szPage.cx;</p>
<p class="src3">szTarget.cy = long(fBmiRatio * m_szPage.cx);</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Obr�zek na v��ku</span></p>
<p class="src2">{</p>
<p class="src3">szTarget.cx = long(m_szPage.cy/fBmiRatio);</p>
<p class="src3">szTarget.cy = m_szPage.cy;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�po�et posunut� pro vycentrov�n� na str�nce</span></p>
<p class="src1">CSize szOffset((m_szPage.cx - szTarget.cx) / 2, (m_szPage.cy - szTarget.cy) / 2);</p>
<p class="src"></p>

<h4>3. Rozta�en� obr�zku na velikost c�lov� plochy (vlastn� tisk)</h4>

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
<p class="src1">if(nRet == GDI_ERROR)<span class="kom">// Tisk byl ne�sp�n�</span></p>
<p class="src1">TRACE0(&quot;Chyba ve StretchDIBits()&quot;);</p>
<p class="src"></p>

<h4>4. Uvoln�n� pam�ti</h4>

<p class="src1">DeleteObject(m_hDib);<span class="kom">// Smaz�n� DIBu</span></p>
<p class="src1">m_hDib = NULL;</p>
<p class="src1">DeleteDC(m_hMemDC);<span class="kom">//Smaz�n� DC</span></p>
<p class="src1">m_hMemDC = NULL;</p>
<p class="src1">m_hOldDC = NULL;</p>
<p class="src0">}</p>

<p>Pro tento postup p�i tisku pot�ebuje nejm�n� 16 bitov� barvy. Pokud se v n�hledu zobrazuje �ern� plocha zkontrolujte nastaven� barev. V tomto by mohla b�t chyba.</p>

<p>Tento �l�nek je m�rn� upraven�m p�ekladem �l�nku &quot;Printing and Print Preview OpenGL in MFC&quot; z anglick�ho webu o programov�n� <?OdkazBlank('http://www.codeguru.com/');?>, kde si m��ete st�hnout i zdrojov� k�d uk�zkov� aplikace. Hlavn�m rozd�lem je z�kladn� t��da pohledu a nastaven� pixel form�tu DC, kter� jsem musel vytvo�it s�m. V p�vodn�m �l�nku to bylo ud�l�no zavol�n�m dvou funkc�, ale j� si nest�hl zdrojov� k�d a nem�l jsem zrovna p��stup k internetu.</p>

<p class="autor">napsal: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<p>Adresa anglick�ho �l�nku je <?OdkazBlank('http://www.codeguru.com/opengl/printpreview.html');?>.</p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_mfc_tisk.rar');?> - Visual C++, MFC</li>
</ul>


<?
include 'p_end.php';
?>
