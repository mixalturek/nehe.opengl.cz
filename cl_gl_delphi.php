<?
$g_title = 'CZ NeHe OpenGL - Vytvo�en� OpenGL okna v Delphi';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Vytvo�en� OpenGL okna v Delphi</h1>

<p class="nadpis_clanku">Tento �l�nek popisuje vytvo�en� OpenGL okna pod opera�n�m syst�mem MS Windows ve v�vojov�m prost�ed� Borland Delphi. J� osobn� pou��v�m Delphi verze 7, ale ani v ni���ch verz�ch by nem�l b�t probl�m vytvo�en� k�d zkompilovat a spustit. Z v�t�� ��sti se jedn� o p�epis prvn�ho NeHe Tutori�lu z jazyka C/C++ do Pascalu. Tak�e sm�le do toho...</p>

<p>Za�neme vytvo�en�m nov�ho projektu. V nab�dce File - New  vybereme konzolovou aplikaci (Console Application). Postup se m��e v r�zn�ch verz�ch Delphi m�rn� li�it. Ve skute�nosti konzolovou aplikaci vytv��et nebudeme, ale vygenerovan� k�d je nejbl�e tomu, co pot�ebujeme.</p>

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

<p>Odstran�me zbytky k�du, kter� by n�m p�ek�ely a dostaneme:</p>

<p class="src0">program Project1;</p>
<p class="src"></p>
<p class="src0">begin</p>
<p class="src"></p>
<p class="src0">end.</p>

<p>Z�klad tedy m�me vytvo�en. Na za��tek p�id�me jednotky, kter� budeme d�le v k�du pou��vat.</p>

<p class="src0">uses</p>
<p class="src1">Windows,</p>
<p class="src1">Messages,</p>
<p class="src1">OpenGL;</p>

<p>D�le deklarujeme glob�ln� prom�nn�. Koment��e, mysl�m, hovo�� za v�e, tak�e se zastav�m jen u prvn�ch dvou. Ka�d� OpenGL program je spojen s Rendering Contextem. Rendering Context ��k�, kter� spojen� vol� OpenGL, aby se spojilo s Device Context (kontext za��zen�). N�m sta�� v�d�t, �e OpenGL Rendering Context je definov�n jako HGLRC. Aby program mohl kreslit do okna, pot�ebujeme vytvo�it Device Context. Ve Windows je Device Context definov�n jako HDC. Device Context napoj� okno na GDI (grafick� rozhran�).</p>

<p class="src0">var</p>
<p class="src1">h_Rc: HGLRC;<span class="kom">// Trval� Rendering Context</span></p>
<p class="src1">h_Dc: HDC;<span class="kom">// Priv�tn� GDI Device Context</span></p>
<p class="src1">h_Wnd: HWND;<span class="kom">// Obsahuje Handle na�eho okna</span></p>
<p class="src1">keys: array [0..255] of BOOL;<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src1">Active: bool = true;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src1">FullScreen: bool = true;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>N�sleduj�c� procedura se vol� v�dy, kdy� u�ivatel m�n� velikost okna. I kdy� nejste schopni zm�nit velikost okna (nap��klad ve fullscreenu), bude tato funkce vol�na alespo� jednou, aby nastavila perspektivn� pohled p�i spu�t�n� programu. Velikost OpenGL sc�ny se bude m�nit v z�vislosti na ���ce a v��ce okna, ve kter�m je zobrazena.</p>

<p class="src0">procedure ReSizeGLScene(Width: GLsizei; Height: GLsizei);<span class="kom">// Zm�na velikosti a inicializace OpenGL okna</span></p>
<p class="src0">begin</p>
<p class="src1">if Height = 0 then<span class="kom">// Zabezpe�en� proti d�len� nulou</span></p>
<p class="src2">Height := 1;<span class="kom">// Nastav� v��ku na jedna</span></p>
<p class="src"></p>
<p class="src1">glViewport(0, 0, Width, Height);<span class="kom">// Resetuje aktu�ln� nastaven�</span></p>

<p>Nastav�me obraz na perspektivn� projekci, to znamen�, �e vzd�len�j�� objekty budou, stejn� jako v re�ln�m sv�t�, men��. P��kaz glMatrixMode(GL_PROJECTION) ovlivn� formu obrazu, d�ky n� budeme moci n�sledn� definovat, jak v�razn� bude perspektiva. Vytvo��me realisticky vypadaj�c� sc�nu. Funkce glLoadIdentity resetuje matici, to znamen�, �e ji nastav� do v�choz�ho stavu. Perspektiva je vypo��t�na s �hlem pohledu 45 stup�� a je zalo�ena na v��ce a ���ce okna. ��slo 1.0 je po��te�n� a 100.0 koncov� bod, kter� ��k� jak hluboko do obrazovky m��eme kreslit. glMatrixMode(GL_MODELVIEW) oznamuje, �e forma pohledu bude znovu zm�n�na.</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvol� projek�n� matici</span></p>
<p class="src1">glLoadIdentity;<span class="kom">// Reset matice</span></p>
<p class="src1">gluPerspective(45.0, Width/Height, 1.0, 100.0);<span class="kom">// V�po�et perspektivy</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvol� matici Modelview</span></p>
<p class="src1">glLoadIdentity;<span class="kom">// Reset matice</span></p>
<p class="src0">end;</p>

<p>Nastav�me v�e pot�ebn� pro OpenGL. Definujeme �ern� pozad�, zapneme depth buffer, aktivujeme smooth shading (vyhlazen� st�nov�n�), atd. Tato funkce se vol� a� po vytvo�en� okna, proto�e mus� b�t dostupn� rendering kontext.</p>

<p class="src0">function InitGL: bool;<span class="kom">// V�echno nastaven� OpenGL</span></p>
<p class="src0">begin</p>

<p>N�sleduj�c� ��dek povol� st�nov�n�, aby se barvy na polygonech p�kn� prom�chaly.</p>

<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// M�ch�n� barev na polygonech</span></p>

<p>Nastav�me barvu pozad� pr�zdn� obrazovky. Rozsah barev se ur�uje ve stupnici od nuly do jedn�. 0.0 je nejtmav�� a 1.0 je nejsv�tlej��. Prvn� parametr ve funkci glClearColor je intenzita �erven� barvy, druh� zelen� a t�et� modr�. ��m bli��� je hodnota barvy 1.0, t�m sv�tlej�� slo�ka barvy bude. Posledn� parametr je hodnota alpha (pr�hlednost). Kdy� budeme �istit obrazovku, tak se o pr�hlednost starat nemus�me. Nyn� ji nech�me na 0.5.</p>

<p class="src1">glClearColor(0.0, 0.0, 0.0, 0.5);<span class="kom">// �ern� pozad�</span></p>

