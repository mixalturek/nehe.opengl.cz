<?
$g_title = 'CZ NeHe OpenGL - Bezierovy køivky a povrchy, výpoèet pomocí evulátorù';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Bezierovy køivky a povrchy, výpoèet pomocí evulátorù</h1>

<p class="nadpis_clanku">V tomto tutoriálu se pokusím osvìtlit problematiku bezeirova povrchu bez potøeby pochopit rovnice, které s tím souvisejí.</p>


<p>Existují dva druhy køivek, ale pro nás jsou dùle¾ité kubické køivky vypadající následovnì. Køivka je definována ètzømi body, z nich¾ dva urèují délku køivky (P0 a P3) a dva (P1 a P2), které spolu s (P0 a P3) urèují tvar.</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_bezier/opengl_ev_2_2.png" width="400" height="300" alt="Kubická køivka" /></div>

<p>Zkusme vykreslit zku¹ební køivku. Nejdøíve je tøeba, nejlépe do pole, ulo¾it souøadnice bodù P0 a¾ P3, souøadnice se ukládají od P0.</p>

<pre>
Lfloat ctrlPointsCurve[4][3] =
{
	{-1.0,-1.0, 0.0},	//P0
	{-1.0, 1.0, 0.0},	//P1
	{ 1.0,-1.0, 0.0},	//P2
	{ 1.0, 1.0, 0.0}	//P3
};
</pre>

<p>Pomocí následující funkce se poèítají body, které se vykreslí na obrazovku. Promìnná ij je index bodu (tzn. P0 a¾ P3), funkce díky nìmu pozná, podle které z øídících bodù se bude poèítat. Uv obsahuje hodnoty od nuly do jedné, pøièem¾ nula oznaèuje zaèátek køivky a jednièka konec køivky. Je dùle¾ité si to pozdìji uvìdomit.</p>

<pre>
GLfloat B(GLint ij, GLfloat uv)
{
	// Náhrada slo¾itého výpoètu faktoriálu rozeskokem
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

<p>Výpoèet souøadnic bodu a jeho vykreslení je ve funkci drawBezierCurve(). Promìnná u je parametr urèující, pro který bod na køivce se bude poèítat výsledný bod na obrazovce. Pøedstavte si, ¾e máte èáru, která má na zaèátku 0 a na konci 1. Kdy¾ budete chtít zobrazit tuto èáru pomocí, dejme tomu, 10 bodù na obrazovce, musíte rozdìlit èíslo 1 na 10 dílù, tak¾e u bude obsahovat 0,1. Podle tohoto èísla urèujete jemnost køivky.</p>

<p>Promìná p0 bude obsahovat vypoèítané X, Y a Z souøadnice bodu pro vykreslení.</p>

<pre>
void drawBezierCurve(GLfloat points[4][3])
{
	int i, j, dim;
	GLfloat u;
	GLfloat p0[3];

	glBegin(GL_LINE_STRIP);

	// Pro ka¾dý bod na køivce poèítáme bod na obrazovce
	for(u = 0.0f; u &lt; 1.0f; u += 0.01f)
	{
		// Dim = 0 je x souøadnice, dim = 1 je y, dim = 2 je z
		for(dim = 0; dim &lt; 3; dim++)
		{
			// Inicializuje p0
			p0[dim] = 0;

			// Prochází v¹echny øídící body P0 a¾ P3
			for (i = 0; i &lt; 4; i++)
			{
				// Napø. pro x souøadnici na obrazovce projede
				// v¹echny x body v matici a znich se vypoèítá
				// výsledná x
				p0[dim] += points[i][dim] * B(i, u);
			}
		}

		// Poté, co se vypoètou v¹echny souøadnice bodu pro èáru,
		// zaène se kreslit line strip
		glVertex3f(p0[0], p0[1], p0[2]);
	}

	glEnd();
}
</pre>

<p>Hmmm, tak si myslím, ¾e byste ted mìli pochopit, jak se vytváøejí bezierovy køivky. Zkuste si pohrát s øídícími body, a» vidíte, jaké tvary mù¾ete dostat. Pokud toto nechápete, nemá cenu jít dál na plochy.</p>


<h3>Bezierovy plochy</h3>

<p>Vrhnem se na to, plochy se skládají ze 4 kubických køivek.</p>

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
