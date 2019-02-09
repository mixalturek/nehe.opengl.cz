<?
$g_title = 'CZ NeHe OpenGL - Lekce 32 - Picking, alfa blending, alfa testing, sorting';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(32);?>

<h1>Lekce 32 - Picking, alfa blending, alfa testing, sorting</h1>

<p class="nadpis_clanku">V tomto tutori�lu se pokus�m zodpov�d�t n�kolik ot�zek, na kter� jsem denn� dotazov�n. Chcete v�d�t, jak p�i kliknut� tla��tkem my�i identifikovat OpenGL objekt nach�zej�c� se pod kurzorem (picking). D�le byste se cht�li dozv�d�t, jak vykreslit objekt bez zobrazen� ur�it� barvy (alfa blending, alfa testing). T�et� v�c�, se kterou si nev�te rady, je, jak �adit objekty, aby se p�i blendingu spr�vn� zobrazily (sorting). Naprogramujeme hru, na kter� si v�e vysv�tl�me.</p>

<p>V�tejte do 32. lekce. Je asi nejdel��, jakou jsem kdy napsal - p�es 1000 ��dk� k�du a v�ce ne� 1500 ��dk� HTML. Tak� je prvn�m, kter� pou��v� nov� NeHeGL z�kladn� k�d. Tutori�l zabral hodn� �asu, ale mysl�m si, �e stoj� za to. Prob�r� se v n�m p�edev��m: alfa blending, alfa testing, �ten� zpr�v my�i, sou�asn� pou��v�n� perspektivn� i pravo�hl� projekce, zobrazov�n� kurzoru my�i pomoc� OpenGL, ru�n� �azen� objekt� podle hloubky, sn�mky animace z jedn� textury a to nejd�le�it�j��: nau��te se v�e o pickingu.</p>

<p>V prvn� verzi program zobrazoval t�i polygony, kter� po kliknut� m�nily barvu. Jak vzru�uj�c�! Tak, jako v�dycky, chci zap�sobit super cool tutori�lem. Nejen, �e jsou v n�m zahrnuty v�echny informace k prob�ran�mu t�matu, ale samoz�ejm� mus� b�t tak� hezk� na pohled. Dokonce i tehdy, pokud neprogramujete, v�s m��e zaujmout - kompletn� hra. Objekty se sest�eluj� tak dlouho, dokud v�m neochabne ruka dr��c� my�, tak�e u� nejste schopni stisknout tla��tko.</p>

<p>Pozn�mka ohledn� k�du: Budu vysv�tlovat pouze lesson33.cpp. V NeHeGL jsou zm�ny p�edev��m v podpo�e my�i ve funkci WindowProc(). Tak� u� nebudu vysv�tlovat loading textur, vytv��en� display list� fontu a v�stup textu. V�e bylo vysv�tleno v minul�ch tutori�lech.</p>

<p>Textury, pou��van� v tomto programu, byly nakresleny v Adobe Photoshopu. Ka�d� z .TGA obr�zk� m� barevnou hloubku 32 bit� na pixel, obsahuje tedy alfa kan�l. Pokud si nejste jist�, jak ho p�idat, kupte si n�jakou knihu, prozkoumejte internet nebo zkuste help. Postup je podobn� vytv��en� masky v tutori�lu o maskingu, nahrajte sv�j obr�zek do Adobe Photoshopu nebo jak�hokoli grafick�ho editoru s podporou alfa kan�lu. Prove�te v�b�r barvy, abyste ozna�ili oblast okolo objektu, zkop�rujte v�b�r a vlo�te ho do nov�ho obr�zku. Negujte obr�zek, tak�e oblast, kde by m�l b�t, bude �ern�. Zm��te okol� na b�l�, vyberte cel� obr�zek a zkop�rujte ho. Vra�te se na origin�l a vytvo�te alfa kan�l, do kter�ho vlo�te masku. Ulo�te obr�zek jako 32 bitov� .TGA soubor. Ujist�te se, �e je za�krtnuto Uchovat pr�hlednost a ukl�dejte bez komprese.</p>

<p>Zjist�me, jestli je definovan� symbolick� konstanta CDS_FULLSCREEN a pokud ne, nadefinujeme ji na hodnotu 4. Pro ty z v�s, kte�� se �pln� ztratili... n�kter� kompil�tory nep�i�azuj� CDS_FULLSCREEN hodnotu. Pokud ji pak v programu pou�ijeme, kompilace skon�� s chybovou zpr�vou. Abychom tomuto p�ede�li, tak ji v p��pad� pot�eby nadefinujeme ru�n�.</p>

<p class="src0">#ifndef CDS_FULLSCREEN<span class="kom">// N�kter� kompil�tory nedefinuj� CDS_FULLSCREEN</span></p>
<p class="src1">#define CDS_FULLSCREEN 4<span class="kom">// Ru�n� nadefinov�n�</span></p>
<p class="src0">#endif</p>

<p>Deklarujeme funkci DrawTargets(), potom prom�nnou okna a kl�ves.</p>

<p class="src0">void DrawTargets();<span class="kom">// Deklarace funkce</span></p>
<p class="src"></p>
<p class="src0">GL_Window* g_window;<span class="kom">// Okno</span></p>
<p class="src0">Keys* g_keys;<span class="kom">// Kl�vesy</span></p>

<p>Ka�d� program pot�ebuje prom�nn�. Base ukl�d� display listy fontu, roll slou�� k pohybu zem� a rolov�n� mrak�. Jako ve v�ech hr�ch i my za��n�me prvn�m levelem. Miss vede z�znam, do kolika objekt� se v dan�m levelu st�elec nestrefil, kill je jeho prav� opak. Score zahrnuje sou�ty zasa�en�ch objekt� z jednotliv�ch level�. Game signalizuje konec hry.</p>

<p class="src0">GLuint base;<span class="kom">// Display listy fontu</span></p>
<p class="src0">GLfloat roll;<span class="kom">// Rolov�n� mrak�</span></p>
<p class="src"></p>
<p class="src0">GLint level = 1;<span class="kom">// Aktu�ln� level</span></p>
<p class="src0">GLint miss;<span class="kom">// Po�et nesest�elen�ch objekt�</span></p>
<p class="src0">GLint kills;<span class="kom">// Po�et sest�elen�ch objekt� v dan�m levelu</span></p>
<p class="src0">GLint score;<span class="kom">// Aktu�ln� sk�re</span></p>
<p class="src"></p>
<p class="src0">bool game;<span class="kom">// Konec hry?</span></p>

<p>Nadefinujeme nov� datov� typ, d�ky kter�mu budeme moci p�edat struktury porovn�vac� funkci. Qsort() toti� o�ek�v� v posledn�m parametru ukazatel na funkci s parametry (const* void, const* void).</p>

<p class="src0">typedef int (*compfn)(const void*, const void*);<span class="kom">// Ukazatel na porovn�vac� funkci</span></p>

<p>Struktura objects bude ukl�dat v�echny informace popisuj�c� sest�elovan� objekt. Rychl� pr�zkum prom�nn�ch: rot ur�uje sm�r rotace na ose z. Pokud je�t� nebyl objekt sest�elen, hit bude obsahovat false. Frame definuje sn�mek animace p�i explozi, dir ur�uje sm�r pohybu. Texid je indexem do pole textur, nab�v� hodnot nula a� �ty�i, z �eho� plyne, �e m�me celkem p�t druh� objekt�. X a y definuje aktu�ln� pozici, spin �hel rotace na ose z. Distance je hodn� d�le�it� prom�nn�, ur�uje hloubku ve sc�n�. Pr�v� podle n� budeme p�i blendingu �adit objekty, aby se nejd��ve vykreslovali vzd�len�j�� a a� po nich bli���.</p>

<p class="src0">struct objects<span class="kom">// Struktura objektu</span></p>
<p class="src0">{</p>
<p class="src1">GLuint rot;<span class="kom">// Rotace (0 - ��dn�, 1 - po sm�ru hodinov�ch ru�i�ek, 2 - proti sm�ru)</span></p>
<p class="src1">bool hit;<span class="kom">// Byl objekt zasa�en?</span></p>
<p class="src"></p>
<p class="src1">GLuint frame;<span class="kom">// Aktu�ln� sn�mek exploze</span></p>
<p class="src1">GLuint dir;<span class="kom">// Sm�r pohybu (0 - vlevo, 1 - vpravo, 2 - nahoru, 3 - dol�)</span></p>
<p class="src1">GLuint texid;<span class="kom">// Index do pole textur</span></p>
<p class="src"></p>
<p class="src1">GLfloat x;<span class="kom">// X pozice</span></p>
<p class="src1">GLfloat y;<span class="kom">// Y pozice</span></p>
<p class="src1">GLfloat spin;<span class="kom">// Sm�r rotace na ose z</span></p>
<p class="src1">GLfloat distance;<span class="kom">// Hloubka ve sc�n�</span></p>
<p class="src0">};</p>

<p>N�sleduj�c� pole vedou z�znamy o deseti textur�ch a t�iceti objektech.</p>

<p class="src0">TextureImage textures[10];<span class="kom">// Deset textur</span></p>
<p class="src0">objects object[30];<span class="kom">// 30 Objekt�</span></p>

