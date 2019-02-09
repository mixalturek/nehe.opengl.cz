<?
$g_title = 'CZ NeHe OpenGL - Pou��v�me gluUnProject()';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Pou��v�me gluUnProject()</h1>

<p class="nadpis_clanku">Pot�ebujete transformovat pozici my�i na sou�adnice v OpenGL sc�n� a nev�te si rady? Pokud ano, jste na spr�vn�m m�st�.</p>

<p>Vytvo��me jednoduchou funkci, kter� provede v�echny pot�ebn� operace. P�ed�vat se j� budou dv� cel� ��sla definuj�c� pozici v okn� a v�stupem budou t�i ��sla x, y, z pozice ve sc�n�. Co v�echno budeme pot�ebovat:</p>

<ul>
<li>Viewport pozice a velikost</li>
<li>Modelview matice</li>
<li>Projek�n� matice</li>
<li>Pozice v okn�</li>
<li>Z�sk�n� OpenGL sou�adnic kurzoru</li>
</ul>

<p>Jak tedy na n�?</p>

<h3>1. Viewport pozice a velikost</h3>

<p>Pot�ebujeme nagrabovat informace o aktu�ln�m viewportu. Konkr�tn� se jedn� o po��te�n� x a y sou�adnice spolu se s jeho ���kou a v��kou. Pro tuto operaci poslou�� OpenGL funkce glGetIntegerv().</p>

<p class="src0">GLint viewport[4];<span class="kom">// Pam� pro viewport</span></p>
<p class="src0">glGetIntegerv(GL_VIEWPORT, viewport);<span class="kom">// Z�sk�n� viewportu</span></p>

<p>Pole viewport bude po proveden� p��kazu obsahovat n�sleduj�c� informace.</p>

<ul>
<li>viewport[0] = x pozice viewportu</li>
<li>viewport[1] = y pozice viewportu</li>
<li>viewport[2] = ���ka viewportu</li>
<li>viewport[3] = v��ka viewportu</li>
</ul>

<h3>2. Modelview matice</h3>

<p>Jakmile m�me informace o viewportu, m��eme se pustit do z�sk�v�n� informac� o modelview matici, kter� ur�uje, jak jsou koordin�ty OpenGL primitiv transformov�ny do viditeln�ch sou�adnic. P�ekl.: V modelview matici je ulo�eno nastaven� kamery (nap�. translace a rotace).</p>

<p class="src0">GLdouble modelview[16];<span class="kom">// Pam� pro modelview matici</span></p>
<p class="src0">glGetDoublev(GL_MODELVIEW_MATRIX, modelview);<span class="kom">// Z�sk�n� modelview matice</span></p>

<h3>3. Projek�n� matice</h3>

<p>D�le pot�ebujeme z�skat projek�n� matici, kter� transformuje vertexy v sou�adnic�ch o�� do o�ez�vac�ch koordin�t�. P�ekl.: Projek�n� matice se pou��v� pro nastaven� perspektivn� nebo pravo�hl� projekce.</p>

<p class="src0">GLdouble projection[16];<span class="kom">// Pam�ti pro projek�n� matici</span></p>
<p class="src0">glGetDoublev(GL_PROJECTION_MATRIX, projection);<span class="kom">// Z�sk�n� projek�n� matice</span></p>

<h3>4. Pozice v okn�</h3>

<p>Pot�, co jsme toto v�echno ud�lali, m��eme nagrabovat sou�adnice v okn�. V na�em p��pad� se zaj�m�me o pozici my�i.</p>

<p class="src0">POINT mouse;<span class="kom">// Bude ukl�dat x a y sou�adnice my�i</span></p>
<p class="src0">GetCursorPos(&amp;mouse);<span class="kom">// Grabov�n� sou�adnic my�i</span></p>
<p class="src0">ScreenToClient(hWnd, &amp;mouse);<span class="kom">// Sou�adnice v klientsk� oblasti okna</span></p>
<p class="src"></p>
<p class="src0">GLfloat winX, winY, winZ;<span class="kom">// Bude ukl�dat x, y, a z sou�adnice</span></p>
<p class="src"></p>
<p class="src0">winX = (float)mouse.x;<span class="kom">// X pozice my�i</span></p>
<p class="src0">winY = (float)mouse.y;<span class="kom">// Y pozice my�i</span></p>

<p>St�ed sou�adnicov�ho syst�mu [0; 0] se ve Windows nach�z� vlevo naho�e, zat�mco u OpenGL je vlevo dole.</p>

