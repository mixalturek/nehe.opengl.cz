<?
$g_title = 'CZ NeHe OpenGL - OpenGL okno v dialogu';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>OpenGL okno v dialogu</h1>

<p class="nadpis_clanku">Zobraz�me d�tsk� OpenGL okno v dialogu a budeme mu p�ed�vat hodnoty z�skan� z ovl�dac�ch prvk� (editboxy a radiobuttony). Periodick� p�ekreslov�n� OpenGL okna zaji��uje zpr�va WM_TIMER - troj�heln�k a �tverec budou rotovat.</p>

<p>Zdrojov� k�d pro tento �l�nek vych�z� z programu <?OdkazWeb('programy', 'Projekt - Dialog');?>, kter� mi poslal Max Zelen� <?VypisEmail('prog.max@seznam.cz');?>. Program jsem upravil, aby v�ce demonstroval mo�nost ovliv�ov�n� sc�ny hodnotami v ovl�dac�ch prvc�ch. Mus�m ale p�iznat, �e bez n�j by tento �l�nek nem�l �anci vzniknout, proto�e bych nem�l dostate�n� znalosti, jak vytvo�it d�tsk� OpenGL okno. D�ky.</p>

<p>Za�neme vygenerov�n�m klasick� Dialog aplikace pou��vaj�c� MFC. Uprav�me zdroj dialogu tak, aby vypadal p�ibli�n� jako na obr�zku. V lev� ��sti nech�me voln� m�sto pro zobrazen� d�tsk�ho OpenGL okna.</p>

<div class="okolo_img"><img src="images/clanky/dialog_resource.gif" width="480" height="332" alt="Zdroj dialogu" /></div>

<p>Pomoc� ClassWizardu p�ipoj�me prom�nn� a funkce k dialogov�m prvk�m:</p>

<ul>
<li>IDC_T_HLOUBKA - double t_hloubka</li>
<li>IDC_C_HLOUBKA - double c_hloubka</li>
<li>IDC_ROT_ANO - double rotace_ano</li>
<li>IDC_ROT_NE - bez prom�nn�</li>
<li>IDC_AKTUALIZOVAT - void CDialogDlg::OnAktualizovat()</li>
</ul>

<p>Aby se po stisku kl�vesy ENTER dialog nezav�ral p�ep�eme virtu�ln� funkci OnOK(), tak aby nic ned�lala.</p>

<p class="src0">void CDialogDlg::OnOK()<span class="kom">// Aby se po stisku kl�vesy ENTER dialog nezav�el</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// TODO: Add extra validation here</span></p>
<p class="src1"><span class="kom">// CDialog::OnOK();// Zru�it</span></p>
<p class="src0">}</p>

<p>Do deklarace t��dy dialogu p�id�me ukazatel na prom�nnou t��dy COpenGL.</p>

<p class="src0">class CDialogDlg : public CDialog<span class="kom">// T��da dialogu</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">COpenGL* gl_okno;<span class="kom">// Ukazatel na d�tsk� OpenGL okno</span></p>
<p class="src"></p>
<p class="src1">CDialogDlg(CWnd* pParent = NULL);<span class="kom">// Konstruktor</span></p>
<p class="src1">~CDialogDlg();<span class="kom">// Destruktor</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Prom�nn� a funkce generovan� ClassWizzardem</span></p>
<p class="src0">}</p>

<p>Na konci konstruktoru ukazatel inicializujeme na NULL.</p>

