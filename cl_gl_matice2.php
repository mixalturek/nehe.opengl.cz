<?
$g_title = 'CZ NeHe OpenGL - Matice v OpenGL';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Matice v OpenGL</h1>

<p class="nadpis_clanku">V tomto èlánku se dozvíte, jakým zpùsobem OpenGL ukládá hodnoty rotací a translací do své modelview matice. Samozøejmì nebudou chybìt obrázky jejího obsahu po rùzných maticových operacích.</p>

<p>Pøedem chci upozornit, ¾e v¹echno vysvìtlím tak, jak tomu rozumím já, tedy neodbornì. Doufám, ¾e dané problematice pùjde z mého výkladu snadno porozumìt. Také neruèím za to, ¾e to pøesnì takto má být, proto¾e jsem si daný postup vymyslel (= odvodil z nìèeho úplnì jiného). Ale funguje, tak co :-).</p>

<p>Zaèneme teorií. Vysvìtlím, kde je co v modelview matici umístìno a pak se pokusím vysvìtlit, jak v¹e dohromady funguje. Jistì víte, ¾e v OpenGL má matice velikost 4x4. Po slo¾itém výpoètu tedy získáme celkem 16 indexù pole. Ale pozor, matice není ulo¾ena jako dvourozmìrná, nýbr¾ jednorozmìrnì. V programu potøebujeme vytvoøit pole o 16 prvcích buï typu float nebo double. Osobnì pou¾ívám float, proto¾e má dostateènou pøesnost, práce s ním je rychlej¹í a zabírá ménì pamìti.</p>

<p class="src0">float Matrix[16];<span class="kom">// OpenGL matice</span></p>

<p>Na následujícím obrázku je znázornìno rozmístìní indexù do 1D pole ve 2D matici, kterou si pro zpøehlednìní pøedstavujeme.</p>

<div class="okolo_img"><img src="images/clanky/matice2/index.gif" width="400" height="200" alt="Indexy v matici" /></div>

<p>Teï si uká¾eme, co se na který index ukládá a k èemu slou¾í. Hodnoty Move oznaèují posun objektu v osách X, Y, Z a Rot èísla definují rotace, kde je pro ka¾dou pùvodní osu (velké písmeno X, Y, Z) pomìr jejího &quot;pøenesení&quot; na osy jiné.</p>

<div class="okolo_img"><img src="images/clanky/matice2/vyznam.jpg" width="400" height="200" alt="Význam indexù v matici" /></div>

<p>Myslím si, ¾e jak zacházet s posunem je ka¾dému jasné, ale rotace je alespoò na první pochopení slo¾itá. Pøedstavme si, ¾e velká písmena X, Y, Z definují pùvodní osu a malá písmena x, y, z oznaèují osu, na kterou se ta pùvodní pøetransformuje. Pro zaèátek si uká¾eme, jak vypadá originální matice, která zobrazuje objekt nenatoèený, neposunutý a ani nezmen¹ený. Kdy¾ s ní násobíme bod, je to, jako bychom ho násobili jednièkou. Jeho souøadnice se tedy nezmìní. Tuto matici v OpenGL generuje funkce glLoadIdentity().</p>

<p>V¹imnìte si, ¾e ve v¹ech Rot jsou jednièky na hlavní diagonále matice. Tedy tam, kde se shoduje malé a velké písmeno - Xx, Yy, Zz. Ve výsledném zobrazení budou mít osy stejné mìøítko, polohu i natoèení jako má absolutní soustava souøadnic.</p>

<p class="src0">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<div class="okolo_img"><img src="images/clanky/matice2/reset.jpg" width="400" height="200" alt="Hodnoty v matici po resetu" /></div>

<p>Teï si uká¾eme, co se stane, kdybychom chtìli objekt otoèit o 90° na ose Y.</p>

<p class="src0">glLoadIdentity();</p>
<p class="src0">glRotatef(90.0f, 0.0f, 1.0f, 0.0f);</p>

<div class="okolo_img"><img src="images/clanky/matice2/roty90.jpg" width="400" height="200" alt="Hodnoty v matici po rotaci o 90 stupòù na ose y" /></div>

