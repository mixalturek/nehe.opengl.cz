<?
$g_title = 'CZ NeHe OpenGL - Tøída kamery a Quaternionu';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Tøída kamery a Quaternionu</h1>

<p class="nadpis_clanku">Chcete si naprogramovat letecký simulátor? Smìr letu nad krajinou mù¾ete mìnit klávesnicí i my¹í... Vytvoøíme nìkolik u¾iteèných tøíd, která vám pomohou s matematikou, která stojí za definováním výhledu kamery a pak v¹echno spojíme do jednoho funkèního celku.</p>

<p>Ahoj, jmenuji se Vic Hollis. Pøed pár lety jsem se díky NeHe Tutoriálùm nauèil OpenGL a myslím, ¾e je èas, abych mu to oplatil. Nedávno jsem zaèal studovat Quaterniony. Abych byl èestný, je¹tì jim moc nerozumím, alespoò ne tak, jak bych mìl. Od té doby, co jsem s nimi zaèal pracovat, mohu øíci, ¾e jejich pou¾ití pro 3D rotace a hledání pozice ve scénì mù¾e hodnì vìcí ulehèit. Samozøejmì, ¾e pro tento druh vìcí nemusíte pou¾ívat zrovna Quaterniony, v¾dy mù¾ete vystaèit s obyèejnými maticemi a analytickou geometrií, nic vám nebrání ani vzít ty nejlep¹í vìci z obou. V na¹em demu se pokusíme vytvoøit dvì jednoduché tøídy. Jedna bude reprezentovat Quaternion a druhá kameru. Nebudu probírat matematiku stojící za Quaterniony, pro lep¹í pochopení sice mù¾e být dùle¾itá, ale jako programátorovi (a vsadil bych se, ¾e i vìt¹inì lidí, kteøí ètou tento èlánek) mi jde pøedev¹ím o získání výsledkù. Vytvoøíme vý¹kovou mapu reprezentující terén nebo krajinu, kolem které budeme moci létat. Pomocí tøídy kamery a Quaternionu nastavíme výhled na scénu zalo¾ený na smìru letu a rychlosti ve stylu Wing Commandera. Nalezení dal¹ích zpùsobù, jak létat okolo scény nechávám na vás, ale po pøeètení tohoto èlánku by to u¾ nemìl být takový problém.</p>

<p>Quaterniony (Pøekl.: ze slovníku - ètveøice, ètyøka) jsou na pochopení opravdu tì¾ké. Po mìsících neúspì¹ných pokusù jsem to prostì vzdal, akceptoval jsem je, jako nìco, co jednodu¹e existuje. Jsem si jistý, ¾e alespoò nìkteøí z vás sly¹eli o efektu gimbal lock. No, je to nìco, co se stane, kdy¾ zaènete aplikovat spoustu rotací najednou, které ovlivòují i následující prùchody renderovací funkcí. Quaterniony nám dávají zpùsob, jak je obejít, a pøedev¹ím proto jsou u¾iteèné. Myslím, ¾e u¾ jsem toho dost namluvil, zaèneme se vìnovat kódu.</p>

<p class="src0">class glQuaternion<span class="kom">// Tøída Quaternionu</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">glQuaternion();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~glQuaternion();<span class="kom">// Destruktor</span></p>
<p class="src"></p>
<p class="src1">glQuaternion operator *(glQuaternion q);<span class="kom">// Operátor násobení</span></p>
<p class="src1">void CreateFromAxisAngle(GLfloat x, GLfloat y, GLfloat z, GLfloat degrees);<span class="kom">// &quot;glRotatef()&quot;</span></p>
<p class="src1">void CreateMatrix(GLfloat *pMatrix);<span class="kom">// Vytvoøení matice</span></p>
<p class="src"></p>
<p class="src0">private:</p>
<p class="src1">GLfloat m_w;</p>
<p class="src1">GLfloat m_z;</p>
<p class="src1">GLfloat m_y;</p>
<p class="src1">GLfloat m_x;</p>
<p class="src0">};</p>

<p>Zaèneme funkcí, kterou budeme z Quaternion tøídy pou¾ívat asi nejèastìji. Chová se prakticky úplnì stejnì jako stará dobrá glRotatef(), v¹echny parametry jsou také stejné, ale mají trochu jiné poøadí.</p>

