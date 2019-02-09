<?
$g_title = 'CZ NeHe OpenGL - Lekce 43 - FreeType Fonty v OpenGL';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(43);?>

<h1>Lekce 43 - FreeType Fonty v OpenGL</h1>

<p class="nadpis_clanku">Tak tu je r�chly tutori�l, ktor� v�m uk�e, ako pou��va� FreeType Font rendering library v OpenGL. Pou�it�m kni�nice FreeType m��eme vytvori� anti-aliasovan� text, ktor� vyzer� lep�ie ako text vytvoren� pou�it�m bitm�p (lekcia 13). N� text bude ma� aj in� v�hody - m��eme ho �ahko rotova� a tie� dobre spolupracuje s OpenGL vyberac�mi (picking) funkciami.</p>

<p class="netisk"><a href="tut_43.php">Verze v �e�tin�...</a></p>

<p>Motiv�cia: Tu je uk�ka toho ist�ho textu vytvoren�ho pomocou WGL bitmap a vykreslen�ho pomocou FreeType (oba Arial Black Kurz�va):</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_43_foo_foo.gif" width="158" height="37" alt="FreeType font a WGL font" /></div>

<p>Z�kladn� probl�m s pou�it�m bitmapov�ch fontov je, �e OpenGL bitmapy s� bin�rne obr�zky. To znamen�, �e OpenGL si pam�t� len 1 bit na 1 pixel. Ak zoomujete na text tvoren� pomocou WGL, v�sledok vyzer� asi takto:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_43_wgl.gif" width="185" height="85" alt="Zv�t�en� WGL font" /></div>

<p>Preto�e bitmapy s� bin�rne obr�zky, nie s� okolo nich �ed� pixely a to znamen�, �e vyzeraj� hor�ie. Na��astie je ve�mi jednoduch� vytvori� dobre vyzeraj�ce fonty pomocou GNU FreeType kni�nice. T�to kni�nicu pou��va aj Blizzard vo svojich hr�ch, tak�e to mus� by� dobr� :-))) Tu je pribl�en� text, ktor� bol vytvoren� s pomocou kni�nice FreeType:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_43_free.gif" width="196" height="85" alt="Zv�t�en� FreeType font" /></div>

<p>Ako m��eme vidie�, okolo hr�n sa nach�dza kopec �ed�ch pixelov, �o je typick� pre anti-aliasovan� (vyhlazen�) fonty. �ed� pixely skrṵuj� font, pri poh�ade z dia�ky.</p>

<p>Najprv si siahnite kni�nicu GNU FreeType z <?OdkazBlank('http://gnuwin32.sourceforge.net/packages/freetype.htm');?>. Stiahnite si bin�rky a v�voj�rske s�bory. Ke� to nain�talujete, ur�ite si v�imnite licen�n� podmienky - hovor� sa tam, �e ak pou�ijete FreeType vo vlastn�ch programoch, mus�te uvies� ich kredit niekde vo va�ej dokument�cii.</p>

<p>Teraz treba nastavi� MSVC, aby vedelo pou��va� FreeType. V menu Project - Settings - Link sa uistite, �e ste pridali libfreetype.lib do Object/libraries spolu s opengl32.lib, glaux.lib a glu32.lib (ak je to potrebn�).</p>

<p>�alej potrebujeme v Tools - Options - Directories prida� adres�re kni�nice FreeType. Pod Show Directories for vyberieme Include Files, dvojklik na pr�zdny riadok na spodku zoznamu, objav� sa ... tla��tko, ktor� pou�ijeme pre vybratie adres�ra. Takto prid�me:</p>

<p class="src0">C:\PROGRAM FILES\GNUWIN32\INCLUDE\FREETYPE32</p>

<p>a</p>

<p class="src0">C:\PROGRAM FILES\GNUWIN\INCLUDE</p>

<p>Teraz pod Show Directories For vyberieme Library Files a prid�me</p>

<p class="src0">C:\PROGRAM FILES\GNUWIN32\LIB</p>

<p>Na tomto mieste by sme u� mali by� schopn� kompilova� programy pou��vaj�ce FreeType, aj ke� nep�jdu bez freetype-6.dll. K�piu tohto s�boru m�me v GNUWIN32\BIN a umiestnime ho aj do adres�ra, kam v�etky programy vidia (napr. C:\PROGRAM FILES\MICROSOFT\VISUAL STUDIO\VC98\BIN alebo do C:\WINDOWS\SYSTEM, pre WIN9x alebo C:\WINNT\SYSTEM32 pre WIN NT/2000/XP). Pam�tajte, freetype-6.dll mus�te prilo�i� ku ka�d�mu programu �o vytvor�te.</p>

