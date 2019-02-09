<?
$g_title = 'CZ NeHe OpenGL - Lekce 28 - Bezierovy k�ivky a povrchy, fullscreen fix';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(28);?>

<h1>Lekce 28 - Bezierovy k�ivky a povrchy, fullscreen fix</h1>

<p class="nadpis_clanku">David Nikdel je osoba stoj�c� za t�mto skv�l�m tutori�lem, ve kter�m se nau��te, jak se vytv��ej� Bezierovy k�ivky. D�ky nim lze velice jednodu�e zak�ivit povrch a prov�d�t jeho plynulou animaci pouhou modifikac� n�kolika kontroln�ch bod�. Aby byl v�sledn� povrch modelu je�t� zaj�mav�j��, je na n�j namapov�na textura. Tutori�l tak� eliminuje probl�my s fullscreenem, kdy se po n�vratu do syst�mu neobnovilo p�vodn� rozli�en� obrazovky.</p>

<p>Tento tutori�l je od za��tku zam��len pouze jako �vod do Bezierov�ch k�ivek, aby n�kdo mnohem �ikovn�j�� ne� j� dok�zal vytvo�it n�co opravdu skv�l�ho. Neberte ho jako kompletn� Bezier knihovnu, ale sp�e jako koncept, jak tyto k�ivky pracuj� a co dok��. Tak� pros�m omluvte mou, v n�kter�ch p��padech, ne a� tak spr�vnou terminologii. Douf�m, �e bude alespo� trochu srozumiteln�. Abych tak �ekl: Nikdo nen� dokonal�...</p>

<p>Pochopit Bezierovy k�ivky s nulov�mi znalostmi matematiky je nemo�n�. Proto bude n�sledovat mali�ko del�� sekce teorie, kter� by v�s m�la do problematiky alespo� trochu zasv�tit. Pokud v�echno u� zn�te, nic v�m nebr�n� tuto nut(d)nou sekci p�esko�it a v�novat se k�du.</p>

<p>Bezierovy k�ivky b�vaj� prim�rn� metodou, jak v grafick�ch editorech �i oby�ejn�ch programech vykreslovat zak�iven� linky. Jsou obvykle reprezentov�ny s�ri� bod�, z nich ka�d� dva reprezentuj� te�nu ke grafu funkce.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_28_krivka1.gif" width="260" height="110" alt="Bezierova k�ivka" /></div>

<p>Toto je nejjednodu��� mo�n� Bezierova k�ivka. Del�� jsou tvo�eny spojen�m n�kolika dohromady. Je tvo�ena pouze �ty�mi body, dva konce a dva st�edov� kontroln� body. Pro po��ta� jsou v�echny �pln� stejn�, ale abychom si pomohli, spojujeme prvn� a posledn� dva. Linky budou v�dy te�nami k ukon�ovac�m bod�m. Parametrick� k�ivky jsou kresleny nalezen�m libovoln�ho po�tu bod� rovnom�rn� rozprost�en�ch po k�ivce, kter� se spoj� ��rami. Po�tem bod� m��eme ovl�dat hranatost k�ivky a samoz�ejm� tak� dobu trv�n� v�po�t�. Poda��-li se n�m mno�stv� bod� spr�vn� regulovat, pozorovatel v ka�d�m okam�iku uvid� perfektn� zak�iven� povrch bez trh�n� animace.</p>

<p>V�echny Bezierovy k�ivky jsou v zalo�eny na z�kladn�m vzorci funkce. Komplikovan�j�� verze jsou z n�j odvozeny.</p>

<p class="src0">t + (1 - t) = 1</p>

<p>Vypad� jednodu�e? Ano, rovnice jednoduch� ur�it� je, ale nesm�me zapomenout na to, �e je to pouze Bezierova k�ivka prvn�ho stupn�. Pou�ijeme-li trochu terminologie: Bezierovy k�ivky jsou polynomi�ln� (mnoho�lenn�). Jak si zajist� pamatujete z algebry, prvn� stupe� z polynomu je p��mka - nic zaj�mav�ho. Z�kladn� funkce vych�z�, dosad�me-li libovoln� ��slo t. Rovnici m��eme ov�em tak� mocnit na druhou, na t�et�, na jak�koli ��slo, proto�e se ob� strany rovnaj� jedn�. Zkus�me ji tedy umocnit na t�et�.</p>

