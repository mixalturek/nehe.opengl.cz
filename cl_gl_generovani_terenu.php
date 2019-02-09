<?
$g_title = 'CZ NeHe OpenGL - Procedurálne generovanie terénu';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>Procedurálne generovanie terénu</h1>

<p class="nadpis_clanku">Mo¾no ste u¾ poèuli o vý¹kových mapách. Sú to také èiernobiele obrázky, pomocou ktorých sa vytvára 3D terén (vý¹ka terénu na urèitej pozícii je urèená farbou zodpovedajúceho bodu na vý¹kovej mape). Najjednoduch¹ie je vý¹kovú mapu naèíta» zo súboru a je pokoj. Sú v¹ak situácie, ako napr. keï robíte grafické demo, ktoré má by» èo najmen¹ie, keï príde vhod vý¹kovú mapu vygenerova» procedurálne. Tak¾e si uká¾eme ako na to. E¹te snáï spomeniem ¾e èíta» ïalej mô¾u aj tí, ktorí chcú vedie» ako vygenerova» takzvané &quot;oblaky&quot; (niekedy sa tomu hovorí aj plazma), nakoµko tento tutoriál bude z veµkej èasti práve o tom.</p>

<p>Na¹a vý¹ková mapa bude vyzera» pribli¾ne takto:</p>

<div class="okolo_img"><img src="images/clanky/cl_gl_generovani_terenu/map.jpg" width="256" height="256" alt="Vý¹ková mapa" /></div>

<p>Tak¾e ako to teda funguje? Prvú vec, ktorú by ste mali pozna», je rekurzia. Pre tých, ktorí nevedia èo to je, skúsim v krátkosti vysvetli». Rekurzia je, keï nejaká procedúra alebo funkcia zavolá sama seba. Príklad:</p>

<pre>
procedure davaj(x: integer);
begin
	writeln(x);
	if x &lt; 10 then davaj(x+1);
end;
</pre>

<p>Tato procedúra spravi to, ¾e vypí¹u v¹etky èísla od X do 10  (tak¾a davaj(3); vypí¹e èísla od 3 do 10). To if x &lt; 10 zabezpeèí, ¾e keï bude procedúra zavolaná s parametrom väè¹ím alebo rovným 10, procedúra nebude ïalej vola» sama seba. Niesom si istý, tu¹ím sa tomu hovorí návrat z rekurzie alebo tak nejako, podstatné je to, ¾e bez toho by rekurzívna procedúra volala sama seba donekoneèna, èo by viedlo k pádu programu (pre nedostatok pamäte). Na túto vec treba v¾dy pri písaní rekurzívnych procedúr myslie», aby vám niekedy skonèili.</p>

<p>Tak¾e toµko vsuvka o rekurzii, teraz spa» k vytváraniu vý¹kovej mapy. Algorytmus je taký, ¾e procedúra dostane ako parametre súradnice obdå¾nika, ktorý chceme vyplni» oblakmi, rozdelí ho na 4 men¹ie obdå¾niky a zavolá sama seba na ka¾dý z nich. Situácia vyzerá takto:</p>

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

<p>Funkcia dostane ako parametre X1,Y1,X2,Y2, èo sú súradnice obdå¾nika, ktorý chceme vyplni». Najskôr skontroluje, èi náhodou jeho rozmer nieje men¹í ako 2x2, ak áno, ukonèí sa (to bude návrat z rekurzie). Potom stredu ka¾dej strany (1,2,3,4) nastaví farbu, ktorá je rovná priemeru farieb koncových bodov tejto strany (tak¾e farba bodu 1 bude (cl1+cl2)/2..atï) a stredu obdå¾nika nastaví farbu rovnú priemeru farieb jeho rohov, teda (cl1+cl2+cl3+cl4)/4. K tejto farbe potom pripoèíta/odpoèíta náhodné èíslo, ktorého veµkos» bude závisie» od veµkosti obdå¾nika. No a nakoniec zavolá 4 krát sama seba na v¹etky ¹tyri obdå¾niky ktoré vynikli &quot;rozdelením&quot; pôvodného obdå¾nika (cl1,1,5,3; 1,cl2,4,5; 3,5,2,cl3; 5,4,cl4,2). To je celé.</p>

