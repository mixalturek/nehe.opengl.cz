<?
$g_title = 'CZ NeHe OpenGL - Analytick� geometrie';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<style>
<!--
.vektor { text-decoration: overline; }
-->
</style>

<h1>Analytick� geometrie</h1>

<p class="nadpis_clanku">Tento �l�nek vych�z� z m�ch z�pisk� do matematiky z druh�ho ro�n�ku na st�edn� �kole. Jo, na diktov�n� byla Wakuovka v�dycky dobr�... Tehdy jsem moc nech�pal k �emu mi tento obor matematiky v�bec bude, ale kdy� jsem se za�al v�novat OpenGL, z�hy jsem pochopil. Zkuste si vz�t nap��klad n�jak� pozd�j�� NeHe Tutori�l. Bez znalost� 3D matematiky nem�te �anci. Douf�m, �e v�m tento �l�nek pom��e alespo� se z�klady a pochopen�m princip�.</p>

<h2>Kart�zsk� soustava sou�adnic</h2>
<p>Tak tedy za�neme. Co je geometrie v� snad ka�d�. Ale co je analytick� geometrie? Dala by se definovat velice jednodu�e: Analytick� geometrie �e�� v�po�tem to, co se d� nakreslit. A p�esn� o to se v po��ta�ov� grafice jedn�. Pro v�echny v�po�ty je nutn� soustava sou�adnic. Tvo�� ji dv� (pop�. t�i) navz�jem kolm� osy, kter� maj� stejn� velk� jednotky.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/soustava_souradnic.gif" width="175" height="78" alt="Soustava sou�adnic" /></div>

<h2>Bod</h2>
<p>Nejjednodu���m �tvarem je bod. Nem� ��dn� rozm�r, ale pouze polohu. Jeho poloha m� ve dvourozm�rn�m syst�mu dv� slo�ky, ve t��rozm�rn�m t�i atd. Form�ln� se zapisuje takto: JM�NO BODU[x, y]. V n�sleduj�c�m p��kladu by se uveden� body definovaly takto:</p>

<p class="src0">A[-2; 1]</p>
<p class="src0">B[ 3; 1]</p>
<p class="src0">C[-2; 3]</p>
<p class="src"></p>
<p class="src0">S[ ?; ?]<span class="kom">// Je st�edem �se�ky BC</span></p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/priklad_01.gif" width="200" height="200" alt="P��klad 1" /></div>

<h3>St�ed �se�ky - grafick� metoda</h3>
<p>Bod S jsme nedefinovali, proto�e jen tak od oka jeho slo�ky neur��me. V�me pouze, �e je st�edem �se�ky BC a chceme ur�it polohu. V p��pad� grafick� metody bychom si vzali pap�r, tu�ku a prav�tko, nakreslili bychom sou�adnicov� osy, vepsali body a spojili je �se�kami. D�le bychom zm��ili d�lku �se�ky BC, vyd�lili ji dv�ma a tuto vypo��tanou vzd�lenost nanesli (je jedno, zda z bodu B nebo C, proto�e S je v polovin�). Ode�teme jednotliv� x, y slo�ky a z�sk�me polohu bodu S.</p>

<h3>St�ed �se�ky - po�etn� metoda</h3>
<p>Jin� je ale situace, kdybychom nem�li ��dn� pom�cky a museli v�echno spo��tat. Z obr�zku jde na prvn� pohled vid�t, �e x-ov� slo�ka bodu S je pr�m�rem x-ov�ch slo�ek bodu B a C. Y-ov� slo�ka je to sam�. M��eme tedy vytvo�it vzorec a dosadit hodnoty. Porovn�me-li v�sledek S[0,5; 2] s obr�zkem, sou�adnice odpov�daj�. Od nyn�j�ka budeme pou��vat pouze po�etn� metodu.</p>

<p class="src0">S[x; y] = [((x<sub>B</sub> + x<sub>C</sub>) / 2); ((y<sub>B</sub> + y<sub>C</sub>) / 2)]</p>
<p class="src0">S[((3 - 2)) / 2); ((1 + 3) / 2)]</p>
<p class="src0">S[0,5; 2]</p>
<p class="src"></p>

