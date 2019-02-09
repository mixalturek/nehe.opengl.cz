<?
$g_title = 'CZ NeHe OpenGL - Octree';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Octree</h1>

<p class="nadpis_clanku">Octree (octal tree, oktalov� strom) je zp�sob rozd�lov�n� 3D prostoru na oblasti, kter� umo��uje vykreslit pouze tu ��st sv�ta/levelu/sc�ny, kter� se nach�z� ve v�hledu kamery, a t�m zna�n� urychlit rendering. M��e se tak� pou��t k detekc�m koliz�.</p>

<h3>Popis octree</h3>

<p>P��klad, pro� je rozd�lov�n� prostoru tak nezbytn�... P�edpokl�dejme, �e pro hru vytv���me kompletn� 3D sv�t, kter� se skl�d� z v�ce jak
100 000 polygon�. Kdybychom je p�i ka�d�m p�ekreslen� sc�ny v cyklu pos�lali na grafickou kartu �pln� v�echny, FPS by zcela ur�it� kleslo na absolutn� minimum a aplikace byla extr�mn� trhan�. Na absolutn� nejnov�j��ch kart�ch by to nemuselo b�t zase tak stra�n�, ale pro� se omezovat jen na u�ivatele, kte�� si mohou dovolit grafickou kartu za deset tis�c a v�ce? N�kdy, dokonce i kdy� m�te opravdu rychlou grafickou kartu, m��e hodn� zpomalovat samotn� cyklus pos�laj�c� data. Nena�la by se n�jak� cesta, jak renderovat pouze polygony ve v�hledu kamery? To je pr�v� nejv�t�� v�hodou octree - umo��uje RYCHLE naj�t viditeln� polygony a vykreslit je.</p>


<h3>Jak octree pracuje</h3>

<p>Octree pracuje pomoc� krychl�. Za��n�me ko�enov�m uzlem (root node), co� je krychle, jej� st�ny jsou rovnob�n� se sou�adnicov�mi osami a kter� v sob� zahrnuje kompletn� cel� sv�t, level nebo sc�nu. Kdy� si okolo cel�ho hern�ho sv�ta p�edstav�te velkou neviditelnou krychli, ur�it� se nezm�l�te.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/0.jpg" width="210" height="175" alt="Ko�enov� uzel" /></div>

<p>Ko�enov� uzel ukl�d� v�echny vertexy cel�ho sv�ta. Vzato kolem a kolem, toto je n�m zat�m naprosto k ni�emu, tak�e ho zkus�me rozd�lit na osm men��ch (odsud slovo octree - octal tree), kter� ji budou kompletn� vypl�ovat.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/1.jpg" width="210" height="190" alt="Prvn� d�len�" /></div>

<p>Prvn�m d�len�m jsme rozd�lili sv�t na osm men��ch ��st�. Um�te si p�edstavit, kolik jich bude po dvou, t�ech nebo �ty�ech d�len�ch? Ze sv�ta se stane spousta mal�ch krychli�ek. Ale k �emu to vlastn� je, kam zmizel ten n�r�st rychlosti u vykreslov�n�? P�edstavte si, �e se kamera nach�z� p�esn� ve st�edu sv�ta a v z�b�ru m� prav� doln� roh. z linek je jasn� patrn�, �e jsou vid�t pouze �ty�i z osmi uzl� v octree. Jedn� se o dv� horn� a dv� spodn� zadn� krychle. Z toho v�eho vypl�v�, �e sta�� vykreslit pouze vertexy ulo�en� v t�chto �ty�ech uzlech.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/2.jpg" width="320" height="200" alt="Pohled do prav�ho zadn�ho rohu" /></div>

<p>Ale jak zjistit, kter� uzly p�jdou vid�t a kter� ne? Budete se divit, ale odpov�� je velice snadn� - frustum culling. Sta�� z�skat rozm�ry v�hledu kamery a potom otestovat ka�dou krychli, jestli ho prot�n� pop�. le�� cel� uvnit�. Pokud ano, vykresl�me v�echny vertexy, kter� jsou p�i�azeny tomuto uzlu. V p��kladu v��e jsme z�skali n�r�st v�konu o 50% a to jsme d�lili pouze jednou. ��m v�ce d�len� bude, t�m dos�hneme v�t�� p�esnosti (na bod). Samoz�ejm� mus�me vytvo�it optim�ln� po�et uzl�, proto�e p�esp��li� rekurzivn�ch test� by zpomalovalo mo�n� v�ce, ne� kdyby nebyly ��dn�. Zkusme rozd�lit ka�dou z dosavadn�ch osmi krychl� na dal��ch &quot;osm&quot;.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/3.jpg" width="215" height="190" alt="Dal�� �rove� d�len�" /></div>

