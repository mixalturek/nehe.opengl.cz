<?
$g_title = 'CZ NeHe OpenGL - Obsah NeHe OpenGL tutori�l�';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>NeHe OpenGL Tutori�ly</h1>

<?
function NeHe()
{
	for($i = 0; $i <= 48; $i++)
	{
		switch($i)
		{
			case 0:
				$tit='P�edmluva k NeHe Tutori�l�m';
				$napsal='Michal Turek - Woq';
				$prelozil='nen� p�ekladem';
				$text='Je�t� ne� se pust�te do �ten� tohoto textu, m�li byste v�d�t, �e nen� sou��st� ofici�ln�ch (anglick�ch) NeHe Tutori�l�. Napsal ho &quot;pouze&quot; jeden z p�ekladatel�, kter�mu chyb�lo n�co na zp�sob trochu ucelen�j��ho �vodu do tak obrovsk� problematiky, jakou p�edstavuje programov�n� 3D grafiky, her a ostatn� v�eho okolo OpenGL.';
				break;
			case 1:
				$tit='Vytvo�en� OpenGL okna ve Windows';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='V�clav Slov��ek - Wessan';
				$text='Nau��te se jak nastavit a vytvo�it OpenGL okno ve Windows. Program, kter� vytvo��te zobraz� &quot;pouze&quot; pr�zdn� okno. �ern� pozad� nevypad� nic moc, ale pokud porozum�te t�to lekci, budete m�t velmi dobr� z�klad pro jakoukoliv dal�� pr�ci. Zjist�te jak OpenGL pracuje, jak prob�h� vytv��en� okna a tak� jak napsat jednodu�e pochopiteln� k�d.';
				break;
			case 2:
				$tit='Vytv��en� troj�heln�k� a �ty��heln�k�';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='V�clav Slov��ek - Wessan';
				$text='Zdrojov� k�d z prvn� lekce trochu uprav�me, aby program vykreslil troj�heln�k a �tverec. V�m, �e si asi mysl�te, �e takov�to vykreslov�n� je banalita, ale a� za�nete programovat pochop�te, �e orientovat se ve 3D prostoru nen� na p�edstavivost a� tak jednoduch�. Jak�koli vytv��en� objekt� v OpenGL z�vis� na troj�heln�c�ch a �tverc�ch. Pokud pochop�te tuto lekci m�te nap�l vyhr�no.';
				break;
			case 3:
				$tit='Barvy';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='S jednoduch�m roz���en�m znalost� ze druh� lekce budete moci pou��vat barvy. Nau��te se jak ploch� vybarvov�n�, tak i barevn� p�echody. Barvy rozz��� vzhled aplikace a t�m sp�e zaujmou div�ka.';
				break;
			case 4:
				$tit='Rotace';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='Nau��me se, jak ot��et objekt okolo os. Troj�heln�k se bude ot��et kolem osy y a �tverec kolem osy x. Je jednoduch� vytvo�it sc�nu z polygon�. P�id�n� pohybu ji p�kn� o�iv�.';
				break;
			case 5:
				$tit='Pevn� objekty';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='Roz���en�m posledn� ��sti vytvo��me skute�n� 3D objekty. Narozd�l od 2D objekt� ve 3D prostoru. Zm�n�me troj�heln�k na pyramidu a �tverec na krychli. Pyramida bude vybarvena barevn�m p�echodem a ka�dou st�nu krychle vybarv�me jinou barvou.';
				break;
			case 6:
				$tit='Textury';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='Namapujeme bitmapov� obr�zek na krychli. Pou�ijeme zdrojov� k�dy z prvn� lekce, proto�e je jednodu�� (a p�ehledn�j��) za��t s pr�zdn�m oknem ne� slo�it� upravovat p�edchoz� lekci.';
				break;
			case 7:
				$tit='Texturov� filtry, osv�tlen�, ovl�d�n� pomoc� kl�vesnice';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Ji�� Rajsk� - RAJSOFT junior';
				$text='V tomto d�le se pokus�m vysv�tlit pou�it� t�� odli�n�ch texturov�ch filtr�. D�le pak pohybu objekt� pomoc� kl�vesnice a nakonec aplikaci jednoduch�ch sv�tel v OpenGL. Nebude se jako obvykle navazovat na k�d z p�edchoz�ho d�lu, ale za�ne se p�kn� od za��tku.';
				break;
			case 8:
				$tit='Blending';
				$napsal='Tom Stanis';
				$prelozil='Ji�� Rajsk� - RAJSOFT junior';
				$text='Dal�� typ speci�ln�ho efektu v OpenGL je blending, neboli pr�hlednost. Kombinace pixel� je ur�ena alfa hodnotou barvy a pou�itou funkc�. Nab�v�-li alfa 0.0f, materi�l zpr�hledn�, hodnota 1.0f p�in�� prav� opak.';
				break;
			case 9:
				$tit='Pohyb bitmap ve 3D prostoru';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Milan Turek';
				$text='Tento tutori�l v�s nau�� pohyb objekt� ve 3D prostoru a kreslen� bitmap bez �ern�ch m�st, zakr�vaj�c�ch objekty za nimi. Jednoduchou animaci a roz���en� pou�it� blendingu. Te� byste u� m�li rozum�t OpenGL velmi dob�e. Nau�ili jste se v�e od nastaven� OpenGL okna, po mapov�n� textur za pou�it� sv�tel a blendingu. To byl prvn� tutori�l pro st�edn� pokro�il�. A pokra�ujeme d�le...';
				break;
			case 10:
				$tit='Vytvo�en� 3D sv�ta a pohyb v n�m';
				$napsal='Lionel Brits - �etelgeuse';
				$prelozil='Ji�� Rajsk� - RAJSOFT junior &amp; Michal Turek - Woq';
				$text='Do sou�asnosti jsme programovali ot��ej�c� se kostku nebo p�r hv�zd. M�te (m�li byste m�t :-) z�kladn� pojem o 3D. Ale rotuj�c� krychle asi nejsou to nejlep�� k tvorb� dobr�ch deathmatchov�ch protivn�k�! Ne�ekejte a za�n�te s Quakem IV je�t� dnes! Tyto dny pot�ebujete k velk�mu, komplikovan�mu a dynamick�mu 3D sv�tu s pohybem do v�ech sm�r�, skv�l�mi efekty zrcadel, port�l�, deformacemi a t�eba tak� vysok�m frameratem. Tato lekce v�m vysv�tl� z�kladn� strukturu 3D sv�ta a pohybu v n�m.';
				break;
			case 11:
				$tit='Efekt vln�c� se vlajky';
				$napsal='Bosco';
				$prelozil='Michal Turek - Woq';
				$text='Nau��me se jak pomoc� sinusov� funkce animovat obr�zky. Pokud zn�te standardn� �et�i� Windows "L�taj�c� 3D objekty" (i on by m�l b�t programovan� v OpenGL), tak budeme d�lat n�co podobn�ho.';
				break;
			case 12:
				$tit='Display list';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Chcete v�d�t, jak urychlit va�e programy v OpenGL? Jste unaveni z nesmysln�ho opisov�n� ji� napsan�ho k�du? Nejde to n�jak jednodu�eji? Ne�lo by nap��klad jedn�m p��kazem vykreslit otexturovanou krychli? Samoz�ejm�, �e jde. Tento tutori�l je ur�en� speci�ln� pro v�s. P�edvytvo�en� objekty a jejich vykreslov�n� jedn�m ��dkem k�du. Jak snadn�...';
				break;
			case 13:
				$tit='Bitmapov� fonty';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='�asto kladen� ot�zka t�kaj�c� se OpenGL zn�: &quot;Jak zobrazit text?&quot;. V�dycky jde namapovat texturu textu. Bohu�el nad n�m m�te velmi malou kontrolu. A pokud nejste dob�� v blendigu, v�t�inou skon��te smixov�n�m s ostatn�mi obr�zky. Pokud byste cht�li zn�t leh�� cestu k v�stupu textu na jak�koli m�sto s libovolnou barvou nebo fontem, potom je tato lekce ur�it� pro v�s. Bitmapov� fonty jsou 2D p�sma, kter� nemohou b�t rotov�ny. V�dy je uvid�te zep�edu.';
				break;
			case 14:
				$tit='Outline fonty';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Bitmapov� fonty nesta��? Pot�ebujete kontrolovat pozici textu i na ose z? Cht�li byste fonty s hloubkou? Pokud zn� va�e odpov�� ano, pak jsou 3D fonty nejlep�� �e�en�. M��ete s nimi pohybovat na ose z a t�m m�nit jejich velikost, ot��et je, prost� d�lat v�e, co nem��ete s oby�ejn�mi. Jsou nejlep�� volbou ke hr�m a dem�m.';
				break;
			case 15:
				$tit='Mapov�n� textur na fonty';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Po vysv�tlen� bitmapov�ch a 3D font� v p�edchoz�ch dvou lekc�ch jsem se rozhodl napsat lekci o mapov�n� textur na fonty. Jedn� se o tzv. automatick� generov�n� koordin�t� textur. Po do�ten� t�to lekce budete um�t namapovat texturu opravdu na cokoli - zcela snadno a jednodu�e.';
				break;
			case 16:
				$tit='Mlha';
				$napsal='Christopher Aliotta - Precursor';
				$prelozil='Michal Turek - Woq';
				$text='Tato lekce roz�i�uje pou�it�m mlhy lekci 7. Nau��te se pou��vat t�� r�zn�ch filtr�, m�nit barvu a nastavit oblast p�soben� mlhy (v hloubce). Velmi jednoduch� a &quot;efektn�&quot; efekt.';
				break;
			case 17:
				$tit='2D fonty z textur';
				$napsal='Giuseppe D\'Agata &amp; Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V t�to lekci se nau��te, jak vykreslit font pomoc� texturou omapovan�ho obd�ln�ku. Dozv�te se tak�, jak pou��vat pixely m�sto jednotek. I kdy� nem�te r�di mapov�n� 2D znak�, najdete zde spoustu nov�ch informac� o OpenGL.';
				break;
			case 18:
				$tit='Kvadriky';
				$napsal='GB Schmick - TipTup';
				$prelozil='Michal Turek - Woq';
				$text='P�edstavuje se v�m b�je�n� sv�t kvadrik�. Jedn�m ��dkem k�du snadno vytv���te komplexn� objekty typu koule, disku, v�lce ap. Pomoc� matematiky a trochy pl�nov�n� lze snadno morphovat jeden do druh�ho.';
				break;
			case 19:
				$tit='��sticov� syst�my';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Cht�li jste u� n�kdy naprogramovat exploze, vodn� font�ny, planouc� hv�zdy a jin� skv�l� efekty, nicm�n� k�dov�n� ��sticov�ch syst�m� bylo bu� p��li� t�k� nebo jste v�bec nev�d�li, jak na to? V t�to lekci zjist�te, jak vytvo�it jednoduch�, ale dob�e vypadaj�c� ��sticov� syst�m. Extra p�id�me duhov� barvy a ovl�d�n� kl�vesnic�. Tak� se dozv�te, jak pomoc� triangle stripu jednodu�e vykreslovat velk� mno�stv� troj�heln�k�.';
				break;
			case 20:
				$tit='Maskov�n�';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='�ern� okraje obr�zk� jsme dosud o�ez�vali blendingem. A�koli je tato metoda efektivn�, ne v�dy transparentn� objekty vypadaj� dob�e. Modelov� situace: vytv���me hru a pot�ebujeme celistv� text nebo zak�iven� ovl�dac� panel, ale p�i blendingu sc�na prosv�t�. Nejlep��m �e�en�m je maskov�n� obr�zk�.';
				break;
			case 21:
				$tit='P��mky, antialiasing, �asov�n�, pravo�hl� projekce, z�kladn� zvuky a jednoduch� hern� logika';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Prvn� opravdu rozs�hl� tutori�l - jak u� plyne z gigantick�ho n�zvu. Doufejme, �e takov� spousta informac� a technik dok�e ud�lat ��astn�m opravdu ka�d�ho. Str�vil jsem dva dny k�dov�n�m a kolem dvou t�dn� psan�m tohoto HTML souboru. Pokud jste n�kdy hr�li hru Admiar, lekce v�s vr�t� do vzpom�nek. �kol hry sest�v� z vypln�n� jednotliv�ch pol��ek m��ky. Samoz�ejm� se mus�te vyh�bat v�em nep��tel�m.';
				break;
			case 22:
				$tit='Bump Mapping &amp; Multi Texturing';
				$napsal='Jens Schneider';
				$prelozil='V�clav Slov��ek - Wessan';
				$text='Prav� �as vr�tit se zp�tky na za��tek a za��t si opakovat. Nov��k�m v OpenGL se absolutn� nedoporu�uje! Pokud, ale m�te odvahu, m��ete zkusit dobrodru�stv� s nadupanou grafikou. V t�to lekci modifikujeme k�d z �est� lekce, aby podporoval hardwarov� multi texturing p�es opravdu skv�l� vizu�ln� efekt nazvan� bump mapping.';
				break;
			case 23:
				$tit='Mapov�n� textur na kulov� kvadriky';
				$napsal='GB Schmick - TipTup';
				$prelozil='Milan Turek';
				$text='Tento tutori�l je naps�n na b�zi <a href="tut_18.php">lekce 18</a>. V <a href="tut_15.php">lekci 15</a> (Mapov�n� textur na fonty) jsem psal o automatick�m mapov�n� textur. Vysv�tlil jsem jak m��eme poprosit OpenGL o automatick� generov�n� texturov�ch koordin�t�, ale proto�e lekce 15 byla celkem skromn�, rozhodl jsem se p�idat mnohem v�ce detail� o t�to technice.';
				break;
			case 24:
				$tit='V�pis OpenGL roz���en�, o�ez�vac� testy a textury z TGA obr�zk�';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V t�to lekci se nau��te, jak zjistit, kter� OpenGL roz���en� (extensions) podporuje va�e grafick� karta. Vyp�eme je do st�edu okna, se kter�m budeme moci po stisku �ipek rolovat. Pou�ijeme klasick� 2D texturov� font s t�m rozd�lem, �e texturu vytvo��me z TGA obr�zku. Jeho nejv�t��mi p�ednostmi jsou jednoduch� pr�ce a podpora alfa kan�lu. Odbour�n�m bitmap u� nebudeme muset inkludovat knihovnu glaux.';
				break;
			case 25:
				$tit='Morfov�n� objekt� a jejich nahr�v�n� z textov�ho souboru';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V t�to lekci se nau��te, jak nahr�t sou�adnice vrchol� z textov�ho souboru a plynulou transformaci z jednoho objektu na druh�. Nezam���me se ani tak na grafick� v�stup jako sp�e na efekty a pot�ebnou matematiku okolo. K�d m��e b�t velice jednodu�e modifikov�n k vykreslov�n� linkami nebo polygony.';
				break;
			case 26:
				$tit='Odrazy a jejich o�ez�v�n� za pou�it� stencil bufferu';
				$napsal='Banu Cosmin - Choko';
				$prelozil='Milan Turek &amp; Michal Turek - Woq';
				$text='Tutori�l demonstruje extr�mn� realistick� odrazy za pou�it� stencil bufferu a jejich o�ez�v�n�, aby &quot;nevystoupily&quot; ze zrcadla. Je mnohem v�ce pokrokov� ne� p�edchoz� lekce, tak�e p�ed za��tkem �ten� doporu�uji men�� opakov�n�. Odrazy objekt� nebudou vid�t nad zrcadlem nebo na druh� stran� zdi a budou m�t barevn� n�dech zrcadla - skute�n� odrazy.';
				break;
			case 27:
				$tit='St�ny';
				$napsal='Banu Cosmin - Choko &amp; Brett Porter';
				$prelozil='Michal Turek - Woq';
				$text='P�edstavuje se v�m velmi komplexn� tutori�l na vrh�n� st�n�. Efekt je doslova neuv��iteln�. St�ny se roztahuj�, oh�baj� a zahaluj� i ostatn� objekty ve sc�n�. Realisticky se pokrout� na st�n�ch nebo podlaze. Se v��m lze pomoc� kl�vesnice pohybovat ve 3D prostoru. Pokud je�t� nejste se stencil bufferem a matematikou jako jedna rodina, nem�te nejmen�� �anci.';
				break;
			case 28:
				$tit='Bezierovy k�ivky a povrchy, fullscreen fix';
				$napsal='David Nikdel';
				$prelozil='Michal Turek - Woq';
				$text='David Nikdel je osoba stoj�c� za t�mto skv�l�m tutori�lem, ve kter�m se nau��te, jak se vytv��ej� Bezierovy k�ivky. D�ky nim lze velice jednodu�e zak�ivit povrch a prov�d�t jeho plynulou animaci pouhou modifikac� n�kolika kontroln�ch bod�. Aby byl v�sledn� povrch modelu je�t� zaj�mav�j��, je na n�j namapov�na textura. Tutori�l tak� eliminuje probl�my s fullscreenem, kdy se po n�vratu do syst�mu neobnovilo p�vodn� rozli�en� obrazovky.';
				break;
			case 29:
				$tit='Blitter, nahr�v�n� .RAW textur';
				$napsal='Andreas L�ffler &amp; Rob Fletcher';
				$prelozil='V�clav Slov��ek - Wessan &amp; Michal Turek - Woq';
				$text='V t�to lekci se nau��te, jak se nahr�vaj� .RAW obr�zky a konvertuj� se do textur. Dozv�te se tak� o blitteru, grafick� metod� p�en�en� dat, kter� umo��uje modifikovat textury pot�, co u� byly nahr�ny do programu. M��ete j�m zkop�rovat ��st jedn� textury do druh�, blendingem je sm�chat dohromady a tak� roztahovat. Mali�ko uprav�me program tak, aby v dob�, kdy nen� aktivn�, v�bec nezat�oval procesor.';
				break;
			case 30:
				$tit='Detekce koliz�';
				$napsal='Dimitrios Christopoulos';
				$prelozil='Michal Turek - Woq';
				$text='Na podobn� tutori�l jste u� jist� netrp�liv� �ekali. Nau��te se z�klady o detekc�ch koliz�, jak na n� reagovat a na fyzice zalo�en� modelovac� efekty (n�razy, p�soben� gravitace ap.). Tutori�l se v�ce zam��uje na obecnou funkci koliz� ne� zdrojov�m k�d�m. Nicm�n� d�le�it� ��sti k�du jsou tak� pops�ny. Neo�ek�vejte, �e po prvn�m p�e�ten� �pln� v�emu z koliz� porozum�te. Je to komplexn� n�m�t, se kter�m v�m pomohu za��t.';
				break;
			case 31:
				$tit='Nahr�v�n� a renderov�n� model�';
				$napsal='Brett Porter';
				$prelozil='Michal Turek - Woq';
				$text='Dal�� skv�l� tutori�l! Nau��te se, jak nahr�t a zobrazit otexturovan� Milkshape3D model. Nezd� se to, ale asi nejv�ce se budou hodit znalosti o pr�ci s dynamickou pam�t� a jej�m kop�rov�n� z jednoho m�sta na druh�.';
				break;
			case 32:
				$tit='Picking, alfa blending, alfa testing, sorting';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V tomto tutori�lu se pokus�m zodpov�d�t n�kolik ot�zek, na kter� jsem denn� dotazov�n. Chcete v�d�t, jak p�i kliknut� tla��tkem my�i identifikovat OpenGL objekt nach�zej�c� se pod kurzorem (picking). D�le byste se cht�li dozv�d�t, jak vykreslit objekt bez zobrazen� ur�it� barvy (alfa blending, alfa testing). T�et� v�c�, se kterou si nev�te rady, je, jak �adit objekty, aby se p�i blendingu spr�vn� zobrazily (sorting). Naprogramujeme hru, na kter� si v�e vysv�tl�me.';
				break;
			case 33:
				$tit='Nahr�v�n� komprimovan�ch i nekomprimovan�ch obr�zk� TGA';
				$napsal='Evan Pipho - Terminate';
				$prelozil='Michal Turek - Woq';
				$text='V lekci 24 jsem v�m uk�zal cestu, jak nahr�vat nekomprimovan� 24/32 bitov� TGA obr�zky. Jsou velmi u�ite�n�, kdy� pot�ebujete alfa kan�l, ale nesm�te se starat o jejich velikost, proto�e byste je ihned p�estali pou��vat. K diskov�mu m�stu nejsou zrovna �etrn�. Probl�m velikosti vy�e�� nahr�v�n� obr�zk� komprimovan�ch metodou RLE. K�d pro loading a hlavi�kov� soubory jsou odd�leny od hlavn�ho projektu, aby mohly b�t snadno pou�ity i jinde.';
				break;
			case 34:
				$tit='Generov�n� ter�n� a krajin za pou�it� v��kov�ho mapov�n� textur';
				$napsal='Ben Humphrey - DigiBen';
				$prelozil='Michal Turek - Woq';
				$text='Cht�li byste vytvo�it v�rnou simulaci krajiny, ale nev�te, jak na to? Bude n�m sta�it oby�ejn� 2D obr�zek ve stupn�ch �edi, pomoc� kter�ho deformujeme rovinu do t�et�ho rozm�ru. Na prvn� pohled t�ko �e�iteln� probl�my b�vaj� �astokr�t velice jednoduch�.';
				break;
			case 35:
				$tit='P�ehr�v�n� videa ve form�tu AVI';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='P�ehr�v�n� AVI videa v OpenGL? Na pozad�, povrchu krychle, koule, �i v�lce, ve fullscreenu nebo v oby�ejn�m okn�. Co v�c si p��t...';
				break;
			case 36:
				$tit='Radial Blur, renderov�n� do textury';
				$napsal='Dario Corno - rIo';
				$prelozil='Michal Turek - Woq';
				$text='Spole�n�mi silami vytvo��me extr�mn� p�sobiv� efekt radial blur, kter� nevy�aduje ��dn� OpenGL roz���en� a funguje na jak�mkoli hardwaru. Nau��te se tak�, jak lze na pozad� aplikace vyrenderovat sc�nu do textury, aby pozorovatel nic nevid�l.';
				break;
			case 37:
				$tit='Cel-Shading';
				$napsal='Sami Hamlaoui - MENTAL';
				$prelozil='V�clav Slov��ek - Wessan &amp; Michal Turek - Woq';
				$text='Cel-Shading je druh vykreslov�n�, p�i kter�m v�sledn� modely vypadaj� jako ru�n� kreslen� karikatury z komiks� (cartoons). Rozli�n� efekty mohou b�t dosa�eny miniaturn� modifikac� zdrojov�ho k�du. Cel-Shading je velmi �sp�n�m druhem renderingu, kter� dok�e kompletn� zm�nit duch hry. Ne ale v�dy... mus� se um�t a pou��t s rozmyslem.';
				break;
			case 38:
				$tit='Nahr�v�n� textur z resource souboru &amp; texturov�n� troj�heln�k�';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='V�clav Slov��ek - Wessan';
				$text='Tento tutori�l jsem napsal pro v�echny z v�s, kte�� se m� v emailech dotazovali na to &quot;Jak m�m loadovat texturu ze zdroj� programu, abych m�l v�echny obr�zky ulo�en� ve v�sledn�m .exe souboru?&quot; a tak� pro ty, kte�� psali &quot;V�m, jak otexturovat obd�ln�k, ale jak mapovat na troj�heln�k?&quot; Tutori�l nen�, oproti jin�m, extr�mn� pokrokov�, ale kdy� nic jin�ho, tak se nau��te, jak skr�t va�e precizn� textury p�ed okem u�ivatele. A co v�c - budete moci trochu zt�it jejich kraden� :-)';
				break;
			case 39:
				$tit='�vod do fyzik�ln�ch simulac�';
				$napsal='Erkin Tunca';
				$prelozil='V�clav Slov��ek - Wessan';
				$text='V gravita�n�m poli se pokus�me rozpohybovat hmotn� bod s konstantn� rychlost�, hmotn� bod p�ipojen� k pru�in� a hmotn� bod, na kter� p�sob� gravita�n� s�la - v�e podle fyzik�ln�ch z�kon�. K�d je zalo�en na nejnov�j��m NeHeGL k�du.';
				break;
			case 40:
				$tit='Fyzik�ln� simulace lana';
				$napsal='Erkin Tunca';
				$prelozil='Michal Turek - Woq';
				$text='P�ich�z� druh� ��st dvoud�ln� s�rie o fyzik�ln�ch simulac�ch. Z�klady u� zn�me, a proto se pust�me do komplikovan�j��ho �kolu - kl�vesnic� ovl�dat pohyby simulovan�ho lana. Zat�hneme-li za horn� konec, prost�edn� ��st se rozhoupe a spodek se vl��� po zemi. Skv�l� efekt.';
				break;
			case 41:
				$tit='Volumetrick� mlha a nahr�v�n� obr�zk� pomoc� IPicture';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='V tomto tutori�lu se nau��te, jak pomoc� roz���en� EXT_fog_coord vytvo�it volumetrickou mlhu. Tak� zjist�te, jak pracuje IPicture k�d a jak ho m��ete vyu��t pro nahr�v�n� obr�zk� ve sv�ch vlastn�ch projektech. Demo sice nen� a� tak komplexn� jako n�kter� jin�, nicm�n� i p�esto vypad� hodn� efektn�.';
				break;
			case 42:
				$tit='V�ce viewport�';
				$napsal='Jeff Molofee - NeHe';
				$prelozil='Michal Turek - Woq';
				$text='Tento tutori�l byl naps�n pro v�echny z v�s, kte�� se cht�li dozv�d�t, jak do jednoho okna zobrazit v�ce pohled� na jednu sc�nu, kdy v ka�d�m prob�h� jin� efekt. Jako bonus p�id�m z�sk�v�n� velikosti OpenGL okna a velice rychl� zp�sob aktualizace textury bez jej�ho znovuvytv��en�.';
				break;
			case 43:
				$tit='FreeType Fonty v OpenGL';
				$napsal='Sven Olsen';
				$prelozil='Pavel Hradsk� a Michal Turek - Woq';
				$text='Pou�it�m knihovny FreeType Font rendering library m��ete snadno vypisovat vyhlazen� znaky, kter� vypadaj� mnohem l�pe ne� p�smena u bitmapov�ch font� z lekce 13. N� text bude m�t ale i jin� v�hody - bezprobl�mov� rotace, dobr� spolupr�ce s OpenGL vyb�rac�mi (picking) funkcemi a v�ce��dkov� �et�zce.';
				break;
			case 44:
				$tit='�o�kov� efekty';
				$napsal='Vic Hollis';
				$prelozil='Michal Turek - Woq';
				$text='�o�kov� efekty vznikaj� po dopadu paprsku sv�tla nap�. na objektiv kamery nebo fotoapar�tu. Pod�v�te-li se na z��i vyvolanou �o�kou, zjist�te, �e jednotliv� �tvary maj� jednu spole�nou v�c. Pozorovateli se zd�, jako by se v�echny pohybovaly skrz st�ed sc�ny. S t�mto na mysli m��eme osu z jednodu�e odstranit a vytv��et v�e ve 2D. Jedin� probl�m souvisej�c� s nep��tomnost� z sou�adnice je, jak zjistit, jestli se zdroj sv�tla nach�z� ve v�hledu kamery nebo ne. P�ipravte se proto na trochu matematiky.';
				break;
			case 45:
				$tit='Vertex Buffer Object (VBO)';
				$napsal='Paul Frazee';
				$prelozil='Michal Turek - Woq';
				$text='Jeden z nejv�t��ch probl�m� jak�koli 3D aplikace je zaji�t�n� jej� rychlosti. V�dy byste m�li limitovat mno�stv� aktu�ln� renderovan�ch polygon� bu� �azen�m, cullingem nebo n�jak�m algoritmem na sni�ov�n� detail�. Kdy� nic z toho nepom�h�, m��ete zkusit nap��klad vertex arrays. Modern� grafick� karty nab�zej� roz���en� nazvan� vertex buffer object, kter� pracuje podobn� jako vertex arrays krom� toho, �e nahr�v� data do vysoce v�konn� pam�ti grafick� karty, a tak podstatn� sni�uje �as pot�ebn� pro rendering. Samoz�ejm� ne v�echny karty tato nov� roz���en� podporuj�, tak�e mus�me implementovat i verzi zalo�enou na vertex arrays.';
				break;
			case 46:
				$tit='Fullscreenov� antialiasing';
				$napsal='Colt McAnlis - MainRoach';
				$prelozil='Michal Turek - Woq';
				$text='Cht�li byste, aby va�e aplikace vypadaly je�t� l�pe ne� doposud? Fullscreenov� vyhlazov�n�, naz�van� t� multisampling, by v�m mohlo pomoci. S v�hodou ho pou��vaj� ne-realtimov� renderovac� programy, nicm�n� s dne�n�m hardwarem ho m��eme dos�hnout i v re�ln�m �ase. Bohu�el je implementov�no pouze jako roz���en� ARB_MULTISAMPLE, kter� nebude pracovat, pokud ho grafick� karta nepodporuje.';
				break;
			case 47:
				$tit='CG vertex shader';
				$napsal='Owen Bourne';
				$prelozil='Michal Turek - Woq';
				$text='Pou��v�n� vertex a fragment (pixel) shader� ke &quot;�pinav� pr�ci&quot; p�i renderingu m��e m�t nespo�et v�hod. Nejv�ce je vid�t nap�. pohyb objekt� do te� v�hradn� z�visl� na CPU, kter� neb�� na CPU, ale na GPU. Pro psan� velice kvalitn�ch shader� poskytuje CG (p�im��en�) snadn� rozhran�. Tento tutori�l v�m uk�e jednoduch� vertex shader, kter� sice n�co d�l�, ale nebude p�edv�d�t ne nezbytn� osv�tlen� a podobn� slo�it�j�� nadstavby. Tak jako tak je p�edev��m ur�en pro za��te�n�ky, kte�� u� maj� n�jak� zku�enosti s OpenGL a zaj�maj� se o CG.';
				break;
			case 48:
				$tit='ArcBall rotace';
				$napsal='Terence J. Grant';
				$prelozil='Pavel Hradsk� a Michal Turek - Woq';
				$text='Nebylo by skv�l� ot��et modelem pomoc� my�i jednoduch�m drag &amp; drop? S ArcBall rotacemi je to mo�n�. Moje implementace je zalo�en� na my�lenk�ch Brettona Wadea a Kena Shoemakea. K�d tak� obsahuje funkci pro rendering toroidu - kompletn� i s norm�lami.';
				break;
			default:
				break;
		}

		$L = ($i < 10) ? '0'.$i : $i;

		echo "<div class=\"object\">\n";
		echo "<img src=\"images/nehe_tut/tut_$L.jpg\" alt=\"Lekce $i\" class=\"nehe_img_sm\" />\n";
		echo "<h3><a href=\"tut_$L.php\">Lekce $i - $tit</a></h3>\n";
		echo "<div>Napsal: $napsal</div>\n";
		echo "<div>P�elo�il: $prelozil</div>\n";
		echo "<p>$text</p>\n";
		echo "</div>\n\n";
	}
}

NeHe();// Vol�n� funkce
?>

<?
include 'p_end.php';
?>
