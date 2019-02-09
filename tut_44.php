<?
$g_title = 'CZ NeHe OpenGL - Lekce 44 - �o�kov� efekty';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(44);?>

<h1>Lekce 44 - �o�kov� efekty</h1>

<p class="nadpis_clanku">�o�kov� efekty vznikaj� po dopadu paprsku sv�tla nap�. na objektiv kamery nebo fotoapar�tu. Pod�v�te-li se na z��i vyvolanou �o�kou, zjist�te, �e jednotliv� �tvary maj� jednu spole�nou v�c. Pozorovateli se zd�, jako by se v�echny pohybovaly skrz st�ed sc�ny. S t�mto na mysli m��eme osu z jednodu�e odstranit a vytv��et v�e ve 2D. Jedin� probl�m souvisej�c� s nep��tomnost� z sou�adnice je, jak zjistit, jestli se zdroj sv�tla nach�z� ve v�hledu kamery nebo ne. P�ipravte se proto na trochu matematiky.</p>

<p>Ahoj v�ichni, jsem tu s dal��m tutori�lem. Roz����me na�i t��du glCamera o �o�kov� efekty (P�ekl.: v origin�le Lens Flare - �o�kov� z��e), kter� jsou sice n�ro�n� na mno�stv� v�po�t�, ale vypadaj� opravdu realisticky. Jak u� jsem napsal, do t��dy kamery p�id�me mo�nost, jak zjistit, jestli se bod nebo koule nach�z� ve v�hledu kamery na sc�nu. Nem�li bychom v�ak p�i tom odrovnat procesor.</p>

<p>Jsem na rozpac�ch, ale mus�m zm�nit, �e t��da kamery obsahuje chybu. P�ed t�m, ne� za�neme, mus�me ji z�platovat. Funkci SetPerspective() upravte podle n�sleduj�c�ho vzoru.</p>

<p class="src0">void glCamera::SetPerspective()</p>
<p class="src0">{</p>
<p class="src1">GLfloat Matrix[16];<span class="kom">// Pole pro modelview matici</span></p>
<p class="src1">glVector v;<span class="kom">// Sm�r a rychlost kamery</span></p>
<p class="src"></p>
<p class="src1">glRotatef(m_HeadingDegrees, 0.0f, 1.0f, 0.0f);<span class="kom">// V�po�et sm�rov�ho vektoru</span></p>
<p class="src1">glRotatef(m_PitchDegrees, 1.0f, 0.0f, 0.0f);</p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, Matrix);<span class="kom">// Z�sk�n� matice</span></p>
<p class="src"></p>
<p class="src1">m_DirectionVector.i = Matrix[8];<span class="kom">// Sm�rov� vektor</span></p>
<p class="src1">m_DirectionVector.j = Matrix[9];</p>
<p class="src1">m_DirectionVector.k = -Matrix[10];<span class="kom">// Mus� b�t invertov�n</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glRotatef(m_PitchDegrees, 1.0f, 0.0f, 0.0f);<span class="kom">// Spr�vn� orientace sc�ny</span></p>
<p class="src1">glRotatef(m_HeadingDegrees, 0.0f, 1.0f, 0.0f);</p>
<p class="src"></p>
<p class="src1">v = m_DirectionVector;<span class="kom">// Aktualizovat sm�r podle rychlosti</span></p>
<p class="src1">v *= m_ForwardVelocity;</p>
<p class="src"></p>
<p class="src1">m_Position.x += v.i;<span class="kom">// Inkrementace pozice vektorem</span></p>
<p class="src1">m_Position.y += v.j;</p>
<p class="src1">m_Position.z += v.k;</p>
<p class="src"></p>
<p class="src1">glTranslatef(-m_Position.x, -m_Position.y, -m_Position.z);<span class="kom">// P�esun na novou pozici</span></p>
<p class="src0">}</p>

<p>P�ed t�m, ne� se pust�me do k�dov�n�, si nakresl�me �ty�i textury pro �o�kovou z��i. Prvn� p�edstavuje mlhavou z��i nebo s�l�n� a bude v�dy umis�ov�na na pozici sv�teln�ho zdroje. Pomoc� dal�� m��eme vytv��et z�blesky z���c� ven ze sv�tla. Op�t ji um�st�me na jeho pozici. T�et� se vzhledem podob� prvn� textu�e, ale uprost�ed je mnohem v�ce definovan�. Budeme j� dynamicky pohybovat p�es sc�nu. Posledn� textura je z���c�, dut� vypadaj�c� kruh, kter� budeme p�esunovat v z�vislosti na pozici a orientaci kamery. Existuj� samoz�ejm� i dal�� typy textur; pro dal�� informace se pod�vejte na reference uveden� na konci tutori�lu.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_44_big_glow.jpg" width="128" height="128" alt="Big Glow" />
<img src="images/nehe_tut/tut_44_streaks.jpg" width="128" height="128" alt="Streaks" />
<img src="images/nehe_tut/tut_44_glow.jpg" width="64" height="64" alt="Glow" />
<img src="images/nehe_tut/tut_44_halo.jpg" width="64" height="64" alt="Halo" />
</div>

