<?
$g_title = 'CZ NeHe OpenGL - Kamera pro 3D svìt';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Kamera pro 3D svìt</h1>

<p class="nadpis_clanku">V tomto èlánku se pokusíme implementovat snadno pou¾itelnou tøídu kamery, která bude vhodná pro pohyby v obecném 3D svìtì, napøíklad pro nìjakou støíleèku - my¹ mìní smìr natoèení a ¹ipky na klávesnici zaji¹»ují pohyb. Pøesto¾e budeme pou¾ívat malièko matematiky, nebojte se a smìle do ètení!</p>


<h3>Ortogonální a polární souøadnice</h3>

<p>Kdy¾ se vysloví spojení 'souøadnice ve 3D prostoru', vìt¹ina lidí si pravdìpodobnì pøedstaví tøi èísla oznaèující slo¾ky polohového vektoru na osách x, y a z, není to v¹ak jediná mo¾nost. Ortogonální souøadnicový systém lze bez problémù nahradit tzv. polárním systémem.</p>

<p>U polárního systému figurují také tøi souøadnice. Horizontální a vertikální úhel definují smìr natoèení a prùseèík s koulí urèí jednoznaèné souøadnice bodu. Pokud si to nedoká¾e pøedstavit, zaènìte ve 2D, kde figuruje jen jeden úhel s kru¾nicí a pak pøejdìte do 3D.</p>

<p>Napøíklad bod le¾ící na páté jednotce osy y [0,5,0] by se v polárním souøadnicovém systému vyjádøil jako horizontální úhel 90 stupòù, vertikální také 90 stupòù a polomìr pìt jednotek ([0,0,0] se pøedpokládá na ose x).</p>


<h3>Tøída kamery - ccamera.h</h3>

<p>Jak u¾ jste z úvodu asi pochopili, pro implementace kamery se budou více hodit polární souøadnice, proto¾e si pøi natáèení vystaèíme s obyèejným sèítáním dvou èísel. Pokud jste si právì polo¾ili otázku, zda je mo¾né v OpenGL pou¾ívat polární souøadnice, je vidìt, ¾e u¾ myslíte trochu dopøedu. Ne, v OpenGL je pouze ortogonální souøadnicový systém, ale to se doufejme nìjak poddá :-)</p>

<p>Ale pojïme ke kódu. Soubor cmath je C++ obdoba, céèkovského math.h a pomocí SDL_opengl.h po¾ádáme multiplatformní knihovnu SDL, na které máme vystavìnou aplikaci, aby zpøístupnila OpenGL a GLU funkce.</p>

<p>Pozn.: Tímto se omlouvám v¹em, kteøí preferují jiná API pro vytváøení okýnek (napø. glut nebo Win32 API), díky SDL bude mo¾né zkompilovat a spustit program na v¹ech bì¾nì pou¾ívaných operaèních systémech, tak¾e by si teoreticky nemìli stì¾ovat ani Woknaøi ani Linuxáci.</p>

<p>Tøída kamery je navíc na SDL nezávislá, tak¾e v pøípadì xenofobie ze SDL staèí umazat øádek se SDL_opengl.h a vlo¾it celou tøídu do vlastního aplikaèního kódu. Pokud by vás naopak SDL zaujalo, nedávno jsem se ho pokou¹el popsat v <?OdkazBlank('http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-1/', 'sérii èlánkù')?> pro <?OdkazBlank('http://www.root.cz/')?>.</p>

<pre>
#ifndef __CCAMERA_H__
#define __CCAMERA_H__

#include &lt;cmath&gt;
#include &lt;SDL_opengl.h&gt;
</pre>

<p>Dal¹í dva hlavièkové soubory souvisí s mým základním kódem pro vytváøení aplikací, kterým se sna¾ím urychlit si práci pøi zakládání nových aplikací. Basecode.h obsahuje základní nastavení a cvector.h poskytuje rozhraní k ortogonálnímu 3D vektoru.</p>

