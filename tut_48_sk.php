<?
$g_title = 'CZ NeHe OpenGL - Lekce 48 - Rot�cia ArcBall';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(48);?>

<h1>Lekce 48 - Rot�cia ArcBall</h1>

<p class="nadpis_clanku">Nebolo by super rotova� v� model len tak, iba pou�it�m my�i? S ArcBall (prekl.: Obl�kov� lopta, gu�a :-) je to mo�n�. Tento dokument je zalo�en� na mojej vlastnej implement�cii a �vah�ch o prid�van� do va�ich projektov. Moja implement�cia ArcBall je zalo�en� na tej od Brettona Wadea, ktor� je zalo�en� na implement�cii Kena Shoemakea zo s�rie kn�h Graphic Gems. Ja som to trosku upravil, opravil zop�r ch�b a urobil optimaliz�ciu pre na�e ��ely.</p>

<p class="netisk"><a href="tut_48.php">Verze v �e�tin�...</a></p>

<p>ArcBall funguje tak, �e mapuje s�radnice kliknut� do okna priamo do s�radn�c ArcBall, akoby to bolo priamo pred vami.</p>

<p>Na dosiahnutie tohoto, najsk�r jednoducho zmen��me mierku s�radn�c my�i z rozsahu 0..sirka, 0..vyska na rozsah -1..1, 1..-1 (Pam�tajte, �e prevr�time znamienko Y s�radnice, aby sme dosiahli korektn� v�sledok v OpenGL). A to vyzer� asi takto:</p>

<p class="src0"><span class="kom">MousePt.X  =  ((MousePt.X / ((Width  - 1) / 2)) - 1);</span></p>
<p class="src0"><span class="kom">MousePt.Y  = -((MousePt.Y / ((Height - 1) / 2)) - 1);</span></p>

<p>Jedin� d�vod pre�o sme menili mierku s�radn�c je, aby sme si u�ah�ili matiku a ��astnou zhodou okolnost� to dovo�uje kompil�toru k�d tro�ku optimalizova�.</p>

<p>�alej vyr�tame d�ku vektora a ur��me, �i je alebo nie je vn�tri gule. Ak je, vr�time vektor z vn�tra gule, inak normalizujeme bod a vr�time najbli��� bod k vonkaj�ku gule.</p>

<p>Ke� u� m�me oba vektory, vyr�tame vektor kolm� na za�iato�n� a koncov� vektor s uhlom, ��m dostaneme quaternion. S t�mto v ruk�ch, m�me dos� inform�ci� na generovanie rota�nej matice a sme doma.</p>

<p>Trieda ArcBall bude ma� nasledovn� kon�truktor. NewWidth a NewHeight s� rozmery okna.</p>

<p class="src0">ArcBall_t::ArcBall_t(GLfloat NewWidth, GLfloat NewHeight)</p>

<p>Ke� u��vate� klikne my�ou, vyr�ta sa po�iato�n� vektor pod�a toho, kam klikol.</p>

<p class="src0">void ArcBall_t::click(const Point2fT* NewPt)</p>

<p>Ke� u��vate� potiahne my�ou (drag), koncov� vektor sa aktualizuje cez proced�ru drag a ak je poskytnut� aj v�stupn� quaternion (NewRot), tento je aktualizovan� v�slednou maticou.</p>

<p class="src0">void ArcBall_t::drag(const Point2fT* NewPt, Quat4fT* NewRot)</p>

<p>Ak sa zmen� ve�kos� okna, jednoducho updatneme ArcBall.</p>

<p class="src0">void ArcBall_t::setBounds(GLfloat NewWidth, GLfloat NewHeight)</p>

<p>Vo va�ich projektoch budete potrebova� zop�r premenn�ch:</p>

<p>Transformation je na�a fin�lna transform�cia - rot�cia alebo aj posunutie. LastRot je posledn� rot�cia, ktor� sme zaznamenali od konca dragu. ThisRot je rot�cia po�as �ahania stla�enej my�i. V�etko je inicializovan� na identitu.</p>

<p>Ke� klikneme, za�neme z identick�ho stavu rot�cie. Ke� dragneme, po��tame rot�ciu od za�iato�nej poz�cie kliku po bod �ahania. Aj ke� pou��vame t�to implement�ciu na rotovanie objektov na sc�ne, je d�le�it� poznamena�, �e nerotujeme samotn� ArcBall. Preto�e chceme ma� rast�ce rot�cie, mus�me sa s nimi sami vysporiada�.</p>

<p>Tu prich�dza na sc�nu LastRot a ThisRot. LastRot m��eme definova� ako &quot;v�etky rot�cie a� do teraz&quot;, ThisRot je &quot;aktu�lna rot�cia&quot;. V�dy, ke� sa za�ne rot�cia, ThisRot sa modifikuje origin�lnou rot�ciou. Potom sa aktualizuje ako v�sledok Ono_Samo*LastRot (Potom sa uprav� fin�lna transform�cia). Ke� drag skon��, LastRot nadobudne hodnoty ThisRot.</p>

<p>Ak by sme nezhroma��ovali rot�cie samotn�, model by vyzeral, akoby sa priliepal (snapoval) na za�iatok s�radn�c ka�d� raz, ke� klikneme. Napr�klad, ak by sme rotovali okolo X-ovej osi o 90 stup�ov, potom o 45 stup�ov, chceli by sme 135 namiesto len posledn�ch 45.</p>

