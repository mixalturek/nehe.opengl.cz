<?
$g_title = 'CZ NeHe OpenGL - Lekce 12 - Display list';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(12);?>

<h1>Lekce 12 - Display list</h1>

<p class="nadpis_clanku">Chcete vìdìt, jak urychlit va¹e programy v OpenGL? Jste unaveni z nesmyslného opisování ji¾ napsaného kódu? Nejde to nìjak jednodu¹eji? Ne¹lo by napøíklad jedním pøíkazem vykreslit otexturovanou krychli? Samozøejmì, ¾e jde. Tento tutoriál je urèený speciálnì pro vás. Pøedvytvoøené objekty a jejich vykreslování jedním øádkem kódu. Jak snadné...</p>

<p>Øeknìme, ¾e programujete hru &quot;Asteroidy&quot;. Ka¾dý level zaèíná alespoò se dvìma. No, tak¾e se v klidu posadíte a pøijdete na to, jak vytvoøit 3d asteroid. Jistì bude z polygonù, jak jinak. Tøeba osmistìnný. Pokud byste chtìli pracovat elegantnì, vytvoøíte cyklus a v nìm mù¾ete v¹e vykreslovat. Skonèíte s osmnácti nebo více øádky. V klidu. Ale pozor! Pokud tento cyklus probìhne vícekrát, znatelnì zpomalí vykreslování. Jednou, a¾ budete vytváøet mnohem komplexnìj¹í objekty a scény, pochopíte, co mám na mysli.</p>

<p>Tak¾e, jaké je øe¹ení? Display list, neboli pøedvytvoøené objekty! Tímto zpùsobem vytváøíte v¹e pouze jednou. Namapovat textury, barvy, cokoli, co chcete. A samozøejmì musíte tento display list pojmenovat. Jeliko¾ vytváøíme asteroidy nazveme display list "asteroid". Ve chvíli, kdy budete chtít vykreslit texturovaný/obarvený asteroid na monitor, v¹echno, co udìláte je zavolání funkce glCallList(asteroid). Pøedvytvoøený asteroid se okam¾itì zobrazí. Proto¾e je jednou vytvoøený v pamìti (display listu), OpenGL nemusí v¹e znovu pøepoèítávat. Odstranili jsme velké zatí¾ení procesoru a umo¾nili programu bì¾et o mnoho rychleji.</p>

<p>Pøipraveni? Vytvoøíme scénu skládající se z patnácti krychlí. Tyto krychle jsou vytvoøeny z krabice a víka - celkem dva display listy. Víko bude vybarveno na tmav¹í odstín. Kód vychází z ¹esté lekce. Pøepí¹eme vìt¹inu programu, aby bylo snaz¹í najít zmìny.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;gl\gl.h&gt;<span class="kom">// Hlavièkový soubor pro OpenGL32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glu.h&gt;<span class="kom">// Hlavièkový soubor pro Glu32 knihovnu</span></p>
<p class="src0">#include &lt;gl\glaux.h&gt;<span class="kom">// Hlavièkový soubor pro Glaux knihovnu</span></p>
<p class="src"></p>
<p class="src0">HDC hDC = NULL;<span class="kom">// Privátní GDI Device Context</span></p>
<p class="src0">HGLRC hRC = NULL;<span class="kom">// Trvalý Rendering Context</span></p>
<p class="src0">HWND hWnd = NULL;<span class="kom">// Obsahuje Handle na¹eho okna</span></p>
<p class="src0">HINSTANCE hInstance;<span class="kom">// Obsahuje instanci aplikace</span></p>
<p class="src"></p>
<p class="src0">bool keys[256];<span class="kom">// Pole pro ukládání vstupu z klávesnice</span></p>
<p class="src0">bool active = TRUE;<span class="kom">// Ponese informaci o tom, zda je okno aktivní</span></p>
<p class="src0">bool fullscreen = TRUE;<span class="kom">// Ponese informaci o tom, zda je program ve fullscreenu</span></p>

<p>Deklarujeme promìnné. Napøed místo pro texturu. Dal¹í dvì promìnné budou vystupovat jako pointery na místo do pamìti RAM, kde jsou ulo¾eny display listy.</p>

<p class="src0">GLuint texture[1];<span class="kom">// Ukládá texturu</span></p>
<p class="src0">GLuint box;<span class="kom">// Ukládá display list krabice</span></p>
<p class="src0">GLuint top;<span class="kom">// Ukládá display list víka</span></p>
<p class="src"></p>
<p class="src0">GLuint xloop;<span class="kom">// Pozice na ose x</span></p>
<p class="src0">GLuint yloop;<span class="kom">// Pozice na ose y</span></p>
<p class="src0">GLfloat xrot;<span class="kom">// Rotace na ose x</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Rotace na ose y</span></p>

