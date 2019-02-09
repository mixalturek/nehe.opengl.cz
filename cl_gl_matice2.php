<?
$g_title = 'CZ NeHe OpenGL - Matice v OpenGL';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Matice v OpenGL</h1>

<p class="nadpis_clanku">V tomto �l�nku se dozv�te, jak�m zp�sobem OpenGL ukl�d� hodnoty rotac� a translac� do sv� modelview matice. Samoz�ejm� nebudou chyb�t obr�zky jej�ho obsahu po r�zn�ch maticov�ch operac�ch.</p>

<p>P�edem chci upozornit, �e v�echno vysv�tl�m tak, jak tomu rozum�m j�, tedy neodborn�. Douf�m, �e dan� problematice p�jde z m�ho v�kladu snadno porozum�t. Tak� neru��m za to, �e to p�esn� takto m� b�t, proto�e jsem si dan� postup vymyslel (= odvodil z n��eho �pln� jin�ho). Ale funguje, tak co :-).</p>

<p>Za�neme teori�. Vysv�tl�m, kde je co v modelview matici um�st�no a pak se pokus�m vysv�tlit, jak v�e dohromady funguje. Jist� v�te, �e v OpenGL m� matice velikost 4x4. Po slo�it�m v�po�tu tedy z�sk�me celkem 16 index� pole. Ale pozor, matice nen� ulo�ena jako dvourozm�rn�, n�br� jednorozm�rn�. V programu pot�ebujeme vytvo�it pole o 16 prvc�ch bu� typu float nebo double. Osobn� pou��v�m float, proto�e m� dostate�nou p�esnost, pr�ce s n�m je rychlej�� a zab�r� m�n� pam�ti.</p>

<p class="src0">float Matrix[16];<span class="kom">// OpenGL matice</span></p>

<p>Na n�sleduj�c�m obr�zku je zn�zorn�no rozm�st�n� index� do 1D pole ve 2D matici, kterou si pro zp�ehledn�n� p�edstavujeme.</p>

<div class="okolo_img"><img src="images/clanky/matice2/index.gif" width="400" height="200" alt="Indexy v matici" /></div>

<p>Te� si uk�eme, co se na kter� index ukl�d� a k �emu slou��. Hodnoty Move ozna�uj� posun objektu v os�ch X, Y, Z a Rot ��sla definuj� rotace, kde je pro ka�dou p�vodn� osu (velk� p�smeno X, Y, Z) pom�r jej�ho &quot;p�enesen�&quot; na osy jin�.</p>

<div class="okolo_img"><img src="images/clanky/matice2/vyznam.jpg" width="400" height="200" alt="V�znam index� v matici" /></div>

<p>Mysl�m si, �e jak zach�zet s posunem je ka�d�mu jasn�, ale rotace je alespo� na prvn� pochopen� slo�it�. P�edstavme si, �e velk� p�smena X, Y, Z definuj� p�vodn� osu a mal� p�smena x, y, z ozna�uj� osu, na kterou se ta p�vodn� p�etransformuje. Pro za��tek si uk�eme, jak vypad� origin�ln� matice, kter� zobrazuje objekt nenato�en�, neposunut� a ani nezmen�en�. Kdy� s n� n�sob�me bod, je to, jako bychom ho n�sobili jedni�kou. Jeho sou�adnice se tedy nezm�n�. Tuto matici v OpenGL generuje funkce glLoadIdentity().</p>

<p>V�imn�te si, �e ve v�ech Rot jsou jedni�ky na hlavn� diagon�le matice. Tedy tam, kde se shoduje mal� a velk� p�smeno - Xx, Yy, Zz. Ve v�sledn�m zobrazen� budou m�t osy stejn� m���tko, polohu i nato�en� jako m� absolutn� soustava sou�adnic.</p>

<p class="src0">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<div class="okolo_img"><img src="images/clanky/matice2/reset.jpg" width="400" height="200" alt="Hodnoty v matici po resetu" /></div>

<p>Te� si uk�eme, co se stane, kdybychom cht�li objekt oto�it o 90� na ose Y.</p>

<p class="src0">glLoadIdentity();</p>
<p class="src0">glRotatef(90.0f, 0.0f, 1.0f, 0.0f);</p>