<p>Na¹e kamera nebude podporovat naklápìní (napø. letadlo pøi zatáèení), tak¾e pro pøehlednost a zkrácení definujeme standardní UP vektor smìøující ve smìru osy y.</p>

<pre>
#include &quot;basecode.h&quot;
#include &quot;cvector.h&quot;

// Standard up vector
#define UP CVector&lt;float&gt;(0.0f, 1.0f, 0.0f)


namespace basecode
{
</pre>

<p>Co se týèe èlenských promìnných tøídy, tak m_pos definuje pozici kamery a m_dir smìr jejího natoèení, v¾dy se bude jednat o jednotkový vektor. Pozici v podstatì nepotøebujeme, bez vìt¹ích problémù by mohla být externí, nicménì ji definujeme také, a» máme v¹e pohromadì.</p>

<p>Dal¹í dva atributy specifikují úhly pro polární souøadnice. Polomìr nepotøebujeme, proto¾e u kamery je dùle¾itý pouze smìr a ne pøesný bod, na který je zamìøena.</p>

<p>Speed promìnné pou¾ijeme pro specifikaci rychlosti pohybù a m_max_vert_angle ukládá maximální vertikální úhel natoèení. Bez ní, popø. slo¾itìj¹ího urèování znamínek (80 i 100 stupòù má stejný sin), by se po pøekroèení 90 stupòù vracela kamera nelogicky nazpátek. U leteckých a hlavnì vesmírných simulátorù, kde není pøesné 'dole', by se toto muselo je¹tì vyøe¹it.</p>

<pre>
class CCamera
{
protected:
	CVector&lt;float&gt; m_pos;
	CVector&lt;float&gt; m_dir;// Relative to position
	float m_horz_angle;// All angles are in degrees
	float m_vert_angle;
	float m_speed_rot;
	float m_speed;
	float m_max_vert_angle;

public:
	CCamera(const CVector&lt;float&gt;&amp; pos);
	~CCamera();
</pre>

<p>Následující metody slou¾í jako rozhraní k zapouzdøeným atributùm tøídy. V¹imnìte si pøedev¹ím funkcí GetLeft() a GetRight(), které slou¾í pro získání vektoru vlevo resp. vpravo. Vyu¾ívá se u nich tzv. vektorového souèinu dvou vektorù, jeho¾ výsledkem je vektor na nì kolmý. U kvádru s hranami A, B a C by se operací A x B získala hrana C a operací B x A opaènì orientovaná C.<p>

<pre>
	const CVector&lt;float&gt;&amp; GetPos() const { return m_pos; }
	const CVector&lt;float&gt;&amp; GetDir() const { return m_dir; }
	const CVector&lt;float&gt; GetLeft() const { return UP.Cross(m_dir); }
	const CVector&lt;float&gt; GetRight() const { return m_dir.Cross(UP); }
	const CVector&lt;float&gt; GetUp() const { return UP; }

	float GetXPos() const { return m_pos.GetX(); }
	float GetYPos() const { return m_pos.GetY(); }
	float GetZPos() const { return m_pos.GetZ(); }

	float GetXDir() const { return m_dir.GetX(); }
	float GetYDir() const { return m_dir.GetY(); }
	float GetZDir() const { return m_dir.GetZ(); }

	void SetPos(const CVector&lt;float&gt;&amp; pos) { m_pos = pos; }
	void SetXPos(float x) { m_pos.SetX(x); }
	void SetYPos(float y) { m_pos.SetY(y); }
	void SetZPos(float z) { m_pos.SetZ(z); }

	float GetHorizontalAngle() const { return m_horz_angle; }
	float GetVerticalAngle() const { return m_vert_angle; }

