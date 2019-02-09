<?
$g_title = 'CZ NeHe OpenGL - Lekce 30 - Detekce koliz�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(30);?>

<h1>Lekce 30 - Detekce koliz�</h1>

<p class="nadpis_clanku">Na podobn� tutori�l jste u� jist� netrp�liv� �ekali. Nau��te se z�klady o detekc�ch koliz�, jak na n� reagovat a na fyzice zalo�en� modelovac� efekty (n�razy, p�soben� gravitace ap.). Tutori�l se v�ce zam��uje na obecnou funkci koliz� ne� zdrojov�m k�d�m. Nicm�n� d�le�it� ��sti k�du jsou tak� pops�ny. Neo�ek�vejte, �e po prvn�m p�e�ten� �pln� v�emu z koliz� porozum�te. Je to komplexn� n�m�t, se kter�m v�m pomohu za��t.</p>

<p>Zdrojov� k�d, na n�m� tato lekce stav�, poch�z� z m�ho d��v�j��ho p��sp�vku do jedn� sout�e (najdete na OGLchallenge.dhs.org). T�matem byly Bl�zniv� kolize a m�j p��sp�vek (mimochodem, z�skal prvn� m�sto :-) se jmenoval Magick� m�stnost.</p>

<p>Detekce koliz� jsou obt�n�, dodnes nebylo nalezeno ��dn� snadn� �e�en�. Samoz�ejm� existuj� velice obecn� algoritmy, kter� um� pracovat s jak�mkoli druhem objekt�, ale o�ek�vejte od nich tak� pat�i�nou cenu. My budeme zkoumat postupy, kter� jsou velmi rychl�, relativn� snadn� k pochopen� a v r�mci mez� celkem flexibiln�. D�raz mus� b�t vlo�en nejen na detekci kolize, ale i na reakci objekt� na n�raz. Je d�le�it�, aby se v�e d�lo podle fyzik�ln�ch z�kon�. M�me mnoho v�c� na pr�ci! Poj�me se tedy pod�vat, co v�echno se v t�to lekci nau��me:</p>

<h3>Detekce koliz� mezi</h3>
<ul>
<li>Pohybuj�c� se koul� a rovinou</li>
<li>Pohybuj�c� se koul� a v�lcem</li>
<li>Dv�ma pohybuj�c�mi se koulemi</li>
</ul>

<h3>Fyzik�ln� zalo�en� modelov�n�</h3>
<ul>
<li>Reakce na kolize - odrazy</li>
<li>Pohyb v gravitaci za pou�it� Eulerov�ch rovnic</li>
</ul>

<h3>Speci�ln� efekty</h3>
<ul>
<li>Modelov�n� exploz� za pou�it� metody Fin-Tree Billboard</li>
<li>Zvuky pomoc� Windows Multimedia Library (pouze Windows)</li>
</ul>

<h3>Zdrojov� k�d se d�l� na p�t ��st�</h3>

<ul>
<li>Lesson30.cpp - Z�kladn� k�d tutori�lu</li>
<li>Image.cpp, Image.h - Nahr�v�n� bitmap</li>
<li>Tmatrix.cpp, Tmatrix.h - T��da pro pr�ci s maticemi</li>
<li>Tray.cpp, Tray.h - T��da pro pr�ci s polop��mkami</li>
<li>Tvector.cpp, Tvector.h - T��da pro pr�ci s vektory</li>
</ul>

<p>T��dy Vektor, Ray a Matrix jsou velmi u�ite�n�. Daj� se pou��t v jak�mkoli projektu. Doporu�uji je pe�liv� prostudovat, t�eba se v�m budou n�kdy hodit.</p>

<h2>Detekce koliz�</h2>

<h3>Polop��mka</h3>

<p>P�i detekci koliz� vyu�ijeme algoritmus, kter� se v�t�inou pou��v� v trasov�n� polop��mek (ray tracing). Vektorov� reprezentace polop��mky je tvo�ena bodem, kter� ozna�uje za��tek a vektorem (oby�ejn� normalizovan�m) ur�uj�c�m sm�r polop��mky. Rovnice polop��mky:</p>

<p class="src0"><span class="kom">bod_na_polop��mce = za��tek + t * sm�r</span></p>

<p>��slo t nab�v� hodnot od nuly do nekone�na. Dosad�me-li nulu z�sk�me po��te�n� bod. U v�t��ch ��sel dostaneme odpov�daj�c� body na polop��mce. Bod, po��tek i sm�r jsou 3D vektory se slo�kami x, y, a z. Nyn� m��eme pou��t tuto reprezentaci polop��mky k v�po�tu pr�se��ku s rovinou nebo v�lcem.</p>

<h3>Pr�se��k polop��mky a roviny</h3>

<p>Vektorov� reprezentace roviny vypad� takto:</p>

<p class="src0"><span class="kom">Xn dot X = d</span></p>

<p>Xn je norm�la roviny, X je bod na jej�m povrchu a d je ��slo, kter� ur�uje vzd�lenost roviny od po��tku sou�adnicov�ho syst�mu.</p>

<p>P�ekl.: Pod operac� &quot;dot&quot; se skr�v� skal�rn� sou�in dvou vektor� (dot product), kter� se vypo�te sou�tem n�sobk� jednotliv�ch x, y a z slo�ek. Nech�v�m ho v p�vodn�m zn�n�, proto�e i ve zdrojov�ch k�dech budeme volat metodu dot().</p>

<p class="src0"><span class="kom">P�ekl.: P dot Q = Px * Qx + Py * Qy + Pz * Qz</span></p>

<p>Abychom definovali rovinu, pot�ebujeme 3D bod a vektor, kter� je kolm� k rovin�. Pokud vezmeme za 3D bod vektor (0, 0, 0) a pro norm�lu vektor (0, 1, 0), prot�n� rovina osy x a z. Pokud zn�me bod a norm�lu, d� se chyb�j�c� ��slo d snadno dopo��tat.</p>

<p>Pozn.: Vektorov� reprezentace roviny je ekvivalentn� v�ce zn�m� form� Ax + By + Cz + D = 0 (obecn� rovnice roviny). Pro p�epo�et dosa�te za A, B, C slo�ky norm�ly a p�i�a�te D = -d.</p>

<p>Nyn� m�me dv� rovnice</p>

<p class="src0"><span class="kom">bod_na_polop��mce = za��tek + t * sm�r</span></p>
<p class="src0"><span class="kom">Xn dot X = d</span></p>

<p>Pokud polop��mka protne rovinu v n�jak�m bod�, mus� sou�adnice pr�se��k� vyhovovat ob�ma rovnic�m</p>

<p class="src0"><span class="kom">Xn dot bod_na_polop��mce = d</span></p>

<p>Nebo</p>

<p class="src0"><span class="kom">(Xn dot za��tek) + t * (Xn dot sm�r) = d</span></p>

<p>Vyj�d��me t</p>

<p class="src0"><span class="kom">t = (d - Xn dot za��tek) / (Xn dot sm�r)</span></p>

<p>Dosad�me d</p>

<p class="src0"><span class="kom">t = (Xn dot bod_na_polop��mce - Xn dot za��tek) / (Xn dot sm�r)</span></p>