<h3>D�lka �se�ky</h3>
<p>Druh�m probl�mem m��e b�t, pokud chceme zjistit d�lku �se�ky BC. Op�t se pod�v�me na obr�zek a na prvn� pohled vid�me :-), �e �se�ka BC je p�eponou pravo�hl�ho troj�heln�ku ABC, pro kter� plat� Pythagorova v�ta.</p>

<p class="src0">c<sup>2</sup> = a<sup>2</sup> + b<sup>2</sup></p>
<p class="src0">c = sqrt(a<sup>2</sup> + b<sup>2</sup>)<span class="kom">// sqrt(x) = druh� odmocnina z x</span></p>

<p>D� se ��ct, �e n�sleduj�c� vzorec by platil i tehdy, pokud by troj�heln�k ABC nebyl pravo�hl� a dokonce i tehdy, pokud bychom nem�li v�bec ��dn� troj�heln�k, ale pouze �se�ku BC. Prav�m �hlem je v tomto p��pad� kolmost sou�adnicov�ch os, d�lky stran a, c zastupuj� pr�m�ty �se�ky do os x, y. Pythagorova v�ta tedy st�le plat�.</p>

<p class="src0">|BC| = sqrt((x<sub>B</sub> - x<sub>C</sub>)<sup>2</sup> + (y<sub>B</sub> - y<sub>C</sub>)<sup>2</sup>)</p>
<p class="src0">|BC| = sqrt(5<sup>2</sup> + (-2)<sup>2</sup>)</p>
<p class="src0">|BC| = sqrt(25 + 4)</p>
<p class="src0">|BC| = sqrt(29)</p>
<p class="src0">|BC| = 5,3851</p>
<p class="src"></p>

<h2>Vektor</h2>

<p>Vektory obecn� ur�uj�, jak se zm�n� slo�ky sou�adnic x a y ne� se p�esuneme z po��tku vektoru na jeho konec. Zna�� se �ipkou sm��uj�c� zleva doprava (od po��te�n�ho do koncov�ho bodu) nad jm�nem vektoru. Pozn.: V tomto �l�nku ho zna��m pouze ��rou, proto�e nemohu p�ij�t na to, jak v HTML vytvo�it �ipku. Narozd�l od bodu, kter� se um�s�uje do hranat�ch, p�eme slo�ky vektoru do oby�ejn�ch kulat�ch z�vorek. Chceme-li zjistit vektor <span class="vektor">BC</span> ode�teme od koncov�ho bodu, po��tek.</p>

<p class="src0"><span class="vektor">BC</span> = ((x<sub>C</sub> - x<sub>B</sub>); (y<sub>C</sub> - y<sub>B</sub>))</p>
<p class="src0"><span class="vektor">BC</span> = ((-2 -3); (3-1))</p>
<p class="src0"><span class="vektor">BC</span> = (-5; 2)</p>

<p>Dva stejn� vektory (stejn� velk� a stejn� orientovan�) p�edstavuj� v matematice dv� r�zn� um�st�n� t�ho� vektoru. Z toho plyne, �e vektor m��eme p�en�st kamkoli na rovnob�nou p��mku, akor�t nesm�me zm�nit jeho velikost. Toto ale neplat� ve fyzice, kde se u vektoru, krom� velikosti mus� ur�it i po��tek nebo konec (nap�. p�sobi�t� s�ly).</p>

<h3>Velikost vektoru</h3>
<p>P�i zji��ov�n� velikosti vektoru se postupuje �pln� stejn� jako p�i ur�ov�n� d�lky �se�ky.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/priklad_02.gif" width="100" height="100" alt="P��klad 2" /></div>

<p class="src0">|<span class="vektor">AB</span>| = sqrt((x<sub>B</sub> - x<sub>A</sub>)<sup>2</sup> + (y<sub>B</sub> - y<sub>A</sub>)<sup>2</sup>)</p>
<p class="src"></p>

