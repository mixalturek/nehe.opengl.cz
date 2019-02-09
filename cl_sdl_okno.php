<?
$g_title = 'CZ NeHe OpenGL - Vytvoøení SDL okna';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Vytvoøení SDL okna</h1>

<p class="nadpis_clanku">Woq mì po¾ádal, abych napsal tutoriál pro pou¾ívání OpenGL pod knihovnou SDL. Je to mùj první tutoriál, tak¾e doufám, ¾e se bude líbit. Zkusíme vytvoøit kód, který bude odpovídat druhé lekci &quot;normálních&quot; tutoriálù. Zdrojový kód je &quot;oficiální&quot; NeHe port druhé lekce do SDL. Pokusím se popsat, jak se vytváøí okno a vìci okolo.</p>

<p>Na úvod bych chtìl podìkovat Bernymu za sebe i za v¹echny, kterým tento èlánek velice pomohl, a kteøí si myslí, ¾e dìlat programy výhradnì pro komerèní Wokna není tou správnou cestou.</p>

<p>Nejdøíve nìco o tom, co je SDL. SDL, univerzální knihovna, slou¾í pro tvorbu her. Znáte-li Allegro, tak SDL je mu trochu podobné. Bylo napsáno v C(/C++) a jeho domovské prostøedí bylo Linux. Nyní ale existuje i pod BeOS, MacOS, Windows, QNX, AmigaOS, PS2,.... Obsahuje základní API pro vytvoøení okna, 2D grafiku, vícevláknové programování, obsluhu klávesnice, krysy a joysticku, pøehrávání videa a zvukù. Ke knihovnì jsou je¹tì podpùrné knihovnièky pro naèítáni rozmanitých grafických formátù, pro lep¹í ozvuèení (mp3, ogg,..) a sí»ové slu¾by pro multiplayer. Pøímo podporuje vyu¾ití OpenGL, které samo o sobì manipulaci s okny, komunikaci se systémem a v¹echnu práci okolo neumí a tudí¾ nedìlá. Kód který vytvoøíte v SDL mù¾ete velice snadno pøenést jinam jednoduchým pøekompilováním (bude to fungovat, pokud nepí¹ete jako èuòata). SDL není jen nìjaká malá knihovnièka, ale pou¾ívají ji i komerèní hry. Vìt¹ina her od Lokisoft (portují hry pod Linux) je nad SDL a Unreal Tournament 2003 také. Nejlep¹í bude, pokud nav¹tívíte domovský web - <?OdkazBlank('http://www.libsdl.org/');?>. Jsou tam v¹emo¾né helpy, návody, manuály a také vystaveny práce lidí, kteøí SDL pou¾ívají; pár dílek i z ÈR.</p>

<p>Ne¾ zaèneme, ujistìte se, ¾e máte knihovnu SDL. Pokud ne, nav¹tivte vý¹e uvedenou adresu a stáhnìte si soubory pro va¹i konkrétní platformu.</p>

<p>A teï u¾ se vrhneme na programování. Nejdøíve vlo¾íme hlavièkové soubory. Mo¾ná se to zdá na první pohled jako ¹ílenost, ale vyøe¹íme pøená¹ení kódu na rùzné OS. Proto doporuèuji toto vkládání nechat tak, jak je.</p>

<p class="src0">#ifdef WIN32<span class="kom"> // Pokud se bude kompilovat program po Windows</span></p>
<p class="src1">#define WIN32_LEAN_AND_MEAN</p>
<p class="src1">#include &lt;windows.h&gt;<span class="kom"> // Hlavièkový soubor pro Windows</span></p>
<p class="src0">#endif</p>
<p class="src"></p>
<p class="src0">#if defined(__APPLE__) &amp;&amp; defined(__MACH__)<span class="kom"> // Pokud se bude kompilovat program pro Apple</span></p>
<p class="src1">#include &lt;OpenGL/gl.h&gt;<span class="kom"> // Hlavièkový soubor pro OpenGL32 knihovnu v Applu</span></p>
<p class="src1">#include &lt;OpenGL/glu.h&gt;<span class="kom"> // Hlavièkový soubor pro GLu32 knihovnu v Applu</span></p>
<p class="src0">#else</p>
<p class="src1">#include &lt;GL/gl.h&gt;<span class="kom"> // Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src1">#include &lt;GL/glu.h&gt;<span class="kom"> // Hlavièkový soubor pro GLu32 knihovnu</span></p>
<p class="src0">#endif</p>
<p class="src"></p>
<p class="src0">#include "SDL.h"<span class="kom"> // Hlavièkový soubor pro SDL</span></p>

