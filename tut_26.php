<?
$g_title = 'CZ NeHe OpenGL - Lekce 26 - Odrazy a jejich oøezávání za pou¾ití stencil bufferu';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(26);?>

<h1>Lekce 26 - Odrazy a jejich oøezávání za pou¾ití stencil bufferu</h1>

<p class="nadpis_clanku">Tutoriál demonstruje extrémnì realistické odrazy za pou¾ití stencil bufferu a jejich oøezávání, aby &quot;nevystoupily&quot; ze zrcadla. Je mnohem více pokrokový ne¾ pøedchozí lekce, tak¾e pøed zaèátkem ètení doporuèuji men¹í opakování. Odrazy objektù nebudou vidìt nad zrcadlem nebo na druhé stranì zdi a budou mít barevný nádech zrcadla - skuteèné odrazy.</p>

<p><b>Dùle¾ité:</b> Proto¾e grafické karty Voodoo 1, 2 a nìkteré jiné nepodporují stencil buffer, nebude na nich tento tutoriál fungovat. Pokud si nejste jistí, ¾e va¹e karta stencil buffer podporuje, stáhnìte si zdrojový kód a zkuste jej spustit. Kromì toho budete také potøebovat procesor a grafickou kartu se slu¹ným výkonem. Na mé GeForce 1 obèas vidím malé zpomalení. Demo bì¾í nejlépe v 32 bitových barvách.</p>

<p>První èást kódu je celkem standardní.</p>

<p class="src0">#include &lt;windows.h&gt;<span class="kom">// Hlavièkový soubor pro Windows</span></p>
<p class="src0">#include &lt;stdio.h&gt;<span class="kom">// Hlavièkový soubor pro standardní vstup/výstup</span></p>
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

<p>Nastavíme pole pro definici osvìtlení. Okolní svìtlo bude 70% bílé. Difúzní svìtlo nastavuje rozptyl osvìtlení (mno¾ství svìtla rovnomìrnì odrá¾ené na plochách objektù). V tomto pøípadì odrá¾íme plnou intenzitou. Poslední je pozice. Pokud bychom ho mohli spatøit, plulo by v pravém horním rohu monitoru.</p>

<p class="src0"><span class="kom">// Parametry svìtla</span></p>
<p class="src0">static GLfloat LightAmb[] = {0.7f, 0.7f, 0.7f, 1.0f};<span class="kom">// Okolní</span></p>
<p class="src0">static GLfloat LightDif[] = {1.0f, 1.0f, 1.0f, 1.0f};<span class="kom">// Rozptýlené</span></p>
<p class="src0">static GLfloat LightPos[] = {4.0f, 4.0f, 6.0f, 1.0f};<span class="kom">// Pozice</span></p>

<p>Ukazatel q je pro quadratic koule (plá¾ový míè). Xrot a yrot ukládají hodnoty natoèení míèe, xrotspeed a yrotspeed definují rychlost rotace. Zoom pou¾íváme pro pøibli¾ování a oddalování scény a height je vý¹ka balónu nad podlahou. Pole texture[] u¾ standardnì ukládá textury.</p>

<p class="src0">GLUquadricObj *q;<span class="kom">// Quadratic pro kreslení koule (míèe)</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot = 0.0f;<span class="kom">// X rotace</span></p>
<p class="src0">GLfloat yrot = 0.0f;<span class="kom">// Y rotace</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrotspeed = 0.0f;<span class="kom">// Rychlost x rotace</span></p>
<p class="src0">GLfloat yrotspeed = 0.0f;<span class="kom">// Rychlost y rotace</span></p>
<p class="src"></p>
<p class="src0">GLfloat zoom = -7.0f;<span class="kom">// Hloubka v obrazovce</span></p>
<p class="src0">GLfloat height = 2.0f;<span class="kom">// Vý¹ka míèe nad scénou</span></p>
<p class="src"></p>
<p class="src0">GLuint texture[3];<span class="kom">// 3 textury</span></p>

<p>Vytváøení lineárnì filtrovaných textur z bitmap je standardní, v pøedchozích lekcích jsme jej pou¾ívali velice èasto, tak¾e ho sem nebudu opisovat. Na obrázcích vidíte texturu míèe, podlahy a svìtla odrá¾eného od míèe.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_26_tex_mic.jpg" width="128" height="128" alt="Textura míèe" />
<img src="images/nehe_tut/tut_26_tex_podlaha.jpg" width="128" height="128" alt="Textura podlahy" />
<img src="images/nehe_tut/tut_26_tex_svetlo.jpg" width="128" height="128" alt="Textura svìtla odrá¾eného od míèe" />
</div>

