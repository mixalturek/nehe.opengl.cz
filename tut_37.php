<?
$g_title = 'CZ NeHe OpenGL - Lekce 37 - Cel-Shading';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(37);?>

<h1>Lekce 37 - Cel-Shading</h1>

<p class="nadpis_clanku">Cel-Shading je druh vykreslov�n�, p�i kter�m v�sledn� modely vypadaj� jako ru�n� kreslen� karikatury z komiks� (cartoons). Rozli�n� efekty mohou b�t dosa�eny miniaturn� modifikac� zdrojov�ho k�du. Cel-Shading je velmi �sp�n�m druhem renderingu, kter� dok�e kompletn� zm�nit duch hry. Ne ale v�dy... mus� se um�t a pou��t s rozmyslem.</p>

<p>�l�nek o teorii Cel-Shadingu napsal Sami &quot;MENTAL&quot; Hamlaoui a um�stil ho na <?OdkazBlank('http://www.gamedev.net/reference/programming/features/celshading', 'GameDev.net');?>. Pot�, co byl jeho �l�nek publikov�n, zavalili �ten��i Samiho emaily, ve kter�ch se dotazovali po zdrojov�m k�du. Napsal tedy dal�� �l�nek, tentokr�t pro NeHe, kter� u� ale popisuje pouze zdrojov� k�d. Tato �esk� verze je slo�ena z obou �l�nk� - teoretick�ho i praktick�ho.</p>

<h2>Teoretick� ��st</h2>

<p>P�edt�m ne� p�jdete d�l, m�li byste m�t dostate�n� znalosti z n�sleduj�c�ch oblast�:</p>

<ul>
<li>Mapov�n� 1D textur</li>
<li>Texturovac� koordin�ty</li>
<li>Softwarov� osv�tlen�</li>
<li>Vektorov� matematika</li>
</ul>

<p>Pokud n��emu z t�chto �ty� polo�ek nerozum�te, neznamen� to, �e byste nutn� neporozum�li Cel-Shadingu, ale ur�it� budete m�t obrovsk� pot��e s psan�m vlastn�ch program�.</p>

<h3>Z�kladn� rendering</h3>

<p>Za�neme opravdu jednoduch�mi v�cmi. ��dn� sv�tla, ��dn� obrysy, pouze ploch� cartoon modely. Budeme pot�ebovat jenom dva druhy dat - pozici a barvu ka�d�ho vertexu. P�ed kreslen�m v�dy vypneme osv�tlen� a blending. Co by se stalo? P�i zapnut�ch sv�tlech by objekty vypadaly norm�ln�. Nedos�hli bychom ploch�ho cartoon efektu. Blending vyp�n�me, aby se jednotliv� vertexy nesm�chaly s ostatn�mi.</p>

<h4>Shrnuto</h4>

<ul>
<li>Vypnout sv�tla</li>
<li>Vypnout blending</li>
<li>Vykreslit obarven� body</li>
</ul>

<h3>Z�kladn� osv�tlen� (sm�rov�)</h3>

<p>Ka�d� vertex bude pot�ebovat i dal�� data. Krom� p�vodn� pozice a barvy budeme pou��vat i norm�lov� vektor a intenzitu osv�tlen� (jedna float hodnota). Tyto nov� prom�nn� pou�ijeme pro renderov�n� se z�kladn�m osv�tlen�m.</p>

<h4>Sv�teln� mapy (lighting maps)</h4>

<p>Nechci v�s popl�st, pod lightmapami si nep�edstavujte simulaci sv�tel na objektech typu Quake 1 a Quake 2. Pod�vejte se na st�ny, abyste pochopili, co m�m na mysli. Nep�edstavujte si oblasti, kter� jsou osv�tleny/ztmaveny specifick�mi m�sty map. To, co budeme pou��vat zde, je kompletn� novou formou lightmap - 1D textury.</p>

<p>Zkuste si naj�t n�jakou animaci (Cartoon Network je v�dy dobr�m zdrojem) a pod�vejte se na osv�tlen� postav. V�imli jste si, �e nejsou hladk� jako v re�ln�m �ivot�? Sv�tlo se rozd�luje do jednotliv�ch plo�ek. Nikdy jsem nesly�el ��dn� term�n nebo pojmenov�n� pro tento efekt, tak�e mu budeme ��kat Sharp lighting. Abychom ho vytvo�ili pot�ebujeme definovat 1D texturu, kter� bude ukl�dat po�adovan� hodnoty.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_37_texmap.gif" width="257" height="18" alt="1D textura" /></div>

<p>Toto je textura 1x16 pixel� (velmi zv�t�en�). Pou��v�me hodnoty stup�� �edi, proto�e budou zkombinov�ny s barvou vertexu. M��ete si v�imnout, �e v lightmap� jsou pouze 3 barvy, kter� maj� podobnou intenzitu, jak� se pou��v� v animovan�ch filmech. D�ky tomu, �e pou��v�me velikost pr�v� 16 pixel�, m��eme snadno modifikovat hodnoty, abychom vytvo�ili rozli�n� efekty. Pokud chcete, m��ete tak� pou��t oby�ejnou �ernob�lou texturu, ale nedoporu�uje se to. Nikdy byste nem�li pou��t 100% �ernou, proto�e tato barva vytv��� vyzdvi�en� a okraje, kter� vypadaj� dost �patn�.</p>

