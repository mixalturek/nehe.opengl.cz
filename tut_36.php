<?
$g_title = 'CZ NeHe OpenGL - Lekce 36 - Radial Blur, renderov�n� do textury';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(36);?>

<h1>Lekce 36 - Radial Blur, renderov�n� do textury</h1>

<p class="nadpis_clanku">Spole�n�mi silami vytvo��me extr�mn� p�sobiv� efekt radial blur, kter� nevy�aduje ��dn� OpenGL roz���en� a funguje na jak�mkoli hardwaru. Nau��te se tak�, jak lze na pozad� aplikace vyrenderovat sc�nu do textury, aby pozorovatel nic nevid�l.</p>

<p>Ahoj, jmenuji se Dario Corno, ale jsem tak� z n�m jako rIo ze Spinning Kids. Prvn� ze v�eho vysv�tl�m, pro� jsem se rozhodl napsat tento tutori�l. Roku 1989 jsem se stal &quot;sc�na�em&quot;. Cht�l bych po v�s, abyste si st�hli n�jak� dema. Pochop�te, co to demo je a v �em spo��vaj� demo efekty.</p>

<p>Dema vytv��ej� opravdov� kode�i na uk�zku hardcore a �asto i brut�ln�ch k�dovac�ch technik. Sv�m zp�sobem jsou druhem um�n�, ve kter�m se spojuje v�e od hudby (hudba na pozad�, zvuky) a mal��stv� (grafika, design, modely) p�es matematiku a fyziku (v�e funguje na n�jak�ch principech) a� po programov�n� a detailn� znalost po��ta�e na �rovni hardwaru. Obrovsk� kolekce dem m��ete naj�t na <?OdkazBlank('http://www.pouet.net/');?> a <?OdkazBlank('http://ftp.scene.org/');?>, v �ech�ch pak <?OdkazBlank('http://www.scene.cz/');?>. Ale abyste se hned na za��tku nevylekali... toto nen� prav� smrt�c� tutori�l, i kdy� mus�m uznat, �e v�sledek stoj� za to.</p>

<p>P�ekl.: Se sv�m prvn�m demem jsem se setkal ve druh�ku na st�edn�, kdy n�m spolubydl�c� na intru Luk� Duzsty Hoger ukazoval na 486 notebooku jeden progr�mek, kter� zab�ral kolem 2 kB. Na za��tku byla vid�t ruka, jak kresl� na pl�tno d�m, strom a postavy, sc�na se vyboulila do 3D a mus�m ��ct, �e na 256 barev a DOSovou grafiku v�e vypadalo �chvatn� - kam se program�to�i vyu��vaj�c� pohodln�ch slu�eb OpenGL v�bec hrabou :-). Proti tomu koderovi fakt batolata. Asi nejlep�� demo, kter� jsem kdy vid�l byla 64 kB animace &quot;re�ln�ho&quot; 3D prost�ed� ve video kvalit�, kter� trvala n�co p�es �tvrt hodiny. Jenom texty v kreditu na konci musely zab�rat polovinu m�sta. Zkuste si pro zaj�mavost zkompilovat pr�zdnou MFC aplikaci vygenerovanou APP Wizzardem, kter� nav�c tah� v�t�inu pot�ebn�ch funkc� z DLL knihoven - nedostanete se pod 30 kB.</p>

<p>Tolik tedy k �vodu... Co se ale dozv�te v tomto tutori�lu? Vysv�tl�m v�m, jak vytvo�it perfektn� efekt (pou��van� v demech), kter� vypad� jako radial blur (radi�ln� rozmaz�n�). N�kdy je tak� ozna�ov�n jako volumetrick� sv�tla, ale nev��te, je to pouze oby�ejn� radial blur.</p>

<p>Radial blur b�v� oby�ejn� vytv��en (pouze p�i softwarov�m renderingu) rozmaz�v�n�m pixel� origin�ln�ho obr�zku v opa�n�m sm�ru ne� se nach�z� st�ed rozmaz�v�n�. S dne�n�m hardwarem je docela obt�n� prov�d�t ru�n� blurring (rozmaz�v�n�) za pou�it� color bufferu (alespo� v p��pad�, �e je podporov�n v�emi grafick�mi kartami), tak�e pot�ebujeme vyu��t mal�ho triku, abychom dos�hli alespo� podobn�ho efektu. Jako bonus se tak� dozv�te, jak je snadn� renderovat do textury.</p>

