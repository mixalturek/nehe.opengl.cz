<?
$g_title = 'CZ NeHe OpenGL - Procedur�lne generovanie ter�nu';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Procedur�lne generovanie ter�nu</h1>

<p class="nadpis_clanku">Mo�no ste u� po�uli o v��kov�ch map�ch. S� to tak� �iernobiele obr�zky, pomocou ktor�ch sa vytv�ra 3D ter�n (v��ka ter�nu na ur�itej poz�cii je ur�en� farbou zodpovedaj�ceho bodu na v��kovej mape). Najjednoduch�ie je v��kov� mapu na��ta� zo s�boru a je pokoj. S� v�ak situ�cie, ako napr. ke� rob�te grafick� demo, ktor� m� by� �o najmen�ie, ke� pr�de vhod v��kov� mapu vygenerova� procedur�lne. Tak�e si uk�eme ako na to. E�te sn�� spomeniem �e ��ta� �alej m��u aj t�, ktor� chc� vedie� ako vygenerova� takzvan� &quot;oblaky&quot; (niekedy sa tomu hovor� aj plazma), nako�ko tento tutori�l bude z�ve�kej �asti pr�ve o tom.</p>

<p>Na�a v��kov� mapa bude vyzera� pribli�ne takto:</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_generovani_terenu/map.jpg" width="256" height="256" alt="V��kov� mapa" /></div>

<p>Tak�e ako to teda funguje? Prv� vec, ktor� by ste mali pozna�, je rekurzia. Pre t�ch, ktor� nevedia �o to je, sk�sim v�kr�tkosti vysvetli�. Rekurzia je, ke� nejak� proced�ra alebo funkcia zavol� sama seba. Pr�klad:</p>

<pre>
procedure davaj(x: integer);
begin
	writeln(x);
	if x &lt; 10 then davaj(x+1);
end;
</pre>

<p>Tato proced�ra spravi to, �e vyp�u v�etky ��sla od X do 10  (tak�a davaj(3); vyp�e ��sla od 3 do 10). To if x &lt; 10 zabezpe��, �e ke� bude proced�ra zavolan� s�parametrom v���m alebo rovn�m 10, proced�ra nebude �alej vola� sama seba. Niesom si ist�, tu��m sa tomu hovor� n�vrat z�rekurzie alebo tak nejako, podstatn� je to, �e bez toho by rekurz�vna proced�ra volala sama seba donekone�na, �o by viedlo k�p�du programu (pre nedostatok pam�te). Na t�to vec treba v�dy pri p�san� rekurz�vnych proced�r myslie�, aby v�m niekedy skon�ili.</p>

<p>Tak�e to�ko vsuvka o�rekurzii, teraz spa� k�vytv�raniu v��kovej mapy. Algorytmus je tak�, �e proced�ra dostane ako parametre s�radnice obd�nika, ktor� chceme vyplni� oblakmi, rozdel� ho na 4 men�ie obd�niky a�zavol� sama seba na ka�d� z�nich. Situ�cia vyzer� takto:</p>

<pre>
[X1,Y1]       [X2,Y1]
Cl1-------1-------Cl2
  |       |       |
  |       |       |
  3-------5-------4
  |       |       |
  |       |       |
Cl3-------2-------Cl4
[X1,Y2]       [X2,Y2]
</pre>

<p>Funkcia dostane ako parametre X1,Y1,X2,Y2, �o s� s�radnice obd�nika, ktor� chceme vyplni�. Najsk�r skontroluje, �i n�hodou jeho rozmer nieje men�� ako 2x2, ak �no, ukon�� sa (to bude n�vrat z rekurzie). Potom stredu ka�dej strany (1,2,3,4) nastav� farbu, ktor� je rovn� priemeru farieb koncov�ch bodov tejto strany (tak�e farba bodu 1 bude (cl1+cl2)/2..at�) a�stredu obd�nika nastav� farbu rovn� priemeru farieb jeho rohov, teda (cl1+cl2+cl3+cl4)/4. K�tejto farbe potom pripo��ta/odpo��ta n�hodn� ��slo, ktor�ho ve�kos� bude z�visie� od ve�kosti obd�nika. No a�nakoniec zavol� 4 kr�t sama seba na v�etky �tyri obd�niky ktor� vynikli &quot;rozdelen�m&quot; p�vodn�ho obd�nika (cl1,1,5,3; 1,cl2,4,5; 3,5,2,cl3; 5,4,cl4,2). To je cel�.</p>

