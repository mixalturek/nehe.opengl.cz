<?
$g_title = 'CZ NeHe OpenGL - Lekce 33 - Nahr�v�n� komprimovan�ch i nekomprimovan�ch obr�zk� TGA';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(33);?>

<h1>Lekce 33 - Nahr�v�n� komprimovan�ch i nekomprimovan�ch obr�zk� TGA</h1>

<p class="nadpis_clanku">V lekci 24 jsem v�m uk�zal cestu, jak nahr�vat nekomprimovan� 24/32 bitov� TGA obr�zky. Jsou velmi u�ite�n�, kdy� pot�ebujete alfa kan�l, ale nesm�te se starat o jejich velikost, proto�e byste je ihned p�estali pou��vat. K diskov�mu m�stu nejsou zrovna �etrn�. Probl�m velikosti vy�e�� nahr�v�n� obr�zk� komprimovan�ch metodou RLE. K�d pro loading a hlavi�kov� soubory jsou odd�leny od hlavn�ho projektu, aby mohly b�t snadno pou�ity i jinde.</p>

<p>Za�neme dv�ma hlavi�kov� soubory. Texture.h, prvn� z nich, popisuje strukturu textury. Ka�d� hlavi�kov� soubor by m�l obsahovat ochranu proti v�cen�sobn�mu vlo�en�. Zaji��uj� ji p��kazy preprocesoru jazyka C. Pokud nen� definovan� symbolick� konstanta __TEXTURE_H__, nadefinujeme ji a do stejn�ho bloku podm�nky vep�eme zdrojov� k�d. P�i n�sleduj�c�m pokusu o inkludov�n� hlavi�kov�ho souboru existence konstanty ozn�m� preprocesoru, �e u� byl soubor jednou vlo�en, a tud� ho nem� vkl�dat podruh�.</p>

<p class="src0">#ifndef __TEXTURE_H__</p>
<p class="src0">#define __TEXTURE_H__</p>

<p>Budeme pot�ebovat strukturu informac� o obr�zku, ze kter�ho se vytv��� textura. Ukazatel imageData obsahuje data obr�zku, bpp barevnou hloubku, width a height rozm�ry. TexID je identifik�torem OpenGL textury, kter� se p�ed�v� funkci glBindTexture(). Type ur�uje typ textury - GL_RGB nebo GL_RGBA.</p>

<p class="src0">typedef struct<span class="kom">// Struktura textury</span></p>
<p class="src0">{</p>
<p class="src1">GLubyte* imageData;<span class="kom">// Data</span></p>
<p class="src1">GLuint bpp;<span class="kom">// Barevn� hloubka v bitech</span></p>
<p class="src1">GLuint width;<span class="kom">// ���ka</span></p>
<p class="src1">GLuint height;<span class="kom">// V��ka</span></p>
<p class="src1">GLuint type;<span class="kom">// Typ (GL_RGB, GL_RGBA)</span></p>
<p class="src1">GLuint texID;<span class="kom">// ID textury</span></p>
<p class="src0">} Texture;</p>
<p class="src"></p>
<p class="src0">#endif</p>

<p>Druh� hlavi�kov� soubor, tga.h, je speci�ln� ur�en pro loading TGA. Op�t za�neme o�et�en�m v�cen�sobn�ho inkludov�n�, pot� vlo��me hlavi�kov� soubor textury.</p>

<p class="src0">#ifndef __TGA_H__</p>
<p class="src0">#define __TGA_H__</p>
<p class="src"></p>
<p class="src0">#include &quot;texture.h&quot;<span class="kom">// Hlavi�kov� soubor textury</span></p>

<p>Strukturu TGAHeader p�edstavuje pole dvan�cti byt�, kter� ukl�daj� hlavi�ku obr�zku. Druh� struktura obsahuje pomocn� prom�nn� pro nahr�v�n� - nap�. velikost dat, barevnou hloubku a podobn�.</p>

