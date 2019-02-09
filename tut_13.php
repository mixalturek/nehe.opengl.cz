<?
$g_title = 'CZ NeHe OpenGL - Lekce 13 - Bitmapov� fonty';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(13);?>

<h1>Lekce 13 - Bitmapov� fonty</h1>

<p class="nadpis_clanku">�asto kladen� ot�zka t�kaj�c� se OpenGL zn�: "Jak zobrazit text?". V�dycky jde namapovat texturu textu. Bohu�el nad n�m m�te velmi malou kontrolu. A pokud nejste dob�� v blendingu, v�t�inou skon��te smixov�n�m s ostatn�mi obr�zky. Pokud byste cht�li zn�t leh�� cestu k v�stupu textu na jak�koli m�sto s libovolnou barvou nebo fontem, potom je tato lekce ur�it� pro v�s. Bitmapov� fonty jsou 2D p�sma, kter� nemohou b�t rotov�ny. V�dy je uvid�te zep�edu.</p>

<p>Mo�n� si �eknete co je tak t�k�ho na v�stupu textu. M��ete spustit grafick� editor,  vepsat text do obr�zku, nahr�t ho jako texturu, zapnout blending a pot� namapovat na polygon. Ale t�m uberete �as procesoru. V z�vislosti na typu filtrov�n� m��e v�sledek vypadat rozmazan� nebo jako poskl�dan� z kosti�ek. Pokud by m�l alfa kan�l, skon�� sm�chan� s objekty na obrazovce. Jist� v�te kolik r�zn�ch font� je dostupn�ch v syst�mu. V tomto tutori�lu se nau��te jak je pou��vat. Nejen, �e bitmapov� fonty vypadaj� stokr�t l�pe ne� text na textu�e, ale m��ete je jednodu�e m�nit za b�hu programu. Nen� t�eba d�lat texturu pro ka�d� slovo nebo n�pis, kter� chcete vypsat. Sta�� jen jeden p��kaz. Sna�il jsem se vytvo�it tuto funkci co nejjednodu���. V�echno co mus�te ud�lat je napsat glPrint("Hello, world!"). Podle dlouh�ho �vodu m��ete ��ci, �e jsem s t�mto tutori�lem dost spokojen�. Trvalo mi p�ibli�n� hodinu a p�l napsat tento program. Pro� tak dlouho? Proto�e ve skute�nosti nejsou dostupn� ��dn� informace o pou��v�n� bitmapov�ch font�, pokud samoz�ejm� nem�te r�di MFC. Abych udr�el v�e jednoduch�, rozhodl jsem se, �e by bylo p�kn� napsat jej v k pochopen� jednoduch�m C k�du.</p>

<p>Mal� pozn�mka: Tento k�d je specifick� pro Windows. Pou��v� wgl funkce Windows pro vytvo�en� fontu. Apple m� pravd�podobn� agl podporu, kter� by m�la d�lat tu samou v�c a X m� glx. Nane�t�st� nemohu zaru�it, �e tento k�d je p�enositeln�. Pokud m� n�kdo na platform� nez�visl� k�d pro kreslen� font� na obrazovku, po�lete mi jej a j� nap�i jin� tutori�l o fontech:</p>

<p>Za�neme typick�m k�dem z lekce 1. P�id�me hlavi�kov� soubor stdio.h pro vstupn� v�stupn� operace, stdarg.h pro rozbor textu a konvertov�n� prom�nn�ch do textu a kone�n� math.h, tak�e m��eme pohybovat textem po obrazovce s pou�it�m funkc� SIN a COS.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src"></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavi�kov� soubor pro matematickou knihovnu</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standartn� vstup/v�stup</span></p>
<p class="src0">#include &lt;stdarg.h&gt;<span class="kom">// Hlavi�kov� soubor pro funkce s prom�nn�m po�tem parametr�</span></p>
<p class="src"></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Priv�tn� GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trval� Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na�eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukl�d�n� vstupu z kl�vesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivn�</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>P�id�me 3 nov� prom�nn�. V base ulo��me ��slo prvn�ho vytvo�en�ho display listu. Ka�d� znak bude pot�ebovat vlastn�, tak�e jich bude relativn� dost. Znaku 'A' p�i�ad�me ��slo 65, 'B' 66, 'C' 67 atd. Lehce usoud�te, �e 'A' bude ulo�eno v base + 65 ('A' je 65 znak Ascii tabulky). D�le p�id�me 2 ��ta�e, kter� pou�ijeme k pohybu textu po obrazovce s pou�it�m sin� a kosin�. Budou slou�it i ke generov�n� barvy znak� (v�ce d�le).</p>

