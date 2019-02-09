<?
$g_title = 'CZ NeHe OpenGL - SDL Image';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>SDL Image</h1>

<p class="nadpis_clanku">Urèitì se vám nelíbí mít v¹echny textury ulo¾ené v BMP souborech, které nejsou zrovna pøátelské k místu na disku. Bohu¾el SDL ¾ádný jiný formát pøímo nepodporuje. Nicménì existuje malé roz¹íøení v podobì knihovnièky SDL Image poskytující funkci IMG_Load(), která umí naèíst vìt¹inu pou¾ívaných grafických formátù.</p>

<p>Pokud tuto knihovnu nemáte, mù¾ete si ji stáhnout ze známé adresy <?OdkazBlank('http://www.libsdl.org/');?>. Zaèneme vlo¾ením hlavièkových souborù. Nezapomeòte kromì OpenGL a SDL pøilinkovat i SDL_image.</p>

<p class="src0">#include &lt;SDL.h&gt;<span class="kom">// Hlavní SDL knihovna</span></p>
<p class="src0">#include &lt;SDL_opengl.h&gt;<span class="kom">// Vlo¾í za nás OpenGL</span></p>
<p class="src0">#include &lt;SDL_image.h&gt;<span class="kom">// Abychom mohli pou¾ívat funkci IMG_Load()</span></p>
<p class="src"></p>
<p class="src0">#include &lt;stdio.h&gt;</p>
<p class="src0">#include &lt;string.h&gt;</p>
<p class="src0">#include &lt;stdlib.h&gt;</p>

<p>Daklarujeme promìnnou OpenGL textury.</p>

<p class="src0">GLuint gl_texture;<span class="kom">// Textura</span></p>

<p>Napí¹eme funkci, která naète SDL\_Surface. Obrázek musíme souèasnì upravit, aby byl ve slo¾kách RGB a nebyl vzhùru nohama.</p>

<p class="src0">SDL_Surface* LoadBitmap(const char *filename)<span class="kom">// Funkce pro naèteni bitmapy</span></p>
<p class="src0">{</p>
<p class="src1">Uint8 *rowhi, *rowlo;<span class="kom">// Ukazatele na prohazováni øádkù</span></p>
<p class="src1">Uint8 *tmpbuf, tmpch;<span class="kom">// Doèasná pamì»</span></p>
<p class="src1">int i, j;<span class="kom">// Øídící promìnné pro cykly</span></p>
<p class="src"></p>
<p class="src1">SDL_Surface *image;<span class="kom">// Naèítaný obrázek</span></p>
<p class="src"></p>
<p class="src1">image = IMG_Load(filename);<span class="kom">// Naètení dat obrázku</span></p>
<p class="src"></p>
<p class="src1">if (image == NULL)<span class="kom">// O¹etøení chyby pøi naèítání</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nepodarilo se nacist %s: %s\n&quot;, filename, SDL_GetError());</p>
<p class="src2">return(NULL);</p>
<p class="src1">}</p>

<p>GL surfaces jsou vzhùru nohama, tak¾e budeme muset bitmapu pøevrátit. Alokujeme dynamickou pamì», do které odlo¾íme právì pøemis»ovaný kousek. Její velikost se neurèí podle image-&gt;w, ale podle image-&gt;pitch, proto¾e u SDL\_Surface se mù¾e stát, ¾e kvùli zarovnávání v pamìti SDL zabere více místa, ne¾ je skuteèný rozmìr obrázku.</p>

<p>Image-&gt;pitch udává ¹íøku zabrané pamìti, ale image-&gt;w udává ¹íøku obrázku.</p>

