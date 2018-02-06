<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package helpers
 * 
 */
class Helper_TBDev_PDF extends Art_Abstract_Helper {
	
	const RESOURCE_CONTRACT = 'tbdevelopment_smlouva_';
	const RESOURCE_EXT_PDF	= '.pdf';
	
	static protected function _includeStyle( $path )
	{
		$path = 'files/pdf_styles/'.$path;
		if( file_exists($path) )
		{
			return '<style>'.file_get_contents($path).'</style>';
		}
		else
		{
			return null;
		}
	}
	
	
	/**
	 *	Generates a registration doc for natural person
	 * 
	 *	@param string $titul_jmeno_prijmeni
	 *	@param int $klientske_cislo
	 *	@param string $datum_narozeni
	 *	@param string $adresa_trvaleho_pobytu
	 *	@param string $kontaktni_adresa
	 *	@param string $email
	 *	@param string $tel
	 *	@param string $datum_vystaveni
	 *	@return Art_Model_Resource_Db
	 */
	static function registrationDocForPerson($titul_jmeno_prijmeni, $klientske_cislo, $datum_narozeni, $adresa_trvaleho_pobytu, $kontaktni_adresa, $email, $tel, $datum_vystaveni = NULL)
	{
		if( NULL === $datum_vystaveni )
		{
			$datum_vystaveni = date('j.n.Y');
		}
		
		$filename = static::RESOURCE_CONTRACT.Art_Filter::urlName($klientske_cislo).static::RESOURCE_EXT_PDF;
		$filehash = rand_str();
		$filepath = 'files/pdf/'.$filehash.static::RESOURCE_EXT_PDF;
		
		if( !file_exists( dirname($filepath) ) )
		{
			mkdir(dirname($filepath), 0777, true );
		}
		
		
		$pdf = Art_PDF::newFile(); /*@var $pdf Art_TCPDF */
		$pdf->setHtmlFooter('');
		$pdf->SetTitle('TB Development - smlouva');
		$pdf->SetSubject('TB Development - smlouva');
		$pdf->SetKeywords('TB Development, smlouva');
		
		$pdf->AddPage();
		$body = static::_includeStyle('style_body2.css');
		$body .= '
			<h1>PŘIHLÁŠKA</h1>
			<h2>do Spolku přátel při TBDI</h3>
			<br>&nbsp;
			<h3>Identifikační údaje uchazeče:</h3>
			<table>
				<tr>
					<td>(Titul), jméno a příjmení:</td>
					<td>'.$titul_jmeno_prijmeni.'</td>
				</tr>
				<tr class="height10px">
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Datum narození:</td>
					<td>'.$datum_narozeni.'</td>
				</tr>
				<tr class="height10px">
					<td></td>
					<td></td>
				</tr>       
				<tr>
					<td>Adresa trvalého pobytu:</td>
					<td>'.$adresa_trvaleho_pobytu.'</td>
				</tr>
				<tr class="height10px">
					<td></td>
					<td></td>
				</tr>        
				<tr>
					<td>Kontaktní adresa<sup>1</sup>:</td>
					<td>'.$kontaktni_adresa.'</td>
				</tr>
				<tr class="height10px">
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>E-mailový a tel. kontakt:</td>
					<td>'.$email.', '.$tel.'</td>
				</tr>
			</table>
			<br>

			<p>Níže podepsaný uchazeč tímto žádá o přijetí za člena Spolku přátel při TBDI, IČ: 039 35 175, sídlem Třída generála Píky 13, 613 00 Brno - Černá pole (dále též pouze „spolek“) a projevuje vůli být vázán stanovami spolku ode dne, kdy se stane jeho členem. Uchazeč prohlašuje, že si před podpisem žádosti stanovy spolku přečetl a souhlasí s nimi.</p>
			<p>Podáním této přihlášky uděluje uchazeč souhlas spolku, aby v případě přijetí za člena nakládal pro potřeby chodu spolku s výše uvedenými osobními údaji uchazeče, které uchazeč poskytl nebo poskytne ve smyslu zákona o ochraně osobních údajů.</p>
			<p>Před podpisem přihlášky byl uchazeč srozuměn s výší členského poplatku, který jsou členové povinni pravidelně hradit:</p>
			<p>1200 Kč/rok trvání členství (nikoli rok kalendářní), se splatností předem. První členský poplatek je splatný v den přijetí za člena. Uchazeč si je vědom, že poplatek se hradí za samo členství bez ohledu na to, v jaké míře jej jako člen využíval nebo jaké aktivity spolek v konkrétním období vyvinul. Každý další členský poplatek je splatný prvním dnem příslušného roku trvání členství (nikoli roku kalendářního)<sup>2.</sup></p>
			<p>Členské poplatky je možno hradit bankovním převodem na účet spolku, č. ú.: 2700785944/2010 Jako VS se užívá členské číslo uvedené dále na druhé straně, aby bylo možno platbu identifikovat. Přípustné jsou i platby v hotovosti k rukám předsedy spolku nebo jím pověřené osoby.</p>

			<p>&nbsp;
			<br>
			<br>
			<br>
			<br>
			<br></p>
			<p class="floatLeft">V Brně dne '.$datum_vystaveni.'</p>

			<div align="right">……………………………………<br>podpis uchazeče &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
			</div>


			<p class="floatClear"><small>1 Pokud se liší od adresy trvalého pobytu<br>2 Příklad: Členství vznikne 5. 5. 2015, první poplatek je splatný dne 5. 5. 2015. První rok trvání členství uplyne dne 5. 5. 2016. Poplatek na další rok trvání členství je splatný dne 6. 5. 2016 jako prvního dne dalšího roku trvání členství, další poplatek je splatný dne 6. 5. 2017 atd.</small></p>
		';

		$pdf->writeHTML($body, true, false, true, false, '');
		
		$pdf->AddPage();
		$body = static::_includeStyle('style_body2.css');
		$body .= '
			<table>
				<tr>
					<td width="48%">
					<p>
						<span align="center">
						<strong>SPOLEK PŘÁTEL PŘI TBDI<br>
						<u>OBECNÁ PRAVIDLA ĆERPÁNÍ VÝHOD</u><br><br>
						<small>I.<br>
						Úvod</small></strong><br>
						</span>
						<span class="small-text">
						1)	Spolek mimo jiné zprostředkovaně umožňuje svým členům v souladu s čl. X. odst. 3) stanov spolku čerpání výhod spočívajících v možnosti odebírat různé zboží nebo služby za zvýhodněné ceny od dodavatelů dle nabídek uváděných na webových stránkách spolku (aktuálně club.tbdevelopment.cz, ale nevylučuje se, že v budoucnu může dojít ke změně domény), kdy tyto nabídky si členové mohou zobrazit po přihlášení do systému za užití členských přihlašovacích údajů. Tedy, výhoda spočívá v dojednání zvýhodněných cen a v zajištění nebo zorganizování čerpání daného zboží nebo služeb od dodavatelů za tyto zvýhodněné ceny.
						</span>
						</p>
						<p class="small-text">
						2)	Výše identifikovaný člen, jakožto současný nebo budoucí člen spolku, má nebo bude mít oprávnění po dohodě se spolkem těchto výhod čerpat.
						</p>
						<p class="small-text">
						3)	Tato pravidla stanovují bližší práva a povinnosti člena při čerpání výhod v podobě možnosti odebírat zboží a služby od dodavatelů za zvýhodněné ceny.  Jedná se o pravidla obecná, bližší práva a povinnosti jsou dále rozvedeny u každé nabídky na zvýhodněné odebírání konkrétního zboží nebo služby na webových stránkách spolku. V případě přímého rozporu mají přednost pravidla stanovená u konkrétní nabídky.
						</p>
						
						<p>
						<span align="center">
							<small><strong>II.<br>
							Systém zajišťování a čerpání výhod</strong></small><br>
						</span>
						<span class="small-text">
						1)	Při sjednávání odběru služeb nebo zboží od dodavatelů za zvýhodněné ceny spolek přímo spolupracuje s obchodní společností TB development&investment, s.r.o., IČ: 02595354, sídlem Třída generála Píky 13, 613 00 Brno - Černá pole.
						</span></p>
						<p class="small-text">2)	Systém čerpání výhod v podobě možnosti odebírat od dodavatelů různé zboží a služby za zvýhodněné služby funguje následovně (není – li dále uvedeno jinak): </p>
						<p class="small-text">a)	Člen si na webových stránkách spolku zvolí konkrétní nabídku, tj. zboží nebo služby od konkrétního dodavatele, které chce čerpat za tam popsanou zvýhodněnou cenu a zašle objednávku spolku (typicky e-mailem). Spolek (zpravidla v osobě předsedy spolku) požadavek člena zpracuje a zařídí u společnosti TB development&investment, s.r.o., aby požadované zboží nebo požadovanou službu smluvně zajistila u příslušného dodavatele. Společnost TB development&investment, s.r.o., je tedy tím, kdo je v přímém smluvním vztahu s dodavateli zboží a služeb. Tito dodavatelé jsou písemně či ústně zpraveni o tom, že koncovými a faktickými odběrateli zboží a služeb jsou spolek a jeho členové, kterým tyto služby a zboží společnost zpřístupní, což je jednou z podmínek poskytování zvýhodněných cen (tzn. úzký okruh osob).</p>
						<p class="small-text">b)	Úhrada ceny za obdržené zboží a čerpané služby se následně provádí tak, že dodavatel služby nebo zboží požaduje úhradu po svém smluvním partnerovi, tj. po společnosti TB development&investment, s.r.o., která pochopitelně danou úhradu na základě ústního smluvního vztahu mezi ní a spolkem žádá po spolku, neboť pro něj a jeho členy dané zboží a služby zajišťovala. <strong><u>Cenu za odebrané zboží a čerpané služby spolek vyúčtuje členovi, který je povinen tuto cenu spolku uhradit.</u></strong></p>
						<p class="small-text">3)	Člen výše popsaný systém bere na vědomí, souhlasí s ním a zavazuje se dodržovat svoje platební povinnosti vůči spolku.</p>
						<p class="small-text">4)	Sjednává se a člen bere na vědomí, že spolek je oprávněn kdykoli ukončit zajišťování konkrétní výhody, ať již z důvodu změny postoje dodavatele, zániku smluvního vztahu dodavatele se společností TB development&investment, s.r.o., nebo i z jiných, třeba i neuvedených důvodů. Nejedná se z pohledu spolku o výdělečnou činnost a spolek není podnikatelem. Svoji snahu a činnost ve prospěch členů vyvíjí na dobrovolné bázi, tudíž po něm nelze žádat zajišťování výhod </p>

					</td>
					<td width="4%">&nbsp;</td>
					<td width="48%">
						<p class="small-text">v nárokové podobě. Pokud naopak z nabídky vyplývá, že člen je povinen čerpat určitou výhodu (zboží/služba) nejméně po určitou dobu, je tím člen vázán.</p>
						<p><span align="center"><small><strong>III.<br>
