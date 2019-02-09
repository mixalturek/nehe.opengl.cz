<?
$g_title = 'CZ NeHe OpenGL - Lekce 10 - Vytvoøení 3D svìta a pohyb v nìm';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(10);?>

<h1>Lekce 10 - Vytvoøení 3D svìta a pohyb v nìm</h1>

<p class="nadpis_clanku">Do souèasnosti jsme programovali otáèející se kostku nebo pár hvìzd. Máte (mìli byste mít :-) základní pojem o 3D. Ale rotující krychle asi nejsou to nejlep¹í k tvorbì dobrých deathmatchových protivníkù! Neèekejte a zaènìte s Quakem IV je¹tì dnes! Tyto dny potøebujete k velkému, komplikovanému a dynamickému 3D svìtu s pohybem do v¹ech smìrù, skvìlými efekty zrcadel, portálù, deformacemi a tøeba také vysokým frameratem. Tato lekce vám vysvìtlí základní strukturu 3D svìta a pohybu v nìm.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
<p class="src0">#include &lt;math.h&gt;<span class="kom">// Hlavièkový soubor pro matematickou knihovnu</span></p>
<p class="src"></p>
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
<p class="src"></p>
<p class="src0">bool blend;<span class="kom">// Blending ON/OFF</span></p>
<p class="src0">bool bp;<span class="kom">// B stisknuto? (blending)</span></p>
<p class="src0">bool fp;<span class="kom">// F stisknuto? (texturové filtry)</span></p>
<p class="src"></p>
<p class="src0">const float piover180 = 0.0174532925f;<span class="kom">// Zjednodu¹í pøevod mezi stupni a radiány</span></p>
<p class="src0">float heading;<span class="kom">// Pomocná pro pøepoèítávání xpos a zpos pøi pohybu</span></p>
<p class="src0">float xpos;<span class="kom">// Urèuje x-ové souøadnice na podlaze</span></p>
<p class="src0">float zpos;<span class="kom">// Urèuje z-ové souøadnice na podlaze</span></p>
<p class="src"></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y rotace (natoèení scény doleva/doprava - smìr pohledu)</span></p>
<p class="src0">GLfloat walkbias = 0;<span class="kom">// Houpání scény pøi pohybu (simulace krokù)</span></p>
<p class="src0">GLfloat walkbiasangle = 0;<span class="kom">// Pomocná pro vypoèítání walkbias</span></p>
<p class="src0">GLfloat lookupdown = 0.0f;<span class="kom">// Urèuje úhel natoèení pohledu nahoru/dolù</span></p>
<p class="src0">GLfloat z=0.0f;<span class="kom">// Hloubka v obrazovce</span></p>
<p class="src"></p>
<p class="src0">GLuint filter;<span class="kom">// Pou¾itý texturový filtr</span></p>
<p class="src0">GLuint texture[3];<span class="kom">// Ukládá textury</span></p>

<p>Bìhem definování 3D svìta stylem dlouhých sérií èísel se stává stále obtí¾nìj¹ím udr¾et slo¾itý kód pøehledný. Musíme tøídit data do jednoduchého a pøedev¹ím funkèního tvaru. Pro zpøehlednìní vytvoøíme celkem tøi struktury.</p>

<p>Body obsahují skuteèná data, která zajímají OGL. Ka¾dý bod definujeme pozicí v prostoru (x,y,z) a koordináty textury (u,v).</p>

<p class="src0">typedef struct tagVERTEX<span class="kom">// Struktura bodu</span></p>
<p class="src0">{</p>
<p class="src1">float x, y, z;<span class="kom">// Souøadnice v prostoru</span></p>
<p class="src1">float u, v;<span class="kom">// Texturové koordináty</span></p>
<p class="src0">} VERTEX;</p>

<p>V¹echno se skládá z ploch. Proto¾e trojúhelníky jsou nejjednodu¹¹í, vyu¾ijeme právì je.</p>

<p class="src0">typedef struct tagTRIANGLE<span class="kom">// Struktura trojúhelníku</span></p>
<p class="src0">{</p>
<p class="src1">VERTEX vertex[3];<span class="kom">// Pole tøí bodù</span></p>
<p class="src0">} TRIANGLE;</p>

