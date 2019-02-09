<?
$g_title = 'CZ NeHe OpenGL - Lekce 23 - Mapov�n� textur na kulov� quadratiky';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(23);?>

<h1>Lekce 23 - Mapov�n� textur na kulov� kvadriky</h1>

<p class="nadpis_clanku">Tento tutori�l je naps�n na b�zi lekce 18. V lekci 15 (Mapov�n� textur na fonty) jsem psal o automatick�m mapov�n� textur. Vysv�tlil jsem jak m��eme poprosit OpenGL o automatick� generov�n� texturov�ch koordin�t�, ale proto�e lekce 15 byla celkem skromn�, rozhodl jsem se p�idat mnohem v�ce detail� o t�to technice.</p>

<p>Mapov�n� kulov�ho prost�ed� (Sphere Environment Mapping) je rychl� cesta pro p�id�n� zrcadlen� na kovov� nebo zrcadlov� objekty. T�eba�e nen� tak p�esn� jako skute�n� zrcadlen� nebo jako krychlov� mapa prost�ed� (cube environment map) je o hodn� rychlej��. Jako z�klad pou�ijeme k�d z lekce 18, ale nepou�ijeme ��dnou z p�edchoz�ch textur. Pou�ijeme jednu kulovou mapu (sphere map) a jeden obr�zek pro pozad�.</p>

<p>Ne� za�neme... Red Book definuje kulovou mapu jako obraz sc�ny na kovov� kouli z nekone�n� vzd�lenosti a nekone�n�ho ohniskov�ho bodu. To je id�ln� a ve skute�n�m �ivot� nemo�n�. Nejlep�� zp�sob, bez pou�it� �o�ek ryb�ho oka (fish eye lens), kter� jsem na�el je pou�it� programu Adobe Photoshop:</p>

<p>Nejd��ve budeme pot�ebovat obr�zek prost�ed�, kter� chceme namapovat na kouli. Otev�eme obr�zek v Adobe Photoshopu a vybereme cel� obr�zek. Zkop�rujeme obr�zek a vytvo��me nov� obr�zek PSD (Photoshop form�t). Nov� obr�zek by m�l b�t stejn� velikosti jako obr�zek kter� jsme pr�v� zkop�rovali. Vlo��me kopii p�vodn�ho obr�zku do nov�ho. D�vodem pro� d�l�me kopii je, �e tak m��e Photoshop aplikovat sv� filtry. Nam�sto kop�rov�n� obr�zku m��eme vybrat m�d z lok�ln�ho menu (na kliknut� prav�ho tla��tka my�i) a zvolit m�d RGB. Pot� budou dostupn� v�echny filtry.</p>

<p>D�le pot�ebujeme zm�nit velikost obr�zku tak �e bude mocninou dvou. Pamatujte, �e abyste mohli pou��t obr�zek jako texturu mus� m�t rozm�ry 128x128, 256x256 atd. V menu image tedy vybereme image size, od�krtneme constraint proportions (zachovat pom�r stran) a zm�n�me velikost obr�zku na platnou velikost textury. Pokud m� v� obr�zek velikost 100x90 je lep�� vytvo�it texturu o velikosti 128x128 ne� 64x64. Vytv��en�m men��ho obr�zku ztrat�te hodn� detail�.</p>

<p>Jako posledn� vybereme menu filter (filtry) a v n�m distort (zdeformovat) a pou�ijte spherize modifier (modifik�tor koule). M��eme vid�t, �e st�ed obr�zku je nafoukl� jako bal�n. V norm�ln�m kulov�m mapov�n� by byla vn�j�� plocha �ern�, ale to nem� skute�n� vliv. Ulo��me obr�zek jako BMP a jsme p�ipraveni k programov�n�.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_23_bg.jpg" width="256" height="256" alt="Textura pozad�" />
<img src="images/nehe_tut/tut_23_reflect.jpg" width="256" height="256" alt="Pozad� zdeformovan� koul�" />
</div>

<p>V t�to lekci nebudeme p�id�vat ��dn� nov� glob�ln� prom�nn�, ale pouze uprav�me index pole pro ulo�en� �esti textur.</p>

<p class="src0">GLuint texture[6];<span class="kom">// �est textur</span></p>