<h3>Normalizace vektoru na jednotkovou d�lku</h3>
<p>V matematice nen� u vektoru d�le�it� jeho d�lka, ale sm�r. Sm��uj�-li dva vektory stejn�m sm�rem, jsou shodn� (mohou le�et i na libovoln�ch rovnob�k�ch). Jednotkovou d�lku vektoru vypo��t�me tak, �e z�sk�me aktu�ln� d�lku vektoru a touto hodnotou vyd�l�me jednotliv� x, y, z slo�ky.</p>

<h2>Po��t�n� s vektory</h2>

<h3>S��t�n� a ode��t�n�</h3>
<p>V�dy se�teme zvlṻ x-ov� a zvlṻ y-slo�ky. Nic t�k�ho.</p>

<p class="src0"><span class="vektor">a</span> = (-3; 2)</p>
<p class="src0"><span class="vektor">b</span> = ( 4; 6)</p>
<p class="src"></p>
<p class="src0"><span class="vektor">a</span> + <span class="vektor">b</span> = ( 1; 8)</p>
<p class="src0"><span class="vektor">a</span> - <span class="vektor">b</span> = (-7;-4)</p>
<p class="src"></p>

<h3>N�soben� a d�len� vektoru ��slem</h3>
<p>Op�t vyn�sob�me zvlṻ jednotliv� slo�ky.</p>

<p class="src0">4 * <span class="vektor">a</span> = (4 * x<sub>a</sub>; 4 * y<sub>a</sub>)</p>
<p class="src0">4 * <span class="vektor">a</span> = (-12; 8)</p>
<p class="src"></p>

<h3>Skal�rn� sou�in vektor�</h3>

<p>N�sob� se zvlṻ x-ov� a zvlṻ y-ov� slo�ky, kter� se se�tou.</p>

<p class="src0"><span class="vektor">u</span> * <span class="vektor">v</span> = u<sub>X</sub>*v<sub>X</sub> + u<sub>Y</sub>*v<sub>Y</sub></p>

<p>Nebo tak�</p>

<p class="src0"><span class="vektor">u</span> * <span class="vektor">v</span> = |<span class="vektor">u</span>| * |<span class="vektor">v</span>| * cos uhel</p>
<p class="src"></p>

<h3>�hel dvou vektor�</h3>

<p>Ze vzorce pro v�po�et skal�rn�ho sou�inu vektor� (viz. v��e) lze odvodit vztah pro v�po�et �hlu dvou vektor�.</p>

<p class="src0">cos uhel = (<span class="vektor">u</span> * <span class="vektor">v</span>) / (|<span class="vektor">u</span>| * |<span class="vektor">v</span>|)</p>

<p>Dosad�me-li do rovnice d��ve uveden� vzorce, z�sk�me n�sleduj�c� vztah:</p>

<img src="images/clanky/analyticka_geometrie/vzorec_uhel_2_vektoru.gif" width="190" height="55" alt="Vzorec pro v�po�et �hlu dvou vektor�" />

<p>A pokud budou vektory normalizovan� (jednotkov� d�lka), m��eme jmenovatel ze zlomku vypustit, proto�e se rovn� jedn�.</p>

<p class="src0">cos uhel = (<span class="vektor">u</span> * <span class="vektor">v</span>)<span class="kom">// Za p�edpokladu jednotkov�ch d�lek</span></p>

<h3>Vektorov� sou�in dvou vektor�</h3>

<p>Vyn�sob�me-li vektorov� dva vektory, z�sk�me vektor t�et�, kter� je kolm� k rovin� ur�en� p�vodn�mi dv�ma vektory. Aby bylo mo�no ur�it vektorov� sou�in, nesm� b�t vektory <span class="vektor">u</span> a <span class="vektor">v</span> rovnob�n�, proto�e by netvo�ily rovinu. Vektorov� sou�in se nezna�� oper�torem *, ale x.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/vektorovy_soucin.gif" width="100" height="100" alt="Vektorov� sou�in dvou vektor�" /></div>

<p class="src0"><span class="vektor">u</span> = (-3; 5; -1)</p>
<p class="src0"><span class="vektor">v</span> = ( 1; 2; -4)</p>

<img src="images/clanky/analyticka_geometrie/vektorovy_soucin_vzorec.gif" width="395" height="60" alt="Vzorec vektorov�ho sou�inu dvou vektor�" />

