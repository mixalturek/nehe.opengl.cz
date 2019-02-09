<?
$g_title = 'CZ NeHe OpenGL - Billboarding';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Billboarding (pøiklápìní polygonù ke kameøe)</h1>

<p class="nadpis_clanku">Ka¾dý, kdo nìkdy programoval èásticové systémy, se jistì setkal s problémem, jak zaøídit, aby byly polygony viditelné z jakéhokoli smìru. Nebo-li, aby se nikdy nestalo, ¾e pøi natoèení kamery kolmo na rovinu èástice, nebyla vidìt pouze tenká linka. Slo¾itý problém, ultra jednoduché øe¹ení...</p>

<p>Existuje nìkolik cest, z nich¾ ka¾dá vede ke zdárnému cíli, nicménì nejefektivnìj¹ím bude pravdìpodobnì roz¹íøení GL_ARB_point_sprite. My jí v¹ak nepùjdeme a to hned ze dvou dùvodù. Prvním a hlavním je, ¾e mùj Radeon 7000 u¾ zaèíná být trochu dýchavièný a toto roz¹íøení bohu¾el nepodporuje. Nemìl bych kde ovìøit funkènost kódu. Druhý, který ale v podstatì souvisí s prvním, je, ¾e kdy¾ pou¾íváte extensiony, mìli byste zajistit i bìh na softwaru.</p>

<p>V tomto èlánku si tedy naprogramujeme v¹e ruènì. Pokud vás zajímá mo¾nost s extensiony a mìla by vás zajímat, proto¾e odebírá procesoru spoustu práce, podívejte se na zdrojový kód &quot;With Extension Support&quot; 44. NeHe Tutoriálu, ve kterém se toto roz¹íøení pou¾ívá.</p>

<p>Pravdìpodobnì znáte jedno hodnì nenároèné øe¹ení, které je ¹etrné k procesoru, ale bohu¾el ne v¾dy jde pou¾ít. Zdrojový kód by mohl vypadat napøíklad následovnì (mimochodem 9. NeHe Tutoriál).</p>

<p class="src0">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src0">glTranslatef(0.0f, 0.0f, -20.0f);<span class="kom">// Posun do obrazovky</span></p>
<p class="src0">glRotatef(rot_x, 1.0f, 0.0f, 0.0f);<span class="kom">// Naklopení</span></p>
<p class="src0">glRotatef(rot_y, 0.0f, 1.0f, 0.0f);<span class="kom">// Pootoèení</span></p>
<p class="src0">glTranslatef(pos_x, 0.0f, 0.0f);<span class="kom">// Je¹tì jeden posun</span></p>
<p class="src0">glRotatef(-rot_y, 0.0f, 1.0f, 0.0f);<span class="kom">// Zru¹í pootoèení</span></p>
<p class="src0">glRotatef(-rot_x, 1.0f, 0.0f, 0.0f);<span class="kom">// Zru¹í naklopení</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">// Polygony jsou pøiklopené k ose z -&gt; rendering</span></p>

<p>...tím, ¾e jsme za druhým glTranslatef() v inverzním poøadí zavolali v¹echny rotace je¹tì jednou, ale s opaèným úhlem, zùstaly translace zachovány, ale rotace se vyru¹ily. Kdy¾ budeme tedy vykreslovat polygony rovnobì¾nì s osou z, budou automaticky pøiklopené. A teï k tomu problému: Jak urèit rotace pøi pou¾ívání napø. gluLookAt()?</p>

<p>Tak¾e tøetí cesta... ne ka¾dý ví, já to tedy nevìdìl, ¾e jsou v modelview matici ulo¾eny pøímo souøadnice vektorù (up, right, front) kamery. Staèí je vzít a pou¾ít... :o)</p>

<pre>
[right_x, up_x, front_x, 0]
[right_y, up_y, front_y, 0]
[right_z, up_z, front_z, 0]
[    t_x,  t_y,     t_z, 1]
</pre>

<p>Napí¹eme jednoduchou funkci, jí¾ se budou pøedávat souøadnice v prostoru, na kterých se má èástice vykreslit, a její velikost. Myslím, ¾e komentáøe postaèí...</p>

