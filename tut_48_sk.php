<?
$g_title = 'CZ NeHe OpenGL - Lekce 48 - Rotácia ArcBall';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(48);?>

<h1>Lekce 48 - Rotácia ArcBall</h1>

<p class="nadpis_clanku">Nebolo by super rotova» vá¹ model len tak, iba pou¾itím my¹i? S ArcBall (prekl.: Oblúková lopta, guµa :-) je to mo¾né. Tento dokument je zalo¾ený na mojej vlastnej implementácii a úvahách o pridávaní do va¹ich projektov. Moja implementácia ArcBall je zalo¾ená na tej od Brettona Wadea, ktorá je zalo¾ená na implementácii Kena Shoemakea zo série kníh Graphic Gems. Ja som to trosku upravil, opravil zopár chýb a urobil optimalizáciu pre na¹e úèely.</p>

<p class="netisk"><a href="tut_48.php">Verze v èe¹tinì...</a></p>

<p>ArcBall funguje tak, ¾e mapuje súradnice kliknutí do okna priamo do súradníc ArcBall, akoby to bolo priamo pred vami.</p>

<p>Na dosiahnutie tohoto, najskôr jednoducho zmen¹íme mierku súradníc my¹i z rozsahu 0..sirka, 0..vyska na rozsah -1..1, 1..-1 (Pamätajte, ¾e prevrátime znamienko Y súradnice, aby sme dosiahli korektný výsledok v OpenGL). A to vyzerá asi takto:</p>

<p class="src0"><span class="kom">MousePt.X  =  ((MousePt.X / ((Width  - 1) / 2)) - 1);</span></p>
<p class="src0"><span class="kom">MousePt.Y  = -((MousePt.Y / ((Height - 1) / 2)) - 1);</span></p>

<p>Jediný dôvod preèo sme menili mierku súradníc je, aby sme si uµahèili matiku a ¹»astnou zhodou okolností to dovoµuje kompilátoru kód tro¹ku optimalizova».</p>

<p>Ïalej vyrátame då¾ku vektora a urèíme, èi je alebo nie je vnútri gule. Ak je, vrátime vektor z vnútra gule, inak normalizujeme bod a vrátime najbli¾¹í bod k vonkaj¹ku gule.</p>

<p>Keï u¾ máme oba vektory, vyrátame vektor kolmý na zaèiatoèný a koncový vektor s uhlom, èím dostaneme quaternion. S týmto v rukách, máme dos» informácií na generovanie rotaènej matice a sme doma.</p>

<p>Trieda ArcBall bude ma» nasledovný kon¹truktor. NewWidth a NewHeight sú rozmery okna.</p>

<p class="src0">ArcBall_t::ArcBall_t(GLfloat NewWidth, GLfloat NewHeight)</p>

<p>Keï u¾ívateµ klikne my¹ou, vyráta sa poèiatoèný vektor podµa toho, kam klikol.</p>

<p class="src0">void ArcBall_t::click(const Point2fT* NewPt)</p>

<p>Keï u¾ívateµ potiahne my¹ou (drag), koncový vektor sa aktualizuje cez procedúru drag a ak je poskytnutý aj výstupný quaternion (NewRot), tento je aktualizovaný výslednou maticou.</p>

<p class="src0">void ArcBall_t::drag(const Point2fT* NewPt, Quat4fT* NewRot)</p>

<p>Ak sa zmení veµkos» okna, jednoducho updatneme ArcBall.</p>

<p class="src0">void ArcBall_t::setBounds(GLfloat NewWidth, GLfloat NewHeight)</p>

<p>Vo va¹ich projektoch budete potrebova» zopár premenných:</p>

<p>Transformation je na¹a finálna transformácia - rotácia alebo aj posunutie. LastRot je posledná rotácia, ktorú sme zaznamenali od konca dragu. ThisRot je rotácia poèas »ahania stlaèenej my¹i. V¹etko je inicializované na identitu.</p>

<p>Keï klikneme, zaèneme z identického stavu rotácie. Keï dragneme, poèítame rotáciu od zaèiatoènej pozície kliku po bod »ahania. Aj keï pou¾ívame túto implementáciu na rotovanie objektov na scéne, je dôle¾ité poznamena», ¾e nerotujeme samotný ArcBall. Preto¾e chceme ma¾ rastúce rotácie, musíme sa s nimi sami vysporiada».</p>

<p>Tu prichádza na scénu LastRot a ThisRot. LastRot mô¾eme definova» ako &quot;v¹etky rotácie a¾ do teraz&quot;, ThisRot je &quot;aktuálna rotácia&quot;. V¾dy, keï sa zaène rotácia, ThisRot sa modifikuje originálnou rotáciou. Potom sa aktualizuje ako výsledok Ono_Samo*LastRot (Potom sa upraví finálna transformácia). Keï drag skonèí, LastRot nadobudne hodnoty ThisRot.</p>

<p>Ak by sme nezhroma¾ïovali rotácie samotné, model by vyzeral, akoby sa priliepal (snapoval) na zaèiatok súradníc ka¾dý raz, keï klikneme. Napríklad, ak by sme rotovali okolo X-ovej osi o 90 stupòov, potom o 45 stupòov, chceli by sme 135 namiesto len posledných 45.</p>