<p>Inicializace OpenGL.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Nastavení OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!LoadGLTextures())<span class="kom">// Loading textur</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// Ukonèí program</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Vyhlazené stínování</span></p>
<p class="src1">glClearColor(0.2f, 0.5f, 1.0f, 1.0f);<span class="kom">// Svìtle modré pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastavení hloubkového bufferu</span></p>

<p>Pøíkaz glClearStencil() definuje chování funkce glClear() pøi mazání stencil bufferu. V tomto pøípadì ho budeme vyplòovat nulami.</p>

<p class="src1">glClearStencil(0);<span class="kom">// Nastavení mazání stencil bufferu</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí testování hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testování hloubky</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivní korekce</span></p>
<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Mapování textur</span></p>

<p>Nastavíme svìtla. Pro okolní pou¾ijeme hodnoty z pole LightAmb[], rozptylové svìtlo definujeme pomocí LightDif[] a pozici z LightPos[]. Nakonec povolíme svìtla. Pokud bychom dále v kódu chtìli vypnout v¹echna svìtla, pou¾ili bychom glDisable(GL_LIGHTING), ale pøi vypínání jenom jednoho postaèí pouze glDisable(GL_LIGHT(0a¾7)). GL_LIGHTING v parametru zakazuje globálnì v¹echna svìtla.</p>

<p class="src1">glLightfv(GL_LIGHT0, GL_AMBIENT, LightAmb);<span class="kom">// Okolní</span></p>
<p class="src1">glLightfv(GL_LIGHT0, GL_DIFFUSE, LightDif);<span class="kom">// Rozptylové</span></p>
<p class="src1">glLightfv(GL_LIGHT0, GL_POSITION, LightPos);<span class="kom">// Pozice</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHT0);<span class="kom">// Povolí svìtlo 0</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Povolí svìtla</span></p>

<p>Dále vytvoøíme a nastavíme objekt quadraticu. Vygenerujeme mu normály pro svìtlo a texturové koordináty, jinak by mìl ploché stínování a ne¹ly by na nìj namapovat textury.</p>

<p class="src1">q = gluNewQuadric();<span class="kom">// Nový quadratic</span></p>
<p class="src"></p>
<p class="src1">gluQuadricNormals(q, GL_SMOOTH);<span class="kom">// Normály pro svìtlo</span></p>
<p class="src1">gluQuadricTexture(q, GL_TRUE);<span class="kom">// Texturové koordináty</span></p>

<p>Nastavíme mapování textur na vykreslované objekty a to tak, aby pøi natáèení míèe byla viditelná stále stejná èást textury. Zatím ho nezapínáme.</p>

<p class="src1">glTexGeni(GL_S, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);<span class="kom">// Automatické mapování textur</span></p>
<p class="src1">glTexGeni(GL_T, GL_TEXTURE_GEN_MODE, GL_SPHERE_MAP);<span class="kom">// Automatické mapování textur</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace v poøádku</span></p>
<p class="src0">}</p>

<p>Následující funkci budeme volat pro vykreslení plá¾ového míèe. Bude jím quadraticová koule s nalepenou texturou. Nastavíme barvu na bílou, aby se textura nezabarvovala, poté zvolíme texturu a vykreslíme kouli o polomìru 0.35 jednotek, s 32 rovnobì¾kami a 16 poledníky.</p>

<p class="src0">void DrawObject()<span class="kom">// Vykreslí plá¾ový míè</span></p>
<p class="src0">{</p>
<p class="src1">glColor3f(1.0f, 1.0f, 1.0f);<span class="kom">// Bílá barva</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[1]);<span class="kom">// Zvolí texturu míèe</span></p>
<p class="src1">gluSphere(q, 0.35f, 32, 16);<span class="kom">// Nakreslí kouli</span></p>

<p>Po vykreslení první koule vybereme texturu svìtla, nastavíme opìt bílou barvu, ale tentokrát s 40% alfou. Povolíme blending, nastavíme jeho funkci zalo¾enou na zdrojové alfa hodnotì, zapneme kulové mapování textur a nakreslíme stejnou kouli jako pøed chvílí. Výsledkem je simulované odrá¾ení svìtla od míèe, ale vlastnì se jedná jen o svìtlé body namapované na plá¾ový míè. Proto¾e je povoleno kulové mapování, textura je v¾dy natoèena k pozorovateli stejnou èástí bez ohledu na natoèení míèe. Je také zapnutý blending tak¾e nová textura nepøebije starou (jednoduchá forma multitexturingu).</p>

<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[2]);<span class="kom">// Zvolí texturu svìtla</span></p>
<p class="src1">glColor4f(1.0f, 1.0f, 1.0f, 0.4f);<span class="kom">// Bílá barva s 40% alfou</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE);<span class="kom">// Mód blendingu</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_TEXTURE_GEN_S);<span class="kom">// Zapne kulové mapování</span></p>
<p class="src1">glEnable(GL_TEXTURE_GEN_T);<span class="kom">// Zapne kulové mapování</span></p>
<p class="src"></p>
<p class="src1">gluSphere(q, 0.35f, 32, 16);<span class="kom">// Stejná koule jako pøed chvílí</span></p>

