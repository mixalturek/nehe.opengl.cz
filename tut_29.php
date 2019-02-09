<?
$g_title = 'CZ NeHe OpenGL - Lekce 29 - Blitter, nahr�v�n� .RAW textur';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(29);?>

<h1>Lekce 29 - Blitter, nahr�v�n� .RAW textur</h1>

<p class="nadpis_clanku">V t�to lekci se nau��te, jak se nahr�vaj� .RAW obr�zky a konvertuj� se do textur. Dozv�te se tak� o blitteru, grafick� metod� p�en�en� dat, kter� umo��uje modifikovat textury pot�, co u� byly nahr�ny do programu. M��ete j�m zkop�rovat ��st jedn� textury do druh�, blendingem je sm�chat dohromady a tak� roztahovat. Mali�ko uprav�me program tak, aby v dob�, kdy nen� aktivn�, v�bec nezat�oval procesor.</p>

<p>Blitting... v po��ta�ov� grafice je toto slovo hodn� pou��van�. Ozna�uje se j�m zkop�rov�n� ��sti jedn� textury a vlo�en� do druh�. Pokud programujete ve Win API nebo MFC, jist� jste sly�eli o funkc�ch BitBlt() nebo StretchBlt(). P�esn� toto se pokus�me vytvo�it.</p>

<p>Chcete-li napsat funkci, kter� implementuje blitting, m�li byste n�co v�d�t o line�rn� grafick� pam�ti. Kdy� se pod�v�te na monitor, vid�te spousty bod� reprezentuj�c�ch n�jak� obr�zek, ovl�dac� prvky nebo t�eba kurzor my�i. V�e je prost� slo�eno z matice pixel�. Ale jak v� grafick� karta nebo BIOS, jak nakreslit bod nap��klad na sou�adnic�ch [64; 64]? Jednodu�e! V�echno, co je na obrazovce nen� v matici, ale v line�rn� pam�ti (v jednorozm�rn�m poli). Pozici bodu v pam�ti m��eme z�skat n�sleduj�c� rovnic�:</p>

<p class="src0"><span class="kom">adresa_v_pam�ti = (pozice_y * rozli�en�_obrazovky_x) + pozice_x</span></p>

<p>Pokud m�me rozli�en� obrazovky 640x480, bude bod [64; 64] um�st�n na pam�ov� adrese (64*640) + 64 = 41024. Proto�e pam�, do kter� budeme ukl�dat bitmapy je tak� line�rn�, m��eme t�to vlastnosti vyu��t p�i p�en�en� blok� grafick�ch dat. V�slednou adresu je�t� budeme n�sobit barevnou hloubkou obr�zku, proto�e nepou��v�te jedno-bytov� pixely (256 barev), ale RGBA obr�zky. Pokud jste tento v�klad nepochopili, nem� cenu j�t d�l...</p>

<p>Vytvo��me strukturu TEXTURE_IMAGE, kter� bude obsahovat informace o nahr�van�m obr�zku - ���ku, v��ku, barevnou hloubku. Pointer data bude ukazovat do dynamick� pam�ti, kam nahrajeme ze souboru data obr�zku.</p>

<p class="src0">typedef struct Texture_Image<span class="kom">// Struktura obr�zku</span></p>
<p class="src0">{</p>
<p class="src1">int width;<span class="kom">// ���ka v pixelech</span></p>
<p class="src1">int height;<span class="kom">// V��ka v pixelech</span></p>
<p class="src1">int format;<span class="kom">// Barevn� hloubka v bytech na pixel</span></p>
<p class="src1">unsigned char *data;<span class="kom">// Data obr�zku</span></p>
<p class="src0">} TEXTURE_IMAGE;</p>

<p>Dal�� datov� typ je ukazatelem na pr�v� vytvo�enou strukturu. Po n�m n�sleduj� dv� prom�nn� t1 a t2. Do nich budeme nahr�vat obr�zky, kter� potom blittingem slou��me do jednoho a vytvo��me z n�j texturu.</p>

<p class="src0">typedef TEXTURE_IMAGE *P_TEXTURE_IMAGE;<span class="kom">// Datov� typ ukazatele na obr�zek</span></p>
<p class="src"></p>
<p class="src0">P_TEXTURE_IMAGE t1;<span class="kom">// Dva obr�zky</span></p>
<p class="src0">P_TEXTURE_IMAGE t2;</p>
<p class="src"></p>
<p class="src0">GLuint texture[1];<span class="kom">// Jedna textura</span></p>

<p>Rot prom�nn� ur�uj� �hel rotace v�sledn�ho objektu. Nic nov�ho.</p>