<p>Vytkneme Xn</p>

<p class="src0"><span class="kom">t = (Xn dot (bod_na_polop��mce - za��tek)) / (Xn dot sm�r)</span></p>

<p>��slo t reprezentuje vzd�lenost od za��tku polop��mky k pr�se��ku s rovinou ve sm�ru polop��mky (ne na kolmici). Dosazen�m t do rovnice polop��mky z�sk�me kolizn� bod. Existuje n�kolik speci�ln�ch situac�. Pokud Xn dot sm�r = 0, budou tyto dva vektory navz�jem kolm�. Polop��mka proch�z� rovnob�n� s rovinou a tud� neexistuje kolizn� bod. Pokud je t z�porn�, pr�se��k le�� p�ed po��te�n�m bodem. Polop��mka nesm��uje k rovin�, ale od n�. Op�t ��dn� pr�se��k.</p>

<p class="src0">int TestIntersionPlane(const Plane&amp; plane, const TVector&amp; position, const TVector&amp; direction, double&amp; lamda, TVector&amp; pNormal)</p>
<p class="src0">{</p>
<p class="src1">double DotProduct = direction.dot(plane._Normal);<span class="kom">// Skal�rn� sou�in vektor�</span></p>
<p class="src1">double l2;<span class="kom">// Ur�uje kolizn� bod</span></p>
<p class="src"></p>
<p class="src1">if ((DotProduct &lt; ZERO) &amp;&amp; (DotProduct &gt; -ZERO))<span class="kom">// Je polop��mka rovnob�n� s rovinou?</span></p>
<p class="src2">return 0;<span class="kom">// Bez pr�se��ku</span></p>
<p class="src"></p>
<p class="src1">l2 = (plane._Normal.dot(plane._Position - position)) / DotProduct;<span class="kom">// Dosazen� do vzorce</span></p>
<p class="src"></p>
<p class="src1">if (l2 &lt; -ZERO)<span class="kom">// Sm��uje polop��mka od roviny?</span></p>
<p class="src2">return 0;<span class="kom">// Bez pr�se��ku</span></p>
<p class="src"></p>
<p class="src1">pNormal = plane._Normal;<span class="kom">// Norm�la roviny</span></p>
<p class="src1">lamda = l2;<span class="kom">// Kolizn� bod</span></p>
<p class="src"></p>
<p class="src1">return 1;<span class="kom">// Pr�se��k existuje</span></p>
<p class="src0">}</p>

<h3>Pr�se��k polop��mky a v�lce</h3>

<p>V�po�et pr�se��ku polop��mky s nekone�n� dlouh�m v�lcem je mnohem komplikovan�j�� ne� vysv�tlen� toho, pro� se t�m zde nebudeme zab�vat. Na pozad� je p��li� mnoho matematiky. M�m prim�rn�m z�m�rem je poskytnout a vysv�tlit n�stroje bez zab�h�n� do zbyte�n�ch detail�, kter� by stejn� n�kte�� nepochopili. V�lec je tvo�en polop��mkou, kter� reprezentuje jeho osu, a polom�rem podstavy. Pro detekci kolize se v tomto tutori�lu pou��v� funkce TestIntersetionCylinder(), kter� vrac� jedni�ku, pokud byl nalezen pr�se��k, jinak nulu.</p>

<p class="src0">int TestIntersionCylinder(const Cylinder& cylinder, const TVector& position, const TVector& direction, double& lamda, TVector& pNormal, TVector& newposition)</p>

<p>V parametrech se p�ed�v� struktura v�lce, za��tek a sm�rov� vektor polop��mky. Krom� n�vratov� hodnoty z�sk�me z funkce vzd�lenost od pr�se��ku (na polop��mce), norm�lu vych�zej�c� z pr�se��ku a bod pr�se��ku.</p>

<h3>Kolize mezi dv�ma pohybuj�c�mi se koulemi</h3>

<p>Koule je v geometrii reprezentov�na st�edem a polom�rem. Zji�t�n�, jestli do sebe dv� koule narazily je ban�ln�. Vypo�teme vzd�lenost mezi dv�ma st�edy a porovn�me ji se sou�tem polom�r�. Jak snadn�!</p>

<p>Probl�my nastanou p�i hled�n� kolizn�ho bodu dvou POHYBUJ�C�CH se koul�. Na obr�zku je p��klad, kdy se dv� koule p�esunou z jednoho m�sta do druh�ho za ur�it� �asov� �sek. Jejich dr�hy se prot�naj�, ale to nen� dostate�n� d�vod k tvrzen�, �e do sebe opravdu naraz�. Mohou se nap��klad pohybovat rozd�lnou rychlost�. Jedna t�m�� stoj� a druh� je za okam�ik �pln� n�kde jinde.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_30_1.gif" width="128" height="128" alt="Dv� pohybuj�c� se koule" /></div>

<p>V p�edchoz�ch metod�ch jsme �e�ili rovnice dvou geometrick�ch objekt�. Pokud podobn� rovnice pro dan� objekt neexistuje nebo nem��e b�t pou�ita (pohyb po slo�it� k�ivce), pou��v� se jin� metoda. V na�em p��pad� zn�me po��te�n� i koncov� body p�esunu, �asov� krok, rychlost (+sm�r) a metodu zji�t�n� n�razu statick�ch koul�. Rozkouskujeme �asov� �sek na mal� ��sti. Koule budeme v z�vislosti na nich postupn� posunovat a poka�d� testovat kolizi. Pokud najdeme n�kter� bod, kdy je vzd�lenost koul� men�� ne� sou�et jejich polom�r�, vezmeme minulou pozici a ozna��me ji jako kolizn� bod. M��e se je�t� za��t interpolovat mezi t�mito dv�ma body na rozhran�, kdy kolize je�t� nebyla a u� je, abychom na�li �pln� p�esnou pozici, ale v�t�inou to nen� pot�eba.</p>

<p>��m men�� bude �asov� �sek, t�m budou ��sti vznikl� rozsek�n�m men�� a metoda bude v�ce p�esn� (a v�ce n�ro�n� na hardware po��ta�e). Nap��klad, pokud bude �asov� �sek 1 a ��sti 3, budeme zji��ovat kolizi v �asech 0, 0,33, 0,66 a 1. V n�sleduj�c�m v�pisu k�du hled�me koule, kter� b�hem n�sleduj�c�ho �asov�ho kroku naraz� do kter�koli z ostatn�ch. Funkce vr�t� indexy obou koul�, bod a �as n�razu.</p>

