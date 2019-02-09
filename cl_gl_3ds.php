<?
$g_title = 'CZ NeHe OpenGL - Naèítání .3DS modelù';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Naèítání .3DS modelù</h1>

<p class="nadpis_clanku">V tomto èlánku si uká¾eme, jak nahrát a vykreslit model ve formátu .3DS (3D Studio Max). Ná¹ kód bude umìt bez problémù naèítat soubory do tøetí verze programu, s vy¹¹ími verzemi bude pracovat také, ale nebude podporovat jejich nové funkce. Vycházím z ukázkového pøíkladu z www.gametutorials.com, kde také najdete zdrojové kódy pro C++ (èlánek je v Delphi).</p>

<p>Hned na úvod bude dobré øíct, ¾e 3D Studio Max nemám a modely, které jsem zkou¹el, jsou exportované z programu Cinema 4D. U¾ pøi testování jsem narazil na rozdíly ve formátu - napø. pùvodní model ukládá barvu jako 3x byte, Cinema 4D jako 3x single, proto nemù¾u zaruèit 100% kompatibilitu.</p>

<p>Program je postaven na NeHe kódu z posledních lekcí. Provedl jsem jen nìkteré drobné úpravy jako napø. zavedení promìnných pro rozmìry okna atp., ale nebudu ho tu podrobnì popisovat. Zamìøím se hlavnì na naèítání 3ds souboru.</p>

<p>V¹e potøebné se nachází v jednotce f_3ds.pas. Na zaèátku definujeme konstanty, které pøedstavují identifikátory jednotlivých blokù v souboru. Ka¾dý 3ds soubor se skládá z urèitých èástí - blokù, které v sobì uchovávají rùzné informace o modelu. Ka¾dý blok obsahuje identifikátor, svoji délku a vlastní data. Nìkteré bloky slou¾í pouze jako kontejnery a obsahují vìt¹í èi men¹í poèet jiných blokù. Ne v¹echny jsou v¹ak zdokumentované, ale to nevadí, proto¾e takové bloky je mo¾né díky znalosti jejich délky pøeskoèit. Podrobný popis struktury souboru se nachází v pøilo¾ené dokumentaci.</p>

<p class="src0">unit f_3ds;
<p class="src"></p>
<p class="src0">interface
<p class="src"></p>
<p class="src0">const<span class="kom">// Konstanty hlavièek jednotlivých blokù</span></p>
<p class="src1">PRIMARY = $4D4D;</p>
<p class="src1">OBJECTINFO = $3D3D;</p>
<p class="src1">VERSION = $0002;</p>
<p class="src1">EDITKEYFRAME = $B000;</p>
<p class="src1">MATERIAL = $AFFF;</p>
<p class="src1">OBJEKT = $4000;</p>
<p class="src1">MATNAME = $A000;</p>
<p class="src1">MATDIFFUSE = $A020;</p>
<p class="src1">MATMAP = $A200;</p>
<p class="src1">MATMAPFILE = $A300;</p>
<p class="src1">OBJECT_MESH = $4100;</p>
<p class="src1">OBJECT_VERTICES = $4110;</p>
<p class="src1">OBJECT_FACES = $4120;</p>
<p class="src1">OBJECT_MATERIAL = $4130;</p>
<p class="src1">OBJECT_UV = $4140;</p>

<p>Dále definujeme nìkolik struktur. CVector3 uchovává souøadnice vertexu, CVector2 ukládá texturové koordináty.</p>

<p class="src0">type</p>
<p class="src1">CVector3 = record<span class="kom">// Vektor 3D</span></p>
<p class="src2">x, y, z: single;</p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">CVector2 = record<span class="kom">// Vektor 2D</span></p>
<p class="src2">x, y: single;</p>
<p class="src1">end;</p>

<p>Struktura tFace obsahuje informace o plo¹ce (trojúhelníku) objektu. Pou¾ívají se dvì pole indexù, index do pole vertexù a index do pole texturových koordinátù. Ka¾dý vertex je v souboru ulo¾en pouze jednou a informace o plo¹ce obsahuje pouze indexy jednotlivých vrcholù. Odpadá tak dublování vertexù, proto¾e ty jsou èasto sdíleny více trojúhelníky. Stejné je to i s texturovými koordináty.</p>

<p class="src1">tFace = record<span class="kom">// Informace o plo¹kách (trojúhelnících) objektu</span></p>
<p class="src2">vertIndex: array [0..2] of integer;<span class="kom">// Index do pole vertexù</span></p>
<p class="src2">coordIndex: array [0..2] of integer;<span class="kom">// Index do pole texturových koordinátù</span></p>
<p class="src1">end;</p>

<p>Struktura tMaterialInfo obsahuje informace o materiálu, jeho jméno, cesta k souboru s texturou (pokud existuje), poèet bytù pou¾itých pro vyjádøení barvy, pole pro barvu, identifikátor textury a dal¹í promìnné pro práci s texturou, které ov¹em nejsou v kódu vyu¾ity.</p>

<p class="src1">tMaterialInfo = record<span class="kom">// Informace o materiálu</span></p>
<p class="src2">strName: string;<span class="kom">// Jméno materiálu</span></p>
<p class="src2">strFile: string;<span class="kom">// Cesta k souboru s texturou</span></p>
<p class="src2">bpc: integer;<span class="kom">// Poèet bytù na barvu</span></p>
<p class="src2">colorub: array [0..2] of byte;<span class="kom">// Barva v bytech</span></p>
<p class="src2">colorf: array [0..2] of single;<span class="kom">// Barva v singlech</span></p>
<p class="src2">texureId: integer;<span class="kom">// ID textury</span></p>
<p class="src2">uTile: double;<span class="kom">// Opakování textury v ose u (nepou¾ito)</span></p>
<p class="src2">vTile: double;<span class="kom">// Opakování textury v ose v (nepou¾ito)</span></p>
<p class="src2">uOffset: double;<span class="kom">// Posunutí textury v ose u (nepou¾ito)</span></p>
<p class="src2">vOffset: double;<span class="kom">// Posunutí textury v ose v (nepou¾ito)</span></p>
<p class="src1">end;</p>

<p>Struktura t3DObject obsahuje informace o objektu - poèet vertexù, poèet plo¹ek (trojúhelníkù), poèet texturových koordinátù, identifikátor pou¾itého materiálu, flag textury (ano/ne), jméno objektu, pole vertexù, pole normál, pole texturových koordinátù a pole plo¹ek.</p>

<p class="src0">t3DObject = record<span class="kom">// Informace o objektu</span></p>
<p class="src1">numOfVerts: integer;<span class="kom">// Poèet vertexù</span></p>
<p class="src1">numOfFaces: integer;<span class="kom">// Poèet plo¹ek</span></p>
<p class="src1">numTexVertex: integer;<span class="kom">// Poèet texturových koordinátù</span></p>
<p class="src1">materialID: integer;<span class="kom">// ID materiálu</span></p>
<p class="src1">bHasTexture: boolean;<span class="kom">// TRUE, pokud materiál obsahuje texturu</span></p>
<p class="src1">strName: string;<span class="kom">// Jméno objektu</span></p>
<p class="src1">pVerts: array of CVector3;<span class="kom">// Vertexy</span></p>
<p class="src1">pNormals: array of CVector3;<span class="kom">// Normály</span></p>
<p class="src1">pTexVerts: array of CVector2;<span class="kom">// Texturové koordináty</span></p>
<p class="src1">pFaces: array of tFace;<span class="kom">// Plo¹ky</span></p>
<p class="src0">end;</p>
<p class="src"></p>
<p class="src0">Pt3DObject = ^t3DObject;<span class="kom">// Ukazatel na objekt</span></p>

<p>Struktura t3DModel obsahuje informaci o modelu - poèet objektù, poèet materiálù, pole materiálù a pole objektù.</p>

<p class="src0">t3DModel = record<span class="kom">// Informace o modelu</span></p>
<p class="src1">numOfObjects: integer;<span class="kom">// Poèet objektù</span></p>
<p class="src1">numOfMaterials: integer;<span class="kom">// Poèet materiálù</span></p>
<p class="src1">pMaterials: array of tMaterialInfo;<span class="kom">// Pole materiálù</span></p>
<p class="src1">pObject: array of t3DObject;<span class="kom">// Pole objektù</span></p>
<p class="src0">end;</p>