<p class="src0">GLuint base;<span class="kom">// ��slo z�kladn�ho display listu znak�</span></p>
<p class="src0">GLfloat cnt1;<span class="kom">// Pro pohyb a barvu textu</span></p>
<p class="src0">GLfloat cnt2;<span class="kom">// Pro pohyb a barvu textu</span></p>

<p>N�sleduj�c� funkce vytvo�� font - asi nejt쾹� ��st. "HFONT font" �ekne Windows, �e budeme manipulovat s fonty Windows. Vytvo�en�m 96 display list� definujeme base. Po skon�en� t�to operace v n� bude ulo�eno ��slo prvn�ho listu.</p>

<p class="src0">GLvoid BuildFont(GLvoid)<span class="kom">// Vytvo�en� fontu</span></p>
<p class="src0">{</p>
<p class="src1">HFONT font;<span class="kom">// Prom�nn� fontu</span></p>
<p class="src1">base = glGenLists(96);<span class="kom">// 96 znak�</span></p>

<p>Vytvo��me font. Prvn� parametr specifikuje velikost. V�imn�te si, �e je to z�porn� ��slo. Vlo�en�m znam�nka m�nus �ekneme Windows, aby na�lo p�smo podle v��ky ZNAKU. Pokud bychom pou�ili kladn�, hledalo by se podle v��ky BU�KY.</p>

<p class="src1">font = CreateFont(-24,<span class="kom">// V��ka</span></p>

<p>Ur��me ���ku bu�ky. Zad�n�m nuly Windows pou�ije implicitn� hodnotu. Konkr�tn� hodnotou vytvo��me font �ir��.</p>

<p class="src2">0,<span class="kom">// ���ka</span></p>

<p>�hel escapement nato�� font. Nen� to zrovna nejlep�� vlastnost. Kdybyste nepou�ili 0, 90, 180 nebo 270 stup��, font by se pravd�podobn� o�ezal r�me�kem.</p>

<p class="src2">0,<span class="kom">// �hel escapement</span></p>
<p class="src2">0,<span class="kom">// �hel orientace</span></p>

<p>Tu�nost fontu je u�ite�n� parametr. Lze pou��t ��sla 0 a� 1000 nebo n�kterou z p�eddefinovan�ch hodnot. FW_DONTCARE (0), FW_NORMAL (400), FW_BOLD (700) a FW_BLACK (900). Je jich samoz�ejm� v�ce, ale mysl�m si, �e tyto �ty�i bohat� sta�� (pop�. pou�ijte n�pov�du MSDN). ��m v�t�� hodnotu pou�ijete, t�m bude tu�n�j��.</p>

<p class="src2">FW_BOLD,<span class="kom">// Tu�nost</span></p>
<p class="src2">FALSE,<span class="kom">// Kurz�va</span></p>
<p class="src2">FALSE,<span class="kom">// Podtr�en�</span></p>
<p class="src2">FALSE,<span class="kom">// P�e�krtnut�</span></p>

<p>Znakov� sada popisuje typ znak�, kter� chcete pou��t. Nap�. CHINESEBIG5_CHARSET, GREEK_CHARSET, RUSSIAN_CHARSET, DEFAULT_CHARSET atd. ANSI je jedin�, kterou pou��v�m, nicm�n� DEFAULT by koneckonc� mohlo pracovat tak�. (Pokud r�di pou��v�te fonty Webdings nebo Wingdings pou�ijte SYMBOL_CHARSET.).</p>

<p class="src2">ANSI_CHARSET,<span class="kom">// Znakov� sada</span></p>

<p>P�esnost v�stupu ��k� Windows jakou znakovou sadu pou��t, maj�-li dv� stejn� jm�na. Je-li v�ce mo�n�ch font� OUT_TT_PRECIS vybere TRUETYPE verzi, kter� vypad� mnohem l�pe - p�edev��m, kdy� se zv�t��. Zadat m��ete tak� OUT_TT_ONLY_PRECIS, kter� v�dy pou�ije TrueType font.</p>

<p class="src2">OUT_TT_PRECIS,<span class="kom">// P�esnost v�stupu (TrueType)</span></p>

<p>P�esnost o�ez�n� je typ o�ez�n�, kter� se pou�ije, kdy� se font dostane ven z o�ez�vac�ho regionu.</p>

<p class="src2">CLIP_DEFAULT_PRECIS,<span class="kom">// P�esnost o�ez�n�</span></p>

<p>Do v�stupn� kvality m��ete zadat PROOF, DRAFT, NONANTIALIASED, DEFAULT nebo ANTIALIASED (m�n� hranat�).</p>

<p class="src2">ANTIALIASED_QUALITY,<span class="kom">// V�stupn� kvalita</span></p>

<p>Nastav�me rodinu a pitch. Do pitch lze zadat DEFAULT_PITCH, FIXED_PITCH a VARIABLE_PITCH. Do rodiny FF_DECORATIVE, FF_MODERN, FF_ROMAN, FF_SCRIPT, FF_SWISS, FF_DONTCARE. Zkuste si s nimi pohr�t.</p>

<p class="src2">FF_DONTCARE | DEFAULT_PITCH,<span class="kom">// Rodina a pitch</span></p>

<p>Nakonec zad�me jm�no fontu. Spus�te MS Word nebo jin� textov� editor a najd�te si jm�no p�sma, kter� se v�m l�b�.</p>

<p class="src2">&quot;Courier New&quot;);<span class="kom">// Jm�no fontu</span></p>

<p>Vybereme font do DC (device context - kontext za��zen�) a vytvo��me 96 display list� po��naj�ce 32 (v Ascii tabulce jsou p�ed 32 neti�titeln� znaky, 32 - mezera). M��ete sestavit v�ech 256 zad�n�m ��sla 256 do glGenList() (v��e - na za��tku t�to funkce). Ujist�te se, �e sma�ete v�ech 256 list� po skon�en� programu (funkce KillFont(GLvoid)) a samoz�ejm� mus�te napsat v n�sleduj�c�m p��kazu m�sto 32 -> 0 a m�sto 96 -> 255 (viz. dal�� lekce o fontech).</p>

<p class="src1">SelectObject(hDC, font);<span class="kom">// V�b�r fontu do DC</span></p>
<p class="src"></p>
<p class="src1">wglUseFontBitmaps(hDC, 32, 96, base);<span class="kom">// Vytvo�� 96 znak�, po��naje 32 v Ascii
</span></p>
<p class="src0">}</p>

