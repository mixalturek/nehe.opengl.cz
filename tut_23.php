<?
$g_title = 'CZ NeHe OpenGL - Lekce 23 - Mapování textur na kulové quadratiky';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(23);?>

<h1>Lekce 23 - Mapování textur na kulové kvadriky</h1>

<p class="nadpis_clanku">Tento tutoriál je napsán na bázi lekce 18. V lekci 15 (Mapování textur na fonty) jsem psal o automatickém mapování textur. Vysvìtlil jsem jak mù¾eme poprosit OpenGL o automatické generování texturových koordinátù, ale proto¾e lekce 15 byla celkem skromná, rozhodl jsem se pøidat mnohem více detailù o této technice.</p>

<p>Mapování kulového prostøedí (Sphere Environment Mapping) je rychlá cesta pro pøidání zrcadlení na kovové nebo zrcadlové objekty. Tøeba¾e není tak pøesná jako skuteèné zrcadlení nebo jako krychlová mapa prostøedí (cube environment map) je o hodnì rychlej¹í. Jako základ pou¾ijeme kód z lekce 18, ale nepou¾ijeme ¾ádnou z pøedchozích textur. Pou¾ijeme jednu kulovou mapu (sphere map) a jeden obrázek pro pozadí.</p>

<p>Ne¾ zaèneme... Red Book definuje kulovou mapu jako obraz scény na kovové kouli z nekoneèné vzdálenosti a nekoneèného ohniskového bodu. To je idální a ve skuteèném ¾ivotì nemo¾né. Nejlep¹í zpùsob, bez pou¾ití èoèek rybího oka (fish eye lens), který jsem na¹el je pou¾ití programu Adobe Photoshop:</p>

<p>Nejdøíve budeme potøebovat obrázek prostøedí, které chceme namapovat na kouli. Otevøeme obrázek v Adobe Photoshopu a vybereme celý obrázek. Zkopírujeme obrázek a vytvoøíme nový obrázek PSD (Photoshop formát). Nový obrázek by mìl být stejné velikosti jako obrázek který jsme právì zkopírovali. Vlo¾íme kopii pùvodního obrázku do nového. Dùvodem proè dìláme kopii je, ¾e tak mù¾e Photoshop aplikovat své filtry. Namísto kopírování obrázku mù¾eme vybrat mód z lokálního menu (na kliknutí pravého tlaèítka my¹i) a zvolit mód RGB. Poté budou dostupné v¹echny filtry.</p>

<p>Dále potøebujeme zmìnit velikost obrázku tak ¾e bude mocninou dvou. Pamatujte, ¾e abyste mohli pou¾ít obrázek jako texturu musí mít rozmìry 128x128, 256x256 atd. V menu image tedy vybereme image size, od¹krtneme constraint proportions (zachovat pomìr stran) a zmìníme velikost obrázku na platnou velikost textury. Pokud má vá¹ obrázek velikost 100x90 je lep¹í vytvoøit texturu o velikosti 128x128 ne¾ 64x64. Vytváøením men¹ího obrázku ztratíte hodnì detailù.</p>

<p>Jako poslední vybereme menu filter (filtry) a v nìm distort (zdeformovat) a pou¾ijte spherize modifier (modifikátor koule). Mù¾eme vidìt, ¾e støed obrázku je nafouklý jako balón. V normálním kulovém mapování by byla vnìj¹í plocha èerná, ale to nemá skuteèný vliv. Ulo¾íme obrázek jako BMP a jsme pøipraveni k programování.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_23_bg.jpg" width="256" height="256" alt="Textura pozadí" />
<img src="images/nehe_tut/tut_23_reflect.jpg" width="256" height="256" alt="Pozadí zdeformované koulí" />
</div>

<p>V této lekci nebudeme pøidávat ¾ádné nové globální promìnné, ale pouze upravíme index pole pro ulo¾ení ¹esti textur.</p>

<p class="src0">GLuint texture[6];<span class="kom">// ©est textur</span></p>

