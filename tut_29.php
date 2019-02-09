<?
$g_title = 'CZ NeHe OpenGL - Lekce 29 - Blitter, nahrávání .RAW textur';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(29);?>

<h1>Lekce 29 - Blitter, nahrávání .RAW textur</h1>

<p class="nadpis_clanku">V této lekci se nauèíte, jak se nahrávají .RAW obrázky a konvertují se do textur. Dozvíte se také o blitteru, grafické metodì pøená¹ení dat, která umo¾òuje modifikovat textury poté, co u¾ byly nahrány do programu. Mù¾ete jím zkopírovat èást jedné textury do druhé, blendingem je smíchat dohromady a také roztahovat. Malièko upravíme program tak, aby v dobì, kdy není aktivní, vùbec nezatì¾oval procesor.</p>

<p>Blitting... v poèítaèové grafice je toto slovo hodnì pou¾ívané. Oznaèuje se jím zkopírování èásti jedné textury a vlo¾ení do druhé. Pokud programujete ve Win API nebo MFC, jistì jste sly¹eli o funkcích BitBlt() nebo StretchBlt(). Pøesnì toto se pokusíme vytvoøit.</p>

<p>Chcete-li napsat funkci, která implementuje blitting, mìli byste nìco vìdìt o lineární grafické pamìti. Kdy¾ se podíváte na monitor, vidíte spousty bodù reprezentujících nìjaký obrázek, ovládací prvky nebo tøeba kurzor my¹i. V¹e je prostì slo¾eno z matice pixelù. Ale jak ví grafická karta nebo BIOS, jak nakreslit bod napøíklad na souøadnicích [64; 64]? Jednodu¹e! V¹echno, co je na obrazovce není v matici, ale v lineární pamìti (v jednorozmìrném poli). Pozici bodu v pamìti mù¾eme získat následující rovnicí:</p>

<p class="src0"><span class="kom">adresa_v_pamìti = (pozice_y * rozli¹ení_obrazovky_x) + pozice_x</span></p>

<p>Pokud máme rozli¹ení obrazovky 640x480, bude bod [64; 64] umístìn na pamì»ové adrese (64*640) + 64 = 41024. Proto¾e pamì», do které budeme ukládat bitmapy je také lineární, mù¾eme této vlastnosti vyu¾ít pøi pøená¹ení blokù grafických dat. Výslednou adresu je¹tì budeme násobit barevnou hloubkou obrázku, proto¾e nepou¾íváte jedno-bytové pixely (256 barev), ale RGBA obrázky. Pokud jste tento výklad nepochopili, nemá cenu jít dál...</p>

<p>Vytvoøíme strukturu TEXTURE_IMAGE, která bude obsahovat informace o nahrávaném obrázku - ¹íøku, vý¹ku, barevnou hloubku. Pointer data bude ukazovat do dynamické pamìti, kam nahrajeme ze souboru data obrázku.</p>

<p class="src0">typedef struct Texture_Image<span class="kom">// Struktura obrázku</span></p>
<p class="src0">{</p>
<p class="src1">int width;<span class="kom">// ©íøka v pixelech</span></p>
<p class="src1">int height;<span class="kom">// Vý¹ka v pixelech</span></p>
<p class="src1">int format;<span class="kom">// Barevná hloubka v bytech na pixel</span></p>
<p class="src1">unsigned char *data;<span class="kom">// Data obrázku</span></p>
<p class="src0">} TEXTURE_IMAGE;</p>

<p>Dal¹í datový typ je ukazatelem na právì vytvoøenou strukturu. Po nìm následují dvì promìnné t1 a t2. Do nich budeme nahrávat obrázky, které potom blittingem slouèíme do jednoho a vytvoøíme z nìj texturu.</p>

<p class="src0">typedef TEXTURE_IMAGE *P_TEXTURE_IMAGE;<span class="kom">// Datový typ ukazatele na obrázek</span></p>
<p class="src"></p>
<p class="src0">P_TEXTURE_IMAGE t1;<span class="kom">// Dva obrázky</span></p>
<p class="src0">P_TEXTURE_IMAGE t2;</p>
<p class="src"></p>
<p class="src0">GLuint texture[1];<span class="kom">// Jedna textura</span></p>

<p>Rot promìnné urèují úhel rotace výsledného objektu. Nic nového.</p>

<p class="src0">GLfloat xrot;<span class="kom">// X rotace</span></p>
<p class="src0">GLfloat yrot;<span class="kom">// Y rotace</span></p>
<p class="src0">GLfloat zrot;<span class="kom">// Z rotace</span></p>

