<?
$g_title = 'CZ NeHe OpenGL - Generov�n� planet';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Generov�n� planet</h1>

<p class="nadpis_clanku">Pokud budete n�kdy pot�ebovat pro svou aplikaci vygenerovat realisticky vypadaj�c� planetu, tento �l�nek se v�m bude ur�it� hodit - popisuje jeden ze zp�sob� vytv��en� nedeformovan�ch kontinent�. Obvykl� zp�soby pokr�v�n� koule rovinnou texturou kon�� obrovsk�mi deformacemi na p�lech. Dal�� nev�hodou n�kter�ch zp�sob� je, �e v�sledek je orientov�n v n�jak�m sm�ru. To u t�to metody nehroz�.</p>

<h3>Postup</h3>

<p>Princip je jednoduch�, hor�� ale bude implementace. Jak tedy postupovat?</p>

<ol>
<li>Vezmeme kouli</li>
<li>N�hodn� zvolenou rovinou proch�zej�c� p�es jej� st�ed, ji rozd�l�me na dv� poloviny</li>
<li>Jednu polovinu o trochu zv�t��me a druhou zmen��me</li>
<li>Opakujeme kroky 2 a 3</li>
</ol>

<div class="okolo_img"><img src="images/clanky/cl_gl_planety/land1.gif" width="160" height="158" alt="Postup" /></div>

<div class="okolo_img">
<div>Na n�sleduj�c�ch obr�zc�ch vid�te cel� postup v n�kolik prvn�ch kroc�ch.</div>
<img src="images/clanky/cl_gl_planety/land2.gif" width="111" height="111" alt="P�ed rozd�len�m" />
<img src="images/clanky/cl_gl_planety/land3.gif" width="111" height="111" alt="Po prvn�m rozd�len�" />
<img src="images/clanky/cl_gl_planety/land4.gif" width="111" height="111" alt="Po druh�m rozd�len�" />
<img src="images/clanky/cl_gl_planety/land5.gif" width="111" height="111" alt="Po t�et�m rozd�len�" />
</div>

<p>Po velk�m po�tu d�len� se za�nou mal� h�ebeny formovat do tvaru kontinent�, tak�e nechte algoritmus prob�hat tak dlouho, dokud nemaj� po�adovan� tvar.</p>

<div class="okolo_img">
<div><b>100 iterac�</b> - P�kn� tvarovan� kontinenty se objev� u� po sto iterac�ch.</div>
<img src="images/clanky/cl_gl_planety/land6.gif" width="222" height="222" alt="�eln� pohled" />
<img src="images/clanky/cl_gl_planety/land7.gif" width="222" height="222" alt="Pohled zezadu" />
</div>

<div class="okolo_img">
<div><b>1000 iterac�</b> - Objevuje se prvn� zn�mka hor a z�rove� se za��naj� objevovat i ostrovy.</div>
<img src="images/clanky/cl_gl_planety/land8.gif" width="222" height="222" alt="�eln� pohled" />
<img src="images/clanky/cl_gl_planety/land9.gif" width="222" height="222" alt="Pohled zezadu" />
</div>

<div class="okolo_img">
<div><b>10000 iterac�</b> - Te� se ji� objevuj� velk� hory. Pob�e�� je komplexn� a objevuj� se ostrovy a jezera.</div>
<img src="images/clanky/cl_gl_planety/land10.gif" width="222" height="222" alt="�eln� pohled" />
<img src="images/clanky/cl_gl_planety/land11.gif" width="222" height="222" alt="Pohled zezadu" />
</div>


<h3>Nedostatky</h3>

<p>Jist� jste si v�imli n��eho podivn�ho. Pohled zezadu vypad� skoro stejn� jako �eln� strana vzh�ru nohama a s prohozen�mi kontinenty za oce�ny. To je asi nejv�t�� nev�hoda t�to metody. Na m�st�, kde se na jedn� stran� planety nach�zej� mo�e, je na druh� stran� kontinent, nicm�n� si toho �asto ani nev�imnete.</p>

