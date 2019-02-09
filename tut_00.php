<?
$g_title = 'CZ NeHe OpenGL - Lekce 0 - Pøedmluva k NeHe Tutoriálùm';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Lekce 0 - Pøedmluva k NeHe Tutoriálùm</h1>

<p class="nadpis_clanku">Je¹tì ne¾ se pustíte do ètení tohoto textu, mìli byste vìdìt, ¾e není souèástí oficiálních (anglických) NeHe Tutoriálù. Napsal ho &quot;pouze&quot; jeden z pøekladatelù, kterému chybìlo nìco na zpùsob trochu ucelenìj¹ího úvodu do tak obrovské problematiky, jakou pøedstavuje programování 3D grafiky, her a ostatnì v¹eho okolo OpenGL.</p>

<p>Kdy¾ jsem zaèínal s NeHe, myslel jsem si, ¾e umím programovat. Je mo¾né, ¾e u vás je to pravda, u mì ale nebyla. Napsal jsem ji¾ sice nìkolik aplikací, ale vìt¹inou se skládaly z bezduché lineární posloupnosti pøíkazù s obèasnými odskoky do if blokù testující návratové hodnoty, pár cykly a podobnì. Opravdu ucelenou mno¾inu funkcí plných mnoha vnoøených cyklù a podmínek, kdy ka¾dá plní svùj pøesnì daný úèel a dohromady tak tvoøí jednotný celek, jsem nikdy nenapsal. Toto mì nauèily a¾ NeHe Tutoriály.</p>

<p>Mo¾ná vám pøi ètení desáté, dvacáté lekce bude pøipadat, ¾e a¾ se dostanete na konec, budete umìt (nejen) o OpenGL naprosto v¹e. Pokud mi slíbíte, ¾e to nikomu neøeknete, prozradím vám jedno malé, ale dùle¾ité tajemství... v NeHe Tutoriálech rozhodnì NENÍ v¹echno. Zpoèátku to tak sice vypadá, ale a¾ doètete poslední tutoriál a ohlédnete se zpìt, zjistíte, ¾e sice umíte vytvoøit OpenGL okno, vykreslit do scény nejrùznìj¹í obrazce, pokrýt scénu neprostupnou mlhou, vrhat stíny, naèítat nejrùznìj¹í formáty obrázkù, loadovat úchvatné 3D modely nebo tvoøit jednoduché hry, ale vìøte nebo ne, je to opravdu jen zaèátek. Stejnì jako snad u èehokoli jiného i zde platí, ¾e èím více vìcí u¾ znáte, tím více jich neustále objevujete pøed sebou.</p>

<p>Po doètení tìch 350, nebo kolik jich vlastnì je, stránek zjistíte, ¾e NeHe byl jen malý úvod. Na druhou stranu vám mohu slíbit, ¾e pokud pøeètete a hlavnì pochopíte vìt¹inu z probíraných témat, budete mít vystavìné ultra pevné ¾elezobetonové základy, které se nikdy a za ¾ádných okolností nezøítí jako vratký domeèek z karet...</p>

<p>Ale dost stra¹ení, nebojte, není to zase úplnì tak hrozné, jak jsem právì napsal. Poèítaèová grafika má tu výhodu, ¾e jakmile pochopíte principy a vytvoøíte si pevné jádro poznatkù, v¹e, co se nauèíte dále, bude (vìt¹inou) jen nabalování dal¹ích funkcí a efektù, které sice mohou být za urèitých okolností naprosto ú¾asné, ale pokud je nepou¾ijete, tak se zase nic tak hrozného nestane.</p>

<p>Po dosa¾ení této úrovnì narazíte na jednu zajímavou vìc a to, ¾e pøi programování tzv. &quot;grafiky&quot; je samotná grafika jen malá èásteèka z moøe dal¹ích a vìt¹inou stejnì nezbytných a mnohem slo¾itìj¹ích vìcí, které ale na první pohled nejsou vidìt. Nezasvìceným lidem vìt¹inou naprosto unikají. Abychom nezùstali jen v teoretické rovinì, uvedu nìkolik praktických pøíkladù.</p>

<p>Vezmìme si tøeba trojúhelník, na který chceme namapovat texturu, co¾ je samo o sobì velice jednoduché. Tedy, abychom byli pøesní, extrémnì slo¾ité, ale jako &quot;blbí programátoøi&quot; ;-) necháme ve¹kerou ¹pinavou práci na OpenGL a pota¾mo na hardwaru grafické karty. Zkrátka vystaèíme si nìkolika málo øádky kódu. Pokud máme data obrázku ve správném formátu, je i vytvoøení textury hraèka. Problém spoèívá pøedev¹ím v nahrání obrázku do pamìti. Existují spousty formátù od tìch nejjednodu¹¹ích (.RAW), kdy staèí soubor nahrát do pamìti tak jak je, pøes trochu slo¾itìj¹í (.TGA, .BMP), které se je¹tì dají nahrát vlastními silami, a¾ po hodnì slo¾ité formáty (.JPG a spol.), kdy ka¾dý rozumnì uva¾ující èlovìk vùbec nepøemý¹lí a ihned sáhne po cizí knihovnì. To samé platí i pro nejrùznìj¹í 3D modely z CAD/CAM programù, 3D Studia MAX, Milkshape 3D a dal¹ího modelovacího softwaru. Orientovat se v naèítání externích dat bývá opravdu hodnì slo¾ité, zvlá¹» kdy¾ ani neznáte pøesný formát souboru, proto¾e ho daná firma vùbec neuvolnila.</p>

