<?
$g_title = 'CZ NeHe OpenGL - Zprovoznìní OpenGL v Linuxu (ovladaèe karty, kompilace)';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Zprovoznìní OpenGL v Linuxu (ovladaèe karty, kompilace)</h1>

<p class="nadpis_clanku">Kdy¾ jsem pøibli¾nì pøed pùl rokem (podzim 2003) pøecházel z MS Windows&reg; na operaèní systém Linux, mìl jsem relativnì velké potí¾e se zprovoznìním OpenGL. Nejedná se sice o nic slo¾itého, nicménì pro tehdy nic nechápajícího Woqa u¾ivatele (analogie na Frantu u¾ivatele :-) to byl naprosto neøe¹itelný problém.</p>

<p>Nad tímto èlánkem budou s nejvìt¹í pravdìpodobností v¹ichni Linuxoví guru nechápavì kroutit hlavou (my¹leno nad jeho smyslem ne slo¾itostí obsahu), nicménì zaèáteèníkùm by se opravdu mohl hodit. Pokud budete mít po pøeètení dojem, ¾e byl naprosto pod va¹i úroveò, velice se vám omlouvám, ale ka¾dý nìkde musí zaèít.</p>

<p>V¹echno, co zde budu popisovat urèitì platí pro <?OdkazBlank('http://www.mandrake.cz/', 'Mandrake');?> Linux 9.2, s jinými distribucemi nemám zku¹enosti, ale mìlo by to být stejné nebo alespoò podobné.</p>

<h3>Program je extrémnì trhaný - chybí ovladaèe grafické karty</h3>

<p>Tento problém je zpùsoben tím, ¾e v systému není pøítomen ovladaè grafické karty (respektive je, ale pouze obecný) a OpenGL se kompletnì emuluje na software. Abych pøede¹el jízlivým poznámkám od u¾ivatelù jiného operaèního systému: Po instalaci M$ Windows XP&reg; je situace naprosto stejná, nicménì pøíèina komplikací le¾í na trochu jiném místì.</p>

<p>V Linuxu celý problém spoèívá v tom, ¾e ovladaè vytvoøený výrobcem, je komerèní software (nebo nìco v tom smyslu), a proto by systém nemohl být zdarma. Samozøejmì, kdy¾ si Linux koupíte, platíte nìjakou èástku, napø. já jsem obìtoval plných 361 Kè! V cenì byly 3 standardní CD Mandrake, 1 Bonus CD od èeského distributora (v¹echny 4 lisované) a ti¹tìný 136 stránkový manuál. Pokud by se vám pøesto ji¾ zmínìných 361 Kè zdálo pøíli¹ mnoho, nic vám, kromì pomalého pøipojení k internetu, nebrání ve stáhnutí ISO obrazù CD z <?OdkazBlank('ftp://mandrake.redbox.cz/');?>, pak to bude opravdu zadarmo. V pøípadì dra¾¹ích verzí systému, kde u¾ má výrobce nìjaký zisk, bývají ovladaèe na správném místì ihned po instalaci.</p>

<p>Abych trochu rýpl do M$ a prosím opravte mì, jestli se mýlím. Proè nejsou ve Windows XP ovladaèe s podporou OpenGL nainstalované, ani kdy¾ je karta správnì detekovaná? Nejsem si jistý, zda na software bì¾í i DirectX (nevím, nepou¾ívám - ani Win ani DirectX), ale dá se pøedpokládat, ¾e ne. Zkrátka OpenGL je konkurenèní standard...</p>

<p>Jak tedy nainstalovat driver grafické karty? Nejdøíve zajdìte na web výrobce (ATI - <?OdkazBlank('http://www.ati.com/');?>, NVIDIA - <?OdkazBlank('http://www.nvidia.com/');?>) a stáhnìte si je. Mimochodem, u Mandrake Linuxu je mù¾ete najít na Bonus CD. Firma NVIDIA podporuje Linux u¾ hodnì dlouho, tak¾e by zde nemìly být vìt¹í problémy. Naproti tomu ATI se o Linux zaèala starat a¾ od Radeonu 8500. Instalaci ovladaèù karet tìchto dvou výrobcù popisuje napø. <?OdkazBlank('ftp://mandrake.contactel.cz/people/bibri/doc/cz/', 'Mandrake - Instalaèní manuál');?>, který napsal pan Ivan Bíbr. Pøed vlastní instalací si také urèitì pøeètìte README dokument, který najdete u ovladaèù. Nìkteré vìci se pøece jen mohly zmìnit, budete mít jistotu.</p>