<p>Objekt, kter� jsem se pro tento tutori�l rozhodl pou��t, je spir�la, proto�e vypad� hodn� dob�e. Nav�c jsem u� celkem unaven� z krychli�ek :-] Mus�m je�t� poznamenat, �e vysv�tluji hlavn� vytv��en� v�sledn�ho efektu, naopak pomocn� k�d u� m�n� detailn�ji. M�li byste ho m�t u� d�vno za�it�.</p>

<p class="src0"><span class="kom">// U�ivatelsk� prom�nn�</span></p>
<p class="src0">float angle;<span class="kom">// �hel rotace spir�ly</span></p>
<p class="src0">float vertexes[4][3];<span class="kom">// �ty�i body o t�ech sou�adnic�ch</span></p>
<p class="src0">float normal[3];<span class="kom">// Data norm�lov�ho vektoru</span></p>
<p class="src0">GLuint BlurTexture;<span class="kom">// Textura</span></p>

<p>Tak tedy za�neme... Funkce EmptyTexture() generuje pr�zdnou texturu a vrac� ��slo jej�ho identifik�toru. Na za��tku alokujeme pam� obr�zku o velikosti 128*128*4. Tato ��sla ozna�uj� ���ku, v��ku a barevnou hloubku (RGBA) obr�zku. Po alokaci pam� vynulujeme. Proto�e budeme texturu roztahovat, pou�ijeme pro ni line�rn� filtrov�n�, GL_NEAREST v na�em p��pad� nevypad� zrovna nejl�pe.</p>

<p class="src0">GLuint EmptyTexture()<span class="kom">// Vytvo�� pr�zdnou texturu</span></p>
<p class="src0">{</p>
<p class="src1">GLuint txtnumber;<span class="kom">// ID textury</span></p>
<p class="src1">unsigned int* data;<span class="kom">// Ukazatel na data obr�zku</span></p>
<p class="src"></p>
<p class="src1">data = (unsigned int*) new GLuint[((128 * 128) * 4 * sizeof(unsigned int))];<span class="kom">// Alokace pam�ti</span></p>
<p class="src1">ZeroMemory(data,((128 * 128)* 4 * sizeof(unsigned int)));<span class="kom">// Nulov�n� pam�ti</span></p>
<p class="src"></p>
<p class="src1">glGenTextures(1, &amp;txtnumber);<span class="kom">// Jedna textura</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, txtnumber);<span class="kom">// Zvol� texturu</span></p>
<p class="src1">glTexImage2D(GL_TEXTURE_2D, 0, 4, 128, 128, 0, GL_RGBA, GL_UNSIGNED_BYTE, data);<span class="kom">// Vytvo�en� textury</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Line�rn� filtrov�n� pro zmen�en� i zv�t�en�</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);</p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);</p>
<p class="src"></p>
<p class="src1">delete [] data;<span class="kom">// Uvoln�n� pam�ti</span></p>
<p class="src"></p>
<p class="src1">return txtnumber;<span class="kom">// Vr�t� ID textury</span></p>
<p class="src0">}</p>

<p>N�sleduj�c� funkce normalizuje vektor, kter� je p�ed�n v parametru jako pole t�� float�. Spo��t�me jeho d�lku a s jej� pomoc� vyd�l�me v�echny t�i slo�ky.</p>

