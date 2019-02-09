<?
$g_title = 'CZ NeHe OpenGL - Lekce 42 - V�ce viewport�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(42);?>

<h1>Lekce 42 - V�ce viewport�</h1>

<p class="nadpis_clanku">Tento tutori�l byl naps�n pro v�echny z v�s, kte�� se cht�li dozv�d�t, jak do jednoho okna zobrazit v�ce pohled� na jednu sc�nu, kdy v ka�d�m prob�h� jin� efekt. Jako bonus p�id�m z�sk�v�n� velikosti OpenGL okna a velice rychl� zp�sob aktualizace textury bez jej�ho znovuvytv��en�.</p>

<p>V�tejte do dal��ho perfektn�ho tutori�lu. Tentokr�t se pokus�me v jednom okn� zobrazit �ty�i viewporty, kter� se budou p�i zm�n� velikosti okna bez probl�m� zmen�ovat i zv�t�ovat. Ve dvou z nich zapneme sv�tla, jeden bude pou��vat pravo�hlou projekci a t�i perspektivn�. Abychom demu zajistili kvalitn� efekty, budeme do textury postupn� generovat p�dorys bludi�t� a mapovat ji na objekty v jednotliv�ch viewportech.</p>

<p>Jakmile jednou porozum�te tomuto tutori�lu, nebudete m�t nejmen�� probl�my p�i vytv��en� her pro v�ce hr��� s rozd�len�mi sc�nami nebo 3D aplikac�, ve kter�ch pot�ebujete n�kolik pohled� na modelovan� objekt (p�dorys, n�rys, bokorys, dr�t�n� model ap.).</p>

<p>Jako z�kladn� k�d m��ete pou��t bu� nejnov�j�� NeHeGL nebo IPicture. Je to, d� se ��ct, jedno, ale provedeme v n�m n�kolik �prav. Nejd�le�it�j�� zm�nu najdete ve funkci ReshapeGL(), ve kter� se definuj� dimenze sc�ny (hlavn� viewport). V�echna nastaven� p�esuneme do vykreslovac� smy�ky, z�stane zde pouze definov�n� rozm�r� hlavn�ho okna.</p>

<p class="src0">void ReshapeGL(int width, int height)<span class="kom">// Vol� se p�i zm�n� velikosti okna</span></p>
<p class="src0">{</p>
<p class="src1">glViewport(0, 0, (GLsizei)(width), (GLsizei)(height));<span class="kom">// Reset aktu�ln�ho viewportu</span></p>
<p class="src0">}</p>

<p>Druh� zm�na spo��v� v o�et�en� syst�mov� ud�losti WM_ERASEBKGND. Ukon�en�m funkce zamez�me r�zn�mu mihot�n� a blik�n� sc�ny p�i roztahov�n� okna, kdy syst�m automaticky ma�e pozad�. Pokud nerozum�te, odstra�te oba ��dky a porovnejte chov�n� okna p�i zm�n� jeho velikosti.</p>

