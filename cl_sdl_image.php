<?
$g_title = 'CZ NeHe OpenGL - SDL Image';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>SDL Image</h1>

<p class="nadpis_clanku">Ur�it� se v�m nel�b� m�t v�echny textury ulo�en� v BMP souborech, kter� nejsou zrovna p��telsk� k m�stu na disku. Bohu�el SDL ��dn� jin� form�t p��mo nepodporuje. Nicm�n� existuje mal� roz���en� v podob� knihovni�ky SDL Image poskytuj�c� funkci IMG_Load(), kter� um� na��st v�t�inu pou��van�ch grafick�ch form�t�.</p>

<p>Pokud tuto knihovnu nem�te, m��ete si ji st�hnout ze zn�m� adresy <?OdkazBlank('http://www.libsdl.org/');?>. Za�neme vlo�en�m hlavi�kov�ch soubor�. Nezapome�te krom� OpenGL a SDL p�ilinkovat i SDL_image.</p>

<p class="src0">#include &lt;SDL.h&gt;<span class="kom">// Hlavn� SDL knihovna</span></p>
<p class="src0">#include &lt;SDL_opengl.h&gt;<span class="kom">// Vlo�� za n�s OpenGL</span></p>
<p class="src0">#include &lt;SDL_image.h&gt;<span class="kom">// Abychom mohli pou��vat funkci IMG_Load()</span></p>
<p class="src"></p>
<p class="src0">#include &lt;stdio.h&gt;</p>
<p class="src0">#include &lt;string.h&gt;</p>
<p class="src0">#include &lt;stdlib.h&gt;</p>

<p>Daklarujeme prom�nnou OpenGL textury.</p>

<p class="src0">GLuint gl_texture;<span class="kom">// Textura</span></p>

<p>Nap�eme funkci, kter� na�te SDL\_Surface. Obr�zek mus�me sou�asn� upravit, aby byl ve slo�k�ch RGB a nebyl vzh�ru nohama.</p>

<p class="src0">SDL_Surface* LoadBitmap(const char *filename)<span class="kom">// Funkce pro na�teni bitmapy</span></p>
<p class="src0">{</p>
<p class="src1">Uint8 *rowhi, *rowlo;<span class="kom">// Ukazatele na prohazov�ni ��dk�</span></p>
<p class="src1">Uint8 *tmpbuf, tmpch;<span class="kom">// Do�asn� pam�</span></p>
<p class="src1">int i, j;<span class="kom">// ��d�c� prom�nn� pro cykly</span></p>
<p class="src"></p>
<p class="src1">SDL_Surface *image;<span class="kom">// Na��tan� obr�zek</span></p>
<p class="src"></p>
<p class="src1">image = IMG_Load(filename);<span class="kom">// Na�ten� dat obr�zku</span></p>
<p class="src"></p>
<p class="src1">if (image == NULL)<span class="kom">// O�et�en� chyby p�i na��t�n�</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nepodarilo se nacist %s: %s\n&quot;, filename, SDL_GetError());</p>
<p class="src2">return(NULL);</p>
<p class="src1">}</p>

<p>GL surfaces jsou vzh�ru nohama, tak�e budeme muset bitmapu p�evr�tit. Alokujeme dynamickou pam�, do kter� odlo��me pr�v� p�emis�ovan� kousek. Jej� velikost se neur�� podle image-&gt;w, ale podle image-&gt;pitch, proto�e u SDL\_Surface se m��e st�t, �e kv�li zarovn�v�n� v pam�ti SDL zabere v�ce m�sta, ne� je skute�n� rozm�r obr�zku.</p>

<p>Image-&gt;pitch ud�v� ���ku zabran� pam�ti, ale image-&gt;w ud�v� ���ku obr�zku.</p>

