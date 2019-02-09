<?
$g_title = 'CZ NeHe OpenGL - Lekce 48 - ArcBall rotace';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(48);?>

<h1>Lekce 48 - ArcBall rotace</h1>

<p class="nadpis_clanku">Nebylo by skvìlé otáèet modelem pomocí my¹i jednoduchým drag &amp; drop? S ArcBall rotacemi je to mo¾né. Moje implementace je zalo¾ená na my¹lenkách Brettona Wadea a Kena Shoemakea. Kód také obsahuje funkci pro rendering toroidu - kompletnì i s normálami.</p>

<p class="netisk"><a href="tut_48_sk.php">Verze ve sloven¹tinì...</a></p>

<p>ArcBall funguje tak, ¾e mapuje okenní souøadnice kliknutí pøímo do souøadnic ArcBallu. Zmen¹í pomìr souøadnic my¹i z rozsahu [0..¹íøka, 0..vý¹ka] na rozsah [-1..1, 1..-1]. Pamatujte si, ¾e aby v OpenGL dosáhl korektního výsledku, musí pøevrátit znamínko y souøadnice. Vzorec vypadá takto:</p>

<p class="src0"><span class="kom">MousePt.X  =  ((MousePt.X / ((Width  - 1) / 2)) - 1);</span></p>
<p class="src0"><span class="kom">MousePt.Y  = -((MousePt.Y / ((Height - 1) / 2)) - 1);</span></p>

<p>Jediný dùvod, proè jsme mìnili mìøítko souøadnic je, abychom zjednodu¹ili matematiku, nicménì ¹»astnou shodou okolností to dovoluje kompilátoru kód trochu optimalizovat. Dále vypoèítáme délku vektoru a urèíme, jestli se nachází nebo nenachází uvnitø koule. Pokud ano, vrátíme vektor z jejího vnitøku, jinak normalizujeme bod a vrátíme nejbli¾¹í pozici k vnìj¹ku koule. Poté, co máme oba vektory, získáme vektor souèasnì kolmý na poèáteèní i koncový vektor, èím¾ dostaneme quaternion. S tímto v rukách máme dost informací na vygenerování rotaèní matice.</p>

<p>Konstruktoru tøídy ArcBall budeme pøedávat rozmìry okna.</p>

<p class="src0">ArcBall_t::ArcBall_t(GLfloat NewWidth, GLfloat NewHeight)</p>

<p>Kdy¾ u¾ivatel klikne my¹í, vypoèítáme poèáteèní vektor podle toho, kam kliknul.</p>

<p class="src0">void ArcBall_t::click(const Point2fT* NewPt)</p>

<p>Kdy¾ táhne my¹í (drag), aktualizujeme koncový vektor pomocí metody drag() a pokud je poskytnut i výstupní quaternion, aktualizujeme ho pomocí výsledné rotace.</p>

<p class="src0">void ArcBall_t::drag(const Point2fT* NewPt, Quat4fT* NewRot)</p>

<p>Pøi zmìnì velikosti okna jednodu¹e aktualizujeme i rozmìry ArcBallu.</p>

<p class="src0">void ArcBall_t::setBounds(GLfloat NewWidth, GLfloat NewHeight)</p>

<p>V projektu budeme potøebovat i nìkolik dal¹ích promìnných. Transformation je finální transformace, která urèuje rotaci, ale také posunutí. LastRot pøedstavuje poslední zaznamenanou rotaci od konce dragu a ThisRot urèuje rotaci v dobì táhnutí my¹í. V¹echny tøi na zaèátku inicializujeme na matici identity.</p>

<p>Pøi kliknutí se zaèíná z identického stavu rotace a kdy¾ následnì táhneme, rotace se poèítá od pozice kliknutí a¾ po bod táhnutí. I kdy¾ na otáèení objektù ve scénì pou¾íváme tuto implementaci, je dùle¾ité poznamenat, ¾e nerotujeme samotný ArcBall. S rostoucími (pøírùstkovými) rotacemi se musíme vypoøádat sami. To je úkol LastRot a ThisRot. LastRot si mù¾eme pøedstavit jako v¹echny rotace a¾ do teï a ThisRot jako aktuální rotace. V¾dy, kdy¾ zaène rotace, ThisRot se modifikuje pomocí originální rotace a potom se aktualizuje jako výsledek souèinu s LastRot (a také se upraví koneèná transformace). Po skonèení dragu pøiøadíme do LastRot hodnoty z ThisRot. Kdybychom neakumulovali rotace samotné, model by vypadal, jako by se pøi ka¾dém kliknutí pøilepil na zaèátek souøadnic. Napøíklad pøi rotaci okolo osy x o 90 stupòù a potom o 45 stupòù, chceme získat 135 namísto posledních 45.</p>

