<?
$g_title = 'CZ NeHe OpenGL - Lekce 31 - Nahrávání a renderování modelù';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(31);?>

<h1>Lekce 31 - Nahrávání a renderování modelù</h1>

<p class="nadpis_clanku">Dal¹í skvìlý tutoriál! Nauèíte se, jak nahrát a zobrazit otexturovaný Milkshape3D model. Nezdá se to, ale asi nejvíce se budou hodit znalosti o práci s dynamickou pamìtí a jejím kopírování z jednoho místa na druhé.</p>

<p>Zdrojový kód tohoto projektu byl vyjmut z PortaLib3D, knihovny, kterou jsem napsal, abych lidem umo¾nil zobrazovat modely za pou¾ití velmi malého mno¾ství dal¹ího kódu. Abyste se na ni mohli opravdu spolehnout musíte nejdøíve vìdìt, co dìlá a jak pracuje.</p>

<p>Èást PortaLib3D, uvedená zde, si stále zachovává mùj copyright. To neznamená, ¾e ji nesmíte pou¾ívat, ale ¾e pøi vlo¾ení kódu do svého projektu musíte uvést nále¾itý credit. To je v¹e - ¾ádné velké nároky. Pokud byste chtìli èíst, pochopit a re-implementovat celý kód (¾ádné kopírovat vlo¾it!), budete uvolnìni ze své povinnosti. Pak je to vá¹ výtvor. Pojïme se ale podívat na nìco zajímavìj¹ího.</p>

<p>Model, který pou¾íváme v tomto projektu, pochází z Milkshape3D. Je to opravdu kvalitní balík pro modelování, který zahrnuje vlastní file-formát. Mým dal¹ím plánem je implementovat Anim8or (<?OdkazBlank('http://www.anim8or.com/');?>), souborový reader. Je free a umí èíst samozøejmì i 3DS. Nicménì formát souboru není tím hlavním pro loading modelù. Nejdøíve se musí vytvoøit vlastní struktury, které jsou schopny pojmout data.</p>

<p>První ze v¹eho deklarujeme obecnou tøídu Model, která je kontejnerem pro v¹echna data.</p>

<p class="src0">class Model<span class="kom">// Obecné úlo¾i¹tì dat (abstraktní tøída)</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>

<p>Ze v¹eho nejdùle¾itìj¹í jsou samozøejmì vertexy. Pole tøí desetinných hodnot m_location reprezentuje jednotlivé x, y, z souøadnice. Promìnnou m_boneID budeme v tomto tutoriálu ignorovat. Její èas pøijde a¾ v dal¹ím pøi kosterní animaci.</p>

<p class="src1">struct Vertex<span class="kom">// Struktura vertexu</span></p>
<p class="src1">{</p>
<p class="src2">float m_location[3];<span class="kom">// X, y, z souøadnice</span></p>
<p class="src2">char m_boneID;<span class="kom">// Pro skeletální animaci</span></p>
<p class="src1">};</p>

<p>V¹echny vertexy potøebujeme seskupit do trojúhelníkù. Pole m_vertexIndices obsahuje tøi indexy do pole vertexù. Touto cestou bude ka¾dý vertex ulo¾en v pamìti pouze jednou. V polích m_s a m_t jsou texturové koordináty ka¾dého vrcholu. Poslední atribut definuje tøi normálové vektory pro svìtlo.</p>

<p class="src1">struct Triangle<span class="kom">// Struktura trojúhelníku</span></p>
<p class="src1">{</p>
<p class="src2">int m_vertexIndices[3];<span class="kom">// Tøi indexy do pole vertexù</span></p>
<p class="src2">float m_s[3], m_t[3];<span class="kom">// Texturové koordináty</span></p>
<p class="src2">float m_vertexNormals[3][3];<span class="kom">// Tøi normálové vektory</span></p>
<p class="src1">};</p>

<p>Dal¹í struktura popisuje mesh modelu. Mesh je skupina trojúhelníkù, na které je aplikován stejný materiál a textura. Skupiny meshù dohromady tvoøí celý model. Stejnì jako trojúhelníky obsahovaly pouze indexy na vertexy, budou i meshe obsahovat pouze indexy na trojúhelníky. Proto¾e neznáme jejich pøesný poèet, musí být pole dynamické. Tøetí promìnná je opìt indexem, tentokrát do materiálù (textura, osvìtlení).</p>

