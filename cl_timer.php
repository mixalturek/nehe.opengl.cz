<?
$g_title = 'CZ NeHe OpenGL - Timer';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Timer</h1>

<p class="nadpis_clanku">P�edstavte si, �e d�l�te n�jak� velk� dynamick� sv�t, pro kter� pot�ebujete mnoho v�po�t� z�visl�ch na uplynul�m �ase (pohyb, rotace, animace, fyzika). Pokud synchronizujete klasicky pomoc� FPS, neobejdete se p�i ka�d�m vykreslen� bez spousty d�len�. Z�kladem v�eho je, tyto operace prov�d�t co nejm�n�, abychom zbyte�n� nezat�ovali procesor.</p>

<p>Celou aplikaci zalo��me na my�lence, �e kdy� zajist�me, aby se sc�na v �ase neaktualizovala n�hodn�, ale v�dy po ur�it�m �seku, nebudeme muset pou��vat ��dn� d�len� na z�klad� FPS. V�sledkem na�eho dne�n�ho sna�en� bude SDL okno s ot��ej�c� se krychli�kou, kter� bude na jak�mkoli po��ta�i rotovat v�dy stejn� rychle.</p>

<p>Za�neme klasicky vlo�en�m hlavi�kov�ch soubor� a p�ilinkov�n�m knihoven. Nelekejte se knihovny SDL, m��ete bez probl�m� pou��t i Win32 nebo jak�koli jin� API - z�le�� jen na v�s.</p>

<p class="src0">#include &lt;sdl.h&gt;<span class="kom">// Vlozi SDL</span></p>
<p class="src0">#include &lt;sdl_opengl.h&gt;<span class="kom">// SDL za nas vlozi OpenGL</span></p>
<p class="src"></p>
<p class="src0">#pragma comment (lib, &quot;opengl32.lib&quot;)<span class="kom">// P�ilinkov�n� knihoven</span></p>
<p class="src0">#pragma comment (lib, &quot;glu32.lib&quot;)</p>
<p class="src0">#pragma comment (lib, &quot;sdl.lib&quot;)</p>
<p class="src0">#pragma comment (lib, &quot;sdlmain.lib&quot;)</p>

<p>Definujeme symbolickou konstantu pro �asova�. Ka�d�ch 20 ms, co� odpov�d� 50 FPS, budeme aktualizovat pom�ry ve sc�n�. D�le deklarujeme dv� prom�nn� pot�ebn� pro rotaci krychle. Last_time udr�uje hodnotu �asu minul� aktualizace sc�ny.</p>

<p class="src0"><span class="kom">// Interval timeru v ms, odpovida 50 FPS</span></p>
<p class="src0">#define TIMER_INTERVAL 20</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Promenne</span></p>
<p class="src0">float rotx, roty;<span class="kom">// Rotace na osach x a y</span></p>
<p class="src0">unsigned int last_time = 0;<span class="kom">// Cas minule aktualizace sceny</span></p>

<p>Ub�hlo-li od minul�ho p�ekreslen� v�ce ne� 20 ms, zv���me hodnoty prom�nn�ch. V tomto p��pad� je samostatn� funkce relativn� zbyte�n�, ale a� budete m�t des�tky nebo stovky prom�nn�ch, bude se ur�it� hodit.</p>

<p class="src0">void OnTimer()<span class="kom">// Aktualizace sceny</span></p>
<p class="src0">{</p>
<p class="src1">rotx += 2.0f;<span class="kom">// Zvysi uhly rotace</span></p>
<p class="src1">roty += 2.0f;</p>
<p class="src0">}</p>

<p>Na za��tku renderovac� funkce vykresl�me v�e pot�ebn�, o �asov�n� se zat�m nestar�me. Kdybychom prom�nn� inkrementovali v z�vislosti na FPS p��mo u glRotatef(), v�e by se prov�d�lo i 1000 kr�t za sekundu. To je na jednu stranu zbyte�n�, proto�e oko v�ce ne� 50 FPS nem� �anci post�ehnout. Na druhou stranu, kdybyste m�li ve sc�n� p��li� mnoho objekt� nap�. gigantick� ��sticov� syst�m a m�li u ka�d� ��stice d�lit pozici, rotaci, �ivotnost a dal�� parametry pomoc� FPS, v�e by se, m�sto omezen� rychlosti na po�adovanou hodnotu, nadm�rn�m d�len�m tot�ln� zpomalilo. Ch�pete tu ironii? Prost�edek, kter� m� rychlost regulovat, je p���inou obrovsk�ch n�rok� na procesor a ve v�sledku aplikaci sp�e zpomaluje. A� budete pracovat na n�jak�m v�t��m projektu, berte v �vahu, �e ka�d� faktor ovliv�uj�c� rychlost enginu je velice d�le�it�.</p>

<p class="src0">bool DrawGLScene()</p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);</p>
<p class="src1">glLoadIdentity();</p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -4.0f);</p>
<p class="src"></p>
<p class="src1">glRotatef(rotx, 1.0f, 0.0f, 0.0f);</p>
<p class="src1">glRotatef(roty, 0.0f, 1.0f, 0.0f);</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// Rendering nap�. krychle</span></p>
<p class="src1">glEnd();</p>

<p>V na�� verzi �asova�e otestujeme, zda od minul� aktualizace ub�hlo 20 ms. Pokud ne, m��e se sc�na p�ekreslit znovu. Prom�nn� neinkrementujeme a sc�na tud� z�st�v� beze zm�n. Pokud u� ale ub�hlo pot�ebn�ch 20 ms, zavol�me funkci OnTimer(), kter� aktualizuje prom�nn�. Za sekundu by se v�e m�lo prov�st p�ibli�n� 50 kr�t (50 FPS). Co se ale stane, kdy� m�sto po nap�. 5 ms se na toto m�sto program dostane a� za 100 ms? Cyklus zajist�, �e se prom�nn� aktualizuj� p�tkr�t m�sto pouze jednou, jak by to bylo u oby�ejn�ho v�tven� s testem na 20 ms.</p>

