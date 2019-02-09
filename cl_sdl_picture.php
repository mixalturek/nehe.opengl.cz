<?
$g_title = 'CZ NeHe OpenGL - Komprimovan� textury a SDL_Image';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Komprimovan� textury a SDL_Image</h1>

<p class="nadpis_clanku">V tomto �l�nku si uk�eme, jak vytv��et komprimovan� OpenGL textury a jak za pomoci knihovny SDL_Image snadno na��tat obr�zky s alfa kan�lem nebo v paletov�m re�imu. T��du Picture jsem se sna�il navrhnou tak, aby byla co nejjednodu��� a dala se snadno pou��t v ka�d�m programu, z�rove� d�ky SDL_Image poskytuje velk� mo�nosti.</p>

<p>Prvn�, co si uk�eme a pop�eme, je deklarace t��dy Picture. Jak u� je patrn� z koment���, SizeX a SizeY ozna�uj� rozm�r obr�zku. U Bpp, kter� specifikuje velikost jednoho pixelu, pozor! Tato zkratka se v�t�inou pou��v� jako <b>Bit</b> Per Pixel, nicm�n� my v n� ukl�d�me <b>Byte</b> Per Pixel. Jej� hodnota n�m tedy ��k� nejen, kolik zabere jeden pixel byt� v pam�ti, ale tak� kolik m� slo�ek (3 = RGB, 4 = RGBA, ...). Ukazatel Data bude v sob� ukl�dat informace obr�zku, tedy jednotliv� pixely. V�echny funkce si rozep�eme d�le krom� Free(), FlipHorizontal() a FlipVertical(), u nich� je to zbyte�n�. Ty, kter� maj� n�vratovou hodnotu typu bool, vracej� true jako �sp�ch a false jako ne�sp�ch, ale to je, douf�m, ka�d�mu jasn�.</p>

<p class="src0">class Picture<span class="kom">// T��da obr�zku</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">Uint16 SizeX;<span class="kom">// ���ka obr�zku</span></p>
<p class="src1">Uint16 SizeY;<span class="kom">// V��ka obr�zku</span></p>
<p class="src1">Uint16 Bpp;<span class="kom">// Po�et <b>!BYT�!</b> na pixel</span></p>
<p class="src1">Uint8 *Data;<span class="kom">// Ukazatel na data obr�zku</span></p>
<p class="src"></p>
<p class="src1">bool Load(const char *FileName);<span class="kom">// Na�te obr�zek</span></p>
<p class="src1">void Free(void);<span class="kom">// Uvoln� obr�zek z pam�ti</span></p>
<p class="src"></p>
<p class="src1">void FlipHorizontal(void);<span class="kom">// Obr�t� obr�zek vodorovn�</span></p>
<p class="src1">void FlipVertical(void);<span class="kom">// Obr�t� obr�zek svisle</span></p>
<p class="src1">bool HalfSize(void);<span class="kom">// Zmen�� obr�zek na polovinu</span></p>
<p class="src"></p>
<p class="src1">GLuint CreateTexture(int MinFilter, int MagFilter, int BitsPerColor, bool MipMaps, bool Compress);<span class="kom">// Vytvo�en� textury</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Konstruktor a destruktor</span></p>
<p class="src1">Picture(void) { memset(this, 0 , sizeof(Picture)); }<span class="kom">// Vy�ist� pam� objektu</span></p>
<p class="src1">~Picture() { Free(); }<span class="kom">// Uvoln� obr�zek z pam�ti</span></p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">bool GetCompressTexExt(int Format);<span class="kom">// Zjist� p��tomnost po�adovan�ho roz���en� form�tu pro texturu</span></p>
<p class="src0">unsigned int GetInternalFormat(int Pixel_Format, int BitsPerColor);<span class="kom">// Pomocn� funkce pro zji�t�n� internal form�tu</span></p>