<p>N�sleduj�c� k�d je kr�sn� jednoduch�. Sma�e 96 vytvo�en�ch display list� z pam�ti po��naje prvn�m, kter� je ur�en v "base". Nejsem si jist�, jestli by to Windows ud�lali automaticky. Jeden ��dek za jistotu stoj�.</p>

<p class="src0">GLvoid KillFont(GLvoid)<span class="kom">// Sma�e font</span></p>
<p class="src0">{</p>
<p class="src1">glDeleteLists(base, 96);<span class="kom">// Sma�e v�ech 96 znak� (display list�)</span></p>
<p class="src0">}</p>

<p>A te� p�ich�z� na �adu funkce, kv�li n� je naps�na tato lekce. Vol� se �pln� stejn� jako klasick� printf("Hello, world!"); s t�m rozd�lem, �e na za��tek p�id�te gl a p�ed z�vorkou uberete f :-]</p>

<p class="src0">GLvoid glPrint(const char *fmt, ...)<span class="kom">// Klon printf() pro OpenGL</span></p>
<p class="src0">{</p>

<p>Prvn� ��dek alokuje pam� pro 256 znak�. Jak�si �et�zec, kter� nakonec vyp�eme na obrazovku. Druhou prom�nnou tvo�� ukazatel do argument� funkce, kter� jsme p�i vol�n� zadali s �et�zcem k�d t�to lekce. ( printf("%d %d", i,  j) - to zn�te, ne?)</p>

<p class="src1">char text[256];<span class="kom">// Ukl�d� �et�zec</span></p>
<p class="src1">va_list ap;<span class="kom">// Pointer do argument� funkce</span></p>

<p>Dal�� dva ��dky zkou�ej�, jestli byl zad�n text. Pokud ne fmt ukazuje na nic (NULL) a tud� se nic nevyp�e.</p>

<p class="src1">if (fmt == NULL)<span class="kom">// Byl p�ed�n text?</span></p>
<p class="src2">return;<span class="kom">// Konec</span></p>

<p>N�sleduj�c� k�d konvertuje ve�ker� symboly  (%d, %f...) v �et�zci na konkr�tn� hodnoty. Po �prav� bude v�e ulo�eno v text.</p>

<p class="src1">va_start(ap, fmt);<span class="kom">// Rozbor �et�zce</span></p>
<p class="src1">vsprintf(text, fmt, ap);<span class="kom">// Zam�n� symboly za konkr�tn� ��sla</span></p>
<p class="src1">va_end(ap);<span class="kom">// V�sledek je ulo�en v text</span></p>

<p>P��kaz glListBase(base-32) je na vysv�tlen� trochu obt�n�j��. �ekn�me, �e vykreslujeme znak 'A', kter� je reprezentov�n 65 (v Ascii). Bez glListBase(base-32) OpenGL nev�, kde m� naj�t tento znak. Mohlo by vyhledat display list 65, ale pokud by se base rovnalo 1000, tak by 'A' bylo ulo�eno v display listu 1065. Tak�e nastaven�m base na po��te�n� bod, OpenGL bude v�d�t, odkud vz�t ten spr�vn� display list. Ode��t�me 32, proto�e jsme nevytvo�ili prvn�ch 32 display list�, tud� je p�esko��me.</p>

<p class="src1">glPushAttrib(GL_LIST_BIT);<span class="kom">// Ulo�� sou�asn� stav display list�</span></p>
<p class="src1">glListBase(base - 32);<span class="kom">// Nastav� z�kladn� znak na 32</span></p>

<p>Zavol�me funkci glCallLists(), kter� najednou zobrazuje v�ce display list�. strlen(text) vr�t� po�et znak� v �et�zci a t�m i po�et k zobrazen�. D�le pot�ebujeme zn�t typ p�ed�van�ho parametru (posledn�). Ani te� nebudeme vkl�dat v�ce ne� 256 znak�, tak�e pou�ijeme GL_UNSIGNED_BYTE (byte m��e nab�vat hodnot 0-255, co� je p�esn� to, co pot�ebujeme). V posledn�m parametru p�ed�me text. Ka�d� display list v�, kde je prav� hrana toho p�edchoz�ho, ��m� zamez�me nakupen� znak� na sebe, na jedno m�sto. P�ed za��tkem kreslen� n�sleduj�c� znaku se p�esune o tuto hodnotu doprava (glTranslatef()). Nakonec nastav�me GL_LIST_BIT zp�t na hodnotu maj�c� p�ed vol�n�m glListBase().</p>

<p class="src1">glCallLists(strlen(text), GL_UNSIGNED_BYTE, text);<span class="kom">// Vykresl� display listy</span></p>
<p class="src1">glPopAttrib();<span class="kom">// Obnov� p�vodn� stav display list�</span></p>
<p class="src0">}</p>

