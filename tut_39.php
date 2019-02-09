<?
$g_title = 'CZ NeHe OpenGL - Lekce 39 - �vod do fyzik�ln�ch simulac�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(39);?>

<h1>Lekce 39 - �vod do fyzik�ln�ch simulac�</h1>

<p class="nadpis_clanku">V gravita�n�m poli se pokus�me rozpohybovat hmotn� bod s konstantn� rychlost�, hmotn� bod p�ipojen� k pru�in� a hmotn� bod, na kter� p�sob� gravita�n� s�la - v�e podle fyzik�ln�ch z�kon�. K�d je zalo�en na nejnov�j��m NeHeGL k�du.</p>

<p>Pokud zvl�d�te fyziku a chcete pou��vat k�d pro fyzik�ln� simulaci, tak V�m tento tutori�l m��e pomoci. Abyste ale mohli n�co vyt�it, m�li byste v�d�t n�co o po��t�n� s vektory v trojrozm�rn�m prostoru a fyzik�ln�ch veli�in�ch, jako je s�la nebo rychlost. Tutori�l obsahuje popis velmi jednoduch�ho fyzik�ln�ho simul�toru.</p>

<h2>T��da Vector3D</h2>

<p>N�vrh fyzik�ln�ho simula�n�ho enginu nen� v�dy jednoduch�. Ale je zde jednoduch� posloupnost z�vislost� - aplikace pot�ebuje simula�n� ��st a ta pot�ebuje matematick� knihovny. Tady tuto z�vislost uplatn�me. Na��m c�lem je z�skat z�sobn�k na simulaci pohybu objekt� v prostoru. Simula�n� ��st bude obsahovat t��dy Mass a Simulation. T��da Simulation bude na��m z�sobn�kem. Pokud vytvo��me t��du Simulation budeme schopni vyv�jet aplikace, kter� ji vyu��vaj�. Ale p�edt�m pot�ebujeme matematickou knihovnu. Knihovna obsahuje pouze jednu t��du Vector3D, kter� pro n�s bude p�edstavovat body, vektory, pozice, rychlost a s�lu ve 3D prostoru.</p>

<p>Vector3D tedy bude jedin�m �lenem na�� matematick� knihovny. Obsahuje sou�adnice x, y, z v p�esnosti float a zav�d� oper�tory pro po��t�n� s vektory ve 3D. Abychom byli konkr�tn�, p�et��me oper�tory s��t�n�, od��t�n�, n�soben� a d�len�. Proto�e se tento tutori�l zam��uje na fyziku a ne matematiku, nebudu podrobn� vysv�tlovat Vector3D. Pod�v�te-li se na jeho zdrojov� k�d, mysl�m si, �e nebudete m�t probl�my porozum�t.</p>

<h2>S�la a pohyb</h2>

<p>Abychom mohli implementovat fyzik�ln� simulaci, m�li bychom v�d�t, jak bude vypadat n� objekt. Bude m�t polohu a rychlost. Pokud je um�st�n na Zemi, M�s�ci, Marsu nebo na jak�mkoliv m�st�, kde je gravitace mus� m�t tak� hmotnost, kter� se li�� podle velikosti p�sob�c� gravita�n� s�ly. Vezm�me si t�eba knihu. Na Zemi v�� 1 kg, ale na M�s�ci pouze 0,17 kg, proto�e M�s�c na ni p�sob� men�� gravita�n� silou. My budeme uva�ovat hmotnost na Zemi.</p>

<p>Pot�, kdy� jsme pochopili, co pro n�s znamen� hmotnost, m�li bychom se p�esunout k s�le a pohybu. Objekt s nenulovou rychlost� se pohybuje ve sm�ru rychlosti. Proto je jeden z d�vod� zm�ny polohy v prostoru rychlost. A� se to nezd�, je dal�� p�sob�c� veli�inou �as. Posunut� p�edm�tu tedy z�vis� na tom, jak rychle se pohybuje, a na tom kolik �asu uplynulo od po��tku pohybu. Pokud v�m vztah mezi polohou, rychlost� a �asem nen� jasn�, tak asi nem� cenu pokra�ovat. Doporu�uji si vz�t u�ebnici fyziky a naj�t si kapitolu zab�vaj�c� se Newtonovy z�kony.</p>

