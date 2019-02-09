<?
$g_title = 'CZ NeHe OpenGL - Lekce 43 - FreeType Fonty v OpenGL';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(43);?>

<h1>Lekce 43 - FreeType Fonty v OpenGL</h1>

<p class="nadpis_clanku">Pou�it�m knihovny FreeType Font rendering library m��ete snadno vypisovat vyhlazen� znaky, kter� vypadaj� mnohem l�pe ne� p�smena u bitmapov�ch font� z lekce 13. N� text bude m�t ale i jin� v�hody - bezprobl�mov� rotace, dobr� spolupr�ce s OpenGL vyb�rac�mi (picking) funkcemi a v�ce��dkov� �et�zce.</p>

<p class="netisk"><a href="tut_43_sk.php">Verze ve sloven�tin�...</a></p>

<p>Motivace: Tady m�te uk�zky bitmapov�ho fontu vytvo�en�ho pomoc� WGL a FreeType fontu. Oba jsou Arial Black Kurz�va.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_43_foo_foo.gif" width="158" height="37" alt="FreeType font a WGL font" /></div>

<p>Z�kladn� probl�m s pou�it�m bitmapov�ch font� je, �e OpenGL bitmapy jsou bin�rn�mi obr�zky. To znamen�, �e si OpenGL pamatuje pouze 1 bit na 1 pixel. Zoomujeme-li na text vytvo�en� pomoc� WGL, v�sledek vypad� p�ibli�n� takto:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_43_wgl.gif" width="185" height="85" alt="Zv�t�en� WGL font" /></div>

<p>Proto�e jsou bitmapy bin�rn� obr�zky, nejsou okolo nich �ed� pixely a to znamen�, �e vypadaj� h��. Na�t�st� je velmi jednoduch� pomoc� GNU FreeType knihovny vytvo�it dob�e vypadaj�c� fonty. Tuto knihovnu pou��v� i Blizzard ve sv�ch hr�ch, tak�e mus� b�t opravdu dobr� :-))). Op�t uk�zka zv�t�en�ho textu, tentokr�t s knihovnou FreeType:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_43_free.gif" width="196" height="85" alt="Zv�t�en� FreeType font" /></div>

<p>Jak m��eme vid�t, okolo okraj� se nach�z� spousta �ed�ch pixel�, kter� jsou typick� pro vyhlazen� (anti-aliasovan�) fonty. �ed� pixely vylep�uj� znaky p�i pohledu z d�lky.</p>

<p>Knihovnu GNU FreeType si m��ete st�hnout na adrese <?OdkazBlank('http://gnuwin32.sourceforge.net/packages/freetype.htm');?>. Konkr�tn� se jedn� o bin�rn� a v�voj��sk� soubory. P�i instalaci si ur�it� v�imnete licen�n� podm�nky. Hovo�� se v n�, �e p�i pou�it� ve vlastn�ch programech, mus�te n�kde v dokumentaci uv�st kredit.</p>

<p>Po instalaci pot�ebujeme nastavit MSVC, aby um�lo pou��vat FreeType. V menu Project - Settings - Link se ujist�te, �e jste spolu s opengl32.lib, glu32.lib, glaux.lib a podobn�mi p�idali do Object/libraries i libfreetype.lib.</p>

<p>D�le pot�ebujeme v Tools - Options - Directories p�idat cesty k hlavi�kov�ch soubor�m. Pod Show Directories for vybereme Include Files a poklik�me na pr�zdn� ��dek dole v seznamu. Objev� se tla��tko se t�emi te�kami, kter� pou�ijeme pro v�b�r adres��e. Takto p�id�me:</p>

<p class="src0">C:\PROGRAM FILES\GNUWIN32\INCLUDE\FREETYPE32</p>

<p>a</p>

<p class="src0">C:\PROGRAM FILES\GNUWIN\INCLUDE</p>

<p>Pod Show Directories For vybereme Library Files a p�id�me</p>

<p class="src0">C:\PROGRAM FILES\GNUWIN32\LIB</p>

<p>Na tomto m�st� bychom m�li b�t schopni kompilovat programy, kter� pou��vaj� FreeType, ale nep�jdou spustit bez dynamick� knihovny freetype-6.dll. Kopii tohoto souboru naleznete v GNUWIN32\BIN. Je t�eba ji um�stit do adres��e, ve kter�m syst�m p�i spou�t�n� program� knihovny hled� (nap�. C:\PROGRAM FILES\MICROSOFT\VISUAL STUDIO\VC98\BIN nebo C:\WINDOWS\SYSTEM pro WIN9x, C:\WINNT\SYSTEM32 pro WIN NT/2000/XP). P�ekl.: Osobn� doporu�uji podobn� DLL knihovny v�hradn� vkl�dat do adres��e, ve kter�m se nach�z� spoust�n� exe soubor, proto�e a� budete v� program n�komu kop�rovat, nikdy na n� nezapomenete (Tak ten tv�j program mi ne�el spustit!).</p>