<p class="src0">void ReduceToUnit(float vector[3])<span class="kom">// V�po�et normalizovan�ho vektoru (jednotkov� d�lka)</span></p>
<p class="src0">{</p>
<p class="src1">float length;<span class="kom">// D�lka vektoru</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�po�et sou�asn� d�lky vektoru</span></p>
<p class="src1">length = (float)sqrt((vector[0]*vector[0]) + (vector[1]*vector[1]) + (vector[2]*vector[2]));</p>
<p class="src"></p>
<p class="src1">if(length == 0.0f)<span class="kom">// Prevence d�len� nulou</span></p>
<p class="src1">{</p>
<p class="src2">length = 1.0f;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">vector[0] /= length;<span class="kom">// Vyd�len� jednotliv�ch slo�ek d�lkou</span></p>
<p class="src1">vector[1] /= length;</p>
<p class="src1">vector[2] /= length;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�sledn� vektor je p�ed�n zp�t v parametru funkce</span></p>
<p class="src0">}</p>

<p>Pomoc� funkce calcNormal() lze vypo��tat vektor, kter� je kolm� ke t�em bod�m tvo��c�m rovinu. Dostali jsme dva parametry: v[3][3] p�edstavuje t�i body (o t�ech slo�k�ch x,y,z) a do out[3] ulo��me v�sledek. Na za��tku deklarujeme dva pomocn� vektory a t�i konstanty, kter� vystupuj� jako indexy do pole.</p>

<p class="src0">void calcNormal(float v[3][3], float out[3])<span class="kom">// V�po�et norm�lov�ho vektoru polygonu</span></p>
<p class="src0">{</p>
<p class="src1">float v1[3], v2[3];<span class="kom">// Vektor 1 a vektor 2 (x,y,z)</span></p>
<p class="src"></p>
<p class="src1">static const int x = 0;<span class="kom">// Pomocn� indexy do pole</span></p>
<p class="src1">static const int y = 1;</p>
<p class="src1">static const int z = 2;</p>

<p>Ze t�ech bod� p�edan�ch funkci vytvo��me dva vektory a spo��t�me t�et� vektor, kter� je k nim kolm�.</p>

<p class="src1">v1[x] = v[0][x] - v[1][x];<span class="kom">// V�po�et vektoru z 1. bodu do 0. bodu</span></p>
<p class="src1">v1[y] = v[0][y] - v[1][y];</p>
<p class="src1">v1[z] = v[0][z] - v[1][z];</p>
<p class="src"></p>
<p class="src1">v2[x] = v[1][x] - v[2][x];<span class="kom">// V�po�et vektoru z 2. bodu do 1. bodu</span></p>
<p class="src1">v2[y] = v[1][y] - v[2][y];</p>
<p class="src1">v2[z] = v[1][z] - v[2][z];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�sledkem vektorov�ho sou�inu dvou vektor� je t�et� vektor, kter� je k nim kolm�</span></p>
<p class="src1">out[x] = v1[y]*v2[z] - v1[z]*v2[y];</p>
<p class="src1">out[y] = v1[z]*v2[x] - v1[x]*v2[z];</p>
<p class="src1">out[z] = v1[x]*v2[y] - v1[y]*v2[x];</p>

<p>Aby v�e bylo dokonal�, tak v�sledn� vektor normalizujeme na jednotkovou d�lku.</p>

<p class="src1">ReduceToUnit(out);<span class="kom">// Normalizace v�sledn�ho vektoru</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// V�sledn� vektor je p�ed�n zp�t v parametru funkce</span></p>
<p class="src0">}</p>

<p>N�sleduj�c� rutina vykresluje spir�lu. Po deklaraci prom�nn�ch nastav�me pomoc� gluLookAt() v�hled do sc�ny. D�v�me se z bodu 0, 5, 50 do bodu 0, 0, 0. UP vektor m��� vzh�ru ve sm�ru osy y.</p>

<p class="src0">void ProcessHelix()<span class="kom">// Vykresl� spir�lu</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat x;<span class="kom">// Sou�adnice x, y, z</span></p>
<p class="src1">GLfloat y;</p>
<p class="src1">GLfloat z;</p>
<p class="src"></p>
<p class="src1">GLfloat phi;<span class="kom">// �hly</span></p>
<p class="src1">GLfloat theta;</p>
<p class="src1">GLfloat u;</p>
<p class="src1">GLfloat v;</p>
<p class="src"></p>
<p class="src1">GLfloat r;<span class="kom">// Polom�r z�vitu</span></p>
<p class="src1">int twists = 5;<span class="kom">// P�t z�vit�</span></p>
<p class="src"></p>
<p class="src1">GLfloat glfMaterialColor[] = { 0.4f, 0.2f, 0.8f, 1.0f};<span class="kom">// Barva materi�lu</span></p>
<p class="src1">GLfloat specular[] = { 1.0f, 1.0f, 1.0f, 1.0f};<span class="kom">// Specular sv�tlo</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">gluLookAt(0,5,50, 0,0,0, 0,1,0);<span class="kom">// Pozice o�� (0,5,50), st�ed sc�ny (0,0,0), UP vektor na ose y</span></p>

<p>Ulo��me matici a p�esuneme se o pades�t jednotek do sc�ny. V z�vislosti na �hlu angle (glob�ln� prom�nn�) se spir�lou rotujeme. Tak� nastav�me materi�ly.</p>

<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0, 0, -50);<span class="kom">// Pades�t jednotek do sc�ny</span></p>
<p class="src1">glRotatef(angle/2.0f, 1, 0, 0);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(angle/3.0f, 0, 1, 0);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastaven� materi�l�</span></p>
<p class="src1">glMaterialfv(GL_FRONT_AND_BACK, GL_AMBIENT_AND_DIFFUSE, glfMaterialColor);</p>
<p class="src1">glMaterialfv(GL_FRONT_AND_BACK, GL_SPECULAR,specular);</p>

