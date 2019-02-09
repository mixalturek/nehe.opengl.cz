<?
$g_title = 'CZ NeHe OpenGL - OpenGL okno v dialogu';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>OpenGL okno v dialogu</h1>

<p class="nadpis_clanku">Zobrazíme dìtské OpenGL okno v dialogu a budeme mu pøedávat hodnoty získané z ovládacích prvkù (editboxy a radiobuttony). Periodické pøekreslování OpenGL okna zaji¹»uje zpráva WM_TIMER - trojúhelník a ètverec budou rotovat.</p>

<p>Zdrojový kód pro tento èlánek vychází z programu <?OdkazWeb('programy', 'Projekt - Dialog');?>, který mi poslal Max Zelený <?VypisEmail('prog.max@seznam.cz');?>. Program jsem upravil, aby více demonstroval mo¾nost ovlivòování scény hodnotami v ovládacích prvcích. Musím ale pøiznat, ¾e bez nìj by tento èlánek nemìl ¹anci vzniknout, proto¾e bych nemìl dostateèné znalosti, jak vytvoøit dìtské OpenGL okno. Díky.</p>

<p>Zaèneme vygenerováním klasické Dialog aplikace pou¾ívající MFC. Upravíme zdroj dialogu tak, aby vypadal pøibli¾nì jako na obrázku. V levé èásti necháme volné místo pro zobrazení dìtského OpenGL okna.</p>

<div class="okolo_img"><img src="images/clanky/dialog_resource.gif" width="480" height="332" alt="Zdroj dialogu" /></div>

<p>Pomocí ClassWizardu pøipojíme promìnné a funkce k dialogovým prvkùm:</p>

<ul>
<li>IDC_T_HLOUBKA - double t_hloubka</li>
<li>IDC_C_HLOUBKA - double c_hloubka</li>
<li>IDC_ROT_ANO - double rotace_ano</li>
<li>IDC_ROT_NE - bez promìnné</li>
<li>IDC_AKTUALIZOVAT - void CDialogDlg::OnAktualizovat()</li>
</ul>

<p>Aby se po stisku klávesy ENTER dialog nezavíral pøepí¹eme virtuální funkci OnOK(), tak aby nic nedìlala.</p>

<p class="src0">void CDialogDlg::OnOK()<span class="kom">// Aby se po stisku klávesy ENTER dialog nezavøel</span></p>
<p class="src0">{</p>
<p class="src1"><span class="kom">// TODO: Add extra validation here</span></p>
<p class="src1"><span class="kom">// CDialog::OnOK();// Zru¹it</span></p>
<p class="src0">}</p>

<p>Do deklarace tøídy dialogu pøidáme ukazatel na promìnnou tøídy COpenGL.</p>

<p class="src0">class CDialogDlg : public CDialog<span class="kom">// Tøída dialogu</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">COpenGL* gl_okno;<span class="kom">// Ukazatel na dìtské OpenGL okno</span></p>
<p class="src"></p>
<p class="src1">CDialogDlg(CWnd* pParent = NULL);<span class="kom">// Konstruktor</span></p>
<p class="src1">~CDialogDlg();<span class="kom">// Destruktor</span></p>
<p class="src"></p>
<p class="src1"><span class="kom">// Promìnné a funkce generované ClassWizzardem</span></p>
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
<p class="src1">gl_okno = NULL;<span class="kom">// Aby destruktor vìdìl, jestli bylo okno vytvoøeno</span></p>
<p class="src0">}</p>

<p>Funkce OnInitDialog() se volá hned po vytvoøení dialogu, tìsnì pøed prvním vykreslením - nejlep¹í místo pro vytvoøení dìtského OpenGL okna.</p>

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

<p>Vytvoøíme obdélník, který urèuje pozici a velikost zamý¹leného dìtského okna.</p>

<p class="src1">CRect rect(7, 7, 300, 300);<span class="kom">// Pozice a velikost okna (obdélník)</span></p>

<p>Alokujeme pro okno dynamickou pamì» a inicializujeme jeho èlenské promìnné. T_hloubka a c_hloubka urèují hloubku trojúhelníku a ètverce ve scénì.</p>

<p class="src1">gl_okno = new COpenGL;<span class="kom">// Dynamický objekt tøídy</span></p>
<p class="src"></p>
<p class="src1">gl_okno-&gt;rc = rect;<span class="kom">// Nastavení atributù tøídy</span></p>
<p class="src1">gl_okno-&gt;t_hloubka = t_hloubka;</p>
<p class="src1">gl_okno-&gt;c_hloubka = c_hloubka;</p>

