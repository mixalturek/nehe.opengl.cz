<?
$g_title = 'CZ NeHe OpenGL - Na��t�n� .3DS model�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Na��t�n� .3DS model�</h1>

<p class="nadpis_clanku">V�tomto �l�nku si uk�eme, jak nahr�t a vykreslit model ve form�tu .3DS (3D Studio Max). N� k�d bude um�t bez probl�m� na��tat soubory do t�et� verze programu, s vy���mi verzemi bude pracovat tak�, ale nebude podporovat jejich nov� funkce. Vych�z�m z�uk�zkov�ho p��kladu z www.gametutorials.com, kde tak� najdete zdrojov� k�dy pro C++ (�l�nek je v Delphi).</p>

<p>Hned na �vod bude dobr� ��ct, �e 3D Studio Max nem�m a modely, kter� jsem zkou�el, jsou exportovan� z programu Cinema 4D. U� p�i testov�n� jsem narazil na rozd�ly ve form�tu - nap�. p�vodn� model ukl�d� barvu jako 3x byte, Cinema 4D jako 3x single, proto nem��u zaru�it 100% kompatibilitu.</p>

<p>Program je postaven na NeHe k�du z�posledn�ch lekc�. Provedl jsem jen n�kter� drobn� �pravy jako nap�. zaveden� prom�nn�ch pro rozm�ry okna atp., ale nebudu ho tu podrobn� popisovat. Zam���m se hlavn� na na��t�n� 3ds souboru.</p>

<p>V�e pot�ebn� se nach�z� v jednotce f_3ds.pas. Na za��tku definujeme konstanty, kter� p�edstavuj� identifik�tory jednotliv�ch blok� v souboru. Ka�d� 3ds soubor se skl�d� z ur�it�ch ��st� - blok�, kter� v sob� uchov�vaj� r�zn� informace o modelu. Ka�d� blok obsahuje identifik�tor, svoji d�lku a vlastn� data. N�kter� bloky slou�� pouze jako kontejnery a obsahuj� v�t�� �i men�� po�et jin�ch blok�. Ne v�echny jsou v�ak zdokumentovan�, ale to nevad�, proto�e takov� bloky je mo�n� d�ky znalosti jejich d�lky p�esko�it. Podrobn� popis struktury souboru se nach�z� v p�ilo�en� dokumentaci.</p>

<p class="src0">unit f_3ds;
<p class="src"></p>
<p class="src0">interface
<p class="src"></p>
<p class="src0">const<span class="kom">// Konstanty hlavi�ek jednotliv�ch blok�</span></p>
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

<p>D�le definujeme n�kolik struktur. CVector3 uchov�v� sou�adnice vertexu, CVector2 ukl�d� texturov� koordin�ty.</p>

<p class="src0">type</p>
<p class="src1">CVector3 = record<span class="kom">// Vektor 3D</span></p>
<p class="src2">x, y, z: single;</p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">CVector2 = record<span class="kom">// Vektor 2D</span></p>
<p class="src2">x, y: single;</p>
<p class="src1">end;</p>

<p>Struktura tFace obsahuje informace o plo�ce (troj�heln�ku) objektu. Pou��vaj� se dv� pole index�, index do pole vertex� a index do pole texturov�ch koordin�t�. Ka�d� vertex je v souboru ulo�en pouze jednou a informace o plo�ce obsahuje pouze indexy jednotliv�ch vrchol�. Odpad� tak dublov�n� vertex�, proto�e ty jsou �asto sd�leny v�ce troj�heln�ky. Stejn� je to i s texturov�mi koordin�ty.</p>

<p class="src1">tFace = record<span class="kom">// Informace o plo�k�ch (troj�heln�c�ch) objektu</span></p>
<p class="src2">vertIndex: array [0..2] of integer;<span class="kom">// Index do pole vertex�</span></p>
<p class="src2">coordIndex: array [0..2] of integer;<span class="kom">// Index do pole texturov�ch koordin�t�</span></p>
<p class="src1">end;</p>

<p>Struktura tMaterialInfo obsahuje informace o materi�lu, jeho jm�no, cesta k souboru s texturou (pokud existuje), po�et byt� pou�it�ch pro vyj�d�en� barvy, pole pro barvu, identifik�tor textury a dal�� prom�nn� pro pr�ci s texturou, kter� ov�em nejsou v k�du vyu�ity.</p>

<p class="src1">tMaterialInfo = record<span class="kom">// Informace o materi�lu</span></p>
<p class="src2">strName: string;<span class="kom">// Jm�no materi�lu</span></p>
<p class="src2">strFile: string;<span class="kom">// Cesta k souboru s texturou</span></p>
<p class="src2">bpc: integer;<span class="kom">// Po�et byt� na barvu</span></p>
<p class="src2">colorub: array [0..2] of byte;<span class="kom">// Barva v bytech</span></p>
<p class="src2">colorf: array [0..2] of single;<span class="kom">// Barva v singlech</span></p>
<p class="src2">texureId: integer;<span class="kom">// ID textury</span></p>
<p class="src2">uTile: double;<span class="kom">// Opakov�n� textury v ose u (nepou�ito)</span></p>
<p class="src2">vTile: double;<span class="kom">// Opakov�n� textury v ose v (nepou�ito)</span></p>
<p class="src2">uOffset: double;<span class="kom">// Posunut� textury v ose u (nepou�ito)</span></p>
<p class="src2">vOffset: double;<span class="kom">// Posunut� textury v ose v (nepou�ito)</span></p>
<p class="src1">end;</p>

<p>Struktura t3DObject obsahuje informace o objektu - po�et vertex�, po�et plo�ek (troj�heln�k�), po�et texturov�ch koordin�t�, identifik�tor pou�it�ho materi�lu, flag textury (ano/ne), jm�no objektu, pole vertex�, pole norm�l, pole texturov�ch koordin�t� a pole plo�ek.</p>

<p class="src0">t3DObject = record<span class="kom">// Informace o objektu</span></p>
<p class="src1">numOfVerts: integer;<span class="kom">// Po�et vertex�</span></p>
<p class="src1">numOfFaces: integer;<span class="kom">// Po�et plo�ek</span></p>
<p class="src1">numTexVertex: integer;<span class="kom">// Po�et texturov�ch koordin�t�</span></p>
<p class="src1">materialID: integer;<span class="kom">// ID materi�lu</span></p>
<p class="src1">bHasTexture: boolean;<span class="kom">// TRUE, pokud materi�l obsahuje texturu</span></p>
<p class="src1">strName: string;<span class="kom">// Jm�no objektu</span></p>
<p class="src1">pVerts: array of CVector3;<span class="kom">// Vertexy</span></p>
<p class="src1">pNormals: array of CVector3;<span class="kom">// Norm�ly</span></p>
<p class="src1">pTexVerts: array of CVector2;<span class="kom">// Texturov� koordin�ty</span></p>
<p class="src1">pFaces: array of tFace;<span class="kom">// Plo�ky</span></p>
<p class="src0">end;</p>
<p class="src"></p>
<p class="src0">Pt3DObject = ^t3DObject;<span class="kom">// Ukazatel na objekt</span></p>

<p>Struktura t3DModel obsahuje informaci o modelu - po�et objekt�, po�et materi�l�, pole materi�l� a pole objekt�.</p>

<p class="src0">t3DModel = record<span class="kom">// Informace o modelu</span></p>
<p class="src1">numOfObjects: integer;<span class="kom">// Po�et objekt�</span></p>
<p class="src1">numOfMaterials: integer;<span class="kom">// Po�et materi�l�</span></p>
<p class="src1">pMaterials: array of tMaterialInfo;<span class="kom">// Pole materi�l�</span></p>
<p class="src1">pObject: array of t3DObject;<span class="kom">// Pole objekt�</span></p>
<p class="src0">end;</p>

<p>Struktura tChunk obsahuje informace o na��tan�m bloku. Identifik�tor, d�lku a po�et ji� p�e�ten�ch byt�.</p>

<p class="src0">tChunk = record<span class="kom">// Informace o bloku</span></p>
<p class="src1">ID: word;<span class="kom">// Identifik�tor bloku</span></p>
<p class="src1">length: cardinal;<span class="kom">// D�lka bloku</span></p>
<p class="src1">bytesRead: cardinal;<span class="kom">// Ji� p�e�ten� byty</span></p>
<p class="src0">end;</p>

<p>D�le n�sleduje t��da, kter� se star� o nahr�v�n� souboru. Mysl�m, �e koment��e u jednotliv�ch ��dk� hovo�� za v�e. K jednotliv�m metod�m se podrobn� vr�t�m n�e.</p>

