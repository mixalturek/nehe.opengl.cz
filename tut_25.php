<?
$g_title = 'CZ NeHe OpenGL - Lekce 25 - Morfov�n� objekt� a jejich nahr�v�n� z textov�ho souboru';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(25);?>

<h1>Lekce 25 - Morfov�n� objekt� a jejich nahr�v�n� z textov�ho souboru</h1>

<p class="nadpis_clanku">V t�to lekci se nau��te, jak nahr�t sou�adnice vrchol� z textov�ho souboru a plynulou transformaci z jednoho objektu na druh�. Nezam���me se ani tak na grafick� v�stup jako sp�e na efekty a pot�ebnou matematiku okolo. K�d m��e b�t velice jednodu�e modifikov�n k vykreslov�n� linkami nebo polygony.</p>

<p>Poznamenejme, �e ka�d� objekt by m�l b�t seskl�d�n ze stejn�ho po�tu bod� jako v�echny ostatn�. Je to sice hodn� omezuj�c� po�adavek, ale co se d� d�lat - chceme p�ece, aby zm�ny vypadaly dob�e. Za�neme vlo�en�m hlavi�kov�ch soubor�. Tentokr�t nepou��v�me textury, tak�e se obejdeme bez glaux.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavi�kov� soubor pro matematickou knihovnu</span></p>
<p class="src"></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Priv�tn� GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trval� Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na�eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>Po deklarov�n� v�ech standardn�ch prom�nn�ch p�id�me nov�. Rot ukl�daj� aktu�ln� �hel rotace na jednotliv�ch sou�adnicov�ch os�ch, speed definuj� rychlost rotace. Posledn� t�i desetinn� prom�nn� ur�uj� pozici ve sc�n�.</p>

<p class="src0">GLfloat xrot, yrot, zrot;<span class="kom">// Rotace</span></p>
<p class="src0">GLfloat xspeed, yspeed, zspeed;<span class="kom">// Rychlost rotace</span></p>
<p class="src0">GLfloat cx, cy, cz = -15;<span class="kom">// Pozice</span></p>

<p>Aby se program zbyte�n� nezpomaloval p�i pokusech morfovat objekt s�m na sebe, deklarujeme key, kter� ozna�uje pr�v� zobrazen� objekt. Morph v programu indikuje, jestli pr�v� prov�d�me transformaci objekt� nebo ne. V ust�len�m stavu m� hodnotu FALSE.</p>

<p class="src0">int key = 1;<span class="kom">// Pr�v� zobrazen� objekt</span></p>
<p class="src0">bool morph = FALSE;<span class="kom">// Prob�h� pr�v� morfov�n�?</span></p>

<p>P�i�azen�m 200 do steps ur��me, �e zm�na jednoho objektu na druh� bude trvat 200 p�ekreslen�. ��m v�t�� ��slo zad�me, t�m budou p�em�ny plynulej��, ale z�rove� m�n� pomal�. No a step definuje ��slo pr�v� prov�d�n�ho kroku.</p>

<p class="src0">int steps = 200;<span class="kom">// Po�et krok� zm�ny</span></p>
<p class="src0">int step = 0;<span class="kom">// Aktu�ln� krok</span></p>

<p>Struktura VERTEX obsahuje x, y, z slo�ky pozice jednoho bodu ve 3D prostoru.</p>

<p class="src0">typedef struct<span class="kom">// Struktura pro bod ve 3D</span></p>
<p class="src0">{</p>
<p class="src1">float x, y, z;<span class="kom">// X, y, z slo�ky pozice</span></p>
<p class="src0">} VERTEX;<span class="kom">// Nazvan� VERTEX</span></p>

<p>Pokus�me se o vytvo�en� struktury objektu. co v�echno budeme pot�ebovat? Tak ur�it� to bude n�jak� pole pro ulo�en� v�ech vrchol�. Abychom ho mohli v pr�b�hu programu libovoln� m�nit, deklarujeme jej jako ukazatel do dynamick� pam�ti. Celo��seln� prom�nn� vert specifikuje maxim�ln� mo�n� index tohoto pole a vlastn� i po�et bod�, ze kter�ch se skl�d�.</p>

<p class="src0">typedef struct<span class="kom">// Struktura objektu</span></p>
<p class="src0">{</p>
<p class="src1">int verts;<span class="kom">// Po�et bod�, ze kter�ch se skl�d�</span></p>
<p class="src1">VERTEX* points;<span class="kom">// Ukazatel do pole vertex�</span></p>
<p class="src0">} OBJECT;<span class="kom">// Nazvan� OBJECT</span></p>