<p class="src0"><span class="kom">// Quad v¾dy pøiklopený ke kameøe (èástice apod.)</span></p>
<p class="src0">void DrawBillboardedQuad(float x, float y, float z, float width, float height)</p>
<p class="src0">{</p>
<p class="src1">width /= 2.0f;<span class="kom">// Polovina velikosti</span></p>
<p class="src1">height /= 2.0f;</p>
<p class="src"></p>
<p class="src1">float mat[16];</p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, mat);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// QVector zapouzdøuje operace s 3D vektorem</span></p>
<p class="src1">QVector&lt;float&gt; right(mat[0], mat[4], mat[8]);</p>
<p class="src1">right.Normalize();</p>
<p class="src1">right *= width;</p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; up(mat[1], mat[5], mat[9]);</p>
<p class="src1">up.Normalize();</p>
<p class="src1">up *= height;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslení</span></p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2">glVertex3f(x + (-right.GetX() - up.GetX()),</p>
<p class="src4">y + (-right.GetY() - up.GetY()),</p>
<p class="src4">z + (-right.GetZ() - up.GetZ()));</p>
<p class="src2">glVertex3f(x + ( right.GetX() - up.GetX()),</p>
<p class="src4">y + ( right.GetY() - up.GetY()),</p>
<p class="src4">z + ( right.GetZ() - up.GetZ()));</p>
<p class="src2">glVertex3f(x + ( right.GetX() + up.GetX()),</p>
<p class="src4">y + ( right.GetY() + up.GetY()),</p>
<p class="src4">z + ( right.GetZ() + up.GetZ()));</p>
<p class="src2">glVertex3f(x + (-right.GetX() + up.GetX()),</p>
<p class="src4">y + (-right.GetY() + up.GetY()),</p>
<p class="src4">z + (-right.GetZ() + up.GetZ()));</p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>Je¹tì se lze setkat s natáèením pouze okolo jedné osy. Typickým pøíkladem je oheò, stromy a podobnì. To se dìlá napøíklad takto...</p>

<p class="src0"><span class="kom">// Natáèí objekt pouze vzhledem k ose y (stromy, oheò apod.)</span></p>
<p class="src0">void DrawBillboardedQuad(float x, float y, float z, float width, float height)</p>
<p class="src0">{</p>
<p class="src1">width /= 2.0f;<span class="kom">// Polovina velikosti</span></p>
<p class="src1">height /= 2.0f;</p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; pos(x, y + height, z);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// pozice_kamery je vektor, který obsahuje aktuální umístìní kamery</span></p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; a;<span class="kom">// Vektor od kamery k billboardu</span></p>
<p class="src1">a.SetX(pozice_kamery.GetX() - pos.GetX());</p>
<p class="src1">a.SetY(pozice_kamery.GetY() - pos.GetY());</p>
<p class="src1">a.SetZ(pozice_kamery.GetZ() - pos.GetZ());</p>
<p class="src1">a.Normalize();</p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; b;<span class="kom">// Standardní up vektor</span></p>
<p class="src1">b.SetX(0.0f);</p>
<p class="src1">b.SetY(1.0f);</p>
<p class="src1">b.SetZ(0.0f);</p>
<p class="src1"><span class="kom">// b.Normalize();// [0,1,0] je u¾ normalizované</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vektorový souèin tìchto vektorù (vektor kolmý k obìma najednou)</span></p>
<p class="src1">QVector&lt;float&gt; c(a.Cross(b));</p>
<p class="src1">c.Normalize();</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Neguje smìr, aby byl vidìt èelní face</span></p>
<p class="src1">QVector&lt;float&gt; right;</p>
<p class="src1">right = -c * width;</p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; up;</p>
<p class="src1">up.SetX(0.0f);</p>
<p class="src1">up.SetY(1.0f * height);</p>
<p class="src1">up.SetZ(0.0f);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslení</span></p>
<p class="src1">glBegin(GL_QUADS);</p>
<p class="src2">glVertex3f(pos.GetX() + (-right.GetX() - up.GetX()),</p>
<p class="src4">pos.GetY() + (-right.GetY() - up.GetY()),</p>
<p class="src4">pos.GetZ() + (-right.GetZ() - up.GetZ()));</p>
<p class="src2">glVertex3f(pos.GetX() + ( right.GetX() - up.GetX()),</p>
<p class="src4">pos.GetY() + ( right.GetY() - up.GetY()),</p>
<p class="src4">pos.GetZ() + ( right.GetZ() - up.GetZ()));</p>
<p class="src2">glVertex3f(pos.GetX() + ( right.GetX() + up.GetX()),</p>
<p class="src4">pos.GetY() + ( right.GetY() + up.GetY()),</p>
<p class="src4">pos.GetZ() + ( right.GetZ() + up.GetZ()));</p>
<p class="src2">glVertex3f(pos.GetX() + (-right.GetX() + up.GetX()),</p>
<p class="src4">pos.GetY() + (-right.GetY() + up.GetY()),</p>
<p class="src4">pos.GetZ() + (-right.GetZ() + up.GetZ()));</p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>Abych to shrnul, velice krátký èláneèek, ale mù¾e se hodit...</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojových kódù</li>
</ul>

<div class="okolo_img"><img src="images/clanky/cl_gl_billboard.jpg" width="648" height="508" alt="Pøi pohybu se èervený ètverec natáèí" /></div>

<?
include 'p_end.php';
?>