<p>Jedin� zm�na v inicializa�n�m k�du je p��kaz volaj�c� BuildFont().</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echna nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povol� jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� hloubkov� testov�n�</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>
<p class="src"></p>
<p class="src1">BuildFont();<span class="kom">// Vytvo�� font</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>P�ejdeme k vykreslov�n�. Po obvykl�ch inicializac�ch se p�esuneme o 1 jednotku do obrazovky. Bitmapov� fonty pracuj� l�pe p�i pou�it� kolm� (ortho) projekce ne� p�i perspektivn�. Nicm�n� kolm� vypad� h��e, tud� provedeme translaci do obrazovky. Po p�esunu o 1 jednotku dovnit�, budeme moci um�stit text kamkoli od -0.5 do +0.5 na ose x. Po p�esunu o 10 bychom mohli vykreslovat na pozice od -5.0 do +5.0. Nikdy nem��te velikost textu a naprosto nikdy nepou��vejte zm�nu m���tka glScale(x,y,z). Chcete-li m�t font v�t�� �i men�� mus�te na to myslet p�i vytv��en�.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-1.0f);<span class="kom">// P�esun o 1 do obrazovky</span></p>

<p>D�le nastav�me barvu textu. V tomto p��pad� pou��v�m dva ��ta�e. �erven� slo�ka se ur�uje podle kosinu prvn�ho ��ta�e. Hodnoty se m�n� od -1.0 do +1.0. Zelen� slo�ku vypo��t�me podle sinu druh�ho ��ta�e. Rozsahy jsou stejn� jako v p�edchoz�m p��pad�. K modr� barv� jsou pou�ity oba ��ta�e s kosinem. Hodnoty n�le�ej� od 0.5 do 1.5, tedy v�sledek operace nebude nikdy 0 a text bude v�dy viditeln�.</p>