<div class="okolo_img"><img src="images/clanky/matice2/roty90.jpg" width="400" height="200" alt="Hodnoty v matici po rotaci o 90 stup�� na ose y" /></div>

<p>Tak u� to b�v� na sv�t�, �e vin�k unik� bez trestu a v�ichni okolo to schytaj�. Ano, vid�te dob�e, s osou Y se nic nestalo, proto�e jsme se okolo n� oto�ili o 90�. Kdy� si to p�edstav�me v prostoru: co se stane s bodem na ose x, pokud p�ed vykreslen�m provedeme rotaci okolo osy Y o 90�? Z osy X se p�esune na osu Z. Kdy� se pod�v�te, p�vodn� osa X (velk� p�smeno) se dostala na osu z (mal� p�smeno). To sam� se stalo s p�vodn� osou Z kter� se dostala na z�pornou ��st osy x. Pokud bychom rotovali opa�n�m sm�rem, tedy o -90� na ose Y, v RotXz by byla hodnota -1 a v RotZx by bylo 1, co� je p�esn� opak p�edchoz�ho p��padu.</p>

<p>Pro ty z v�s, kte�� si nedok�� p�edstavit, jak by se mohla jedna osa p�en�st na jinou, si uk�eme obr�zek, na kter�m jde v�echno bez probl�m� vid�t.</p>

<div class="okolo_img"><img src="images/clanky/matice2/3dosy.jpg" width="464" height="177" alt="Poloha sou�adnicov�ch os" /></div>

<p>U� ch�pete, jak se dostal bod z jedn� osy na druhou? Stejn� to funguje i s maticemi. Ka�dou ze t�� os si m��eme p�edstavit jako skupinu bod� v prostoru, kter� ot���me o n�jak� �hel. P�esn� takto jsem se dop�tral k funk�n�mu k�du.</p>

<p>Je samoz�ejm� jasn�, �e se v�e neot��� jen o 90�, ale musel jsem to n�jak vysv�tlit. Jen pro uk�zku se pod�vejme, co se stane pokud rotujeme okolo osy Y o 45�.</p>

<p class="src0">glLoadIdentity();</p>
<p class="src0">glRotatef(45.0f, 0.0f, 1.0f, 0.0f);</p>

<div class="okolo_img"><img src="images/clanky/matice2/roty45.jpg" width="400" height="200" alt="Hodnoty v matici po rotaci o 45 stup��" /></div>

<p>Asi v�s n�kter� napadlo, �e kdy� je �hel polovi�n� ne� p�edt�m, pro� na indexech nejsou hodnoty 0,5 nam�sto 0,707. Je to proto, �e v�po�et t�chto hodnot prov�d�me pomoc� goniometrick�ch funkc� sin a cos a ty, jak v�me, nejsou line�rn�. Pokud by byly, doch�zelo by k deformac�m obrazu, zmen�ov�n� atd... P�edstavte si kru�nici vykreslenou pomoc� sin a cos. Kdyby byly tyto funkce line�rn�, vznikl by �tverec. Ot���me-li body v rovin�, pohybujeme se po kru�nici, ve 3D prostoru po kouli.</p>

<p>Nesm�me zapomenout na zm�nu m���tka. N�sleduj�c� obr�zek ukazuje, jak bude matice vypadat po jednon�sobn�m zv�t�en� na ose x (z�st�v� stejn�), dvojn�sobn�m na ose y a trojn�sobn�m na ose z. V OpenGL by se to provedlo vol�n�m funkce glScalef().</p>

<p class="src0">glLoadIdentity();</p>
<p class="src0">glScalef(1.0f, 2.0f, 3.0f);</p>

<div class="okolo_img"><img src="images/clanky/matice2/scale.jpg" width="400" height="200" alt="Zm�na m���tka" /></div>

<p>Posledn� p��klad ukazuje sou�asnou zm�nu m���tka a rotaci na ose y o 45�.</p>

<p class="src0">glLoadIdentity();</p>
<p class="src0">glScalef(1.0f, 2.0f, 3.0f);</p>
<p class="src0">glRotatef(45.0f, 0.0f, 1.0f, 0.0f);</p>

<div class="okolo_img"><img src="images/clanky/matice2/scale_rot.jpg" width="400" height="200" alt="Zm�na m���tka a rotace" /></div>

