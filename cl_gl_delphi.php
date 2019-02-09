<?
$g_title = 'CZ NeHe OpenGL - Vytvoøení OpenGL okna v Delphi';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Vytvoøení OpenGL okna v Delphi</h1>

<p class="nadpis_clanku">Tento èlánek popisuje vytvoøení OpenGL okna pod operaèním systémem MS Windows ve vývojovém prostøedí Borland Delphi. Já osobnì pou¾ívám Delphi verze 7, ale ani v ni¾¹ích verzích by nemìl být problém vytvoøený kód zkompilovat a spustit. Z vìt¹í èásti se jedná o pøepis prvního NeHe Tutoriálu z jazyka C/C++ do Pascalu. Tak¾e smìle do toho...</p>

<p>Zaèneme vytvoøením nového projektu. V nabídce File - New  vybereme konzolovou aplikaci (Console Application). Postup se mù¾e v rùzných verzích Delphi mírnì li¹it. Ve skuteènosti konzolovou aplikaci vytváøet nebudeme, ale vygenerovaný kód je nejblí¾e tomu, co potøebujeme.</p>

<p class="src0">program Project1;</p>
<p class="src"></p>
<p class="src0"><span class="kom">{$APPTYPE CONSOLE}</span></p>
<p class="src"></p>
<p class="src0">uses</p>
<p class="src1">SysUtils;</p>
<p class="src"></p>
<p class="src0">begin</p>
<p class="src1"><span class="kom">{ TODO -oUser -cConsole Main : Insert code here }</span></p>
<p class="src0">end.</p>

<p>Odstraníme zbytky kódu, které by nám pøeká¾ely a dostaneme:</p>

<p class="src0">program Project1;</p>
<p class="src"></p>
<p class="src0">begin</p>
<p class="src"></p>
<p class="src0">end.</p>

<p>Základ tedy máme vytvoøen. Na zaèátek pøidáme jednotky, které budeme dále v kódu pou¾ívat.</p>

<p class="src0">uses</p>
<p class="src1">Windows,</p>
<p class="src1">Messages,</p>
<p class="src1">OpenGL;</p>

<p>Dále deklarujeme globální promìnné. Komentáøe, myslím, hovoøí za v¹e, tak¾e se zastavím jen u prvních dvou. Ka¾dý OpenGL program je spojen s Rendering Contextem. Rendering Context øíká, která spojení volá OpenGL, aby se spojilo s Device Context (kontext zaøízení). Nám staèí vìdìt, ¾e OpenGL Rendering Context je definován jako HGLRC. Aby program mohl kreslit do okna, potøebujeme vytvoøit Device Context. Ve Windows je Device Context definován jako HDC. Device Context napojí okno na GDI (grafické rozhraní).</p>

<p class="src0">var</p>
<p class="src1">h_Rc: HGLRC;<span class="kom">// Trvalý Rendering Context</span></p>
<p class="src1">h_Dc: HDC;<span class="kom">// Privátní GDI Device Context</span></p>
<p class="src1">h_Wnd: HWND;<span class="kom">// Obsahuje Handle na¹eho okna</span></p>
<p class="src1">keys: array [0..255] of BOOL;<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src1">Active: bool = true;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src1">FullScreen: bool = true;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>Následující procedura se volá v¾dy, kdy¾ u¾ivatel mìní velikost okna. I kdy¾ nejste schopni zmìnit velikost okna (napøíklad ve fullscreenu), bude tato funkce volána alespoò jednou, aby nastavila perspektivní pohled pøi spu¹tìní programu. Velikost OpenGL scény se bude mìnit v závislosti na ¹íøce a vý¹ce okna, ve kterém je zobrazena.</p>

<p class="src0">procedure ReSizeGLScene(Width: GLsizei; Height: GLsizei);<span class="kom">// Zmìna velikosti a inicializace OpenGL okna</span></p>
<p class="src0">begin</p>
<p class="src1">if Height = 0 then<span class="kom">// Zabezpeèení proti dìlení nulou</span></p>
<p class="src2">Height := 1;<span class="kom">// Nastaví vý¹ku na jedna</span></p>
<p class="src"></p>
<p class="src1">glViewport(0, 0, Width, Height);<span class="kom">// Resetuje aktuální nastavení</span></p>

<p>Nastavíme obraz na perspektivní projekci, to znamená, ¾e vzdálenìj¹í objekty budou, stejnì jako v reálném svìtì, men¹í. Pøíkaz glMatrixMode(GL_PROJECTION) ovlivní formu obrazu, díky ní budeme moci následnì definovat, jak výrazná bude perspektiva. Vytvoøíme realisticky vypadající scénu. Funkce glLoadIdentity resetuje matici, to znamená, ¾e ji nastaví do výchozího stavu. Perspektiva je vypoèítána s úhlem pohledu 45 stupòù a je zalo¾ena na vý¹ce a ¹íøce okna. Èíslo 1.0 je poèáteèní a 100.0 koncový bod, který øíká jak hluboko do obrazovky mù¾eme kreslit. glMatrixMode(GL_MODELVIEW) oznamuje, ¾e forma pohledu bude znovu zmìnìna.</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvolí projekèní matici</span></p>
<p class="src1">glLoadIdentity;<span class="kom">// Reset matice</span></p>
<p class="src1">gluPerspective(45.0, Width/Height, 1.0, 100.0);<span class="kom">// Výpoèet perspektivy</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvolí matici Modelview</span></p>
<p class="src1">glLoadIdentity;<span class="kom">// Reset matice</span></p>
<p class="src0">end;</p>

<p>Nastavíme v¹e potøebné pro OpenGL. Definujeme èerné pozadí, zapneme depth buffer, aktivujeme smooth shading (vyhlazené stínování), atd. Tato funkce se volá a¾ po vytvoøení okna, proto¾e musí být dostupný rendering kontext.</p>

<p class="src0">function InitGL: bool;<span class="kom">// V¹echno nastavení OpenGL</span></p>
<p class="src0">begin</p>

<p>Následující øádek povolí stínování, aby se barvy na polygonech pìknì promíchaly.</p>

<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Míchání barev na polygonech</span></p>

<p>Nastavíme barvu pozadí prázdné obrazovky. Rozsah barev se urèuje ve stupnici od nuly do jedné. 0.0 je nejtmav¹í a 1.0 je nejsvìtlej¹í. První parametr ve funkci glClearColor je intenzita èervené barvy, druhý zelené a tøetí modré. Èím bli¾¹í je hodnota barvy 1.0, tím svìtlej¹í slo¾ka barvy bude. Poslední parametr je hodnota alpha (prùhlednost). Kdy¾ budeme èistit obrazovku, tak se o prùhlednost starat nemusíme. Nyní ji necháme na 0.5.</p>

