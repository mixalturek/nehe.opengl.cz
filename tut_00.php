<?
$g_title = 'CZ NeHe OpenGL - Lekce 0 - P�edmluva k NeHe Tutori�l�m';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Lekce 0 - P�edmluva k NeHe Tutori�l�m</h1>

<p class="nadpis_clanku">Je�t� ne� se pust�te do �ten� tohoto textu, m�li byste v�d�t, �e nen� sou��st� ofici�ln�ch (anglick�ch) NeHe Tutori�l�. Napsal ho &quot;pouze&quot; jeden z p�ekladatel�, kter�mu chyb�lo n�co na zp�sob trochu ucelen�j��ho �vodu do tak obrovsk� problematiky, jakou p�edstavuje programov�n� 3D grafiky, her a ostatn� v�eho okolo OpenGL.</p>

<p>Kdy� jsem za��nal s NeHe, myslel jsem si, �e um�m programovat. Je mo�n�, �e u v�s je to pravda, u m� ale nebyla. Napsal jsem ji� sice n�kolik aplikac�, ale v�t�inou se skl�daly z bezduch� line�rn� posloupnosti p��kaz� s ob�asn�mi odskoky do if blok� testuj�c� n�vratov� hodnoty, p�r cykly a podobn�. Opravdu ucelenou mno�inu funkc� pln�ch mnoha vno�en�ch cykl� a podm�nek, kdy ka�d� pln� sv�j p�esn� dan� ��el a dohromady tak tvo�� jednotn� celek, jsem nikdy nenapsal. Toto m� nau�ily a� NeHe Tutori�ly.</p>

<p>Mo�n� v�m p�i �ten� des�t�, dvac�t� lekce bude p�ipadat, �e a� se dostanete na konec, budete um�t (nejen) o OpenGL naprosto v�e. Pokud mi sl�b�te, �e to nikomu ne�eknete, prozrad�m v�m jedno mal�, ale d�le�it� tajemstv�... v NeHe Tutori�lech rozhodn� NEN� v�echno. Zpo��tku to tak sice vypad�, ale a� do�tete posledn� tutori�l a ohl�dnete se zp�t, zjist�te, �e sice um�te vytvo�it OpenGL okno, vykreslit do sc�ny nejr�zn�j�� obrazce, pokr�t sc�nu neprostupnou mlhou, vrhat st�ny, na��tat nejr�zn�j�� form�ty obr�zk�, loadovat �chvatn� 3D modely nebo tvo�it jednoduch� hry, ale v��te nebo ne, je to opravdu jen za��tek. Stejn� jako snad u �ehokoli jin�ho i zde plat�, �e ��m v�ce v�c� u� zn�te, t�m v�ce jich neust�le objevujete p�ed sebou.</p>

<p>Po do�ten� t�ch 350, nebo kolik jich vlastn� je, str�nek zjist�te, �e NeHe byl jen mal� �vod. Na druhou stranu v�m mohu sl�bit, �e pokud p�e�tete a hlavn� pochop�te v�t�inu z prob�ran�ch t�mat, budete m�t vystav�n� ultra pevn� �elezobetonov� z�klady, kter� se nikdy a za ��dn�ch okolnost� nez��t� jako vratk� dome�ek z karet...</p>

<p>Ale dost stra�en�, nebojte, nen� to zase �pln� tak hrozn�, jak jsem pr�v� napsal. Po��ta�ov� grafika m� tu v�hodu, �e jakmile pochop�te principy a vytvo��te si pevn� j�dro poznatk�, v�e, co se nau��te d�le, bude (v�t�inou) jen nabalov�n� dal��ch funkc� a efekt�, kter� sice mohou b�t za ur�it�ch okolnost� naprosto ��asn�, ale pokud je nepou�ijete, tak se zase nic tak hrozn�ho nestane.</p>

