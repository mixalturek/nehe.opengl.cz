<?
$g_title = 'CZ NeHe OpenGL - P��mka ve 2D';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>P��mka ve 2D</h1>

<p class="nadpis_clanku">Radom�r Vr�na m� po��dal o radu, jak vypo��tat pr�se��k dvou 2D p��mek. Rozhodl jsem se, �e mu m�sto obecn�ch matematick�ch vzorc� po�lu rovnou kompletn� C++ k�d. Nicm�n� mi trochu p�erostl p�es hlavu, a tak vznikla kompletn� t��da p��mky v obecn�m tvaru. Krom� pr�se��ku um� ur�it i jejich vz�jemnou polohu (rovnob�n�, kolm�...), �hel, kter� sv�raj� nebo vzd�lenost libovoln�ho bodu od p��mky. Douf�m, �e tento m�j drobn� �let nebude moc vadit :-]</p>

<p>K�d rozd�l�me klasicky na hlavi�kov� a implementa�n� soubor t��dy, za�neme hlavi�kov�m. Aby nenastaly probl�my p�i v�cen�sobn�m inkludov�n�, nadefinujeme symbolickou konstantu __PRIMKA2D_H__ a p�ed vlastn� definic� otestujeme, jestli u� existuje. Pokud ano, instrukce preprocesoru #ifndef zajist�, �e se tento soubor nebude zpracov�vat dvakr�t.</p>

<p class="src0">#ifndef __PRIMKA2D_H__</p>
<p class="src0">#define __PRIMKA2D_H__</p>
<p class="src"></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Matematick� knihovna</span></p>

<p>P�edpokl�d�m, �e u� zn�te rovnici p��mky v obecn�m tvaru, ale pro ty z v�s, kte�� je�t� nejsou na st�edn� �kole (konec druh�ho ro�n�ku), ji zkus�m alespo� nazna�it.</p>

<p>Obecn� rovnice p��mky je vyj�d�ena ve tvaru a*x + b*y + c = 0. Pokud za x, y dosad�me sou�adnice libovoln�ho 2D bodu a vyjde n�m nula, m�me jistotu, �e tento bod na p��mce le��. Konstanty a, b p�edstavuj� norm�lov� vektor p��mky (vektor, kter� je k p��mce kolm�). c je tak� konstanta, ur�� se v�po�tem p�i dosazen� bodu do rovnice. Krom� obecn�ho tvaru existuj� i dal�� vz�jemn� zam�niteln� tvary - nap��klad parametrick� a sm�rnicov�.</p>

<p>Mysl�m, �e koment��e v�e vysv�tluj�...</p>

