<?
$g_title = 'CZ NeHe OpenGL - Zprovozn�n� OpenGL v Linuxu (ovlada�e karty, kompilace)';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Zprovozn�n� OpenGL v Linuxu (ovlada�e karty, kompilace)</h1>

<p class="nadpis_clanku">Kdy� jsem p�ibli�n� p�ed p�l rokem (podzim 2003) p�ech�zel z MS Windows&reg; na opera�n� syst�m Linux, m�l jsem relativn� velk� pot�e se zprovozn�n�m OpenGL. Nejedn� se sice o nic slo�it�ho, nicm�n� pro tehdy nic nech�paj�c�ho Woqa u�ivatele (analogie na Frantu u�ivatele :-) to byl naprosto ne�e�iteln� probl�m.</p>

<p>Nad t�mto �l�nkem budou s nejv�t�� pravd�podobnost� v�ichni Linuxov� guru nech�pav� kroutit hlavou (my�leno nad jeho smyslem ne slo�itost� obsahu), nicm�n� za��te�n�k�m by se opravdu mohl hodit. Pokud budete m�t po p�e�ten� dojem, �e byl naprosto pod va�i �rove�, velice se v�m omlouv�m, ale ka�d� n�kde mus� za��t.</p>

<p>V�echno, co zde budu popisovat ur�it� plat� pro <?OdkazBlank('http://www.mandrake.cz/', 'Mandrake');?> Linux 9.2, s jin�mi distribucemi nem�m zku�enosti, ale m�lo by to b�t stejn� nebo alespo� podobn�.</p>

<h3>Program je extr�mn� trhan� - chyb� ovlada�e grafick� karty</h3>

<p>Tento probl�m je zp�soben t�m, �e v syst�mu nen� p��tomen ovlada� grafick� karty (respektive je, ale pouze obecn�) a OpenGL se kompletn� emuluje na software. Abych p�ede�el j�zliv�m pozn�mk�m od u�ivatel� jin�ho opera�n�ho syst�mu: Po instalaci M$ Windows XP&reg; je situace naprosto stejn�, nicm�n� p���ina komplikac� le�� na trochu jin�m m�st�.</p>

<p>V Linuxu cel� probl�m spo��v� v tom, �e ovlada� vytvo�en� v�robcem, je komer�n� software (nebo n�co v tom smyslu), a proto by syst�m nemohl b�t zdarma. Samoz�ejm�, kdy� si Linux koup�te, plat�te n�jakou ��stku, nap�. j� jsem ob�toval pln�ch 361 K�! V cen� byly 3 standardn� CD Mandrake, 1 Bonus CD od �esk�ho distributora (v�echny 4 lisovan�) a ti�t�n� 136 str�nkov� manu�l. Pokud by se v�m p�esto ji� zm�n�n�ch 361 K� zd�lo p��li� mnoho, nic v�m, krom� pomal�ho p�ipojen� k internetu, nebr�n� ve st�hnut� ISO obraz� CD z <?OdkazBlank('ftp://mandrake.redbox.cz/');?>, pak to bude opravdu zadarmo. V p��pad� dra���ch verz� syst�mu, kde u� m� v�robce n�jak� zisk, b�vaj� ovlada�e na spr�vn�m m�st� ihned po instalaci.</p>

<p>Abych trochu r�pl do M$ a pros�m opravte m�, jestli se m�l�m. Pro� nejsou ve Windows XP ovlada�e s podporou OpenGL nainstalovan�, ani kdy� je karta spr�vn� detekovan�? Nejsem si jist�, zda na software b�� i DirectX (nev�m, nepou��v�m - ani Win ani DirectX), ale d� se p�edpokl�dat, �e ne. Zkr�tka OpenGL je konkuren�n� standard...</p>

