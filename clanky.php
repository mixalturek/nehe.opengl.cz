<?
$g_title = 'CZ NeHe OpenGL - �l�nky';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>�l�nky</h1>


<div class="object">
<div class="date">24.09.2005</div>
<h3>
<?OdkazWeb('cl_gl_kamera_3d', 'Kamera pro 3D sv�t');?> - Michal Turek</h3>

<p>V tomto �l�nku se pokus�me implementovat snadno pou�itelnou t��du kamery, kter� bude vhodn� pro pohyby v obecn�m 3D sv�t�, nap��klad pro n�jakou st��le�ku - my� m�n� sm�r nato�en� a �ipky na kl�vesnici zaji��uj� pohyb. P�esto�e budeme pou��vat mali�ko matematiky, nebojte se a sm�le do �ten�!</p>
</div>


<div class="object">
<div class="date">07.09.2005</div>
<h3>
<?OdkazWeb('cl_gl_generovani_terenu', 'Procedur�lne generovanie ter�nu');?> - Peter Mindek</h3>

<p>Mo�no ste u� po�uli o v��kov�ch map�ch. S� to tak� �iernobiele obr�zky, pomocou ktor�ch sa vytv�ra 3D ter�n (v��ka ter�nu na ur�itej poz�cii je ur�en� farbou zodpovedaj�ceho bodu na v��kovej mape). Najjednoduch�ie je v��kov� mapu na��ta� zo s�boru a je pokoj. S� v�ak situ�cie, ako napr. ke� rob�te grafick� demo, ktor� m� by� �o najmen�ie, ke� pr�de vhod v��kov� mapu vygenerova� procedur�lne. Tak�e si uk�eme ako na to. E�te sn�� spomeniem �e ��ta� �alej m��u aj t�, ktor� chc� vedie� ako vygenerova� takzvan� &quot;oblaky&quot; (niekedy sa tomu hovor� aj plazma), nako�ko tento tutori�l bude z�ve�kej �asti pr�ve o tom.</p>
</div>


<div class="object">
<div class="date">27.01.2005</div>
<h3>
<?OdkazWeb('cl_gl_billboard', 'Billboarding (p�ikl�p�n� polygon� ke kame�e)');?> - Michal Turek</h3>

<p>Ka�d�, kdo n�kdy programoval ��sticov� syst�my, se jist� setkal s probl�mem, jak za��dit, aby byly polygony viditeln� z jak�hokoli sm�ru. Nebo-li, aby se nikdy nestalo, �e p�i nato�en� kamery kolmo na rovinu ��stice, nebyla vid�t pouze tenk� linka. Slo�it� probl�m, ultra jednoduch� �e�en�...</p>
</div>


<div class="object">
<div class="date">22.10.2004</div>
<h3>
<?OdkazWeb('cl_freetype_cz', 'FreeType Fonty v OpenGL a �esky');?> - Luk� Beran - Berka</h3>

<p>Chcete pou��vat ve sv�ch programech FreeType Fonty i s �esk�mi znaky? Pokud ano, jste na spr�vn�m m�st�. Tento �l�nek dopl�uje NeHe Tutori�l 43, ve kter�m bylo pops�no pou�it� FreeType s OpenGL, ale bohu�el bez �esk�ch znak�. Pou�ito s laskav�m svolen�m <?OdkazBlank('http://programovani.wz.cz/');?>.</p>
</div>


<div class="object">
<div class="date">25.08.2004</div>
<h3>
<?OdkazWeb('cl_gl_3ds', 'Na��t�n� .3DS model�');?> - Michal Tu�ek</h3>

<p>V�tomto �l�nku si uk�eme, jak nahr�t a vykreslit model ve form�tu .3DS (3D Studio Max). N� k�d bude um�t bez probl�m� na��tat soubory do t�et� verze programu, s vy���mi verzemi bude pracovat tak�, ale nebude podporovat jejich nov� funkce. Vych�z�m z�uk�zkov�ho p��kladu z <?OdkazBlank('http://www.gametutorials.com/');?>, kde tak� najdete zdrojov� k�dy pro C++ (�l�nek je v Delphi).</p>
</div>


