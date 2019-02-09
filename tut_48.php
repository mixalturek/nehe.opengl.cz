<?
$g_title = 'CZ NeHe OpenGL - Lekce 48 - ArcBall rotace';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(48);?>

<h1>Lekce 48 - ArcBall rotace</h1>

<p class="nadpis_clanku">Nebylo by skv�l� ot��et modelem pomoc� my�i jednoduch�m drag &amp; drop? S ArcBall rotacemi je to mo�n�. Moje implementace je zalo�en� na my�lenk�ch Brettona Wadea a Kena Shoemakea. K�d tak� obsahuje funkci pro rendering toroidu - kompletn� i s norm�lami.</p>

<p class="netisk"><a href="tut_48_sk.php">Verze ve sloven�tin�...</a></p>

<p>ArcBall funguje tak, �e mapuje okenn� sou�adnice kliknut� p��mo do sou�adnic ArcBallu. Zmen�� pom�r sou�adnic my�i z rozsahu [0..���ka, 0..v��ka] na rozsah [-1..1, 1..-1]. Pamatujte si, �e aby v OpenGL dos�hl korektn�ho v�sledku, mus� p�evr�tit znam�nko y sou�adnice. Vzorec vypad� takto:</p>

<p class="src0"><span class="kom">MousePt.X  =  ((MousePt.X / ((Width  - 1) / 2)) - 1);</span></p>
<p class="src0"><span class="kom">MousePt.Y  = -((MousePt.Y / ((Height - 1) / 2)) - 1);</span></p>

<p>Jedin� d�vod, pro� jsme m�nili m���tko sou�adnic je, abychom zjednodu�ili matematiku, nicm�n� ��astnou shodou okolnost� to dovoluje kompil�toru k�d trochu optimalizovat. D�le vypo��t�me d�lku vektoru a ur��me, jestli se nach�z� nebo nenach�z� uvnit� koule. Pokud ano, vr�t�me vektor z jej�ho vnit�ku, jinak normalizujeme bod a vr�t�me nejbli��� pozici k vn�j�ku koule. Pot�, co m�me oba vektory, z�sk�me vektor sou�asn� kolm� na po��te�n� i koncov� vektor, ��m� dostaneme quaternion. S t�mto v ruk�ch m�me dost informac� na vygenerov�n� rota�n� matice.</p>

<p>Konstruktoru t��dy ArcBall budeme p�ed�vat rozm�ry okna.</p>

<p class="src0">ArcBall_t::ArcBall_t(GLfloat NewWidth, GLfloat NewHeight)</p>

<p>Kdy� u�ivatel klikne my��, vypo��t�me po��te�n� vektor podle toho, kam kliknul.</p>

<p class="src0">void ArcBall_t::click(const Point2fT* NewPt)</p>

<p>Kdy� t�hne my�� (drag), aktualizujeme koncov� vektor pomoc� metody drag() a pokud je poskytnut i v�stupn� quaternion, aktualizujeme ho pomoc� v�sledn� rotace.</p>

<p class="src0">void ArcBall_t::drag(const Point2fT* NewPt, Quat4fT* NewRot)</p>

<p>P�i zm�n� velikosti okna jednodu�e aktualizujeme i rozm�ry ArcBallu.</p>

<p class="src0">void ArcBall_t::setBounds(GLfloat NewWidth, GLfloat NewHeight)</p>

<p>V projektu budeme pot�ebovat i n�kolik dal��ch prom�nn�ch. Transformation je fin�ln� transformace, kter� ur�uje rotaci, ale tak� posunut�. LastRot p�edstavuje posledn� zaznamenanou rotaci od konce dragu a ThisRot ur�uje rotaci v dob� t�hnut� my��. V�echny t�i na za��tku inicializujeme na matici identity.</p>

<p>P�i kliknut� se za��n� z identick�ho stavu rotace a kdy� n�sledn� t�hneme, rotace se po��t� od pozice kliknut� a� po bod t�hnut�. I kdy� na ot��en� objekt� ve sc�n� pou��v�me tuto implementaci, je d�le�it� poznamenat, �e nerotujeme samotn� ArcBall. S rostouc�mi (p��r�stkov�mi) rotacemi se mus�me vypo��dat sami. To je �kol LastRot a ThisRot. LastRot si m��eme p�edstavit jako v�echny rotace a� do te� a ThisRot jako aktu�ln� rotace. V�dy, kdy� za�ne rotace, ThisRot se modifikuje pomoc� origin�ln� rotace a potom se aktualizuje jako v�sledek sou�inu s LastRot (a tak� se uprav� kone�n� transformace). Po skon�en� dragu p�i�ad�me do LastRot hodnoty z ThisRot. Kdybychom neakumulovali rotace samotn�, model by vypadal, jako by se p�i ka�d�m kliknut� p�ilepil na za��tek sou�adnic. Nap��klad p�i rotaci okolo osy x o 90 stup�� a potom o 45 stup��, chceme z�skat 135 nam�sto posledn�ch 45.</p>

