<?
$g_title = 'CZ NeHe OpenGL - Lekce 44 - Èoèkové efekty';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<?FceImgNeHeMaly(44);?>

<h1>Lekce 44 - Èoèkové efekty</h1>

<p class="nadpis_clanku">Èoèkové efekty vznikají po dopadu paprsku svìtla napø. na objektiv kamery nebo fotoaparátu. Podíváte-li se na záøi vyvolanou èoèkou, zjistíte, ¾e jednotlivé útvary mají jednu spoleènou vìc. Pozorovateli se zdá, jako by se v¹echny pohybovaly skrz støed scény. S tímto na mysli mù¾eme osu z jednodu¹e odstranit a vytváøet v¹e ve 2D. Jediný problém související s nepøítomností z souøadnice je, jak zjistit, jestli se zdroj svìtla nachází ve výhledu kamery nebo ne. Pøipravte se proto na trochu matematiky.</p>

<p>Ahoj v¹ichni, jsem tu s dal¹ím tutoriálem. Roz¹íøíme na¹i tøídu glCamera o èoèkové efekty (Pøekl.: v originále Lens Flare - èoèková záøe), které jsou sice nároèné na mno¾ství výpoètù, ale vypadají opravdu realisticky. Jak u¾ jsem napsal, do tøídy kamery pøidáme mo¾nost, jak zjistit, jestli se bod nebo koule nachází ve výhledu kamery na scénu. Nemìli bychom v¹ak pøi tom odrovnat procesor.</p>

<p>Jsem na rozpacích, ale musím zmínit, ¾e tøída kamery obsahuje chybu. Pøed tím, ne¾ zaèneme, musíme ji záplatovat. Funkci SetPerspective() upravte podle následujícího vzoru.</p>

<p class="src0">void glCamera::SetPerspective()</p>
<p class="src0">{</p>
<p class="src1">GLfloat Matrix[16];<span class="kom">// Pole pro modelview matici</span></p>
<p class="src1">glVector v;<span class="kom">// Smìr a rychlost kamery</span></p>
<p class="src"></p>
<p class="src1">glRotatef(m_HeadingDegrees, 0.0f, 1.0f, 0.0f);<span class="kom">// Výpoèet smìrového vektoru</span></p>
<p class="src1">glRotatef(m_PitchDegrees, 1.0f, 0.0f, 0.0f);</p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, Matrix);<span class="kom">// Získání matice</span></p>
<p class="src"></p>
<p class="src1">m_DirectionVector.i = Matrix[8];<span class="kom">// Smìrový vektor</span></p>
<p class="src1">m_DirectionVector.j = Matrix[9];</p>
<p class="src1">m_DirectionVector.k = -Matrix[10];<span class="kom">// Musí být invertován</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">glRotatef(m_PitchDegrees, 1.0f, 0.0f, 0.0f);<span class="kom">// Správná orientace scény</span></p>
<p class="src1">glRotatef(m_HeadingDegrees, 0.0f, 1.0f, 0.0f);</p>
<p class="src"></p>
<p class="src1">v = m_DirectionVector;<span class="kom">// Aktualizovat smìr podle rychlosti</span></p>
<p class="src1">v *= m_ForwardVelocity;</p>
<p class="src"></p>
<p class="src1">m_Position.x += v.i;<span class="kom">// Inkrementace pozice vektorem</span></p>
<p class="src1">m_Position.y += v.j;</p>
<p class="src1">m_Position.z += v.k;</p>
<p class="src"></p>
<p class="src1">glTranslatef(-m_Position.x, -m_Position.y, -m_Position.z);<span class="kom">// Pøesun na novou pozici</span></p>
<p class="src0">}</p>

<p>Pøed tím, ne¾ se pustíme do kódování, si nakreslíme ètyøi textury pro èoèkovou záøi. První pøedstavuje mlhavou záøi nebo sálání a bude v¾dy umis»ována na pozici svìtelného zdroje. Pomocí dal¹í mù¾eme vytváøet záblesky záøící ven ze svìtla. Opìt ji umístíme na jeho pozici. Tøetí se vzhledem podobá první textuøe, ale uprostøed je mnohem více definovaná. Budeme jí dynamicky pohybovat pøes scénu. Poslední textura je záøící, dutì vypadající kruh, který budeme pøesunovat v závislosti na pozici a orientaci kamery. Existují samozøejmì i dal¹í typy textur; pro dal¹í informace se podívejte na reference uvedené na konci tutoriálu.</p>

