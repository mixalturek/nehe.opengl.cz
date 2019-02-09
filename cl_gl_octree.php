<?
$g_title = 'CZ NeHe OpenGL - Octree';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Octree</h1>

<p class="nadpis_clanku">Octree (octal tree, oktalový strom) je zpùsob rozdìlování 3D prostoru na oblasti, který umo¾òuje vykreslit pouze tu èást svìta/levelu/scény, která se nachází ve výhledu kamery, a tím znaènì urychlit rendering. Mù¾e se také pou¾ít k detekcím kolizí.</p>

<h3>Popis octree</h3>

<p>Pøíklad, proè je rozdìlování prostoru tak nezbytné... Pøedpokládejme, ¾e pro hru vytváøíme kompletní 3D svìt, který se skládá z více jak
100 000 polygonù. Kdybychom je pøi ka¾dém pøekreslení scény v cyklu posílali na grafickou kartu úplnì v¹echny, FPS by zcela urèitì kleslo na absolutní minimum a aplikace byla extrémnì trhaná. Na absolutnì nejnovìj¹ích kartách by to nemuselo být zase tak stra¹né, ale proè se omezovat jen na u¾ivatele, kteøí si mohou dovolit grafickou kartu za deset tisíc a více? Nìkdy, dokonce i kdy¾ máte opravdu rychlou grafickou kartu, mù¾e hodnì zpomalovat samotný cyklus posílající data. Nena¹la by se nìjaká cesta, jak renderovat pouze polygony ve výhledu kamery? To je právì nejvìt¹í výhodou octree - umo¾òuje RYCHLE najít viditelné polygony a vykreslit je.</p>


<h3>Jak octree pracuje</h3>

<p>Octree pracuje pomocí krychlí. Zaèínáme koøenovým uzlem (root node), co¾ je krychle, její¾ stìny jsou rovnobì¾né se souøadnicovými osami a která v sobì zahrnuje kompletnì celý svìt, level nebo scénu. Kdy¾ si okolo celého herního svìta pøedstavíte velkou neviditelnou krychli, urèitì se nezmýlíte.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/0.jpg" width="210" height="175" alt="Koøenový uzel" /></div>

<p>Koøenový uzel ukládá v¹echny vertexy celého svìta. Vzato kolem a kolem, toto je nám zatím naprosto k nièemu, tak¾e ho zkusíme rozdìlit na osm men¹ích (odsud slovo octree - octal tree), které ji budou kompletnì vyplòovat.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/1.jpg" width="210" height="190" alt="První dìlení" /></div>

<p>Prvním dìlením jsme rozdìlili svìt na osm men¹ích èástí. Umíte si pøedstavit, kolik jich bude po dvou, tøech nebo ètyøech dìleních? Ze svìta se stane spousta malých krychlièek. Ale k èemu to vlastnì je, kam zmizel ten nárùst rychlosti u vykreslování? Pøedstavte si, ¾e se kamera nachází pøesnì ve støedu svìta a v zábìru má pravý dolní roh. z linek je jasnì patrnì, ¾e jsou vidìt pouze ètyøi z osmi uzlù v octree. Jedná se o dvì horní a dvì spodní zadní krychle. Z toho v¹eho vyplývá, ¾e staèí vykreslit pouze vertexy ulo¾ené v tìchto ètyøech uzlech.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/2.jpg" width="320" height="200" alt="Pohled do pravého zadního rohu" /></div>

<p>Ale jak zjistit, které uzly pùjdou vidìt a které ne? Budete se divit, ale odpovìï je velice snadná - frustum culling. Staèí získat rozmìry výhledu kamery a potom otestovat ka¾dou krychli, jestli ho protíná popø. le¾í celá uvnitø. Pokud ano, vykreslíme v¹echny vertexy, které jsou pøiøazeny tomuto uzlu. V pøíkladu vý¹e jsme získali nárùst výkonu o 50% a to jsme dìlili pouze jednou. Èím více dìlení bude, tím dosáhneme vìt¹í pøesnosti (na bod). Samozøejmì musíme vytvoøit optimální poèet uzlù, proto¾e pøespøíli¹ rekurzivních testù by zpomalovalo mo¾ná více, ne¾ kdyby nebyly ¾ádné. Zkusme rozdìlit ka¾dou z dosavadních osmi krychlí na dal¹ích &quot;osm&quot;.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/3.jpg" width="215" height="190" alt="Dal¹í úroveò dìlení" /></div>

