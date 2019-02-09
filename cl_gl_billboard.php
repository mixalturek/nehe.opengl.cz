<?
$g_title = 'CZ NeHe OpenGL - Billboarding';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Billboarding (p�ikl�p�n� polygon� ke kame�e)</h1>

<p class="nadpis_clanku">Ka�d�, kdo n�kdy programoval ��sticov� syst�my, se jist� setkal s probl�mem, jak za��dit, aby byly polygony viditeln� z jak�hokoli sm�ru. Nebo-li, aby se nikdy nestalo, �e p�i nato�en� kamery kolmo na rovinu ��stice, nebyla vid�t pouze tenk� linka. Slo�it� probl�m, ultra jednoduch� �e�en�...</p>

<p>Existuje n�kolik cest, z nich� ka�d� vede ke zd�rn�mu c�li, nicm�n� nejefektivn�j��m bude pravd�podobn� roz���en� GL_ARB_point_sprite. My j� v�ak nep�jdeme a to hned ze dvou d�vod�. Prvn�m a hlavn�m je, �e m�j Radeon 7000 u� za��n� b�t trochu d�chavi�n� a toto roz���en� bohu�el nepodporuje. Nem�l bych kde ov��it funk�nost k�du. Druh�, kter� ale v podstat� souvis� s prvn�m, je, �e kdy� pou��v�te extensiony, m�li byste zajistit i b�h na softwaru.</p>

<p>V tomto �l�nku si tedy naprogramujeme v�e ru�n�. Pokud v�s zaj�m� mo�nost s extensiony a m�la by v�s zaj�mat, proto�e odeb�r� procesoru spoustu pr�ce, pod�vejte se na zdrojov� k�d &quot;With Extension Support&quot; 44. NeHe Tutori�lu, ve kter�m se toto roz���en� pou��v�.</p>

<p>Pravd�podobn� zn�te jedno hodn� nen�ro�n� �e�en�, kter� je �etrn� k procesoru, ale bohu�el ne v�dy jde pou��t. Zdrojov� k�d by mohl vypadat nap��klad n�sledovn� (mimochodem 9. NeHe Tutori�l).</p>

<p class="src0">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src0">glTranslatef(0.0f, 0.0f, -20.0f);<span class="kom">// Posun do obrazovky</span></p>
<p class="src0">glRotatef(rot_x, 1.0f, 0.0f, 0.0f);<span class="kom">// Naklopen�</span></p>
<p class="src0">glRotatef(rot_y, 0.0f, 1.0f, 0.0f);<span class="kom">// Pooto�en�</span></p>
<p class="src0">glTranslatef(pos_x, 0.0f, 0.0f);<span class="kom">// Je�t� jeden posun</span></p>
<p class="src0">glRotatef(-rot_y, 0.0f, 1.0f, 0.0f);<span class="kom">// Zru�� pooto�en�</span></p>
<p class="src0">glRotatef(-rot_x, 1.0f, 0.0f, 0.0f);<span class="kom">// Zru�� naklopen�</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">// Polygony jsou p�iklopen� k ose z -&gt; rendering</span></p>

<p>...t�m, �e jsme za druh�m glTranslatef() v inverzn�m po�ad� zavolali v�echny rotace je�t� jednou, ale s opa�n�m �hlem, z�staly translace zachov�ny, ale rotace se vyru�ily. Kdy� budeme tedy vykreslovat polygony rovnob�n� s osou z, budou automaticky p�iklopen�. A te� k tomu probl�mu: Jak ur�it rotace p�i pou��v�n� nap�. gluLookAt()?</p>

<p>Tak�e t�et� cesta... ne ka�d� v�, j� to tedy nev�d�l, �e jsou v modelview matici ulo�eny p��mo sou�adnice vektor� (up, right, front) kamery. Sta�� je vz�t a pou��t... :o)</p>

<pre>
[right_x, up_x, front_x, 0]
[right_y, up_y, front_y, 0]
[right_z, up_z, front_z, 0]
[    t_x,  t_y,     t_z, 1]
</pre>

<p>Nap�eme jednoduchou funkci, j� se budou p�ed�vat sou�adnice v prostoru, na kter�ch se m� ��stice vykreslit, a jej� velikost. Mysl�m, �e koment��e posta��...</p>

<p class="src0"><span class="kom">// Quad v�dy p�iklopen� ke kame�e (��stice apod.)</span></p>
<p class="src0">void DrawBillboardedQuad(float x, float y, float z, float width, float height)</p>
<p class="src0">{</p>
<p class="src1">width /= 2.0f;<span class="kom">// Polovina velikosti</span></p>
<p class="src1">height /= 2.0f;</p>
<p class="src"></p>
<p class="src1">float mat[16];</p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, mat);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// QVector zapouzd�uje operace s 3D vektorem</span></p>
<p class="src1">QVector&lt;float&gt; right(mat[0], mat[4], mat[8]);</p>
<p class="src1">right.Normalize();</p>
<p class="src1">right *= width;</p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; up(mat[1], mat[5], mat[9]);</p>
<p class="src1">up.Normalize();</p>
<p class="src1">up *= height;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslen�</span></p>
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

<p>Je�t� se lze setkat s nat��en�m pouze okolo jedn� osy. Typick�m p��kladem je ohe�, stromy a podobn�. To se d�l� nap��klad takto...</p>

<p class="src0"><span class="kom">// Nat��� objekt pouze vzhledem k ose y (stromy, ohe� apod.)</span></p>
<p class="src0">void DrawBillboardedQuad(float x, float y, float z, float width, float height)</p>
<p class="src0">{</p>
<p class="src1">width /= 2.0f;<span class="kom">// Polovina velikosti</span></p>
<p class="src1">height /= 2.0f;</p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; pos(x, y + height, z);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// pozice_kamery je vektor, kter� obsahuje aktu�ln� um�st�n� kamery</span></p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; a;<span class="kom">// Vektor od kamery k billboardu</span></p>
<p class="src1">a.SetX(pozice_kamery.GetX() - pos.GetX());</p>
<p class="src1">a.SetY(pozice_kamery.GetY() - pos.GetY());</p>
<p class="src1">a.SetZ(pozice_kamery.GetZ() - pos.GetZ());</p>
<p class="src1">a.Normalize();</p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; b;<span class="kom">// Standardn� up vektor</span></p>
<p class="src1">b.SetX(0.0f);</p>
<p class="src1">b.SetY(1.0f);</p>
<p class="src1">b.SetZ(0.0f);</p>
<p class="src1"><span class="kom">// b.Normalize();// [0,1,0] je u� normalizovan�</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vektorov� sou�in t�chto vektor� (vektor kolm� k ob�ma najednou)</span></p>
<p class="src1">QVector&lt;float&gt; c(a.Cross(b));</p>
<p class="src1">c.Normalize();</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Neguje sm�r, aby byl vid�t �eln� face</span></p>
<p class="src1">QVector&lt;float&gt; right;</p>
<p class="src1">right = -c * width;</p>
<p class="src"></p>
<p class="src1">QVector&lt;float&gt; up;</p>
<p class="src1">up.SetX(0.0f);</p>
<p class="src1">up.SetY(1.0f * height);</p>
<p class="src1">up.SetZ(0.0f);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslen�</span></p>
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

<p>Abych to shrnul, velice kr�tk� �l�ne�ek, ale m��e se hodit...</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojov�ch k�d�</li>
</ul>

<div class="okolo_img"><img src="images/clanky/cl_gl_billboard.jpg" width="648" height="508" alt="P�i pohybu se �erven� �tverec nat���" /></div>

<?
include 'p_end.php';
?>