<p class="src0">void glQuaternion::CreateFromAxisAngle(GLfloat x, GLfloat y, GLfloat z, GLfloat degrees)<span class="kom">// &quot;glRotatef()&quot;</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat angle = GLfloat((degrees / 180.0f) * PI);<span class="kom">// Pøevedení stupòù na radiány</span></p>
<p class="src1">GLfloat result = (GLfloat)sin(angle / 2.0f);<span class="kom">// Sinus polovièního úhlu</span></p>
<p class="src"></p>
<p class="src1">m_x = GLfloat(x * result);<span class="kom">// X, y, z, w souøadnice Quaternionu</span></p>
<p class="src1">m_y = GLfloat(y * result);</p>
<p class="src1">m_z = GLfloat(z * result);</p>
<p class="src1">m_w = (GLfloat)cos(angle / 2.0f);</p>
<p class="src0">}</p>

<p>Metodu CreateMatrix() budeme také pou¾ívat hodnì èasto. Vytváøí homogenní matici o velikosti 4x4, která mù¾e být pomocí glMultMatrix() pøedána OpenGL. Z toho mimo jiné plyne, ¾e pokud budete pou¾ívat tuto tøídu, nebudete nikdy muset volat pøímo funkci glRotatef().</p>

<p class="src0">void glQuaternion::CreateMatrix(GLfloat *pMatrix)<span class="kom">// Vytvoøení OpenGL matice</span></p>
<p class="src0">{</p>
<p class="src1">if(!pMatrix)<span class="kom">// Nealokovaná pamì»?</span></p>
<p class="src1">{</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// První øádek</span></p>
<p class="src1">pMatrix[ 0] = 1.0f - 2.0f * (m_y * m_y + m_z * m_z); </p>
<p class="src1">pMatrix[ 1] = 2.0f * (m_x * m_y + m_z * m_w);</p>
<p class="src1">pMatrix[ 2] = 2.0f * (m_x * m_z - m_y * m_w);</p>
<p class="src1">pMatrix[ 3] = 0.0f;  </p>
<p class="src"></p>
<p class="src1"><span class="kom">// Druhý øádek</span></p>
<p class="src1">pMatrix[ 4] = 2.0f * (m_x * m_y - m_z * m_w);  </p>
<p class="src1">pMatrix[ 5] = 1.0f - 2.0f * (m_x * m_x + m_z * m_z); </p>
<p class="src1">pMatrix[ 6] = 2.0f * (m_z * m_y + m_x * m_w);  </p>
<p class="src1">pMatrix[ 7] = 0.0f;  </p>
<p class="src"></p>
<p class="src1"><span class="kom">// Tøetí øádek</span></p>
<p class="src1">pMatrix[ 8] = 2.0f * (m_x * m_z + m_y * m_w);</p>
<p class="src1">pMatrix[ 9] = 2.0f * (m_y * m_z - m_x * m_w);</p>
<p class="src1">pMatrix[10] = 1.0f - 2.0f * (m_x * m_x + m_y * m_y);  </p>
<p class="src1">pMatrix[11] = 0.0f;  </p>
<p class="src"></p>
<p class="src1"><span class="kom">// Ètvrtý øádek</span></p>
<p class="src1">pMatrix[12] = 0;  </p>
<p class="src1">pMatrix[13] = 0;  </p>
<p class="src1">pMatrix[14] = 0;  </p>
<p class="src1">pMatrix[15] = 1.0f;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// pMatrix[] je nyní homogenní maticí o rozmìrech 4x4 pou¾itelná v OpenGL</span></p>
<p class="src0">}</p>

<p>Dále napí¹eme operátor násobení. Nebýt jeho, nemohli bychom kombinovat více rotací a celá tøída by byla prakticky k nièemu. Pamatujte si, ¾e výsledek souèinu Quaternionù není komutativní. To znamená, ¾e Quaternion a * Quaternion b se nerovná Quaternion b * Quaternion a, co¾ vlastnì platí i pøi násobení matic.</p>

<p class="src0">glQuaternion glQuaternion::operator *(glQuaternion q)<span class="kom">// Operátor násobení</span></p>
<p class="src0">{</p>
<p class="src1">glQuaternion r;<span class="kom">// Pomocný Quaternion</span></p>
<p class="src"></p>
<p class="src1">r.m_w = m_w*q.m_w - m_x*q.m_x - m_y*q.m_y - m_z*q.m_z;<span class="kom">// Výpoèet :-)</span></p>
<p class="src1">r.m_x = m_w*q.m_x + m_x*q.m_w + m_y*q.m_z - m_z*q.m_y;</p>
<p class="src1">r.m_y = m_w*q.m_y + m_y*q.m_w + m_z*q.m_x - m_x*q.m_z;</p>
<p class="src1">r.m_z = m_w*q.m_z + m_z*q.m_w + m_x*q.m_y - m_y*q.m_x;</p>
<p class="src"></p>
<p class="src1">return r;<span class="kom">// Vrácení výsledku</span></p>
<p class="src0">}</p>

<p>Tøída Quaternionu je hotová, teï zkusíme vytvoøit tøídu kamery.</p>

<p class="src0">class glCamera<span class="kom">// Tøída kamery</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">GLfloat m_MaxPitchRate;</p>
<p class="src1">GLfloat m_MaxHeadingRate;</p>
<p class="src1">GLfloat m_HeadingDegrees;</p>
<p class="src1">GLfloat m_PitchDegrees;</p>
<p class="src1">GLfloat m_MaxForwardVelocity;<span class="kom">// Maximální rychlost pohybu</span></p>
<p class="src1">GLfloat m_ForwardVelocity;<span class="kom">// Souèasná rychlost pohybu</span></p>
<p class="src1">glQuaternion m_qHeading;<span class="kom">// Quaternion horizontální rotace</span></p>
<p class="src1">glQuaternion m_qPitch;<span class="kom">// Quaternion vertikální rotace</span></p>
<p class="src1">glPoint m_Position;<span class="kom">// Pozice</span></p>
<p class="src1">glVector m_DirectionVector;<span class="kom">// Smìrový vektor</span></p>
<p class="src"></p>
<p class="src1">void ChangeVelocity(GLfloat vel);<span class="kom">// Zmìna rychlosti</span></p>
<p class="src1">void ChangeHeading(GLfloat degrees);<span class="kom">// Zmìna horizontálního natoèení</span></p>
<p class="src1">void ChangePitch(GLfloat degrees);<span class="kom">// Zmìna vertikálního natoèení</span></p>
<p class="src1">void SetPerspective(void);<span class="kom">// Nastavení výhledu na scénu</span></p>
<p class="src"></p>
<p class="src1">glCamera();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~glCamera();<span class="kom">// Destruktor</span></p>
<p class="src0">};</p>