<p>Schematicky nastíním alespoò postup... NVIDIA má vlastní instalátor, který sám zaøídí vìt¹inu potøebných vìcí. Nemìli byste ho spou¹tìt z grafického re¾imu, ale výhradnì z konzole. Systém X Window by mìl být pøi této operaci v¾dy vypnutý. Osobnì jsem zprovozòoval GeForce 2 a v¹e bylo bez nejmen¹ích problémù. Po dokonèení instalace je je¹tì nutné zmìnit hodnoty nìkolika polo¾ek v konfiguraèním souboru XFree (/etc/X11/XF86Config-4). V¹e potøebné najdete v README souboru.</p>

<p>ATI, co vím, distribuuje ovladaèe v balíècích RPM. Jak jsem ji¾ napsal vý¹e, jedná se pouze o karty Radeon 8500 a novìj¹í, tak¾e s mým Radeonem 7000 byl trochu problém. Na¹tìstí jeho drivery (a nìkolika desítek dal¹ích karet) v sobì obsahuje pøímo XFree. Nevím, jak se zapínají obecnì, ale s nejvìt¹í pravdìpodobností se opìt jedná o /etc/X11/XF86Config-4. Mandrake 9.2 má v závìru instalace &quot;Souhrn&quot;, ve kterém lze zmìnit v¹echna dosavadní nastavení. Na stejném místì, kde se definuje rozli¹ení monitoru, barevná hloubka a podobnì, najdete i polo¾ku &quot;Grafická karta&quot;. Pokud zmìníte volbu ze standardního &quot;Radeon&quot; na &quot;Radeon fglrx&quot;, instalátor se vás v dal¹ím kroku zeptá, zda chcete pou¾ít softwarovou emulaci OpenGL nebo bìh na hardwaru. Pokud máte Linux u¾ nainstalovaný, lze ovladaè zmìnit v Ovládací centrum -&gt; Hardware -&gt; Nastavení grafického serveru -&gt; Grafická karta (nezkou¹el jsem...).</p>

<div class="okolo_img"><a href="images/clanky/cl_gl_linux/ovladac.jpg"><img src="images/clanky/cl_gl_linux/ovladac_small.jpg" width="400" height="300" alt="Zmìna ovladaèe grafické karty" /></a></div>

<h3>Nelze zkompilovat OpenGL/SDL aplikace - v systému nejsou potøebné knihovny</h3>

<p>Znám nìkolik lidí, kteøí programují OpenGL aplikace pod knihovnou SDL (viz <?OdkazWeb('clanky', 'èlánky');?>). Její nejvìt¹í výhodou je, ¾e se výsledný program dá portovat na mnoho operaèních systémù. V mém pøípadì se jedná o vývoj v Linuxu a pøípadný pøenos do MS Windows, nicménì je mo¾ný i obrácený smìr :-).</p>

<p>Jako vývojové prostøedí preferuji textový KDE editor KWrite (takový &quot;malièko&quot; lep¹í Notepad) a kompilátor gcc popø. g++. Je samozøejmì mo¾né pou¾ívat i specializovaná vývojová prostøedí, jako jsou napøíklad KDevelop nebo Anjuta, která si v nièem nezadají s Wokenním Visual C++. Abyste pochopili následný výklad, doporuèuji pøeèíst si alespoò <?OdkazBlank('http://www.root.cz/clanek/2009', 'první díl');?> série èlánkù Programování pod Linuxem pro v¹echny, který vychází na <?OdkazBlank('http://www.root.cz/');?>. Bez tìchto znalostí se opravdu neobejdete.</p>

<p>Jestli je mo¾né OpenGL program zkompilovat, lze nejlépe ovìøit samotnou kompilací. Pou¾ijte tøeba demonstraèní pøíklad umístìný úplnì dole na této stránce - kdysi jsem ho stáhl z internetu a pou¾ívám ho právì na úvodní testy po nové instalaci systému.</p>

<p>Rozbalte archív (napøíklad Konquerorem) a po pøesunu do adresáøe projektu zadejte pøíkaz &quot;make&quot;, který provádí kompilaci.</p>