<p>Nebudeme limitovat velikost objekt�. V�za by m�la b�t vy��� ne� plechovka coly a k�bl naopak �ir�� ne� v�za. Abychom si uleh�ili �ivot, vytvo��me strukturu obsahuj�c� v��ku a ���ku. Definujeme a ihned inicializujeme pole t�chto struktur o p�ti prvc�ch. Na ka�d�m indexu se nach�z� jeden z p�ti typ� objekt�.</p>

<p class="src0">struct dimensions<span class="kom">// Rozm�r objektu</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat w;<span class="kom">// ���ka</span></p>
<p class="src1">GLfloat h;<span class="kom">// V��ka</span></p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Velikost ka�d�ho objektu: Modr� tv��, k�bl, ter�, Coca-cola, V�za</span></p>
<p class="src0">dimensions size[5] = {{1.0f,1.0f}, {1.0f,1.0f}, {1.0f,1.0f}, {0.5f,1.0f}, {0.75f,1.5f}};</p>

<p>Tento k�d bude vol�n funkc� qsort(). Porovn�v� hloubku dvou objekt� ve sc�n� a vrac� -1, pokud je prvn� objekt d�le, bude-li ale vzd�len�j�� druh� objekt vr�t� funkce 1. Z�sk�me-li 0, znamen� to, �e jsou oba ve stejn� vzd�lenosti od pozorovatele.</p>

