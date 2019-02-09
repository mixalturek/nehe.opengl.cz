<?
$g_title = 'CZ NeHe OpenGL - Lekce 15 - Mapov�n� textur na fonty';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(15);?>

<h1>Lekce 15 - Mapov�n� textur na fonty</h1>

<p class="nadpis_clanku">Po vysv�tlen� bitmapov�ch a 3D font� v p�edchoz�ch dvou lekc�ch jsem se rozhodl napsat lekci o mapov�n� textur na fonty. Jedn� se o tzv. automatick� generov�n� koordin�t� textur. Po do�ten� t�to lekce budete um�t namapovat texturu opravdu na cokoli - zcela snadno a jednodu�e.</p>

<p>Stejn� jako v minul� a p�edminul� lekci je k�d specifick� pro Windows. Pokud by m�l n�kdo na platform� nez�visl� k�d sem s n�m a j� nap�u nov� tutori�l o fontech.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavi�kov� soubor pro matematickou knihovnu</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
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
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>
<p class="src"></p>
<p class="src0">GLuint base;<span class="kom">// Ukazatel na prvn� z display list� pro font</span></p>
<p class="src0">GLuint texture[1];<span class="kom">// Ukl�d� texturu</span></p>
<p class="src0">GLuint rot;<span class="kom">// Pro pohyb, rotaci a barvu textu</span></p>

<p>P�i psan� funkce nahr�vaj�c� font jsem ud�lal malou zm�nu. Pokud jste si spustili program, asi jste na prvn� pohled nena�li ten font - ale byl tam. V�imli jste si poletuj�c� pir�tsk� lebky se zk��en�mi kostmi. Pr�v� ona je jeden znak z p�sma Wingdings, kter� pat�� mezi tzv. symbolov� fonty.</p>

<p class="src0">GLvoid BuildFont(GLvoid)<span class="kom">// Vytvo�en� fontu</span></p>
<p class="src0">{</p>
<p class="src1">GLYPHMETRICSFLOAT gmf[256];<span class="kom">// Ukl�d� informace o fontu</span></p>
<p class="src1">HFONT font;<span class="kom">// Prom�nn� fontu</span></p>
<p class="src"></p>
<p class="src1">base = glGenLists(256);<span class="kom">// 256 znak�</span></p>
<p class="src"></p>
<p class="src1">font = CreateFont(-12,<span class="kom">// V��ka</span></p>
<p class="src2">0,<span class="kom">// ���ka</span></p>
<p class="src2">0,<span class="kom">// �hel escapement</span></p>
<p class="src2">0,<span class="kom">// �hel orientace</span></p>
<p class="src2">FW_BOLD,<span class="kom">// Tu�nost</span></p>
<p class="src2">FALSE,<span class="kom">// Kurz�va</span></p>
<p class="src2">FALSE,<span class="kom">// Podtr�en�</span></p>
<p class="src2">FALSE,<span class="kom">// P�e�krtnut�</span></p>

<p>M�sto ANSI_CHARSET podle stylu lekce 14, pou�ijeme SYMBOL_CHARSET. T�m �ekneme Windows�m, �e vytv��en� font nen� typick�m p�smem tvo�en�m znaky, ale �e obsahuje mal� obr�zky (symboly). Pokud byste zapomn�li zm�nit tuto ��dku, p�sma typu Wingdings, webdings a dal��, kter� zkou��te pou��t, nebudou vykreslovat symboly (lebka ...), ale norm�ln� znaky (A, B ...).</p>

<p class="src2">SYMBOL_CHARSET,<span class="kom">// Znakov� sada</span></p>
<p class="src2">OUT_TT_PRECIS,<span class="kom">// P�esnost v�stupu (TrueType)</span></p>
<p class="src2">CLIP_DEFAULT_PRECIS,<span class="kom">// P�esnost o�ez�n�</span></p>
<p class="src2">ANTIALIASED_QUALITY,<span class="kom">// V�stupn� kvalita</span></p>
<p class="src2">FF_DONTCARE|DEFAULT_PITCH,<span class="kom">// Rodina a pitch</span></p>
<p class="src2">&quot;Wingdings&quot;);<span class="kom">// Jm�no fontu</span></p>
<p class="src"></p>
<p class="src1">SelectObject(hDC, font);<span class="kom">// V�b�r fontu do DC</span></p>
<p class="src"></p>
<p class="src1">wglUseFontOutlines(hDC,<span class="kom">// Vybere DC</span></p>
<p class="src2">0,<span class="kom">// Po��te�n� znak</span></p>
<p class="src2">255,<span class="kom">// Koncov� znak</span></p>
<p class="src2">base,<span class="kom">// Adresa prvn�ho znaku</span></p>

<p>Po��t�m s v�t�� hranatost�. To znamen�, �e se OpenGL nebude dr�et obrys� fontu tak t�sn�. Pokud zde p�ed�te 0.0f, v�imnete si probl�m� spojen�ch s mapov�n�m textur na zak�iven� roviny. Povol�te-li jistou hranatost, v�t�ina probl�m� zmiz�. (J� (p�ekladatel) jsem ��dn� probl�my s 0.0f nem�l, dokonce to vypadalo o dost l�pe.)</p>

<p class="src2">0.1f,<span class="kom">// Hranatost</span></p>
<p class="src2">0.2f,<span class="kom">// Hloubka v ose z</span></p>
<p class="src2">WGL_FONT_POLYGONS,<span class="kom">// Polygony ne dr�t�n� model</span></p>
<p class="src2">gmf);<span class="kom">// Adresa bufferu pro ulo�en� informac�.</span></p>
<p class="src0">}</p>