<p>Vytvoøíme dvì pole barev. První ukládá svìtlé barvy. Hodnoty ve slo¾ených závorkách reprezentují èervené, zelené a modré slo¾ky. Druhé pole urèuje tmav¹í barvy, které pou¾ijeme ke kreslení víka krychlí. Chceme, aby bylo tmav¹í ne¾ ostatní stìny.</p>

<p class="src0">static GLfloat boxcol[5][3]=<span class="kom">// Pole pro barvy stìn krychle</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Svìtlé: èervená, oran¾ová, ¾lutá, zelená, modrá</span></p>
<p class="src1">{1.0f,0.0f,0.0f},{1.0f,0.5f,0.0f},{1.0f,1.0f,0.0f},{0.0f,1.0f,0.0f},{0.0f,1.0f,1.0f}</p>
<p class="src0">};</p>
<p class="src"></p>
<p class="src0">static GLfloat topcol[5][3]=<span class="kom">// Pole pro barvy víka krychle</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Tmavé: èervená, oran¾ová, ¾lutá, zelená, modrá</span></p>
<p class="src1">{0.5f,0.0f,0.0f},{0.5f,0.25f,0.0f},{0.5f,0.5f,0.0f},{0.0f,0.5f,0.0f},{0.0f,0.5f,0.5f}</p>
<p class="src0">};</p>

<p>Následující funkce generuje display listy.</p>