<p>Pomocí funkce Create() okno vytvoøíme.</p>

<p class="src1"><span class="kom">// Vytvoøení a zobrazení okna</span></p>
<p class="src1">gl_okno-&gt;Create(NULL, NULL, WS_CHILD|WS_CLIPSIBLINGS|WS_CLIPCHILDREN|WS_VISIBLE, rect, this, 0);</p>
<p class="src"></p>
<p class="src1">return TRUE;<span class="kom">// Return TRUE  unless you set the focus to a control</span></p>
<p class="src0">}</p>

<p>V destruktoru ovìøujeme, jestli je ukazatel stále nastaven na NULL. Pokud ne uvolníme jeho pamì».</p>

<p class="src0">CDialogDlg::~CDialogDlg()<span class="kom">// Destruktor</span></p>
<p class="src0">{</p>
<p class="src1">if(gl_okno != NULL)<span class="kom">// Bylo okno vytvoøeno?</span></p>
<p class="src1">{</p>
<p class="src2">delete gl_okno;<span class="kom">// Sma¾e ho</span></p>
<p class="src1">}</p>
<p class="src0">}</p>

<p>Poslední funkcí, která v této tøídì stojí za vysvìtlení je reakce na tlaèítko IDC_AKTUALIZOVAT, ale øekneme si o ní více, a¾ budeme vìdìt, jak vypadá a funguje tøída COpenGL.</p>

<p>Pozn.: Správnì bychom mìli vytvoøit je¹tì jednu tøídu - potomka COpenGL a a¾ tu upravovat. Výsledný èlánek by se v¹ak velmi znepøehlednil. Zapouzdøenost dat rad¹i vùbec nezmiòuji...</p>