<p>Vypneme kulové mapování a blending.</p>

<p class="src1">glDisable(GL_TEXTURE_GEN_S);<span class="kom">// Vypne kulové mapování</span></p>
<p class="src1">glDisable(GL_TEXTURE_GEN_T);<span class="kom">// Vypne kulové mapování</span></p>
<p class="src"></p>
<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vepne blending</span></p>
<p class="src0">}</p>

<p>Následující funkce kreslí podlahu, nad kterou se míè vzná¹í. Vybereme texturu podlahy a na ose z vykreslíme ètverec s jednoduchou texturou.</p>

<p class="src0">void DrawFloor()<span class="kom">// Vykreslí podlahu</span></p>
<p class="src0">{</p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Zvolí texturu podlahy</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Kreslení obdélníkù</span></p>
<p class="src2">glNormal3f(0.0, 1.0, 0.0);<span class="kom">// Normálová vektor míøí vzhùru</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(0.0f, 1.0f);<span class="kom">// Levý dolní bod textury</span></p>
<p class="src2">glVertex3f(-2.0, 0.0, 2.0);<span class="kom">// Levý dolní bod podlahy</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(0.0f, 0.0f);<span class="kom">// Levý horní bod textury</span></p>
<p class="src2">glVertex3f(-2.0, 0.0,-2.0);<span class="kom">// Levý horní bod podlahy</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(1.0f, 0.0f);<span class="kom">// Pravý horní bod textury</span></p>
<p class="src2">glVertex3f( 2.0, 0.0,-2.0);<span class="kom">// Pravý horní bod podlahy</span></p>
<p class="src"></p>
<p class="src2">glTexCoord2f(1.0f, 1.0f);<span class="kom">// Pravý dolní bod textury</span></p>
<p class="src2">glVertex3f( 2.0, 0.0, 2.0);<span class="kom">// Pravý dolní bod podlahy</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src0">}</p>

<p>Na tomto místì zkombinujeme v¹echny objekty a obrázky tak, abychom vytvoøili výslednou scénu. Zaèneme mazáním obrazovky (GL_COLOR_BUFFER_BIT) na výchozí modrou barvu, hloubkového bufferu (GL_DEPTH_BUFFER_BIT) a stencil bufferu (GL_STENCIL_BUFFER_BIT). Pøi èi¹tìní stencil bufferu ho vyplòujeme nulami.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslí výslednou scénu</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Sma¾e obrazovku, hloubkový buffer a stencil buffer</span></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT | GL_STENCIL_BUFFER_BIT);</p>

<p>Nadefinujeme rovnici oøezávací plochy (clipping plane equation). Bude pou¾ita pøi vykreslení odra¾eného míèe. Hodnota na ose y je záporná, to znamená, ¾e uvidíme pixely jen pokud jsou kresleny pod podlahou nebo na záporné èásti osy y. Pøi pou¾ití této rovnice se nezobrazí nic, co vykreslíme nad podlahou (odraz nemù¾e vystoupit ze zrcadla). Více pozdìji.</p>

<p class="src1"><span class="kom">// Rovnice oøezávací plochy</span></p>
<p class="src1">double eqr[] = { 0.0f, -1.0f, 0.0f, 0.0f };<span class="kom">// Pou¾ito pro odra¾ený objekt</span></p>

