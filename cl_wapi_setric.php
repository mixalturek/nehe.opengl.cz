<?
$g_title = 'CZ NeHe OpenGL - Jak na ¹etøiè obrazovky';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Jak na ¹etøiè obrazovky</h1>

<p class="nadpis_clanku">U¾ dávno jsem si chtìl naprogramovat vlastní ¹etøiè obrazovky. Mìl jsem sice tøídu CScreenSaverWnd pro MFC, ale ta nepodporovala OpenGL. U 38. NeHe Tutoriálu jsem na¹el odkaz na ¹etøiè obrazovky s podporou OpenGL, který napsal Brian Hunsucker. Chtìl bych mu podìkovat, proto¾e na jeho zdrojovém kódu z vìt¹í èásti staví tento èlánek.</p>

<p>Zaèneme parametry spou¹tìní ¹etøièe obrazovky. Které definují, zda se má spustit ¹etøiè obrazovky jako takový (<b>s</b>) nebo pouze jeho konfiguraèní dialog (<b>c</b>). Mìli bychom ho získávat z pøíkazové øádky testováním parametrù funkce main(), ale defakto se o nìj nemusíme starat, proto¾e v¹e zajistí knihovna scrnsave, která je souèástí Visual C++. Jenom tak na okraj, k main() se vùbec nedostaneme, proto¾e je pøedkompilovaná v scrnsave. Stejnì tak se nestaráme o zavøení ¹etøièe a podobné vìci. Program v¹e dìlá automaticky.</p>

<p>Jedna malá poznámka: pøi vývoji aplikace se po spu¹tìní zobrazí konfiguraèní dialog. Abychom program spustili jako ¹etøiè, musíme mu pøedat parametr <b>s</b>. V Project/Settings pod nabídkou Debug se musí napsat do Program arguments písmeno <b>s</b>.</p>

<p>Vygenerujeme klasický Win32 Aplication projekt a mù¾eme psát kód. Vlo¾íme hlavièkové soubory a pøilinkujeme potøebné knihovny.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;scrnsave.h&gt;<span class="kom">// Hlavièkový soubor pro ¹etøiè obrazovky</span></p>
<p class="src"></p>
<p class="src0">#include &lt;GL/gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL</span></p>
<p class="src0">#include &lt;GL/glu.h&gt;<span class="kom">// Hlavièkový soubor pro GLU</span></p>
<p class="src"></p>
<p class="src0">#include &quot;res/resource.h&quot;<span class="kom">// Hlavièkový soubor pro Resource (konfiguraèní dialog, ikona ...)</span></p>
<p class="src"></p>
<p class="src0">#pragma comment (lib,&quot;opengl32.lib&quot;)<span class="kom">// Pøilinkování OpenGL</span></p>
<p class="src0">#pragma comment (lib,&quot;glu32.lib&quot;)<span class="kom">// Pøilinkování GLU</span></p>
<p class="src0">#pragma comment (lib,&quot;scrnsave.lib&quot;)<span class="kom">// Pøilinkování knihovny ¹etøièe obrazovky</span></p>

<p>Instance aplikace je jedinou globální promìnnou.</p>

<p class="src0">HINSTANCE hInstance;<span class="kom">// Ukládá instanci aplikace</span></p>

<p>V následující funkci inicializujeme okno tak, aby podporovalo OpenGL. Dá se øíct, ¾e s nejvìt¹í pravdìpodobností tuto funkci nebudete muset nikdy zmìnit.</p>