<p>Jakmile m�te vytvo�enu svou texturu, nahrajte ji do API, kter� pou��v�te (OpenGL, DX, software). Vr�t�me se k n� za chv�li.</p>

<h4>Po��t�n� osv�tlen�</h4>

<p>Te� p�ijdou vhod znalosti ohledn� softwarov�ho osv�tlen�. Pokus�m se v�e vysv�tlit jednoduch�m jazykem. V�dy se ujist�te, �e m�te normalizovan� sm�rov� vektor sv�tla! V�e, co pot�ebujeme ud�lat, je spo��t�n� skal�rn�ho sou�inu vektoru sv�tla s norm�lou vertexu.</p>

<p>Skal�rn� sou�in je matematick� funkce, kter� spo��t� �hel mezi dv�ma vektory a vr�t� ho jako kosinus �hlu. Invertujeme-li kosinus z�sk�me �hel. Nam�sto kosinu v�ak pova�ujte ��slo za texturovac� koordin�tu. Texturovac� koordin�ty jsou ��sla od nuly do jedn�. Kosinus je sice v rozmez� -1 a� 1, ale pokud bude ��slo z�porn� m��eme mu p�i�adit nulu. Skal�rn� sou�in vektoru sv�tla a norm�ly vertexu m��eme tedy pova�ovat za texturovac� koordin�ty!</p>

<h4>Rendering objektu</h4>

<p>Nyn� m�me texturovac� koordin�ty ka�d�ho vertexu, je �as vykreslit objekt. Stejn� jako minule vypneme sv�tla i blending, ale zapneme 1D texturov�n�. Vykresl�me objekt stejn� jako minule, ale p�ed t�m, ne� um�st�me vertex, specifikujeme texturov� koordin�ty (simulace sv�tla).</p>

<h4>Shrnuto</h4>

<ul>
<li>Vytvo�it Sharp lighting mapu</li>
<li>Spo��tat a ulo�it skal�rn� sou�in mezi norm�lou vertexu a sm�rov�m vektorem sv�tla</li>
<li>Vypnout sv�tla a blending</li>
<li>Zapnout texturov�n�</li>
<li>Zvolit texturu lightmapy</li>
<li>Vykreslit polygony ur�en� texturovac�mi koordin�ty, barvou a pozic� vertex�</li>
</ul>

<h3>Um�stiteln� sv�tla</h3>

<p>Tato metoda je pouhou modifikac� minul�ho postupu. Um�stiteln� sv�tlo nab�z� mnohem v�ce flexibility ne� sm�rov� osv�tlen�, proto�e m��e b�t libovoln� posunov�no po sc�n�. Dynamicky osv�tlovan� polygony jsou v�ce realistick�, ale pou�it� matematika je del��. Ne komplikovan�j��, pouze del��.</p>

<h4>Spo��t�n� Sharp koordin�t� sv�tla</h4>

<p>U sm�rov�ho osv�tlen� jsme pot�ebovali z�skat skal�rn� sou�in sm�rov�ho vektoru sv�tla s norm�lou vertexu. Nyn�, proto�e um�stiteln� sv�tla nemaj� sm�rov� vektor (emituj� sv�tlo do v�ech sm�r�), bude m�t ka�d� vertex sv�j paprsek, kter� z��� skrz n�j. Nejd��ve pot�ebujeme ur�it vektor sm��uj�c� z pozice sv�teln�ho zdroje k pozici vertexu. Normalizujeme ho, tak�e bude m�t jednotkovou d�lku. t�m jsme z�skali sm�r sv�tla k vertexu. Vypo��t�me skal�rn� sou�in mezi vektorem sv�tla a norm�lou vertexu. V�e opakujeme pro ka�d� vertex ve sc�n�. T�mito nadbyte�n�mi v�po�ty se v�ak sn��� FPS. Poj�me se pod�vat na rychlej�� metodu, kter� redukuje celkov� po�et osv�tlen�ch vertex�.</p>

<h4>Testov�n� vzd�lenosti od sv�tla</h4>

<p>Ke sn��en� po�tu osv�tlen�ch vertex� p�i�ad�me ka�d�mu sv�tlu polom�r kam paprsky dosahuj�. P�ed po��t�n�m hodnot osv�tlen� (viz. v��e) zkontrolujeme, jestli je vertex v kouli, ur�en� polom�rem sv�tla. Pokud ano, aplikujeme na n�j sv�tla. Je to z�kladn� detekce koliz� s koul�, na kterou existuj� spousty �l�nk� a tutori�l�.</p>

<h4>Rendering</h4>

<p>Objekt vykresl�me stejn� jako u sm�rov�ho osv�tlen�. Specifikujeme barvu, texturovac� koordin�ty a pozici.</p>

<h4>Shrnuto</h4>

<ul>
<li>Vytvo�it Sharp lighting mapu</li>
<li>P�i pou�it� polom�ru sv�tla zjistit, jestli je bod uvnit�</li>
<li>Z�skat a normalizovat vektor od sv�tla k vertexu</li>
<li>Vypo��tat skal�rn� sou�in vektoru s norm�lou vertexu</li>
<li>Zopakovat 2-4x pro ka�d� vertex (P�ekl.: ???)</li>
<li>Renderovat jako minule</li>
</ul>

<h3>Obrysy a zv�razn�n�</h3>