<div class="okolo_img">
<img src="images/nehe_tut/tut_44_big_glow.jpg" width="128" height="128" alt="Big Glow" />
<img src="images/nehe_tut/tut_44_streaks.jpg" width="128" height="128" alt="Streaks" />
<img src="images/nehe_tut/tut_44_glow.jpg" width="64" height="64" alt="Glow" />
<img src="images/nehe_tut/tut_44_halo.jpg" width="64" height="64" alt="Halo" />
</div>

<p>Teï byste u¾ mìli mít alespoò pøedstavu, co budeme vykreslovat. Obecnì se dá øíci, ¾e se èoèkový efekt nikdy neobjeví, dokud se nepodíváme do zdroje svìtla nebo alespoò jeho smìrem, a proto potøebujeme najít cestu, jak zjistit, jestli se daný bod (pozice svìtla) nachází ve výhledu kamery nebo ne. Mù¾eme vynásobit modelview a projekèní matici a potom nalézt oøezávací roviny, které OpenGL pou¾ívá. Druhá mo¾nost je pou¾ít roz¹íøení GL_HP_occlusion_test nebo GL_NV_occlusion_query, ale ne ka¾dá grafická karta je implementuje. My pou¾ijeme vìc, která funguje v¾dy a v¹ude - matematiku.</p>

<p>Pøekl.: Obèas, kdy¾ je nìco málo vysvìtlené, pøidávám vlastní texty, ale teï to po mnì prosím nechtìjte :-)</p>

<p class="src0">void glCamera::UpdateFrustum()<span class="kom">// Získání oøezávacích rovin</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat clip[16];<span class="kom">// Pomocná matice</span></p>
<p class="src1">GLfloat proj[16];<span class="kom">// Projekèní matice</span></p>
<p class="src1">GLfloat modl[16];<span class="kom">// Modelview matice</span></p>
<p class="src1">GLfloat t;<span class="kom">// Pomocná</span></p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_PROJECTION_MATRIX, proj);<span class="kom">// Získání projekèní matice</span></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, modl);<span class="kom">// Získání modelview matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vynásobí projekèní matici pomocí modelview</span></p>
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
<p class="src1">m_Frustum[0][0] = clip[ 3] - clip[ 0];<span class="kom">// Získání pravé roviny</span></p>
<p class="src1">m_Frustum[0][1] = clip[ 7] - clip[ 4];</p>
<p class="src1">m_Frustum[0][2] = clip[11] - clip[ 8];</p>
<p class="src1">m_Frustum[0][3] = clip[15] - clip[12];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[0][0] * m_Frustum[0][0] + m_Frustum[0][1] * m_Frustum[0][1] + m_Frustum[0][2] * m_Frustum[0][2] ));</p>
<p class="src1">m_Frustum[0][0] /= t;</p>
<p class="src1">m_Frustum[0][1] /= t;</p>
<p class="src1">m_Frustum[0][2] /= t;</p>
<p class="src1">m_Frustum[0][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[1][0] = clip[ 3] + clip[ 0];<span class="kom">// Získání levé roviny</span></p>
<p class="src1">m_Frustum[1][1] = clip[ 7] + clip[ 4];</p>
<p class="src1">m_Frustum[1][2] = clip[11] + clip[ 8];</p>
<p class="src1">m_Frustum[1][3] = clip[15] + clip[12];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[1][0] * m_Frustum[1][0] + m_Frustum[1][1] * m_Frustum[1][1] + m_Frustum[1][2] * m_Frustum[1][2] ));</p>
<p class="src1">m_Frustum[1][0] /= t;</p>
<p class="src1">m_Frustum[1][1] /= t;</p>
<p class="src1">m_Frustum[1][2] /= t;</p>
<p class="src1">m_Frustum[1][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[2][0] = clip[ 3] + clip[ 1];<span class="kom">// Získání dolní roviny</span></p>
<p class="src1">m_Frustum[2][1] = clip[ 7] + clip[ 5];</p>
<p class="src1">m_Frustum[2][2] = clip[11] + clip[ 9];</p>
<p class="src1">m_Frustum[2][3] = clip[15] + clip[13];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[2][0] * m_Frustum[2][0] + m_Frustum[2][1] * m_Frustum[2][1] + m_Frustum[2][2] * m_Frustum[2][2] ));</p>
<p class="src1">m_Frustum[2][0] /= t;</p>
<p class="src1">m_Frustum[2][1] /= t;</p>
<p class="src1">m_Frustum[2][2] /= t;</p>
<p class="src1">m_Frustum[2][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[3][0] = clip[ 3] - clip[ 1];<span class="kom">// Získání horní roviny</span></p>
<p class="src1">m_Frustum[3][1] = clip[ 7] - clip[ 5];</p>
<p class="src1">m_Frustum[3][2] = clip[11] - clip[ 9];</p>
<p class="src1">m_Frustum[3][3] = clip[15] - clip[13];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[3][0] * m_Frustum[3][0] + m_Frustum[3][1] * m_Frustum[3][1] + m_Frustum[3][2] * m_Frustum[3][2] ));</p>
<p class="src1">m_Frustum[3][0] /= t;</p>
<p class="src1">m_Frustum[3][1] /= t;</p>
<p class="src1">m_Frustum[3][2] /= t;</p>
<p class="src1">m_Frustum[3][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[4][0] = clip[ 3] - clip[ 2];<span class="kom">// Získání zadní roviny</span></p>
<p class="src1">m_Frustum[4][1] = clip[ 7] - clip[ 6];</p>
<p class="src1">m_Frustum[4][2] = clip[11] - clip[10];</p>
<p class="src1">m_Frustum[4][3] = clip[15] - clip[14];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[4][0] * m_Frustum[4][0] + m_Frustum[4][1] * m_Frustum[4][1] + m_Frustum[4][2] * m_Frustum[4][2] ));</p>
<p class="src1">m_Frustum[4][0] /= t;</p>
<p class="src1">m_Frustum[4][1] /= t;</p>
<p class="src1">m_Frustum[4][2] /= t;</p>
<p class="src1">m_Frustum[4][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[5][0] = clip[ 3] + clip[ 2];<span class="kom">// Získání pøední roviny</span></p>
<p class="src1">m_Frustum[5][1] = clip[ 7] + clip[ 6];</p>
<p class="src1">m_Frustum[5][2] = clip[11] + clip[10];</p>
<p class="src1">m_Frustum[5][3] = clip[15] + clip[14];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[5][0] * m_Frustum[5][0] + m_Frustum[5][1] * m_Frustum[5][1] + m_Frustum[5][2] * m_Frustum[5][2] ));</p>
<p class="src1">m_Frustum[5][0] /= t;</p>
<p class="src1">m_Frustum[5][1] /= t;</p>
<p class="src1">m_Frustum[5][2] /= t;</p>
<p class="src1">m_Frustum[5][3] /= t;</p>
<p class="src0">}</p>