<p class="src1">glClearColor(0.0, 0.0, 0.0, 0.5);<span class="kom">// Èerné pozadí</span></p>

<p>Následující tøi øádky ovlivòují depth buffer. Depth buffer si mù¾ete pøedstavit jako vrstvy/hladiny obrazovky. Obsahuje informace, o tom jak hluboko jsou zobrazované objekty.</p>

<p class="src1">glClearDepth(1.0);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí hloubkové testování</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>

<p>Dále oznámíme, ¾e chceme pou¾ít nejlep¹í korekce perspektivy. Jen nepatrnì to sní¾í výkon, ale zlep¹í se vzhled celé scény.</p>

<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>

<p>Nakonec vrátíme true. Kdy¾ budeme chtít zjistit, jestli inicializace probìhla bez problémù, mù¾eme zkontrolovat, zda funkce vrátila hodnotu true nebo false. Mù¾ete pøidat vlastní kód, který vrátí false, kdy¾ se inicializace nezdaøí - napø. pøi neúspì¹ném loadingu textur.</p>

<p class="src1">Result := true;<span class="kom">// Inicializace probìhla v poøádku</span></p>
<p class="src0">end;</p>

<p>Do této funkce umístíme v¹echno vykreslování. Jediné co nyní udìláme, je smazání obrazovky na barvu, pro kterou jsme se rozhodli, také vyma¾eme obsah hloubkového bufferu a resetujeme scénu. Zatím nebudeme nic kreslit. Pøíkaz Result := true nám øíká, ¾e pøi kreslení nenastaly ¾ádné problémy.</p>

<p class="src0">function DrawGLScene: bool;<span class="kom">// Vykreslování</span></p>
<p class="src0">begin</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT or GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity;<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">Result := true;<span class="kom">// Vykreslení probìhlo v poøádku</span></p>
<p class="src0">end;</p>

<p>Následující èást kódu je volána tìsnì pøed koncem programu. Úkolem funkce KillGLWindow je uvolnìní renderovacího kontextu, kontextu zaøízení a handle okna. Pøidal jsem zde nezbytné kontrolování chyb. Kdy¾ není program schopen nìco uvolnit, oznámí chybu, která øíká co selhalo. Usnadní tak slo¾ité hledání problémù.</p>

<p class="src0">procedure KillGLWindow;<span class="kom">// Zavírání okna</span></p>
<p class="src0">begin</p>

<p>Zjistíme, zda je program ve fullscreenu. Pokud ano, tak ho pøepneme zpìt do systému. Mohli bychom vypnout okno pøed opu¹tìním fullscreenu, ale na nìkterých grafických kartách tím zpùsobíme problémy a systém by se mohl zhroutit.</p>

<p class="src1">if FullScreen then<span class="kom">// Jsme ve fullscreenu?</span></p>
<p class="src1">begin</p>

<p>K návratu do pùvodního nastavení systému pou¾íváme funkci ChangeDisplaySettings(devmode(nil^),0). Jako první parametr zadáme devmode(nil^) a jako druhý 0 - pou¾ijeme hodnoty ulo¾ené v registrech Windows (pùvodní rozli¹ení, barevnou hloubku, obnovovací frekvenci, atd.). Po pøepnutí zviditelníme kurzor.</p>

<p class="src2">ChangeDisplaySettings(devmode(nil^), 0);<span class="kom">// Pøepnutí do systému</span></p>
<p class="src2">showcursor(true);<span class="kom">// Zobrazí kurzor my¹i</span></p>
<p class="src1">end;</p>

<p>Zkontrolujeme, zda máme renderovací kontext. Kdy¾ ne, program pøeskoèí èást kódu pod ním, který kontroluje, zda máme kontext zaøízení.</p>

<p class="src1">if h_rc <> 0 then<span class="kom">// Máme rendering kontext?</span></p>
<p class="src1">begin</p>

<p>Zjistíme, zda mù¾eme odpojit h_RC od h_DC.</p>

<p class="src2">if (not wglMakeCurrent(h_Dc, 0)) then<span class="kom">// Jsme schopni oddìlit kontexty?</span></p>

<p>Pokud nejsme schopni uvolnit DC a RC, zobrazíme zprávu, ¾e DC a RC nelze uvolnit. Nula v parametru znamená, ¾e informaèní okno nemá ¾ádného rodièe. Text ihned za 0 je text, který se vypí¹e do zprávy. Dal¹í parametr definuje text li¹ty. Parametr MB_OK znamená, ¾e chceme mít na chybové zprávì jen jedno tlaèítko s nápisem OK. MB_ICONERROR zobrazí ikonu.</p>

<p class="src3">MessageBox(0, 'Release of DC and RC failed.', 'Shutdown Error', MB_OK or MB_ICONERROR);</p>

<p>Zkusíme vymazat Rendering Context. Pokud se pokus nezdaøí, opìt se zobrazí chybová zpráva. Nakonec nastavíme h_RC a 0.</p>

<p class="src2">if (not wglDeleteContext(h_Rc)) then<span class="kom">// Jsme schopni smazat RC?</span></p>
<p class="src2">begin</p>
<p class="src3">MessageBox(0, 'Release of Rendering Context failed.', 'Shutdown Error', MB_OK or MB_ICONERROR);</p>
<p class="src3">h_Rc := 0;<span class="kom">// Nastaví hRC na 0</span></p>
<p class="src2">end;</p>
<p class="src1">end;</p>

<p>Zjistíme, zda má program kontext zaøízení. Kdy¾ ano odpojíme ho. Pokud se odpojení nezdaøí, zobrazí se chybová zpráva a h_DC bude nastaven na 0.</p>

<p class="src1">if (h_Dc = 1) and (releaseDC(h_Wnd,h_Dc) <> 0) then<span class="kom">// Jsme schopni uvolnit DC</span></p>
<p class="src1">begin</p>
<p class="src2">MessageBox(0, 'Release of Device Context failed.', 'Shutdown Error', MB_OK or MB_ICONERROR);</p>
<p class="src2">h_Dc := 0;<span class="kom">// Nastaví hDC na 0</span></p>
<p class="src1">end;</p>

<p>Nyní zjistíme, zda máme handle okna a pokud ano, pokusíme se odstranit okno pou¾itím funkce DestroyWindow(h_Wnd). Pokud se pokus nezdaøí, zobrazí se chybová zpráva a h_Wnd bude nastaveno na 0.</p>

