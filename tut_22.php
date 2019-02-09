<?
$g_title = 'CZ NeHe OpenGL - Lekce 22 - Bump Mapping &amp; Multi Texturing';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(22);?>

<h1>Lekce 22 - Bump Mapping &amp; Multi Texturing</h1>

<p class="nadpis_clanku">Prav� �as vr�tit se zp�tky na za��tek a za��t si opakovat. Nov��k�m v OpenGL se absolutn� nedoporu�uje! Pokud, ale m�te odvahu, m��ete zkusit dobrodru�stv� s nadupanou grafikou. V t�to lekci modifikujeme k�d z �est� lekce, aby podporoval hardwarov� multi texturing p�es opravdu skv�l� vizu�ln� efekt nazvan� bump mapping.</p>

<p>P�i p�ekladu t�to lekce jsem zva�oval zda m�m n�kter� term�ny p�ekl�dat do �e�tiny. Ale vzhledem k tomu, �e jsou to v�t�inou n�zvy, kter� se b�n� v oboru po��ta�ov� grafiky objevuj�, rozhodl jsem se nechat je v p�vodn�m zn�n�. Aby v�ak i ti, kte�� se s nimi setk�vaj� poprv�, v�d�li o �em je �e�, tak je zde v rychlosti vysv�tl�m:</p>

<p><b>OpenGL extension</b> je funkce, kter� nen� v b�n� specifikaci OpenGL dostupn�, ale kv�li nov�m mo�nostem grafick�ch akceler�tor� a nov�m postup�m p�i programov�n� byla do OpenGL dodate�n� p�id�na. Tyto funkce ve sv�m n�zvu obsahuj� EXT nebo ARB. Firmy se samoz�ejm� sna��, aby jejich akceler�tor podporoval t�chto roz���en� co nejv�ce, proto�e mnoh� z nich zrychluj� pr�ci, p�id�vaj� nov� mo�nosti nebo zvy�uj� v�kon.</p>

<p><b>Bumpmapa</b> je textura, kter� obsahuje informace o reli�fu. V�t�inou b�v� ve stupn�ch �edi, kde tmav� m�sta ud�vaj� vyv��eniny a sv�tl� r�hy, nebo naopak - to z�le�� na program�torovi.</p>

<p><b>Emboss bumpmapping</b> je postup vytv��en� reli�fovan�ch textur, u kter�ch se zd�, �e jsou tvarovan� i do hloubky - hlavn� t�ma t�to lekce.</p>

<p><b>Alpha kan�l</b> je posledn� slo�ka RGBA barvy, kter� obsahuje informace o pr�hlednosti. Pokud je alpha maxim�ln� (255 nebo 1.0f), tak nen� objekt v�bec pr�hledn�. Pokud je alpha nulov� je objekt neviditeln�.</p>

<p><b>Blending</b> je m�ch�n� alpha kan�lu s barevnou texturou. Dociluje se j�m pr�hlednosti.</p>

<p><b>Artefakt</b> je n�jak� vizu�ln� prvek, kter� by se v renderovan� sc�n� nem�l objevovat. Nicm�n� vzhledem k tomu, �e postupy, kter� by je nezanech�valy jsou v�t�inou velmi pomal�, mus� se pou��vat jin�, kter� na �kor kvality zv��� rychlost renderov�n�.</p>

<p>Dal�� n�zvy typu <b>vertex</b>, <b>pipeline</b>, ... by m�ly b�t dob�e zn�m� z p�edchoz�ch tutori�l�.</p>

<p>Douf�m, �e V�m p�eklad i t�ma budou srozumiteln� a �e V�m pomohou vytv��et kvalitn� OpenGL aplikace. Pokud byste narazili na n�jak� probl�m, nen� nic jednodu���ho ne� poslat emailem dotaz. R�d V�m na v�echny ot�zky odpov�m, p��padn� oprav�m nedostatky v textu.</p>

<p>Tato lekce byla naps�na Jensem Schneiderem. Voln� vych�z� z 6. lekce, i kdy� vzniklo mnoho zm�n. Nau��te se zde:</p>
<ul>
<li>Jak ovl�dat multitexturovac� mo�nosti grafick�ho akceler�toru.</li>
<li>Jak vytvo�it zd�n� emdoss bumpmappingu (reli�f na textur�ch).</li>
<li>Jak ud�lat pomoc� blendingu profesion�ln� vypadaj�c� loga, kter� &quot;pluj�&quot; nad renderovanou sc�nou.</li>
<li>Z�klady multi-pass (n�kolika f�zov�ch) renderovac�ch technik.</li>
<li>Jak vyu��vat efektivn� transformace matice.</li>
</ul>

<p>Nejm�n� t�i z v��e uveden�ch bod� mohou b�t pova�ov�ny za &quot;pokro�il� renderovac� techniky&quot;. M�li byste m�t ji� z�kladn� p�edstavu o tom, jak funguje renderovac� pipeline OpenGL. M�li byste zn�t v�t�inu p��kaz� u�it�ch v tutori�lu a m�li byste b�t obezn�meni s vektorovou matematikou. Sekce, kter� za��naj� slovy "za��tek teorie(...)" a kon�� slovy "konec teorie(...)", se sna�� vysv�tlit problematiku uvedenou v z�vork�ch. Tohle je zde jen pro jistotu. Pokud danou problematiku zn�te, m��ete tyto ��sti jednodu�e p�esko�it. Pokud budete m�t probl�my s porozum�n�m k�du, zva�te n�vrat zp�t k teoretick�m ��stem textu. Posledn�, ale nem�n� d�le�it�: Tato lekce obsahuje v�ce ne� 1 200 ��dek k�du a velk� ��st z nich je nejen nudn�, ale i dob�e zn�m� t�m, kte�� �etli p�edchoz� tutori�ly. Proto nebudu komentovat ka�d� ��dek, ale jen podstatu t�to lekce. Pokud naraz�te na n�co jako <b>&gt;-&lt;</b>, znamen� to, �e zde byly vynech�ny n�jak� nepodstatn� ��dky k�du.</p>

<p>Tak�e, jdeme na to:</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavi�kov� soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavi�kov� soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">#include &quot;glext.h&quot;<span class="kom">// Hlavi�kov� soubor pro multitexturing</span></p>
<p class="src"></p>
<p class="src0">#include &lt;string.h&gt;<span class="kom">// Hlavi�kov� soubor pro �et�zce</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavi�kov� soubor pro matematiku</span></p>

<p>GLfloat MAX_EMBOSS ud�v� &quot;s�lu&quot; bumpmappingu. Vy��� hodnoty hodn� zv�razn� efekt, ale stejn� tak sn��� kvalitu obrazu t�m, �e zanech�vaj� v roz�ch ploch takzvan� &quot;artefakty&quot;.</p>

<p class="src0">#define MAX_EMBOSS (GLfloat)0.008f<span class="kom">// Maxim�ln� posunut� efektem</span></p>

<p>Fajn, p�iprav�me se na pou�it� GL_ARB_multitexture. Je to celkem jednoduch�:</p>

<p>V�t�ina grafick�ch akceler�tor� m� dnes v�ce ne� jednu texturovac� jednotku. Abychom mohli t�to v�hody vyu��t, mus�me prov��it, zda akceler�tor podporuje GL_ARB_multitexture, kter� umo��uje namapovat dv� nebo v�ce textur na jeden �tvar p�i jednom pr�chodu pipeline. Nezn� to p��li� v�znamn�, ale opak je pravdou! Skoro v�dy kdy� n�co programujete, p�id�n�m dal�� textury na objekt, razantn� zv���te jeho vizu�ln� kvalitu. D��ve bylo nutno pou��t dv� prokl�dan� textury p�i v�cen�sobn�m vykreslov�n� geometrie, co� m��e v�st k velk�mu poklesu v�konu. D�le v tutori�lu bude multitexturing je�t� podrobn�ji pops�n.</p>

<p>Te� zp�t ke k�du: __ARB_ENABLE je u�ito pro ur�en� toho, zda chceme vyu��t multitexturingu, kdy� bude dostupn�. Pokud chcete poznat va�� kartou podporovan� OpenGL roz���en�, pouze odkomentujte #define EXT_INFO. D�le chceme prov��it podporu extensions p�i b�hu programu, abychom zajistili p�enositelnost k�du. Proto pot�ebujeme m�sto pro p�r �et�zc�. D�le chceme rozli�ovat mezi mo�nost� pou��vat extensions a samotn�m pou��v�n�m. Nakonec pot�ebujeme v�d�t, kolik texturovac�ch jednotek m�me k dispozici (pou�ijeme ale pouze dv�). Alespo� jedna texturovac� jednotka je v�dy p��tomna na akceler�toru podporuj�c�m OpenGL, tak�e nastav�me maxTexelUnits na hodnotu 1.</p>

<p class="src0">#define __ARB_ENABLE true<span class="kom">// Pou�ito pro vy�azen� multitexturingu</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">// #define EXT_INFO// Odkomentujte, pokud chcete p�i startu vid�t podporovan� roz���en� OpenGL
</span></p>
<p class="src"></p>
<p class="src0">#define MAX_EXTENSION_SPACE 10240<span class="kom">// M�sto pro �et�zce s OpenGL roz���en�mi</span></p>
<p class="src0">#define MAX_EXTENSION_LENGTH 256<span class="kom">// Maximum znak� v jednom �et�zci s roz���en�m</span></p>
<p class="src"></p>
<p class="src0">bool multitextureSupported = false;<span class="kom">// Indik�tor podpory multitexturingu</span></p>
<p class="src0">bool useMultitexture = true;<span class="kom">// Pou�it multitexturing?</span></p>
<p class="src"></p>
<p class="src0">GLint maxTexelUnits = 1;<span class="kom">// Po�et texturovac�ch jednotek - nejm�n� 1</span></p>

<p>N�sleduj�c� ��dky slou�� k tomu, aby spojily roz���en� s vol�n�m funkc� v C++. Pouze vyu�ijeme PNF-kdo-to-kdy-p�e�etl jako p�eddefinovan�ho datov�ho typu schopn�ho popsat vol�n� funkc�. Zpo��tku nen� jist�, zda z�sk�me p��stup k t�mto prototyp�m funkc�, tud�� je nastav�me na NULL. P��kazy glMultiTexCoordifARB odkazuj� na dob�e zn�m� p��kazy glTexCoordif(), ud�vaj�c� i-rozm�rn� sou�adnice textury. V�imn�te si, �e proto mohou �pln� nahradit p��kazy glTexCoordif. D��ve jsme pou��vali pouze verzi pro typ GLfloat, my pot�ebujeme pouze prototypy k p��kaz�m kon��c�m na "f" - ostatn� jsou potom taky dostupn� (fv, i, ...). Posledn� dva prototypy slou�� k ur�en� texturovac� jednotky, kter� bude p�ij�mat informace o textu�e (glActiveTextureARB()) a k ur�en�, kter� texturovac� jednotka je asociov�na s p��kazem ArrayPointer (glClientActiveTextureARB). Mimochodem: ARB je zkratkou &quot;Architectural Review Board&quot;. Roz���en� s ARB v n�zvu nejsou vy�adov�ny pro implementaci kompatibiln� s OpenGL, ale jsou �iroce vyu��v�ny a podporov�ny.</p>

<p class="src0">PFNGLMULTITEXCOORD1FARBPROC glMultiTexCoord1fARB = NULL;</p>
<p class="src0">PFNGLMULTITEXCOORD2FARBPROC glMultiTexCoord2fARB = NULL;</p>
<p class="src0">PFNGLMULTITEXCOORD3FARBPROC glMultiTexCoord3fARB = NULL;</p>
<p class="src0">PFNGLMULTITEXCOORD4FARBPROC glMultiTexCoord4fARB = NULL;</p>
<p class="src"></p>
<p class="src0">PFNGLACTIVETEXTUREARBPROC glActiveTextureARB = NULL;</p>
<p class="src0">PFNGLCLIENTACTIVETEXTUREARBPROC glClientActiveTextureARB = NULL;</p>

<p>Pot�ebujeme glob�ln� prom�nn�:</p>

