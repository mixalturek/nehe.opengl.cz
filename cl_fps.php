<?
$g_title = 'CZ NeHe OpenGL - FPS: Konstantn� rychlost animace';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>FPS: Konstantn� rychlost animace</h1>

<p class="nadpis_clanku">FPS je zkratka z po��te�n�ch p�smen slov Frames Per Second, kter� by se dala do �e�tiny p�elo�it jako po�et sn�mk� za sekundu. Tato t�i p�smena jsou sp�sou p�i spou�t�n� program� na r�zn�ch po��ta��ch. Vezm�te si hru, kterou program�tor za��te�n�k vyv�j� doma na sv�m po��ta�i o rychlosti, �ekn�me, Pentium II. D� ji kamar�dovi, aby se na ni pod�val a zhodnotil. Kamar�d m� doma P4, spust� ji a v�e je ��len� rychl�. D�ky FPS se toto nikdy nestane, na jak�mkoli po��ta�i p�jde hra v�dy stejn� rychle.</p>

<p>Z�kladem v�eho je �as, kter� ub�hl mezi jednotliv�mi pr�chody renderovac� funkc�. Uvedu p��klad. M�me raketu, kter� m� let�t �ekn�me 50 jednotek za sekundu. V�me, �e mezi t�mto a p�edchoz�m vykreslen�m ub�hlo 0,1s, tak�e ji posuneme o 5 jednotek. T�mto zp�sobem budou posuny objekt� v animaci za ur�it� �as v�dy konstantn�, nez�vis� na rychlosti po��ta�e ani aktu�ln� z�t�i procesoru.</p>

<p>V�sledkem cel�ho tohoto �l�nku bude velmi jednoduch� t��da, kter� v�echny pot�ebn� operace implementuje a uk�zka jej�ho pou�it�.</p>

<p>T��da FPS obsahuje t�i atributy: �as p�i minul�m pr�chodu vykreslovac� funkc�, aktu�ln� �as a hodnotu FPS. O aktualizaci prom�nn�ch se star� funkce Vypocet(), kter� se mus� volat stejn� �asto jako p�ekreslen� sc�ny, nejl�pe p��mo ve funkci, kter� ho m� na starosti. GetFPS() je vlo�eno z d�vodu zapouzd�enosti dat.</p>

<p class="src0">class FPS<span class="kom">// T��da FPS</span></p>
<p class="src0">{</p>
<p class="src0">private:</p>
<p class="src1">unsigned int stary_cas;</p>
<p class="src1">unsigned int aktualni_cas;</p>
<p class="src1">double fps;<span class="kom">// Po�et sn�mk� za sekundu</span></p>
<p class="src0">public:</p>
<p class="src1">void Vypocet();<span class="kom">// Volat p�ed ka�d�m p�ekreslen�m sc�ny</span></p>
<p class="src"></p>
<p class="src1">inline double GetFPS();<span class="kom">// Kv�li zapouzd�enosti dat</span></p>
<p class="src1">{</p>
<p class="src2">return fps;</p>
<p class="src1">}</p>
<p class="src0">};</p>

<p>Pro z�sk�n� aktu�ln�ho �asu m��eme pou��t libovolnou funkci. Ve Windows se v�t�inou pou��v� GetTickCount(), kter� vrac� po�et milisekund od spu�t�n� syst�mu. Proto�e jsem za�al p�i programov�n� pou��vat knihovnu SDL (Umo��uje snadn� portov�n� aplikace do r�zn�ch opera�n�ch syst�m� - Linux, Win, Mac OS, BeOS, FreeBSD...), pou�iji v tomto �l�nku SDL_GetTicks(), kter� vrac� po�et milisekund od inicializace SDL. V�po�et FPS je velmi jednoduch�. P�evedeme rozd�l �as� na sekundy a vyd�l�me j�m jedni�ku, kter� reprezentuje jedno vykreslen�. Na konci p�i�ad�me star�mu �asu aktu�ln� �as.</p>