<p>Prvn� a nejd�le�it�j�� metoda ve t��d� Picture je Load(), kter� na�te obr�zek a p�evede ho na v OpenGL pou�iteln� form�t. Pro jeho na�ten� pou��v�me funkci IMG_Load(), kter� vrac� SDL_Surface, jen�e ten m� n�kolik nev�hod, kter� ho p�i p��m�m pou�it� v OpenGL vy�azuj�. V�t�ina na�ten�ch obr�zk� (krom� JPG) m� prohozen� �erven� a modr� slo�ky pixel�. Dal��m probl�mem jsou obr�zky ulo�en� v paletov�m re�imu, ty budeme muset p�ev�st na norm�ln� form�t, a aby toho nebylo m�lo, obr�zek m� i prohozen� ��dky :-(</p>

<p class="src0">bool Picture::Load(const char *FileName)<span class="kom">// Na�te obr�zek</span></p>
<p class="src0">{</p>
<p class="src1">Free();<span class="kom">// Zkontroluje, jestli u� nen� na�ten� jin� obr�zek a p��padn� ho uvolni</span></p>
<p class="src"></p>
<p class="src1">SDL_Surface *Image = IMG_Load(FileName);<span class="kom">// Na�te SDL_Surface pomoc� knihovny SDL_Image</span></p>
<p class="src"></p>
<p class="src1">if(Image == NULL)<span class="kom">// Nelze na��st</span></p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nelze nacist soubor \&quot;%s\&quot; : %s\n&quot;, FileName, SDL_GetError());</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SizeX = Image-&gt;w;<span class="kom">// Nastaven� prom�nn�ch ve t��d�</span></p>
<p class="src1">SizeY = Image-&gt;h;</p>
<p class="src"></p>
<p class="src1">if(Image-&gt;format == NULL)</p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Chyba v nactenem obrazku \&quot;%s\&quot;, neni udaj o formatu\n&quot;, FileName);</p>
<p class="src2">SDL_FreeSurface(Image);</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">Uint32 x, y, pix, Change = 0, PalIndex;<span class="kom">// Prom�nn� pro ��zen� cyklu</span></p>
<p class="src1">Uint8 *Pixels = (Uint8 *)Image-&gt;pixels;</p>

<p>Obr�zek bez palety pozn�me jen podle ukazatele pallete, kter� bude v takov�m p��pad� nastaven na hodnotu NULL, u paletov�ch obr�zk� obsahuje barvy pro indexy v obr�zku. Je�t� by se dal poznat podle po�tu byt� na pixel, kter� b�v� roven jedn�, ale kdybychom na�etli obr�zek s jednou slo�kou na pixel, byl by automaticky pova�ov�n za paletov�, co� by mohlo v�st k chyb�m - nap��klad u obr�zku obsahuj�c�m pouze alfa kan�l (i kdy� si nejsem jist, zda takov� form�t existuje).</p>

<p class="src1">if(Image-&gt;format-&gt;palette == NULL)<span class="kom">// Obr�zek bez palety</span></p>
<p class="src1">{</p>
<p class="src2">Bpp = Image-&gt;format-&gt;BytesPerPixel;</p>

<p>Alokujeme pot�ebnou pam� podle rozm�r� obr�zku a po�tu barevn�ch slo�ek. Na tomto m�st� by mohli rejpalov� nam�tat, �e je zbyte�n� p�id�vat p��kaz sizeof(Uint8), kter� v tomto p��pad� vr�t� hodnotu jedna. Mo�n� ano, ale nemuselo by to tak b�t, u�et��te si mnoho probl�m�.</p>

<p class="src2">Data = (Uint8 *) malloc(sizeof(Uint8) * SizeX * SizeY * Bpp);<span class="kom">// Alokace pam�ti obr�zku</span></p>
<p class="src"></p>
<p class="src2">if(Data == NULL)</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Nelze pridelit pamet (%d kB) potrebnou pro obrazek \&quot;%s\&quot;\n&quot;, (SizeX * SizeY * Bpp * sizeof(Uint8)) / 1024, FileName);</p>
<p class="src3">SDL_FreeSurface(Image);</p>
<p class="src3">return false;</p>
<p class="src2">}</p>