	void SetHorizontalAngle(float horz_angle);
	void SetVerticalAngle(float vert_angle);
</pre>

<p>Go*() funkce slou¾í pro zmìnu polohy kamery, volají se pøi stisku ¹ipek na klávesnici. Fungují velice jednodu¹e - po¾adovaný smìr vynásobený rychlostí a pøevrácenou hodnotou fps se pøiète k aktuální pozici.</p>

<pre>
	void GoFront(float fps) { m_pos += (m_dir*m_speed / fps); }
	void GoBack(float fps) { m_pos -= (m_dir*m_speed / fps); }
	void GoLeft(float fps) { m_pos += (UP.Cross(m_dir)*m_speed / fps); }
	void GoRight(float fps) { m_pos += (m_dir.Cross(UP)*m_speed / fps); }
</pre>

<p>Metoda Rotate() mìní úhly natoèení kamery. Aby bylo rozhraní co nejjednodu¹¹í, pøedávají se jí relativní pohyby my¹i získané z okenního mana¾eru. LookAt() se bude pou¾ívat pøi renderingu scény, v podstatì pouze volá nad m_pos, m_dir a UP vektorem standardní funkci gluLookAt().</p>

<pre>
	void Rotate(int xrel, int yrel, float fps);
	void LookAt() const;// gluLookAt()
</pre>

<p>Poslední dvì metody slou¾í pro zji¹tìní, zda kamera opustila obdélník herního høi¹tì, respektive k jejímu pøesunu na jeho nejbli¾¹í okraj.</p>

<pre>
	// When the area (playground etc.) has borders
	bool IsInQuad(int x_half, int z_half);
	void PosToQuad(int x_half, int z_half);
};

} // namespace

#endif
</pre>


<h3>Implementace kamery - ccamera.cpp</h3>

<p>No, v podstatì nám toho doprogramovat u¾ moc nezbylo, vìt¹ina metod je v hlavièce tøídy. V konstruktoru nastavíme v¹echny atributy na výchozí hodnoty a destruktor necháme prázdný.</p>

<pre>
#include &quot;ccamera.h&quot;

namespace basecode
{

CCamera::CCamera(const CVector&lt;float&gt;&amp; pos) :
		m_pos(pos),
		m_dir(0.0f, 0.0f, -1.0f),
		m_horz_angle(-90.0f),
		m_vert_angle(0.0f),
		m_speed_rot(2.0f),
		m_speed(10.0f),
		m_max_vert_angle(90.0f)
{

}

CCamera::~CCamera()
{

}
</pre>

<p>Následující dvì metody slou¾í k nastavení úhlù natoèení kamery. Proto¾e by smìrový vektor pøestal být validní, nestaèí pouhé pøiøazení do promìnných, ale m_dir se musí je¹tì aktualizovat. Rotate() je v podstatì to samé.</p>

<pre>
void CCamera::SetHorizontalAngle(float horz_angle)
{
	m_horz_angle = horz_angle;

	m_dir.SetX(cos(DEGTORAD(m_horz_angle)));
	m_dir.SetZ(sin(DEGTORAD(m_horz_angle)));
}

void CCamera::SetVerticalAngle(float vert_angle)
{
	if(m_vert_angle &gt; -m_max_vert_angle
	&amp;&amp; m_vert_angle &lt; m_max_vert_angle)
	{
		m_vert_angle = vert_angle;
		m_dir.SetY(-sin(DEGTORAD(m_vert_angle)));
	}
}

void CCamera::Rotate(int xrel, int yrel, float fps)
{
	m_horz_angle += xrel*m_speed_rot / fps;

	m_dir.SetX(cos(DEGTORAD(m_horz_angle)));
	m_dir.SetZ(sin(DEGTORAD(m_horz_angle)));

	if((m_vert_angle &lt;  m_max_vert_angle &amp;&amp; yrel &gt; 0)
	|| (m_vert_angle &gt; -m_max_vert_angle &amp;&amp; yrel &lt; 0))
	{
		m_vert_angle += yrel*m_speed_rot / fps;

		m_dir.SetY(-sin(DEGTORAD(m_vert_angle)));
	}
}
</pre>

<p>Funkce LookAt() obsahuje pouze volání standardního gluLookAt() z knihovny GLU, která nahrazuje OpenGL glTranslatef() a glRotatef() jedním, o nìco snadnìji pou¾itelným, pøíkazem. V prvních tøech parametrech se jí pøedává aktuální pozice kamery, v dal¹ích tøech smìr a v posledních tøech UP vektor.</p>

<pre>
void CCamera::LookAt() const
{
	gluLookAt(	m_pos.GetX(),
			m_pos.GetY(),
			m_pos.GetZ(),
			m_pos.GetX()+m_dir.GetX(),
			m_pos.GetY()+m_dir.GetY(),
			m_pos.GetZ()+m_dir.GetZ(),
			0.0, 1.0, 0.0);
}
</pre>

<p>Poslední dvì metody o¹etøují stav, kdy se kamera ocitne mimo obdélníkovou plochu hracího høi¹tì. Tím jsme si pro¹li celý kód tøídy kamery a teï se mù¾eme koneènì vrhnout na samotnou aplikaci.</p>

<pre>
bool CCamera::IsInQuad(int x_half, int z_half)
{
	if(m_pos.GetX() &lt; -x_half)
		return false;
	if(m_pos.GetX() &gt; x_half)
		return false;

	if(m_pos.GetZ() &lt; -z_half)
		return false;
	if(m_pos.GetZ() &gt; z_half)
		return false;

	return true;
}

void CCamera::PosToQuad(int x_half, int z_half)
{
	if(m_pos.GetX() &lt; -x_half)
		m_pos.SetX(-x_half);
	if(m_pos.GetX() &gt; x_half)
		m_pos.SetX(x_half);

	if(m_pos.GetZ() &lt; -z_half)
		m_pos.SetZ(-z_half);
	if(m_pos.GetZ() &gt; z_half)
		m_pos.SetZ(z_half);
}

} // namespace
</pre>


