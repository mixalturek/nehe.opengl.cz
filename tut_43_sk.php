<?
$g_title = 'CZ NeHe OpenGL - Lekce 43 - FreeType Fonty v OpenGL';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(43);?>

<h1>Lekce 43 - FreeType Fonty v OpenGL</h1>

<p class="nadpis_clanku">Tak tu je rýchly tutoriál, ktorý vám uká¾e, ako pou¾íva» FreeType Font rendering library v OpenGL. Pou¾itím kni¾nice FreeType mô¾eme vytvori» anti-aliasovaný text, ktorý vyzerá lep¹ie ako text vytvorený pou¾itím bitmáp (lekcia 13). Ná¹ text bude ma» aj iné výhody - mô¾eme ho µahko rotova» a tie¾ dobre spolupracuje s OpenGL vyberacími (picking) funkciami.</p>

<p class="netisk"><a href="tut_43.php">Verze v èe¹tinì...</a></p>

<p>Motivácia: Tu je uká¾ka toho istého textu vytvoreného pomocou WGL bitmap a vykresleného pomocou FreeType (oba Arial Black Kurzíva):</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_43_foo_foo.gif" width="158" height="37" alt="FreeType font a WGL font" /></div>

<p>Základný problém s pou¾itím bitmapových fontov je, ¾e OpenGL bitmapy sú binárne obrázky. To znamená, ¾e OpenGL si pamätá len 1 bit na 1 pixel. Ak zoomujete na text tvorený pomocou WGL, výsledok vyzerá asi takto:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_43_wgl.gif" width="185" height="85" alt="Zvìt¹ený WGL font" /></div>

<p>Preto¾e bitmapy sú binárne obrázky, nie sú okolo nich ¹edé pixely a to znamená, ¾e vyzerajú hor¹ie. Na¹»astie je veµmi jednoduché vytvori» dobre vyzerajúce fonty pomocou GNU FreeType kni¾nice. Túto kni¾nicu pou¾íva aj Blizzard vo svojich hrách, tak¾e to musí by» dobré :-))) Tu je priblí¾ený text, ktorý bol vytvorený s pomocou kni¾nice FreeType:</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_43_free.gif" width="196" height="85" alt="Zvìt¹ený FreeType font" /></div>

<p>Ako mô¾eme vidie», okolo hrán sa nachádza kopec ¹edých pixelov, èo je typické pre anti-aliasované (vyhlazené) fonty. ©edé pixely skrá¹µujú font, pri pohµade z diaµky.</p>

<p>Najprv si siahnite kni¾nicu GNU FreeType z <?OdkazBlank('http://gnuwin32.sourceforge.net/packages/freetype.htm');?>. Stiahnite si binárky a vývojárske súbory. Keï to nain¹talujete, urèite si v¹imnite licenèné podmienky - hovorí sa tam, ¾e ak pou¾ijete FreeType vo vlastných programoch, musíte uvies» ich kredit niekde vo va¹ej dokumentácii.</p>

<p>Teraz treba nastavi» MSVC, aby vedelo pou¾íva» FreeType. V menu Project - Settings - Link sa uistite, ¾e ste pridali libfreetype.lib do Object/libraries spolu s opengl32.lib, glaux.lib a glu32.lib (ak je to potrebné).</p>

<p>Ïalej potrebujeme v Tools - Options - Directories prida» adresáre kni¾nice FreeType. Pod Show Directories for vyberieme Include Files, dvojklik na prázdny riadok na spodku zoznamu, objaví sa ... tlaèítko, ktoré pou¾ijeme pre vybratie adresára. Takto pridáme:</p>

<p class="src0">C:\PROGRAM FILES\GNUWIN32\INCLUDE\FREETYPE32</p>

<p>a</p>

<p class="src0">C:\PROGRAM FILES\GNUWIN\INCLUDE</p>

<p>Teraz pod Show Directories For vyberieme Library Files a pridáme</p>