<p class="src0">class COpenGL : public CWnd<span class="kom">// Tøída OpenGL okna</span></p>
<p class="src0">{</p>
<p class="src0">public:</p>
<p class="src1">COpenGL();<span class="kom">// Konstruktor</span></p>
<p class="src1">virtual ~COpenGL();<span class="kom">// Destruktor</span></p>

<p>Funkci MySetPixelFormat() voláme ve funkci OnCreate(). Má za úkol nastavit okno tak, aby podporovalo OpenGL.</p>

<p class="src1">MySetPixelFormat(HDC hdc);<span class="kom">// Nastavuje pixel formát</span></p>

<p>Promìnnou rc jsme inicializovali ve funkci CDialogDlg::OnInitDialog() tìsnì pøed zavoláním Create(). Obsahuje velikost okna, která je dùle¾itá pro nastavení perspektivy. Kontext zaøízení a rendering kontext u¾ urèitì znáte. OpenGL se bez nich neobejde.</p>

<p class="src1">CRect rc;<span class="kom">// Velikost okna</span></p>
<p class="src1">HDC m_hgldc;<span class="kom">// Kontext zaøízení</span></p>
<p class="src1">HGLRC m_hglRC;<span class="kom">// Rendering kontext</span></p>

<p>Následující ètyøi promìnné slou¾í pro ulo¾ení hloubky objektù ve scénì a úhlu natoèení. Pøepínaè rotace zapíná/vypíná otáèení objektù.</p>

<p class="src1">double t_hloubka;<span class="kom">// Hloubka trojúhelníku ve scénì</span></p>
<p class="src1">double c_hloubka;<span class="kom">// Hloubka ètverce ve scénì</span></p>
<p class="src1">float t_rot;<span class="kom">// Úhel rotace trojúhelníku</span></p>
<p class="src1">float c_rot;<span class="kom">// Úhel rotace ètverce</span></p>
<p class="src"></p>
<p class="src1">bool rotace;<span class="kom">// Zapnutá/vypnutá rotace objektù</span></p>

<p>Deklarace funkcí generovaných ClassWizzardem.</p>

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

<p>V konstruktoru tøídy inicializujeme promìnné.</p>

<p class="src0">COpenGL::COpenGL()<span class="kom">// Konstruktor</span></p>
<p class="src0">{</p>
<p class="src1">t_rot = 0.0f;<span class="kom">// Nastavení parametrù na výchozí hodnoty</span></p>
<p class="src1">c_rot = 0.0f;</p>
<p class="src1">rotace = true;</p>
<p class="src0">}</p>

<p>Ve funkci MySetPixelFormat() nastavujeme okno, aby podporovalo OpenGL. Nebudu ji vysvìtlovat, proto¾e je tato operace velmi dobøe popsaná napø. v NeHe tutoriálu 1.</p>

<p class="src0">int COpenGL::MySetPixelFormat(HDC hdc)<span class="kom">// Nastaví pixel formát</span></p>
<p class="src0">{</p>
<p class="src1">PIXELFORMATDESCRIPTOR *ppfd;</p>
<p class="src1">int pixelformat;</p>
<p class="src"></p>
<p class="src1">PIXELFORMATDESCRIPTOR pfd =</p>
<p class="src1">{</p>
<p class="src2">sizeof(PIXELFORMATDESCRIPTOR),<span class="kom">// Velikost struktury</span></p>
<p class="src2">1,<span class="kom">// Èíslo verze</span></p>
<p class="src2">PFD_DRAW_TO_WINDOW |<span class="kom">// Podpora okna</span></p>
<p class="src2">PFD_SUPPORT_OPENGL |<span class="kom">// OpenGL</span></p>
<p class="src2">PFD_DOUBLEBUFFER,<span class="kom">// Dva buffery</span></p>
<p class="src2">PFD_TYPE_RGBA,<span class="kom">// RGBA formát</span></p>
<p class="src2">32,<span class="kom">// Barevná hloubka</span></p>
<p class="src2">0, 0, 0, 0, 0, 0,<span class="kom">// Bity barev ignorovány</span></p>
<p class="src2">8,<span class="kom">// ®ádný alpha buffer</span></p>
<p class="src2">0,<span class="kom">// Shift Bit ignorován</span></p>
<p class="src2">8,<span class="kom">// ®ádný Accumulation buffer</span></p>
<p class="src2">0, 0, 0, 0,<span class="kom">// Accumulation bity ignorovány</span></p>
<p class="src2">64,<span class="kom">// Z-Buffer</span></p>
<p class="src2">8,<span class="kom">// Stencil buffer</span></p>
<p class="src2">8,<span class="kom">// Auxiliary buffer</span></p>
<p class="src2">PFD_MAIN_PLANE,<span class="kom">// Hlavní vykreslovací vrstva</span></p>
<p class="src2">0,<span class="kom">// Rezervováno</span></p>
<p class="src2">0, 0, 0<span class="kom">// Layer Masks ignorovány</span></p>
<p class="src1">};</p>
<p class="src"></p>
<p class="src1">ppfd = &amp;pfd;</p>
<p class="src"></p>
<p class="src1">if ((pixelformat = ChoosePixelFormat(hdc, ppfd)) == 0)<span class="kom">// Podaøilo se najít Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">::MessageBox(NULL, &quot;ChoosePixelFormat failed&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">if (SetPixelFormat(hdc, pixelformat, ppfd) == FALSE)<span class="kom">// Podaøilo se nastavit Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">::MessageBox(NULL, &quot;SetPixelFormat failed&quot;, &quot;Error&quot;, MB_OK);</p>
<p class="src2">return FALSE;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">return TRUE;</p>
<p class="src0">}</p>

<p>Funkce OnCreate() se volá po vytvoøení okna, pøed prvním vykreslením. Inicializujeme v ní OpenGL okno.</p>

<p class="src0">int COpenGL::OnCreate(LPCREATESTRUCT lpCreateStruct)<span class="kom">// Inicializace</span></p>
<p class="src0">{</p>

<p>Získáme kontext zaøízení a nastavíme Pixel Format, aby okno podporovalo OpenGL. Potom nastavíme rendering kontext.</p>

<p class="src1">m_hgldc = ::GetDC(m_hWnd);<span class="kom">// Získá kontext zaøízení</span></p>
<p class="src"></p>
<p class="src1">if(!MySetPixelFormat(m_hgldc))<span class="kom">// Podaøilo se nastavit Pixel Format?</span></p>
<p class="src1">{</p>
<p class="src2">::MessageBox(m_hWnd,&quot;MySetPixelFormat Failed!&quot;,&quot;Error&quot;,MB_OK);</p>
<p class="src2">return -1;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1"><span class="kom">// Vytvoøí a nastaví Rendering kontext</span></p>
<p class="src1">m_hglRC = wglCreateContext(m_hgldc);</p>
<p class="src1">wglMakeCurrent(m_hgldc, m_hglRC);</p>

<p>Nastavíme OpenGL perspektivu. Tento kód by se mìl volat pøi ka¾dé zmìnì velikosti okna, ale proto¾e se rozmìry dialogu nemìní, staèí pouze jednou pøi inicializaci. V objektu rc máme ulo¾enou velikost okna.</p>

<p class="src1"><span class="kom">// Inicializace OpenGL okna</span></p>
<p class="src1">if(rc.bottom == 0)<span class="kom">// Proti dìlení nulou</span></p>
<p class="src1">{</p>
<p class="src2">rc.bottom = 1;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">glViewport(0, 0, rc.right, rc.bottom);<span class="kom">// Resetuje aktuální nastavení</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_PROJECTION);<span class="kom">// Zvolí projekèní matici</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src"></p>
<p class="src1">gluPerspective(45.0f, (GLfloat)rc.right / (GLfloat)rc.bottom, 1.0f, 100.0f);<span class="kom">// Výpoèet perspektivy</span></p>
<p class="src"></p>
<p class="src1">glMatrixMode(GL_MODELVIEW);<span class="kom">// Zvolí matici Modelview</span></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>

<p>Inicializujeme OpenGL podle konkrétních po¾adavkù.</p>

<p class="src1"><span class="kom">// U¾ivatelská inicializace</span></p>
<p class="src1">glShadeModel(GL_SMOOTH);<span class="kom">// Zapne stínování(jemné)</span></p>
<p class="src1">glClearColor(0.0f, 0.0f, 0.0f, 0.0f);<span class="kom">// Èerné pozadí</span></p>
<p class="src1">glClearDepth(1.0f);<span class="kom">// Nastaví Depth Buffer</span></p>
<p class="src1">glEnable(GL_DEPTH_TEST);<span class="kom">// Povolí hloubkové testování</span></p>
<p class="src1">glDepthFunc(GL_LEQUAL);<span class="kom">// Typ hloubkového testování</span></p>
<p class="src1">glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);<span class="kom">// Perspektiva</span></p>

<p>Spustíme timer, který bude zaji¹»ovat periodické pøekreslování scény.</p>

<p class="src1">SetTimer(1, 30, NULL);<span class="kom">// Spustí timer pro pøekreslování</span></p>
<p class="src"></p>
<p class="src1">return 0;</p>
<p class="src0">}</p>

<p>O¹etøíme zprávu èasovaèe. Volání funkce Invalidate() má za následek pøekreslení okna.</p>

<p class="src0">void COpenGL::OnTimer(UINT nIDEvent)<span class="kom">// Èasovaè</span></p>
<p class="src0">{</p>
<p class="src1">Invalidate();<span class="kom">// Pøekreslení okna</span></p>
<p class="src"></p>
<p class="src1">CWnd::OnTimer(nIDEvent);<span class="kom">// Metoda rodièovské tøídy</span></p>
<p class="src0">}</p>

<p>Funkce OnDestroy() se volá tìsnì pøed zavøením okna. Vypneme èasovaè a sma¾eme kontexty.</p>

<p class="src0">void COpenGL::OnDestroy()<span class="kom">// Deinicializace</span></p>
<p class="src0">{</p>
<p class="src1">CWnd::OnDestroy();<span class="kom">// Metoda rodièovské tøídy</span></p>
<p class="src"></p>
<p class="src1">KillTimer(1);<span class="kom">// Vypnutí èasovaèe</span></p>
<p class="src"></p>
<p class="src1">wglMakeCurrent(NULL, NULL);<span class="kom">// Neaktivní rendering kontext</span></p>
<p class="src1">wglDeleteContext(m_hglRC);<span class="kom">// Sma¾e rendering kontext</span></p>
<p class="src1">::ReleaseDC(m_hWnd, m_hgldc);<span class="kom">// Ukonèí vykreslování</span></p>
<p class="src0">}</p>

<p>OnPaint() zaji¹»uje renderování OpenGL scény. Vytvoøíme nìco ve stylu NeHe tutoriálu 4. Trojúhelník rotuje kolem osy y ètverec kolem osy x.</p>

<p class="src0">void COpenGL::OnPaint()<span class="kom">// Vykreslování</span></p>
<p class="src0">{</p>
<p class="src1">CPaintDC dc(this);<span class="kom">// Kontext zaøízení</span></p>
<p class="src"></p>
<p class="src1">glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);<span class="kom">// Vyèistí buffery</span></p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslated(-1.5, 0.0, t_hloubka);<span class="kom">// Posun doleva a do hloubky</span></p>
<p class="src1">glRotatef(t_rot, 0.0f, 1.0f, 0.0f);<span class="kom">// Rotace na ose y</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_TRIANGLES);<span class="kom">// Zaèátek kreslení trojúhelníkù</span></p>
<p class="src2">glColor3f(1.0f, 0.0f, 0.0f);<span class="kom">// Èervená barva</span></p>
<p class="src2">glVertex3f(0.0f, 1.0f, 0.0f);<span class="kom">// Horní bod</span></p>
<p class="src2">glColor3f(0.0f, 1.0f, 0.0f);<span class="kom">// Zelená barva</span></p>
<p class="src2">glVertex3f(-1.0f, -1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src2">glColor3f(0.0f, 0.0f, 1.0f);<span class="kom">// Modrá barva</span></p>
<p class="src2">glVertex3f(1.0f, -1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">glLoadIdentity();<span class="kom">// Reset matice</span></p>
<p class="src1">glTranslated(1.5, 0.0, c_hloubka);<span class="kom">// Posun o 3 jednotky doprava</span></p>
<p class="src1">glRotatef(c_rot, 1.0f, 0.0f, 0.0f);<span class="kom">// Rotace na ose x</span></p>
<p class="src"></p>
<p class="src1">glColor3f(0.5f, 0.5f, 1.0f);<span class="kom">// Svìtle modrá barva</span></p>
<p class="src"></p>
<p class="src1">glBegin(GL_QUADS);<span class="kom">// Zaèátek kreslení obdélníkù</span></p>
<p class="src2">glVertex3f(-1.0f, 1.0f, 0.0f);<span class="kom">// Levý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f, 1.0f, 0.0f);<span class="kom">// Pravý horní bod</span></p>
<p class="src2">glVertex3f( 1.0f,-1.0f, 0.0f);<span class="kom">// Pravý dolní bod</span></p>
<p class="src2">glVertex3f(-1.0f,-1.0f, 0.0f);<span class="kom">// Levý dolní bod</span></p>
<p class="src1">glEnd();</p>
<p class="src"></p>
<p class="src1">if(rotace)<span class="kom">// Je zapnutá rotace?</span></p>
<p class="src1">{</p>
<p class="src2">t_rot += 3.0f;<span class="kom">// Zvìt¹í úhel</span></p>
<p class="src2">c_rot += 2.0f;</p>
<p class="src1">}</p>

<p>Nakonec nesmíme zapomenout prohodit buffery.</p>

<p class="src1">SwapBuffers(m_hgldc);<span class="kom">// Prohodí buffery</span></p>
<p class="src0">}</p>

<p>Nyní víte, jak se vytváøí OpenGL okno a jak pracuje. Nezapomnìli jsme na nìco? Je¹tì musíme pøipojit ovládací prvky dialogu tak, aby mohly ovlivòovat vykreslovanou scénu. Po stisku tlaèítka IDC_AKTUALIZOVAT získáme obsah ovládacích prvkù a nastavíme polo¾ky objektu tøídy COpenGL. Opravdu nic slo¾itého.</p>

<p class="src0">void CDialogDlg::OnAktualizovat()<span class="kom">// Stisk tlaèítka</span></p>
<p class="src0">{</p>
<p class="src1">if(UpdateData() == 0)<span class="kom">// Nagrabuje hodnoty z ovládacích prvkù</span></p>
<p class="src2">return;</p>
<p class="src"></p>
<p class="src1">gl_okno-&gt;t_hloubka = t_hloubka;<span class="kom">// Nastaví promìnné</span></p>
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

<p>Preventivnì pøekreslíme OpenGL scénu. Kdyby nebyla aktivní rotace, timer by byl vypnutý a tudí¾ by nevolal periodické pøekreslování. Zmìny by se neprojevily.</p>

<p class="src1">gl_okno-&gt;Invalidate();<span class="kom">// Pøekreslení OpenGL okna</span></p>
<p class="src0">}</p>

<p>Pokud programujete pod MFC, nemìlo by vám napojení dialogových prvkù dìlat vìt¹í problémy. Já jsem se v zaèátcích nedostal pøes vytváøení OpenGL okna, je¹tì jednou proto dìkuji Maxi Zelenému <?VypisEmail('prog.max@seznam.cz');?>, který mi poslal ukázkový program.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('woq@email.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/dialog.rar');?> - Visual C++, MFC</li>
</ul>

<div class="okolo_img"><img src="images/clanky/dialog.jpg" width="480" height="332" alt="Dialog" /></div>

<?
include 'p_end.php';
?>
