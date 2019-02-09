<?
$g_title = 'CZ NeHe OpenGL - Kamera pro 3D sv�t';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Kamera pro 3D sv�t</h1>

<p class="nadpis_clanku">V tomto �l�nku se pokus�me implementovat snadno pou�itelnou t��du kamery, kter� bude vhodn� pro pohyby v obecn�m 3D sv�t�, nap��klad pro n�jakou st��le�ku - my� m�n� sm�r nato�en� a �ipky na kl�vesnici zaji��uj� pohyb. P�esto�e budeme pou��vat mali�ko matematiky, nebojte se a sm�le do �ten�!</p>


<h3>Ortogon�ln� a pol�rn� sou�adnice</h3>

<p>Kdy� se vyslov� spojen� 'sou�adnice ve 3D prostoru', v�t�ina lid� si pravd�podobn� p�edstav� t�i ��sla ozna�uj�c� slo�ky polohov�ho vektoru na os�ch x, y a z, nen� to v�ak jedin� mo�nost. Ortogon�ln� sou�adnicov� syst�m lze bez probl�m� nahradit tzv. pol�rn�m syst�mem.</p>

<p>U pol�rn�ho syst�mu figuruj� tak� t�i sou�adnice. Horizont�ln� a vertik�ln� �hel definuj� sm�r nato�en� a pr�se��k s koul� ur�� jednozna�n� sou�adnice bodu. Pokud si to nedok�e p�edstavit, za�n�te ve 2D, kde figuruje jen jeden �hel s kru�nic� a pak p�ejd�te do 3D.</p>

<p>Nap��klad bod le��c� na p�t� jednotce osy y [0,5,0] by se v pol�rn�m sou�adnicov�m syst�mu vyj�d�il jako horizont�ln� �hel 90 stup��, vertik�ln� tak� 90 stup�� a polom�r p�t jednotek ([0,0,0] se p�edpokl�d� na ose x).</p>


<h3>T��da kamery - ccamera.h</h3>

<p>Jak u� jste z �vodu asi pochopili, pro implementace kamery se budou v�ce hodit pol�rn� sou�adnice, proto�e si p�i nat��en� vysta��me s oby�ejn�m s��t�n�m dvou ��sel. Pokud jste si pr�v� polo�ili ot�zku, zda je mo�n� v OpenGL pou��vat pol�rn� sou�adnice, je vid�t, �e u� mysl�te trochu dop�edu. Ne, v OpenGL je pouze ortogon�ln� sou�adnicov� syst�m, ale to se doufejme n�jak podd� :-)</p>

<p>Ale poj�me ke k�du. Soubor cmath je C++ obdoba, c��kovsk�ho math.h a pomoc� SDL_opengl.h po��d�me multiplatformn� knihovnu SDL, na kter� m�me vystav�nou aplikaci, aby zp��stupnila OpenGL a GLU funkce.</p>

<p>Pozn.: T�mto se omlouv�m v�em, kte�� preferuj� jin� API pro vytv��en� ok�nek (nap�. glut nebo Win32 API), d�ky SDL bude mo�n� zkompilovat a spustit program na v�ech b�n� pou��van�ch opera�n�ch syst�mech, tak�e by si teoreticky nem�li st�ovat ani Wokna�i ani Linux�ci.</p>

<p>T��da kamery je nav�c na SDL nez�visl�, tak�e v p��pad� xenofobie ze SDL sta�� umazat ��dek se SDL_opengl.h a vlo�it celou t��du do vlastn�ho aplika�n�ho k�du. Pokud by v�s naopak SDL zaujalo, ned�vno jsem se ho pokou�el popsat v <?OdkazBlank('http://www.root.cz/clanky/sdl-hry-nejen-pro-linux-1/', 's�rii �l�nk�')?> pro <?OdkazBlank('http://www.root.cz/')?>.</p>

<pre>
#ifndef __CCAMERA_H__
#define __CCAMERA_H__

#include &lt;cmath&gt;
#include &lt;SDL_opengl.h&gt;
</pre>

<p>Dal�� dva hlavi�kov� soubory souvis� s m�m z�kladn�m k�dem pro vytv��en� aplikac�, kter�m se sna��m urychlit si pr�ci p�i zakl�d�n� nov�ch aplikac�. Basecode.h obsahuje z�kladn� nastaven� a cvector.h poskytuje rozhran� k ortogon�ln�mu 3D vektoru.</p>

<p>Na�e kamera nebude podporovat nakl�p�n� (nap�. letadlo p�i zat��en�), tak�e pro p�ehlednost a zkr�cen� definujeme standardn� UP vektor sm��uj�c� ve sm�ru osy y.</p>

<pre>
#include &quot;basecode.h&quot;
#include &quot;cvector.h&quot;

// Standard up vector
#define UP CVector&lt;float&gt;(0.0f, 1.0f, 0.0f)


