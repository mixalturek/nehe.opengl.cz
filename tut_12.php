<?
$g_title = 'CZ NeHe OpenGL - Lekce 12 - Display list';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(12);?>

<h1>Lekce 12 - Display list</h1>

<p class="nadpis_clanku">Chcete v�d�t, jak urychlit va�e programy v OpenGL? Jste unaveni z nesmysln�ho opisov�n� ji� napsan�ho k�du? Nejde to n�jak jednodu�eji? Ne�lo by nap��klad jedn�m p��kazem vykreslit otexturovanou krychli? Samoz�ejm�, �e jde. Tento tutori�l je ur�en� speci�ln� pro v�s. P�edvytvo�en� objekty a jejich vykreslov�n� jedn�m ��dkem k�du. Jak snadn�...</p>

<p>�ekn�me, �e programujete hru &quot;Asteroidy&quot;. Ka�d� level za��n� alespo� se dv�ma. No, tak�e se v klidu posad�te a p�ijdete na to, jak vytvo�it 3d asteroid. Jist� bude z polygon�, jak jinak. T�eba osmist�nn�. Pokud byste cht�li pracovat elegantn�, vytvo��te cyklus a v n�m m��ete v�e vykreslovat. Skon��te s osmn�cti nebo v�ce ��dky. V klidu. Ale pozor! Pokud tento cyklus prob�hne v�cekr�t, znateln� zpomal� vykreslov�n�. Jednou, a� budete vytv��et mnohem komplexn�j�� objekty a sc�ny, pochop�te, co m�m na mysli.</p>

<p>Tak�e, jak� je �e�en�? Display list, neboli p�edvytvo�en� objekty! T�mto zp�sobem vytv���te v�e pouze jednou. Namapovat textury, barvy, cokoli, co chcete. A samoz�ejm� mus�te tento display list pojmenovat. Jeliko� vytv���me asteroidy nazveme display list "asteroid". Ve chv�li, kdy budete cht�t vykreslit texturovan�/obarven� asteroid na monitor, v�echno, co ud�l�te je zavol�n� funkce glCallList(asteroid). P�edvytvo�en� asteroid se okam�it� zobraz�. Proto�e je jednou vytvo�en� v pam�ti (display listu), OpenGL nemus� v�e znovu p�epo��t�vat. Odstranili jsme velk� zat�en� procesoru a umo�nili programu b�et o mnoho rychleji.</p>

<p>P�ipraveni? Vytvo��me sc�nu skl�daj�c� se z patn�cti krychl�. Tyto krychle jsou vytvo�eny z krabice a v�ka - celkem dva display listy. V�ko bude vybarveno na tmav�� odst�n. K�d vych�z� z �est� lekce. P�ep�eme v�t�inu programu, aby bylo snaz�� naj�t zm�ny.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavi�kov� soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavi�kov� soubor pro standardn� vstup/v�stup</span></p>
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

<p>Deklarujeme prom�nn�. Nap�ed m�sto pro texturu. Dal�� dv� prom�nn� budou vystupovat jako pointery na m�sto do pam�ti RAM, kde jsou ulo�eny display listy.</p>

<p class="src0">GLuint texture[1];<span class="kom">// Ukl�d� texturu</span></p>
<p class="src0">GLuint box;<span class="kom">// Ukl�d� display list krabice</span></p>
<p class="src0">GLuint top;<span class="kom">// Ukl�d� display list v�ka</span></p>
<p class="src"></p>
<p class="src0">GLuint xloop;<span class="kom">// Pozice na ose x</span></p>
<p class="src0">GLuint yloop;<span class="kom">// Pozice na ose y</span></p>
<p class="src0">GLfloat xrot;<span class="kom">// Rotace na ose x</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Rotace na ose y</span></p>

<p>Vytvo��me dv� pole barev. Prvn� ukl�d� sv�tl� barvy. Hodnoty ve slo�en�ch z�vork�ch reprezentuj� �erven�, zelen� a modr� slo�ky. Druh� pole ur�uje tmav�� barvy, kter� pou�ijeme ke kreslen� v�ka krychl�. Chceme, aby bylo tmav�� ne� ostatn� st�ny.</p>

<p class="src0">static GLfloat boxcol[5][3]=<span class="kom">// Pole pro barvy st�n krychle</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Sv�tl�: �erven�, oran�ov�, �lut�, zelen�, modr�</span></p>
<p class="src1">{1.0f,0.0f,0.0f},{1.0f,0.5f,0.0f},{1.0f,1.0f,0.0f},{0.0f,1.0f,0.0f},{0.0f,1.0f,1.0f}</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">static GLfloat topcol[5][3]=<span class="kom">// Pole pro barvy v�ka krychle</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Tmav�: �erven�, oran�ov�, �lut�, zelen�, modr�</span></p>
<p class="src1">{0.5f,0.0f,0.0f},{0.5f,0.25f,0.0f},{0.5f,0.5f,0.0f},{0.0f,0.5f,0.0f},{0.0f,0.5f,0.5f}</p>
<p class="src0">};</p>