<p>Na poèátku v¹eho je sektor. Ka¾dý 3D svìt je v základì celý ze sektorù. Mù¾e jím být místnost, kostka èi jakýkoli jiný vìt¹í útvar.</p>

<p class="src0">typedef struct tagSECTOR<span class="kom">// Struktura sektoru</span></p>
<p class="src0">{</p>
<p class="src1">int numtriangles;<span class="kom">// Poèet trojúhelníkù v sektoru</span></p>
<p class="src1">TRIANGLE* triangle;<span class="kom">// Ukazatel na dynamické pole trojúhelníkù</span></p>
<p class="src0">} SECTOR;</p>
<p class="src"></p>
<p class="src0">SECTOR sector1;<span class="kom">// Bude obsahovat v¹echna data 3D svìta</span></p>

<p>Abychom program je¹tì více zpøehlednili, ve zdrojovém kódu, který se kompiluje, nebudou ¾ádné èíselné souøadnice. K exe souboru - výsledku na¹í práce - pøilo¾íme textový soubor. V nìm nadefinujeme v¹echny body 3D prostoru a k nim odpovídající texturové koordináty. Z dùvodu vìt¹í pøehlednosti pøidáme komentáøe. Bez nich by byl totální zmatek. Obsah souboru se mù¾e kdykoli zmìnit. Hodit se to bude pøedev¹ím pøi vytváøení prostøedí - metoda pokusù a omylù, kdy nemusíte poka¾dé rekompilovat program. Upravovat mù¾e i u¾ivatel a tím si vytvoøit vlastní prostøedí. Nemusíte mu poskytovat nic navíc, neøkuli zdrojové kódy. Tento soubor by pøece stejnì dostal. Ze zaèátku bude lep¹í pou¾ívat textové soubory (snadná editace, ménì kódu), binární odlo¾íme na pozdìji.</p>

<p>První øádka NUMPOLLIES xx urèuje celkový poèet trojúhelníkù. Text za zpìtnými lomítky znaèí komentáø. V ka¾dém následujícím øádku je definován jeden bod v prostoru a texturové koordináty. Tøi øádky urèí trojúhelník, celý soubor sektor.</p>

<p class="src0">NUMPOLLIES 36</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Floor 1</span></p>
<p class="src0">-3.0 0.0 -3.0 0.0 6.0</p>
<p class="src0">-3.0 0.0&nbsp;&nbsp;3.0 0.0 0.0</p>
<p class="src0">&nbsp;3.0 0.0&nbsp;&nbsp;3.0 6.0 0.0</p>
<p class="src0">-3.0 0.0 -3.0 0.0 6.0</p>
<p class="src0">&nbsp;3.0 0.0 -3.0 6.0 6.0</p>
<p class="src0">&nbsp;3.0 0.0&nbsp;&nbsp;3.0 6.0 0.0</p>
<p class="src"></p>
<p class="src0"><span class="kom">// Ceiling 1</span></p>
<p class="src0">-3.0 1.0 -3.0 0.0 6.0</p>
<p class="src0">-3.0 1.0&nbsp;&nbsp;3.0 0.0 0.0</p>
<p class="src0">&nbsp;3.0 1.0&nbsp;&nbsp;3.0 6.0 0.0</p>
<p class="src0">-3.0 1.0 -3.0 0.0 6.0</p>
<p class="src0">&nbsp;3.0 1.0 -3.0 6.0 6.0</p>
<p class="src0">&nbsp;3.0 1.0&nbsp;&nbsp;3.0 6.0 0.0</p>

<p>... atd. Data jednoho trojúhelníku tedy obecnì vypadají takto:</p>

<p class="src0">x1 y1 z1 u1 v1</p>
<p class="src0">x2 y2 z2 u2 v2</p>
<p class="src0">x3 y3 z3 u3 v3</p>

<p>Otázkou je, jak tyto data vyjmeme ze souboru. Vytvoøíme funkci readstr(), která naète jeden <b>pou¾itelný</b> øádek.</p>

