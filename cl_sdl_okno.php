<?
$g_title = 'CZ NeHe OpenGL - Vytvo�en� SDL okna';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Vytvo�en� SDL okna</h1>

<p class="nadpis_clanku">Woq m� po��dal, abych napsal tutori�l pro pou��v�n� OpenGL pod knihovnou SDL. Je to m�j prvn� tutori�l, tak�e douf�m, �e se bude l�bit. Zkus�me vytvo�it k�d, kter� bude odpov�dat druh� lekci &quot;norm�ln�ch&quot; tutori�l�. Zdrojov� k�d je &quot;ofici�ln�&quot; NeHe port druh� lekce do SDL. Pokus�m se popsat, jak se vytv��� okno a v�ci okolo.</p>

<p>Na �vod bych cht�l pod�kovat Bernymu za sebe i za v�echny, kter�m tento �l�nek velice pomohl, a kte�� si mysl�, �e d�lat programy v�hradn� pro komer�n� Wokna nen� tou spr�vnou cestou.</p>

<p>Nejd��ve n�co o tom, co je SDL. SDL, univerz�ln� knihovna, slou�� pro tvorbu her. Zn�te-li Allegro, tak SDL je mu trochu podobn�. Bylo naps�no v C(/C++) a jeho domovsk� prost�ed� bylo Linux. Nyn� ale existuje i pod BeOS, MacOS, Windows, QNX, AmigaOS, PS2,.... Obsahuje z�kladn� API pro vytvo�en� okna, 2D grafiku, v�cevl�knov� programov�n�, obsluhu kl�vesnice, krysy a joysticku, p�ehr�v�n� videa a zvuk�. Ke knihovn� jsou je�t� podp�rn� knihovni�ky pro na��t�ni rozmanit�ch grafick�ch form�t�, pro lep�� ozvu�en� (mp3, ogg,..) a s�ov� slu�by pro multiplayer. P��mo podporuje vyu�it� OpenGL, kter� samo o sob� manipulaci s okny, komunikaci se syst�mem a v�echnu pr�ci okolo neum� a tud� ned�l�. K�d kter� vytvo��te v SDL m��ete velice snadno p�en�st jinam jednoduch�m p�ekompilov�n�m (bude to fungovat, pokud nep�ete jako �u�ata). SDL nen� jen n�jak� mal� knihovni�ka, ale pou��vaj� ji i komer�n� hry. V�t�ina her od Lokisoft (portuj� hry pod Linux) je nad SDL a Unreal Tournament 2003 tak�. Nejlep�� bude, pokud nav�t�v�te domovsk� web - <?OdkazBlank('http://www.libsdl.org/');?>. Jsou tam v�emo�n� helpy, n�vody, manu�ly a tak� vystaveny pr�ce lid�, kte�� SDL pou��vaj�; p�r d�lek i z �R.</p>

<p>Ne� za�neme, ujist�te se, �e m�te knihovnu SDL. Pokud ne, nav�tivte v��e uvedenou adresu a st�hn�te si soubory pro va�i konkr�tn� platformu.</p>

<p>A te� u� se vrhneme na programov�n�. Nejd��ve vlo��me hlavi�kov� soubory. Mo�n� se to zd� na prvn� pohled jako ��lenost, ale vy�e��me p�en�en� k�du na r�zn� OS. Proto doporu�uji toto vkl�d�n� nechat tak, jak je.</p>

<p class="src0">#ifdef WIN32<span class="kom"> // Pokud se bude kompilovat program po Windows</span></p>
<p class="src1">#define WIN32_LEAN_AND_MEAN</p>
<p class="src1">#include &lt;windows.h&gt;<span class="kom"> // Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#endif</p>
<p class="src"></p>
<p class="src0">#if defined(__APPLE__) &amp;&amp; defined(__MACH__)<span class="kom"> // Pokud se bude kompilovat program pro Apple</span></p>
<p class="src1">#include &lt;OpenGL/gl.h&gt;<span class="kom"> // Hlavi�kov� soubor pro OpenGL32 knihovnu v Applu</span></p>
<p class="src1">#include &lt;OpenGL/glu.h&gt;<span class="kom"> // Hlavi�kov� soubor pro GLu32 knihovnu v Applu</span></p>
<p class="src0">#else</p>
<p class="src1">#include &lt;GL/gl.h&gt;<span class="kom"> // Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src1">#include &lt;GL/glu.h&gt;<span class="kom"> // Hlavi�kov� soubor pro GLu32 knihovnu</span></p>
<p class="src0">#endif</p>
<p class="src"></p>
<p class="src0">#include "SDL.h"<span class="kom"> // Hlavi�kov� soubor pro SDL</span></p>