<p>Tak u¾ to bývá na svìtì, ¾e viník uniká bez trestu a v¹ichni okolo to schytají. Ano, vidíte dobøe, s osou Y se nic nestalo, proto¾e jsme se okolo ní otoèili o 90°. Kdy¾ si to pøedstavíme v prostoru: co se stane s bodem na ose x, pokud pøed vykreslením provedeme rotaci okolo osy Y o 90°? Z osy X se pøesune na osu Z. Kdy¾ se podíváte, pùvodní osa X (velké písmeno) se dostala na osu z (malé písmeno). To samé se stalo s pùvodní osou Z která se dostala na zápornou èást osy x. Pokud bychom rotovali opaèným smìrem, tedy o -90° na ose Y, v RotXz by byla hodnota -1 a v RotZx by bylo 1, co¾ je pøesný opak pøedchozího pøípadu.</p>

<p>Pro ty z vás, kteøí si nedoká¾í pøedstavit, jak by se mohla jedna osa pøenést na jinou, si uká¾eme obrázek, na kterém jde v¹echno bez problémù vidìt.</p>

<div class="okolo_img"><img src="images/clanky/matice2/3dosy.jpg" width="464" height="177" alt="Poloha souøadnicových os" /></div>

<p>U¾ chápete, jak se dostal bod z jedné osy na druhou? Stejnì to funguje i s maticemi. Ka¾dou ze tøí os si mù¾eme pøedstavit jako skupinu bodù v prostoru, které otáèíme o nìjaký úhel. Pøesnì takto jsem se dopátral k funkènímu kódu.</p>

<p>Je samozøejmì jasné, ¾e se v¹e neotáèí jen o 90°, ale musel jsem to nìjak vysvìtlit. Jen pro ukázku se podívejme, co se stane pokud rotujeme okolo osy Y o 45°.</p>

<p class="src0">glLoadIdentity();</p>
<p class="src0">glRotatef(45.0f, 0.0f, 1.0f, 0.0f);</p>

<div class="okolo_img"><img src="images/clanky/matice2/roty45.jpg" width="400" height="200" alt="Hodnoty v matici po rotaci o 45 stupòù" /></div>

<p>Asi vás nìkteré napadlo, ¾e kdy¾ je úhel polovièní ne¾ pøedtím, proè na indexech nejsou hodnoty 0,5 namísto 0,707. Je to proto, ¾e výpoèet tìchto hodnot provádíme pomocí goniometrických funkcí sin a cos a ty, jak víme, nejsou lineární. Pokud by byly, docházelo by k deformacím obrazu, zmen¹ování atd... Pøedstavte si kru¾nici vykreslenou pomocí sin a cos. Kdyby byly tyto funkce lineární, vznikl by ètverec. Otáèíme-li body v rovinì, pohybujeme se po kru¾nici, ve 3D prostoru po kouli.</p>

<p>Nesmíme zapomenout na zmìnu mìøítka. Následující obrázek ukazuje, jak bude matice vypadat po jednonásobném zvìt¹ení na ose x (zùstává stejná), dvojnásobném na ose y a trojnásobném na ose z. V OpenGL by se to provedlo voláním funkce glScalef().</p>

<p class="src0">glLoadIdentity();</p>
<p class="src0">glScalef(1.0f, 2.0f, 3.0f);</p>

<div class="okolo_img"><img src="images/clanky/matice2/scale.jpg" width="400" height="200" alt="Zmìna mìøítka" /></div>

<p>Poslední pøíklad ukazuje souèasnou zmìnu mìøítka a rotaci na ose y o 45°.</p>

<p class="src0">glLoadIdentity();</p>
<p class="src0">glScalef(1.0f, 2.0f, 3.0f);</p>
<p class="src0">glRotatef(45.0f, 0.0f, 1.0f, 0.0f);</p>

<div class="okolo_img"><img src="images/clanky/matice2/scale_rot.jpg" width="400" height="200" alt="Zmìna mìøítka a rotace" /></div>