<p>Struktura tChunk obsahuje informace o naèítaném bloku. Identifikátor, délku a poèet ji¾ pøeètených bytù.</p>

<p class="src0">tChunk = record<span class="kom">// Informace o bloku</span></p>
<p class="src1">ID: word;<span class="kom">// Identifikátor bloku</span></p>
<p class="src1">length: cardinal;<span class="kom">// Délka bloku</span></p>
<p class="src1">bytesRead: cardinal;<span class="kom">// Ji¾ pøeètené byty</span></p>
<p class="src0">end;</p>

<p>Dále následuje tøída, která se stará o nahrávání souboru. Myslím, ¾e komentáøe u jednotlivých øádkù hovoøí za v¹e. K jednotlivým metodám se podrobnì vrátím ní¾e.</p>

<p class="src0">CLoad3DS = class<span class="kom">// Tøída pro nahrání 3DS souboru</span></p>
<p class="src1">public</p>
<p class="src2">constructor Create;<span class="kom">// Konstruktor</span></p>
<p class="src2">destructor Destroy; override;<span class="kom">// Destruktor</span></p>
<p class="src2">function Import3DS(var pModel: t3DModel; strFileName: string): boolean<span class="kom">// Funkce pro nahrání souboru</span></p>
<p class="src"></p>
<p class="src1">private</p>
<p class="src2">m_FilePointer: integer;<span class="kom">// Ukazatel na soubor</span></p>
<p class="src2">function GetString(var pBuffer: string): integer;<span class="kom">// Naète øetìzec</span></p>
<p class="src2">procedure ReadChunk(var pChunk: tChunk);<span class="kom">// Naète dal¹í blok</span></p>
<p class="src2">procedure ProcessNextChunk(var pModel: t3DModel; var pPreviousChunk: tChunk);<span class="kom">// Naète dal¹í soubor blokù</span></p>
<p class="src2">procedure ProcessNextObjectChunk(var pModel: t3DModel; var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète dal¹í objektový blok</span></p>
<p class="src2">procedure ProcessNextMaterialChunk(var pModel: t3DModel; var pPreviousChunk: tChunk);<span class="kom">// Naète dal¹í materiálový blok</span></p>
<p class="src2">procedure ReadColorChunk(var pMaterial: tMaterialInfo; var pChunk: tChunk);<span class="kom">// Naète RGB barvu objektu</span></p>
<p class="src2">procedure ReadVertices(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète vertexy objektu</span></p>
<p class="src2">procedure ReadVertexIndices(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète plo¹ky objektu</span></p>
<p class="src2">procedure ReadUVCoordinates(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète texturové koordináty objektu</span></p>
<p class="src2">procedure ReadObjectMaterial(pModel: t3DModel; var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète materiál objektu</span></p>
<p class="src2">procedure ComputeNormals(pModel: t3DModel);<span class="kom">// Vypoèítá normály</span></p>
<p class="src2">procedure CleanUp;<span class="kom">// Uvolní prostøedky a uzavøe soubor</span></p>
<p class="src0">end;</p>

<p>Promìnná gBuffer slou¾í pro naètení nepotøebných dat, jako jsou neznámé bloky nebo bloky, které zámìrnì pøeskoèíme, proto¾e je nebudeme potøebovat. Dále pøidáme dvì jednotky.</p>

<p class="src0">var</p>
<p class="src1">gBuffer: array [0..50000] of integer;<span class="kom">// Buffer pro naètení nepotøebných dat</span></p>
<p class="src"></p>
<p class="src0">implementation</p>
<p class="src0">uses SysUtils, Windows;</p>

<p>Funkce Vector spoèítá vektor mezi dvìma body. Nic slo¾itého. Jak jistì v¹ichni znáte z analytické geometrie, od koncového bodu se odeète poèátek.</p>

<p class="src0">function Vector(vPoint1, vPoint2: CVector3): CVector3;<span class="kom">// Výpoèet vektoru mezi dvìma body</span></p>
<p class="src0">var</p>
<p class="src1">vVector: CVector3;</p>
<p class="src0">begin</p>
<p class="src1">vVector.x := vPoint1.x - vPoint2.x;<span class="kom">// Bod 1 - Bod 2</span></p>
<p class="src1">vVector.y := vPoint1.y - vPoint2.y;</p>
<p class="src1">vVector.z := vPoint1.z - vPoint2.z;</p>
<p class="src1">Result := vVector;</p>
<p class="src0">end;</p>

<p>Funce Cross vrací vektorový souèin dvou vektorù, tedy vektor kolmý na rovinu, kterou vytváøejí dva pùvodní vektory. S touto funkcí budeme poèítat normálové vektory pro svìtlo.</p>

<p class="src0">function Cross(vVector1, vVector2: CVector3): CVector3;<span class="kom">// Vektorový souèin</span></p>
<p class="src0">var</p>
<p class="src1">vCross: CVector3;</p>
<p class="src0">begin</p>
<p class="src1">vCross.x := ((vVector1.y * vVector2.z) - (vVector1.z * vVector2.y));</p>
<p class="src1">vCross.y := ((vVector1.z * vVector2.x) - (vVector1.x * vVector2.z));</p>
<p class="src1">vCross.z := ((vVector1.x * vVector2.y) - (vVector1.y * vVector2.x));</p>
<p class="src1">Result := vCross;</p>
<p class="src0">end;</p>

<p>Funkce Normalize vrací jednotkový vektor.</p>

<p class="src0">function Normalize(vNormal: CVector3): CVector3;<span class="kom">// Normalizace vektoru</span></p>
<p class="src0">var</p>
<p class="src1">Magnitude: Double;</p>
<p class="src0">begin</p>
<p class="src1">Magnitude := Sqrt(Sqr(vNormal.x) + Sqr(vNormal.y) + Sqr(vNormal.z));<span class="kom">// Velikost vektoru</span></p>
<p class="src1">vNormal.x := vNormal.x / Magnitude;<span class="kom">// Vektor / velikost</span></p>
<p class="src1">vNormal.y := vNormal.y / Magnitude;</p>
<p class="src1">vNormal.z := vNormal.z / Magnitude;</p>
<p class="src1">Result := vNormal;</p>
<p class="src0">end;</p>

<p>Funkce AddVector vrací souèet dvou vektorù.</p>

<p class="src0">function AddVector(vVector1, vVector2: CVector3): CVector3;<span class="kom">// Souèet vektorù</span></p>
<p class="src0">var</p>
<p class="src1">vResult: CVector3;</p>
<p class="src0">begin</p>
<p class="src1">vResult.x := vVector2.x + vVector1.x;<span class="kom">// Vektor 1 + Vektor 2</span></p>
<p class="src1">vResult.y := vVector2.y + vVector1.y;</p>
<p class="src1">vResult.z := vVector2.z + vVector1.z;</p>
<p class="src1">Result := vResult;</p>
<p class="src0">end;</p>

<p>Funkce DivideVectorByScaler vydìlí vektor èíslem a tím ho zkrátí popø. prodlou¾í.</p>

<p class="src0">function DivideVectorByScaler(vVector1: CVector3; Scaler: Double): CVector3;<span class="kom">// Dìlení vektoru èíslem</span></p>
<p class="src0">var</p>
<p class="src1">vResult: CVector3;</p>
<p class="src0">begin</p>
<p class="src1">vResult.x := vVector1.x / Scaler;</p>
<p class="src1">vResult.y := vVector1.y / Scaler;</p>
<p class="src1">vResult.z := vVector1.z / Scaler;</p>
<p class="src1">Result := vResult;</p>
<p class="src0">end;</p>

<p>Dále následují vlastní metody tøídy CLoad3DS. V proceduøe CleanUp se provádí ve¹kerý potøebný úklid - uvolnìní pamìti, uzavøení souborù...</p>

<p class="src0"><span class="kom">{ CLoad3DS }</span></p>
<p class="src"></p>
<p class="src0">procedure CLoad3DS.CleanUp;<span class="kom">// Úklid</span></p>
<p class="src0">begin</p>
<p class="src1">if m_FilePointer &lt;&gt; -1 then<span class="kom">// Máme otevøen soubor?</span></p>
<p class="src1">begin</p>
<p class="src2">FileClose(m_FilePointer);<span class="kom">// Zavøeme ho</span></p>
<p class="src2">m_FilePointer := -1;</p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Procedura ComputeNormals slou¾í pro výpoèet normál vertexù. Na zaèátku zkontrolujeme, zda máme v modelu alespoò jeden objekt. Pokud ne, nemá smysl pokraèovat a proceduru ukonèíme. V cyklu projdeme v¹echny objekty modelu.</p>

<p class="src0">procedure CLoad3DS.ComputeNormals(pModel: t3DModel);<span class="kom">// Výpoèet normál</span></p>
<p class="src0">var</p>
<p class="src1">vVector1, vVector2, vNormal: CVector3;</p>
<p class="src1">vPoly: array [0..2] of CVector3;</p>
<p class="src1">index, i, j: integer;</p>
<p class="src1">pObject: Pt3DObject;</p>
<p class="src1">pNormals, pTempNormals: array of CVector3;</p>
<p class="src1">vSum, vZero: CVector3;</p>
<p class="src1">shared: integer;</p>
<p class="src0">begin</p>
<p class="src1">if pModel.numOfObjects &lt;= 0 then exit;<span class="kom">// Pokud nemáme objekt tak konèíme</span></p>
<p class="src"></p>
<p class="src1">for index := 0 to pModel.numOfObjects - 1 do<span class="kom">// Cyklus pøes v¹echny objekty modelu</span></p>
<p class="src1">begin</p>

<p>Ulo¾íme si objekt do pomocné promìnné a nastavíme velikost polí pro výpoèet normál.</p>

<p class="src2">pObject := @pModel.pObject[index];<span class="kom">// Získá aktuální objekt</span></p>
<p class="src"></p>
<p class="src2">SetLength(pNormals, pObject.numOfFaces);<span class="kom">// Alokace potøebné pamìti</span></p>
<p class="src2">SetLength(pTempNormals, pObject.numOfFaces);</p>
<p class="src2">SetLength(pObject.pNormals, pObject.numOfVerts);</p>

<p>Projdeme v¹echny plo¹ky objektu. Pro pøehlednost si ulo¾íme ka¾dý vertex trojúhelníku do samostatné promìnné a vypoèítáme normálu plo¹ky (získáme dva vektory a z nich spoèítáme normálu). Je¹tì ne¾ z normály udìláme jednotkový vektor, ulo¾íme ji do pomocného pole.</p>

<p class="src2">for i := 0 to pObject.numOfFaces - 1 do<span class="kom">// Cyklus pøes v¹echny plo¹ky objektu</span></p>
<p class="src2">begin</p>
<p class="src3">vPoly[0] := pObject.pVerts[pObject.pFaces[i].vertIndex[0]];<span class="kom">// Pro pøehlednost ulo¾í 3 vertexy do pomocné promìnné</span></p>
<p class="src3">vPoly[1] := pObject.pVerts[pObject.pFaces[i].vertIndex[1]];</p>
<p class="src3">vPoly[2] := pObject.pVerts[pObject.pFaces[i].vertIndex[2]];</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Vlastní výpoèet normál</span></p>
<p class="src3">vVector1 := Vector(vPoly[0], vPoly[2]);<span class="kom">// Výpoèet 1. vektoru</span></p>
<p class="src3">vVector2 := Vector(vPoly[2], vPoly[1]);<span class="kom">// Výpoèet 2. vektoru</span></p>
<p class="src3">vNormal := Cross(vVector1, vVector2);<span class="kom">// Výpoèet normály</span></p>
<p class="src"></p>
<p class="src3">pTempNormals[i] := vNormal;<span class="kom">// Ulo¾í nenormalizovanou normálu pro pozdìj¹í výpoèty normál vertexù</span></p>
<p class="src3">vNormal := Normalize(vNormal);<span class="kom">// Normalizuje normálu</span></p>
<p class="src3">pNormals[i] := vNormal;<span class="kom">// Ulo¾í normálu do pole</span></p>
<p class="src2">end;</p>

<p>Teï zbývá jen vypoèítat normály jednotlivých vertexù. V prvním cyklu projdeme v¹echny vertexy a v dal¹ím zjistíme, ke kolika plo¹kám daný vertex nále¾í. Pro ka¾dý vertex projdeme v¹echny plo¹ky a pokud najdeme nìjakou plo¹ku, ke které patøí, zvý¹íme &quot;váhu&quot; pøíslu¹ného vertexu o normálu plo¹ky (pou¾ijeme døíve ulo¾ený normálový vektor, pøed tím, ne¾ jsme z nìj udìlali jednotkový). Nakonec z normálového vektoru vertexu udìláme jednotkový. Není to tak hrozné, jak to z pøedchozího textu vypadá. V¹e je vidìt na obrázku:</p>

<div class="okolo_img">
<img src="images/clanky/cl_gl_3ds/1.jpg" width="380" height="275" alt="Výpoèet normál vertexù" />
<div>Bod 1 nále¾í jen jedné plo¹ce - normálový vektor vertexu bude shodný s vektorem plo¹ky.</div>
<div>Bod 2 nále¾í ke dvìma plo¹kám - jeho normálový vektor bude slo¾en z vektorù obou plo¹ek.</div>
<div>Bod 3 nále¾í ke tøem plo¹kám - jeho normálový vektor bude slo¾en z vektorù v¹ech tøí plo¹ek.</div>
</div>

<p class="src"></p>
<p class="src2"><span class="kom">// Výpoèet normál vertexù</span></p>
<p class="src2">ZeroMemory(@vSum, sizeof(vSum));</p>
<p class="src2">vZero := vSum;</p>
<p class="src2">shared := 0;</p>
<p class="src"></p>
<p class="src2">for i := 0 to pObject.numOfVerts - 1 do<span class="kom">// Cyklus pøes v¹echny vertexy</span></p>
<p class="src2">begin</p>
<p class="src3">for j := 0 to pObject.numOfFaces - 1 do<span class="kom">// Cyklus pøes v¹echny plo¹ky (trojúhelníky)</span></p>
<p class="src4">if (pObject.pFaces[j].vertIndex[0] = i) or<span class="kom">// Je vertex sdílen s jinou plo¹kou?</span></p>
<p class="src5">(pObject.pFaces[j].vertIndex[1] = i) or</p>
<p class="src5">(pObject.pFaces[j].vertIndex[2] = i) then</p>
<p class="src4">begin</p>
<p class="src5">vSum := AddVector(vSum, pTempNormals[j]);<span class="kom">// Pøiète nenormalizovanou normálu</span></p>
<p class="src5">Inc(shared);<span class="kom">// Zvý¹í poèet sdílených trojúhelníkù</span></p>
<p class="src4">end;</p>
<p class="src"></p>
<p class="src3">pObject.pNormals[i] := DivideVectorByScaler(vSum, -shared);<span class="kom">// Získá normálu</span></p>
<p class="src3">pObject.pNormals[i] := Normalize(pObject.pNormals[i]);<span class="kom">// Normalizuje normálu</span></p>
<p class="src3">vSum := vZero;</p>
<p class="src3">shared := 0;</p>
<p class="src2">end;</p>
<p class="src"></p>
<p class="src2">SetLength(pTempNormals, 0);<span class="kom">// Uvolní pamì»</span></p>
<p class="src2">SetLength(pNormals, 0);</p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Konstruktor - zde se inicializují promìnné.</p>

<p class="src0">constructor CLoad3DS.Create;<span class="kom">// Konstruktor</span></p>
<p class="src0">begin</p>
<p class="src1">m_FilePointer := -1;</p>
<p class="src0">end;</p>

<p>Destruktor - zde se uvolòují prostøedky pou¾ívané promìnnými.</p>

<p class="src0">destructor CLoad3DS.Destroy;<span class="kom">// Destruktor</span></p>
<p class="src0">begin
<p class="src1">inherited;
<p class="src0">end;

<p>Funkce GetString naète øetìzec ze souboru do promìnné pBuffer a vrací jeho délku. Princip je jednoduchý, èteme znak po znaku, dokud nenarazíme na konec øetìzce (#0).</p>

<p class="src0">function CLoad3DS.GetString(var pBuffer: string): integer;<span class="kom">// Naète øetìzec</span></p>
<p class="src0">var</p>
<p class="src1">index: integer;</p>
<p class="src1">tmpChar: Char;</p>
<p class="src0">begin</p>
<p class="src1">index := 1;</p>
<p class="src1">FileRead(m_FilePointer, tmpChar, 1);<span class="kom">// Naète první znak</span></p>
<p class="src1">pBuffer := pBuffer + tmpChar;</p>
<p class="src"></p>
<p class="src1">while pBuffer[index] &lt;&gt; #0 do<span class="kom">// Kontroluje, zda jsme na konci øetìzce (#0)</span></p>
<p class="src1">begin</p>
<p class="src2">FileRead(m_FilePointer, tmpChar, 1);<span class="kom">// Naète dal¹í znak</span></p>
<p class="src2">pBuffer := pBuffer + tmpChar;</p>
<p class="src2">Inc(index);</p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">Result := Length(pBuffer);<span class="kom">// Vrací délku øetìzce</span></p>
<p class="src0">end;</p>

<p>Funkce Import3DS nahraje model ze souboru. Na zaèátku se pokusíme otevøít soubor. Pokud nastala chyba, zobrazíme chybovou zprávu a ukonèíme funkci.</p>

<p class="src0">function CLoad3DS.Import3DS(var pModel: t3DModel; strFileName: string): boolean;<span class="kom">// Nahraje model</span></p>
<p class="src0">var</p>
<p class="src1">strMessage: PAnsiChar;</p>
<p class="src1">currentChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">ZeroMemory(@currentChunk, sizeof(currentChunk));</p>
<p class="src1">m_FilePointer := FileOpen(strFileName, fmOpenRead);<span class="kom">// Otevøe 3DS soubor</span></p>
<p class="src"></p>
<p class="src1">if m_FilePointer = -1 then<span class="kom">// Pokud nastala chyba, zobrazíme zprávu</span></p>
<p class="src1">begin</p>
<p class="src2">strMessage := PAnsiChar(Format('Unable to find the file: %s!', [strFileName]));</p>
<p class="src2">MessageBox(0, strMessage, 'Error', MB_OK);</p>
<p class="src2">Result := false;</p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pokud se nám podaøilo soubor otevøít, naèteme hlavièku prvního bloku. Podle identifikátoru bloku zjistíme, zda se jedná o primární blok. Pokud ne, pak se nejedná o 3ds soubor, a proto zobrazíme chybovou hlá¹ku, uklidíme po sobì a ukonèíme funkci.</p>

<p class="src1">ReadChunk(currentChunk);<span class="kom">// Naète první blok</span></p>
<p class="src"></p>
<p class="src1">if currentChunk.ID &lt;&gt; PRIMARY then<span class="kom">// Pokud máme jiný ne¾ primární blok, nejedná se o 3DS soubor</span></p>
<p class="src1">begin</p>
<p class="src2">strMessage := PAnsiChar(Format('Unable to load PRIMARY chunk from file: %s!', [strFileName]));</p>
<p class="src2">MessageBox(0, strMessage, 'Error', MB_OK);</p>
<p class="src2">CleanUp;</p>
<p class="src2">Result := false;</p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pomocí procedury ProcessNextChunk, která je volána rekurzivnì (volá sama sebe), projdeme v¹echny bloky v souboru. Potom spoèítáme normály a uklidíme.</p>

<p class="src1">ProcessNextChunk(pModel, currentChunk);<span class="kom">// Naète objekty, procedura je volána rekurzivnì</span></p>
<p class="src1">ComputeNormals(pModel);<span class="kom">// Výpoèet normál vrcholù</span></p>
<p class="src1">CleanUp;<span class="kom">// Úklid</span></p>
<p class="src1">Result := true;</p>
<p class="src0">end;</p>

<p>Procedura ProcessNextChunk naèítá jednotlivé bloky souboru.</p>

<p class="src0">procedure CLoad3DS.ProcessNextChunk(var pModel: t3DModel; var pPreviousChunk: tChunk);<span class="kom">// Naète dal¹í bloky</span></p>
<p class="src0">var</p>
<p class="src1">currentChunk, tempChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">ZeroMemory(@currentChunk, sizeof(currentChunk));</p>
<p class="src1">ZeroMemory(@tempChunk, sizeof(tempChunk));</p>

<p>Nejdøíve otestujeme, jestli u¾ nejsme na konci bloku. Pokud ne, naèteme hlavièku podbloku.</p>

<p class="src1">while pPreviousChunk.bytesRead &lt; pPreviousChunk.length do<span class="kom">// Cyklus dokud nenaèteme celý blok</span></p>
<p class="src1">begin</p>
<p class="src2">ReadChunk(currentChunk);<span class="kom">// Naète dal¹í podblok</span></p>

<p>Rozvìtvíme kód podle jednotlivých identifikátorù blokù.</p>

<p class="src2">case currentChunk.ID of<span class="kom">// Vìtvení podle hlavièky bloku</span></p>

<p>Blok s informací o verzi souboru. Verzi naèteme do bufferu a zkontrolujeme, jestli není vy¹¹í ne¾ 3. Pokud ano, zobrazíme varovnou hlá¹ku o mo¾né nekompatibilitì formátu.</p>

<p class="src3">VERSION:<span class="kom">// Informace o verzi souboru</span></p>
<p class="src3">begin</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src"></p>
<p class="src4">if (currentChunk.length - currentChunk.bytesRead = 4) and (gBuffer[0] &gt; $03) then<span class="kom">// Pokud je verze vy¹¹í ne¾ 3, zobrazíme varování</span></p>
<p class="src5">MessageBox(0, 'This 3DS file is over version 3 so it may load incorrectly', 'Warning', MB_OK);</p>
<p class="src3">end;</p>

<p>Blok OBJECTINFO je pouze kontejner, který obsahuje dal¹í bloky s informacemi o objektu. Hned proto naèteme hlavièku podbloku a rekurzivnì zavoláme proceduru ProcessNextChunk.</p>

<p class="src3">OBJECTINFO:<span class="kom">// Hlavièka objektu</span></p>
<p class="src3">begin</p>
<p class="src4">ReadChunk(tempChunk);<span class="kom">// Naète dal¹í podblok</span></p>
<p class="src4">tempChunk.bytesRead := tempChunk.bytesRead + FileRead(m_FilePointer, gBuffer, tempChunk.length - tempChunk.bytesRead);</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + tempChunk.bytesRead;</p>
<p class="src4">ProcessNextChunk(pModel, currentChunk);<span class="kom">// Naète dal¹í informace o objektu</span></p>
<p class="src3">end;</p>

<p>Blok MATERIAL je také pouze kontejner, uvozuje ka¾dý materiál pou¾itý na objektu. Zvý¹íme tedy poèet materiálù a naèteme dal¹í informace o materiálu.</p>

<p class="src3">MATERIAL:<span class="kom">// Informace o materiálu</span></p>
<p class="src3">begin</p>
<p class="src4">Inc(pModel.numOfMaterials);<span class="kom">// Zvý¹í poèet materiálù</span></p>
<p class="src4">SetLength(pModel.pMaterials, Length(pModel.pMaterials) + 1);</p>
<p class="src4">ProcessNextMaterialChunk(pModel, currentChunk);<span class="kom">// Naète dal¹í informace o materiálu</span></p>
<p class="src3">end;</p>

<p>Blok OBJEKT je opìt jenom kontejner, uvozuje ka¾dý objekt pou¾itý v modelu. Proto zvý¹íme poèet objektù, získáme jméno objektu a naèteme zbývající informace o objektu.</p>

<p class="src3">OBJEKT:<span class="kom">// Informace o objektu</span></p>
<p class="src3">begin</p>
<p class="src4">Inc(pModel.numOfObjects);<span class="kom">// Zvý¹í poèet objektù</span></p>
<p class="src4">SetLength(pModel.pObject, Length(pModel.pObject) + 1);</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + GetString(pModel.pObject[pModel.numOfObjects - 1].strName);<span class="kom">// Naète jméno objektu</span></p>
<p class="src4">ProcessNextObjectChunk(pModel, pModel.pObject[pModel.numOfObjects - 1], currentChunk);<span class="kom">// Naète zbývající informace o objektu</span></p>
<p class="src3">end;</p>

<p>Blok EDITKEYFRAME obsahuje informace o klíèových snímcích animace objektu. My ho nepou¾íváme, proto zahodíme zbytek bloku do bufferu.</p>

<p class="src3">EDITKEYFRAME:<span class="kom">// Klíèový snímek - nepou¾ito - pøeskoèíme</span></p>
<p class="src3">begin</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src3">end;</p>

<p>Pokud naèteme neznámý blok, zahodíme jeho obsah do bufferu.</p>

<p class="src3">else<span class="kom">// Pokud naèteme neznámý blok, ignorujeme ho</span></p>
<p class="src3">begin</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src3">end;</p>
<p class="src2">end;</p>

<p>Nakonec zvìt¹íme poèet pøeètených bytù.</p>

<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + currentChunk.bytesRead;<span class="kom">// Zvìt¹íme poèet pøeètených bytù</span></p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Procedura ProcessNextMaterialChunk naèítá informace o materiálu. Na zaèátku zkontrolujeme, jestli nejsme na konci bloku. Pokud ne, naèteme hlavièku podbloku a rozvìtvíme kód podle identifikátoru hlavièky.</p>

<p class="src0">procedure CLoad3DS.ProcessNextMaterialChunk(var pModel: t3DModel; var pPreviousChunk: tChunk);<span class="kom">// Naète informace o materiálu</span></p>
<p class="src0">var</p>
<p class="src1">currentChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">ZeroMemory(@currentChunk, sizeof(currentChunk));</p>
<p class="src"></p>
<p class="src1">while pPreviousChunk.bytesRead &lt; pPreviousChunk.length do<span class="kom">// Ète dokud nejsme na konci podbloku</span></p>
<p class="src1">begin</p>
<p class="src2">ReadChunk(currentChunk);<span class="kom">// Naète blok</span></p>
<p class="src2">case currentChunk.ID of<span class="kom">// Vìtvení podle hlavièky bloku</span></p>

<p>Blok MATNAME obsahuje jméno materiálu. Zavoláme funkci GetString, která ho naète.</p>

<p class="src3">MATNAME:<span class="kom">// Naète jméno materiálu</span></p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + GetString(pModel.pMaterials[pModel.numOfMaterials - 1].strName;</p>

<p>Blok MATDIFFUSE obsahuje informace o barvì objektu. Naèteme je samostatnou funkcí.</p>

<p class="src3">MATDIFFUSE:<span class="kom">// Naète barvu objektu</span></p>
<p class="src4">ReadColorChunk(pModel.pMaterials[pModel.numOfMaterials - 1], currentChunk);</p>

<p>Blok MATMAP obsahuje informace o materiálu. Voláme rekurzivnì.</p>

<p class="src3">MATMAP:<span class="kom">// Naète informace o materiálu</span></p>
<p class="src4">ProcessNextMaterialChunk(pModel, currentChunk);</p>

<p>Blok MATMAPFILE obsahuje jméno souboru obsahující texturu, která je pou¾ita v materiálu. Jméno souboru získáme funkcí GetString.</p>

<p class="src3">MATMAPFILE:<span class="kom">// Naète jméno souboru s texturou</span></p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + GetString(pModel.pMaterials[pModel.numOfMaterials - 1].strFile);</p>

<p>Pokud naèteme neznámý blok, zahodíme jeho obsah do bufferu.</p>

<p class="src3">else<span class="kom">// Ignorujeme neznámé bloky</span></p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src2">end;</p>

<p>Nakonec zvìt¹íme poèet pøeètených bytù.</p>

<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + currentChunk.bytesRead;<span class="kom">// Zvìt¹íme poèet pøeètených bytù</span></p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Procedura ProcessNextObjectChunk naèítá informace o objektu. Jako obvykle zkontrolujeme, jestli u¾ nejsme na konci bloku. Pokud ne, naèteme hlavièku podbloku a rozvìtvíme program podle identifikátoru hlavièky.</p>

<p class="src0">procedure CLoad3DS.ProcessNextObjectChunk(var pModel: t3DModel; var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète informace o objektu</span></p>
<p class="src0">var</p>
<p class="src1">currentChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">while pPreviousChunk.bytesRead &lt; pPreviousChunk.length do<span class="kom">// Ète do konce podbloku</span></p>
<p class="src1">begin</p>
<p class="src2">ZeroMemory(@currentChunk, sizeof(currentChunk));</p>
<p class="src2">ReadChunk(currentChunk);<span class="kom">// Naète dal¹í blok</span></p>
<p class="src"></p>
<p class="src2">case currentChunk.ID of<span class="kom">// Vìtvení podle hlavièky</span></p>

<p>Blok OBJECT_MESH obsahuje informace o objektu. Volána rekurzivnì.</p>

<p class="src3">OBJECT_MESH:<span class="kom">// Nový objekt</span></p>
<p class="src4">ProcessNextObjectChunk(pModel, pObject, currentChunk);<span class="kom">// Naète jeho informace</span></p>

<p>Blok OBJECT_VERTICES obsahuje vertexy objektu. Naèteme je funkcí ReadVertices.</p>

<p class="src3">OBJECT_VERTICES:<span class="kom">// Naète vertexy objektu</span></p>
<p class="src4">ReadVertices(pObject, currentChunk);</p>

<p>Blok OBJECT_FACES obsahuje plo¹ky objektu. Naèteme je funkcí ReadVertexIndices.</p>

<p class="src3">OBJECT_FACES:<span class="kom">// Naète plo¹ky objektu</span></p>
<p class="src4">ReadVertexIndices(pObject, currentChunk);</p>

<p>Blok OBJECT_MATERIAL obsahuje jméno pou¾itého materiálu. Naèteme ho funkcí ReadObjectMaterial.</p>

<p class="src3">OBJECT_MATERIAL:<span class="kom">// Naète jméno pou¾itého materiálu</span></p>
<p class="src4">ReadObjectMaterial(pModel, pObject, currentChunk);</p>

<p>Blok OBJECT_UV obsahuje texturové koordináty. Naèteme je funkcí ReadUVCoordinates.</p>

<p class="src3">OBJECT_UV:<span class="kom">// Naète texturové koordináty objektu</span></p>
<p class="src4">ReadUVCoordinates(pObject,currentChunk);</p>

<p>Ignorujeme neznámé bloky a inkrementujeme èítaè pøeètených bytù.</p>

<p class="src3">else<span class="kom">// Ignorujeme neznámé bloky</span></p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src2">end;</p>
<p class="src"></p>
<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + currentChunk.bytesRead;<span class="kom">// Zvìt¹íme poèet pøeètených bytù</span></p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Procedura ReadColorChunk naèítá barvu objektu. Zjistíme informace o bloku a na základì délky bloku pou¾ijeme vhodnou promìnnou pro ulo¾ení hodnot RGB. Originální kód poèítal jen s variantou, ¾e barva je ulo¾ena jako 3x byte (3x 1 byte). Ale já, kdy¾ jsem exportoval svùj model z programu Cinema 4D, jsem zjistil, ¾e je barva ukládaná jako 3x single (3x 4 byty). Proto jsem implementoval rozvìtvení.</p>

<p class="src0">procedure CLoad3DS.ReadColorChunk(var pMaterial: tMaterialInfo; var pChunk: tChunk);<span class="kom">// Naète barvu objektu</span></p>
<p class="src0">var</p>
<p class="src1">tempChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">ZeroMemory(@tempChunk, sizeof(tempChunk));</p>
<p class="src1">ReadChunk(tempChunk);<span class="kom">// Informace o bloku</span></p>
<p class="src1">pMaterial.bpc := tempChunk.length - tempChunk.bytesRead;</p>
<p class="src"></p>
<p class="src1">if pMaterial.bpc = 3 then<span class="kom">// Podle délky bloku naèteme barvu do pøíslu¹ného pole (3-byte, 12-single)</span></p>
<p class="src2">tempChunk.bytesRead := tempChunk.bytesRead + FileRead(m_FilePointer, pMaterial.colorub, pMaterial.bpc)</p>
<p class="src1">else</p>
<p class="src2">tempChunk.bytesRead := tempChunk.bytesRead + FileRead(m_FilePointer, pMaterial.colorf, pMaterial.bpc);</p>
<p class="src"></p>
<p class="src1">pChunk.bytesRead := pChunk.bytesRead + tempChunk.bytesRead;<span class="kom">// Zvìt¹íme poèet pøeètených bytù</span></p>
<p class="src0">end;</p>

<p>Procedura ReadChunk naète hlavièku dal¹ího bloku.</p>

<p class="src0">procedure CLoad3DS.ReadChunk(var pChunk: tChunk);<span class="kom">// Naète hlavièku bloku</span></p>
<p class="src0">begin</p>
<p class="src1">pChunk.bytesRead := FileRead(m_FilePointer, pChunk.ID, 2);<span class="kom">// ID bloku</span></p>
<p class="src1">pChunk.bytesRead := pChunk.bytesRead + FileRead(m_FilePointer, pChunk.length, 4);<span class="kom">// Délka bloku</span></p>
<p class="src0">end;</p>

<p>Procedura ReadObjectMaterial naète jméno pou¾itého materiálu. Potom v cyklu nastavíme u objektu identifikátor pou¾itého materiálu a pokud materiál obsahuje texturu, nastavíme pøíznak textury na TRUE.</p>

<p class="src0">procedure CLoad3DS.ReadObjectMaterial(pModel: t3DModel; var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète materiály</span></p>
<p class="src0">var</p>
<p class="src1">strMaterial: string;</p>
<p class="src1">i: integer;</p>
<p class="src0">begin</p>
<p class="src1">strMaterial := '';</p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + GetString(strMaterial);<span class="kom">// Jméno materiálu</span></p>
<p class="src"></p>
<p class="src1">for i := 0 to pModel.numOfMaterials - 1 do<span class="kom">// Projde v¹echny materiály</span></p>
<p class="src2">if strMaterial = pModel.pMaterials[i].strName then<span class="kom">// Na¹li jsme ná¹ materiál?</span></p>
<p class="src2">begin</p>
<p class="src3">pObject.materialID := i;<span class="kom">// Nastavíme jeho index</span></p>
<p class="src"></p>
<p class="src3">if pModel.pMaterials[i].strFile &lt;&gt; '' then<span class="kom">// Pokud existuje soubor s texturou</span></p>
<p class="src4">pObject.bHasTexture := true;<span class="kom">// Nastavíme pøíznak</span></p>
<p class="src"></p>
<p class="src3">break;</p>
<p class="src2">end</p>
<p class="src2">else</p>
<p class="src3">pObject.materialID := -1;<span class="kom">// Objekt nemá materiál</span></p>
<p class="src"></p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, Buffer, pPreviousChunk.length - pPreviousChunk.bytesRead);<span class="kom">// Zvìt¹íme poèet pøeètených bytù</span></p>
<p class="src0">end;</p>

<p>Procedura ReadUVCoordinates naèítá texturové koordináty. Nejdøíve zjistíme poèet koordinátù a nastavíme délku pole pro koordináty. V cyklu potom naèteme v¹echny koordináty.</p>

<p class="src0">procedure CLoad3DS.ReadUVCoordinates(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète texturové koordináty</span></p>
<p class="src0">var</p>
<p class="src1">i: integer;</p>
<p class="src0">begin</p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.numTexVertex, 2);<span class="kom">// Naète poèet koordinátù</span></p>
<p class="src1">SetLength(pObject.pTexVerts,pObject.numTexVertex);<span class="kom">// Nastaví délku pole</span></p>
<p class="src"></p>
<p class="src1">for i := 0 to pObject.numTexVertex - 1 do<span class="kom">// Naète v¹echny koordináty</span></p>
<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.pTexVerts[i], 8);</p>
<p class="src0">end;</p>

<p>Procedura ReadVertexIndices naèítá indexy do pole vertexù. Na zaèátku zjistíme poèet plo¹ek a nastavíme pro nì velikost pole. V cyklech projdeme v¹echny plo¹ky a jejich vrcholy a naèteme indexy pøíslu¹ných vertexù. U plo¹ek nás zajímají jen první tøi hodnoty pøedstavující indexy vrcholù, ètvrtá hodnota - viditelnost - je pro nás nezajímavá a tak ji pøeskoèíme.</p>

<p class="src0">procedure CLoad3DS.ReadVertexIndices(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète indexy do pole vertexù</span></p>
<p class="src0">var</p>
<p class="src1">index: word;</p>
<p class="src1">i, j: integer;</p>
<p class="src0">begin</p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.numOfFaces, 2);<span class="kom">// Naète poèet plo¹ek</span></p>
<p class="src1">SetLength(pObject.pFaces, pObject.numOfFaces);<span class="kom">// Nastaví velikost pole</span></p>
<p class="src1">ZeroMemory(pObject.pFaces, pObject.numOfFaces);</p>
<p class="src"></p>
<p class="src1">for i := 0 to pObject.numOfFaces - 1 do<span class="kom">// Cyklus pøes v¹echny plo¹ky</span></p>
<p class="src2">for j := 0 to 3 do<span class="kom">// Cyklus pøes vrcholy plo¹ek</span></p>
<p class="src2">begin</p>
<p class="src3">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, index, sizeof(index));<span class="kom">// Naète index vrcholu</span></p>
<p class="src3">if j &lt; 3 then</p>
<p class="src4">pObject.pFaces[i].vertIndex[j] := index;<span class="kom">// Ulo¾í index do pole</span></p>
<p class="src2">end;</p>
<p class="src0">end;</p>

<p>Procedura ReadVertices ète jednotlivé vertexy. Zjistíme poèet vertexù a nastavíme velikost pole. V cyklu naèteme v¹echny vertexy a prohodíme jejich osy Y a Z, proto¾e 3D Studio Max pou¾ívá jiný systém os ne¾ OpenGL. Navíc ze stejného dùvodu zmìníme orientaci u osy Z.</p>

<p class="src0">procedure CLoad3DS.ReadVertices(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Naète vertexy</span></p>
<p class="src0">var</p>
<p class="src1">i: integer;</p>
<p class="src1">fTempY: Double;</p>
<p class="src0">begin</p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.numOfVerts, 2);<span class="kom">// Naète poèet vertexù</span></p>
<p class="src1">SetLength(pObject.pVerts, pObject.numOfVerts);<span class="kom">// Nastaví velikost pole</span></p>
<p class="src1">ZeroMemory(pObject.pVerts, pObject.numOfVerts);</p>
<p class="src"></p>
<p class="src1">for i := 0 to pObject.numOfVerts - 1 do<span class="kom">// Cyklus pøes v¹echny vertexy</span></p>
<p class="src1">begin</p>
<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.pVerts[i], 12);</p>
<p class="src2">fTempY := pObject.pVerts[i].y;<span class="kom">// Prohodí Y a Z</span></p>
<p class="src2">pObject.pVerts[i].y := pObject.pVerts[i].z;</p>
<p class="src2">pObject.pVerts[i].z := -fTempY;<span class="kom">// Je¹tì zmìna orientace osy</span></p>
<p class="src1">end;</p>
<p class="src0">end;</p>
<p class="src"></p>
<p class="src0">end.</p>

<p>Tak, to bylo srdce celého naèítání 3ds modelu. Nyní u¾ jen upravíme NeHe kód aplikace a máme hotovo. Vysvìtlím jen doplnìní základního NeHe kódu, pro popis jeho kompletní struktury odkazuji na NeHe tutoriály. Na zaèátku pøidáme jednotku glaux pro podporu nahrávání bitmapových obrázkù a na¹i jednotku f_3ds, která naète 3ds model. Dále definujeme promìnné. g_Texture pøedstavuje pole pou¾itých textur, g_Load3ds je na¹e tøída 3ds a g_3DModel je promìnná, do které se naète 3ds model. g_ViewMode obsahuje zpùsob vykreslování vertexù (dále bude implementováno pøepínání mezi klasickým a drátìným modelem) a následují promìnné pro zapnutí/vypnutí osvìtlení a pro rotaci objektu. Nakonec zvolíme jméno nahrávaného souboru.</p>

<p class="src0">g_Texture: array of UINT;<span class="kom">// Pole textur</span></p>
<p class="src0">g_Load3ds: CLoad3DS;<span class="kom">// Tøída 3DS</span></p>
<p class="src0">g_3DModel: t3DModel;<span class="kom">// 3DS model</span></p>
<p class="src0">g_ViewMode: integer = GL_TRIANGLES;<span class="kom">// Zpùsob vykreslování</span></p>
<p class="src0">g_bLighting: boolean = true;<span class="kom">// Osvìtlení</span></p>
<p class="src0">g_RotateX: GLfloat = 0.0;<span class="kom">// Rotace</span></p>
<p class="src0">g_RotationSpeed: GLfloat = 0.8;<span class="kom">// Rychlost rotace</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">// V¾dy nechte odkomentovaný pouze jeden øádek, ostatní zakomentujte!!!</span></p>
<p class="src0">FILE_NAME: string = 'face.3ds';<span class="kom">// Soubor, který budeme nahrávat - originál</span></p>
<p class="src0"><span class="kom">//FILE_NAME: string = 'muz.3ds';// Pøedpøipravený objekt v Cinema 4D</span></p>
<p class="src0"><span class="kom">//FILE_NAME: string = 'snehulak.3ds';// Mùj výtvor</span></p>

<p>Vytvoøíme proceduru CreateTexture, která se bude starat o nahrávání textur. Jako parametry jí pøedáme pole textur, jméno souboru s obrázkem a identifikátor textury (index do pole textur). Následuje standardní kód na vytvoøení mipmapované textury z bitmapového obrázku. Tedy nejdøíve naèteme obrázek do pomocné promìnné, zavoláme funkci glGenTextures a necháme si vygenerovat jednu texturu. Nastavíme zpùsob interpretace naètených dat - funkce glPixelStorei, zvolíme vytvoøenou texturu - glBindTexture, pøevedeme obrázek na mipmapovou texturu - gluBuild2DMipmaps a nakonec nastavíme její parametry - glTexParameteri.</p>

<p class="src0">procedure CreateTexture(var textureArray: array of UINT; strFileName: LPSTR; textureID: integer);<span class="kom">// Vytvoøení textury</span></p>
<p class="src0">var</p>
<p class="src1">pBitmap: PTAUX_RGBImageRec;<span class="kom">// Pomocná promìnná pro nahrání bitmapy</span></p>
<p class="src0">begin</p>
<p class="src1">if strFileName = '' then exit;<span class="kom">// Bylo pøedáno jméno soubor?</span></p>
<p class="src1">pBitmap := auxDIBImageLoadA(strFileName);<span class="kom">// Naètení bitmapy</span></p>
<p class="src1">if pBitmap = nil then exit;<span class="kom">// Podaøilo se naèíst bitmapu?</span></p>
<p class="src"></p>
<p class="src1">glGenTextures(1, textureArray[textureID]);<span class="kom">// Generujeme 1 texturu</span></p>
<p class="src1">glPixelStorei(GL_UNPACK_ALIGNMENT, 1);<span class="kom">// Zpùsob interpretace dat v pamìti (1 - hodnoty typu byte)</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, textureArray[textureID]);<span class="kom">// Zvolí texturu</span></p>
<p class="src1">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, pBitmap.sizeX, pBitmap.sizeY, GL_RGB, GL_UNSIGNED_BYTE, pBitmap.data);<span class="kom">// Vytvoøí mipmapovou texturu</span></p>
<p class="src"></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR_MIPMAP_NEAREST);<span class="kom">// Zpùsob filtrování</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR_MIPMAP_LINEAR);</p>
<p class="src0">end;</p>

<p>Do funkce Initialize pøidáme následující kód. Nejprve otestujeme, zda pøi spu¹tìní programu nebyl náhodou pøedán parametr (V C/C++ se jedná o parametry argc, argv funkce main().). Jako jediný mo¾ný parametr je jméno souboru s modelem. Pokud bylo pøedáno, má pøednost pøed jménem, které je nastaveno v programu &quot;natvrdo&quot;. Rozhodl jsem se to implementovat pro ty, kteøí pro otestování programu nechtìjí (nebo nemù¾ou - neprogramátoøi) spustit vývojové prostøedí a chtìjí zkusit nahrát jiný model. Pak staèí napsat: main.exe muz.3ds a místo standardního modelu face.3ds se nahraje model mu¾e.</p>

<p>Jen bych chtìl dopøedu upozornit, ¾e mé modely byly velikostnì upraveny tak, aby se nemuselo hýbat s viewportem. Pokud se rozhodnete naèíst svùj vlastní model, pak je velmi pravdìpodobné, ¾e budete muset zmìnit parametry funkce gluLookAt, která se nachází v proceduøe Draw a nastavuje pohled kamery!!! Pro &quot;jednoduchost&quot; tohoto ukázkového kódu jsem místo zmìny pohledu kamery radìji zvolil zmìnu velikosti vytváøeného objektu.</p>

<p class="src0"><span class="kom">// Funkce Initialize</span></p>
<p class="src1">if ParamCount &lt;&gt; 0 then FILE_NAME := ParamStr(1);<span class="kom">// Pokud byl programu pøedán soubor jako parametr, pak jej naèteme místo souboru, který je definovám pøímo v programu</span></p>

<p>Dále vytvoøíme instanci tøídy a nahrajeme model ze souboru. Podle poètu materiálù nastavíme velikost pole pro jejich textury. V cyklu projdeme v¹echny materiály a pokud obsahují textury, tak je vytvoøíme. Nakonec zapneme svìtlo nula, osvìtlení, barvu materiálu, mapování textur a testování hloubky.</p>

<p class="src1">ZeroMemory(@g_3DModel, sizeof(g_3DModel));<span class="kom">// Nulování pamìti</span></p>
<p class="src1">g_Load3ds := CLoad3DS.Create;<span class="kom">// Vytvoøí instanci tøídy 3DS</span></p>
<p class="src1">g_Load3ds.Import3DS(g_3DModel, FILE_NAME);<span class="kom">// Nahraje model</span></p>
<p class="src1">SetLength(g_Texture, g_3DModel.numOfMaterials);<span class="kom">// Nastaví velikost pole pro textury</span></p>
<p class="src"></p>
<p class="src1">for i := 0 to g_3DModel.numOfMaterials -1 do<span class="kom">// Cyklus pøes jednotlivé materiály</span></p>
<p class="src1">begin</p>
<p class="src2">if g_3DModel.pMaterials[i].strFile &lt;&gt; '' then<span class="kom">// Obsahuje materiál texturu?</span></p>
<p class="src3">CreateTexture(g_Texture, PChar(g_3DModel.pMaterials[i].strFile), i);<span class="kom">// Pokud ano, pak ji vytvoøíme</span></p>
<p class="src"></p>
<p class="src2">g_3DModel.pMaterials[i].texureId := i;<span class="kom">// Ulo¾í ID textury</span></p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne svìtlo</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne osvìtlení</span></p>
<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Povolí barvu materiálu</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Povolí mapování textur</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>

<p>V proceduøe Deinitialize se postaráme o uvolnìní alokovaných prostøedkù. V objektu nastavíme délku v¹ech dynamických polí na 0 - tím uvolníme pamì», kterou zabíraly. Uvolníme pamì» pro textury a zru¹íme instanci tøídy 3ds.</p>

<p class="src0"><span class="kom">// Funkce Deinitialize</span></p>
<p class="src1">for i := 0 to g_3DModel.numOfObjects - 1 do<span class="kom">// Projde v¹echny objekty v modelu</span></p>
<p class="src1">begin</p>
<p class="src2">SetLength(g_3DModel.pObject[i].pVerts, 0);<span class="kom">// Uvolní pole vertexù</span></p>
<p class="src2">SetLength(g_3DModel.pObject[i].pNormals, 0);<span class="kom">// Uvolní pole normál</span></p>
<p class="src2">SetLength(g_3DModel.pObject[i].pTexVerts, 0);<span class="kom">// Uvolní pole texturovaných vertexù</span></p>
<p class="src2">SetLength(g_3DModel.pObject[i].pFaces, 0);<span class="kom">// Uvolní pole facù</span></p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">glDeleteTextures(g_3DModel.numOfMaterials, @g_Texture);<span class="kom">// Uvolní pamì» pro textury</span></p>
<p class="src1">SetLength(g_Texture, 0);<span class="kom">// Uvolní pole textur</span></p>
<p class="src1">g_Load3ds.Free;<span class="kom">// Zru¹í instanci tøídy 3DS</span></p>

<p>Do procedury Update doplníme obsluhu stisku kláves. ©ipkami budeme ovlivòovat rotaci objektu.</p>

<p class="src0"><span class="kom">// Funkce Update</span></p>
<p class="src1">if g_keys.keyDown[VK_LEFT] then<span class="kom">// ©ipka vlevo</span></p>
<p class="src2">g_RotationSpeed := g_RotationSpeed - 0.05;<span class="kom">// Úprava rotace</span></p>
<p class="src"></p>
<p class="src1">if g_keys.keyDown[VK_RIGHT] then<span class="kom">// ©ipka vpravo</span></p>
<p class="src2">g_RotationSpeed := g_RotationSpeed + 0.05;<span class="kom">// Úprava rotace</span></p>

<p>Ve¹keré vykreslování se provádí v proceduøe Draw. Ne jejím zaèátku sma¾eme obrazovku a hloubkový buffer a resetujeme matici.</p>

<p class="src0">procedure Draw;<span class="kom">// Vykreslení scény</span></p>
<p class="src0">var</p>
<p class="src1">i, j, whichVertex: integer;<span class="kom">// Cykly</span></p>
<p class="src1">pObject: t3DObject;<span class="kom">// Objekt</span></p>
<p class="src1">index: integer;<span class="kom">// Index do pole vertexù</span></p>
<p class="src0">begin</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT or GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity;<span class="kom">// Reset matice</span></p>

<p>Dále nastavíme pohled kamery, rotaci modelu a aktualizujeme úhel natoèení modelu.</p>

<p class="src1">gluLookAt(0,1.5,8, 0,0.5,0, 0,1,0);<span class="kom">// Nastavení pohledu kamery</span></p>
<p class="src1">glRotatef(g_RotateX, 0, 1.0, 0);<span class="kom">// Rotace modelu</span></p>
<p class="src1">g_RotateX := g_RotateX + g_RotationSpeed;<span class="kom">// Aktualizace úhlu natoèení</span></p>

<p>Vykreslíme v¹echny objekty modelu. Na zaèátku cyklu testujeme, zda model obsahuje objekty a pokud ne, tak cyklus ukonèíme. Objekt ulo¾íme do pomocné promìnné a otestujeme, jestli má texturu. Pokud ano, zapneme mapování textur a pøíslu¹nou texturu zvolíme. Pokud ne, vypneme mapování textur.</p>

<p class="src1">for i := 0 to g_3DModel.numOfObjects - 1 do<span class="kom">// Projde v¹echny objekty v modelu</span></p>
<p class="src1">begin</p>
<p class="src2">if Length(g_3DModel.pObject) = 0 then break;<span class="kom">// Pokud model neobsahuje ¾ádný objekt, ukonèíme cyklus</span></p>
<p class="src3">pObject := g_3DModel.pObject[i];<span class="kom">// Ulo¾íme objekt do pomocné promìnné</span></p>
<p class="src"></p>
<p class="src2">if pObject.bHasTexture then<span class="kom">// Má objekt texturu?</span></p>
<p class="src2">begin<span class="kom">// Pokud ano</span></p>
<p class="src3">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapování textur</span></p>
<p class="src3">glColor3ub(255, 255, 255);</p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, g_Texture[pObject.materialID]);<span class="kom">// Zvolí pøíslu¹nou texturu</span></p>
<p class="src2">end</p>
<p class="src2">else<span class="kom">// Pokud ne</span></p>
<p class="src2">begin</p>
<p class="src3">glDisable(GL_TEXTURE_2D);<span class="kom">// Vypne mapování textur</span></p>
<p class="src3">glColor3ub(255, 255, 255);</p>
<p class="src2">end;</p>

<p>Projdeme v¹echny trojúhelníky, nastavíme normály a pokud má objekt texturu nastavíme i texturové koordináty. Pokud objekt texturu nemá, nastavíme jeho barvu. Nakonec vykreslíme vlastní vertexy.</p>

<p class="src2">glBegin(g_ViewMode);<span class="kom">// Zaèátek vykreslování vertexù</span></p>
<p class="src"></p>
<p class="src2">for j := 0 to pObject.numOfFaces - 1 do<span class="kom">// Projde v¹echny trojúhelníky</span></p>
<p class="src3">for whichVertex := 0 to 2 do<span class="kom">// Projde v¹echny vrcholy trojúhelníkù</span></p>
<p class="src3">begin</p>
<p class="src4">index := pObject.pFaces[j].vertIndex[whichVertex];<span class="kom">// Index do pole vertexù</span></p>
<p class="src4">glNormal3f(pObject.pNormals[index].x, pObject.pNormals[index].y, pObject.pNormals[index].z);<span class="kom">// Nastaví normálu</span></p>
<p class="src"></p>
<p class="src4">if pObject.bHasTexture then<span class="kom">// Má objekt texturu?</span></p>
<p class="src4">begin</p>
<p class="src5">if Assigned(pObject.pTexVerts) then<span class="kom">// Má objekt texturové koordináty?</span></p>
<p class="src6">glTexCoord2f(pObject.pTexVerts[index].x, pObject.pTexVerts[index].y);<span class="kom">// Nastaví texturové koordináty</span></p>
<p class="src4">end</p>
<p class="src4">else</p>
<p class="src4">begin</p>
<p class="src5">if (Length(g_3DModel.pMaterials) &lt;&gt; 0) and (pObject.materialID &gt;= 0) then<span class="kom">// Kdy¾ objekt nemá texturu má alespoò materiál?</span></p>
<p class="src6">if g_3DModel.pMaterials[pObject.materialID].bpc = 3 then<span class="kom">// Podle poètu bytù na barevný kanál, pou¾ijeme vhodnou funkci pro nastavení barvy</span></p>
<p class="src7">glColor3ubv(@g_3DModel.pMaterials[pObject.materialID].colorub)</p>
<p class="src6">else</p>
<p class="src7">glColor3fv(@g_3DModel.pMaterials[pObject.materialID].colorf);</p>
<p class="src4">end;</p>
<p class="src"></p>
<p class="src4">glVertex3f(pObject.pVerts[index].x, pObject.pVerts[index].y, pObject.pVerts[index].z);<span class="kom">// Nakonec vykreslíme vertex</span></p>
<p class="src3">end;</p>
<p class="src"></p>
<p class="src2">glEnd;<span class="kom">// Konec vykreslování vertexù</span></p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">glFlush;<span class="kom">// Vyprázdní OpenGL renderovací pipeline</span></p>
<p class="src0">end;</p>

<p>Do funkce WindowProc pøidáme podporu my¹i. Pøi stisku levého tlaèítka my¹i dojde ke zmìnì módu vykreslování na drátìný model a zpìt. Stiskem pravého tlaèítka my¹i se zapíná/vypíná osvìtlení.</p>

<p class="src0"><span class="kom">// WindowProc</span></p>
<p class="src2">WM_LBUTTONDOWN:<span class="kom">// Obsluha levého tlaèítka my¹i</span></p>
<p class="src2">begin</p>
<p class="src3">if g_ViewMode = GL_TRIANGLES then</p>
<p class="src4">g_ViewMode := GL_LINE_STRIP<span class="kom">// Zmìna módu vykreslování</span></p>
<p class="src3">else</p>
<p class="src4">g_ViewMode := GL_TRIANGLES;</p>
<p class="src"></p>
<p class="src3">Result := 0;</p>
<p class="src2">end;</p>
<p class="src"></p>
<p class="src2">WM_RBUTTONDOWN:<span class="kom">// Obsluha pravého tlaèítka my¹i</span></p>
<p class="src2">begin</p>
<p class="src3">g_bLighting := not g_bLighting;<span class="kom">// Zapne/vypne osvìtlení</span></p>
<p class="src"></p>
<p class="src3">if g_bLighting then</p>
<p class="src4">glEnable(GL_LIGHTING)</p>
<p class="src3">else</p>
<p class="src4">glDisable(GL_LIGHTING);</p>
<p class="src"></p>
<p class="src3">Result := 0;</p>
<p class="src2">end;

<p>Tak a máme hotovo. Teï u¾ jen zbývá ná¹ výtvor spustit. Po nìkolika probdìlých nocích s hexavýpisem souboru a kalkulaèkou v ruce a dal¹ích volných chvílích dne, jsem se koneènì dobral výsledku. Uff!!! :-)))</p>

<p class="autor">napsal: Michal Tuèek <?VypisEmail('michal_praha@seznam.cz');?>, 23.08.2004</p>

<p>C++ pøedloha zdrojového kódu pro èlánek: <?OdkazBlank('http://www.gametutorials.com/');?> (Pozn.: <span class="warning">Nìkteré tutoriály na gametutorials.com zaèaly být placené!)</span></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_gl_3ds.zip');?> - Delphi 7</li>
<li><?OdkazDown('download/clanky/cl_gl_3ds_cpp_sdl.zip');?> - C++, SDL</li>
</ul>

<div class="okolo_img"><img src="images/clanky/cl_gl_3ds/2.jpg" width="604" height="474" alt="Model tváøe" /></div>
<div class="okolo_img"><img src="images/clanky/cl_gl_3ds/3.jpg" width="604" height="475" alt="Model snìhuláka" /></div>
<div class="okolo_img"><img src="images/clanky/cl_gl_3ds/4.jpg" width="604" height="475" alt="Model mu¾e" /></div>

<?
include 'p_end.php';
?>