<p>N�sleduj�c� t�i ��dky ovliv�uj� depth buffer. Depth buffer si m��ete p�edstavit jako vrstvy/hladiny obrazovky. Obsahuje informace, o tom jak hluboko jsou zobrazovan� objekty.</p>

<p class="src1">glClearDepth(1.0);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� hloubkov� testov�n�</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>

<p>D�le ozn�m�me, �e chceme pou��t nejlep�� korekce perspektivy. Jen nepatrn� to sn�� v�kon, ale zlep�� se vzhled cel� sc�ny.</p>

<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>

<p>Nakonec vr�t�me true. Kdy� budeme cht�t zjistit, jestli inicializace prob�hla bez probl�m�, m��eme zkontrolovat, zda funkce vr�tila hodnotu true nebo false. M��ete p�idat vlastn� k�d, kter� vr�t� false, kdy� se inicializace nezda�� - nap�. p�i ne�sp�n�m loadingu textur.</p>

<p class="src1">Result := true;<span class="kom">// Inicializace prob�hla v po��dku</span></p>
<p class="src0">end;</p>

<p>Do t�to funkce um�st�me v�echno vykreslov�n�. Jedin� co nyn� ud�l�me, je smaz�n� obrazovky na barvu, pro kterou jsme se rozhodli, tak� vyma�eme obsah hloubkov�ho bufferu a resetujeme sc�nu. Zat�m nebudeme nic kreslit. P��kaz Result := true n�m ��k�, �e p�i kreslen� nenastaly ��dn� probl�my.</p>

<p class="src0">function DrawGLScene: bool;<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">begin</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT or GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity;<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">Result := true;<span class="kom">// Vykreslen� prob�hlo v po��dku</span></p>
<p class="src0">end;</p>

<p>N�sleduj�c� ��st k�du je vol�na t�sn� p�ed koncem programu. �kolem funkce KillGLWindow je uvoln�n� renderovac�ho kontextu, kontextu za��zen� a handle okna. P�idal jsem zde nezbytn� kontrolov�n� chyb. Kdy� nen� program schopen n�co uvolnit, ozn�m� chybu, kter� ��k� co selhalo. Usnadn� tak slo�it� hled�n� probl�m�.</p>

<p class="src0">procedure KillGLWindow;<span class="kom">// Zav�r�n� okna</span></p>
<p class="src0">begin</p>

<p>Zjist�me, zda je program ve fullscreenu. Pokud ano, tak ho p�epneme zp�t do syst�mu. Mohli bychom vypnout okno p�ed opu�t�n�m fullscreenu, ale na n�kter�ch grafick�ch kart�ch t�m zp�sob�me probl�my a syst�m by se mohl zhroutit.</p>

<p class="src1">if FullScreen then<span class="kom">// Jsme ve fullscreenu?</span></p>
<p class="src1">begin</p>

<p>K n�vratu do p�vodn�ho nastaven� syst�mu pou��v�me funkci ChangeDisplaySettings(devmode(nil^),0). Jako prvn� parametr zad�me devmode(nil^) a jako druh� 0 - pou�ijeme hodnoty ulo�en� v registrech Windows (p�vodn� rozli�en�, barevnou hloubku, obnovovac� frekvenci, atd.). Po p�epnut� zviditeln�me kurzor.</p>

<p class="src2">ChangeDisplaySettings(devmode(nil^), 0);<span class="kom">// P�epnut� do syst�mu</span></p>
<p class="src2">showcursor(true);<span class="kom">// Zobraz� kurzor my�i</span></p>
<p class="src1">end;</p>

<p>Zkontrolujeme, zda m�me renderovac� kontext. Kdy� ne, program p�esko�� ��st k�du pod n�m, kter� kontroluje, zda m�me kontext za��zen�.</p>

<p class="src1">if h_rc <> 0 then<span class="kom">// M�me rendering kontext?</span></p>
<p class="src1">begin</p>

<p>Zjist�me, zda m��eme odpojit h_RC od h_DC.</p>

<p class="src2">if (not wglMakeCurrent(h_Dc, 0)) then<span class="kom">// Jsme schopni odd�lit kontexty?</span></p>

<p>Pokud nejsme schopni uvolnit DC a RC, zobraz�me zpr�vu, �e DC a RC nelze uvolnit. Nula v parametru znamen�, �e informa�n� okno nem� ��dn�ho rodi�e. Text ihned za 0 je text, kter� se vyp�e do zpr�vy. Dal�� parametr definuje text li�ty. Parametr MB_OK znamen�, �e chceme m�t na chybov� zpr�v� jen jedno tla��tko s n�pisem OK. MB_ICONERROR zobraz� ikonu.</p>

<p class="src3">MessageBox(0, 'Release of DC and RC failed.', 'Shutdown Error', MB_OK or MB_ICONERROR);</p>

<p>Zkus�me vymazat Rendering Context. Pokud se pokus nezda��, op�t se zobraz� chybov� zpr�va. Nakonec nastav�me h_RC a 0.</p>

<p class="src2">if (not wglDeleteContext(h_Rc)) then<span class="kom">// Jsme schopni smazat RC?</span></p>
<p class="src2">begin</p>
<p class="src3">MessageBox(0, 'Release of Rendering Context failed.', 'Shutdown Error', MB_OK or MB_ICONERROR);</p>
<p class="src3">h_Rc := 0;<span class="kom">// Nastav� hRC na 0</span></p>
<p class="src2">end;</p>
<p class="src1">end;</p>

<p>Zjist�me, zda m� program kontext za��zen�. Kdy� ano odpoj�me ho. Pokud se odpojen� nezda��, zobraz� se chybov� zpr�va a h_DC bude nastaven na 0.</p>

<p class="src1">if (h_Dc = 1) and (releaseDC(h_Wnd,h_Dc) <> 0) then<span class="kom">// Jsme schopni uvolnit DC</span></p>
<p class="src1">begin</p>
<p class="src2">MessageBox(0, 'Release of Device Context failed.', 'Shutdown Error', MB_OK or MB_ICONERROR);</p>
<p class="src2">h_Dc := 0;<span class="kom">// Nastav� hDC na 0</span></p>
<p class="src1">end;</p>

<p>Nyn� zjist�me, zda m�me handle okna a pokud ano, pokus�me se odstranit okno pou�it�m funkce DestroyWindow(h_Wnd). Pokud se pokus nezda��, zobraz� se chybov� zpr�va a h_Wnd bude nastaveno na 0.</p>

