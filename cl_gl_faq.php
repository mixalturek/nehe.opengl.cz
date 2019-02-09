<?
$g_title = 'CZ NeHe OpenGL - FAQ: �asto kladen� dotazy';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>FAQ: �asto kladen� dotazy</h1>

<p class="nadpis_clanku">Na emailu se mi n�kter�, v�t�inou za��te�nick�, dotazy neust�le opakuj�, jako p��klad lze uv�st probl�my s knihovnou GLAUX a symbolickou konstantou CDS_FULLSCREEN v Dev-C++. Douf�m, �e tato str�nka trochu sn�� zat�en�, ale pokud si st�le nev�te rady, nebojte se mi napsat. Douf�m, �e nebude vadit, kdy� sem um�st�m i ten v� probl�m.</p>


<h3>Co je OpenGL?</h3>
<p>Znal� program�tor by v�m s nejv�t�� pravd�podobnost� odpov�d�l, �e se jedn� o standard popisuj�c� API (aplika�n� programov� rozhran�) mezi programem a hardwarem grafick� karty. J� se pokus�m o trochu pochopiteln�j�� vysv�tlen�. Ur�it� jste u� vid�li n�jakou 3D ak�n� hru (nap�. Quake, Unreal Tournament...), ve kter� se lze pohybovat do jak�hokoli sm�ru (nahoru, dol�, doleva, doprava, dop�edu, dozadu). OpenGL, velice zjednodu�en� �e�eno, zaji��uje rendering (vykreslen�) t�chto 3D objekt� na 2D monitor a d�le se star� o spoustu dal��ch v�c�, kter� byste ale na tomto m�st� stejn� nepochopili...</p>


<h3>Chci se nau�it programovat v OpenGL, ale nev�m, kde za��t.</h3>
<p>Za�n�te nap��klad u �l�nku <?OdkazWeb('cl_gl_zacinam', 'Pomoc, za��n�m');?>, ur�it� v n�m naleznete odpov��.</p>


<h3>Vykreslov�n� je pomal� a trhan� (okolo 1 FPS a m�n�)</h3>
<p>OpenGL s nejv�t�� pravd�podobnost� neb�� na hardwaru grafick� karty, ale emuluje se softwarov�. Zkuste nainstalovat/p�einstalovat ovlada�e grafick� karty. Mimochodem, nespol�hejte na to, �e jsou v syst�mu, kdy� jde nastavit libovoln� rozli�en� a barevnou hloubku (ano, i ve Win XP). <?OdkazWeb('cl_gl_linux', 'Instalace v Linuxu');?>.</p>


<h3>Nefunguje mi blending, mapov�n� textur atd.</h3>
<p>Nezapomn�li jste ho zapnout pomoc� funkce glEnable()? Grrrr!!! Klid, m� se to st�v� taky :-)</p>


<h3>Lze zad�vat hodnoty barev cel�mi ��sly?</h3>
<p>Ano, m�sto glColor3<b>f</b>() - hodnoty od 0.0f do 1.0f - zavolejte glColor3<b>ub</b>(). <b>U</b>nsigned <b>B</b>yte m��e nab�vat hodnot od 0 do 255, na tato ��sla jste asi zvykl� v�ce a pravd�podobn� to bude i rychlej�� (o m�lo, ale p�ece :-)...</p>


<h3>Co znamenaj� ��sla 0.0f a 1.0f u funkce glTexCoord2f(x, y)?</h3>
<p>Tato dv� ��sla ozna�uj� x, y pozici na textu�e - 0.0f je lev�/spodn� okraj, 1.0f ur�uje prav�/horn� okraj a ��sla mezi specifikuj� jinou pozici (nap�. 0.5f polovina textury). Nemus� se v�ak zad�vat pouze hodnoty v intervalu od 0.0f do 1.0f. P�ed�n� v�t��ch ��sel vytvo�� &quot;dla�dicov�&quot; efekt - jako kdy� si d�te v OS na plochu obr�zek 50x50 pixel� vedle sebe. Nap�. ��slo 10.0f zp�sob� namapov�n� dan� textury 10x p�es celou ���ku polygonu.</p>


<h3>Jak vykreslit objekt na ur�itou pozici v okn� a o ur�it� velikosti (m��eno v pixelech)?</h3>
<p>Probl�mem je, �e i kdybychom ignorovali nato�en� sc�ny a podobn� v�ci naprosto nep�enositeln� ze 3D do 2D, perspektivn� korekce zp�sob�, �e se bude velikost objekt� v z�vislosti na hloubce m�nit. Samoz�ejm� lze pou��t r�zn� p�epo��t�vac� funkce (gluProject() a <?OdkazWeb('cl_gl_gluunproject', 'gluUnProject()');?>), kter� konvertuj� pixelov� sou�adnice v okn� na OpenGL jednotky a naopak, ale a� budete d�lat v OpenGL trochu d�le, zjist�te, �e ve v�t�in� p��pad� nic takov�ho v�bec nepot�ebujete.</p>

<p>Pokus�m se o modelovou situaci. Programujeme klasick� 3D automobilov� simul�tor, ve kter�m se m��e hr�� d�vat z pohledu �idi�e, z kamery um�st�n� naho�e za autem, pop�. z dynamicky p�esunovan� kamery vedle cesty. Budeme zmen�ovat/zv�t�ovat v�echny objekty podle zvolen�ho pohledu a vym��let 3 r�zn� funkce pro rendering? Samoz�ejm�, �e ne. Hlavn� trik spo��v� v tom, �e ve v�ech t�ech pohledech vykresl�me v�e naprosto stejn�, ale p�ed samotn�m vykreslen�m zm�n�me pozici kamery a �hel pohledu - nic v�c, nic m��. �ekn�me, �e z�vodn� dr�ha zab�r� plochu 1000 jednotek s maxim�ln�m p�ev��en�m 30 jednotek. Stromy jsou vysok� 3 a� 5 jednotek a modely z�vodn�ch aut maj� d�lku 1 jednotku - nejsou d�le�it� konkr�tn� velikosti, ale pom�r mezi nimi. S t�mito znalostmi m��eme bez probl�m� vykreslit sc�nu, o n�jak� pohledy se v�bec nemus�me starat. Zm�nu pohledu pak provedeme zavol�n�m funkc� glTranslatef(), glRotatef(), pop�. gluLookAt() p�ed samotn�m vykreslen�m. Douf�m, �e je to pochopiteln�.</p>

