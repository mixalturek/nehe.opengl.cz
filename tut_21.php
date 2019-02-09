<?
$g_title = 'CZ NeHe OpenGL - Lekce 21 - P��mky, antialiasing, �asov�n�, pravo�hl� projekce, z�kladn� zvuky a jednoduch� hern� logika';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(21);?>

<h1>Lekce 21 - P��mky, antialiasing, �asov�n�, pravo�hl� projekce, z�kladn� zvuky a jednoduch� hern� logika</h1>

<p class="nadpis_clanku">Prvn� opravdu rozs�hl� tutori�l - jak u� plyne z gigantick�ho n�zvu. Doufejme, �e takov� spousta informac� a technik dok�e ud�lat ��astn�m opravdu ka�d�ho. Str�vil jsem dva dny k�dov�n�m a kolem dvou t�dn� psan�m tohoto HTML souboru. Pokud jste n�kdy hr�li hru Admiar, lekce v�s vr�t� do vzpom�nek. �kol hry sest�v� z vypln�n� jednotliv�ch pol��ek m���ky. Samoz�ejm� se mus�te vyh�bat v�em nep��tel�m.</p>

<p>N�m�t t�to lekce je vcelku slo�it�. V�m, �e spousta z v�s je unavena studiem z�klad�. Ka�d� by zem�el pro zvl�tnosti 3D objekt�, multitexturingu a podobn�. T�mto lidem se omlouv�m, proto�e chci zachovat postupn� nabalov�n� znalost�. Po velk�m skoku vp�ed nen� u kr��ku zp�t snadn� udr�et z�jem �ten���. J� osobn� preferuji konstantn� tempo. Mo�n� jsem ztratil n�kolik z v�s, ale douf�m, �e ne p��li� mnoho. Do dne�ka se ve v�ech m�ch tutori�lech objevovaly polygony, obd�ln�ky a troj�heln�ky. Pravd�podobn� jste si v�imli ne�mysln� diskriminace :-) �ar, p��mek, linek a podobn�ch jednorozm�rn�ch �tvar�. O n�kolik hodin pozd�ji za�al vznikat Line Tutori�l. Vypadal v klidu, ale tot�ln� nudn�! Linky jsou skv�l�, ale v porovn�n� s n�kter�mi efekty nic moc. Shrnuto: rozhodl jsem se napsat multi-tutori�l. Na konci lekce bychom m�li m�t vytvo�enu jednoduchou hru typu 'Admiar'. Mis� bude vyplnit pol��ka m���ky. Hr��e nesm� chytit nep��tel� - jak jinak. Implementujeme levely, etapy, �ivoty, zvuky a k�dy - k pr�chodu skrz levely, kdy� se v�ci stanou p��li� obt��n�mi. A�koli hru spust�te i na Pentiu 166 s Voodoo 2, rychlej�� procesor nebude na �kodu.</p>

<p>Roz����me standardn� k�d z lekce jedna. P�id�me pot�ebn� hlavi�kov� soubory - stdio.h pro souborov� operace a stdarg.h kv�li v�stupu prom�nn�ch (level, obt��nost ap.).</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src0">#include &lt;stdarg.h&gt;<span class="kom">// Hlavi�kov� soubor pro funkce s prom�nn�m po�tem parametr�</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Priv�tn� GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trval� Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na�eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>Deklarujeme prom�nn�. Pole vline ukl�d� z�znamy o 121 vertik�ln�ch link�ch, kter� tvo�� m���ku. 11 p��mek zleva doprava a 11 �as ze shora dol�. Hline ukl�d� 121 horizont�ln�ch p��mek. Ap pou��v�me ke zji�t�n� stisku kl�vesy A. Filled je nastaveno na FALSE, jestli�e m���ka nen� kompletn� vypln�n� a TRUE pokud je. Gameover ukon�uje hru. Pokud se anti rovn� TRUE je zapnut antialiasing objekt�.</p>

<p class="src0">bool vline[11][10];<span class="kom">// Ukl�d� z�znamy o vertik�ln�ch link�ch</span></p>
<p class="src0">bool hline[10][11];<span class="kom">// Ukl�d� z�znamy o horizont�ln�ch link�ch</span></p>
<p class="src0">bool ap;<span class="kom">// Stisknuto 'A'?</span></p>
<p class="src0">bool filled;<span class="kom">// Bylo ukon�eno vypl�ov�n� m���ky?</span></p>
<p class="src0">bool gameover;<span class="kom">// Konec hry?</span></p>
<p class="src0">bool anti = TRUE;<span class="kom">// Antialiasing?</span></p>

<p>P�ich�zej� na �adu celo��seln� prom�nn�. Loop1 a loop2 u��v�me k ozna�en� bod� v hern� m���ce, zji�t�n� zda do n�s nep��tel nevrazil  a k vygenerov�n� randomov� pozice. Zastaven� pohybu nep��tel je implementov�no ��ta�em delay. Po dosa�en� ur�it� hodnoty se za�nou znovu h�bat a delay se zp�tky vynuluje.</p>

<p>Prom�nn� adjust je speci�ln�. I kdy� program obsahuje timer, tento timer pouze zji��uje, zda je po��ta� (pr�b�h programu) p��li� rychl� a v takov�m p��pad� ho zpomal�me. Na grafick� kart� GeForce hra b�� hodn� rychle. Po testu s PIII/450 s Voodoo 3500 TV si nelze nev�imnout extr�mn� lenosti. Probl�m spo��v� v k�du pro �asov�n�, kter� hru pouze zpomaluje. Zrychlen� j�m nelze prov�st. Vytvo�il jsem prom�nnou adjust, kter� m��e nab�vat nuly a� p�ti. ��m vy��� hodnota, t�m rychleji se objekty pohybuj� - podpora star��ch syst�m�. Nicm�n� nez�le��, jak rychl� je hra, absolutn� rychlost prov�d�n� programu se nikdy nezv���. Nastaven�m adjust na trojku vytvo��me kompromis pro pomal� i rychl� syst�my. V�ce o �asov�n� d�le.</p>

<p>Lives ukl�d� po�et �ivot�, level u��v�me k zaznamen�v�n� obt��nosti. Nen� to level, kter� se zobrazuje na monitoru. Level2 za��n� se stejnou hodnotou, ale m��e b�t inkrementov�n donekone�na - z�le�� na obratnosti hr��e. Pokud dok�e dos�hnout t�et�ho levelu, prom�nn� level se p�estane zvy�ovat. ur�uje pouze vnit�n� obt��nost hry. Stage definuje konkr�tn� etapu hry.</p>

<p class="src0">int loop1;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src0">int loop2;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src0">int delay;<span class="kom">// Doba zastaven� nep��tel</span></p>
<p class="src0">int adjust = 3;<span class="kom">// Rychlostn� kompenzace pro pomal� syst�my</span></p>
<p class="src0">int lives = 5;<span class="kom">// Po�et �ivot� hr��e</span></p>
<p class="src0">int level = 1;<span class="kom">// Vnit�n� obt��nost hry</span></p>
<p class="src0">int level2 = level;<span class="kom">// Zobrazovan� level</span></p>
<p class="src0">int stage = 1;<span class="kom">// Etapa/f�ze hry</span></p>

<p>Definujeme strukturu objektu - hr��, nep��tel ap. Vnit�n� prom�nn� fx a fy ukl�daj� pomocnou polohu pro plynul� pohyb (fx = fine x). X a y definuj� pozici na m���ce. Mohou nab�vat hodnot od nuly do deseti. Kdybychom se s hr��em po sc�n� pohybovali pomoc� t�chto dvou prom�nn�ch m�li bychom jeden�ct pozic vodorovn� a jeden�ct svisle. Hr�� by p�eskakoval z jednoho m�sta na druh�. Proto p�i pohybu pou��v�me up�es�uj�c� fx a fy. Posledn� prom�nnou spin pou��v�me pro ot��en� objekt� okolo osy z.</p>

<p class="src0">struct object<span class="kom">// Struktura objektu ve h�e</span></p>
<p class="src0">{</p>
<p class="src1">int fx, fy;<span class="kom">// Pohybov� pozice</span></p>
<p class="src1">int x, y;<span class="kom">// Absolutn� pozice</span></p>
<p class="src1">float spin;<span class="kom">// Ot��en� objektu dokola</span></p>
<p class="src0">};</p>

<p>Na z�klad� struktury vytvo��me hr��e, dev�t nep��tel a jeden speci�ln� objekt - sklen�n� p�es�pac� hodiny, kter� se sem tam objev�. Pokud je stihnete sebrat, nep��tel se na chv�li zastav�.</p>

<p class="src0">struct object player;<span class="kom">// Hr��</span></p>
<p class="src0">struct object enemy[9];<span class="kom">// Nep��tel�</span></p>
<p class="src0">struct object hourglass;<span class="kom">// Sklen�n� hodiny</span></p>

<p>Abychom prom�nn� pro �asova� m�li pohromad�, slou��me je do struktury. Frekvenci �asova�e deklarujeme jako 64-bitov� cel� ��slo. Resolution je perioda (obr�cen� hodnota frekvence). Mm_timer_start a mm_timer_elapsed udr�uj� po��te�n� a uplynul� �as. Pou��v�me je pouze tehdy, pokud po��ta� nem� performance counter (v p�ekladu: ��ta� proveden� nebo v�konu, z�stanu u anglick�ho term�nu). Logick� prom�nn� performance_timer bude nastavena na TRUE pokud program detekuje, �e po��ta� m� performance counter. Pokud ho nenajde budeme pro �asov�n� pou��vat m�n� p�esn�, ale celkov� dosta�uj�c� multimedi�ln� timer. Posledn� dv� prom�nn� jsou op�t 64-bitov� integery, kter� ukl�daj� �as spu�t�n� a uplynul� �as performance counteru. Prom�nnou na b�zi t�to struktury pojmenujeme timer.</p>

<p class="src0">struct <span class="kom">// Informace pro �asova�</span></p>
<p class="src0">{</p>
<p class="src1">__int64 frequency;<span class="kom">// Frekvence</span></p>
<p class="src1">float resolution;<span class="kom">// Perioda</span></p>
<p class="src1">unsigned long mm_timer_start;<span class="kom">// Startovn� �as multimedi�ln�ho timeru</span></p>
<p class="src1">unsigned long mm_timer_elapsed;<span class="kom">// Uplynul� �as multimedi�ln� timeru</span></p>
<p class="src1">bool performance_timer;<span class="kom">// U��v�me Performance Timer?</span></p>
<p class="src1">__int64 performance_timer_start;<span class="kom">// Startovn� �as Performance Timeru</span></p>
<p class="src1">__int64 performance_timer_elapsed;<span class="kom">// Uplynul� �as Performance Timeru</span></p>
<p class="src0">} timer;<span class="kom">// Struktura se jmenuje timer</span></p>

<p>N�sleduj�c� pole si m��eme p�edstavit jako tabulku rychlost�. objekt ve h�e se m��e pohybovat rozd�ln�mi rychlostmi. V�e z�vis� na prom�nn� adjust (v��e). Pokud se jej� hodnota rovn� nule pohybuj�c� se o pixel za ur�it� �as, pokud p�ti, rychlost �in� dvacet pixel�. Inkrementov�n�m adjust se na pomal�ch po��ta��ch zv��� rychlost (ale i &quot;trhanost&quot;) hry. Po�et pixel� kroku je v tabulce. Adjust pou��v�me jako index do tohoto pole.</p>

