<?
$g_title = 'CZ NeHe OpenGL - Lekce 31 - Nahr�v�n� a renderov�n� model�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(31);?>

<h1>Lekce 31 - Nahr�v�n� a renderov�n� model�</h1>

<p class="nadpis_clanku">Dal�� skv�l� tutori�l! Nau��te se, jak nahr�t a zobrazit otexturovan� Milkshape3D model. Nezd� se to, ale asi nejv�ce se budou hodit znalosti o pr�ci s dynamickou pam�t� a jej�m kop�rov�n� z jednoho m�sta na druh�.</p>

<p>Zdrojov� k�d tohoto projektu byl vyjmut z PortaLib3D, knihovny, kterou jsem napsal, abych lidem umo�nil zobrazovat modely za pou�it� velmi mal�ho mno�stv� dal��ho k�du. Abyste se na ni mohli opravdu spolehnout mus�te nejd��ve v�d�t, co d�l� a jak pracuje.</p>

<p>��st PortaLib3D, uveden� zde, si st�le zachov�v� m�j copyright. To neznamen�, �e ji nesm�te pou��vat, ale �e p�i vlo�en� k�du do sv�ho projektu mus�te uv�st n�le�it� credit. To je v�e - ��dn� velk� n�roky. Pokud byste cht�li ��st, pochopit a re-implementovat cel� k�d (��dn� kop�rovat vlo�it!), budete uvoln�ni ze sv� povinnosti. Pak je to v� v�tvor. Poj�me se ale pod�vat na n�co zaj�mav�j��ho.</p>

<p>Model, kter� pou��v�me v tomto projektu, poch�z� z Milkshape3D. Je to opravdu kvalitn� bal�k pro modelov�n�, kter� zahrnuje vlastn� file-form�t. M�m dal��m pl�nem je implementovat Anim8or (<?OdkazBlank('http://www.anim8or.com/');?>), souborov� reader. Je free a um� ��st samoz�ejm� i 3DS. Nicm�n� form�t souboru nen� t�m hlavn�m pro loading model�. Nejd��ve se mus� vytvo�it vlastn� struktury, kter� jsou schopny pojmout data.</p>

<p>Prvn� ze v�eho deklarujeme obecnou t��du Model, kter� je kontejnerem pro v�echna data.</p>

<p class="src0">class Model<span class="kom">// Obecn� �lo�i�t� dat (abstraktn� t��da)</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>

<p>Ze v�eho nejd�le�it�j�� jsou samoz�ejm� vertexy. Pole t�� desetinn�ch hodnot m_location reprezentuje jednotliv� x, y, z sou�adnice. Prom�nnou m_boneID budeme v tomto tutori�lu ignorovat. Jej� �as p�ijde a� v dal��m p�i kostern� animaci.</p>

<p class="src1">struct Vertex<span class="kom">// Struktura vertexu</span></p>
<p class="src1">{</p>
<p class="src2">float m_location[3];<span class="kom">// X, y, z sou�adnice</span></p>
<p class="src2">char m_boneID;<span class="kom">// Pro skelet�ln� animaci</span></p>
<p class="src1">};</p>

<p>V�echny vertexy pot�ebujeme seskupit do troj�heln�k�. Pole m_vertexIndices obsahuje t�i indexy do pole vertex�. Touto cestou bude ka�d� vertex ulo�en v pam�ti pouze jednou. V pol�ch m_s a m_t jsou texturov� koordin�ty ka�d�ho vrcholu. Posledn� atribut definuje t�i norm�lov� vektory pro sv�tlo.</p>

<p class="src1">struct Triangle<span class="kom">// Struktura troj�heln�ku</span></p>
<p class="src1">{</p>
<p class="src2">int m_vertexIndices[3];<span class="kom">// T�i indexy do pole vertex�</span></p>
<p class="src2">float m_s[3], m_t[3];<span class="kom">// Texturov� koordin�ty</span></p>
<p class="src2">float m_vertexNormals[3][3];<span class="kom">// T�i norm�lov� vektory</span></p>
<p class="src1">};</p>

<p>Dal�� struktura popisuje mesh modelu. Mesh je skupina troj�heln�k�, na kter� je aplikov�n stejn� materi�l a textura. Skupiny mesh� dohromady tvo�� cel� model. Stejn� jako troj�heln�ky obsahovaly pouze indexy na vertexy, budou i meshe obsahovat pouze indexy na troj�heln�ky. Proto�e nezn�me jejich p�esn� po�et, mus� b�t pole dynamick�. T�et� prom�nn� je op�t indexem, tentokr�t do materi�l� (textura, osv�tlen�).</p>