<h3>Implementace</h3>

<p>Nejd��ve si mus�me nadefinovat &quot;kouli&quot; s mo�nost� prom�nn�ho polom�ru. Ud�l�me to pomoc� dvourozm�rn�ho pole m_r - viz n�sleduj�c� v�pis hlavi�kov�ho souboru. M��eme si to dovolit, proto�e ka�d� bod v prostoru je d�n dv�ma �hly a vzd�lenost� od st�edu. Prvn� index pole ud�v� �hel alfa, druh� index p�edstavuje �hel beta a hodnota ulo�en� v poli vyjad�uje vzd�lenost od st�edu. �hel alfa ud�v� odklon spojnice bodu a po��tkem s osou x v rovin� x, z. �hel beta ud�v� �hel mezi spojnic� bodu a po��tkem s osou y. P�edpokl�d�me klasickou orientaci sou�adnic v OpenGL, tj. x doprava, y nahoru a z ven z obrazovky (k u�ivateli). Pro p�epo�et index� pole na �hel pou��v�me n�sleduj�c� makro:</p>

<p class="src0"><span class="key">#define</span> UHEL(x) (2 * PI * (<span class="key">double</span>(x) / <span class="key">double</span>(SLICES)))</p>

<p>...kde x je index pole, PI je hodnota 3.14159... a SLICES ud�v� v kolika bodech na obvodu je koule definovan�, tj. po�et poledn�k�. Po�et rovnob�ek je polovi�n�, aby byl zjednodu�en� k�d pro vykreslov�n� a v�po�et �hlu z indexu. Pro v�po�et sou�adnic z �hl� a polom�ru se pou��vaj� n�sleduj�c� vzorce:</p>

<p class="src0"><span class="kom">x = r * cos(alfa) * sin(beta)</span></p>
<p class="src0"><span class="kom">y = r * cos(beta)</span></p>
<p class="src0"><span class="kom">z = r * sin(alfa) * sin(beta)</span></p>

<p>A zde je slibovan� v�pis hlavi�kov�ho souboru.</p>

<p class="src0"><span class="kom">// na kolik ��st� je planeta rozd�lena</span></p>
<p class="src0"><span class="kom">// po�et poledn�k� je roven SLICES</span></p>
<p class="src0"><span class="kom">// po�et rovnob�ek je roven SLICES / 2</span></p>
<p class="src0"><span class="kom">// ��m je hodnota v�t��, t�m d�le trv� v�po�et a vykreslov�n�, ale z�rove� se zlep�uje vzhled</span></p>
<p class="src0"><span class="key">#define</span> SLICES 500</p>
<p class="src"></p>
<p class="src0"><span class="kom">// polom�r hladiny oce�n� - nastavuje se pomoc� funkce Reset </span></p>
<p class="src0"><span class="kom">// (je vol�na automaticky v konstruktoru, ale m��ete ji volat i sami)</span></p>
<p class="src0"><span class="key">#define</span> R m_default_r</p>
<p class="src"></p>
<p class="src0"><span class="key">#define</span> PI 3.1415926535897932384626433832795</p>
<p class="src"></p>
<p class="src0"><span class="kom">// p�epo�et indexu pole na �hel</span></p>
<p class="src0"><span class="key">#define</span> UHEL(x) (2 * PI * (<span class="key">double</span>(x) / <span class="key">double</span>(SLICES)))</p>
<p class="src"></p>
<p class="src0"><span class="key">class</span> CPlanet</p>
<p class="src0">{</p>
<p class="src0"><span class="key">public</span>:</p>
<p class="src1"><span class="kom">// funkce pro generov�n� kontinent�</span></p>
<p class="src1"><span class="kom">// nsteps ud�v� kolik krok� algoritmu pro generov�n� prov�st</span></p>
<p class="src1"><span class="kom">// p�kn� v�sledky lze dostat asi od 250 krok�</span></p>
<p class="src1"><span class="key">void</span> GenerujKontinenty(<span class="key">const</span> <span class="key">int</span> nsteps);</p>
<p class="src1"><span class="kom">// resetuje do v�choz�ho stavu a nastav� polom�r planety</span></p>
<p class="src1"><span class="key">void</span> Reset(<span class="key">const</span> <span class="key">double</span> r);</p>
<p class="src1"><span class="kom">// vykresl� planetu (pomoc� OpenGL)</span></p>
<p class="src1"><span class="key">void</span> Draw();</p>
<p class="src1">CPlanet();</p>
<p class="src1"><span class="key">virtual</span> ~CPlanet();</p>
<p class="src"></p>
<p class="src0"><span class="key">protected</span>:</p>
<p class="src1"><span class="kom">// pomocn� prom�nn� do kter� se ukl�d� v��ka nejvy���ho vrcholu kv�li volb� barvy p�i vykreslov�n�</span></p>
<p class="src1"><span class="key">double</span> m_max_r;</p>
<p class="src1"><span class="kom">// pole pro ulo�en� v��ky povrchu</span></p>
<p class="src1"><span class="key">double</span> m_r[SLICES][SLICES/2];</p>
<p class="src1"><span class="kom">// v�choz� polom�r planety a z�rove� v��ka hladiny mo��</span></p>
<p class="src1"><span class="key">double</span> m_default_r;</p>
<p class="src0">};</p>

