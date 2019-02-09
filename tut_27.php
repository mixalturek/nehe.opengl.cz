<?
$g_title = 'CZ NeHe OpenGL - Lekce 27 - St�ny';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(27);?>

<h1>Lekce 27 - St�ny</h1>

<p class="nadpis_clanku">P�edstavuje se v�m velmi komplexn� tutori�l na vrh�n� st�n�. Efekt je doslova neuv��iteln�. St�ny se roztahuj�, oh�baj� a zahaluj� i ostatn� objekty ve sc�n�. Realisticky se pokrout� na st�n�ch nebo podlaze. Se v��m lze pomoc� kl�vesnice pohybovat ve 3D prostoru. Pokud je�t� nejste se stencil bufferem a matematikou jako jedna rodina, nem�te nejmen�� �anci.</p>

<p>Tento tutori�l m� trochu jin� p��stup - sumarizuje v�echny va�e znalosti o OpenGL a p�id�v� spoustu dal��ch. Ka�dop�dn� byste m�li stoprocentn� ch�pat nastavov�n� a pr�ci se stencil bufferem. Pokud m�te pocit, �e v n��em existuj� mezery, zkuste se vr�tit ke �ten� d��v�j��ch lekc�. Mimo jin� byste tak� m�li m�t alespo� mal� znalosti o analytick� geometrii (vektory, rovnice p��mek a rovin, n�soben� matic...) - ur�it� m�jte po ruce n�jakou knihu. J� osobn� pou��v�m z�pisky z matematiky prvn�ho semestru na univerzit�. V�dy jsem v�d�l, �e se n�kdy budou hodit.</p>

<p>Nyn� u� ale ke k�du. Aby byl program p�ehledn�, definujeme n�kolik struktur. Prvn� z nich, sPoint, vyjad�uje bod nebo vektor v prostoru. Ukl�d� jeho x, y, z sou�adnice.</p>

<p class="src0">struct sPoint<span class="kom">// Sou�adnice bodu nebo vektoru</span></p>
<p class="src0">{</p>
<p class="src1">float x, y, z;</p>
<p class="src0">};</p>

<p>Struktura sPlaneEq ukl�d� hodnoty a, b, c, d obecn� rovnice roviny, kter� je definov�na vzorcem ax + by + cz + d = 0.</p>

<p class="src0">struct sPlaneEq<span class="kom">// Rovnice roviny</span></p>
<p class="src0">{</p>
<p class="src1">float a, b, c, d;<span class="kom">// Ve tvaru ax + by + cz + d = 0</span></p>
<p class="src0">};</p>

<p>Struktura sPlane obsahuje v�echny informace pot�ebn� k pops�n� troj�heln�ku, kter� vrh� st�n. Instance t�chto struktur budou reprezentovat facy (�elo, st�na - nebudu p�ekl�dat, proto�e je tento term�n hodn� pou��van� i v �e�tin�) troj�heln�k�. Facem se rozum� st�na troj�heln�ku, kter� je p�ivr�cen� nebo odvr�cen� od pozorovatele. Jeden troj�heln�k m� v�dy dva facy.</p>

<p>Pole p[3] definuje t�i indexy v poli vertex� objektu, kter� dohromady tvo�� tento troj�heln�k. Druh� trojrozm�rn� pole, normals[3], zastupuje norm�lov� vektor ka�d�ho rohu. T�et� pole specifikuje indexy sousedn�ch fac�. PlaneEq ur�uje rovnici roviny, ve kter� le�� tento face a parametr visible oznamuje, jestli je face p�ivr�cen� (viditeln�) ke zdroji sv�tla nebo ne.</p>

<p class="src0">struct sPlane<span class="kom">// Popisuje jeden face objektu</span></p>
<p class="src0">{</p>
<p class="src1">unsigned int p[3];<span class="kom">// Indexy 3 vertex� v objektu, kter� vytv��ej� tento face</span></p>
<p class="src1">sPoint normals[3];<span class="kom">// Norm�lov� vektory ka�d�ho vertexu</span></p>
<p class="src1">unsigned int neigh[3];<span class="kom">// Indexy sousedn�ch fac�</span></p>
<p class="src"></p>
<p class="src1">sPlaneEq PlaneEq;<span class="kom">// Rovnice roviny facu</span></p>
<p class="src1">bool visible;<span class="kom">// Je face viditeln� (p�ivr�cen� ke sv�tlu)?</span></p>
<p class="src0">};</p>

<p>Posledn� struktura, glObject, je mezi pr�v� definovan�mi strukturami na nejvy��� �rovni. Prom�nn� nPoints a nPlanes ur�uj� po�et prvk�, kter� pou��v�me v pol�ch points a planes.</p>

<p class="src0">struct glObject<span class="kom">// Struktura objektu</span></p>
<p class="src0">{</p>
<p class="src1">GLuint nPoints;<span class="kom">// Po�et vertex�</span></p>
<p class="src1">sPoint points[100];<span class="kom">// Pole vertex�</span></p>
<p class="src"></p>
<p class="src1">GLuint nPlanes;<span class="kom">// Po�et fac�</span></p>
<p class="src1">sPlane planes[200];<span class="kom">// Pole fac�</span></p>
<p class="src0">};</p>

<p>GLvector4f a GLmatrix16f jsou pomocn� datov� typy, kter� definujeme pro snadn�j�� p�ed�v�n� parametr� funkci VMatMult(). V�ce pozd�ji.</p>

<p class="src0">typedef float GLvector4f[4];<span class="kom">// Nov� datov� typ</span></p>
<p class="src0">typedef float GLmatrix16f[16];<span class="kom">// Nov� datov� typ</span></p>

<p>Nadefinujeme prom�nn�. Obj je objektem, kter� vrh� st�n. Pole ObjPos[] definuje jeho polohu, roty jsou �hlem nato�en� na os�ch x, y a speedy jsou rychlosti ot��en�.</p>

<p class="src0">glObject obj;<span class="kom">// Objekt, kter� vrh� st�n</span></p>
<p class="src"></p>
<p class="src0">float ObjPos[] = { -2.0f, -2.0f, -5.0f };<span class="kom">// Pozice objektu</span></p>
<p class="src"></p>
<p class="src0">GLfloat xrot = 0, xspeed = 0;<span class="kom">// X rotace a x rychlost rotace objektu</span></p>
<p class="src0">GLfloat yrot = 0, yspeed = 0;<span class="kom">// Y rotace a y rychlost rotace objektu</span></p>

<p>N�sleduj�c� �ty�i pole definuj� sv�tlo a dal�� �ty�i pole materi�l. Pou�ijeme je p�edev��m v InitGL() p�i inicializaci sc�ny.</p>

<p class="src0">float LightPos[] = { 0.0f, 5.0f,-4.0f, 1.0f };<span class="kom">// Pozice sv�tla</span></p>
<p class="src0">float LightAmb[] = { 0.2f, 0.2f, 0.2f, 1.0f };<span class="kom">// Ambient sv�tlo</span></p>
<p class="src0">float LightDif[] = { 0.6f, 0.6f, 0.6f, 1.0f };<span class="kom">// Diffuse sv�tlo</span></p>
<p class="src0">float LightSpc[] = { -0.2f, -0.2f, -0.2f, 1.0f };<span class="kom">// Specular sv�tlo</span></p>
<p class="src"></p>
<p class="src0">float MatAmb[] = { 0.4f, 0.4f, 0.4f, 1.0f };<span class="kom">// Materi�l - Ambient hodnoty (prost�ed�, atmosf�ra)</span></p>
<p class="src0">float MatDif[] = { 0.2f, 0.6f, 0.9f, 1.0f };<span class="kom">// Materi�l - Diffuse hodnoty (rozptylov�n� sv�tla)</span></p>
<p class="src0">float MatSpc[] = { 0.0f, 0.0f, 0.0f, 1.0f };<span class="kom">// Materi�l - Specular hodnoty (zrcadlivost)</span></p>
<p class="src0">float MatShn[] = { 0.0f };<span class="kom">// Materi�l - Shininess hodnoty (lesk)</span></p>