<p>K nahr�n� textur p�id�me k�d, kter� u� zn�te z p�edchoz�ch tutori�l�. Vytvo��me mipmapovanou texturu, proto�e vypad� l�pe. </p>

<p class="src0">int LoadGLTextures()<span class="kom">// Vytvo�� texturu</span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>
<p class="src"></p>
<p class="src1">AUX_RGBImageRec *TextureImage[1];<span class="kom">// M�sto pro obr�zek</span></p>
<p class="src1">memset(TextureImage,0,sizeof(void *)*1);<span class="kom">// Nastav� ukazatel na NULL</span></p>
<p class="src"></p>
<p class="src1">if (TextureImage[0]=LoadBMP(&quot;Data/Lights.bmp&quot;))<span class="kom">// Nahraje bitmapu</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;</p>
<p class="src"></p>
<p class="src2">glGenTextures(1, &amp;texture[0]);<span class="kom">// Generuje texturu</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�� line�rn� mipmapovanou texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[0]);</p>
<p class="src2">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, TextureImage[0]-&gt;sizeX, TextureImage[0]-&gt;sizeY, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[0]-&gt;data);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR_MIPMAP_NEAREST);</p>

<p>Dal�� ��dky umo��uj� pou��t automatick� generov�n� koordin�t� textur na jak�koli zobrazovan� objekty. P��kaz glTexGen je velmi siln� a komplexn�. Popsat v�echny vlastnosti, kter� zahrnuje, by byl tutori�l s�m o sob�. Nicm�n�, v�e, co pot�ebujete v�d�t, je �e, GL_S a GL_T jsou texturov� koordin�ty. Implicitn� jsou nastaveny tak, aby vzaly pozice x a y na obrazovce a p�i�ly s bodem textury. V�imn�te si, �e objekty nejsou texturov�ny na ose z. P�edn� i zadn� ��st ploch je otexturovan� a to je to, na �em z�le��. x (GL_S) mapuje textury zleva doprava a y (GL_T) nahoru a dol�. GL_TEXTURE_GEN_MODE pou�ijeme p�i v�b�ru texturov�ho mapov�n� S i T. Jsou celkem t�i mo�nosti v dal��m parametru:</p>
<p>GL_EYE_LINEAR - textura je namapovan� na v�echny st�ny stejn�<br />
GL_OBJECT_LINEAR - textura je fixovan� na p�edn� st�nu, do hloubky se prot�hne<br />
GL_SPHERE_MAP - textura kovov� odr�ej�c� sv�tlo</p>
<p>Je d�le�it� poznamenat, �e jsem vypustil spoustu k�du. Spr�vn� bychom m�li ur�it tak� GL_OBJECT_PLANE, ale implicitn� nastaven� n�m sta��. Pokud byste se cht�li dozv�d�t v�ce, tak si kupte n�jakou dobrou knihu nebo zkuste n�pov�du MSDN.</p>

<p class="src2">glTexGeni(GL_S, GL_TEXTURE_GEN_MODE, GL_OBJECT_LINEAR);</p>
<p class="src2">glTexGeni(GL_T, GL_TEXTURE_GEN_MODE, GL_OBJECT_LINEAR);</p>
<p class="src2">glEnable(GL_TEXTURE_GEN_S);</p>
<p class="src2">glEnable(GL_TEXTURE_GEN_T);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (TextureImage[0])<span class="kom">// Pokud bitmapa existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (TextureImage[0]-&gt;data)<span class="kom">// Pokud existuj� data bitmapy</span></p>
<p class="src2">{</p>
<p class="src3">free(TextureImage[0]-&gt;data);<span class="kom">// Sma�e data bitmapy</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">free(TextureImage[0]);<span class="kom">// Sma�e strukturu bitmapy</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return Status;</p>
<p class="src0">}</p>