<p class="src0"><span class="kom">// Funkce WindowProc()</span></p>
<p class="src1">switch (uMsg)<span class="kom">// V�tven� podle do�l� zpr�vy</span></p>
<p class="src1">{</p>
<p class="src2">case WM_ERASEBKGND:<span class="kom">//Okno zkou�� smazat pozad�</span></p>
<p class="src3">return 0;<span class="kom">// Z�kaz maz�n� (prevence blik�n�)</span></p>

<p>Nyn� p�ejdeme k opravdov�mu k�du tohoto tutori�lu. Za�neme deklarac� glob�ln�ch prom�nn�ch. Mx a my specifikuj� m�stnost v bludi�ti, ve kter� se pr�v� nach�z�me. Width a height definuj� rozm�ry textury, ka�d�mu pixelu bludi�t� odpov�d� jeden pixel na textu�e. Pokud va�e grafick� karta podporuje v�t�� textury, zkuste zv�t�it toto ��slo na n�sleduj�c� n�sobky dvou nap�. 256, 512, 1024. Ujist�te se ale, �e ho nezv�t��te p��li� mnoho. M�-li nap��klad okno ���ku 1024 pixel�, viewporty budou polovi�n�, tak�e nem� cenu, aby textura byla v�t�� ne� 512, proto�e by se stejn� zmen�ovala. To sam� samoz�ejm� plat� i pro v��ku.</p>

<p class="src0">int mx, my;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src"></p>
<p class="src0">const width = 128;<span class="kom">// ���ka textury (mus� b�t mocninou ��sla 2)</span></p>
<p class="src0">const height = 128;<span class="kom">// V��ka textury (mus� b�t mocninou ��sla 2)</span></p>

<p>Done povede z�znam o tom, jestli u� bylo generov�n� bludi�t� dokon�eno. V�ce podrobnost� se dozv�te pozd�ji. Sp pou��v�me k o�et�en� toho, aby program nebral dlouh� stisk mezern�ku za n�kolik spolu nesouvisej�c�ch stisk�. Po jeho zm��knut� resetujeme texturu a za�neme kreslit bludi�t� od znova.</p>

<p class="src0">BOOL done;<span class="kom">// Bludi�t� vygenerov�no?</span></p>
<p class="src0">BOOL sp;<span class="kom">// Flag stisku mezern�ku</span></p>

<p>�ty�prvkov� pole r, g, b ukl�daj� slo�ky barev pro jednotliv� viewporty. Pou��v�me datov� typ BYTE, proto�e se l�pe z�sk�vaj� n�hodn� ��sla od 0 do 255 ne� od 0.0f do 1.0f. Tex_data ukazuje na pam� dat textury.</p>

<p class="src0">BYTE r[4], g[4], b[4];<span class="kom">// �ty�i n�hodn� barvy</span></p>
<p class="src0">BYTE* tex_data;<span class="kom">// Data textury</span></p>

<p>Xrot, yrot a zrot specifikuj� �hel rotace 3D objektu na jednotliv�ch sou�adnicov�ch os�ch. Quadratic pou�ijeme pro kreslen� koule a v�lce.</p>

<p class="src0">GLfloat xrot, yrot, zrot;<span class="kom">// �hly rotac� objekt�</span></p>
<p class="src0">GLUquadricObj *quadric;<span class="kom">// Objekt quadraticu</span></p>

<p>Pomoc� n�sleduj�c� funkce budeme moci snadno zab�lit pixel textury na sou�adnic�ch dmx, dmy. Tex_data p�edstavuje ukazatel na data textury. Lokaci pixelu z�sk�me vyn�soben�m y pozice (dmy) ���kou ��dku (width) a p�i�ten�m pozice na ��dku (dmx). Proto�e se ka�d� pixel skl�d� ze t�� byt� n�sob�me v�sledek t�emi. Aby kone�n� barva byla b�l�, mus�me p�i�adit ��slo 255 v�em t�em barevn�m slo�k�m.</p>

<p class="src0">void UpdateTex(int dmx, int dmy)<span class="kom">// Zab�l� ur�en� pixel na textu�e</span></p>
<p class="src0">{</p>
<p class="src1">tex_data[0 + ((dmx + (width * dmy)) * 3)] = 255;<span class="kom">// �erven� slo�ka</span></p>
<p class="src1">tex_data[1 + ((dmx + (width * dmy)) * 3)] = 255;<span class="kom">// Zelen� slo�ka</span></p>
<p class="src1">tex_data[2 + ((dmx + (width * dmy)) * 3)] = 255;<span class="kom">// Modr� slo�ka</span></p>
<p class="src0">}</p>

<p>Reset m� na starosti n�kolik relativn� d�le�it�ch �kon�. V prvn� �ad� kompletn� za�ern� texturu a t�m odstran� dosavadn� bludi�t�, d�le p�i�azuje nov� barvy viewport�m a reinicializuje pozici v bludi�ti. Prvn� ��dkou k�du nulujeme data textury, co� ve v�sledku znamen�, �e v�echny pixely budou �ern�.</p>

<p class="src0">void Reset(void)<span class="kom">// Reset textury, barev, aktu�ln� pozice v bludi�ti</span></p>
<p class="src0">{</p>
<p class="src1">ZeroMemory(tex_data, width * height * 3);<span class="kom">// Nuluje pam� textury</span></p>

<p>Pot�ebujeme nastavit n�hodnou barvu viewport�. Pro ty z v�s, kte�� to je�t� nev�, random nen� zase tak n�hodn�, jak by se mohl na prvn� pohled zd�t. Pokud vytvo��te jednoduch� program, kter� m� vypsat deset n�hodn�ch ��sel, tak samoz�ejm� vyp�e deset n�hodn�ch ��sel, kter� nem�te �anci p�edem odhadnout. Ale p�i p��t�m spu�t�n� se bude v�ech deset &quot;n�hodn�ch&quot; ��sel opakovat. Abychom tento probl�m odstranili, inicializujeme gener�tor. Pokud bychom ho ale nastavili na konstantn� hodnotu (1, 2, 3...), v�sledkem by op�t byla p�i v�ce spu�t�n�ch stejn� ��sla. Proto p�ed�v�me funkci srand() hodnotu aktu�ln�ho �asu (P�ekl.: po�et milisekund od spu�t�n� OS), kter� se samoz�ejm� v�dy m�n�.</p>

<p>P�ekl.: B�v� zvykem inicializovat gener�tor n�hodn�ch ��sel pouze jednou a to n�kde na za��tku funkce main() a ne, jak d�l�me zde, p�i ka�d�m vol�n� Reset() - nen� to �patn�, ale je to zbyte�n�.</p>

<p class="src1">srand(GetTickCount());<span class="kom">// Inicializace gener�toru n�hodn�ch ��sel</span></p>

<p>V cyklu, kter� projde v�echny �ty�i viewporty nastavujeme pro ka�d� n�hodnou barvu. Mohli bychom generovat ��slo v pln�m rozsahu (0 a� 255), ale nem�li bychom tak zaru�eno, �e nez�sk�me n�jakou n�zkou hodnotu (aby na �ern� byla vid�t). P�i�ten�m 128 z�sk�me sv�tlej�� barvy.</p>

<p class="src1">for (int loop = 0; loop &lt; 4; loop++)<span class="kom">// Generuje �ty�i n�hodn� barvy</span></p>
<p class="src1">{</p>
<p class="src2">r[loop] = rand() % 128 + 128;<span class="kom">// �erven� slo�ka</span></p>
<p class="src2">g[loop] = rand() % 128 + 128;<span class="kom">// Zelen� slo�ka</span></p>
<p class="src2">b[loop] = rand() % 128 + 128;<span class="kom">// Modr� slo�ka</span></p>
<p class="src1">}</p>

<p>Nakonec nastav�me po��te�n� bod v bludi�ti - op�t n�hodn�. V�sledkem mus� b�t sud� ��slo (zajist� n�soben� dv�ma), proto�e lich� pozice ozna�uj� st�ny mezi m�stnostmi.</p>

<p class="src1">mx = int(rand() % (width / 2)) * 2;<span class="kom">// N�hodn� x pozice</span></p>
<p class="src1">my = int(rand() % (height / 2)) * 2;<span class="kom">// N�hodn� y pozice</span></p>
<p class="src0">}</p>

<p>Prvn�m ��dkem v inicializaci alokujeme dynamickou pam� pro ulo�en� textury.</p>

<p class="src0">BOOL Initialize (GL_Window* window, Keys* keys)<span class="kom">// Inicializace OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">tex_data = new BYTE[width * height * 3];<span class="kom">// Alokace pam�ti pro texturu</span></p>
<p class="src"></p>
<p class="src1">g_window = window;<span class="kom">// Okno</span></p>
<p class="src1">g_keys = keys;<span class="kom">// Kl�vesy</span></p>

<p>Vol�me Reset(), abychom ji za�ernili a nastavili barvy viewport�.</p>

<p class="src1">Reset();<span class="kom">// Reset textury, barev, pozice</span></p>

<p>Inicializaci textury za�neme nastaven�m clamp parametr� do rozmez� [0; 1]. T�mto odstran�me mo�n� artefakty v podob� tenk�ch linek, kter� vznikaj� na okraj�ch textury. P���ina jejich zobrazov�n� spo��v� v line�rn�m filtrov�n�, kter� se pokou�� vyhladit texturu, ale zahrnuje do n� i jej� okraje. Zkuste odstranit prvn� dva ��dky a uvid�te, co mysl�m. Jak u� jsem zm�nil, nastav�me line�rn� filtrov�n� a vytvo��me texturu.</p>

<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_S, GL_CLAMP);<span class="kom">// Clamp parametry textury</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_T, GL_CLAMP);</p>
<p class="src"></p>
<p class="src1">glTexParameterf(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);<span class="kom">// Line�rn� filtrov�n�</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR); </p>
<p class="src"></p>
<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, GL_RGB, width, height, 0, GL_RGB, GL_UNSIGNED_BYTE, tex_data);<span class="kom">// Vytvo�� texturu</span></p>

