<?
$g_title = 'CZ NeHe OpenGL - T��da kamery a Quaternionu';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>T��da kamery a Quaternionu</h1>

<p class="nadpis_clanku">Chcete si naprogramovat leteck� simul�tor? Sm�r letu nad krajinou m��ete m�nit kl�vesnic� i my��... Vytvo��me n�kolik u�ite�n�ch t��d, kter� v�m pomohou s matematikou, kter� stoj� za definov�n�m v�hledu kamery a pak v�echno spoj�me do jednoho funk�n�ho celku.</p>

<p>Ahoj, jmenuji se Vic Hollis. P�ed p�r lety jsem se d�ky NeHe Tutori�l�m nau�il OpenGL a mysl�m, �e je �as, abych mu to oplatil. Ned�vno jsem za�al studovat Quaterniony. Abych byl �estn�, je�t� jim moc nerozum�m, alespo� ne tak, jak bych m�l. Od t� doby, co jsem s nimi za�al pracovat, mohu ��ci, �e jejich pou�it� pro 3D rotace a hled�n� pozice ve sc�n� m��e hodn� v�c� uleh�it. Samoz�ejm�, �e pro tento druh v�c� nemus�te pou��vat zrovna Quaterniony, v�dy m��ete vysta�it s oby�ejn�mi maticemi a analytickou geometri�, nic v�m nebr�n� ani vz�t ty nejlep�� v�ci z obou. V na�em demu se pokus�me vytvo�it dv� jednoduch� t��dy. Jedna bude reprezentovat Quaternion a druh� kameru. Nebudu prob�rat matematiku stoj�c� za Quaterniony, pro lep�� pochopen� sice m��e b�t d�le�it�, ale jako program�torovi (a vsadil bych se, �e i v�t�in� lid�, kte�� �tou tento �l�nek) mi jde p�edev��m o z�sk�n� v�sledk�. Vytvo��me v��kovou mapu reprezentuj�c� ter�n nebo krajinu, kolem kter� budeme moci l�tat. Pomoc� t��dy kamery a Quaternionu nastav�me v�hled na sc�nu zalo�en� na sm�ru letu a rychlosti ve stylu Wing Commandera. Nalezen� dal��ch zp�sob�, jak l�tat okolo sc�ny nech�v�m na v�s, ale po p�e�ten� tohoto �l�nku by to u� nem�l b�t takov� probl�m.</p>

<p>Quaterniony (P�ekl.: ze slovn�ku - �tve�ice, �ty�ka) jsou na pochopen� opravdu t�k�. Po m�s�c�ch ne�sp�n�ch pokus� jsem to prost� vzdal, akceptoval jsem je, jako n�co, co jednodu�e existuje. Jsem si jist�, �e alespo� n�kte�� z v�s sly�eli o efektu gimbal lock. No, je to n�co, co se stane, kdy� za�nete aplikovat spoustu rotac� najednou, kter� ovliv�uj� i n�sleduj�c� pr�chody renderovac� funkc�. Quaterniony n�m d�vaj� zp�sob, jak je obej�t, a p�edev��m proto jsou u�ite�n�. Mysl�m, �e u� jsem toho dost namluvil, za�neme se v�novat k�du.</p>

<p class="src0">class glQuaternion<span class="kom">// T��da Quaternionu</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">glQuaternion();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~glQuaternion();<span class="kom">// Destruktor</span></p>
<p class="src"></p>
<p class="src1">glQuaternion operator *(glQuaternion q);<span class="kom">// Oper�tor n�soben�</span></p>
<p class="src1">void CreateFromAxisAngle(GLfloat x, GLfloat y, GLfloat z, GLfloat degrees);<span class="kom">// &quot;glRotatef()&quot;</span></p>
<p class="src1">void CreateMatrix(GLfloat *pMatrix);<span class="kom">// Vytvo�en� matice</span></p>
<p class="src"></p>
<p class="src0">private:</p>
<p class="src1">GLfloat m_w;</p>
<p class="src1">GLfloat m_z;</p>
<p class="src1">GLfloat m_y;</p>
<p class="src1">GLfloat m_x;</p>
<p class="src0">};</p>

<p>Za�neme funkc�, kterou budeme z Quaternion t��dy pou��vat asi nej�ast�ji. Chov� se prakticky �pln� stejn� jako star� dobr� glRotatef(), v�echny parametry jsou tak� stejn�, ale maj� trochu jin� po�ad�.</p>

