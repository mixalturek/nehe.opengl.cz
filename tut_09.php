<?
$g_title = 'CZ NeHe OpenGL - Lekce 9 - Pohyb bitmap ve 3D prostoru';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(9);?>

<h1>Lekce 9 - Pohyb bitmap ve 3D prostoru</h1>

<p class="nadpis_clanku">Tento tutoriál vás nauèí pohyb objektù ve 3D prostoru a kreslení bitmap bez èerných míst, zakrývajících objekty za nimi. Jednoduchou animaci a roz¹íøené pou¾ití blendingu. Teï byste u¾ mìli rozumìt OpenGL velmi dobøe. Nauèili jste se v¹e od nastavení OpenGL okna, po mapování textur za pou¾ití svìtel a blendingu. To byl první tutoriál pro støednì pokroèilé. A pokraèujeme dále...</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavièkový soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavièkový soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Privátní GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trvalý Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na¹eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>Twinkle urèuje, zda se pou¾ívá tøpytivý efekt a tp indikuje stisk klávesy T.</p>

<p class="src0">bool twinkle;<span class="kom">// Tøpytivý efekt</span></p>
<p class="src0">bool tp;<span class="kom">// Stisknuto T?</span></p>

<p>Num urèuje kolik hvìzd bude zobrazeno na obrazovce. Je definováno jako konstanta, tak¾e ho mù¾ete mìnit libovolnì, ale jen v tomto øádku. Nezkou¹ejte mìnit hodnotu num pozdìji v kódu, pokud nechcete pøivodit katastrofu.</p>

<p class="src0">const num=50;<span class="kom">// Poèet zobrazovaných hvìzd</span></p>

<p>Deklarujeme strukturu, v ní¾ budeme uchovávat informace o jednotlivých hvìzdách.</p>

<p class="src0">typedef struct<span class="kom">// Struktura hvìzdy</span></p>
<p class="src0">{</p>
<p class="src1">int r, g, b;<span class="kom">// Barva</span></p>
<p class="src1">GLfloat dist,<span class="kom">// Vzdálenost od støedu</span></p>
<p class="src1">angle;<span class="kom">// Úhel natoèení</span></p>
<p class="src0">} stars;<span class="kom">// Jméno struktury je stars</span></p>

<p>Ka¾dá polo¾ka v poli star obsahuje objekt struktury stars, tj. pìt hodnot popisujících hvìzdu.</p>

<p class="src0">stars star[num];<span class="kom">// Pole hvìzd o velikosti num</span></p>

<p>Dále vytvoøíme promìnné pro nastavení vzdálenosti pozorovatele (zoom) a úhlu pozorování (tilt). Deklarujeme promìnnou spin natáèející hvìzdy okolo osy z, co¾ bude vypadat jako by se otáèely okolo své souèasné pozice. Loop je øídící promìnná cyklu, který pou¾ijeme pro nakreslení v¹ech padesáti hvìzd. Texture[1] ukládá jednu èernobílou texturu.</p>

<p class="src0">GLfloat zoom=-15.0f;<span class="kom">// Hloubka v obrazovce</span></p>
<p class="src0">GLfloat tilt=90.0f;<span class="kom">// Úhel pohledu</span></p>
<p class="src0">GLfloat spin;<span class="kom">// Natoèení hvìzd</span></p>
<p class="src0"></p>
<p class="src0">GLuint loop;<span class="kom">// Øídící promìnná cyklu</span></p>
<p class="src0">GLuint texture[1];<span class="kom">// Ukládá texturu</span></p>

<p>Hned po pøedcházejícím kódu pøidáme kód pro nahrání textury. Nebudu jej znovu opisovat. Je to ten samý jako v lekci 6, 7 a 8. Bitmapa, kterou tentokrát nahrajeme je nazvána star.bmp. Textura bude pou¾ívat lineární filtrování.</p>

<p class="src1">if(TextureImage[0]=LoadBMP("Data/Tim.bmp"))<span class="kom">// Loading bitmapy</span></p>

<p>V tomto projektu nebudeme pou¾ívat hloubkové testování, tak¾e pokud pou¾íváte kód z lekce 1, ujistìte se, ¾e jste odstranili volání glDepthFunc(GL_LEQUAL); a glEnable(GL_DEPTH_TEST); jinak získáte velmi ¹patné výsledky. Nicménì v tomto kódu pou¾íváme mapování textur, tak¾e se ujistìte, ¾e jste pøidali øádky, které nejsou v lekci 1. V¹imnìte si ¾e povolujeme mapování textur a blending.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echna nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje textury</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturové mapování</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povolí jemné stínování</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE);<span class="kom">// Typ blendingu pro prùhlednost</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>

<p>Následující kód je nový. Nastaví poèáteèní úhel, vzdálenost a barvu ka¾dé hvìzdy. V¹imnìte si jak je jednoduché zmìnit hodnoty ve struktuøe. Smyèka projde v¹ech 50 hvìzd.</p>