<p>Vytvo��me funkci na inicializaci OpenGL (sm�chan� InitGL() a ResizeGLScene() z klasick�ch tutori�l�). Na��m c�lem nen� rozeb�rat OpenGL, ale sp�e vysv�tlit SDL. Pro detaily se pod�vejte do druh� lekce.</p>

<p class="src0">void InitGL(int Width, int Height)<span class="kom"> // Tuto funkci vol�me hned po vytvo�en� okna pro inicializace OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glViewport(0, 0, Width, Height);</p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom"> // �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom"> // Povol�me maz�n� pozad�</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom"> // Vybereme typ  Depth Testu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom"> // Povol�me Depth Test</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom"> // Povol�me Smooth Color Shading</span></p>
<p class="src1">glMatrixMode(GL_PROJECTION);</p>
<p class="src1">glLoadIdentity();<span class="kom"> // Resetujeme projek�n� matici</span></p>
<p class="src1">gluPerspective(45.0f,(GLfloat)Width/(GLfloat)Height,0.1f,100.0f);<span class="kom"> // Vypo��t�me pom�r okna</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);</p>
<p class="src0">}</p>

<p>Vykreslov�n�...</p>

<p class="src0">void DrawGLScene()<span class="kom"> // Hlavn� vykreslovac� funkce</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom"> // Vymaz�n� obrazovkov�ho a hloubkov�ho bufferu</span></p>
<p class="src1">glLoadIdentity();<span class="kom"> // Reset matice pohledu</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom"> // Posun o 1.5 doleva a o 6 do hloubky</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nakresl� troj�heln�k</span></p>
<p class="src1">glBegin(GL_POLYGON);</p>
<p class="src2">glVertex3f(0.0f, 1.0f, 0.0f);</p>
<p class="src2">glVertex3f(1.0f,-1.0f, 0.0f);</p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glTranslatef(3.0f,0.0f,0.0f);<span class="kom"> // Posuneme se o 3 doprava</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nakresl� �tverec</span></p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);</p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);</p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);</p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);</p>
<p class="src1">glEnd();</p>

<p>Na konci provedeme drobnou zm�nu. Zavol�me funkci SDL_GL_SwapBuffers(), kter� zajist�, �e se na obrazovce objev� v�e, co jsme ve funkci DrawGLScene() nakreslili. V klasick�ch tutori�lech najdete SwapBuffers() ve WinMain() po vol�n� t�to funkce.</p>

<p class="src1">SDL_GL_SwapBuffers();<span class="kom"> // Prohozeni bufferu, aby se zobrazilo, co jsme nakreslili</span></p>
<p class="src0">}</p>

<p>�e se funkce main() prov�d� po spu�t�n� programu jako prvn� douf�m v�te :-] Le�� zde v�t�ina SDL k�du. Nejprve provedeme inicializaci a pak cyklujeme dokola v jednoduch� smy�ce zpr�v. D�vejte si pozor, aby deklarace main() byla p�esn� takov�, jak m� b�t - nikdy nevynech�vat argumenty a n�vratovou hodnotu.</p>

<p class="src0">int main(int argc, char **argv)<span class="kom"> // Prost� main() :-)</span></p>
<p class="src0">{</p>
<p class="src1">int done;<span class="kom"> // Ukon�ovac� prom�nn�</span></p>

<p>Inicializace knihovny se mus� zavolat jako prvn� p�ed v��m ostatn�m. SDL_INIT_VIDEO znamen�, �e budeme cht�t inicializovat v�stup na obrazovku. M��e se kombinovat i s dal��mi jako t�eba timerem, audiem,... Knihovna SDL n�m umo��uje pou��vat stdout a stderr pro textov� v�stup. V Linuxu vypisuje na termin�l, ve windows vytvo�� soubory stdout.txt a stderr.txt (v p��pad�, �e do nich nic nezap�ete je pak op�t sma�e).</p>

<p class="src1">if ( SDL_Init(SDL_INIT_VIDEO) &lt; 0 )<span class="kom"> // Inicializace SDL s grafick�m v�stupem</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, "Selhala inicializace SDL: %s\n", SDL_GetError());</p>
<p class="src2">exit(1);</p>
<p class="src1">}</p>

