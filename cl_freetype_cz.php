<?
$g_title = 'CZ NeHe OpenGL - FreeType Fonty v OpenGL a èesky';
$g_description = 'NeHe OpenGL Tutoriály v èe¹tinì, programování 3D grafiky, èlánky, programy se zdrojovými kódy...';
$g_keywords = 'opengl, nehe, tutoriály, woq, programování, 3D';

include 'p_begin.php';
?>

<h1>FreeType Fonty v OpenGL a èesky</h1>

<p class="nadpis_clanku">Chcete pou¾ívat ve svých programech FreeType Fonty i s èeskými znaky? Pokud ano, jste na správném místì. Tento èlánek doplòuje NeHe Tutoriál 43, ve kterém bylo popsáno pou¾ití FreeType s OpenGL, ale bohu¾el bez èeských znakù. Pou¾ito s laskavým svolením tvorbaher.bonusweb.cz .</p>

<p>Asi první, co vás napadne, aby kód podporoval èeské znaky, bude zámìna datového typu char za unsigned char a v¹ude, kde se nachází èíslo 128, napsat 256. Bohu¾el tato úvaha není správná, FreeType pou¾ívá kódování, ve kterém se èeské znaky nacházejí na vy¹¹ích indexech ne¾ 256. Záchranou tedy bude typ wchar_t, který má velikost 2 byty a je urèen právì pro takové úèely.</p>

<p>Hlavièkový soubor FreeType.h necháme témìø nezmìnìný. Pouze øádek s using std::string; upravíme na</p>

<p class="src0">using std::wstring;</p>

<p>díky èemu¾ budeme moci pou¾ívat 16-bitové znaky.</p>

<p>Ve zdrojovém souboru FreeType.cpp udìláme zmìn trochu více. Nejdøíve pøepí¹eme ve funkci make_dlist() char ch na wchar_t ch. Dále nahradíme èíslo 128 za 383, které bude specifikovat poèet jednotlivých display listù potøebných pro ulo¾ení v¹ech písmen. Znak ¾, který se nachází v abecedì jako poslední, pou¾ívá kód 382, nezapomeòte, ¾e pole v C/C++ zaèíná od nuly. Konkrétnì se jedná o následující øádky</p>

<p class="src0">textures = new GLuint[383];</p>
<p class="src0">list_base = glGenLists(383);</p>
<p class="src0">glGenTextures(383, textures);</p>
<p class="src"></p>
<p class="src0">for(unsigned char i=0; i<383; i++)</p>
<p class="src1">...</p>
<p class="src"></p>
<p class="src0">glDeleteLists(list_base, 383);</p>
<p class="src0">glDeleteTextures(383, textures);</p>

<p>Pøi renderingu textu v print() umístíme za sekci</p>

<p class="src0">if (fmt == NULL)</p>
<p class="src1">*text=0;</p>
<p class="src0">else</p>
<p class="src0">{</p>
<p class="src1">va_start(ap, fmt);</p>
<p class="src1">vsprintf(text, fmt, ap);</p>
<p class="src1">va_end(ap);</p>
<p class="src0">}</p>

<p>cyklus, který konvertuje v¹echny znaky do správného kódování. Znaky bez háèkù a èárek zùstanou nezmìnìné. Znaky s háèky se nahradí pøedepsaným kódem a znaky s èárkami se zkonvertují z char (záporné hodnoty) na unsigned char. Kódy znakù s èárkami jsou stejné pro obì kódování.</p>

<p class="src0">wchar_t NEWtext[256];</p>
<p class="src0">wchar_t znak;</p>
<p class="src"></p>
<p class="src0">for(int loop1=0; loop1<256; loop1++)</p>
<p class="src0">{</p>
<p class="src1">switch(text[loop1])<span class="kom">// V text je ulo¾en ti¹tìný øetìzec, který je v pùvodním kódování</span></p>
<p class="src1">{</p>
<p class="src1">case 'È':</p>
<p class="src2">znak=268;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'è':</p>
<p class="src2">znak=269;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'Ï':</p>
<p class="src2">znak=270;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'ï':</p>
<p class="src2">znak=271;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'Ì':</p>
<p class="src2">znak=282;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'ì':</p>
<p class="src2">znak=283;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'Ò':</p>
<p class="src2">znak=327;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'ò':</p>
<p class="src2">znak=328;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'Ø':</p>
<p class="src2">znak=344;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'ø':</p>
<p class="src2">znak=345;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '©':</p>
<p class="src2">znak=352;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '¹':</p>
<p class="src2">znak=353;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '«':</p>
<p class="src2">znak=356;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '»':</p>
<p class="src2">znak=357;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'Ù':</p>
<p class="src2">znak=366;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case 'ù':</p>
<p class="src2">znak=367;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '®':</p>
<p class="src2">znak=381;</p>
<p class="src2">break;</p>
<p class="src"></p>
<p class="src1">case '¾':</p>
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
<p class="src0"><span class="kom">// const char *start_line=text;// Tento øádek zamìníme za:</span></p>
<p class="src0">const wchar_t *start_line = NEWtext;<span class="kom">// Pøiøazujeme pøedpøipravený øetìzec ve správném kódování</span></p>

<p>Na následujících nìkolika øádcích, podobnì jako v hlavièkovém souboru, zamìníme tøikrát string za wstring. A také const char na const wchar_t a text za NEWtext.</p>

<p>Kvùli rozdílné velikosti char a wchar_t musíme je¹tì upravit glCallLists(lines[i].length(), GL_UNSIGNED_BYTE, lines[i].c_str()); na glCallLists(lines[i].length(), GL_UNSIGNED_SHORT, lines[i].c_str());. Typ wchar_t odpovídá unsigned short int.</p>

<p>Nakonec nezapomeòte pøidat k výslednému .EXE souboru èeský font.</p>

<p class="autor">napsal: Luká¹ Beran - Berka <?VypisEmail('sysel001@seznam.cz');?>, 22.10.2004</p>

<h3 class="zdroj_kody_nadpis">Zdrojové kódy</h3>

<ul class="zdroj_kody">
<li><?OdkazDown('download/clanky/cl_freetype_cz.rar');?> - Visual C++ projekt</li>
</ul>

<?
include 'p_end.php';
?>