<p>Rychlost objektu se m�n�, pokud na objekt p�sob� n�jak� s�la. Jej� vektor je kombinac� sm�ru (po��te�n� a koncov� bod) a velikosti. Velikost p�soben� je p��mo �m�rn� p�sob�c� s�le a nep��mo �m�rn� hmotnosti objektu. Zm�na rychlosti za jednotku �asu se naz�v� zrychlen�. ��m v�t�� s�la p�sob� na objekt, t�m v�ce zrychluje. ��m m�, ale v�t�� hmotnost, t�m je men�� zrychlen�.</p>

<p class="src0">zrychlen� = s�la / hmotnost</p>

<p>Odsud jednodu�e vyj�d��me s�lu:</p>

<p class="src0">s�la = hmotnost * zrychlen�</p>

<p>P�i p��prav� prost�ed� simulace si mus�te d�vat pozor na to, jak� podm�nky v tomto prost�ed� panuj�. Prost�ed� v tomto tutori�lu bude pr�zdn� prostor �ekaj�c� na zapln�n� objekty, kter� vytvo��me. Nejd��ve se rozhodneme, jak� jednotky pou�ijeme pro hmotnost, �as a d�lku. Rozhodl jsem se pou��t kilogram pro hmotnost, sekundu pro �as a metr pro d�lku. Tak�e jednotky rychlosti budou m/s a jednotky zrychlen� budou m/s^2 (metr za sekundu na druhou).</p>

<p>Abychom toto v�echno vyu�ili v praxi, mus�me napsat t��du, kter� bude reprezentovat objekt a bude obsahovat jeho hmotnost, polohu, rychlost a s�lu, kter� na n�ho p�sob�.</p>

<p class="src0">class Mass</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">float m;<span class="kom">// Hmotnost</span></p>
<p class="src"></p>
<p class="src1">Vector3D pos;<span class="kom">// Pozice v prostoru</span></p>
<p class="src1">Vector3D vel;<span class="kom">// Rychlosti a sm�r pohybu</span></p>
<p class="src1">Vector3D force;<span class="kom">// S�la p�sob�c� na objekt</span></p>

<p>V konstruktoru inicializujeme pouze hmotnost, kter� se jako jedin� nebude m�nit. Pozice, rychlost i p�sob�c� s�ly se ur�it� m�nit budou.</p>

<p class="src1">Mass(float m)<span class="kom">// Konstruktor</span></p>
<p class="src1">{</p>
<p class="src2">this-&gt;m = m;</p>
<p class="src1">}</p>

<p>Aplikujeme silov� p�soben�. Objekt m��e sou�asn� ovliv�ovat n�kolik zdroj�. Vektor v parametru je sou�et v�ech sil p�sob�c�ch na objekt. P�ed jeho aplikac� bychom m�li st�vaj�c� s�lu vynulovat. K tomu slou�� druh� funkce.</p>