<p class="src1">for (loop=0; loop&lt;num; loop++)<span class="kom">// Inicializuje hvìzdy</span></p>
<p class="src1">{</p>
<p class="src2">star[loop].angle=0.0f;<span class="kom">// V¹echny mají na zaèátku nulový úhel</span></p>

<p>Poèítám vzdálenost pomocí aktuální hvìzdy (hodnoty promìnné loop), kterou dìlím maximálním poètem hvìzd. Poté násobím výsledek pìti. V podstatì to posune ka¾dou hvìzdu o trochu dále ne¾ tu pøedcházející. Kdy¾ je loop 50 (poslední hvìzda), loop dìleno num je 1.0f. Pøíèina proè násobím pìti je, ¾e 1*5= 5 a to je okraj obrazovky. Nechci aby hvìzdy nebyly zobrazené tak¾e 5.0f je perfektní. Pokud nastavíte hodnotu promìnné zoom hloubìji do obrazovky, mù¾ete pou¾ít hodnotu vìt¹í ne¾ 5.0f, ale hvìzdy budou men¹í (z dùvodu perspektivy). V¹imnìte si, ¾e barva ka¾dé hvìzdy je tvoøena pomocí náhodných hodnot od 0 do 255. Mù¾ete se divit jak mù¾eme pou¾ít tak velké hodnoty, kdy¾ normálnì jsou hodnoty barev od 0.0f do 1.0f. Kdy¾ nastavujeme barvu, pou¾ijeme funkci glColor4ub namísto glColor4f. ub znamená unsigned byte, který mù¾e nabývat hodnot od 0 do 255. V tomto programu je jednodu¹¹í pou¾ít byty ne¾ generovat desetinné hodnoty.</p>

<p class="src2">star[loop].dist=(float(loop)/num)*5.0f;<span class="kom">// Vzdálenost od støedu</span></p>
<p class="src2">star[loop].r=rand()%256;<span class="kom">// Barva</span></p>
<p class="src2">star[loop].g=rand()%256;<span class="kom">// Barva</span></p>
<p class="src2">star[loop].b=rand()%256;<span class="kom">// Barva</span></p>
<p class="src1">}</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Na øadu pøichází vykreslování.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Výbìr textury</span></p>
<p class="src"></p>
<p class="src1">for (loop=0; loop&lt;num; loop++)<span class="kom">// Prochází jednotlivé hvìzdy</span></p>
<p class="src1">{</p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glTranslatef(0.0f,0.0f,zoom);<span class="kom">// Pøesun do obrazovky o zoom</span></p>
<p class="src2">glRotatef(tilt,1.0f,0.0f,0.0f);<span class="kom">// Naklopení pohledu</span></p>

<p>Teï pohneme hvìzdou. První vìc kterou udìláme je pootoèení okolo osy y. Dal¹í øádek kódu posune hvìzdu na ose x. Normálnì to znamená posun na pravou stranu obrazovky, ale proto¾e jsme pootoèili výhled okolo osy y, kladná hodnota osy x mù¾e být kdekoli.</p>

<p class="src2">glRotatef(star[loop].angle,0.0f,1.0f,0.0f);<span class="kom">// Rotace o úhel konkrétní hvìzdy</span></p>
<p class="src2">glTranslatef(star[loop].dist,0.0f,0.0f);<span class="kom">// Pøesun vpøed na ose x</span></p>

<p>Hvìzda je ve skuteènosti plochá textura. Pokud nakreslíte plochý ètyøúhelník a namapujete na nìj texturu, bude to vypadat dobøe. Bude èelem k vám, jak má. Ale kdy¾ scénu pootoèíte o 90 stupòù okolo osy y, textura bude èelem k levé nebo pravé stranì obrazovky a vy uvidíte pouze tenkou linku, co¾ nechceme. Chceme aby hvìzdy byly poøád èelem k nám nezávisle na natoèení a naklopení. Udìláme to zru¹ením v¹ech rotací v opaèném poøadí tìsnì pøedtím ne¾ vykreslíme hvìzdu. Pootoèíme zpìt zadáním invertovaného úhlu pro rotaci a poté zru¹íme naklopení opìt pomocí záporného úhlu. Proto¾e jsme døíve posunuli poèátek, tak je na pozici ve které jsme ji chtìli. Zmìnili jsme její polohu, ale texturu stále vidíme správnì zepøedu. </p>

<p class="src2">glRotatef(-star[loop].angle,0.0f,1.0f,0.0f);<span class="kom">// Zru¹ení pootoèení</span></p>
<p class="src2">glRotatef(-tilt,1.0f,0.0f,0.0f);<span class="kom">// Zru¹ení naklopení</span></p>

<p>Jestli¾e je twinkle TRUE nakreslíme na obrazovku nerotující hvìzdu. Pro získání rozdílných barev vezmeme maximální poèet hvìzd (num) a odeèteme èíslo aktuální hvìzdy (loop), poté odeèteme 1, proto¾e loop nabývá hodnot od 0 do num-1. Tímto zpùsobem získáme hvìzdy rozdílných barev. Není to právì nejlep¹í zpùsob, ale je efektivní. Poslední hodnota je alfa hodnota. Èím je ni¾¹í, tím je hvìzda prùhlednìj¹í. Pokud projde kód podmínkou, bude ka¾dá hvìzda nakreslena dvakrát. To zpomalí program. O kolik závisí na va¹em poèítaèi, ale výsledek bude stát za to - smísí se barvy dvou hvìzd. Proto¾e se nenatáèí, budou vypadat, jako by byly animované. V¹imnìte si jak je jednoduché pøidat barvu do textury. Tøeba¾e je textura èernobílá, dostaneme takovou barvu, jakou zvolíme pøed vykreslením.</p>

<p class="src2">if (twinkle)<span class="kom">// Pokud je zapnutý tøpytivý efekt</span></p>
<p class="src2">{</p>
<p class="src3">glColor4ub(star[(num-loop)-1].r,star[(num-loop)-1].g,star[(num-loop)-1].b,255);</p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f, 0.0f);</p>
<p class="src3">glEnd();</p>
<p class="src2">}</p>