<div class="object">
<div class="date">15.08.2004</div>
<h3>
<?OdkazWeb('cl_gl_delphi', 'Vytvo�en� OpenGL okna v Delphi');?> - Michal Tu�ek</h3>

<p>Tento �l�nek popisuje vytvo�en� OpenGL okna pod opera�n�m syst�mem MS Windows ve v�vojov�m prost�ed� Borland Delphi. J� osobn� pou��v�m Delphi verze 7, ale ani v ni���ch verz�ch by nem�l b�t probl�m vytvo�en� k�d zkompilovat a spustit. Z v�t�� ��sti se jedn� o p�epis prvn�ho NeHe Tutori�lu z jazyka C/C++ do Pascalu. Tak�e sm�le do toho...</p>
</div>


<div class="object">
<div class="date">11.07.2004</div>
<h3>
<?OdkazWeb('cl_gl_octree', 'Octree');?> - Michal Turek</h3>

<p>Octree (octal tree, oktalov� strom) je zp�sob rozd�lov�n� 3D prostoru na oblasti, kter� umo��uje vykreslit pouze tu ��st sv�ta/levelu/sc�ny, kter� se nach�z� ve v�hledu kamery, a t�m zna�n� urychlit rendering. M��e se tak� pou��t k detekc�m koliz�.</p>
</div>


<div class="object">
<div class="date">20.06.2004</div>
<h3>
<?OdkazWeb('cl_gl_planety', 'Generov�n� planet');?> - Milan Turek</h3>

<p>Pokud budete n�kdy pot�ebovat pro svou aplikaci vygenerovat realisticky vypadaj�c� planetu, tento �l�nek se v�m bude ur�it� hodit - popisuje jeden ze zp�sob� vytv��en� nedeformovan�ch kontinent�. Obvykl� zp�soby pokr�v�n� koule rovinnou texturou kon�� obrovsk�mi deformacemi na p�lech. Dal�� nev�hodou n�kter�ch zp�sob� je, �e v�sledek je orientov�n v n�jak�m sm�ru. To u t�to metody nehroz�.</p>
</div>


<div class="object">
<div class="date">09.05.2004</div>
<h3>
<?OdkazWeb('cl_gl_faq', 'FAQ: �asto kladen� dotazy');?> - Michal Turek</h3>

<p>Na emailu se mi n�kter�, v�t�inou za��te�nick�, dotazy neust�le opakuj�, jako p��klad lze uv�st probl�my s knihovnou GLAUX a symbolickou konstantou CDS_FULLSCREEN v Dev-C++. Douf�m, �e tato str�nka trochu sn�� zat�en�, ale pokud si st�le nev�te rady, nebojte se mi napsat. Douf�m, �e nebude vadit, kdy� sem um�st�m i ten v� probl�m.</p>
</div>


<div class="object">
<div class="date">27.03.2004</div>
<h3>
<?OdkazWeb('cl_gl_linux', 'Zprovozn�n� OpenGL v Linuxu (ovlada�e karty, kompilace)');?> - Michal Turek</h3>

<p>Kdy� jsem p�ibli�n� p�ed p�l rokem (podzim 2003) p�ech�zel z MS Windows&reg; na opera�n� syst�m Linux, m�l jsem relativn� velk� pot�e se zprovozn�n�m OpenGL. Nejedn� se sice o nic slo�it�ho, nicm�n� pro tehdy nic nech�paj�c�ho Woqa u�ivatele (analogie na Frantu u�ivatele :-) to byl naprosto ne�e�iteln� probl�m.</p>
</div>


<div class="object">
<div class="date">31.12.2003</div>
<h3>
<?OdkazWeb('cl_gl_kamera', 'T��da kamery a Quaternionu');?> - Michal Turek</h3>

<p>Chcete si naprogramovat leteck� simul�tor? Sm�r letu nad krajinou m��ete m�nit kl�vesnic� i my��... Vytvo��me n�kolik u�ite�n�ch t��d, kter� v�m pomohou s matematikou, kter� stoj� za definov�n�m v�hledu kamery a pak v�echno spoj�me do jednoho funk�n�ho celku.</p>
</div>