<p>Vytvoøíme funkci na inicializaci OpenGL (smíchané InitGL() a ResizeGLScene() z klasických tutoriálù). Na¹ím cílem není rozebírat OpenGL, ale spí¹e vysvìtlit SDL. Pro detaily se podívejte do druhé lekce.</p>

<p class="src0">void InitGL(int Width, int Height)<span class="kom"> // Tuto funkci voláme hned po vytvoøení okna pro inicializace OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glViewport(0, 0, Width, Height);</p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom"> // Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom"> // Povolíme mazání pozadí</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom"> // Vybereme typ  Depth Testu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom"> // Povolíme Depth Test</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom"> // Povolíme Smooth Color Shading</span></p>
<p class="src1">glMatrixMode(GL_PROJECTION);</p>
<p class="src1">glLoadIdentity();<span class="kom"> // Resetujeme projekèní matici</span></p>
<p class="src1">gluPerspective(45.0f,(GLfloat)Width/(GLfloat)Height,0.1f,100.0f);<span class="kom"> // Vypoèítáme pomìr okna</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);</p>
<p class="src0">}</p>

<p>Vykreslování...</p>

<p class="src0">void DrawGLScene()<span class="kom"> // Hlavní vykreslovací funkce</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom"> // Vymazání obrazovkového a hloubkového bufferu</span></p>
<p class="src1">glLoadIdentity();<span class="kom"> // Reset matice pohledu</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom"> // Posun o 1.5 doleva a o 6 do hloubky</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nakreslí trojúhelník</span></p>
<p class="src1">glBegin(GL_POLYGON);</p>
<p class="src2">glVertex3f(0.0f, 1.0f, 0.0f);</p>
<p class="src2">glVertex3f(1.0f,-1.0f, 0.0f);</p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glTranslatef(3.0f,0.0f,0.0f);<span class="kom"> // Posuneme se o 3 doprava</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nakreslí ètverec</span></p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);</p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);</p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);</p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);</p>
<p class="src1">glEnd();</p>

<p>Na konci provedeme drobnou zmìnu. Zavoláme funkci SDL_GL_SwapBuffers(), která zajistí, ¾e se na obrazovce objeví v¹e, co jsme ve funkci DrawGLScene() nakreslili. V klasických tutoriálech najdete SwapBuffers() ve WinMain() po volání této funkce.</p>

<p class="src1">SDL_GL_SwapBuffers();<span class="kom"> // Prohozeni bufferu, aby se zobrazilo, co jsme nakreslili</span></p>
<p class="src0">}</p>

<p>®e se funkce main() provádí po spu¹tìní programu jako první doufám víte :-] Le¾í zde vìt¹ina SDL kódu. Nejprve provedeme inicializaci a pak cyklujeme dokola v jednoduché smyèce zpráv. Dávejte si pozor, aby deklarace main() byla pøesnì taková, jak má být - nikdy nevynechávat argumenty a návratovou hodnotu.</p>

<p class="src0">int main(int argc, char **argv)<span class="kom"> // Prostì main() :-)</span></p>
<p class="src0">{</p>
<p class="src1">int done;<span class="kom"> // Ukonèovací promìnná</span></p>

<p>Inicializace knihovny se musí zavolat jako první pøed v¹ím ostatním. SDL_INIT_VIDEO znamená, ¾e budeme chtít inicializovat výstup na obrazovku. Mù¾e se kombinovat i s dal¹ími jako tøeba timerem, audiem,... Knihovna SDL nám umo¾òuje pou¾ívat stdout a stderr pro textový výstup. V Linuxu vypisuje na terminál, ve windows vytvoøí soubory stdout.txt a stderr.txt (v pøípadì, ¾e do nich nic nezapí¹ete je pak opìt sma¾e).</p>

<p class="src1">if ( SDL_Init(SDL_INIT_VIDEO) &lt; 0 )<span class="kom"> // Inicializace SDL s grafickým výstupem</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, "Selhala inicializace SDL: %s\n", SDL_GetError());</p>
<p class="src2">exit(1);</p>
<p class="src1">}</p>