<p>Pokud bychom se nedr�eli z�sady, �e v�echny objekty mus� m�t stejn� po�et vrchol�, vznikly by komplikace. Daj� se vy�e�it prom�nnou, kter� obsahuje ��slo maxim�ln�ho po�tu sou�adnic. Uve�me p��klad: jeden objekt bude krychle s osmi vrcholy a druh� pyramida se �ty�mi. Do maxver tedy ulo��me ��slo osm. Nicm�n� stejn� doporu�uji, aby m�ly v�echny objekty stejn� po�et bod� - v�e bude jednodu���.</p>

<p class="src0">int maxver;<span class="kom">// Eventu�ln� ukl�d� maxim�ln� po�et bod� v jednom objektu</span></p>

<p>Prvn� t�i instance struktury OBJECT ukl�daj� data, kter� nahrajeme ze soubor�. Do �tvrt�ho vygenerujeme n�hodn� ��sla - body n�hodn� rozh�zen� po obrazovce. Helper je objekt pro vykreslov�n�. Obsahuje mezistavy z�skan� kombinac� objekt� v ur�it�m kroku morfingu. Posledn� dv� prom�nn� jsou ukazatele na zdrojov� a v�sledn� objekt, kter� chce u�ivatel zam�nit.</p>

<p class="src0">OBJECT morph1, morph2, morph3, morph4;<span class="kom">// Koule, toroid, v�lec (trubka), n�hodn� body</span></p>
<p class="src0">OBJECT helper, *sour, *dest;<span class="kom">// Pomocn�, zdrojov� a c�lov� objekt</span></p>

<p>Ve funkci objallocate() alokujeme pam� pro strukturu objektu, na kter� ukazuje pointer *k p�edan� parametrem. Celo��seln� n definuje po�et vrchol� objektu.</p>

<p>Funkci malloc(), kter� vrac� ukazatel na dynamicky alokovanou pam� p�ed�me jej� po�adovanou velikost. Z�sk�me ji oper�torem sizeof() vyn�soben�m po�tem vertex�. Proto�e malloc() vrac� ukazatel na void, mus�me ho p�etypovat.</p>

<p>Pozn. p�ekl.: Program by je�t� m�l otestovat jestli byla opravdu alokov�na. Kdyby se operace nezda�ila, program by p�istupoval k nezabran� pam�ti a aplikace by se zcela jist� zhroutila. Malloc() v p��pad� ne�sp�chu vrac� NULL.</p>

<p class="src0">void objallocate(OBJECT *k,int n)<span class="kom">// Alokuje dynamickou pam� pro objekt</span></p>
<p class="src0">{</p>
<p class="src1">k-&gt;points = (VERTEX*) malloc(sizeof(VERTEX) * n);<span class="kom">// Alokuje pam�</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// P�ekladatel:</span></p>
<p class="src1"><span class="kom">// if(k-&gt;points == NULL)</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// MessageBox(NULL,&quot;Chyba p�i alokaci pam�ti pro objekt&quot;, &quot;ERROR&quot;, MB_OK | MB_ICONSTOP);</span></p>
<p class="src2"><span class="kom">// Ukon�it program</span></p>
<p class="src1"><span class="kom">// }</span></p>
<p class="src0">}</p>

<p>Po ka�d� alokaci dynamick� pam�ti mus� V�DY p�ij�t jej� uvoln�n�. Funkci op�t p�ed�v�me ukazatel na objekt.</p>

<p class="src0">void objfree(OBJECT *k)<span class="kom">// Uvoln� dynamickou pam� objektu</span></p>
<p class="src0">{</p>
<p class="src1">free(k-&gt;points);<span class="kom">// Uvoln� pam�</span></p>
<p class="src0">}</p>