<p>Ur�it� jste si v�imli zm�ny oproti minul�mu d�len�. V t�to �rovni u� je zbyte�n� VYTV��ET v ka�d� z osmi p�vodn�ch krychl� dal��ch OSM, horn� a spodn� ��st m��e z�stat nerozd�lena. V�dy zkou��me rozd�lit uzel na osm dal��, ale pokud se u� v t�to oblasti nevyskytuj� ��dn� troj�heln�ky, ignorujeme ji a ani u� pro ni nealokujeme ��dnou dal�� pam�. ��m v�ce prostor rozd�lujeme, t�m v�ce uzly kop�ruj� origin�ln� sv�t. Na n�sleduj�c�ch obr�zc�ch jsou vyobrazeny dv� koule, ka�d� na opa�n� stran�. Po prvn�m d�len� se ko�enov� uzel rozd�l� pouze na dva poduzly, ne na osm. Po dal��ch d�len�ch krychle kop�ruj� objekty mnohem v�ce. Nov� uzly se vytv��ej� pouze tehdy, jsou-li pot�eba, v dan�m prostoru se mus� nach�zet objekty.</p>

<div class="okolo_img">
<img src="images/clanky/cl_gl_octree/4.jpg" width="304" height="304" alt="Dal�� �rovn� d�len�" />
<img src="images/clanky/cl_gl_octree/5.jpg" width="304" height="304" alt="Dal�� �rovn� d�len�" />
</div>


<h3>Kdy ukon�it d�len�</h3>

<p>Nyn� u� byste m�li ch�pat, jak d�len� pracuje, ale je�t� nev�te, kdy ho zastavit, aby rekurze neprob�hala do nekone�na. Existuj� t�i z�kladn� zp�soby.</p>

<p>Prvn� spo��v� v ukon�en� rozd�lov�n� uzlu, pokud je po�et jeho troj�heln�k� men�� ne� maxim�ln� po�et (nap��klad sto). Pokud jich bude m�n�, ukon��me d�len� a v�echny zb�vaj�c� troj�heln�ky p�i�ad�me tomuto uzlu. Z toho plyne, �e troj�heln�ky obsahuj� V�HRADN� koncov� uzly. Po rozd�len� na dal�� �rove� nep�i�ad�me troj�heln�ky rodi�ovsk�mu uzlu, ale jeho potomk�m p��padn� a� potomk�m jeho potomk� atd.</p>

<p>Dal�� mo�nost�, jak ukon�it rekurzi, je definovat ur�itou maxim�ln� �rove� d�len�. M��eme si nap��klad zvolit maxim�ln� hloubku rekurze deset a po jej�m p�esa�en� p�i�ad�me zb�vaj�c� troj�heln�ky koncov�m uzl�m.</p>

<p>T�et�m zp�sobem je test maxim�ln�ho po�tu uzl�, m��e jich b�t nap�. 500. P�ed ka�d�m vytvo�en�m nov�ho uzlu, inkrementujeme ��ta� jejich po�tu a otestujeme, jestli je jejich celkov� po�et v�t�� ne� 500. Pokud ano, d�lit d�le nebudeme a p�i�ad�me koncov�m uzl�m v�echny zb�vaj�c� troj�heln�ky.</p>

<p>Osobn� pou��v�m kombinaci prvn� a t�et� metody, ale pro za��tek m��e b�t dobr�m n�padem zkusit prvn� a druhou, tak�e budete moci testovat r�zn� �rovn� d�len� vizu�ln� i manu�ln�.</p>


<h3>Jak vykreslit octree</h3>

<p>Jakmile octree jednou vytvo��me, m�me mo�nost vykreslovat pouze uzly, kter� se nach�zej� ve v�hledu kamery. Po�et troj�heln�k� v uzlu jsme se sna�ili co nejv�ce minimalizovat, proto�e mus�me vykreslit i ��ste�n� viditeln� krychle - kv�li p�esahuj�c�mu ro�ku renderovat tis�ce troj�heln�k�. V�dy za��n�me od ko�enov�ho uzlu, pro ka�d�, na ni��� �rovni, m�me ulo�eny sou�adnice jeho st�edu a ���ku. Tato organizace dat se skv�le hod� pro p�ed�n� do funkce jako</p>

<p class="src0">bool CubeInFrustum(float x, float y, float z, float size);<span class="kom">// Je krychle viditeln�?</span></p>