<p>Pre zvy¹né premenné (okrem isDragged) je v¹etko èo potrebujete aktualizova» ich v správnom èase, podµa vá¹ho systému. ArcBall potrebuje, aby jeho hranice boli resetnuté zaka¾dým, èo sa zmení veµkos» okna. MousePt sa aktualizuje keï pohnete my¹ou, alebo stlaèíte tlaèítko. isClicked/isRClicked keï stlaèíte µavé/pravé tlaèítko my¹i. isClicked pou¾ijeme pre zistenie kliknutí a dragnutí. isRClicked pou¾ijeme na resetnutie v¹etkých rotácií.</p>

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
<p class="src"></p>
<p class="src0">ArcBallT ArcBall(640.0f, 480.0f);<span class="kom">// Instance ArcBallu</span></p>
<p class="src0">Point2fT MousePt;<span class="kom">// Pozice my¹i</span></p>
<p class="src"></p>
<p class="src0">bool isClicked  = false;<span class="kom">// Kliknuto my¹í?</span></p>
<p class="src0">bool isRClicked = false;<span class="kom">// Kliknuto pravým tlaèítkem my¹i?</span></p>
<p class="src0">bool isDragging = false;<span class="kom">// Táhnutí my¹í?</span></p>

<p>Zvy¹né systémové updaty podµa NeHeGL/Windows vyzerajú asi takto:</p>

<p class="src0">void ReshapeGL (int width, int height)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Konec funkce</span></p>
<p class="src1">ArcBall.setBounds((GLfloat)width, (GLfloat)height);<span class="kom">// Nastaví hranice pro ArcBall</span></p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">LRESULT CALLBACK WindowProc (HWND hWnd, UINT uMsg, WPARAM wParam, LPARAM lParam)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// O¹etøení zpráv my¹i</span></p>
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
<p class="src"></p>
<p class="src1"><span class="kom">// Pokraèování funkce</span></p>
<p class="src0">}</p>

<p>Keï u¾ tento kód máme, je na èase vysporiada» sa z klikacou logikou :-) Je to dos» samovysvetµujúce, ak u¾ viete v¹etko nad tým.</p>

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
<p class="src1">if (!isDragging)<span class="kom">// Pokud se je¹tì netáhne my¹í</span></p>
<p class="src1">{</p>
<p class="src2">if (isClicked)<span class="kom">// První kliknutí</span></p>
<p class="src2">{</p>
<p class="src3">isDragging = true;<span class="kom">// Pøíprava na draging</span></p>
<p class="src3">LastRot = ThisRot;<span class="kom">// Nastavení minulé statické rotace na dynamickou</span></p>
<p class="src3">ArcBall.click(&amp;MousePt);<span class="kom">// Aktualizace startovního vektoru a pøíprava na dragging</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// U¾ se táhne my¹í</span></p>
<p class="src1">{</p>
<p class="src2">if (isClicked)<span class="kom">// Tlaèítko je stisknuto</span></p>
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
<p class="src3">isDragging = false;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Toto sa postará o v¹etko. Teraz u¾ len potrebujeme aplikova» transformáciu na na¹e modely a sme hotoví. Je to dos» jednoduché:</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Smazání bufferù</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Translace doleva a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾ení matice</span></p>
<p class="src2">glMultMatrixf(Transform.M);<span class="kom">// Aplikování dynamické transformace</span></p>
<p class="src2">glColor3f(0.75f,0.75f,1.0f);<span class="kom">// Barva</span></p>
<p class="src2">Torus(0.30f, 1.00f);<span class="kom">// Vykreslení toroidu (speciální funkce)</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení pùvodní matice</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(1.5f,0.0f,-6.0f);<span class="kom">// Translace doprava a do hlubky</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾ení matice</span></p>
<p class="src2">glMultMatrixf(Transform.M);<span class="kom">// Aplikování dynamické transformace</span></p>
<p class="src2">glColor3f(1.0f,0.75f,0.75f);<span class="kom">// Barva</span></p>
<p class="src2">gluSphere(quadratic,1.3f,20,20);<span class="kom">// Vykreslení koule</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení matice</span></p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Flushnutí renderovací pipeline</span></p>
<p class="src0">}</p>

<p>Pridal som aj uká¾ku, ktorá demon¹truje toto v¹etko. nemusíte pou¾íva» moju matiku a funkcie, naopak, odporúèam spravi» si vlastné, ak si dos» veríte. Akokoµvek, v¹etko je dos» sebestaèné a malo by fungova». Keï u¾ vidíte, aké to je jednoduché, mali by ste by» schopní pou¾i» ArcBall vo vlastných projektoch!</p>

<p class="autor">napsal: Terence J. Grant <?VypisEmail('tjgrant@tatewake.com');?><br />
do sloven¹tiny pøelo¾il: Pavel Hradský - PcMaster<?VypisEmail('pcmaster@stonline.sk');?><br />
do èe¹tiny pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<p>Whooaa! Tak toto je u¾ moj druhý preklad, dúfam ¾e sa vám páèil a pomohol vám, s otázkami sa obrá»te na mòa alebo správcu webu (Woq).
Te¹ím sa na ïal¹ie preklady!</p>

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