<p class="src0">(t + (1 - t))<sup>3</sup> = 1<sup>3</sup></p>
<p class="src0">t<sup>3</sup> + 3t<sup>2</sup>(1 - t) + 3t(1 - t)<sup>2</sup> + (1 - t)<sup>3</sup> = 1</p>

<p>Tuto rovnici pou�ijeme k v�po�tu mnohem v�ce pou��van�j�� k�ivky - Bezierovy k�ivky t�et�ho stupn�. Pro toto rozhodnut� existuj� dva d�vody:</p>

<ul>
<li>Tento polynomi�l je nejni���ho mo�n�ho stupn�, kdy u� k�ivka nemus� le�et v rovin�, ale i v prostoru.</li>
<li>Te�ny k funkci u� nejsou z�visl� na jin�ch (k�ivky 2. stupn� mohou m�t pouze t�i kontroln� body, my pot�ebujeme �ty�i).</li>
</ul>

<p>Zb�v� ale dodat je�t� jedna v�c... Cel� lev� strana rovnice se rovn� jedn�, tak�e je bezpe�n� p�edpokl�dat, �e pokud p�id�me v�echny slo�ky m�la by se st�le rovnat jedn�. Zn� to, jako by to mohlo b�t pou�ito k rozhodnut� kolik z ka�d�ho kontroln�ho bodu lze pou��t p�i v�po�tu bodu na k�ivce? (n�pov�da: Prost� �ekni ano ;-) Ano. Spr�vn�! Pokud chceme spo��tat hodnotu bodu v procentech vzd�lenosti na k�ivce, jednodu�e n�sob�me ka�dou slo�ku kontroln�m bodem (stejn� jako vektor) a nalezneme sou�et. Obecn� budeme pracovat s hodnotami 0 &gt;= t &gt;= 1, ale nen� to technicky nutn�. Dokonale zmateni? Rad�ji nap�u tu funkci.</p>

<p class="src0">P1*t<sup>3</sup> + P2*3*t<sup>2</sup>*(1-t) + P3*3*t*(1-t)<sup>2</sup> + P4*(1-t)<sup>3</sup> = P<sub>new</sub></p>

<p>Proto�e jsou polynomi�ly v�dy spojit�, jsou dobrou cestou k pohybu mezi �ty�mi body. M��eme dos�hnout ale v�dy pouze okrajov�ch bod� (P1 a P4). Pokud tuto v�tu nech�pete, pod�vejte se na prvn� obr�zek. V t�chto p��padech se t = 0 pop�. t = 1.</p>

<p>To je sice hezk�, ale jak m�m pou��t Bezierovy k�ivky ve 3D? Je to docela jednoduch�. Pot�ebujeme 16 kontroln�ch bod� (4x4) a dv� prom�nn� t a v. Vytvo��me z nich �ty�i paraleln� k�ivky. Na ka�d� z nich spo��t�me jeden bod p�i ur�it�m v a pou�ijeme tyto �ty�i body k vytvo�en� nov� k�ivky a spo��t�me t. Nalezen�m v�ce bod� m��eme nakreslit triangle strip a t�m zobrazit Bezier�v povrch.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_28_krivka2.gif" width="280" height="145" alt="Bezier�v povrch" />
<img src="images/nehe_tut/tut_28_krivka3.jpg" width="280" height="145" alt="Princip vytv��en� Bezierova povrchu" />
</div>