<p>Funkce readstr() je velmi podobn� (�pln� stejn�) jako v lekci 10. Na�te jeden ��dek ze souboru f a ulo�� ho do �et�zce string. Abychom mohli udr�et data souboru p�ehledn� funkce p�eskakuje pr�zdn� ��dky (\n) a c-��kovsk� koment��e (��dky za��naj�c� //, respektive '/').</p>

<p class="src0">void readstr(FILE *f,char *string)<span class="kom">// Na�te jeden pou�iteln� ��dek ze souboru</span></p>
<p class="src0">{</p>
<p class="src1">do</p>
<p class="src1">{</p>
<p class="src2">fgets(string, 255, f);<span class="kom">// Na�ti ��dek</span></p>
<p class="src1">} while ((string[0] == '/') || (string[0] == '\n'));<span class="kom">// Pokud nen� pou�iteln�, na�ti dal��</span></p>
<p class="src1">return;</p>
<p class="src0">}</p>

<p>Nap�eme funkci pro loading objektu z textov�ho souboru. Name specifikuje diskovou cestu k souboru a k je ukazatel na objekt, do kter�ho ulo��me v�sledek.</p>

<p class="src0">void objload(char *name,OBJECT *k)<span class="kom">// Nahraje objekt ze souboru</span></p>
<p class="src0">{</p>

<p>Za�neme deklarac� lok�ln�ch prom�nn�ch funkce. Do ver na�teme po�et vertex�, kter� ur�uje prvn� ��dka v souboru (v�ce d�le). D� se ��ct, �e rx, ry, rz jsou pouze pro zp�ehledn�n� zdrojov�ho k�du programu. Ze souboru do nich na�teme jednotliv� slo�ky bodu. Ukazatel filein ukazuje na soubor (po otev�en�). Oneline je znakov� buffer. V�dy do n�j na�teme jednu ��dku, analyzujeme ji a z�sk�me informace, kter� pot�ebujeme.</p>

<p class="src1">int ver;<span class="kom">// Po�et bod�</span></p>
<p class="src1">float rx, ry, rz;<span class="kom">// X, y, z pozice</span></p>
<p class="src1">FILE* filein;<span class="kom">// Handle souboru</span></p>
<p class="src1">char oneline[255];<span class="kom">// Znakov� buffer</span></p>

<p>Pomoc� funkce fopen() otev�eme soubor pro �ten�. Pozn. p�ekl.: Stejn� jako u alokace pam�ti i zde chyb� o�et�en� chyb.</p>

<p class="src1">filein = fopen(name, &quot;rt&quot;);<span class="kom">// Otev�e soubor</span></p>

<p class="src1"><span class="kom">// P�ekladatel:</span></p>
<p class="src1"><span class="kom">// if(filein == NULL)</span></p>
<p class="src1"><span class="kom">// {</span></p>
<p class="src2"><span class="kom">// MessageBox(NULL,&quot;Chyba p�i otev�en� souboru s daty&quot;, &quot;ERROR&quot;, MB_OK | MB_ICONSTOP);</span></p>
<p class="src2"><span class="kom">// Ukon�it program</span></p>
<p class="src1"><span class="kom">// }</span></p>

<p>Do znakov�ho bufferu na�teme prvn� ��dku. M�la by b�t ve tvaru: <span class="src0">Vertices: x\n</span>. Z �et�zce tedy pot�ebujeme vydolovat ��slo x, kter� ud�v� po�et vertex� definovan�ch v souboru. Tento po�et ulo��me do vnit�n� prom�nn� struktury a potom alokujeme tolik pam�ti, aby se do n� v�echny koordin�ty sou�adnic ve�ly.</p>

<p class="src1">readstr(filein, oneline);<span class="kom">// Na�te prvn� ��dku ze souboru</span></p>
<p class="src1">sscanf(oneline, &quot;Vertices: %d\n&quot;, &amp;ver);<span class="kom">// Po�et vertex�</span></p>
<p class="src"></p>
<p class="src1">k-&gt;verts = ver;<span class="kom">// Nastav� polo�ku struktury na spr�vnou hodnotu</span></p>
<p class="src"></p>
<p class="src1">objallocate(k, ver);<span class="kom">// Alokace pam�ti pro objekt</span></p>

<p>U� tedy v�me z kolikati bod� je objekt vytvo�en a m�me alokov�nu pot�ebnou pam�. Nyn� je�t� mus�me na��st jednotliv� hodnoty. Provedeme to cyklem for s ��d�c� prom�nnou i, kter� se ka�d�m pr�chodem inkrementuje. Postupn� na�teme v�echny ��dky do bufferu, ze kter�ho p�es funkci sscanf() dostaneme ��seln� hodnoty slo�ek vertexu pro v�echny t�i sou�adnicov� osy. Pomocn� prom�nn� zkop�rujeme do prom�nn�ch struktury. Po anal�ze cel�ho souboru ho zav�eme.</p>

<p>Je�t� mus�m upozornit, �e je d�le�it�, aby soubor obsahoval stejn� po�et bod� jako je definov�no na za��tku. Pokud by jich bylo v�ce, tolik by to nevadilo - posledn� by se prost� nena�etly. V ��dn�m p��pad� jich ale NESM� b�t m�n�! S nejv�t�� pravd�podobnost� by to zhroutilo program. V�e, na co se pokou��m upozornit by se dalo shrnout do v�ty: Jestli�e soubor za��n� &quot;Vertices: 10&quot;, mus� v n�m b�t specifikov�no 10 sou�adnic (30 ��sel - x, y, z).</p>

<p class="src1">for (int i = 0; i &lt; ver; i++)<span class="kom">// Postupn� na��t� body</span></p>
<p class="src1">{</p>
<p class="src2">readstr(filein, oneline);<span class="kom">// Na�te ��dek ze souboru</span></p>
<p class="src"></p>
<p class="src2">sscanf(oneline, &quot;%f %f %f&quot;, &amp;rx, &amp;ry, &amp;rz);<span class="kom">// Najde a ulo�� t�i ��sla</span></p>
<p class="src"></p>
<p class="src2">k-&gt;points[i].x = rx;<span class="kom">// Nastav� vnit�n� prom�nnou struktury</span></p>
<p class="src2">k-&gt;points[i].y = ry;<span class="kom">// Nastav� vnit�n� prom�nnou struktury</span></p>
<p class="src2">k-&gt;points[i].z = rz;<span class="kom">// Nastav� vnit�n� prom�nnou struktury</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">fclose(filein);<span class="kom">// Zav�e soubor</span></p>

<p>Otestujeme, zda nen� prom�nn� ver (po�et bod� aktu�ln�ho objektu) v�t�� ne� maxver (v sou�asnosti maxim�ln� zn�m� po�et vertex� v jednom objektu). Pokud ano p�i�ad�me ver do maxver.</p>

<p class="src1">if(ver &gt; maxver)<span class="kom">// Aktualizuje maxim�ln� po�et vertex�</span></p>
<p class="src2">maxver = ver;</p>
<p class="src0">}</p>

<p>Na �adu p�ich�z� trochu m�n� pochopiteln� funkce - zvlṻ pro ty, kte�� nemaj� v l�sce matematiku. Bohu�el morfing na n� stav�. Co tedy d�l�? Spo��t� o klik m�me posunout bod specifikovan� parametrem i. Na za��tku deklarujeme pomocn� vertex, podle vzorce spo��t�me jeho jednotliv� x, y, z slo�ky a v z�v�ru ho vr�t�me volaj�c� funkci.</p>

<p>Pou�it� matematika pracuje asi takto: od sou�adnice i-t�ho bodu zdrojov�ho objektu ode�teme sou�adnici bodu, do kter�ho morfujeme. Rozd�l vyd�l�me zam��len�m po�tem krok� a kone�n� v�sledek ulo��me do a.</p>

<p>�ekn�me, �e x-ov� sou�adnice zdrojov�ho objektu (sour) je rovna �ty�iceti a c�lov�ho objektu (dest) dvaceti. U deklarace glob�ln�ch prom�nn�ch jsme steps p�i�adili 200. V�po�tem a.x = (40-20)/200 = 20/200 = 0,1 zjist�me, �e p�i p�esunu ze 40 na 20 s krokem 200 pot�ebujeme ka�d� p�ekreslen� pohnout na ose x bodem o desetinu jednotky. Nebo jinak: n�sob�me-li 200*0,1 dostaneme rozd�l pozic 20, co� je tak� pravda (40-20=20). M�lo by to fungovat.</p>

<p class="src0">VERTEX calculate(int i)<span class="kom">// Spo��t� o kolik pohnout bodem p�i morfingu</span></p>
<p class="src0">{</p>
<p class="src1">VERTEX a;<span class="kom">// Pomocn� bod</span></p>
<p class="src"></p>
<p class="src1">a.x = (sour-&gt;points[i].x - dest-&gt;points[i].x) / steps;<span class="kom">// Spo��t� posun</span></p>
<p class="src1">a.y = (sour-&gt;points[i].y - dest-&gt;points[i].y) / steps;<span class="kom">// Spo��t� posun</span></p>
<p class="src1">a.z = (sour-&gt;points[i].z - dest-&gt;points[i].z) / steps;<span class="kom">// Spo��t� posun</span></p>
<p class="src"></p>
<p class="src1">return a;<span class="kom">// Vr�t� v�sledek</span></p>
<p class="src0">}</p>

<p>Za��tek inicializa�n� funkce nen� ��dnou novinkou, ale d�le v k�du najdete zm�ny.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echno nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE);<span class="kom">// Typ blendingu</span></p>
<p class="src1"><span class="kom">// glEnable(GL_BLEND);// Zapne blending (p�ekl.: autor asi zapomn�l)</span></p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src"></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolen� testov�n� hloubky</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>

<p>Proto�e je�t� nezn�me maxim�ln� po�et bod� v jednom objektu, p�i�ad�me do maxver nulu. Pot� pomoc� funkce objload() na�teme z disku data jednotliv�ch objekt� (koule, toroid, v�lec). V prvn�m parametru p�ed�v�me cestu se jm�nem souboru, ve druh�m adresu objektu, do kter�ho se maj� data ulo�it.</p>

<p class="src1">maxver = 0;<span class="kom">// Nulov�n� maxim�ln�ho po�tu bod�</span></p>
<p class="src"></p>
<p class="src1">objload(&quot;data/sphere.txt&quot;, &amp;morph1);<span class="kom">//Na�te kouli</span></p>
<p class="src1">objload(&quot;data/torus.txt&quot;, &amp;morph2);<span class="kom">// Na�te toroid</span></p>
<p class="src1">objload(&quot;data/tube.txt&quot;, &amp;morph3);<span class="kom">// Na�te v�lec</span></p>

<p>�tvrt� objekt nena��t�me ze souboru. Budou j�m po sc�n� rozh�zen� body (p�esn� 486 bod�). Nejd��ve mus�me alokovat pam� pro jednotliv� vertexy a potom sta�� v cyklu vygenerovat n�hodn� sou�adnice. Budou v rozmez� od -7 do +7.</p>

<p class="src1">objallocate(&amp; morph4, 486);<span class="kom">// Alokace pam�ti pro 486 bod�</span></p>
<p class="src"></p>
<p class="src1">for(int i=0; i &lt; 486; i++)<span class="kom">// Cyklus generuje n�hodn� sou�adnice</span></p>
<p class="src1">{</p>
<p class="src2">morph4.points[i].x = ((float)(rand() % 14000) / 1000) - 7;<span class="kom">// N�hodn� hodnota</span></p>
<p class="src2">morph4.points[i].y = ((float)(rand() % 14000) / 1000) - 7;<span class="kom">// N�hodn� hodnota</span></p>
<p class="src2">morph4.points[i].z = ((float)(rand() % 14000) / 1000) - 7;<span class="kom">// N�hodn� hodnota</span></p>
<p class="src1">}</p>

<p>Ze soubor� jsme loadovali v�echny objekty do struktur. Jejich data u� nebudeme upravovat. Od te� jsou jen pro �ten�. Pot�ebujeme tedy je�t� jeden objekt, helper, kter� bude p�i morfingu ukl�dat jednotliv� mezistavy. Proto�e na za��tku zobrazujeme morp1 (koule) na�teme i do pomocn�ho tento objekt.</p>

<p class="src1">objload(&quot;data/sphere.txt&quot;, &amp;helper);<span class="kom">// Na�ten� koule do pomocn�ho objektu</span></p>

<p>Nastav�me je�t� pointery pro zdrojov� a c�lov� objekt, tak aby ukazovali na adresu morph1.</p>

<p class="src1">sour = dest = &amp;morph1;<span class="kom">// Inicializace ukazatel� na objekty</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Ukon�� funkci</span></p>
<p class="src0">}</p>