<p class="src0">void glQuaternion::CreateFromAxisAngle(GLfloat x, GLfloat y, GLfloat z, GLfloat degrees)<span class="kom">// &quot;glRotatef()&quot;</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat angle = GLfloat((degrees / 180.0f) * PI);<span class="kom">// P�eveden� stup�� na radi�ny</span></p>
<p class="src1">GLfloat result = (GLfloat)sin(angle / 2.0f);<span class="kom">// Sinus polovi�n�ho �hlu</span></p>
<p class="src"></p>
<p class="src1">m_x = GLfloat(x * result);<span class="kom">// X, y, z, w sou�adnice Quaternionu</span></p>
<p class="src1">m_y = GLfloat(y * result);</p>
<p class="src1">m_z = GLfloat(z * result);</p>
<p class="src1">m_w = (GLfloat)cos(angle / 2.0f);</p>
<p class="src0">}</p>

<p>Metodu CreateMatrix() budeme tak� pou��vat hodn� �asto. Vytv��� homogenn� matici o velikosti 4x4, kter� m��e b�t pomoc� glMultMatrix() p�ed�na OpenGL. Z toho mimo jin� plyne, �e pokud budete pou��vat tuto t��du, nebudete nikdy muset volat p��mo funkci glRotatef().</p>

<p class="src0">void glQuaternion::CreateMatrix(GLfloat *pMatrix)<span class="kom">// Vytvo�en� OpenGL matice</span></p>
<p class="src0">{</p>
<p class="src1">if(!pMatrix)<span class="kom">// Nealokovan� pam�?</span></p>
<p class="src1">{</p>
<p class="src2">return;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Prvn� ��dek</span></p>
<p class="src1">pMatrix[ 0] = 1.0f - 2.0f * (m_y * m_y + m_z * m_z); </p>
<p class="src1">pMatrix[ 1] = 2.0f * (m_x * m_y + m_z * m_w);</p>
<p class="src1">pMatrix[ 2] = 2.0f * (m_x * m_z - m_y * m_w);</p>
<p class="src1">pMatrix[ 3] = 0.0f;  </p>
<p class="src"></p>
<p class="src1"><span class="kom">// Druh� ��dek</span></p>
<p class="src1">pMatrix[ 4] = 2.0f * (m_x * m_y - m_z * m_w);  </p>
<p class="src1">pMatrix[ 5] = 1.0f - 2.0f * (m_x * m_x + m_z * m_z); </p>
<p class="src1">pMatrix[ 6] = 2.0f * (m_z * m_y + m_x * m_w);  </p>
<p class="src1">pMatrix[ 7] = 0.0f;  </p>
<p class="src"></p>
<p class="src1"><span class="kom">// T�et� ��dek</span></p>
<p class="src1">pMatrix[ 8] = 2.0f * (m_x * m_z + m_y * m_w);</p>
<p class="src1">pMatrix[ 9] = 2.0f * (m_y * m_z - m_x * m_w);</p>
<p class="src1">pMatrix[10] = 1.0f - 2.0f * (m_x * m_x + m_y * m_y);  </p>
<p class="src1">pMatrix[11] = 0.0f;  </p>
<p class="src"></p>
<p class="src1"><span class="kom">// �tvrt� ��dek</span></p>
<p class="src1">pMatrix[12] = 0;  </p>
<p class="src1">pMatrix[13] = 0;  </p>
<p class="src1">pMatrix[14] = 0;  </p>
<p class="src1">pMatrix[15] = 1.0f;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// pMatrix[] je nyn� homogenn� matic� o rozm�rech 4x4 pou�iteln� v OpenGL</span></p>
<p class="src0">}</p>

<p>D�le nap�eme oper�tor n�soben�. Neb�t jeho, nemohli bychom kombinovat v�ce rotac� a cel� t��da by byla prakticky k ni�emu. Pamatujte si, �e v�sledek sou�inu Quaternion� nen� komutativn�. To znamen�, �e Quaternion a * Quaternion b se nerovn� Quaternion b * Quaternion a, co� vlastn� plat� i p�i n�soben� matic.</p>

<p class="src0">glQuaternion glQuaternion::operator *(glQuaternion q)<span class="kom">// Oper�tor n�soben�</span></p>
<p class="src0">{</p>
<p class="src1">glQuaternion r;<span class="kom">// Pomocn� Quaternion</span></p>
<p class="src"></p>
<p class="src1">r.m_w = m_w*q.m_w - m_x*q.m_x - m_y*q.m_y - m_z*q.m_z;<span class="kom">// V�po�et :-)</span></p>
<p class="src1">r.m_x = m_w*q.m_x + m_x*q.m_w + m_y*q.m_z - m_z*q.m_y;</p>
<p class="src1">r.m_y = m_w*q.m_y + m_y*q.m_w + m_z*q.m_x - m_x*q.m_z;</p>
<p class="src1">r.m_z = m_w*q.m_z + m_z*q.m_w + m_x*q.m_y - m_y*q.m_x;</p>
<p class="src"></p>
<p class="src1">return r;<span class="kom">// Vr�cen� v�sledku</span></p>
<p class="src0">}</p>

<p>T��da Quaternionu je hotov�, te� zkus�me vytvo�it t��du kamery.</p>

<p class="src0">class glCamera<span class="kom">// T��da kamery</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">GLfloat m_MaxPitchRate;</p>
<p class="src1">GLfloat m_MaxHeadingRate;</p>
<p class="src1">GLfloat m_HeadingDegrees;</p>
<p class="src1">GLfloat m_PitchDegrees;</p>
<p class="src1">GLfloat m_MaxForwardVelocity;<span class="kom">// Maxim�ln� rychlost pohybu</span></p>
<p class="src1">GLfloat m_ForwardVelocity;<span class="kom">// Sou�asn� rychlost pohybu</span></p>
<p class="src1">glQuaternion m_qHeading;<span class="kom">// Quaternion horizont�ln� rotace</span></p>
<p class="src1">glQuaternion m_qPitch;<span class="kom">// Quaternion vertik�ln� rotace</span></p>
<p class="src1">glPoint m_Position;<span class="kom">// Pozice</span></p>
<p class="src1">glVector m_DirectionVector;<span class="kom">// Sm�rov� vektor</span></p>
<p class="src"></p>
<p class="src1">void ChangeVelocity(GLfloat vel);<span class="kom">// Zm�na rychlosti</span></p>
<p class="src1">void ChangeHeading(GLfloat degrees);<span class="kom">// Zm�na horizont�ln�ho nato�en�</span></p>
<p class="src1">void ChangePitch(GLfloat degrees);<span class="kom">// Zm�na vertik�ln�ho nato�en�</span></p>
<p class="src1">void SetPerspective(void);<span class="kom">// Nastaven� v�hledu na sc�nu</span></p>
<p class="src"></p>
<p class="src1">glCamera();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~glCamera();<span class="kom">// Destruktor</span></p>
<p class="src0">};</p>