<p>Posledn� dv� prom�nn� jsou pro kouli, na kterou dopad� st�n objektu.</p>

<p class="src0">GLUquadricObj *q;<span class="kom">// Quadratic pro kreslen� koule</span></p>
<p class="src0">float SpherePos[] = { -4.0f, -5.0f, -6.0f };<span class="kom">// Pozice koule</span></p>

<p>Struktura datov�ho souboru, kter� pou��v�me pro definici objektu, nen� a� tak slo�it�, jak na prvn� pohled vypad�. Soubor se d�l� do dvou ��st�: jedna ��st pro vertexy a  druh� pro facy. Prvn� ��slo prvn� ��sti ur�uje po�et vertex� a po n�m n�sleduj� jejich definice. Druh� ��st za��n� specifikac� po�tu fac�. Na ka�d�m dal��m ��dku je celkem dvan�ct ��sel. Prvn� t�i p�edstavuj� indexy do pole vertex� (ka�d� face m� t�i vrcholy) a zbyl�ch dev�t hodnot ur�uje t�i norm�lov� vektory (pro ka�d� vrchol jeden). To je v�e. Abych nezapomn�l v adres��i Data m��ete naj�t je�t� t�i podobn� soubory.</p>

<p class="src0"><span class="kom">24</span></p>
<p class="src0"><span class="kom">-2  0.2 -0.2</span></p>
<p class="src0"><span class="kom">2  0.2 -0.2</span></p>
<p class="src0"><span class="kom">2  0.2  0.2</span></p>
<p class="src0"><span class="kom">-2  0.2  0.2</span></p>
<p class="src0"><span class="kom">-2 -0.2 -0.2</span></p>
<p class="src0"><span class="kom">2 -0.2 -0.2</span></p>
<p class="src0"><span class="kom">2 -0.2  0.2</span></p>
<p class="src0"><span class="kom">-2 -0.2  0.2</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">-0.2  2 -0.2</span></p>
<p class="src0"><span class="kom">0.2  2 -0.2</span></p>
<p class="src0"><span class="kom">0.2  2  0.2</span></p>
<p class="src0"><span class="kom">0.2  2  0.2</span></p>
<p class="src0"><span class="kom">-0.2 -2 -0.2</span></p>
<p class="src0"><span class="kom">0.2 -2 -0.2</span></p>
<p class="src0"><span class="kom">0.2 -2  0.2</span></p>
<p class="src0"><span class="kom">-0.2 -2  0.2</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">-0.2  0.2 -2</span></p>
<p class="src0"><span class="kom">0.2  0.2 -2</span></p>
<p class="src0"><span class="kom">0.2  0.2  2</span></p>
<p class="src0"><span class="kom">-0.2  0.2  2</span></p>
<p class="src0"><span class="kom">-0.2 -0.2 -2</span></p>
<p class="src0"><span class="kom">0.2 -0.2 -2</span></p>
<p class="src0"><span class="kom">0.2 -0.2  2</span></p>
<p class="src0"><span class="kom">-0.2 -0.2  2</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">36</span></p>
<p class="src0"><span class="kom">1 3 2 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">1 4 3 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">5 6 7 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">5 7 8 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">5 4 1 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">5 8 4 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">3 6 2 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">3 7 6 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">5 1 2 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">5 2 6 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">3 4 8 0 0 1 0 0 1 0 0 1</span></p>
<p class="src0"><span class="kom">3 8 7 0 0 1 0 0 1 0 0 1</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">9 11 10 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">9 12 11 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">13 14 15 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">13 15 16 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">13 12 9 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">13 16 12 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">11 14 10 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">11 15 14 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">13 9 10 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">13 10 14 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">11 12 16 0 0 1 0 0 1 0 0 1</span></p>
<p class="src0"><span class="kom">11 16 15 0 0 1 0 0 1 0 0 1</span></p>
<p class="src"></p>
<p class="src0"><span class="kom">17 19 18 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">17 20 19 0 1 0 0 1 0 0 1 0</span></p>
<p class="src0"><span class="kom">21 22 23 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">21 23 24 0 -1 0 0 -1 0 0 -1 0</span></p>
<p class="src0"><span class="kom">21 20 17 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">21 24 20 -1 0 0 -1 0 0 -1 0 0</span></p>
<p class="src0"><span class="kom">19 22 18 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">19 23 22 1 0 0 1 0 0 1 0 0</span></p>
<p class="src0"><span class="kom">21 17 18 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">21 18 22 0 0 -1 0 0 -1 0 0 -1</span></p>
<p class="src0"><span class="kom">19 20 24 0 0 1 0 0 1 0 0 1</span></p>
<p class="src0"><span class="kom">19 24 23 0 0 1 0 0 1 0 0 1</span></p>

<p>Pr�v� p�edstaven� soubor nahr�v� funkce ReadObject(). Pro pochopen� podstaty by m�ly sta�it koment��e.</p>

<p class="src0">inline int ReadObject(char *st, glObject *o)<span class="kom">// Nahraje objekt</span></p>
<p class="src0">{</p>
<p class="src1">FILE *file;<span class="kom">// Handle souboru</span></p>
<p class="src1">unsigned int i;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src"></p>
<p class="src1">file = fopen(st, &quot;r&quot;);<span class="kom">// Otev�e soubor pro �ten�</span></p>
<p class="src"></p>
<p class="src1">if (!file)<span class="kom">// Poda�ilo se ho otev��t?</span></p>
<p class="src2">return FALSE;<span class="kom">// Pokud ne - konec funkce</span></p>
<p class="src"></p>
<p class="src1">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;nPoints));<span class="kom">// Na�ten� po�tu vertex�</span></p>
<p class="src"></p>
<p class="src1">for (i = 1; i &lt;= o-&gt;nPoints; i++)<span class="kom">// Na��t� vertexy</span></p>
<p class="src1">{</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;points[i].x));<span class="kom">// Jednotliv� x, y, z slo�ky</span></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;points[i].y));</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;points[i].z));</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;nPlanes));<span class="kom">// Na�ten� po�tu fac�</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; o-&gt;nPlanes; i++)<span class="kom">// Na��t� facy</span></p>
<p class="src1">{</p>
<p class="src2">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;planes[i].p[0]));<span class="kom">// Na�ten� index� vertex�</span></p>
<p class="src2">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;planes[i].p[1]));</p>
<p class="src2">fscanf(file, &quot;%d&quot;, &amp;(o-&gt;planes[i].p[2]));</p>
<p class="src"></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[0].x));<span class="kom">// Norm�lov� vektory prvn�ho vertexu</span></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[0].y));</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[0].z));</p>
<p class="src"></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[1].x));<span class="kom">// Norm�lov� vektory druh�ho vertexu</span></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[1].y));</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[1].z));</p>
<p class="src"></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[2].x));<span class="kom">// Norm�lov� vektory t�et�ho vertexu</span></p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[2].y));</p>
<p class="src2">fscanf(file, &quot;%f&quot;, &amp;(o-&gt;planes[i].normals[2].z));</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�e v po��dku</span></p>
<p class="src0">}</p>

<p>D�ky funkci SetConnectivity() za��naj� b�t v�ci zaj�mav� :-) Hled�me v n� ke ka�d�mu facu t�i sousedn� facy, se kter�mi m� spole�nou hranu. Proto�e je zdrojov� k�d, abych tak �ekl, trochu h��e pochopiteln�, p�id�v�m i pseudo k�d, kter� by mohl situaci mali�ko objasnit.</p>