namespace basecode
{
</pre>

<p>Co se t��e �lensk�ch prom�nn�ch t��dy, tak m_pos definuje pozici kamery a m_dir sm�r jej�ho nato�en�, v�dy se bude jednat o jednotkov� vektor. Pozici v podstat� nepot�ebujeme, bez v�t��ch probl�m� by mohla b�t extern�, nicm�n� ji definujeme tak�, a� m�me v�e pohromad�.</p>

<p>Dal�� dva atributy specifikuj� �hly pro pol�rn� sou�adnice. Polom�r nepot�ebujeme, proto�e u kamery je d�le�it� pouze sm�r a ne p�esn� bod, na kter� je zam��ena.</p>

<p>Speed prom�nn� pou�ijeme pro specifikaci rychlosti pohyb� a m_max_vert_angle ukl�d� maxim�ln� vertik�ln� �hel nato�en�. Bez n�, pop�. slo�it�j��ho ur�ov�n� znam�nek (80 i 100 stup�� m� stejn� sin), by se po p�ekro�en� 90 stup�� vracela kamera nelogicky nazp�tek. U leteck�ch a hlavn� vesm�rn�ch simul�tor�, kde nen� p�esn� 'dole', by se toto muselo je�t� vy�e�it.</p>

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

<p>N�sleduj�c� metody slou�� jako rozhran� k zapouzd�en�m atribut�m t��dy. V�imn�te si p�edev��m funkc� GetLeft() a GetRight(), kter� slou�� pro z�sk�n� vektoru vlevo resp. vpravo. Vyu��v� se u nich tzv. vektorov�ho sou�inu dvou vektor�, jeho� v�sledkem je vektor na n� kolm�. U kv�dru s hranami A, B a C by se operac� A x B z�skala hrana C a operac� B x A opa�n� orientovan� C.<p>

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

<p>Go*() funkce slou�� pro zm�nu polohy kamery, volaj� se p�i stisku �ipek na kl�vesnici. Funguj� velice jednodu�e - po�adovan� sm�r vyn�soben� rychlost� a p�evr�cenou hodnotou fps se p�i�te k aktu�ln� pozici.</p>

<pre>
	void GoFront(float fps) { m_pos += (m_dir*m_speed / fps); }
	void GoBack(float fps) { m_pos -= (m_dir*m_speed / fps); }
	void GoLeft(float fps) { m_pos += (UP.Cross(m_dir)*m_speed / fps); }
	void GoRight(float fps) { m_pos += (m_dir.Cross(UP)*m_speed / fps); }
</pre>

<p>Metoda Rotate() m�n� �hly nato�en� kamery. Aby bylo rozhran� co nejjednodu���, p�ed�vaj� se j� relativn� pohyby my�i z�skan� z okenn�ho mana�eru. LookAt() se bude pou��vat p�i renderingu sc�ny, v podstat� pouze vol� nad m_pos, m_dir a UP vektorem standardn� funkci gluLookAt().</p>

<pre>
	void Rotate(int xrel, int yrel, float fps);
	void LookAt() const;// gluLookAt()
</pre>

<p>Posledn� dv� metody slou�� pro zji�t�n�, zda kamera opustila obd�ln�k hern�ho h�i�t�, respektive k jej�mu p�esunu na jeho nejbli��� okraj.</p>

<pre>
	// When the area (playground etc.) has borders
	bool IsInQuad(int x_half, int z_half);
	void PosToQuad(int x_half, int z_half);
};

} // namespace

#endif
</pre>


<h3>Implementace kamery - ccamera.cpp</h3>

<p>No, v podstat� n�m toho doprogramovat u� moc nezbylo, v�t�ina metod je v hlavi�ce t��dy. V konstruktoru nastav�me v�echny atributy na v�choz� hodnoty a destruktor nech�me pr�zdn�.</p>

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

<p>N�sleduj�c� dv� metody slou�� k nastaven� �hl� nato�en� kamery. Proto�e by sm�rov� vektor p�estal b�t validn�, nesta�� pouh� p�i�azen� do prom�nn�ch, ale m_dir se mus� je�t� aktualizovat. Rotate() je v podstat� to sam�.</p>

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

<p>Funkce LookAt() obsahuje pouze vol�n� standardn�ho gluLookAt() z knihovny GLU, kter� nahrazuje OpenGL glTranslatef() a glRotatef() jedn�m, o n�co snadn�ji pou�iteln�m, p��kazem. V prvn�ch t�ech parametrech se j� p�ed�v� aktu�ln� pozice kamery, v dal��ch t�ech sm�r a v posledn�ch t�ech UP vektor.</p>

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

<p>Posledn� dv� metody o�et�uj� stav, kdy se kamera ocitne mimo obd�ln�kovou plochu hrac�ho h�i�t�. T�m jsme si pro�li cel� k�d t��dy kamery a te� se m��eme kone�n� vrhnout na samotnou aplikaci.</p>

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


