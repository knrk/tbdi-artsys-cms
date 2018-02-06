<?php

		//Helper_TBDev_Import::setTBDDB();

class Helper_TBDev_Import extends Art_Abstract_Helper {
	
	static function initTBDDB ( $setDB = false )
	{
		Art_Main::db()->query('TRUNCATE TABLE address');
		Art_Main::db()->query('TRUNCATE TABLE dashboard');
		Art_Main::db()->query('TRUNCATE TABLE invite_code');
		Art_Main::db()->query('TRUNCATE TABLE note');
		Art_Main::db()->query('TRUNCATE TABLE review');
		Art_Main::db()->query('TRUNCATE TABLE service');
		Art_Main::db()->query('TRUNCATE TABLE service_payment');
		Art_Main::db()->query('TRUNCATE TABLE service_price');
		Art_Main::db()->query('TRUNCATE TABLE user_group_x_service_price');
		Art_Main::db()->query('TRUNCATE TABLE user');
		Art_Main::db()->query('TRUNCATE TABLE user_data');
		Art_Main::db()->query('TRUNCATE TABLE user_x_company');
		Art_Main::db()->query('TRUNCATE TABLE user_x_email');
		Art_Main::db()->query('TRUNCATE TABLE user_x_invite_code');
		Art_Main::db()->query('TRUNCATE TABLE user_x_manager');
		Art_Main::db()->query('TRUNCATE TABLE user_x_request');
		Art_Main::db()->query('TRUNCATE TABLE user_x_service');
		Art_Main::db()->query('TRUNCATE TABLE user_x_Service_Investment_Value');
		
		Art_Main::db()->query('TRUNCATE TABLE mail_dump');
		Art_Main::db()->query('TRUNCATE TABLE resource');
		
		Art_User::createDefaultUser('IUGF5678KJHY');
		
		/* SERVICES */
		
		$serviceM = new Service;
		$serviceM->type = 'membership';
		$serviceM->name = 'Členství';
		$serviceM->settings = '{"icon":"user","article":"",promo":"service-membership","conditions":""}';
		$serviceM->save();
		
		$serviceE = new Service;
		$serviceE->type = 'energy';
		$serviceE->name = 'Energie';
		$serviceE->settings = '{"icon":"plug","article":"","promo":"service-energy","conditions":"energy-conditions.pdf"}';
		$serviceE->save();
		
		$serviceI = new Service;
		$serviceI->type = 'investment';
		$serviceI->name = 'Investice';
		$serviceI->settings = '{"icon":"pie-chart","article":"","promo":"service-investment","conditions":"investment-conditions.pdf"}';
		$serviceI->save();
		
		$serviceT = new Service;
		$serviceT->type = 'tariffs';
		$serviceT->name = 'Telefonní tarif';
		$serviceT->settings = '{"icon":"phone","article":"","promo":"service-tariffs","conditions":"tariffs-conditions.pdf"}';
		$serviceT->save();
				
		$serviceP = new Service;
		$serviceP->type = 'fuel';
		$serviceP->name = 'Pohonné hmoty';
		$serviceP->settings = '{"icon":"car","article":"","promo":"service-fuel","conditions":"fuel-conditions.pdf"}';
		$serviceP->save();
		
		/* SERVICE PRICES */
		
		$servicePriceM = new Service_Price;
		$servicePriceM->is_default = 1;
		$servicePriceM->price = 1200;
		$servicePriceM->time_interval = '1r';
		$servicePriceM->setService($serviceM);
		$servicePriceM->save();	
		
		$servicePriceMm = new Service_Price;
		$servicePriceMm->is_default = 1;
		$servicePriceMm->price = 100;
		$servicePriceMm->time_interval = '1m';
		$servicePriceMm->setService($serviceM);
		$servicePriceMm->save();
				
		$servicePriceE = new Service_Price;
		$servicePriceE->is_default = 1;
		$servicePriceE->price = 0;
		$servicePriceE->time_interval = '1m';
		$servicePriceE->setService($serviceE);
		$servicePriceE->save();
		
		$servicePriceI = new Service_Price;
		$servicePriceI->is_default = 1;
		$servicePriceI->price = 0;
		$servicePriceI->time_interval = '1m';
		$servicePriceI->setService($serviceI);
		$servicePriceI->save();
		
		$servicePriceT = new Service_Price;
		$servicePriceT->is_default = 1;
		$servicePriceT->price = 0;
		$servicePriceT->time_interval = '1m';
		$servicePriceT->setService($serviceT);
		$servicePriceT->save();
		
		$servicePriceP = new Service_Price;
		$servicePriceP->is_default = 1;
		$servicePriceP->price = 0;
		$servicePriceP->time_interval = '1m';
		$servicePriceP->setService($serviceP);
		$servicePriceP->save();
				
		/* USER GROUPS */
		
		$userGroup = new Art_Model_User_Group;
		$userGroup->id_rights = 2;
		$userGroup->name = 'Registered';
		$userGroup->save();
		
		$userGroupA = new Art_Model_User_Group;
		$userGroupA->id_rights = 8;
		$userGroupA->name = 'Authorized';
		$userGroupA->save();
		
		$managerUserGroup = new Art_Model_User_Group;
		$managerUserGroup->id_rights = 9;
		$managerUserGroup->name = 'Manager';
		$managerUserGroup->save();
		
		$userGroupM = new Art_Model_User_Group;
		$userGroupM->id_rights = 2;
		$userGroupM->name = Helper_TBDev::MEMBERSHIP_MEMBERS_GROUP;
		$userGroupM->save();
		
		$userGroupE = new Art_Model_User_Group;
		$userGroupE->id_rights = 2;
		$userGroupE->name = 'Energy service members';
		$userGroupE->save();
		
		$userGroupI = new Art_Model_User_Group;
		$userGroupI->id_rights = 2;
		$userGroupI->name = 'Investment service members';
		$userGroupI->save();
		
		$userGroupT = new Art_Model_User_Group;
		$userGroupT->id_rights = 2;
		$userGroupT->name = 'Tariffs service members';
		$userGroupT->save();
		
		$userGroupP = new Art_Model_User_Group;
		$userGroupP->id_rights = 2;
		$userGroupP->name = 'Fuel service members';
		$userGroupP->save();
		
		/* USER GROUP - SERVICE PRICE */
		
		$userGroupServicePriceM = new User_Group_X_Service_Price;
		$userGroupServicePriceM->time_from = dateSQL();
		$userGroupServicePriceM->time_to = dateSQL('+5 years');
		$userGroupServicePriceM->setServicePrice($servicePriceM);
		$userGroupServicePriceM->setUserGroup($userGroupM);
		$userGroupServicePriceM->save();
		
		$userGroupServicePriceMm = new User_Group_X_Service_Price;
		$userGroupServicePriceMm->time_from = dateSQL();
		$userGroupServicePriceMm->time_to = dateSQL('+5 years');
		$userGroupServicePriceMm->setServicePrice($servicePriceMm);
		$userGroupServicePriceMm->setUserGroup($userGroupM);
		$userGroupServicePriceMm->save();
		
		$userGroupServicePrice = new User_Group_X_Service_Price;
		$userGroupServicePrice->time_from = dateSQL();
		$userGroupServicePrice->time_to = dateSQL('+5 years');
		$userGroupServicePrice->setServicePrice($servicePriceE);
		$userGroupServicePrice->setUserGroup($userGroupE);
		$userGroupServicePrice->save();
		
		$userGroupServicePrice = new User_Group_X_Service_Price;
		$userGroupServicePrice->time_from = dateSQL();
		$userGroupServicePrice->time_to = dateSQL('+5 years');
		$userGroupServicePrice->setServicePrice($servicePriceI);
		$userGroupServicePrice->setUserGroup($userGroupI);
		$userGroupServicePrice->save();
		
		$userGroupServicePrice = new User_Group_X_Service_Price;
		$userGroupServicePrice->time_from = dateSQL();
		$userGroupServicePrice->time_to = dateSQL('+5 years');
		$userGroupServicePrice->setServicePrice($servicePriceT);
		$userGroupServicePrice->setUserGroup($userGroupT);
		$userGroupServicePrice->save();
		
		$userGroupServicePrice = new User_Group_X_Service_Price;
		$userGroupServicePrice->time_from = dateSQL();
		$userGroupServicePrice->time_to = dateSQL('+5 years');
		$userGroupServicePrice->setServicePrice($servicePriceP);
		$userGroupServicePrice->setUserGroup($userGroupP);
		$userGroupServicePrice->save();

		
	$managers = array(array('name'=>'Martin','surname'=>'Tůma','email'=>'tuma@tbdevelopment.cz','ID'=>'2','tel'=>'720316767','d'=>'24','m'=>'6','y'=>'1991'),
					array('name'=>'Lukáš','surname'=>'Špunar','email'=>'spunar@tbdevelopment.cz','ID'=>'1','tel'=>'776575179','d'=>'26','m'=>'10','y'=>'1989'),
					array('name'=>'Ivo','surname'=>'Burian','email'=>'burian@tbdevelopment.cz','ID'=>'3','tel'=>'608673073','d'=>'26','m'=>'12','y'=>'1976'));

	$manExt = array(array('2015-05-30 08:11:33', 'Tábor', 'Bukurešťská', '2792', '39005'),
					array('2015-06-04 09:12:20', 'Brno', 'Těsnohlídkova', '942/8', '61300'),
					array('2015-06-05 09:49:08', 'Brno', 'Vranovská', '508/26', '61400'));
	
	foreach ( $managers	as $key => $value ) 
	{
		$user = new Art_Model_User;
		$user->user_number = $value['ID'];
		$user->active = 1;
		$user->id_currency = 1;	
		$user->save();

if ( ART_DEBUG ) :
		$password = 'pass';
else:
		$password = Art_User::generatePassword();
endif;

		$user_data = new Art_Model_User_Data;
		$user_data->name = $value['name'];
		$user_data->surname = $value['surname'];
		$user_data->email = $value['email'];
		$user_data->salt = Art_User::generateSalt();
		$user_data->password = Art_User::hashPassword($password, $user_data->salt);
		$user_data->verif = 1;
		$user_data->verif_date = $manExt[$key][0];
if ( ART_DEBUG ) :		
		$user_data->pass_changed_date = dateSQL();	// TODO so far
endif;
		$user_data->setUser($user);
		$user_data->born_day = $value['d'];
		$user_data->born_month = $value['m'];
		$user_data->born_year = $value['y'];
		$user_data->save();

		$user_x_user_group = new Art_Model_User_X_User_Group;
		$user_x_user_group->setUser($user);
		$user_x_user_group->setGroup($managerUserGroup);
		$user_x_user_group->save();

		$insertDeliveryUserAddress = new Art_Model_Address();
		$insertDeliveryUserAddress->area_code = '420';
		$insertDeliveryUserAddress->phone = $value['tel'];
		$insertDeliveryUserAddress->city = $manExt[$key][1];
		$insertDeliveryUserAddress->street = $manExt[$key][2];
		$insertDeliveryUserAddress->housenum = $manExt[$key][3];
		$insertDeliveryUserAddress->zip = $manExt[$key][4];
		$insertDeliveryUserAddress->setUser($user);
		$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getDelivery());
		$insertDeliveryUserAddress->id_country = 1;
		$insertDeliveryUserAddress->save();
		
		/* INV CODES */
		$invCode = new Invite_Code;
		$invCode->active = 1;
		$invCode->code = Helper_TBDev::generateInviteCode();
		$invCode->note = 'Initial';
		$invCode->setUser($user);
		$invCode->created_by = $user->id;
		$invCode->save();
	}
	
