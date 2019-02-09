<?
$g_title = 'CZ NeHe OpenGL - Lekce 45 - Vertex Buffer Object (VBO)';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(45);?>

<h1>Lekce 45 - Vertex Buffer Object (VBO)</h1>

<p class="nadpis_clanku">Jeden z nejv�t��ch probl�m� jak�koli 3D aplikace je zaji�t�n� jej� rychlosti. V�dy byste m�li limitovat mno�stv� aktu�ln� renderovan�ch polygon� bu� �azen�m, cullingem nebo n�jak�m algoritmem na sni�ov�n� detail�. Kdy� nic z toho nepom�h�, m��ete zkusit nap��klad vertex arrays. Modern� grafick� karty nab�zej� roz���en� nazvan� vertex buffer object, kter� pracuje podobn� jako vertex arrays krom� toho, �e nahr�v� data do vysoce v�konn� pam�ti grafick� karty, a tak podstatn� sni�uje �as pot�ebn� pro rendering. Samoz�ejm� ne v�echny karty tato nov� roz���en� podporuj�, tak�e mus�me implementovat i verzi zalo�enou na vertex arrays.</p>

<div>V tomto tutori�lu budeme</div>
<ul>
<li>nahr�vat data v��kov� mapy</li>
<li>pou��vat vertex arrays k efektivn�mu pos�l�n� dat vertex� do OpenGL</li>
<li>prost�ednictv�m VBO nahr�vat data do pam�ti grafick� karty</li>
</ul>

<p>Jako v�dy nejd��ve nadefinujeme parametry aplikace. Prvn� dv� symbolick� konstanty p�edstavuj� rozli�en� v��kov� mapy a m���tko pro vertik�ln� rozt�hnut� (viz. tutori�l 34 o v��kov�ch map�ch). Kdy� nadefinujete t�et� konstantu, v programu se vypne pou��v�n� VBO... abyste snadno mohli porovnat rychlostn� rozd�l.</p>

<p class="src0"><span class="kom">// Parametry v��kov� mapy</span></p>
<p class="src0">#define MESH_RESOLUTION 4.0f<span class="kom">// Po�et pixel� na vertex</span></p>
<p class="src0">#define MESH_HEIGHTSCALE 1.0f<span class="kom">// M���tko vyv��en�</span></p>
<p class="src0"><span class="kom">//#define NO_VBOS// Vyp�n� VBO</span></p>

<p>K definic�m tak� mus�me p�idat konstanty, datov� typy a ukazatele na funkce pro VBO roz���en�. Zahrnul jsem jen parametry nutn� pro toto demo. Pokud pot�ebujete v�ce funkcionality, doporu�uji z <?OdkazBlank('http://www.opengl.org/');?> st�hnout nejnov�j�� glext.h a pou��t definice obsa�en� v n�m. Pro k�d to jist� bude �ist�j�� metoda.</p>

<p class="src0"><span class="kom">// Roz���en� VBO z glext.h</span></p>
<p class="src0">#define GL_ARRAY_BUFFER_ARB 0x8892</p>
<p class="src0">#define GL_STATIC_DRAW_ARB 0x88E4</p>
<p class="src0">typedef void (APIENTRY * PFNGLBINDBUFFERARBPROC) (GLenum target, GLuint buffer);</p>
<p class="src0">typedef void (APIENTRY * PFNGLDELETEBUFFERSARBPROC) (GLsizei n, const GLuint *buffers);</p>
<p class="src0">typedef void (APIENTRY * PFNGLGENBUFFERSARBPROC) (GLsizei n, GLuint *buffers);</p>
<p class="src0">typedef void (APIENTRY * PFNGLBUFFERDATAARBPROC) (GLenum target, int size, const GLvoid *data, GLenum usage);</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Ukazatele na funkce pro VBO</span></p>
<p class="src0">PFNGLGENBUFFERSARBPROC glGenBuffersARB = NULL;<span class="kom">// Generov�n� VBO jm�na</span></p>
<p class="src0">PFNGLBINDBUFFERARBPROC glBindBufferARB = NULL;<span class="kom">// Zvolen� VBO bufferu</span></p>
<p class="src0">PFNGLBUFFERDATAARBPROC glBufferDataARB = NULL;<span class="kom">// Nahr�v�n� dat VBO</span></p>
<p class="src0">PFNGLDELETEBUFFERSARBPROC glDeleteBuffersARB = NULL;<span class="kom">// Maz�n� VBO</span></p>

