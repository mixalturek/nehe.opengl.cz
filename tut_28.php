<?
$g_title = 'CZ NeHe OpenGL - Lekce 28 - Bezierovy køivky a povrchy, fullscreen fix';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(28);?>

<h1>Lekce 28 - Bezierovy køivky a povrchy, fullscreen fix</h1>

<p class="nadpis_clanku">David Nikdel je osoba stojící za tímto skvìlým tutoriálem, ve kterém se nauèíte, jak se vytváøejí Bezierovy køivky. Díky nim lze velice jednodu¹e zakøivit povrch a provádìt jeho plynulou animaci pouhou modifikací nìkolika kontrolních bodù. Aby byl výsledný povrch modelu je¹tì zajímavìj¹í, je na nìj namapována textura. Tutoriál také eliminuje problémy s fullscreenem, kdy se po návratu do systému neobnovilo pùvodní rozli¹ení obrazovky.</p>

<p>Tento tutoriál je od zaèátku zamý¹len pouze jako úvod do Bezierových køivek, aby nìkdo mnohem ¹ikovnìj¹í ne¾ já dokázal vytvoøit nìco opravdu skvìlého. Neberte ho jako kompletní Bezier knihovnu, ale spí¹e jako koncept, jak tyto køivky pracují a co doká¾í. Také prosím omluvte mou, v nìkterých pøípadech, ne a¾ tak správnou terminologii. Doufám, ¾e bude alespoò trochu srozumitelná. Abych tak øekl: Nikdo není dokonalý...</p>

<p>Pochopit Bezierovy køivky s nulovými znalostmi matematiky je nemo¾né. Proto bude následovat malièko del¹í sekce teorie, která by vás mìla do problematiky alespoò trochu zasvìtit. Pokud v¹echno u¾ znáte, nic vám nebrání tuto nut(d)nou sekci pøeskoèit a vìnovat se kódu.</p>

<p>Bezierovy køivky bývají primární metodou, jak v grafických editorech èi obyèejných programech vykreslovat zakøivené linky. Jsou obvykle reprezentovány sérií bodù, z nich ka¾dé dva reprezentují teènu ke grafu funkce.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_28_krivka1.gif" width="260" height="110" alt="Bezierova køivka" /></div>

<p>Toto je nejjednodu¹¹í mo¾ná Bezierova køivka. Del¹í jsou tvoøeny spojením nìkolika dohromady. Je tvoøena pouze ètyømi body, dva konce a dva støedové kontrolní body. Pro poèítaè jsou v¹echny úplnì stejné, ale abychom si pomohli, spojujeme první a poslední dva. Linky budou v¾dy teènami k ukonèovacím bodùm. Parametrické køivky jsou kresleny nalezením libovolného poètu bodù rovnomìrnì rozprostøených po køivce, které se spojí èárami. Poètem bodù mù¾eme ovládat hranatost køivky a samozøejmì také dobu trvání výpoètù. Podaøí-li se nám mno¾ství bodù správnì regulovat, pozorovatel v ka¾dém okam¾iku uvidí perfektnì zakøivený povrch bez trhání animace.</p>

<p>V¹echny Bezierovy køivky jsou v zalo¾eny na základním vzorci funkce. Komplikovanìj¹í verze jsou z nìj odvozeny.</p>

<p class="src0">t + (1 - t) = 1</p>

<p>Vypadá jednodu¹e? Ano, rovnice jednoduchá urèitì je, ale nesmíme zapomenout na to, ¾e je to pouze Bezierova køivka prvního stupnì. Pou¾ijeme-li trochu terminologie: Bezierovy køivky jsou polynomiální (mnohoèlenné). Jak si zajisté pamatujete z algebry, první stupeò z polynomu je pøímka - nic zajímavého. Základní funkce vychází, dosadíme-li libovolné èíslo t. Rovnici mù¾eme ov¹em také mocnit na druhou, na tøetí, na jakékoli èíslo, proto¾e se obì strany rovnají jedné. Zkusíme ji tedy umocnit na tøetí.</p>