<p>Teraz si uk�eme ako to bude vyzera� prakticky (prakticky = ako to zap�sa� v�Delphi; t�mto sa z�rove� ospravedl�ujem v�etk�m c��karom, ale ke� niekto pou��va tak hackersk� jazyk ako C alebo nebodaj C++, ur�ite pochop� aj ten pascalovsk� k�d ;)). Najsk�r treba porie�i�, kam budeme na�u v��kov� mapu generova�. Ja som pou�il glob�lne jednorozmern� dynamick� pole, (aby som mohol jednoducho meni� ve�kos� mapy, ale ak chcete, k�udne m��ete pou�i� aj statick� pole, alebo si alokova� pam� t�mi va�imi malloc-mi a�getmem-ami), ktor� pomenujeme poeticky _.</p>

<pre>
var	_: array of byte;
	width: integer;//premenn� width bude obsahova� ��rku aj v��ku �tvorcovej mapy
</pre>

<p>Pred samotn�m aktom tvorenia proced�ry, ktor� sa bude stara� o�vygenerovanie oblakov do n�ho po�a, nap�eme si zop�r pomocn�ch proced�r:</p>

<pre>
Function Interval(Value,Min,Max: Integer): Integer;
//oseka value tak aby bola v intervale &lt;min,max&gt;
Begin
	Interval:=Value;
	If Value &lt; Min then Interval:=Min;
	If Value &gt; Max then Interval:=Max;
End;

Function Md(Cl1,Cl2: Byte): Byte;
//vrati priemer cl1 a cl2
var T: Integer;
Begin
	T:=(Cl1 + Cl2) div 2;
	Md:=T;
End;

Function FF(X1,Y1,X2,Y2: Integer): Integer;
//parametre tejto funkcie su suradnice obdlznika
//funkcia vrati nahodne cislo; cim je obdlznik
//s lavym hornym rohom [X1,Y1] a pravym dolnym
//rohom [X2,Y2] mensi, tym bude nahodne cislo mensie
Var M: Byte;
Begin
	M:=Random(2);
	if M=0 then FF:=Round(Random((ABS(X2-X1)+ABS(Y2-Y1)))*0.5)
	else FF:=-Round(Random((ABS(X2-X1)+ABS(Y2-Y1)))*0.5);
End;
</pre>

<p>Sk�r ako za�neme, mus�me si e�te ujasni� jednu vec. Zjavne ideme generova� dvojrozmern� (2D) obr�zok, a�chceme ho ma� ulo�en� v�1 rozmernom poli. Tak�e ot�zka znie, ak� je index po�a pre bod so s�radnicami [X,Y]? Nebudem v�s nap�na�, je to takto:</p>

<pre>
Pole[Y * sirka + X]
</pre>

<p>Tud� ke� chceme bodu na s�radniciach [X,Y] nastavi� farbu F, nap�eme</p>

<pre>
_[Y * Width + X]:=F;
</pre>

<p>Teraz m�me v�etko �o potrebujeme na to, aby sme mohli nap�sa� na�u rekurz�vnu funkciu, ktor� n�m vygeneruje oblaky. Nazveme si ju Divide. Tu je okomentovan� zdroj�k:</p>

<pre>
Procedure Divide(X1,Y1,X2,Y2: Integer);
// procedura vlozi do vyskovej mapy 5 bodov:
//
// 1 - stred strany [X1,Y1][X2,Y1], bude mat farbu (Cl1+Cl2) div 2
// 2 - stred strany [X1,Y2][X2,Y2], bude mat farbu (Cl3+Cl4) div 2
// 3 - stred strany [X1,Y1][X1,Y2], bude mat farbu (Cl1+Cl3) div 2
// 4 - stred strany [X2,Y1][X2,Y2], bude mat farbu (Cl2+Cl4) div 2
// 5 - stred obdlznika, bude mat farbu (1+2+3+4) div 4 + FF
//
// [X1,Y1]       [X2,Y1]
// Cl1-------1-------Cl2
//   |       |       |
//   |       |       |
//   3-------5-------4
//   |       |       |
//   |       |       |
// Cl3-------2-------Cl4
// [X1,Y2]       [X2,Y2]
//
// potom rekurzivne zavola sama seba na vsetky 4 obdlzniky
// (cl1,1,5,3; 1,cl2,4,5; 3,5,2,cl3; 5,4,cl4,2)
//
// tym vyplni oblakmi cely obdlznik, na ktory bola povodne zavolana

