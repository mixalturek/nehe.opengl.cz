<?
$g_title = 'CZ NeHe OpenGL - Lekce 35 - P�ehr�v�n� videa ve form�tu AVI';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(35);?>

<h1>Lekce 35 - P�ehr�v�n� videa ve form�tu AVI</h1>

<p class="nadpis_clanku">P�ehr�v�n� AVI videa v OpenGL? Na pozad�, povrchu krychle, koule, �i v�lce, ve fullscreenu nebo v oby�ejn�m okn�. Co v�c si p��t...</p>

<p>Na za��tku bych cht�l pod�kovat Fredsterovi za AVI animaci, Maxwellu Saylesovi za rady p�i programov�n�, Jonathanu Nixovi a Johnu F. MCGowanovi, Ph. D. za skv�l� �l�nky/dokumenty o AVI form�tu. Moc jste mi pomohli.</p>

<p>Mus�m ��ci, �e jsem na tento tutori�l opravdu py�n�. kdy� m� Jonathan F. Blok p�ivedl na n�pad AVI p�ehr�va�e v OpenGL, nem�l jsem nejmen�� potuchu, jak AVI otev��t, nato� jak by mohl video p�ehr�va� fungovat. Za�al jsem listov�n�m ve sv�ch knih�ch o programov�n� - v�bec nic. Pot� jsem zkusil MSDN. Na�el jsem spoustu u�ite�n�ch informac�, ale bylo jich pot�eba mnohem, mnohem v�ce. Po hodin�ch prol�z�n� internetu jsem m�l poznamen�ny pouze dva weby. Nemohu ��ct, �e moje vyhled�vac� postupy jsou �pln� nejlep��, ale v cca. 99,9% p��pad� jsem nikdy nem�l nejmen�� probl�my. Byl jsem absolutn� �okov�n, kdy� jsem zjistil, jak m�lo p��klad� na p�ehr�v�n� videa tam bylo. V�t�ina z nich nav�c ne�la zkompilovat, n�kter� byly komplexn� (alespo� pro m�) a plnily sv�j ��el, nicm�n� byly programov�ny ve VB, Delphi nebo podobn� (ne VC++).</p>

<p>Prvn� z u�ite�n�ch str�nek, kter� jsem na�el, byl �l�nek od Janathana Nixe nadepsan� <?OdkazBlank('http://www.gamedev.net/reference/programming/features/avifile/', 'AVI soubory');?>. Jonathan m� u m� obrovsk� respekt za tak extr�mn� brilantn� dokument. A�koli jsem se rozhodl j�t jinou cestou ne� on, vnesl m� do problematiky. Druh� web, tentokr�t od Johna F. MCGowana, Ph. D., m� titulek The AVI Overview. Mohl bych te� za��t popisovat, jak ��asn� jsou Johnovi str�nky, ale snadn�j�� bude, kdy� se <?OdkazBlank('http://www.jmcgowan.com/avi.html', 'sami pod�v�te');?>. Soust�edil na nich snad v�e, co je o AVI zn�mo.</p>

<p>Posledn� v�c�, na kterou chci upozornit, je, �e ��dn� ��st z cel�ho k�du NEBYLA vyp�j�ena a nic nebylo okop�rov�no. K�dov�n� mi zabralo pln� t�i dny, pou��val jsem pouze informace z v��e uveden�ch zdroj�. Z�rove� c�t�m, �e by bylo vhodn� poznamenat, �e m�j k�d nemus� b�t nejlep��m zp�sobem pro p�ehr�v�n� AVI soubor�. Dokonce nemus� b�t ani vhodnou cestou, ale funguje a snadno se pou��v�. Nicm�n� pokud se v�m m�j styl a k�d nel�b�, nebo c�t�te-li, �e uvoln�n�m tohoto tutori�lu dokonce zra�uji program�torskou komunitu, m�te n�kolik mo�nost�: 1) zkuste si na internetu naj�t jin� zdroje, 2) napi�te si sv�j vlastn� AVI p�ehr�va� nebo 3) napi�te lep�� tutori�l. Ka�d�, kdo nav�t�v� tento web, by m�l v�d�t, �e k�duji pro z�bavu. Hlavn�m ��elem t�chto str�nek je uleh�it �ivot ne-elitn�m program�tor�m, kte�� za��naj� s OpenGL. Tutori�ly ukazuj�, jak jsem !j�! dok�zal vytvo�it specifick� efekt... nic v�ce, nic m�n�.</p>

<p>Poj�me ale ke k�du. Jako prvn� v�c vlo��me a p�ilinkujeme knihovnu Video For Windows. Obrovsk� d�ky Microsoft&reg;u (Nikdy bych nev��il, �e to �eknu). Pomoc� t�to knihovny bude otev�r�n� a p�ehr�v�n� AVI pouhou banalitou.</p>

<p class="src0">#include &lt;vfw.h&gt;<span class="kom">// Hlavi�kov� soubor knihovny Video pro Windows</span></p>
<p class="src0">#pragma comment(lib, &quot;vfw32.lib&quot;)<span class="kom">// P�ilinkov�n� VFW32.lib</span></p>

<p>Deklarujeme prom�nn�. Angle je �hel nato�en� zobrazovan�ho objektu. Next p�edstavuje cel� ��slo, kter� pou�ijeme pro spo��t�n� mno�stv� uplynul�ho �asu (v milisekund�ch), abychom mohli udr�et framerate na spr�vn� hodnot�. V�ce o tomto d�le. Frame bude samoz�ejm� obsahovat ��slo aktu�ln� zobrazovan�ho sn�mku animace. Effect p�edstavuje druh objektu na obrazovce (krychle, koule, v�lec, ��dn�). Bude-li env rovno true, budou se automaticky generovat texturov� sou�adnice. Bg p�edstavuje flag, kter� definuje, jestli se m� pozad� zobrazovat nebo ne. Sp, ep a bp slou�� pro o�et�en� del��ho stisku kl�ves.</p>