<p>Urèitì jste si v¹imli zmìny oproti minulému dìlení. V této úrovni u¾ je zbyteèná VYTVÁØET v ka¾dé z osmi pùvodních krychlí dal¹ích OSM, horní a spodní èást mù¾e zùstat nerozdìlena. V¾dy zkou¹íme rozdìlit uzel na osm dal¹í, ale pokud se u¾ v této oblasti nevyskytují ¾ádné trojúhelníky, ignorujeme ji a ani u¾ pro ni nealokujeme ¾ádnou dal¹í pamì». Èím více prostor rozdìlujeme, tím více uzly kopírují originální svìt. Na následujících obrázcích jsou vyobrazeny dvì koule, ka¾dá na opaèné stranì. Po prvním dìlení se koøenový uzel rozdìlí pouze na dva poduzly, ne na osm. Po dal¹ích dìleních krychle kopírují objekty mnohem více. Nové uzly se vytváøejí pouze tehdy, jsou-li potøeba, v daném prostoru se musí nacházet objekty.</p>

<div class="okolo_img">
<img src="images/clanky/cl_gl_octree/4.jpg" width="304" height="304" alt="Dal¹í úrovnì dìlení" />
<img src="images/clanky/cl_gl_octree/5.jpg" width="304" height="304" alt="Dal¹í úrovnì dìlení" />
</div>


<h3>Kdy ukonèit dìlení</h3>

<p>Nyní u¾ byste mìli chápat, jak dìlení pracuje, ale je¹tì nevíte, kdy ho zastavit, aby rekurze neprobíhala do nekoneèna. Existují tøi základní zpùsoby.</p>

<p>První spoèívá v ukonèení rozdìlování uzlu, pokud je poèet jeho trojúhelníkù men¹í ne¾ maximální poèet (napøíklad sto). Pokud jich bude ménì, ukonèíme dìlení a v¹echny zbývající trojúhelníky pøiøadíme tomuto uzlu. Z toho plyne, ¾e trojúhelníky obsahují VÝHRADNÌ koncové uzly. Po rozdìlení na dal¹í úroveò nepøiøadíme trojúhelníky rodièovskému uzlu, ale jeho potomkùm pøípadnì a¾ potomkùm jeho potomkù atd.</p>

<p>Dal¹í mo¾ností, jak ukonèit rekurzi, je definovat urèitou maximální úroveò dìlení. Mù¾eme si napøíklad zvolit maximální hloubku rekurze deset a po jejím pøesa¾ení pøiøadíme zbývající trojúhelníky koncovým uzlùm.</p>

<p>Tøetím zpùsobem je test maximálního poètu uzlù, mù¾e jich být napø. 500. Pøed ka¾dým vytvoøením nového uzlu, inkrementujeme èítaè jejich poètu a otestujeme, jestli je jejich celkový poèet vìt¹í ne¾ 500. Pokud ano, dìlit dále nebudeme a pøiøadíme koncovým uzlùm v¹echny zbývající trojúhelníky.</p>

<p>Osobnì pou¾ívám kombinaci první a tøetí metody, ale pro zaèátek mù¾e být dobrým nápadem zkusit první a druhou, tak¾e budete moci testovat rùzné úrovnì dìlení vizuálnì i manuálnì.</p>


<h3>Jak vykreslit octree</h3>

<p>Jakmile octree jednou vytvoøíme, máme mo¾nost vykreslovat pouze uzly, které se nacházejí ve výhledu kamery. Poèet trojúhelníkù v uzlu jsme se sna¾ili co nejvíce minimalizovat, proto¾e musíme vykreslit i èásteènì viditelné krychle - kvùli pøesahujícímu ro¾ku renderovat tisíce trojúhelníkù. V¾dy zaèínáme od koøenového uzlu, pro ka¾dý, na ni¾¹í úrovni, máme ulo¾eny souøadnice jeho støedu a ¹íøku. Tato organizace dat se skvìle hodí pro pøedání do funkce jako</p>

<p class="src0">bool CubeInFrustum(float x, float y, float z, float size);<span class="kom">// Je krychle viditelná?</span></p>