<p>Za�neme funkc� SetPerspective(), kter� prov�d� translaci na po�adovan� sou�adnice ve sc�n�. Deklarujeme �estn�cti prvkov� pole pro matici. Do Pomocn�ho Quaternionu q ulo��me sou�in dvou Quaternion�, kter� reprezentuj� rotace na os�ch x a y a t�m nalezneme orientaci ve sc�n�. Z v�sledku extrahujeme matici a aplikujeme ji na OpenGL, ��m� nato��me kameru spr�vn�m sm�rem. Jako dal�� v�c, kterou pot�ebujeme z�skat, je sm�rov� vektor zalo�en� na orientaci. S jeho pomoc� se budeme moci p�esunout na pozici ve sc�n�. Matice zalo�en� na m_Pitch obsahuje hodnotu, kterou m��eme pou��t pro jeho j sou�adnici. Za v�imnut� stoj� zaj�mav� v�c - t�et� ��dek matice (elementy 8, 9, 10) v�dy obsahuj� transla�n� sou�adnice, tak�e nebudeme muset slo�it� vypo��t�vat nov� sm�rov� vektor. Pamatujete si, jak jsem psal, �e n�soben� Quaternion� nen� komutativn�? No, te� to s v�hodou vyu�ijeme pro z�sk�n� i a k sou�adnic vektoru. P�i n�soben� prohod�me m_Heading a m_Pitch, d�ky �emu� z�sk�me odli�nou matici, ve kter� jsou koordin�ty i a k ulo�eny. Proto�e jsme pro rotaci pou�ili Quaterniony s jednotkovou d�lkou, bude i t�et� ��dek v matici obsahovat normalizovan� vektor. Tento vektor vyn�sob�me rychlost� pohybu a p�i�teme ho k pozici. Nakonec zb�v� pomoc� glTranslatef() p�esunout se na ni. M�jte na pam�ti, �e tato funkce modeluje l�t�n� ve stylu Wing Commandera. Nebude pracovat jako MS Flight Simulator.</p>