<p class="src0">int steps[6]={ 1, 2, 4, 5, 10, 20 };<span class="kom">// Krokovac� hodnota pro p�izp�soben� pomal�ho videa</span></p>

<p>Deklarujeme pole dvou textur - pozad� a bitmapov� font. Base ukazuje na prvn� display list fontu (viz. minul� tutori�ly). Funkce pro nahr�v�n� a vytv��en� textur nebudu opisovat, byly tu u� tolikr�t, �e je mus�te zn�t na zpam� (p�ekladatel).</p>

<p class="src0">GLuint texture[2];<span class="kom">// Dv� textury</span></p>
<p class="src0">GLuint base;<span class="kom">// Z�kladn� display list pro font</span></p>

<p>Inicializujeme �asova�. Za�neme vynulov�n�m v�ech prom�nn�ch. Potom zjist�me, zda budeme moci pou��vat performance counter. Pokud ano,ulo��me frekvenci do timer.frequency, pokud ne budeme pou��vat multimedi�ln� timer - nastav�me timer.performance_timer na FALSE a na�teme do po��te�n� hodnoty aktu�ln� �as. Timer.resolution definujeme na 0.001 (P�ekladatel: d�len� je celkem zbyte�n�) a timer.frequency na 1000. Proto�e je�t� neuplynul ��dn� �as, p�i�ad�me uplynul�mu �asu startovn� �as.</p>

<p class="src0">void TimerInit(void)<span class="kom">// Inicializace timeru</span></p>
<p class="src0">{</p>
<p class="src1">memset(&amp;timer, 0, sizeof(timer));<span class="kom">// Vynuluje prom�nn� struktury</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zjist� jestli je Performance Counter dostupn� a pokud ano, bude na�tena jeho frekvence</span></p>
<p class="src1">if (!QueryPerformanceFrequency((LARGE_INTEGER *) &amp;timer.frequency))</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Performance Counter nen� dostupn�</span></p>
<p class="src2">timer.performance_timer = FALSE;<span class="kom">// Nastav� Performance Timer na FALSE</span></p>
<p class="src2">timer.mm_timer_start = timeGetTime();<span class="kom">// Z�sk�n� aktu�ln�ho �asu</span></p>
<p class="src2">timer.resolution = 1.0f/1000.0f;<span class="kom">// Nastaven� periody</span></p>
<p class="src2">timer.frequency = 1000;<span class="kom">// Nastaven� frekvence</span></p>
<p class="src2">timer.mm_timer_elapsed = timer.mm_timer_start;<span class="kom">// Uplynul� �as = po��te�n�</span></p>
<p class="src1">}</p>

<p>M�-li po��ta� performance counter projdeme touto v�tv�. Nastav�me po��te�n� hodnotu a ozn�m�me, �e m��eme pou��vat performance counter. Pot� spo��t�me periodu pomoc� frekvence z�skan� v if() v��e. Perioda je p�evr�cen� hodnota frekvence. Nakonec nastav�me uplynul� �as na startovn�. V�imn�te si, �e m�sto sd�len� prom�nn�ch obou timer�, jsem se rozhodl pou��t r�zn�. Ob� cesty by pracovaly, ale tato je p�ehledn�j��.</p>

<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Performance Counter je mo�n� pou��vat</span></p>
<p class="src2">QueryPerformanceCounter((LARGE_INTEGER *) &amp;timer.performance_timer_start);<span class="kom">// Po��te�n� �as</span></p>
<p class="src2">timer.performance_timer = TRUE;<span class="kom">// Nastaven� Performance Timer na TRUE</span></p>
<p class="src2">timer.resolution = (float) (((double)1.0f)/((double)timer.frequency));<span class="kom">// Spo��t�n� periody</span></p>
<p class="src2">timer.performance_timer_elapsed = timer.performance_timer_start;<span class="kom">//Nastav� uplynul� �as na po��te�n�</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>V n�sleduj�c� funkci na�teme timer a vr�t�me uplynul� �as v milisekund�ch. Deklarujeme 64-bitov� cel� ��slo, do kter�ho na�teme sou�asnou hodnotu ��ta�e. Op�t v�tv�me program podle p��tomnosti performance timeru. Prvn� ��dkou v if() na�teme obsah ��ta�e. D�le od n�j ode�teme po��te�n� �as, kter� jsme z�skali p�i inicializaci �asova�e. Z�skan� rozd�l n�sob�me periodou ��ta�e. Abychom v�sledek v sekund�ch p�evedli na milisekundy n�sob�me ho tis�cem. Tuto hodnotu vr�t�me. Nepou��v�me-li performance counter, provede se v�tev else, kter� d�l� analogicky to sam�. Na�teme sou�asn� �as, ode�teme od n�j po��te�n�, n�sob�me periodou a pot� tis�cem. Op�t z�sk�me uplynul� �as v milisekund�ch a vr�t�me ho.</p>