<p>N�sleduj�c� funkce generuje display listy.</p>

<p class="src0">GLvoid BuildLists(<span class="kom">// Generuje display listy</span>)</p>
<p class="src0">{</p>

<p>Za�neme ozn�men�m OpenGL, �e chceme vytvo�it dva listy. glGenList(2) pro n� alokuje m�sto v pam�ti a vr�t� pointer na prvn� z nich.</p>

<p class="src1">box=glGenLists(2);<span class="kom">// 2 listy</span></p>

<p>Vytvo��me prvn� list. U� jsme zabrali m�sto pro dva listy a v�me, �e box ukazuje na za��tek p�ipraven� pam�ti. Pou�ijeme p��kaz glNewList(). Prvn� parametr box �ekne, �e chceme ulo�it list do pam�ti, kam ukazuje. Druh� parametr GL_COMPILE ��k�, �e chceme p�edvytvo�it list v pam�ti tak, aby se nemuselo p�i ka�d�m vykreslov�n� znovu v�echno generovat a p�epo��t�vat. GL_COMPILE je stejn� jako programov�n�. Pokud nap�ete program a nahrajete ho do va�eho p�eklada�e (kompileru), mus�te ho zkompilovat v�dy, kdy� ho chcete spustit. Ale pokud bude zkompilov�n do .exe souboru, v�echno, co se mus� pro spu�t�n� vykonat je kliknout my�� na tento .exe soubor a spustit ho. Samoz�ejm� bez kompilace. Cokoli OpenGL zkompiluje v display listu je mo�no pou��t bez jak�koli dal�� pot�eby p�epo��t�v�n�. Urychl� se vykreslov�n�.</p>

<p class="src1">glNewList(box,GL_COMPILE);<span class="kom">// Nov� kompilovan� display list - krabice</span></p>
<p class="src2">glBegin(GL_QUADS);</p>
<p class="src3"><span class="kom">// Spodn� st�na</span></p>
<p class="src3">glNormal3f( 0.0f,-1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src3"><span class="kom">// P�edn� st�na</span></p>
<p class="src3">glNormal3f( 0.0f, 0.0f, 1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src3"><span class="kom">// Zadn� st�na</span></p>
<p class="src3">glNormal3f( 0.0f, 0.0f,-1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src3"><span class="kom">// Prav� st�na</span></p>
<p class="src3">glNormal3f( 1.0f, 0.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src3"><span class="kom">// Lev� st�na</span></p>
<p class="src3">glNormal3f(-1.0f, 0.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glEnd();</p>
<p class="src1">glEndList();</p>

<p>P��kazem glEndList() ozn�m�me, �e kon��me vytv��en� listu. Cokoli je mezi glNewList() a glEndList() je sou��st� display listu a naopak, pokud je n�co p�ed nebo za u� k n�mu nepat��. Abychom zjistili, kam ho ulo��me druh� display list, vezmeme hodnotu ji� vytvo�en�ho a p�i�teme k n�mu jedni�ku (na za��tku funkce jsme �ekli, �e d�l�me 2 display listy, tak�e je to v po��dku).</p>

<p class="src1">top=box+1;<span class="kom">// Do top vlo��me adresu druh�ho display listu</span></p>
<p class="src"></p>
<p class="src1">glNewList(top,GL_COMPILE);<span class="kom">// Kompilovan� display list - v�ko</span></p>
<p class="src2">glBegin(GL_QUADS);</p>
<p class="src3"><span class="kom">// Horn� st�na</span></p>
<p class="src3">glNormal3f( 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glEnd();</p>
<p class="src1">glEndList();</p>
<p class="src0">}</p>

<p>Vytvo�ili jsme oba display listy. Nahr�v�n� textur je stejn�, jako v minul�ch lekc�ch. Rozhodl jsem se pou��t mimapping, proto�e nem�m r�d, kdy� vid�m pixely. Pou�ijeme obr�zek cube.bmp ulo�en� v adres��i data. Najd�te funkci LoadBMP() a upravte ��dek se jm�nem bitmapy.</p>

<p class="src1">if (TextureImage[0]=LoadBMP(&quot;Data/Cube.bmp&quot;))<span class="kom">// Loading textury</span></p>

<p>V inicializa�n� funkci je jen n�kolik zm�n. P�id�me ��dek BuildList(). V�imn�te si, �e jsme ho um�stili a� za LoadGLTextures(). Display list by se zkompiloval bez textur.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V�echna nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje texturu</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src1">BuildLists();<span class="kom">// Vytvo�� display listy</span></p>
<p class="src1"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturov� mapov�n�</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� hloubkov� testov�n�</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>

<p>N�sleduj�c� t�i ��dky zap�naj� rychl� a �pinav� osv�tlen� (quick and dirty lighting). Light0 je p�eddefinov�no na v�t�in� video karet, tak�e zamez� nep��jemnostem p�i nastaven� sv�tel. Po light0 nastav�me osv�tlen�. Pokud va�e karta nepodporuje light0, uvid�te �ern� monitor - mus�te vypnout sv�tla. Posledn� ��dka p�id�v� barvu do mapov�n� textur. Nezapneme-li vybarvov�n� materi�lu, textura bude m�t v�dy origin�ln� barvu. glColor3f(r,g,b) nebude m�t ��dn� efekt (ve vykreslovac� funkci.</p>

<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne implicitn� sv�tlo</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne sv�tla</span></p>
<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Zapne vybarvov�n� materi�l�</span></p>

<p>Nakonec nastav�me perspektivn� korekce, aby obraz vypadal l�pe. Vr�cen�m true ozn�m�me programu, �e inicializace prob�hla v po��dku.</p>

<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep�� perspektivn� korekce</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>P�ich�z� na �adu vykreslovac� funkce. Jako oby�ejn�, p�id�me p�r ��lenost� s matematikou. Tentokr�t, ale nebudou ��dn� siny a kosiny. Za�neme vymaz�n�m obrazovky a depth bufferu. Potom namapujeme texturu na krychli. Mohl bych tento p��kaz p�idat do k�du display listu, Ale te� kdykoli mohu vym�nit aktu�ln� texturu za jinou. Douf�m, �e u� rozum�te, �e cokoli je v display listu, tak se nem��e zm�nit.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e obrazovku a hloubkov� buffer</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// V�b�r textury</span></p>

<p>M�me cyklus s ��d�c� prom�nnou yloop. Tato smy�ka je pou�ita k ur�en� pozice krychl� na ose y. Vykreslujeme p�t ��dk�, proto k�d prob�hne p�tkr�t.</p>

<p class="src1">for (yloop=1;yloop&lt;6;yloop++)<span class="kom">// Proch�z� ��dky</span></p>
<p class="src1">{</p>

<p>D�le m�me vno�en� cyklus s prom�nnou xloop. Je pou�it� pro pozici krychl� na ose x. Jejich po�et z�vis� na tom, ve kter�m ��dku se nach�zej�. Pokud se nach�z�me v horn�m ��dku vykresl�me jednu, ve druh�m dv�, atd.</p>

<p class="src2">for (xloop=0;xloop&lt;yloop;xloop++)<span class="kom">// Proch�z� sloupce</span></p>
<p class="src2">{</p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>N�sleduj�c� ��dek p�esune po��tek sou�adnic na dan� bod obrazovky. Prvn� pohled je to trochu matouc�.</p>

<p>Na ose x: Posuneme se doprava o 1,4 jednotky, tak�e pyramida je na st�edu obrazovky. Potom n�sob�me prom�nnou xloop hodnotou 2,8 a p�i�teme 1,4. (N�sob�me hodnotou 2,8, tak�e krychle nejsou jedna nad druhou. 2,8 je p�ibli�n� jejich ���ka, kdy� jsou pooto�eny o 45 stup��.) Nakonec ode�teme yloop*1,4. To je posune doleva v z�vislosti na tom, ve kter� �ad� jsme. Pokud bychom je nep�esunuli, se�ad� se na lev� stran�. (A nevypadaj� jako pyramida.)</p>

<p>Na ose y: Ode�teme prom�nnou yloop od �esti jinak by pyramida byla vytvo�ena vzh�ru nohama. Pot� n�sob�me v�sledek hodnotou 2,4. Jinak krychle budou jedna na vrcholu druh� na ose Y. (2,4 se p�ibli�n� rovn� v��ce krychle). Pot� ode�teme 7, tak�e pyramida za��n� na spodku obrazovky a je sestavov�na ze zdola nahoru.</p>

<p>Na ose z: Posuneme 20 jednotek dovnit�. Tak�e se pyramida vejde akor�t na obrazovku.</p>

<p class="src3"><span class="kom">// Pozice krychle na obrazovce</span></p>
<p class="src3">glTranslatef(1.4f+(float(xloop)*2.8f)-(float(yloop)*1.4f),((6.0f-float(yloop))*2.4f)-7.0f,-20.0f);</p>

<p>Naklon�me krychle o 45 stup�� k pohledu a ode�teme 2*yloop. Perspektivn� m�d nach�l� krychle automaticky, tak�e ode��t�me, abychom vykompenzovali naklon�n�. Nen� to nejlep�� cesta, ale pracuje to. Potom p�i�teme xrot. To n�m d�v� mo�nost ovl�dat �hel kl�vesnic�. Tak� pou�ijeme rotaci o 45 stup�� na ose y. P�i�teme yroot kv�li ovl�d�n� kl�vesnic�.</p>

<p class="src3"><span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(45.0f-(2.0f*yloop)+xrot,1.0f,0.0f,0.0f);</p>
<p class="src3">glRotatef(45.0f+yrot,0.0f,1.0f,0.0f);</p>

<p>Vybereme barvu krabice (sv�tlou). V�imn�te si, �e pou��v�me glColor3fv(). Tato funkce vyb�r� najednou v�echny t�i hodnoty (�erven�, zelen�, modr�) najednou a t�m nastav� barvu. V tomto p��pad� ji najdeme v poli boxcol s indexem yloop-1. T�m zajist�me rozli�nou barvu, pro ka�d� ��dek pyramidy. Kdybychom pou�ili xloop-1, dostali bychom stejn� barvy pro ka�d� sloupec.</p>

<p class="src3">glColor3fv(boxcol[yloop-1]);<span class="kom">// Barva</span></p>

<p>Po nastaven� barvy zb�v� jedin� - vykreslit krabici. Pro vykreslen� zavol�me pouze funkci glCallList(box). Parametr �ekne OpenGL, kter� display list m�me na mysli. Krabice bude vybarven� d��ve vybranou barvou, bude posunut� a taky nato�en�.</p>

<p class="src3">glCallList(box);<span class="kom">// Vykreslen�</span></p>

<p>Barvu v�ka vyb�r�me �pln� stejn�, jako p�ed chv�l�, akor�t z pole tmav��ch barev. Potom ho vykresl�me.</p>

<p class="src3">glColor3fv(topcol[yloop-1])<span class="kom">// Barva</span>;</p>
<p class="src"></p>
<p class="src3">glCallList(top);<span class="kom">// Vykreslen�</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Posledn� zbytek zm�n ud�l�me ve funkci WinMain(). K�d p�id�me za p��kaz SwapBuffers(hDC). Ov���me, zda jsou stisknuty �ipky a podle v�sledku pohybujeme krychlemi.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">SwapBuffers(hDC);<span class="kom">// V�m�na buffer�</span></p>
<p class="src"></p>
<p class="src4">if (keys[VK_LEFT])<span class="kom">// �ipka vlevo</span></p>
<p class="src4">{</p>
<p class="src5">yrot-=0.2f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_RIGHT])<span class="kom">// �ipka vpravo</span></p>
<p class="src4">{</p>
<p class="src5">yrot+=0.2f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_UP])<span class="kom">// �ipka nahoru</span></p>
<p class="src4">{</p>
<p class="src5">xrot-=0.2f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN])<span class="kom">// �ipka dolu</span></p>
<p class="src4">{</p>
<p class="src5">xrot+=0.2f;</p>
<p class="src4">}</p>

<p>Po do�ten� t�to lekce, byste m�li rozum�t, jak display list pracuje, jak ho vytvo�it a jak ho vykreslit. Jsou velk�m p��nosem. Nejen, �e zjednodu�� psan� slo�it�j��ch projekt�, ale tak� p�idaj� trochu na rychlosti cel�ho programu.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson12.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson12_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson12.zip">C#</a> k�d t�to lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson12.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson12.tar.gz">Cygwin</a> k�d t�to lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson12.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson12.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson12.zip">Euphoria</a> k�d t�to lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson12.zip">Game GLUT</a> k�d t�to lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson12.zip">Irix</a> k�d t�to lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson12.zip">Java</a> k�d t�to lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson12.zip">Jedi-SDL</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson12.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson12.zip">LCC Win32</a> k�d t�to lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson12.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson12.tar.gz">Linux/GLX</a> k�d t�to lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson12.tar.gz">Linux/SDL</a> k�d t�to lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson12.sit">Mac OS</a> k�d t�to lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson12.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson12.zip">MASM</a> k�d t�to lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson12.zip">Visual C++ / OpenIL</a> k�d t�to lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson12.zip">Visual Basic</a> k�d t�to lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson12.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(12);?>
<?FceNeHeOkolniLekce(12);?>

<?
include 'p_end.php';
?>