<p>Teï mù¾eme vytvoøit okno aplikace. Máme k tomu funkci SDL_SetVideoMode. První dva parametry jsou rozmìry okna, dal¹í parametr urèuje barevnou hloubku (0 zachovává souèasnou). Poslední parametr je kombinací rùzných flagù. Pokud chceme po¾ívat OpenGL, musíme zadat SDL_OPENGL (nebo SDL_OPENGLBLIT). Pokud chcete fullscreen, mù¾ete pøidat flag SDL_FULLSCREEN. V X11 (Linuxu) mù¾ete pøepínat mezi fullscreenem a oknem svobodnì kdykoliv se vám zachce voláním funkce SDL_WM_ToggleFullScreen(), ale ve Windows si v SDL_SetVideoMode() jednou vyberete a to vám pak zùstane. Proto¾e jsme inicializovali SDL, musíme ho v pøípadì erroru také ukonèit. Má to na starosti funkce SDL_Quit().</p>

<p class="src1">if (SDL_SetVideoMode(640, 480, 0, SDL_OPENGL) == NULL)<span class="kom"> // Vytvoøení OpenGL okna 640x480</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, "Neni mozne vytvorit OpenGL okno: %s\n", SDL_GetError());</p>
<p class="src2">SDL_Quit();<span class="kom"> // Ukonèení SDL</span></p>
<p class="src2">exit(2);</p>
<p class="src1">}</p>

<p>Nastavíme titulek okna. Mù¾eme pøidat i ikonku, ale proto¾e ¾ádnou nemáme, pou¾ijeme NULL.</p>

<p class="src1">SDL_WM_SetCaption("Jeff Molofee's GL Code Tutorial ... NeHe '99", NULL);<span class="kom"> // Titulek okna</span></p>
<p class="src1">InitGL(640, 480);<span class="kom"> // Inicializace OpenGL</span></p>