<p>Pokud ovl�d�te goniometrick� funkce, je v�po�et jednotliv�ch bod� spir�ly relativn� jednoduch�, ale nebudu to zde vysv�tlovat (P�ekl.: d�ky bohu... :-), proto�e spir�la nen� hlavn� n�pln� tohoto tutori�lu. Nav�c jsem si k�d p�j�il od kamar�d� z Listen Software. P�jdeme jednodu���, ale ne nejrychlej�� cestou. S vertex arrays by bylo v�e mnohem rychlej��.</p>

<p class="src1">r = 1.5f;<span class="kom">// Polom�r</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�k�</span></p>
<p class="src2">for(phi = 0; phi &lt;= 360; phi += 20.0)<span class="kom">// 360 stup�� v kroku po 20 stupn�ch</span></p>
<p class="src2">{</p>
<p class="src3">for(theta = 0; theta &lt;= 360*twists; theta += 20.0)<span class="kom">// 360 stup��* po�et z�vit� po 20 stupn�ch</span></p>
<p class="src3">{</p>
<p class="src4">v = (phi / 180.0f * 3.142f);<span class="kom">// �hel prvn�ho bodu (0)</span></p>
<p class="src4">u = (theta / 180.0f * 3.142f);<span class="kom">// �hel prvn�ho bodu (0)</span></p>
<p class="src"></p>
<p class="src4">x = float(cos(u) * (2.0f + cos(v))) * r;<span class="kom">// Pozice x, y, z prvn�ho bodu</span></p>
<p class="src4">y = float(sin(u) * (2.0f + cos(v))) * r;</p>
<p class="src4">z = float(((u - (2.0f * 3.142f)) + sin(v)) * r);</p>
<p class="src"></p>
<p class="src4">vertexes[0][0] = x;<span class="kom">// Kop�rov�n� prvn�ho bodu do pole</span></p>
<p class="src4">vertexes[0][1] = y;</p>
<p class="src4">vertexes[0][2] = z;</p>
<p class="src"></p>
<p class="src4">v = (phi / 180.0f * 3.142f);<span class="kom">// �hel druh�ho bodu (0)</span></p>
<p class="src4">u = ((theta + 20) / 180.0f * 3.142f);<span class="kom">// �hel druh�ho bodu (20)</span></p>
<p class="src"></p>
<p class="src4">x = float(cos(u) * (2.0f + cos(v))) * r;<span class="kom">// Pozice x, y, z druh�ho bodu</span></p>
<p class="src4">y = float(sin(u) * (2.0f + cos(v))) * r;</p>
<p class="src4">z = float(((u - (2.0f * 3.142f)) + sin(v)) * r);</p>
<p class="src"></p>
<p class="src4">vertexes[1][0] = x;<span class="kom">// Kop�rov�n� druh�ho bodu do pole</span></p>
<p class="src4">vertexes[1][1] = y;</p>
<p class="src4">vertexes[1][2] = z;</p>
<p class="src"></p>
<p class="src4">v=((phi + 20) / 180.0f * 3.142f);<span class="kom">// �hel t�et�ho bodu (20)</span></p>
<p class="src4">u=((theta + 20) / 180.0f * 3.142f);<span class="kom">// �hel t�et�ho bodu (20)</span></p>
<p class="src"></p>
<p class="src4">x = float(cos(u) * (2.0f + cos(v))) * r;<span class="kom">// Pozice x, y, z t�et�ho bodu</span></p>
<p class="src4">y = float(sin(u) * (2.0f + cos(v))) * r;</p>
<p class="src4">z = float(((u - (2.0f * 3.142f)) + sin(v)) * r);</p>
<p class="src"></p>
<p class="src4">vertexes[2][0] = x;<span class="kom">// Kop�rov�n� t�et�ho bodu do pole</span></p>
<p class="src4">vertexes[2][1] = y;</p>
<p class="src4">vertexes[2][2] = z;</p>
<p class="src"></p>
<p class="src4">v = ((phi + 20) / 180.0f * 3.142f);<span class="kom">// �hel �tvrt�ho bodu (20)</span></p>
<p class="src4">u = ((theta) / 180.0f * 3.142f);<span class="kom">// �hel �tvrt�ho bodu (0)</span></p>
<p class="src"></p>
<p class="src4">x = float(cos(u) * (2.0f + cos(v))) * r;<span class="kom">// Pozice x, y, z �tvrt�ho bodu</span></p>
<p class="src4">y = float(sin(u) * (2.0f + cos(v))) * r;</p>
<p class="src4">z = float(((u - (2.0f * 3.142f)) + sin(v)) * r);</p>
<p class="src"></p>
<p class="src4">vertexes[3][0] = x;<span class="kom">// Kop�rov�n� �tvrt�ho bodu do pole</span></p>
<p class="src4">vertexes[3][1] = y;</p>
<p class="src4">vertexes[3][2] = z;</p>
<p class="src"></p>
<p class="src4">calcNormal(vertexes, normal);<span class="kom">// V�po�et norm�ly obd�ln�ku</span></p>
<p class="src"></p>
<p class="src4">glNormal3f(normal[0], normal[1], normal[2]);<span class="kom">// Posl�n� norm�ly OpenGL</span></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Rendering obd�ln�ku</span></p>
<p class="src4">glVertex3f(vertexes[0][0], vertexes[0][1], vertexes[0][2]);</p>
<p class="src4">glVertex3f(vertexes[1][0], vertexes[1][1], vertexes[1][2]);</p>
<p class="src4">glVertex3f(vertexes[2][0], vertexes[2][1], vertexes[2][2]);</p>
<p class="src4">glVertex3f(vertexes[3][0], vertexes[3][1], vertexes[3][2]);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src"></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� matice</span></p>
<p class="src0">}</p>

<p>Funkce ViewOrtho() slou�� k p�epnut� z perspektivn� projekce do pravo�hl� a ViewPerspective() k n�vratu zp�t. V�e u� bylo pops�no nap��klad v tutori�lech o fontech, ale i jinde, tak�e to zde nebudu znovu prob�rat.</p>

<p class="src0">void ViewOrtho()<span class="kom">// Nastavuje pravo�hlou projekci</span></p>
<p class="src0">{</p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Projek�n� matice</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glOrtho(0, 640 , 480 , 0, -1, 1);<span class="kom">// Nastaven� pravo�hl� projekce</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Modelview matice</span></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">void ViewPerspective()<span class="kom">// Obnoven� perspektivn�ho m�du</span></p>
<p class="src0">{</p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Projek�n� matice</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� matice</span></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Modelview matice</span></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� matice</span></p>
<p class="src0">}</p>

<p>Poj�me si vysv�tlit, jak pracuje na�e imitace efektu radial blur. Pot�ebujeme vykreslit sc�nu tak, aby se jevila jakoby rozmazan� od st�edu do v�ech sm�r�. Nem��eme ��st ani zapisovat pixely a pokud chceme zachovat kompatibilitu s r�zn�m grafick�mi kartami, nem�li bychom pou��vat ani OpenGL roz���en� ani jin� p��kazy specifick� pro ur�it� hardware. �e�en� je docela snadn�, OpenGL n�m d�v� mo�nost blurnout (rozmazat) textury. OK... ne opravdov� blurring. Pokud za pou�it� line�rn�ho filtrov�n� rozt�hneme textury, v�sledek bude, s trochou p�edstavivosti, vypadat podobn� jako gausovo rozmaz�v�n� (gaussian blur). Tak�e, co se stane, pokud p�ilep�me spoustu rozt�hnut�ch textur vyobrazuj�c�ch 3D objekt na sc�nu p�esn� p�ed n�j? Odpov�� je celkem snadn� - radial blur!</p>

<p>Pot�ebujeme v�ak vy�e�it dva souvisej�c� probl�my: jak v realtimu vytv��et tuto texturu a jak ji zobrazit p�esn� p�ed objekt. �e�en� prvn�ho je mnohem snaz�� ne� si asi mysl�te. Co takhle renderovat p��mo do textury? Pokud aplikace pou��v� double buffering, je p�edn� buffer zobrazen na obrazovce a do zadn�ho se kresl�. Dokud nezavol�me p��kaz SwapBuffers(), zm�ny se navenek neprojev�. Renderov�n� do textury spo��v� v renderingu do zadn�ho bufferu (tedy klasicky, jak jsme zvykl�) a v zkop�rov�n� jeho obsahu do textury pomoc� funkce glCopyTexImage2D().</p>

<p>Probl�m dva: vycentrov�n� textury p�esn� p�ed 3D objekt. V�me, �e pokud zm�n�me viewport bez nastaven� spr�vn� perspektivy, z�sk�me deformovanou sc�nu. Nap��klad, nastav�me-li ho opravdu �irok� bude sc�na rozt�hnut� vertik�ln�.</p>

<p>Nejd��ve nastav�me viewport tak, aby byl �tvercov� a m�l stejn� rozm�ry jako textura (128x128). Po renderov�n� objektu, nakop�rujeme color buffer do textury a sma�eme ho. Obnov�me p�vodn� rozm�ry a vykresl�me objekt podruh�, tentokr�t p�i spr�vn�m rozli�en�. Pot�, co texturu namapujeme na obd�ln�k o velikosti sc�ny, rozt�hne se zp�t na p�vodn� velikost a bude um�st�n� p�esn� p�ed 3D objekt. Douf�m, �e to d�v� smysl. P�edstavte si 640x480 screenshot zmen�en� na bitmapu o velikosti 128x128 pixel�. Tuto bitmapu m��eme v grafick�m editoru rozt�hnout na p�vodn� rozm�ry 640x480 pixel�. Kvalita bude o mnoho hor��, ale obr�zku si budou odpov�dat.</p>

<p>Poj�me se pod�vat na k�d. Funkce RenderToTexture() je opravdu jednoduch�, ale p�edstavuje kvalitn� "designov� trik". Nastav�me viewport na rozm�ry textury a zavol�me rutinu pro vykreslen� spir�ly. Potom zvol�me blur texturu jako aktivn� a z viewportu do n� nakop�rujeme color buffer. Prvn� parametr funkce glCopyTexImage2D() indikuje, �e pou��v�me 2D texturu, nula ozna�uje �rove� mip mapy (mip map level), defaultn� se zad�v� nula. GL_LUMINANCE p�edstavuje form�t dat. Pou��v�me pr�v� tuto ��st bufferu, proto�e v�sledek vypad� p�esv�d�iv�ji, ne� kdybychom zadali nap�. GL_ALPHA, GL_RGB, GL_INTENSITY nebo jin�. Dal�� dva parametry ��kaj�, kde za��t (0, 0), dvakr�t 128 p�edstavuje v��ku a ���ku. Posledn� parametr bychom zm�nili, kdybychom po�adovali okraj (r�me�ek), ale te� ho nechceme. V tuto chv�li m�me v textu�e ulo�enu kopii color bufferu. Sma�eme ho a nastav�me viewport zp�t na spr�vn� rozm�ry.</p>

<p>D�LE�IT�: Tento postup m��e b�t pou�it pouze s double bufferingem. D�vodem je, �e v�echny pot�ebn� operace se mus� prov�d�t na pozad� (v zadn�m bufferu), aby je u�ivatel nevid�l.</p>

<p class="src0">void RenderToTexture()<span class="kom">// Rendering do textury</span></p>
<p class="src0">{</p>
<p class="src1">glViewport(0, 0, 128, 128);<span class="kom">// Nastaven� viewportu (odpov�d� velikosti textury)</span></p>
<p class="src"></p>
<p class="src1">ProcessHelix();<span class="kom">// Rendering spir�ly</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, BlurTexture);<span class="kom">// Zvol� texturu</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zkop�ruje viewport do textury (od 0, 0 do 128, 128, bez okraje)</span></p>
<p class="src1">glCopyTexImage2D(GL_TEXTURE_2D, 0, GL_LUMINANCE, 0, 0, 128, 128, 0);</p>
<p class="src"></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.5f, 0.5);<span class="kom">// St�edn� modr� barva pozad�</span></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src"></p>
<p class="src1">glViewport(0, 0, 640, 480);<span class="kom">// Obnoven� viewportu</span></p>
<p class="src0">}</p>

<p>Funkce DrawBlur() vykresluje p�ed sc�nu n�kolik pr�hledn�ch otexturovan�ch obd�ln�k�. Pohrajeme-li si trochu s alfou dostaneme imitaci efektu radial blur. Nejprve vypneme automatick� generov�n� texturov�ch koordin�t� a potom zapneme 2D textury. Vypneme depth testy, nastav�me blending, zapneme ho a zvol�me texturu. Abychom mohli snadno kreslit obd�ln�ky p�esn� p�es celou sc�nu, p�epneme do pravo�hl� projekce.</p>

<p class="src0">void DrawBlur(int times, float inc)<span class="kom">// Vykresl� rozmazan� obr�zek</span></p>
<p class="src0">{</p>
<p class="src1">float spost = 0.0f;<span class="kom">// Po��te�n� offset sou�adnic na textu�e</span></p>
<p class="src1">float alphainc = 0.9f / times;<span class="kom">// Rychlost blednut� pro alfa blending</span></p>
<p class="src1">float alpha = 0.2f;<span class="kom">// Po��te�n� hodnota alfy</span></p>
<p class="src"></p>
<p class="src1">glDisable(GL_TEXTURE_GEN_S);<span class="kom">// Vypne automatick� generov�n� texturov�ch koordin�t�</span></p>
<p class="src1">glDisable(GL_TEXTURE_GEN_T);</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapov�n� textur</span></p>
<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne testov�n� hloubky</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE);<span class="kom">// M�d blendingu</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, BlurTexture);<span class="kom">// Zvol� texturu</span></p>
<p class="src"></p>
<p class="src1">ViewOrtho();<span class="kom">// P�epne do pravo�hl� projekce</span></p>

<p>V cyklu vykresl�me texturu tolikr�t, abychom vytvo�ili radial blur. Sou�adnice vertex� z�st�vaj� po��d stejn�, ale zv�t�ujeme koordin�ty u textur a tak� sni�ujeme alfu. Takto vykresl�me celkem 25 quad�, jejich� textura se roztahuje poka�d� o 0.015f.</p>

<p class="src1">alphainc = alpha / times;<span class="kom">// Hodnota zm�ny alfy p�i jednom kroku</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslen� obd�ln�k�</span></p>
<p class="src2">for (int num = 0; num &lt; times; num++)<span class="kom">// Po�et krok� renderov�n� skvrn</span></p>
<p class="src2">{</p>
<p class="src3">glColor4f(1.0f, 1.0f, 1.0f, alpha);<span class="kom">// Nastaven� hodnoty alfy</span></p>
<p class="src"></p>
<p class="src3">glTexCoord2f(0 + spost, 1 - spost);<span class="kom">// Texturov� koordin�ty (0, 1)</span></p>
<p class="src3">glVertex2f(0, 0);<span class="kom">// Prvn� vertex (0, 0)</span></p>
<p class="src"></p>
<p class="src3">glTexCoord2f(0 + spost, 0 + spost);<span class="kom">// Texturov� koordin�ty (0, 0)</span></p>
<p class="src3">glVertex2f(0, 480);<span class="kom">// Druh� vertex (0, 480)</span></p>
<p class="src"></p>
<p class="src3">glTexCoord2f(1 - spost, 0 + spost);<span class="kom">// Texturov� koordin�ty (1, 0)</span></p>
<p class="src3">glVertex2f(640, 480);<span class="kom">// T�et� vertex (640, 480)</span></p>
<p class="src"></p>
<p class="src3">glTexCoord2f(1 - spost, 1 - spost);<span class="kom">// Texturov� koordin�ty (1, 1)</span></p>
<p class="src3">glVertex2f(640, 0);<span class="kom">// �tvrt� vertex (640, 0)</span></p>
<p class="src"></p>
<p class="src3">spost += inc;<span class="kom">// Postupn� zvy�ov�n� skvrn (zoomov�n� do st�edu textury)</span></p>
<p class="src3">alpha = alpha - alphainc;<span class="kom">// Postupn� sni�ov�n� alfy (blednut� obr�zku)</span></p>
<p class="src2">}</p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>

<p>Zb�v� obnovit p�vodn� parametry.</p>

<p class="src1">ViewPerspective();<span class="kom">// Obnoven� perspektivy</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testov�n� hloubky</span></p>
<p class="src1">glDisable(GL_TEXTURE_2D);<span class="kom">// Vypne mapov�n� textur</span></p>
<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vypne blending</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, 0);<span class="kom">// Zru�en� vybran� textury</span></p>
<p class="src0">}</p>