<p class="src0">void glCamera::SetPerspective()<span class="kom">// Nastaven� v�hledu na sc�nu</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat Matrix[16];<span class="kom">// OpenGL matice</span></p>
<p class="src1">glQuaternion q;<span class="kom">// Pomocn� Quaternion</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Quaterniony budou reprezentovat rotace na os�ch</span></p>
<p class="src1">m_qPitch.CreateFromAxisAngle(1.0f, 0.0f, 0.0f, m_PitchDegrees);</p>
<p class="src1">m_qHeading.CreateFromAxisAngle(0.0f, 1.0f, 0.0f, m_HeadingDegrees);</p>
<p class="src"></p>
<p class="src1">q = m_qPitch * m_qHeading;<span class="kom">// Kombinov�n� rotac� a ulo�en� v�sledku do q</span></p>
<p class="src1">q.CreateMatrix(Matrix);</p>
<p class="src"></p>
<p class="src1">glMultMatrixf(Matrix);<span class="kom">// Vyn�sob� OpenGL matici</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Sou�adnice j sm�rov�ho vektoru</span></p>
<p class="src1">m_qPitch.CreateMatrix(Matrix);</p>
<p class="src1">m_DirectionVector.j = Matrix[9];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Sou�adnice i a k sm�rov�ho vektoru</span></p>
<p class="src1">q = m_qHeading * m_qPitch;</p>
<p class="src1">q.CreateMatrix(Matrix);</p>
<p class="src1">m_DirectionVector.i = Matrix[8];</p>
<p class="src1">m_DirectionVector.k = Matrix[10];</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Zv�t�en� sm�rov�ho vektoru pomoc� rychlosti</span></p>
<p class="src1">m_DirectionVector *= m_ForwardVelocity;</p>
<p class="src"></p>
<p class="src1"><span class="kom">// P�i�ten� sm�rov�ho vektoru k aktu�ln� pozici</span></p>
<p class="src1">m_Position.x += m_DirectionVector.i;</p>
<p class="src1">m_Position.y += m_DirectionVector.j;</p>
<p class="src1">m_Position.z += m_DirectionVector.k;</p>
<p class="src"></p>
<p class="src1">glTranslatef(-m_Position.x, -m_Position.y, m_Position.z);<span class="kom">// P�esun na novou pozici</span></p>
<p class="src0">}</p>

<p>V inicializa�n�m k�du nahrajeme data v��kov� mapy a texturu ter�nu, tak� nastav�me maxim�ln� dovolenou rychlost pohybu, pitch i heading.</p>