<div class="object">
<div class="date">13.12.2003</div>
<h3>
<?OdkazWeb('cl_gl_matice2', 'Matice v OpenGL');?> - Radom�r Vr�na</h3>

<p>V tomto �l�nku se dozv�te, jak�m zp�sobem OpenGL ukl�d� hodnoty rotac� a translac� do sv� modelview matice. Samoz�ejm� nebudou chyb�t obr�zky jej�ho obsahu po r�zn�ch maticov�ch operac�ch.</p>
</div>


<div class="object">
<div class="date">09.12.2003</div>
<h3>
<?OdkazWeb('cl_gl_matice', 'Operace s maticemi');?> - P�emysl Jaro�</h3>

<p>Zaj�mali jste se n�kdy o to, jak funguj� OpenGL funkce pracuj�c� s maticemi? V tomto �l�nku si vysv�tl�me, jak funguj� funkce typu glTranslatef(), glRotatef(), glScalef() a jak je p��padn� nahradit vlastn�m k�dem.</p>
</div>


<div class="object">
<div class="date">01.12.2003</div>
<h3>
<?OdkazWeb('cl_gl_gluunproject', 'Pou��v�me gluUnProject()');?> - P�emysl Jaro�</h3>

<p>Pot�ebujete transformovat pozici my�i na sou�adnice v OpenGL sc�n� a nev�te si rady? Pokud ano, jste na spr�vn�m m�st�.</p>
</div>


<div class="object">
<div class="date">28.10.2003</div>
<h3>
<?OdkazWeb('cl_timer', 'Timer');?> - Marek Ol��k - Eosie</h3>

<p>P�edstavte si, �e d�l�te n�jak� velk� dynamick� sv�t, pro kter� pot�ebujete mnoho v�po�t� z�visl�ch na uplynul�m �ase (pohyb, rotace, animace, fyzika). Pokud synchronizujete klasicky pomoc� FPS, neobejdete se p�i ka�d�m vykreslen� bez spousty d�len�. Z�kladem v�eho je, tyto operace prov�d�t co nejm�n�, abychom zbyte�n� nezat�ovali procesor.</p>
</div>


<div class="object">
<div class="date">14.09.2003</div>
<h3>
<?OdkazWeb('cl_gl_zacinam', 'Pomoc, za��n�m');?> - Michal Turek</h3>

<p>V�te, vzpomn�l jsem si na sv� za��tky s OpenGL, kdy �lov�k nemohl sehnat t�m�� ��dn� informace o OpenGL, jednodu�e proto, �e ��dn� neexistovaly. To byl vlastn� d�vod pro p�eklady NeHe Tutori�l� a n�sledn� vznik tohoto webu. Informac� je u� nyn� relativn� dost, ale st�le z�stala ot�zka: Kde za��t?</p>
</div>


<div class="object">
<div class="date">21.07.2003</div>
<h3>
<?OdkazWeb('cl_fps', 'FPS: Konstantn� rychlost animace');?> - Michal Turek</h3>

<p>FPS je zkratka z po��te�n�ch p�smen slov Frames Per Second, kter� by se dala do �e�tiny p�elo�it jako po�et sn�mk� za sekundu. Tato t�i p�smena jsou sp�sou p�i spou�t�n� program� na r�zn�ch po��ta��ch. Vezm�te si hru, kterou program�tor za��te�n�k vyv�j� doma na sv�m po��ta�i o rychlosti, �ekn�me, Pentium II. D� ji kamar�dovi, aby se na ni pod�val a zhodnotil. Kamar�d m� doma P4, spust� ji a v�e je ��len� rychl�. D�ky FPS se toto nikdy nestane, na jak�mkoli po��ta�i p�jde hra v�dy stejn� rychle.</p>
</div>


<div class="object">
<div class="date">04.11.2002</div>
<h3>
<a href="cl_gl_referat.pdf">OpenGL - Refer�t na praktikum z informatiky</a> - Daniel �ech (PDF, 27 stran)</h3>