<p>Te� m��eme vytvo�it okno aplikace. M�me k tomu funkci SDL_SetVideoMode. Prvn� dva parametry jsou rozm�ry okna, dal�� parametr ur�uje barevnou hloubku (0 zachov�v� sou�asnou). Posledn� parametr je kombinac� r�zn�ch flag�. Pokud chceme po��vat OpenGL, mus�me zadat SDL_OPENGL (nebo SDL_OPENGLBLIT). Pokud chcete fullscreen, m��ete p�idat flag SDL_FULLSCREEN. V X11 (Linuxu) m��ete p�ep�nat mezi fullscreenem a oknem svobodn� kdykoliv se v�m zachce vol�n�m funkce SDL_WM_ToggleFullScreen(), ale ve Windows si v SDL_SetVideoMode() jednou vyberete a to v�m pak z�stane. Proto�e jsme inicializovali SDL, mus�me ho v p��pad� erroru tak� ukon�it. M� to na starosti funkce SDL_Quit().</p>

<p class="src1">if (SDL_SetVideoMode(640, 480, 0, SDL_OPENGL) == NULL)<span class="kom"> // Vytvo�en� OpenGL okna 640x480</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, "Neni mozne vytvorit OpenGL okno: %s\n", SDL_GetError());</p>
<p class="src2">SDL_Quit();<span class="kom"> // Ukon�en� SDL</span></p>
<p class="src2">exit(2);</p>
<p class="src1">}</p>

<p>Nastav�me titulek okna. M��eme p�idat i ikonku, ale proto�e ��dnou nem�me, pou�ijeme NULL.</p>

<p class="src1">SDL_WM_SetCaption("Jeff Molofee's GL Code Tutorial ... NeHe '99", NULL);<span class="kom"> // Titulek okna</span></p>
<p class="src1">InitGL(640, 480);<span class="kom"> // Inicializace OpenGL</span></p>