<p class="src0">float angle;<span class="kom">// �hel rotace objektu</span></p>
<p class="src0">int next;<span class="kom">// Pro animaci</span></p>
<p class="src0">int frame = 0;<span class="kom">// Aktu�ln� sn�mek videa</span></p>
<p class="src0">int effect;<span class="kom">// Zobrazen� objekt</span></p>
<p class="src"></p>
<p class="src0">bool env = TRUE;<span class="kom">// Automaticky generovat texturov� koordin�ty?</span></p>
<p class="src0">bool bg = TRUE;<span class="kom">// Zobrazovat pozad�?</span></p>
<p class="src"></p>
<p class="src0">bool sp;<span class="kom">// Stisknut mezern�k?</span></p>
<p class="src0">bool ep;<span class="kom">// Stisknuto E?</span></p>
<p class="src0">bool bp;<span class="kom">// Stisknuto B?</span></p>

<p>Struktura psi bude udr�ovat informace o AVI souboru. Pavi p�edstavuje ukazatel na buffer, do kter�ho po otev�en� AVI obdr��me handle nov�ho proudu. Pgf, pointer na objekt GetFrame, pou�ijeme pro z�sk�v�n� jednotliv�ch sn�mk�, kter� pomoc� bmih zkonvertujeme do form�tu, kter� pot�ebujeme pro vytvo�en� textury. Lastframe ukl�d� ��slo posledn�ho sn�mku animace. Width a height definuj� rozm�ry AVI proudu, pdata je ukazatel na data obr�zku vr�cen� po po�adavku na sn�mek. Mpf (Miliseconds Per Frame) pou�ijeme pro v�po�et doby zobrazen� sn�mku. P�edpokl�d�m, �e nem�te nejmen�� pon�t�, k �emu v�echny tyto prom�nn� vlastn� slou��... v�e byste m�li pochopit d�le.</p>

<p class="src0">AVISTREAMINFO psi;<span class="kom">// Informace o datov�m proudu videa</span></p>
<p class="src0">PAVISTREAM pavi;<span class="kom">// Handle proudu</span></p>
<p class="src0">PGETFRAME pgf;<span class="kom">// Ukazatel na objekt GetFrame</span></p>
<p class="src0">BITMAPINFOHEADER bmih;<span class="kom">// Hlavi�ka pro DrawDibDraw dek�dov�n�</span></p>
<p class="src"></p>
<p class="src0">long lastframe;<span class="kom">// Posledn� sn�mek proudu</span></p>
<p class="src0">int width;<span class="kom">// ���ka videa</span></p>
<p class="src0">int height;<span class="kom">// V��ka videa</span></p>
<p class="src0">char* pdata;<span class="kom">// Ukazatel na data textury</span></p>
<p class="src0">int mpf;<span class="kom">// Doba zobrazen� jednoho sn�mku (Milliseconds Per Frame)</span></p>

<p>Pomoc� knihovny GLU budeme moci vykreslit dva quadratic �tvary, kouli a v�lec. Hdd je handle na DIB (Device Independent Bitmap) a hdc je handle na kontext za��zen�. HBitmap p�edstavuje handle na bitmapu z�vislou na za��zen� (DDB - Device Dependent Bitmap), pou�ijeme ji d�le p�i konverz�ch. Data je pointer, kter� bude ukazovat na data obr�zku pou�iteln� pro vytvo�en� textury. Op�t - v�ce pochop�te d�le.</p>

<p class="src0">GLUquadricObj *quadratic;<span class="kom">// Objekt quadraticu</span></p>
<p class="src"></p>
<p class="src0">HDRAWDIB hdd;<span class="kom">// Handle DIBu</span></p>
<p class="src0">HBITMAP hBitmap;<span class="kom">// Handle bitmapy z�visl� na za��zen�</span></p>
<p class="src0">HDC hdc = CreateCompatibleDC(0);<span class="kom">// Kontext za��zen�</span></p>
<p class="src0">unsigned char* data = 0;<span class="kom">// Ukazatel na bitmapu o zm�n�n� velikosti</span></p>

<p>Nyn� mal� �vod do jazyka Assembler (ASM). Pokud jste ho je�t� nikdy d��ve nepou�ili, nelekejte se. M��e vypadat slo�it�, ale v�e je velmi jednoduch�. P�i programov�n� tohoto tutori�lu jsem se dostal p�ed velk� probl�m. Aplikace b�ela v po��dku, ale barvy byly divn�. V�e, co m�lo b�t �erven� bylo modr�, a v�e co m�lo b�t modr� bylo �erven� - klasick� prohozen� R a B slo�ky pixel�. Byl jsem absolutn� �okovan�. Myslel jsem si, �e jsem v k�du ud�lal n�jakou ��lenou chybu typu &quot;��rka sem, znam�nko tam...&quot;. Po pe�liv�m prostudov�n� v�eho, co jsem do t� doby napsal, jsem nebyl schopen bug naj�t. Za�al jsem znovu pro��tat MSDN. Pro� byla �erven� a modr� slo�ka barvy prohozen�?! V MSDN bylo p�ece jasn� naps�no, �e 24 bitov� bitmapy jsou ve form�tu RGB!!! Po spoust� dal��ho �ten� jsem probl�m objevil. Ve Windows se RGB data ukl�daj� pozp�tku a RGB ulo�en� pozp�tku je p�eci BGR! Tak�e si jednou pro v�dy zapamatujte, �e v OpenGL RGB znamen� RGB a ve Windows RGB znamen� BGR - jak jednoduch�.</p>

<p>Po st�nostech od fanou�k� Microsoft&reg;u (P�ekl.: Ono n�co takov�ho existuje?!): Rozhodl jsem se p�idat kr�tk� vysv�tlen�... Nepomlouv�m Microsoft kv�li tomu, �e ozna�il BGR form�t barvy za RGB. Jestli se mu p�evr�cen� zkratka l�b� v�ce, a� si ji pou��v�. Nicm�n� nalezen� chyby m��e b�t pro ciz�ho program�tora velice frustruj�c� (zvlṻ kdy� ��dn� neexistuje).</p>

<p>Blue p�idal: M� to co d�lat s konvencemi little endian a big endian. Intel a Intel kompatibiln� syst�my pou��vaj� little endian, u kter�ho se m�n� v�znamn� byty ukl�daj� d��ve ne� v�ce v�znamn�. Specifikaci OpenGL vytvo�ila firma SGI (Silicon Graphic), jej� syst�my pravd�podobn� pou��vaj� big endian, a tud� OpenGL standardn� vy�aduj� bitmapy ve form�tu big endian.</p>

