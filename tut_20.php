<?
$g_title = 'CZ NeHe OpenGL - Lekce 20 - Maskování';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(20);?>

<h1>Lekce 20 - Maskování</h1>

<p class="nadpis_clanku">Èerné okraje obrázkù jsme dosud oøezávali blendingem. Aèkoli je tato metoda efektivní, ne v¾dy transparentní objekty vypadají dobøe. Modelová situace: vytváøíme hru a potøebujeme celistvý text nebo zakøivený ovládací panel, ale pøi blendingu scéna prosvítá. Nejlep¹ím øe¹ením je maskování obrázkù.</p>

<p>Bitmapový formát obrázku je podporován ka¾dým poèítaèem a ka¾dým operaèním systémem. Nejen, ¾e se s nimi snadno pracuje, ale velmi snadno se nahrávají a konvertují na textury. K oøezání èerných okrajù textu a obrázkù jsme s výhodou pou¾ívali blending, ale ne v¾dy výsledek vypadal dobøe. Pøi spritové animaci ve høe nechcete, aby postavou prosvítalo pozadí. Podobnì i text by mìl být pevný a snadno èitelný. V takových situacích se s výhodou vyu¾ívá maskování. Má dvì fáze. V první do scény umístíme èernobílou texturu, ve druhé na stejné místo vykreslíme hlavní texturu. Pou¾itý typ blendingu zajistí, ¾e tam, kde se v masce (první obrázek) vyskytovala bílá barva zùstane pùvodní scéna. Textura se neprùhlednì vykreslí na èernou barvu.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavièkový soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavièkový soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Privátní GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trvalý Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na¹eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>Masking ukládá pøíznak zapnutého/vypnutého maskování a podle scene se rozhodujeme, zda vykreslujeme první nebo druhou verzi scény. Loop je øídící promìnná cyklù, roll pou¾ijeme pro rolování textur a rotaci objektu pøi zapnuté druhé scénì.</p>

<p class="src0">bool masking=TRUE;<span class="kom">// Maskování on/off</span></p>
<p class="src0">bool mp;<span class="kom">// Stisknuto M?</span></p>
<p class="src0">bool sp;<span class="kom">// Stisknut mezerník?</span></p>
<p class="src0">bool scene;<span class="kom">// Která scéna se má kreslit</span></p>
<p class="src"></p>
<p class="src0">GLuint texture[5];<span class="kom">// Ukládá 5 textur</span></p>
<p class="src0">GLuint loop;<span class="kom">// Øídící promìnná cyklù</span></p>
<p class="src"></p>
<p class="src0">GLfloat roll;<span class="kom">// Rolování textur</span></p>

<p>Generování textur je ve svém principu úplnì stejné jako ve v¹ech minulých lekcích, ale velmi pøehlednì demonstruje nahrávání více textur najednou. Témìø v¾dy jsme pou¾ívali pouze jednu. Deklarujeme pole ukazatelù na pìt bitmap, vynulujeme je a nahrajeme do nich obrázky, které vzápìtí zmìníme na textury.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Nahraje bitmapu a konvertuje na texturu</span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;</p>
<p class="src1">AUX_RGBImageRec *TextureImage[5];<span class="kom">// Alokuje místo pro bitmapy</span></p>
<p class="src1">memset(TextureImage,0,sizeof(void *)*5);</p>
<p class="src"></p>
<p class="src1">if ((TextureImage[0]=LoadBMP(&quot;Data/logo.bmp&quot;)) &amp;&amp;<span class="kom">// Logo</span></p>
<p class="src1">(TextureImage[1]=LoadBMP(&quot;Data/mask1.bmp&quot;)) &amp;&amp;<span class="kom">// První maska</span></p>
<p class="src1">(TextureImage[2]=LoadBMP(&quot;Data/image1.bmp&quot;)) &amp;&amp;<span class="kom">// První obrázek</span></p>
<p class="src1">(TextureImage[3]=LoadBMP(&quot;Data/mask2.bmp&quot;)) &amp;&amp;<span class="kom">// Druhá maska</span></p>
<p class="src1">(TextureImage[4]=LoadBMP(&quot;Data/image2.bmp&quot;)))<span class="kom">// Druhý obrázek</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;</p>
<p class="src2">glGenTextures(5, &amp;texture[0]);</p>
<p class="src"></p>
<p class="src2">for (loop=0; loop&lt;5; loop++)<span class="kom">// Generuje jednotlivé textury</span></p>
<p class="src2">{</p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop]);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src3">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[loop]-&gt;sizeX, TextureImage[loop]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[loop]-&gt;data);</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">for (loop=0; loop&lt;5; loop++)</p>
<p class="src1">{</p>
<p class="src2">if (TextureImage[loop])</p>
<p class="src2">{</p>
<p class="src3">if (TextureImage[loop]-&gt;data)</p>
<p class="src3">{</p>
<p class="src4">free(TextureImage[loop]-&gt;data);</p>
<p class="src3">}</p>
<p class="src3">free(TextureImage[loop]);</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">return Status;</p>
<p class="src0">}</p>

<p>Z inicializace zùstala doslova kostra.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echno nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje textury</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Povolí mazání Depth Bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkové testování</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapování textur</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Pøi vykreslování zaèneme jako obyèejnì mazáním bufferù, resetem matice a translací do obrazovky.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-2.0f);<span class="kom">// Pøesun do obrazovky</span></p>