<p>V¹emu, co bylo doposud probráno v této lekci byste mìli rozumìt. Teï pøijde nìco &quot;malièko&quot; hor¹ího. Potøebujeme nakreslit odraz míèe a to tak, aby se na obrazovce zobrazoval jenom na tìch pixelech, kde je podlaha. K tomu vyu¾ijeme stencil buffer. Pomocí funkce glClear() jsme ho vyplnili samými nulami. Rùznými nastaveními, které si vysvìtlíme dále, docílíme toho, ¾e se podlaha sice nezobrazí na obrazovce, ale na místech, kde se mìla vykreslit se stencil buffer nastaví do jednièky. Pro pochopení si pøedstavte, ¾e je to obrazovka v pamìti, její¾ pixely jsou rovny jednièce, pokud se na nich objekt vykresluje a nule (nezmìnìný) pokud ne. Na místa, kde je stencil buffer v jednièce vykreslíme plochý odraz míèe, ale ne do stencil bufferu - viditelnì na obrazovku. Odraz vlastnì mù¾eme vykreslit i kdekoli jinde, ale pouze tady bude vidìt. Nakonec klasickým zpùsobem vykreslíme v¹echno ostatní. To je asi v¹echno, co byste mìli o stencil bufferu prozatím vìdìt.</p>

<p>Nyní u¾ konkrétnì ke kódu. Resetujeme matici modelview a potom pøesuneme scénu o ¹est jednotek dolù a o zoom do hloubky. Nejlep¹í vysvìtlení pro translaci dolù bude na pøíkladì. Vezmìte si list papíru a umístìte jej rovnobì¾nì se zemí do úrovnì oèí. Neuvidíte nic víc ne¾ tenkou linku. Posunete-li jím o malièko dolù, spatøíte celou plochu, proto¾e se na nìj budete dívat více ze shora namísto pøímo na okraj. Roz¹íøil se zorný úhel.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f, -0.6f, zoom);<span class="kom">// Zoom a vyvý¹ení kamery nad podlahu</span></p>

<p>Novým pøíkazem definujeme barevnou masku pro vykreslované barvy. Funkci se pøedávají ètyøi parametry reprezentující èervenou, zelenou, modrou a alfu. Pokud napøíklad èervenou slo¾ku nastavíme na jedna (GL_TRUE) a v¹echny ostatní na nulu (GL_FALSE), tak se bude moci zobrazit pouze èervená barva. V opaèném pøípadì (0,1,1,1) se budou zobrazovat v¹echny barvy mimo èervenou. Asi tu¹íte, ¾e jsou barvy implicitnì nastaveny tak, aby se v¹echny zobrazovaly. No, a proto¾e v tuto chvíli nechceme nic zobrazovat zaká¾eme v¹echny barvy.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_26_color_1111.jpg" width="63" height="63" alt="glColorMask(1,1,1,1);" />
<img src="images/nehe_tut/tut_26_color_1000.jpg" width="63" height="63" alt="glColorMask(1,0,0,0);" />
<img src="images/nehe_tut/tut_26_color_0111.jpg" width="63" height="63" alt="glColorMask(0,1,1,1);" />
</div>

<p class="src1">glColorMask(0,0,0,0);<span class="kom">// Nastaví masku barev, aby se nic nezobrazilo</span></p>

<p>Zaèínáme pracovat se stencil bufferem. Napøed potøebujeme získat obraz podlahy vyjádøený jednièkami (viz. vý¹e). Zaèneme zapnutím stencilového testování (stencil testing). Jakmile je povoleno jsme schopni modifikovat stencil buffer.</p>

<p class="src1">glEnable(GL_STENCIL_TEST);<span class="kom">// Zapne stencil buffer pro pamì»ový obraz podlahy</span></p>

<p>Následující pøíkaz je mo¾ná tì¾ko pochopitelný, ale urèitì se velice tì¾ko vysvìtluje. Funkce glStencilFunc(GL_ALWAYS,1,1) oznamuje OpenGL, jaký typ testu chceme pou¾ít na ka¾dý pixel pøi jeho vykreslování. GL_ALWAYS zaruèí, ¾e test probìhne v¾dy. Druhý parametr je referenèní hodnotou a tøetí parametr je maska. U ka¾dého pixelu se hodnota masky ANDuje s referenèní hodnotou a výsledek se ulo¾í do stencil bufferu. V na¹em pøípadì se do nìj umístí poka¾dé jednièka (reference &amp; maska = 1 &amp; 1 = 1). Nyní víme, ¾e na souøadnicích pixelu na obrazovce, kde by se vykreslil objekt, bude ve stencil bufferu jednièka.</p>

<p>Pozn.: Stencilové testy jsou vykonávány na pixelech poka¾dé, kdy¾ se objekt vykresluje na scénu. Referenèní hodnota ANDovaná s hodnotou masky se testuje proti aktuální hodnotì ve stencil bufferu ANDované s hodnotou masky.</p>

