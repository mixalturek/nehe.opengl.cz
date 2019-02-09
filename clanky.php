<?
$g_title = 'CZ NeHe OpenGL - Èlánky';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Èlánky</h1>


<div class="object">
<div class="date">24.09.2005</div>
<h3>
<?OdkazWeb('cl_gl_kamera_3d', 'Kamera pro 3D svìt');?> - Michal Turek</h3>

<p>V tomto èlánku se pokusíme implementovat snadno pou¾itelnou tøídu kamery, která bude vhodná pro pohyby v obecném 3D svìtì, napøíklad pro nìjakou støíleèku - my¹ mìní smìr natoèení a ¹ipky na klávesnici zaji¹»ují pohyb. Pøesto¾e budeme pou¾ívat malièko matematiky, nebojte se a smìle do ètení!</p>
</div>


<div class="object">
<div class="date">07.09.2005</div>
<h3>
<?OdkazWeb('cl_gl_generovani_terenu', 'Procedurálne generovanie terénu');?> - Peter Mindek</h3>

<p>Mo¾no ste u¾ poèuli o vý¹kových mapách. Sú to také èiernobiele obrázky, pomocou ktorých sa vytvára 3D terén (vý¹ka terénu na urèitej pozícii je urèená farbou zodpovedajúceho bodu na vý¹kovej mape). Najjednoduch¹ie je vý¹kovú mapu naèíta» zo súboru a je pokoj. Sú v¹ak situácie, ako napr. keï robíte grafické demo, ktoré má by» èo najmen¹ie, keï príde vhod vý¹kovú mapu vygenerova» procedurálne. Tak¾e si uká¾eme ako na to. E¹te snáï spomeniem ¾e èíta» ïalej mô¾u aj tí, ktorí chcú vedie» ako vygenerova» takzvané &quot;oblaky&quot; (niekedy sa tomu hovorí aj plazma), nakoµko tento tutoriál bude z veµkej èasti práve o tom.</p>
</div>


<div class="object">
<div class="date">27.01.2005</div>
<h3>
<?OdkazWeb('cl_gl_billboard', 'Billboarding (pøiklápìní polygonù ke kameøe)');?> - Michal Turek</h3>

<p>Ka¾dý, kdo nìkdy programoval èásticové systémy, se jistì setkal s problémem, jak zaøídit, aby byly polygony viditelné z jakéhokoli smìru. Nebo-li, aby se nikdy nestalo, ¾e pøi natoèení kamery kolmo na rovinu èástice, nebyla vidìt pouze tenká linka. Slo¾itý problém, ultra jednoduché øe¹ení...</p>
</div>


<div class="object">
<div class="date">22.10.2004</div>
<h3>
<?OdkazWeb('cl_freetype_cz', 'FreeType Fonty v OpenGL a èesky');?> - Luká¹ Beran - Berka</h3>

<p>Chcete pou¾ívat ve svých programech FreeType Fonty i s èeskými znaky? Pokud ano, jste na správném místì. Tento èlánek doplòuje NeHe Tutoriál 43, ve kterém bylo popsáno pou¾ití FreeType s OpenGL, ale bohu¾el bez èeských znakù. Pou¾ito s laskavým svolením <?OdkazBlank('http://programovani.wz.cz/');?>.</p>
</div>


<div class="object">
<div class="date">25.08.2004</div>
<h3>
<?OdkazWeb('cl_gl_3ds', 'Naèítání .3DS modelù');?> - Michal Tuèek</h3>

<p>V tomto èlánku si uká¾eme, jak nahrát a vykreslit model ve formátu .3DS (3D Studio Max). Ná¹ kód bude umìt bez problémù naèítat soubory do tøetí verze programu, s vy¹¹ími verzemi bude pracovat také, ale nebude podporovat jejich nové funkce. Vycházím z ukázkového pøíkladu z <?OdkazBlank('http://www.gametutorials.com/');?>, kde také najdete zdrojové kódy pro C++ (èlánek je v Delphi).</p>
</div>


