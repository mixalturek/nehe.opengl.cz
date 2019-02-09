<?
$g_title = 'CZ NeHe OpenGL - Lekce 34 - Generov�n� ter�n� a krajin za pou�it� v��kov�ho mapov�n� textur';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(34);?>

<h1>Lekce 34 - Generov�n� ter�n� a krajin za pou�it� v��kov�ho mapov�n� textur</h1>

<p class="nadpis_clanku">Cht�li byste vytvo�it v�rnou simulaci krajiny, ale nev�te, jak na to? Bude n�m sta�it oby�ejn� 2D obr�zek ve stupn�ch �edi, pomoc� kter�ho deformujeme rovinu do t�et�ho rozm�ru. Na prvn� pohled t�ko �e�iteln� probl�my b�vaj� �astokr�t velice jednoduch�.</p>

<p>Nyn� byste u� m�li b�t opravdov�mi experty na OpenGL, ale mo�n� nev�te, co to je v��kov� mapov�n� (height mapping). P�edstavte si rovinu, vytla�enou podle n�jak� formy do 3D prostoru. T�to form� se ��k� v��kov� mapa, kterou m��e b�t defakto jak�koli typ dat. Obr�zky, textov� soubory nebo t�eba datov� proud zvuku - z�le�� jen na v�s. My budeme pou��vat .RAW obr�zek ve stupn�ch �edi.</p>

<p>Definujeme t�i opravdu d�le�it� symbolick� konstanty. MAP_SIZE p�edstavuje rozm�r mapy, v na�em p��pad� se jedn� o ���ku/v��ku obr�zku (1024x1024). Konstanta STEP_SIZE ur�uje velikost krok� p�i grabov�n� hodnot z obr�zku. V sou�asn� chv�li bereme v �vahu ka�d� �estn�ct� pixel. Zmen�en�m ��sla p�id�v�me do v�sledn�ho povrchu polygony, tak�e vypad� m�n� hranat�, ale z�rove� zvy�ujeme n�ro�nost na rendering. HEIGHT_RATIO slou�� jako m���tko v��ky na ose y. Mal� ��slo zredukuje vysok� hory s �dol�mi na plochou rovinu.</p>

<p class="src0">#define MAP_SIZE 1024<span class="kom">// Velikost .RAW obr�zku v��kov� mapy</span></p>
<p class="src0">#define STEP_SIZE 16<span class="kom">// Hustota grabov�n� pixel�</span></p>
<p class="src0">#define HEIGHT_RATIO 1.5f<span class="kom">// Zoom v��ky ter�nu na ose y</span></p>

<p>Prom�nn� bRender p�edstavuje p�ep�na� mezi pevn�mi polygony a dr�t�n�m modelem, scaleValue ur�uje zoom sc�ny na v�ech t�ech os�ch.</p>

<p class="src0">bool bRender = TRUE;<span class="kom">// Polygony - true, dr�t�n� model - false</span></p>
<p class="src0">float scaleValue = 0.15f;<span class="kom">// M���tko velikosti ter�nu (v�echny osy)</span></p>

<p>Deklarujeme jednorozm�rn� pole pro ulo�en� v�ech dat v��kov� mapy. Pou��van� .RAW obr�zek neobsahuje RGB slo�ky barvy, ale ka�d� pixel je tvo�en jedn�m bytem, kter� specifikuje jeho odst�n. Nicm�n� o barvu se starat nebudeme, jde n�m p�edev��m o hodnoty. ��slo 255 bude p�edstavovat nejvy��� mo�n� bod povrchu a nula nejni���.</p>

<p class="src0">BYTE g_HeightMap[MAP_SIZE * MAP_SIZE];<span class="kom">// Ukl�d� data v��kov� mapy</span></p>