<p class="src0"><span class="kom">Za��tek funkce</span></p>
<p class="src0"><span class="kom">{</span></p>
<p class="src1"><span class="kom">Postupn� se proch�z� ka�d� face (A) v objektu</span></p>
<p class="src1"><span class="kom">{</span></p>
<p class="src2"><span class="kom">V ka�d�m pr�chodu se znovu proch�z� v�echny facy (B) objektu (zji��uje se sousedstv� A s B)</span></p>
<p class="src2"><span class="kom">{</span></p>
<p class="src3"><span class="kom">D�le se projdou v�echny hrany facu A</span></p>
<p class="src3"><span class="kom">{</span></p>
<p class="src4"><span class="kom">Pokud aktu�ln� hrana je�t� nem� p�i�azen�ho souseda</span></p>
<p class="src4"><span class="kom">{</span></p>
<p class="src5"><span class="kom">Projdou se v�echny hrany facu B</span></p>
<p class="src5"><span class="kom">{</span></p>
<p class="src6"><span class="kom">Provedou se v�po�ty, kter�mi se zjist�, jestli je okraj A stejn� jako okraj B</span></p>
<p class="src6"><span class="kom">Pokud ano</span></p>
<p class="src6"><span class="kom">{</span></p>
<p class="src7"><span class="kom">Nastav� se soused v A</span></p>
<p class="src7"><span class="kom">Nastav� se soused v B</span></p>
<p class="src6"><span class="kom">}</span></p>
<p class="src5"><span class="kom">}</span></p>
<p class="src4"><span class="kom">}</span></p>
<p class="src3"><span class="kom">}</span></p>
<p class="src2"><span class="kom">}</span></p>
<p class="src1"><span class="kom">}</span></p>
<p class="src0"><span class="kom">}</span></p>
<p class="src0"><span class="kom">Konec funkce</span></p>

<p>U� ch�pete?</p>

