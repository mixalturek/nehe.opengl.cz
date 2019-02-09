<?
$g_title = 'CZ NeHe OpenGL - Lekce 19 - ��sticov� syst�my';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(19);?>

<h1>Lekce 19 - ��sticov� syst�my</h1>

<p class="nadpis_clanku">Cht�li jste u� n�kdy naprogramovat exploze, vodn� font�ny, planouc� hv�zdy a jin� skv�l� efekty, nicm�n� k�dov�n� ��sticov�ch syst�m� bylo bu� p��li� t�k� nebo jste v�bec nev�d�li, jak na to? V t�to lekci zjist�te, jak vytvo�it jednoduch�, ale dob�e vypadaj�c� ��sticov� syst�m. Extra p�id�me duhov� barvy a ovl�d�n� kl�vesnic�. Tak� se dozv�te, jak pomoc� triangle stripu jednodu�e vykreslovat velk� mno�stv� troj�heln�k�.</p>

<p>V t�to lekci vytvo��me t�m�� komplexn� ��sticov� syst�m. Jakmile jednou pochop�te, jak pracuj� zvl�dnete cokoli.</p>

<p>P�edem upozor�uji, �e dodne�ka jsem nikdy nic podobn�ho vytv��el. V�dy jsem si myslel, �e ty slavn� a "komer�n�" ��sticov� syst�my jsou hodn� komplexn�m kusem k�du.</p>

<p>Mo�n� nebudete v��it, kdy� p��i, �e tento k�d je 100% p�vodn�. Nem�l jsem p�ed sebou ��dn� technick� dokumentace. Onehdy jsem prost� p�em��lel a n�hle se mi v hlav� vygenerovala spousta n�pad�. Nam�sto uva�ov�n� o ��stici jako o pixelu p�esunuj�c�m se z bodu A do bodu B a d�laj�c�m to �i ono jsem ka�d� p�i�adil vlastn� objekt (strukturu) reaguj�c� na prost�ed� kolem. Zapouzd�uje �ivot, st�rnut�, barvu, rychlost, gravita�n� z�vislosti a dal�� vlastnosti.</p>

<p>Tak�e� a�koli program, podle m�, vypad� perfektn� a pracuje p�esn�, jak jsem cht�l, mo�n� nen� tou spr�vnou cestou k vytv��en� ��sticov�ch syst�m�. Osobn� jsem se nestaral, jak dob�e pracuje, ale ve sv�ch projektech jsem ho mohl bez probl�m� pou��vat. Jestli�e jste typem lid�, "��oural�", kte�� pot�ebuj� poznat spr�vnou cestu, zkuste str�vit hodiny prohled�v�n�m internetu. Toto bylo varov�n�.</p>

<p>Pou�ijeme k�d z lekce 1. Symbolick� konstanta definuje po�et vytv��en�ch ��stic. Rainbow zap�n�/vyp�n� cyklov�n� mezi duhov�mi barvami. Sp a rp p�edch�zej� opakov�n� k�du p�i del��m stisku mezern�ku a enteru.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">#define MAX_PARTICLES 1000<span class="kom">// Po�et vytv��en�ch ��stic</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Priv�tn� GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trval� Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na�eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>
<p class="src"></p>
<p class="src0">bool rainbow = true;<span class="kom">// Duhov� efekt?</span></p>
<p class="src0">bool sp;<span class="kom">// Stisknut� mezern�k?</span></p>
<p class="src0">bool rp;<span class="kom">// Stisknut� enter?</span></p>

<p>N�sleduj� pomocn� prom�nn�. Slowdown kontroluje rychlost pohybu ��stic (��m vy��� ��slo, t�m pomaleji se pohybuj�). Xspeed a yspeed ovliv�uj� rychlost na jednotliv�ch os�ch. Jsou pouze jedn�m faktorem implementovan�m kv�li ovl�d�n� kl�vesnic�. Zoom pou��v�me pro p�esun do/ze sc�ny.</p>

<p class="src0">float slowdown=2.0f;<span class="kom">// Zpomalen� ��stic</span></p>
<p class="src0">float xspeed;<span class="kom">// Z�kladn� rychlost na ose x</span></p>
<p class="src0">float yspeed;<span class="kom">// Z�kladn� rychlost na ose y</span></p>
<p class="src0">float zoom=-40.0f;<span class="kom">// Zoom</span></p>

<p>Loop vyu��v�me p�edev��m jako prom�nnou cyklu, ve kter�ch inicializujeme a vykreslujeme ��stice. Col vych�z� ze slova color a zna�� barvu. Pomoc� �asova�e delay p�i zapnut�m duhov�m m�du cyklujeme mezi barvami.</p>