<h3>Tøída aplikace - ccameraapp.h</h3>

<p>Aby nezùstalo jen u tøídy kamery, uká¾eme si je¹tì její pou¾ití v aplikaci. Opìt nejprve inkludujeme hlavièkové soubory. Díky vectoru budeme moci pracovat se ¹ablonou dynamického pole ze standardní knihovny ¹ablon jazyka C++ (STL). CApplication je rodièovská tøída na¹í aplikace, CGrid poskytuje funkce pro vykreslování jednoduchý drátìných modelù, o kameøe je tento èlánek a CVector u¾ byl také popsán.</p>

<pre>
#ifndef __CCAMERAAPP_H__
#define __CCAMERAAPP_H__

#include &lt;vector&gt;
#include &quot;basecode.h&quot;
#include &quot;capplication.h&quot;
#include &quot;cgrid.h&quot;
#include &quot;ccamera.h&quot;
#include &quot;cvector.h&quot;

#define SIZE 64.0f

using namespace std;

namespace basecode
{
</pre>

<p>Co se týká metod tøídy, ze jmen by mìlo být jasné, co má která na starost. Je¹tì se k nim vrátíme u implementace.</p>

<pre>
class CCameraApp : public CApplication
{
public:
	CCameraApp(int argc, char *argv[]);
	virtual ~CCameraApp();
	virtual void Init(const string&amp; win_title);

protected:
	virtual void InitGL();
	virtual void OnInit();
	virtual void Draw();
	virtual void Update();
	virtual bool ProcessEvent(SDL_Event&amp; event);
</pre>

<p>Atribut m_cam je objekt kamery a dal¹í dvì promìnné budou udr¾ovat pozice a smìry støel vypou¹tìných stiskem tlaèítka my¹i.</p>

<pre>
private:
	CCamera m_cam;
	vector&lt;CVector&lt;float&gt; &gt; m_bullets_pos;
	vector&lt;CVector&lt;float&gt; &gt; m_bullets_dir;
};

}

#endif
</pre>


<h3>Implementace tøídy aplikace - ccameraapp.cpp</h3>

<p>V konstruktoru tøídy zavoláme pomocí inicializátorù konstruktor pøedka a nastavíme výchozí pozici kamery. V tìle metody rezervujeme pamì» pro sto støel a destruktor zùstává prázdný.</p>

