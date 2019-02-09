<?
$g_title = 'CZ NeHe OpenGL - Lekce 47 - CG vertex shader';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(47);?>

<h1>Lekce 47 - CG vertex shader</h1>

<p class="nadpis_clanku">Pou��v�n� vertex a fragment (pixel) shader� ke &quot;�pinav� pr�ci&quot; p�i renderingu m��e m�t nespo�et v�hod. Nejv�ce je vid�t nap�. pohyb objekt� do te� v�hradn� z�visl� na CPU, kter� neb�� na CPU, ale na GPU. Pro psan� velice kvalitn�ch shader� poskytuje CG (p�im��en�) snadn� rozhran�. Tento tutori�l v�m uk�e jednoduch� vertex shader, kter� sice n�co d�l�, ale nebude p�edv�d�t ne nezbytn� osv�tlen� a podobn� slo�it�j�� nadstavby. Tak jako tak je p�edev��m ur�en pro za��te�n�ky, kte�� u� maj� n�jak� zku�enosti s OpenGL a zaj�maj� se o CG.</p>

<p>Hned na za��tku uvedu dv� internetov� adresy, kter� by se v�m mohli hodit. Jedn� se o <?OdkazBlank('http://developer.nvidia.com/');?> a <?OdkazBlank('http://www.cgshaders.org/');?>.</p>

<p>P�ekl.: Perfektn� �l�nek o vertex a pixel shaderech vy�el v �asopise CHIP 01/2004: Hardwarov� Fotorealismus - Mo�nosti modern�ch 3D grafick�ch akceler�tor� (str. 96 - 100).</p>

<p>Pozn�mka: ��elem tohoto tutori�lu nen� nau�it �pln� v�echno o psan� vertex shader� pou��vaj�c�ch CG. M� v �myslu vysv�tlit, jak �sp�n� nahr�t a spustit vertex shader v OpenGL.</p>

<h3>Nastaven�</h3>

<p>Prvn� krok spo��v� v downloadu CG kompil�toru od nVidie. Proto�e existuj� rozd�ly mezi verzemi 1.0 a 1.1, dbejte na to, abyste si st�hli ten nov�j��. K�d p�elo�en� pro jeden nemus� pracovat i s druh�m. Rozd�ly jsou nap�. v rozd�ln� pojmenovan�ch prom�nn�ch, nahrazen�ch funkc�ch a podobn�.</p>

<p>D�le mus�me nahr�t hlavi�kov� a knihovn� soubory CG na m�sto, kde je m��e Visual Studio naj�t. Proto�e ze z�sady ned�v��uji instal�tor�m, kter� pov�t�inou pracuj� jinak, ne� se o�ek�v�, osobn� d�v�m p�ednost ru�n�mu kop�rov�n� knihovn�ch soubor�</p>

<p class="src0">z: C:\Program Files\NVIDIA Corporation\Cg\lib</p>
<p class="src0">do: C:\Program Files\Microsoft Visual Studio\VC98\Lib</p>

<p>a hlavi�kov�ch soubor�</p>

<p class="src0">z: C:\Program Files\NVIDIA Corporation\Cg\include</p>
<p class="src0">do: C:\Program Files\Microsoft Visual Studio\VC98\Include</p>

<h3>CG Tutori�l</h3>