<p>Nyn� polo��me vektory <span class="vektor">i</span>, <span class="vektor">j</span>, <span class="vektor">k</span> rovny jedn� (jednotkov� vektory). N�soben�m jedni�kou se koeficienty nezm�n�, tak�e jsme pr�v� z�skali v�sledek.</p>

<p class="src0"><span class="vektor">w</span> = (-18; -13; -11)</p>

<h2>Vektorov� prostory</h2>

<h3>Line�rn� z�visl� a line�rn� nez�visl� vektory</h3>

<p>Dimenze (rozm�r) vektorov�ho prostoru ur�uje maxim�ln� po�et line�rn� nez�visl�ch vektor�. Je tak� d�na po�tem slo�ek sou�adnice. Vektory jsou line�rn� z�visl�, kdy� pro libovoln� vektor soustavy vektor� plat�:</p>

<p class="src0"><span class="vektor">v1</span>, <span class="vektor">v2</span>, <span class="vektor">v3</span> ... <span class="vektor">vn</span></p>
<p class="src0"><span class="vektor">vm</span> = k1*<span class="vektor">v1</span> + k2*<span class="vektor">v2</span> + ... + kn*<span class="vektor">vn</span></p>

<p>Pro line�rn� nez�visl� vektory tato rovnice neplat� pro ��dn� vektor syst�mu.</p>

<h3>Jednorozm�rn� prostor - p��mka</h3>
<p>Jednorozm�rn� prostor m� pouze jeden nez�visl� vektor. Ostatn� vektory na p��mce se daj� pomoc� tohoto vektoru vyj�d�it.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/dimenze1.gif" width="200" height="57" alt="Jednorozm�rn� prostor" /></div>

<h3>Dvourozm�rn� prostor - rovina</h3>
<p>M� dva line�rn� nez�visl� vektory.</p>

<h3>T��rozm�rn� prostor</h3>
<p>M� t�i line�rn� nez�visl� vektory. Mysl�m, �e podstatu u� ch�pete. Tyto vlastnosti se vyu��vaj� k �e�en� syst�mu N rovnic o N nezn�m�ch.</p>

<h2>Kolm� vektory</h2>
<p>Dva kolm� vektory v rovin� maj� obr�cen� po�ad� slo�ek a u jedn� z nich (jedno kter�) opa�n� znam�nko. Kolm� vektor k zadan�mu vektoru je rovn� libovoln� k-n�sobek vektoru z�skan�ho uveden�m postupem. Odvozen� t�to definice vych�z� ze vzorce pro �hel dvou vektor�, kde se za �hel dosad� 90�.</p>

<p class="src0"><span class="vektor">a</span> = ( 3; 2)</p>
<p class="src0"><span class="vektor">b</span> = ( ?; ?)<span class="kom">// Je kolm� k <span class="vektor">a</span></span></p>
<p class="src"></p>
<p class="src0"><span class="vektor">b</span> = ( 2;-3)</p>
<p class="src0"><span class="vektor">b</span> = (-2; 3)</p>
<p class="src0"><span class="vektor">b</span> = (-4; 6)<span class="kom">// Atd.</span></p>

<h2>Rovnice p��mky v rovin�</h2>
<h3>Typy rovnic p��mky:</h3>
<ul>
<li>Parametrick� rovnice p��mky</li>
<li>Obecn� tvar</li>
<li>Sm�rnicov� tvar</li>
<li>Sm�rnicov� tvar ur�en� sou�adnicemi bod�</li>
<li>�sekov� tvar</li>
</ul>

<h2>Parametrick� tvar rovnic p��mky</h2>
<p>N�sleduj�c� vzorec je parametrickou rovnic� p��mky.</p>

<p class="src0">p:</p>
<p class="src0">x = A<sub>X</sub> + s<sub>X</sub>*t</p>
<p class="src0">y = A<sub>Y</sub> + s<sub>Y</sub>*t</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_primky_param.gif" width="139" height="57" alt="Parametrick� rovnice p��mky" /></div>

<p class="src0">A[2; 4]</p>
<p class="src0"><span class="vektor">s</span> = (5; 7)</p>
<p class="src"></p>
<p class="src0">p:</p>
<p class="src0">x = 2 + 5*t</p>
<p class="src0">y = 4 + 7*t</p>
<p class="src"></p>