Var	Cl1,Cl2,Cl3,Cl4,	//farby v rohoch obdlznika
	C1,C2,C3,C4: Byte;	//farby s stredoch stran
	NX,NY: Integer;		//suradnice stredu obdlznika
Begin
	//nacitanie farieb v rohoch obdlznika
	Cl1:=_[Y1*Width+X1];
	Cl2:=_[Y1*Width+X2];
	Cl3:=_[Y2*Width+X1];
	Cl4:=_[Y2*Width+X2];

	//ak je obdlznik mensi ako 2x2, nieje co vykreslovat
	//a procedura moze skoncit, keby tu tento riadok nebol,
	//rekurzia by nikdy neskoncila
	if (ABS(X2-X1)&lt;2) and (Y2-Y1&lt;2) then Exit;

	//stred obdlznika
	NX:=Round((X1+X2)/2);
	NY:=Round((Y1+Y2)/2);

	//stredy stran
	C1:=Md(Cl1,Cl2);_[Y1*Width+NX]:=C1;
	C2:=Md(Cl3,Cl4);_[Y2*Width+NX]:=C2;
	C3:=Md(Cl1,Cl3);_[NY*Width+X1]:=C3;
	C4:=Md(Cl2,Cl4);_[NY*Width+X2]:=C4;

	//stred obdlznika (ff je nahodne cislo,
	// tym mensie cim mensi je obdlznik)
	_[NY*Width+NX]:=Interval(Md(Md(Cl1,Cl2),
		Md(Cl3,Cl4))+FF(X1,Y1,X2,Y2),0,255 );

	//rekurzia (zabezpeci aby sa vyplnik cely obdlznik)
	Divide(X1,Y1,NX,NY);
	Divide(NX,Y1,X2,NY);
	Divide(X1,NY,NX,Y2);
	Divide(NX,NY,X2,Y2);
End;
</pre>

<p>Mo�no ste si v�imli, �e  cel� oblaky z�visia od toho, akej farby s� v�rohoch v��kovej mapy. Tak�e pred samotn�m zavolan�m proced�ry Divide im nastav�me n�hodn� hodnoty:</p>

<pre>
Procedure GenClouds;
//procedura do pola _ vygeneruje oblaky - vyskovu mapu
Begin
	Randomize;

	//rohom nastavime nahodne hodnoty
	//budu mat vplyv na tvar celeho terenu
	_[0*Width+0]:=Random(64);
	_[0*Width+Width-1]:=Random(64);
	_[(Width-1)*Width+0]:=Random(64);
	_[(Width-1)*Width+Width-1]:=Random(64);

	//vykreslenie oblakov
	Divide(0,0,Width-1,Width-1);
End;
</pre>

<p>Ak sa �udujete pre�o tam je random(64) a�nie random(256), tak vedzte, �e je to tak k�li tomu, aby bol ter�n na okrajoch ni��� (to k�li kamere, ktor� bude lieta� okolo ter�nu). Preto sa pros�m nesna�te v�tom h�ada� nejak� hlb�� v�znam.</p>

<p>Pre istotu si e�te uk�eme, ako n� vysnen� ter�n vyrenderova� pomocou OpenGL. Konkr�tne si uk�eme hne� dva sp�soby. Na prv� budeme potrebova� �al�ie dve proced�ry. Tieto proced�ry pou�ijeme, ke� budeme chcie� vyr�ta� norm�lov� vektor �ubovoln�ho trojuholn�ka - to sa hod�, ke� chceme sc�nu osvetli�:</p>

<p>Definujeme si aj �trukt�ru TCoord3D, do ktorej si m��eme ulo�i� s�radnice bodu alebo vektora:</p>

<pre>
TYPE TCoord3D = RECORD
	X,Y,Z: GLfloat;
END;