<p>To je snad v¹e o teorii a teï honem ke zdrojovým kódùm - slíbené funkce pro rotaci. Musím jen dodat, ¾e nejsou shodné s OpenGL glRotatef(), ve které se objekt po posunutí otáèí podle støedu scény, transformuje tedy i svou pozici. Tyto funkce to nedìlají a myslím, ¾e je to tak lep¹í (zále¾í na zvyku). Pokud potøebujete pøetransformovat i pozici (napøíklad kdy¾ je pøipevnìn k jinému objektu ve scénì), jednodu¹e si vypoèítáte rotaci a pak pozici vynásobíte maticí stejnì jako normální bod v prostoru.</p>

<p class="src0">void RV6_MATRIX::RotateX(float Angle)<span class="kom">// Rotace na ose x</span></p>
<p class="src0">{</p>
<p class="src1">float p;</p>
<p class="src1">float _sin = sinf(-Angle);</p>
<p class="src1">float _cos = cosf(-Angle);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa X na X</span></p>
<p class="src1">p = Matrix[4];</p>
<p class="src1">Matrix[4] = p * _cos - Matrix[8] * _sin;</p>
<p class="src1">Matrix[8] = p * _sin + Matrix[8] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa X na Y</span></p>
<p class="src1">p = Matrix[5];</p>
<p class="src1">Matrix[5] = p * _cos - Matrix[9] * _sin;</p>
<p class="src1">Matrix[9] = p * _sin + Matrix[9] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa X na Z</span></p>
<p class="src1">p = Matrix[6];</p>
<p class="src1">Matrix[6] = p * _cos - Matrix[10] * _sin;</p>
<p class="src1">Matrix[10] = p * _sin + Matrix[10] * _cos;</p>
<p class="src0">}</p>

<p class="src"></p>

<p class="src0">void RV6_MATRIX::RotateY(float Angle)<span class="kom">// Rotace na ose y</span></p>
<p class="src0">{</p>
<p class="src1">float p, _sin = sinf(Angle), _cos = cosf(Angle);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Y na X</span></p>
<p class="src1">p = Matrix[0];</p>
<p class="src1">Matrix[0] = p * _cos - Matrix[8] * _sin;</p>
<p class="src1">Matrix[8] = p * _sin + Matrix[8] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Y na Y</span></p>
<p class="src1">p = Matrix[1];</p>
<p class="src1">Matrix[1] = p * _cos - Matrix[9] * _sin;</p>
<p class="src1">Matrix[9] = p * _sin + Matrix[9] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Y na Z</span></p>
<p class="src1">p = Matrix[2];</p>
<p class="src1">Matrix[2] = p * _cos - Matrix[10] * _sin;</p>
<p class="src1">Matrix[10] = p * _sin + Matrix[10] * _cos;</p>
<p class="src0">}</p>

<p class="src"></p>

<p class="src0">void RV6_MATRIX::RotateZ(float Angle)<span class="kom">// Rotace na ose z</span></p>
<p class="src0">{</p>
<p class="src1">float p, _sin = sinf(-Angle), _cos = cosf(-Angle);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Z na X</span></p>
<p class="src1">p = Matrix[0];</p>
<p class="src1">Matrix[0] = p * _cos - Matrix[4] * _sin;</p>
<p class="src1">Matrix[4] = p * _sin + Matrix[4] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Z na X</span></p>
<p class="src1">p = Matrix[1];</p>
<p class="src1">Matrix[1] = p * _cos - Matrix[5] * _sin;</p>
<p class="src1">Matrix[5] = p * _sin + Matrix[5] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Z na X</span></p>
<p class="src1">p = Matrix[2];</p>
<p class="src1">Matrix[2] = p * _cos - Matrix[6] * _sin;</p>
<p class="src1">Matrix[6] = p * _sin + Matrix[6] * _cos;</p>
<p class="src0">}</p>

<p class="src"></p>