<p class="src0">GLfloat xrot;<span class="kom">// X rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y rotace</span></p>
<p class="src0">GLfloat zrot;<span class="kom">// Z rotace</span></p>

<p>Funkc� AllocateTextureBuffer(), alokujeme dynamickou pam� pro obr�zek a vr�t�me ukazatel. P�i ne�sp�chu se vrac� NULL. Funkci p�ed�v� program celkem t�i parametry: ���ku, v��ku a barevnou hloubku v bytech na pixel.</p>

<p class="src0">P_TEXTURE_IMAGE AllocateTextureBuffer(GLint w, GLint h, GLint f)<span class="kom">// Alokuje pam� pro obr�zek</span></p>
<p class="src0">{</p>

<p>Ukazatel na obr�zek ti vr�t�me na konci funkce volaj�c�mu k�du. Na za��tku ho inicializujeme na NULL. Prom�nn� c, p�i�ad�me tak� NULL. P�edstavuje �lo�i�t� nahr�van�ch dat.</p>

<p class="src1">P_TEXTURE_IMAGE ti = NULL;<span class="kom">// Ukazatel na strukturu obr�zku</span></p>
<p class="src1">unsigned char *c = NULL;<span class="kom">// Ukazatel na data obr�zku</span></p>

<p>Pomoc� standardn� funkce malloc() se pokus�me alokovat dynamickou pam� pro strukturu obr�zku. Pokud se operace poda��, program pokra�uje d�le. P�i jak�koli chyb� vr�t� malloc() NULL. Vyp�eme chybovou zpr�vu a ozn�m�me volaj�c�mu k�du ne�sp�ch.</p>

<p class="src1">ti = (P_TEXTURE_IMAGE)malloc(sizeof(TEXTURE_IMAGE));<span class="kom">// Alokace pam�ti pro strukturu</span></p>
<p class="src"></p>
<p class="src1">if(ti != NULL)<span class="kom">// Poda�ila se alokace pam�ti?</span></p>
<p class="src1">{</p>

<p>Po �sp�n� alokaci pam�ti vypln�me strukturu atributy obr�zku. Barevn� hloubka nen� v obvykl�m form�tu bit na pixel, ale kv�li jednodu��� manipulaci s pam�t� v bytech na pixel.</p>

<p class="src2">ti-&gt;width = w;<span class="kom">// Nastav� atribut ���ky</span></p>
<p class="src2">ti-&gt;height = h;<span class="kom">// Nastav� atribut v��ky</span></p>
<p class="src2">ti-&gt;format = f;<span class="kom">// Nastav� atribut barevn� hloubky</span></p>

<p>Stejn�m zp�sobem jako pro strukturu alokujeme pam� i pro data obr�zku. Jej� velikost z�sk�me n�soben�m ���ky, v��ky a barevn� hloubky. P�i �sp�chu nastav�me atribut data struktury na pr�v� z�skanou dynamickou pam�, ne�sp�ch o�et��me stejn� jako minule.</p>

<p class="src2">c = (unsigned char *)malloc(w * h * f);<span class="kom">// Alokace pam�ti pro strukturu</span></p>
<p class="src"></p>
<p class="src2">if (c != NULL)<span class="kom">// Poda�ila se alokace pam�ti?</span></p>
<p class="src2">{</p>
<p class="src3">ti-&gt;data = c;<span class="kom">// Nastav� ukazatel na data</span></p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Alokace pam�ti pro data se nepoda�ila</span></p>
<p class="src2">{</p>
<p class="src3">MessageBox(NULL, &quot;Could Not Allocate Memory For A Texture Buffer&quot;, &quot;BUFFER ERROR&quot;, MB_OK | MB_ICONINFORMATION);</p>

<p>P�ekl.: Tady by spr�vn� m�la funkce vr�tit nam�sto NULL prom�nnou ti nebo je�t� l�pe p�ed opu�t�n�m funkce dealokovat dynamickou pam� struktury ti. Bez vr�cen� ukazatele nem��eme z venku pam� uvolnit. Pokud opera�n� syst�m nepracuje tak, jak m� (Toto nen� nar�ka na MS Windows :-), �ili po skon�en� neuvoln� poskytne zdroje programu, vznikaj� pam�ov� �niky.</p>

<p class="src3"><span class="kom">// Uvoln�n� pam�ti struktury (P�ekl.)</span></p>
<p class="src3"><span class="kom">// free(ti);</span></p>
<p class="src3"><span class="kom">// ti = NULL;</span></p>
<p class="src"></p>
<p class="src3">return NULL;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">else<span class="kom">// Alokace pam�ti pro strukturu se nepoda�ila</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Could Not Allocate An Image Structure&quot;,&quot;IMAGE STRUCTURE ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">return NULL;</p>
<p class="src1">}</p>

<p>Pokud dosud nebyly ��dn� probl�my, vr�t�me ukazatel na strukturu ti.</p>

<p class="src1">return ti;<span class="kom">// Vr�t� ukazatel na dynamickou pam�</span></p>
<p class="src0">}</p>

<p>Ve funkci DeallocateTexture() d�l�me prav� opak - uvol�ujeme pam� obr�zku, na kterou ukazuje p�edan� parametr t.</p>

<p class="src0">void DeallocateTexture(P_TEXTURE_IMAGE t)<span class="kom">// Uvoln� dynamicky alokovanou pam� obr�zku</span></p>
<p class="src0">{</p>
<p class="src1">if(t)<span class="kom">// Pokud struktura obr�zku existuje</span></p>
<p class="src1">{</p>
<p class="src2">if(t-&gt;data)<span class="kom">// Pokud existuj� data obr�zku</span></p>
<p class="src2">{</p>
<p class="src3">free(t-&gt;data);<span class="kom">// Uvoln� data obr�zku</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">free(t);<span class="kom">// Uvoln� strukturu obr�zku</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>V�echno u� m�me p�ipraven�, zb�v� jenom nahr�t .RAW obr�zek. RAW form�t je nejjednodu��� a nejrychlej�� zp�sob, jak nahr�t do programu texturu (samoz�ejm� krom� funkce auxDIBImageLoad()). Pro� je to tak jednoduch�? Proto�e .RAW form�t obsahuje pouze samotn� data bitmapy bez hlavi�ek nebo n��eho dal��ho. Jedin�, co mus�me ud�lat, je otev��t soubor a na��st data tak, jak jsou. T�m��... bohu�el tento form�t m� dv� nev�hody. Prvn� je to, �e ho neotev�ete v n�kter�ch grafick�ch editorech, o druh� pozd�ji. Pochop�te sami :-(</p>

<p>Funkci p�ed�v�me n�zev souboru a ukazatel na strukturu.</p>

<p class="src0">int ReadTextureData(char *filename, P_TEXTURE_IMAGE buffer)<span class="kom">// Na�te data obr�zku</span></p>
<p class="src0">{</p>

<p>Deklarujeme handle souboru, ��d�c� prom�nn� cykl� a prom�nnou done, kter� indikuje �sp�ch/ne�sp�ch operace volaj�c�mu k�du. Na za��tku j� p�i�ad�me nulu, proto�e obr�zek je�t� nen� nahran�. Prom�nnou stride, kter� ur�uje velikost ��dku, hned na za��tku inicializujeme na hodnotu z�skanou vyn�soben�m ���ky ��dku v pixelech s barevnou hloubkou. Pokud bude obr�zek �irok� 256 pixel� a barevn� hloubka 4 byty (32 bit�, RGBA), velikost ��dku bude celkem 1024 byt�. Pointer p ukazuje do pam�ti dat obr�zku.</p>

<p class="src1">FILE *f;<span class="kom">// Handle souboru</span></p>
<p class="src1">int i, j, k;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src1">int done = 0;<span class="kom">// Po�et na�ten�ch byt� ze souboru (n�vratov� hodnota)</span></p>
<p class="src"></p>
<p class="src1">int stride = buffer-&gt;width * buffer-&gt;format;<span class="kom">// Velikost ��dku</span></p>
<p class="src1">unsigned char *p = NULL;<span class="kom">// Ukazatel na aktu�ln� byte pam�ti</span></p>

<p>Otev�eme soubor pro �ten� v bin�rn�m m�du.</p>

<p class="src1">f = fopen(filename, &quot;rb&quot;);<span class="kom">// Otev�e soubor</span></p>
<p class="src"></p>
<p class="src1">if(f != NULL)<span class="kom">// Poda�ilo se ho otev��t?</span></p>
<p class="src1">{</p>

<p>Pokud soubor existuje a �el otev��t, za�neme se postupn� vno�ovat do cykl�. V�e by bylo velice jednoduch�, kdyby .RAW form�t byl trochu jinak uspo��d�n. ��dky vedou, jak je obvykl�, zleva doprava, ale jejich po�ad� je invertovan�. To znamen�, �e prvn� ��dek je posledn�, druh� p�edposledn� atd. Vn�j�� cyklus tedy nastav�me tak, aby ��d�c� prom�nn� ukazovala dol� na za��tek obr�zku. Soubor na��t�me od za��tku, ale hodnoty ukl�d�me od konce pam�ti vzh�ru. V�sledkem je p�evr�cen� obr�zku.</p>

<p class="src2">for(i = buffer-&gt;height-1; i &gt;= 0 ; i--)<span class="kom">// Od zdola nahoru po ��dc�ch</span></p>
<p class="src2">{</p>

<p>Nastav�me ukazatel, kam se pr�v� ukl�d�, na spr�vn� ��dek pam�ti. Jej�m za��tkem je samoz�ejm� buffer-&gt;data. Se�teme ho s um�st�n�m od za��tku i * velikost ��dku. P�edstavte si, �e buffer-&gt;data je str�nka v pam�ti a i * stride p�edstavuje offset. Je to �pln� stejn�. Offsetem se pohybujeme po p�id�len� str�nce. Na za��tku je maxim�ln� a postupn� kles�. V�sledkem je, �e v pam�ti postupujeme vzh�ru. Mysl�m, �e je to pochopiteln�.</p>

<p class="src3">p = buffer-&gt;data + (i * stride);<span class="kom">// P ukazuje na po�adovan� ��dek</span></p>

<p>Druh�m cyklem se pohybujeme zleva doprava po pixelech obr�zku (ne bytech!).</p>

<p class="src3">for (j = 0; j &lt; buffer-&gt;width; j++)<span class="kom">// Zleva doprava po pixelech</span></p>
<p class="src3">{</p>

<p>T�et� cyklus proch�z� jednotliv� byty v pixelu. Pokud barevn� hloubka (= byty na pixel) bude 4, cyklus projde celkem 3x (od 0 do 2; format-1). D�vodem ode�ten� jedni�ky je, �e v�t�ina .RAW obr�zk� neobsahuje alfa hodnotu, ale pouze RGB slo�ky. Alfu nastav�me ru�n�.</p>

<p>V�imn�te si tak�, �e ka�d�m pr�chodem inkrementujeme t�i prom�nn�: k, p a done. ��d�c� prom�nn� k je jasn�. P ukazovalo p�ed vstupem do v�ech cykl� na za��tek posledn�ho ��dku v pam�ti. Postupn� ho inkrementujeme a� dos�hne �pln�ho konce. Potom ho nastav�me na p�edposledn� ��dek atd. Done na konci funkce vr�t�me, ozna�uje celkov� po�et na�ten�ch byt�.</p>

<p class="src4">for (k = 0; k &lt; buffer-&gt;format-1; k++, p++, done++)<span class="kom">// Jednotliv� byty v pixelu</span></p>
<p class="src4">{</p>

<p>Funkce fgetc() na�te ze souboru f jeden znak a vr�t� ho. Tento znak m� velikost 1 byte (U� v�te pro� zrovna unsigned char?). Pova�ujeme ho za slo�ku barvy. Proto�e se cyklus po t�et�m pr�chodu zastav�, na�teme a ulo��me slo�ky R, G a B.</p>

<p class="src5">*p = fgetc(f);<span class="kom">// Na�te R, G a B slo�ku barvy</span></p>
<p class="src4">}</p>

<p>Po opu�t�n� cyklu p�i�ad�me alfu a op�t inkrementujeme ukazatel, aby se posunul na dal�� byte.</p>

<p>P�ekl.: Tady se hod� poznamenat, �e alfa nemus� b�t zrovna 255 (nepr�hledn�), ale m��eme ji nastavit na polovinu (122) a tak vytvo�it polopr�hlednou texturu. Nebo si ��ct, �e pixel o ur�it�ch slo�k�ch RGB bude pr�hledn�. V�t�inou se vezme �ern� nebo b�l� barva, ale nic nebr�n� nap�. na�ten� lev�ho horn�ho pixelu obr�zku a zpr�hledn�n� v�ech ostatn�ch pixel� se stejn�m RGB. Nebo postupn�, jak na��t�me jednotliv� pixely v ��dku, sni�ovat alfu od 255 do 0. Textura bude vlevo nepr�hledn� a vpravo pr�hledn� - plynul� p�echod. S pr�hlednost� se d�laj� hodn� kvalitn� efekty. Mali�k� upozorn�n� na konec: Efekty s alfa hodnotou jsou mo�n� nejen u .RAW textur. Nezapome�te, �e u� v 6. lekci !!! jsme m�li p��stup k dat�m textury. Funkci glTexImage2D() jsme na konci LoadGLTextures() p�ed�vali parametr data!</p>

<p class="src4">*p = 255;<span class="kom">// Alfa nepr�hledn� (ru�n� nastaven�)</span></p>
<p class="src4">p++;<span class="kom">// Ukazatel na dal�� byte</span></p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Pot�, co projdeme v�echny byty v pixelu, pixely v ��dku a ��dky v souboru se v�echny cykly ukon��. Uf, Kone�n�! :-) Po ukon�en� cykl� zav�eme soubor.</p>

<p class="src2">fclose(f);<span class="kom">// Zav�e soubor</span></p>
<p class="src1">}</p>

<p>Pokud byly probl�my s otev�en�m souboru (neexistuje ap.) zobraz�me chybovou zpr�vu.</p>

<p class="src1">else<span class="kom">// Soubor se nepoda�ilo otev��t</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Unable To Open Image File&quot;,&quot;IMAGE ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src1">}</p>

<p>Nakonec vr�t�me done. Pokud se soubor nepoda�ilo otev��t a my nic nena�etli, obsahuje nulu. Pokud bylo v�e v po��dku done se rovn� po�tu na�ten�ch byt�.</p>

<p class="src1">return done;<span class="kom">// Vr�t� po�et na�ten�ch byt�</span></p>
<p class="src0">}</p>

<p>M�me loadovan� data obr�zku, tak�e vytvo��me texturu. Funkci p�ed�v�me ukazatel na obr�zek. Vygenerujeme texturu, nastav�me ji jako aktu�ln�, zvol�me line�rn� filtrov�n� pro zv�t�en� i zmen�en� a nakonec vytvo��me mipmapovanou texturu. V�e je �pln� stejn� jako s knihovnou glaux, ale s t�m rozd�lem, �e jsme si obr�zek tentokr�t nahr�li sami.</p>

<p class="src0">void BuildTexture(P_TEXTURE_IMAGE tex)<span class="kom">// Vytvo�� texturu</span></p>
<p class="src0">{</p>
<p class="src1">glGenTextures(1, &amp;texture[0]);<span class="kom">// Generuje texturu</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Vybere texturu za aktu�ln�</span></p>
<p class="src"></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);<span class="kom">// Line�rn� filtrov�n�</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Mipmapovan� textura</span></p>
<p class="src1">gluBuild2DMipmaps(GL_TEXTURE_2D, GL_RGB, tex-&gt;width, tex-&gt;height, GL_RGBA, GL_UNSIGNED_BYTE, tex-&gt;data);</p>
<p class="src0">}</p>

<p>Funkci Blit(), kter� implementuje blitting, je p�ed�v�na spousta parametr�. Co vyjad�uj�? Vezmeme to hezky popo�ad�. Src je zdrojov�m obr�zkem, jeho� data vkl�d�me do c�lov�ho obr�zku dst. Ostatn� parametry vyzna�uj�, kter� data se zkop�ruj� (obd�ln�k ur�en� �ty�mi src_* ��sly), kam se maj� do c�lov�ho obr�zku um�stit (dst_*) a jak�m zp�sobem (blending, pop�. alfa hodnota).</p>

<p class="src0"><span class="kom">// Blitting obr�zk�</span></p>
<p class="src0">void Blit(P_TEXTURE_IMAGE src,<span class="kom">// Zdrojov� obr�zek</span></p>
<p class="src1">P_TEXTURE_IMAGE dst,<span class="kom">// C�lov� obr�zek</span></p>
<p class="src1">int src_xstart,<span class="kom">// Lev� horn� bod kop�rovan� oblasti</span></p>
<p class="src1">int src_ystart,<span class="kom">// Lev� horn� bod kop�rovan� oblasti</span></p>
<p class="src1">int src_width,<span class="kom">// ���ka kop�rovan� oblasti</span></p>
<p class="src1">int src_height,<span class="kom">// V��ka kop�rovan� oblasti</span></p>
<p class="src1">int dst_xstart,<span class="kom">// Kam kop�rovat (lev� horn� bod)</span></p>
<p class="src1">int dst_ystart,<span class="kom">// Kam kop�rovat (lev� horn� bod)</span></p>
<p class="src1">int blend,<span class="kom">// Pou��t blending?</span></p>
<p class="src1">int alpha)<span class="kom">// Hodnota alfy p�i blendingu</span></p>
<p class="src0">{</p>

<p>Po ��d�c�ch prom�nn�ch cykl� deklarujeme pomocn� prom�nn� s a d, kter� ukazuj� do pam�ti obr�zk�. D�le o�et��me p�ed�van� parametry tak, aby alfa hodnota byla v rozmez� 0 a� 255 a blend 0 nebo 1.</p>

<p class="src1">int i, j, k;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src1">unsigned char *s, *d;<span class="kom">// Pomocn� ukazatele na data zdroje a c�le</span></p>
<p class="src"></p>
<p class="src1">if(alpha &gt; 255)<span class="kom">// Je alfa mimo rozsah?</span></p>
<p class="src2">alpha = 255;</p>
<p class="src1">if(alpha &lt; 0)</p>
<p class="src2">alpha = 0;</p>
<p class="src"></p>
<p class="src1">if(blend &lt; 0)<span class="kom">// Je blending mimo rozsah?</span></p>
<p class="src2">blend = 0;</p>
<p class="src1">if(blend &gt; 1)</p>
<p class="src2">blend = 1;</p>

<p>P�ekl.: Cel� kop�rov�n� rad�ji vysv�tl�m na p��kladu, bude sn�ze pochopiteln�. M�me obr�zek 256 pixel� �irok� a chceme zkop�rovat nap�. oblast od 50. do 200. pixelu o ur�it� v��ce. P�ed vstupem do cyklu se p�esuneme na prvn� kop�rovan� ��dek. Potom sko��me na 50. pixel zleva, zkop�rujeme 150 pixel� a sko��me na konec ��dku p�es zb�vaj�c�ch 56 pixel�. V�e opakujeme pro dal�� ��dek, dokud nezkop�rujeme cel� po�adovan� obd�ln�k dat zdrojov�ho obr�zku do c�lov�ho.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_29_priklad.gif" width="128" height="128" alt="P��klad" /></div>

<p>Nyn� nastav�me ukazatele d a s. C�lov� ukazatel z�sk�me se�ten�m adresy, kde za��naj� data c�lov�ho obr�zku s offsetem, kter� je v�sledkem n�soben� y pozice, kam za�neme kop�rovat, ���kou obr�zku v pixelech a barevnou hloubkou obr�zku. T�mto z�sk�me ��dek, na kter�m za��n�me kop�rovat. Zdrojov� ukazatel ur��me analogicky.</p>

<p class="src1"><span class="kom">// Ukazatele na prvn� kop�rovan� ��dek</span></p>
<p class="src1">d = dst-&gt;data + (dst_ystart * dst-&gt;width * dst-&gt;format);</p>
<p class="src1">s = src-&gt;data + (src_ystart * src-&gt;width * src-&gt;format);</p>

<p>Vn�j�� cyklus proch�z� kop�rovan� ��dky od shora dol�.</p>

<p class="src1">for (i = 0; i &lt; src_height; i++)<span class="kom">// ��dky, ve kter�ch se kop�ruj� data</span></p>
<p class="src1">{</p>

<p>U� m�me ukazatel nastaven na spr�vn� ��dek, ale je�t� mus�me p�i��st x-ovou pozici, kter� se op�t n�sob� barevnou hloubkou. Akci provedeme pro zdrojov� i c�lov� ukazatel.</p>

<p class="src2"><span class="kom">// Posun na prvn� kop�rovan� pixel v ��dku</span></p>
<p class="src2">s = s + (src_xstart * src-&gt;format);</p>
<p class="src2">d = d + (dst_xstart * dst-&gt;format);</p>

<p>Pointery nyn� ukazuj� na prvn� kop�rovan� pixel. Za�neme cyklus, kter� v ��dku proch�z� jednotliv� pixely.</p>

<p class="src2">for (j = 0; j &lt; src_width; j++)<span class="kom">// Pixely v ��dku, kter� se maj� kop�rovat</span></p>
<p class="src2">{</p>

<p>Nejvnit�n�j�� cyklus proch�z� jednotliv� byty v pixelu. V�imn�te si, �e se tak� inkrementuj� pozice ve zdrojov�m i c�lov�m obr�zku.</p>

<p class="src3">for(k = 0; k &lt; src-&gt;format; k++, d++, s++)<span class="kom">// Byty v kop�rovan�m pixelu</span></p>
<p class="src3">{</p>

<p>P�ich�z� nejzaj�mav�j�� ��st - vytvo�en� alfablendingu. P�edstavte si, �e m�te dva pixely: �erven� (zdroj) a zelen� (c�l). Oba le�� na stejn�ch sou�adnic�ch. Pokud je nezpr�hledn�te, p�jde vid�t pouze jeden z nich, proto�e p�vodn� pixel bude nahrazen nov�m. Jak jist� v�te, ka�d� pixel se skl�d� ze t�� barevn�ch kan�l� RGB. Chceme-li vytvo�it alfa blending, mus�me nejd��ve spo��tat opa�nou hodnotu alfa kan�lu a to tak, �e ode�teme tuto hodnotu od maxima (255 - alpha). N�sob�me j� c�lov� (zelen�) pixel a se�teme ho se zdrojov�m (�erven�m), kter� jsme n�sobili neupravenou alfou. Jsme skoro hotovi. Kone�nou barvu vypo��t�me d�len�m v�sledku maxim�ln� hodnotou pr�hlednosti (255). tuto operaci z d�vodu v�t�� rychlosti vykon�v� bitov� posun doprava o osm bit�. A je to! M�me pixel slo�en� z obou p�edch�zej�c�ch pixel�. V�imn�te si, �e se v�po�ty postupn� prov�d�j� se v�emi kan�ly RGBA. V�te, co jsme pr�v� implementovali? OpenGL techniku glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA).</p>

<p class="src4">if (blend)<span class="kom">// Je po�adov�n blending?</span></p>
<p class="src4">{</p>
<p class="src5">*d = ((*s * alpha) + (*d * (255-alpha))) &gt;&gt; 8;<span class="kom">// Slou�en� dvou pixel� do jednoho</span></p>
<p class="src4">}</p>

<p>Pokud nebudeme cht�t blending, jednodu�e zkop�rujeme data ze zdrojov� bitmapy do c�lov�. ��dn� matematika, alfa se ignoruje.</p>

<p class="src4">else<span class="kom">// Bez blendingu</span></p>
<p class="src4">{</p>
<p class="src5">*d = *s;<span class="kom">// Oby�ejn� kop�rov�n�</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Dojdeme-li a� na konec kop�rovan� oblasti, zv�t��me ukazatel tak, aby se dostal na konec ��dku. Pokud dob�e rozum�me ukazatel�m a pam�ov�m operac�m, je blitting hra�kou.</p>

<p class="src2"><span class="kom">// Sko�� ukazatelem na konec ��dku</span></p>
<p class="src2">d = d + (dst-&gt;width - (src_width + dst_xstart)) * dst-&gt;format;</p>
<p class="src2">s = s + (src-&gt;width - (src_width + src_xstart)) * src-&gt;format;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Inicializace je tentokr�t zm�n�na od z�klad�. Alokujeme pam� pro dva obr�zky velik� 256 pixel�, kter� maj� barevnou hloubku 4 byty (RGBA). Pot� se je pokus�me nahr�t. Pokud n�co nevyjde vyp�eme chybovou zpr�vu a ukon��me program.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">t1 = AllocateTextureBuffer(256, 256, 4);<span class="kom">// Alokace pam�ti pro prvn� obr�zek</span></p>
<p class="src"></p>
<p class="src1">if (ReadTextureData(&quot;Data/Monitor.raw&quot;, t1) == 0)<span class="kom">// Nahraje data obr�zku</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nic se nenahr�lo</span></p>
<p class="src2">MessageBox(NULL, &quot;Could Not Read 'Monitor.raw' Image Data&quot;, &quot;TEXTURE ERROR&quot;, MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">t2 = AllocateTextureBuffer(256, 256, 4);<span class="kom">// Alokace pam�ti pro druh� obr�zek</span></p>
<p class="src"></p>
<p class="src1">if (ReadTextureData(&quot;Data/GL.raw&quot;, t2) == 0)<span class="kom">// Nahraje data obr�zku</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nic se nenahr�lo</span></p>
<p class="src2">MessageBox(NULL, &quot;Could Not Read 'GL.raw' Image Data&quot;, &quot;TEXTURE ERROR&quot;, MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>

<p>Pokud jsme se dostali a� tak daleko, je bezpe�n� p�edpokl�dat, �e m��eme pracovat s daty obr�zk�, kter� se pokus�me blittingem slou�it do jednoho. P�ed�me je funkci - obr�zek t2 jako zdrojov�, t1 jako c�lov�. V�sledn� obr�zek z�skan� slou�en�m se ulo�� do t1. Vytvo��me z n�j texturu.</p>

<p class="src1"><span class="kom">// Blitting obr�zk�</span></p>
<p class="src1">Blit(t2,<span class="kom">// Zdrojov� obr�zek</span></p>
<p class="src2">t1,<span class="kom">// C�lov� obr�zek</span></p>
<p class="src2">127,<span class="kom">// Lev� horn� bod kop�rovan� oblasti</span></p>
<p class="src2">127,<span class="kom">// Lev� horn� bod kop�rovan� oblasti</span></p>
<p class="src2">128,<span class="kom">// ���ka kop�rovan� oblasti</span></p>
<p class="src2">128,<span class="kom">// V��ka kop�rovan� oblasti</span></p>
<p class="src2">64,<span class="kom">// Kam kop�rovat (lev� horn� bod)</span></p>
<p class="src2">64,<span class="kom">// Kam kop�rovat (lev� horn� bod)</span></p>
<p class="src2">1,<span class="kom">// Pou��t blending?</span></p>
<p class="src2">128)<span class="kom">// Hodnota alfy p�i blendingu</span></p>
<p class="src"></p>
<p class="src1">BuildTexture(t1);<span class="kom">// Vytvo�� texturu</span></p>

<p>P�ekl.: P�vodn� jsem cht�l vlo�it obr�zky, abyste v�d�li, jak vypadaj�, ale bohu�el ani jeden grafick� editor, kter� m�m zrovna doma .RAW form�t nepodporuje. V anglick�m tutori�lu je zm�n�no, �e Adobe Photoshop to svede. Ale poradil jsem si... v�te jak? OpenGL.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_29_noblit1.gif" width="170" height="185" alt="Obr�zek t2" />
<img src="images/nehe_tut/tut_29_noblit2.gif" width="170" height="185" alt="Obr�zek t1" />
<img src="images/nehe_tut/tut_29_blit.gif" width="170" height="185" alt="Obr�zek t1 po blittingu" />
</div>

<p>Potom, co je vytvo�ena textura, m��eme uvolnit pam� obou obr�zk�.</p>

<p class="src1">DeallocateTexture(t1);<span class="kom">// Uvoln� pam� obr�zk�</span></p>
<p class="src1">DeallocateTexture(t2);</p>

<p>N�sleduj� b�n� nastaven� OpenGL.</p>

<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturov�n�</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src"></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Povol� maz�n� depth bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom">// Typ testov�n� hloubky</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>DrawGLScene() renderuje oby�ejnou krychli - to u� ur�it� zn�te.</p>

<p class="src0">GLvoid DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-10.0f);<span class="kom">// P�esun do hloubky</span></p>
<p class="src"></p>
<p class="src1">glRotatef(xrot, 1.0f,0.0f,0.0f);<span class="kom">// Rotace</span></p>
<p class="src1">glRotatef(yrot, 0.0f,1.0f,0.0f);</p>
<p class="src1">glRotatef(zrot, 0.0f,0.0f,1.0f);</p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Zvol� texturu</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src2"><span class="kom">// �eln� st�na</span></p>
<p class="src2">glNormal3f(0.0f, 0.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 1.0f);</p>
<p class="src2"><span class="kom">// Zadn� st�na</span></p>
<p class="src2">glNormal3f(0.0f, 0.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f,-1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f,-1.0f,-1.0f);</p>
<p class="src2"><span class="kom">// Horn� st�na</span></p>
<p class="src2">glNormal3f(0.0f, 1.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, 1.0f, 1.0f);</p>
<p class="src2"><span class="kom">// Doln� st�na</span></p>
<p class="src2">glNormal3f(0.0f,-1.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,-1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,-1.0f,-1.0f);</p>
<p class="src2"><span class="kom">// Prav� st�na</span></p>
<p class="src2">glNormal3f(1.0f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,-1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 1.0f);</p>
<p class="src2"><span class="kom">// Lev� st�na</span></p>
<p class="src2">glNormal3f(-1.0f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,-1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f,-1.0f);</p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src"></p>
<p class="src1">xrot += 0.3f;<span class="kom">// Zv�t�� �hly rotace</span></p>
<p class="src1">yrot += 0.2f;</p>
<p class="src1">zrot += 0.4f;</p>
<p class="src0">}</p>

<p>Mali�ko uprav�me k�d WinMain(). Pokud nen� program aktivn� (nap�. minimalizovan�), zavol�me WaitMessage(). V�echno se zastav�, dokud program neobdr�� n�jakou zpr�vu (oby�ejn� o maximalizaci okna). Ve v�sledku dos�hneme toho, �e pokud program nen� aktivn� nebude v�bec zat�ovat procesor.</p>

<p class="src0"><span class="kom">// Funkce WinMain() - v hlavn� smy�ce programu</span></p>
<p class="src2">if (!active)<span class="kom">// Je program neaktivn�?</span></p>
<p class="src2">{</p>
<p class="src3">WaitMessage();<span class="kom">// �ekej na zpr�vu a zat�m nic ned�lej</span></p>
<p class="src2">}</p>

<p>Tak�e to bychom m�li. Nyn� m�te ve sv�ch hr�ch, enginech, demech nebo jak�chkoli programech v�echny dve�e otev�en� pro vytv��en� velmi efektn�ch blending efekt�. S texturov�mi buffery m��ete vytv��et v�ci jako nap��klad real-time plazmu nebo vodu. Vz�jemnou kombinac� v�ce obr�zk� (i n�kolikr�t za sebou) je mo�n� dos�hnout t�m�� fotorealistick�ho ter�nu. Hodn� �t�st�.</p>

<p class="autor">napsal: Andreas L�ffler &amp; Rob Fletcher<br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?> &amp; V�clav Slov��ek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul>
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson29.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson29_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson29.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson29.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson29.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson29.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson29.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:rodolphe.suescun@wanadoo.fr">Rodolphe Suescun</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson29.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson29.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(29);?>
<?FceNeHeOkolniLekce(29);?>

<?
include 'p_end.php';
?>
