<?
$g_title = 'CZ NeHe OpenGL - Lekce 46 - Fullscreenov� antialiasing';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(46);?>

<h1>Lekce 46 - Fullscreenov� antialiasing</h1>

<p class="nadpis_clanku">Cht�li byste, aby va�e aplikace vypadaly je�t� l�pe ne� doposud? Fullscreenov� vyhlazov�n�, naz�van� t� multisampling, by v�m mohlo pomoci. S v�hodou ho pou��vaj� ne-realtimov� renderovac� programy, nicm�n� s dne�n�m hardwarem ho m��eme dos�hnout i v re�ln�m �ase. Bohu�el je implementov�no pouze jako roz���en� ARB_MULTISAMPLE, kter� nebude pracovat, pokud ho grafick� karta nepodporuje.</p>

<p>V tomto zaj�mav�m tutori�lu zkus�me posunout grafick� vzhled aplikac� je�t� d�le. O antialiasingu jste u� �etli v minul�ch tutori�lech, multisampling, narozd�l od n�j, neoperuje s jednotliv�mi objekty zvlṻ, ale pracuje a� s vykreslovan�mi pixely. Ve v�sledn�m obr�zku se pokou�� naj�t a odstranit ostr� hrany. Proto�e se mus� vz�t v �vahu ka�d� zobrazovan� pixel, bez hardwarov� akcelerace grafick� karty by velice sn�il v�kon aplikace.</p>

<p class="src0"><span class="kom">Vid_mem = sizeof(Front_buffer) + sizeof(Back_buffer) + num_samples * (sizeof(Front_buffer) + sizeof(ZS_buffer))</span></p>

<p>Pro v�ce informac� pros�m zkuste tyto odkazy:</p>

<div><?OdkazBlank('http://developer.nvidia.com/attach/3464', 'GDC2002 - OpenGL Multisample');?></div>
<div><?OdkazBlank('http://developer.nvidia.com/attach/2064', 'OpenGL Pixel Formats and Multisample Antialiasing');?></div>

<p>Po tomto nutn�m �vodu se kone�n� m��eme pustit do pr�ce. Narozd�l od jin�ch roz���en�, kter� OpenGL p�i renderingu vyu��v�, mus�me s ARB_MULTISAMPLE po��tat u� p�i vytv��en� okna. Postupujeme tedy n�sledovn�:</p>

<ul>
<li>Vytvo��me okno �pln� stejn� jako oby�ejn�</li>
<li>Dot�eme se, jestli m��eme vyhlazovat pixely</li>
<li>Pokud je multisampling dostupn�, zru��me okno a vytvo��me ho s nov�m pixel form�tem</li>
<li>Pro ��sti, kter� chceme vyhlazovat, jednodu�e zavol�me glEnable(GL_ARB_MULTISAMPLE)</li>
</ul>

<p>Za�neme v souboru arb_multisample.cpp. Jako v�dy inkludujeme hlavi�kov� soubory pro OpenGL a knihovnu GLU. O arb_multisample.h se budeme bavit pozd�ji.</p>

<p class="src0">#include &lt;windows.h&gt;</p>
<p class="src0">#include &lt;gl/gl.h&gt;</p>
<p class="src0">#include &lt;gl/glu.h&gt;</p>
<p class="src"></p>
<p class="src0">#include &quot;arb_multisample.h&quot;</p>

<p>Symbolick� konstanty pou�ijeme p�i definov�n� atribut� pixel form�tu. Podporuje-li grafick� karta multisampling, bude logick� prom�nn� arbMultisampleSupported obsahovat true.</p>

<p class="src0">#define WGL_SAMPLE_BUFFERS_ARB 0x2041<span class="kom">// Symbolick� konstanty pro multisampling</span></p>
<p class="src0">#define WGL_SAMPLES_ARB 0x2042</p>
<p class="src"></p>
<p class="src0">bool arbMultisampleSupported = false;<span class="kom">// Je multisampling dostupn�?</span></p>
<p class="src0">int arbMultisampleFormat = 0;<span class="kom">// Form�t multisamplingu</span></p>