<p>Dále modifikujeme funkci LoadGLTextures(), abychom mohli nahrát 2 textury a aplikovat 3 filtry. Jednodu¹e dvakrát projdeme cyklem a v ka¾dém prùchodu vytvoøíme 3 textury poka¾dé s jiným filtrovacím módem. Skoro celý tento kód je nový nebo modifikovaný.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Loading bitmap a konverze na textury</span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>
<p class="src"></p>
<p class="src1">AUX_RGBImageRec *TextureImage[2];<span class="kom">// Ukládá dvì bitmapy</span></p>
<p class="src"></p>
<p class="src1">memset(TextureImage,0,sizeof(void *)*2);<span class="kom">// Vynuluje pamì»</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nahraje bitmapy a kontroluje vzniklé chyby</span></p>
<p class="src1">if ((TextureImage[0]=LoadBMP(&quot;Data/BG.bmp&quot;)) &amp;&amp;<span class="kom">// Textura pozadí</span></p>
<p class="src1">(TextureImage[1]=LoadBMP(&quot;Data/Reflect.bmp&quot;)))<span class="kom">// Textura kulové mapy (sphere map)</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;<span class="kom">// V¹e je bez problémù</span></p>
<p class="src"></p>
<p class="src2">glGenTextures(6, &amp;texture[0]);<span class="kom">// Generuje ¹est textur</span></p>
<p class="src"></p>
<p class="src2">for (int loop=0; loop&lt;=1; loop++)</p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Vytvoøí nelineárnì filtrovanou texturu</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop]);<span class="kom">// Textury 0 a 1</span></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_NEAREST);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_NEAREST);</p>
<p class="src"></p>
<p class="src3">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[loop]-&gt;sizeX, TextureImage[loop]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[loop]-&gt;data);</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Vytvoøí lineárnì filtrovanou texturu</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop+2]);<span class="kom">// Textury 2 a 3</span></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src"></p>
<p class="src3">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[loop]-&gt;sizeX, TextureImage[loop]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[loop]-&gt;data);</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Vytvoøí mipmapovanou texturu</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop+4]);<span class="kom">// Textury 2 a 3</span></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR_MIPMAP_NEAREST);</p>
<p class="src"></p>
<p class="src3">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, TextureImage[loop]-&gt;sizeX, TextureImage[loop]-&gt;sizeY, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[loop]-&gt;data);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">for (loop=0; loop&lt;=1; loop++)</p>
<p class="src2">{</p>
<p class="src3">if (TextureImage[loop])<span class="kom">// Pokud obrázek existuje</span></p>
<p class="src3">{</p>
<p class="src4">if (TextureImage[loop]-&gt;data)<span class="kom">// Pokud existují data obrázku</span></p>
<p class="src4">{</p>
<p class="src5">free(TextureImage[loop]-&gt;data);<span class="kom">// Uvolní pamì» obrázku</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">free(TextureImage[loop]);<span class="kom">// Uvolní strukturu obrázku</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return Status;<span class="kom">// Oznámí pøípadné chyby</span></p>
<p class="src0">}</p>

<p>Trochu upravíme kód kreslení krychle. Namísto pou¾ití hodnot 1.0 a -1.0 pro normály, pou¾ijeme 0.5 a -0.5. Zmìnou hodnot normál mù¾eme mìnit velikost odrazové mapy dovnitø a ven. Pokud je hodnota normály velká, odra¾ený obrázek je vìt¹í a mohl by se zobrazovat ètvereèkovanì. Sní¾ením hodnoty normál na 0.5 a -0.5 je obrázek trochu zmen¹en, tak¾e obrázek odrá¾ený na krychli nevypadá tak ètvereèkovanì. Nastavením pøíli¹ malých hodnot získáme ne¾ádoucí výsledky.</p>

<p class="src0">GLvoid glDrawCube()</p>
<p class="src0">{</p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// Pøední stìna</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f, 0.5f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Zadní stìna</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f,-0.5f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Vrchní stìna</span></p>
<p class="src2">glNormal3f( 0.0f, 0.5f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Spodní stìna</span></p>
<p class="src2">glNormal3f( 0.0f,-0.5f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Pravá stìna</span></p>
<p class="src2">glNormal3f( 0.5f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Levá stìna</span></p>
<p class="src2">glNormal3f(-0.5f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>Do InitGL pøidáme volání dvou nových funkcí. Tyto dvì volání nastaví mód generování textur na S a T pro kulové mapování (sphere mapping). Texturové koordináty S, T, R a Q souvisí s koordináty objektu x, y, z a w. Pokud pou¾íváte jednorozmìrnou (1D) texturu, pou¾ijete souøadnici S. Pokud pou¾ijete dvourozmìrnou texturu pou¾ijete souøadnice S a T.</p>

<p>Tak¾e následující kód øíká OpenGL jak automaticky generovat S a T koordináty na kulovì mapovaném (sphere-mapping) vzorci. Koordináty R a Q jsou obvykle ignorovány. Koordinát Q mù¾e být pou¾it pro pokroèilé techniky mapování textur a koordinát R mù¾e být u¾iteèný a¾ bude do OpenGL pøidáno mapování 3D textur. Ale pro teï budeme koordináty R a Q ignorovat. Koordinát S bì¾í horizontálnì pøes èelo na¹eho polygonu a T zase vertikálnì.</p>

<p class="src0"><span class="kom">// Funkce InitGL()</span></p>
<p class="src1"><span class="kom">// Nastavení módu generování textur pro S koordináty pro kulové mapování</span></p>
<p class="src1">glTexGeni(GL_S, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);</p>
<p class="src1"><span class="kom">// Nastavení módu generování textur pro T koordináty pro kulové mapování</span></p>
<p class="src1">glTexGeni(GL_T, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);</p>

<p>Máme témìø hotovo. V¹e co musíme je¹tì udìlat je nastavit vykreslování. Odstranil jsem nìkolik typù quadratikù, proto¾e nepracovali dobøe s mapováním prostøedí (environment mapping). Zaprvé potøebujeme  povolit generování textur. Potom vybereme odrazovou texturu (kulovou mapu - sphere map) a vykreslíme ná¹ objekt. Pøed vykreslením pozadí vypneme kulové mapování. V¹imnìte si, ¾e pøíkaz glBindTexture() mù¾e vypadat docela slo¾itì. V¹e co dìláme je výbìr filtru pro kreslení na¹í kulové mapy nebo obrázku pozadí.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// V¹echno kreslení</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,z);</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_GEN_S);<span class="kom">// Povolí generování texturových koordinátù S</span></p>
<p class="src1">glEnable(GL_TEXTURE_GEN_T);<span class="kom">// Povolí generování texturových koordinátù T</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter+(filter+1)]); <span class="kom">// Zvolí texturu kulové mapy</span></p>
<p class="src1">glPushMatrix();</p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);</p>
<p class="src"></p>
<p class="src1">switch(object)<span class="kom">// Vybere, co se bude kreslit</span></p>
<p class="src1">{</p>
<p class="src2">case 0:</p>
<p class="src3">glDrawCube();<span class="kom">// Krychle</span></p>
<p class="src3">break;</p>
<p class="src"></p>
<p class="src2">case 1:</p>
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrování</span></p>
<p class="src3">gluCylinder(quadratic,1.0f,1.0f,3.0f,32,32);<span class="kom">// Válec</span></p>
<p class="src3">break;</p>
<p class="src"></p>
<p class="src2">case 2:</p>
<p class="src3">gluSphere(quadratic,1.3f,32,32);<span class="kom">// Koule</span></p>
<p class="src3">break;</p>
<p class="src"></p>
<p class="src2">case 3:</p>
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrování</span></p>
<p class="src3">gluCylinder(quadratic,1.0f,0.0f,3.0f,32,32);<span class="kom">// Ku¾el</span></p>
<p class="src2">break;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glPopMatrix();</p>
<p class="src1">glDisable(GL_TEXTURE_GEN_S);<span class="kom">// Vypne automatické generování koordinátù S</span></p>
<p class="src1">glDisable(GL_TEXTURE_GEN_T);<span class="kom">// Vypne automatické generování koordinátù T</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter*2]);<span class="kom">// Zvolí texturu pozadí</span></p>
<p class="src1">glPushMatrix();</p>
<p class="src1">glTranslatef(0.0f, 0.0f, -24.0f);</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2">glNormal3f( 0.0f, 0.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-13.3f, -10.0f,  10.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 13.3f, -10.0f,  10.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 13.3f,  10.0f,  10.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-13.3f,  10.0f,  10.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glPopMatrix();</p>
<p class="src"></p>
<p class="src1">xrot+=xspeed;</p>
<p class="src1">yrot+=yspeed;</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Poslední vìc, kterou v této lekci udìláme je upravení kódu kontrolujícího stisk mezerníku - odstranili jsme disky.</p>

<p class="src4">if (keys[' '] &amp;&amp; !sp)</p>
<p class="src4">{</p>
<p class="src5">sp=TRUE;</p>
<p class="src5">object++;</p>
<p class="src"></p>
<p class="src5">if(object&gt;3)</p>
<p class="src6">object=0;</p>
<p class="src4">}</p>

<p>A máme hotovo. Umíme vytváøet skuteènì pùsobivé efekty s pou¾itím zrcadlení okolí na objektu - napøíklad témìø pøesného odrazu pokoje. Pùvodnì jsem chtìl také ukázat, jak vytváøet krychlové mapování prostøedí, ale moje aktuální videokarta ho nepodporuje. Mo¾ná za mìsíc nebo tak nìjak, a¾ si koupím GeForce2 :-]. Mapování okolí jsem se nauèil sám (hlavnì proto, ¾e jsem o tom nemohl najít témìø ¾ádné informace), tak¾e pokud je v tomto tutoriálu nìco nepøesné, po¹lete mi email nebo uvìdomte NeHe-ho. Díky a hodnì ¹tìstí.</p>

<p class="autor">napsal: <?OdkazBlank('http://www.tiptup.com/', 'GB Schmick - TipTup');?><br />
pøelo¾il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson23.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson23.zip">Delphi</a> kód této lekce. ( <a href="mailto:Alexandre.Hirzel@nat.unibe.ch">Alexandre Hirzel</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson23.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson23.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson23.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson23.zip">Game GLUT</a> kód této lekce. ( <a href="">Anonymous</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson23.zip">Java</a> kód této lekce. ( <a href="mailto:chris@interdictor.org">Chris Veenboer</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson23.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson23.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:arkadi@it.lv">Arkadi Shishlov</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosx/lesson23.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson23.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson23.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(23);?>
<?FceNeHeOkolniLekce(23);?>

<?
include 'p_end.php';
?>