<div class="object">
<div class="date">15.08.2004</div>
<h3>
<?OdkazWeb('cl_gl_delphi', 'Vytvoøení OpenGL okna v Delphi');?> - Michal Tuèek</h3>

<p>Tento èlánek popisuje vytvoøení OpenGL okna pod operaèním systémem MS Windows ve vývojovém prostøedí Borland Delphi. Já osobnì pou¾ívám Delphi verze 7, ale ani v ni¾¹ích verzích by nemìl být problém vytvoøený kód zkompilovat a spustit. Z vìt¹í èásti se jedná o pøepis prvního NeHe Tutoriálu z jazyka C/C++ do Pascalu. Tak¾e smìle do toho...</p>
</div>


<div class="object">
<div class="date">11.07.2004</div>
<h3>
<?OdkazWeb('cl_gl_octree', 'Octree');?> - Michal Turek</h3>

<p>Octree (octal tree, oktalový strom) je zpùsob rozdìlování 3D prostoru na oblasti, který umo¾òuje vykreslit pouze tu èást svìta/levelu/scény, která se nachází ve výhledu kamery, a tím znaènì urychlit rendering. Mù¾e se také pou¾ít k detekcím kolizí.</p>
</div>


<div class="object">
<div class="date">20.06.2004</div>
<h3>
<?OdkazWeb('cl_gl_planety', 'Generování planet');?> - Milan Turek</h3>

<p>Pokud budete nìkdy potøebovat pro svou aplikaci vygenerovat realisticky vypadající planetu, tento èlánek se vám bude urèitì hodit - popisuje jeden ze zpùsobù vytváøení nedeformovaných kontinentù. Obvyklé zpùsoby pokrývání koule rovinnou texturou konèí obrovskými deformacemi na pólech. Dal¹í nevýhodou nìkterých zpùsobù je, ¾e výsledek je orientován v nìjakém smìru. To u této metody nehrozí.</p>
</div>


<div class="object">
<div class="date">09.05.2004</div>
<h3>
<?OdkazWeb('cl_gl_faq', 'FAQ: Èasto kladené dotazy');?> - Michal Turek</h3>

<p>Na emailu se mi nìkteré, vìt¹inou zaèáteènické, dotazy neustále opakují, jako pøíklad lze uvést problémy s knihovnou GLAUX a symbolickou konstantou CDS_FULLSCREEN v Dev-C++. Doufám, ¾e tato stránka trochu sní¾í zatí¾ení, ale pokud si stále nevíte rady, nebojte se mi napsat. Doufám, ¾e nebude vadit, kdy¾ sem umístím i ten vá¹ problém.</p>
</div>


<div class="object">
<div class="date">27.03.2004</div>
<h3>
<?OdkazWeb('cl_gl_linux', 'Zprovoznìní OpenGL v Linuxu (ovladaèe karty, kompilace)');?> - Michal Turek</h3>

<p>Kdy¾ jsem pøibli¾nì pøed pùl rokem (podzim 2003) pøecházel z MS Windows&reg; na operaèní systém Linux, mìl jsem relativnì velké potí¾e se zprovoznìním OpenGL. Nejedná se sice o nic slo¾itého, nicménì pro tehdy nic nechápajícího Woqa u¾ivatele (analogie na Frantu u¾ivatele :-) to byl naprosto neøe¹itelný problém.</p>
</div>


<div class="object">
<div class="date">31.12.2003</div>
<h3>
<?OdkazWeb('cl_gl_kamera', 'Tøída kamery a Quaternionu');?> - Michal Turek</h3>

<p>Chcete si naprogramovat letecký simulátor? Smìr letu nad krajinou mù¾ete mìnit klávesnicí i my¹í... Vytvoøíme nìkolik u¾iteèných tøíd, která vám pomohou s matematikou, která stojí za definováním výhledu kamery a pak v¹echno spojíme do jednoho funkèního celku.</p>
</div>