<p class="src0">Matrix4fT Transform =<span class="kom">// Finální transformace</span></p>
<p class="src0">{</p>
<p class="src1">1.0f, 0.0f, 0.0f, 0.0f,</p>
<p class="src1">0.0f, 1.0f, 0.0f, 0.0f,</p>
<p class="src1">0.0f, 0.0f, 1.0f, 0.0f,</p>
<p class="src1">0.0f, 0.0f, 0.0f, 1.0f</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">Matrix3fT LastRot =<span class="kom">// Minulá rotace</span></p>
<p class="src0">{</p>
<p class="src1">1.0f, 0.0f, 0.0f,</p>
<p class="src1">0.0f, 1.0f, 0.0f,</p>
<p class="src1">0.0f, 0.0f, 1.0f</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">Matrix3fT ThisRot =<span class="kom">// Souèasná rotace</span></p>
<p class="src0">{</p>
<p class="src1">1.0f, 0.0f, 0.0f,</p>
<p class="src1">0.0f, 1.0f, 0.0f,</p>
<p class="src1">0.0f, 0.0f, 1.0f</p>
<p class="src0">};</p>

<p>Co se týèe zbytku promìnných (kromì isDragged), v¹echno, co s nimi musíme udìlat, je v¾dy je ve správný èas aktualizovat. ArcBall potøebuje, aby se jeho hranice pøi ka¾dé zmìnì velikosti okna resetovaly. MousePt se aktualizuje pøi pohybu my¹í nebo stisknutí tlaèítka a isClicked/isRClicked pøi stlaèení levého/pravého tlaèítka my¹i. Levé tlaèítko slou¾í pro dragging a pravé pro resetování v¹ech rotací do výchozího identity stavu.</p>

<p class="src0">ArcBallT ArcBall(640.0f, 480.0f);<span class="kom">// Instance ArcBallu</span></p>
<p class="src0">Point2fT MousePt;<span class="kom">// Pozice my¹i</span></p>
<p class="src"></p>
<p class="src0">bool isClicked  = false;<span class="kom">// Kliknuto my¹í?</span></p>
<p class="src0">bool isRClicked = false;<span class="kom">// Kliknuto pravým tlaèítkem my¹i?</span></p>
<p class="src0">bool isDragging = false;<span class="kom">// Táhnuto my¹í?</span></p>

<p>Aktualizace promìnných vypadají takto:</p>

<p class="src0"><span class="kom">// Konec ReshapeGL()</span></p>
<p class="src1">ArcBall.setBounds((GLfloat)width, (GLfloat)height);<span class="kom">// Nastaví hranice pro ArcBall</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">// Funkce WindowProc() - o¹etøení zpráv my¹i</span></p>
<p class="src1">case WM_MOUSEMOVE:<span class="kom">// Pohyb</span></p>
<p class="src2">MousePt.s.X = (GLfloat)LOWORD(lParam);</p>
<p class="src2">MousePt.s.Y = (GLfloat)HIWORD(lParam);</p>
<p class="src2">isClicked = (LOWORD(wParam) & MK_LBUTTON) ? true : false;</p>
<p class="src2">isRClicked = (LOWORD(wParam) & MK_RBUTTON) ? true : false;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case WM_LBUTTONUP:<span class="kom">// Uvolnìní levého tlaèítka</span></p>
<p class="src2">isClicked = false;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case WM_RBUTTONUP:<span class="kom">// Uvolnìní pravého tlaèítka</span></p>
<p class="src2">isRClicked  = false;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case WM_LBUTTONDOWN:<span class="kom">// Kliknutí levým tlaèítkem</span></p>
<p class="src2">isClicked = true;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case WM_RBUTTONDOWN:<span class="kom">// Kliknutí pravým tlaèítkem</span></p>
<p class="src2">isRClicked = true;</p>
<p class="src2">break;</p>

<p>Máme-li toto v¹echno, je na èase vypoøádat se s klikací logikou.</p>

<p class="src0">void Update(DWORD milliseconds)<span class="kom">// Aktualizace scény</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown [VK_ESCAPE] == TRUE)<span class="kom">// Stisk ESC</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication(g_window);<span class="kom">// Ukonèení programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown [VK_F1] == TRUE)<span class="kom">// Stisk F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// Pøepnutí do fullscreenu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (isRClicked)<span class="kom">// Kliknutí pravým tlaèítkem - reset v¹ech rotací</span></p>
<p class="src1">{</p>
<p class="src2">Matrix3fSetIdentity(&amp;LastRot);</p>
<p class="src2">Matrix3fSetIdentity(&amp;ThisRot);</p>
<p class="src2">Matrix4fSetRotationFromMatrix3f(&amp;Transform, &amp;ThisRot);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!isDragging)<span class="kom">// Netáhne se my¹í?</span></p>
<p class="src1">{</p>
<p class="src2">if (isClicked)<span class="kom">// Kliknutí?</span></p>
<p class="src2">{</p>
<p class="src3">isDragging = true;<span class="kom">// Pøíprava na dragging</span></p>
<p class="src3">LastRot = ThisRot;<span class="kom">// Nastavení minulé statické rotace na tuto</span></p>
<p class="src3">ArcBall.click(&amp;MousePt);<span class="kom">// Aktualizace startovního vektoru a pøíprava na dragging</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// U¾ se táhne</span></p>
<p class="src1">{</p>
<p class="src2">if (isClicked)<span class="kom">// Je je¹tì stisknuto tlaèítko?</span></p>
<p class="src2">{</p>
<p class="src3">Quat4fT ThisQuat;</p>
<p class="src"></p>
<p class="src3">ArcBall.drag(&amp;MousePt, &amp;ThisQuat);<span class="kom">// Aktualizace koncového vektoru a získání rotace jako quaternionu</span></p>
<p class="src3">Matrix3fSetRotationFromQuat4f(&amp;ThisRot, &amp;ThisQuat);<span class="kom">// Konvertování quaternionu na Matrix3fT</span></p>
<p class="src3">Matrix3fMulMatrix3f(&amp;ThisRot, &amp;LastRot);<span class="kom">// Akumulace minulé rotace do této</span></p>
<p class="src3">Matrix4fSetRotationFromMatrix3f(&amp;Transform, &amp;ThisRot);<span class="kom">// Nastavení koncové transformaèní rotace na tuto</span></p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// U¾ není stisknuto</span></p>
<p class="src2">{</p>
<p class="src3">isDragging = false;<span class="kom">// Konec draggingu</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Teï u¾ jenom potøebujeme aplikovat transformaci na na¹e modely a jsme hotovi.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Smazání bufferù</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(-1.5f, 0.0f, -6.0f);<span class="kom">// Translace doleva a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾ení matice</span></p>
<p class="src2">glMultMatrixf(Transform.M);<span class="kom">// Aplikování transformace</span></p>
<p class="src2">glColor3f(0.75f, 0.75f, 1.0f);<span class="kom">// Barva</span></p>
<p class="src2">Torus(0.30f, 1.00f);<span class="kom">// Vykreslení toroidu (speciální funkce)</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení pùvodní matice</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(1.5f, 0.0f, -6.0f);<span class="kom">// Translace doprava a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾ení matice</span></p>
<p class="src2">glMultMatrixf(Transform.M);<span class="kom">// Aplikování transformace</span></p>
<p class="src2">glColor3f(1.0f, 0.75f, 0.75f);<span class="kom">// Barva</span></p>
<p class="src2">gluSphere(quadratic,1.3f,20,20);<span class="kom">// Vykreslení koule</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení matice</span></p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Flushnutí renderovací pipeline</span></p>
<p class="src0">}</p>

<p>Pøidal jsem i ukázku kompletního kódu, který toto v¹echno demonstruje. Nemusíte pou¾ívat moji matematiku a funkce stojící na pozadí, naopak, pokud si vìøíte, doporuèuji vytvoøit si vlastní. Nicménì i s mými vzorci a výpoèty by v¹echno mìlo bez problémù fungovat.</p>

<p class="autor">napsal: Terence J. Grant <?VypisEmail('tjgrant@tatewake.com');?><br />
do sloven¹tiny pøelo¾il: Pavel Hradský - PcMaster <?VypisEmail('pcmaster@stonline.sk');?><br />
do èe¹tiny pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson48.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson48_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson48.zip">Dev C++</a> kód této lekce. ( <a href="mailto:robohog_64@hotmail.com">Victor Andr?e</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson48.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:werkt@csh.rit.edu">George Gensure</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson48.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(48);?>
<?FceNeHeOkolniLekce(48);?>

<?
include 'p_end.php';
?>