<p>Posledn� prom�nn� je klasick� textura. Rozhodl jsem se pro ni, proto�e vypadaj� mnohem l�pe ne� jednobarevn� body. Tak� si m��ete vytvo�it texturu ohn�, sn�hu, jak�hokoli objektu.</p>

<p class="src0">GLuint loop;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src0">GLuint col;<span class="kom">// Vybran� barva</span></p>
<p class="src0">GLuint delay;<span class="kom">// Zpo�d�n� pro duhov� efekt</span></p>
<p class="src0">GLuint texture[1];<span class="kom">// Ukl�d� texturu</span></p>

<p>N�sleduje struktura definuj�c� vlastnosti ��stic. Obsahuje spoustu atribut�, tak�e si je pop��eme. Pokud bude active rovno true ��stice bude aktivn� a false logicky zna�� neaktivnost. V tomto programu se tato vlastnost nepou��v�, ale n�kdy jindy by mohla b�t u�ite�n�. Life a fade definuj�, jak dlouho a jak jasn� bude ��stice zobrazena. Od �ivota (life) budeme ode��tat st�rnut� (fade). Na za��tku je inicializujeme na random, stejn� jako t�m�� v�echny ostatn� vlastnosti.</p>

<p class="src0">typedef struct<span class="kom">// Vytvo�� stukturu pro ��stici</span></p>
<p class="src0">{</p>
<p class="src1">bool active;<span class="kom">// Aktivn�?</span></p>
<p class="src1">float life;<span class="kom">// �ivot</span></p>
<p class="src1">float fade;<span class="kom">// Rychlost st�rnut�</span></p>
<p class="src"></p>
<p class="src1">float r;<span class="kom">// �erven� slo�ka barvy</span></p>
<p class="src1">float g;<span class="kom">// Zelen� slo�ka barvy</span></p>
<p class="src1">float b;<span class="kom">// Modr� slo�ka barvy</span></p>
<p class="src"></p>
<p class="src1">float x;<span class="kom">// X Pozice</span></p>
<p class="src1">float y;<span class="kom">// Y Pozice</span></p>
<p class="src1">float z;<span class="kom">// Z Pozice</span></p>
<p class="src"></p>
<p class="src1">float xi;<span class="kom">// X sm�r a rychlost</span></p>
<p class="src1">float yi;<span class="kom">// Y sm�r a rychlost</span></p>
<p class="src1">float zi;<span class="kom">// Z sm�r a rychlost</span></p>

<p>N�sleduj�c� prom�nn� ur�uj� p�soben� gravitace (ka�d� ve sv� ose). Kladn� xg zna�� p�soben� doprava, z�porn� doleva. Sm�ry jsou analogick� ke sm�r�m sou�adnicov�ch os.</p>

<p class="src1">float xg;<span class="kom">// X gravitace</span></p>
<p class="src1">float yg;<span class="kom">// Y gravitace</span></p>
<p class="src1">float zg;<span class="kom">// Z gravitace</span></p>
<p class="src"></p>
<p class="src0">} particles;<span class="kom">// Struktura ��stice</span></p>

<p>D�le deklarujeme pole datov�ho typu particles (na�e struktura) o velikosti MAX_PARTICLES a jm�nu particle.</p>

<p class="src0">particles particle[MAX_PARTICLES];<span class="kom">// Pole ��stic</span></p>

<p>Inicializac� pole barev si vytvo��me barevnou paletu. Ka�d� z dvan�cti polo�ek obsahuje 3 RGB slo�ky v rozmez� od �erven� do fialov�.</p>

<p class="src0">static GLfloat colors[12][3]=<span class="kom">// Barevn� paleta</span></p>
<p class="src0">{</p>
<p class="src1">{1.0f,0.5f,0.5f},{1.0f,0.75f,0.5f},{1.0f,1.0f,0.5f},{0.75f,1.0f,0.5f},</p>
<p class="src1">{0.5f,1.0f,0.5f},{0.5f,1.0f,0.75f},{0.5f,1.0f,1.0f},{0.5f,0.75f,1.0f},</p>
<p class="src1">{0.5f,0.5f,1.0f},{0.75f,0.5f,1.0f},{1.0f,0.5f,1.0f},{1.0f,0.5f,0.75f}</p>
<p class="src0">};</p>