<p class="src0">float TimerGetTime()<span class="kom">// Z�sk� �as v milisekund�ch</span></p>
<p class="src0">{</p>
<p class="src1">__int64 time;<span class="kom">// �as se ukl�d� do 64-bitov�ho integeru</span></p>
<p class="src"></p>
<p class="src1">if (timer.performance_timer)<span class="kom">// Performance Timer</span></p>
<p class="src1">{</p>
<p class="src2">QueryPerformanceCounter((LARGE_INTEGER *) &amp;time);<span class="kom">// Na�te aktu�ln� �as</span></p>
<p class="src2"><span class="kom">// Vr�t� uplynul� �as v milisekund�ch</span></p>
<p class="src2">return ((float)(time - timer.performance_timer_start) * timer.resolution)*1000.0f;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Multimedi�ln� timer</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Vr�t� uplynul� �as v milisekund�ch</span></p>
<p class="src2">return ((float)(timeGetTime() - timer.mm_timer_start) * timer.resolution)*1000.0f;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>V dal�� funkci se resetuje pozice hr��e na lev� horn� roh a poloha nep��tel na randomov� body. Lev� horn� roh sc�ny m� sou�adnice [0;0]. P�i�ad�me je hr��ov� x a y. Proto�e je na za��tku linek, nepohybuje se, tak�e i up�es�uj�c� pohybov� pozice nastav�me na nulu.</p>

<p class="src0">void ResetObjects(void)<span class="kom">// Reset hr��e a nep��tel</span></p>
<p class="src0">{</p>
<p class="src1">player.x = 0;<span class="kom">// Hr�� bude vlevo naho�e</span></p>
<p class="src1">player.y = 0;<span class="kom">// Hr�� bude vlevo naho�e</span></p>
<p class="src1">player.fx = 0;<span class="kom">// Pohybov� pozice</span></p>
<p class="src1">player.fy = 0;<span class="kom">// Pohybov� pozice</span></p>

<p>P�ejdeme k inicializaci polohy nep��tel. Jejich aktu�ln� po�et (zobrazen�ch) je roven vnit�n�mu levelu n�soben�mu jeho sou�asnou obt��nost�/etapou. Zapamatujte si, �e maxim�ln� po�et level� je t�i a maxim�ln� po�et etap v levelu je tak� t�i. Z toho plyne, �e m��eme m�t nejv�ce dev�t nep��tel. V cyklu nastav�me x pozici ka�d�ho nep��tele na p�t a� deset a y pozici na nula a� deset. Nechceme, aby se pohybovali ze star� pozice na novou, tak�e se ujist�me, �e se fx a fy budou rovnat x kr�t d�lka linky (60) a y kr�t v��ka linky (40).</p>

<p class="src1"></p>
<p class="src1">for (loop1=0; loop1&lt;(stage*level); loop1++)<span class="kom">// Proch�z� nep��tele</span></p>
<p class="src1">{</p>
<p class="src2">enemy[loop1].x = 5 + rand() % 6;<span class="kom">// Nastav� randomovou x pozici</span></p>
<p class="src2">enemy[loop1].y = rand() % 11;<span class="kom">// Nastav� randomovou y pozici</span></p>
<p class="src2">enemy[loop1].fx = enemy[loop1].x * 60;<span class="kom">// Pohybov� pozice</span></p>
<p class="src2">enemy[loop1].fy = enemy[loop1].y * 40;<span class="kom">// Pohybov� pozice</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Funkce glPrint() se moc nezm�nila. Narozd�l od minul�ch tutori�l� jsem p�idal mo�nost v�pisu hodnot prom�nn�ch. Zapneme mapov�n� textur, resetujeme matici a p�esuneme se na ur�enou pozici. Pokud je zvolena prvn� (nult�) znakov� sada, zm�n�me m���tko tak, aby byl font dvakr�t vy��� a jeden a p�l kr�t �ir��. Pomoc� t�to finty budeme moci vypsat titul hry v�t��mi p�smeny. Na konci vypneme mapov�n� textur.</p>

<p class="src0">GLvoid glPrint(GLint x, GLint y, int set, const char *fmt, ...)<span class="kom">// V�pis text�</span></p>
<p class="src0">{</p>
<p class="src1">char text[256];<span class="kom">// Bude ukl�dat v�sledn� �et�zec</span></p>
<p class="src1">va_list ap;<span class="kom">// Ukazatel do argument� funkce</span></p>
<p class="src"></p>
<p class="src1">if (fmt == NULL)<span class="kom">// Nebyl p�ed�n �et�zec</span></p>
<p class="src2">return;<span class="kom">// Konec</span></p>
<p class="src"></p>
<p class="src1">va_start(ap, fmt);<span class="kom">// Rozd�l� �et�zec pro prom�nn�</span></p>
<p class="src1">vsprintf(text, fmt, ap);<span class="kom">// Konvertuje symboly na ��sla</span></p>
<p class="src1">va_end(ap);<span class="kom">// V�sledek je ulo�en v text</span></p>
<p class="src"></p>
<p class="src1">if (set&gt;1)<span class="kom">// Byla p�ed�na �patn� znakov� sada?</span></p>
<p class="src1">{</p>
<p class="src2">set=1;<span class="kom">// Pokud ano, zvol� se kurz�va</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturov� mapov�n�</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslated(x,y,0);<span class="kom">// P�esun na po�adovanou pozici</span></p>
<p class="src1">glListBase(base-32+(128*set));<span class="kom">// Zvol� znakovou sadu</span></p>
<p class="src"></p>
<p class="src1">if (set==0)<span class="kom">// Pokud je ur�ena prvn� znakov� sada font bude v�t��</span></p>
<p class="src1">{</p>
<p class="src2">glScalef(1.5f,2.0f,1.0f);<span class="kom">// Zm�na m���tka</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glCallLists(strlen(text),GL_UNSIGNED_BYTE, text);<span class="kom">// V�pis textu na monitor</span></p>
<p class="src1">glDisable(GL_TEXTURE_2D);<span class="kom">// Vypne texturov� mapov�n�</span></p>
<p class="src0">}</p>

<p>Implementace zm�ny velikosti okna je nov�. Nam�sto perspektivn� sc�ny pou�ijeme pravo�hlou projekci (ortho view). Jej� hlavn� charakteristikou je, �e se p�i zm�n� vzd�lenosti pozorovatele (translace do hloubky) objekty nezmen�uj� - vypnut� perspektiva. Osa z je m�n� u�ite�n�, n�kdy dokonce ztr�c� v�znam. V tomto tutori�lu s n� nebudeme pracovat v�bec.</p>

<p>Za�neme nastaven�m viewportu, �pln� stejn�, jako p�i perspektivn� sc�n�. Pot� zvol�me projek�n� matici (analogie filmov�mu projektoru; obsahuje informace, jak se zobraz� obr�zek) a resetujeme ji.</p>

<p>Inicializujeme pravo�hlou projekci. Prvn� parametr 0.0f ur�uje pozici lev� hrany sc�ny. Druh� p�ed�van� hodnota ozna�uje polohu prav� hrany. Pokud by m�lo okno velikost 640 x 480, tak ve width bude ulo�ena hodnota 640. Sc�na by za��nala na ose x nulou a kon�ila 640 - p�esn� jako okno. T�et�m parametrem ozna�ujeme spodn� okraj sc�ny. B�v� z�porn�, ale proto�e chceme pracovat s pixely ur��me spodek okna rovnu jeho v��ce. Nula, �tvrt� parametr, definuje horn� okraj. Posledn� dv� hodnoty n�le�� k ose z. V t�to lekci se o ni nestar�me, tak�e nastav�me rozmez� od -1.0f do 1.0f. V�echno budeme vykreslovat v hloubce nula, tak�e uvid�me v�e.</p>

<p>Po nastaven� pravo�hl� sc�ny, zvol�me matici modelview (informace o objektech, lokac�ch, atd.) a resetujeme ji.</p>

<p class="src0">GLvoid ReSizeGLScene(GLsizei width, GLsizei height)<span class="kom">// Inicializace a zm�na velikosti okna</span></p>
<p class="src0">{</p>
<p class="src1">if (height==0)<span class="kom">// Proti d�len� nulou</span></p>
<p class="src1">{</p>
<p class="src2">height=1;<span class="kom">// V��ka se rovn� jedn�</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glViewport(0,0,width,height);<span class="kom">// Reset Viewportu</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvol� projek�n� matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset projek�n� matice</span></p>
<p class="src"></p>
<p class="src1">glOrtho(0.0f,width,height,0.0f,-1.0f,1.0f);<span class="kom">// Vytvo�� pravo�hlou sc�nu</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvol� matici modelview</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice modelview</span></p>
<p class="src0">}</p>

<p>P�i inicializaci se vyskytne n�kolik nov�ch p��kaz�. Za�neme klasicky loadingem textur a kontrolou �sp�nosti t�to akce, pot� vygenerujeme znakovou sadu fontu. Zapneme jemn� st�nov�n�, nastav�me �ern� pozad� a vy�ist�me hloubku jedni�kou.</p>

<p>glHint() oznamuje OpenGL, jak m� vykreslovat. V tomto p��pad� po�adujeme, aby v�echny linky byly nejhez��, jak� OpenGL dok�e vytvo�it. T�mto p��kazem zap�n�me antialiasing. Tak� zapneme blending a zvol�me jeho m�d tak, abychom umo�nili, ji� zm�n�n�, antialiasing linek. Blending je pot�eba, pokud chceme p�kn� skombinovat (sm�chat, zpr�hlednit - blend with) s obr�zkem na pozad�. Pokud chcete vid�t, jak �patn� budou linky vypadat, vypn�te blending. Je d�le�it� pouk�zat na fakt, �e antialiasing se nemus� zobrazovat spr�vn�(? p�ekl.). Objekty ve h�e jsou docela mal�, tak�e si nemus�te v�imnout, �e n�co nen� v po��dku. Pod�vejte se po��dn�. V�imn�te si, jak se linky na nep��tel�ch zjemn� pokud je antialiasing zapnut�. Hr�� a hodiny by m�li vypadat mnohem l�pe.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Loading textur</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">BuildFont();<span class="kom">// Vytvo�en� fontu</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Zapne jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glHint(GL_LINE_SMOOTH_HINT, GL_NICEST);<span class="kom">// Nastaven� antialiasingu linek</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);<span class="kom">// Typ blendingu</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Na �adu p�ich�z� vykreslov�n�. Sma�eme obrazovku a hloubkov� buffer a zvol�me texturu fontu - texture[0]. Abychom slova "GRID CRAZY" vypsali purpurovou barvou nastav�me R a G naplno, G s polovi�n� intenzitou. N�pis vyp��eme na sou�adnice [207;24]. Pou�ijeme prvn� (nultou) znakovou sadu, tak�e bude text velk�mi p�smeny. Pot� zam�n�me purpurovou barvu za �lutou a vyp��eme "Level" s obsahem prom�nn� level2. Dvojka v %2i ur�uje maxim�ln� po�et ��slic. Pomoc� i oznamujeme, �e se jedn� o celo��selnou prom�nnou (integer). O trochu n��e, tou samou barvou, zobraz�me "Stage" s konkr�tn� etapou hry.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// V�echno kreslen�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Zvol� texturu fontu</span></p>
<p class="src"></p>
<p class="src1">glColor3f(1.0f,0.5f,1.0f);<span class="kom">// Purpurov� barva</span></p>
<p class="src1">glPrint(207,24,0,&quot;GRID CRAZY&quot;);<span class="kom">// Vyp��e logo hry</span></p>
<p class="src"></p>
<p class="src1">glColor3f(1.0f,1.0f,0.0f);<span class="kom">// �lut� barva</span></p>
<p class="src1">glPrint(20,20,1,&quot;Level:%2i&quot;,level2);<span class="kom">// Vyp��e level</span></p>
<p class="src1">glPrint(20,40,1,&quot;Stage:%2i&quot;,stage);<span class="kom">// Vyp��e etapu</span></p>

<p>Zkontrolujeme konec hry. Pokud je gameover rovno TRUE zvol�me n�hodnou barvu. Pou��v�me glcolor3ub(), proto�e je mnohem jednodu��� vygenerovat ��slo od 0 do 255 ne� od 0.0f do 1.0f. Doprava od titulku hry vyp��eme "GAME OVER" a o ��dek n��e "PRESS SPACE". Upozor�ujeme hr��e, �e zem�el a �e pomoc� mezern�ku m��e hru resetovat.</p>

<p class="src1">if (gameover)<span class="kom">// Konec hry?</span></p>
<p class="src1">{</p>
<p class="src2">glColor3ub(rand()%255,rand()%255,rand()%255);<span class="kom">// N�hodn� barva</span></p>
<p class="src2">glPrint(472,20,1,&quot;GAME OVER&quot;);<span class="kom">// Vyp��e GAME OVER</span></p>
<p class="src2">glPrint(456,40,1,&quot;PRESS SPACE&quot;);<span class="kom">// Vyp��e PRESS SPACE</span></p>
<p class="src1">}</p>

<p>Pokud mu v�ak n�jak� �ivoty zbyly, zobraz�me doprava od titulku hry animovan� obr�zky hr��e. Vytvo��me cyklus, kter� jde od nuly do aktu�ln�ho po�tu �ivot� m�nus jedna. Jedni�ku ode��t�me, proto�e jeden obr�zek se zobrazuje do hrac�ho pole.</p>

<p class="src1">for (loop1=0; loop1&lt;lives-1; loop1++)<span class="kom">// Cyklus vykresluj�c� �ivoty</span></p>
<p class="src1">{</p>

<p>Uvnit� cyklu resetujeme matici a provedeme translaci doprava na pozici, kterou z�sk�me v�po�tem: 490 plus ��d�c� prom�nn� kr�t 40. T�mto zp�sobem budeme moci vykreslit ka�d� animovan� �ivot hr��e o 40 pixel� doprava od minul�ho. Pot� orotujeme pohled proti sm�ru hodinov�ch ru�i�ek v z�vislosti na hodnot� ulo�en� v player.spin. Z�porn�m znam�nkem zp�sob�me, �e se budou �ivoty ot��et opa�n�m sm�rem ne� hr��.</p>

<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glTranslatef(490+(loop1*40.0f),40.0f,0.0f);<span class="kom">// P�esun doprava od titulku</span></p>
<p class="src2">glRotatef(-player.spin,0.0f,0.0f,1.0f);<span class="kom">// Rotace proti sm�ru hodinov�ch ru�i�ek</span></p>

<p>Zvol�me zelenou barvu a za�neme zobrazovat. Kreslen� linek je �pln� stejn�, jako kreslen� polygon�. Za�neme s glBegin(GL_LINES). T�m ozn�m�me OpenGL, �e chceme kreslit p��mky. Pro jednu sta�� pouze dva body. My zad�v�me body pomoc� glVertex2d(), proto�e nepot�ebujeme hloubku, ale samoz�ejm� lze pou��t i glVertex3f() pro plnohodnotn� bod ve 3D prostoru.</p>

<p class="src2">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelen� barva</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_LINES);<span class="kom">// Za��tek kreslen� �ivot�</span></p>
<p class="src"></p>
<p class="src3">glVertex2d(-5,-5);<span class="kom">// Lev� horn� bod</span></p>
<p class="src3">glVertex2d( 5, 5);<span class="kom">// Prav� doln� bod</span></p>
<p class="src3">glVertex2d( 5,-5);<span class="kom">// Prav� horn� bod</span></p>
<p class="src3">glVertex2d(-5, 5);<span class="kom">// Lev� doln� bod</span></p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Po vykreslen� X (X - tvar hr��e), znovu nato��me sc�nu, ale tentokr�t pouze o polovinu �hlu. Zad�me tmav�� zelenou barvu a vykresl�me +, ale trochu v�t�� ne� X. Proto�e je + pomalej�� a tmav��, X vypad�, jako by se ot��elo na jeho vrcholu.</p>

<p class="src2">glRotatef(-player.spin*0.5f,0.0f,0.0f,1.0f);<span class="kom">// Rotace proti sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src2">glColor3f(0.0f,0.75f,0.0f);<span class="kom">// Tmav�� zelen� barva</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_LINES);<span class="kom">// Pokra�ov�n� kreslen� �ivot�</span></p>
<p class="src"></p>
<p class="src3">glVertex2d(-7, 0);<span class="kom">// Lev� st�edov� bod</span></p>
<p class="src3">glVertex2d( 7, 0);<span class="kom">// Prav� st�edov� bod</span></p>
<p class="src3">glVertex2d( 0,-7);<span class="kom">// Horn� st�edov� bod</span></p>
<p class="src3">glVertex2d( 0, 7);<span class="kom">// Doln� st�edov� bod</span></p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src1">}</p>

<p>Nakresl�me hern� m���ku. Nastaven�m prom�nn� filled na TRUE ozn�m�me programu, �e u� byla m���ka kompletn� vypln�n� (v�ce d�le). Ur��me ���ku ��ry na 2.0f - linky ztloustnou a m���ka bude opticky v�ce definovan�. P�esto�e se zhor�� kvalita grafick�ho v�stupu, vypneme antialiasing. Velmi zat�uje procesor a pokud nem�te hodn� dobrou grafickou kartu, zaznamen�te obrovsk� zpomalen�. Vyzkou�ejte si a konejte, jak uzn�te za vhodn�.</p>

<p class="src1">filled=TRUE;<span class="kom">// P�ed testem je v�echno vypln�n�</span></p>
<p class="src1">glLineWidth(2.0f);<span class="kom">// �ir�� ��ry</span></p>
<p class="src1">glDisable(GL_LINE_SMOOTH);<span class="kom">// Vypne antialiasing</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Po resetu matice deklarujeme dva vno�en� cykly. Prvn�m proch�z�me m���ku zleva doprava a druh�m ze shora dol�. Nastav�me barvu na modrou a pokud je pr�v� kreslen� linka ji� p�ejet� hr��em, p�ebijeme modrou barvu b�lou. D�le zkontrolujeme, zda se nechyst�me kreslit p��li� vpravo. Pokud ano p�esko��me kreslen�.</p>

<p class="src1">for (loop1=0; loop1&lt;11; loop1++)<span class="kom">// Cyklus zleva doprava</span></p>
<p class="src1">{</p>
<p class="src2">for (loop2=0; loop2&lt;11; loop2++)<span class="kom">// Cyklus ze shora dol�</span></p>
<p class="src2">{</p>
<p class="src3">glColor3f(0.0f,0.5f,1.0f);<span class="kom">// Modr� barva</span></p>
<p class="src"></p>
<p class="src3">if (hline[loop1][loop2])<span class="kom">// Byla u� linka p�ejet�?</span></p>
<p class="src3">{</p>
<p class="src4">glColor3f(1.0f,1.0f,1.0f);<span class="kom">// B�l� barva</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (loop1&lt;10)<span class="kom">// Nekreslit �pln� vpravo</span></p>
<p class="src3">{</p>

<p>Otestujeme, jestli u� byla horizont�ln� linka p�ejet�. Pokud ne, p�i�ad�me do filled FALSE a t�m ozn�m�me, �e je�t� nejm�n� jedna linka nebyla vypln�n�, a tud�� je�t� nem��eme tento level opustit.</p>

<p class="src4">if (!hline[loop1][loop2])<span class="kom">// Nebyla linka je�t� p�ejet�?</span></p>
<p class="src4">{</p>
<p class="src5">filled=FALSE;<span class="kom">// V�echno je�t� nen� vypln�no</span></p>
<p class="src4">}</p>

<p>Pot� kone�n� vykresl�me horizont�ln� linku. Proto�e je vodorovn�, p�i�ad�me y-ov� hodnot� obou bod� stejnou velikost. P�i��t�me sedmdes�tku, aby nad hrac�m polem z�stalo voln� m�sto pro informace o po�tu �ivot�, levelu ap. Hodnoty na ose x se li�� t�m, �e druh� bod je posunut o �edes�t pixel� doprava (80-20=60). Op�t p�i��t�me konstantu, v tomto p��pad� dvac�tku, aby hrac� pole nebylo nama�k�no na lev� okraj a vpravo nebyla zbyte�n� mezera. V�imn�te si, �e linky jsou kresleny zleva doprava. Toto je d�vod, pro� nechceme kreslit jeden�ctou - neve�la by se na obrazovku.</p>

<p class="src4">glBegin(GL_LINES);<span class="kom">// Za��tek kreslen� horizont�ln�ch linek</span></p>
<p class="src5">glVertex2d(20+(loop1*60),70+(loop2*40));<span class="kom">// Lev� bod</span></p>
<p class="src5">glVertex2d(80+(loop1*60),70+(loop2*40));<span class="kom">// Prav� bod</span></p>
<p class="src4">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src3">}</p>

<p>Na �adu p�ich�zej� vertik�ln� linky. K�d je t�m�� stejn�, tak�e text popisu nebudu zbyte�n� opisovat. Linky se kresl� ze shora dol� nam�sto zleva doprava - jedin� odli�nost.</p>

<p class="src3">glColor3f(0.0f,0.5f,1.0f);<span class="kom">// Modr� barva</span></p>
<p class="src"></p>
<p class="src3">if (vline[loop1][loop2])<span class="kom">// Byla u� linka p�ejet�?</span></p>
<p class="src3">{</p>
<p class="src4">glColor3f(1.0f,1.0f,1.0f);<span class="kom">// B�l� barva</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (loop2&lt;10)<span class="kom">// Nekreslit �pln� dol�</span></p>
<p class="src3">{</p>
<p class="src4">if (!vline[loop1][loop2])<span class="kom">// Nebyla linka je�t� p�ejet�?</span></p>
<p class="src4">{</p>
<p class="src5">filled=FALSE;<span class="kom">// V�echno je�t� nebylo vypln�no</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">glBegin(GL_LINES);<span class="kom">// Za��tek kreslen� vertik�ln�ch linek</span></p>
<p class="src5">glVertex2d(20+(loop1*60),70 +(loop2*40));<span class="kom">// Horn� bod</span></p>
<p class="src5">glVertex2d(20+(loop1*60),110+(loop2*40));<span class="kom">// Doln� bod</span></p>
<p class="src4">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src3">}</p>

<p>Sc�na je dohromady seskl�dan� z obd�ln�k� o velikosti jedn� desetiny obr�zku sc�ny. Na ka�d� z nich je namapovan� ur�it� ��st velk� textury, proto mus�me zapnout mapov�n� textur. Proto�e nechceme, aby m�l kreslen� obd�ln�k barevn� n�dech, nastav�me barvu na b�lou. Tak� nesm�me zapomenout zvolit texturu.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_21_image.jpg" width="128" height="128" alt="Textura hrac� plochy" /></div>

<p class="src3">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>
<p class="src3">glColor3f(1.0f,1.0f,1.0f);<span class="kom">// B�l� barva</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[1]);<span class="kom">// Zvol� texturu</span></p>

<p>D�le prov���me, jestli aktu�ln� obd�ln�k ve sc�n� je�t� existuje (nen� za hranou hrac� plochy). Nach�z�me se v cyklech, kter� postupn� vykresluj� 11 linek vodorovn� a 11 svisle. Nicm�n� nevykreslujeme 11 obd�ln�k�, ale pouze 10! Ov���me, jestli se nechyst�me kreslit na jeden�ctou pozici - loop1 i loop2 mus� b�t men�� ne� deset (0-9).</p>

<p class="src3">if ((loop1&lt;10) &amp;&amp; (loop2&lt;10))<span class="kom">// Pouze pokud je obd�ln�k v hrac� plo�e</span></p>
<p class="src3">{</p>

<p>Zjist�me p�ejet� v�ech okoln�ch linek obd�ln�ku. Kraje testujeme v po�ad�: horn�, doln�, lev� a prav�. Po ka�d�m pr�chodu vnit�n�m cyklem se inkrementuje loop1 a t�m se z prav�ho okraje st�v� lev� okraj n�sleduj�c�ho obd�ln�ku. V p��pad� pr�chodu vn�j�� smy�kou se ze spodn�ch hran obd�ln�k� v ��dku st�vaj� horn� okraje nov�ch obd�ln�k� v ��dku o jedno n��e. V�e by m�lo b�t z�ejm� z diagramu.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_21_diagram.jpg" width="256" height="128" alt="Diagram" /></div>

<p>Pokud jsou v�echny okraje projet� (rovnaj� se TRUE), m��eme namapovat texturu a vykreslit obd�ln�k. D�l�me to stejn�m stylem, jako jsme roz�ez�vali texturu znakov� sady na jednotliv� p�smena. Ani te� se neobejdeme bez matematiky. D�l�me loop1 i loop2 deseti, proto�e chceme rozd�lit texturu mezi sto obd�ln�k� (10x10). Koordin�ty jsou v rozmez� od nuly do jedn� s krokem jedn� desetiny (1/10=0,1).</p>

<p>Tak�e abychom dostali prav� horn� roh, vyd�l�me hodnotu prom�nn�ch loop deseti a p�i�teme 0,1 k x-ov�mu koordin�tu. Lev� horn� roh z�sk�me d�len�m bez ��dn�ch dal��ch komplikac�. Lev� doln� bod spo��v� op�t v d�len� deseti a p�i�ten� 0,1 k ypsilonov� slo�ce. Dost�v�me se k prav�mu doln�mu rohu, u kter�ho se po vyd�len� p�i��t� 0,1 k ob�ma sou�adnicov�m slo�k�m. Douf�m, �e to d�v� smysl (J� taky - p�ekl.).</p>

<p>Pokud budou oba loopy rovny dev�ti, ve v�sledku dostaneme kombinaci 0,9 a 1,0, kter� dosad�me do parametr� funkce glTexCoord2f(x,y). sou�adnice vrchol� obd�ln�k� pro glVertex2d(x,y) z�sk�me analogicky jako okraje linek m���ky. P�i��t�me k nim, ale je�t� konstanty (1, 59, 1, 39), kter� zaji��uj� zmen�en� obd�ln�k� - aby se ve�ly do pol��ek m���ky a p�itom nic nep�ekryly.</p>

<p class="src4"><span class="kom">// Jsou p�ejety v�echny �ty�i okraje obd�ln�ku?</span></p>
<p class="src4">if (hline[loop1][loop2] &amp;&amp; hline[loop1][loop2+1] &amp;&amp; vline[loop1]loop2] &amp;&amp; vline[loop1+1][loop2])</p>
<p class="src4">{</p>
<p class="src5">glBegin(GL_QUADS);<span class="kom">// Vykresl� otexturovan� obd�ln�k</span></p>
<p class="src"></p>
<p class="src6">glTexCoord2f(float(loop1/10.0f)+0.1f,1.0f-(float(loop2/10.0f)));</p>
<p class="src6">glVertex2d(20+(loop1*60)+59,(70+loop2*40+1));<span class="kom">// Prav� horn�</span></p>
<p class="src"></p>
<p class="src6">glTexCoord2f(float(loop1/10.0f),1.0f-(float(loop2/10.0f)));</p>
<p class="src6">glVertex2d(20+(loop1*60)+1,(70+loop2*40+1));<span class="kom">// Lev� horn�</span></p>
<p class="src"></p>
<p class="src6">glTexCoord2f(float(loop1/10.0f),1.0f-(float(loop2/10.0f)+0.1f));</p>
<p class="src6">glVertex2d(20+(loop1*60)+1,(70+loop2*40)+39);<span class="kom">// Lev� doln�</span></p>
<p class="src"></p>
<p class="src6">glTexCoord2f(float(loop1/10.0f)+0.1f,1.0f-(float(loop2/10.0f)+0.1f));</p>
<p class="src6">glVertex2d(20+(loop1*60)+59,(70+loop2*40)+39);<span class="kom">// Prav� doln�</span></p>
<p class="src"></p>
<p class="src5">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src4">}</p>
<p class="src3">}</p>

<p>V z�v�ru vypneme mapov�n� textur a po opu�t�n� obou cykl� vr�t�me ���ku ��ry na p�vodn� hodnotu.</p>

<p class="src3">glDisable(GL_TEXTURE_2D);<span class="kom">// Vypne mapov�n� textur</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glLineWidth(1.0f);<span class="kom">// ���ka ��ry 1.0f</span></p>

<p>V p��pad�, �e je anti rovno TRUE, zapneme zjem�ov�n� linek (antialiasing).</p>

<p class="src1">if (anti)<span class="kom">// M� b�t zapnut� antialiasing?</span></p>
<p class="src1">{</p>
<p class="src2">glEnable(GL_LINE_SMOOTH);<span class="kom">// Zapne antialiasing</span></p>
<p class="src1">}</p>

<p>Abychom usnadnili hru, p�id�me speci�ln� objekt - p�es�pac� hodiny, jejich� sebr�n�m se nep��tel� na chv�li zastav�. Pro jejich um�st�n� v hrac�m poli pou��v�me prom�nn� x a y, nicm�n� proto�e se nebudou pohybovat, m��eme vyu��t nepot�ebn� fx jako p�ep�na� (0 jsou viditeln�, 1 nejsou, 2 hr�� je sebral). Fy implementujeme pro ��ta�, jak dlouho by m�ly b�t viditeln�.</p>

<p>Za�neme testem viditelnosti. Pokud se nemaj� zobrazit, p�esko��me vykreslen�. Pokud ano, resetujeme matici a translac� je um�st�me. Proto�e m���ka za��n� na dvac�tce, p�i�teme tuto hodnotu k x*60. Ze stejn�ho d�vodu na ose y p�i��t�me 70. D�le orotujeme matici okolo osy z o �hel ulo�en� v hourglass.spin. P�ed vykreslen�m je�t� zvol�me n�hodnou barvu.</p>

<p class="src1">if (hourglass.fx==1)<span class="kom">// Hodiny se maj� vykreslit</span></p>
<p class="src1">{</p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset Matice</span></p>
<p class="src"></p>
<p class="src2">glTranslatef(20.0f+(hourglass.x*60),70.0f+(hourglass.y*40),0.0f);<span class="kom">// Um�st�n�</span></p>
<p class="src2">glRotatef(hourglass.spin,0.0f,0.0f,1.0f);<span class="kom">// Rotace ve sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src"></p>
<p class="src2">glColor3ub(rand()%255,rand()%255,rand()%255);<span class="kom">// N�hodn� barva</span></p>

<p>Pomoc� GL_LINES ozn�m�me kreslen� linek. Horn� lev� bod z�sk�me ode�ten�m p�ti pixel� v obou sm�rech. Konec p��mky le�� p�t pixel� sm�rem vpravo dol� od aktu�ln� pozice. Druhou linku za�neme vpravo naho�e a skon��me vlevo dole. Tvar p�smene X dopln�me o horn� a doln� uzav�rac� linku.</p>

<p class="src2">glBegin(GL_LINES);<span class="kom">// Vykreslen� p�es�pac�ch hodin</span></p>
<p class="src"></p>
<p class="src3">glVertex2d(-5,-5);<span class="kom">// Lev� horn� bod</span></p>
<p class="src3">glVertex2d( 5, 5);<span class="kom">// Prav� doln� bod</span></p>
<p class="src3">glVertex2d( 5,-5);<span class="kom">// Prav� horn� bod</span></p>
<p class="src3">glVertex2d(-5, 5);<span class="kom">// Lev� doln� bod</span></p>
<p class="src"></p>
<p class="src3">glVertex2d(-5, 5);<span class="kom">// Lev� doln� bod</span></p>
<p class="src3">glVertex2d( 5, 5);<span class="kom">// Prav� doln� bod</span></p>
<p class="src3">glVertex2d(-5,-5);<span class="kom">// Lev� horn� bod</span></p>
<p class="src3">glVertex2d( 5,-5);<span class="kom">// Prav� horn� bod</span></p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src1">}</p>

<p>D�le vykresl�me hr��e. Op�t resetujeme matici a ur��me pozici ve sc�n�. V�imn�te si, �e pro jemn� neskokov� pohyb pou��v�me fx a fy. Nato��me matici o ulo�en� �hel, zvol�me sv�tle zelenou barvu a pomoc� linek vykresl�me tvar p�smene X.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset Matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(player.fx+20.0f,player.fy+70.0f,0.0f);<span class="kom">// P�esun na pozici</span></p>
<p class="src1">glRotatef(player.spin,0.0f,0.0f,1.0f);<span class="kom">// Rotace po sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src"></p>
<p class="src1">glColor3f(0.0f,1.0f,0.0f);<span class="kom">// Zelen� barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_LINES);<span class="kom">// Vykreslen� hr��e</span></p>
<p class="src"></p>
<p class="src2">glVertex2d(-5,-5);<span class="kom">// Lev� horn� bod</span></p>
<p class="src2">glVertex2d( 5, 5);<span class="kom">// Prav� doln� bod</span></p>
<p class="src2">glVertex2d( 5,-5);<span class="kom">// Prav� horn� bod</span></p>
<p class="src2">glVertex2d(-5, 5);<span class="kom">// Lev� doln� bod</span></p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Aby nevypadal a� tak nudn�, p�id�me je�t� tvar znam�nka +, kter� se ot��� trochu rychleji, m� tmav�� barvu a je o dva pixely v�t��.</p>

<p class="src1">glRotatef(player.spin*0.5f,0.0f,0.0f,1.0f);<span class="kom">// Rotace po sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src"></p>
<p class="src1">glColor3f(0.0f,0.75f,0.0f);<span class="kom">// Tmav�� zelen� barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_LINES);<span class="kom">// Pokra�ov�n� kreslen� hr��e</span></p>
<p class="src"></p>
<p class="src2">glVertex2d(-7, 0);<span class="kom">// Lev� st�edov� bod</span></p>
<p class="src2">glVertex2d( 7, 0);<span class="kom">// Prav� st�edov� bod</span></p>
<p class="src2">glVertex2d( 0,-7);<span class="kom">// Horn� st�edov� bod</span></p>
<p class="src2">glVertex2d( 0, 7);<span class="kom">// Doln� st�edov� bod</span></p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Je�t� zb�v� vykreslit nep��tele, tak�e se do nich pust�me. Deklarujeme cyklus proch�zej�c� v�echny nep��tele, kte�� jsou viditeln� v konkr�tn�m levelu. Tento po�et z�sk�me vyn�soben�m levelu s obt��nost�. Jejich maxim�ln� po�et je dev�t. Uvnit� smy�ky resetujeme matici a um�st�me pr�v� vykreslovan�ho nep��tele pomoc� fx a fy (m��e se pohybovat). Zvol�me r��ovou barvu a pomoc� linek vykresl�me �tverec postaven� na �pi�ku, kter� nerotuje.</p>

<p class="src1">for (loop1=0; loop1&lt;(stage*level); loop1++)<span class="kom">// Vykresl� nep��tele</span></p>
<p class="src1">{</p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glTranslatef(enemy[loop1].fx+20.0f,enemy[loop1].fy+70.0f,0.0f);<span class="kom">// P�esun na pozici</span></p>
<p class="src2">glColor3f(1.0f,0.5f,0.5f);<span class="kom">// R��ov� barva</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_LINES);<span class="kom">// Vykreslen� nep��tel</span></p>
<p class="src"></p>
<p class="src3">glVertex2d( 0,-7);<span class="kom">// Horn� bod</span></p>
<p class="src3">glVertex2d(-7, 0);<span class="kom">// Lev� bod</span></p>
<p class="src3">glVertex2d(-7, 0);<span class="kom">// Lev� bod</span></p>
<p class="src3">glVertex2d( 0, 7);<span class="kom">// Doln� bod</span></p>
<p class="src3">glVertex2d( 0, 7);<span class="kom">// Doln� bod</span></p>
<p class="src3">glVertex2d( 7, 0);<span class="kom">// Prav� bod</span></p>
<p class="src3">glVertex2d( 7, 0);<span class="kom">// Prav� bod</span></p>
<p class="src3">glVertex2d( 0,-7);<span class="kom">// Horn� bod</span></p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>P�id�me krvav� �erven� X, kter� se ot��� okolo osy z a pot� ukon��me obrovskou vykreslovac� funkci.</p>

<p class="src2">glRotatef(enemy[loop1].spin,0.0f,0.0f,1.0f);<span class="kom">// Rotace vnit�ku nep��tele</span></p>
<p class="src"></p>
<p class="src2">glColor3f(1.0f,0.0f,0.0f);<span class="kom">// Krvav� barva</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_LINES);<span class="kom">// Pokra�ov�n� kreslen� nep��tel</span></p>
<p class="src"></p>
<p class="src3">glVertex2d(-7,-7);<span class="kom">// Lev� horn� bod</span></p>
<p class="src3">glVertex2d( 7, 7);<span class="kom">// Prav� doln� bod</span></p>
<p class="src3">glVertex2d(-7, 7);<span class="kom">// Lev� doln� bod</span></p>
<p class="src3">glVertex2d( 7,-7);<span class="kom">// Prav� horn� bod</span></p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Konec funkce</span></p>
<p class="src0">}</p>

<p>Zm�n ve funkci WinMain() bude tak� trochu v�c. Proto�e se jedn� o hru, mus�me o�et�it ovl�d�n� kl�vesnic�, �asov�n� a v�e ostatn�, co jsme dosud neud�lali.</p>

<p class="src0">int WINAPI WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPSTR lpCmdLine, int nCmdShow)</p>
<p class="src0">{</p>
<p class="src1">MSG msg;</p>
<p class="src1">BOOL done=FALSE;</p>
<p class="src"></p>
<p class="src1">if (MessageBox(NULL,&quot;Would You Like To Run In Fullscreen Mode?&quot;, &quot;Start FullScreen?&quot;, MB_YESNO|MB_ICONQUESTION) == IDNO)</p>
<p class="src1">{</p>
<p class="src2">fullscreen=FALSE;</p>
<p class="src1">}</p>

<p>Zm�n�me titulek okna na "NeHe's Line Tutorial" a p�id�me vol�n� funkce ResetObjects(), kter� inicializuje pozici hr��e na lev� horn� roh a nep��tel�m p�ed�l� n�hodn� um�st�n�, nejm�n� v�ak p�t pol��ek od hr��e. Pot� zavol�me funkci pro inicializaci timeru.</p>

<p class="src1">if (!CreateGLWindow(&quot;NeHe's Particle Tutorial&quot;,640,480,16,fullscreen))</p>
<p class="src1">{</p>
<p class="src2">return 0;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">ResetObjects();<span class="kom">// Inicializuje pozici hr��e a nep��tel</span></p>
<p class="src1">TimerInit();<span class="kom">// Zprovozn�n� timeru</span></p>
<p class="src"></p>
<p class="src1">while(!done)</p>
<p class="src1">{</p>
<p class="src2">if (PeekMessage(&amp;msg,NULL,0,0,PM_REMOVE))</p>
<p class="src2">{</p>
<p class="src3">if (msg.message==WM_QUIT)</p>
<p class="src3">{</p>
<p class="src4">done=TRUE;</p>
<p class="src3">}</p>
<p class="src3">else</p>
<p class="src3">{</p>
<p class="src4">TranslateMessage(&amp;msg);</p>
<p class="src4">DispatchMessage(&amp;msg);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>

<p>Te� zajist�me, aby pracoval k�d pro �asov�n�. P�edt�m ne� vykresl�me sc�nu, nagrabujeme aktu�ln� �as a ulo��me jej do desetinn� prom�nn� nazvan� start. Potom vykresl�me sc�nu a prohod�me buffery.</p>

<p class="src3">float start=TimerGetTime();<span class="kom">// Nagrabujeme aktu�ln� �as</span></p>
<p class="src"></p>
<p class="src3">if ((active &amp;&amp; !DrawGLScene()) || keys[VK_ESCAPE])</p>
<p class="src3">{</p>
<p class="src4">done=TRUE;</p>
<p class="src3">}</p>
<p class="src3">else</p>
<p class="src3">{</p>
<p class="src4">SwapBuffers(hDC);</p>
<p class="src3">}</p>

<p>Vytvo��me �asov� zpo�d�n� a to tak, �e vkl�d�me pr�zdn� p��kazy tak dlouho, dokud je aktu�ln� hodnota �asova�e (TimerGetTime()) men�� ne� po��te�n� hodnota se�ten� s rychlost� kroky hry kr�t dva. t�mto velmi jednodu�e zpomal�me OPRAVDU rychl� syst�my.</p>

<p>Proto�e pou��v�me krokov�n� rychlosti (ur�en� prom�nnou adjust) program v�dy pob�� stejnou rychlost�. Nap��klad, pokud je hodnota kroku rovna jedn�, m�li bychom �ekat dokud timer nebude v�t�� nebo roven dv�ma (2*1). Ale pokud zv�t��me rychlost kroku na dva (zp�sob�, �e se hr�� bude pohybovat o dvakr�t tolik pixel� najednou), zpo�d�n� se zv�t�� na �ty�i (2*2). A�koli se pohybujeme dvakr�t tak rychle, zpo�d�n� trv� dvakr�t d�le a tud�� hra b�� stejn� rychle (ale v�ce trhan�).</p>

<p>Spousta lid� jde ale jinou cestou ne� my. Je t�eba br�t v �vahu �as kter� ub�hl mezi jednotliv�mi cykly ve kter�ch se renderuje. Na za��tku ka�d�ho cyklu se ulo�� aktu�ln� �as, od kter�ho se ode�te �as v minul�m cyklu a t�mto rozd�lem se vyd�l� rychlost, kterou se m� objekt pohybovat. Nap��klad: m�me auto, kter� m� jet rychlost� 10 jednotek za sekundu. V�me, �e mezi t�mto a p�edchoz�m cyklem ub�hlo 20 ms. Objekt mus�me tedy posunout o (10*20)/1000 = 0,2 jednotek. Bohu�el v tomto programu to takto prov�st nem��eme, proto�e pou��v�me m���ku a ne nap�. otev�enou krajinu. Hodnoty fx a fy mus� b�t p�esn� ur�en�. Pokud hr��ova fx bude �ekn�me 59 a po��ta� rozhodne posunout hr��e o dva pixely doprava, tak po stisku �ipky nahoru hr�� nep�jde po &quot;�edes�t�ch pixelech&quot;, ale o kousek vedle.</p>

<p>P�ekl.: Nicm�n� i na�e metoda m� jeden velk� error - okno nem��e v �ekac�ch cyklech zpracov�vat ��dn� zpr�vy. �ekn�me, �e bude (pon�kud p�e�enu) �asov� zpo�d�n� 5 sekund. Okno nen� aktivn� a u�ivateli p�ipad�, �e v programu nastala fat�ln� chyba. Pokus� se ho ukon�it, ale i to se mu poda�� a� za t�chto p�t sekund. A pokud se bude zpomalovac� k�d volat �ast�ji (nap�. po ka�d�m p�ekreslen�)... ch�pete? I u n�s je tento probl�m trochu znateln�. Pokud se pokou��te zato�it do ur�it� linky, n�kdy se stref�te a� na n�kolik�t� pokus - program nezareaguje v�as. Pro� vlastn� vzniklo v�cevl�knov� programov�n�? Aby odstranilo zd�nliv� &quot;spadnut� programy&quot; p�i n�ro�n�ch a dlouho trvaj�c�ch v�po�tech. J� osobn�, bych se takov�muto �asov�n� za ka�dou cenu vyhnul.</p>

<p class="src3"><span class="kom">// Pl�tv� cykly procesoru na rychl�ch syst�mech</span></p>
<p class="src3">while(TimerGetTime() &lt; start + float(steps[adjust] * 2.0f))</p>
<p class="src3">{</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_F1])</p>
<p class="src3">{</p>
<p class="src4">keys[VK_F1]=FALSE;</p>
<p class="src4">KillGLWindow();</p>
<p class="src4">fullscreen =! fullscreen;</p>
<p class="src"></p>
<p class="src4">if (!CreateGLWindow(&quot;NeHe's Line Tutorial&quot;,640,480,16,fullscreen))</p>
<p class="src4">{</p>
<p class="src5">return 0;</p>
<p class="src4">}</p>
<p class="src3">}</p>

<p>P�ejdeme k ovl�d�n� kl�vesnic�. Po stisku 'A' znegujeme prom�nnou anti a t�m ozn�m�me k�du pro kreslen�, �e m� nebo nem� pou��vat antialiasing.</p>

<p class="src3">if (keys['A'] &amp;&amp; !ap)<span class="kom">// Stisk A</span></p>
<p class="src3">{</p>
<p class="src4">ap = TRUE;<span class="kom">// Nastav� p��znak</span></p>
<p class="src4">anti=!anti;<span class="kom">// Zapne/vypne antialiasing</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (!keys['A'])<span class="kom">// Uvoln�n� A</span></p>
<p class="src3">{</p>
<p class="src4">ap=FALSE;<span class="kom">// Vypne p��znak</span></p>
<p class="src3">}</p>

<p>Te� pohyb a logika nep��tel. Cht�l jsem udr�et k�d opravdu jednoduch�, tak�e ne�ekejte ��dn� z�zraky. Pracuje tak, �e nep��tel� zjist�, kde je hr�� a pot� se vydaj� jeho sm�rem (na pozici x, y). Mohou nap��klad vid�t, �e je v hrac�m poli naho�e, ale v �ase, kdy testovali pozici x, hr�� u� m��e b�t d�ky fx �pln� n�kde jinde. �astokr�t se dostanou tam, kde byl o krok p�edt�m. N�kdy vypadaj� opravdu zmaten�.</p>

<p>Za�neme uji�t�n�m se, jestli u� nen� konec hry a jestli je okno aktivn�. Pokud se nap��klad minimalizovalo, nep��tel� se nebudou na pozad� pohybovat.</p>

<p>Vytvo��me cyklus, kter� i tentokr�t proch�z� v�echny nep��tele.</p>

<p class="src3">if (!gameover &amp;&amp; active)<span class="kom">// Nen�-li konec hry a okno je aktivn�</span></p>
<p class="src3">{</p>
<p class="src4">for (loop1=0; loop1&lt;(stage*level); loop1++)<span class="kom">// Proch�z� v�echny nep��tele</span></p>
<p class="src4">{</p>

<p>V p��pad�, �e bude x pozice nep��tele men�� ne� x pozice hr��e a z�rove� se tak� mus� rovnat y*40 pozici y (jsme v pr�se��ku vertik�ln� a horizont�ln� linky) posuneme nep��tele doprava. Analogick�m zp�sobem implementujeme i pohyb doleva, nahoru a dol�.</p>

<p>Pozn�mka: po zm�n� pozic x a y nelze vid�t ��dn� pohyb, proto�e p�i vykreslov�n� objekty um�s�ujeme pomoc� prom�nn�ch fx a fy. Zm�nou x a y jenom ur�ujeme po�adovan� sm�r pohybu.</p>

<p class="src5">if ((enemy[loop1].x &lt; player.x) &amp;&amp; (enemy[loop1].fy==enemy[loop1].y*40))</p>
<p class="src5">{</p>
<p class="src6">enemy[loop1].x++;<span class="kom">// P�esun o pol��ko doprava</span></p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if ((enemy[loop1].x &gt; player.x) &amp;&amp; (enemy[loop1].fy==enemy[loop1].y*40))</p>
<p class="src5">{</p>
<p class="src6">enemy[loop1].x--;<span class="kom">// P�esun o pol��ko doleva</span></p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if ((enemy[loop1].y &lt; player.y) &amp;&amp; (enemy[loop1].fx==enemy[loop1].x*60))</p>
<p class="src5">{</p>
<p class="src6">enemy[loop1].y++;<span class="kom">// P�esun o pol��ko dol�</span></p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if ((enemy[loop1].y &gt; player.y) &amp;&amp; (enemy[loop1].fx==enemy[loop1].x*60))</p>
<p class="src5">{</p>
<p class="src6">enemy[loop1].y--;<span class="kom">// P�esun o pol��ko nahoru</span></p>
<p class="src5">}</p>

<p>N�sleduj�c� k�d prov�d� opravdov� pohyb. Zjist�me, zda je prom�nn� delay v�t�� ne� t�i m�nus level. Pokud jsme v levelu jedna, program pojde cyklem dvakr�t (3-1=2), p�edt�m ne� se nep��tel opravdu pohne. V levelu t�i (nejvy��� mo�n�) se nep��tel� budou pohybovat stejnou rychlost� jako hr�� - tedy bez zpo�d�n�. Tak� ov��ujeme, jestli se hourglas.fx nerovn� dv�ma. Tato prom�nn� ozna�uje hr��ovo sebr�n� p�es�pac�ch hodin. V tak�m p��pad� nep��telem nepohybujeme.</p>

<p>Pokud je zpo�d�n� vy��� ne� t�i m�nus level a hr�� nesebral hodiny, pohneme nep��telem �pravou prom�nn�ch fx a fy. Nejprve vynulujeme zpo�d�n�, tak�e ho budeme moci znovu po��tat a potom op�t deklarujeme cyklus, kter� proch�z� v�echy viditeln� nep��tele.</p>

<p class="src5">if (delay &gt; (3-level) &amp;&amp; (hourglass.fx!=2))<span class="kom">// Hr�� nesebral p�es�pac� hodiny</span></p>
<p class="src5">{</p>
<p class="src6">delay=0;<span class="kom">// Reset delay na nulu</span></p>
<p class="src"></p>
<p class="src6">for (loop2=0; loop2&lt;(stage*level); loop2++)<span class="kom">// Proch�z� v�echny nep��tele</span></p>
<p class="src6">{</p>

<p>Nep��tel se v�dy pohybuje pomoc� fx/fy sm�rem k x/y. V prvn�m if zjist�me jestli je fx men�� ne� x*60. V takov�m p��pad� ho posuneme doprava o vzd�lenost steps[adjust]. Tak� zm�n�me jeho �hel nato�en�, aby vznikl dojem rolov�n� doprava.</p>

<p>�pln� stejn� provedeme pohyby doleva, dol� a nahoru.</p>

<p class="src7">if (enemy[loop2].fx &lt; enemy[loop2].x*60)<span class="kom">// Fx je men�� ne� x</span></p>
<p class="src7">{</p>
<p class="src8">enemy[loop2].fx+=steps[adjust];<span class="kom">// Zv��it fx</span></p>
<p class="src8">enemy[loop2].spin+=steps[adjust];<span class="kom">// Rotace ve sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src7">}</p>
<p class="src"></p>
<p class="src7">if (enemy[loop2].fx &gt; enemy[loop2].x*60)<span class="kom">// Fx je v�t�� ne� x</span></p>
<p class="src7">{</p>
<p class="src8">enemy[loop2].fx-=steps[adjust];<span class="kom">// Sn��it fx</span></p>
<p class="src8">enemy[loop2].spin-=steps[adjust];<span class="kom">// Rotace proti sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src7">}</p>
<p class="src"></p>
<p class="src7">if (enemy[loop2].fy &lt; enemy[loop2].y*40)<span class="kom">// Fy je men�� ne� y</span></p>
<p class="src7">{</p>
<p class="src8">enemy[loop2].fy+=steps[adjust];<span class="kom">// Zv��it fy</span></p>
<p class="src8">enemy[loop2].spin+=steps[adjust];<span class="kom">// Rotace ve sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src7">}</p>
<p class="src"></p>
<p class="src7">if (enemy[loop2].fy &gt; enemy[loop2].y*40)<span class="kom">// Fy je v�t�� ne� y</span></p>
<p class="src7">{</p>
<p class="src8">enemy[loop2].fy-=steps[adjust];<span class="kom">// Sn��it fy</span></p>
<p class="src8">enemy[loop2].spin-=steps[adjust];<span class="kom">// Rotace proti sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src7">}</p>
<p class="src6">}</p>
<p class="src5">}</p>

<p>Pohyb tedy m�me. nyn� pot�ebujeme vy�e�it n�raz nep��tel do hr��e. V p��pad�, �e se ob� fx i ob� fy rovnaj�... hr�� zem�e. Dekrementujeme �ivoty a v p��pad� jejich nulov� hodnoty prohl�s�me hru za skon�enou. Resetujeme v�echny objekty a nech�me zahr�t �mrtn� skladbu.</p>

<p>Zvuky jsou v na�ich tutori�lech novinkou. rozhodl jsem se pou��t tu nejz�kladn�j�� dostupnou rutinu... PlaySound(). P�ed�v�me j� t�i parametry. Prvn� ur�uje cestu k souboru se zvukem. Druh� parametr pomoc� nulov�ho ukazatele ignorujeme. T�et� parametr je flag stylu. Dva nej�ast�ji pou��van� jsou: SND_SYNC, kter� zastav� prov�d�n� programu, dokud p�ehr�v�n� zvuku neskon��. Druh� mo�nost, SND_ASYNC, p�ehr�v� zvuk nez�visle na b�hu programu. D�me p�ednost mali�k�mu zpo�d�n�, tak�e funkci p�ed�me SND_SYNC.</p>

<p>Na za��tku tutori�lu jsem zapomn�l na jednu v�c: Abychom mohli pou��vat funkci PlaySound(), pot�ebujeme inkludovat knihovnu WINMM.LIB (Windows Multimedia Library). Ve Visual C++ to lze prov�st v nab�dce Project/Setting/Link.</p>

<p class="src5"><span class="kom">// Setk�n� nep��tele s hr��em</span></p>
<p class="src5">if ((enemy[loop1].fx==player.fx) &amp;&amp; (enemy[loop1].fy==player.fy))</p>
<p class="src5">{</p>
<p class="src6">lives--;<span class="kom">// Hr�� ztr�c� �ivot</span></p>
<p class="src"></p>
<p class="src6">if (lives==0)<span class="kom">// Nulov� po�et �ivot�</span></p>
<p class="src6">{</p>
<p class="src7">gameover=TRUE;<span class="kom">// Konec hry</span></p>
<p class="src6">}</p>
<p class="src"></p>
<p class="src6">ResetObjects();<span class="kom">// Reset pozice hr��e a nep��tel</span></p>
<p class="src"></p>
<p class="src6">PlaySound(&quot;Data/Die.wav&quot;, NULL, SND_SYNC);<span class="kom">// Zahraje um�r��ek</span></p>
<p class="src5">}</p>
<p class="src4">}</p>

<p>O�et��me stisk kurzorov�ch kl�ves. Vy�e��me �ipku doprava, ostatn� sm�ry jsou zcela analogick�. Abychom nevypadli pry� z hrac�ho pole mus� b�t player.x men�� ne� deset (���ka m���ky). Nechceme, aby mohl zm�nit sm�r uprost�ed p�esunu a tak kontrolujeme, zda se fx==player.x*60 a fy==player.y*40. Nastanou-li ob� rovnosti, m��eme s ur�itost� ��ci, �e se nach�z� v pr�se��ku rovnob�n� se svislou linkou a tedy dokon�il sv�j pohyb. Plat�-li v�echny podm�nky, ozna��me linku pod hr��em jako p�ejetou a posuneme jej na n�sleduj�c� pozici.</p>

<p class="src4">if (keys[VK_RIGHT] &amp;&amp; (player.x&lt;10) &amp;&amp; (player.fx==player.x*60) &amp;&amp; (player.fy==player.y*40))</p>
<p class="src4">{</p>
<p class="src5">hline[player.x][player.y]=TRUE;<span class="kom">// Ozna�en� linky</span></p>
<p class="src5">player.x++;<span class="kom">// Doprava</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_LEFT] &amp;&amp; (player.x&gt;0) &amp;&amp; (player.fx==player.x*60) &amp;&amp; (player.fy==player.y*40))</p>
<p class="src4">{</p>
<p class="src5">hline[player.x][player.y]=TRUE;<span class="kom">// Ozna�en� linky</span></p>
<p class="src5">player.x--;<span class="kom">// Doleva</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN] &amp;&amp; (player.y&lt;10) &amp;&amp; (player.fx==player.x*60) &amp;&amp; (player.fy==player.y*40))</p>
<p class="src4">{</p>
<p class="src5">vline[player.x][player.y]=TRUE;<span class="kom">// Ozna�en� linky</span></p>
<p class="src5">player.y++;<span class="kom">// Dol�</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_UP] &amp;&amp; (player.y&gt;0) &amp;&amp; (player.fx==player.x*60) &amp;&amp; (player.fy==player.y*40))</p>
<p class="src4">{</p>
<p class="src5">vline[player.x][player.y]=TRUE;<span class="kom">// Ozna�en� linky</span></p>
<p class="src5">player.y--;<span class="kom">// Nahoru</span></p>
<p class="src4">}</p>

<p>Hr��e m�me, d� se ��ci, p�esunut�ho - ale pouze v programu! Je viditeln� st�le na stejn�m m�st�, proto�e ho vykreslujeme pomoc� fx a fy. Provn�me, polohu fx vzhledem k x a pokud se nerovnaj�, sn���me vzd�lenost mezinimi o p�esn� dan� �sek. Po n�kolika p�ekreslen�ch se za�nou ob� hodnoty rovnat, co� zna��, �e dokon�il pohyb a nyn� se nach�z� v pr�se��ku linek. P�i n�sledn�m stisku kl�vesy m��eme za��t hr��e znovu posunovat (viz. k�d v��e).</p>

<p class="src4">if (player.fx&lt;player.x*60)<span class="kom">// Fx je men�� ne� x</span></p>
<p class="src4">{</p>
<p class="src5">player.fx+=steps[adjust];<span class="kom">// Zv�t�� fx</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (player.fx&gt;player.x*60)<span class="kom">// Fx je v�t�� ne� x</span></p>
<p class="src4">{</p>
<p class="src5">player.fx-=steps[adjust];<span class="kom">// Zmen�� fx</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (player.fy&lt;player.y*40)<span class="kom">// Fy je men�� ne� y</span></p>
<p class="src4">{</p>
<p class="src5">player.fy+=steps[adjust];<span class="kom">// Zv�t�� fy</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (player.fy&gt;player.y*40)<span class="kom">// Fy je v�t�� ne� y</span></p>
<p class="src4">{</p>
<p class="src5">player.fy-=steps[adjust];<span class="kom">// Zmen�� fy</span></p>
<p class="src4">}</p>
<p class="src3">}</p>

<p>Nastane-li konec hry, projde program v�tv� else. V n� je pouze test stisku mezern�ku, kter� znovu spust� hru. Nastav�me filled na TRUE a d�ky tomu si program bude myslet, �e je m���ka kompletn� vypln�n� - resetuje se pozice hr��e i nep��tel. Abychom byli p�esn�, program si vlastn� mysl�, �e jsme dokon�ili level, a proto inkrementuje do stage p�i�azenou nulu na jedna. P�esn� tohle chceme. �ivot vr�t�me na po��te�n� hodnotu.</p>

<p class="src3">else<span class="kom">// Jinak (if (!gameover && active))</span></p>
<p class="src3">{</p>
<p class="src4">if (keys[' '])<span class="kom">// Stisknut� mezern�k</span></p>
<p class="src4">{</p>
<p class="src5">gameover = FALSE;<span class="kom">// Konec hry</span></p>
<p class="src5">filled = TRUE;<span class="kom">// M���ka vypln�n�</span></p>
<p class="src"></p>
<p class="src5">level = 1;<span class="kom">// Level</span></p>
<p class="src5">level2 = 1;<span class="kom">// Zobrazovan� level</span></p>
<p class="src5">stage = 0;<span class="kom">// Obt��nost hry</span></p>
<p class="src"></p>
<p class="src5">lives = 5;<span class="kom">// Po�et �ivot�</span></p>
<p class="src4">}</p>
<p class="src3">}</p>

<p>N�sleduj�c� ��st testuje, zda je m���ka kompletn� vypln�n�. Filled m��e b�t nastaveno na TRUE celkem dv�ma zp�soby. Bu� je m���ka �pln� vypln�n�, nebo skon�ila hra (zabit�m hr��e; nula �ivot�) a u�ivatel stiksl mezern�k, aby ji restartoval.</p>

<p class="src3">if (filled)<span class="kom">// Vypln�n� m���ka?</span></p>
<p class="src3">{</p>

<p>A� u� to zp�sobil kter�koli p��pad je n�m to celkem jedno. V�dy zahrajeme zvuk zna��c� ukon�en� levelu. U� jsme jednou vysv�tloval, jak PlaySound() pracuje. P�ed�n�m SND_SYNC vytvo��me �asov� zpo�d�n�, kdy program �ek� a� zvuk dohraje.</p>

<p class="src4">PlaySound(&quot;Data/Complete.wav&quot;, NULL, SND_SYNC);<span class="kom">// Zvuk ukon�en� levelu</span></p>

<p>Potom inkrementujeme stage a zjist�me, jestli nen� v�t�� ne� t�i. Pokud ano, vr�t�me ho na jedno, zv�t��me vnit�n� i zobrazovan� level o jedni�ku.</p>

<p class="src4">stage++;<span class="kom">// Inkrementace obt��nosti</span></p>
<p class="src"></p>
<p class="src4">if (stage &gt; 3)<span class="kom">// Je v�t�� ne� t�i?</span></p>
<p class="src4">{</p>
<p class="src5">stage=1;<span class="kom">// Reset na jedni�ku</span></p>
<p class="src5">level++;<span class="kom">// Zv�t�� level</span></p>
<p class="src5">level2++;<span class="kom">// Zv�t�� zobrazovan� level</span></p>

<p>Pokud bude vnit�n� level v�t�� ne� t�i, vr�t�me ho zp�t na trojku a p�id�me hr��i jeden �ivot, ale pouze do maxim�ln�ch p�ti. V�ce �iv� nikdy nebude.</p>

<p class="src5">if (level&gt;3)<span class="kom">// Je level v�t�� ne� t�i?</span></p>
<p class="src5">{</p>
<p class="src6">level=3;<span class="kom">// Vr�t� ho zp�tky na t�i</span></p>
<p class="src6">lives++;<span class="kom">// �ivot nav�c</span></p>
<p class="src"></p>
<p class="src6">if (lives &gt; 5)<span class="kom">// M� v�c �ivot� ne� p�t?</span></p>
<p class="src6">{</p>
<p class="src7">lives = 5;<span class="kom">// Maxim�ln� po�et �ivot� p�t</span></p>
<p class="src6">}</p>
<p class="src5">} </p>
<p class="src4">}</p>

<p>Resetujeme v�echny objekty ve h�e (h���, nep��tel�) a vynulujeme flag projet� v�ech linek na FALSE. Pokud bychom to neud�lali, dal�� level by byl p�ed�asn� ukon�en - program by op�t sko�il do tohoto k�du. Mimochodem, je �pln� stejn� jako k�d pro vykrelsov�n� m���ky.</p>

<p class="src4">ResetObjects();<span class="kom">// Reset pozice hr��e a nep��tel</span></p>
<p class="src"></p>
<p class="src4">for (loop1=0; loop1&lt;11; loop1++)<span class="kom">// Cyklus skrz x koordin�ty m���ky</span></p>
<p class="src4">{</p>
<p class="src5">for (loop2=0; loop2&lt;11; loop2++)<span class="kom">// Cyklus skrz y koordin�ty m���ky</span></p>
<p class="src5">{</p>
<p class="src6">if (loop1 &lt; 10)<span class="kom">// X mus� b�t men�� ne� deset</span></p>
<p class="src6">{</p>
<p class="src7">hline[loop1][loop2] = FALSE;<span class="kom">// Nulov�n�</span></p>
<p class="src6">}</p>
<p class="src"></p>
<p class="src6">if (loop2 &lt; 10)<span class="kom">// Y mus� b�t men�� ne� deset</span></p>
<p class="src6">{</p>
<p class="src7">vline[loop1][loop2] = FALSE;<span class="kom">// Nulov�n�</span></p>
<p class="src6">}</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src3">}</p>

<p>Pokus�me se umplementovat hr��ovo sebr�n� p�es�pac�ch hodin. �e si mus� polohy odpov�dat je, mysl�m si, jasn�. Nicm�n� p�id�v�me je�t� podm�nku hourgalss.fx==1. Nejedn� se o ��dnou polohu. Fx pou��v�me jako indik�tor toh, �e jsou zobrazen� na monitoru.</p>

<p class="src3"><span class="kom">// Hr�� sebral p�es�pac� hodiny</span></p>
<p class="src3">if ((player.fx==hourglass.x*60) &amp;&amp; (player.fy==hourglass.y*40) &amp;&amp; (hourglass.fx==1))</p>
<p class="src3">{</p>

<p>Nech�me zahr�t zvuk zmrazen�. Aby zvuk zn�l na pozad�, pou��v�me SND_ASYNC. D�ky OR-ov�n� se symbolickou konstantou SND_LOOP doc�l�me toho, �e se po dokon�en� p�ehr�v�n� zvuku s�m znovu spust�. Zastavit ho m��eme bu� po�adavkem na zastven�, nebo p�ehr�n�m jin�ho zvuku.</p>

<p>Aby hodiny nebyly d�le zobrazen� nastav�me fx na dva. Tak� p�i�ad�me do fy nulu. Fy je n�co jako ��ta�, kter� inkrementujeme do ur�it� hodnoty, po jej�m� p�ete�en� zm�n�me hodnotu fx.</p>

<p class="src4">PlaySound(&quot;Data/freeze.wav&quot;, NULL, SND_ASYNC | SND_LOOP);<span class="kom">// Zvuk zmrazen�</span></p>
<p class="src"></p>
<p class="src4">hourglass.fx=2;<span class="kom">// Skryje hodiny</span></p>
<p class="src4">hourglass.fy=0;<span class="kom">// Nuluje ��ta�</span></p>
<p class="src3">}</p>

<p>N�sleduj�c� k�d zaji��uje nar�st�n� rotace hr��e o polovinu ni��� rychlost� ne� m� hra. V p��pad�, �e bude hodnota vy��� ne� 360� ode�teme 360. T�m zajist�me, aby nebyla moc vysok�.</p>

<p class="src3">player.spin += 0.5f * steps[adjust];<span class="kom">// Rotace hr��e</span></p>
<p class="src"></p>
<p class="src3">if (player.spin&gt;360.0f)<span class="kom">// �hel je v�t�� ne� 360�</span></p>
<p class="src3">{</p>
<p class="src4">player.spin -= 360;<span class="kom">// Ode�te 360</span></p>
<p class="src3">}</p>

<p>Aby se hodiny to�ily opa�n�m sm�rem ne� hr��, nam�sto zvy�ov�n�, �hel sni�ujeme. Rychlost je �tvrtinov� oproti rychlosti hry. Op�t o�et��me podte�en� prom�nn�.</p>

<p class="src3">hourglass.spin-=0.25f*steps[adjust];<span class="kom">// Rotace p�es�pac�ch hodin</span></p>
<p class="src"></p>
<p class="src3">if (hourglass.spin &lt; 0.0f)<span class="kom">// �hel je men�� ne� 0�</span></p>
<p class="src3">{</p>
<p class="src4">hourglass.spin += 360.0f;<span class="kom">// P�i�te 360</span></p>
<p class="src3">}</p>

<p>Zv�t��me hodnotu ��ta�e p�es�pac�ch hodin, o kter� jsme mluvili p�ed chv�l�. Op�t podle rychlosti hry. D�le zjist�me, jestli se hourglass.fx rovn� nule (nejsou zobrazen�) a z�rove� jelsti je ��ta� v�t�� ne� 6000 d�leno level. V takov�m p��pad� p�ehrajeme zvuk zobrazen�, vygenerujeme novou pozici a p�es fx=1 hodiny zobraz�me. Vynulujeme ��ta�, aby mohl po��tat znovu.</p>

<p class="src3">hourglass.fy+=steps[adjust];<span class="kom">// Zv�t�en� hodnoty ��ta�e p�es�pac�ch hodin</span></p>
<p class="src"></p>
<p class="src3">if ((hourglass.fx==0) &amp;&amp; (hourglass.fy &gt; 6000/level))<span class="kom">// Hodiny jsou skryt� a p�etekl ��ta�</span></p>
<p class="src3">{</p>
<p class="src4">PlaySound(&quot;Data/hourglass.wav&quot;, NULL, SND_ASYNC);<span class="kom">// Zvuk zobrazen� hodin</span></p>
<p class="src"></p>
<p class="src4">hourglass.x = rand()%10+1;<span class="kom">// N�hodn� pozice</span></p>
<p class="src4">hourglass.y = rand()%11;<span class="kom">// N�hodn� pozice</span></p>
<p class="src"></p>
<p class="src4">hourglass.fx = 1;<span class="kom">// Zobrazen� hodin</span></p>
<p class="src4">hourglass.fy = 0;<span class="kom">// Nulov�n� ��ta�e</span></p>
<p class="src3">}</p>

<p>P�ekl-li ��ta� v dob�, kdy jsou hodiny viditeln� (fx==1), schov�me je a op�t vynulujeme ��ta�.</p>

<p class="src3">if ((hourglass.fx==1) &amp;&amp; (hourglass.fy&gt;6000/level))<span class="kom">// Hodiny jsou zobrazen� a p�etekl ��ta�</span></p>
<p class="src3">{</p>
<p class="src4">hourglass.fx = 0;<span class="kom">// Skr�t hodiny</span></p>
<p class="src4">hourglass.fy = 0;<span class="kom">// Nulov�n� ��ta�e</span></p>
<p class="src3">}</p>

<p>P�i hr��ov� sebr�n� hodin jsme zmrazili v�echny nep��tele. Nyn� je rozmraz�me. Fx==2 indikuje, �e byly hodiny sebr�ny. Fy porovn�v�me s vypo�tenou hodnotou. Jsou-li ob� podm�nyk pravdiv�, vypneme zvuk, kter� zn� ve smy�ce na pozad� a to tak, �e p�ehrajeme nulov� zvuk. Zneviditeln�me hodiny a vynulujeme jejich ��ta�.</p>

<p class="src3">if ((hourglass.fx==2) &amp;&amp; (hourglass.fy&gt;500+(500*level)))<span class="kom">// Nep��tel� zmrazen� a p�etekl ��ta�</span></p>
<p class="src3">{</p>
<p class="src4">PlaySound(NULL, NULL, 0);<span class="kom">// Vypne zvuk zmrazen�</span></p>
<p class="src"></p>
<p class="src4">hourglass.fx = 0;<span class="kom">// Skr�t hodiny</span></p>
<p class="src4">hourglass.fy = 0;<span class="kom">// Nulov�n� ��ta�e</span></p>
<p class="src3">}</p>

<p>Na sam�m konci hlavn� smy�ky programu inkrementujeme prom�nnou delay. To je, mysl�m si, v�e.</p>

<p class="src3">delay++;<span class="kom">// Inkrementuje ��ta� zpo�d�n� nep��tel</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">KillGLWindow();<span class="kom">// Zru�� okno</span></p>
<p class="src1">return (msg.wParam);<span class="kom">// Ukon�� program</span></p>
<p class="src0">}</p>

<p>Psan�m tohoto tutori�lu jsem str�vil spoustu �asu. Za��nal jako zcela jednoduch� tutori�l o link�ch, kter� se �pln� ne�ekan� rozvinul v men�� hru. Doufejme, �e budete moci ve sv�ch programech vyu��t v�e, co jste se zde nau�ili. V�m, �e se spousta z v�s ptala po h�e s kosti�kami a pol��ky. Nemohli jste dostat v�ce kosti�kovat�j�� a v�ce pol��kovat�j�� hru ne� je tato. A�koli lekce nevysv�tluje mnoho nov�ch v�c� o OpenGL, mysl�m si, �e �asov�n� a zvuky jsou tak� d�le�it� - zvlṻ ve hr�ch. Co je�t� napsat? Asi nic...</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson21.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson21_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson21.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson21.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson21.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson21.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson21.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:christop@fhw.gr">Dimi</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson21.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson21.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson21.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:marius@hot.ee">Marius Andra</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson21.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:miqster@gmx.net">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson21.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson21.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson21.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson21.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:chris.j84@free.fr">Christophe</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson21.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson21.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(21);?>
<?FceNeHeOkolniLekce(21);?>

<?
include 'p_end.php';
?>