<p>Funkce kter� implementuje generov�n� kontinent� vypad� takto:</p>

<p class="src0"><span class="key">void</span> CPlanet::GenerujKontinenty(<span class="key">const</span> <span class="key">int</span> nsteps)</p>
<p class="src0">{</p>
<p class="src1">m_max_r = R;</p>
<p class="src1"><span class="key">int</span> i,j,k;</p>
<p class="src1"><span class="key">double</span> nx,ny,nz,x,y,z,ns;</p>
<p class="src"></p>
<p class="src1"><span class="key">for</span> (k=0; k&lt;nsteps; k++)<span class="kom">// opakovat po zadan� po�et krok�</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// n�hodn� vygenerovat norm�lov� vektor plochy</span></p>
<p class="src2">nx = (<span class="key">double</span>(rand())/<span class="key">double</span>(RAND_MAX))-0.5;</p>
<p class="src2">ny = (<span class="key">double</span>(rand())/<span class="key">double</span>(RAND_MAX))-0.5;</p>
<p class="src2">nz = (<span class="key">double</span>(rand())/<span class="key">double</span>(RAND_MAX))-0.5;</p>
<p class="src"></p>
<p class="src2"><span class="kom">// pro v�echny vrcholy</span></p>
<p class="src2"><span class="key">for</span>(i=0; i&lt;SLICES; i++)</p>
<p class="src3"><span class="key">for</span>(j=0; j&lt;SLICES/2; j++)</p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// vektor (jednotkov�) ze st�edu koule k vrcholu</span></p>
<p class="src4">x = cos(UHEL(i))*sin(UHEL(j));</p>
<p class="src4">y = cos(UHEL(j));</p>
<p class="src4">z = sin(UHEL(i))*sin(UHEL(j));</p>
<p class="src"></p>
<p class="src4"><span class="kom">// skal�rn� sou�in norm�lov�ho vektoru a vektoru ze st�edu koule k vrcholu</span></p>
<p class="src4"><span class="kom">// pokud je �hel mezi vektory men�� nebo roven 90 stup�� je kladn�, jinak z�porn�</span></p>
<p class="src4">ns = nx*x + ny*y + nz*z;</p>
<p class="src"></p>
<p class="src4"><span class="key">if</span> (ns&gt;=0)<span class="kom">// �hel mezi vektory men�� nebo roven 90 stup��</span></p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// zv��it vrchol</span></p>
<p class="src5">m_r[i][j] += 1e-3*R;</p>
<p class="src4">}</p>
<p class="src4"><span class="key">else</span><span class="kom">// �hel mezi vektory v�t�� ne� 90 stup��</span></p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// sn�it vrchol</span></p>
<p class="src5">m_r[i][j] -= 1e-3*R;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4"><span class="kom">// pomocn� krok kv�li volb� barvy vrcholu</span></p>
<p class="src4"><span class="kom">// pokud je v��ka vrcholu v�t�� ne� maxim�ln� potom maxim�ln� nastavit na v��ku vrcholu</span></p>
<p class="src4"><span class="key">if</span> (m_max_r&lt;m_r[i][j]) m_max_r=m_r[i][j];</p>
<p class="src3">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Te� je je�t� t�eba na z�klad� takto spo��tan�ch hodnot planetu vykreslit.</p>

