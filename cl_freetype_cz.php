<?
$g_title = 'CZ NeHe OpenGL - FreeType Fonty v OpenGL a �esky';
$g_description = 'NeHe OpenGL Tutori�ly v �e�tin�, programov�n� 3D grafiky, �l�nky, programy se zdrojov�mi k�dy...';
$g_keywords = 'opengl, nehe, tutori�ly, woq, programov�n�, 3D';

include 'p_begin.php';
?>

<h1>FreeType Fonty v OpenGL a �esky</h1>

<p class="nadpis_clanku">Chcete pou��vat ve sv�ch programech FreeType Fonty i s �esk�mi znaky? Pokud ano, jste na spr�vn�m m�st�. Tento �l�nek dopl�uje NeHe Tutori�l 43, ve kter�m bylo pops�no pou�it� FreeType s OpenGL, ale bohu�el bez �esk�ch znak�. Pou�ito s laskav�m svolen�m tvorbaher.bonusweb.cz .</p>

<p>Asi prvn�, co v�s napadne, aby k�d podporoval �esk� znaky, bude z�m�na datov�ho typu char za unsigned char a v�ude, kde se nach�z� ��slo 128, napsat 256. Bohu�el tato �vaha nen� spr�vn�, FreeType pou��v� k�dov�n�, ve kter�m se �esk� znaky nach�zej� na vy���ch indexech ne� 256. Z�chranou tedy bude typ wchar_t, kter� m� velikost 2 byty a je ur�en pr�v� pro takov� ��ely.</p>

<p>Hlavi�kov� soubor FreeType.h nech�me t�m�� nezm�n�n�. Pouze ��dek s using std::string; uprav�me na</p>

<p class="src0">using std::wstring;</p>

<p>d�ky �emu� budeme moci pou��vat 16-bitov� znaky.</p>

<p>Ve zdrojov�m souboru FreeType.cpp ud�l�me zm�n trochu v�ce. Nejd��ve p�ep�eme ve funkci make_dlist() char ch na wchar_t ch. D�le nahrad�me ��slo 128 za 383, kter� bude specifikovat po�et jednotliv�ch display list� pot�ebn�ch pro ulo�en� v�ech p�smen. Znak �, kter� se nach�z� v abeced� jako posledn�, pou��v� k�d 382, nezapome�te, �e pole v C/C++ za��n� od nuly. Konkr�tn� se jedn� o n�sleduj�c� ��dky</p>

<p class="src0">textures = new GLuint[383];</p>
<p class="src0">list_base = glGenLists(383);</p>
<p class="src0">glGenTextures(383, textures);</p>
<p class="src"></p>
<p class="src0">for(unsigned char i=0; i<383; i++)</p>
<p class="src1">...</p>
<p class="src"></p>
<p class="src0">glDeleteLists(list_base, 383);</p>
<p class="src0">glDeleteTextures(383, textures);</p>

<p>P�i renderingu textu v print() um�st�me za sekci</p>

<p class="src0">if (fmt == NULL)</p>
<p class="src1">*text=0;</p>
<p class="src0">else</p>
<p class="src0">{</p>
<p class="src1">va_start(ap, fmt);</p>
<p class="src1">vsprintf(text, fmt, ap);</p>
<p class="src1">va_end(ap);</p>
<p class="src0">}</p>

<p>cyklus, kter� konvertuje v�echny znaky do spr�vn�ho k�dov�n�. Znaky bez h��k� a ��rek z�stanou nezm�n�n�. Znaky s h��ky se nahrad� p�edepsan�m k�dem a znaky s ��rkami se zkonvertuj� z char (z�porn� hodnoty) na unsigned char. K�dy znak� s ��rkami jsou stejn� pro ob� k�dov�n�.</p>

<p class="src0">wchar_t NEWtext[256];</p>
<p class="src0">wchar_t znak;</p>
<p class="src"></p>
<p class="src0">for(int loop1=0; loop1<256; loop1++)</p>
<p class="src0">{</p>
<p class="src1">switch(text[loop1])<span class="kom">// V text je ulo�en ti�t�n� �et�zec, kter� je v p�vodn�m k�dov�n�</span></p>
<p class="src1">{</p>
<p class="src1">case '�':</p>
<p class="src2">znak=268;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=269;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=270;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=271;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=282;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=283;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=327;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=328;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=344;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=345;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=352;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=353;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=356;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=357;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=366;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=367;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=381;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '�':</p>
<p class="src2">znak=382;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">default :</p>
<p class="src2">znak=(unsigned char) text[loop1];</p>
<p class="src2">break;</p>
<p class="src1">}</p>
<p class="src"></p>
<p class="src1">NEWtext[loop1]=znak;</p>
<p class="src0">}</p>
<p class="src"></p>
<p class="src0"><span class="kom">// const char *start_line=text;// Tento ��dek zam�n�me za:</span></p>
<p class="src0">const wchar_t *start_line = NEWtext;<span class="kom">// P�i�azujeme p�edp�ipraven� �et�zec ve spr�vn�m k�dov�n�</span></p>

<p>Na n�sleduj�c�ch n�kolika ��dc�ch, podobn� jako v hlavi�kov�m souboru, zam�n�me t�ikr�t string za wstring. A tak� const char na const wchar_t a text za NEWtext.</p>

<p>Kv�li rozd�ln� velikosti char a wchar_t mus�me je�t� upravit glCallLists(lines[i].length(), GL_UNSIGNED_BYTE, lines[i].c_str()); na glCallLists(lines[i].length(), GL_UNSIGNED_SHORT, lines[i].c_str());. Typ wchar_t odpov�d� unsigned short int.</p>

<p>Nakonec nezapome�te p�idat k v�sledn�mu .EXE souboru �esk� font.</p>

<p class="autor">napsal: Luk� Beran - Berka <?VypisEmail('sysel001@seznam.cz');?>, 22.10.2004</p>

<h3 class="zdroj_kody_nadpis">Zdrojov� k�dy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_freetype_cz.rar');?> - Visual C++ projekt</li>
</ul>

<?
include 'p_end.php';
?>