<p class="src1">if (h_Wnd <> 0) and (not destroywindow(h_Wnd)) then<span class="kom">// Jsme schopni odstranit okno?</span></p>
<p class="src1">begin</p>
<p class="src2">MessageBox(0, 'Could not release hWnd.', 'Shutdown Error', MB_OK or MB_ICONERROR);</p>
<p class="src2">h_Wnd := 0;<span class="kom">// Nastaví hWnd na 0</span></p>
<p class="src1">end;</p>

<p>Odregistrováním tøídy okna oficiálnì uzavøeme okno a pøedejdeme zobrazení chybové zprávy &quot;Windows Class already registered&quot; pøi opìtovném spu¹tìní programu.</p>

<p class="src1">if (not UnregisterClass('OpenGL', hInstance)) then<span class="kom">// Jsme schopni odregistrovat tøídu okna?</span></p>
<p class="src1">begin</p>
<p class="src2">MessageBox(0, 'Could Not Unregister Class.', 'SHUTDOWN ERROR', MB_OK or MB_ICONINFORMATION);</p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Dal¹í èást kódu má na starosti vytvoøení OpenGL okna. Jak si mù¾ete v¹imnout funkce vrací boolean a pøijímá 5 parametrù v poøadí: název okna, ¹íøka okna, vý¹ka okna, barevná hloubka, fullscreen (pokud je parametr true program pobì¾í ve fullscreenu, pokud bude false program pobì¾í v oknì). Vracíme boolean, abychom vìdìli, zda bylo okno úspì¹nì vytvoøeno.</p>

<p class="src0">function CreateGlWindow(title:Pchar; width, height, bits:integer; FullScreenflag:bool) : boolean stdcall;</p>

<p>Za chvíli po¾ádáme Windows, aby pro nás na¹el pixel format, který odpovídá tomu, který chceme. Toto èíslo ulo¾íme do promìnné PixelFormat.</p>

<p class="src0">var</p>
<p class="src1">Pixelformat: GLuint;<span class="kom">// Ukládá formát pixelù</span></p>

<p>Wc bude pou¾ijeme k uchování informací o struktuøe Windows Class. Zmìnou hodnot jednotlivých polo¾ek lze ovlivnit vzhled a chování okna. Pøed vytvoøením samotného okna se musí zaregistrovat nìjaká struktura pro okno.</p>

<p class="src1">wc: TWndclass;<span class="kom">// Struktura Windows Class</span></p>

<p>DwExStyle a dwStyle ponesou informaci o normálních a roz¹íøených informacích o oknu.</p>

<p class="src1">dwExStyle: dword;<span class="kom">// Roz¹íøený styl okna</span></p>
<p class="src1">dwStyle: dword;<span class="kom">// Styl okna</span></p>
<p class="src1">pfd: pixelformatdescriptor;<span class="kom">// Nastavení formátu pixelù</span></p>
<p class="src1">dmScreenSettings: Devmode;<span class="kom">// Mód zaøízení</span></p>
<p class="src1">h_Instance: hinst;<span class="kom">// Instance okna</span></p>
<p class="src1">WindowRect: TRect;<span class="kom">// Obdélník okna</span></p>
<p class="src"></p>
<p class="src0">begin
<p class="src1">WindowRect.Left := 0;<span class="kom">// Nastaví levý okraj na nulu</span></p>
<p class="src1">WindowRect.Top := 0;<span class="kom">// Nastaví horní okraj na nulu</span></p>
<p class="src1">WindowRect.Right := width;<span class="kom">// Nastaví pravý okraj na zadanou hodnotu</span></p>
<p class="src1">WindowRect.Bottom := height;<span class="kom">// Nastaví spodní okraj na zadanou hodnotu</span></p>

<p>Získáme instanci pro okno.</p>

<p class="src1">h_instance := GetModuleHandle(nil);<span class="kom">// Získá instanci okna</span></p>

<p>Pøiøadíme globální promìnné fullscreen, hodnotu fullscreenflag. Tak¾e pokud na¹e okno pobì¾í ve fullscreenu, promìnná fullscreen se bude rovnat true.</p>

<p class="src1">FullScreen := FullScreenflag;<span class="kom">// Nastaví promìnnou fullscreen na správnou hodnotu</span></p>

<p>Definujeme Window Class. CS_HREDRAW a CS_VREDRAW donutí na¹e okno, aby se pøekreslilo, kdykoliv se zmìní jeho velikost. CS_OWNDC vytvoøí privátní kontext zaøízení. To znamená, ¾e není sdílen s ostatními aplikacemi. WndProc je procedura okna, která sleduje pøíchozí zprávy pro program. ®ádná extra data pro okno nepou¾íváme, tak¾e do dal¹ích dvou polo¾ek pøiøadíme nulu. Nastavíme instanci a hIcon, co¾ je ikona, kterou pou¾ívá ná¹ program a pro kurzor my¹i pou¾íváme standardní ¹ipku. Barva pozadí nás nemusí zajímat (to zaøídíme v OpenGL). Nechceme, aby okno mìlo menu, tak¾e i tuto hodnotu nastavíme na nil. Jméno tøídy mù¾e být libovolné.</p>

<p class="src1">with wc do</p>
<p class="src1">begin</p>
<p class="src2">style := CS_HREDRAW or CS_VREDRAW or CS_OWNDC;<span class="kom">// Pøekreslení pøi zmìnì velikosti a vlastní DC</span></p>
<p class="src2">lpfnWndProc := @WndProc;<span class="kom">// Definuje proceduru okna</span></p>
<p class="src2">cbClsExtra := 0;<span class="kom">// ®ádná extra data</span></p>
<p class="src2">cbWndExtra := 0;<span class="kom">// ®ádná extra data</span></p>
<p class="src2">instance := h_Instance;<span class="kom">// Instance</span></p>
<p class="src2">hIcon := LoadIcon(0, IDI_WINLOGO);<span class="kom">// Standardní ikona</span></p>
<p class="src2">hCursor := LoadCursor(0, IDC_ARROW);<span class="kom">// Standardní kurzor my¹i</span></p>
<p class="src2">hbrBackground := 0;<span class="kom">// Pozadí není nutné</span></p>
<p class="src2">lpszMenuName := nil;<span class="kom">// Nechceme menu</span></p>
<p class="src2">lpszClassName := 'OpenGl';<span class="kom">// Jméno tøídy okna</span></p>
<p class="src1">end;</p>