<h3>T��da aplikace - ccameraapp.h</h3>

<p>Aby nez�stalo jen u t��dy kamery, uk�eme si je�t� jej� pou�it� v aplikaci. Op�t nejprve inkludujeme hlavi�kov� soubory. D�ky vectoru budeme moci pracovat se �ablonou dynamick�ho pole ze standardn� knihovny �ablon jazyka C++ (STL). CApplication je rodi�ovsk� t��da na�� aplikace, CGrid poskytuje funkce pro vykreslov�n� jednoduch� dr�t�n�ch model�, o kame�e je tento �l�nek a CVector u� byl tak� pops�n.</p>

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

<p>Co se t�k� metod t��dy, ze jmen by m�lo b�t jasn�, co m� kter� na starost. Je�t� se k nim vr�t�me u implementace.</p>

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

<p>Atribut m_cam je objekt kamery a dal�� dv� prom�nn� budou udr�ovat pozice a sm�ry st�el vypou�t�n�ch stiskem tla��tka my�i.</p>

<pre>
private:
	CCamera m_cam;
	vector&lt;CVector&lt;float&gt; &gt; m_bullets_pos;
	vector&lt;CVector&lt;float&gt; &gt; m_bullets_dir;
};

}

#endif
</pre>


<h3>Implementace t��dy aplikace - ccameraapp.cpp</h3>

<p>V konstruktoru t��dy zavol�me pomoc� inicializ�tor� konstruktor p�edka a nastav�me v�choz� pozici kamery. V t�le metody rezervujeme pam� pro sto st�el a destruktor z�st�v� pr�zdn�.</p>

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

<p>V metod� Init() skryjeme kurzor my�i a v InitGL() nastav�me v�echny po�adovan� vlastnosti OpenGL.</p>

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

<p>Ve funkci slou��c� pro rendering sc�ny hned po resetu matice zavol�me LookAt() kamery, kter� nastav� v�echny pot�ebn� translace a rotace. Pot� vykresl�me pomoc� t��dy CGrid rovinu rovnob�nou s osami x a z a n�sledn� pomoc� stejn� t��dy sou�adnicov� osy. T�m m�me vytvo�en demonstra�n� 3D sv�t.</p>

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

<p>D�le vykresl�me v�echny st�ely. P�edem se omlouv�m za jejich vzhled :-(, ale i kdy� se jedn� o nep�ikl�p�j�c� se modr� �tvere�ky, dostate�n� demonstruj� pou�it�.</p>

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

<p>V aktualiza�n� funkci testujeme stisk �ipek a kl�ves W, S, A a D, kter� umo��uj� chozen� po sc�n�. Y-ovou pozici kamery v�dy nastav�me na p�t jednotek nad povrchem, aby nemohla &quot;ulet�t&quot; z plochy. Na konci funkce p�esuneme v�echny st�ely na novou pozici.</p>

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

<p>Do�li jsme a� ke zpracov�n� ud�lost�. P�i pohybu my�� by teoreticky m�lo sta�it zavolat funkci Rotate() kamery, ale bohu�el to nen� tak jednoduch�. Aby my� nemohla opustit okno, po ka�d�m posunut� p�esuneme kurzor zp�t doprost�ed okna. Probl�mem je, �e funkce SDL_WarpMouse() sama o sob� generuje ud�lost SDL_MOUSEMOTION, tak�e by do�lo k zacyklen� (zpracov�n� ud�losti generuje novou), o�et��me to podm�nkou na za��tku. D�le budeme ignorovat n�kolik prvn�ch ud�lost�.</p>

<p>Tak� byste si mohli v�imnou p�ed�v�n� relativn�ch posun� my�i funkci Rotate(). Toto je specialita SDL, kterou nap��klad Win32 API neposkytuje - p�ed�v� pouze absolutn� pozici v okn�. Muselo by se to �e�it pomoc� dal��ch dvou prom�nn�ch obsahuj�c�ch pozici z minula.</p>

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

<p>Stisk libovoln�ho tla��tka my�i zp�sob� vypu�t�n� st�ely, p�id�v� se pouze jako nov� polo�ka na konec dynamick�ho pole. Uvoln�n� se provede najednou a� p�i ukon�en� aplikace, nic explicitn�ho nen� pro jednoduchost implementov�no.</p>

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

<p>A to je z tohoto �l�nku v�e...</p>


<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_gl_camera_3d_makefile.tar.gz');?> - C++ s Makefile</li>
<li><?OdkazDown('download/clanky/cl_gl_camera_3d_devcpp.tar.gz');?> - Dev-C++</li>
</ul>

<p class="autor">Michal Turek <?VypisEmail('WOQ@seznam.cz');?>, 23.09.2005</p>


<div class="okolo_img"><img src="images/clanky/cl_gl_kamera_3d.png" width="642" height="506" alt="Screenshot uk�zkov� aplikace" /></div>


<?
include 'p_end.php';
?>
