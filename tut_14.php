<?
$g_title = 'CZ NeHe OpenGL - Lekce 14 - Outline fonty';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(14);?>

<h1>Lekce 14 - Outline fonty</h1>

<p class="nadpis_clanku">Bitmapov� fonty nesta��? Pot�ebujete kontrolovat pozici textu i na ose z? Cht�li byste fonty s hloubkou? Pokud zn� va�e odpov�� ano, pak jsou 3D fonty nejlep�� �e�en�. M��ete s nimi pohybovat na ose z a t�m m�nit jejich velikost, ot��et je, prost� d�lat v�e, co nem��ete s oby�ejn�mi. Jsou nejlep�� volbou ke hr�m a dem�m.</p>

<p>Tato lekce je voln�mi pokra�ov�n�m t� minul� (13). Tehdy jsme se nau�ili pou��vat bitmapov� fonty. 3D p�sma se vytv��ej� velmi podobn�. Nicm�n�... vypadaj� stokr�t l�pe. M��ete je zv�t�ovat, pohybovat s nimi ve 3D, maj� hloubku. P�i osv�tlen� vypadaj� opravdu efektn�. Stejn� jako v minul� lekci je k�d specifick� pro Windows. Pokud by m�l n�kdo na platform� nez�visl� k�d, sem s n�m a j� nap�u nov� tutori�l. Roz����me typick� k�d prvn� lekce.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src"></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavi�kov� soubor pro matematickou knihovnu</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src0">#include &lt;stdarg.h&gt;<span class="kom">// Hlavi�kov� soubor pro funkce s prom�nn�m po�tem parametr�</span></p>
<p class="src"></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Priv�tn� GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trval� Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na�eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>

<p>Base si pamatujete z 13. lekce jako ukazatel na prvn� z display list� ascii znak�, rot slou�� k pohybu, rotaci a vybarvov�n� textu.</p>

<p class="src0">GLuint base;<span class="kom">// ��slo z�kladn�ho display listu znak�</span></p>
<p class="src0">GLfloat rot;<span class="kom">// Pro pohyb, rotaci a barvu textu</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>GLYPHMETRICSFLOAT gmf[256] ukl�d� informace o velikosti a orientaci ka�d�ho z 256 display list� fontu. D�le v lekci v�m uk�u, jak zjistit ���ku jednotliv�ch znak� a t�m velmi snadno a p�esn� vycentrovat text na obrazovce.</p>

<p class="src0">GLYPHMETRICSFLOAT gmf[256];<span class="kom">// Ukl�d� informace o fontu</span></p>

<p>Skoro cel� k�d n�sleduj�c� funkce byl pou�it ji� ve 13. lekci, tak�e pokud mu moc nerozum�te, v�te, kde hledat informace.</p>