<p>OK, tak te� u� kone�n� m��eme za��t programovat. Jako z�klad vezmeme lekci 13. Zkop�rujeme soubor lesson13.cpp a p�id�me ho do projektu. Stejn� tak zkop�rujeme dva nov� soubory freetype.h a freetype.cpp, do kter�ch budeme p�id�vat v�echen FreeTypov� k�d. A� skon��me, budeme m�t jednoduchou FreeType knihovnu, kterou budeme moci vyu��t i v jin�ch OpenGL programech.</p>

<p>Za�neme vytv��en�m freetype.h. Samoz�ejm� mus�me nadefinovat hlavi�ky FreeType a OpenGL. Tak� p�id�me p�r u�ite�n�ch ��st� ze Standard Template Library (STL). Konkr�tn� se jedn� o t��dy vyj�mek, kter� n�m zjednodu�� vytv��en� p�kn�ch debugov�ch zpr�v. Pou�it� STL zvy�uje �anci, �e n�kdo jin�, kdo pou��v� n� k�d, �sp�n� zachyt� v�echny poslan� vyj�mky.</p>

<p class="src0">#ifndef FREE_NEHE_H</p>
<p class="src0">#define FREE_NEHE_H</p>
<p class="src"></p>
<p class="src0">#include &lt;windows.h&gt;</p>
<p class="src"></p>
<p class="src0"><span class="kom">// FreeType hlavi�ky</span></p>
<p class="src0">#include &lt;ft2build.h&gt;</p>
<p class="src0">#include &lt;freetype/freetype.h&gt;</p>
<p class="src0">#include &lt;freetype/ftglyph.h&gt;</p>
<p class="src0">#include &lt;freetype/ftoutln.h&gt;</p>
<p class="src0">#include &lt;freetype/fttrigon.h&gt;</p>
<p class="src"></p>
<p class="src0"><span class="kom">// OpenGL hlavi�ky</span></p>
<p class="src0">#include &lt;GL/gl.h&gt;</p>
<p class="src0">#include &lt;GL/glu.h&gt;</p>
<p class="src"></p>
<p class="src0"><span class="kom">// STL hlavi�ky</span></p>
<p class="src0">#include &lt;vector&gt;</p>
<p class="src0">#include &lt;string&gt;</p>
<p class="src"></p>
<p class="src0"><span class="kom">// STL vyj�mky</span></p>
<p class="src0">#include &lt;stdexcept&gt;</p>

<p>N�sleduj�c� pragma MSVC zabr�n�, aby oznamovalo zbyte�n� varov�n� o vektorech �et�zc�.</p>

<p class="src0">#pragma warning(disable: 4786)</p>

<p>V�echny informace, kter� ka�d� font pot�ebuje, d�me do jedn� struktury (toto uleh�� pr�ci s v�ce p�smy). Jak jsme se nau�ili v lekci 13, kdy� WGL vytv��� font, generuje sadu display list� s postupn� se zvy�uj�c�m ID. To je �ikovn�, proto�e d�ky tomu m��eme pro vyps�n� cel�ho �et�zce pou��t jedin� p��kaz glCallLists(). V na�� knihovn� nastav�me v�echno �pln� stejn�, co� znamen�, �e pole list_base bude ukl�dat prvn�ch 128 display list� jednotliv�ch znak�. Proto�e se chyst�me pro vykreslov�n� pou��t textury, pot�ebujeme tak� ulo�it 128 asociovan�ch textur. Posledn�, co ud�l�me, je deklarov�n� prom�nn� ozna�uj�c� v��ku vytv��en�ho fontu v pixelech, kter� n�m umo�n� vypisovat i zalomen� ��dk� ozna�en� \n v �et�zci.</p>