<p>Vykreslov�n� za�neme klasicky smaz�n�m obrazovky a hloubkov�ho bufferu, resetem matice, posunem a rotacemi. M�sto abychom v�echny pohyby prov�d�li na konci funkce, tentokr�t je um�st�me na za��tek. Pot� deklarujeme pomocn� prom�nn�. Do tx, ty, tz spo��t�me sou�adnice, kter� pak p�ed�me funkci glVertex3f() kv�li nakreslen� bodu. Q je pomocn� bod pro v�po�et.</p>

<p class="src0">void DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(cx,cy,cz);<span class="kom">// P�esun na pozici</span></p>
<p class="src1">glRotatef(xrot, 1,0,0);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(yrot, 0,1,0);<span class="kom">// Rotace na ose y</span></p>
<p class="src1">glRotatef(zrot, 0,0,1);<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src1">xrot += xspeed;<span class="kom">// Zv�t�� �hly rotace</span></p>
<p class="src1">yrot += yspeed;</p>
<p class="src1">zrot += zspeed;</p>
<p class="src"></p>
<p class="src1">GLfloat tx, ty, tz;<span class="kom">// Pomocn� sou�adnice</span></p>
<p class="src1">VERTEX q;<span class="kom">// Pomocn� bod pro v�po�ty</span></p>