<p class="src0">CLoad3DS = class<span class="kom">// T��da pro nahr�n� 3DS souboru</span></p>
<p class="src1">public</p>
<p class="src2">constructor Create;<span class="kom">// Konstruktor</span></p>
<p class="src2">destructor Destroy; override;<span class="kom">// Destruktor</span></p>
<p class="src2">function Import3DS(var pModel: t3DModel; strFileName: string): boolean<span class="kom">// Funkce pro nahr�n� souboru</span></p>
<p class="src"></p>
<p class="src1">private</p>
<p class="src2">m_FilePointer: integer;<span class="kom">// Ukazatel na soubor</span></p>
<p class="src2">function GetString(var pBuffer: string): integer;<span class="kom">// Na�te �et�zec</span></p>
<p class="src2">procedure ReadChunk(var pChunk: tChunk);<span class="kom">// Na�te dal�� blok</span></p>
<p class="src2">procedure ProcessNextChunk(var pModel: t3DModel; var pPreviousChunk: tChunk);<span class="kom">// Na�te dal�� soubor blok�</span></p>
<p class="src2">procedure ProcessNextObjectChunk(var pModel: t3DModel; var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te dal�� objektov� blok</span></p>
<p class="src2">procedure ProcessNextMaterialChunk(var pModel: t3DModel; var pPreviousChunk: tChunk);<span class="kom">// Na�te dal�� materi�lov� blok</span></p>
<p class="src2">procedure ReadColorChunk(var pMaterial: tMaterialInfo; var pChunk: tChunk);<span class="kom">// Na�te RGB barvu objektu</span></p>
<p class="src2">procedure ReadVertices(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te vertexy objektu</span></p>
<p class="src2">procedure ReadVertexIndices(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te plo�ky objektu</span></p>
<p class="src2">procedure ReadUVCoordinates(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te texturov� koordin�ty objektu</span></p>
<p class="src2">procedure ReadObjectMaterial(pModel: t3DModel; var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te materi�l objektu</span></p>
<p class="src2">procedure ComputeNormals(pModel: t3DModel);<span class="kom">// Vypo��t� norm�ly</span></p>
<p class="src2">procedure CleanUp;<span class="kom">// Uvoln� prost�edky a uzav�e soubor</span></p>
<p class="src0">end;</p>

<p>Prom�nn� gBuffer slou�� pro na�ten� nepot�ebn�ch dat, jako jsou nezn�m� bloky nebo bloky, kter� z�m�rn� p�esko��me, proto�e je nebudeme pot�ebovat. D�le p�id�me dv� jednotky.</p>

<p class="src0">var</p>
<p class="src1">gBuffer: array [0..50000] of integer;<span class="kom">// Buffer pro na�ten� nepot�ebn�ch dat</span></p>
<p class="src"></p>
<p class="src0">implementation</p>
<p class="src0">uses SysUtils, Windows;</p>

<p>Funkce Vector spo��t� vektor mezi dv�ma body. Nic slo�it�ho. Jak jist� v�ichni zn�te z analytick� geometrie, od koncov�ho bodu se ode�te po��tek.</p>

<p class="src0">function Vector(vPoint1, vPoint2: CVector3): CVector3;<span class="kom">// V�po�et vektoru mezi dv�ma body</span></p>
<p class="src0">var</p>
<p class="src1">vVector: CVector3;</p>
<p class="src0">begin</p>
<p class="src1">vVector.x := vPoint1.x - vPoint2.x;<span class="kom">// Bod 1 - Bod 2</span></p>
<p class="src1">vVector.y := vPoint1.y - vPoint2.y;</p>
<p class="src1">vVector.z := vPoint1.z - vPoint2.z;</p>
<p class="src1">Result := vVector;</p>
<p class="src0">end;</p>

<p>Funce Cross vrac� vektorov� sou�in dvou vektor�, tedy vektor kolm� na rovinu, kterou vytv��ej� dva p�vodn� vektory. S touto funkc� budeme po��tat norm�lov� vektory pro sv�tlo.</p>

<p class="src0">function Cross(vVector1, vVector2: CVector3): CVector3;<span class="kom">// Vektorov� sou�in</span></p>
<p class="src0">var</p>
<p class="src1">vCross: CVector3;</p>
<p class="src0">begin</p>
<p class="src1">vCross.x := ((vVector1.y * vVector2.z) - (vVector1.z * vVector2.y));</p>
<p class="src1">vCross.y := ((vVector1.z * vVector2.x) - (vVector1.x * vVector2.z));</p>
<p class="src1">vCross.z := ((vVector1.x * vVector2.y) - (vVector1.y * vVector2.x));</p>
<p class="src1">Result := vCross;</p>
<p class="src0">end;</p>

<p>Funkce Normalize vrac� jednotkov� vektor.</p>

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

<p>Funkce AddVector vrac� sou�et dvou vektor�.</p>

<p class="src0">function AddVector(vVector1, vVector2: CVector3): CVector3;<span class="kom">// Sou�et vektor�</span></p>
<p class="src0">var</p>
<p class="src1">vResult: CVector3;</p>
<p class="src0">begin</p>
<p class="src1">vResult.x := vVector2.x + vVector1.x;<span class="kom">// Vektor 1 + Vektor 2</span></p>
<p class="src1">vResult.y := vVector2.y + vVector1.y;</p>
<p class="src1">vResult.z := vVector2.z + vVector1.z;</p>
<p class="src1">Result := vResult;</p>
<p class="src0">end;</p>

<p>Funkce DivideVectorByScaler vyd�l� vektor ��slem a t�m ho zkr�t� pop�. prodlou��.</p>

<p class="src0">function DivideVectorByScaler(vVector1: CVector3; Scaler: Double): CVector3;<span class="kom">// D�len� vektoru ��slem</span></p>
<p class="src0">var</p>
<p class="src1">vResult: CVector3;</p>
<p class="src0">begin</p>
<p class="src1">vResult.x := vVector1.x / Scaler;</p>
<p class="src1">vResult.y := vVector1.y / Scaler;</p>
<p class="src1">vResult.z := vVector1.z / Scaler;</p>
<p class="src1">Result := vResult;</p>
<p class="src0">end;</p>

<p>D�le n�sleduj� vlastn� metody t��dy CLoad3DS. V procedu�e CleanUp se prov�d� ve�ker� pot�ebn� �klid - uvoln�n� pam�ti, uzav�en� soubor�...</p>

<p class="src0"><span class="kom">{ CLoad3DS }</span></p>
<p class="src"></p>
<p class="src0">procedure CLoad3DS.CleanUp;<span class="kom">// �klid</span></p>
<p class="src0">begin</p>
<p class="src1">if m_FilePointer &lt;&gt; -1 then<span class="kom">// M�me otev�en soubor?</span></p>
<p class="src1">begin</p>
<p class="src2">FileClose(m_FilePointer);<span class="kom">// Zav�eme ho</span></p>
<p class="src2">m_FilePointer := -1;</p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Procedura ComputeNormals slou�� pro v�po�et norm�l vertex�. Na za��tku zkontrolujeme, zda m�me v modelu alespo� jeden objekt. Pokud ne, nem� smysl pokra�ovat a proceduru ukon��me. V cyklu projdeme v�echny objekty modelu.</p>

<p class="src0">procedure CLoad3DS.ComputeNormals(pModel: t3DModel);<span class="kom">// V�po�et norm�l</span></p>
<p class="src0">var</p>
<p class="src1">vVector1, vVector2, vNormal: CVector3;</p>
<p class="src1">vPoly: array [0..2] of CVector3;</p>
<p class="src1">index, i, j: integer;</p>
<p class="src1">pObject: Pt3DObject;</p>
<p class="src1">pNormals, pTempNormals: array of CVector3;</p>
<p class="src1">vSum, vZero: CVector3;</p>
<p class="src1">shared: integer;</p>
<p class="src0">begin</p>
<p class="src1">if pModel.numOfObjects &lt;= 0 then exit;<span class="kom">// Pokud nem�me objekt tak kon��me</span></p>
<p class="src"></p>
<p class="src1">for index := 0 to pModel.numOfObjects - 1 do<span class="kom">// Cyklus p�es v�echny objekty modelu</span></p>
<p class="src1">begin</p>

<p>Ulo��me si objekt do pomocn� prom�nn� a nastav�me velikost pol� pro v�po�et norm�l.</p>

<p class="src2">pObject := @pModel.pObject[index];<span class="kom">// Z�sk� aktu�ln� objekt</span></p>
<p class="src"></p>
<p class="src2">SetLength(pNormals, pObject.numOfFaces);<span class="kom">// Alokace pot�ebn� pam�ti</span></p>
<p class="src2">SetLength(pTempNormals, pObject.numOfFaces);</p>
<p class="src2">SetLength(pObject.pNormals, pObject.numOfVerts);</p>

<p>Projdeme v�echny plo�ky objektu. Pro p�ehlednost si ulo��me ka�d� vertex troj�heln�ku do samostatn� prom�nn� a vypo��t�me norm�lu plo�ky (z�sk�me dva vektory a z nich spo��t�me norm�lu). Je�t� ne� z norm�ly ud�l�me jednotkov� vektor, ulo��me ji do pomocn�ho pole.</p>

<p class="src2">for i := 0 to pObject.numOfFaces - 1 do<span class="kom">// Cyklus p�es v�echny plo�ky objektu</span></p>
<p class="src2">begin</p>
<p class="src3">vPoly[0] := pObject.pVerts[pObject.pFaces[i].vertIndex[0]];<span class="kom">// Pro p�ehlednost ulo�� 3 vertexy do pomocn� prom�nn�</span></p>
<p class="src3">vPoly[1] := pObject.pVerts[pObject.pFaces[i].vertIndex[1]];</p>
<p class="src3">vPoly[2] := pObject.pVerts[pObject.pFaces[i].vertIndex[2]];</p>
<p class="src"></p>
<p class="src3"><span class="kom">// Vlastn� v�po�et norm�l</span></p>
<p class="src3">vVector1 := Vector(vPoly[0], vPoly[2]);<span class="kom">// V�po�et 1. vektoru</span></p>
<p class="src3">vVector2 := Vector(vPoly[2], vPoly[1]);<span class="kom">// V�po�et 2. vektoru</span></p>
<p class="src3">vNormal := Cross(vVector1, vVector2);<span class="kom">// V�po�et norm�ly</span></p>
<p class="src"></p>
<p class="src3">pTempNormals[i] := vNormal;<span class="kom">// Ulo�� nenormalizovanou norm�lu pro pozd�j�� v�po�ty norm�l vertex�</span></p>
<p class="src3">vNormal := Normalize(vNormal);<span class="kom">// Normalizuje norm�lu</span></p>
<p class="src3">pNormals[i] := vNormal;<span class="kom">// Ulo�� norm�lu do pole</span></p>
<p class="src2">end;</p>

<p>Te� zb�v� jen vypo��tat norm�ly jednotliv�ch vertex�. V prvn�m cyklu projdeme v�echny vertexy a v dal��m zjist�me, ke kolika plo�k�m dan� vertex n�le��. Pro ka�d� vertex projdeme v�echny plo�ky a pokud najdeme n�jakou plo�ku, ke kter� pat��, zv���me &quot;v�hu&quot; p��slu�n�ho vertexu o norm�lu plo�ky (pou�ijeme d��ve ulo�en� norm�lov� vektor, p�ed t�m, ne� jsme z n�j ud�lali jednotkov�). Nakonec z norm�lov�ho vektoru vertexu ud�l�me jednotkov�. Nen� to tak hrozn�, jak to z p�edchoz�ho textu vypad�. V�e je vid�t na obr�zku:</p>

<div class="okolo_img">
<img src="images/clanky/cl_gl_3ds/1.jpg" width="380" height="275" alt="V�po�et norm�l vertex�" />
<div>Bod 1 n�le�� jen jedn� plo�ce - norm�lov� vektor vertexu bude shodn� s vektorem plo�ky.</div>
<div>Bod 2 n�le�� ke dv�ma plo�k�m - jeho norm�lov� vektor bude slo�en z vektor� obou plo�ek.</div>
<div>Bod 3 n�le�� ke t�em plo�k�m - jeho norm�lov� vektor bude slo�en z vektor� v�ech t�� plo�ek.</div>
</div>

<p class="src"></p>
<p class="src2"><span class="kom">// V�po�et norm�l vertex�</span></p>
<p class="src2">ZeroMemory(@vSum, sizeof(vSum));</p>
<p class="src2">vZero := vSum;</p>
<p class="src2">shared := 0;</p>
<p class="src"></p>
<p class="src2">for i := 0 to pObject.numOfVerts - 1 do<span class="kom">// Cyklus p�es v�echny vertexy</span></p>
<p class="src2">begin</p>
<p class="src3">for j := 0 to pObject.numOfFaces - 1 do<span class="kom">// Cyklus p�es v�echny plo�ky (troj�heln�ky)</span></p>
<p class="src4">if (pObject.pFaces[j].vertIndex[0] = i) or<span class="kom">// Je vertex sd�len s jinou plo�kou?</span></p>
<p class="src5">(pObject.pFaces[j].vertIndex[1] = i) or</p>
<p class="src5">(pObject.pFaces[j].vertIndex[2] = i) then</p>
<p class="src4">begin</p>
<p class="src5">vSum := AddVector(vSum, pTempNormals[j]);<span class="kom">// P�i�te nenormalizovanou norm�lu</span></p>
<p class="src5">Inc(shared);<span class="kom">// Zv��� po�et sd�len�ch troj�heln�k�</span></p>
<p class="src4">end;</p>
<p class="src"></p>
<p class="src3">pObject.pNormals[i] := DivideVectorByScaler(vSum, -shared);<span class="kom">// Z�sk� norm�lu</span></p>
<p class="src3">pObject.pNormals[i] := Normalize(pObject.pNormals[i]);<span class="kom">// Normalizuje norm�lu</span></p>
<p class="src3">vSum := vZero;</p>
<p class="src3">shared := 0;</p>
<p class="src2">end;</p>
<p class="src"></p>
<p class="src2">SetLength(pTempNormals, 0);<span class="kom">// Uvoln� pam�</span></p>
<p class="src2">SetLength(pNormals, 0);</p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Konstruktor - zde se inicializuj� prom�nn�.</p>

<p class="src0">constructor CLoad3DS.Create;<span class="kom">// Konstruktor</span></p>
<p class="src0">begin</p>
<p class="src1">m_FilePointer := -1;</p>
<p class="src0">end;</p>

<p>Destruktor - zde se uvol�uj� prost�edky pou��van� prom�nn�mi.</p>

<p class="src0">destructor CLoad3DS.Destroy;<span class="kom">// Destruktor</span></p>
<p class="src0">begin
<p class="src1">inherited;
<p class="src0">end;

<p>Funkce GetString na�te �et�zec ze souboru do prom�nn� pBuffer a vrac� jeho d�lku. Princip je jednoduch�, �teme znak po znaku, dokud nenaraz�me na konec �et�zce (#0).</p>

<p class="src0">function CLoad3DS.GetString(var pBuffer: string): integer;<span class="kom">// Na�te �et�zec</span></p>
<p class="src0">var</p>
<p class="src1">index: integer;</p>
<p class="src1">tmpChar: Char;</p>
<p class="src0">begin</p>
<p class="src1">index := 1;</p>
<p class="src1">FileRead(m_FilePointer, tmpChar, 1);<span class="kom">// Na�te prvn� znak</span></p>
<p class="src1">pBuffer := pBuffer + tmpChar;</p>
<p class="src"></p>
<p class="src1">while pBuffer[index] &lt;&gt; #0 do<span class="kom">// Kontroluje, zda jsme na konci �et�zce (#0)</span></p>
<p class="src1">begin</p>
<p class="src2">FileRead(m_FilePointer, tmpChar, 1);<span class="kom">// Na�te dal�� znak</span></p>
<p class="src2">pBuffer := pBuffer + tmpChar;</p>
<p class="src2">Inc(index);</p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">Result := Length(pBuffer);<span class="kom">// Vrac� d�lku �et�zce</span></p>
<p class="src0">end;</p>

<p>Funkce Import3DS nahraje model ze souboru. Na za��tku se pokus�me otev��t soubor. Pokud nastala chyba, zobraz�me chybovou zpr�vu a ukon��me funkci.</p>

<p class="src0">function CLoad3DS.Import3DS(var pModel: t3DModel; strFileName: string): boolean;<span class="kom">// Nahraje model</span></p>
<p class="src0">var</p>
<p class="src1">strMessage: PAnsiChar;</p>
<p class="src1">currentChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">ZeroMemory(@currentChunk, sizeof(currentChunk));</p>
<p class="src1">m_FilePointer := FileOpen(strFileName, fmOpenRead);<span class="kom">// Otev�e 3DS soubor</span></p>
<p class="src"></p>
<p class="src1">if m_FilePointer = -1 then<span class="kom">// Pokud nastala chyba, zobraz�me zpr�vu</span></p>
<p class="src1">begin</p>
<p class="src2">strMessage := PAnsiChar(Format('Unable to find the file: %s!', [strFileName]));</p>
<p class="src2">MessageBox(0, strMessage, 'Error', MB_OK);</p>
<p class="src2">Result := false;</p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pokud se n�m poda�ilo soubor otev��t, na�teme hlavi�ku prvn�ho bloku. Podle identifik�toru bloku zjist�me, zda se jedn� o prim�rn� blok. Pokud ne, pak se nejedn� o 3ds soubor, a proto zobraz�me chybovou hl�ku, uklid�me po sob� a ukon��me funkci.</p>

<p class="src1">ReadChunk(currentChunk);<span class="kom">// Na�te prvn� blok</span></p>
<p class="src"></p>
<p class="src1">if currentChunk.ID &lt;&gt; PRIMARY then<span class="kom">// Pokud m�me jin� ne� prim�rn� blok, nejedn� se o 3DS soubor</span></p>
<p class="src1">begin</p>
<p class="src2">strMessage := PAnsiChar(Format('Unable to load PRIMARY chunk from file: %s!', [strFileName]));</p>
<p class="src2">MessageBox(0, strMessage, 'Error', MB_OK);</p>
<p class="src2">CleanUp;</p>
<p class="src2">Result := false;</p>
<p class="src2">exit;</p>
<p class="src1">end;</p>

<p>Pomoc� procedury ProcessNextChunk, kter� je vol�na rekurzivn� (vol� sama sebe), projdeme v�echny bloky v souboru. Potom spo��t�me norm�ly a uklid�me.</p>

<p class="src1">ProcessNextChunk(pModel, currentChunk);<span class="kom">// Na�te objekty, procedura je vol�na rekurzivn�</span></p>
<p class="src1">ComputeNormals(pModel);<span class="kom">// V�po�et norm�l vrchol�</span></p>
<p class="src1">CleanUp;<span class="kom">// �klid</span></p>
<p class="src1">Result := true;</p>
<p class="src0">end;</p>

<p>Procedura ProcessNextChunk na��t� jednotliv� bloky souboru.</p>

<p class="src0">procedure CLoad3DS.ProcessNextChunk(var pModel: t3DModel; var pPreviousChunk: tChunk);<span class="kom">// Na�te dal�� bloky</span></p>
<p class="src0">var</p>
<p class="src1">currentChunk, tempChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">ZeroMemory(@currentChunk, sizeof(currentChunk));</p>
<p class="src1">ZeroMemory(@tempChunk, sizeof(tempChunk));</p>

<p>Nejd��ve otestujeme, jestli u� nejsme na konci bloku. Pokud ne, na�teme hlavi�ku podbloku.</p>

<p class="src1">while pPreviousChunk.bytesRead &lt; pPreviousChunk.length do<span class="kom">// Cyklus dokud nena�teme cel� blok</span></p>
<p class="src1">begin</p>
<p class="src2">ReadChunk(currentChunk);<span class="kom">// Na�te dal�� podblok</span></p>

<p>Rozv�tv�me k�d podle jednotliv�ch identifik�tor� blok�.</p>

<p class="src2">case currentChunk.ID of<span class="kom">// V�tven� podle hlavi�ky bloku</span></p>

<p>Blok s informac� o verzi souboru. Verzi na�teme do bufferu a zkontrolujeme, jestli nen� vy��� ne� 3. Pokud ano, zobraz�me varovnou hl�ku o mo�n� nekompatibilit� form�tu.</p>

<p class="src3">VERSION:<span class="kom">// Informace o verzi souboru</span></p>
<p class="src3">begin</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src"></p>
<p class="src4">if (currentChunk.length - currentChunk.bytesRead = 4) and (gBuffer[0] &gt; $03) then<span class="kom">// Pokud je verze vy��� ne� 3, zobraz�me varov�n�</span></p>
<p class="src5">MessageBox(0, 'This 3DS file is over version 3 so it may load incorrectly', 'Warning', MB_OK);</p>
<p class="src3">end;</p>

<p>Blok OBJECTINFO je pouze kontejner, kter� obsahuje dal�� bloky s informacemi o objektu. Hned proto na�teme hlavi�ku podbloku a rekurzivn� zavol�me proceduru ProcessNextChunk.</p>

<p class="src3">OBJECTINFO:<span class="kom">// Hlavi�ka objektu</span></p>
<p class="src3">begin</p>
<p class="src4">ReadChunk(tempChunk);<span class="kom">// Na�te dal�� podblok</span></p>
<p class="src4">tempChunk.bytesRead := tempChunk.bytesRead + FileRead(m_FilePointer, gBuffer, tempChunk.length - tempChunk.bytesRead);</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + tempChunk.bytesRead;</p>
<p class="src4">ProcessNextChunk(pModel, currentChunk);<span class="kom">// Na�te dal�� informace o objektu</span></p>
<p class="src3">end;</p>

<p>Blok MATERIAL je tak� pouze kontejner, uvozuje ka�d� materi�l pou�it� na objektu. Zv���me tedy po�et materi�l� a na�teme dal�� informace o materi�lu.</p>

<p class="src3">MATERIAL:<span class="kom">// Informace o materi�lu</span></p>
<p class="src3">begin</p>
<p class="src4">Inc(pModel.numOfMaterials);<span class="kom">// Zv��� po�et materi�l�</span></p>
<p class="src4">SetLength(pModel.pMaterials, Length(pModel.pMaterials) + 1);</p>
<p class="src4">ProcessNextMaterialChunk(pModel, currentChunk);<span class="kom">// Na�te dal�� informace o materi�lu</span></p>
<p class="src3">end;</p>

<p>Blok OBJEKT je op�t jenom kontejner, uvozuje ka�d� objekt pou�it� v modelu. Proto zv���me po�et objekt�, z�sk�me jm�no objektu a na�teme zb�vaj�c� informace o objektu.</p>

<p class="src3">OBJEKT:<span class="kom">// Informace o objektu</span></p>
<p class="src3">begin</p>
<p class="src4">Inc(pModel.numOfObjects);<span class="kom">// Zv��� po�et objekt�</span></p>
<p class="src4">SetLength(pModel.pObject, Length(pModel.pObject) + 1);</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + GetString(pModel.pObject[pModel.numOfObjects - 1].strName);<span class="kom">// Na�te jm�no objektu</span></p>
<p class="src4">ProcessNextObjectChunk(pModel, pModel.pObject[pModel.numOfObjects - 1], currentChunk);<span class="kom">// Na�te zb�vaj�c� informace o objektu</span></p>
<p class="src3">end;</p>

<p>Blok EDITKEYFRAME obsahuje informace o kl��ov�ch sn�mc�ch animace objektu. My ho nepou��v�me, proto zahod�me zbytek bloku do bufferu.</p>

<p class="src3">EDITKEYFRAME:<span class="kom">// Kl��ov� sn�mek - nepou�ito - p�esko��me</span></p>
<p class="src3">begin</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src3">end;</p>

<p>Pokud na�teme nezn�m� blok, zahod�me jeho obsah do bufferu.</p>

<p class="src3">else<span class="kom">// Pokud na�teme nezn�m� blok, ignorujeme ho</span></p>
<p class="src3">begin</p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src3">end;</p>
<p class="src2">end;</p>

<p>Nakonec zv�t��me po�et p�e�ten�ch byt�.</p>

<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + currentChunk.bytesRead;<span class="kom">// Zv�t��me po�et p�e�ten�ch byt�</span></p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Procedura ProcessNextMaterialChunk na��t� informace o materi�lu. Na za��tku zkontrolujeme, jestli nejsme na konci bloku. Pokud ne, na�teme hlavi�ku podbloku a rozv�tv�me k�d podle identifik�toru hlavi�ky.</p>

<p class="src0">procedure CLoad3DS.ProcessNextMaterialChunk(var pModel: t3DModel; var pPreviousChunk: tChunk);<span class="kom">// Na�te informace o materi�lu</span></p>
<p class="src0">var</p>
<p class="src1">currentChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">ZeroMemory(@currentChunk, sizeof(currentChunk));</p>
<p class="src"></p>
<p class="src1">while pPreviousChunk.bytesRead &lt; pPreviousChunk.length do<span class="kom">// �te dokud nejsme na konci podbloku</span></p>
<p class="src1">begin</p>
<p class="src2">ReadChunk(currentChunk);<span class="kom">// Na�te blok</span></p>
<p class="src2">case currentChunk.ID of<span class="kom">// V�tven� podle hlavi�ky bloku</span></p>

<p>Blok MATNAME obsahuje jm�no materi�lu. Zavol�me funkci GetString, kter� ho na�te.</p>

<p class="src3">MATNAME:<span class="kom">// Na�te jm�no materi�lu</span></p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + GetString(pModel.pMaterials[pModel.numOfMaterials - 1].strName;</p>

<p>Blok MATDIFFUSE obsahuje informace o barv� objektu. Na�teme je samostatnou funkc�.</p>

<p class="src3">MATDIFFUSE:<span class="kom">// Na�te barvu objektu</span></p>
<p class="src4">ReadColorChunk(pModel.pMaterials[pModel.numOfMaterials - 1], currentChunk);</p>

<p>Blok MATMAP obsahuje informace o materi�lu. Vol�me rekurzivn�.</p>

<p class="src3">MATMAP:<span class="kom">// Na�te informace o materi�lu</span></p>
<p class="src4">ProcessNextMaterialChunk(pModel, currentChunk);</p>

<p>Blok MATMAPFILE obsahuje jm�no souboru obsahuj�c� texturu, kter� je pou�ita v materi�lu. Jm�no souboru z�sk�me funkc� GetString.</p>

<p class="src3">MATMAPFILE:<span class="kom">// Na�te jm�no souboru s texturou</span></p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + GetString(pModel.pMaterials[pModel.numOfMaterials - 1].strFile);</p>

<p>Pokud na�teme nezn�m� blok, zahod�me jeho obsah do bufferu.</p>

<p class="src3">else<span class="kom">// Ignorujeme nezn�m� bloky</span></p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src2">end;</p>

<p>Nakonec zv�t��me po�et p�e�ten�ch byt�.</p>

<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + currentChunk.bytesRead;<span class="kom">// Zv�t��me po�et p�e�ten�ch byt�</span></p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Procedura ProcessNextObjectChunk na��t� informace o objektu. Jako obvykle zkontrolujeme, jestli u� nejsme na konci bloku. Pokud ne, na�teme hlavi�ku podbloku a rozv�tv�me program podle identifik�toru hlavi�ky.</p>

<p class="src0">procedure CLoad3DS.ProcessNextObjectChunk(var pModel: t3DModel; var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te informace o objektu</span></p>
<p class="src0">var</p>
<p class="src1">currentChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">while pPreviousChunk.bytesRead &lt; pPreviousChunk.length do<span class="kom">// �te do konce podbloku</span></p>
<p class="src1">begin</p>
<p class="src2">ZeroMemory(@currentChunk, sizeof(currentChunk));</p>
<p class="src2">ReadChunk(currentChunk);<span class="kom">// Na�te dal�� blok</span></p>
<p class="src"></p>
<p class="src2">case currentChunk.ID of<span class="kom">// V�tven� podle hlavi�ky</span></p>

<p>Blok OBJECT_MESH obsahuje informace o objektu. Vol�na rekurzivn�.</p>

<p class="src3">OBJECT_MESH:<span class="kom">// Nov� objekt</span></p>
<p class="src4">ProcessNextObjectChunk(pModel, pObject, currentChunk);<span class="kom">// Na�te jeho informace</span></p>

<p>Blok OBJECT_VERTICES obsahuje vertexy objektu. Na�teme je funkc� ReadVertices.</p>

<p class="src3">OBJECT_VERTICES:<span class="kom">// Na�te vertexy objektu</span></p>
<p class="src4">ReadVertices(pObject, currentChunk);</p>

<p>Blok OBJECT_FACES obsahuje plo�ky objektu. Na�teme je funkc� ReadVertexIndices.</p>

<p class="src3">OBJECT_FACES:<span class="kom">// Na�te plo�ky objektu</span></p>
<p class="src4">ReadVertexIndices(pObject, currentChunk);</p>

<p>Blok OBJECT_MATERIAL obsahuje jm�no pou�it�ho materi�lu. Na�teme ho funkc� ReadObjectMaterial.</p>

<p class="src3">OBJECT_MATERIAL:<span class="kom">// Na�te jm�no pou�it�ho materi�lu</span></p>
<p class="src4">ReadObjectMaterial(pModel, pObject, currentChunk);</p>

<p>Blok OBJECT_UV obsahuje texturov� koordin�ty. Na�teme je funkc� ReadUVCoordinates.</p>

<p class="src3">OBJECT_UV:<span class="kom">// Na�te texturov� koordin�ty objektu</span></p>
<p class="src4">ReadUVCoordinates(pObject,currentChunk);</p>

<p>Ignorujeme nezn�m� bloky a inkrementujeme ��ta� p�e�ten�ch byt�.</p>

<p class="src3">else<span class="kom">// Ignorujeme nezn�m� bloky</span></p>
<p class="src4">currentChunk.bytesRead := currentChunk.bytesRead + FileRead(m_FilePointer, gBuffer, currentChunk.length - currentChunk.bytesRead);</p>
<p class="src2">end;</p>
<p class="src"></p>
<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + currentChunk.bytesRead;<span class="kom">// Zv�t��me po�et p�e�ten�ch byt�</span></p>
<p class="src1">end;</p>
<p class="src0">end;</p>

<p>Procedura ReadColorChunk na��t� barvu objektu. Zjist�me informace o bloku a na z�klad� d�lky bloku pou�ijeme vhodnou prom�nnou pro ulo�en� hodnot RGB. Origin�ln� k�d po��tal jen s variantou, �e barva je ulo�ena jako 3x byte (3x 1 byte). Ale j�, kdy� jsem exportoval sv�j model z programu Cinema 4D, jsem zjistil, �e je barva ukl�dan� jako 3x single (3x 4 byty). Proto jsem implementoval rozv�tven�.</p>

<p class="src0">procedure CLoad3DS.ReadColorChunk(var pMaterial: tMaterialInfo; var pChunk: tChunk);<span class="kom">// Na�te barvu objektu</span></p>
<p class="src0">var</p>
<p class="src1">tempChunk: tChunk;</p>
<p class="src0">begin</p>
<p class="src1">ZeroMemory(@tempChunk, sizeof(tempChunk));</p>
<p class="src1">ReadChunk(tempChunk);<span class="kom">// Informace o bloku</span></p>
<p class="src1">pMaterial.bpc := tempChunk.length - tempChunk.bytesRead;</p>
<p class="src"></p>
<p class="src1">if pMaterial.bpc = 3 then<span class="kom">// Podle d�lky bloku na�teme barvu do p��slu�n�ho pole (3-byte, 12-single)</span></p>
<p class="src2">tempChunk.bytesRead := tempChunk.bytesRead + FileRead(m_FilePointer, pMaterial.colorub, pMaterial.bpc)</p>
<p class="src1">else</p>
<p class="src2">tempChunk.bytesRead := tempChunk.bytesRead + FileRead(m_FilePointer, pMaterial.colorf, pMaterial.bpc);</p>
<p class="src"></p>
<p class="src1">pChunk.bytesRead := pChunk.bytesRead + tempChunk.bytesRead;<span class="kom">// Zv�t��me po�et p�e�ten�ch byt�</span></p>
<p class="src0">end;</p>

<p>Procedura ReadChunk na�te hlavi�ku dal��ho bloku.</p>

<p class="src0">procedure CLoad3DS.ReadChunk(var pChunk: tChunk);<span class="kom">// Na�te hlavi�ku bloku</span></p>
<p class="src0">begin</p>
<p class="src1">pChunk.bytesRead := FileRead(m_FilePointer, pChunk.ID, 2);<span class="kom">// ID bloku</span></p>
<p class="src1">pChunk.bytesRead := pChunk.bytesRead + FileRead(m_FilePointer, pChunk.length, 4);<span class="kom">// D�lka bloku</span></p>
<p class="src0">end;</p>

<p>Procedura ReadObjectMaterial na�te jm�no pou�it�ho materi�lu. Potom v cyklu nastav�me u objektu identifik�tor pou�it�ho materi�lu a pokud materi�l obsahuje texturu, nastav�me p��znak textury na TRUE.</p>

<p class="src0">procedure CLoad3DS.ReadObjectMaterial(pModel: t3DModel; var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te materi�ly</span></p>
<p class="src0">var</p>
<p class="src1">strMaterial: string;</p>
<p class="src1">i: integer;</p>
<p class="src0">begin</p>
<p class="src1">strMaterial := '';</p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + GetString(strMaterial);<span class="kom">// Jm�no materi�lu</span></p>
<p class="src"></p>
<p class="src1">for i := 0 to pModel.numOfMaterials - 1 do<span class="kom">// Projde v�echny materi�ly</span></p>
<p class="src2">if strMaterial = pModel.pMaterials[i].strName then<span class="kom">// Na�li jsme n� materi�l?</span></p>
<p class="src2">begin</p>
<p class="src3">pObject.materialID := i;<span class="kom">// Nastav�me jeho index</span></p>
<p class="src"></p>
<p class="src3">if pModel.pMaterials[i].strFile &lt;&gt; '' then<span class="kom">// Pokud existuje soubor s texturou</span></p>
<p class="src4">pObject.bHasTexture := true;<span class="kom">// Nastav�me p��znak</span></p>
<p class="src"></p>
<p class="src3">break;</p>
<p class="src2">end</p>
<p class="src2">else</p>
<p class="src3">pObject.materialID := -1;<span class="kom">// Objekt nem� materi�l</span></p>
<p class="src"></p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, Buffer, pPreviousChunk.length - pPreviousChunk.bytesRead);<span class="kom">// Zv�t��me po�et p�e�ten�ch byt�</span></p>
<p class="src0">end;</p>

<p>Procedura ReadUVCoordinates na��t� texturov� koordin�ty. Nejd��ve zjist�me po�et koordin�t� a nastav�me d�lku pole pro koordin�ty. V cyklu potom na�teme v�echny koordin�ty.</p>

<p class="src0">procedure CLoad3DS.ReadUVCoordinates(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te texturov� koordin�ty</span></p>
<p class="src0">var</p>
<p class="src1">i: integer;</p>
<p class="src0">begin</p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.numTexVertex, 2);<span class="kom">// Na�te po�et koordin�t�</span></p>
<p class="src1">SetLength(pObject.pTexVerts,pObject.numTexVertex);<span class="kom">// Nastav� d�lku pole</span></p>
<p class="src"></p>
<p class="src1">for i := 0 to pObject.numTexVertex - 1 do<span class="kom">// Na�te v�echny koordin�ty</span></p>
<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.pTexVerts[i], 8);</p>
<p class="src0">end;</p>

<p>Procedura ReadVertexIndices na��t� indexy do pole vertex�. Na za��tku zjist�me po�et plo�ek a nastav�me pro n� velikost pole. V cyklech projdeme v�echny plo�ky a jejich vrcholy a na�teme indexy p��slu�n�ch vertex�. U plo�ek n�s zaj�maj� jen prvn� t�i hodnoty p�edstavuj�c� indexy vrchol�, �tvrt� hodnota - viditelnost - je pro n�s nezaj�mav� a tak ji p�esko��me.</p>

<p class="src0">procedure CLoad3DS.ReadVertexIndices(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te indexy do pole vertex�</span></p>
<p class="src0">var</p>
<p class="src1">index: word;</p>
<p class="src1">i, j: integer;</p>
<p class="src0">begin</p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.numOfFaces, 2);<span class="kom">// Na�te po�et plo�ek</span></p>
<p class="src1">SetLength(pObject.pFaces, pObject.numOfFaces);<span class="kom">// Nastav� velikost pole</span></p>
<p class="src1">ZeroMemory(pObject.pFaces, pObject.numOfFaces);</p>
<p class="src"></p>
<p class="src1">for i := 0 to pObject.numOfFaces - 1 do<span class="kom">// Cyklus p�es v�echny plo�ky</span></p>
<p class="src2">for j := 0 to 3 do<span class="kom">// Cyklus p�es vrcholy plo�ek</span></p>
<p class="src2">begin</p>
<p class="src3">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, index, sizeof(index));<span class="kom">// Na�te index vrcholu</span></p>
<p class="src3">if j &lt; 3 then</p>
<p class="src4">pObject.pFaces[i].vertIndex[j] := index;<span class="kom">// Ulo�� index do pole</span></p>
<p class="src2">end;</p>
<p class="src0">end;</p>

<p>Procedura ReadVertices �te jednotliv� vertexy. Zjist�me po�et vertex� a nastav�me velikost pole. V cyklu na�teme v�echny vertexy a prohod�me jejich osy Y a Z, proto�e 3D Studio Max pou��v� jin� syst�m os ne� OpenGL. Nav�c ze stejn�ho d�vodu zm�n�me orientaci u osy Z.</p>

<p class="src0">procedure CLoad3DS.ReadVertices(var pObject: t3DObject; var pPreviousChunk: tChunk);<span class="kom">// Na�te vertexy</span></p>
<p class="src0">var</p>
<p class="src1">i: integer;</p>
<p class="src1">fTempY: Double;</p>
<p class="src0">begin</p>
<p class="src1">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.numOfVerts, 2);<span class="kom">// Na�te po�et vertex�</span></p>
<p class="src1">SetLength(pObject.pVerts, pObject.numOfVerts);<span class="kom">// Nastav� velikost pole</span></p>
<p class="src1">ZeroMemory(pObject.pVerts, pObject.numOfVerts);</p>
<p class="src"></p>
<p class="src1">for i := 0 to pObject.numOfVerts - 1 do<span class="kom">// Cyklus p�es v�echny vertexy</span></p>
<p class="src1">begin</p>
<p class="src2">pPreviousChunk.bytesRead := pPreviousChunk.bytesRead + FileRead(m_FilePointer, pObject.pVerts[i], 12);</p>
<p class="src2">fTempY := pObject.pVerts[i].y;<span class="kom">// Prohod� Y a Z</span></p>
<p class="src2">pObject.pVerts[i].y := pObject.pVerts[i].z;</p>
<p class="src2">pObject.pVerts[i].z := -fTempY;<span class="kom">// Je�t� zm�na orientace osy</span></p>
<p class="src1">end;</p>
<p class="src0">end;</p>
<p class="src"></p>
<p class="src0">end.</p>

<p>Tak, to bylo srdce cel�ho na��t�n� 3ds modelu. Nyn� u� jen uprav�me NeHe k�d aplikace a m�me hotovo. Vysv�tl�m jen dopln�n� z�kladn�ho NeHe k�du, pro popis jeho kompletn� struktury odkazuji na NeHe tutori�ly. Na za��tku p�id�me jednotku glaux pro podporu nahr�v�n� bitmapov�ch obr�zk� a na�i jednotku f_3ds, kter� na�te 3ds model. D�le definujeme prom�nn�. g_Texture p�edstavuje pole pou�it�ch textur, g_Load3ds je na�e t��da 3ds a g_3DModel je prom�nn�, do kter� se na�te 3ds model. g_ViewMode obsahuje zp�sob vykreslov�n� vertex� (d�le bude implementov�no p�ep�n�n� mezi klasick�m a dr�t�n�m modelem) a n�sleduj� prom�nn� pro zapnut�/vypnut� osv�tlen� a pro rotaci objektu. Nakonec zvol�me jm�no nahr�van�ho souboru.</p>

<p class="src0">g_Texture: array of UINT;<span class="kom">// Pole textur</span></p>
<p class="src0">g_Load3ds: CLoad3DS;<span class="kom">// T��da 3DS</span></p>
<p class="src0">g_3DModel: t3DModel;<span class="kom">// 3DS model</span></p>
<p class="src0">g_ViewMode: integer = GL_TRIANGLES;<span class="kom">// Zp�sob vykreslov�n�</span></p>
<p class="src0">g_bLighting: boolean = true;<span class="kom">// Osv�tlen�</span></p>
<p class="src0">g_RotateX: GLfloat = 0.0;<span class="kom">// Rotace</span></p>
<p class="src0">g_RotationSpeed: GLfloat = 0.8;<span class="kom">// Rychlost rotace</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">// V�dy nechte odkomentovan� pouze jeden ��dek, ostatn� zakomentujte!!!</span></p>
<p class="src0">FILE_NAME: string = 'face.3ds';<span class="kom">// Soubor, kter� budeme nahr�vat - origin�l</span></p>
<p class="src0"><span class="kom">//FILE_NAME: string = 'muz.3ds';// P�edp�ipraven� objekt v Cinema 4D</span></p>
<p class="src0"><span class="kom">//FILE_NAME: string = 'snehulak.3ds';// M�j v�tvor</span></p>

<p>Vytvo��me proceduru CreateTexture, kter� se bude starat o nahr�v�n� textur. Jako parametry j� p�ed�me pole textur, jm�no souboru s obr�zkem a identifik�tor textury (index do pole textur). N�sleduje standardn� k�d na vytvo�en� mipmapovan� textury z bitmapov�ho obr�zku. Tedy nejd��ve na�teme obr�zek do pomocn� prom�nn�, zavol�me funkci glGenTextures a nech�me si vygenerovat jednu texturu. Nastav�me zp�sob interpretace na�ten�ch dat - funkce glPixelStorei, zvol�me vytvo�enou texturu - glBindTexture, p�evedeme obr�zek na mipmapovou texturu - gluBuild2DMipmaps a nakonec nastav�me jej� parametry - glTexParameteri.</p>

<p class="src0">procedure CreateTexture(var textureArray: array of UINT; strFileName: LPSTR; textureID: integer);<span class="kom">// Vytvo�en� textury</span></p>
<p class="src0">var</p>
<p class="src1">pBitmap: PTAUX_RGBImageRec;<span class="kom">// Pomocn� prom�nn� pro nahr�n� bitmapy</span></p>
<p class="src0">begin</p>
<p class="src1">if strFileName = '' then exit;<span class="kom">// Bylo p�ed�no jm�no soubor?</span></p>
<p class="src1">pBitmap := auxDIBImageLoadA(strFileName);<span class="kom">// Na�ten� bitmapy</span></p>
<p class="src1">if pBitmap = nil then exit;<span class="kom">// Poda�ilo se na��st bitmapu?</span></p>
<p class="src"></p>
<p class="src1">glGenTextures(1, textureArray[textureID]);<span class="kom">// Generujeme 1 texturu</span></p>
<p class="src1">glPixelStorei(GL_UNPACK_ALIGNMENT, 1);<span class="kom">// Zp�sob interpretace dat v pam�ti (1 - hodnoty typu byte)</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, textureArray[textureID]);<span class="kom">// Zvol� texturu</span></p>
<p class="src1">gluBuild2DMipmaps(GL_TEXTURE_2D, 3, pBitmap.sizeX, pBitmap.sizeY, GL_RGB, GL_UNSIGNED_BYTE, pBitmap.data);<span class="kom">// Vytvo�� mipmapovou texturu</span></p>
<p class="src"></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR_MIPMAP_NEAREST);<span class="kom">// Zp�sob filtrov�n�</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR_MIPMAP_LINEAR);</p>
<p class="src0">end;</p>

<p>Do funkce Initialize p�id�me n�sleduj�c� k�d. Nejprve otestujeme, zda p�i spu�t�n� programu nebyl n�hodou p�ed�n parametr (V C/C++ se jedn� o parametry argc, argv funkce main().). Jako jedin� mo�n� parametr je jm�no souboru s modelem. Pokud bylo p�ed�no, m� p�ednost p�ed jm�nem, kter� je nastaveno v programu &quot;natvrdo&quot;. Rozhodl jsem se to implementovat pro ty, kte�� pro otestov�n� programu necht�j� (nebo nem��ou - neprogram�to�i) spustit v�vojov� prost�ed� a cht�j� zkusit nahr�t jin� model. Pak sta�� napsat: main.exe muz.3ds a m�sto standardn�ho modelu face.3ds se nahraje model mu�e.</p>

<p>Jen bych cht�l dop�edu upozornit, �e m� modely byly velikostn� upraveny tak, aby se nemuselo h�bat s viewportem. Pokud se rozhodnete na��st sv�j vlastn� model, pak je velmi pravd�podobn�, �e budete muset zm�nit parametry funkce gluLookAt, kter� se nach�z� v procedu�e Draw a nastavuje pohled kamery!!! Pro &quot;jednoduchost&quot; tohoto uk�zkov�ho k�du jsem m�sto zm�ny pohledu kamery rad�ji zvolil zm�nu velikosti vytv��en�ho objektu.</p>

<p class="src0"><span class="kom">// Funkce Initialize</span></p>
<p class="src1">if ParamCount &lt;&gt; 0 then FILE_NAME := ParamStr(1);<span class="kom">// Pokud byl programu p�ed�n soubor jako parametr, pak jej na�teme m�sto souboru, kter� je definov�m p��mo v programu</span></p>

<p>D�le vytvo��me instanci t��dy a nahrajeme model ze souboru. Podle po�tu materi�l� nastav�me velikost pole pro jejich textury. V cyklu projdeme v�echny materi�ly a pokud obsahuj� textury, tak je vytvo��me. Nakonec zapneme sv�tlo nula, osv�tlen�, barvu materi�lu, mapov�n� textur a testov�n� hloubky.</p>

<p class="src1">ZeroMemory(@g_3DModel, sizeof(g_3DModel));<span class="kom">// Nulov�n� pam�ti</span></p>
<p class="src1">g_Load3ds := CLoad3DS.Create;<span class="kom">// Vytvo�� instanci t��dy 3DS</span></p>
<p class="src1">g_Load3ds.Import3DS(g_3DModel, FILE_NAME);<span class="kom">// Nahraje model</span></p>
<p class="src1">SetLength(g_Texture, g_3DModel.numOfMaterials);<span class="kom">// Nastav� velikost pole pro textury</span></p>
<p class="src"></p>
<p class="src1">for i := 0 to g_3DModel.numOfMaterials -1 do<span class="kom">// Cyklus p�es jednotliv� materi�ly</span></p>
<p class="src1">begin</p>
<p class="src2">if g_3DModel.pMaterials[i].strFile &lt;&gt; '' then<span class="kom">// Obsahuje materi�l texturu?</span></p>
<p class="src3">CreateTexture(g_Texture, PChar(g_3DModel.pMaterials[i].strFile), i);<span class="kom">// Pokud ano, pak ji vytvo��me</span></p>
<p class="src"></p>
<p class="src2">g_3DModel.pMaterials[i].texureId := i;<span class="kom">// Ulo�� ID textury</span></p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne sv�tlo</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne osv�tlen�</span></p>
<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Povol� barvu materi�lu</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Povol� mapov�n� textur</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>

<p>V procedu�e Deinitialize se postar�me o uvoln�n� alokovan�ch prost�edk�. V objektu nastav�me d�lku v�ech dynamick�ch pol� na 0 - t�m uvoln�me pam�, kterou zab�raly. Uvoln�me pam� pro textury a zru��me instanci t��dy 3ds.</p>

<p class="src0"><span class="kom">// Funkce Deinitialize</span></p>
<p class="src1">for i := 0 to g_3DModel.numOfObjects - 1 do<span class="kom">// Projde v�echny objekty v modelu</span></p>
<p class="src1">begin</p>
<p class="src2">SetLength(g_3DModel.pObject[i].pVerts, 0);<span class="kom">// Uvoln� pole vertex�</span></p>
<p class="src2">SetLength(g_3DModel.pObject[i].pNormals, 0);<span class="kom">// Uvoln� pole norm�l</span></p>
<p class="src2">SetLength(g_3DModel.pObject[i].pTexVerts, 0);<span class="kom">// Uvoln� pole texturovan�ch vertex�</span></p>
<p class="src2">SetLength(g_3DModel.pObject[i].pFaces, 0);<span class="kom">// Uvoln� pole fac�</span></p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">glDeleteTextures(g_3DModel.numOfMaterials, @g_Texture);<span class="kom">// Uvoln� pam� pro textury</span></p>
<p class="src1">SetLength(g_Texture, 0);<span class="kom">// Uvoln� pole textur</span></p>
<p class="src1">g_Load3ds.Free;<span class="kom">// Zru�� instanci t��dy 3DS</span></p>

<p>Do procedury Update dopln�me obsluhu stisku kl�ves. �ipkami budeme ovliv�ovat rotaci objektu.</p>

<p class="src0"><span class="kom">// Funkce Update</span></p>
<p class="src1">if g_keys.keyDown[VK_LEFT] then<span class="kom">// �ipka vlevo</span></p>
<p class="src2">g_RotationSpeed := g_RotationSpeed - 0.05;<span class="kom">// �prava rotace</span></p>
<p class="src"></p>
<p class="src1">if g_keys.keyDown[VK_RIGHT] then<span class="kom">// �ipka vpravo</span></p>
<p class="src2">g_RotationSpeed := g_RotationSpeed + 0.05;<span class="kom">// �prava rotace</span></p>

<p>Ve�ker� vykreslov�n� se prov�d� v procedu�e Draw. Ne jej�m za��tku sma�eme obrazovku a hloubkov� buffer a resetujeme matici.</p>

<p class="src0">procedure Draw;<span class="kom">// Vykreslen� sc�ny</span></p>
<p class="src0">var</p>
<p class="src1">i, j, whichVertex: integer;<span class="kom">// Cykly</span></p>
<p class="src1">pObject: t3DObject;<span class="kom">// Objekt</span></p>
<p class="src1">index: integer;<span class="kom">// Index do pole vertex�</span></p>
<p class="src0">begin</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT or GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity;<span class="kom">// Reset matice</span></p>

<p>D�le nastav�me pohled kamery, rotaci modelu a aktualizujeme �hel nato�en� modelu.</p>

<p class="src1">gluLookAt(0,1.5,8, 0,0.5,0, 0,1,0);<span class="kom">// Nastaven� pohledu kamery</span></p>
<p class="src1">glRotatef(g_RotateX, 0, 1.0, 0);<span class="kom">// Rotace modelu</span></p>
<p class="src1">g_RotateX := g_RotateX + g_RotationSpeed;<span class="kom">// Aktualizace �hlu nato�en�</span></p>

<p>Vykresl�me v�echny objekty modelu. Na za��tku cyklu testujeme, zda model obsahuje objekty a pokud ne, tak cyklus ukon��me. Objekt ulo��me do pomocn� prom�nn� a otestujeme, jestli m� texturu. Pokud ano, zapneme mapov�n� textur a p��slu�nou texturu zvol�me. Pokud ne, vypneme mapov�n� textur.</p>

<p class="src1">for i := 0 to g_3DModel.numOfObjects - 1 do<span class="kom">// Projde v�echny objekty v modelu</span></p>
<p class="src1">begin</p>
<p class="src2">if Length(g_3DModel.pObject) = 0 then break;<span class="kom">// Pokud model neobsahuje ��dn� objekt, ukon��me cyklus</span></p>
<p class="src3">pObject := g_3DModel.pObject[i];<span class="kom">// Ulo��me objekt do pomocn� prom�nn�</span></p>
<p class="src"></p>
<p class="src2">if pObject.bHasTexture then<span class="kom">// M� objekt texturu?</span></p>
<p class="src2">begin<span class="kom">// Pokud ano</span></p>
<p class="src3">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>
<p class="src3">glColor3ub(255, 255, 255);</p>
<p class="src3">glBindTexture(GL_TEXTURE_2D, g_Texture[pObject.materialID]);<span class="kom">// Zvol� p��slu�nou texturu</span></p>
<p class="src2">end</p>
<p class="src2">else<span class="kom">// Pokud ne</span></p>
<p class="src2">begin</p>
<p class="src3">glDisable(GL_TEXTURE_2D);<span class="kom">// Vypne mapov�n� textur</span></p>
<p class="src3">glColor3ub(255, 255, 255);</p>
<p class="src2">end;</p>

<p>Projdeme v�echny troj�heln�ky, nastav�me norm�ly a pokud m� objekt texturu nastav�me i texturov� koordin�ty. Pokud objekt texturu nem�, nastav�me jeho barvu. Nakonec vykresl�me vlastn� vertexy.</p>

<p class="src2">glBegin(g_ViewMode);<span class="kom">// Za��tek vykreslov�n� vertex�</span></p>
<p class="src"></p>
<p class="src2">for j := 0 to pObject.numOfFaces - 1 do<span class="kom">// Projde v�echny troj�heln�ky</span></p>
<p class="src3">for whichVertex := 0 to 2 do<span class="kom">// Projde v�echny vrcholy troj�heln�k�</span></p>
<p class="src3">begin</p>
<p class="src4">index := pObject.pFaces[j].vertIndex[whichVertex];<span class="kom">// Index do pole vertex�</span></p>
<p class="src4">glNormal3f(pObject.pNormals[index].x, pObject.pNormals[index].y, pObject.pNormals[index].z);<span class="kom">// Nastav� norm�lu</span></p>
<p class="src"></p>
<p class="src4">if pObject.bHasTexture then<span class="kom">// M� objekt texturu?</span></p>
<p class="src4">begin</p>
<p class="src5">if Assigned(pObject.pTexVerts) then<span class="kom">// M� objekt texturov� koordin�ty?</span></p>
<p class="src6">glTexCoord2f(pObject.pTexVerts[index].x, pObject.pTexVerts[index].y);<span class="kom">// Nastav� texturov� koordin�ty</span></p>
<p class="src4">end</p>
<p class="src4">else</p>
<p class="src4">begin</p>
<p class="src5">if (Length(g_3DModel.pMaterials) &lt;&gt; 0) and (pObject.materialID &gt;= 0) then<span class="kom">// Kdy� objekt nem� texturu m� alespo� materi�l?</span></p>
<p class="src6">if g_3DModel.pMaterials[pObject.materialID].bpc = 3 then<span class="kom">// Podle po�tu byt� na barevn� kan�l, pou�ijeme vhodnou funkci pro nastaven� barvy</span></p>
<p class="src7">glColor3ubv(@g_3DModel.pMaterials[pObject.materialID].colorub)</p>
<p class="src6">else</p>
<p class="src7">glColor3fv(@g_3DModel.pMaterials[pObject.materialID].colorf);</p>
<p class="src4">end;</p>
<p class="src"></p>
<p class="src4">glVertex3f(pObject.pVerts[index].x, pObject.pVerts[index].y, pObject.pVerts[index].z);<span class="kom">// Nakonec vykresl�me vertex</span></p>
<p class="src3">end;</p>
<p class="src"></p>
<p class="src2">glEnd;<span class="kom">// Konec vykreslov�n� vertex�</span></p>
<p class="src1">end;</p>
<p class="src"></p>
<p class="src1">glFlush;<span class="kom">// Vypr�zdn� OpenGL renderovac� pipeline</span></p>
<p class="src0">end;</p>

<p>Do funkce WindowProc p�id�me podporu my�i. P�i stisku lev�ho tla��tka my�i dojde ke zm�n� m�du vykreslov�n� na dr�t�n� model a zp�t. Stiskem prav�ho tla��tka my�i se zap�n�/vyp�n� osv�tlen�.</p>

<p class="src0"><span class="kom">// WindowProc</span></p>
<p class="src2">WM_LBUTTONDOWN:<span class="kom">// Obsluha lev�ho tla��tka my�i</span></p>
<p class="src2">begin</p>
<p class="src3">if g_ViewMode = GL_TRIANGLES then</p>
<p class="src4">g_ViewMode := GL_LINE_STRIP<span class="kom">// Zm�na m�du vykreslov�n�</span></p>
<p class="src3">else</p>
<p class="src4">g_ViewMode := GL_TRIANGLES;</p>
<p class="src"></p>
<p class="src3">Result := 0;</p>
<p class="src2">end;</p>
<p class="src"></p>
<p class="src2">WM_RBUTTONDOWN:<span class="kom">// Obsluha prav�ho tla��tka my�i</span></p>
<p class="src2">begin</p>
<p class="src3">g_bLighting := not g_bLighting;<span class="kom">// Zapne/vypne osv�tlen�</span></p>
<p class="src"></p>
<p class="src3">if g_bLighting then</p>
<p class="src4">glEnable(GL_LIGHTING)</p>
<p class="src3">else</p>
<p class="src4">glDisable(GL_LIGHTING);</p>
<p class="src"></p>
<p class="src3">Result := 0;</p>
<p class="src2">end;

<p>Tak a m�me hotovo. Te� u� jen zb�v� n� v�tvor spustit. Po n�kolika probd�l�ch noc�ch s hexav�pisem souboru a kalkula�kou v ruce a dal��ch voln�ch chv�l�ch dne, jsem se kone�n� dobral v�sledku. Uff!!! :-)))</p>

<p class="autor">napsal: Michal Tu�ek <?VypisEmail('michal_praha@seznam.cz');?>, 23.08.2004</p>

<p>C++ p�edloha zdrojov�ho k�du pro �l�nek: <?OdkazBlank('http://www.gametutorials.com/');?> (Pozn.: <span class="warning">N�kter� tutori�ly na gametutorials.com za�aly b�t placen�!)</span></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_gl_3ds.zip');?> - Delphi 7</li>
<li><?OdkazDown('download/clanky/cl_gl_3ds_cpp_sdl.zip');?> - C++, SDL</li>
</ul>

<div class="okolo_img"><img src="images/clanky/cl_gl_3ds/2.jpg" width="604" height="474" alt="Model tv��e" /></div>
<div class="okolo_img"><img src="images/clanky/cl_gl_3ds/3.jpg" width="604" height="475" alt="Model sn�hul�ka" /></div>
<div class="okolo_img"><img src="images/clanky/cl_gl_3ds/4.jpg" width="604" height="475" alt="Model mu�e" /></div>

<?
include 'p_end.php';
?>