<p class="src1">glStencilFunc(GL_ALWAYS, 1, 1);<span class="kom">// Poka¾dé probìhne, reference, maska</span></p>

<p>GlStencilOp() zpracuje tøi rozdílné po¾adavky zalo¾ené na stencilových funkcích, které jsme se rozhodli pou¾ít. První parametr øíká OpenGL, co má udìlat pokud test neuspìje. Proto¾e je nastaven na GL_KEEP nechá hodnotu stencil bufferu tak, jak právì je. Nicménì test uspìje v¾dy, proto¾e máme funkci nastavenu na GL_ALWAYS. Druhý parametr urèuje co dìlat, pokud stencil test probìhne, ale hloubkový test bude neúspì¹ný. Tato situace by nastala napøíklad, kdy¾ by se objekt vykreslil za jiným objektem a hloubkový test by nepovolil jeho vykreslení. Opìt mù¾e být ignorován, proto¾e hned následujícím pøíkazem hloubkové testy vypínáme. Tøetí parametr je pro nás dùle¾itý. Definuje, co se má vykonat, pokud test uspìje (uspìje v¾dycky). V na¹em pøípadì OpenGL nahradí nulu ve stencil bufferu na jednièku (referenèní hodnota ANDovaná s maskou = 1).</p>

<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_REPLACE);<span class="kom">// Vykreslením nastavíme konkrétní bit ve stencil bufferu na 1</span></p>

<p>Po nastavení stencilových testù vypneme hloubkové testy a zavoláme funkci pro vykreslení podlahy.</p>

<p class="src1">glDisable(GL_DEPTH_TEST);<span class="kom">// Vypne testování hloubky</span></p>
<p class="src"></p>
<p class="src1">DrawFloor();<span class="kom">// Vykreslí podlahu (do stencil bufferu ne na scénu)</span></p>

<p>Tak¾e teï máme ve stencil bufferu neviditelnou masku podlahy. Tak dlouho, jak bude stencilové testování zapnuté, budeme moci zobrazovat pixely pouze tam, kde je stencil buffer v jednièce (tam kde byla vykreslena podlaha). Zapneme hloubkové testování a nastavíme masku barev zpìt do jednièek. To znamená, ¾e se od teï v¹e vykreslované opravdu zobrazí.</p>

<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>
<p class="src1">glColorMask(1, 1, 1, 1);<span class="kom">// Povolí zobrazování barev</span></p>

<p>Namísto u¾ití GL_ALWAYS pro stencilovou funkci, pou¾ijeme GL_EQUAL. Reference i maska zùstávají v jednièce. Pro stencilové operace nastavíme v¹echny parametry na GL_KEEP. Vykreslované pixely se zobrazí na obrazovku POUZE tehdy, kdy¾ je na jejich souøadnicích hodnota stencilu v jednièce (reference ANDovaná s maskou (1), které jsou rovny (GL_EQUAL) hodnotì stencil bufferu ANDované s maskou (také 1)). GL_KEEP zajistí, ¾e se hodnoty ve stencil bufferu nebudou modifikovat.</p>

<p class="src1">glStencilFunc(GL_EQUAL, 1, 1);<span class="kom">// Zobrazí se pouze pixely na jednièkách ve stencil bufferu (podlaha)</span></p>
<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_KEEP);<span class="kom">// Nemìnit obsah stencil bufferu</span></p>

<p>Zapneme oøezávací plochu zrcadla, která je definována rovnicí ulo¾enou v poli eqr[]. Umo¾òuje, aby byl odraz objektu vykreslen pouze smìrem dolù od podlahy (v podlaze). Touto cestou nebude moci odraz míèe vystoupit do &quot;reálného svìta&quot;. Pokud nechápete, co je tímto mínìno zakomentáøujte v kódu øádek glEnable(GL_CLIP_PLANE0), zkompilujte program a zkuste projít reálným míèem skrz podlahu. Pokud clipping nebude zapnutý uvidíte, jak pøi vstupu míèe do podlahy jeho odraz vystoupí nahoru nad podlahu. V¹e vidíte na obrázku. Mimochodem, v¹imnìte si, ¾e vystoupiv¹í obraz je poøád vidìt jen tam, kde je ve stencil bufferu obraz podlahy.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_26_clip.jpg" width="80" height="80" alt="Clipping zapnutý" />
<img src="images/nehe_tut/tut_26_no_clip.jpg" width="80" height="80" alt="Clipping vypnutý" />
</div>