<p>Hlavní cyklus programu. Pro opu¹tìní programu pou¾ijeme promìnnou done (deklarovaná na zaèátku main(), kterou testujeme v ka¾dém prùbìhu hlavním cyklem.</p>

<p class="src1">done = 0;<span class="kom"> // Je¹tì nekonèit</span></p>
<p class="src1">while (!done)<span class="kom"> // Hlavní cyklus programu</span></p>
<p class="src1">{</p>
<p class="src2">DrawGLScene();<span class="kom"> // Vykreslení scény</span></p>

<p>Obsluha událostí by mìla pøijít do zvlá¹tní funkce (analogie WndProc() z Windows), ale nebudeme jednoduchý kód komplikovat. Nejprve vytvoøíme promìnou typu SDL_Event. Pak pro naètení zpráv voláme v cyklu SDL_PollEvent(). Dokud pøicházejí události, tak se jimi zabýváme. Tentokrát zareagujeme pouze na po¾adavek ukonèení pomocí klávesy ESC. Pøesuny okna, zmìna jeho velikosti ap. se dìjí automaticky.</p>

<p class="src2">SDL_Event event;<span class="kom"> // Promìnná zprávy</span></p>
<p class="src"></p>
<p class="src2">while (SDL_PollEvent(&amp;event))<span class="kom"> // Zpracovávat zprávy</span></p>
<p class="src2">{</p>
<p class="src3">if (event.type == SDL_QUIT)<span class="kom"> // Zpráva o ukonèení</span></p>
<p class="src3">{</p>
<p class="src4">done = 1;<span class="kom"> // Ukonèit hlavní cyklus</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (event.type == SDL_KEYDOWN)<span class="kom"> // Zpráva o stisku klávesy</span></p>
<p class="src3">{</p>
<p class="src4">if (event.key.keysym.sym == SDLK_ESCAPE)<span class="kom"> // Klávesa ESC</span></p>
<p class="src4">{</p>
<p class="src5">done = 1;<span class="kom"> // Ukonèit hlavní cyklus</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Na konci u¾ jen ukonèíme SDL.</p>

<p class="src1">SDL_Quit();<span class="kom"> // Ukonèení SDL</span></p>
<p class="src1">return 0;</p>
<p class="src0">}</p>

<p>Pro kompilaci tutoriálu v Linuxu mù¾eme pou¾ít napø. pøíkaz ní¾e. Spustíme standardní kompilátor gcc a zapneme v¹echna varovná hlá¹ení. Chceme zkompilovat soubor lesson02.c a pøilinkovat knihovny pro SDL (v¹imnìte si zpìtných apostrofù!!! - na klávesnici pod Esc), OpenGL a GLU. Výsledný spustitelný soubor se vytvoøí ve stejném adresáøi, ve kterém se právì nacházíme, a bude mít název lesson02. Tento nový program se mù¾e spustit buï pøes libovolný souborový mana¾er nebo - kdy¾ u¾ jsme ve shellu - pøíkazem ./lesson02. Pokud dáme na konec ampérsand, spustí se na pozadí a nebudeme mít blokovanou pøíkazovou øádku. Aby ¹el program spustit, musí na poèítaèi bì¾et XFree server (XFree86, X, X11, XWindow - prostì grafický re¾im :-).</p>

<p class="src0">[woq@komputerovka c]$ gcc -Wall lesson02.c `sdl-config --libs --cflags` -lGL -lGLU  -o lesson02</p>
<p class="src0">[woq@komputerovka c]$ ./lesson02 &amp;</p>

<p>Ve Windows a Visual C++ je zprovoznìní malièko komplikovanìj¹í. Vycházím z jednoho èlánku o SDL, který napsal Franti¹ek Jahoda a který jsem kdysi stáhl z <?OdkazBlank('http://www.builder.cz/');?>. Povìt¹inou CTRL+C &amp; CTRL+V :-] V prostøedí Microsoft Visual C++ postupujte takto:</p>

<ul>
<li>Rozbalte SDL do urèitého adresáøe napø. C:\SDL\</li>
<li>Nahrajte SDL.dll do C:\Windows\System nebo ho v¾dy pøidejte do adresáøe projektu</li>
<li>Pøidejte v Tools\Options\Directories\Include files\ cestu k adresáøi INCLUDE v na¹em pøípadì C:\SDL\INCLUDE</li>
<li>Vytvoøte nový prázdný Win32 projekt: tedy File\New\Projects\Win32 Application, zvolte si jméno projektu a lokaci ulo¾ení, pak kliknìte na OK a zvolte An empty project a nakonec zvolte Finish</li>
<li>V Project\Settings\ C\C++ \Code Generation\  navolte Multi-threaded. Toto nastavení proveïte u ka¾dého projektu, jinak vám to pøi kompilaci bude psát chybu !!! (Mì psalo, a proto jsem navolil &quot;Multithreaded DLL&quot;).</li>
<li>Vytvoøte nový soubor main.cpp a pøidejte ho do projektu (zdroják vý¹e)</li>
<li>Pøidejte do projektu SDL.lib a SDLmain.lib. Pokud nechcete pracovat pøes nabídky jako já, vlo¾te následující kód za v¹echna #include.
<p class="src0">#pragma comment (lib,"opengl32.lib")</p>
<p class="src0">#pragma comment (lib,"glu32.lib")</p>
<p class="src0">#pragma comment (lib,"glaux.lib")</p>
<p class="src0">#pragma comment (lib,"sdl.lib")</p>
<p class="src0">#pragma comment (lib,"sdlmain.lib")</p>
</li>
<li>Nakonec nahrajte do adresáøe s programem soubor README-SDL.txt (copyright SDL)</li>
</ul>

<p class="autor">napsal: Bernard Lidický - Berny <?VypisEmail('2berny@seznam.cz');?>, 17.02.2003</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/sdl_okno.rar');?> - Linux, Windows - Visual C++</li>
</ul>

<h3>Errata</h3>
<ul>
<li>15.08.2004 - Michal Turek - Woq: Neúplný pøíkaz pro kompilaci v Linuxu, chybìlo pøilinkování -lGL a -lGLU. Dík za upozornìní.</li>
</ul>

<div class="okolo_img"><a href="images/clanky/cl_sdl_okno_big.png"><img src="images/clanky/cl_sdl_okno.png" width="320" height="240" alt="SDL program pod Mandrake GNU/Linuxem, KDE 3.2 - Plastik" /></a></div>

<?
include 'p_end.php';
?>