<p class="src1">if (h_Wnd <> 0) and (not destroywindow(h_Wnd)) then<span class="kom">// Jsme schopni odstranit okno?</span></p>
<p class="src1">begin</p>
<p class="src2">MessageBox(0, 'Could not release hWnd.', 'Shutdown Error', MB_OK or MB_ICONERROR);</p>
<p class="src2">h_Wnd := 0;<span class="kom">// Nastav� hWnd na 0</span></p>
<p class="src1">end;</p>

<p>Odregistrov�n�m t��dy okna ofici�ln� uzav�eme okno a p�edejdeme zobrazen� chybov� zpr�vy &quot;Windows Class already registered&quot; p�i op�tovn�m spu�t�n� programu.</p>

<p class="src1">if (not UnregisterClass('OpenGL', hInstance)) then<span class="kom">// Jsme schopni odregistrovat t��du okna?</span></p>
<p class="src1">begin</p>
<p class="src2">MessageBox(0, 'Could Not Unregister Class.', 'SHUTDOWN ERROR', MB_OK or MB_ICONINFORMATION);</p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Dal�� ��st k�du m� na starosti vytvo�en� OpenGL okna. Jak si m��ete v�imnout funkce vrac� boolean a p�ij�m� 5 parametr� v po�ad�: n�zev okna, ���ka okna, v��ka okna, barevn� hloubka, fullscreen (pokud je parametr true program pob�� ve fullscreenu, pokud bude false program pob�� v okn�). Vrac�me boolean, abychom v�d�li, zda bylo okno �sp�n� vytvo�eno.</p>

<p class="src0">function CreateGlWindow(title:Pchar; width, height, bits:integer; FullScreenflag:bool) : boolean stdcall;</p>

<p>Za chv�li po��d�me Windows, aby pro n�s na�el pixel format, kter� odpov�d� tomu, kter� chceme. Toto ��slo ulo��me do prom�nn� PixelFormat.</p>

<p class="src0">var</p>
<p class="src1">Pixelformat: GLuint;<span class="kom">// Ukl�d� form�t pixel�</span></p>

<p>Wc bude pou�ijeme k uchov�n� informac� o struktu�e Windows Class. Zm�nou hodnot jednotliv�ch polo�ek lze ovlivnit vzhled a chov�n� okna. P�ed vytvo�en�m samotn�ho okna se mus� zaregistrovat n�jak� struktura pro okno.</p>

<p class="src1">wc: TWndclass;<span class="kom">// Struktura Windows Class</span></p>

<p>DwExStyle a dwStyle ponesou informaci o norm�ln�ch a roz���en�ch informac�ch o oknu.</p>

<p class="src1">dwExStyle: dword;<span class="kom">// Roz���en� styl okna</span></p>
<p class="src1">dwStyle: dword;<span class="kom">// Styl okna</span></p>
<p class="src1">pfd: pixelformatdescriptor;<span class="kom">// Nastaven� form�tu pixel�</span></p>
<p class="src1">dmScreenSettings: Devmode;<span class="kom">// M�d za��zen�</span></p>
<p class="src1">h_Instance: hinst;<span class="kom">// Instance okna</span></p>
<p class="src1">WindowRect: TRect;<span class="kom">// Obd�ln�k okna</span></p>
<p class="src"></p>
<p class="src0">begin
<p class="src1">WindowRect.Left := 0;<span class="kom">// Nastav� lev� okraj na nulu</span></p>
<p class="src1">WindowRect.Top := 0;<span class="kom">// Nastav� horn� okraj na nulu</span></p>
<p class="src1">WindowRect.Right := width;<span class="kom">// Nastav� prav� okraj na zadanou hodnotu</span></p>
<p class="src1">WindowRect.Bottom := height;<span class="kom">// Nastav� spodn� okraj na zadanou hodnotu</span></p>

<p>Z�sk�me instanci pro okno.</p>

<p class="src1">h_instance := GetModuleHandle(nil);<span class="kom">// Z�sk� instanci okna</span></p>

<p>P�i�ad�me glob�ln� prom�nn� fullscreen, hodnotu fullscreenflag. Tak�e pokud na�e okno pob�� ve fullscreenu, prom�nn� fullscreen se bude rovnat true.</p>

<p class="src1">FullScreen := FullScreenflag;<span class="kom">// Nastav� prom�nnou fullscreen na spr�vnou hodnotu</span></p>

<p>Definujeme Window Class. CS_HREDRAW a CS_VREDRAW donut� na�e okno, aby se p�ekreslilo, kdykoliv se zm�n� jeho velikost. CS_OWNDC vytvo�� priv�tn� kontext za��zen�. To znamen�, �e nen� sd�len s ostatn�mi aplikacemi. WndProc je procedura okna, kter� sleduje p��choz� zpr�vy pro program. ��dn� extra data pro okno nepou��v�me, tak�e do dal��ch dvou polo�ek p�i�ad�me nulu. Nastav�me instanci a hIcon, co� je ikona, kterou pou��v� n� program a pro kurzor my�i pou��v�me standardn� �ipku. Barva pozad� n�s nemus� zaj�mat (to za��d�me v OpenGL). Nechceme, aby okno m�lo menu, tak�e i tuto hodnotu nastav�me na nil. Jm�no t��dy m��e b�t libovoln�.</p>

<p class="src1">with wc do</p>
<p class="src1">begin</p>
<p class="src2">style := CS_HREDRAW or CS_VREDRAW or CS_OWNDC;<span class="kom">// P�ekreslen� p�i zm�n� velikosti a vlastn� DC</span></p>
<p class="src2">lpfnWndProc := @WndProc;<span class="kom">// Definuje proceduru okna</span></p>
<p class="src2">cbClsExtra := 0;<span class="kom">// ��dn� extra data</span></p>
<p class="src2">cbWndExtra := 0;<span class="kom">// ��dn� extra data</span></p>
<p class="src2">instance := h_Instance;<span class="kom">// Instance</span></p>
<p class="src2">hIcon := LoadIcon(0, IDI_WINLOGO);<span class="kom">// Standardn� ikona</span></p>
<p class="src2">hCursor := LoadCursor(0, IDC_ARROW);<span class="kom">// Standardn� kurzor my�i</span></p>
<p class="src2">hbrBackground := 0;<span class="kom">// Pozad� nen� nutn�</span></p>
<p class="src2">lpszMenuName := nil;<span class="kom">// Nechceme menu</span></p>
<p class="src2">lpszClassName := 'OpenGl';<span class="kom">// Jm�no t��dy okna</span></p>
<p class="src1">end;</p>

<p>Zaregistrujeme pr�v� definovanou t��du okna. Kdy� nastane chyba, zobraz� se chybov� hl�en�. Zm��knut�m tla��tka OK se program ukon��.</p>