<p>Teraz si uká¾eme ako to bude vyzera» prakticky (prakticky = ako to zapísa» v Delphi; týmto sa zároveò ospravedlòujem v¹etkým céèkarom, ale keï niekto pou¾íva tak hackerský jazyk ako C alebo nebodaj C++, urèite pochopí aj ten pascalovský kód ;)). Najskôr treba porie¹i», kam budeme na¹u vý¹kovú mapu generova». Ja som pou¾il globálne jednorozmerné dynamické pole, (aby som mohol jednoducho meni» veµkos» mapy, ale ak chcete, kµudne mô¾ete pou¾i» aj statické pole, alebo si alokova» pamä» tými va¹imi malloc-mi a getmem-ami), ktoré pomenujeme poeticky _.</p>

<pre>
var	_: array of byte;
	width: integer;//premenná width bude obsahova» ¹írku aj vý¹ku ¹tvorcovej mapy
</pre>

<p>Pred samotným aktom tvorenia procedúry, ktorá sa bude stara» o vygenerovanie oblakov do ná¹ho poµa, napí¹eme si zopár pomocných procedúr:</p>

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

<p>Skôr ako zaèneme, musíme si e¹te ujasni» jednu vec. Zjavne ideme generova» dvojrozmerný (2D) obrázok, a chceme ho ma» ulo¾ený v 1 rozmernom poli. Tak¾e otázka znie, aký je index poµa pre bod so súradnicami [X,Y]? Nebudem vás napína», je to takto:</p>

<pre>
Pole[Y * sirka + X]
</pre>

<p>Tudí¾ keï chceme bodu na súradniciach [X,Y] nastavi» farbu F, napí¹eme</p>

<pre>
_[Y * Width + X]:=F;
</pre>

<p>Teraz máme v¹etko èo potrebujeme na to, aby sme mohli napísa» na¹u rekurzívnu funkciu, ktorá nám vygeneruje oblaky. Nazveme si ju Divide. Tu je okomentovaný zdroják:</p>

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

<p>Mo¾no ste si v¹imli, ¾e  celé oblaky závisia od toho, akej farby sú v rohoch vý¹kovej mapy. Tak¾e pred samotným zavolaním procedúry Divide im nastavíme náhodné hodnoty:</p>

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

<p>Ak sa èudujete preèo tam je random(64) a nie random(256), tak vedzte, ¾e je to tak kôli tomu, aby bol terén na okrajoch ni¾¹í (to kôli kamere, ktorá bude lieta» okolo terénu). Preto sa prosím nesna¾te v tom hµada» nejaký hlb¹í význam.</p>

<p>Pre istotu si e¹te uká¾eme, ako ná¹ vysnený terén vyrenderova» pomocou OpenGL. Konkrétne si uká¾eme hneï dva spôsoby. Na prvý budeme potrebova» ïal¹ie dve procedúry. Tieto procedúry pou¾ijeme, keï budeme chcie» vyráta» normálový vektor µubovolného trojuholníka - to sa hodí, keï chceme scénu osvetli»:</p>

<p>Definujeme si aj ¹truktúru TCoord3D, do ktorej si mô¾eme ulo¾i» súradnice bodu alebo vektora:</p>

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

<p>Terén budeme vykresµova» po trojuholníkoch - ka¾dej ¹tvorici bodov ([X,Y], [X+1,Y], [X+1,Y] a [X+1,Y+1]) na vý¹kovej mape budú zodpoveda» dva trojuholníky. Tak¾e nám staèí prebehnú» pole dvoma vnorenými cyklami. No a takto to bude vyzera»:</p>

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

<p>Z komentárov by malo by» v¹etko jasné. Snáï len toµko, ¾e treba niekde zadefinova» premennú Smooth typu boolean, ktorá urèuje, èi sa má terén vykresµova» vyhµadený alebo nie.</p>

<!--
<p>Pozn. (WOQ): V¹echny podmínky &quot;if Smooth then&quot; bych spojil do jedné a vyhodil ven mimo vnoøené cykly - v podstatì dva rùzný zdrojáky pro svìtla a bez svìtel. Kdy¾ si pronásobíte 256*256*10 vyjde vám 655360 :-(, co¾ je u¾ docela velké èíslo, zvlá¹» kdy¾ jde nahradit jednièkou a jedním copy &amp; paste s nìkolika úpravami ;-)</p>
-->

<p class="autor">napsal: Peter Mindek <?VypisEmail('2mindo@gmail.com');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_gl_generovani_terenu.tar.gz');?> - Delphi</li>
</ul>

<div class="okolo_img"><img src="images/clanky/cl_gl_generovani_terenu/screen.jpg" width="648" height="508" alt="Screenshot programu" /></div>

<?
include 'p_end.php';
?>