<p>�l�nek popisuje vznik a principy OpenGL, z�klady pr�ce s n�m, OpenGL datov� typy, pro� funkce za��naj� gl a na jejich konci b�v� 3f, jinde 3ub apod. a z�kladn� pr�ci v OpenGL. Na konci naleznete n�kolik uk�zkov�ch zdrojov�ch k�d� v GLUT a Win32 API a porovn�n� OpenGL s DirectX. Ur�it� si ho p�e�t�te, stoj� za to. P�vod: n�kde na internetu.</p>
</div>


<div class="object">
<div class="date">25.05.2004</div>
<h3>
<a href="cl_sdl_hry.pdf">N�kolik pozn�mek k tvorb� po��ta�ov�ch her</a> - Bernard Lidick� (PDF, 19 stran)</h3>

<p>Tento �l�nek by m�l b�t shrnut�m m�ch zku�enost� s tvorbou po��ta�ov�ch her, mohl by usnadnit �ivot za��naj�c�m amat�rsk�m tv�rc�m her a zejm�na program�tor�m. Pod�v�me se v n�m na n�kolik obecn�ch v�c� o hr�ch a pak se vrhneme na grafiku, kl�vesnici s my�� a nakonec na �as. V ka�d� ��sti se pokus�me vytvo�it n�jak� p��klady a na nich p�edv�st o �em je �e�. <?OdkazDown('download/clanky/cl_sdl_hry.tar.bz2');?> - text �l�nku (PDF, TeX) + p��klady.</p>
</div>


<div class="object">
<div class="date">28.03.2004</div>
<h3>
<?OdkazWeb('cl_sdl_picture', 'Komprimovan� textury a SDL_Image');?> - Radom�r Vr�na</h3>

<p>V tomto �l�nku si uk�eme, jak vytv��et komprimovan� OpenGL textury a jak za pomoci knihovny SDL_Image snadno na��tat obr�zky s alfa kan�lem nebo v paletov�m re�imu. T��du Picture jsem se sna�il navrhnou tak, aby byla co nejjednodu��� a dala se snadno pou��t v ka�d�m programu, z�rove� d�ky SDL_Image poskytuje velk� mo�nosti.</p>
</div>


<div class="object">
<div class="date">07.04.2003</div>
<h3>
<?OdkazWeb('cl_sdl_image', 'Knihovna SDL Image');?> - Bernard Lidick�</h3>

<p>Ur�it� se v�m nel�b� m�t v�echny textury ulo�en� v BMP souborech, kter� nejsou zrovna p��telsk� k m�stu na disku. Bohu�el SDL ��dn� jin� form�t p��mo nepodporuje. Nicm�n� existuje mal� roz���en� v podob� knihovni�ky SDL Image poskytuj�c� funkci IMG_Load(), kter� um� na��st v�t�inu pou��van�ch grafick�ch form�t�.</p>
</div>


<div class="object">
<div class="date">17.02.2003</div>
<h3>
<?OdkazWeb('cl_sdl_okno', 'Vytvo�en� SDL okna');?> - Bernard Lidick�</h3>

<p>Woq m� po��dal, abych napsal tutori�l pro pou��v�n� OpenGL pod knihovnou SDL. Je to m�j prvn� tutori�l, tak�e douf�m, �e se bude l�bit. Zkus�me vytvo�it k�d, kter� bude odpov�dat druh� lekci &quot;norm�ln�ch&quot; tutori�l�. Zdrojov� k�d je &quot;ofici�ln�&quot; NeHe port druh� lekce do SDL. Pokus�m se popsat, jak se vytv��� okno a v�ci okolo.</p>
</div>


<div class="object">
<div class="date">13.02.2004</div>
<h3>
<?OdkazWeb('cl_mat_primka2d', 'P��mka ve 2D');?> - Michal Turek</h3>

