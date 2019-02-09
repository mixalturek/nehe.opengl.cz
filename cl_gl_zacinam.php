<?
$g_title = 'CZ NeHe OpenGL - Pomoc, zaèínám';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Pomoc, zaèínám</h1>

<p class="nadpis_clanku">Víte, vzpomnìl jsem si na své zaèátky s OpenGL, kdy èlovìk nemohl sehnat témìø ¾ádné informace o OpenGL, jednodu¹e proto, ¾e ¾ádné neexistovaly. To byl vlastnì dùvod pro pøeklady NeHe Tutoriálù a následný vznik tohoto webu. Informací je u¾ nyní relativnì dost, ale stále zùstala otázka: Kde zaèít?</p>

<p>Ètete-li tuto stránku, tak u¾ asi víte, èeho chcete dosáhnout - nauèit se OpenGL, abyste mohli do svých skvìlých 2D her pøidat i tøetí rozmìr. Ka¾dá cesta v¹ak zaèíná jedním jediným krokem, o kterém se øíká, ¾e bývá v¾dy nejtì¾¹í. Tímto prvním krokem by mohl být napøíklad èlánek, který právì ètete.</p>

<h3>Programování</h3>

<p>Pøedtím ne¾ se pustíte do OpenGL, mìli byste umìt (MUSÍTE UMÌT) programovat. Tímto nemyslím padesáti-øádkové kalkulaèky v Pascalu typu: zadejte první èíslo, zadejte operátor, zadejte druhé èíslo, poèítám, výsledek je..., ale nìjaký vìt¹í projekt plný spousty vnoøených cyklù a podmínek. Mimochodem nepou¾ívejte Pascal (není my¹leno Delphi, ale starý TP 6, TP 7 ap.). Nejsem &quot;rasista&quot;, ale nemám rád &quot;archeologické pozùstatky&quot;, o kterých uèitelé (kteøí samozøejmì vìt¹inou neumí programovat) øíkají, ¾e jsou nezbytným základem pro výuku programování. Pøitom v¹ak zakládají na takovou spoustu zlozvykù (nezbytných pro práci v Pascalu a Dosu), ¾e trvá pìknì dlouho ne¾ se z nich prùmìrnì inteligentní èlovìk vyhrabe. Osobnì doporuèuji C/C++, proto¾e je velmi roz¹íøené a proto¾e na jeho syntaxi staví vìt¹ina pozdìj¹ích programovacích jazykù - Java, JavaScript, Perl, PHP, Action Script... Pokud nìkdy zkusíte programovat napø. webové aplikace v¹e bude desetkrát jednodu¹¹í, ne¾ kdy¾ zaèínáte od zaèátku.</p>

<p>Abych se vrátil zpìt ke èlánku... Dùkladná znalost kódování sice pro OpenGL není podmínkou, ale na sto procent se podobné znalosti budou hodit. Kdy¾ ne hned tak tøeba a¾ budete do svých programù importovat nejrùznìj¹í formáty obrázkù nebo modelù. Pokud jste u¾ nìkdy pracovali s grafikou, zkuste vytvoøit hru Tetris a pokud ne, tak alespoò zkuste vytvoøit obecný algoritmus pro pøevod èísel z libovolné soustavy do libovolné jiné. Napøíklad ze sedmièkové do dvaceti pìtkové. ®e neumíte pracovat v grafickém re¾imu vùbec nevadí. V¹e obstará OpenGL.</p>

<h3>API</h3>

<p>U¾ umíte opravdu dobøe programovat :-), ale nejspí¹ pouze v Dosovském textovém re¾imu. Sly¹eli jste nìkdy o událostmi øízeném programování, kdy operaèní systém posílá oknu zprávy o stisku kláves, pohybu my¹í, po¾adavky na pøekreslení...? Ne? Pak si vyberte nìjaké API (Application Programming Interface) a nauète se ho ovládat. Pokud chcete vytváøet programy pro MS&reg; Windows&reg;, máte více mo¾ností, ale s nejvìt¹í pravdìpodobností se budete rozhodovat mezi &quot;klasickým&quot; Win32 API a knihovnou MFC (Microsoft Foundation Class Library). Panuje názor, ¾e MFC by se mìlo pou¾ívat pro aplikace typu textových editorù a dialogových oken. Dema, hry ap. by mìl programátor vytváøet v systémovém API, proto¾e je v¾dy (vìt¹inou) rychlej¹í. K tomuto názoru se také pøikláním. Kdy¾ mám programovat hru, která má být rychlá, musím pøesnì vìdìt, co se v programu odehrává. V MFC se v¹echny funkce volají &quot;jakoby náhodou&quot; - k hlavní smyèce programu se prostì nedostanete (pokud to neumíte). Z toho plyne: Chcete-li programovat pod OpenGL, vyberte si systémové API. Mimochodem, právì v nìm jsou psané NeHe Tutoriály.</p>