<p class="src0">HGLRC InitOGLWindow(HWND hWnd)<span class="kom">// Inicializace okna</span></p>
<p class="src0">{</p>
<p class="src1">HDC hDC = GetDC(hWnd);<span class="kom">// Kontext zaøízení</span></p>
<p class="src1">HGLRC hRC = 0;<span class="kom">// Renderovací kontext</span></p>
<p class="src"></p>
<p class="src1">PIXELFORMATDESCRIPTOR pfd;</p>
<p class="src1">int nFormat;</p>
<p class="src"></p>
<p class="src1">ZeroMemory(&amp;pfd, sizeof(PIXELFORMATDESCRIPTOR));</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastavení okna</span></p>
<p class="src1">pfd.nSize = sizeof(PIXELFORMATDESCRIPTOR);</p>
<p class="src1">pfd.nVersion = 1;</p>
<p class="src1">pfd.dwFlags = PFD_SUPPORT_OPENGL | PFD_DRAW_TO_WINDOW | PFD_DOUBLEBUFFER;</p>
<p class="src1">pfd.cColorBits = 24;</p>
<p class="src1">pfd.cDepthBits = 24;</p>
<p class="src"></p>
<p class="src1">nFormat = ChoosePixelFormat(hDC, &amp;pfd);</p>
<p class="src1">DescribePixelFormat(hDC, nFormat, sizeof(PIXELFORMATDESCRIPTOR), &amp;pfd);</p>
<p class="src1">SetPixelFormat(hDC, nFormat, &amp;pfd);</p>
<p class="src"></p>
<p class="src1">hRC = wglCreateContext(hDC);</p>
<p class="src1">wglMakeCurrent(hDC, hRC);</p>
<p class="src"></p>
<p class="src1">ReleaseDC(hWnd, hDC);</p>
<p class="src"></p>
<p class="src1">return hRC;<span class="kom">// Vrátí renderovací kontext</span></p>
<p class="src0">}</p>

<p>Do inicializace OpenGL pøidáme i nastavení perspektivy, která se standardnì vkládá do funkce pro zmìnu velikosti okna. Nic se nestane, proto¾e parametry okna ¹etøièe obrazovky se nikdy nezmìní. Jak také? Jakmile se pohne my¹í, program je ukonèen.</p>

<p class="src0">void InitOpenGL(GLsizei width, GLsizei height)<span class="kom">// Inicializace OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (height==0)<span class="kom">// Proti dìlení nulou</span></p>
<p class="src1">{</p>
<p class="src2">height=1;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glViewport(0,0,width,height);<span class="kom">// Reset Viewportu</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvolí projekèní matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Perspektiva</span></p>
<p class="src1">gluPerspective(45.0f, (GLfloat)(width)/(GLfloat)(height),1.0f, 20.0f);</p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvolí matici Modelview</span></p>
<p class="src1">glLoadIdentity();</p>

<p>Na tomto místì si sami nastavte OpenGL, jak uznáte za vhodné. V na¹em pøípadì definujeme stínování, perspektivní korekce, barvu pozadí a nastavení hloubkového bufferu.</p>

<p class="src1"><span class="kom">// U¾ivatelská inicializace</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Vyhlazené stínování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivní korekce</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 1.0f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne test hloubky</span></p>
<p class="src0">}</p>

<p>Druhým místem, které se pøi vytváøení nového ¹etøièe mìní, je vykreslovací funkce. Abychom vìdìli, ¾e program funguje, vykreslíme trojúhelník a obdélník. Nic svìtoborného...</p>