<p>Do inicializa�n�ho k�du jsem oproti k�du z prvn� lekce p�idal loading textury, nastaven� blendingu a zad�n� po��te�n�ch hodnot ��stic.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echna nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje textury</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povol�me jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f,0.0f,0.0f,0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne hloubkov� testov�n�</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE);<span class="kom">// Typ blendingu</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT,GL_NICEST);<span class="kom">// Perspektiva</span></p>
<p class="src1">glHint(GL_POINT_SMOOTH_HINT,GL_NICEST);<span class="kom">// Jemnost bod�</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D,texture[0]);<span class="kom">// Vybere texturu</span></p>

<p>Inicializujeme jednotliv� ��stice. Za�neme aktivov�n�m. Pamatujte si, �e naktivn� nezobrazujeme a neaktualizujeme. Potom je o�iv�me. Nebyl jsem si jist�, zda je zhas�n�n� (zpr�hled�ov�n�) ��stic z�visl� na zkracov�n� �ivota, spr�vnou cestou. Nicm�n� pracuje skv�le, tak co :-) Maxim�ln� �ivot 1.0f d�v� nejjasn�j�� vykreslen� (viz. blending).</p>

<p class="src1">for (loop=0;loop&lt;MAX_PARTICLES;loop++)<span class="kom">// Inicializace ��stic</span></p>
<p class="src1">{</p>
<p class="src2">particle[loop].active=true;<span class="kom">// Aktivace</span></p>
<p class="src2">particle[loop].life=1.0f;<span class="kom">// O�iven�</span></p>

<p>Na randomovou hodnotu nastav�me rychlost st�rnut� a postupn�ho zhas�n�n�. Ka�d�m vykreslen�m se �ivot (life) zkracuje o st�rnut� (fade). Hodnotu 0 a� 99 vyd�l�me 1000 a t�m z�sk�me velmi mal� ��slo. Aby rychlost st�rnut� nikdy nebyla nulov�, p�i�teme 0,003.</p>

<p class="src2">particle[loop].fade=float(rand()%100)/1000.0f+0.003f;<span class="kom">// Rychlost st�rnut�</span></p>

<p>Nastav�me barvu ��stic na n�kterou z v��e vytvo�en� palety. Matematika je jednoduch�: vezmeme ��d�c� prom�nnou cyklu a vyn�sob�me ji pod�lem po�tu barev s celkov�m po�tem ��stic. Nap��klad p�i prvn�m pr�chodu bude loop = 0, po dosazen� a v�po�tu z�sk�me 0*(12/1000)=0. P�i posledn�m pr�chodu (loop = po�et ��stic -1 = 999) vyjde 999*(12/1000)=11,988. Proto�e p�ed�v�me int, v�sledek se o�e�e na 11, co� je posledn� barva v palet�.</p>

<p class="src2">particle[loop].r=colors[loop*(12/MAX_PARTICLES)][0];<span class="kom">// �erven�</span></p>
<p class="src2">particle[loop].g=colors[loop*(12/MAX_PARTICLES)][1];<span class="kom">// Zelen�</span></p>
<p class="src2">particle[loop].b=colors[loop*(12/MAX_PARTICLES)][2];<span class="kom">// Modr�</span></p>

<p>Inicializujeme sm�r a rychlost pohybu ��stic. V�po�et provedeme op�t randomem, kter� pro po��te�n� efekt exploze n�sob�me deseti. Dostaneme kladn� nebo z�porn� ��sla ur�uj�c� sm�r a rychlost pohybu v jednotliv�ch os�ch.</p>

<p class="src2">particle[loop].xi=float((rand()%50)-26.0f)*10.0f;<span class="kom">// Rychlost a sm�r pohybu na ose x</span></p>
<p class="src2">particle[loop].yi=float((rand()%50)-25.0f)*10.0f;<span class="kom">// Rychlost a sm�r pohybu na ose y</span></p>
<p class="src2">particle[loop].zi=float((rand()%50)-25.0f)*10.0f;<span class="kom">// Rychlost a sm�r pohybu na ose z</span></p>

<p>Nakonec nastav�me gravita�n� p�soben�. V�t�inou gravitace strh�v� v�ci dol�, ale ta na�e bude moci p�sobit v�emi sm�ry. Na za��tku ov�em klasicky dol� (yg = - 0,8).</p>

<p class="src2">particle[loop].xg=0.0f;<span class="kom">// Gravitace na ose x</span></p>
<p class="src2">particle[loop].yg=-0.8f;<span class="kom">// Gravitace na ose y</span></p>
<p class="src2">particle[loop].zg=0.0f;<span class="kom">// Gravitace na ose z</span></p>
<p class="src1">}</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>V dal�� funkci se pokus�me o vykreslov�n�, zajist�me p�soben� gravitace ap. Matici ModelView resetujeme pouze jednou a to na za��tku. Pozici ��stic tedy nebudeme ur�ovat slo�it�mi posuny a rotacemi, ale pouze sou�adnicemi p�ed�van�mi funkci glVertex3f().</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1"></p>
<p class="src1">for (loop=0;loop&lt;MAX_PARTICLES;loop++)<span class="kom">// Cyklus proch�z� ka�dou ��stici</span></p>
<p class="src1">{</p>

<p>Prvn� v�c� je zkontrolov�n�, zda je ��stice aktivn�, pokud ne nebudeme ji aktualizovat ani vykreslovat. Nicm�n� v tomto programu budou aktivn� v�echny.</p>

<p class="src2">if (particle[loop].active)<span class="kom">// Pokud je ��stice aktivn�</span></p>
<p class="src2">{</p>

<p>N�sleduj�c� t�i prom�nn� x,y,z jsou sp��e pomocn� k zp�ehledn�n� k�du. V�imn�te si, �e k pozici na ose z p�i��t�me zoom, ��m� m��eme jednodu�e m�nit hloubku v obrazovce.</p>

<p class="src3">float x=particle[loop].x;<span class="kom">// x pozice</span></p>
<p class="src3">float y=particle[loop].y;<span class="kom">// y pozice</span></p>
<p class="src3">float z=particle[loop].z+zoom;<span class="kom">// z pozice + zoom</span></p>

<p>D�le obarv�me ��stici jej� barvou. Jako alfa kan�l (pr�hlednost) s v�hodou vyu�ijeme �ivot, kter� nab�v� hodnot od 1.0f (pln�) do 0.0f (smrt). Postupn�m st�rnut�m se tedy st�v� pr�hledn�j�� a� vybledne docela.</p>

<p class="src3"><span class="kom">// Barva ��stice</span></p>
<p class="src3">glColor4f(particle[loop].r,particle[loop].g,particle[loop].b,particle[loop].life);</p>

<p>M�me pozici i barvu, tak�e p�ejdeme k vykreslen�. P�vodn� jsem cht�l pou��t otexturovan� �tverec, ale pak jsem se rozhodl pro otexturovan� &quot;triangle strip&quot;. V�t�ina grafick�ch karet renderuje troj�heln�ky mnohem rychleji ne� �tverce, proto�e se �ty��heln�ky �asto konvertuj� na dva troj�heln�ky. K vykreslen� klasick�m zp�sobem bychom pot�ebovali 6 r�zn�ch bod�, pou�it�m triangle stripu sta�� pouze �ty�i. Nejprve tedy po��d�me OpenGL o vykreslen� triangle stripu.</p>

<p class="src3">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Vytvo�� obd�ln�k pomoc� triangle stripu</span></p>

<p>Triangle strip vykresluje s�rii troj�heln�k� u�it�m bod� V0, V1, V2, potom V2, v1, V3 (v�imn�te si po�ad�), d�le V2, V3, V4 atd. T�mto po�ad�m se zajist�, �e se v�echny vykresl� se stejnou orientac� (viz. po�ad� zad�v�n� vrchol�), kter� je d�le�it� u n�kter�ch operac�, nap�. cullingu. Aby se n�co vykreslilo mus� b�t zad�ny alespo� t�i body. Pro pou�it� triangle stripu existuj� dva dobr� d�vody. Prvn�: po inicializaci prvn�ho troj�heln�ku sta�� pro ka�d� nov� troj�heln�k jenom jeden bod, kter� bude skombinov�n s body toho minul�ho. Druh�: odstran�n�m ��sti k�du program pob�� rychleji. Zdrojov� k�d bude krat�� a p�ehledn�j��. Po�et vykreslen�ch troj�heln�ku vykreslen�ch na monitor bude o dva men�� ne� po�et zadan�ch bod�.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_19_1.gif" width="128" height="128" alt="triangle_strip" /></div>

<p class="src4">glTexCoord2d(1,1); glVertex3f(x+0.5f,y+0.5f,z);<span class="kom">// Horn� prav�</span></p>
<p class="src4">glTexCoord2d(0,1); glVertex3f(x-0.5f,y+0.5f,z);<span class="kom">// Horn� lev�</span></p>
<p class="src4">glTexCoord2d(1,0); glVertex3f(x+0.5f,y-0.5f,z);<span class="kom">// Doln� prav�</span></p>
<p class="src4">glTexCoord2d(0,0); glVertex3f(x-0.5f,y-0.5f,z);<span class="kom">// Doln� lev�</span></p>
<p class="src"></p>
<p class="src3">glEnd();<span class="kom">// Ukon�� triangle strip</span></p>

<p>Po vykreslen� p�ich�z� na �adu aktualizace ��stice. Matematika m��e vypadat stra�n�, ale je kr�sn� jednoduch�. Vezmeme pozici na konkr�tn� ose a p�i�teme k n� pohyb na t�to ose vyd�len� slowdown kr�t tis�c. Nap�. pokud bude ��stice uprost�ed obrazovky (0,0,0), pohyb xi 10 a slowdown 1 p�esuneme ji o 10/(1*1000) - do bodu 0.01f na ose x. Pokud bychom inkrementovali slowdown na 2 p�esuneme se pouze na 0.005f. Toto je tak� d�vod n�soben� startovn� hodnoty des�tkou. Body se po spu�t�n� programu pohybuj� mnohem rychleji, tak�e vytvo�� dojem exploze.</p>

<p class="src3">particle[loop].x+=particle[loop].xi/(slowdown*1000);<span class="kom">// Pohyb na ose x</span></p>
<p class="src3">particle[loop].y+=particle[loop].yi/(slowdown*1000);<span class="kom">// Pohyb na ose y</span></p>
<p class="src3">particle[loop].z+=particle[loop].zi/(slowdown*1000);<span class="kom">// Pohyb na ose z</span></p>

<p>Po spo��t�n� pohybu aplikujeme gravita�n� p�soben�. Doc�l�me toho p�i�ten�m "gravita�n� s�ly" k rychlosti pohybu. �ekn�me, �e rychlost pohybu je 10 a gravitace o velikosti 1 p�sob� v opa�n�m sm�ru. Ka�d�m p�ekreslen�m se rychlost pohybu dekrementov�n�m zpomal�. Po deseti p�ekreslen�ch ��stice zm�n� sm�r.</p>

<p class="src3">particle[loop].xi+=particle[loop].xg;<span class="kom">// Gravita�n� p�soben� na ose x</span></p>
<p class="src3">particle[loop].yi+=particle[loop].yg;<span class="kom">// Gravita�n� p�soben� na ose y</span></p>
<p class="src3">particle[loop].zi+=particle[loop].zg;<span class="kom">// Gravita�n� p�soben� na ose z</span></p>

<p>Sn���me hodnotu �ivota o st�rnut�. Kdybychom toto ned�lali nikdy by ��stice nesho�ela. Ka�d� m� nastavenu jinou rychlost st�rnut�, tud�� nezem�ou ve stejn� �asov� okam�ik.</p>

<p class="src3">particle[loop].life-=particle[loop].fade;<span class="kom">// Sn��� �ivot o st�rnut�</span></p>

<p>V t�to chv�li mus�me otestovat, zda je po zest�rnut� st�le na�ivu.</p>

<p class="src3">if (particle[loop].life&lt;0.0f)<span class="kom">// Pokud zem�ela</span></p>
<p class="src3">{</p>

<p>Pokud zem�ela &quot;reinkarnujeme&quot; ji nastaven�m pln�ho �ivota a nov� n�hodn� rychlosti st�rnut�.</p>

<p class="src4">particle[loop].life=1.0f;<span class="kom">// Nov� �ivot</span></p>
<p class="src4">particle[loop].fade=float(rand()%100)/1000.0f+0.003f;<span class="kom">// N�hodn� st�rnut�</span></p>

<p>Resetujeme jej� pozici na st�ed obrazovky.</p>

<p class="src4">particle[loop].x=0.0f;<span class="kom">// Vycentrov�n� doprost�ed obrazovky</span></p>
<p class="src4">particle[loop].y=0.0f;<span class="kom">// Vycentrov�n� doprost�ed obrazovky</span></p>
<p class="src4">particle[loop].z=0.0f;<span class="kom">// Vycentrov�n� doprost�ed obrazovky</span></p>

<p>Ur��me novou rychlost a vlastn� i sm�r. V�imn�te si, �e jsem zv�t�il maxim�ln� a minim�ln� rychlost z 50 na 60 oproti funkci InitGL(), ale tentokr�t v�sledek nen�sob�m deseti. U� nechceme ��dn� exploze, ale pomalej�� pohyb. Z d�vodu ovl�d�n� kl�vesnic� p�i��t�me k hodnot� i glob�ln� rychlost (xspeed, yspeed).</p>

<p class="src4">particle[loop].xi=xspeed+float((rand()%60)-32.0f);<span class="kom">// Nov� rychlost a sm�r</span></p>
<p class="src4">particle[loop].yi=yspeed+float((rand()%60)-30.0f);<span class="kom">// Nov� rychlost a sm�r</span></p>
<p class="src4">particle[loop].zi=float((rand()%60)-30.0f);<span class="kom">// Nov� rychlost a sm�r</span></p>

<p>��stici p�i�ad�me tak� novou barvu. Prom�nn� col ukl�d� ��slo 0 a� 11 (12 barev). Pomoc� n� vyb�r�me �ervenou, zelenou a modrou intenzitu z palety vytvo�en� na za��tku programu.</p>

<p class="src4">particle[loop].r=colors[col][0];<span class="kom">// Vybere barvu z palety</span></p>
<p class="src4">particle[loop].g=colors[col][1];<span class="kom">// Vybere barvu z palety</span></p>
<p class="src4">particle[loop].b=colors[col][2];<span class="kom">// Vybere barvu z palety</span></p>
<p class="src3">}</p>

<p>N�sleduj�c� k�d aktualizuje p�soben� gravitace. Stisknut�m 8 na kl�vesnici zv�t��me yg (y gravitaci) a ��stice bude ta�ena vzh�ru. Tato testov�n� jsou vlo�ena do vykreslov�n� z d�vodu zjednodu�en�. Kdyby bylo um�st�no n�kam jinam museli bychom vytvo�it nov� cyklus d�laj�c� �pln� stejnou pr�ci. Podobn� postupy poskytuj� skv�l� mo�nosti. Nap�. se m��ete pokus�te o proud vody v�trem vyst�ikuj�c� p��mo vzh�ru. P�id�n�m gravitace p�sob�c� dol� vytvo��te font�nu vody.</p>

<p class="src3"><span class="kom">// Pokud je stisknuta 8 a y gravitace je men�� ne� 1.5</span></p>
<p class="src3">if (keys[VK_NUMPAD8] &amp;&amp; (particle[loop].yg&lt;1.5f)) particle[loop].yg+=0.01f;</p>

<p class="src3"><span class="kom">// Pokud je stisknuta 2 a y gravitace je men�� ne� -1.5</span></p>
<p class="src3">if (keys[VK_NUMPAD2] &amp;&amp; (particle[loop].yg&gt;-1.5f)) particle[loop].yg-=0.01f;</p>
<p class="src3"><span class="kom">// Pokud je stisknuta 6 a x gravitace je men�� ne� 1.5</span></p>
<p class="src3">if (keys[VK_NUMPAD6] &amp;&amp; (particle[loop].xg&lt;1.5f)) particle[loop].xg+=0.01f;</p>
<p class="src3"><span class="kom">// Pokud je stisknuta 4 a x gravitace je men�� ne� -1.5</span></p>
<p class="src3">if (keys[VK_NUMPAD4] &amp;&amp; (particle[loop].xg&gt;-1.5f)) particle[loop].xg-=0.01f;</p>

<p>Pro radost p�ip��eme malou "vychyt�vku". M�j bratr si myslel, �e �vodn� v�buch je skv�l� efekt. Stisknut�m kl�vesy TAB se v�echny ��stice resetuj� do centra obrazovky. Rychlost se vyn�sob� deseti a t�m vytvo�� explozi.</p>

<p class="src3">if (keys[VK_TAB])<span class="kom">// Zp�sob� v�buch</span></p>
<p class="src3">{</p>
<p class="src4">particle[loop].x=0.0f;<span class="kom">// Vycentrov�n� na st�ed obrazovky</span></p>
<p class="src4">particle[loop].y=0.0f;<span class="kom">// Vycentrov�n� na st�ed obrazovky</span></p>
<p class="src4">particle[loop].z=0.0f;<span class="kom">// Vycentrov�n� na st�ed obrazovky</span></p>
<p class="src4">particle[loop].xi=float((rand()%50)-26.0f)*10.0f;<span class="kom">// N�hodn� rychlost</span></p>
<p class="src4">particle[loop].yi=float((rand()%50)-25.0f)*10.0f;<span class="kom">// N�hodn� rychlost</span></p>
<p class="src4">particle[loop].zi=float((rand()%50)-25.0f)*10.0f;<span class="kom">// N�hodn� rychlost</span></p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">return TRUE;<span class="kom">// V�echno OK</span></p>
<p class="src0">}</p>

<p>Funkci WinMain nap��i celou, proto�e je v n� celkem dost zm�n.</p>

<p class="src0">int WINAPI WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPSTR lpCmdLine, int nCmdShow)</p>
<p class="src0">{</p>
<p class="src1">MSG msg;</p>
<p class="src1">BOOL done=FALSE;</p>
<p class="src"></p>
<p class="src1">if (MessageBox(NULL,&quot;Would You Like To Run In Fullscreen Mode?&quot;, &quot;Start FullScreen?&quot;, MB_YESNO|MB_ICONQUESTION) == IDNO)</p>
<p class="src1">{</p>
<p class="src2">fullscreen=FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (!CreateGLWindow(&quot;NeHe's Particle Tutorial&quot;,640,480,16,fullscreen))</p>
<p class="src1">{</p>
<p class="src2">return 0;</p>
<p class="src1">}</p>

<p>Toto je prvn� d�le�it� zm�na. P�i rozhodnut� u�ivatele pou��t fullscreen zm�n�me slowdown ze 2.0f na 1.0f. Tato �prava nen� a� tak d�le�it� - lze ji vypustit. Slou�� k urychlen� fullscreenu - moje grafick� karta pracuje v okn� trochu rychleji. Nev�m pro�.</p>

<p class="src1">if (fullscreen)</p>
<p class="src1">{</p>
<p class="src2">slowdown=1.0f;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">while(!done)</p>
<p class="src1">{</p>
<p class="src2">if (PeekMessage(&amp;msg,NULL,0,0,PM_REMOVE))</p>
<p class="src2">{</p>
<p class="src3">if (msg.message==WM_QUIT)</p>
<p class="src3">{</p>
<p class="src4">done=TRUE;</p>
<p class="src3">}</p>
<p class="src3">else</p>
<p class="src3">{</p>
<p class="src4">TranslateMessage(&amp;msg);</p>
<p class="src4">DispatchMessage(&amp;msg);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">if ((active &amp;&amp; !DrawGLScene()) || keys[VK_ESCAPE])</p>
<p class="src3">{</p>
<p class="src4">done=TRUE;</p>
<p class="src3">}</p>
<p class="src3">else</p>
<p class="src3">{</p>
<p class="src4">DrawGLScene();</p>
<p class="src4">SwapBuffers(hDC);</p>

<p>O�et��me vstup z kl�vesnice (+, -, PageUp, PageDown)</p>

<p class="src4">if (keys[VK_ADD] &amp;&amp; (slowdown&gt;1.0f)) slowdown-=0.01f;<span class="kom">// Urychlen� ��stic</span></p>
<p class="src4">if (keys[VK_SUBTRACT] &amp;&amp; (slowdown&lt;4.0f)) slowdown+=0.01f;<span class="kom">// Zpomalen� ��stic</span></p>
<p class="src4">if (keys[VK_PRIOR])zoom+=0.1f;<span class="kom">// P�ibl��en� pohledu</span></p>
<p class="src4">if (keys[VK_NEXT])zoom-=0.1f;<span class="kom">// Odd�len� pohledu</span></p>

<p>V n�sleduj�c�ch ��dc�ch testujeme stisk enteru, abychom zapnuli cyklov�n� barvami.</p>

<p class="src4">if (keys[VK_RETURN] &amp;&amp; !rp)<span class="kom">// Stisk enteru</span></p>
<p class="src4">{</p>
<p class="src5">rp=true;<span class="kom">// Nastav� p��znak</span></p>
<p class="src5">rainbow = !rainbow;<span class="kom">// Zapne/vypne duhov� efekt</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys[VK_RETURN]) rp=false;<span class="kom">// Po uvoln�n� vypne p��znak</span></p>

<p>Operace p�i tisku mezern�ku mohou b�t trochu matouc�. Stejn� jako p�i enteru otestujeme, zda je zapnut duhov� efekt. Pokud je, pod�v�me e jestli je hodnota po��tadla counter v�t�� ne� 25. Pou��v� se ke zm�n� barvy cel�ch skupin ��stic. Pokud by se zm�nila barva p�i ka�d�m framu v�echny ��stice by se obarvily jinak. Vytvo�en�m zpo�d�n� stihneme obarvit stejnou barvou v�ce ��stic.</p>

<p class="src4">if ((keys[' '] &amp;&amp; !sp) || (rainbow &amp;&amp; (delay&gt;25)))<span class="kom">// Mezern�k nebo duhov� efekt</span></p>
<p class="src4">{</p>

<p>Pokud je stisknut mezern�k vypne se duhov� efekt. Kdybychom ho nedeaktivovali, tak by se dokola m�nily barvy dokud by nebyl stisknut enter. D�v� smysl, �e pokud �lov�k bouch� do mezern�ku nam�sto do enteru, tak chce barvami proch�zet s�m.</p>

<p class="src5">if (keys[' '])rainbow=false;<span class="kom">// Pokud je stisknut vypne se duhov� m�d</span></p>

<p>Pokud je mezern�k stisknut nebo je zapnut duhov� m�d a zpo�d�n� je v�t�� ne� 25, p�i�azen�m true do sp ozn�m�me po��ta�i, �e byl stisknut. Pot� nastav�me delay na nulu, tak�e se m��e znovu po��tat do 25. Nakonec inkrementujeme barvu na dal�� v palet�.</p>

<p class="src5">sp=true;<span class="kom">// Ozn�m� programu, �e byl stisknut mezern�k</span></p>
<p class="src5">delay=0;<span class="kom">// Resetuje zpo�d�n� duhov�ch barev</span></p>
<p class="src5">col++;<span class="kom">// Zm�n� barvu ��stice</span></p>

<p>Proto�e m�me pouze 12 barev mus�me zamezit p�ete�en� pole a n�sledn� zhroucen� programu.</p>

<p class="src5">if (col&gt;11) col=0;<span class="kom">// Proti p�ete�en� pole</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (!keys[' ']) sp=false;<span class="kom">// Uvoln�n� mezern�ku</span></p>

<p>Definujeme ovl�d�n� ��stic. Na za��tku programu jsme deklarovali dv� prom�nn� rychlosti (xspeed, yspeed). Kdy� ��stice vyho�� (zem�e) p�i�ad�me j� novou rychlost z�visej�c� na t�chto prom�nn�ch. M��eme ovliv�ovat jejich sm�r. ��dek dole testuje stisk �ipky nahoru. V takov�m p��pad� yspeed inkrementujeme. ��stice se bude pohybovat nahoru. Max rychlost je omezena na 200, v�t�� u� nevypad� dob�e. Analogick�m principem pracuje i ovl�d�n� ostatn�mi �ipkami.</p>

<p class="src4">if (keys[VK_UP] &amp;&amp; (yspeed&lt;200)) yspeed+=1.0f;<span class="kom">// �ipka nahoru</span></p>
<p class="src4">if (keys[VK_DOWN] &amp;&amp; (yspeed&gt;-200)) yspeed-=1.0f;<span class="kom">// �ipka dol�</span></p>
<p class="src4">if (keys[VK_RIGHT] &amp;&amp; (xspeed&lt;200)) xspeed+=1.0f;<span class="kom">// �ipka doprava</span></p>
<p class="src4">if (keys[VK_LEFT] &amp;&amp; (xspeed&gt;-200)) xspeed-=1.0f;<span class="kom">// �ipka doleva</span></p>

<p>Zb�v� inkrementovat zpo�d�n� delay, pou�it� pro rychlost zm�n barev. Ostatn� k�d zn�te z minul�ch lekc�.</p>

<p class="src4">delay++;<span class="kom">// Inkrementace zpo�d�n� duhov�ho efektu</span></p>
<p class="src"></p>
<p class="src4">if (keys[VK_F1])</p>
<p class="src4">{</p>
<p class="src5">keys[VK_F1]=FALSE;</p>
<p class="src5">KillGLWindow();</p>
<p class="src5">fullscreen = !fullscreen;</p>
<p class="src5">if (!CreateGLWindow(&quot;NeHe's Particle Tutorial&quot;,640,480,16,fullscreen))</p>
<p class="src5">{</p>
<p class="src6">return 0;</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">KillGLWindow();</p>
<p class="src1">return (msg.wParam);</p>
<p class="src0">}</p>

<p>V t�to lekci jsem se pokou�el o vysv�tlen� jednoduch�ho, ale p�sobiv�ho ��sticov�ho syst�mu. Jeho nejv�hodn�j�� pou�it� spo��v� ve vytvo�en� efekt� typu ohn�, vody, sn�hu, exploz�, hv�zd a spousty dal��ch. Jednoduch�m modifikov�n�m k�du lze snadno naprogramovat zcela nov� efekt.</p>

<p>D�kuji Richardu Nutmanovi za upozorn�n�, �e by bylo v�hodn�j�� umis�ovat ��stice pou�it�m glVertex3f() nam�sto resetov�n�m matice a slo�it�mi translacemi. Ob� metody vypadaj� stejn�, ale jeho verze sni�uje zat��en� po��ta�e. Program b�� rychleji.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson19.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson19_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson19.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson19.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson19.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson19.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson19.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson19.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson19.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson19.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:christop@fhw.gr">Dimitrios Christopoulos</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson19.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson19.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson19.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson19.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:kjrockot@home.com">Ken Rockot</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson19.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:miqster@gmx.net">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson19.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson19.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:OBorstad@Bowesnet.com">Owen Borstad</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson19.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson19.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:chris.j84@free.fr">Christophe</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson19.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson19.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:fredo@studenten.net">Edo</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson19.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(19);?>
<?FceNeHeOkolniLekce(19);?>

<?
include 'p_end.php';
?>