<p class="src1">tmpbuf = (Uint8 *)malloc(image-&gt;pitch);<span class="kom">// Alokace pamìti</span></p>
<p class="src"></p>
<p class="src1">if (tmpbuf == NULL)<span class="kom">// O¹etøení chyby</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nedostatek pameti\n&quot;);</p>
<p class="src2">return NULL;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastavení prvního a posledního øádku</span></p>
<p class="src1">rowhi = (Uint8 *)image-&gt;pixels;</p>
<p class="src1">rowlo = rowhi + (image-&gt;h * image-&gt;pitch) - image-&gt;pitch;</p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; image-&gt;h/2; i++)</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Pøevrácení BGR na RGB</span></p>
<p class="src2">if (image-&gt;format-&gt;Bshift == 0)</p>
<p class="src2">{</p>
<p class="src3">for (j = 0; j &lt; image-&gt;w; j++)</p>
<p class="src3">{</p>
<p class="src4">tmpch = rowhi[j*3];</p>
<p class="src"></p>
<p class="src4">rowhi[j*3] = rowhi[j*3+2];</p>
<p class="src4">rowhi[j*3+2] = tmpch;</p>
<p class="src"></p>
<p class="src4">tmpch = rowlo[j*3];</p>
<p class="src"></p>
<p class="src4">rowlo[j*3] = rowlo[j*3+2];</p>
<p class="src4">rowlo[j*3+2] = tmpch;</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Prohození øádkù</span></p>
<p class="src2">memcpy(tmpbuf, rowhi, image-&gt;pitch);</p>
<p class="src2">memcpy(rowhi,  rowlo, image-&gt;pitch);</p>
<p class="src2">memcpy(rowlo, tmpbuf, image-&gt;pitch);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Posun ukazatelù na øádky</span></p>
<p class="src2">rowhi += image-&gt;pitch;</p>
<p class="src2">rowlo -= image-&gt;pitch;</p>
<p class="src1">}</p>

<p>Zbývá u¾ jen smazat doèasný odkládací prostor a vrátit naètenou bitmapu.</p>

<p class="src1">free(tmpbuf);<span class="kom">// Úklid</span></p>
<p class="src"></p>
<p class="src1">return image;<span class="kom">// Vrátí naètený obrázek</span></p>
<p class="src0">}</p>

<p>Teï, kdy¾ máme naètený obrázek, vytvoøíme funkci, která z nìj vytvoøí OpenGL texturu. Hned na zaèátku se pokusíme naèíst obrázek právì napsanou funkcí LoadBitmap(). Pokud tato oprace sel¾e, vrátíme nulu.</p>

<p class="src0">GLuint CreateTexture(const char* file, int min_filter, int mag_filter, bool mipmaps)<span class="kom">// Vytvoøí texturu</span></p>
<p class="src0">{</p>
<p class="src1">SDL_Surface *surface;<span class="kom">// Obrázek</span></p>
<p class="src"></p>
<p class="src1">surface = LoadBitmap(file);<span class="kom">// Naètení obrázku</span></p>
<p class="src"></p>
<p class="src1">if (surface == NULL)<span class="kom">// O¹etøení chyby</span></p>
<p class="src2">return 0;</p>

<p>Vytvoøíme místo pro novou texturu a nastavíme filtry podle parametrù funkce.</p>

<p class="src1">GLuint texture;<span class="kom">// OpenGL textura</span></p>
<p class="src"></p>
<p class="src1">glGenTextures(1, &amp;texture);<span class="kom">// Generování jedné textury</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture);<span class="kom">// Nastavení textury</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastavení po¾adovaných filtrù</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, mag_filter);</p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, min_filter);</p>

<p>Vybereme podle pøání, jestli se mají pou¾ívat mipmapy nebo ne.</p>

<p class="src1">if (mipmaps)<span class="kom">// Mipmapovaná textura</span></p>
<p class="src1">{</p>
<p class="src2">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, surface-&gt;w, surface-&gt;h, GL_RGB, GL_UNSIGNED_BYTE, surface-&gt;pixels);</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Obyèejná textura</span></p>
<p class="src1">{</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, 3, surface-&gt;w, surface-&gt;h, 0, GL_RGB, GL_UNSIGNED_BYTE, surface-&gt;pixels);</p>
<p class="src1">}</p>

<p>Nakonec v¹e uklidíme. Pro mazání SDL\_Surface pou¾ívejte zásadnì SDL\_FreeSurface() a nikdy delete nebo free. Data bitmapy mù¾eme také smazat, proto¾e nejsou potøeba. OpenGL si je nakopírovalo a my u¾ se o nì nemusíme starat.</p>