procedure Normalize(var Vector: TCoord3D);
//procedura normalizuje vektor
var Len: GLfloat;
begin
	//dlzka vektora
	Len:=SQRT(SQR(Vector.X)+SQR(Vector.Y)+SQR(Vector.Z));

	if Len=0 then Exit;

	//vypocet normalizovaneho &lt;0,1&gt; vektora
	Vector.X:=Vector.X/Len;
	Vector.Y:=Vector.Y/Len;
	Vector.Z:=Vector.Z/Len;
end;

procedure CalcNormal(P1,P2,P3: TCoord3D; var Res: TCoord3D);
//procedura vypocita normalovy vektor
//trojuholnika a ulozi ho do premennej Res
var V1,V2: TCoord3D;
begin
	//vypocitanie dvoch vektorov trojuholnika
	V1.X:=P1.X - P2.X;
	V1.Y:=P1.Y - P2.Y;
	V1.Z:=P1.Z - P2.Z;

	V2.X:=P2.X - P3.X;
	V2.Y:=P2.Y - P3.Y;
	V2.Z:=P2.Z - P3.Z;

	//vektorovy sucin
	Res.X:=V1.Y*V2.Z - V1.Z*V2.Y;
	Res.Y:=V1.Z*V2.X - V1.X*V2.Z;
	Res.Z:=V1.X*V2.Y - V1.Y*V2.X;

	Normalize(Res);
end;
</pre>

<p>Ter�n budeme vykres�ova� po trojuholn�koch - ka�dej �tvorici bodov ([X,Y], [X+1,Y], [X+1,Y] a [X+1,Y+1]) na v��kovej mape bud� zodpoveda� dva trojuholn�ky. Tak�e n�m sta�� prebehn�� pole dvoma vnoren�mi cyklami. No a�takto to bude vyzera�:</p>

<pre>
function DrawGLScene(): BOOL; {vykreslenie 3D sceny}
var C,E: Integer;
    Norm: TCoord3D; // normalovy vektor
    P: Array[0..2] of TCoord3D; //suradnice vrcholov vykreslovaneho trojuholnika