<p class="src0"><span class="kom">// *** Modifikovan� MSDN k�d pro tento tutori�l ***</span></p>
<p class="src0">int Compare(struct objects *elem1, struct objects *elem2)<span class="kom">// Porovn�vac� funkce</span></p>
<p class="src0">{</p>
<p class="src1">if (elem1-&gt;distance &lt; elem2-&gt;distance)<span class="kom">// Prvn� je vzd�len�j��</span></p>
<p class="src1">{</p>
<p class="src2">return -1;</p>
<p class="src1">}</p>
<p class="src1">else if (elem1-&gt;distance &gt; elem2-&gt;distance)<span class="kom">// Prvn� je bli���</span></p>
<p class="src1">{</p>
<p class="src2">return 1;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Vzd�lenosti jsou stejn�</span></p>
<p class="src1">{</p>
<p class="src2">return 0;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Ve funkci InitObject() nastavujeme objekt na v�choz� hodnoty. P�i�ad�me mu rotaci po sm�ru hodinov�ch ru�i�ek. Animace exploze samoz�ejm� za��n� na prvn�m (nult�m) sn�mku. Objekt je�t� nebyl zasa�en, tak�e nastav�me hit na false. Randomem zvol�me jednu z p�ti dostupn�ch textur.</p>

<p class="src0">GLvoid InitObject(int num)<span class="kom">// Inicializace objektu</span></p>
<p class="src0">{</p>
<p class="src1">object[num].rot = 1;<span class="kom">// Rotace po sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src1">object[num].frame = 0;<span class="kom">// Prvn� sn�mek exploze</span></p>
<p class="src1">object[num].hit = FALSE;<span class="kom">// Je�t� nebyl zasa�en</span></p>
<p class="src1">object[num].texid = rand() % 5;<span class="kom">// N�hodn� index textury</span></p>

<p>Vzd�lenost od pozorovatele nastav�me op�t n�hodn� na hodnotu 0.0f a� -40.0f (4000/100 = 40). P�ed renderingem objektu v�ak sc�nu je�t� posouv�me do hloubky o dal��ch deset jednotek, tak�e se objekt defakto zobraz� v rozmez� od -10.0f do -50.0f. Ani p��li� bl�zko ani p��li� daleko.</p>

<p class="src1">object[num].distance = -(float(rand() % 4001) / 100.0f);<span class="kom">// N�hodn� hloubka</span></p>

<p>Po definov�n� hloubky ur��me v��ku nad zem�. Nechceme, aby se objekt nach�zel n��e ne� -1.5f, proto�e by byl pod zem�. Tak� by nem�l b�t v��e ne� 3.0f. Abychom z�stali v tomto rozmez�, v�sledek randomu nesm� b�t vy��� ne� 4.5f (-1.5f + 4.5f = 3.0f).</p>

<p class="src1">object[num].y = -1.5f + (float(rand() % 451) / 100.0f);<span class="kom">// N�hodn� y pozice</span></p>

<p>V�po�et po��te�n� x pozice je mali�ko slo�it�j��. Vezmeme pozici objektu v hloubce a ode�teme od n� 15.0f. V�sledek operace vyd�l�me dv�ma a ode�teme od n�j 5*level. N�sleduje dal�� od��t�n�. Tentokr�t ode�teme n�hodn� ��slo od 0 do 5 n�soben� aktu�ln�m levelem. P�edpokl�d�m, �e nech�pete :-). Objekty se nyn� ve vy���ch levelech zobrazuj� d�le od viditeln� ��sti sc�ny (vlevo nebo vpravo). Kdybychom toto neud�lali, zobrazovaly by se rychle jeden za druh�m, tak�e by bylo velmi obt��n� v�echny zas�hnout a dostat se tak do dal��ho levelu.</p>

<p>Abyste l�pe pochopili ur�ov�n� x pozice, uvedu p��klad. �ekn�me, �e se objekt nach�z� -30.0f jednotek hluboko ve sc�n� a aktu�ln� level je 1.</p>

<p class="src0"><span class="kom">object[num].x = ((-30.0f - 15.0f) / 2.0f) - (5*1) - float(rand() % (5*1));</span></p>
<p class="src0"><span class="kom">object[num].x = (-45.0f / 2.0f) - 5 - float(rand() % 5);</span></p>
<p class="src0"><span class="kom">object[num].x = (-22.5f) - 5 - { �ekn�me 3.0f };</span></p>
<p class="src0"><span class="kom">object[num].x = (-22.5f) - 5 - { 3.0f };</span></p>
<p class="src0"><span class="kom">object[num].x = -27.5f - { 3.0f };</span></p>
<p class="src0"><span class="kom">object[num].x = -30.5f;</span></p>

<p>P�ed renderingem objektu prov�d�me translaci o deset jednotek do sc�ny na ose z a hloubka v na�em p��kladu je -30.0f. Celkov� hloubka ve sc�n� je tedy -40.0f. Pou��v�n�m perspektivn�ho k�du z NeHeGL m��eme p�edpokl�dat, �e lev� okraj viditeln� sc�ny je -20.0f a prav� okraj se nach�z� na +20.0f. P�ed ode��t�n�m random� se rovn� x-ov� pozice -22.5f, co� je PR�V� okraj viditeln� sc�ny. Po t�chto operac�ch to u� je ale -30.0f a to znamen�, �e ne� se poprv� objev�, mus� nejd��ve urazit cel�ch 8 jednotek doprava. U� je to jasn�j��?</p>

<p class="src1"><span class="kom">// N�hodn� x pozice zalo�en� na hloubce v obrazovce a s n�hodn�m zpo�d�n�m p�ed vstupem na sc�nu</span></p>
<p class="src1">object[num].x = ((object[num].distance - 15.0f) / 2.0f) - (5*level) - float(rand() % (5*level));</p>

<p>Nakonec zvol�me n�hodn� sm�r pohybu: 0 vlevo nebo 1 vpravo.</p>

<p class="src1">object[num].dir = (rand() % 2);<span class="kom">// N�hodn� sm�r pohybu</span></p>

<p>Nyn� se pod�v�me, kter�m sm�rem se bude objekt posunovat. Pokud p�jde doleva (dir == 0), zm�n�me rotaci na proti sm�ru hodinov�ch ru�i�ek (rot = 2). Pozice na ose x je defaultn� z�porn�. Nicm�n�, pokud se m�me pohybovat vlevo, mus�me se na za��tku nach�zet vpravo. Negujeme tedy hodnotu x.</p>

<p class="src1">if (object[num].dir == 0)<span class="kom">// Pohybuje se doleva?</span></p>
<p class="src1">{</p>
<p class="src2">object[num].rot = 2;<span class="kom">// Rotace proti sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src2">object[num].x = -object[num].x;<span class="kom">// V�choz� pozice vpravo</span></p>
<p class="src1">}</p>

<p>Zjist�me, kter� druh objektu po��ta� vybral. Pokud se index textury rovn� nule, zvolil texturu modr� tv��e a ty se v�dy pohybuj� t�sn� nad zem�. Ru�n� nastav�me y pozici na -2.0f.</p>

<p class="src1">if (object[num].texid == 0)<span class="kom">// Modr� tv��</span></p>
<p class="src1">{</p>
<p class="src2">object[num].y = -2.0f;<span class="kom">// V�dy t�sn� nad zem�</span></p>
<p class="src1">}</p>

<p>Pr�ce s objektem k�blu bude slo�it�j��. Padaj� toti� z nebe (dir = 3). Z toho tak� plyne, �e bychom m�li nastavit novou x-ovou pozici, proto�e by nikdy nebyl vid�t (objekty jsou na za��tku v�dy vlevo nebo vpravo od sc�ny). Nam�sto ode��t�n� 15 z minul�ho p��kladu ode�teme pouze 10. T�mto dos�hneme men��ho rozmez� hodnot, kter� udr�� objekt viditeln� na sc�n�. P�edpokl�d�me-li, �e se hloubka rovn� -30.0f, skon��me s n�hodnou hodnotou od 0.0f do +40.0f. Horn� hodnota je kladn� a ne z�porn�, jak by se mohlo zd�t, proto�e rand() v�dy vrac� kladn� ��slo. Z�skali jsme tedy ��slo od 0.0f do 40.0f, k n�mu p�i�teme hloubku (z�porn� ��slo) m�nus 10.0f a to cel� d�len� dv�ma. Op�t p��klad: p�epokl�d�me, �e vr�cen� n�hodn� hodnota je 15 a objekt se nach�z� ve vzd�lenosti -30.0f jednotek.</p>

<p class="src0"><span class="kom">object[num].x = float(rand() % int(-30.0f - 10.0f)) + ((-30.0f - 10.0f) / 2.0f);</span></p>
<p class="src0"><span class="kom">object[num].x = float(rand() % int(-40.0f) + (-40.0f) / 2.0f);</span></p>
<p class="src0"><span class="kom">object[num].x = { p�edpokl�dejme 15 } + (-20.0f);</span></p>
<p class="src0"><span class="kom">object[num].x = 15.0f - 20.0f;</span></p>
<p class="src0"><span class="kom">object[num].x = -5.0f;</span></p>

<p>Nakonec ur��me um�st�n� na ose y. Chceme, aby padal z oblohy, ale nevystupoval z mrak�. ��slo 4.5f odpov�d� pozici mali�ko n��e pod mraky.</p>

<p class="src1">if (object[num].texid == 1)<span class="kom">// K�bl</span></p>
<p class="src1">{</p>
<p class="src2">object[num].dir = 3;<span class="kom">// Pad� dol�</span></p>
<p class="src2">object[num].x = float(rand() % int(object[num].distance - 10.0f)) + ((object[num].distance - 10.0f) / 2.0f);</p>
<p class="src2">object[num].y = 4.5f;<span class="kom">// T�sn� pod mraky</span></p>
<p class="src1">}</p>

<p>Objekt ter�e by m�l vystoupit nahoru ze zem� (dir = 2). Pro um�st�n� na ose x pou�ijeme stejn� postup jako p�ed chv�l�. Nechceme, aby jeho po��te�n� poloha za��nala nad zem�, tak�e nastav�me y na -3.0f (pod zem�). Od n�j ode�teme n�hodn� ��slo od nuly do 5*level, aby se neobjevil hned, ale se zpo�d�n�m a� po chv�li. ��m vy��� level, t�m d�le trv�, ne� se objev�. To d�v� hr��i trochu �asu na vzpamatov�n� se - bez t�to operace by ter�e vyskakovaly rychle jeden za druh�m.</p>

<p class="src1">if (object[num].texid == 2)<span class="kom">// Ter�</span></p>
<p class="src1">{</p>
<p class="src2">object[num].dir = 2;<span class="kom">// Vylet� vzh�ru</span></p>
<p class="src2">object[num].x = float(rand() % int(object[num].distance - 10.0f)) + ((object[num].distance - 10.0f) / 2.0f);</p>
<p class="src2">object[num].y = -3.0f - float(rand() % (5*level));<span class="kom">// Pod zem�</span></p>
<p class="src1">}</p>

<p>V�echny ostatn� objekty se pohybuj� zleva doprava, a proto nen� nutn�, abychom jejich nastaven� n�jak�m zp�sobem m�nili.</p>

<p>Mohli bychom u� skon�it, ale zb�v� je�t� ud�lat jednu velice d�le�itou v�c. Aby alfa blending pracoval spr�vn�, mus� b�t pr�hledn� polygony vykreslov�ny od nejvzd�len�j��ch po nejbli��� a nesm� se prot�nat. Z buffer toti� vy�azuje vzd�len�j�� polygony, jsou-li ji� n�jak� p�ed nimi. Kdyby ty p�edn� nebyly pr�hledn�, ni�emu by to nevadilo a nav�c by se rendering urychlil, nicm�n�, kdy� jsou objekty vep�edu pr�hledn�, tak by objekty za nimi m�ly b�t vid�t. Nyn� se bu� nezobraz� nebo je kolem p�edn�ch vykreslen �tvercov� tvar, reprezentuj�c� p�vodn� polygon bez pr�hlednosti... nic hezk�ho.</p>

<p>Zn�me hloubku v�ech objekt�, tak�e nen� ��dn� probl�m, abychom je po inicializaci nov�ho se�adili, jak pot�ebujeme. Pou�ijeme standardn� funkci qsort() (quick sort - rychl� �azen�). P�i n�sledn�m renderingu vezmeme prvn� prvek pole a vykresl�me ho. Nebudeme se muset o nic starat, proto�e v�me, �e je ve sc�n� nejhloub�ji.</p>

<p>Tento k�d jsem nalezl v MSDN, ale �sp�chu p�edch�zelo dlouh� hled�n� na internetu. Funkce qsort() pracuje dob�e a dovoluje �adit cel� struktury. P�ed�v�me j� �ty�i parametry. Prvn� ukazuje na pole objekt�, kter� maj� b�t se�azeny, druh� ur�uje jejich po�et (odpov�d� aktu�ln�mu levelu). T�et� parametr definuje velikost jedn� struktury a �tvrt� je ukazatelem na porovn�vac� funkci Compare(). S nejv�t�� pravd�podobnost� existuje n�jak� lep�� metoda pro �azen� struktur, ale qsort() vyhovuje. Je rychl� a snadno se pou��v�.</p>

<p>D�le�it� pozn�mka: Pokud pou��v�te glAlphaFunc() a glEnable(GL_ALPHA_TEST) nam�sto &quot;klasick�ho&quot; blendingu, nen� �azen� nutn�. Pou��v�n�m alpha funkc� jste ale omezeni na �plnou pr�hlednost nebo �plnou nepr�hlednost, nic mezi t�m. Pou��v�n� BlendFunc() a �azen� objekt� stoj� sice trochu pr�ce nav�c, ale dovoluje m�t objekty polopr�hledn�.</p>

<p class="src1"><span class="kom">// *** Modifikovan� MSDN k�d pro tento tutori�l ***</span></p>
<p class="src1">qsort((void *) &amp;object, level, sizeof(struct objects), (compfn)Compare);<span class="kom">// �azen� objekt� podle hloubky</span></p>
<p class="src0">}</p>

<p>Prvn� dva p��kazy v inicializa�n�m k�du nagrabuj� informace o okn� a indik�toru stisknut�ch kl�ves. Funkc� srand() inicializujeme gener�tor n�hodn�ch ��sel, potom loadujeme textury a vytvo��me display listy fontu.</p>

<p class="src0">BOOL Initialize (GL_Window* window, Keys* keys)<span class="kom">// Inicializace OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;</p>
<p class="src1">g_keys = keys;</p>
<p class="src"></p>
<p class="src1">srand((unsigned)time(NULL));<span class="kom">// Inicializace gener�toru n�hodn�ch ��sel</span></p>
<p class="src"></p>
<p class="src1">if ((!LoadTGA(&amp;textures[0],&quot;Data/BlueFace.tga&quot;)) ||<span class="kom">// Modr� tv��</span></p>
<p class="src2">(!LoadTGA(&amp;textures[1],&quot;Data/Bucket.tga&quot;)) ||<span class="kom">// Kbel�k</span></p>
<p class="src2">(!LoadTGA(&amp;textures[2],&quot;Data/Target.tga&quot;)) ||<span class="kom">// Ter�</span></p>
<p class="src2">(!LoadTGA(&amp;textures[3],&quot;Data/Coke.tga&quot;)) ||<span class="kom">// Coca-Cola</span></p>
<p class="src2">(!LoadTGA(&amp;textures[4],&quot;Data/Vase.tga&quot;)) ||<span class="kom">// V�za</span></p>
<p class="src2">(!LoadTGA(&amp;textures[5],&quot;Data/Explode.tga&quot;)) ||<span class="kom">// Exploze</span></p>
<p class="src2">(!LoadTGA(&amp;textures[6],&quot;Data/Ground.tga&quot;)) ||<span class="kom">// Zem�</span></p>
<p class="src2">(!LoadTGA(&amp;textures[7],&quot;Data/Sky.tga&quot;)) ||<span class="kom">// Obloha</span></p>
<p class="src2">(!LoadTGA(&amp;textures[8],&quot;Data/Crosshair.tga&quot;)) ||<span class="kom">// Kurzor</span></p>
<p class="src2">(!LoadTGA(&amp;textures[9],&quot;Data/Font.tga&quot;)))<span class="kom">// Font</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Inicializace se nezda�ila</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">BuildFont();<span class="kom">// Vytvo�� display listy fontu</span></p>

<p>Nastav�me �ern� pozad�. Depth bufferem testujeme na m�n� nebo rovno (GL_LEQUAL).</p>

<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� depth bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testov�n� hloubky</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>

<p>P��kaz glBlendFunc() je VELMI d�le�it�. Parametry GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA oznamuj� OpenGL, aby p�i renderingu pou��valo alfa hodnoty ulo�en� v textu�e. Aby se blending mohl projevit, mus�me ho zapnout. D�le zap�n�me i mapov�n� 2D textur a o�ez�v�n� zadn�ch stran polygon�. P�i kreslen� zad�v�me sou�adnice polygon� proti sm�ru hodinov�ch ru�i�ek, tak�e odstran�n� zadn�ch stran polygon� ni�emu nevad�. Nav�c se program urychl�, proto�e m� s kreslen�m pouze polovinu pr�ce.</p>

<p>V��e v tutori�lu jsem psal o pou�it� glAlphaFunc() nam�sto blendingu. Pokud chcete pou��vat rad�ji alfa funkci, zakoment��ujte dva ��dky d�le�it� pro blending a odkoment��ujte dva ��dky alfy. Zakoment��ovat m��ete tak� �azen� objekt� pomoc� qsort() a v�e s n�m spojen�. P�i alfa testingu nen� po�ad� renderingu d�le�it�.</p>

<p>Program p�jde v po��dku, ale obloha se nezobraz�. P���inou je jej� textura, kter� m� alfa hodnotu 0.5f. Alfa, narozd�l od blendingu, v�ak m��e b�t bu� nula nebo jedna, nic mezi. Probl�m lze vy�e�it modifikac� alfa kan�lu textury. Ob� metody p�in�ej� velmi dobr� v�sledky.</p>

<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);<span class="kom">// Nastaven� alfa blendingu</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne alfa blending</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// glAlphaFunc(GL_GREATER, 0.1f);// Nastaven� alfa testingu</span></p>
<p class="src1"><span class="kom">// glEnable(GL_ALPHA_TEST);// Zapne alfa testing</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>
<p class="src1">glEnable(GL_CULL_FACE);<span class="kom">// O�ez�v�n� zadn�ch stran polygon�</span></p>

<p>Na tomto m�st� inicializujeme v�echny objekty, kter� program pou��v� a potom ukon��me funkci.</p>

<p class="src1">for (int loop = 0; loop &lt; 30; loop++)<span class="kom">// Proch�z� v�echny objekty</span></p>
<p class="src1">{</p>
<p class="src2">InitObject(loop);<span class="kom">// Inicializace ka�d�ho z nich</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace �sp�n�</span></p>
<p class="src0">}</p>

<p>Naprogramujeme detekci z�sah� do objekt�. Ze v�eho nejd��ve deklarujeme buffer, kter� pou�ijeme k ulo�en� informac� o vybran�ch objektech. Prom�nn� hits slou�� k po��t�n� z�sah�.</p>

<p class="src0">void Selection(void)<span class="kom">// Detekce zasa�en� objekt�</span></p>
<p class="src0">{</p>
<p class="src1">GLuint buffer[512];<span class="kom">// Deklarace selection bufferu</span></p>
<p class="src1">GLint hits;<span class="kom">// Po�et zasa�en�ch objekt�</span></p>

<p>Skon�ila-li hra, nen� ��dn� d�vod, abychom hledali, kter� objekt byl zasa�en, a proto ukon��me funkci. Pokud je hr�� st�le ve h�e, p�ehrajeme zvuk v�st�elu. Tato funkce je vol�na pouze tehdy, kdy� hr�� stiskl tla��tko my�i. A pokud stiskl tla��tko my�i, znamen� to, �e cht�l vyst�elit. Nez�le��, jestli zas�hl nebo ne, zvuk v�st�elu je sly�et v�dy. P�ehrajeme ho v asynchron�m m�du (SND_ASYNC), aby b�el na pozad� a program nemusel �ekat a� skon��.</p>

<p class="src1">if (game)<span class="kom">// Konec hry?</span></p>
<p class="src1">{</p>
<p class="src2">return;<span class="kom">// Nen� d�vod testovat na z�sah</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">PlaySound(&quot;data/shot.wav&quot;, NULL, SND_ASYNC);<span class="kom">// P�ehraje zvuk v�st�elu</span></p>

<p>Nastav�me pole viewport tak, aby obsahovalo pozici x, y se ���kou a v��kou aktu�ln�ho viewportu (OpenGL okna). Vol�n�m funkce glSelectBuffer() na��d�me OpenGL, aby pou�ilo na�e pole buffer pro sv�j selection buffer.</p>

<p class="src1">GLint viewport[4];<span class="kom">// Velikost viewportu. [0] = x, [1] = y, [2] = v��ka, [3] = ���ka</span></p>
<p class="src"></p>
<p class="src1">glGetIntegerv(GL_VIEWPORT, viewport);<span class="kom">// Nastav� pole podle velikosti a lokace sc�ny relativn� k oknu</span></p>
<p class="src1">glSelectBuffer(512, buffer);<span class="kom">// P�ik�e OpenGL, aby pro selekci objekt� pou�ilo pole buffer</span></p>

<p>V�echen k�d n��e je velmi d�le�it�. Nejd��ve p�evedeme OpenGL do selection m�du. Nic, co se vykresluje, se nezobraz�, ale nam�sto toho se informace o renderovan�ch objektech ulo�� do selection bufferu. Potom vol�n�m glInitNames() a glPushName(0) inicializujeme name stack (stack jmen). Kdyby OpenGL nebylo v selection m�du, glPushName() by bylo ignorov�no.</p>

<p class="src1">(void) glRenderMode(GL_SELECT);<span class="kom">// P�eveden� OpenGL do selection m�du</span></p>
<p class="src"></p>
<p class="src1">glInitNames();<span class="kom">// Inicializace name stacku</span></p>
<p class="src1">glPushName(0);<span class="kom">// Vlo�� 0 (nejm�n� jedna polo�ka) na stack</span></p>

<p>Po p��prav� name stacku mus�me omezit kreslen� na oblast pod kurzorem. Zvol�me projek�n� matici, pushneme ji na stack a resetujeme ji vol�n�m glLoadIdentity().</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvol� projek�n� matici</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� projek�n� matice</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Oblast kreslen� omez�me p��kazem gluPickMatrix(). Prvn� parametr ur�uje pozice my�i na ose x, druh� je na ose y. Jedni�ky p�edstavuj� ���ku a v��ku picking regionu. Posledn�m parametrem je pole viewport, kter� ur�uje aktu�ln� okraje viewportu. Mouse_x a mouse_y budou st�edem picking regionu.</p>

<p class="src1"><span class="kom">// Vytvo�en� matice, kter� zv�t�� malou ��st obrazovky okolo kurzoru my�i</span></p>
<p class="src1">gluPickMatrix((GLdouble) mouse_x, (GLdouble) (viewport[3] - mouse_y), 1.0f, 1.0f, viewport);</p>

<p>Vol�n�m gluPerspective vyn�sob�me perspektivn� matici pick matic�, kter� omezuje vykreslov�n� na oblast vy��danou od gluPickMatrix(). Potom p�epneme na matici modelview a vykresl�me sest�elovan� objekty. Kresl�me je funkc� DrawTargets() a ne Draw(), proto�e chceme ur�it z�sahy do objekt� a ne do oblohy, zem� nebo kurzoru. Po vykreslen� objekt� p�epneme zp�t na projek�n� matici a popneme ji ze stacku. Nakonec se znovu vr�t�me k matici modelview. Posledn�m p��kazem p�epneme OpenGL zp�t do renderovac�ho m�du, tak�e se op�t budou vykreslovan� objekty zobrazovat na sc�nu. Prom�nn� hits bude po p�i�azen� obsahovat po�et objekt�, kter� byly vykresleny na oblast specifikovanou gluPickMatrix(). Tedy tam, kde se nach�zel kurzor my�i p�i v�st�elu.</p>

<p class="src1"><span class="kom">// Aplikov�n� perspektivn� matice</span></p>
<p class="src1">gluPerspective(45.0f, (GLfloat) (viewport[2] - viewport[0]) / (GLfloat) (viewport[3] - viewport[1]), 0.1f, 100.0f);</p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Modelview matice</span></p>
<p class="src"></p>
<p class="src1">DrawTargets();<span class="kom">// Renderuje objekty do selection bufferu</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Projek�n� matice</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� projek�n� matice</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Modelview matice</span></p>
<p class="src"></p>
<p class="src1">hits = glRenderMode(GL_RENDER);<span class="kom">// P�epnut� do renderovac�ho m�du, ulo�en� po�tu objekt� pod kurzorem</span></p>

<p>Zjist�me, jestli bylo zaznamen�no v�ce ne� nula z�sah�. Pokud ano, p�i�ad�me prom�nn� choose jm�no prvn�ho objektu, kter� byl vykreslen do picking oblasti. Depth ukl�d�, jak hluboko ve sc�n� se tento objekt nach�z�. Ka�d� z�sah zab�r� v bufferu �ty�i polo�ky. Prvn� je po�tem jmen v name stacku, kdy� se z�sah ud�l. Druh� polo�ka p�edstavuje minim�ln� z hodnotu (hloubku) ze v�ech vertex�, kter� prot�naly zobrazenou oblast v �ase z�sahu. T�et� naopak obsahuje maxim�ln� z hodnotu a posledn� polo�ka je obsahem name stacku v �ase z�sahu, nebo-li jm�no objektu. V tomto programu n�s zaj�m� minim�ln� z hodnota a jm�no objektu.</p>

<p class="src1">if (hits &gt; 0)<span class="kom">// Bylo v�ce ne� nula z�sah�?</span></p>
<p class="src1">{</p>
<p class="src2">int choose = buffer[3];<span class="kom">// Ulo�� jm�no prvn�ho objektu</span></p>
<p class="src2">int depth = buffer[1];<span class="kom">// Ulo�� jeho hloubku</span></p>

<p>Zalo��me cyklus skrz v�echny z�sahy, abychom se ujistili, �e ��dn� z objekt� nen� bl��e ne� ten prvn�. Jin�mi slovy pot�ebujeme naj�t nejbli��� objekt ke st�elci. Kdybychom ho nehledali a st�elec zas�hl dva p�ekr�vaj�c� se objekty najednou, mohl by ten v po�ad� pole prvn� b�t vzd�len�j�� od pozorovatele. kliknut� my�� by sest�elilo �patn� objekt. Je jasn�, �e pokud je n�kolik ter�� za sebou, tak se p�i v�st�elu zas�hne v�dy ten nejbli���.</p>

<p>Ka�d� objekt m� v poli buffer �ty�i polo�ky, tak�e n�sob�me aktu�ln� pr�b�h �ty�mi. Abychom z�skali hloubku objektu (druh� polo�ka), p�i��t�me jedni�ku. pokud je pr�v� testovan� hloubka men�� ne� aktu�ln� nejni���, p�ip��eme informace o jm�nu objektu a jeho hloubce. Po v�ech pr�chodech cyklem, bude choose obsahovat jm�no ke st�elci nejbli���ho zasa�en�ho objektu a depth jeho hloubku.</p>

<p class="src2">for (int loop = 1; loop &lt; hits; loop++)<span class="kom">// Proch�z� v�echny detekovan� z�sahy</span></p>
<p class="src2">{</p>
<p class="src3">if (buffer[loop*4 + 1] &lt; GLuint(depth))<span class="kom">// Je tento objekt bl��e ne� n�kter� z p�edchoz�ch?</span></p>
<p class="src3">{</p>
<p class="src4">choose = buffer[loop*4 + 3];<span class="kom">// Ulo�� jm�no bli���ho objektu</span></p>
<p class="src4">depth = buffer[loop*4 + 1];<span class="kom">// Ulo�� jeho hloubku</span></p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Na�li jsme zasa�en� objekt. P�i�azen�m TRUE do hit ho ozna��me, aby nemohl b�t zasa�en po druh� nebo zni�en automaticky po opu�t�n� sc�ny. P�i�teme k hr��ovu score jedni�ku a tak� inkrementujeme po�et z�sah� v dan�m levelu.</p>

<p class="src2">if (!object[choose].hit)<span class="kom">// Nebyl je�t� objekt zasa�en?</span></p>
<p class="src2">{</p>
<p class="src3">object[choose].hit = TRUE;<span class="kom">// Ozna�� ho jako zasa�en�</span></p>
<p class="src"></p>
<p class="src3">score += 1;<span class="kom">// Zv��� celkov� sk�re</span></p>
<p class="src3">kills += 1;<span class="kom">// Zv��� po�et z�sah� v levelu</span></p>

<p>Chceme, aby v ka�d�m n�sleduj�c�m levelu musel hr�� sest�elit v�t�� po�et objekt�. T�m se znesnad�uje postup mezi levely. Zkontrolujeme, jestli je kills v�t�� ne� aktu�ln� level n�soben� p�ti. V levelu jedna sta�� pro postup sest�elit pouze p�t objekt� (1*5). V druh�m levelu u� je to deset (2*5), atd. Hra za��n� b�t t쾹� a t쾹�.</p>

<p>Nastal-li �as pro p�esun do n�sleduj�ho levelu, nastav�me po�et nezasa�en�ch objekt� na nulu, aby j�m m�l hr�� v�t�� �anci �sp�n� proj�t. Ale aby v�e nebylo zase tak jednoduch�, vynulujeme i po�et zasa�en�ch objekt�. Nakonec nesm�me zapomenout inkrementovat level a otestovat, jestli u� nebyl posledn�. D�vod pro� m�me zrovna t�icet level� je velice jednoduch�. T�ic�t� level je u� ��len� obt��n�, mysl�m, �e nikdo nem� �anci ho dos�hnout. Druh�m d�vodem je maxim�ln� po�et objekt� - je jich pr�v� t�icet. Chcete-li jich v�ce poupravujte program.</p>

<p>Na sc�n� m��ete m�t ale MAXIM�LN� 64 objekt� (0 a� 63). Pokud jich zkus�te renderovat 65 a v�ce, PICKING P�ESTANE PRACOVAT SPR�VN� a za�nou se d�t podivn� v�ci. V�echno od n�hodn� vybuchuj�c�ch objekt� a� k cel�mu va�emu po��ta�i se kompletn� zhrout�. 64 objekt� je fyzik�ln� limit OpenGL, stejn� jako nap��klad 8 sv�tel ve sc�n�.</p>

<p>Pokud jste n�jakou ��astnou n�hodou bohem :-) a dostanete se a� k t�ic�t�mu levelu, v��e u� bohu�el nepostoup�te. Nicm�n� celkov� sk�re se bude st�le zvy�ovat a po�et zasa�en�ch i nezasa�en�ch objekt� se v�dy na tomto m�st� resetuje.</p>

<p class="src3">if (kills &gt; level*5)<span class="kom">// �as pro dal�� level?</span></p>
<p class="src3">{</p>
<p class="src4">miss = 0;<span class="kom">// Nulov�n� nezasa�en�ch objekt�</span></p>
<p class="src4">kills = 0;<span class="kom">// Nulov�n� zasa�en�ch objekt� v tomto levelu</span></p>
<p class="src4">level += 1;<span class="kom">// Posun na dal�� level</span></p>
<p class="src"></p>
<p class="src4">if (level &gt; 30)<span class="kom">// Posledn� level?</span></p>
<p class="src4">{</p>
<p class="src5">level = 30;<span class="kom">// Nastaven� levelu na posledn�</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Ve funkci Update() testujeme stisk kl�ves a aktualizujeme um�st�n� objekt� ve sc�n�. Jednou z p��jemn�ch v�c� je p�ed�van� parametr miliseconds, kter� definuje uplynul� �as od p�edchoz�ho vol�n�. Na jeho b�zi posuneme objekt o danou vzd�lenost. A v�sledek? Hra p�jde stejn� rychle na libovoln�m procesoru. ALE je zde jeden nedostatek. �ekn�me, �e m�me objekt pohybuj�c� se p�t jednotek za deset sekund. Rychl� po��ta� posune objektem o p�l jednotky za sekundu. Na pomal�m syst�mu m��e trvat 2 sekundy, ne� se funkce znovu zavol�. T�m vznikaj� r�zn� zpo�d�n� a trh�n�, zkr�tka animace u� nen� plynul�. Lep�� �e�en� v�ak neexistuje. Pomal� po��ta� nezrychl�te, leda koupit nov�...</p>

<p>Ale zp�tky ke k�du. Prvn� podm�nka zji��uje stisk kl�vesy ESC, kter� ukon�uje aplikaci.</p>

<p class="src0">void Update(DWORD milliseconds)<span class="kom">// Aktualizace pohyb� ve sc�n� a stisk kl�ves</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown[VK_ESCAPE])<span class="kom">// Kl�vesa ESC?</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication(g_window);<span class="kom">// Ukon�en� programu</span></p>
<p class="src1">}</p>

<p>Kl�vesa F1 p�ep�n� m�d okna mezi syst�mem a fullscreenem.</p>

<p class="src1">if (g_keys-&gt;keyDown[VK_F1])<span class="kom">// Kl�vesa F1?</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// P�epnut� fullscreen/okno</span></p>
<p class="src1">}</p>

<p>Stisk mezern�ku po skon�en� hry zalo�� novou. Inicializujeme v�ech t�icet objekt�, nastav�me konec hry na false, sk�re na nulu, prvn� level a zasa�en� i nezasa�en� objekty v tomto levelu tak� na nulu. Nic nepochopiteln�ho.</p>

<p class="src1">if (g_keys-&gt;keyDown[' '] &amp;&amp; game)<span class="kom">// Mezern�k na konci hry?</span></p>
<p class="src1">{</p>
<p class="src2">for (int loop = 0; loop &lt; 30; loop++)<span class="kom">// Proch�z� v�echny objekty</span></p>
<p class="src2">{</p>
<p class="src3">InitObject(loop);<span class="kom">// Jejich inicializace</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">game = FALSE;<span class="kom">// Je�t� nen� konec hry</span></p>
<p class="src"></p>
<p class="src2">score = 0;<span class="kom">// Nulov� sk�re</span></p>
<p class="src2">level = 1;<span class="kom">// Prvn� level</span></p>
<p class="src2">kills = 0;<span class="kom">// Nula zasa�en�ch objekt�</span></p>
<p class="src2">miss = 0;<span class="kom">// Nula nezasa�en�ch objekt�</span></p>
<p class="src1">}</p>

<p>K vytvo�en� iluze pluj�c�ch mrak� a pohybuj�c� se zem�, ode�teme od roll ��slo 0.00005f n�soben� po�tem milisekund od minul�ho renderingu. Princip �asov�n� jsme si vysv�tlili v��e.</p>

<p class="src1">roll -= milliseconds * 0.00005f;<span class="kom">// Mraky pluj� a zem� se pohybuje</span></p>

<p>Zalo��me cyklus, kter� proch�z� v�echny objekty ve sc�n� a aktualizuje je. Jejich po�et je roven aktu�ln�mu levelu.</p>

<p class="src1">for (int loop = 0; loop &lt; level; loop++)<span class="kom">// Aktualizace v�ech viditeln�ch objekt�</span></p>
<p class="src1">{</p>

<p>Pot�ebujeme zjistit, kter�m sm�rem se kter� objekt ot���. Podle sm�ru rotace uprav�me aktu�ln� �hel nato�en� o 0.2 stup�� vyn�soben�ch ��d�c� prom�nnou cyklu se�tenou s milisekundami. P�i��t�n�m loop z�sk�me rozd�lnou rotaci pro ka�d� objekt. Druh� objekt se nyn� ot��� rychleji ne� prvn� a t�et� objekt je�t� rychleji ne� druh�.</p>

<p class="src2">if (object[loop].rot == 1)<span class="kom">// Rotace po sm�ru hodinov�ch ru�i�ek?</span></p>
<p class="src3">object[loop].spin -= 0.2f * (float(loop + milliseconds));</p>
<p class="src"></p>
<p class="src2">if (object[loop].rot == 2)<span class="kom">// Rotace proti sm�ru hodinov�ch ru�i�ek?</span></p>
<p class="src3">object[loop].spin += 0.2f * (float(loop + milliseconds));</p>

<p>P�esuneme se ke k�du zaji��uj�c�mu pohyby. Pokud se objekt pohybuje doprava (dir == 1), p�i�teme k x pozici 0.0012f. Podobn�m zp�sobem o�et��me posun doleva (dir == 0). P�i sm�ru nahoru (dir == 2) zv�t��me y hodnotu, proto�e kladn� ��st osy y le�� naho�e. Sm�r dol� (dir == 3) je �pln� stejn� jako p�edchoz�. Ode��t�me v�ak men�� ��slo, aby byl p�d pomalej��.</p>

<p class="src2">if (object[loop].dir == 1)<span class="kom">// Pohyb doprava?</span></p>
<p class="src3">object[loop].x += 0.012f * float(milliseconds);</p>
<p class="src"></p>
<p class="src2">if (object[loop].dir == 0)<span class="kom">// Pohyb doleva?</span></p>
<p class="src3">object[loop].x -= 0.012f * float(milliseconds);</p>
<p class="src"></p>
<p class="src2">if (object[loop].dir == 2)<span class="kom">// Pohyb nahoru?</span></p>
<p class="src3">object[loop].y += 0.012f * float(milliseconds);</p>
<p class="src"></p>
<p class="src2">if (object[loop].dir == 3)<span class="kom">// Pohyb dol�?</span></p>
<p class="src3">object[loop].y -= 0.0025f * float(milliseconds);</p>

<p>Posunuli jsme objektem a nyn� pot�ebujeme otestovat, jestli je na sc�n� je�t� vid�t. M��eme to zjistit podle hloubky ve sc�n� m�nus 15.0f (mal� tolerance nav�c) a d�len�m dv�ma. Pro ty z v�s, kte�� od inicializace objekt� u� zapomn�li... Pokud jste dvacet jednotek ve sc�n�, m�te z ka�d� strany zhruba deset jednotek viditeln� sc�ny (z�le�� na nastaven� perspektivy). Tak�e -20.0f (hloubka) -15.0f (extra okraj) = -35.0f. Vyd�l�me 2.0f a z�sk�me -17.5f, co� je p�ibli�n� 7.5 jednotek vlevo od viditeln� sc�ny. Objekt tedy u� ur�it� nen� vid�t.</p>

<p>Mus� tak� platit podm�nka, �e se objekt pohybuje doleva (dir == 0). Pokud ne, nestar�me se o n�j. Posledn� ��st logick�ho v�razu p�edstavuje test z�sahu. Shrneme to: pokud objekt vylet�l vlevo ze sc�ny, pohybuje se doleva a nebyl zasa�en, u�ivatel ho u� nem� �anci zas�hnout. Zv���me po�et nezasa�en�ch objekt� a ozna��me objekt jako zasa�en�, aby se o n�j program u� p���t� nestaral. Touto cestou (hit = true) tak� zajist�me autodestrukci, co� n�m po n�jak� dob� umo�n� jeho automatickou reinicializaci - nov� textura, sm�r pohybu, rotace ap.</p>

<p class="src2"><span class="kom">// Objekt vylet�l vlevo ze sc�ny, pohybuje se vlevo a je�t� nebyl zasa�en</span></p>
<p class="src2">if ((object[loop].x &lt; (object[loop].distance - 15.0f) / 2.0f) &amp;&amp; (object[loop].dir == 0) &amp;&amp; !object[loop].hit)</p>
<p class="src2">{</p>
<p class="src3">miss += 1;<span class="kom">// Zv��en� po�tu nezasa�en�ch objekt�</span></p>
<p class="src3">object[loop].hit = TRUE;<span class="kom">// Odstran�n� objektu (zaji��uje animaci exploze a reinicializaci)</span></p>
<p class="src2">}</p>

<p>Analogicky o�et��me opu�t�n� sc�ny vpravo a n�raz do zem�.</p>

<p class="src2"><span class="kom">// Objekt vylet�l vpravo ze sc�ny, pohybuje se vpravo a je�t� nebyl zasa�en</span></p>
<p class="src2">if ((object[loop].x &gt; -(object[loop].distance - 15.0f) / 2.0f) &amp;&amp; (object[loop].dir == 1) &amp;&amp; !object[loop].hit)</p>
<p class="src2">{</p>
<p class="src3">miss += 1;<span class="kom">// Zv��en� po�tu nezasa�en�ch objekt�</span></p>
<p class="src3">object[loop].hit = TRUE;<span class="kom">// Odstran�n� objektu (zaji��uje animaci exploze a reinicializaci)</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Objekt narazil do zem�, pohybuje se dol� a je�t� nebyl zasa�en</span></p>
<p class="src2">if ((object[loop].y &lt; -2.0f) &amp;&amp; (object[loop].dir == 3) &amp;&amp; !object[loop].hit)</p>
<p class="src2">{</p>
<p class="src3">miss += 1;<span class="kom">// Zv��en� po�tu nezasa�en�ch objekt�</span></p>
<p class="src3">object[loop].hit = TRUE;<span class="kom">// Odstran�n� objektu (zaji��uje animaci exploze a reinicializaci)</span></p>
<p class="src2">}</p>

<p>Narozd�l od p�edchoz�ch test� p�i letu vzh�ru ud�l�me men�� zm�nu. Pokud se objekt dostane na ose y v��e ne� 4.5f jednotek (t�sn� pod mraky), nezni��me ho, ale pouze zm�n�me jeho sm�r, aby se pohyboval dol�. Destrukci zajist� p�edchoz� k�d pro nara�en� do zem�.</p>

<p class="src2">if ((object[loop].y &gt; 4.5f) &amp;&amp; (object[loop].dir == 2))<span class="kom">// Objekt je pod mraky a sm��uje vzh�ru</span></p>
<p class="src3">object[loop].dir = 3;<span class="kom">// Zm�na sm�ru na p�d</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Do mapy zpr�v ve funkci WindowProc() p�id�me dv� v�tve, kter� obsluhuj� ud�losti my�i. P�i stisknut� lev�ho tla��tka ulo��me pozici kliknut� v okn� a ve funkci Selection() zjist�me, jestli se hr�� strefil do n�kter�ho z objekt� nebo ne. Proto�e vykreslujeme vlastn� OpenGL kurzor, pot�ebujeme p�i renderingu zn�t jeho pozici. O to se star� WM_MOUSEMOVE.</p>

<p class="src0"><span class="kom">// Funkce WindowProc</span></p>
<p class="src1">case WM_LBUTTONDOWN:<span class="kom">// Stisknut� lev�ho tla��tka my�i</span></p>
<p class="src2">mouse_x = LOWORD(lParam);</p>
<p class="src2">mouse_y = HIWORD(lParam);</p>
<p class="src2">Selection();</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case WM_MOUSEMOVE:<span class="kom">// Pohyb my�i</span></p>
<p class="src2">mouse_x = LOWORD(lParam);</p>
<p class="src2">mouse_y = HIWORD(lParam);</p>
<p class="src2">break;</p>

<p>P�istoup�me k vykreslen� objektu. Funkci se p�ed�vaj� celkem t�i parametry, kter� ho dostate�n� popisuj� - ���ka, v��ka a textura. Obd�ln�k renderujeme zad�v�n�m bod� proti sm�ru hodinov�ch ru�i�ek, abychom mohli pou��t culling.</p>

<p class="src0">void Object(float width, float height, GLuint texid)<span class="kom">// Vykresl� objekt</span></p>
<p class="src0">{</p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, textures[texid].texID);<span class="kom">// Zvol� spr�vnou texturu</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�k�</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-width,-height, 0.0f);<span class="kom">// Lev� doln�</span></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( width,-height, 0.0f);<span class="kom">// Prav� doln�</span></p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( width, height, 0.0f);<span class="kom">// Prav� horn�</span></p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-width, height, 0.0f);<span class="kom">// Lev� horn�</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src0">}</p>

<p>K�d pro renderov�n� exploze dost�v� pouze jeden parametr - identifik�tor objektu. Pot�ebujeme nagrabovat sou�adnice oblasti na textu�e exploze. Ud�l�me to podobnou cestou, jako kdy� jsme z�sk�vali jednotliv� znaky z textury fontu. Ex a ey p�edstavuj� sloupec a ��dek z�visl� na po�ad� sn�mku animace (framu).</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_32_exploze.jpg" width="256" height="256" alt="Textura sn�mk� exploze" /></div>

<p>Pozici na ose x z�sk�me d�len�m aktu�ln�ho sn�mku �ty�mi. Proto�e m�me 64 sn�mk� a pouze 16 obr�zk�, pot�ebujeme animaci zpomalit. Zbytek po d�len� uprav� ��slo na hodnoty 0 a� 3 a aby texturov� koordin�ty byly v rozmez� 0.0f a 1.0f, d�l�me �ty�mi. Z�skali jsme sloupec, nyn� je�t� ��dek. Prvn� d�len� op�t zmen�uje ��slo, druh� d�len� eliminuje cel� ��dek a posledn�m d�len�m z�sk�me vertik�ln� sou�adnici na textu�e.</p>

<p>Pokud je aktu�ln� sn�mek 16, ey = 16/4/4/4 = 4/4/4 = 0,25. Jeden ��dek dol�. je-li sn�mek 60, ey = 60/4/4/4 = 15/4/4 = 3/4 = 0,75. Matematici nev��� vlastn�m o��m... D�vod pro� se 15/4 nerovn� 3,75 je to, �e do posledn�ho d�len� pracujeme s cel�mi ��sly. Po��t�me-li se zaokrouhlov�n�m dojdeme k z�v�ru, �e v�sledkem jsou v�dy ��sla 0.0f, 0.25f, 0.50f nebo 0.75f. Douf�m, �e to d�v� smysl. Je to jednoduch�, ale matematika zastra�uje.</p>

<p class="src0">void Explosion(int num)<span class="kom">// Animace exploze objektu</span></p>
<p class="src0">{</p>
<p class="src1">float ex = (float)((object[num].frame/4)%4)/4.0f;<span class="kom">// V�po�et x sn�mku exploze (0.0f - 0.75f)</span></p>
<p class="src1">float ey = (float)((object[num].frame/4)/4)/4.0f;<span class="kom">// V�po�et y sn�mku exploze (0.0f - 0.75f)</span></p>

<p>Z�skali jsme texturovac� koordin�ty, zb�v� vykreslit obd�ln�k. Vertexy jsou fixov�ny na -1.0f a 1.0f. U textur ode��t�me ey od 1.0f. Pokud bychom to neud�lali animace by prob�hala v opa�n�m po�ad�. Po��tek texturovac�ch sou�adnic je vlevo dole.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, textures[5].texID);<span class="kom">// Textura exploze</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�k�</span></p>
<p class="src2">glTexCoord2f(ex, 1.0f - (ey));</p>
<p class="src2">glVertex3f(-1.0f, -1.0f, 0.0f);<span class="kom">// Lev� doln�</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(ex + 0.25f, 1.0f - (ey));</p>
<p class="src2">glVertex3f( 1.0f, -1.0f, 0.0f);<span class="kom">// Prav� doln�</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(ex + 0.25f, 1.0f - (ey + 0.25f));</p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Prav� horn�</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(ex, 1.0f - (ey + 0.25f));</p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Lev� horn�</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Jak je vysv�tleno v��e, sn�mek nesm� b�t vy��� ne� 63, jinak by animace za�ala nanovo. P�i p�es�hnut� tohoto ��sla reinicializujeme objekt.</p>

<p class="src1">object[num].frame += 1;<span class="kom">// Zv��� sn�mek exploze</span></p>
<p class="src"></p>
<p class="src1">if (object[num].frame &gt; 63)<span class="kom">// Posledn� sn�mek?</span></p>
<p class="src1">{</p>
<p class="src2">InitObject(num);<span class="kom">// Reinicializace objektu</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>N�sleduj�c� sekce k�du vykresluje objekty. Za�neme resetov�n�m matice a p�esunem o deset jednotek do hloubky.</p>

<p class="src0">void DrawTargets(void)<span class="kom">// Vykresl� objekty</span></p>
<p class="src0">{</p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -10.0f);<span class="kom">// Posun do hloubky</span></p>

<p>Zalo��me cyklus proch�zej�c� v�echny aktivn� objekty. Funkc� glLoadName() skryt� ozna��me individu�ln� objekty - ka�d�mu se ur�� jm�no (��slo), kter� odpov�d� indexu v poli. Prvn�mu se p�i�ad� nula, druh�mu jedni�ka atd. Podle tohoto jm�na m��eme zjistit, kter� objekt byl zasa�en. Pokud program nen� v selection m�du glLoadName() je ignorov�no. Po p�i�azen� jm�na ulo��me matici.</p>

<p class="src1">for (int loop = 0; loop &lt; level; loop++)<span class="kom">// Proch�z� aktivn� objekty</span></p>
<p class="src1">{</p>
<p class="src2">glLoadName(loop);<span class="kom">// P�i�ad� objektu jm�no (pro detekci z�sah�)</span></p>
<p class="src2">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>

<p>P�esuneme se na pozici objektu, kde m� b�t vykreslen.</p>

<p class="src2">glTranslatef(object[loop].x, object[loop].y, object[loop].distance);<span class="kom">// Um�st�n� objektu</span></p>

<p>P�ed renderingem testujeme, jestli byl zasa�en nebo ne. Pokud podm�nka plat�, vykresl�me m�sto objektu sn�mek animace exploze, jinak oto��me objektem na ose z o jeho �hel spin a a� potom ho vykresl�me. Pro ur�en� rozm�r� pou�ijeme pole size, kter� jsme vytvo�ili na za��tku programu. Texid reprezentuje typ objektu (texturu).</p>

<p class="src2">if (object[loop].hit)<span class="kom">// Byl objekt zasa�en?</span></p>
<p class="src2">{</p>
<p class="src3">Explosion(loop);<span class="kom">// Vykresl� sn�mek exploze</span></p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Objekt nebyl zasa�en</span></p>
<p class="src2">{</p>
<p class="src3">glRotatef(object[loop].spin,0.0f,0.0f,1.0f);<span class="kom">// Nato�en� na ose z</span></p>
<p class="src3">Object(size[object[loop].texid].w, size[object[loop].texid].h, object[loop].texid);<span class="kom">// Vykreslen�</span></p>
<p class="src2">}</p>

<p>Po renderingu popneme matici, abychom zru�ili posun a nato�en�.</p>

<p class="src2">glPopMatrix();<span class="kom">// Obnov� matici</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Draw() je hlavn� vykreslovac� funkc�. Jako obvykle sma�eme buffery a resetujeme matici, kterou n�sledn� pushneme.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslen� sc�ny</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�� matici</span></p>

<p>Zvol�me texturu (v po�ad� sedm�) a pokus�me se vykreslit oblohu. Je slo�ena ze �ty� otexturovan�ch obd�ln�k�. Prvn� p�edstavuje oblohu od zem� p��mo vzh�ru. Textura na n�m roluje docela pomalu. Druh� obd�ln�k je vykreslen na stejn�m m�st�, ale jeho textura roluje rychleji. Ob� textury se blendingem spoj� dohromady a vytvo�� tak hezk� v�cevrstv� efekt.</p>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�k�</span></p>
<p class="src2">glTexCoord2f(1.0f,roll/1.5f+1.0f); glVertex3f( 28.0f,+7.0f,-50.0f);<span class="kom">// Prav� horn�</span></p>
<p class="src2">glTexCoord2f(0.0f,roll/1.5f+1.0f); glVertex3f(-28.0f,+7.0f,-50.0f);<span class="kom">// Lev� horn�</span></p>
<p class="src2">glTexCoord2f(0.0f,roll/1.5f+0.0f); glVertex3f(-28.0f,-3.0f,-50.0f);<span class="kom">// Lev� doln�</span></p>
<p class="src2">glTexCoord2f(1.0f,roll/1.5f+0.0f); glVertex3f( 28.0f,-3.0f,-50.0f);<span class="kom">// Prav� doln�</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(1.5f,roll+1.0f); glVertex3f( 28.0f,+7.0f,-50.0f);<span class="kom">// Prav� horn�</span></p>
<p class="src2">glTexCoord2f(0.5f,roll+1.0f); glVertex3f(-28.0f,+7.0f,-50.0f);<span class="kom">// Lev� horn�</span></p>
<p class="src2">glTexCoord2f(0.5f,roll+0.0f); glVertex3f(-28.0f,-3.0f,-50.0f);<span class="kom">// Lev� doln�</span></p>
<p class="src2">glTexCoord2f(1.5f,roll+0.0f); glVertex3f( 28.0f,-3.0f,-50.0f);<span class="kom">// Prav� doln�</span></p>

<p>Abychom p�idali iluzi, �e mraky pluj� sm�rem k pozorovateli, t�et� obd�ln�k sm��uje z hloubky dop�edu. Dal�� obd�ln�k je op�t na stejn� m�st�, ale textura roluje rychleji. V�sledkem �ty� oby�ejn�ch obd�ln�k� je obloha, kter� se jev�, jako by stoupala od zem� vzh�ru a p�ibli�ovala se k pozorovateli. Mohl jsem pou��t otexturovanou polokouli, ale byl jsem p��li� l�n�. Efekt s obd�ln�ky vypad� celkem slu�n�.</p>

<p class="src2">glTexCoord2f(1.0f,roll/1.5f+1.0f); glVertex3f( 28.0f,+7.0f,0.0f);<span class="kom">// Prav� horn�</span></p>
<p class="src2">glTexCoord2f(0.0f,roll/1.5f+1.0f); glVertex3f(-28.0f,+7.0f,0.0f);<span class="kom">// Lev� horn�</span></p>
<p class="src2">glTexCoord2f(0.0f,roll/1.5f+0.0f); glVertex3f(-28.0f,+7.0f,-50.0f);<span class="kom">// Lev� doln�</span></p>
<p class="src2">glTexCoord2f(1.0f,roll/1.5f+0.0f); glVertex3f( 28.0f,+7.0f,-50.0f);<span class="kom">// Bottom Right</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(1.5f,roll+1.0f); glVertex3f( 28.0f,+7.0f,0.0f);<span class="kom">// Prav� horn�</span></p>
<p class="src2">glTexCoord2f(0.5f,roll+1.0f); glVertex3f(-28.0f,+7.0f,0.0f);<span class="kom">// Lev� horn�</span></p>
<p class="src2">glTexCoord2f(0.5f,roll+0.0f); glVertex3f(-28.0f,+7.0f,-50.0f);<span class="kom">// Lev� doln�</span></p>
<p class="src2">glTexCoord2f(1.5f,roll+0.0f); glVertex3f( 28.0f,+7.0f,-50.0f);<span class="kom">// Prav� doln�</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Nyn� vykresl�me zemi. Za��n� tam, kde se nach�z� nejni��� bod oblohy a sm��uje sm�rem k pozorovateli. Roluje stejn� rychle jako mraky. Abychom p�idali trochu v�ce detail� a zamezili tak nep��jemn�mu kosti�kov�n� p�i velk�m zv�t�en�, namapujeme texturu sedmkr�t na ose x a �ty�ikr�t na ose y.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, textures[6].texID);<span class="kom">// Textura zem�</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�k�</span></p>
<p class="src2">glTexCoord2f(7.0f,4.0f-roll); glVertex3f( 27.0f,-3.0f,-50.0f);<span class="kom">// Prav� horn�</span></p>
<p class="src2">glTexCoord2f(0.0f,4.0f-roll); glVertex3f(-27.0f,-3.0f,-50.0f);<span class="kom">// Lev� horn�</span></p>
<p class="src2">glTexCoord2f(0.0f,0.0f-roll); glVertex3f(-27.0f,-3.0f,0.0f);<span class="kom">// Lev� doln�</span></p>
<p class="src2">glTexCoord2f(7.0f,0.0f-roll); glVertex3f( 27.0f,-3.0f,0.0f);<span class="kom">// Prav� doln�</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Pozad� je vykresleno, p�istoup�me k sest�elovan�m objekt�m. Napsali jsme pro n� speci�ln� funkci. Potom obnov�me matici.</p>

<p class="src1">DrawTargets();<span class="kom">// Sest�elovan� objekty</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� matice</span></p>

<p>Vykresl�me kurzor my�i. Nagrabovan� rozm�ry okna ulo��me do struktury obd�ln�ku window. Zvol�me projek�n� matici a pushneme ji, resetujeme ji a p�evedeme sc�nu z perspektivn�ho m�du do pravo�hl� projekce. Sou�adnice 0, 0 se nach�zej� vlevo dole.</p>

<p>Ve funkci glOrtho() prohod�me t�et� a �tvrt� parametr, aby byl kurzor renderov�n proti sm�ru hodinov�ch ru�i�ek a culling pracoval tak, jak chceme. Kdyby byl po��tek sou�adnic naho�e, zad�v�n� bod� by prob�halo v opa�n�m sm�ru a kurzor s textem by se nezobrazil.</p>

<p class="src1">RECT window;<span class="kom">// Prom�nn� obd�ln�ku</span></p>
<p class="src1">GetClientRect (g_window-&gt;hWnd,&amp;window);<span class="kom">// Grabov�n� rozm�r� okna</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Projek�n� matice</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�� projek�n� matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset projek�n� matice</span></p>
<p class="src"></p>
<p class="src1">glOrtho(0, window.right, 0, window.bottom, -1, 1);<span class="kom">// Nastaven� pravo�hl� sc�ny</span></p>

<p>Po nastaven� kolm� projekce zvol�me modelview matici a um�st�me kurzor. Probl�m je v tom, �e po��tek sc�ny (0, 0) je vlevo dole, ale okno (syst�m) ho m� vlevo naho�e. Kdybychom pozici kurzoru neinvertovali, tak by se p�i posunut� dol�, pohyboval nahoru. Od spodn�ho okraje okna ode�teme mouse_y. Nam�sto p�ed�v�n� velikosti v OpenGL jednotk�ch, specifikujeme ���ku a v��ku v pixelech.</p>

<p>Rozhodl jsem se pou��t vlastn� a ne syst�mov� kurzor ze dvou d�vod�. Prvn� a v�ce d�le�it� je, �e vypad� l�pe a m��e b�t modifikov�n v jak�mkoli grafick�m editoru, kter� podporuje alfa kan�l. Druh�m d�vodem je, �e n�kter� grafick� karty kurzor ve fullscreenu nezobrazuj�. Hr�t hru podobn�ho typu bez kurzoru nen� v�bec snadn� :-).</p>

<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvol� matici modelview</span></p>
<p class="src1">glTranslated(mouse_x, window.bottom-mouse_y, 0.0f);<span class="kom">// Posun na pozici kurzoru</span></p>
<p class="src"></p>
<p class="src1">Object(16, 16, 8);<span class="kom">// Vykresl� kurzor my�i</span></p>

<p>Vyp��eme logo NeHe Productions zarovnan� na st�ed horn� ��sti okna, d�le zobraz�me aktu�ln� level a sk�re.</p>

<p class="src1">glPrint(240, 450, &quot;NeHe Productions&quot;);<span class="kom">// Logo</span></p>
<p class="src1">glPrint(10, 10, &quot;Level: %i&quot;, level);<span class="kom">// Level</span></p>
<p class="src1">glPrint(250, 10, &quot;Score: %i&quot;, score);<span class="kom">// Sk�re</span></p>

<p>Otestujeme, jestli hr�� nestrefil v�ce ne� dev�t objekt�. Pokud ano, nastav�me game na true, ��m� indikujeme konec hry.</p>

<p class="src1">if (miss &gt; 9)<span class="kom">// Nestrefil hr�� v�ce ne� dev�t objekt�?</span></p>
<p class="src1">{</p>
<p class="src2">miss = 9;<span class="kom">// Limit je dev�t</span></p>
<p class="src2">game = TRUE;<span class="kom">// Konec hry</span></p>
<p class="src1">}</p>

<p>Po skon�en� hry vypisujeme text GAME OVER. Je-li hr�� je�t� ve h�e, vyp��eme kolik objekt� mu m��e je�t� uniknout. Text je ve form�tu nap�. '6/10' - m��e je�t� nezas�hnout �est objekt� z deseti.</p>

<p class="src1">if (game)<span class="kom">// Konec hry?</span></p>
<p class="src1">{</p>
<p class="src2">glPrint(490, 10, &quot;GAME OVER&quot;);<span class="kom">// Vyp��e konec hry</span></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">glPrint(490, 10, &quot;Morale: %i/10&quot;, 10-miss);<span class="kom">// Vyp��e po�et objekt�, kter� nemus� sest�elit</span></p>
<p class="src1">}</p>

<p>Zb�v� obnovit p�vodn� nastaven�. Zvol�me projek�n� matici, obnov�me ji, zvol�me modelview matici a vypr�zdn�me buffer, abychom se ujistili, �e v�echny objekty byly v po��dku zobrazeny.</p>

<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Projek�n� matice</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� projek�n� matice</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Modelview matice</span></p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vypr�zdn� OpenGL renderovac� pipeline</span></p>
<p class="src0">}</p>

<p>Tento tutori�l je v�sledkem mnoha probd�l�ch noc� p�i k�dov�n� a psan� HTML. Nyn� byste m�li rozum�t pickingu, alfa testingu a �azen� podle hloubky p�i alfa blendingu. Picking umo��uje vytvo�it interaktivn� software, kter� se ovl�d� my��. V�echno od her a� po n�dhern� GUI. Nejv�t�� v�hodou pickingu je, �e si nemus�me v�st slo�it� z�znam, kde se objekty nach�zej�, o translac�ch a rotac�ch ani nemluv�. Objektu sta�� p�i�adit jm�no a po�kat na v�sledek. S alfa blendingem a testingem m��ete vykreslit objekt kompletn� nepr�hledn� a/nebo pln� otvor�. V�sledek je ��asn�, nemus�te se starat o prosv�t�n� textur.</p>

<p>Mohl jsem str�vit spoustu �asu p�id�v�n�m pohyb� podle fyzik�ln�ch z�kon�, grafiky, zvuk� a podobn�. Nicm�n� jsem vysv�tlil OpenGL techniky bez dal��ch zbyte�nost�. Douf�m, �e se po �ase objev� n�jak� skv�l� modifikace k�du, kter� u� ale nech�m na v�s.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson32.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson32_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson32.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson32.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Hugh@mikespike.freeserve.co.uk">Hugh Waite</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson32.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson32.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson32.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:edgarcostanzo@tiscalinet.it">Edgar Costanzo</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson32.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson32.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(32);?>
<?FceNeHeOkolniLekce(32);?>

<?
include 'p_end.php';
?>