<p>Skv�l�! Tak�e jsem vytvo�il p�ehr�va�, kter� je absolutn� k ni�emu (P�ekl.: v origin�le absolute crap - zkuste si toto slovo naj�t ve slovn�ku, j� chci b�t slu�n� :-). Prvn�m �e�en�m, kter� m� napadlo, bylo prohodit byty manu�ln� pomoc� cyklu for. Pracovalo to v po��dku, ale stra�n� pomalu. M�l jsem v�eho po krk. Zkusil jsem modifikoval generov�n� textury na GL_BGR_EXT m�sto GL_RGB. Obrovsk� n�r�st rychlosti a barvy vypadaj� skv�le! Tak�e jsem probl�m kone�n� vy�e�il... alespo� jsem si to myslel. N�kter� OpenGL ovlada�e maj� s GL_BGR_EXT probl�my :-( Maxwell Sayles mi doporu�il prohozen� byt� pomoc� ASM. O minutku pozd�ji mi ICQ-oval k�d uveden� n�e, kter� je rychl� a pln� dokonale svou funkci.</p>

<p>Ka�d� sn�mek animace se ukl�d� do bufferu, obr�zek m� v�dy �tvercovou velikost 256 pixel� a 3 barevn� slo�ky ve form�tu BGR (speci�ln� pro Billa Gatese: RGB). Funkce flipIt() proch�z� tento buffer po t�� bytov�ch kroc�ch a zam��uje �ervenou slo�ku za modrou. R m� b�t ulo�eno na pozici abx+0 a B na abx+2. Cyklus se opakuje tak dlouho, dokud nejsou v�echny pixely ve form�tu RGB.</p>

<p>P�edpokl�d�m, �e v�t�ina z v�s nen� z ASM moc nad�en�. Jak u� jsem psal, p�vodn� jsem pl�noval pou��t GL_BGR_EXT. Funguje, ale ne na v�ech kart�ch. Potom jsem se rozhodl j�t cestou minul�ch tutori�l� a swapovat byty pomoc� bitov�ch operac� XOR, kter� pracuj� na v�ech po��ta��ch, ale ne extr�mn� rychle. Dokud jsme nepracovali s real-time videem, sta�ily, ale tentokr�t pot�ebujeme co mo�n� nejrychlej�� metodu. Zv��me-li v�echny mo�nosti, je ASM podle m�ho n�zoru nejlep�� volbou. Pokud m�te je�t� lep�� zp�sob, pros�m... POU�IJTE HO! Ne��k�m v�m, jak co M�TE d�lat, j� pouze ukazuji, jak jsem probl�my vy�e�il j�. V�e proto tak� vysv�tluji do detail�, abyste m�j k�d, pokud zn�te lep��, mohli nahradit.</p>

<p class="src0">void flipIt(void* buffer)<span class="kom">// Prohod� �ervenou a modrou slo�ku pixel� v obr�zku</span></p>
<p class="src0">{</p>
<p class="src1">void* b = buffer;<span class="kom">// Ukazatel na buffer</span></p>
<p class="src"></p>
<p class="src1">__asm <span class="kom">// ASM k�d</span></p>
<p class="src1">{</p>
<p class="src3">mov ecx, 256*256 <span class="kom">// ��d�c� &quot;prom�nn�&quot; cyklu</span></p>
<p class="src3">mov ebx, b <span class="kom">// Ebx ukazuje na data</span></p>
<p class="src"></p>
<p class="src2">label: <span class="kom">// N�v�t� pro cyklus</span></p>
<p class="src3">mov al, [ebx+0] <span class="kom">// P�esune B slo�ku do al</span></p>
<p class="src3">mov ah, [ebx+2] <span class="kom">// P�esune R slo�ku do ah</span></p>
<p class="src3">mov [ebx+2], al <span class="kom">// Vlo�� B na spr�vnou pozici</span></p>
<p class="src3">mov [ebx+0], ah <span class="kom">// Vlo�� R na spr�vnou pozici</span></p>
<p class="src"></p>
<p class="src3">add ebx, 3 <span class="kom">// P�esun na dal�� t�i byty</span></p>
<p class="src3">dec ecx <span class="kom">// Dekrementuje ��ta�</span></p>
<p class="src3">jnz label <span class="kom">// Pokud se ��ta� nerovn� nule skok na n�v�t�</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jak u� z n�zvu funkce OpenAVI() vypl�v�, otev�r� AVI soubor. Parametr szFile je �et�zec s diskovou cestou k souboru. �et�zec title pou�ijeme pro zobrazen� informac� o AVI do titulku okna.</p>

<p class="src0">void OpenAVI(LPCSTR szFile)<span class="kom">// Otev�e AVI soubor</span></p>
<p class="src0">{</p>
<p class="src1">TCHAR title[100];<span class="kom">// Pro vyps�n� textu do titulku okna</span></p>

<p>Abychom inicializovali knihovnu AVI file, zavol�me AVIFileInit(). Existuje mnoho zp�sob�, jak otev��t video soubor. Rozhodl jsem se pou��t AVIStreamOpenFromFile(), kter� otev�e jeden datov� proud. Pavi p�edstavuje ukazatel na buffer, kam funkce vrac� handle nov�ho proudu, szFile ozna�uje diskovou cestu k souboru. T�et� parametr ur�uje typ proudu, kter� si p�ejeme otev��t. V tomto projektu n�s zaj�m� pouze video. Nula, dal�� parametr, oznamuje, �e se m� pou��t prvn� v�skyt proudu streamtypeVIDEO - v AVI jich m��e b�t v�ce. OF_READ definuje, �e n�m sta�� otev�en� pouze pro �ten� a NULL na konci je ukazatel na t��dn� identifik�tor handleru (P�ekl.: class identifier of the handler). Abych byl up��mn� nem�m nejmen�� p�edstavu, co to znamen�, proto pomoc� NULL nech�v�m knihovnu, aby vybrala za m�.</p>

<p>Nastanou-li p�i otev�r�n� jak�koli probl�my, zobraz� se u�ivateli informa�n� okno, nicm�n� ukon�en� programu nen� implementov�no. P�id�n� n�jak�ho druhu chybov�ch test� by pro v�s nem�lo b�t moc t�k�, j� jsem byl p��li� l�n�.</p>

<p class="src1">AVIFileInit();<span class="kom">// P�iprav� knihovnu AVIFile na pou�it�</span></p>
<p class="src"></p>
<p class="src1">if (AVIStreamOpenFromFile(&amp;pavi, szFile, streamtypeVIDEO, 0, OF_READ, NULL) != 0)<span class="kom">// Otev�e AVI proud</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Chybov� zpr�va</span></p>
<p class="src2">MessageBox (HWND_DESKTOP, &quot;Failed To Open The AVI Stream&quot;, &quot;Error&quot;, MB_OK | MB_ICONEXCLAMATION);</p>
<p class="src1">}</p>

<p>Pokud jsme se dostali a� sem, m��eme p�edpokl�dat, �e se soubor otev�el v po��dku a video proud byl lokalizov�n. U deklarace prom�nn�ch jsme vytvo�ili objekt struktury AVISTREAMINFO a nazvali ho psi. Vol�n�m funkce AVIStreamInfo() do n�j nagrabujeme r�zn� informace o AVI, s jejich� pomoc� spo��t�me ���ku a v��ku sn�mku v pixelech. Potom funkc� AVIStreamLength() z�sk�me ��slo posledn�ho sn�mku videa, kter� z�rove� ozna�uje celkov� po�et v�ech sn�mk�.</p>

<p>V�po�et framerate je snadn�. Po�et sn�mk� za sekundu se rovn� psi.dwRate d�leno psi.dwScale. Tato hodnota by m�la odpov�dat ��slu, kter� lze z�skat kliknut�m na AVI soubor a zvolen�m vlastnost�. Pt�te se, co to m� co spole�n�ho s mpf (�as zobrazen� jednoho sn�mku)? Kdy� jsem poprv� psal k�d pro animaci, zkou�el jsem pro zvolen� spr�vn�ho sn�mku animace pou��t FPS. Dostal jsem se do probl�m�... v�echna videa se p�ehr�vala p��li� rychle. Proto jsem nahl�dl do vlastnost� video souboru face2.avi. Je dlouh� 3,36 sekund, framerate �in� 29,974 FPS a m� celkem 91 sn�mk�. Pokud vyn�sob�me 3,36 kr�t 29,976 dostaneme 100 sn�mk� - velmi nep�esn�.</p>

<p>Proto jsem se rozhodl d�lat v�ci trochu jinak. Nam�sto po�tu sn�mk� za sekundu spo��t�me, jak dlouho by m�l b�t sn�mek zobrazen. Funkce AVIStreamSampleToTime() zkonvertuje pozici v animaci na �as v milisekund�ch, ne� se video dostane do t�to pozice. Z�sk�me tedy �as posledn�ho sn�mku, vyd�l�me ho jeho pozic� (=po�tem v�ech sn�mk�) a v�sledek vlo��me do prom�nn� mpf. Stejn� hodnoty byste dos�hli nagrabov�n�m mno�stv� �asu pot�ebn�ho pro jeden sn�mek. P��kaz by vypadal takto: AVIStreamSampleToTime(pavi, 1). Oba zp�soby jsou mo�n�. D�kuji Albertu Chaulkovi za n�pad.</p>

<p class="src1">AVIStreamInfo(pavi, &amp;psi, sizeof(psi));<span class="kom">// Na�te informace o proudu</span></p>
<p class="src"></p>
<p class="src1">width = psi.rcFrame.right - psi.rcFrame.left;<span class="kom">// V�po�et ���ky</span></p>
<p class="src1">height = psi.rcFrame.bottom - psi.rcFrame.top;<span class="kom">// V�po�et v��ky</span></p>
<p class="src"></p>
<p class="src1">lastframe = AVIStreamLength(pavi);<span class="kom">// Posledn� sn�mek proudu</span></p>
<p class="src1">mpf = AVIStreamSampleToTime(pavi, lastframe) / lastframe;<span class="kom">// Po�et milisekund na jeden sn�mek</span></p>

<p>OpenGL po�aduje, aby rozm�ry textury byly mocninou ��sla 2, ale v�t�ina vide� m�v� velikost 160x120, 320x240 nebo jin� nevhodn� hodnoty. Pro konverzi na pot�ebn� rozm�ry pou�ijeme Windows funkce pro pr�ci s DIB obr�zky. Jako prvn� v�c specifikujeme hlavi�ku bitmapy a to tak, �e vypln�me BITMAPINFOHEADER prom�nnou bmih. Nastav�me velikost struktury a biPlanes. Barevnou hloubku ur��me na 24 bit� (RGB), obr�zek bude m�t rozm�ry 256x256 pixel� a nebude komprimovan�.</p>

<p class="src1">bmih.biSize = sizeof(BITMAPINFOHEADER);<span class="kom">// Velikost struktury</span></p>
<p class="src1">bmih.biPlanes = 1;<span class="kom">// BiPlanes</span></p>
<p class="src1">bmih.biBitCount = 24;<span class="kom">// Po�et bit� na pixel</span></p>
<p class="src1">bmih.biWidth = 256;<span class="kom">// ���ka bitmapy</span></p>
<p class="src1">bmih.biHeight = 256;<span class="kom">// V��ka bitmapy</span></p>
<p class="src1">bmih.biCompression = BI_RGB;<span class="kom">// RGB m�d</span></p>

<p>Funkce CreateDibSection() vytvo�� obr�zek DIB, do kter�ho budeme moci p��mo zapisovat. Pokud v�e prob�hne v po��dku m�l by hBitmap obsahovat nov� vytvo�en� obr�zek. Hdc p�edstavuje handle kontextu za��zen�, druh� parametr je ukazatel na strukturu, kterou jsme pr�v� inicializovali. T�et� parametr specifikuje RGB typ dat. Do prom�nn� data se ulo�� ukazatel na data vytvo�en�ho obr�zku. Nastav�me-li p�edposledn� parametr na NULL, funkce za n�s sama alokuje pam�. Posledn� parametr budeme jednodu�e ignorovat. P��kaz SelectObject() zvol� obr�zek do kontextu za��zen�.</p>

<p class="src1">hBitmap = CreateDIBSection(hdc, (BITMAPINFO*)(&amp;bmih), DIB_RGB_COLORS, (void**)(&amp;data), NULL, NULL);</p>
<p class="src1">SelectObject(hdc, hBitmap);<span class="kom">// Zvol� bitmapu do kontextu za��zen�</span></p>

<p>P�edt�m ne� budeme moci na��tat jednotliv� sn�mky, mus�me p�ipravit program na dekomprimaci videa. Zavol�me funkci AVIStreamGetFrameOpen() a p�ed�me j� ukazatel na datov� proud videa. Za druh� parametr se m��e p�edat struktura podobn� t� v��e, pomoc� kter� lze specifikovat vr�cen� video form�t. Bohu�el jedinou v�c�, kterou lze ovlivnit je ���ka a v��ka obr�zku. V MSDN se tak� uv�d�, �e se m��e p�edat AVIGETFRAMEF_BESTDISPLAYFMT, kter� automaticky zvol� nejlep�� form�t zobrazen�. Nicm�n� m�j kompil�tor nem� pro tuto symbolickou konstantu ��dnou definici. Dopadne-li v�e dob�e, z�sk�me GETFRAME objekt pot�ebn� pro �ten� dat jednotliv�ch sn�mk�. P�i probl�mech se zobraz� chybov� okno.</p>

<p class="src1">pgf = AVIStreamGetFrameOpen(pavi, NULL);<span class="kom">// Vytvo�� PGETFRAME pou�it�m po�adovan�ho m�du</span></p>
<p class="src"></p>
<p class="src1">if (pgf == NULL)<span class="kom">// Ne�sp�ch?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox (HWND_DESKTOP, &quot;Failed To Open The AVI Frame&quot;, &quot;Error&quot;, MB_OK | MB_ICONEXCLAMATION);</p>
<p class="src1">}</p>

<p>Jako t�e�ni�ku na dortu zobraz�me do titulku okna ���ku, v��ku a po�et sn�mk� videa.</p>

<p class="src1"><span class="kom">// Informace o videu (���ka, v��ka, po�et sn�mk�)</span></p>
<p class="src1">wsprintf (title, &quot;NeHe's AVI Player: Width: %d, Height: %d, Frames: %d&quot;, width, height, lastframe);</p>
<p class="src1">SetWindowText(g_window-&gt;hWnd, title);<span class="kom">// Modifikace titulku okna</span></p>
<p class="src0">}</p>

<p>Otev�r�n� AVI prob�hlo bez probl�m�, n�sleduj�c� funkce nagrabuje jeho jeden sn�mek, zkonvertuje ho do pou�iteln� formy (velikost, barevn� hloubka RGB) a vytvo�� z n�j texturu. Prom�nn� lpbi bude ukl�dat informace o hlavi�ce bitmapy sn�mku. P��kaz na dal��m ��dku pln� hned n�kolik funkc�. Nagrabuje sn�mek specifikovan� pomoc� frame a vypln� lpbi informacemi o hlavi�ce sn�mku. P�esko�en�m hlavi�ky (lpbi-&gt;biSize) a informac� o barv�ch (lpbi-&gt;biClrUsed * sizeof(RGBQUAD)) z�sk�me ukazatel na opravdov� data obr�zku.</p>

<p class="src0">void GrabAVIFrame(int frame)<span class="kom">// Grabuje po�adovan� sn�mek z proudu</span></p>
<p class="src0">{</p>
<p class="src1">LPBITMAPINFOHEADER lpbi;<span class="kom">// Hlavi�ka bitmapy</span></p>
<p class="src"></p>
<p class="src1">lpbi = (LPBITMAPINFOHEADER)AVIStreamGetFrame(pgf, frame);<span class="kom">// Grabuje data z AVI proudu</span></p>
<p class="src1">pdata = (char *)lpbi + lpbi-&gt;biSize + lpbi-&gt;biClrUsed * sizeof(RGBQUAD);<span class="kom">// Ukazatel na data</span></p>

<p>Kv�li textu�e mus�me zkonvertovat pr�v� z�skan� obr�zek na pou�itelnou velikost a barevnou hloubku. Pomoc� funkce DrawDibDraw() m��eme kreslit p��mo do na�eho DIBu. Jej� prvn� parametr je DrawDib DC, dal�� parametr p�edstavuje handle na kontext za��zen�. Nuly definuj� lev� horn� a 256 prav� doln� roh v�sledn�ho obd�ln�ku. Lpbi je ukazatel na hlavi�ku sn�mku, kter� jsme pr�v� na�etli, a pdata ukazuje na data obr�zku. N�sleduje lev� horn� a prav� doln� roh zdrojov�ho obr�zku (�ili ���ka a v��ka sn�mku). Posledn� parametr nech�me na nule. Touto cestou m��eme zkonvertovat obr�zek o jak�koli ���ce, v��ce a barevn� hloubce na obr�zek 256x256x24.</p>

<p class="src1"><span class="kom">// Konvertov�n� obr�zku na po�adovan� form�t</span></p>
<p class="src1">DrawDibDraw(hdd, hdc, 0, 0, 256, 256, lpbi, pdata, 0, 0, width, height, 0);</p>

<p>V sou�asn� chv�li u� v ruk�ch dr��me data, ze kter�ch lze vygenerovat texturu. Nicm�n� jej� R a B slo�ky jsou prohozeny. Proto zavol�me na�i ASM funkce, kter� jednotliv� byty um�st� na korektn� pozice v obr�zku.</p>

<p class="src1">flipIt(data);<span class="kom">// Prohod� R a B slo�ku pixel�</span></p>

<p>P�vodn� jsem texturu aktualizoval jej�m smaz�n�m a znovuvytvo�en�m. N�kolik lid� mi nez�visle na sob� poradilo, abych zkusil pou��t glTexSubImage2D(). Uv�d�m citaci z OpenGL Red Book: &quot;Vytvo�en� textury m��e b�t mnohem n�ro�n�j�� ne� modifikace u� existuj�c�. V OpenGL Release 1.1 p�ibyly nov� rutiny pro nahrazen� v�ech ��st� textury za nov� informace. Toto m��e b�t u�ite�n� pro programy, kter� nap�. v real-timu sn�maj� obr�zky videa a vytv��ej� z nich textury. Aplikace pak za b�hu vytvo�� pouze jednu texturu a pomoc� glTexSubImage2D() bude postupn� nahrazovat jej� data za nov� sn�mky videa.&quot;</p>

<p>Osobn� jsem nezaznamenal v�t�� n�r�st rychlosti, ale na pomalej��ch kart�ch m��e b�t v�e jinak. Parametry funkce jsou n�sleduj�c�: typ v�stupu, �rove� detail� pro mipmapping, x a y offset po��tku kop�rovan� oblasti (0, 0 - lev� doln� roh), ���ka a v��ka oblasti, RGB form�t pixel�, typ dat a ukazatel na data.</p>

<p>Kevin Rogers p�idal: Cht�l bych pouk�zat na dal�� d�le�itou vlastnost glTexSubImage2d(). Nejen, �e je rychlej�� na mnoha OpenGL implementac�ch, ale c�lov� oblast obr�zku nemus� b�t nutn� mocninou ��sla 2. Toto je p�edev��m u�ite�n� pro p�ehr�v�n� videa, jeho� rozli�en� b�v� mocninou dvojky opravdu z��dka (v�t�inou 320x200). Dost�v�me tak flexibiln� mo�nost p�ehr�vat video v jeho origin�ln� velikosti ne� jej slo�it� m�nit, n�kdy i dvakr�t (do textury, zp�t na obrazovku).</p>

<p>Nen� mo�n� aktualizovat texturu, pokud jste ji je�t� nevytvo�ili! My ji vytv���me v k�du funkce Initialize(). Druh� d�le�it� v�c spo��v� v tom, �e pokud v� projekt obsahuje v�ce ne� jednu texturu, mus�te p�ed aktualizac� zvolit jako aktivn� (glBindTexture()) tu spr�vnou, proto�e byste mohli p�epsat texturu, kterou nechcete.</p>


<p class="src1">glTexSubImage2D(GL_TEXTURE_2D, 0, 0, 0, 256, 256, GL_RGB, GL_UNSIGNED_BYTE, data);<span class="kom">// Aktualizace textury</span></p>
<p class="src0">}</p>

<p>N�sleduj�c� funkce je vol�na p�i ukon�ov�n� programu. M� za �kol smazat DrawDib DC a uvolnit alokovan� zdroje. Zav�r� tak� GetFrame zdroj, odstra�uje souborov� proud a ukon�uje pr�ci s AVI souborem.</p>

<p class="src0">void CloseAVI(void)<span class="kom">// Zav�en� AVI souboru</span></p>
<p class="src0">{</p>
<p class="src1">DeleteObject(hBitmap);<span class="kom">// Sma�e bitmapu</span></p>
<p class="src1">DrawDibClose(hdd);<span class="kom">// Zav�e DIB</span></p>
<p class="src1">AVIStreamGetFrameClose(pgf);<span class="kom">// Dealokace GetFrame zdroje</span></p>
<p class="src1">AVIStreamRelease(pavi);<span class="kom">// Uvoln�n� proudu</span></p>
<p class="src1">AVIFileExit();<span class="kom">// Uvoln�n� souboru</span></p>
<p class="src0">}</p>

<p>Inicializace je hezky p��mo�ar�. nastav�me po��te�n� �hel na nulu a pomoc� knihovny DrawDib nagrabujeme DC. Pokud se v�e zda��, tak by se m�lo hdd st�t handlem na nov� vytvo�en� kontext za��zen�. D�le ur��me �ern� pozad�, zapneme hloubkov� testov�n� atd.</p>

<p class="src0">BOOL Initialize (GL_Window* window, Keys* keys)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;</p>
<p class="src1">g_keys = keys;</p>
<p class="src"></p>
<p class="src1">angle = 0.0f;<span class="kom">// Na po��tku nulov� �hel</span></p>
<p class="src1">hdd = DrawDibOpen();<span class="kom">// Kontext za��zen� DIBu</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ test� hloubky</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivn� korekce</span></p>

<p>V dal�� ��sti k�du zapneme mapov�n� 2D textur, nastav�me filtr GL_NEAREST a definujeme kulov� mapov�n�, kter� umo�n� automatick� generov�n� texturov�ch koordin�t�. Pokud m�te v�konn� syst�m, zkuste pou��t line�rn� filtrov�n�, bude vypadat l�pe.</p>

<p class="src1">quadratic = gluNewQuadric();<span class="kom">// Vytvo�� objekt quadraticu</span></p>
<p class="src1">gluQuadricNormals(quadratic, GLU_SMOOTH);<span class="kom">// Norm�ly</span></p>
<p class="src1">gluQuadricTexture(quadratic, GL_TRUE);<span class="kom">// Texturov� koordin�ty</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturov�n�</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER, GL_NEAREST);<span class="kom">// Filtry textur</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER, GL_NEAREST);</p>
<p class="src"></p>
<p class="src1">glTexGeni(GL_S, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);<span class="kom">// Automatick� generov�n� koordin�t�</span></p>
<p class="src1">glTexGeni(GL_T, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);</p>

<p>Po obvykl� inicializaci otev�eme AVI soubor. Jist� jste si v�imli, �e jsem se sna�il udr�et rozhran� v co nejjednodu��� form�, tak�e sta�� p�edat pouze �et�zec se jm�nem souboru. Na konci vytvo��me texturu a ukon��me funkci.</p>

<p class="src1">OpenAVI(&quot;data/face2.avi&quot;);<span class="kom">// Otev�en� AVI souboru</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vytvo�en� textury</span></p>
<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, GL_RGB, 256, 256, 0, GL_RGB, GL_UNSIGNED_BYTE, data);</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�e OK</span></p>
<p class="src0">}</p>