<p class="src0">C:\PROGRAM FILES\GNUWIN32\LIB</p>

<p>Na tomto mieste by sme u¾ mali by» schopní kompilova» programy pou¾ívajúce FreeType, aj keï nepôjdu bez freetype-6.dll. Kópiu tohto súboru máme v GNUWIN32\BIN a umiestnime ho aj do adresára, kam v¹etky programy vidia (napr. C:\PROGRAM FILES\MICROSOFT\VISUAL STUDIO\VC98\BIN alebo do C:\WINDOWS\SYSTEM, pre WIN9x alebo C:\WINNT\SYSTEM32 pre WIN NT/2000/XP). Pamätajte, freetype-6.dll musíte prilo¾i» ku ka¾dému programu èo vytvoríte.</p>

<p>Ok, teraz u¾ koneène mô¾eme zaèa» programova». Za základ programu zoberieme Lekciu 13. Tak¾e si skopírujte lesson13.cpp do vá¹ho adresára a pridajte ho do projektu. Pridajte aj skopírujte dva nové súbory freetype.cpp a freetype.h. Do týchto súborov budeme pridáva» v¹etok FreeTypový kód. Trochu modifikujeme kód lekcie 13 aby sme si ukázali èo sme napísali. Keï skonèíme, budemem ma» malú jednoduchú OpenGL FreeType kni¾nicu, ktorú mô¾ete pou¾i» aj v iných OpenGL projektoch.</p>

<p>Zaèneme s freetype.h. Samozrejme, treba prida» hlavièky FreeType a OpenGL. Tie¾ pridáme zopár u¾itoèných èastí z Standard Template Library (STL), vrátane STL exception classes, ktoré nám zjednodu¹ia vytváranie pekných debugovacich správ.</p>

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

<p>V¹etky informácie, ktoré ka¾dý font potrebuje dáme do jednej ¹truktúry (toto uµahèí prácu z viacerými písmami). Ako sme sa nauèili v lekcii 13, keï WGL vytvára font, generuje sadu postupných display listov. Toto je ¹ikovné, lebo to znamená, ¾e mô¾ete pou¾i» glCallLists na vypísanie re»azca znakov iba jedným príkazom. Keï vytvoríme na¹e písmo, nastavíme veci v¾dy tak isto, èo znamená, ¾e pole list_base si zapamätá prvých 128 postupných display listov. Keï¾e sa chystáme pou¾íva» textúry, tie¾ si potrebujeme ulo¾i» 128 textúr. Posledné èo nám treba je vý¹ka fontu, ktorý sme vytvorili v pixeloch (toto nám umo¾ní handlova» nové riadky v na¹ej print funkcii).</p>

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

<p>Posledná vec, ktorú potrebujeme je prototyp pre na¹u print funkciu.</p>

<p class="src1"><span class="kom">// Vlajkova funkcia kniznice - tato nam vykresli nas text na suradniciach X, Y</span></p>
<p class="src1"><span class="kom">// Pouzitim ft_font sucastna Modelview Matica bude tiez aplikovana na text</span></p>
<p class="src1">void print(const font_data &amp;ft_font, float x, float y, const char *fmt, ...) ;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">#endif</p>

<p>A to je koniec hlavièkového súboru freetype.h. Nastal èas otvori» freetype.cpp.</p>