<p class="src1">if RegisterClass(wc) = 0 then<span class="kom">// Registruje t��du okna</span></p>
<p class="src1">begin</p>
<p class="src2">MessageBox(0, 'Failed To Register The Window Class.', 'Error', MB_OK or MB_ICONERROR);</p>
<p class="src2">Result := false;<span class="kom">// P�i chyb� vr�t� false</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Nyn� si zjist�me, zda m� program b�et ve fullscreenu nebo v okn�.</p>

<p class="src1">if FullScreen then<span class="kom">// Budeme ve fullscreenu?</span></p>
<p class="src1">begin</p>

<p>S p�ep�n�n�m do fullscreenu m�vaj� lid� mnoho probl�m�. Je zde p�r d�le�it�ch v�c�, na kter� si mus�te d�vat pozor. Ujist�te se, �e ���ka a v��ka, kterou pou��v�te ve fullscreenu, je toto�n� s tou, kterou chcete pou��t v okn�. Dal�� v�c je hodn� d�le�it�. Mus�te p�epnout do fullscreenu p�edt�m, ne� vytvo��te okno. V tomto k�du se o rovnost v��ky a ���ky nemus�te starat, proto�e velikost ve fullscreenu i v okn� budou stejn�.</p>

<p class="src2">ZeroMemory(@dmScreenSettings, sizeof(dmScreenSettings));<span class="kom">// Vynulov�n� pam�ti</span></p>
<p class="src"></p>
<p class="src2">with dmScreensettings do</p>
<p class="src2">begin</p>
<p class="src3">dmSize := sizeof(dmScreenSettings);<span class="kom">// Velikost struktury Devmode</span></p>
<p class="src3">dmPelsWidth := width;<span class="kom">// ���ka okna</span></p>
<p class="src3">dmPelsHeight := height;<span class="kom">// V��ka okna</span></p>
<p class="src3">dmBitsPerPel := bits;<span class="kom">// Barevn� hloubka</span></p>
<p class="src3">dmFields := DM_BITSPERPEL or DM_PELSWIDTH or DM_PELSHEIGHT;</p>
<p class="src2">end;</p>

<p>Funkce ChangeDisplaySettings se pokus� p�epnout do m�du, kter� je ulo�en v dmScreenSettings. Pou�iji parametr CDS_FULLSCREEN, proto�e odstran� pracovn� li�tu ve spodn� ��sti obrazovky a nep�esune nebo nezm�n� velikost okna p�i p�ep�n�n� z fullscreenu do syst�mu nebo naopak.</p>

<p class="src2"><span class="kom">// Pokus� se pou��t pr�v� definovan� nastaven�</span></p>
<p class="src2">if (ChangeDisplaySettings(dmScreenSettings, CDS_FULLSCREEN)) <> DISP_CHANGE_SUCCESSFUL THEN</p>
<p class="src2">begin</p>

<p>Pokud pr�v� vytvo�en� fullscreen m�d neexistuje, zobraz� se chybov� zpr�va s nab�dkou spu�t�n� v okn� nebo opu�t�n� programu.</p>

<p class="src3"><span class="kom">// Nejde-li fullscreen, m��e u�ivatel spustit program v okn� nebo ho opustit</span></p>
<p class="src3">if MessageBox(0, 'This FullScreen Mode Is Not Supported. Use Windowed Mode Instead?', 'NeHe GL', MB_YESNO or MB_ICONEXCLAMATION) = IDYES then</p>

<p>Kdy� se u�ivatel rozhodne pro b�h v okn�, do prom�nn� fullscreen se p�i�ad� false a program pokra�uje d�le.</p>

<p class="src4">FullScreen := false<span class="kom">// B�h v okn�</span></p>
<p class="src3">else</p>
<p class="src3">begin</p>

<p>Pokud se u�ivatel rozhodl pro ukon�en� programu, zobraz� se u�ivateli zpr�va, �e program bude ukon�en. Bude vr�cena hodnota false, kter� na�emu programu ��k�, �e pokus o vytvo�en� okna nebyl �sp�n� a potom se program ukon��.</p>

<p class="src4"><span class="kom">// Zobraz� u�ivateli zpr�vu, �e program bude ukon�en</span></p>
<p class="src4">MessageBox(0, 'Program Will Now Close.', 'Error', MB_OK or MB_ICONERROR);</p>
<p class="src4">Result := false;<span class="kom">// Vr�t� FALSE</span></p>
<p class="src4">exit;</p>
<p class="src3">end;</p>
<p class="src2">end;</p>
<p class="src1">end;</p>

<p>Proto�e pokus o p�epnut� do fullscreenu m��e selhat nebo se u�ivatel m��e rozhodnout pro b�h programu v okn�, zkontrolujeme je�t� jednou, zda je prom�nn� fullscreen true nebo false. A� pot� nastav�me typ obrazu.</p>

<p class="src1">if FullScreen then<span class="kom">// Jsme st�le ve fullscreenu?</span></p>
<p class="src1">begin

<p>Pokud jsme st�le ve fullscreenu nastav�me roz���en� styl na WS_EX_APPWINDOW, co� donut� okno, aby p�ekrylo pracovn� li�tu. Styl okna ur��me na WS_POPUP. Tento typ okna nem� ��dn� okraje, co� je pro fullscreen v�hodn�. Nakonec vypneme kurzor my�i. Pokud v� program nen� interaktivn�, je v�t�inou vhodn�j�� ve fullscreenu kurzor vypnout.</p>

<p class="src2">dwExStyle := WS_EX_APPWINDOW;<span class="kom">// Roz���en� styl okna</span></p>
<p class="src2">dwStyle := WS_POPUP or WS_CLIPSIBLINGS or WS_CLIPCHILDREN;<span class="kom">// Styl okna</span></p>
<p class="src2">Showcursor(false);<span class="kom">// Skryje kurzor</span></p>
<p class="src1">end</p>
<p class="src1">else</p>
<p class="src1">begin</p>

<p>Pokud m�sto fullscreenu pou��v�me b�h v okn�, nastav�me roz���en� styl na WS_EX_WINDOWEDGE. To dod� oknu trochu 3D vzhledu. Styl nastav�me na WS_OVERLAPPEDWINDOW m�sto na WS_POPUP. WS_OVERLAPPEDWINDOW vytvo�� okno s li�tou, okraji, tla��tky pro minimalizaci a maximalizaci. Budeme moci m�nit velikost.</p>

<p class="src2">dwExStyle := WS_EX_APPWINDOW or WS_EX_WINDOWEDGE;<span class="kom">// Roz���en� styl okna</span></p>
<p class="src2">dwStyle := WS_OVERLAPPEDWINDOW or WS_CLIPSIBLINGS or WS_CLIPCHILDREN;<span class="kom">// Styl okna</span></p>
<p class="src1">end;</p>