<p>P�es glBegin(GL_POINTS) ozn�m�me OpenGL, �e v bl�zk� dob� budeme vykreslovat body. V cyklu for proch�z�me vertexy. ��d�c� prom�nnou i bychom tak� mohli porovn�vat s maxver, ale proto�e maj� v�echny objekty stejn� po�et sou�adnic, m��eme s klidem pou��t po�et vertex� prvn�ho objektu - morph1.verts.</p>

<p class="src1">glBegin(GL_POINTS);<span class="kom">// Za��tek kreslen� bod�</span></p>
<p class="src2">for(int i = 0; i &lt; morph1.verts; i++)<span class="kom">// Cyklus proch�z� vertexy</span></p>
<p class="src2">{</p>

<p>V p��pad� morfingu spo��t�me o kolik se m� vykreslovan� bod posunout oproti pozici p�i minul�m vykreslen�. Takto vypo��tan� hodnoty ode�teme od sou�adnic pomocn�ho objektu, do kter�ho ka�d� p�ekreslen� ukl�d�me aktu�ln� mezistav morfingu. Pokud se zrovna objekty mezi sebou netransformuj� ode��t�me nulu, tak�e se sou�adnice defakto nem�n�.</p>

<p class="src3">if(morph)<span class="kom">// Pokud zrovna morfujeme</span></p>
<p class="src4">q = calculate(i);<span class="kom">// Spo��t�me hodnotu posunut�</span></p>
<p class="src3">else<span class="kom">// Jinak</span></p>
<p class="src4">q.x = q.y = q.z = 0;<span class="kom">// Budeme ode��tat nulu, ale t�m neposouv�me</span></p>
<p class="src"></p>
<p class="src3">helper.points[i].x -= q.x;<span class="kom">// Posunut� na ose x</span></p>
<p class="src3">helper.points[i].y -= q.y;<span class="kom">// Posunut� na ose y</span></p>
<p class="src3">helper.points[i].z -= q.z;<span class="kom">// Posunut� na ose z</span></p>

<p>Abychom si zp�ehlednili program a tak� kv�li mali�k�mu efektu, zkop�rujeme pr�v� z�skan� ��sla do pomocn�ch prom�nn�ch.</p>

<p class="src3">tx = helper.points[i].x;<span class="kom">// Zp�ehledn�n� + efekt</span></p>
<p class="src3">ty = helper.points[i].y;<span class="kom">// Zp�ehledn�n� + efekt</span></p>
<p class="src3">tz = helper.points[i].z;<span class="kom">// Zp�ehledn�n� + efekt</span></p>

<p>V�echno m�me spo��t�no, tak�e p�ejdeme k vykreslen�. Nastav�me barvu na zelenomodrou a nakresl�me bod. Potom zvol�me trochu tmav�� modrou barvu. Ode�teme dvojn�sobek sou�adnic q od t a z�sk�me um�st�n� bodu p�i n�sleduj�c�m vol�n� t�to funkce (ob jedno). Na t�to pozici znovu vykresl�me bod. Do t�etice v�eho dobr�ho znovu ztmav�me barvu a op�t spo��t�me dal�� pozici, na kter� se vyskytne po �ty�ech pr�chodech touto funkc� a op�t ho vykresl�me.</p>

<p>Pro� jsme kr�sn� p�ehledn� k�d vlastn� komplikovali? I kdy� si to asi neuv�domujete, vytvo�ili jsme jednoduch� ��sticov� syst�m. S pou�it�m blendingu vytvo�� perfektn� efekt, kter� se ale bohu�el projev� pouze p�i transformaci objekt� z jednoho na druh�. Pokud zrovna nemorfujeme, v q sou�adnic�ch jsou ulo�eny nuly, tak�e druh� a t�et� bod kresl�me na stejn� m�sto jako prvn�.</p>

<p class="src3">glColor3f(0, 1, 1);<span class="kom">// Zelenomodr� barva</span></p>
<p class="src3">glVertex3f(tx, ty, tz);<span class="kom">// Vykresl� prvn� bod</span></p>
<p class="src"></p>
<p class="src3">glColor3f(0, 0.5f, 1);<span class="kom">// Mod�ej�� zelenomodr� barva</span></p>
<p class="src3">tx -= 2*q.x;<span class="kom">// Spo��t�n� nov�ch pozic</span></p>
<p class="src3">ty -= 2*q.y;</p>
<p class="src3">ty -= 2*q.y;</p>
<p class="src3">glVertex3f(tx, ty, tz);<span class="kom">// Vykresl� druh� bod v nov� pozici</span></p>
<p class="src"></p>
<p class="src3">glColor3f(0, 0, 1);<span class="kom">// Modr� barva</span></p>
<p class="src3">tx -= 2*q.x;<span class="kom">// Spo��t�n� nov�ch pozic</span></p>
<p class="src3">ty -= 2*q.y;</p>
<p class="src3">ty -= 2*q.y;</p>
<p class="src3">glVertex3f(tx, ty, tz);<span class="kom">// Vykresl� t�et� bod v nov� pozici</span></p>

<p>Ukon��me t�lo cyklu a glEnd() ozn�m�, �e d�le u� nebudeme nic vykreslovat.</p>

<p class="src2">}</p>
<p class="src1">glEnd();<span class="kom">// Ukon�� kreslen�</span></p>