<p>Ud�l�me tak� n�kolik zm�n v inicializa�n�m k�du. BuildFont() p�esuneme pod loading textur. Pokud byste cht�li m�nit barvy textur pou�it�m glColor3f(R,G,B), p�idejte glEnable(GL_COLOR_MATERIAL).</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echna nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje texturu</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">BuildFont();<span class="kom">// Vytvo�� font</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� hloubkov� testov�n�</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne implicitn� sv�tlo</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne sv�tla</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>

<p>Zapneme automatick� mapov�n� textur. texture[0] se te� namapuje na jak�koli 3D objekt kreslen� na obrazovku. Pokud byste pot�ebovali v�ce kontroly m��ete automatick� mapov�n� p�i kreslen� ru�n� zap�nat a vyp�nat.</p>

<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturov� mapov�n�</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Zvol� texturu</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Nam�sto udr�ov�n� objektu uprost�ed obrazovky budeme v t�to lekci "l�tat" dokola po cel�m monitoru. P�esuneme se o 3 jednotky dovnit�. Hodnota pro osu x se bude m�nit od -1.1 do +1.1. Krajn� meze na ose y jsou -0.8 a +0.8. K v�po�tu pou�ijeme prom�nnou "rot". Jako v�dy budeme rotovat okolo os.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� bufferu</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(1.1f*float(cos(rot/16.0f)),0.8f*float(sin(rot/20.0f)),-3.0f);</p>
<p class="src1">glRotatef(rot,1.0f,0.0f,0.0f);<span class="kom">// Rotace na x</span></p>
<p class="src1">glRotatef(rot*1.2f,0.0f,1.0f,0.0f);<span class="kom">// Rotace na y</span></p>
<p class="src1">glRotatef(rot*1.4f,0.0f,0.0f,1.0f);<span class="kom">// Rotace na z</span></p>

<p>P�esuneme se trochu doleva, dol� a dop�edu k vycentrov�n� symbolu na ka�d� ose, abychom simulovali tak� ot��en� kolem vlastn�ho centra (-0.35 je ��slo, kter� pracuje ;) S t�mto p�esunem jsem si musel trochu pohr�t, proto�e si nejsem jist�, jak je font �irok�, ka�d� p�smeno se v�cem�n� li��. Nejsem si jist�, pro� se fonty nevytv��ej� kolem centr�ln�ho bodu.</p>

<p class="src1">glTranslatef(-0.35f,-0.35f,0.1f);<span class="kom">// Vycentrov�n�</span></p>

<p>Nakonec nakresl�me lebku a zk��en� kosti. Nech�pete-li pro� pr�v� "N", tak si pus�te MS Word vyberte p�smo Wingdings a napi�te "N" - odpov�d� mu tento symbol. Aby se lebka pohybovala ka�d�m p�ekreslen�m inkrementujeme rot.</p>

<p class="src1">glPrint(&quot;N&quot;);<span class="kom">// Vykresl� lebku a zk��en� kosti</span></p>
<p class="src1">rot+=0.1f;<span class="kom">// Inkrementace rotace a pohybu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>I kdy� jsem nepopsal prob�ranou l�tku do ��dn�ch extr�mn�ch detail�, m�li byste pochopit, jak po��dat OpenGL o automatick� generov�n� texturov�ch koordin�t�. Nem�li byste m�t ��dn� probl�my s otexturov�n�m jak�chkoli objekt�. Zm�nou pouh�ch dvou ��dk� k�du (viz. GL_SPHERE_MAP u vytv��en� textur), dos�hnete perfektn�ho efektu sf�rick�ho mapov�n� (kovov� odlesky sv�tla).</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson15.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson15_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson15.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson15.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson15.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson15.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson15.zip">GLut</a> k�d t�to lekce. ( <a href="mailto:oster@ieee.org">David Phillip Oster</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson15.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson15.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson15.sit.hqx">Mac OS</a> k�d t�to lekce. ( <a href="mailto:oster@ieee.org">David Phillip Oster</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson15.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:greg@ozducati.com">Greg Helps</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson15.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson15.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(15);?>
<?FceNeHeOkolniLekce(15);?>

<?
include 'p_end.php';
?>