<p>Po dosa�en� t�to �rovn� naraz�te na jednu zaj�mavou v�c a to, �e p�i programov�n� tzv. &quot;grafiky&quot; je samotn� grafika jen mal� ��ste�ka z mo�e dal��ch a v�t�inou stejn� nezbytn�ch a mnohem slo�it�j��ch v�c�, kter� ale na prvn� pohled nejsou vid�t. Nezasv�cen�m lidem v�t�inou naprosto unikaj�. Abychom nez�stali jen v teoretick� rovin�, uvedu n�kolik praktick�ch p��klad�.</p>

<p>Vezm�me si t�eba troj�heln�k, na kter� chceme namapovat texturu, co� je samo o sob� velice jednoduch�. Tedy, abychom byli p�esn�, extr�mn� slo�it�, ale jako &quot;blb� program�to�i&quot; ;-) nech�me ve�kerou �pinavou pr�ci na OpenGL a pota�mo na hardwaru grafick� karty. Zkr�tka vysta��me si n�kolika m�lo ��dky k�du. Pokud m�me data obr�zku ve spr�vn�m form�tu, je i vytvo�en� textury hra�ka. Probl�m spo��v� p�edev��m v nahr�n� obr�zku do pam�ti. Existuj� spousty form�t� od t�ch nejjednodu���ch (.RAW), kdy sta�� soubor nahr�t do pam�ti tak jak je, p�es trochu slo�it�j�� (.TGA, .BMP), kter� se je�t� daj� nahr�t vlastn�mi silami, a� po hodn� slo�it� form�ty (.JPG a spol.), kdy ka�d� rozumn� uva�uj�c� �lov�k v�bec nep�em��l� a ihned s�hne po ciz� knihovn�. To sam� plat� i pro nejr�zn�j�� 3D modely z CAD/CAM program�, 3D Studia MAX, Milkshape 3D a dal��ho modelovac�ho softwaru. Orientovat se v na��t�n� extern�ch dat b�v� opravdu hodn� slo�it�, zvlṻ kdy� ani nezn�te p�esn� form�t souboru, proto�e ho dan� firma v�bec neuvolnila.</p>

<p>Obr�zky, modely a v�echny ostatn� form�ty dat v�ak maj� jednu obrovskou v�hodu - pr�ce s nimi je t�m�� v�dy naprosto stejn�. Lze vytvo�it univerz�ln� knihovny, kter� nap�. uspo��daj� data nahran� ze souboru do pevn� dan�ho univerz�ln�ho form�tu, program�tor je pak pouze p�evezme a bez probl�m� pou�ije.</p>

<p>Jsou ale oblasti, kde ��dn� knihovny od ciz�ch lid� nepom��ou. Pokud v�bec existuj�, tak v�t�inou mus�me prov�st spoustu slo�it�ch �prav, aby pasovaly na n� p��pad. Typick�m p��kladem mohou b�t fyzik�ln� simulace. Co to je? V�t�inou se jedn� o pohyby objekt�, kter� maj� vypadat jako z re�ln�ho sv�ta, ale nejen ony.</p>

<p>Nap��klad p�i vytv��en� automobilov�ho simul�toru je velice lehk� p�i zato�en� volantem zm�nit sm�r auta. Trochu t쾹� je u� zajistit, aby p�i zat��en� nejelo v jednom okam�iku ur�it�m sm�rem a v druh�m kolmo na p�vodn� sm�r a nav�c stejnou rychlost� - hodn� nere�ln�. Automobilov�mu fanou�kovi by d�le mohlo b�t divn�, �e se auto p�i najet� ve stovce do ostr� zat��ky nep�evrhne a ani nedostane do smyku. M�m pokra�ovat nebo sta��? Mysl�m, �e velice brzy pochop�te sami. Dal�� podobn� &quot;��lenosti&quot; uvedu pouze v rychlosti, k v�t�in� z nich byste m�li v NeHe nal�zt alespo� �vod. Jedn� se hlavn� o nejr�zn�j�� grafick� efekty, detekce koliz�, kostern� animace model�, nejr�zn�j�� optimalizace rychlosti programu, um�l� inteligence po��ta�ov�ch protihr��� atd. atd. atd... ka�d� t�ma samo o sob� na tis�c str�nek.</p>