<p class="src0">#include &quot;freetype.h&quot;<span class="kom">// Zahròme ná¹ súbor freetype.h</span></p>
<p class="src"></p>
<p class="src0">namespace freetype</p>
<p class="src0">{</p>

<p>Na vykreslenie ka¾dého znaku pou¾ijeme textúru. OpenGL textúry potrebujú ma» rozmery ktoré sú mocninami 2, tak¾e potrebujeme aby bitmapy vytvorené FreeTypom mali povolenú veµkos». Na to potrebujeme túto funkciu:</p>

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

<p>Ïal¹ia funkcia, ktorú budeme potrebova» je make_dlist(). Je to naozaj srdce tohoto kódu. Parameter je FT_Face, je to objekt, ktorý FreeType pou¾íva na uchovanie informácii o fonte a vytvára display list podµa toho, aký znak je po¹leme.</p>

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

<p>Teraz, keï u¾ máme bitmapu vytvorenú pomocou FreeType, potrebujeme ju vyplni» prázdnymi pixelmi aby umo¾nili jej pou¾itie v OpenGL. Je dôle¾ité pamäta» si, ¾e kým OpenGL pou¾íva pojem bitmapy ako binárne obrázky, vo FreeType bitmapy uchovávajú 8 bitov informácií na pixel (256 mo¾ností), tak¾e bitmapy FreeTypu mô¾u obsahova» aj ¹edé farby aby vytvorili anti-aliasovaný text.</p>

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

<p>Keï sme hotoví, mô¾eme sa pusti» do vytvárania OpenGL textúry. Zahàòame alfa kanál, tak¾e èierne èasti bitmapy budú priehµadné a okraje textu budú plynulo priesvitné (preto by mali vyzera» správne na akomkoµvek podklade).</p>

<p class="src2"><span class="kom">// Teraz len nastavime parametre textury</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, tex_base[ch]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Tu vytvarame samotnu texturu, vsimnite si, ze pouzivame GL_LUMINANCE_ALPHA aby sme vyjadrili, ze pouzivame data 2 kanalov</span></p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, GL_RGBA, width, height, 0, GL_LUMINANCE_ALPHA, GL_UNSIGNED_BYTE, expanded_data);</p>
<p class="src"></p>
<p class="src2">delete [] expanded_data;<span class="kom">// Ked mame texturu vytvorenu, nepotrebujeme uz Expanded Data</span></p>

<p>Na vykreslenie ná¹ho textu pou¾ívame ¹tvoruholníky s textúrami. To znamená, ¾e bude jednoduché otáèa» a zväè¹ova»/zmen¹ova» text a text bude dedi» farbu z aktuálnej OpenGL farby (èo by tak nebolo, ak by sme pou¾ívali pixmapy). </p>

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

<p>Ïal¹ia funkcia, ktorú ideme vytvori» bude pou¾íva» make_dlist na vytvorenie mno¾iny display listov kore¹pondujúcich k danému súboru s fontom a vý¹ke v pixeloch.</p>

<p>FreeType pou¾íva truetype fonty, tak¾e budete potrebova» nejaké súbory s truetype písmami ako parametre tejto funkcie (súbory .ttf).
Truetypové písma sú veµmi be¾né a je kopec stránok, kde si ich mô¾ete stiahnu». Aj Windows pou¾íva ttf ako väè¹inu písiem, tak¾e mô¾ete pou¾i» aj tieto z adresára windows/fonts.</p>

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

<p>Teraz potrebujeme funkciu na vyèistenie v¹etkých display listov a textúr spojených s fontom.</p>

<p class="src1">void font_data::clean()</p>
<p class="src1">{</p>
<p class="src2">glDeleteLists(list_base, 128);</p>
<p class="src2">glDeleteTextures(128, textures);</p>
<p class="src2">delete [] textures;</p>
<p class="src1">}</p>

<p>Tu sú dve malé funkcie, ktoré definujme v oèakávaní na¹ej print funkcie. Funkcia print bude chcie» myslie» v pixelových súradniciach (tie¾ nazývané window coordinates), tak¾e budeme potrebova» prepnú» do projekènej matice, ktorá spôsobí ¾e v¹etko bude merané v oknových súradniciach (od µavého horného rohu).</p>

<p>Pou¾ijeme dve veµmi u¾itoèné funkcie, glGet() na získanie rozmerov okna a glPushAttrib / glPopAttrib na uistenie sa, ¾e sme nechali maticu v pôvodnom stave, ako sme ju na¹li. Ak tieto funkcie nepoznáte, je pravdepodobne vhodné vyhµada» si ich vo va¹ej obµúbenom OpenGL manuály.</p>

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

<p>Na¹a funkcia print vyzerá veµmi podobne ako tá z lekcie 13, ale je tu zopár dôle¾itých výnimiek. OpenGL umo¾òujúce príznaky (flags), ktoré nastavujeme sú iné, èo sa odrazí vo fakte, ¾e pou¾ívame 2 kanálové textúry namiesto bitmáp. Tie¾ urobíme trochu extra spracovania na riadku textu aby sme správne zvládli nové riadky. Pou¾ijeme OpenGL matíce a attribute háldy (stacks) aby sme sa uistili, ¾e funkcia napraví v¹etky zmeny, ktoré robí do interného stavu OpenGL (toto zabráni tomu, aby ktokoµvek pou¾il funkciu a zrazu zistil, ¾e sa ModelView matica záhadne zmenila).</p>

<p class="src1"><span class="kom">// Skoro ako NeHe glPrint funckcia, ale modifikovana, aby pracovala s freetype fontmi</span></p>
<p class="src1">void print(const font_data &amp;ft_font, float x, float y, const char *fmt, ...)</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Chceme system suradnic, kde je je vzdialenost merana v pixeloch</span></p>
<p class="src2">pushScreenCoordinateMatrix();</p>
<p class="src2"></p>
<p class="src2">GLuint font = ft_font.list_base;</p>
<p class="src2">float h = ft_font.h / 0.63f;<span class="kom">// Trochu zvacsime vý¹ku, aby bola medzera medzi riadkami</span></p>
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
<p class="src2"><span class="kom">// Toto by sa dalo spravit ak milsia pomocou kni¾nice regulárnych výrazov,</span></p>
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

<p>Preto¾e pou¾ívame otexturované ¹tvoruholníky, v¹etky transformácie, ktoré urobíme do ModelView matice pred volaním glCallLists sa prejavia na texte samotnom. To znamená, ¾e je mo¾nos» rotova», alebo meni» veµkos» textu (ïal¹ia výhoda WGL bitmáp). Najprirodzenej¹í spôsob ako to vyu¾i» by bolo necha» aktuálnu maticu samotnú, teda urobi» v¹etky transformácie pred pou¾itím print funkcie. Ale kvôli spôsobu akým pou¾ívame maticu ModelView na nastavenie pozície textu, toto nepôjde. Na¹a ïal¹ia mo¾nos» je ulo¾i» si kópiu matice a aplikova» je medzi glTranslate a glCallLists. Toto je dos» jednoduché, ale preto¾e potrebujeme vykresµova» text pou¾ívajúc ¹peciálnu projekènú maticu, efekty modelview matice by boli trochu iné ako by sa dalo predpoklada» - v¹etko by bolo interpretované vo veµkosti pixelov. Cez toto by sme sa mohli dosta» resetovaním projekènej matice vnútri funkcie print. To je asi dobrý nápad - ale ak sa o to pokúsite, urèite scalujte fonty na po¾adovanú veµkos» (sna¾ia sa by» 32x32, ale vy asi potrebujete nieèo okolo 0.01x0.01).</p>

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

<p>Kni¾nica je teraz hotová. Otvorme teraz lesson13.cpp a urobíme tam zopár malých zmien aby sme mohli pou¾i» funkcie, ktoré sme práve napísali. Pod ostatnými hlavièkami, pridajte freetype.h.</p>

<p class="src0">#include &quot;freetype.h&quot;<span class="kom">// Hlavicka pre nasu kniznicu</span></p>

<p>A kým sme tu, vytvorme globálny objekt font_data.</p>

<p class="src0">freetype::font_data our_font;<span class="kom">// Toto uchováva v¹etky informácie pre font, ktorý ideme vytvori»</span></p>

<p>Teraz potrebuejme vedie» ako vytvori» a znièi» prostriedky ná¹ho fontu. Tak¾e pridajte nasledujúci riadok na koniec InitGL.</p>

<p class="src0"><span class="kom">// InitGL()</span></p>
<p class="src1">our_font.init(&quot;test.TTF&quot;, 16);    <span class="kom">//Build the freetype font</span></p>

<p>A pridajte tento riadok na zaèiatok KillGLWindow aby sa odstránil font, keï program skonèil.</p>

<p class="src0"><span class="kom">// KillGLWindow()</span></p>
<p class="src1">our_font.clean();</p>

<p>Teraz musíme zmeni» funkcie DrawGLScene, aby pou¾ívala na¹u print funkciu. Toto by mohlo by» dos» ale s»a¾íme si to kreatívnej¹ím postupom aby sme mohli font rotova» a meni» jeho veµkos».</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Tu vsetko vykreslujeme</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vycistime zasobniky</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Resetnime aktualnu maticu ModelView</span></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -1.0f);<span class="kom">// Pohnime sa 1 jednotku do obrazovky</span></p>
<p class="src"></p>
<p class="src1">glColor3ub(0, 0, 0xff);<span class="kom">// Modrý text</span></p>
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