<p>N�sleduj�c� funkce testuje, zda je WGL OpenGL roz���en� na syst�mu dostupn� v dan�m form�tu.</p>

<p class="src0">bool WGLisExtensionSupported(const char *extension)<span class="kom">// Je roz���en� podporov�no?</span></p>
<p class="src0">{</p>
<p class="src1">const size_t extlen = strlen(extension);</p>
<p class="src1">const char *supported = NULL;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pokud je to mo�n�, pokus� se wglGetExtensionStringARB pou��t na aktu�ln� DC</span></p>
<p class="src1">PROC wglGetExtString = wglGetProcAddress(&quot;wglGetExtensionsStringARB&quot;);</p>
<p class="src"></p>
<p class="src1">if (wglGetExtString)<span class="kom">// WGL OpenGL roz���en�</span></p>
<p class="src1">{</p>
<p class="src2">supported = ((char*(__stdcall*)(HDC))wglGetExtString)(wglGetCurrentDC());</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (supported == NULL)<span class="kom">// Zkus� je�t� standardn� OpenGL �et�zec s roz���en�mi</span></p>
<p class="src1">{</p>
<p class="src2">supported = (char*)glGetString(GL_EXTENSIONS);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (supported == NULL)<span class="kom">// Pokud sel�e i toto, nen� �et�zec dostupn�</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">for (const char* p = supported; ; p++)<span class="kom">// Testov�n� obsahu �et�zce</span></p>
<p class="src1">{</p>
<p class="src2">p = strstr(p, extension);<span class="kom">// Hled� pod�et�zec</span></p>
<p class="src"></p>
<p class="src2">if (p == NULL)<span class="kom">// Pod�et�zec nen� v �et�zci</span></p>
<p class="src2">{</p>
<p class="src3">return false;<span class="kom">// Roz���en� nebylo nalezeno</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Okolo pod�et�zce se mus� vyskytovat odd�lova� (mezera nebo NULL)</span></p>
<p class="src2">if ((p == supported || p[-1] == ' ') &amp;&amp; (p[extlen] == '\0' || p[extlen] == ' '))</p>
<p class="src2">{</p>
<p class="src3">return true;<span class="kom">// Roz���en� bylo nalezeno</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Funkce InitMultisample() je sv�m zp�sobem j�drem programu. Dot�eme se na podporu pot�ebn�ho roz���en� a pokud ji m�me, z�sk�me po�adovan� pixel form�t.</p>

<p class="src0">bool InitMultisample(HINSTANCE hInstance, HWND hWnd, PIXELFORMATDESCRIPTOR pfd)<span class="kom">// Inicializace multisamplingu</span></p>
<p class="src0">{</p>
<p class="src1">if (!WGLisExtensionSupported(&quot;WGL_ARB_multisample&quot;))<span class="kom">// Existuje �et�zec ve WGL</span></p>
<p class="src1">{</p>
<p class="src2">arbMultisampleSupported = false;</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">PFNWGLCHOOSEPIXELFORMATARBPROC wglChoosePixelFormatARB = (PFNWGLCHOOSEPIXELFORMATARBPROC)wglGetProcAddress(&quot;wglChoosePixelFormatARB&quot;);<span class="kom">// Z�sk�n� pixel form�tu</span></p>
<p class="src"></p>
<p class="src1">if (!wglChoosePixelFormatARB)<span class="kom">// Dan� pixel form�t nen� dostupn�</span></p>
<p class="src1">{</p>
<p class="src2">arbMultisampleSupported = false;</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">HDC hDC = GetDC(hWnd);<span class="kom">// Z�sk�n� kontextu za��zen�</span></p>
<p class="src"></p>
<p class="src1">int pixelFormat;</p>
<p class="src1">int valid;</p>
<p class="src1">UINT numFormats;</p>
<p class="src1">float fAttributes[] = {0, 0};</p>

<p>N�sleduj�c� pole atribut� slou�� pro definov�n� vlastnost� pixel form�tu. V�echny polo�ky krom� WGL_SAMPLE_BUFFERS_ARB a WGL_SAMPLE_ARB jsou standardn�, a proto by n�m nem�ly �init pot�e. Pokud usp�je hlavn� test podpory multisamplingu, kter� reprezentuje wglChoosePixelFormatARB(), m�me vyhr�no.</p>

<p class="src1">int iAttributes[] =<span class="kom">// Atributy</span></p>
<p class="src1">{</p>
<p class="src2">WGL_DRAW_TO_WINDOW_ARB, GL_TRUE,</p>
<p class="src2">WGL_SUPPORT_OPENGL_ARB, GL_TRUE,</p>
<p class="src2">WGL_ACCELERATION_ARB, WGL_FULL_ACCELERATION_ARB,</p>
<p class="src2">WGL_COLOR_BITS_ARB, 24,</p>
<p class="src2">WGL_ALPHA_BITS_ARB, 8,</p>
<p class="src2">WGL_DEPTH_BITS_ARB, 16,</p>
<p class="src2">WGL_STENCIL_BITS_ARB, 0,</p>
<p class="src2">WGL_DOUBLE_BUFFER_ARB, GL_TRUE,</p>
<p class="src2">WGL_SAMPLE_BUFFERS_ARB, GL_TRUE,</p>
<p class="src2">WGL_SAMPLES_ARB, 4,</p>
<p class="src2">0, 0</p>
<p class="src1">};</p>
<p class="src"></p>
<p class="src1">valid = wglChoosePixelFormatARB(hDC, iAttributes, fAttributes, 1, &amp;pixelFormat, &amp;numFormats);<span class="kom">// Pixel form�t pro �ty�i vzorkov�n�</span></p>
<p class="src"></p>
<p class="src1">if (valid &amp;&amp; numFormats &gt;= 1)<span class="kom">// Vr�ceno true a po�et form�t� je v�t�� ne� jedna</span></p>
<p class="src1">{</p>
<p class="src2">arbMultisampleSupported = true;</p>
<p class="src2">arbMultisampleFormat = pixelFormat;</p>
<p class="src2">return arbMultisampleSupported;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">iAttributes[19] = 2;<span class="kom">// �ty�i vzorkov�n� nejsou dostupn�, test dvou</span></p>
<p class="src"></p>
<p class="src1">valid = wglChoosePixelFormatARB(hDC, iAttributes, fAttributes, 1, &amp;pixelFormat, &amp;numFormats);</p>
<p class="src"></p>
<p class="src1">if (valid &amp;&amp; numFormats &gt;= 1)</p>
<p class="src1">{</p>
<p class="src2">arbMultisampleSupported = true;</p>
<p class="src2">arbMultisampleFormat = pixelFormat; </p>
<p class="src2">return arbMultisampleSupported;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return arbMultisampleSupported;<span class="kom">// Vr�cen� validn�ho form�tu</span></p>
<p class="src0">}</p>

<p>K�d pro detekci multisamplingu m�me hotov, te� modifikujeme vytv��en� okna. Inkludujeme hlavi�kov� soubor arb_multisample.h a vytvo��me funk�n� prototypy.</p>

<p class="src0">#include &quot;arb_multisample.h&quot;<span class="kom">// Hlavi�kov� soubor pro multisampling</span></p>
<p class="src"></p>
<p class="src0">BOOL DestroyWindowGL(GL_Window* window);<span class="kom">// Funk�n� prototypy</span></p>
<p class="src0">BOOL CreateWindowGL(GL_Window* window);</p>

<p>N�sleduj�c� v�pis k�du pat�� do funkce CreateWindowGL(). P�vodn� k�d pov�t�inou z�stane, ale ud�l�me v n�m n�kolik zm�n. V z�kladu pot�ebujeme vy�e�it probl�m, kter� spo��v� v tom, �e nem��eme polo�it dotaz na pixel form�t (detekovat p��tomnost multisamplingu), dokud nen� vytvo�eno okno. Nicm�n� naproti tomu nem��eme vytvo�it okno s vyhlazov�n�m, dokud nem�me pixel form�t, kter� ho podporuje. Trochu se to podob� ot�zce, zda bylo prvn� vejce nebo slepice. Implementujeme dvoupr�chodov� syst�m - nejprve vytvo��me oby�ejn� okno, dot�eme se na pixel form�t a pokud je multisampling podporov�n, zru��me okno a vytvo��me spr�vn�. Trochu t�kop�dn�, ale nezn�m jin� zp�sob.</p>

<p class="src0"><span class="kom">// Funkce CreateWindowGL()</span></p>
<p class="src1">window-&gt;hDC = GetDC(window-&gt;hWnd);<span class="kom">// Grabov�n� kontextu za��zen�</span></p>
<p class="src"></p>
<p class="src1">if (window-&gt;hDC == 0)<span class="kom">// Poda�ilo se ho z�skat?</span></p>
<p class="src1">{</p>
<p class="src2">DestroyWindow(window-&gt;hWnd);<span class="kom">// Zru�en� okna</span></p>
<p class="src2">window-&gt;hWnd = 0;<span class="kom">// Nulov�n� handle</span></p>
<p class="src"></p>
<p class="src2">return FALSE;<span class="kom">// Ne�sp�ch</span></p>
<p class="src1">}</p>

<p>P�i prvn�m pr�chodu touto funkc� (dal�� pr�chody nap�. p�i p�ep�n�n� do/z fullscreenu) nen� mo�n� multisampling natvrdo zapnout, tak�e jsme vytvo�ili pouze oby�ejn� okno. Pokud m�me jistotu, �e ho m��eme pou��t, nastav�me pixel form�t na arbMultiSampleFormat.</p>

<p class="src1">if(!arbMultisampleSupported)<span class="kom">// Multisampling nen� podporov�n</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Vytvo�en� norm�ln�ho okna</span></p>
<p class="src2">PixelFormat = ChoosePixelFormat(window-&gt;hDC, &amp;pfd);<span class="kom">// Z�sk� kompatibiln� pixel form�t</span></p>
<p class="src"></p>
<p class="src2">if (PixelFormat == 0)<span class="kom">// Poda�ilo se ho z�skat?</span></p>
<p class="src2">{</p>
<p class="src3">ReleaseDC(window-&gt;hWnd, window-&gt;hDC);<span class="kom">// Uvoln�n� kontextu za��zen�</span></p>
<p class="src3">window-&gt;hDC = 0;<span class="kom">// Nulov�n� prom�nn�</span></p>
<p class="src3">DestroyWindow(window-&gt;hWnd);<span class="kom">// Zru�en� okna</span></p>
<p class="src3">window-&gt;hWnd = 0;<span class="kom">// Nulov�n� handle</span></p>
<p class="src"></p>
<p class="src3">return FALSE;<span class="kom">// Ne�sp�ch</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Multisampling je podporov�n</span></p>
<p class="src1">{</p>
<p class="src2">PixelFormat = arbMultisampleFormat;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (SetPixelFormat(window-&gt;hDC, PixelFormat, &amp;pfd) == FALSE)<span class="kom">// Zkus� nastavit pixel form�t</span></p>
<p class="src1">{</p>
<p class="src2">ReleaseDC(window-&gt;hWnd, window-&gt;hDC);</p>
<p class="src2">window-&gt;hDC = 0;</p>
<p class="src2">DestroyWindow(window-&gt;hWnd);</p>
<p class="src2">window-&gt;hWnd = 0;</p>
<p class="src"></p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">window-&gt;hRC = wglCreateContext(window-&gt;hDC);<span class="kom">// Zkus� z�skat rendering kontext</span></p>
<p class="src"></p>
<p class="src1">if (window-&gt;hRC == 0)<span class="kom">// Poda�ilo se ho z�skat?</span></p>
<p class="src1">{</p>
<p class="src2">ReleaseDC(window-&gt;hWnd, window-&gt;hDC);</p>
<p class="src2">window-&gt;hDC = 0;</p>
<p class="src2">DestroyWindow(window-&gt;hWnd);</p>
<p class="src2">window-&gt;hWnd = 0;</p>
<p class="src"></p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (wglMakeCurrent(window-&gt;hDC, window-&gt;hRC) == FALSE)<span class="kom">// Aktivuje rendering kontext</span></p>
<p class="src1">{</p>
<p class="src2">wglDeleteContext(window-&gt;hRC);</p>
<p class="src2">window-&gt;hRC = 0;</p>
<p class="src2">ReleaseDC(window-&gt;hWnd, window-&gt;hDC);</p>
<p class="src2">window-&gt;hDC = 0;</p>
<p class="src2">DestroyWindow(window-&gt;hWnd);</p>
<p class="src2">window-&gt;hWnd = 0;</p>
<p class="src"></p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>

<p>Okno bylo vytvo�eno, tak�e m�me k dispozici handle pro dotaz na multisampling. Pokud je podporov�n, zru��me okno a vytvo��me ho s nov�m pixel form�tem.</p>

<p class="src1">if(!arbMultisampleSupported &amp;&amp; CHECK_FOR_MULTISAMPLE)<span class="kom">// Je multisampling dostupn�?</span></p>
<p class="src1">{</p>
<p class="src2"></p>
<p class="src2">if(InitMultisample(window-&gt;init.application-&gt;hInstance, window-&gt;hWnd, pfd))<span class="kom">// Inicializace multisamplingu</span></p>
<p class="src2">{</p>
<p class="src3">DestroyWindowGL(window);</p>
<p class="src3">return CreateWindowGL(window);</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">ShowWindow(window-&gt;hWnd, SW_NORMAL);<span class="kom">// Zobraz� okno</span></p>
<p class="src1">window-&gt;isVisible = TRUE;</p>
<p class="src"></p>
<p class="src1">ReshapeGL(window-&gt;init.width, window-&gt;init.height);<span class="kom">// Ozn�m� rozm�ry okna OpenGL</span></p>
<p class="src1">ZeroMemory(window-&gt;keys, sizeof(Keys));<span class="kom">// Nulov�n� pole indikuj�c� stisk kl�ves</span></p>
<p class="src1">window-&gt;lastTickCount = GetTickCount();<span class="kom">// Inicializuje �asovou prom�nnou</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�e v po��dku</span></p>
<p class="src0">}</p>

<p>OK, nastavov�n� je kompletn�, dost�v�me se k z�bavn�j�� ��sti, pro kterou jsme se tak sna�ili. Na�t�st� se sdru�en� ARB rozhodlo u�init multisampling dynamick�m, co� n�m ho umo��uje kdykoli zapnout nebo vypnout. Sta�� jednoduch� glEnable() a glDisable().</p>

<p class="src0">glEnable(GL_MULTISAMPLE_ARB);</p>
<p class="src1"><span class="kom">// Vykreslen� vyhlazovan�ch objekt�</span></p>
<p class="src0">glDisable(GL_MULTISAMPLE_ARB);</p>

<p>A to je v�e. A� spust�te uk�zkov� demo, uvid�te, jak kvalitn� vyhlazov�n� zlep�uje celkov� vzhled sc�ny.</p>

<p class="autor">napsal: Colt McAnlis - MainRoach <?VypisEmail('duhroach@hotmail.com');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson46.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson46_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson46.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson46.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(46);?>
<?FceNeHeOkolniLekce(46);?>

<?
include 'p_end.php';
?>