<ul>
<li>filter - ud�v�, jak� filtr se m� pou��t. Pou�ijeme nejsp��e GL_LINEAR, tak�e filter inicializujeme ��slem 1.</li>
<li>texture - textury, pot�ebujeme 3 - na ka�d� filtr jednu</li>
<li>bump - bumpmapy</li>
<li>invbump - p�evr�cen� bump mapy - jejich v�znam je pops�n v jedn� z teoretick�ch ��st� t�to lekce</li>
<li>glLogo a multiLogo - vyu�ijeme pro textury, kter� budou p�id�ny do sc�ny v posledn� f�zi rendrov�n�</li>
<li>prom�nn� s Light v n�zvu - jsou pole nesouc� informace o osv�tlen� sc�ny</li>
</ul>

<p class="src0">GLuint filter=1;<span class="kom">// Jak� filtr pou��t</span></p>
<p class="src0">GLuint texture[3];<span class="kom">// M�sto pro t�i textury</span></p>
<p class="src"></p>
<p class="src0">GLuint bump[3];<span class="kom">// Na�e bumpmapy</span></p>
<p class="src0">GLuint invbump[3];<span class="kom">// Invertovan� bumpmapy</span></p>
<p class="src"></p>
<p class="src0">GLuint glLogo;<span class="kom">// M�sto pro OpenGL Logo</span></p>
<p class="src0">GLuint multiLogo;<span class="kom">// M�sto pro logo s multitexturingem</span></p>
<p class="src"></p>
<p class="src0">GLfloat LightAmbient[] = { 0.2f, 0.2f, 0.2f};<span class="kom">// Barva ambientn�ho sv�tla je 20% b�l�</span></p>
<p class="src0">GLfloat LightDiffuse[] = { 1.0f, 1.0f, 1.0f};<span class="kom">// Dif�zn� sv�tlo je b�l�</span></p>
<p class="src0">GLfloat LightPosition[] = { 0.0f, 0.0f, 2.0f};<span class="kom">// Pozice je n�kde uprost�ed sc�ny</span></p>
<p class="src"></p>
<p class="src0">GLfloat Gray[] = { 0.5f, 0.5f, 0.5f, 1.0f };<span class="kom">// Barva okraje textury</span></p>
<p class="src"></p>
<p class="src0">bool emboss = false;<span class="kom">// Jenom Emboss, ��dn� z�kladn� textura</span></p>
<p class="src0">bool bumps = true;<span class="kom">// Pou��vat bumpmapping?</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot;<span class="kom">// X rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y rotace</span></p>
<p class="src"></p>
<p class="src0">GLfloat xspeed;<span class="kom">// Rychlost x rotace</span></p>
<p class="src0">GLfloat yspeed;<span class="kom">// Rychlost y rotace</span></p>
<p class="src"></p>
<p class="src0">GLfloat z = -5.0f;<span class="kom">// Hloubka v obrazovce</span></p>

<p>Dal�� ��st k�du obsahuje sou�adnice kostky sestaven� z GL_QUADS. Ka�d�ch p�t ��sel reprezentuje jednu sadu 2D texturovac�ch sou�adnic a jednu sadu 3D vertexov�ch sou�adnic bodu. Data jsou uvedena v poli kv�li snaz��mu vykreslov�n� ve for smy�k�ch. B�hem jednoho renderovac�ho cyklu budeme tyto sou�adnice pot�ebovat v�cekr�t.</p>

<p class="src0">GLfloat data[] =</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// P�edn� st�na</span></p>
<p class="src1">0.0f, 0.0f, -1.0f, -1.0f, +1.0f,</p>
<p class="src1">1.0f, 0.0f, +1.0f, -1.0f, +1.0f,</p>
<p class="src1">1.0f, 1.0f, +1.0f, +1.0f, +1.0f,</p>
<p class="src1">0.0f, 1.0f, -1.0f, +1.0f, +1.0f,</p>
<p class="src1"><span class="kom">// Zadn� st�na</span></p>
<p class="src1">1.0f, 0.0f, -1.0f, -1.0f, -1.0f,</p>
<p class="src1">1.0f, 1.0f, -1.0f, +1.0f, -1.0f,</p>
<p class="src1">0.0f, 1.0f, +1.0f, +1.0f, -1.0f,</p>
<p class="src1">0.0f, 0.0f, +1.0f, -1.0f, -1.0f,</p>
<p class="src1"><span class="kom">// Horn� st�na</span></p>
<p class="src1">0.0f, 1.0f, -1.0f, +1.0f, -1.0f,</p>
<p class="src1">0.0f, 0.0f, -1.0f, +1.0f, +1.0f,</p>
<p class="src1">1.0f, 0.0f, +1.0f, +1.0f, +1.0f,</p>
<p class="src1">1.0f, 1.0f, +1.0f, +1.0f, -1.0f,</p>
<p class="src1"><span class="kom">// Doln� st�na</span></p>
<p class="src1">1.0f, 1.0f, -1.0f, -1.0f, -1.0f,</p>
<p class="src1">0.0f, 1.0f, +1.0f, -1.0f, -1.0f,</p>
<p class="src1">0.0f, 0.0f, +1.0f, -1.0f, +1.0f,</p>
<p class="src1">1.0f, 0.0f, -1.0f, -1.0f, +1.0f,</p>
<p class="src1"><span class="kom">// Prav� st�na</span></p>
<p class="src1">1.0f, 0.0f, +1.0f, -1.0f, -1.0f,</p>
<p class="src1">1.0f, 1.0f, +1.0f, +1.0f, -1.0f,</p>
<p class="src1">0.0f, 1.0f, +1.0f, +1.0f, +1.0f,</p>
<p class="src1">0.0f, 0.0f, +1.0f, -1.0f, +1.0f,</p>
<p class="src1"><span class="kom">// Lev� st�na</span></p>
<p class="src1">0.0f, 0.0f, -1.0f, -1.0f, -1.0f,</p>
<p class="src1">1.0f, 0.0f, -1.0f, -1.0f, +1.0f,</p>
<p class="src1">1.0f, 1.0f, -1.0f, +1.0f, +1.0f,</p>
<p class="src1">0.0f, 1.0f, -1.0f, +1.0f, -1.0f</p>
<p class="src0">};</p>

<p>Dal�� ��st k�du rozhoduje o pou�it� OpenGL extensions za b�hu programu.</p>
<p>P�edpokl�dejme, �e m�me dlouh� �et�zec obsahuj�c� n�zvy v�ech podporovan�ch roz���en� odd�len�ch znakem nov�ho ��dku -'\n'. Pot�ebujeme vyhledat znak nov�ho ��dku a tuto ��st za��t porovn�vat s hledan�m �et�zcem, dokud nenaraz�me na dal�� znak nov�ho ��dku, nebo dokud nalezen� �et�zec neodpov�d� tomu hledan�mu. V prvn�m p��pad� vr�t�me true, v druh�m p��pad� vezmeme dal�� sub-�et�zec dokud nenaraz�me na konec �et�zce. Budeme si muset d�t pozor na to, zda �et�zec neza��n� znakem nov�ho ��dku.</p>
<p>Pozn�mka: Kontrola podpory roz���en� by se m�la V�DY prov�d�t a� za b�hu programu.</p>

