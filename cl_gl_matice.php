<?
$g_title = 'CZ NeHe OpenGL - Operace s maticemi';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Operace s maticemi</h1>

<p class="nadpis_clanku">Zajímali jste se nìkdy o to, jak fungují OpenGL funkce pracující s maticemi? V tomto èlánku si vysvìtlíme, jak fungují funkce typu glTranslatef(), glRotatef(), glScalef() a jak je pøípadnì nahradit vlastním kódem.</p>

<h3>Matice identity (Identity Matrix)</h3>

<p>Matice jsou to, co dìlá na¹i aplikaci 3D, bez nich by rotace a posuny byly prakticky nemo¾né. Ka¾dý vrchol ve scénì bychom museli zadávat ruènì - stra¹ná pøedstava pro ka¾dého programátora. Ve 3D mají standardní matice velikost 4x4.</p>

<p class="src0">[1, 0, 0, 0]</p>
<p class="src0">[0, 1, 0, 0]</p>
<p class="src0">[0, 0, 1, 0]</p>
<p class="src0">[0, 0, 0, 1]</p>

<p>Pokud bychom vynásobili bod maticí nahoøe (ekvivalent násobení jednou), ¾ádná zmìna by se nekonala. Proto tuto matici nazýváme maticí identity, je základem pro v¹echny ostatní. Samozøejmì, ¾e chceme provádìt nejrùznìj¹í translace a rotace, pro nì máme dal¹í matice.</p>

<h3>Matice posunù (Translation Matrix)</h3>

<p>Èísla tx, ty a tz pøedstavují hodnoty posunu.</p>

<p class="src0">[ 1, 0, 0, 0]</p>
<p class="src0">[ 0, 1, 0, 0]</p>
<p class="src0">[ 0, 0, 1, 0]</p>
<p class="src0">[tx,ty,tz, 1]</p>

<h3>Matice rotací (Rotation Matrix)</h3>

<p>Rotace na ose x:</p>
&nbsp;
<p class="src0">[ 1, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, 0]</p>
<p class="src0">[ 0, cos(xrot),-sin(xrot), 0]</p>
<p class="src0">[ 0, sin(xrot), cos(xrot), 0]</p>
<p class="src0">[ 0, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, 1]</p>

<p>Rotace na ose y:</p>

<p class="src0">[ cos(yrot), 0, sin(yrot), 0]</p>
<p class="src0">[ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, 1, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, 0]</p>
<p class="src0">[-sin(yrot), 0, cos(yrot), 0]</p>
<p class="src0">[ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, 0, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, 1]</p>

<p>Rotace na ose z:</p>

<p class="src0">[ cos(zrot),-sin(zrot), 0, 0]</p>
<p class="src0">[ sin(zrot),  cos(zrot), 0, 0]</p>
<p class="src0">[ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, 1, 0]</p>
<p class="src0">[ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0, 0, 1]</p>

<h3>Matice mìøítka (Scale Matrix)</h3>

<p>Èísla sx, sy a sz pøedstavují úroveò zmen¹ení/zvìt¹ení.</p>

<p class="src0">[sx, 0, 0, 0]</p>
<p class="src0">[ 0,sy, 0, 0]</p>
<p class="src0">[ 0, 0,sz, 0]</p>
<p class="src0">[ 0, 0, 0, 1]</p>

<p>Matice mù¾eme mezi sebou kombinovat také tak, ¾e je vynásobíme. Namísto pou¾ívání funkcí glTranslatef(), glRotatef() a glScalef() mù¾eme libovolný bod transformovat prostøednictvím nìkteré z tìchto matic. V¹imnìte si, ¾e posunem zmìníme matici i pro jakoukoli dal¹í operaci - úpravy nejsou doèasné, ale trvalé.</p>

<p>Anglický originál èlánku mù¾ete najít na adrese <?OdkazBlank('http://nehe.gamedev.net/data/articles/article.asp?article=02');?>.</p>

<h2>Dodatek pøekladatele</h2>