<p>Te� byste u� m�li m�t alespo� p�edstavu, co budeme vykreslovat. Obecn� se d� ��ci, �e se �o�kov� efekt nikdy neobjev�, dokud se nepod�v�me do zdroje sv�tla nebo alespo� jeho sm�rem, a proto pot�ebujeme naj�t cestu, jak zjistit, jestli se dan� bod (pozice sv�tla) nach�z� ve v�hledu kamery nebo ne. M��eme vyn�sobit modelview a projek�n� matici a potom nal�zt o�ez�vac� roviny, kter� OpenGL pou��v�. Druh� mo�nost je pou��t roz���en� GL_HP_occlusion_test nebo GL_NV_occlusion_query, ale ne ka�d� grafick� karta je implementuje. My pou�ijeme v�c, kter� funguje v�dy a v�ude - matematiku.</p>

<p>P�ekl.: Ob�as, kdy� je n�co m�lo vysv�tlen�, p�id�v�m vlastn� texty, ale te� to po mn� pros�m necht�jte :-)</p>

<p class="src0">void glCamera::UpdateFrustum()<span class="kom">// Z�sk�n� o�ez�vac�ch rovin</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat clip[16];<span class="kom">// Pomocn� matice</span></p>
<p class="src1">GLfloat proj[16];<span class="kom">// Projek�n� matice</span></p>
<p class="src1">GLfloat modl[16];<span class="kom">// Modelview matice</span></p>
<p class="src1">GLfloat t;<span class="kom">// Pomocn�</span></p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_PROJECTION_MATRIX, proj);<span class="kom">// Z�sk�n� projek�n� matice</span></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, modl);<span class="kom">// Z�sk�n� modelview matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vyn�sob� projek�n� matici pomoc� modelview</span></p>
<p class="src1">clip[ 0] = modl[ 0] * proj[ 0] + modl[ 1] * proj[ 4] + modl[ 2] * proj[ 8] + modl[ 3] * proj[12];</p>
<p class="src1">clip[ 1] = modl[ 0] * proj[ 1] + modl[ 1] * proj[ 5] + modl[ 2] * proj[ 9] + modl[ 3] * proj[13];</p>
<p class="src1">clip[ 2] = modl[ 0] * proj[ 2] + modl[ 1] * proj[ 6] + modl[ 2] * proj[10] + modl[ 3] * proj[14];</p>
<p class="src1">clip[ 3] = modl[ 0] * proj[ 3] + modl[ 1] * proj[ 7] + modl[ 2] * proj[11] + modl[ 3] * proj[15];</p>
<p class="src"></p>
<p class="src1">clip[ 4] = modl[ 4] * proj[ 0] + modl[ 5] * proj[ 4] + modl[ 6] * proj[ 8] + modl[ 7] * proj[12];</p>
<p class="src1">clip[ 5] = modl[ 4] * proj[ 1] + modl[ 5] * proj[ 5] + modl[ 6] * proj[ 9] + modl[ 7] * proj[13];</p>
<p class="src1">clip[ 6] = modl[ 4] * proj[ 2] + modl[ 5] * proj[ 6] + modl[ 6] * proj[10] + modl[ 7] * proj[14];</p>
<p class="src1">clip[ 7] = modl[ 4] * proj[ 3] + modl[ 5] * proj[ 7] + modl[ 6] * proj[11] + modl[ 7] * proj[15];</p>
<p class="src"></p>
<p class="src1">clip[ 8] = modl[ 8] * proj[ 0] + modl[ 9] * proj[ 4] + modl[10] * proj[ 8] + modl[11] * proj[12];</p>
<p class="src1">clip[ 9] = modl[ 8] * proj[ 1] + modl[ 9] * proj[ 5] + modl[10] * proj[ 9] + modl[11] * proj[13];</p>
<p class="src1">clip[10] = modl[ 8] * proj[ 2] + modl[ 9] * proj[ 6] + modl[10] * proj[10] + modl[11] * proj[14];</p>
<p class="src1">clip[11] = modl[ 8] * proj[ 3] + modl[ 9] * proj[ 7] + modl[10] * proj[11] + modl[11] * proj[15];</p>
<p class="src"></p>
<p class="src1">clip[12] = modl[12] * proj[ 0] + modl[13] * proj[ 4] + modl[14] * proj[ 8] + modl[15] * proj[12];</p>
<p class="src1">clip[13] = modl[12] * proj[ 1] + modl[13] * proj[ 5] + modl[14] * proj[ 9] + modl[15] * proj[13];</p>
<p class="src1">clip[14] = modl[12] * proj[ 2] + modl[13] * proj[ 6] + modl[14] * proj[10] + modl[15] * proj[14];</p>
<p class="src1">clip[15] = modl[12] * proj[ 3] + modl[13] * proj[ 7] + modl[14] * proj[11] + modl[15] * proj[15];</p>
<p class="src"></p>
<p class="src1">m_Frustum[0][0] = clip[ 3] - clip[ 0];<span class="kom">// Z�sk�n� prav� roviny</span></p>
<p class="src1">m_Frustum[0][1] = clip[ 7] - clip[ 4];</p>
<p class="src1">m_Frustum[0][2] = clip[11] - clip[ 8];</p>
<p class="src1">m_Frustum[0][3] = clip[15] - clip[12];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[0][0] * m_Frustum[0][0] + m_Frustum[0][1] * m_Frustum[0][1] + m_Frustum[0][2] * m_Frustum[0][2] ));</p>
<p class="src1">m_Frustum[0][0] /= t;</p>
<p class="src1">m_Frustum[0][1] /= t;</p>
<p class="src1">m_Frustum[0][2] /= t;</p>
<p class="src1">m_Frustum[0][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[1][0] = clip[ 3] + clip[ 0];<span class="kom">// Z�sk�n� lev� roviny</span></p>
<p class="src1">m_Frustum[1][1] = clip[ 7] + clip[ 4];</p>
<p class="src1">m_Frustum[1][2] = clip[11] + clip[ 8];</p>
<p class="src1">m_Frustum[1][3] = clip[15] + clip[12];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[1][0] * m_Frustum[1][0] + m_Frustum[1][1] * m_Frustum[1][1] + m_Frustum[1][2] * m_Frustum[1][2] ));</p>
<p class="src1">m_Frustum[1][0] /= t;</p>
<p class="src1">m_Frustum[1][1] /= t;</p>
<p class="src1">m_Frustum[1][2] /= t;</p>
<p class="src1">m_Frustum[1][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[2][0] = clip[ 3] + clip[ 1];<span class="kom">// Z�sk�n� doln� roviny</span></p>
<p class="src1">m_Frustum[2][1] = clip[ 7] + clip[ 5];</p>
<p class="src1">m_Frustum[2][2] = clip[11] + clip[ 9];</p>
<p class="src1">m_Frustum[2][3] = clip[15] + clip[13];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[2][0] * m_Frustum[2][0] + m_Frustum[2][1] * m_Frustum[2][1] + m_Frustum[2][2] * m_Frustum[2][2] ));</p>
<p class="src1">m_Frustum[2][0] /= t;</p>
<p class="src1">m_Frustum[2][1] /= t;</p>
<p class="src1">m_Frustum[2][2] /= t;</p>
<p class="src1">m_Frustum[2][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[3][0] = clip[ 3] - clip[ 1];<span class="kom">// Z�sk�n� horn� roviny</span></p>
<p class="src1">m_Frustum[3][1] = clip[ 7] - clip[ 5];</p>
<p class="src1">m_Frustum[3][2] = clip[11] - clip[ 9];</p>
<p class="src1">m_Frustum[3][3] = clip[15] - clip[13];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[3][0] * m_Frustum[3][0] + m_Frustum[3][1] * m_Frustum[3][1] + m_Frustum[3][2] * m_Frustum[3][2] ));</p>
<p class="src1">m_Frustum[3][0] /= t;</p>
<p class="src1">m_Frustum[3][1] /= t;</p>
<p class="src1">m_Frustum[3][2] /= t;</p>
<p class="src1">m_Frustum[3][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[4][0] = clip[ 3] - clip[ 2];<span class="kom">// Z�sk�n� zadn� roviny</span></p>
<p class="src1">m_Frustum[4][1] = clip[ 7] - clip[ 6];</p>
<p class="src1">m_Frustum[4][2] = clip[11] - clip[10];</p>
<p class="src1">m_Frustum[4][3] = clip[15] - clip[14];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[4][0] * m_Frustum[4][0] + m_Frustum[4][1] * m_Frustum[4][1] + m_Frustum[4][2] * m_Frustum[4][2] ));</p>
<p class="src1">m_Frustum[4][0] /= t;</p>
<p class="src1">m_Frustum[4][1] /= t;</p>
<p class="src1">m_Frustum[4][2] /= t;</p>
<p class="src1">m_Frustum[4][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[5][0] = clip[ 3] + clip[ 2];<span class="kom">// Z�sk�n� p�edn� roviny</span></p>
<p class="src1">m_Frustum[5][1] = clip[ 7] + clip[ 6];</p>
<p class="src1">m_Frustum[5][2] = clip[11] + clip[10];</p>
<p class="src1">m_Frustum[5][3] = clip[15] + clip[14];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[5][0] * m_Frustum[5][0] + m_Frustum[5][1] * m_Frustum[5][1] + m_Frustum[5][2] * m_Frustum[5][2] ));</p>
<p class="src1">m_Frustum[5][0] /= t;</p>
<p class="src1">m_Frustum[5][1] /= t;</p>
<p class="src1">m_Frustum[5][2] /= t;</p>
<p class="src1">m_Frustum[5][3] /= t;</p>
<p class="src0">}</p>