<p>Obrázky, modely a v¹echny ostatní formáty dat v¹ak mají jednu obrovskou výhodu - práce s nimi je témìø v¾dy naprosto stejná. Lze vytvoøit univerzální knihovny, které napø. uspoøádají data nahraná ze souboru do pevnì daného univerzálního formátu, programátor je pak pouze pøevezme a bez problémù pou¾ije.</p>

<p>Jsou ale oblasti, kde ¾ádné knihovny od cizích lidí nepomù¾ou. Pokud vùbec existují, tak vìt¹inou musíme provést spoustu slo¾itých úprav, aby pasovaly na ná¹ pøípad. Typickým pøíkladem mohou být fyzikální simulace. Co to je? Vìt¹inou se jedná o pohyby objektù, které mají vypadat jako z reálného svìta, ale nejen ony.</p>

<p>Napøíklad pøi vytváøení automobilového simulátoru je velice lehké pøi zatoèení volantem zmìnit smìr auta. Trochu tì¾¹í je u¾ zajistit, aby pøi zatáèení nejelo v jednom okam¾iku urèitým smìrem a v druhém kolmo na pùvodní smìr a navíc stejnou rychlostí - hodnì nereálné. Automobilovému fanou¹kovi by dále mohlo být divné, ¾e se auto pøi najetí ve stovce do ostré zatáèky nepøevrhne a ani nedostane do smyku. Mám pokraèovat nebo staèí? Myslím, ¾e velice brzy pochopíte sami. Dal¹í podobné &quot;¹ílenosti&quot; uvedu pouze v rychlosti, k vìt¹inì z nich byste mìli v NeHe nalézt alespoò úvod. Jedná se hlavnì o nejrùznìj¹í grafické efekty, detekce kolizí, kosterní animace modelù, nejrùznìj¹í optimalizace rychlosti programu, umìlá inteligence poèítaèových protihráèù atd. atd. atd... ka¾dé téma samo o sobì na tisíc stránek.</p>

<p>Ale proè to sem v¹echno pí¹i? Abyste se zaèali co nejdøíve a co nejvíce vìnovat jednomu hodnì dùle¾itému pøedmìtu: matematice. Pokud jste je¹tì ve ¹kole, neberte ji jako nìco, co se musíte nauèit, abyste nedostali ¹patnou známku nebo nepropadli. Berte ji jako nìco, co se vám bude jednou urèitì hodit a bez èeho se prostì neobejdete. Jestli vám nejde a proto ji nenávidíte, zkuste svùj pøístup zmìnit, bez ní budete mít mnohem men¹í ¹ance, pokud nìjaké.</p>

<p>Je pravda, ¾e ú¾asných a nepøedstavitelných efektù dosáhnete i obyèejným sèítáním a odèítáním (hlavnì v zaèátcích), ale za rok, za dva se minimálnì bez analytické geometrie (body, vektory, roviny...) naprosto neobejdete, v 3D grafice vás bude provázet na ka¾dém kroku. Pokud se chystáte vìnovat grafice pøedev¹ím kvùli hrám - je zajímavé, ¾e v¹ichni zaèínají s my¹lenkou her - UÈTE SE MATEMATIKU, jinak nemáte nejmen¹í ¹anci.</p>

<p>Kdy¾ u¾ jsme u té ¹koly, vrhnìte se i na angliètinu. O programování existuje spousta literatury i v èe¹tinì, ale devadesát devìt procent se vìnuje naprostým základùm. Zbývající jedno procento v té záplavì stále stejných informací velice jistì pøehlédnete. Zírali byste, kolik knih a internetových serverù se vìnuje pokroèilým programovacím technikám, grafickým efektùm, programování her a vùbec v¹em pøevratným novinkám v oblasti poèítaèù, bohu¾el v¹echno anglicky.</p>

<p>Asi si myslíte, ¾e máte èeské literatury víc, ne¾ stihnete kdy pøeèíst. Jestli vás programování chytne jako mì, a vìøte, ¾e chytne :-), dávám vám pùl roku, rok, víc urèitì ne. Abych pravdu øekl, já jsem zaèínal s OpenGL témìø rovnou na anglických textech. V té dobì bylo do èe¹tiny pøelo¾eno pouze osm NeHe Tutoriálù - pro zaèáteèníka absolutní minimum, v knihkupectvích nic (pøetrvává do dne¹ka) a i na èeském internetu se nalezlo textù pomálu a to jsem pro¹mejdil ka¾dý kout. Dnes se dá alespoò po internetu pár vìcí sehnat i v èe¹tinì, ale v mnoha oblastech angliètina stále dominuje, troufám si øíct, ¾e bude i nadále.</p>

<p>Co víc napsat? Asi jen popøát hodnì ¹tìstí, zaèátky bývají opravdu hodnì slo¾ité. Je¹tì jedna vìc, pokud nebudete úspì¹ní u prvního tutoriálu, zkuste ho pøeskoèit a nìkdy pozdìji se k nìmu vrátit. U¾ hodnì lidí hned na zaèátku odradil a pøitom se v nìm OpenGL skoro nevyskytuje, témìø èisté Win32 API. Popisuje &quot;pouze&quot; vytvoøení okna s podporou OpenGL. Zatím vám staèí vìdìt, ¾e to, co chcete vykreslit, se pí¹e do funkce DrawGLScene() a ve¹kerá inicializace se umis»uje do InitGL(), v¹e ostatní staèí zkopírovat ...a modlit se, ¾e to bude fungovat i nadále :-)</p>

<p>Tak¾e je¹tì jednou pøeji hodnì úspìchù s OpenGL a pøíjemné chvíle pøi ètení NeHe Tutoriálù...</p>

<p class="autor">Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?>, 19.09.2004</p>

<?FceNeHeOkolniLekce(0);?>

<?
include 'p_end.php';
?>