<p>..., kter� vr�t� true nebo false podle toho, jestli by krychle po vykreslen� byla vid�t nebo ne. Pokud ano, stejn�m zp�sobem otestujeme v�echny jej� poduzly, v opa�n�m p��pad� ignorujeme celou v�tev stromu. Testy prov�d�me rekurzivn� a� po koncov� uzly, u nich� v p��pad� viditelnosti vykresl�me v�echny vertexy, kter� obsahuj�. Op�t opakuji, �e vertexy jsou ulo�eny pouze v koncov�ch uzlech. Obr�zek dole ukazuje pr�chod skrz dvou�rov�ov� octree. �erven� obd�ln�ky p�edstavuj� viditeln� uzly, b�l� nejsou vid�t.</p>

<p>Na prvn� pohled dojdeme k z�v�ru, �e ��m v�ce �rovn� vytvo��me, t�m m�n� troj�heln�k� budeme muset renderovat. Pokud by troj�heln�ky byly rovnom�rn� rozlo�en�, p�i aktu�ln�m pohledu kamery by se jich u ko�enov�ho uzlu vykreslovalo 100 procent (jeden z jednoho uzlu), v prvn� �rovni 62 procent (p�t z osmi uzl�) a v posledn� �rovni 28 procent (dev�t z 32 uzl�; respektive z 64, ale polovina neobsahovala troj�heln�ky, tak�e nebyly vytvo�eny).</p>

<p>Tato ��sla ale berte s velkou rezervou, proto�e z�le�� na pozici a nato�en� kamery. Pokud by byl v z�b�ru kompletn� cel� sv�t (pohled z dostate�n� vzd�len�ho extern�ho bodu), nejen, �e by nedoch�zelo k ��dn� rychlostn� optimalizaci, ale kv�li spoust� dodate�n�ch v�po�t� by se cel� rendering v�razn� zpomalil - ��m v�ce uzl�, t�m v�ce &quot;zbyte�n�ch&quot; test�, kter� neodstran� ��dn� troj�heln�ky. Nicm�n�, abych v�s nestra�il, nezn�m hru, kter� by umo��ovala opustit hern� sv�t a d�vat se z venku, tak�e se s nejv�t�� pravd�podobnost� v�dy n�jak� uzly o�e�ou. N�kdy jich bude v�ce jindy m�n�.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/6.jpg" width="320" height="200" alt="Vykreslen� octree" /></div>

<h3>Kolize s octree</h3>

<p>Octree sice prim�rn� slou�� pro rendering, ale stejn� tak dob�e se m��e pou��t i pro detekci koliz�. Programov� techniky koliz� se li�� od hry ke h�e, tak�e asi budete cht�t naprogramovat vlastn� algoritmus, kter� bude va�emu enginu pln� vyhovovat. Z�kladem je funkce, kter� z octree vr�t� v�echny vertexy nach�zej�c� se v bl�zkosti p�edan�ho bodu. M�l by j�m b�t st�ed postavy nebo objektu. Pot�e nastanou v bl�zkosti okraje uzlu, kde objekt bez probl�m� proch�z� skrz netestovan� vertexy ze sousedn�ho. �e�en� je op�t hned n�kolik. Spolu s bodem m��eme p�edat bu� polom�r nebo bounding box a potom zjistit kolize s uzly v octree. V�e z�vis� na tvaru testovan�ho objektu. P��klad n�kolika funk�n�ch prototyp�:</p>

<p class="src0">CVector3* GetVerticesFromPoint(float x, float y, float z);<span class="kom">// Vertexy z uzlu octree</span></p>
<p class="src0">CVector3* GetVerticesFromPointAndRadius(float x, float y, float z, float radius);<span class="kom">// Vertexy z kulov� oblasti</span></p>
<p class="src0">CVector3* GetVerticesFromPointAndCube(float x, float y, float z, float size);<span class="kom">// Vertexy k krychlov� oblasti</span></p>

<p>Jsem si jist�, �e v�s pr�v� napadly i mnohem lep�� zp�soby koliz�, ale na za��tku se snadn�ji implementuj� jednoduch� techniky. Jakmile jednou m�te k dispozici vertexy v dan� oblasti, m��ete prov�st mnohem p�esn�j�� v�po�ty. Je�t� jednou... kdy� p�ijde na r�zn� typy koliz�, v�dy nejv�ce z�le�� na tom, jak aplikace pracuje.</p>

<p class="autor">napsal: Ben Humphrey - DigiBen <?VypisEmail('digiben@gametutorials.com');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?>, 12.07.2004</p>

<p>Anglick� origin�l �l�nku: <?OdkazBlank('http://www.gametutorials.com/Tutorials/OpenGL/Octree.htm');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li>Najdete na <?OdkazBlank('http://www.gametutorials.com/');?> v sekci Tutorials -&gt; OpenGL</li>
<li>Frustum culling je dostupn� na stejn� adrese, byl pops�n tak� v <?OdkazWeb('tut_44', 'NeHe Tutori�lu 44');?></li>
</ul>

<?
include 'p_end.php';
?>