<h3>Sestaven� parametrick�ch rovnic ze dvou bod�</h3>
<p>Toto u� by m�lo b�t jasn�, ale mysl�m si, �e mal� uk�zka �kodit nebude. Je �pln� jedno, zda po z�sk�n� vektoru dosad�me do rovnice sou�adnice bodu A nebo B, proto�e oba le�� na hledan� p��mce.</p>

<p class="src0">A[2; 4]</p>
<p class="src0">B[5; 3]</p>
<p class="src"></p>
<p class="src0"><span class="vektor">s</span> = B - A = (5-2; 3-4) = (3; -1)</p>
<p class="src"></p>
<p class="src0">p:</p>
<p class="src0">x = 2 + 3*t</p>
<p class="src0">y = 4 - t</p>

<p>A je�t� si zkus�me, zda bod C[1; 2] na t�to p��mce le�� nebo ne. Za x a y dosad�me jeho sou�adnice a pokud se budou parametry t v obou rovnic�ch rovnat, C le�� na p��mce.</p>

<p class="src0">p:</p>
<p class="src0">1 = 2 + 3*t <span class="kom">=&gt;</span> 3*t = -1 <span class="kom">=&gt;</span> t = -1/3</p>
<p class="src0">2 = 4 - t <span class="kom">=&gt;</span> -t = -2 <span class="kom">=&gt;</span> t = 2</p>

<p>V prvn� rovnici vy�lo t rovno -1/3 v druh� se t rovnalo 2. Z toho plyne, �e bod C nele�� na p��mce t.</p>

<p>Nev�hoda parametrick�ho vyj�d�en� je v tom, �e na prvn� pohled nepozn�me, jestli se jedn� u dvou soustav rovnic o tut� p��mku nebo ne.</p>

<h3>Po��t�n� s rovnicemi p��mek v parametrick�m tvaru</h3>

<p>P�edem se omlouv�m za to, ale v�echno bude pouze teoreticky, proto�e psan� p��klad� v HTML mi u� leze opravdu na nervy.</p>

<p>Rovnici p��mky sestavit um�me, ale jak bychom postupovali p�i hled�n� rovnice p��mky, kter� je rovnob�n� ke zn�m� p��mce v ur�it�m bod�? Vych�z�me z toho, �e rovnob�n� p��mky mohou m�t stejn� sm�rov� vektory, tak�e pouze nahrad�me sou�adnice bodu v rovnic�ch.</p>

<p>U kolm� p��mky by se op�t nahradily sou�adnice bodu, ale nav�c bychom je�t� museli zm�nit vektor na kolm� - nic t�k�ho (viz. kolm� vektory).</p>

<p>Pr�se��k dvou p��mek je spole�n�m �e�en�m dvou rovnic p��mek. V parametrick�m tvaru porovn�me sou�. x prvn� p��mky se sou�. x druh� p��mky. Vypo�teme parametr t a jestli plat� rovnost i pro sou�adnice y p�i stejn�m t, je tento parametr p��slu�n� k pr�se��ku. Dosazen�m parametru do rovnic dostaneme sou�adnice pr�se��ku.</p>

<p>�hel dvou p��mek je �hlem mezi dv�ma sm�rov�mi vektory (viz. �hel dvou vektor�).</p>

<h2>Obecn� rovnice p��mky</h2>
<p>N�e je uveden jej� vzorec. Prom�nn� x a y jsou sou�adnice bodu, kter� le�� na p��mce. A, b jsou slo�ky norm�lov�ho vektoru p��mky.</p>

<p class="src0">p: a*x + b*y + c = 0</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_primky_obec.gif" width="139" height="57" alt="Obecn� rovnice p��mky" /></div>

<h3>Sestaven� obecn� rovnice p��mky ze dvou bod�</h3>

<p>Nejprve vypo��t�me sm�rov� vektor a ten p�evedeme na norm�lov�. T�m z�sk�me prom�nn� a, b, x, y. Z�stane n�m jednoduch� rovnice o jedn� nezn�m�. Vypo�teme c a dosad�me do p�vodn� rovnice</p>