<p>Po zapnutí oøezávací plochy 0 (obyèejnì jich mù¾e být 0 a¾ 5) jí pøedáme parametry rovnice ulo¾ené v eqr[].</p>

<p class="src1">glEnable(GL_CLIP_PLANE0);<span class="kom">// Zapne oøezávací testy pro odraz</span></p>
<p class="src1">glClipPlane(GL_CLIP_PLANE0, eqr);<span class="kom">// Rovnice oøezávací roviny</span></p>

<p>Zálohujeme aktuální stav matice, aby ji zmìny trvale neovlivnily. Zadáním mínus jednièky do glScalef() obrátíme smìr osy y. Do této chvíle procházela zezdola nahoru, nyní naopak. Stejný efekt by mìla rotace o 180°. V¹e je teï invertované jako v zrcadle. Pokud nìco vykreslíme nahoøe, zobrazí se to dole (zrcadlo je vodorovnì ne svisle), rotujeme-li po smìru, objekt se otoèí proti smìru hodinových ruèièek a podobnì. Tento stav se mù¾e zru¹it buï opìtovným voláním glScalef(), které provede opìtovnou inverzi nebo POPnutím matice.</p>

<p class="src1">glPushMatrix();<span class="kom">// Záloha matice</span></p>
<p class="src2">glScalef(1.0f, -1.0f, 1.0f);<span class="kom">// Zrcadlení smìru osy y</span></p>

<p>Nadefinujeme pozici svìtla podle pole LightPos[]. Na reálný míè svítí z pravé horní strany, ale proto¾e se i poloha svìtla zrcadlí, tak na odraz bude záøit zezdola.</p>

<p class="src2">glLightfv(GL_LIGHT0, GL_POSITION, LightPos);<span class="kom">// Umístìní svìtla</span></p>

<p>Pøesuneme se na ose y nahoru nebo dolù v závislosti na promìnné height. Opìt je translace zrcadlena, tak¾e pokud se pøesuneme o pìt jednotek nad podlahu budeme vlastnì o pìt jednotek pod podlahou. Stejným zpùsobem pracují i rotace. Nakonec nakreslíme objekt plá¾ového míèe a POPneme matici. Tím zru¹íme v¹echny zmìny od volání glPushMatrix().</p>