<p>Jak tedy nainstalovat driver grafick� karty? Nejd��ve zajd�te na web v�robce (ATI - <?OdkazBlank('http://www.ati.com/');?>, NVIDIA - <?OdkazBlank('http://www.nvidia.com/');?>) a st�hn�te si je. Mimochodem, u Mandrake Linuxu je m��ete naj�t na Bonus CD. Firma NVIDIA podporuje Linux u� hodn� dlouho, tak�e by zde nem�ly b�t v�t�� probl�my. Naproti tomu ATI se o Linux za�ala starat a� od Radeonu 8500. Instalaci ovlada�� karet t�chto dvou v�robc� popisuje nap�. <?OdkazBlank('ftp://mandrake.contactel.cz/people/bibri/doc/cz/', 'Mandrake - Instala�n� manu�l');?>, kter� napsal pan Ivan B�br. P�ed vlastn� instalac� si tak� ur�it� p�e�t�te README dokument, kter� najdete u ovlada��. N�kter� v�ci se p�ece jen mohly zm�nit, budete m�t jistotu.</p>

<p>Schematicky nast�n�m alespo� postup... NVIDIA m� vlastn� instal�tor, kter� s�m za��d� v�t�inu pot�ebn�ch v�c�. Nem�li byste ho spou�t�t z grafick�ho re�imu, ale v�hradn� z konzole. Syst�m X Window by m�l b�t p�i t�to operaci v�dy vypnut�. Osobn� jsem zprovoz�oval GeForce 2 a v�e bylo bez nejmen��ch probl�m�. Po dokon�en� instalace je je�t� nutn� zm�nit hodnoty n�kolika polo�ek v konfigura�n�m souboru XFree (/etc/X11/XF86Config-4). V�e pot�ebn� najdete v README souboru.</p>

<p>ATI, co v�m, distribuuje ovlada�e v bal��c�ch RPM. Jak jsem ji� napsal v��e, jedn� se pouze o karty Radeon 8500 a nov�j��, tak�e s m�m Radeonem 7000 byl trochu probl�m. Na�t�st� jeho drivery (a n�kolika des�tek dal��ch karet) v sob� obsahuje p��mo XFree. Nev�m, jak se zap�naj� obecn�, ale s nejv�t�� pravd�podobnost� se op�t jedn� o /etc/X11/XF86Config-4. Mandrake 9.2 m� v z�v�ru instalace &quot;Souhrn&quot;, ve kter�m lze zm�nit v�echna dosavadn� nastaven�. Na stejn�m m�st�, kde se definuje rozli�en� monitoru, barevn� hloubka a podobn�, najdete i polo�ku &quot;Grafick� karta&quot;. Pokud zm�n�te volbu ze standardn�ho &quot;Radeon&quot; na &quot;Radeon fglrx&quot;, instal�tor se v�s v dal��m kroku zept�, zda chcete pou��t softwarovou emulaci OpenGL nebo b�h na hardwaru. Pokud m�te Linux u� nainstalovan�, lze ovlada� zm�nit v Ovl�dac� centrum -&gt; Hardware -&gt; Nastaven� grafick�ho serveru -&gt; Grafick� karta (nezkou�el jsem...).</p>

<div class="okolo_img"><a href="images/clanky/cl_gl_linux/ovladac.jpg"><img src="images/clanky/cl_gl_linux/ovladac_small.jpg" width="400" height="300" alt="Zm�na ovlada�e grafick� karty" /></a></div>

<h3>Nelze zkompilovat OpenGL/SDL aplikace - v syst�mu nejsou pot�ebn� knihovny</h3>

<p>Zn�m n�kolik lid�, kte�� programuj� OpenGL aplikace pod knihovnou SDL (viz <?OdkazWeb('clanky', '�l�nky');?>). Jej� nejv�t�� v�hodou je, �e se v�sledn� program d� portovat na mnoho opera�n�ch syst�m�. V m�m p��pad� se jedn� o v�voj v Linuxu a p��padn� p�enos do MS Windows, nicm�n� je mo�n� i obr�cen� sm�r :-).</p>