<p>Jako posledn� v t�to funkci zkontrolujeme jestli transformujeme objekty. Pokud ano a z�rove� mus� b�t aktu�ln� krok morfingu men�� ne� celkov� po�et krok�, inkrementujeme aktu�ln� krok. Po dokon�en� morfingu ho vypneme. Proto�e jsme u� do�li k c�lov�mu objektu, ud�l�me z n�j zdrojov�. Krok reinicializujeme na nulu.</p>

<p class="src1">if(morph &amp;&amp; step &lt;= steps)<span class="kom">// Morfujeme a krok je men�� ne� maximum</span></p>
<p class="src1">{</p>
<p class="src2">step++;<span class="kom">// P��t� pokra�uj n�sleduj�c�m krokem</span></p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Nemorfujeme nebo byl pr�v� ukon�en</span></p>
<p class="src1">{</p>
<p class="src2">morph = FALSE;<span class="kom">// Konec morfingu</span></p>
<p class="src2">sour = dest;<span class="kom">// C�lov� objekt je nyn� zdrojov�</span></p>
<p class="src2">step = 0;<span class="kom">// Prvn� (nulov�) krok morfingu</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>KillGLWindow uprav�me jenom m�lo. Uvoln�me pouze dynamicky alokovanou pam�.</p>

<p class="src0">GLvoid KillGLWindow(GLvoid)<span class="kom">// Zav�r�n� okna</span></p>
<p class="src0">{</p>
<p class="src1">objfree(&amp;morph1);<span class="kom">// Uvoln� alokovanou pam�</span></p>
<p class="src1">objfree(&amp;morph2);<span class="kom">// Uvoln� alokovanou pam�</span></p>
<p class="src1">objfree(&amp;morph3);<span class="kom">// Uvoln� alokovanou pam�</span></p>
<p class="src1">objfree(&amp;morph4);<span class="kom">// Uvoln� alokovanou pam�</span></p>
<p class="src1">objfree(&amp;helper);<span class="kom">// Uvoln� alokovanou pam�</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zbytek nezm�n�n</span></p>
<p class="src0">}</p>

<p>Ve funkci WinMain() uprav�me k�d testuj�c� stisk kl�ves. N�sleduj�c�mi �esti testy regulujeme rychlost rotace objektu.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if(keys[VK_PRIOR])<span class="kom">// PageUp?</span></p>
<p class="src5">zspeed += 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_NEXT])<span class="kom">// PageDown?</span></p>
<p class="src5">zspeed -= 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_DOWN])<span class="kom">// �ipka dolu?</span></p>
<p class="src5">xspeed += 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_UP])<span class="kom">// �ipka nahoru?</span></p>
<p class="src5">xspeed -= 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_RIGHT])<span class="kom">// �ipka doprava?</span></p>
<p class="src5">yspeed += 0.01f;</p>
<p class="src"></p>
<p class="src4">if(keys[VK_LEFT])<span class="kom">// �ipka doleva?</span></p>
<p class="src5">yspeed -= 0.01f;</p>