<p class="src1">struct Mesh<span class="kom">//Mesh modelu</span></p>
<p class="src1">{</p>
<p class="src2">int *m_pTriangleIndices;<span class="kom">// Indexy do troj�heln�k�</span></p>
<p class="src2">int m_numTriangles;<span class="kom">// Po�et troj�heln�k�</span></p>
<p class="src2">int m_materialIndex;<span class="kom">// Index do materi�l�</span></p>
<p class="src1">};</p>

<p>Ve struktu�e Material jsou ulo�en� standardn� koeficienty sv�tla, ve stejn�m form�tu jako pou��v� OpenGL: okoln� (ambient), rozpt�len� (diffuse), odra�en� (specular), vyza�uj�c� (emissive) a lesklost (shininess). d�le obsahuje objekt textury a souborovou cestu k textu�e, aby mohla b�t znovu nahr�na, kdy� je ukon�en kontext OpenGL.</p>

<p class="src1">struct Material<span class="kom">// Vlastnosti materi�l�</span></p>
<p class="src1">{</p>
<p class="src2">float m_ambient[4], m_diffuse[4], m_specular[4], m_emissive[4];<span class="kom">// Reakce materi�lu na sv�tlo</span></p>
<p class="src2">float m_shininess;<span class="kom">// Lesk materi�lu</span></p>
<p class="src2">GLuint m_texture;<span class="kom">// Textura</span></p>
<p class="src2">char *m_pTextureFilename;<span class="kom">// Souborov� cesta k textu�e</span></p>
<p class="src1">};</p>

<p>Vytvo��me prom�nn� pr�v� napsan�ch struktur ve form� ukazatel� na dynamick� pole, jejich� pam� alokuje funkce pro loading objekt�. Mus�me samoz�ejm� ukl�dat i velikost pol�.</p>

<p class="src0">protected:</p>
<p class="src1">int m_numVertices;<span class="kom">// Po�et vertex�</span></p>
<p class="src1">Vertex *m_pVertices;<span class="kom">// Dynamick� pole vertex�</span></p>
<p class="src"></p>
<p class="src1">int m_numTriangles;<span class="kom">// Po�et troj�heln�k�</span></p>
<p class="src1">Triangle *m_pTriangles;<span class="kom">// Dynamick� pole troj�heln�k�</span></p>
<p class="src"></p>
<p class="src1">int m_numMeshes;<span class="kom">// Po�et mesh�</span></p>
<p class="src1">Mesh *m_pMeshes;<span class="kom">// Dynamick� pole mesh�</span></p>
<p class="src"></p>
<p class="src1">int m_numMaterials;<span class="kom">// Po�et materi�l�</span></p>
<p class="src1">Material *m_pMaterials;<span class="kom">// Dynamick� pole materi�l�</span></p>

<p>A kone�n� metody t��dy. Virtu�ln� �lensk� funkce loadModelData() m� za �kol nahr�t data ze souboru. P�i�ad�me j� nulu, aby nemohl b�t vytvo�en objekt t��dy (abstraktn� t��da). Tato t��da je zam��lena pouze jako �lo�i�t� dat. V�echny operace pro nahr�v�n� maj� na starosti odvozen� t��dy, kdy ka�d� z nich um� sv�j vlastn� form�t souboru. Cel� hierarchie je v�ce obecn�.</p>

<p class="src0">public:</p>
<p class="src1">Model();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~Model();<span class="kom">// Destruktor</span></p>
<p class="src"></p>
<p class="src1">virtual bool loadModelData(const char *filename) = 0;<span class="kom">// Loading objektu ze souboru</span></p>

<p>Metoda reloadTextures() slou�� pro loading textur a jejich znovunahr�v�n�, kdy� se ztrat� kontext OpenGL (nap�. p�i p�epnut� z/do fullscreenu). Draw() vykresluje objekt. Tato funkce nemus� b�t virtu�ln�, proto�e defakto zn�me v�echny pot�ebn� informace o struktu�e objektu (vertexy, troj�heln�ky...).</p>

<p class="src1">void reloadTextures();<span class="kom">// Znovunahr�n� textur</span></p>
<p class="src1">void draw();<span class="kom">// Vykreslen� objektu</span></p>
<p class="src0">};</p>

<p>Od t��dy Model pod�d�me t��du MilkshapeModel. P�ep�eme v n� metodu loadModelData().</p>

<p class="src0">class MilkshapeModel : public Model</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">MilkshapeModel();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~MilkshapeModel();<span class="kom">// Destruktor</span></p>
<p class="src"></p>
<p class="src1">virtual bool loadModelData(const char *filename);<span class="kom">// Loading objektu ze souboru</span></p>
<p class="src0">};</p>

<p>Nyn� nahr�v�n� objekt�. P�ep�eme virtu�ln� funkci loadModelData() abstraktn� t��dy Model tak, aby ve t��d� MilkShapeModel nahr�vala data ze souboru ve form�tu Milkshape3D. P�ed�v�me j� �et�zec se jm�nem souboru. Pokud v�e prob�hne v po��dku, funkce nastav� datov� struktury a vr�t� true.</p>