<p>Hlavn� cyklus programu. Pro opu�t�n� programu pou�ijeme prom�nnou done (deklarovan� na za��tku main(), kterou testujeme v ka�d�m pr�b�hu hlavn�m cyklem.</p>

<p class="src1">done = 0;<span class="kom"> // Je�t� nekon�it</span></p>
<p class="src1">while (!done)<span class="kom"> // Hlavn� cyklus programu</span></p>
<p class="src1">{</p>
<p class="src2">DrawGLScene();<span class="kom"> // Vykreslen� sc�ny</span></p>

<p>Obsluha ud�lost� by m�la p�ij�t do zvl�tn� funkce (analogie WndProc() z Windows), ale nebudeme jednoduch� k�d komplikovat. Nejprve vytvo��me prom�nou typu SDL_Event. Pak pro na�ten� zpr�v vol�me v cyklu SDL_PollEvent(). Dokud p�ich�zej� ud�losti, tak se jimi zab�v�me. Tentokr�t zareagujeme pouze na po�adavek ukon�en� pomoc� kl�vesy ESC. P�esuny okna, zm�na jeho velikosti ap. se d�j� automaticky.</p>

<p class="src2">SDL_Event event;<span class="kom"> // Prom�nn� zpr�vy</span></p>
<p class="src"></p>
<p class="src2">while (SDL_PollEvent(&amp;event))<span class="kom"> // Zpracov�vat zpr�vy</span></p>
<p class="src2">{</p>
<p class="src3">if (event.type == SDL_QUIT)<span class="kom"> // Zpr�va o ukon�en�</span></p>
<p class="src3">{</p>
<p class="src4">done = 1;<span class="kom"> // Ukon�it hlavn� cyklus</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (event.type == SDL_KEYDOWN)<span class="kom"> // Zpr�va o stisku kl�vesy</span></p>
<p class="src3">{</p>
<p class="src4">if (event.key.keysym.sym == SDLK_ESCAPE)<span class="kom"> // Kl�vesa ESC</span></p>
<p class="src4">{</p>
<p class="src5">done = 1;<span class="kom"> // Ukon�it hlavn� cyklus</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Na konci u� jen ukon��me SDL.</p>

<p class="src1">SDL_Quit();<span class="kom"> // Ukon�en� SDL</span></p>
<p class="src1">return 0;</p>
<p class="src0">}</p>

<p>Pro kompilaci tutori�lu v Linuxu m��eme pou��t nap�. p��kaz n�e. Spust�me standardn� kompil�tor gcc a zapneme v�echna varovn� hl�en�. Chceme zkompilovat soubor lesson02.c a p�ilinkovat knihovny pro SDL (v�imn�te si zp�tn�ch apostrof�!!! - na kl�vesnici pod Esc), OpenGL a GLU. V�sledn� spustiteln� soubor se vytvo�� ve stejn�m adres��i, ve kter�m se pr�v� nach�z�me, a bude m�t n�zev lesson02. Tento nov� program se m��e spustit bu� p�es libovoln� souborov� mana�er nebo - kdy� u� jsme ve shellu - p��kazem ./lesson02. Pokud d�me na konec amp�rsand, spust� se na pozad� a nebudeme m�t blokovanou p��kazovou ��dku. Aby �el program spustit, mus� na po��ta�i b�et XFree server (XFree86, X, X11, XWindow - prost� grafick� re�im :-).</p>

<p class="src0">[woq@komputerovka c]$ gcc -Wall lesson02.c `sdl-config --libs --cflags` -lGL -lGLU  -o lesson02</p>
<p class="src0">[woq@komputerovka c]$ ./lesson02 &amp;</p>

<p>Ve Windows a Visual C++ je zprovozn�n� mali�ko komplikovan�j��. Vych�z�m z jednoho �l�nku o SDL, kter� napsal Franti�ek Jahoda a kter� jsem kdysi st�hl z <?OdkazBlank('http://www.builder.cz/');?>. Pov�t�inou CTRL+C &amp; CTRL+V :-] V prost�ed� Microsoft Visual C++ postupujte takto:</p>

<ul>
<li>Rozbalte SDL do ur�it�ho adres��e nap�. C:\SDL\</li>
<li>Nahrajte SDL.dll do C:\Windows\System nebo ho v�dy p�idejte do adres��e projektu</li>
<li>P�idejte v Tools\Options\Directories\Include files\ cestu k adres��i INCLUDE v na�em p��pad� C:\SDL\INCLUDE</li>
<li>Vytvo�te nov� pr�zdn� Win32 projekt: tedy File\New\Projects\Win32 Application, zvolte si jm�no projektu a lokaci ulo�en�, pak klikn�te na OK a zvolte An empty project a nakonec zvolte Finish</li>
<li>V Project\Settings\ C\C++ \Code Generation\  navolte Multi-threaded. Toto nastaven� prove�te u ka�d�ho projektu, jinak v�m to p�i kompilaci bude ps�t chybu !!! (M� psalo, a proto jsem navolil &quot;Multithreaded DLL&quot;).</li>
<li>Vytvo�te nov� soubor main.cpp a p�idejte ho do projektu (zdroj�k v��e)</li>
<li>P�idejte do projektu SDL.lib a SDLmain.lib. Pokud nechcete pracovat p�es nab�dky jako j�, vlo�te n�sleduj�c� k�d za v�echna #include.
<p class="src0">#pragma comment (lib,"opengl32.lib")</p>
<p class="src0">#pragma comment (lib,"glu32.lib")</p>
<p class="src0">#pragma comment (lib,"glaux.lib")</p>
<p class="src0">#pragma comment (lib,"sdl.lib")</p>
<p class="src0">#pragma comment (lib,"sdlmain.lib")</p>
</li>
<li>Nakonec nahrajte do adres��e s programem soubor README-SDL.txt (copyright SDL)</li>
</ul>

<p class="autor">napsal: Bernard Lidick� - Berny <?VypisEmail('2berny@seznam.cz');?>, 17.02.2003</p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/sdl_okno.rar');?> - Linux, Windows - Visual C++</li>
</ul>

<h3>Errata</h3>
<ul>
<li>15.08.2004 - Michal Turek - Woq: Ne�pln� p��kaz pro kompilaci v Linuxu, chyb�lo p�ilinkov�n� -lGL a -lGLU. D�k za upozorn�n�.</li>
</ul>

<div class="okolo_img"><a href="images/clanky/cl_sdl_okno_big.png"><img src="images/clanky/cl_sdl_okno.png" width="320" height="240" alt="SDL program pod Mandrake GNU/Linuxem, KDE 3.2 - Plastik" /></a></div>

<?
include 'p_end.php';
?>
