<?
$g_title = 'CZ NeHe OpenGL - Lekce 46 - Fullscreenový antialiasing';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(46);?>

<h1>Lekce 46 - Fullscreenový antialiasing</h1>

<p class="nadpis_clanku">Chtìli byste, aby va¹e aplikace vypadaly je¹tì lépe ne¾ doposud? Fullscreenové vyhlazování, nazývané té¾ multisampling, by vám mohlo pomoci. S výhodou ho pou¾ívají ne-realtimové renderovací programy, nicménì s dne¹ním hardwarem ho mù¾eme dosáhnout i v reálném èase. Bohu¾el je implementováno pouze jako roz¹íøení ARB_MULTISAMPLE, které nebude pracovat, pokud ho grafická karta nepodporuje.</p>

<p>V tomto zajímavém tutoriálu zkusíme posunout grafický vzhled aplikací je¹tì dále. O antialiasingu jste u¾ èetli v minulých tutoriálech, multisampling, narozdíl od nìj, neoperuje s jednotlivými objekty zvlá¹», ale pracuje a¾ s vykreslovanými pixely. Ve výsledném obrázku se pokou¹í najít a odstranit ostré hrany. Proto¾e se musí vzít v úvahu ka¾dý zobrazovaný pixel, bez hardwarové akcelerace grafické karty by velice sní¾il výkon aplikace.</p>

<p class="src0"><span class="kom">Vid_mem = sizeof(Front_buffer) + sizeof(Back_buffer) + num_samples * (sizeof(Front_buffer) + sizeof(ZS_buffer))</span></p>

<p>Pro více informací prosím zkuste tyto odkazy:</p>

<div><?OdkazBlank('http://developer.nvidia.com/attach/3464', 'GDC2002 - OpenGL Multisample');?></div>
<div><?OdkazBlank('http://developer.nvidia.com/attach/2064', 'OpenGL Pixel Formats and Multisample Antialiasing');?></div>

<p>Po tomto nutném úvodu se koneènì mù¾eme pustit do práce. Narozdíl od jiných roz¹íøení, která OpenGL pøi renderingu vyu¾ívá, musíme s ARB_MULTISAMPLE poèítat u¾ pøi vytváøení okna. Postupujeme tedy následovnì:</p>

<ul>
<li>Vytvoøíme okno úplnì stejnì jako obyèejnì</li>
<li>Dotá¾eme se, jestli mù¾eme vyhlazovat pixely</li>
<li>Pokud je multisampling dostupný, zru¹íme okno a vytvoøíme ho s novým pixel formátem</li>
<li>Pro èásti, které chceme vyhlazovat, jednodu¹e zavoláme glEnable(GL_ARB_MULTISAMPLE)</li>
</ul>

<p>Zaèneme v souboru arb_multisample.cpp. Jako v¾dy inkludujeme hlavièkové soubory pro OpenGL a knihovnu GLU. O arb_multisample.h se budeme bavit pozdìji.</p>

<p class="src0">#include &lt;windows.h&gt;</p>
<p class="src0">#include &lt;gl/gl.h&gt;</p>
<p class="src0">#include &lt;gl/glu.h&gt;</p>
<p class="src"></p>
<p class="src0">#include &quot;arb_multisample.h&quot;</p>

<p>Symbolické konstanty pou¾ijeme pøi definování atributù pixel formátu. Podporuje-li grafická karta multisampling, bude logická promìnná arbMultisampleSupported obsahovat true.</p>

<p class="src0">#define WGL_SAMPLE_BUFFERS_ARB 0x2041<span class="kom">// Symbolické konstanty pro multisampling</span></p>
<p class="src0">#define WGL_SAMPLES_ARB 0x2042</p>
<p class="src"></p>
<p class="src0">bool arbMultisampleSupported = false;<span class="kom">// Je multisampling dostupný?</span></p>
<p class="src0">int arbMultisampleFormat = 0;<span class="kom">// Formát multisamplingu</span></p>

<p>Následující funkce testuje, zda je WGL OpenGL roz¹íøení na systému dostupné v daném formátu.</p>