<pre>
#include &quot;ccameraapp.h&quot;

namespace basecode
{

CCameraApp::CCameraApp(int argc, char *argv[]) :
		CApplication(argc, argv),
		m_cam(CVector&lt;float&gt;(0.0f, 0.0f, 0.0f))
{
	m_bullets_pos.reserve(100);
	m_bullets_dir.reserve(100);
}

CCameraApp::~CCameraApp()
{

}
</pre>

<p>V metodì Init() skryjeme kurzor my¹i a v InitGL() nastavíme v¹echny po¾adované vlastnosti OpenGL.</p>

<pre>
void CCameraApp::Init(const string&amp; win_title)
{
	CApplication::Init(win_title);
	SDL_ShowCursor(SDL_DISABLE);
}

void CCameraApp::InitGL()
{
	glClearColor(0.0, 0.0, 0.0, 0.0);
	glClearDepth(1.0);
	glDepthFunc(GL_LEQUAL);
	glEnable(GL_DEPTH_TEST);
	glShadeModel(GL_SMOOTH);
	glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);

	glEnable(GL_LINE_SMOOTH);
	glLineWidth(3.0f);

	glEnable(GL_BLEND);
	glBlendFunc(GL_SRC_ALPHA, GL_ONE_MINUS_SRC_ALPHA);
}

void CCameraApp::OnInit()
{

}
</pre>

<p>Ve funkci slou¾ící pro rendering scény hned po resetu matice zavoláme LookAt() kamery, která nastaví v¹echny potøebné translace a rotace. Poté vykreslíme pomocí tøídy CGrid rovinu rovnobì¾nou s osami x a z a následnì pomocí stejné tøídy souøadnicové osy. Tím máme vytvoøen demonstraèní 3D svìt.</p>

<pre>
void CCameraApp::Draw()
{
	glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);
	glLoadIdentity();

	m_cam.LookAt();

	glColor4ub(30, 200, 30, 255);
	CGrid::DrawPlaneXZ(SIZE, SIZE/4.0f, -0.2f);
	glColor4ub(255, 0, 0, 255);
	CGrid::DrawAxis(SIZE / 2.0f);
</pre>

<p>Dále vykreslíme v¹echny støely. Pøedem se omlouvám za jejich vzhled :-(, ale i kdy¾ se jedná o nepøiklápìjící se modré ètvereèky, dostateènì demonstrují pou¾ití.</p>

<pre>
	glDisable(GL_BLEND);
	glBegin(GL_QUADS);
	glColor3ub(0, 0, 255);

	vector&lt;CVector&lt;float&gt; &gt;::iterator it;

	for(it = m_bullets_pos.begin(); it != m_bullets_pos.end(); it++)
	{
		glVertex3fv(*it + CVector&lt;float&gt;(-1,-1, 0));
		glVertex3fv(*it + CVector&lt;float&gt;( 1,-1, 0));
		glVertex3fv(*it + CVector&lt;float&gt;( 1, 1, 0));
		glVertex3fv(*it + CVector&lt;float&gt;(-1, 1, 0));
	}

	glEnd();
	glEnable(GL_BLEND);
}
</pre>

<p>V aktualizaèní funkci testujeme stisk ¹ipek a kláves W, S, A a D, které umo¾òují chození po scénì. Y-ovou pozici kamery v¾dy nastavíme na pìt jednotek nad povrchem, aby nemohla &quot;uletìt&quot; z plochy. Na konci funkce pøesuneme v¹echny støely na novou pozici.</p>