<p class="src1">tmpbuf = (Uint8 *)malloc(image-&gt;pitch);<span class="kom">// Alokace pam�ti</span></p>
<p class="src"></p>
<p class="src1">if (tmpbuf == NULL)<span class="kom">// O�et�en� chyby</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nedostatek pameti\n&quot;);</p>
<p class="src2">return NULL;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastaven� prvn�ho a posledn�ho ��dku</span></p>
<p class="src1">rowhi = (Uint8 *)image-&gt;pixels;</p>
<p class="src1">rowlo = rowhi + (image-&gt;h * image-&gt;pitch) - image-&gt;pitch;</p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; image-&gt;h/2; i++)</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// P�evr�cen� BGR na RGB</span></p>
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
<p class="src2"><span class="kom">// Prohozen� ��dk�</span></p>
<p class="src2">memcpy(tmpbuf, rowhi, image-&gt;pitch);</p>
<p class="src2">memcpy(rowhi,  rowlo, image-&gt;pitch);</p>
<p class="src2">memcpy(rowlo, tmpbuf, image-&gt;pitch);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Posun ukazatel� na ��dky</span></p>
<p class="src2">rowhi += image-&gt;pitch;</p>
<p class="src2">rowlo -= image-&gt;pitch;</p>
<p class="src1">}</p>

<p>Zb�v� u� jen smazat do�asn� odkl�dac� prostor a vr�tit na�tenou bitmapu.</p>

<p class="src1">free(tmpbuf);<span class="kom">// �klid</span></p>
<p class="src"></p>
<p class="src1">return image;<span class="kom">// Vr�t� na�ten� obr�zek</span></p>
<p class="src0">}</p>

<p>Te�, kdy� m�me na�ten� obr�zek, vytvo��me funkci, kter� z n�j vytvo�� OpenGL texturu. Hned na za��tku se pokus�me na��st obr�zek pr�v� napsanou funkc� LoadBitmap(). Pokud tato oprace sel�e, vr�t�me nulu.</p>

<p class="src0">GLuint CreateTexture(const char* file, int min_filter, int mag_filter, bool mipmaps)<span class="kom">// Vytvo�� texturu</span></p>
<p class="src0">{</p>
<p class="src1">SDL_Surface *surface;<span class="kom">// Obr�zek</span></p>
<p class="src"></p>
<p class="src1">surface = LoadBitmap(file);<span class="kom">// Na�ten� obr�zku</span></p>
<p class="src"></p>
<p class="src1">if (surface == NULL)<span class="kom">// O�et�en� chyby</span></p>
<p class="src2">return 0;</p>

<p>Vytvo��me m�sto pro novou texturu a nastav�me filtry podle parametr� funkce.</p>

<p class="src1">GLuint texture;<span class="kom">// OpenGL textura</span></p>
<p class="src"></p>
<p class="src1">glGenTextures(1, &amp;texture);<span class="kom">// Generov�n� jedn� textury</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture);<span class="kom">// Nastaven� textury</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastaven� po�adovan�ch filtr�</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, mag_filter);</p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, min_filter);</p>

<p>Vybereme podle p��n�, jestli se maj� pou��vat mipmapy nebo ne.</p>

<p class="src1">if (mipmaps)<span class="kom">// Mipmapovan� textura</span></p>
<p class="src1">{</p>
<p class="src2">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, surface-&gt;w, surface-&gt;h, GL_RGB, GL_UNSIGNED_BYTE, surface-&gt;pixels);</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Oby�ejn� textura</span></p>
<p class="src1">{</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, 3, surface-&gt;w, surface-&gt;h, 0, GL_RGB, GL_UNSIGNED_BYTE, surface-&gt;pixels);</p>
<p class="src1">}</p>

<p>Nakonec v�e uklid�me. Pro maz�n� SDL\_Surface pou��vejte z�sadn� SDL\_FreeSurface() a nikdy delete nebo free. Data bitmapy m��eme tak� smazat, proto�e nejsou pot�eba. OpenGL si je nakop�rovalo a my u� se o n� nemus�me starat.</p>

<p class="src1">SDL_FreeSurface(surface);<span class="kom">// Smaz�n� SDL_Surface</span></p>
<p class="src"></p>
<p class="src1">surface = NULL;<span class="kom">// Nastaven� ukazatele na NULL</span></p>
<p class="src"></p>
<p class="src1">return texture;<span class="kom">// Vr�t� texturu</span></p>
<p class="src0">}</p>