<p>P�izp�sob�me okno podle stylu, kter� jsme vytvo�ili. P�izp�soben� ud�l� okno v takov�m rozli�en�, jak� po�adujeme. Norm�ln� by okraje p�ekr�valy ��st okna. S pou�it�m p��kazu AdjustWindowRectEx ��dn� ��st OpenGL sc�ny nebude p�ekryta okraji, m�sto toho bude okno ud�l�no o m�lo v�t��, aby se do n�j ve�ly v�echny pixely tvo��c� okraj okna. Ve fullscreenu tato funkce nem� ��dn� efekt.</p>

<p class="src1">AdjustWindowRectEx(WindowRect, dwStyle, false, dwExStyle);<span class="kom">// P�izp�soben� velikosti okna</span></p>

<p>Vytvo��me okno a zkontrolujeme, zda bylo vytvo�eno spr�vn�. Pou�ijeme funkci CreateWindowEx se v�emi parametry, kter� vy�aduje. Roz���en� styl, kter� jsme se rozhodli pou��t, jm�no t��dy (mus� b�t stejn� jako to, kter� jste pou�ili, kdy� jste registrovali Window Class), titulek okna, styl okna, lev� horn� pozice okna (0,0 je nejjist�j��), ���ka a v��ka. Nechceme m�t rodi�ovsk� okno ani menu, tak�e nastav�me tyto parametry na 0. Zad�me instanci okna a kone�n� p�i�ad�me nil na m�sto posledn�ho parametru.</p>

<p class="src1"><span class="kom">// Vytvo�en� okna</span></p>
<p class="src1">H_wnd := CreateWindowEx(dwExStyle,<span class="kom">// Roz���en� styl</span></p>
<p class="src2">'OpenGl',<span class="kom">// Jm�no t��dy</span></p>
<p class="src2">Title,<span class="kom">// Titulek</span></p>
<p class="src2">dwStyle,<span class="kom">// Definovan� styl</span></p>
<p class="src2">0, 0,<span class="kom">// Pozice</span></p>
<p class="src2">WindowRect.Right-WindowRect.Left,<span class="kom">// V�po�et ���ky</span></p>
<p class="src2">WindowRect.Bottom-WindowRect.Top,<span class="kom">// V�po�et v��ky</span></p>
<p class="src2">0,<span class="kom">// ��dn� rodi�ovsk� okno</span></p>
<p class="src2">0,<span class="kom">// Bez menu</span></p>
<p class="src2">hinstance,<span class="kom">// Instance</span></p>
<p class="src2">nil);<span class="kom">// Nep�edat nic do WM_CREATE</span></p>

<p>D�le zkontrolujeme, zda bylo vytvo�eno. Pokud bylo, h_Wnd obsahuje handle tohoto okna. Kdy� se vytvo�en� okna nepovede, k�d zobraz� chybovou zpr�vu a program se ukon��.</p>