<p>Deklarujeme jednoduch� t��dy vertexu a texturov�ch koordin�t�. CMesh je kompletn� t��dou, kter� m��e zapouzd�it z�kladn� data meshe. V na�em p��pad� se jedn� o v��kovou mapu. K�d vysv�tluje s�m sebe, v�imn�te si akor�t, �e data vertex� jsou odd�len� od texturov�ch koordin�t� do vlastn�ho pole. Jak bude vysv�tleno d�le, nen� to �pln� nutn�.</p>

<p class="src0">class CVert<span class="kom">// T��da vertexu</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">float x;</p>
<p class="src1">float y;</p>
<p class="src1">float z;</p>
<p class="src0">};</p>
<p class="src0">typedef CVert CVec;<span class="kom">// Definice jsou synonymn�</span></p>
<p class="src"></p>
<p class="src0">class CTexCoord<span class="kom">// T��da texturov�ch koordin�t�</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">float u;</p>
<p class="src1">float v;</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">class CMesh<span class="kom">// T��da meshe (v��kov� mapy)</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">int m_nVertexCount;<span class="kom">// Po�et vertex�</span></p>
<p class="src1">CVert* m_pVertices;<span class="kom">// Sou�adnice vertex�</span></p>
<p class="src1">CTexCoord* m_pTexCoords;<span class="kom">// Texturov� koordin�ty</span></p>
<p class="src1">unsigned int m_nTextureId;<span class="kom">// ID textury</span></p>
<p class="src"></p>
<p class="src1">unsigned int m_nVBOVertices;<span class="kom">// Jm�no (ID) VBO pro vertexy</span></p>
<p class="src1">unsigned int m_nVBOTexCoords;<span class="kom">// Jm�no (ID) VBO pro texturov� koordin�ty</span></p>
<p class="src"></p>
<p class="src1">AUX_RGBImageRec* m_pTextureImage;<span class="kom">// Data v��kov� mapy</span></p>
<p class="src"></p>
<p class="src0">public:</p>
<p class="src1">CMesh();<span class="kom">// Konstruktor</span></p>
<p class="src1">~CMesh();<span class="kom">// Destruktor</span></p>
<p class="src"></p>
<p class="src1">bool LoadHeightmap(char* szPath, float flHeightScale, float flResolution);<span class="kom">// Loading v��kov� mapy</span></p>
<p class="src1">float PtHeight(int nX, int nY);<span class="kom">// Hodnota na indexu v��kov� mapy</span></p>
<p class="src1">void BuildVBOs();<span class="kom">// Vytvo�en� VBO</span></p>
<p class="src0">};</p>

<p>Glob�ln� prom�nn� g_bVBOSupported indikuje podporu VBO ze strany grafick� karty. Nastav�me ji v inicializa�n�m k�du. G_pMesh bude ukl�dat data v��kov� mapy a g_flYRot ur�uje �hel nato�en� sc�ny. Prom�nn� g_nFPS bude obsahovat po�et sn�mk� za sekundu a g_nFrames je ��ta� jednotliv�ch sn�mk�. Posledn� prom�nn� ukl�d� �as minul�ho v�po�tu FPS.</p>

<p class="src0">bool g_fVBOSupported = false;<span class="kom">// Flag podpory VBO</span></p>
<p class="src0">CMesh* g_pMesh = NULL;<span class="kom">// Data meshe</span></p>
<p class="src0">float g_flYRot = 0.0f;<span class="kom">// Rotace</span></p>
<p class="src0">int g_nFPS = 0, g_nFrames = 0;<span class="kom">// FPS a ��ta� pro FPS</span></p>
<p class="src0">DWORD g_dwLastFPS = 0;<span class="kom">// �as minul�ho testu FPS</span></p>

