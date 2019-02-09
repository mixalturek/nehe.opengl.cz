<?
$g_title = 'CZ NeHe OpenGL - Pomoc, za��n�m';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>Pomoc, za��n�m</h1>

<p class="nadpis_clanku">V�te, vzpomn�l jsem si na sv� za��tky s OpenGL, kdy �lov�k nemohl sehnat t�m�� ��dn� informace o OpenGL, jednodu�e proto, �e ��dn� neexistovaly. To byl vlastn� d�vod pro p�eklady NeHe Tutori�l� a n�sledn� vznik tohoto webu. Informac� je u� nyn� relativn� dost, ale st�le z�stala ot�zka: Kde za��t?</p>

<p>�tete-li tuto str�nku, tak u� asi v�te, �eho chcete dos�hnout - nau�it se OpenGL, abyste mohli do sv�ch skv�l�ch 2D her p�idat i t�et� rozm�r. Ka�d� cesta v�ak za��n� jedn�m jedin�m krokem, o kter�m se ��k�, �e b�v� v�dy nejt쾹�. T�mto prvn�m krokem by mohl b�t nap��klad �l�nek, kter� pr�v� �tete.</p>

<h3>Programov�n�</h3>

<p>P�edt�m ne� se pust�te do OpenGL, m�li byste um�t (MUS�TE UM�T) programovat. T�mto nemysl�m pades�ti-��dkov� kalkula�ky v Pascalu typu: zadejte prvn� ��slo, zadejte oper�tor, zadejte druh� ��slo, po��t�m, v�sledek je..., ale n�jak� v�t�� projekt pln� spousty vno�en�ch cykl� a podm�nek. Mimochodem nepou��vejte Pascal (nen� my�leno Delphi, ale star� TP 6, TP 7 ap.). Nejsem &quot;rasista&quot;, ale nem�m r�d &quot;archeologick� poz�statky&quot;, o kter�ch u�itel� (kte�� samoz�ejm� v�t�inou neum� programovat) ��kaj�, �e jsou nezbytn�m z�kladem pro v�uku programov�n�. P�itom v�ak zakl�daj� na takovou spoustu zlozvyk� (nezbytn�ch pro pr�ci v Pascalu a Dosu), �e trv� p�kn� dlouho ne� se z nich pr�m�rn� inteligentn� �lov�k vyhrabe. Osobn� doporu�uji C/C++, proto�e je velmi roz���en� a proto�e na jeho syntaxi stav� v�t�ina pozd�j��ch programovac�ch jazyk� - Java, JavaScript, Perl, PHP, Action Script... Pokud n�kdy zkus�te programovat nap�. webov� aplikace v�e bude desetkr�t jednodu���, ne� kdy� za��n�te od za��tku.</p>

<p>Abych se vr�til zp�t ke �l�nku... D�kladn� znalost k�dov�n� sice pro OpenGL nen� podm�nkou, ale na sto procent se podobn� znalosti budou hodit. Kdy� ne hned tak t�eba a� budete do sv�ch program� importovat nejr�zn�j�� form�ty obr�zk� nebo model�. Pokud jste u� n�kdy pracovali s grafikou, zkuste vytvo�it hru Tetris a pokud ne, tak alespo� zkuste vytvo�it obecn� algoritmus pro p�evod ��sel z libovoln� soustavy do libovoln� jin�. Nap��klad ze sedmi�kov� do dvaceti p�tkov�. �e neum�te pracovat v grafick�m re�imu v�bec nevad�. V�e obstar� OpenGL.</p>

<h3>API</h3>

<p>U� um�te opravdu dob�e programovat :-), ale nejsp� pouze v Dosovsk�m textov�m re�imu. Sly�eli jste n�kdy o ud�lostmi ��zen�m programov�n�, kdy opera�n� syst�m pos�l� oknu zpr�vy o stisku kl�ves, pohybu my��, po�adavky na p�ekreslen�...? Ne? Pak si vyberte n�jak� API (Application Programming Interface) a nau�te se ho ovl�dat. Pokud chcete vytv��et programy pro MS&reg; Windows&reg;, m�te v�ce mo�nost�, ale s nejv�t�� pravd�podobnost� se budete rozhodovat mezi &quot;klasick�m&quot; Win32 API a knihovnou MFC (Microsoft Foundation Class Library). Panuje n�zor, �e MFC by se m�lo pou��vat pro aplikace typu textov�ch editor� a dialogov�ch oken. Dema, hry ap. by m�l program�tor vytv��et v syst�mov�m API, proto�e je v�dy (v�t�inou) rychlej��. K tomuto n�zoru se tak� p�ikl�n�m. Kdy� m�m programovat hru, kter� m� b�t rychl�, mus�m p�esn� v�d�t, co se v programu odehr�v�. V MFC se v�echny funkce volaj� &quot;jakoby n�hodou&quot; - k hlavn� smy�ce programu se prost� nedostanete (pokud to neum�te). Z toho plyne: Chcete-li programovat pod OpenGL, vyberte si syst�mov� API. Mimochodem, pr�v� v n�m jsou psan� NeHe Tutori�ly.</p>