<p class="src1">SDL_FreeSurface(surface);<span class="kom">// Smazání SDL_Surface</span></p>
<p class="src"></p>
<p class="src1">surface = NULL;<span class="kom">// Nastavení ukazatele na NULL</span></p>
<p class="src"></p>
<p class="src1">return texture;<span class="kom">// Vrátí texturu</span></p>
<p class="src0">}</p>

<p>Funkce, která vykresluje OpenGL scénu. Obdoba DrawGLScene() z NeHe OpenGL Tutoriálù.</p>

<p class="src0">void RenderScene()<span class="kom">// Vykreslí scénu</span></p>
<p class="src0">{</p>
<p class="src1">static float rott = 0.0f;<span class="kom">// Statická promìnná pro úhel rotace</span></p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, gl_texture);<span class="kom">// Zvolí texturu</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -6.0f);<span class="kom">// Pøesun do obrazovky</span></p>
<p class="src"></p>
<p class="src1">glRotatef(rott,-1.0, 0.0, 0.0);<span class="kom">// Natoèení scény</span></p>
<p class="src1">glRotatef(rott, 0.0, 1.0, 0.0);</p>
<p class="src1">glRotatef(rott, 0.0, 0.0,-1.0);</p>
<p class="src"></p>
<p class="src1">glBegin(GL_POLYGON);<span class="kom">// Vykreslí obdélník</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, 0.0f,-1.0);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, 0.0f, 1.0);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 0.0f, 1.0);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, 0.0f,-1.0);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">rott += 0.1;<span class="kom">// Zvìt¹í rotaci</span></p>
<p class="src"></p>
<p class="src1">SDL_GL_SwapBuffers();<span class="kom">// Prohození bufferù</span></p>
<p class="src0">}</p>

<p>Incializace SDL.</p>

<p class="src0">void InitSDL()<span class="kom">// Inicializace SDL</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Inicializace SDL grafiky a SDL Timeru </span></p>
<p class="src1">if (SDL_Init(SDL_INIT_VIDEO) &lt; 0)</p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nepodarilo se inicializovat SDL: %s\n&quot;, SDL_GetError());</p>
<p class="src2">exit(1);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">atexit(SDL_Quit);<span class="kom">// Nastavení funkce pøi volání exit();</span></p>
<p class="src"></p>
<p class="src1">SDL_GL_SetAttribute(SDL_GL_DOUBLEBUFFER, 1);<span class="kom">// Chceme doublebuffering s OpenGL</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastavení velikosti a stylu okna</span></p>
<p class="src1">unsigned int flags = SDL_OPENGL;<span class="kom">// | SDL_FULLSCREEN; // Pøípadnì bitovì orovat s fullscreen</span></p>
<p class="src"></p>
<p class="src1">if (SDL_SetVideoMode(640, 480, 0, flags) == NULL)<span class="kom">// Vytvoøí okno</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nepodarilo se vytvorit OpenGL okno : %s\n&quot;, SDL_GetError());</p>
<p class="src2">SDL_Quit();</p>
<p class="src2">exit(2);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SDL_WM_SetCaption(&quot;Lesson - SDL_image&quot;, NULL);<span class="kom">// Nastavení titulku okna</span></p>
<p class="src0">}</p>

<p>Incializace OpenGL.</p>

<p class="src0">void InitOpenGL()<span class="kom">// Nastavení základních parametrù OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glViewport(0, 0, 640, 480);</p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Povolení textur</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Povolení mazání hloubkového bufferu</span></p>
<p class="src1">glDepthFunc(GL_LESS);</p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolení testování hloubky</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);</p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Povolení svìtel</span></p>
<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Povolení vybarvování materiálù</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);</p>
<p class="src1">glLoadIdentity();</p>
<p class="src"></p>
<p class="src1">gluPerspective(45.0f, (GLfloat)640 / (GLfloat)480, 0.1f, 1000.0f);</p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Naètení textury</span></p>
<p class="src1">if ((gl_texture = CreateTexture(&quot;test.jpg&quot;, GL_LINEAR, GL_LINEAR, false)) == 0)</p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nepodarilo se vytvorit texturu\n&quot;);</p>
<p class="src2">exit(1);</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Pøi zavírání programu voláme ShutDownApp(), která sma¾e texturu a ukonèí SDL.</p>