<p>Zaregistrujeme právì definovanou tøídu okna. Kdy¾ nastane chyba, zobrazí se chybové hlá¹ení. Zmáèknutím tlaèítka OK se program ukonèí.</p>

<p class="src1">if RegisterClass(wc) = 0 then<span class="kom">// Registruje tøídu okna</span></p>
<p class="src1">begin</p>
<p class="src2">MessageBox(0, 'Failed To Register The Window Class.', 'Error', MB_OK or MB_ICONERROR);</p>
<p class="src2">Result := false;<span class="kom">// Pøi chybì vrátí false</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Nyní si zjistíme, zda má program bì¾et ve fullscreenu nebo v oknì.</p>

<p class="src1">if FullScreen then<span class="kom">// Budeme ve fullscreenu?</span></p>
<p class="src1">begin</p>

<p>S pøepínáním do fullscreenu mívají lidé mnoho problémù. Je zde pár dùle¾itých vìcí, na které si musíte dávat pozor. Ujistìte se, ¾e ¹íøka a vý¹ka, kterou pou¾íváte ve fullscreenu, je toto¾ná s tou, kterou chcete pou¾ít v oknì. Dal¹í vìc je hodnì dùle¾itá. Musíte pøepnout do fullscreenu pøedtím, ne¾ vytvoøíte okno. V tomto kódu se o rovnost vý¹ky a ¹íøky nemusíte starat, proto¾e velikost ve fullscreenu i v oknì budou stejné.</p>

<p class="src2">ZeroMemory(@dmScreenSettings, sizeof(dmScreenSettings));<span class="kom">// Vynulování pamìti</span></p>
<p class="src"></p>
<p class="src2">with dmScreensettings do</p>
<p class="src2">begin</p>
<p class="src3">dmSize := sizeof(dmScreenSettings);<span class="kom">// Velikost struktury Devmode</span></p>
<p class="src3">dmPelsWidth := width;<span class="kom">// ©íøka okna</span></p>
<p class="src3">dmPelsHeight := height;<span class="kom">// Vý¹ka okna</span></p>
<p class="src3">dmBitsPerPel := bits;<span class="kom">// Barevná hloubka</span></p>
<p class="src3">dmFields := DM_BITSPERPEL or DM_PELSWIDTH or DM_PELSHEIGHT;</p>
<p class="src2">end;</p>

<p>Funkce ChangeDisplaySettings se pokusí pøepnout do módu, který je ulo¾en v dmScreenSettings. Pou¾iji parametr CDS_FULLSCREEN, proto¾e odstraní pracovní li¹tu ve spodní èásti obrazovky a nepøesune nebo nezmìní velikost okna pøi pøepínání z fullscreenu do systému nebo naopak.</p>

<p class="src2"><span class="kom">// Pokusí se pou¾ít právì definované nastavení</span></p>
<p class="src2">if (ChangeDisplaySettings(dmScreenSettings, CDS_FULLSCREEN)) <> DISP_CHANGE_SUCCESSFUL THEN</p>
<p class="src2">begin</p>

<p>Pokud právì vytvoøený fullscreen mód neexistuje, zobrazí se chybová zpráva s nabídkou spu¹tìní v oknì nebo opu¹tìní programu.</p>

<p class="src3"><span class="kom">// Nejde-li fullscreen, mù¾e u¾ivatel spustit program v oknì nebo ho opustit</span></p>
<p class="src3">if MessageBox(0, 'This FullScreen Mode Is Not Supported. Use Windowed Mode Instead?', 'NeHe GL', MB_YESNO or MB_ICONEXCLAMATION) = IDYES then</p>

<p>Kdy¾ se u¾ivatel rozhodne pro bìh v oknì, do promìnné fullscreen se pøiøadí false a program pokraèuje dále.</p>

<p class="src4">FullScreen := false<span class="kom">// Bìh v oknì</span></p>
<p class="src3">else</p>
<p class="src3">begin</p>

<p>Pokud se u¾ivatel rozhodl pro ukonèení programu, zobrazí se u¾ivateli zpráva, ¾e program bude ukonèen. Bude vrácena hodnota false, která na¹emu programu øíká, ¾e pokus o vytvoøení okna nebyl úspì¹ný a potom se program ukonèí.</p>

<p class="src4"><span class="kom">// Zobrazí u¾ivateli zprávu, ¾e program bude ukonèen</span></p>
<p class="src4">MessageBox(0, 'Program Will Now Close.', 'Error', MB_OK or MB_ICONERROR);</p>
<p class="src4">Result := false;<span class="kom">// Vrátí FALSE</span></p>
<p class="src4">exit;</p>
<p class="src3">end;</p>
<p class="src2">end;</p>
<p class="src1">end;</p>

<p>Proto¾e pokus o pøepnutí do fullscreenu mù¾e selhat nebo se u¾ivatel mù¾e rozhodnout pro bìh programu v oknì, zkontrolujeme je¹tì jednou, zda je promìnná fullscreen true nebo false. A¾ poté nastavíme typ obrazu.</p>

<p class="src1">if FullScreen then<span class="kom">// Jsme stále ve fullscreenu?</span></p>
<p class="src1">begin

<p>Pokud jsme stále ve fullscreenu nastavíme roz¹íøený styl na WS_EX_APPWINDOW, co¾ donutí okno, aby pøekrylo pracovní li¹tu. Styl okna urèíme na WS_POPUP. Tento typ okna nemá ¾ádné okraje, co¾ je pro fullscreen výhodné. Nakonec vypneme kurzor my¹i. Pokud vá¹ program není interaktivní, je vìt¹inou vhodnìj¹í ve fullscreenu kurzor vypnout.</p>

<p class="src2">dwExStyle := WS_EX_APPWINDOW;<span class="kom">// Roz¹íøený styl okna</span></p>
<p class="src2">dwStyle := WS_POPUP or WS_CLIPSIBLINGS or WS_CLIPCHILDREN;<span class="kom">// Styl okna</span></p>
<p class="src2">Showcursor(false);<span class="kom">// Skryje kurzor</span></p>
<p class="src1">end</p>
<p class="src1">else</p>
<p class="src1">begin</p>

<p>Pokud místo fullscreenu pou¾íváme bìh v oknì, nastavíme roz¹íøený styl na WS_EX_WINDOWEDGE. To dodá oknu trochu 3D vzhledu. Styl nastavíme na WS_OVERLAPPEDWINDOW místo na WS_POPUP. WS_OVERLAPPEDWINDOW vytvoøí okno s li¹tou, okraji, tlaèítky pro minimalizaci a maximalizaci. Budeme moci mìnit velikost.</p>