<div class="object">
<div class="date">13.12.2003</div>
<h3>
<?OdkazWeb('cl_gl_matice2', 'Matice v OpenGL');?> - Radomír Vrána</h3>

<p>V tomto èlánku se dozvíte, jakým zpùsobem OpenGL ukládá hodnoty rotací a translací do své modelview matice. Samozøejmì nebudou chybìt obrázky jejího obsahu po rùzných maticových operacích.</p>
</div>


<div class="object">
<div class="date">09.12.2003</div>
<h3>
<?OdkazWeb('cl_gl_matice', 'Operace s maticemi');?> - Pøemysl Jaro¹</h3>

<p>Zajímali jste se nìkdy o to, jak fungují OpenGL funkce pracující s maticemi? V tomto èlánku si vysvìtlíme, jak fungují funkce typu glTranslatef(), glRotatef(), glScalef() a jak je pøípadnì nahradit vlastním kódem.</p>
</div>


<div class="object">
<div class="date">01.12.2003</div>
<h3>
<?OdkazWeb('cl_gl_gluunproject', 'Pou¾íváme gluUnProject()');?> - Pøemysl Jaro¹</h3>

<p>Potøebujete transformovat pozici my¹i na souøadnice v OpenGL scénì a nevíte si rady? Pokud ano, jste na správném místì.</p>
</div>


<div class="object">
<div class="date">28.10.2003</div>
<h3>
<?OdkazWeb('cl_timer', 'Timer');?> - Marek Ol¹ák - Eosie</h3>

<p>Pøedstavte si, ¾e dìláte nìjaký velký dynamický svìt, pro který potøebujete mnoho výpoètù závislých na uplynulém èase (pohyb, rotace, animace, fyzika). Pokud synchronizujete klasicky pomocí FPS, neobejdete se pøi ka¾dém vykreslení bez spousty dìlení. Základem v¹eho je, tyto operace provádìt co nejménì, abychom zbyteènì nezatì¾ovali procesor.</p>
</div>


<div class="object">
<div class="date">14.09.2003</div>
<h3>
<?OdkazWeb('cl_gl_zacinam', 'Pomoc, zaèínám');?> - Michal Turek</h3>

<p>Víte, vzpomnìl jsem si na své zaèátky s OpenGL, kdy èlovìk nemohl sehnat témìø ¾ádné informace o OpenGL, jednodu¹e proto, ¾e ¾ádné neexistovaly. To byl vlastnì dùvod pro pøeklady NeHe Tutoriálù a následný vznik tohoto webu. Informací je u¾ nyní relativnì dost, ale stále zùstala otázka: Kde zaèít?</p>
</div>


<div class="object">
<div class="date">21.07.2003</div>
<h3>
<?OdkazWeb('cl_fps', 'FPS: Konstantní rychlost animace');?> - Michal Turek</h3>

<p>FPS je zkratka z poèáteèních písmen slov Frames Per Second, která by se dala do èe¹tiny pøelo¾it jako poèet snímkù za sekundu. Tato tøi písmena jsou spásou pøi spou¹tìní programù na rùzných poèítaèích. Vezmìte si hru, kterou programátor zaèáteèník vyvíjí doma na svém poèítaèi o rychlosti, øeknìme, Pentium II. Dá ji kamarádovi, aby se na ni podíval a zhodnotil. Kamarád má doma P4, spustí ji a v¹e je ¹ílenì rychlé. Díky FPS se toto nikdy nestane, na jakémkoli poèítaèi pùjde hra v¾dy stejnì rychle.</p>
</div>


<div class="object">
<div class="date">04.11.2002</div>
<h3>
<a href="cl_gl_referat.pdf">OpenGL - Referát na praktikum z informatiky</a> - Daniel Èech (PDF, 27 stran)</h3>