<p>Jako v�vojov� prost�ed� preferuji textov� KDE editor KWrite (takov� &quot;mali�ko&quot; lep�� Notepad) a kompil�tor gcc pop�. g++. Je samoz�ejm� mo�n� pou��vat i specializovan� v�vojov� prost�ed�, jako jsou nap��klad KDevelop nebo Anjuta, kter� si v ni�em nezadaj� s Wokenn�m Visual C++. Abyste pochopili n�sledn� v�klad, doporu�uji p�e��st si alespo� <?OdkazBlank('http://www.root.cz/clanek/2009', 'prvn� d�l');?> s�rie �l�nk� Programov�n� pod Linuxem pro v�echny, kter� vych�z� na <?OdkazBlank('http://www.root.cz/');?>. Bez t�chto znalost� se opravdu neobejdete.</p>

<p>Jestli je mo�n� OpenGL program zkompilovat, lze nejl�pe ov��it samotnou kompilac�. Pou�ijte t�eba demonstra�n� p��klad um�st�n� �pln� dole na t�to str�nce - kdysi jsem ho st�hl z internetu a pou��v�m ho pr�v� na �vodn� testy po nov� instalaci syst�mu.</p>

<p>Rozbalte arch�v (nap��klad Konquerorem) a po p�esunu do adres��e projektu zadejte p��kaz &quot;make&quot;, kter� prov�d� kompilaci.</p>

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

<p>Pokud se vyp�e n�co v tomto stylu, nen� v syst�mu nainstalovan� knihovna SDL. Postupujte podle n�sleduj�c�ho obr�zku. Mimochodem, nejsou na n�m vid�t ty spr�vn� bal��ky, proto�e u m� je u� v�e funk�n�.</p>

<div class="okolo_img"><a href="images/clanky/cl_gl_linux/install_sdl.jpg"><img src="images/clanky/cl_gl_linux/install_sdl_small.jpg" width="400" height="300" alt="Instalace SDL" /></a></div>

<p>Znovu zkuste make. Jak je vid�t z n�sleduj�c�ho v�pisu, kompila�n� f�ze prob�hla v po��dku, ale linker nemohl naj�t knihovnu libGL(U).so (nep�esn� �e�eno: n�co jako opengl32.lib z Windows).</p>

<p class="src0"><span class="kom">[woq@localhost Color]$</span> <b>make</b></p>
<p class="src0">g++ -c Main.cpp `sdl-config --cflags`</p>
<p class="src0">g++ -c Init.cpp `sdl-config --cflags`</p>
<p class="src0">g++ -o Color  Main.o Init.o `sdl-config --libs` -lGL -lGLU -lm</p>
<p class="src0">/usr//bin/ld: cannot find -lGL</p>
<p class="src0">collect2: ld returned 1 exit status</p>
<p class="src0">make: *** [Color] Error 1</p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span></p>

<p>Nev�m pro� nejsou knihovny pot�ebn� pro OpenGL um�st�ny v adres��i /usr/lib, ale v jeho XFree verzi /usr/X11R6/lib, kter� v�ak nen� uvedena v syst�mov� prom�nn� s cestami ke knihovn�m. Existuje n�kolik zp�sob� �e�en�, z nich� je asi nejsnadn�j�� vytvo�it symbolick� odkazy (z�stupce) na pot�ebn� soubory. Proto�e oby�ejn� u�ivatel� nemaj� p��stupov� pr�va na z�pis do t�chto adres���, mus�te se p�ihl�sit jako superu�ivatel root.</p>

<p class="src0"><span class="kom">[woq@localhost Color]$</span> <b>su root</b></p>
<p class="src0">Password:</p>
<p class="src0"><span class="kom">[root@localhost Color]#</span> <b>ln -s /usr/X11R6/lib/libGL.so /usr/lib/libGL.so</b></p>
<p class="src0"><span class="kom">[root@localhost Color]#</span> <b>ln -s /usr/X11R6/lib/libGLU.so /usr/lib/libGLU.so</b></p>
<p class="src0"><span class="kom">[root@localhost Color]#</span> <b>exit</b></p>
<p class="src0">exit</p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span></p>