Způsob vyúčtování, bližší platební povinnosti člena<br></strong></small></span>
<span class="small-text">1)	Spolek vyzve člena k úhradě zpravidla prostřednictvím e-mailové zprávy na elektronickou adresu člena nebo písemně. Člen je povinen hradit ty služby a to zboží, které žádal, ať již pro sebe, nebo pro další jím označené osoby (typicky dle čl. X. odst. 4 stanov spolku). Tyto služby a zboží spolek členovi vyúčtuje na základě údajů získaných od společnosti TB development&investment, s.r.o.</span></p>
						<p class="small-text">2)	Pokud není v pravidlech pro konkrétní nabídku uvedeno jinak, předkládá spolek členovi vyúčtování vždy měsíčně zpětně. Člen je povinen uhradit spolkem vyúčtovanou cenu zboží nebo služeb vždy bezodkladně, nejpozději však ve lhůtě stanovené spolkem. POZOR, společnost TB development&investment, s.r.o., smí pro zjednodušení procesu sama přímou cestou předkládat vyúčtování členovi a žádat úhradu. Pokud k tomu dojde, bere člen na vědomí, že se tak děje po dohodě mezi touto společností a spolkem.</p>
						<p class="small-text">3)	Člen bere na vědomí, že včasnost úhrady z jeho strany je velmi důležitá pro to, aby nedošlo k prodlení s úhradou ceny zboží nebo služeb dodavateli jako takovému, který by následně mohl požadovat smluvní pokuty nebo zákonné úroky z prodlení. To by představovalo škodu, za kterou by v konečném důsledku člen odpovídal, což bere na vědomí. Teprve poté, co člen úhradu ve prospěch spolku provede, může být tato přeposlána dodavateli (přímo, nebo prostřednictvím společnosti TB developlment&investment, s.r.o.). Nelze žádat, aby za člena tuto cenu nejprve hradil spolek s dodatečným vyzváním k úhradě, byť se k tomu může spolek dobrovolně rozhodnout.</p>
						<p class="small-text">4)	Člen bere na vědomí, že prodlení s úhradou vyúčtované ceny zboží nebo služeb může spolku způsobit vážné komplikace. Zavazuje se tedy své platební povinnosti plnit vždy řádně a včas a sjednává se, že porušení této povinnosti se považuje za přímý důvod k vyloučení člena ze spolku. I když k vyloučení člena nedojde, má vždy spolek (a skrze pokyn spolku společnost TB development&investment, s.r.o.) právo dočasně nebo trvale ukončit poskytování nebo zajišťování veškerých výhod členovi a dalším osobám, které výhod čerpají spolu se členem.</p>
						<p>
							<span align="center">
							<strong><small>IV.<br>
							Další a závěrečná ujednání<br></small></strong></span>
							<span class="small-text">
							1)	Člen bere na vědomí, že je oprávněn čerpat zde popisovaných výhod výhradně v době jeho trvání členství ve spolku. Poté toto právo zaniká, tudíž mu nadále nebudou zajišťovány předmětné služby ani zboží. To platí i pro osoby, na které oprávnění čerpání výhod člen v souladu s ustanovením čl. X. odst. 4) stanov po dohodě se spolkem rozšířil, tzn. i u těchto osob oprávnění zaniká nejpozději s okamžikem zániku členství člena.
							</span>
						</p>
						<p class="small-text">2)	Tím, že člen požádá spolek o možnost čerpat určitou službu nebo odebírat určité zboží, dává najevo, že si řádně přečetl též pravidla uvedená u konkrétní nabídky na webových stránkách spolku, souhlasí s nimi a bude se jimi řídit ve spojení s touto dohodou o obecných pravidlech čerpání výhod.</p>
						<p class="small-text">3)	Na jiné právní vztahy, nežli ty, které se týkají čerpání výhod v podobě odběru zboží nebo služeb od dodavatelů za zvýhodněné ceny, se tato pravidla nepoužijí, tj. tato pravidla nemají ambice obsáhnout všechny oblasti činnosti spolku, týkají se výhradně té části činnosti spolku mající za cíl zpřístupnit členům slevy na trhu zboží a služeb (telefonní tarify, pohonné hmoty, dodávky energií apod.). Tato pravidla se z povahy věci neuplatní, pokud člen uzavře sám smlouvu přímo s dodavatelem.</p>
						<p class="small-text">4)	Tato pravidla se uplatní i tehdy, pokud v době uzavření této dohody člen službu již čerpá nebo zboží již odebírá. Práva a povinnosti vzniklá před podpisem této dohody zůstávají nedotčena.</p>
					</td>
				</tr>
			</table>';
		
		$pdf->writeHTML($body, true, false, true, false, '');
		
		$pdf->AddPage();
		$body = static::_includeStyle('style_body2.css');
		$body .= '
			<h3>Rozhodnutí předsedy spolku:</h3>

			<p><strong>Předseda spolku tímto přijímá uchazeče za člena spolku.</strong> Člen je nyní zavázán plněním povinností dle stanov. Předseda spolku předává novému členovi přihlašovací údaje na webové stránky spolku.</p> 
			<p>Členství je uděleno na dobu neurčitou, zaniknout může pouze za podmínek uvedených ve stanovách nebo za podmínek dle zákona. Uděluje se členské číslo:</p> <div align="center">'.$klientske_cislo.'</div>
			<p>E-mailový kontakt předsedy spolku: spunar@tbdevelopment.cz. Případná změna bude oznámena prostřednictvím webových stránek nebo e-mailové zprávy.</p>

			<br>
			<p class="floatLeft">V Brně dne .........................</p>
			<br>
			<div align="right">……………………………………………<br>
			Spolek přátel při TBDI &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;<br>
			podepsán předseda spolku &nbsp; &nbsp; &nbsp; &nbsp;
			<br>
			</div>

			<p>Uchazeč, jakožto nový člen, níže svým podpisem stvrzuje, že mu byly předány přihlašovací údaje na webové stránky spolku. Současně stvrzuje, že souhlasí se vším, co bylo uvedeno výše ze strany předsedy spolku.</p>
			<p>Uchazeč, jakožto nový člen, prohlašuje, že byl výslovně poučen o významu webových stránek spolku: club.tbdevelopment.cz, které dle stanov mohou sloužit k vyvěšení pozvánky na zasedání členské schůze, k předložení návrhu usnesení členské schůzi k přijetí bez osobního zasedání apod. Současně byl člen upozorněn na povinnost pravidelně kontrolovat svou e-mailovou schránku, která má obdobný význam.</p>
			<p>Současně stvrzuje, že mu bylo předáno jedno vyhotovení tohoto rozhodnutí o přijetí za člena.</p>

			<br>
			<p class="floatLeft">V Brně dne ........................</p>
			<br>
			<div align="right">………………………………………………<br>
			podpis uchazeče jako nového člena
			<br>
			</div>
		';
		$pdf->writeHTML($body, true, false, true, false, '');

		$pdf->AddPage();
		$body = static::_includeStyle('style_body2.css');
		$body .= '
			<h3>Rozhodnutí předsedy spolku:</h3>

			<p><strong>Předseda spolku tímto přijímá uchazeče za člena spolku.</strong> Člen je nyní zavázán plněním povinností dle stanov. Předseda spolku předává novému členovi přihlašovací údaje na webové stránky spolku.</p> 
			<p>Členství je uděleno na dobu neurčitou, zaniknout může pouze za podmínek uvedených ve stanovách nebo za podmínek dle zákona. Uděluje se členské číslo:</p><div align="center">'.$klientske_cislo.'</div>
			<p>E-mailový kontakt předsedy spolku: spunar@tbdevelopment.cz. Případná změna bude oznámena prostřednictvím webových stránek nebo e-mailové zprávy.</p>

			<br>
			<p class="floatLeft">V Brně dne .........................</p>
			<br>
			<div align="right">……………………………………………<br>
			Spolek přátel při TBDI &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;<br>
			podepsán předseda spolku &nbsp; &nbsp; &nbsp; &nbsp;
			<br>
			</div>

			<p>Uchazeč, jakožto nový člen, níže svým podpisem stvrzuje, že mu byly předány přihlašovací údaje na webové stránky spolku. Současně stvrzuje, že souhlasí se vším, co bylo uvedeno výše ze strany předsedy spolku.</p>
			<p>Uchazeč, jakožto nový člen, prohlašuje, že byl výslovně poučen o významu webových stránek spolku: club.tbdevelopment.cz, které dle stanov mohou sloužit k vyvěšení pozvánky na zasedání členské schůze, k předložení návrhu usnesení členské schůzi k přijetí bez osobního zasedání apod. Současně byl člen upozorněn na povinnost pravidelně kontrolovat svou e-mailovou schránku, která má obdobný význam.</p>
			<p>Současně stvrzuje, že mu bylo předáno jedno vyhotovení tohoto rozhodnutí o přijetí za člena.</p>

			<br>
			<p class="floatLeft">V Brně dne ........................</p>
			<br>
			<div align="right">………………………………………………<br>
			podpis uchazeče jako nového člena
			<br>
			</div>
		';
		$pdf->writeHTML($body, true, false, true, false, '');
		
		$pdf->Output($filepath);
		
		$rsrc = new Art_Model_Resource_Db;
		$rsrc->hash = $filehash;
		$rsrc->name = $filename;
		$rsrc->path = $filepath;
		$rsrc->size = filesize($filepath);
		$rsrc->rights_read = 0;
		$rsrc->rights_write = 0;
		$rsrc->save();
		
		return $rsrc;
	}
	
	
	/**
	 *	Generates a registration doc for legal person (company)
	 * 
	 *	@param string $nazev_firmy
	 *	@param int	$klientske_cislo
	 *	@param int	$ico
	 *	@param string $adresa_sidla
	 *	@param string $kontaktni_adresa
	 *	@param string $zastupce_titul_jmeno_prijmeni
	 *	@param string $funkce_zastupce
	 *	@param string $email
	 *	@param string $tel
	 *	@param string $opravnena_osoba
	 *	@param int	$poplatek_za_clenstvi
	 *	@param string $datum_vystaveni
	 *	@return Art_Model_Resource_Db
	 */
	static function registrationDocForCompany($nazev_firmy, $klientske_cislo, $ico, $adresa_sidla, $kontaktni_adresa, $zastupce_titul_jmeno_prijmeni, $funkce_zastupce, $email, $tel, $opravnena_osoba, $poplatek_za_clenstvi, $datum_vystaveni = NULL)
	{
		if( NULL === $datum_vystaveni )
		{
			$datum_vystaveni = date('j.n.Y');
		}
		
		$filename = static::RESOURCE_CONTRACT.Art_Filter::urlName($klientske_cislo).static::RESOURCE_EXT_PDF;
		$filehash = rand_str();
		$filepath = 'files/pdf/'.$filehash.static::RESOURCE_EXT_PDF;
		
		if( !file_exists( dirname($filepath) ) )
		{
			mkdir(dirname($filepath), 0777, true );
		}
		
		
		$pdf = Art_PDF::newFile(); /*@var $pdf Art_TCPDF */
		$pdf->setHtmlFooter('');
		$pdf->SetTitle('TB Development - smlouva');
		$pdf->SetSubject('TB Development - smlouva');
		$pdf->SetKeywords('TB Development, smlouva');
		
		$pdf->AddPage();
		$body = static::_includeStyle('style_body2.css');
		$body .= '
			<h1>PŘIHLÁŠKA</h1>
			<h2>do Spolku přátel při TBDI</h3>
			<h3>Identifikační údaje uchazeče:</h3>
			<table>
				<tr>
					<td>Název:</td>
					<td>'.$nazev_firmy.'</td>
				</tr>
				<tr class="height10px">
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>IČO:</td>
					<td>'.$ico.'</td>
				</tr>
				<tr class="height10px">
					<td></td>
					<td></td>
				</tr>       
				<tr>
					<td>Adresa sídla:</td>
					<td>'.$adresa_sidla.'</td>
				</tr>
				<tr class="height10px">
					<td></td>
					<td></td>
				</tr>        
				<tr>
					<td>Kontaktní adresa<sup>1</sup>:</td>
					<td>'.$kontaktni_adresa.'</td>
				</tr>
				<tr class="height10px">
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Zastoupen:</td>
					<td>'.$zastupce_titul_jmeno_prijmeni.', '.$funkce_zastupce.'</td>
				</tr>
				<tr class="height10px">
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>E-mailový a tel. kontakt:</td>
					<td>'.$email.', '.$tel.'</td>
				</tr>
			</table>
			
			
			<p>Níže podepsaný uchazeč tímto žádá o přijetí za člena Spolku přátel při TBDI, IČ: 039 35 175, sídlem Třída generála Píky 13, 613 00 Brno - Černá pole (dále též pouze „spolek“) a projevuje vůli být vázán stanovami spolku ode dne, kdy se stane jeho členem. Uchazeč prohlašuje, že si před podpisem přečetl stanovy spolku a níže na straně 2/3 uvedená pravidla, a že s těmito souhlasí.</p>
			<p>Osoba oprávněná fyzicky využívat členství dle čl. X. odst. 3) stanov<sup>3</sup>:</p>
			<p>'.$opravnena_osoba.'</p>
			<p>Před podpisem přihlášky byl uchazeč srozuměn s výší členského poplatku, který jsou členové povinni pravidelně hradit:</p>
			<p>'.$poplatek_za_clenstvi.' Kč/rok trvání členství (nikoli rok kalendářní), se splatností předem. První členský poplatek je splatný v den přijetí za člena. Uchazeč si je vědom, že poplatek se hradí za samo členství bez ohledu na to, v jaké míře jej jako člen využíval nebo jaké aktivity spolek v konkrétním období vyvinul. Každý další členský poplatek je splatný prvním dnem příslušného roku trvání členství (nikoli roku kalendářního)<sup>4</sup>.</p>
			<p>Členské poplatky je možno hradit bankovním převodem na účet spolku, č. ú.: 2700785944/2010 Jako VS se užívá členské číslo uvedené dále na druhé straně, aby bylo možno platbu identifikovat. Přípustné jsou i platby v hotovosti k rukám předsedy spolku nebo pověřené osoby.</p>

			<p class="floatLeft">V Brně dne '.$datum_vystaveni.'</p>

			<div align="right">……………………………………<br>Razitko<sup>5</sup>, podpis uchazeče
			</div>


			<p class="floatClear"><small>1 Pokud se liší od adresy sídla<br>
			2 Jméno, příjmení, funkce (např. jednatel)<br>
			3 Jméno, příjmení, trvalý pobyt (případně kontaktní adresa), e-mail a telefon<br>
			4 Příklad: Členství vznikne 5. 5. 2015, první poplatek je splatný dne 5. 5. 2015. První rok trvání členství uplyne dne 5. 5. 2016. Poplatek na další rok trvání členství je splatný dne 6. 5. 2016 jako prvního dne dalšího roku trvání členství, další poplatek je splatný dne 6. 5. 2017 atd.<br>
			5 Není-li razítko, vypíše se hůlkovým písmem název právnické osoby</small></p>
		';

		$pdf->writeHTML($body, true, false, true, false, '');
		
		
		$pdf->AddPage();
		$body = static::_includeStyle('style_body2.css');
		$body .= '
			<table>
				<tr>
					<td width="48%">
					<p>
						<span align="center">
						<strong>SPOLEK PŘÁTEL PŘI TBDI<br>
						<u>OBECNÁ PRAVIDLA ĆERPÁNÍ VÝHOD</u><br><br>
						<small>I.<br>
						Úvod</small></strong><br>
						</span>
						<span class="small-text">
						1)	Spolek mimo jiné zprostředkovaně umožňuje svým členům v souladu s čl. X. odst. 3) stanov spolku čerpání výhod spočívajících v možnosti odebírat různé zboží nebo služby za zvýhodněné ceny od dodavatelů dle nabídek uváděných na webových stránkách spolku (aktuálně club.tbdevelopment.cz, ale nevylučuje se, že v budoucnu může dojít ke změně domény), kdy tyto nabídky si členové mohou zobrazit po přihlášení do systému za užití členských přihlašovacích údajů. Tedy, výhoda spočívá v dojednání zvýhodněných cen a v zajištění nebo zorganizování čerpání daného zboží nebo služeb od dodavatelů za tyto zvýhodněné ceny.
						</span>
						</p>
						<p class="small-text">
						2)	Výše identifikovaný člen, jakožto současný nebo budoucí člen spolku, má nebo bude mít oprávnění po dohodě se spolkem těchto výhod čerpat.
						</p>
						<p class="small-text">
						3)	Tato pravidla stanovují bližší práva a povinnosti člena při čerpání výhod v podobě možnosti odebírat zboží a služby od dodavatelů za zvýhodněné ceny.  Jedná se o pravidla obecná, bližší práva a povinnosti jsou dále rozvedeny u každé nabídky na zvýhodněné odebírání konkrétního zboží nebo služby na webových stránkách spolku. V případě přímého rozporu mají přednost pravidla stanovená u konkrétní nabídky.
						</p>
						
						<p>
						<span align="center">
							<small><strong>II.<br>
							Systém zajišťování a čerpání výhod</strong></small><br>
						</span>
						<span class="small-text">
						1)	Při sjednávání odběru služeb nebo zboží od dodavatelů za zvýhodněné ceny spolek přímo spolupracuje s obchodní společností TB development&investment, s.r.o., IČ: 02595354, sídlem Třída generála Píky 13, 613 00 Brno - Černá pole.
						</span></p>
						<p class="small-text">2)	Systém čerpání výhod v podobě možnosti odebírat od dodavatelů různé zboží a služby za zvýhodněné služby funguje následovně (není – li dále uvedeno jinak): </p>
						<p class="small-text">a)	Člen si na webových stránkách spolku zvolí konkrétní nabídku, tj. zboží nebo služby od konkrétního dodavatele, které chce čerpat za tam popsanou zvýhodněnou cenu a zašle objednávku spolku (typicky e-mailem). Spolek (zpravidla v osobě předsedy spolku) požadavek člena zpracuje a zařídí u společnosti TB development&investment, s.r.o., aby požadované zboží nebo požadovanou službu smluvně zajistila u příslušného dodavatele. Společnost TB development&investment, s.r.o., je tedy tím, kdo je v přímém smluvním vztahu s dodavateli zboží a služeb. Tito dodavatelé jsou písemně či ústně zpraveni o tom, že koncovými a faktickými odběrateli zboží a služeb jsou spolek a jeho členové, kterým tyto služby a zboží společnost zpřístupní, což je jednou z podmínek poskytování zvýhodněných cen (tzn. úzký okruh osob).</p>
						<p class="small-text">b)	Úhrada ceny za obdržené zboží a čerpané služby se následně provádí tak, že dodavatel služby nebo zboží požaduje úhradu po svém smluvním partnerovi, tj. po společnosti TB development&investment, s.r.o., která pochopitelně danou úhradu na základě ústního smluvního vztahu mezi ní a spolkem žádá po spolku, neboť pro něj a jeho členy dané zboží a služby zajišťovala. <strong><u>Cenu za odebrané zboží a čerpané služby spolek vyúčtuje členovi, který je povinen tuto cenu spolku uhradit.</u></strong></p>
						<p class="small-text">3)	Člen výše popsaný systém bere na vědomí, souhlasí s ním a zavazuje se dodržovat svoje platební povinnosti vůči spolku.</p>
						<p class="small-text">4)	Sjednává se a člen bere na vědomí, že spolek je oprávněn kdykoli ukončit zajišťování konkrétní výhody, ať již z důvodu změny postoje dodavatele, zániku smluvního vztahu dodavatele se společností TB development&investment, s.r.o., nebo i z jiných, třeba i neuvedených důvodů. Nejedná se z pohledu spolku o výdělečnou činnost a spolek není podnikatelem. Svoji snahu a činnost ve prospěch členů vyvíjí na dobrovolné bázi, tudíž po něm nelze žádat zajišťování výhod </p>

					</td>
					<td width="4%">&nbsp;</td>
					<td width="48%">
						<p class="small-text">v nárokové podobě. Pokud naopak z nabídky vyplývá, že člen je povinen čerpat určitou výhodu (zboží/služba) nejméně po určitou dobu, je tím člen vázán.</p>
						<p><span align="center"><small><strong>III.<br>