<p class="src2">glTranslatef(0.0f, height, 0.0f);<span class="kom">// Umístìní míèe</span></p>
<p class="src2">glRotatef(xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src2">glRotatef(yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src2">DrawObject();<span class="kom">// Vykreslí míè (odraz)</span></p>
<p class="src"></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoví matici</span></p>

<p>Vypneme oøezávací testy, tak¾e se budou zobrazovat i objekty nad podlahou. Také vypneme stencil testy, abychom mohli vykreslovat i jinam ne¾ na pixely, které byly modifikovány podlahou.</p>

<p class="src1">glDisable(GL_CLIP_PLANE0);<span class="kom">// Vypne oøezávací rovinu</span></p>
<p class="src1">glDisable(GL_STENCIL_TEST);<span class="kom">// U¾ nebudeme potøebovat stencil testy</span></p>

<p>Pøipravíme program na vykreslení podlahy. Opìt umístíme svìtlo, ale tak, aby u¾ jeho pozice nebyla zrcadlena. Osa y je sice u¾ v poøádku, ale svìtlo je stále vpravo dole.</p>

<p class="src1">glLightfv(GL_LIGHT0, GL_POSITION, LightPos);<span class="kom">// Umístìní svìtla</span></p>

<p>Zapneme blending, vypneme svìtla (globálnì) a nastavíme 80% prùhlednost bez zmìny barev textur (bílá nepøidává barevný nádech). Mód blendingu je nastaven pomocí glBlendFunc(). Poté vykreslíme èásteènì prùhlednou podlahu. Asi nechápete, proè jsme napøed kreslili odraz a a¾ poté zrcadlo. Je to proto, ¾e chceme, aby byl odraz míèe smíchán s barvami podlahy. Pokud se díváte do modrého zrcadla, tak také oèekáváte trochu namodralý odraz. Vykreslení míèe napøed zpùsobí zabarvení podlahou. Efekt je více reálný.</p>

<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending, jinak by se odraz míèe nezobrazil</span></p>
<p class="src1">glDisable(GL_LIGHTING);<span class="kom">// Kvùli blendingu vypneme svìtla</span></p>
<p class="src"></p>
<p class="src1">glColor4f(1.0f, 1.0f, 1.0f, 0.8f);<span class="kom">// Bílá barva s 80% prùhledností</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);<span class="kom">// Funkce na bázi alfy zdroje a jedna mínus alfy cíle</span></p>
<p class="src"></p>
<p class="src1">DrawFloor();<span class="kom">// Vykreslí podlahu</span></p>

<p>A koneènì vykreslíme reálný míè. Napøed ale zapneme svìtla (pozice u¾ je nastavená). Kdybychom nevypnuli blending, míè by pøi prùchodu podlahou vypadal jako odraz. To nechceme.</p>

<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne svìtla</span></p>
<p class="src1">glDisable(GL_BLEND);<span class="kom">// Vypne blending</span></p>

<p>Tento míè u¾ narozdíl od jeho odrazu neoøezáváme. Kdybychom pou¾ívali clipping, nezobrazil by se pod podlahou. Docílili bychom toho definováním hodnoty +1.0f na ose y u rovnice oøezávací roviny. Pro toto demo není ¾ádný dùvod, abychom míè nemohli vidìt pod podlahou. V¹echny translace i rotace zùstávají stejné jako minule s tím rozdílem, ¾e nyní u¾ jde osa y klasickým smìrem. Kdy¾ posuneme reálný míè dolù, odraz jde nahoru a naopak.</p>

<p class="src1">glTranslatef(0.0f, height, 0.0f);<span class="kom">// Umístìní míèe</span></p>
<p class="src1">glRotatef(xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src1">DrawObject();<span class="kom">// Vykreslí míè</span></p>

<p>Zvìt¹íme hodnoty natoèení míèe a jeho odrazu o rychlost rotací. Pøed návratem z funkce zavoláme glFlush(), které poèká na ukonèení renderingu. Prevence mihotání na pomalej¹ích grafických kartách.</p>

<p class="src1">xrot += xrotspeed;<span class="kom">// Zvìt¹í natoèení</span></p>
<p class="src1">yrot += yrotspeed;<span class="kom">// Zvìt¹í natoèení</span></p>
<p class="src"></p>
<p class="src1">glFlush();<span class="kom">// Vyprázdní pipeline</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹echno v poøádku</span></p>
<p class="src0">}</p>

<p>Následující funkce testuje stisk kláves. Voláme ji periodicky v hlavní smyèce WinMain(). ©ipkami ovládáme rychlost rotace míèe, klávesy A a Z pøibli¾ují/oddalují scénu, Page Up s Page Down umo¾òují zmìnit vý¹ku plá¾ového míèe nad podlahou. Klávesa ESC plní stále svoji funkci, ale její umístìní zùstalo ve WinMain().</p>

<p class="src0">void ProcessKeyboard()<span class="kom">// Ovládání klávesnicí</span></p>
<p class="src0">{</p>
<p class="src1">if (keys[VK_RIGHT]) yrotspeed += 0.08f;<span class="kom">// ©ipka vpravo zvý¹í rychlost y rotace</span></p>
<p class="src1">if (keys[VK_LEFT]) yrotspeed -= 0.08f;<span class="kom">// ©ipka vlevo sní¾í rychlost y rotace</span></p>
<p class="src1">if (keys[VK_DOWN]) xrotspeed += 0.08f;<span class="kom">// ©ipka dolù zvý¹í rychlost x rotace</span></p>
<p class="src1">if (keys[VK_UP]) xrotspeed -= 0.08f;<span class="kom">// ©ipka nahoru sní¾í rychlost x rotace</span></p>
<p class="src"></p>
<p class="src1">if (keys['A']) zoom +=0.05f;<span class="kom">// A pøiblí¾í scénu</span></p>
<p class="src1">if (keys['Z']) zoom -=0.05f;<span class="kom">// Z oddálí scénu</span></p>
<p class="src"></p>
<p class="src1">if (keys[VK_PRIOR]) height += 0.03f;<span class="kom">// Page Up zvìt¹í vzdálenost míèe nad podlahou</span></p>
<p class="src1">if (keys[VK_NEXT]) height -= 0.03f;<span class="kom">// Page Down zmen¹í vzdálenost míèe nad podlahou</span></p>
<p class="src0">}</p>

<p>V CreateGLWindow() je úplnì miniaturní zmìna, nicménì by bez ní program nefungoval. Ve struktuøe PIXELFORMATDESCRIPTOR pfd nastavíme èíslo, které vyjadøuje poèet bitù stencil bufferu. Ve v¹ech minulých lekcích jsme ho nepotøebovali, tak¾e mu byla pøiøazena nula. Pøi pou¾ití stencil bufferu MUSÍ být poèet jeho bitù vìt¹í nebo roven jedné! Nám staèí jeden bit.</p>

<p class="src0"><span class="kom">// Uprostøed funkce CreateGLWindow()</span></p>
<p class="src"></p>
<p class="src1">static PIXELFORMATDESCRIPTOR pfd=<span class="kom">// Oznamuje Windows jak chceme v¹e nastavit</span></p>
<p class="src1">{</p>
<p class="src2">sizeof(PIXELFORMATDESCRIPTOR),<span class="kom">// Velikost struktury</span></p>
<p class="src2">1,<span class="kom">// Èíslo verze</span></p>
<p class="src2">PFD_DRAW_TO_WINDOW |<span class="kom">// Podpora okna</span></p>
<p class="src2">PFD_SUPPORT_OPENGL |<span class="kom">// Podpora OpenGL</span></p>
<p class="src2">PFD_DOUBLEBUFFER,<span class="kom">// Podpora double bufferingu</span></p>
<p class="src2">PFD_TYPE_RGBA,<span class="kom">// RGBA formát</span></p>
<p class="src2">bits,<span class="kom">// Barevná hloubka</span></p>
<p class="src2">0, 0, 0, 0, 0, 0,<span class="kom">// Bity barev ignorovány</span></p>
<p class="src2">0,<span class="kom">// ®ádný alfa buffer</span></p>
<p class="src2">0,<span class="kom">// Ignorován shift bit</span></p>
<p class="src2">0,<span class="kom">// ®ádný akumulaèní buffer</span></p>
<p class="src2">0, 0, 0, 0,<span class="kom">// Akumulaèní bity ignorovány</span></p>
<p class="src2">16,<span class="kom">// 16 bitový z-buffer</span></p>
<p class="src2"><span class="warning">1</span>,<span class="kom">// Stencil buffer <span class="warning">(DÙLE®ITÉ)</span></span></p>
<p class="src2">0,<span class="kom">// ®ádný auxiliary buffer</span></p>
<p class="src2">PFD_MAIN_PLANE,<span class="kom">// Hlavní vykreslovací vrstva</span></p>
<p class="src2">0,<span class="kom">// Rezervováno</span></p>
<p class="src2">0, 0, 0<span class="kom">// Maska vrstvy ignorována</span></p>
<p class="src1">};</p>

<p>Jak jsem se zmínil vý¹e, test stisknutí kláves u¾ nebudeme vykonávat pøímo ve WinMain(), ale ve funkci ProcessKeyboard(), kterou voláme hned po vykreslení scény.</p>

<p class="src0"><span class="kom">// Funkce WinMain()</span></p>
<p class="src"></p>
<p class="src5">DrawGLScene();<span class="kom">// Vykreslí scénu</span></p>
<p class="src5">SwapBuffers(hDC);<span class="kom">// Prohodí buffery</span></p>
<p class="src"></p>
<p class="src5">ProcessKeyboard();<span class="kom">// Vstup z klávesnice</span></p>

<p>Doufám, ¾e jste si u¾ili tuto lekci. Vím, ¾e probírané téma nebylo zrovna nejjednodu¹¹í, ale co se dá dìlat? Byl to jeden z nejtì¾¹ích tutoriálù, jak jsem kdy napsal. Pro mì je celkem snadné pochopit, co který øádek dìlá a který pøíkaz se musí pou¾ít, aby vznikl po¾adovaný efekt. Ale sednìte si k poèítaèi a pokuste se to vysvìtlit lidem, kteøí neví, co to je stencil buffer a mo¾ná o nìm dokonce v ¾ivotì nesly¹eli (Pøekl.: Mùj pøípad). Osobnì si myslím, ¾e i kdy¾ mu napoprvé neporozumíte, po druhém pøeètení by mìlo být v¹e jasné...</p>

<p class="autor">napsal: Jeff Molofee - NeHe <?VypisEmail('nehe@connect.ab.ca');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?> &amp; Milan Turek <?VypisEmail('nalim.kerut@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson26.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson26_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson26.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson26.zip">Delphi</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson26.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson26.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson26.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson26.tar.gz">Linux</a> kód této lekce. ( <a href="mailto:grayfox@pobox.sk">Gray Fox</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson26.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson26.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(26);?>
<?FceNeHeOkolniLekce(26);?>

<?
include 'p_end.php';
?>