<p>Informace o CG uveden� v tomto tutori�lu byly v�t�inou z�sk�ny z CG u�ivatelsk�ho manu�lu (CG Toolkit User's Manual).</p>

<p>Existuje n�kolik podstatn�ch bod�, kter� byste si m�li prov�dy zapamatovat. Prvn� a nejd�le�it�j�� je, �e se vertex program provede na KA�D�M vertexu, kter� p�ed�te grafick� kart�. Jedin� mo�nost, jak ho spustit nad n�kolika zvolen�mi vertexy je bu� ho nahr�vat/mazat individu�ln� pro ka�d� vertex nebo pos�lat vertexy do proudu, ve kter�m budou ovlivn�ny a do proudu, kde nebudou. V�stup vertex programu je p�ed�n fragment (pixel) shaderu. To plat� pouze tehdy, pokud je implementov�n a zapnut. Za posledn� si zapamatujte, �e se vertex program provede nad vertexy p�edt�m, ne� se vytvo�� primitiva. Fragment shader je na rozd�l od toho vykon�n a� po rasterizaci.</p>

<p>Poj�me se kone�n� pod�vat na tutori�l. Vytvo��me pr�zdn� textov� soubor a pojmenujeme ho wave.cg. Do n�j budeme ps�t ve�ker� CG k�d. Nejd��ve vytvo��me datov� struktury, kter� budou obsahovat v�echny prom�nn� a informace pot�ebn� pro shader.</p>

<p>Ka�d� ze v�ech t�� prom�nn�ch struktury (pozice, barva a hodnota vlny) je n�sledov�na p�eddefinovan�m jm�nem (POSITION, COLOR0, COLOR1). Tato p�eddefinovan� jm�na se vztahuj� k s�mantice jazyka. Specifikuj� mapov�n� vstup� do p�esn� ur�en�ch hardwarov�ch registr�. Mimochodem, jedinou opravdu po�adovanou vstupn� prom�nnou do vertex programu je position.</p>

<p class="src0">struct appdata</p>
<p class="src0">{</p>
<p class="src1">float4 position : POSITION;</p>
<p class="src1">float4 color: COLOR0;</p>
<p class="src1">float3 wave: COLOR1;</p>
<p class="src0">};</p>

<p>D�le vytvo��me strukturu vfconn. Ta bude obsahovat v�stup vertex programu, kter� se po rasterizace p�ed� fragment shaderu. Stejn� jako vstupy maj� i v�stupy p�eddefinovan� jm�na. HPos reprezentuje pozici transformovanou do homogenn�ho sou�adnicov�ho syst�mu a Col0 ur�uje barvu vertexu zm�n�nou v programu.</p>

<p class="src0">struct vfconn</p>
<p class="src0">{</p>
<p class="src1">float4 HPos: POSITION;</p>
<p class="src1">float4 Col0: COLOR0;</p>
<p class="src0">};</p>

<p>Zb�v� n�m pouze napsat vertex program. Funkce se definuje stejn� jako v jazyce C. M� n�vratov� typ (struktura vfconn), jm�no (main, ale m��e j�m b�t i jak�koli jin�) a parametry. V na�em p��klad� ze vstupu p�evezmeme strukturu appdata, kter� obsahuje pozici vertexu, jeho barvu a hodnotu v��ky pro vytvo�en� sinusov�ch vln. Dostaneme tak� uniformn� parametr, kter�m je aktu�ln� modelview matice. Pot�ebujeme ji pro transformaci pozice do homogenn�ho sou�adnicov�ho syst�mu.</p>

<p class="src0">vfconn main(appdata IN, uniform float4x4 ModelViewProj)</p>
<p class="src0">{</p>

<p>Do prom�nn� OUT ulo��me modifikovan� vstupn� parametry a na konci programu ji vr�t�me.</p>

<p class="src1">vfconn OUT;<span class="kom">// V�stup z vertex shaderu (pos�l� se na fragment shader, pokud je dostupn�)</span></p>

<p>Vypo��t�me pozici na ose y v z�vislosti na x a z pozici vertexu. X i z vyd�l�me p�ti (respektive �ty�mi), p�echody budou jemn�j��. Zm��te hodnoty na 1.0, abyste vid�li, co mysl�m. Prom�nn� IN.wave specifikovan� hlavn�m programem obsahuje st�le se zv�t�uj�c� hodnotu, kter� zp�sob�, �e se sinusov� vlna rozpohybuje p�es cel� mesh. Y pozici spo��t�me z pozice v meshi jako sinus hodnoty vlny plus aktu�ln� x nebo z pozice. Aby byla v�sledn� vlna vy���, vyn�sob�me je�t� v�sledek ��slem 2,5.</p>

<p class="src1"><span class="kom">// Zm�na y pozice v z�vislosti na sinusov� vln�</span></p>
<p class="src1">IN.position.y = (sin(IN.wave.x + (IN.position.x / 5.0)) + sin(IN.wave.x + (IN.position.z / 4.0))) * 2.5f;</p>

<p>Nastav�me v�stupn� prom�nn� na�eho vertex programu. Nejd��ve transformujeme novou pozici vertexu do homogenn�ho sou�adnicov�ho syst�mu a potom p�i�ad�me v�stupn� barv� hodnotu vstupn�. Pomoc� return p�ed�me v�e fragment shaderu (pokud je zapnut�).</p>


<p class="src1">OUT.HPos = mul(ModelViewProj, IN.position);<span class="kom">// Transformace pozice na homogenn� sou�adnice</span></p>
<p class="src1">OUT.Col0.xyz = IN.color.xyz;<span class="kom">// Nastaven� barvy</span></p>
<p class="src"></p>
<p class="src1">return OUT;</p>
<p class="src0">}</p>

<h3>OpenGL Tutori�l</h3>

<p>V tuto chv�li m�me vertex program b��c� na grafick� kart� hotov. M��eme se pustit do hlavn�ho programu. Vytvo��me v n�m rovinn� mesh poskl�dan� z troj�heln�k� (triangle strip�), kter� budeme pos�lat na grafickou kartu. Na n� se ovlivn� y pozice ka�d�ho vertexu tak, aby ve v�sledku vznikly pohybuj�c� se sinusov� vlny.</p>

<p>V prvn� �ad� inkludujeme hlavi�kov� soubory, kter� v OpenGL umo�n� spustit CG shader. Mus�me tak� ��ct Visual Studiu, aby p�ilinkovalo pot�ebn� knihovn� soubory.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Windows</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// OpenGL</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// GLU</span></p>
<p class="src"></p>
<p class="src0">#include &lt;cg\cg.h&gt;<span class="kom">// CG hlavi�ky</span></p>
<p class="src0">#include &lt;cg\cggl.h&gt;<span class="kom">// CG hlavi�ky specifick� pro OpenGL</span></p>
<p class="src"></p>
<p class="src0">#include &quot;NeHeGL.h&quot;<span class="kom">// NeHe OpenGL</span></p>
<p class="src"></p>
<p class="src0">#pragma comment(lib, &quot;opengl32.lib&quot;)<span class="kom">// P�ilinkov�n� OpenGL</span></p>
<p class="src0">#pragma comment(lib, &quot;glu32.lib&quot;)<span class="kom">// P�ilinkov�n� GLU</span></p>
<p class="src"></p>
<p class="src0">#pragma comment(lib, &quot;cg.lib&quot;)<span class="kom">// P�ilinkov�n� CG</span></p>
<p class="src0">#pragma comment(lib, &quot;cggl.lib&quot;)<span class="kom">// P�ilinkov�n� OpenGL CG</span></p>
<p class="src"></p>
<p class="src0">#define TWO_PI 6.2831853071<span class="kom">// PI * 2</span></p>
<p class="src"></p>
<p class="src0">GL_Window* g_window;<span class="kom">// Struktura okna</span></p>
<p class="src0">Keys* g_keys;<span class="kom">// Kl�vesnice</span></p>

<p>Symbolick� konstanta SIZE ur�uje velikost meshe na os�ch x a z. D�le vytvo��me prom�nnou cg_enable, kter� bude oznamovat, jestli m� b�t vertex program zapnut� nebo vypnut�. Pole mesh slou�� pro ulo�en� dat meshe a wave_movement pro vytvo�en� sinusov� vlny.</p>

<p class="src0">#define SIZE 64<span class="kom">// Velikost meshe</span></p>
<p class="src"></p>
<p class="src0">bool cg_enable = TRUE, sp;<span class="kom">// Flag spu�t�n� CG</span></p>
<p class="src0">GLfloat mesh[SIZE][SIZE][3];<span class="kom">// Data meshe</span></p>
<p class="src0">GLfloat wave_movement = 0.0f;<span class="kom">// Pro vytvo�en� sinusov� vlny</span></p>

<p>N�sleduj� prom�nn� pro CG. CGcontext slou�� jako kontejner pro n�kolik CG program�. Obecn� sta�� pouze jeden CGcontext bez ohledu na po�et vertex a fragment program�, kter� vyu��v�me. Z jednoho kontextu m��ete pomoc� funkc� cgGetFirstProgram() a cgGetNextProgram() zvolit libovoln� program. CG profile definuje profil vertex�. CG parametry zprost�edkov�vaj� vazbu mezi hlavn�m programem a CG programem b��c�m na grafick� kart�. Ka�d� CG parameter je handle na koresponduj�c� prom�nnou v shaderu.</p>

<p class="src0">CGcontext cgContext;<span class="kom">// CG kontext</span></p>
<p class="src0">CGprogram cgProgram;<span class="kom">// CG vertex program</span></p>
<p class="src0">CGprofile cgVertexProfile;<span class="kom">// CG profil</span></p>
<p class="src0">CGparameter position, color, modelViewMatrix, wave;<span class="kom">// Parametry pro shader</span></p>

<p>Deklaraci glob�ln�ch prom�nn�ch m�me za sebou, poj�me se pod�vat na inicializa�n� funkci. Po obvykl�ch nastaven�ch zapneme vykreslov�n� dr�t�n�ch model�. Pou��v�me je z d�vodu, �e vypln�n� polygony nevypadaj� bez sv�tel dob�e. Pomoc� dvou vno�en�ch cykl� inicializujeme pole mesh tak, aby se st�ed roviny nach�zel v po��tku sou�adnicov�ho syst�mu. Pozici na ose y nastav�me u v�ech bod� na 0.0f, sinusovou deformaci m� na starosti CG program.</p>

<p class="src0">BOOL Initialize(GL_Window* window, Keys* keys)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">g_window = window;<span class="kom">// Okno</span></p>
<p class="src1">g_keys = keys;<span class="kom">// Kl�vesnice</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Maz�n� hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testov�n� hloubky</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� testov�n� hloubky</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nastaven� perspektivy</span></p>
<p class="src"></p>
<p class="src1">glPolygonMode(GL_FRONT_AND_BACK, GL_LINE);<span class="kom">// Dr�t�n� model</span></p>
<p class="src"></p>
<p class="src1">for (int x = 0; x &lt; SIZE; x++)<span class="kom">// Inicializace meshe</span></p>
<p class="src1">{</p>
<p class="src2">for (int z = 0; z &lt; SIZE; z++)</p>
<p class="src2">{</p>
<p class="src3">mesh[x][z][0] = (float) (SIZE / 2) - x;<span class="kom">// Vycentrov�n� na ose x</span></p>
<p class="src3">mesh[x][z][1] = 0.0f;<span class="kom">// Ploch� rovina</span></p>
<p class="src3">mesh[x][z][2] = (float) (SIZE / 2) - z;<span class="kom">// Vycentrov�n� na ose z</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Mus�me tak� inicializovat CG, jako prvn� vytvo��me kontext. Pokud funkce vr�t� NULL, n�co selhalo, chyby v�t�inou nast�vaj� kv�li nepoveden� alokaci pam�ti. Zobraz�me chybovou zpr�vu a vr�t�me false, ��m� ukon��me i cel� program.</p>

<p class="src1">cgContext = cgCreateContext();<span class="kom">// Vytvo�en� CG kontextu</span></p>
<p class="src"></p>
<p class="src1">if (cgContext == NULL)<span class="kom">// OK?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Failed To Create Cg Context&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>

<p>Pomoc� cgGLGetLatestProfile() ur��me minul� profil vertex�, za typ profilu p�ed�me CG_GL_VERTEX. Kdybychom vytv��eli fragment shader, p�ed�vali bychom CG_GL_FRAGMENT. Pokud nen� ��dn� vhodn� profil dostupn�, vr�t� funkce CG_PROFILE_UNKNOWN. S validn�m profilem m��eme zavolat cgGLSetOptimalOptions(). Tato funkce se pou��v� poka�d�, kdy� se p�ekl�d� nov� CG program, proto�e podstatn� optimalizuje kompilaci shaderu v z�vislosti na aktu�ln�m grafick�m hardwaru a jeho ovlada��ch.</p>

<p class="src1">cgVertexProfile = cgGLGetLatestProfile(CG_GL_VERTEX);<span class="kom">// Z�sk�n� minul�ho profilu vertex�</span></p>
<p class="src"></p>
<p class="src1">if (cgVertexProfile == CG_PROFILE_UNKNOWN)<span class="kom">// OK?</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Invalid profile type&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">cgGLSetOptimalOptions(cgVertexProfile);<span class="kom">// Nastaven� profilu</span></p>

<p>Zavol�me funkci cgCreateprogramFromFile(), ��m� na�teme a zkompilujeme CG program. Prvn� parametr specifikuje CG kontext, ke kter�mu bude program p�ipojen. Druh� parametr ur�uje, �e soubor obsahuje zdrojov� k�d (CG_SOURCE) a ne objektov� k�d p�edkompilovan�ho programu (CG_OBJECT). Jako t�et� polo�ka se p�ed�v� cesta k souboru, �tvrt� je minul�m profilem pro konkr�tn� typ programu (vertex profil pro vertex program, fragment profil pro fragment program). P�t� parametr specifikuje vstupn� funkci do programu, jej� jm�no m��e b�t libovoln�, ne pouze main(). Posledn� parametr slou�� pro p�ed�n� p��davn�ch argument� kompil�toru. V�t�inou se d�v� NULL.</p>

<p>Pokud z n�jak�ho d�vodu funkce sel�e, z�sk�me pomoc� cgGetError() typ chyby. Do �et�zcov� podoby ho m��eme p�ev�st prost�ednictv�m cgGetErrorString().</p>

<p class="src1"><span class="kom">// Nahraje a zkompiluje vertex shader</span></p>
<p class="src1">cgProgram = cgCreateProgramFromFile(cgContext, CG_SOURCE, &quot;CG/Wave.cg&quot;, cgVertexProfile, &quot;main&quot;, 0);</p>
<p class="src"></p>
<p class="src1">if (cgProgram == NULL)<span class="kom">// OK?</span></p>
<p class="src1">{</p>
<p class="src2">CGerror Error = cgGetError();<span class="kom">// Typ chyby</span></p>
<p class="src"></p>
<p class="src2">MessageBox(NULL, cgGetErrorString(Error), &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>

<p>Nahrajeme zkompilovan� program a p�iprav�me ho pro zvolen� (binding).</p>

<p class="src1">cgGLLoadProgram(cgProgram);<span class="kom">// Nahraje program do grafick� karty</span></p>

<p>Jako posledn� krok inicializace z�sk�me handle na prom�nn�, se kter�mi bude CG program manipulovat. Pokud dan� prom�nn� neexistuje, cgGetNamedParameter() vr�t� NULL. Nezn�me-li jm�na parametr�, m��eme pou��t dvojici funkc� cgGetFirstParameter() a cgGetNextParameter().</p>

<p class="src1"><span class="kom">// Handle na prom�nn�</span></p>
<p class="src1">position = cgGetNamedParameter(cgProgram, &quot;IN.position&quot;);</p>
<p class="src1">color = cgGetNamedParameter(cgProgram, &quot;IN.color&quot;);</p>
<p class="src1">wave = cgGetNamedParameter(cgProgram, &quot;IN.wave&quot;);</p>
<p class="src1">modelViewMatrix = cgGetNamedParameter(cgProgram, &quot;ModelViewProj&quot;);</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Pomoc� deinicializa�n� funkce po sob� uklid�me. Jednodu�e zavol�me cgDestroyContext() pro ka�d� CGcontext prom�nnou. Tak� bychom mohli smazat jednotliv� CG programy, k tomu slou�� funkce cgDestroyProgram(), nicm�n� cgDestroyContext() je sma�e automaticky.</p>

<p class="src0">void Deinitialize(void)<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">cgDestroyContext(cgContext);<span class="kom">// Sma�e CG kontext</span></p>
<p class="src0">}</p>

<p>Do aktualiza�n� funkce p�id�me k�d pro o�et�en� stisku mezern�ku, kter� zap�n�/vyp�n� CG program b��c� na grafick� kart�.</p>

<p class="src0">void Update(float milliseconds)<span class="kom">// Aktualizace</span></p>
<p class="src0">{</p>
<p class="src1">if (g_keys-&gt;keyDown[VK_ESCAPE])<span class="kom">// Stisk Esc</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication(g_window);<span class="kom">// Konec programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_F1])<span class="kom">// Stisk F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen(g_window);<span class="kom">// P�epnut� do/z fullscreenu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[' '] &amp;&amp; !sp)<span class="kom">// Stisk mezern�ku</span></p>
<p class="src1">{</p>
<p class="src2">sp = TRUE;</p>
<p class="src2">cg_enable = !cg_enable;<span class="kom">// Zapne/vypne CG program</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown[' '])</p>
<p class="src1">{</p>
<p class="src2">sp = FALSE;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>A jako posledn� vykreslov�n�. Kamerou se p�esuneme o 45 jednotek p�ed po��tek sou�adnicov�ho syst�mu a nahoru o 25 jednotek.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">gluLookAt(0.0f, 25.0f, -45.0f, 0.0f, 0.0f, 0.0f, 0, 1, 0);<span class="kom">// Pozice kamery</span></p>

<p>Modelview matici vertex shaderu nastav�me na aktu�ln� OpenGL matici. Bez toho bychom nemohli p�epo��t�vat pozici vertex� do homogenn�ch sou�adnic.</p>

<p class="src1"><span class="kom">// Nastaven� modelview matice v shaderu</span></p>
<p class="src1">cgGLSetStateMatrixParameter(modelViewMatrix, CG_GL_MODELVIEW_PROJECTION_MATRIX, CG_GL_MATRIX_IDENTITY);</p>

<p>Pokud je flag cg_enable v true, vol�n�m cgGLEnableProfile() aktivujeme p�edan� profil. Funkce cgGLBindProgram() zvol� n� program a dokud ho nevypneme, provede se nad ka�d�m vertexem poslan�m na grafickou kartu. Tak� mus�me poslat barvu vertex�.</p>

<p class="src1">if (cg_enable)<span class="kom">// Zapnout CG shader?</span></p>
<p class="src1">{</p>
<p class="src2">cgGLEnableProfile(cgVertexProfile);<span class="kom">// Zapne profil</span></p>
<p class="src2">cgGLBindProgram(cgProgram);<span class="kom">// Zvol� program</span></p>
<p class="src2">cgGLSetParameter4f(color, 0.5f, 1.0f, 0.5f, 1.0f);<span class="kom">// Nastav� barvu (sv�tle zelen�)</span></p>
<p class="src1">}</p>

<p>Tak te� jsme kone�n� p�ipraveni na rendering meshe. Pro ka�dou hodnotu sou�adnice x v cyklu vykresl�me prou�ek roviny seskl�dan� triangle stripem.</p>

<p class="src1">for (int x = 0; x &lt; SIZE - 1; x++)<span class="kom">// Vykreslen� meshe</span></p>
<p class="src1">{</p>
<p class="src2">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Ka�d� prou�ek jedn�m triangle stripem</span></p>
<p class="src"></p>
<p class="src2">for (int z = 0; z &lt; SIZE - 1; z++)</p>
<p class="src2">{</p>

<p>Sou�asn� s renderovan�mi vertexy dynamicky p�ed�me i hodnotu wave parametru, d�ky kter�mu bude moci CG program z roviny vygenerovat sinusov� vlny. Jakmile grafick� karta dostane v�echna data, automaticky spust� CG program. V�imn�te si, �e do triangle stripu pos�l�me dva body, to m� za n�sledek, �e se nevykresl� pouze troj�heln�k, ale rovnou cel� �tverec.</p>

<p class="src3">cgGLSetParameter3f(wave, wave_movement, 1.0f, 1.0f);<span class="kom">// Parametr vlny</span></p>
<p class="src"></p>
<p class="src3">glVertex3f(mesh[x][z][0], mesh[x][z][1], mesh[x][z][2]);<span class="kom">// Vertex</span></p>
<p class="src3">glVertex3f(mesh[x+1][z][0], mesh[x+1][z][1], mesh[x+1][z][2]);<span class="kom">// Vertex</span></p>
<p class="src"></p>
<p class="src3">wave_movement += 0.00001f;<span class="kom">// Inkrementace parametru vlny</span></p>
<p class="src"></p>
<p class="src3">if (wave_movement &gt; TWO_PI)<span class="kom">// V�t�� ne� dv� p� (6,28)?</span></p>
<p class="src3">{</p>
<p class="src4">wave_movement = 0.0f;<span class="kom">// Vynulovat</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec triangle stripu</span></p>
<p class="src1">}</p>

<p>Po dokon�en� renderingu otestujeme, jestli je cg_enable rovno true a pokud ano, vypneme vertex profil. D�le m��eme kreslit cokoli chceme, ani� by to bylo ovlivn�no CG programem.</p>

<p class="src1">if (cg_enable)<span class="kom">// Zapnut� CG shader?</span></p>
<p class="src1">{</p>
<p class="src2">cgGLDisableProfile(cgVertexProfile);<span class="kom">// Vypne profil</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vypr�zdn�n� renderovac� pipeline</span></p>
<p class="src0">}</p>

<p class="autor">napsal: Owen Bourne <?VypisEmail('o.bourne@griffith.edu.au');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson47.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson47.tar.gz">Linux/GLut</a> k�d t�to lekce. ( <a href="mailto:foxdie@pobox.sk">Gray Fox</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson47.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:r3lik@shaw.ca">Jason Schultz</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson47.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
</ul>

<?FceImgNeHeVelky(47);?>
<?FceNeHeOkolniLekce(47);?>

<?
include 'p_end.php';
?>