<p class="src0">CDialogDlg::CDialogDlg(CWnd* pParent) : CDialog(CDialogDlg::IDD, pParent)</p>
<p class="src0">{</p>
<p class="src1"><span class="kom">//{{AFX_DATA_INIT(CDialogDlg)</span></p>
<p class="src1">c_hloubka = -7.0;</p>
<p class="src1">t_hloubka = -7.0;</p>
<p class="src1">rotace_ano = 0;</p>
<p class="src1"><span class="kom">//}}AFX_DATA_INIT</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Note that LoadIcon does not require a subsequent DestroyIcon in Win32</span></p>
<p class="src1">m_hIcon = AfxGetApp()-&gt;LoadIcon(IDR_MAINFRAME);</p>
<p class="src"></p>
<p class="src1">gl_okno = NULL;<span class="kom">// Aby destruktor v�d�l, jestli bylo okno vytvo�eno</span></p>
<p class="src0">}</p>

<p>Funkce OnInitDialog() se vol� hned po vytvo�en� dialogu, t�sn� p�ed prvn�m vykreslen�m - nejlep�� m�sto pro vytvo�en� d�tsk�ho OpenGL okna.</p>

<p class="src0">BOOL CDialogDlg::OnInitDialog()<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>
<p class="src1">CDialog::OnInitDialog();</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Set the icon for this dialog.  The framework does this automatically</span></p>
<p class="src1"><span class="kom">// when the application's main window is not a dialog</span></p>
<p class="src1">SetIcon(m_hIcon, TRUE);<span class="kom">// Set big icon</span></p>
<p class="src1">SetIcon(m_hIcon, FALSE);<span class="kom">// Set small icon</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// TODO: Add extra initialization here</span></p>

<p>Vytvo��me obd�ln�k, kter� ur�uje pozici a velikost zam��len�ho d�tsk�ho okna.</p>

<p class="src1">CRect rect(7, 7, 300, 300);<span class="kom">// Pozice a velikost okna (obd�ln�k)</span></p>

<p>Alokujeme pro okno dynamickou pam� a inicializujeme jeho �lensk� prom�nn�. T_hloubka a c_hloubka ur�uj� hloubku troj�heln�ku a �tverce ve sc�n�.</p>

<p class="src1">gl_okno = new COpenGL;<span class="kom">// Dynamick� objekt t��dy</span></p>
<p class="src"></p>
<p class="src1">gl_okno-&gt;rc = rect;<span class="kom">// Nastaven� atribut� t��dy</span></p>
<p class="src1">gl_okno-&gt;t_hloubka = t_hloubka;</p>
<p class="src1">gl_okno-&gt;c_hloubka = c_hloubka;</p>

<p>Pomoc� funkce Create() okno vytvo��me.</p>

<p class="src1"><span class="kom">// Vytvo�en� a zobrazen� okna</span></p>
<p class="src1">gl_okno-&gt;Create(NULL, NULL, WS_CHILD|WS_CLIPSIBLINGS|WS_CLIPCHILDREN|WS_VISIBLE, rect, this, 0);</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Return TRUE  unless you set the focus to a control</span></p>
<p class="src0">}</p>

<p>V destruktoru ov��ujeme, jestli je ukazatel st�le nastaven na NULL. Pokud ne uvoln�me jeho pam�.</p>