<p>D�le modifikujeme funkci LoadGLTextures(), abychom mohli nahr�t 2 textury a aplikovat 3 filtry. Jednodu�e dvakr�t projdeme cyklem a v ka�d�m pr�chodu vytvo��me 3 textury poka�d� s jin�m filtrovac�m m�dem. Skoro cel� tento k�d je nov� nebo modifikovan�.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Loading bitmap a konverze na textury</span></p>
<p class="src0">{</p>
<p class="src1">int Status=FALSE;<span class="kom">// Indikuje chyby</span></p>
<p class="src"></p>
<p class="src1">AUX_RGBImageRec *TextureImage[2];<span class="kom">// Ukl�d� dv� bitmapy</span></p>
<p class="src"></p>
<p class="src1">memset(TextureImage,0,sizeof(void *)*2);<span class="kom">// Vynuluje pam�</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nahraje bitmapy a kontroluje vznikl� chyby</span></p>
<p class="src1">if ((TextureImage[0]=LoadBMP(&quot;Data/BG.bmp&quot;)) &amp;&amp;<span class="kom">// Textura pozad�</span></p>
<p class="src1">(TextureImage[1]=LoadBMP(&quot;Data/Reflect.bmp&quot;)))<span class="kom">// Textura kulov� mapy (sphere map)</span></p>
<p class="src1">{</p>
<p class="src2">Status=TRUE;<span class="kom">// V�e je bez probl�m�</span></p>
<p class="src"></p>
<p class="src2">glGenTextures(6, &amp;texture[0]);<span class="kom">// Generuje �est textur</span></p>
<p class="src"></p>
<p class="src2">for (int loop=0; loop&lt;=1; loop++)</p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Vytvo�� neline�rn� filtrovanou texturu</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop]);<span class="kom">// Textury 0 a 1</span></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_NEAREST);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_NEAREST);</p>
<p class="src"></p>
<p class="src3">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[loop]-&gt;sizeX, TextureImage[loop]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[loop]-&gt;data);</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Vytvo�� line�rn� filtrovanou texturu</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop+2]);<span class="kom">// Textury 2 a 3</span></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src"></p>
<p class="src3">glTexImage2D(GL_TEXTURE_2D, 0, 3, TextureImage[loop]-&gt;sizeX, TextureImage[loop]-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[loop]-&gt;data);</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Vytvo�� mipmapovanou texturu</span></p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, texture[loop+4]);<span class="kom">// Textury 2 a 3</span></p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src3">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR_MIPMAP_NEAREST);</p>
<p class="src"></p>
<p class="src3">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, TextureImage[loop]-&gt;sizeX, TextureImage[loop]-&gt;sizeY, GL_RGB, GL_UNSIGNED_BYTE, TextureImage[loop]-&gt;data);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">for (loop=0; loop&lt;=1; loop++)</p>
<p class="src2">{</p>
<p class="src3">if (TextureImage[loop])<span class="kom">// Pokud obr�zek existuje</span></p>
<p class="src3">{</p>
<p class="src4">if (TextureImage[loop]-&gt;data)<span class="kom">// Pokud existuj� data obr�zku</span></p>
<p class="src4">{</p>
<p class="src5">free(TextureImage[loop]-&gt;data);<span class="kom">// Uvoln� pam� obr�zku</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">free(TextureImage[loop]);<span class="kom">// Uvoln� strukturu obr�zku</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return Status;<span class="kom">// Ozn�m� p��padn� chyby</span></p>
<p class="src0">}</p>

<p>Trochu uprav�me k�d kreslen� krychle. Nam�sto pou�it� hodnot 1.0 a -1.0 pro norm�ly, pou�ijeme 0.5 a -0.5. Zm�nou hodnot norm�l m��eme m�nit velikost odrazov� mapy dovnit� a ven. Pokud je hodnota norm�ly velk�, odra�en� obr�zek je v�t�� a mohl by se zobrazovat �tvere�kovan�. Sn�en�m hodnoty norm�l na 0.5 a -0.5 je obr�zek trochu zmen�en, tak�e obr�zek odr�en� na krychli nevypad� tak �tvere�kovan�. Nastaven�m p��li� mal�ch hodnot z�sk�me ne��douc� v�sledky.</p>

<p class="src0">GLvoid glDrawCube()</p>
<p class="src0">{</p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// P�edn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f, 0.5f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Zadn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f,-0.5f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Vrchn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 0.5f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2"><span class="kom">// Spodn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f,-0.5f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Prav� st�na</span></p>
<p class="src2">glNormal3f( 0.5f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src2"><span class="kom">// Lev� st�na</span></p>
<p class="src2">glNormal3f(-0.5f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>Do InitGL p�id�me vol�n� dvou nov�ch funkc�. Tyto dv� vol�n� nastav� m�d generov�n� textur na S a T pro kulov� mapov�n� (sphere mapping). Texturov� koordin�ty S, T, R a Q souvis� s koordin�ty objektu x, y, z a w. Pokud pou��v�te jednorozm�rnou (1D) texturu, pou�ijete sou�adnici S. Pokud pou�ijete dvourozm�rnou texturu pou�ijete sou�adnice S a T.</p>

<p>Tak�e n�sleduj�c� k�d ��k� OpenGL jak automaticky generovat S a T koordin�ty na kulov� mapovan�m (sphere-mapping) vzorci. Koordin�ty R a Q jsou obvykle ignorov�ny. Koordin�t Q m��e b�t pou�it pro pokro�il� techniky mapov�n� textur a koordin�t R m��e b�t u�ite�n� a� bude do OpenGL p�id�no mapov�n� 3D textur. Ale pro te� budeme koordin�ty R a Q ignorovat. Koordin�t S b�� horizont�ln� p�es �elo na�eho polygonu a T zase vertik�ln�.</p>

<p class="src0"><span class="kom">// Funkce InitGL()</span></p>
<p class="src1"><span class="kom">// Nastaven� m�du generov�n� textur pro S koordin�ty pro kulov� mapov�n�</span></p>
<p class="src1">glTexGeni(GL_S, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);</p>
<p class="src1"><span class="kom">// Nastaven� m�du generov�n� textur pro T koordin�ty pro kulov� mapov�n�</span></p>
<p class="src1">glTexGeni(GL_T, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);</p>

<p>M�me t�m�� hotovo. V�e co mus�me je�t� ud�lat je nastavit vykreslov�n�. Odstranil jsem n�kolik typ� quadratik�, proto�e nepracovali dob�e s mapov�n�m prost�ed� (environment mapping). Zaprv� pot�ebujeme  povolit generov�n� textur. Potom vybereme odrazovou texturu (kulovou mapu - sphere map) a vykresl�me n� objekt. P�ed vykreslen�m pozad� vypneme kulov� mapov�n�. V�imn�te si, �e p��kaz glBindTexture() m��e vypadat docela slo�it�. V�e co d�l�me je v�b�r filtru pro kreslen� na�� kulov� mapy nebo obr�zku pozad�.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// V�echno kreslen�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,z);</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_GEN_S);<span class="kom">// Povol� generov�n� texturov�ch koordin�t� S</span></p>
<p class="src1">glEnable(GL_TEXTURE_GEN_T);<span class="kom">// Povol� generov�n� texturov�ch koordin�t� T</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter+(filter+1)]); <span class="kom">// Zvol� texturu kulov� mapy</span></p>
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
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrov�n�</span></p>
<p class="src3">gluCylinder(quadratic,1.0f,1.0f,3.0f,32,32);<span class="kom">// V�lec</span></p>
<p class="src3">break;</p>
<p class="src"></p>
<p class="src2">case 2:</p>
<p class="src3">gluSphere(quadratic,1.3f,32,32);<span class="kom">// Koule</span></p>
<p class="src3">break;</p>
<p class="src"></p>
<p class="src2">case 3:</p>
<p class="src3">glTranslatef(0.0f,0.0f,-1.5f);<span class="kom">// Vycentrov�n�</span></p>
<p class="src3">gluCylinder(quadratic,1.0f,0.0f,3.0f,32,32);<span class="kom">// Ku�el</span></p>
<p class="src2">break;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glPopMatrix();</p>
<p class="src1">glDisable(GL_TEXTURE_GEN_S);<span class="kom">// Vypne automatick� generov�n� koordin�t� S</span></p>
<p class="src1">glDisable(GL_TEXTURE_GEN_T);<span class="kom">// Vypne automatick� generov�n� koordin�t� T</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter*2]);<span class="kom">// Zvol� texturu pozad�</span></p>
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

<p>Posledn� v�c, kterou v t�to lekci ud�l�me je upraven� k�du kontroluj�c�ho stisk mezern�ku - odstranili jsme disky.</p>

<p class="src4">if (keys[' '] &amp;&amp; !sp)</p>
<p class="src4">{</p>
<p class="src5">sp=TRUE;</p>
<p class="src5">object++;</p>
<p class="src"></p>
<p class="src5">if(object&gt;3)</p>
<p class="src6">object=0;</p>
<p class="src4">}</p>

<p>A m�me hotovo. Um�me vytv��et skute�n� p�sobiv� efekty s pou�it�m zrcadlen� okol� na objektu - nap��klad t�m�� p�esn�ho odrazu pokoje. P�vodn� jsem cht�l tak� uk�zat, jak vytv��et krychlov� mapov�n� prost�ed�, ale moje aktu�ln� videokarta ho nepodporuje. Mo�n� za m�s�c nebo tak n�jak, a� si koup�m GeForce2 :-]. Mapov�n� okol� jsem se nau�il s�m (hlavn� proto, �e jsem o tom nemohl naj�t t�m�� ��dn� informace), tak�e pokud je v tomto tutori�lu n�co nep�esn�, po�lete mi email nebo uv�domte NeHe-ho. D�ky a hodn� �t�st�.</p>

<p class="autor">napsal: <?OdkazBlank('http://www.tiptup.com/', 'GB Schmick - TipTup');?><br />
p�elo�il: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson23.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson23.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Alexandre.Hirzel@nat.unibe.ch">Alexandre Hirzel</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson23.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson23.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson23.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson23.zip">Game GLUT</a> k�d t�to lekce. ( <a href="">Anonymous</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson23.zip">Java</a> k�d t�to lekce. ( <a href="mailto:chris@interdictor.org">Chris Veenboer</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson23.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson23.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:arkadi@it.lv">Arkadi Shishlov</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosx/lesson23.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson23.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson23.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(23);?>
<?FceNeHeOkolniLekce(23);?>

<?
include 'p_end.php';
?>