Způsob vyúčtování, bližší platební povinnosti člena<br></strong></small></span>
<span class="small-text">1)	Spolek vyzve člena k úhradě zpravidla prostřednictvím e-mailové zprávy na elektronickou adresu člena nebo písemně. Člen je povinen hradit ty služby a to zboží, které žádal, ať již pro sebe, nebo pro další jím označené osoby (typicky dle čl. X. odst. 4 stanov spolku). Tyto služby a zboží spolek členovi vyúčtuje na základě údajů získaných od společnosti TB development&investment, s.r.o.</span></p>
						<p class="small-text">2)	Pokud není v pravidlech pro konkrétní nabídku uvedeno jinak, předkládá spolek členovi vyúčtování vždy měsíčně zpětně. Člen je povinen uhradit spolkem vyúčtovanou cenu zboží nebo služeb vždy bezodkladně, nejpozději však ve lhůtě stanovené spolkem. POZOR, společnost TB development&investment, s.r.o., smí pro zjednodušení procesu sama přímou cestou předkládat vyúčtování členovi a žádat úhradu. Pokud k tomu dojde, bere člen na vědomí, že se tak děje po dohodě mezi touto společností a spolkem.</p>
						<p class="small-text">3)	Člen bere na vědomí, že včasnost úhrady z jeho strany je velmi důležitá pro to, aby nedošlo k prodlení s úhradou ceny zboží nebo služeb dodavateli jako takovému, který by následně mohl požadovat smluvní pokuty nebo zákonné úroky z prodlení. To by představovalo škodu, za kterou by v konečném důsledku člen odpovídal, což bere na vědomí. Teprve poté, co člen úhradu ve prospěch spolku provede, může být tato přeposlána dodavateli (přímo, nebo prostřednictvím společnosti TB developlment&investment, s.r.o.). Nelze žádat, aby za člena tuto cenu nejprve hradil spolek s dodatečným vyzváním k úhradě, byť se k tomu může spolek dobrovolně rozhodnout.</p>
						<p class="small-text">4)	Člen bere na vědomí, že prodlení s úhradou vyúčtované ceny zboží nebo služeb může spolku způsobit vážné komplikace. Zavazuje se tedy své platební povinnosti plnit vždy řádně a včas a sjednává se, že porušení této povinnosti se považuje za přímý důvod k vyloučení člena ze spolku. I když k vyloučení člena nedojde, má vždy spolek (a skrze pokyn spolku společnost TB development&investment, s.r.o.) právo dočasně nebo trvale ukončit poskytování nebo zajišťování veškerých výhod členovi a dalším osobám, které výhod čerpají spolu se členem.</p>
						<p>
							<span align="center">
							<strong><small>IV.<br>
							Další a závěrečná ujednání<br></small></strong></span>
							<span class="small-text">
							1)	Člen bere na vědomí, že je oprávněn čerpat zde popisovaných výhod výhradně v době jeho trvání členství ve spolku. Poté toto právo zaniká, tudíž mu nadále nebudou zajišťovány předmětné služby ani zboží. To platí i pro osoby, na které oprávnění čerpání výhod člen v souladu s ustanovením čl. X. odst. 4) stanov po dohodě se spolkem rozšířil, tzn. i u těchto osob oprávnění zaniká nejpozději s okamžikem zániku členství člena.
							</span>
						</p>
						<p class="small-text">2)	Tím, že člen požádá spolek o možnost čerpat určitou službu nebo odebírat určité zboží, dává najevo, že si řádně přečetl též pravidla uvedená u konkrétní nabídky na webových stránkách spolku, souhlasí s nimi a bude se jimi řídit ve spojení s touto dohodou o obecných pravidlech čerpání výhod.</p>
						<p class="small-text">3)	Na jiné právní vztahy, nežli ty, které se týkají čerpání výhod v podobě odběru zboží nebo služeb od dodavatelů za zvýhodněné ceny, se tato pravidla nepoužijí, tj. tato pravidla nemají ambice obsáhnout všechny oblasti činnosti spolku, týkají se výhradně té části činnosti spolku mající za cíl zpřístupnit členům slevy na trhu zboží a služeb (telefonní tarify, pohonné hmoty, dodávky energií apod.). Tato pravidla se z povahy věci neuplatní, pokud člen uzavře sám smlouvu přímo s dodavatelem.</p>
						<p class="small-text">4)	Tato pravidla se uplatní i tehdy, pokud v době uzavření této dohody člen službu již čerpá nebo zboží již odebírá. Práva a povinnosti vzniklá před podpisem této dohody zůstávají nedotčena.</p>
					</td>
				</tr>
			</table>';
		
		$pdf->writeHTML($body, true, false, true, false, '');
		
		
		$pdf->AddPage();
		$body = static::_includeStyle('style_body2.css');
		$body .= '
			<h3>Rozhodnutí předsedy spolku:</h3>

			<p><strong>Předseda spolku tímto přijímá uchazeče za člena spolku.</strong> Člen je nyní zavázán plněním povinností dle stanov. Předseda spolku předává novému členovi přihlašovací údaje na webové stránky spolku.Členství je uděleno na dobu neurčitou, zaniknout může pouze za podmínek uvedených ve stanovách nebo za podmínek dle zákona. Uděluje se členské číslo:</p>
			<div align="center">'.$klientske_cislo.'</div>
			<p>E-mailový kontakt předsedy spolku: spunar@tbdevelopment.cz. Případná změna bude oznámena prostřednictvím webových stránek nebo e-mailové zprávy.</p>

			<br>
			<p class="floatLeft">V Brně dne .........................</p>
			<br>
			<div align="right">……………………………………………<br>
			<strong>Spolek přátel při TBDI &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;</strong><br>
			podepsán předseda spolku &nbsp; &nbsp; &nbsp; &nbsp;
			<br>
			</div>

			<p>Uchazeč, jakožto nový člen, níže svým podpisem stvrzuje, že mu byly předány přihlašovací údaje na webové stránky spolku. Současně stvrzuje, že souhlasí se vším, co bylo uvedeno výše ze strany předsedy spolku.</p>
			<p>Uchazeč, jakožto nový člen, prohlašuje, že byl výslovně poučen o významu webových stránek spolku: club.tbdevelopment.cz, které dle stanov mohou sloužit k vyvěšení pozvánky na zasedání členské schůze, k předložení návrhu usnesení členské schůzi k přijetí bez osobního zasedání apod. Současně byl člen upozorněn na povinnost pravidelně kontrolovat svou e-mailovou schránku, která má obdobný význam.</p>
			<p>Pokud označil uchazeč osobu oprávněnou fyzicky využívat členství ve smyslu čl. X. odst. 3), učinil tak s vědomím, že tato osoba má o dané oprávnění zájem. Pokud se toto ukáže jako nesprávný předpoklad, jde tato skutečnost k tíži uchazeče. Členský poplatek se nevrací. Zároveň v takovém případě uchazeč prohlašuje, že je mu známo, že jím označená osoba souhlasí s tím, aby spolek pro vlastní potřeby nakládal s osobními údaji dané osoby. Pokud je oprávněnou osobou sám zástupce uchazeče podepisující přihlášku, platí toto obdobně.</p>
			<p>Uchazeč stvrzuje, že mu bylo předáno rozhodnutí o přijetí za člena.</p>

			<br>
			<p class="floatLeft">V Brně dne ........................</p>
			<br>
			<div align="right">………………………………………………<br>
			Razítko<sup>6</sup>, podpis zástupce
			<br>
			</div>
			<br><br><br><br><br><br><br><br><br><br><br><br>
			<small>
			6 Není-li razítko, vypíše se hůlkovým písmem název právnické osoby</small>
		';
		$pdf->writeHTML($body, true, false, true, false, '');

		$pdf->AddPage();
		$body = static::_includeStyle('style_body2.css');
		$body .= '
			
			<h3>Rozhodnutí předsedy spolku:</h3>

			<p><strong>Předseda spolku tímto přijímá uchazeče za člena spolku.</strong> Člen je nyní zavázán plněním povinností dle stanov. Předseda spolku předává novému členovi přihlašovací údaje na webové stránky spolku.Členství je uděleno na dobu neurčitou, zaniknout může pouze za podmínek uvedených ve stanovách nebo za podmínek dle zákona. Uděluje se členské číslo:</p>
			<div align="center">'.$klientske_cislo.'</div>
			<p>E-mailový kontakt předsedy spolku: spunar@tbdevelopment.cz. Případná změna bude oznámena prostřednictvím webových stránek nebo e-mailové zprávy.</p>

			<br>
			<p class="floatLeft">V Brně dne .........................</p>
			<br>
			<div align="right">……………………………………………<br>
			<strong>Spolek přátel při TBDI &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;</strong><br>
			podepsán předseda spolku &nbsp; &nbsp; &nbsp; &nbsp;
			<br>
			</div>

			<p>Uchazeč, jakožto nový člen, níže svým podpisem stvrzuje, že mu byly předány přihlašovací údaje na webové stránky spolku. Současně stvrzuje, že souhlasí se vším, co bylo uvedeno výše ze strany předsedy spolku.</p>
			<p>Uchazeč, jakožto nový člen, prohlašuje, že byl výslovně poučen o významu webových stránek spolku: club.tbdevelopment.cz, které dle stanov mohou sloužit k vyvěšení pozvánky na zasedání členské schůze, k předložení návrhu usnesení členské schůzi k přijetí bez osobního zasedání apod. Současně byl člen upozorněn na povinnost pravidelně kontrolovat svou e-mailovou schránku, která má obdobný význam.</p>
			<p>Pokud označil uchazeč osobu oprávněnou fyzicky využívat členství ve smyslu čl. X. odst. 3), učinil tak s vědomím, že tato osoba má o dané oprávnění zájem. Pokud se toto ukáže jako nesprávný předpoklad, jde tato skutečnost k tíži uchazeče. Členský poplatek se nevrací. Zároveň v takovém případě uchazeč prohlašuje, že je mu známo, že jím označená osoba souhlasí s tím, aby spolek pro vlastní potřeby nakládal s osobními údaji dané osoby. Pokud je oprávněnou osobou sám zástupce uchazeče podepisující přihlášku, platí toto obdobně.</p>
			<p>Uchazeč stvrzuje, že mu bylo předáno rozhodnutí o přijetí za člena.</p>

			<br>
			<p class="floatLeft">V Brně dne ........................</p>
			<br>
			<div align="right">………………………………………………<br>
			Razítko<sup>6</sup>, podpis zástupce
			<br>
			</div>
			<br><br><br><br><br><br><br><br><br><br><br><br>
			<small>
			6 Není-li razítko, vypíše se hůlkovým písmem název právnické osoby</small>
		';
		$pdf->writeHTML($body, true, false, true, false, '');
		
		$pdf->Output($filepath);
		
		$rsrc = new Art_Model_Resource_Db;
		$rsrc->hash = $filehash;
		$rsrc->name = $filename;
		$rsrc->path = $filepath;
		$rsrc->size = filesize($filepath);
		$rsrc->rights_read = 0;
		$rsrc->rights_write = 0;
		$rsrc->save();
		
		return $rsrc;
	}
}