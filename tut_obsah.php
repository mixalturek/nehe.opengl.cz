<?
$g_title = 'CZ NeHe OpenGL - Obsah NeHe OpenGL tutoriálù';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>NeHe OpenGL Tutoriály</h1>

<?
function NeHe()
{
	for($i = 0; $i <= 48; $i++)
	{
		switch($i)
		{
			case 0:
				$tit='Pøedmluva k NeHe Tutoriálùm';
				$napsal='Michal Turek - Woq';
				$prelozil='není pøekladem';
				$text='Je¹tì ne¾ se pustíte do ètení tohoto textu, mìli byste vìdìt, ¾e není souèástí oficiálních (anglických) NeHe Tutoriálù. Napsal ho &quot;pouze&quot; jeden z pøekladatelù, kterému chybìlo nìco na zpùsob trochu ucelenìj¹ího úvodu do tak obrovské problematiky, jakou pøedstavuje programování 3D grafiky, her a ostatnì v¹eho okolo OpenGL.';
				break;
			case 1:
				$tit='Vytvoøení OpenGL okna ve Windows';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Václav Slováèek - Wessan';
				$text='Nauèíte se jak nastavit a vytvoøit OpenGL okno ve Windows. Program, který vytvoøíte zobrazí &quot;pouze&quot; prázdné okno. Èerné pozadí nevypadá nic moc, ale pokud porozumíte této lekci, budete mít velmi dobrý základ pro jakoukoliv dal¹í práci. Zjistíte jak OpenGL pracuje, jak probíhá vytváøení okna a také jak napsat jednodu¹e pochopitelný kód.';
				break;
			case 2:
				$tit='Vytváøení trojúhelníkù a ètyøúhelníkù';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Václav Slováèek - Wessan';
				$text='Zdrojový kód z první lekce trochu upravíme, aby program vykreslil trojúhelník a ètverec. Vím, ¾e si asi myslíte, ¾e takovéto vykreslování je banalita, ale a¾ zaènete programovat pochopíte, ¾e orientovat se ve 3D prostoru není na pøedstavivost a¾ tak jednoduché. Jakékoli vytváøení objektù v OpenGL závisí na trojúhelnících a ètvercích. Pokud pochopíte tuto lekci máte napùl vyhráno.';
				break;
			case 3:
				$tit='Barvy';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='S jednoduchým roz¹íøením znalostí ze druhé lekce budete moci pou¾ívat barvy. Nauèíte se jak ploché vybarvování, tak i barevné pøechody. Barvy rozzáøí vzhled aplikace a tím spí¹e zaujmou diváka.';
				break;
			case 4:
				$tit='Rotace';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='Nauèíme se, jak otáèet objekt okolo os. Trojúhelník se bude otáèet kolem osy y a ètverec kolem osy x. Je jednoduché vytvoøit scénu z polygonù. Pøidání pohybu ji pìknì o¾iví.';
				break;
			case 5:
				$tit='Pevné objekty';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='Roz¹íøením poslední èásti vytvoøíme skuteèné 3D objekty. Narozdíl od 2D objektù ve 3D prostoru. Zmìníme trojúhelník na pyramidu a ètverec na krychli. Pyramida bude vybarvena barevným pøechodem a ka¾dou stìnu krychle vybarvíme jinou barvou.';
				break;
			case 6:
				$tit='Textury';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='Namapujeme bitmapový obrázek na krychli. Pou¾ijeme zdrojové kódy z první lekce, proto¾e je jednodu¹í (a pøehlednìj¹í) zaèít s prázdným oknem ne¾ slo¾itì upravovat pøedchozí lekci.';
				break;
			case 7:
				$tit='Texturové filtry, osvìtlení, ovládání pomocí klávesnice';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Jiøí Rajský - RAJSOFT junior';
				$text='V tomto díle se pokusím vysvìtlit pou¾ití tøí odli¹ných texturových filtrù. Dále pak pohybu objektù pomocí klávesnice a nakonec aplikaci jednoduchých svìtel v OpenGL. Nebude se jako obvykle navazovat na kód z pøedchozího dílu, ale zaène se pìknì od zaèátku.';
				break;
			case 8:
				$tit='Blending';
				$napsal='Tom Stanis';
				$prelozil='Jiøí Rajský - RAJSOFT junior';
				$text='Dal¹í typ speciálního efektu v OpenGL je blending, neboli prùhlednost. Kombinace pixelù je urèena alfa hodnotou barvy a pou¾itou funkcí. Nabývá-li alfa 0.0f, materiál zprùhlední, hodnota 1.0f pøiná¹í pravý opak.';
				break;
			case 9:
				$tit='Pohyb bitmap ve 3D prostoru';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='Tento tutoriál vás nauèí pohyb objektù ve 3D prostoru a kreslení bitmap bez èerných míst, zakrývajících objekty za nimi. Jednoduchou animaci a roz¹íøené pou¾ití blendingu. Teï byste u¾ mìli rozumìt OpenGL velmi dobøe. Nauèili jste se v¹e od nastavení OpenGL okna, po mapování textur za pou¾ití svìtel a blendingu. To byl první tutoriál pro støednì pokroèilé. A pokraèujeme dále...';
				break;
			case 10:
				$tit='Vytvoøení 3D svìta a pohyb v nìm';
				$napsal='Lionel Brits - ßetelgeuse';
				$prelozil='Jiøí Rajský - RAJSOFT junior &amp; Michal Turek - Woq';
				$text='Do souèasnosti jsme programovali otáèející se kostku nebo pár hvìzd. Máte (mìli byste mít :-) základní pojem o 3D. Ale rotující krychle asi nejsou to nejlep¹í k tvorbì dobrých deathmatchových protivníkù! Neèekejte a zaènìte s Quakem IV je¹tì dnes! Tyto dny potøebujete k velkému, komplikovanému a dynamickému 3D svìtu s pohybem do v¹ech smìrù, skvìlými efekty zrcadel, portálù, deformacemi a tøeba také vysokým frameratem. Tato lekce vám vysvìtlí základní strukturu 3D svìta a pohybu v nìm.';
				break;
			case 11:
				$tit='Efekt vlnící se vlajky';
				$napsal='Bosco';
				$prelozil='Michal Turek - Woq';
				$text='Nauèíme se jak pomocí sinusové funkce animovat obrázky. Pokud znáte standardní ¹etøiè Windows "Létající 3D objekty" (i on by mìl být programovaný v OpenGL), tak budeme dìlat nìco podobného.';
				break;
			case 12:
				$tit='Display list';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Chcete vìdìt, jak urychlit va¹e programy v OpenGL? Jste unaveni z nesmyslného opisování ji¾ napsaného kódu? Nejde to nìjak jednodu¹eji? Ne¹lo by napøíklad jedním pøíkazem vykreslit otexturovanou krychli? Samozøejmì, ¾e jde. Tento tutoriál je urèený speciálnì pro vás. Pøedvytvoøené objekty a jejich vykreslování jedním øádkem kódu. Jak snadné...';
				break;
			case 13:
				$tit='Bitmapové fonty';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Èasto kladená otázka týkající se OpenGL zní: &quot;Jak zobrazit text?&quot;. V¾dycky jde namapovat texturu textu. Bohu¾el nad ním máte velmi malou kontrolu. A pokud nejste dobøí v blendigu, vìt¹inou skonèíte smixováním s ostatními obrázky. Pokud byste chtìli znát lehèí cestu k výstupu textu na jakékoli místo s libovolnou barvou nebo fontem, potom je tato lekce urèitì pro vás. Bitmapové fonty jsou 2D písma, které nemohou být rotovány. V¾dy je uvidíte zepøedu.';
				break;
			case 14:
				$tit='Outline fonty';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Bitmapové fonty nestaèí? Potøebujete kontrolovat pozici textu i na ose z? Chtìli byste fonty s hloubkou? Pokud zní va¹e odpovìï ano, pak jsou 3D fonty nejlep¹í øe¹ení. Mù¾ete s nimi pohybovat na ose z a tím mìnit jejich velikost, otáèet je, prostì dìlat v¹e, co nemù¾ete s obyèejnými. Jsou nejlep¹í volbou ke hrám a demùm.';
				break;
			case 15:
				$tit='Mapování textur na fonty';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Po vysvìtlení bitmapových a 3D fontù v pøedchozích dvou lekcích jsem se rozhodl napsat lekci o mapování textur na fonty. Jedná se o tzv. automatické generování koordinátù textur. Po doètení této lekce budete umìt namapovat texturu opravdu na cokoli - zcela snadno a jednodu¹e.';
				break;
			case 16:
				$tit='Mlha';
				$napsal='Christopher Aliotta - Precursor';
				$prelozil='Michal Turek - Woq';
				$text='Tato lekce roz¹iøuje pou¾itím mlhy lekci 7. Nauèíte se pou¾ívat tøí rùzných filtrù, mìnit barvu a nastavit oblast pùsobení mlhy (v hloubce). Velmi jednoduchý a &quot;efektní&quot; efekt.';
				break;
			case 17:
				$tit='2D fonty z textur';
				$napsal='Giuseppe D\'Agata &amp; Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V této lekci se nauèíte, jak vykreslit font pomocí texturou omapovaného obdélníku. Dozvíte se také, jak pou¾ívat pixely místo jednotek. I kdy¾ nemáte rádi mapování 2D znakù, najdete zde spoustu nových informací o OpenGL.';
				break;
			case 18:
				$tit='Kvadriky';
				$napsal='GB Schmick - TipTup';
				$prelozil='Michal Turek - Woq';
				$text='Pøedstavuje se vám bájeèný svìt kvadrikù. Jedním øádkem kódu snadno vytváøíte komplexní objekty typu koule, disku, válce ap. Pomocí matematiky a trochy plánování lze snadno morphovat jeden do druhého.';
				break;
			case 19:
				$tit='Èásticové systémy';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Chtìli jste u¾ nìkdy naprogramovat exploze, vodní fontány, planoucí hvìzdy a jiné skvìlé efekty, nicménì kódování èásticových systémù bylo buï pøíli¹ tì¾ké nebo jste vùbec nevìdìli, jak na to? V této lekci zjistíte, jak vytvoøit jednoduchý, ale dobøe vypadající èásticový systém. Extra pøidáme duhové barvy a ovládání klávesnicí. Také se dozvíte, jak pomocí triangle stripu jednodu¹e vykreslovat velké mno¾ství trojúhelníkù.';
				break;
			case 20:
				$tit='Maskování';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Èerné okraje obrázkù jsme dosud oøezávali blendingem. Aèkoli je tato metoda efektivní, ne v¾dy transparentní objekty vypadají dobøe. Modelová situace: vytváøíme hru a potøebujeme celistvý text nebo zakøivený ovládací panel, ale pøi blendingu scéna prosvítá. Nejlep¹ím øe¹ením je maskování obrázkù.';
				break;
			case 21:
				$tit='Pøímky, antialiasing, èasování, pravoúhlá projekce, základní zvuky a jednoduchá herní logika';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='První opravdu rozsáhlý tutoriál - jak u¾ plyne z gigantického názvu. Doufejme, ¾e taková spousta informací a technik doká¾e udìlat ¹»astným opravdu ka¾dého. Strávil jsem dva dny kódováním a kolem dvou týdnù psaním tohoto HTML souboru. Pokud jste nìkdy hráli hru Admiar, lekce vás vrátí do vzpomínek. Úkol hry sestává z vyplnìní jednotlivých políèek møí¾ky. Samozøejmì se musíte vyhýbat v¹em nepøátelùm.';
				break;
			case 22:
				$tit='Bump Mapping &amp; Multi Texturing';
				$napsal='Jens Schneider';
				$prelozil='Václav Slováèek - Wessan';
				$text='Pravý èas vrátit se zpátky na zaèátek a zaèít si opakovat. Nováèkùm v OpenGL se absolutnì nedoporuèuje! Pokud, ale máte odvahu, mù¾ete zkusit dobrodru¾ství s nadupanou grafikou. V této lekci modifikujeme kód z ¹esté lekce, aby podporoval hardwarový multi texturing pøes opravdu skvìlý vizuální efekt nazvaný bump mapping.';
				break;
			case 23:
				$tit='Mapování textur na kulové kvadriky';
				$napsal='GB Schmick - TipTup';
				$prelozil='Milan Turek';
				$text='Tento tutoriál je napsán na bázi <a href="tut_18.php">lekce 18</a>. V <a href="tut_15.php">lekci 15</a> (Mapování textur na fonty) jsem psal o automatickém mapování textur. Vysvìtlil jsem jak mù¾eme poprosit OpenGL o automatické generování texturových koordinátù, ale proto¾e lekce 15 byla celkem skromná, rozhodl jsem se pøidat mnohem více detailù o této technice.';
				break;
			case 24:
				$tit='Výpis OpenGL roz¹íøení, oøezávací testy a textury z TGA obrázkù';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V této lekci se nauèíte, jak zjistit, která OpenGL roz¹íøení (extensions) podporuje va¹e grafická karta. Vypí¹eme je do støedu okna, se kterým budeme moci po stisku ¹ipek rolovat. Pou¾ijeme klasický 2D texturový font s tím rozdílem, ¾e texturu vytvoøíme z TGA obrázku. Jeho nejvìt¹ími pøednostmi jsou jednoduchá práce a podpora alfa kanálu. Odbouráním bitmap u¾ nebudeme muset inkludovat knihovnu glaux.';
				break;
			case 25:
				$tit='Morfování objektù a jejich nahrávání z textového souboru';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V této lekci se nauèíte, jak nahrát souøadnice vrcholù z textového souboru a plynulou transformaci z jednoho objektu na druhý. Nezamìøíme se ani tak na grafický výstup jako spí¹e na efekty a potøebnou matematiku okolo. Kód mù¾e být velice jednodu¹e modifikován k vykreslování linkami nebo polygony.';
				break;
			case 26:
				$tit='Odrazy a jejich oøezávání za pou¾ití stencil bufferu';
				$napsal='Banu Cosmin - Choko';
				$prelozil='Milan Turek &amp; Michal Turek - Woq';
				$text='Tutoriál demonstruje extrémnì realistické odrazy za pou¾ití stencil bufferu a jejich oøezávání, aby &quot;nevystoupily&quot; ze zrcadla. Je mnohem více pokrokový ne¾ pøedchozí lekce, tak¾e pøed zaèátkem ètení doporuèuji men¹í opakování. Odrazy objektù nebudou vidìt nad zrcadlem nebo na druhé stranì zdi a budou mít barevný nádech zrcadla - skuteèné odrazy.';
				break;
			case 27:
				$tit='Stíny';
				$napsal='Banu Cosmin - Choko &amp; Brett Porter';
				$prelozil='Michal Turek - Woq';
				$text='Pøedstavuje se vám velmi komplexní tutoriál na vrhání stínù. Efekt je doslova neuvìøitelný. Stíny se roztahují, ohýbají a zahalují i ostatní objekty ve scénì. Realisticky se pokroutí na stìnách nebo podlaze. Se v¹ím lze pomocí klávesnice pohybovat ve 3D prostoru. Pokud je¹tì nejste se stencil bufferem a matematikou jako jedna rodina, nemáte nejmen¹í ¹anci.';
				break;
			case 28:
				$tit='Bezierovy køivky a povrchy, fullscreen fix';
				$napsal='David Nikdel';
				$prelozil='Michal Turek - Woq';
				$text='David Nikdel je osoba stojící za tímto skvìlým tutoriálem, ve kterém se nauèíte, jak se vytváøejí Bezierovy køivky. Díky nim lze velice jednodu¹e zakøivit povrch a provádìt jeho plynulou animaci pouhou modifikací nìkolika kontrolních bodù. Aby byl výsledný povrch modelu je¹tì zajímavìj¹í, je na nìj namapována textura. Tutoriál také eliminuje problémy s fullscreenem, kdy se po návratu do systému neobnovilo pùvodní rozli¹ení obrazovky.';
				break;
			case 29:
				$tit='Blitter, nahrávání .RAW textur';
				$napsal='Andreas Löffler &amp; Rob Fletcher';
				$prelozil='Václav Slováèek - Wessan &amp; Michal Turek - Woq';
				$text='V této lekci se nauèíte, jak se nahrávají .RAW obrázky a konvertují se do textur. Dozvíte se také o blitteru, grafické metodì pøená¹ení dat, která umo¾òuje modifikovat textury poté, co u¾ byly nahrány do programu. Mù¾ete jím zkopírovat èást jedné textury do druhé, blendingem je smíchat dohromady a také roztahovat. Malièko upravíme program tak, aby v dobì, kdy není aktivní, vùbec nezatì¾oval procesor.';
				break;
			case 30:
				$tit='Detekce kolizí';
				$napsal='Dimitrios Christopoulos';
				$prelozil='Michal Turek - Woq';
				$text='Na podobný tutoriál jste u¾ jistì netrpìlivì èekali. Nauèíte se základy o detekcích kolizí, jak na nì reagovat a na fyzice zalo¾ené modelovací efekty (nárazy, pùsobení gravitace ap.). Tutoriál se více zamìøuje na obecnou funkci kolizí ne¾ zdrojovým kódùm. Nicménì dùle¾ité èásti kódu jsou také popsány. Neoèekávejte, ¾e po prvním pøeètení úplnì v¹emu z kolizí porozumíte. Je to komplexní námìt, se kterým vám pomohu zaèít.';
				break;
			case 31:
				$tit='Nahrávání a renderování modelù';
				$napsal='Brett Porter';
				$prelozil='Michal Turek - Woq';
				$text='Dal¹í skvìlý tutoriál! Nauèíte se, jak nahrát a zobrazit otexturovaný Milkshape3D model. Nezdá se to, ale asi nejvíce se budou hodit znalosti o práci s dynamickou pamìtí a jejím kopírování z jednoho místa na druhé.';
				break;
			case 32:
				$tit='Picking, alfa blending, alfa testing, sorting';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V tomto tutoriálu se pokusím zodpovìdìt nìkolik otázek, na které jsem dennì dotazován. Chcete vìdìt, jak pøi kliknutí tlaèítkem my¹i identifikovat OpenGL objekt nacházející se pod kurzorem (picking). Dále byste se chtìli dozvìdìt, jak vykreslit objekt bez zobrazení urèité barvy (alfa blending, alfa testing). Tøetí vìcí, se kterou si nevíte rady, je, jak øadit objekty, aby se pøi blendingu správnì zobrazily (sorting). Naprogramujeme hru, na které si v¹e vysvìtlíme.';
				break;
			case 33:
				$tit='Nahrávání komprimovaných i nekomprimovaných obrázkù TGA';
				$napsal='Evan Pipho - Terminate';
				$prelozil='Michal Turek - Woq';
				$text='V lekci 24 jsem vám ukázal cestu, jak nahrávat nekomprimované 24/32 bitové TGA obrázky. Jsou velmi u¾iteèné, kdy¾ potøebujete alfa kanál, ale nesmíte se starat o jejich velikost, proto¾e byste je ihned pøestali pou¾ívat. K diskovému místu nejsou zrovna ¹etrné. Problém velikosti vyøe¹í nahrávání obrázkù komprimovaných metodou RLE. Kód pro loading a hlavièkové soubory jsou oddìleny od hlavního projektu, aby mohly být snadno pou¾ity i jinde.';
				break;
			case 34:
				$tit='Generování terénù a krajin za pou¾ití vý¹kového mapování textur';
				$napsal='Ben Humphrey - DigiBen';
				$prelozil='Michal Turek - Woq';
				$text='Chtìli byste vytvoøit vìrnou simulaci krajiny, ale nevíte, jak na to? Bude nám staèit obyèejný 2D obrázek ve stupních ¹edi, pomocí kterého deformujeme rovinu do tøetího rozmìru. Na první pohled tì¾ko øe¹itelné problémy bývají èastokrát velice jednoduché.';
				break;
			case 35:
				$tit='Pøehrávání videa ve formátu AVI';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Pøehrávání AVI videa v OpenGL? Na pozadí, povrchu krychle, koule, èi válce, ve fullscreenu nebo v obyèejném oknì. Co víc si pøát...';
				break;
			case 36:
				$tit='Radial Blur, renderování do textury';
				$napsal='Dario Corno - rIo';
				$prelozil='Michal Turek - Woq';
				$text='Spoleènými silami vytvoøíme extrémnì pùsobivý efekt radial blur, který nevy¾aduje ¾ádná OpenGL roz¹íøení a funguje na jakémkoli hardwaru. Nauèíte se také, jak lze na pozadí aplikace vyrenderovat scénu do textury, aby pozorovatel nic nevidìl.';
				break;
			case 37:
				$tit='Cel-Shading';
				$napsal='Sami Hamlaoui - MENTAL';
				$prelozil='Václav Slováèek - Wessan &amp; Michal Turek - Woq';
				$text='Cel-Shading je druh vykreslování, pøi kterém výsledné modely vypadají jako ruènì kreslené karikatury z komiksù (cartoons). Rozlièné efekty mohou být dosa¾eny miniaturní modifikací zdrojového kódu. Cel-Shading je velmi úspì¹ným druhem renderingu, který doká¾e kompletnì zmìnit duch hry. Ne ale v¾dy... musí se umìt a pou¾ít s rozmyslem.';
				break;
			case 38:
				$tit='Nahrávání textur z resource souboru &amp; texturování trojúhelníkù';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Václav Slováèek - Wessan';
				$text='Tento tutoriál jsem napsal pro v¹echny z vás, kteøí se mì v emailech dotazovali na to &quot;Jak mám loadovat texturu ze zdrojù programu, abych mìl v¹echny obrázky ulo¾ené ve výsledném .exe souboru?&quot; a také pro ty, kteøí psali &quot;Vím, jak otexturovat obdélník, ale jak mapovat na trojúhelník?&quot; Tutoriál není, oproti jiným, extrémnì pokrokový, ale kdy¾ nic jiného, tak se nauèíte, jak skrýt va¹e precizní textury pøed okem u¾ivatele. A co víc - budete moci trochu ztí¾it jejich kradení :-)';
				break;
			case 39:
				$tit='Úvod do fyzikálních simulací';
				$napsal='Erkin Tunca';
				$prelozil='Václav Slováèek - Wessan';
				$text='V gravitaèním poli se pokusíme rozpohybovat hmotný bod s konstantní rychlostí, hmotný bod pøipojený k pru¾inì a hmotný bod, na který pùsobí gravitaèní síla - v¹e podle fyzikálních zákonù. Kód je zalo¾en na nejnovìj¹ím NeHeGL kódu.';
				break;
			case 40:
				$tit='Fyzikální simulace lana';
				$napsal='Erkin Tunca';
				$prelozil='Michal Turek - Woq';
				$text='Pøichází druhá èást dvoudílné série o fyzikálních simulacích. Základy u¾ známe, a proto se pustíme do komplikovanìj¹ího úkolu - klávesnicí ovládat pohyby simulovaného lana. Zatáhneme-li za horní konec, prostøední èást se rozhoupe a spodek se vláèí po zemi. Skvìlý efekt.';
				break;
			case 41:
				$tit='Volumetrická mlha a nahrávání obrázkù pomocí IPicture';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V tomto tutoriálu se nauèíte, jak pomocí roz¹íøení EXT_fog_coord vytvoøit volumetrickou mlhu. Také zjistíte, jak pracuje IPicture kód a jak ho mù¾ete vyu¾ít pro nahrávání obrázkù ve svých vlastních projektech. Demo sice není a¾ tak komplexní jako nìkterá jiná, nicménì i pøesto vypadá hodnì efektnì.';
				break;
			case 42:
				$tit='Více viewportù';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Tento tutoriál byl napsán pro v¹echny z vás, kteøí se chtìli dozvìdìt, jak do jednoho okna zobrazit více pohledù na jednu scénu, kdy v ka¾dém probíhá jiný efekt. Jako bonus pøidám získávání velikosti OpenGL okna a velice rychlý zpùsob aktualizace textury bez jejího znovuvytváøení.';
				break;
			case 43:
				$tit='FreeType Fonty v OpenGL';
				$napsal='Sven Olsen';
				$prelozil='Pavel Hradský a Michal Turek - Woq';
				$text='Pou¾itím knihovny FreeType Font rendering library mù¾ete snadno vypisovat vyhlazené znaky, které vypadají mnohem lépe ne¾ písmena u bitmapových fontù z lekce 13. Ná¹ text bude mít ale i jiné výhody - bezproblémová rotace, dobrá spolupráce s OpenGL vybíracími (picking) funkcemi a víceøádkové øetìzce.';
				break;
			case 44:
				$tit='Èoèkové efekty';
				$napsal='Vic Hollis';
				$prelozil='Michal Turek - Woq';
				$text='Èoèkové efekty vznikají po dopadu paprsku svìtla napø. na objektiv kamery nebo fotoaparátu. Podíváte-li se na záøi vyvolanou èoèkou, zjistíte, ¾e jednotlivé útvary mají jednu spoleènou vìc. Pozorovateli se zdá, jako by se v¹echny pohybovaly skrz støed scény. S tímto na mysli mù¾eme osu z jednodu¹e odstranit a vytváøet v¹e ve 2D. Jediný problém související s nepøítomností z souøadnice je, jak zjistit, jestli se zdroj svìtla nachází ve výhledu kamery nebo ne. Pøipravte se proto na trochu matematiky.';
				break;
			case 45:
				$tit='Vertex Buffer Object (VBO)';
				$napsal='Paul Frazee';
				$prelozil='Michal Turek - Woq';
				$text='Jeden z nejvìt¹ích problémù jakékoli 3D aplikace je zaji¹tìní její rychlosti. V¾dy byste mìli limitovat mno¾ství aktuálnì renderovaných polygonù buï øazením, cullingem nebo nìjakým algoritmem na sni¾ování detailù. Kdy¾ nic z toho nepomáhá, mù¾ete zkusit napøíklad vertex arrays. Moderní grafické karty nabízejí roz¹íøení nazvané vertex buffer object, které pracuje podobnì jako vertex arrays kromì toho, ¾e nahrává data do vysoce výkonné pamìti grafické karty, a tak podstatnì sni¾uje èas potøebný pro rendering. Samozøejmì ne v¹echny karty tato nová roz¹íøení podporují, tak¾e musíme implementovat i verzi zalo¾enou na vertex arrays.';
				break;
			case 46:
				$tit='Fullscreenový antialiasing';
				$napsal='Colt McAnlis - MainRoach';
				$prelozil='Michal Turek - Woq';
				$text='Chtìli byste, aby va¹e aplikace vypadaly je¹tì lépe ne¾ doposud? Fullscreenové vyhlazování, nazývané té¾ multisampling, by vám mohlo pomoci. S výhodou ho pou¾ívají ne-realtimové renderovací programy, nicménì s dne¹ním hardwarem ho mù¾eme dosáhnout i v reálném èase. Bohu¾el je implementováno pouze jako roz¹íøení ARB_MULTISAMPLE, které nebude pracovat, pokud ho grafická karta nepodporuje.';
				break;
			case 47:
				$tit='CG vertex shader';
				$napsal='Owen Bourne';
				$prelozil='Michal Turek - Woq';
				$text='Pou¾ívání vertex a fragment (pixel) shaderù ke &quot;¹pinavé práci&quot; pøi renderingu mù¾e mít nespoèet výhod. Nejvíce je vidìt napø. pohyb objektù do teï výhradnì závislý na CPU, který nebì¾í na CPU, ale na GPU. Pro psaní velice kvalitních shaderù poskytuje CG (pøimìøenì) snadné rozhraní. Tento tutoriál vám uká¾e jednoduchý vertex shader, který sice nìco dìlá, ale nebude pøedvádìt ne nezbytné osvìtlení a podobné slo¾itìj¹í nadstavby. Tak jako tak je pøedev¹ím urèen pro zaèáteèníky, kteøí u¾ mají nìjaké zku¹enosti s OpenGL a zajímají se o CG.';
				break;
			case 48:
				$tit='ArcBall rotace';
				$napsal='Terence J. Grant';
				$prelozil='Pavel Hradský a Michal Turek - Woq';
				$text='Nebylo by skvìlé otáèet modelem pomocí my¹i jednoduchým drag &amp; drop? S ArcBall rotacemi je to mo¾né. Moje implementace je zalo¾ená na my¹lenkách Brettona Wadea a Kena Shoemakea. Kód také obsahuje funkci pro rendering toroidu - kompletnì i s normálami.';
				break;
			default:
				break;
		}

		$L = ($i < 10) ? '0'.$i : $i;

		echo "<div class=\"object\">\n";
		echo "<img src=\"images/nehe_tut/tut_$L.jpg\" alt=\"Lekce $i\" class=\"nehe_img_sm\" />\n";
		echo "<h3><a href=\"tut_$L.php\">Lekce $i - $tit</a></h3>\n";
		echo "<div>Napsal: $napsal</div>\n";
		echo "<div>Pøelo¾il: $prelozil</div>\n";
		echo "<p>$text</p>\n";
		echo "</div>\n\n";
	}
}

NeHe();// Volání funkce
?>

<?
include 'p_end.php';
?>