<p>Ok, teraz u� kone�ne m��eme za�a� programova�. Za z�klad programu zoberieme Lekciu 13. Tak�e si skop�rujte lesson13.cpp do v�ho adres�ra a pridajte ho do projektu. Pridajte aj skop�rujte dva nov� s�bory freetype.cpp a freetype.h. Do t�chto s�borov budeme prid�va� v�etok FreeTypov� k�d. Trochu modifikujeme k�d lekcie 13 aby sme si uk�zali �o sme nap�sali. Ke� skon��me, budemem ma� mal� jednoduch� OpenGL FreeType kni�nicu, ktor� m��ete pou�i� aj v in�ch OpenGL projektoch.</p>

<p>Za�neme s freetype.h. Samozrejme, treba prida� hlavi�ky FreeType a OpenGL. Tie� prid�me zop�r u�ito�n�ch �ast� z Standard Template Library (STL), vr�tane STL exception classes, ktor� n�m zjednodu�ia vytv�ranie pekn�ch debugovacich spr�v.</p>

<p class="src0">#ifndef FREE_NEHE_H</p>
<p class="src0">#define FREE_NEHE_H</p>
<p class="src"></p>
<p class="src0"><span class="kom">// FreeType hlavicky</span></p>
<p class="src0">#include &lt;ft2build.h&gt;</p>
<p class="src0">#include &lt;freetype/freetype.h&gt;</p>
<p class="src0">#include &lt;freetype/ftglyph.h&gt;</p>
<p class="src0">#include &lt;freetype/ftoutln.h&gt;</p>
<p class="src0">#include &lt;freetype/fttrigon.h&gt;</p>
<p class="src"></p>
<p class="src0"><span class="kom">// OpenGL hlavicky</span></p>
<p class="src0">#include &lt;windows.h&gt;</p>
<p class="src0">#include &lt;GL/gl.h&gt;</p>
<p class="src0">#include &lt;GL/glu.h&gt;</p>
<p class="src"></p>
<p class="src0"><span class="kom">// STL hlavicky</span></p>
<p class="src0">#include &lt;vector&gt;</p>
<p class="src0">#include &lt;string&gt;</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Pouzitie STL zvysuje sance, ze niekto iny, kto pouziva nas kod uspesne zachyti vsetky vynimky, co mu posleme</span></p>
<p class="src0">#include &lt;stdexcept&gt;</p>
<p class="src"></p>
<p class="src"></p>
<p class="src0"><span class="kom">// MSVC vypluje vsetky druhy zbytocnych varovani ak vytvorime vektory retazcov, tato pragma tomu zabrani</span></p>
<p class="src0">#pragma warning(disable: 4786)</p>

<p>V�etky inform�cie, ktor� ka�d� font potrebuje d�me do jednej �trukt�ry (toto u�ah�� pr�cu z viacer�mi p�smami). Ako sme sa nau�ili v lekcii 13, ke� WGL vytv�ra font, generuje sadu postupn�ch display listov. Toto je �ikovn�, lebo to znamen�, �e m��ete pou�i� glCallLists na vyp�sanie re�azca znakov iba jedn�m pr�kazom. Ke� vytvor�me na�e p�smo, nastav�me veci v�dy tak isto, �o znamen�, �e pole list_base si zapam�t� prv�ch 128 postupn�ch display listov. Ke�e sa chyst�me pou��va� text�ry, tie� si potrebujeme ulo�i� 128 text�r. Posledn� �o n�m treba je v��ka fontu, ktor� sme vytvorili v pixeloch (toto n�m umo�n� handlova� nov� riadky v na�ej print funkcii).</p>