<pre>
void CCameraApp::Update()
{
	SDL_PumpEvents();

	Uint8* keys;
	keys = SDL_GetKeyState(NULL);

	if(keys[SDLK_UP] == SDL_PRESSED || keys[SDLK_w] == SDL_PRESSED)
		m_cam.GoFront(GetFPS());
	if(keys[SDLK_DOWN] == SDL_PRESSED || keys[SDLK_s] == SDL_PRESSED)
		m_cam.GoBack(GetFPS());
	if(keys[SDLK_LEFT] == SDL_PRESSED || keys[SDLK_a] == SDL_PRESSED)
		m_cam.GoLeft(GetFPS());
	if(keys[SDLK_RIGHT] == SDL_PRESSED || keys[SDLK_d] == SDL_PRESSED)
		m_cam.GoRight(GetFPS());

	m_cam.SetYPos(5.0f);


	vector&lt;CVector&lt;float&gt; &gt;::iterator it_pos;
	vector&lt;CVector&lt;float&gt; &gt;::iterator it_dir;

	for(it_pos = m_bullets_pos.begin(), it_dir = m_bullets_dir.begin();
	    it_pos != m_bullets_pos.end() || it_dir != m_bullets_dir.end();
	    it_pos++, it_dir++)
	{
		*it_pos += *it_dir / GetFPS();
	}
}
</pre>

<p>Do¹li jsme a¾ ke zpracování událostí. Pøi pohybu my¹í by teoreticky mìlo staèit zavolat funkci Rotate() kamery, ale bohu¾el to není tak jednoduché. Aby my¹ nemohla opustit okno, po ka¾dém posunutí pøesuneme kurzor zpìt doprostøed okna. Problémem je, ¾e funkce SDL_WarpMouse() sama o sobì generuje událost SDL_MOUSEMOTION, tak¾e by do¹lo k zacyklení (zpracování události generuje novou), o¹etøíme to podmínkou na zaèátku. Dále budeme ignorovat nìkolik prvních událostí.</p>

<p>Také byste si mohli v¹imnou pøedávání relativních posunù my¹i funkci Rotate(). Toto je specialita SDL, kterou napøíklad Win32 API neposkytuje - pøedává pouze absolutní pozici v oknì. Muselo by se to øe¹it pomocí dal¹ích dvou promìnných obsahujících pozici z minula.</p>

<pre>
bool CCameraApp::ProcessEvent(SDL_Event&amp; event)
{
	switch(event.type)
	{
	case SDL_MOUSEMOTION:
		// SDL_WarpMouse() generates SDL_MOUSEMOTION event :-(
		if(event.motion.x != GetWinWidth() >> 1
		|| event.motion.y != GetWinHeight() >> 1)
		{
			// First several messages MUST be ignored
			static int kriza = 0;
			if(kriza++ &lt; 5)
				break;

			m_cam.Rotate(event.motion.xrel,
					event.motion.yrel, GetFPS());

			// Center mouse in window
			SDL_WarpMouse(GetWinWidth()&gt;&gt;1, GetWinHeight()&gt;&gt;1);
		}
		break;
</pre>

<p>Stisk libovolného tlaèítka my¹i zpùsobí vypu¹tìní støely, pøidává se pouze jako nová polo¾ka na konec dynamického pole. Uvolnìní se provede najednou a¾ pøi ukonèení aplikace, nic explicitního není pro jednoduchost implementováno.</p>

<pre>
	case SDL_MOUSEBUTTONDOWN:
		m_bullets_pos.push_back(CVector&lt;float&gt;(m_cam.GetPos()));
		m_bullets_dir.push_back(CVector&lt;float&gt;(m_cam.GetDir()*20.0f));
		break;


	default:// Other events
		return CApplication::ProcessEvent(event);
		break;
	}

	return true;
}

} // namespace
</pre>

<p>A to je z tohoto èlánku v¹e...</p>


<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_gl_camera_3d_makefile.tar.gz');?> - C++ s Makefile</li>
<li><?OdkazDown('download/clanky/cl_gl_camera_3d_devcpp.tar.gz');?> - Dev-C++</li>
</ul>

<p class="autor">Michal Turek <?VypisEmail('WOQ@seznam.cz');?>, 23.09.2005</p>


<div class="okolo_img"><img src="images/clanky/cl_gl_kamera_3d.png" width="642" height="506" alt="Screenshot ukázkové aplikace" /></div>


<?
include 'p_end.php';
?>