<p>Funkce Loadheightmap() nahr�v� data v��kov� mapy. Pro ty z v�s, kte�� o ni�em takov�m je�t� nesly�eli (P�ekl.: v origin�le - kdo �ijete pod sk�lou :-). V��kov� mapa je dvou dimenzion�ln� sada dat, v�t�inou obr�zek, kter� hodnotami jednotliv�ch pixel� specifikuje vertik�ln� v��ku dan� ��sti ter�nu. Existuje mnoho r�zn�ch zp�sob�, jak ji vytvo�it. Moje implementace na��t� t�� kan�lovou RGB bitmapu a ke zji�t�n� v��ky pou��v� v�po�et luminance. V�sledn� hodnota bude d�ky tomu stejn� pro barevn� i �ernob�l� obr�zek. Osobn� doporu�uji �ty�kan�lov� form�t vstupn�ch dat, jako je nap��klad targa (.TGA) obr�zek, u kter�ho alfa kan�l m��e specifikovat v��ku. Nicm�n� pro ��ely tohoto tutori�lu bude dosta�ovat oby�ejn� bitmapa.</p>

<p>Ujist�me se, �e soubor obr�zku existuje a pokud ano, loadujeme ho pomoc� knihovny glaux. V�m, existuj� mnohem lep�� cesty nahr�v�n� obr�zk�...</p>