<p class="src0"><span class="kom">// Dame vsetko do namespacu, tak mozme pouzit nazov funkcie ako print bez toho, aby sme sa bali ci uz taka niekde v kode nie je</span></p>
<p class="src0">namespace freetype</p>
<p class="src0">{</p>
<p class="src1">using std::vector;<span class="kom">// Vnurti tohto Namespacu, si umoznime pisat len vector namiesto std::vector</span></p>
<p class="src"></p>
<p class="src1">using std::string;<span class="kom">// To iste pre String</span></p>
<p class="src"></p>
<p class="src1">struct font_data<span class="kom">// Toto uchovava vsetky informacie spojene s hocijakym FreeType fontom, ktory chceme vytvorit</span></p>
<p class="src1">{</p>
<p class="src2">float h;<span class="kom">// Uchovava vysku fontu</span></p>
<p class="src2">GLuint* textures;<span class="kom">// ID textur</span></p>
<p class="src2">GLuint list_base;<span class="kom">// ID prveho display listu</span></p>
<p class="src"></p>
<p class="src2">void init(const char * fname, unsigned int h);<span class="kom">// Init funkcia vytvori pismo s vyskou h zo suboru fname</span></p>
<p class="src"></p>
<p class="src2">void clean();<span class="kom">// Uvolnenie vsetkcy prostriedkov spojenych s fontom</span></p>
<p class="src1">};</p>

<p>Posledn� vec, ktor� potrebujeme je prototyp pre na�u print funkciu.</p>

<p class="src1"><span class="kom">// Vlajkova funkcia kniznice - tato nam vykresli nas text na suradniciach X, Y</span></p>
<p class="src1"><span class="kom">// Pouzitim ft_font sucastna Modelview Matica bude tiez aplikovana na text</span></p>
<p class="src1">void print(const font_data &amp;ft_font, float x, float y, const char *fmt, ...) ;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">#endif</p>

<p>A to je koniec hlavi�kov�ho s�boru freetype.h. Nastal �as otvori� freetype.cpp.</p>

<p class="src0">#include &quot;freetype.h&quot;<span class="kom">// Zahr�me n� s�bor freetype.h</span></p>
<p class="src"></p>
<p class="src0">namespace freetype</p>
<p class="src0">{</p>

<p>Na vykreslenie ka�d�ho znaku pou�ijeme text�ru. OpenGL text�ry potrebuj� ma� rozmery ktor� s� mocninami 2, tak�e potrebujeme aby bitmapy vytvoren� FreeTypom mali povolen� ve�kos�. Na to potrebujeme t�to funkciu:</p>

<p class="src1"><span class="kom">// Tato funkcia vrati prvu mocninu 2 vacsiu alebo rovnu ako cele cislo, ktore jej dame ako parameter</span></p>
<p class="src1">inline int next_p2(int a)</p>
<p class="src1">{</p>
<p class="src2">int rval = 1;</p>
<p class="src"></p>
<p class="src2">while(rval &lt; a)</p>
<p class="src2">{</p>
<p class="src3">rval &lt;&lt;= 1;<span class="kom">// Krajsi sposob ako pisat rval *= 2</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return rval;</p>
<p class="src1">}</p>

<p>�al�ia funkcia, ktor� budeme potrebova� je make_dlist(). Je to naozaj srdce tohoto k�du. Parameter je FT_Face, je to objekt, ktor� FreeType pou��va na uchovanie inform�cii o fonte a vytv�ra display list pod�a toho, ak� znak je po�leme.</p>

<p class="src1">void make_dlist(FT_Face face, char ch, GLuint list_base, GLuint * tex_base)<span class="kom">// Vytvorme display list pre dany znak</span></p>
<p class="src1">{</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Prva vec, ktoru urobime je ze povieme FreeTypu aby vykrelil nas znak do bitmapy. Na to treba zopar FreeTypovych prikazov</span></p>
<p class="src"></p>
<p class="src2">if(FT_Load_Glyph(face, FT_Get_Char_Index(face, ch), FT_LOAD_DEFAULT))<span class="kom">// Nacitaj glyph pre nas znak</span></p>
<p class="src2">{</p>
<p class="src3">throw std::runtime_error(&quot;FT_Load_Glyph failed&quot;);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">FT_Glyph glyph;</p>
<p class="src"></p>
<p class="src2">if(FT_Get_Glyph(face-&gt;glyph, &amp;glyph))<span class="kom">// Presun glyph do glyph objektu</span></p>
<p class="src2">{</p>
<p class="src3">throw std::runtime_error(&quot;FT_Get_Glyph failed&quot;);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">FT_Glyph_To_Bitmap(&amp;glyph, ft_render_mode_normal, 0, 1);<span class="kom">// Konvertuj glyph na bitmapu</span></p>
<p class="src2">FT_BitmapGlyph bitmap_glyph = (FT_BitmapGlyph)glyph;</p>
<p class="src"></p>
<p class="src2">FT_Bitmap&amp; bitmap = bitmap_glyph-&gt;bitmap;<span class="kom">// Tota reference nam ulahci pristup k bitmape:</span></p>

<p>Teraz, ke� u� m�me bitmapu vytvoren� pomocou FreeType, potrebujeme ju vyplni� pr�zdnymi pixelmi aby umo�nili jej pou�itie v OpenGL. Je d�le�it� pam�ta� si, �e k�m OpenGL pou��va pojem bitmapy ako bin�rne obr�zky, vo FreeType bitmapy uchov�vaj� 8 bitov inform�ci� na pixel (256 mo�nost�), tak�e bitmapy FreeTypu m��u obsahova� aj �ed� farby aby vytvorili anti-aliasovan� text.</p>

<p class="src2"><span class="kom">// Pouzime nasu Helper funkciu aby sme dostali sirky bitmap, ktore budeme potrebovat na vytvorenie nasej textury</span></p>
<p class="src2">int width = next_p2(bitmap.width);</p>
<p class="src2">int height = next_p2(bitmap.rows);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Alokujme pamat na texturu</span></p>
<p class="src2">GLubyte* expanded_data = new GLubyte[2 * width * height];</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Tu naplnime data pre bitmapu.</span></p>
<p class="src2"><span class="kom">// Vsimnite si, ze pouzivame dvojkanalovu bitmapu</span></p>
<p class="src2"><span class="kom">// 1 kanal pre ziarivost a druhy ako alfa kanal</span></p>
<p class="src2"><span class="kom">// Ale priradime oba kanaly hodnote, ktoru najdeme vo FreeType bitmape</span></p>
<p class="src2"><span class="kom">// Pouzijeme ?: operator aby sme povedali ktora hodnota ma byt 0</span></p>
<p class="src2"><span class="kom">// ak sme v prazdnej zone a 1 ak sme v FreeType bimape</span></p>
<p class="src"></p>
<p class="src2">for(int j=0; j &lt;height;j++)</p>
<p class="src2">{</p>
<p class="src3">for(int i=0; i &lt; width; i++)</p>
<p class="src3">{</p>
<p class="src4">expanded_data[2 * (i + j*width)] = expanded_data[2 * (i + j*width) + 1] = (i &gt;= bitmap.width || j &gt;= bitmap.rows) ? 0 : bitmap.buffer[i + bitmap.width*j];</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Ke� sme hotov�, m��eme sa pusti� do vytv�rania OpenGL text�ry. Zah��ame alfa kan�l, tak�e �ierne �asti bitmapy bud� prieh�adn� a okraje textu bud� plynulo priesvitn� (preto by mali vyzera� spr�vne na akomko�vek podklade).</p>

<p class="src2"><span class="kom">// Teraz len nastavime parametre textury</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, tex_base[ch]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Tu vytvarame samotnu texturu, vsimnite si, ze pouzivame GL_LUMINANCE_ALPHA aby sme vyjadrili, ze pouzivame data 2 kanalov</span></p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, GL_RGBA, width, height, 0, GL_LUMINANCE_ALPHA, GL_UNSIGNED_BYTE, expanded_data);</p>
<p class="src"></p>
<p class="src2">delete [] expanded_data;<span class="kom">// Ked mame texturu vytvorenu, nepotrebujeme uz Expanded Data</span></p>

<p>Na vykreslenie n�ho textu pou��vame �tvoruholn�ky s text�rami. To znamen�, �e bude jednoduch� ot��a� a zv��ova�/zmen�ova� text a text bude dedi� farbu z aktu�lnej OpenGL farby (�o by tak nebolo, ak by sme pou��vali pixmapy). </p>

<p class="src2">glNewList(list_base + ch, GL_COMPILE);<span class="kom">// Teraz vytvorime Display List</span></p>
<p class="src"></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D,tex_base[ch]);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Najprv potrebujeme pohnut kameru trosku nahor tak, ze znak bude mat dost miesta medzi nim, a tym pred nim</span></p>
<p class="src2">glTranslatef(bitmap_glyph-&gt;left,0,0);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Teraz sa presunieme o kusok dole pre pripad ze bitmapa presahuje cez spodok </span></p>
<p class="src2"><span class="kom">// Toto plati len pre znaky ako 'g' alebo 'y' a pod.</span></p>
<p class="src"></p>
<p class="src2">glPushMatrix();</p>
<p class="src2">glTranslatef(0,bitmap_glyph-&gt;top-bitmap.rows,0);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Teraz musime ratat s faktom, ze vela z nasich textur je vyplnenych prazdnym miestom.</span></p>
<p class="src2"><span class="kom">// Vyjadrime aka cast textury je pouzivana aktualnym znakom</span></p>
<p class="src2"><span class="kom">// a ulozime tu informaciu do premennych x a y, potom vykreslime</span></p>
<p class="src2"><span class="kom">// stvorec, budeme ukazovat len na tie casti textury, ktore</span></p>
<p class="src2"><span class="kom">// znak sam obsahuje.</span></p>
<p class="src"></p>
<p class="src2">float x = (float)bitmap.width / (float)width;</p>
<p class="src2">float y = (float)bitmap.rows / (float)height;</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Tu vykreslime otexturovane stvoruholniky.</span></p>
<p class="src2"><span class="kom">// Bitmapa, ktoru sme ziskali z FreeTypu nebola</span></p>
<p class="src2"><span class="kom">// orientovana celkom tak, ako sme chceli, takze</span></p>
<p class="src2"><span class="kom">// dame texturu na stvoruholnik tak, aby bol</span></p>
<p class="src2"><span class="kom">// vysledok umiestneny spravne.</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);</p>
<p class="src3">glTexCoord2d(0,0); glVertex2f(0,bitmap.rows);</p>
<p class="src3">glTexCoord2d(0,y); glVertex2f(0,0);</p>
<p class="src3">glTexCoord2d(x,y); glVertex2f(bitmap.width,0);</p>
<p class="src3">glTexCoord2d(x,0); glVertex2f(bitmap.width,bitmap.rows);</p>
<p class="src2">glEnd();</p>
<p class="src2">glPopMatrix();</p>
<p class="src"></p>
<p class="src2">glTranslatef(face-&gt;glyph-&gt;advance.x &gt;&gt; 6 ,0,0);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Inkrementujeme poziciu rastra akoby sme pracovali s bitmapovym fontom.</span></p>
<p class="src2"><span class="kom">// Potrebne len ak chcete vyratat dlzku textu.</span></p>
<p class="src2"><span class="kom">// glBitmap(0, 0, 0, 0, face->glyph->advance.x >> 6, 0, NULL);</span></p>
<p class="src"></p>
<p class="src2">glEndList();<span class="kom">// Ukoncime display list</span></p>
<p class="src1">}</p>

<p>�al�ia funkcia, ktor� ideme vytvori� bude pou��va� make_dlist na vytvorenie mno�iny display listov kore�ponduj�cich k dan�mu s�boru s fontom a v��ke v pixeloch.</p>

<p>FreeType pou��va truetype fonty, tak�e budete potrebova� nejak� s�bory s truetype p�smami ako parametre tejto funkcie (s�bory .ttf).
Truetypov� p�sma s� ve�mi be�n� a je kopec str�nok, kde si ich m��ete stiahnu�. Aj Windows pou��va ttf ako v��inu p�siem, tak�e m��ete pou�i� aj tieto z adres�ra windows/fonts.</p>

<p class="src1">void font_data::init(const char * fname, unsigned int h)</p>
<p class="src1">{</p>
<p class="src2">textures = new GLuint[128];<span class="kom">// Alokujme pamat pre uchovanie ID cisel textur</span></p>
<p class="src"></p>
<p class="src2">this-&gt;h = h;</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvorime a inicializujeme kniznicu FreeType</span></p>
<p class="src2">FT_Library library;</p>
<p class="src"></p>
<p class="src2">if (FT_Init_FreeType(&amp;library))</p>
<p class="src2">{</p>
<p class="src3">throw std::runtime_error(&quot;FT_Init_FreeType failed&quot;);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Objekt, v ktorom uchovavame FreeType informacie pre dany font sa vola face</span></p>
<p class="src2">FT_Face face;</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Tu nacitavame informacie fontu zo suboru.</span></p>
<p class="src2"><span class="kom">// Spomedzi roznych miest kodu, najcastejsie tu sa zasekuje,</span></p>
<p class="src2"><span class="kom">// lebo FT_New_Face spadne ak subor s fontom neexistuje alebo je chybny.</span></p>
<p class="src"></p>
<p class="src2">if (FT_New_Face(library, fname, 0, &amp;face))</p>
<p class="src2">{</p>
<p class="src3">throw std::runtime_error(&quot;FT_New_Face failed (there is probably a problem with your font file)&quot;);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Pre nejaky nevysvetlitelny dovod, FreeType meria velkost pisma</span></p>
<p class="src2"><span class="kom">// v 1/64-nach pixelov. Preto, aby sme mali font vyskoky h pixelov,</span></p>
<p class="src2"><span class="kom">// musime davat velkost h * 64.</span></p>
<p class="src2"><span class="kom">// (h &lt;&lt; 6 je len krajsi sposob pisania h*64)</span></p>
<p class="src"></p>
<p class="src2">FT_Set_Char_Size(face, h &lt;&lt; 6, h &lt;&lt; 6, 96, 96);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Tu poziadame OpenGL o alokacie prostriedkov pre vsetky nase textury a display listy, ktore chceme vytvorit.</span></p>
<p class="src2">list_base = glGenLists(128);</p>
<p class="src2">glGenTextures(128, textures);</p>
<p class="src"></p>
<p class="src2">for(unsigned char i = 0; i &lt; 128; i++)<span class="kom">//Tu vytvarame kazdy z display listov</span></p>
<p class="src2">{</p>
<p class="src3">make_dlist(face, i, list_base, textures);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Nepotrebujeme face informacie, pretoze display list uz bol vytvoreny, tak uvolnime pridruzene prostriedky.</span></p>
<p class="src2">FT_Done_Face(face);</p>
<p class="src"></p>
<p class="src2">FT_Done_FreeType(library);<span class="kom">// To iste pre Font Library.</span></p>
<p class="src1">}</p>

<p>Teraz potrebujeme funkciu na vy�istenie v�etk�ch display listov a text�r spojen�ch s fontom.</p>

<p class="src1">void font_data::clean()</p>
<p class="src1">{</p>
<p class="src2">glDeleteLists(list_base, 128);</p>
<p class="src2">glDeleteTextures(128, textures);</p>
<p class="src2">delete [] textures;</p>
<p class="src1">}</p>

<p>Tu s� dve mal� funkcie, ktor� definujme v o�ak�van� na�ej print funkcie. Funkcia print bude chcie� myslie� v pixelov�ch s�radniciach (tie� naz�van� window coordinates), tak�e budeme potrebova� prepn�� do projek�nej matice, ktor� sp�sob� �e v�etko bude meran� v oknov�ch s�radniciach (od �av�ho horn�ho rohu).</p>

<p>Pou�ijeme dve ve�mi u�ito�n� funkcie, glGet() na z�skanie rozmerov okna a glPushAttrib / glPopAttrib na uistenie sa, �e sme nechali maticu v p�vodnom stave, ako sme ju na�li. Ak tieto funkcie nepozn�te, je pravdepodobne vhodn� vyh�ada� si ich vo va�ej ob��benom OpenGL manu�ly.</p>

<p class="src1"><span class="kom">// Dost samovystizna funkcia, ktora potlaci projekcnu maticu,</span></p>
<p class="src1"><span class="kom">// ktora urobi object world suradnice identickymi s suradnicami okna. </span></p>
<p class="src1">inline void pushScreenCoordinateMatrix()</p>
<p class="src1">{</p>
<p class="src2">glPushAttrib(GL_TRANSFORM_BIT);</p>
<p class="src2">GLint viewport[4];</p>
<p class="src2">glGetIntegerv(GL_VIEWPORT, viewport);</p>
<p class="src2">glMatrixMode(GL_PROJECTION);</p>
<p class="src2">glPushMatrix();</p>
<p class="src2">glLoadIdentity();</p>
<p class="src2">gluOrtho2D(viewport[0],viewport[2],viewport[1],viewport[3]);</p>
<p class="src2">glPopAttrib();</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Uvolni projekcnu maticu bez zmeny aktualneho MatrixModu</span></p>
<p class="src1">inline void pop_projection_matrix()</p>
<p class="src1">{</p>
<p class="src2">glPushAttrib(GL_TRANSFORM_BIT);</p>
<p class="src2">glMatrixMode(GL_PROJECTION);</p>
<p class="src2">glPopMatrix();</p>
<p class="src2">glPopAttrib();</p>
<p class="src1">}</p>

<p>Na�a funkcia print vyzer� ve�mi podobne ako t� z lekcie 13, ale je tu zop�r d�le�it�ch v�nimiek. OpenGL umo��uj�ce pr�znaky (flags), ktor� nastavujeme s� in�, �o sa odraz� vo fakte, �e pou��vame 2 kan�lov� text�ry namiesto bitm�p. Tie� urob�me trochu extra spracovania na riadku textu aby sme spr�vne zvl�dli nov� riadky. Pou�ijeme OpenGL mat�ce a attribute h�ldy (stacks) aby sme sa uistili, �e funkcia naprav� v�etky zmeny, ktor� rob� do intern�ho stavu OpenGL (toto zabr�ni tomu, aby ktoko�vek pou�il funkciu a zrazu zistil, �e sa ModelView matica z�hadne zmenila).</p>

<p class="src1"><span class="kom">// Skoro ako NeHe glPrint funckcia, ale modifikovana, aby pracovala s freetype fontmi</span></p>
<p class="src1">void print(const font_data &amp;ft_font, float x, float y, const char *fmt, ...)</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Chceme system suradnic, kde je je vzdialenost merana v pixeloch</span></p>
<p class="src2">pushScreenCoordinateMatrix();</p>
<p class="src2"></p>
<p class="src2">GLuint font = ft_font.list_base;</p>
<p class="src2">float h = ft_font.h / 0.63f;<span class="kom">// Trochu zvacsime v��ku, aby bola medzera medzi riadkami</span></p>
<p class="src2"></p>
<p class="src2">chartext[256];<span class="kom">// Nas retazec</span></p>
<p class="src2">va_listap;<span class="kom">// Ukazatel na nas zoznam argumentov</span></p>
<p class="src"></p>
<p class="src2">if (fmt == NULL)<span class="kom">// Ak nemame ziadny text</span></p>
<p class="src2">{</p>
<p class="src3">*text = 0;<span class="kom">// Nerobme nic</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">va_start(ap, fmt);<span class="kom">// Rozoberia retazec na premenne</span></p>
<p class="src4">vsprintf(text, fmt, ap);<span class="kom">// A konevrtuje symboly na cisla</span></p>
<p class="src3">va_end(ap);<span class="kom">// Vysledky sa ulozia do text</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Tento kod rozdeli dany text text na sadu riadkov.</span></p>
<p class="src2"><span class="kom">// Toto by sa dalo spravit ak milsia pomocou kni�nice regul�rnych v�razov,</span></p>
<p class="src2"><span class="kom">// dostupnych napr. na boost.org.</span></p>
<p class="src"></p>
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
<p class="src"></p>
<p class="src2">glPushAttrib(GL_LIST_BIT | GL_CURRENT_BIT  | GL_ENABLE_BIT | GL_TRANSFORM_BIT);</p>
<p class="src2">glMatrixMode(GL_MODELVIEW);</p>
<p class="src2">glDisable(GL_LIGHTING);</p>
<p class="src2">glEnable(GL_TEXTURE_2D);</p>
<p class="src2">glDisable(GL_DEPTH_TEST);</p>
<p class="src2">glEnable(GL_BLEND);</p>
<p class="src2">glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);</p>
<p class="src"></p>
<p class="src2">glListBase(font);</p>

<p>Preto�e pou��vame otexturovan� �tvoruholn�ky, v�etky transform�cie, ktor� urob�me do ModelView matice pred volan�m glCallLists sa prejavia na texte samotnom. To znamen�, �e je mo�nos� rotova�, alebo meni� ve�kos� textu (�al�ia v�hoda WGL bitm�p). Najprirodzenej�� sp�sob ako to vyu�i� by bolo necha� aktu�lnu maticu samotn�, teda urobi� v�etky transform�cie pred pou�it�m print funkcie. Ale kv�li sp�sobu ak�m pou��vame maticu ModelView na nastavenie poz�cie textu, toto nep�jde. Na�a �al�ia mo�nos� je ulo�i� si k�piu matice a aplikova� je medzi glTranslate a glCallLists. Toto je dos� jednoduch�, ale preto�e potrebujeme vykres�ova� text pou��vaj�c �peci�lnu projek�n� maticu, efekty modelview matice by boli trochu in� ako by sa dalo predpoklada� - v�etko by bolo interpretovan� vo ve�kosti pixelov. Cez toto by sme sa mohli dosta� resetovan�m projek�nej matice vn�tri funkcie print. To je asi dobr� n�pad - ale ak sa o to pok�site, ur�ite scalujte fonty na po�adovan� ve�kos� (sna�ia sa by� 32x32, ale vy asi potrebujete nie�o okolo 0.01x0.01).</p>

<p class="src2">float modelview_matrix[16];</p>
<p class="src2">glGetFloatv(GL_MODELVIEW_MATRIX, modelview_matrix);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Tu sa vlastne deje zobrazovanie textu.</span></p>
<p class="src2"><span class="kom">// Pre kazdy riadok textu resetneme maticu modelview aby</span></p>
<p class="src2"><span class="kom">// text zacal na spravnej pozicii.</span></p>
<p class="src2"><span class="kom">// Vsimnite si, ze potrebuejeme resetnut maticu, radsej ako ju</span></p>
<p class="src2"><span class="kom">// len posuvat dole o h. To preto, lebo ked kazdy znak je nakresleny,</span></p>
<p class="src2"><span class="kom">// modifikuje aktualnu maticu aby dalsi znak bol vykresleny hned za nim.</span></p>
<p class="src"></p>
<p class="src2">for(int i = 0; i &lt; lines.size(); i++)</p>
<p class="src2">{</p>
<p class="src3">glPushMatrix();</p>
<p class="src3">glLoadIdentity();</p>
<p class="src3">glTranslatef(x, y - h*i, 0);</p>
<p class="src3">glMultMatrixf(modelview_matrix);</p>
<p class="src"></p>

<p class="src3"><span class="kom">// Vypoznamkovane veci ohladom Raster pozicie by mohli byt uzitocne ak by ste potrebovali vediet dlzku textu, ktory vytvarate.</span></p>
<p class="src3"><span class="kom">// Ak sa rozhodnete pouzit to, ujistite sa ze tiez odpoznamkujete aj prikaz glBitmap v make_dlist().</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// glRasterPos2f(0,0);</span></p>
<p class="src"></p>
<p class="src3">glCallLists(lines[i].length(), GL_UNSIGNED_BYTE, lines[i].c_str());</p>
<p class="src"></p>
<p class="src3"><span class="kom">// float rpos[4];</span></p>
<p class="src3"><span class="kom">// glGetFloatv(GL_CURRENT_RASTER_POSITION, rpos);</span></p>
<p class="src3"><span class="kom">// float len = x - rpos[0];</span></p>
<p class="src"></p>
<p class="src3">glPopMatrix();</p>
<p class="src"></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">glPopAttrib();</p>
<p class="src"></p>
<p class="src2">pop_projection_matrix();</p>
<p class="src1">}</p>
<p class="src0">}<span class="kom">// Ukoncime Namespace</span></p>

<p>Kni�nica je teraz hotov�. Otvorme teraz lesson13.cpp a urob�me tam zop�r mal�ch zmien aby sme mohli pou�i� funkcie, ktor� sme pr�ve nap�sali. Pod ostatn�mi hlavi�kami, pridajte freetype.h.</p>

<p class="src0">#include &quot;freetype.h&quot;<span class="kom">// Hlavicka pre nasu kniznicu</span></p>

<p>A k�m sme tu, vytvorme glob�lny objekt font_data.</p>

<p class="src0">freetype::font_data our_font;<span class="kom">// Toto uchov�va v�etky inform�cie pre font, ktor� ideme vytvori�</span></p>

<p>Teraz potrebuejme vedie� ako vytvori� a zni�i� prostriedky n�ho fontu. Tak�e pridajte nasleduj�ci riadok na koniec InitGL.</p>

<p class="src0"><span class="kom">// InitGL()</span></p>
<p class="src1">our_font.init(&quot;test.TTF&quot;, 16);    <span class="kom">//Build the freetype font</span></p>

<p>A pridajte tento riadok na za�iatok KillGLWindow aby sa odstr�nil font, ke� program skon�il.</p>

<p class="src0"><span class="kom">// KillGLWindow()</span></p>
<p class="src1">our_font.clean();</p>

<p>Teraz mus�me zmeni� funkcie DrawGLScene, aby pou��vala na�u print funkciu. Toto by mohlo by� dos� ale s�a��me si to kreat�vnej��m postupom aby sme mohli font rotova� a meni� jeho ve�kos�.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Tu vsetko vykreslujeme</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vycistime zasobniky</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Resetnime aktualnu maticu ModelView</span></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -1.0f);<span class="kom">// Pohnime sa 1 jednotku do obrazovky</span></p>
<p class="src"></p>
<p class="src1">glColor3ub(0, 0, 0xff);<span class="kom">// Modr� text</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Umiestnime WGL Text na obrazovku</span></p>
<p class="src1">glRasterPos2f(-0.40f, 0.35f);</p>
<p class="src1">glPrint(&quot;Active WGL Bitmap Text With NeHe - %7.2f&quot;, cnt1);<span class="kom">// Vykreslime text do sceny</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Tu vykreslime nejaky text pomocou FreeTypu.</span></p>
<p class="src1"><span class="kom">// Jediny dolezity prikaz je Print(), ale aby sme vysledky trochu</span></p>
<p class="src1"><span class="kom">// zatraktivnlili, pridali sme kod na tocenie a scalovanie textu.</span></p>
<p class="src"></p>
<p class="src1">glColor3ub(0xff,0,0);<span class="kom">// Cerveny Text</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();</p>
<p class="src1">glLoadIdentity();</p>
<p class="src1">glRotatef(cnt1,0,0,1);</p>
<p class="src1">glScalef(1,.8+.3*cos(cnt1/5),1);</p>
<p class="src1">glTranslatef(-180,0,0);</p>
<p class="src"></p>
<p class="src1">freetype::print(our_font, 320, 240, &quot;Active FreeType Text - %7.2f&quot;, cnt1);</p>
<p class="src"></p>
<p class="src1">glPopMatrix();</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Odstrante tuto poznamu ak chcete vyskusat schopnost funkcie print zvladnut nove riadky</span></p>
<p class="src1"><span class="kom">// freetype::print(our_font, 320, 200, &quot;Here\nthere\nbe\n\nnewlines\n.&quot;, cnt1);</span></p>
<p class="src"></p>
<p class="src1">cnt1 += 0.051f;<span class="kom">// Zvysme prve pocitadlo</span></p>
<p class="src1">cnt2 += 0.005f;<span class="kom">// Zvysme druhe pocitadlo</span></p>
<p class="src1">return TRUE;<span class="kom">// Vsetko prebehlo OK</span></p>
<p class="src0">}</p>

<p>Posledn� vec, ktor� treba urobi�, je prida� k�d na handlovanie v�nimiek. Cho�te do WinMain a vyh�adajte odstavec try { .. } na za�iatku funkcie.</p>

<p class="src0"><span class="kom">// WinMain()</span></p>
<p class="src1">MSG msg;<span class="kom">// Windows Message Structure</span></p>
<p class="src1">BOOL done = FALSE;<span class="kom">// Bool Variable To Exit Loop</span></p>
<p class="src"></p>
<p class="src1">try<span class="kom">// Use exception handling</span></p>
<p class="src1">{</p>

<p>Potom modifikujte koniec funkcie aby sme mali prikaz catch { }.</p>

<p class="src2">KillGLWindow();<span class="kom">// Zru� okno</span></p>
<p class="src"></p>
<p class="src1">}</p>
<p class="src1">catch (std::exception &amp;e)<span class="kom">// Chyt vsetky dane vyjimky</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, e.what(), &quot;CAUGHT AN EXCEPTION&quot;, MB_OK | MB_ICONINFORMATION);</p>
<p class="src1">}</p>

<p>Tak a teraz ak natraf�me na v�nimku, dostaneme message box hovoriaci �o sa stalo. Pozor, tento k�d m��e spomali� v� program, tak�e ke� kompilujete kone�n� verziu v�ho programu, mo�no sa v�m bude hodi� vypn�� exception handling v Project->Settings->C/C++, "C++ Language".</p>

<p>A to je ono! Skompilujte program a mali by ste vidie� pekn� FreeType renderovan� text pohybuj�ci sa okolo origin�lneho bitmapov�ho textu z lekcie 13.</p>

<h3>Zop�r odkazov na z�ver:</h3>
<ul>
<li><?OdkazBlank('http://www.opengl.org/developers/faqs/technical/fonts.htm');?></li>
<li><?OdkazBlank('http://oglft.sourceforge.net/');?></li>
<li><?OdkazBlank('http://homepages.paradise.net.nz/henryj/code/#FTGL');?></li>
<li><?OdkazBlank('http://plib.sourceforge.net/fnt');?></li>
</ul>

<p class="autor">napsal: Sven Olsen <?VypisEmail('sven@sccs.swarthmore.edu');?><br />
do sloven�tiny p�elo�il: Pavel Hradsk� - PcMaster<?VypisEmail('pcmaster@stonline.sk');?><br />
do �e�tiny p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<p>Pozn.: Pros�m ospravedlote v�etky chyby v tomto �l�nku, je to m�j prv� preklad. D�fam, �e v�m pomohol, ak m�te nejak� probl�my s porozumen�m, alebo ste natrafili na z�va�n� chybu, kontaktujte pros�m m�a, alebo autora tohto webu.</p>

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