<p class="src2">dwExStyle := WS_EX_APPWINDOW or WS_EX_WINDOWEDGE;<span class="kom">// Roz¹íøený styl okna</span></p>
<p class="src2">dwStyle := WS_OVERLAPPEDWINDOW or WS_CLIPSIBLINGS or WS_CLIPCHILDREN;<span class="kom">// Styl okna</span></p>
<p class="src1">end;</p>

<p>Pøizpùsobíme okno podle stylu, který jsme vytvoøili. Pøizpùsobení udìlá okno v takovém rozli¹ení, jaké po¾adujeme. Normálnì by okraje pøekrývaly èást okna. S pou¾itím pøíkazu AdjustWindowRectEx ¾ádná èást OpenGL scény nebude pøekryta okraji, místo toho bude okno udìláno o málo vìt¹í, aby se do nìj ve¹ly v¹echny pixely tvoøící okraj okna. Ve fullscreenu tato funkce nemá ¾ádný efekt.</p>

<p class="src1">AdjustWindowRectEx(WindowRect, dwStyle, false, dwExStyle);<span class="kom">// Pøizpùsobení velikosti okna</span></p>

<p>Vytvoøíme okno a zkontrolujeme, zda bylo vytvoøeno správnì. Pou¾ijeme funkci CreateWindowEx se v¹emi parametry, které vy¾aduje. Roz¹íøený styl, který jsme se rozhodli pou¾ít, jméno tøídy (musí být stejné jako to, které jste pou¾ili, kdy¾ jste registrovali Window Class), titulek okna, styl okna, levá horní pozice okna (0,0 je nejjistìj¹í), ¹íøka a vý¹ka. Nechceme mít rodièovské okno ani menu, tak¾e nastavíme tyto parametry na 0. Zadáme instanci okna a koneènì pøiøadíme nil na místo posledního parametru.</p>

<p class="src1"><span class="kom">// Vytvoøení okna</span></p>
<p class="src1">H_wnd := CreateWindowEx(dwExStyle,<span class="kom">// Roz¹íøený styl</span></p>
<p class="src2">'OpenGl',<span class="kom">// Jméno tøídy</span></p>
<p class="src2">Title,<span class="kom">// Titulek</span></p>
<p class="src2">dwStyle,<span class="kom">// Definovaný styl</span></p>
<p class="src2">0, 0,<span class="kom">// Pozice</span></p>
<p class="src2">WindowRect.Right-WindowRect.Left,<span class="kom">// Výpoèet ¹íøky</span></p>
<p class="src2">WindowRect.Bottom-WindowRect.Top,<span class="kom">// Výpoèet vý¹ky</span></p>
<p class="src2">0,<span class="kom">// ®ádné rodièovské okno</span></p>
<p class="src2">0,<span class="kom">// Bez menu</span></p>
<p class="src2">hinstance,<span class="kom">// Instance</span></p>
<p class="src2">nil);<span class="kom">// Nepøedat nic do WM_CREATE</span></p>

<p>Dále zkontrolujeme, zda bylo vytvoøeno. Pokud bylo, h_Wnd obsahuje handle tohoto okna. Kdy¾ se vytvoøení okna nepovede, kód zobrazí chybovou zprávu a program se ukonèí.</p>