<p>Pre zvy�n� premenn� (okrem isDragged) je v�etko �o potrebujete aktualizova� ich v spr�vnom �ase, pod�a v�ho syst�mu. ArcBall potrebuje, aby jeho hranice boli resetnut� zaka�d�m, �o sa zmen� ve�kos� okna. MousePt sa aktualizuje ke� pohnete my�ou, alebo stla��te tla��tko. isClicked/isRClicked ke� stla��te �av�/prav� tla��tko my�i. isClicked pou�ijeme pre zistenie kliknut� a dragnut�. isRClicked pou�ijeme na resetnutie v�etk�ch rot�ci�.</p>

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
<p class="src"></p>
<p class="src0">ArcBallT ArcBall(640.0f, 480.0f);<span class="kom">// Instance ArcBallu</span></p>
<p class="src0">Point2fT MousePt;<span class="kom">// Pozice my�i</span></p>
<p class="src"></p>
<p class="src0">bool isClicked  = false;<span class="kom">// Kliknuto my��?</span></p>
<p class="src0">bool isRClicked = false;<span class="kom">// Kliknuto prav�m tla��tkem my�i?</span></p>
<p class="src0">bool isDragging = false;<span class="kom">// T�hnut� my��?</span></p>

<p>Zvy�n� syst�mov� updaty pod�a NeHeGL/Windows vyzeraj� asi takto:</p>

<p class="src0">void ReshapeGL (int width, int height)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Konec funkce</span></p>
<p class="src1">ArcBall.setBounds((GLfloat)width, (GLfloat)height);<span class="kom">// Nastav� hranice pro ArcBall</span></p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">LRESULT CALLBACK WindowProc (HWND hWnd, UINT uMsg, WPARAM wParam, LPARAM lParam)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// O�et�en� zpr�v my�i</span></p>
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
<p class="src"></p>
<p class="src1"><span class="kom">// Pokra�ov�n� funkce</span></p>
<p class="src0">}</p>

<p>Ke� u� tento k�d m�me, je na �ase vysporiada� sa z klikacou logikou :-) Je to dos� samovysvet�uj�ce, ak u� viete v�etko nad t�m.</p>

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
<p class="src1">if (!isDragging)<span class="kom">// Pokud se je�t� net�hne my��</span></p>
<p class="src1">{</p>
<p class="src2">if (isClicked)<span class="kom">// Prvn� kliknut�</span></p>
<p class="src2">{</p>
<p class="src3">isDragging = true;<span class="kom">// P��prava na draging</span></p>
<p class="src3">LastRot = ThisRot;<span class="kom">// Nastaven� minul� statick� rotace na dynamickou</span></p>
<p class="src3">ArcBall.click(&amp;MousePt);<span class="kom">// Aktualizace startovn�ho vektoru a p��prava na dragging</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// U� se t�hne my��</span></p>
<p class="src1">{</p>
<p class="src2">if (isClicked)<span class="kom">// Tla��tko je stisknuto</span></p>
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
<p class="src3">isDragging = false;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Toto sa postar� o v�etko. Teraz u� len potrebujeme aplikova� transform�ciu na na�e modely a sme hotov�. Je to dos� jednoduch�:</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Smaz�n� buffer�</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(-1.5f,0.0f,-6.0f);<span class="kom">// Translace doleva a do hloubky</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>
<p class="src2">glMultMatrixf(Transform.M);<span class="kom">// Aplikov�n� dynamick� transformace</span></p>
<p class="src2">glColor3f(0.75f,0.75f,1.0f);<span class="kom">// Barva</span></p>
<p class="src2">Torus(0.30f, 1.00f);<span class="kom">// Vykreslen� toroidu (speci�ln� funkce)</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� p�vodn� matice</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(1.5f,0.0f,-6.0f);<span class="kom">// Translace doprava a do hlubky</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>
<p class="src2">glMultMatrixf(Transform.M);<span class="kom">// Aplikov�n� dynamick� transformace</span></p>
<p class="src2">glColor3f(1.0f,0.75f,0.75f);<span class="kom">// Barva</span></p>
<p class="src2">gluSphere(quadratic,1.3f,20,20);<span class="kom">// Vykreslen� koule</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� matice</span></p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Flushnut� renderovac� pipeline</span></p>
<p class="src0">}</p>

<p>Pridal som aj uk�ku, ktor� demon�truje toto v�etko. nemus�te pou��va� moju matiku a funkcie, naopak, odpor��am spravi� si vlastn�, ak si dos� ver�te. Akoko�vek, v�etko je dos� sebesta�n� a malo by fungova�. Ke� u� vid�te, ak� to je jednoduch�, mali by ste by� schopn� pou�i� ArcBall vo vlastn�ch projektoch!</p>

<p class="autor">napsal: Terence J. Grant <?VypisEmail('tjgrant@tatewake.com');?><br />
do sloven�tiny p�elo�il: Pavel Hradsk� - PcMaster<?VypisEmail('pcmaster@stonline.sk');?><br />
do �e�tiny p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<p>Whooaa! Tak toto je u� moj druh� preklad, d�fam �e sa v�m p��il a pomohol v�m, s ot�zkami sa obr�te na m�a alebo spr�vcu webu (Woq).
Te��m sa na �al�ie preklady!</p>

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