<p class="src0">(t + (1 - t))<sup>3</sup> = 1<sup>3</sup></p>
<p class="src0">t<sup>3</sup> + 3t<sup>2</sup>(1 - t) + 3t(1 - t)<sup>2</sup> + (1 - t)<sup>3</sup> = 1</p>

<p>Tuto rovnici pou¾ijeme k výpoètu mnohem více pou¾ívanìj¹í køivky - Bezierovy køivky tøetího stupnì. Pro toto rozhodnutí existují dva dùvody:</p>

<ul>
<li>Tento polynomiál je nejni¾¹ího mo¾ného stupnì, kdy u¾ køivka nemusí le¾et v rovinì, ale i v prostoru.</li>
<li>Teèny k funkci u¾ nejsou závislé na jiných (køivky 2. stupnì mohou mýt pouze tøi kontrolní body, my potøebujeme ètyøi).</li>
</ul>

<p>Zbývá ale dodat je¹tì jedna vìc... Celá levá strana rovnice se rovná jedné, tak¾e je bezpeèné pøedpokládat, ¾e pokud pøidáme v¹echny slo¾ky mìla by se stále rovnat jedné. Zní to, jako by to mohlo být pou¾ito k rozhodnutí kolik z ka¾dého kontrolního bodu lze pou¾ít pøi výpoètu bodu na køivce? (nápovìda: Prostì øekni ano ;-) Ano. Správnì! Pokud chceme spoèítat hodnotu bodu v procentech vzdálenosti na køivce, jednodu¹e násobíme ka¾dou slo¾ku kontrolním bodem (stejnì jako vektor) a nalezneme souèet. Obecnì budeme pracovat s hodnotami 0 &gt;= t &gt;= 1, ale není to technicky nutné. Dokonale zmateni? Radìji napí¹u tu funkci.</p>

<p class="src0">P1*t<sup>3</sup> + P2*3*t<sup>2</sup>*(1-t) + P3*3*t*(1-t)<sup>2</sup> + P4*(1-t)<sup>3</sup> = P<sub>new</sub></p>

<p>Proto¾e jsou polynomiály v¾dy spojité, jsou dobrou cestou k pohybu mezi ètyømi body. Mù¾eme dosáhnout ale v¾dy pouze okrajových bodù (P1 a P4). Pokud tuto vìtu nechápete, podívejte se na první obrázek. V tìchto pøípadech se t = 0 popø. t = 1.</p>

<p>To je sice hezké, ale jak mám pou¾ít Bezierovy køivky ve 3D? Je to docela jednoduché. Potøebujeme 16 kontrolních bodù (4x4) a dvì promìnné t a v. Vytvoøíme z nich ètyøi paralelní køivky. Na ka¾dé z nich spoèítáme jeden bod pøi urèitém v a pou¾ijeme tyto ètyøi body k vytvoøení nové køivky a spoèítáme t. Nalezením více bodù mù¾eme nakreslit triangle strip a tím zobrazit Bezierùv povrch.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_28_krivka2.gif" width="280" height="145" alt="Bezierùv povrch" />
<img src="images/nehe_tut/tut_28_krivka3.jpg" width="280" height="145" alt="Princip vytváøení Bezierova povrchu" />
</div>