<p class="src0">class WPrimka2D<span class="kom">// T��da 2D p��mky v obecn�m tvaru</span></p>
<p class="src0">{</p>
<p class="src0">private:</p>
<p class="src1">double a, b, c;<span class="kom">// Obecn� rovnice p��mky a*x + b*y + c = 0</span></p>
<p class="src"></p>
<p class="src0">public:</p>
<p class="src1">WPrimka2D();<span class="kom">// Konstruktor</span></p>
<p class="src1">~WPrimka2D();<span class="kom">// Destruktor</span></p>
<p class="src1">WPrimka2D(const WPrimka2D& primka);<span class="kom">// Kop�rovac� konstruktor</span></p>
<p class="src1">WPrimka2D(double a, double b, double c);<span class="kom">// P��m� zad�n� prom�nn�ch</span></p>
<p class="src1">WPrimka2D(double x1, double y1, double x2, double y2);<span class="kom">// P��mka ze dvou bod�</span></p>
<p class="src"></p>
<p class="src1">void Create(double a, double b, double c);<span class="kom">// P��m� zad�n� prom�nn�ch</span></p>
<p class="src1">void Create(double x1, double y1, double x2, double y2);<span class="kom">// P��mka ze dvou bod�</span></p>
<p class="src"></p>
<p class="src1">inline double GetA() { return a; }<span class="kom">// Z�sk�n� atribut�</span></p>
<p class="src1">inline double GetB() { return b; }</p>
<p class="src1">inline double GetC() { return b; }</p>
<p class="src"></p>
<p class="src1">bool operator==(WPrimka2D&amp; primka);<span class="kom">// Spl�vaj�c� p��mky?</span></p>
<p class="src1">bool operator!=(WPrimka2D&amp; primka);<span class="kom">// Nespl�vaj�c� p��mky?</span></p>
<p class="src"></p>
<p class="src1">bool JeNaPrimce(double x, double y);<span class="kom">// Le�� bod na p��mce?</span></p>
<p class="src1">bool JsouRovnobezne(WPrimka2D&amp; primka);<span class="kom">// Jsou p��mky rovnob�n�?</span></p>
<p class="src1">bool JsouKolme(WPrimka2D&amp; primka);<span class="kom">// Jsou p��mky kolm�?</span></p>
<p class="src"></p>
<p class="src1">bool Prusecik(WPrimka2D&amp; primka, double&amp; retx, double&amp; rety);<span class="kom">// Pr�se��k p��mek</span></p>
<p class="src1">double Uhel(WPrimka2D&amp; primka);<span class="kom">// �hel p��mek (v radi�nech)</span></p>
<p class="src1">double VzdalenostBodu(double x, double y);<span class="kom">// Vzd�lenost bodu od p��mky</span></p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">#endif</p>

<p>Hlavi�kov� soubor je za n�mi. Za�neme implementovat jednotliv� metody. V obecn�m konstruktoru nastav�me v�echny vlastnosti na nulu, destruktor nech�me pr�zdn�.</p>

<p class="src0">#include &quot;primka2d.h&quot;<span class="kom">// Hlavi�kov� soubor</span></p>
<p class="src"></p>
<p class="src0">WPrimka2D::WPrimka2D()<span class="kom">// Obecn� konstruktor</span></p>
<p class="src0">{</p>
<p class="src1">a = 0.0;<span class="kom">// Nulov�n�</span></p>
<p class="src1">b = 0.0;</p>
<p class="src1">c = 0.0;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">WPrimka2D::~WPrimka2D()<span class="kom">// Destruktor</span></p>
<p class="src0">{</p>
<p class="src"></p>
<p class="src0">}</p>

<p>Kop�rovac� konstruktor...</p>

<p class="src0">WPrimka2D::WPrimka2D(const WPrimka2D& primka)<span class="kom">// Kop�rovac� konstruktor</span></p>
<p class="src0">{</p>
<p class="src1">a = primka.a;</p>
<p class="src1">b = primka.b;</p>
<p class="src1">c = primka.c;</p>
<p class="src0">}</p>

<p>Abychom mohli inicializovat t��du u� p�i jej�m vytvo�en�, p�et��me konstruktor. Kdykoli v programu ho m��e nahradit metoda Create().</p>

<p class="src0">WPrimka2D::WPrimka2D(double a, double b, double c)<span class="kom">// P��m� zad�n� prom�nn�ch</span></p>
<p class="src0">{</p>
<p class="src1">Create(a, b, c);</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">void WPrimka2D::Create(double a, double b, double c)<span class="kom">// P��m� zad�n� prom�nn�ch</span></p>
<p class="src0">{</p>
<p class="src1">this-&gt;a = a;</p>
<p class="src1">this-&gt;b = b;</p>
<p class="src1">this-&gt;c = c;</p>
<p class="src0">}</p>

<p>�tvrt� konstruktor um� vytvo�it p��mku ze dvou bod�, op�t ho m��e nahradit funkce Create(). Jak jsem nazna�il v��e, prom�nn� a, b p�edstavuj� norm�lov� vektor p��mky. Sm�rov� vektor by se z�skal jednoduch�m ode�ten�m koncov�ho bodu od po��te�n�ho. Vytvo�en� norm�lov�ho vektoru je podobn�, ale nav�c prohod�me slo�ky vektoru a u jedn� invertujeme znam�nko.</p>

<p>Rad�ji p��klad. M�me dva body [1; 2] a [4; 3], sm�rov� vektor se z�sk� ode�ten�m koncov�ho bodu od po��te�n�ho, nicm�n� p�i vytv��en� p��mky je �pln� jedno, kter� pova�ujeme za po��te�n� a kter� za koncov�. Prvn� bod bude nap��klad po��te�n� a druh� koncov�. Sm�rov� vektor je tedy s = (4-1, 3-2) = (3; 1). Norm�lov� vektor m� prohozen� po�ad� slo�ek a u jedn� opa�n� znam�nko. n = (-1; 3) nebo (1; -3).</p>

<p>Pro �plnost: je naprosto jedno, zda vezmeme p��mo vypo�ten� vektor nebo jeho k-n�sobek. Oba vektory uveden� v minul�m odstavci jsou k-n�sobkem toho druh�ho (k = -1). Stejn� tak bychom mohli vykr�tit vektor (5; 10) na (1; 2). Z toho plyne, �e jedna p��mka m��e b�t k-n�sobkem druh� - viz. d�le.</p>

<p class="src0">WPrimka2D::WPrimka2D(double x1, double y1, double x2, double y2)<span class="kom">// P��mka ze dvou bod�</span></p>
<p class="src0">{</p>
<p class="src1">Create(x1, y1, x2, y2);</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">void WPrimka2D::Create(double x1, double y1, double x2, double y2)</p>
<p class="src0">{</p>
<p class="src1">if(x1 == x2 &amp;&amp; y1 == y2)<span class="kom">// 2 stejn� body netvo�� p��mku</span></p>
<p class="src1">{</p>
<p class="src2">Create(0.0, 0.0, 0.0);<span class="kom">// Platn� hodnoty</span></p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">a = y2 - y1;</p>
<p class="src1">b = x1 - x2;</p>

<p>Prom�nnou c vypo�teme dosazen�m jednoho bodu (v na�em p��pad� prvn�ho) do zat�m ne�pln� rovnice. V z�kladn� rovnici a*x + b*y + c = 0 p�esuneme v�echno krom� c na pravou stranu a z�sk�me c = -a*x -b*y.</p>

<p class="src1">c = -a*x1 -b*y1;</p>
<p class="src0">}</p>

<p>Zda bod le�� na p��mce, zjist�me dosazen�m jeho sou�adnic do rovnice p��mky. Pokud se v�sledek rovn� nule, le�� na n�.</p>

<p class="src0">bool WPrimka2D::JeNaPrimce(double x, double y)<span class="kom">// Le�� bod na p��mce?</span></p>
<p class="src0">{</p>
<p class="src1">if(a*x + b*y + c == 0.0)<span class="kom">// Dosazen� sou�adnic do rovnice</span></p>
<p class="src1">{</p>
<p class="src2">return true;</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jestli jsou p��mky stejn� (spl�vaj�c�) se zjist� porovn�n�m jejich slo�ek, ale nav�c mus�me vz�t v �vahu i k-n�sobky. Nebudeme tedy porovn�vat p��mo vnit�n� prom�nn�, ale m�sto toho vypo�teme pom�ry a/a, b/b a c/c. Budou-li tyto pom�ry vnit�n�ch prom�nn�ch stejn�, je jasn�, �e se jedn� se o jednu a tu samou p��mku.</p>

<p class="src0">bool WPrimka2D::operator==(WPrimka2D&amp; primka)<span class="kom">// Jsou p��mky spl�vaj�c�?</span></p>
<p class="src0">{</p>
<p class="src1">double ka = a / primka.a;<span class="kom">// Nesta�� pouze zkontrolovat hodnoty, primka m��e b�t k-n�sobkem</span></p>
<p class="src1">double kb = b / primka.b;</p>
<p class="src1">double kc = c / primka.c;</p>
<p class="src"></p>
<p class="src1">if(ka == kb &amp;&amp; ka == kc)<span class="kom">// Mus� b�t stejn�</span></p>
<p class="src1">{</p>
<p class="src2">return true;<span class="kom">// Spl�vaj�c� p��mky</span></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">return false;<span class="kom">// Dv� r�zn� p��mky</span></p>
<p class="src1">}</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">bool WPrimka2D::operator!=(WPrimka2D&amp; primka)<span class="kom">// Nejsou p��mky spl�vaj�c�?</span></p>
<p class="src0">{</p>
<p class="src1">return !(*this == primka);<span class="kom">// Negace porovn�n�</span></p>
<p class="src0">}</p>

<p>Zji�t�n�, jestli jsou p��mky rovnob�n�, je velmi podobn� oper�toru porovn�n�. Maj�-li stejn� norm�lov� vektor, pop�. vektor jedn� je k-n�sobkem druh�, jsou rovnob�n�. T�et� prom�nnou, c, nemus�me a vlastn� ani nesm�me testovat.</p>

<p class="src0">bool WPrimka2D::JsouRovnobezne(WPrimka2D&amp; primka)<span class="kom">// Jsou p��mky rovnob�n�?</span></p>
<p class="src0">{</p>
<p class="src1">double ka = a / primka.a;<span class="kom">// Nesta�� zkontrolovat hodnoty, p m��e b�t k-n�sobkem</span></p>
<p class="src1">double kb = b / primka.b;</p>
<p class="src"></p>
<p class="src1">if(ka == kb)<span class="kom">// Mus� b�t stejn�</span></p>
<p class="src1">{</p>
<p class="src2">return true;</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Kolmost dvou p��mek se nejjednodu�eji odhal� tak, �e se jedna z nich nato�� o 90 stup�� a otestuje se jejich rovnob�nost - pro� to zbyte�n� komplikovat...</p>

<p class="src0">bool WPrimka2D::JsouKolme(WPrimka2D&amp; primka)<span class="kom">// Jsou p��mky kolm�?</span></p>
<p class="src0">{</p>
<p class="src1">WPrimka2D pom(-primka.b, primka.a, primka.c);<span class="kom">// P��mka s kolm�m vektorem</span></p>
<p class="src"></p>
<p class="src1">return JsouRovnobezne(pom);</p>
<p class="src0">}</p>

<p>Dost�v�me se k podstat� cel�ho �l�nku - pr�se��k dvou p��mek. Nejd��ve otestujeme jestli se nejedn� o dv� spl�vaj�c� p��mky, pokud ano, maj� nekone�n� mnoho spole�n�ch bod�. Nejsou-li spl�vaj�c�, mohou b�t je�t� rovnob�n�, pak nemaj� ��dn� spole�n� bod. Ve v�ech ostatn�ch p��padech maj� pouze jeden spole�n� bod a t�m je pr�se��k. Proto�e mus� vyhovovat sou�asn� ob�ma rovnic�m, �e��me soustavu dvou rovnic o dvou nezn�m�ch x a y.</p>

<div class="okolo_img"><img src="images/clanky/primka2d.gif" width="175" height="153" alt="V�po�et pr�se��ku" /></div>

<p>Pokud funkce vr�t� true, byl pr�se��k nalezen, sou�adnice ulo��me do referenc� retx a rety. False indikuje bu� ��dn� pr�se��k (rovnob�n� p��mky), nebo nekone�n� mnoho spole�n�ch bod� (spl�vaj�c� p��mky).</p>

<p class="src0">bool WPrimka2D::Prusecik(WPrimka2D&amp; primka, double&amp; retx, double&amp; rety)<span class="kom">// Pr�se��k p��mek</span></p>
<p class="src0">{</p>
<p class="src1">if(*this == primka)<span class="kom">// P��mky jsou spl�vaj�c� - nekone�n� mnoho spole�n�ch bod�</span></p>
<p class="src1">{</p>
<p class="src2">return false;<span class="kom">// Sp�e by se m�lo vr�tit true a n�jak� bod... z�le�� na pou�it�</span></p>
<p class="src1">}</p>
<p class="src1">else if(JsouRovnobezne(primka))<span class="kom">// P��mky jsou rovnob�n� - ��dn� spole�n� bod</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Jeden spole�n� bod - pr�se��k (vyhovuje sou�asn� ob�ma rovnic�m)</span></p>
<p class="src1">{</p>
<p class="src2">retx = (b*primka.c - c * primka.b) / (a*primka.b - primka.a*b);</p>
<p class="src2">rety = -(a*primka.c - primka.a * c) / (a*primka.b -  primka.a*b);</p>
<p class="src"></p>
<p class="src2">return true;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>�hel dvou p��mek je �hlem dvou sm�rov�ch vektor�, m��eme v�ak pou��t i norm�lov� vektory, proto�e v�sledek bude stejn�. Kosinus �hlu se rovn� zlomku, u kter�ho se v �itateli nach�z� skal�rn� sou�in vektor� (n�sob� se zvlṻ x a zvlṻ y slo�ky) a ve jmenovateli sou�in d�lek vektor� (Pythagorova v�ta). Pokud nech�pete, berte to jako vzorec.</p>

<p class="src0">double WPrimka2D::Uhel(WPrimka2D&amp; primka)<span class="kom">// �hel p��mek</span></p>
<p class="src0">{</p>
<p class="src1">return acos((a*primka.a + b*primka.b) / (sqrt(a*a + b*b) * sqrt(primka.a*primka.a + primka.b*primka.b)));</p>
<p class="src0">}</p>

<p>Vzd�lenost bodu od p��mky je u� trochu slo�it�j��. Vypo�te se rovnice p��mky, kter� je kolm� k zadan� p��mce a proch�z� ur�en�m bodem. Potom se najde pr�se��k t�chto p��mek a vypo�te se vzd�lenost bod�. Cel� tento postup se ale d� mnohon�sobn� zjednodu�it, kdy� si najdete vzorec v matematicko fyzik�ln�ch tabulk�ch :-)</p>

<p class="src0">double WPrimka2D::VzdalenostBodu(double x, double y)<span class="kom">// Vzd�lenost bodu od p��mky</span></p>
<p class="src0">{</p>
<p class="src1">double vzdalenost = (a*x + b*y + c) / sqrt(a*a + b*b);</p>
<p class="src"></p>
<p class="src1">if(vzdalenost &lt; 0.0)<span class="kom">// Absolutn� hodnota</span></p>
<p class="src1">{</p>
<p class="src2">vzdalenost = -vzdalenost;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return vzdalenost;</p>
<p class="src0">}</p>

<p>Abych se ale vr�til na za��tek, p�vodn�m z�m�rem bylo vypo��tat pr�se��k dvou p��mek. S na�� t��dou to nen� nic slo�it�ho...</p>

<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Knihovna pro standardn� vstup a v�stup</span></p>
<p class="src0">#include &quot;primka2d.h&quot;<span class="kom">// T��da p��mky</span></p>
<p class="src"></p>
<p class="src0">int main(int argc, char** argv)<span class="kom">// Vstup do programu</span></p>
<p class="src0">{</p>
<p class="src1">WPrimka2D primka1(3.0, -1.0, 1.0);<span class="kom">// Dv� p��mky</span></p>
<p class="src1">WPrimka2D primka2(1.0, 3.0, -14.0);</p>
<p class="src"></p>
<p class="src1">double prusecik_x, prusecik_y;<span class="kom">// Sou�adnice pr�se��ku</span></p>
<p class="src"></p>
<p class="src1">if(primka1.Prusecik(primka2, prusecik_x, prusecik_y))<span class="kom">// V�po�et pr�se��ku</span></p>
<p class="src1">{</p>
<p class="src2">printf(&quot;Pr�se��k [%f; %f]\n&quot;, prusecik_x, prusecik_y);<span class="kom">// Vyps�n� hodnot</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return 0;</p>
<p class="src0">}</p>

<p>Douf�m, �e se v�m tento �l�nek l�bil. Pokud bude z�jem (napi�te nap�. do Diskuze k tomuto �l�nku), mohu vytvo�it n�co podobn�ho o p��mce ve 3D. Tam ale bode situace o trochu komplikovan�j��, proto�e v trojrozm�rn�m prostoru obecn� rovnice p��mky neexistuje. Budeme si muset vysta�it se soustavou parametrick�ch rovnic.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/primka2d.tar.gz');?> - Linuxov� g++ OK, jin� kompil�tory by m�li b�t tak� pou�iteln�</li>
</ul>

<?
include 'p_end.php';
?>