<p class="src1">struct Mesh<span class="kom">//Mesh modelu</span></p>
<p class="src1">{</p>
<p class="src2">int *m_pTriangleIndices;<span class="kom">// Indexy do trojúhelníkù</span></p>
<p class="src2">int m_numTriangles;<span class="kom">// Poèet trojúhelníkù</span></p>
<p class="src2">int m_materialIndex;<span class="kom">// Index do materiálù</span></p>
<p class="src1">};</p>

<p>Ve struktuøe Material jsou ulo¾ené standardní koeficienty svìtla, ve stejném formátu jako pou¾ívá OpenGL: okolní (ambient), rozptýlené (diffuse), odra¾ené (specular), vyzaøující (emissive) a lesklost (shininess). dále obsahuje objekt textury a souborovou cestu k textuøe, aby mohla být znovu nahrána, kdy¾ je ukonèen kontext OpenGL.</p>

<p class="src1">struct Material<span class="kom">// Vlastnosti materiálù</span></p>
<p class="src1">{</p>
<p class="src2">float m_ambient[4], m_diffuse[4], m_specular[4], m_emissive[4];<span class="kom">// Reakce materiálu na svìtlo</span></p>
<p class="src2">float m_shininess;<span class="kom">// Lesk materiálu</span></p>
<p class="src2">GLuint m_texture;<span class="kom">// Textura</span></p>
<p class="src2">char *m_pTextureFilename;<span class="kom">// Souborová cesta k textuøe</span></p>
<p class="src1">};</p>

<p>Vytvoøíme promìnné právì napsaných struktur ve formì ukazatelù na dynamická pole, jejich¾ pamì» alokuje funkce pro loading objektù. Musíme samozøejmì ukládat i velikost polí.</p>

<p class="src0">protected:</p>
<p class="src1">int m_numVertices;<span class="kom">// Poèet vertexù</span></p>
<p class="src1">Vertex *m_pVertices;<span class="kom">// Dynamické pole vertexù</span></p>
<p class="src"></p>
<p class="src1">int m_numTriangles;<span class="kom">// Poèet trojúhelníkù</span></p>
<p class="src1">Triangle *m_pTriangles;<span class="kom">// Dynamické pole trojúhelníkù</span></p>
<p class="src"></p>
<p class="src1">int m_numMeshes;<span class="kom">// Poèet meshù</span></p>
<p class="src1">Mesh *m_pMeshes;<span class="kom">// Dynamické pole meshù</span></p>
<p class="src"></p>
<p class="src1">int m_numMaterials;<span class="kom">// Poèet materiálù</span></p>
<p class="src1">Material *m_pMaterials;<span class="kom">// Dynamické pole materiálù</span></p>

<p>A koneènì metody tøídy. Virtuální èlenská funkce loadModelData() má za úkol nahrát data ze souboru. Pøiøadíme jí nulu, aby nemohl být vytvoøen objekt tøídy (abstraktní tøída). Tato tøída je zamý¹lena pouze jako úlo¾i¹tì dat. V¹echny operace pro nahrávání mají na starosti odvozené tøídy, kdy ka¾dá z nich umí svùj vlastní formát souboru. Celá hierarchie je více obecná.</p>

<p class="src0">public:</p>
<p class="src1">Model();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~Model();<span class="kom">// Destruktor</span></p>
<p class="src"></p>
<p class="src1">virtual bool loadModelData(const char *filename) = 0;<span class="kom">// Loading objektu ze souboru</span></p>

<p>Metoda reloadTextures() slou¾í pro loading textur a jejich znovunahrávání, kdy¾ se ztratí kontext OpenGL (napø. pøi pøepnutí z/do fullscreenu). Draw() vykresluje objekt. Tato funkce nemusí být virtuální, proto¾e defakto známe v¹echny potøebné informace o struktuøe objektu (vertexy, trojúhelníky...).</p>

<p class="src1">void reloadTextures();<span class="kom">// Znovunahrání textur</span></p>
<p class="src1">void draw();<span class="kom">// Vykreslení objektu</span></p>
<p class="src0">};</p>

<p>Od tøídy Model podìdíme tøídu MilkshapeModel. Pøepí¹eme v ní metodu loadModelData().</p>

<p class="src0">class MilkshapeModel : public Model</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">MilkshapeModel();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~MilkshapeModel();<span class="kom">// Destruktor</span></p>
<p class="src"></p>
<p class="src1">virtual bool loadModelData(const char *filename);<span class="kom">// Loading objektu ze souboru</span></p>
<p class="src0">};</p>