<p class="src1">if h_Wnd = 0 then<span class="kom">// Pokud se okno nepodaøilo vytvoøit</span></p>
<p class="src1">begin</p>
<p class="src2">KillGlWindow;<span class="kom">// Zru¹í okno</span></p>
<p class="src2">MessageBox(0, 'Window creation error.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Vrátí chybu</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Vybereme Pixel Format, který podporuje OpenGL, dále zvolíme double buffering a RGBA (èervená, zelená, modrá, prùhlednost). Pokusíme se najít formát, který odpovídá tomu, pro který jsme se rozhodli (16 bitù, 24 bitù, 32 bitù). Nakonec nastavíme Z-Buffer. Ostatní parametry se nepou¾ívají nebo pro nás nejsou dùle¾ité.</p>

<p class="src1">with pfd do<span class="kom">// Oznámíme Windows jak chceme v¹e nastavit</span></p>
<p class="src1">begin</p>
<p class="src2">nSize := SizeOf(PIXELFORMATDESCRIPTOR);<span class="kom">// Velikost struktury</span></p>
<p class="src2">nVersion := 1;<span class="kom">// Èíslo verze</span></p>
<p class="src2">dwFlags := PFD_DRAW_TO_WINDOW<span class="kom">// Podpora okna</span></p>
<p class="src2">or PFD_SUPPORT_OPENGL<span class="kom">// Podpora OpenGL</span></p>
<p class="src2">or PFD_DOUBLEBUFFER;<span class="kom">// Podpora Double Bufferingu</span></p>
<p class="src2">iPixelType := PFD_TYPE_RGBA;<span class="kom">// RGBA Format</span></p>
<p class="src2">cColorBits := bits;<span class="kom">// Zvolí barevnou hloubku</span></p>
<p class="src2">cRedBits := 0;<span class="kom">// Bity barev ignorovány</span></p>
<p class="src2">cRedShift := 0;</p>
<p class="src2">cGreenBits := 0;</p>
<p class="src2">cBlueBits := 0;</p>
<p class="src2">cBlueShift := 0;</p>
<p class="src2">cAlphaBits := 0;<span class="kom">// ®ádný alpha buffer</span></p>
<p class="src2">cAlphaShift := 0;<span class="kom">// Ignorován Shift bit</span></p>
<p class="src2">cAccumBits := 0;<span class="kom">// ®ádný akumulaèní buffer</span></p>
<p class="src2">cAccumRedBits := 0;<span class="kom">// Akumulaèní bity ignorovány</span></p>
<p class="src2">cAccumGreenBits := 0;</p>
<p class="src2">cAccumBlueBits := 0;</p>
<p class="src2">cAccumAlphaBits := 0;</p>
<p class="src2">cDepthBits := 16;<span class="kom">// 16-bitový hloubkový buffer (Z-Buffer)</span></p>
<p class="src2">cStencilBits := 0;<span class="kom">// ®ádný Stencil Buffer</span></p>
<p class="src2">cAuxBuffers := 0;<span class="kom">// ®ádný Auxiliary Buffer</span></p>
<p class="src2">iLayerType := PFD_MAIN_PLANE;<span class="kom">// Hlavní vykreslovací vrstva</span></p>
<p class="src2">bReserved := 0;<span class="kom">// Rezervováno</span></p>
<p class="src2">dwLayerMask := 0;<span class="kom">// Maska vrstvy ignorována</span></p>
<p class="src2">dwVisibleMask := 0;</p>
<p class="src2">dwDamageMask := 0;</p>
<p class="src1">end;</p>

<p>Pokud nenastaly problémy bìhem vytváøení okna, pokusíme se pøipojit kontext zaøízení. Pokud se to nepodaøí, zobrazí se chybové hlá¹ení a program se ukonèí.</p>

<p class="src1">h_Dc := GetDC(h_Wnd);<span class="kom">// Zkusí pøipojit kontext zaøízení</span></p>
<p class="src"></p>
<p class="src1">if h_Dc = 0 then<span class="kom">// Podaøilo se pøipojit kontext zaøízení?</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(0, 'Cant create a GL device context.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukonèí program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Kdy¾ získáme kontext zaøízení, pokusíme se najít odpovídající Pixel Format. Kdy¾ ho Windows nenajde, zobrazí se chybová zpráva a program se ukonèí.</p>

<p class="src1">PixelFormat := ChoosePixelFormat(h_Dc, @pfd);<span class="kom">// Zkusí najít Pixel Format</span></p>
<p class="src"></p>
<p class="src1">if (PixelFormat = 0) then<span class="kom">// Podaøilo se najít Pixel Format?</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(0, 'Cant Find A Suitable PixelFormat.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukonèí program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Kdy¾ Windows najde odpovídající formát, tak se ho pokusíme nastavit. Pokud pøi pokusu o nastavení nastane chyba, opìt se zobrazí chybové hlá¹ení a program se ukonèí.</p>

<p class="src1">if (not SetPixelFormat(h_Dc, PixelFormat, @pfd)) then<span class="kom">// Podaøilo se nastavit Pixel Format?</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(0, 'Cant set PixelFormat.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukonèí program</span></p>
<p class="src1">exit;</p>
<p class="src1">end;</p>

<p>Pokud byl nastaven Pixel Format správnì, pokusíme se získat Rendering Context. Pokud ho nezískáme, program zobrazí chybovou zprávu a ukonèí se.</p>

<p class="src1">h_Rc := wglCreateContext(h_Dc);<span class="kom">// Podaøilo se vytvoøit Rendering Context?</span></p>
<p class="src"></p>
<p class="src1">if (h_Rc = 0) then</p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(0, 'Cant create a GL rendering context.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukonèí program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pokud nenastaly ¾ádné chyby pøi vytváøení jak Device Context, tak Rendering Context, v¹e co musíme nyní udìlat je aktivovat Rendering Context. Pokud ho nebudeme moci aktivovat, zobrazí se chybová zpráva a program se ukonèí.</p>

<p class="src1">if (not wglMakeCurrent(h_Dc, h_Rc)) then<span class="kom">// Podaøilo se aktivovat Rendering Context?</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(0, 'Cant activate the GL rendering context.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukonèí program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pokud bylo okno vytvoøeno, zobrazíme ho na obrazovce, nastavíme ho, aby bylo v popøedí (vy¹¹í priorita) a pak nastavíme zamìøení na toto okno. Zavoláme funkci ResizeGLScene s parametry odpovídajícími vý¹ce a ¹íøce okna, abychom správnì nastavili perspektivu OpenGL.</p>

<p class="src1">ShowWindow(h_Wnd, SW_SHOW);<span class="kom">// Zobrazení okna</span></p>
<p class="src1">SetForegroundWindow(h_Wnd);<span class="kom">// Do popøedí</span></p>
<p class="src1">SetFOcus(h_Wnd);<span class="kom">// Zamìøí fokus</span></p>
<p class="src1">ReSizeGLScene(width, height);<span class="kom">// Nastavení perspektivy OpenGL scény</span></p>

<p>Koneènì se dostáváme k volání vý¹e definované funkce InitGL, ve které nastavujeme osvìtlení, loading textur a cokoliv jiného, co je potøeba. Ve funkci InitGL mù¾ete vytvoøit svou vlastní kontrolu chyb a vracet true, kdy¾ v¹e probìhne bez problémù, nebo false, pokud nastanou nìjaké problémy. Napøíklad, nastane-li chyba pøi nahrávání textur, vrátíte false, jako znamení, ¾e nìco selhalo a program se ukonèí.</p>

<p class="src1">if not InitGL then<span class="kom">// Inicializace okna</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zavøe okno</span></p>
<p class="src2">MessageBox(0, 'Initialization failed.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukonèí program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pokud jsme se dostali a¾ takhle daleko, mù¾eme konstatovat, ¾e vytvoøení okna probìhlo bez problémù.</p>

<p class="src1">Result := true;<span class="kom">// V¹e probìhlo v poøádku</span></p>
<p class="src0">end;</p>

<p>Nyní se vypoøádáme se systémovými zprávami pro okno. Kdy¾ máme zaregistrovánu na¹i Window Class, mù¾eme podstoupit k èásti kódu, která má na starosti zpracování zpráv.</p>

<p class="src0">function WndProc(hWnd: HWND;<span class="kom">// Handle okna</span></p>
<p class="src1">message: UINT;<span class="kom">// Zpráva pro okno</span></p>
<p class="src1">wParam: WPARAM;<span class="kom">// Doplòkové informace</span></p>
<p class="src1">lParam: LPARAM):<span class="kom">// Doplòkové informace</span></p>
<p class="src1">LRESULT; stdcall;</p>
<p class="src0">begin</p>

<p>Po pøíchodu WM_SYSCOMMAND (systémový pøíkaz) porovnáme wParam s mo¾nými stavy, které mohly nastat. Kdy¾ je wParam WM_SCREENSAVE nebo SC_MONITORPOWER, sna¾í se systém zapnout spoøiè obrazovky nebo pøejít do úsporného re¾imu. Jestli¾e vrátíme 0 zabráníme systému, aby tyto akce provedl.</p>

<p class="src1">if message = WM_SYSCOMMAND then<span class="kom">// Systémový pøíkaz</span></p>
<p class="src1">begin</p>
<p class="src2">case wParam of<span class="kom">// Typ systémového pøíkazu</span></p>
<p class="src3">SC_SCREENSAVE,<span class="kom">// Pokus o zapnutí ¹etøièe obrazovky</span></p>
<p class="src3">SC_MONITORPOWER:<span class="kom">// Pokus o pøechod do úsporného re¾imu?</span></p>
<p class="src3">begin</p>
<p class="src4">result := 0;<span class="kom">// Zabrání obojímu</span></p>
<p class="src4">exit;</p>
<p class="src3">end;</p>
<p class="src2">end;</p>
<p class="src1">end;</p>

<p>Napí¹eme mapu zpráv. Program se bude vìtvit podle promìnné message, která obsahuje jméno zprávy.</p>

<p class="src1">case message of<span class="kom">// Vìtvení podle pøíchozí zprávy</span></p>

<p>Po pøíchodu WM_ACTIVE zkontrolujeme, zda je okno stále aktivní. Pokud bylo minimalizováno, nastavíme hodnotu active na false. Pokud je okno aktivní, promìnná active bude mít hodnotu true.</p>

<p class="src2">WM_ACTIVATE:<span class="kom">// Zmìna aktivity okna</span></p>
<p class="src2">begin</p>
<p class="src3">if (Hiword(wParam) = 0) then<span class="kom">// Zkontroluje zda není minimalizované</span></p>
<p class="src4">active := true<span class="kom">// Program je aktivní</span></p>
<p class="src3">else</p>
<p class="src4">active := false;<span class="kom">// Program není aktivní</span></p>
<p class="src"></p>
<p class="src3">Result := 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">end;</p>

<p>Pøi¹lo-li WM_CLOSE, bylo okno zavøeno. Po¹leme tedy zprávu pro opu¹tìní programu, která pøeru¹í vykonávání hlavního cyklu. Promìnnou done (ve WinMain) nastavíme na true, hlavní smyèka se pøeru¹í a program se ukonèí.</p>

<p class="src2">WM_CLOSE:<span class="kom">// Povel k ukonèení programu</span></p>
<p class="src2">begin</p>
<p class="src3">PostQuitMessage(0);<span class="kom">// Po¹le zprávu o ukonèení</span></p>
<p class="src3">result := 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">end;</p>

<p>Pokud byla stisknuta klávesa, mù¾eme podle wParam zjistit, která z nich to byla. Potom pøiøadíme do odpovídající buòky v poli keys[] true. Díky tomu mù¾eme kdykoli v programu zjistit, která klávesa je právì stisknutá. Tímto zpùsobem lze zkontrolovat i stisk více kláves najednou.</p>

<p class="src2">WM_KEYDOWN:<span class="kom">// Stisk klávesy</span></p>
<p class="src2">begin</p>
<p class="src3">keys[wParam] := TRUE;<span class="kom">// Oznámí to programu</span></p>
<p class="src3">result := 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">end;</p>

<p>Pokud byla naopak klávesa uvolnìna, ulo¾íme do buòky s indexem wParam v poli keys[] hodnotu false. Tímto zpùsobem mù¾eme zjistit, zda je klávesa je¹tì stále stisknuta nebo ji¾ byla uvolnìna. Ka¾dá klávesa je reprezentována jedním èíslem od 0 do 255. Kdy¾ napøíklad stisknu klávesu èíslo 40, hodnota keys[40] bude true, jakmile ji pustím, její hodnota se vrátí opìt na false.</p>

<p class="src2">WM_KEYUP:<span class="kom">// Uvolnìní klávesy</span></p>
<p class="src2">begin</p>
<p class="src3">keys[wParam] := FALSE;<span class="kom">// Oznámí to programu</span></p>
<p class="src3">result := 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">end;</p>

<p>Kdykoliv u¾ivatel zmìní velikost okna, po¹le se WM_SIZE. Pøeèteme LOWORD a HIWORD hodnoty lParam, abychom zjistili, jaká je nová ¹íøka a vý¹ka okna. Pøedáme tyto hodnoty do funkce ReSizeGLScene a tím se perspektiva OpenGL scény zmìní podle nových rozmìrù.</p>

<p class="src2">WM_SIZE:<span class="kom">// Zmìna velikosti okna</span></p>
<p class="src2">begin</p>
<p class="src3">ReSizeGLScene(LOWORD(lParam), HIWORD(lParam));<span class="kom">// LoWord=©íøka, HiWord=Vý¹ka</span></p>
<p class="src3">result := 0;<span class="kom">// Návrat do hlavního cyklu programu</span></p>
<p class="src2">end</p>
<p class="src1">else</p>

<p>Zprávy, o které se nestaráme, budou pøedány funkci DefWindowProc, tak¾e se s nimi za nás vypoøádá operaèní systém.</p>

<p class="src2"><span class="kom">// Pøedání ostatních zpráv systému</span></p>
<p class="src2">begin</p>
<p class="src3">Result := DefWindowProc(hWnd, message, wParam, lParam);</p>
<p class="src2">end;</p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Funkce WinMain je vstupní bod do aplikace, místo, odkud budeme volat funkce na otevøení okna, snímaní zpráv a interakci s u¾ivatelem.</p>

<p class="src0">function WinMain(hInstance: HINST;<span class="kom">// Instance</span></p>
<p class="src1">hPrevInstance: HINST;<span class="kom">// Pøedchozí instance</span></p>
<p class="src1">lpCmdLine: PChar;<span class="kom">// Parametry pøíkazové øádky</span></p>
<p class="src1">nCmdShow: integer):<span class="kom">// Stav zobrazení okna</span></p>
<p class="src1">integer; stdcall;</p>

<p>Deklarujeme dvì lokální promìnné. Msg bude pou¾ita na zji¹»ování, zda se mají zpracovávat nìjaké zprávy. Promìnná done bude mít na poèátku hodnotu false. To znamená, ¾e ná¹ program je¹tì nemá být ukonèen. Dokud se done rovná false, program pobì¾í. Jakmile se zmìní z false na true, program se ukonèí.</p>

<p class="src0">var</p>
<p class="src1">msg: TMsg;<span class="kom">// Struktura zpráv systému</span></p>
<p class="src1">done: Bool;<span class="kom">// Promìnná pro ukonèení programu</span></p>
<p class="src"></p>
<p class="src0">begin</p>
<p class="src1">done := false;</p>

<p>Dal¹í èást kódu je volitelná. Zobrazuje zprávu, která se zeptá u¾ivatele, zda chce spustit program ve fullscreenu. Pokud u¾ivatel vybere mo¾nost Ne, hodnota promìnné fullscreen se zmìní z výchozího true na false, a tím pádem se program spustí v oknì.</p>

<p class="src1"><span class="kom">// Dotaz na u¾ivatele pro fullscreen/okno</span></p>
<p class="src1">if MessageBox(0, 'Would You Like To Run In FullScreen Mode?', 'Start FullScreen', MB_YESNO or MB_ICONQUESTION) = IDNO then</p>
<p class="src2">FullScreen := false<span class="kom">// Bìh v oknì</span></p>
<p class="src1">else</p>
<p class="src2">FullScreen := true;<span class="kom">// Fullscreen</span></p>

<p>Vytvoøíme OpenGL okno. Zadáme text titulku, ¹íøku, vý¹ku, barevnou hloubku a true (fullscreen), nebo false (okno) jako parametry do funkce CreateGLWindow. Tak a je to! Je to pìknì lehké, ¾e? Pokud se okno nepodaøí z nìjakého dùvodu vytvoøit, bude vráceno false a program se okam¾itì ukonèí.</p>

<p class="src1">if not CreateGLWindow('NeHes OpenGL Framework', 640, 480, 16, FullScreen) then<span class="kom">// Vytvoøení OpenGL okna</span></p>
<p class="src1">begin</p>
<p class="src2">Result := 0;<span class="kom">// Konec programu pøi chybì</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Smyèka se opakuje tak dlouho, dokud se done rovná false.</p>

<p class="src1">while not done do<span class="kom">// Hlavní cyklus programu</span></p>
<p class="src1">begin</p>

<p>První vìc, kterou udìláme, je zkontrolování zpráv pro okno. Pomocí funkce PeekMessage mù¾eme zjistit zda nìjaké zprávy èekají na zpracování bez toho, aby byl program pozastaven. Mnoho programù pou¾ívá funkci GetMessage. Pracuje to skvìle, ale program nic nedìlá, kdy¾ nedostává ¾ádné zprávy.</p>

<p class="src2">if (PeekMessage(msg, 0, 0, 0, PM_REMOVE)) then<span class="kom">// Pøi¹la zpráva?</span></p>
<p class="src2">begin</p>

<p>Zkontrolujeme, zda jsme neobdr¾eli zprávu pro ukonèení programu. Pokud je aktuální zpráva WM_QUIT, která je zpùsobena voláním funkce PostQuitMessage(0), nastavíme done na true, èím¾ pøeru¹íme hlavní cyklus a ukonèíme program.</p>

<p class="src3">if msg.message = WM_QUIT then<span class="kom">// Obdr¾eli jsme zprávu pro ukonèení?</span></p>
<p class="src4">done := true<span class="kom">// Konec programu</span></p>
<p class="src3">else</p>
<p class="src3">begin</p>

<p>Kdy¾ zpráva nevyzývá k ukonèení programu, tak pøedáme funkcím TranslateMessage a DispatchMessage referenci na tuto zprávu, aby ji funkce WndProc nebo Windows zpracovaly.</p>

<p class="src4">TranslateMessage(msg);<span class="kom">// Pøelo¾í zprávu</span></p>
<p class="src4">DispatchMessage(msg);<span class="kom">// Ode¹le zprávu</span></p>
<p class="src3">end;</p>
<p class="src2">end</p>
<p class="src2">else<span class="kom">// Pokud nedo¹la ¾ádná zpráva</span></p>
<p class="src2">begin</p>

<p>Pokud zde nebudou ji¾ ¾ádné zprávy, pøekreslíme OpenGL scénu. Následující øádek kontroluje, zda je okno aktivní. Na¹e scéna je vyrenderována a je zkontrolována vrácená hodnota. Kdy¾ funkce DrawGLScene vrátí false nebo je stisknut ESC, hodnota promìnné done je nastavena na true, co¾ ukonèí bìh programu.</p>

<p class="src3"><span class="kom">// Je program aktivní, ale nelze kreslit? Byl stisknut ESC?</span></p>
<p class="src3">if (active and not(DrawGLScene) or keys[VK_ESCAPE]) then</p>
<p class="src4">done := true<span class="kom">// Ukonèíme program</span></p>
<p class="src3">else<span class="kom">// Pøekreslení scény</span></p>

<p>Kdy¾ v¹echno probìhlo bez problémù, prohodíme obsah bufferù (s pou¾itím dvou bufferù pøedejdeme blikání obrazu pøi pøekreslování). Pou¾itím dvojitého bufferingu v¹echno vykreslujeme do obrazovky v pamìti, kterou nevidíme. Jakmile vymìníme obsah bufferù, to co je na obrazovce se pøesune do této skryté obrazovky a to, co je ve skryté obrazovce se pøenese na monitor. Díky tomu nevidíme probliknutí.</p>

<p class="src4">SwapBuffers(h_Dc);<span class="kom">// Prohození bufferù (Double Buffering)</span></p>

<p>Pøi stisku klávesy F1 pøepneme z fullscreenu do okna a naopak.</p>

<p class="src3">if keys[VK_F1] then<span class="kom">// Byla stisknuta klávesa F1?</span></p>
<p class="src3">begin</p>
<p class="src4">Keys[VK_F1] := false;<span class="kom">// Oznaè ji jako nestisknutou</span></p>
<p class="src4">KillGLWindow;<span class="kom">// Zru¹í okno</span></p>
<p class="src4">FullScreen := not FullScreen;<span class="kom">// Negace fullscreen</span></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Znovuvytvoøení okna</span></p>
<p class="src4">if not CreateGLWindow('NeHes OpenGL Framework', 640, 480, 16, fullscreen) then</p>
<p class="src5">Result := 0;<span class="kom">// Konec programu pokud nebylo vytvoøeno</span></p>
<p class="src3">end;</p>
<p class="src2">end;</p>
<p class="src1">end;<span class="kom">// Konec smyèky while</span></p>

<p>Pokud se promìnná done rovná true, hlavní cyklus se pøeru¹í. Zavøeme okno a opustíme program.</p>

<p class="src1">KillGLWindow;<span class="kom">// Zavøe okno</span></p>
<p class="src1">result := msg.wParam;<span class="kom">// Ukonèení programu</span></p>
<p class="src0">end;</p>

<p>Celý program se skládá pouze z volání funkce WinMain, která se u¾ postará o v¹e ostatní.</p>

<p class="src0">begin</p>
<p class="src1">WinMain(hInstance, hPrevInst, CmdLine, CmdShow);<span class="kom">// Start programu</span></p>
<p class="src0">end.</p>

<p>Pokud se vám text zdál povìdomý, máte pravdu. V podstatì jde o Wessanùv pøeklad lekce 1, èím¾ mu tímto dìkuji, jen kód je v Delphi. Pokud budete mít jakékoli dotazy nebo problémy, neváhejte mì kontaktovat.</p>

<p class="autor">napsal: Michal Tuèek <?VypisEmail('michal_praha@seznam.cz');?>, 15.08.2004</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li>Najdete v sekci <?OdkazWeb('download', 'download');?>...</li>
</ul>

<?
include 'p_end.php';
?>