<p class="src0">void FPS::Vypocet()</p>
<p class="src0">{</p>
<p class="src1">aktualni_cas = SDL_GetTicks();<span class="kom">// Vr�t� po�et milisekund od inicializace SDL</span></p>
<p class="src1"><span class="kom">// aktualni_cas = GetTickCount();// Specifick� pro Windows</span></p>
<p class="src"></p>
<p class="src1">fps = 1.0 / ((aktualni_cas - stary_cas) / 1000.0);<span class="kom">// Po�et sn�mk� za sekundu</span></p>
<p class="src1">stary_cas = aktualni_cas;<span class="kom">// Pro dal�� pr�chod</span></p>
<p class="src0">}</p>

<p>Jak pou��t tuto t��du? �ekn�me, �e v OpenGL pot�ebujeme krychli, kter� rotuje o 45� za sekundu. P�i ka�d� aktualizaci �hlu nato�en� k n�mu p�i�teme pln�ch 45�, kter� ale mus�me vyd�lit hodnotou FPS. Jednotliv� posuny se rozf�zuj� tak, aby za sekundu byla krychle nato�en� o po�adovan�ch 45�.</p>

<p class="src0">FPS fps;<span class="kom">// Objekt t��dy</span></p>
<p class="src0">double uhel = 0.0;<span class="kom">// �hel nato�en� krychle</span></p>
<p class="src"></p>
<p class="src0">void DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">fps.Vypocet();<span class="kom">// Aktualizace FPS</span></p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslated(0.0, 0.0, -6.0);<span class="kom">// Posun do hloubky</span></p>
<p class="src1">glRotated(uhel, 0.0, 1.0, 0.0);<span class="kom">// Oto�� krychli v z�vislosti na FPS</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslen� krychle</span></p>
<p class="src"></p>
<p class="src1">uhel += 45.0 / fps.GetFPS();<span class="kom">// Aktualizace �hlu nato�en� v z�vislosti na FPS</span></p>
<p class="src0">}</p>

<p>Je nutn� poznamenat, �e nic nen� id�ln�. Pokud by jedno vykreslen� bylo tak n�ro�n�, �e by trvalo, p�e�enu, deset sekund, rychlost by sice z�stala na konstantn�ch 45� za sekundu, ale vykreslovalo by se a� po cel�ch deseti sekund�ch (FPS = 0,1). Dovedete si p�edstavit to trh�n�?! Obecn� se ��k�, �e by FPS nikdy nem�lo klesnout pod 30. Nic n�m nebr�n�, abychom vlo�ili za v�po�et FPS podm�nku if(fps.GetFPS < 30) sni� kvalitu renderingu; Mohlo by j�m b�t nap��klad zm�na kvality textur GL_LINEAR na GL_NEAREST, vypnut� antialiasingu, odstran�n� n�kter�ch efekt� a podobn�.</p>

<p>FPS v�ak nen� jedinou mo�nost�, jak zajistit plynulost animace. M��eme m�t syst�mov� timer (ve Windows zpr�va WM_TIMER), kter� periodicky vol� renderovac� funkci. Tuto techniku osobn� pou��v�m jen kdy� nen� ��dn� jin� mo�nost (v�dy je jin� mo�nost), proto�e automatick� vykreslov�n� v hlavn� smy�ce je v�dy rychlej�� a spolehliv�j��. Pokud nastav�te timer na moc kr�tk� �as (cca. 50 ms a m�n�), budou se v�cen�sobn� poslan� zpr�vy mazat. Aby nedo�lo k zahlcen� fronty zpr�v, syst�m do n� nikdy neum�st� v�ce ne� jednu zpr�vu WM_TIMER. Ostatn� se neberou v �vahu, jako by se v�bec neposlali.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojov�ch k�d�</li>
</ul>

<?
include 'p_end.php';
?>