<p>Prom�nn� Change obsahuje hodnotu ur�uj�c�, kolik slo�ek barev se m� prohodit. Skoro v�dy je t�eba prohodit dv� slo�ky (z BGR na RGB), ale n�kdy ne, ur�uje to Bshift v SDL_Surface. Tak� si mus�me ov��it, zda m� obr�zek alespo� t�i slo�ky, pokud ne, nem� smysl je prohazovat. Tento p��pad m��e nastat nap��klad u obr�zk� jen s alfa hodnotou.</p>

<p class="src2">if(Image-&gt;format-&gt;Bshift == 0)<span class="kom">// Pokud je obr�zek BGR/BGRA, mus� se prohodit R a B</span></p>
<p class="src2">{</p>
<p class="src3">if(Bpp &gt;= 3)</p>
<p class="src3">{</p>
<p class="src4">Change = 3;<span class="kom">// Prom�nn� change zaji��uje p�ehazov�n� prvn�ch t�� slo�ek barvy</span></p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Kop�rovac� cyklus z�rove� se swapov�n�m slo�ek barev prohazuje i ��dky, kter� jsou v SDL_Surface opa�n�. Prvn� z vno�en�ch cykl�, kter� kop�ruje pixely nemus� prob�hnout, proto�e prom�nn� Change m��e b�t nula a to znamen�, �e se neprohazuj� barevn� slo�ky. Druh� prob�hne ve dvou p��padech. Hodnota v Change je nula a prvn� cyklus neprob�hl nebo je po�et slo�ek (Bpp) v�t�� ne� t�i a mus� se p�idat k prvn�m t�em prohozen�m je�t� dal�� hodnoty (nap�. alfa).</p>

<p class="src2">for(y = 0; y &lt; SizeY; y++)<span class="kom">// Kop�rovac� cyklus</span></p>
<p class="src2">{</p>
<p class="src3">for(x = 0 ; x &lt; SizeX ; x++)</p>
<p class="src3">{</p>
<p class="src4">for(pix = 0 ; pix &lt; Change ; pix++)<span class="kom">// Hodnoty, kter� se prohod� (v�dy 3) BGR na RGB</span></p>
<p class="src4">{</p>
<p class="src5">Data[(x + (y * SizeY)) * Bpp + (Change-1 - pix)] = Pixels[(x + ((SizeY-1 - y) * SizeY)) * Bpp + pix];</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">for(pix = Change ; pix &lt; Bpp ; pix++)<span class="kom">// Hodnoty, kter� z�stanou neprohozeny (nap�. alfa)</span></p>
<p class="src4">{</p>
<p class="src5">Data[(x + (y * SizeY)) * Bpp + pix] = Pixels[(x + ((SizeY-1 - y) * SizeY)) * Bpp + pix];</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Obr�zek v paletov�m re�imu je d�ky uspo��d�n� palety v SDL_Surface, kter� v sob� obsahuje strukturu SDL_Color, daleko jednodu���. Sta�� jen prohazovat ��dky. Obr�zek obsahuje m�sto slo�ek barev jen indexy do palety, pomoc� nich� se v n� orientujeme. T�mto zp�sobem na�teme barvy do pole Data.</p>

<p class="src1">else<span class="kom">// Obr�zek v paletov�m re�imu</span></p>
<p class="src1">{</p>
<p class="src2">Bpp = 3;<span class="kom">// P�edpokl�d�me RGB form�t a tud� zab�r� jeden pixel 3 byty</span></p>
<p class="src"></p>
<p class="src2">if(Image-&gt;format-&gt;palette-&gt;colors == NULL)<span class="kom">// Kontrola palety</span></p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Chyba v palete obrazku \&quot;%s\&quot;\n&quot;, FileName);</p>
<p class="src3">SDL_FreeSurface(Image);</p>
<p class="src3">return false;</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">Data = (Uint8 *) malloc(sizeof(Uint8) * SizeX * SizeY * Bpp);<span class="kom">// Pam� pro obr�zek</span></p>
<p class="src"></p>
<p class="src2">if(Data == NULL)</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Nelze pridelit pamet(%d kB) potrebnou pro obrazek \&quot;%s\&quot;\n&quot;, (SizeX * SizeY * Bpp * sizeof(Uint8)) / 1024, FileName);</p>
<p class="src3">SDL_FreeSurface(Image);</p>
<p class="src3">return false;</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">for(y = 0 ; y &lt; SizeY ; y++)<span class="kom">// Kop�rovac� cyklus</span></p>
<p class="src2">{</p>
<p class="src3">for(x = 0 ; x &lt; SizeX ; x++)</p>
<p class="src3">{</p>
<p class="src4">PalIndex = Pixels[x + ((SizeY-1 - y) * SizeY)];<span class="kom">// Index v palet�</span></p>
<p class="src"></p>
<p class="src4">Data[(x + (y * SizeY)) * Bpp] = Image-&gt;format-&gt;palette-&gt;colors[PalIndex].r;<span class="kom">// �erven�</span></p>
<p class="src4">Data[(x + (y * SizeY)) * Bpp + 1] = Image-&gt;format-&gt;palette-&gt;colors[PalIndex].g;<span class="kom">// Zelen�</span></p>
<p class="src4">Data[(x + (y * SizeY)) * Bpp + 2] = Image-&gt;format-&gt;palette-&gt;colors[PalIndex].b;<span class="kom">// Modr�</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SDL_FreeSurface(Image);<span class="kom">// �klid</span></p>
<p class="src1">return true;<span class="kom">// V�e OK</span></p>
<p class="src0">}</p>

<p>Ne� se pust�me do vytv��en� textury, d�me si trochu oddych. Pod�v�me se na funkci HalfSize, z jej�ho� n�zvu vypl�v� i jej� ��el - zmen�� obr�zek na polovinu. Mo�n� se n�kte�� pt�te, k �emu je takov� funkce dobr�, kdy� vlastn� sni�uje kvalitu obr�zku. Pr�v� o to jde, m��ete tak snadno ve sv�m programu v�echny textury p�i na��t�n� zmen�it na polovinu a t�m �et�it pam� a v�kon slab��ch stroj�.</p>

<p>Tato funkce ov�em nen� �pln� primitivn�m vynech�n�m jednoho ��dku jako v jist�m nejmenovan�m kresl�c�m programu od firmy Microsoft&reg;. P�i zmen�en� o polovinu se st�v� ze �ty� pixel� jeden, kter� je jejich pr�m�rem. To zajist�, aby nevymizely d�le�it� detaily. Podobn� pracuje i roz���en� multisample u grafick�ch karet, kde se pro zlep�en� kvality obrazu a zahlazen� hran vyrenderuje v�t�� obr�zek, kter� je n�sledn� zmen�en a zobrazen. Kdyby n�kdo cht�l vid�t, jak to vypad� bez tohoto efektu, a� odkomentuje variantu bez zahlazen� a odstran� tu se zahlazen�m.</p>

<p class="src0">bool Picture::HalfSize(void)<span class="kom">// Zmen�� obr�zek na polovinu</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Kontrola velikosti, dat a po�tu byt� na slo�ku barvy</span></p>
<p class="src1">if(Data == NULL || SizeX &lt; 2 || SizeY &lt; 2 || Bpp &lt; 1)</p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">int NewSizeX = SizeX / 2;<span class="kom">// Nov� velikost</span></p>
<p class="src1">int NewSizeY = SizeY / 2;</p>
<p class="src"></p>
<p class="src1">BYTE *NewPic = (BYTE *) malloc(sizeof(BYTE) * NewSizeX * NewSizeY * Bpp);<span class="kom">// P�id�len� pam�ti pro nov� polovi�n� obr�zek</span></p>
<p class="src"></p>
<p class="src1">if(NewPic == NULL)</p>
<p class="src1">{</p>
<p class="src2">fprintf(stderr, &quot;Nelze pridelit pamet(%d kB)\n&quot;, (sizeof(BYTE) * NewSizeX * NewSizeY * Bpp) / 1024);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Projdeme star� obr�zek a nahrajeme ho do polovi�n�ho. P�itom v�dy vytvo��me ze 4 pixel� jeden, kter� bude jejich pr�m�rem.</span></p>
<p class="src1">int x, y, b;</p>
<p class="src"></p>
<p class="src1">for(y = 0 ; y &lt; NewSizeY ; y++)</p>
<p class="src1">{</p>
<p class="src2">for(x = 0 ; x &lt; NewSizeX ; x++)</p>
<p class="src2">{</p>
<p class="src3">for(b = 0 ; b &lt; Bpp ; b++)</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// NewPic[(x + y * NewSizeX) * Bpp + b] = Data[(x*2 + y*2 * SizeX) * Bpp + b];</span><span class="kom">// Bez vyhlazen�</span></p>
<p class="src"></p>
<p class="src4">NewPic[(x + y * NewSizeX) * Bpp + b] = (BYTE) ((float) (Data[(x*2 + y*2 * SizeX) * Bpp + b] + Data[(x*2+1 + y*2 * SizeX) * Bpp + b] + Data[(x*2 + (y*2+1) * SizeX) * Bpp + b] + Data[(x*2+1 + (y*2+1) * SizeX) * Bpp + b]) / 4.0f);<span class="kom">// S vyhlazen�m</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Uvoln�n� star�ho obr�zku a nastaven� ukazatele na nov�</span></p>
<p class="src1">free(Data);</p>
<p class="src1">SizeX = NewSizeX;</p>
<p class="src1">SizeY = NewSizeY;</p>
<p class="src1">Data = NewPic;</p>
<p class="src"></p>
<p class="src1">return true;<span class="kom">// OK</span></p>
<p class="src0">}</p>

<p>Pomalu se vrhneme na funkci vytv��ej�c� z obr�zku texturu, ale p�edt�m se nejd��ve pod�v�me na jej� pomocnou funkci, kter� ov��uje podporu po�adovan�ho form�tu. Pomoc� glGetIntegerv() zjist�me po�et podporovan�ch form�t�, abychom mohli alokovat dostate�n� velkou pam� pro jejich seznam. Pot� si pomoc� stejn� funkce vy��d�me onen seznam, kter� n�sledn� prohled�me. Pokud nalezneme shodu s form�tem zadan�m v jedin�m parametru, funkce vr�t� true. Pokud nebude shoda nalezena, co� znamen� �e tento form�t nen� podporov�n, vr�t�me false.</p>

<p class="src0">bool GetCompressTexExt(int Format)<span class="kom">// Zjist� p��tomnost po�adovan�ho roz���en� form�tu pro texturu</span></p>
<p class="src0">{</p>
<p class="src1">GLint NumFormat = 0;</p>
<p class="src1">GLint *Formats = NULL;</p>
<p class="src"></p>
<p class="src1">glGetIntegerv(GL_NUM_COMPRESSED_TEXTURE_FORMATS_ARB, &amp;NumFormat);</p>
<p class="src1">Formats = (GLint *) malloc(sizeof(GLint) * NumFormat);</p>
<p class="src"></p>
<p class="src1">if(Formats == NULL)</p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glGetIntegerv(GL_COMPRESSED_TEXTURE_FORMATS_ARB, Formats);</p>
<p class="src"></p>
<p class="src1">for(GLint i = 0 ; i &lt; NumFormat ; i++)</p>
<p class="src1">{</p>
<p class="src2">if(Format == Formats[i])</p>
<p class="src2">{</p>
<p class="src3">free(Formats);</p>
<p class="src3">return true;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">free(Formats);</p>
<p class="src1">return false;</p>
<p class="src0">}</p>

<p>Kone�n� se dost�v�me k funkci CreateTexture(), kter� po vytvo�en� textury vr�t� jej� OpenGL adresu. Nap�ed si vysv�tl�me parametry. Do MinFilter a MagFilter se zad�v� filtrov�n� textury. V t�chto parametrech m��ete pou��t klasick� hodnoty OpenGL (GL_LINEAR, GL_NEAREST_MIPMAP_NEAREST, ...) nebo pro zjednodu�en� RV_LINEAR a RV_NEAREST, kter� jsou definovan� v hlavi�kov�m souboru na�� t��dy. Za tyto hodnoty v�m funkce sama dosad� podle dal��ho parametru MipMaps spr�vn� filtrov�n� pro norm�ln� nebo mipmapov� textury. Parametr BitsPerColor ur�uje velikost jedn� slo�ky barvy v pam�ti grafick� karty. Jedna slo�ka m��e b�t t�eba i 4 bity, co� je polovina bytu a to je taky d�vod pro� se zad�v� v bitech. Posledn� parametr Compress zap�n� komprimaci textur, u kter� odpad� nutnost nastavovat po�et bit� na slo�ku barvy.</p>

<p class="src0">GLuint Picture::CreateTexture(int MinFilter, int MagFilter, int BitsPerColor, bool MipMaps, bool Compress)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Zjist�, jestli jsou hodnoty filtrov�n� GL* nebo RV* - p�ed�l� je na GL</span></p>
<p class="src1">if(MinFilter == RV_NEAREST)</p>
<p class="src1">{</p>
<p class="src2">if(MipMaps)</p>
<p class="src2">{</p>
<p class="src3">MinFilter = GL_NEAREST_MIPMAP_NEAREST;</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">MinFilter = GL_NEAREST;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else if(MinFilter == RV_LINEAR)</p>
<p class="src1">{</p>
<p class="src2">if(MipMaps)</p>
<p class="src2">{</p>
<p class="src3">MinFilter = GL_LINEAR_MIPMAP_LINEAR;</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">MinFilter = GL_LINEAR;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(MagFilter == RV_NEAREST)</p>
<p class="src1">{</p>
<p class="src2">MagFilter = GL_NEAREST;</p>
<p class="src1">}</p>
<p class="src1">else if(MagFilter == RV_LINEAR)</p>
<p class="src1">{</p>
<p class="src2">MagFilter = GL_LINEAR;</p>
<p class="src1">}</p>

<p>Zde podle po�tu byt� na pixel ur��me OpenGL form�t textury. GL_ALPHA m� jednu slo�ku, RGB m� t�i a RGBA m� �ty�i slo�ky. Pokud je obr�zek v jin�m form�tu, nastane chyba. Nen� ale probl�m, podle pot�eby dopsat i jin� form�ty nebo napsat p�et�enou funkci kter� bude m�t o parametr v�c pr�v� pro form�t textury. J� jsem zvolil tento postup proto, aby byla funkce co nejv�ce samostatn� a nemuselo se zad�vat zbyte�n� moc parametr�, jejich� zji��ov�n� by pouze zdr�ovalo psan� programu a sni�ovalo jeho p�ehlednost.</p>

<p class="src1">unsigned int glFormat;<span class="kom">// Nastav� form�t podle po�tu byt� na barvu</span></p>
<p class="src"></p>
<p class="src1">switch(Bpp)</p>
<p class="src1">{</p>
<p class="src1">case 1:</p>
<p class="src2">glFormat = GL_ALPHA;</p>
<p class="src2">break;</p>
<p class="src1">case 3:</p>
<p class="src2">glFormat = GL_RGB;</p>
<p class="src2">break;</p>
<p class="src1">case 4:</p>
<p class="src2">glFormat = GL_RGBA;</p>
<p class="src2">break;</p>
<p class="src1">default:</p>
<p class="src2">fprintf(stderr, &quot;Nelze vybrat format textury. Obrazek obsahuje %d bytu na pixel\n&quot;, Bpp);</p>
<p class="src2">return 0;</p>
<p class="src1">}</p>

<p>Zde se podle form�tu textury vyhodnocuje jej� internal form�t, kter� ud�v�, jak se m� textura v pam�ti ulo�it. To m��e b�t jeden z komprima�n�ch form�t� nebo oby�ejn� GL_RGB8, kter� za n�s podle parametru BitsPerColor vybere funkce GetInternalFormat(). Nebudu ji zde popisovat (je to jen seznam, prohl�dn�te si ji ve zdroj�ch).</p>

<p class="src1"><span class="kom">// Nastav� internal format podle po�tu Bit� na barvu, nebo vybere compress program</span></p>
<p class="src1">unsigned int InternalFormat;</p>
<p class="src"></p>
<p class="src1">if(Compress)</p>
<p class="src1">{</p>
<p class="src2">if(glFormat == GL_RGB)</p>
<p class="src2">{</p>
<p class="src3">InternalFormat = GL_COMPRESSED_RGB_S3TC_DXT1_EXT;</p>
<p class="src2">}</p>
<p class="src2">else if(glFormat == GL_RGBA)</p>
<p class="src2">{</p>
<p class="src3">InternalFormat = GL_COMPRESSED_RGBA_S3TC_DXT5_EXT;</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Chyba, komprimovane textury mohou byt pouze ve formatu RGB nebo RGBA\n&quot;);</p>
<p class="src3">return 0;</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">if(GetCompressTexExt(InternalFormat) == false)</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Graficka karta nepodporuje rozsireni potrebne pro komprese textur\n&quot;);</p>
<p class="src3">return 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">if((InternalFormat = GetInternalFormat(glFormat, BitsPerColor)) == 0)</p>
<p class="src2">{</p>
<p class="src3">fprintf(stderr, &quot;Nelze vybrat internal format. glFormat %d, bytu na slozku barvy %d\n&quot;, glFormat, Bpp);</p>
<p class="src3">return 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Vytvo�en� textury z na�ten�ho obr�zku - rutina, kterou ka�d� OpenGL program�tor vygeneruje i o p�lnoci. Pou�ijeme zde hodnoty, kter� jsme p�edt�m pracn� shroma��ovali a vyb�rali.</p>

<p class="src1"><span class="kom">// Vytvo�en� textury</span></p>
<p class="src1">GLuint TexID;</p>
<p class="src1">glGenTextures(1, &amp;TexID);</p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, TexID);</p>
<p class="src"></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, MinFilter);</p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, MagFilter);</p>
<p class="src"></p>
<p class="src1">if(MipMaps)</p>
<p class="src1">{</p>
<p class="src2">gluBuild2DMipmaps(GL_TEXTURE_2D, InternalFormat, SizeX, SizeY, glFormat, GL_UNSIGNED_BYTE, Data);</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, InternalFormat, SizeX, SizeY, 0, glFormat, GL_UNSIGNED_BYTE, Data);</p>
<p class="src1">}</p>
<p class="src1"></p>
<p class="src1">return TexID;</p>
<p class="src0">}</p>


<p>Popis t��dy Picture je ��astn� za n�mi, ale jak ji v programu pou��t? Sta�� includovat Picture.h (+ Picture.cpp) a napsat n�co na tento zp�sob:</p>

<p class="src1">Picture Pic;<span class="kom">// Objekt t��dy</span></p>
<p class="src"></p>
<p class="src1">if(!Pic.Load("Alien2.tga"))<span class="kom">// Nahr�n� obr�zku</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">GLuint Texture = Pic.CreateTexture(RV_LINEAR, RV_LINEAR, 8, true, false);<span class="kom">// Vytvo�en� OpenGL textury</span></p>
<p class="src"></p>
<p class="src1">if(Texture == 0)<span class="kom">// Chyba p�i vytv��en� textury</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>V tomto p��pad� jsem ani nepou�il funkci free na uvoln�n� pam�ti, proto�e se po skon�en� funkce automaticky zavol� destruktor.</p>

<p>A to je v�e, jak prost�! No zas tak prost� to nebylo. Jen mal� upozorn�n� pro rejpaly: a� si budete prohl�et funkci FlipHorizontal(), tak mi nepi�te, �e jsem mohl prohazovat cel� ��dky a ne pixel po pixelu. P�i tomto postupu toti� nepot�ebujeme dynamicky p�id�lit pam� pro cel� ��dek, ale pou��v�me statick� pole o deseti prvc�ch a budeme p�edpokl�dat velikost pixel� men�� ne� deset byt� (samoz�ejm� je to o�et�en� ifem).</p>

<p class="autor">napsal: Radom�r Vr�na <?VypisEmail('rvalien@c-box.cz');?></p>

<p>Tento �l�nek byl naps�n pro web http://nehe.ceske-hry.cz/. Pokud ho chcete um�stit i na sv� str�nky, zeptejte autora.</p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_sdl_picture.rar');?> - Zdrojov� k�dy t��dy a uk�zkov� program (Visual C++)</li>
</ul>

<?
include 'p_end.php';
?>