<p class="src0">void DrawGLScene()</p>
<p class="src0">{</p>
<p class="src1">glClear (GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Zaèátek kreslení trojúhelníkù</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horní bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glTranslatef(3.0f,0.0f,0.0f);<span class="kom">// Posun o 3 jednotky doprava</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Levý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Pravý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení obdélníkù</span></p>
<p class="src"></p>
<p class="src1">glFlush();</p>
<p class="src0">}</p>

<p>I ¹etøièi obrazovky posílá systém zprávy. Není jich sice mnoho, ale bez nìkterých bychom se urèitì neobe¹li. Deklarujeme promìnné kontextu zaøízení a renderovacího kontextu a dále vìtvíme funkci podle do¹lé zprávy.</p>

<p class="src0"><span class="kom">// Procedura okna ¹etøièe obrazovky</span></p>
<p class="src0">LRESULT WINAPI ScreenSaverProc(HWND hWnd, UINT message, WPARAM wParam, LPARAM lParam)</p>
<p class="src0">{</p>
<p class="src1">HDC hDC;<span class="kom">// Kontext zaøízení</span></p>
<p class="src1">static HGLRC hRC;<span class="kom">// Renderovací kontext</span></p>
<p class="src"></p>
<p class="src1">switch (message)<span class="kom">// Vìtví program podle do¹lé zprávy</span></p>
<p class="src1">{ </p>

<p>Na zprávu WM_CREATE, kterou posílá systém ihned po vytvoøení okna, inicializujeme okno. Poté pomocí funkce GetClientRect() nagrabujeme jeho ¹íøku a vý¹ku, kterou pøedáme do InitOpenGL(). Nakonec spustíme timer zaji¹»ující periodické pøekreslování scény.</p>

<p class="src2">case WM_CREATE:<span class="kom">// Vytvoøení okna</span></p>
<p class="src"></p>
<p class="src3">hRC = InitOGLWindow(hWnd);<span class="kom">// Získání kontextu zaøízení</span></p>
<p class="src"></p>
<p class="src3">RECT WindowRect;<span class="kom">// Pro zji¹tìní velikosti okna</span></p>
<p class="src"></p>
<p class="src3">int width;<span class="kom">// ©íøka</span></p>
<p class="src3">int height;<span class="kom">// Vý¹ka</span></p>
<p class="src"></p>
<p class="src3">GetClientRect(hWnd, &amp;WindowRect);<span class="kom">// Získá velikost okna</span></p>
<p class="src"></p>
<p class="src3">width = WindowRect.right - WindowRect.left;<span class="kom">// ©íøka okna</span></p>
<p class="src3">height = WindowRect.bottom - WindowRect.top;<span class="kom">// Vý¹ka okna</span></p>
<p class="src"></p>
<p class="src3">InitOpenGL(width, height);<span class="kom">// Inicializace OpenGL</span></p>
<p class="src"></p>
<p class="src3">SetTimer(hWnd, 1, 20, NULL);<span class="kom">// Zapnutí timeru</span></p>
<p class="src"></p>
<p class="src3">break;</p>

<p>O kousek vý¹e jsme spustili timer. Nyní definujeme, ¾e po pøíchodu jeho zprávy se má získat DC. Poté pøekreslíme scénu, prohodíme buffery a opìt uvolníme DC. Kód je tak krátký, ¾e vytváøet novou funkci je úplnì zbyteèné.</p>

<p class="src2">case WM_TIMER:<span class="kom">// Zpráva od èasovaèe</span></p>
<p class="src"></p>
<p class="src3">hDC = GetDC(hWnd);<span class="kom">// Získání kontextu zaøízení</span></p>
<p class="src"></p>
<p class="src3">DrawGLScene();<span class="kom">// Vykreslí scénu</span></p>
<p class="src3">SwapBuffers(hDC);<span class="kom">// Prohodí buffery</span></p>
<p class="src"></p>
<p class="src3">ReleaseDC(hWnd, hDC);<span class="kom">// Uvolní kontext zaøízení</span></p>
<p class="src"></p>
<p class="src3">break;</p>

<p>Na zprávu WM_DESTROY provedeme deinicializaci.</p>

<p class="src2">case WM_DESTROY:<span class="kom">// Zavøení okna</span></p>
<p class="src"></p>
<p class="src3">KillTimer(hWnd,1); <span class="kom">// Vypnutí timeru</span></p>
<p class="src"></p>
<p class="src3">wglMakeCurrent(NULL, NULL);</p>
<p class="src3">wglDeleteContext(hRC);<span class="kom">// Sma¾e renderovací kontext</span></p>
<p class="src"></p>
<p class="src3">break;</p>
<p class="src1">}</p>

<p>V¹echny ostatní zprávy pøedáme dále.</p>

<p class="src1">return DefScreenSaverProc(hWnd, message, wParam, lParam);<span class="kom">// Neo¹etøené zprávy</span></p>
<p class="src0">}</p>

<p>Úplnì na zaèátku jsme si øekli, ¾e program má dvì cesty provádìní. Klasický ¹etøiè u¾ máme, nyní se vrhneme na konfiguraèní dialog. Nejprve ho vytvoøíme. Má dvì tlaèítka OK a CANCEL (popø. i dal¹í). Zaèneme mapou zpráv a dá se øíct, ¾e i skonèíme. Pøi WM_INITDIALOG mù¾eme jednotlivé prvky inicializovat na hodnoty, které získáme napøíklad z pomocného souboru nebo z registrù Windows. Urèovaly by chování ¹etøièe, ale ná¹ program je velmi jednoduchý, tak proè ho komplikovat. Namapováním zprávy WM_COMMAND urèíme, co se má udìlat po kliknutí na tlaèítka.</p>

<p class="src0"><span class="kom">// Procedura okna konfiguraèního dialogu</span></p>
<p class="src0">BOOL WINAPI ScreenSaverConfigureDialog(HWND hDlg, UINT message, WPARAM wParam, LPARAM lParam)</p>
<p class="src0">{</p>
<p class="src1">switch (message)<span class="kom">// Vìtví program podle do¹lé zprávy</span></p>
<p class="src1">{</p>
<p class="src2">case WM_INITDIALOG:<span class="kom">// Inicializace dialogu</span></p>
<p class="src3">return TRUE;<span class="kom">// V poøádku</span></p>
<p class="src"></p>
<p class="src2">case WM_COMMAND:<span class="kom">// Pøíkaz (napø. kliknutí na tlaèítko)</span></p>
<p class="src"></p>
<p class="src3">switch (LOWORD(wParam))<span class="kom">// Které tlaèítko?</span></p>
<p class="src3">{</p>
<p class="src3">case IDOK:<span class="kom">// OK</span></p>
<p class="src4">EndDialog(hDlg, TRUE);<span class="kom">// Zavøe dialog</span></p>
<p class="src4">return TRUE;<span class="kom">// V poøádku</span></p>
<p class="src"></p>
<p class="src3">case IDCANCEL:<span class="kom">// Cancel</span></p>
<p class="src4">EndDialog(hDlg, TRUE);<span class="kom">// Zavøe dialog</span></p>
<p class="src4">break;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return FALSE;<span class="kom">// False - u¾ivatel napø. stiskl Cancel</span></p>
<p class="src0">}</p>

<p>K èemu je tato funkce, abych øekl pravdu, nevím, ale kompilátor mi bez ní hlásí chyby.</p>

<p class="src0">BOOL WINAPI RegisterDialogClasses(HANDLE hInst)<span class="kom">// ???</span></p>
<p class="src0">{</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>No a tím jsme i skonèili. Mo¾ná se ptáte: &quot;A opravdu to funguje?&quot;. Ano, také jsem nechápal. Kdy¾ jsem tento kód vidìl poprvé, bez main() a jakýchkoli jiných návazností pochyboval jsem, ¾e svùj vlastní ¹etøiè nìkdy v ¾ivotì rozjedu, ale podaøilo se. Èlovìk se nemusí skoro o nic starat v¹e je pøipraveno v knihovnì scrnsave. Stáhnìte si zdrojový kód a uvidíte.</p>

<p>Abych nezapomnìl, výsledný .exe soubor je nutné pøejmenovat na .scr a zkopírovat do Windows/System. A¾ potom budete moci  v nastavení obrazovky vymìnit ¹etøiè za nový (samozøejmì lep¹í).</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/setric.rar');?> - Visual C++</li>
</ul>

<?
include 'p_end.php';
?>
