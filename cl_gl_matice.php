<?
$g_title = 'CZ NeHe OpenGL - Operace s maticemi';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Operace s maticemi</h1>

<p class="nadpis_clanku">Zaj�mali jste se n�kdy o to, jak funguj� OpenGL funkce pracuj�c� s maticemi? V tomto �l�nku si vysv�tl�me, jak funguj� funkce typu glTranslatef(), glRotatef(), glScalef() a jak je p��padn� nahradit vlastn�m k�dem.</p>

<h3>Matice identity (Identity Matrix)</h3>

<p>Matice jsou to, co d�l� na�i aplikaci 3D, bez nich by rotace a posuny byly prakticky nemo�n�. Ka�d� vrchol ve sc�n� bychom museli zad�vat ru�n� - stra�n� p�edstava pro ka�d�ho program�tora. Ve 3D maj� standardn� matice velikost 4x4.</p>

<p class="src0">[1, 0, 0, 0]</p>
<p class="src0">[0, 1, 0, 0]</p>
<p class="src0">[0, 0, 1, 0]</p>
<p class="src0">[0, 0, 0, 1]</p>

<p>Pokud bychom vyn�sobili bod matic� naho�e (ekvivalent n�soben� jednou), ��dn� zm�na by se nekonala. Proto tuto matici naz�v�me matic� identity, je z�kladem pro v�echny ostatn�. Samoz�ejm�, �e chceme prov�d�t nejr�zn�j�� translace a rotace, pro n� m�me dal�� matice.</p>

<h3>Matice posun� (Translation Matrix)</h3>

<p>��sla tx, ty a tz p�edstavuj� hodnoty posunu.</p>

<p class="src0">[ 1, 0, 0, 0]</p>
<p class="src0">[ 0, 1, 0, 0]</p>
<p class="src0">[ 0, 0, 1, 0]</p>
<p class="src0">[tx,ty,tz, 1]</p>

<h3>Matice rotac� (Rotation Matrix)</h3>

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

<h3>Matice m���tka (Scale Matrix)</h3>

<p>��sla sx, sy a sz p�edstavuj� �rove� zmen�en�/zv�t�en�.</p>

<p class="src0">[sx, 0, 0, 0]</p>
<p class="src0">[ 0,sy, 0, 0]</p>
<p class="src0">[ 0, 0,sz, 0]</p>
<p class="src0">[ 0, 0, 0, 1]</p>

<p>Matice m��eme mezi sebou kombinovat tak� tak, �e je vyn�sob�me. Nam�sto pou��v�n� funkc� glTranslatef(), glRotatef() a glScalef() m��eme libovoln� bod transformovat prost�ednictv�m n�kter� z t�chto matic. V�imn�te si, �e posunem zm�n�me matici i pro jakoukoli dal�� operaci - �pravy nejsou do�asn�, ale trval�.</p>

<p>Anglick� origin�l �l�nku m��ete naj�t na adrese <?OdkazBlank('http://nehe.gamedev.net/data/articles/article.asp?article=02');?>.</p>

<h2>Dodatek p�ekladatele</h2>

<p>Pomoc� uveden�ch v�po�t� se m��eme vyhnout pou��v�n� p��kaz� glRotatef() a glTranslatef(). Modelovou matici si m��eme spo��tat sami a pak ji pouze p�edat  OpenGL pomoc� p��kazu glLoadMatrix() - p�esn� takto to d�l� nap��klad hra Quake III Arena.</p>

<h3>P��stup k modelov� matici</h3>

<p>Z modelov� matice m��ete libovoln� ��st i libovoln� do n� zapisovat. Z�pis se uskute��uje pomoc� funkce glLoadMatrix(), �ten� za pomoci funkce
glGetDoublev(GL_MODELVIEW_MATRIX, &lt;pole do kter�ho se maj� data na��st&gt;);</p>

<h3>V�po�ty s maticemi</h3>

<p>N�sleduj� funkce demonstruj� pou��v�n� matic 4x4. Jak u� plyne z n�zvu, funkce Load_Identity() resetuje matici ulo�enou v glob�ln�m poli m.</p>

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

<p>N�kdy m��eme cht�t pou��t matici 3x3 (obsahuje pouze informace o rotaci). Jak vid�te, z�sk�n� z matice 4x4 je jednoduch�. Sta�� ji o�ezat o posledn� ��dek a sloupec.</p>