<p class="src0">bool CMesh::LoadHeightmap(char* szPath, float flHeightScale, float flResolution)</p>
<p class="src0">{</p>
<p class="src1">FILE* fTest = fopen(szPath, &quot;r&quot;);<span class="kom">// Otev�en� pro �ten�</span></p>
<p class="src"></p>
<p class="src1">if (!fTest)</p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">fclose(fTest);<span class="kom">// Uvoln� handle</span></p>
<p class="src"></p>
<p class="src1">m_pTextureImage = auxDIBImageLoad(szPath);<span class="kom">// Nahraje obr�zek</span></p>

<p>V�ci za��naj� b�t trochu zaj�mav�j��. Ze v�eho nejd��ve bych cht�l pouk�zat, �e pro ka�d� troj�heln�k generuji t�i vertexy - jednotliv� body nejsou sd�len�. M�li byste to v�d�t u� p�ed na��t�n�m.</p>

<p>Abychom mohli alokovat pam� pro data, pot�ebujeme zn�t jej� velikost. V�po�et je celkem jednoduch� ((���ka ter�nu / rozli�en�) * (d�lka ter�nu / rozli�en�) * 3 vertexy na troj�heln�k * 2 troj�heln�ky na �tverec). alokujeme pam� pro vertexy i texturov� koordin�ty, deklarujeme pomocn� prom�nn� a ve t�ech vno�en�ch cyklech nastav�me ob� pole.</p>

<p class="src1"><span class="kom">// Generov�n� pole vertex�</span></p>
<p class="src1">m_nVertexCount = (int)(m_pTextureImage-&gt;sizeX * m_pTextureImage-&gt;sizeY * 6 / (flResolution * flResolution));</p>
<p class="src"></p>
<p class="src1">m_pVertices = new CVec[m_nVertexCount];<span class="kom">// Alokace pam�ti</span></p>
<p class="src1">m_pTexCoords = new CTexCoord[m_nVertexCount];</p>
<p class="src"></p>
<p class="src1">int nX, nZ, nTri, nIndex = 0;<span class="kom">// Pomocn�</span></p>
<p class="src1">float flX, flZ;</p>
<p class="src"></p>
<p class="src1">for (nZ = 0; nZ &lt; m_pTextureImage-&gt;sizeY; nZ += (int)flResolution)</p>
<p class="src1">{</p>
<p class="src2">for (nX = 0; nX &lt; m_pTextureImage-&gt;sizeX; nX += (int)flResolution)</p>
<p class="src2">{</p>
<p class="src3">for (nTri = 0; nTri &lt; 6; nTri++)</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// V�po�et x a z pozice bodu</span></p>
<p class="src4">flX = (float)nX + ((nTri == 1 || nTri == 2 || nTri == 5) ? flResolution : 0.0f);</p>
<p class="src4">flZ = (float)nZ + ((nTri == 2 || nTri == 4 || nTri == 5) ? flResolution : 0.0f);</p>
<p class="src"></p>
<p class="src4"><span class="kom">// Nastaven� vertexu v poli</span></p>
<p class="src4">m_pVertices[nIndex].x = flX - (m_pTextureImage-&gt;sizeX / 2);</p>
<p class="src4">m_pVertices[nIndex].y = PtHeight((int)flX, (int)flZ) * flHeightScale;</p>
<p class="src4">m_pVertices[nIndex].z = flZ - (m_pTextureImage-&gt;sizeY / 2);</p>
<p class="src"></p>
<p class="src4"><span class="kom">// Nastaven� texturov�ch koordin�t� v poli</span></p>
<p class="src4">m_pTexCoords[nIndex].u = flX / m_pTextureImage-&gt;sizeX;</p>
<p class="src4">m_pTexCoords[nIndex].v = flZ / m_pTextureImage-&gt;sizeY;</p>
<p class="src"></p>
<p class="src4">nIndex++;<span class="kom">// Inkrementace indexu</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Z obr�zku v��kov� mapy vytvo��me OpenGL texturu a potom uvoln�me jeho pam�.</p>

<p class="src1">glGenTextures(1, &amp;m_nTextureId);<span class="kom">// OpenGL ID</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, m_nTextureId);<span class="kom">// Zvol� texturu</span></p>
<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, 3, m_pTextureImage-&gt;sizeX, m_pTextureImage-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, m_pTextureImage-&gt;data);<span class="kom">// Nahraje texturu do OpenGL</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);<span class="kom">// Line�rn� filtrov�n�</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src"></p>
<p class="src1">if (m_pTextureImage)<span class="kom">// Uvoln�n� pam�ti</span></p>
<p class="src1">{</p>
<p class="src2">if (m_pTextureImage-&gt;data)</p>
<p class="src2">{</p>
<p class="src3">free(m_pTextureImage-&gt;data);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">free(m_pTextureImage);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return true;</p>
<p class="src0">}</p>

<p>Funkce PtHeight() vypo��t� index do pole s daty, p�itom o�et�� p��stup do nealokovan� pam�ti a vr�t� v��ku na dan�m indexu. Aby mohl b�t obr�zek barevn� i �ernob�l�, pou�ijeme vzorec pro luminanci. Opravdu nic slo�it�ho.</p>

<p class="src0">float CMesh::PtHeight(int nX, int nY)<span class="kom">// V��ka na indexu</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// V�po�et pozice v poli, o�et�en� p�ete�en�</span></p>
<p class="src1">int nPos = ((nX % m_pTextureImage-&gt;sizeX) + ((nY % m_pTextureImage-&gt;sizeY) * m_pTextureImage-&gt;sizeX)) * 3;</p>
<p class="src"></p>
<p class="src1">float flR = (float)m_pTextureImage-&gt;data[nPos];<span class="kom">// Grabov�n� slo�ek barvy</span></p>
<p class="src1">float flG = (float)m_pTextureImage-&gt;data[nPos + 1];</p>
<p class="src1">float flB = (float)m_pTextureImage-&gt;data[nPos + 2];</p>
<p class="src"></p>
<p class="src1">return (0.299f * flR + 0.587f * flG + 0.114f * flB);<span class="kom">// V�po�et luminance</span></p>
<p class="src0">}</p>

<p>V n�sleduj�c� funkci za�neme kone�n� pracovat s vertex arrays a VBO. Tak�e, co to jsou pole vertex�? V z�kladu je to syst�m, d�ky kter�mu m��eme uk�zat OpenGL na pole geometrick�ch dat a potom je n�kolika m�lo p��kazy vykreslit. V�sledkem je, �e odpadaj� spousty v�skyt� funkc� typu glVertex3f() a jin�ch, kter� sv�m mnohon�sobn�m vol�n�m zbyte�n� zpomaluj� rendering. Syst�m vertex buffer object (VBO) jde je�t� d�le, nam�sto standardn� pam�ti aplikace alokovan� v RAM pou��v� vysoce v�konnou pam� grafick� karty. �as renderingu se zkracuje tak� proto, �e data nemus� putovat &quot;po cel�m po��ta�i&quot;, ale jsou ulo�ena p��mo na za��zen�, kde se pou��vaj�.</p>

<p>Tak�e te� se chyst�me vytvo�it Vertex Buffer Object. Pro tuto operaci existuje n�kolik mo�n�ch zp�sob� realizace, jeden z nich se naz�v� &quot;mapov�n�&quot; pam�ti. Mysl�m, �e na tomto m�st� bude nejlep�� j�t tou nejsnadn�j�� cestou. Nejprve pomoc� glGenBuffersARB() z�sk�me validn� jm�no VBO. Je to vlastn� ��slo ID, kter� OpenGL asociuje s na�imi daty. D�le, podobn� jako u textur, mus�me VBO nastavit jako aktivn�, �ili ��ct OpenGL, �e s n�m chceme pracovat. K tomu slou�� funkce glBindBufferARB(). Nakonec nahrajeme data do grafick� karty. Funkci se p�ed�v� velikost dat v bytech a ukazatel na n�. Proto�e u� po t�to operaci nebudou pot�eba, m��eme je smazat z RAM.</p>

<p class="src0">void CMesh::BuildVBOs()<span class="kom">// Vytvo�en� VBO</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// VBO pro vertexy</span></p>
<p class="src1">glGenBuffersARB(1, &amp;m_nVBOVertices);<span class="kom">// Z�sk�n� jm�na (ID)</span></p>
<p class="src1">glBindBufferARB(GL_ARRAY_BUFFER_ARB, m_nVBOVertices);<span class="kom">// Zvolen� bufferu</span></p>
<p class="src1">glBufferDataARB(GL_ARRAY_BUFFER_ARB, m_nVertexCount * 3 * sizeof(float), m_pVertices, GL_STATIC_DRAW_ARB);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// VBO pro texturov� koordin�ty</span></p>
<p class="src1">glGenBuffersARB(1, &amp;m_nVBOTexCoords);<span class="kom">// Z�sk�n� jm�na (ID)</span></p>
<p class="src1">glBindBufferARB(GL_ARRAY_BUFFER_ARB, m_nVBOTexCoords);<span class="kom">// Zvolen� bufferu</span></p>
<p class="src1">glBufferDataARB(GL_ARRAY_BUFFER_ARB, m_nVertexCount * 2 * sizeof(float), m_pTexCoords, GL_STATIC_DRAW_ARB);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Data v RAM u� jsou zbyte�n�</span></p>
<p class="src1">delete [] m_pVertices;</p>
<p class="src1">delete [] m_pTexCoords;</p>
<p class="src1">m_pVertices = NULL;</p>
<p class="src1">m_pTexCoords = NULL;</p>
<p class="src0">}</p>

<p>Tak to bychom m�li, te� je �as na inicializaci. Vytvo��me dynamick� objekt v��kov� mapy a pokus�me se ji vygenerovat ze souboru terrain.bmp. Nen�-li nadefinovan� symbolick� konstanta NO_VBOS, zjist�me, jestli grafick� karta podporuje roz���en� GL_ARB_vertex_buffer_object. Pokud ano, pomoc� wglGetProcAddress() nagrabujeme ukazatele na pot�ebn� funkce a vytvo��me VBO. V�imn�te si, �e se ve funkci BuildVBOs() ma�ou data v��kov� mapy, kter� se vol� pouze, pokud je VBO podporov�no.</p>

<p class="src0">BOOL Initialize(GL_Window* window, Keys* keys)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;</p>
<p class="src1">g_keys = keys;</p>
<p class="src"></p>
<p class="src1">g_pMesh = new CMesh();<span class="kom">// Instance v��kov� mapy</span></p>
<p class="src"></p>
<p class="src1">if(!g_pMesh-&gt;LoadHeightmap(&quot;terrain.bmp&quot;, MESH_HEIGHTSCALE, MESH_RESOLUTION))<span class="kom">// Nahr�n�</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Error Loading Heightmap&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0">#ifndef NO_VBOS</p>
<p class="src1">g_fVBOSupported = IsExtensionSupported(&quot;GL_ARB_vertex_buffer_object&quot;);<span class="kom">// Test podpory VBO</span></p>
<p class="src"></p>
<p class="src1">if(g_fVBOSupported)<span class="kom">// Je roz���en� podporov�no?</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Ukazatele na GL funkce</span></p>
<p class="src2">glGenBuffersARB = (PFNGLGENBUFFERSARBPROC) wglGetProcAddress(&quot;glGenBuffersARB&quot;);</p>
<p class="src2">glBindBufferARB = (PFNGLBINDBUFFERARBPROC) wglGetProcAddress(&quot;glBindBufferARB&quot;);</p>
<p class="src2">glBufferDataARB = (PFNGLBUFFERDATAARBPROC) wglGetProcAddress(&quot;glBufferDataARB&quot;);</p>
<p class="src2">glDeleteBuffersARB = (PFNGLDELETEBUFFERSARBPROC) wglGetProcAddress(&quot;glDeleteBuffersARB&quot;);</p>
<p class="src"></p>
<p class="src2">g_pMesh-&gt;BuildVBOs();<span class="kom">// Poslat data vertex� do pam�ti grafick� karty</span></p>
<p class="src1">}</p>
<p class="src0">#else</p>
<p class="src1">g_fVBOSupported = false;<span class="kom">// Bez VBO</span></p>
<p class="src0">#endif</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Klasick� nastaven� OpenGL</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);</p>
<p class="src1">glClearDepth(1.0f);</p>
<p class="src1">glDepthFunc(GL_LEQUAL);</p>
<p class="src1">glEnable(GL_DEPTH_TEST);</p>
<p class="src1">glShadeModel(GL_SMOOTH);</p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);</p>
<p class="src1">glEnable(GL_TEXTURE_2D);</p>
<p class="src1">glColor4f(1.0f, 1.0f, 1.0f, 1.0f);</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace �sp�n�</span></p>
<p class="src0">}</p>