<p>Funkce, kter� vykresluje OpenGL sc�nu. Obdoba DrawGLScene() z NeHe OpenGL Tutori�l�.</p>

<p class="src0">void RenderScene()<span class="kom">// Vykresl� sc�nu</span></p>
<p class="src0">{</p>
<p class="src1">static float rott = 0.0f;<span class="kom">// Statick� prom�nn� pro �hel rotace</span></p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, gl_texture);<span class="kom">// Zvol� texturu</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -6.0f);<span class="kom">// P�esun do obrazovky</span></p>
<p class="src"></p>
<p class="src1">glRotatef(rott,-1.0, 0.0, 0.0);<span class="kom">// Nato�en� sc�ny</span></p>
<p class="src1">glRotatef(rott, 0.0, 1.0, 0.0);</p>
<p class="src1">glRotatef(rott, 0.0, 0.0,-1.0);</p>
<p class="src"></p>
<p class="src1">glBegin(GL_POLYGON);<span class="kom">// Vykresl� obd�ln�k</span></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, 0.0f,-1.0);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, 0.0f, 1.0);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 0.0f, 1.0);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, 0.0f,-1.0);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">rott += 0.1;<span class="kom">// Zv�t�� rotaci</span></p>
<p class="src"></p>
<p class="src1">SDL_GL_SwapBuffers();<span class="kom">// Prohozen� buffer�</span></p>
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
<p class="src1">atexit(SDL_Quit);<span class="kom">// Nastaven� funkce p�i vol�n� exit();</span></p>
<p class="src"></p>
<p class="src1">SDL_GL_SetAttribute(SDL_GL_DOUBLEBUFFER, 1);<span class="kom">// Chceme doublebuffering s OpenGL</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastaven� velikosti a stylu okna</span></p>
<p class="src1">unsigned int flags = SDL_OPENGL;<span class="kom">// | SDL_FULLSCREEN; // P��padn� bitov� orovat s fullscreen</span></p>
<p class="src"></p>
<p class="src1">if (SDL_SetVideoMode(640, 480, 0, flags) == NULL)<span class="kom">// Vytvo�� okno</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nepodarilo se vytvorit OpenGL okno : %s\n&quot;, SDL_GetError());</p>
<p class="src2">SDL_Quit();</p>
<p class="src2">exit(2);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SDL_WM_SetCaption(&quot;Lesson - SDL_image&quot;, NULL);<span class="kom">// Nastaven� titulku okna</span></p>
<p class="src0">}</p>

<p>Incializace OpenGL.</p>

<p class="src0">void InitOpenGL()<span class="kom">// Nastaven� z�kladn�ch parametr� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glViewport(0, 0, 640, 480);</p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Povolen� textur</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Povolen� maz�n� hloubkov�ho bufferu</span></p>
<p class="src1">glDepthFunc(GL_LESS);</p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolen� testov�n� hloubky</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);</p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Povolen� sv�tel</span></p>
<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Povolen� vybarvov�n� materi�l�</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);</p>
<p class="src1">glLoadIdentity();</p>
<p class="src"></p>
<p class="src1">gluPerspective(45.0f, (GLfloat)640 / (GLfloat)480, 0.1f, 1000.0f);</p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Na�ten� textury</span></p>
<p class="src1">if ((gl_texture = CreateTexture(&quot;test.jpg&quot;, GL_LINEAR, GL_LINEAR, false)) == 0)</p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nepodarilo se vytvorit texturu\n&quot;);</p>
<p class="src2">exit(1);</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>P�i zav�r�n� programu vol�me ShutDownApp(), kter� sma�e texturu a ukon�� SDL.</p>

<p class="src0">void ShutDownApp()<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">glDeleteTextures(1, &amp;gl_texture);<span class="kom">// Vymaz�n� textury</span></p>
<p class="src"></p>
<p class="src1">SDL_Quit();<span class="kom">// Ukon�en� SDL</span></p>
<p class="src0">}</p>