begin

	glClear(GL_COLOR_BUFFER_BIT or GL_DEPTH_BUFFER_BIT);

	glLoadIdentity();
	glTranslatef(0.0,0.0,-2);
	glRotatef(--Angle,0.0,1.0,0.0);

	glBegin(GL_TRIANGLES);

	for E:=0 to Width-2 do
		for C:=0 to Width-2 do begin
			//prvy trojuholnik

			//vyratame si suradnice trojuholnika, ktory chceme vykreslit
			//suradnice Y (vysky) zavysia od hodnot vo vyskovej mape (logicky)
			//celkova vyska terenu je este zredukovana pomocou toho /Width/1.5

			//pri vypocte suradnic X a Z si vsimnite to /Width*4
			//to nam zabezpeci, ze bez ohladu na velkost vyskovej mapy
			//bude mat vyrenderovany teren sirku aj dlzku 4
			//v opengl jednotkach)

			//mimochodom shr 1 (shift right) robi bitovy posun doprava,
			//v tomto pripade o 1 bit; takze shr 1 je to iste ako div 2, ale
			//o nieco rychlejsie (procak tusim tuto operaciu priamo podporuje)

			P[0].X:=(C-(Width shr 1))/Width*4;
			P[0].Y:=-0.5+(_[(E)*Width+C]/Width/1.5);
			P[0].Z:=(E-(Width shr 1))/Width*4;

			P[1].X:=(C-(Width shr 1)+1)/Width*4;
			P[1].Y:=-0.5+(_[(E)*Width+C+1]/Width/1.5);
			P[1].Z:=(E-(Width shr 1))/Width*4;

			P[2].X:=(C-(Width shr 1)+1)/Width*4;
			P[2].Y:=-0.5+(_[(E+1)*Width+C+1]/Width/1.5);
			P[2].Z:=(E-(Width shr 1)+1)/Width*4;

			//ak nechceme mat teren vyhladeny, vyrateme si normalu trojuholnika
			//a nastavime ju

			if Not Smooth then begin
				CalcNormal(P[2],P[1],P[0], Norm);
				glNormal3f(Norm.X,Norm.Y,Norm.Z);
			end;

			//ak chceme teren vyhladeny, pre kazdy vertex nastavime normalu, ktora
			//bude vlastne normalizovany ekvivalent vektora s rovnakymi suradnicami
			//ako samotny vertex (mimochodom ked si takymto sposobom onormalujete
			//kocku a nechate ju rotovat, dostanete celkom posobivy vysledok)

			//ako bonus nastavime vertext farbu podla jeho vysky, aby sme teren
			//videli aj s vypnutym svetlom

			if Smooth then begin
				Norm:=P[0];
				Normalize(Norm);
				glNormal3f(Norm.X,Norm.Y,Norm.Z);
			end;
			glColor3f(0.3,0.5+P[0].Y,0.0);
			glVertex3f(P[0].X,P[0].Y,P[0].Z);

			if Smooth then begin
				Norm:=P[1];
				Normalize(Norm);
				glNormal3f(Norm.X,Norm.Y,Norm.Z);
			end;
			glColor3f(0.3,0.5+P[1].Y,0.0);
			glVertex3f(P[1].X,P[1].Y,P[1].Z);

			if Smooth then begin
				Norm:=P[2];
				Normalize(Norm);
				glNormal3f(Norm.X,Norm.Y,Norm.Z);
			end;
			glColor3f(0.3,0.5+P[2].Y,0.0);
			glVertex3f(P[2].X,P[2].Y,P[2].Z);

			//druhy trojuholnik
			P[0].X:=(C-(Width shr 1))/Width*4;
			P[0].Y:=-0.5+(_[(E)*Width+C]/Width/1.5);
			P[0].Z:=(E-(Width shr 1))/Width*4;

			P[1].X:=(C-(Width shr 1)+1)/Width*4;
			P[1].Y:=-0.5+(_[(E+1)*Width+C+1]/Width/1.5);
			P[1].Z:=(E-(Width shr 1)+1)/Width*4;

			P[2].X:=(C-(Width shr 1))/Width*4;
			P[2].Y:=-0.5+(_[(E+1)*Width+C]/Width/1.5);
			P[2].Z:=(E-(Width shr 1)+1)/Width*4;

			if Not Smooth then begin
				CalcNormal(P[2],P[1],P[0], Norm);
				glNormal3f(Norm.X,Norm.Y,Norm.Z);
			end;

			if Smooth then begin
				Norm:=P[0];
				Normalize(Norm);
				glNormal3f(Norm.X,Norm.Y,Norm.Z);
			end;
			glColor3f(0.3,0.5+P[0].Y,0.0);
			glVertex3f(P[0].X,P[0].Y,P[0].Z);

			if Smooth then begin
				Norm:=P[1];
				Normalize(Norm);
				glNormal3f(Norm.X,Norm.Y,Norm.Z);
			end;
			glColor3f(0.3,0.5+P[1].Y,0.0);
			glVertex3f(P[1].X,P[1].Y,P[1].Z);

			if Smooth then begin
				Norm:=P[2];
				Normalize(Norm);
				glNormal3f(Norm.X,Norm.Y,Norm.Z);
			end;
			glColor3f(0.3,0.5+P[2].Y,0.0);
			glVertex3f(P[2].X,P[2].Y,P[2].Z);
		end;

	glEnd();

	Angle:=Angle+(0.5/FPS);

	Result:=TRUE;// OK
end;
</pre>

<p>Z�koment�rov by malo by� v�etko jasn�. Sn�� len to�ko, �e treba niekde zadefinova� premenn� Smooth typu boolean, ktor� ur�uje, �i sa m� ter�n vykres�ova� vyh�aden� alebo nie.</p>

<!--
<p>Pozn. (WOQ): V�echny podm�nky &quot;if Smooth then&quot; bych spojil do jedn� a vyhodil ven mimo vno�en� cykly - v podstat� dva r�zn� zdroj�ky pro sv�tla a bez sv�tel. Kdy� si pron�sob�te 256*256*10 vyjde v�m 655360 :-(, co� je u� docela velk� ��slo, zvlṻ kdy� jde nahradit jedni�kou a jedn�m copy &amp; paste s n�kolika �pravami ;-)</p>
-->

<p class="autor">napsal: Peter Mindek <?VypisEmail('2mindo@gmail.com');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_gl_generovani_terenu.tar.gz');?> - Delphi</li>
</ul>

<div class="okolo_img"><img src="images/clanky/cl_gl_generovani_terenu/screen.jpg" width="648" height="508" alt="Screenshot programu" /></div>

<?
include 'p_end.php';
?>