<p class="src0">int FindBallCol(TVector&amp; point, double&amp; TimePoint, double Time2, int&amp; BallNr1, int&amp; BallNr2)</p>
<p class="src0">{</p>
<p class="src1">TVector RelativeV;<span class="kom">// Relativn� rychlost mezi koulemi</span></p>
<p class="src1">TRay rays;<span class="kom">// Polop��mka</span></p>
<p class="src"></p>
<p class="src1">double MyTime = 0.0;<span class="kom">// Hled�n� p�esn� pozice n�razu</span></p>
<p class="src1">double Add = Time2 / 150.0;<span class="kom">// Rozkouskuje �asov� �sek na 150 ��st�</span></p>
<p class="src1">double Timedummy = 10000;<span class="kom">// �as n�razu</span></p>
<p class="src"></p>
<p class="src1">TVector posi;<span class="kom">// Pozice na polop��mce</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Test v�ech koul� proti v�em ostatn�m po 150 kroc�ch</span></p>
<p class="src1">for (int i = 0; i &lt; NrOfBalls - 1; i++)<span class="kom">// V�echny koule</span></p>
<p class="src1">{</p>
<p class="src2">for (int j = i + 1; j &lt; NrOfBalls; j++)<span class="kom">// V�echny zb�vaj�c� koule</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// V�po�et vzd�lenosti</span></p>
<p class="src3">RelativeV = ArrayVel[i] - ArrayVel[j];<span class="kom">// Relativn� rychlost mezi koulemi</span></p>
<p class="src3">rays = TRay(OldPos[i], TVector::unit(RelativeV));<span class="kom">// Polop��mka</span></p>
<p class="src"></p>
<p class="src3">if ((rays.dist(OldPos[j])) &gt; 40)<span class="kom">// Je vzd�lenost v�t�� ne� 2 polom�ry?</span></p>
<p class="src3">{</p>
<p class="src4">continue;<span class="kom">// Dal��</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3"><span class="kom">// N�raz</span></p>
<p class="src"></p>
<p class="src3">MyTime = 0.0;<span class="kom">// Inicializace p�ed vstupem do cyklu</span></p>
<p class="src"></p>
<p class="src3">while (MyTime &lt; Time2)<span class="kom">// P�esn� bod n�razu</span></p>
<p class="src3">{</p>
<p class="src4">MyTime += Add;<span class="kom">// Zv�t�� �as</span></p>
<p class="src4">posi = OldPos[i] + RelativeV * MyTime;<span class="kom">//P�esun na dal�� bod (pohyb na polop��mce)</span></p>
<p class="src"></p>
<p class="src4">if (posi.dist(OldPos[j]) &lt;= 40)<span class="kom">// N�raz</span></p>
<p class="src4">{</p>
<p class="src5">point = posi;<span class="kom">// Bod n�razu</span></p>
<p class="src"></p>
<p class="src5">if (Timedummy &gt; (MyTime - Add))<span class="kom">// Bli��� n�raz, ne� kter� jsme u� na�li (v �ase)?</span></p>
<p class="src5">{</p>
<p class="src6">Timedummy = MyTime - Add;<span class="kom">// P�i�adit �as n�razu</span></p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">BallNr1 = i;<span class="kom">// Ozna�en� koul�, kter� narazily</span></p>
<p class="src5">BallNr2 = j;</p>
<p class="src5">break;<span class="kom">// Ukon�� vnit�n� cyklus</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (Timedummy != 10000)<span class="kom">// Na�li jsme kolizi?</span></p>
<p class="src1">{</p>
<p class="src2">TimePoint = Timedummy;<span class="kom">// �as n�razu</span></p>
<p class="src2">return 1;<span class="kom">// �sp�ch</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return 0;<span class="kom">// Bez kolize</span></p>
<p class="src0">}</p>

<h3>Kolize mezi koul� a rovinou nebo v�lcem</h3>

<p>Nyn� u� um�me zjistit pr�se��k polop��mky a roviny/v�lce. Tyto znalosti pou�ijeme pro hled�n� koliz� mezi koul� a jedn�m z t�chto objekt�. Pot�ebujeme naj�t p�esn� bod n�razu. P�evod znalost� z polop��mky na pohybuj�c� se kouli je relativn� snadn�. Pod�vejte se na lev� obr�zek, mo�n�, �e podstatu pochop�te sami.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_30_2.gif" width="500" height="150" alt="N�raz pohybuj�c� se koule do roviny/v�lce" /></div>

<p>Ka�d� koule sice m� polom�r, ale my ji budeme br�t jako bezrozm�rnou ��sti, kter� m� pouze pozici. K povrchu t�lesa p�i�teme ve sm�ru norm�lov�ho vektoru offset ur�en� polom�rem koule. Neboli k polom�ru v�lce p�i�teme pr�m�r koule (2 polom�ry; z ka�d� strany jeden). Operac� jsme se vr�tili k detekci kolize polop��mka - v�lec. Rovina je je�t� jednodu���. Posuneme ji sm�rem ke kouli o jej� polom�r. Na obr�zku jsou ��rkovan� nakresleny &quot;virtu�ln�&quot; objekty pro testy koliz� a pln� objekty, kter� program vykresl�. Kdybychom k objekt�m p�i testech nep�ipo��t�vali offset, koule by p�ed odrazem z poloviny pronikaly do objekt� (obr�zek vpravo).</p>

<p>M�me-li ur�it m�sto n�razu, je vhodn� nejd��ve zjistit, jestli kolize nastane p�i aktu�ln�m �asov�m �seku. Proto�e polop��mka m� nekone�nou d�lku, je v�dy mo�n�, �e se kolizn� bod nach�z� a� n�kde za novou pozic� koule. Abychom to zjistili, spo��t�me novou pozici a ur��me vzd�lenost mezi po��te�n�m a koncov�m bodem. Pokud je tato vzd�lenost krat�� ne� vzd�lenost, o kterou se objekt posune, tak m�me jistotu, �e kolize nastane v tomto �asov�m �seku. Abychom spo��tali p�esn� �as kolize pou�ijeme n�sleduj�c� jednoduchou rovnici. Dst p�edstavuje vzd�lenost mezi po��te�n�m a koncov�m bodem, Dsc vzd�lenost mezi po��te�n�m a kolizn�m bodem a �asov� krok je definov�n jako T. �e�en�m z�sk�me �as kolize Tc.</p>

<p class="src0"><span class="kom">Tc = Dsc * T / Dst</span></p>

<p>V�po�et se provede samoz�ejm� jenom tehdy, kdy� m� kolize nastat v tomto �asov�m kroku. Vr�cen� �as je zlomkem (��st�) cel�ho �asov�ho kroku. Pokud bude �asov� krok 1 s a my nalezneme kolizn� bod p�esn� uprost�ed vzd�lenosti, �as kolize se bude rovnat 0,5 s. Je interpretov�n jako: V �asov�m okam�iku 0,5 sekund po za��tku p�esunu do sebe objekty naraz�. Kolizn� bod se vypo�te n�soben�m �asu Tc aktu�ln� rychlost� a p�i�ten�m po��te�n�ho bodu.</p>

<p class="src0"><span class="kom">bod_kolize = start + rychlost * Tc</span></p>

<p>Tento kolizn� bod je v�ak na objektu s offsetem (pomocn�m). Abychom nalezli bod n�razu na re�ln�m objektu, p�i�teme k bodu kolize invertovan� norm�lov� vektor z bodu kolize, kter� m� velikost polom�ru koule. Norm�lov� vektor z�sk�me z funkce pro kolize. V�imn�te si, �e funkce pro kolizi s v�lcem vrac� bod n�razu, tak�e nemus� b�t znovu po��t�n.</p>

<h2>Modelov�n� zalo�en� na fyzice</h2>

<h3>Reakce na n�raz</h3>

<p>O�et�en� toho, jak se koule zachov� po n�razu je stejn� d�le�it� jako samotn� nalezen� kolizn�ho bodu. Pou�it� algoritmy a funkce popisuj� p�esn� bod n�razu, norm�lov� vektor vych�zej�c� z objekt� v m�st� n�razu a �asov� �sek, ve kter�m kolize nastala.</p>

<p>P�i odrazech n�m pomohou fyzik�ln� z�kony. Implementujeme pou�ku: &quot;�hel dopadu se rovn� �hlu odrazu&quot;. Oba �hly se vztahuj� k norm�lov�mu vektoru, kter� vych�z� z objektu v kolizn�m bod�. N�sleduj�c� obr�zek ukazuje odraz polop��mky od koule.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_30_3.gif" width="220" height="180" alt="Odraz od koule" /></div>

<p>I je sm�rov� vektor p�ed n�razem, N je norm�lov� vektor v bod� kolize a R je sm�rov� vektor po odrazu, kter� se vypo�te podle n�sleduj�c� rovnice:</p>

<p class="src0"><span class="kom">R = 2 * (-I dot N) * N + I</span></p>

<p>Omezen� spo��v� v tom, �e I i N mus� b�t jednotkov� vektory. U n�s v�ak d�lka vektoru reprezentuje rychlost a sm�r koule, a proto nem��e b�t bez transformace dosazen do rovnice. Pot�ebujeme z n�j vyjmout rychlost. Nalezneme jeho velikost a vyd�l�me j� jednotliv� x, y, z slo�ky. Z�skan� jednotkov� vektor dosad�me do rovnice a vypo�teme R. Jsme skoro u konce. Vektor nyn� m��� ve sm�ru odra�en� polop��mky, ale nem� p�vodn� d�lku. Minule jsme d�lili, tak�e te� budeme n�sobit.</p>

<p>N�sleduj�c� v�pis k�du se pou��v� pro v�po�et odrazu po kolizi koule s rovinou nebo v�lcem. Uveden� algoritmus pracuje i s jin�mi povrchy, nez�le�� na jejich tvaru. Pokud nalezneme bod kolize a norm�lu, je odraz v�dy stejn�.</p>

<p class="src0">rt2 = ArrayVel[BallNr].mag();<span class="kom">// Ulo�� d�lku vektoru</span></p>
<p class="src0">ArrayVel[BallNr].unit();<span class="kom">// Normalizace vektoru</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">// V�po�et odrazu</span></p>
<p class="src0">ArrayVel[BallNr] = TVector::unit((normal * (2 * normal.dot(-ArrayVel[BallNr]))) + ArrayVel[BallNr]);</p>
<p class="src0">ArrayVel[BallNr] = ArrayVel[BallNr] * rt2;<span class="kom">// Nastaven� p�vodn� d�lky</span></p>

<h3>Kdy� se koule sraz� s jinou</h3>

<p>O�et�en� vz�jemn�ho n�razu dvou pohybuj�c�ch se koul� je mnohem obt�n�j��. Mus� b�t vy�e�eny slo�it� rovnice. Nebudeme nic odvozovat, pouze vysv�tl�m v�sledek. Situace p�i kolizi dvou koul� vypad� p�ibli�n� takto:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_30_4.gif" width="256" height="180" alt="Sr�ka dvou koul�" /></div>

<p>Vektory U1 a U2 p�edstavuj� rychlost koul� v �ase n�razu. St�edy dohromady spojuje osa X_Axis, na kter� le�� vektory U1x a U2x, co� jsou vlastn� pr�m�ty rychlosti. U1y a U2y jsou projekce rychlosti na osu, kter� je kolm� k X_Axis. K jejich v�po�tu posta�� jednoduch� skal�rn� sou�in.</p>

<p>Do n�sleduj�c�ch rovnic dosazujeme je�t� ��sla M1 a M2, kter� vyjad�uj� hmotnost koul�. Sna��me se vypo��tat orientaci vektor� rychlosti U1 a U2 po odrazu. Budou je vyjad�ovat nov� vektory V1 a V2. ��sla V1x, V1y, V2x, V2y jsou op�t pr�m�ty.</p>

<p>a) naj�t X_Axis</p>

<p class="src0"><span class="kom">X_Axis = (st�ed2 - st�ed1)</span></p>
<p class="src0"><span class="kom">Jednotkov� vektor, X_Axis.unit();</span></p>

<p>b) naj�t projekce</p>

<p class="src0"><span class="kom">U1x = X_Axis * (X_Axis dot U1)</span></p>
<p class="src0"><span class="kom">U1y = U1 - U1x</span></p>
<p class="src0"><span class="kom">U2x = -X_Axis * (-X_Axis dot U2)</span></p>
<p class="src0"><span class="kom">U2y = U2 - U2x</span></p>

<p>c) naj�t nov� rychlosti</p>

<p class="src0"><span class="kom">V1x = ((U1x * M1) + (U2x * M2) - (U1x - U2x) * M2) / (M1 + M2)</span></p>
<p class="src0"><span class="kom">V2x = ((U1x * M1) + (U2x * M2) - (U2x - U1x) * M1) / (M1 + M2)</span></p>

<p>V na�� aplikaci nastavujeme jednotkovou hmotnost (M1 = M2 = 1), a proto se v�po�et v�sledn�ch vektor� velmi zjednodu��.</p>

<p>d) naj�t kone�n� rychlosti</p>

<p class="src0"><span class="kom">V1y = U1y</span></p>
<p class="src0"><span class="kom">V2y = U2y</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">V1 = V1x + V1y</span></p>
<p class="src0"><span class="kom">V2 = V2x + V2y</span></p>

<p>Odvozen� rovnic st�lo hodn� pr�ce, ale jakmile se nach�zej� v t�to form�, je jejich pou�it� docela snadn�. K�d, kter� vykon�v� sr�ky dvou koul� vypad� takto:</p>

<p class="src0">TVector pb1, pb2, xaxis, U1x, U1y, U2x, U2y, V1x, V1y, V2x, V2y;<span class="kom">// Deklarace prom�nn�ch</span></p>
<p class="src0">double a, b;</p>
<p class="src"></p>
<p class="src0">pb1 = OldPos[BallColNr1] + ArrayVel[BallColNr1] * BallTime;<span class="kom">// Nalezen� pozice koule 1</span></p>
<p class="src0">pb2 = OldPos[BallColNr2] + ArrayVel[BallColNr2] * BallTime;<span class="kom">// Nalezen� pozice koule 2</span></p>
<p class="src"></p>
<p class="src0">xaxis = (pb2 - pb1).unit();<span class="kom">// Nalezen� X_Axis</span></p>
<p class="src"></p>
<p class="src0">a = xaxis.dot(ArrayVel[BallColNr1]);<span class="kom">// Nalezen� projekce</span></p>
<p class="src0">U1x = xaxis * a;<span class="kom">// Nalezen� pr�m�t� vektor�</span></p>
<p class="src0">U1y = ArrayVel[BallColNr1] - U1x;</p>
<p class="src"></p>
<p class="src0">xaxis = (pb1 - pb2).unit();</p>
<p class="src"></p>
<p class="src0">b = xaxis.dot(ArrayVel[BallColNr2]);<span class="kom">// To sam� pro druhou kouli</span></p>
<p class="src0">U2x = xaxis * b;</p>
<p class="src0">U2y = ArrayVel[BallColNr2] - U2x;</p>
<p class="src"></p>
<p class="src0">V1x = (U1x + U2x - (U1x - U2x)) * 0.5;<span class="kom">// Nalezen� nov�ch rychlost�</span></p>
<p class="src0">V2x = (U1x + U2x - (U2x - U1x)) * 0.5;</p>
<p class="src0">V1y = U1y;</p>
<p class="src0">V2y = U2y;</p>
<p class="src"></p>
<p class="src0">for (j = 0; j < NrOfBalls; j++)<span class="kom">// Posun v�ech koul� do �asu n�razu</span></p>
<p class="src0">{</p>
<p class="src1">ArrayPos[j] = OldPos[j] + ArrayVel[j] * BallTime;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">ArrayVel[BallColNr1] = V1x + V1y;<span class="kom">// Nastaven� pr�v� vypo��tan�ch vektor� koul�m, kter� do sebe narazily</span></p>
<p class="src0">ArrayVel[BallColNr2] = V2x + V2y;</p>

<h3>Pohyb v gravitaci za pou�it� Eulerov�ch rovnic</h3>

<p>Pro simulaci realistick�ch pohyb� nejsou n�razy, hled�n� kolizn�ch bod� a odrazy dostate�n�. Mus� b�t p�id�n je�t� pohyb podle fyzik�ln�ch z�kon�. Asi nejpou��van�j�� metodou jsou Eulerovy rovnice. V�echny v�po�ty se vykon�vaj� pro ur�it� �asov� �sek. To znamen�, �e se cel� simulace neposouv� vp�ed plynule, ale po ur�it�ch skoc�ch. P�edstavte si, �e m�te fotoapar�t a ka�dou vte�inu v�slednou sc�nu vyfot�te. B�hem t�to vte�iny se provedou v�echny pohyby, testy koliz� a odrazy. V�sledn� obr�zek se zobraz� na monitoru a z�stane tam a� do dal�� vte�iny. Op�t stejn� v�po�ty a dal�� zobrazen�. Takto pracuj� v�echny po��ta�ov� animace, ale mnohem rychleji. Oko, stejn� jako u filmu, vid� plynul� pohyb. V z�vislosti na Eulerov�ch rovnic�ch se rychlost a pozice v ka�d�m �asov�m kroku zm�n� takto:</p>

<p class="src0"><span class="kom">nov�_rychlost = star�_rychlost + zrychlen� * �asov� �sek</span></p>
<p class="src0"><span class="kom">nov�_pozice = star�_pozice + nov�_rychlost * �asov� �sek</span></p>

<p>Nyn� se objekty pohybuj� a testuj� na kolize s pou�it�m nov� rychlosti. Zrychlen� objektu je z�sk�no vyd�len�m s�ly, kter� na n�j p�sob�, jeho hmotnost�.</p>

<p class="src0"><span class="kom">zrychlen� = s�la / hmotnost</span></p>

<p>V tomto demu je gravitace jedin� s�la, kter� p�sob� na objekt. M��e b�t reprezentov�na vektorem, kter� ud�v� gravita�n� zrychlen�. U n�s se bude tento vektor rovnat (0; -0,5; 0). To znamen�, �e na za��tku ka�d�ho �asov�ho �seku spo��t�me novou rychlost koule a s testov�n�m koliz� ji posuneme. Pokud b�hem �asov�ho �seku naraz� (nap�. po 0,5 s), posuneme ji na pozici kolize, vypo�teme odraz (nov� vektor rychlosti) a p�esuneme ji o zb�vaj�c� �as (0,5 s). V n�m op�t testujeme kolize atd. Opakujeme tak dlouho, dokud zb�v� n�jak� �as.</p>

<p>Pokud je p��tomno v�ce pohybuj�c�ch se objekt�, mus� b�t nejprve testov�n ka�d� z nich na n�razy do statick�ch objekt�. Ulo�� se �asov� nejbli��� z nich. Potom se provedou testy n�raz� mezi pohybuj�c�mi se objekty - ka�d� s ka�d�m. Vr�cen� �as je porovn�n s �asem u test� se statick�mi objekty a v �vahu je br�n nejbli��� n�raz. Cel� simulace se posune do tohoto �asu. Vypo�te se odraz objektu a op�t se provedou detekce n�raz� do statick�ch objekt� atd. atd. atd. - dokud zb�v� n�jak� �as. P�ekresl� se sc�na a v�e se opakuje nanovo.</p>

<h2>Speci�ln� efekty</h2>

<h3>Exploze</h3>

<p>Kdykoli, kdy� se objekty sraz�, nastane exploze, kter� se zobraz� na sou�adnic�ch pr�se��ku. Velmi jednoduchou cestou je alfablending dvou polygon�, kter� jsou navz�jem kolm� a jejich st�ed je na sou�adnic�ch kolizn�ho bodu. Oba polygony se postupn� zv�t�uj� a zpr�hled�uj�. Alfa hodnota se zmen�uje z po��te�n� jedni�ky a� na nulu. D�ky Z bufferu m��e spousta alfablendovan�ch polygon� zp�sobovat probl�my - navz�jem se p�ekr�vaj�, a proto si p�j��me techniku pou��vanou p�i renderingu ��stic. Abychom v�e d�lali spr�vn�, mus�me polygony �adit od zadn�ch po p�edn� podle vzd�lenosti od pozorovatele. Tak� vypneme z�pis do Depth bufferu (ne �ten�). V�imn�te si, �e omezujeme po�et exploz� na maxim�ln� dvacet na jeden sn�mek. Nastane-li jich najednou v�ce, pole se zapln� a dal�� se nebudou br�t v �vahu. N�sleduje k�d, kter� aktualizuje a renderuje exploze.</p>

<p class="src0">glEnable(GL_BLEND);<span class="kom">// Blending</span></p>
<p class="src0">glDepthMask(GL_FALSE);<span class="kom">// Vypne z�pis do depth bufferu</span></p>
<p class="src0">glBindTexture(GL_TEXTURE_2D, texture[1]);<span class="kom">// Textura exploze</span></p>
<p class="src"></p>
<p class="src0">for(i = 0; i < 20; i++)<span class="kom">// Proch�z� v�buchy</span></p>
<p class="src0">{</p>
<p class="src1">if(ExplosionArray[i]._Alpha >= 0)<span class="kom">// Je exploze vid�t?</span></p>
<p class="src1">{</p>
<p class="src2">glPushMatrix();<span class="kom">// Z�loha matice</span></p>
<p class="src3">ExplosionArray[i]._Alpha -= 0.01f;<span class="kom">// Aktualizace alfa hodnoty</span></p>
<p class="src3">ExplosionArray[i]._Scale += 0.03f;<span class="kom">// Aktualizace m���tka</span></p>
<p class="src"></p>
<p class="src3">glColor4f(1, 1, 0, ExplosionArray[i]._Alpha);<span class="kom">// �lut� barva s pr�hlednost�</span></p>	 
<p class="src3">glScalef(ExplosionArray[i]._Scale, ExplosionArray[i]._Scale, ExplosionArray[i]._Scale);<span class="kom">// Zm�na m���tka</span></p>
<p class="src"></p>
<p class="src3">glTranslatef((float)ExplosionArray[i]._Position.X() / ExplosionArray[i]._Scale, (float)ExplosionArray[i]._Position.Y() / explosionArray[i]._Scale, (float)ExplosionArray[i]._Position.Z() / ExplosionArray[i]._Scale);<span class="kom">// P�esun na pozici kolizn�ho bodu, m���tko je offsetem</span></p>
<p class="src"></p>
<p class="src3">glCallList(dlist);<span class="kom">// Zavol� display list</span></p>
<p class="src2">glPopMatrix();<span class="kom">// Obnova p�vodn� matice</span></p>
<p class="src1">}</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">glDepthMask(GL_TRUE);<span class="kom">// Obnova p�vodn�ch parametr� OpenGL</span></p>
<p class="src0">glDisable(GL_BLEND);</p>
<p class="src0">glDisable(GL_TEXTURE_2D);</p>

<h3>Zvuky</h3>

<p>Pro p�ehr�v�n� zvuk� se pou��v� funkce PlaySound() z multimedi�ln� knihovny Windows - rychl� cesta, jak bez probl�m� p�ehr�t .wav zvuk.</p>

<h2>Vysv�tlen� k�du</h2>

<p>Gratuluji... pokud st�le �tete, �sp�n� jste se prokousali dlouhou a n�ro�nou teoretickou sekc�. P�edt�m, ne� si za�nete hr�t s demem, m�l by b�t je�t� vysv�tlen zdrojov� k�d. Ze v�eho nejd��ve se ale p�jdeme pod�vat na glob�ln� prom�nn�.</p>

<p>Vektory dir a pos reprezentuj� pozici a sm�r kamery, kterou v programu pohybujeme funkc� gluLookAt(). Pokud sc�na nen� vykreslov�na v m�du &quot;sledov�n� koule&quot;, ot��� se kolem osy y.</p>

<p class="src0">TVector dir;<span class="kom">// Sm�r kamery</span></p>
<p class="src0">TVector pos(0, -50, 1000);<span class="kom">// Pozice kamery</span></p>
<p class="src0">float camera_rotation = 0;<span class="kom">// Rotace sc�ny na ose y</span></p>

<p>Gravitace, kter� p�sob� na koule.</p>

<p class="src0">TVector accel(0, -0.05, 0);<span class="kom">// Gravita�n� zrychlen� aplikovan� na koule</span></p>

<p>Pole, kter� ukl�daj� novou a starou pozici v�ech koul� a jejich sm�r. Po�et koul� je natvrdo nastaven na deset.</p>

<p class="src0">TVector ArrayVel[10];<span class="kom">// Rychlost koul�</span></p>
<p class="src0">TVector ArrayPos[10];<span class="kom">// Pozice koul�</span></p>
<p class="src0">TVector OldPos[10];<span class="kom">// Star� pozice koul�</span></p>

<p>�asov� �sek pro simulaci.</p>

<p class="src0">double Time = 0.6;<span class="kom">// �asov� krok simulace</span></p>

<p>Pokud je tato prom�nn� v jedni�ce, zm�n� se m�d kamery tak, aby sledovala pohyby koule. Pro jej� um�st�n� a nasm�rov�n� se pou�ije pozice a sm�r koule s indexem 1, kter� tedy bude v�dy v z�b�ru.</p>

<p class="src0">int hook_toball1 = 0;<span class="kom">// Sledovat kamerou kouli?</span></p>

<p>N�sleduj�c� struktury se popisuj� samy sv�m jm�nem. Budou ukl�dat data o rovin�ch, v�lc�ch a exploz�ch.</p>

<p class="src0">struct Plane<span class="kom">// Struktura roviny</span></p>
<p class="src0">{</p>
<p class="src1">TVector _Position;</p>
<p class="src1">TVector _Normal;</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">struct Cylinder<span class="kom">// Struktura v�lce</span></p>
<p class="src0">{</p>
<p class="src1">TVector _Position;</p>
<p class="src1">TVector _Axis;</p>
<p class="src1">double _Radius;</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">struct Explosion<span class="kom">// Struktura exploze</span></p>
<p class="src0">{</p>
<p class="src1">TVector _Position;</p>
<p class="src1">float   _Alpha;</p>
<p class="src1">float   _Scale;</p>
<p class="src0">};</p>

<p>Objekty struktur.</p>

<p class="src0">Plane pl1, pl2, pl3, pl4, pl5;<span class="kom">// P�t rovin m�stnosti (bez stropu)</span></p>
<p class="src0">Cylinder cyl1, cyl2, cyl3;<span class="kom">// T�i v�lce</span></p>
<p class="src0">Explosion ExplosionArray[20];<span class="kom">// Dvacet exploz�</span></p>

<p>Textury, display list, quadratic.</p>

<p class="src0">GLuint texture[4];<span class="kom">// �ty�i textury</span></p>
<p class="src0">GLuint dlist;<span class="kom">// Display list v�buchu</span></p>
<p class="src0">GLUquadricObj *cylinder_obj;<span class="kom">// Quadratic pro kreslen� koul� a v�lc�</span></p>

<p>Funkce pro kolize koul� se statick�mi objekty a mezi koulemi navz�jem.</p>

<p class="src0">int TestIntersionPlane(const Plane& plane, const TVector& position, const TVector& direction, double& lamda, TVector& pNormal);</p>
<p class="src"></p>
<p class="src0">int TestIntersionCylinder(const Cylinder& cylinder, const TVector& position, const TVector& direction, double& lamda, TVector& pNormal, TVector& newposition);</p>
<p class="src"></p>
<p class="src0">int FindBallCol(TVector& point, double& TimePoint, double Time2, int& BallNr1, int& BallNr2);</p>

<p>Loading textur, inicializace prom�nn�ch, logika simulace, renderov�n� sc�ny a inicializace OpenGL.</p>

<p class="src0">void LoadGLTextures();</p>
<p class="src0">void InitVars();</p>
<p class="src0">void idle();</p>
<p class="src"></p>
<p class="src0">int DrawGLScene(GLvoid);</p>
<p class="src0">int InitGL(GLvoid)</p>

<p>Pro informace o geometrick�ch t��d�ch vektoru, polop��mky a matice nahl�dn�te do zdrojov�ch k�d�. Jsou velmi u�ite�n� a mohou b�t bez probl�m� vyu�ity ve va�ich vlastn�ch programech.</p>

<p>Nejd�le�it�j�� kroky simulace nejprve pop�i pseudok�dem.</p>

<p class="src0"><span class="kom">while (�asov� �sek != 0)</span></p>
<p class="src0"><span class="kom">{</span></p>
<p class="src1"><span class="kom">for (ka�d� koule)</span></p>
<p class="src1"><span class="kom">{</span></p>
<p class="src2"><span class="kom">V�po�et nejbli��� kolize s rovinami;</span></p>
<p class="src2"><span class="kom">V�po�et nejbli��� kolize s v�lci;</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">Ulo�it/nahradit &quot;z�znam&quot; o kolizi, pokud je to do te� nejbli��� kolize v �ase;</span></p>
<p class="src1"><span class="kom">}</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">Testy koliz� mezi pohybuj�c�mi se koulemi;</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">Ulo�it/nahradit &quot;z�znam&quot; o kolizi, pokud je to do te� nejbli��� kolize v �ase;</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">if (nastala kolize?)</span></p>
<p class="src1"><span class="kom">{</span></p>
<p class="src2"><span class="kom">P�esun v�ech koul� do �asu nejbli��� kolize;</span></p>
<p class="src2"><span class="kom">(U� m�me vypo�ten bod, norm�lu a �as kolize.)</span></p>
<p class="src2"><span class="kom">V�po�et odrazu;</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">�asov� �sek -= �as kolize;</span></p>
<p class="src1"><span class="kom">}</span></p>
<p class="src1"><span class="kom">else</span></p>
<p class="src1"><span class="kom">{</span></p>
<p class="src2"><span class="kom">P�esun v�ech koul� na konec �asov�ho �seku;</span></p>
<p class="src1"><span class="kom">}</span></p>
<p class="src0"><span class="kom">}</span></p>

<p>Zdrojov� k�d zalo�en� na pseudok�du je na prvn� pohled mnohem v�ce n�ro�n� na �ten� a hlavn� pochopen�, nicm�n� v z�kladu je jeho p�esnou implementac�.</p>

<p class="src0">void idle()<span class="kom">// Simula�n� logika - kolize</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Deklarace prom�nn�ch</span></p>
<p class="src1">double rt, rt2, rt4, lamda = 10000;</p>
<p class="src"></p>
<p class="src1">TVector norm, uveloc;</p>
<p class="src1">TVector normal, point, time;</p>
<p class="src"></p>
<p class="src1">double RestTime, BallTime;</p>
<p class="src"></p>
<p class="src1">TVector Pos2;</p>
<p class="src"></p>
<p class="src1">int BallNr = 0, dummy = 0, BallColNr1, BallColNr2;</p>
<p class="src1">TVector Nc;</p>
<p class="src"></p>
<p class="src1">if (!hook_toball1)<span class="kom">// Pokud kamera nesleduje kouli</span></p>
<p class="src1">{</p>
<p class="src2">camera_rotation += 0.1f;<span class="kom">// Pooto�en� sc�ny</span></p>
<p class="src"></p>
<p class="src2">if (camera_rotation &gt; 360)<span class="kom">// O�et�en� p�ete�en�</span></p>
<p class="src2">{</p>
<p class="src3">camera_rotation = 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">RestTime = Time;</p>
<p class="src1">lamda = 1000;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�po�et rychlost� v�ech koul� pro n�sleduj�c� �asov� �sek (Eulerovy rovnice)</span></p>
<p class="src1">for (int j = 0; j &lt; NrOfBalls; j++)</p>
<p class="src1">{</p>
<p class="src2">ArrayVel[j] += accel * RestTime;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">while (RestTime &gt; ZERO)<span class="kom">// Dokud neskon�il �asov� �sek</span></p>
<p class="src1">{</p>
<p class="src2">lamda = 10000;<span class="kom">// Inicializace na velmi vysokou hodnotu</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Kolize v�ech koul� s rovinami a v�lci</span></p>
<p class="src2">for (int i = 0; i &lt; NrOfBalls; i++)<span class="kom">// V�echny koule</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// V�po�et nov� pozice a vzd�lenosti</span></p>
<p class="src3">OldPos[i] = ArrayPos[i];</p>
<p class="src3">TVector::unit(ArrayVel[i], uveloc);</p>
<p class="src3">ArrayPos[i] = ArrayPos[i] + ArrayVel[i] * RestTime;</p>
<p class="src3">rt2 = OldPos[i].dist(ArrayPos[i]);</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Kolize koule s rovinou</span></p>
<p class="src3">if (TestIntersionPlane(pl1, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// �as n�razu</span></p>
<p class="src4">rt4 = rt * RestTime / rt2;</p>
<p class="src"></p>
<p class="src4"><span class="kom">// Pokud je men�� ne� n�kter� z d��ve nalezen�ch nahradit ho</span></p>
<p class="src4">if (rt4 &lt;= lamda)</p>
<p class="src4">{</p>
<p class="src5">if (rt4 &lt;= RestTime + ZERO)</p>
<p class="src5">{</p>
<p class="src6">if (!((rt &lt;= ZERO) &amp;&amp; (uveloc.dot(norm) &gt; ZERO)))</p>
<p class="src6">{</p>
<p class="src7">normal = norm;</p>
<p class="src7">point = OldPos[i] + uveloc * rt;</p>
<p class="src7">lamda = rt4;</p>
<p class="src7">BallNr = i;</p>
<p class="src6">}</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionPlane(pl2, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To sam� jako minule, ale s jinou rovinou</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionPlane(pl3, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To sam� jako minule, ale s jinou rovinou</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionPlane(pl4, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To sam� jako minule, ale s jinou rovinou</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionPlane(pl5, OldPos[i], uveloc, rt, norm))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To sam� jako minule, ale s jinou rovinou</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Kolize koule s v�lcem</span></p>
<p class="src3">if (TestIntersionCylinder(cyl1, OldPos[i], uveloc, rt, norm, Nc))</p>
<p class="src3">{</p>
<p class="src4">rt4 = rt * RestTime / rt2;</p>
<p class="src"></p>
<p class="src4">if (rt4 &lt;= lamda)</p>
<p class="src4">{</p>
<p class="src5">if (rt4 &lt;= RestTime + ZERO)</p>
<p class="src5">{</p>
<p class="src6">if (!((rt &lt;= ZERO) &amp;&amp; (uveloc.dot(norm) &gt; ZERO)))</p>
<p class="src6">{</p>
<p class="src7">normal = norm;</p>
<p class="src7">point = Nc;</p>
<p class="src7">lamda = rt4;</p>
<p class="src7">BallNr = i;</p>
<p class="src6">}</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionCylinder(cyl2, OldPos[i], uveloc, rt, norm, Nc))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To sam� jako minule, ale s jin�m v�lcem</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (TestIntersionCylinder(cyl3, OldPos[i], uveloc, rt, norm, Nc))</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// To sam� jako minule, ale s jin�m v�lcem</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Kolize mezi koulemi</span></p>
<p class="src2">if (FindBallCol(Pos2, BallTime, RestTime, BallColNr1, BallColNr2))</p>
<p class="src2">{</p>
<p class="src3">if (sounds)<span class="kom">// Jsou zapnut� zvuky?</span></p>
<p class="src3">{</p>
<p class="src4">PlaySound(&quot;Data/Explode.wav&quot;, NULL, SND_FILENAME | SND_ASYNC);</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if ((lamda == 10000) || (lamda &gt; BallTime))</p>
<p class="src3">{</p>
<p class="src4">RestTime = RestTime - BallTime;</p>
<p class="src"></p>
<p class="src4">TVector pb1, pb2, xaxis, U1x, U1y, U2x, U2y, V1x, V1y, V2x, V2y;<span class="kom">// Deklarace prom�nn�ch</span></p>
<p class="src4">double a, b;</p>
<p class="src"></p>
<p class="src4">pb1 = OldPos[BallColNr1] + ArrayVel[BallColNr1] * BallTime;<span class="kom">// Nalezen� pozice koule 1</span></p>
<p class="src4">pb2 = OldPos[BallColNr2] + ArrayVel[BallColNr2] * BallTime;<span class="kom">// Nalezen� pozice koule 2</span></p>
<p class="src"></p>
<p class="src4">xaxis = (pb2 - pb1).unit();<span class="kom">// Nalezen� X_Axis</span></p>
<p class="src"></p>
<p class="src4">a = xaxis.dot(ArrayVel[BallColNr1]);<span class="kom">// Nalezen� projekce</span></p>
<p class="src4">U1x = xaxis * a;<span class="kom">// Nalezen� pr�m�t� vektor�</span></p>
<p class="src4">U1y = ArrayVel[BallColNr1] - U1x;</p>
<p class="src"></p>
<p class="src4">xaxis = (pb1 - pb2).unit();</p>
<p class="src"></p>
<p class="src4">b = xaxis.dot(ArrayVel[BallColNr2]);<span class="kom">// To sam� pro druhou kouli</span></p>
<p class="src4">U2x = xaxis * b;</p>
<p class="src4">U2y = ArrayVel[BallColNr2] - U2x;</p>
<p class="src"></p>
<p class="src4">V1x = (U1x + U2x - (U1x - U2x)) * 0.5;<span class="kom">// Nalezen� nov�ch rychlost�</span></p>
<p class="src4">V2x = (U1x + U2x - (U2x - U1x)) * 0.5;</p>
<p class="src4">V1y = U1y;</p>
<p class="src4">V2y = U2y;</p>
<p class="src"></p>
<p class="src4">for (j = 0; j &lt; NrOfBalls; j++)<span class="kom">// Aktualizace pozic v�ech koul�</span></p>
<p class="src4">{</p>
<p class="src5">ArrayPos[j] = OldPos[j] + ArrayVel[j] * BallTime;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">ArrayVel[BallColNr1] = V1x + V1y;<span class="kom">// Nastaven� pr�v� vypo��tan�ch vektor� koul�m, kter� do sebe narazily</span></p>
<p class="src4">ArrayVel[BallColNr2] = V2x + V2y;</p>
<p class="src"></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Aktualizace pole exploz�</span></p>
<p class="src4">for(j = 0; j &lt; 20; j++)<span class="kom">// V�echny exploze</span></p>
<p class="src4">{</p>
<p class="src5">if (ExplosionArray[j]._Alpha &lt;= 0)<span class="kom">// Hled� voln� m�sto</span></p>
<p class="src5">{</p>
<p class="src6">ExplosionArray[j]._Alpha = 1;<span class="kom">// Nepr�hledn�</span></p>
<p class="src6">ExplosionArray[j]._Position = ArrayPos[BallColNr1];<span class="kom">// Pozice</span></p>
<p class="src6">ExplosionArray[j]._Scale = 1;<span class="kom">// M���tko</span></p>
<p class="src"></p>
<p class="src6">break;<span class="kom">// Ukon�it prohled�v�n�</span></p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">continue;<span class="kom">// Opakovat cyklus</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Konec test� koliz�</span></p>
<p class="src2"><span class="kom">// Pokud se pro�el cel� �asov� �sek a byly vypo�teny reakce koul�, kter� narazily</span></p>
<p class="src2">if (lamda != 10000)</p>
<p class="src2">{</p>
<p class="src3">RestTime -= lamda;<span class="kom">// Ode�ten� �asu kolize od �asov�ho �seku</span></p>
<p class="src"></p>
<p class="src3">for (j = 0; j &lt; NrOfBalls; j++)</p>
<p class="src3">{</p>
<p class="src4">ArrayPos[j] = OldPos[j] + ArrayVel[j] * lamda;</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">rt2 = ArrayVel[BallNr].mag();</p>
<p class="src3">ArrayVel[BallNr].unit();</p>
<p class="src3">ArrayVel[BallNr] = TVector::unit((normal * (2 * normal.dot(-ArrayVel[BallNr]))) + ArrayVel[BallNr]);</p>
<p class="src3">ArrayVel[BallNr] = ArrayVel[BallNr] * rt2;</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Aktualizace pole exploz�</span></p>
<p class="src3">for(j = 0; j &lt; 20; j++)<span class="kom">// V�echny exploze</span></p>
<p class="src3">{</p>
<p class="src4">if (ExplosionArray[j]._Alpha &lt;= 0)<span class="kom">// Hled� voln� m�sto</span></p>
<p class="src4">{</p>
<p class="src5">ExplosionArray[j]._Alpha = 1;<span class="kom">// Nepr�hledn�</span></p>
<p class="src5">ExplosionArray[j]._Position = ArrayPos[BallColNr1];<span class="kom">// Pozice</span></p>
<p class="src5">ExplosionArray[j]._Scale = 1;<span class="kom">// M���tko</span></p>
<p class="src"></p>
<p class="src5">break;<span class="kom">// Ukon�it prohled�v�n�</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">RestTime = 0;<span class="kom">// Ukon�en� hlavn�ho cyklu a vlastn� i funkce</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jak jsem u� napsal na za��tku, p�edm�t koliz� je velmi t�k� a rozs�hl�, aby se dal popsat jen v jednom tutori�lu, p�esto jste se nau�ili spoustu nov�ch v�c�. M��ete za��t vytv��et vlastn� p�sobiv� dema. Nyn�, kdy� ch�pete z�klady, budete l�pe rozum�t i ciz�m zdrojov�m k�d�m, kter� v�s zase posunou o kousek d�l. P�eji hodn� �t�st�.</p>

<p class="autor">napsal: Dimitrios Christopoulos<br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3>Informace o autorovi</h3>

<p>V sou�asn� dob� pracuje jako softwarov� in�en�r virtu�ln� reality Hel�nsk�ho sv�ta v At�n�ch/�ecko (www.fhw.gr). A�koli se narodil v N�mecku, studoval �eckou univerzitu Patras na bakal��e p��rodn�ch v�d v po��ta�ov�m in�en�rstv� a informatice. Je tak� dr�itelem MSc degree (titul Magistra p��rodn�ch v�d) z univerzity Hull (Anglie) v po��ta�ov� grafice a virtu�ln�m prost�ed�.</p>

<p>Prvn� kr��ky s programov�n�m podnikl v jazyce Basic na Commodoru 64. Po za��tku studia se p�eorientoval na C/C++/Assembler na platform� PC. B�hem n�kolika minul�ch let si jako grafick� API zvolil OpenGL.</p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson30.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson30_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson30.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson30.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:another.freak@gmx.de">Felix Hahn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson30.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:conrado@buhrer.net">Conrado</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson30.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson30.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:rodolphe.suescun@wanadoo.fr">Rodolphe Suescun</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson30.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson30.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(30);?>
<?FceNeHeOkolniLekce(30);?>

<?
include 'p_end.php';
?>