<p class="src1">if h_Wnd = 0 then<span class="kom">// Pokud se okno nepoda�ilo vytvo�it</span></p>
<p class="src1">begin</p>
<p class="src2">KillGlWindow;<span class="kom">// Zru�� okno</span></p>
<p class="src2">MessageBox(0, 'Window creation error.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Vr�t� chybu</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Vybereme Pixel Format, kter� podporuje OpenGL, d�le zvol�me double buffering a RGBA (�erven�, zelen�, modr�, pr�hlednost). Pokus�me se naj�t form�t, kter� odpov�d� tomu, pro kter� jsme se rozhodli (16 bit�, 24 bit�, 32 bit�). Nakonec nastav�me Z-Buffer. Ostatn� parametry se nepou��vaj� nebo pro n�s nejsou d�le�it�.</p>

<p class="src1">with pfd do<span class="kom">// Ozn�m�me Windows jak chceme v�e nastavit</span></p>
<p class="src1">begin</p>
<p class="src2">nSize := SizeOf(PIXELFORMATDESCRIPTOR);<span class="kom">// Velikost struktury</span></p>
<p class="src2">nVersion := 1;<span class="kom">// ��slo verze</span></p>
<p class="src2">dwFlags := PFD_DRAW_TO_WINDOW<span class="kom">// Podpora okna</span></p>
<p class="src2">or PFD_SUPPORT_OPENGL<span class="kom">// Podpora OpenGL</span></p>
<p class="src2">or PFD_DOUBLEBUFFER;<span class="kom">// Podpora Double Bufferingu</span></p>
<p class="src2">iPixelType := PFD_TYPE_RGBA;<span class="kom">// RGBA Format</span></p>
<p class="src2">cColorBits := bits;<span class="kom">// Zvol� barevnou hloubku</span></p>
<p class="src2">cRedBits := 0;<span class="kom">// Bity barev ignorov�ny</span></p>
<p class="src2">cRedShift := 0;</p>
<p class="src2">cGreenBits := 0;</p>
<p class="src2">cBlueBits := 0;</p>
<p class="src2">cBlueShift := 0;</p>
<p class="src2">cAlphaBits := 0;<span class="kom">// ��dn� alpha buffer</span></p>
<p class="src2">cAlphaShift := 0;<span class="kom">// Ignorov�n Shift bit</span></p>
<p class="src2">cAccumBits := 0;<span class="kom">// ��dn� akumula�n� buffer</span></p>
<p class="src2">cAccumRedBits := 0;<span class="kom">// Akumula�n� bity ignorov�ny</span></p>
<p class="src2">cAccumGreenBits := 0;</p>
<p class="src2">cAccumBlueBits := 0;</p>
<p class="src2">cAccumAlphaBits := 0;</p>
<p class="src2">cDepthBits := 16;<span class="kom">// 16-bitov� hloubkov� buffer (Z-Buffer)</span></p>
<p class="src2">cStencilBits := 0;<span class="kom">// ��dn� Stencil Buffer</span></p>
<p class="src2">cAuxBuffers := 0;<span class="kom">// ��dn� Auxiliary Buffer</span></p>
<p class="src2">iLayerType := PFD_MAIN_PLANE;<span class="kom">// Hlavn� vykreslovac� vrstva</span></p>
<p class="src2">bReserved := 0;<span class="kom">// Rezervov�no</span></p>
<p class="src2">dwLayerMask := 0;<span class="kom">// Maska vrstvy ignorov�na</span></p>
<p class="src2">dwVisibleMask := 0;</p>
<p class="src2">dwDamageMask := 0;</p>
<p class="src1">end;</p>

<p>Pokud nenastaly probl�my b�hem vytv��en� okna, pokus�me se p�ipojit kontext za��zen�. Pokud se to nepoda��, zobraz� se chybov� hl�en� a program se ukon��.</p>

<p class="src1">h_Dc := GetDC(h_Wnd);<span class="kom">// Zkus� p�ipojit kontext za��zen�</span></p>
<p class="src"></p>
<p class="src1">if h_Dc = 0 then<span class="kom">// Poda�ilo se p�ipojit kontext za��zen�?</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(0, 'Cant create a GL device context.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukon�� program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Kdy� z�sk�me kontext za��zen�, pokus�me se naj�t odpov�daj�c� Pixel Format. Kdy� ho Windows nenajde, zobraz� se chybov� zpr�va a program se ukon��.</p>

<p class="src1">PixelFormat := ChoosePixelFormat(h_Dc, @pfd);<span class="kom">// Zkus� naj�t Pixel Format</span></p>
<p class="src"></p>
<p class="src1">if (PixelFormat = 0) then<span class="kom">// Poda�ilo se naj�t Pixel Format?</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(0, 'Cant Find A Suitable PixelFormat.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukon�� program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Kdy� Windows najde odpov�daj�c� form�t, tak se ho pokus�me nastavit. Pokud p�i pokusu o nastaven� nastane chyba, op�t se zobraz� chybov� hl�en� a program se ukon��.</p>

<p class="src1">if (not SetPixelFormat(h_Dc, PixelFormat, @pfd)) then<span class="kom">// Poda�ilo se nastavit Pixel Format?</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(0, 'Cant set PixelFormat.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukon�� program</span></p>
<p class="src1">exit;</p>
<p class="src1">end;</p>

<p>Pokud byl nastaven Pixel Format spr�vn�, pokus�me se z�skat Rendering Context. Pokud ho nez�sk�me, program zobraz� chybovou zpr�vu a ukon�� se.</p>

<p class="src1">h_Rc := wglCreateContext(h_Dc);<span class="kom">// Poda�ilo se vytvo�it Rendering Context?</span></p>
<p class="src"></p>
<p class="src1">if (h_Rc = 0) then</p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(0, 'Cant create a GL rendering context.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukon�� program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pokud nenastaly ��dn� chyby p�i vytv��en� jak Device Context, tak Rendering Context, v�e co mus�me nyn� ud�lat je aktivovat Rendering Context. Pokud ho nebudeme moci aktivovat, zobraz� se chybov� zpr�va a program se ukon��.</p>

<p class="src1">if (not wglMakeCurrent(h_Dc, h_Rc)) then<span class="kom">// Poda�ilo se aktivovat Rendering Context?</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(0, 'Cant activate the GL rendering context.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukon�� program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pokud bylo okno vytvo�eno, zobraz�me ho na obrazovce, nastav�me ho, aby bylo v pop�ed� (vy��� priorita) a pak nastav�me zam��en� na toto okno. Zavol�me funkci ResizeGLScene s parametry odpov�daj�c�mi v��ce a ���ce okna, abychom spr�vn� nastavili perspektivu OpenGL.</p>

<p class="src1">ShowWindow(h_Wnd, SW_SHOW);<span class="kom">// Zobrazen� okna</span></p>
<p class="src1">SetForegroundWindow(h_Wnd);<span class="kom">// Do pop�ed�</span></p>
<p class="src1">SetFOcus(h_Wnd);<span class="kom">// Zam��� fokus</span></p>
<p class="src1">ReSizeGLScene(width, height);<span class="kom">// Nastaven� perspektivy OpenGL sc�ny</span></p>

<p>Kone�n� se dost�v�me k vol�n� v��e definovan� funkce InitGL, ve kter� nastavujeme osv�tlen�, loading textur a cokoliv jin�ho, co je pot�eba. Ve funkci InitGL m��ete vytvo�it svou vlastn� kontrolu chyb a vracet true, kdy� v�e prob�hne bez probl�m�, nebo false, pokud nastanou n�jak� probl�my. Nap��klad, nastane-li chyba p�i nahr�v�n� textur, vr�t�te false, jako znamen�, �e n�co selhalo a program se ukon��.</p>

<p class="src1">if not InitGL then<span class="kom">// Inicializace okna</span></p>
<p class="src1">begin</p>
<p class="src2">KillGLWindow;<span class="kom">// Zav�e okno</span></p>
<p class="src2">MessageBox(0, 'Initialization failed.', 'Error', MB_OK or MB_ICONEXCLAMATION);</p>
<p class="src2">Result := false;<span class="kom">// Ukon�� program</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pokud jsme se dostali a� takhle daleko, m��eme konstatovat, �e vytvo�en� okna prob�hlo bez probl�m�.</p>

<p class="src1">Result := true;<span class="kom">// V�e prob�hlo v po��dku</span></p>
<p class="src0">end;</p>

<p>Nyn� se vypo��d�me se syst�mov�mi zpr�vami pro okno. Kdy� m�me zaregistrov�nu na�i Window Class, m��eme podstoupit k ��sti k�du, kter� m� na starosti zpracov�n� zpr�v.</p>

<p class="src0">function WndProc(hWnd: HWND;<span class="kom">// Handle okna</span></p>
<p class="src1">message: UINT;<span class="kom">// Zpr�va pro okno</span></p>
<p class="src1">wParam: WPARAM;<span class="kom">// Dopl�kov� informace</span></p>
<p class="src1">lParam: LPARAM):<span class="kom">// Dopl�kov� informace</span></p>
<p class="src1">LRESULT; stdcall;</p>
<p class="src0">begin</p>

<p>Po p��chodu WM_SYSCOMMAND (syst�mov� p��kaz) porovn�me wParam s mo�n�mi stavy, kter� mohly nastat. Kdy� je wParam WM_SCREENSAVE nebo SC_MONITORPOWER, sna�� se syst�m zapnout spo�i� obrazovky nebo p�ej�t do �sporn�ho re�imu. Jestli�e vr�t�me 0 zabr�n�me syst�mu, aby tyto akce provedl.</p>

<p class="src1">if message = WM_SYSCOMMAND then<span class="kom">// Syst�mov� p��kaz</span></p>
<p class="src1">begin</p>
<p class="src2">case wParam of<span class="kom">// Typ syst�mov�ho p��kazu</span></p>
<p class="src3">SC_SCREENSAVE,<span class="kom">// Pokus o zapnut� �et�i�e obrazovky</span></p>
<p class="src3">SC_MONITORPOWER:<span class="kom">// Pokus o p�echod do �sporn�ho re�imu?</span></p>
<p class="src3">begin</p>
<p class="src4">result := 0;<span class="kom">// Zabr�n� oboj�mu</span></p>
<p class="src4">exit;</p>
<p class="src3">end;</p>
<p class="src2">end;</p>
<p class="src1">end;</p>

<p>Nap�eme mapu zpr�v. Program se bude v�tvit podle prom�nn� message, kter� obsahuje jm�no zpr�vy.</p>

<p class="src1">case message of<span class="kom">// V�tven� podle p��choz� zpr�vy</span></p>

<p>Po p��chodu WM_ACTIVE zkontrolujeme, zda je okno st�le aktivn�. Pokud bylo minimalizov�no, nastav�me hodnotu active na false. Pokud je okno aktivn�, prom�nn� active bude m�t hodnotu true.</p>

<p class="src2">WM_ACTIVATE:<span class="kom">// Zm�na aktivity okna</span></p>
<p class="src2">begin</p>
<p class="src3">if (Hiword(wParam) = 0) then<span class="kom">// Zkontroluje zda nen� minimalizovan�</span></p>
<p class="src4">active := true<span class="kom">// Program je aktivn�</span></p>
<p class="src3">else</p>
<p class="src4">active := false;<span class="kom">// Program nen� aktivn�</span></p>
<p class="src"></p>
<p class="src3">Result := 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">end;</p>

<p>P�i�lo-li WM_CLOSE, bylo okno zav�eno. Po�leme tedy zpr�vu pro opu�t�n� programu, kter� p�eru�� vykon�v�n� hlavn�ho cyklu. Prom�nnou done (ve WinMain) nastav�me na true, hlavn� smy�ka se p�eru�� a program se ukon��.</p>

<p class="src2">WM_CLOSE:<span class="kom">// Povel k ukon�en� programu</span></p>
<p class="src2">begin</p>
<p class="src3">PostQuitMessage(0);<span class="kom">// Po�le zpr�vu o ukon�en�</span></p>
<p class="src3">result := 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">end;</p>

<p>Pokud byla stisknuta kl�vesa, m��eme podle wParam zjistit, kter� z nich to byla. Potom p�i�ad�me do odpov�daj�c� bu�ky v poli keys[] true. D�ky tomu m��eme kdykoli v programu zjistit, kter� kl�vesa je pr�v� stisknut�. T�mto zp�sobem lze zkontrolovat i stisk v�ce kl�ves najednou.</p>

<p class="src2">WM_KEYDOWN:<span class="kom">// Stisk kl�vesy</span></p>
<p class="src2">begin</p>
<p class="src3">keys[wParam] := TRUE;<span class="kom">// Ozn�m� to programu</span></p>
<p class="src3">result := 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">end;</p>

<p>Pokud byla naopak kl�vesa uvoln�na, ulo��me do bu�ky s indexem wParam v poli keys[] hodnotu false. T�mto zp�sobem m��eme zjistit, zda je kl�vesa je�t� st�le stisknuta nebo ji� byla uvoln�na. Ka�d� kl�vesa je reprezentov�na jedn�m ��slem od 0 do 255. Kdy� nap��klad stisknu kl�vesu ��slo 40, hodnota keys[40] bude true, jakmile ji pust�m, jej� hodnota se vr�t� op�t na false.</p>

<p class="src2">WM_KEYUP:<span class="kom">// Uvoln�n� kl�vesy</span></p>
<p class="src2">begin</p>
<p class="src3">keys[wParam] := FALSE;<span class="kom">// Ozn�m� to programu</span></p>
<p class="src3">result := 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">end;</p>

<p>Kdykoliv u�ivatel zm�n� velikost okna, po�le se WM_SIZE. P�e�teme LOWORD a HIWORD hodnoty lParam, abychom zjistili, jak� je nov� ���ka a v��ka okna. P�ed�me tyto hodnoty do funkce ReSizeGLScene a t�m se perspektiva OpenGL sc�ny zm�n� podle nov�ch rozm�r�.</p>

<p class="src2">WM_SIZE:<span class="kom">// Zm�na velikosti okna</span></p>
<p class="src2">begin</p>
<p class="src3">ReSizeGLScene(LOWORD(lParam), HIWORD(lParam));<span class="kom">// LoWord=���ka, HiWord=V��ka</span></p>
<p class="src3">result := 0;<span class="kom">// N�vrat do hlavn�ho cyklu programu</span></p>
<p class="src2">end</p>
<p class="src1">else</p>

<p>Zpr�vy, o kter� se nestar�me, budou p�ed�ny funkci DefWindowProc, tak�e se s nimi za n�s vypo��d� opera�n� syst�m.</p>

<p class="src2"><span class="kom">// P�ed�n� ostatn�ch zpr�v syst�mu</span></p>
<p class="src2">begin</p>
<p class="src3">Result := DefWindowProc(hWnd, message, wParam, lParam);</p>
<p class="src2">end;</p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Funkce WinMain je vstupn� bod do aplikace, m�sto, odkud budeme volat funkce na otev�en� okna, sn�man� zpr�v a interakci s u�ivatelem.</p>

<p class="src0">function WinMain(hInstance: HINST;<span class="kom">// Instance</span></p>
<p class="src1">hPrevInstance: HINST;<span class="kom">// P�edchoz� instance</span></p>
<p class="src1">lpCmdLine: PChar;<span class="kom">// Parametry p��kazov� ��dky</span></p>
<p class="src1">nCmdShow: integer):<span class="kom">// Stav zobrazen� okna</span></p>
<p class="src1">integer; stdcall;</p>

<p>Deklarujeme dv� lok�ln� prom�nn�. Msg bude pou�ita na zji��ov�n�, zda se maj� zpracov�vat n�jak� zpr�vy. Prom�nn� done bude m�t na po��tku hodnotu false. To znamen�, �e n� program je�t� nem� b�t ukon�en. Dokud se done rovn� false, program pob��. Jakmile se zm�n� z false na true, program se ukon��.</p>

<p class="src0">var</p>
<p class="src1">msg: TMsg;<span class="kom">// Struktura zpr�v syst�mu</span></p>
<p class="src1">done: Bool;<span class="kom">// Prom�nn� pro ukon�en� programu</span></p>
<p class="src"></p>
<p class="src0">begin</p>
<p class="src1">done := false;</p>

<p>Dal�� ��st k�du je voliteln�. Zobrazuje zpr�vu, kter� se zept� u�ivatele, zda chce spustit program ve fullscreenu. Pokud u�ivatel vybere mo�nost Ne, hodnota prom�nn� fullscreen se zm�n� z v�choz�ho true na false, a t�m p�dem se program spust� v okn�.</p>

<p class="src1"><span class="kom">// Dotaz na u�ivatele pro fullscreen/okno</span></p>
<p class="src1">if MessageBox(0, 'Would You Like To Run In FullScreen Mode?', 'Start FullScreen', MB_YESNO or MB_ICONQUESTION) = IDNO then</p>
<p class="src2">FullScreen := false<span class="kom">// B�h v okn�</span></p>
<p class="src1">else</p>
<p class="src2">FullScreen := true;<span class="kom">// Fullscreen</span></p>

<p>Vytvo��me OpenGL okno. Zad�me text titulku, ���ku, v��ku, barevnou hloubku a true (fullscreen), nebo false (okno) jako parametry do funkce CreateGLWindow. Tak a je to! Je to p�kn� lehk�, �e? Pokud se okno nepoda�� z n�jak�ho d�vodu vytvo�it, bude vr�ceno false a program se okam�it� ukon��.</p>

<p class="src1">if not CreateGLWindow('NeHes OpenGL Framework', 640, 480, 16, FullScreen) then<span class="kom">// Vytvo�en� OpenGL okna</span></p>
<p class="src1">begin</p>
<p class="src2">Result := 0;<span class="kom">// Konec programu p�i chyb�</span></p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Smy�ka se opakuje tak dlouho, dokud se done rovn� false.</p>

<p class="src1">while not done do<span class="kom">// Hlavn� cyklus programu</span></p>
<p class="src1">begin</p>

<p>Prvn� v�c, kterou ud�l�me, je zkontrolov�n� zpr�v pro okno. Pomoc� funkce PeekMessage m��eme zjistit zda n�jak� zpr�vy �ekaj� na zpracov�n� bez toho, aby byl program pozastaven. Mnoho program� pou��v� funkci GetMessage. Pracuje to skv�le, ale program nic ned�l�, kdy� nedost�v� ��dn� zpr�vy.</p>

<p class="src2">if (PeekMessage(msg, 0, 0, 0, PM_REMOVE)) then<span class="kom">// P�i�la zpr�va?</span></p>
<p class="src2">begin</p>

<p>Zkontrolujeme, zda jsme neobdr�eli zpr�vu pro ukon�en� programu. Pokud je aktu�ln� zpr�va WM_QUIT, kter� je zp�sobena vol�n�m funkce PostQuitMessage(0), nastav�me done na true, ��m� p�eru��me hlavn� cyklus a ukon��me program.</p>

<p class="src3">if msg.message = WM_QUIT then<span class="kom">// Obdr�eli jsme zpr�vu pro ukon�en�?</span></p>
<p class="src4">done := true<span class="kom">// Konec programu</span></p>
<p class="src3">else</p>
<p class="src3">begin</p>

<p>Kdy� zpr�va nevyz�v� k ukon�en� programu, tak p�ed�me funkc�m TranslateMessage a DispatchMessage referenci na tuto zpr�vu, aby ji funkce WndProc nebo Windows zpracovaly.</p>

<p class="src4">TranslateMessage(msg);<span class="kom">// P�elo�� zpr�vu</span></p>
<p class="src4">DispatchMessage(msg);<span class="kom">// Ode�le zpr�vu</span></p>
<p class="src3">end;</p>
<p class="src2">end</p>
<p class="src2">else<span class="kom">// Pokud nedo�la ��dn� zpr�va</span></p>
<p class="src2">begin</p>

<p>Pokud zde nebudou ji� ��dn� zpr�vy, p�ekresl�me OpenGL sc�nu. N�sleduj�c� ��dek kontroluje, zda je okno aktivn�. Na�e sc�na je vyrenderov�na a je zkontrolov�na vr�cen� hodnota. Kdy� funkce DrawGLScene vr�t� false nebo je stisknut ESC, hodnota prom�nn� done je nastavena na true, co� ukon�� b�h programu.</p>

<p class="src3"><span class="kom">// Je program aktivn�, ale nelze kreslit? Byl stisknut ESC?</span></p>
<p class="src3">if (active and not(DrawGLScene) or keys[VK_ESCAPE]) then</p>
<p class="src4">done := true<span class="kom">// Ukon��me program</span></p>
<p class="src3">else<span class="kom">// P�ekreslen� sc�ny</span></p>

<p>Kdy� v�echno prob�hlo bez probl�m�, prohod�me obsah buffer� (s pou�it�m dvou buffer� p�edejdeme blik�n� obrazu p�i p�ekreslov�n�). Pou�it�m dvojit�ho bufferingu v�echno vykreslujeme do obrazovky v pam�ti, kterou nevid�me. Jakmile vym�n�me obsah buffer�, to co je na obrazovce se p�esune do t�to skryt� obrazovky a to, co je ve skryt� obrazovce se p�enese na monitor. D�ky tomu nevid�me probliknut�.</p>

<p class="src4">SwapBuffers(h_Dc);<span class="kom">// Prohozen� buffer� (Double Buffering)</span></p>

<p>P�i stisku kl�vesy F1 p�epneme z fullscreenu do okna a naopak.</p>

<p class="src3">if keys[VK_F1] then<span class="kom">// Byla stisknuta kl�vesa F1?</span></p>
<p class="src3">begin</p>
<p class="src4">Keys[VK_F1] := false;<span class="kom">// Ozna� ji jako nestisknutou</span></p>
<p class="src4">KillGLWindow;<span class="kom">// Zru�� okno</span></p>
<p class="src4">FullScreen := not FullScreen;<span class="kom">// Negace fullscreen</span></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Znovuvytvo�en� okna</span></p>
<p class="src4">if not CreateGLWindow('NeHes OpenGL Framework', 640, 480, 16, fullscreen) then</p>
<p class="src5">Result := 0;<span class="kom">// Konec programu pokud nebylo vytvo�eno</span></p>
<p class="src3">end;</p>
<p class="src2">end;</p>
<p class="src1">end;<span class="kom">// Konec smy�ky while</span></p>

<p>Pokud se prom�nn� done rovn� true, hlavn� cyklus se p�eru��. Zav�eme okno a opust�me program.</p>

<p class="src1">KillGLWindow;<span class="kom">// Zav�e okno</span></p>
<p class="src1">result := msg.wParam;<span class="kom">// Ukon�en� programu</span></p>
<p class="src0">end;</p>

<p>Cel� program se skl�d� pouze z vol�n� funkce WinMain, kter� se u� postar� o v�e ostatn�.</p>

<p class="src0">begin</p>
<p class="src1">WinMain(hInstance, hPrevInst, CmdLine, CmdShow);<span class="kom">// Start programu</span></p>
<p class="src0">end.</p>

<p>Pokud se v�m text zd�l pov�dom�, m�te pravdu. V podstat� jde o Wessan�v p�eklad lekce 1, ��m� mu t�mto d�kuji, jen k�d je v Delphi. Pokud budete m�t jak�koli dotazy nebo probl�my, nev�hejte m� kontaktovat.</p>

<p class="autor">napsal: Michal Tu�ek <?VypisEmail('michal_praha@seznam.cz');?>, 15.08.2004</p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li>Najdete v sekci <?OdkazWeb('download', 'download');?>...</li>
</ul>

<?
include 'p_end.php';
?>