<p>Èlánek popisuje vznik a principy OpenGL, základy práce s ním, OpenGL datové typy, proè funkce zaèínají gl a na jejich konci bývá 3f, jinde 3ub apod. a základní práci v OpenGL. Na konci naleznete nìkolik ukázkových zdrojových kódù v GLUT a Win32 API a porovnání OpenGL s DirectX. Urèitì si ho pøeètìte, stojí za to. Pùvod: nìkde na internetu.</p>
</div>


<div class="object">
<div class="date">25.05.2004</div>
<h3>
<a href="cl_sdl_hry.pdf">Nìkolik poznámek k tvorbì poèítaèových her</a> - Bernard Lidický (PDF, 19 stran)</h3>

<p>Tento èlánek by mìl být shrnutím mých zku¹eností s tvorbou poèítaèových her, mohl by usnadnit ¾ivot zaèínajícím amatérským tvùrcùm her a zejména programátorùm. Podíváme se v nìm na nìkolik obecných vìcí o hrách a pak se vrhneme na grafiku, klávesnici s my¹í a nakonec na èas. V ka¾dé èásti se pokusíme vytvoøit nìjaké pøíklady a na nich pøedvést o èem je øeè. <?OdkazDown('download/clanky/cl_sdl_hry.tar.bz2');?> - text èlánku (PDF, TeX) + pøíklady.</p>
</div>


<div class="object">
<div class="date">28.03.2004</div>
<h3>
<?OdkazWeb('cl_sdl_picture', 'Komprimované textury a SDL_Image');?> - Radomír Vrána</h3>

<p>V tomto èlánku si uká¾eme, jak vytváøet komprimované OpenGL textury a jak za pomoci knihovny SDL_Image snadno naèítat obrázky s alfa kanálem nebo v paletovém re¾imu. Tøídu Picture jsem se sna¾il navrhnou tak, aby byla co nejjednodu¹¹í a dala se snadno pou¾ít v ka¾dém programu, zároveò díky SDL_Image poskytuje velké mo¾nosti.</p>
</div>


<div class="object">
<div class="date">07.04.2003</div>
<h3>
<?OdkazWeb('cl_sdl_image', 'Knihovna SDL Image');?> - Bernard Lidický</h3>

<p>Urèitì se vám nelíbí mít v¹echny textury ulo¾ené v BMP souborech, které nejsou zrovna pøátelské k místu na disku. Bohu¾el SDL ¾ádný jiný formát pøímo nepodporuje. Nicménì existuje malé roz¹íøení v podobì knihovnièky SDL Image poskytující funkci IMG_Load(), která umí naèíst vìt¹inu pou¾ívaných grafických formátù.</p>
</div>


<div class="object">
<div class="date">17.02.2003</div>
<h3>
<?OdkazWeb('cl_sdl_okno', 'Vytvoøení SDL okna');?> - Bernard Lidický</h3>

<p>Woq mì po¾ádal, abych napsal tutoriál pro pou¾ívání OpenGL pod knihovnou SDL. Je to mùj první tutoriál, tak¾e doufám, ¾e se bude líbit. Zkusíme vytvoøit kód, který bude odpovídat druhé lekci &quot;normálních&quot; tutoriálù. Zdrojový kód je &quot;oficiální&quot; NeHe port druhé lekce do SDL. Pokusím se popsat, jak se vytváøí okno a vìci okolo.</p>
</div>


<div class="object">
<div class="date">13.02.2004</div>
<h3>
<?OdkazWeb('cl_mat_primka2d', 'Pøímka ve 2D');?> - Michal Turek</h3>