<p class="src0"><span class="key">void</span> CPlanet::Draw()</p>
<p class="src0">{</p>
<p class="src1">register <span class="key">int</span> i,j;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// vykreslen� koule s r�zn�mi polom�ry jednotliv�ch bod�</span></p>
<p class="src1">glBegin(GL_TRIANGLE_STRIP);</p>
<p class="src2"><span class="key">if</span> (m_r[0][0] &lt;= R)<span class="kom">// pod hladinou mo�e</span></p>
<p class="src2">{</p>
<p class="src3">glColor3d(0, 0, 0.9);<span class="kom">// modr� barva</span></p>
<p class="src3"><span class="kom">// vykreslen� vrcholu koule</span></p>
<p class="src3">glVertex3d(R * cos(UHEL(0)) * sin(UHEL(0)), R * cos(UHEL(0)), R * sin(UHEL(0)) * sin(UHEL(0)));</p>
<p class="src2">}</p>
<p class="src2"><span class="key">else</span><span class="kom">// nad hladinou mo�e</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// barva mezi zelenou a hn�dou (podle nadmo�sk� v��ky)</span></p>
<p class="src3">glColor3d(0.455 * (m_r[0][0]-R) / (m_max_r-R), 0.39 * (m_r[0][0]-R) / (m_max_r-R) + (1.0 - (m_r[0][0] - R) / (m_max_r-R)), 0.196 * ((m_r[0][0] - R) / (m_max_r - R)));</p>
<p class="src"></p>
<p class="src3"><span class="kom">// vykreslen� vrcholu koule</span></p>
<p class="src3">glVertex3d(m_r[0][0] * cos(UHEL(0)) * sin(UHEL(0)), m_r[0][0] * cos(UHEL(0)), m_r[0][0] * sin(UHEL(0)) * sin(UHEL(0)));</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="key">for</span>(i=0; i&lt;SLICES; i++)</p>
<p class="src3"><span class="key">for</span>(j=0; j&lt;SLICES/2; j++)</p>
<p class="src3">{</p>
<p class="src4"><span class="key">if</span> (m_r[i][(j + 1) % (SLICES / 2)] &lt;= R)<span class="kom">// pod hladinou mo�e</span></p>
<p class="src4">{</p>
<p class="src5">glColor3d(0, 0, 0.9);<span class="kom">// modr� barva</span></p>
<p class="src5"><span class="kom">// vykreslen� vrcholu koule</span></p>
<p class="src5">glVertex3d(R * cos(UHEL(i)) * sin(UHEL(j + 1)), R * cos(UHEL(j + 1)), R * sin(UHEL(i)) * sin(UHEL(j + 1)));</p>
<p class="src4">}</p>
<p class="src4"><span class="key">else</span></p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// barva mezi zelenou a hn�dou (podle nadmo�sk� v��ky)</span></p>
<p class="src5">glColor3d(0.455 * ((m_r[i][(j + 1) % (SLICES/2)] - R) / (m_max_r - R)), 0.39 * (m_r[i][(j + 1) % (SLICES / 2)] - R) / (m_max_r - R) + (1.0 - (m_r[i][(j + 1) % (SLICES / 2)] - R) / (m_max_r - R)), 0.196 * ((m_r[i][(j + 1) % (SLICES / 2)] - R) / (m_max_r - R)));</p>
<p class="src"></p>
<p class="src5"><span class="kom">// vykreslen� vrcholu koule</span></p>
<p class="src5">glVertex3d(m_r[i][(j + 1) % (SLICES / 2)] * cos(UHEL(i)) * sin(UHEL(j + 1)), m_r[i][(j + 1) % (SLICES / 2)] * cos(UHEL(j + 1)), m_r[i][(j + 1) % (SLICES / 2)] * sin(UHEL(i)) * sin(UHEL(j + 1)));</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4"><span class="key">if</span> (m_r[(i+1)%SLICES][j] &lt;= R)<span class="kom">// pod hladinou mo�e</span></p>
<p class="src4">{</p>
<p class="src5">glColor3d(0, 0, 0.9);<span class="kom">// modr� barva</span></p>
<p class="src5"><span class="kom">// vykreslen� vrcholu koule</span></p>
<p class="src5">glVertex3d(R * cos(UHEL(i + 1)) * sin(UHEL(j)), R * cos(UHEL(j)), R * sin(UHEL(i + 1)) * sin(UHEL(j)));</p>
<p class="src4">}</p>
<p class="src4"><span class="key">else</span></p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// barva mezi zelenou a hn�dou (podle nadmo�sk� v��ky)</span></p>
<p class="src5">glColor3d(0.455 * ((m_r[(i + 1) % SLICES][j] - R) / (m_max_r - R)), 0.39 * (m_r[(i + 1) % SLICES][j] - R) / (m_max_r - R) + (1.0 - (m_r[(i + 1) % SLICES][j] - R) / (m_max_r - R)), 0.196 * ((m_r[(i + 1) % SLICES][j] - R) / (m_max_r - R)));</p>
<p class="src"></p>
<p class="src5"><span class="kom">// vykreslen� vrcholu koule</span></p>
<p class="src5">glVertex3d(m_r[(i + 1) % SLICES][j] * cos(UHEL(i + 1)) * sin(UHEL(j)), m_r[(i + 1) % SLICES][j] * cos(UHEL(j)), m_r[(i + 1) % SLICES][j] * sin(UHEL(i + 1)) * sin(UHEL(j)));</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>Pro �plnost zde uvedu je�t� v�pis funkce Reset(), konstruktoru a destruktoru.</p>

<p class="src0"><span class="key">void</span> CPlanet::Reset(<span class="key">const</span> <span class="key">double</span> r)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// v�echny vrcholy se nastav� na v�choz� polom�r</span></p>
<p class="src1">m_default_r=r;</p>
<p class="src1">register <span class="key">int</span> i,j;</p>
<p class="src"></p>
<p class="src1"><span class="key">for</span>(i=0; i&lt;SLICES; i++)</p>
<p class="src2"><span class="key">for</span>(j=0; j&lt;SLICES/2; j++)</p>
<p class="src3">m_r[i][j]=R;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">CPlanet::CPlanet()</p>
<p class="src0">{</p>
<p class="src1">Reset(20);</p>
<p class="src1">srand( (<span class="key">unsigned</span>)time( NULL ) );</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">CPlanet::~CPlanet()</p>
<p class="src0">{</p>
<p class="src"></p>
<p class="src0">}</p>

<h3>N�prava nedostatku</h3>

<p>V��e uveden� nedostatek, tj. �e tam, kde se na jedn� stran� planety vyskytuje kontinent je na druh� stran� mo�e, lze obej�t jednodu�e tak, �e kouli nerozd�lujete st�edem, ale libovoln�m bodem, kter� do n� pat��. Pro definov�n� tohoto bodu se m��e pou��t norm�lov� vektor plochy, jen mus�me trochu upravit zp�sob jeho v�po�tu - nesta�� pouze jednotkov� norm�lov� vektor, ale mus� se volit (o n�hodn� velikosti) men�� ne� je polom�r koule.</p>

<p class="src0">nx = <span class="key">double</span>(rand() % <span class="key">int</span>(R)) * ((<span class="key">double</span>(rand()) / <span class="key">double</span>(RAND_MAX)) - 0.5);</p>
<p class="src0">ny = <span class="key">double</span>(rand() % <span class="key">int</span>(R)) * ((<span class="key">double</span>(rand()) / <span class="key">double</span>(RAND_MAX)) - 0.5);</p>
<p class="src0">nz = <span class="key">double</span>(rand() % <span class="key">int</span>(R)) * ((<span class="key">double</span>(rand()) / <span class="key">double</span>(RAND_MAX)) - 0.5);</p>

<p>Je�t� je nutn� modifikovat zp�sob v�po�tu prom�nn� <span class="src">ns</span>, tak aby se po��talo od bodu dan�ho norm�lov�m vektorem.</p>

<p class="src0">ns = nx * (x - nx) + ny * (y - ny) + nz * (z - nz);</p>

<p>T�m ale vznikne dal�� probl�m. Proto�e norm�lov� vektor ukazuje v�dy od st�edu k d�l�c� rovin�, v�t�� ��st koule se bude v�dy zmen�ovat. To lze ale odstranit n�sleduj�c� jednoduchou �pravou podm�nky rozhoduj�c�, zda zmen�ovat nebo zv�t�ovat.</p>

<p class="src0"><span class="key">if</span> (m * ns &gt;= 0)<span class="kom">// �hel mezi vektory men�� nebo roven 90 stup��</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// zv��it vrchol</span></p>
<p class="src1">m_r[i][j] += 1e-3 * R;</p>
<p class="src0">}</p>
<p class="src0"><span class="key">else</span><span class="kom">// �hel mezi vektory v�t�� ne� 90 stup��</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// sn�it vrchol</span></p>
<p class="src1">m_r[i][j] -= 1e-3 * R;</p>
<p class="src0">}</p>

<p>Jedin� rozd�l od p�edch�zej�c�ho k�du je v n�soben� prom�nnou m, kter� m� hodnotu bu� plus nebo m�nus jedna (n�hodn�). Tuto hodnotu vol�me v�dy pouze jednou pro ka�d� v�po�et norm�lov�ho vektoru. Nejlep�� je um�stit n�sleduj�c� ��dek hned za v�po�et prom�nn�ch nx, ny, nz.</p>

<p class="src0">m = ((rand() % 2) ? -1 : 1)</p>

<p>V�sledek upraven�ho algoritmu bude vypadat nap��klad takto:</p>

<div class="okolo_img">
<img src="images/clanky/cl_gl_planety/land12.gif" width="222" height="222" alt="�eln� pohled">
<img src="images/clanky/cl_gl_planety/land13.gif" width="222" height="222" alt="Pohled zezadu">
</div>

<p>... mezi p�edn� a zadn� stranou u� nen� ��dn� shoda.</p>

<p class="autor">napsal: Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>


<h3>Anglick� origin�l</h3>

<ul class="zdroj_kody">
<li><?OdkazBlank('http://freespace.virgin.net/hugo.elias/models/m_landsp.htm');?></li>
</ul>


<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/planety.rar');?> - Uk�zkov� demo ve Visual C++</li>
</ul>

<div class="okolo_img"><img src="images/clanky/cl_gl_planety/planety.jpg" width="640" height="480" alt="Generov�n� planet" /></div>

<?
include 'p_end.php';
?>