<p class="src0"><span class="kom">[woq@localhost Documents]$</span> <b>cd Color</b></p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span> <b>make</b></p>
<p class="src0">g++ -c Main.cpp `sdl-config --cflags`</p>
<p class="src0">/bin/sh: line 1: sdl-config: command not found</p>
<p class="src0">In file included from Main.cpp:15:</p>
<p class="src0">main.h:10:94: SDL.h: No such file or directory</p>
<p class="src0">In file included from Main.cpp:15:</p>
<p class="src0">main.h:35: error: syntax error before `*' token</p>
<p class="src0">main.h:59: error: `SDL_keysym' was not declared in this scope</p>
<p class="src0">main.h:59: error: `keysym' was not declared in this scope</p>
<p class="src0">main.h:59: error: variable or field `HandleKeyPressEvent' declared void</p>
<p class="src0">Main.cpp: In function `void MainLoop()':</p>
<p class="src0">Main.cpp:40: error: `SDL_Event' undeclared (first use this function)</p>
<p class="src0">Main.cpp:40: error: (Each undeclared identifier is reported only once for each function it appears in.)</p>
<p class="src0">Main.cpp:40: error: syntax error before `;' token</p>
<p class="src0">Main.cpp:44: error: `event' undeclared (first use this function)</p>
<p class="src0">Main.cpp:44: error: `SDL_PollEvent' undeclared (first use this function)</p>
<p class="src0">Main.cpp:48: error: `SDL_QUIT' undeclared (first use this function)</p>
<p class="src0">Main.cpp:52: error: `SDL_KEYDOWN' undeclared (first use this function)</p>
<p class="src0">Main.cpp:53: error: `HandleKeyPressEvent' cannot be used as a function</p>
<p class="src0">Main.cpp:56: error: `SDL_VIDEORESIZE' undeclared (first use this function)</p>
<p class="src0">Main.cpp:58: error: `MainWindow' undeclared (first use this function)</p>
<p class="src0">Main.cpp:58: error: `SDL_SetVideoMode' undeclared (first use this function)</p>
<p class="src0">Main.cpp:63: error: `SDL_GetError' undeclared (first use this function)</p>
<p class="src0">Main.cpp: In function `void RenderScene()':</p>
<p class="src0">Main.cpp:116: error: `SDL_GL_SwapBuffers' undeclared (first use this function)</p>
<p class="src0">make: *** [Main.o] Error 1</p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span></p>

<p>Pokud se vypí¹e nìco v tomto stylu, není v systému nainstalovaná knihovna SDL. Postupujte podle následujícího obrázku. Mimochodem, nejsou na nìm vidìt ty správné balíèky, proto¾e u mì je u¾ v¹e funkèní.</p>

<div class="okolo_img"><a href="images/clanky/cl_gl_linux/install_sdl.jpg"><img src="images/clanky/cl_gl_linux/install_sdl_small.jpg" width="400" height="300" alt="Instalace SDL" /></a></div>

<p>Znovu zkuste make. Jak je vidìt z následujícího výpisu, kompilaèní fáze probìhla v poøádku, ale linker nemohl najít knihovnu libGL(U).so (nepøesnì øeèeno: nìco jako opengl32.lib z Windows).</p>

<p class="src0"><span class="kom">[woq@localhost Color]$</span> <b>make</b></p>
<p class="src0">g++ -c Main.cpp `sdl-config --cflags`</p>
<p class="src0">g++ -c Init.cpp `sdl-config --cflags`</p>
<p class="src0">g++ -o Color  Main.o Init.o `sdl-config --libs` -lGL -lGLU -lm</p>
<p class="src0">/usr//bin/ld: cannot find -lGL</p>
<p class="src0">collect2: ld returned 1 exit status</p>
<p class="src0">make: *** [Color] Error 1</p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span></p>

<p>Nevím proè nejsou knihovny potøebné pro OpenGL umístìny v adresáøi /usr/lib, ale v jeho XFree verzi /usr/X11R6/lib, která v¹ak není uvedena v systémové promìnné s cestami ke knihovnám. Existuje nìkolik zpùsobù øe¹ení, z nich¾ je asi nejsnadnìj¹í vytvoøit symbolické odkazy (zástupce) na potøebné soubory. Proto¾e obyèejní u¾ivatelé nemají pøístupová práva na zápis do tìchto adresáøù, musíte se pøihlásit jako superu¾ivatel root.</p>