<p>Tato funkce byla opravdu n�ro�n�! Jsem si jist�, �e u� v�te, pro� vznikaj� nejr�zn�j�� OpenGL roz���en�. A�koli je matematika celkem p��mo�ar�, jej� stra�n� d�lka zobrazuje v�ci slo�it�. Pou�ili jsme celkem 190 z�kladn�ch operac� (n�soben�, d�len�, s��t�n�, od��t�n�), plus �est druh�ch odmocnin. Proto�e ji budeme volat p�i ka�d�m p�ekreslen� sc�ny, mohla by se snaha o optimalizaci vyplatit. Dokud nemodifikujeme projek�n� matici translac� nebo rotac�, m��eme pou��vat jej� rychlej�� ekvivalent UpdateFrustumFaster().</p>

<p class="src0">void glCamera::UpdateFrustumFaster()<span class="kom">// Z�sk�n� o�ez�vac�ch rovin (optimalizovan� funkce)</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat clip[16];<span class="kom">// Pomocn� matice</span></p>
<p class="src1">GLfloat proj[16];<span class="kom">// Projek�n� matice</span></p>
<p class="src1">GLfloat modl[16];<span class="kom">// Modelview matice</span></p>
<p class="src1">GLfloat t;<span class="kom">// Pomocn�</span></p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_PROJECTION_MATRIX, proj);<span class="kom">// Z�sk�n� projek�n� matice</span></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, modl);<span class="kom">// Z�sk�n� modelview matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vyn�sob� projek�n� matici pomoc� modelview (nesm� b�t p�ed t�m pou�ita rotace ani translace)</span></p>
<p class="src1">clip[ 0] = modl[ 0] * proj[ 0];</p>
<p class="src1">clip[ 1] = modl[ 1] * proj[ 5];</p>
<p class="src1">clip[ 2] = modl[ 2] * proj[10] + modl[ 3] * proj[14];</p>
<p class="src1">clip[ 3] = modl[ 2] * proj[11];</p>
<p class="src"></p>
<p class="src1">clip[ 4] = modl[ 4] * proj[ 0];</p>
<p class="src1">clip[ 5] = modl[ 5] * proj[ 5];</p>
<p class="src1">clip[ 6] = modl[ 6] * proj[10] + modl[ 7] * proj[14];</p>
<p class="src1">clip[ 7] = modl[ 6] * proj[11];</p>
<p class="src"></p>
<p class="src1">clip[ 8] = modl[ 8] * proj[ 0];</p>
<p class="src1">clip[ 9] = modl[ 9] * proj[ 5];</p>
<p class="src1">clip[10] = modl[10] * proj[10] + modl[11] * proj[14];</p>
<p class="src1">clip[11] = modl[10] * proj[11];</p>
<p class="src"></p>
<p class="src1">clip[12] = modl[12] * proj[ 0];</p>
<p class="src1">clip[13] = modl[13] * proj[ 5];</p>
<p class="src1">clip[14] = modl[14] * proj[10] + modl[15] * proj[14];</p>
<p class="src1">clip[15] = modl[14] * proj[11];</p>
<p class="src"></p>
<p class="src1">m_Frustum[0][0] = clip[ 3] - clip[ 0];<span class="kom">// Z�sk�n� prav� roviny</span></p>
<p class="src1">m_Frustum[0][1] = clip[ 7] - clip[ 4];</p>
<p class="src1">m_Frustum[0][2] = clip[11] - clip[ 8];</p>
<p class="src1">m_Frustum[0][3] = clip[15] - clip[12];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[0][0] * m_Frustum[0][0] + m_Frustum[0][1] * m_Frustum[0][1] + m_Frustum[0][2] * m_Frustum[0][2] ));</p>
<p class="src1">m_Frustum[0][0] /= t;</p>
<p class="src1">m_Frustum[0][1] /= t;</p>
<p class="src1">m_Frustum[0][2] /= t;</p>
<p class="src1">m_Frustum[0][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[1][0] = clip[ 3] + clip[ 0];<span class="kom">// Z�sk�n� lev� roviny</span></p>
<p class="src1">m_Frustum[1][1] = clip[ 7] + clip[ 4];</p>
<p class="src1">m_Frustum[1][2] = clip[11] + clip[ 8];</p>
<p class="src1">m_Frustum[1][3] = clip[15] + clip[12];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[1][0] * m_Frustum[1][0] + m_Frustum[1][1] * m_Frustum[1][1] + m_Frustum[1][2] * m_Frustum[1][2] ));</p>
<p class="src1">m_Frustum[1][0] /= t;</p>
<p class="src1">m_Frustum[1][1] /= t;</p>
<p class="src1">m_Frustum[1][2] /= t;</p>
<p class="src1">m_Frustum[1][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[2][0] = clip[ 3] + clip[ 1];<span class="kom">// Z�sk�n� spodn� roviny</span></p>
<p class="src1">m_Frustum[2][1] = clip[ 7] + clip[ 5];</p>
<p class="src1">m_Frustum[2][2] = clip[11] + clip[ 9];</p>
<p class="src1">m_Frustum[2][3] = clip[15] + clip[13];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[2][0] * m_Frustum[2][0] + m_Frustum[2][1] * m_Frustum[2][1] + m_Frustum[2][2] * m_Frustum[2][2] ));</p>
<p class="src1">m_Frustum[2][0] /= t;</p>
<p class="src1">m_Frustum[2][1] /= t;</p>
<p class="src1">m_Frustum[2][2] /= t;</p>
<p class="src1">m_Frustum[2][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[3][0] = clip[ 3] - clip[ 1];<span class="kom">// Z�sk�n� horn� roviny</span></p>
<p class="src1">m_Frustum[3][1] = clip[ 7] - clip[ 5];</p>
<p class="src1">m_Frustum[3][2] = clip[11] - clip[ 9];</p>
<p class="src1">m_Frustum[3][3] = clip[15] - clip[13];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[3][0] * m_Frustum[3][0] + m_Frustum[3][1] * m_Frustum[3][1] + m_Frustum[3][2] * m_Frustum[3][2] ));</p>
<p class="src1">m_Frustum[3][0] /= t;</p>
<p class="src1">m_Frustum[3][1] /= t;</p>
<p class="src1">m_Frustum[3][2] /= t;</p>
<p class="src1">m_Frustum[3][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[4][0] = clip[ 3] - clip[ 2];<span class="kom">// Z�sk�n� zadn� roviny</span></p>
<p class="src1">m_Frustum[4][1] = clip[ 7] - clip[ 6];</p>
<p class="src1">m_Frustum[4][2] = clip[11] - clip[10];</p>
<p class="src1">m_Frustum[4][3] = clip[15] - clip[14];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[4][0] * m_Frustum[4][0] + m_Frustum[4][1] * m_Frustum[4][1] + m_Frustum[4][2] * m_Frustum[4][2] ));</p>
<p class="src1">m_Frustum[4][0] /= t;</p>
<p class="src1">m_Frustum[4][1] /= t;</p>
<p class="src1">m_Frustum[4][2] /= t;</p>
<p class="src1">m_Frustum[4][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[5][0] = clip[ 3] + clip[ 2];<span class="kom">// Z�sk�n� p�edn� roviny</span></p>
<p class="src1">m_Frustum[5][1] = clip[ 7] + clip[ 6];</p>
<p class="src1">m_Frustum[5][2] = clip[11] + clip[10];</p>
<p class="src1">m_Frustum[5][3] = clip[15] + clip[14];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace v�sledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[5][0] * m_Frustum[5][0] + m_Frustum[5][1] * m_Frustum[5][1] + m_Frustum[5][2] * m_Frustum[5][2] ));</p>
<p class="src1">m_Frustum[5][0] /= t;</p>
<p class="src1">m_Frustum[5][1] /= t;</p>
<p class="src1">m_Frustum[5][2] /= t;</p>
<p class="src1">m_Frustum[5][3] /= t;</p>
<p class="src0">}</p>

