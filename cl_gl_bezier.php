<?
$g_title = 'CZ NeHe OpenGL - Bezierovy k�ivky a povrchy, v�po�et pomoc� evul�tor�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Bezierovy k�ivky a povrchy, v�po�et pomoc� evul�tor�</h1>

<p class="nadpis_clanku">V tomto tutori�lu se pokus�m osv�tlit problematiku bezeirova povrchu bez pot�eby pochopit rovnice, kter� s t�m souvisej�.</p>


<p>Existuj� dva druhy k�ivek, ale pro n�s jsou d�le�it� kubick� k�ivky vypadaj�c� n�sledovn�. K�ivka je definov�na �tz�mi body, z nich� dva ur�uj� d�lku k�ivky (P0 a P3) a dva (P1 a P2), kter� spolu s (P0 a P3) ur�uj� tvar.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_bezier/opengl_ev_2_2.png" width="400" height="300" alt="Kubick� k�ivka" /></div>

<p>Zkusme vykreslit zku�ebn� k�ivku. Nejd��ve je t�eba, nejl�pe do pole, ulo�it sou�adnice bod� P0 a� P3, sou�adnice se ukl�daj� od P0.</p>

<pre>
Lfloat ctrlPointsCurve[4][3] =
{
	{-1.0,-1.0, 0.0},	//P0
	{-1.0, 1.0, 0.0},	//P1
	{ 1.0,-1.0, 0.0},	//P2
	{ 1.0, 1.0, 0.0}	//P3
};
</pre>

<p>Pomoc� n�sleduj�c� funkce se po��taj� body, kter� se vykresl� na obrazovku. Prom�nn� ij je index bodu (tzn. P0 a� P3), funkce d�ky n�mu pozn�, podle kter� z ��d�c�ch bod� se bude po��tat. Uv obsahuje hodnoty od nuly do jedn�, p�i�em� nula ozna�uje za��tek k�ivky a jedni�ka konec k�ivky. Je d�le�it� si to pozd�ji uv�domit.</p>

<pre>
GLfloat B(GLint ij, GLfloat uv)
{
	// N�hrada slo�it�ho v�po�tu faktori�lu rozeskokem
	switch(ij)
	{
	case 0:
		return pow(1.0-uv, 3.0);
	case 1:
		return 3.0*uv*pow(1.0-uv, 2.0);
	case 2:
		return 3.0*uv*uv*(1.0-uv);
	case 3:
		return uv*uv*uv;
	default:
		return 0;
	}
}
</pre>

<p>V�po�et sou�adnic bodu a jeho vykreslen� je ve funkci drawBezierCurve(). Prom�nn� u je parametr ur�uj�c�, pro kter� bod na k�ivce se bude po��tat v�sledn� bod na obrazovce. P�edstavte si, �e m�te ��ru, kter� m� na za��tku 0 a na konci 1. Kdy� budete cht�t zobrazit tuto ��ru pomoc�, dejme tomu, 10 bod� na obrazovce, mus�te rozd�lit ��slo 1 na 10 d�l�, tak�e u bude obsahovat 0,1. Podle tohoto ��sla ur�ujete jemnost k�ivky.</p>

<p>Prom�n� p0 bude obsahovat vypo��tan� X, Y a Z sou�adnice bodu pro vykreslen�.</p>

<pre>
void drawBezierCurve(GLfloat points[4][3])
{
	int i, j, dim;
	GLfloat u;
	GLfloat p0[3];

	glBegin(GL_LINE_STRIP);

	// Pro ka�d� bod na k�ivce po��t�me bod na obrazovce
	for(u = 0.0f; u &lt; 1.0f; u += 0.01f)
	{
		// Dim = 0 je x sou�adnice, dim = 1 je y, dim = 2 je z
		for(dim = 0; dim &lt; 3; dim++)
		{
			// Inicializuje p0
			p0[dim] = 0;

			// Proch�z� v�echny ��d�c� body P0 a� P3
			for (i = 0; i &lt; 4; i++)
			{
				// Nap�. pro x sou�adnici na obrazovce projede
				// v�echny x body v matici a znich se vypo��t�
				// v�sledn� x
				p0[dim] += points[i][dim] * B(i, u);
			}
		}

		// Pot�, co se vypo�tou v�echny sou�adnice bodu pro ��ru,
		// za�ne se kreslit line strip
		glVertex3f(p0[0], p0[1], p0[2]);
	}

	glEnd();
}
</pre>

<p>Hmmm, tak si mysl�m, �e byste ted m�li pochopit, jak se vytv��ej� bezierovy k�ivky. Zkuste si pohr�t s ��d�c�mi body, a� vid�te, jak� tvary m��ete dostat. Pokud toto nech�pete, nem� cenu j�t d�l na plochy.</p>


<h3>Bezierovy plochy</h3>

<p>Vrhnem se na to, plochy se skl�daj� ze 4 kubick�ch k�ivek.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_bezier/opengl_ev_3_3.png" width="400" height="329" alt="Bezierova plocha" /></div>

<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<p class="src"></p>
<pre>

</pre>



<?
include 'p_end.php';
?>