<p class="src0">void ShutDownApp()<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">glDeleteTextures(1, &amp;gl_texture);<span class="kom">// Vymazání textury</span></p>
<p class="src"></p>
<p class="src1">SDL_Quit();<span class="kom">// Ukonèení SDL</span></p>
<p class="src0">}</p>

<p>Napí¹eme funkci, zpracovávající zprávy, které programu posílá systém. Reagujeme na ukonèení aplikace (SDL_QUIT), stisk klávesy (SDL_KEYDOWN) Esc (SDLK_ESCAPE) a pøekreslení okna SDL_VIDEOEXPOSE. Ostatní události nás nezajímají.</p>

<p class="src0">bool ProcessEvents()<span class="kom">// Obsluha událostí</span></p>
<p class="src0">{</p>
<p class="src1">SDL_Event event;<span class="kom">// Promìnná zprávy</span></p>
<p class="src"></p>
<p class="src1">while (SDL_PollEvent(&amp;event))<span class="kom">// Dokud pøicházejí zprávy, zpracovávat je</span></p>
<p class="src1">{</p>
<p class="src2">switch (event.type)<span class="kom">// Jaká pøi¹la zpráva?</span></p>
<p class="src2">{</p>
<p class="src3">case SDL_QUIT:<span class="kom">// U¾ivatel si pøeje ukonèit aplikaci</span></p>
<p class="src4">return false;</p>
<p class="src4">break;</p>
<p class="src"></p>
<p class="src3">case SDL_KEYDOWN:<span class="kom">// Stisknutá klávesa</span></p>
<p class="src4">switch (event.key.keysym.sym)<span class="kom">// Jaká klávesa?</span></p>
<p class="src4">{</p>
<p class="src5">case SDLK_ESCAPE:<span class="kom">// Esc</span></p>
<p class="src6">return false;</p>
<p class="src6">break;</p>
<p class="src4">}</p>
<p class="src4">break;</p>
<p class="src"></p>
<p class="src3">case SDL_VIDEOEXPOSE:<span class="kom">// Potøeba pøekreslit okno</span></p>
<p class="src4">RenderScene();<span class="kom">// Vykreslí scénu</span></p>
<p class="src4">break;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return true;</p>
<p class="src0">}</p>

<p>Main() je první funkcí, která se volá po spu¹tìní programu. Po jejím ukonèení se ukonèí i program. Na zaèátku inicializujeme SDL a OpenGL. V hlavní smyèce programu zpracováváme zprávy a pokud ¾ádné nepøijdou, pøekreslíme scénu. Po pøíchodu zprázy o ukonèení nebo pokud u¾ivatel stiskl klávesu Esc, uvolníme zdroje, které program zabral a ukonèíme aplikaci.</p>

<p class="src0">int main(int argc, char ** argv)<span class="kom">// Hlavní funkce</span></p>
<p class="src0">{</p>
<p class="src1">InitSDL(); <span class="kom">// Inicializace SDL</span></p>
<p class="src1">InitOpenGL();<span class="kom">// Inicializace OpenGL</span></p>
<p class="src"></p>
<p class="src1">while (ProcessEvents())<span class="kom">// Hlavní smyèka programu</span></p>
<p class="src1">{</p>
<p class="src2">RenderScene();<span class="kom">// Pokud nepøi¹la zpráva pøekreslí scénu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">ShutDownApp();<span class="kom">// Deinicializace</span></p>
<p class="src"></p>
<p class="src1">return 0;<span class="kom">// Konec main() a programu</span></p>
<p class="src0">}</p>

<p>Tak to je asi v¹echno. V archivu zdrojového kódu najdete pouze CPP soubor s JPG obrázkem, tak¾e musíte program je¹tì pøelo¾it. Pro kompilaci v Linuxu napi¹te:</p>

<p class="src0">gcc `sdl-config --libs --cflags` -lSDL_image -L/usr/X11R6/lib -lGL -lGLU lessonSDL_image.cpp</p>

<p>Pracujete-li v jiném operaèním systému, nezapomeòte kromì OpenGL a SDL pøilinkovat i knihovnu SDL_image.</p>

<p class="autor">napsal: Bernard Lidický - Berny <?VypisEmail('2berny@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/sdl_image.rar');?> - C/C++</li>
</ul>

<?
include 'p_end.php';
?>