<p>To je snad v�e o teorii a te� honem ke zdrojov�m k�d�m - sl�ben� funkce pro rotaci. Mus�m jen dodat, �e nejsou shodn� s OpenGL glRotatef(), ve kter� se objekt po posunut� ot��� podle st�edu sc�ny, transformuje tedy i svou pozici. Tyto funkce to ned�laj� a mysl�m, �e je to tak lep�� (z�le�� na zvyku). Pokud pot�ebujete p�etransformovat i pozici (nap��klad kdy� je p�ipevn�n k jin�mu objektu ve sc�n�), jednodu�e si vypo��t�te rotaci a pak pozici vyn�sob�te matic� stejn� jako norm�ln� bod v prostoru.</p>

<p class="src0">void RV6_MATRIX::RotateX(float Angle)<span class="kom">// Rotace na ose x</span></p>
<p class="src0">{</p>
<p class="src1">float p;</p>
<p class="src1">float _sin = sinf(-Angle);</p>
<p class="src1">float _cos = cosf(-Angle);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa X na X</span></p>
<p class="src1">p = Matrix[4];</p>
<p class="src1">Matrix[4] = p * _cos - Matrix[8] * _sin;</p>
<p class="src1">Matrix[8] = p * _sin + Matrix[8] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa X na Y</span></p>
<p class="src1">p = Matrix[5];</p>
<p class="src1">Matrix[5] = p * _cos - Matrix[9] * _sin;</p>
<p class="src1">Matrix[9] = p * _sin + Matrix[9] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa X na Z</span></p>
<p class="src1">p = Matrix[6];</p>
<p class="src1">Matrix[6] = p * _cos - Matrix[10] * _sin;</p>
<p class="src1">Matrix[10] = p * _sin + Matrix[10] * _cos;</p>
<p class="src0">}</p>

<p class="src"></p>

<p class="src0">void RV6_MATRIX::RotateY(float Angle)<span class="kom">// Rotace na ose y</span></p>
<p class="src0">{</p>
<p class="src1">float p, _sin = sinf(Angle), _cos = cosf(Angle);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Y na X</span></p>
<p class="src1">p = Matrix[0];</p>
<p class="src1">Matrix[0] = p * _cos - Matrix[8] * _sin;</p>
<p class="src1">Matrix[8] = p * _sin + Matrix[8] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Y na Y</span></p>
<p class="src1">p = Matrix[1];</p>
<p class="src1">Matrix[1] = p * _cos - Matrix[9] * _sin;</p>
<p class="src1">Matrix[9] = p * _sin + Matrix[9] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Y na Z</span></p>
<p class="src1">p = Matrix[2];</p>
<p class="src1">Matrix[2] = p * _cos - Matrix[10] * _sin;</p>
<p class="src1">Matrix[10] = p * _sin + Matrix[10] * _cos;</p>
<p class="src0">}</p>

<p class="src"></p>

<p class="src0">void RV6_MATRIX::RotateZ(float Angle)<span class="kom">// Rotace na ose z</span></p>
<p class="src0">{</p>
<p class="src1">float p, _sin = sinf(-Angle), _cos = cosf(-Angle);</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Z na X</span></p>
<p class="src1">p = Matrix[0];</p>
<p class="src1">Matrix[0] = p * _cos - Matrix[4] * _sin;</p>
<p class="src1">Matrix[4] = p * _sin + Matrix[4] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Z na X</span></p>
<p class="src1">p = Matrix[1];</p>
<p class="src1">Matrix[1] = p * _cos - Matrix[5] * _sin;</p>
<p class="src1">Matrix[5] = p * _sin + Matrix[5] * _cos;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Osa Z na X</span></p>
<p class="src1">p = Matrix[2];</p>
<p class="src1">Matrix[2] = p * _cos - Matrix[6] * _sin;</p>
<p class="src1">Matrix[6] = p * _sin + Matrix[6] * _cos;</p>
<p class="src0">}</p>

<p class="src"></p>