<p>Pøepokládám, ¾e matematiky u¾ bylo dost. Pojïme se vrhnout na kód této lekce. ( Ze v¹eho nejdøíve vytvoøíme struktury. POINT_3D je obyèejný bod ve tøírozmìrném prostoru. Druhá struktura je u¾ trochu zajímavìj¹í - pøedstavuje Bezierùv povrch. Anchors[4][4] je dvourozmìrné pole 16 øídících bodù. Do display listu dlBPatch ulo¾íme výsledný model a texture ukládá texturu, kterou na nìj namapujeme.</p>

<p class="src0">typedef struct point_3d<span class="kom">// Struktura bodu</span></p>
<p class="src0">{</p>
<p class="src1">double x, y, z;</p>
<p class="src0">} POINT_3D;</p>
<p class="src"></p>
<p class="src0">typedef struct bpatch<span class="kom">// Struktura Bezierova povrchu</span></p>
<p class="src0">{</p>
<p class="src1">POINT_3D anchors[4][4];<span class="kom">// Møí¾ka øídících bodù (4x4)</span></p>
<p class="src1">GLuint dlBPatch;<span class="kom">// Display list</span></p>
<p class="src1">GLuint texture;<span class="kom">// Textura</span></p>
<p class="src0">} BEZIER_PATCH;</p>

<p>Mybezier je objektem právì vytvoøené textury, rotz kontroluje úhel natoèení scény. ShowCPoints indikuje, jestli vykreslujeme møí¾ku mezi øídícími body nebo ne. Divs urèuje hladkost (hranatost) výsledného povrchu.</p>

<p class="src0">BEZIER_PATCH mybezier;<span class="kom">// Bezierùv povrch</span></p>
<p class="src"></p>
<p class="src0">GLfloat rotz = 0.0f;<span class="kom">// Rotace na ose z</span></p>
<p class="src0">BOOL showCPoints = TRUE;<span class="kom">// Flag pro zobrazení møí¾ky mezi kontrolními body</span></p>
<p class="src0">int divs = 7;<span class="kom">// Poèet interpolací (mno¾ství vykreslovaných polygonù)</span></p>

<p>Jestli si pamatujete, tak v úvodu jsem psal, ¾e budeme malièko upravovat kód pro vytváøení okna tak, aby se pøi návratu z fullscreenu obnovilo pùvodní rozli¹ení obrazovky (nìkteré grafické karty s tím mají problémy). DMsaved ukládá pùvodní nastavení monitoru pøed vstupem do fullscreenu.</p>

<p class="src0">DEVMODE DMsaved;<span class="kom">// Ukládá pùvodní nastavení monitoru</span></p>

<p>Následuje nìkolik pomocných funkcí pro jednoduchou vektorovou matematiku. Sèítání, násobení a vytváøení 3D bodù. Nic slo¾itého.</p>

<p class="src0">POINT_3D pointAdd(POINT_3D p, POINT_3D q)<span class="kom">// Sèítání dvou bodù</span></p>
<p class="src0">{</p>
<p class="src1">p.x += q.x;</p>
<p class="src1">p.y += q.y;</p>
<p class="src1">p.z += q.z;</p>
<p class="src"></p>
<p class="src1">return p;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">POINT_3D pointTimes(double c, POINT_3D p)<span class="kom">// Násobení bodu konstantou</span></p>
<p class="src0">{</p>
<p class="src1">p.x *= c;</p>
<p class="src1">p.y *= c;</p>
<p class="src1">p.z *= c;</p>
<p class="src"></p>
<p class="src1">return p;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">POINT_3D makePoint(double a, double b, double c)<span class="kom">// Vytvoøení bodu ze tøí èísel</span></p>
<p class="src0">{</p>
<p class="src1">POINT_3D p;</p>
<p class="src"></p>
<p class="src1">p.x = a;</p>
<p class="src1">p.y = b;</p>
<p class="src1">p.z = c;</p>
<p class="src"></p>
<p class="src1">return p;</p>
<p class="src0">}</p>

<p>Funkcí Bernstein() poèítáme bod, který le¾í na Bezierovì køivce. V parametrech jí pøedáváme promìnnou u, která specifikuje procentuální vzdálenost bodu od okraje køivky vzhledem k její délce a pole ètyø bodù, které jednoznaènì definují køivku. Vícenásobným voláním a krokováním u v¾dy o stejný pøírùstek mù¾eme získat aproximaci køivky.</p>

<p class="src0">POINT_3D Bernstein(float u, POINT_3D *p)<span class="kom">// Spoèítá souøadnice bodu le¾ícího na køivce</span></p>
<p class="src0">{</p>
<p class="src1">POINT_3D a, b, c, d, r;<span class="kom">// Pomocné promìnné</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Výpoèet podle vzorce</span></p>
<p class="src1">a = pointTimes(pow(u,3), p[0]);</p>
<p class="src1">b = pointTimes(3 * pow(u,2) * (1-u), p[1]);</p>
<p class="src1">c = pointTimes(3 * u * pow((1-u), 2), p[2]);</p>
<p class="src1">d = pointTimes(pow((1-u), 3), p[3]);</p>
<p class="src"></p>
<p class="src1">r = pointAdd(pointAdd(a, b), pointAdd(c, d));<span class="kom">// Seètení násobkù a, b, c, d</span></p>
<p class="src1"></p>
<p class="src1">return r;<span class="kom">// Vrácení výsledného bodu</span></p>
<p class="src0">}</p>

<p>Nejvìt¹í èást práce odvádí funkce genBezier(). Spoèítá køivky, vygeneruje triangle strip a výsledek ulo¾í do display listu. Pou¾ití display listu je v tomto pøípadì více ne¾ vhodné, proto¾e nemusíme provádìt slo¾ité výpoèty pøi ka¾dém framu, ale pouze pøi zmìnách vy¾ádaných u¾ivatelem. Odstraní se tím zbyteèné zatí¾ení procesoru. Funkci pøedáváme strukturu BEZIER_PATCH, v ní¾ jsou ulo¾eny v¹echny potøebné øídící body. Divs urèuje kolikrát budeme provádìt výpoèty - ovládá hranatost výsledného modelu. Následující obrázky jsou získány pøepnutím do re¾imu vykreslování linek místo polygonù (glPolygonMode(GL_FRONT_AND_BACK, GL_LINES)) a zakázáním textur. Jasnì je vidìt, ¾e èím je èíslo v divs vìt¹í, tím je objekt zaoblenìj¹í.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_28_bezier1.gif" width="200" height="183" alt="Drátový model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier2.gif" width="200" height="183" alt="Drátový model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier3.gif" width="200" height="183" alt="Drátový model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier4.gif" width="200" height="183" alt="Drátový model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier5.gif" width="200" height="183" alt="Drátový model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier6.gif" width="200" height="183" alt="Drátový model Bezierova povrchu" />
</div>

<p class="src0">GLuint genBezier(BEZIER_PATCH patch, int divs)<span class="kom">// Generuje display list Bezierova povrchu</span></p>
<p class="src0">{</p>

<p>Promìnné u, v øídí cykly generující jednotlivé body na Bezierovì køivce a py, px, pyold jsou jejich procentuální hodnoty, které slou¾í k urèení místa na køivce. Nabývají hodnot v intervalu od 0 do 1, tak¾e je mù¾eme bez komplikací pou¾ít i jako texturovací koordináty. Drawlist je display list, do kterého kreslíme výsledný povrch. Do temp ulo¾íme ètyøi body pro získání pomocné Bezierovy køivky. Dynamické pole last ukládá minulý øádek bodù, proto¾e pro triangle strip potøebujeme dva øádky.</p>

<p class="src1">int u = 0, v;<span class="kom">// Øídící promìnné</span></p>
<p class="src1">float py, px, pyold;<span class="kom">// Procentuální hodnoty</span></p>
<p class="src"></p>
<p class="src1">GLuint drawlist = glGenLists(1);<span class="kom">// Display list</span></p>
<p class="src"></p>
<p class="src1">POINT_3D temp[4];<span class="kom">// Øídící body pomocné køivky</span></p>
<p class="src1">POINT_3D* last = (POINT_3D*) malloc(sizeof(POINT_3D) * (divs+1));<span class="kom">// První øada polygonù</span></p>
<p class="src"></p>
<p class="src1">if (patch.dlBPatch != NULL)<span class="kom">// Pokud existuje starý display list</span></p>
<p class="src2">glDeleteLists(patch.dlBPatch, 1);<span class="kom">// Sma¾eme ho</span></p>
<p class="src"></p>
<p class="src1">temp[0] = patch.anchors[0][3];<span class="kom">// První odvozená køivka (osa x)</span></p>
<p class="src1">temp[1] = patch.anchors[1][3];</p>
<p class="src1">temp[2] = patch.anchors[2][3];</p>
<p class="src1">temp[3] = patch.anchors[3][3];</p>
<p class="src"></p>
<p class="src1">for (v = 0; v &lt;= divs; v++)<span class="kom">// Vytvoøí první øádek bodù</span></p>
<p class="src1">{</p>
<p class="src2">px = ((float)v) / ((float)divs);<span class="kom">// Px je procentuální hodnota v</span></p>
<p class="src2">last[v] = Bernstein(px, temp);<span class="kom">// Spoèítá bod na køivce ve vzdálenosti px</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glNewList(drawlist, GL_COMPILE);<span class="kom">// Nový display list</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, patch.texture);<span class="kom">// Zvolí texturu</span></p>

<p>Vnìj¹í cyklus prochází øádky a vnitøní jednotlivé sloupce. Nebo to mù¾e být i naopak. Zále¾í na tom, co si ka¾dý pøedstaví pod pojmy øádek a sloupec :-)</p>

<p class="src2">for (u = 1; u &lt;= divs; u++)<span class="kom">// Prochází body na køivce</span></p>
<p class="src2">{</p>
<p class="src3">py  = ((float)u) / ((float)divs);<span class="kom">// Py je procentuální hodnota u</span></p>
<p class="src3">pyold = ((float)u - 1.0f) / ((float)divs);<span class="kom">// Pyold má hodnotu py pøi minulém prùchodu cyklem</span></p>

<p>V ka¾dém prvku pole patch.anchors[] máme ulo¾eny ètyøi øídící body (dvourozmìrné pole). Celé pole dohromady tvoøí ètyøi paralelní køivky, které si oznaèíme jako øádky. Nyní spoèítáme body, které jsou umístìny na v¹ech ètyøech køivkách ve stejné vzdálenosti py a ulo¾íme je do pole temp[], které pøedstavuje sloupec v øádku a celkovì tvoøí ètyøi øídící body nové køivky pro sloupec.</p>

<p>Celou akci si pøedstavte jako trochu komplikovanìj¹í procházení dvourozmìrného pole - vnìj¹í cyklus prochází øádky a vnitøní sloupce. Z upravených øídících promìnných si vybíráme pozice bodù a texturovací koordináty. Py s pyold pøedstavuje dva &quot;rovnìbì¾né&quot; øádky a px sloupec. (Pøekl.: Ne¾ jsem tohle pochopil... v originále o tom nebyla ani zmínka).</p>

<p class="src3">temp[0] = Bernstein(py, patch.anchors[0]);<span class="kom">// Spoèítá Bezierovy body pro køivku</span></p>
<p class="src3">temp[1] = Bernstein(py, patch.anchors[1]);</p>
<p class="src3">temp[2] = Bernstein(py, patch.anchors[2]);</p>
<p class="src3">temp[3] = Bernstein(py, patch.anchors[3]);</p>
<p class="src"></p>
<p class="src3">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Zaèátek kreslení triangle stripu</span></p>
<p class="src4">for (v = 0; v &lt;= divs; v++)<span class="kom">// Prochází body na køivce</span></p>
<p class="src4">{</p>
<p class="src5">px = ((float)v) / ((float)divs);<span class="kom">// Px je procentuální hodnota v</span></p>
<p class="src"></p>
<p class="src5">glTexCoord2f(pyold, px);<span class="kom">// Texturovací koordináty z minulého prùchodu</span></p>
<p class="src5">glVertex3d(last[v].x, last[v].y, last[v].z);<span class="kom">// Bod z minulého prùchodu</span></p>

<p>Do pole last nyní ulo¾íme nové hodnoty, které se pøi dal¹ím prùchodu cyklem stanou opìt starými.</p>

<p class="src5">last[v] = Bernstein(px, temp);<span class="kom">// Generuje nový bod</span></p>
<p class="src"></p>
<p class="src5">glTexCoord2f(py, px);<span class="kom">// Nové texturové koordináty</span></p>
<p class="src5">glVertex3d(last[v].x, last[v].y, last[v].z);<span class="kom">// Nový bod</span></p>
<p class="src4">}</p>
<p class="src3">glEnd();<span class="kom">// Konec triangle stripu</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src1">glEndList();<span class="kom">// Konec display listu</span></p>
<p class="src"></p>
<p class="src1">free(last);<span class="kom">// Uvolní dynamické pole vertexù</span></p>
<p class="src1">return drawlist;<span class="kom">// Vrátí právì vytvoøený display list</span></p>
<p class="src0">}</p>

<p>Jediná vìc, kterou nedìláme, ale která by se urèitì mohla hodit, jsou normálové vektory pro svìtlo. Kdy¾ na nì pøijde, máme dvì mo¾nosti. V první nalezneme støed ka¾dého trojúhelníku, aplikujeme na nìj nìkolik výpoètu k získáním teèen k Bezierovì køivce na osách x a y, vektorovì je vynásobíme a tím získáme vektor kolmý souèasnì k obìma teènám. Po normalizování ho mù¾eme pou¾ít jako normálu. Druhý zpùsob je rychlej¹í a jednodu¹¹í, ale ménì pøesný. Mù¾eme cheatovat a pou¾ít normálový vektor trojúhelníku (spoèítaný libovolným zpùsobem). Tím získáme docela dobrou aproximaci. Osobnì preferuji druhou, jednodu¹¹í cestu, která ov¹em nevypadá tak realistiky.</p>

<p>Ve funkci initBezier() inicializujeme matici kontrolních bodù na výchozí hodnoty. Pohrajte si s nimi, a» vidíte, jak jednodu¹e se dají mìnit tvary povrchù.</p>

<p class="src0">void initBezier(void)<span class="kom">// Poèáteèní nastavení kontrolních bodù</span></p>
<p class="src0">{</p>
<p class="src1">mybezier.anchors[0][0] = makePoint(-0.75,-0.75,-0.5);</p>
<p class="src1">mybezier.anchors[0][1] = makePoint(-0.25,-0.75, 0.0);</p>
<p class="src1">mybezier.anchors[0][2] = makePoint( 0.25,-0.75, 0.0);</p>
<p class="src1">mybezier.anchors[0][3] = makePoint( 0.75,-0.75,-0.5);</p>
<p class="src1">mybezier.anchors[1][0] = makePoint(-0.75,-0.25,-0.75);</p>
<p class="src1">mybezier.anchors[1][1] = makePoint(-0.25,-0.25, 0.5);</p>
<p class="src1">mybezier.anchors[1][2] = makePoint( 0.25,-0.25, 0.5);</p>
<p class="src1">mybezier.anchors[1][3] = makePoint( 0.75,-0.25,-0.75);</p>
<p class="src1">mybezier.anchors[2][0] = makePoint(-0.75, 0.25, 0.0);</p>
<p class="src1">mybezier.anchors[2][1] = makePoint(-0.25, 0.25,-0.5);</p>
<p class="src1">mybezier.anchors[2][2] = makePoint( 0.25, 0.25,-0.5);</p>
<p class="src1">mybezier.anchors[2][3] = makePoint( 0.75, 0.25, 0.0);</p>
<p class="src1">mybezier.anchors[3][0] = makePoint(-0.75, 0.75,-0.5);</p>
<p class="src1">mybezier.anchors[3][1] = makePoint(-0.25, 0.75,-1.0);</p>
<p class="src1">mybezier.anchors[3][2] = makePoint( 0.25, 0.75,-1.0);</p>
<p class="src1">mybezier.anchors[3][3] = makePoint( 0.75, 0.75,-0.5);</p>
<p class="src"></p>
<p class="src1">mybezier.dlBPatch = NULL;<span class="kom">// Display list je¹tì neexistuje</span></p>
<p class="src0">}</p>

<p>InitGL() je celkem standardní. Na jejím konci zavoláme funkce pro inicializaci kontrolních bodù, nahrání textury a vygenerování display listu Bezierova povrchu.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturování</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testování hloubky</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivní korekce</span></p>
<p class="src"></p>
<p class="src1">initBezier();<span class="kom">// Inicializace kontrolních bodù</span></p>
<p class="src1">LoadGLTexture(&amp;(mybezier.texture), &quot;./data/NeHe.bmp&quot;);<span class="kom">// Loading textury</span></p>
<p class="src1">mybezier.dlBPatch = genBezier(mybezier, divs);<span class="kom">// Generuje display list Bezierova povrchu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace v poøádku</span></p>
<p class="src0">}</p>

<p>Vykreslování není oproti minulým tutoriálùm vùbec slo¾ité. Po v¹ech translacích a rotacích zavoláme display list a potom pøípadnì propojíme øídící body èervenými èarami. Chcete-li linky zapnout nebo vypnout stisknìte mezerník.</p>


<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// V¹echno kreslení</span></p>
<p class="src0">{</p>
<p class="src1">int i, j;<span class="kom">// Øídící promìnné cyklù</span></p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -4.0f);<span class="kom">// Pøesun do hloubky</span></p>
<p class="src1">glRotatef(-75.0f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(rotz, 0.0f, 0.0f, 1.0f);<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src1">glCallList(mybezier.dlBPatch);<span class="kom">// Vykreslí display list Bezierova povrchu</span></p>
<p class="src"></p>
<p class="src1">if (showCPoints)<span class="kom">// Pokud je zapnuté vykreslování møí¾ky</span></p>
<p class="src1">{</p>
<p class="src2">glDisable(GL_TEXTURE_2D);<span class="kom">// Vypne texturování</span></p>
<p class="src2">glColor3f(1.0f, 0.0f, 0.0f);<span class="kom">// Èervená barva</span></p>
<p class="src"></p>
<p class="src2">for(i = 0; i &lt; 4; i++)<span class="kom">// Horizontální linky</span></p>
<p class="src2">{</p>
<p class="src3">glBegin(GL_LINE_STRIP);<span class="kom">// Kreslení linek</span></p>
<p class="src4">for(j = 0; j &lt; 4; j++)<span class="kom">// Ètyøi linky</span></p>
<p class="src4">{</p>
<p class="src5">glVertex3d(mybezier.anchors[i][j].x, mybezier.anchors[i][j].y, mybezier.anchors[i][j].z);</p>
<p class="src4">}</p>
<p class="src3">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">for(i = 0; i &lt; 4; i++)<span class="kom">// Vertikální linky</span></p>
<p class="src2">{</p>
<p class="src3">glBegin(GL_LINE_STRIP);<span class="kom">// Kreslení linek</span></p>
<p class="src4">for(j = 0; j &lt; 4; j++)<span class="kom">// Ètyøi linky</span></p>
<p class="src4">{</p>
<p class="src5">glVertex3d(mybezier.anchors[j][i].x, mybezier.anchors[j][i].y, mybezier.anchors[j][i].z);</p>
<p class="src4">}</p>
<p class="src3">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">glColor3f(1.0f, 1.0f, 1.0f);<span class="kom">// Bílá barva</span></p>
<p class="src2">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturování</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V poøádku</span></p>
<p class="src0">}</p>

<p>Práci s Bezierovými køivkami jsme úspì¹nì dokonèili, ale je¹tì nesmíme zapomenout na fullscreen fix. Odstraòuje problém s pøepínám z fullscreenu do okenního módu, kdy nìkteré grafické karty správnì neobnovují pùvodní rozli¹ení obrazovky (napø. moje staøièká ATI Rage PRO a nìkolik dal¹ích). Doufám, ¾e budete pou¾ívat tento pozmìnìný kód, aby si ka¾dý mohl bez komplikací vychutnat va¹e skvìlá OpenGL dema. V tutoriálu jsme provedli celkem tøi zmìny. První pøi deklaraci promìnných, kdy jsme vytvoøili promìnnou DEVMODE DMsaved. Druhou najdete v CreateGLWindow(), kde jsme tuto pomocnou strukturu naplnili informacemi o aktuálním nastavení. Tøetí zmìna je v KillGLWindow(), kde se obnovuje pùvodní ulo¾ené nastavení.</p>

<p class="src0">BOOL CreateGLWindow(char* title, int width, int height, int bits, bool fullscreenflag)<span class="kom">// Vytváøení okna</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Deklarace promìnných</span></p>
<p class="src"></p>
<p class="src1">EnumDisplaySettings(NULL, ENUM_CURRENT_SETTINGS, &amp;DMsaved);<span class="kom">// Ulo¾í aktuální nastavení obrazovky</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// V¹e ostatní zùstává stejné</span></p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">GLvoid KillGLWindow(GLvoid)<span class="kom">// Zavøení okna</span></p>
<p class="src0">{</p>
<p class="src1">if (fullscreen)<span class="kom">// Jsme ve fullscreenu?</span></p>
<p class="src1">{</p>
<p class="src2">if (!ChangeDisplaySettings(NULL, CDS_TEST))<span class="kom">// Pokud pokusná zmìna nefunguje</span></p>
<p class="src2">{</p>
<p class="src3">ChangeDisplaySettings(NULL, CDS_RESET);<span class="kom">// Odstraní hodnoty z registrù</span></p>
<p class="src3">ChangeDisplaySettings(&amp;DMsaved, CDS_RESET);<span class="kom">// Pou¾ije ulo¾ené nastavení</span></p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">ChangeDisplaySettings(NULL, CDS_RESET);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">ShowCursor(TRUE);<span class="kom">// Zobrazí ukazatel my¹i</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// V¹e ostatní zùstává stejné</span></p>
<p class="src0">}</p>

<p>Poslední vìcí jsou u¾ standardní testy stisku kláves.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src3">if (keys[VK_LEFT])<span class="kom">// ©ipka doleva</span></p>
<p class="src3">{</p>
<p class="src4">rotz -= 0.8f;<span class="kom">// Rotace doleva</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_RIGHT])<span class="kom">// ©ipka doprava</span></p>
<p class="src3">{</p>
<p class="src4">rotz += 0.8f;<span class="kom">// Rotace doprava</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_UP])<span class="kom">// ©ipka nahoru</span></p>
<p class="src3">{</p>
<p class="src4">divs++;<span class="kom">// Men¹í hranatost povrchu</span></p>
<p class="src4">mybezier.dlBPatch = genBezier(mybezier, divs);<span class="kom">// Aktualizace display listu</span></p>
<p class="src4">keys[VK_UP] = FALSE;</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_DOWN] &amp;&amp; divs &gt; 1)<span class="kom">// ©ipka dolù</span></p>
<p class="src3">{</p>
<p class="src4">divs--;<span class="kom">// Vìt¹í hranatost povrchu</span></p>
<p class="src4">mybezier.dlBPatch = genBezier(mybezier, divs);<span class="kom">// Aktualizace display listu</span></p>
<p class="src4">keys[VK_DOWN] = FALSE;</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_SPACE])<span class="kom">// Mezerník</span></p>
<p class="src3">{</p>
<p class="src4">showCPoints = !showCPoints;<span class="kom">// Zobrazí/skryje linky mezi øídícími body</span></p>
<p class="src4">keys[VK_SPACE] = FALSE;</p>
<p class="src3">}</p>

<p>Doufám, ¾e pro vás byl tento tutoriál pouèný a ¾e od nynìj¹ka miluje Bezierovy køivky stejnì jako já ;-) Je¹tì jsem se o tom nezmínil, ale mnohé z vás jistì napadlo, ¾e se s nimi dá vytvoøit perfektní morfovací efekt. A velmi jednodu¹e! Nezapomeòte, se mìní poloha pouze ¹estnácti bodù. Zkuste o tom popøemý¹let...</p>

<p class="autor">napsal: David Nikdel <?VypisEmail('ogapo@ithink.net');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul>
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson28.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson28_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson28.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson28.zip">Delphi</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson28.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson28.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson28.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson28.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:rodolphe.suescun@wanadoo.fr">Rodolphe Suescun</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson28.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson28.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(28);?>
<?FceNeHeOkolniLekce(28);?>

<?
include 'p_end.php';
?>
