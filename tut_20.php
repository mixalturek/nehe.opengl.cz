<?
$g_title = 'CZ NeHe OpenGL - Lekce 20 - Maskov�n�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(20);?>

<h1>Lekce 20 - Maskov�n�</h1>

<p class="nadpis_clanku">�ern� okraje obr�zk� jsme dosud o�ez�vali blendingem. A�koli je tato metoda efektivn�, ne v�dy transparentn� objekty vypadaj� dob�e. Modelov� situace: vytv���me hru a pot�ebujeme celistv� text nebo zak�iven� ovl�dac� panel, ale p�i blendingu sc�na prosv�t�. Nejlep��m �e�en�m je maskov�n� obr�zk�.</p>

<p>Bitmapov� form�t obr�zku je podporov�n ka�d�m po��ta�em a ka�d�m opera�n�m syst�mem. Nejen, �e se s nimi snadno pracuje, ale velmi snadno se nahr�vaj� a konvertuj� na textury. K o�ez�n� �ern�ch okraj� textu a obr�zk� jsme s v�hodou pou��vali blending, ale ne v�dy v�sledek vypadal dob�e. P�i spritov� animaci ve h�e nechcete, aby postavou prosv�talo pozad�. Podobn� i text by m�l b�t pevn� a snadno �iteln�. V takov�ch situac�ch se s v�hodou vyu��v� maskov�n�. M� dv� f�ze. V prvn� do sc�ny um�st�me �ernob�lou texturu, ve druh� na stejn� m�sto vykresl�me hlavn� texturu. Pou�it� typ blendingu zajist�, �e tam, kde se v masce (prvn� obr�zek) vyskytovala b�l� barva z�stane p�vodn� sc�na. Textura se nepr�hledn� vykresl� na �ernou barvu.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
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

<p>Masking ukl�d� p��znak zapnut�ho/vypnut�ho maskov�n� a podle scene se rozhodujeme, zda vykreslujeme prvn� nebo druhou verzi sc�ny. Loop je ��d�c� prom�nn� cykl�, roll pou�ijeme pro rolov�n� textur a rotaci objektu p�i zapnut� druh� sc�n�.</p>

<p class="src0">bool masking=TRUE;<span class="kom">// Maskov�n� on/off</span></p>
<p class="src0">bool mp;<span class="kom">// Stisknuto M?</span></p>
<p class="src0">bool sp;<span class="kom">// Stisknut mezern�k?</span></p>
<p class="src0">bool scene;<span class="kom">// Kter� sc�na se m� kreslit</span></p>
<p class="src"></p>
<p class="src0">GLuint texture[5];<span class="kom">// Ukl�d� 5 textur</span></p>
<p class="src0">GLuint loop;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src"></p>
<p class="src0">GLfloat roll;<span class="kom">// Rolov�n� textur</span></p>

<p>Generov�n� textur je ve sv�m principu �pln� stejn� jako ve v�ech minul�ch lekc�ch, ale velmi p�ehledn� demonstruje nahr�v�n� v�ce textur najednou. T�m�� v�dy jsme pou��vali pouze jednu. Deklarujeme pole ukazatel� na p�t bitmap, vynulujeme je a nahrajeme do nich obr�zky, kter� vz�p�t� zm�n�me na textury.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Nahraje bitmapu a konvertuje na texturu</span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;</p>
<p class="src1">AUX_RGBImageRec *TextureImage[5];<span class="kom">// Alokuje m�sto pro bitmapy</span></p>
<p class="src1">memset(TextureImage,0,sizeof(void *)*5);</p>
<p class="src"></p>
<p class="src1">if ((TextureImage[0]=LoadBMP(&quot;Data/logo.bmp&quot;)) &amp;&amp;<span class="kom">// Logo</span></p>
<p class="src1">(TextureImage[1]=LoadBMP(&quot;Data/mask1.bmp&quot;)) &amp;&amp;<span class="kom">// Prvn� maska</span></p>
<p class="src1">(TextureImage[2]=LoadBMP(&quot;Data/image1.bmp&quot;)) &amp;&amp;<span class="kom">// Prvn� obr�zek</span></p>
<p class="src1">(TextureImage[3]=LoadBMP(&quot;Data/mask2.bmp&quot;)) &amp;&amp;<span class="kom">// Druh� maska</span></p>
<p class="src1">(TextureImage[4]=LoadBMP(&quot;Data/image2.bmp&quot;)))<span class="kom">// Druh� obr�zek</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;</p>
<p class="src2">glGenTextures(5, &amp;texture[0]);</p>
<p class="src"></p>
<p class="src2">for (loop=0; loop&lt;5; loop++)<span class="kom">// Generuje jednotliv� textury</span></p>
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