<p>Posledná vec, ktorú treba urobi», je prida» kód na handlovanie výnimiek. Choïte do WinMain a vyhµadajte odstavec try { .. } na zaèiatku funkcie.</p>

<p class="src0"><span class="kom">// WinMain()</span></p>
<p class="src1">MSG msg;<span class="kom">// Windows Message Structure</span></p>
<p class="src1">BOOL done = FALSE;<span class="kom">// Bool Variable To Exit Loop</span></p>
<p class="src"></p>
<p class="src1">try<span class="kom">// Use exception handling</span></p>
<p class="src1">{</p>

<p>Potom modifikujte koniec funkcie aby sme mali prikaz catch { }.</p>

<p class="src2">KillGLWindow();<span class="kom">// Zru¹ okno</span></p>
<p class="src"></p>
<p class="src1">}</p>
<p class="src1">catch (std::exception &amp;e)<span class="kom">// Chyt vsetky dane vyjimky</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, e.what(), &quot;CAUGHT AN EXCEPTION&quot;, MB_OK | MB_ICONINFORMATION);</p>
<p class="src1">}</p>

<p>Tak a teraz ak natrafíme na výnimku, dostaneme message box hovoriaci èo sa stalo. Pozor, tento kód mô¾e spomali» vá¹ program, tak¾e keï kompilujete koneènú verziu vá¹ho programu, mo¾no sa vám bude hodi» vypnú» exception handling v Project->Settings->C/C++, "C++ Language".</p>