<p>Nyní nahrávání objektù. Pøepí¹eme virtuální funkci loadModelData() abstraktní tøídy Model tak, aby ve tøídì MilkShapeModel nahrávala data ze souboru ve formátu Milkshape3D. Pøedáváme jí øetìzec se jménem souboru. Pokud v¹e probìhne v poøádku, funkce nastaví datové struktury a vrátí true.</p>

<p class="src0">bool MilkshapeModel::loadModelData(const char *filename)</p>
<p class="src0">{</p>

<p>Soubor otevøeme jako vstupní (ios::in), binární (ios::binary) a nebudeme ho vytváøet (ios::nocreate). Pokud nebyl nalezen vrátí funkce false, aby indikovala error.</p>

<p class="src1">ifstream inputFile(filename, ios::in | ios::binary | ios::nocreate);<span class="kom">// Otevøení souboru</span></p>
<p class="src"></p>
<p class="src1">if (inputFile.fail())<span class="kom">// Podaøilo se ho otevøít?</span></p>
<p class="src2">return false;</p>

<p>Zjistíme velikost souboru v bytech a potom ho celý naèteme do pomocného bufferu pBuffer.</p>

<p class="src1"><span class="kom">// Velikost souboru</span></p>
<p class="src1">inputFile.seekg(0, ios::end);</p>
<p class="src1">long fileSize = inputFile.tellg();</p>
<p class="src1">inputFile.seekg(0, ios::beg);</p>
<p class="src"></p>
<p class="src1">byte *pBuffer = new byte[fileSize];<span class="kom">// Alokace pamìti pro kopii souboru</span></p>
<p class="src1">inputFile.read(pBuffer, fileSize);<span class="kom">// Vytvoøení pamì»ové kopie souboru</span></p>
<p class="src1">inputFile.close();<span class="kom">// Zavøení souboru</span></p>

<p>Deklarujeme pomocný ukazatel pPtr, který ihned inicializujeme tak, aby ukazoval na stejné místo jako pBuffer, tedy na zaèátek pamìti. Do hlavièky souboru pHeader ulo¾íme adresu hlavièky a zvìt¹íme adresu v pPtr o velikost hlavièky.</p>

<p>Pozn.: Strukturu hlavièky a jí podobné jsem na zaèátku tutoriálu neuvádìl, proto¾e je budeme pou¾ívat jenom zde, v této funkci. Pokud vás pøeci zajímají, stáhnìte si zdrojový kód. Jsou deklarované nahoøe v souboru MilkshapeModel.cpp.</p>

<p class="src1">const byte *pPtr = pBuffer;<span class="kom">// Pomocný ukazatel na kopii souboru</span></p>
<p class="src"></p>
<p class="src1">MS3DHeader *pHeader = (MS3DHeader*)pPtr;<span class="kom">// Ukazatel na hlavièku</span></p>
<p class="src1">pPtr += sizeof(MS3DHeader);<span class="kom">// Posun za hlavièku</span></p>

<p>Hlavièka pøímo specifikuje formát souboru. Ujistíme se, ¾e se jedná o platný formát, který umíme nahrát.</p>

<p class="src1"><span class="kom">// Není Milkshape3D souborem</span></p>
<p class="src1">if (strncmp(pHeader-&gt;m_ID, &quot;MS3D000000&quot;, 10) != 0)</p>
<p class="src1">{</p>
<p class="src2">delete [] pBuffer;<span class="kom">// Pøekl.: Sma¾e kopii souboru !!!!!</span></p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// ©patná verze souboru, tøída podporuje pouze verze 1.3 a 1.4</span></p>
<p class="src1">if (pHeader-&gt;m_version &lt; 3 || pHeader-&gt;m_version &gt; 4)</p>
<p class="src1">{</p>
<p class="src2">delete [] pBuffer;<span class="kom">// Pøekl.: Sma¾e kopii souboru !!!!!</span></p>
<p class="src2">return false;</p>
<p class="src1">}</p>

<p>Naèteme v¹echny vertexy. Nejdøíve zjistíme jejich poèet, alokujeme potøebnou pamì» a pøesuneme pPtr na dal¹í pozici. V cyklu procházíme jednotlivé vertexy. Nastavíme ukazatel pVertex na pøetypovaný pPtr a definujeme m_boneID. Nakonec zavoláme memcpy() pro zkopírování hodnot a zvìt¹íme pPtr.</p>

<p class="src1">int nVertices = *(word*)pPtr;<span class="kom">// Poèet vertexù</span></p>
<p class="src"></p>
<p class="src1">m_numVertices = nVertices;<span class="kom">// Nastaví atribut tøídy</span></p>
<p class="src1">m_pVertices = new Vertex[nVertices];<span class="kom">// Alokace pamìti pro vertexy</span></p>
<p class="src"></p>
<p class="src1">pPtr += sizeof(word);<span class="kom">// Posun za poèet vertexù</span></p>
<p class="src"></p>
<p class="src1">int i;<span class="kom">//Pomocná promìnná</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; nVertices; i++)<span class="kom">// Nahrává vertexy</span></p>
<p class="src1">{</p>
<p class="src2">MS3DVertex *pVertex = (MS3DVertex*)pPtr;<span class="kom">// Ukazatel na vertex</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Naètení vertexu</span></p>
<p class="src2">m_pVertices[i].m_boneID = pVertex-&gt;m_boneID;</p>
<p class="src2">memcpy(m_pVertices[i].m_location, pVertex-&gt;m_vertex, sizeof(float) * 3);</p>
<p class="src"></p>
<p class="src2">pPtr += sizeof(MS3DVertex);<span class="kom">// Posun za tento vertex</span></p>
<p class="src1">}</p>

<p>Stejnì jako u vertexù, tak i trojúhelníkù nejdøíve provedeme potøebné operace pro alokaci pamìti. V cyklu procházíme jednotlivé trojúhelníky a inicializujeme je. V¹imnìte si, ¾e v souboru jsou indexy vertexù ulo¾eny v poli word hodnot, ale v modelu kvùli konzistentnosti a jednoduchosti pou¾íváme datový typ int. Èíslo se implicitnì pøetypuje.</p>

<p class="src1">int nTriangles = *(word*)pPtr;<span class="kom">// Poèet trojúhelníkù</span></p>
<p class="src1">m_numTriangles = nTriangles;<span class="kom">// Nastaví atribut tøídy</span></p>
<p class="src1">m_pTriangles = new Triangle[nTriangles];<span class="kom">// Alokace pamìti pro trojúhelníky</span></p>
<p class="src"></p>
<p class="src1">pPtr += sizeof(word);<span class="kom">// Posun za poèet trojúhelníkù</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; nTriangles; i++)<span class="kom">// Naèítá trojúhelníky</span></p>
<p class="src1">{</p>
<p class="src2">MS3DTriangle *pTriangle = (MS3DTriangle*)pPtr;<span class="kom">// Ukazatel na trojúhelník</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Naètení trojúhelníku</span></p>
<p class="src2">int vertexIndices[3] = { pTriangle-&gt;m_vertexIndices[0], pTriangle-&gt;m_vertexIndices[1], pTriangle-&gt;m_vertexIndices[2] };</p>

<p>V¹echna èísla v poli t jsou nastavena na 1.0 mínus originál. To proto, ¾e OpenGL pou¾ívá poèátek texturovacího souøadnicového systému vlevo dole, narozdíl od Milkshape, které ho má vlevo nahoøe. Odeètením od jednièky, y souøadnici invertujeme. V¹e ostatní by mìlo být bez problémù.</p>

<p class="src2">float t[3] = { 1.0f-pTriangle-&gt;m_t[0], 1.0f-pTriangle-&gt;m_t[1], 1.0f-pTriangle-&gt;m_t[2] };</p>
<p class="src"></p>
<p class="src2">memcpy(m_pTriangles[i].m_vertexNormals, pTriangle-&gt;m_vertexNormals, sizeof(float)*3*3);</p>
<p class="src2">memcpy(m_pTriangles[i].m_s, pTriangle-&gt;m_s, sizeof(float)*3);</p>
<p class="src2">memcpy(m_pTriangles[i].m_t, t, sizeof(float)*3);</p>
<p class="src2">memcpy(m_pTriangles[i].m_vertexIndices, vertexIndices, sizeof(int)*3);</p>
<p class="src"></p>
<p class="src2">pPtr += sizeof(MS3DTriangle);<span class="kom">// Posun za tento trojúhelník</span></p>
<p class="src1">}</p>

<p>Nahrajeme struktury mesh. V Milkshape3D jsou také nazývány groups - skupiny. V ka¾dé se li¹í poèet trojúhelníkù, tak¾e nemù¾eme naèíst ¾ádnou standardní strukturu. Namísto toho budeme dynamicky alokovat pamì» pro indexy trojúhelníkù a v ka¾dém prùchodu je naèítat.</p>

<p class="src1">int nGroups = *(word*)pPtr;<span class="kom">// Poèet meshù</span></p>
<p class="src1">m_numMeshes = nGroups;<span class="kom">// Nastaví atribut tøídy</span></p>
<p class="src1">m_pMeshes = new Mesh[nGroups];<span class="kom">// Alokace pamìti pro meshe</span></p>
<p class="src"></p>
<p class="src1">pPtr += sizeof(word);<span class="kom">// Posun za poèet meshù</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; nGroups; i++)<span class="kom">// Naèítá meshe</span></p>
<p class="src1">{</p>
<p class="src2">pPtr += sizeof(byte);<span class="kom">// Posun za flagy</span></p>
<p class="src2">pPtr += 32;<span class="kom">// Posun za jméno</span></p>
<p class="src"></p>
<p class="src2">word nTriangles = *(word*)pPtr;<span class="kom">// Poèet trojúhelníkù v meshi</span></p>
<p class="src2">pPtr += sizeof(word);<span class="kom">// Posun za poèet trojúhelníkù</span></p>
<p class="src"></p>
<p class="src2">int *pTriangleIndices = new int[nTriangles];<span class="kom">// Alokace pamìti pro indexy trojúhelníkù</span></p>
<p class="src"></p>
<p class="src2">for (int j = 0; j &lt; nTriangles; j++)<span class="kom">// Naèítá indexy trojúhelníkù</span></p>
<p class="src2">{</p>
<p class="src3">pTriangleIndices[j] = *(word*)pPtr;<span class="kom">// Pøiøadí index trojúhelníku</span></p>
<p class="src3">pPtr += sizeof(word);<span class="kom">// Posun za index trojúhelníku</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">char materialIndex = *(char*)pPtr;<span class="kom">// Naète index materiálu</span></p>
<p class="src"></p>
<p class="src2">pPtr += sizeof(char);<span class="kom">// Posun za index materiálu</span></p>
<p class="src"></p>
<p class="src2">m_pMeshes[i].m_materialIndex = materialIndex;<span class="kom">// Index materiálu</span></p>
<p class="src2">m_pMeshes[i].m_numTriangles = nTriangles;<span class="kom">// Poèet trojúhelníkù</span></p>
<p class="src2">m_pMeshes[i].m_pTriangleIndices = pTriangleIndices;<span class="kom">// Indexy trojúhelníkù</span></p>
<p class="src1">}</p>

<p>Poslední, co naèítáme jsou informace o materiálech.</p>

<p class="src1">int nMaterials = *(word*)pPtr;<span class="kom">// Poèet materiálù</span></p>
<p class="src1">m_numMaterials = nMaterials;<span class="kom">// Nastaví atribut tøídy</span></p>
<p class="src1">m_pMaterials = new Material[nMaterials];<span class="kom">// Alokace pamìti pro materiály</span></p>
<p class="src"></p>
<p class="src1">pPtr += sizeof(word);<span class="kom">// Posun za poèet materiálù</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; nMaterials; i++)<span class="kom">// Prochází materiály</span></p>
<p class="src1">{</p>
<p class="src2">MS3DMaterial *pMaterial = (MS3DMaterial*)pPtr;<span class="kom">// Ukazatel na materiál</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Naète materiál</span></p>
<p class="src2">memcpy(m_pMaterials[i].m_ambient, pMaterial-&gt;m_ambient, sizeof(float)*4);</p>
<p class="src2">memcpy(m_pMaterials[i].m_diffuse, pMaterial-&gt;m_diffuse, sizeof(float)*4);</p>
<p class="src2">memcpy(m_pMaterials[i].m_specular, pMaterial-&gt;m_specular, sizeof(float)*4);</p>
<p class="src2">memcpy(m_pMaterials[i].m_emissive, pMaterial-&gt;m_emissive, sizeof(float)*4);</p>
<p class="src2">m_pMaterials[i].m_shininess = pMaterial-&gt;m_shininess;</p>

<p>Alokujeme pamì» pro øetìzec jména souboru textury a zkopírujeme ho.</p>

<p class="src2"><span class="kom">// Alokace pro jméno souboru textury</span></p>
<p class="src2">m_pMaterials[i].m_pTextureFilename = new char[strlen(pMaterial-&gt;m_texture)+1];</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Zkopírování jména souboru</span></p>
<p class="src2">strcpy(m_pMaterials[i].m_pTextureFilename, pMaterial-&gt;m_texture);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Posun za materiál</span></p>
<p class="src2">pPtr += sizeof(MS3DMaterial);</p>
<p class="src1">}</p>

<p>Nakonec loadujeme textury objektu, uvolníme pamì» kopie souboru a vrátíme true, abychom oznámili úspìch celé akce.</p>

<p class="src1">reloadTextures();<span class="kom">// Nahraje textury</span></p>
<p class="src"></p>
<p class="src1">delete [] pBuffer;<span class="kom">// Sma¾e kopii souboru</span></p>
<p class="src"></p>
<p class="src1">return true;<span class="kom">// Model byl nahrán</span></p>
<p class="src0">}</p>

<p>Nyní jsou èlenské promìnné tøídy Model vyplnìné. Zbývá je¹tì nahrát textury. V cyklu procházíme v¹echny materiály a testujeme, jestli je øetìzec se jménem textury del¹í ne¾ nula. Pokud ano nahrajeme texturu pomocí standardní NeHe funkce. Pokud ne pøiøadíme textuøe nulu jako indikaci, ¾e neexistuje.</p>

<p class="src0">void Model::reloadTextures()<span class="kom">// Nahrání textur</span></p>
<p class="src0">{</p>
<p class="src1">for (int i = 0; i &lt; m_numMaterials; i++)<span class="kom">// Jednotlivé materiály</span></p>
<p class="src1">{</p>
<p class="src2">if (strlen(m_pMaterials[i].m_pTextureFilename) &gt; 0)<span class="kom">// Existuje øetìzec s cestou</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Nahraje texturu</span></p>
<p class="src3">m_pMaterials[i].m_texture = LoadGLTexture(m_pMaterials[i].m_pTextureFilename);</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Nulou indikuje, ¾e materiál nemá texturu</span></p>
<p class="src3">m_pMaterials[i].m_texture = 0;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Mù¾eme zaèít vykreslovat model. Díky uspoøádání do struktur to není nic slo¾itého. Ze v¹eho nejdøíve ulo¾íme atribut, jestli je zapnuté nebo vypnuté texturování. Na konci funkce ho budeme moci obnovit.</p>

<p class="src0">void Model::draw()</p>
<p class="src0">{</p>
<p class="src1">GLboolean texEnabled = glIsEnabled(GL_TEXTURE_2D);<span class="kom">// Ulo¾í atribut</span></p>

<p>Ka¾dý mesh renderujeme samostatnì, proto¾e mesh seskupuje v¹echny trojúhelníky se stejnými vlastnostmi. Staèí jedno hromadné nastavení OpenGL pro velkou skupinu polygonù, namísto mnohem ménì efektivnímu: nastavit vlastnosti pro trojúhelník - vykreslit trojúhelník. S meshi postupujeme takto: nastavit vlastnosti - vykreslit v¹echny trojúhelníky s tìmito vlastnostmi.</p>

<p class="src1">for (int i = 0; i &lt; m_numMeshes; i++)<span class="kom">// Meshe</span></p>
<p class="src1">{</p>

<p>M_pMeshes[i] pou¾ijeme jako referenci na aktuální mesh. Ka¾dý z nich má vlastní materiálové vlastnosti, podle kterých nastavíme OpenGL. Pokud se materialIndex rovná -1, znamená to, ¾e mesh není definován. V takovém pøípadì zùstaneme u implicitních nastavení OpenGL. Texturu zvolíme a zapneme pouze tehdy, pokud je vìt¹í ne¾ nula. Pøi jejím loadingu jsme nadefinovali, ¾e pokud neexistuje nastavíme ji na nulu. Vypnutí texturingu je tedy logickým krokem. Pokud materiál meshe neexistuje, texturování také vypneme, proto¾e nemáme kde vzít texturu.</p>

<p class="src2">int materialIndex = m_pMeshes[i].m_materialIndex;<span class="kom">// Index</span></p>
<p class="src"></p>
<p class="src2">if (materialIndex &gt;= 0)<span class="kom">// Obsahuje mesh index materiálu?</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Nastaví OpenGL</span></p>
<p class="src3">glMaterialfv(GL_FRONT, GL_AMBIENT, m_pMaterials[materialIndex].m_ambient);</p>
<p class="src3">glMaterialfv(GL_FRONT, GL_DIFFUSE, m_pMaterials[materialIndex].m_diffuse);</p>
<p class="src3">glMaterialfv(GL_FRONT, GL_SPECULAR, m_pMaterials[materialIndex].m_specular);</p>
<p class="src3">glMaterialfv(GL_FRONT, GL_EMISSION, m_pMaterials[materialIndex].m_emissive);</p>
<p class="src3">glMaterialf(GL_FRONT, GL_SHININESS, m_pMaterials[materialIndex].m_shininess);</p>
<p class="src"></p>
<p class="src3">if (m_pMaterials[materialIndex].m_texture &gt; 0)<span class="kom">// Obsahuje materiál texturu?</span></p>
<p class="src3">{</p>
<p class="src4">glBindTexture(GL_TEXTURE_2D, m_pMaterials[materialIndex].m_texture);</p>
<p class="src4">glEnable(GL_TEXTURE_2D);</p>
<p class="src3">}</p>
<p class="src3">else<span class="kom">// Bez textury</span></p>
<p class="src3">{</p>
<p class="src4">glDisable(GL_TEXTURE_2D);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Bez materiálu nemù¾e být ani textura</span></p>
<p class="src2">{</p>
<p class="src3">glDisable(GL_TEXTURE_2D);</p>
<p class="src2">}</p>

<p>Pøi vykreslování procházíme nejdøíve v¹echny trojúhelníky meshe a potom ka¾dý z jeho vrcholù. Specifikujeme normálový vektor a texturové koordináty.</p>

<p class="src2">glBegin(GL_TRIANGLES);<span class="kom">// Zaèátek trojúhelníkù</span></p>
<p class="src2">{</p>
<p class="src3">for (int j = 0; j &lt; m_pMeshes[i].m_numTriangles; j++)<span class="kom">// Trojúhelníky v meshi</span></p>
<p class="src3">{</p>
<p class="src4">int triangleIndex = m_pMeshes[i].m_pTriangleIndices[j];<span class="kom">// Index</span></p>
<p class="src"></p>
<p class="src4">const Triangle* pTri = &amp;m_pTriangles[triangleIndex];<span class="kom">// Trojúhelník</span></p>
<p class="src"></p>
<p class="src4">for (int k = 0; k &lt; 3; k++)<span class="kom">// Vertexy v trojúhelníku</span></p>
<p class="src4">{</p>
<p class="src5">int index = pTri-&gt;m_vertexIndices[k];<span class="kom">// Index vertexu</span></p>
<p class="src"></p>
<p class="src5">glNormal3fv(pTri-&gt;m_vertexNormals[k]);<span class="kom">// Normála</span></p>
<p class="src5">glTexCoord2f(pTri-&gt;m_s[k], pTri-&gt;m_t[k]);<span class="kom">// Texturovací souøadnice</span></p>
<p class="src5">glVertex3fv(m_pVertices[index].m_location);<span class="kom">// Souøadnice vertexu</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src1">}</p>

<p>Obnovíme atribut OpenGL.</p>

<p class="src1"><span class="kom">// Obnovení nastavení OpenGL</span></p>
<p class="src1">if (texEnabled)</p>
<p class="src1">{</p>
<p class="src2">glEnable(GL_TEXTURE_2D);</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">glDisable(GL_TEXTURE_2D);</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Jediným dal¹ím kódem ve tøídì Model, který stojí za pozornost je konstruktor a destruktor. Konstruktor inicializuje v¹echny èlenské promìnné na nulu nebo v pøípadì ukazatelù na NULL. Mìjte na pamìti, ¾e pokud zavoláte funkci loadModelData() dvakrát pro jeden objekt, nastanou úniky pamìti! Pamì» se toti¾ uvolòuje a¾ v destruktoru.</p>

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

<p>Vysvìtlili jsme si tøídu Model, zbytek u¾ bude velice jednoduchý. Nahoøe v souboru Lesson32.cpp deklarujeme ukazatel na model a inicializujeme ho na NULL.</p>

<p class="src0">Model *pModel = NULL;<span class="kom">// Ukazatel na model</span></p>

<p>Jeho data nahrajeme a¾ ve funkci WinMain(). Loading NIKDY nevkládejte do InitGL(), proto¾e se volá v¾dycky, kdy¾ u¾ivatel zmìní mód fullscreen/okno. Pøi této akci se ztrácí a znovu vytváøí OpenGL kontext, ale data modelu se nemusí (a kvùli únikùm pamìti dokonce nesmí) reloadovat. Zùstávají nedotèená. Staèí znovu nahrát textury, které jsou na OpenGL závislé. Je-li ve scénì více modelù, musí se reloadTextures() volat zvlá¹» pro ka¾dý objekt tøídy. Pokud se stane, ¾e budou modely najednou bílé, znamená to, ¾e se textury nenahrály správnì.</p>

<p class="src0"><span class="kom">// Zaèátek funkce WinMain()</span></p>
<p class="src1">pModel = new MilkshapeModel();<span class="kom">// Alokace pamìti pro model</span></p>
<p class="src"></p>
<p class="src1">if (pModel->loadModelData("data/model.ms3d") == false)<span class="kom">// Pokusí se nahrát model</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, "Couldn't load the model data\\model.ms3d", "Error", MB_OK | MB_ICONERROR);</p>
<p class="src2">return 0;<span class="kom">// Model se nepodaøilo nahrát - program se ukonèí</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Zaèátek funkce InitGL()</span></p>
<p class="src1">pModel->reloadTextures();<span class="kom">// Nahrání textur modelu</span></p>

<p>Poslední, co popí¹eme je DrawGLScene(). Namísto klasických glTranslatef() a glRotatef() pou¾ijeme funkci gluLookAt(). Prvními tøemi parametry umís»uje kameru na pozici, prostøední tøi souøadnice urèují støed scény a poslední tøi definují vektor smìøující vzhùru. V na¹em pøípadì se díváme z bodu (75, 75, 75) na bod (0, 0, 0). Model tedy bude vykreslen kolem souøadnic (0, 0, 0), pokud pøed kreslením neprovedeme translaci. Osa y smìøuje vzhùru. Aby se gluLookAt() chovala tímto zpùsobem, musí být volána jako první po glLoadIdentity().</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Rendering scény</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">gluLookAt(75,75,75, 0,0,0, 0,1,0);<span class="kom">// Pøesun kamery</span></p>

<p>Aby byl výsledek trochu zajímavìj¹í rotujeme modelem kolem osy y.</p>

<p class="src1">glRotatef(yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>

<p>Pro rendering modelu pou¾ijeme jeho vlastní funkce. Vykreslí se vycentrovaný okolo støedu, ale pouze tehdy, ¾e i v Milkshape 3D byl modelován okolo støedu. Pokus s ním budete chtít rotovat, posunovat nebo mìnit velikost, zavolejte odpovídající OpenGL funkce. Pro otestování si zkuste vytvoøit vlastní model a nahrajte ho do programu. Funguje?</p>

<p class="src1">pModel->draw();<span class="kom">// Rendering modelu</span></p>
<p class="src"></p>
<p class="src1">yrot += 1.0f;<span class="kom">// Otáèení scény</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>A co dál? Plánuji dal¹í tutoriál pro NeHe, ve kterém roz¹íøíme tøídu tak, aby umo¾òovala animaci objektu pomocí jeho kostry (skeletal animation). Mo¾ná také naprogramuji dal¹í tøídy loaderù - program bude schopen nahrát více rùzných formátù. Krok ke skeletální animaci není a¾ zase tak velký, jak se mù¾e zdát, aèkoli matematika bude o stupeò slo¾itìj¹í. Pokud je¹tì nerozumíte maticím a vektorùm, je èas se na nì trochu podívat.</p>

<p class="autor">napsal: <?OdkazBlank('http://rsn.gamedev.net/', 'Brett Porter');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3>Informace o autorovi</h3>

<p>Brett Porter se narodil v Austrálii, studoval na Wollogongské Univerzitì. Nedávno absolvoval na BCompSc A BMath (BSc - bakaláø pøírodních vìd). Programovat zaèal pøed dvanácti lety v Basicu na &quot;klonu&quot; Commodore 64 zvaném VZ300, ale brzy pøe¹el na Pascal, Intel Assembler, C++ a Javu. Pøed nìkolika lety zaèal pou¾ívat OpenGL.</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson31.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson31_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson31.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson31.zip">Dev C++</a> kód této lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/glut/lesson31.zip">GLut</a> kód této lekce. ( <a href="mailto:rb@roccobalsamo.com">Rocco Balsamo</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson31.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:rodolphe.suescun@wanadoo.fr">Rodolphe Suescun</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson31.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson31.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(31);?>
<?FceNeHeOkolniLekce(31);?>

<?
include 'p_end.php';
?>