<p>Dal��ch �est kl�ves pohybuje objektem po sc�n�.</p>

<p class="src4">if (keys['Q'])<span class="kom">// Q?</span></p>
<p class="src5">cz -= 0.01f;<span class="kom">// D�le</span></p>
<p class="src"></p>
<p class="src4">if (keys['Z'])<span class="kom">// Z?</span></p>
<p class="src5">cz += 0.01f;<span class="kom">// Bl�e</span></p>
<p class="src"></p>
<p class="src4">if (keys['W'])<span class="kom">// W?</span></p>
<p class="src5">cy += 0.01f;<span class="kom">// Nahoru</span></p>
<p class="src"></p>
<p class="src4">if (keys['S'])<span class="kom">// S?</span></p>
<p class="src5">cy -= 0.01f;<span class="kom">// Dolu</span></p>
<p class="src"></p>
<p class="src4">if (keys['D'])<span class="kom">// D?</span></p>
<p class="src5">cx += 0.01f;<span class="kom">// Doprava</span></p>
<p class="src"></p>
<p class="src4">if (keys['A'])<span class="kom">// A?</span></p>
<p class="src5">cx -= 0.01f;<span class="kom">// Doleva</span></p>

<p>Te� o�et��me stisk kl�ves 1-4. Aby se k�d provedl, nesm� b�t p�i stisku jedni�ky key roven jedn� (nejde morfovat z prvn�ho objektu na prvn�) a tak� nesm�me pr�v� morfovat (nevypadalo by to dob�e). V takov�m p��pad� nastav�me pro p��t� pr�chod t�mto m�stem key na jedna a morph na TRUE. C�lov�m objektem bude objekt jedna. Kl�vesy 2, 3, 4 jsou analogick�.</p>