<p>Nemáte dostatek penìz na zakoupení legálního operaèního systému MS Windows? Nebo se nechcete vázat ke konkrétnímu operaènímu systému? Nahraïte Win32 API za multiplatformní knihovnu <?OdkazWeb('clanky', 'SDL');?> (Simple DirectMedia Layer). Napí¹ete jediný zdrojový kód a potom snadno pøelo¾íte program pro Windows&reg;, Linux, BSD, FreeBSD, Mac OS a spousty dal¹ích operaèních systémù. Pochopte jednu vìc: Svìt nestojí na Billových Woknech&reg;. Slovo OpenGL je zkratkou ze slov Open Graphic Library. Open znamená OTEVØENÝ, èili ka¾dý mù¾e implementovat OpenGL a pokud splní urèitá kritéria standardu (a zaplatí licenèní poplatky), bude tato implementace pova¾ována za plnohodnotné OpenGL. Nezahazujte hlavní rys OpenGL - jeho multiplatformnost a nezávislost na programovacím jazyku.</p>

<h3>OpenGL</h3>

<p>Nyní se koneènì pustíme do OpenGL, jste na nìj dostateènì pøipraveni. Asi ka¾dý vám øekne, a» zaènete na <?OdkazWeb('tut_obsah', 'NeHe OpenGL Tutoriálech');?>. Dobrá rada, dr¾te se jí. Nezapomeòte ale, ¾e pouze ètením se programovat nenauèíte. Musíte hlavnì prakticky kódovat. Paralelnì s NeHe Tutoriály doporuèuji èíst èlánek od Daniela Èecha <a href="cl_gl_referat.pdf">Referát na praktikum z informatiky</a> (formát PDF), který hodnì dobøe popisuje okolnosti vzniku a principy OpenGL, základy práce s ním, OpenGL datové typy, proè funkce zaèínají gl a na jejich konci bývá 3f, jinde 3ub, a úplnì jinde 4d. Ètìte i dal¹í <?OdkazWeb('clanky', 'èlánky');?> a prohlí¾ejte cizí zdrojové kódy. Nìkolik programù od èeských autorù naleznete <?OdkazWeb('programy', 'zde');?>, ale opravdu gigantické mno¾ství jich je na <?OdkazBlank('http://nehe.gamedev.net/');?> nebo <?OdkazBlank('http://www.gametutorials.com/');?>. Pokud umíte anglicky, cizí èlánky ani zdrojové kódy nebudou problémem. Ètìte diskuse na fórech (napø. <?OdkazBlank('http://www.builder.cz/');?>), naleznete na nich spoustu praktických informací a vyøe¹ených problémù. Dal¹ím hodnì kvalitním zdrojem informací témìø o v¹em jsou Linuxové manuálové stránky ve Windows pak nápovìda MSDN od Microsoftu. Existují offliny zabírající nìkolik CD, ale pokud se k nim nedostanete (bývají pøilo¾eny k Visual Studiu), zkuste <?OdkazBlank('http://msdn.microsoft.com/');?>.</p>

<h3>Pomáhejte</h3>

<p>A¾ budete umìt OpenGL, pomáhejte ostatním. I vy jste na zaèátku potøebovali pomoc. Tímto pomáháním nemyslím zrovna psaní èlánkù pro tento web (nicménì i to mù¾ete :-), ale kdy¾ vám nìkdo napí¹e email i se zaèáteènickým dotazem, odpovìzte mu. Pokud neznáte odpovìï, zkuste ho nasmìrovat, kde by ji mohl najít. To samé platí i pro diskusní fóra. Poskytujte zdrojové kódy (napø. pod licencí <?OdkazBlank('http://www.gnu.cz/', 'GNU GPL');?>). Kdysi, kdy¾ jsem se poprvé doèetl o my¹lence Linuxu a lidem okolo nìj, jsem pochopil, ¾e to nejlep¹í na mých programech budou volnì pøístupné zdrojové kódy. Bez nich by si jich s nejvìt¹í pravdìpodobností nikdo ani nev¹iml. Myslíte si, ¾e nìkdo bude z internetu stahovat 5MB dat, aby je za pùl hodiny smazal? Nebude. Pokud v¹ak pøidáte zdrojové kódy, stráví u nich tøeba týden, nauèí se spoustu nových vìcí a pravdìpodobnì je doporuèí i dal¹ím lidem, aby se na nì podívali - nicménì zále¾í jen na vás.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojových kódù</li>
</ul>

<?
include 'p_end.php';
?>