<p class="src0">void RV6_MATRIX::Rotate(float x, float y, float z)<span class="kom">// Hlavn� funkce pro rotaci</span></p>
<p class="src0">{</p>
<p class="src1">if(x)</p>
<p class="src1">{</p>
<p class="src2">RotateX(x);</p>
<p class="src1">}</p>
<p class="src1">if(y)</p>
<p class="src1">{</p>
<p class="src2">RotateY(y);</p>
<p class="src1">}</p>
<p class="src1">if(z)</p>
<p class="src1">{</p>
<p class="src2">RotateZ(z);</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>To, co vid�te jsou probd�l� noci, vyplakan� litry slz, p�tky ve �kole... Jak vid�te je to �ryvek ze t��dy, kter� obsahuje jen pole o 16 prvc�ch se jm�nem Matrix. Toto mu�en� by bylo na nic, kdyby se nedalo n�jak pou��t. Proto zde uvedu dal�� funkci, kter� podle matice p�etransformuje vertex �i vektor (u vektoru je ale lep�� vytvo�it funkci, ve kter� se nebudou p�i��tat pozice) na absolutn� sou�adnice. Jen pro po��dek, t��da RV6_VECTOR3 obsahuje t�i ��sla x, y, z.</p>

<p class="src0">void RV6_MATRIX::TransformVertex(RV6_VECTOR3 *Vertex)<span class="kom">// Transformuje vertex podle matice</span></p>
<p class="src0">{</p>
<p class="src1">RV6_VECTOR3 New;</p>
<p class="src"></p>
<p class="src1">New.x = Vertex->x * Matrix[0] + Vertex->y * Matrix[4] + Vertex->z * Matrix[8] + Matrix[12];</p>
<p class="src1">New.y = Vertex->x * Matrix[1] + Vertex->y * Matrix[5] + Vertex->z * Matrix[9] + Matrix[13];</p>
<p class="src1">New.z = Vertex->x * Matrix[2] + Vertex->y * Matrix[6] + Vertex->z * Matrix[10] + Matrix[14];</p>
<p class="src"></p>
<p class="src1">*Vertex = New;</p>
<p class="src0">}</p>

<p>Dal�� d�le�itou v�c� je nahr�n� na�� matice do OpenGL. K tomu slou�� standardn� funkce glLoadMatrixf(). Posledn� p�smeno v jej�m n�zvu znamen� float, pokud po��v�te double mus�te jej zam�nit za d.</p>

<p class="src0">glLoadMatrixf(Matrix);<span class="kom">// Uploadov�n� matice do OpenGL</span></p>

<p>Pokud chcete naopak na��st matici, pou�ijte funkci glGetFloatv(). Prvn� parametr ozna�uje, kterou matici ��d�me (GL_MODELVIEW_MATRIX, GL_PROJECTION_MATRIX nebo GL_TEXTURE_MATRIX) a druh� pole, kam se maj� data ulo�it.</p>

<p class="src0">glGetFloatv(GL_MODELVIEW_MATRIX, Matrix);<span class="kom">// Z�sk�n� OpenGL matice</span></p>

<p>Kdy� si to trochu shrneme... nikdy nezapome�te p�ed prvn� operac� uv�st matici do z�kladn�ho stavu, kdy jsou na hlavn� diagon�le jedni�ky a v�ude jinde nuly, jinak by se v�m nic nezobrazovalo a ani nerotovalo. D�le chcete asi v�d�t k �emu slou�� posledn� sloupec v matici. P�izn�m se, �e nev�m, ale prost� tam je. Ve �kole jsme matice je�t� nebrali, v�e jsem zkoumal grabov�n�m hodnot z OpenGL a jejich v�pisem do souboru.</p>

<p>To je snad v�e k matic�m, jestli jim je�t� nech�pete, zkuste si tento �l�nek p�e��st je�t� jednou (pozor na nekone�n� cyklus :-) nebo donu�te n�koho a� v�m vysv�tl� co a jak. J� u� asi l�pe vysv�tlovat nedok�i. Douf�m, �e jsem alespo� n�komu pomohl.</p>

<p class="autor">napsal: Radom�r Vr�na <?VypisEmail('rvalien@c-box.cz?subject=�l�nek - Matice');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojov�ch k�d�</li>
</ul>

<p>Tento �l�nek byl naps�n pro web <?OdkazBlank('http://nehe.ceske-hry.cz/');?>. Pokud ho chcete um�stit i na svoje str�nky, nap�ed se zeptejte autora, je to slu�nost.</p>

<?
include 'p_end.php';
?>