<p>Zvolíme texturu loga a namapujeme ji na obdélník. Koordináty vypadají nìjak divnì. Namísto obvyklých hodnot 0 a¾ 1 tentokrát zadáme èísla 0 a 3. Pøedáním trojky oznámíme, ¾e chceme namapovat texturu na polygon tøikrát. Pro vysvìtlení mì napadá vlastnost vedle sebe pøi umístìní malého obrázku na plochu OS. Trojku zadáváme do ¹íøky i do vý¹ky, tudí¾ se na polygon rovnomìrnì namapuje celkem devìt stejných obrázkù. Ke koordinátùm také pøièítáme (defakto odeèítáme) promìnnou roll, kterou na konci funkce inkrementujeme. Vzniká dojem, ¾e vykreslovaná hladina scény roluje, ale v programu se vlastnì mìní pouze texturové koordináty. Rolování mù¾e být pou¾ito pro rùzné efekty. Napøíklad pohybující se mraky nebo text létající po objektu.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_20_roll_2.jpg" width="96" height="96" alt="Po rolování" />
<img src="images/nehe_tut/tut_20_roll_1.jpg" width="96" height="96" alt="Pøed rolováním" />
</div>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Výbìr textury loga</span></p>

<div class="okolo_img"><img src="images/nehe_tut/tut_20_logo.jpg" width="128" height="128" alt="Logo" /></div>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslení obdélníkù</span></p>
<p class="src2">glTexCoord2f(0.0f, -roll+0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src2">glTexCoord2f(3.0f, -roll+0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src2">glTexCoord2f(3.0f, -roll+3.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, -roll+3.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení</span></p>

<p>Zapneme blending. Aby efekt pracoval musíme vypnout testování hloubky. Kdyby se nevypnulo nejvìt¹í pravdìpodobností by nic nebylo vidìt.</p>

<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne testování hloubky</span></p>

<p>Podle hodnoty promìnné se rozhodneme, zda budeme obrázek maskovat nebo pou¾ijeme mnohokrát vyzkou¹ený blending. Maska je èernobílá kopie textury, kterou chceme vykreslit. Bílé oblasti masky budou prùhledné, èerné nebudou. Pod bílými sekcemi zùstane scéna nezmìnìna.</p>

<p class="src1">if (masking)<span class="kom">// Je zapnuté maskování?</span></p>
<p class="src1">{</p>
<p class="src2">glBlendFunc(GL_DST_COLOR,GL_ZERO);<span class="kom">// Blending barvy obrazu pomocí nuly (èerná)</span></p>
<p class="src1">}</p>

<p>Pokud bude scene true vykreslíme druhou, jinak první scénu.</p>

<p class="src1">if (scene)<span class="kom">// Vykreslujeme druhou scénu?</span></p>
<p class="src1">{</p>

<p>Nechceme objekty pøíli¹ velké, tak¾e se pøesuneme hloubìji do obrazovky. Provedeme rotaci na ose z o 0° a¾ 360° podle promìnné roll.</p>

<p class="src2">glTranslatef(0.0f,0.0f,-1.0f);<span class="kom">// Pøesun o jednotku do obrazovky</span></p>
<p class="src2">glRotatef(roll*360,0.0f,0.0f,1.0f);<span class="kom">// Rotace na ose z</span></p>

<p>Pokud je zapnuté maskování, vykreslíme nejdøíve masku a potom objekt. Pøi vypnutém pouze objekt.</p>

<p class="src2">if (masking)<span class="kom">// Je zapnuté maskování?</span></p>
<p class="src2">{</p>

<p>Nastavení blendingu pro masku jsme provedli døíve. Zvolíme texturu masky a namapujeme ji na obdélník. Po vykreslení se na scénì objeví èerná místa odpovídající masce.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_20_mask2.jpg" width="128" height="128" alt="Druhá maska" />
<img src="images/nehe_tut/tut_20_image2.jpg" width="128" height="128" alt="Druhý obrázek" />
</div>

<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[3]);<span class="kom">// Výbìr textury druhé masky</span></p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src3">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src2">}</p>

<p>Znovu zmìníme mód blendingu. Oznámíme tím, ¾e chceme vykreslit v¹echny èásti barevné textury, které NEJSOU èerné. Proto¾e je obrázek barevnou kopií masky, tak se vykreslí jen místa nad èernými èástmi masky. Proto¾e je maska èerná, nic ze scény nebude prosvítat skrz textury. Vznikne dojem pevnì vypadajícího obrázku. Zvolíme barevnou texturu. Poté ji vykreslíme se stejnými souøadnicemi bodù v prostoru a stejnými texturovými koordináty jako masku. Kdybychom masku nevykreslily, obrázek by se zkopíroval do scény, ale díky blendingu by byl prùhledný. Objekty za ním by prosvítaly.</p>

<p class="src2">glBlendFunc(GL_ONE, GL_ONE);<span class="kom">// Pro druhý barevný obrázek</span></p>
<p class="src"></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[4]);<span class="kom">// Zvolí druhý obrázek</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src2">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src1">}</p>

<p>Pøi hodnotì FALSE ulo¾ené ve scene se vykreslí první scéna. Opìt vìtvíme program podle maskování. Pøi zapnutém vykreslíme masku pro scénu jedna. Textura roluje zprava doleva (roll pøièítáme k horizontálním koordinátùm). Chceme, aby textura zaplnila celou scénu, tak¾e neprovádíme translaci do obrazovky.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_20_mask1.jpg" width="128" height="128" alt="První maska" />
<img src="images/nehe_tut/tut_20_image1.jpg" width="128" height="128" alt="První obrázek" />
</div>

<p class="src1">else<span class="kom">// Vykreslení první scény</span></p>
<p class="src1">{</p>
<p class="src2">if (masking)<span class="kom">// Je zapnuté maskování?</span></p>
<p class="src2">{</p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[1]);<span class="kom">// Výbìr textury první masky</span></p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src4">glTexCoord2f(roll+0.0f, 0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(roll+4.0f, 0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(roll+4.0f, 4.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(roll+0.0f, 4.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src3">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src2">}</p>

<p>Blending nastavíme stejnì jako minule. Vybereme texturu scény jedna a vykreslíme ji na stejné místo jako masku.</p>

<p class="src2">glBlendFunc(GL_ONE, GL_ONE);<span class="kom">// Pro první barevný obrázek</span></p>
<p class="src"></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[2]);<span class="kom">// Zvolí první obrázek</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src3">glTexCoord2f(roll+0.0f, 0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(roll+4.0f, 0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(roll+4.0f, 4.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(roll+0.0f, 4.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src2">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src1">}</p>

<p>Zapneme testování hloubky a vypneme blending. V malém programu je to vìc celkem zbyteèná, ale u rozsáhlej¹ích projektù nìkdy nevíte, co zrovna máte zapnuté nebo vypnuté. Tyto chyby se obtí¾nì hledají a kradou èas. Po urèité dobì ztrácíte orientaci, kód se stává slo¾itìj¹ím - preventivní opatøení.</p>

<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>
<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vypne blending</span></p>

<p>Aby se scéna dynamicky pohybovala musíme inkrementovat roll.</p>

<p class="src1">roll+=0.002f;<span class="kom">// Inkrementace roll</span></p>
<p class="src"></p>
<p class="src1">if (roll&gt;1.0f)<span class="kom">// Je vìt¹í ne¾ jedna?</span></p>
<p class="src1">{</p>
<p class="src2">roll-=1.0f;<span class="kom">// Odeète jedna</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>O¹etøíme vstup z klávesnice. Po stisku mezerníku zmìníme vykreslovanou scénu.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if (keys[' '] &amp;&amp; !sp)<span class="kom">// Mezerník - zmìna scény</span></p>
<p class="src4">{</p>
<p class="src5">sp=TRUE;</p>
<p class="src5">scene=!scene;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys[' '])<span class="kom">// Uvolnìní mezerníku</span></p>
<p class="src4">{</p>
<p class="src5">sp=FALSE;</p>
<p class="src4">}</p>

<p>Stiskem klávesy M zapneme, popø. vypneme maskování.</p>

<p class="src4">if (keys['M'] &amp;&amp; !mp)<span class="kom">// Klávesa M - zapne/vypne maskování</span></p>
<p class="src4">{</p>
<p class="src5">mp=TRUE;</p>
<p class="src5">masking=!masking;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys['M'])<span class="kom">// Uvolnìní klávesy M</span></p>
<p class="src4">{</p>
<p class="src5">mp=FALSE;</p>
<p class="src4">}</p>

<p>Vytvoøení masky není pøíli¹ tì¾ké. Pokud máte originální obrázek ji¾ nakreslený, otevøete ho v nìjakém grafickém editoru a transformujte ho do ¹edé palety barev. Po této operaci zvy¹te kontrast, tak¾e se ¹edé pixely ztmaví na èerné. Zkuste také sní¾it jas ap. Je dùle¾ité, aby bílá byla opravdu bílá a èerná èistì èerná. Máte-li pochyby pøeveïte obrázek do èernobílého re¾imu (2 barvy). Pokud by v masce zùstaly ¹edé pixely byly by prùhledné. Je také dùle¾ité, aby barevný obrázek mìl èerné pozadí a masku bílou. Otestujte si barvy masky kapátkem (vìt¹inou bývají chyby na rozhraní). Bílá je v RGB 255 255 255 (FF FF FF), èerná 0 0 0.</p>

<p>Lze zjistit barvu pixelù pøi nahrávání bitmapy. Chcete-li pixel prùhledný mù¾ete mu pøiøadit alfu rovnou nule. V¹em ostatním barvám 255. Tato metoda také pracuje spolehlivì, ale vy¾aduje extra kód. Tímto chci poukázat, ¾e k výsledku existuje více cest - v¹echny mohou být správné.</p>

<p>Nauèili jsme se, jak vykreslit èást textury bez pou¾ití alfa kanálu. Klasický blending, který známe, nevypadal nejlépe a textury s alfa kanálem potøebují obrázky, které alfa kanál podporují. Bitmapy jsou vhodné pøedev¹ím díky snadné práci, ale mají ji¾ zmínìné omezení. Tento program ukázal, jak obejít nedostatky bitmapových obrázkù a vykreslování jedné textury vícekrát na jeden obdélník. V¹e jsme roz¹íøili rolováním textur po scénì.</p>

<p>Dìkuji Robu Santovi za ukázkový kód, ve kterém mi poprvé pøedstavil trik mapování dvou textur. Nicménì ani tato cesta není úplnì dokonalá. Aby efekt pracoval, potøebujete dva prùchody - dvakrát vykreslujete jeden objekt. Z toho plyne, ¾e vykreslování tímto zpùsobem je dvakrát pomalej¹í. Nicménì... co se dá dìlat?</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson20.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson20_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson20.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson20.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson20.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson20.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson20.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson20.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson20.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:alex_r@vortexentertainment.com">Alexandre Ribeiro de S?</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson20.tar.gz">Irix / GLUT</a> kód této lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson20.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson20.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson20.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:bryantdesign11@mindspring.com">Daniel Davis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson20.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:miqster@gmx.net">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson20.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson20.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson20.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson20.zip">MASM</a> kód této lekce. ( <a href="mailto:chris.j84@free.fr">Christophe</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson20.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson20.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:fredo@studenten.net">Edo</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson20.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(20);?>
<?FceNeHeOkolniLekce(20);?>

<?
include 'p_end.php';
?>