<p>Funkce LoadRawFile() nahr�v� RAW soubor s obr�zkem. Nic komplexn�ho! V parametrech se j� p�ed�v� �et�zec diskov� cesty, velikost dat obr�zku a ukazatel na pam�, do kter� se ukl�d�. Otev�eme soubor pro �ten� v bin�rn�m m�du a o�et��me situaci, kdy neexistuje.</p>

<p class="src0">void LoadRawFile(LPSTR strName, int nSize, BYTE* pHeightMap)<span class="kom">// Nahraje .RAW soubor</span></p>
<p class="src0">{</p>
<p class="src1">FILE *pFile = NULL;<span class="kom">// Handle souboru</span></p>
<p class="src"></p>
<p class="src1">pFile = fopen(strName, &quot;rb&quot;);<span class="kom">// Otev�en� souboru pro �ten� v bin�rn�m m�du</span></p>
<p class="src"></p>
<p class="src1">if (pFile == NULL)<span class="kom">// Otev�en� v po��dku?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Can't Find The Height Map!&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return;</p>
<p class="src1">}</p>

<p>Pomoc� fread() na�teme po jednom bytu ze souboru pFile data o velikosti nSize a ulo��me je do pam�ti na lokaci pHeightMap. Vyskytne-li se chyba, vyp�eme varovnou zpr�vu.</p>

<p class="src1">fread(pHeightMap, 1, nSize, pFile);<span class="kom">// Na�te soubor do pam�ti</span></p>
<p class="src"></p>
<p class="src1">int result = ferror(pFile);<span class="kom">// V�sledek na��t�n� dat</span></p>
<p class="src"></p>
<p class="src1">if (result)<span class="kom">// Nastala chyba?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Failed To Get Data!&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src1">}</p>

<p>Na konci zb�v� u� jenom zav��t soubor.</p>

<p class="src1">fclose(pFile);<span class="kom">// Zav�en� souboru</span></p>
<p class="src0">}</p>

<p>K�d pro inicializaci OpenGL byste m�li bez probl�m� pochopit sami.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Inicializace OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testov�n� hloubky</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivn� korekce</span></p>

<p>P�ed vr�cen�m true je�t� do g_HeightMap nahrajeme .RAW obr�zek.</p>

<p class="src1">LoadRawFile(&quot;Data/Terrain.raw&quot;, MAP_SIZE * MAP_SIZE, g_HeightMap);<span class="kom">// Na�ten� dat v��kov� mapy</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�e v po��dku</span></p>
<p class="src0">}</p>

<p>M�me zde jeden probl�m - ulo�ili jsme dvourozm�rn� obr�zek do jednorozm�rn�ho pole. Co s t�m? Funkce Height() provede v�po�et pro transformaci x, y sou�adnic na index do tohoto pole a vr�t� hodnotu, kter� je na n�m ulo�en�. P�i pr�ci s poli bychom se v�dy m�li start o mo�nost p�ete�en� pam�ti. Jednoduch�m trikem zmen��me vysok� hodnoty tak, aby byly v�dy platn�. Pokud n�kter� z hodnot p�es�hne dan� index, zbytek po d�len� ji zmen�� do rozmez�, kter� m��eme bez obav pou��t. D�le otestujeme, jestli se v poli opravdu nach�zej� data.</p>

<p class="src0">int Height(BYTE *pHeightMap, int X, int Y)<span class="kom">// P�epo��t� 2D sou�adnice na 1D a vr�t� ulo�enou hodnotu</span></p>
<p class="src0">{</p>
<p class="src1">int x = X % MAP_SIZE;<span class="kom">// Proti p�ete�en� pam�ti</span></p>
<p class="src1">int y = Y % MAP_SIZE;</p>
<p class="src"></p>
<p class="src1">if(!pHeightMap)<span class="kom">// Obsahuje pam� data?</span></p>
<p class="src1">{</p>
<p class="src2">return 0;</p>
<p class="src1">}</p>

<p>Aby se jednorozm�rn� pole chovalo jako dvojrozm�rn�, mus�me zapojit trochu matematiky. Index do 1D pole na 2D sou�adnic�ch z�sk�me tak, �e vyn�sob�me ��dek (y) jeho ���kou (MAP_SIZE) a p�i�teme konkr�tn� pozici na ��dku (x).</p>

<p class="src1">return pHeightMap[(y * MAP_SIZE) + x];<span class="kom">// Vr�t� hodnotu z pole</span></p>
<p class="src0">}</p>