<p class="src0">A[2; 4]</p>
<p class="src0">B[5; 3]</p>
<p class="src"></p>
<p class="src0"><span class="vektor">s</span> = <span class="vektor">AB</span> = (5-2; 3-4) = (3; -1)<span class="kom">// Sm�rov� vektor</span></p>
<p class="src0"><span class="vektor">n</span> = (1; 3)<span class="kom">// Norm�lov� vektor</span></p>

<p>Z�skali jsme ��sla a = 1, b = 3 a pou�ijeme nap�. slo�ky bodu A (je to jedno). V�e dosad�me do rovnice p��mky a hled�me ��slo c.</p>

<p class="src0">p: a*x + b*y + c = 0</p>
<p class="src0">p: 1*2 + 3*4 + c = 0</p>
<p class="src"></p>
<p class="src0">c = - (1*2 + 3*4) = - (2 + 12) = -14</p>

<p>Nakonec dosad�me do rovnice p��mky ��sla a, b, c a m�me v�sledek.</p>

<p class="src0">p: x + 3y - 14 = 0<span class="kom">// V�sledn� rovnice p��mky</span></p>

<p>Je�t� jedna pozn�mka na konec. Je-li sm�rov� (pop�. norm�lov�) vektor zad�n tak, �e jeho slo�ky se daj� roz���it nebo kr�tit, m��eme za n�j zvolit jeho libovoln� k n�sobek. N�sleduj�c� dv� rovnice p��mek jsou tedy toto�n�:</p>

<p class="src0">p: -4x + 8y + 24 = 0</p>
<p class="src0">p: -x + 2y + 6 = 0<span class="kom">// Vykr�cen� verze</span></p>
<p class="src"></p>

<h3>Rovnice rovnob�n�ch p��mek</h3>
<p>Hled�me rovnob�ku q s p��mkou p: x - 2y + 3 = 0, kter� proch�z� bodem M[2; 5]. Sta�� dosadit sou�adnice bodu M do rovnice p a vypo��tat ��slo c, kter� dosad�me do p�vodn� rovnice p. V�imn�te si, �e se p��mky li�� pouze ��slem c.</p>

<p class="src0">q: x - 2y + c = 0</p>
<p class="src0">2 - 2*5 + c = 0</p>
<p class="src0">c = 8</p>
<p class="src"></p>
<p class="src0">q: x - 2y + 8 = 0<span class="kom">// P��mka q je rovnob�n� s p</span></p>
<p class="src0">p: x - 2y + 3 = 0</p>
<p class="src"></p>

<h3>Rovnice kolm�ch p��mek</h3>
<p>Op�t pouze postup: Vytvo�� se kolm� vektor k norm�lov�mu vektoru p�vodn� p��mky. Spolu s bodem, kter�m m� kolm� p��mka proch�zet, se dosad� do rovnice a vypo��t� se c. P��klad dvou kolm�ch p��mek:</p>

<p class="src0">p: 5x - 6y + 4 = 0</p>
<p class="src0">q: 6x + 5y -15 = 0</p>

<p>Stejn� jako rovnob�n� p��mky, jsou si tak� trochu podobn�...</p>

<h2>Rovnice p��mky ve sm�rnicov�m tvaru</h2>

<p>Sm�rnicov� tvar rovnice p��mky se u��v� tam, kde je zad�n sklon p��mky vzhledem k ose x nebo je-li sm�rnice vypo�tena jin�m zp�sobem (nap�. derivac�). ��slo q v n�sleduj�c� rovnici ozna�uje �sek na ose y, kde ji p��mka prot�n�. K je sm�rnice p��mky, kter� se vypo��t� goniometrickou funkc� k = tg a. Je to tak� derivace jak�koli funkce - tedy te�na k jej�mu grafu.</p>

<p class="src0">y = k*x + q</p>

<p>Nebo tak�:</p>

<p class="src0">y - y<sub>A</sub> = k * (x - x<sub>A</sub>)</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_primky_smer.gif" width="345" height="161" alt="Sm�rnicov� rovnice p��mky" /></div>

<h3>Z�sk�n� ��sla k</h3>