<p class="src0"><span class="kom">[woq@localhost Color]$</span> <b>su root</b></p>
<p class="src0">Password:</p>
<p class="src0"><span class="kom">[root@localhost Color]#</span> <b>ln -s /usr/X11R6/lib/libGL.so /usr/lib/libGL.so</b></p>
<p class="src0"><span class="kom">[root@localhost Color]#</span> <b>ln -s /usr/X11R6/lib/libGLU.so /usr/lib/libGLU.so</b></p>
<p class="src0"><span class="kom">[root@localhost Color]#</span> <b>exit</b></p>
<p class="src0">exit</p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span></p>

<p>Nyní by mìlo být v¹e v poøádku. Zapi¹te make a po zkompilování spus»te vytvoøený program.</p>

<p class="src0"><span class="kom">[woq@localhost Color]$</span> <b>make</b></p>
<p class="src0">g++ -c Main.cpp `sdl-config --cflags`</p>
<p class="src0">g++ -c Init.cpp `sdl-config --cflags`</p>
<p class="src0">g++ -o Color  Main.o Init.o `sdl-config --libs` -lGL -lGLU -lm</p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span> <b>Color</b></p>
<p class="src0"> Hit the F1 key to Toggle between Fullscreen and windowed mode</p>
<p class="src0"> Hit ESC to quit</p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span></p>

<p>A výsledek celého na¹eho sna¾ení...</p>

<div class="okolo_img"><a href="images/clanky/cl_gl_linux/all_ok.jpg"><img src="images/clanky/cl_gl_linux/all_ok_small.jpg" width="400" height="300" alt="Vse OK" /></a></div>

<p>Pokud byste chtìli pøilinkovat knihovny nutné pro OpenGL napø. ve vývojovém prostøedí KDevelop, kliknìte v menu na polo¾ku Projekt a v ní na Options... Objeví se vám okno, ve kterém staèí na kartì Linker Options pøidat potøebné knihovny (mìly by staèit -lGL -lGLU -lSDL).</p>

<div class="okolo_img"><a href="images/clanky/cl_gl_linux/kdevelop.jpg"><img src="images/clanky/cl_gl_linux/kdevelop_small.jpg" width="400" height="300" alt="Pøilinkování OpenGL knihoven ve vývojovém prostøedí KDevelop" /></a></div>

<p>Pokud u¾ nìjakou dobu v Linuxu pracujete, jistì byl pro vás celý postup velice jednoduchý, ale pro lidi, kteøí na nìj pøe¹li teprve vèera, to byl s nejvìt¹í pravdìpodobností absolutnì neøe¹itelný problém. Pro mì tedy byl. Zprovoznìní kompilace OpenGL programù pøerostlo v chaotické pokusy a omyly, pøi kterých jsem si kompletnì zlikvidoval systém závislostí softwarových balíèkù. Po pøekonání této fáze jsem u¾ &quot;jen&quot;, kvùli jednomu RPM stáhnutému z internetu obsahujícímu GLU, který byl v konfliktu s jiným, musel odinstalovat a následnì znovu nainstalovat pìtinu celého software.</p>

<p>Hlavním problémem pro mì tehdy bylo odnauèit se øe¹it problémy v Linuxu &quot;Wokenním zpùsobem&quot;. Kdy¾ uvedu pøíklad: je¹tì nedávno jsem mìl za to, ¾e *.so soubory jsou analogií *.dll knihoven z Windows a *.a pøedstavují *.lib. Ve v¹ech textech, co jsem èetl o kompilování v gcc, bylo pøece jasnì uvedeno, ¾e se argument -lGL pøevede na libGL.a a tento soubor se pak hledá ve standardních adresáøích s knihovnami. To je sice naprostá pravda, ale nikde u¾ nebyla ani zmínka, ¾e se u linkování pou¾ívá i libGL.so, o kterém jsem si tudí¾ myslel, ¾e je Linuxovou formou DLL knihovny - v linkovací fázi naprosto nepou¾itelné. Pøekonání této utkvìlé pøedstavy mi trvalo nejménì mìsíc... a to je jen jedna z mála ukázek. Abych to ukonèil: Linux se nerovná Windows, jak toto pochopíte (uvnitø - ne, ¾e vám to nìkdo øekne), máte vyhráno.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_gl_linux_color.tar.gz');?> - zdrojové kódy pro test kompilace OpenGL a SDL v Linuxu</li>
</ul>

<?
include 'p_end.php';
?>