<p class="src0">CDialogDlg::~CDialogDlg()<span class="kom">// Destruktor</span></p>
<p class="src0">{</p>
<p class="src1">if(gl_okno != NULL)<span class="kom">// Bylo okno vytvo�eno?</span></p>
<p class="src1">{</p>
<p class="src2">delete gl_okno;<span class="kom">// Sma�e ho</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Posledn� funkc�, kter� v t�to t��d� stoj� za vysv�tlen� je reakce na tla��tko IDC_AKTUALIZOVAT, ale �ekneme si o n� v�ce, a� budeme v�d�t, jak vypad� a funguje t��da COpenGL.</p>

<p>Pozn.: Spr�vn� bychom m�li vytvo�it je�t� jednu t��du - potomka COpenGL a a� tu upravovat. V�sledn� �l�nek by se v�ak velmi znep�ehlednil. Zapouzd�enost dat rad�i v�bec nezmi�uji...</p>

<p class="src0">class COpenGL : public CWnd<span class="kom">// T��da OpenGL okna</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">COpenGL();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~COpenGL();<span class="kom">// Destruktor</span></p>

<p>Funkci MySetPixelFormat() vol�me ve funkci OnCreate(). M� za �kol nastavit okno tak, aby podporovalo OpenGL.</p>

<p class="src1">MySetPixelFormat(HDC hdc);<span class="kom">// Nastavuje pixel form�t</span></p>

<p>Prom�nnou rc jsme inicializovali ve funkci CDialogDlg::OnInitDialog() t�sn� p�ed zavol�n�m Create(). Obsahuje velikost okna, kter� je d�le�it� pro nastaven� perspektivy. Kontext za��zen� a rendering kontext u� ur�it� zn�te. OpenGL se bez nich neobejde.</p>

<p class="src1">CRect rc;<span class="kom">// Velikost okna</span></p>
<p class="src1">HDC m_hgldc;<span class="kom">// Kontext za��zen�</span></p>
<p class="src1">HGLRC m_hglRC;<span class="kom">// Rendering kontext</span></p>

<p>N�sleduj�c� �ty�i prom�nn� slou�� pro ulo�en� hloubky objekt� ve sc�n� a �hlu nato�en�. P�ep�na� rotace zap�n�/vyp�n� ot��en� objekt�.</p>

<p class="src1">double t_hloubka;<span class="kom">// Hloubka troj�heln�ku ve sc�n�</span></p>
<p class="src1">double c_hloubka;<span class="kom">// Hloubka �tverce ve sc�n�</span></p>
<p class="src1">float t_rot;<span class="kom">// �hel rotace troj�heln�ku</span></p>
<p class="src1">float c_rot;<span class="kom">// �hel rotace �tverce</span></p>
<p class="src"></p>
<p class="src1">bool rotace;<span class="kom">// Zapnut�/vypnut� rotace objekt�</span></p>

<p>Deklarace funkc� generovan�ch ClassWizzardem.</p>

<p class="src0">protected:</p>
<p class="src1"><span class="kom">//{{AFX_MSG(COpenGL)</span></p>
<p class="src1">afx_msg int OnCreate(LPCREATESTRUCT lpCreateStruct);</p>
<p class="src1">afx_msg void OnPaint();</p>
<p class="src1">afx_msg void OnTimer(UINT nIDEvent);</p>
<p class="src1">afx_msg void OnDestroy();</p>
<p class="src1"><span class="kom">//}}AFX_MSG</span></p>
<p class="src"></p>
<p class="src1">DECLARE_MESSAGE_MAP()</p>
<p class="src0">}</p>

<p>V konstruktoru t��dy inicializujeme prom�nn�.</p>

<p class="src0">COpenGL::COpenGL()<span class="kom">// Konstruktor</span></p>
<p class="src0">{</p>
<p class="src1">t_rot = 0.0f;<span class="kom">// Nastaven� parametr� na v�choz� hodnoty</span></p>
<p class="src1">c_rot = 0.0f;</p>
<p class="src1">rotace = true;</p>
<p class="src0">}</p>

<p>Ve funkci MySetPixelFormat() nastavujeme okno, aby podporovalo OpenGL. Nebudu ji vysv�tlovat, proto�e je tato operace velmi dob�e popsan� nap�. v NeHe tutori�lu 1.</p>

<p class="src0">int COpenGL::MySetPixelFormat(HDC hdc)<span class="kom">// Nastav� pixel form�t</span></p>
<p class="src0">{</p>
<p class="src1">PIXELFORMATDESCRIPTOR *ppfd;</p>
<p class="src1">int pixelformat;</p>
<p class="src"></p>
<p class="src1">PIXELFORMATDESCRIPTOR pfd =</p>
<p class="src1">{</p>
<p class="src2">sizeof(PIXELFORMATDESCRIPTOR),<span class="kom">// Velikost struktury</span></p>
<p class="src2">1,<span class="kom">// ��slo verze</span></p>
<p class="src2">PFD_DRAW_TO_WINDOW |<span class="kom">// Podpora okna</span></p>
<p class="src2">PFD_SUPPORT_OPENGL |<span class="kom">// OpenGL</span></p>
<p class="src2">PFD_DOUBLEBUFFER,<span class="kom">// Dva buffery</span></p>
<p class="src2">PFD_TYPE_RGBA,<span class="kom">// RGBA form�t</span></p>
<p class="src2">32,<span class="kom">// Barevn� hloubka</span></p>
<p class="src2">0, 0, 0, 0, 0, 0,<span class="kom">// Bity barev ignorov�ny</span></p>
<p class="src2">8,<span class="kom">// ��dn� alpha buffer</span></p>
<p class="src2">0,<span class="kom">// Shift Bit ignorov�n</span></p>
<p class="src2">8,<span class="kom">// ��dn� Accumulation buffer</span></p>
<p class="src2">0, 0, 0, 0,<span class="kom">// Accumulation bity ignorov�ny</span></p>
<p class="src2">64,<span class="kom">// Z-Buffer</span></p>
<p class="src2">8,<span class="kom">// Stencil buffer</span></p>
<p class="src2">8,<span class="kom">// Auxiliary buffer</span></p>
<p class="src2">PFD_MAIN_PLANE,<span class="kom">// Hlavn� vykreslovac� vrstva</span></p>
<p class="src2">0,<span class="kom">// Rezervov�no</span></p>
<p class="src2">0, 0, 0<span class="kom">// Layer Masks ignorov�ny</span></p>
<p class="src1">};</p>
<p class="src"></p>
<p class="src1">ppfd = &amp;pfd;</p>
<p class="src"></p>
<p class="src1">if ((pixelformat = ChoosePixelFormat(hdc, ppfd)) == 0)<span class="kom">// Poda�ilo se naj�t Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">::MessageBox(NULL, &quot;ChoosePixelFormat failed&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (SetPixelFormat(hdc, pixelformat, ppfd) == FALSE)<span class="kom">// Poda�ilo se nastavit Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">::MessageBox(NULL, &quot;SetPixelFormat failed&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Funkce OnCreate() se vol� po vytvo�en� okna, p�ed prvn�m vykreslen�m. Inicializujeme v n� OpenGL okno.</p>

<p class="src0">int COpenGL::OnCreate(LPCREATESTRUCT lpCreateStruct)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>

<p>Z�sk�me kontext za��zen� a nastav�me Pixel Format, aby okno podporovalo OpenGL. Potom nastav�me rendering kontext.</p>

<p class="src1">m_hgldc = ::GetDC(m_hWnd);<span class="kom">// Z�sk� kontext za��zen�</span></p>
<p class="src"></p>
<p class="src1">if(!MySetPixelFormat(m_hgldc))<span class="kom">// Poda�ilo se nastavit Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">::MessageBox(m_hWnd,&quot;MySetPixelFormat Failed!&quot;,&quot;Error&quot;,MB_OK);</p>
<p class="src2">return -1;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vytvo�� a nastav� Rendering kontext</span></p>
<p class="src1">m_hglRC = wglCreateContext(m_hgldc);</p>
<p class="src1">wglMakeCurrent(m_hgldc, m_hglRC);</p>

<p>Nastav�me OpenGL perspektivu. Tento k�d by se m�l volat p�i ka�d� zm�n� velikosti okna, ale proto�e se rozm�ry dialogu nem�n�, sta�� pouze jednou p�i inicializaci. V objektu rc m�me ulo�enou velikost okna.</p>

<p class="src1"><span class="kom">// Inicializace OpenGL okna</span></p>
<p class="src1">if(rc.bottom == 0)<span class="kom">// Proti d�len� nulou</span></p>
<p class="src1">{</p>
<p class="src2">rc.bottom = 1;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glViewport(0, 0, rc.right, rc.bottom);<span class="kom">// Resetuje aktu�ln� nastaven�</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvol� projek�n� matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">gluPerspective(45.0f, (GLfloat)rc.right / (GLfloat)rc.bottom, 1.0f, 100.0f);<span class="kom">// V�po�et perspektivy</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvol� matici Modelview</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Inicializujeme OpenGL podle konkr�tn�ch po�adavk�.</p>

<p class="src1"><span class="kom">// U�ivatelsk� inicializace</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Zapne st�nov�n�(jemn�)</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// �ern� pozad�</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastav� Depth Buffer</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povol� hloubkov� testov�n�</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkov�ho testov�n�</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektiva</span></p>

<p>Spust�me timer, kter� bude zaji��ovat periodick� p�ekreslov�n� sc�ny.</p>

<p class="src1">SetTimer(1, 30, NULL);<span class="kom">// Spust� timer pro p�ekreslov�n�</span></p>
<p class="src"></p>
<p class="src1">return 0;</p>
<p class="src0">}</p>

<p>O�et��me zpr�vu �asova�e. Vol�n� funkce Invalidate() m� za n�sledek p�ekreslen� okna.</p>

<p class="src0">void COpenGL::OnTimer(UINT nIDEvent)<span class="kom">// �asova�</span></p>
<p class="src0">{</p>
<p class="src1">Invalidate();<span class="kom">// P�ekreslen� okna</span></p>
<p class="src"></p>
<p class="src1">CWnd::OnTimer(nIDEvent);<span class="kom">// Metoda rodi�ovsk� t��dy</span></p>
<p class="src0">}</p>

<p>Funkce OnDestroy() se vol� t�sn� p�ed zav�en�m okna. Vypneme �asova� a sma�eme kontexty.</p>

<p class="src0">void COpenGL::OnDestroy()<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">CWnd::OnDestroy();<span class="kom">// Metoda rodi�ovsk� t��dy</span></p>
<p class="src"></p>
<p class="src1">KillTimer(1);<span class="kom">// Vypnut� �asova�e</span></p>
<p class="src"></p>
<p class="src1">wglMakeCurrent(NULL, NULL);<span class="kom">// Neaktivn� rendering kontext</span></p>
<p class="src1">wglDeleteContext(m_hglRC);<span class="kom">// Sma�e rendering kontext</span></p>
<p class="src1">::ReleaseDC(m_hWnd, m_hgldc);<span class="kom">// Ukon�� vykreslov�n�</span></p>
<p class="src0">}</p>

<p>OnPaint() zaji��uje renderov�n� OpenGL sc�ny. Vytvo��me n�co ve stylu NeHe tutori�lu 4. Troj�heln�k rotuje kolem osy y �tverec kolem osy x.</p>

<p class="src0">void COpenGL::OnPaint()<span class="kom">// Vykreslov�n�</span></p>
<p class="src0">{</p>
<p class="src1">CPaintDC dc(this);<span class="kom">// Kontext za��zen�</span></p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vy�ist� buffery</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslated(-1.5, 0.0, t_hloubka);<span class="kom">// Posun doleva a do hloubky</span></p>
<p class="src1">glRotatef(t_rot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Za��tek kreslen� troj�heln�k�</span></p>
<p class="src2">glColor3f(1.0f, 0.0f, 0.0f);<span class="kom">// �erven� barva</span></p>
<p class="src2">glVertex3f(0.0f, 1.0f, 0.0f);<span class="kom">// Horn� bod</span></p>
<p class="src2">glColor3f(0.0f, 1.0f, 0.0f);<span class="kom">// Zelen� barva</span></p>
<p class="src2">glVertex3f(-1.0f, -1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src2">glColor3f(0.0f, 0.0f, 1.0f);<span class="kom">// Modr� barva</span></p>
<p class="src2">glVertex3f(1.0f, -1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslated(1.5, 0.0, c_hloubka);<span class="kom">// Posun o 3 jednotky doprava</span></p>
<p class="src1">glRotatef(c_rot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src"></p>
<p class="src1">glColor3f(0.5f, 0.5f, 1.0f);<span class="kom">// Sv�tle modr� barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Za��tek kreslen� obd�ln�k�</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Lev� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Prav� horn� bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Prav� doln� bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Lev� doln� bod</span></p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">if(rotace)<span class="kom">// Je zapnut� rotace?</span></p>
<p class="src1">{</p>
<p class="src2">t_rot += 3.0f;<span class="kom">// Zv�t�� �hel</span></p>
<p class="src2">c_rot += 2.0f;</p>
<p class="src1">}</p>

<p>Nakonec nesm�me zapomenout prohodit buffery.</p>

<p class="src1">SwapBuffers(m_hgldc);<span class="kom">// Prohod� buffery</span></p>
<p class="src0">}</p>

<p>Nyn� v�te, jak se vytv��� OpenGL okno a jak pracuje. Nezapomn�li jsme na n�co? Je�t� mus�me p�ipojit ovl�dac� prvky dialogu tak, aby mohly ovliv�ovat vykreslovanou sc�nu. Po stisku tla��tka IDC_AKTUALIZOVAT z�sk�me obsah ovl�dac�ch prvk� a nastav�me polo�ky objektu t��dy COpenGL. Opravdu nic slo�it�ho.</p>

<p class="src0">void CDialogDlg::OnAktualizovat()<span class="kom">// Stisk tla��tka</span></p>
<p class="src0">{</p>
<p class="src1">if(UpdateData() == 0)<span class="kom">// Nagrabuje hodnoty z ovl�dac�ch prvk�</span></p>
<p class="src2">return;</p>
<p class="src"></p>
<p class="src1">gl_okno-&gt;t_hloubka = t_hloubka;<span class="kom">// Nastav� prom�nn�</span></p>
<p class="src1">gl_okno-&gt;c_hloubka = c_hloubka;</p>
<p class="src"></p>
<p class="src1">if(!gl_okno-&gt;rotace &amp;&amp; rotace_ano == 0)<span class="kom">// Zapnout rotaci</span></p>
<p class="src1">{</p>
<p class="src2">gl_okno-&gt;rotace = true;</p>
<p class="src2">gl_okno-&gt;SetTimer(1, 30, NULL);</p>
<p class="src1">}</p>
<p class="src1">if(gl_okno-&gt;rotace &amp;&amp; rotace_ano == 1)<span class="kom">// Vypnout rotaci</span></p>
<p class="src1">{</p>
<p class="src2">gl_okno-&gt;rotace = false;</p>
<p class="src2">gl_okno-&gt;KillTimer(1);</p>
<p class="src1">}</p>

<p>Preventivn� p�ekresl�me OpenGL sc�nu. Kdyby nebyla aktivn� rotace, timer by byl vypnut� a tud� by nevolal periodick� p�ekreslov�n�. Zm�ny by se neprojevily.</p>

<p class="src1">gl_okno-&gt;Invalidate();<span class="kom">// P�ekreslen� OpenGL okna</span></p>
<p class="src0">}</p>

<p>Pokud programujete pod MFC, nem�lo by v�m napojen� dialogov�ch prvk� d�lat v�t�� probl�my. J� jsem se v za��tc�ch nedostal p�es vytv��en� OpenGL okna, je�t� jednou proto d�kuji Maxi Zelen�mu <?VypisEmail('prog.max@seznam.cz');?>, kter� mi poslal uk�zkov� program.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/dialog.rar');?> - Visual C++, MFC</li>
</ul>

<div class="okolo_img"><img src="images/clanky/dialog.jpg" width="480" height="332" alt="Dialog" /></div>

<?
include 'p_end.php';
?>