<p>Tato funkce byla opravdu nároèná! Jsem si jistý, ¾e u¾ víte, proè vznikají nejrùznìj¹í OpenGL roz¹íøení. Aèkoli je matematika celkem pøímoèará, její stra¹ná délka zobrazuje vìci slo¾itì. Pou¾ili jsme celkem 190 základních operací (násobení, dìlení, sèítání, odèítání), plus ¹est druhých odmocnin. Proto¾e ji budeme volat pøi ka¾dém pøekreslení scény, mohla by se snaha o optimalizaci vyplatit. Dokud nemodifikujeme projekèní matici translací nebo rotací, mù¾eme pou¾ívat její rychlej¹í ekvivalent UpdateFrustumFaster().</p>

<p class="src0">void glCamera::UpdateFrustumFaster()<span class="kom">// Získání oøezávacích rovin (optimalizovaná funkce)</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat clip[16];<span class="kom">// Pomocná matice</span></p>
<p class="src1">GLfloat proj[16];<span class="kom">// Projekèní matice</span></p>
<p class="src1">GLfloat modl[16];<span class="kom">// Modelview matice</span></p>
<p class="src1">GLfloat t;<span class="kom">// Pomocná</span></p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_PROJECTION_MATRIX, proj);<span class="kom">// Získání projekèní matice</span></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, modl);<span class="kom">// Získání modelview matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vynásobí projekèní matici pomocí modelview (nesmí být pøed tím pou¾ita rotace ani translace)</span></p>
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
<p class="src1">m_Frustum[0][0] = clip[ 3] - clip[ 0];<span class="kom">// Získání pravé roviny</span></p>
<p class="src1">m_Frustum[0][1] = clip[ 7] - clip[ 4];</p>
<p class="src1">m_Frustum[0][2] = clip[11] - clip[ 8];</p>
<p class="src1">m_Frustum[0][3] = clip[15] - clip[12];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[0][0] * m_Frustum[0][0] + m_Frustum[0][1] * m_Frustum[0][1] + m_Frustum[0][2] * m_Frustum[0][2] ));</p>
<p class="src1">m_Frustum[0][0] /= t;</p>
<p class="src1">m_Frustum[0][1] /= t;</p>
<p class="src1">m_Frustum[0][2] /= t;</p>
<p class="src1">m_Frustum[0][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[1][0] = clip[ 3] + clip[ 0];<span class="kom">// Získání levé roviny</span></p>
<p class="src1">m_Frustum[1][1] = clip[ 7] + clip[ 4];</p>
<p class="src1">m_Frustum[1][2] = clip[11] + clip[ 8];</p>
<p class="src1">m_Frustum[1][3] = clip[15] + clip[12];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[1][0] * m_Frustum[1][0] + m_Frustum[1][1] * m_Frustum[1][1] + m_Frustum[1][2] * m_Frustum[1][2] ));</p>
<p class="src1">m_Frustum[1][0] /= t;</p>
<p class="src1">m_Frustum[1][1] /= t;</p>
<p class="src1">m_Frustum[1][2] /= t;</p>
<p class="src1">m_Frustum[1][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[2][0] = clip[ 3] + clip[ 1];<span class="kom">// Získání spodní roviny</span></p>
<p class="src1">m_Frustum[2][1] = clip[ 7] + clip[ 5];</p>
<p class="src1">m_Frustum[2][2] = clip[11] + clip[ 9];</p>
<p class="src1">m_Frustum[2][3] = clip[15] + clip[13];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[2][0] * m_Frustum[2][0] + m_Frustum[2][1] * m_Frustum[2][1] + m_Frustum[2][2] * m_Frustum[2][2] ));</p>
<p class="src1">m_Frustum[2][0] /= t;</p>
<p class="src1">m_Frustum[2][1] /= t;</p>
<p class="src1">m_Frustum[2][2] /= t;</p>
<p class="src1">m_Frustum[2][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[3][0] = clip[ 3] - clip[ 1];<span class="kom">// Získání horní roviny</span></p>
<p class="src1">m_Frustum[3][1] = clip[ 7] - clip[ 5];</p>
<p class="src1">m_Frustum[3][2] = clip[11] - clip[ 9];</p>
<p class="src1">m_Frustum[3][3] = clip[15] - clip[13];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[3][0] * m_Frustum[3][0] + m_Frustum[3][1] * m_Frustum[3][1] + m_Frustum[3][2] * m_Frustum[3][2] ));</p>
<p class="src1">m_Frustum[3][0] /= t;</p>
<p class="src1">m_Frustum[3][1] /= t;</p>
<p class="src1">m_Frustum[3][2] /= t;</p>
<p class="src1">m_Frustum[3][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[4][0] = clip[ 3] - clip[ 2];<span class="kom">// Získání zadní roviny</span></p>
<p class="src1">m_Frustum[4][1] = clip[ 7] - clip[ 6];</p>
<p class="src1">m_Frustum[4][2] = clip[11] - clip[10];</p>
<p class="src1">m_Frustum[4][3] = clip[15] - clip[14];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[4][0] * m_Frustum[4][0] + m_Frustum[4][1] * m_Frustum[4][1] + m_Frustum[4][2] * m_Frustum[4][2] ));</p>
<p class="src1">m_Frustum[4][0] /= t;</p>
<p class="src1">m_Frustum[4][1] /= t;</p>
<p class="src1">m_Frustum[4][2] /= t;</p>
<p class="src1">m_Frustum[4][3] /= t;</p>
<p class="src"></p>
<p class="src1">m_Frustum[5][0] = clip[ 3] + clip[ 2];<span class="kom">// Získání pøední roviny</span></p>
<p class="src1">m_Frustum[5][1] = clip[ 7] + clip[ 6];</p>
<p class="src1">m_Frustum[5][2] = clip[11] + clip[10];</p>
<p class="src1">m_Frustum[5][3] = clip[15] + clip[14];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Normalizace výsledku</span></p>
<p class="src1">t = GLfloat(sqrt( m_Frustum[5][0] * m_Frustum[5][0] + m_Frustum[5][1] * m_Frustum[5][1] + m_Frustum[5][2] * m_Frustum[5][2] ));</p>
<p class="src1">m_Frustum[5][0] /= t;</p>
<p class="src1">m_Frustum[5][1] /= t;</p>
<p class="src1">m_Frustum[5][2] /= t;</p>
<p class="src1">m_Frustum[5][3] /= t;</p>
<p class="src0">}</p>