<p class="src0">typedef struct<span class="kom">// Hlavi�ka TGA souboru</span></p>
<p class="src0">{</p>
<p class="src1">GLubyte Header[12];<span class="kom">// Dvan�ct byt�</span></p>
<p class="src0">} TGAHeader;</p>
<p class="src"></p>
<p class="src0">typedef struct<span class="kom">// Struktura obr�zku</span></p>
<p class="src0">{</p>
<p class="src1">GLubyte header[6];<span class="kom">// �est u�ite�n�ch byt� z hlavi�ky</span></p>
<p class="src1">GLuint bytesPerPixel;<span class="kom">// Barevn� hloubka v bytech</span></p>
<p class="src1">GLuint imageSize;<span class="kom">// Velikost pam�ti pro obr�zek</span></p>
<p class="src1"><span class="kom">// GLuint temp;// P�ekl.: nikde nen� pou�it�</span></p>
<p class="src1">GLuint type;<span class="kom">// Typ</span></p>
<p class="src1">GLuint Height;<span class="kom">// V��ka</span></p>
<p class="src1">GLuint Width;<span class="kom">// ���ka</span></p>
<p class="src1">GLuint Bpp;<span class="kom">// Barevn� hloubka v bitech</span></p>
<p class="src0">} TGA;</p>

<p>Deklarujeme instance pr�v� vytvo�en�ch struktur, abychom je mohli pou��t v programu.</p>

<p class="src0">TGAHeader tgaheader;<span class="kom">// TGA hlavi�ka</span></p>
<p class="src0">TGA tga;<span class="kom">// TGA obr�zek</span></p>

<p>N�sleduj�c� dv� pole pomohou ur�it validitu nahr�van�ho souboru. Pokud se hlavi�ka obr�zku neshoduje s n�kterou z nich, neum�me ho nahr�t.</p>

<p class="src0">GLubyte uTGAcompare[12] = { 0,0, 2,0,0,0,0,0,0,0,0,0 };<span class="kom">// TGA hlavi�ka nekomprimovan�ho obr�zku</span></p>
<p class="src0">GLubyte cTGAcompare[12] = { 0,0,10,0,0,0,0,0,0,0,0,0 };<span class="kom">// TGA hlavi�ka komprimovan�ho obr�zku</span></p>

<p>Ob� funkce nahr�vaj� TGA - jedna nekomprimovan� druh� komprimovan�.</p>

<p class="src0">bool LoadUncompressedTGA(Texture*, char*, FILE*);<span class="kom">// Nekomprimovan� TGA</span></p>
<p class="src0">bool LoadCompressedTGA(Texture*, char*, FILE*);<span class="kom">// Komprimovan� TGA</span></p>
<p class="src"></p>
<p class="src0">#endif</p>

<p>P�esuneme se k souboru TGALoader.cpp, kter� implementuje nahr�vac� funkce. Prvn�m ��dkem k�du vlo��me hlavi�kov� soubor. Inkludujeme pouze tga.h, proto�e texture.h jsme u� vlo�ili v n�m.</p>

<p class="src0">#include &quot;tga.h&quot;<span class="kom">// Hlavi�kov� soubor TGA</span></p>

<p>Funkce LoadTGA() je ta, kterou v programu vol�me, abychom nahr�li obr�zek. V parametrech se j� p�ed�v� ukazatel na texturu a �et�zec diskov� cesty. Nic dal��ho nepot�ebuje, proto�e si v�echny ostatn� parametry detekuje sama (ze souboru). Deklarujeme handle souboru a otev�eme ho pro �ten� v bin�rn�m m�du. Pokud n�co sel�e, nap�. soubor neexistuje, vyp�eme chybovou zpr�vu a vr�t�me false jako indikaci chyby.</p>