<p class="src0">void readstr(FILE *f,char *string)<span class="kom">// Naète jeden pou¾itelný øádek ze souboru</span></p>
<p class="src0">{</p>
<p class="src1">do</p>
<p class="src1">{</p>
<p class="src2">fgets(string, 255, f);<span class="kom">// Naèti øádek</span></p>
<p class="src1">} while ((string[0] == '/') || (string[0] == '\n'));<span class="kom">// Pokud není pou¾itelný naèti dal¹í</span></p>
<p class="src"></p>
<p class="src1">return;</p>
<p class="src0">}</p>

<p>Tuto funkci budeme volat v SetupWorld(). Nadefinujeme ná¹ soubor jako filein a otevøeme ho pouze pro ètení. Na konci ho samozøejmì zavøeme.</p>

<p class="src0">void SetupWorld()<span class="kom">// Naèti 3D svìt ze souboru</span></p>
<p class="src0">{</p>
<p class="src1">float x, y, z, u, v;<span class="kom">// body v prostoru a koordináty textur</span></p>
<p class="src1">int numtriangles;<span class="kom">// Poèet trojúhelníkù</span></p>
<p class="src1">FILE *filein;<span class="kom">// Ukazatel na soubor</span></p>
<p class="src1">char oneline[255];<span class="kom">// Znakový buffer</span></p>
<p class="src1">filein = fopen(&quot;data/world.txt&quot;, &quot;rt&quot;);<span class="kom">// Otevøení souboru pro ètení</span></p>

<p>Pøeèteme data sektoru. Tato lekce bude poèítat pouze s jedním sektorem, ale není tì¾ké provést malou úpravu. Program potøebuje znát poèet trojúhelníkù v sektoru, aby vìdìl, kolik informací má pøeèíst. Tato hodnota mù¾e být definována jako konstanta pøímo v programu, ale urèitì udìláme lépe, kdy¾ ji ulo¾íme pøímo do souboru (program se pøizpùsobí).</p>

<p class="src1">readstr(filein,oneline);<span class="kom">// Naètení prvního pou¾itelného øádku</span></p>
<p class="src1">sscanf(oneline, &quot;NUMPOLLIES %d\n&quot;, &amp;numtriangles);<span class="kom">// Vyjmeme poèet trojúhelníkù</span></p>

<p>Alokujeme potøebnou pamì» pro v¹echny trojúhelníky a ulo¾íme jejich poèet do polo¾ky struktury.</p>

<p class="src1">sector1.triangle = new TRIANGLE[numtriangles];<span class="kom">// Alokace potøebné pamìti</span></p>
<p class="src1">sector1.numtriangles = numtriangles;<span class="kom">// Ulo¾ení poètu trojúhelníkù</span></p>

<p>Po alokaci pamìti mù¾eme pøistoupit k inicializaci v¹ech datových slo¾ek sektoru.</p>

<p class="src1">for (int loop = 0; loop &lt; numtriangles; loop++)<span class="kom">// Prochází trojúhelníky</span></p>
<p class="src1">{</p>
<p class="src2">for (int vert = 0; vert &lt; 3; vert++)<span class="kom">// Prochází vrcholy trojúhelníkù</span></p>
<p class="src2">{</p>

<p>Naèteme øádek, do pomocných promìnných ulo¾íme jednotlivé hodnoty a ty znovu ulo¾íme do polo¾ek struktury. S mezikrokem je kód mnohem pøehlednìj¹í.</p>

<p class="src3">readstr(filein,oneline);<span class="kom">// Naète øádek</span></p>
<p class="src3">sscanf(oneline, &quot;%f %f %f %f %f&quot;, &amp;x, &amp;y, &amp;z, &amp;u, &amp;v);<span class="kom">// Naètení do pomocných promìnných</span></p>
<p class="src"></p>
<p class="src3"><span class="kom">// Inicializuje jednotlivé polo¾ky struktury</span></p>
<p class="src3">sector1.triangle[loop].vertex[vert].x = x;</p>
<p class="src3">sector1.triangle[loop].vertex[vert].y = y;</p>
<p class="src3">sector1.triangle[loop].vertex[vert].z = z;</p>
<p class="src3">sector1.triangle[loop].vertex[vert].u = u;</p>
<p class="src3">sector1.triangle[loop].vertex[vert].v = v;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">fclose(filein);<span class="kom">// Zavøe soubor</span></p>
<p class="src1">return;</p>
<p class="src0">}</p>

<p>Právì napsanou funkci zavoláme pøi inicializaci programu.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// V¹echna nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Nahraje texturu</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne mapování textur</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA,GL_ONE);<span class="kom">// Nastavení blendingu pro prùhlednost</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Nastavení hloubkového bufferu</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne hloubkové testování</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Povolíme jemné stínování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Nejlep¹í perspektivní korekce</span></p>
<p class="src"></p>
<p class="src1">SetupWorld();<span class="kom">// Loading 3D svìta</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Teï kdy¾ máme sektor naètený do pamìti, potøebujeme ho zobrazit. U¾ dlouho známe nìjaké ty rotace a pohyb, ale kamera v¾dy smìøovala do støedu (0,0,0). Ka¾dý dobrý 3D engine umo¾òuje chodit kolem a objevovat svìt. Jedna mo¾nost, jak k tomu dospìt je toèit kamerou a kreslit 3D prostøedí relativnì k pozici kamery - funkce gluLookAt(). Proto¾e tohle je¹tì neznáme budeme kameru simulovat takto:</p>

<ul>
<li>U¾ivatel stiskne ¹ipku</li>
<li>Vlevo/vpravo - otoèíme svìt okolo støedu v opaèném smìru ne¾ je rotace kamery - glRoratef()</li>
<li>Dopøedu/dozadu - posuneme svìt v opaèném smìru ne¾ je pohyb kamery - glTranslatef()</li>
</ul>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">GLfloat x_m, y_m, z_m, u_m, v_m;<span class="kom">// Pomocné souøadnice a koordináty textury</span></p>
<p class="src1">GLfloat xtrans = -xpos;<span class="kom">// Pro pohyb na ose x</span></p>
<p class="src1">GLfloat ztrans = -zpos;<span class="kom">// Pro pohyb na ose z</span></p>
<p class="src1">GLfloat ytrans = -walkbias-0.25f;<span class="kom">// Poskakování kamery (simulace krokù)</span></p>
<p class="src1">GLfloat sceneroty = 360.0f - yrot;<span class="kom">// Úhel smìru pohledu</span></p>
<p class="src"></p>
<p class="src1">int numtriangles;<span class="kom">// Poèet trojúhelníkù</span></p>
<p class="src"></p>
<p class="src1">glRotatef(lookupdown, 1.0f,0.0f,0.0f);<span class="kom">// Rotace na ose x - pohled nahoru/dolù</span></p>
<p class="src1">glRotatef(sceneroty, 0.0f,1.0f,0.0f);<span class="kom">// Rotace na ose y - otoèení doleva/doprava</span></p>
<p class="src1">glTranslatef(xtrans, ytrans, ztrans);<span class="kom">// Posun na pozici ve scénì</span></p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[filter]);<span class="kom">// Výbìr textury podle filtru</span></p>
<p class="src"></p>
<p class="src1">numtriangles = sector1.numtriangles;<span class="kom">// Poèet trojúhelníkù - pro pøehlednost</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Projde a vykreslí v¹echny trojúhelníky</span></p>
<p class="src1">for (int loop_m = 0; loop_m &lt; numtriangles; loop_m++)</p>
<p class="src1">{</p>
<p class="src2">glBegin(GL_TRIANGLES);<span class="kom">// Zaèátek kreslení trojúhelníkù</span></p>
<p class="src3">glNormal3f(0.0f, 0.0f, 1.0f);<span class="kom">// Normála ukazuje dopøedu - svìtlo</span></p>
<p class="src"></p>
<p class="src3">x_m = sector1.triangle[loop_m].vertex[0].x;<span class="kom">// První vrchol</span></p>
<p class="src3">y_m = sector1.triangle[loop_m].vertex[0].y;</p>
<p class="src3">z_m = sector1.triangle[loop_m].vertex[0].z;</p>
<p class="src3">u_m = sector1.triangle[loop_m].vertex[0].u;</p>
<p class="src3">v_m = sector1.triangle[loop_m].vertex[0].v;</p>
<p class="src3">glTexCoord2f(u_m,v_m); glVertex3f(x_m,y_m,z_m);<span class="kom">// Vykreslení</span></p>
<p class="src"></p>
<p class="src3">x_m = sector1.triangle[loop_m].vertex[1].x;<span class="kom">// Druhý vrchol</span></p>
<p class="src3">y_m = sector1.triangle[loop_m].vertex[1].y;</p>
<p class="src3">z_m = sector1.triangle[loop_m].vertex[1].z;</p>
<p class="src3">u_m = sector1.triangle[loop_m].vertex[1].u;</p>
<p class="src3">v_m = sector1.triangle[loop_m].vertex[1].v;</p>
<p class="src3">glTexCoord2f(u_m,v_m); glVertex3f(x_m,y_m,z_m);<span class="kom">// Vykreslení</span></p>
<p class="src"></p>
<p class="src3">x_m = sector1.triangle[loop_m].vertex[2].x;<span class="kom">// Tøetí vrchol</span></p>
<p class="src3">y_m = sector1.triangle[loop_m].vertex[2].y;</p>
<p class="src3">z_m = sector1.triangle[loop_m].vertex[2].z;</p>
<p class="src3">u_m = sector1.triangle[loop_m].vertex[2].u;</p>
<p class="src3">v_m = sector1.triangle[loop_m].vertex[2].v;</p>
<p class="src3">glTexCoord2f(u_m,v_m); glVertex3f(x_m,y_m,z_m);<span class="kom">// Vykreslení</span></p>
<p class="src"></p>
<p class="src2">glEnd();<span class="kom">// Konec kreslení trojúhelníkù</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Pøejdeme do funkce WinMain() na ovládání klávesnicí. Kdy¾ je stisknuta ¹ipka vlevo/vpravo, promìnná yrot je zvý¹ena/sní¾ena, tudí¾ se natoèí výhled. Kdy¾ je stisknuta ¹ipka dopøedu/dozadu, spoèítá se nová pozice pro kameru s pou¾itím sinu a kosinu - vy¾aduje trochu znalostí trigonometrie. Piover180 je pouze èíslo pro konverzi mezi stupni a radiány. Walkbias je offset vytváøející houpání scény pøi simulaci krokù. Jednodu¹e upraví y pozici kamery podle sinové vlny. Jako jednoduchý pohyb vpøed a vzad nevypadá ¹patnì.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src4">if (keys['B'] &amp;&amp; !bp)<span class="kom">// Klávesa B - zapne/vypne blending</span></p>
<p class="src4">{</p>
<p class="src5">bp=TRUE;</p>
<p class="src5">blend=!blend;</p>
<p class="src5">if (!blend)</p>
<p class="src5">{</p>
<p class="src6">glDisable(GL_BLEND);</p>
<p class="src6">glEnable(GL_DEPTH_TEST);</p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">glEnable(GL_BLEND);</p>
<p class="src6">glDisable(GL_DEPTH_TEST);</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src4">if (!keys['B'])</p>
<p class="src4">{</p>
<p class="src5">bp=FALSE;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys['F'] &amp;&amp; !fp)<span class="kom">// Klávesa F - cyklování mezi texturovými filtry</span></p>
<p class="src4">{</p>
<p class="src5">fp=TRUE;</p>
<p class="src5">filter+=1;</p>
<p class="src5">if (filter&gt;2)</p>
<p class="src5">{</p>
<p class="src6">filter=0;</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src4">if (!keys['F'])</p>
<p class="src4">{</p>
<p class="src5">fp=FALSE;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_UP])<span class="kom">// ©ipka nahoru - pohyb dopøedu</span></p>
<p class="src4">{</p>
<p class="src"></p>
<p class="src5">xpos -= (float)sin(heading*piover180) * 0.05f;<span class="kom">// Pohyb na ose x</span></p>
<p class="src5">zpos -= (float)cos(heading*piover180) * 0.05f;<span class="kom">// Pohyb na ose z</span></p>
<p class="src5">if (walkbiasangle &gt;= 359.0f)</p>
<p class="src5">{</p>
<p class="src6">walkbiasangle = 0.0f;</p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">walkbiasangle+= 10;</p>
<p class="src5">}</p>
<p class="src5">walkbias = (float)sin(walkbiasangle * piover180)/20.0f;<span class="kom">// Simulace krokù</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_DOWN])<span class="kom">// ©ipka dolù - pohyb dozadu</span></p>
<p class="src4">{</p>
<p class="src5">xpos += (float)sin(heading*piover180) * 0.05f;<span class="kom">// Pohyb na ose x</span></p>
<p class="src5">zpos += (float)cos(heading*piover180) * 0.05f;<span class="kom">// Pohyb na ose z</span></p>
<p class="src5">if (walkbiasangle &lt;= 1.0f)</p>
<p class="src5">{</p>
<p class="src6">walkbiasangle = 359.0f;</p>
<p class="src5">}</p>
<p class="src5">else</p>
<p class="src5">{</p>
<p class="src6">walkbiasangle-= 10;</p>
<p class="src5">}</p>
<p class="src5">walkbias = (float)sin(walkbiasangle * piover180)/20.0f;<span class="kom">// Simulace krokù</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_RIGHT])<span class="kom">// ©ipka doprava</span></p>
<p class="src4">{</p>
<p class="src5">heading -= 1.0f;<span class="kom">// Natoèení scény</span></p>
<p class="src5">yrot = heading;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_LEFT])<span class="kom">// ©ipka doleva</span></p>
<p class="src4">{</p>
<p class="src5">heading += 1.0f;<span class="kom">// Natoèení scény</span></p>
<p class="src5">yrot = heading;</p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_PRIOR])<span class="kom">// Page Up</span></p>
<p class="src4">{</p>
<p class="src5">lookupdown-= 1.0f;<span class="kom">// Natoèení scény</span></p>
<p class="src4">}</p>
<p class="src"></p>
<p class="src4">if (keys[VK_NEXT])<span class="kom">// Page Down</span></p>
<p class="src4">{</p>
<p class="src5">lookupdown+= 1.0f;<span class="kom">// Natoèení scény</span></p>
<p class="src4">}</p>

<p>Vytvoøili jsme první 3D svìt. Nevypadá sice jako v Quake-ovi, ale my také nejsme Carmack nebo Abrash. Zkuste tlaèítka F - texturový filtr a B - blending. PgUp/PgDown nachýlí kameru nahoru/dolù. Pohyb ¹ipkami vás doufám napadne.</p>

<p>Teï asi pøemý¹líte co dál. Mo¾ná pou¾ijete tento kód na plnohodnotný 3D engine, mìli byste být schopni ho vytvoøit. Pravdìpodobnì budete mít ve høe více ne¾ jeden sektor, zvlá¹tì pøi pou¾ití vchodù.</p>

<p>Tato implementace kódu umo¾òuje nahrávání mnohonásobných sektorù a má zpìtné vykreslování /backface culling/ (nekreslí polygony od kamery). Hodnì ¹tìstí v dal¹ích pokusech.</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Jiøí Rajský - RAJSOFT junior <?VypisEmail('predator.jr@seznam.cz');?><br />
kompletnì pøepsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<p>Pozn.: Tuto lekci nepsal NeHe, ale Lionel Brits. Jak sám autor uvádí, je to jeho první tutoriál - a bohu¾el bylo to vidìt. Pokud se podíváte do anglické verze, tak zjistíte, ¾e bez zdrojových kódù nemáte absolutní ¹anci nìco pochopit. Nìkdy je dokonce velmi tì¾ké identifikovat, která èást kódu patøí ke které funkci. Aby byl text krat¹í pou¾íval vynechávky (nìkdy i u hodnì dùle¾itého kódu - tøeba naèítání pozic ze souboru), ap. Pøeklad Jiøího Rajského byl, dá se øíct, pøesný a to v tomto pøípadì, byla mo¾ná chyba. Proto jsem se rozhodl vìt¹í èást lekce pøepsat. Vím, ¾e ani teï to není nijak zvlá¹» slavné, ale sna¾il jsem se. Kód jsem samozøejmì neupravoval (i kdy¾ by si to také zaslou¾il).</p>

<p><b>Chyby v kódu:</b> Kdy¾ jsem pøepisoval tuto lekci, musel jsem ji pochopit ze zdrojových kódù a pøi tom jsem na¹el nìkolik chyb. Je mi to tak trochu blbý, proto¾e bych kód asi sám nedokázal napsat, ale na druhou stranu byste o tom mìli vìdìt.</p>

<p>Zbyteèná deklarace promìnné z. Tuto promìnnou autor pravdìpodobnì pou¾íval ze zaèátku a pak ji nahradil jinou. Svìdèí o tom i dvojité testování PageUp/PageDown (do lekce nevypisováno). Nikde jinde ji nenajdete.</p>

<p>Neuvolnìní dynamicky alokované pamìti. Ve funkci SetupWorld() jsme pomocí operátoru new alokovali pamì» pro trojúhelníky. Nikdy v programu, ale není její uvolnìní. I kdy¾ by mìl operaèní systém po skonèení programu ru¹it v¹echny systémové zdroje, nelze se na to spoléhat. Tuto chybu odstraníte napøíklad takto:</p>

<p class="src0"><span class="kom">// Pøidat na konec funkce KillGLWindow()</span></p>
<p class="src1">delete [] sector1.triangle;<span class="kom">// Uvolnìní dynamicky alokované pamìti</span></p>
<p class="src"></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson10.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson10_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/c_sharp/lesson10.zip">C#</a> kód této lekce. ( <a href="mailto:bholley@unlnotes.unl.edu">Brian Holley</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson10.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cygwin/lesson10.tar.gz">Cygwin</a> kód této lekce. ( <a href="mailto:stephan@lazyfellow.com">Stephan Ferraro</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson10.zip">Delphi</a> kód této lekce. ( <a href="mailto:marca@stack.nl">Marc Aarts</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson10.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson10.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/gameglut/lesson10.zip">Game GLUT</a> kód této lekce. ( <a href="mailto:milix_gr@hotmail.com">Milikas Anastasios</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/irix/lesson10.zip">Irix</a> kód této lekce. ( <a href="mailto:rpf1@york.ac.uk">Rob Fletcher</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/java/lesson10.zip">Java</a> kód této lekce. ( <a href="mailto:jeff@consunet.com.au">Jeff Kirby</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jedisdl/lesson10.zip">Jedi-SDL</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson10.jar">JoGL</a> kód této lekce. ( <a href="mailto:ncb000gt65@hotmail.com">Nicholas Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson10.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson10.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:ulmont@bellsouth.net">Richard Campbell</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson10.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:Mihael.Vrbanec@stud.uni-karlsruhe.de">Mihael Vrbanec</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxsdl/lesson10.tar.gz">Linux/SDL</a> kód této lekce. ( <a href="mailto:leggett@eecs.tulane.edu">Ti Leggett</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/mac/lesson10.sit">Mac OS</a> kód této lekce. ( <a href="mailto:asp@usc.edu">Anthony Parker</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson10.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/masm/lesson10.zip">MASM</a> kód této lekce. ( <a href="mailto:scalp@bigfoot.com">Nico (Scalp)</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/openil/lesson10.zip">Visual C++ / OpenIL</a> kód této lekce. ( <a href="mailto:doomwiz@ticnet.com">Denton Woods</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/pbasic/lesson10.zip">Power Basic</a> kód této lekce. ( <a href="mailto:anguslaw@net.ntl.com">Angus Law</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson10-2.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:jcapellman@hotmail.com">Jarred Capellman</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vb/lesson10.zip">Visual Basic</a> kód této lekce. ( <a href="mailto:rosscogl@email.com">Ross Dawson</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vfortran/lesson10.zip">Visual Fortran</a> kód této lekce. ( <a href="mailto:Jean-Philippe.Perois@wanadoo.fr">Jean-Philippe Perois</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson10.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(10);?>
<?FceNeHeOkolniLekce(10);?>

<?
include 'p_end.php';
?>