<p>Pozad� budeme mazat �ernou barvou a hloubku jedni�kou. D�le nastav�me testov�n� hloubky na men�� nebo rovno a zapneme ho.</p>

<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� depth bufferu</span></p>
<p class="src"></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>

<p>Povolen� GL_COLOR_MATERIAL umo�n� m�nit barvu textury pou�it�m funkce glColor3f(). Tak� zap�n�me mapov�n� textur.</p>

<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Zapne vybarvov�n� materi�l�</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>

<p>Vytvo��me a inicializujeme objekt quadraticu tak, aby obsahoval norm�lov� vektory pro sv�tlo a texturov� koordin�ty.</p>

<p class="src1">quadric = gluNewQuadric();<span class="kom">// Vytvo�� objekt quadraticu</span></p>
<p class="src1">gluQuadricNormals(quadric, GLU_SMOOTH);<span class="kom">// Norm�ly pro sv�tlo</span></p>
<p class="src1">gluQuadricTexture(quadric, GL_TRUE);<span class="kom">// Texturov� koordin�ty</span></p>

<p>I kdy� je�t� nem�me povoleny sv�tla glob�ln�, zapneme sv�tlo 0.</p>

<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne sv�tlo 0</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�e v po��dku</span></p>
<p class="src0">}</p>

<p>Po jak�koli alokaci dynamick� pam�ti mus� p�ij�t jej� uvoln�n�. Tuto akci vlo��me do funkce Deinitialize(), kter� se vol� p�ed ukon�en�m programu.</p>

<p class="src0">void Deinitialize(void)<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">delete [] tex_data;<span class="kom">// Sma�e data textury</span></p>
<p class="src0">}</p>