<p>P�epokl�d�m, �e matematiky u� bylo dost. Poj�me se vrhnout na k�d t�to lekce. ( Ze v�eho nejd��ve vytvo��me struktury. POINT_3D je oby�ejn� bod ve t��rozm�rn�m prostoru. Druh� struktura je u� trochu zaj�mav�j�� - p�edstavuje Bezier�v povrch. Anchors[4][4] je dvourozm�rn� pole 16 ��d�c�ch bod�. Do display listu dlBPatch ulo��me v�sledn� model a texture ukl�d� texturu, kterou na n�j namapujeme.</p>

<p class="src0">typedef struct point_3d<span class="kom">// Struktura bodu</span></p>
<p class="src0">{</p>
<p class="src1">double x, y, z;</p>
<p class="src0">} POINT_3D;</p>
<p class="src"></p>
<p class="src0">typedef struct bpatch<span class="kom">// Struktura Bezierova povrchu</span></p>
<p class="src0">{</p>
<p class="src1">POINT_3D anchors[4][4];<span class="kom">// M��ka ��d�c�ch bod� (4x4)</span></p>
<p class="src1">GLuint dlBPatch;<span class="kom">// Display list</span></p>
<p class="src1">GLuint texture;<span class="kom">// Textura</span></p>
<p class="src0">} BEZIER_PATCH;</p>

<p>Mybezier je objektem pr�v� vytvo�en� textury, rotz kontroluje �hel nato�en� sc�ny. ShowCPoints indikuje, jestli vykreslujeme m��ku mezi ��d�c�mi body nebo ne. Divs ur�uje hladkost (hranatost) v�sledn�ho povrchu.</p>

<p class="src0">BEZIER_PATCH mybezier;<span class="kom">// Bezier�v povrch</span></p>
<p class="src"></p>
<p class="src0">GLfloat rotz = 0.0f;<span class="kom">// Rotace na ose z</span></p>
<p class="src0">BOOL showCPoints = TRUE;<span class="kom">// Flag pro zobrazen� m��ky mezi kontroln�mi body</span></p>
<p class="src0">int divs = 7;<span class="kom">// Po�et interpolac� (mno�stv� vykreslovan�ch polygon�)</span></p>

<p>Jestli si pamatujete, tak v �vodu jsem psal, �e budeme mali�ko upravovat k�d pro vytv��en� okna tak, aby se p�i n�vratu z fullscreenu obnovilo p�vodn� rozli�en� obrazovky (n�kter� grafick� karty s t�m maj� probl�my). DMsaved ukl�d� p�vodn� nastaven� monitoru p�ed vstupem do fullscreenu.</p>

<p class="src0">DEVMODE DMsaved;<span class="kom">// Ukl�d� p�vodn� nastaven� monitoru</span></p>

<p>N�sleduje n�kolik pomocn�ch funkc� pro jednoduchou vektorovou matematiku. S��t�n�, n�soben� a vytv��en� 3D bod�. Nic slo�it�ho.</p>

<p class="src0">POINT_3D pointAdd(POINT_3D p, POINT_3D q)<span class="kom">// S��t�n� dvou bod�</span></p>
<p class="src0">{</p>
<p class="src1">p.x += q.x;</p>
<p class="src1">p.y += q.y;</p>
<p class="src1">p.z += q.z;</p>
<p class="src"></p>
<p class="src1">return p;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">POINT_3D pointTimes(double c, POINT_3D p)<span class="kom">// N�soben� bodu konstantou</span></p>
<p class="src0">{</p>
<p class="src1">p.x *= c;</p>
<p class="src1">p.y *= c;</p>
<p class="src1">p.z *= c;</p>
<p class="src"></p>
<p class="src1">return p;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">POINT_3D makePoint(double a, double b, double c)<span class="kom">// Vytvo�en� bodu ze t�� ��sel</span></p>
<p class="src0">{</p>
<p class="src1">POINT_3D p;</p>
<p class="src"></p>
<p class="src1">p.x = a;</p>
<p class="src1">p.y = b;</p>
<p class="src1">p.z = c;</p>
<p class="src"></p>
<p class="src1">return p;</p>
<p class="src0">}</p>

<p>Funkc� Bernstein() po��t�me bod, kter� le�� na Bezierov� k�ivce. V parametrech j� p�ed�v�me prom�nnou u, kter� specifikuje procentu�ln� vzd�lenost bodu od okraje k�ivky vzhledem k jej� d�lce a pole �ty� bod�, kter� jednozna�n� definuj� k�ivku. V�cen�sobn�m vol�n�m a krokov�n�m u v�dy o stejn� p��r�stek m��eme z�skat aproximaci k�ivky.</p>

<p class="src0">POINT_3D Bernstein(float u, POINT_3D *p)<span class="kom">// Spo��t� sou�adnice bodu le��c�ho na k�ivce</span></p>
<p class="src0">{</p>
<p class="src1">POINT_3D a, b, c, d, r;<span class="kom">// Pomocn� prom�nn�</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�po�et podle vzorce</span></p>
<p class="src1">a = pointTimes(pow(u,3), p[0]);</p>
<p class="src1">b = pointTimes(3 * pow(u,2) * (1-u), p[1]);</p>
<p class="src1">c = pointTimes(3 * u * pow((1-u), 2), p[2]);</p>
<p class="src1">d = pointTimes(pow((1-u), 3), p[3]);</p>
<p class="src"></p>
<p class="src1">r = pointAdd(pointAdd(a, b), pointAdd(c, d));<span class="kom">// Se�ten� n�sobk� a, b, c, d</span></p>
<p class="src1"></p>
<p class="src1">return r;<span class="kom">// Vr�cen� v�sledn�ho bodu</span></p>
<p class="src0">}</p>

<p>Nejv�t�� ��st pr�ce odv�d� funkce genBezier(). Spo��t� k�ivky, vygeneruje triangle strip a v�sledek ulo�� do display listu. Pou�it� display listu je v tomto p��pad� v�ce ne� vhodn�, proto�e nemus�me prov�d�t slo�it� v�po�ty p�i ka�d�m framu, ale pouze p�i zm�n�ch vy��dan�ch u�ivatelem. Odstran� se t�m zbyte�n� zat�en� procesoru. Funkci p�ed�v�me strukturu BEZIER_PATCH, v n� jsou ulo�eny v�echny pot�ebn� ��d�c� body. Divs ur�uje kolikr�t budeme prov�d�t v�po�ty - ovl�d� hranatost v�sledn�ho modelu. N�sleduj�c� obr�zky jsou z�sk�ny p�epnut�m do re�imu vykreslov�n� linek m�sto polygon� (glPolygonMode(GL_FRONT_AND_BACK, GL_LINES)) a zak�z�n�m textur. Jasn� je vid�t, �e ��m je ��slo v divs v�t��, t�m je objekt zaoblen�j��.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_28_bezier1.gif" width="200" height="183" alt="Dr�tov� model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier2.gif" width="200" height="183" alt="Dr�tov� model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier3.gif" width="200" height="183" alt="Dr�tov� model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier4.gif" width="200" height="183" alt="Dr�tov� model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier5.gif" width="200" height="183" alt="Dr�tov� model Bezierova povrchu" />
<img src="images/nehe_tut/tut_28_bezier6.gif" width="200" height="183" alt="Dr�tov� model Bezierova povrchu" />
</div>

<p class="src0">GLuint genBezier(BEZIER_PATCH patch, int divs)<span class="kom">// Generuje display list Bezierova povrchu</span></p>
<p class="src0">{</p>

<p>Prom�nn� u, v ��d� cykly generuj�c� jednotliv� body na Bezierov� k�ivce a py, px, pyold jsou jejich procentu�ln� hodnoty, kter� slou�� k ur�en� m�sta na k�ivce. Nab�vaj� hodnot v intervalu od 0 do 1, tak�e je m��eme bez komplikac� pou��t i jako texturovac� koordin�ty. Drawlist je display list, do kter�ho kresl�me v�sledn� povrch. Do temp ulo��me �ty�i body pro z�sk�n� pomocn� Bezierovy k�ivky. Dynamick� pole last ukl�d� minul� ��dek bod�, proto�e pro triangle strip pot�ebujeme dva ��dky.</p>

<p class="src1">int u = 0, v;<span class="kom">// ��d�c� prom�nn�</span></p>
<p class="src1">float py, px, pyold;<span class="kom">// Procentu�ln� hodnoty</span></p>
<p class="src"></p>
<p class="src1">GLuint drawlist = glGenLists(1);<span class="kom">// Display list</span></p>
<p class="src"></p>
<p class="src1">POINT_3D temp[4];<span class="kom">// ��d�c� body pomocn� k�ivky</span></p>
<p class="src1">POINT_3D* last = (POINT_3D*) malloc(sizeof(POINT_3D) * (divs+1));<span class="kom">// Prvn� �ada polygon�</span></p>
<p class="src"></p>
<p class="src1">if (patch.dlBPatch != NULL)<span class="kom">// Pokud existuje star� display list</span></p>
<p class="src2">glDeleteLists(patch.dlBPatch, 1);<span class="kom">// Sma�eme ho</span></p>
<p class="src"></p>
<p class="src1">temp[0] = patch.anchors[0][3];<span class="kom">// Prvn� odvozen� k�ivka (osa x)</span></p>
<p class="src1">temp[1] = patch.anchors[1][3];</p>
<p class="src1">temp[2] = patch.anchors[2][3];</p>
<p class="src1">temp[3] = patch.anchors[3][3];</p>
<p class="src"></p>
<p class="src1">for (v = 0; v &lt;= divs; v++)<span class="kom">// Vytvo�� prvn� ��dek bod�</span></p>
<p class="src1">{</p>
<p class="src2">px = ((float)v) / ((float)divs);<span class="kom">// Px je procentu�ln� hodnota v</span></p>
<p class="src2">last[v] = Bernstein(px, temp);<span class="kom">// Spo��t� bod na k�ivce ve vzd�lenosti px</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glNewList(drawlist, GL_COMPILE);<span class="kom">// Nov� display list</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, patch.texture);<span class="kom">// Zvol� texturu</span></p>

<p>Vn�j�� cyklus proch�z� ��dky a vnit�n� jednotliv� sloupce. Nebo to m��e b�t i naopak. Z�le�� na tom, co si ka�d� p�edstav� pod pojmy ��dek a sloupec :-)</p>

<p class="src2">for (u = 1; u &lt;= divs; u++)<span class="kom">// Proch�z� body na k�ivce</span></p>
<p class="src2">{</p>
<p class="src3">py  = ((float)u) / ((float)divs);<span class="kom">// Py je procentu�ln� hodnota u</span></p>
<p class="src3">pyold = ((float)u - 1.0f) / ((float)divs);<span class="kom">// Pyold m� hodnotu py p�i minul�m pr�chodu cyklem</span></p>

<p>V ka�d�m prvku pole patch.anchors[] m�me ulo�eny �ty�i ��d�c� body (dvourozm�rn� pole). Cel� pole dohromady tvo�� �ty�i paraleln� k�ivky, kter� si ozna��me jako ��dky. Nyn� spo��t�me body, kter� jsou um�st�ny na v�ech �ty�ech k�ivk�ch ve stejn� vzd�lenosti py a ulo��me je do pole temp[], kter� p�edstavuje sloupec v ��dku a celkov� tvo�� �ty�i ��d�c� body nov� k�ivky pro sloupec.</p>

<p>Celou akci si p�edstavte jako trochu komplikovan�j�� proch�zen� dvourozm�rn�ho pole - vn�j�� cyklus proch�z� ��dky a vnit�n� sloupce. Z upraven�ch ��d�c�ch prom�nn�ch si vyb�r�me pozice bod� a texturovac� koordin�ty. Py s pyold p�edstavuje dva &quot;rovn�b�n�&quot; ��dky a px sloupec. (P�ekl.: Ne� jsem tohle pochopil... v origin�le o tom nebyla ani zm�nka).</p>

<p class="src3">temp[0] = Bernstein(py, patch.anchors[0]);<span class="kom">// Spo��t� Bezierovy body pro k�ivku</span></p>
<p class="src3">temp[1] = Bernstein(py, patch.anchors[1]);</p>
<p class="src3">temp[2] = Bernstein(py, patch.anchors[2]);</p>
<p class="src3">temp[3] = Bernstein(py, patch.anchors[3]);</p>
<p class="src"></p>
<p class="src3">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Za��tek kreslen� triangle stripu</span></p>
<p class="src4">for (v = 0; v &lt;= divs; v++)<span class="kom">// Proch�z� body na k�ivce</span></p>
<p class="src4">{</p>
<p class="src5">px = ((float)v) / ((float)divs);<span class="kom">// Px je procentu�ln� hodnota v</span></p>
<p class="src"></p>
<p class="src5">glTexCoord2f(pyold, px);<span class="kom">// Texturovac� koordin�ty z minul�ho pr�chodu</span></p>
<p class="src5">glVertex3d(last[v].x, last[v].y, last[v].z);<span class="kom">// Bod z minul�ho pr�chodu</span></p>

<p>Do pole last nyn� ulo��me nov� hodnoty, kter� se p�i dal��m pr�chodu cyklem stanou op�t star�mi.</p>

<p class="src5">last[v] = Bernstein(px, temp);<span class="kom">// Generuje nov� bod</span></p>
<p class="src"></p>
<p class="src5">glTexCoord2f(py, px);<span class="kom">// Nov� texturov� koordin�ty</span></p>
<p class="src5">glVertex3d(last[v].x, last[v].y, last[v].z);<span class="kom">// Nov� bod</span></p>
<p class="src4">}</p>
<p class="src3">glEnd();<span class="kom">// Konec triangle stripu</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src1">glEndList();<span class="kom">// Konec display listu</span></p>
<p class="src"></p>
<p class="src1">free(last);<span class="kom">// Uvoln� dynamick� pole vertex�</span></p>
<p class="src1">return drawlist;<span class="kom">// Vr�t� pr�v� vytvo�en� display list</span></p>
<p class="src0">}</p>

<p>Jedin� v�c, kterou ned�l�me, ale kter� by se ur�it� mohla hodit, jsou norm�lov� vektory pro sv�tlo. Kdy� na n� p�ijde, m�me dv� mo�nosti. V prvn� nalezneme st�ed ka�d�ho troj�heln�ku, aplikujeme na n�j n�kolik v�po�tu k z�sk�n�m te�en k Bezierov� k�ivce na os�ch x a y, vektorov� je vyn�sob�me a t�m z�sk�me vektor kolm� sou�asn� k ob�ma te�n�m. Po normalizov�n� ho m��eme pou��t jako norm�lu. Druh� zp�sob je rychlej�� a jednodu���, ale m�n� p�esn�. M��eme cheatovat a pou��t norm�lov� vektor troj�heln�ku (spo��tan� libovoln�m zp�sobem). T�m z�sk�me docela dobrou aproximaci. Osobn� preferuji druhou, jednodu��� cestu, kter� ov�em nevypad� tak realistiky.</p>

<p>Ve funkci initBezier() inicializujeme matici kontroln�ch bod� na v�choz� hodnoty. Pohrajte si s nimi, a� vid�te, jak jednodu�e se daj� m�nit tvary povrch�.</p>

<p class="src0">void initBezier(void)<span class="kom">// Po��te�n� nastaven� kontroln�ch bod�</span></p>
<p class="src0">{</p>
<p class="src1">mybezier.anchors[0][0] = makePoint(-0.75,-0.75,-0.5);</p>
<p class="src1">mybezier.anchors[0][1] = makePoint(-0.25,-0.75, 0.0);</p>
<p class="src1">mybezier.anchors[0][2] = makePoint( 0.25,-0.75, 0.0);</p>
<p class="src1">mybezier.anchors[0][3] = makePoint( 0.75,-0.75,-0.5);</p>
<p class="src1">mybezier.anchors[1][0] = makePoint(-0.75,-0.25,-0.75);</p>
<p class="src1">mybezier.anchors[1][1] = makePoint(-0.25,-0.25, 0.5);</p>
<p class="src1">mybezier.anchors[1][2] = makePoint( 0.25,-0.25, 0.5);</p>
<p class="src1">mybezier.anchors[1][3] = makePoint( 0.75,-0.25,-0.75);</p>
<p class="src1">mybezier.anchors[2][0] = makePoint(-0.75, 0.25, 0.0);</p>
<p class="src1">mybezier.anchors[2][1] = makePoint(-0.25, 0.25,-0.5);</p>
<p class="src1">mybezier.anchors[2][2] = makePoint( 0.25, 0.25,-0.5);</p>
<p class="src1">mybezier.anchors[2][3] = makePoint( 0.75, 0.25, 0.0);</p>
<p class="src1">mybezier.anchors[3][0] = makePoint(-0.75, 0.75,-0.5);</p>
<p class="src1">mybezier.anchors[3][1] = makePoint(-0.25, 0.75,-1.0);</p>
<p class="src1">mybezier.anchors[3][2] = makePoint( 0.25, 0.75,-1.0);</p>
<p class="src1">mybezier.anchors[3][3] = makePoint( 0.75, 0.75,-0.5);</p>
<p class="src"></p>
<p class="src1">mybezier.dlBPatch = NULL;<span class="kom">// Display list je�t� neexistuje</span></p>
<p class="src0">}</p>

<p>InitGL() je celkem standardn�. Na jej�m konci zavol�me funkce pro inicializaci kontroln�ch bod�, nahr�n� textury a vygenerov�n� display listu Bezierova povrchu.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturov�n�</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testov�n� hloubky</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivn� korekce</span></p>
<p class="src"></p>
<p class="src1">initBezier();<span class="kom">// Inicializace kontroln�ch bod�</span></p>
<p class="src1">LoadGLTexture(&amp;(mybezier.texture), &quot;./data/NeHe.bmp&quot;);<span class="kom">// Loading textury</span></p>
<p class="src1">mybezier.dlBPatch = genBezier(mybezier, divs);<span class="kom">// Generuje display list Bezierova povrchu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace v po��dku</span></p>
<p class="src0">}</p>

<p>Vykreslov�n� nen� oproti minul�m tutori�l�m v�bec slo�it�. Po v�ech translac�ch a rotac�ch zavol�me display list a potom p��padn� propoj�me ��d�c� body �erven�mi �arami. Chcete-li linky zapnout nebo vypnout stiskn�te mezern�k.</p>


<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// V�echno kreslen�</span></p>
<p class="src0">{</p>
<p class="src1">int i, j;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -4.0f);<span class="kom">// P�esun do hloubky</span></p>
<p class="src1">glRotatef(-75.0f, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(rotz, 0.0f, 0.0f, 1.0f);<span class="kom">// Rotace na ose z</span></p>
<p class="src"></p>
<p class="src1">glCallList(mybezier.dlBPatch);<span class="kom">// Vykresl� display list Bezierova povrchu</span></p>
<p class="src"></p>
<p class="src1">if (showCPoints)<span class="kom">// Pokud je zapnut� vykreslov�n� m��ky</span></p>
<p class="src1">{</p>
<p class="src2">glDisable(GL_TEXTURE_2D);<span class="kom">// Vypne texturov�n�</span></p>
<p class="src2">glColor3f(1.0f, 0.0f, 0.0f);<span class="kom">// �erven� barva</span></p>
<p class="src"></p>
<p class="src2">for(i = 0; i &lt; 4; i++)<span class="kom">// Horizont�ln� linky</span></p>
<p class="src2">{</p>
<p class="src3">glBegin(GL_LINE_STRIP);<span class="kom">// Kreslen� linek</span></p>
<p class="src4">for(j = 0; j &lt; 4; j++)<span class="kom">// �ty�i linky</span></p>
<p class="src4">{</p>
<p class="src5">glVertex3d(mybezier.anchors[i][j].x, mybezier.anchors[i][j].y, mybezier.anchors[i][j].z);</p>
<p class="src4">}</p>
<p class="src3">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">for(i = 0; i &lt; 4; i++)<span class="kom">// Vertik�ln� linky</span></p>
<p class="src2">{</p>
<p class="src3">glBegin(GL_LINE_STRIP);<span class="kom">// Kreslen� linek</span></p>
<p class="src4">for(j = 0; j &lt; 4; j++)<span class="kom">// �ty�i linky</span></p>
<p class="src4">{</p>
<p class="src5">glVertex3d(mybezier.anchors[j][i].x, mybezier.anchors[j][i].y, mybezier.anchors[j][i].z);</p>
<p class="src4">}</p>
<p class="src3">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">glColor3f(1.0f, 1.0f, 1.0f);<span class="kom">// B�l� barva</span></p>
<p class="src2">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturov�n�</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V po��dku</span></p>
<p class="src0">}</p>

<p>Pr�ci s Bezierov�mi k�ivkami jsme �sp�n� dokon�ili, ale je�t� nesm�me zapomenout na fullscreen fix. Odstra�uje probl�m s p�ep�n�m z fullscreenu do okenn�ho m�du, kdy n�kter� grafick� karty spr�vn� neobnovuj� p�vodn� rozli�en� obrazovky (nap�. moje sta�i�k� ATI Rage PRO a n�kolik dal��ch). Douf�m, �e budete pou��vat tento pozm�n�n� k�d, aby si ka�d� mohl bez komplikac� vychutnat va�e skv�l� OpenGL dema. V tutori�lu jsme provedli celkem t�i zm�ny. Prvn� p�i deklaraci prom�nn�ch, kdy jsme vytvo�ili prom�nnou DEVMODE DMsaved. Druhou najdete v CreateGLWindow(), kde jsme tuto pomocnou strukturu naplnili informacemi o aktu�ln�m nastaven�. T�et� zm�na je v KillGLWindow(), kde se obnovuje p�vodn� ulo�en� nastaven�.</p>

<p class="src0">BOOL CreateGLWindow(char* title, int width, int height, int bits, bool fullscreenflag)<span class="kom">// Vytv��en� okna</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Deklarace prom�nn�ch</span></p>
<p class="src"></p>
<p class="src1">EnumDisplaySettings(NULL, ENUM_CURRENT_SETTINGS, &amp;DMsaved);<span class="kom">// Ulo�� aktu�ln� nastaven� obrazovky</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�e ostatn� z�st�v� stejn�</span></p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">GLvoid KillGLWindow(GLvoid)<span class="kom">// Zav�en� okna</span></p>
<p class="src0">{</p>
<p class="src1">if (fullscreen)<span class="kom">// Jsme ve fullscreenu?</span></p>
<p class="src1">{</p>
<p class="src2">if (!ChangeDisplaySettings(NULL, CDS_TEST))<span class="kom">// Pokud pokusn� zm�na nefunguje</span></p>
<p class="src2">{</p>
<p class="src3">ChangeDisplaySettings(NULL, CDS_RESET);<span class="kom">// Odstran� hodnoty z registr�</span></p>
<p class="src3">ChangeDisplaySettings(&amp;DMsaved, CDS_RESET);<span class="kom">// Pou�ije ulo�en� nastaven�</span></p>
<p class="src2">}</p>
<p class="src2">else</p>
<p class="src2">{</p>
<p class="src3">ChangeDisplaySettings(NULL, CDS_RESET);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">ShowCursor(TRUE);<span class="kom">// Zobraz� ukazatel my�i</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�e ostatn� z�st�v� stejn�</span></p>
<p class="src0">}</p>

<p>Posledn� v�c� jsou u� standardn� testy stisku kl�ves.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src3">if (keys[VK_LEFT])<span class="kom">// �ipka doleva</span></p>
<p class="src3">{</p>
<p class="src4">rotz -= 0.8f;<span class="kom">// Rotace doleva</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_RIGHT])<span class="kom">// �ipka doprava</span></p>
<p class="src3">{</p>
<p class="src4">rotz += 0.8f;<span class="kom">// Rotace doprava</span></p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_UP])<span class="kom">// �ipka nahoru</span></p>
<p class="src3">{</p>
<p class="src4">divs++;<span class="kom">// Men�� hranatost povrchu</span></p>
<p class="src4">mybezier.dlBPatch = genBezier(mybezier, divs);<span class="kom">// Aktualizace display listu</span></p>
<p class="src4">keys[VK_UP] = FALSE;</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_DOWN] &amp;&amp; divs &gt; 1)<span class="kom">// �ipka dol�</span></p>
<p class="src3">{</p>
<p class="src4">divs--;<span class="kom">// V�t�� hranatost povrchu</span></p>
<p class="src4">mybezier.dlBPatch = genBezier(mybezier, divs);<span class="kom">// Aktualizace display listu</span></p>
<p class="src4">keys[VK_DOWN] = FALSE;</p>
<p class="src3">}</p>
<p class="src"></p>
<p class="src3">if (keys[VK_SPACE])<span class="kom">// Mezern�k</span></p>
<p class="src3">{</p>
<p class="src4">showCPoints = !showCPoints;<span class="kom">// Zobraz�/skryje linky mezi ��d�c�mi body</span></p>
<p class="src4">keys[VK_SPACE] = FALSE;</p>
<p class="src3">}</p>

<p>Douf�m, �e pro v�s byl tento tutori�l pou�n� a �e od nyn�j�ka miluje Bezierovy k�ivky stejn� jako j� ;-) Je�t� jsem se o tom nezm�nil, ale mnoh� z v�s jist� napadlo, �e se s nimi d� vytvo�it perfektn� morfovac� efekt. A velmi jednodu�e! Nezapome�te, se m�n� poloha pouze �estn�cti bod�. Zkuste o tom pop�em��let...</p>

<p class="autor">napsal: David Nikdel <?VypisEmail('ogapo@ithink.net');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul>
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson28.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson28_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson28.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson28.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson28.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson28.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson28.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson28.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:rodolphe.suescun@wanadoo.fr">Rodolphe Suescun</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson28.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson28.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(28);?>
<?FceNeHeOkolniLekce(28);?>

<?
include 'p_end.php';
?>