<p>Ale pro� to sem v�echno p�i? Abyste se za�ali co nejd��ve a co nejv�ce v�novat jednomu hodn� d�le�it�mu p�edm�tu: matematice. Pokud jste je�t� ve �kole, neberte ji jako n�co, co se mus�te nau�it, abyste nedostali �patnou zn�mku nebo nepropadli. Berte ji jako n�co, co se v�m bude jednou ur�it� hodit a bez �eho se prost� neobejdete. Jestli v�m nejde a proto ji nen�vid�te, zkuste sv�j p��stup zm�nit, bez n� budete m�t mnohem men�� �ance, pokud n�jak�.</p>

<p>Je pravda, �e ��asn�ch a nep�edstaviteln�ch efekt� dos�hnete i oby�ejn�m s��t�n�m a od��t�n�m (hlavn� v za��tc�ch), ale za rok, za dva se minim�ln� bez analytick� geometrie (body, vektory, roviny...) naprosto neobejdete, v 3D grafice v�s bude prov�zet na ka�d�m kroku. Pokud se chyst�te v�novat grafice p�edev��m kv�li hr�m - je zaj�mav�, �e v�ichni za��naj� s my�lenkou her - U�TE SE MATEMATIKU, jinak nem�te nejmen�� �anci.</p>

<p>Kdy� u� jsme u t� �koly, vrhn�te se i na angli�tinu. O programov�n� existuje spousta literatury i v �e�tin�, ale devades�t dev�t procent se v�nuje naprost�m z�klad�m. Zb�vaj�c� jedno procento v t� z�plav� st�le stejn�ch informac� velice jist� p�ehl�dnete. Z�rali byste, kolik knih a internetov�ch server� se v�nuje pokro�il�m programovac�m technik�m, grafick�m efekt�m, programov�n� her a v�bec v�em p�evratn�m novink�m v oblasti po��ta��, bohu�el v�echno anglicky.</p>

<p>Asi si mysl�te, �e m�te �esk� literatury v�c, ne� stihnete kdy p�e��st. Jestli v�s programov�n� chytne jako m�, a v��te, �e chytne :-), d�v�m v�m p�l roku, rok, v�c ur�it� ne. Abych pravdu �ekl, j� jsem za��nal s OpenGL t�m�� rovnou na anglick�ch textech. V t� dob� bylo do �e�tiny p�elo�eno pouze osm NeHe Tutori�l� - pro za��te�n�ka absolutn� minimum, v knihkupectv�ch nic (p�etrv�v� do dne�ka) a i na �esk�m internetu se nalezlo text� pom�lu a to jsem pro�mejdil ka�d� kout. Dnes se d� alespo� po internetu p�r v�c� sehnat i v �e�tin�, ale v mnoha oblastech angli�tina st�le dominuje, trouf�m si ��ct, �e bude i nad�le.</p>

<p>Co v�c napsat? Asi jen pop��t hodn� �t�st�, za��tky b�vaj� opravdu hodn� slo�it�. Je�t� jedna v�c, pokud nebudete �sp�n� u prvn�ho tutori�lu, zkuste ho p�esko�it a n�kdy pozd�ji se k n�mu vr�tit. U� hodn� lid� hned na za��tku odradil a p�itom se v n�m OpenGL skoro nevyskytuje, t�m�� �ist� Win32 API. Popisuje &quot;pouze&quot; vytvo�en� okna s podporou OpenGL. Zat�m v�m sta�� v�d�t, �e to, co chcete vykreslit, se p�e do funkce DrawGLScene() a ve�ker� inicializace se umis�uje do InitGL(), v�e ostatn� sta�� zkop�rovat ...a modlit se, �e to bude fungovat i nad�le :-)</p>

<p>Tak�e je�t� jednou p�eji hodn� �sp�ch� s OpenGL a p��jemn� chv�le p�i �ten� NeHe Tutori�l�...</p>

<p class="autor">Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?>, 19.09.2004</p>

<?FceNeHeOkolniLekce(0);?>

<?
include 'p_end.php';
?>