<p>Update m� na starosti aktualizaci zobrazovan� sc�ny, stisky kl�ves, pohyby, rotace a podobn�. Celo��selnou prom�nnou dir vyu�ijeme k pohybu n�hodn�m sm�rem.</p>

<p class="src0">void Update(float milliseconds)<span class="kom">// Aktualizace sc�ny</span></p>
<p class="src0">{</p>
<p class="src1">int dir;<span class="kom">// Ukl�d� aktu�ln� sm�r pohybu</span></p>

<p>V prvn� f�zi o�et��me kl�vesnici. P�i stisku Esc ukon��me program, F1 p�ep�n� m�d fullscreen/okno a mezern�k resetuje bludi�t�.</p>

<p class="src1">if (g_keys-&gt;keyDown[VK_ESCAPE])<span class="kom">// Kl�vesa Esc</span></p>
<p class="src1">{</p>
<p class="src2">TerminateApplication (g_window);<span class="kom">// Konec programu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[VK_F1])<span class="kom">// Kl�vesa F1</span></p>
<p class="src1">{</p>
<p class="src2">ToggleFullscreen (g_window);<span class="kom">// P�epne fullscreen/okno</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (g_keys-&gt;keyDown[' '] &amp;&amp; !sp)<span class="kom">// Mezern�k</span></p>
<p class="src1">{</p>
<p class="src2">sp = TRUE;</p>
<p class="src2">Reset();<span class="kom">// Resetuje sc�nu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!g_keys-&gt;keyDown[' '])<span class="kom">// Uvoln�n� mezern�ku</span></p>
<p class="src1">{</p>
<p class="src2">sp = FALSE;</p>
<p class="src1">}</p>

<p>Rot prom�nn� zv�t��me v z�vislosti na po�tu uplynul�ch milisekund od minul�ho vol�n� t�to funkce. T�m zajist�me rotaci objekt�.</p>

<p class="src1">xrot += (float)(milliseconds) * 0.02f;<span class="kom">// Aktualizace �hl� nato�en�</span></p>
<p class="src1">yrot += (float)(milliseconds) * 0.03f;</p>
<p class="src1">zrot += (float)(milliseconds) * 0.015f;</p>

<p>K�d n�e zji��uje, jestli bylo kreslen� bludi�t� ukon�eno (textura kompletn� zapln�na). Nejd��ve nastav�me flag done na true. P�edpokl�d�me tedy, �e u� vykresleno bylo. Ve dvou vno�en�ch cyklech proch�z�me jednotliv� ��dky i sloupce a kontrolujeme, zda byl n� odhad spr�vn�. Pokud ne, nastav�me done na false.</p>

<p>Jak pracuje k�d? ��d�c� prom�nn� cykl� zvy�ujeme o dva, proto�e n�m jde jen o sud� indexy v poli. Ka�d� bludi�t� se skl�d� ze st�n (lich�) a m�stnost� (sud�). Kdy� otev�eme dve�e, dostaneme se do m�stnosti a pr�v� ty tedy mus�me testovat. Kontroly st�n jsou samoz�ejm� zbyte�n�. Pokud se hodnota v poli rovn� nule, znamen� to, �e jsme do n�j je�t� nekreslili a m�stnost nebyla nav�t�vena.</p>

<p class="src1">done = TRUE;<span class="kom">// P�edpokl�d� se, �e je u� bludi�t� kompletn�</span></p>
<p class="src"></p>
<p class="src1">for (int x = 0; x &lt; width; x += 2)<span class="kom">// Proch�z� v�echny m�stnosti na ose x</span></p>
<p class="src1">{</p>
<p class="src2">for (int y = 0; y &lt; height; y += 2)<span class="kom">// Proch�z� v�echny m�stnosti na ose y</span></p>
<p class="src2">{</p>
<p class="src3">if (tex_data[((x + (width * y)) * 3)] == 0)<span class="kom">// Pokud m� pixel �ernou barvu</span></p>
<p class="src3">{</p>
<p class="src4">done = FALSE;<span class="kom">// Bludi�t� je�t� nen� hotov�</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Pokud byly v�echny m�stnosti objeveny, zm�n�me titulek okna na ...Maze Complete!, potom po�k�me p�t sekund, aby si ho stihl u�ivatel p�e��st, vr�t�me titulek zp�t a resetujeme bludi�t�.</p>

<p class="src1">if (done)<span class="kom">// Je bludi�t� hotov�?</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Zm�na titulku okna</span></p>
<p class="src2">SetWindowText(g_window-&gt;hWnd, &quot;Lesson 42: Multiple Viewports... 2003 NeHe Productions... Maze Complete!&quot;);</p>
<p class="src2">Sleep(5000);<span class="kom">// Zastaven� na p�t sekund</span></p>
<p class="src"></p>
<p class="src2">SetWindowText(g_window-&gt;hWnd, &quot;Lesson 42: Multiple Viewports... 2003 NeHe Productions... Building Maze!&quot;);</p>
<p class="src2">Reset();<span class="kom">// Reset bludi�t� a sc�ny</span></p>
<p class="src1">}</p>

<p>P�edpokl�d�m, �e pro v�s n�sleduj�c� podm�nka vypad� tot�ln� ��len�, ale v�bec nen� t�k�. skl�d� se ze �ty� AND-ovan�ch podpodm�nek a ka�d� z nich ze dvou dal��ch. V�echny �ty�i hlavn� ��sti jsou skoro stejn� a dohromady zji��uj�, jestli existuje m�stnost okolo aktu�ln� pozice, kter� je�t� nebyla nav�t�vena. V�e si vysv�tl�me na prvn� z podpodm�nek: Nejprve se pt�me, jestli jsme v m�stnosti vpravo u� byli a potom, jestli jsou vpravo je�t� n�jak� m�stnosti (kv�li okraji textury). Pokud se �erven� slo�ka pixelu rovn� 255, podm�nka plat�. Okraj textury v dan�m sm�ru nalezneme tak� snadno.</p>

<p>To sam� vykon�me pro v�echny sm�ry a pokud nem�me kam j�t, mus�me vygenerovat novou pozici. V�e si zt��me t�m, �e chceme, abychom se objevili na pozici, kter� u� byla nav�t�vena. Pokud ne, vygenerujeme v cyklu dal�� sou�adnici. Mo�n� se pt�te, pro� hled�me nav�t�venou m�stnost? Proto�e nechceme spoustu mal�ch odd�len�ch ��st� bludi�t�, ale jedno obrovsk�. Dok�ete si to p�edstavit?</p>

<p>Zd� se v�m to moc slo�it�? Abychom udr�eli velikost k�du na minimu, nekontrolujeme, jestli je mx-2 men�� ne� nula a podobn� pro v�echny sm�ry. Pokud si p�ejete 100% o�et�en� chyb, modifikujte podm�nku tak, aby netestovala pam�, kter� u� nepat�� textu�e.</p>


<p class="src1"><span class="kom">// M�me kam j�t?</span></p>
<p class="src1">if (((tex_data[(((mx+2)+(width*my))*3)] == 255) || mx&gt;(width-4)) &amp;&amp; ((tex_data[(((mx-2)+(width*my))*3)] == 255) || mx&lt;2) &amp;&amp; ((tex_data[((mx+(width*(my+2)))*3)] == 255) || my&gt;(height-4)) &amp;&amp; ((tex_data[((mx+(width*(my-2)))*3)] == 255) || my&lt;2))</p>
<p class="src1">{</p>
<p class="src2">do</p>
<p class="src2">{</p>
<p class="src3">mx = int(rand() % (width / 2)) * 2;<span class="kom">// Nov� pozice</span></p>
<p class="src3">my = int(rand() % (height / 2)) * 2;</p>
<p class="src2">}</p>
<p class="src2">while (tex_data[((mx + (width * my)) * 3)] == 0);<span class="kom">// Hled� se nav�t�ven� m�stnost</span></p>
<p class="src1">}</p>

<p>Do prom�nn� dir vygenerujeme n�hodn� ��slo od nuly do t��, kter� vyjad�uje sm�r, kter�m se pokus�me j�t.</p>

<p class="src1">dir = int(rand() % 4);<span class="kom">// N�hodn� sm�r pohybu</span></p>

<p>Pokud se rovn� nule (sm�r doprava) a pokud nejsme na okraji bludi�t� (textury), zkontrolujeme, jestli u� byla m�stnost vpravo nav�t�vena. Pokud ne, ozna��me dve�e (pixel st�ny, ne m�stnosti) jako nav�t�ven� a projdeme do dal�� m�stnosti.</p>

<p class="src1">if ((dir == 0) &amp;&amp; (mx &lt;= (width-4)))<span class="kom">// Sm�r doprava; vpravo je m�sto</span></p>
<p class="src1">{</p>
<p class="src2">if (tex_data[(((mx+2) + (width*my)) * 3)] == 0)<span class="kom">// M�stnost vpravo je�t� nebyla nav�t�vena</span></p>
<p class="src2">{</p>
<p class="src3">UpdateTex(mx+1, my);<span class="kom">// Ozna�� pr�chod mezi m�stnostmi</span></p>
<p class="src3">mx += 2;<span class="kom">// Posunut� doprava</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Analogicky o�et��me v�echny dal�� sm�ry.</p>

<p class="src1">if ((dir == 1) &amp;&amp; (my &lt;= (height-4)))<span class="kom">// Sm�r dol�; dole je m�sto</span></p>
<p class="src1">{</p>
<p class="src2">if (tex_data[((mx + (width * (my+2))) * 3)] == 0)<span class="kom">// M�stnost dole je�t� nebyla nav�t�vena</span></p>
<p class="src2">{</p>
<p class="src3">UpdateTex(mx, my+1);<span class="kom">// Ozna�� pr�chod mezi m�stnostmi</span></p>
<p class="src3">my += 2;<span class="kom">// Posunut� dol�</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if ((dir == 2) &amp;&amp; (mx &gt;= 2))<span class="kom">// Sm�r doleva; vlevo je m�sto</span></p>
<p class="src1">{</p>
<p class="src2">if (tex_data[(((mx-2) + (width*my)) * 3)] == 0)<span class="kom">// M�stnost vlevo je�t� nebyla nav�t�vena</span></p>
<p class="src2">{</p>
<p class="src3">UpdateTex(mx-1, my);<span class="kom">// Ozna�� pr�chod mezi m�stnostmi</span></p>
<p class="src3">mx -= 2;<span class="kom">// Posunut� doleva</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if ((dir == 3) &amp;&amp; (my &gt;= 2))<span class="kom">// Sm�r nahoru; naho�e je m�sto</span></p>
<p class="src1">{</p>
<p class="src2">if (tex_data[((mx + (width * (my-2))) * 3)] == 0)<span class="kom">// M�stnost naho�e je�t� nebyla nav�t�vena</span></p>
<p class="src2">{</p>
<p class="src3">UpdateTex(mx, my-1);<span class="kom">// Ozna�� pr�chod mezi m�stnostmi</span></p>
<p class="src3">my -= 2;<span class="kom">// Posunut� nahoru</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Po p�esunut� se do nov� m�stnosti ji mus�me ozna�it.</p>

<p class="src1">UpdateTex(mx, my);<span class="kom">// Ozna�en� nov� m�stnosti</span></p>
<p class="src0">}</p>

<p>Vykreslov�n� za�neme netradi�n�. Pot�ebujeme zjistit velikost klientsk� oblasti okna, abychom mohli jednotliv� viewporty roztahovat korektn�. Deklarujeme objekt struktury obd�ln�ku a nagrabujeme do n�j sou�adnice okna. ���ku a v��ku spo��t�me jednoduch�m ode�ten�m.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">RECT rect;<span class="kom">// Struktura obd�ln�ku</span></p>
<p class="src"></p>
<p class="src1">GetClientRect(g_window-&gt;hWnd, &amp;rect);<span class="kom">// Grabov�n� rozm�r� okna</span></p>
<p class="src"></p>
<p class="src1">int window_width = rect.right - rect.left;<span class="kom">// ���ka okna</span></p>
<p class="src1">int window_height = rect.bottom - rect.top;<span class="kom">// V��ka okna</span></p>

<p>Texturu mus�me aktualizovat p�i ka�d�m p�ekreslen� sc�ny. Nejrychlej�� metodou je p��kaz glTexSubImage2D(), kter� namapuje jakoukoli ��st obr�zku na objekt ve sc�n� jako texturu. Prvn� parametr oznamuje, �e chceme pou��t 2D texturu. ��slo �rovn� detail� nastav�me na nulu a tak� nechceme ��dn� x ani y offset. ���ka a v��ka je ur�ena rozm�ry obr�zku. Ka�d� pixel se skl�d� z RGB slo�ek a data jsou ve form�tu bezznam�nkov�ch byt�. Posledn� parametr p�edstavuje ukazatel na za��tek dat.</p>

<p>Jak jsem ji� napsal, funkc� glTexSubImage2D() velmi rychle aktualizujeme texturu bez nutnosti jej�ho opakovan�ho smaz�n� a sestaven�. Tento p��kaz ji ale NEVYTV���!!! Mus�te ji tedy sestavit p�ed prvn� aktualizac�, v na�em p��pad� se jedn� o glTexImage2D() ve funkci Initialize().</p>

<p class="src1"><span class="kom">// Zvol� aktualizovanou texturu</span></p>
<p class="src1">glTexSubImage2D(GL_TEXTURE_2D, 0, 0, 0, width, height, GL_RGB, GL_UNSIGNED_BYTE, tex_data);</p>

<p>V�imn�te si n�sleduj�c�ho ��dku, je opravdu d�le�it�. Sma�eme j�m kompletn� celou sc�nu. Z toho plyne, �e nema�eme podsc�ny jednotliv�ch viewport� postupn�, ale V�ECHNY NAJEDNOU p�ed t�m, ne� cokoli vykresl�me. Tak� si v�imn�te, �e v tuto chv�li k vol�n� nep�id�v�me maz�n� depth bufferu. Ten naopak o�et��me u ka�d�ho viewportu zvlṻ.</p>

<p class="src1">glClear(GL_COLOR_BUFFER_BIT);<span class="kom">// Sma�e obrazovku</span></p>

<p>Chceme vykreslit �ty�i rozd�ln� viewporty, tak�e zalo��me cyklus od nuly do t��, pomoc� ��d�c� prom�nn� nastav�me barvu.</p>

<p class="src1">for (int loop = 0; loop &lt; 4; loop++)<span class="kom">// Proch�z� viewporty</span></p>
<p class="src1">{</p>
<p class="src2">glColor3ub(r[loop], g[loop], b[loop]);<span class="kom">// Barva</span></p>

<p>P�edt�m ne� cokoli vykresl�me, pot�ebujeme nastavit viewporty. Prvn� bude um�st�n vlevo naho�e. Na ose x tedy za��n� na nule a na ose y v polovin� okna. ���ku i v��ku nastav�me na polovinu rozm�r� okna. Pokud se nach�z�me ve fullscreenu s rozli�en�m obrazovky 1024x768, bude tento viewport za��nat na sou�adnic�ch [0; 384]. ���ka se bude rovna 512 a v��ka 384.</p>

<p class="src2">if (loop == 0)<span class="kom">// Prvn� sc�na</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Lev� horn� viewport, velikost poloviny okna</span></p>
<p class="src3">glViewport(0, window_height / 2, window_width / 2, window_height / 2);</p>

<p>Po definov�n� viewportu zvol�me projek�n� matici, resetujeme ji a nastav�me kolmou 2D projekci, kter� kompletn� zapl�uje cel� viewport. Lev� roh spo��v� na nule a prav� na polovin� velikosti okna (���ka viewportu). Spodn� bod je tak� polovinou okna a horn�mu p�ed�me nulu. Sou�adnice [0; 0] tedy odpov�d� lev�mu horn�mu rohu.</p>

<p class="src3">glMatrixMode(GL_PROJECTION);<span class="kom">// Projek�n� matice</span></p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset projek�n� matice</span></p>
<p class="src"></p>
<p class="src3">gluOrtho2D(0, window_width / 2, window_height / 2, 0);<span class="kom">// Pravo�hl� projekce</span></p>
<p class="src2">}</p>

<p>Druh� viewport le�� v prav�m horn�m rohu. Op�t zvol�me projek�n� matici a resetujeme ji. Tentokr�t nenastavujeme pravo�hlou, ale perspektivn� sc�nu.</p>

<p class="src2">if (loop == 1)<span class="kom">// Druh� sc�na</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Prav� horn� viewport, velikost poloviny okna</span></p>
<p class="src3">glViewport(window_width / 2, window_height / 2, window_width / 2, window_height / 2);</p>
<p class="src3">glMatrixMode(GL_PROJECTION);<span class="kom">// Projek�n� matice</span></p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset projek�n� matice</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Perspektivn� projekce</span></p>
<p class="src3">gluPerspective(45.0, (GLfloat)(width) / (GLfloat)(height), 0.1f, 500.0); </p>
<p class="src2">}</p>

<p>T�et� viewport um�st�me vpravo a �tvrt� vlevo dol�.</p>

<p class="src2">if (loop == 2)<span class="kom">// T�et� sc�na</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Prav� doln� viewport, velikost poloviny okna</span></p>
<p class="src3">glViewport(window_width / 2, 0, window_width / 2, window_height / 2);</p>
<p class="src3">glMatrixMode(GL_PROJECTION);<span class="kom">// Projek�n� matice</span></p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset projek�n� matice</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Perspektivn� projekce</span></p>
<p class="src3">gluPerspective(45.0, (GLfloat)(width) / (GLfloat)(height), 0.1f, 500.0); </p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">if (loop == 3)<span class="kom">// �tvrt� sc�na</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Lev� doln� viewport, velikost poloviny okna</span></p>
<p class="src3">glViewport(0, 0, window_width / 2, window_height / 2);</p>
<p class="src3">glMatrixMode(GL_PROJECTION);<span class="kom">// Projek�n� matice</span></p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset projek�n� matice</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Perspektivn� projekce</span></p>
<p class="src3">gluPerspective(45.0, (GLfloat)(width) / (GLfloat)(height), 0.1f, 500.0); </p>
<p class="src2">}</p>

<p>Zvol�me matici modelview, resetujeme ji a sma�eme hloubkov� buffer.</p>

<p class="src2">glMatrixMode(GL_MODELVIEW);<span class="kom">// Matice modelview</span></p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glClear(GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e hloubkov� buffer</span></p>

<p>Sc�na prvn�ho viewportu bude obsahovat ploch� otexturovan� obd�ln�k. Proto�e se nach�z�me v pravo�hl� projekci, nepot�ebujeme zad�vat sou�adnice na ose z. Objekty by se stejn� nezmen�ily. Vertex�m p�ed�me rozm�ry viewportu, kter� tud� bude kompletn� vypln�n.</p>

<p class="src2">if (loop == 0)<span class="kom">// Prvn� sc�na, bludi�t� p�es cel� viewport</span></p>
<p class="src2">{</p>
<p class="src3">glBegin(GL_QUADS);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex2i(window_width / 2, 0);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex2i(0, 0);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex2i(0, window_height / 2);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex2i(window_width / 2, window_height / 2);</p>
<p class="src3">glEnd();</p>
<p class="src2">}</p>

<p>Jako druh� objekt nakresl�me kouli. M�me zapnutou perspektivu, tak�e se nejd��ve p�esuneme o 14 jednotek do obrazovky. Potom objekt nato��me o dan� �hel na v�ech t�ech sou�adnicov�ch os�ch, zapneme sv�tla, vykresl�me kouli o polom�ru 4.0f jednotky a vypneme sv�tla.</p>

<p class="src2">if (loop == 1)<span class="kom">// Druh� sc�na, koule</span></p>
<p class="src2">{</p>
<p class="src3">glTranslatef(0.0f, 0.0f, -14.0f);<span class="kom">// P�esun do hloubky</span></p>
<p class="src"></p>
<p class="src3">glRotatef(xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(yrot, 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glRotatef(zrot, 0.0f, 0.0f, 1.0f);</p>
<p class="src"></p>
<p class="src3">glEnable(GL_LIGHTING);<span class="kom">// Zapne sv�tlo</span></p>
<p class="src3">gluSphere(quadric, 4.0f, 32, 32);<span class="kom">// Koule</span></p>
<p class="src3">glDisable(GL_LIGHTING);<span class="kom">// Vypne sv�tlo</span></p>
<p class="src2">}</p>

<p>T�et� viewport se velmi podob� prvn�mu, ale na rozd�l od n�j pou��v� perspektivu. P�esuneme obd�ln�k o dv� jednotky do hloubky a nato��me matici o 45 stup��. Horn� hrana se t�m p�dem vzd�l� a spodn� p�ibl��. Abychom je�t� p�idali n�jak� ten efekt, rotujeme j�m tak� na ose z.</p>

<p class="src2">if (loop == 2)<span class="kom">// T�et� sc�na, bludi�t� na rovin�</span></p>
<p class="src2">{</p>
<p class="src3">glTranslatef(0.0f, 0.0f, -2.0f);<span class="kom">// P�esun do hloubky</span></p>
<p class="src"></p>
<p class="src3">glRotatef(-45.0f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace o 45 stup��</span></p>
<p class="src3">glRotatef(zrot / 1.5f, 0.0f, 0.0f, 1.0f);<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src3">glBegin(GL_QUADS);</p>
<p class="src4">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, 0.0f);</p>
<p class="src4">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, 0.0f);</p>
<p class="src3">glEnd();</p>
<p class="src2">}</p>

<p>�tvrt�m a posledn�m objektem je v�lec, kter� se nach�z� sedm jednotek hluboko ve sc�n� a rotuje na v�ech t�ech os�ch. Zapneme sv�tla a potom se je�t� posuneme o dv� jednotky (o polovinu jeho d�lky) na ose z. Chceme, aby se ot��el okolo sv�ho st�edu a ne konce. Vykresl�me ho a vypneme sv�tla.</p>

<p class="src2">if (loop == 3)<span class="kom">// T�et� sc�na, v�lec</span></p>
<p class="src2">{</p>
<p class="src3">glTranslatef(0.0f,0.0f,-7.0f);<span class="kom">// P�esun do hloubky</span></p>
<p class="src"></p>
<p class="src3">glRotatef(-xrot/2,1.0f,0.0f,0.0f);<span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(-yrot/2,0.0f,1.0f,0.0f);</p>
<p class="src3">glRotatef(-zrot/2,0.0f,0.0f,1.0f);</p>
<p class="src"></p>
<p class="src3">glEnable(GL_LIGHTING);<span class="kom">// Zapne sv�tlo</span></p>
<p class="src3">glTranslatef(0.0f,0.0f,-2.0f);<span class="kom">// Vycentrov�n�</span></p>
<p class="src3">gluCylinder(quadric,1.5f,1.5f,4.0f,32,16);<span class="kom">// V�lec</span></p>
<p class="src3">glDisable(GL_LIGHTING);<span class="kom">// Vypne sv�tlo</span></p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Na konci vykreslov�n� flushneme renderovac� pipeline.</p>

<p class="src1">glFlush();<span class="kom">// Vypr�zdn�n� pipeline</span></p>
<p class="src0">}</p>

<p>Douf�m, �e tento tutori�l zodpov�d�l v�echny va�e ot�zky ohledn� v�ce viewport� v jednom okn�. Nyn� tak� zn�te jeden z mnoha zp�sob� generov�n� bludi�t� a um�te upravit texturu bez jej�ho komplikovan�ho maz�n� a znovuvytv��en�. Co v�c si p��t?</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson42.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson42_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson42.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson42.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Eshat@gmx.net">Eshat Cakar</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson42.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:robohog_64@hotmail.com">Victor Andr?e</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson42.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:evik@chaos.hu">Evik</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson42.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson42.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/extras/lesson42/lesson42_dual_window.zip">Lesson 42 - Multi Window</a> Code For This Lesson by Marcel Laverdet</li>
</ul>

<?FceImgNeHeVelky(42);?>
<?FceNeHeOkolniLekce(42);?>

<?
include 'p_end.php';
?>