<p>Abych se ale vr�til k p�vodn� ot�zce, perspektivn� zmen�ov�n� objekt� lze eliminovat pou�it�m pravo�hl� (=kolm�) projekce. S v�hodou se j� vyu��v� nap�. u <?OdkazWeb('tut_17', 'v�pisu text�');?> (u nich najdete i implementaci v programu). Postup spo��v� v nahrazen� gluPerspective() za funkci glOrtho(), kter� se p�ed�vaj� po�adovan� rozm�ry 2D sc�ny. P�i p�esunech objektu do hloubky budou jeho rozm�ry p�i pravo�hl� projekci v�dy stejn�. A je�t� mal� pozn�mka na z�v�r: pokud nep�ed�te glOrtho() aktu�ln� rozm�ry viewportu, ale konstantn� hodnoty nap�. 1000x1000, p�i zm�n� jeho velikosti se relativn� poloha vykreslovan�ho objektu nezm�n�. Z toho vypl�v�, �e po zv�t�en� okna ze 640x480 na 1600x1200 bude objekt &quot;uprost�ed&quot; opravdu &quot;uprost�ed&quot; a ne v lev� ��sti.</p>


<h3>OpenGL &amp; 2D grafika</h3>
<p>OpenGL 2D grafiku p��mo nepodporuje, ale m�sto 3D glVertex3f(x, y, z) lze pou��vat jej� 2D variantu glVertex2f(x, y) - za z sou�adnici se automaticky dosad� 0, ale na pozad� se defakto jedn� st�le o 3D. Pokud budou vadit &quot;bezrozm�rn�&quot; OpenGL jednotky a perspektiva, je mo�n� se p�epnout do pravo�hl� projekce (NeHe Tutori�ly <?OdkazWeb('tut_17', '17');?>, <?OdkazWeb('tut_21', '21');?>, <?OdkazWeb('tut_24', '24');?> atd.), kde lze nastavit zad�v�n� sou�adnic v pixelech.</p>

<p>Blitter a spol., co v�m, OpenGL p��mo nepodporuje (update: glDrawPixels(), glReadPixels(), glCopyPixels() atd.), ale naprogramovat si ho nen� zase a� tak t�k� (NeHe <?OdkazWeb('tut_29', '29');?>). Abych to shrnul, s klasickou 2D grafikou je v OpenGL trochu probl�m, proto�e bez grafick�ho akceler�toru se hra (v�t�inou se jedn� o hry :) nepohne z m�sta, nicm�n� samo o sob� m� tak� spoustu vychyt�vek. Nap��klad perfektn� vypadaj� klasick� 2D rovinn� hry, ve kter�ch jsou v�echny objekty i prost�ed� trojrozm�rn� (ale na plo�e, v konstantn� hloubce).</p>


<h3>Nejde mi zkompilovat k�d <?OdkazWeb('tut_06', '6. NeHe Tutori�lu');?> a vy���</h3>
<p>V tomto tutori�lu se za��naj� pou��vat textury, pro jejich� nahr�v�n� je pot�eba knihovna GLAUX. Tato knihovna je docela zastaral�, a proto se k n�kter�m kompil�tor�m standardn� nep�ibaluje a obecn� se ji nedoporu�uje pou��vat. Asi nejjednodu��� �e�en� je st�hnout si ji <?OdkazWeb('download', 'v downloadu');?>, d�le m��ete zkusit nahr�t obr�zek jinou knihovnou (nap�. <?OdkazWeb('cl_sdl_image', 'SDL_Image');?> - nutn� projekt v SDL a ne ve Win API) nebo si napsat vlastn� nahr�vac� k�d (nap�. form�t TGA byl vysv�tlen v <?OdkazWeb('tut_24', 'tut. 24');?>, <?OdkazWeb('tut_33', 'tut. 33');?>).</p>


<h3>V Dev-C++ nejde zkompilovat <b>ChangeDisplaySettings(&amp;dmScreenSettings, CDS_FULLSCREEN)</b>.</h3>
<p>N�kter� kompil�tory a v�vojov� prost�ed�, mezi nimi pr�v� Dev-C++, symbolickou konstantu CDS_FULLSCREEN automaticky nedefinuj�. Probl�m vy�e��te ru�n�m nadefinov�n�m, m�lo by sta�it n�co takov�ho:</p>

<p class="src0">#ifndef CDS_FULLSCREEN<span class="kom">// N�kter� kompil�tory nedefinuj� CDS_FULLSCREEN</span></p>
<p class="src1">#define CDS_FULLSCREEN 4<span class="kom">// Ru�n� nadefinov�n�</span></p>
<p class="src0">#endif</p>


<h3>Jak do programu nahr�t objekt ze 3D Studia Max (.3ds)?</h3>
<p>Zkuste �l�nek <?OdkazWeb('cl_gl_3ds', 'Na��t�n� .3DS model�');?>...</p>

<!--
<h3></h3>
<p class="src"></p>
-->

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?>, 15.08.2004</p>

<?
include 'p_end.php';
?>