<p class="src0">GLvoid BuildFont(GLvoid)<span class="kom">// Vytvo�en� fontu</span></p>
<p class="src0">{</p>
<p class="src1">HFONT font;<span class="kom">// Prom�nn� fontu</span></p>
<p class="src1">base = glGenLists(256);<span class="kom">// 256 znak�</span></p>
<p class="src"></p>
<p class="src1">font = CreateFont(-24,<span class="kom">// V��ka</span></p>
<p class="src2">0,<span class="kom">// ���ka</span></p>
<p class="src2">0,<span class="kom">// �hel escapement</span></p>
<p class="src2">0,<span class="kom">// �hel orientace</span></p>
<p class="src2">FW_BOLD,<span class="kom">// Tu�nost</span></p>
<p class="src2">FALSE,<span class="kom">// Kurz�va</span></p>
<p class="src2">FALSE,<span class="kom">// Podtr�en�</span></p>
<p class="src2">FALSE,<span class="kom">// P�e�krtnut�</span></p>
<p class="src2">ANSI_CHARSET,<span class="kom">// Znakov� sada</span></p>
<p class="src2">OUT_TT_PRECIS,<span class="kom">// P�esnost v�stupu (TrueType)</span></p>
<p class="src2">CLIP_DEFAULT_PRECIS,<span class="kom">// P�esnost o�ez�n�</span></p>
<p class="src2">ANTIALIASED_QUALITY,<span class="kom">// V�stupn� kvalita</span></p>
<p class="src2">FF_DONTCARE|DEFAULT_PITCH,<span class="kom">// Rodina a pitch</span></p>
<p class="src2">&quot;Courier New&quot;);<span class="kom">// Jm�no fontu</span></p>
<p class="src"></p>
<p class="src1">SelectObject(hDC, font);<span class="kom">// V�b�r fontu do DC</span></p>

<p>Pomoc� funkce wglUseFontOutlines() vytvo��me 3D font. V parametrech p�ed�me DC, prvn� znak, po�et display list�, kter� se budou vytv��et a ukazatel na pam�, kam se budou vytvo�en� display listy ukl�dat.</p>

<p class="src1">wglUseFontOutlines(hDC,<span class="kom">// Vybere DC</span></p>
<p class="src2">0,<span class="kom">// Po��te�n� znak</span></p>
<p class="src2">255,<span class="kom">// Koncov� znak</span></p>
<p class="src2">base,<span class="kom">// Adresa prvn�ho znaku</span></p>

<p>Nastav�me �rove� odchylek, kter� ur�uje jak hranat� bude vypadat. Potom ur��me ���ku nebo sp�e hloubku na ose z. 0.0f by byl ploch� 2D font. ��m v�t�� ��slo p�i�ad�me, t�m bude hlub��. Parametr WGL_FONT_POLYGONS ��k�, �e m� OpenGL vytvo�it pevn� (celistv�) znaky s pou�it�m polygon�. P�i pou�it� WGL_FONT_LINES se vytvo�� z linek (podobn� dr�t�n�mu modelu). Je d�le�it� poznamenat, �e by se v tomto p��pad� negenerovaly norm�lov� vektory, tak�e sv�tlo nebude vypadat dob�e. Posledn� parametr ukazuje na buffer pro ulo�en� informac� o display listech.</p>

<p class="src2">0.0f,<span class="kom">// Hranatost</span></p>
<p class="src2">0.2f,<span class="kom">// Hloubka v ose z</span></p>
<p class="src2">WGL_FONT_POLYGONS,<span class="kom">// Polygony ne dr�t�n� model</span></p>
<p class="src2">gmf);<span class="kom">// Adresa bufferu pro ulo�en� informac�.</span></p>
<p class="src0">}</p>

<p>V n�sleduj� funkci se ma�e 256 display list� fontu po��naje prvn�m, kter� je definov�n v base. Nejsem si jist�, jestli by to Windows ud�laly automaticky. Jeden ��dek za jistotu stoj�. Funkce se vol� p�i skon�en� programu.</p>

<p class="src0">GLvoid KillFont(GLvoid)<span class="kom">// Sma�e font</span></p>
<p class="src0">{</p>
<p class="src1">glDeleteLists(base, 256);<span class="kom">// Sma�e v�ech 256 znak�</span></p>
<p class="src0">}</p>

<p>Tento k�d zavol�te v�dy, kdy� budete pot�ebovat vypsat n�jak� text. �et�zec je ulo�en ve "fmt".</p>