<p>Vych�z�me z toho, �e k = tg a. Postup zji�t�n� tg a vysv�tluje obr�zek.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_primky_smer_zisk_k.gif" width="183" height="161" alt="Z�sk�n� ��sla k" /></div>

<p>Po zji�t�n� ��sla k n�m u� nic nebr�n� tomu, abychom ho spolu se slo�kami bodu A nebo B dosadili do rovnice a z�skali q.</p>

<h3>Rovnob�ky a kolmice</h3>

<p>Rovnob�n� p��mky maj� v rovnic�ch stejnou hodnotu k, li�� se v q. Kolmice maj� v k z�pornou p�evr�cenou hodnotu. To znamen�, �e pokud je u jedn� nap��klad k = 2, u druh� bude k = - 1/2.</p>

<h3>Vzd�lenost bodu od p��mky a vzd�lenost dvou rovnob�ek</h3>

<p>��sla a, b, c jsou konstanty z rovnice p��mky v obecn�m tvaru a ��sla x, y jsou sou�adnice bodu. Sta�� pak dosadit do vzorce.</p>

<img src="images/clanky/analyticka_geometrie/bod_od_primky.gif" width="90" height="46" alt="Vzd�lenost bodu od p��mky" />

<p>Vzd�lenost dvou rovnob�ek lze zjistit �pln� stejn�. Nejd��ve z�sk�me bod, kter� le�� na jedn� z nich a pak op�t dosad�me do vzorce.</p>

<h2>Rovina v prostoru</h2>

<h3>Typy rovnic rovin:</h3>
<ul>
<li>Parametrick� rovnice roviny</li>
<li>Obecn� rovnice</li>
</ul>

<h2>Parametrick� rovnice roviny</h2>
<p>Jsou skoro stejn� jako parametrick� rovnice p��mky. N�sleduje vzorec a p��klad.</p>

<p class="src0">R:</p>
<p class="src0">x = A<sub>X</sub> + k<sub>X</sub>*<span class="vektor">u</span> + l<sub>X</sub>*<span class="vektor">v</span></p>
<p class="src0">y = A<sub>Y</sub> + k<sub>Y</sub>*<span class="vektor">u</span> + l<sub>Z</sub>*<span class="vektor">v</span></p>
<p class="src0">z = A<sub>Z</sub> + k<sub>Y</sub>*<span class="vektor">u</span> + l<sub>Z</sub>*<span class="vektor">v</span></p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_roviny_param.gif" width="77" height="80" alt="Parametrick� rovnice roviny" /></div>

<p class="src0">R:</p>
<p class="src0">x = 1 + 3k +l</p>
<p class="src0">y = 4 - 4k - 5l</p>
<p class="src0">z = -2 + 7k - l</p>

<h2>Obecn� rovnice roviny</h2>

<p>Vzorec je analogick� vzorci obecn� rovnice p��mky. ��sla a, b, c jsou slo�ky norm�lov�ho vektoru roviny a ��sla x, y, z jsou sou�adnice bodu, kter� na n� le��. Stejn� jako se u p��mky dopo��t�valo c, nyn� se mus� spo��tat d - ��dn� probl�m, v�e u� zn�me.</p>

<p class="src0">R: ax + by + cz + d = 0</p>

<p>D�me si jeden p��klad a z�rove� ho teoreticky! vy�e��me. Ur�ete rovnici roviny R, kter� je d�na body K[-2; 1; 1], L[0; 3; -5]; M[4; -6; 9]. Postupujeme tak, �e ur��me jeden bod jako z�kladn�, nap��klad K a ur��me vektory <span class="vektor">KL</span> a <span class="vektor">KM</span>. Pot�ebujeme ur�it vektor, kter� je kolm� k ob�ma najednou. Z�sk�me ho vektorov�m sou�inem vektor� <span class="vektor">n</span> = <span class="vektor">KL</span> x <span class="vektor">KM</span>. V�sledek dosad�me do rovnice roviny a vypo��t�me nezn�mou d.</p>

<div class="okolo_img"><img src="images/clanky/analyticka_geometrie/rov_roviny_obec.gif" width="58" height="63" alt="Parametrick� rovnice roviny" /></div>