<p>P�i deinicializaci zavol�me CloseAVI(), ��m� kompletn� ukon��me pr�ci s videem.</p>

<p class="src0">void Deinitialize(void)<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">CloseAVI();<span class="kom">// Zav�e AVI</span></p>
<p class="src0">}</p>

<p>Ve funkci Update() zji��ujeme p��padn� stisky kl�ves a v z�vislosti na uplynul�m �ase aktualizujeme pom�ry ve sc�n�. Jako v�dy ESC ukon�uje program a F1 p�ep�n� m�d fullscreen/okno. Mezern�kem inkrementujeme prom�nnou efekt, jej� hodnota ur�uje, jestli se ve sc�n� zobrazuje krychle, koule, v�lec, pop�. nic (pouze pozad�).</p>

<p class="src0">void Update(DWORD milliseconds)<span class="kom">// Aktualizace sc�ny</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown[VK_ESCAPE] == TRUE)<span class="kom">// ESC</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication (g_window);<span class="kom">// Konec programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_F1] == TRUE)<span class="kom">// F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen (g_window);<span class="kom">// Zam�n� m�d fullscreen/okno</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if ((g_keys-&gt;keyDown[' ']) &amp;&amp; !sp)<span class="kom">// Mezern�k</span></p>
<p class="src1">{</p>
<p class="src2">sp = TRUE;</p>
<p class="src2">effect++;<span class="kom">// N�sleduj�c� objekt v �ad�</span></p>
<p class="src"></p>
<p class="src2">if (effect &gt; 3)<span class="kom">// P�ete�en�?</span></p>
<p class="src2">{</p>
<p class="src3">effect = 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown[' '])<span class="kom">// Uvoln�n� mezern�ku</span></p>
<p class="src1">{</p>
<p class="src2">sp = FALSE;</p>
<p class="src1">}</p>

<p>Pomoc� kl�vesy B zap�n�me/vyp�n�me pozad�. Generov�n� texturov�ch koordin�t� ur�uje flag env, kter� negujeme po stisku kl�vesy E.</p>

<p class="src1">if ((g_keys-&gt;keyDown['B']) &amp;&amp; !bp)<span class="kom">// Kl�vesa B</span></p>
<p class="src1">{</p>
<p class="src2">bp = TRUE;</p>
<p class="src2">bg = !bg;<span class="kom">// Nastav� flag pro zobrazov�n� pozad�</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown['B'])<span class="kom">// Uvoln�n� B</span></p>
<p class="src1">{</p>
<p class="src2">bp = FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if ((g_keys-&gt;keyDown['E']) &amp;&amp; !ep)<span class="kom">// Kl�vesa E</span></p>
<p class="src1">{</p>
<p class="src2">ep = TRUE;</p>
<p class="src2">env = !env;<span class="kom">// Nastav� flag pro automatick� generov�n� texturov�ch koordin�t�</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown['E'])<span class="kom">// Uvoln�n� E</span></p>
<p class="src1">{</p>
<p class="src2">ep = FALSE;</p>
<p class="src1">}</p>

<p>V z�vislosti na uplynul�m �ase zv�t��me �hel nato�en� objektu.</p>

<p class="src1">angle += (float)(milliseconds) / 60.0f;<span class="kom">// Aktualizace �hlu nato�en�</span></p>

<p>V origin�ln� verzi tutori�lu byla v�echna videa p�ehr�v�na v�dy stejnou rychlost� a to nebylo p��li� vhodn�. Proto jsem k�d p�epsal tak, aby jeho rychlost byla v�dy korektn�. Obsah prom�nn� next zv�t��me o po�et uplynul�ch milisekund od mil�ho vol�n�. Jist� si pamatujete, �e mpf obsahuje �as, jak dlouho m� b�t ka�d� sn�mek zobrazen. Vyd�l�me-li tedy ��slo next hodnotou mpf, z�sk�me spr�vn� sn�mek. Nakonec se ujist�me, �e nov� vypo�ten� sn�mek nep�etekl p�es maxim�ln� hodnotu. V takov�m p��pad� za�neme video p�ehr�vat znovu od za��tku.</p>

<p>Asi v�s nep�ekvap�, �e pokud je po��ta� p��li� pomal�, n�kter� sn�mky se automaticky p�eskakuj�. Pokud chcete, aby byl ka�d� sn�mek zobrazen, p�i�em� nez�vis� na tom, jak pomalu program b��, m��ete otestovat, jestli je next vy��� ne� mpf a pokud ano, inkrementujte sn�mek o jedni�ku a resetujte next zp�t na nulu. Oba zp�soby pracuj�, ale pro rychl� po��ta�e je vhodn�j�� uveden� k�d.</p>

<p>C�t�te-li se plni s�ly a energie, zkuste implementovat obvykl� funkce video p�ehr�va�� - nap�. rychl� p�ev�jen�, pauzu nebo zp�tn� chod.</p>

<p class="src1">next += milliseconds;<span class="kom">// Zv�t�en� next o uplynul� �as</span></p>
<p class="src1">frame = next / mpf;<span class="kom">// V�po�et aktu�ln�ho sn�mku</span></p>
<p class="src"></p>
<p class="src1">if (frame &gt;= lastframe)<span class="kom">// P�ete�en� sn�mk�?</span></p>
<p class="src1">{</p>
<p class="src2">frame = 0;<span class="kom">// P�eto�� video na za��tek</span></p>
<p class="src2">next = 0;<span class="kom">// Nulov�n� �asu</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>U� m�me t�m�� v�e, zb�v� pouze vykreslov�n� sc�ny. Jako v�dy na za��tku sma�eme obrazovku a hloubkov� buffer. Potom nagrabujeme po�adovan� sn�mek animace. Pokud byste cht�li sou�asn� pou��vat v�ce vide�, museli byste p�idat i ID textury - dal�� pr�ce pro v�s.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e buffery</span></p>
<p class="src"></p>
<p class="src1">GrabAVIFrame(frame);<span class="kom">// Nagrabuje po�adovan� sn�mek videa</span></p>

<p>Chceme-li kreslit pozad�, resetujeme modelview matici a na oby�ejn� obd�ln�k namapujeme dan� sn�mek videa. Aby se objevil a� za v�emi objekty, um�st�me ho dvacet jednotek do sc�ny a samoz�ejm� ho rozt�hneme na po�adovanou velikost.</p>

<p class="src1">if (bg)<span class="kom">// Zobrazuje se pozad�?</span></p>
<p class="src1">{</p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);<span class="kom">// Vykreslov�n� obd�ln�k�</span></p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 11.0f, 8.3f,-20.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-11.0f, 8.3f,-20.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-11.0f,-8.3f,-20.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 11.0f,-8.3f,-20.0f);</p>
<p class="src2">glEnd();</p>
<p class="src1">}</p>

<p>Resetujeme matici a p�esuneme se deset jednotek do sc�ny. Pokud se env rovn� TRUE, zapneme automatick� generov�n� texturov�ch koordin�t�.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -10.0f);<span class="kom">// Posun do sc�ny</span></p>
<p class="src"></p>
<p class="src1">if (env)<span class="kom">// Zapnuto generov�n� sou�adnic textur?</span></p>
<p class="src1">{</p>
<p class="src2">glEnable(GL_TEXTURE_GEN_S);</p>
<p class="src2">glEnable(GL_TEXTURE_GEN_T);</p>
<p class="src1">}</p>

<p>Na posledn� chv�li jsem p�idal i rotaci objektu na os�ch x, y a n�sledn� p�ibl�en� na ose z. Objekt se bude pohybovat po sc�n�. Bez t�chto t�� ��dk� by pouze rotoval na jednom m�st� uprost�ed obrazovky.</p>

<p class="src1">glRotatef(angle*2.3f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src1">glRotatef(angle*1.8f, 0.0f, 1.0f, 0.0f);</p>
<p class="src1">glTranslatef(0.0f, 0.0f, 2.0f);<span class="kom">// P�esun na novou pozici</span></p>

<p>Pomoc� v�tven� do v�ce sm�r� vykresl�me objekt, kter� je pr�v� aktivn�. Jako prvn� mo�nost m�me krychli.</p>

<p class="src1">switch (effect)<span class="kom">// V�tven� podle efektu</span></p>
<p class="src1">{</p>
<p class="src2">case 0:<span class="kom">// Krychle</span></p>
<p class="src3">glRotatef(angle*1.3f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(angle*1.1f, 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glRotatef(angle*1.2f, 0.0f, 0.0f, 1.0f);</p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�k�</span></p>
<p class="src4"><span class="kom">// �eln� st�na</span></p>
<p class="src4">glNormal3f(0.0f, 0.0f, 0.5f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src4"><span class="kom">// Zadn� st�na</span></p>
<p class="src4">glNormal3f(0.0f, 0.0f,-0.5f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src4"><span class="kom">// Horn� st�na</span></p>
<p class="src4">glNormal3f(0.0f, 0.5f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src4"><span class="kom">// Spodn� st�na</span></p>
<p class="src4">glNormal3f(0.0f,-0.5f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src4"><span class="kom">// Prav� st�na</span></p>
<p class="src4">glNormal3f(0.5f, 0.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src4"><span class="kom">// Lev� st�na</span></p>
<p class="src4">glNormal3f(-0.5f, 0.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src3">glEnd();</p>
<p class="src3">break;</p>

<p>Jak vykreslit kouli, u� jist� d�vno v�te, nicm�n� pro jistotu p�id�v�m kr�tk� koment��. Jej� polom�r �in� 1.3f jednotek, skl�d� se z dvaceti poledn�k� a dvaceti rovnob�ek. Pou��v�m ��slo 20, proto�e chci, aby nebyla perfektn� hladk�, ale trochu segmentovan� - bude vid�t n�znak jej� rotace.</p>

<p class="src2">case 1:<span class="kom">// Koule</span></p>
<p class="src3">glRotatef(angle*1.3f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(angle*1.1f, 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glRotatef(angle*1.2f, 0.0f, 0.0f, 1.0f);</p>
<p class="src"></p>
<p class="src3">gluSphere(quadratic, 1.3f, 20, 20);<span class="kom">// Vykreslen� koule</span></p>
<p class="src3">break;</p>

<p>V�lec vykresl�me pomoc� funkce gluCylinder(). Bude m�t pr�m�r 1.0f a jeho v��ka bude �init t�i jednotky.</p>

<p class="src2">case 2:<span class="kom">// V�lec</span></p>
<p class="src3">glRotatef(angle*1.3f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(angle*1.1f, 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glRotatef(angle*1.2f, 0.0f, 0.0f, 1.0f);</p>
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrov�n�</span></p>
<p class="src"></p>
<p class="src3">gluCylinder(quadratic, 1.0f, 1.0f, 3.0f, 32, 32);<span class="kom">// Vykreslen� v�lce</span></p>
<p class="src3">break;</p>
<p class="src1">}</p>

<p>Pokud je env v jedni�ce, vypneme generov�n� texturov�ch koordin�t�.</p>

<p class="src1">if (env)<span class="kom">// Zapnuto generov�n� sou�adnic textur?</span></p>
<p class="src1">{</p>
<p class="src2">glDisable(GL_TEXTURE_GEN_S);</p>
<p class="src2">glDisable(GL_TEXTURE_GEN_T);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vypr�zdn� OpenGL pipeline</span></p>
<p class="src0">}</p>

<p>Douf�m, �e jste si, stejn� jako j�, u�ili tento tutori�l. Za chv�li budou 2 hodiny r�no... u� na n�m pracuji p�es �est hodin. Zn� to ��len�, ale psan� textu, aby d�val smysl, nen� lehk� �kol. V�e jsem t�ikr�t p�e�etl a sna�il se objasnit v�ci co nejl�pe. V��te nebo ne, pro m� je d�le�it�, abyste pochopili, jak v�ci pracuj� a pro� v�bec pracuj�. Bez �ten��� bych brzy skon�il.</p>

<p>Jak u� jsem napsal, toto je m�j prvn� pokus o p�ehr�v�n� videa. Norm�ln� nep�i o p�edm�tu, kter� jsem se pr�v� nau�il, ale mysl�m, �e mi to pro jednou odpust�te. Faktem je, �e jsem si od ciz�ch lid� p�j�il opravdu absolutn� minimum k�du, v�e je p�vodn�. Douf�m, �e se mi poda�ilo otev��t dve�e povodni p�ehr�v�n� AVI ve va�ich kvalitn�ch demech. Mo�n� se tak stane, mo�n� ne. Ka�dop�dn� uk�zkov� tutori�l u� m�te.</p>

<p>Obrovsk� d�ky pat�� Fredsterovi, kter� vytvo�il uk�zkov� video tv��e. Byla to jedna z celkem �esti animac�, kter� mi poslal. ��dn� dotazy, ��dn� po�adavky. Poslal jsem mu email s prosbou a on mi pomohl. Obrovsk� respekt.</p>

<p>Nejv�t�� d�k v�ak pat�� Jonathanu de Blok. Neb�t jeho, tento tutori�l by nevznikl. Pr�v� on ve mn� vzbudil z�jem o AVI form�t. Poslal mi toti� ��st k�du z jeho p�ehr�va�e. Trp�liv� odpov�dal na v�echny ot�zky ohledn� jeho k�du. Nic jsem si v�ak nep�j�il, m�j k�d pracuje na �pln� jin�m z�kladu.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson35.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson35_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson35.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson35.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson35.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:matthias.haack@epost.de">Matthias Haack</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson35.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(35);?>
<?FceNeHeOkolniLekce(35);?>

<?
include 'p_end.php';
?>