<p class="src0">Matrix4fT Transform =<span class="kom">// Fin�ln� transformace</span></p>
<p class="src0">{</p>
<p class="src1">1.0f, 0.0f, 0.0f, 0.0f,</p>
<p class="src1">0.0f, 1.0f, 0.0f, 0.0f,</p>
<p class="src1">0.0f, 0.0f, 1.0f, 0.0f,</p>
<p class="src1">0.0f, 0.0f, 0.0f, 1.0f</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">Matrix3fT LastRot =<span class="kom">// Minul� rotace</span></p>
<p class="src0">{</p>
<p class="src1">1.0f, 0.0f, 0.0f,</p>
<p class="src1">0.0f, 1.0f, 0.0f,</p>
<p class="src1">0.0f, 0.0f, 1.0f</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">Matrix3fT ThisRot =<span class="kom">// Sou�asn� rotace</span></p>
<p class="src0">{</p>
<p class="src1">1.0f, 0.0f, 0.0f,</p>
<p class="src1">0.0f, 1.0f, 0.0f,</p>
<p class="src1">0.0f, 0.0f, 1.0f</p>
<p class="src0">};</p>

<p>Co se t��e zbytku prom�nn�ch (krom� isDragged), v�echno, co s nimi mus�me ud�lat, je v�dy je ve spr�vn� �as aktualizovat. ArcBall pot�ebuje, aby se jeho hranice p�i ka�d� zm�n� velikosti okna resetovaly. MousePt se aktualizuje p�i pohybu my�� nebo stisknut� tla��tka a isClicked/isRClicked p�i stla�en� lev�ho/prav�ho tla��tka my�i. Lev� tla��tko slou�� pro dragging a prav� pro resetov�n� v�ech rotac� do v�choz�ho identity stavu.</p>

<p class="src0">ArcBallT ArcBall(640.0f, 480.0f);<span class="kom">// Instance ArcBallu</span></p>
<p class="src0">Point2fT MousePt;<span class="kom">// Pozice my�i</span></p>
<p class="src"></p>
<p class="src0">bool isClicked  = false;<span class="kom">// Kliknuto my��?</span></p>
<p class="src0">bool isRClicked = false;<span class="kom">// Kliknuto prav�m tla��tkem my�i?</span></p>
<p class="src0">bool isDragging = false;<span class="kom">// T�hnuto my��?</span></p>

<p>Aktualizace prom�nn�ch vypadaj� takto:</p>

<p class="src0"><span class="kom">// Konec ReshapeGL()</span></p>
<p class="src1">ArcBall.setBounds((GLfloat)width, (GLfloat)height);<span class="kom">// Nastav� hranice pro ArcBall</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">// Funkce WindowProc() - o�et�en� zpr�v my�i</span></p>
<p class="src1">case WM_MOUSEMOVE:<span class="kom">// Pohyb</span></p>
<p class="src2">MousePt.s.X = (GLfloat)LOWORD(lParam);</p>
<p class="src2">MousePt.s.Y = (GLfloat)HIWORD(lParam);</p>
<p class="src2">isClicked = (LOWORD(wParam) & MK_LBUTTON) ? true : false;</p>
<p class="src2">isRClicked = (LOWORD(wParam) & MK_RBUTTON) ? true : false;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case WM_LBUTTONUP:<span class="kom">// Uvoln�n� lev�ho tla��tka</span></p>
<p class="src2">isClicked = false;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case WM_RBUTTONUP:<span class="kom">// Uvoln�n� prav�ho tla��tka</span></p>
<p class="src2">isRClicked  = false;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case WM_LBUTTONDOWN:<span class="kom">// Kliknut� lev�m tla��tkem</span></p>
<p class="src2">isClicked = true;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case WM_RBUTTONDOWN:<span class="kom">// Kliknut� prav�m tla��tkem</span></p>
<p class="src2">isRClicked = true;</p>
<p class="src2">break;</p>

<p>M�me-li toto v�echno, je na �ase vypo��dat se s klikac� logikou.</p>

<p class="src0">void Update(DWORD milliseconds)<span class="kom">// Aktualizace sc�ny</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown [VK_ESCAPE] == TRUE)<span class="kom">// Stisk ESC</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication(g_window);<span class="kom">// Ukon�en� programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown [VK_F1] == TRUE)<span class="kom">// Stisk F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// P�epnut� do fullscreenu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (isRClicked)<span class="kom">// Kliknut� prav�m tla��tkem - reset v�ech rotac�</span></p>
<p class="src1">{</p>
<p class="src2">Matrix3fSetIdentity(&amp;LastRot);</p>
<p class="src2">Matrix3fSetIdentity(&amp;ThisRot);</p>
<p class="src2">Matrix4fSetRotationFromMatrix3f(&amp;Transform, &amp;ThisRot);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!isDragging)<span class="kom">// Net�hne se my��?</span></p>
<p class="src1">{</p>
<p class="src2">if (isClicked)<span class="kom">// Kliknut�?</span></p>
<p class="src2">{</p>
<p class="src3">isDragging = true;<span class="kom">// P��prava na dragging</span></p>
<p class="src3">LastRot = ThisRot;<span class="kom">// Nastaven� minul� statick� rotace na tuto</span></p>
<p class="src3">ArcBall.click(&amp;MousePt);<span class="kom">// Aktualizace startovn�ho vektoru a p��prava na dragging</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// U� se t�hne</span></p>
<p class="src1">{</p>
<p class="src2">if (isClicked)<span class="kom">// Je je�t� stisknuto tla��tko?</span></p>
<p class="src2">{</p>
<p class="src3">Quat4fT ThisQuat;</p>
<p class="src"></p>
<p class="src3">ArcBall.drag(&amp;MousePt, &amp;ThisQuat);<span class="kom">// Aktualizace koncov�ho vektoru a z�sk�n� rotace jako quaternionu</span></p>
<p class="src3">Matrix3fSetRotationFromQuat4f(&amp;ThisRot, &amp;ThisQuat);<span class="kom">// Konvertov�n� quaternionu na Matrix3fT</span></p>
<p class="src3">Matrix3fMulMatrix3f(&amp;ThisRot, &amp;LastRot);<span class="kom">// Akumulace minul� rotace do t�to</span></p>
<p class="src3">Matrix4fSetRotationFromMatrix3f(&amp;Transform, &amp;ThisRot);<span class="kom">// Nastaven� koncov� transforma�n� rotace na tuto</span></p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// U� nen� stisknuto</span></p>
<p class="src2">{</p>
<p class="src3">isDragging = false;<span class="kom">// Konec draggingu</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Te� u� jenom pot�ebujeme aplikovat transformaci na na�e modely a jsme hotovi.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Smaz�n� buffer�</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(-1.5f, 0.0f, -6.0f);<span class="kom">// Translace doleva a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>
<p class="src2">glMultMatrixf(Transform.M);<span class="kom">// Aplikov�n� transformace</span></p>
<p class="src2">glColor3f(0.75f, 0.75f, 1.0f);<span class="kom">// Barva</span></p>
<p class="src2">Torus(0.30f, 1.00f);<span class="kom">// Vykreslen� toroidu (speci�ln� funkce)</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� p�vodn� matice</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(1.5f, 0.0f, -6.0f);<span class="kom">// Translace doprava a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>
<p class="src2">glMultMatrixf(Transform.M);<span class="kom">// Aplikov�n� transformace</span></p>
<p class="src2">glColor3f(1.0f, 0.75f, 0.75f);<span class="kom">// Barva</span></p>
<p class="src2">gluSphere(quadratic,1.3f,20,20);<span class="kom">// Vykreslen� koule</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� matice</span></p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Flushnut� renderovac� pipeline</span></p>
<p class="src0">}</p>

<p>P�idal jsem i uk�zku kompletn�ho k�du, kter� toto v�echno demonstruje. Nemus�te pou��vat moji matematiku a funkce stoj�c� na pozad�, naopak, pokud si v���te, doporu�uji vytvo�it si vlastn�. Nicm�n� i s m�mi vzorci a v�po�ty by v�echno m�lo bez probl�m� fungovat.</p>

<p class="autor">napsal: Terence J. Grant <?VypisEmail('tjgrant@tatewake.com');?><br />
do sloven�tiny p�elo�il: Pavel Hradsk� - PcMaster <?VypisEmail('pcmaster@stonline.sk');?><br />
do �e�tiny p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson48.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson48_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson48.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:robohog_64@hotmail.com">Victor Andr?e</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson48.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:werkt@csh.rit.edu">George Gensure</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson48.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(48);?>
<?FceNeHeOkolniLekce(48);?>

<?
include 'p_end.php';
?>