<p class="src0">GLvoid BuildLists(<span class="kom">// Generuje display listy</span>)</p>
<p class="src0">{</p>

<p>Zaèneme oznámením OpenGL, ¾e chceme vytvoøit dva listy. glGenList(2) pro nì alokuje místo v pamìti a vrátí pointer na první z nich.</p>

<p class="src1">box=glGenLists(2);<span class="kom">// 2 listy</span></p>

<p>Vytvoøíme první list. U¾ jsme zabrali místo pro dva listy a víme, ¾e box ukazuje na zaèátek pøipravené pamìti. Pou¾ijeme pøíkaz glNewList(). První parametr box øekne, ¾e chceme ulo¾it list do pamìti, kam ukazuje. Druhý parametr GL_COMPILE øíká, ¾e chceme pøedvytvoøit list v pamìti tak, aby se nemuselo pøi ka¾dém vykreslování znovu v¹echno generovat a pøepoèítávat. GL_COMPILE je stejné jako programování. Pokud napí¹ete program a nahrajete ho do va¹eho pøekladaèe (kompileru), musíte ho zkompilovat v¾dy, kdy¾ ho chcete spustit. Ale pokud bude zkompilován do .exe souboru, v¹echno, co se musí pro spu¹tìní vykonat je kliknout my¹í na tento .exe soubor a spustit ho. Samozøejmì bez kompilace. Cokoli OpenGL zkompiluje v display listu je mo¾no pou¾ít bez jakékoli dal¹í potøeby pøepoèítávání. Urychlí se vykreslování.</p>

<p class="src1">glNewList(box,GL_COMPILE);<span class="kom">// Nový kompilovaný display list - krabice</span></p>
<p class="src2">glBegin(GL_QUADS);</p>
<p class="src3"><span class="kom">// Spodní stìna</span></p>
<p class="src3">glNormal3f( 0.0f,-1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src3"><span class="kom">// Pøední stìna</span></p>
<p class="src3">glNormal3f( 0.0f, 0.0f, 1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src3"><span class="kom">// Zadní stìna</span></p>
<p class="src3">glNormal3f( 0.0f, 0.0f,-1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src3"><span class="kom">// Pravá stìna</span></p>
<p class="src3">glNormal3f( 1.0f, 0.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f, -1.0f,  1.0f);</p>
<p class="src3"><span class="kom">// Levá stìna</span></p>
<p class="src3">glNormal3f(-1.0f, 0.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, -1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f, -1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src2">glEnd();</p>
<p class="src1">glEndList();</p>

<p>Pøíkazem glEndList() oznámíme, ¾e konèíme vytváøení listu. Cokoli je mezi glNewList() a glEndList() je souèástí display listu a naopak, pokud je nìco pøed nebo za u¾ k nìmu nepatøí. Abychom zjistili, kam ho ulo¾íme druhý display list, vezmeme hodnotu ji¾ vytvoøeného a pøièteme k nìmu jednièku (na zaèátku funkce jsme øekli, ¾e dìláme 2 display listy, tak¾e je to v poøádku).</p>

<p class="src1">top=box+1;<span class="kom">// Do top vlo¾íme adresu druhého display listu</span></p>
<p class="src"></p>
<p class="src1">glNewList(top,GL_COMPILE);<span class="kom">// Kompilovaný display list - víko</span></p>
<p class="src2">glBegin(GL_QUADS);</p>
<p class="src3"><span class="kom">// Horní stìna</span></p>
<p class="src3">glNormal3f( 0.0f, 1.0f, 0.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f,  1.0f, -1.0f);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,  1.0f,  1.0f);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f,  1.0f, -1.0f);</p>
<p class="src2">glEnd();</p>
<p class="src1">glEndList();</p>
<p class="src0">}</p>

<p>Vytvoøili jsme oba display listy. Nahrávání textur je stejné, jako v minulých lekcích. Rozhodl jsem se pou¾ít mimapping, proto¾e nemám rád, kdy¾ vidím pixely. Pou¾ijeme obrázek cube.bmp ulo¾ený v adresáøi data. Najdìte funkci LoadBMP() a upravte øádek se jménem bitmapy.</p>

<p class="src1">if (TextureImage[0]=LoadBMP(&quot;Data/Cube.bmp&quot;))<span class="kom">// Loading textury</span></p>

<p>V inicializaèní funkci je jen nìkolik zmìn. Pøidáme øádek BuildList(). V¹imnìte si, ¾e jsme ho umístili a¾ za LoadGLTextures(). Display list by se zkompiloval bez textur.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echna nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje texturu</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src1">BuildLists();<span class="kom">// Vytvoøí display listy</span></p>
<p class="src1"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturové mapování</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí hloubkové testování</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>

<p>Následující tøi øádky zapínají rychlé a ¹pinavé osvìtlení (quick and dirty lighting). Light0 je pøeddefinováno na vìt¹inì video karet, tak¾e zamezí nepøíjemnostem pøi nastavení svìtel. Po light0 nastavíme osvìtlení. Pokud va¹e karta nepodporuje light0, uvidíte èerný monitor - musíte vypnout svìtla. Poslední øádka pøidává barvu do mapování textur. Nezapneme-li vybarvování materiálu, textura bude mít v¾dy originální barvu. glColor3f(r,g,b) nebude mít ¾ádný efekt (ve vykreslovací funkci.</p>

<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Zapne implicitní svìtlo</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne svìtla</span></p>
<p class="src1">glEnable(GL_COLOR_MATERIAL);<span class="kom">// Zapne vybarvování materiálù</span></p>

<p>Nakonec nastavíme perspektivní korekce, aby obraz vypadal lépe. Vrácením true oznámíme programu, ¾e inicializace probìhla v poøádku.</p>

<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Pøichází na øadu vykreslovací funkce. Jako obyèejnì, pøidáme pár ¹íleností s matematikou. Tentokrát, ale nebudou ¾ádné siny a kosiny. Zaèneme vymazáním obrazovky a depth bufferu. Potom namapujeme texturu na krychli. Mohl bych tento pøíkaz pøidat do kódu display listu, Ale teï kdykoli mohu vymìnit aktuální texturu za jinou. Doufám, ¾e u¾ rozumíte, ¾e cokoli je v display listu, tak se nemù¾e zmìnit.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e obrazovku a hloubkový buffer</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Výbìr textury</span></p>

<p>Máme cyklus s øídící promìnnou yloop. Tato smyèka je pou¾ita k urèení pozice krychlí na ose y. Vykreslujeme pìt øádkù, proto kód probìhne pìtkrát.</p>

<p class="src1">for (yloop=1;yloop&lt;6;yloop++)<span class="kom">// Prochází øádky</span></p>
<p class="src1">{</p>

<p>Dále máme vnoøený cyklus s promìnnou xloop. Je pou¾itý pro pozici krychlí na ose x. Jejich poèet závisí na tom, ve kterém øádku se nacházejí. Pokud se nacházíme v horním øádku vykreslíme jednu, ve druhém dvì, atd.</p>

<p class="src2">for (xloop=0;xloop&lt;yloop;xloop++)<span class="kom">// Prochází sloupce</span></p>
<p class="src2">{</p>
<p class="src3">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Následující øádek pøesune poèátek souøadnic na daný bod obrazovky. První pohled je to trochu matoucí.</p>

<p>Na ose x: Posuneme se doprava o 1,4 jednotky, tak¾e pyramida je na støedu obrazovky. Potom násobíme promìnnou xloop hodnotou 2,8 a pøièteme 1,4. (Násobíme hodnotou 2,8, tak¾e krychle nejsou jedna nad druhou. 2,8 je pøibli¾nì jejich ¹íøka, kdy¾ jsou pootoèeny o 45 stupòù.) Nakonec odeèteme yloop*1,4. To je posune doleva v závislosti na tom, ve které øadì jsme. Pokud bychom je nepøesunuli, seøadí se na levé stranì. (A nevypadají jako pyramida.)</p>

<p>Na ose y: Odeèteme promìnnou yloop od ¹esti jinak by pyramida byla vytvoøena vzhùru nohama. Poté násobíme výsledek hodnotou 2,4. Jinak krychle budou jedna na vrcholu druhé na ose Y. (2,4 se pøibli¾nì rovná vý¹ce krychle). Poté odeèteme 7, tak¾e pyramida zaèíná na spodku obrazovky a je sestavována ze zdola nahoru.</p>

<p>Na ose z: Posuneme 20 jednotek dovnitø. Tak¾e se pyramida vejde akorát na obrazovku.</p>

<p class="src3"><span class="kom">// Pozice krychle na obrazovce</span></p>
<p class="src3">glTranslatef(1.4f+(float(xloop)*2.8f)-(float(yloop)*1.4f),((6.0f-float(yloop))*2.4f)-7.0f,-20.0f);</p>

<p>Nakloníme krychle o 45 stupòù k pohledu a odeèteme 2*yloop. Perspektivní mód nachýlí krychle automaticky, tak¾e odeèítáme, abychom vykompenzovali naklonìní. Není to nejlep¹í cesta, ale pracuje to. Potom pøièteme xrot. To nám dává mo¾nost ovládat úhel klávesnicí. Také pou¾ijeme rotaci o 45 stupòù na ose y. Pøièteme yroot kvùli ovládání klávesnicí.</p>

<p class="src3"><span class="kom">// Rotace</span></p>
<p class="src3">glRotatef(45.0f-(2.0f*yloop)+xrot,1.0f,0.0f,0.0f);</p>
<p class="src3">glRotatef(45.0f+yrot,0.0f,1.0f,0.0f);</p>

<p>Vybereme barvu krabice (svìtlou). V¹imnìte si, ¾e pou¾íváme glColor3fv(). Tato funkce vybírá najednou v¹echny tøi hodnoty (èervená, zelená, modrá) najednou a tím nastaví barvu. V tomto pøípadì ji najdeme v poli boxcol s indexem yloop-1. Tím zajistíme rozli¹nou barvu, pro ka¾dý øádek pyramidy. Kdybychom pou¾ili xloop-1, dostali bychom stejné barvy pro ka¾dý sloupec.</p>

<p class="src3">glColor3fv(boxcol[yloop-1]);<span class="kom">// Barva</span></p>

<p>Po nastavení barvy zbývá jediné - vykreslit krabici. Pro vykreslení zavoláme pouze funkci glCallList(box). Parametr øekne OpenGL, který display list máme na mysli. Krabice bude vybarvená døíve vybranou barvou, bude posunutá a taky natoèená.</p>

<p class="src3">glCallList(box);<span class="kom">// Vykreslení</span></p>

<p>Barvu víka vybíráme úplnì stejnì, jako pøed chvílí, akorát z pole tmav¹ích barev. Potom ho vykreslíme.</p>

<p class="src3">glColor3fv(topcol[yloop-1])<span class="kom">// Barva</span>;</p>
<p class="src"></p>
<p class="src3">glCallList(top);<span class="kom">// Vykreslení</span></p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Poslední zbytek zmìn udìláme ve funkci WinMain(). Kód pøidáme za pøíkaz SwapBuffers(hDC). Ovìøíme, zda jsou stisknuty ¹ipky a podle výsledku pohybujeme krychlemi.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">SwapBuffers(hDC);<span class="kom">// Výmìna bufferù</span></p>
<p class="src"></p>
<p class="src4">if (keys[VK_LEFT])<span class="kom">// ©ipka vlevo</span></p>
<p class="src4">{</p>
<p class="src5">yrot-=0.2f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_RIGHT])<span class="kom">// ©ipka vpravo</span></p>
<p class="src4">{</p>
<p class="src5">yrot+=0.2f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_UP])<span class="kom">// ©ipka nahoru</span></p>
<p class="src4">{</p>
<p class="src5">xrot-=0.2f;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN])<span class="kom">// ©ipka dolu</span></p>
<p class="src4">{</p>
<p class="src5">xrot+=0.2f;</p>
<p class="src4">}</p>

<p>Po doètení této lekce, byste mìli rozumìt, jak display list pracuje, jak ho vytvoøit a jak ho vykreslit. Jsou velkým pøínosem. Nejen, ¾e zjednodu¹í psaní slo¾itìj¹ích projektù, ale také pøidají trochu na rychlosti celého programu.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson12.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson12_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson12.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson12.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson12.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson12.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson12.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson12.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson12.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson12.zip">Irix</a> kód této lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson12.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson12.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson12.jar">JoGL</a> kód této lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson12.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson12.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson12.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson12.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson12.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson12.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson12.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson12.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson12.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson12.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(12);?>
<?FceNeHeOkolniLekce(12);?>

<?
include 'p_end.php';
?>