<p>Nap�eme funkci, zpracov�vaj�c� zpr�vy, kter� programu pos�l� syst�m. Reagujeme na ukon�en� aplikace (SDL_QUIT), stisk kl�vesy (SDL_KEYDOWN) Esc (SDLK_ESCAPE) a p�ekreslen� okna SDL_VIDEOEXPOSE. Ostatn� ud�losti n�s nezaj�maj�.</p>

<p class="src0">bool ProcessEvents()<span class="kom">// Obsluha ud�lost�</span></p>
<p class="src0">{</p>
<p class="src1">SDL_Event event;<span class="kom">// Prom�nn� zpr�vy</span></p>
<p class="src"></p>
<p class="src1">while (SDL_PollEvent(&amp;event))<span class="kom">// Dokud p�ich�zej� zpr�vy, zpracov�vat je</span></p>
<p class="src1">{</p>
<p class="src2">switch (event.type)<span class="kom">// Jak� p�i�la zpr�va?</span></p>
<p class="src2">{</p>
<p class="src3">case SDL_QUIT:<span class="kom">// U�ivatel si p�eje ukon�it aplikaci</span></p>
<p class="src4">return false;</p>
<p class="src4">break;</p>
<p class="src"></p>
<p class="src3">case SDL_KEYDOWN:<span class="kom">// Stisknut� kl�vesa</span></p>
<p class="src4">switch (event.key.keysym.sym)<span class="kom">// Jak� kl�vesa?</span></p>
<p class="src4">{</p>
<p class="src5">case SDLK_ESCAPE:<span class="kom">// Esc</span></p>
<p class="src6">return false;</p>
<p class="src6">break;</p>
<p class="src4">}</p>
<p class="src4">break;</p>
<p class="src"></p>
<p class="src3">case SDL_VIDEOEXPOSE:<span class="kom">// Pot�eba p�ekreslit okno</span></p>
<p class="src4">RenderScene();<span class="kom">// Vykresl� sc�nu</span></p>
<p class="src4">break;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return true;</p>
<p class="src0">}</p>

<p>Main() je prvn� funkc�, kter� se vol� po spu�t�n� programu. Po jej�m ukon�en� se ukon�� i program. Na za��tku inicializujeme SDL a OpenGL. V hlavn� smy�ce programu zpracov�v�me zpr�vy a pokud ��dn� nep�ijdou, p�ekresl�me sc�nu. Po p��chodu zpr�zy o ukon�en� nebo pokud u�ivatel stiskl kl�vesu Esc, uvoln�me zdroje, kter� program zabral a ukon��me aplikaci.</p>

<p class="src0">int main(int argc, char ** argv)<span class="kom">// Hlavn� funkce</span></p>
<p class="src0">{</p>
<p class="src1">InitSDL(); <span class="kom">// Inicializace SDL</span></p>
<p class="src1">InitOpenGL();<span class="kom">// Inicializace OpenGL</span></p>
<p class="src"></p>
<p class="src1">while (ProcessEvents())<span class="kom">// Hlavn� smy�ka programu</span></p>
<p class="src1">{</p>
<p class="src2">RenderScene();<span class="kom">// Pokud nep�i�la zpr�va p�ekresl� sc�nu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">ShutDownApp();<span class="kom">// Deinicializace</span></p>
<p class="src"></p>
<p class="src1">return 0;<span class="kom">// Konec main() a programu</span></p>
<p class="src0">}</p>

<p>Tak to je asi v�echno. V archivu zdrojov�ho k�du najdete pouze CPP soubor s JPG obr�zkem, tak�e mus�te program je�t� p�elo�it. Pro kompilaci v Linuxu napi�te:</p>

<p class="src0">gcc `sdl-config --libs --cflags` -lSDL_image -L/usr/X11R6/lib -lGL -lGLU lessonSDL_image.cpp</p>

<p>Pracujete-li v jin�m opera�n�m syst�mu, nezapome�te krom� OpenGL a SDL p�ilinkovat i knihovnu SDL_image.</p>

<p class="autor">napsal: Bernard Lidick� - Berny <?VypisEmail('2berny@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/sdl_image.rar');?> - C/C++</li>
</ul>

<?
include 'p_end.php';
?>