<p class="src0">int InitGL(GLvoid)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// Nastaven� OpenGL</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);</p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.3f, 0.5f);</p>
<p class="src1">glClearDepth(1.0f);</p>
<p class="src1">glEnable(GL_DEPTH_TEST);</p>
<p class="src1">glDepthFunc(GL_LEQUAL);</p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);</p>
<p class="src"></p>
<p class="src1">if(!hMap.LoadRawFile(&quot;Art/Terrain1.raw&quot;, MAP_SIZE * MAP_SIZE))<span class="kom">// V��kov� mapa</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Failed to load Terrain1.raw.&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(!hMap.LoadTexture(&quot;Art/Dirt1.bmp&quot;))<span class="kom">// Textura v��kov� mapy</span></p>
<p class="src1">{</p>
<p class="src2">MessageBox(NULL, &quot;Failed to load terrain texture.&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Nastaven� kamery</span></p>
<p class="src1">Cam.m_MaxForwardVelocity = 5.0f;</p>
<p class="src1">Cam.m_MaxPitchRate = 5.0f;</p>
<p class="src1">Cam.m_MaxHeadingRate = 5.0f;</p>
<p class="src1">Cam.m_PitchDegrees = 0.0f;</p>
<p class="src1">Cam.m_HeadingDegrees = 0.0f;</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Inicializace OK</span></p>
<p class="src0">}</p>

<p>Otestujeme stisky kl�ves. �ipky nahoru a dol� vertik�ln� nakl�n�j� kameru, jako byste k�vali hlavou. �ipky doleva a doprava umo��uj� horizont�ln� zat��en� a W se S zrychluj�/zpomaluj� pohyb. T��da obsahuje metody pro v�echny tyto operace. Jejich k�d nebudu uv�d�t, proto�e je velmi jednoduch�.</p>

<p class="src0">void CheckKeys(void)<span class="kom">// O�et�en� kl�vesnice</span></p>
<p class="src0">{</p>
<p class="src1">if(keys[VK_UP])<span class="kom">// �ipka nahoru</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangePitch(5.0f);<span class="kom">// Sm�r k zemi</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys[VK_DOWN])<span class="kom">// �ipka dolu</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangePitch(-5.0f);<span class="kom">// Sm�r od zem�</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys[VK_LEFT])<span class="kom">// �ipka doleva</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangeHeading(-5.0f);<span class="kom">// Ot��en� doleva</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys[VK_RIGHT])<span class="kom">// �ipka doprava</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangeHeading(5.0f);<span class="kom">// Ot��en� doprava</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys['W'] == TRUE)<span class="kom">// W</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangeVelocity(0.1f);<span class="kom">// Zrychlen� pohybu</span></p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if(keys['S'] == TRUE)<span class="kom">// S</span></p>
<p class="src1">{</p>
<p class="src2">Cam.ChangeVelocity(-0.1f);<span class="kom">// Zpomalen� pohybu</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>N�sleduj�c� funkce v z�kladu prov�d� to sam� jako CheckKeys() s rozd�lem, �e se nejedn� o kl�vesnici, ale o my�. Prom�nn� DeltaMouse bude obsahovat vzd�lenost my�i relativn� od st�edu okna. ��m rychleji j� u�ivatel posune, t�m bude rozd�l v�t�� a t�m rychleji se kamera nato��. Na rozd�l od kl�vesnice, kde nelze definovat m�n� nebo v�ce stla�en� kl�vesa, tato funkce neaplikuje v�dy konstantn� hodnoty.</p>

<p class="src0">void CheckMouse(void)<span class="kom">// O�et�en� my�i</span></p>
<p class="src0">{</p>
<p class="src1">GLfloat DeltaMouse;<span class="kom">// Rozd�l pozice od st�edu okna</span></p>
<p class="src1">POINT pt;<span class="kom">// Pomocn� bod</span></p>
<p class="src"></p>
<p class="src1">GetCursorPos(&amp;pt);<span class="kom">// Grabov�n� aktu�ln� polohy my�i</span></p>
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
<p class="src1">else if(MouseY &gt; CenterY)<span class="kom">// Posun dol�</span></p>
<p class="src1">{</p>
<p class="src2">DeltaMouse = GLfloat(MouseY - CenterY);</p>
<p class="src2">Cam.ChangePitch(0.2f * DeltaMouse);</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">MouseX = CenterX;<span class="kom">// Obnoven� pro dal�� pr�chod</span></p>
<p class="src1">MouseY = CenterY;</p>
<p class="src"></p>
<p class="src1">SetCursorPos(CenterX, CenterY);<span class="kom">// My� uprost�ed okna</span></p>
<p class="src0">}</p>

<p>Na sam� z�v�r jsme si nechali vykreslov�n�. Sma�eme obrazovku, resetujeme matici a potom s pomoc� t��dy nato��me kameru spr�vn�m sm�rem. d�le zm�n�me m���tko vzd�lenosti na os�ch a vykresl�me v��kovou mapu. Jako posledn� p�ed ukon�en�m funkce o�et��me z�sahy u�ivatele.</p>

<p class="src0">int DrawGLScene(GLvoid)<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Sma�e buffery</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">Cam.SetPerspective();<span class="kom">// Nastaven� v�hledu kamery</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// V��kov� mapa</span></p>
<p class="src1">glScalef(hMap.m_ScaleValue, hMap.m_ScaleValue * HEIGHT_RATIO, hMap.m_ScaleValue);</p>
<p class="src1">hMap.DrawHeightMap();</p>
<p class="src"></p>
<p class="src1">CheckKeys();<span class="kom">// Test kl�vesnice</span></p>
<p class="src1">CheckMouse();<span class="kom">// Test my�i</span></p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// V�e OK</span></p>
<p class="src0">}</p>

<p>Je zde je�t� p�r v�c�, kter� by se m�ly o OpenGL a Quaternionech obecn� ��ct. Toto demo by �lo velmi snadno naprogramovat i bez Quaternion� pouze s glRotatef() na ot��en� a glGetFloatv() na z�sk�n� modelview matice pro sm�rov� vektor. Tak�e pro� pou��vat Quaterniony? No, opravdu je nepot�ebuejete, alespo� ne na n�co podobn�ho, jako je toto demo. P�ipad� mi, jako by mezi lidmi panoval n�zor, �e aby mohli vytvo�it n�co ve stylu leteck�ho simul�toru, pot�ebuj� spoustu high tech matematick�ch t��d. OpenGL pro v�s samo o sob� uchov�v� z�znam v�t�iny pot�ebn�ch informac�, tak�e opravdu neexistuje ��dn� d�vod do k�du vkl�dat extra v�po�ty pro tento druh operac�, nav�c v�t�inou zpomaluj�. U� jsem vid�l spousty zdrojov�ch k�d�, kter� prov�d�ly snad v�echny druhy ��len� vektorov� matematiky, stejn� tak operace s maticemi a tak� ty, kter� nic takov�ho ned�laly. V jedn� dob� jsem v��il, �e mi Quaterniony dovol� ud�lat t�m�� cokoli, jen jim tak rozum�t. Pot�, co jsem se kone�n� nau�il, jak pracuj�, zjistil jsem, �e je nikdy na prvn�m m�st� nebudu pou��vat. Nem�jte mi to za zl�. Ne��k�m, �e jsou k ni�emu nebo podobn�. Jako ka�d� v�c, maj� i oni sv� pou�it�. Jsem si jist�, �e nastanou p��pady, kdy je mo�n� budete cht�t pou��t a te� u� m��ete. Chci jen pouk�zat, �e je pro l�t�n� okolo sc�ny v�bec nepot�ebujete, hodit se m��e i star� dobr� OpenGL k�d. Kdybyste nahradili SetPerspective() za k�d dole, dostali byste �pln� stejn� v�sledek, ale mo�n� tou��te po efektu gimbal lock, kter� byl zm�n�n d��ve. Pro udr�en� jednoduchosti nebyl v tomto k�du pou�it. Kdy� kombinujete n�kolik rotac� a neresetujete p�i ka�d�m pr�chodu renderingem matici, gimbal lock �asto zp�sobuje bolest hlavy. My jsme jako spr�vn� OpenGL program�to�i glLoadIdentity() v�dy volali.</p>

<p class="src0">void glCamera::SetPerspective()<span class="kom">// Uk�zka alternativn�ho k�du</span></p>
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
<p class="src1">m_DirectionVector *= m_ForwardVelocity;<span class="kom">// P�idat sm�ru i rychlost</span></p>
<p class="src"></p>
<p class="src1">m_Position.x += m_DirectionVector.i;<span class="kom">// K pozici p�i��st vektor</span></p>
<p class="src1">m_Position.y += m_DirectionVector.j;</p>
<p class="src1">m_Position.z += m_DirectionVector.k;</p>
<p class="src"></p>
<p class="src1">glTranslatef(-m_Position.x, -m_Position.y, m_Position.z);<span class="kom">// P�esun na novou pozici</span></p>
<p class="src0">}</p>

<p>Douf�m, �e tento k�d n�kde s v�hodou pou�ijete. Cht�l bych pod�kovat DigiBenovi z <?OdkazBlank('http://www.gametutorials.com/');?> za jeho tutori�l na Quaterniony. Odn�kud jsem se je p�ece nau�it musel :-)</p>

<p class="autor">napsal: Vic Hollis <?VypisEmail('vichollis@comcast.net');?><br />
p�elo�il: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><a href="http://nehe.gamedev.net/data/lessons/vc/quaternion_camera_class.zip">Visual C++</a></li>
<li><a href="http://nehe.gamedev.net/data/lessons/bcb6/quaternion_camera_class.zip">Borland C++ Builder 6</a> k�d t�to lekce. ( <a href="mailto:Dominique@SavageSoftware.com.au">Dominique Louis</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/cwarrior/quaternion_camera_class.zip">Code Warrior 5.3</a> k�d t�to lekce. ( <a href="mailto:DelusionalBeing@hotmail.com">Scott Lupton</a> )</li>
<li><a href="http://nehe.gamedev.net/data/lessons/devc/quaternion_camera_class.zip">Dev C++</a> k�d t�to lekce. ( <a href="mailto:nehe@coffeewizard.co.uk">Mike</a> )</li>
</ul>

<div class="okolo_img"><img src="images/clanky/kamera.jpg" class="nehe_velky" alt="T��da kamery" /></div>

<?
include 'p_end.php';
?>