<p>Radomír Vrána mì po¾ádal o radu, jak vypoèítat prùseèík dvou 2D pøímek. Rozhodl jsem se, ¾e mu místo obecných matematických vzorcù po¹lu rovnou kompletní C++ kód. Nicménì mi trochu pøerostl pøes hlavu, a tak vznikla kompletní tøída pøímky v obecném tvaru. Kromì prùseèíku umí urèit i jejich vzájemnou polohu (rovnobì¾né, kolmé...), úhel, který svírají nebo vzdálenost libovolného bodu od pøímky. Doufám, ¾e tento mùj drobný úlet nebude moc vadit :-]</p>
</div>


<div class="object">
<div class="date">10.07.2003</div>
<h3>
<?OdkazWeb('cl_mat_geometrie', 'Analytická geometrie');?> - Michal Turek</h3>

<p>Tento èlánek vychází z mých zápiskù do matematiky z druhého roèníku na støední ¹kole. Jo, na diktování byla Wakuovka v¾dycky dobrá... Tehdy jsem moc nechápal k èemu mi tento obor matematiky vùbec bude, ale kdy¾ jsem se zaèal vìnovat OpenGL, záhy jsem pochopil. Zkuste si vzít napøíklad nìjaký pozdìj¹í NeHe Tutoriál. Bez znalostí 3D matematiky nemáte ¹anci. Doufám, ¾e vám tento èlánek pomù¾e alespoò se základy a pochopením principù.</p>
</div>


<div class="object">
<div class="date">22.07.2003</div>
<h3>
<?OdkazWeb('cl_mfc_dialog', 'OpenGL okno v dialogu');?> - Michal Turek</h3>

<p>Zobrazíme dìtské OpenGL okno v dialogu a budeme mu pøedávat hodnoty získané z ovládacích prvkù (editboxy a radiobuttony). Periodické pøekreslování OpenGL okna zaji¹»uje zpráva WM_TIMER - trojúhelník a ètverec budou rotovat.</p>
</div>


<div class="object">
<div class="date">23.06.2003</div>
<h3>
<?OdkazWeb('cl_mfc_tisk', 'Tisk a náhled pøed tiskem OpenGL scény');?> - Milan Turek</h3>

<p>Obalení OpenGL tøídami MFC nám dovolí vyu¾ít obou výhod API: rychlého vykreslování a elegantního rozhraní. Nicménì díky faktu, ¾e mnoho ovladaèù tiskáren nepracuje s API funkcí SetPixelFormat(), není mo¾né tisknout OpenGL scénu pøímo na tiskárnu. Velmi roz¹íøená technika je vykreslit OpenGL scénu do DIBu a poté ji zkopírovat do DC pro tisk nebo náhled. V tomto èlánku uvidíte jak to udìlat.</p>
</div>


<div class="object">
<div class="date">23.06.2003</div>
<h3>
<?OdkazWeb('cl_mfc_dib', 'Kopírování OpenGL okna do DIBu');?> - Milan Turek</h3>

<p>Obèas potøebujeme sejmout obrazovku v OpenGL a poté s ní pracovat jako s obyèejnou bitmapou. V tomto èlánku vám uká¾i získání obsahu OpenGL okna a jeho ulo¾ení do DIBu ve formì nekomprimované bitmapy. Jediný omezením mù¾e být 24 bitová barevná hloubka obrazovky.</p>
</div>


<div class="object">
<div class="date">23.06.2003</div>
<h3>
<?OdkazWeb('cl_wapi_setric', 'Jak na ¹etøiè obrazovky');?> - Michal Turek</h3>

<p>U¾ dávno jsem si chtìl naprogramovat vlastní ¹etøiè obrazovky. Mìl jsem sice tøídu CScreenSaverWnd pro MFC, ale ta nepodporovala OpenGL. U <?OdkazWeb('tut_38', 'NeHe Tutoriálu 38');?> jsem na¹el odkaz na ¹etøiè obrazovky s podporou OpenGL, který napsal Brian Hunsucker. Chtìl bych mu podìkovat, proto¾e na jeho zdrojovém kódu z vìt¹í èásti staví tento èlánek.</p>
</div>




<?
include 'p_end.php';
?>