<p>Operací se provádí stále mnoho, ale oproti pøedchozí verzi, je jich pouze nìco pøes polovinu (102). Optimalizace byla celkem jednoduchá, odstranil jsem pouze v¹echna násobení, která se díky nule vykrátí. Pokud chcete kompletní optimalizaci, pou¾ijte ji¾ zmínìná roz¹íøení, která za vás udìlají stejnou práci a navíc mnohem rychleji, proto¾e v¹echny výpoèty probìhnou na hardwaru grafické karty. Aèkoli volání obou UpdateFrustum() funkcí navy¹uje výkonnostní ztrátu, mù¾eme nyní snadno zjistit, jestli se libovolný bod nachází ve výhledu kamery. Obsahuje-li scéna více objektù nároèných na rendering, bude urèitì výhodné vykreslovat pouze ty, které pùjdou vidìt - napøíklad u rozsáhlého terénu.</p>

<p>PointInFrustum() vrácením true oznámí, ¾e se bod pøedaný v parametru nachází ve viditelné oblasti okna. Druhá funkce je prakticky stejná, ale jedná se o kouli.</p>

<p class="src0">BOOL glCamera::PointInFrustum(glPoint p)<span class="kom">// Bude bod vidìt na scénì?</span></p>
<p class="src0">{</p>
<p class="src1">int i;</p>
<p class="src"></p>
<p class="src1">for(i = 0; i &lt; 6; i++)<span class="kom">// Bod se musí nacházet mezi v¹emi ¹esti oøezávacími rovinami</span></p>
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
<p class="src0">BOOL glCamera::SphereInFrustum(glPoint p, GLfloat Radius)<span class="kom">// Bude koule vidìt na scénì?</span></p>
<p class="src0">{</p>
<p class="src1">int i;</p>
<p class="src"></p>
<p class="src1">for(i = 0; i &lt; 6; i++)<span class="kom">// Koule se musí nacházet mezi v¹emi ¹esti oøezávacími rovinami</span></p>
<p class="src1">{</p>
<p class="src2">if(m_Frustum[i][0] * p.x + m_Frustum[i][1] * p.y + m_Frustum[i][2] * p.z + m_Frustum[i][3] &lt;= -Radius)</p>
<p class="src2">{</p>
<p class="src3">return FALSE;</p>
<p class="src2">}</p>
<p class="src1">}</p>
<p class="src1"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Ve funkci IsOccluded() po¾ádáme gluProject() o zji¹tìní, do které èásti viewportu bude zadaný bod projektován. Pozice ve viewportu odpovídá souøadnicím v depth bufferu. Pokud bude hloubka pixelu v bufferu men¹í ne¾ hloubka na¹eho bodu, je jasné, ¾e se u¾ nìco nachází pøed ním.</p>

<p class="src0">bool glCamera::IsOccluded(glPoint p)<span class="kom">// Je pøed bodem nìco vykresleno?</span></p>
<p class="src0">{</p>
<p class="src1">GLint viewport[4];<span class="kom">// Data viewportu</span></p>
<p class="src1">GLdouble mvmatrix[16], projmatrix[16];<span class="kom">// Transformaèní matice</span></p>
<p class="src1">GLdouble winx, winy, winz;<span class="kom">// Výsledné souøadnice</span></p>
<p class="src1">GLdouble flareZ;<span class="kom">// Hloubka záøe v obrazovce</span></p>
<p class="src1">GLfloat bufferZ;<span class="kom">// Hloubka z bufferu</span></p>
<p class="src"></p>
<p class="src1">glGetIntegerv(GL_VIEWPORT, viewport);<span class="kom">// Získání viewportu</span></p>
<p class="src1">glGetDoublev(GL_MODELVIEW_MATRIX, mvmatrix);<span class="kom">// Získání modelview matice</span></p>
<p class="src1">glGetDoublev(GL_PROJECTION_MATRIX, projmatrix);<span class="kom">// Získání projekèní matice</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Kam do viewportu (2D) se vykreslí bod (3D)</span></p>
<p class="src1">gluProject(p.x, p.y, p.z, mvmatrix, projmatrix, viewport, &amp;winx, &amp;winy, &amp;winz);</p>
<p class="src1">flareZ = winz;</p>
<p class="src"></p>
<p class="src1">glReadPixels(winx, winy, 1, 1, GL_DEPTH_COMPONENT, GL_FLOAT, &amp;bufferZ);<span class="kom">// Hloubka v depth bufferu</span></p>
<p class="src"></p>
<p class="src1">if (bufferZ &lt; flareZ)<span class="kom">// Pøed bodem se nachází objekt</span></p>
<p class="src1">{</p>
<p class="src2">return true;</p>
<p class="src1">}</p>
<p class="src1">else<span class="kom">// Nic pøed bodem není</span></p>
<p class="src1">{</p>
<p class="src2">return false;</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>V¹echny obdélníky objektù èoèkového efektu by mìly být vykreslovány na rovinu rovnobì¾nou s obrazovkou monitoru, ale mù¾e se stát, ¾e budou kvùli rotacím naklonìné. To je problém, proto¾e by se mìly zobrazit ploché i v pøípadì, ¾e se díváme na zdroj svìtla ze strany. Namísto otexturovaného quadu bychom mohli s výhodou vyu¾ít point sprity. Kdy¾ chceme nakreslit &quot;klasický&quot; obdélník, pøedáme OpenGL souøadnice ètyø bodù, texturovací koordináty a normálové vektory. Na rozdíl od toho point sprite vy¾aduje pouze x, y, z souøadnice a nic jiného. Grafická karta vykreslí kolem tìchto souøadnic obdélník, který bude v¾dy orientován k obrazovce. Mo¾ná se vám pøi programování èásticových systémù stalo, ¾e po natoèení scény o 90 stupòù v¹echny èástice zmizely, proto¾e byly vykreslovány kolmo k plo¹e obrazovky. Právì pro nì se hodí point sprity nejvíce, ale pro èoèkové efekty také. Jejich velká nevýhoda spoèívá v implementaci, existují pouze jako roz¹íøení (GL_NV_point_sprite), tak¾e se mù¾e stát, ¾e je grafická karta nebude podporovat. Ani zde tedy roz¹íøení nepou¾ijeme. Øe¹ení mù¾e spoèívat v invertování v¹ech rotací, nicménì problémy nastanou, pokud se kamera dostane za zdroj svìtla. Proto, abychom tomu pøede¹li, budeme pøi pohybu kamerou zároveò mìnit také polohu svìtla. Získáme i vedlej¹í efekt, zdroj svìtla se bude jevit jakoby stále ve stejné vzdálenosti a také dovolí èoèkovým efektùm o trochu vylep¹it pohybování po pøímé lince.</p>

<p>Vypoèteme vzdálenost kamery od svìtla a pøes smìrový vektor kamery získáme prùseèík, jeho¾ vzdálenost od kamery musí být stejná jako vzdálenost kamery a svìtla. Máme-li prùseèík, mù¾eme nalézt vektor, pøes který vykreslíme v¹echny èásti èoèkového efektu. Obrázek bude mo¾ná názornìj¹í...</p>

<div class="okolo_img"><img src="images/nehe_tut/tut_44_diag.jpg" width="512" height="512" alt="Grafické znázornìní jak se získá vektor" /></div>

<p class="src0">void glCamera::RenderLensFlare()<span class="kom">// Vykreslení èoèkových objektù</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat Length = 0.0f;</p>
<p class="src"></p>
<p class="src1">if(SphereInFrustum(m_LightSourcePos, 1.0f) == TRUE)<span class="kom">// Pouze pokud kamera smìøuje ke svìtlu</span></p>
<p class="src1">{</p>
<p class="src2">vLightSourceToCamera = m_Position - m_LightSourcePos;<span class="kom">// Vektor od kamery ke svìtlu</span></p>
<p class="src2">Length = vLightSourceToCamera.Magnitude();<span class="kom">// Vzdálenost kamery od svìtla</span></p>
<p class="src"></p>
<p class="src2">ptIntersect = m_DirectionVector * Length;<span class="kom">// Bod prùseèíku</span></p>
<p class="src2">ptIntersect += m_Position;</p>
<p class="src"></p>
<p class="src2">vLightSourceToIntersect = ptIntersect - m_LightSourcePos;<span class="kom">// Vektor mezi svìtlem a prùseèíkem</span></p>
<p class="src2">Length = vLightSourceToIntersect.Magnitude();<span class="kom">// Vzdálenost svìtla a prùseèíku</span></p>
<p class="src2">vLightSourceToIntersect.Normalize();<span class="kom">// Normalizace vektoru</span></p>

<p>Na získaném smìrovém vektoru vykreslíme záblesky. Posuneme se o x jednotek dolù po vektoru vLightSourceToIntersect a následným pøiètením k pozici svìtla získáme nový po¾adovaný bod.</p>

<p class="src2"><span class="kom">// Nastavení OpenGL</span></p>
<p class="src2">glEnable(GL_BLEND);</p>
<p class="src2">glBlendFunc(GL_SRC_ALPHA, GL_ONE);</p>
<p class="src2">glDisable(GL_DEPTH_TEST);</p>
<p class="src2">glEnable(GL_TEXTURE_2D);</p>
<p class="src"></p>
<p class="src2">if (!IsOccluded(m_LightSourcePos))<span class="kom">// Pøed støedem záøe nesmí být ¾ádný objekt</span></p>
<p class="src2">{</p>
<p class="src3"><span class="kom">// Vykreslení záøe</span></p>
<p class="src3">RenderBigGlow(0.60f, 0.60f, 0.8f, 1.0f, m_LightSourcePos, 16.0f);</p>
<p class="src3">RenderStreaks(0.60f, 0.60f, 0.8f, 1.0f, m_LightSourcePos, 16.0f);</p>
<p class="src3">RenderGlow(0.8f, 0.8f, 1.0f, 0.5f, m_LightSourcePos, 3.5f);</p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.1f);<span class="kom">// Bod ve 20% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.9f, 0.6f, 0.4f, 0.5f, pt, 0.6f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.15f);<span class="kom">// Bod ve 30% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.8f, 0.5f, 0.6f, 0.5f, pt, 1.7f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.175f);<span class="kom">// Bod ve 35% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.9f, 0.2f, 0.1f, 0.5f, pt, 0.83f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.285f);<span class="kom">// Bod ve 57% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.7f, 0.7f, 0.4f, 0.5f, pt, 1.6f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.2755f);<span class="kom">// Bod ve 55.1% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.9f, 0.9f, 0.2f, 0.5f, pt, 0.8f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.4775f);<span class="kom">// Bod ve 95.5% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.93f, 0.82f, 0.73f, 0.5f, pt, 1.0f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.49f);<span class="kom">// Bod ve 98% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.7f, 0.6f, 0.5f, 0.5f, pt, 1.4f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.65f);<span class="kom">// Bod ve 130% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.7f, 0.8f, 0.3f, 0.5f, pt, 1.8f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.63f);<span class="kom">// Bod ve 126% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.4f, 0.3f, 0.2f, 0.5f, pt, 1.4f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.8f);<span class="kom">// Bod ve 160% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.7f, 0.5f, 0.5f, 0.5f, pt, 1.4f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.7825f);<span class="kom">// Bod ve 156.5% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.8f, 0.5f, 0.1f, 0.5f, pt, 0.6f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 1.0f);<span class="kom">// Bod ve 200% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderHalo(0.5f, 0.5f, 0.7f, 0.5f, pt, 1.7f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src"></p>
<p class="src3">pt = vLightSourceToIntersect * (Length * 0.975f);<span class="kom">// Bod ve 195% vzdálenosti od svìtla ve smìru prùseèíku</span></p>
<p class="src3">pt += m_LightSourcePos;</p>
<p class="src"></p>
<p class="src3">RenderGlow(0.4f, 0.1f, 0.9f, 0.5f, pt, 2.0f);<span class="kom">// Vykreslení záøe</span></p>
<p class="src2">}</p>
<p class="src"></p>
<p class="src2"><span class="kom">// Obnovení nastavení OpenGL</span></p>
<p class="src2">glDisable(GL_BLEND);</p>
<p class="src2">glEnable(GL_DEPTH_TEST);</p>
<p class="src2">glDisable(GL_TEXTURE_2D);</p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Následuje výpis kódu pro rendering èoèkové záøe. Máme celkem ètyøi rùzné funkce, které se ale li¹í pouze texturou objektu, jinak jsou identické.</p>

<p class="src0">void glCamera::RenderHalo(GLfloat r, GLfloat g, GLfloat b, GLfloat a, glPoint p, GLfloat scale)<span class="kom">// Vykreslení záøe</span></p>
<p class="src0">{</p>
<p class="src1">glPoint q[4];<span class="kom">// Pomocný bod</span></p>
<p class="src"></p>
<p class="src1">q[0].x = (p.x - scale);<span class="kom">// Výpoèet pozice</span></p>
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
<p class="src1">glPushMatrix();<span class="kom">// Ulo¾ení matice</span></p>
<p class="src2">glTranslatef(p.x, p.y, p.z);<span class="kom">// Pøesun na pozici</span></p>
<p class="src2">glRotatef(-m_HeadingDegrees, 0.0f, 1.0f, 0.0f);<span class="kom">// Odstranìní rotací</span></p>
<p class="src2">glRotatef(-m_PitchDegrees, 1.0f, 0.0f, 0.0f);</p>
<p class="src2">glBindTexture(GL_TEXTURE_2D, m_HaloTexture);<span class="kom">// Textura</span></p>
<p class="src2">glColor4f(r, g, b, a);<span class="kom">// Nastavení barvy</span></p>
<p class="src"></p>
<p class="src2">glBegin(GL_TRIANGLE_STRIP);</p>
<p class="src3">glTexCoord2f(0.0f, 0.0f); glVertex2f(q[0].x, q[0].y);</p>
<p class="src3">glTexCoord2f(0.0f, 1.0f); glVertex2f(q[1].x, q[1].y);</p>
<p class="src3">glTexCoord2f(1.0f, 0.0f); glVertex2f(q[2].x, q[2].y);</p>
<p class="src3">glTexCoord2f(1.0f, 1.0f); glVertex2f(q[3].x, q[3].y);</p>
<p class="src2">glEnd();</p>
<p class="src1">glPopMatrix();<span class="kom">// Obnovení matice</span></p>
<p class="src0">}</p>

<p>Tak to by z kódu bylo v¹echno. Pomocí kláves W, S, A, D mù¾ete v programu mìnit smìr kamery. Klávesy 1 a 2 zapínají/vypínají výpisy informací. Z a C nastavují kameøe konstantní rychlost a X ji zastavuje.</p>

<p>Samozøejmì nejsem první èlovìk, který vytváøel èoèkové efekty, a proto mù¾ete dole najít pár odkazù, které mi pøi psaní pomohly. Chtìl bych také podìkovat Davu Steerovi, Cameron Tidwell, Bertu Sammonsovi a Brannon Martidale za zpìtnou vazbu a testování kódu na rozlièném hardware.</p>

<ul>
<li><?OdkazBlank('http://www.gamedev.net/reference/articles/article874.asp');?></li>
<li><?OdkazBlank('http://www.gamedev.net/reference/articles/article813.asp');?></li>
<li><?OdkazBlank('http://www.opengl.org/developers/code/mjktips/lensflare/');?></li>
<li><?OdkazBlank('http://www.markmorley.com/opengl/frustumculling.html');?></li>
<li><?OdkazBlank('http://oss.sgi.com/projects/ogl-sample/registry/HP/occlusion_test.txt');?></li>
<li><?OdkazBlank('http://oss.sgi.com/projects/ogl-sample/registry/NV/occlusion_query.txt');?></li>
</ul>

<p class="autor">napsal: Vic Hollis <?VypisEmail('vichollis@comcast.netVic');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3>Poznámky Daria Corna - rIO ze Spinning Kids</h3>

<p>Pøidal jsem nìkolik testù pro zji¹tìní objektù ve scénì pøed èoèkovým efektem (na pozici zdroje svìtla). V takovém pøípadì se záøe vypíná. Nový kód by mìl být dobøe okomentován a je oznaèen øetìzcem # New Stuff #. Jeho pøípadné odstranìní by nemìlo èinit problémy. Modifikace jsou následující:</p>

<ul>
<li>Nová metoda tøídy glCamera nazvaná IsOccluded(), která vrací true v pøípadì, ¾e se pøed svìtlem nachází nìjaký objekt</li>
<li>Nìkolik promìnných pro gluCylinder (pou¾it jako objekt stínící svìtlu)</li>
<li>Zmìny v glDraw() pro vykreslení stínícího objektu</li>
<li>Deinicializaèní kód pro quadratic</li>
</ul>

<p>Doufám, ¾e se vám modifikovaná verze bude líbit více. Jako domácí úkol si mù¾ete zkusil testovat více ne¾ jeden bod na souøadnicích svìtla, aby se záøe skokovì nevypínala, ale postupnì mizela.</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/lesson44.zip">Visual C++</a> kód této lekce.</li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/lesson44_bcb6.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:conglth@hotmail.com">Le Thanh Cong</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/lesson44.zip">Dev C++</a> kód této lekce. ( <a href="mailto:michael@mudsplat.com">Michael Small</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/lesson44.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/vs_net/lesson44.zip">Visual Studio .NET</a> kód této lekce. ( <a href="mailto:webmaster@joachimrohde.de">Joachim Rohde</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/extras/lesson44/lesson44_with_extensions.zip">Lesson 44 - With Extension Support</a> (VC++).</li>
</ul>

<?FceImgNeHeVelky(44);?>
<?FceNeHeOkolniLekce(44);?>

<?
include 'p_end.php';
?>
