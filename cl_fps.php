<?
$g_title = 'CZ NeHe OpenGL - FPS: Konstantní rychlost animace';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>FPS: Konstantní rychlost animace</h1>

<p class="nadpis_clanku">FPS je zkratka z poèáteèních písmen slov Frames Per Second, která by se dala do èe¹tiny pøelo¾it jako poèet snímkù za sekundu. Tato tøi písmena jsou spásou pøi spou¹tìní programù na rùzných poèítaèích. Vezmìte si hru, kterou programátor zaèáteèník vyvíjí doma na svém poèítaèi o rychlosti, øeknìme, Pentium II. Dá ji kamarádovi, aby se na ni podíval a zhodnotil. Kamarád má doma P4, spustí ji a v¹e je ¹ílenì rychlé. Díky FPS se toto nikdy nestane, na jakémkoli poèítaèi pùjde hra v¾dy stejnì rychle.</p>

<p>Základem v¹eho je èas, který ubìhl mezi jednotlivými prùchody renderovací funkcí. Uvedu pøíklad. Máme raketu, která má letìt øeknìme 50 jednotek za sekundu. Víme, ¾e mezi tímto a pøedchozím vykreslením ubìhlo 0,1s, tak¾e ji posuneme o 5 jednotek. Tímto zpùsobem budou posuny objektù v animaci za urèitý èas v¾dy konstantní, nezávisí na rychlosti poèítaèe ani aktuální zátì¾i procesoru.</p>

<p>Výsledkem celého tohoto èlánku bude velmi jednoduchá tøída, která v¹echny potøebné operace implementuje a ukázka jejího pou¾ití.</p>

<p>Tøída FPS obsahuje tøi atributy: èas pøi minulém prùchodu vykreslovací funkcí, aktuální èas a hodnotu FPS. O aktualizaci promìnných se stará funkce Vypocet(), která se musí volat stejnì èasto jako pøekreslení scény, nejlépe pøímo ve funkci, která ho má na starosti. GetFPS() je vlo¾eno z dùvodu zapouzdøenosti dat.</p>

<p class="src0">class FPS<span class="kom">// Tøída FPS</span></p>
<p class="src0">{</p>
<p class="src0">private:</p>
<p class="src1">unsigned int stary_cas;</p>
<p class="src1">unsigned int aktualni_cas;</p>
<p class="src1">double fps;<span class="kom">// Poèet snímkù za sekundu</span></p>
<p class="src0">public:</p>
<p class="src1">void Vypocet();<span class="kom">// Volat pøed ka¾dým pøekreslením scény</span></p>
<p class="src"></p>
<p class="src1">inline double GetFPS();<span class="kom">// Kvùli zapouzdøenosti dat</span></p>
<p class="src1">{</p>
<p class="src2">return fps;</p>
<p class="src1">}</p>
<p class="src0">};</p>

<p>Pro získání aktuálního èasu mù¾eme pou¾ít libovolnou funkci. Ve Windows se vìt¹inou pou¾ívá GetTickCount(), která vrací poèet milisekund od spu¹tìní systému. Proto¾e jsem zaèal pøi programování pou¾ívat knihovnu SDL (Umo¾òuje snadné portování aplikace do rùzných operaèních systémù - Linux, Win, Mac OS, BeOS, FreeBSD...), pou¾iji v tomto èlánku SDL_GetTicks(), která vrací poèet milisekund od inicializace SDL. Výpoèet FPS je velmi jednoduchý. Pøevedeme rozdíl èasù na sekundy a vydìlíme jím jednièku, která reprezentuje jedno vykreslení. Na konci pøiøadíme starému èasu aktuální èas.</p>

<p class="src0">void FPS::Vypocet()</p>
<p class="src0">{</p>
<p class="src1">aktualni_cas = SDL_GetTicks();<span class="kom">// Vrátí poèet milisekund od inicializace SDL</span></p>
<p class="src1"><span class="kom">// aktualni_cas = GetTickCount();// Specifické pro Windows</span></p>
<p class="src"></p>
<p class="src1">fps = 1.0 / ((aktualni_cas - stary_cas) / 1000.0);<span class="kom">// Poèet snímkù za sekundu</span></p>
<p class="src1">stary_cas = aktualni_cas;<span class="kom">// Pro dal¹í prùchod</span></p>
<p class="src0">}</p>

<p>Jak pou¾ít tuto tøídu? Øeknìme, ¾e v OpenGL potøebujeme krychli, která rotuje o 45° za sekundu. Pøi ka¾dé aktualizaci úhlu natoèení k nìmu pøièteme plných 45°, které ale musíme vydìlit hodnotou FPS. Jednotlivé posuny se rozfázují tak, aby za sekundu byla krychle natoèená o po¾adovaných 45°.</p>

<p class="src0">FPS fps;<span class="kom">// Objekt tøídy</span></p>
<p class="src0">double uhel = 0.0;<span class="kom">// Úhel natoèení krychle</span></p>
<p class="src"></p>
<p class="src0">void DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">fps.Vypocet();<span class="kom">// Aktualizace FPS</span></p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyma¾e obrazovku a hloubkový buffer</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glTranslated(0.0, 0.0, -6.0);<span class="kom">// Posun do hloubky</span></p>
<p class="src1">glRotated(uhel, 0.0, 1.0, 0.0);<span class="kom">// Otoèí krychli v závislosti na FPS</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslení krychle</span></p>
<p class="src"></p>
<p class="src1">uhel += 45.0 / fps.GetFPS();<span class="kom">// Aktualizace úhlu natoèení v závislosti na FPS</span></p>
<p class="src0">}</p>

<p>Je nutné poznamenat, ¾e nic není idální. Pokud by jedno vykreslení bylo tak nároèné, ¾e by trvalo, pøe¾enu, deset sekund, rychlost by sice zùstala na konstantních 45° za sekundu, ale vykreslovalo by se a¾ po celých deseti sekundách (FPS = 0,1). Dovedete si pøedstavit to trhání?! Obecnì se øíká, ¾e by FPS nikdy nemìlo klesnout pod 30. Nic nám nebrání, abychom vlo¾ili za výpoèet FPS podmínku if(fps.GetFPS < 30) sni¾ kvalitu renderingu; Mohlo by jím být napøíklad zmìna kvality textur GL_LINEAR na GL_NEAREST, vypnutí antialiasingu, odstranìní nìkterých efektù a podobnì.</p>

<p>FPS v¹ak není jedinou mo¾ností, jak zajistit plynulost animace. Mù¾eme mít systémový timer (ve Windows zpráva WM_TIMER), který periodicky volá renderovací funkci. Tuto techniku osobnì pou¾ívám jen kdy¾ není ¾ádná jiná mo¾nost (v¾dy je jiná mo¾nost), proto¾e automatické vykreslování v hlavní smyèce je v¾dy rychlej¹í a spolehlivìj¹í. Pokud nastavíte timer na moc krátký èas (cca. 50 ms a ménì), budou se vícenásobnì poslané zprávy mazat. Aby nedo¹lo k zahlcení fronty zpráv, systém do ní nikdy neumístí více ne¾ jednu zprávu WM_TIMER. Ostatní se neberou v úvahu, jako by se vùbec neposlali.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojových kódù</li>
</ul>

<?
include 'p_end.php';
?>