<p class="src1">void applyForce(Vector3D force)</p>
<p class="src1">{</p>
<p class="src2">this-&gt;force += force;<span class="kom">// Vn�j�� s�la je p�i�tena</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">void init()</p>
<p class="src1">{</p>
<p class="src2">force.x = 0;</p>
<p class="src2">force.y = 0;</p>
<p class="src2">force.z = 0;</p>
<p class="src1">}</p>

<p>Zde je stru�n� seznam toho, co p�i simulaci mus�me prov�st:</p>

<ol>
<li>Vynulovat s�lu - metoda init()</li>
<li>Vypo��tat znovu p�sob�c� s�lu</li>
<li>P�izp�sobit pohyb posunu v �ase</li>
</ol>

<p>Pro pr�ci s �asem pou�ijeme Eulerovu metodu, kterou vyu��v� v�t�ina her. Existuj� mnohem sofistikovan�j�� metody, ale tahle posta��. Velmi jednodu�e se vypo��t� rychlost a poloha pro dal�� �asov� �sek s ohledem na p�sob�c� s�lu a uplynul� �as. Ke st�vaj�c� rychlosti p�i�teme jej� zm�nu, kter� je z�visl� na zrychlen� (s�la/m) a uplynul�m �ase (dt). V dal��m kroku p�izp�sob�me polohu - op�t v z�vislosti na �ase.</p>

<p class="src1">void simulate(float dt)</p>
<p class="src1">{</p>
<p class="src2">vel += (force / m) * dt;<span class="kom">// Zm�na rychlosti je p�i�tena k aktu�ln� rychlosti</span></p>
<p class="src2">pos += vel * dt;<span class="kom">// Zm�na polohy je p�i�tena k aktu�ln� poloze</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0">};</p>

<h2>Jak by m�la simulace pracovat</h2>

<p>P�i fyzik�ln� simulaci se b�hem ka�d�ho posunu opakuje tot�. S�ly jsou vynulov�ny, potom znovu spo��t�ny. V z�vislosti na nich se ur�uj� rychlosti a polohy p�edm�t�. Tento postup se opakuje tolikr�t, kolikr�t chceme. Je zaji��ov�n t��dou Simulation. Jej�m �kolem je vytv��et, ukl�dat a mazat objekty a starat se o b�h simulace.</p>

<p class="src0">class Simulation</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">int numOfMasses;<span class="kom">// Po�et objekt� v z�sobn�ku</span></p>
<p class="src1">Mass** masses;<span class="kom">// Objekty jsou uchov�v�ny v jednorozm�rn�m poli ukazatel� na objekty</span></p>
<p class="src"></p>

<p class="src1">Simulation(int numOfMasses, float m)<span class="kom">// Konstruktor vytvo�� objekty s danou hmotnost�</span></p>
<p class="src1">{</p>
<p class="src2">this-&gt;numOfMasses = numOfMasses;<span class="kom">// Inicializace po�tu</span></p>
<p class="src2">masses = new Mass*[numOfMasses];<span class="kom">// Alokace dynamick� pam�ti pro pole ukazatel�</span></p>
<p class="src"></p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Projdeme v�echny ukazatele na objekty</span></p>
<p class="src3">masses[a] = new Mass(m);<span class="kom">// Vytvo��me objekt a um�st�me ho na m�sto v poli</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">~Simulation()<span class="kom">// Sma�e vytvo�en� objekty</span></p>
<p class="src1">{</p>
<p class="src2">release();</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">virtual void release()<span class="kom">// Uvoln� dynamickou pam�</span></p>
<p class="src1">{</p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Sma�e v�echny vytvo�en� objekty</span></p>
<p class="src2">{</p>
<p class="src3">delete(masses[a]);<span class="kom">// Uvoln� dynamickou pam� objekt�</span></p>
<p class="src3">masses[a] = NULL;<span class="kom">// Nastav� ukazatele na NULL</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">delete(masses);<span class="kom">// Uvoln� dynamickou pam� ukazatel� na objekty</span></p>
<p class="src2">masses = NULL;<span class="kom">// Nastav� ukazatel na NULL</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">Mass* getMass(int index)<span class="kom">// Z�sk�n� objektu s ur�it�m indexem</span></p>
<p class="src1">{</p>
<p class="src2">if (index &lt; 0 || index &gt;= numOfMasses)<span class="kom">// Pokud index nen� v rozsahu pole</span></p>
<p class="src3">return NULL;<span class="kom">// Vr�t� NULL</span></p>
<p class="src"></p>
<p class="src2">return masses[index];<span class="kom">// Vr�t� objekt s dan�m indexem</span></p>
<p class="src1">}</p>

<p>Proces simulace se skl�d� ze t�� krok�:</p>

<ol>
<li>Init() nastav� s�ly na nulu</li>
<li>Solve() znovu aplikuje s�ly</li>
<li>Simulate(float dt) posune objekty v z�vislosti na �ase</li>
</ol>

<p class="src1">virtual void init()<span class="kom">// Tato metoda zavol� init() metodu ka�d�ho objektu</span></p>
<p class="src1">{</p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Proch�z� objekty</span></p>
<p class="src3">masses[a]-&gt;init();<span class="kom">// Zavol�n� init() dan�ho objektu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">virtual void solve()</p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Bez implementace, proto�e nechceme v z�kladn�m z�sobn�ku ��dn� s�ly</span></p>
<p class="src2"><span class="kom">// Ve vylep�en�ch z�sobn�c�ch, bude tato metoda nahrazena, aby na objekty p�sobila n�jak� s�la</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">virtual void simulate(float dt)<span class="kom">// V�po�et v z�vislosti na �ase</span></p>
<p class="src1">{</p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)<span class="kom">// Projdeme v�echny objekty</span></p>
<p class="src3">masses[a]-&gt;simulate(dt);<span class="kom">// V�po�et nov� polohy a rychlosti objektu</span></p>
<p class="src1">}</p>

<p>V�echny tyto metody jsou vol�ny v n�sleduj�c� funkci.</p>

<p class="src1">virtual void operate(float dt)<span class="kom">// Kompletn� simula�n� metoda</span></p>
<p class="src1">{</p>
<p class="src2">init();<span class="kom">// Krok 1: vynulov�n� sil</span></p>
<p class="src2">solve();<span class="kom">// Krok 2: aplikace sil</span></p>
<p class="src2">simulate(dt);<span class="kom">// Krok 3: vypo��t�n� polohy a rychlosti objekt� v z�vislosti na �ase</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0">};</p>

<p>Nyn� m�me jednoduch� simula�n� engine. Je zalo�en� na matematick� knihovn�. Obsahuje t��dy Mass a Simulation. Pou��v� b�nou Eulerovu metodu na v�po�et simulace. Te� jsme p�ipraveni na v�voj aplikac�. Aplikace, kterou budeme vyv�jet vyu��v�:</p>

<ol>
<li>Objekty s konstantn� hmotnost�</li>
<li>Objekty v gravita�n�m poli</li>
<li>Objekty spojen� pru�inou s n�jak�m bodem</li>
</ol>

<h2>Ovl�d�n� simulace aplikac�</h2>

<p>P�edt�m ne� nap�eme n�jakou simulaci, m�li bychom v�d�t, jak se t��dami zach�zet. V tomto tutori�lu jsou simula�n� a aplika�n� ��sti odd�leny do dvou samostatn�ch soubor�. V souboru s aplika�n� ��st� je funkce Update(), kter� se vol� opakovan� p�i ka�d�m nov�m framu.</p>

<p class="src0">void Update (DWORD milliseconds)<span class="kom">// Aktualizace pohybu</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// O�et�en� vstupu z kl�vesnice</span></p>
<p class="src1">if (g_keys->keyDown [VK_ESCAPE] == TRUE)</p>
<p class="src2">TerminateApplication (g_window);</p>
<p class="src"></p>
<p class="src1">if (g_keys->keyDown [VK_F1] == TRUE)</p>
<p class="src2">ToggleFullscreen (g_window);</p>
<p class="src"></p>
<p class="src1">if (g_keys->keyDown [VK_F2] == TRUE)</p>
<p class="src2">slowMotionRatio = 1.0f;</p>
<p class="src"></p>
<p class="src1">if (g_keys->keyDown [VK_F3] == TRUE)</p>
<p class="src2">slowMotionRatio = 10.0f;</p>

<p>DWORD milliseconds je �as, kter� uplynul od p�edchoz�ho vol�n� funkce. Budeme po��tat �as p�i simulac�ch na milisekundy. Pokud bude simulace sledovat tento �as, p�jde stejn� rychle jako v re�ln�m �ase. K proveden� simulace jednodu�e zavol�me funkci operate(float dt). P�edt�m ne� ji zavol�me mus�me zn�t hodnotu dt. Proto�e ve t��d� Simulation nepou��v�me milisekundy, ale sekundy, p�evedeme prom�nnou milliseconds na sekundy. Potom pou�ijeme prom�nnou slowMotionRatio, kter� ud�v�, jak m� b�t simulace zpomalen� vzhledem k re�ln�mu �asu. Touto prom�nnou d�l�me dt a dostaneme nov� dt. P�id�me dt k prom�nn� timeElapsed, kter� ud�v� kolik �asu simulace u� ub�hlo (neud�v� tedy re�ln� �as).</p>

<p class="src1">float dt = milliseconds / 1000.0f;<span class="kom">// P�epo��t� milisekundy na sekundy</span></p>
<p class="src"></p>
<p class="src1">dt /= slowMotionRatio;<span class="kom">// D�len� dt zpomalovac� prom�nnou</span></p>
<p class="src"></p>
<p class="src1">timeElapsed += dt;<span class="kom">// Zv�t�en� uplynul�ho �asu</span></p>

<p>Te� u� je dt skoro p�ipraveno na pou�it� v simulaci. Ale! je tu jedna d�le�it� v�c, kterou bychom m�li v�d�t: ��m men�� je dt, t�m re�ln�j�� je simulace. Pokud nebude dt dostate�n� mal�, na�e simulace se nebude chovat realisticky, proto�e pohyb nebude spo��t�n dostate�n� precizn�. Anal�za stability se u��v� p�i fyzik�ln�ch simulac�ch, aby zajistila maxim�ln� p�ijatelnou hodnotu dt. V tomto tutori�lu se nebudeme pou�t�t do detail�. Pokud vyv�j�te hru a ne specializovanou aplikaci, tato metoda bohat� sta�� na to, abyste se vyhnuli chyb�m.</p>

<p>Nap��klad v automobilov�m simul�toru je vhodn�, aby se dt pohybovalo mezi 2 a� 5 milisekundami pro b�n� auto a mezi 1 a 3 milisekundami pro formuli. P�i ark�dov�m simul�toru je mo�n� pou��t dt v rozsahu od 10 do 200 milisekund. ��m ni��� je dt, t�m siln�j�� procesor pot�ebujeme, abychom st�hali simulovat v re�ln�m �ase. To je d�vod pro� se u star��ch her nepou��vaj� fyzik�ln� simulace.</p>

<p>V n�sleduj�c�m k�du nastav�me maxim�ln� hodnotu dt na 0.1 sekundy (100 milisekund). S touto hodnotou spo��t�me kolikr�t cyklus simulace p�i ka�d�m projit� funkce zopakujeme. To �e�� n�sleduj�c� vzorec:</p>

<p>int numOfIterations = (int)(dt / maxPossible_dt) + 1;</p>

<p>NumOfIterations je po�et cykl�, kter� p�i simulaci provedeme. Dejme tomu, �e aplikace b�� 20 fram� za sekundu. Z toho plyne, �e dt=0.05. numOfIterations tedy bude 1. Simulace se provede jednou po 0.05 sekund�ch. Pokud by dt bylo 0.12 sekund, pak numOfIterations bude 2. Pod v k�du uveden�m vzorcem m��ete vid�t, �e dt po��t�me je�t� jednou. Pod�l�me ho po�tem cykl� a bude dt = 0.12 / 2 = 0.06. dt bylo p�vodn� vy��� ne� maxim�ln� mo�n� hodnota 0.1. Te� se tedy rovn� 0.06. My ale provedeme dva cykly simulace, tak�e v simulaci ub�hne �as 0.12 sekund. Prozkoumejte n�sleduj�c� k�d a ujist�te se, �e v�emu rozum�te.</p>

<p class="src1"><span class="kom">// Abychom nep�ekro�ili hranici kdy u� se simulace nechov� re�ln�</span></p>
<p class="src1">float maxPossible_dt = 0.1f;<span class="kom">// Nastaven� maxim�ln� hodnoty dt na 0.1 sekund</span></p>
<p class="src"></p>
<p class="src1">int numOfIterations = (int)(dt / maxPossible_dt) + 1;<span class="kom">// V�po�et po�tu opakov�n� simulace v z�vislosti na dt a maxim�ln� mo�n� hodnot� dt</span></p>
<p class="src"></p>
<p class="src1">if (numOfIterations != 0)<span class="kom">// Vyhneme se d�len� nulou</span></p>
<p class="src2">dt = dt / numOfIterations;<span class="kom">// dt by se m�la aktualizovat pomoc� numOfIterations</span></p>
<p class="src"></p>
<p class="src1">for (int a = 0; a &lt; numOfIterations; ++a)<span class="kom">// Simulaci pot�ebujeme opakovat numOfIterations-kr�t</span></p>
<p class="src1">{</p>
<p class="src2">constantVelocity.operate(dt);<span class="kom">// Proveden� simulace konstantn� rychlosti za dt sekund</span></p>
<p class="src2">motionUnderGravitation.operate(dt);<span class="kom">// Proveden� simulace pohybu v gravitaci za dt sekund</span></p>
<p class="src2">massConnectedWithSpring.operate(dt);<span class="kom">// Proveden�  simulace pru�iny za dt sekund
</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0">}</p>

<h2>1. Objekt s konstantn� rychlost�</h2>

<p>Objekt s konstantn� rychlost� nepot�ebuje p�soben� extern� s�ly. Pouze vytvo��me objekt a nastav�me jeho rychlost na (1.0f, 0.0f, 0.0f), tak�e se bude pohybovat po ose x rychlost� 1 m/s. T��du ConstantVelocity odvod�me od t��dy Simulation.</p>

<p class="src0">class ConstantVelocity : public Simulation</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1"><span class="kom">// Konstruktor nejd��ve pou�ije konstruktor nad�azen� t��dy, aby vytvo�il objekt o hmotnosti 1 kg</span></p>
<p class="src1">ConstantVelocity() : Simulation(1, 1.0f)</p>
<p class="src1">{</p>
<p class="src2">masses[0]-&gt;pos = Vector3D(0.0f, 0.0f, 0.0f);<span class="kom">// Nastav�me polohu objektu na po��tek</span></p>
<p class="src2">masses[0]-&gt;vel = Vector3D(1.0f, 0.0f, 0.0f);<span class="kom">// Nastav�me rychlost objektu na (1.0f, 0.0f, 0.0f) m/s</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src0">};</p>

<p>Kdy� je vol�na metoda operate(float dt) t��dy ConstantVelocity, vypo��t� se nov� polohu objektu. Tato metoda je vol�na hlavn� aplikac� p�ed ka�d�m p�ekreslen�m okna. Dejme tomu, �e aplikace b�� 10 fram� za sekundu. To znamen�, �e p�i ka�d�m nov�m v�po�tu bude dt 0.1 sekundy. Kdy� se potom zavol� funkce simulate(float dt) dan�ho objektu, k jeho pozici se p�i�te rychlost*dt, kter� se rovn�:</p>

<p>Vector3D(1.0f, 0.0f, 0.0f) * 0.1 = Vector3D(0.1f, 0.0f, 0.0f)</p>

<p>P�i ka�d� frame se objekt pohne o 0.1 metru doprava. Po 10 framech to bude pr�v� 1 metr. Rychlost byla 1.0 m/s. Tak�e to bude fungovat celkem slu�n�.</p>

<p>Kdy� spust�te aplikaci, uvid�te objekt pohybuj�c� se konstantn� rychlost� po ose x. Aplikace nab�z� dv� rychlosti plynut� �asu. Stisknut�m F2 pob�� stejn� rychle jako re�ln� �as. Stisknut�m F3 pob�� 10kr�t pomaleji. Na obrazovce uvid�te p��mky zn�zor�uj�c� sou�adnicovou plochu. Mezery mezi p�imkami jsou 1 metr. D�ky t�mto p��mk�m uvid�te, �e se objekt pohybuje 1 metr za sekundu v re�ln�m �ase a 1 metr za 10 sekund ve zpomalen�m �ase. V��e popsan� technika je zp�sob, jak ud�lat simulaci tak, aby b�ela v re�ln�m �ase. Abyste ji mohli pou��t mus�te se pevn� rozhodnout, v jak�ch jednotk�ch simulace pob��.</p>

<h2>Aplikace s�ly</h2>

<p>P�i simulac�ch s konstantn� rychlost� jsme nepou�ili s�lu p�sob�c� na objekt, proto�e v�me, �e pokud s�la p�sob� na objekt, tak m�n� jeho rychlost. Pokud chceme pohyb s prom�nlivou rychlost� pou�ijeme vn�j�� s�lu. Nejd��ve mus�me v�echny p�sob�c� s�ly se��st, abychom dostali v�slednou s�lu, kterou v simula�n� f�zi aplikujeme na objekt.</p>

<p>Dejme tomu, �e chcete pou��t na objekt s�lu 1 N ve sm�ru x. Pak do solve() nap�ete:</p>

<p>mass-&gt;applyForce(Vector3D(1.0f, 0.0f, 0.0f));</p>

<p>Pokud chcete nav�c p�idat s�lu 2 N ve sm�ru y, nap�ete:</p>

<p>mass->applyForce(Vector3D(1.0f, 0.0f, 0.0f));<br />
mass->applyForce(Vector3D(0.0f, 2.0f, 0.0f));</p>

<p>Na objekt m��ete pou��t libovoln� mno�stv� sil, libovoln�ch sm�r�, abyste ovlivnili pohyb. V n�sleduj�c� ��sti pou�ijeme jednoduchou s�lu.</p>

<h2>2. Pohyb v gravitaci</h2>

<p>MotionUnderGravitation vytvo�� objekt a nech� na n�j p�sobit s�lu. Touto silou bude pr�v� gravitace, kter� se vypo��t� vyn�soben�m hmotnosti objektu a gravita�n�ho zrychlen�:</p>

<p>F = m * g</p>

<p>Gravita�n� zrychlen� na Zemi odpov�d� 9.81 m/s^2. To znamen�, �e objekt p�i voln�m p�du zrychl� ka�dou sekundu o 9.81 m/s dokud na n�ho nep�sob� ��dn� jin� s�la ne� gravitace. M��e j� b�t odpor vzduchu, kter� p�sob� v�dycky, ale to sem nepat��.</p>

<p class="src0">class MotionUnderGravitation : public Simulation</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">Vector3D gravitation;<span class="kom">// Gravita�n� zrychlen�</span></p>

<p>Konstruktor p�ij�m� Vector3D, kter� ud�v� s�lu a orientaci gravitace.</p>

<p class="src1"><span class="kom">// Konstruktor nejd��ve pou�ije konstruktor nad�azen� t��dy, aby vytvo�il 1 objekt o hmotnosti 1kg</span></p>
<p class="src1">MotionUnderGravitation(Vector3D gravitation) : Simulation(1, 1.0f)</p>
<p class="src1">{</p>
<p class="src2">this-&gt;gravitation = gravitation;<span class="kom">// Nastaven� gravitace</span></p>
<p class="src2">masses[0]-&gt;pos = Vector3D(-10.0f, 0.0f, 0.0f);<span class="kom">// Nastaven� polohy objektu</span></p>
<p class="src2">masses[0]-&gt;vel = Vector3D(10.0f, 15.0f, 0.0f);<span class="kom">// Nastaven� rychlosti objektu</span></p>
<p class="src1">}</p>

<p class="src1">virtual void solve()<span class="kom">// Aplikace gravitace na v�echny objekty, na kter� m� p�sobit</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Pou�ijeme gravitaci na v�echny objekty (zat�m m�me jenom jeden, ale to se m��e do budoucna zm�nit)</span></p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)</p>
<p class="src3">masses[a]-&gt;applyForce(gravitation * masses[a]-&gt;m);<span class="kom">// S�la gravitace se spo��t� F = m * g</span></p>
<p class="src1">}</p>

<p>V k�du naho�e si m��ete v�imnout vzorce F = m * g. Pro re�ln� p�soben� gravitace byste m�li p�edat konstruktoru Vectror3D(0.0f, -9.81f, 0.0f). -9.81 znamen�, �e m� gravitace p�sobit proti sm�ru y, co� zp�sobuje, �e objekt pad� sm�rem dol�. M��ete zkusit zadat kladn� ��slo a ur�it� pozn�te rozd�l.</p>

<h2>3. Objekt spojen� pru�inou s bodem</h2>

<p>V tomto p��klad� chceme spojit objekt se statick�m bodem. Pru�ina by m�la objekt p�itahovat k bodu upevn�n� a tak zp�sobovat oscilaci objektu. V konstruktoru nastav�me bod upevn�n� a pozici objektu.</p>

<p class="src0">class MassConnectedWithSpring : public Simulation</p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">float springConstant;<span class="kom">// ��m vy��� bude tato konstanta, t�m tu��� bude pru�ina</span></p>
<p class="src1">Vector3D connectionPos;<span class="kom">// Bod ke kter�mu bude objekt p�ipojen</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Konstruktor nejd��ve pou�ije konstruktor nad�azen� t��dy, aby vytvo�il 1 objekt o hmotnosti 1kg</span></p>
<p class="src1">MassConnectedWithSpring(float springConstant) : Simulation(1, 1.0f)</p>
<p class="src1">{</p>
<p class="src2">this-&gt;springConstant = springConstant;<span class="kom">// Nastaven� tuhosti pru�iny</span></p>
<p class="src"></p>
<p class="src2">connectionPos = Vector3D(0.0f, -5.0f, 0.0f);<span class="kom">// Nastaven� pozice upev�ovac�ho bodu</span></p>
<p class="src"></p>
<p class="src2">masses[0]-&gt;pos = connectionPos + Vector3D(10.0f, 0.0f, 0.0f);<span class="kom">// Nastaven� pozice objektu na 10 metr� napravo od bodu, ke kter�mu je uchycen</span></p>
<p class="src2">masses[0]-&gt;vel = Vector3D(0.0f, 0.0f, 0.0f);<span class="kom">// Nastaven� rychlosti objektu na nulu</span></p>
<p class="src1">}</p>

<p>Rychlost objektu je nula a jeho pozice je 10 metr� napravo od �chytu, tak�e se bude pohybovat ze za��tku sm�rem doleva. S�la pru�iny se d� zapsat jako</p>

<p>F = -k * x</p>

<p>k je tuhost pru�iny a x je vzd�lenost od �chytu. Z�porn� hodnota u k zna��, �e jde o p�ita�livou s�lu. Kdyby bylo k kladn�, tak by pru�ina objekt odpuzovala, co� zcela jist� neodpov�d� skute�n�mu chov�n�.</p>

<p class="src1">virtual void solve()<span class="kom">// U�it� s�ly pru�iny</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Pou�ijeme s�lu na v�echny objekty (zat�m m�me jenom jeden, ale to se m��e do budoucna zm�nit)</span></p>
<p class="src2">for (int a = 0; a &lt; numOfMasses; ++a)</p>
<p class="src2">{</p>
<p class="src3">Vector3D springVector = masses[a]-&gt;pos - connectionPos;<span class="kom">// Nalezen� vektoru od pozice objektu k �chytu</span></p>
<p class="src3">masses[a]-&gt;applyForce(-springVector * springConstant);<span class="kom">// Pou�it� s�ly podle uveden�ho vzorce</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">};</p>

<p>V�po�et s�ly v k�du naho�e odpov�d� vzorci, kter� jsme si uvedli (F = -k * x). Jenom je zde m�sto x trojrozm�rn� vektor a m�sto k je zde springConstant. ��m vy��� je springConstant, t�m rychleji objekt osciluje.</p>

<p>V tomto tutori�lu jsem se sna�il p�edv�st z�kladn� prvky pro tvorbu fyzik�ln�ch simulac�. Pokud v�s zaj�m� fyzika, nebude pro v�s t�k� vytvo�it vlastn� simulace. M��ete zkou�et slo�it�j�� interakce a vytvo�it tak zaj�mav� dema a hry. Dal�� v po�ad� by m�li b�t simulace pevn�ch objekt�, jednoduch� mechaniky a pokro�il� simula�n� metody.</p>

<p class="autor">napsal: Erkin Tunca <?VypisEmail('erkintunca@icqmail.com');?><br />
p�elo�il: V�clav Slov��ek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson39.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson39_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson39.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson39.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson39.tar.gz">Linux/GLut</a> k�d t�to lekce. ( <a href="mailto:laks@imag.fr">Laks Raghupathi</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson39.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(39);?>
<?FceNeHeOkolniLekce(39);?>

<?
include 'p_end.php';
?>