<p>Zaèneme funkcí SetPerspective(), která provádí translaci na po¾adované souøadnice ve scénì. Deklarujeme ¹estnácti prvkové pole pro matici. Do Pomocného Quaternionu q ulo¾íme souèin dvou Quaternionù, které reprezentují rotace na osách x a y a tím nalezneme orientaci ve scénì. Z výsledku extrahujeme matici a aplikujeme ji na OpenGL, èím¾ natoèíme kameru správným smìrem. Jako dal¹í vìc, kterou potøebujeme získat, je smìrový vektor zalo¾ený na orientaci. S jeho pomocí se budeme moci pøesunout na pozici ve scénì. Matice zalo¾ená na m_Pitch obsahuje hodnotu, kterou mù¾eme pou¾ít pro jeho j souøadnici. Za v¹imnutí stojí zajímavá vìc - tøetí øádek matice (elementy 8, 9, 10) v¾dy obsahují translaèní souøadnice, tak¾e nebudeme muset slo¾itì vypoèítávat nový smìrový vektor. Pamatujete si, jak jsem psal, ¾e násobení Quaternionù není komutativní? No, teï to s výhodou vyu¾ijeme pro získání i a k souøadnic vektoru. Pøi násobení prohodíme m_Heading a m_Pitch, díky èemu¾ získáme odli¹nou matici, ve které jsou koordináty i a k ulo¾eny. Proto¾e jsme pro rotaci pou¾ili Quaterniony s jednotkovou délkou, bude i tøetí øádek v matici obsahovat normalizovaný vektor. Tento vektor vynásobíme rychlostí pohybu a pøièteme ho k pozici. Nakonec zbývá pomocí glTranslatef() pøesunout se na ni. Mìjte na pamìti, ¾e tato funkce modeluje létání ve stylu Wing Commandera. Nebude pracovat jako MS Flight Simulator.</p>