<p>A to je ono! Skompilujte program a mali by ste vidie» pekný FreeType renderovaný text pohybujúci sa okolo originálneho bitmapového textu z lekcie 13.</p>

<h3>Zopár odkazov na záver:</h3>
<ul>
<li><?OdkazBlank('http://www.opengl.org/developers/faqs/technical/fonts.htm');?></li>
<li><?OdkazBlank('http://oglft.sourceforge.net/');?></li>
<li><?OdkazBlank('http://homepages.paradise.net.nz/henryj/code/#FTGL');?></li>
<li><?OdkazBlank('http://plib.sourceforge.net/fnt');?></li>
</ul>

<p class="autor">napsal: Sven Olsen <?VypisEmail('sven@sccs.swarthmore.edu');?><br />
do sloven¹tiny pøelo¾il: Pavel Hradský - PcMaster<?VypisEmail('pcmaster@stonline.sk');?><br />
do èe¹tiny pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<p>Pozn.: Prosím ospravedlote v¹etky chyby v tomto èlánku, je to môj prvý preklad. Dúfam, ¾e vám pomohol, ak máte nejaké problémy s porozumením, alebo ste natrafili na záva¾nú chybu, kontaktujte prosím mòa, alebo autora tohto webu.</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson43.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson43.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson43.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:agraves@bu.edu">Aaron Graves</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson43.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(43);?>
<?FceNeHeOkolniLekce(43);?>

<?
include 'p_end.php';
?>