<p class="src0">void MATRIX3x3Convert_From_MATRIX4x4(MATRIX4x4 m1)<span class="kom">// O�ez�n� matice 4x4 na 3x3</span></p>
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

<p>N�sleduj�c� dv� funkce p�eb�raj� aktu�ln� matici a s jej� pomoc� n�sob� bod nebo vektor. Z�sk�me tak jeho absolutn� (skute�nou) pozici v prostoru. Argumenty by m�ly b�t jasn� - matice a vektor. Bez t�to funkce by prakticky ne�ly po��tat kolize objekt� ve sc�n�.</p>

<p class="src0">VECTOR Matrix_krat_vektor(MATRIX4x4 m1, VECTOR v1)<span class="kom">// N�soben� vektoru matic� 4x4</span></p>
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
<p class="src0">VERTEX3D Matrix_krat_bod(MATRIX4x4 m1, VERTEX3D p1)<span class="kom">// N�soben� bodu matic� 4x4</span></p>
<p class="src0">{</p>
<p class="src1">VERTEX3D temp;</p>
<p class="src"></p>
<p class="src1">temp.x = (float)(p1.x * m1.m[0] + p1.y * m1.m[4] + p1.z * m1.m[8] + m1.m[12]);</p>
<p class="src1">temp.y = (float)(p1.x * m1.m[1] + p1.y * m1.m[5] + p1.z * m1.m[9] + m1.m[13]);</p>
<p class="src1">temp.z = (float)(p1.x * m1.m[2] + p1.y * m1.m[6] + p1.z * m1.m[10] + m1.m[14]);</p>
<p class="src"></p>
<p class="src1">return temp;</p>
<p class="src0">}</p>

<p>Te� je�t� jednou to sam� ale s pou�it�m matice 3x3.</p>

<p class="src0">VECTOR Matrix_krat_vector(MATRIX3x3 m1, VECTOR v1)<span class="kom">// N�soben� bodu matic� 3x3</span></p>
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

<p class="src0">VERTEX3D Matrix_krat_bod(MATRIX3x3 m1, VERTEX3D p1)<span class="kom">// N�soben� bodu matic� 3x3</span></p>
<p class="src0">{</p>
<p class="src1">VERTEX3D temp;</p>
<p class="src"></p>
<p class="src1">temp.x = (float)(p1.x * m1.m[0] + p1.y * m1.m[3] + p1.z * m1.m[6]);</p>
<p class="src1">temp.y = (float)(p1.x * m1.m[1] + p1.y * m1.m[4] + p1.z * m1.m[7]);</p>
<p class="src1">temp.z = (float)(p1.x * m1.m[2] + p1.y * m1.m[5] + p1.z * m1.m[8]);</p>
<p class="src"></p>
<p class="src1">return temp;</p>
<p class="src0">}</p>

<p>V�po�ty s maticemi maj� mnohem v�ce mo�nost�, nap�. jsem v�bec neuvedl, jak spo��tat v matici posun a rotaci nebo jak v�sledek p�edat OpenGL. T�m bychom se mohli kompletn� vyhnout pou��v�n� funkc� glRotatef() a glTranslatef(). Jak u� jsem napsal, matice se hlav� pou��vaj� k z�sk�n� absolutn� pozice bodu ve sc�n�, kterou pot�ebujeme p�i v�po�tech koliz�, na v�echno ostatn� se pou��vaj� standardn� OpenGL funkce. Dal�� podrobnosti m��ete naj�t nap�. v NeHe Tutori�lu 30 (t��da Tmatrix v souborech Tmatrix.cpp a Tmatrix.h). P�edev��m z tohoto zdroje jsem �erpal.</p>

<p>P�ilo�en� zdrojov� k�d demonstruje, jak z�skat skute�nou pozici bodu v prostoru za pou�it� modelov� matice. Po vol�n� funkc� glTranslatef() a glRotatef() vykresl�me krychli a spo��t�me pozice jej�ch vrchol�, kter� n�sledn� zobraz�me na sc�nu vpravo naho�e.</p>

<p class="autor">napsal: Paul Frazee - The Rainmaker <?VypisEmail('frazee@swbell.net');?><br />
p�elo�il: P�emysl Jaro� <?VypisEmail('xzf@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/matice.zip');?> - Soubory pot�ebn� pro knihovnu SDL nejsou p�ilo�eny</li>
</ul>

<?
include 'p_end.php';
?>
