<?
$g_title = 'CZ NeHe OpenGL - Jak na �et�i� obrazovky';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Jak na �et�i� obrazovky</h1>

<p class="nadpis_clanku">U� d�vno jsem si cht�l naprogramovat vlastn� �et�i� obrazovky. M�l jsem sice t��du CScreenSaverWnd pro MFC, ale ta nepodporovala OpenGL. U 38. NeHe Tutori�lu jsem na�el odkaz na �et�i� obrazovky s podporou OpenGL, kter� napsal Brian Hunsucker. Cht�l bych mu pod�kovat, proto�e na jeho zdrojov�m k�du z v�t�� ��sti stav� tento �l�nek.</p>

<p>Za�neme parametry spou�t�n� �et�i�e obrazovky. Kter� definuj�, zda se m� spustit �et�i� obrazovky jako takov� (<b>s</b>) nebo pouze jeho konfigura�n� dialog (<b>c</b>). M�li bychom ho z�sk�vat z p��kazov� ��dky testov�n�m parametr� funkce main(), ale defakto se o n�j nemus�me starat, proto�e v�e zajist� knihovna scrnsave, kter� je sou��st� Visual C++. Jenom tak na okraj, k main() se v�bec nedostaneme, proto�e je p�edkompilovan� v scrnsave. Stejn� tak se nestar�me o zav�en� �et�i�e a podobn� v�ci. Program v�e d�l� automaticky.</p>

<p>Jedna mal� pozn�mka: p�i v�voji aplikace se po spu�t�n� zobraz� konfigura�n� dialog. Abychom program spustili jako �et�i�, mus�me mu p�edat parametr <b>s</b>. V Project/Settings pod nab�dkou Debug se mus� napsat do Program arguments p�smeno <b>s</b>.</p>

<p>Vygenerujeme klasick� Win32 Aplication projekt a m��eme ps�t k�d. Vlo��me hlavi�kov� soubory a p�ilinkujeme pot�ebn� knihovny.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;scrnsave.h&gt;<span class="kom">// Hlavi�kov� soubor pro �et�i� obrazovky</span></p>
<p class="src"></p>
<p class="src0">#include &lt;GL/gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL</span></p>
<p class="src0">#include &lt;GL/glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro GLU</span></p>
<p class="src"></p>
<p class="src0">#include &quot;res/resource.h&quot;<span class="kom">// Hlavi�kov� soubor pro Resource (konfigura�n� dialog, ikona ...)</span></p>
<p class="src"></p>
<p class="src0">#pragma comment (lib,&quot;opengl32.lib&quot;)<span class="kom">// P�ilinkov�n� OpenGL</span></p>
<p class="src0">#pragma comment (lib,&quot;glu32.lib&quot;)<span class="kom">// P�ilinkov�n� GLU</span></p>
<p class="src0">#pragma comment (lib,&quot;scrnsave.lib&quot;)<span class="kom">// P�ilinkov�n� knihovny �et�i�e obrazovky</span></p>

<p>Instance aplikace je jedinou glob�ln� prom�nnou.</p>

<p class="src0">HINSTANCE hInstance;<span class="kom">// Ukl�d� instanci aplikace</span></p>

<p>V n�sleduj�c� funkci inicializujeme okno tak, aby podporovalo OpenGL. D� se ��ct, �e s nejv�t�� pravd�podobnost� tuto funkci nebudete muset nikdy zm�nit.</p>

<p class="src0">HGLRC InitOGLWindow(HWND hWnd)<span class="kom">// Inicializace okna</span></p>
<p class="src0">{</p>
<p class="src1">HDC hDC = GetDC(hWnd);<span class="kom">// Kontext za��zen�</span></p>
<p class="src1">HGLRC hRC = 0;<span class="kom">// Renderovac� kontext</span></p>
<p class="src"></p>
<p class="src1">PIXELFORMATDESCRIPTOR pfd;</p>
<p class="src1">int nFormat;</p>
<p class="src"></p>
<p class="src1">ZeroMemory(&amp;pfd, sizeof(PIXELFORMATDESCRIPTOR));</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastaven� okna</span></p>
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
<p class="src1">return hRC;<span class="kom">// Vr�t� renderovac� kontext</span></p>
<p class="src0">}</p>

<p>Do inicializace OpenGL p�id�me i nastaven� perspektivy, kter� se standardn� vkl�d� do funkce pro zm�nu velikosti okna. Nic se nestane, proto�e parametry okna �et�i�e obrazovky se nikdy nezm�n�. Jak tak�? Jakmile se pohne my��, program je ukon�en.</p>