<p>Draw() je tentokr�t opravdu kr�tk�. Nastav�me �ern� pozad�, sma�eme obrazovku i hloubku a resetujeme matici. Vyrenderujeme spir�lu do textury, potom i na obrazovku a nakonec vykresl�me blur efekt.</p>

<p class="src0">void Draw(void)<span class="kom">// Vykreslen� sc�ny</span></p>
<p class="src0">{</p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubku</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">RenderToTexture();<span class="kom">// Rendering do textury</span></p>
<p class="src1">ProcessHelix();<span class="kom">// Rendering spir�ly</span></p>
<p class="src1">DrawBlur(25, 0.02f);<span class="kom">// Rendering blur efektu</span></p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vypr�zdn�n� OpenGL pipeline</span></p>
<p class="src0">}</p>

<p>Douf�m, �e se v�m tento tutori�l l�bil. Nenau�ili jste se sice nic v�c ne� rendering do textury, ale v�sledn� efekt vypad� opravdu skv�le.</p>

<p>M�te svobodu v pou��v�n� tohoto k�du ve sv�ch programech jakkoli chcete, ale p�ed t�m, ne� tak u�in�te, pod�vejte se na n�j a pochopte ho - jedin� podm�nka! Abych nezapomn�l, uve�te m� pros�m do kredit�.</p>