<p>Radom�r Vr�na m� po��dal o radu, jak vypo��tat pr�se��k dvou 2D p��mek. Rozhodl jsem se, �e mu m�sto obecn�ch matematick�ch vzorc� po�lu rovnou kompletn� C++ k�d. Nicm�n� mi trochu p�erostl p�es hlavu, a tak vznikla kompletn� t��da p��mky v obecn�m tvaru. Krom� pr�se��ku um� ur�it i jejich vz�jemnou polohu (rovnob�n�, kolm�...), �hel, kter� sv�raj� nebo vzd�lenost libovoln�ho bodu od p��mky. Douf�m, �e tento m�j drobn� �let nebude moc vadit :-]</p>
</div>


<div class="object">
<div class="date">10.07.2003</div>
<h3>
<?OdkazWeb('cl_mat_geometrie', 'Analytick� geometrie');?> - Michal Turek</h3>

<p>Tento �l�nek vych�z� z m�ch z�pisk� do matematiky z druh�ho ro�n�ku na st�edn� �kole. Jo, na diktov�n� byla Wakuovka v�dycky dobr�... Tehdy jsem moc nech�pal k �emu mi tento obor matematiky v�bec bude, ale kdy� jsem se za�al v�novat OpenGL, z�hy jsem pochopil. Zkuste si vz�t nap��klad n�jak� pozd�j�� NeHe Tutori�l. Bez znalost� 3D matematiky nem�te �anci. Douf�m, �e v�m tento �l�nek pom��e alespo� se z�klady a pochopen�m princip�.</p>
</div>


<div class="object">
<div class="date">22.07.2003</div>
<h3>
<?OdkazWeb('cl_mfc_dialog', 'OpenGL okno v dialogu');?> - Michal Turek</h3>

<p>Zobraz�me d�tsk� OpenGL okno v dialogu a budeme mu p�ed�vat hodnoty z�skan� z ovl�dac�ch prvk� (editboxy a radiobuttony). Periodick� p�ekreslov�n� OpenGL okna zaji��uje zpr�va WM_TIMER - troj�heln�k a �tverec budou rotovat.</p>
</div>


<div class="object">
<div class="date">23.06.2003</div>
<h3>
<?OdkazWeb('cl_mfc_tisk', 'Tisk a n�hled p�ed tiskem OpenGL sc�ny');?> - Milan Turek</h3>

<p>Obalen� OpenGL t��dami MFC n�m dovol� vyu��t obou v�hod API: rychl�ho vykreslov�n� a elegantn�ho rozhran�. Nicm�n� d�ky faktu, �e mnoho ovlada�� tisk�ren nepracuje s API funkc� SetPixelFormat(), nen� mo�n� tisknout OpenGL sc�nu p��mo na tisk�rnu. Velmi roz���en� technika je vykreslit OpenGL sc�nu do DIBu a pot� ji zkop�rovat do DC pro tisk nebo n�hled. V tomto �l�nku uvid�te jak to ud�lat.</p>
</div>


<div class="object">
<div class="date">23.06.2003</div>
<h3>
<?OdkazWeb('cl_mfc_dib', 'Kop�rov�n� OpenGL okna do DIBu');?> - Milan Turek</h3>

<p>Ob�as pot�ebujeme sejmout obrazovku v OpenGL a pot� s n� pracovat jako s oby�ejnou bitmapou. V tomto �l�nku v�m uk�i z�sk�n� obsahu OpenGL okna a jeho ulo�en� do DIBu ve form� nekomprimovan� bitmapy. Jedin� omezen�m m��e b�t 24 bitov� barevn� hloubka obrazovky.</p>
</div>


<div class="object">
<div class="date">23.06.2003</div>
<h3>
<?OdkazWeb('cl_wapi_setric', 'Jak na �et�i� obrazovky');?> - Michal Turek</h3>

<p>U� d�vno jsem si cht�l naprogramovat vlastn� �et�i� obrazovky. M�l jsem sice t��du CScreenSaverWnd pro MFC, ale ta nepodporovala OpenGL. U <?OdkazWeb('tut_38', 'NeHe Tutori�lu 38');?> jsem na�el odkaz na �et�i� obrazovky s podporou OpenGL, kter� napsal Brian Hunsucker. Cht�l bych mu pod�kovat, proto�e na jeho zdrojov�m k�du z v�t�� ��sti stav� tento �l�nek.</p>
</div>




<?
include 'p_end.php';
?>
