<?
$g_title = 'CZ NeHe OpenGL - Pou¾íváme gluUnProject()';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Pou¾íváme gluUnProject()</h1>

<p class="nadpis_clanku">Potøebujete transformovat pozici my¹i na souøadnice v OpenGL scénì a nevíte si rady? Pokud ano, jste na správném místì.</p>

<p>Vytvoøíme jednoduchou funkci, která provede v¹echny potøebné operace. Pøedávat se jí budou dvì celá èísla definující pozici v oknì a výstupem budou tøi èísla x, y, z pozice ve scénì. Co v¹echno budeme potøebovat:</p>

<ul>
<li>Viewport pozice a velikost</li>
<li>Modelview matice</li>
<li>Projekèní matice</li>
<li>Pozice v oknì</li>
<li>Získání OpenGL souøadnic kurzoru</li>
</ul>

<p>Jak tedy na nì?</p>

<h3>1. Viewport pozice a velikost</h3>

<p>Potøebujeme nagrabovat informace o aktuálním viewportu. Konkrétnì se jedná o poèáteèní x a y souøadnice spolu se s jeho ¹íøkou a vý¹kou. Pro tuto operaci poslou¾í OpenGL funkce glGetIntegerv().</p>

<p class="src0">GLint viewport[4];<span class="kom">// Pamì» pro viewport</span></p>
<p class="src0">glGetIntegerv(GL_VIEWPORT, viewport);<span class="kom">// Získání viewportu</span></p>

<p>Pole viewport bude po provedení pøíkazu obsahovat následující informace.</p>

<ul>
<li>viewport[0] = x pozice viewportu</li>
<li>viewport[1] = y pozice viewportu</li>
<li>viewport[2] = ¹íøka viewportu</li>
<li>viewport[3] = vý¹ka viewportu</li>
</ul>

<h3>2. Modelview matice</h3>

<p>Jakmile máme informace o viewportu, mù¾eme se pustit do získávání informací o modelview matici, která urèuje, jak jsou koordináty OpenGL primitiv transformovány do viditelných souøadnic. Pøekl.: V modelview matici je ulo¾eno nastavení kamery (napø. translace a rotace).</p>

<p class="src0">GLdouble modelview[16];<span class="kom">// Pamì» pro modelview matici</span></p>
<p class="src0">glGetDoublev(GL_MODELVIEW_MATRIX, modelview);<span class="kom">// Získání modelview matice</span></p>

<h3>3. Projekèní matice</h3>

<p>Dále potøebujeme získat projekèní matici, která transformuje vertexy v souøadnicích oèí do oøezávacích koordinátù. Pøekl.: Projekèní matice se pou¾ívá pro nastavení perspektivní nebo pravoúhlé projekce.</p>

<p class="src0">GLdouble projection[16];<span class="kom">// Pamìti pro projekèní matici</span></p>
<p class="src0">glGetDoublev(GL_PROJECTION_MATRIX, projection);<span class="kom">// Získání projekèní matice</span></p>

<h3>4. Pozice v oknì</h3>

<p>Poté, co jsme toto v¹echno udìlali, mù¾eme nagrabovat souøadnice v oknì. V na¹em pøípadì se zajímáme o pozici my¹i.</p>

<p class="src0">POINT mouse;<span class="kom">// Bude ukládat x a y souøadnice my¹i</span></p>
<p class="src0">GetCursorPos(&amp;mouse);<span class="kom">// Grabování souøadnic my¹i</span></p>
<p class="src0">ScreenToClient(hWnd, &amp;mouse);<span class="kom">// Souøadnice v klientské oblasti okna</span></p>
<p class="src"></p>
<p class="src0">GLfloat winX, winY, winZ;<span class="kom">// Bude ukládat x, y, a z souøadnice</span></p>
<p class="src"></p>
<p class="src0">winX = (float)mouse.x;<span class="kom">// X pozice my¹i</span></p>
<p class="src0">winY = (float)mouse.y;<span class="kom">// Y pozice my¹i</span></p>

<p>Støed souøadnicového systému [0; 0] se ve Windows nachází vlevo nahoøe, zatímco u OpenGL je vlevo dole.</p>

<p class="src0">winY = (float)viewport[3] - winY;<span class="kom">// Odeète aktuální y pozici my¹i od vý¹ky obrazovky.</span></p>

<p>Jistì jste si v¹imli, ¾e nám chybí hodnota na ose z...</p>

<p class="src0">glReadPixels(winX, winY, 1, 1, GL_DEPTH_COMPONENT, GL_FLOAT, &amp;winZ);<span class="kom">// Získání z pozice</span></p>

<h3>5. Získání OpenGL souøadnic kurzoru</h3>

<p>V¹e, co nám je¹tì zbývá udìlat, je vypoèítat finální hodnoty na OpenGL osách.</p>

<p class="src0">GLdouble posX, posY, posZ;<span class="kom">// Bude obsahovat výsledné hodnoty</span></p>
<p class="src0">gluUnProject(winX, winY, winZ, modelview, projection, viewport, &amp;posX, &amp;posY, &amp;posZ);<span class="kom">// Transformace do OpenGL souøadnic</span></p>

<p>Pomocí právì získaných znalostí mù¾eme napsat C/C++ funkci, která z pozice my¹i v oknì vypoèítá OpenGL souøadnice.</p>

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

<p>A je¹tì jednou, tentokrát v Delphi...</p>

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

<p>Pøi testování jsem objevil, ¾e pokud se v Delphi rovná y hodnota nule, vrací se nedefinovaná hodnota.</p>

<p class="src1">if (Y = 0) then Y := 1;</p>
<p class="src"></p>
<p class="src1">glReadPixels(X, -Y, 1, 1, GL_DEPTH_COMPONENT, GL_FLOAT, @winZ);</p>
<p class="src1">gluUnProject(X, viewport[4]-Y, winZ, @modelview, @projection, @viewport, Result[1], Result[2], Result[3]);</p>
<p class="src0">end;</p>


<p>Tak to bude v¹e. Myslím, ¾e to ani nebolelo...</p>

<p class="autor">napsal: Luke Benstead <?VypisEmail('Lukerd84@lycos.co.uk');?><br />
pøelo¾il: Pøemysl Jaro¹ <?VypisEmail('xzf@seznam.cz');?></p>

<p>Anglický originál èlánku lze najít na adrese <?OdkazBlank('http://nehe.gamedev.net/data/articles/article.asp?article=13');?>.</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojových kódù</li>
</ul>

<?
include 'p_end.php';
?>