<p>Obrysy a zv�razn�n� jsou tenk� �ern� linky reprezentuj�c� tahy tu�kou, kter� zd�raz�uj� okraje. M��ou znepokojit, ale jejich vytvo�en� je mnohem jednodu��� ne� si mysl�te.</p>

<h4>V�po�et kde zv�raz�ovat</h4>

<p>Pravidlo je jednoduch�: vykreslit linku na okraji, kter� m� jeden p�ivr�cen� a jeden odvr�cen� polygon. Zn� to hloup�, ale zkuste se pod�vat nap��klad na kl�vesnici. V�imli jste si, �e nem��ete vid�t zadn� ��sti kl�ves? To proto, �e jsou odvr�cen�. Na rozhran� vykresl�me ��ru, abychom zv�raznili, �e tam je okraj.</p>

<p>Mo�n�, �e si to ani neuv�domujete, ale nikde jsem se nezm�nil o na�em vlastn�m cullingu polygon�. To proto, �e v�e za n�s ud�l� API, ve kter�m programujeme.</p>

<h4>Rendering zv�razn�n�</h4>

<p>Klasicky vykresl�me objekt a pak nastav�me ���ku ��ry na dva a� t�i pixely. M��eme tak� zapnout antialiasing. Zm�n�me m�d cullingu, aby odstra�oval p�ivr�cen� polygony. P�epneme do dr�t�n�ho modelu, tak�e se budou vykreslovat pouze okrajov� hrany polygon�. Vykresl�me je, ale nepot�ebujeme specifikovat barvu a texturovac� koordin�ty. T�m vykresl�me dr�t�n� model objektu z relativn� �irok�ch linek. Nicm�n�... cullingem jsou linky p�ivr�cen�ch polygon� odstran�ny a depth bufferem se vy�ad� v�echny linky, kter� jsou hloub�ji ne� p�ivr�cen� (tedy ty zadn�). Zd�lo by se, �e tedy nevykresl�me nic. Ale d�ky ���ce ��ry zasahuj� linky okrajov�ch polygon� a� za okraje objektu. Pr�v� ty se vykresl�. Z toho plyne, �e tato metoda nebude pracovat p�i tlou��ce ��ry nastaven� na jeden pixel.</p>

<h4>Shrnuto</h4>

<ul>
<li>Vykreslit objekt jako norm�ln�</li>
<li>P�epnout orientaci fac�</li>
<li>Nastavit 100% �ernou barvu</li>
<li>Zm�nit m�d polygon� na dr�t�n� model</li>
<li>Vykreslit objekt znovu, ale specifikovat pouze pozice vertex�</li>
<li>Obnovit origin�ln� nastaven�</li>
</ul>

<p>To je z teorie asi v�echno. Nyn� se ji pokus�me p�ev�st do praxe.</p>

<h2>Praktick� ��st</h2>

<p>Na za��tku bych se cht�l omluvit za volbu pou�it�ho modelu, ale v posledn� dob� si hodn� hraji s Quake 2...</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src"></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavi�kov� soubor pro matematickou knihovnu</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src"></p>
<p class="src0">#include &quot;NeHeGL.h&quot;<span class="kom">// Hlavi�kov� soubor pro NeHeGL</span></p>

<p>Nadefinujeme p�r struktur, kter� n�m pomohou p�i ukl�d�n� dat. Prvn� z t�chto struktur je tagMATRIX. Pokud se na ni pod�v�te, zjist�te, �e ukl�d� matici jako jednorozm�rn� pole 16ti float� m�sto toho, aby to bylo dvourozm�rn� pole 4x4. To je proto, �e OpenGL pracuje taky s jednorozm�rn�m polem. Pokud bychom pou�ili 4x4, hodnoty by byly ve �patn�m po�ad�.</p>

<p class="src0">typedef struct tagMATRIX<span class="kom">// Ukl�d� OpenGL matici</span></p>
<p class="src0">{</p>
<p class="src1">float Data[16];<span class="kom">// Matice ve form�tu OpenGL</span></p>
<p class="src0">}</p>
<p class="src0">MATRIX;</p>

<p>Dal�� strukturou je vektor, kter� ukl�d� jednotliv� x, y, z slo�ky na os�ch.</p>

<p class="src0">typedef struct tagVECTOR<span class="kom">// Struktura vektoru</span></p>
<p class="src0">{</p>
<p class="src1">float X, Y, Z;<span class="kom">// Slo�ky vektoru</span></p>
<p class="src0">}</p>
<p class="src0">VECTOR;</p>

<p>T�et� je vertexov� struktura. Ka�d� vertex se bude skl�dat z pozice a norm�ly (��dn� texturovac� koordin�ty). Slo�ky struktury mus� b�t v uveden�m po�ad�, jinak se p�i loadov�n� stane n�co OPRAVDU stra�n�ho (s�m jsem kv�li tomu rozsekal cel� tento k�d, abych na�el chybu).</p>

<p class="src0">typedef struct tagVERTEX<span class="kom">// Struktura vertexu</span></p>
<p class="src0">{</p>
<p class="src1">VECTOR Nor;<span class="kom">// Norm�la vertexu</span></p>
<p class="src1">VECTOR Pos;<span class="kom">// Pozice vertexu</span></p>
<p class="src0">}</p>
<p class="src0">VERTEX;</p>

<p>Nakonec struktura polygonu. V�m, �e toto nen� nejlep�� zp�sob, jak ukl�dat vertexy, ale pro jednoduchost to sta��. Norm�ln� bych pou�il pole vertex� a pole polygon� obsahuj�c�ch indexy vertex� tvo��c�ch polygon, ale my to ud�l�me jinak - v�e pro jednoduchost.</p>