<p class="src0">void glCamera::SetPerspective()<span class="kom">// Nastavení výhledu na scénu</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat Matrix[16];<span class="kom">// OpenGL matice</span></p>
<p class="src1">glQuaternion q;<span class="kom">// Pomocný Quaternion</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Quaterniony budou reprezentovat rotace na osách</span></p>
<p class="src1">m_qPitch.CreateFromAxisAngle(1.0f, 0.0f, 0.0f, m_PitchDegrees);</p>
<p class="src1">m_qHeading.CreateFromAxisAngle(0.0f, 1.0f, 0.0f, m_HeadingDegrees);</p>
<p class="src"></p>
<p class="src1">q = m_qPitch * m_qHeading;<span class="kom">// Kombinování rotací a ulo¾ení výsledku do q</span></p>
<p class="src1">q.CreateMatrix(Matrix);</p>
<p class="src"></p>
<p class="src1">glMultMatrixf(Matrix);<span class="kom">// Vynásobí OpenGL matici</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Souøadnice j smìrového vektoru</span></p>
<p class="src1">m_qPitch.CreateMatrix(Matrix);</p>
<p class="src1">m_DirectionVector.j = Matrix[9];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Souøadnice i a k smìrového vektoru</span></p>
<p class="src1">q = m_qHeading * m_qPitch;</p>
<p class="src1">q.CreateMatrix(Matrix);</p>
<p class="src1">m_DirectionVector.i = Matrix[8];</p>
<p class="src1">m_DirectionVector.k = Matrix[10];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zvìt¹ení smìrového vektoru pomocí rychlosti</span></p>
<p class="src1">m_DirectionVector *= m_ForwardVelocity;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Pøiètení smìrového vektoru k aktuální pozici</span></p>
<p class="src1">m_Position.x += m_DirectionVector.i;</p>
<p class="src1">m_Position.y += m_DirectionVector.j;</p>
<p class="src1">m_Position.z += m_DirectionVector.k;</p>
<p class="src"></p>
<p class="src1">glTranslatef(-m_Position.x, -m_Position.y, m_Position.z);<span class="kom">// Pøesun na novou pozici</span></p>
<p class="src0">}</p>

<p>V inicializaèním kódu nahrajeme data vý¹kové mapy a texturu terénu, také nastavíme maximální dovolenou rychlost pohybu, pitch i heading.</p>

<p class="src0">int InitGL(GLvoid)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Nastavení OpenGL</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);</p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.3f, 0.5f);</p>
<p class="src1">glClearDepth(1.0f);</p>
<p class="src1">glEnable(GL_DEPTH_TEST);</p>
<p class="src1">glDepthFunc(GL_LEQUAL);</p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);</p>
<p class="src"></p>
<p class="src1">if(!hMap.LoadRawFile(&quot;Art/Terrain1.raw&quot;, MAP_SIZE * MAP_SIZE))<span class="kom">// Vý¹ková mapa</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Failed to load Terrain1.raw.&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(!hMap.LoadTexture(&quot;Art/Dirt1.bmp&quot;))<span class="kom">// Textura vý¹kové mapy</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Failed to load terrain texture.&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastavení kamery</span></p>
<p class="src1">Cam.m_MaxForwardVelocity = 5.0f;</p>
<p class="src1">Cam.m_MaxPitchRate = 5.0f;</p>
<p class="src1">Cam.m_MaxHeadingRate = 5.0f;</p>
<p class="src1">Cam.m_PitchDegrees = 0.0f;</p>
<p class="src1">Cam.m_HeadingDegrees = 0.0f;</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace OK</span></p>
<p class="src0">}</p>

<p>Otestujeme stisky kláves. ©ipky nahoru a dolù vertikálnì naklánìjí kameru, jako byste kývali hlavou. ©ipky doleva a doprava umo¾òují horizontální zatáèení a W se S zrychlují/zpomalují pohyb. Tøída obsahuje metody pro v¹echny tyto operace. Jejich kód nebudu uvádìt, proto¾e je velmi jednoduché.</p>