<p class="src0">void InitOpenGL(GLsizei width, GLsizei height)<span class="kom">// Inicializace OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (height==0)<span class="kom">// Proti d�len� nulou</span></p>
<p class="src1">{</p>
<p class="src2">height=1;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glViewport(0,0,width,height);<span class="kom">// Reset Viewportu</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvol� projek�n� matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Perspektiva</span></p>
<p class="src1">gluPerspective(45.0f, (GLfloat)(width)/(GLfloat)(height),1.0f, 20.0f);</p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvol� matici Modelview</span></p>
<p class="src1">glLoadIdentity();</p>

<p>Na tomto m�st� si sami nastavte OpenGL, jak uzn�te za vhodn�. V na�em p��pad� definujeme st�nov�n�, perspektivn� korekce, barvu pozad� a nastaven� hloubkov�ho bufferu.</p>

<p class="src1"><span class="kom">// U�ivatelsk� inicializace</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Vyhlazen� st�nov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivn� korekce</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 1.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne test hloubky</span></p>
<p class="src0">}</p>

<p>Druh�m m�stem, kter� se p�i vytv��en� nov�ho �et�i�e m�n�, je vykreslovac� funkce. Abychom v�d�li, �e program funguje, vykresl�me troj�heln�k a obd�ln�k. Nic sv�toborn�ho...</p>