<p>Na tomto m�st� nastavujeme barvu vertexu podle aktu�ln� v��ky nad height mapou. Z�sk�me hodnotu na indexu pole a d�len�m 256.0f ji zmen��me do rozmez� 0.0f a� 1.0f. Abychom ji je�t� trochu ztmavili, ode�teme -0.15f. V�sledek p�ed�me funkci glColor3f() jako modrou slo�ku barvy.</p>

<p class="src0">void SetVertexColor(BYTE *pHeightMap, int x, int y)<span class="kom">// Z�sk� barvu v z�vislosti na v��ce</span></p>
<p class="src0">{</p>
<p class="src1">if(!pHeightMap)<span class="kom">// Obsahuje pam� data?</span></p>
<p class="src1">{</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Z�sk�n� hodnoty, p�epo�et do rozmez� 0.0f a� 1.0f, ztmaven�</span></p>
<p class="src1">float fColor = (Height(pHeightMap, x, y) / 256.0f) - 0.15f;</p>
<p class="src"></p>
<p class="src1">glColor3f(0, 0, fColor);<span class="kom">// Odst�ny modr� barvy</span></p>
<p class="src0">}</p>

<p>Dost�v�me se k nejpodstatn�j�� ��sti cel�ho tutori�lu - renderov�n� ter�nu. Prom�nn� X, Y slou�� k proch�zej� v��kov� mapy a x, y, z jsou 3D sou�adnicemi vertexu.</p>

<p class="src0">void RenderHeightMap(BYTE pHeightMap[])<span class="kom">// Renderuje ter�n</span></p>
<p class="src0">{</p>
<p class="src1">int X = 0, Y = 0;<span class="kom">// Pro proch�zen� polem</span></p>
<p class="src1">int x, y, z;<span class="kom">// Sou�adnice vertex�</span></p>
<p class="src"></p>
<p class="src1">if(!pHeightMap)<span class="kom">// Obsahuje pam� data?</span></p>
<p class="src1">{</p>
<p class="src2">return;</p>
<p class="src1">}</p>

<p>Podle logick� hodnoty bRender p�ip�n�me mezi vykreslov�n�m obd�ln�k� a linek.</p>

<p class="src1">if(bRender)<span class="kom">// Co chce u�ivatel renderovat?</span></p>
<p class="src1">{</p>
<p class="src2">glBegin(GL_QUADS);<span class="kom">// Polygony</span></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src1">{</p>
<p class="src2">glBegin(GL_LINES);<span class="kom">// Dr�t�n� model</span></p>
<p class="src1">}</p>

<p>Zalo��me dva vno�en� cykly, kter� proch�zej� jednotliv� pixely v��kov� mapy. Vn�j�� se star� o osu x a vnit�n� o osu y, z �eho� plyne, �e vykreslujeme po sloupc�ch a ne po ��dc�ch. V�imn�te si, �e po ka�d�m pr�chodu nezv�t�ujeme ��d�c� prom�nnou o jeden pixel, ale hned o n�kolik najednou. Sice v�sledn� ter�n nebude tak hladk� a p�esn�, ale d�ky men��mu po�tu polygon� se rendering urychl�. Pokud by se STEP_SIZE rovnalo jedn�, ka�d�mu pixelu by se p�i�adil jeden polygon. Mysl�m, �e ��slo �estn�ct bude vyhovuj�c�, ale pokud zapnete sv�tla, kter� zv�raz�uj� hranatost povrchu, m�li byste ho sn�it.</p>

<p>P�ekl.: �pln� nejlep�� by bylo, kdyby se velikost kroku ur�ovala p�ed vstupem do cykl� podle aktu�ln�ho FPS. N�sleduj�c� uk�zkov� k�d zav�d� zp�tnovazebn� regula�n� smy�ku.</p>

<p class="src1"><span class="kom">// P�ekl.: Regulace po�tu polygon�</span></p>
<p class="src1"><span class="kom">// if(FPS &lt; 30)// Ni��� hodnoty =&gt; viditeln� trh�n� pohyb� animace</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// if(STEP_SIZE &gt; 1)// Doln� mez (1 pixel)</span></p>
<p class="src2"><span class="kom">// {</span></p>
<p class="src3"><span class="kom">// STEP_SIZE--;// Mus� b�t prom�nnou a ne symbolickou konstantou</span></p>
<p class="src2"><span class="kom">// }</span></p>
<p class="src1"><span class="kom">// }</span></p>
<p class="src1"><span class="kom">// else</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// if(STEP_SIZE &lt; MAP_SIZE-1)// Horn� mez (velikost v��kov� mapy)</span></p>
<p class="src2"><span class="kom">// {</span></p>
<p class="src3"><span class="kom">// STEP_SIZE++;// Mus� b�t prom�nnou a ne symbolickou konstantou</span></p>
<p class="src2"><span class="kom">// }</span></p>
<p class="src1"><span class="kom">// }</span></p>
<p class="src"></p>
<p class="src1">for (X = 0; X &lt; MAP_SIZE; X += STEP_SIZE)<span class="kom">// ��dky v��kov� mapy</span></p>
<p class="src1">{</p>
<p class="src2">for (Y = 0; Y &lt; MAP_SIZE; Y += STEP_SIZE)<span class="kom">// Sloupce v��kov� mapy</span></p>
<p class="src2">{</p>

<p>P�epokl�d�m, �e to, jak ur�it pozici vertexu, jste u� d�vno vytu�ili. Hodnota na ose x odpov�d� x-ov� sou�adnici v��kov� mapy a na ose z y-ov�. Z�skali jsme um�st�n� bodu na rovin�, pot�ebujeme ho je�t� vyzdvihnout do v��ky, kter� v OpenGL odpov�d� osa y. Tato v��ka je definov�na hodnotou ulo�enou na dan�m prvku pole (sv�tlost� obr�zku). Opravdu nic slo�it�ho...</p>

<p class="src3"><span class="kom">// Sou�adnice lev�ho doln�ho vertexu</span></p>
<p class="src3">x = X;</p>
<p class="src3">y = Height(pHeightMap, X, Y );</p>
<p class="src3">z = Y;</p>

<p>Ur��me barvu bodu podle v��ky nad rovinou. ��m v��e se nach�z�, t�m bude sv�tlej��. Potom pomoc� funkce glVertex3i() p�ed�me OpenGL sou�adnice vertexu.</p>

<p class="src3">SetVertexColor(pHeightMap, x, z);<span class="kom">// Barva vertexu</span></p>
<p class="src3">glVertex3i(x, y, z);<span class="kom">// Definov�n� vertexu</span></p>

<p>Druh� vertex ur��me p�i�ten�m STEP_SIZE k ose z. Na tomto m�st� se budeme nach�zet p�i p��t�m pr�chodu cyklem, tak�e se mezi jednotliv�mi polygony nebudou vyskytovat mezery. Analogicky z�sk�me i dal�� dva body obd�ln�ku. Nyn� mi u� v���te, kdy� jsem na za��tku tutori�lu psal, �e slo�it� vypadaj�c� v�ci b�vaj� �asto velice jednoduch�?</p>

<p class="src3"><span class="kom">// Sou�adnice lev�ho horn�ho vertexu</span></p>
<p class="src3">x = X;</p>
<p class="src3">y = Height(pHeightMap, X, Y + STEP_SIZE );  </p>
<p class="src3">z = Y + STEP_SIZE ;</p>
<p class="src"></p>
<p class="src3">SetVertexColor(pHeightMap, x, z);<span class="kom">// Barva vertexu</span></p>
<p class="src3">glVertex3i(x, y, z);<span class="kom">// Definov�n� vertexu</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Sou�adnice prav�ho horn�ho vertexu</span></p>
<p class="src3">x = X + STEP_SIZE; </p>
<p class="src3">y = Height(pHeightMap, X + STEP_SIZE, Y + STEP_SIZE ); </p>
<p class="src3">z = Y + STEP_SIZE ;</p>
<p class="src"></p>
<p class="src3">SetVertexColor(pHeightMap, x, z);<span class="kom">// Barva vertexu</span></p>
<p class="src3">glVertex3i(x, y, z);<span class="kom">// Definov�n� vertexu</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Sou�adnice prav�ho doln�ho vertexu</span></p>
<p class="src3">x = X + STEP_SIZE; </p>
<p class="src3">y = Height(pHeightMap, X + STEP_SIZE, Y ); </p>
<p class="src3">z = Y;</p>
<p class="src"></p>
<p class="src3">SetVertexColor(pHeightMap, x, z);<span class="kom">// Barva vertexu</span></p>
<p class="src3">glVertex3i(x, y, z);<span class="kom">// Definov�n� vertexu</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Po vykreslen� ter�nu reinicializujeme barvu na b�lou, abychom nem�li starosti s barvou ostatn�ch objekt� ve sc�n� (net�k� se tohoto dema).</p>

<p class="src1">glColor4f(1.0f, 1.0f, 1.0f, 1.0f);<span class="kom">// Reset barvy</span></p>
<p class="src0">}</p>

<p>Na za��tku DrawGLScene() za�neme klasicky smaz�n�m buffer� a resetem matice.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslen� OpenGL sc�ny</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Pomoc� funkce gluLookAt() um�st�me a nato��me kameru tak, aby byl renderovan� ter�n v z�b�ru. Prvn� t�i parametry ur�uj� jej� pozici vzhledem k po��tku sou�adnicov�ho syst�mu, dal�� t�i body reprezentuj� m�sto, kam je nato�en� a posledn� t�i p�edstavuj� vektor vzh�ru. V na�em p��pad� se nach�z�me nad sledovan�m ter�nem a d�v�me se na n�j trochu dol� (55 je men�� ne� 60) sp�e doleva (186 je men�� ne� 212). Hodnota 171 p�edstavuje vzd�lenost od kamery na ose z. Proto�e se hory zvedaj� od zdola nahoru, nastav�me u vektoru vzh�ru jedni�ku na ose y. Ostatn� dv� hodnoty z�stanou na nule.</p>

<p>P�i prvn�m pou�it� m��e b�t gluLookAt() trochu odstra�uj�c�, asi jste zmateni. Nejlep�� radou je pohr�t si se v�emi hodnotami, abyste vid�li, jak se pohled na sc�nu postupn� m�n�. Pokud byste nap��klad p�epsal pozici z 60 na 120, vid�li byste ter�n sp�e seshora ne� z boku, proto�e se st�le d�v�te na sou�adnice 55.</p>

<p>Praktick� p��klad: �ekn�me, �e jste vysok� kolem 1,8 m. O�i, kter� reprezentuj� kameru, jsou trochu n�e - 1,7 m. Stoj�te p�ed st�nou, kter� je vysok� pouze 1 m, tak�e bez probl�m� vid�te jej� horn� stranu. Pokud ale zedn�ci dostav� st�nu do v��ky t�� metr�, budete se muset d�vat VZH�RU, ale jej� vrch u� NEUVID�TE. V�hled se zm�nil podle toho, jestli se d�v�te dol� nebo vzh�ru (respektive jestli jste nad nebo pod objektem).</p>

<p class="src1"><span class="kom">// Um�st�n� a nato�en� kamery</span></p>
<p class="src1">gluLookAt(212,60,194, 186,55,171, 0,1,0);<span class="kom">// Pozice, sm�r, vektor vzh�ru</span></p>

<p>Aby byl v�sledn� ter�n pon�kud men��, zm�n�me m���tko sou�adnicov�ch os. Proto�e nav�c n�sob�me y-ovou hodnotu, budou se hory jevit vy���. Mohli bychom tak� pou��t translace a rotace, ale to u� nech�m na v�s.</p>

<p class="src1">glScalef(scaleValue, scaleValue * HEIGHT_RATIO, scaleValue);<span class="kom">// Zoom ter�nu</span></p>

<p>Pomoc� d��ve napsan� funkce vyrenderujeme ter�n.</p>

<p class="src1">RenderHeightMap(g_HeightMap);<span class="kom">// Renderov�n� ter�nu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�e v po��dku</span></p>
<p class="src0">}</p>

<p>Kliknut�m lev�ho tla��tka my�i m��e u�ivatel p�epnout mezi renderov�n�m polygon� a linek (dr�t�n� model).</p>

<p class="src0"><span class="kom">// Funkce WndProc()</span></p>
<p class="src2">case WM_LBUTTONDOWN:<span class="kom">// Lev� tla��tko my�i</span></p>
<p class="src2">{</p>
<p class="src3">bRender = !bRender;<span class="kom">// P�epne mezi polygony a dr�t�n�m modelem</span></p>
<p class="src3">return 0;<span class="kom">// Konec funkce</span></p>
<p class="src2">}</p>

<p>�ipkami nahoru a dol� zv�t�ujeme/zmen�ujeme m���tko sc�ny a t�m i velikost ter�nu.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src3">if (keys[VK_UP])<span class="kom">// �ipka nahoru</span></p>
<p class="src3">{</p>
<p class="src4">scaleValue += 0.001f;<span class="kom">// Vyv��� hory</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_DOWN])<span class="kom">// �ipka dol�</span></p>
<p class="src3">{</p>
<p class="src4">scaleValue -= 0.001f;<span class="kom">// Sn�� hory</span></p>
<p class="src3">}</p>

<p>Tak to je v�echno, v��kov�m mapov�n�m textur jsme naprogramovali n�dherou krajinu, kter� je ale zabarven� do modra. Zkuste si nakreslit texturu (leteck� pohled), kter� reprezentuje zasn�en� vrcholy hor, louky, jezera a podobn� a namapujte ji na ter�n. Texturovac� koordin�ty z�sk�te vyd�len�m pozice na rovin� rozm�rem obr�zku (zmen�en� hodnot do rozsahu 0.0f a� 1.0f). Plazmov�mi efekty a rolov�n�m se m��e krajina dynamicky m�nit. D鹻 a sn�h zajist� ��sticov� syst�my, kter� u� tak� zn�te. Vlo��te-li krajinu do skyboxu, nikdo nepozn�, �e se jedn� o po��ta�ov� model a ne o video animaci.</p>

<p>Nebo m��ete vytvo�it mo�skou hladinu s vlnami, na kter�ch se pohupuje uplavan� m�� (v��ku nad mo�sk�m dnem p�ece zn�te - hodnota na indexu v poli). Nechte u�ivatele, a� ho m��e ovl�dat. Mo�nosti jsou bez hranic...</p>

<p class="autor">napsal: Ben Humphrey - DigiBen<br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson34.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson34_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson34.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson34.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde (aka Marilyn)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson34.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson34.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson34.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson34.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Schubert_P@Yahoo.de">Patrick Schubert</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson34.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson34.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(34);?>
<?FceNeHeOkolniLekce(34);?>

<?
include 'p_end.php';
?>