<p class="src0">GLvoid glPrint(const char *fmt, ...)<span class="kom">// Klon printf() pro OpenGL</span></p>
<p class="src0">{</p>

<p>Prom�nnou "length" pou�ijeme ke zji�t�n� d�lky textu. Pole "text" ukl�d� kone�n� �et�zec pro vykreslen�. T�et� prom�nn� je ukazatel do parametr� funkce (pokud bychom zavolali funkci s n�jakou prom�nnou, "ap" na ni bude ukazovat.</p>

<p class="src1">float length=0;<span class="kom">// D�lka znaku</span></p>
<p class="src1">char text[256];<span class="kom">// Kone�n� �et�zec</span></p>
<p class="src1">va_list ap;<span class="kom">// Ukazatel do argument� funkce</span></p>
<p class="src"></p>
<p class="src1">if (fmt == NULL)<span class="kom">// Pokud nebyl p�ed�n �et�zec</span></p>
<p class="src2">return;<span class="kom">// Konec</span></p>

<p>N�sleduj�c� k�d konvertuje ve�ker� symboly v �et�zci (%d, %f ap.) na znaky, kter� reprezentuj� ��seln� hodnoty v prom�nn�ch. Poupravovan� text se ulo�� do �et�zce text.</p>

<p class="src1">va_start(ap, fmt);<span class="kom">// Rozbor �et�zce pro prom�nn�</span></p>
<p class="src2">vsprintf(text, fmt, ap);<span class="kom">// Zam�n� symboly za ��sla</span></p>
<p class="src1">va_end(ap);<span class="kom">// V�sledek je nyn� ulo�en v text</span></p>

<p>Text by �el vycentrovat manu�ln�, ale n�sleduj�c� metoda je ur�it� lep��. V ka�d�m pr�chodu cyklem p�i�teme k d�lce �et�zce ���ku aktu�ln� znaku, kterou najdeme v gmf[text[loop]].gmfCellIncX. gmf ukl�d� informace o ka�d�m znaku (display listu), tedy nap��klad i v��ku znaku, ulo�enou pod gmfCellIncY. Tuto techniku lze pou��t p�i vertik�ln�m vykreslov�n�.</p>

<p class="src1">for (unsigned int loop=0;loop&lt;(strlen(text));loop++)<span class="kom">// Zjist� po�et znak� textu</span></p>
<p class="src1">{</p>
<p class="src2">length+=gmf[text[loop]].gmfCellIncX;<span class="kom">// Inkrementace o ���ku znaku</span></p>
<p class="src1">}</p>

<p>K vycentrov�n� textu posuneme po��tek doleva o polovinu d�lky �et�zce.</p>

<p class="src1">glTranslatef(-length/2,0.0f,0.0f);<span class="kom">// Zarovn�n� na st�ed</span></p>

<p>Nastav�me GL_LIST_BIT a t�m zamez�me p�soben� jin�ch display list�, pou�it�ch v programu na glListBase(). P�ede�l�m p��kazem ur��me, kde m� OpenGL hledat spr�vn� display listy jednotliv�ch znak�.</p>

<p class="src1">glPushAttrib(GL_LIST_BIT);<span class="kom">// Ulo�� sou�asn� stav display list�</span></p>
<p class="src1">glListBase(base);<span class="kom">// Nastav� prvn� display list na base</span></p>

<p>Zavol�me funkci glCallLists(), kter� najednou zobrazuje v�ce display list�. strlen(text) vr�t� po�et znak� v �et�zci a t�m i po�et k zobrazen�. D�le pot�ebujeme zn�t typ p�ed�van�ho parametru (posledn�). Ani te� nebudeme vkl�dat v�ce ne� 256 znak�, tak�e pou�ijeme GL_UNSIGNED_BYTE (byte m��e nab�vat hodnot 0-255, co� je p�esn� to, co pot�ebujeme). V posledn�m parametru p�ed�me text. Ka�d� display list v�, kde je prav� hrana toho p�edchoz�ho, ��m� zamez�me nakupen� znak� na sebe, na jedno m�sto. P�ed za��tkem kreslen� n�sleduj�c� znaku se p�esune o tuto hodnotu doprava (glTranslatef()). Nakonec nastav�me GL_LIST_BIT zp�t na hodnotu maj�c� p�ed vol�n�m glListBase().</p>

<p class="src1">glCallLists(strlen(text), GL_UNSIGNED_BYTE, text);<span class="kom">// Vykresl� display listy</span></p>
<p class="src1">glPopAttrib();<span class="kom">// Obnov� p�vodn� stav display list�</span></p>
<p class="src0">}</p>

<p>Provedeme p�r drobn�ch zm�n v inicializa�n�m k�du. ��dka BuildFont() ze 13. lekce z�stala na stejn�m m�st�, ale p�ibyl nov� k�d pro pou�it� sv�tel. Light0 je p�eddefinov�n na v�t�in� grafick�ch karet. Tak� jsem p�idal glEnable(GL_COLOR_MATERIAL). Ke zm�n� barvy p�sma pot�ebujeme zapnout vybarvov�n� materi�l�, proto�e i znaky jsou 3D objekty. Pokud vykreslujete vlastn� objekty a n�jak� text, mus�te p�ed funkc� glPrint() zavolat glEnable(GL_COLOR_MATERIAL) a po vykreslen� textu glDisable(GL_COLOR_MATERIAL), jinak by se zm�nila barva i v�mi vykreslovan�ho objektu.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echna nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povol� jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� hloubkov� testov�n�</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne implicitn� sv�tlo</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne sv�tla</span></p>
<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Zapne vybarvov�n� materi�l�</span></p>
<p class="src1">BuildFont();<span class="kom">// Vytvo�� font</span></p>
<p class="src1"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>P�esuneme se 10 jednotek do obrazovky. Outline fonty vypadaj� skv�le v perspektivn�m m�du. Kdy� jsou um�st�ny hloub�ji, zmen�uj� se. Pomoc� funkce glScalef(x,y,z) m��eme tak� m�nit m���tka os. Pokud bychom nap��klad cht�li vykreslit font dvakr�t vy���, pou�ijeme glScalef(1.0f,2.0f,1.0f).</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-10.0f);<span class="kom">// P�esun do obrazovky</span></p>
<p class="src1">glRotatef(rot,1.0f,0.0f,0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(rot*1.5f,0.0f,1.0f,0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src1">glRotatef(rot*1.4f,0.0f,0.0f,1.0f);<span class="kom">// Rotace na ose z</span></p>

<p>Jako oby�ejn� jsem pou�il pro zm�nu barev "jednoduch�" matematiky. (Pozn. p�ekladatele: tahle v�ta se mi povedla :)</p>

<p class="src1"><span class="kom">// Pulzov�n� barev z�visl� na pozici a rotaci</span></p>
<p class="src1">glColor3f(1.0f*float(cos(rot/20.0f)),1.0f*float(sin(rot/25.0f)),1.0f-0.5f*float(cos(rot/17.0f)));</p>
<p class="src"></p>
<p class="src1">glPrint(&quot;NeHe - %3.2f&quot;,rot/50);<span class="kom">// V�pis textu</span></p>
<p class="src"></p>
<p class="src1">rot+=0.5f;<span class="kom">// Inkrementace ��ta�e</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Posledn� k�d, kter� se provede p�ed opu�t�n�m programu je smaz�n� fontu vol�n�m KillFont().</p>

<p class="src0"><span class="kom">//Konec funkce KillGLWindow(GLvoid)</span></p>
<p class="src1">if(!UnregisterClass("OpenGL",hInstance))</p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,"Could Not Unregister Class.","SHUTDOWN ERROR",MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">hInstance=NULL;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">KillFont();<span class="kom">//Smaz�n� fontu</span></p>
<p class="src0">}</p>

<p>Po do�ten� t�to lekce byste m�li b�t schopni pou��vat 3D fonty. Stejn� jako jsem psal ve 13. lekci, ani tentokr�t jsem na internetu nena�el podobn� �l�nek. Mo�n� jsem opravdu prvn�, kdo p�e o tomto t�matu.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson14.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson14_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson14.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson14.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson14.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson14.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson14.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson14.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson14.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:pepijn.vaneeckhoudt@luciad.com">Pepijn Van Eeckhoudt</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson14.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson14.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson14.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:greg@ozducati.com">Greg Helps</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson14.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson14-2.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:fredo@studenten.net">Edo</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson14.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(14);?>
<?FceNeHeOkolniLekce(14);?>

<?
include 'p_end.php';
?>
