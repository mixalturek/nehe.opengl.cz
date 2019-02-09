<?
$g_title = 'CZ NeHe OpenGL - Timer';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Timer</h1>

<p class="nadpis_clanku">Pøedstavte si, ¾e dìláte nìjaký velký dynamický svìt, pro který potøebujete mnoho výpoètù závislých na uplynulém èase (pohyb, rotace, animace, fyzika). Pokud synchronizujete klasicky pomocí FPS, neobejdete se pøi ka¾dém vykreslení bez spousty dìlení. Základem v¹eho je, tyto operace provádìt co nejménì, abychom zbyteènì nezatì¾ovali procesor.</p>

<p>Celou aplikaci zalo¾íme na my¹lence, ¾e kdy¾ zajistíme, aby se scéna v èase neaktualizovala náhodnì, ale v¾dy po urèitém úseku, nebudeme muset pou¾ívat ¾ádné dìlení na základì FPS. Výsledkem na¹eho dne¹ního sna¾ení bude SDL okno s otáèející se krychlièkou, která bude na jakémkoli poèítaèi rotovat v¾dy stejnì rychle.</p>

<p>Zaèneme klasicky vlo¾ením hlavièkových souborù a pøilinkováním knihoven. Nelekejte se knihovny SDL, mù¾ete bez problémù pou¾ít i Win32 nebo jakékoli jiné API - zále¾í jen na vás.</p>

<p class="src0">#include &lt;sdl.h&gt;<span class="kom">// Vlozi SDL</span></p>
<p class="src0">#include &lt;sdl_opengl.h&gt;<span class="kom">// SDL za nas vlozi OpenGL</span></p>
<p class="src"></p>
<p class="src0">#pragma comment (lib, &quot;opengl32.lib&quot;)<span class="kom">// Pøilinkování knihoven</span></p>
<p class="src0">#pragma comment (lib, &quot;glu32.lib&quot;)</p>
<p class="src0">#pragma comment (lib, &quot;sdl.lib&quot;)</p>
<p class="src0">#pragma comment (lib, &quot;sdlmain.lib&quot;)</p>

<p>Definujeme symbolickou konstantu pro èasovaè. Ka¾dých 20 ms, co¾ odpovídá 50 FPS, budeme aktualizovat pomìry ve scénì. Dále deklarujeme dvì promìnné potøebné pro rotaci krychle. Last_time udr¾uje hodnotu èasu minulé aktualizace scény.</p>

<p class="src0"><span class="kom">// Interval timeru v ms, odpovida 50 FPS</span></p>
<p class="src0">#define TIMER_INTERVAL 20</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Promenne</span></p>
<p class="src0">float rotx, roty;<span class="kom">// Rotace na osach x a y</span></p>
<p class="src0">unsigned int last_time = 0;<span class="kom">// Cas minule aktualizace sceny</span></p>

<p>Ubìhlo-li od minulého pøekreslení více ne¾ 20 ms, zvý¹íme hodnoty promìnných. V tomto pøípadì je samostatná funkce relativnì zbyteèná, ale a¾ budete mít desítky nebo stovky promìnných, bude se urèitì hodit.</p>

<p class="src0">void OnTimer()<span class="kom">// Aktualizace sceny</span></p>
<p class="src0">{</p>
<p class="src1">rotx += 2.0f;<span class="kom">// Zvysi uhly rotace</span></p>
<p class="src1">roty += 2.0f;</p>
<p class="src0">}</p>

<p>Na zaèátku renderovací funkce vykreslíme v¹e potøebné, o èasování se zatím nestaráme. Kdybychom promìnné inkrementovali v závislosti na FPS pøímo u glRotatef(), v¹e by se provádìlo i 1000 krát za sekundu. To je na jednu stranu zbyteèné, proto¾e oko více ne¾ 50 FPS nemá ¹anci postøehnout. Na druhou stranu, kdybyste mìli ve scénì pøíli¹ mnoho objektù napø. gigantický èásticový systém a mìli u ka¾dé èástice dìlit pozici, rotaci, ¾ivotnost a dal¹í parametry pomocí FPS, v¹e by se, místo omezení rychlosti na po¾adovanou hodnotu, nadmìrným dìlením totálnì zpomalilo. Chápete tu ironii? Prostøedek, který má rychlost regulovat, je pøíèinou obrovských nárokù na procesor a ve výsledku aplikaci spí¹e zpomaluje. A¾ budete pracovat na nìjakém vìt¹ím projektu, berte v úvahu, ¾e ka¾dý faktor ovlivòující rychlost enginu je velice dùle¾itý.</p>

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
<p class="src2"><span class="kom">// Rendering napø. krychle</span></p>
<p class="src1">glEnd();</p>

<p>V na¹í verzi èasovaèe otestujeme, zda od minulé aktualizace ubìhlo 20 ms. Pokud ne, mù¾e se scéna pøekreslit znovu. Promìnné neinkrementujeme a scéna tudí¾ zùstává beze zmìn. Pokud u¾ ale ubìhlo potøebných 20 ms, zavoláme funkci OnTimer(), která aktualizuje promìnné. Za sekundu by se v¹e mìlo provést pøibli¾nì 50 krát (50 FPS). Co se ale stane, kdy¾ místo po napø. 5 ms se na toto místo program dostane a¾ za 100 ms? Cyklus zajistí, ¾e se promìnné aktualizují pìtkrát místo pouze jednou, jak by to bylo u obyèejného vìtvení s testem na 20 ms.</p>

<p class="src1"><span class="kom">// O¹etøení timeru</span></p>
<p class="src1">unsigned int actual_time = SDL_GetTicks();<span class="kom">// Grabovani casu (ve WinAPI GetTickCount())</span></p>
<p class="src"></p>
<p class="src1">while (SDL_GetTicks() &gt; last_time + TIMER_INTERVAL)<span class="kom">// Aktualizovat, dokud scena neni v &quot;soucasnem&quot; case</span></p>
<p class="src1">{</p>
<p class="src2">OnTimer();<span class="kom">// Aktualizace sceny</span></p>
<p class="src2">last_time += TIMER_INTERVAL;<span class="kom">// Pricteni 20 ms</span></p>

<p>Kdybychom mìli extrémnì pomalý poèítaè a/nebo hodnì nároènou funkci OnTimer(), mohl by v ní program zùstat déle ne¾ 20 ms. Uvízli bychom v cyklu.</p>

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
<p>A nakonec bì¾ná inicializace SDL a OpenGL, kterou u¾ nebudu popisovat.</p>
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
<p class="src1">gluPerspective(45.0f, 640.f/480.0f, 1.0f, 100.0f);<span class="kom">// Vypoèet perspektivy</span></p>
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

<p>Po zpracování zpráv, pøekreslíme scénu.</p>

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

<p>Tímto èlánkem neodepisuji èasování pøes FPS, to ne. V¾dy je dobré vidìt v¹echny mo¾nosti a umìt se (!pøed zaèátkem práce!) rozhodnout, která z nich je pro toto urèité zadání vhodnìj¹í a kterou proto pou¾ít. Musím v¹ak pøiznat, ¾e tento timer má proti èasování pomocí FPS mnoho výhod, a proto jej ve svých programech upøednostòuji.</p>

<p class="autor">napsal: Marek Ol¹ák - Eosie <?VypisEmail('eosie@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/timer.tar.gz');?> - Visual C++</li>
</ul>

<?
include 'p_end.php';
?>