<p>Nyn� by m�lo b�t v�e v po��dku. Zapi�te make a po zkompilov�n� spus�te vytvo�en� program.</p>

<p class="src0"><span class="kom">[woq@localhost Color]$</span> <b>make</b></p>
<p class="src0">g++ -c Main.cpp `sdl-config --cflags`</p>
<p class="src0">g++ -c Init.cpp `sdl-config --cflags`</p>
<p class="src0">g++ -o Color  Main.o Init.o `sdl-config --libs` -lGL -lGLU -lm</p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span> <b>Color</b></p>
<p class="src0"> Hit the F1 key to Toggle between Fullscreen and windowed mode</p>
<p class="src0"> Hit ESC to quit</p>
<p class="src0"><span class="kom">[woq@localhost Color]$</span></p>

<p>A v�sledek cel�ho na�eho sna�en�...</p>

<div class="okolo_img"><a href="images/clanky/cl_gl_linux/all_ok.jpg"><img src="images/clanky/cl_gl_linux/all_ok_small.jpg" width="400" height="300" alt="Vse OK" /></a></div>

<p>Pokud byste cht�li p�ilinkovat knihovny nutn� pro OpenGL nap�. ve v�vojov�m prost�ed� KDevelop, klikn�te v menu na polo�ku Projekt a v n� na Options... Objev� se v�m okno, ve kter�m sta�� na kart� Linker Options p�idat pot�ebn� knihovny (m�ly by sta�it -lGL -lGLU -lSDL).</p>

<div class="okolo_img"><a href="images/clanky/cl_gl_linux/kdevelop.jpg"><img src="images/clanky/cl_gl_linux/kdevelop_small.jpg" width="400" height="300" alt="P�ilinkov�n� OpenGL knihoven ve v�vojov�m prost�ed� KDevelop" /></a></div>

<p>Pokud u� n�jakou dobu v Linuxu pracujete, jist� byl pro v�s cel� postup velice jednoduch�, ale pro lidi, kte�� na n�j p�e�li teprve v�era, to byl s nejv�t�� pravd�podobnost� absolutn� ne�e�iteln� probl�m. Pro m� tedy byl. Zprovozn�n� kompilace OpenGL program� p�erostlo v chaotick� pokusy a omyly, p�i kter�ch jsem si kompletn� zlikvidoval syst�m z�vislost� softwarov�ch bal��k�. Po p�ekon�n� t�to f�ze jsem u� &quot;jen&quot;, kv�li jednomu RPM st�hnut�mu z internetu obsahuj�c�mu GLU, kter� byl v konfliktu s jin�m, musel odinstalovat a n�sledn� znovu nainstalovat p�tinu cel�ho software.</p>

<p>Hlavn�m probl�mem pro m� tehdy bylo odnau�it se �e�it probl�my v Linuxu &quot;Wokenn�m zp�sobem&quot;. Kdy� uvedu p��klad: je�t� ned�vno jsem m�l za to, �e *.so soubory jsou analogi� *.dll knihoven z Windows a *.a p�edstavuj� *.lib. Ve v�ech textech, co jsem �etl o kompilov�n� v gcc, bylo p�ece jasn� uvedeno, �e se argument -lGL p�evede na libGL.a a tento soubor se pak hled� ve standardn�ch adres���ch s knihovnami. To je sice naprost� pravda, ale nikde u� nebyla ani zm�nka, �e se u linkov�n� pou��v� i libGL.so, o kter�m jsem si tud� myslel, �e je Linuxovou formou DLL knihovny - v linkovac� f�zi naprosto nepou�iteln�. P�ekon�n� t�to utkv�l� p�edstavy mi trvalo nejm�n� m�s�c... a to je jen jedna z m�la uk�zek. Abych to ukon�il: Linux se nerovn� Windows, jak toto pochop�te (uvnit� - ne, �e v�m to n�kdo �ekne), m�te vyhr�no.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_gl_linux_color.tar.gz');?> - zdrojov� k�dy pro test kompilace OpenGL a SDL v Linuxu</li>
</ul>

<?
include 'p_end.php';
?>