<p>Z inicializace z�stala doslova kostra.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echno nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje textury</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Povol� maz�n� Depth Bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkov� testov�n�</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>P�i vykreslov�n� za�neme jako oby�ejn� maz�n�m buffer�, resetem matice a translac� do obrazovky.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-2.0f);<span class="kom">// P�esun do obrazovky</span></p>

<p>Zvol�me texturu loga a namapujeme ji na obd�ln�k. Koordin�ty vypadaj� n�jak divn�. Nam�sto obvykl�ch hodnot 0 a� 1 tentokr�t zad�me ��sla 0 a 3. P�ed�n�m trojky ozn�m�me, �e chceme namapovat texturu na polygon t�ikr�t. Pro vysv�tlen� m� napad� vlastnost vedle sebe p�i um�st�n� mal�ho obr�zku na plochu OS. Trojku zad�v�me do ���ky i do v��ky, tud� se na polygon rovnom�rn� namapuje celkem dev�t stejn�ch obr�zk�. Ke koordin�t�m tak� p�i��t�me (defakto ode��t�me) prom�nnou roll, kterou na konci funkce inkrementujeme. Vznik� dojem, �e vykreslovan� hladina sc�ny roluje, ale v programu se vlastn� m�n� pouze texturov� koordin�ty. Rolov�n� m��e b�t pou�ito pro r�zn� efekty. Nap��klad pohybuj�c� se mraky nebo text l�taj�c� po objektu.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_20_roll_2.jpg" width="96" height="96" alt="Po rolov�n�" />
<img src="images/nehe_tut/tut_20_roll_1.jpg" width="96" height="96" alt="P�ed rolov�n�m" />
</div>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// V�b�r textury loga</span></p>

<div class="okolo_img"><img src="images/nehe_tut/tut_20_logo.jpg" width="128" height="128" alt="Logo" /></div>