<p class="src0">winY = (float)viewport[3] - winY;<span class="kom">// Ode�te aktu�ln� y pozici my�i od v��ky obrazovky.</span></p>

<p>Jist� jste si v�imli, �e n�m chyb� hodnota na ose z...</p>

<p class="src0">glReadPixels(winX, winY, 1, 1, GL_DEPTH_COMPONENT, GL_FLOAT, &amp;winZ);<span class="kom">// Z�sk�n� z pozice</span></p>

<h3>5. Z�sk�n� OpenGL sou�adnic kurzoru</h3>

<p>V�e, co n�m je�t� zb�v� ud�lat, je vypo��tat fin�ln� hodnoty na OpenGL os�ch.</p>

<p class="src0">GLdouble posX, posY, posZ;<span class="kom">// Bude obsahovat v�sledn� hodnoty</span></p>
<p class="src0">gluUnProject(winX, winY, winZ, modelview, projection, viewport, &amp;posX, &amp;posY, &amp;posZ);<span class="kom">// Transformace do OpenGL sou�adnic</span></p>

<p>Pomoc� pr�v� z�skan�ch znalost� m��eme napsat C/C++ funkci, kter� z pozice my�i v okn� vypo��t� OpenGL sou�adnice.</p>

<p class="src0">CVector3 GetOGLPos(int x, int y)</p>
<p class="src0">{</p>
<p class="src1">GLint viewport[4];</p>
<p class="src1">GLdouble modelview[16];</p>
<p class="src1">GLdouble projection[16];</p>
<p class="src1">GLfloat winX, winY, winZ;</p>
<p class="src1">GLdouble posX, posY, posZ;</p>
<p class="src"></p>
<p class="src1">glGetDoublev(GL_MODELVIEW_MATRIX, modelview );</p>
<p class="src1">glGetDoublev(GL_PROJECTION_MATRIX, projection);</p>
<p class="src1">glGetIntegerv(GL_VIEWPORT, viewport);</p>
<p class="src"></p>
<p class="src1">winX = (float)x;</p>
<p class="src1">winY = (float)viewport[3] - (float)y;</p>
<p class="src1">glReadPixels(x, int(winY), 1, 1, GL_DEPTH_COMPONENT, GL_FLOAT, &amp;winZ);</p>
<p class="src"></p>
<p class="src1">gluUnProject(winX, winY, winZ, modelview, projection, viewport, &amp;posX, &amp;posY, &amp;posZ);</p>
<p class="src"></p>
<p class="src1">return CVector3(posX, posY, posZ);</p>
<p class="src0">}</p>

<p>A je�t� jednou, tentokr�t v Delphi...</p>

<p class="src0">function GetOGLPos(X, Y: Integer): T3D_Point;</p>
<p class="src0">var</p>
<p class="src1">viewport: array [1..4]  of Integer;</p>
<p class="src1">modelview: array [1..16] of Double;</p>
<p class="src1">projection: array [1..16] of Double;</p>
<p class="src1">winZ: Single;</p>
<p class="src0">begin</p>
<p class="src1">glGetDoublev(GL_MODELVIEW_MATRIX, @modelview);</p>
<p class="src1">glGetDoublev(GL_PROJECTION_MATRIX, @projection);</p>
<p class="src1">glGetIntegerv(GL_VIEWPORT, @viewport);</p>

<p>P�i testov�n� jsem objevil, �e pokud se v Delphi rovn� y hodnota nule, vrac� se nedefinovan� hodnota.</p>

<p class="src1">if (Y = 0) then Y := 1;</p>
<p class="src"></p>
<p class="src1">glReadPixels(X, -Y, 1, 1, GL_DEPTH_COMPONENT, GL_FLOAT, @winZ);</p>
<p class="src1">gluUnProject(X, viewport[4]-Y, winZ, @modelview, @projection, @viewport, Result[1], Result[2], Result[3]);</p>
<p class="src0">end;</p>


<p>Tak to bude v�e. Mysl�m, �e to ani nebolelo...</p>

<p class="autor">napsal: Luke Benstead <?VypisEmail('Lukerd84@lycos.co.uk');?><br />
p�elo�il: P�emysl Jaro� <?VypisEmail('xzf@seznam.cz');?></p>

<p>Anglick� origin�l �l�nku lze naj�t na adrese <?OdkazBlank('http://nehe.gamedev.net/data/articles/article.asp?article=13');?>.</p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojov�ch k�d�</li>
</ul>

<?
include 'p_end.php';
?>