<p class="src0">inline void SetConnectivity(glObject *o)<span class="kom">// Nastaven� soused� jednotliv�ch fac�</span></p>
<p class="src0">{</p>
<p class="src1">unsigned int p1i, p2i, p1j, p2j;<span class="kom">// Pomocn� prom�nn�</span></p>
<p class="src1">unsigned int P1i, P2i, P1j, P2j;<span class="kom">// Pomocn� prom�nn�</span></p>
<p class="src1">unsigned int i, j, ki, kj;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src"></p>
<p class="src1">for(i = 0; i &lt; o-&gt;nPlanes-1; i++)<span class="kom">// Ka�d� face objektu (A)</span></p>
<p class="src1">{</p>
<p class="src2">for(j = i+1; j &lt; o-&gt;nPlanes; j++)<span class="kom">// Ka�d� face objektu (B)</span></p>
<p class="src2">{</p>
<p class="src3">for(ki = 0; ki &lt; 3; ki++)<span class="kom">// Ka�d� okraj facu (A)</span></p>
<p class="src3">{</p>
<p class="src4">if(!o-&gt;planes[i].neigh[ki])<span class="kom">// Okraj je�t� nem� souseda?</span></p>
<p class="src4">{</p>
<p class="src5">for(kj = 0; kj &lt; 3; kj++)<span class="kom">// Ka�d� okraj facu (B)</span></p>
<p class="src5">{</p>

<p>Nalezen�m dvou vertex�, kter� ozna�uj� konce hrany a jejich porovn�n�m m��eme zjistit, jestli maj� spole�n� okraj. ��st (kj+1) % 3 ozna�uje vertex um�st�n� vedle toho, o kter�m uva�ujeme. Ov���me, jestli jsou vertexy stejn�. Proto�e m��e b�t jejich po�ad� rozd�ln� mus�me testovat ob� mo�nosti.</p>

<p class="src6"><span class="kom">// V�po�ty pro zji�t�n� sousedstv�</span></p>
<p class="src6">p1i = ki;</p>
<p class="src6">p1j = kj;</p>
<p class="src"></p>
<p class="src6">p2i = (ki+1) % 3;</p>
<p class="src6">p2j = (kj+1) % 3;</p>
<p class="src"></p>
<p class="src6">p1i = o-&gt;planes[i].p[p1i];</p>
<p class="src6">p2i = o-&gt;planes[i].p[p2i];</p>
<p class="src6">p1j = o-&gt;planes[j].p[p1j];</p>
<p class="src6">p2j = o-&gt;planes[j].p[p2j];</p>
<p class="src"></p>
<p class="src6">P1i = ((p1i+p2i) - abs(p1i-p2i)) / 2;</p>
<p class="src6">P2i = ((p1i+p2i) + abs(p1i-p2i)) / 2;</p>
<p class="src6">P1j = ((p1j+p2j) - abs(p1j-p2j)) / 2;</p>
<p class="src6">P2j = ((p1j+p2j) + abs(p1j-p2j)) / 2;</p>
<p class="src"></p>
<p class="src6">if((P1i == P1j) &amp;&amp; (P2i == P2j))<span class="kom">// Jsou soused�?</span></p>
<p class="src6">{</p>
<p class="src7">o-&gt;planes[i].neigh[ki] = j+1;</p>
<p class="src7">o-&gt;planes[j].neigh[kj] = i+1;</p>
<p class="src6">}</p>
<p class="src5">}</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Abychom se mohli alespo� trochu nadechnout :-) vyp��i k�d funkce DrawGLObject(), kter� je na prvn� pohled mali�ko jednodu���. Jak u� z n�zvu vypl�v�, vykresluje objekt.</p>

<p class="src0">void DrawGLObject(glObject o)<span class="kom">// Vykreslen� objektu</span></p>
<p class="src0">{</p>
<p class="src1">unsigned int i, j;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Kreslen� troj�heln�k�</span></p>
<p class="src2">for (i = 0; i &lt; o.nPlanes; i++)<span class="kom">// Projde v�echny facy</span></p>
<p class="src2">{</p>
<p class="src3">for (j = 0; j &lt; 3; j++)<span class="kom">// Troj�heln�k m� t�i rohy</span></p>
<p class="src3">{</p>
<p class="src4"><span class="kom">// Norm�lov� vektor a um�st�n� bodu</span></p>
<p class="src4">glNormal3f(o.planes[i].normals[j].x, o.planes[i].normals[j].y, o.planes[i].normals[j].z);</p>
<p class="src4">glVertex3f(o.points[o.planes[i].p[j]].x, o.points[o.planes[i].p[j]].y, o.points[o.planes[i].p[j]].z);</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">glEnd();</p>
<p class="src0">}</p>

<p>V�po�et rovnice roviny vypad� pro ne-matematika sice hodn� slo�it�, ale je to pouze implementace matematick�ho vzorce, kter� se, kdy� je pot�eba, najde v tabulk�ch nebo kn��ce.</p>

<p>P�ekl.: Mali�k� chybi�ka. Pole v[] m� rozsah �ty�i prvky, ale pou��vaj� se jenom t�i. Index 0 se nikdy nepou�ije.</p>

<p class="src0">inline void CalcPlane(glObject o, sPlane *plane)<span class="kom">// Rovnice roviny ze t�� bod�</span></p>
<p class="src0">{</p>
<p class="src1">sPoint v[4];<span class="kom">// Pomocn� hodnoty</span></p>
<p class="src1">int i;<span class="kom">// ��d�c� prom�nn� cykl�</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; 3; i++)<span class="kom">// Pro zkr�cen� z�pisu</span></p>
<p class="src1">{</p>
<p class="src2">v[i+1].x = o.points[plane-&gt;p[i]].x;<span class="kom">// Ulo�� hodnoty do pomocn�ch prom�nn�ch</span></p>
<p class="src2">v[i+1].y = o.points[plane-&gt;p[i]].y;</p>
<p class="src2">v[i+1].z = o.points[plane-&gt;p[i]].z;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">plane-&gt;PlaneEq.a = v[1].y*(v[2].z-v[3].z) + v[2].y*(v[3].z-v[1].z) + v[3].y*(v[1].z-v[2].z);</p>
<p class="src1">plane-&gt;PlaneEq.b = v[1].z*(v[2].x-v[3].x) + v[2].z*(v[3].x-v[1].x) + v[3].z*(v[1].x-v[2].x);</p>
<p class="src1">plane-&gt;PlaneEq.c = v[1].x*(v[2].y-v[3].y) + v[2].x*(v[3].y-v[1].y) + v[3].x*(v[1].y-v[2].y);</p>
<p class="src1">plane-&gt;PlaneEq.d = -( v[1].x*(v[2].y*v[3].z - v[3].y*v[2].z) + v[2].x*(v[3].y*v[1].z - v[1].y*v[3].z) + v[3].x*(v[1].y*v[2].z - v[2].y*v[1].z) );</p>
<p class="src0">}</p>

<p>Funkce, kter� jsme pr�v� napsali se volaj� ve funkci InitGLObjects(). Neexistuje-li po�adovan� soubor, vr�t�me false. Pokud ale existuje, funkc� ReadObject() ho nahrajeme do pam�ti, pomoc� SetConnectivity() najdeme soused�c� facy a potom se v cyklu spo��t�me rovnici roviny ka�d�ho facu.</p>

<p class="src0">int InitGLObjects()<span class="kom">// Inicializuje objekty</span></p>
<p class="src0">{</p>
<p class="src1">if (!ReadObject(&quot;Data/Object2.txt&quot;, &amp;obj))<span class="kom">// Nahraje objekt</span></p>
<p class="src1">{</p>
<p class="src2">return FALSE;<span class="kom">// P�i chyb� konec</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">SetConnectivity(&amp;obj);<span class="kom">// Pospojuje facy (najde sousedy)</span></p>
<p class="src"></p>
<p class="src1">for (unsigned int i = 0; i &lt; obj.nPlanes; i++)<span class="kom">// Proch�z� facy</span></p>
<p class="src2">CalcPlane(obj, &amp;(obj.planes[i]));<span class="kom">// Spo��t� rovnici roviny facu</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�e v po��dku</span></p>
<p class="src0">}</p>

<p>Nyn� p�ich�z� funkce, kter� renderuje st�n. Na za��tku nastav�me v�echny pot�ebn� parametry OpenGL a pot�, ne na obrazovku, ale do stencil bufferu, vyrenderujeme st�n. D�le vykresl�me vep�edu p�ed sc�nu velk� �ed� obd�ln�k. Tam, kde byl stencil buffer modifikov�n se zobraz� �ed� plochy - st�n.</p>

<p class="src0">void CastShadow(glObject *o, float *lp)<span class="kom">// Vr�en� st�nu</span></p>
<p class="src0">{</p>
<p class="src1">unsigned int i, j, k, jj;<span class="kom">// Pomocn�</span></p>
<p class="src1">unsigned int p1, p2;<span class="kom">// Dva body okraje vertexu, kter� vrhaj� st�n</span></p>
<p class="src1">sPoint v1, v2;<span class="kom">// Vektor mezi sv�tlem a p�edchoz�mi body</span></p>

<p>Nejprve ur��me, kter� povrchy jsou p�ivr�cen� ke sv�tlu a to tak, �e zjist�me, kter� strana facu je osv�tlen�. Provedeme to velice jednodu�e: m�me rovnici roviny (ax + by + cz + d = 0) i polohu sv�tla, tak�e dosad�me x, y, z koordin�ty sv�tla do rovnice. Nezaj�m� n�s hodnota, ale znam�nko v�sledku. Pokud bude v�sledek v�t�� ne� nula, m��� norm�lov� vektor roviny na stranu ke sv�tlu a rovina je osv�tlen�. P�i z�porn�m ��sle m��� vektor od sv�tla, rovina je od n�j odvr�cen�. Vy�el-li by v�sledek nula, bude sv�tlo le�et v rovin� facu, ale t�m se nebudeme zab�vat.</p>

<p class="src1">float side;<span class="kom">// Pomocn� prom�nn�</span></p>
<p class="src"></p>
<p class="src1">for (i = 0; i &lt; o-&gt;nPlanes; i++)<span class="kom">// Projde v�echny facy objektu</span></p>
<p class="src1">{</p>
<p class="src2"><span class="kom">// Rozhodne jestli je face p�ivr�cen� nebo odvr�cen� od sv�tla</span></p>
<p class="src2">side = o-&gt;planes[i].PlaneEq.a * lp[0] + o-&gt;planes[i].PlaneEq.b * lp[1] + o-&gt;planes[i].PlaneEq.c * lp[2] + o-&gt;planes[i].PlaneEq.d * lp[3];</p>
<p class="src"></p>
<p class="src2">if (side &gt; 0)<span class="kom">// Je p�ivr�cen�?</span></p>
<p class="src2">{</p>
<p class="src3">o-&gt;planes[i].visible = TRUE;</p>
<p class="src2">}</p>
<p class="src2">else<span class="kom">// Nen�</span></p>
<p class="src2">{</p>
<p class="src3">o-&gt;planes[i].visible = FALSE;</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Nastav�me parametry OpenGL, kter� jsou nutn� pro vr�en� st�nu. Vypneme sv�tla, proto�e nebudeme renderovat do color bufferu (v�stup na obrazovku), ale pouze do stencil bufferu. Ze stejn�ho d�vodu zak�eme pomoc� glColorMask() vykreslov�n� na obrazovku. A�koli je testov�n� hloubky st�le zapnut�, nechceme, aby st�ny byly v depth bufferu reprezentov�ny pevn�mi objekty. Jako prevenci tedy nastav�me masku hloubky na GL_FALSE. Nakonec nastav�me stencil buffer tak, aby na m�sta v n�m ozna�en� mohly b�t vykresleny st�ny.</p>

<p class="src1">glDisable(GL_LIGHTING);<span class="kom">// Vypne sv�tla</span></p>
<p class="src1">glDepthMask(GL_FALSE);<span class="kom">// Vypne z�pis do depth bufferu</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Funkce depth bufferu</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_STENCIL_TEST);<span class="kom">// Zapne stencilov� testy</span></p>
<p class="src1">glColorMask(0, 0, 0, 0);<span class="kom">// Nekreslit na obrazovky</span></p>
<p class="src1">glStencilFunc(GL_ALWAYS, 1, 0xffffffff);<span class="kom">// Funkce stencilu</span></p>

<p>Proto�e m�me zapnut� o�ez�v�n� zadn�ch stran troj�heln�k� (viz. InitGL()), specifikujeme, kter� strany jsou p�edn�. Tak� nastav�me stencil buffer tak, aby se v n�m p�i kreslen� zv�t�ovaly hodnoty.</p>

<p class="src1">glFrontFace(GL_CCW);<span class="kom">// �eln� st�na proti sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_INCR);<span class="kom">// Zvy�ov�n� hodnoty stencilu</span></p>

<p>V cyklu projdeme ka�d� face a pokud je ozna�en jako viditeln� (p�ivr�cen� ke sv�tlu), zkontrolujeme v�echny jeho okraje. Pokud vedle n�j nen� ��dn� sousedn� face nebo sice m� souseda, kter� ale nen� viditeln�, na�li jsme okraj objektu, kter� vrh� st�n. Pokud se nad t�mito dv�ma podm�nkami zamysl�te, zjist�te, �e jsou pravdiv�. Z�skali jsme prvn� dv� sou�adnice �ty��heln�ku, kter� je st�nou st�nu. V tomto p��pad� si p�edstavte st�n jako oblast, kter� je ohrani�ena na jedn� stran� objektem br�n�c�m pr�chodu sv�teln�ch paprsk�, z druh� strany prom�tac� rovinou (st�na m�stnosti) a na okraj�ch �ty��heln�ky, kter� se pr�v� sna��me vykreslit. U� je to trochu jasn�j��?</p>

<p class="src1">for (i = 0; i &lt; o-&gt;nPlanes; i++)<span class="kom">// Ka�d� face objektu</span></p>
<p class="src1">{</p>
<p class="src2">if (o-&gt;planes[i].visible)<span class="kom">// Je p�ivr�cen� ke sv�tlu</span></p>
<p class="src2">{</p>
<p class="src3">for (j = 0; j &lt; 3; j++)<span class="kom">// Ka�d� okraj facu</span></p>
<p class="src3">{</p>
<p class="src4">k = o-&gt;planes[i].neigh[j];<span class="kom">// Index souseda (pomocn�)</span></p>

<p>Nyn� zjist�me, jestli je vedle aktu�ln�ho okraje face, kter� bu� nen� viditeln� nebo v�bec neexistuje (nem� souseda). Pokud podm�nka plat�, na�li jsme okraj objektu, kter� vrh� st�n.</p>

<p class="src4"><span class="kom">// Pokud nem� souseda, kter� je p�ivr�cen� ke sv�tlu</span></p>
<p class="src4">if ((!k) || (!o-&gt;planes[k-1].visible))</p>
<p class="src4">{</p>

<p>Rohy hrany pr�v� ov��ovan�ho troj�heln�ku ud�vaj� prvn� dva body st�nu. Dal�� dva z�sk�me spo��t�n�m sm�rov�ho vektoru, kter� vych�z� ze sv�tla, proch�z� bodem p1 pop�. p2 a d�ky n�soben� stem pokra�uje ve stejn�m sm�ru n�kam do hlubin sc�ny. N�soben� stem bychom si mohli p�edstavit jako m���tko pro prodlou�en� vektoru a tud�� i polygonu, aby dos�hl a� k prom�tac� rovin� a neskon�il n�kde p�ed n�.</p>

<p>Kreslen� st�nu hrubou silou pou�it� zde, nen� zrovna nejvhodn�j��, proto�e m� velmi velk� n�roky na grafickou kartu. Nekresl�me toti� pouze k prom�tac� rovin�, ale a� za ni k�d t�to lekce. ( * 100). Pro v�t�� ��innost by bylo vhodn� modifikovat tento algoritmus tak, aby se polygony st�nu o�ezaly objektem, na kter� dopad�. Tento postup by ov�em byl mnohem n�ro�n�j�� na vymy�len� a asi by byl problematick� s�m o sob�.</p>

<p class="src5"><span class="kom">// Na�li jsme okraj objektu, kter� vrh� st�n - nakresl�me polygon</span></p>
<p class="src5">p1 = o-&gt;planes[i].p[j];<span class="kom">// Prvn� bod okraje</span></p>
<p class="src5">jj = (j+1) % 3;<span class="kom">// Pro z�sk�n� druh�ho okraje</span></p>
<p class="src5">p2 = o-&gt;planes[i].p[jj];<span class="kom">// Druh� bod okraje</span></p>
<p class="src"></p>
<p class="src5"><span class="kom">// D�lka vektoru</span></p>
<p class="src5">v1.x = (o-&gt;points[p1].x - lp[0]) * 100;</p>
<p class="src5">v1.y = (o-&gt;points[p1].y - lp[1]) * 100;</p>
<p class="src5">v1.z = (o-&gt;points[p1].z - lp[2]) * 100;</p>
<p class="src"></p>
<p class="src5">v2.x = (o-&gt;points[p2].x - lp[0]) * 100;</p>
<p class="src5">v2.y = (o-&gt;points[p2].y - lp[1]) * 100;</p>
<p class="src5">v2.z = (o-&gt;points[p2].z - lp[2]) * 100;</p>

<p>Zbytek u� je celkem snadn�. M�me dva body s d�lkou a tak vykresl�me �ty��heln�k - jeden z mnoha okraj� st�nu.</p>

<p class="src5">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Nakresl� okrajov� polygon st�nu</span></p>
<p class="src6">glVertex3f(o-&gt;points[p1].x, o-&gt;points[p1].y, o-&gt;points[p1].z);</p>
<p class="src6">glVertex3f(o-&gt;points[p1].x + v1.x, o-&gt;points[p1].y + v1.y, o-&gt;points[p1].z + v1.z);</p>
<p class="src6">glVertex3f(o-&gt;points[p2].x, o-&gt;points[p2].y, o-&gt;points[p2].z);</p>
<p class="src6">glVertex3f(o-&gt;points[p2].x + v2.x, o-&gt;points[p2].y + v2.y, o-&gt;points[p2].z + v2.z);</p>
<p class="src5">glEnd();</p>

<p>V cyklech z�staneme tak dlouho, dokud nenajdeme a nevykresl�me v�echny okraje st�nu.</p>

<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>
<p class="src1">}</p>

<p>Nejjednodu��� a nejpochopiteln�j�� vysv�tlen� toho, pro� vykreslujeme to sam� je�t� jednou, je obr�zek - st�ny budou pouze tam, kde b�t maj�. P�i vykreslov�n� se nyn� budou hodnoty ve stencil bufferu sni�ovat. Tak� si v�imn�te, �e funkc� glFrontFace() budeme o�ez�vat opa�n� strany troj�heln�k�.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_27_1.jpg" width="200" height="150" alt="Bez druh�ho kreslen�" />
<img src="images/nehe_tut/tut_27_2.jpg" width="200" height="150" alt="Se druh�m kreslen�m" />
</div>

<p class="src1">glFrontFace(GL_CW);<span class="kom">// �eln� st�na po sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_DECR);<span class="kom">// Sni�ov�n� hodnoty stencilu</span></p>
<p class="src"></p>
<p class="src1">for (i=0; i &lt; o-&gt;nPlanes; i++)<span class="kom">// Ka�d� face objektu</span></p>
<p class="src1">{</p>
<p class="src2">if (o-&gt;planes[i].visible)<span class="kom">// Je p�ivr�cen� ke sv�tlu</span></p>
<p class="src2">{</p>
<p class="src3">for (j = 0; j &lt; 3; j++)<span class="kom">// Ka�d� okraj facu</span></p>
<p class="src3">{</p>
<p class="src4">k = o-&gt;planes[i].neigh[j];<span class="kom">// Index souseda (pomocn�)</span></p>
<p class="src"></p>
<p class="src4"><span class="kom">// Pokud nem� souseda, kter� je p�ivr�cen� ke sv�tlu</span></p>
<p class="src4">if ((!k) || (!o-&gt;planes[k-1].visible))</p>
<p class="src4">{</p>
<p class="src5"><span class="kom">// Na�li jsme okraj objektu, kter� vrh� st�n - nakresl�me polygon</span></p>
<p class="src5">p1 = o-&gt;planes[i].p[j];<span class="kom">// Prvn� bod okraje</span></p>
<p class="src5">jj = (j+1) % 3;<span class="kom">// Pro z�sk�n� druh�ho okraje</span></p>
<p class="src5">p2 = o-&gt;planes[i].p[jj];<span class="kom">// Druh� bod okraje</span></p>
<p class="src"></p>
<p class="src5"><span class="kom">// D�lka vektoru</span></p>
<p class="src5">v1.x = (o-&gt;points[p1].x - lp[0])*100;</p>
<p class="src5">v1.y = (o-&gt;points[p1].y - lp[1])*100;</p>
<p class="src5">v1.z = (o-&gt;points[p1].z - lp[2])*100;</p>
<p class="src"></p>
<p class="src5">v2.x = (o-&gt;points[p2].x - lp[0])*100;</p>
<p class="src5">v2.y = (o-&gt;points[p2].y - lp[1])*100;</p>
<p class="src5">v2.z = (o-&gt;points[p2].z - lp[2])*100;</p>
<p class="src"></p>
<p class="src5">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// Nakresl� okrajov� polygon st�nu</span></p>
<p class="src6">glVertex3f(o-&gt;points[p1].x, o-&gt;points[p1].y, o-&gt;points[p1].z);</p>
<p class="src6">glVertex3f(o-&gt;points[p1].x + v1.x, o-&gt;points[p1].y + v1.y, o-&gt;points[p1].z + v1.z);</p>
<p class="src6">glVertex3f(o-&gt;points[p2].x, o-&gt;points[p2].y, o-&gt;points[p2].z);</p>
<p class="src6">glVertex3f(o-&gt;points[p2].x + v2.x, o-&gt;points[p2].y + v2.y, o-&gt;points[p2].z + v2.z);</p>
<p class="src5">glEnd();</p>
<p class="src4">}</p>
<p class="src3">}</p>
<p class="src2">}</p>

<p class="src1">}</p>

<p>A� te� opravdu zobraz�me na sc�nu st�ny. Na �rovni roviny obrazovky vykresl�me velk�, �ed�, polopr�hledn� obd�ln�k. Zobraz� se pouze ty pixely, kter� byly pr�v� ozna�eny ve stencil bufferu (na pozici st�nu). ��m bude obd�ln�k tmav��, t�m tmav�� bude i st�n. M��ete zkusit jinou pr�hlednost nebo dokonce i barvu. Jak by se v�m l�bil �erven�, zelen� nebo modr� st�n? ��dn� probl�m!</p>

<p class="src1">glFrontFace(GL_CCW);<span class="kom">// �eln� st�na proti sm�ru hodinov�ch ru�i�ek</span></p>
<p class="src1">glColorMask(1, 1, 1, 1);<span class="kom">// Vykreslovat na obrazovku</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vykreslen� obd�ln�ku p�es celou sc�nu</span></p>
<p class="src1">glColor4f(0.0f, 0.0f, 0.0f, 0.4f);<span class="kom">// �ern�, 40% pr�hledn�</span></p>
<p class="src1">glEnable(GL_BLEND);<span class="kom">// Zapne blending</span></p>
<p class="src1">glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);<span class="kom">// Typ blendingu</span></p>
<p class="src"></p>
<p class="src1">glStencilFunc(GL_NOTEQUAL, 0, 0xffffffff);<span class="kom">// Nastaven� stencilu</span></p>
<p class="src1">glStencilOp(GL_KEEP, GL_KEEP, GL_KEEP);<span class="kom">// Nem�nit hodnotu stencilu</span></p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�� matici</span></p>
<p class="src2">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_TRIANGLE_STRIP);<span class="kom">// �ern� obd�ln�k</span></p>
<p class="src3">glVertex3f(-0.1f, 0.1f,-0.10f);</p>
<p class="src3">glVertex3f(-0.1f,-0.1f,-0.10f);</p>
<p class="src3">glVertex3f( 0.1f, 0.1f,-0.10f);</p>
<p class="src3">glVertex3f( 0.1f,-0.1f,-0.10f);</p>
<p class="src2">glEnd();</p>
<p class="src"></p>
<p class="src1">glPopMatrix();<span class="kom">// Obnov� matici</span></p>

<p>Nakonec obnov�me zm�n�n� parametry OpenGL na v�choz� hodnoty.</p>

<p class="src1"><span class="kom">// Obnov� zm�n�n� parametry OpenGL</span></p>
<p class="src1">glDisable(GL_BLEND);</p>
<p class="src1">glDepthFunc(GL_LEQUAL);</p>
<p class="src1">glDepthMask(GL_TRUE);</p>
<p class="src1">glEnable(GL_LIGHTING);</p>
<p class="src1">glDisable(GL_STENCIL_TEST);</p>
<p class="src1">glShadeModel(GL_SMOOTH);</p>
<p class="src0">}</p>

<p>DrawGLScene(), ostatn� jako v�dycky, zaji��uje v�echno vykreslov�n�. Prom�nn� Minv bude reprezentovat OpenGL matici, wlp budou lok�ln� koordin�ty a lp pomocn� pozice sv�tla.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Hlavn� vykreslovac� funkce</span></p>
<p class="src0">{</p>
<p class="src1">GLmatrix16f Minv;<span class="kom">// OpenGL matice</span></p>
<p class="src1">GLvector4f wlp, lp;<span class="kom">// Relativn� pozice sv�tla</span></p>

<p>Sma�eme obrazovkov�, hloubkov� i stencil buffer. Resetujeme matici a p�esuneme se o dvacet jednotek do obrazovky. Um�st�me sv�tlo, provedeme translaci na pozici koule a pomoc� quadraticu ji vykresl�me.</p>

<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT | GL_STENCIL_BUFFER_BIT);<span class="kom">// Sma�e buffery</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -20.0f);<span class="kom">// P�esun 20 jednotek do hloubky</span></p>
<p class="src"></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_POSITION, LightPos);<span class="kom">// Um�st�n� sv�tla</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(SpherePos[0], SpherePos[1], SpherePos[2]);<span class="kom">// Um�st�n� koule</span></p>
<p class="src1">gluSphere(q, 1.5f, 32, 16);<span class="kom">// Vykreslen� koule</span></p>

<p>Spo��t�me relativn� pozici sv�tla vzhledem k lok�ln�mu sou�adnicov�mu syst�mu objektu, kter� vrh� st�n. Do prom�nn� Min ulo��me transforma�n� matici objektu, ale obr�cenou (v�e se z�porn�mi ��sly a zad�van� opa�n�m po�ad�m), tak�e se stane invertovanou transforma�n� matic�. Z lp vytvo��me kopii pozice sv�tla a pot� ho vyn�sob�me pr�v� z�skanou OpenGL matic�. Jednodu�e �e�eno: na konci bude lp pozic� sv�tla v sou�adnicov�m syst�mu objektu.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glRotatef(-yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src1">glRotatef(-xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, Minv);<span class="kom">// Ulo�en� ModelView matice do Minv</span></p>
<p class="src"></p>
<p class="src1">lp[0] = LightPos[0];<span class="kom">// Ulo�en� pozice sv�tla</span></p>
<p class="src1">lp[1] = LightPos[1];</p>
<p class="src1">lp[2] = LightPos[2];</p>
<p class="src1">lp[3] = LightPos[3];</p>
<p class="src"></p>
<p class="src1">VMatMult(Minv, lp);<span class="kom">// Vyn�soben� pozice sv�tla OpenGL matic�</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(-ObjPos[0], -ObjPos[1], -ObjPos[2]);<span class="kom">// Posun z�porn� o pozici objektu</span></p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, Minv);<span class="kom">// Ulo�en� ModelView matice do Minv</span></p>
<p class="src"></p>
<p class="src1">wlp[0] = 0.0f;<span class="kom">// Glob�ln� koordin�ty na nulu</span></p>
<p class="src1">wlp[1] = 0.0f;</p>
<p class="src1">wlp[2] = 0.0f;</p>
<p class="src1">wlp[3] = 1.0f;</p>
<p class="src"></p>
<p class="src1">VMatMult(Minv, wlp);<span class="kom">// Origin�ln� glob�ln� sou�adnicov� syst�m relativn� k lok�ln�mu </span></p>
<p class="src"></p>
<p class="src1">lp[0] += wlp[0];<span class="kom">// Pozice sv�tla je relativn� k lok�ln�mu sou�adnicov�mu syst�mu objektu</span></p>
<p class="src1">lp[1] += wlp[1];</p>
<p class="src1">lp[2] += wlp[2];</p>

<p>Vykresl�me m�stnost s objektem a potom zavol�me funkci CastShadow(), kter� vykresl� st�n objektu. P�ed�v�me j� referenci na objekt spolu s pozic� sv�tla, kter� je nyn� ve stejn�m sou�adnicov�m syst�mu jako objekt.</p>

<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslatef(0.0f, 0.0f, -20.0f);<span class="kom">// P�esun 20 jednotek do hloubky</span></p>
<p class="src"></p>
<p class="src1">DrawGLRoom();<span class="kom">// Vykreslen� m�stnosti</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(ObjPos[0], ObjPos[1], ObjPos[2]);<span class="kom">// Um�st�n� objektu</span></p>
<p class="src1">glRotatef(xrot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src1">glRotatef(yrot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src1">DrawGLObject(obj);<span class="kom">// Vykreslen� objektu</span></p>
<p class="src"></p>
<p class="src1">CastShadow(&amp;obj, lp);<span class="kom">// Vr�en� st�nu zalo�en� na siluet�</span></p>

<p>Abychom po spu�t�n� dema vid�li, kde se pr�v� nach�z� sv�tlo, vykresl�me na jeho pozici mal� oran�ov� kruh (respektive kouli).</p>

<p class="src1">glColor4f(0.7f, 0.4f, 0.0f, 1.0f);<span class="kom">// Oran�ov� barva</span></p>
<p class="src"></p>
<p class="src1">glDisable(GL_LIGHTING);<span class="kom">// Vypne sv�tlo</span></p>
<p class="src1">glDepthMask(GL_FALSE);<span class="kom">// Vypne masku hloubky</span></p>
<p class="src"></p>
<p class="src1">glTranslatef(lp[0], lp[1], lp[2]);<span class="kom">// Translace na pozici sv�tla</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Po��d jsme v lok�ln�m sou�adnicov�m syst�mu objektu</span></p>
<p class="src1">gluSphere(q, 0.2f, 16, 8);<span class="kom">// Vykreslen� mal� koule (reprezentuje sv�tlo)</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne sv�tlo</span></p>
<p class="src1">glDepthMask(GL_TRUE);<span class="kom">// Zapne masku hloubky</span></p>

<p>Aktualizujeme rotaci objektu a ukon��me funkci.</p>

<p class="src1">xrot += xspeed;<span class="kom">// Zv�t�en� �hlu rotace objektu</span></p>
<p class="src1">yrot += yspeed;</p>
<p class="src"></p>
<p class="src1">glFlush();</p>
<p class="src1">return TRUE;<span class="kom">// V�echno v po��dku</span></p>
<p class="src0">}</p>

<p>D�le nap��eme speci�ln� funkci DrawGLRoom(), kter� vykresl� m�stnost. Je j� oby�ejn� krychle.</p>

<p class="src0">void DrawGLRoom()<span class="kom">// Vykresl� m�stnost (krychli)</span></p>
<p class="src0">{</p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src2"><span class="kom">// Podlaha</span></p>
<p class="src2">glNormal3f(0.0f, 1.0f, 0.0f);<span class="kom">// Norm�la sm��uje nahoru</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f,-20.0f);<span class="kom">// Lev� zadn�</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f, 20.0f);<span class="kom">// Lev� p�edn�</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f, 20.0f);<span class="kom">// Prav� p�edn�</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f,-20.0f);<span class="kom">// Prav� zadn�</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Strop</span></p>
<p class="src2">glNormal3f(0.0f,-1.0f, 0.0f);<span class="kom">// Norm�la sm��uje dol�</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f, 20.0f);<span class="kom">// Lev� p�edn�</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f,-20.0f);<span class="kom">// Lev� zadn�</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f,-20.0f);<span class="kom">// Prav� zadn�</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f, 20.0f);<span class="kom">// Prav� p�edn�</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// �eln� st�na</span></p>
<p class="src2">glNormal3f(0.0f, 0.0f, 1.0f);<span class="kom">// Norm�la sm��uje do hloubky</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f,-20.0f);<span class="kom">// Lev� horn�</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f,-20.0f);<span class="kom">// Lev� doln�</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f,-20.0f);<span class="kom">// Prav� doln�</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f,-20.0f);<span class="kom">// Prav� horn�</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Zadn� st�na</span></p>
<p class="src2">glNormal3f(0.0f, 0.0f,-1.0f);<span class="kom">// Norm�la sm��uje k obrazovce</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f, 20.0f);<span class="kom">// Prav� horn�</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f, 20.0f);<span class="kom">// Prav� spodn�</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f, 20.0f);<span class="kom">// Lev� spodn�</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f, 20.0f);<span class="kom">// Lev� zadn�</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Lev� st�na</span></p>
<p class="src2">glNormal3f(1.0f, 0.0f, 0.0f);<span class="kom">// Norm�la sm��uje doprava</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f, 20.0f);<span class="kom">// P�edn� horn�</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f, 20.0f);<span class="kom">// P�edn� doln�</span></p>
<p class="src2">glVertex3f(-10.0f,-10.0f,-20.0f);<span class="kom">// Zadn� doln�</span></p>
<p class="src2">glVertex3f(-10.0f, 10.0f,-20.0f);<span class="kom">// Zadn� horn�</span></p>
<p class="src"></p>
<p class="src2"><span class="kom">// Prav� st�na</span></p>
<p class="src2">glNormal3f(-1.0f, 0.0f, 0.0f);<span class="kom">// Norm�la sm��uje doleva</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f,-20.0f);<span class="kom">// Zadn� horn�</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f,-20.0f);<span class="kom">// Zadn� doln�</span></p>
<p class="src2">glVertex3f( 10.0f,-10.0f, 20.0f);<span class="kom">// P�edn� doln�</span></p>
<p class="src2">glVertex3f( 10.0f, 10.0f, 20.0f);<span class="kom">// P�edn� horn�</span></p>
<p class="src1">glEnd();<span class="kom">// Konec kreslen�</span></p>
<p class="src0">}</p>

<p>P�edt�m ne� zapomenu... v DrawGLScene() jsme pou�ili funkci VMatMult(), kter� n�sob� vektor matic�. Op�t se jedn� o implementaci vzorce z kn��ky o matematice.</p>

<p class="src0">void VMatMult(GLmatrix16f M, GLvector4f v)</p>
<p class="src0">{</p>
<p class="src1">GLfloat res[4];<span class="kom">// Ukl�d� v�sledky</span></p>
<p class="src"></p>
<p class="src1">res[0] = M[ 0]*v[0] + M[ 4]*v[1] + M[ 8]*v[2] + M[12]*v[3];</p>
<p class="src1">res[1] = M[ 1]*v[0] + M[ 5]*v[1] + M[ 9]*v[2] + M[13]*v[3];</p>
<p class="src1">res[2] = M[ 2]*v[0] + M[ 6]*v[1] + M[10]*v[2] + M[14]*v[3];</p>
<p class="src1">res[3] = M[ 3]*v[0] + M[ 7]*v[1] + M[11]*v[2] + M[15]*v[3];</p>
<p class="src"></p>
<p class="src1">v[0] = res[0];<span class="kom">// V�sledek ulo�� zp�t do v</span></p>
<p class="src1">v[1] = res[1];</p>
<p class="src1">v[2] = res[2];</p>
<p class="src1">v[3] = res[3];<span class="kom">// Homogenn� sou�adnice</span></p>
<p class="src0">}</p>

<p>V Inicializaci OpenGL nejsou t�m�� ��dn� novinky. Na za��tku nahrajeme a inicializujeme objekt, kter� vrh� st�n, potom nastav�me obvykl� parametry a sv�tla.</p>

<p class="src0">int InitGL(GLvoid)<span class="kom">// Nastaven� OpenGL</span></p>
<p class="src0">{</p>
<p class="src1">if (!InitGLObjects())<span class="kom">// Nahraje objekt</span></p>
<p class="src2">return FALSE;</p>
<p class="src"></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Jemn� st�nov�n�</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.5f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaven� hloubkov�ho bufferu</span></p>
<p class="src1">glClearStencil(0);<span class="kom">// Nastaven� stencil bufferu</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� testov�n� hloubky</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ testov�n� hloubky</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektivn� korekce</span></p>
<p class="src"></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_POSITION, LightPos);<span class="kom">// Pozice sv�tla</span></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_AMBIENT, LightAmb);<span class="kom">// Ambient sv�tlo</span></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_DIFFUSE, LightDif);<span class="kom">// Diffuse sv�tlo</span></p>
<p class="src1">glLightfv(GL_LIGHT1, GL_SPECULAR, LightSpc);<span class="kom">// Specular sv�tlo</span></p>
<p class="src"></p>
<p class="src1">glEnable(GL_LIGHT1);<span class="kom">// Zapne sv�tlo 1</span></p>
<p class="src1">glEnable(GL_LIGHTING);<span class="kom">// Zapne sv�tla</span></p>

<p>Materi�ly, kter� ur�uj� jak vypadaj� polygony p�i dopadu sv�tla, jsou, mysl�m, novinkou. Nemus�me vepisovat ��dn� hodnoty, proto�e p�ed�van� pole jsou definov�ny na za��tku tutori�lu. Materi�ly mimo jin� ur�uj� i barvu povrchu, tak�e p�i zapnut�m sv�tle nebude m�t zm�na barvy pomoc� glColor() ��dn� vliv (P�ekl. To jsem zjistil �pln� n�hodou. Nev�m, jestli je to pravda obecn�, ale minim�ln� v tomto demu ano.).</p>

<p class="src1">glMaterialfv(GL_FRONT, GL_AMBIENT, MatAmb);<span class="kom">// Prost�ed�, atmosf�ra</span></p>
<p class="src1">glMaterialfv(GL_FRONT, GL_DIFFUSE, MatDif);<span class="kom">// Rozptylov�n� sv�tla</span></p>
<p class="src1">glMaterialfv(GL_FRONT, GL_SPECULAR, MatSpc);<span class="kom">// Zrcadlivost</span></p>
<p class="src1">glMaterialfv(GL_FRONT, GL_SHININESS, MatShn);<span class="kom">// Lesk</span></p>

<p>Abychom alespo� trochu zrychlili vykreslov�n�, zapneme culling, tak�e se zadn� strany troj�heln�k� nebudou vykreslovat. Kter� strana je odvr�cen� se ur�� podle po�ad� zad�v�n� vrchol� polygon� (po/proti sm�ru hodinov�ch ru�i�ek).</p>

<p class="src1">glCullFace(GL_BACK);<span class="kom">// O�ez�v�n� zadn�ch stran</span></p>
<p class="src1">glEnable(GL_CULL_FACE);<span class="kom">// Zapne o�ez�v�n�</span></p>

<p>Budeme vykreslovat i n�jak� koule, tak�e vytvo��me a inicializujeme quadratic.</p>

<p class="src1">q = gluNewQuadric();<span class="kom">// Nov� quadratic</span></p>
<p class="src1">gluQuadricNormals(q, GL_SMOOTH);<span class="kom">// Generov�n� norm�lov�ch vektor� pro sv�tlo</span></p>
<p class="src1">gluQuadricTexture(q, GL_FALSE);<span class="kom">// Nepot�ebujeme texturovac� koordin�ty</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V po��dku</span></p>
<p class="src0">}</p>

<p>Posledn� funkc� tohoto tutori�lu je ProcessKeyboard(). Stejn� jako vykreslov�n�, tak i ona, se vol� v ka�d�m pr�chodu hlavn� smy�ky programu. O�et�uje u�ivatelsk� p��kazy p�i stisku kl�ves. Jak se program zachov�, popisuj� koment��e.</p>

<p class="src0">void ProcessKeyboard()<span class="kom">// O�et�en� kl�vesnice</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Rotace objektu</span></p>
<p class="src1">if (keys[VK_LEFT]) yspeed -= 0.1f;<span class="kom">// �ipka vlevo - sni�uje y rychlost</span></p>
<p class="src1">if (keys[VK_RIGHT]) yspeed += 0.1f;<span class="kom">// �ipka vpravo - zvy�uje y rychlost</span></p>
<p class="src1">if (keys[VK_UP]) xspeed -= 0.1f;<span class="kom">// �ipka nahoru - sni�uje x rychlost</span></p>
<p class="src1">if (keys[VK_DOWN]) xspeed += 0.1f;<span class="kom">// �ipka dol� - zvy�uje x rychlost</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pozice objektu</span></p>
<p class="src1">if (keys[VK_NUMPAD6]) ObjPos[0] += 0.05f;<span class="kom">// '6' - pohybuje objektem doprava</span></p>
<p class="src1">if (keys[VK_NUMPAD4]) ObjPos[0] -= 0.05f;<span class="kom">// '4' - pohybuje objektem doleva</span></p>
<p class="src"></p>
<p class="src1">if (keys[VK_NUMPAD8]) ObjPos[1] += 0.05f;<span class="kom">// '8' - pohybuje objektem nahoru</span></p>
<p class="src1">if (keys[VK_NUMPAD5]) ObjPos[1] -= 0.05f;<span class="kom">// '5' - pohybuje objektem dol�</span></p>
<p class="src"></p>
<p class="src1">if (keys[VK_NUMPAD9]) ObjPos[2] += 0.05f;<span class="kom">// '9' - p�ibli�uje objekt</span></p>
<p class="src1">if (keys[VK_NUMPAD7]) ObjPos[2] -= 0.05f;<span class="kom">// '7' oddaluje objekt</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pozice sv�tla</span></p>
<p class="src1">if (keys['L']) LightPos[0] += 0.05f;<span class="kom">// 'L' - pohybuje sv�tlem doprava</span></p>
<p class="src1">if (keys['J']) LightPos[0] -= 0.05f;<span class="kom">// 'J'  - pohybuje sv�tlem doleva</span></p>
<p class="src"></p>
<p class="src1">if (keys['I']) LightPos[1] += 0.05f;<span class="kom">// 'I' - pohybuje sv�tlem nahoru</span></p>
<p class="src1">if (keys['K']) LightPos[1] -= 0.05f;<span class="kom">// 'K' - pohybuje sv�tlem dol�</span></p>
<p class="src"></p>
<p class="src1">if (keys['O']) LightPos[2] += 0.05f;<span class="kom">// 'O' - p�ibli�uje sv�tlo</span></p>
<p class="src1">if (keys['U']) LightPos[2] -= 0.05f;<span class="kom">// 'U' - oddaluje sv�tlo</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pozice koule</span></p>
<p class="src1">if (keys['D']) SpherePos[0] += 0.05f;<span class="kom">// 'D' - pohybuje koul� doprava</span></p>
<p class="src1">if (keys['A']) SpherePos[0] -= 0.05f;<span class="kom">// 'A' - pohybuje koul� doleva</span></p>
<p class="src"></p>
<p class="src1">if (keys['W']) SpherePos[1] += 0.05f;<span class="kom">// 'W' - pohybuje koul� nahoru</span></p>
<p class="src1">if (keys['S']) SpherePos[1] -= 0.05f;<span class="kom">// 'S'- pohybuje koul� dol�</span></p>
<p class="src"></p>
<p class="src1">if (keys['E']) SpherePos[2] += 0.05f;<span class="kom">// 'E' - p�ibli�uje kouli</span></p>
<p class="src1">if (keys['Q']) SpherePos[2] -= 0.05f;<span class="kom">// 'Q' - oddaluje kouli</span></p>
<p class="src0">}</p>
<p class="src"></p>

<h3>N�kolik pozn�mek ohledn� tutori�lu</h3>
<p>Na prvn� pohled vypad� demo hyperefektn� :-), ale m� tak� sv� mouchy. Tak nap��klad koule nezastavuje projekci st�nu na st�nu. V re�ln�m prost�ed� by tak� vrhala st�n, tak�e by se nic moc nestalo. Nicm�n� je zde pouze na uk�zku toho, co se se st�nem stane na zak�iven�m povrchu.</p>

<p>Pokud program b�� extr�mn� pomalu, zkuste p�epnout do fullscreenu nebo zm�nit barevnou hloubku na 32 bit�. Arseny L. napsal: &quot;Pokud m�te probl�my s TNT2 v okenn�m m�du, ujist�te se, �e nem�te nastavenu 16bitovou barevnou hloubku. V tomto barevn�m m�du je stencil buffer emulovan�, co� ve v�sledku znamen� mal� v�kon. V 32bitov�m m�du je v�e bez probl�m�.&quot;</p>

<p class="autor">napsal: Banu Cosmin - Choko &amp; Brett Porter <?VypisEmail('brettporter@yahoo.com');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson27.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson27_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:christian@tugzip.com">Christian Kindahl</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson27.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/delphi/lesson27.zip">Delphi</a> k�d t�to lekce. ( <a href="mailto:another.freak@gmx.de">Felix Hahn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson27.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:danprogram@hotmail.com">Dan</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/jogl/lesson27.jar">JoGL</a> k�d t�to lekce. ( <a href="mailto:abezrati@hotmail.com">Abdul Bezrati</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/kde/lesson27.tar.gz">KDE/QT</a> k�d t�to lekce. ( <a href="mailto:zhajdu@socal.rr.com">Zsolt Hajdu</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/linux/lesson27.tar.gz">Linux</a> k�d t�to lekce. ( <a href="mailto:jay@remotepoint.com">Jay Groven</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/macosxcocoa/lesson27.zip">Mac OS X/Cocoa</a> k�d t�to lekce. ( <a href="mailto:blb@pobox.com">Bryan Blackburn</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson27.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:ultimatezeus@hotmail.com">Grant James</a> )</li>
</ul>

<?FceImgNeHeVelky(27);?>
<?FceNeHeOkolniLekce(27);?>

<?
include 'p_end.php';
?>