<p class="src1"><span class="kom">// Pulzov�n� barev z�visl� na pozici</span></p>
<p class="src1">glColor3f(1.0f*float(cos(cnt1)),1.0f*float(sin(cnt2)),1.0f-0.5f*float(cos(cnt1+cnt2)));</p>

<p>K ur�en� pozice pou�ijeme nov� p��kaz. St�ed z�stal na 0.0, ale asi jste si v�imli, �e sch�z� pozice osy z. Po p�esunu o jednotku do obrazovky je lev� nejvzd�len�j�� viditeln� bod -0.5 a prav� +0.5. Proto�e se v�dy text vykresluje zleva doprava, p�esuneme o 0.45 doleva. T�m bude vycentrov�n na st�ed. Pou�it� matematika vykon�v� stejnou funkci jako p�i nastavov�n� barvy. Na ose x se text pohybuje od -0.5 do -0.4 (ode�etli jsme 0.45). T�m udr��me text v�dy viditeln�. Na ose y se hranice nach�zej� na -0.35 a +0.35.</p>

<p class="src1">glRasterPos2f(-0.45f+0.05f*float(cos(cnt1)), 0.32f*float(sin(cnt2)));<span class="kom">// Pozice textu</span></p>

<p>Vyp�eme text. Tuto funkci jsem navrhl jako super snadnou a u�ivatelsky p��jemnou. Vypad� jako vol�n� printf() ze stdio.h zk��en� s OpenGL. Text se vykresl� na pozici, kam jsme p�esunuli p�ed chv�l�. Pod�et�zec %7.2f oznamuje vypisov�n� obsahu prom�nn�. Sedmi�ka ur�uje, maxim�ln� d�lku ��sla a dvojka up�es�uje po�et desetinn�ch m�st. f zna�� desetinn� ��slo (float). Je mi samoz�ejm� jasn�, �e pokud ovl�d�te jazyk C, tak je to pro v�s hra�ka. Konvence jsou stejn� jako u klasick� printf(). Pokud to bude nutn� m��ete se pod�vat do n�pov�dy MSDN.</p>

<p class="src1">glPrint(&quot;Active OpenGL Text With NeHe - %7.2f&quot;, cnt1);<span class="kom">// V�pis textu</span></p>

<p>Nakonec zb�v� inkrementov�n� ��ta�e, aby se m�nila pozice a barva.</p>

<p class="src1">cnt1+=0.051f;</p>
<p class="src1">cnt2+=0.005f;</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Posledn� k�d, kter� se provede p�ed opu�t�n�m programu je smaz�n� fontu vol�n�m KillFont().</p>

<p class="src0"><span class="kom">//Konec funkce KillGLWindow(GLvoid)</span></p>
<p class="src1">if(!UnregisterClass("OpenGL",hInstance))</p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,"Could Not Unregister Class.","SHUTDOWN ERROR",MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">hInstance=NULL;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">KillFont();<span class="kom">//Smaz�n� fontu</span></p>
<p class="src0">}</p>

<p>Hotovo... Na internetu jsem hledal podobn� tutori�l, ale nic jsem nena�el. Mo�n� jsem prvn�, kter� p�e na podobn� t�ma. V�e je mo�n�. U�ijte si v�pis textu a ��astn� k�dov�n�.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson13.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson13_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson13.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson13.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson13.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson13.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson13.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson13.tar.gz">Irix / GLUT</a> k�d t�to lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson13.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson13.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:ncb000gt65@hotmail.com">Nicholas Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson13.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson13.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson13.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson13.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson13.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson13.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson13.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:andras.kobor@wanadoo.fr">Andras Kobor</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson13.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson13-2.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:fredo@studenten.net">Edo</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson13.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(13);?>
<?FceNeHeOkolniLekce(13);?>

<?
include 'p_end.php';
?>