<p>Funkci IsExtensionSupported(), kter� zji��uje podporu roz���en�, m��ete z�skat na OpenGL.org, ale moje varianta je o trochu �ist��. N�kte�� lid� sice pomoc� strstr() hledaj� pouze p��tomnost pod�et�zce v �et�zci, nicm�n� zd� se, �e OpenGL.org moc ned�v��uje konzistentnosti �et�zce s roz���en�mi.</p>

<p class="src0">bool IsExtensionSupported(char* szTargetExtension)<span class="kom">// Je roz���en� podporov�no?</span></p>
<p class="src0">{</p>
<p class="src1">const unsigned char *pszExtensions = NULL;</p>
<p class="src1">const unsigned char *pszStart;</p>
<p class="src1">unsigned char *pszWhere, *pszTerminator;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Jm�no by nem�lo m�t mezery</span></p>
<p class="src1">pszWhere = (unsigned char *)strchr(szTargetExtension, ' ');</p>
<p class="src"></p>
<p class="src1">if (pszWhere || *szTargetExtension == '\0')</p>
<p class="src1">{</p>
<p class="src2">return false;<span class="kom">// Nepodporov�no</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">pszExtensions = glGetString(GL_EXTENSIONS);<span class="kom">// �et�zec s n�zvy roz���en�</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vyhled�v�n� pod�et�zce se jm�nem roz���en�</span></p>
<p class="src1">pszStart = pszExtensions;</p>
<p class="src"></p>
<p class="src1">for (;;)</p>
<p class="src1">{</p>
<p class="src2">pszWhere = (unsigned char *) strstr((const char *) pszStart, szTargetExtension);</p>
<p class="src"></p>
<p class="src2">if (!pszWhere)</p>
<p class="src2">{</p>
<p class="src3">break;</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">pszTerminator = pszWhere + strlen(szTargetExtension);</p>
<p class="src"></p>
<p class="src2">if (pszWhere == pszStart || *(pszWhere - 1) == ' ')</p>
<p class="src2">{</p>
<p class="src3">if (*pszTerminator == ' ' || *pszTerminator == '\0')</p>
<p class="src3">{</p>
<p class="src4">return true;<span class="kom">// Podporov�no</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">pszStart = pszTerminator;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return false;<span class="kom">// Nepodporov�no</span></p>
<p class="src0">}</p>

<p>V�t�ina v�c� je u� hotov�, zb�v� vykreslov�n�.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);</p>
<p class="src1">glLoadIdentity();</p>

<p>Existuje n�kolik mo�nost�, jak z�skat FPS. Asi nejjednodu��� je ��tat po dobu jedn� sekundy pr�chody vykreslovac� funkc�.</p>

<p class="src1"><span class="kom">// Z�sk�n� FPS</span></p>
<p class="src1">if(GetTickCount() - g_dwLastFPS &gt;= 1000)<span class="kom">// Ub�hla sekunda?</span></p>
<p class="src1">{</p>
<p class="src2">g_dwLastFPS = GetTickCount();<span class="kom">// Aktualizace �asu pro dal�� m��en�</span></p>
<p class="src2">g_nFPS = g_nFrames;<span class="kom">// Ulo�en� FPS</span></p>
<p class="src2">g_nFrames = 0;<span class="kom">// Reset ��ta�e</span></p>
<p class="src"></p>
<p class="src2">char szTitle[256] = {0};<span class="kom">// �et�zec titulku okna</span></p>
<p class="src2">sprintf(szTitle, &quot;Lesson 45: NeHe &amp; Paul Frazee's VBO Tut - %d Triangles, %d FPS&quot;, g_pMesh-&gt;m_nVertexCount / 3, g_nFPS);</p>
<p class="src"></p>
<p class="src2">if(g_fVBOSupported)<span class="kom">// Pou��v�/nepou��v� VBO</span></p>
<p class="src2">{</p>
<p class="src3">strcat(szTitle, &quot;, Using VBOs&quot;);</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">strcat(szTitle, &quot;, Not Using VBOs&quot;);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">SetWindowText(g_window-&gt;hWnd, szTitle);<span class="kom">// Nastav� titulek</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">g_nFrames++;<span class="kom">// Inkrementace ��ta�e FPS</span></p>

<p>P�esuneme kameru nad ter�n a nato��me sc�nu okolo osy y. Prom�nnou g_flYRot inkrementujeme ve funkci Update().</p>

<p class="src1">glTranslatef(0.0f, -220.0f, 0.0f);<span class="kom">// P�esun nad ter�n</span></p>
<p class="src1">glRotatef(10.0f, 1.0f, 0.0f, 0.0f);<span class="kom">// Naklon�n� kamery</span></p>
<p class="src1">glRotatef(g_flYRot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace kamery</span></p>

<p>Abychom mohli pracovat s vertex arrays (a tak� VBO), mus�me zapnout GL_VERTEX_ARRAY a GL_TEXTURE_COORD_ARRAY. Proto�e m�me pouze jednu v��kovou mapu, nemuseli bychom to d�lat po ka�d�, ale b�v� to dobr�m zvykem.</p>

<p class="src1">glEnableClientState(GL_VERTEX_ARRAY);<span class="kom">// Zapne vertex arrays</span></p>
<p class="src1">glEnableClientState(GL_TEXTURE_COORD_ARRAY);<span class="kom">// Zapne texture coord arrays</span></p>

<p>D�le mus�me specifikovat pole, ve kter�ch m� OpenGL hledat data. Za�nu nejprve vertex arrays (��st else), proto�e jsou jednodu���. V�e, co pot�ebujeme ud�lat, je zavol�n� funkce glVertexPointer(), kter� se p�ed�v� po�et prvk� na jeden vertex (2, 3 nebo 4), typ dat, prokl�d�n� (v p��pad�, �e nejsou vertexy v samostatn� struktu�e) a ukazatel na pole. To sam� plat� i pro texturov� koordin�ty, ale maj� svoji vlastn� funkci. Tak� bychom mohli ulo�it v�echna data do jednoho velk�ho pam�ov�ho bufferu a pou��t glInterleavedArrays(), ale nech�me je odd�len�, abyste vid�li, jak pou��t v�ce VBO najednou.</p>

<p>Jedin� rozd�l mezi vertex arrays a VBO je na tomto m�st� pouze v tom, �e u VBO zavol�me glBindBufferARB() a do gl*Pointer() p�ed�me m�sto ukazatele hodnotu NULL.</p>

<p class="src1">if(g_fVBOSupported)<span class="kom">// Podporuje grafick� karta VBO?</span></p>
<p class="src1">{</p>
<p class="src2">glBindBufferARB(GL_ARRAY_BUFFER_ARB, g_pMesh-&gt;m_nVBOVertices);</p>
<p class="src2">glVertexPointer(3, GL_FLOAT, 0, (char *) NULL);<span class="kom">// P�edat NULL</span></p>
<p class="src2">glBindBufferARB(GL_ARRAY_BUFFER_ARB, g_pMesh-&gt;m_nVBOTexCoords);</p>
<p class="src2">glTexCoordPointer(2, GL_FLOAT, 0, (char *) NULL);<span class="kom">// P�edat NULL</span></p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Oby�ejn� vertex arrays</span></p>
<p class="src1">{</p>
<p class="src2">glVertexPointer(3, GL_FLOAT, 0, g_pMesh-&gt;m_pVertices);<span class="kom">// Ukazatel na data vertex�</span></p>
<p class="src2">glTexCoordPointer(2, GL_FLOAT, 0, g_pMesh-&gt;m_pTexCoords);<span class="kom">// Ukazatel na texturov� koordin�ty</span></p>
<p class="src1">}</p>

<p>Samotn� rendering je je�t� snaz��. Pomoc� glDrawArrays() �ekneme OpenGL, aby vykreslil troj�heln�ky ve form�tu GL_TRIANGLES. Jako po��te�n� index v poli p�ed�me nulu, celkov� po�et vertex� by m�l b�t jasn�. Funkce pomoc� client state sama detekuje, co v�echno m� p�i renderingu pou��t (textury, sv�tlo...). Existuje mnohem v�ce zp�sob�, jak poslat data OpenGL. Jako p��klad uvedu glArrayElement(), ale na�e verze je ze v�ech nejrychlej��. V�imn�te si tak�, �e nespecifikujeme ��dn� glBegin() a glEnd(). Zde nejsou nutn�.</p>

<p>Funkce glDrawArrays() je tak� d�vodem, pro� jsem zvolil nesd�let jeden vertex mezi n�kolika troj�heln�ky - nen� to mo�n�. co v�m, nejlep�� cestou, jak optimalizovat pam�ov� n�roky, je pou��t triangle strip. V p��pad� sv�tel byste m�li zajistit, aby m�l k sob� ka�d� vertex odpov�daj�c� norm�lov� vektor. Je to sice nutnost, bez kter� by tato funkce nefungovala, na druhou stranu se v�ak obrovsky zlep�� vzhled renderovan�ho objektu.</p>

<p class="src1">glDrawArrays(GL_TRIANGLES, 0, g_pMesh-&gt;m_nVertexCount);<span class="kom">// Vykreslen� vertex�</span></p>

<p>Zb�v� vypnout client state a m�me hotovo.</p>

<p class="src1">glDisableClientState(GL_VERTEX_ARRAY);<span class="kom">// Vypne vertex arrays</span></p>
<p class="src1">glDisableClientState(GL_TEXTURE_COORD_ARRAY);<span class="kom">// Vypne texture coord arrays</span></p>
<p class="src0">}</p>

<p>Pokud byste cht�li z�skat v�ce informac� o vertex buffer object, doporu�uji prostudovat si dokumentaci ve SGI registru roz���en� (SGI's extensions registry) na <?OdkazBlank('http://oss.sgi.com/projects/ogl-sample/registry/');?>. Je to sice trochu t쾹� �ten� ne� tento tutori�l, ale budete zn�t mnohem v�ce mo�nost� implementace a detail�.</p>

<p class="autor">napsal: Paul Frazee <?VypisEmail('paulfrazee@cox.net');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson45.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson45_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson45.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson45.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:gery.buchgraber@gmx.de">Gerald Buchgraber</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson45.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(45);?>
<?FceNeHeOkolniLekce(45);?>

<?
include 'p_end.php';
?>