<p class="src0">bool LoadTGA(Texture* texture, char* filename)<span class="kom">// Nahraje TGA soubor</span></p>
<p class="src0">{</p>
<p class="src1">FILE* fTGA;<span class="kom">// Handle souboru</span></p>
<p class="src1">fTGA = fopen(filename, &quot;rb&quot;);<span class="kom">// Otev�e soubor</span></p>
<p class="src"></p>
<p class="src1">if(fTGA == NULL)<span class="kom">// Nepoda�ilo se ho otev��t?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not open texture file&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Zkus�me na��st hlavi�ku obr�zku (prvn�ch 12 byt� souboru), kter� ur�uje jeho typ. V�sledek se ulo�� do prom�nn� tgaheader.</p>

<p class="src1">if(fread(&amp;tgaheader, sizeof(TGAHeader), 1, fTGA) == 0)<span class="kom">// Na�te hlavi�ku souboru</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not read file header&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Pr�v� na�tenou hlavi�ku porovn�me s hlavi�kou nekomprimovan�ho obr�zku. Jsou-li shodn� nahrajeme obr�zek funkc� LoadUncompressedTGA(). Pokud shodn� nejsou zkus�me, jestli se nejedn� o komprimovan� obr�zek. V tomto p��pad� pou�ijeme pro nahr�v�n� funkci LoadCompressedTGA(). S jin�mi typy soubor� pracovat neum�me, tak�e jedin�, co m��eme ud�lat, je ozn�men� ne�sp�chu a ukon�en� funkce.</p>

<p>P�ekl.: M�la by se je�t� testovat n�vratov� hodnota, proto�e, jak uvid�te d�le, funkce v mnoha p��padech vracej� false. Program by si bez kontroly ni�eho nev�iml a pokra�oval d�le.</p>

<p class="src1">if(memcmp(uTGAcompare, &amp;tgaheader, sizeof(tgaheader)) == 0)<span class="kom">// Nekomprimovan�</span></p>
<p class="src1">{</p>
<p class="src2">LoadUncompressedTGA(texture, filename, fTGA);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// P�ekl.: Testovat n�vratovou hodnotu !!!</span></p>
<p class="src2"><span class="kom">// if(!LoadUncompressedTGA(texture, filename, fTGA))// Test n�vratov� hodnoty</span></p>
<p class="src2"><span class="kom">// {</span></p>
<p class="src3"><span class="kom">// return false;</span></p>
<p class="src2"><span class="kom">// }</span></p>
<p class="src1">}</p>
<p class="src1">else if(memcmp(cTGAcompare, &amp;tgaheader, sizeof(tgaheader)) == 0)<span class="kom">// Komprimovan�</span></p>
<p class="src1">{</p>
<p class="src2">LoadCompressedTGA(texture, filename, fTGA);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// P�ekl.: Testovat n�vratovou hodnotu !!!</span></p>
<p class="src2"><span class="kom">// if(!LoadCompressedTGA(texture, filename, fTGA))// Test n�vratov� hodnoty</span></p>
<p class="src2"><span class="kom">// {</span></p>
<p class="src3"><span class="kom">// return false;</span></p>
<p class="src2"><span class="kom">// }</span></p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Ani jeden z nich</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;TGA file be type 2 or type 10 &quot;, &quot;Invalid Image&quot;, MB_OK);</p>
<p class="src2">fclose(fTGA);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Pokud dosud nenastala ��dn� chyba, m��eme ozn�mit volaj�c�mu k�du, �e obr�zek byl v po��dku nahr�n a �e m��e z jeho dat vytvo�it texturu.</p>

<p class="src1">return true;<span class="kom">// V�e v po��dku</span></p>
<p class="src0">}</p>

<p>P�istoup�me k opravdov�mu nahr�v�n� obr�zk�, za�neme nekomprimovan�mi. Tato funkce je z velk� ��sti zalo�ena na t� z lekce 24, moc novinek v n� nenajdete. Zkus�me na��st dal��ch �est byt� ze souboru a ulo��me je do tga.header.</p>

<p class="src0">bool LoadUncompressedTGA(Texture* texture, char* filename, FILE* fTGA)<span class="kom">// Nahraje nekomprimovan� TGA</span></p>
<p class="src0">{</p>
<p class="src1">if(fread(tga.header, sizeof(tga.header), 1, fTGA) == 0)<span class="kom">// �est u�ite�n�ch byt�</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not read info header&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>M�me dost informac� pro ur�en� v��ky, ���ky a barevn� hloubky obr�zku. Ulo��me je do obou struktur - textury i obr�zku.</p>

<p class="src1">texture-&gt;width = tga.header[1] * 256 + tga.header[0];<span class="kom">// ���ka</span></p>
<p class="src1">texture-&gt;height = tga.header[3] * 256 + tga.header[2];<span class="kom">// V��ka</span></p>
<p class="src1">texture-&gt;bpp = tga.header[4];<span class="kom">// Barevn� hloubka v bitech</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Kop�rov�n� dat do struktury obr�zku</span></p>
<p class="src1">tga.Width = texture-&gt;width;</p>
<p class="src1">tga.Height = texture-&gt;height;</p>
<p class="src1">tga.Bpp = texture-&gt;bpp;</p>

<p>Otestujeme, jestli m� obr�zek alespo� jeden pixel a jestli je barevn� hloubka 24 nebo 32 bit�.</p>

<p class="src1"><span class="kom">// Platn� hodnoty?</span></p>
<p class="src1">if((texture-&gt;width &lt;= 0) || (texture-&gt;height &lt;= 0) || ((texture-&gt;bpp != 24) &amp;&amp; (texture-&gt;bpp != 32)))</p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Invalid texture information&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Nyn� nastav�me typ obr�zku. V p��pad� 24 bit� je j�m GL_RGB, u 32 bit� m� obr�zek i alfa kan�l, tak�e pou�ijeme GL_RGBA.</p>

<p class="src1">if(texture-&gt;bpp == 24)<span class="kom">// 24 bitov� obr�zek?</span></p>
<p class="src1">{</p>
<p class="src2">texture-&gt;type = GL_RGB;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// 32 bitov� obr�zek</span></p>
<p class="src1">{</p>
<p class="src2">texture-&gt;type = GL_RGBA;</p>
<p class="src1">}</p>

<p>Spo��t�me barevnou hloubku v BYTECH a celkovou velikost pam�ti pot�ebnou pro data. Vz�p�t� se ji pokus�me alokovat.</p>

<p class="src1">tga.bytesPerPixel = (tga.Bpp / 8);<span class="kom">// BYTY na pixel</span></p>
<p class="src1">tga.imageSize = (tga.bytesPerPixel * tga.Width * tga.Height);<span class="kom">// Velikost pam�ti</span></p>
<p class="src"></p>
<p class="src1">texture-&gt;imageData = (GLubyte *)malloc(tga.imageSize);<span class="kom">// Alokace pam�ti pro data</span></p>
<p class="src"></p>
<p class="src1">if(texture-&gt;imageData == NULL)<span class="kom">// Alokace ne�sp�n�</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not allocate memory for image&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2">fclose(fTGA);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Pokud se poda�ila alokace pam�ti, nahrajeme do n� data obr�zku.</p>

<p class="src1"><span class="kom">// Pokus� se nahr�t data obr�zku</span></p>
<p class="src1">if(fread(texture-&gt;imageData, 1, tga.imageSize, fTGA) != tga.imageSize)</p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not read image data&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src2">if(texture-&gt;imageData != NULL)</p>
<p class="src2">{</p>
<p class="src3">free(texture-&gt;imageData);<span class="kom">// Uvoln�n� pam�ti</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">fclose(fTGA);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Form�t TGA se od form�tu OpenGL li�� t�m, �e m� v pixelech p�ehozen� R a B slo�ky barvy (BGR m�sto RGB). Mus�me tedy zam�nit prvn� a t�et� byte v ka�d�m pixelu. Abychom tuto operace urychlili, provedeme t�i bin�rn� operace XOR. V�sledek je stejn� jako p�i pou�it� pomocn� prom�nn�.</p>

<p class="src1"><span class="kom">// P�evod BGR na RGB</span></p>
<p class="src1">for(GLuint cswap = 0; cswap &lt; (int)tga.imageSize; cswap += tga.bytesPerPixel)</p>
<p class="src1">{</p>
<p class="src2">texture-&gt;imageData[cswap] ^= texture-&gt;imageData[cswap+2] ^=</p>
<p class="src2">texture-&gt;imageData[cswap] ^= texture-&gt;imageData[cswap+2];</p>
<p class="src1">}</p>

<p>Obr�zek jsme �sp�n� nahr�li, tak�e zav�eme soubor a vr�cen�m true ozn�m�me �sp�ch.</p>

<p class="src1">fclose(fTGA);<span class="kom">// Zav�en� souboru</span></p>
<p class="src1">return true;<span class="kom">// �sp�ch</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pam� dat obr�zku se uvol�uje a� po vytvo�en� textury</span></p>
<p class="src0">}</p>

<p>Nyn� p�istoup�me k nahr�v�n� obr�zku komprimovan�ho metodou RLE (RunLength Encoded). Za��tek je stejn� jako u nekomprimovan�ho obr�zku - na�teme v��ku, ���ku a barevnou hloubku, o�et��me neplatn� hodnoty a spo��t�me velikost pot�ebn� pam�ti, kterou op�t alokujeme. V�imn�te si, �e velikost po�adovan� pam�ti je takov�, aby do n� mohla b�t ulo�ena data PO DEKOMPRIMOV�N�, ne p�ed dekomprimov�n�m.</p>

<p class="src0">bool LoadCompressedTGA(Texture* texture, char* filename, FILE* fTGA)<span class="kom">// Nahraje komprimovan� obr�zek</span></p>
<p class="src0">{ </p>
<p class="src1">if(fread(tga.header, sizeof(tga.header), 1, fTGA) == 0)<span class="kom">// �est u�ite�n�ch byt�</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not read info header&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">texture-&gt;width = tga.header[1] * 256 + tga.header[0];<span class="kom">// ���ka</span></p>
<p class="src1">texture-&gt;height = tga.header[3] * 256 + tga.header[2];<span class="kom">// V��ka</span></p>
<p class="src1">texture-&gt;bpp = tga.header[4];<span class="kom">// Barevn� hloubka v bitech</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Kop�rov�n� dat do struktury obr�zku</span></p>
<p class="src1">tga.Width = texture-&gt;width;</p>
<p class="src1">tga.Height = texture-&gt;height;</p>
<p class="src1">tga.Bpp = texture-&gt;bpp;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Platn� hodnoty?</span></p>
<p class="src1">if((texture-&gt;width &lt;= 0) || (texture-&gt;height &lt;= 0) || ((texture-&gt;bpp != 24) &amp;&amp; (texture-&gt;bpp != 32)))</p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Invalid texture information&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2"></p>
<p class="src2">if(fTGA != NULL)</p>
<p class="src2">{</p>
<p class="src3">fclose(fTGA);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(texture-&gt;bpp == 24)<span class="kom">// 24 bitov� obr�zek?</span></p>
<p class="src1">{</p>
<p class="src2">texture-&gt;type = GL_RGB;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// 32 bitov� obr�zek</span></p>
<p class="src1">{</p>
<p class="src2">texture-&gt;type = GL_RGBA;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">tga.bytesPerPixel = (tga.Bpp / 8);<span class="kom">// BYTY na pixel</span></p>
<p class="src1">tga.imageSize = (tga.bytesPerPixel * tga.Width * tga.Height);<span class="kom">// Velikost pam�ti</span></p>
<p class="src"></p>
<p class="src1">texture-&gt;imageData = (GLubyte *)malloc(tga.imageSize);<span class="kom">// Alokace pam�ti pro data (po dekomprimov�n�)</span></p>
<p class="src"></p>
<p class="src1">if(texture-&gt;imageData == NULL)<span class="kom">// Alokace ne�sp�n�</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Could not allocate memory for image&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src2">fclose(fTGA);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>D�le pot�ebujeme zjistit p�esn� po�et pixel�, ze kter�ch je obr�zek slo�en. Jednodu�e vyn�sob�me v��ku obr�zku se ���kou. Tak� mus�me zn�t, na kter�m pixelu se pr�v� nach�z�me a kam do pam�ti zapisujeme.</p>

<p class="src1">GLuint pixelcount = tga.Height * tga.Width;<span class="kom">// Po�et pixel�</span></p>
<p class="src1">GLuint currentpixel = 0;<span class="kom">// Aktu�ln� na��tan� pixel</span></p>
<p class="src1">GLuint currentbyte = 0;<span class="kom">// Aktu�ln� na��tan� byte</span></p>

<p>Alokujeme pomocn� pole t�� nebo �ty� byt� (podle barevn� hloubky) k ulo�en� jednoho pixelu. P�ekl.: M�la by se testovat spr�vnost alokace pam�ti!</p>

<p class="src1">GLubyte* colorbuffer = (GLubyte *)malloc(tga.bytesPerPixel);<span class="kom">// Pam� pro jeden pixel</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// P�ekl.: Test �sp�nosti alokace pam�ti !!!</span></p>
<p class="src1"><span class="kom">// if(colorbuffer == NULL)// Alokace ne�sp�n�</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// MessageBox(NULL, &quot;Could not allocate memory for color buffer&quot;, &quot;ERROR&quot;, MB_OK);</span></p>
<p class="src2"><span class="kom">// fclose(fTGA);</span></p>
<p class="src2"><span class="kom">// return false;</span></p>
<p class="src1"><span class="kom">// }</span></p>

<p>V hlavn�m cyklu deklarujeme prom�nnou k ulo�en� bytu hlavi�ky, kter� definuje, jestli je n�sleduj�c� sekce obr�zku ve form�tu RAW nebo RLE a jak dlouh� je. Pokud je byte hlavi�ky men�� nebo roven 127, jedn� se o RAW hlavi�ku. Hodnota v n� ulo�en�, ur�uje po�et pixel� m�nus jedna, kter� vz�p�t� na�teme a zkop�rujeme do pam�ti. Po t�chto pixelech se v souboru vyskytuje dal�� byte hlavi�ky. Pokud je byte hlavi�ky v�t�� ne� 127, p�edstavuje toto ��slo (zmen�en� o 127), kolikr�t se m� n�sleduj�c� pixel v dekomprimovan�m obr�zku opakovat. Hned po n�m se bude vyskytovat dal�� hlavi�kov� byte. Na�teme hodnoty tohoto pixelu a zkop�rujeme ho do imageData tolikr�t, kolikr�t pot�ebujeme.</p>

<p>Podstatu komprese RLE tedy u� zn�te, pod�vejme se na k�d. Jak jsem ji� zm�nil, zalo��me cyklus p�es cel� soubor a pokus�me se na��st byte prvn� hlavi�ky.</p>

<p class="src1">do<span class="kom">// Proch�z� cel� soubor</span></p>
<p class="src1">{</p>
<p class="src2">GLubyte chunkheader = 0;<span class="kom">// Byte hlavi�ky</span></p>
<p class="src"></p>
<p class="src2">if(fread(&amp;chunkheader, sizeof(GLubyte), 1, fTGA) == 0)<span class="kom">// Na�te byte hlavi�ky</span></p>
<p class="src2">{</p>
<p class="src3">MessageBox(NULL, &quot;Could not read RLE header&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src3">if(fTGA != NULL)</p>
<p class="src3">{</p>
<p class="src4">fclose(fTGA);</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if(texture-&gt;imageData != NULL)</p>
<p class="src3">{</p>
<p class="src4">free(texture-&gt;imageData);</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3"><span class="kom">// P�ekl.: Uvoln�n� dynamick� pam�ti !!!</span></p>
<p class="src3"><span class="kom">// if(colorbuffer != NULL)</span></p>
<p class="src3"><span class="kom">// {</span></p>
<p class="src4"><span class="kom">// free(colorbuffer);</span></p>
<p class="src3"><span class="kom">// }</span></p>
<p class="src"></p>
<p class="src3">return false;</p>
<p class="src2">}</p>

<p>Pokud se jedn� o RAW hlavi�ku, p�i�teme k bytu jedni�ku, abychom z�skali po�et pixel� n�sleduj�c�ch po hlavi�ce. Potom zalo��me dal�� cyklus, kter� na��t� v�echny po�adovan�ho pixely do pomocn�ho pole colorbuffer a vz�p�t� je ve spr�vn�m form�tu ukl�d� do imageData.</p>

<p class="src2">if(chunkheader &lt; 128)<span class="kom">// RAW ��st obr�zku</span></p>
<p class="src2">{</p>
<p class="src3">chunkheader++;<span class="kom">// Po�et pixel� v sekci p�ed v�skytem dal��ho bytu hlavi�ky</span></p>
<p class="src"></p>
<p class="src3">for(short counter = 0; counter &lt; chunkheader; counter++)<span class="kom">// Jednotliv� pixely</span></p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// Na��t�n� po jednom pixelu</span></p>
<p class="src4">if(fread(colorbuffer, 1, tga.bytesPerPixel, fTGA) != tga.bytesPerPixel)</p>
<p class="src4">{</p>
<p class="src5">MessageBox(NULL, &quot;Could not read image data&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src5">if(fTGA != NULL)</p>
<p class="src5">{</p>
<p class="src6">fclose(fTGA);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(colorbuffer != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(colorbuffer);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(texture-&gt;imageData != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(texture-&gt;imageData);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">return false;</p>
<p class="src4">}</p>

<p>P�i kop�rov�n� do imageData prohod�me po�ad� byt� z form�tu BGR na RGB. Pokud je v obr�zku i alfa kan�l, zkop�rujeme i �tvrt� byte. Abychom se p�esunuli na dal�� pixel pop�. byte hlavi�ky, zv�t��me aktu�ln� byte o barevnou hloubku (+3 nebo +4). Inkrementujeme tak� po�et na�ten�ch pixel�.</p>

<p class="src4"><span class="kom">// Z�pis do pam�ti, prohod� R a B slo�ku barvy</span></p>
<p class="src4">texture-&gt;imageData[currentbyte] = colorbuffer[2];</p>
<p class="src4">texture-&gt;imageData[currentbyte + 1] = colorbuffer[1];</p>
<p class="src4">texture-&gt;imageData[currentbyte + 2] = colorbuffer[0];</p>
<p class="src"></p>
<p class="src4">if(tga.bytesPerPixel == 4)<span class="kom">// 32 bitov� obr�zek?</span></p>
<p class="src4">{</p>
<p class="src5">texture-&gt;imageData[currentbyte + 3] = colorbuffer[3];<span class="kom">// Kop�rov�n� alfy</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">currentbyte += tga.bytesPerPixel;<span class="kom">// Aktualizuje byte</span></p>
<p class="src4">currentpixel++;<span class="kom">// P�esun na dal�� pixel</span></p>

<p>Zjist�me, jestli je po�adov� ��slo aktu�ln�ho pixelu v�t�� ne� celkov� po�et pixel�. Pokud ano, je soubor obr�zku po�kozen nebo je v n�m n�kde chyba. Jak jsme na to p�i�li? M�me na��tat dal�� pixel, ale defakto je u� m�me v�echny na�ten�, proto�e aktu�ln� hodnota je v�t�� ne� maxim�ln�. Nesta�ila by alokovan� pam� pro dekomprimovanou verzi obr�zku. Tuto skute�nost mus�me ka�dop�dn� o�et�it.</p>

<p class="src4">if(currentpixel &gt; pixelcount)<span class="kom">// Jsme za hranic� obr�zku?</span></p>
<p class="src4">{</p>
<p class="src5">MessageBox(NULL, &quot;Too many pixels read&quot;, &quot;ERROR&quot;, NULL);</p>
<p class="src"></p>
<p class="src5">if(fTGA != NULL)</p>
<p class="src5">{</p>
<p class="src6">fclose(fTGA);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(colorbuffer != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(colorbuffer);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(texture-&gt;imageData != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(texture-&gt;imageData);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">return false;</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Vy�e�ili jsme ��st RAW, nyn� implementujeme sekci RLE. Ze v�eho nejd��ve od bytu hlavi�ky ode�teme ��slo 127, abychom z�skali kolikr�t se m� n�sleduj�c� pixel opakovat.</p>

<p class="src2">else<span class="kom">// RLE ��st obr�zku</span></p>
<p class="src2">{</p>
<p class="src3">chunkheader -= 127;<span class="kom">// Po�et pixel� v sekci</span></p>

<p>Na�teme jeden pixel po hlavi�ce a potom ho po�adovan�-kr�t vlo��me do imageData. Op�t zam��ujeme form�t BGR za RGB. Stejn� jako minule inkrementujeme aktu�ln� byte i pixel a o�et�ujeme p�ete�en�.</p>

<p class="src3">if(fread(colorbuffer, 1, tga.bytesPerPixel, fTGA) != tga.bytesPerPixel)<span class="kom">// Na�te jeden pixel</span></p>
<p class="src3">{</p>
<p class="src4">MessageBox(NULL, &quot;Could not read from file&quot;, &quot;ERROR&quot;, MB_OK);</p>
<p class="src"></p>
<p class="src4">if(fTGA != NULL)</p>
<p class="src4">{</p>
<p class="src5">fclose(fTGA);</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if(colorbuffer != NULL)</p>
<p class="src4">{</p>
<p class="src5">free(colorbuffer);</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if(texture-&gt;imageData != NULL)</p>
<p class="src4">{</p>
<p class="src5">free(texture-&gt;imageData);</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">return false;</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">for(short counter = 0; counter &lt; chunkheader; counter++)<span class="kom">// Kop�rov�n� pixelu</span></p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// Z�pis do pam�ti, prohod� R a B slo�ku barvy</span></p>
<p class="src4">texture-&gt;imageData[currentbyte] = colorbuffer[2];</p>
<p class="src4">texture-&gt;imageData[currentbyte + 1] = colorbuffer[1];</p>
<p class="src4">texture-&gt;imageData[currentbyte + 2] = colorbuffer[0];</p>
<p class="src"></p>
<p class="src4">if(tga.bytesPerPixel == 4)<span class="kom">// 32 bitov� obr�zek?</span></p>
<p class="src4">{</p>
<p class="src5">texture-&gt;imageData[currentbyte + 3] = colorbuffer[3];<span class="kom">// Kop�rov�n� alfy</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">currentbyte += tga.bytesPerPixel;<span class="kom">// Aktualizuje byte</span></p>
<p class="src4">currentpixel++;<span class="kom">// P�esun na dal�� pixel</span></p>
<p class="src"></p>
<p class="src4">if(currentpixel &gt; pixelcount)<span class="kom">// Jsme za hranic� obr�zku?</span></p>
<p class="src4">{</p>
<p class="src5">MessageBox(NULL, &quot;Too many pixels read&quot;, &quot;ERROR&quot;, NULL);</p>
<p class="src"></p>
<p class="src5">if(fTGA != NULL)</p>
<p class="src5">{</p>
<p class="src6">fclose(fTGA);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(colorbuffer != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(colorbuffer);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">if(texture-&gt;imageData != NULL)</p>
<p class="src5">{</p>
<p class="src6">free(texture-&gt;imageData);</p>
<p class="src5">}</p>
<p class="src"></p>
<p class="src5">return false;</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Hlavn� cyklus opakujeme tak dlouho, dokud v souboru zb�vaj� nena�ten� pixely. Po konci loadingu soubor zav�eme a vr�cen�m true indikujeme �sp�ch.</p>

<p class="src1">} while(currentpixel &lt; pixelcount);<span class="kom">// Pokra�uj dokud zb�vaj� pixely</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// P�ekl.: Uvoln�n� dynamick� pam�ti !!!</span></p>
<p class="src1"><span class="kom">// if(colorbuffer != NULL)</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// free(colorbuffer);</span></p>
<p class="src1"><span class="kom">// }</span></p>
<p class="src"></p>
<p class="src1">fclose(fTGA);<span class="kom">// Zav�en� souboru</span></p>
<p class="src1">return true;<span class="kom">// �sp�ch</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pam� dat obr�zku se uvol�uje a� po vytvo�en� textury</span></p>
<p class="src0">}</p>

<p>Nyn� jsou data obr�zku p�ipravena pro vytvo�en� textury a to u� jist� zvl�dnete sami. V tomto tutori�lu n�m �lo p�edev��m o nahr�v�n� TGA obr�zk�. Uk�zkov� demo bylo vytvo�eno jen proto, abyste vid�li, �e k�d opravdu funguje.</p>

<p>A jak je to s �sp�nost� komprimace metody RLE? Je jasn�, �e nejmen�� pam� bude zab�rat obr�zek s rozs�hl�mi plochami stejn�ch pixel� (na ��dc�ch). Pokud chcete ��sla, tak si vezmeme na pomoc obr�zky pou�it� v tomto demu: oba jsou 128x128 pixel� velik�, nekomprimovan� zab�r� na disku 48,0 kB a komprimovan� pouze 5,29 kB. Na obou je sice n�co jin�ho, ale dev�tin�sobn� zmen�en� velikosti mluv� za v�e.</p>

<p class="autor">napsal: Evan Pipho - Terminate <?VypisEmail('terminate@gdnmail.net');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson33.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson33_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson33.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson33.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson33.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Schubert_P@Yahoo.de">Patrick Schubert</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson33.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson33.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(33);?>
<?FceNeHeOkolniLekce(33);?>

<?
include 'p_end.php';
?>