<p class="src1"><span class="kom">// O�et�en� timeru</span></p>
<p class="src1">unsigned int actual_time = SDL_GetTicks();<span class="kom">// Grabovani casu (ve WinAPI GetTickCount())</span></p>
<p class="src"></p>
<p class="src1">while (SDL_GetTicks() &gt; last_time + TIMER_INTERVAL)<span class="kom">// Aktualizovat, dokud scena neni v &quot;soucasnem&quot; case</span></p>
<p class="src1">{</p>
<p class="src2">OnTimer();<span class="kom">// Aktualizace sceny</span></p>
<p class="src2">last_time += TIMER_INTERVAL;<span class="kom">// Pricteni 20 ms</span></p>

<p>Kdybychom m�li extr�mn� pomal� po��ta� a/nebo hodn� n�ro�nou funkci OnTimer(), mohl by v n� program z�stat d�le ne� 20 ms. Uv�zli bychom v cyklu.</p>

<p class="src2">if(SDL_GetTicks() &gt; actual_time + 1000)<span class="kom">// Trva cely cyklus dele nez 1 sekundu?</span></p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Cyklus trva prilis dlouho! Slaby pocitac!\n&quot;);</p>
<p class="src3">fprintf(stderr, &quot;Program ukoncen...&quot;);</p>
<p class="src"></p>
<p class="src3">return false;<span class="kom">// Konec funkce a programu</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1"></p>
<p class="src1">return true;</p>
<p class="src0">}</p>
<p class="src"></p>
<p>A nakonec b�n� inicializace SDL a OpenGL, kterou u� nebudu popisovat.</p>
<p class="src"></p>
<p class="src0">int main(int argc, char *argv[])<span class="kom">// Proste main()</span></p>
<p class="src0">{</p>
<p class="src1">bool quit = false;<span class="kom">// Flag ukonceni programu</span></p>
<p class="src1">SDL_Event event;<span class="kom">// Udalost</span></p>
<p class="src"></p>
<p class="src1">if (SDL_Init(SDL_INIT_VIDEO | SDL_INIT_TIMER) &lt; 0)<span class="kom">// Inicializace SDL</span></p>
<p class="src1">{</p>
<p class="src2">SDL_Quit();</p>
<p class="src2">return 0;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!SDL_SetVideoMode(640, 480, 16, SDL_OPENGL))<span class="kom">// Spusteni OpenGL a zmena rozliseni</span></p>
<p class="src1">{</p>
<p class="src2">SDL_Quit();</p>
<p class="src2">return 0;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SDL_WM_SetCaption(&quot;Timer Example by Eosie&quot;, NULL);<span class="kom">// Titulek okna</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Inicializace OpenGL</span></p>
<p class="src1">glViewport(0, 0, 640, 480);<span class="kom">// Resetuje aktualni nastaveni</span></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvoli projekcni matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">gluPerspective(45.0f, 640.f/480.0f, 1.0f, 100.0f);<span class="kom">// Vypo�et perspektivy</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvoli matici Modelview</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// Cerne pozadi</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Mazani hloubky</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom">// Nastaveni hloubky</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povoleni testu hloubky</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemne stinovani</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivni korekce</span></p>
<p class="src"></p>
<p class="src1">while (!quit)<span class="kom">// Smycka na kresleni a hlidani udalosti</span></p>
<p class="src1">{</p>
<p class="src2">while (SDL_PollEvent(&amp;event))<span class="kom">// Hlidani udalosti</span></p>
<p class="src2">{</p>
<p class="src3">switch (event.type)<span class="kom">// Vetvi podle dosle zpravy</span></p>
<p class="src3">{</p>
<p class="src4">case SDL_QUIT:<span class="kom">// Konec</span></p>
<p class="src4">quit = true;</p>
<p class="src4">break;</p>
<p class="src"></p>
<p class="src4">case SDL_KEYDOWN:<span class="kom">// Klavesa</span></p>
<p class="src4">if (event.key.keysym.sym == SDLK_ESCAPE)<span class="kom">// ESC</span></p>
<p class="src4">{</p>
<p class="src5">quit = true;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">break;</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Po zpracov�n� zpr�v, p�ekresl�me sc�nu.</p>

<p class="src2">if (!DrawGLScene())<span class="kom">// Vykresleni sceny</span></p>
<p class="src2">{</p>
<p class="src3">quit = true;<span class="kom">// Konec programu</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">SDL_GL_SwapBuffers();<span class="kom">// Prohozeni bufferu</span></p>
<p class="src2">glFlush();</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SDL_Quit();<span class="kom">// Ukonceni SDL</span></p>
<p class="src1">return 0;<span class="kom">// Konec programu</span></p>
<p class="src0">}</p>

<p>T�mto �l�nkem neodepisuji �asov�n� p�es FPS, to ne. V�dy je dobr� vid�t v�echny mo�nosti a um�t se (!p�ed za��tkem pr�ce!) rozhodnout, kter� z nich je pro toto ur�it� zad�n� vhodn�j�� a kterou proto pou��t. Mus�m v�ak p�iznat, �e tento timer m� proti �asov�n� pomoc� FPS mnoho v�hod, a proto jej ve sv�ch programech up�ednost�uji.</p>

<p class="autor">napsal: Marek Ol��k - Eosie <?VypisEmail('eosie@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/timer.tar.gz');?> - Visual C++</li>
</ul>

<?
include 'p_end.php';
?>