	if ( $setDB )
	{
		static::setTBDDB();
	}
}
	
	/**
	 *	Initialize DB
	 *
	 *	@param boolean $test_users
	 *	@return void
	 */
	static function setTBDDB (  )
	{
	
	$users = array(
		array('Bc.', 'Tomáš', 'Fejtl', '8', '1', '1992', '733538098', '100', 'fejtlt@gmail.com', '2', '1', '2015-04-25', '2', '2015-04-24 06:11:26', 'Brno', 'Košinova', null, '61200'),
			array(null, 'Soňa', 'Svobodová', '18', '10', '1990', '733538214', '108', 'sonnasvobodova@email.cz', '4', '0', '2015-05-01', '2', '2015-05-27 13:35:02', 'Kyjov', 'Karla Čapka', '2249', '69701'),
			array(null, 'Helena', 'Fejtlová', '16', '5', '1968', '733538098', '189', 'radfej@seznam.cz', '4', '0', '2016-05-11', '1', '2016-05-11 15:35:52', 'Brno', 'Košinova', '68', '61200'),
		array(null, 'David', 'Hýbal', '10', '5', '1982', '602262015', '123', 'dhybal@seznam.cz', '2', '1', '2015-06-15', '1', '2015-06-08 12:42:23', 'Brno', 'Kuršova', '6', '63500'),	
			array(null, 'Richard', 'Zich', '11', '3', '1978', '777975575', '303', 'rich.zich@seznam.cz', '5', '0', '2015-05-06', '1', '2015-05-09 09:31:16', 'Kuřim', 'Na Loučkách', '1220', '66434'),
			array(null, 'Kateřina', 'Hýbalová', '28', '3', '1992', '724746246', '113', 'katerina.hybalova@gmail.com', '5', '0', '2015-06-23', '1', '2015-06-23 10:48:46', 'Hradec Králové', 'Gruzínská', '645', '50341'),
			array(null, 'Petr', 'Hronek', '2', '7', '1987', '734545806', '304', 'pitr.hronek@seznam.cz', '5', '0', '2015-05-05', '2', '2015-05-09 09:33:38', 'Brno', 'Za školou', '3', '61700'),	
		array(null, 'Ivan', 'Uhlíř', null, null, null, null, null, null, '2', '1', null, null, null, null, null, null, null),	
			array(null, 'Radim', 'Klaška', '9', '4', '1993', '607666073', '105', 'klaska.radim@gmail.com', '6', '0', '2015-04-01', '2', '2015-05-14 18:39:35', 'Jiříkovice', 'Na návsi', '1', '66451'),
			array(null, 'Zdeněk', 'Nejtek', '10', '7', '1982', '731068017', '106', 'zdeneknejtek@seznam.cz', '6', '0', '2015-04-01', '2', '2015-05-14 19:04:19', 'Brno', 'Stamicova', '5', '62300'),
			array(null, 'Pavla', 'Franzova', '12', '7', '1974', '732114844', '159', 'liborfranz@seznam.cz', '6', '0', '2015-09-18', '1', '2015-09-18 16:56:23', 'Lelekovice', 'Poňava', '330', '66431'),
			array(null, 'Pavlína', 'Motalová', '15', '5', '1992', '773184581', '177', 'motalovapavlina@seznam.cz', '6', '0', '2016-01-05', '1', '2016-01-05 07:45:06', 'Rozstání', 'Rozstání', '315', '79862'),	
		array(null, 'Zdena', 'Černá', '6', '8', '1990', '604490141', '102', 'zdenchaa@seznam.cz', '2', '0', '2015-04-25', '2', '2015-04-25 16:29:50', 'Podhorní', null, '20', '62800'),
		array(null, 'Jan', 'Vymětal', '22', '11', '1989', '733501251', '174', 'h.vymetal@seznam.cz', '2', '0', '2015-12-22', '1', '2015-12-22 21:31:56', 'Kroměříž', 'Mánesova', '3605/4', '76701'),
		array(null, 'Parviz', 'Aganj', null, null, null, null, null, null, '2', '1', null, null, null, null, null, null, null),
			array('Bc.', 'Katarína', 'Potůčková', '15', '8', '1991', '607528553', '110', 'potuckovakatarina@seznam.cz', '7', '0', '2015-05-27', '1', '2015-05-27 12:27:37', 'Benešov', 'Pražského povstání', '2265', '25601'),
			array(null, 'Pavel', 'Vymazal', '13', '4', '1990', '608042700', '135', 'tattoo.ediieee@gmail.com', '7', '0', '2015-07-16', '1', '2015-07-10 09:58:51', 'Praha 2', 'Slavíkova', '1620/13', '12000'),	
		array(null, 'Veronika', 'Kolbalová', null, null, null, null, null, null, '2', '1', null, null, null, null, null, null, null),	
			array(null, 'Jan', 'Hajn', '16', '3', '1993', '774594678', '118', 'hajn@volny.cz', '8', '0', '2015-06-10', '1.6', '2015-06-10 16:20:25', 'Brno', 'Molákova', '4', '62800', 'Brno', 'Kounicova', '11', '60200',),
		array(null, 'Ales', 'Netopilík', '4', '8', '1990', '733265984', '116', 'ales.netopilik@gmail.com', '2', '1', '2015-06-05', '1', '2015-06-05 09:04:39', 'Hodonín', 'U Červených domků', '3', '69501'),
			array(null, 'Ondřej', 'Šnobl', '18', '4', '1987', '603924674', '133', 'snoblondrei@gmail.com', '9', '0', '2015-07-10', '1', '2015-07-09 22:03:22', 'Hodonín', 'U Červených domků', '3', '69501'),
		array(null, 'Alžběta', 'Svobodová', '30', '7', '1994', '605718650', '139', 'freedomelizabeth@centrum.cz', '2', '0', '2015-07-16', '1.3', '2015-07-16 05:14:12', 'Brno', 'Řezáčova', '44', '62400', 'Brno', 'Rybářská', '8', '60300',),
		array(null, 'Kristýna', 'Karásková', '16', '5', '1976', '603428607', '130', 'kristianek.k@centrum.cz', '2', '0', '2015-08-10', '1', '2015-08-10 10:34:10', 'Brno', 'Demlova', '20', '61300'),
		array(null, 'Eva', 'Totková', '25', '2', '1982', '605101255', '157', 'petr.totek@gmail.com', '2', '1', '2015-09-13', '1', '2015-09-13 14:17:33', 'Kroměříž', 'Kotojedská', '3311', '76701'),
			array(null, 'Alena', 'Šimordová', '19', '3', '1964', '732237283', '152', 'Simordova@seznam.cz', '10', '1', '2015-09-02', '1', '2015-09-02 04:06:37', 'Kroměříž', 'Zborovská', '4179/11', '76701'),
				array('MUDr', 'Milan', 'Navrátil', '28', '2', '1970', '605820840', '172', 'mnavrat@centrum.cz', '11', '0', '2016-01-28', '1', '2016-01-28 18:35:21', 'Brno', 'Skalky', '6', '61600'),	
		array(null, 'Vladimír', 'Lajda', '27', '10', '1960', '603581912', '148', 'lajda.vladimir@seznam.cz', '2', '1', '2015-09-08', '1', '2015-08-29 09:10:06', 'Trnava', null, '399', '76318'),
			array(null, 'Soňa', 'Skřivánková', '23', '3', '1970', '775799971', '147', 'sonaskrivankova@seznam.cz', '12', '0', '2015-09-08', '1', '2015-08-29 09:02:04', 'Trnava', null, '399', '76318'),
			array('Bc.', 'Radka', 'Skřivánková', '17', '4', '1992', '775299062', '166', 'radkaskrivankova@seznam.cz', '12', '0', '2015-10-13', '1', '2015-10-13 18:55:52', 'Trnava', 'Trnava', '399', '76318'),
			array(null, 'Gabriela', 'Skřivánková', '22', '11', '1993', '775299063', '167', 'gabcaskrivankova@seznam.cz', '12', '0', '2015-10-13', '1', '2015-10-13 18:59:49', 'Trnava', 'Trnava', '399', '76318'),	
		array(null, 'Alois', 'Hrda', '28', '9', '1966', '775678915', '160', 'aloishrda@seznam.cz', '2', '0', '2015-09-29', '1', '2015-09-29 12:42:39', 'Šťáhlavy', 'Seifertova', '592', '33203'),
		array('Bc.', 'Tomáš', 'Uher', '23', '8', '1988', '731110857', '164', 'tom.maske@gmail.com', '2', '0', '2015-11-17', '1', '2015-11-17 20:58:15', 'Brno', 'Úvoz', '25', '60200'),
		array('Ing.', 'Jana', 'Široká', '16', '6', '1981', '730575874', '115', 'janasiroka@centrum.cz', '2', '0', '2015-07-23', '1', '2015-07-23 05:31:05', 'Vyškov', 'Sochorova', '27', '68201'),
		array(null, 'Dušan', 'Tekeljak', '7', '7', '1987', '774315605', '104', 'tekeljak@gmail.com', '2', '0', '2015-05-12', '1', '2015-05-12 04:53:42', 'Žilina', 'M. Šinského', '5', '01007'),
		array(null, 'Juraj', 'Durec', '1', '1', '1996', '722313078', '184', 'jdurec96@gmail.com', '2', '0', '2016-03-22', '1', '2016-03-22 09:57:11', 'Myjava', 'Stará Myjava', '63', '90701', 'Brno', 'Zdráhalova', '10', '61300',),
		array(null, 'David', 'Egydy', '1', '1', '1990', '606035858', '186', 'David.egydy@gmail.com', '2', '0', null, null, '2016-03-26 14:23:25', 'Hnátnice', 'Hnatnice', '286', '56101', 'Brno', 'Vinohrady', '488/20', '63900',),
	
		array(null, 'Kateřina', 'Kousalová', '20', '1', '1977', '775942255', '125', 'aktak777@seznam.cz', '1', '0', '2015-06-22', '2', '2015-06-09 06:20:06', 'Sezimovo Ústí', 'Budějovická', '334', '39102'),
		array(null, 'Radek', 'Pavlík', '15', '4', '1992', '728078914', '120', 'radekpavlik7@seznam.cz', '1', '0', '2015-06-07', '1', '2015-06-05 19:49:12', 'Želec', 'Želeč', '26', '39174', 'Tábor', 'Klokotská', '119', '39001',),
		array('PharmDr.', 'Martina', 'Kejdušová', '18', '2', '1982', '737226262', '128', 'Martina.bajerova@seznam.cz', '1', '1', '2015-06-29', '1', '2015-06-29 10:16:26', 'Brno', 'Vychodilova', '12B', '61600'),
			array(null, 'Petra', 'Doleželová ', '2', '6', '1982', '732584503', '301', 'pdolezelova@centrum.cz', '13', '0', '2015-04-09', '2', '2015-04-29 21:05:00', 'Brno', 'Oranžová', '8', '62100', 'Brno', 'Světlá', '22', '61400',),
			array(null, 'Jiří', 'Janů', '24', '5', '1985', '775978774', '121', 'noll.trade@seznam.cz', '13', '0', '2015-06-05', '1', '2015-06-05 20:51:25', 'Pardubice', 'Brožíkova', '444', '53009', 'Pardubice', 'Kpt. Bartoše', '411', '53009',),
			array(null, 'Hana', 'Vrchotová', '24', '3', '1980', '775240324', '302', 'hana.vrchotova@ceskapojistovna.cz', '13', '0', '2015-05-06', '1.3', '2015-04-29 21:36:02', 'Brno', 'Obecká', '30', '62800'),
			array('Ing.', 'Tiago', 'Cruz Pereira de Oliveira', '5', '3', '1987', '605856464', '109', 'tc.oliveira0@gmail.com', '13', '0', '2015-06-01', '1', '2015-05-27 08:17:58', null, null, null, null,),
			array('Dr.', 'Kateřina', 'Kubová', '15', '11', '1973', '776310681', '117', 'kubovak@vfu.cz', '13', '0', '2015-06-05', '2', '2015-06-05 09:08:34', 'Brno', 'Lipská', '6', '61600'),
			array('MVDr.', 'Igor', 'Svobodník', '6', '1', '1976', '603344058', '127', 'info@veterina-svobodnik.cz', '13', '0', '2015-07-02', '1', '2015-06-25 12:41:28', 'Praha', 'Náhorní', '2', '18200'),
			array('Mgr.', 'Pavol', 'Brka', '28', '7', '1985', '733605503', '112', 'pavol.brka@gmail.com', '13', '0', '2015-06-03', '1.3', '2015-06-03 07:28:31', 'Liptovský Mikuláš', 'Jilemnického', '3', '03101', 'Brno', 'Jana Babáka', '1861/3', '61600',),
			array(null, 'Svatava', 'Bajerová', '28', '4', '1955', '724307264', '176', 'bajerova@zsalsova.cz', '13', '0', '2016-01-04', '1', '2016-01-04 19:30:54', 'Kopřivnice', 'Francouzská', '1194', '74221'),
		array('MUDr.', 'František', 'Rusznyák', '9', '11', '1982', '608322063', '129', 'rusznyakf@seznam.cz', '1', '0', '2015-07-01', '2', '2015-07-01 17:11:29', 'Brno', 'Střední', '1', '60200'),	
		array(null, 'Václav', 'Krlín', '7', '10', '1984', '737403188', '114', 'vaclavkrlin@seznam.cz', '1', '0', '2015-06-05', '1.2', '2015-06-03 12:25:28', 'Milevsko', 'Havlíčkova', '446', '39901'),
		array('Bc.', 'Stanislav', 'Saniter', '8', '2', '1991', '603228872', '131', 'stana.saniter@gmail.com', '1', '0', '2015-07-03', '1', '2015-07-03 11:53:48', 'Baška', 'Hodońovice', '106', '73901'),	
		array(null, 'Markéta', 'Tomášková', '1', '3', '1978', '777593966', '126', 'marketa.tomas@seznam.cz', '1', '1', '2015-07-02', '1.1', '2015-06-10 18:47:34', 'Sezimovo Ústí', 'Wolkerova', '1397', '39102'),	
			array(null, 'Romana', 'Maroušková', '11', '8', '1981', '739165610', '153', 'hrunata@seznam.cz', '14', '0', '2015-09-08', '1', '2015-09-08 15:16:06', 'Hlavatce', null, '10', '39175'),
			array(null, 'Petr', 'Hron', '27', '12', '1974', '605766744', '146', null, '14', '0', '2015-09-08', '1', '2015-09-08 14:58:27', null, 'Zvěrotice', '40', '39201'),
			array(null, 'Věra', 'Dobešová', '1', '1', '1957', '604235017', '185', 'VDobesova@seznam.cz', '14', '0', '2016-03-22', '1', '2016-03-22 10:36:27', 'Tábor', 'Ke Chlumu', '246', '39003'),
		array(null, 'David', 'Zumer', '26', '1', '1995', '732558028', '138', 'david.zumer23@seznam.cz', '1', '1', '2015-07-14', '1', '2015-07-14 09:56:38', 'Nový Jičín', 'Zborovská', '9', '74101'),
			array(null, 'Radek', 'Svoboda', '10', '4', '1996', '732309044', '142', 'radeksvoboda8@seznam.cz', '15', '0', '2015-08-21', '1', '2015-08-21 17:46:59', 'Lešná', 'Lešná', '192', '75641'),
			array(null, 'Roman', 'Zumer', '22', '5', '1965', '737425903', '158', 'roman.zumer@seznam.cz', '15', '0', '2015-09-13', '1', '2015-09-13 16:19:40', 'Nový Jičín', 'Vančurova', '6', '74101'),	
		array(null, 'Roman', 'Breda', '27', '5', '1992', '721227517', '101', 'Fredys94@seznam.cz', '1', '0', '2015-09-06', '0.11', '2015-09-06 14:07:18', 'Tabor', 'Hlinice', '55', '39002'),

		array(null, 'Karolína', 'Vovsová', null, null, null, null, null, null, '1', '1', null, null, null, null, null, null, null),
			array(null, 'Tereza', 'Bulantová', '4', '10', '1992', '603515580', '151', 'mistikt@seznam.cz', '16', '0', '2015-08-31', '1', '2015-08-31 20:11:31', 'Bechyně', 'Na Libuši', null, '39165'),
		array(null, 'Robert', 'Kubinský', '10', '11', '1979', '777005479', '145', 'kubinskyrobert@gmail.com', '1', '0', '2015-09-08', '1', '2015-09-08 13:03:45', 'Vyškov', 'Masarykovo náměstí', '108/1', '68201', 'Brno', 'Mariánské náměstí', '5', '61700',),
		array(null, 'Barbora', 'Holá', '30', '8', '1991', '774759592', '143', 'barborahola@seznam.cz', '1', '1', '2015-09-25', '0.10', '2015-09-25 12:29:02', 'Tábor', 'Hanojská', '3', '39005', 'Tábor', 'Hanojská', '2831', '39005',),
			array(null, 'Kryštof', 'Kopec', '25', '2', '1998', '728332551', '180', 'p.kopcova@atlas.cz', '17', '0', '2016-02-27', '0.6', '2016-02-27 08:16:30', 'Praha', 'Točitá', '1721', '14000'),
		array(null, 'Iva ', 'Menzelová ', '23', '10', '1986', '732739396', '162', 'menzelova.iva@centrum.cz ', '1', '1', '2015-10-11', '1', '2015-10-11 19:30:51', 'Tábor', 'Jaselská', '2326', '39003'),
			array(null, 'Luboš', 'Heršálek', '22', '5', '1986', '601324270', '163', 'lhersalek@seznam.cz', '18', '0', '2015-10-12', '1', '2015-10-12 13:13:32', 'Tábor', 'Hlinice', '27', '39002'),
			array(null, 'Aleš', 'Vobořil', '17', '2', '1984', '736463729', '170', 'alesvoboril@seznam.cz', '18', '0', '2015-10-19', '1', '2015-10-19 09:52:43', 'Tábor', 'Minská', '2780', '39005', 'Tábor', 'Žižkovo Náměstí', '3', '39001',),
			array(null, 'Zdeňka', 'Vobořilová', '17', '6', '1961', '603483540', '165', 'zdenka.voborilova@seznam.cz', '18', '0', '2015-10-13', '1', '2015-10-13 09:43:30', 'Tábor', 'Minská', '2780', '39005'),
			array(null, 'Zdeňka', 'Vobořilová', '1', '3', '1987', '775401030', '168', 'voborilovazdenka@seznam.cz', '18', '0', '2015-10-15', '1', '2015-10-15 10:20:23', 'Tábor', 'Minská', '2780', '39005'),
			array(null, 'Zdeněk ', 'Menzel', '30', '7', '1988', '732311835', '175', 'menci26@seznam.cz', '18', '0', '2016-01-04', '1', '2016-01-04 19:15:23', 'Tábor', 'Havanská', '2819', '390 05'),
		array(null, 'Annette', 'Bocová', '8', '10', '1993', '776331201', '173', 'any.b@seznam.cz', '1', '0', '2015-12-09', '1', '2015-12-09 12:20:48', 'Brno', 'Křídlovická', '72', '60300', 'Brno', 'Hlavní', '126', '62400',),	
		array('Mgr.', 'Radoslava', 'Lalíková', '17', '11', '1988', '702074747', '182', 'radka.lalikova6@gmail.com', '1', '0', '2016-03-07', '1', '2016-03-07 11:48:27', 'Bratislava', 'Staré Grunty', null, '841 04', 'Brno', 'třída Generála Píky', null, '61300',),				
		array(null, 'Josef', 'Hort', '15', '4', '1991', '604976927', '103', 'jhort@seznam.cz', '1', '0', '2015-04-28', '1', '2015-04-28 07:03:13', 'Jinošov', null, '73', '67571'),
		array('Ing.', 'Lenka', 'Cirklová', '9', '9', '1971', '774878629', '171', 'lenka@e-way.cz', '1', '0', '2016-01-27', '1', '2016-01-27 16:44:06', 'Brno', 'Schwaigrova', '644/2', '61700', 'Brno', 'Obřanská', '272/129', '61400',),
		array(null, 'Martin', 'Janovský', '7', '10', '1990', '77547572', '155', 'martin.janovsky@viptrader.cz', '1', '0', '2015-09-01', null, '2015-09-08 09:41:35', 'Tábor', 'Nad Dolinami', '227', '39002'),
		array(null, 'Marie', 'Kratochvílová', '23', '11', '1946', '777783214', '137', 'kratmaru@seznam.cz', '1', '0', '2015-12-01', '1', '2015-12-01 19:42:56', 'Želec', null, '227', '39174'),
		array(null, 'David', 'Neužil', '12', '2', '1994', '776120288', '149', 'davineuzil@gmail.com', '1', '0', '2015-09-02', '1', '2015-09-02 14:22:39', 'Čenkov', 'Čenkov', '74', '39175'),
		
		array(null, 'Andrea', 'Hecker Sivonova', '1', '1', '1973', '607646241', '191', 'A.HECKER@HOTMAIL.FR', '1', '0', null, null, '2016-04-29 08:50:04', 'Alsace-Champagne-Ardenne-Lorraine', 'Moulins-lès-Metz', 'Rue d\'\'Alsace', '60', '57160', 'République tchèque', 'Région d\'\'Olomouc', 'Prostějov', 'sídliště Svobody', '3536/37', '796 01',),
		array(null, 'Petr', 'Voves', '1', '1', '1995', '608831644', '178', 'petr.voves@email.cz', '1', '0', '2016-03-10', '1', '2016-03-10 13:07:44', 'Tábor', 'Brigádníků', '2573', '39002', 'Tábor', 'Atc Knížecí rybník', '399', '39156',),
		array(null, 'Matěj', 'Němec', '1', '1', '1993', null, '192', 'nemec.matej93@gmail.com', '1', '0', '2016-05-08', '1', '2016-05-08 07:57:53', 'Tábor', 'Havanská', '2812', '39005'),
		array(null, 'Michal', 'Kakos', null, null, null, null, null, null, '1', '1', null, null, null, null, null, null, null),
			array(null, 'Monika', 'Kosíková', '1', '1', '1992', '734252409', '187', 'kosikova.monika@seznam.cz', '19', '0', '2016-03-29', '1', '2016-03-29 09:26:04', 'Vlašim', 'Komenského', '1464', '25801'),
		array(null, 'Tomáš', 'Koupil', '1', '1', '1982', '776552515', '188', 'tomaskoupil@email.cz', '1', '0', '2016-03-30', '1', '2016-03-30 19:50:08', 'Brno', 'Marie Pavlíkové', '1893', 'Tišnov'),

		array(null, 'Helena', 'Hedtfeld', '27', '3', '1974', '737705205', '111', 'hh38@seznam.cz', '3', '0', '2015-06-01', '1', '2015-06-01 10:35:06', 'Brno', 'Vinohrady', '20', '63900'),
		array(null, 'Petr', 'Krejčí', '18', '4', '1976', '774593925', '140', 'pmodano@seznam.cz', '3', '0', '2015-06-08', '1.6', '2015-08-10 19:05:52', 'Brno', 'Celní', '5', '63900'),
		array(null, 'Miroslav ', 'Hadáček', '3', '7', '1980', '776647618', '132', 'info@hadys-corp.cz', '3', '0', '2015-07-27', '1.6', '2015-07-27 13:02:23', 'Praha', 'Chudenická', '1059', '10200', 'Loket', 'Mírová', '521', '35733',),
		array(null, 'Michael', 'Šedý', '4', '5', '1997', '731457120', '144', 'michael.sedy@gmail.com', '3', '0', '2015-11-05', '0.4', '2015-11-05 17:42:36', 'Brno', 'Zábrdovická', '20', '61500'),
		array(null, 'Miloslav', 'Foukal', '2', '10', '1975', '773460184', '169', 'miloslav.foukal@gmail.com', '3', '0', '2015-10-19', '1', '2015-10-19 07:31:57', 'Brno', 'Jurkovičova', '7', '63800'),
		array('Bc.', 'Alena', 'Šedá', '13', '7', '1974', '733765857', '179', 'alca.seda77@gmail.com', '3', '0', '2016-02-23', '1', '2016-02-23 16:40:46', 'Brno', 'Zábrdovická', '20', '61500'),
	
		
		array(null, 'Lukáš', 'Koňařík', '6', '5', '1980', '775994439', '193', 'lukas@konarik.info', '2', '0', '2016-05-20', '1', '2016-05-20 11:15:08', 'Praha', 'Rezlerova', '310', '10900', 'Brno', 'Čápkova', '45/42', '60200',),
		array('Ing.', 'Vlastimil', 'Janoušek', '26', '2', '1967', '602524204', '201', 'vlastimil.janousek@seznam.cz', '2', '0', '2016-06-14', null, '2016-06-14 09:30:06', 'Brno', 'Zemědělská', '14', '61300'),

		array(null, 'Vladimíra', 'Podehradská', '5', '6', '1978', '736153694', '194', 'vladka01@email.cz', '2', '0', '2016-05-22', '1', '2016-05-22 08:42:29', 'Kroměříž', 'Francouzská', '4021', '76701'),
		array('Bc', 'Iveta', 'Kováčová', '20', '8', '1988', '722953525', '196', 'ivetkovacova@seznam.cz', '2', '0', '2016-05-23', '1', '2016-05-23 15:31:58', 'Brno', 'Milady Horákové', '339/42', '60200'),
		array(null, 'Marek ', 'Butula', '6', '9', '1982', '77028008', '197', 'marekbutula@seznam.cz', '2', '0', '2016-05-23', '1', '2016-05-23 15:36:02', 'Kroměříž', 'Kollárova', '39', '76701'),
		array(null, 'David', 'Konvalinka', '24', '11', '1978', '721050015', '198', 'david.konvalinka@centrum.cz', '2', '0', '2016-06-05', '0.2', '2016-06-05 08:23:18', 'Tábor', 'Smolínova', '1122', '39002'),
		array(null, 'Petra', 'Konvalinková', '27', '4', '1977', '777864306', '199', 'kytka27@centrum.cz', '2', '0', '2016-06-05', '0.2', '2016-06-05 08:26:31', 'Tábor', 'Smolínova', '1122', '39002'),
		array(null, 'David', 'Konvalinka', '31', '8', '1999', '722435354', '200', 'david.konvalinka@centrum.cz', '2', '0', '2016-06-05', '0.2', '2016-06-05 15:38:57', 'Tábor', 'Smolínova', '1122', '39002'),
		array(null, 'Václav', 'Vlček', '8', '10', '1987', '704290701', '202', 'edlila25@seznam.cz', '2', '0', '2016-06-15', '1', '2016-06-15 10:52:18', 'Brno', 'Husova', '5', '60200', 'Brno-Slatina', 'Langrova', '1d', '62700',),
		array(null, 'Tomáš', 'Němec', '7', '1', '1990', '775491017', '203', 'nemeto@email.cz', '2', '0', '2016-06-29', null, '2016-06-29 11:50:53', 'Brno', 'Záhumenice', '554/25', '61900'),
		array('Ing.', 'Pavel', 'Kočí', '19', '8', '1989', '702008047', '204', 'kocipavel@email.cz', '2', '0', '2016-07-03', null, '2016-07-03 10:34:25', 'Kroměříž', 'Nitranská', '2', '76701'),
		array(null, 'Martin', 'Mazel', '15', '8', '1982', '739327816', '205', 'lionubk@seznam.cz', '2', '0', '2016-07-13', null, '2016-07-13 07:16:28', 'Brno', 'Pohankova', '7', '62800'),	
	);
	
	foreach ( $users as $value ) /* @var $value  */ 
	{
		$user = new Art_Model_User;
		$user->save();

		$userId = $user->id;

		$user->user_number = !empty($value[7]) ? $value[7] : Helper_TBDev::generateUserNumber();
		$user->active = 1;
		$user->id_currency = 1;	
		$user->save();

if ( ART_DEBUG ) :
		$password = 'pass';
else:
		$password = Art_User::generatePassword();
endif;

		$user_data = new Art_Model_User_Data;
		$user_data->degree = $value[0];
		$user_data->name = $value[1];
		$user_data->surname = $value[2];
		$user_data->email = $value[8];
		$user_data->salt = Art_User::generateSalt();
		$user_data->password = Art_User::hashPassword($password, $user_data->salt);
		$user_data->verif = 1;
		$user_data->verif_date = $value[13];
		$user_data->verif_id = 10;
if ( ART_DEBUG ) :		
		$user_data->pass_changed_date = dateSQL();	// TODO so far
endif;
		$user_data->born_day = $value[3];
		$user_data->born_month = $value[4];
		$user_data->born_year = $value[5];
		$user_data->setUser($user);
		$user_data->save();

		$inviteCode = new User_X_Invite_Code();
		$inviteCode->setUser($user);
		$inviteCode->id_invite_code = $value[9];
		$inviteCode->save();

		$userManager = new User_X_Manager();
		$userManager->setUser($user);
		$userManager->id_manager = Helper_TBDev::getManagerForUser(Helper_TBDev::getUserInvitedBy($user))->id;
		$userManager->save();

		$insertUserToGroup = new Art_Model_User_X_User_Group();
		$insertUserToGroup->setUser($user);
		$insertUserToGroup->setGroup(Art_Model_User_Group::getRegistered());
		$insertUserToGroup->save();

		$userUserGroup = new Art_Model_User_X_User_Group;
		$userUserGroup->setUser($user);
		$userUserGroup->setGroup(Art_Model_User_Group::getAuthorized());
		$userUserGroup->save();
		
		$userUserGroup = new Art_Model_User_X_User_Group;
		$userUserGroup->setUser($user);
		$userUserGroup->setGroup(new Art_Model_User_Group(array('name'=>Helper_TBDev::MEMBERSHIP_MEMBERS_GROUP)));
		$userUserGroup->save();
		
		//Tariffs
		$userUserGroup = new Art_Model_User_X_User_Group;
		$userUserGroup->setUser($user);
		$userUserGroup->id_user_group = 8;
		$userUserGroup->save();

		$insertDeliveryUserAddress = new Art_Model_Address();
		$insertDeliveryUserAddress->area_code = '420';
		$insertDeliveryUserAddress->phone = $value[6];
		$insertDeliveryUserAddress->city = $value[14];
		$insertDeliveryUserAddress->street = $value[15];
		$insertDeliveryUserAddress->housenum = $value[16];
		$insertDeliveryUserAddress->zip = $value[17];
		$insertDeliveryUserAddress->setUser($user);
		$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getDelivery());
		$insertDeliveryUserAddress->id_country = 1;
		$insertDeliveryUserAddress->save();
		
		if ( isset($value[18]) )
		{
			$insertDeliveryUserAddress = new Art_Model_Address();
			$insertDeliveryUserAddress->city = $value[18];
			$insertDeliveryUserAddress->street = $value[19];
			$insertDeliveryUserAddress->housenum = $value[20];
			$insertDeliveryUserAddress->zip = $value[21];
			$insertDeliveryUserAddress->setUser($user);
			$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getContact());
			$insertDeliveryUserAddress->id_country = 1;
			$insertDeliveryUserAddress->save();
		}
		
		$userService = new User_X_Service();
		$userService->activated = 1;
		$userService->activated_date = $value[11];
		$userService->setUser($user);
		$userService->id_service = 1;
		$userService->save();
		
		if ( '1' === $value[10] )
		{
			/* INV CODES */
			$invCode = new Invite_Code;
			$invCode->active = 1;
			$invCode->code = Helper_TBDev::generateInviteCode();
			$invCode->note = 'Initial';
			$invCode->setUser($user);
			$invCode->created_by = $user->id;
			$invCode->save();
		}
		
		if ( isset($value[12]) && NULL !== $value[12] )
		{
			$separator = strpos($value[12], '.');
			
			if ( false === $separator )
			{
				$payment = new Service_Payment();
				$payment->value = $value[12]*1200;
				$payment->received_date = $value[11];
				$payment->setUser($user);
				$payment->id_user_paid_by = $userId;
				$payment->id_user_group_x_service_price = 2;
				$payment->save();
			}
			else
			{
				$val = substr($value[12],0,$separator+1)*12+substr($value[12],$separator+1);
				//p($value[12]);
				//d($val);
				$payment = new Service_Payment();
				$payment->value = $val*100;
				$payment->received_date = $value[11];
				$payment->setUser($user);
				$payment->id_user_paid_by = $userId;
				$payment->id_user_group_x_service_price = 2;
				$payment->save();
			}
		}
		
		$body = 'Dobrý den,<br>'
			. ''
			. 'Heslo: ' . $password . '<br>'
			. ''
			. 'TBD';
		
		//Helper_Email::sendMail($user_data->email, $subject, $body);
	}
	
	
	$users = array(
		array(null, 'CashGo_Jmeno', 'CashGo_Prijmeni', '1', '1', '2000', '', '90100', '', '2', '2016-03-10', '1', '2016-03-10 14:21:51', 'Jezeřany-Maršovice', 'Jezeřany-Maršovice', '256', '67175', 'Cash&Go'),
			array(null, 'Petr', 'Zoufalý', '1', '1', '1995', '604976927', '181', 'petrzoufaly@email.cz', '2', '2016-03-10', '1', '2016-03-10 14:21:51', 'Jezeřany-Maršovice', 'Jezeřany-Maršovice', '256', '67175'),
			array(null, 'Dominik', 'Zoufalý', '1', '1', '1997', '731068017', '183', 'domzoufaly@gmail.com', '20', '2016-03-10', '1', '2016-03-10 19:17:41', 'Brno', 'Žitná', '23', '62100'),	

		array(null, 'COLLEGAS_Jmeno', 'COLLEGAS_Prijmeni', '1', '1', '2000', '', '90200', '', '1', '2015-08-14', '1', '2015-08-14 10:28:31', 'Brno', 'Karáskovo náměstí', '16', '61500', 'COLLEGAS'),
			array(null, 'Edita', 'Pavelková', '24', '12', '1990', '733731534', '141', 'edlila25@seznam.cz', '1', '2015-08-14', '1', '2015-08-14 10:28:31', 'Brno', 'Karáskovo náměstí', '16', '61500'),
			array(null, 'Petr', 'Staňa', '7', '9', '1984', '775108555', '124', 'petr.stana84@gmail.cz', '21', '2015-09-21', '1', '2015-09-21 12:57:08', 'Újezd u Brna', 'Na Zahrádkách', '917', '66453'),
			array(null, 'Sona', 'Dvořáková', '9', '10', '1980', '731651643', '154', 'danieldvor@seznam.cz', '21', '2015-09-09', '1', '2015-09-09 17:22:12', 'Tišnov', 'Mlýnská', '337', '66601'),
			array(null, 'Daniel', 'Dvořák', '8', '3', '1977', '605138762', '156', null, '21', '2015-09-09', '1', '2015-09-09 17:24:03', 'Tišnov', 'Mlýnská', '337', '66601'),
			array(null, 'Ivana', 'Stupkovà', '2', '3', '1985', '721101125', '161', 'I.stupkova@seznam.cz', '21', '2015-09-29', '1', '2015-09-29 17:33:47', 'Boskovice', 'Doktora Svěráka', '17', '68001'),		
	);
	
	$userGroup = null;
	
	foreach ( $users as $value ) /* @var $value  */ 
	{
		$user = new Art_Model_User;
		$user->save();

		$userId = $user->id;

		$user->user_number = !empty($value[7]) ? $value[7] : Helper_TBDev::generateCompanyNumber();
		$user->active = 1;
		$user->id_currency = 1;	
		$user->save();
		
if ( ART_DEBUG ) :
		$password = 'pass';
else:
		$password = Art_User::generatePassword();
endif;

		$user_data = new Art_Model_User_Data;
		$user_data->degree = $value[0];
		$user_data->name = $value[1];
		$user_data->surname = $value[2];
		$user_data->email = $value[8];
		$user_data->salt = Art_User::generateSalt();
		$user_data->password = Art_User::hashPassword($password, $user_data->salt);
		$user_data->verif = 1;
		$user_data->verif_date = $value[12];
		$user_data->verif_id = 10;
if ( ART_DEBUG ) :		
		$user_data->pass_changed_date = dateSQL();	// TODO so far
endif;
		$user_data->born_day = $value[3];
		$user_data->born_month = $value[4];
		$user_data->born_year = $value[5];
		$user_data->setUser($user);
		$user_data->save();

		$inviteCode = new User_X_Invite_Code();
		$inviteCode->setUser($user);
		$inviteCode->id_invite_code = $value[9];
		$inviteCode->save();

		$userManager = new User_X_Manager();
		$userManager->setUser($user);
		$userManager->id_manager = Helper_TBDev::getManagerForUser(Helper_TBDev::getUserInvitedBy($user))->id;
		$userManager->save();

		$insertUserToGroup = new Art_Model_User_X_User_Group();
		$insertUserToGroup->setUser($user);
		$insertUserToGroup->setGroup(Art_Model_User_Group::getRegistered());
		$insertUserToGroup->save();

		$userUserGroup = new Art_Model_User_X_User_Group;
		$userUserGroup->setUser($user);
		$userUserGroup->setGroup(Art_Model_User_Group::getAuthorized());
		$userUserGroup->save();

		$insertDeliveryUserAddress = new Art_Model_Address();
		$insertDeliveryUserAddress->area_code = '420';
		$insertDeliveryUserAddress->phone = $value[6];
		$insertDeliveryUserAddress->city = $value[13];
		$insertDeliveryUserAddress->street = $value[14];
		$insertDeliveryUserAddress->housenum = $value[15];
		$insertDeliveryUserAddress->zip = $value[16];
		$insertDeliveryUserAddress->setUser($user);		
		$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getDelivery());
		$insertDeliveryUserAddress->id_country = 1;
		$insertDeliveryUserAddress->save();
		
		$userService = new User_X_Service();
		$userService->activated = 1;
		$userService->activated_date = $value[10];
		$userService->setUser($user);
		$userService->id_service = 1;
		$userService->save();
		
		if ( isset($value[17]) )
		{
			$insertCompanyAddress = new Art_Model_Address();
			$insertCompanyAddress->setUser($user);
			$insertCompanyAddress->setType(Art_Model_Address_Type::getCompany());
			$insertCompanyAddress->company_name = $value[17];
			$insertCompanyAddress->ico = __('ico');
			$insertCompanyAddress->id_country = 1;
			$insertCompanyAddress->save();
			
			//Create group for company
			$userGroup = new Art_Model_User_Group();
			$userGroup->id_rights = 2;
			$userGroup->name = Helper_TBDev::GROUP_COMPANY.$value[17];
			$userGroup->save();

			$userUserGroup = new Art_Model_User_X_User_Group();
			$userUserGroup->setUser($user);
			$userUserGroup->setGroup($userGroup);
			$userUserGroup->save();

			$servicePrice = new Service_Price();
			$servicePrice->price = 1200;
			$servicePrice->time_interval = '1r';
			$servicePrice->setService(new Service(array('type'=>Helper_TBDev::MEMBERSHIP_TYPE)));
			$servicePrice->save();

			$from = Helper_TBDev::getDate(1, 1, 2016);
			$to = Helper_TBDev::getDate(1, 1, 2017);

			$userGroupServicePrice = new User_Group_X_Service_Price();
			$userGroupServicePrice->setUserGroup($userGroup);
			$userGroupServicePrice->setServicePrice($servicePrice);
			$userGroupServicePrice->time_from = dateSQL($from);
			$userGroupServicePrice->time_to = dateSQL($to);
			$userGroupServicePrice->save();
			
			$userCompany = new User_X_Company();
			$userCompany->id_user = $userId;
			$userCompany->id_company_user = $userId;
			$userCompany->save();
			
			/* INV CODES */
			$invCode = new Invite_Code;
			$invCode->active = 1;
			$invCode->code = Helper_TBDev::generateInviteCode();
			$invCode->note = 'Initial';
			$invCode->setUser($user);
			$invCode->created_by = $user->id;
			$invCode->save();
		}		
		else
		{
			$userUserGroup = new Art_Model_User_X_User_Group;
			$userUserGroup->setUser($user);
			$userUserGroup->setGroup(new Art_Model_User_Group(array('name'=>Helper_TBDev::MEMBERSHIP_MEMBERS_GROUP)));
			$userUserGroup->save();

			//Tariffs
			$userUserGroup = new Art_Model_User_X_User_Group;
			$userUserGroup->setUser($user);
			$userUserGroup->id_user_group = 8;
			$userUserGroup->save();
		}	
			
		if ( isset($value[11]) && NULL !== $value[11] )
		{
			$separator = strpos($value[11], '.');

			if ( false === $separator )
			{
				$payment = new Service_Payment();
				$payment->value = $value[11]*1200;
				$payment->received_date = $value[10];
				$payment->setUser($user);
				$payment->id_user_paid_by = $userId;
				$payment->id_user_group_x_service_price = 2;
				$payment->save();
			}
			else
			{
				$val = substr($value[11],0,$separator+1)*12+substr($value[11],$separator+1);

				$payment = new Service_Payment();
				$payment->value = $val*100;
				$payment->received_date = $value[10];
				$payment->setUser($user);
				$payment->id_user_paid_by = $userId;
				$payment->id_user_group_x_service_price = 2;
				$payment->save();
			}
		}
		
		$body = 'Dobrý den,<br>'
			. ''
			. 'Heslo: ' . $password . '<br>'
			. ''
			. 'TBD';
		
		//Helper_Email::sendMail($user_data->email, $subject, $body);
	}
	
	}
}