<p class="src0">void RV6_MATRIX::Rotate(float x, float y, float z)<span class="kom">// Hlavní funkce pro rotaci</span></p>
<p class="src0">{</p>
<p class="src1">if(x)</p>
<p class="src1">{</p>
<p class="src2">RotateX(x);</p>
<p class="src1">}</p>
<p class="src1">if(y)</p>
<p class="src1">{</p>
<p class="src2">RotateY(y);</p>
<p class="src1">}</p>
<p class="src1">if(z)</p>
<p class="src1">{</p>
<p class="src2">RotateZ(z);</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>To, co vidíte jsou probdìlé noci, vyplakané litry slz, pìtky ve ¹kole... Jak vidíte je to úryvek ze tøídy, která obsahuje jen pole o 16 prvcích se jménem Matrix. Toto muèení by bylo na nic, kdyby se nedalo nìjak pou¾ít. Proto zde uvedu dal¹í funkci, která podle matice pøetransformuje vertex èi vektor (u vektoru je ale lep¹í vytvoøit funkci, ve které se nebudou pøièítat pozice) na absolutní souøadnice. Jen pro poøádek, tøída RV6_VECTOR3 obsahuje tøi èísla x, y, z.</p>

<p class="src0">void RV6_MATRIX::TransformVertex(RV6_VECTOR3 *Vertex)<span class="kom">// Transformuje vertex podle matice</span></p>
<p class="src0">{</p>
<p class="src1">RV6_VECTOR3 New;</p>
<p class="src"></p>
<p class="src1">New.x = Vertex->x * Matrix[0] + Vertex->y * Matrix[4] + Vertex->z * Matrix[8] + Matrix[12];</p>
<p class="src1">New.y = Vertex->x * Matrix[1] + Vertex->y * Matrix[5] + Vertex->z * Matrix[9] + Matrix[13];</p>
<p class="src1">New.z = Vertex->x * Matrix[2] + Vertex->y * Matrix[6] + Vertex->z * Matrix[10] + Matrix[14];</p>
<p class="src"></p>
<p class="src1">*Vertex = New;</p>
<p class="src0">}</p>

<p>Dal¹í dùle¾itou vìcí je nahrání na¹í matice do OpenGL. K tomu slou¾í standardní funkce glLoadMatrixf(). Poslední písmeno v jejím názvu znamená float, pokud po¾íváte double musíte jej zamìnit za d.</p>

<p class="src0">glLoadMatrixf(Matrix);<span class="kom">// Uploadování matice do OpenGL</span></p>

<p>Pokud chcete naopak naèíst matici, pou¾ijte funkci glGetFloatv(). První parametr oznaèuje, kterou matici ¾ádáme (GL_MODELVIEW_MATRIX, GL_PROJECTION_MATRIX nebo GL_TEXTURE_MATRIX) a druhý pole, kam se mají data ulo¾it.</p>

<p class="src0">glGetFloatv(GL_MODELVIEW_MATRIX, Matrix);<span class="kom">// Získání OpenGL matice</span></p>

<p>Kdy¾ si to trochu shrneme... nikdy nezapomeòte pøed první operací uvést matici do základního stavu, kdy jsou na hlavní diagonále jednièky a v¹ude jinde nuly, jinak by se vám nic nezobrazovalo a ani nerotovalo. Dále chcete asi vìdìt k èemu slou¾í poslední sloupec v matici. Pøiznám se, ¾e nevím, ale prostì tam je. Ve ¹kole jsme matice je¹tì nebrali, v¹e jsem zkoumal grabováním hodnot z OpenGL a jejich výpisem do souboru.</p>

<p>To je snad v¹e k maticím, jestli jim je¹tì nechápete, zkuste si tento èlánek pøeèíst je¹tì jednou (pozor na nekoneèný cyklus :-) nebo donu»te nìkoho a» vám vysvìtlí co a jak. Já u¾ asi lépe vysvìtlovat nedoká¾i. Doufám, ¾e jsem alespoò nìkomu pomohl.</p>

<p class="autor">napsal: Radomír Vrána <?VypisEmail('rvalien@c-box.cz?subject=Èlánek - Matice');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojových kódù</li>
</ul>

<p>Tento èlánek byl napsán pro web <?OdkazBlank('http://nehe.ceske-hry.cz/');?>. Pokud ho chcete umístit i na svoje stránky, napøed se zeptejte autora, je to slu¹nost.</p>

<?
include 'p_end.php';
?>