<p class="src0">typedef struct tagPOLYGON<span class="kom">// Struktura polygonu</span></p>
<p class="src0">{</p>
<p class="src1">VERTEX Verts[3];<span class="kom">// Pole t�� vertex�</span></p>
<p class="src0">}</p>
<p class="src0">POLYGON;</p>

<p>N�dhern� jednoduch� ��st k�du. P�e�t�te si koment�� ke ka�d� prom�nn� a budete v�d�t, pro� jsme ji deklarovali.</p>

<p class="src0">bool outlineDraw = true;<span class="kom">// Flag pro vykreslov�n� obrysu</span></p>
<p class="src0">bool outlineSmooth = false;<span class="kom">// Flag pro vyhlazov�n� �ar</span></p>
<p class="src0">float outlineColor[3] = { 0.0f, 0.0f, 0.0f };<span class="kom">// Barva �ar</span></p>
<p class="src0">float outlineWidth = 3.0f;<span class="kom">// Tlou��ka �ar</span></p>
<p class="src"></p>
<p class="src0">VECTOR lightAngle;<span class="kom">// Sm�r sv�tla</span></p>
<p class="src0">bool lightRotate = false;<span class="kom">// Flag oznamuj�c� zda rotujeme sv�tlem</span></p>
<p class="src"></p>
<p class="src0">float modelAngle = 0.0f;<span class="kom">// �hel nato�en� objektu na ose y</span></p>
<p class="src0">bool modelRotate = false;<span class="kom">// Flag na ot��en� modelem</span></p>
<p class="src"></p>
<p class="src0">POLYGON* polyData = NULL;<span class="kom">// Data polygon�</span></p>
<p class="src0">int polyNum = 0;<span class="kom">// Po�et polygon�</span></p>
<p class="src"></p>
<p class="src0">GLuint shaderTexture[1];<span class="kom">// M�sto pro jednu texturu</span></p>

<p>Model je ulo�en �pln� nejjednodu���m zp�sobem. Prvn�ch p�r bajt� obsahuje po�et polygon� tvo��c�ch objekt a zbytek souboru je pole tagPOLYGON struktur. Proto m��e n�sleduj�c� funkce data p��mo na��st bez jak�hokoliv dal��ho upravov�n�.</p>

<p class="src0">BOOL ReadMesh()<span class="kom">// Na�te obsah souboru model.txt</span></p>
<p class="src0">{</p>
<p class="src1">FILE *In = fopen(&quot;Data\\model.txt&quot;, &quot;rb&quot;);<span class="kom">// Otev�e soubor</span></p>
<p class="src"></p>
<p class="src1">if (!In)<span class="kom">// Kontrola chyby otev�en�</span></p>
<p class="src2">return FALSE;</p>
<p class="src"></p>
<p class="src1">fread(&amp;polyNum, sizeof(int), 1, In);<span class="kom">// Na�te hlavi�ku souboru (po�et vertex�)</span></p>
<p class="src"></p>
<p class="src1">polyData = new POLYGON[polyNum];<span class="kom">// Alokace pam�ti</span></p>
<p class="src1">fread(&amp;polyData[0], sizeof(POLYGON) * polyNum, 1, In);<span class="kom">// Na�te v�echna data</span></p>
<p class="src"></p>
<p class="src1">fclose(In);<span class="kom">// Zav�e soubor</span></p>
<p class="src1">return TRUE;<span class="kom">// Loading objektu �sp�n�</span></p>
<p class="src0">}</p>

<p>Funkce DotProduct() spo��t� �hel mezi dv�ma vektory nebo rovinami. Funkce Magnitude() spo��t� d�lku vektoru a funkce Normalize() uprav� vektor na jednotkovou d�lku.</p>

<p class="src0">inline float DotProduct(VECTOR &amp;V1, VECTOR &amp;V2)<span class="kom">// Spo��t� odchylku dvou vektor�</span></p>
<p class="src0">{</p>
<p class="src1">return V1.X * V2.X + V1.Y * V2.Y + V1.Z * V2.Z;<span class="kom">// Vr�t� �hel</span></p>
<p class="src0">}</p>
<p class="src"></p>

<p class="src0">inline float Magnitude(VECTOR &amp;V)<span class="kom">// Spo��t� d�lku vektoru</span></p>
<p class="src0">{</p>
<p class="src1">return sqrtf(V.X * V.X + V.Y * V.Y + V.Z * V.Z);<span class="kom">// Vr�t� d�lku vektoru</span></p>
<p class="src0">}</p>
<p class="src"></p>