<p>Operac� se prov�d� st�le mnoho, ale oproti p�edchoz� verzi, je jich pouze n�co p�es polovinu (102). Optimalizace byla celkem jednoduch�, odstranil jsem pouze v�echna n�soben�, kter� se d�ky nule vykr�t�. Pokud chcete kompletn� optimalizaci, pou�ijte ji� zm�n�n� roz���en�, kter� za v�s ud�laj� stejnou pr�ci a nav�c mnohem rychleji, proto�e v�echny v�po�ty prob�hnou na hardwaru grafick� karty. A�koli vol�n� obou UpdateFrustum() funkc� navy�uje v�konnostn� ztr�tu, m��eme nyn� snadno zjistit, jestli se libovoln� bod nach�z� ve v�hledu kamery. Obsahuje-li sc�na v�ce objekt� n�ro�n�ch na rendering, bude ur�it� v�hodn� vykreslovat pouze ty, kter� p�jdou vid�t - nap��klad u rozs�hl�ho ter�nu.</p>

<p>PointInFrustum() vr�cen�m true ozn�m�, �e se bod p�edan� v parametru nach�z� ve viditeln� oblasti okna. Druh� funkce je prakticky stejn�, ale jedn� se o kouli.</p>

<p class="src0">BOOL glCamera::PointInFrustum(glPoint p)<span class="kom">// Bude bod vid�t na sc�n�?</span></p>
<p class="src0">{</p>
<p class="src1">int i;</p>
<p class="src"></p>
<p class="src1">for(i = 0; i &lt; 6; i++)<span class="kom">// Bod se mus� nach�zet mezi v�emi �esti o�ez�vac�mi rovinami</span></p>
<p class="src1">{</p>
<p class="src2">if(m_Frustum[i][0] * p.x + m_Frustum[i][1] * p.y + m_Frustum[i][2] * p.z + m_Frustum[i][3] &lt;= 0)</p>
<p class="src2">{</p>
<p class="src3">return FALSE;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0">BOOL glCamera::SphereInFrustum(glPoint p, GLfloat Radius)<span class="kom">// Bude koule vid�t na sc�n�?</span></p>
<p class="src0">{</p>
<p class="src1">int i;</p>
<p class="src"></p>
<p class="src1">for(i = 0; i &lt; 6; i++)<span class="kom">// Koule se mus� nach�zet mezi v�emi �esti o�ez�vac�mi rovinami</span></p>
<p class="src1">{</p>
<p class="src2">if(m_Frustum[i][0] * p.x + m_Frustum[i][1] * p.y + m_Frustum[i][2] * p.z + m_Frustum[i][3] &lt;= -Radius)</p>
<p class="src2">{</p>
<p class="src3">return FALSE;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Ve funkci IsOccluded() po��d�me gluProject() o zji�t�n�, do kter� ��sti viewportu bude zadan� bod projektov�n. Pozice ve viewportu odpov�d� sou�adnic�m v depth bufferu. Pokud bude hloubka pixelu v bufferu men�� ne� hloubka na�eho bodu, je jasn�, �e se u� n�co nach�z� p�ed n�m.</p>

<p class="src0">bool glCamera::IsOccluded(glPoint p)<span class="kom">// Je p�ed bodem n�co vykresleno?</span></p>
<p class="src0">{</p>
<p class="src1">GLint viewport[4];<span class="kom">// Data viewportu</span></p>
<p class="src1">GLdouble mvmatrix[16], projmatrix[16];<span class="kom">// Transforma�n� matice</span></p>
<p class="src1">GLdouble winx, winy, winz;<span class="kom">// V�sledn� sou�adnice</span></p>
<p class="src1">GLdouble flareZ;<span class="kom">// Hloubka z��e v obrazovce</span></p>
<p class="src1">GLfloat bufferZ;<span class="kom">// Hloubka z bufferu</span></p>
<p class="src"></p>
<p class="src1">glGetIntegerv(GL_VIEWPORT, viewport);<span class="kom">// Z�sk�n� viewportu</span></p>
<p class="src1">glGetDoublev(GL_MODELVIEW_MATRIX, mvmatrix);<span class="kom">// Z�sk�n� modelview matice</span></p>
<p class="src1">glGetDoublev(GL_PROJECTION_MATRIX, projmatrix);<span class="kom">// Z�sk�n� projek�n� matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Kam do viewportu (2D) se vykresl� bod (3D)</span></p>
<p class="src1">gluProject(p.x, p.y, p.z, mvmatrix, projmatrix, viewport, &amp;winx, &amp;winy, &amp;winz);</p>
<p class="src1">flareZ = winz;</p>
<p class="src"></p>
<p class="src1">glReadPixels(winx, winy, 1, 1, GL_DEPTH_COMPONENT, GL_FLOAT, &amp;bufferZ);<span class="kom">// Hloubka v depth bufferu</span></p>
<p class="src"></p>
<p class="src1">if (bufferZ &lt; flareZ)<span class="kom">// P�ed bodem se nach�z� objekt</span></p>
<p class="src1">{</p>
<p class="src2">return true;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Nic p�ed bodem nen�</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>V�echny obd�ln�ky objekt� �o�kov�ho efektu by m�ly b�t vykreslov�ny na rovinu rovnob�nou s obrazovkou monitoru, ale m��e se st�t, �e budou kv�li rotac�m naklon�n�. To je probl�m, proto�e by se m�ly zobrazit ploch� i v p��pad�, �e se d�v�me na zdroj sv�tla ze strany. Nam�sto otexturovan�ho quadu bychom mohli s v�hodou vyu��t point sprity. Kdy� chceme nakreslit &quot;klasick�&quot; obd�ln�k, p�ed�me OpenGL sou�adnice �ty� bod�, texturovac� koordin�ty a norm�lov� vektory. Na rozd�l od toho point sprite vy�aduje pouze x, y, z sou�adnice a nic jin�ho. Grafick� karta vykresl� kolem t�chto sou�adnic obd�ln�k, kter� bude v�dy orientov�n k obrazovce. Mo�n� se v�m p�i programov�n� ��sticov�ch syst�m� stalo, �e po nato�en� sc�ny o 90 stup�� v�echny ��stice zmizely, proto�e byly vykreslov�ny kolmo k plo�e obrazovky. Pr�v� pro n� se hod� point sprity nejv�ce, ale pro �o�kov� efekty tak�. Jejich velk� nev�hoda spo��v� v implementaci, existuj� pouze jako roz���en� (GL_NV_point_sprite), tak�e se m��e st�t, �e je grafick� karta nebude podporovat. Ani zde tedy roz���en� nepou�ijeme. �e�en� m��e spo��vat v invertov�n� v�ech rotac�, nicm�n� probl�my nastanou, pokud se kamera dostane za zdroj sv�tla. Proto, abychom tomu p�ede�li, budeme p�i pohybu kamerou z�rove� m�nit tak� polohu sv�tla. Z�sk�me i vedlej�� efekt, zdroj sv�tla se bude jevit jakoby st�le ve stejn� vzd�lenosti a tak� dovol� �o�kov�m efekt�m o trochu vylep�it pohybov�n� po p��m� lince.</p>

<p>Vypo�teme vzd�lenost kamery od sv�tla a p�es sm�rov� vektor kamery z�sk�me pr�se��k, jeho� vzd�lenost od kamery mus� b�t stejn� jako vzd�lenost kamery a sv�tla. M�me-li pr�se��k, m��eme nal�zt vektor, p�es kter� vykresl�me v�echny ��sti �o�kov�ho efektu. Obr�zek bude mo�n� n�zorn�j��...</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_44_diag.jpg" width="512" height="512" alt="Grafick� zn�zorn�n� jak se z�sk� vektor" /></div>

<p class="src0">void glCamera::RenderLensFlare()<span class="kom">// Vykreslen� �o�kov�ch objekt�</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat Length = 0.0f;</p>
<p class="src"></p>
<p class="src1">if(SphereInFrustum(m_LightSourcePos, 1.0f) == TRUE)<span class="kom">// Pouze pokud kamera sm��uje ke sv�tlu</span></p>
<p class="src1">{</p>
<p class="src2">vLightSourceToCamera = m_Position - m_LightSourcePos;<span class="kom">// Vektor od kamery ke sv�tlu</span></p>
<p class="src2">Length = vLightSourceToCamera.Magnitude();<span class="kom">// Vzd�lenost kamery od sv�tla</span></p>
<p class="src"></p>
<p class="src2">ptIntersect = m_DirectionVector * Length;<span class="kom">// Bod pr�se��ku</span></p>
<p class="src2">ptIntersect += m_Position;</p>
<p class="src"></p>
<p class="src2">vLightSourceToIntersect = ptIntersect - m_LightSourcePos;<span class="kom">// Vektor mezi sv�tlem a pr�se��kem</span></p>
<p class="src2">Length = vLightSourceToIntersect.Magnitude();<span class="kom">// Vzd�lenost sv�tla a pr�se��ku</span></p>
<p class="src2">vLightSourceToIntersect.Normalize();<span class="kom">// Normalizace vektoru</span></p>

<p>Na z�skan�m sm�rov�m vektoru vykresl�me z�blesky. Posuneme se o x jednotek dol� po vektoru vLightSourceToIntersect a n�sledn�m p�i�ten�m k pozici sv�tla z�sk�me nov� po�adovan� bod.</p>

<p class="src2"><span class="kom">// Nastaven� OpenGL</span></p>
<p class="src2">glEnable(GL_BLEND);</p>
<p class="src2">glBlendFunc(GL_SRC_ALPHA, GL_ONE);</p>
<p class="src2">glDisable(GL_DEPTH_TEST);</p>
<p class="src2">glEnable(GL_TEXTURE_2D);</p>
<p class="src"></p>
<p class="src2">if (!IsOccluded(m_LightSourcePos))<span class="kom">// P�ed st�edem z��e nesm� b�t ��dn� objekt</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Vykreslen� z��e</span></p>
<p class="src3">RenderBigGlow(0.60f, 0.60f, 0.8f, 1.0f, m_LightSourcePos, 16.0f);</p>
<p class="src3">RenderStreaks(0.60f, 0.60f, 0.8f, 1.0f, m_LightSourcePos, 16.0f);</p>
<p class="src3">RenderGlow(0.8f, 0.8f, 1.0f, 0.5f, m_LightSourcePos, 3.5f);</p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.1f);<span class="kom">// Bod ve 20% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.9f, 0.6f, 0.4f, 0.5f, pt, 0.6f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.15f);<span class="kom">// Bod ve 30% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.8f, 0.5f, 0.6f, 0.5f, pt, 1.7f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.175f);<span class="kom">// Bod ve 35% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.9f, 0.2f, 0.1f, 0.5f, pt, 0.83f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.285f);<span class="kom">// Bod ve 57% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.7f, 0.7f, 0.4f, 0.5f, pt, 1.6f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.2755f);<span class="kom">// Bod ve 55.1% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.9f, 0.9f, 0.2f, 0.5f, pt, 0.8f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.4775f);<span class="kom">// Bod ve 95.5% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.93f, 0.82f, 0.73f, 0.5f, pt, 1.0f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.49f);<span class="kom">// Bod ve 98% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.7f, 0.6f, 0.5f, 0.5f, pt, 1.4f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.65f);<span class="kom">// Bod ve 130% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.7f, 0.8f, 0.3f, 0.5f, pt, 1.8f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.63f);<span class="kom">// Bod ve 126% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.4f, 0.3f, 0.2f, 0.5f, pt, 1.4f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.8f);<span class="kom">// Bod ve 160% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.7f, 0.5f, 0.5f, 0.5f, pt, 1.4f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.7825f);<span class="kom">// Bod ve 156.5% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.8f, 0.5f, 0.1f, 0.5f, pt, 0.6f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 1.0f);<span class="kom">// Bod ve 200% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.5f, 0.5f, 0.7f, 0.5f, pt, 1.7f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.975f);<span class="kom">// Bod ve 195% vzd�lenosti od sv�tla ve sm�ru pr�se��ku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.4f, 0.1f, 0.9f, 0.5f, pt, 2.0f);<span class="kom">// Vykreslen� z��e</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Obnoven� nastaven� OpenGL</span></p>
<p class="src2">glDisable(GL_BLEND);</p>
<p class="src2">glEnable(GL_DEPTH_TEST);</p>
<p class="src2">glDisable(GL_TEXTURE_2D);</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>N�sleduje v�pis k�du pro rendering �o�kov� z��e. M�me celkem �ty�i r�zn� funkce, kter� se ale li�� pouze texturou objektu, jinak jsou identick�.</p>

<p class="src0">void glCamera::RenderHalo(GLfloat r, GLfloat g, GLfloat b, GLfloat a, glPoint p, GLfloat scale)<span class="kom">// Vykreslen� z��e</span></p>
<p class="src0">{</p>
<p class="src1">glPoint q[4];<span class="kom">// Pomocn� bod</span></p>
<p class="src"></p>
<p class="src1">q[0].x = (p.x - scale);<span class="kom">// V�po�et pozice</span></p>
<p class="src1">q[0].y = (p.y - scale);</p>
<p class="src"></p>
<p class="src1">q[1].x = (p.x - scale);</p>
<p class="src1">q[1].y = (p.y + scale);</p>
<p class="src"></p>
<p class="src1">q[2].x = (p.x + scale);</p>
<p class="src1">q[2].y = (p.y - scale);</p>
<p class="src"></p>
<p class="src1">q[3].x = (p.x + scale);</p>
<p class="src1">q[3].y = (p.y + scale);</p>
<p class="src"></p>
<p class="src1">glPushMatrix();<span class="kom">// Ulo�en� matice</span></p>
<p class="src2">glTranslatef(p.x, p.y, p.z);<span class="kom">// P�esun na pozici</span></p>
<p class="src2">glRotatef(-m_HeadingDegrees, 0.0f, 1.0f, 0.0f);<span class="kom">// Odstran�n� rotac�</span></p>
<p class="src2">glRotatef(-m_PitchDegrees, 1.0f, 0.0f, 0.0f);</p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, m_HaloTexture);<span class="kom">// Textura</span></p>
<p class="src2">glColor4f(r, g, b, a);<span class="kom">// Nastaven� barvy</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_TRIANGLE_STRIP);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex2f(q[0].x, q[0].y);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex2f(q[1].x, q[1].y);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex2f(q[2].x, q[2].y);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex2f(q[3].x, q[3].y);</p>
<p class="src2">glEnd();</p>
<p class="src1">glPopMatrix();<span class="kom">// Obnoven� matice</span></p>
<p class="src0">}</p>

<p>Tak to by z k�du bylo v�echno. Pomoc� kl�ves W, S, A, D m��ete v programu m�nit sm�r kamery. Kl�vesy 1 a 2 zap�naj�/vyp�naj� v�pisy informac�. Z a C nastavuj� kame�e konstantn� rychlost a X ji zastavuje.</p>

<p>Samoz�ejm� nejsem prvn� �lov�k, kter� vytv��el �o�kov� efekty, a proto m��ete dole naj�t p�r odkaz�, kter� mi p�i psan� pomohly. Cht�l bych tak� pod�kovat Davu Steerovi, Cameron Tidwell, Bertu Sammonsovi a Brannon Martidale za zp�tnou vazbu a testov�n� k�du na rozli�n�m hardware.</p>

<ul>
<li><?OdkazBlank('http://www.gamedev.net/reference/articles/article874.asp');?></li>
<li><?OdkazBlank('http://www.gamedev.net/reference/articles/article813.asp');?></li>
<li><?OdkazBlank('http://www.opengl.org/developers/code/mjktips/lensflare/');?></li>
<li><?OdkazBlank('http://www.markmorley.com/opengl/frustumculling.html');?></li>
<li><?OdkazBlank('http://oss.sgi.com/projects/ogl-sample/registry/HP/occlusion_test.txt');?></li>
<li><?OdkazBlank('http://oss.sgi.com/projects/ogl-sample/registry/NV/occlusion_query.txt');?></li>
</ul>

<p class="autor">napsal: Vic Hollis <?VypisEmail('vichollis@comcast.netVic');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3>Pozn�mky Daria Corna - rIO ze Spinning Kids</h3>

<p>P�idal jsem n�kolik test� pro zji�t�n� objekt� ve sc�n� p�ed �o�kov�m efektem (na pozici zdroje sv�tla). V takov�m p��pad� se z��e vyp�n�. Nov� k�d by m�l b�t dob�e okomentov�n a je ozna�en �et�zcem # New Stuff #. Jeho p��padn� odstran�n� by nem�lo �init probl�my. Modifikace jsou n�sleduj�c�:</p>

<ul>
<li>Nov� metoda t��dy glCamera nazvan� IsOccluded(), kter� vrac� true v p��pad�, �e se p�ed sv�tlem nach�z� n�jak� objekt</li>
<li>N�kolik prom�nn�ch pro gluCylinder (pou�it jako objekt st�n�c� sv�tlu)</li>
<li>Zm�ny v glDraw() pro vykreslen� st�n�c�ho objektu</li>
<li>Deinicializa�n� k�d pro quadratic</li>
</ul>

<p>Douf�m, �e se v�m modifikovan� verze bude l�bit v�ce. Jako dom�c� �kol si m��ete zkusil testovat v�ce ne� jeden bod na sou�adnic�ch sv�tla, aby se z��e skokov� nevyp�nala, ale postupn� mizela.</p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson44.zip">Visual C++</a> k�d t�to lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson44_bcb6.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson44.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:michael@mudsplat.com">Michael Small</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson44.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson44.zip">Visual Studio .NET</a> k�d t�to lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/extras/lesson44/lesson44_with_extensions.zip">Lesson 44 - With Extension Support</a> (VC++).</li>
</ul>

<?FceImgNeHeVelky(44);?>
<?FceNeHeOkolniLekce(44);?>

<?
include 'p_end.php';
?>