<p>..., která vrátí true nebo false podle toho, jestli by krychle po vykreslení byla vidìt nebo ne. Pokud ano, stejným zpùsobem otestujeme v¹echny její poduzly, v opaèném pøípadì ignorujeme celou vìtev stromu. Testy provádíme rekurzivnì a¾ po koncové uzly, u nich¾ v pøípadì viditelnosti vykreslíme v¹echny vertexy, které obsahují. Opìt opakuji, ¾e vertexy jsou ulo¾eny pouze v koncových uzlech. Obrázek dole ukazuje prùchod skrz dvouúrovòový octree. Èervené obdélníky pøedstavují viditelné uzly, bílé nejsou vidìt.</p>

<p>Na první pohled dojdeme k závìru, ¾e èím více úrovní vytvoøíme, tím ménì trojúhelníkù budeme muset renderovat. Pokud by trojúhelníky byly rovnomìrnì rozlo¾ené, pøi aktuálním pohledu kamery by se jich u koøenového uzlu vykreslovalo 100 procent (jeden z jednoho uzlu), v první úrovni 62 procent (pìt z osmi uzlù) a v poslední úrovni 28 procent (devìt z 32 uzlù; respektive z 64, ale polovina neobsahovala trojúhelníky, tak¾e nebyly vytvoøeny).</p>

<p>Tato èísla ale berte s velkou rezervou, proto¾e zále¾í na pozici a natoèení kamery. Pokud by byl v zábìru kompletnì celý svìt (pohled z dostateènì vzdáleného externího bodu), nejen, ¾e by nedocházelo k ¾ádné rychlostní optimalizaci, ale kvùli spoustì dodateèných výpoètù by se celý rendering výraznì zpomalil - èím více uzlù, tím více &quot;zbyteèných&quot; testù, které neodstraní ¾ádné trojúhelníky. Nicménì, abych vás nestra¹il, neznám hru, která by umo¾òovala opustit herní svìt a dívat se z venku, tak¾e se s nejvìt¹í pravdìpodobností v¾dy nìjaké uzly oøe¾ou. Nìkdy jich bude více jindy ménì.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_octree/6.jpg" width="320" height="200" alt="Vykreslení octree" /></div>

<h3>Kolize s octree</h3>

<p>Octree sice primárnì slou¾í pro rendering, ale stejnì tak dobøe se mù¾e pou¾ít i pro detekci kolizí. Programové techniky kolizí se li¹í od hry ke høe, tak¾e asi budete chtít naprogramovat vlastní algoritmus, který bude va¹emu enginu plnì vyhovovat. Základem je funkce, která z octree vrátí v¹echny vertexy nacházející se v blízkosti pøedaného bodu. Mìl by jím být støed postavy nebo objektu. Potí¾e nastanou v blízkosti okraje uzlu, kde objekt bez problémù prochází skrz netestované vertexy ze sousedního. Øe¹ení je opìt hned nìkolik. Spolu s bodem mù¾eme pøedat buï polomìr nebo bounding box a potom zjistit kolize s uzly v octree. V¹e závisí na tvaru testovaného objektu. Pøíklad nìkolika funkèních prototypù:</p>

<p class="src0">CVector3* GetVerticesFromPoint(float x, float y, float z);<span class="kom">// Vertexy z uzlu octree</span></p>
<p class="src0">CVector3* GetVerticesFromPointAndRadius(float x, float y, float z, float radius);<span class="kom">// Vertexy z kulové oblasti</span></p>
<p class="src0">CVector3* GetVerticesFromPointAndCube(float x, float y, float z, float size);<span class="kom">// Vertexy k krychlové oblasti</span></p>

<p>Jsem si jistý, ¾e vás právì napadly i mnohem lep¹í zpùsoby kolizí, ale na zaèátku se snadnìji implementují jednoduché techniky. Jakmile jednou máte k dispozici vertexy v dané oblasti, mù¾ete provést mnohem pøesnìj¹í výpoèty. Je¹tì jednou... kdy¾ pøijde na rùzné typy kolizí, v¾dy nejvíce zále¾í na tom, jak aplikace pracuje.</p>

<p class="autor">napsal: Ben Humphrey - DigiBen <?VypisEmail('digiben@gametutorials.com');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?>, 12.07.2004</p>

<p>Anglický originál èlánku: <?OdkazBlank('http://www.gametutorials.com/Tutorials/OpenGL/Octree.htm');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li>Najdete na <?OdkazBlank('http://www.gametutorials.com/');?> v sekci Tutorials -&gt; OpenGL</li>
<li>Frustum culling je dostupný na stejné adrese, byl popsán také v <?OdkazWeb('tut_44', 'NeHe Tutoriálu 44');?></li>
</ul>

<?
include 'p_end.php';
?>