<p class="src0">void CheckKeys(void)<span class="kom">// O¹etøení klávesnice</span></p>
<p class="src0">{</p>
<p class="src1">if(keys[VK_UP])<span class="kom">// ©ipka nahoru</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangePitch(5.0f);<span class="kom">// Smìr k zemi</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys[VK_DOWN])<span class="kom">// ©ipka dolu</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangePitch(-5.0f);<span class="kom">// Smìr od zemì</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys[VK_LEFT])<span class="kom">// ©ipka doleva</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangeHeading(-5.0f);<span class="kom">// Otáèení doleva</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys[VK_RIGHT])<span class="kom">// ©ipka doprava</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangeHeading(5.0f);<span class="kom">// Otáèení doprava</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys['W'] == TRUE)<span class="kom">// W</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangeVelocity(0.1f);<span class="kom">// Zrychlení pohybu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys['S'] == TRUE)<span class="kom">// S</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangeVelocity(-0.1f);<span class="kom">// Zpomalení pohybu</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Následující funkce v základu provádí to samé jako CheckKeys() s rozdílem, ¾e se nejedná o klávesnici, ale o my¹. Promìnná DeltaMouse bude obsahovat vzdálenost my¹i relativnì od støedu okna. Èím rychleji jí u¾ivatel posune, tím bude rozdíl vìt¹í a tím rychleji se kamera natoèí. Na rozdíl od klávesnice, kde nelze definovat ménì nebo více stlaèená klávesa, tato funkce neaplikuje v¾dy konstantní hodnoty.</p>

<p class="src0">void CheckMouse(void)<span class="kom">// O¹etøení my¹i</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat DeltaMouse;<span class="kom">// Rozdíl pozice od støedu okna</span></p>
<p class="src1">POINT pt;<span class="kom">// Pomocný bod</span></p>
<p class="src"></p>
<p class="src1">GetCursorPos(&amp;pt);<span class="kom">// Grabování aktuální polohy my¹i</span></p>
<p class="src"></p>
<p class="src1">MouseX = pt.x;</p>
<p class="src1">MouseY = pt.y;</p>
<p class="src"></p>
<p class="src1">if(MouseX &lt; CenterX)<span class="kom">// Posun doleva</span></p>
<p class="src1">{</p>
<p class="src2">DeltaMouse = GLfloat(CenterX - MouseX);</p>
<p class="src2">Cam.ChangeHeading(-0.2f * DeltaMouse);</p>
<p class="src"></p>
<p class="src1">}</p>
<p class="src1">else if(MouseX &gt; CenterX)<span class="kom">// Posun doprava</span></p>
<p class="src1">{</p>
<p class="src2">DeltaMouse = GLfloat(MouseX - CenterX);</p>
<p class="src2">Cam.ChangeHeading(0.2f * DeltaMouse);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(MouseY &lt; CenterY)<span class="kom">// Posun nahoru</span></p>
<p class="src1">{</p>
<p class="src2">DeltaMouse = GLfloat(CenterY - MouseY);</p>
<p class="src2">Cam.ChangePitch(-0.2f * DeltaMouse);</p>
<p class="src1">}</p>
<p class="src1">else if(MouseY &gt; CenterY)<span class="kom">// Posun dolù</span></p>
<p class="src1">{</p>
<p class="src2">DeltaMouse = GLfloat(MouseY - CenterY);</p>
<p class="src2">Cam.ChangePitch(0.2f * DeltaMouse);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">MouseX = CenterX;<span class="kom">// Obnovení pro dal¹í prùchod</span></p>
<p class="src1">MouseY = CenterY;</p>
<p class="src"></p>
<p class="src1">SetCursorPos(CenterX, CenterY);<span class="kom">// My¹ uprostøed okna</span></p>
<p class="src0">}</p>

<p>Na samý závìr jsme si nechali vykreslování. Sma¾eme obrazovku, resetujeme matici a potom s pomocí tøídy natoèíme kameru správným smìrem. dále zmìníme mìøítko vzdálenosti na osách a vykreslíme vý¹kovou mapu. Jako poslední pøed ukonèením funkce o¹etøíme zásahy u¾ivatele.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma¾e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">Cam.SetPerspective();<span class="kom">// Nastavení výhledu kamery</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vý¹ková mapa</span></p>
<p class="src1">glScalef(hMap.m_ScaleValue, hMap.m_ScaleValue * HEIGHT_RATIO, hMap.m_ScaleValue);</p>
<p class="src1">hMap.DrawHeightMap();</p>
<p class="src"></p>
<p class="src1">CheckKeys();<span class="kom">// Test klávesnice</span></p>
<p class="src1">CheckMouse();<span class="kom">// Test my¹i</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V¹e OK</span></p>
<p class="src0">}</p>

<p>Je zde je¹tì pár vìcí, které by se mìly o OpenGL a Quaternionech obecnì øíct. Toto demo by ¹lo velmi snadno naprogramovat i bez Quaternionù pouze s glRotatef() na otáèení a glGetFloatv() na získání modelview matice pro smìrový vektor. Tak¾e proè pou¾ívat Quaterniony? No, opravdu je nepotøebuejete, alespoò ne na nìco podobného, jako je toto demo. Pøipadá mi, jako by mezi lidmi panoval názor, ¾e aby mohli vytvoøit nìco ve stylu leteckého simulátoru, potøebují spoustu high tech matematických tøíd. OpenGL pro vás samo o sobì uchovává záznam vìt¹iny potøebných informací, tak¾e opravdu neexistuje ¾ádný dùvod do kódu vkládat extra výpoèty pro tento druh operací, navíc vìt¹inou zpomalují. U¾ jsem vidìl spousty zdrojových kódù, které provádìly snad v¹echny druhy ¹ílené vektorové matematiky, stejnì tak operace s maticemi a také ty, které nic takového nedìlaly. V jedné dobì jsem vìøil, ¾e mi Quaterniony dovolí udìlat témìø cokoli, jen jim tak rozumìt. Poté, co jsem se koneènì nauèil, jak pracují, zjistil jsem, ¾e je nikdy na prvním místì nebudu pou¾ívat. Nemìjte mi to za zlé. Neøíkám, ¾e jsou k nièemu nebo podobnì. Jako ka¾dá vìc, mají i oni své pou¾ití. Jsem si jistý, ¾e nastanou pøípady, kdy je mo¾ná budete chtít pou¾ít a teï u¾ mù¾ete. Chci jen poukázat, ¾e je pro létání okolo scény vùbec nepotøebujete, hodit se mù¾e i starý dobrý OpenGL kód. Kdybyste nahradili SetPerspective() za kód dole, dostali byste úplnì stejný výsledek, ale mo¾ná tou¾íte po efektu gimbal lock, který byl zmínìn døíve. Pro udr¾ení jednoduchosti nebyl v tomto kódu pou¾it. Kdy¾ kombinujete nìkolik rotací a neresetujete pøi ka¾dém prùchodu renderingem matici, gimbal lock èasto zpùsobuje bolest hlavy. My jsme jako správní OpenGL programátoøi glLoadIdentity() v¾dy volali.</p>

<p class="src0">void glCamera::SetPerspective()<span class="kom">// Ukázka alternativního kódu</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat Matrix[16];</p>
<p class="src"></p>
<p class="src1">glRotatef(m_HeadingDegrees, 0.0f, 1.0f, 0.0f);</p>
<p class="src1">glRotatef(m_PitchDegrees, 1.0f, 0.0f, 0.0f);</p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, Matrix);</p>
<p class="src"></p>
<p class="src1">m_DirectionVector.i = Matrix[8];</p>
<p class="src1">m_DirectionVector.k = Matrix[10];</p>
<p class="src"></p>
<p class="src1">glLoadIdentity();</p>
<p class="src1">glRotatef(m_PitchDegrees, 1.0f, 0.0f, 0.0f);</p>
<p class="src"></p>
<p class="src1">glGetFloatv(GL_MODELVIEW_MATRIX, Matrix);</p>
<p class="src1">m_DirectionVector.j = Matrix[9];</p>
<p class="src"></p>
<p class="src1">glRotatef(m_HeadingDegrees, 0.0f, 1.0f, 0.0f);</p>
<p class="src"></p>
<p class="src1">m_DirectionVector *= m_ForwardVelocity;<span class="kom">// Pøidat smìru i rychlost</span></p>
<p class="src"></p>
<p class="src1">m_Position.x += m_DirectionVector.i;<span class="kom">// K pozici pøièíst vektor</span></p>
<p class="src1">m_Position.y += m_DirectionVector.j;</p>
<p class="src1">m_Position.z += m_DirectionVector.k;</p>
<p class="src"></p>
<p class="src1">glTranslatef(-m_Position.x, -m_Position.y, m_Position.z);<span class="kom">// Pøesun na novou pozici</span></p>
<p class="src0">}</p>

<p>Doufám, ¾e tento kód nìkde s výhodou pou¾ijete. Chtìl bych podìkovat DigiBenovi z <?OdkazBlank('http://www.gametutorials.com/');?> za jeho tutoriál na Quaterniony. Odnìkud jsem se je pøece nauèit musel :-)</p>

<p class="autor">napsal: Vic Hollis <?VypisEmail('vichollis@comcast.net');?><br />
pøelo¾il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/quaternion_camera_class.zip">Visual C++</a></li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/quaternion_camera_class.zip">Borland C++ Builder 6</a> kód této lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/quaternion_camera_class.zip">Code Warrior 5.3</a> kód této lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/quaternion_camera_class.zip">Dev C++</a> kód této lekce. ( <a href="mailto:nehe@coffeewizard.co.uk">Mike</a> )</li>
</ul>

<div class="okolo_img"><img src="images/clanky/kamera.jpg" class="nehe_velky" alt="Tøída kamery" /></div>

<?
include 'p_end.php';
?>