<p class="src0">void Normalize(VECTOR &amp;V)<span class="kom">// Vytvo�� jednotkov� vektor</span></p>
<p class="src0">{</p>
<p class="src1">float M = Magnitude(V);<span class="kom">// Spo��t� aktu�ln� d�lku vektoru</span></p>
<p class="src"></p>
<p class="src1">if (M != 0.0f)<span class="kom">// Proti d�len� nulou</span></p>
<p class="src1">{</p>
<p class="src2">V.X /= M;<span class="kom">// Normalizov�n� jednotliv�ch slo�ek</span></p>
<p class="src2">V.Y /= M;</p>
<p class="src2">V.Z /= M;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Funkce RotateVector() pooto�� vektor podle zadan� matice. V�imn�te si, �e vektor pouze oto��, ale u� nic ned�l� s jeho pozic�. Funkce se pou��v� pro ot��en� norm�l, aby zajistila, �e norm�ly budou p�i po��t�n� osv�tlen� ukazovat spr�vn�m sm�rem.</p>

<p class="src0">void RotateVector(MATRIX &amp;M, VECTOR &amp;V, VECTOR &amp;D)<span class="kom">// Rotace vektoru podle zadan� matice</span></p>
<p class="src0">{</p>
<p class="src1">D.X = (M.Data[0] * V.X) + (M.Data[4] * V.Y) + (M.Data[8]  * V.Z);<span class="kom">// Oto�en� na x</span></p>
<p class="src1">D.Y = (M.Data[1] * V.X) + (M.Data[5] * V.Y) + (M.Data[9]  * V.Z);<span class="kom">// Oto�en� na y</span></p>
<p class="src1">D.Z = (M.Data[2] * V.X) + (M.Data[6] * V.Y) + (M.Data[10] * V.Z);<span class="kom">// Oto�en� na z</span></p>
<p class="src0">}</p>

<p>Prvn� v�znamn�j�� funkc� tohoto enginu je Initialize(), kter� prov�d� to, co je z jej�ho n�zvu zjevn� - inicializaci.</p>

<p class="src0">BOOL Initialize (GL_Window* window, Keys* keys)<span class="kom">// U�ivatelsk� a OpenGL inicializace</span></p>
<p class="src0">{</p>
<p class="src1">int i;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>

<p>N�sleduj�c� 3 prom�nn� jsou pou�ity pro na�ten� shader souboru. Line obsahuje jeden ��dek �et�zce a pole shaderData ukl�d� hodnoty pro shading. Pou��v�me 96 hodnot nam�sto 32, proto�e pot�ebujeme p�ev�st stupn� �edi na hodnoty RGB, aby s nimi mohlo OpenGL pracovat. M��eme sice hodnoty ulo�it jako stupn� �edi, ale bude jednodu��� kdy� p�i nahr�v�n� textury pou�ijeme stejn� hodnoty pro jednotliv� slo�ky RGB.</p>

<p class="src1">char Line[255];<span class="kom">// Pole 255 znak�</span></p>
<p class="src1">float shaderData[32][3];<span class="kom">// Pole 96 shader hodnot</span></p>
<p class="src"></p>
<p class="src1">FILE *In = NULL;<span class="kom">// Ukazatel na soubor</span></p>

<p>Klasick� nastaven� enginu a OpenGL...</p>

<p class="src1">g_window = window;</p>
<p class="src1">g_keys = keys;</p>
<p class="src"></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivn� korekce</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.7f, 0.7f, 0.7f, 0.0f);<span class="kom">// Sv�tle �ed� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom">// Typ testov�n� hloubky</span></p>

<p>P�i vykreslov�n� �ar chceme, aby byly p�kn� vyhlazen�. Implicitn� je tato funkce vypnuta, ale stiskem kl�vesy 2 ji m��eme zap�nat a vyp�nat podle libosti.</p>

<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glDisable(GL_LINE_SMOOTH);<span class="kom">// Vypne vyhlazov�n� �ar</span></p>

<p>Zapneme o�ez�v�n� vnit�n�ch st�n objektu, kter� stejn� nejsou vid�t a vypneme OpenGL sv�tla, proto�e pot�ebn� v�po�ty provedeme po sv�m.</p>

<p class="src1">glEnable(GL_CULL_FACE);<span class="kom">// Zapne face culling (o�ez�v�n� st�n)</span></p>
<p class="src1">glDisable(GL_LIGHTING);<span class="kom">// Vypne sv�tla</span></p>

<p>V dal�� ��sti k�du na�teme shader soubor. Obsahuje pouze 32 desetinn�ch ��sel ulo�en�ch, pro jednoduchou modifikaci, v ASCII form�tu, ka�d� na samostatn�m ��dku.</p>

<p class="src1">In = fopen(&quot;Data\\shader.txt&quot;, &quot;r&quot;);<span class="kom">// Otev�en� shader souboru</span></p>
<p class="src"></p>
<p class="src1">if (In)<span class="kom">// Kontrola, zda je soubor otev�en</span></p>
<p class="src1">{</p>
<p class="src2">for (i = 0; i &lt; 32; i++)<span class="kom">// Projde v�ech 32 hodnot ve stupn�ch �edi</span></p>
<p class="src2">{</p>
<p class="src3">if (feof(In))<span class="kom">// Kontrola konce souboru</span></p>
<p class="src4">break;</p>
<p class="src"></p>
<p class="src3">fgets(Line, 255, In);<span class="kom">// Z�sk�n� aktu�ln�ho ��dku</span></p>

<p>P�em�n�me na�ten� stupn� �edi na RGB, jak jsme si popsali v��e.</p>

<p class="src3"><span class="kom">// Zkop�ruje danou hodnotu do v�ech slo�ek barvy</span></p>
<p class="src3">shaderData[i][0] = shaderData[i][1] = shaderData[i][2] = float(atof(Line));</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">fclose(In);<span class="kom">// Zav�e soubor</span></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Ne�sp�ch</span></p>
<p class="src1">}</p>

<p>Nahrajeme texturu p�esn� tak, jak je. Bez pou�it� filtrov�n�, jinak by v�sledek vypadal opravdu hnusn�, p�inejmen��m. Pou�ijeme GL_TEXTURE_1D, proto�e jde o jednorozm�rn� pole hodnot.</p>

<p class="src1">glGenTextures(1, &amp;shaderTexture[0]);<span class="kom">// Z�sk�n� ID textury</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_1D, shaderTexture[0]);<span class="kom">// P�i�azen� textury; od te� je 1D texturou</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nikdy nepou��vejte bi-/trilinearn� filtrov�n�!</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_1D, GL_TEXTURE_MAG_FILTER, GL_NEAREST);</p>
<p class="src1">glTexParameteri(GL_TEXTURE_1D, GL_TEXTURE_MIN_FILTER, GL_NEAREST);</p>
<p class="src"></p>
<p class="src1">glTexImage1D(GL_TEXTURE_1D, 0, GL_RGB, 32, 0, GL_RGB , GL_FLOAT, shaderData);<span class="kom">// Upload dat</span></p>

<p>Nastav�me sm�r dopad�n� sv�tla na objekt ze sm�ru kladn� ��sti osy z. Ve sv�m d�sledku to znamen�, �e sv�tlo bude zep�edu sv�tit na model.</p>

<p class="src1">lightAngle.X = 0.0f;<span class="kom">// Nastaven� sm�ru x</span></p>
<p class="src1">lightAngle.Y = 0.0f;<span class="kom">// Nastaven� sm�ru y</span></p>
<p class="src1">lightAngle.Z = 1.0f;<span class="kom">// Nastaven� sm�ru z</span></p>
<p class="src"></p>
<p class="src1">Normalize(lightAngle);<span class="kom">// Normalizov�n� vektoru sv�tla</span></p>

<p>Na�ten� tvaru ze souboru (funkce pops�na v��e).</p>

<p class="src1">return ReadMesh();<span class="kom">// Vr�t� n�vratovou hodnotu funkce ReadMesh()</span></p>
<p class="src0">}</p>

<p>Funkce Deinitialize() je prav�m opakem p�edchoz� funkce. Sma�e texturu a data polygon� nahran� pomoc� funkc� Initialize() a ReadMesh().</p>

<p class="src0">void Deinitialize(void)<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">glDeleteTextures(1, &amp;shaderTexture[0]);<span class="kom">// Sma�e shader texturu</span></p>
<p class="src1"></p>
<p class="src1">delete [] polyData;<span class="kom">// Uvoln� data polygon�</span></p>
<p class="src0">}</p>

<p>Funkce Update() se periodicky vol� v hlavn� smy�ce tohoto dema. Jedinou jej� funkc� je zpracov�n� vstupu z kl�vesnice.</p>

<p class="src0">void Update(DWORD milliseconds)<span class="kom">// Aktualizace sc�ny (objektu)</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown [VK_ESCAPE] == TRUE)<span class="kom">// Kl�vesa ESC</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication (g_window);<span class="kom">// Ukon�en� programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown [VK_F1] == TRUE)<span class="kom">// Kl�vesa F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// P�epnut� m�d� fullscreen/okno</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown [' '] == TRUE)<span class="kom">// Mezern�k</span></p>
<p class="src1">{</p>
<p class="src2">modelRotate = !modelRotate;<span class="kom">// Zapne/vypne rotaci objektu</span></p>
<p class="src"></p>
<p class="src2">g_keys-&gt;keyDown [' '] = FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown ['1'] == TRUE)<span class="kom">// Kl�vesa ��sla 1</span></p>
<p class="src1">{</p>
<p class="src2">outlineDraw = !outlineDraw;<span class="kom">// Zapne/vypne vykreslov�n� obrysu</span></p>
<p class="src"></p>
<p class="src2">g_keys-&gt;keyDown ['1'] = FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown ['2'] == TRUE)<span class="kom">// Kl�vesa ��slo 2</span></p>
<p class="src1">{</p>
<p class="src2">outlineSmooth = !outlineSmooth;<span class="kom">// Zapne/vypne anti-aliasing</span></p>
<p class="src"></p>
<p class="src2">g_keys-&gt;keyDown ['2'] = FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown [VK_UP] == TRUE)<span class="kom">// �ipka nahoru</span></p>
<p class="src1">{</p>
<p class="src2">outlineWidth++;<span class="kom">// Zv�t�� tlou��ku ��ry</span></p>
<p class="src"></p>
<p class="src2">g_keys-&gt;keyDown [VK_UP] = FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown [VK_DOWN] == TRUE)<span class="kom">// �ipka dol�</span></p>
<p class="src1">{</p>
<p class="src2">outlineWidth--;<span class="kom">// Zmen�� tlou��ku ��ry</span></p>
<p class="src"></p>
<p class="src2">g_keys-&gt;keyDown [VK_DOWN] = FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (modelRotate)<span class="kom">// Je rotace zapnut�</span></p>
<p class="src2">modelAngle += (float)(milliseconds) / 10.0f;<span class="kom">// Aktualizace �hlu nato�en� v z�vislosti na FPS</span></p>
<p class="src0">}</p>

<p>Funkce, na kterou u� ur�it� netrp�liv� �ek�te. Draw() prov�d� v�t�inu nejd�le�it�j�� pr�ce v tomto tutori�lu - po��t� hodnoty st�nu, renderuje dan� tvar a renderuje obrys.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">int i, j;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>

<p>Prom�nn� TmpShade se pou�ije na ulo�en� do�asn� hodnoty st�nu pro aktu�ln� vertex. V�echna data t�kaj�c�ho se jednoho vertexu jsou spo��t�na ve stejn�m �ase, co� znamen�, �e m��eme pou��t jen jednu prom�nnou, kterou postupn� pou�ijeme pro v�echny vertexy. Struktury TmpMatrix, TmpVector a TmpNormal jsou tak� pou�ity pro spo��t�n� dat jednoho vertexu. TmpMatrix se nastav� v�dy jednou p�i startu funkce Draw() a nezm�n� se a� do jej�ho dal��ho startu. TmpVector a TmpNormal se li�� vertex od vertexu.</p>

<p class="src1">float TmpShade;<span class="kom">// Do�asn� hodnota st�nu</span></p>
<p class="src"></p>
<p class="src1">MATRIX TmpMatrix;<span class="kom">// Do�asn� MATRIX struktura</span></p>
<p class="src1">VECTOR TmpVector, TmpNormal;<span class="kom">// Do�asn� VECTOR struktury</span></p>

<p>Po deklaraci prom�nn�ch vyma�eme buffery a data OpenGL matice.</p>

<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Nejd��ve zkontrolujeme zda chceme obrys vyhlazen�. Kdy� ano, zapneme anti-aliasing. Kdy� ne, tak ho vypneme. Jak jednoduch�...</p>

<p class="src1">if (outlineSmooth)<span class="kom">// Chce u�ivatel vyhlazen� ��ry?</span></p>
<p class="src1">{</p>
<p class="src2">glHint(GL_LINE_SMOOTH_HINT, GL_NICEST);<span class="kom">// Pou�ije nejkvalitn�j�� v�po�ty</span></p>
<p class="src2">glEnable(GL_LINE_SMOOTH);<span class="kom">// Zapne anti-aliasing</span></p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Nechce</span></p>
<p class="src1">{</p>
<p class="src2">glDisable(GL_LINE_SMOOTH);<span class="kom">// Vypne anti-aliasing</span></p>
<p class="src1">}</p>

<p>Posunut�m kamery o 2 jednotky dozadu nastav�me pohled, potom model pooto��me o dan� �hel. Pozn�mka: proto�e jsme nejd��ve pohnuli s kamerou, model se bude to�it na m�st�. Pokud bychom to ud�lali opa�n�, model by rotoval kolem kamery.</p>

<p class="src1">glTranslatef(0.0f, 0.0f, -2.0f);<span class="kom">// Posun do hloubky</span></p>
<p class="src1">glRotatef(modelAngle, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace objektem na ose y</span></p>

<p>Z�sk�me nov� vytvo�enou OpenGL matici a ulo��me ji do TmpMatrix.</p>

<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, TmpMatrix.Data);<span class="kom">// Z�sk�n� matice</span></p>

<p>Kouzla za��naj�... Povol�me 1D texturov�n� a pou�ijeme texturu st�nu. Potom nastav�me barvu modelu. Vybral jsem b�lou, proto�e na n� jde l�pe vid�t sv�tlo a st�n ne� na ostatn�ch barv�ch. Nejm�n� vhodn� je zcela ur�it� �ern�.</p>

<p class="src1"><span class="kom">// K�d Cel-Shadingu</span></p>
<p class="src1">glEnable(GL_TEXTURE_1D);<span class="kom">// Zapne 1D texturov�n�</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_1D, shaderTexture[0]);<span class="kom">// Zvol� texturu</span></p>
<p class="src"></p>
<p class="src1">glColor3f(1.0f, 1.0f, 1.0f);<span class="kom">// Nastaven� barvy modelu (b�l�)</span></p>

<p>Za�neme s kreslen�m troj�heln�k�. Projdeme v�echny polygony v poli a v�echny vertexy ka�d�ho z t�chto polygon�. Nejd��ve zkop�rujeme norm�lu do do�asn� struktury. D�ky tomu m��eme hodnotami norm�ly ot��et bez toho, �e bychom ztratili p�vodn� data (bez pr�b�n� degradace).</p>

<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek kreslen� troj�heln�k�</span></p>
<p class="src"></p>
<p class="src2">for (i = 0; i &lt; polyNum; i++)<span class="kom">// Proch�z� jednotliv� polygony</span></p>
<p class="src2">{</p>
<p class="src3">for (j = 0; j &lt; 3; j++)<span class="kom">// Proch�z� jednotliv� vertexy</span></p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// Zkop�rov�n� aktu�ln� norm�ly do do�asn� struktury</span></p>
<p class="src4">TmpNormal.X = polyData[i].Verts[j].Nor.X;</p>
<p class="src4">TmpNormal.Y = polyData[i].Verts[j].Nor.Y;</p>
<p class="src4">TmpNormal.Z = polyData[i].Verts[j].Nor.Z;</p>

<p>Oto��me vektor o matici, kterou jsme z�skali od OpenGL a normalizujeme ho.</p>

<p class="src4">RotateVector(TmpMatrix, TmpNormal, TmpVector);<span class="kom">// Oto�� vektor podle matice</span></p>
<p class="src"></p>
<p class="src4">Normalize(TmpVector);<span class="kom">// Normalizace norm�ly</span></p>

<p>Spo��t�me odchylku pooto�en� norm�ly a sm�ru sv�tla. Potom hodnotu d�me do rozmez� 0-1 (z p�vodn�ho -1 a� 1).</p>

<p class="src4">TmpShade = DotProduct(TmpVector, lightAngle);<span class="kom">// Spo��t�n� hodnoty st�nu</span></p>
<p class="src"></p>
<p class="src4">if (TmpShade &lt; 0.0f)<span class="kom">// Pokud je TmpShade men�� ne� nula bude se rovnat nule</span></p>
<p class="src5">TmpShade = 0.0f;</p>

<p>P�ed�me tuto hodnotu OpenGL jako texturovac� sou�adnici. Potom p�ed�me pozici vertexu a opakujeme. A opakujeme. A opakujeme. Mysl�m, �e podstatu u� ch�pete.</p>

<p class="src4">glTexCoord1f(TmpShade);<span class="kom">// Nastaven� texturovac� sou�adnice na hodnotu st�nu</span></p>
<p class="src4">glVertex3fv(&amp;polyData[i].Verts[j].Pos.X);<span class="kom">// Po�le pozici vertexu</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src"></p>
<p class="src1">glDisable(GL_TEXTURE_1D);<span class="kom">// Vypne 1D texturov�n�</span></p>

<p>P�esuneme se k obrys�m. Obrys m��eme definovat jako hranu, kde je jeden polygon p�ivr�cen sm�rem k n�m a druh� od n�s. Pou�ijeme pro OpenGL b�n� testov�n� hloubky - m�n� nebo stejn� (GL_LEQUAL) a tak� nastav�me vy�azov�n� v�ech polygon� oto�en�ch k n�m. Tak� pou�ijeme blending, aby to trochu vypadalo.</p>

<p>Nastav�me OpenGL tak, aby polygony �elem od n�s vyrenderoval jako ��ry. Vy�ad�me v�echny polygony �elem k n�m a nastav�me testov�n� hloubky na men�� nebo stejn� na aktu�ln� ose Z. Potom je�t� nastav�me barvu �ar, projdeme v�echny polygony a vykresl�me jejich rohy. Sta�� zadat pozici. Nemus�me zad�vat norm�lu a st�ny, proto�e chceme jenom obrys.</p>

<p class="src1"><span class="kom">// K�d pro vykreslen� obrys�</span></p>
<p class="src1">if (outlineDraw)<span class="kom">// Chceme v�bec kreslit obrys?</span></p>
<p class="src1">{</p>
<p class="src2">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src2">glBlendFunc(GL_SRC_ALPHA,GL_ONE_MINUS_SRC_ALPHA);<span class="kom">// M�d blendingu</span></p>
<p class="src"></p>
<p class="src2">glPolygonMode(GL_BACK, GL_LINE);<span class="kom">// Odvr�cen� polygony se stanout pouze obrysov�mi �arami</span></p>
<p class="src2">glLineWidth(outlineWidth);<span class="kom">// Nastaven� ���ky ��ry</span></p>
<p class="src"></p>
<p class="src2">glCullFace(GL_FRONT);<span class="kom">// Nerenderovat p�ivr�cen� polygony</span></p>
<p class="src2">glDepthFunc(GL_LEQUAL);<span class="kom">// M�d testov�n� hloubky</span></p>
<p class="src"></p>
<p class="src2">glColor3fv(&amp;outlineColor[0]);<span class="kom">// Barva obrysu (�ern�)</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek kreslen� troj�heln�k�</span></p>
<p class="src"></p>
<p class="src3">for (i = 0; i &lt; polyNum; i++)<span class="kom">// Proch�z� jednotliv� polygony</span></p>
<p class="src3">{</p>
<p class="src4">for (j = 0; j &lt; 3; j++)<span class="kom">// Proch�z� jednotliv� vertexy</span></p>
<p class="src4">{</p>
<p class="src5">glVertex3fv(&amp;polyData[i].Verts[j].Pos.X);<span class="kom">// Po�le pozici vertexu</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Na konci u� jenom vr�t�me nastaven� do p�vodn�ho stavu a ukon��me funkci i tutori�l.</p>

<p class="src2">glDepthFunc(GL_LESS);<span class="kom">// Testov�n� hloubky na p�vodn� nastaven�</span></p>
<p class="src2"></p>
<p class="src2">glCullFace(GL_BACK);<span class="kom">// Nastaven� o�ez�v�n� na p�vodn� hodnotu</span></p>
<p class="src2">glPolygonMode(GL_BACK, GL_FILL);<span class="kom">// Norm�ln� vykreslov�n�</span></p>
<p class="src2"></p>
<p class="src2">glDisable(GL_BLEND);<span class="kom">// Vypne blending</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p class="autor">napsal: Sami &quot;MENTAL&quot; Hamlaoui <?VypisEmail('disk_disaster@hotmail.com');?><br />
teoretickou ��st p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?><br />
praktickou ��st p�elo�il: V�clav Slov��ek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson37.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson37_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson37.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson37.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson37.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson37.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson37.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson37.tar.gz">Linux / GLut</a> k�d t�to lekce. ( <a href="mailto:rainmaker@xs4all.nl">Kah</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson37.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Schubert_P@Yahoo.de">Patrick Schubert</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson37.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:drfnbee@wanadoo.fr">Sean Farrell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson37.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson37.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(37);?>
<?FceNeHeOkolniLekce(37);?>

<?
include 'p_end.php';
?>