<h2>Zvl�tn� polohy roviny v prostoru</h2>

<h3>Rovina proch�zej�c� po��tkem sou�adnic</h3>
<p>Jeden bod v rovin� (po��tek sou�adnic) m� sou�adnice 0[0; 0; 0]. Z toho plyne, �e:</p>
<p class="src0">R: a*x + b*y + c*z + d = 0</p>
<p class="src0">R: a*0 + b*0 + c*0 + d = 0</p>
<p class="src0">d = 0<span class="kom">// V�sledn� rovnice roviny</span></p>
<p class="src"></p>

<h3>Rovina rovnob�n� s n�kterou osou</h3>
<p>Chyb� koeficient u prom�nn�, kter� pat�� ke konkr�tn� ose.</p>
<p class="src0">R: 3y - 2z + 5 = 0<span class="kom">// Rovina rovnob�n� s osou x</span></p>
<p class="src"></p>

<h3>Rovina rovnob�n� s dv�ma osami</h3>
<p>U obou chyb� koeficienty a t�et� slo�ka je konstantn�.</p>
<p class="src0">R: 3z + 5 = 0<span class="kom">// Rovina rovnob�n� s osami x a y</span></p>
<p class="src0">z = - 5/3</p>
<p class="src0">z = konst.</p>

<h2>Zp�soby zad�n� roviny</h2>

<p>Ka�d� rovina je zad�na v�dy t�emi nez�visl�mi prvky:</p>
<ul>
<li>T�i body nele��c� v p��mce</li>
<li>Dv� p��mky, kter� nejsou mimob�n� (= kter� se prot�naj�)</li>
<li>Jeden bod a dva sm�rov� vektory</li>
<li>Rovnob�nost roviny s n�kterou osou a dva body, pop�. jedna p��mka</li>
<li>Rovnob�nost roviny se dv�ma osami a jeden bod</li>
</ul>

<h2>P��mka v prostoru</h2>

<p>V rovin� mohla b�t p��mka vyj�d�ena mnoha zp�soby, kter� se v prostoru smrskly na jedin� mo�n� zp�sob - parametrick� rovnice.</p>

<h3>Parametrick� rovnice p��mky</h3>
<p>Tohle u� zn�me, tak�e to nebudu zbyte�n� rozepisovat, p�id�v� se pouze dal�� rovnice pro osu z.</p>

<p class="src0">p:</p>
<p class="src0">x = A<sub>X</sub> + <span class="vektor">s</span><sub>X</sub>*t</p>
<p class="src0">y = A<sub>Y</sub> + <span class="vektor">s</span><sub>Y</sub>*t</p>
<p class="src0">z = A<sub>Z</sub> + <span class="vektor">s</span><sub>Z</sub>*t</p>

<p>Hled�me-li p��mku jako pr�se�nici dvou rovin, zvol�me dva body, kter� le�� sou�asn� v obou rovin�ch - tj. jednu sou�adnici zvol�me a ostatn� dv� mus� vyhovovat rovnic�m obou rovin. Pak sta�� vytvo�it sm�rov� vektor a n�sledn� ho s jedn�m z bod� dosadit do parametrick�ch rovin p��mek.</p>

<h2>Z�v�r</h2>

<p>... a nyn� byste m�li rozum�t alespo� z�klad�m analytick� geometrie. My jsme toto u�ivo brali ve �kole skoro �tvrt roku, tak�e tento �l�nek berte jako stru�n�!!! v�tah. Pokud se v�m zd�, �e je v�e jakoby useknut�, m�te pravdu. Jak to tak b�v�, skon�il �koln� rok a na za��tku dal��ho u� se nepokra�ovalo. Tak� mus�m poznamenat, �e ke ka�d�ho t�matu m�me v se�it� n�kolik p��klad�, ale opisovat je sem by se s nejv�t�� pravd�podobnost� stalo moj� no�n� m�rou - d�kuji za pochopen�... nebo jinak �e�eno: Bu�te r�di za to, co tady m�te, psal jsem to skoro t�den, �ty�i hodiny denn� :-)</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<?
include 'p_end.php';
?>