<p>Teï vykreslíme hlavní hvìzdu. Jediný rozdíl od pøedcházejícího kódu je, ¾e tato hvìzda je natoèena okolo osy z a má jinou barvu (viz. indexy).</p>

<p class="src2">glRotatef(spin,0.0f,0.0f,1.0f);</p>
<p class="src2">glColor4ub(star[loop].r,star[loop].g,star[loop].b,255);</p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f, 0.0f);</p>
<p class="src2">glEnd();</p>

<p>Pootoèíme hvìzdu zvìt¹ením hodnoty promìnné spin. Poté zmìníme úhel ka¾dé hvìzdy o loop/num. To znamená, ¾e vzdálenìj¹í hvìzdy se otáèí rychleji. Nakonec sní¾íme vzdálenost hvìzdy od støedu, tak¾e to vypadá, ¾e jsou nasávány doprostøed.</p>

<p class="src2">spin+=0.01f;<span class="kom">// Pootoèení hvìzd</span></p>
<p class="src2">star[loop].angle+=float(loop)/num;<span class="kom">// Zvý¹ení úhlu hvìzdy</span></p>
<p class="src2">star[loop].dist-=0.01f;<span class="kom">// Zmìna vzdálenosti hvìzdy od støedu</span></p>

<p>Zkontrolujeme zda hvìzda dosáhla støedu. Pokud se tak stane, dostane novou barvu a je posunuta o 5 jednotek od støedu, tak¾e mù¾e opìt zaèít svou cestu jako nová hvìzda.</p>

<p class="src2">if (star[loop].dist&lt;0.0f)<span class="kom">// Dosáhla støedu</span></p>
<p class="src2">{</p>
<p class="src3">star[loop].dist+=5.0f;<span class="kom">// Nová pozice</span></p>
<p class="src3">star[loop].r=rand()%256;<span class="kom">// Nová barva</span></p>
<p class="src3">star[loop].g=rand()%256;<span class="kom">// Nová barva</span></p>
<p class="src3">star[loop].b=rand()%256;<span class="kom">// Nová barva</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Pøidáme kód zji¹»ující stisk klávesy T. Pøejdìte k funkci WinMain(). Najdìte øádek SwapBuffers(hDC). Pí¹eme za nìj.</p>
<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">SwapBuffers(hDC);<span class="kom">// Prohození bufferù</span></p>
<p class="src"></p>
<p class="src4">if (keys['T'] &amp;&amp; !tp)<span class="kom">// T - tøpytivý efekt</span></p>
<p class="src4">{</p>
<p class="src5">tp=TRUE;</p>
<p class="src5">twinkle=!twinkle;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys['T'])<span class="kom">// Uvolnìní T</span></p>
<p class="src4">{</p>
<p class="src5">tp=FALSE;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_UP])<span class="kom">// ©ipka nahoru - nakloní obraz</span></p>
<p class="src4">{</p>
<p class="src5">tilt-=0.5f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN])<span class="kom">// ©ipka dolu - nakloní obraz</span></p>
<p class="src4">{</p>
<p class="src5">tilt+=0.5f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_PRIOR])<span class="kom">// PageUp - zvìt¹í hloubku</span></p>
<p class="src4">{</p>
<p class="src5">zoom-=0.2f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_NEXT])<span class="kom">// PageDown - zmen¹í hloubku</span></p>
<p class="src4">{</p>
<p class="src5">zoom+=0.2f;</p>
<p class="src4">}</p>

<p>A máme hotovo. Nauèili jste se jednoduchou, ale celkem efektní animaci.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson09.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson09_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson09.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson09.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson09.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson09.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson09.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson09.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson09.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson09.zip">Irix</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson09.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson09.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson09.jar">JoGL</a> kód této lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson09.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson09.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson09.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson09.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson09.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson09.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson09.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson09.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson09.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/solaris/lesson09.zip">Solaris</a> kód této lekce. ( <a href="mailto:lakmal@gunasekara.de">Lakmal Gunasekara</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson09.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:pdetagyos@home.com">Peter De Tagyos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson09.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson09.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(9);?>
<?FceNeHeOkolniLekce(9);?>

<?
include 'p_end.php';
?>