<p class="src0">namespace freetype<span class="kom">// Proti v�cen�sobn�mu pou�it� stejn�ch identifik�tor�</span></p>
<p class="src0">{</p>
<p class="src1">using std::vector;<span class="kom">// Mo�nost ps�t jen vector nam�sto std::vector</span></p>
<p class="src1">using std::string;<span class="kom">// To sam� pro string</span></p>
<p class="src"></p>
<p class="src1">struct font_data<span class="kom">// Zapouzd�en� v�eho do struktury</span></p>
<p class="src1">{</p>
<p class="src2">float h;<span class="kom">// V��ka fontu</span></p>
<p class="src2">GLuint* textures;<span class="kom">// ID textur</span></p>
<p class="src2">GLuint list_base;<span class="kom">// ID prvn�ho display listu</span></p>
<p class="src"></p>
<p class="src2">void init(const char* fname, unsigned int h);<span class="kom">// Vytvo�en� p�sma s v��kou h ze souboru fname</span></p>
<p class="src2">void clean();<span class="kom">// Uvoln�n� v�ech prost�edk� spojen�ch s fontem</span></p>
<p class="src1">};</p>

<p>Funkce print() vykresl� zadan� text na sou�adnic�ch x, y. Modelview matice bude tak� aplikovan� na text.</p>

<p class="src1">void print(const font_data &amp;ft_font, float x, float y, const char *fmt, ...);<span class="kom">// Vykresl� text</span></p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">#endif</p>

<p>To je konec hlavi�kov�ho souboru freetype.h, te� otev�eme freetype.cpp.</p>

<p class="src0">#include &quot;freetype.h&quot;<span class="kom">// Vlo�� freetype.h</span></p>
<p class="src"></p>
<p class="src0">namespace freetype</p>
<p class="src0">{</p>

<p>Pro vykreslen� znak� budeme pou��vat textury, kter� mus� samoz�ejm� m�t rozm�ry mocniny ��sla 2. N�sleduj�c� funkce vr�t� prvn� mocninu dvojky, kter� se rovn� nebo je v�t�� ne� p�edan� ��slo.</p>

<p class="src1">inline int next_p2(int a)<span class="kom">// Vr�t� n�sleduj�c� mocninu ��sla 2</span></p>
<p class="src1">{</p>
<p class="src2">int rval = 1;<span class="kom">// Nastav� bit vpravo do jedni�ky</span></p>
<p class="src"></p>
<p class="src2">while(rval &lt; a)<span class="kom">// Dokud je nalezen� mocnina men�� ne� minimum</span></p>
<p class="src2">{</p>
<p class="src3">rval &lt;&lt;= 1;<span class="kom">// Z�sk�n� dal�� mocniny (rotace bit� doleva, rychlej�� zp�sob, jak napsat rval *= 2)</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return rval;<span class="kom">// Vr�cen� v�sledku</span></p>
<p class="src1">}</p>

<p>Dal�� funkce, kterou budeme pot�ebovat, je srdcem tohoto k�du. Make_dlist() vytvo�� display list podle poslan�ho znaku, parametr FT_Face p�edstavuje objekt, kter� FreeType pou��v� pro uchov�n� informac� o fontu. Funkci se d�le p�ed�v� ID z�kladn�ho display listu a ukazatel na texturu.</p>

<p class="src1">void make_dlist(FT_Face face, char ch, GLuint list_base, GLuint* tex_base)<span class="kom">// Vytvo�� display list pro dan� znak</span></p>
<p class="src1">{</p>

<p>Na za��tku po��d�me FreeType o vykreslen� dan�ho znaku do bitmapy.</p>

<p class="src2">if(FT_Load_Glyph(face, FT_Get_Char_Index(face, ch), FT_LOAD_DEFAULT))<span class="kom">// Na�te glyph znaku</span></p>
<p class="src2">{</p>
<p class="src3">throw std::runtime_error(&quot;FT_Load_Glyph failed&quot;);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">FT_Glyph glyph;<span class="kom">// Glyph objekt</span></p>
<p class="src"></p>
<p class="src2">if(FT_Get_Glyph(face-&gt;glyph, &amp;glyph))<span class="kom">// P�esun glyphu do glyph objektu</span></p>
<p class="src2">{</p>
<p class="src3">throw std::runtime_error(&quot;FT_Get_Glyph failed&quot;);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">FT_Glyph_To_Bitmap(&amp;glyph, ft_render_mode_normal, 0, 1);<span class="kom">// Konvertov�n� glyphu na bitmapu</span></p>
<p class="src2">FT_BitmapGlyph bitmap_glyph = (FT_BitmapGlyph)glyph;</p>
<p class="src"></p>
<p class="src2">FT_Bitmap&amp; bitmap = bitmap_glyph-&gt;bitmap;<span class="kom">// Reference uleh�� p��stup k bitmap�</span></p>

<p>Te�, kdy� m�me pomoc� FreeType vytvo�enu bitmapu, pot�ebujeme do n� doplnit pr�zdn� pixely, abychom ji mohli pou��t v OpenGL. Je d�le�it� zapamatovat si, �e zat�mco OpenGL pou��v� term�n bitmapa k ozna�en� bin�rn�ch obr�zk�, ve FreeType bitmapa ukl�d� 8 bit� informace na pixel, tak�e m��e ukl�dat i �ed� slo�ky, kter� jsou pot�ebn� pro vyhlazen� text.</p>

<p class="src2">int width = next_p2(bitmap.width);<span class="kom">// Velikost textury - mocnina ��sla 2</span></p>
<p class="src2">int height = next_p2(bitmap.rows);</p>
<p class="src"></p>
<p class="src2">GLubyte* expanded_data = new GLubyte[2 * width * height];<span class="kom">// Alokace pam�ti</span></p>

<p>V�imn�te si, �e pou��v�me dvoukan�lovou bitmapu, prvn� kan�l pro z��ivost a druh� pro alfu. Oba p�i�ad�me hodnot�, kterou jsme nalezli ve FreeType bitmap�. Tern�ln� oper�tor ? : pou�ijeme pro ur�en� nulov� hodnoty, pokud se nach�z�me v okrajov� z�n� (zv�t�en� pro mocninu 2), v ostatn�ch p��padech plat� hodnota p�evzat� z FreeType bitmapy.</p>

<p class="src2">for(int j = 0; j &lt; height; j++)</p>
<p class="src2">{</p>
<p class="src3">for(int i = 0; i &lt; width; i++)</p>
<p class="src3">{</p>
<p class="src4">expanded_data[2 * (i + j*width)] = expanded_data[2 * (i + j*width) + 1] = (i &gt;= bitmap.width || j &gt;= bitmap.rows) ? 0 : bitmap.buffer[i + bitmap.width*j];</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Jsme hotovi, tak�e se m��eme pustit do vytv��en� OpenGL textury. Proto�e zahrneme alfa kan�l, �ern� ��sti bitapy budou v�dy pr�hledn� a okraje textu budou plynule pr�svitn�. Text by m�l vypadat spr�vn� na jak�mkoli podklad�. Jak u� jsem napsal, texturu vytv���me ze slo�ek luminance a alfa.</p>

<p class="src2"><span class="kom">// Nastaven� parametr� textury</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, tex_base[ch]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);</p>
<p class="src"></p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, GL_RGBA, width, height, 0, GL_LUMINANCE_ALPHA, GL_UNSIGNED_BYTE, expanded_data);</p>
<p class="src"></p>
<p class="src2">delete [] expanded_data;<span class="kom">// Uvoln�n� pam�ti bitmapy</span></p>

<p>Na vykreslen� znaku pou�ijeme otexturovan� �ty��heln�ky. To znamen�, �e bude jednoduch� ot��et, zv�t�ovat i zmen�ovat text a dokonce bude od OpenGL d�dit barvu.</p>

<p class="src2">glNewList(list_base + ch, GL_COMPILE);<span class="kom">// Vytvo�en� display listu</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, tex_base[ch]);</p>

<p>Nejd��v pohneme kamerou trochu doprava, aby byl znak vycentrovan� mezi minul�m a n�sleduj�c�m. Ulo��me matici a pokud pracujeme se znakem typu g nebo y, posuneme se trochu dol�.</p>

<p class="src3">glTranslatef(bitmap_glyph-&gt;left, 0, 0);<span class="kom">// Vycentrov�n� znaku mezi minul�m a n�sleduj�c�m</span></p>
<p class="src"></p>
<p class="src3">glPushMatrix();</p>
<p class="src4">glTranslatef(0, bitmap_glyph-&gt;top - bitmap.rows, 0);<span class="kom">// Posun o trochu dol�</span></p>

<p>Mus�me po��tat s faktem, �e mnoho textur je na okraji vypln�n�ch pr�zdn�m m�stem. Zjist�me, jak� ��st textury je znakem pou��v�na a tuto hodnotu ulo��me do pomocn�ch prom�nn�ch x a y, kterou p�ed kreslen�m p�ed�me funkci glTexCoord2d().</p>

<p class="src4">float x = (float)bitmap.width / (float)width;</p>
<p class="src4">float y = (float)bitmap.rows / (float)height;</p>

<p>Na tomto m�st� vykresl�me otexturovan� obd�ln�k. Bitmapa, kterou jsme z�skali pomoc� FreeType nen� orientovan� p�esn� tak, jak by m�la, ale to n�m nevad�, proto�e m��eme explicitn� ur�it polohu pro spr�vn� zarovn�n�.</p>

<p class="src4">glBegin(GL_QUADS);<span class="kom">// Vykreslen� znaku</span></p>
<p class="src5">glTexCoord2d(0, 0); glVertex2f(0, bitmap.rows);</p>
<p class="src5">glTexCoord2d(0, y); glVertex2f(0, 0);</p>
<p class="src5">glTexCoord2d(x, y); glVertex2f(bitmap.width, 0);</p>
<p class="src5">glTexCoord2d(x, 0); glVertex2f(bitmap.width, bitmap.rows);</p>
<p class="src4">glEnd();</p>
<p class="src3">glPopMatrix();</p>
<p class="src"></p>
<p class="src3">glTranslatef(face-&gt;glyph-&gt;advance.x &gt;&gt; 6, 0, 0);</p>

<p>Inkrementujeme pozici v rastru stejn�, jako bychom pracovali s bitmapov�m fontem. To je nutn� pouze, pokud bychom cht�li spo��tat aktu�ln� d�lku textu. Proto jsem ��dek zakomentoval.</p>

<p class="src3"><span class="kom">// glBitmap(0, 0, 0, 0, face->glyph->advance.x &gt;&gt; 6, 0, NULL);</span></p>
<p class="src"></p>
<p class="src2">glEndList();<span class="kom">// Ukon��me display list</span></p>
<p class="src1">}</p>

<p>Dal�� funkce, kterou se chyst�me vytvo�it, bude pou��vat make_dlist() pro vytvo�en� mno�iny display list� odpov�daj�c�ch dan�mu souboru s fontem a v��ce v pixelech. FreeType pou��v� truetype fonty, tak�e budeme pot�ebovat n�jak� .ttf soubor s fontem. Truetypov� p�sma jsou velmi b�n�, existuje spousta m�st na internetu, kde si je m��ete st�hnout. Jednodu��� bude ale pod�vat se do adres��e windows/fonts.</p>

<p class="src1">void font_data::init(const char * fname, unsigned int h)<span class="kom">// Vytvo�en� fontu</span></p>
<p class="src1">{</p>
<p class="src2">textures = new GLuint[128];<span class="kom">// Pam� pro ID textur</span></p>
<p class="src"></p>
<p class="src2">this-&gt;h = h;</p>
<p class="src"></p>
<p class="src2">FT_Library library;<span class="kom">// Vytvo�en� FreeType</span></p>
<p class="src"></p>
<p class="src2">if (FT_Init_FreeType(&amp;library))<span class="kom">// Inicializace FreeType</span></p>
<p class="src2">{</p>
<p class="src3">throw std::runtime_error(&quot;FT_Init_FreeType failed&quot;);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">FT_Face face;<span class="kom">// Objekt pro informace o fontu</span></p>

<p>Na tomto m�st� se pokus�me na��st ze souboru data fontu. Ze v�ech m�st, kde se k�d m��e zaseknout je pr�v� toto nej�ast�j��, proto�e soubor nap�. nemus� existovat nebo m��e b�t n�jak�m zp�sobem po�kozen.</p>

<p class="src2">if (FT_New_Face(library, fname, 0, &amp;face))<span class="kom">// Na�ten� fontu ze souboru</span></p>
<p class="src2">{</p>
<p class="src3">throw std::runtime_error(&quot;FT_New_Face failed (there is probably a problem with your font file)&quot;);</p>
<p class="src2">}</p>

<p>Z n�jak�m nevysv�tliteln�ch d�vod� m��� FreeType velikost p�sma v 1/64-n�ch pixel�. Proto, abychom m�li font vysok� h pixel�, mus�me p�ed�vat velikost n�sobenou ��slem 64. H &lt;&lt; 6 je jen rychlej�� zp�sob psan� t�to operace.</p>

<p class="src2">FT_Set_Char_Size(face, h &lt;&lt; 6, h &lt;&lt; 6, 96, 96);</p>

<p>P�ekl.: Anglick� znakov� sad� sta�� pouze 128 znak�, ale �e�tina obsahuje nav�c h��ky a ��rky, tak�e pokud je chcete pou��vat, mus�te upravit k�d.</p>

<p class="src2">list_base = glGenLists(128);<span class="kom">// 128 display list� a textur</span></p>
<p class="src2">glGenTextures(128, textures);</p>
<p class="src"></p>
<p class="src2">for(unsigned char i = 0; i &lt; 128; i++)<span class="kom">// Vytvo�en� display list� znak�</span></p>
<p class="src2">{</p>
<p class="src3">make_dlist(face, i, list_base, textures);</p>
<p class="src2">}</p>

<p>Proto�e v�echna data m�me ulo�ena v display listech a textur�ch, m��eme uvolnit pou�it� zdroje FreeType.</p>

<p class="src2">FT_Done_Face(face);<span class="kom">// Uvoln�n� zdroj�</span></p>
<p class="src2">FT_Done_FreeType(library);</p>
<p class="src1">}</p>

<p>Vytvo��me clean() funkci, kter� uvoln� v�echny prost�edky spojen� s display listy a texturami.</p>

<p class="src1">void font_data::clean()</p>
<p class="src1">{</p>
<p class="src2">glDeleteLists(list_base, 128);</p>
<p class="src2">glDeleteTextures(128, textures);</p>
<p class="src2">delete [] textures;</p>
<p class="src1">}</p>

<p>N�sleduj� dv� pomocn� funkce pro print(), kter� bude cht�t operovat ne v OpenGL jednotk�ch, ale v pixelov�ch sou�adnic�ch okna. Nula se bude nach�zet v lev�m horn�m rohu.</p>

<p class="src1">inline void pushScreenCoordinateMatrix()<span class="kom">// P�epne do pravo�hl� projekce</span></p>
<p class="src1">{</p>
<p class="src2">glPushAttrib(GL_TRANSFORM_BIT);</p>
<p class="src2">GLint viewport[4];</p>
<p class="src2">glGetIntegerv(GL_VIEWPORT, viewport);</p>
<p class="src2">glMatrixMode(GL_PROJECTION);</p>
<p class="src2">glPushMatrix();</p>
<p class="src2">glLoadIdentity();</p>
<p class="src2">gluOrtho2D(viewport[0], viewport[2], viewport[1], viewport[3]);</p>
<p class="src2">glPopAttrib();</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">inline void pop_projection_matrix()<span class="kom">// Obnov� perspektivu</span></p>
<p class="src1">{</p>
<p class="src2">glPushAttrib(GL_TRANSFORM_BIT);</p>
<p class="src2">glMatrixMode(GL_PROJECTION);</p>
<p class="src2">glPopMatrix();</p>
<p class="src2">glPopAttrib();</p>
<p class="src1">}</p>

<p>Nov� funkce print() vypad� velmi podobn� jako ta z lekce 13, ale je tu p�r rozd�l�. Nastav�me jin� OpenGL flagy, proto�e pou��v�me pouze dvoukan�lov� textury nam�sto bitmap. Abychom zajistili p�echody na nov� ��dky, p�id�me n�kolik extra v�po�t� a samoz�ejm� nesm�me zapomenout na zaji�t�n� toho, aby v�echna nastaven� byla po v�stupu z funkce ve stejn� stavu jako p�ed vstupem.</p>

<p class="src1">void print(const font_data &amp;ft_font, float x, float y, const char *fmt, ...)<span class="kom">// Rendering textu</span></p>
<p class="src1">{</p>
<p class="src2">pushScreenCoordinateMatrix();<span class="kom">// Sou�adn� soustava v pixelech</span></p>
<p class="src2"></p>
<p class="src2">GLuint font = ft_font.list_base;</p>
<p class="src2">float h = ft_font.h / 0.63f;<span class="kom">// V�t�� mezera mezi ��dky</span></p>
<p class="src2"></p>
<p class="src2">char text[256];<span class="kom">// V�sledn� �et�zec</span></p>
<p class="src2">va_list ap;<span class="kom">// Ukazatel na argumenty funkce</span></p>
<p class="src"></p>
<p class="src2">if (fmt == NULL)<span class="kom">// Byl p�ed�n text?</span></p>
<p class="src2">{</p>
<p class="src3">*text = 0;<span class="kom">// Nic ned�lat</span></p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">va_start(ap, fmt);<span class="kom">// Anal�za �et�zce na prom�nn�</span></p>
<p class="src4">vsprintf(text, fmt, ap);<span class="kom">// Konvertov�n� symbol� na ��sla</span></p>
<p class="src3">va_end(ap);<span class="kom">// V�sledky jsou ulo�eny do text</span></p>
<p class="src2">}</p>

<p>N�sleduj�c� k�d rozd�l� dan� text na sadu ��dk�. Velmi jednodu�e by se to dalo prov�st pomoc� regul�rn�ch v�raz�, jedna takov� knihovna je dostupn� nap�. na boost.org, ale my nic takov�ho pou��vat nebudeme - jednodu�e se sna��m udr�et k�d bez zbyte�n�ch z�vislost� na m�n� nutn�ch knihovn�ch.</p>

<p class="src2"><span class="kom">// Rozd�len� �et�zce na jednotliv� ��dky</span></p>
<p class="src2">const char *start_line = text;</p>
<p class="src2">vector&lt;string&gt; lines;</p>
<p class="src"></p>
<p class="src2">for(const char *c = text; *c; c++)</p>
<p class="src2">{</p>
<p class="src3">if(*c == '\n')</p>
<p class="src3">{</p>
<p class="src4">string line;</p>
<p class="src"></p>
<p class="src4">for(const char *n = start_line; n &lt; c; n++)</p>
<p class="src4">{</p>
<p class="src5">line.append(1, *n);</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">lines.push_back(line);</p>
<p class="src4">start_line = c + 1;</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">if(start_line)</p>
<p class="src2">{</p>
<p class="src3">string line;</p>
<p class="src3">for(const char *n = start_line; n &lt; c; n++)</p>
<p class="src3">{</p>
<p class="src4">line.append(1, *n);</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">lines.push_back(line);</p>
<p class="src2">}</p>

<p>Z�lohujeme v�echny OpenGL parametry a potom je nastav�me na nutn� hodnoty.</p>

<p class="src2">glPushAttrib(GL_LIST_BIT | GL_CURRENT_BIT  | GL_ENABLE_BIT | GL_TRANSFORM_BIT);</p>
<p class="src"></p>
<p class="src2">glMatrixMode(GL_MODELVIEW);</p>
<p class="src2">glDisable(GL_LIGHTING);</p>
<p class="src2">glEnable(GL_TEXTURE_2D);</p>
<p class="src2">glDisable(GL_DEPTH_TEST);</p>
<p class="src2">glEnable(GL_BLEND);</p>
<p class="src2">glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);</p>
<p class="src"></p>
<p class="src2">glListBase(font);</p>

<p>V�echny transformace, kter� provedeme na modelview matici p�ed vol�n�m t�to funkce, se projev� i na textu samotn�m. To znamen�, �e p�i v�pisu textu m�me mo�nost rotovat nebo m�nit jeho velikost. Nejp�irozen�j�� cestou by bylo ponechat p�vodn� matici, tak jak byla, ale to nebude pracovat, proto�e chceme m�t kontrolu nad pozic� textu. Dal�� mo�nost� by bylo vytvo�it kopii matice a mezi glTranslatef() a glCallLists() ji aplikovat, nicm�n� m���tko projek�n� matice te� u� nen� v OpenGL jednotk�ch, ale v pixelech, tak�e bychom z�skali tro�ku odli�n� efekt, ne� by n�kdo mohl o�ek�vat. P�es toto bychom se tak� mohli dostat neresetov�n projek�n� matice uvnit� print(). To je v n�kter�ch situac�ch docela dobr� n�pad, ale pokud to budete zkou�et, zajist�te, �e fonty budou m�t odpov�daj�c� velikost (jsou nastaveny na 32x32, ale vy pravd�podobn� budete pot�ebovat n�co kolem 0,01x0,01). Zkuste uhodnout, kterou cestou jdeme my :-)</p>

<p class="src2">float modelview_matrix[16];</p>
<p class="src2">glGetFloatv(GL_MODELVIEW_MATRIX, modelview_matrix);</p>

<p>Zobrazov�n� textu se d�je pr�v� na tomto m�st�. Pro ka�d� ��dek resetujeme matici, aby za��nal na spr�vn� pozici. V�imn�te si, �e m�sto posunu dol� o v��ku h, ji rad�ji rovnou resetujeme. To proto, �e se p�i vykreslen� ka�d�ho znaku posouv�me doprava na pozici znaku za n�m.</p>

<p class="src2">for(int i = 0; i &lt; lines.size(); i++)<span class="kom">// Proch�z� jednotliv� ��dky textu</span></p>
<p class="src2">{</p>
<p class="src3">glPushMatrix();<span class="kom">// Z�loha matice</span></p>
<p class="src3">glLoadIdentity();<span class="kom">// Resetov�n� matice</span></p>
<p class="src3">glTranslatef(x, y - h*i, 0);<span class="kom">// P�esun na odpov�daj�c� pozici</span></p>
<p class="src3">glMultMatrixf(modelview_matrix);</p>

<p>Pokud byste pot�ebovali zjistit d�lku textu, kter� vytv���te odkomentujete n�sleduj�c� ��dky. Pokud se tak rozhodnete, mus�te odkomentovat i p��kaz glBitmap() v make_dlist().</p>

<p class="src3"><span class="kom">// glRasterPos2f(0, 0);</span></p>
<p class="src"></p>
<p class="src3">glCallLists(lines[i].length(), GL_UNSIGNED_BYTE, lines[i].c_str());<span class="kom">// Vykresl� ��dek textu</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// float rpos[4];</span></p>
<p class="src3"><span class="kom">// glGetFloatv(GL_CURRENT_RASTER_POSITION, rpos);</span></p>
<p class="src3"><span class="kom">// float len = x - rpos[0];</span></p>
<p class="src"></p>
<p class="src3">glPopMatrix();<span class="kom">// Obnoven� matice</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">glPopAttrib();<span class="kom">// Obnoven� OpenGL flag�</span></p>
<p class="src"></p>
<p class="src2">pop_projection_matrix();<span class="kom">// Obnoven� perspektivy</span></p>
<p class="src1">}</p>
<p class="src0">}<span class="kom">// Konec namespace</span></p>

<p>Knihovna je te� kompletn�. Abychom mohli vid�t v�sledek, otev�eme soubor lesson13.cpp a provedeme v n�m n�kolik men��ch zm�n. Za includov�n� hlavi�kov�ch soubor� vlo�te i freetype.h.</p>

<p class="src0">#include &quot;freetype.h&quot;<span class="kom">// Vlo�en� freetype</span></p>

<p>A kdy� u� jsme tu, deklarujeme i glob�ln� objekt font_data.</p>

<p class="src0">freetype::font_data our_font;<span class="kom">// Informace pro vytv��en� font</span></p>

<p>D�le pot�ebujeme font inicializovat...</p>

<p class="src0"><span class="kom">// InitGL()</span></p>
<p class="src1">our_font.init(&quot;test.TTF&quot;, 16);<span class="kom">// Vytvo�en� fontu</span></p>

<p>... a p�i skon�en� programu odstranit.</p>

<p class="src0"><span class="kom">// KillGLWindow()</span></p>
<p class="src1">our_font.clean();</p>

<p>Do funkce DrawGLScene() dopln�me v�pis FreeType fontu, kter� bude nav�c rotovat a m�nit svou velikost.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// V�echno vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Smaz�n� buffer�</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -1.0f);<span class="kom">// Posun o jednotku do obrazovky</span></p>
<p class="src1">glColor3ub(0, 0, 0xff);<span class="kom">// Modr� text</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslen� WGL textu</span></p>
<p class="src1">glRasterPos2f(-0.40f, 0.35f);</p>
<p class="src1">glPrint(&quot;Active WGL Bitmap Text With NeHe - %7.2f&quot;, cnt1);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslen� FreeType fontu</span></p>
<p class="src"></p>
<p class="src1">glColor3ub(0xff,0,0);<span class="kom">// �erven� text</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();</p>
<p class="src1">glLoadIdentity();</p>
<p class="src1">glRotatef(cnt1, 0, 0, 1);</p>
<p class="src1">glScalef(1, 0.8 + 0.3 * cos(cnt1 / 5), 1);</p>
<p class="src1">glTranslatef(-180, 0, 0);</p>
<p class="src"></p>
<p class="src1">freetype::print(our_font, 320, 240, &quot;Active FreeType Text - %7.2f&quot;, cnt1);</p>
<p class="src"></p>
<p class="src1">glPopMatrix();</p>

<p>Chcete-li otestovat i p�echody na nov� ��dky, odstra�te koment��.</p>

<p class="src1"><span class="kom">// freetype::print(our_font, 320, 200, &quot;Here\nthere\nbe\n\nnewlines\n.&quot;, cnt1);</span></p>
<p class="src"></p>
<p class="src1">cnt1 += 0.051f;<span class="kom">// Zv�t�en� hodnot v ��ta��ch</span></p>
<p class="src1">cnt2 += 0.005f;</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�e OK</span></p>
<p class="src0">}</p>

<p>Nakonec mus�me p�idat k�d pro odchyt�v�n� vyj�mek. P�ejdeme do WinMain() a na za��tku vyhled�me sekci try { }.</p>

<p class="src0"><span class="kom">// WinMain()</span></p>
<p class="src1">MSG msg;<span class="kom">// Struktura zpr�vy</span></p>
<p class="src1">BOOL done = FALSE;<span class="kom">// Prom�nn� pro ukon�en� cyklu</span></p>
<p class="src"></p>
<p class="src1">try<span class="kom">// Sekce, ve kter� se budou zachyt�vat vyj�mky</span></p>
<p class="src1">{</p>

<p>Konec funkce modifikujeme p�id�n�m catch { }, kter� vyp�e text vyj�mky.</p>

<p class="src2">KillGLWindow();<span class="kom">// Zru�en� okna</span></p>
<p class="src1">}</p>
<p class="src1">catch (std::exception &amp;e)<span class="kom">// O�et�en� vyj�mek</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, e.what(), &quot;CAUGHT AN EXCEPTION&quot;, MB_OK | MB_ICONINFORMATION);</p>
<p class="src1">}</p>

<p>Tak a te�, kdy� v programu nastane vyj�mka, se zobraz� text oznamuj�c� u�ivateli, co se stalo. Pozor, tento k�d m��e zpomalit v� program, tak�e se mo�n� p�i kompilov�n� kone�n� verze bude hodit vypnut� odchyt�v�n� vyj�mek (Project-&gt;Settings-&gt;C/C++, &quot;C++ Language&quot;).</p>

<p>Zkompilujte program. Po spu�t�n� byste m�li vid�t p�kn� text renderovan� pomoc� FreeType, kter� se pohybuje okolo origin�ln�ho textu z lekce 13.</p>

<h3>Obecn� pozn�mky</h3>

<p>Pravd�podobn� budete cht�t pr�v� vytvo�enou FreeType knihovnu je�t� d�le vylep�it. Konkr�tn� se m��e jednat nap�. o zarovn�v�n� textu na st�ed. K tomu budete pot�ebovat n�jak�m zp�sobem zjistit jeho d�lku. Jedn�m zp�sobem m��e b�t vlo�en� p��kazu glBitmap() do display listu, kter� bude modifikovat pozici v rastru. Prakticky v�echno u� je v k�du p�ipraveno, sta�� odkoment��ovat p��slu�n� p��kazy.</p>

<p>FreeType fonty zab�raj� tak� mnohem v�ce m�sta ne� oby�ejn� WGL bitmapov� font. Pokud z n�jak�ho d�vodu pot�ebujete �et�it texturovac� pam�, zkuste vytvo�it jednu texturu, kter� bude obsahovat matici v�ech znak�, stejnou jak� je v lekci 13.</p>

<p>Na rozd�l od bitmap obd�ln�ky s namapovanou texturou reprezentuj�c� text dob�e spolupracuj� s OpenGL picking funkcemi (lekce 32), co� velmi usnad�uje zji�t�n�, jestli n�kdo na text klikl my�� nebo p�es n�j p�ejel.</p>

<p>Nakonec uv�d�m odkazy na n�kolik knihoven font� pro OpenGL. Z�le�� pouze na v�s, jestli je budete cht�t pou��t m�sto tohoto k�du.</p>

<ul>
<li><?OdkazBlank('http://www.opengl.org/developers/faqs/technical/fonts.htm', 'GLTT');?> - tato knihovna je u� relativn� star�. Je zalo�ena na FreeType1. P�edpokl�d�m, �e pro kompilaci v MSVC6 budete pot�ebovat nal�zt kopii star� distribuce FreeType1.</li>
<li><?OdkazBlank('http://oglft.sourceforge.net/', 'OGLFT');?> - je zalo�eno na FreeType2, kompilace pod MSVC d� mo�n� trochu pr�ce, proto�e byla zam��ena p�edev��m pro Linux...</li>
<li><?OdkazBlank('http://homepages.paradise.net.nz/henryj/code/#FTGL', 'FTGL');?> - t�et� knihovna zalo�en� na FreeType, vyv�jena pro OS X.</li>
<li><?OdkazBlank('http://plib.sourceforge.net/fnt', 'FNT');?> - knihovna, kter� nen� zalo�ena na FreeType, je ��st� PLIB, m� hezk� interface, pou��v� vlastn� form�t font�, kompilace pod MSVC6 s minimem pot��...</li>
</ul>

<p class="autor">napsal: Sven Olsen <?VypisEmail('sven@sccs.swarthmore.edu');?><br />
do sloven�tiny p�elo�il: Pavel Hradsk� - PcMaster<?VypisEmail('pcmaster@stonline.sk');?><br />
do �e�tiny p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson43.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson43.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson43.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:agraves@bu.edu">Aaron Graves</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson43.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(43);?>
<?FceNeHeOkolniLekce(43);?>

<?
include 'p_end.php';
?>