<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�k�</span></p>
<p class="src2">glTexCoord2f(0.0f, -roll+0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src2">glTexCoord2f(3.0f, -roll+0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src2">glTexCoord2f(3.0f, -roll+3.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, -roll+3.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Zapneme blending. Aby efekt pracoval mus�me vypnout testov�n� hloubky. Kdyby se nevypnulo nejv�t�� pravd�podobnost� by nic nebylo vid�t.</p>

<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne testov�n� hloubky</span></p>

<p>Podle hodnoty prom�nn� se rozhodneme, zda budeme obr�zek maskovat nebo pou�ijeme mnohokr�t vyzkou�en� blending. Maska je �ernob�l� kopie textury, kterou chceme vykreslit. B�l� oblasti masky budou pr�hledn�, �ern� nebudou. Pod b�l�mi sekcemi z�stane sc�na nezm�n�na.</p>

<p class="src1">if (masking)<span class="kom">// Je zapnut� maskov�n�?</span></p>
<p class="src1">{</p>
<p class="src2">glBlendFunc(GL_DST_COLOR,GL_ZERO);<span class="kom">// Blending barvy obrazu pomoc� nuly (�ern�)</span></p>
<p class="src1">}</p>

<p>Pokud bude scene true vykresl�me druhou, jinak prvn� sc�nu.</p>

<p class="src1">if (scene)<span class="kom">// Vykreslujeme druhou sc�nu?</span></p>
<p class="src1">{</p>

<p>Nechceme objekty p��li� velk�, tak�e se p�esuneme hloub�ji do obrazovky. Provedeme rotaci na ose z o 0� a� 360� podle prom�nn� roll.</p>

<p class="src2">glTranslatef(0.0f,0.0f,-1.0f);<span class="kom">// P�esun o jednotku do obrazovky</span></p>
<p class="src2">glRotatef(roll*360,0.0f,0.0f,1.0f);<span class="kom">// Rotace na ose z</span></p>

<p>Pokud je zapnut� maskov�n�, vykresl�me nejd��ve masku a potom objekt. P�i vypnut�m pouze objekt.</p>

<p class="src2">if (masking)<span class="kom">// Je zapnut� maskov�n�?</span></p>
<p class="src2">{</p>

<p>Nastaven� blendingu pro masku jsme provedli d��ve. Zvol�me texturu masky a namapujeme ji na obd�ln�k. Po vykreslen� se na sc�n� objev� �ern� m�sta odpov�daj�c� masce.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_20_mask2.jpg" width="128" height="128" alt="Druh� maska" />
<img src="images/nehe_tut/tut_20_image2.jpg" width="128" height="128" alt="Druh� obr�zek" />
</div>

<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[3]);<span class="kom">// V�b�r textury druh� masky</span></p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src3">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src2">}</p>

<p>Znovu zm�n�me m�d blendingu. Ozn�m�me t�m, �e chceme vykreslit v�echny ��sti barevn� textury, kter� NEJSOU �ern�. Proto�e je obr�zek barevnou kopi� masky, tak se vykresl� jen m�sta nad �ern�mi ��stmi masky. Proto�e je maska �ern�, nic ze sc�ny nebude prosv�tat skrz textury. Vznikne dojem pevn� vypadaj�c�ho obr�zku. Zvol�me barevnou texturu. Pot� ji vykresl�me se stejn�mi sou�adnicemi bod� v prostoru a stejn�mi texturov�mi koordin�ty jako masku. Kdybychom masku nevykreslily, obr�zek by se zkop�roval do sc�ny, ale d�ky blendingu by byl pr�hledn�. Objekty za n�m by prosv�taly.</p>

<p class="src2">glBlendFunc(GL_ONE, GL_ONE);<span class="kom">// Pro druh� barevn� obr�zek</span></p>
<p class="src"></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[4]);<span class="kom">// Zvol� druh� obr�zek</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src1">}</p>

<p>P�i hodnot� FALSE ulo�en� ve scene se vykresl� prvn� sc�na. Op�t v�tv�me program podle maskov�n�. P�i zapnut�m vykresl�me masku pro sc�nu jedna. Textura roluje zprava doleva (roll p�i��t�me k horizont�ln�m koordin�t�m). Chceme, aby textura zaplnila celou sc�nu, tak�e neprov�d�me translaci do obrazovky.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_20_mask1.jpg" width="128" height="128" alt="Prvn� maska" />
<img src="images/nehe_tut/tut_20_image1.jpg" width="128" height="128" alt="Prvn� obr�zek" />
</div>

<p class="src1">else<span class="kom">// Vykreslen� prvn� sc�ny</span></p>
<p class="src1">{</p>
<p class="src2">if (masking)<span class="kom">// Je zapnut� maskov�n�?</span></p>
<p class="src2">{</p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[1]);<span class="kom">// V�b�r textury prvn� masky</span></p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src4">glTexCoord2f(roll+0.0f, 0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(roll+4.0f, 0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(roll+4.0f, 4.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src4">glTexCoord2f(roll+0.0f, 4.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src3">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src2">}</p>

<p>Blending nastav�me stejn� jako minule. Vybereme texturu sc�ny jedna a vykresl�me ji na stejn� m�sto jako masku.</p>

<p class="src2">glBlendFunc(GL_ONE, GL_ONE);<span class="kom">// Pro prvn� barevn� obr�zek</span></p>
<p class="src"></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[2]);<span class="kom">// Zvol� prvn� obr�zek</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src3">glTexCoord2f(roll+0.0f, 0.0f); glVertex3f(-1.1f,-1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(roll+4.0f, 0.0f); glVertex3f( 1.1f,-1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(roll+4.0f, 4.0f); glVertex3f( 1.1f, 1.1f, 0.0f);</p>
<p class="src3">glTexCoord2f(roll+0.0f, 4.0f); glVertex3f(-1.1f, 1.1f, 0.0f);</p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src1">}</p>

<p>Zapneme testov�n� hloubky a vypneme blending. V mal�m programu je to v�c celkem zbyte�n�, ale u rozs�hlej��ch projekt� n�kdy nev�te, co zrovna m�te zapnut� nebo vypnut�. Tyto chyby se obt�n� hledaj� a kradou �as. Po ur�it� dob� ztr�c�te orientaci, k�d se st�v� slo�it�j��m - preventivn� opat�en�.</p>

<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>
<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vypne blending</span></p>

<p>Aby se sc�na dynamicky pohybovala mus�me inkrementovat roll.</p>

<p class="src1">roll+=0.002f;<span class="kom">// Inkrementace roll</span></p>
<p class="src"></p>
<p class="src1">if (roll&gt;1.0f)<span class="kom">// Je v�t�� ne� jedna?</span></p>
<p class="src1">{</p>
<p class="src2">roll-=1.0f;<span class="kom">// Ode�te jedna</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>O�et��me vstup z kl�vesnice. Po stisku mezern�ku zm�n�me vykreslovanou sc�nu.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if (keys[' '] &amp;&amp; !sp)<span class="kom">// Mezern�k - zm�na sc�ny</span></p>
<p class="src4">{</p>
<p class="src5">sp=TRUE;</p>
<p class="src5">scene=!scene;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys[' '])<span class="kom">// Uvoln�n� mezern�ku</span></p>
<p class="src4">{</p>
<p class="src5">sp=FALSE;</p>
<p class="src4">}</p>

<p>Stiskem kl�vesy M zapneme, pop�. vypneme maskov�n�.</p>

<p class="src4">if (keys['M'] &amp;&amp; !mp)<span class="kom">// Kl�vesa M - zapne/vypne maskov�n�</span></p>
<p class="src4">{</p>
<p class="src5">mp=TRUE;</p>
<p class="src5">masking=!masking;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys['M'])<span class="kom">// Uvoln�n� kl�vesy M</span></p>
<p class="src4">{</p>
<p class="src5">mp=FALSE;</p>
<p class="src4">}</p>

<p>Vytvo�en� masky nen� p��li� t�k�. Pokud m�te origin�ln� obr�zek ji� nakreslen�, otev�ete ho v n�jak�m grafick�m editoru a transformujte ho do �ed� palety barev. Po t�to operaci zvy�te kontrast, tak�e se �ed� pixely ztmav� na �ern�. Zkuste tak� sn�it jas ap. Je d�le�it�, aby b�l� byla opravdu b�l� a �ern� �ist� �ern�. M�te-li pochyby p�eve�te obr�zek do �ernob�l�ho re�imu (2 barvy). Pokud by v masce z�staly �ed� pixely byly by pr�hledn�. Je tak� d�le�it�, aby barevn� obr�zek m�l �ern� pozad� a masku b�lou. Otestujte si barvy masky kap�tkem (v�t�inou b�vaj� chyby na rozhran�). B�l� je v RGB 255 255 255 (FF FF FF), �ern� 0 0 0.</p>

<p>Lze zjistit barvu pixel� p�i nahr�v�n� bitmapy. Chcete-li pixel pr�hledn� m��ete mu p�i�adit alfu rovnou nule. V�em ostatn�m barv�m 255. Tato metoda tak� pracuje spolehliv�, ale vy�aduje extra k�d. T�mto chci pouk�zat, �e k v�sledku existuje v�ce cest - v�echny mohou b�t spr�vn�.</p>

<p>Nau�ili jsme se, jak vykreslit ��st textury bez pou�it� alfa kan�lu. Klasick� blending, kter� zn�me, nevypadal nejl�pe a textury s alfa kan�lem pot�ebuj� obr�zky, kter� alfa kan�l podporuj�. Bitmapy jsou vhodn� p�edev��m d�ky snadn� pr�ci, ale maj� ji� zm�n�n� omezen�. Tento program uk�zal, jak obej�t nedostatky bitmapov�ch obr�zk� a vykreslov�n� jedn� textury v�cekr�t na jeden obd�ln�k. V�e jsme roz���ili rolov�n�m textur po sc�n�.</p>

<p>D�kuji Robu Santovi za uk�zkov� k�d, ve kter�m mi poprv� p�edstavil trik mapov�n� dvou textur. Nicm�n� ani tato cesta nen� �pln� dokonal�. Aby efekt pracoval, pot�ebujete dva pr�chody - dvakr�t vykreslujete jeden objekt. Z toho plyne, �e vykreslov�n� t�mto zp�sobem je dvakr�t pomalej��. Nicm�n�... co se d� d�lat?</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson20.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson20_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson20.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson20.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson20.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson20.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson20.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson20.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson20.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:alex_r@vortexentertainment.com">Alexandre Ribeiro de S?</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson20.tar.gz">Irix / GLUT</a> k�d t�to lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson20.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson20.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson20.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:bryantdesign11@mindspring.com">Daniel Davis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson20.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:miqster@gmx.net">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson20.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson20.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson20.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson20.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:chris.j84@free.fr">Christophe</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson20.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson20.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:fredo@studenten.net">Edo</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson20.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(20);?>
<?FceNeHeOkolniLekce(20);?>

<?
include 'p_end.php';
?>