<p class="src4">if (keys['1'] &amp;&amp; (key!=1) &amp;&amp; !morph)<span class="kom">// Kl�vesa 1?</span></p>
<p class="src4">{</p>
<p class="src5">key = 1;<span class="kom">// Proti dvojn�sobn�mu stisku</span></p>
<p class="src5">morph = TRUE;<span class="kom">// Za�ne morfovac� proces</span></p>
<p class="src5">dest = &amp;morph1;<span class="kom">// Nastav� c�lov� objekt</span></p>
<p class="src4">}</p>
<p class="src4">if (keys['2'] &amp;&amp; (key!=2) &amp;&amp; !morph)<span class="kom">// Kl�vesa 2?</span></p>
<p class="src4">{</p>
<p class="src5">key = 2;<span class="kom">// Proti dvojn�sobn�mu stisku</span></p>
<p class="src5">morph = TRUE;<span class="kom">// Za�ne morfovac� proces</span></p>
<p class="src5">dest = &amp;morph2;<span class="kom">// Nastav� c�lov� objekt</span></p>
<p class="src4">}</p>
<p class="src4">if (keys['3'] &amp;&amp; (key!=3) &amp;&amp; !morph)<span class="kom">// Kl�vesa 3?</span></p>
<p class="src4">{</p>
<p class="src5">key = 3;<span class="kom">// Proti dvojn�sobn�mu stisku</span></p>
<p class="src5">morph = TRUE;<span class="kom">// Za�ne morfovac� proces</span></p>
<p class="src5">dest = &amp;morph3;<span class="kom">// Nastav� c�lov� objekt</span></p>
<p class="src4">}</p>
<p class="src4">if (keys['4'] &amp;&amp; (key!=4) &amp;&amp; !morph)<span class="kom">// Kl�vesa 4?</span></p>
<p class="src4">{</p>
<p class="src5">key = 4;<span class="kom">// Proti dvojn�sobn�mu stisku</span></p>
<p class="src5">morph = TRUE;<span class="kom">// Za�ne morfovac� proces</span></p>
<p class="src5">dest = &amp;morph4;<span class="kom">// Nastav� c�lov� objekt</span></p>
<p class="src4">}</p>

<p>Douf�m, �e jste si tento tutori�l u�ili. A�koli v�stup nen� a� tak fantastick� jako v n�kter�ch jin�ch, nau�ili jste se spoustu v�c�. Hran�m si s k�dem lze doc�lit skv�l�ch efekt� - t�eba po sc�n� n�hodn� rozh�zen� body m�n�c� se ve slova. Zkuste pou��t polygony nebo linky nam�sto bod�, v�sledek bude je�t� lep��.</p>

<p>P�ed t�m, ne� vznikla tato lekce bylo vytvo�eno demo &quot;Morph&quot;, kter� demonstruje mnohem pokro�ilej�� verzi prob�ran�ho efektu. Lze ho naj�t na adrese <?OdkazBlank('http://homepage.ntlworld.com/fj.williams/PgSoftware.html');?>.</p>

<p class="autor">napsal: Piotr Cieslak<br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson25.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson25_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson25.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson25.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Alexandre.Hirzel@nat.unibe.ch">Alexandre Hirzel</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson25.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson25.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson25.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson25.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:jay@remotepoint.com">Jay Groven</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson25.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Schubert_P@Yahoo.de">Patrick Schubert</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson25.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:scarab@egyptian.net">DarkAlloy</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson25.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson25.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(25);?>
<?FceNeHeOkolniLekce(25);?>

<?
include 'p_end.php';
?>