<p class="src0">bool WGLisExtensionSupported(const char *extension)<span class="kom">// Je roz¹íøení podporováno?</span></p>
<p class="src0">{</p>
<p class="src1">const size_t extlen = strlen(extension);</p>
<p class="src1">const char *supported = NULL;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pokud je to mo¾né, pokusí se wglGetExtensionStringARB pou¾ít na aktuální DC</span></p>
<p class="src1">PROC wglGetExtString = wglGetProcAddress(&quot;wglGetExtensionsStringARB&quot;);</p>
<p class="src"></p>
<p class="src1">if (wglGetExtString)<span class="kom">// WGL OpenGL roz¹íøení</span></p>
<p class="src1">{</p>
<p class="src2">supported = ((char*(__stdcall*)(HDC))wglGetExtString)(wglGetCurrentDC());</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (supported == NULL)<span class="kom">// Zkusí je¹tì standardní OpenGL øetìzec s roz¹íøeními</span></p>
<p class="src1">{</p>
<p class="src2">supported = (char*)glGetString(GL_EXTENSIONS);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (supported == NULL)<span class="kom">// Pokud sel¾e i toto, není øetìzec dostupný</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">for (const char* p = supported; ; p++)<span class="kom">// Testování obsahu øetìzce</span></p>
<p class="src1">{</p>
<p class="src2">p = strstr(p, extension);<span class="kom">// Hledá podøetìzec</span></p>
<p class="src"></p>
<p class="src2">if (p == NULL)<span class="kom">// Podøetìzec není v øetìzci</span></p>
<p class="src2">{</p>
<p class="src3">return false;<span class="kom">// Roz¹íøení nebylo nalezeno</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Okolo podøetìzce se musí vyskytovat oddìlovaè (mezera nebo NULL)</span></p>
<p class="src2">if ((p == supported || p[-1] == ' ') &amp;&amp; (p[extlen] == '\0' || p[extlen] == ' '))</p>
<p class="src2">{</p>
<p class="src3">return true;<span class="kom">// Roz¹íøení bylo nalezeno</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Funkce InitMultisample() je svým zpùsobem jádrem programu. Dotá¾eme se na podporu potøebného roz¹íøení a pokud ji máme, získáme po¾adovaný pixel formát.</p>

<p class="src0">bool InitMultisample(HINSTANCE hInstance, HWND hWnd, PIXELFORMATDESCRIPTOR pfd)<span class="kom">// Inicializace multisamplingu</span></p>
<p class="src0">{</p>
<p class="src1">if (!WGLisExtensionSupported(&quot;WGL_ARB_multisample&quot;))<span class="kom">// Existuje øetìzec ve WGL</span></p>
<p class="src1">{</p>
<p class="src2">arbMultisampleSupported = false;</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">PFNWGLCHOOSEPIXELFORMATARBPROC wglChoosePixelFormatARB = (PFNWGLCHOOSEPIXELFORMATARBPROC)wglGetProcAddress(&quot;wglChoosePixelFormatARB&quot;);<span class="kom">// Získání pixel formátu</span></p>
<p class="src"></p>
<p class="src1">if (!wglChoosePixelFormatARB)<span class="kom">// Daný pixel formát není dostupný</span></p>
<p class="src1">{</p>
<p class="src2">arbMultisampleSupported = false;</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">HDC hDC = GetDC(hWnd);<span class="kom">// Získání kontextu zaøízení</span></p>
<p class="src"></p>
<p class="src1">int pixelFormat;</p>
<p class="src1">int valid;</p>
<p class="src1">UINT numFormats;</p>
<p class="src1">float fAttributes[] = {0, 0};</p>

<p>Následující pole atributù slou¾í pro definování vlastností pixel formátu. V¹echny polo¾ky kromì WGL_SAMPLE_BUFFERS_ARB a WGL_SAMPLE_ARB jsou standardní, a proto by nám nemìly èinit potí¾e. Pokud uspìje hlavní test podpory multisamplingu, který reprezentuje wglChoosePixelFormatARB(), máme vyhráno.</p>

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
<p class="src1">valid = wglChoosePixelFormatARB(hDC, iAttributes, fAttributes, 1, &amp;pixelFormat, &amp;numFormats);<span class="kom">// Pixel formát pro ètyøi vzorkování</span></p>
<p class="src"></p>
<p class="src1">if (valid &amp;&amp; numFormats &gt;= 1)<span class="kom">// Vráceno true a poèet formátù je vìt¹í ne¾ jedna</span></p>
<p class="src1">{</p>
<p class="src2">arbMultisampleSupported = true;</p>
<p class="src2">arbMultisampleFormat = pixelFormat;</p>
<p class="src2">return arbMultisampleSupported;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">iAttributes[19] = 2;<span class="kom">// Ètyøi vzorkování nejsou dostupná, test dvou</span></p>
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
<p class="src1">return arbMultisampleSupported;<span class="kom">// Vrácení validního formátu</span></p>
<p class="src0">}</p>

<p>Kód pro detekci multisamplingu máme hotov, teï modifikujeme vytváøení okna. Inkludujeme hlavièkový soubor arb_multisample.h a vytvoøíme funkèní prototypy.</p>

<p class="src0">#include &quot;arb_multisample.h&quot;<span class="kom">// Hlavièkový soubor pro multisampling</span></p>
<p class="src"></p>
<p class="src0">BOOL DestroyWindowGL(GL_Window* window);<span class="kom">// Funkèní prototypy</span></p>
<p class="src0">BOOL CreateWindowGL(GL_Window* window);</p>

<p>Následující výpis kódu patøí do funkce CreateWindowGL(). Pùvodní kód povìt¹inou zùstane, ale udìláme v nìm nìkolik zmìn. V základu potøebujeme vyøe¹it problém, který spoèívá v tom, ¾e nemù¾eme polo¾it dotaz na pixel formát (detekovat pøítomnost multisamplingu), dokud není vytvoøeno okno. Nicménì naproti tomu nemù¾eme vytvoøit okno s vyhlazováním, dokud nemáme pixel formát, který ho podporuje. Trochu se to podobá otázce, zda bylo první vejce nebo slepice. Implementujeme dvouprùchodový systém - nejprve vytvoøíme obyèejné okno, dotá¾eme se na pixel formát a pokud je multisampling podporován, zru¹íme okno a vytvoøíme správné. Trochu tì¾kopádné, ale neznám jiný zpùsob.</p>

<p class="src0"><span class="kom">// Funkce CreateWindowGL()</span></p>
<p class="src1">window-&gt;hDC = GetDC(window-&gt;hWnd);<span class="kom">// Grabování kontextu zaøízení</span></p>
<p class="src"></p>
<p class="src1">if (window-&gt;hDC == 0)<span class="kom">// Podaøilo se ho získat?</span></p>
<p class="src1">{</p>
<p class="src2">DestroyWindow(window-&gt;hWnd);<span class="kom">// Zru¹ení okna</span></p>
<p class="src2">window-&gt;hWnd = 0;<span class="kom">// Nulování handle</span></p>
<p class="src"></p>
<p class="src2">return FALSE;<span class="kom">// Neúspìch</span></p>
<p class="src1">}</p>

<p>Pøi prvním prùchodu touto funkcí (dal¹í prùchody napø. pøi pøepínání do/z fullscreenu) není mo¾né multisampling natvrdo zapnout, tak¾e jsme vytvoøili pouze obyèejné okno. Pokud máme jistotu, ¾e ho mù¾eme pou¾ít, nastavíme pixel formát na arbMultiSampleFormat.</p>

<p class="src1">if(!arbMultisampleSupported)<span class="kom">// Multisampling není podporován</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Vytvoøení normálního okna</span></p>
<p class="src2">PixelFormat = ChoosePixelFormat(window-&gt;hDC, &amp;pfd);<span class="kom">// Získá kompatibilní pixel formát</span></p>
<p class="src"></p>
<p class="src2">if (PixelFormat == 0)<span class="kom">// Podaøilo se ho získat?</span></p>
<p class="src2">{</p>
<p class="src3">ReleaseDC(window-&gt;hWnd, window-&gt;hDC);<span class="kom">// Uvolnìní kontextu zaøízení</span></p>
<p class="src3">window-&gt;hDC = 0;<span class="kom">// Nulování promìnné</span></p>
<p class="src3">DestroyWindow(window-&gt;hWnd);<span class="kom">// Zru¹ení okna</span></p>
<p class="src3">window-&gt;hWnd = 0;<span class="kom">// Nulování handle</span></p>
<p class="src"></p>
<p class="src3">return FALSE;<span class="kom">// Neúspìch</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Multisampling je podporován</span></p>
<p class="src1">{</p>
<p class="src2">PixelFormat = arbMultisampleFormat;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (SetPixelFormat(window-&gt;hDC, PixelFormat, &amp;pfd) == FALSE)<span class="kom">// Zkusí nastavit pixel formát</span></p>
<p class="src1">{</p>
<p class="src2">ReleaseDC(window-&gt;hWnd, window-&gt;hDC);</p>
<p class="src2">window-&gt;hDC = 0;</p>
<p class="src2">DestroyWindow(window-&gt;hWnd);</p>
<p class="src2">window-&gt;hWnd = 0;</p>
<p class="src"></p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">window-&gt;hRC = wglCreateContext(window-&gt;hDC);<span class="kom">// Zkusí získat rendering kontext</span></p>
<p class="src"></p>
<p class="src1">if (window-&gt;hRC == 0)<span class="kom">// Podaøilo se ho získat?</span></p>
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

<p>Okno bylo vytvoøeno, tak¾e máme k dispozici handle pro dotaz na multisampling. Pokud je podporován, zru¹íme okno a vytvoøíme ho s novým pixel formátem.</p>

<p class="src1">if(!arbMultisampleSupported &amp;&amp; CHECK_FOR_MULTISAMPLE)<span class="kom">// Je multisampling dostupný?</span></p>
<p class="src1">{</p>
<p class="src2"></p>
<p class="src2">if(InitMultisample(window-&gt;init.application-&gt;hInstance, window-&gt;hWnd, pfd))<span class="kom">// Inicializace multisamplingu</span></p>
<p class="src2">{</p>
<p class="src3">DestroyWindowGL(window);</p>
<p class="src3">return CreateWindowGL(window);</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">ShowWindow(window-&gt;hWnd, SW_NORMAL);<span class="kom">// Zobrazí okno</span></p>
<p class="src1">window-&gt;isVisible = TRUE;</p>
<p class="src"></p>
<p class="src1">ReshapeGL(window-&gt;init.width, window-&gt;init.height);<span class="kom">// Oznámí rozmìry okna OpenGL</span></p>
<p class="src1">ZeroMemory(window-&gt;keys, sizeof(Keys));<span class="kom">// Nulování pole indikující stisk kláves</span></p>
<p class="src1">window-&gt;lastTickCount = GetTickCount();<span class="kom">// Inicializuje èasovou promìnnou</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹e v poøádku</span></p>
<p class="src0">}</p>

<p>OK, nastavování je kompletní, dostáváme se k zábavnìj¹í èásti, pro kterou jsme se tak sna¾ili. Na¹tìstí se sdru¾ení ARB rozhodlo uèinit multisampling dynamickým, co¾ nám ho umo¾òuje kdykoli zapnout nebo vypnout. Staèí jednoduché glEnable() a glDisable().</p>

<p class="src0">glEnable(GL_MULTISAMPLE_ARB);</p>
<p class="src1"><span class="kom">// Vykreslení vyhlazovaných objektù</span></p>
<p class="src0">glDisable(GL_MULTISAMPLE_ARB);</p>

<p>A to je v¹e. A¾ spustíte ukázkové demo, uvidíte, jak kvalitnì vyhlazování zlep¹uje celkový vzhled scény.</p>

<p class="autor">napsal: Colt McAnlis - MainRoach <?VypisEmail('duhroach@hotmail.com');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson46.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson46_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson46.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson46.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(46);?>
<?FceNeHeOkolniLekce(46);?>

<?
include 'p_end.php';
?>