<p>Nem�te dostatek pen�z na zakoupen� leg�ln�ho opera�n�ho syst�mu MS Windows? Nebo se nechcete v�zat ke konkr�tn�mu opera�n�mu syst�mu? Nahra�te Win32 API za multiplatformn� knihovnu <?OdkazWeb('clanky', 'SDL');?> (Simple DirectMedia Layer). Nap�ete jedin� zdrojov� k�d a potom snadno p�elo��te program pro Windows&reg;, Linux, BSD, FreeBSD, Mac OS a spousty dal��ch opera�n�ch syst�m�. Pochopte jednu v�c: Sv�t nestoj� na Billov�ch Woknech&reg;. Slovo OpenGL je zkratkou ze slov Open Graphic Library. Open znamen� OTEV�EN�, �ili ka�d� m��e implementovat OpenGL a pokud spln� ur�it� krit�ria standardu (a zaplat� licen�n� poplatky), bude tato implementace pova�ov�na za plnohodnotn� OpenGL. Nezahazujte hlavn� rys OpenGL - jeho multiplatformnost a nez�vislost na programovac�m jazyku.</p>

<h3>OpenGL</h3>

<p>Nyn� se kone�n� pust�me do OpenGL, jste na n�j dostate�n� p�ipraveni. Asi ka�d� v�m �ekne, a� za�nete na <?OdkazWeb('tut_obsah', 'NeHe OpenGL Tutori�lech');?>. Dobr� rada, dr�te se j�. Nezapome�te ale, �e pouze �ten�m se programovat nenau��te. Mus�te hlavn� prakticky k�dovat. Paraleln� s NeHe Tutori�ly doporu�uji ��st �l�nek od Daniela �echa <a href="cl_gl_referat.pdf">Refer�t na praktikum z informatiky</a> (form�t PDF), kter� hodn� dob�e popisuje okolnosti vzniku a principy OpenGL, z�klady pr�ce s n�m, OpenGL datov� typy, pro� funkce za��naj� gl a na jejich konci b�v� 3f, jinde 3ub, a �pln� jinde 4d. �t�te i dal�� <?OdkazWeb('clanky', '�l�nky');?> a prohl�ejte ciz� zdrojov� k�dy. N�kolik program� od �esk�ch autor� naleznete <?OdkazWeb('programy', 'zde');?>, ale opravdu gigantick� mno�stv� jich je na <?OdkazBlank('http://nehe.gamedev.net/');?> nebo <?OdkazBlank('http://www.gametutorials.com/');?>. Pokud um�te anglicky, ciz� �l�nky ani zdrojov� k�dy nebudou probl�mem. �t�te diskuse na f�rech (nap�. <?OdkazBlank('http://www.builder.cz/');?>), naleznete na nich spoustu praktick�ch informac� a vy�e�en�ch probl�m�. Dal��m hodn� kvalitn�m zdrojem informac� t�m�� o v�em jsou Linuxov� manu�lov� str�nky ve Windows pak n�pov�da MSDN od Microsoftu. Existuj� offliny zab�raj�c� n�kolik CD, ale pokud se k nim nedostanete (b�vaj� p�ilo�eny k Visual Studiu), zkuste <?OdkazBlank('http://msdn.microsoft.com/');?>.</p>

<h3>Pom�hejte</h3>

<p>A� budete um�t OpenGL, pom�hejte ostatn�m. I vy jste na za��tku pot�ebovali pomoc. T�mto pom�h�n�m nemysl�m zrovna psan� �l�nk� pro tento web (nicm�n� i to m��ete :-), ale kdy� v�m n�kdo nap�e email i se za��te�nick�m dotazem, odpov�zte mu. Pokud nezn�te odpov��, zkuste ho nasm�rovat, kde by ji mohl naj�t. To sam� plat� i pro diskusn� f�ra. Poskytujte zdrojov� k�dy (nap�. pod licenc� <?OdkazBlank('http://www.gnu.cz/', 'GNU GPL');?>). Kdysi, kdy� jsem se poprv� do�etl o my�lence Linuxu a lidem okolo n�j, jsem pochopil, �e to nejlep�� na m�ch programech budou voln� p��stupn� zdrojov� k�dy. Bez nich by si jich s nejv�t�� pravd�podobnost� nikdo ani nev�iml. Mysl�te si, �e n�kdo bude z internetu stahovat 5MB dat, aby je za p�l hodiny smazal? Nebude. Pokud v�ak p�id�te zdrojov� k�dy, str�v� u nich t�eba t�den, nau�� se spoustu nov�ch v�c� a pravd�podobn� je doporu�� i dal��m lidem, aby se na n� pod�vali - nicm�n� z�le�� jen na v�s.</p>

<p class="autor">napsal: Michal Turek - Woq <?VypisEmail('WOQ@seznam.cz');?></p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li>Bez zdrojov�ch k�d�</li>
</ul>

<?
include 'p_end.php';
?>