<p class="src0">bool isInString(char *string, const char *search)</p>
<p class="src0">{</p>
<p class="src1">int pos = 0;</p>
<p class="src1">int maxpos = strlen(search)-1;</p>
<p class="src1">int len = strlen(string);</p>
<p class="src1">char *other;</p>
<p class="src"></p>
<p class="src1">for (int i=0; i&lt;len; i++)</p>
<p class="src1">{</p>
<p class="src2">if ((i==0) || ((i&gt;1) &amp;&amp; string[i-1]=='\n'))<span class="kom">// Nov� roz���en� za��n� zde</span></p>
<p class="src2">{</p>
<p class="src3">other = &amp;string[i];</p>
<p class="src3">pos=0;<span class="kom">// Za��t nov� hled�n�</span></p>
<p class="src"></p>
<p class="src3">while (string[i]!='\n')<span class="kom">// Hled�n� cel�ho �et�zce jm�na roz���en�</span></p>
<p class="src3">{</p>
<p class="src4">if (string[i]==search[pos])</p>
<p class="src5">pos++;<span class="kom">// Dal�� znak</span></p>
<p class="src"></p>
<p class="src4">if ((pos&gt;maxpos) &amp;&amp; string[i+1]=='\n')</p>
<p class="src5">return true; <span class="kom">// A m�me to!</span></p>
<p class="src"></p>
<p class="src4">i++;</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return false;<span class="kom">// Sm�la, nic jsme nena�li!</span></p>
<p class="src0">}</p>

<p>Te� mus�me z�skat �et�zec obsahuj�c� n�zvy extensions a p�ev�st ho tak, aby jednotliv� n�zvy byly odd�leny znakem nov�ho ��dku. Pokud najdeme sub-�et�zec &quot;GL_ARB_multitexture&quot;, tak je tato funkce podporovan�. Ale my j� pou�ijeme, jen kdy� je __ARB_ENABLE nastaveno na true. Je�t� pot�ebujeme zjistit podporu GL_EXT_texture_env_combine. Toto roz���en� zav�d� nov� zp�sob interakce s texturovac�mi jednotkami. My to pot�ebujeme, proto�e GL_ARB_multitexture pouze p�en�� v�stup z jedn� texturovac� jednotky do dal�� s vy���m ��slem. Ne� abychom pou��vali dal�� komplexn� rovnice pro v�po�et blendingu (kter� by ale mohly m�t odli�n� efekt), rad�ji zajist�me podporu tohoto roz���en�. Pokud jsou v�echna roz���en� podporov�na, zjist�me kolik texturovac�ch jednotek m�me k dispozici a hodnotu ulo��me do maxTexelUnits. Pak mus�me spojit funkce s na�imi jm�ny. To provedeme pomoc� funkce wglGetProcAdress() s parametrem obsahuj�c�m n�zev funkce.</p>

<p class="src0">bool initMultitexture(void)</p>
<p class="src0">{</p>
<p class="src1">char *extensions;</p>
<p class="src"></p>
<p class="src1">extensions = strdup((char *) glGetString(GL_EXTENSIONS));<span class="kom">// Z�sk�n� �et�zce s roz���en�mi</span></p>
<p class="src1">int len = strlen(extensions);<span class="kom">// D�lka �et�zce</span></p>
<p class="src"></p>
<p class="src1">for (int i = 0; i&lt;len; i++)<span class="kom">// Rozd�lit znakem nov�ho ��dku m�sto mezery</span></p>
<p class="src2">if (extensions[i] == ' ')</p>
<p class="src3">extensions[i] = '\n';</p>
<p class="src"></p>
<p class="src0">#ifdef EXT_INFO</p>
<p class="src1">MessageBox(hWnd,extensions,&quot;supported GL extensions&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src0">#endif</p>
<p class="src"></p>
<p class="src1">if (isInString(extensions,&quot;GL_ARB_multitexture&quot;)<span class="kom">// Je multitexturing podporov�n?
</span></p>
<p class="src1">&amp;&amp; __ARB_ENABLE<span class="kom">// P��znak pro povolen� multitexturingu</span></p>
<p class="src1">&amp;&amp; isInString(extensions,&quot;GL_EXT_texture_env_combine&quot;))<span class="kom">// Je podporov�no texture-environment-combining?</span></p>
<p class="src1">{</p>
<p class="src2">glGetIntegerv(GL_MAX_TEXTURE_UNITS_ARB, &amp;maxTexelUnits);</p>
<p class="src"></p>
<p class="src2">glMultiTexCoord1fARB = (PFNGLMULTITEXCOORD1FARBPROC)wglGetProcAddress(&quot;glMultiTexCoord1fARB&quot;);</p>
<p class="src2">glMultiTexCoord2fARB = (PFNGLMULTITEXCOORD2FARBPROC)wglGetProcAddress(&quot;glMultiTexCoord2fARB&quot;);</p>
<p class="src2">glMultiTexCoord3fARB = (PFNGLMULTITEXCOORD3FARBPROC)wglGetProcAddress(&quot;glMultiTexCoord3fARB&quot;);</p>
<p class="src2">glMultiTexCoord4fARB = (PFNGLMULTITEXCOORD4FARBPROC)wglGetProcAddress(&quot;glMultiTexCoord4fARB&quot;);</p>
<p class="src"></p>
<p class="src2">glActiveTextureARB = (PFNGLACTIVETEXTUREARBPROC)wglGetProcAddress(&quot;glActiveTextureARB&quot;);</p>
<p class="src"></p>
<p class="src2">glClientActiveTextureARB = (PFNGLCLIENTACTIVETEXTUREARBPROC)wglGetProcAddress(&quot;glClientActiveTextureARB&quot;);</p>
<p class="src"></p>
<p class="src0">#ifdef EXT_INFO</p>
<p class="src2">MessageBox(hWnd,&quot;The GL_ARB_multitexture extension will be used.&quot;,&quot;feature supported!&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src0">#endif</p>
<p class="src"></p>
<p class="src2">return true;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">useMultitexture = false;<span class="kom">// Nem��eme to pou��vat, pokud to nen� podporov�no!</span></p>
<p class="src1">return false;</p>
<p class="src0">}</p>

<p>InitLights() pouze inicializuje osv�tlen�. Je vol�na funkc� initGL().</p>

<p class="src0">void initLights(void)</p>
<p class="src0">{</p>
<p class="src1">glLightfv(GL_LIGHT1, GL_AMBIENT, LightAmbient);<span class="kom">// Na�ten� informace o sv�tlech do GL_LIGHT1</span></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_DIFFUSE, LightDiffuse);</p>
<p class="src1">glLightfv(GL_LIGHT1, GL_POSITION, LightPosition);</p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHT1);</p>
<p class="src0">}</p>

<p>V t�to lekci vytvo��me hodn� textur. Nyn� k na�� na��tac� funkci. Nejd��ve loadujeme z�kladn� bitmapu a p�iprav�me z n� t�i filtrovan� textury (GL_NEAREST, GL_LINEAR, GL_LINEAR_MIPMAP_NEAREST). Pou�ijeme pouze jednu datovou strukturu na ulo�en� bitmap. Nav�c zavedeme novou strukturu nazvanou alpha, kter� bude obsahovat informace o alpha kan�lu (pr�hlednosti) textury. Proto ulo��me RGBA obr�zky jako dv� bitmapy: jednu 24 bitovou RGB a jednu osmi bitovou ve stupn�ch �edi pro alpha kan�l. Aby fungovalo na��t�n� spr�vn�, mus�me po ka�d�m na�ten� smazat Image, jinak nebudeme upozorn�ni na p��padn� chyby p�i nahr�v�n� textur.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_22_base.gif" width="128" height="128" alt="Textura Base" /></div>

<p>Tak� je u specifikace typu textury vhodn� uv�st m�sto ��sla 3 prom�nnou GL_RGB8, a to kv�li lep�� kompatibilit� s dal��mi verzemi OpenGL. Tato zm�na je ozna�ena v k�du <span class="warning">takto</span>.</p>

<p class="src0">int LoadGLTextures()<span class="kom">// Loading bitmapy a konverze na texturu</span></p>
<p class="src0">{</p>
<p class="src1">bool status=true;<span class="kom">// Indikuje chyby</span></p>
<p class="src1">AUX_RGBImageRec *Image=NULL;<span class="kom">// Ukl�d� bitmapu</span></p>
<p class="src1">char *alpha=NULL;</p>
<p class="src"></p>
<p class="src1">if (Image = auxDIBImageLoad(&quot;Data/Base.bmp&quot;))<span class="kom">// Nahraje bitmapu</span></p>
<p class="src1">{</p>
<p class="src2">glGenTextures(3, texture);<span class="kom">// Generuje t�i textury</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�en� neline�rn� filtrovan� textury</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[0]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_NEAREST);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_NEAREST);</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, <span class="warning">GL_RGB8</span>, Image-&gt;sizeX, Image-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, Image-&gt;data);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�en� line�rn� filtrovan� textury</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[1]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, <span class="warning">GL_RGB8</span>, Image-&gt;sizeX, Image-&gt;sizeY, 0, GL_RGB, GL_UNSIGNED_BYTE, Image-&gt;data);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�en� mipmapovan� textury</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, texture[2]);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR_MIPMAP_NEAREST);</p>
<p class="src2">gluBuild2DMipmaps(GL_TEXTURE_2D, <span class="warning">GL_RGB8</span>, Image-&gt;sizeX, Image-&gt;sizeY, GL_RGB, GL_UNSIGNED_BYTE, Image-&gt;data);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">else</p>
<p class="src2">status = false;</p>
<p class="src"></p>
<p class="src1">if (Image)<span class="kom">// Pokud obr�zek existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (Image-&gt;data)<span class="kom">// Pokud existuj� data obr�zku</span></p>
<p class="src3">delete Image-&gt;data;<span class="kom">// Uvoln� data obr�zku</span></p>
<p class="src"></p>
<p class="src2">delete Image;<span class="kom">// Uvoln� strukturu obr�zku</span></p>
<p class="src"></p>
<p class="src2">Image = NULL;<span class="kom">// Nastav� ukazatel na NULL</span></p>
<p class="src1">}</p>

<p>Na�teme bumpmapu. Z d�vod� uveden�ch n��e mus� m�t pouze 50% intenzitu, tak�e ji mus�me n�jak�m zp�sobem ztmavit. J� jsem se rozhodl pou��t funkci glPixelTransferf(), kter� ud�v� jak�m zp�sobem budou bitmapy p�evedeny na textury. My tuto funkci pou�ijeme na ztmaven� jednotliv�ch RGB kan�l� bitmapy na 50% p�vodn� intenzity. Pokud dosud nepou��v�te rodinu funkc� glPixelTransfer(), m�li byste se na n� pod�vat - jsou celkem u�ite�n�.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_22_bump.gif" width="128" height="128" alt="Textura Bumpmapy" /></div>

<p class="src1"><span class="kom">// Loading bumpmap</span></p>
<p class="src1">if (Image = auxDIBImageLoad(&quot;Data/Bump.bmp&quot;))</p>
<p class="src1">{</p>
<p class="src2">glPixelTransferf(GL_RED_SCALE,0.5f);<span class="kom">// Sn��en� intenzity RGB na 50% - polovi�n� intenzita</span></p>
<p class="src2">glPixelTransferf(GL_GREEN_SCALE,0.5f);</p>
<p class="src2">glPixelTransferf(GL_BLUE_SCALE,0.5f);</p>

<p>Dal�� probl�m je, �e nechceme, aby se bitmapa v textu�e po��d opakovala, chceme ji namapovat pouze jednou na texturovac� sou�adnice od (0.0f,0.0f) do (1.0f,1.0f). V�e kolem nich by m�lo b�t namapov�no �ernou barvou. Toho dos�hneme zavol�n�m dvou funkc� glTexParameteri(), kter� nen� t�eba popisovat.</p>

<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_WRAP_S,GL_CLAMP);<span class="kom">// Bez wrappingu (zalamov�n�)</span></p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_WRAP_T,GL_CLAMP);</p>
<p class="src"></p>
<p class="src2">glTexParameterfv(GL_TEXTURE_2D,GL_TEXTURE_BORDER_COLOR,Gray);<span class="kom">// Barva okraje textury</span></p>
<p class="src"></p>
<p class="src2">glGenTextures(3, bump);<span class="kom">// Vytvo�� t�i textury</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�en� neline�rn� filtrovan� textury</span></p>
<p class="src2">&gt;-&lt;</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�en� line�rn� filtrovan� textury</span></p>
<p class="src2">&gt;-&lt;</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�en� mipmapovan� textury</span></p>
<p class="src2">&gt;-&lt;</p>

<p>Nyn� mus�me vytvo�it je�t� invertovanou bumpmapu, o kter� jsme ji� psali a jej�� v�znam bude vysv�tlen d�le. Ode�ten�m barvy ka�d�ho bodu bumpmapy od b�l� barvy {255, 255, 255} z�sk�me obr�zek s invertovan�mi barvami. P�edt�m nesm�me nastavit intenzitu zp�t na 100% (ne� jsem na to p�i�el str�vil jsem nad t�m asi 3 hodiny), invertovan� bitmapa mus� b�t tedy tak� ztmaven� na 50%.</p>

<p class="src2">for (int i = 0; i &lt; 3 * Image-&gt;sizeX * Image-&gt;sizeY; i++)<span class="kom">// Invertov�n� bumpmapy</span></p>
<p class="src3">Image-&gt;data[i] = 255 - Image-&gt;data[i];</p>
<p class="src"></p>
<p class="src2">glGenTextures(3, invbump);<span class="kom">// Vytvo�� t�i textury</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�en� neline�rn� filtrovan� textury</span></p>
<p class="src2">&gt;-&lt;</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�en� line�rn� filtrovan� textury</span></p>
<p class="src2">&gt;-&lt;</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�en� mipmapovan� textury</span></p>
<p class="src2">&gt;-&lt;</p>
<p class="src"></p>
<p class="src2">glPixelTransferf(GL_RED_SCALE,1.0f);<span class="kom">// Vr�cen� intenzity RGB zp�t na 100%</span></p>
<p class="src2">glPixelTransferf(GL_GREEN_SCALE,1.0f);</p>
<p class="src2">glPixelTransferf(GL_BLUE_SCALE,1.0f);</p>
<p class="src"></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src2">status = false;</p>
<p class="src"></p>
<p class="src1">if (Image)<span class="kom">// Pokud obr�zek existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (Image-&gt;data)<span class="kom">// Pokud existuj� data obr�zku</span></p>
<p class="src3">delete Image-&gt;data;<span class="kom">// Uvoln� data obr�zku</span></p>
<p class="src"></p>
<p class="src2">delete Image;<span class="kom">// Uvoln� strukturu obr�zku</span></p>
<p class="src"></p>
<p class="src2">Image = NULL;<span class="kom">// Nastav� ukazatel na NULL</span></p>
<p class="src1">}</p>

<p>Na��t�n� bitmap log je velmi jednoduch� a� na zkombinov�n� RGB-A kan�l�, nicm�n� k�d by m�l b�t dostate�n� jasn�. V�imn�te si, �e tato textura je vytvo�ena z dat alpha, nikoliv z dat Image. Bude zde pou�it pouze jeden filtr.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_22_opengl_alpha.gif" width="128" height="64" alt="Textura OpenGL_ALPHA" /></div>

<p class="src1"><span class="kom">// Na�te bitmapy log</span></p>
<p class="src1">if (Image = auxDIBImageLoad(&quot;Data/OpenGL_ALPHA.bmp&quot;))</p>
<p class="src1">{</p>
<p class="src2">alpha = new char[4*Image-&gt;sizeX*Image-&gt;sizeY];<span class="kom">// Alokuje pam� pro RGBA8-Texturu</span></p>
<p class="src"></p>
<p class="src2">for (int a=0; a &lt; Image-&gt;sizeX * Image-&gt;sizeY; a++)</p>
<p class="src3">alpha[4*a+3] = Image-&gt;data[a*3];<span class="kom">// Vezme pouze �ervenou barvu jako alpha kan�l</span></p>

<div class="okolo_img"><img src="images/nehe_tut/tut_22_opengl.gif" width="128" height="64" alt="Textura OpenGL" /></div>

<p class="src"></p>
<p class="src2">if (!(Image = auxDIBImageLoad(&quot;Data/OpenGL.bmp&quot;)))</p>
<p class="src3">status = false;</p>
<p class="src"></p>
<p class="src2">for (a = 0; a &lt; Image-&gt;sizeX * Image-&gt;sizeY; a++)</p>
<p class="src2">{</p>
<p class="src3">alpha[4*a]=Image-&gt;data[a*3];<span class="kom">// R</span></p>
<p class="src3">alpha[4*a+1]=Image-&gt;data[a*3+1];<span class="kom">// G</span></p>
<p class="src3">alpha[4*a+2]=Image-&gt;data[a*3+2];<span class="kom">// B</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">glGenTextures(1, &amp;glLogo);<span class="kom">// Vytvo�� jednu texturu</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�� line�rn� filtrovanou RGBA8-Texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, glLogo);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, <span class="warning">GL_RGBA8</span>, Image-&gt;sizeX, Image-&gt;sizeY, 0, GL_RGBA, GL_UNSIGNED_BYTE, alpha);</p>
<p class="src"></p>
<p class="src2">delete alpha;<span class="kom">// Uvoln� alokovanou pam�</span></p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src2">status = false;</p>
<p class="src"></p>
<p class="src1">if (Image)<span class="kom">// Pokud obr�zek existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (Image-&gt;data)<span class="kom">// Pokud existuj� data obr�zku</span></p>
<p class="src3">delete Image-&gt;data;<span class="kom">// Uvoln� data obr�zku</span></p>
<p class="src"></p>
<p class="src2">delete Image;<span class="kom">// Uvoln� strukturu obr�zku</span></p>
<p class="src"></p>
<p class="src2">Image = NULL;<span class="kom">// Nastav� ukazatel na NULL</span></p>
<p class="src1">}</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_22_multi_on_alpha.gif" width="256" height="64" alt="Textura Extension Enabled ALFA" /></div>

<p class="src1">if (Image = auxDIBImageLoad(&quot;Data/multi_on_alpha.bmp&quot;))</p>
<p class="src1">{</p>
<p class="src2">alpha = new char[4*Image-&gt;sizeX*Image-&gt;sizeY];<span class="kom">// Alokuje pam� pro RGBA8-Texturu</span></p>
<p class="src"></p>
<p class="src2">for (int a = 0; a &lt; Image-&gt;sizeX * Image-&gt;sizeY; a++)</p>
<p class="src3">alpha[4*a+3]=Image-&gt;data[a*3];<span class="kom">// Vezme pouze �ervenou barvu jako alpha kan�l</span></p>

<div class="okolo_img"><img src="images/nehe_tut/tut_22_multi_on.gif" width="256" height="64" alt="Textura Extension Enabled" /></div>

<p class="src2">if (!(Image=auxDIBImageLoad(&quot;Data/multi_on.bmp&quot;)))</p>
<p class="src3">status = false;</p>
<p class="src"></p>
<p class="src2">for (a=0; a &lt; Image-&gt;sizeX * Image-&gt;sizeY; a++)</p>
<p class="src3">{</p>
<p class="src3">alpha[4*a] = Image-&gt;data[a*3];<span class="kom">// R</span></p>
<p class="src3">alpha[4*a+1] = Image-&gt;data[a*3+1];<span class="kom">// G</span></p>
<p class="src3">alpha[4*a+2] = Image-&gt;data[a*3+2];<span class="kom">// B</span></p>
<p class="src2">}</p>
<p class="src0"></p>
<p class="src"></p>
<p class="src2">glGenTextures(1, &amp;multiLogo);<span class="kom">// Vytvo�� jednu texturu</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Vytvo�� line�rn� filtrovanou RGBA8-Texturu</span></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, multiLogo);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MAG_FILTER,GL_LINEAR);</p>
<p class="src2">glTexParameteri(GL_TEXTURE_2D,GL_TEXTURE_MIN_FILTER,GL_LINEAR);</p>
<p class="src2">glTexImage2D(GL_TEXTURE_2D, 0, <span class="warning">GL_RGBA8</span>, Image-&gt;sizeX, Image-&gt;sizeY, 0, GL_RGBA, GL_UNSIGNED_BYTE, alpha);</p>
<p class="src"></p>
<p class="src2">delete alpha;</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src2">status = false;</p>
<p class="src"></p>
<p class="src1">if (Image)<span class="kom">// Pokud obr�zek existuje</span></p>
<p class="src1">{</p>
<p class="src2">if (Image-&gt;data)<span class="kom">// Pokud existuj� data obr�zku</span></p>
<p class="src3">delete Image-&gt;data;<span class="kom">// Uvoln� data obr�zku</span></p>
<p class="src"></p>
<p class="src2">delete Image;<span class="kom">// Uvoln� strukturu obr�zku</span></p>
<p class="src"></p>
<p class="src2">Image = NULL;<span class="kom">// Nastav� ukazatel na NULL</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return status;<span class="kom">// Vr�t� status</span></p>
<p class="src0">}</p>

<p>N�sleduje funkce doCube(), kter� kresl� krychli spolu s norm�lami. V�imn�te si, �e tato verze zat�uje pouze texturovac� jednotku #0, glTexCoord(s, t) pracuje stejn� jako glMultiTexCoord(GL_TEXTURE0_ARB, s, t). Krychle m��e b�t taky vykreslena pomoc� prokl�dan�ch pol�, to ale te� nebudeme �e�it. Nem��e v�ak b�t ulo�ena na display listu, ty pou��vaj� pravd�podobn� p�esnost r�znou od GLfloat, co� vede k nep�kn�m vedlej��m efekt�m.</p>

<p class="src0">void doCube(void)</p>
<p class="src0">{</p>
<p class="src1">int i;</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src"></p>
<p class="src2"><span class="kom">// P�edn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f, +1.0f);</p>
<p class="src2">for (i=0; i&lt;4; i++)</p>
<p class="src2">{</p>
<p class="src3">glTexCoord2f(data[5*i],data[5*i+1]);</p>
<p class="src3">glVertex3f(data[5*i+2],data[5*i+3],data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Zadn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 0.0f,-1.0f);</p>
<p class="src2">for (i=4; i&lt;8; i++)</p>
<p class="src2">{</p>
<p class="src3">glTexCoord2f(data[5*i],data[5*i+1]);</p>
<p class="src3">glVertex3f(data[5*i+2],data[5*i+3],data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Horn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f, 1.0f, 0.0f);</p>
<p class="src2">for (i=8; i&lt;12; i++)</p>
<p class="src2">{</p>
<p class="src3">glTexCoord2f(data[5*i],data[5*i+1]);</p>
<p class="src3">glVertex3f(data[5*i+2],data[5*i+3],data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Spodn� st�na</span></p>
<p class="src2">glNormal3f( 0.0f,-1.0f, 0.0f);</p>
<p class="src2">for (i=12; i&lt;16; i++)</p>
<p class="src2">{</p>
<p class="src3">glTexCoord2f(data[5*i],data[5*i+1]);</p>
<p class="src3">glVertex3f(data[5*i+2],data[5*i+3],data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Prav� st�na</span></p>
<p class="src2">glNormal3f( 1.0f, 0.0f, 0.0f);</p>
<p class="src2">for (i=16; i&lt;20; i++)</p>
<p class="src2">{</p>
<p class="src3">glTexCoord2f(data[5*i],data[5*i+1]);</p>
<p class="src3">glVertex3f(data[5*i+2],data[5*i+3],data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Lev� st�na</span></p>
<p class="src2">glNormal3f(-1.0f, 0.0f, 0.0f);</p>
<p class="src2">for (i=20; i&lt;24; i++)</p>
<p class="src2">{</p>
<p class="src3">glTexCoord2f(data[5*i],data[5*i+1]);</p>
<p class="src3">glVertex3f(data[5*i+2],data[5*i+3],data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>P�ich�z� �as na inicializaci OpenGL. V�e je jako v lekci 06, krom� toho, �e zavol�me funkci initLights(), m�sto toho, abychom sv�tla nastavovali zde. A je�t� samoz�ejm� vol�me nastaven� p��padn�ho multitexturingu.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echno nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">multitextureSupported = initMultitexture();</p>
<p class="src"></p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Vytvo�en� textur</span></p>
<p class="src2">return false;</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturov� mapov�n�</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Zapne smooth shading</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolen� testov�n� hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testov�n� hloubky</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Kvalitn� v�po�ty perspektivy</span></p>
<p class="src"></p>
<p class="src1">initLights();<span class="kom">// Inicializace sv�tel</span></p>
<p class="src"></p>
<p class="src1">return true;<span class="kom">// V�e v po��dku</span></p>
<p class="src0">}</p>

<hr />

<p><b>Za��tek teorie (Emboss Bump Mapping)</b></p>

<p>Zde je asi 95% pr�ce. V�e u �eho bylo naps�no, �e bude vysv�tleno pozd�ji, je v n�sleduj�c� teoretick� sekci. Jedn� se o p�eps�n� prezentace v PowerPointu do HTML.</p>

<p><b>Emboss Bump Mapping</b></p>

<p>Michael I. Gold - NVidia Corporation</p>

<p><b>Bump Mapping</b></p>

<p>Skute�n� bump mapping pou��v� per-pixel osv�tlen�.</p>

<ul>
<li>V�po�et osv�tlen� na ka�d�m pixelu zalo�en� na r�zn�ch norm�lov�ch vektorech.</li>
<li>V�po�etn� velmi n�ro�n�.</li>
<li>Pro v�ce informac� se pod�vejte na: Blinn, J. : Simulation of Wrinkled Surfaces, Computer Graphics. 12,3 (August 1978) 286-292.</li>
<li>Pro informace na webu zajd�te na: <?OdkazBlank('http://www.r3.nu/');?> a pod�vejte se na Cass Everitt's Orthogonal Illumination Thesis. (pozn.: Jens)</li>
</ul>

<p><b>Emboss Bump Mapping</b></p>

<p>Emboss Bump Mapping je pouze n�hra�ka.</p>

<ul>
<li>Pouze difuzn� osv�tlen�, ��dn� odra�en�.</li>
<li>V�skyt artefakt� (m��e v�st k rozmazan�mu pohybu pozn.: Jens)</li>
<li>Dostupn� na dne�n�m hardwaru</li>
<li>Vypad� celkem slu�n�</li>
</ul>

<p><b>V�po�et dif�zn�ho osv�tlen�</b></p>

<p>C = (L * N) x Dl x Dm</p>

<ul>
<li>L je vektor sv�tla</li>
<li>N je norm�lov� vektor</li>
<li>Dl je barva difusn�ho sv�tla</li>
<li>Dm je difusn� barva materi�lu</li>
<li>Bump Mapping m�n� pro ka�d� pixel N</li>
<li>Emboss Bump Mapping se bl��� L * N</li>
</ul>

<p><b>P�ibli�n� stupe� rozptylu L * N</b></p>

<p>Textura reprezentuje v��kovou mapu</p>

<ul>
<li>[0,1] ur�uje interval prohybu (v��kov�ho rozd�lu)</li>
<li>Prvn� odvozen� reprezentuje sklon (�hel) m - m je pouze jednorozm�rn� - reprezentuje sklon na sou�adnic�ch (s,t) dan� textury (pozn.: Jens)</li>
<li>m zvy�uje nebo sni�uje z�kladn� stupe� rozptylu Fd</li>
<li>(Fd + m) se bl��� (L * N) na pixel</li>
</ul>

<p><b>P�ibli�n� odvozen�</b></p>

<p>Zohledn�n� p�ibli�n�ch �daj�</p>

<ul>
<li>Vyv��en� H0 v bod� o sou�adnic�ch (s,t)</li>
<li>Vyv��en� H1 v bod� m�rn� posunut�m ke zdroji sv�tla (s + ds, t + dt)</li>
<li>Ode�ten� p�vodn� v��ky H0 od posunut� H1</li>
<li>Rozd�l je okam�it�m sklonem m = H1 - H0</li>
</ul>


<p><b>Spo��t�n� reli�fu</b></p>

<p>1) P�vodn� reli�f (H0).</p>
<div class="okolo_img"><img src="images/nehe_tut/tut_22_image002.jpg" width="140" height="48" alt="P�vodn� reli�f (H0)" /></div>

<p>2) P�vodn� reli�f (H0) prolo�en� druh�m (H1), kter� je m�rn� posunut� sm�rem ke sv�tlu.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_22_image004.jpg" width="216" height="91" alt="P�vodn� reli�f (H0) prolo�en� druh�m (H1), kter� je m�rn� posunut� sm�rem ke sv�tlu" /></div>

<p>3) Ode�ten� p�vodn�ho od posunut�ho reli�fu (H0-H1) - vede ke vzniku sv�tl�ch (B) a tmav�ch (D) ploch.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_22_image006.jpg" width="173" height="48" alt="Ode�ten� p�vodn�ho od posunut�ho reli�fu (H0-H1) - vede ke vzniku sv�tl�ch (B) a tmav�ch (D) ploch" /></div>

<p><b>V�po�et osv�tlen�</b></p>

<p>Ur��me hodnotu barvy (Cf) dan� plochy</p>

<ul>
<li>Cf = (L * N) x Dl x Dm</li>
<li>(L * N) ~ (Fd + (H0 - H1))</li>
<li>Dm x Dl je vlastn� ji� ur�en texturou Ct. Jinak m��ete pracovat s Dl a Dm samostatn�, pokud jste dostate�n� zdatn�. (To se prov�d� pomoc� OpenGL-Lighting! pozn.: Jens)</li>
<li>Cf = (Fd + (H0 - H1)) x Ct</li>
</ul>

<p><b>Je to v�e? Takhle jednoduch�?!</b></p>

<p>Je�t� nejsme �pln� hotov�. St�le mus�me:</p>

<ul>
<li>Vytvo�it texturu (pomoc� grafick�ho editoru pozn.: Jens)</li>
<li>Spo��tat posunut� textury (ds,dt)</li>
<li>Spo��tat stupe� rozptylu Fd (pomoc� OpenGL-Lighting! pozn.: Jens)</li>
<li>Oboj� je odvozeno z norm�ly N a vektoru sv�tla L (v na�em p��pad� se spo��t� p�esn� pouze (ds,dt)! pozn.: Jens)</li>
<li>Te� si d�me tro�ku matematiky</li>
</ul>

<p><b>Tvorba textury</b></p>

<p>Uchov�vejte textury!</p>

<ul>
<li>Sou�asn� multitexturovac� hardware podporuje pouze dv� textury!</li>
<li>Bumpmapa v alpha kan�lu (my to t�mto zp�sobem ned�l�me, ale m��ete si to zkusit jako takov� cvi�en�, pokud m�te TNT chipset pozn.:Jens)</li>
<li>Maxim�ln� prohyb = 1.0</li>
<li>Z�kladn� v��ka = 0.5</li>
<li>Maxim�ln� pokles = 0.0</li>
<li>Barva povrchu v RGB kan�lech</li>
<li>Nastavit intern� form�t na GL_RGBA8 !!</li>
</ul>

<p><b>V�po�et offsetu textury</b></p>

<p>Pooto�en� vektoru sv�tla</p>

<ul>
<li>Pot�eba je norm�ln� sou�adnicov� syst�m</li>
<li>Odvozen� sou�adnicov�ho syst�mu z norm�lov�ho a "horn�ho" vektoru (my p�ed�me sm�r texturovac�ch sou�adnic do na�eho gener�toru posunut� explicitn� pozn.: Jens)</li>
<li>Norm�la je osa z</li>
<li>Meziprodukt je osa x</li>
<li>Zahozen� &quot;horn�ho&quot; vektoru, odvozen� osy y z os x a z</li>
<li>Vytvo�en� matice Mn 3x3 ze spo��tan�ch os</li>
<li>Transformace vektoru sv�tla do norm�ln�ho prostoru (Mn se taky naz�v� ortonorm�ln� z�klad pozn.: Jens)</li>
</ul>

<p><b>V�po�et offsetu textury (pokra�ov�n�)</b></p>

<p>Pou�ijte pro posunut� vektor sv�tla norm�ln�ho prostoru</p>

<ul>
<li>L' = Mn x L</li>
<li>Pou��t L'x, L'y pro (ds, dt)</li>
<li>Pou��t L'z pro stupe� rozptylu! (Rad�ji ne! Pokud nevlastn�te TNT, pou�ijte m�sto toho OpenGL-Lighting, jinak byste museli renderovat jeden cyklus nav�c! pozn.: Jens)</li>
<li>Pokud je vektor sv�tla bl�zk� norm�le, L'x, L'y jsou n�zk�</li>
<li>Pokud se vektor sv�tla bl��� tangentov� rovin�, L'x, L'y jsou vysok�</li>
<li>Co kdy� je L'z men�� ne� nula?</li>
<li>Sv�tlo je na opa�n� stran� ne� norm�la</li>
<li>Pak se bude rovnat nule.</li>
</ul>

<p><b>Implementace na TNT</b></p>

<p>Spo��tejte vektory, texturovac� sou�adnice na hostiteli</p>

<ul>
<li>P�edejte stupe� rozptylu v alpha kan�lu</li>
<li>Mohli byste vyu��t barvu vertexu pro barvu rozpt�len�ho sv�tla</li>
<li>H0 a barvu z texturovac� jednotky 0</li>
<li>H1 z texturovac� jednotky 1 (stejn� textura jin� sou�adnice)</li>
<li>ARB_multitexture extension</li>
<li>Zkombinuje extension (precizn�ji: NVIDIA_multitexture_combiners extension, podporovan� v�emi akceler�tory rodiny TNT pozn.: Jens)</li>
</ul>

<p><b>Implementace na TNT (pokra�ov�n�)</b></p>

<p>Nastaven� alpha kan�lu na combineru</p>

<ul>
<li>(1-T0a) + T1a - 0.5 (T0a zastupuje &quot;texturovac� jednotku 0, alpha kan�l&quot; pozn.: Jens)</li>
<li>(T1a-T0a) se namapuje na (-1,1), ale hardware ji p�ipevn� na (0,1)</li>
<li>P�ednastaven� 0.5 vyva�uje ztr�tu oproti uchycen� (zva�te u�it� 0.5, mohli byste dos�hnout v�t�� rozmanitosti bumpmap, pozn.: Jens)</li>
<li>M��ete p�izp�sobit barvu rozpt�len�ho sv�tla T0c</li>
<li>RGB nastaven� combineru 0:</li>
<li>(T0c * C0a + T0c * Fda - 0.5)*2</li>
<li>0.5 vyva�uje ztr�tu oproti uchycen�</li>
<li>N�soben� dv�ma prosv�tl� obraz</li>
</ul>

<p><b>Konec teorie (Emboss Bump Mapping)</b></p>

<hr />

<p>My to ale ud�l�me trochu jinak ne� podle TNT implementace, abychom umo�nili na�emu programu b�et na V�ECH akceler�torech. Zde se m��eme p�iu�it dv� nebo t�i v�ci. Jedna z nich je, �e bumpmapping je v�ce f�zov� algoritmus na v�t�in� karet (ne na TNT, kde se to d� nahradit jednou dvou-texturovac� f�z�). U� byste si m�li b�t schopni p�edstavit, jak hezk� multitexturing ve skute�nosti je. Nyn� implementujeme 3-f�zov� netexturovac� algoritmus, kter� pak m��e b�t (a bude) vylep�en na 2 f�zov� texturovac� algoritmus.</p>

<p>Te� byste si m�li uv�domit, �e mus�me ud�lat n�jak� n�soben� matice matic� (a n�soben� vektoru matic�). Ale to nen� nic �eho bychom se m�li ob�vat: OpenGL zvl�dne n�soben� matice matic� za n�s a n�soben� vektoru matic� je celkem jednoduch�: funkce VMatMult(M,v) vyn�sob� matici M s vektorem v a v�sledek ulo�� zp�t ve v: v = M * v. V�echny matice a vektory p�edan� funkci musej� m�t stejn� tvar: matice 4x4 a 4-rozm�rn� vektory. To je pro zaji�t�n� kompatibility s OpenGL.</p>

<p class="src0">void VMatMult(GLfloat *M, GLfloat *v)</p>
<p class="src0">{</p>
<p class="src1">GLfloat res[3];</p>
<p class="src"></p>
<p class="src1">res[0] = M[0]*v[0]+M[1]*v[1]+M[ 2]*v[2]+M[ 3]*v[3];</p>
<p class="src1">res[1] = M[4]*v[0]+M[5]*v[1]+M[ 6]*v[2]+M[ 7]*v[3];</p>
<p class="src1">res[2] = M[8]*v[0]+M[9]*v[1]+M[10]*v[2]+M[11]*v[3];</p>
<p class="src"></p>
<p class="src1">v[0]=res[0];</p>
<p class="src1">v[1]=res[1];</p>
<p class="src1">v[2]=res[2];</p>
<p class="src"></p>
<p class="src1">v[3]=M[15];<span class="kom">// Homogenn� sou�adnice</span></p>
<p class="src0">}</p>

<hr />

<p><b>Za��tek teorie (algoritmy pro Emboss Bump Mapping)</b></p>

<p>Zde se zm�n�me o dvou odli�n�ch algoritmech. Prvn� popisuje program, kter� se jmenuje GL_BUMP a napsal ho Diego T�rtara v roce 1999. I p�es p�r nev�hod velmi p�kn� implementuje bumpmapping. Te� se na tento algoritmus pod�v�me:</p>

<ol>
<li>V�echny vektory mus� b�t BU� v prostoru objektu NEBO v prostoru sc�ny</li>
<li>Spo��t�n� vektoru v z aktu�ln� pozice vertexu vzhledem ke sv�tlu</li>
<li>Normalizace v</li>
<li>Prom�tnut� v do tangenoidn�ho prostoru. (To je plocha, kter� se dot�k� dan�ho vertexu. Pokud pracujete s rovn�mi plochami, tak je to zpravidla plocha samotn�.)</li>
<li>Posuneme sou�adnice (s,t) o slo�ky x,y vektoru v</li>
</ol>

<p>To nevypad� �patn�! V podstat� je to algoritmus popsan� Michaelem I. Goldem v��e. M� v�ak z�sadn� nev�hodu: T�mara pou��v� projekci pouze pro rovinu xy. To pro na�e pot�eby nesta��, proto�e zjednodu�uje prom�tac� krok pouze na slo�ky x a y a se slo�kou z vektoru v v�bec nepo��t�.</p>

<p>Ale tato implementace vytvo�� rozpt�len� sv�tlo stejn�m zp�sobem, jako ho budeme d�lat my: s pou�it�m v OpenGL zabudovan� podpory osv�tlen�. Tak�e nem��eme pou��t metodu kombiner�, jakou navrhuje Gold (Chceme, aby na�e programy b�ely i na jin�ch ne� TNT kart�ch!), nem��eme ulo�it stupe� rozptylu do alpha kan�lu. Tak ji� m�me probl�m s 3 f�zov�m netexturovan�m a 2 f�zov�m texturov�n�m, pro� na posledn� pr�chod nepou��t OpenGL-Lighting, aby za n�s dod�lal ambientn� sv�tlo a barvy? Je to mo�n� (a v�sledek vypad� celkem dob�e), ale jen proto, �e nyn� nepou��v�me slo�itou geometrii. Tohle byste si m�li zapamatovat. Pokud budete cht�t renderovat n�kolik tis�c bumpmapovan�ch troj�heln�k�, zkuste objevit n�co jin�ho.</p>

<p>Nav�c, pou��v� multitexturing (jak m��eme vid�t) ne tak jednodu�e jako my s ohledem na tento speci�ln� p��pad.</p>

<p>Ale te� k na�� implementaci. Vypad� podobn� jako algoritmus p�edt�m, krom� projek�n� f�ze, kde pou�ijeme vlastn� postup:</p>

<ul>
<li>Pou�ijeme SOU�ADNICE OBJEKTU, to znamen�, �e nepou�ijeme matici modelu p�i v�po�tech. Tohle m� za p���inu nemil� vedlej�� efekt: kdy� chceme ot��et krychl�, sou�adnice v objektu se nezm�n�, ale sou�adnice vertexu v sou�adnic�ch sc�ny (vzhledem k o��m) se zm�n�. Ale pozice na�eho sv�tla by se nem�la pohybovat s krychl�, m�la by b�t statick�, co� znamen�, �e sou�adnice by se nem�ly m�nit. Abychom to vykompenzovali, pou�ijeme mal� trik, b�n� u��van� s po��ta�ov� grafice: m�sto transformace ka�d�ho vertexu do prostoru sv�ta kv�li bumpmap�m, p�evedeme sou�adnice sv�tla do prostoru objektu s pou�it�m inverzn� matice modelu. Tohle je velmi snadn�, vzhledem k tomu, �e p�esn� v�me, jak jsme vytvo�ili matici modelu, nen� probl�m tento postup obr�tit. K tomu se je�t� dostaneme.</li>
<li>Spo��t�me dan� vertex c na povrchu.</li>
<li>Pak spo��t�me norm�lu n s d�lkou 1 (v�t�inou zn�me n pro ka�dou st�nu krychle). To je d�le�it�, m��eme tak u�et�it �as p�i zji��ov�n� normalizovan�ch vektor�. Spo��t�me vektor sv�tla v z vektoru c sm��uj�c�mu k pozici sv�tla l.</li>
<li>Pokud je t�eba je�t� n�co ud�lat, sestav�me matici Mn reprezentuj�c� ortonorm�ln� projekci.</li>
<li>Spo��t�me posunut� sou�adnic textury vyn�soben�m dan�ch sou�adnic textury (s a t) a v a MAX_EMBOSS: ds = s*v*MAX_EMBOSS, dt= t*v*MAX_EMBOSS. V�imn�te si, �e s,t a v jsou vektory, ale MAX_EMBOSS nen�.</li>
<li>V druh� f�zi p�id�me posunut� k sou�adnic�m textury.</li>
</ul>

<p><b>Pro� je to dobr�?</b></p>

<ul>
<li>Rychlost (jen p�r odmocnin a n�soben� vertex�)</li>
<li>Vypad� dob�e!</li>
<li>Funguje se v�emi povrchy, nejen s rovinami.</li>
<li>B�� na v�ech akceler�torech.</li>
<li>Je glBegin/glEnd p��telsk�: nepot�ebuje &quot;zak�zan�&quot; GL p��kazy.</li>
</ul>

<p><b>Nev�hody:</b></p>

<ul>
<li>Nen� �pln� fyz�k�ln� spr�vn�.</li>
<li>Zanech�v� men�� artefakty.</li>
</ul>

<p>Tento n��rtek ukazuje, kde se nach�zej� jednotliv� vektory. M��ete jednodu�e z�skat t a s ode�ten�m jesnosti jednotliv�ch vektor�, ale ujist�te se, �e jsou spr�vn� nato�en� a normalizovan�. Modr� bod ozna�uje vertex, kde je namapov�n texCoord2f(0.0f, 0.0f).</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_22_image008.jpg" width="412" height="243" alt="Lokace vektor�" /></div>

<p><b>Konec teorie (algoritmy pro Emboss Bump Mapping)</b></p>

<hr />

<p>Te� se pod�vejme na gener�tor posunut� textury. Tato funkce se jmenuje SetUpBumps().</p>

<p class="src0"><span class="kom">// Funkce nastav� posunut� textury</span></p>
<p class="src0"><span class="kom">// n : norm�la k plo�e, mus� m�t d�lku 1</span></p>
<p class="src0"><span class="kom">// c : n�jak� bod na povrchu</span></p>
<p class="src0"><span class="kom">// l : pozice sv�tla</span></p>
<p class="src0"><span class="kom">// s : sm�r texturovac�ch sou�adnic s (mus� b�t normalizov�n!)</span></p>
<p class="src0"><span class="kom">// t : sm�r texturovac�ch sou�adnic t (mus� b�t normalizov�n!)</span></p>
<p class="src"></p>
<p class="src0">void SetUpBumps(GLfloat *n, GLfloat *c, GLfloat *l, GLfloat *s, GLfloat *t)</p>
<p class="src0">{</p>
<p class="src1">GLfloat v[3];<span class="kom">// Vertex z aktu�ln� pozice ke sv�tlu</span></p>
<p class="src1">GLfloat lenQ;<span class="kom">// Pou�ito p�i normalizaci</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Spo��t�n� v z aktu�ln�ho vertexu c ke sv�tlu a jeho normalizace</span></p>
<p class="src1">v[0] = l[0] - c[0];</p>
<p class="src1">v[1] = l[1] - c[1];</p>
<p class="src1">v[2] = l[2] - c[2];</p>
<p class="src"></p>
<p class="src1">lenQ = (GLfloat) sqrt(v[0]*v[0] + v[1]*v[1] + v[2]*v[2]);</p>
<p class="src"></p>
<p class="src1">v[0] /= lenQ;</p>
<p class="src1">v[1] /= lenQ;</p>
<p class="src1">v[2] /= lenQ;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zohledn�n� v tak, abychom dostali texturovac� sou�adnice</span></p>
<p class="src1">c[0] = (s[0]*v[0] + s[1]*v[1] + s[2]*v[2]) * MAX_EMBOSS;</p>
<p class="src1">c[1] = (t[0]*v[0] + t[1]*v[1] + t[2]*v[2]) * MAX_EMBOSS;</p>
<p class="src0">}</p>

<p>Nep�ipad� v�m to tak komplikovan� jako p�edt�m? Teorie je ale d�le�it�, abyste pochopili jak efekt funguje a jak ho ovl�dat. B�hem psan� tutori�lu jsem se to s�m nau�il :-]</p>

<p>V�dycky jsem cht�l zobrazit logo p�i b�hu uk�zkov�ho programu. My te� taky dv� zobraz�me. Zavol�me funkci doLogo(). Ta vyresetuje GL_MODELVIEW matici, kter� mus� b�t p�i posledn�m pr�chodu zavol�na.</p>

<p>Tato funkce zobraz� dv� loga: OpenGl logo a logo multitexturingu, pokud je povolen. Loga jsou z��sti pr�hledn�. Proto�e maj� alpha kan�l, sm�ch�me je pomoc� GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA podle OpenGL dokumentace. Ob� dv� jsou ploch�, nem�me pro n� sou�adnici z. ��sla pou�it� pro hrany jsou zji�t�ny "empiricky" (pokus-chyba), tak aby loga padla p�kn� do ro�k�. Mus�me zapnout blending a vypnout sv�tla, abychom se vyhli chybn�m efekt�m. Abychom zajistili, �e loga budou v�dy vep�edu, vyresetujeme GL_MODELVIEW matici a nastav�me funkci na testov�n� hloubky na GL_ALWAYS.</p>

<p class="src0">void doLogo(void)<span class="kom">// MUS� SE ZAVOLAT A� NAKONEC!!! Zobraz� dv� loga</span></p>
<p class="src0">{</p>
<p class="src1">glDepthFunc(GL_ALWAYS);</p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE_MINUS_SRC_ALPHA);</p>
<p class="src"></p>
<p class="src1">glEnable(GL_BLEND);</p>
<p class="src1">glDisable(GL_LIGHTING);</p>
<p class="src"></p>
<p class="src1">glLoadIdentity();</p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D,glLogo);</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2">glTexCoord2f(0.0f,0.0f); glVertex3f(0.23f, -0.4f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f,0.0f); glVertex3f(0.53f, -0.4f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f,1.0f); glVertex3f(0.53f, -0.25f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f,1.0f); glVertex3f(0.23f, -0.25f,-1.0f);</p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">if (useMultitexture)</p>
<p class="src1">{</p>
<p class="src2">glBindTexture(GL_TEXTURE_2D,multiLogo);</p>
<p class="src"></p>
<p class="src2">glBegin(GL_QUADS);</p>
<p class="src3">glTexCoord2f(0.0f,0.0f); glVertex3f(-0.53f, -0.4f,-1.0f);</p>
<p class="src3">glTexCoord2f(1.0f,0.0f); glVertex3f(-0.33f, -0.4f,-1.0f);</p>
<p class="src3">glTexCoord2f(1.0f,1.0f); glVertex3f(-0.33f, -0.3f,-1.0f);</p>
<p class="src3">glTexCoord2f(0.0f,1.0f); glVertex3f(-0.53f, -0.3f,-1.0f);</p>
<p class="src2">glEnd();</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glDepthFunc(GL_LEQUAL);</p>
<p class="src0">}</p>

<p>Te� p�ich�z� funkce na bumpmapping bez texturingu. Je to t��-pr�chodov� implementace. Jako prvn� GL_MODELVIEW matice se p�evr�t� pomoc� aplikace v�ech proveden�ch krok� v opa�n�m po�ad� a obr�cen� na matici dan� identity. V�sledkem je matice, kter� p�i aplikaci na objekt &quot;vrac�&quot; GL_MODELVIEW. My j� jednodu�e z�sk�me funkc� glGetFloatv(). Pamatujte, �e matice mus� b�t pole s 16 prvky a �e je tato matice &quot;p�esunuta&quot;!</p>

<p>Mimochodem: Kdy� p�esn� nev�te, jak se s matic� manipuluje, zva�te pou�it� glob�ln�ch sou�adnic, proto�e p�evracen� matice je slo�it� a n�ro�n� na �as. Ale pokud pou��v�te mnoho vertex�, p�evracen� matice m��e b�t daleko rychlej��.</p>

<p class="src0">bool doMesh1TexelUnits(void)</p>
<p class="src0">{</p>
<p class="src1">GLfloat c[4] = {0.0f, 0.0f, 0.0f, 1.0f};<span class="kom">// Aktu�ln� vertex</span></p>
<p class="src1">GLfloat n[4] = {0.0f, 0.0f, 0.0f, 1.0f};<span class="kom">// Normalizovan� norm�la dan�ho povrchu</span></p>
<p class="src1">GLfloat s[4] = {0.0f, 0.0f, 0.0f, 1.0f};<span class="kom">// Sm�r texturovac�ch sou�adnic s, normalizov�no</span></p>
<p class="src1">GLfloat t[4] = {0.0f, 0.0f, 0.0f, 1.0f};<span class="kom">// Sm�r texturovac�ch sou�adnic t, normalizov�no</span></p>
<p class="src"></p>
<p class="src1">GLfloat l[4];<span class="kom">// Pozice sv�tla, kter� bude transformov�na do prostoru objektu</span></p>
<p class="src1">GLfloat Minv[16];<span class="kom">// P�evr�cen� modelview matice</span></p>
<p class="src"></p>
<p class="src1">int i;</p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Sestaven� p�evr�cen� modelview matice; nahrad� funkce Push a Pop jednou funkc� glLoadIdentity()</span></p>
<p class="src1"><span class="kom">// Jednoduch� sestaven� t�m, �e v�echny transformace provedeme opa�n� a v opa�n�m po�ad�</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();</p>
<p class="src"></p>
<p class="src1">glRotatef(-yrot,0.0f,1.0f,0.0f);</p>
<p class="src1">glRotatef(-xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glTranslatef(0.0f,0.0f,-z);</p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX,Minv);</p>
<p class="src"></p>
<p class="src1">glLoadIdentity();</p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,z);</p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Transformace pozice sv�tla do sou�adnic objektu:</span></p>
<p class="src1">l[0] = LightPosition[0];</p>
<p class="src1">l[1] = LightPosition[1];</p>
<p class="src1">l[2] = LightPosition[2];</p>
<p class="src1">l[3] = 1.0f;<span class="kom">// Homogen� sou�adnice</span></p>
<p class="src"></p>
<p class="src1">VMatMult(Minv,l);</p>

<p>Prvn� f�ze:</p>

<ul>
<li>Pou�it� bump textury</li>
<li>Vypnut� blendingu</li>
<li>Vypnut� sv�tel</li>
<li>Pou�it� texturovac�ch sou�adnic bez posunut�</li>
<li>Vytvo�en� geometrie</li>
</ul>

<p>Tohle vyrenderuje krychli pouze z bumpmap.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, bump[filter]);</p>
<p class="src1">glDisable(GL_BLEND);</p>
<p class="src1">glDisable(GL_LIGHTING);</p>
<p class="src1">doCube();</p>
<p class="src"></p>

<p>Druh� f�ze:</p>

<ul>
<li>Pou�it� p�evr�cen� bumpmapy</li>
<li>Povolen� blendingu GL_ONE, GL_ONE</li>
<li>Ponech� vypnut� sv�tla</li>
<li>Pou�it� posunut�ch texturovac�ch sou�adnic (P�ed ka�dou st�nou krychle mus�me zavolat funkci SetUpBumps())</li>
<li>Vytvo�en� geometrie</li>
</ul>

<p>Tohle vyrendruje krychli se spr�vn�m emboss bumpmappingem, ale bez barev.</p>
<p>Mohli bychom u�et�it �as rotac� vektoru sv�tla opa�n�m sm�rem. To v�ak nefunguje �pln� spr�vn�, tak to ud�l�me jinou cestou: oto��me ka�dou norm�lu a prost�edn� bod stejn� jako na�i geometrii.</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D,invbump[filter]);</p>
<p class="src1">glBlendFunc(GL_ONE,GL_ONE);</p>
<p class="src1">glDepthFunc(GL_LEQUAL);</p>
<p class="src1">glEnable(GL_BLEND);</p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// P�edn� st�na</span></p>
<p class="src2">n[0] = 0.0f;</p>
<p class="src2">n[1] = 0.0f;</p>
<p class="src2">n[2] = 1.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = 1.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 1.0f;</p>
<p class="src2">t[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">for (i=0; i&lt;4; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glTexCoord2f(data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Zadn� st�na</span></p>
<p class="src2">n[0] = 0.0f;</p>
<p class="src2">n[1] = 0.0f;</p>
<p class="src2">n[2] = -1.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = -1.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 1.0f;</p>
<p class="src2">t[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">for (i=4; i&lt;8; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glTexCoord2f(data[5*i]+c[0], data[5*i+1]+c[1]); </p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Horn� st�na</span></p>
<p class="src2">n[0] = 0.0f;</p>
<p class="src2">n[1] = 1.0f;</p>
<p class="src2">n[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = 1.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 0.0f;</p>
<p class="src2">t[2] = -1.0f;</p>
<p class="src"></p>
<p class="src2">for (i=8; i&lt;12; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glTexCoord2f(data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Spodn� st�na</span></p>
<p class="src2">n[0] = 0.0f;</p>
<p class="src2">n[1] = -1.0f;</p>
<p class="src2">n[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = -1.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 0.0f;</p>
<p class="src2">t[2] = -1.0f;</p>
<p class="src"></p>
<p class="src2">for (i=12; i&lt;16; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>

<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glTexCoord2f(data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Prav� st�na</span></p>
<p class="src2">n[0] = 1.0f;</p>
<p class="src2">n[1] = 0.0f;</p>
<p class="src2">n[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = 0.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = -1.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 1.0f;</p>
<p class="src2">t[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">for (i=16; i&lt;20; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glTexCoord2f(data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Lev� st�na</span></p>
<p class="src2">n[0] = -1.0f;</p>
<p class="src2">n[1] = 0.0f;</p>
<p class="src2">n[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = 0.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 1.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 1.0f;</p>
<p class="src2">t[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">for (i=20; i&lt;24; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glTexCoord2f(data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src1">glEnd();</p>

<p>T�et� f�ze:</p>

<ul>
<li>Pou�it� z�kladn� barevn� textury</li>
<li>Povolun� blendingu GL_DST_COLOR, GL_SRC_COLOR</li>
<li>Tuto blending rovnici n�sobit dv�ma: (Cdst*Csrc)+(Csrc*Cdst) = 2(Csrc*Cdst)!</li>
<li>Povolen� sv�tel, aby vytvo�ily ambientn� a rozpt�len� sv�tlo</li>
<li>Vr�cen� GL_TEXTURE matice zp�t na "norm�ln�" texturovac� sou�adnice</li>
<li>Vytvo�it geometrii</li>
</ul>

<p>Tohle dokon�� renderov�n� krychle s osv�tlen�m. Nejd��ve mus�me nastavit texture environment na GL_MODULATE. M��eme zap�nat a vyp�nat multitexturing. Tuto f�zi provedeme, jen pokud u�ivatel nechce vid�t pouze emboss.</p>

<p class="src1">if (!emboss)</p>
<p class="src1">{</p>
<p class="src2">glTexEnvf(GL_TEXTURE_ENV, GL_TEXTURE_ENV_MODE, GL_MODULATE);</p>
<p class="src2">glBindTexture(GL_TEXTURE_2D,texture[filter]);</p>
<p class="src2">glBlendFunc(GL_DST_COLOR,GL_SRC_COLOR);</p>
<p class="src2">glEnable(GL_LIGHTING);</p>
<p class="src"></p>
<p class="src2">doCube();</p>
<p class="src1">}</p>

<p>Posledn� f�ze:</p>

<ul>
<li>Pooto�en� krychle pro p���t� kreslen�</li>
<li>Nakreslen� log</li>
</ul>

<p class="src1">xrot += xspeed;</p>
<p class="src1">yrot += yspeed;</p>
<p class="src"></p>
<p class="src1">if (xrot &gt; 360.0f)</p>
<p class="src2">xrot -= 360.0f;</p>
<p class="src"></p>
<p class="src1">if (xrot &lt; 0.0f)</p>
<p class="src2">xrot += 360.0f;</p>
<p class="src"></p>
<p class="src1">if (yrot &gt; 360.0f)</p>
<p class="src2">yrot -= 360.0f;</p>
<p class="src"></p>
<p class="src1">if (yrot &lt; 0.0f)</p>
<p class="src2">yrot += 360.0f;</p>
<p class="src"></p>
<p class="src1">doLogo();<span class="kom">// Nakonec loga</span></p>
<p class="src"></p>
<p class="src1">return true;</p>
<p class="src0">}</p>

<p>Dal�� funkce ud�l� tohle v�echno ve dvou f�z�ch s podporou multitexturingu. Pou�ijeme dv� texturovac� jednotky. V�ce by bylo extr�mn� obt��n� vzhledem k blendingov�m rovnic�m. L�pe pou��t TNT. V�imn�te si, �e se funkce li�� od doMesh1TexelUnits() jen t�m, �e pos�l�me dv� sady texturovac�ch sou�adnich na ka�d� vertex!</p>

<p class="src0">bool doMesh2TexelUnits(void)</p>
<p class="src0">{</p>
<p class="src1">GLfloat c[4] = {0.0f,0.0f,0.0f,1.0f};<span class="kom">// Aktu�ln� vertex</span></p>
<p class="src1">GLfloat n[4] = {0.0f,0.0f,0.0f,1.0f};<span class="kom">// Normalizovan� norm�la povrchu</span></p>
<p class="src1">GLfloat s[4] = {0.0f,0.0f,0.0f,1.0f};<span class="kom">// Sm�r texturovac�ch sou�adnic s, normalizov�no</span></p>
<p class="src1">GLfloat t[4] = {0.0f,0.0f,0.0f,1.0f};<span class="kom">// Sm�r texturovac�ch sou�adnic t, normalizov�no</span></p>
<p class="src"></p>
<p class="src1">GLfloat l[4];<span class="kom">// Pozice sv�tla k p�eveden� na sou�adnice objektu</span></p>
<p class="src1">GLfloat Minv[16];<span class="kom">// P�evr�cen� modelview matice</span></p>
<p class="src"></p>
<p class="src1">int i;</p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Sestaven� p�evr�cen� modelview matice, tohle nahrad� funkce Push a Pop jednou funkc� glLoadIdentity()</span></p>
<p class="src1"><span class="kom">// Jednoduch� sestaven� t�m, �e v�echny transformace provedeme opa�n� a v opa�n�m po�ad�</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();</p>
<p class="src"></p>
<p class="src1">glRotatef(-yrot,0.0f,1.0f,0.0f);</p>
<p class="src1">glRotatef(-xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glTranslatef(0.0f,0.0f,-z);</p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX,Minv);</p>
<p class="src"></p>
<p class="src1">glLoadIdentity();</p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,z);</p>
<p class="src"></p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Transformace pozice sv�tla na sou�adnice objektu:</span></p>
<p class="src1">l[0] = LightPosition[0];</p>
<p class="src1">l[1] = LightPosition[1];</p>
<p class="src1">l[2] = LightPosition[2];</p>
<p class="src1">l[3] = 1.0f;<span class="kom">// Homogen� sou�adnice</span></p>
<p class="src"></p>
<p class="src1">VMatMult(Minv,l);</p>

<p>Prvn� f�ze:</p>

<ul>
<li>Bez blendingu</li>
<li>Bez sv�tel</li>
</ul>

<p>Nastaven� texture combineru 0 na</p>

<ul>
<li>Pou�it� bumpmapy</li>
<li>Pou�it� neposunut�ch texturovac�ch sou�adnic</li>
<li>Nastavev� operace s texturou na GL_REPLACE, kter� pouze vykresl� texturu</li>
</ul>

<p>Nastaven� texture combineru 1 na</p>

<ul>
<li>Posunut� texturovac� sou�adnice</li>
<li>Nastaven� operace s texturou na GL_ADD, co� je multitexturovac�m ekvivalentem k ONE, ONE blendingu</li>
</ul>

<p>Tohle vyrenderuje krychli skl�daj�c� se z �ed�ch map.</p>

<p class="src1"><span class="kom">// TEXTUROVAC� JEDNOTKA #0:</span></p>
<p class="src1">glActiveTextureARB(GL_TEXTURE0_ARB);</p>
<p class="src1">glEnable(GL_TEXTURE_2D);</p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, bump[filter]);</p>
<p class="src1">glTexEnvf (GL_TEXTURE_ENV, GL_TEXTURE_ENV_MODE, GL_COMBINE_EXT);</p>
<p class="src1">glTexEnvf (GL_TEXTURE_ENV, GL_COMBINE_RGB_EXT, GL_REPLACE);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// TEXTUROVAC� JEDNOTKA #1:</span></p>
<p class="src1">glActiveTextureARB(GL_TEXTURE1_ARB);</p>
<p class="src1">glEnable(GL_TEXTURE_2D);</p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, invbump[filter]);</p>
<p class="src1">glTexEnvf (GL_TEXTURE_ENV, GL_TEXTURE_ENV_MODE, GL_COMBINE_EXT);</p>
<p class="src1">glTexEnvf (GL_TEXTURE_ENV, GL_COMBINE_RGB_EXT, GL_ADD);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Obecn� p�ep�na�e</span></p>
<p class="src1">glDisable(GL_BLEND);</p>
<p class="src1">glDisable(GL_LIGHTING);</p>

<p>Te� pouze vyrenderujeme st�ny jednu po druh� jako v doMesh1TexelUnits(). Pouze jedna novinka: pou��v� glMultiTexCoordfARB() m�sto glTexCoord2f(). V�imn�te si, �e v prvn�m parametru je uvedeno, kter� texturovac� jednotce p��slu�� sou�adnice. Parametr mus� b�t GL_TEXTUREi_ARB, kde i je v intervalu od 0 do 31.</p>

<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2"><span class="kom">// P�edn� st�na</span></p>
<p class="src2">n[0] = 0.0f;</p>
<p class="src2">n[1] = 0.0f;</p>
<p class="src2">n[2] = 1.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = 1.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 1.0f;</p>
<p class="src2">t[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">for (i=0; i&lt;4; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE0_ARB, data[5*i], data[5*i+1]);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE1_ARB, data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Zadn� st�na</span></p>
<p class="src2">n[0] = 0.0f;</p>
<p class="src2">n[1] = 0.0f;</p>
<p class="src2">n[2] = -1.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = -1.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 1.0f;</p>
<p class="src2">t[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">for (i=4; i&lt;8; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE0_ARB,data[5*i], data[5*i+1]);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE1_ARB,data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Horn� st�na</span></p>
<p class="src2">n[0] = 0.0f;</p>
<p class="src2">n[1] = 1.0f;</p>
<p class="src2">n[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = 1.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 0.0f;</p>
<p class="src2">t[2] = -1.0f;</p>
<p class="src"></p>
<p class="src2">for (i=8; i&lt;12; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE0_ARB,data[5*i], data[5*i+1]);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE1_ARB,data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Doln� st�na</span></p>
<p class="src2">n[0] = 0.0f;</p>
<p class="src2">n[1] = -1.0f;</p>
<p class="src2">n[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = -1.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 0.0f;</p>
<p class="src2">t[2] = -1.0f;</p>
<p class="src"></p>
<p class="src2">for (i=12; i&lt;16; i++)</p>
<p class="src2">{</p>
<p class="src3">>c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE0_ARB,data[5*i], data[5*i+1]);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE1_ARB,data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Prav� st�na</span></p>
<p class="src2">n[0] = 1.0f;</p>
<p class="src2">n[1] = 0.0f;</p>
<p class="src2">n[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = 0.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = -1.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 1.0f;</p>
<p class="src2">t[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">for (i=16; i&lt;20; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE0_ARB,data[5*i], data[5*i+1]);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE1_ARB,data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Lev� st�na</span></p>
<p class="src2">n[0] = -1.0f;</p>
<p class="src2">n[1] = 0.0f;</p>
<p class="src2">n[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">s[0] = 0.0f;</p>
<p class="src2">s[1] = 0.0f;</p>
<p class="src2">s[2] = 1.0f;</p>
<p class="src"></p>
<p class="src2">t[0] = 0.0f;</p>
<p class="src2">t[1] = 1.0f;</p>
<p class="src2">t[2] = 0.0f;</p>
<p class="src"></p>
<p class="src2">for (i=20; i&lt;24; i++)</p>
<p class="src2">{</p>
<p class="src3">c[0] = data[5*i+2];</p>
<p class="src3">c[1] = data[5*i+3];</p>
<p class="src3">c[2] = data[5*i+4];</p>
<p class="src"></p>
<p class="src3">SetUpBumps(n,c,l,s,t);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE0_ARB,data[5*i], data[5*i+1]);</p>
<p class="src3">glMultiTexCoord2fARB(GL_TEXTURE1_ARB,data[5*i]+c[0], data[5*i+1]+c[1]);</p>
<p class="src3">glVertex3f(data[5*i+2], data[5*i+3], data[5*i+4]);</p>
<p class="src2">}</p>
<p class="src1">glEnd();</p>

<p>Druh� f�ze:</p>

<ul>
<li>Pou�it� z�kladn� textury</li>
<li>Povolen� osv�tlen�</li>
<li>Neposunut� texturovac� sou�adnice - vyresetovat GL_TEXTURE matice</li>
<li>Nastaven� texture environment na GL_MODULATE</li>
</ul>

<p>Tohle vyrenderuje celou bumpmapovanou krychli.</p>

<p class="src1">glActiveTextureARB(GL_TEXTURE1_ARB);</p>
<p class="src1">glDisable(GL_TEXTURE_2D);</p>
<p class="src1">glActiveTextureARB(GL_TEXTURE0_ARB);</p>
<p class="src"></p>
<p class="src1">if (!emboss)</p>
<p class="src1">{</p>
<p class="src2">glTexEnvf (GL_TEXTURE_ENV, GL_TEXTURE_ENV_MODE, GL_MODULATE);</p>
<p class="src"></p>
<p class="src2">glBindTexture(GL_TEXTURE_2D,texture[filter]);</p>
<p class="src2">glBlendFunc(GL_DST_COLOR,GL_SRC_COLOR);</p>
<p class="src"></p>
<p class="src2">glEnable(GL_BLEND);</p>
<p class="src2">glEnable(GL_LIGHTING);</p>
<p class="src"></p>
<p class="src2">doCube();</p>
<p class="src1">}</p>

<p>Posledn� f�ze:</p>

<ul>
<li>Pooto�en� krychle</li>
<li>Nakreslen� log</li>
</ul>

<p class="src1">xrot += xspeed;</p>
<p class="src1">yrot += yspeed;</p>
<p class="src"></p>
<p class="src1">if (xrot&gt;360.0f)</p>
<p class="src2">xrot -= 360.0f;</p>
<p class="src"></p>
<p class="src1">if (xrot&lt;0.0f)</p>
<p class="src2">xrot += 360.0f;</p>
<p class="src"></p>
<p class="src1">if (yrot&gt;360.0f)</p>
<p class="src2">yrot -= 360.0f;</p>
<p class="src"></p>
<p class="src1">if (yrot&lt;0.0f)</p>
<p class="src2">yrot += 360.0f;</p>
<p class="src"></p>
<p class="src1">doLogo();<span class="kom">// Nakonec loga</span></p>
<p class="src"></p>
<p class="src1">return true;</p>
<p class="src0">}</p>

<p>Kone�n� funkce na renderov�n� bez bumpmappingu - abychom mohli vid�t ten rozd�l!</p>

<p class="src0">bool doMeshNoBumps(void)</p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f,0.0f,z);</p>
<p class="src"></p>
<p class="src1">glRotatef(xrot,1.0f,0.0f,0.0f);</p>
<p class="src1">glRotatef(yrot,0.0f,1.0f,0.0f);</p>
<p class="src"></p>
<p class="src1">if (useMultitexture)</p>
<p class="src1">{</p>
<p class="src2">glActiveTextureARB(GL_TEXTURE1_ARB);</p>
<p class="src2">glDisable(GL_TEXTURE_2D);</p>
<p class="src2">glActiveTextureARB(GL_TEXTURE0_ARB);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glDisable(GL_BLEND);</p>
<p class="src1">glBindTexture(GL_TEXTURE_2D,texture[filter]);</p>
<p class="src1">glBlendFunc(GL_DST_COLOR,GL_SRC_COLOR);</p>
<p class="src1">glEnable(GL_LIGHTING);</p>
<p class="src"></p>
<p class="src1">doCube();</p>
<p class="src"></p>
<p class="src1">xrot += xspeed;</p>
<p class="src1">yrot += yspeed;</p>
<p class="src"></p>
<p class="src1">if (xrot&gt;360.0f)</p>
<p class="src2">xrot -= 360.0f;</p>
<p class="src"></p>
<p class="src1">if (xrot&lt;0.0f)</p>
<p class="src2">xrot += 360.0f;</p>
<p class="src"></p>
<p class="src1">if (yrot&gt;360.0f)</p>
<p class="src2">yrot -= 360.0f;</p>
<p class="src"></p>
<p class="src1">if (yrot&lt;0.0f)</p>
<p class="src2">yrot += 360.0f;</p>
<p class="src"></p>
<p class="src1">doLogo();<span class="kom">// Nakonec loga</span></p>
<p class="src"></p>
<p class="src1">return true;</p>
<p class="src0">}</p>

<p>V�e co mus� drawGLScene() ud�lat je rozhodnout jakou doMesh funkci zavolat.</p>

<p class="src0">bool DrawGLScene(GLvoid)<span class="kom">// V�echno kreslen�</span></p>
<p class="src0">{</p>
<p class="src1">if (bumps)</p>
<p class="src1">{</p>
<p class="src2">if (useMultitexture &amp;&amp; maxTexelUnits &gt; 1)</p>
<p class="src3">return doMesh2TexelUnits();</p>
<p class="src2">else</p>
<p class="src3">return doMesh1TexelUnits();</p>
<p class="src1">}</p>
<p class="src1">else</p>
<p class="src2">return doMeshNoBumps();</p>
<p class="src0">}</p>

<p>Hlavn� funkce Windows, p�id�ny n�kter� kl�vesy:</p>

<ul>
<li>E: p�ep�n�n� Emboss/bumpmapov� m�d</li>
<li>M: vyp�n�n� a zap�n�n� multitexturingu</li>
<li>B: vyp�n�n� a zap�n�n� bumpmappingu, pouze v emboss m�du</li>
<li>F: p�ep�n�n� filtr�, GL_NEAREST nen� vhodn� pro bumpmapping</li>
<li>KURSOROV� KL�VESY: ot��en� krychle</li>
</ul>

<p class="src0">int WINAPI WinMain(HINSTANCE hInstance,  HINSTANCE hPrevInstance, LPSTR lpCmdLine, int nCmdShow)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Za��tek z�st�v� nezm�n�n</span></p>
<p class="src4">if (keys['E'])</p>
<p class="src4">{</p>
<p class="src5">keys['E']=false;</p>
<p class="src5">emboss=!emboss;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys['M'])</p>
<p class="src4">{</p>
<p class="src5">keys['M']=false;</p>
<p class="src5">useMultitexture=((!useMultitexture) &amp;&amp; multitextureSupported);</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys['B'])</p>
<p class="src4">{</p>
<p class="src5">keys['B']=false;</p>
<p class="src5">bumps=!bumps;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys['F'])</p>
<p class="src4">{</p>
<p class="src5">keys['F']=false;</p>
<p class="src5">filter++;</p>
<p class="src5">filter%=3;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_PRIOR])</p>
<p class="src4">{</p>
<p class="src5">z-=0.02f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_NEXT])</p>
<p class="src4">{</p>
<p class="src5">z+=0.02f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_UP])</p>
<p class="src4">{</p>
<p class="src5">xspeed-=0.01f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN])</p>
<p class="src4">{</p>
<p class="src5">xspeed+=0.01f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_RIGHT])</p>
<p class="src4">{</p>
<p class="src5">yspeed+=0.01f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_LEFT])</p>
<p class="src4">{</p>
<p class="src5">yspeed-=0.01f;</p>
<p class="src4">}</p>
<p class="src1"><span class="kom">// Konec tak� nezm�n�n</span></p>
<p class="src0">}</p>

<p>Te� kdy� jsme zvl�dli tento tutori�l, p�r slov o generov�n� textur a bumpmapov�ch objekt�. P�edt�m, ne� za�nete programovat ambici�zn� hry a budete se divit, pro� bumpmapping nen� tak rychl� a nevypad� tak dob�e, p�e�t�te si toto:</p>

<ul>
<li>Nem�li byste pou��vat textury 256x256 jako v t�to lekci. To v�e hodn� zpomal�. Pou��vejte je pouze p�i demonstrac�ch.</li>
<li>Bumpmapovan� krychle nen� b�n�. To��c� se je�t� m�n�. D�vodem je �hel pohledu: ��m ost�ej�� �hel, t�m v�ce optick�ch chyb se kv�li filtrov�n� objev�. Skoro v�echny multif�zov� algoritmy t�mto trp�. Abyste se vyhli pou��v�n� velmi detailn�ch textur, zredukujte �hly viditelnosti na minimum a p�edfiltrujte textury tak, aby dokonale sedly na tento rozptyl �hl�.</li>
<li>Nejd��ve byste m�li m�t barevnou texturu. Z n� se d� velmi snadno pomoc� pr�m�rn�no grafick�ho programu ud�lat textura ve stupn�ch �edi.</li>
<li>Bumpmapa by m�la b�t &quot;ost�ej��&quot; a m�t v�t�� kontrast ne� barevn� textura. Toho v�t�inou doc�l�te pou�it�m n�jak�ho &quot;sparpening filtru&quot;. Z po��tku to mo�n� bude vypadat divn�, ale k dosa�en� kvalitn�ho efektu je to nutn�.</li>
<li>Bumpmapa by se barvama m�la bl��it 50% �ed� (RGB 127,127,127). Tato barva znamen� hladk� povrch, sv�tlej�� m�sta reprezentuj� r�hy. Tohoto m��ete dos�hnout u�it�m histogramu v n�kter�ch grafick�ch programech.</li>
<li>Bumpmapa m��e b�t �ty�ikr�t men�� ne� barevn� textura bez v�n�ho sn��en� kvality obrazu.</li>
</ul>

<p>Pod�kov�n�:</p>

<ul>
<li>Michael I. Gold za dokumentaci o bumpmappingu</li>
<li>Diego T�rtara za uk�zkov� k�d</li>
<li>nVidia za uk�zky na www</li>
<li>NeHe za to, �e m� nau�il mnoho o OpenGL</li>
</ul>

<p class="autor">napsal: Jens Schneider <?VypisEmail('schneide@pool.informatik.rwth-aachen.de');?><br />
p�elo�il: V�clav Slov��ek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson22.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson22_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson22.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson22.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson22.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson22.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson22.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson22.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:lucriz@inwind.it">Luca Rizzuti</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson22.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson22.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:classic@sover.net">Morgan Aldridge</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson22.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson22.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson22.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(22);?>
<?FceNeHeOkolniLekce(22);?>

<?
include 'p_end.php';
?>