<p>Funkcí AllocateTextureBuffer(), alokujeme dynamickou pamì» pro obrázek a vrátíme ukazatel. Pøi neúspìchu se vrací NULL. Funkci pøedává program celkem tøi parametry: ¹íøku, vý¹ku a barevnou hloubku v bytech na pixel.</p>

<p class="src0">P_TEXTURE_IMAGE AllocateTextureBuffer(GLint w, GLint h, GLint f)<span class="kom">// Alokuje pamì» pro obrázek</span></p>
<p class="src0">{</p>

<p>Ukazatel na obrázek ti vrátíme na konci funkce volajícímu kódu. Na zaèátku ho inicializujeme na NULL. Promìnné c, pøiøadíme také NULL. Pøedstavuje úlo¾i¹tì nahrávaných dat.</p>

<p class="src1">P_TEXTURE_IMAGE ti = NULL;<span class="kom">// Ukazatel na strukturu obrázku</span></p>
<p class="src1">unsigned char *c = NULL;<span class="kom">// Ukazatel na data obrázku</span></p>

<p>Pomocí standardní funkce malloc() se pokusíme alokovat dynamickou pamì» pro strukturu obrázku. Pokud se operace podaøí, program pokraèuje dále. Pøi jakékoli chybì vrátí malloc() NULL. Vypí¹eme chybovou zprávu a oznámíme volajícímu kódu neúspìch.</p>

<p class="src1">ti = (P_TEXTURE_IMAGE)malloc(sizeof(TEXTURE_IMAGE));<span class="kom">// Alokace pamìti pro strukturu</span></p>
<p class="src"></p>
<p class="src1">if(ti != NULL)<span class="kom">// Podaøila se alokace pamìti?</span></p>
<p class="src1">{</p>

<p>Po úspì¹né alokaci pamìti vyplníme strukturu atributy obrázku. Barevná hloubka není v obvyklém formátu bit na pixel, ale kvùli jednodu¹¹í manipulaci s pamìtí v bytech na pixel.</p>

<p class="src2">ti-&gt;width = w;<span class="kom">// Nastaví atribut ¹íøky</span></p>
<p class="src2">ti-&gt;height = h;<span class="kom">// Nastaví atribut vý¹ky</span></p>
<p class="src2">ti-&gt;format = f;<span class="kom">// Nastaví atribut barevné hloubky</span></p>

<p>Stejným zpùsobem jako pro strukturu alokujeme pamì» i pro data obrázku. Její velikost získáme násobením ¹íøky, vý¹ky a barevné hloubky. Pøi úspìchu nastavíme atribut data struktury na právì získanou dynamickou pamì», neúspìch o¹etøíme stejnì jako minule.</p>

<p class="src2">c = (unsigned char *)malloc(w * h * f);<span class="kom">// Alokace pamìti pro strukturu</span></p>
<p class="src"></p>
<p class="src2">if (c != NULL)<span class="kom">// Podaøila se alokace pamìti?</span></p>
<p class="src2">{</p>
<p class="src3">ti-&gt;data = c;<span class="kom">// Nastaví ukazatel na data</span></p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Alokace pamìti pro data se nepodaøila</span></p>
<p class="src2">{</p>
<p class="src3">MessageBox(NULL, &quot;Could Not Allocate Memory For A Texture Buffer&quot;, &quot;BUFFER ERROR&quot;, MB_OK | MB_ICONINFORMATION);</p>

<p>Pøekl.: Tady by správnì mìla funkce vrátit namísto NULL promìnnou ti nebo je¹tì lépe pøed opu¹tìním funkce dealokovat dynamickou pamì» struktury ti. Bez vrácení ukazatele nemù¾eme z venku pamì» uvolnit. Pokud operaèní systém nepracuje tak, jak má (Toto není nará¾ka na MS Windows :-), èili po skonèení neuvolní poskytne zdroje programu, vznikají pamì»ové úniky.</p>

<p class="src3"><span class="kom">// Uvolnìní pamìti struktury (Pøekl.)</span></p>
<p class="src3"><span class="kom">// free(ti);</span></p>
<p class="src3"><span class="kom">// ti = NULL;</span></p>
<p class="src"></p>
<p class="src3">return NULL;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">else<span class="kom">// Alokace pamìti pro strukturu se nepodaøila</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Could Not Allocate An Image Structure&quot;,&quot;IMAGE STRUCTURE ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">return NULL;</p>
<p class="src1">}</p>

<p>Pokud dosud nebyly ¾ádné problémy, vrátíme ukazatel na strukturu ti.</p>

<p class="src1">return ti;<span class="kom">// Vrátí ukazatel na dynamickou pamì»</span></p>
<p class="src0">}</p>

<p>Ve funkci DeallocateTexture() dìláme pravý opak - uvolòujeme pamì» obrázku, na kterou ukazuje pøedaný parametr t.</p>

<p class="src0">void DeallocateTexture(P_TEXTURE_IMAGE t)<span class="kom">// Uvolní dynamicky alokovanou pamì» obrázku</span></p>
<p class="src0">{</p>
<p class="src1">if(t)<span class="kom">// Pokud struktura obrázku existuje</span></p>
<p class="src1">{</p>
<p class="src2">if(t-&gt;data)<span class="kom">// Pokud existují data obrázku</span></p>
<p class="src2">{</p>
<p class="src3">free(t-&gt;data);<span class="kom">// Uvolní data obrázku</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2">free(t);<span class="kom">// Uvolní strukturu obrázku</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>V¹echno u¾ máme pøipravené, zbývá jenom nahrát .RAW obrázek. RAW formát je nejjednodu¹¹í a nejrychlej¹í zpùsob, jak nahrát do programu texturu (samozøejmì kromì funkce auxDIBImageLoad()). Proè je to tak jednoduché? Proto¾e .RAW formát obsahuje pouze samotná data bitmapy bez hlavièek nebo nìèeho dal¹ího. Jediné, co musíme udìlat, je otevøít soubor a naèíst data tak, jak jsou. Témìø... bohu¾el tento formát má dvì nevýhody. První je to, ¾e ho neotevøete v nìkterých grafických editorech, o druhé pozdìji. Pochopíte sami :-(</p>

<p>Funkci pøedáváme název souboru a ukazatel na strukturu.</p>

<p class="src0">int ReadTextureData(char *filename, P_TEXTURE_IMAGE buffer)<span class="kom">// Naète data obrázku</span></p>
<p class="src0">{</p>

<p>Deklarujeme handle souboru, øídící promìnné cyklù a promìnnou done, která indikuje úspìch/neúspìch operace volajícímu kódu. Na zaèátku jí pøiøadíme nulu, proto¾e obrázek je¹tì není nahraný. Promìnnou stride, která urèuje velikost øádku, hned na zaèátku inicializujeme na hodnotu získanou vynásobením ¹íøky øádku v pixelech s barevnou hloubkou. Pokud bude obrázek ¹iroký 256 pixelù a barevná hloubka 4 byty (32 bitù, RGBA), velikost øádku bude celkem 1024 bytù. Pointer p ukazuje do pamìti dat obrázku.</p>

<p class="src1">FILE *f;<span class="kom">// Handle souboru</span></p>
<p class="src1">int i, j, k;<span class="kom">// Øídící promìnné cyklù</span></p>
<p class="src1">int done = 0;<span class="kom">// Poèet naètených bytù ze souboru (návratová hodnota)</span></p>
<p class="src"></p>
<p class="src1">int stride = buffer-&gt;width * buffer-&gt;format;<span class="kom">// Velikost øádku</span></p>
<p class="src1">unsigned char *p = NULL;<span class="kom">// Ukazatel na aktuální byte pamìti</span></p>

<p>Otevøeme soubor pro ètení v binárním módu.</p>

<p class="src1">f = fopen(filename, &quot;rb&quot;);<span class="kom">// Otevøe soubor</span></p>
<p class="src"></p>
<p class="src1">if(f != NULL)<span class="kom">// Podaøilo se ho otevøít?</span></p>
<p class="src1">{</p>

<p>Pokud soubor existuje a ¹el otevøít, zaèneme se postupnì vnoøovat do cyklù. V¹e by bylo velice jednoduché, kdyby .RAW formát byl trochu jinak uspoøádán. Øádky vedou, jak je obvyklé, zleva doprava, ale jejich poøadí je invertované. To znamená, ¾e první øádek je poslední, druhý pøedposlední atd. Vnìj¹í cyklus tedy nastavíme tak, aby øídící promìnná ukazovala dolù na zaèátek obrázku. Soubor naèítáme od zaèátku, ale hodnoty ukládáme od konce pamìti vzhùru. Výsledkem je pøevrácení obrázku.</p>

<p class="src2">for(i = buffer-&gt;height-1; i &gt;= 0 ; i--)<span class="kom">// Od zdola nahoru po øádcích</span></p>
<p class="src2">{</p>

<p>Nastavíme ukazatel, kam se právì ukládá, na správný øádek pamìti. Jejím zaèátkem je samozøejmì buffer-&gt;data. Seèteme ho s umístìním od zaèátku i * velikost øádku. Pøedstavte si, ¾e buffer-&gt;data je stránka v pamìti a i * stride pøedstavuje offset. Je to úplnì stejné. Offsetem se pohybujeme po pøidìlené stránce. Na zaèátku je maximální a postupnì klesá. Výsledkem je, ¾e v pamìti postupujeme vzhùru. Myslím, ¾e je to pochopitelné.</p>

<p class="src3">p = buffer-&gt;data + (i * stride);<span class="kom">// P ukazuje na po¾adovaný øádek</span></p>

<p>Druhým cyklem se pohybujeme zleva doprava po pixelech obrázku (ne bytech!).</p>

<p class="src3">for (j = 0; j &lt; buffer-&gt;width; j++)<span class="kom">// Zleva doprava po pixelech</span></p>
<p class="src3">{</p>

<p>Tøetí cyklus prochází jednotlivé byty v pixelu. Pokud barevná hloubka (= byty na pixel) bude 4, cyklus projde celkem 3x (od 0 do 2; format-1). Dùvodem odeètení jednièky je, ¾e vìt¹ina .RAW obrázkù neobsahuje alfa hodnotu, ale pouze RGB slo¾ky. Alfu nastavíme ruènì.</p>

<p>V¹imnìte si také, ¾e ka¾dým prùchodem inkrementujeme tøi promìnné: k, p a done. Øídící promìnná k je jasná. P ukazovalo pøed vstupem do v¹ech cyklù na zaèátek posledního øádku v pamìti. Postupnì ho inkrementujeme a¾ dosáhne úplného konce. Potom ho nastavíme na pøedposlední øádek atd. Done na konci funkce vrátíme, oznaèuje celkový poèet naètených bytù.</p>

<p class="src4">for (k = 0; k &lt; buffer-&gt;format-1; k++, p++, done++)<span class="kom">// Jednotlivé byty v pixelu</span></p>
<p class="src4">{</p>

<p>Funkce fgetc() naète ze souboru f jeden znak a vrátí ho. Tento znak má velikost 1 byte (U¾ víte proè zrovna unsigned char?). Pova¾ujeme ho za slo¾ku barvy. Proto¾e se cyklus po tøetím prùchodu zastaví, naèteme a ulo¾íme slo¾ky R, G a B.</p>

<p class="src5">*p = fgetc(f);<span class="kom">// Naète R, G a B slo¾ku barvy</span></p>
<p class="src4">}</p>

<p>Po opu¹tìní cyklu pøiøadíme alfu a opìt inkrementujeme ukazatel, aby se posunul na dal¹í byte.</p>

<p>Pøekl.: Tady se hodí poznamenat, ¾e alfa nemusí být zrovna 255 (neprùhledná), ale mù¾eme ji nastavit na polovinu (122) a tak vytvoøit poloprùhlednou texturu. Nebo si øíct, ¾e pixel o urèitých slo¾kách RGB bude prùhledný. Vìt¹inou se vezme èerná nebo bílá barva, ale nic nebrání napø. naètení levého horního pixelu obrázku a zprùhlednìní v¹ech ostatních pixelù se stejným RGB. Nebo postupnì, jak naèítáme jednotlivé pixely v øádku, sni¾ovat alfu od 255 do 0. Textura bude vlevo neprùhledná a vpravo prùhledná - plynulý pøechod. S prùhledností se dìlají hodnì kvalitní efekty. Malièké upozornìní na konec: Efekty s alfa hodnotou jsou mo¾né nejen u .RAW textur. Nezapomeòte, ¾e u¾ v 6. lekci !!! jsme mìli pøístup k datùm textury. Funkci glTexImage2D() jsme na konci LoadGLTextures() pøedávali parametr data!</p>

<p class="src4">*p = 255;<span class="kom">// Alfa neprùhledná (ruèní nastavení)</span></p>
<p class="src4">p++;<span class="kom">// Ukazatel na dal¹í byte</span></p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Poté, co projdeme v¹echny byty v pixelu, pixely v øádku a øádky v souboru se v¹echny cykly ukonèí. Uf, Koneènì! :-) Po ukonèení cyklù zavøeme soubor.</p>

<p class="src2">fclose(f);<span class="kom">// Zavøe soubor</span></p>
<p class="src1">}</p>

<p>Pokud byly problémy s otevøením souboru (neexistuje ap.) zobrazíme chybovou zprávu.</p>

<p class="src1">else<span class="kom">// Soubor se nepodaøilo otevøít</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL,&quot;Unable To Open Image File&quot;,&quot;IMAGE ERROR&quot;,MB_OK | MB_ICONINFORMATION);</p>
<p class="src1">}</p>

<p>Nakonec vrátíme done. Pokud se soubor nepodaøilo otevøít a my nic nenaèetli, obsahuje nulu. Pokud bylo v¹e v poøádku done se rovná poètu naètených bytù.</p>

<p class="src1">return done;<span class="kom">// Vrátí poèet naètených bytù</span></p>
<p class="src0">}</p>

<p>Máme loadovaná data obrázku, tak¾e vytvoøíme texturu. Funkci pøedáváme ukazatel na obrázek. Vygenerujeme texturu, nastavíme ji jako aktuální, zvolíme lineární filtrování pro zvìt¹ení i zmen¹ení a nakonec vytvoøíme mipmapovanou texturu. V¹e je úplnì stejné jako s knihovnou glaux, ale s tím rozdílem, ¾e jsme si obrázek tentokrát nahráli sami.</p>

<p class="src0">void BuildTexture(P_TEXTURE_IMAGE tex)<span class="kom">// Vytvoøí texturu</span></p>
<p class="src0">{</p>
<p class="src1">glGenTextures(1, &amp;texture[0]);<span class="kom">// Generuje texturu</span></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Vybere texturu za aktuální</span></p>
<p class="src"></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);<span class="kom">// Lineární filtrování</span></p>
<p class="src1">glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Mipmapovaná textura</span></p>
<p class="src1">gluBuild2DMipmaps(GL_TEXTURE_2D, GL_RGB, tex-&gt;width, tex-&gt;height, GL_RGBA, GL_UNSIGNED_BYTE, tex-&gt;data);</p>
<p class="src0">}</p>

<p>Funkci Blit(), která implementuje blitting, je pøedávána spousta parametrù. Co vyjadøují? Vezmeme to hezky popoøadì. Src je zdrojovým obrázkem, jeho¾ data vkládáme do cílového obrázku dst. Ostatní parametry vyznaèují, která data se zkopírují (obdélník urèený ètyømi src_* èísly), kam se mají do cílového obrázku umístit (dst_*) a jakým zpùsobem (blending, popø. alfa hodnota).</p>

<p class="src0"><span class="kom">// Blitting obrázkù</span></p>
<p class="src0">void Blit(P_TEXTURE_IMAGE src,<span class="kom">// Zdrojový obrázek</span></p>
<p class="src1">P_TEXTURE_IMAGE dst,<span class="kom">// Cílový obrázek</span></p>
<p class="src1">int src_xstart,<span class="kom">// Levý horní bod kopírované oblasti</span></p>
<p class="src1">int src_ystart,<span class="kom">// Levý horní bod kopírované oblasti</span></p>
<p class="src1">int src_width,<span class="kom">// ©íøka kopírované oblasti</span></p>
<p class="src1">int src_height,<span class="kom">// Vý¹ka kopírované oblasti</span></p>
<p class="src1">int dst_xstart,<span class="kom">// Kam kopírovat (levý horní bod)</span></p>
<p class="src1">int dst_ystart,<span class="kom">// Kam kopírovat (levý horní bod)</span></p>
<p class="src1">int blend,<span class="kom">// Pou¾ít blending?</span></p>
<p class="src1">int alpha)<span class="kom">// Hodnota alfy pøi blendingu</span></p>
<p class="src0">{</p>

<p>Po øídících promìnných cyklù deklarujeme pomocné promìnné s a d, které ukazují do pamìti obrázkù. Dále o¹etøíme pøedávané parametry tak, aby alfa hodnota byla v rozmezí 0 a¾ 255 a blend 0 nebo 1.</p>

<p class="src1">int i, j, k;<span class="kom">// Øídící promìnné cyklù</span></p>
<p class="src1">unsigned char *s, *d;<span class="kom">// Pomocné ukazatele na data zdroje a cíle</span></p>
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

<p>Pøekl.: Celé kopírování radìji vysvìtlím na pøíkladu, bude snáze pochopitelné. Máme obrázek 256 pixelù ¹iroký a chceme zkopírovat napø. oblast od 50. do 200. pixelu o urèité vý¹ce. Pøed vstupem do cyklu se pøesuneme na první kopírovaný øádek. Potom skoèíme na 50. pixel zleva, zkopírujeme 150 pixelù a skoèíme na konec øádku pøes zbývajících 56 pixelù. V¹e opakujeme pro dal¹í øádek, dokud nezkopírujeme celý po¾adovaný obdélník dat zdrojového obrázku do cílového.</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_29_priklad.gif" width="128" height="128" alt="Pøíklad" /></div>

<p>Nyní nastavíme ukazatele d a s. Cílový ukazatel získáme seètením adresy, kde zaèínají data cílového obrázku s offsetem, který je výsledkem násobení y pozice, kam zaèneme kopírovat, ¹íøkou obrázku v pixelech a barevnou hloubkou obrázku. Tímto získáme øádek, na kterém zaèínáme kopírovat. Zdrojový ukazatel urèíme analogicky.</p>

<p class="src1"><span class="kom">// Ukazatele na první kopírovaný øádek</span></p>
<p class="src1">d = dst-&gt;data + (dst_ystart * dst-&gt;width * dst-&gt;format);</p>
<p class="src1">s = src-&gt;data + (src_ystart * src-&gt;width * src-&gt;format);</p>

<p>Vnìj¹í cyklus prochází kopírované øádky od shora dolù.</p>

<p class="src1">for (i = 0; i &lt; src_height; i++)<span class="kom">// Øádky, ve kterých se kopírují data</span></p>
<p class="src1">{</p>

<p>U¾ máme ukazatel nastaven na správný øádek, ale je¹tì musíme pøièíst x-ovou pozici, která se opìt násobí barevnou hloubkou. Akci provedeme pro zdrojový i cílový ukazatel.</p>

<p class="src2"><span class="kom">// Posun na první kopírovaný pixel v øádku</span></p>
<p class="src2">s = s + (src_xstart * src-&gt;format);</p>
<p class="src2">d = d + (dst_xstart * dst-&gt;format);</p>

<p>Pointery nyní ukazují na první kopírovaný pixel. Zaèneme cyklus, který v øádku prochází jednotlivé pixely.</p>

<p class="src2">for (j = 0; j &lt; src_width; j++)<span class="kom">// Pixely v øádku, které se mají kopírovat</span></p>
<p class="src2">{</p>

<p>Nejvnitønìj¹í cyklus prochází jednotlivé byty v pixelu. V¹imnìte si, ¾e se také inkrementují pozice ve zdrojovém i cílovém obrázku.</p>

<p class="src3">for(k = 0; k &lt; src-&gt;format; k++, d++, s++)<span class="kom">// Byty v kopírovaném pixelu</span></p>
<p class="src3">{</p>

<p>Pøichází nejzajímavìj¹í èást - vytvoøení alfablendingu. Pøedstavte si, ¾e máte dva pixely: èervený (zdroj) a zelený (cíl). Oba le¾í na stejných souøadnicích. Pokud je nezprùhledníte, pùjde vidìt pouze jeden z nich, proto¾e pùvodní pixel bude nahrazen novým. Jak jistì víte, ka¾dý pixel se skládá ze tøí barevných kanálù RGB. Chceme-li vytvoøit alfa blending, musíme nejdøíve spoèítat opaènou hodnotu alfa kanálu a to tak, ¾e odeèteme tuto hodnotu od maxima (255 - alpha). Násobíme jí cílový (zelený) pixel a seèteme ho se zdrojovým (èerveným), který jsme násobili neupravenou alfou. Jsme skoro hotovi. Koneènou barvu vypoèítáme dìlením výsledku maximální hodnotou prùhlednosti (255). tuto operaci z dùvodu vìt¹í rychlosti vykonává bitový posun doprava o osm bitù. A je to! Máme pixel slo¾ený z obou pøedcházejících pixelù. V¹imnìte si, ¾e se výpoèty postupnì provádìjí se v¹emi kanály RGBA. Víte, co jsme právì implementovali? OpenGL techniku glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA).</p>

<p class="src4">if (blend)<span class="kom">// Je po¾adován blending?</span></p>
<p class="src4">{</p>
<p class="src5">*d = ((*s * alpha) + (*d * (255-alpha))) &gt;&gt; 8;<span class="kom">// Slouèení dvou pixelù do jednoho</span></p>
<p class="src4">}</p>

<p>Pokud nebudeme chtít blending, jednodu¹e zkopírujeme data ze zdrojové bitmapy do cílové. ®ádná matematika, alfa se ignoruje.</p>

<p class="src4">else<span class="kom">// Bez blendingu</span></p>
<p class="src4">{</p>
<p class="src5">*d = *s;<span class="kom">// Obyèejné kopírování</span></p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p>Dojdeme-li a¾ na konec kopírované oblasti, zvìt¹íme ukazatel tak, aby se dostal na konec øádku. Pokud dobøe rozumíme ukazatelùm a pamì»ovým operacím, je blitting hraèkou.</p>

<p class="src2"><span class="kom">// Skoèí ukazatelem na konec øádku</span></p>
<p class="src2">d = d + (dst-&gt;width - (src_width + dst_xstart)) * dst-&gt;format;</p>
<p class="src2">s = s + (src-&gt;width - (src_width + src_xstart)) * src-&gt;format;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Inicializace je tentokrát zmìnìna od základù. Alokujeme pamì» pro dva obrázky veliké 256 pixelù, které mají barevnou hloubku 4 byty (RGBA). Poté se je pokusíme nahrát. Pokud nìco nevyjde vypí¹eme chybovou zprávu a ukonèíme program.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">t1 = AllocateTextureBuffer(256, 256, 4);<span class="kom">// Alokace pamìti pro první obrázek</span></p>
<p class="src"></p>
<p class="src1">if (ReadTextureData(&quot;Data/Monitor.raw&quot;, t1) == 0)<span class="kom">// Nahraje data obrázku</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nic se nenahrálo</span></p>
<p class="src2">MessageBox(NULL, &quot;Could Not Read 'Monitor.raw' Image Data&quot;, &quot;TEXTURE ERROR&quot;, MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">t2 = AllocateTextureBuffer(256, 256, 4);<span class="kom">// Alokace pamìti pro druhý obrázek</span></p>
<p class="src"></p>
<p class="src1">if (ReadTextureData(&quot;Data/GL.raw&quot;, t2) == 0)<span class="kom">// Nahraje data obrázku</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Nic se nenahrálo</span></p>
<p class="src2">MessageBox(NULL, &quot;Could Not Read 'GL.raw' Image Data&quot;, &quot;TEXTURE ERROR&quot;, MB_OK | MB_ICONINFORMATION);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>

<p>Pokud jsme se dostali a¾ tak daleko, je bezpeèné pøedpokládat, ¾e mù¾eme pracovat s daty obrázkù, které se pokusíme blittingem slouèit do jednoho. Pøedáme je funkci - obrázek t2 jako zdrojový, t1 jako cílový. Výsledný obrázek získaný slouèením se ulo¾í do t1. Vytvoøíme z nìj texturu.</p>

<p class="src1"><span class="kom">// Blitting obrázkù</span></p>
<p class="src1">Blit(t2,<span class="kom">// Zdrojový obrázek</span></p>
<p class="src2">t1,<span class="kom">// Cílový obrázek</span></p>
<p class="src2">127,<span class="kom">// Levý horní bod kopírované oblasti</span></p>
<p class="src2">127,<span class="kom">// Levý horní bod kopírované oblasti</span></p>
<p class="src2">128,<span class="kom">// ©íøka kopírované oblasti</span></p>
<p class="src2">128,<span class="kom">// Vý¹ka kopírované oblasti</span></p>
<p class="src2">64,<span class="kom">// Kam kopírovat (levý horní bod)</span></p>
<p class="src2">64,<span class="kom">// Kam kopírovat (levý horní bod)</span></p>
<p class="src2">1,<span class="kom">// Pou¾ít blending?</span></p>
<p class="src2">128)<span class="kom">// Hodnota alfy pøi blendingu</span></p>
<p class="src"></p>
<p class="src1">BuildTexture(t1);<span class="kom">// Vytvoøí texturu</span></p>

<p>Pøekl.: Pùvodnì jsem chtìl vlo¾it obrázky, abyste vìdìli, jak vypadají, ale bohu¾el ani jeden grafický editor, který mám zrovna doma .RAW formát nepodporuje. V anglickém tutoriálu je zmínìno, ¾e Adobe Photoshop to svede. Ale poradil jsem si... víte jak? OpenGL.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_29_noblit1.gif" width="170" height="185" alt="Obrázek t2" />
<img src="images/nehe_tut/tut_29_noblit2.gif" width="170" height="185" alt="Obrázek t1" />
<img src="images/nehe_tut/tut_29_blit.gif" width="170" height="185" alt="Obrázek t1 po blittingu" />
</div>

<p>Potom, co je vytvoøena textura, mù¾eme uvolnit pamì» obou obrázkù.</p>

<p class="src1">DeallocateTexture(t1);<span class="kom">// Uvolní pamì» obrázkù</span></p>
<p class="src1">DeallocateTexture(t2);</p>

<p>Následují bì¾ná nastavení OpenGL.</p>

<p class="src1">glEnable(GL_TEXTURE_2D);<span class="kom">// Zapne texturování</span></p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemné stínování</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// Èerné pozadí</span></p>
<p class="src"></p>
<p class="src1">glClearDepth(1.0);<span class="kom">// Povolí mazání depth bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Zapne testování hloubky</span></p>
<p class="src1">glDepthFunc(GL_LESS);<span class="kom">// Typ testování hloubky</span></p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>DrawGLScene() renderuje obyèejnou krychli - to u¾ urèitì znáte.</p>

<p class="src0">GLvoid DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(0.0f,0.0f,-10.0f);<span class="kom">// Pøesun do hloubky</span></p>
<p class="src"></p>
<p class="src1">glRotatef(xrot, 1.0f,0.0f,0.0f);<span class="kom">// Rotace</span></p>
<p class="src1">glRotatef(yrot, 0.0f,1.0f,0.0f);</p>
<p class="src1">glRotatef(zrot, 0.0f,0.0f,1.0f);</p>
<p class="src"></p>
<p class="src1">glBindTexture(GL_TEXTURE_2D, texture[0]);<span class="kom">// Zvolí texturu</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src2"><span class="kom">// Èelní stìna</span></p>
<p class="src2">glNormal3f(0.0f, 0.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 1.0f);</p>
<p class="src2"><span class="kom">// Zadní stìna</span></p>
<p class="src2">glNormal3f(0.0f, 0.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f,-1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f,-1.0f,-1.0f);</p>
<p class="src2"><span class="kom">// Horní stìna</span></p>
<p class="src2">glNormal3f(0.0f, 1.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f, 1.0f, 1.0f);</p>
<p class="src2"><span class="kom">// Dolní stìna</span></p>
<p class="src2">glNormal3f(0.0f,-1.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f,-1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f,-1.0f,-1.0f);</p>
<p class="src2"><span class="kom">// Pravá stìna</span></p>
<p class="src2">glNormal3f(1.0f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f( 1.0f,-1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f( 1.0f, 1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f( 1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f( 1.0f,-1.0f, 1.0f);</p>
<p class="src2"><span class="kom">// Levá stìna</span></p>
<p class="src2">glNormal3f(-1.0f, 0.0f, 0.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 0.0f); glVertex3f(-1.0f,-1.0f,-1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 0.0f); glVertex3f(-1.0f,-1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(1.0f, 1.0f); glVertex3f(-1.0f, 1.0f, 1.0f);</p>
<p class="src2">glTexCoord2f(0.0f, 1.0f); glVertex3f(-1.0f, 1.0f,-1.0f);</p>
<p class="src1">glEnd();<span class="kom">// Konec kreslení</span></p>
<p class="src"></p>
<p class="src1">xrot += 0.3f;<span class="kom">// Zvìt¹í úhly rotace</span></p>
<p class="src1">yrot += 0.2f;</p>
<p class="src1">zrot += 0.4f;</p>
<p class="src0">}</p>

<p>Malièko upravíme kód WinMain(). Pokud není program aktivní (napø. minimalizovaný), zavoláme WaitMessage(). V¹echno se zastaví, dokud program neobdr¾í nìjakou zprávu (obyèejnì o maximalizaci okna). Ve výsledku dosáhneme toho, ¾e pokud program není aktivní nebude vùbec zatì¾ovat procesor.</p>

<p class="src0"><span class="kom">// Funkce WinMain() - v hlavní smyèce programu</span></p>
<p class="src2">if (!active)<span class="kom">// Je program neaktivní?</span></p>
<p class="src2">{</p>
<p class="src3">WaitMessage();<span class="kom">// Èekej na zprávu a zatím nic nedìlej</span></p>
<p class="src2">}</p>

<p>Tak¾e to bychom mìli. Nyní máte ve svých hrách, enginech, demech nebo jakýchkoli programech v¹echny dveøe otevøené pro vytváøení velmi efektních blending efektù. S texturovými buffery mù¾ete vytváøet vìci jako napøíklad real-time plazmu nebo vodu. Vzájemnou kombinací více obrázkù (i nìkolikrát za sebou) je mo¾né dosáhnout témìø fotorealistického terénu. Hodnì ¹tìstí.</p>

<p class="autor">napsal: Andreas Löffler &amp; Rob Fletcher<br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?> &amp; Václav Slováèek - Wessan <?VypisEmail('horizont@host.sk');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul>
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson29.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson29_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson29.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson29.zip">Dev C++</a> kód této lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/euphoria/lesson29.zip">Euphoria</a> kód této lekce. ( <a href="mailto:1evan@sbcglobal.net">Evan Marshall</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/lccwin32/lccwin32_lesson29.zip">LCC Win32</a> kód této lekce. ( <a href="mailto:rwishlaw@shaw.ca">Robert Wishlaw</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linuxglx/lesson29.tar.gz">Linux/GLX</a> kód této lekce. ( <a href="mailto:rodolphe.suescun@wanadoo.fr">Rodolphe Suescun</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson29.zip">Mac OS X/Cocoa</a> kód této lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson29.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(29);?>
<?FceNeHeOkolniLekce(29);?>

<?
include 'p_end.php';
?>