<p class="src0">bool MilkshapeModel::loadModelData(const char *filename)</p>
<p class="src0">{</p>

<p>Soubor otev�eme jako vstupn� (ios::in), bin�rn� (ios::binary) a nebudeme ho vytv��et (ios::nocreate). Pokud nebyl nalezen vr�t� funkce false, aby indikovala error.</p>

<p class="src1">ifstream inputFile(filename, ios::in | ios::binary | ios::nocreate);<span class="kom">// Otev�en� souboru</span></p>
<p class="src"></p>
<p class="src1">if (inputFile.fail())<span class="kom">// Poda�ilo se ho otev��t?</span></p>
<p class="src2">return false;</p>

<p>Zjist�me velikost souboru v bytech a potom ho cel� na�teme do pomocn�ho bufferu pBuffer.</p>

<p class="src1"><span class="kom">// Velikost souboru</span></p>
<p class="src1">inputFile.seekg(0, ios::end);</p>
<p class="src1">long fileSize = inputFile.tellg();</p>
<p class="src1">inputFile.seekg(0, ios::beg);</p>
<p class="src"></p>
<p class="src1">byte *pBuffer = new byte[fileSize];<span class="kom">// Alokace pam�ti pro kopii souboru</span></p>
<p class="src1">inputFile.read(pBuffer, fileSize);<span class="kom">// Vytvo�en� pam�ov� kopie souboru</span></p>
<p class="src1">inputFile.close();<span class="kom">// Zav�en� souboru</span></p>

<p>Deklarujeme pomocn� ukazatel pPtr, kter� ihned inicializujeme tak, aby ukazoval na stejn� m�sto jako pBuffer, tedy na za��tek pam�ti. Do hlavi�ky souboru pHeader ulo��me adresu hlavi�ky a zv�t��me adresu v pPtr o velikost hlavi�ky.</p>

<p>Pozn.: Strukturu hlavi�ky a j� podobn� jsem na za��tku tutori�lu neuv�d�l, proto�e je budeme pou��vat jenom zde, v t�to funkci. Pokud v�s p�eci zaj�maj�, st�hn�te si zdrojov� k�d. Jsou deklarovan� naho�e v souboru MilkshapeModel.cpp.</p>

<p class="src1">const byte *pPtr = pBuffer;<span class="kom">// Pomocn� ukazatel na kopii souboru</span></p>
<p class="src"></p>
<p class="src1">MS3DHeader *pHeader = (MS3DHeader*)pPtr;<span class="kom">// Ukazatel na hlavi�ku</span></p>
<p class="src1">pPtr += sizeof(MS3DHeader);<span class="kom">// Posun za hlavi�ku</span></p>

<p>Hlavi�ka p��mo specifikuje form�t souboru. Ujist�me se, �e se jedn� o platn� form�t, kter� um�me nahr�t.</p>

<p class="src1"><span class="kom">// Nen� Milkshape3D souborem</span></p>
<p class="src1">if (strncmp(pHeader-&gt;m_ID, &quot;MS3D000000&quot;, 10) != 0)</p>
<p class="src1">{</p>
<p class="src2">delete [] pBuffer;<span class="kom">// P�ekl.: Sma�e kopii souboru !!!!!</span></p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// �patn� verze souboru, t��da podporuje pouze verze 1.3 a 1.4</span></p>
<p class="src1">if (pHeader-&gt;m_version &lt; 3 || pHeader-&gt;m_version &gt; 4)</p>
<p class="src1">{</p>
<p class="src2">delete [] pBuffer;<span class="kom">// P�ekl.: Sma�e kopii souboru !!!!!</span></p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Na�teme v�echny vertexy. Nejd��ve zjist�me jejich po�et, alokujeme pot�ebnou pam� a p�esuneme pPtr na dal�� pozici. V cyklu proch�z�me jednotliv� vertexy. Nastav�me ukazatel pVertex na p�etypovan� pPtr a definujeme m_boneID. Nakonec zavol�me memcpy() pro zkop�rov�n� hodnot a zv�t��me pPtr.</p>

<p class="src1">int nVertices = *(word*)pPtr;<span class="kom">// Po�et vertex�</span></p>
<p class="src"></p>
<p class="src1">m_numVertices = nVertices;<span class="kom">// Nastav� atribut t��dy</span></p>
<p class="src1">m_pVertices = new Vertex[nVertices];<span class="kom">// Alokace pam�ti pro vertexy</span></p>
<p class="src"></p>
<p class="src1">pPtr += sizeof(word);<span class="kom">// Posun za po�et vertex�</span></p>
<p class="src"></p>
<p class="src1">int i;<span class="kom">//Pomocn� prom�nn�</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; nVertices; i++)<span class="kom">// Nahr�v� vertexy</span></p>
<p class="src1">{</p>
<p class="src2">MS3DVertex *pVertex = (MS3DVertex*)pPtr;<span class="kom">// Ukazatel na vertex</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Na�ten� vertexu</span></p>
<p class="src2">m_pVertices[i].m_boneID = pVertex-&gt;m_boneID;</p>
<p class="src2">memcpy(m_pVertices[i].m_location, pVertex-&gt;m_vertex, sizeof(float) * 3);</p>
<p class="src"></p>
<p class="src2">pPtr += sizeof(MS3DVertex);<span class="kom">// Posun za tento vertex</span></p>
<p class="src1">}</p>

<p>Stejn� jako u vertex�, tak i troj�heln�k� nejd��ve provedeme pot�ebn� operace pro alokaci pam�ti. V cyklu proch�z�me jednotliv� troj�heln�ky a inicializujeme je. V�imn�te si, �e v souboru jsou indexy vertex� ulo�eny v poli word hodnot, ale v modelu kv�li konzistentnosti a jednoduchosti pou��v�me datov� typ int. ��slo se implicitn� p�etypuje.</p>

<p class="src1">int nTriangles = *(word*)pPtr;<span class="kom">// Po�et troj�heln�k�</span></p>
<p class="src1">m_numTriangles = nTriangles;<span class="kom">// Nastav� atribut t��dy</span></p>
<p class="src1">m_pTriangles = new Triangle[nTriangles];<span class="kom">// Alokace pam�ti pro troj�heln�ky</span></p>
<p class="src"></p>
<p class="src1">pPtr += sizeof(word);<span class="kom">// Posun za po�et troj�heln�k�</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; nTriangles; i++)<span class="kom">// Na��t� troj�heln�ky</span></p>
<p class="src1">{</p>
<p class="src2">MS3DTriangle *pTriangle = (MS3DTriangle*)pPtr;<span class="kom">// Ukazatel na troj�heln�k</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Na�ten� troj�heln�ku</span></p>
<p class="src2">int vertexIndices[3] = { pTriangle-&gt;m_vertexIndices[0], pTriangle-&gt;m_vertexIndices[1], pTriangle-&gt;m_vertexIndices[2] };</p>

<p>V�echna ��sla v poli t jsou nastavena na 1.0 m�nus origin�l. To proto, �e OpenGL pou��v� po��tek texturovac�ho sou�adnicov�ho syst�mu vlevo dole, narozd�l od Milkshape, kter� ho m� vlevo naho�e. Ode�ten�m od jedni�ky, y sou�adnici invertujeme. V�e ostatn� by m�lo b�t bez probl�m�.</p>

<p class="src2">float t[3] = { 1.0f-pTriangle-&gt;m_t[0], 1.0f-pTriangle-&gt;m_t[1], 1.0f-pTriangle-&gt;m_t[2] };</p>
<p class="src"></p>
<p class="src2">memcpy(m_pTriangles[i].m_vertexNormals, pTriangle-&gt;m_vertexNormals, sizeof(float)*3*3);</p>
<p class="src2">memcpy(m_pTriangles[i].m_s, pTriangle-&gt;m_s, sizeof(float)*3);</p>
<p class="src2">memcpy(m_pTriangles[i].m_t, t, sizeof(float)*3);</p>
<p class="src2">memcpy(m_pTriangles[i].m_vertexIndices, vertexIndices, sizeof(int)*3);</p>
<p class="src"></p>
<p class="src2">pPtr += sizeof(MS3DTriangle);<span class="kom">// Posun za tento troj�heln�k</span></p>
<p class="src1">}</p>

<p>Nahrajeme struktury mesh. V Milkshape3D jsou tak� naz�v�ny groups - skupiny. V ka�d� se li�� po�et troj�heln�k�, tak�e nem��eme na��st ��dnou standardn� strukturu. Nam�sto toho budeme dynamicky alokovat pam� pro indexy troj�heln�k� a v ka�d�m pr�chodu je na��tat.</p>

<p class="src1">int nGroups = *(word*)pPtr;<span class="kom">// Po�et mesh�</span></p>
<p class="src1">m_numMeshes = nGroups;<span class="kom">// Nastav� atribut t��dy</span></p>
<p class="src1">m_pMeshes = new Mesh[nGroups];<span class="kom">// Alokace pam�ti pro meshe</span></p>
<p class="src"></p>
<p class="src1">pPtr += sizeof(word);<span class="kom">// Posun za po�et mesh�</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; nGroups; i++)<span class="kom">// Na��t� meshe</span></p>
<p class="src1">{</p>
<p class="src2">pPtr += sizeof(byte);<span class="kom">// Posun za flagy</span></p>
<p class="src2">pPtr += 32;<span class="kom">// Posun za jm�no</span></p>
<p class="src"></p>
<p class="src2">word nTriangles = *(word*)pPtr;<span class="kom">// Po�et troj�heln�k� v meshi</span></p>
<p class="src2">pPtr += sizeof(word);<span class="kom">// Posun za po�et troj�heln�k�</span></p>
<p class="src"></p>
<p class="src2">int *pTriangleIndices = new int[nTriangles];<span class="kom">// Alokace pam�ti pro indexy troj�heln�k�</span></p>
<p class="src"></p>
<p class="src2">for (int j = 0; j &lt; nTriangles; j++)<span class="kom">// Na��t� indexy troj�heln�k�</span></p>
<p class="src2">{</p>
<p class="src3">pTriangleIndices[j] = *(word*)pPtr;<span class="kom">// P�i�ad� index troj�heln�ku</span></p>
<p class="src3">pPtr += sizeof(word);<span class="kom">// Posun za index troj�heln�ku</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">char materialIndex = *(char*)pPtr;<span class="kom">// Na�te index materi�lu</span></p>
<p class="src"></p>
<p class="src2">pPtr += sizeof(char);<span class="kom">// Posun za index materi�lu</span></p>
<p class="src"></p>
<p class="src2">m_pMeshes[i].m_materialIndex = materialIndex;<span class="kom">// Index materi�lu</span></p>
<p class="src2">m_pMeshes[i].m_numTriangles = nTriangles;<span class="kom">// Po�et troj�heln�k�</span></p>
<p class="src2">m_pMeshes[i].m_pTriangleIndices = pTriangleIndices;<span class="kom">// Indexy troj�heln�k�</span></p>
<p class="src1">}</p>

<p>Posledn�, co na��t�me jsou informace o materi�lech.</p>

<p class="src1">int nMaterials = *(word*)pPtr;<span class="kom">// Po�et materi�l�</span></p>
<p class="src1">m_numMaterials = nMaterials;<span class="kom">// Nastav� atribut t��dy</span></p>
<p class="src1">m_pMaterials = new Material[nMaterials];<span class="kom">// Alokace pam�ti pro materi�ly</span></p>
<p class="src"></p>
<p class="src1">pPtr += sizeof(word);<span class="kom">// Posun za po�et materi�l�</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; nMaterials; i++)<span class="kom">// Proch�z� materi�ly</span></p>
<p class="src1">{</p>
<p class="src2">MS3DMaterial *pMaterial = (MS3DMaterial*)pPtr;<span class="kom">// Ukazatel na materi�l</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Na�te materi�l</span></p>
<p class="src2">memcpy(m_pMaterials[i].m_ambient, pMaterial-&gt;m_ambient, sizeof(float)*4);</p>
<p class="src2">memcpy(m_pMaterials[i].m_diffuse, pMaterial-&gt;m_diffuse, sizeof(float)*4);</p>
<p class="src2">memcpy(m_pMaterials[i].m_specular, pMaterial-&gt;m_specular, sizeof(float)*4);</p>
<p class="src2">memcpy(m_pMaterials[i].m_emissive, pMaterial-&gt;m_emissive, sizeof(float)*4);</p>
<p class="src2">m_pMaterials[i].m_shininess = pMaterial-&gt;m_shininess;</p>

<p>Alokujeme pam� pro �et�zec jm�na souboru textury a zkop�rujeme ho.</p>

<p class="src2"><span class="kom">// Alokace pro jm�no souboru textury</span></p>
<p class="src2">m_pMaterials[i].m_pTextureFilename = new char[strlen(pMaterial-&gt;m_texture)+1];</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Zkop�rov�n� jm�na souboru</span></p>
<p class="src2">strcpy(m_pMaterials[i].m_pTextureFilename, pMaterial-&gt;m_texture);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Posun za materi�l</span></p>
<p class="src2">pPtr += sizeof(MS3DMaterial);</p>
<p class="src1">}</p>

<p>Nakonec loadujeme textury objektu, uvoln�me pam� kopie souboru a vr�t�me true, abychom ozn�mili �sp�ch cel� akce.</p>

<p class="src1">reloadTextures();<span class="kom">// Nahraje textury</span></p>
<p class="src"></p>
<p class="src1">delete [] pBuffer;<span class="kom">// Sma�e kopii souboru</span></p>
<p class="src"></p>
<p class="src1">return true;<span class="kom">// Model byl nahr�n</span></p>
<p class="src0">}</p>

<p>Nyn� jsou �lensk� prom�nn� t��dy Model vypln�n�. Zb�v� je�t� nahr�t textury. V cyklu proch�z�me v�echny materi�ly a testujeme, jestli je �et�zec se jm�nem textury del�� ne� nula. Pokud ano nahrajeme texturu pomoc� standardn� NeHe funkce. Pokud ne p�i�ad�me textu�e nulu jako indikaci, �e neexistuje.</p>

<p class="src0">void Model::reloadTextures()<span class="kom">// Nahr�n� textur</span></p>
<p class="src0">{</p>
<p class="src1">for (int i = 0; i &lt; m_numMaterials; i++)<span class="kom">// Jednotliv� materi�ly</span></p>
<p class="src1">{</p>
<p class="src2">if (strlen(m_pMaterials[i].m_pTextureFilename) &gt; 0)<span class="kom">// Existuje �et�zec s cestou</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Nahraje texturu</span></p>
<p class="src3">m_pMaterials[i].m_texture = LoadGLTexture(m_pMaterials[i].m_pTextureFilename);</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Nulou indikuje, �e materi�l nem� texturu</span></p>
<p class="src3">m_pMaterials[i].m_texture = 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>M��eme za��t vykreslovat model. D�ky uspo��d�n� do struktur to nen� nic slo�it�ho. Ze v�eho nejd��ve ulo��me atribut, jestli je zapnut� nebo vypnut� texturov�n�. Na konci funkce ho budeme moci obnovit.</p>

<p class="src0">void Model::draw()</p>
<p class="src0">{</p>
<p class="src1">GLboolean texEnabled = glIsEnabled(GL_TEXTURE_2D);<span class="kom">// Ulo�� atribut</span></p>

<p>Ka�d� mesh renderujeme samostatn�, proto�e mesh seskupuje v�echny troj�heln�ky se stejn�mi vlastnostmi. Sta�� jedno hromadn� nastaven� OpenGL pro velkou skupinu polygon�, nam�sto mnohem m�n� efektivn�mu: nastavit vlastnosti pro troj�heln�k - vykreslit troj�heln�k. S meshi postupujeme takto: nastavit vlastnosti - vykreslit v�echny troj�heln�ky s t�mito vlastnostmi.</p>

<p class="src1">for (int i = 0; i &lt; m_numMeshes; i++)<span class="kom">// Meshe</span></p>
<p class="src1">{</p>

<p>M_pMeshes[i] pou�ijeme jako referenci na aktu�ln� mesh. Ka�d� z nich m� vlastn� materi�lov� vlastnosti, podle kter�ch nastav�me OpenGL. Pokud se materialIndex rovn� -1, znamen� to, �e mesh nen� definov�n. V takov�m p��pad� z�staneme u implicitn�ch nastaven� OpenGL. Texturu zvol�me a zapneme pouze tehdy, pokud je v�t�� ne� nula. P�i jej�m loadingu jsme nadefinovali, �e pokud neexistuje nastav�me ji na nulu. Vypnut� texturingu je tedy logick�m krokem. Pokud materi�l meshe neexistuje, texturov�n� tak� vypneme, proto�e nem�me kde vz�t texturu.</p>

<p class="src2">int materialIndex = m_pMeshes[i].m_materialIndex;<span class="kom">// Index</span></p>
<p class="src"></p>
<p class="src2">if (materialIndex &gt;= 0)<span class="kom">// Obsahuje mesh index materi�lu?</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Nastav� OpenGL</span></p>
<p class="src3">glMaterialfv(GL_FRONT, GL_AMBIENT, m_pMaterials[materialIndex].m_ambient);</p>
<p class="src3">glMaterialfv(GL_FRONT, GL_DIFFUSE, m_pMaterials[materialIndex].m_diffuse);</p>
<p class="src3">glMaterialfv(GL_FRONT, GL_SPECULAR, m_pMaterials[materialIndex].m_specular);</p>
<p class="src3">glMaterialfv(GL_FRONT, GL_EMISSION, m_pMaterials[materialIndex].m_emissive);</p>
<p class="src3">glMaterialf(GL_FRONT, GL_SHININESS, m_pMaterials[materialIndex].m_shininess);</p>
<p class="src"></p>
<p class="src3">if (m_pMaterials[materialIndex].m_texture &gt; 0)<span class="kom">// Obsahuje materi�l texturu?</span></p>
<p class="src3">{</p>
<p class="src4">glBindTexture(GL_TEXTURE_2D, m_pMaterials[materialIndex].m_texture);</p>
<p class="src4">glEnable(GL_TEXTURE_2D);</p>
<p class="src3">}</p>
<p class="src3">else<span class="kom">// Bez textury</span></p>
<p class="src3">{</p>
<p class="src4">glDisable(GL_TEXTURE_2D);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Bez materi�lu nem��e b�t ani textura</span></p>
<p class="src2">{</p>
<p class="src3">glDisable(GL_TEXTURE_2D);</p>
<p class="src2">}</p>

<p>P�i vykreslov�n� proch�z�me nejd��ve v�echny troj�heln�ky meshe a potom ka�d� z jeho vrchol�. Specifikujeme norm�lov� vektor a texturov� koordin�ty.</p>

<p class="src2">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek troj�heln�k�</span></p>
<p class="src2">{</p>
<p class="src3">for (int j = 0; j &lt; m_pMeshes[i].m_numTriangles; j++)<span class="kom">// Troj�heln�ky v meshi</span></p>
<p class="src3">{</p>
<p class="src4">int triangleIndex = m_pMeshes[i].m_pTriangleIndices[j];<span class="kom">// Index</span></p>
<p class="src"></p>
<p class="src4">const Triangle* pTri = &amp;m_pTriangles[triangleIndex];<span class="kom">// Troj�heln�k</span></p>
<p class="src"></p>
<p class="src4">for (int k = 0; k &lt; 3; k++)<span class="kom">// Vertexy v troj�heln�ku</span></p>
<p class="src4">{</p>
<p class="src5">int index = pTri-&gt;m_vertexIndices[k];<span class="kom">// Index vertexu</span></p>
<p class="src"></p>
<p class="src5">glNormal3fv(pTri-&gt;m_vertexNormals[k]);<span class="kom">// Norm�la</span></p>
<p class="src5">glTexCoord2f(pTri-&gt;m_s[k], pTri-&gt;m_t[k]);<span class="kom">// Texturovac� sou�adnice</span></p>
<p class="src5">glVertex3fv(m_pVertices[index].m_location);<span class="kom">// Sou�adnice vertexu</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src1">}</p>

<p>Obnov�me atribut OpenGL.</p>

<p class="src1"><span class="kom">// Obnoven� nastaven� OpenGL</span></p>
<p class="src1">if (texEnabled)</p>
<p class="src1">{</p>
<p class="src2">glEnable(GL_TEXTURE_2D);</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">glDisable(GL_TEXTURE_2D);</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jedin�m dal��m k�dem ve t��d� Model, kter� stoj� za pozornost je konstruktor a destruktor. Konstruktor inicializuje v�echny �lensk� prom�nn� na nulu nebo v p��pad� ukazatel� na NULL. M�jte na pam�ti, �e pokud zavol�te funkci loadModelData() dvakr�t pro jeden objekt, nastanou �niky pam�ti! Pam� se toti� uvol�uje a� v destruktoru.</p>

<p class="src0">Model::Model()<span class="kom">// Konstruktor</span></p>
<p class="src0">{</p>
<p class="src1">m_numMeshes = 0;</p>
<p class="src1">m_pMeshes = NULL;</p>
<p class="src"></p>
<p class="src1">m_numMaterials = 0;</p>
<p class="src1">m_pMaterials = NULL;</p>
<p class="src"></p>
<p class="src1">m_numTriangles = 0;</p>
<p class="src1">m_pTriangles = NULL;</p>
<p class="src"></p>
<p class="src1">m_numVertices = 0;</p>
<p class="src1">m_pVertices = NULL;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">Model::~Model()<span class="kom">// Destruktor</span></p>
<p class="src0">{</p>
<p class="src1">int i;</p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; m_numMeshes; i++)</p>
<p class="src1">{</p>
<p class="src2">delete[] m_pMeshes[i].m_pTriangleIndices;</p>
<p class="src1">}</p>
<p class="src1">for (i = 0; i &lt; m_numMaterials; i++)</p>
<p class="src1">{</p>
<p class="src2">delete[] m_pMaterials[i].m_pTextureFilename;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">m_numMeshes = 0;</p>
<p class="src"></p>
<p class="src1">if (m_pMeshes != NULL)</p>
<p class="src1">{</p>
<p class="src2">delete[] m_pMeshes;</p>
<p class="src2">m_pMeshes = NULL;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">m_numMaterials = 0;</p>
<p class="src"></p>
<p class="src1">if (m_pMaterials != NULL)</p>
<p class="src1">{</p>
<p class="src2">delete[] m_pMaterials;</p>
<p class="src2">m_pMaterials = NULL;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">m_numTriangles = 0;</p>
<p class="src"></p>
<p class="src1">if (m_pTriangles != NULL)</p>
<p class="src1">{</p>
<p class="src2">delete[] m_pTriangles;</p>
<p class="src2">m_pTriangles = NULL;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">m_numVertices = 0;</p>
<p class="src"></p>
<p class="src1">if (m_pVertices != NULL)</p>
<p class="src1">{</p>
<p class="src2">delete[] m_pVertices;</p>
<p class="src2">m_pVertices = NULL;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Vysv�tlili jsme si t��du Model, zbytek u� bude velice jednoduch�. Naho�e v souboru Lesson32.cpp deklarujeme ukazatel na model a inicializujeme ho na NULL.</p>

<p class="src0">Model *pModel = NULL;<span class="kom">// Ukazatel na model</span></p>

<p>Jeho data nahrajeme a� ve funkci WinMain(). Loading NIKDY nevkl�dejte do InitGL(), proto�e se vol� v�dycky, kdy� u�ivatel zm�n� m�d fullscreen/okno. P�i t�to akci se ztr�c� a znovu vytv��� OpenGL kontext, ale data modelu se nemus� (a kv�li �nik�m pam�ti dokonce nesm�) reloadovat. Z�st�vaj� nedot�en�. Sta�� znovu nahr�t textury, kter� jsou na OpenGL z�visl�. Je-li ve sc�n� v�ce model�, mus� se reloadTextures() volat zvlṻ pro ka�d� objekt t��dy. Pokud se stane, �e budou modely najednou b�l�, znamen� to, �e se textury nenahr�ly spr�vn�.</p>

<p class="src0"><span class="kom">// Za��tek funkce WinMain()</span></p>
<p class="src1">pModel = new MilkshapeModel();<span class="kom">// Alokace pam�ti pro model</span></p>
<p class="src"></p>
<p class="src1">if (pModel->loadModelData("data/model.ms3d") == false)<span class="kom">// Pokus� se nahr�t model</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, "Couldn't load the model data\\model.ms3d", "Error", MB_OK | MB_ICONERROR);</p>
<p class="src2">return 0;<span class="kom">// Model se nepoda�ilo nahr�t - program se ukon��</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Za��tek funkce InitGL()</span></p>
<p class="src1">pModel->reloadTextures();<span class="kom">// Nahr�n� textur modelu</span></p>

<p>Posledn�, co pop�eme je DrawGLScene(). Nam�sto klasick�ch glTranslatef() a glRotatef() pou�ijeme funkci gluLookAt(). Prvn�mi t�emi parametry um�s�uje kameru na pozici, prost�edn� t�i sou�adnice ur�uj� st�ed sc�ny a posledn� t�i definuj� vektor sm��uj�c� vzh�ru. V na�em p��pad� se d�v�me z bodu (75, 75, 75) na bod (0, 0, 0). Model tedy bude vykreslen kolem sou�adnic (0, 0, 0), pokud p�ed kreslen�m neprovedeme translaci. Osa y sm��uje vzh�ru. Aby se gluLookAt() chovala t�mto zp�sobem, mus� b�t vol�na jako prvn� po glLoadIdentity().</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Rendering sc�ny</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">gluLookAt(75,75,75, 0,0,0, 0,1,0);<span class="kom">// P�esun kamery</span></p>

<p>Aby byl v�sledek trochu zaj�mav�j�� rotujeme modelem kolem osy y.</p>

<p class="src1">glRotatef(yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>

<p>Pro rendering modelu pou�ijeme jeho vlastn� funkce. Vykresl� se vycentrovan� okolo st�edu, ale pouze tehdy, �e i v Milkshape 3D byl modelov�n okolo st�edu. Pokus s n�m budete cht�t rotovat, posunovat nebo m�nit velikost, zavolejte odpov�daj�c� OpenGL funkce. Pro otestov�n� si zkuste vytvo�it vlastn� model a nahrajte ho do programu. Funguje?</p>

<p class="src1">pModel->draw();<span class="kom">// Rendering modelu</span></p>
<p class="src"></p>
<p class="src1">yrot += 1.0f;<span class="kom">// Ot��en� sc�ny</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>A co d�l? Pl�nuji dal�� tutori�l pro NeHe, ve kter�m roz����me t��du tak, aby umo��ovala animaci objektu pomoc� jeho kostry (skeletal animation). Mo�n� tak� naprogramuji dal�� t��dy loader� - program bude schopen nahr�t v�ce r�zn�ch form�t�. Krok ke skelet�ln� animaci nen� a� zase tak velk�, jak se m��e zd�t, a�koli matematika bude o stupe� slo�it�j��. Pokud je�t� nerozum�te matic�m a vektor�m, je �as se na n� trochu pod�vat.</p>

<p class="autor">napsal: <?OdkazBlank('http://rsn.gamedev.net/', 'Brett Porter');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3>Informace o autorovi</h3>

<p>Brett Porter se narodil v Austr�lii, studoval na Wollogongsk� Univerzit�. Ned�vno absolvoval na BCompSc A BMath (BSc - bakal�� p��rodn�ch v�d). Programovat za�al p�ed dvan�cti lety v Basicu na &quot;klonu&quot; Commodore 64 zvan�m VZ300, ale brzy p�e�el na Pascal, Intel Assembler, C++ a Javu. P�ed n�kolika lety za�al pou��vat OpenGL.</p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson31.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson31_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson31.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson31.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson31.zip">GLut</a> k�d t�to lekce. ( <a href="mailto:rb@roccobalsamo.com">Rocco Balsamo</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson31.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:rodolphe.suescun@wanadoo.fr">Rodolphe Suescun</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson31.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson31.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(31);?>
<?FceNeHeOkolniLekce(31);?>

<?
include 'p_end.php';
?>