<p>Pomocí uvedených výpoètù se mù¾eme vyhnout pou¾ívání pøíkazù glRotatef() a glTranslatef(). Modelovou matici si mù¾eme spoèítat sami a pak ji pouze pøedat  OpenGL pomocí pøíkazu glLoadMatrix() - pøesnì takto to dìlá napøíklad hra Quake III Arena.</p>

<h3>Pøístup k modelové matici</h3>

<p>Z modelové matice mù¾ete libovolnì èíst i libovolnì do ní zapisovat. Zápis se uskuteèòuje pomocí funkce glLoadMatrix(), ètení za pomoci funkce
glGetDoublev(GL_MODELVIEW_MATRIX, &lt;pole do kterého se mají data naèíst&gt;);</p>

<h3>Výpoèty s maticemi</h3>

<p>Následují funkce demonstrují pou¾ívání matic 4x4. Jak u¾ plyne z názvu, funkce Load_Identity() resetuje matici ulo¾enou v globálním poli m.</p>

<p class="src0">void Load_Identity(void)<span class="kom">// Reset matice</span></p>
<p class="src0">{</p>
<p class="src1">int i;</p>
<p class="src"></p>
<p class="src1">for(i = 0; i < 16; i++)</p>
<p class="src1">{</p>
<p class="src2">m[i] = 0.0f;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">m[0] =  1.0f;</p>
<p class="src1">m[5] =  1.0f;</p>
<p class="src1">m[10] = 1.0f;</p>
<p class="src1">m[15] = 1.0f;</p>
<p class="src0">}</p>

<p>Nìkdy mù¾eme chtít pou¾ít matici 3x3 (obsahuje pouze informace o rotaci). Jak vidíte, získání z matice 4x4 je jednoduché. Staèí ji oøezat o poslední øádek a sloupec.</p>

<p class="src0">void MATRIX3x3Convert_From_MATRIX4x4(MATRIX4x4 m1)<span class="kom">// Oøezání matice 4x4 na 3x3</span></p>
<p class="src0">{</p>
<p class="src1">m[0] = m1.m[0];</p>
<p class="src1">m[1] = m1.m[1];</p>
<p class="src1">m[2] = m1.m[2];</p>
<p class="src"></p>
<p class="src1">m[3] = m1.m[4];</p>
<p class="src1">m[4] = m1.m[5];</p>
<p class="src1">m[5] = m1.m[6];</p>
<p class="src"></p>
<p class="src1">m[6] = m1.m[8];</p>
<p class="src1">m[7] = m1.m[9];</p>
<p class="src1">m[8] = m1.m[10];</p>
<p class="src0">}</p>

<p>Následující dvì funkce pøebírají aktuální matici a s její pomocí násobí bod nebo vektor. Získáme tak jeho absolutní (skuteènou) pozici v prostoru. Argumenty by mìly být jasné - matice a vektor. Bez této funkce by prakticky ne¹ly poèítat kolize objektù ve scénì.</p>

<p class="src0">VECTOR Matrix_krat_vektor(MATRIX4x4 m1, VECTOR v1)<span class="kom">// Násobení vektoru maticí 4x4</span></p>
<p class="src0">{</p>
<p class="src1">VECTOR temp;</p>
<p class="src"></p>
<p class="src1">temp.vx = v1.vx * m1.m[0] + v1.vy * m1.m[4] + v1.vz * m1.m[8] + m1.m[12];</p>
<p class="src1">temp.vy = v1.vx * m1.m[1] + v1.vy * m1.m[5] + v1.vz * m1.m[9] + m1.m[13];</p>
<p class="src1">temp.vz = v1.vx * m1.m[2] + v1.vy * m1.m[6] + v1.vz * m1.m[10] + m1.m[14];</p>
<p class="src"></p>
<p class="src1">return temp;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">VERTEX3D Matrix_krat_bod(MATRIX4x4 m1, VERTEX3D p1)<span class="kom">// Násobení bodu maticí 4x4</span></p>
<p class="src0">{</p>
<p class="src1">VERTEX3D temp;</p>
<p class="src"></p>
<p class="src1">temp.x = (float)(p1.x * m1.m[0] + p1.y * m1.m[4] + p1.z * m1.m[8] + m1.m[12]);</p>
<p class="src1">temp.y = (float)(p1.x * m1.m[1] + p1.y * m1.m[5] + p1.z * m1.m[9] + m1.m[13]);</p>
<p class="src1">temp.z = (float)(p1.x * m1.m[2] + p1.y * m1.m[6] + p1.z * m1.m[10] + m1.m[14]);</p>
<p class="src"></p>
<p class="src1">return temp;</p>
<p class="src0">}</p>

<p>Teï je¹tì jednou to samé ale s pou¾itím matice 3x3.</p>

<p class="src0">VECTOR Matrix_krat_vector(MATRIX3x3 m1, VECTOR v1)<span class="kom">// Násobení bodu maticí 3x3</span></p>
<p class="src0">{</p>
<p class="src1">VECTOR temp;</p>
<p class="src"></p>
<p class="src1">temp.vx = v1.vx * m1.m[0] + v1.vy * m1.m[3] + v1.vz * m1.m[6];</p>
<p class="src1">temp.vy = v1.vx * m1.m[1] + v1.vy * m1.m[4] + v1.vz * m1.m[7];</p>
<p class="src1">temp.vz = v1.vx * m1.m[2] + v1.vy * m1.m[5] + v1.vz * m1.m[8];</p>
<p class="src"></p>
<p class="src1">return temp;</p>
<p class="src0">}</p>

<p class="src"></p>

<p class="src0">VERTEX3D Matrix_krat_bod(MATRIX3x3 m1, VERTEX3D p1)<span class="kom">// Násobení bodu maticí 3x3</span></p>
<p class="src0">{</p>
<p class="src1">VERTEX3D temp;</p>
<p class="src"></p>
<p class="src1">temp.x = (float)(p1.x * m1.m[0] + p1.y * m1.m[3] + p1.z * m1.m[6]);</p>
<p class="src1">temp.y = (float)(p1.x * m1.m[1] + p1.y * m1.m[4] + p1.z * m1.m[7]);</p>
<p class="src1">temp.z = (float)(p1.x * m1.m[2] + p1.y * m1.m[5] + p1.z * m1.m[8]);</p>
<p class="src"></p>
<p class="src1">return temp;</p>
<p class="src0">}</p>

<p>Výpoèty s maticemi mají mnohem více mo¾ností, napø. jsem vùbec neuvedl, jak spoèítat v matici posun a rotaci nebo jak výsledek pøedat OpenGL. Tím bychom se mohli kompletnì vyhnout pou¾ívání funkcí glRotatef() a glTranslatef(). Jak u¾ jsem napsal, matice se hlavì pou¾ívají k získání absolutní pozice bodu ve scénì, kterou potøebujeme pøi výpoètech kolizí, na v¹echno ostatní se pou¾ívají standardní OpenGL funkce. Dal¹í podrobnosti mù¾ete najít napø. v NeHe Tutoriálu 30 (tøída Tmatrix v souborech Tmatrix.cpp a Tmatrix.h). Pøedev¹ím z tohoto zdroje jsem èerpal.</p>

<p>Pøilo¾ený zdrojový kód demonstruje, jak získat skuteènou pozici bodu v prostoru za pou¾ití modelové matice. Po volání funkcí glTranslatef() a glRotatef() vykreslíme krychli a spoèítáme pozice jejích vrcholù, které následnì zobrazíme na scénu vpravo nahoøe.</p>

<p class="autor">napsal: Paul Frazee - The Rainmaker <?VypisEmail('frazee@swbell.net');?><br />
pøelo¾il: Pøemysl Jaro¹ <?VypisEmail('xzf@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/matice.zip');?> - Soubory potøebné pro knihovnu SDL nejsou pøilo¾eny</li>
</ul>

<?
include 'p_end.php';
?>