<p>Tady v�m nech�v�m seznam �loh, kter� si m��ete zkusit vy�e�it:</p>

<ul>
<li>Modifikujte funkci DrawBlur() tak, abyste z�skali horizont�ln� rozmaz�n�, vertik�ln� rozmaz�n� nebo dal�� efekty (Twirl blur)</li>
<li>Pohrajte si s parametry DrawBlur() (p�idat, odstranit), abyste grafiku synchronizovali s hudbou</li>
<li>Modifikujte parametry textury - nap�. GL_LUMINANCE (hezk� st�nov�n�)</li>
<li>Zkuste super fale�n� volumetrick� st�nov�n� pou�it�m tmav�ch textur nam�sto luminance textury</li>
</ul>

<p>Tak to u� bylo opravdu v�echno. Zkuste nav�t�vit m� webov� str�nky <?OdkazBlank('http://www.spinningkids.org/rio');?>, naleznete tam n�kolik dal��ch tutori�l�...</p>

<p class="autor">napsal: Dario Corno - rIo <?VypisEmail('rio@spinningkids.org');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson36.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson36_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson36.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson36.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:Eshat@gmx.net">Eshat Cakar</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson36.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:zealouselixir@mchsi.com">Warren Moore</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson36.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Schubert_P@Yahoo.de">Patrick Schubert</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson36.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:ant@solace.mh.se">Anthony Whitehead</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson36.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson36.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:dario@solinf.it">Dario Corno</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson36.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(36);?>
<?FceNeHeOkolniLekce(36);?>

<?
include 'p_end.php';
?>