<p class="src0">void DrawGLScene()</p>
<p class="src0">{</p>
<p class="src1">glClear (GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Posun doleva a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek kreslen� troj�heln�k�</span></p>
<p class="src2">glVertex3f( 0.0f, 1.0f, 0.0f);<span class="kom">// Horn� bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glTranslatef(3.0f,0.0f,0.0f);<span class="kom">// Posun o 3 jednotky doprava</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Lev� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Prav� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen� obd�ln�k�</span></p>
<p class="src"></p>
<p class="src1">glFlush();</p>
<p class="src0">}</p>

<p>I �et�i�i obrazovky pos�l� syst�m zpr�vy. Nen� jich sice mnoho, ale bez n�kter�ch bychom se ur�it� neobe�li. Deklarujeme prom�nn� kontextu za��zen� a renderovac�ho kontextu a d�le v�tv�me funkci podle do�l� zpr�vy.</p>

<p class="src0"><span class="kom">// Procedura okna �et�i�e obrazovky</span></p>
<p class="src0">LRESULT WINAPI ScreenSaverProc(HWND hWnd, UINT message, WPARAM wParam, LPARAM lParam)</p>
<p class="src0">{</p>
<p class="src1">HDC hDC;<span class="kom">// Kontext za��zen�</span></p>
<p class="src1">static HGLRC hRC;<span class="kom">// Renderovac� kontext</span></p>
<p class="src"></p>
<p class="src1">switch (message)<span class="kom">// V�tv� program podle do�l� zpr�vy</span></p>
<p class="src1">{ </p>

<p>Na zpr�vu WM_CREATE, kterou pos�l� syst�m ihned po vytvo�en� okna, inicializujeme okno. Pot� pomoc� funkce GetClientRect() nagrabujeme jeho ���ku a v��ku, kterou p�ed�me do InitOpenGL(). Nakonec spust�me timer zaji��uj�c� periodick� p�ekreslov�n� sc�ny.</p>

<p class="src2">case WM_CREATE:<span class="kom">// Vytvo�en� okna</span></p>
<p class="src"></p>
<p class="src3">hRC = InitOGLWindow(hWnd);<span class="kom">// Z�sk�n� kontextu za��zen�</span></p>
<p class="src"></p>
<p class="src3">RECT WindowRect;<span class="kom">// Pro zji�t�n� velikosti okna</span></p>
<p class="src"></p>
<p class="src3">int width;<span class="kom">// ���ka</span></p>
<p class="src3">int height;<span class="kom">// V��ka</span></p>
<p class="src"></p>
<p class="src3">GetClientRect(hWnd, &amp;WindowRect);<span class="kom">// Z�sk� velikost okna</span></p>
<p class="src"></p>
<p class="src3">width = WindowRect.right - WindowRect.left;<span class="kom">// ���ka okna</span></p>
<p class="src3">height = WindowRect.bottom - WindowRect.top;<span class="kom">// V��ka okna</span></p>
<p class="src"></p>
<p class="src3">InitOpenGL(width, height);<span class="kom">// Inicializace OpenGL</span></p>
<p class="src"></p>
<p class="src3">SetTimer(hWnd, 1, 20, NULL);<span class="kom">// Zapnut� timeru</span></p>
<p class="src"></p>
<p class="src3">break;</p>

<p>O kousek v��e jsme spustili timer. Nyn� definujeme, �e po p��chodu jeho zpr�vy se m� z�skat DC. Pot� p�ekresl�me sc�nu, prohod�me buffery a op�t uvoln�me DC. K�d je tak kr�tk�, �e vytv��et novou funkci je �pln� zbyte�n�.</p>

<p class="src2">case WM_TIMER:<span class="kom">// Zpr�va od �asova�e</span></p>
<p class="src"></p>
<p class="src3">hDC = GetDC(hWnd);<span class="kom">// Z�sk�n� kontextu za��zen�</span></p>
<p class="src"></p>
<p class="src3">DrawGLScene();<span class="kom">// Vykresl� sc�nu</span></p>
<p class="src3">SwapBuffers(hDC);<span class="kom">// Prohod� buffery</span></p>
<p class="src"></p>
<p class="src3">ReleaseDC(hWnd, hDC);<span class="kom">// Uvoln� kontext za��zen�</span></p>
<p class="src"></p>
<p class="src3">break;</p>

<p>Na zpr�vu WM_DESTROY provedeme deinicializaci.</p>

<p class="src2">case WM_DESTROY:<span class="kom">// Zav�en� okna</span></p>
<p class="src"></p>
<p class="src3">KillTimer(hWnd,1); <span class="kom">// Vypnut� timeru</span></p>
<p class="src"></p>
<p class="src3">wglMakeCurrent(NULL, NULL);</p>
<p class="src3">wglDeleteContext(hRC);<span class="kom">// Sma�e renderovac� kontext</span></p>
<p class="src"></p>
<p class="src3">break;</p>
<p class="src1">}</p>

<p>V�echny ostatn� zpr�vy p�ed�me d�le.</p>

<p class="src1">return DefScreenSaverProc(hWnd, message, wParam, lParam);<span class="kom">// Neo�et�en� zpr�vy</span></p>
<p class="src0">}</p>

<p>�pln� na za��tku jsme si �ekli, �e program m� dv� cesty prov�d�n�. Klasick� �et�i� u� m�me, nyn� se vrhneme na konfigura�n� dialog. Nejprve ho vytvo��me. M� dv� tla��tka OK a CANCEL (pop�. i dal��). Za�neme mapou zpr�v a d� se ��ct, �e i skon��me. P�i WM_INITDIALOG m��eme jednotliv� prvky inicializovat na hodnoty, kter� z�sk�me nap��klad z pomocn�ho souboru nebo z registr� Windows. Ur�ovaly by chov�n� �et�i�e, ale n� program je velmi jednoduch�, tak pro� ho komplikovat. Namapov�n�m zpr�vy WM_COMMAND ur��me, co se m� ud�lat po kliknut� na tla��tka.</p>

<p class="src0"><span class="kom">// Procedura okna konfigura�n�ho dialogu</span></p>
<p class="src0">BOOL WINAPI ScreenSaverConfigureDialog(HWND hDlg, UINT message, WPARAM wParam, LPARAM lParam)</p>
<p class="src0">{</p>
<p class="src1">switch (message)<span class="kom">// V�tv� program podle do�l� zpr�vy</span></p>
<p class="src1">{</p>
<p class="src2">case WM_INITDIALOG:<span class="kom">// Inicializace dialogu</span></p>
<p class="src3">return TRUE;<span class="kom">// V po��dku</span></p>
<p class="src"></p>
<p class="src2">case WM_COMMAND:<span class="kom">// P��kaz (nap�. kliknut� na tla��tko)</span></p>
<p class="src"></p>
<p class="src3">switch (LOWORD(wParam))<span class="kom">// Kter� tla��tko?</span></p>
<p class="src3">{</p>
<p class="src3">case IDOK:<span class="kom">// OK</span></p>
<p class="src4">EndDialog(hDlg, TRUE);<span class="kom">// Zav�e dialog</span></p>
<p class="src4">return TRUE;<span class="kom">// V po��dku</span></p>
<p class="src"></p>
<p class="src3">case IDCANCEL:<span class="kom">// Cancel</span></p>
<p class="src4">EndDialog(hDlg, TRUE);<span class="kom">// Zav�e dialog</span></p>
<p class="src4">break;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return FALSE;<span class="kom">// False - u�ivatel nap�. stiskl Cancel</span></p>
<p class="src0">}</p>

<p>K �emu je tato funkce, abych �ekl pravdu, nev�m, ale kompil�tor mi bez n� hl�s� chyby.</p>

<p class="src0">BOOL WINAPI RegisterDialogClasses(HANDLE hInst)<span class="kom">// ???</span></p>
<p class="src0">{</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>No a t�m jsme i skon�ili. Mo�n� se pt�te: &quot;A opravdu to funguje?&quot;. Ano, tak� jsem nech�pal. Kdy� jsem tento k�d vid�l poprv�, bez main() a jak�chkoli jin�ch n�vaznost� pochyboval jsem, �e sv�j vlastn� �et�i� n�kdy v �ivot� rozjedu, ale poda�ilo se. �lov�k se nemus� skoro o nic starat v�e je p�ipraveno v knihovn� scrnsave. St�hn�te si zdrojov� k�d a uvid�te.</p>

<p>Abych nezapomn�l, v�sledn� .exe soubor je nutn� p�ejmenovat na .scr a zkop�rovat do Windows/System. A� potom budete moci  v nastaven� obrazovky vym�nit �et�i� za nov� (samoz�ejm� lep��).</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/setric.rar');?> - Visual C++</li>
</ul>

<?
include 'p_end.php';
?>
